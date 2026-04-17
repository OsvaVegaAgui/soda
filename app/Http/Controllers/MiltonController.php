<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Venta;
use App\Models\User;
use App\Models\DetalleVenta;
use App\Models\ProductoSoda;
use App\Models\ProductoSodaCodigo;
use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MiltonController extends Controller
{
    public function resolver(Request $request, string $accion, ?string $id = null)
    {
        if ($id !== null) {
            $id = strtolower($id) === 'null' ? null : (ctype_digit($id) ? (int) $id : $id);
        }

        switch ($accion) {
            case 'registrar':
                return $this->registrar($request);
            case 'agregar':
                return $this->agregar($request);
            case 'quitar':
                return $this->quitar($request);
            case 'lista':
                return $this->lista($request);
            case 'ver':
                if ($id === null) return redirect()->route('ventas', ['accion' => 'lista']);
                return $this->ver($id);
            case 'buscar-producto':
                return $this->buscarProducto($request);
            case 'crear':
                return redirect()->route('ventas', ['accion' => 'cobrar']);
            // ── POS (cobro por transacción) ───────────────────────────────────
            case 'cobrar':
                return $this->cobrar();
            case 'pos-agregar':
                return $this->posAgregar($request);
            case 'pos-quitar':
                return $this->posQuitar($request);
            case 'pos-procesar':
                return $this->posProcesar($request);
            case 'pos-limpiar':
                return $this->posLimpiar();
            case 'reporte':
                return $this->reporte($request);
            case 'reporte-pdf':
                return $this->reportePdf($request);
            case 'reporte-horas':
                return $this->reporteHoras($request);
            default:
                return redirect()->route('ventas', ['accion' => 'registrar']);
        }
    }

    // ── Resumen del día: ítems vendidos agrupados ─────────────────────────────
    protected function registrar(Request $request)
    {
        $fecha = $request->input('fecha', now()->toDateString());

        $ventaIds = Venta::where('fecha', $fecha)->pluck('id');

        $items = DetalleVenta::whereIn('venta_id', $ventaIds)
            ->selectRaw('codigo, nombre, SUM(cantidad_vendida) as total_cantidad, SUM(subtotal) as total_monto')
            ->groupBy('codigo', 'nombre')
            ->orderBy('nombre')
            ->get();

        $totalGeneral = $items->sum('total_monto');

        return view('pages.ventas.registrar', compact('items', 'fecha', 'totalGeneral'));
    }

    // ── Agregar o incrementar un producto (AJAX) ──────────────────────────────
    protected function agregar(Request $request)
    {
        $codigo = trim($request->input('codigo', ''));

        if ($codigo === '') {
            return response()->json(['ok' => false, 'message' => 'Código vacío.'], 422);
        }

        $producto = $this->encontrarProducto($codigo);

        if (!$producto) {
            return response()->json(['ok' => false, 'message' => 'Producto no encontrado.'], 404);
        }

        $hoy   = now()->toDateString();
        $venta = Venta::firstOrCreate(
            ['fecha' => $hoy],
            ['user_id' => auth()->id()]
        );

        $detalle = DetalleVenta::where('venta_id', $venta->id)
            ->where('codigo', $producto['codigo'])
            ->first();

        if ($detalle) {
            $detalle->cantidad_vendida++;
            $detalle->subtotal = $detalle->cantidad_vendida * $detalle->precio_unitario;
            $detalle->save();
        } else {
            $detalle = DetalleVenta::create([
                'venta_id'         => $venta->id,
                'codigo'           => $producto['codigo'],
                'nombre'           => $producto['nombre'],
                'cantidad_vendida' => 1,
                'precio_unitario'  => $producto['precio'],
                'subtotal'         => $producto['precio'],
            ]);
        }

        return response()->json([
            'ok'      => true,
            'message' => $producto['nombre'] . ' agregado.',
            'detalle' => [
                'id'               => $detalle->id,
                'codigo'           => $detalle->codigo,
                'nombre'           => $detalle->nombre,
                'cantidad_vendida' => $detalle->cantidad_vendida,
            ],
        ]);
    }

    // ── Decrementar o eliminar un detalle (AJAX) ──────────────────────────────
    protected function quitar(Request $request)
    {
        $detalle = DetalleVenta::find($request->input('id'));

        if (!$detalle) {
            return response()->json(['ok' => false, 'message' => 'Registro no encontrado.'], 404);
        }

        if ($detalle->cantidad_vendida <= 1) {
            $nombre = $detalle->nombre;
            $detalle->delete();
            return response()->json(['ok' => true, 'eliminado' => true, 'nombre' => $nombre]);
        }

        $detalle->cantidad_vendida--;
        $detalle->subtotal = $detalle->cantidad_vendida * $detalle->precio_unitario;
        $detalle->save();

        return response()->json([
            'ok'               => true,
            'eliminado'        => false,
            'cantidad_vendida' => $detalle->cantidad_vendida,
        ]);
    }

    // ── Buscar producto (Select2 + barcode lookup) ────────────────────────────
    protected function buscarProducto(Request $request)
    {
        if ($request->filled('term')) {
            $term = $request->input('term');

            $productosSoda = ProductoSoda::query()
                ->select('codigo_softland as codigo', 'nombre', 'precio')
                ->where('activo', true)
                ->where(function ($q) use ($term) {
                    $q->where('nombre', 'like', '%' . $term . '%')
                      ->orWhere('codigo_softland', 'like', '%' . $term . '%')
                      ->orWhere('codigo_barras', 'like', '%' . $term . '%');
                })
                ->limit(10)
                ->get()
                ->map(fn($p) => [
                    'codigo'   => $p->codigo,
                    'nombre'   => $p->nombre,
                    'precio'   => $p->precio,
                    'tipo'     => 'soda',
                    'etiqueta' => $p->nombre . ' (' . $p->codigo . ')',
                ]);

            $productosTicket = Ticket::query()
                ->select('codigo', 'nombre', 'precio')
                ->where(function ($q) use ($term) {
                    $q->where('nombre', 'like', '%' . $term . '%')
                      ->orWhere('codigo', 'like', '%' . $term . '%');
                })
                ->limit(10)
                ->get()
                ->map(fn($p) => [
                    'codigo'   => $p->codigo,
                    'nombre'   => $p->nombre,
                    'precio'   => $p->precio,
                    'tipo'     => 'ticket',
                    'etiqueta' => $p->nombre . ' (' . $p->codigo . ')',
                ]);

            return response()->json([
                'success'   => true,
                'productos' => $productosSoda->concat($productosTicket)->values(),
            ]);
        }

        // Búsqueda puntual por código exacto
        $codigo   = trim($request->input('codigo', ''));
        $producto = $this->encontrarProducto($codigo);

        if ($producto) {
            return response()->json(['success' => true, 'producto' => $producto]);
        }

        return response()->json(['success' => false, 'message' => 'Producto no encontrado.'], 404);
    }

    // ── Lista de ventas con filtro por fecha y rol ────────────────────────────
    protected function lista(Request $request)
    {
        $fechaIni = $request->input('fecha_ini', now()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        $query = Venta::with(['user', 'detalles'])
            ->whereBetween('fecha', [$fechaIni, $fechaFin]);

        if (auth()->user()->rol === 2) {
            $query->where('user_id', auth()->id());
        }

        $ventas = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();

        return view('pages.ventas.lista', compact('ventas', 'fechaIni', 'fechaFin'));
    }

    // ── Ver detalle de una venta ──────────────────────────────────────────────
    protected function ver($id)
    {
        $query = Venta::with(['user', 'detalles']);

        if (auth()->user()->rol === 2) {
            $query->where('user_id', auth()->id());
        }

        $venta = $query->find($id);

        if (!$venta) {
            return redirect()->route('ventas', ['accion' => 'lista'])
                ->with('error', 'Venta no encontrada.');
        }

        return view('pages.ventas.ver', compact('venta'));
    }

    // ── Formulario crear (legado) ─────────────────────────────────────────────
    protected function crear()
    {
        return view('pages.ventas.crear');
    }

    protected function guardar(Request $request)
    {
        $request->validate([
            'fecha'                          => 'required|date',
            'detalles'                       => 'required|array|min:1',
            'detalles.*.codigo'              => 'required|string|max:50',
            'detalles.*.cantidad_vendida'    => 'required|integer|min:1',
            'detalles.*.precio_unitario'     => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $userId = auth()->id();
            if (!$userId) {
                $firstUser = User::first();
                if ($firstUser) {
                    $userId = $firstUser->id;
                } else {
                    return response()->json(['success' => false, 'message' => 'No hay usuarios en la base de datos.'], 500);
                }
            }

            $venta = Venta::create(['fecha' => $request->fecha, 'user_id' => $userId]);

            foreach ($request->detalles as $d) {
                $subtotal = $d['cantidad_vendida'] * $d['precio_unitario'];
                DetalleVenta::create([
                    'venta_id'         => $venta->id,
                    'codigo'           => $d['codigo'],
                    'cantidad_vendida' => $d['cantidad_vendida'],
                    'precio_unitario'  => $d['precio_unitario'],
                    'subtotal'         => $subtotal,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venta guardada correctamente', 'venta_id' => $venta->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════════════════
    // Reporte de ventas
    // ════════════════════════════════════════════════════════════════════════════

    protected function reporte(Request $request)
    {
        [$ventas, $filtros, $resumen] = $this->consultaReporte($request);
        $usuarios = User::orderBy('name')->get(['id', 'name']);
        return view('pages.ventas.reporte', compact('ventas', 'filtros', 'resumen', 'usuarios'));
    }

    protected function reportePdf(Request $request)
    {
        [$ventas, $filtros, $resumen] = $this->consultaReporte($request);
        $generadoEn = Carbon::now()->translatedFormat('d \d\e F Y, H:i');

        $pdf = Pdf::loadView('pages.ventas.reporte_pdf', compact('ventas', 'filtros', 'resumen', 'generadoEn'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('reporte_ventas_' . now()->format('Ymd_His') . '.pdf');
    }

    private function consultaReporte(Request $request): array
    {
        $fechaIni      = $request->input('fecha_ini', now()->startOfMonth()->toDateString());
        $fechaFin      = $request->input('fecha_fin', now()->toDateString());
        $userId        = $request->input('user_id');
        $tipoProducto  = $request->input('tipo_producto');

        $query = Venta::with(['user', 'detalles'])
            ->whereBetween('fecha', [$fechaIni, $fechaFin]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($tipoProducto === 'soda') {
            $query->whereHas('detalles', fn($q) => $q->where('codigo', 'like', 'SOD-%'));
        } elseif ($tipoProducto === 'ticket_cat1') {
            $codigos = Ticket::where('categoria_d', 1)->pluck('codigo');
            $query->whereHas('detalles', fn($q) => $q->whereIn('codigo', $codigos));
        } elseif ($tipoProducto === 'ticket_cat2') {
            $codigos = Ticket::where('categoria_d', 2)->pluck('codigo');
            $query->whereHas('detalles', fn($q) => $q->whereIn('codigo', $codigos));
        }

        $ventas = $query->orderBy('fecha', 'desc')->get();

        // Calcular total real desde detalles (compatible con ambos flujos)
        $ventas->each(fn($v) => $v->total_calculado = $v->detalles->sum('subtotal'));

        $resumen = [
            'total_ventas'  => $ventas->count(),
            'total_monto'   => $ventas->sum('total_calculado'),
            'total_items'   => $ventas->sum(fn($v) => $v->detalles->count()),
            'promedio'      => $ventas->count() > 0 ? $ventas->sum('total_calculado') / $ventas->count() : 0,
        ];

        $filtros = [
            'fecha_ini'     => $fechaIni,
            'fecha_fin'     => $fechaFin,
            'user_id'       => $userId,
            'tipo_producto' => $tipoProducto,
        ];

        return [$ventas, $filtros, $resumen];
    }

    // ════════════════════════════════════════════════════════════════════════════
    // Reporte por hora del día
    // ════════════════════════════════════════════════════════════════════════════

    protected function reporteHoras(Request $request)
    {
        $fechaIni = $request->input('fecha_ini', now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());
        $userId   = $request->input('user_id');

        $query = DB::table('ventas as v')
            ->join('detalle_venta as dv', 'dv.venta_id', '=', 'v.id')
            ->selectRaw('HOUR(v.created_at) as hora, COUNT(DISTINCT v.id) as transacciones, SUM(dv.subtotal) as monto')
            ->whereBetween('v.fecha', [$fechaIni, $fechaFin]);

        if ($userId) {
            $query->where('v.user_id', $userId);
        }

        $raw = $query
            ->groupByRaw('HOUR(v.created_at)')
            ->orderBy('hora')
            ->get()
            ->keyBy('hora');

        // Array completo de 24 horas (rellena con 0 los huecos)
        $horas = [];
        for ($h = 0; $h < 24; $h++) {
            $horas[$h] = [
                'hora'          => sprintf('%02d:00', $h),
                'transacciones' => isset($raw[$h]) ? (int)   $raw[$h]->transacciones : 0,
                'monto'         => isset($raw[$h]) ? (float) $raw[$h]->monto         : 0.0,
            ];
        }

        $collection        = collect($horas);
        $peakHora          = $collection->sortByDesc('monto')->first();
        $totalMonto        = $collection->sum('monto');
        $totalTransacciones = $collection->sum('transacciones');

        $usuarios = User::orderBy('name')->get(['id', 'name']);
        $filtros  = ['fecha_ini' => $fechaIni, 'fecha_fin' => $fechaFin, 'user_id' => $userId];

        return view('pages.ventas.reporte_horas', compact(
            'horas', 'peakHora', 'totalMonto', 'totalTransacciones', 'filtros', 'usuarios'
        ));
    }

    // ════════════════════════════════════════════════════════════════════════════
    // POS — cobro por transacción individual
    // ════════════════════════════════════════════════════════════════════════════

    protected function cobrar()
    {
        $items = session('pos_cart', []);
        $total = array_sum(array_column($items, 'subtotal'));
        return view('pages.ventas.cobrar', compact('items', 'total'));
    }

    protected function posAgregar(Request $request)
    {
        $codigo = trim($request->input('codigo', ''));

        if ($codigo === '') {
            return response()->json(['ok' => false, 'message' => 'Código vacío.'], 422);
        }

        $producto = $this->encontrarProducto($codigo);

        if (!$producto) {
            return response()->json(['ok' => false, 'message' => 'Producto no encontrado.'], 404);
        }

        $cart = session('pos_cart', []);
        $key  = $producto['codigo'];

        if (isset($cart[$key])) {
            $cart[$key]['cantidad']++;
            $cart[$key]['subtotal'] = $cart[$key]['cantidad'] * $cart[$key]['precio_unitario'];
        } else {
            $cart[$key] = [
                'codigo'          => $producto['codigo'],
                'nombre'          => $producto['nombre'],
                'precio_unitario' => $producto['precio'],
                'cantidad'        => 1,
                'subtotal'        => $producto['precio'],
            ];
        }

        session(['pos_cart' => $cart]);
        $total = array_sum(array_column($cart, 'subtotal'));

        return response()->json([
            'ok'      => true,
            'message' => $producto['nombre'] . ' agregado.',
            'item'    => $cart[$key],
            'total'   => $total,
        ]);
    }

    protected function posQuitar(Request $request)
    {
        $codigo = trim($request->input('codigo', ''));
        $cart   = session('pos_cart', []);

        if (!isset($cart[$codigo])) {
            return response()->json(['ok' => false, 'message' => 'Ítem no encontrado.'], 404);
        }

        $nombre = $cart[$codigo]['nombre'];

        if ($cart[$codigo]['cantidad'] <= 1) {
            unset($cart[$codigo]);
            $eliminado = true;
        } else {
            $cart[$codigo]['cantidad']--;
            $cart[$codigo]['subtotal'] = $cart[$codigo]['cantidad'] * $cart[$codigo]['precio_unitario'];
            $eliminado = false;
        }

        session(['pos_cart' => $cart]);
        $total = array_sum(array_column($cart, 'subtotal'));

        return response()->json([
            'ok'       => true,
            'eliminado'=> $eliminado,
            'nombre'   => $nombre,
            'codigo'   => $codigo,
            'cantidad' => $eliminado ? 0 : $cart[$codigo]['cantidad'],
            'total'    => $total,
        ]);
    }

    protected function posProcesar(Request $request)
    {
        $cart = session('pos_cart', []);

        if (empty($cart)) {
            return response()->json(['ok' => false, 'message' => 'El carrito está vacío.'], 422);
        }

        $metodoPago = $request->input('metodo_pago');
        if (!in_array($metodoPago, ['efectivo', 'tarjeta', 'mixto'])) {
            return response()->json(['ok' => false, 'message' => 'Seleccione un método de pago.'], 422);
        }

        $total         = array_sum(array_column($cart, 'subtotal'));
        $montoEfectivo = null;
        $vuelto        = null;

        if (in_array($metodoPago, ['efectivo', 'mixto'])) {
            $montoEfectivo = (float) $request->input('monto_efectivo', 0);
            if ($montoEfectivo <= 0) {
                return response()->json(['ok' => false, 'message' => 'Ingrese el monto en efectivo.'], 422);
            }
            if ($metodoPago === 'efectivo' && $montoEfectivo < $total) {
                return response()->json(['ok' => false, 'message' => 'El monto en efectivo es insuficiente.'], 422);
            }
            if ($metodoPago === 'mixto' && $montoEfectivo >= $total) {
                return response()->json(['ok' => false, 'message' => 'Para pago total en efectivo, use solo el método Efectivo.'], 422);
            }
            if ($metodoPago === 'efectivo') {
                $vuelto = round($montoEfectivo - $total, 2);
            }
        }

        try {
            DB::beginTransaction();

            $venta = Venta::create([
                'fecha'          => now()->toDateString(),
                'user_id'        => auth()->id(),
                'total'          => $total,
                'metodo_pago'    => $metodoPago,
                'monto_efectivo' => $montoEfectivo,
                'vuelto'         => $vuelto,
            ]);

            foreach ($cart as $item) {
                DetalleVenta::create([
                    'venta_id'         => $venta->id,
                    'codigo'           => $item['codigo'],
                    'nombre'           => $item['nombre'],
                    'cantidad_vendida' => $item['cantidad'],
                    'precio_unitario'  => $item['precio_unitario'],
                    'subtotal'         => $item['subtotal'],
                ]);
            }

            DB::commit();
            session()->forget('pos_cart');

            $montoTarjeta = ($metodoPago === 'mixto') ? round($total - $montoEfectivo, 2) : null;

            return response()->json([
                'ok'               => true,
                'message'          => 'Venta procesada correctamente.',
                'total'            => number_format($total, 2),
                'vuelto'           => $vuelto !== null ? number_format($vuelto, 2) : null,
                'id'               => $venta->id,
                'monto_efectivo_fmt' => ($metodoPago === 'mixto') ? number_format($montoEfectivo, 2) : null,
                'monto_tarjeta_fmt'  => $montoTarjeta !== null ? number_format($montoTarjeta, 2) : null,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => 'Error al procesar: ' . $e->getMessage()], 500);
        }
    }

    protected function posLimpiar()
    {
        session()->forget('pos_cart');
        return response()->json(['ok' => true]);
    }

    // ── Helper privado: buscar producto en ambas tablas ───────────────────────
    private function encontrarProducto(string $codigo): ?array
    {
        if ($codigo === '') return null;

        // 1. Buscar por codigo_softland o codigo_barras principal
        $soda = ProductoSoda::where('activo', true)
            ->where(function ($q) use ($codigo) {
                $q->where('codigo_softland', $codigo)
                  ->orWhere('codigo_barras', $codigo);
            })
            ->first();

        if ($soda) {
            return [
                'codigo' => $soda->codigo_softland ?? $soda->codigo_barras,
                'nombre' => $soda->nombre,
                'precio' => $soda->precio ?? 0,
            ];
        }

        // 2. Buscar en códigos adicionales (múltiples barcodes por producto)
        $extra = ProductoSodaCodigo::where('codigo_barras', $codigo)
            ->with(['producto' => fn($q) => $q->where('activo', true)])
            ->first();

        if ($extra && $extra->producto) {
            $p = $extra->producto;
            return [
                'codigo' => $p->codigo_softland ?? $p->codigo_barras,
                'nombre' => $p->nombre,
                'precio' => $p->precio ?? 0,
            ];
        }

        $ticket = Ticket::where('codigo', $codigo)->first();

        if ($ticket) {
            return [
                'codigo' => $ticket->codigo,
                'nombre' => $ticket->nombre,
                'precio' => $ticket->precio ?? 0,
            ];
        }

        return null;
    }
}
