{{-- Catalog Content Grid - Company Products --}}
{{-- Corporate Palette: Blu #1E3A5F, Oro #C9A227, Verde #2D7D46 --}}

@props(['products', 'company'])

@if (isset($products) && $products->count() > 0)
    <div id="products-grid" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @foreach ($products as $product)
            @include('company.partials.product-card', [
                'product' => $product,
                'company' => $company,
            ])
        @endforeach
    </div>

    {{-- Results Summary --}}
    <div class="mt-6 text-center text-sm text-gray-400">
        {{ __('company.catalog.showing_results', [
            'from' => $products->firstItem() ?? 0,
            'to' => $products->lastItem() ?? 0,
            'total' => $products->total() ?? $products->count(),
        ]) }}
    </div>
@else
    {{-- Empty State --}}
    <div
        class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-700 bg-gray-900/60 px-6 py-16 text-center">
        <div class="mb-6 rounded-full bg-[#1E3A5F]/30 p-6">
            <svg class="h-16 w-16 text-[#C9A227]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </div>
        <h3 class="mb-2 text-2xl font-bold text-white">
            {{ __('company.catalog.no_products_title') }}
        </h3>
        <p class="mb-6 max-w-md text-gray-400">
            @if (request('search') || request('category'))
                {{ __('company.catalog.no_products_filtered') }}
            @else
                {{ __('company.catalog.no_products_description') }}
            @endif
        </p>
        @if (request('search') || request('category'))
            <a href="{{ route('company.catalog', $company->id) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-[#C9A227] px-6 py-3 font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:bg-[#D4B445] hover:shadow-xl">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ __('company.catalog.clear_filters') }}
            </a>
        @endif
    </div>
@endif
