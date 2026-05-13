<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ── Header ── */
        .header-table {
            width: 100%;
            margin-bottom: 12px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .company-subtitle {
            font-size: 8px;
            color: #555;
        }

        .doc-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        .company-right {
            text-align: right;
            font-size: 10px;
            font-weight: bold;
        }

        /* ── Datos de ingreso ── */
        .entry-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .entry-data td {
            padding: 4px 6px;
            border: 1px solid #000;
            font-size: 11px;
        }

        .entry-data .field-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
        }

        .entry-data .field-value {
            font-size: 12px;
        }

        /* ── Norma divider ── */
        .norma {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            padding: 6px 0;
            margin: 8px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }

        /* ── Ensayo Table ── */
        .ensayo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .ensayo-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }

        .ensayo-table td {
            border: 1px solid #000;
            padding: 5px 10px;
            font-size: 11px;
            vertical-align: middle;
        }

        .ensayo-table .row-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            width: 50%;
        }

        .ensayo-table .row-value {
            text-align: center;
            width: 50%;
        }

        .cumple-header {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border: 1px solid #000;
            padding: 4px;
            background-color: #f0f0f0;
        }

        /* S/N badges */
        .badge-s {
            display: inline-block;
            background-color: #f1c40f;
            color: #000;
            font-weight: bold;
            padding: 2px 8px;
            font-size: 11px;
        }

        .badge-n {
            display: inline-block;
            color: #999;
            padding: 2px 8px;
            font-size: 11px;
        }

        .badge-s-active {
            background-color: #f1c40f;
            font-weight: bold;
            padding: 2px 8px;
        }

        .badge-n-active {
            background-color: #e74c3c;
            color: #fff;
            font-weight: bold;
            padding: 2px 8px;
        }

        /* ── Firmas ── */
        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .firmas-table td {
            border: 1px solid #000;
            padding: 5px 10px;
            font-size: 11px;
        }

        .firmas-table .firma-label {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            width: 20%;
        }

        .firmas-table .firma-value {
            width: 30%;
            min-height: 20px;
        }
    </style>
</head>

<body>

    <!-- ═══ HEADER ═══ -->
    <table class="header-table">
        <tr>
            <td width="30%">
                <img src="{{ public_path('storage/ikhana-logo.png') }}" style="width: 120px;">
            </td>
            <td width="40%" class="doc-title">
                Protocolo de Ensayo
            </td>
            <td width="30%" class="company-right">
                IKHANA TECHNOLOGY
            </td>
        </tr>
    </table>

    <!-- ═══ DATOS DEL INGRESO ═══ -->
    <table class="entry-data">
        <!-- Row 1: Proveedor + Fecha -->
        <tr>
            <td colspan="4">
                <span class="field-label">Proveedor:</span>
                <span class="field-value">{{ $entry->provider->fantasy_name ?? '-' }}</span>
            </td>
            <td colspan="2">
                <span class="field-label">Fecha:</span>
                <span class="field-value">{{ \Carbon\Carbon::parse($entry->test->test_date)->format('d/m/Y') }}</span>
            </td>
        </tr>
        <!-- Row 2: Remito + Lote -->
        <tr>
            <td colspan="4">
                <span class="field-label">Remito:</span>
                <span class="field-value">{{ $entry->remito }}</span>
            </td>
            <td colspan="2">
                <span class="field-label">Lote:</span>
                <span class="field-value">{{ $entry->batch }}</span>
            </td>
        </tr>
        <!-- Row 3: Materia + Medida Diámetro -->
        <tr>
            <td colspan="2">
                <span class="field-label">Materia:</span>
                <span class="field-value">{{ $entry->type->name ?? '-' }}</span>
            </td>
            <td colspan="2">
                <span class="field-label">Medida Diámetro:</span>
                <span class="field-value">{{ $entry->characteristic->decimal_value ?? '-' }}</span>
            </td>
            <td colspan="2">&nbsp;</td>
        </tr>
        <!-- Row 4: Kilogramos -->
        <tr>
            <td colspan="6">
                <span class="field-label">Kilogramos:</span>
                <span class="field-value">{{ number_format($entry->quantity_kg, 3, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <!-- ═══ NORMA ═══ -->
    <div class="norma">NORMA IRAM 2011</div>

    <!-- ═══ TABLA DE ENSAYO: VALORES A CUMPLIR / VALORES MEDIDOS ═══ -->
    <table class="ensayo-table">
        <thead>
            <tr>
                <th>Valores a Cumplir</th>
                <th>Valores Medidos</th>
            </tr>
        </thead>
        <tbody>
            <!-- Diámetro Máximo -->
            <tr>
                <td class="row-label">
                    Ø Máximo: {{ $entry->characteristic->decimal_value ?? '-' }} mm
                </td>
                <td class="row-value">&nbsp;</td>
            </tr>
            <!-- Diámetro Mínimo -->
            <tr>
                <td class="row-label">
                    Ø Mínimo:
                    {{ $entry->characteristic->decimal_value ? number_format($entry->characteristic->decimal_value - 0.01, 2, ',', '.') : '-' }}
                    mm
                </td>
                <td class="row-value">&nbsp;</td>
            </tr>

            <!-- Cumple S/N header -->
            <tr>
                <td class="cumple-header" colspan="2">Cumple S/N</td>
            </tr>

            <!-- Bobinado -->
            <tr>
                <td class="row-label">Bobinado</td>
                <td class="row-value">
                    @if($entry->test)
                        <span class="{{ $entry->test->check_winding ? 'badge-s-active' : 'badge-s' }}">S</span>
                        &nbsp;&nbsp;
                        <span class="{{ !$entry->test->check_winding ? 'badge-n-active' : 'badge-n' }}">N</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <!-- Limpieza -->
            <tr>
                <td class="row-label">Limpieza</td>
                <td class="row-value">
                    @if($entry->test)
                        <span class="{{ $entry->test->check_cleanliness ? 'badge-s-active' : 'badge-s' }}">S</span>
                        &nbsp;&nbsp;
                        <span class="{{ !$entry->test->check_cleanliness ? 'badge-n-active' : 'badge-n' }}">N</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <!-- Acondicionado -->
            <tr>
                <td class="row-label">Acondicionado</td>
                <td class="row-value">
                    @if($entry->test)
                        <span class="{{ $entry->test->check_packaging ? 'badge-s-active' : 'badge-s' }}">S</span>
                        &nbsp;&nbsp;
                        <span class="{{ !$entry->test->check_packaging ? 'badge-n-active' : 'badge-n' }}">N</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <!-- Identificación -->
            <tr>
                <td class="row-label">Identificación</td>
                <td class="row-value">
                    @if($entry->test)
                        <span class="{{ $entry->test->check_identification ? 'badge-s-active' : 'badge-s' }}">S</span>
                        &nbsp;&nbsp;
                        <span class="{{ !$entry->test->check_identification ? 'badge-n-active' : 'badge-n' }}">N</span>
                    @else
                        -
                    @endif
                </td>
            </tr>

            <!-- Resistencia Ohmica -->
            <tr>
                <td class="row-label">
                    Resistencia Ohmica:
                    @if($entry->characteristic && $entry->characteristic->iramCopperMaxResistance)
                        {{ number_format($entry->characteristic->iramCopperMaxResistance->max_resistance_ohm_km, 1, ',', '.') }}
                        Ω/Km
                    @else
                        - Ω/Km
                    @endif
                </td>
                <td class="row-value">
                    @if($entry->test)
                        {{ number_format($entry->test->resistance_ohm_km, 1, ',', '.') }} Ω/Km
                    @else
                        -
                    @endif
                </td>
            </tr>

            <!-- Estiramiento (placeholder for future) -->
            <tr>
                <td class="row-label">Estiramiento: &nbsp;&nbsp;&nbsp; 21%</td>
                <td class="row-value">21 %</td>
            </tr>
        </tbody>
    </table>

    <!-- ═══ FIRMAS ═══ -->
    <table class="firmas-table">
        <tr>
            <td class="firma-label">Realizó:</td>
            <td class="firma-value">&nbsp;</td>
            <td class="firma-label">Controló:</td>
            <td class="firma-value">&nbsp;</td>
        </tr>
        <tr>
            <td class="firma-label">Aprobó:</td>
            <td class="firma-value">&nbsp;</td>
            <td class="firma-label">Fecha:</td>
            <!-- TODO: Chequear fecha de ? Cual es la diferencia con la de arriba  -->
            <td class="firma-value">{{ \Carbon\Carbon::parse($entry->test->test_date)->format('d/m/Y') }}</td>
        </tr>
    </table>

</body>

</html>