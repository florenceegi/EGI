@props(['egi'])

@php
    $extension = strtolower($egi->extension ?? '');
    $mime = strtolower($egi->file_mime ?? '');
    
    // Logica determinazione tipo media
    $isPdf = $extension === 'pdf' || $mime === 'application/pdf';
    
    // Futuri tipi:
    // $isVideo = in_array($extension, ['mp4', 'mov', 'avi']) || str_contains($mime, 'video/');
    // $isAudio = in_array($extension, ['mp3', 'wav', 'ogg']) || str_contains($mime, 'audio/');
@endphp

@if ($isPdf)
    <x-media.pdf-display :egi="$egi" />
@else
    {{-- Default to image display --}}
    <x-media.image-display :egi="$egi" />
@endif

