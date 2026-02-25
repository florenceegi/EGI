<x-layouts.superadmin pageTitle="{{ __('menu.superadmin_platform_settings') }}">

    {{-- Header --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold text-base-content">⚙️ {{ __('platform_settings.title') }}</h1>
            <p class="mt-2 text-lg text-base-content/70">{{ __('platform_settings.subtitle') }}</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-6 shadow-lg" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Gruppi di setting --}}
    @forelse ($settings as $group => $groupSettings)
        <div class="card mb-6 bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="card-title text-xl capitalize">
                        🔧 {{ str_replace('_', ' ', $group) }}
                        <span class="badge badge-ghost badge-sm">{{ $groupSettings->count() }} setting</span>
                    </h2>
                    <button type="submit" form="form-{{ $group }}" class="btn btn-primary btn-sm">
                        💾 {{ __('platform_settings.save_group') }}
                    </button>
                </div>

                <form id="form-{{ $group }}"
                    action="{{ route('superadmin.platform-settings.update-group', $group) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>{{ __('platform_settings.col_key') }}</th>
                                    <th>{{ __('platform_settings.col_label') }}</th>
                                    <th>{{ __('platform_settings.col_value') }}</th>
                                    <th>{{ __('platform_settings.col_type') }}</th>
                                    <th>{{ __('platform_settings.col_description') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupSettings as $setting)
                                    <tr>
                                        <td>
                                            <code
                                                class="rounded bg-base-200 px-2 py-1 text-xs">{{ $setting->key }}</code>
                                        </td>
                                        <td class="font-semibold">
                                            {{ $setting->label ?? $setting->key }}
                                        </td>
                                        <td class="min-w-[200px]">
                                            @if ($setting->is_editable)
                                                <input type="text" name="settings[{{ $setting->id }}]"
                                                    value="{{ $setting->value }}"
                                                    class="input input-sm input-bordered w-full font-mono"
                                                    aria-label="{{ $setting->label ?? $setting->key }}">
                                            @else
                                                <span class="font-mono text-sm text-base-content/60">
                                                    {{ $setting->value }}
                                                </span>
                                                <span class="badge badge-ghost badge-xs ml-1">readonly</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-outline badge-xs">{{ $setting->value_type }}</span>
                                        </td>
                                        <td class="max-w-xs text-sm text-base-content/60">
                                            {{ $setting->description }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <span>{{ __('platform_settings.empty') }}</span>
        </div>
    @endforelse

</x-layouts.superadmin>
