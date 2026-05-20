<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff;
            padding: 6px;
        }

        .grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
        }

        .grid-table td {
            width: 25%;
            vertical-align: top;
            padding: 0;
        }

        .tiquete {
            border: 2px solid #000;
            padding: 6px 6px 8px 6px;
            text-align: center;
            height: 88px;
            overflow: hidden;
        }

        .tiq-numero {
            font-size: 8px;
            color: #000;
            text-align: center;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .tiq-nombre {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 6px;
            line-height: 1.15;
        }

        .tiq-barcode {
            display: none;
        }

        .tiq-fecha {
            font-size: 12px;
            color: #000;
        }

        .td-vacio { width: 25%; }
    </style>
</head>
<body>

@foreach ($filas as $fila)
    <table class="grid-table">
        <tr>
            @foreach ($fila as $t)
                <td>
                    <div class="tiquete">
                        <div class="tiq-numero">{{ $t['numero'] }}</div>
                        <div class="tiq-nombre">{{ $t['nombre'] }}</div>
                        <div class="tiq-barcode"><img src="data:image/png;base64,{{ $t['barcode'] }}"></div>
                        <div class="tiq-fecha">{{ $t['fecha'] }}</div>
                    </div>
                </td>
            @endforeach

            @for ($i = count($fila); $i < 4; $i++)
                <td class="td-vacio"></td>
            @endfor
        </tr>
    </table>
@endforeach

</body>
</html>
