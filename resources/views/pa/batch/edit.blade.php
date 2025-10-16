<x-pa-layout>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('pa.batch.index') }}"
                class="mr-4 text-gray-400 hover:text-gray-600">
                <span class="material-icons">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ __('pa_batch.sources.edit') }}
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    {{ __('pa_batch.sources.subtitle') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <form method="POST" action="{{ route('pa.batch.update', $source->id) }}" class="px-4 py-6 sm:p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                {{-- Name --}}
                <div class="sm:col-span-4">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('pa_batch.sources.name') }} *
                    </label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" required
                            value="{{ old('name', $source->name) }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="sm:col-span-6">
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('pa_batch.sources.description') }}
                    </label>
                    <div class="mt-2">
                        <textarea name="description" id="description" rows="3"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('description', $source->description) }}</textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Path --}}
                <div class="sm:col-span-4">
                    <label for="path" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('pa_batch.sources.path') }} *
                    </label>
                    <div class="mt-2">
                        <input type="text" name="path" id="path" required
                            value="{{ old('path', $source->path) }}"
                            placeholder="/percorso/assoluto/directory"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('pa_batch.sources.path_help') }}
                    </p>
                    @error('path')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- File Pattern --}}
                <div class="sm:col-span-3">
                    <label for="file_pattern" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('pa_batch.sources.pattern') }}
                    </label>
                    <div class="mt-2">
                        <input type="text" name="file_pattern" id="file_pattern"
                            value="{{ old('file_pattern', $source->file_pattern) }}"
                            placeholder="*.pdf"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono">
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('pa_batch.sources.pattern_help') }}
                    </p>
                    @error('file_pattern')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Priority --}}
                <div class="sm:col-span-2">
                    <label for="priority" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('pa_batch.sources.priority') }}
                    </label>
                    <div class="mt-2">
                        <select name="priority" id="priority"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected(old('priority', $source->priority) == $i)>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ __('pa_batch.sources.priority_help') }}
                    </p>
                    @error('priority')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="sm:col-span-2">
                    <label for="status" class="block text-sm font-medium leading-6 text-gray-900">
                        {{ __('pa_batch.sources.status') }}
                    </label>
                    <div class="mt-2">
                        <select name="status" id="status"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="active" @selected(old('status', $source->status) === 'active')>
                                {{ __('pa_batch.sources.active') }}
                            </option>
                            <option value="paused" @selected(old('status', $source->status) === 'paused')>
                                {{ __('pa_batch.sources.paused') }}
                            </option>
                            <option value="disabled" @selected(old('status', $source->status) === 'disabled')>
                                {{ __('pa_batch.sources.disabled') }}
                            </option>
                        </select>
                    </div>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Auto Process --}}
                <div class="sm:col-span-6">
                    <div class="relative flex gap-x-3">
                        <div class="flex h-6 items-center">
                            <input type="checkbox" name="auto_process" id="auto_process" value="1"
                                @checked(old('auto_process', $source->auto_process))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        </div>
                        <div class="text-sm leading-6">
                            <label for="auto_process" class="font-medium text-gray-900">
                                {{ __('pa_batch.sources.auto_process') }}
                            </label>
                            <p class="text-gray-500">
                                {{ __('pa_batch.sources.auto_process_help') }}
                            </p>
                        </div>
                    </div>
                    @error('auto_process')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="{{ route('pa.batch.index') }}"
                    class="text-sm font-semibold leading-6 text-gray-900">
                    Annulla
                </a>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Salva Modifiche
                </button>
            </div>
        </form>
    </div>
</x-pa-layout>

