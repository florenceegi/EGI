<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Certificate of Authenticity - {{ $certificate['serial'] }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-amber-50 to-yellow-50">

{{-- Certificate Header --}}
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">

        {{-- Title Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-amber-900 mb-2">
                Certificato di Autenticità
            </h1>
            <p class="text-amber-700 text-base">
                Visualizzazione Pubblica di Verifica
            </p>

            {{-- EGI Image --}}
            @if(isset($artwork['image_url']) && $artwork['image_url'])
                <div class="mt-6 flex justify-center">
                    <div class="relative">
                        <img src="{{ $artwork['image_url'] }}"
                             alt="{{ $artwork['name'] }}"
                             class="w-64 h-64 object-cover rounded-lg shadow-lg border-4 border-amber-200">
                        <div class="absolute -bottom-2 -right-2 bg-amber-600 text-white text-xs px-2 py-1 rounded-full">
                            ID: {{ $artwork['internal_id'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Status Banner --}}
        <div class="max-w-5xl mx-auto mb-6">
            @if($certificate['effective_status'] === 'valid')
                <div class="bg-green-500 text-white rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <div class="font-bold text-lg">Certificato Verificato e Autentico</div>
                            <div class="text-sm opacity-90">Verificato il: {{ now()->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold">{{ $certificate['serial'] }}</div>
                        <div class="text-sm opacity-90">Numero Seriale</div>
                    </div>
                </div>
            @elseif($certificate['effective_status'] === 'incomplete')
                <div class="bg-amber-500 text-white rounded-lg p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <div class="font-bold text-lg">Certificato Non Pronto</div>
                            <div class="text-sm opacity-90">Emesso il: {{ $certificate['issued_at']->format('d/m/Y H:i') }} - Requires CoA Traits</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold">{{ $certificate['serial'] }}</div>
                        <div class="text-sm opacity-90">Numero Seriale</div>
                    </div>
                </div>
            @elseif($certificate['status'] === 'revoked')
                <div class="bg-red-500 text-white rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <div class="font-bold text-lg">Certificato Revocato</div>
                            <div class="text-sm opacity-90">Questo certificato non è più valido</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- CoA Traits Completeness Warning --}}
        @if(isset($artwork['traits']) && isset($artwork['traits']['traits_incomplete']) && $artwork['traits']['traits_incomplete'])
                    {{-- Warning for Generic Traits (only if no CoA traits) --}}
        @if(!$certificate['has_coa_traits'])
            <div class="max-w-5xl mx-auto mb-6">
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-base">Certificato Non Pronto - Traits Generici</div>
                            <div class="text-sm mt-1">
                                Questo certificato è stato generato utilizzando i traits generici EGI invece dei traits CoA specifici.
                                Il certificato NON È VALIDO finché non vengono configurati i CoA traits (tecnica, materiali, supporto) dal pannello di gestione dell'opera.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @endif

        {{-- Main Certificate Content --}}
        <div class="max-w-5xl mx-auto">
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">

                {{-- Certificate Body --}}
                <div class="grid md:grid-cols-2 gap-8 p-8">

                    {{-- LEFT: Identità Opera --}}
                    <div class="space-y-6">
                        <h2 class="text-lg font-bold text-gray-900 border-b pb-2">
                            Informazioni Opera
                        </h2>

                        {{-- Titolo e ID --}}
                        <div>
                            <label class="text-xs font-medium text-gray-600">Titolo</label>
                            <div class="text-base font-bold text-gray-900">{{ $artwork['name'] }}</div>
                            <div class="text-xs text-gray-500 mt-1">ID interno: #{{ $artwork['internal_id'] ?? 'N/A' }}</div>
                        </div>

                        {{-- Anno --}}
                        @if($artwork['year'])
                        <div>
                            <label class="text-xs font-medium text-gray-600">Anno</label>
                            <div class="text-sm font-semibold text-gray-900">{{ $artwork['year'] }}</div>
                        </div>
                        @endif

                        {{-- COA Traits Box (Tecnica, Materiali, Supporto) --}}
                        @if($artwork['technique'] || $artwork['materials'] || $artwork['support'])
                        <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-lg p-4 border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Caratteristiche Tecniche
                            </h4>
                            <div class="grid grid-cols-1 gap-3">
                                @if($artwork['technique'])
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full flex-shrink-0"></div>
                                    <div>
                                        <span class="text-xs font-medium text-blue-700">Tecnica:</span>
                                        <span class="text-sm text-gray-900 ml-1">{{ $artwork['technique'] }}</span>
                                    </div>
                                </div>
                                @endif

                                @if($artwork['materials'])
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full flex-shrink-0"></div>
                                    <div>
                                        <span class="text-xs font-medium text-green-700">Materiali:</span>
                                        <span class="text-sm text-gray-900 ml-1">{{ $artwork['materials'] }}</span>
                                    </div>
                                </div>
                                @endif

                                @if($artwork['support'])
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-amber-500 rounded-full flex-shrink-0"></div>
                                    <div>
                                        <span class="text-xs font-medium text-amber-700">Supporto:</span>
                                        <span class="text-sm text-gray-900 ml-1">{{ $artwork['support'] }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Dimensioni --}}
                        @if($artwork['dimensions'])
                        <div>
                            <label class="text-xs font-medium text-gray-600">Dimensioni</label>
                            <div class="text-sm font-semibold text-gray-900">{{ $artwork['dimensions'] }}</div>
                        </div>
                        @endif

                        {{-- Edizione --}}
                        @if($artwork['edition'])
                        <div>
                            <label class="text-xs font-medium text-gray-600">Edizione</label>
                            <div class="text-sm font-semibold text-gray-900">{{ $artwork['edition'] }}</div>
                        </div>
                        @endif

                        {{-- Autore --}}
                        <div>
                            <label class="text-xs font-medium text-gray-600">Autore</label>
                            <div class="text-sm font-bold text-gray-900">{{ $artwork['author'] }}</div>
                        </div>

                        {{-- Tutti i Traits / Metadati dell'Opera --}}
                        @if(isset($artwork['traits']['data']) && count($artwork['traits']['data']) > 0)
                        <div class="bg-gray-50 border rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Metadati e Caratteristiche dell'Opera
                            </h3>
                            <div class="grid grid-cols-1 gap-2 text-sm">
                                @foreach($artwork['traits']['data'] as $trait)
                                    @if(isset($trait['value']) && $trait['value'] && trim($trait['value']) !== '')
                                    <div class="flex justify-between items-center py-1 border-b border-gray-200 last:border-0">
                                        <span class="font-medium text-gray-700 flex items-center">
                                            {{ $trait['trait_type'] }}:
                                            @if(isset($trait['category']) && $trait['category'] !== 'generic')
                                                @if($trait['category'] === 'technique')
                                                    <span class="ml-1 text-xs bg-blue-100 text-blue-600 px-1 rounded">Tecnica</span>
                                                @elseif($trait['category'] === 'materials')
                                                    <span class="ml-1 text-xs bg-green-100 text-green-600 px-1 rounded">Materiale</span>
                                                @elseif($trait['category'] === 'support')
                                                    <span class="ml-1 text-xs bg-amber-100 text-amber-600 px-1 rounded">Supporto</span>
                                                @elseif($trait['category'] === 'platform_metadata')
                                                    <span class="ml-1 text-xs bg-purple-100 text-purple-600 px-1 rounded">Piattaforma</span>
                                                @endif
                                            @endif
                                        </span>
                                        <span class="text-gray-900 text-right">{{ $trait['value'] }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Metadata Aggiuntivi (descrizione, info tecniche, ecc.) --}}
                        @if(isset($artwork['traits']['metadata']) && count($artwork['traits']['metadata']) > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Informazioni Aggiuntive
                            </h3>
                            <div class="space-y-3">
                                @php
                                    $groupedMetadata = collect($artwork['traits']['metadata'])->groupBy('category');
                                @endphp

                                @foreach($groupedMetadata as $category => $items)
                                    <div>
                                        <h4 class="text-xs font-medium text-blue-700 uppercase tracking-wide mb-2">
                                            @if($category === 'artwork_info')
                                                📝 Informazioni Opera
                                            @elseif($category === 'technical')
                                                ⚙️ Informazioni Tecniche
                                            @elseif($category === 'platform_metadata')
                                                🏛️ Metadati Piattaforma
                                            @else
                                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                                            @endif
                                        </h4>
                                        <div class="grid grid-cols-1 gap-1 text-sm">
                                            @foreach($items as $item)
                                                <div class="flex justify-between items-start py-1">
                                                    <span class="font-medium text-blue-700 text-xs">{{ $item['label'] }}:</span>
                                                    <span class="text-blue-900 text-right text-xs max-w-xs">
                                                        @if($item['type'] === 'description' && strlen($item['value']) > 100)
                                                            {{ Str::limit($item['value'], 100) }}
                                                        @else
                                                            {{ $item['value'] }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Backward compatibility for old format --}}
                        @if(isset($artwork['traits']) && !isset($artwork['traits']['data']) && count($artwork['traits']) > 0)
                        <div class="bg-gray-50 border rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Metadati e Caratteristiche dell'Opera
                            </h3>
                            <div class="grid grid-cols-1 gap-2 text-sm">
                                @foreach($artwork['traits'] as $trait)
                                    @if(isset($trait['value']) && $trait['value'] && trim($trait['value']) !== '')
                                    <div class="flex justify-between items-center py-1 border-b border-gray-200 last:border-0">
                                        <span class="font-medium text-gray-700">{{ $trait['trait_type'] ?? 'Trait' }}:</span>
                                        <span class="text-gray-900 text-right">{{ $trait['value'] }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Creator Block (mostrato solo quando Creator ≠ Autore) --}}
                        @if(isset($creator))
                        <div class="col-span-full bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-blue-900">Pubblicato da</h4>
                                    <p class="text-sm text-blue-800">
                                        <span class="font-medium">{{ $creator['name'] }}</span>
                                        <span class="text-blue-600">({{ $creator['role'] }})</span>
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        Relazione con l'autore: {{ $creator['relationship_to_author'] }}
                                    </p>
                                    @if(isset($annexes['authorization']))
                                    <p class="text-xs text-blue-600 mt-1">
                                        <a href="#annex-e" class="underline hover:text-blue-800">
                                            → Vedi documentazione autorizzazione (Annesso E)
                                        </a>
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Thumbnail con link --}}
                        @if($artwork['thumbnail'])
                        <div>
                            <label class="text-sm font-medium text-gray-600">Immagine</label>
                            <div class="mt-2">
                                <a href="{{ $artwork['dossier_link'] ?? '#' }}" target="_blank" class="block">
                                    <img src="{{ $artwork['thumbnail'] }}" alt="{{ $artwork['name'] }}"
                                         class="w-32 h-32 object-cover rounded-lg border hover:shadow-lg transition-shadow">
                                </a>
                                @if($artwork['dossier_link'])
                                <a href="{{ $artwork['dossier_link'] }}" target="_blank"
                                   class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                    → Visualizza dossier immagini completo
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- RIGHT: Dettagli Certificato --}}
                    <div class="space-y-5">
                        <h2 class="text-lg font-bold text-gray-900 border-b pb-2">
                            Dettagli Certificato
                        </h2>

                        {{-- Data Emissione --}}
                        <div>
                            <label class="text-xs font-medium text-gray-600">Data Emissione</label>
                            <div class="text-sm font-semibold text-gray-900">{{ $certificate['issued_at']->format('d/m/Y') }}</div>
                        </div>

                        {{-- Emesso da --}}
                        <div>
                            <label class="text-xs font-medium text-gray-600">Emesso da</label>
                            <div class="text-sm font-semibold text-gray-900">{{ $certificate['issuer_name'] }}</div>
                        </div>

                        {{-- Luogo di emissione --}}
                        @if($certificate['issue_location'])
                        <div>
                            <label class="text-xs font-medium text-gray-600">Luogo di emissione</label>
                            <div class="text-sm font-semibold text-gray-900">{{ $certificate['issue_location'] }}</div>
                        </div>
                        @endif

                        {{-- Firma --}}
                        <div>
                            <label class="text-xs font-medium text-gray-600">Firma</label>
                            <div class="flex items-center space-x-2">
                                @if($certificate['qes_signature'])
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">QES</span>
                                @endif
                                @if($certificate['wallet_signature'])
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">Firma wallet</span>
                                    <code class="text-xs text-gray-600">{{ Str::limit($certificate['wallet_public_key'], 16) }}...</code>
                                    <a href="#" class="text-xs text-blue-600 hover:underline">verifica firma</a>
                                @endif
                            </div>
                        </div>

                        {{-- Hash del certificato (SHA-256) --}}
                        <div>
                            <label class="text-sm font-medium text-gray-600">Hash del certificato (SHA-256)</label>
                            <div class="bg-gray-50 p-3 rounded border mt-1">
                                <div class="flex items-center justify-between">
                                    <code class="text-xs font-mono text-gray-800 break-all">{{ $certificate['verification_hash'] }}</code>
                                    <button onclick="copyHash()" aria-label="Copia hash"
                                            class="ml-2 text-gray-500 hover:text-gray-700 flex-shrink-0">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"/>
                                            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- QR Code --}}
                        <div>
                            <label class="text-sm font-medium text-gray-600">QR Code Verifica</label>
                            <div class="mt-2">
                                <div class="w-24 h-24 bg-gray-200 rounded border flex items-center justify-center">
                                    <span class="text-xs text-gray-500">QR Code</span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Scansiona per verificare</div>
                            </div>
                        </div>

                        {{-- Stato --}}
                        <div>
                            <label class="text-sm font-medium text-gray-600">Stato</label>
                            <div class="mt-1">
                                @if($certificate['effective_status'] === 'valid')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Valid</span>
                                @elseif($certificate['effective_status'] === 'incomplete')
                                    <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm font-medium">Non Pronto</span>
                                @elseif($certificate['status'] === 'revoked')
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">Revoked</span>
                                @endif
                            </div>
                        </div>

                        {{-- Note --}}
                        @if($certificate['notes'])
                        <div>
                            <label class="text-sm font-medium text-gray-600">Note</label>
                            <div class="text-sm text-gray-700 leading-relaxed mt-1">{{ $certificate['notes'] }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Continue with Annexes, On-chain, Actions... --}}

                {{-- Annexes Section --}}
                @if($certificate['has_annexes'])
                <div class="border-t border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Annessi Professionali</h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">

                        {{-- Annex A: Provenienza --}}
                        @if($annexes['provenance'] ?? false)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">A</span>
                                <span class="text-xs text-gray-500">v{{ $annexes['provenance']['version'] }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 mb-1">Provenienza</h3>
                            <div class="text-xs text-gray-600 mb-2">{{ $annexes['provenance']['items_count'] ?? 0 }} documenti</div>
                            <div class="text-xs font-mono text-gray-500 mb-2">SHA-256: {{ Str::limit($annexes['provenance']['hash'], 16) }}...</div>
                            <a href="{{ $annexes['provenance']['download_url'] }}"
                               class="text-xs text-blue-600 hover:underline">scarica →</a>
                        </div>
                        @endif

                        {{-- Annex B: Condition --}}
                        @if($annexes['condition'] ?? false)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">B</span>
                                <span class="text-xs text-gray-500">v{{ $annexes['condition']['version'] }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 mb-1">Condition Report</h3>
                            <div class="text-xs text-gray-600 mb-2">{{ $annexes['condition']['items_count'] ?? 0 }} rapporti</div>
                            <div class="text-xs font-mono text-gray-500 mb-2">SHA-256: {{ Str::limit($annexes['condition']['hash'], 16) }}...</div>
                            <a href="{{ $annexes['condition']['download_url'] }}"
                               class="text-xs text-blue-600 hover:underline">scarica →</a>
                        </div>
                        @endif

                        {{-- Annex C: Exhibitions --}}
                        @if($annexes['exhibitions'] ?? false)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-medium">C</span>
                                <span class="text-xs text-gray-500">v{{ $annexes['exhibitions']['version'] }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 mb-1">Mostre/Pubblicazioni</h3>
                            <div class="text-xs text-gray-600 mb-2">{{ $annexes['exhibitions']['items_count'] ?? 0 }} eventi</div>
                            <div class="text-xs font-mono text-gray-500 mb-2">SHA-256: {{ Str::limit($annexes['exhibitions']['hash'], 16) }}...</div>
                            <a href="{{ $annexes['exhibitions']['download_url'] }}"
                               class="text-xs text-blue-600 hover:underline">scarica →</a>
                        </div>
                        @endif

                        {{-- Annex D: Photos --}}
                        @if($annexes['photos'] ?? false)
                                                {{-- Annex D: Photos --}}
                        @if($annexes['photos'] ?? false)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium">D</span>
                                <span class="text-xs text-gray-500">v{{ $annexes['photos']['version'] }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 mb-1">Fotografie Aggiuntive</h3>
                            <div class="text-xs text-gray-600 mb-2">{{ $annexes['photos']['items_count'] ?? 0 }} immagini</div>
                            <div class="text-xs font-mono text-gray-500 mb-2">SHA-256: {{ Str::limit($annexes['photos']['hash'], 16) }}...</div>
                            <a href="{{ $annexes['photos']['download_url'] }}"
                               class="text-xs text-blue-600 hover:underline">scarica →</a>
                        </div>
                        @endif

                        {{-- Annex E: Authorization (NEW) --}}
                        @if($annexes['authorization'] ?? false)
                        <div id="annex-e" class="border-2 border-amber-200 rounded-lg p-4 bg-amber-50 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded text-xs font-medium">E</span>
                                <span class="text-xs text-gray-500">v{{ $annexes['authorization']['version'] }}</span>
                            </div>
                            <h3 class="font-medium text-gray-900 mb-1 flex items-center">
                                <svg class="w-4 h-4 text-amber-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Documentazione Autorizzazione
                            </h3>
                            <div class="text-xs text-gray-600 mb-2">
                                {{ $annexes['authorization']['data']['authorization_type'] ?? 'Tipo autorizzazione non specificato' }}
                            </div>
                            <div class="text-xs text-gray-600 mb-2">
                                {{ $annexes['authorization']['items_count'] ?? 0 }} documenti
                            </div>
                            <div class="text-xs font-mono text-gray-500 mb-2">SHA-256: {{ Str::limit($annexes['authorization']['hash'], 16) }}...</div>
                            <div class="flex space-x-2">
                                <a href="{{ $annexes['authorization']['download_url'] }}"
                                   class="text-xs text-blue-600 hover:underline">scarica documenti →</a>
                                @if($annexes['authorization']['data']['legal_verification_url'] ?? false)
                                <a href="{{ $annexes['authorization']['data']['legal_verification_url'] }}"
                                   class="text-xs text-green-600 hover:underline">verifica legale →</a>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                @endif

                {{-- On-chain Information (if applicable) --}}
                @if($certificate['blockchain_info'])
                <div class="border-t border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Informazioni On-chain</h2>
                    <div class="grid md:grid-cols-3 gap-6">

                        <div>
                            <label class="text-sm font-medium text-gray-600">Rete</label>
                            <div class="text-lg text-gray-900">{{ $certificate['blockchain_info']['network'] }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Asset ID</label>
                            <div class="text-lg font-mono text-gray-900">{{ $certificate['blockchain_info']['asset_id'] }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Explorer</label>
                            <a href="{{ $certificate['blockchain_info']['explorer_url'] }}" target="_blank"
                               class="text-lg text-blue-600 hover:underline">Visualizza su blockchain →</a>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Actions Section --}}
                <div class="border-t border-gray-200 p-8">
                    <div class="flex flex-wrap gap-4 justify-center">

                        {{-- Download PDF --}}
                        <button onclick="downloadPDF()" aria-label="Scarica PDF del certificato"
                                class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Scarica PDF
                        </button>

                        {{-- Download JSON Snapshot --}}
                        <button onclick="downloadJSON()" aria-label="Scarica snapshot JSON del certificato"
                                class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            JSON Snapshot
                        </button>

                        {{-- Print --}}
                        <button onclick="window.print()" aria-label="Stampa certificato"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zM5 14H4v-2h1v2zm1 0v2h8v-2H6zM15 12h1v2h-1v-2z" clip-rule="evenodd"/>
                            </svg>
                            Stampa
                        </button>

                        {{-- API Verify --}}
                        <button onclick="showAPIVerify()" aria-label="Mostra endpoint API per verifica"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            API Verify
                        </button>

                        {{-- Share Link --}}
                        <button onclick="shareLink()" aria-label="Condividi link del certificato"
                                class="bg-amber-500 hover:bg-amber-600 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>
                            </svg>
                            Condividi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="text-center mt-8 text-amber-700">
            <p class="text-sm">Powered by FlorenceEGI</p>
            <p class="text-xs">Timestamp di Verifica: {{ now()->format('d/m/Y H:i:s') }}</p>
            @if($certificate['version'] > 1)
            <p class="text-xs">Versione CoA: {{ $certificate['version'] }} (riemesso)</p>
            @endif
        </div>

        {{-- API Verify Modal --}}
        <div id="apiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">API Verify Endpoint</h3>
                        <button onclick="closeAPIModal()" class="text-gray-500 hover:text-gray-700">✕</button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Endpoint</label>
                            <code class="block bg-gray-100 p-2 rounded text-sm">{{ url('/api/coa/verify/' . $certificate['serial']) }}</code>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Esempio cURL</label>
                            <pre class="bg-gray-100 p-3 rounded text-xs overflow-x-auto"><code>curl -X GET "{{ url('/api/coa/verify/' . $certificate['serial']) }}" \
     -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copy hash function
function copyHash() {
    const hash = '{{ $certificate['verification_hash'] }}';
    navigator.clipboard.writeText(hash).then(() => {
        alert('Hash copiato negli appunti!');
    });
}

// Download functions
function downloadPDF() {
    window.location.href = '{{ route('coa.bundle', ['coa' => $certificate['id']]) }}';
}

function downloadJSON() {
    window.location.href = '{{ route('coa.verify.certificate', $certificate['serial']) }}';
}

// Share function
function shareLink() {
    if (navigator.share) {
        navigator.share({
            title: 'Certificate of Authenticity - {{ $certificate['serial'] }}',
            text: 'Verified Certificate of Authenticity for {{ $artwork['name'] }}',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copiato negli appunti!');
        });
    }
}

// API Modal functions
function showAPIVerify() {
    const modal = document.getElementById('apiModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

function closeAPIModal() {
    const modal = document.getElementById('apiModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}
</script>
