<x-pa-layout>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('pa.batch.index') }}" class="mr-4 text-gray-400 hover:text-gray-600">
                    <span class="material-icons">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $source->name }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ $source->description ?? __('pa_batch.sources.subtitle') }}
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pa.batch.edit', $source->id) }}"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <span class="material-icons mr-1.5 text-sm">edit</span>
                    Modifica
                </a>
                <form action="{{ route('pa.batch.toggle_status', $source->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center rounded-md {{ $source->status === 'active' ? 'bg-yellow-600 hover:bg-yellow-500' : 'bg-green-600 hover:bg-green-500' }} px-3 py-2 text-sm font-semibold text-white shadow-sm">
                        <span class="material-icons mr-1.5 text-sm">
                            {{ $source->status === 'active' ? 'pause' : 'play_arrow' }}
                        </span>
                        {{ $source->status === 'active' ? __('pa_batch.dashboard.pause') : __('pa_batch.dashboard.resume') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="material-icons text-green-400">check_circle</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Source Details Card --}}
    <div class="mb-6 overflow-hidden bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Dettagli Sorgente</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Configurazione e parametri di elaborazione</p>
        </div>
        <div class="border-t border-gray-200">
            <dl class="divide-y divide-gray-200">
                <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('pa_batch.sources.path') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-mono">
                        {{ $source->path }}
                    </dd>
                </div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('pa_batch.sources.pattern') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0 font-mono">
                        {{ $source->file_pattern }}
                    </dd>
                </div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('pa_batch.sources.status') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        @if ($source->status === 'active')
                            <span
                                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                {{ __('pa_batch.sources.active') }}
                            </span>
                        @elseif($source->status === 'paused')
                            <span
                                class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                {{ __('pa_batch.sources.paused') }}
                            </span>
                        @else
                            <span
                                class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                {{ __('pa_batch.sources.disabled') }}
                            </span>
                        @endif
                    </dd>
                </div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('pa_batch.sources.priority') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $source->priority }}</dd>
                </div>
                <div class="px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">{{ __('pa_batch.sources.auto_process') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        {{ $source->auto_process ? 'Sì' : 'No' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">{{ __('pa_batch.jobs.total') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                {{ number_format($stats['total']) }}
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">{{ __('pa_batch.dashboard.completed') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">
                {{ number_format($stats['completed']) }}
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">{{ __('pa_batch.dashboard.failed') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600">
                {{ number_format($stats['failed']) }}
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">{{ __('pa_batch.dashboard.pending') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">
                {{ number_format($stats['pending']) }}
            </dd>
        </div>
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">{{ __('pa_batch.status.duplicate') }}</dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-600">
                {{ number_format($stats['duplicate']) }}
            </dd>
        </div>
    </div>

    {{-- Recent Jobs Table --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <h2 class="text-base font-semibold leading-6 text-gray-900">
                {{ __('pa_batch.jobs.recent_jobs') }}
            </h2>

            @if ($jobs->isEmpty())
                <div class="mt-10 text-center">
                    <span class="material-icons mx-auto h-12 w-12 text-gray-400" style="font-size: 48px">
                        inbox
                    </span>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">
                        {{ __('pa_batch.jobs.no_jobs') }}
                    </h3>
                </div>
            @else
                <div class="mt-8 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                            {{ __('pa_batch.jobs.file_name') }}
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.jobs.status') }}
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.jobs.attempts') }}
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.jobs.created_at') }}
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Azioni</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($jobs as $job)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-0">
                                                <div class="font-medium text-gray-900">{{ $job->file_name }}</div>
                                                @if ($job->egi)
                                                    <div class="mt-1 text-gray-500">
                                                        {{ $job->egi->title }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                @if ($job->status === 'completed')
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                                        {{ __('pa_batch.status.completed') }}
                                                    </span>
                                                @elseif($job->status === 'failed')
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                                                        {{ __('pa_batch.status.failed') }}
                                                    </span>
                                                @elseif($job->status === 'processing')
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                                        {{ __('pa_batch.status.processing') }}
                                                    </span>
                                                @elseif($job->status === 'duplicate')
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                                                        {{ __('pa_batch.status.duplicate') }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                                                        {{ __('pa_batch.status.pending') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $job->attempts ?? 0 }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $job->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                @if ($job->egi)
                                                    <a href="{{ route('pa.acts.show', $job->egi->id) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('pa_batch.jobs.view_egi') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-pa-layout>

