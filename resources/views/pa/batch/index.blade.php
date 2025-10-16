<x-pa-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ __('pa_batch.dashboard.title') }}
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('pa_batch.dashboard.subtitle') }}
        </p>
    </div>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="mb-6 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="material-icons text-green-400">check_circle</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <span class="material-icons text-red-400">error</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Global Stats Cards --}}
    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Jobs --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                {{ __('pa_batch.dashboard.total_jobs') }}
            </dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                {{ number_format($totalJobs) }}
            </dd>
        </div>

        {{-- Completed --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                {{ __('pa_batch.dashboard.completed') }}
            </dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">
                {{ number_format($completedJobs) }}
            </dd>
        </div>

        {{-- Failed --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                {{ __('pa_batch.dashboard.failed') }}
            </dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-red-600">
                {{ number_format($failedJobs) }}
            </dd>
        </div>

        {{-- Success Rate --}}
        <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
            <dt class="truncate text-sm font-medium text-gray-500">
                {{ __('pa_batch.dashboard.success_rate') }}
            </dt>
            <dd class="mt-1 text-3xl font-semibold tracking-tight text-blue-600">
                @if ($totalJobs > 0)
                    {{ number_format(($completedJobs / $totalJobs) * 100, 1) }}%
                @else
                    0%
                @endif
            </dd>
        </div>
    </div>

    {{-- Sources List --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h2 class="text-base font-semibold leading-6 text-gray-900">
                        {{ __('pa_batch.sources.title') }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-700">
                        {{ __('pa_batch.sources.subtitle') }}
                    </p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a href="{{ route('pa.batch.create') }}"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        {{ __('pa_batch.sources.create') }}
                    </a>
                </div>
            </div>

            @if ($sources->isEmpty())
                {{-- Empty State --}}
                <div class="mt-10 text-center">
                    <span class="material-icons mx-auto h-12 w-12 text-gray-400" style="font-size: 48px">
                        folder_open
                    </span>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">
                        {{ __('pa_batch.sources.no_sources') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('pa_batch.sources.no_sources_desc') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('pa.batch.create') }}"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <span class="material-icons -ml-0.5 mr-1.5 h-5 w-5">add</span>
                            {{ __('pa_batch.sources.create') }}
                        </a>
                    </div>
                </div>
            @else
                {{-- Sources Table --}}
                <div class="mt-8 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                            {{ __('pa_batch.sources.name') }}
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.sources.path') }}
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.sources.status') }}
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.jobs.total') }}
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.dashboard.completed') }}
                                        </th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('pa_batch.dashboard.failed') }}
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">{{ __('pa_batch.dashboard.manage') }}</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($sources as $source)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-0">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="font-medium text-gray-900">
                                                            {{ $source->name }}
                                                        </div>
                                                        @if ($source->description)
                                                            <div class="mt-1 text-gray-500">
                                                                {{ \Str::limit($source->description, 50) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                <div class="font-mono text-xs">{{ $source->path }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
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
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ number_format($source->total_jobs) }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-green-600">
                                                {{ number_format($source->completed_jobs) }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-red-600">
                                                {{ number_format($source->failed_jobs) }}
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <a href="{{ route('pa.batch.show', $source->id) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('pa_batch.dashboard.view_details') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-pa-layout>

