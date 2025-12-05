{{-- Product Card Component for Company Catalog --}}
{{-- Corporate Palette: Blu #1E3A5F, Oro #C9A227, Verde #2D7D46 --}}

@props(['product', 'company', 'viewMode' => 'grid'])

<article
    class="product-card group relative overflow-hidden rounded-xl border border-gray-800 bg-gray-900/70 transition-all duration-300 hover:border-[#C9A227]/60 hover:shadow-2xl hover:shadow-[#C9A227]/10"
    data-view-mode="{{ $viewMode }}">

    {{-- Product Image --}}
    <a href="{{ route('home.egis.show', $product->id) }}" class="block">
        <div class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-800 to-gray-900">
            @if ($product->thumbnail_url ?? ($product->main_image_url ?? null))
                <img src="{{ $product->thumbnail_url ?? $product->main_image_url }}" alt="{{ $product->name }}"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                    loading="lazy">
            @else
                {{-- Placeholder --}}
                <div class="flex h-full items-center justify-center bg-gradient-to-br from-[#1E3A5F]/20 to-[#0F1F33]/40">
                    <svg class="h-20 w-20 text-[#C9A227]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            @endif

            {{-- Gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent opacity-60">
            </div>

            {{-- Badges --}}
            <div class="absolute left-3 top-3 flex flex-col gap-2">
                @if ($product->is_featured ?? false)
                    <span
                        class="rounded-full bg-[#C9A227] px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-gray-900 shadow-lg">
                        {{ __('company.catalog.featured') }}
                    </span>
                @endif
                @if ($product->is_new ?? $product->created_at && $product->created_at->isAfter(now()->subDays(14)))
                    <span
                        class="rounded-full bg-[#2D7D46] px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-white shadow-lg">
                        {{ __('company.catalog.new') }}
                    </span>
                @endif
                @if (($product->discount_percentage ?? 0) > 0)
                    <span
                        class="rounded-full bg-red-600 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-white shadow-lg">
                        -{{ $product->discount_percentage }}%
                    </span>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div
                class="absolute right-3 top-3 flex flex-col gap-2 opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                {{-- Wishlist Button --}}
                <button type="button"
                    class="rounded-full bg-gray-900/80 p-2 text-gray-300 shadow-lg backdrop-blur-sm transition-colors hover:bg-[#C9A227] hover:text-gray-900"
                    title="{{ __('company.catalog.add_to_wishlist') }}"
                    aria-label="{{ __('company.catalog.add_to_wishlist') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                {{-- Quick View Button --}}
                <button type="button"
                    class="rounded-full bg-gray-900/80 p-2 text-gray-300 shadow-lg backdrop-blur-sm transition-colors hover:bg-[#C9A227] hover:text-gray-900"
                    title="{{ __('company.catalog.quick_view') }}" aria-label="{{ __('company.catalog.quick_view') }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            {{-- Blockchain Badge --}}
            @if ($product->is_tokenized ?? ($product->blockchain_hash ?? false))
                <div class="absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full bg-[#1E3A5F]/90 px-2.5 py-1 backdrop-blur-sm"
                    title="{{ __('company.catalog.blockchain_certified') }}">
                    <svg class="h-4 w-4 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="text-xs font-medium text-white">{{ __('company.catalog.certified') }}</span>
                </div>
            @endif
        </div>
    </a>

    {{-- Product Info --}}
    <div class="p-4">
        {{-- Category --}}
        @if ($product->category ?? ($product->categoryRelation ?? null))
            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-[#C9A227]">
                {{ is_object($product->category ?? $product->categoryRelation) ? $product->category->name ?? $product->categoryRelation->name : $product->category }}
            </p>
        @endif

        {{-- Product Name --}}
        <h3 class="mb-2 line-clamp-2 font-semibold text-white transition-colors group-hover:text-[#C9A227]">
            <a href="{{ route('home.egis.show', $product->id) }}">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Short Description --}}
        @if ($product->short_description ?? ($product->description ?? null))
            <p class="mb-3 line-clamp-2 text-sm text-gray-400">
                {{ Str::limit($product->short_description ?? $product->description, 80) }}
            </p>
        @endif

        {{-- Rating --}}
        @if (($product->reviews_count ?? 0) > 0)
            <div class="mb-3 flex items-center gap-2">
                <div class="flex items-center">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="{{ $i <= ($product->average_rating ?? 0) ? 'text-[#C9A227]' : 'text-gray-600' }} h-4 w-4"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                </div>
                <span class="text-xs text-gray-400">({{ $product->reviews_count }})</span>
            </div>
        @endif

        {{-- Price & Add to Cart --}}
        <div class="flex items-end justify-between gap-2">
            <div class="flex flex-col">
                @if (($product->discount_percentage ?? 0) > 0 && isset($product->original_price))
                    <span class="text-sm text-gray-500 line-through">
                        €{{ number_format($product->original_price, 2, ',', '.') }}
                    </span>
                @endif
                <span class="text-xl font-bold text-white">
                    €{{ number_format($product->price ?? ($product->egi_price ?? 0), 2, ',', '.') }}
                </span>
            </div>

            @if ($product->is_available ?? true)
                <button type="button"
                    class="flex items-center gap-1.5 rounded-lg bg-[#C9A227] px-4 py-2 text-sm font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:bg-[#D4B445] hover:shadow-xl"
                    title="{{ __('company.catalog.add_to_cart') }}">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('company.catalog.add') }}</span>
                </button>
            @else
                <span class="rounded-lg bg-gray-700/50 px-4 py-2 text-sm font-medium text-gray-400">
                    {{ __('company.catalog.out_of_stock') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Stock Indicator --}}
    @if (isset($product->stock_quantity) && $product->stock_quantity > 0 && $product->stock_quantity <= 5)
        <div
            class="absolute bottom-0 left-0 right-0 bg-gradient-to-r from-red-600/90 to-red-600/70 px-4 py-1.5 text-center">
            <span class="text-xs font-medium text-white">
                {{ __('company.catalog.only_x_left', ['count' => $product->stock_quantity]) }}
            </span>
        </div>
    @endif
</article>
