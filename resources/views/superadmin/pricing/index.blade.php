<x-layouts.superadmin pageTitle="Gestione Prezzi Features">
    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-base-content">💰 Gestione Prezzi Features</h1>
            <p class="mt-2 text-lg text-base-content/70">Configura i prezzi dinamici delle funzionalità AI e Premium</p>
        </div>
        <a href="{{ route('superadmin.pricing.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            Aggiungi Feature
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Totale Features</div>
            <div class="stat-value text-primary">{{ $pricing->count() }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Features Attive</div>
            <div class="stat-value text-success">{{ $pricing->where('is_active', true)->count() }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Pro Tier</div>
            <div class="stat-value text-warning">{{ $pricing->where('min_tier_required', 'pro')->count() }}</div>
        </div>
        <div class="stat rounded-lg bg-base-100 shadow">
            <div class="stat-title">Enterprise Tier</div>
            <div class="stat-value text-info">{{ $pricing->where('min_tier_required', 'enterprise')->count() }}</div>
        </div>
    </div>

    {{-- Pricing Table --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Tier</th>
                            <th class="text-right">Costo Egili</th>
                            <th class="text-right">Costo FIAT (EUR)</th>
                            <th class="text-center">Stato</th>
                            <th class="text-center">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pricing as $item)
                            <tr>
                                <td class="font-semibold">{{ $item->feature_name }}</td>
                                <td>
                                    <span
                                        class="@if ($item->min_tier_required === 'free') badge-neutral
                                        @elseif($item->min_tier_required === 'starter') badge-info
                                        @elseif($item->min_tier_required === 'pro') badge-warning
                                        @elseif($item->min_tier_required === 'business') badge-secondary
                                        @elseif($item->min_tier_required === 'enterprise') badge-error @endif badge">
                                        {{ ucfirst($item->min_tier_required) }}
                                    </span>
                                </td>
                                <td class="text-right font-mono">
                                    @if ($item->cost_egili)
                                        {{ number_format($item->cost_egili, 0) }} Ƹ
                                    @else
                                        <span class="text-base-content/50">-</span>
                                    @endif
                                </td>
                                <td class="text-right font-mono">
                                    @if ($item->cost_fiat_eur)
                                        € {{ number_format($item->cost_fiat_eur, 2) }}
                                    @else
                                        <span class="text-base-content/50">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->is_active)
                                        <span class="badge badge-success">Attivo</span>
                                    @else
                                        <span class="badge badge-ghost">Disattivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="join">
                                        <a href="{{ route('superadmin.pricing.edit', $item) }}"
                                            class="btn btn-ghost join-item btn-sm">
                                            ✏️
                                        </a>
                                        <form action="{{ route('superadmin.pricing.destroy', $item) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost join-item btn-sm text-error"
                                                onclick="return confirm('Sei sicuro di voler eliminare questo pricing?')">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-base-content/50">
                                    Nessun pricing configurato. Clicca "Aggiungi Feature" per iniziare.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.superadmin>
