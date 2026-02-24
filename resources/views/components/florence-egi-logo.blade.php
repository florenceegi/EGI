@props(['class' => 'h-8 w-8'])

<svg {{ $attributes->merge(['class' => $class]) }} fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
</svg>
