<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test CoA PDF - {{ $coa->serial }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 20px;
        }
    </style>
</head>
<body>
    <h1>Certificate of Authenticity</h1>
    <p>Serial: {{ $coa->serial }}</p>
    <p>EGI Title: {{ $egi->title }}</p>
    <p>Owner: {{ $owner->name }}</p>
    <p>Creator: {{ $creator->name }}</p>
    <p>Status: {{ $coa->status }}</p>
    <p>Issue Date: {{ $coa->issued_at->format('Y-m-d') }}</p>

    @if($traits_snapshot && (isset($traits_snapshot['coa_traits']) || count($traits_snapshot) > 0))
    <h3>Traits:</h3>
    <ul>
        @if(isset($traits_snapshot['coa_traits']))
            {{-- New CoA Traits Structure --}}
            @foreach(['technique', 'materials', 'support'] as $category)
                @if(isset($traits_snapshot['coa_traits'][$category]))
                    @php $categoryData = $traits_snapshot['coa_traits'][$category]; @endphp
                    
                    {{-- Vocabulary Terms --}}
                    @if(!empty($categoryData['vocabulary_terms']))
                        @foreach($categoryData['vocabulary_terms'] as $term)
                        <li>{{ ucfirst($category) }}: {{ $term['translated_name'] }}</li>
                        @endforeach
                    @endif
                    
                    {{-- Custom Terms --}}
                    @if(!empty($categoryData['custom_terms']))
                        @foreach($categoryData['custom_terms'] as $term)
                        <li>{{ ucfirst($category) }} (Custom): {{ $term['text'] }}</li>
                        @endforeach
                    @endif
                @endif
            @endforeach
        @else
            {{-- Backward Compatibility: Generic Traits --}}
            @foreach($traits_snapshot as $trait)
                @if(is_array($trait) && isset($trait['value']))
                <li>{{ $trait['value'] ?? 'Unknown' }}: {{ $trait['display_value'] ?? 'N/A' }}</li>
                @endif
            @endforeach
        @endif
    </ul>
    @endif

    <p>Platform: {{ $platform_info['name'] }}</p>
</body>
</html>
