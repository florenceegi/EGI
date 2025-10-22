<x-enterprise-sidebar logo="FlorenceEGI" badge="SuperAdmin" theme="superadmin">
    <div class="p-6">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                🧠 Gestione Consulenze AI
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Monitora e analizza tutte le richieste AI della piattaforma
            </p>
        </div>

        {{-- Stats Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">Totale Consulenze</div>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $consultations->total() }}</div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">Completate</div>
                <div class="mt-2 text-3xl font-bold text-green-600">
                    {{ $consultations->where('status', 'completed')->count() }}
                </div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">In Attesa</div>
                <div class="mt-2 text-3xl font-bold text-yellow-600">
                    {{ $consultations->where('status', 'pending')->count() }}
                </div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="text-sm font-medium text-gray-600">Fallite</div>
                <div class="mt-2 text-3xl font-bold text-red-600">
                    {{ $consultations->where('status', 'failed')->count() }}
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="mb-6 rounded-lg bg-white p-4 shadow">
            <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Tutti</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">User ID</label>
                    <input type="number" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Da</label>
                    <input type="date" name="date_from"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">A</label>
                    <input type="date" name="date_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="md:col-span-4">
                    <button type="submit" class="rounded-md bg-yellow-600 px-4 py-2 text-white hover:bg-yellow-700">
                        Applica Filtri
                    </button>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">EGI
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Data
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($consultations as $consultation)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">#{{ $consultation->id }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                @if ($consultation->egi)
                                    <a href="{{ route('egis.show', $consultation->egi) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        {{ Str::limit($consultation->egi->title, 30) }}
                                    </a>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                {{ $consultation->user->name ?? 'N/A' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                <span
                                    class="@if ($consultation->status === 'completed') bg-green-100 text-green-800
                                    @elseif($consultation->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($consultation->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                                    {{ $consultation->status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $consultation->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                <a href="{{ route('superadmin.ai.consultations.show', $consultation) }}"
                                    class="text-yellow-600 hover:text-yellow-900">
                                    Dettagli
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                Nessuna consulenza trovata
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $consultations->links() }}
        </div>
    </div>
</x-enterprise-sidebar>
