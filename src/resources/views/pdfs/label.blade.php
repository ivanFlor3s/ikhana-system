<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 0.1cm;
        }

        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 5px;
        }

        .container {
            width: 100%;
        }

        .header {
            margin-bottom: 5px;
        }

        .logo {
            max-width: 100px;
            height: auto;
            margin-bottom: 5px;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px;
        }

        .label {
            font-weight: bold;
            text-decoration: underline;
        }

        .value {
            margin-bottom: 2px;
        }

        .barcode {
            margin-top: 10px;
            text-align: center;
        }

        .barcode img {
            height: 30px;
            width: auto;
        }

        .footer-code {
            text-align: center;
            font-size: 8px;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Logo -->
        <div class="header">
            @if(file_exists(public_path('storage/ikhana_logo_colour.png')))
                <img src="{{ public_path('storage/ikhana_logo_colour.png') }}" class="logo" alt="IKHANA Logo">
            @else
                <strong>IKHANA TECHNOLOGY</strong>
            @endif
        </div>

        <div class="title">Ingreso de Cobre #{{ $entry->entry_number ?? $entry->id }}</div>

        <table>
            <tr>
                <!-- Columna Izquierda -->
                <td width="33%">
                    <div class="field">
                        <span class="label">Proveedor:</span> {{ $entry->provider->business_name }}
                    </div>
                    @if($entry->provider->fantasy_name)
                        <div class="value">{{ $entry->provider->fantasy_name }}</div>
                    @endif

                    <div class="field" style="margin-top: 5px;">
                        <span class="label">Código:</span> DIÁMETRO {{ $entry->characteristic->decimal_value }}
                    </div>

                    <div class="field" style="margin-top: 5px;">
                        <span class="label">Fecha:</span>
                        {{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}
                    </div>
                </td>

                <!-- Columna Central -->
                <td width="33%">
                    <div class="field">
                        <span class="label">Peso:</span> {{ number_format($entry->quantity_kg, 2, ',', '.') }}
                        kilogramos
                    </div>
                    <div class="field">
                        <span class="label">Bobinas:</span> {{ $entry->coils_count }}
                    </div>
                    <div class="field">
                        <span class="label">N° Lote:</span> {{ $entry->batch }}
                    </div>
                    <div class="field">
                        <span class="label">Remito:</span> {{ $entry->remito }}
                    </div>
                </td>

                <!-- Columna Derecha -->
                <td width="33%">
                    <div class="field">
                        <span class="label">ID Embalaje:</span>
                        {{ $entry->test && $entry->test->check_packaging ? 'Cumple' : 'No Cumple' }}
                    </div>
                    <div class="field">
                        <span class="label">Aspecto superficial:</span>
                        {{ $entry->test && $entry->test->check_cleanliness ? 'Cumple' : 'No Cumple' }}
                    </div>
                    <div class="field">
                        <span class="label">Resistencia:</span>
                        @if($entry->test)
                            {{ number_format($entry->test->resistance_ohm_km, 2, ',', '.') }} ohm
                        @else
                            -
                        @endif
                    </div>
                    <div class="field">
                        <span class="label">Resultado:</span> {{ $entry->test ? $entry->test->result : 'Pendiente' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Barcode (Batch or Remito or ID?) Using Batch for now as implied by image being distinct -->
        <div class="barcode">
            <img src="data:image/png;base64,{{ $barcode }}" alt="barcode">
            <div class="footer-code">{{ $entry->batch }}</div>
        </div>
    </div>
</body>

</html>