<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Authenticity - {{ $coa->serial }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #d4af37;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #d4af37;
            margin-bottom: 10px;
        }

        .certificate-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .serial {
            font-size: 14px;
            color: #666;
            font-family: 'Courier New', monospace;
        }

        .artwork-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-left: 4px solid #d4af37;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .traits-warning {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }

        .traits-warning .title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 8px;
        }

        .traits-warning .message {
            color: #856404;
            font-size: 12px;
            line-height: 1.4;
        };
            margin-bottom: 15px;
            color: #d4af37;
        }

        .artwork-grid {
            display: table;
            width: 100%;
        }

        .artwork-image {
            display: table-cell;
            width: 200px;
            vertical-align: top;
            padding-right: 20px;
        }

        .artwork-details {
            display: table-cell;
            vertical-align: top;
        }

        .detail-row {
            margin-bottom: 8px;
        }

        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .traits-section {
            margin-top: 20px;
        }

        .traits-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .trait-item {
            display: table-row;
        }

        .trait-name {
            display: table-cell;
            padding: 5px 10px;
            font-weight: bold;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }

        .trait-value {
            display: table-cell;
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        .provenance-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f5f5f5;
            border-left: 4px solid #2c5282;
        }

        .verification-section {
            margin-top: 40px;
            text-align: center;
            padding: 20px;
            border: 2px dashed #d4af37;
        }

        .qr-code {
            margin: 15px 0;
        }

        .verification-text {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .signature-area {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 20px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 12px;
        }

        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">{{ $platform_info['name'] }}</div>
        <div class="certificate-title">CERTIFICATE OF AUTHENTICITY</div>
        <div class="serial">Serial: {{ $coa->serial }}</div>
    </div>

    <!-- Artwork Information -->
    <div class="artwork-section">
        <div class="section-title">Artwork Information</div>
        <div class="artwork-grid">
            @if($image_url)
            <div class="artwork-image">
                <img src="{{ $image_url }}" alt="Artwork" style="max-width: 180px; max-height: 180px; border: 1px solid #ddd;">
            </div>
            @endif
            <div class="artwork-details">
                <div class="detail-row">
                    <span class="detail-label">Title:</span>
                    <span>{{ $egi->title }}</span>
                </div>
                @if($egi->description)
                <div class="detail-row">
                    <span class="detail-label">Description:</span>
                    <span>{{ is_string($egi->description) ? Str::limit($egi->description, 200) : 'Description not available' }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Creation Date:</span>
                    <span>{{ $egi->creation_date ? $egi->creation_date->format('F j, Y') : 'Not specified' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Current Owner:</span>
                    <span>{{ $owner->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Creator:</span>
                    <span>{{ $creator->name }}</span>
                </div>
            </div>
        </div>

        <!-- CoA Traits Completeness Warning -->
        @if(isset($traits_snapshot['traits_incomplete']) && $traits_snapshot['traits_incomplete'])
        <div class="traits-warning">
            <div class="title">⚠️ Certificato con Traits Generici</div>
            <div class="message">
                Questo certificato è stato generato utilizzando i traits generici EGI invece dei traits CoA specifici. 
                Per una certificazione più professionale e dettagliata, si consiglia di configurare i CoA traits 
                (tecnica, materiali, supporto) dal pannello di gestione dell'opera.
            </div>
        </div>
        @endif

        <!-- Traits Information -->
        @if($traits_snapshot && (isset($traits_snapshot['coa_traits']) || isset($traits_snapshot['data']) || count($traits_snapshot) > 0))
        <div class="traits-section">
            <div class="section-title">🏷️ Artwork Traits</div>
            <div class="traits-grid">
                @if(isset($traits_snapshot['coa_traits']))
                    {{-- Old CoA Traits Structure (deprecated) --}}
                    @foreach(['technique', 'materials', 'support'] as $category)
                        @if(isset($traits_snapshot['coa_traits'][$category]))
                            @php $categoryData = $traits_snapshot['coa_traits'][$category]; @endphp
                            
                            {{-- Vocabulary Terms --}}
                            @if(!empty($categoryData['vocabulary_terms']))
                                @foreach($categoryData['vocabulary_terms'] as $term)
                                <div class="trait-item">
                                    <div class="trait-name">{{ ucfirst($category) }}</div>
                                    <div class="trait-value">{{ $term['translated_name'] }}</div>
                                </div>
                                @endforeach
                            @endif
                            
                            {{-- Custom Terms --}}
                            @if(!empty($categoryData['custom_terms']))
                                @foreach($categoryData['custom_terms'] as $term)
                                <div class="trait-item">
                                    <div class="trait-name">{{ ucfirst($category) }} (Custom)</div>
                                    <div class="trait-value">{{ $term['text'] }}</div>
                                </div>
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @elseif(isset($traits_snapshot['data']))
                    {{-- New Enhanced Traits Structure --}}
                    @foreach($traits_snapshot['data'] as $trait)
                    <div class="trait-item">
                        <div class="trait-name">{{ $trait['trait_type'] ?? 'Trait' }}</div>
                        <div class="trait-value">{{ $trait['value'] ?? 'N/A' }}</div>
                    </div>
                    @endforeach
                @else
                    {{-- Backward Compatibility: Generic Traits --}}
                    @foreach($traits_snapshot as $trait)
                        @if(is_array($trait) && isset($trait['value']))
                        <div class="trait-item">
                            <div class="trait-name">{{ $trait['value'] ?? 'Trait' }}</div>
                            <div class="trait-value">{{ $trait['display_value'] ?? $trait['value'] ?? 'N/A' }}</div>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
        
        {{-- Additional Metadata Section --}}
        @if(isset($traits_snapshot['metadata']) && count($traits_snapshot['metadata']) > 0)
        <div class="traits-section" style="margin-top: 20px;">
            <div class="section-title">📋 Additional Information</div>
            <div class="traits-grid">
                @foreach($traits_snapshot['metadata'] as $metadata)
                <div class="trait-item">
                    <div class="trait-name">{{ $metadata['label'] ?? 'Info' }}</div>
                    <div class="trait-value">
                        @if($metadata['type'] === 'description' && strlen($metadata['value']) > 150)
                            {{ Str::limit($metadata['value'], 150) }}
                        @else
                            {{ $metadata['value'] ?? 'N/A' }}
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>

    <!-- Provenance Information -->
    <div class="provenance-section">
        <div class="section-title">Certificate Information</div>
        <div class="detail-row">
            <span class="detail-label">Issue Date:</span>
            <span>{{ $coa->issued_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Certificate ID:</span>
            <span style="font-family: 'Courier New', monospace;">{{ $coa->serial }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status:</span>
            <span style="color: {{ $coa->status === 'active' ? '#059669' : '#dc2626' }}; font-weight: bold;">
                {{ ucfirst($coa->status) }}
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Platform:</span>
            <span>{{ $platform_info['name'] }} - {{ $platform_info['url'] }}</span>
        </div>
    </div>

    <!-- Verification Section -->
    <div class="verification-section">
        <div class="section-title">Verification</div>
        @if(isset($qr_code))
        <div class="qr-code">
            {!! $qr_code !!}
        </div>
        @endif
        <div style="font-weight: bold; margin-bottom: 10px;">
            Verification Hash: <span style="font-family: 'Courier New', monospace; font-size: 11px;">{{ $coa->verification_hash }}</span>
        </div>
        <div class="verification-text">
            To verify this certificate, visit {{ $platform_info['url'] }}/verify/{{ $coa->serial }}<br>
            or scan the QR code above with your mobile device.
        </div>
    </div>

    <!-- Signature Area -->
    <div class="signature-area">
        <div class="signature-block">
            <div class="signature-line">
                Platform Authority<br>
                {{ $platform_info['name'] }}
            </div>
        </div>
        <div class="signature-block">
            <div class="signature-line">
                Generated on<br>
                {{ $platform_info['issued_at']->format('F j, Y') }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This certificate was generated electronically by {{ $platform_info['name'] }} and is valid without signature.</p>
        <p>For questions about this certificate, please contact support at {{ $platform_info['url'] }}</p>
        <p>Certificate generated on {{ $platform_info['issued_at']->format('Y-m-d H:i:s \U\T\C') }}</p>
    </div>
</body>
</html>
