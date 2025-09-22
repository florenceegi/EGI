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
            font-size: 10pt;
            line-height: 1.3;
            color: #1a1a1a;
            background: #ffffff;
        }

        /* HEADER SEMPLIFICATO */
        .header {
            text-align: center;
            border-bottom: 2px solid #8B4513;
            padding-bottom: 12pt;
            margin-bottom: 15pt;
        }

        .company-name {
            font-size: 20pt;
            font-weight: bold;
            color: #8B4513;
            text-transform: uppercase;
            letter-spacing: 2pt;
            margin-bottom: 6pt;
        }

        .certificate-title {
            font-size: 16pt;
            font-weight: bold;
            color: #2F4F4F;
            margin-bottom: 6pt;
            text-transform: uppercase;
            letter-spacing: 1pt;
        }

        .serial {
            font-family: 'Courier New', monospace;
            font-size: 11pt;
            font-weight: bold;
            background: #f5f5f5;
            border: 1px solid #ccc;
            padding: 4pt 8pt;
            display: inline-block;
            color: #8B4513;
        }

        /* LAYOUT MODIFICATO: SINISTRA 65%, DESTRA 35% */
        .content {
            display: table;
            width: 100%;
            margin-top: 15pt;
        }

        .left-col {
            display: table-cell;
            width: 65%;
            vertical-align: top;
            padding-right: 10pt;
        }

        .right-col {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            padding-left: 10pt;
        }

        /* IMMAGINE SEMPLIFICATA */
        .artwork-image {
            width: 100%;
            max-height: 120pt;
            object-fit: contain;
            border: 1px solid #ccc;
            margin-bottom: 10pt;
        }

        .image-placeholder {
            width: 100%;
            height: 120pt;
            background: #f8f8f8;
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 9pt;
            font-style: italic;
            margin-bottom: 15pt;
            text-align: center;
        }

        /* TABELLE SEMPLICI */
        .info-section {
            margin-bottom: 12pt;
        }

        .section-title {
            background: #8B4513;
            color: white;
            padding: 6pt 8pt;
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            margin-bottom: 8pt;
        }

        .info-table td {
            padding: 4pt 6pt;
            border-bottom: 1px dotted #ddd;
            vertical-align: top;
            font-size: 9pt;
        }

        .info-label {
            font-weight: bold;
            color: #2F4F4F;
            width: 35%;
            background: #f8f8f8;
        }

        .info-value {
            color: #1a1a1a;
        }

        /* TRAITS SEMPLIFICATI */
        .traits-section {
            margin-top: 12pt;
        }

        .trait-row {
            margin-bottom: 8pt;
        }

        .trait-category-title {
            background: #f0f0f0;
            padding: 4pt 6pt;
            font-weight: bold;
            color: #2F4F4F;
            font-size: 9pt;
            text-transform: uppercase;
            border: 1px solid #ddd;
            border-bottom: none;
        }

        .trait-values {
            padding: 6pt;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            min-height: 20pt;
            font-size: 9pt;
        }

        .trait-item {
            margin-bottom: 2pt;
        }

        .trait-empty {
            color: #888;
            font-style: italic;
        }

        /* SEZIONE VERIFICA COMPATTA */
        .verification {
            background: #e8f5e8;
            border: 2px solid #228B22;
            border-radius: 3pt;
            padding: 8pt;
            text-align: center;
            margin: 15pt 0;
        }

        .verification-title {
            background: #228B22;
            color: white;
            padding: 6pt;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: -8pt -8pt 6pt -8pt;
        }

        .verification-text {
            font-size: 9pt;
            color: #333;
        }

        /* HASH COMPATTO */
        .hash-section {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 6pt;
            margin: 10pt 0;
        }

        .hash-title {
            font-weight: bold;
            color: #8B4513;
            font-size: 9pt;
            margin-bottom: 3pt;
        }

        .hash-value {
            font-family: 'Courier New', monospace;
            font-size: 7pt;
            color: #666;
            word-break: break-all;
        }

        /* QR CODE */
        .qr-section {
            text-align: center;
            margin: 10pt 0;
        }

        .qr-code img {
            width: 35mm;
            height: 35mm;
            border: 1pt solid #8B4513;
        }

        .qr-label {
            font-size: 8pt;
            color: #666;
            margin-top: 3pt;
        }

        /* FIRMA COMPATTA */
        .signature {
            margin-top: 15pt;
            border-top: 1px solid #ddd;
            padding-top: 10pt;
        }

        .signature-title {
            text-align: center;
            font-weight: bold;
            color: #2F4F4F;
            margin-bottom: 8pt;
            font-size: 10pt;
        }

        .signature-boxes {
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 10pt;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 25pt;
            padding-top: 3pt;
            font-size: 8pt;
            color: #666;
        }

        /* FOOTER MINIMO */
        .footer {
            position: fixed;
            bottom: 8mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            font-size: 7pt;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 4pt;
        }

        /* METADATA COMPATTO */
        .metadata-grid {
            font-size: 8pt;
        }

        .metadata-row {
            margin-bottom: 3pt;
        }

        .metadata-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 40%;
        }

        .metadata-value {
            color: #333;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <div class="header">
        <div class="company-name">{{ __('coa_traits.pdf_company_name') }}</div>
        <div class="certificate-title">{{ __('coa_traits.pdf_certificate_title') }}</div>
        <div class="serial">{{ $serial }}</div>
    </div>

    <!-- CONTENT A DUE COLONNE -->
    <div class="content">
        <!-- COLONNA SINISTRA: TUTTE LE INFO -->
        <div class="left-col">
            <!-- INFORMAZIONI OPERA -->
            <div class="info-section">
                <div class="section-title">{{ __('coa_traits.pdf_artwork_info') }}</div>

                <!-- TITOLO IN EVIDENZA -->
                @if ($egi->title)
                    <h2 style="font-size: 14pt; font-weight: bold; color: #000; margin-bottom: 8pt;">
                        {{ $egi->title }}
                    </h2>
                @endif

                <!-- ID INTERNO SE DISPONIBILE -->
                @if ($egi->internal_id || $egi->id)
                    <div style="font-size: 9pt; color: #666; font-style: italic; margin-bottom: 10pt;">
                        {{ __('coa_traits.pdf_internal_id') }}: {{ $egi->internal_id ?? $egi->id }}
                    </div>
                @endif

                <table class="info-table">
                    @if ($creator->name ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_author') }}:</td>
                            <td class="info-value">{{ $creator->name }}</td>
                        </tr>
                    @endif

                    @if ($egi->year ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_year') }}:</td>
                            <td class="info-value">{{ $egi->year }}</td>
                        </tr>
                    @endif

                    @if ($egi->dimension ?? ($egi->size ?? null))
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_size') }}:</td>
                            <td class="info-value">{{ $egi->dimension ?? $egi->size }}</td>
                        </tr>
                    @endif

                    @if ($egi->edition ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_edition') }}:</td>
                            <td class="info-value">{{ $egi->edition }}</td>
                        </tr>
                    @endif

                    @if ($egi->description ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_description') }}:</td>
                            <td class="info-value">{{ $egi->description }}</td>
                        </tr>
                    @endif

                    @if ($egi->collection->collection_name ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_collection') }}:</td>
                            <td class="info-value">{{ $egi->collection->collection_name }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- DETTAGLI CERTIFICATO -->
            <div class="info-section">
                <div class="section-title">{{ __('coa_traits.pdf_certificate_details') }}</div>

                <table class="info-table">
                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_certificate_id') }}:</td>
                        <td class="info-value" style="font-family: 'Courier New', monospace; font-weight: bold;">{{ $serial }}</td>
                    </tr>

                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_creation_date') }}:</td>
                        <td class="info-value">{{ $issued_at ? date('d/m/Y H:i', strtotime($issued_at)) : date('d/m/Y H:i') }}</td>
                    </tr>

                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_blockchain_hash') }}:</td>
                        <td class="info-value" style="font-family: 'Courier New', monospace; font-size: 8pt; word-break: break-all; line-height: 1.2;">{{ hash('sha256', $serial) }}</td>
                    </tr>

                    @if ($egi->location ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_location') }}:</td>
                            <td class="info-value">{{ $egi->location }}</td>
                        </tr>
                    @endif

                    @if ($egi->provenance ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_provenance') }}:</td>
                            <td class="info-value">{{ $egi->provenance }}</td>
                        </tr>
                    @endif

                    @if ($egi->conservation_state ?? null)
                        <tr>
                            <td class="info-label">{{ __('coa_traits.pdf_conservation') }}:</td>
                            <td class="info-value">{{ $egi->conservation_state }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_issued_by') }}:</td>
                        <td class="info-value">{{ __('coa_traits.pdf_powered_by') }}</td>
                    </tr>

                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_issue_place') }}:</td>
                        <td class="info-value">{{ __('coa_traits.pdf_florence_italy') }}</td>
                    </tr>

                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_status') }}:</td>
                        <td class="info-value">{{ __('coa_traits.pdf_status_valid') }}</td>
                    </tr>
                </table>
            </div>

            <!-- TECNICA/MATERIALI/SUPPORTO -->
            @if (isset($traits_snapshot) && is_array($traits_snapshot) && count($traits_snapshot) > 0)
                <div class="info-section">
                    <div class="section-title">{{ __('coa_traits.pdf_technical_details') }}</div>

                    @php
                        $categorized = [
                            'technique' => [],
                            'materials' => [],
                            'support' => [],
                        ];

                        foreach ($traits_snapshot as $trait) {
                            if (
                                isset($trait['category']) &&
                                isset($trait['value']) &&
                                isset($categorized[$trait['category']])
                            ) {
                                $categorized[$trait['category']][] = $trait['value'];
                            }
                        }
                    @endphp

                    @foreach (['technique', 'materials', 'support'] as $category)
                        @if (count($categorized[$category]) > 0)
                            <div class="traits-group">
                                <div class="trait-category">{{ __('coa_traits.category_' . $category) }}</div>
                                <div class="trait-values">
                                    @foreach ($categorized[$category] as $trait_value)
                                        <div class="trait-item">{{ $trait_value }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <!-- COLONNA DESTRA: QR CODE -->
        <div class="right-col">
            <!-- QR CODE (OBBLIGATORIO) -->
            <div class="qr-section">
                @if (isset($qr_code))
                    <div class="qr-code">
                        <img src="{{ $qr_code }}" alt="QR Code verifica" />
                    </div>
                    <div class="qr-label">{{ __('coa_traits.pdf_scan_to_verify') }}</div>
                    @if (isset($verification_url))
                        <div class="qr-url">{{ $verification_url }}</div>
                    @endif
                @else
                    <div
                        style="width: 35mm; height: 35mm; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 8pt; color: #666;">
                        QR Non<br />Disponibile
                    </div>
                    <div class="qr-label">{{ __('coa_traits.pdf_scan_to_verify') }}</div>
                @endif
            </div>

            <!-- HASH CERTIFICATO -->
            <div class="info-section">
                <div class="section-title">{{ __('coa_traits.pdf_hash_title') }}</div>
                <div class="hash-value">{{ $file_hash ?? hash('sha256', $serial) }}</div>
            </div>
        </div>
    </div>

    <!-- FOOTER MINIMO -->
    <div class="footer">
        {{ __('coa_traits.pdf_powered_by') }}
    </div>

</body>

</html>
