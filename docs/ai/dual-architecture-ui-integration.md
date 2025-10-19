# EGI Dual Architecture - UI Integration Guide

**Version:** 1.0  
**Date:** 2025-10-19  
**Author:** Padmin D. Curtis (AI Partner OS3.0)

---

## 📋 Overview

Questa guida spiega come integrare i componenti UI dell'architettura duale nella pagina `egis.show`.

---

## 🎨 Componenti Blade Creati

### 1. `x-egi-type-badge`
**Badge tipo EGI** - Conforme Brand Guidelines FlorenceEGI

```blade
<x-egi-type-badge :type="$egi->egi_type" size="md" />
```

**Colori:**
- ASA: Blu Algoritmo (#1B365D)
- SmartContract: Viola Innovazione (#8E44AD) con glow
- PreMint: Oro Fiorentino (#D4A574)

---

### 2. `x-egi-living-panel`
**Dashboard EGI Vivente** - Solo per `egi_type = 'SmartContract'`

```blade
@if($egi->egi_type === 'SmartContract' && $egi->smartContract)
    <x-egi-living-panel :egi="$egi" />
@endif
```

**Features:**
- Success rate AI (grafico)
- Prossimo trigger
- Totale analisi
- Link Algorand Explorer
- Info abbonamento

---

### 3. `x-egi-pre-mint-panel`
**Pannello Pre-Mint** - Solo per `egi_type = 'PreMint'`

```blade
@if($egi->egi_type === 'PreMint' && $egi->pre_mint_mode)
    <x-egi-pre-mint-panel :egi="$egi" />
@endif
```

**Features:**
- Countdown scadenza
- Bottoni analisi AI (descrizione, traits, promozione)
- Promozione a ASA o SmartContract

---

### 4. `x-egi-auto-mint-panel`
**Auto-Mint Creator** - Solo per creator del PreMint

```blade
@if($egi->egi_type === 'PreMint' && $egi->user_id === Auth::id())
    <x-egi-auto-mint-panel :egi="$egi" :isCreator="true" />
@endif
```

**Features:**
- Abilita/Disabilita Auto-Mint
- Scelta mint ASA o SmartContract
- Pricing visibile per SmartContract

---

## 🔧 Integrazione in `egis/show.blade.php`

### Esempio Completo

```blade
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header con Badge Tipo --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">{{ $egi->title }}</h1>
        <x-egi-type-badge :type="$egi->egi_type" size="lg" />
    </div>

    {{-- Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Colonna Principale --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Immagine EGI --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}" class="w-full">
            </div>

            {{-- Descrizione e Info --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Descrizione</h2>
                <p class="text-gray-700">{{ $egi->description }}</p>
            </div>
        </div>

        {{-- Colonna Sidebar --}}
        <div class="space-y-6">
            {{-- Pannello Auto-Mint (Solo Creator di PreMint) --}}
            @if($egi->egi_type === 'PreMint' && $egi->user_id === Auth::id())
                <x-egi-auto-mint-panel :egi="$egi" :isCreator="true" />
            @endif

            {{-- Pannello Pre-Mint (Tutti i PreMint) --}}
            @if($egi->egi_type === 'PreMint' && $egi->pre_mint_mode)
                <x-egi-pre-mint-panel :egi="$egi" />
            @endif

            {{-- Pannello EGI Vivente (Solo SmartContract) --}}
            @if($egi->egi_type === 'SmartContract' && $egi->smartContract)
                <x-egi-living-panel :egi="$egi" />
            @endif

            {{-- Info Base EGI (Tutti) --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold mb-4">Informazioni</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Creator:</dt>
                        <dd class="font-semibold">{{ $egi->user->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Creato il:</dt>
                        <dd class="font-semibold">{{ $egi->created_at->format('d/m/Y') }}</dd>
                    </div>
                    @if($egi->token_EGI)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">ASA ID:</dt>
                            <dd class="font-mono text-xs">{{ $egi->token_EGI }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 🎨 Brand Guidelines Compliance

### Colori Utilizzati

✅ **Oro Fiorentino** `#D4A574` - PreMint, Premium labels  
✅ **Verde Rinascita** `#2D5016` - Auto-Mint, Success states  
✅ **Blu Algoritmo** `#1B365D` - ASA, Blockchain tech  
✅ **Viola Innovazione** `#8E44AD` - SmartContract, AI features  
✅ **Grigio Pietra** `#6B6B6B` - Testi secondari

### Tipografia

✅ **Playfair Display** - Titoli principali  
✅ **Source Sans Pro** - Corpo testi  
✅ **JetBrains Mono** - Codici tecnici (App ID, Hash)

### Principi UI/UX

✅ **Spazi generosi** - Padding 8px multipli  
✅ **Bordi arrotondati** - rounded-xl, rounded-2xl  
✅ **Gradienti eleganti** - from-to con transizioni smooth  
✅ **Shadows** - shadow-lg, shadow-xl per profondità  
✅ **Hover states** - Transizioni 200ms duration  
✅ **ARIA labels** - Accessibilità completa

---

## 🔌 Livewire Actions Required

Ogni componente richiede metodi Livewire nel controller `EgiShowController`:

```php
// Auto-Mint
public function enableAutoMint()
public function disableAutoMint()
public function openCreatorMintModal($type)

// Pre-Mint AI
public function requestAIDescription()
public function requestAITraits()
public function requestAIPromotion()
public function openPromoteModal($type)

// Living Panel
public function triggerAIAnalysis()
```

---

## 📱 Responsive Design

Tutti i componenti sono responsive:
- **Mobile**: Colonna singola
- **Tablet**: Grid 2 colonne ove appropriato
- **Desktop**: Grid 3 colonne con sidebar

---

## ✅ Checklist Integrazione

- [ ] Importare componenti Blade in `egis/show.blade.php`
- [ ] Implementare metodi Livewire richiesti
- [ ] Testare su mobile/tablet/desktop
- [ ] Verificare conformità Brand Guidelines
- [ ] Testare accessibilità (ARIA, contrasti, navigazione)
- [ ] Feature flags abilitati correttamente

---

**FlorenceEGI - Dove l'arte diventa valore virtuoso**

