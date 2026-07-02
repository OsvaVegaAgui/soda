<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen del Día</title>
    <style>
        @page {
            margin: 1.5cm 2.5cm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            margin: 0 2.5cm;
        }

        .titulo {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .meta {
            margin-bottom: 22px;
            font-size: 11px;
            line-height: 1.7;
        }
        .meta .label { font-weight: 700; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead th {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            padding: 7px 8px;
            font-weight: 700;
            text-align: left;
        }
        thead th.center { text-align: center; }
        thead th.right  { text-align: right; }

        tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #ccc;
        }
        tbody td.center { text-align: center; }
        tbody td.right  { text-align: right; }

        tbody tr:nth-child(even) td { background: #f5f5f5; }

        tfoot td {
            padding: 7px 8px;
            font-weight: 700;
            border-top: 2px solid #000;
        }
        tfoot td.center { text-align: center; }
        tfoot td.right  { text-align: right; }

        .vacio {
            text-align: center;
            color: #555;
            padding: 30px 0;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 8px;
            font-size: 9.5px;
            color: #555;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <div class="titulo">Resumen del Día — Productos Vendidos</div>

    <div class="meta">
        <div><span class="label">Fecha:</span> {{ \Carbon\Carbon::parse($fecha)->translatedFormat('l d \d\e F Y') }}</div>
        <div><span class="label">Vendedor:</span> {{ $vendedor }}</div>
        <div><span class="label">Generado:</span> {{ $generadoEn }}</div>
    </div>

    @if($items->isEmpty())
        <p class="vacio">No hay ventas registradas para esta fecha.</p>
    @else
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Código</th>
                <th class="center">Cant. Vendida</th>
                <th class="right">Total ₡</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->nombre ?: 'Sin nombre' }}</td>
                <td>{{ $item->codigo ?: '—' }}</td>
                <td class="center">{{ $item->total_cantidad }}</td>
                <td class="right">₡{{ number_format($item->total_monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total del día</td>
                <td class="center">{{ $items->sum('total_cantidad') }}</td>
                <td class="right">₡{{ number_format($totalGeneral, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="footer">
        <span>SODA IAC</span>
        <span>{{ $generadoEn }}</span>
    </div>

</body>
</html>
