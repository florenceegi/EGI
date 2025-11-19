{{-- resources/views/epp-projects/index.blade.php --}}
<x-guest-layout 
    :title="__('epp_projects.index.page_title')" 
    :metaDescription="__('epp_projects.index.meta_description')">

    {{-- Slot personalizzato per disabilitare la hero section --}}
    <x-slot name="noHero">true</x-slot>

    {{-- Styles --}}
    <x-slot name="headExtra">
        <style>
            /* EPP Projects Index - Brand Guidelines Compliant */
            .epp-hero {
                background: linear-gradient(135deg, #2D5016 0%, #1B365D 100%);
                color: white;
                padding: 4rem 0;
            }

            .equilibrium-card {
                background: white;
                border-radius: 1rem;
                padding: 2rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .equilibrium-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px rgba(45, 80, 22, 0.15);
            }

            .equilibrium-value {
                font-size: 2.5rem;
                font-weight: 700;
                color: #2D5016;
                font-family: 'Playfair Display', serif;
            }

            .project-card {
                background: white;
                border-radius: 1rem;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                border-left: 4px solid #2D5016;
            }

            .project-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 12px 24px rgba(45, 80, 22, 0.2);
            }

            .project-type-badge {
                display: inline-block;
                padding: 0.5rem 1rem;
                border-radius: 2rem;
                font-size: 0.875rem;
                font-weight: 600;
            }

            .badge-arf {
                background: rgba(76, 175, 80, 0.1);
                color: #2D5016;
            }

            .badge-apr {
                background: rgba(27, 54, 93, 0.1);
                color: #1B365D;
            }

            .badge-bpe {
                background: rgba(212, 165, 116, 0.1);
                color: #D4A574;
            }

            .progress-bar-container {
                background: #E5E7EB;
                border-radius: 9999px;
                height: 0.5rem;
                overflow: hidden;
            }

            .progress-bar {
                background: linear-gradient(90deg, #2D5016 0%, #4CAF50 100%);
                height: 100%;
                transition: width 0.6s ease;
            }

            .stat-icon {
                width: 3rem;
                height: 3rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                background: rgba(45, 80, 22, 0.1);
                color: #2D5016;
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
            }

            .btn-primary-epp:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 16px rgba(45, 80, 22, 0.3);
            }

            .type-distribution-bar {
                display: flex;
                height: 3rem;
                border-radius: 0.5rem;
                overflow: hidden;
                margin-top: 1rem;
            }

            .type-segment {
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 0.875rem;
                transition: all 0.3s ease;
            }

            .type-segment:hover {
                filter: brightness(1.1);
            }

            .segment-arf {
                background: #2D5016;
                color: white;
            }

            .segment-apr {
                background: #1B365D;
                color: white;
            }

            .segment-bpe {
                background: #D4A574;
                color: white;
            }
        </style>
    </x-slot>

    {{-- Main Content --}}
    <x-slot name="slot">
        <!-- Hero Section -->
        <div class="epp-hero">
            <div class="container px-4 mx-auto">
                <div class="text-center">
                    <div class="mb-4 text-6xl">🌱</div>
                    <h1 class="mb-4 text-5xl font-bold" style="font-family: 'Playfair Display', serif;">
                        {{ __('epp_projects.index.hero_title') }}
                    </h1>
                    <p class="max-w-2xl mx-auto text-xl opacity-90">
                        {{ __('epp_projects.index.hero_subtitle') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Platform Stats & Equilibrium -->
        <div class="py-12 bg-gray-50">
            <div class="container px-4 mx-auto">
                <!-- Total Equilibrium -->
                <div class="mb-8 equilibrium-card">
                    <div class="grid items-center grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <div class="mb-2 text-sm text-gray-600">
                                {{ __('epp_projects.index.total_equilibrium') }}
                            </div>
                            <div class="equilibrium-value">
                                💎 €{{ number_format($totalEquilibrium, 0, ',', '.') }}
                            </div>
                            <div class="mt-4 text-sm text-gray-600">
                                {{ __('epp_projects.index.equilibrium_description') }}
                            </div>
                        </div>
                        <div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="mb-2 stat-icon" style="margin: 0 auto;">🌳</div>
                                    <div class="mb-1 text-sm text-gray-600">
                                        {{ __('epp_projects.index.active_projects') }}
                                    </div>
                                    <div class="text-2xl font-bold" style="color: #2D5016;">
                                        {{ $projectCount }}
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="mb-2 stat-icon" style="margin: 0 auto; background: rgba(212, 165, 116, 0.1); color: #D4A574;">
                                        📚
                                    </div>
                                    <div class="mb-1 text-sm text-gray-600">
                                        {{ __('epp_projects.index.total_collections') }}
                                    </div>
                                    <div class="text-2xl font-bold" style="color: #D4A574;">
                                        {{ $collectionCount }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Type Distribution -->
                <div class="p-6 bg-white rounded-lg shadow-md">
                    <h3 class="mb-4 text-lg font-bold" style="color: #2D5016;">
                        {{ __('epp_projects.index.project_distribution') }}
                    </h3>
                    <div class="type-distribution-bar">
                        @if($projectsByType['ARF'] > 0)
                            <div class="type-segment segment-arf" 
                                 style="width: {{ ($projectsByType['ARF'] / $projectCount) * 100 }}%"
                                 title="{{ __('epp_projects.types.arf') }}: {{ $projectsByType['ARF'] }}">
                                🌳 {{ $projectsByType['ARF'] }}
                            </div>
                        @endif
                        @if($projectsByType['APR'] > 0)
                            <div class="type-segment segment-apr" 
                                 style="width: {{ ($projectsByType['APR'] / $projectCount) * 100 }}%"
                                 title="{{ __('epp_projects.types.apr') }}: {{ $projectsByType['APR'] }}">
                                🌊 {{ $projectsByType['APR'] }}
                            </div>
                        @endif
                        @if($projectsByType['BPE'] > 0)
                            <div class="type-segment segment-bpe" 
                                 style="width: {{ ($projectsByType['BPE'] / $projectCount) * 100 }}%"
                                 title="{{ __('epp_projects.types.bpe') }}: {{ $projectsByType['BPE'] }}">
                                🐝 {{ $projectsByType['BPE'] }}
                            </div>
                        @endif
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div class="text-center">
                            <div class="text-2xl">🌳</div>
                            <div class="text-sm font-semibold" style="color: #2D5016;">
                                {{ __('epp_projects.types.arf') }}
                            </div>
                            <div class="text-lg font-bold" style="color: #2D5016;">
                                {{ $projectsByType['ARF'] }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl">🌊</div>
                            <div class="text-sm font-semibold" style="color: #1B365D;">
                                {{ __('epp_projects.types.apr') }}
                            </div>
                            <div class="text-lg font-bold" style="color: #1B365D;">
                                {{ $projectsByType['APR'] }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl">🐝</div>
                            <div class="text-sm font-semibold" style="color: #D4A574;">
                                {{ __('epp_projects.types.bpe') }}
                            </div>
                            <div class="text-lg font-bold" style="color: #D4A574;">
                                {{ $projectsByType['BPE'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects List -->
        <div class="py-12 bg-white">
            <div class="container px-4 mx-auto">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold" style="font-family: 'Playfair Display', serif; color: #2D5016;">
                        {{ __('epp_projects.index.projects_title') }}
                    </h2>
                </div>

                <!-- Projects Grid -->
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($projects as $project)
                        <div class="project-card">
                            <div class="p-6">
                                <!-- Project Type & Icon -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="text-4xl">
                                        @if($project->project_type === 'ARF')
                                            🌳
                                        @elseif($project->project_type === 'APR')
                                            🌊
                                        @elseif($project->project_type === 'BPE')
                                            🐝
                                        @endif
                                    </div>
                                    <span class="project-type-badge badge-{{ strtolower($project->project_type) }}">
                                        {{ __('epp_projects.types.' . strtolower($project->project_type)) }}
                                    </span>
                                </div>

                                <!-- Project Name -->
                                <h3 class="mb-2 text-xl font-bold" style="font-family: 'Playfair Display', serif; color: #2D5016;">
                                    {{ $project->name }}
                                </h3>

                                <!-- EPP Organization -->
                                <p class="mb-4 text-sm text-gray-600">
                                    {{ __('epp_projects.index.by') }} 
                                    <span class="font-semibold" style="color: #2D5016;">
                                        {{ $project->eppUser->organizationData->organization_name ?? $project->eppUser->name }}
                                    </span>
                                </p>

                                <!-- Description -->
                                <p class="mb-4 text-sm text-gray-700 line-clamp-3">
                                    {{ Str::limit($project->description, 120) }}
                                </p>

                                <!-- Progress -->
                                <div class="mb-4">
                                    <div class="flex justify-between mb-2 text-sm">
                                        <span class="text-gray-600">{{ __('epp_projects.index.progress') }}</span>
                                        <span class="font-bold" style="color: #2D5016;">
                                            {{ round($project->completion_percentage) }}%
                                        </span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" 
                                             style="width: {{ $project->completion_percentage }}%"></div>
                                    </div>
                                    <div class="flex justify-between mt-2 text-xs text-gray-600">
                                        <span>{{ number_format($project->current_value, 0, ',', '.') }}</span>
                                        <span>{{ number_format($project->target_value, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Equilibrium & Collections -->
                                <div class="grid grid-cols-2 gap-4 pt-4 mb-4 border-t">
                                    <div>
                                        <div class="text-xs text-gray-600">Equilibrium</div>
                                        <div class="font-bold" style="color: #D4A574;">
                                            💎 €{{ number_format($project->equilibrium ?? 0, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-600">{{ __('epp_projects.index.collections') }}</div>
                                        <div class="font-bold" style="color: #1B365D;">
                                            📚 {{ $project->collections_count ?? 0 }}
                                        </div>
                                    </div>
                                </div>

                                <!-- CTA -->
                                <a href="{{ route('epp-projects.show', $project) }}" 
                                   class="block py-3 text-center transition-all rounded-lg btn-primary-epp">
                                    {{ __('epp_projects.index.view_project') }} →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-600 col-span-full">
                            <div class="mb-4 text-6xl">🌱</div>
                            <p class="text-lg">{{ __('epp_projects.index.no_projects') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($projects->hasPages())
                    <div class="mt-12">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-slot>
</x-guest-layout>
