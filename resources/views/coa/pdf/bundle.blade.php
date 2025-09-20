<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Professional Certificate Bundle - {{ $coa->serial }}</title>
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
            font-size: 28px;
            font-weight: bold;
            color: #d4af37;
            margin-bottom: 10px;
        }

        .bundle-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .pro-badge {
            background-color: #d4af37;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin: 10px;
        }

        .serial {
            font-size: 14px;
            color: #666;
            font-family: 'Courier New', monospace;
        }

        .bundle-info {
            background-color: #f0f8ff;
            border-left: 4px solid #2c5282;
            padding: 15px;
            margin-bottom: 30px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-item {
            display: table-cell;
            padding: 0 15px;
            text-align: center;
        }

        .info-number {
            font-size: 24px;
            font-weight: bold;
            color: #2c5282;
        }

        .info-label {
            font-size: 12px;
            color: #666;
        }

        .artwork-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-left: 4px solid #d4af37;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #d4af37;
            border-bottom: 1px solid #d4af37;
            padding-bottom: 5px;
        }

        .artwork-grid {
            display: table;
            width: 100%;
        }

        .artwork-image {
            display: table-cell;
            width: 220px;
            vertical-align: top;
            padding-right: 20px;
        }

        .artwork-details {
            display: table-cell;
            vertical-align: top;
        }

        .detail-row {
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 140px;
        }

        .traits-section {
            margin-top: 25px;
        }

        .traits-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .trait-item {
            display: table-row;
        }

        .trait-name {
            display: table-cell;
            padding: 8px 12px;
            font-weight: bold;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
        }

        .trait-value {
            display: table-cell;
            padding: 8px 12px;
            border: 1px solid #ddd;
        }

        .annexes-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-left: 4px solid #6c757d;
            page-break-inside: avoid;
        }

        .annex-item {
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }

        .annex-header {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }

        .annex-content {
            font-size: 14px;
            line-height: 1.5;
        }

        .annex-meta {
            font-size: 12px;
            color: #6c757d;
            margin-top: 10px;
            font-style: italic;
        }

        .addendums-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            page-break-inside: avoid;
        }

        .addendum-item {
            margin-bottom: 20px;
            padding: 15px;
            background-color: white;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
        }

        .addendum-header {
            font-size: 16px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }

        .verification-section {
            margin-top: 40px;
            text-align: center;
            padding: 25px;
            border: 2px dashed #d4af37;
            background-color: #fffbf0;
        }

        .qr-code {
            margin: 20px 0;
        }

        .verification-text {
            font-size: 12px;
            color: #666;
            margin-top: 15px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }

        .signature-area {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature-block {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 15px;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 12px;
        }

        .page-break {
            page-break-before: always;
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
        <div class="bundle-title">PROFESSIONAL CERTIFICATE BUNDLE</div>
        <span class="pro-badge">PRO</span>
        <div class="serial">Serial: {{ $coa->serial }}</div>
    </div>

    <!-- Bundle Information -->
    <div class="bundle-info">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-number">{{ $bundle_info['annexes_count'] }}</div>
                <div class="info-label">Professional Annexes</div>
            </div>
            <div class="info-item">
                <div class="info-number">{{ $bundle_info['addendums_count'] }}</div>
                <div class="info-label">Policy Addendums</div>
            </div>
            <div class="info-item">
                <div class="info-number">{{ $bundle_info['generated_at']->format('Y') }}</div>
                <div class="info-label">Bundle Year</div>
            </div>
        </div>
    </div>

    <!-- Artwork Information -->
    <div class="artwork-section">
        <div class="section-title">🎨 Artwork Information</div>
        <div class="artwork-grid">
            @if($image_url)
            <div class="artwork-image">
                <img src="{{ $image_url }}" alt="Artwork" style="max-width: 200px; max-height: 200px; border: 2px solid #d4af37; border-radius: 5px;">
            </div>
            @endif
            <div class="artwork-details">
                <div class="detail-row">
                    <span class="detail-label">Title:</span>
                    <span style="font-size: 16px; font-weight: bold;">{{ $egi->title }}</span>
                </div>
                @if($egi->description)
                <div class="detail-row">
                    <span class="detail-label">Description:</span>
                    <span>{{ is_string($egi->description) ? Str::limit($egi->description, 250) : 'Description not available' }}</span>
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
                <div class="detail-row">
                    <span class="detail-label">Certificate Status:</span>
                    <span style="color: {{ $coa->status === 'active' ? '#059669' : '#dc2626' }}; font-weight: bold;">
                        {{ ucfirst($coa->status) }}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Issue Date:</span>
                    <span>{{ $coa->issued_at->format('F j, Y \a\t g:i A') }}</span>
                </div>
            </div>
        </div>

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
            
            {{-- Additional Metadata for Bundle --}}
            @if(isset($traits_snapshot['metadata']) && count($traits_snapshot['metadata']) > 0)
            <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
                <h4 style="font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #666;">📋 Additional Information</h4>
                <div class="traits-grid">
                    @foreach($traits_snapshot['metadata'] as $metadata)
                    <div class="trait-item">
                        <div class="trait-name">{{ $metadata['label'] ?? 'Info' }}</div>
                        <div class="trait-value">
                            @if($metadata['type'] === 'description' && strlen($metadata['value']) > 200)
                                {{ Str::limit($metadata['value'], 200) }}
                            @else
                                {{ $metadata['value'] ?? 'N/A' }}
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Professional Annexes -->
    @if($bundle_info['includes_annexes'] && count($annexes) > 0)
    <div class="page-break"></div>
    <div class="annexes-section">
        <div class="section-title">📋 Professional Annexes</div>
        <p style="margin-bottom: 20px; font-style: italic; color: #6c757d;">
            This section contains professional documentation and detailed provenance information for enhanced certificate authenticity.
        </p>

        @foreach($annexes as $annex)
        <div class="annex-item">
            <div class="annex-header">
                {{ $annex['type_label'] }}
            </div>
            <div class="annex-content">
                @if($annex['type'] === 'A_PROVENANCE')
                    <strong>Previous Owners:</strong> {{ $annex['data']['previous_owners'] ?? 'Not specified' }}<br>
                    <strong>Acquisition Date:</strong> {{ $annex['data']['acquisition_date'] ?? 'Not specified' }}<br>
                    <strong>Source:</strong> {{ $annex['data']['source'] ?? 'Not specified' }}<br>
                    @if(isset($annex['data']['notes']))
                    <strong>Notes:</strong> {{ $annex['data']['notes'] }}
                    @endif
                @elseif($annex['type'] === 'B_CONDITION')
                    <strong>Overall Condition:</strong> {{ $annex['data']['overall_condition'] ?? 'Not specified' }}<br>
                    <strong>Condition Report:</strong> {{ $annex['data']['condition_report'] ?? 'Not specified' }}<br>
                    <strong>Conservation History:</strong> {{ $annex['data']['conservation_history'] ?? 'Not specified' }}<br>
                    <strong>Assessment Date:</strong> {{ $annex['data']['assessment_date'] ?? 'Not specified' }}
                @elseif($annex['type'] === 'C_EXHIBITIONS')
                    <strong>Exhibition History:</strong><br>
                    @if(isset($annex['data']['exhibitions']) && is_array($annex['data']['exhibitions']))
                        @foreach($annex['data']['exhibitions'] as $exhibition)
                        • {{ $exhibition['title'] ?? 'Unknown' }} - {{ $exhibition['location'] ?? 'Unknown' }} ({{ $exhibition['year'] ?? 'Unknown' }})<br>
                        @endforeach
                    @else
                        {{ $annex['data']['exhibitions'] ?? 'No exhibitions recorded' }}
                    @endif
                @elseif($annex['type'] === 'D_PHOTOS')
                    <strong>Additional Documentation:</strong><br>
                    {{ $annex['data']['photo_count'] ?? 0 }} high-resolution images included<br>
                    <strong>Photography Date:</strong> {{ $annex['data']['photo_date'] ?? 'Not specified' }}<br>
                    <strong>Photographer:</strong> {{ $annex['data']['photographer'] ?? 'Not specified' }}
                @endif
            </div>
            <div class="annex-meta">
                Created: {{ $annex['created_at']->format('F j, Y') }} |
                Last Updated: {{ $annex['updated_at']->format('F j, Y') }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Policy Addendums -->
    @if($bundle_info['includes_addendums'] && count($addendums) > 0)
    <div class="page-break"></div>
    <div class="addendums-section">
        <div class="section-title">📜 Policy Addendums</div>
        <p style="margin-bottom: 20px; font-style: italic; color: #856404;">
            This section contains policy updates and governance documentation that apply to this certificate.
        </p>

        @foreach($addendums as $addendum)
        <div class="addendum-item">
            <div class="addendum-header">
                {{ $addendum['title'] }} (v{{ $addendum['version'] }})
            </div>
            <div class="annex-content">
                <strong>Policy Type:</strong> {{ ucfirst($addendum['policy_type']) }}<br>
                <strong>Published:</strong> {{ $addendum['published_at']->format('F j, Y') }}<br><br>
                <strong>Content:</strong><br>
                {{ $addendum['content'] }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Verification Section -->
    <div class="page-break"></div>
    <div class="verification-section">
        <div class="section-title">🔐 Bundle Verification</div>
        @if(isset($qr_code))
        <div class="qr-code">
            {!! $qr_code !!}
        </div>
        @endif
        <div style="font-weight: bold; margin-bottom: 15px;">
            Certificate Hash: <span style="font-family: 'Courier New', monospace; font-size: 11px;">{{ $coa->verification_hash }}</span>
        </div>
        <div style="font-weight: bold; margin-bottom: 15px;">
            Bundle Generated: {{ $bundle_info['generated_at']->format('F j, Y \a\t g:i A \U\T\C') }}
        </div>
        <div class="verification-text">
            To verify this professional bundle, visit {{ $platform_info['url'] }}/verify/{{ $coa->serial }}<br>
            or scan the QR code above with your mobile device.<br><br>
            This bundle includes {{ $bundle_info['annexes_count'] }} professional annexes and {{ $bundle_info['addendums_count'] }} policy addendums.
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
                Bundle Generated<br>
                {{ $bundle_info['generated_at']->format('F j, Y') }}
            </div>
        </div>
        <div class="signature-block">
            <div class="signature-line">
                Professional Grade<br>
                PRO Certificate
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Professional Certificate Bundle</strong> - This enhanced certificate includes comprehensive documentation and provenance information.</p>
        <p>Generated electronically by {{ $platform_info['name'] }} Professional Services - Valid without signature.</p>
        <p>Bundle includes: Core Certificate + {{ $bundle_info['annexes_count'] }} Annexes + {{ $bundle_info['addendums_count'] }} Addendums</p>
        <p>For questions about this certificate bundle, please contact support at {{ $platform_info['url'] }}</p>
        <p>Bundle generated on {{ $bundle_info['generated_at']->format('Y-m-d H:i:s \U\T\C') }}</p>
    </div>
</body>
</html>
