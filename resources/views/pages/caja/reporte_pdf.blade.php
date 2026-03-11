<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Caja</title>
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
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
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

        /* ── Bloque de filtros aplicados ────────────────────────────── */
        .filtros-box {
            background: #f8f9fa;
            border-left: 4px solid #0f3460;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 10.5px;
            color: #444;
        }
        .filtros-box .label { font-weight: 700; color: #0f3460; margin-right: 4px; }

        /* ── Tarjetas de resumen ─────────────────────────────────────── */
        .resumen {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .resumen-card {
            flex: 1;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px 14px;
            text-align: center;
        }
        .resumen-card.destacado { background: #0f3460; color: #fff; border-color: #0f3460; }
        .resumen-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; opacity: .65; margin-bottom: 5px; }
        .resumen-valor { font-size: 17px; font-weight: 900; }
        .resumen-card:not(.destacado) .resumen-valor { color: #198754; }

        /* ── Tabla ───────────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }
        thead tr {
            background: #0f3460;
            color: #fff;
        }
        thead th {
            padding: 9px 10px;
            font-weight: 700;
            letter-spacing: .3px;
        }
        thead th.right { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr { border-bottom: 1px solid #e9ecef; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody td { padding: 8px 10px; vertical-align: top; }
        tbody td.right { text-align: right; font-weight: 700; color: #198754; }
        tbody td.center { text-align: center; }
        tbody td.muted { color: #777; font-size: 10px; }

        tfoot tr { background: #e8f5e9; border-top: 2px solid #198754; }
        tfoot td {
            padding: 9px 10px;
            font-weight: 900;
            font-size: 12px;
        }
        tfoot td.right { text-align: right; color: #198754; }

        /* ── Vacío ───────────────────────────────────────────────────── */
        .empty { text-align: center; padding: 30px; color: #aaa; }

        /* ── Pie de página ───────────────────────────────────────────── */
        .footer {
            margin-top: 28px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            font-size: 9.5px;
            color: #999;
            display: flex;
            justify-content: space-between;
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
            <div class="generado">
                Generado el<br><strong>{{ $generadoEn }}</strong>
            </div>
        </div>
        <div class="titulo-reporte">Reporte de Caja por Usuario</div>
    </div>

    {{-- Filtros aplicados --}}
    <div class="filtros-box">
        <span class="label">Período:</span>
        {{ \Carbon\Carbon::parse($filtros['fecha_ini'])->format('d/m/Y') }}
        al
        {{ \Carbon\Carbon::parse($filtros['fecha_fin'])->format('d/m/Y') }}

        @if(!empty($filtros['user_id']) && $cajas->isNotEmpty())
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span class="label">Usuario:</span>
        {{ $cajas->first()?->user?->name ?? '—' }}
        @else
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span class="label">Usuarios:</span> Todos
        @endif
    </div>

    {{-- Resumen --}}
    <div class="resumen">
        <div class="resumen-card">
            <div class="resumen-label">Registros</div>
            <div class="resumen-valor" style="color:#0f3460;">{{ $totalRegistros }}</div>
        </div>
        <div class="resumen-card destacado">
            <div class="resumen-label">Total General</div>
            <div class="resumen-valor">₡{{ number_format($totalMonto, 2) }}</div>
        </div>
        <div class="resumen-card">
            <div class="resumen-label">Promedio por día</div>
            <div class="resumen-valor">
                ₡{{ $totalRegistros > 0 ? number_format($totalMonto / $totalRegistros, 2) : '0.00' }}
            </div>
        </div>
    </div>

    {{-- Tabla de detalle --}}
    @if($cajas->isEmpty())
    <div class="empty">No hay registros para el período y filtros seleccionados.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th class="right">Monto (₡)</th>
                <th>Observación</th>
                <th class="center">Actualizado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cajas as $caja)
            <tr>
                <td><strong>{{ \Carbon\Carbon::parse($caja->fecha)->format('d/m/Y') }}</strong></td>
                <td>{{ $caja->user?->name ?? '—' }}</td>
                <td class="right">{{ number_format($caja->monto, 2) }}</td>
                <td class="muted">{{ $caja->observacion ?: '—' }}</td>
                <td class="center muted">{{ $caja->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total general</strong></td>
                <td class="right">{{ number_format($totalMonto, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Pie --}}
    <div class="footer">
        <span>Sistema Soda — Reporte de Caja por Usuario</span>
        <span>{{ $generadoEn }}</span>
    </div>

</body>
</html>
