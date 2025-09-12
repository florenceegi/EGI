<x-platform-layout>

    {{-- Dashboard trasformata in Notification Center - rimossi contenitori con bordi eccessivi --}}
    {{-- Il componente livewire:dashboard gestisce già perfettamente le notifiche --}}
    <x-slot name="platformHeader">
        <livewire:dashboard />
    </x-slot>
    
</x-platform-layout>
