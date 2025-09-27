# 🏦 QES Sandbox Providers Implementation Plan

## 🎯 OBIETTIVO

Implementare provider QES reali in modalità sandbox per:

-   **Dimostrazioni investitori** con firme reali
-   **Testing realistico** senza costi
-   **Validazione architettura** con provider reali
-   **Preparazione produzione** con switch semplice

---

## 🏢 PROVIDER SANDBOX IDENTIFICATI

### 1. **Namirial** (Italia)

-   **Sandbox**: Disponibile
-   **CSC API**: Supportato
-   **PAdES**: B-LT supportato
-   **TSA**: Integrato
-   **Costo sandbox**: Gratuito
-   **Documentazione**: Completa

### 2. **InfoCert** (Italia)

-   **Sandbox**: Disponibile
-   **CSC API**: Supportato
-   **PAdES**: B-LT supportato
-   **TSA**: Integrato
-   **Costo sandbox**: Gratuito
-   **Documentazione**: Completa

### 3. **Aruba** (Italia)

-   **Sandbox**: Disponibile
-   **CSC API**: Supportato
-   **PAdES**: B-LT supportato
-   **TSA**: Integrato
-   **Costo sandbox**: Gratuito
-   **Documentazione**: Buona

### 4. **Intesi** (Italia)

-   **Sandbox**: Disponibile
-   **CSC API**: Supportato
-   **PAdES**: B-LT supportato
-   **TSA**: Integrato
-   **Costo sandbox**: Gratuito
-   **Documentazione**: Buona

---

## 🏗️ ARCHITETTURA IMPLEMENTAZIONE

### 1. **Adapter Pattern**

```php
interface SignatureProviderInterface {
    public function signPdf(string $pdfPath, array $options): array;
    public function addCountersignature(string $signedPdf, array $options): array;
    public function addTimestamp(string $signedPdf, array $options): array;
    public function verifySignatures(string $pdfPath): array;
}

class NamirialProvider implements SignatureProviderInterface { }
class InfoCertProvider implements SignatureProviderInterface { }
class ArubaProvider implements SignatureProviderInterface { }
class IntesiProvider implements SignatureProviderInterface { }
```

### 2. **Factory Pattern**

```php
class SignatureProviderFactory {
    public static function create(string $provider, array $config): SignatureProviderInterface {
        return match($provider) {
            'namirial' => new NamirialProvider($config),
            'infocert' => new InfoCertProvider($config),
            'aruba' => new ArubaProvider($config),
            'intesi' => new IntesiProvider($config),
            'mock' => new MockSignatureProvider($config),
            default => throw new InvalidArgumentException("Provider {$provider} not supported")
        };
    }
}
```

### 3. **Configuration Management**

```php
// config/coa.php
'signature' => [
    'enabled' => env('COA_SIGNATURE_ENABLED', true),
    'provider' => env('COA_SIGNATURE_PROVIDER', 'mock'), // mock, namirial, infocert, aruba, intesi
    'sandbox' => [
        'namirial' => [
            'api_url' => env('NAMIRIAL_SANDBOX_URL'),
            'client_id' => env('NAMIRIAL_CLIENT_ID'),
            'client_secret' => env('NAMIRIAL_CLIENT_SECRET'),
            'certificate_id' => env('NAMIRIAL_CERTIFICATE_ID'),
        ],
        'infocert' => [
            'api_url' => env('INFOCERT_SANDBOX_URL'),
            'client_id' => env('INFOCERT_CLIENT_ID'),
            'client_secret' => env('INFOCERT_CLIENT_SECRET'),
            'certificate_id' => env('INFOCERT_CERTIFICATE_ID'),
        ],
        // ... altri provider
    ]
]
```

---

## 📋 PIANO IMPLEMENTAZIONE

### **FASE 1: Setup Provider (1-2 settimane)**

#### 1.1 Registrazione Sandbox

-   [ ] **Namirial**: Registrazione account sandbox
-   [ ] **InfoCert**: Registrazione account sandbox
-   [ ] **Aruba**: Registrazione account sandbox
-   [ ] **Intesi**: Registrazione account sandbox
-   [ ] **Credenziali**: Ottenimento API keys e certificati
-   [ ] **Documentazione**: Studio API e limitazioni sandbox

#### 1.2 Configurazione Ambiente

-   [ ] **Environment variables**: Setup .env per sandbox
-   [ ] **Certificati**: Installazione certificati sandbox
-   [ ] **Network**: Configurazione firewall e proxy
-   [ ] **SSL**: Configurazione certificati SSL

### **FASE 2: Implementazione Adapter (2-3 settimane)**

#### 2.1 Namirial Adapter

-   [ ] **CSC API**: Implementazione client CSC
-   [ ] **Authentication**: OAuth2 flow
-   [ ] **Sign PDF**: Implementazione firma PAdES
-   [ ] **Countersign**: Implementazione co-firma
-   [ ] **Timestamp**: Implementazione TSA
-   [ ] **Verification**: Implementazione verifica firme

#### 2.2 InfoCert Adapter

-   [ ] **CSC API**: Implementazione client CSC
-   [ ] **Authentication**: OAuth2 flow
-   [ ] **Sign PDF**: Implementazione firma PAdES
-   [ ] **Countersign**: Implementazione co-firma
-   [ ] **Timestamp**: Implementazione TSA
-   [ ] **Verification**: Implementazione verifica firme

#### 2.3 Aruba Adapter

-   [ ] **CSC API**: Implementazione client CSC
-   [ ] **Authentication**: OAuth2 flow
-   [ ] **Sign PDF**: Implementazione firma PAdES
-   [ ] **Countersign**: Implementazione co-firma
-   [ ] **Timestamp**: Implementazione TSA
-   [ ] **Verification**: Implementazione verifica firme

#### 2.4 Intesi Adapter

-   [ ] **CSC API**: Implementazione client CSC
-   [ ] **Authentication**: OAuth2 flow
-   [ ] **Sign PDF**: Implementazione firma PAdES
-   [ ] **Countersign**: Implementazione co-firma
-   [ ] **Timestamp**: Implementazione TSA
-   [ ] **Verification**: Implementazione verifica firme

### **FASE 3: Integrazione Sistema (1-2 settimane)**

#### 3.1 Factory Pattern

-   [ ] **ProviderFactory**: Implementazione factory
-   [ ] **Configuration**: Gestione configurazione provider
-   [ ] **Error Handling**: Gestione errori specifici provider
-   [ ] **Logging**: Logging specifico per provider

#### 3.2 Service Layer

-   [ ] **SignatureService**: Aggiornamento per provider reali
-   [ ] **Fallback**: Implementazione fallback mock
-   [ ] **Retry Logic**: Implementazione retry per errori temporanei
-   [ ] **Caching**: Caching certificati e token

#### 3.3 UI/UX

-   [ ] **Provider Selection**: Dropdown selezione provider
-   [ ] **Status Indicators**: Indicatori stato provider
-   [ ] **Error Messages**: Messaggi errore specifici provider
-   [ ] **Demo Mode**: Modalità demo per investitori

### **FASE 4: Testing & Validazione (2-3 settimane)**

#### 4.1 Unit Tests

-   [ ] **Provider Tests**: Test unitari per ogni provider
-   [ ] **Factory Tests**: Test factory pattern
-   [ ] **Configuration Tests**: Test configurazione
-   [ ] **Error Handling Tests**: Test gestione errori

#### 4.2 Integration Tests

-   [ ] **End-to-End**: Test flusso completo con provider reali
-   [ ] **Performance**: Test performance con provider reali
-   [ ] **Reliability**: Test affidabilità e retry
-   [ ] **Security**: Test sicurezza con provider reali

#### 4.3 Demo Preparation

-   [ ] **Demo Scripts**: Script per dimostrazioni
-   [ ] **Demo Data**: Dati di test per demo
-   [ ] **Demo Environment**: Ambiente dedicato demo
-   [ ] **Demo Documentation**: Documentazione per demo

---

## 🎯 DEMO PER INVESTITORI

### **Scenario Demo**

1. **CoA Creation**: Creazione certificato con dati reali
2. **Author Signature**: Firma autore con provider reale
3. **Inspector Countersign**: Co-firma ispettore
4. **PDF Verification**: Verifica firme in Acrobat
5. **Chain of Custody**: Visualizzazione audit trail
6. **Provider Switch**: Cambio provider in tempo reale

### **Provider Demo**

-   **Namirial**: Provider principale per demo
-   **InfoCert**: Provider alternativo per switch
-   **Mock**: Fallback per errori di rete

### **Demo Features**

-   **Real-time**: Firma in tempo reale
-   **Visual Feedback**: Indicatori stato firma
-   **Error Recovery**: Gestione errori elegante
-   **Performance**: Firma rapida (< 5 secondi)

---

## 📊 STIMA TEMPI E COSTI

### **Tempi**

-   **Fase 1**: 1-2 settimane
-   **Fase 2**: 2-3 settimane
-   **Fase 3**: 1-2 settimane
-   **Fase 4**: 2-3 settimane
-   **Totale**: 6-10 settimane

### **Costi**

-   **Sviluppatore Senior**: €15,000 - €25,000
-   **Sandbox**: Gratuito
-   **Infrastruttura**: €500 - €1,000
-   **Totale**: €15,500 - €26,000

### **ROI**

-   **Demo Investitori**: Valore inestimabile
-   **Validazione Architettura**: Riduzione rischi
-   **Preparazione Produzione**: Accelerazione go-live
-   **Competitive Advantage**: Differenziazione

---

## 🚨 RISCHI E MITIGAZIONI

### **Rischi Tecnici**

-   **API Changes**: Versionamento API e backward compatibility
-   **Rate Limiting**: Implementazione retry e backoff
-   **Network Issues**: Fallback e error handling
-   **Certificate Expiry**: Gestione rinnovo certificati

### **Rischi di Processo**

-   **Sandbox Availability**: Provider multipli per ridondanza
-   **Documentation Quality**: Studio approfondito documentazione
-   **Support Response**: Contatti diretti con supporto provider
-   **Compliance Changes**: Monitoraggio aggiornamenti normativi

### **Mitigazioni**

-   **Provider Multipli**: Ridondanza e fallback
-   **Mock Fallback**: Sempre disponibile
-   **Comprehensive Testing**: Test approfonditi
-   **Documentation**: Documentazione completa

---

## 📋 DELIVERABLES

### **Codice**

-   [ ] Adapter per 4 provider sandbox
-   [ ] Factory pattern per selezione provider
-   [ ] Configuration management
-   [ ] Error handling e retry logic
-   [ ] Unit e integration tests

### **Documentazione**

-   [ ] Setup guide per provider sandbox
-   [ ] API documentation per ogni provider
-   [ ] Demo scripts e procedures
-   [ ] Troubleshooting guide
-   [ ] Production migration guide

### **Demo**

-   [ ] Demo environment configurato
-   [ ] Demo scripts automatizzati
-   [ ] Demo data preparati
-   [ ] Demo documentation
-   [ ] Video demo per investitori

---

## 🎯 NEXT STEPS

### **Immediati (Questa Settimana)**

1. **Registrazione Sandbox**: Iniziare con Namirial e InfoCert
2. **Studio Documentazione**: Analisi API e limitazioni
3. **Setup Ambiente**: Configurazione environment variables
4. **Piano Dettagliato**: Dettaglio implementazione per ogni provider

### **Prossime 2 Settimane**

1. **Implementazione Adapter**: Iniziare con Namirial
2. **Testing Base**: Test funzionalità base
3. **Error Handling**: Implementazione gestione errori
4. **Documentation**: Documentazione progressi

### **Prossimo Mese**

1. **Completamento Adapter**: Tutti i 4 provider
2. **Integration Testing**: Test integrazione completa
3. **Demo Preparation**: Preparazione demo investitori
4. **Performance Optimization**: Ottimizzazione performance

---

**Data creazione**: 26 settembre 2025  
**Versione**: 1.0  
**Stato**: Draft - Da approvare  
**Priorità**: Alta - Per demo investitori
