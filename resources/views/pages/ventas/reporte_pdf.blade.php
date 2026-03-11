<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
        }

        /* ── Encabezado ─────────────────────────────────────────────── */
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
            color: #fff;
            padding: 22px 28px 18px;
            margin-bottom: 20px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid rgba(255,255,255,.2);
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .empresa { font-size: 18px; font-weight: 700; letter-spacing: .5px; }
        .subtitulo { font-size: 11px; opacity: .7; margin-top: 3px; }
        .generado { text-align: right; font-size: 10px; opacity: .7; }
        .titulo-reporte { font-size: 14px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #a8d8ea; }

        /* ── Filtros aplicados ───────────────────────────────────────── */
        .filtros-box {
            background: #f8f9fa;
            border-left: 4px solid #0f3460;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 10.5px;
            color: #444;
        }
        .filtros-box .label { font-weight: 700; color: #0f3460; margin-right: 4px; }

        /* ── Tarjetas resumen ────────────────────────────────────────── */
        .resumen { display: flex; gap: 10px; margin-bottom: 20px; }
        .resumen-card {
            flex: 1; border: 1px solid #dee2e6; border-radius: 6px;
            padding: 11px 12px; text-align: center;
        }
        .resumen-card.verde { background: #198754; color: #fff; border-color: #198754; }
        .resumen-card.azul  { background: #0f3460; color: #fff; border-color: #0f3460; }
        .resumen-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; opacity: .7; margin-bottom: 4px; }
        .resumen-valor { font-size: 16px; font-weight: 900; }
        .resumen-card:not(.verde):not(.azul) .resumen-valor { color: #198754; }

        /* ── Tabla ───────────────────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; }

        thead tr { background: #0f3460; color: #fff; }
        thead th { padding: 9px 10px; font-weight: 700; letter-spacing: .3px; }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr { border-bottom: 1px solid #e9ecef; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody td { padding: 7px 10px; vertical-align: middle; }
        tbody td.right  { text-align: right; font-weight: 700; color: #198754; }
        tbody td.center { text-align: center; }
        tbody td.muted  { color: #777; font-size: 10px; }

        tfoot tr { background: #e8f5e9; border-top: 2px solid #198754; }
        tfoot td { padding: 9px 10px; font-weight: 900; font-size: 12px; }
        tfoot td.right  { text-align: right; color: #198754; }
        tfoot td.center { text-align: center; }

        /* ── Detalle colapsable por venta ────────────────────────────── */
        .detalle-header {
            background: #e9ecef;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 700;
            color: #495057;
            margin-top: 3px;
        }
        .detalle-tabla { width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 8px; }
        .detalle-tabla td { padding: 4px 10px; border-bottom: 1px solid #f0f0f0; }
        .detalle-tabla td.right { text-align: right; }

        /* ── Pie ─────────────────────────────────────────────────────── */
        .footer {
            margin-top: 28px; border-top: 1px solid #dee2e6;
            padding-top: 10px; font-size: 9.5px; color: #999;
            display: flex; justify-content: space-between;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="header">
        <div class="header-top">
            <div>
                <div class="empresa">Sistema Soda</div>
                <div class="subtitulo">Gestión de Ventas y Cajas</div>
            </div>
            <div class="generado">Generado el<br><strong>{{ $generadoEn }}</strong></div>
        </div>
        <div class="titulo-reporte">Reporte General de Ventas</div>
    </div>

    {{-- Filtros --}}
    <div class="filtros-box">
        <span class="label">Período:</span>
        {{ \Carbon\Carbon::parse($filtros['fecha_ini'])->format('d/m/Y') }}
        al
        {{ \Carbon\Carbon::parse($filtros['fecha_fin'])->format('d/m/Y') }}

        @if(!empty($filtros['user_id']) && $ventas->isNotEmpty())
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span class="label">Usuario:</span>
        {{ $ventas->first()?->user?->name ?? '—' }}
        @else
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span class="label">Usuarios:</span> Todos
        @endif
    </div>

    {{-- Resumen --}}
    <div class="resumen">
        <div class="resumen-card azul">
            <div class="resumen-label">Transacciones</div>
            <div class="resumen-valor">{{ $resumen['total_ventas'] }}</div>
        </div>
        <div class="resumen-card verde">
            <div class="resumen-label">Total Vendido</div>
            <div class="resumen-valor">₡{{ number_format($resumen['total_monto'], 2) }}</div>
        </div>
        <div class="resumen-card">
            <div class="resumen-label">Ítems vendidos</div>
            <div class="resumen-valor" style="color:#0f3460;">{{ $resumen['total_items'] }}</div>
        </div>
        <div class="resumen-card">
            <div class="resumen-label">Promedio por venta</div>
            <div class="resumen-valor">₡{{ number_format($resumen['promedio'], 2) }}</div>
        </div>
    </div>

    {{-- Tabla --}}
    @if($ventas->isEmpty())
        <p style="text-align:center;color:#aaa;padding:30px 0;">No hay ventas para el período seleccionado.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th class="center">Ítems</th>
                <th class="right">Total (₡)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
            <tr>
                <td class="muted">{{ $venta->id }}</td>
                <td><strong>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</strong></td>
                <td>{{ $venta->user?->name ?? '—' }}</td>
                <td class="center">{{ $venta->detalles->count() }}</td>
                <td class="right">{{ number_format($venta->total_calculado, 2) }}</td>
            </tr>
            @if($venta->detalles->isNotEmpty())
            <tr>
                <td colspan="5" style="padding:0 10px 6px 20px;">
                    <table class="detalle-tabla">
                        <tr style="background:#f0f0f0;">
                            <td style="font-weight:700;color:#555;font-size:9px;">Producto</td>
                            <td class="right" style="font-weight:700;color:#555;font-size:9px;width:60px;">Cant.</td>
                            <td class="right" style="font-weight:700;color:#555;font-size:9px;width:90px;">Subtotal</td>
                        </tr>
                        @foreach($venta->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->nombre ?: $detalle->codigo }}</td>
                            <td class="right">{{ $detalle->cantidad_vendida }}</td>
                            <td class="right">₡{{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"><strong>Total general</strong></td>
                <td class="center">{{ $resumen['total_items'] }}</td>
                <td class="right">{{ number_format($resumen['total_monto'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Pie --}}
    <div class="footer">
        <span>Sistema Soda — Reporte General de Ventas</span>
        <span>{{ $generadoEn }}</span>
    </div>

</body>
</html>
