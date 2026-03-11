<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CajaDiaria;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function resolver(Request $request, string $accion, ?string $id = null)
    {
        if ($id !== null) {
            $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
        }

        switch ($accion) {
            case 'ingresar':
                if ($request->isMethod('post')) return $this->guardar($request);
                return $this->ingresar();

            case 'historial':
                return $this->historial();

            case 'reporte':
                return $this->reporte($request);

            case 'reporte-pdf':
                return $this->reportePdf($request);

            default:
                return redirect()->route('caja', ['accion' => 'ingresar']);
        }
    }

    // ── Formulario de ingreso de caja del día ─────────────────────────────────
    protected function ingresar()
    {
        if (auth()->user()->rol !== 2) {
            return redirect('index');
        }

        $hoy    = now()->toDateString();
        $userId = auth()->id();

        $cajaHoy = CajaDiaria::where('user_id', $userId)
            ->where('fecha', $hoy)
            ->first();

        return view('pages.caja.ingresar', compact('cajaHoy', 'hoy'));
    }

    // ── Guardar / actualizar caja del día (AJAX) ──────────────────────────────
    protected function guardar(Request $request)
    {
        if (auth()->user()->rol !== 2) {
            return response()->json(['ok' => false, 'message' => 'Acceso no autorizado.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'monto'       => ['required', 'numeric', 'min:0'],
            'observacion' => ['nullable', 'string', 'max:500'],
        ], [
            'monto.required' => 'El monto es obligatorio.',
            'monto.numeric'  => 'El monto debe ser un número válido.',
            'monto.min'      => 'El monto no puede ser negativo.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok'     => false,
                'errors' => $validator->errors(),
                'message'=> $validator->errors()->first(),
            ], 422);
        }

        $hoy    = now()->toDateString();
        $userId = auth()->id();

        $caja = CajaDiaria::updateOrCreate(
            ['user_id' => $userId, 'fecha' => $hoy],
            [
                'monto'       => $request->input('monto'),
                'observacion' => $request->input('observacion'),
            ]
        );

        return response()->json([
            'ok'      => true,
            'message' => 'Caja del día guardada correctamente.',
            'monto'   => number_format($caja->monto, 2),
        ]);
    }

    // ── Reporte de caja (pantalla con filtros) ────────────────────────────────
    protected function reporte(Request $request)
    {
        [$cajas, $filtros, $usuarios] = $this->consultaReporte($request);

        $totalMonto = $cajas->sum('monto');
        $totalRegistros = $cajas->count();

        return view('pages.caja.reporte', compact('cajas', 'filtros', 'usuarios', 'totalMonto', 'totalRegistros'));
    }

    // ── Generar PDF del reporte ───────────────────────────────────────────────
    protected function reportePdf(Request $request)
    {
        [$cajas, $filtros, $usuarios] = $this->consultaReporte($request);

        $totalMonto = $cajas->sum('monto');
        $totalRegistros = $cajas->count();
        $generadoEn = Carbon::now()->translatedFormat('d \d\e F Y, H:i');

        $pdf = Pdf::loadView('pages.caja.reporte_pdf', compact('cajas', 'filtros', 'totalMonto', 'totalRegistros', 'generadoEn'))
                  ->setPaper('a4', 'portrait');

        $nombre = 'reporte_caja_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($nombre);
    }

    // ── Helper: consulta con filtros reutilizable ─────────────────────────────
    private function consultaReporte(Request $request): array
    {
        $user     = auth()->user();
        $fechaIni = $request->input('fecha_ini', now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());
        $userId   = $request->input('user_id');

        $query = CajaDiaria::with('user')
            ->whereBetween('fecha', [$fechaIni, $fechaFin])
            ->orderBy('fecha', 'desc');

        if ($user->rol !== 1) {
            // Rol 2: solo sus propios registros
            $query->where('user_id', $user->id);
        } elseif ($userId) {
            $query->where('user_id', $userId);
        }

        $cajas   = $query->get();
        $filtros = ['fecha_ini' => $fechaIni, 'fecha_fin' => $fechaFin, 'user_id' => $userId];
        $usuarios = $user->rol === 1 ? User::orderBy('name')->get(['id', 'name']) : collect();

        return [$cajas, $filtros, $usuarios];
    }

    // ── Historial de cajas ────────────────────────────────────────────────────
    protected function historial()
    {
        $user = auth()->user();

        if ($user->rol === 1) {
            // Admin: ve todas las cajas de todos los usuarios
            $cajas = CajaDiaria::with('user')
                ->orderBy('fecha', 'desc')
                ->get();
        } else {
            // Usuario rol=2: solo las propias
            $cajas = CajaDiaria::where('user_id', $user->id)
                ->orderBy('fecha', 'desc')
                ->get();
        }

        return view('pages.caja.historial', compact('cajas', 'user'));
    }
}
