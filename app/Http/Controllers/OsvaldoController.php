<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\HistorialTiquetes;
use App\Models\DetalleVenta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\DB;

class OsvaldoController extends Controller
{
    // Categoría de cocina (almuerzos, desayunos, sandwiches, etc.)
    private const CATEGORIA_COCINA = 1;

    public function resolver(Request $request, string $accion, ?string $id = null)
    {
        if ($id !== null) {
            $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
        }

        switch ($accion) {
            case 'crear':
                return $this->crear();
            case 'generar':
                return $this->generar($request);
            case 'historial':
                return $this->historial();
            default:
                abort(404, 'Acción no soportada.');
        }
    }

    // ── Formulario: seleccionar fecha y cantidades ────────────────────────────
    protected function crear()
    {
        $tickets = Ticket::with('categoria')
            ->where('categoria_d', self::CATEGORIA_COCINA)
            ->orderBy('nombre')
            ->get();

        return view('pages.generar_tiquetes.crear', compact('tickets'));
    }

    // ── Generar PDF + guardar historial ───────────────────────────────────────
    protected function generar(Request $request)
    {
        $request->validate([
            'fecha'         => ['required', 'date'],
            'cantidades'    => ['required', 'array'],
            'cantidades.*'  => ['integer', 'min:0', 'max:500'],
        ], [
            'fecha.required'      => 'Debe seleccionar una fecha.',
            'cantidades.required' => 'Debe ingresar al menos una cantidad.',
        ]);

        Carbon::setLocale('es');
        $fecha      = Carbon::parse($request->fecha);
        $cantidades = $request->input('cantidades', []);
        $generator  = new BarcodeGeneratorPNG();

        // Verificar que al menos un ticket tenga cantidad > 0
        $hayAlguno = collect($cantidades)->contains(fn($c) => (int) $c > 0);
        if (!$hayAlguno) {
            return back()->withErrors(['cantidades' => 'Debe ingresar al menos una cantidad mayor a cero.'])
                         ->withInput();
        }

        $ticketsParaPDF = [];

        foreach ($cantidades as $idTicket => $cantidad) {
            $cantidad = (int) $cantidad;
            if ($cantidad <= 0) continue;

            $ticket = Ticket::with('categoria')->find($idTicket);
            if (!$ticket) continue;

            // Guardar historial
            HistorialTiquetes::create([
                'id_ticket'        => $ticket->id_ticket,
                'user_id'          => auth()->id(),
                'cantidad_impresa' => $cantidad,
            ]);

            // Idéntico al tutorial que funciona: PNG binario → base64
            $barcode = base64_encode(
                $generator->getBarcode($ticket->codigo, $generator::TYPE_CODE_128)
            );

            // Agregar N copias al PDF
            for ($i = 0; $i < $cantidad; $i++) {
                $ticketsParaPDF[] = [
                    'nombre'  => $ticket->nombre,
                    'fecha'   => $fecha->format('d/m/Y'),
                    'barcode' => $barcode,
                ];
            }
        }

        // Agrupar en filas de 4 para el layout del PDF
        $filas = array_chunk($ticketsParaPDF, 4);

        $pdf = Pdf::loadView('pages.generar_tiquetes.pdf', compact('filas', 'fecha'))
                  ->setPaper('a4', 'portrait');

        $nombre = 'tiquetes_' . $fecha->format('Y-m-d') . '_' . now()->format('His') . '.pdf';
        $token  = $request->input('download_token', '');

        $response = $pdf->download($nombre);

        if ($token) {
            $response->headers->setCookie(
                cookie('pdf_ready', $token, 0, '/', null, false, false)
            );
        }

        return $response;
    }

    // ── Historial de generaciones ─────────────────────────────────────────────
    protected function historial()
    {
        $registros = HistorialTiquetes::with('ticket.categoria')
            ->orderBy('fecha_impresion', 'desc')
            ->get();

        // Obtener codigos únicos para consulta batch (evita N+1)
        $codigos = $registros
            ->filter(fn($r) => $r->ticket)
            ->pluck('ticket.codigo')
            ->unique()
            ->values()
            ->all();

        // Una sola query: total vendido por codigo + fecha de venta
        $ventasPorCodigoFecha = collect();
        if (!empty($codigos)) {
            $ventasPorCodigoFecha = DetalleVenta::join('ventas', 'ventas.id', '=', 'detalle_venta.venta_id')
                ->select(
                    'detalle_venta.codigo',
                    'ventas.fecha',
                    DB::raw('SUM(detalle_venta.cantidad_vendida) as total_vendido')
                )
                ->whereIn('detalle_venta.codigo', $codigos)
                ->groupBy('detalle_venta.codigo', 'ventas.fecha')
                ->get()
                ->keyBy(fn($row) => $row->codigo . '|' . $row->fecha);
        }

        // Adjuntar cantidad_vendida a cada registro del historial
        $registros->each(function ($reg) use ($ventasPorCodigoFecha) {
            $fecha = Carbon::parse($reg->fecha_impresion)->toDateString();
            $key   = ($reg->ticket->codigo ?? '') . '|' . $fecha;
            $reg->cantidad_vendida = (int) ($ventasPorCodigoFecha->get($key)?->total_vendido ?? 0);
        });

        return view('pages.generar_tiquetes.historial', compact('registros'));
    }
}
