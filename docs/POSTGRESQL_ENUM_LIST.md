# 📋 Lista Completa delle Colonne ENUM da Convertire

Questo documento elenca tutte le **131 colonne ENUM** presenti nelle migration che dovranno essere convertite per PostgreSQL.

## Strategia di Conversione

Per ogni ENUM, la strategia consigliata è:
1. **In Production**: Prima eseguire una migration che converte `ENUM` → `VARCHAR`
2. **Validazione**: Usare PHP Enum nel Model per validazione a livello applicazione
3. **Opzionale**: Aggiungere CHECK constraint per validazione a livello database

---

## Elenco per File di Migration

### 1. `0001_01_01_000000_create_users_table.php`
| Colonna | Valori |
|---------|--------|
| `retention_reason` | (vedi file) |

### 2. `2024_01_15_000001_create_consent_types_table.php`
| Colonna | Valori |
|---------|--------|
| `legal_basis` | (multipli valori legali GDPR) |

### 3. `2024_01_15_000002_create_privacy_policies_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | `draft`, `review`, `approved`, `published`, `archived` |

### 4. `2024_01_15_000003_create_data_retention_policies_table.php`
| Colonna | Valori |
|---------|--------|
| `retention_trigger` | (multipli) |
| `deletion_method` | (multipli) |
| `risk_level` | `low`, `medium`, `high`, `critical` |

### 5. `2024_01_15_000003_add_many_columns_in_privacy_polices_table.php`
| Colonna | Valori |
|---------|--------|
| `document_type` | (multipli) |
| `legal_review_status` | (multipli) |

### 6. `2024_01_15_000005_create_gdpr_requests_table.php`
| Colonna | Valori |
|---------|--------|
| `type` | (tipi richiesta GDPR) |
| `status` | (stati richiesta) |

### 7. `2024_01_15_000006_create_data_exports_table.php`
| Colonna | Valori |
|---------|--------|
| `format` | `json`, `csv`, `pdf` |
| `status` | `pending`, `processing`, `completed`, `failed`, `expired` |

### 8. `2024_01_15_000007_create_user_activities_table.php`
| Colonna | Valori |
|---------|--------|
| `category` | (categorie attività) |
| `privacy_level` | `standard`, `high`, `critical`, `immutable` |

### 9. `2024_01_15_000008_create_breach_reports_table.php`
| Colonna | Valori |
|---------|--------|
| `category` | (categorie breach) |
| `severity` | `low`, `medium`, `high`, `critical` |
| `status` | (stati breach) |

### 10. `2024_01_15_000009_create_privacy_policy_acceptances_table.php`
| Colonna | Valori |
|---------|--------|
| `acceptance_type` | (tipi accettazione) |

### 11. `2024_01_15_000011_create_consent_histories_table.php`
| Colonna | Valori |
|---------|--------|
| `action` | (azioni consenso) |

### 12. `2024_01_15_000012_create_anonymized_users_table.php`
| Colonna | Valori |
|---------|--------|
| `anonymization_reason` | (motivi) |
| `anonymization_method` | (metodi) |
| `retention_reason` | (motivi retention) |
| `anonymization_quality` | (livelli qualità) |

### 13. `2024_01_15_000013_create_security_events_table.php`
| Colonna | Valori |
|---------|--------|
| `severity` | `low`, `medium`, `high`, `critical` |
| `status` | `detected`, `investigating`, `resolved`, `false_positive` |

### 14. `2024_01_15_000014_create_dpo_messages_table.php`
| Colonna | Valori |
|---------|--------|
| `priority` | `low`, `normal`, `high`, `urgent` |
| `request_type` | (tipi richiesta DPO) |
| `status` | (stati messaggio) |

### 15. `2024_12_28_131757_create_notification_payload_invitations_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | `pending`, `accepted`, `rejected` |

### 16. `2025_05_02_120944_create_reservations_table.php`
| Colonna | Valori |
|---------|--------|
| `type` | `weak`, `strong` |
| `status` | `active`, `expired`, `completed`, `cancelled`, `withdrawn` |
| `sub_status` | (multipli) |

### 17. `2025_05_16_074109_create_egi_reservation_certificates_table.php`
| Colonna | Valori |
|---------|--------|
| `reservation_type` | `strong`, `weak` |

### 18. `2025_05_26_111141_create_user_domain_tables.php`
| Colonna | Valori |
|---------|--------|
| `gender` | `male`, `female`, `other`, `prefer_not_say` |
| `business_type` | `individual`, `sole_proprietorship`, `partnership`, `corporation`, `non_profit`, `pa_entity` |
| `verification_status` | `pending`, `verified`, `rejected`, `expired` |

### 19. `2025_06_10_125504_create_gdpr_notification_payloads_table.php`
| Colonna | Valori |
|---------|--------|
| `payload_status` | (stati payload) |

### 20. `2025_07_02_162246_create_biographies_table.php`
| Colonna | Valori |
|---------|--------|
| `type` | `single`, `chapters` |

### 21. `2025_08_15_095926_create_notification_payload_reservations_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | `info`, `success`, `warning`, `error`, `pending` |

### 22. `2025_08_20_095612_create_payments_distributions_table.php`
| Colonna | Valori |
|---------|--------|
| `user_type` | (tipi utente) |

### 23. `2025_08_29_192005_create_utilities_table.php`
| Colonna | Valori |
|---------|--------|
| `type` | `physical`, `service`, `hybrid`, `digital` |
| `escrow_tier` | `immediate`, `standard`, `premium` |

### 24. `2025_08_30_080211_create_trait_types_table.php`
| Colonna | Valori |
|---------|--------|
| `display_type` | `text`, `number`, `percentage`, `date`, `boost_number` |

### 25. `2025_09_18_163532_create_coa_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | `valid`, `revoked` |
| `issuer_type` | `author`, `archive`, `platform` |

### 26. `2025_09_18_163549_create_coa_files_table.php`
| Colonna | Valori |
|---------|--------|
| `kind` | `pdf`, `scan_signed`, `image_front`, `image_back`, `signature_detail` |

### 27. `2025_09_18_163557_create_coa_signatures_table.php`
| Colonna | Valori |
|---------|--------|
| `kind` | `qes`, `autograph_scan`, `wallet` |

### 28. `2025_09_18_164915_create_coa_annexes_table.php`
| Colonna | Valori |
|---------|--------|
| `code` | `A_PROVENANCE`, `B_CONDITION`, `C_EXHIBITIONS`, `D_PHOTOS` |

### 29. `2025_09_18_164922_create_coa_events_table.php`
| Colonna | Valori |
|---------|--------|
| `type` | `ISSUED`, `REVOKED`, `ANNEX_ADDED`, `ADDENDUM_ISSUED` |

### 30. `2025_09_18_164933_update_coa_files_kind_enum.php`
| Colonna | Valori |
|---------|--------|
| `kind` | (valori estesi con CoA Pro) |

### 31. `2025_09_19_080416_create_vocabulary_terms_table.php`
| Colonna | Valori |
|---------|--------|
| `category` | `technique`, `materials`, `support` |

### 32. `2025_09_26_150514_update_coa_events_type_enum.php`
| Colonna | Valori |
|---------|--------|
| `type` | (valori aggiornati) |

### 33. `2025_09_26_161732_add_signature_removed_to_coa_events_type_enum.php`
| Colonna | Valori |
|---------|--------|
| `type` | (include `SIGNATURE_REMOVED`) |

### 34. `2025_10_01_085200_add_signed_pdf_types_to_coa_files_kind.php`
| Colonna | Valori |
|---------|--------|
| `kind` | (include tipi PDF firmati) |

### 35. `2025_10_04_190050_add_pa_acts_metadata_to_egis_table.php`
| Colonna | Valori |
|---------|--------|
| `pa_act_type` | (tipi atti PA) |

### 36. `2025_10_07_111351_create_egi_blockchain_table.php`
| Colonna | Valori |
|---------|--------|
| `payment_method` | `stripe`, `paypal`, `bank_transfer`, `mock` |
| `ownership_type` | `treasury`, `wallet` |
| `mint_status` | (stati mint) |

### 37. `2025_10_09_101013_create_egi_acts_table.php`
| Colonna | Valori |
|---------|--------|
| `processing_status` | `pending`, `completed`, `failed` |

### 38. `2025_10_09_105125_extend_payment_distributions_for_mint_tracking.php`
| Colonna | Valori |
|---------|--------|
| `source_type` | `reservation`, `mint`, `transfer` |

### 39. `2025_10_15_000001_add_tokenization_error_tracking_to_egis_table.php`
| Colonna | Valori |
|---------|--------|
| `pa_tokenization_status` | (stati tokenizzazione PA) |

### 40. `2025_10_16_095951_add_blockchain_support_to_egi_reservation_certificates.php`
| Colonna | Valori |
|---------|--------|
| `certificate_type` | `reservation`, `mint` |

### 41. `2025_10_16_100002_create_pa_batch_sources_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | `active`, `paused`, `error` |

### 42. `2025_10_16_100003_create_pa_batch_jobs_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | (stati batch job) |

### 43. `2025_10_19_164403_add_auction_fields_to_egis_table.php`
| Colonna | Valori |
|---------|--------|
| `sale_mode` | `fixed_price`, `auction`, `not_for_sale` |

### 44. `2025_10_19_200000_create_egi_living_subscriptions_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | (stati subscription) |
| `plan_type` | (tipi piano) |
| `payment_method` | `stripe`, `paypal`, `bank_transfer` |

### 45. `2025_10_19_200001_create_egi_smart_contracts_table.php`
| Colonna | Valori |
|---------|--------|
| `sc_status` | (stati smart contract) |

### 46. `2025_10_19_200002_add_smart_contract_support_to_egi_blockchain_table.php`
| Colonna | Valori |
|---------|--------|
| `blockchain_type` | `ASA`, `SmartContract` |

### 47. `2025_10_19_200003_add_dual_architecture_to_egis_table.php`
| Colonna | Valori |
|---------|--------|
| `egi_type` | `ASA`, `SmartContract` |

### 48. `2025_10_20_092156_create_user_wallets_table.php`
| Colonna | Valori |
|---------|--------|
| `type` | `algorand`, `iban` |

### 49. `2025_10_20_125613_add_certificate_type_column_hotfix_egi_reservation_certificates.php`
| Colonna | Valori |
|---------|--------|
| `certificate_type` | `standard`, `premium`, `eco`, `luxury` |

### 50. `2025_10_21_164500_fix_egi_type_enum_remove_premint.php`
| Colonna | Valori |
|---------|--------|
| `egi_type` | `ASA`, `SmartContract` (rimosso `PreMint`) |

### 51. `2025_10_21_170000_create_ai_trait_generations_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | (stati generazione) |

### 52. `2025_10_21_170001_create_ai_trait_proposals_table.php`
| Colonna | Valori |
|---------|--------|
| `match_type` | (tipi match) |
| `user_decision` | (decisioni utente) |

### 53. `2025_10_22_094956_create_ai_egi_analyses_table.php`
| Colonna | Valori |
|---------|--------|
| `analysis_type` | (tipi analisi) |
| `status` | (stati analisi) |

### 54. `2025_10_22_095022_create_ai_pricing_suggestions_table.php`
| Colonna | Valori |
|---------|--------|
| `strategy_type` | (tipi strategia) |
| `user_decision` | (decisioni utente) |

### 55. `2025_10_22_095026_create_ai_marketing_actions_table.php`
| Colonna | Valori |
|---------|--------|
| `action_type` | (tipi azione) |
| `priority` | (priorità) |
| `effort_level` | (livelli sforzo) |
| `frequency` | (frequenze) |
| `expected_impact` | (impatti) |
| `user_decision` | (decisioni utente) |

### 56. `2025_10_22_095030_create_ai_credits_transactions_table.php`
| Colonna | Valori |
|---------|--------|
| `transaction_type` | (tipi transazione) |
| `operation` | (operazioni) |
| `source_type` | (tipi sorgente) |
| `status` | (stati) |

### 57. `2025_10_22_095320_add_ai_credits_to_users_table.php`
| Colonna | Valori |
|---------|--------|
| `ai_subscription_tier` | (tier subscription) |

### 58. `2025_10_22_102000_create_ai_feature_pricing_table.php`
| Colonna | Valori |
|---------|--------|
| `feature_category` | (categorie) |
| `min_tier_required` | (tier minimi) |
| `recurrence_period` | (periodi ricorrenza) |

### 59. `2025_10_22_162232_add_encryption_fields_to_wallets_table.php`
| Colonna | Valori |
|---------|--------|
| `wallet_type` | `algorand`, `iban`, `both` |

### 60. `2025_10_22_162648_drop_user_wallets_table.php`
| Colonna | Valori |
|---------|--------|
| `type` | `algorand`, `iban` |

### 61. `2025_10_23_201039_create_natan_chat_messages_table.php`
| Colonna | Valori |
|---------|--------|
| `role` | `user`, `assistant` |
| `persona_selection_method` | `manual`, `auto`, `keyword`, `ai`, `default` |
| `rag_method` | `semantic`, `keyword`, `none` |

### 62. `2025_10_27_085610_create_project_documents_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | `pending`, `processing`, `ready`, `failed` |

### 63. `2025_10_30_081142_create_natan_unified_context_table.php`
| Colonna | Valori |
|---------|--------|
| `source_type` | `act`, `web`, `memory`, `file` |

### 64. `2025_11_01_100001_create_egili_transactions_table.php`
| Colonna | Valori |
|---------|--------|
| `transaction_type` | (tipi transazione) |
| `operation` | (operazioni) |
| `status` | (stati) |

### 65. `2025_11_01_120000_create_user_feature_purchases_table.php`
| Colonna | Valori |
|---------|--------|
| `payment_method` | (metodi pagamento) |
| `status` | (stati) |

### 66. `2025_11_02_120000_create_egili_merchant_purchases_table.php`
| Colonna | Valori |
|---------|--------|
| `payment_method` | `fiat`, `crypto` |
| `payment_status` | `pending`, `completed`, `failed`, `refunded` |

### 67. `2025_11_02_181551_add_egili_types_to_egili_transactions_table.php`
| Colonna | Valori |
|---------|--------|
| `egili_type` | `lifetime`, `gift` |

### 68. `2025_11_02_182339_add_feature_types_to_ai_feature_pricing_table.php`
| Colonna | Valori |
|---------|--------|
| `feature_type` | `lifetime`, `consumable`, `temporal` |

### 69. `2025_11_02_183958_create_feature_promotions_table.php`
| Colonna | Valori |
|---------|--------|
| `discount_type` | `percentage`, `fixed_amount` |

### 70. `2025_11_03_120000_create_feature_consumption_ledger_table.php`
| Colonna | Valori |
|---------|--------|
| `consumption_type` | `token_based`, `unit_based`, `time_based` |
| `billing_status` | `pending`, `batched`, `charged` |

### 71. `2025_11_09_143115_create_tenants_table_if_missing.php`
| Colonna | Valori |
|---------|--------|
| `entity_type` | `pa`, `company`, `public_entity`, `other` |

### 72. `2025_11_21_182933_create_invoices_table.php`
| Colonna | Valori |
|---------|--------|
| `invoice_type` | `sales`, `purchase`, `credit_note` |
| `invoice_status` | `draft`, `pending`, `sent`, `delivered`, `paid`, `cancelled`, `rejected` |
| `sdi_status` | `not_sent`, `pending`, `sent`, `delivered`, `rejected` |
| `managed_by` | `platform`, `user_external` |

### 73. `2025_11_21_183001_create_invoice_aggregations_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | `pending`, `invoiced`, `exported`, `cancelled` |

### 74. `2025_11_21_183010_add_invoicing_settings_to_user_invoice_preferences.php`
| Colonna | Valori |
|---------|--------|
| `invoicing_mode` | `platform_managed`, `user_managed` |
| `invoice_frequency` | `instant`, `monthly`, `manual` |

### 75. `2025_11_21_add_natan_fields_to_collections.php`
| Colonna | Valori |
|---------|--------|
| `context` | `marketplace`, `pa_project`, `hybrid` |

### 76. `2025_11_21_add_natan_fields_to_egis.php`
| Colonna | Valori |
|---------|--------|
| `document_status` | `pending`, `processing`, `ready`, `failed` |

### 77. `2025_11_22_100200_create_user_tenant_access_table.php`
| Colonna | Valori |
|---------|--------|
| `access_level` | `read`, `query`, `manage`, `admin` |

### 78. `2025_11_25_183401_create_natan_tutor_actions_table.php`
| Colonna | Valori |
|---------|--------|
| `status` | (stati azione tutor) |

---

## Script di Conversione

Per convertire tutti gli ENUM in VARCHAR, creare una nuova migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL non ha bisogno di conversione esplicita se le tabelle
            // sono state create con $table->string() invece di $table->enum()
            return;
        }
        
        // Per MySQL, convertiamo ENUM in VARCHAR
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Esempio per una tabella:
            DB::statement("ALTER TABLE users MODIFY COLUMN status VARCHAR(50)");
            // Ripetere per tutte le 131 colonne...
        }
    }
    
    public function down(): void
    {
        // Non reversibile in modo sicuro
    }
};
```

---

**Totale colonne ENUM: 131**
**File di migration interessati: 78**
