<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <title>Certificato di Autenticità - {{ $serial }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #1a1a1a;
        }

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
            margin-bottom: 10pt;
            text-align: center;
        }

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

        .metadata-row {
            margin-bottom: 3pt;
            font-size: 8pt;
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
    {{-- HEADER --}}
    <div class="header">
        <div class="company-name">FlorenceEGI</div>
        <div class="certificate-title">Certificato di Autenticità</div>
        <div class="serial">{{ $serial }}</div>
    </div>

    {{-- CONTENUTO --}}
    <div class="content">
        {{-- COLONNA SINISTRA --}}
        <div class="left-col">
            {{-- IMMAGINE --}}
            <div class="image-placeholder">
                Immagine Opera
            </div>

            {{-- METADATA --}}
            <div class="info-section">
                <div class="section-title">Metadata Tecnici</div>
                <div style="padding: 6pt;">
                    <div class="metadata-row">
                        <span class="metadata-label">Data Creazione:</span>
                        <span class="metadata-value">2025-09-04</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">ID Interno:</span>
                        <span class="metadata-value">135</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">Tipo Certificato:</span>
                        <span class="metadata-value">Certificato Base</span>
                    </div>
                    <div class="metadata-row">
                        <span class="metadata-label">Stato Blockchain:</span>
                        <span class="metadata-value">Verificato</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLONNA DESTRA --}}
        <div class="right-col">
            {{-- INFORMAZIONI OPERA --}}
            <div class="info-section">
                <div class="section-title">Informazioni Opera</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Titolo:</td>
                        <td class="info-value">#0008068</td>
                    </tr>
                    <tr>
                        <td class="info-label">Autore:</td>
                        <td class="info-value">Il Cherici</td>
                    </tr>
                    <tr>
                        <td class="info-label">Descrizione:</td>
                        <td class="info-value">Anello opaco molto bello anche sul dito</td>
                    </tr>
                    <tr>
                        <td class="info-label">Collezione:</td>
                        <td class="info-value">Florence</td>
                    </tr>
                </table>
            </div>

            {{-- DETTAGLI CERTIFICATO --}}
            <div class="info-section">
                <div class="section-title">Dettagli Certificato</div>
                <table class="info-table">
                    <tr>
                        <td class="info-label">Data Emissione:</td>
                        <td class="info-value">22/09/2025 07:31</td>
                    </tr>
                    <tr>
                        <td class="info-label">Emesso da:</td>
                        <td class="info-value">Powered by FlorenceEGI</td>
                    </tr>
                    <tr>
                        <td class="info-label">Luogo emissione:</td>
                        <td class="info-value">Firenze, Italia</td>
                    </tr>
                    <tr>
                        <td class="info-label">Stato:</td>
                        <td class="info-value">Valido</td>
                    </tr>
                </table>
            </div>

            {{-- CARATTERISTICHE TECNICHE --}}
            <div class="info-section">
                <div class="section-title">Caratteristiche Tecniche</div>

                {{-- TECNICA --}}
                <div class="trait-row">
                    <div class="trait-category-title">Tecnica</div>
                    <div class="trait-values">
                        <div class="trait-item">Jewelry Fabrication</div>
                        <div class="trait-item">Jewelry Repousse</div>
                        <div class="trait-item">Printmaking Engraving</div>
                        <div class="trait-item">Selenio</div>
                    </div>
                </div>

                {{-- MATERIALI --}}
                <div class="trait-row">
                    <div class="trait-category-title">Materiali</div>
                    <div class="trait-values">
                        <div class="trait-item">Material Gem Diamond</div>
                        <div class="trait-item">Material Gem Emerald</div>
                        <div class="trait-item">Material Gem Turquoise</div>
                        <div class="trait-item">Material Gem Ruby</div>
                    </div>
                </div>

                {{-- SUPPORTO --}}
                <div class="trait-row">
                    <div class="trait-category-title">Supporto</div>
                    <div class="trait-values">
                        <div class="trait-item">Support Metal Gold Sheet</div>
                        <div class="trait-item">Support Metal Gold Wire</div>
                        <div class="trait-item">Legno di acacia marcio</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEZIONE VERIFICA --}}
    <div class="verification">
        <div class="verification-title">Certificato Verificato e Autentico</div>
        <div class="verification-text">
            Verificato il: {{ date('d/m/Y H:i') }} | Timestamp: {{ time() }}
        </div>
    </div>

    {{-- HASH --}}
    <div class="hash-section">
        <div class="hash-title">Hash del certificato (SHA-256)</div>
        <div class="hash-value">{{ hash('sha256', $serial) }}</div>
    </div>

    {{-- SEZIONE FIRMA --}}
    <div class="signature">
        <div class="signature-title">Sezione Firme Autorizzate</div>
        <div class="signature-boxes">
            <div class="signature-box">
                <div class="signature-line">Firma Autorizzata</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Data e Timbro</div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Powered by FlorenceEGI | Ultimo aggiornamento: {{ date('d/m/Y H:i') }}
    </div>
</body>

</html>
