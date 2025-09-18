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

    @if($traits_snapshot && count($traits_snapshot) > 0)
    <h3>Traits:</h3>
    <ul>
        @foreach($traits_snapshot as $trait)
        <li>{{ $trait['value'] ?? 'Unknown' }}: {{ $trait['display_value'] ?? 'N/A' }}</li>
        @endforeach
    </ul>
    @endif

    <p>Platform: {{ $platform_info['name'] }}</p>
</body>
</html>
