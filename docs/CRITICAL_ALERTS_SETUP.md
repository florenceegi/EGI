# 🚨 Critical Alert System - Configuration Guide

## Overview

Sistema di allerta multi-canale per errori critici della piattaforma (wallet Natan/Frangette senza Stripe, ecc.)

## Canali di Notifica

### 1️⃣ **Email Alert** ✅ ATTIVO

-   Invia email dettagliata all'admin
-   Include context completo dell'errore
-   Timestamp e severity level

### 2️⃣ **SMS Alert** 🔜 DA CONFIGURARE

-   SMS breve (max 160 caratteri)
-   Richiede provider SMS (Twilio, Nexmo, ecc.)
-   **STATUS**: Codice pronto, provider da integrare

### 3️⃣ **EGI-HUB Notification** 🔜 FUTURO

-   Notifica real-time in EGI-HUB dashboard
-   WebSocket o push notification
-   **STATUS**: Placeholder nel codice

---

## Configurazione .env

Aggiungi al file `.env`:

```bash
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# Critical Alerts Configuration
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Email admin per allerte critiche (RICHIESTO)
CRITICAL_ALERT_EMAIL=admin@florenceegi.it

# Telefono admin per SMS (formato E.164: +39...)
CRITICAL_ALERT_PHONE=+393331234567

# Provider SMS da utilizzare (twilio, nexmo, etc.)
SMS_PROVIDER=null

# Abilita invio SMS (true/false)
SMS_ALERTS_ENABLED=false
```

---

## Test Sistema

### Test Email

```bash
php artisan tinker

# In Tinker:
app(\App\Services\CriticalAlertService::class)->sendCriticalAlert(
    'TEST_ALERT',
    ['test' => true, 'timestamp' => now()],
    'warning'
);
```

### Test Completo (Simula Missing Stripe)

```bash
# Nel codice, triggera l'errore MINT_MISSING_STRIPE_ACCOUNTS
# Il sistema invierà automaticamente:
# - Email all'admin
# - Log UEM
# - SMS (se configurato)
```

---

## Errori Monitorati

### 🔴 **MINT_MISSING_STRIPE_ACCOUNTS** (CRITICAL)

**Trigger**: Wallet piattaforma (Natan/Frangette) senza Stripe Connect

**Azioni automatiche**:

1. ✅ Log UEM con context completo
2. ✅ Email dettagliata all'admin
3. 🔜 SMS breve all'admin (se attivo)
4. ❌ Blocca split payment (throw Exception)

**Esempio context**:

```json
{
    "missing_accounts": [
        { "wallet_id": 15, "platform_role": "Natan" },
        { "wallet_id": 14, "platform_role": "Frangette" }
    ],
    "missing_count": 2,
    "platform_wallets_affected": ["Natan", "Frangette"],
    "timestamp": "2026-01-16T18:25:00+00:00"
}
```

---

## Come Risolvere

### 1. Verifica Wallet Stripe

```bash
php artisan tinker

# Controlla wallet Natan (ID 15)
$wallet = \App\Models\Wallet::find(15);
$wallet->stripe_account_id; // Deve essere NON NULL

# Controlla wallet Frangette (ID 14)
$wallet = \App\Models\Wallet::find(14);
$wallet->stripe_account_id; // Deve essere NON NULL
```

### 2. Configura Stripe Connect

Se manca `stripe_account_id`:

1. **Crea Stripe Connect Account** per il wallet:

    - Via Stripe Dashboard: https://dashboard.stripe.com/connect/accounts
    - O via API Stripe

2. **Associa al Wallet**:

```php
$wallet = \App\Models\Wallet::find(15);
$wallet->stripe_account_id = 'acct_XXXXXXXXXXXXXXXX';
$wallet->save();
```

3. **Verifica Capabilities**:

```bash
# Check che l'account abbia transfers enabled
```

---

## Estendere il Sistema

### Aggiungere Nuovo Tipo di Alert

1. **Aggiungi error code** in `config/error-manager.php`

2. **Trigger alert** nel servizio:

```php
$this->criticalAlertService->sendCriticalAlert(
    'NUOVO_ERROR_CODE',
    ['context' => 'dati rilevanti'],
    'critical' // o 'error', 'warning'
);
```

3. **Personalizza messaggio SMS** in `CriticalAlertService::formatSmsMessage()`

---

## Provider SMS - Future Implementation

### Twilio Integration (Esempio)

```php
// In CriticalAlertService::sendSmsAlert()
if ($smsProvider === 'twilio') {
    $client = new \Twilio\Rest\Client(
        config('services.twilio.sid'),
        config('services.twilio.token')
    );

    $client->messages->create($adminPhone, [
        'from' => config('services.twilio.from'),
        'body' => $message
    ]);
}
```

Configurazione `.env`:

```bash
TWILIO_SID=ACxxxxxxxxxxxxx
TWILIO_TOKEN=your_token
TWILIO_FROM=+15551234567
```

---

## Monitoring

### Log Location

```bash
# UEM Logs
tail -f storage/logs/laravel.log | grep "MINT_MISSING_STRIPE"

# Critical Alert Logs
tail -f storage/logs/laravel.log | grep "Critical Alert"
```

### Metriche da Monitorare

-   Frequenza alert `MINT_MISSING_STRIPE_ACCOUNTS`
-   Delivery rate email
-   Tempo medio risoluzione issue
-   Wallet senza Stripe Connect

---

## Support

**Contatti**:

-   Email: admin@florenceegi.it
-   Sistema: Florence EGI Platform
-   Documentazione: `/docs/critical-alerts/`

**Version**: 1.0.0
**Last Update**: 2026-01-16
