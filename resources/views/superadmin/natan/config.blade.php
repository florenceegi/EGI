{{--
/**
 * NATAN AI Configuration Panel
 *
 * @package Resources\Views\Superadmin\Natan
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (FlorenceEGI - NATAN Configuration)
 * @date 2025-10-27
 * @purpose Superadmin interface for configuring NATAN AI system parameters
 */
--}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('natan.config.title') }}
            </h2>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('superadmin.natan.config.reset') }}"
                    onsubmit="return confirm('{{ __('natan.config.confirm_reset') }}')">
                    @csrf
                    <button type="submit" class="btn-secondary">
                        {{ __('natan.config.reset_defaults') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.natan.config.update') }}">
                @csrf

                {{-- Claude API Limits --}}
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <h3 class="mb-4 text-lg font-semibold text-[#1B365D]">
                            {{ __('natan.config.sections.claude_limits') }}
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.claude_context_limit') }}
                                </label>
                                <input type="number" name="claude_context_limit"
                                    value="{{ old('claude_context_limit', $config['claude_context_limit']) }}"
                                    min="5" max="500"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.claude_context_limit') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.claude_context_limit_minimum') }}
                                </label>
                                <input type="number" name="claude_context_limit_minimum"
                                    value="{{ old('claude_context_limit_minimum', $config['claude_context_limit_minimum']) }}"
                                    min="1" max="50"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.claude_context_limit_minimum') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Token Management --}}
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <h3 class="mb-4 text-lg font-semibold text-[#1B365D]">
                            {{ __('natan.config.sections.token_management') }}
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.max_tokens_per_call') }}
                                </label>
                                <input type="number" name="max_tokens_per_call"
                                    value="{{ old('max_tokens_per_call', $config['max_tokens_per_call']) }}"
                                    min="10000" max="200000" step="1000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">{{ __('natan.config.help.max_tokens_per_call') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.reserved_tokens_system') }}
                                </label>
                                <input type="number" name="reserved_tokens_system"
                                    value="{{ old('reserved_tokens_system', $config['reserved_tokens_system']) }}"
                                    min="500" max="10000" step="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.reserved_tokens_system') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.reserved_tokens_output') }}
                                </label>
                                <input type="number" name="reserved_tokens_output"
                                    value="{{ old('reserved_tokens_output', $config['reserved_tokens_output']) }}"
                                    min="1000" max="20000" step="100"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.reserved_tokens_output') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.avg_tokens_per_char') }}
                                </label>
                                <input type="number" name="avg_tokens_per_char"
                                    value="{{ old('avg_tokens_per_char', $config['avg_tokens_per_char']) }}"
                                    min="0.1" max="1.0" step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">{{ __('natan.config.help.avg_tokens_per_char') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- User Controls (Slider) --}}
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <h3 class="mb-4 text-lg font-semibold text-[#1B365D]">
                            {{ __('natan.config.sections.user_controls') }}
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.slider_min_acts') }}
                                </label>
                                <input type="number" name="slider_min_acts"
                                    value="{{ old('slider_min_acts', $config['slider_min_acts']) }}" min="10"
                                    max="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">{{ __('natan.config.help.slider_min_acts') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.slider_max_acts') }}
                                </label>
                                <input type="number" name="slider_max_acts"
                                    value="{{ old('slider_max_acts', $config['slider_max_acts']) }}" min="100"
                                    max="50000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">{{ __('natan.config.help.slider_max_acts') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.slider_default_acts') }}
                                </label>
                                <input type="number" name="slider_default_acts"
                                    value="{{ old('slider_default_acts', $config['slider_default_acts']) }}"
                                    min="50" max="10000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.slider_default_acts') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cost & Time Estimation --}}
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <h3 class="mb-4 text-lg font-semibold text-[#1B365D]">
                            {{ __('natan.config.sections.estimation') }}
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.cost_per_chunk') }}
                                </label>
                                <input type="number" name="cost_per_chunk"
                                    value="{{ old('cost_per_chunk', $config['cost_per_chunk']) }}" min="0.01"
                                    max="10.0" step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">{{ __('natan.config.help.cost_per_chunk') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.cost_aggregation') }}
                                </label>
                                <input type="number" name="cost_aggregation"
                                    value="{{ old('cost_aggregation', $config['cost_aggregation']) }}" min="0.01"
                                    max="5.0" step="0.01"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">{{ __('natan.config.help.cost_aggregation') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.time_per_chunk_seconds') }}
                                </label>
                                <input type="number" name="time_per_chunk_seconds"
                                    value="{{ old('time_per_chunk_seconds', $config['time_per_chunk_seconds']) }}"
                                    min="1" max="120"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.time_per_chunk_seconds') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.time_aggregation_seconds') }}
                                </label>
                                <input type="number" name="time_aggregation_seconds"
                                    value="{{ old('time_aggregation_seconds', $config['time_aggregation_seconds']) }}"
                                    min="1" max="60"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.time_aggregation_seconds') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quality & Strategy --}}
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <h3 class="mb-4 text-lg font-semibold text-[#1B365D]">
                            {{ __('natan.config.sections.quality') }}
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.min_relevance_score') }}
                                </label>
                                <input type="number" name="min_relevance_score"
                                    value="{{ old('min_relevance_score', $config['min_relevance_score']) }}"
                                    min="0.0" max="1.0" step="0.1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.min_relevance_score') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.chunking_strategy') }}
                                </label>
                                <select name="chunking_strategy"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="token-based"
                                        {{ $config['chunking_strategy'] == 'token-based' ? 'selected' : '' }}>
                                        {{ __('natan.config.strategies.token_based') }}
                                    </option>
                                    <option value="relevance-based"
                                        {{ $config['chunking_strategy'] == 'relevance-based' ? 'selected' : '' }}>
                                        {{ __('natan.config.strategies.relevance_based') }}
                                    </option>
                                    <option value="adaptive"
                                        {{ $config['chunking_strategy'] == 'adaptive' ? 'selected' : '' }}>
                                        {{ __('natan.config.strategies.adaptive') }}
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">{{ __('natan.config.help.chunking_strategy') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Features & Rate Limiting --}}
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200 bg-white p-6">
                        <h3 class="mb-4 text-lg font-semibold text-[#1B365D]">
                            {{ __('natan.config.sections.system') }}
                        </h3>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="enable_progress_tracking" value="1"
                                    {{ $config['enable_progress_tracking'] ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-[#1B365D] shadow-sm">
                                <label class="ml-2 block text-sm text-gray-700">
                                    {{ __('natan.config.enable_progress_tracking') }}
                                </label>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.rate_limit_max_retries') }}
                                </label>
                                <input type="number" name="rate_limit_max_retries"
                                    value="{{ old('rate_limit_max_retries', $config['rate_limit_max_retries']) }}"
                                    min="1" max="10"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.rate_limit_max_retries') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('natan.config.rate_limit_initial_delay_seconds') }}
                                </label>
                                <input type="number" name="rate_limit_initial_delay_seconds"
                                    value="{{ old('rate_limit_initial_delay_seconds', $config['rate_limit_initial_delay_seconds']) }}"
                                    min="1" max="60"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('natan.config.help.rate_limit_initial_delay_seconds') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="rounded bg-[#1B365D] px-6 py-2 font-bold text-white hover:bg-[#2D5016]">
                        {{ __('natan.config.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
