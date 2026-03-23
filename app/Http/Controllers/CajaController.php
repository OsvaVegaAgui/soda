<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CajaDiaria;
use App\Models\Venta;
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

            case 'cerrar':
                if ($request->isMethod('post')) return $this->cerrar($request, $id);
                return redirect()->route('caja', ['accion' => 'ingresar']);

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

    // ── Formulario de ingreso / estado de caja ────────────────────────────────
    protected function ingresar()
    {
        if (auth()->user()->rol !== 2) {
            return redirect('index');
        }

        $hoy    = now()->toDateString();
        $userId = auth()->id();

        $cajaActiva = CajaDiaria::where('user_id', $userId)
            ->where('fecha', $hoy)
            ->where('cerrada', false)
            ->first();

        // Calcular saldo real de la caja activa
        $saldoActiva = null;
        if ($cajaActiva) {
            $ventas = Venta::where('user_id', $userId)
                ->where('fecha', $hoy)
                ->whereIn('metodo_pago', ['efectivo', 'mixto'])
                ->where('created_at', '>=', $cajaActiva->hora_apertura ?? $cajaActiva->created_at)
                ->selectRaw('SUM(monto_efectivo) as total_efectivo, SUM(COALESCE(vuelto, 0)) as total_vuelto')
                ->first();

            $entradas    = $ventas ? (float) $ventas->total_efectivo : 0;
            $vueltos     = $ventas ? (float) $ventas->total_vuelto   : 0;
            $saldoActiva = $cajaActiva->monto + $entradas - $vueltos;
        }

        // Cajas del día ya cerradas
        $cajasCerradasHoy = CajaDiaria::where('user_id', $userId)
            ->where('fecha', $hoy)
            ->where('cerrada', true)
            ->orderBy('hora_apertura')
            ->get();

        return view('pages.caja.ingresar', compact('cajaActiva', 'saldoActiva', 'cajasCerradasHoy', 'hoy'));
    }

    // ── Abrir nueva caja del día (AJAX) ───────────────────────────────────────
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
                'ok'      => false,
                'errors'  => $validator->errors(),
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $hoy    = now()->toDateString();
        $userId = auth()->id();

        // Verificar que no haya una caja activa
        $cajaActiva = CajaDiaria::where('user_id', $userId)
            ->where('fecha', $hoy)
            ->where('cerrada', false)
            ->first();

        if ($cajaActiva) {
            return response()->json([
                'ok'      => false,
                'message' => 'Ya tiene una caja abierta. Debe entregarla antes de abrir una nueva.',
            ], 422);
        }

        $caja = CajaDiaria::create([
            'user_id'       => $userId,
            'fecha'         => $hoy,
            'monto'         => $request->input('monto'),
            'observacion'   => $request->input('observacion'),
            'cerrada'       => false,
            'hora_apertura' => now(),
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Caja abierta correctamente.',
            'monto'   => number_format($caja->monto, 2),
            'caja_id' => $caja->id,
        ]);
    }

    // ── Cerrar / entregar caja (AJAX) ─────────────────────────────────────────
    protected function cerrar(Request $request, ?int $id)
    {
        if (auth()->user()->rol !== 2) {
            return response()->json(['ok' => false, 'message' => 'Acceso no autorizado.'], 403);
        }

        $caja = CajaDiaria::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('cerrada', false)
            ->first();

        if (!$caja) {
            return response()->json(['ok' => false, 'message' => 'Caja no encontrada o ya entregada.'], 404);
        }

        $caja->update([
            'cerrada'     => true,
            'hora_cierre' => now(),
        ]);

        return response()->json([
            'ok'      => true,
            'message' => 'Caja entregada correctamente.',
        ]);
    }

    // ── Historial de cajas ────────────────────────────────────────────────────
    protected function historial()
    {
        $user = auth()->user();

        if ($user->rol === 1) {
            $cajas = CajaDiaria::with('user')
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_apertura', 'desc')
                ->get();
        } else {
            $cajas = CajaDiaria::where('user_id', $user->id)
                ->orderBy('fecha', 'desc')
                ->orderBy('hora_apertura', 'desc')
                ->get();
        }

        // Cargar ventas de efectivo/mixto para los períodos relevantes
        $fechas  = $cajas->pluck('fecha')->unique()->map(fn($f) => $f instanceof Carbon ? $f->toDateString() : substr((string)$f, 0, 10))->values()->toArray();
        $userIds = $cajas->pluck('user_id')->unique()->values()->toArray();

        $ventasBase = Venta::whereIn('user_id', $userIds)
            ->whereIn('fecha', $fechas)
            ->whereIn('metodo_pago', ['efectivo', 'mixto'])
            ->get(['user_id', 'created_at', 'monto_efectivo', 'vuelto']);

        // Mapear ventas a cada caja según su rango horario
        $ventasStats = [];
        foreach ($cajas as $caja) {
            $apertura = $caja->hora_apertura ?? $caja->created_at;
            $cierre   = $caja->cerrada ? $caja->hora_cierre : now();

            $cajaVentas = $ventasBase->filter(function ($v) use ($caja, $apertura, $cierre) {
                return $v->user_id == $caja->user_id
                    && $v->created_at >= $apertura
                    && $v->created_at <= $cierre;
            });

            $ventasStats[$caja->id] = [
                'total_efectivo' => $cajaVentas->sum('monto_efectivo'),
                'total_vuelto'   => $cajaVentas->sum('vuelto'),
            ];
        }

        return view('pages.caja.historial', compact('cajas', 'user', 'ventasStats'));
    }

    // ── Reporte de caja (pantalla con filtros) ────────────────────────────────
    protected function reporte(Request $request)
    {
        [$cajas, $filtros, $usuarios] = $this->consultaReporte($request);

        $totalMonto     = $cajas->sum('monto');
        $totalRegistros = $cajas->count();

        return view('pages.caja.reporte', compact('cajas', 'filtros', 'usuarios', 'totalMonto', 'totalRegistros'));
    }

    // ── Generar PDF del reporte ───────────────────────────────────────────────
    protected function reportePdf(Request $request)
    {
        [$cajas, $filtros, $usuarios] = $this->consultaReporte($request);

        $totalMonto     = $cajas->sum('monto');
        $totalRegistros = $cajas->count();
        $generadoEn     = Carbon::now()->translatedFormat('d \d\e F Y, H:i');

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
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_apertura', 'asc');

        if ($user->rol !== 1) {
            $query->where('user_id', $user->id);
        } elseif ($userId) {
            $query->where('user_id', $userId);
        }

        $cajas    = $query->get();
        $filtros  = ['fecha_ini' => $fechaIni, 'fecha_fin' => $fechaFin, 'user_id' => $userId];
        $usuarios = $user->rol === 1 ? User::orderBy('name')->get(['id', 'name']) : collect();

        return [$cajas, $filtros, $usuarios];
    }
}
