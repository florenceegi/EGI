<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <title>{{ __('coa_traits.pdf_certificate_title') }} - {{ $serial }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4;
            margin: 1.5cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-weight: 400;
            font-size: 10pt;
            line-height: 1.4;
            color: #1a1a1a;
        }

        /* ✨ BANNER DI VERIFICA (VALIDO/INVALIDO) */
        .verified-banner {
            background-color: #2D5016;
            /* VERDE RINASCITA */
            color: #fff;
            padding: 8pt 12pt;
            margin-bottom: 15pt;
            text-align: center;
            font-size: 11pt;
            font-weight: 600;
        }

        .verified-banner span {
            font-size: 9pt;
            font-weight: 400;
            opacity: 0.9;
            display: block;
            margin-top: 2pt;
        }

        .invalid-banner {
            background-color: #8B0000;
            /* rosso scuro ben visibile */
            color: #fff;
            padding: 8pt 12pt;
            margin-bottom: 15pt;
            text-align: center;
            font-size: 11pt;
            font-weight: 700;
        }

        .header {
            text-align: center;
            padding-bottom: 10pt;
            border-bottom: 2px solid #1B365D;
            margin-bottom: 15pt;
        }

        .company-name {
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            font-size: 20pt;
            color: #1B365D;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .certificate-title {
            font-size: 12pt;
            color: #333;
            margin-top: 5pt;
        }

        .serial {
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            background: #fdfaf6;
            border: 1px solid #D4A574;
            color: #333;
            padding: 4pt 8pt;
            display: inline-block;
            margin-top: 10pt;
        }

        .section {
            margin-bottom: 15pt;
            page-break-inside: avoid;
        }

        .section-title {
            background: #1B365D;
            color: #fff;
            padding: 6pt 10pt;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            font-size: 11pt;
            text-transform: uppercase;
            margin-bottom: 5pt;
            letter-spacing: 0.5px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e0e0e0;
        }

        .info-table td {
            padding: 5pt 8pt;
            border-bottom: 1px solid #e0e0e0;
            font-size: 9.5pt;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            width: 35%;
            background: #f9f9f9;
        }

        .info-value {
            word-break: break-all;
        }

        .hash-value {
            font-family: 'Courier New', monospace;
            font-size: 9pt;
        }

        /* ✨ STILI PER LA NUOVA SEZIONE METADATI */
        .metadata-subsection-title {
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            font-size: 10pt;
            color: #1B365D;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 3pt;
            margin-top: 10pt;
            margin-bottom: 5pt;
        }

        .metadata-subsection-title:first-child {
            margin-top: 0;
        }

        .verification {
            background: #f0f5ed;
            border: 2px solid #2D5016;
            padding: 8pt;
            text-align: center;
            margin: 20pt 0;
        }

        .verification-title {
            background: #2D5016;
            color: #fff;
            padding: 5pt;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            font-size: 10pt;
            margin: -8pt -8pt 8pt -8pt;
        }

        .signature-section {
            display: table;
            width: 100%;
            margin-top: 25pt;
            page-break-inside: avoid;
        }

        .signature-box,
        .stamp-box {
            display: table-cell;
            width: 50%;
            vertical-align: bottom;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin: 40pt 20pt 0 20pt;
            padding-top: 5pt;
            font-size: 9pt;
            color: #6B6B6B;
        }

        .stamp-area {
            height: 80pt;
            border: 2px dashed #cccccc;
            margin: 0 20pt;
            line-height: 80pt;
            color: #aaaaaa;
        }

        .stamp-caption {
            font-size: 9pt;
            color: #6B6B6B;
            margin-top: 5pt;
        }

        /* ✨ STILI PER LA NUOVA TABELLA TECNICA COMPATTA */
        .technical-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 8.5pt;
            /* Riduciamo leggermente il font per compattare */
        }

        .technical-table th,
        .technical-table td {
            border: 1px solid #e0e0e0;
            padding: 4pt;
        }

        .technical-table th {
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .metadata-subsection-table {
            width: 100%;
            border: 1px solid #e0e0e0;
            border-collapse: collapse;
            margin-bottom: 12pt;
            page-break-inside: avoid;
        }

        .metadata-subsection-header {
            background-color: #f7f7f7;
            /* Colore opaco come richiesto */
            text-align: left;
            padding: 5pt 8pt;
            font-family: 'Times New Roman', serif;
            font-weight: bold;
            font-size: 10pt;
            color: #1B365D;
            text-transform: uppercase;
            border-bottom: 1px solid #e0e0e0;
        }
    </style>
</head>

<body>
    @if (!empty($effective_valid))
        <div class="verified-banner">
            {{ __('coa_traits.pdf_verified_banner') }}
            <span>{{ __('coa_traits.pdf_verified_on') }} {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    @else
        <div class="invalid-banner">
            {{ __('coa_traits.pdf_invalid_banner') }}
        </div>
    @endif

    <div class="header">
        <div class="company-name">{{ __('coa_traits.pdf_company_name') }}</div>
        <div class="certificate-title">{{ __('coa_traits.pdf_certificate_title') }}</div>
        <div class="serial">{{ $serial }}</div>
    </div>

    <div class="section">
        <div class="section-title">{{ __('coa_traits.pdf_artwork_info') }}</div>
        <table class="info-table">
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_title') }}:</td>
                <td class="info-value">{{ $egi->title ?? __('coa_traits.no_title_available') }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_internal_id') }}:</td>
                <td class="info-value">#{{ $egi->internal_id ?? $egi->id }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_author') }}:</td>
                <td class="info-value">{{ $creator->name ?? __('coa_traits.no_artist_available') }}</td>
            </tr>
        </table>
    </div>

    @php
        $grouped_traits =
            isset($traits_snapshot) && is_array($traits_snapshot)
                ? collect($traits_snapshot)->groupBy('category')
                : collect();
    @endphp
    @if ($grouped_traits->isNotEmpty())
        <div class="section">
            <div class="section-title">{{ __('coa_traits.pdf_technical_details') }}</div>
            <table class="info-table">
                @foreach ($grouped_traits as $category => $traits)
                    <tr>
                        <td class="info-label">{{ __('coa_traits.category_' . $category) }}:</td>
                        <td class="info-value">{{ $traits->pluck('value')->implode('; ') }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="section">
        <div class="section-title">{{ __('coa_traits.pdf_certificate_details') }}</div>
        <table class="info-table">
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_certificate_id') }}:</td>
                <td class="info-value">{{ $serial }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_creation_date') }}:</td>
                <td class="info-value">{{ $egi->created_at_formatted ?? $egi->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_hash_title') }}:</td>
                <td class="info-value hash-value">{{ $file_hash ?? hash('sha256', $serial) }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_issued_by') }}:</td>
                <td class="info-value">{{ $egi->issued_by ?? __('coa_traits.pdf_powered_by') }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_issue_place') }}:</td>
                <td class="info-value">{{ $egi->issue_place ?? __('coa_traits.pdf_florence_italy') }}</td>
            </tr>
            <tr>
                <td class="info-label">{{ __('coa_traits.pdf_status') }}:</td>
                <td class="info-value">
                    @if (!empty($effective_valid))
                        {{ __('coa_traits.pdf_status_valid') }}
                    @else
                        {{ __('coa_traits.pdf_status_invalid') }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="verification">
        <div class="verification-title">{{ __('coa_traits.pdf_verification_title') }}</div>
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; vertical-align: middle; padding-right: 15px;">
                {{ __('coa_traits.pdf_scan_prompt') }}</div>
            @if (isset($qr_code))
                <div style="display: table-cell; width: 80pt; text-align: right;">
                    <img src="{{ $qr_code }}" alt="{{ __('coa_traits.pdf_qr_code_title') }}"
                        style="width: 80pt; height: 80pt;" />
                </div>
            @endif
        </div>
    </div>

    @php
        $grouped_metadata =
            isset($additional_metadata) && is_array($additional_metadata)
                ? collect($additional_metadata)->groupBy('category')
                : collect();
    @endphp
    @if ($grouped_metadata->isNotEmpty())
        <div class="section">
            <div class="section-title">{{ __('coa_traits.pdf_additional_info_title') }}</div>

            @foreach ($grouped_metadata as $category => $metadata_items)
                <table class="metadata-subsection-table">
                    <thead>
                        <tr>
                            <th class="metadata-subsection-header" colspan="2">
                                {{ strtoupper(str_replace('_', ' ', $category)) }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {{-- Usiamo una cella che si estende per contenere la tabella interna --}}
                            <td colspan="2" style="padding: 0;">

                                {{-- Logica condizionale per layout 'technical' --}}
                                @if ($category === 'technical')
                                    <div style="padding: 8pt;">
                                        <table class="technical-table">
                                            <thead>
                                                <tr>
                                                    @foreach ($metadata_items as $item)
                                                        <th>{{ $item['label'] }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    @foreach ($metadata_items as $item)
                                                        <td>{{ $item['value'] }}</td>
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    {{-- RIPRISTINO DELLA "info-table" INTERNA PER TUTTE LE ALTRE CATEGORIE --}}
                                    <table class="info-table" style="border: none; width: 100%;">
                                        @foreach ($metadata_items as $item)
                                            <tr>
                                                <td class="info-label" style="width: 35%;">{{ $item['label'] }}:</td>
                                                <td class="info-value">{{ $item['value'] }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif

                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                {{ __('coa_traits.pdf_author_signature') }} —
                @if (!empty($author_signed))
                    {{ __('coa_traits.pdf_signature_present') }}
                @else
                    {{ __('coa_traits.pdf_signature_missing') }}
                @endif
            </div>
            <div class="signature-line" style="margin-top:10pt">
                {{ __('coa_traits.pdf_inspector_countersign') }} —
                @if (!empty($inspector_countersigned))
                    {{ __('coa_traits.pdf_signature_present') }}
                @else
                    {{ __('coa_traits.pdf_signature_missing') }}
                @endif
            </div>
            <div class="signature-line" style="margin-top:10pt">
                {{ __('coa_traits.pdf_timestamp') }} —
                @if (!empty($timestamped))
                    {{ __('coa_traits.pdf_signature_present') }}
                @else
                    {{ __('coa_traits.pdf_signature_missing') }}
                @endif
            </div>
        </div>
        <div class="stamp-box">
            <div class="stamp-area">{{ __('coa_traits.pdf_stamp_area') }}</div>
            <div class="stamp-caption">{{ __('coa_traits.pdf_stamp_caption') }}</div>
        </div>
    </div>
</body>

</html>
