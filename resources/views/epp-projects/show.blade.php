{{-- resources/views/epp-projects/show.blade.php --}}
<x-guest-layout :title="__('epp_projects.show.page_title', ['name' => $eppProject->name])" :metaDescription="Str::limit($eppProject->description, 155)">

    {{-- Slot personalizzato per disabilitare la hero section --}}
    <x-slot name="noHero">true</x-slot>

    {{-- Styles --}}
    <x-slot name="headExtra">
        <style>
            /* EPP Project Show - Brand Guidelines Compliant */
            .project-hero {
                background: linear-gradient(135deg, #2D5016 0%, #1B365D 100%);
                color: white;
                padding: 3rem 0;
            }

            .project-header-card {
                background: white;
                border-radius: 1rem;
                padding: 2rem;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                margin-top: -4rem;
            }

            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1rem;
                border-radius: 2rem;
                font-size: 0.875rem;
                font-weight: 600;
            }

            .status-in_progress {
                background: rgba(76, 175, 80, 0.1);
                color: #2D5016;
            }

            .status-planned {
                background: rgba(212, 165, 116, 0.1);
                color: #D4A574;
            }

            .status-completed {
                background: rgba(27, 54, 93, 0.1);
                color: #1B365D;
            }

            .section-card {
                background: white;
                border-radius: 1rem;
                padding: 2rem;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                border-left: 4px solid #2D5016;
            }

            .metric-card {
                background: linear-gradient(135deg, rgba(45, 80, 22, 0.05) 0%, rgba(76, 175, 80, 0.05) 100%);
                border-radius: 1rem;
                padding: 1.5rem;
                border: 2px solid rgba(45, 80, 22, 0.1);
            }

            .progress-circle {
                width: 120px;
                height: 120px;
                border-radius: 50%;
                background: conic-gradient(#2D5016 0deg, #4CAF50 var(--progress), #E5E7EB var(--progress));
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .progress-circle::before {
                content: '';
                position: absolute;
                width: 100px;
                height: 100px;
                border-radius: 50%;
                background: white;
            }

            .progress-value {
                position: relative;
                z-index: 1;
                font-size: 1.5rem;
                font-weight: 700;
                color: #2D5016;
            }

            .collection-card {
                background: white;
                border-radius: 1rem;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                border-top: 3px solid #2D5016;
            }

            .collection-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 16px rgba(45, 80, 22, 0.2);
            }

            .egi-card {
                background: white;
                border-radius: 0.75rem;
                overflow: hidden;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .egi-card:hover {
                transform: scale(1.05);
                box-shadow: 0 6px 12px rgba(45, 80, 22, 0.2);
            }

            .blockchain-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                background: rgba(27, 54, 93, 0.1);
                color: #1B365D;
                font-size: 0.875rem;
                font-weight: 600;
            }

            .btn-primary-epp {
                background: linear-gradient(135deg, #2D5016 0%, #4CAF50 100%);
                color: white;
                padding: 0.75rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                display: inline-block;
            }

            .btn-primary-epp:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(45, 80, 22, 0.3);
            }

            .btn-secondary-epp {
                background: white;
                color: #2D5016;
                padding: 0.75rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                transition: all 0.3s ease;
                border: 2px solid #2D5016;
                cursor: pointer;
                display: inline-block;
            }

            .btn-secondary-epp:hover {
                background: rgba(45, 80, 22, 0.05);
            }

            .impact-icon {
                width: 4rem;
                height: 4rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                background: rgba(45, 80, 22, 0.1);
                color: #2D5016;
            }
        </style>
    </x-slot>

    {{-- Main Content --}}
    <x-slot name="slot">
        <!-- Back Button -->
        <div class="container px-4 py-4 mx-auto">
            <a href="{{ route('epp-projects.index') }}"
                class="inline-flex items-center text-gray-600 transition-colors hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                {{ __('epp_projects.show.back_to_projects') }}
            </a>
        </div>

        <!-- Hero Section -->
        @php
            $projectImageUrl = '';
            if (method_exists($eppProject, 'getFirstMediaUrl')) {
                $projectImageUrl = $eppProject->getFirstMediaUrl('project_images', 'card');
            }
        @endphp
        
        <section class="relative overflow-hidden" style="min-height: 400px;">
            {{-- Background Image --}}
            @if($projectImageUrl)
                <div class="absolute inset-0">
                    <img src="{{ $projectImageUrl }}" 
                         alt="{{ $eppProject->name }}"
                         class="object-cover w-full h-full">
                    <div class="absolute inset-0 bg-gradient-to-r from-[#2D5016]/95 via-[#2D5016]/85 to-[#1B365D]/90"></div>
                </div>
            @else
                {{-- Fallback Gradient --}}
                <div class="absolute inset-0" style="background: linear-gradient(135deg, #2D5016 0%, #1B365D 100%);"></div>
            @endif
            
            {{-- Content --}}
            <div class="container relative z-10 px-4 py-16 mx-auto">
                <div class="flex items-center mb-4">
                    @if ($eppProject->project_type === 'ARF')
                        <div class="flex items-center justify-center w-20 h-20 mr-6 text-5xl bg-white/20 backdrop-blur-sm rounded-2xl">🌳</div>
                    @elseif($eppProject->project_type === 'APR')
                        <div class="flex items-center justify-center w-20 h-20 mr-6 text-5xl bg-white/20 backdrop-blur-sm rounded-2xl">🌊</div>
                    @elseif($eppProject->project_type === 'BPE')
                        <div class="flex items-center justify-center w-20 h-20 mr-6 text-5xl bg-white/20 backdrop-blur-sm rounded-2xl">🐝</div>
                    @endif
                    <div>
                        <div class="inline-block px-4 py-2 mb-3 text-sm font-semibold text-white uppercase bg-white/20 backdrop-blur-sm rounded-full">
                            {{ __('epp_projects.types.' . strtolower($eppProject->project_type)) }}
                        </div>
                        <h1 class="text-4xl font-bold text-white md:text-6xl drop-shadow-lg" style="font-family: 'Playfair Display', serif;">
                            {{ $eppProject->name }}
                        </h1>
                        <p class="mt-4 text-xl text-white/90 max-w-3xl drop-shadow">
                            {{ $eppProject->description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Project Header Card -->
        <section class="container px-4 mx-auto mb-12">
            <div class="project-header-card">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- EPP Organization -->
                    <div>
                        <div class="mb-1 text-sm text-gray-600">{{ __('epp_projects.show.managed_by') }}</div>
                        <div class="text-lg font-bold" style="color: #2D5016;">
                            {{ $eppProject->eppUser->organizationData->organization_name ?? $eppProject->eppUser->name }}
                        </div>
                        @if ($eppProject->eppUser->organizationData)
                            <div class="mt-1 text-sm text-gray-600">
                                {{ $eppProject->eppUser->organizationData->fiscal_code }}
                            </div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div>
                        <div class="mb-2 text-sm text-gray-600">{{ __('epp_projects.show.status') }}</div>
                        <span class="status-badge status-{{ $eppProject->status }}">
                            ⬤ {{ __('epp_projects.show.status_' . $eppProject->status) }}
                        </span>
                    </div>

                    <!-- Dates -->
                    <div>
                        <div class="mb-1 text-sm text-gray-600">{{ __('epp_projects.show.started') }}</div>
                        <div class="font-semibold">{{ $eppProject->created_at->format('d/m/Y') }}</div>
                        @if ($eppProject->target_date)
                            <div class="mt-2 text-sm text-gray-600">{{ __('epp_projects.show.target_date') }}</div>
                            <div class="font-semibold">{{ $eppProject->target_date->format('d/m/Y') }}</div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="pt-6 mt-6 border-t">
                    <p class="leading-relaxed text-gray-700">{{ $eppProject->description }}</p>
                </div>
            </div>
        </section>

        <!-- Environmental Impact & Equilibrium -->
        <section class="container px-4 mx-auto mb-12">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Environmental Impact -->
                <div class="section-card">
                    <h2 class="mb-6 text-2xl font-bold" style="font-family: 'Playfair Display', serif; color: #2D5016;">
                        {{ __('epp_projects.show.impact_title') }}
                    </h2>

                    <div class="flex items-center justify-between mb-6">
                        <div class="progress-circle"
                            style="--progress: {{ $eppProject->completion_percentage * 3.6 }}deg;">
                            <span class="progress-value">{{ round($eppProject->completion_percentage) }}%</span>
                        </div>
                        <div class="flex-1 ml-8">
                            <div class="mb-4">
                                <div class="text-sm text-gray-600">{{ __('epp_projects.show.target') }}</div>
                                <div class="text-2xl font-bold" style="color: #2D5016;">
                                    {{ number_format($eppProject->target_value, 0, ',', '.') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">{{ __('epp_projects.show.achieved') }}</div>
                                <div class="text-2xl font-bold" style="color: #4CAF50;">
                                    {{ number_format($eppProject->current_value, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Impact Metrics -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        @foreach ($impactMetrics as $key => $value)
                            <div class="text-center metric-card">
                                <div class="text-2xl font-bold" style="color: #2D5016;">
                                    {{ is_float($value) ? number_format($value, 2, ',', '.') : number_format($value, 0, ',', '.') }}
                                </div>
                                <div class="mt-1 text-xs text-gray-600">
                                    {{ __('epp_projects.dashboard.' . $key) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Equilibrium -->
                <div class="section-card">
                    <h2 class="mb-6 text-2xl font-bold" style="font-family: 'Playfair Display', serif; color: #2D5016;">
                        💎 {{ __('epp_projects.show.equilibrium_title') }}
                    </h2>

                    <div class="mb-4 metric-card">
                        <div class="mb-1 text-sm text-gray-600">{{ __('epp_projects.show.equilibrium_total') }}</div>
                        <div class="text-4xl font-bold" style="color: #2D5016; font-family: 'Playfair Display', serif;">
                            €{{ number_format($equilibriumStats['total'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="metric-card">
                            <div class="mb-1 text-sm text-gray-600">
                                {{ __('epp_projects.show.equilibrium_this_month') }}</div>
                            <div class="text-2xl font-bold" style="color: #4CAF50;">
                                €{{ number_format($equilibriumStats['this_month'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="mb-1 text-sm text-gray-600">{{ __('epp_projects.show.equilibrium_growth') }}
                            </div>
                            <div class="text-2xl font-bold" style="color: #D4A574;">
                                +{{ number_format($equilibriumStats['growth_percentage'], 1) }}%
                            </div>
                        </div>
                    </div>

                    <!-- Blockchain Verification -->
                    <div class="justify-center w-full blockchain-badge">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('epp_projects.show.blockchain_verify') }}
                    </div>
                </div>
            </div>
        </section>

        <!-- Collections Supporting this Project -->
        <section class="container px-4 mx-auto mb-12">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold" style="font-family: 'Playfair Display', serif; color: #2D5016;">
                    📚 {{ __('epp_projects.show.collections_title') }}
                </h2>
                <p class="mt-2 text-gray-600">
                    {{ __('epp_projects.show.collections_subtitle', ['count' => $collections->total()]) }}
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse($collections as $collection)
                    <div
                        class="relative flex flex-col h-full overflow-hidden transition-all bg-white border border-gray-100 shadow-sm group rounded-xl hover:border-green-200 hover:shadow-md">
                        <div class="relative h-48 overflow-hidden bg-gray-100">
                            @if ($collection->getFirstMediaUrl('head', 'card'))
                                <img src="{{ $collection->getFirstMediaUrl('head', 'card') }}"
                                    alt="{{ $collection->collection_name }}"
                                    class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
                            @elseif ($collection->image_banner)
                                <img src="{{ asset('storage/' . $collection->image_banner) }}"
                                    alt="{{ $collection->collection_name }}"
                                    class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
                            @elseif ($collection->url_image_ipfs || $collection->path_image_to_ipfs)
                                <img src="{{ $collection->url_image_ipfs ?? asset('storage/' . $collection->path_image_to_ipfs) }}"
                                    alt="{{ $collection->collection_name }}"
                                    class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div
                                    class="flex h-full items-center justify-center bg-gradient-to-br from-[#2D5016]/10 to-[#2D5016]/5">
                                    <span class="text-6xl">🌿</span>
                                </div>
                            @endif

                            <!-- EPP Badge -->
                            <div
                                class="absolute right-3 top-3 rounded-full bg-[#2D5016] px-3 py-1 text-xs font-bold text-white shadow-sm">
                                EPP Collection
                            </div>
                        </div>

                        <div class="flex flex-col flex-1 p-5">
                            <h3 class="mb-2 text-xl font-bold leading-tight text-gray-900"
                                style="font-family: 'Playfair Display', serif;">
                                {{ $collection->collection_name }}
                            </h3>

                            <p class="flex-1 mb-4 text-sm text-gray-600 line-clamp-2">
                                {{ Str::limit($collection->description ?? '', 100) }}
                            </p>

                            <div class="grid grid-cols-2 gap-4 p-3 mb-4 rounded-lg bg-gray-50">
                                <div>
                                    <div class="text-xs font-medium text-gray-500 uppercase">Equilibrium</div>
                                    <div class="font-bold text-[#2D5016]">€XXX</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs font-medium text-gray-500 uppercase">EGI</div>
                                    <div class="font-bold text-gray-900">
                                        {{ $collection->egis_count ?? $collection->egis->count() }}</div>
                                </div>
                            </div>

                            <a href="{{ route('home.collections.show', $collection) }}"
                                class="block w-full rounded-lg border border-[#2D5016] py-2.5 text-center text-sm font-semibold text-[#2D5016] transition-colors hover:bg-[#2D5016] hover:text-white">
                                {{ __('epp_projects.show.view_collection') }} →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-600 col-span-full">
                        {{ __('epp_projects.index.no_projects') }}
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($collections->hasPages())
                <div class="mt-8">
                    {{ $collections->links() }}
                </div>
            @endif
        </section>

        <!-- EGI/Certificates -->
        <section class="container px-4 mx-auto mb-12">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold" style="font-family: 'Playfair Display', serif; color: #2D5016;">
                    🎨 {{ __('epp_projects.show.certificates_title') }}
                </h2>
                <p class="mt-2 text-gray-600">
                    {{ __('epp_projects.show.certificates_subtitle', ['count' => $egis->total()]) }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5">
                @forelse($egis as $egi)
                    <div class="relative transition-all egi-card group hover:shadow-lg">
                        <a href="{{ route('egis.show', $egi) }}" class="block h-full">
                            @php
                                // Use the accessor if available, otherwise construct path manually as fallback
                                $fileUrl = $egi->original_image_url ?? null;

                                // Fallback logic consistent with Model if accessor fails (e.g. missing key_file)
                                if (
                                    !$fileUrl &&
                                    $egi->collection_id &&
                                    $egi->user_id &&
                                    $egi->key_file &&
                                    $egi->extension
                                ) {
                                    $path = sprintf(
                                        'storage/users_files/collections_%d/creator_%d/%d.%s',
                                        $egi->collection_id,
                                        $egi->user_id,
                                        $egi->key_file,
                                        $egi->extension,
                                    );
                                    $fileUrl = asset($path);
                                }

                                // Also fallback to path_image if available (legacy)
                                if (!$fileUrl && $egi->path_image) {
                                    $fileUrl = asset('storage/' . $egi->path_image);
                                }

                                $isPdf = strtolower($egi->extension) === 'pdf' || $egi->file_mime === 'application/pdf';
                            @endphp

                            @if ($egi->url_image_ipfs || $fileUrl)
                                <div class="relative overflow-hidden bg-gray-200 aspect-square">
                                    @if ($isPdf)
                                        <embed src="{{ $fileUrl }}#toolbar=0&navpanes=0&scrollbar=0"
                                            type="application/pdf"
                                            class="object-cover w-full h-full pointer-events-none">
                                    @else
                                        <img src="{{ $egi->url_image_ipfs ?? $fileUrl }}" alt="{{ $egi->title }}"
                                            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105">
                                    @endif
                                </div>
                            @else
                                <div
                                    class="flex flex-col items-center justify-center transition-colors aspect-square bg-gradient-to-br from-green-100 to-blue-100 group-hover:from-green-200 group-hover:to-blue-200">
                                    <span class="mb-2 text-4xl">📜</span>
                                    <span
                                        class="text-xs font-semibold text-gray-600 group-hover:text-gray-900">{{ __('epp_projects.show.view_certificate') }}</span>
                                </div>
                            @endif

                            <div class="p-2">
                                <div class="text-xs font-semibold truncate" style="color: #2D5016;">
                                    {{ $egi->title }}
                                </div>
                                <div class="text-xs text-gray-600 truncate">
                                    #{{ $egi->id }}
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-600 col-span-full">
                        {{ __('epp_projects.index.no_projects') }}
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($egis->hasPages())
                <div class="mt-8">
                    {{ $egis->links() }}
                </div>
            @endif
        </section>

        <!-- Documentation & Evidence -->
        @if ($eppProject->evidence_url || $eppProject->media)
            <section class="container px-4 mx-auto mb-12">
                <div class="section-card">
                    <h2 class="mb-6 text-2xl font-bold"
                        style="font-family: 'Playfair Display', serif; color: #2D5016;">
                        📁 {{ __('epp_projects.show.documentation_title') }}
                    </h2>

                    @if ($eppProject->evidence_url)
                        <div class="mb-4">
                            <a href="{{ $eppProject->evidence_url }}" target="_blank" class="btn-secondary-epp">
                                {{ __('epp_projects.show.evidence_url') }} →
                            </a>
                        </div>
                    @endif

                    @if ($eppProject->media && count($eppProject->media) > 0)
                        <div>
                            <h3 class="mb-4 font-semibold">{{ __('epp_projects.show.media_gallery') }}</h3>
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                                @foreach ($eppProject->media as $media)
                                    <div class="overflow-hidden bg-gray-200 rounded-lg aspect-square">
                                        <img src="{{ $media }}" alt="Evidence"
                                            class="object-cover w-full h-full">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    </x-slot>
</x-guest-layout>
