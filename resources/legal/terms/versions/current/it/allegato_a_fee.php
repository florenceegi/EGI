<?php

/**
 * @Oracode Legal Content: Allegato A — Tabella delle Fee Dinamiche
 * 🎯 Purpose: Detailed fee structure for Creator and Company transactions
 * 🛡️ Security: Transparent pricing, compliance with ToS v3.0.0
 *
 * @version 3.0.0
 * @effective_date 2026-02-24
 * @locale it
 *
 * Referenziato da:
 * - creator.php Art. 5.1, 5.2, 5.5, 11
 * - collector.php Art. 3.2, 5.3, 5.4
 *
 * Fonte dati verificata:
 * - app/Enums/Fees/FeeStructureEnum.php
 * - docs/FlorenceEGI/04_Gestione_Pagamenti.md
 * - docs/FlorenceEGI/10_Rebind_Logic_Reference.md
 *
 * @package FlorenceEGI\Legal
 * @author Padmin D. Curtis (AI Partner OS3.0-Compliant) for Fabio Cherici
 */

return [
    'metadata' => [
        'title' => 'Allegato A — Tabella delle Fee Dinamiche',
        'version' => '3.0.0',
        'effective_date' => '2026-02-24',
        'document_type' => 'Allegato ai Termini e Condizioni',
        'referenced_by' => ['creator.php', 'collector.php'],
    ],

    'introduzione' => [
        'title' => 'Introduzione',
        'content' => 'Il presente Allegato definisce la struttura delle commissioni (fee) applicate alle transazioni sulla Piattaforma FlorenceEGI. Le percentuali indicate sono calcolate sul prezzo di vendita in Euro. La scelta del Profilo Collection determina la ripartizione dei ricavi.',
    ],

    'sections' => [
        [
            'number' => 1,
            'title' => 'Profili Collection',
            'content' => 'FlorenceEGI distingue due profili di Collection, ciascuno con una struttura di fee differente:

**Profilo CONTRIBUTOR (Modello Etico)**
- Accessibile a: Creator e Company
- Nessun costo di abbonamento
- Include contributi obbligatori a EPP e Frangette (associazione partner per lo sviluppo sociale)

**Profilo NORMAL (Modello Business)**
- Accessibile a: Solo Company
- Richiede abbonamento attivo
- Esente da contributi EPP e Frangette
- Royalty netta più alta per il venditore',
        ],
        [
            'number' => 2,
            'title' => 'Fee Mint (Mercato Primario)',
            'subsections' => [
                [
                    'number' => '2.1',
                    'title' => 'Profilo CONTRIBUTOR — Mint',
                    'content' => 'Ripartizione del prezzo di vendita per nuovi EGI creati con profilo Contributor:

| Destinatario | Percentuale | Descrizione |
|---|---|---|
| **Creator/Company** | **68%** | Royalty netta al venditore |
| **EPP** | **20%** | Contributo obbligatorio al progetto ambientale selezionato |
| **Piattaforma (Natan)** | **10%** | Commissione per infrastruttura tecnologica |
| **Frangette** | **2%** | Contributo obbligatorio per sviluppo sociale |
| **Abbonamento** | **Nessuno** | Profilo gratuito |

*Esempio: Per un EGI venduto a €100, il Creator riceve €68, l\'EPP riceve €20, la Piattaforma €10 e Frangette €2.*',
                ],
                [
                    'number' => '2.2',
                    'title' => 'Profilo NORMAL — Mint',
                    'content' => 'Ripartizione del prezzo di vendita per nuovi EGI creati con profilo Normal:

| Destinatario | Percentuale | Descrizione |
|---|---|---|
| **Company** | **90%** | Royalty netta al venditore |
| **EPP** | **0%** | Esente |
| **Piattaforma (Natan)** | **10%** | Commissione per infrastruttura tecnologica |
| **Frangette** | **0%** | Esente |
| **Abbonamento** | **Obbligatorio** | Richiesto per mantenere la Collection attiva |

*Esempio: Per un EGI venduto a €100, la Company riceve €90 e la Piattaforma €10.*',
                ],
            ],
        ],
        [
            'number' => 3,
            'title' => 'Fee Rebind (Mercato Secondario)',
            'subsections' => [
                [
                    'number' => '3.1',
                    'title' => 'Profilo CONTRIBUTOR — Rebind',
                    'content' => 'Ripartizione del prezzo di rivendita per EGI da Collection con profilo Contributor:

| Destinatario | Percentuale | Descrizione |
|---|---|---|
| **Venditore** | **93%** | Importo netto al venditore (residuo) |
| **Creator originale** | **4,5%** | Royalty contrattuale permanente |
| **EPP** | **1,5%** | Contributo ambientale su rivendita |
| **Piattaforma (Natan)** | **0,8%** | Commissione di servizio |
| **Frangette** | **0,2%** | Contributo sociale |
| **TOTALE FEE** | **7%** | Detratto dal prezzo di vendita |

*Esempio: Per un EGI rivenduto a €1.000, il Venditore riceve €930, il Creator €45, l\'EPP €15, la Piattaforma €8 e Frangette €2.*',
                ],
                [
                    'number' => '3.2',
                    'title' => 'Profilo NORMAL — Rebind',
                    'content' => 'Ripartizione del prezzo di rivendita per EGI da Collection con profilo Normal:

| Destinatario | Percentuale | Descrizione |
|---|---|---|
| **Venditore** | **93%** | Importo netto al venditore (residuo) |
| **Creator originale** | **6%** | Royalty contrattuale permanente |
| **EPP** | **0%** | Esente |
| **Piattaforma (Natan)** | **1%** | Commissione di servizio |
| **Frangette** | **0%** | Esente |
| **TOTALE FEE** | **7%** | Detratto dal prezzo di vendita |

*Esempio: Per un EGI rivenduto a €1.000, il Venditore riceve €930, il Creator €60 e la Piattaforma €10.*',
                ],
            ],
        ],
        [
            'number' => 4,
            'title' => 'Eccezione Commodity (Asset Materiali)',
            'content' => 'Per gli EGI classificati come Commodity (es. Gold Bars, asset materiali):

**Mint**:
- La Fee di Piattaforma (10%) si applica solo sul **margine aziendale** (markup), non sull\'intero prezzo dell\'asset. Il costo vivo della materia prima è esente da fee.

**Rebind**:
- Il venditore paga una **fee fissa di 50 Egili** per il servizio di rivendita
- Le royalties percentuali (Creator, EPP, Natan, Frangette) **non si applicano**
- Il venditore riceve il 100% del prezzo di mercato, meno i 50 Egili di servizio',
        ],
        [
            'number' => 5,
            'title' => 'Fee Dinamiche e Sconti Volume',
            'content' => 'FlorenceEGI si riserva di introdurre meccanismi di riduzione progressiva delle commissioni basati su:

- **Volume cumulativo di vendite**: Riduzione delle fee di piattaforma al raggiungimento di soglie di volume
- **Programma Egili**: Utilizzo di Egili per ridurre o azzerare le commissioni di piattaforma (vedi Allegato B)
- **Accordi personalizzati**: Per Company con volumi significativi, è possibile negoziare fee personalizzate

Le condizioni specifiche saranno comunicate nella dashboard e nel listino aggiornato disponibile nella sezione Impostazioni.',
        ],
        [
            'number' => 6,
            'title' => 'Riepilogo Comparativo',
            'content' => '| Aspetto | Profilo CONTRIBUTOR | Profilo NORMAL |
|---|---|---|
| **Target** | Creator e Company (etiche) | Solo Company |
| **Abbonamento** | Gratuito | Obbligatorio |
| **Royalty netta Mint** | 68% | 90% |
| **Contributo EPP (Mint)** | 20% | 0% |
| **Frangette (Mint)** | 2% | 0% |
| **Fee Piattaforma (Mint)** | 10% | 10% |
| **Creator Royalty (Rebind)** | 4,5% | 6% |
| **Totale Fee Rebind** | 7% | 7% |
| **Commodity Rebind** | 50 Egili fissi | 50 Egili fissi |',
        ],
        [
            'number' => 7,
            'title' => 'Note Importanti',
            'content' => '• Tutte le percentuali sono calcolate sul prezzo di vendita lordo in Euro
• Per pagamenti in ALGO o criptovaluta, il controvalore in Euro al momento della transazione determina le percentuali
• Le fee di rete blockchain (gas fee Algorand) sono a carico dell\'acquirente e non sono incluse nelle percentuali sopra indicate
• Le commissioni dei PSP (Stripe, PayPal) sono separate e aggiuntive rispetto alle fee di piattaforma
• FlorenceEGI si riserva il diritto di modificare le percentuali con un preavviso di 30 giorni. Le modifiche non hanno effetto retroattivo sulle transazioni già concluse
• La royalty del Creator originale (4,5% o 6% su Rebind) è permanente e inalterabile: si applica a ogni rivendita, senza limiti temporali',
        ],
    ],
];
