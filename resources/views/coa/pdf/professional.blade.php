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
            margin: 15mm;
        }

        body {
            font-fami                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_size') }}:</span>
                        <span class="metadata-value">{{ $egi->size ?? 'N/A' }}</span>
                    </div>'Times New Roman', serif;
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

        /* LAYOUT SEMPLICE A DUE COLONNE */
        .content {
            display: table;
            width: 100%;
            margin-top: 15pt;
        }

        .left-col {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            padding-right: 10pt;
        }

        .right-col {
            display: table-cell;
            width: 65%;
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
            width: 60pt;
            height: 60pt;
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
    {{-- HEADER COMPATTO --}}
    <div class="header">
        <div class="company-name">{{ __('coa_traits.pdf_company_name') }}</div>
        <div class="certificate-title">{{ __('coa_traits.pdf_certificate_title') }}</div>
        <div class="serial">{{ $serial }}</div>
    </div>

    {{-- CONTENUTO A DUE COLONNE --}}
    <div class="content">
        {{-- COLONNA SINISTRA --}}
        <div class="left-col">
            {{-- QR CODE --}}
            <div class="qr-section" style="text-align: center; margin-bottom: 15pt;">
                <div style="font-weight: bold; margin-bottom: 8pt; color: #8B4513;">{{ __('coa_traits.pdf_qr_code_title') }}</div>
                @if(isset($qr_code))
                    <div style="display: inline-block; border: 1px solid #ddd; padding: 5pt;">
                        <img src="{{ $qr_code }}" alt="QR Code" style="width: 100pt; height: 100pt;">
                    </div>
                    <div style="font-size: 8pt; color: #666; margin-top: 4pt;">
                        {{ __('coa_traits.pdf_scan_to_verify') }}
                    </div>
                @else
                    <div style="border: 1px solid #ccc; padding: 20pt; background: #f0f0f0; width: 100pt; height: 100pt; margin: 0 auto;">
                        <div style="color: #666; font-size: 8pt;">{{ __('coa_traits.pdf_qr_not_available') }}</div>
                    </div>
                @endif
            </div>

            {{-- METADATA TECNICI COMPLETI --}}
            <div class="info-section" style="margin-top: 15pt;">
                <div class="section-title">{{ __('coa_traits.pdf_technical_metadata') }}</div>
                <div class="metadata-grid">
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_internal_id') }}:</span>
                        <span class="metadata-value">{{ $egi->id ?? __('coa_traits.not_available') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_certificate_type') }}:</span>
                        <span class="metadata-value">{{ __('coa_traits.pdf_core_certificate') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_blockchain_status') }}:</span>
                        <span class="metadata-value">{{ __('coa_traits.pdf_verified_on_chain') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_upload_date') }}:</span>
                        <span class="metadata-value">{{ isset($egi->created_at) ? date('d/m/Y H:i', strtotime($egi->created_at)) : __('coa_traits.not_available') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_publication_status') }}:</span>
                        <span class="metadata-value">{{ !empty($egi->is_published) ? __('coa_traits.pdf_published') : __('coa_traits.pdf_not_published') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_file_size') }}:</span>
                        <span class="metadata-value">{{ $egi->size ?? __('coa_traits.not_available') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_image_dimensions') }}:</span>
                        <span class="metadata-value">{{ $egi->dimension ?? __('coa_traits.not_available') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_file_type') }}:</span>
                        <span class="metadata-value">{{ $egi->file_mime ?? __('coa_traits.not_available') }}</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">{{ __('coa_traits.pdf_file_extension') }}:</span>
                        <span class="metadata-value">{{ $egi->extension ? strtoupper($egi->extension) : __('coa_traits.not_available') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNA DESTRA --}}
        <div class="right-col">
            {{-- INFORMAZIONI OPERA --}}
            <div class="info-section">
                <div class="section-title">{{ __('coa_traits.pdf_artwork_info') }}</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_title') }}:</td>
                        <td class="info-value">{{ $egi->title ?? __('coa_traits.no_title_available') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_author') }}:</td>
                        <td class="info-value">{{ $creator->name ?? __('coa_traits.no_artist_available') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_description') }}:</td>
                        <td class="info-value">{{ $egi->description ?? __('coa_traits.no_description_available') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_collection') }}:</td>
                        <td class="info-value">{{ $egi->collection->collection_name ?? __('coa_traits.no_collection_assigned') }}</td>
                    </tr>
                </table>
            </div>

            {{-- DETTAGLI CERTIFICATO --}}
            <div class="info-section">
                <div class="section-title">{{ __('coa_traits.pdf_certificate_details') }}</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">{{ __('coa_traits.pdf_issue_date') }}:</td>
                        <td class="info-value">
                            {{ $issued_at ? date('d/m/Y H:i', strtotime($issued_at)) : date('d/m/Y H:i') }}</td>
                    </tr>
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

            {{-- CARATTERISTICHE TECNICHE SEMPLIFICATE --}}
            <div class="info-section">
                <div class="section-title">{{ __('coa_traits.pdf_technical_traits') }}</div>

                {{-- TRAITS DISPLAY --}}
                <div class="traits-section">
                    {{-- TRAITS SECTION --}}
                    @php
                        $categorized = [
                            'technique' => [],
                            'materials' => [],
                            'support' => [],
                        ];

                        if (isset($traits_snapshot) && is_array($traits_snapshot)) {
                            foreach ($traits_snapshot as $trait) {
                                if (
                                    isset($trait['category']) &&
                                    isset($trait['value']) &&
                                    isset($categorized[$trait['category']])
                                ) {
                                    $categorized[$trait['category']][] = $trait['value'];
                                }
                            }
                        }
                    @endphp

                    {{-- TECNICA --}}
                    <div class="trait-row">
                        <div class="trait-category-title">{{ __('coa_traits.category_technique') }}</div>
                        <div class="trait-values">
                            @if (count($categorized['technique']) > 0)
                                {{ implode('; ', $categorized['technique']) }}
                            @else
                                <span class="trait-empty">{{ __('coa_traits.no_technique_selected') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- MATERIALI --}}
                    <div class="trait-row">
                        <div class="trait-category-title">{{ __('coa_traits.category_materials') }}</div>
                        <div class="trait-values">
                            @if (count($categorized['materials']) > 0)
                                {{ implode('; ', $categorized['materials']) }}
                            @else
                                <span class="trait-empty">{{ __('coa_traits.no_materials_selected') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- SUPPORTO --}}
                    <div class="trait-row">
                        <div class="trait-category-title">{{ __('coa_traits.category_support') }}</div>
                        <div class="trait-values">
                            @if (count($categorized['support']) > 0)
                                {{ implode('; ', $categorized['support']) }}
                            @else
                                <span class="trait-empty">{{ __('coa_traits.no_support_selected') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEZIONE VERIFICA --}}
    <div class="verification">
        <div class="verification-title">{{ __('coa_traits.pdf_verified_banner') }}</div>
        <div class="verification-text">
            {{ __('coa_traits.pdf_verified_on') }}: {{ now()->format('d/m/Y H:i') }} |
            {{ __('coa_traits.pdf_verification_timestamp') }}: {{ now()->timestamp }}
        </div>
    </div>

    {{-- HASH SICUREZZA --}}
    <div class="hash-section">
        <div class="hash-title">{{ __('coa_traits.pdf_hash_title') }}</div>
        <div class="hash-value">{{ hash('sha256', $serial) }}</div>
    </div>

    {{-- SEZIONE FIRMA --}}
    <div class="signature">
        <div class="signature-title">{{ __('coa_traits.pdf_signature_section') }}</div>
        <div class="signature-boxes">
            <div class="signature-box">
                <div class="signature-line">{{ __('coa_traits.pdf_authorized_signature') }}</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">{{ __('coa_traits.pdf_date_and_stamp') }}</div>
            </div>
        </div>
    </div>

    {{-- FOOTER MINIMO --}}
    <div class="footer">
        {{ __('coa_traits.pdf_powered_by') }} | {{ __('coa_traits.pdf_last_update') }}:
        {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
