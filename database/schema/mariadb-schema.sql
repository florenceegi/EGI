/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `anonymized_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `anonymized_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `original_user_id` varchar(100) NOT NULL,
  `anonymization_id` varchar(100) NOT NULL,
  `pseudonym` varchar(50) DEFAULT NULL,
  `anonymized_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `anonymization_reason` enum('user_request','account_closure','consent_withdrawal','retention_expired','legal_requirement','admin_action','automatic_cleanup') NOT NULL,
  `anonymization_method` enum('full_anonymization','pseudonymization','statistical_anonymization','selective_anonymization') NOT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `anonymization_steps` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`anonymization_steps`)),
  `fields_anonymized` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fields_anonymized`)),
  `data_preserved` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_preserved`)),
  `original_registration_date` date DEFAULT NULL,
  `original_last_login` date DEFAULT NULL,
  `original_user_type` varchar(50) DEFAULT NULL,
  `original_subscription_level` varchar(50) DEFAULT NULL,
  `original_activity_score` int(11) DEFAULT NULL,
  `total_collections_created` int(11) NOT NULL DEFAULT 0,
  `total_egis_created` int(11) NOT NULL DEFAULT 0,
  `total_transactions` int(11) NOT NULL DEFAULT 0,
  `total_transaction_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_logins` int(11) NOT NULL DEFAULT 0,
  `days_active` int(11) NOT NULL DEFAULT 0,
  `region` varchar(100) DEFAULT NULL,
  `country_code` varchar(3) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `device_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`device_categories`)),
  `browser_families` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`browser_families`)),
  `preferred_language` varchar(5) DEFAULT NULL,
  `consent_history_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`consent_history_summary`)),
  `gdpr_requests_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gdpr_requests_summary`)),
  `had_security_incidents` tinyint(1) NOT NULL DEFAULT 0,
  `last_privacy_policy_accepted` timestamp NULL DEFAULT NULL,
  `verification_hash` varchar(64) NOT NULL,
  `anonymization_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) unsigned DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `retention_reason` enum('statistical_analysis','regulatory_requirement','business_intelligence','fraud_prevention','research_purposes') DEFAULT NULL,
  `related_records_anonymized` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_records_anonymized`)),
  `external_references` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`external_references`)),
  `blockchain_references_updated` tinyint(1) NOT NULL DEFAULT 0,
  `anonymization_quality` enum('basic','enhanced','differential','certified') NOT NULL DEFAULT 'basic',
  `quality_notes` text DEFAULT NULL,
  `audit_trail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`audit_trail`)),
  `is_recoverable` tinyint(1) NOT NULL DEFAULT 0,
  `recovery_key_hash` varchar(64) DEFAULT NULL,
  `recovery_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `anonymized_users_original_user_id_unique` (`original_user_id`),
  UNIQUE KEY `anonymized_users_anonymization_id_unique` (`anonymization_id`),
  UNIQUE KEY `anonymized_users_pseudonym_unique` (`pseudonym`),
  KEY `anonymized_users_processed_by_foreign` (`processed_by`),
  KEY `anonymized_users_verified_by_foreign` (`verified_by`),
  KEY `idx_anon_at_reason` (`anonymized_at`,`anonymization_reason`),
  KEY `idx_origtype_anonat` (`original_user_type`,`anonymized_at`),
  KEY `idx_region_anonat` (`region`,`anonymized_at`),
  KEY `idx_expires_reason` (`expires_at`,`retention_reason`),
  KEY `idx_verified` (`anonymization_verified`,`verified_at`),
  KEY `idx_orig_reg_date` (`original_registration_date`),
  KEY `idx_tot_coll_egis` (`total_collections_created`,`total_egis_created`),
  CONSTRAINT `anonymized_users_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `anonymized_users_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `biographies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `biographies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('single','chapters') NOT NULL DEFAULT 'single' COMMENT 'Biography structure type',
  `title` varchar(255) NOT NULL COMMENT 'Biography main title',
  `content` longtext DEFAULT NULL COMMENT 'Full biography content (only for type=single)',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Public visibility flag',
  `is_completed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Completion status for UX purposes',
  `slug` varchar(255) DEFAULT NULL COMMENT 'URL-friendly identifier for public biographies',
  `excerpt` text DEFAULT NULL COMMENT 'Short description for sharing/preview',
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Biography display preferences and options' CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `biographies_slug_unique` (`slug`),
  KEY `biographies_user_id_type_index` (`user_id`,`type`),
  KEY `biographies_is_public_created_at_index` (`is_public`,`created_at`),
  KEY `biographies_slug_index` (`slug`),
  KEY `biographies_is_completed_index` (`is_completed`),
  CONSTRAINT `biographies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `biography_chapters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `biography_chapters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `biography_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Chapter title/heading',
  `content` longtext NOT NULL COMMENT 'Chapter text content',
  `date_from` date DEFAULT NULL COMMENT 'Chapter period start date',
  `date_to` date DEFAULT NULL COMMENT 'Chapter period end date',
  `is_ongoing` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'If true, date_to is ignored (current period)',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Manual ordering within biography',
  `is_published` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Chapter visibility (even within public biography)',
  `formatting_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Rich text formatting, highlights, etc.' CHECK (json_valid(`formatting_data`)),
  `chapter_type` varchar(255) NOT NULL DEFAULT 'standard' COMMENT 'Chapter type for different rendering (standard, milestone, achievement)',
  `slug` varchar(255) DEFAULT NULL COMMENT 'Chapter-specific URL fragment',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `biography_chapters_biography_id_slug_unique` (`biography_id`,`slug`),
  KEY `biography_chapters_biography_id_sort_order_index` (`biography_id`,`sort_order`),
  KEY `biography_chapters_biography_id_date_from_index` (`biography_id`,`date_from`),
  KEY `biography_chapters_biography_id_is_published_index` (`biography_id`,`is_published`),
  KEY `biography_chapters_date_from_date_to_index` (`date_from`,`date_to`),
  CONSTRAINT `biography_chapters_biography_id_foreign` FOREIGN KEY (`biography_id`) REFERENCES `biographies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `breach_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `breach_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `category` enum('data_leak','unauthorized_access','system_breach','phishing','other') NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL,
  `status` enum('reported','acknowledged','investigating','resolved','dismissed','escalated') NOT NULL DEFAULT 'reported',
  `description` text NOT NULL,
  `incident_date` timestamp NULL DEFAULT NULL,
  `affected_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`affected_data`)),
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `investigation_notes` text DEFAULT NULL,
  `actions_taken` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`actions_taken`)),
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `investigation_started_at` timestamp NULL DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `assigned_to` bigint(20) unsigned DEFAULT NULL,
  `user_notified` tinyint(1) NOT NULL DEFAULT 0,
  `user_notified_at` timestamp NULL DEFAULT NULL,
  `response_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `breach_reports_user_id_status_index` (`user_id`,`status`),
  KEY `breach_reports_category_severity_index` (`category`,`severity`),
  KEY `breach_reports_status_created_at_index` (`status`,`created_at`),
  KEY `breach_reports_severity_status_index` (`severity`,`status`),
  KEY `breach_reports_incident_date_index` (`incident_date`),
  KEY `breach_reports_assigned_to_index` (`assigned_to`),
  CONSTRAINT `breach_reports_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `breach_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coa` (
  `id` char(26) NOT NULL,
  `egi_id` bigint(20) unsigned NOT NULL,
  `serial` varchar(64) NOT NULL COMMENT 'Format: COA-EGI-YYYY-000###',
  `status` enum('valid','revoked') NOT NULL DEFAULT 'valid',
  `issuer_type` enum('author','archive','platform') NOT NULL DEFAULT 'author',
  `issuer_name` varchar(190) NOT NULL,
  `issuer_location` varchar(190) DEFAULT NULL,
  `issued_at` datetime NOT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `revoke_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `verification_hash` varchar(64) DEFAULT NULL COMMENT 'SHA-256 hash for public verification',
  `integrity_hash` varchar(64) DEFAULT NULL COMMENT 'SHA-256 hash for tamper detection',
  `signature_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Detailed signature information' CHECK (json_valid(`signature_data`)),
  `notes` text DEFAULT NULL COMMENT 'Optional notes about the certificate',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Certificate expiration date',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional certificate metadata' CHECK (json_valid(`metadata`)),
  `creator_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`creator_info`)),
  `qr_code_data` varchar(512) DEFAULT NULL COMMENT 'QR code verification data',
  PRIMARY KEY (`id`),
  UNIQUE KEY `coa_serial_unique` (`serial`),
  KEY `coa_egi_id_status_index` (`egi_id`,`status`),
  KEY `coa_issued_at_index` (`issued_at`),
  KEY `coa_status_index` (`status`),
  KEY `coa_verification_hash_index` (`verification_hash`),
  KEY `coa_integrity_hash_index` (`integrity_hash`),
  KEY `coa_expires_at_index` (`expires_at`),
  KEY `coa_creator_info_index` (`creator_info`(768)),
  CONSTRAINT `coa_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coa_annexes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coa_annexes` (
  `id` char(26) NOT NULL,
  `coa_id` char(26) NOT NULL,
  `code` enum('A_PROVENANCE','B_CONDITION','C_EXHIBITIONS','D_PHOTOS') NOT NULL COMMENT 'Tipologia annesso',
  `version` int(11) NOT NULL DEFAULT 1 COMMENT 'Versione dell''annesso per questo CoA',
  `path` varchar(255) NOT NULL COMMENT 'File singolo (PDF) o ZIP',
  `mime` varchar(127) NOT NULL,
  `bytes` bigint(20) DEFAULT NULL COMMENT 'Dimensione file in bytes',
  `sha256` char(64) NOT NULL COMMENT 'SHA-256 hash del file in hex',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_coa_annex_version` (`coa_id`,`code`,`version`),
  KEY `coa_annexes_created_by_foreign` (`created_by`),
  KEY `coa_annexes_coa_id_code_index` (`coa_id`,`code`),
  KEY `coa_annexes_sha256_index` (`sha256`),
  KEY `coa_annexes_created_at_index` (`created_at`),
  CONSTRAINT `coa_annexes_coa_id_foreign` FOREIGN KEY (`coa_id`) REFERENCES `coa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coa_annexes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coa_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coa_events` (
  `id` char(26) NOT NULL,
  `coa_id` char(26) NOT NULL,
  `type` enum('ISSUED','REVOKED','ANNEX_ADDED','ADDENDUM_ISSUED') NOT NULL COMMENT 'Tipo di evento CoA',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Esito, motivi, elenco file, hash coinvolti' CHECK (json_valid(`payload`)),
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `coa_events_coa_id_type_created_at_index` (`coa_id`,`type`,`created_at`),
  KEY `coa_events_type_created_at_index` (`type`,`created_at`),
  KEY `coa_events_actor_id_index` (`actor_id`),
  CONSTRAINT `coa_events_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `coa_events_coa_id_foreign` FOREIGN KEY (`coa_id`) REFERENCES `coa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coa_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coa_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coa_id` char(26) NOT NULL,
  `kind` enum('pdf','scan_signed','image_front','image_back','signature_detail','core_pdf','bundle_pdf','annex_pack') NOT NULL COMMENT 'Tipologia file CoA',
  `path` varchar(255) NOT NULL,
  `sha256` char(64) NOT NULL COMMENT 'SHA-256 hash del file in hex',
  `bytes` bigint(20) DEFAULT NULL COMMENT 'Dimensione file in bytes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `coa_files_coa_id_kind_index` (`coa_id`),
  KEY `coa_files_sha256_index` (`sha256`),
  KEY `coa_files_created_at_index` (`created_at`),
  CONSTRAINT `coa_files_coa_id_foreign` FOREIGN KEY (`coa_id`) REFERENCES `coa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coa_signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coa_signatures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coa_id` char(26) NOT NULL,
  `kind` enum('qes','autograph_scan','wallet') NOT NULL,
  `provider` varchar(120) DEFAULT NULL COMMENT 'es. Namirial/InfoCert per QES',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'manifest QES o metadati' CHECK (json_valid(`payload`)),
  `pubkey` varchar(128) DEFAULT NULL COMMENT 'per wallet signature',
  `signature_base64` text DEFAULT NULL COMMENT 'firma del digest',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `coa_signatures_coa_id_kind_index` (`coa_id`,`kind`),
  KEY `coa_signatures_created_at_index` (`created_at`),
  CONSTRAINT `coa_signatures_coa_id_foreign` FOREIGN KEY (`coa_id`) REFERENCES `coa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `coa_snapshot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `coa_snapshot` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coa_id` char(26) NOT NULL,
  `snapshot_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Frozen snapshot of EGI traits at CoA issue time' CHECK (json_valid(`snapshot_json`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `coa_snapshot_coa_id_unique` (`coa_id`),
  KEY `coa_snapshot_created_at_index` (`created_at`),
  CONSTRAINT `coa_snapshot_coa_id_foreign` FOREIGN KEY (`coa_id`) REFERENCES `coa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `collection_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collection_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `is_owner` tinyint(1) NOT NULL DEFAULT 0,
  `joined_at` timestamp NULL DEFAULT NULL,
  `removed_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `collection_user_collection_id_user_id_unique` (`collection_id`,`user_id`),
  KEY `collection_user_user_id_foreign` (`user_id`),
  KEY `collection_user_status_index` (`status`),
  CONSTRAINT `collection_user_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collection_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `creator_id` bigint(20) unsigned DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `epp_id` bigint(20) DEFAULT NULL,
  `EGI_asset_id` bigint(20) DEFAULT NULL,
  `collection_name` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `type` varchar(25) DEFAULT NULL,
  `image_banner` varchar(1024) DEFAULT NULL,
  `image_card` varchar(1024) DEFAULT NULL,
  `image_avatar` varchar(1024) DEFAULT NULL,
  `path_image_to_ipfs` varchar(255) DEFAULT NULL,
  `url_image_ipfs` varchar(255) DEFAULT NULL,
  `url_collection_site` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_via` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `featured_in_guest` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica se la Collection può essere inclusa nel carousel guest',
  `featured_position` tinyint(4) DEFAULT NULL COMMENT 'Posizione forzata nel carousel guest (1-10), null = posizione automatica',
  `position` int(11) DEFAULT NULL,
  `EGI_number` int(11) DEFAULT NULL,
  `floor_price` double DEFAULT NULL,
  `EGI_asset_roles` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `collections_creator_id_foreign` (`creator_id`),
  KEY `collections_owner_id_foreign` (`owner_id`),
  KEY `collections_epp_id_index` (`epp_id`),
  KEY `collections_egi_asset_id_index` (`EGI_asset_id`),
  KEY `collections_collection_name_index` (`collection_name`),
  KEY `collections_is_default_index` (`is_default`),
  KEY `collections_type_index` (`type`),
  KEY `collections_status_index` (`status`),
  KEY `collections_is_published_index` (`is_published`),
  KEY `collections_featured_in_guest_index` (`featured_in_guest`),
  KEY `collections_featured_position_index` (`featured_position`),
  CONSTRAINT `collections_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `collections_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consent_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consent_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_consent_id` bigint(20) unsigned DEFAULT NULL,
  `consent_type_slug` varchar(100) NOT NULL,
  `action` enum('granted','renewed','withdrawn','expired','updated','migrated','restored','invalidated') NOT NULL,
  `action_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `action_source` varchar(100) NOT NULL,
  `previous_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`previous_state`)),
  `new_state` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`new_state`)),
  `state_diff` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`state_diff`)),
  `consent_version` varchar(20) DEFAULT NULL,
  `consent_text_shown` text DEFAULT NULL,
  `consent_options_available` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`consent_options_available`)),
  `consent_selections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`consent_selections`)),
  `interaction_method` varchar(50) NOT NULL,
  `explicit_action` tinyint(1) NOT NULL DEFAULT 1,
  `time_to_decision` int(11) DEFAULT NULL,
  `interaction_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interaction_metadata`)),
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `device_fingerprint` varchar(255) DEFAULT NULL,
  `browser_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`browser_info`)),
  `referrer_url` varchar(500) DEFAULT NULL,
  `legal_basis` varchar(100) NOT NULL DEFAULT 'consent',
  `reason_for_action` text DEFAULT NULL,
  `triggered_by` varchar(100) DEFAULT NULL,
  `business_context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`business_context`)),
  `user_notified` tinyint(1) NOT NULL DEFAULT 0,
  `notification_sent_at` timestamp NULL DEFAULT NULL,
  `notification_channel` varchar(50) DEFAULT NULL,
  `acknowledgment_required` tinyint(1) NOT NULL DEFAULT 0,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `age_verified` tinyint(1) DEFAULT NULL,
  `identity_verified` tinyint(1) DEFAULT NULL,
  `verification_methods` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`verification_methods`)),
  `verification_notes` text DEFAULT NULL,
  `admin_user_id` bigint(20) unsigned DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `requires_review` tinyint(1) NOT NULL DEFAULT 0,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `record_hash` varchar(64) NOT NULL,
  `integrity_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`integrity_metadata`)),
  `is_verified` tinyint(1) NOT NULL DEFAULT 1,
  `related_request_id` varchar(100) DEFAULT NULL,
  `related_incident_id` varchar(100) DEFAULT NULL,
  `related_records` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`related_records`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consent_histories_user_consent_id_foreign` (`user_consent_id`),
  KEY `consent_histories_admin_user_id_foreign` (`admin_user_id`),
  KEY `consent_histories_reviewed_by_foreign` (`reviewed_by`),
  KEY `ch_user_action_time` (`user_id`,`action_timestamp`),
  KEY `ch_user_consent_time` (`user_id`,`consent_type_slug`,`action_timestamp`),
  KEY `ch_consent_action` (`consent_type_slug`,`action`),
  KEY `ch_action_time` (`action`,`action_timestamp`),
  KEY `ch_source_time` (`action_source`,`action_timestamp`),
  KEY `ch_action_timestamp` (`action_timestamp`),
  KEY `ch_review_time` (`requires_review`,`action_timestamp`),
  FULLTEXT KEY `consent_histories_reason_for_action_admin_notes_fulltext` (`reason_for_action`,`admin_notes`),
  CONSTRAINT `consent_histories_admin_user_id_foreign` FOREIGN KEY (`admin_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consent_histories_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consent_histories_user_consent_id_foreign` FOREIGN KEY (`user_consent_id`) REFERENCES `user_consents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consent_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consent_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consent_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL,
  `legal_basis` enum('consent','legitimate_interest','contract','legal_obligation','vital_interests','public_task') NOT NULL DEFAULT 'consent',
  `data_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_categories`)),
  `processing_purposes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`processing_purposes`)),
  `recipients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recipients`)),
  `international_transfers` tinyint(1) DEFAULT 0,
  `transfer_countries` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`transfer_countries`)),
  `is_required` tinyint(1) DEFAULT 0,
  `is_granular` tinyint(1) DEFAULT 1,
  `can_withdraw` tinyint(1) DEFAULT 1,
  `withdrawal_effect_days` int(11) DEFAULT 30,
  `retention_period` varchar(100) DEFAULT NULL,
  `retention_days` int(11) DEFAULT NULL,
  `deletion_method` varchar(50) NOT NULL DEFAULT 'hard_delete',
  `priority_order` int(11) NOT NULL DEFAULT 100,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `requires_double_opt_in` tinyint(1) NOT NULL DEFAULT 0,
  `requires_age_verification` tinyint(1) NOT NULL DEFAULT 0,
  `minimum_age` int(11) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `form_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_fields`)),
  `gdpr_assessment_date` timestamp NULL DEFAULT NULL,
  `gdpr_assessment_notes` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `consent_types_slug_unique` (`slug`),
  KEY `consent_types_created_by_foreign` (`created_by`),
  KEY `consent_types_approved_by_foreign` (`approved_by`),
  KEY `consent_types_slug_is_active_index` (`slug`,`is_active`),
  KEY `consent_types_legal_basis_is_active_index` (`legal_basis`,`is_active`),
  KEY `consent_types_is_required_is_active_index` (`is_required`,`is_active`),
  KEY `consent_types_priority_order_is_active_index` (`priority_order`,`is_active`),
  CONSTRAINT `consent_types_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `consent_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `consent_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `consent_versions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(20) NOT NULL,
  `consent_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`consent_types`)),
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `configuration` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`configuration`)),
  `effective_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deprecated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `consent_versions_version_unique` (`version`),
  KEY `consent_versions_created_by_foreign` (`created_by`),
  KEY `consent_versions_effective_date_index` (`effective_date`),
  KEY `consent_versions_is_active_effective_date_index` (`is_active`,`effective_date`),
  CONSTRAINT `consent_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_exports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_exports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `format` enum('json','csv','pdf') NOT NULL,
  `categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`categories`)),
  `status` enum('pending','processing','completed','failed','expired') NOT NULL DEFAULT 'pending',
  `progress` tinyint(4) NOT NULL DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `file_hash` varchar(64) DEFAULT NULL,
  `download_count` int(10) unsigned NOT NULL DEFAULT 0,
  `last_downloaded_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_exports_token_unique` (`token`),
  KEY `data_exports_user_id_status_index` (`user_id`,`status`),
  KEY `data_exports_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `data_exports_status_created_at_index` (`status`,`created_at`),
  KEY `data_exports_expires_at_index` (`expires_at`),
  KEY `data_exports_token_index` (`token`),
  CONSTRAINT `data_exports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `data_retention_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_retention_policies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `data_category` varchar(100) NOT NULL,
  `data_type` varchar(100) DEFAULT NULL,
  `applicable_tables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_tables`)),
  `applicable_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_fields`)),
  `retention_trigger` enum('time_based','inactivity_based','consent_withdrawal','account_closure','legal_basis_ends','custom_event') NOT NULL,
  `retention_days` int(11) DEFAULT NULL,
  `retention_period` varchar(100) DEFAULT NULL,
  `grace_period_days` int(11) NOT NULL DEFAULT 0,
  `deletion_method` enum('hard_delete','soft_delete','anonymize','pseudonymize','archive') NOT NULL DEFAULT 'anonymize',
  `anonymization_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`anonymization_rules`)),
  `deletion_exceptions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`deletion_exceptions`)),
  `legal_basis` varchar(100) DEFAULT NULL,
  `legal_justification` text DEFAULT NULL,
  `regulatory_requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`regulatory_requirements`)),
  `user_can_request_deletion` tinyint(1) NOT NULL DEFAULT 1,
  `requires_admin_approval` tinyint(1) NOT NULL DEFAULT 0,
  `notify_user_before_deletion` tinyint(1) NOT NULL DEFAULT 1,
  `notification_days_before` int(11) NOT NULL DEFAULT 30,
  `is_automated` tinyint(1) NOT NULL DEFAULT 1,
  `execution_schedule` varchar(50) NOT NULL DEFAULT 'daily',
  `execution_time` time NOT NULL DEFAULT '02:00:00',
  `batch_size` int(11) NOT NULL DEFAULT 1000,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_executed_at` timestamp NULL DEFAULT NULL,
  `last_execution_count` int(11) NOT NULL DEFAULT 0,
  `execution_log` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`execution_log`)),
  `policy_effective_date` timestamp NULL DEFAULT NULL,
  `policy_review_date` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `risk_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `risk_assessment` text DEFAULT NULL,
  `mitigation_measures` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`mitigation_measures`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_retention_policies_slug_unique` (`slug`),
  KEY `data_retention_policies_created_by_foreign` (`created_by`),
  KEY `data_retention_policies_approved_by_foreign` (`approved_by`),
  KEY `drp_data_category_active` (`data_category`,`is_active`),
  KEY `drp_retention_trigger_active` (`retention_trigger`,`is_active`),
  KEY `drp_automated_active` (`is_automated`,`is_active`),
  KEY `drp_last_exec_schedule` (`last_executed_at`,`execution_schedule`),
  KEY `drp_policy_effective_active` (`policy_effective_date`,`is_active`),
  CONSTRAINT `data_retention_policies_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `data_retention_policies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dpo_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dpo_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `request_type` enum('information','complaint','access_request','other') NOT NULL,
  `status` enum('sent','acknowledged','in_progress','responded','closed') NOT NULL DEFAULT 'sent',
  `dpo_response` text DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `handled_by` bigint(20) unsigned DEFAULT NULL,
  `requires_followup` tinyint(1) NOT NULL DEFAULT 0,
  `followup_due_at` timestamp NULL DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dpo_messages_user_id_status_index` (`user_id`,`status`),
  KEY `dpo_messages_status_priority_index` (`status`,`priority`),
  KEY `dpo_messages_request_type_status_index` (`request_type`,`status`),
  KEY `dpo_messages_handled_by_status_index` (`handled_by`,`status`),
  KEY `dpo_messages_acknowledged_at_index` (`acknowledged_at`),
  KEY `dpo_messages_followup_due_at_index` (`followup_due_at`),
  CONSTRAINT `dpo_messages_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `dpo_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `egi_audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `egi_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `egi_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `action` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `egi_audits_egi_id_foreign` (`egi_id`),
  KEY `egi_audits_user_id_foreign` (`user_id`),
  CONSTRAINT `egi_audits_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `egi_audits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `egi_reservation_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `egi_reservation_certificates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint(20) unsigned NOT NULL,
  `egi_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `wallet_address` varchar(58) NOT NULL,
  `reservation_type` enum('strong','weak') NOT NULL,
  `offer_amount_fiat` decimal(10,2) NOT NULL,
  `offer_amount_algo` bigint(20) unsigned NOT NULL,
  `certificate_uuid` char(36) NOT NULL,
  `signature_hash` varchar(64) NOT NULL,
  `is_superseded` tinyint(1) NOT NULL DEFAULT 0,
  `is_current_highest` tinyint(1) NOT NULL DEFAULT 1,
  `pdf_path` varchar(255) DEFAULT NULL,
  `public_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `egi_reservation_certificates_certificate_uuid_unique` (`certificate_uuid`),
  KEY `egi_reservation_certificates_reservation_id_foreign` (`reservation_id`),
  KEY `egi_reservation_certificates_user_id_foreign` (`user_id`),
  KEY `egi_reservation_certificates_certificate_uuid_index` (`certificate_uuid`),
  KEY `egi_reservation_certificates_egi_id_is_current_highest_index` (`egi_id`,`is_current_highest`),
  KEY `egi_reservation_certificates_wallet_address_index` (`wallet_address`),
  CONSTRAINT `egi_reservation_certificates_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `egi_reservation_certificates_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `egi_reservation_certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `egi_traits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `egi_traits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `egi_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `trait_type_id` bigint(20) unsigned DEFAULT NULL,
  `value` varchar(255) NOT NULL,
  `display_value` varchar(255) DEFAULT NULL,
  `image_description` text DEFAULT NULL,
  `image_alt_text` varchar(255) DEFAULT NULL,
  `image_updated_at` timestamp NULL DEFAULT NULL,
  `rarity_percentage` decimal(5,2) DEFAULT NULL,
  `ipfs_hash` varchar(255) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `egi_traits_egi_id_is_locked_index` (`egi_id`,`is_locked`),
  KEY `egi_traits_trait_type_id_value_index` (`trait_type_id`,`value`),
  KEY `egi_traits_category_id_sort_order_index` (`category_id`,`sort_order`),
  CONSTRAINT `egi_traits_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `trait_categories` (`id`),
  CONSTRAINT `egi_traits_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `egi_traits_trait_type_id_foreign` FOREIGN KEY (`trait_type_id`) REFERENCES `trait_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `egi_traits_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `egi_traits_version` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `egi_id` bigint(20) unsigned NOT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `traits_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Snapshot dei traits per versioning' CHECK (json_valid(`traits_json`)),
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `egi_traits_version_egi_id_version_unique` (`egi_id`,`version`),
  KEY `egi_traits_version_egi_id_created_at_index` (`egi_id`,`created_at`),
  KEY `egi_traits_version_created_by_index` (`created_by`),
  CONSTRAINT `egi_traits_version_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `egi_traits_version_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `egis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `egis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint(20) unsigned NOT NULL,
  `key_file` bigint(20) unsigned DEFAULT NULL,
  `token_EGI` varchar(255) DEFAULT NULL,
  `jsonMetadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`jsonMetadata`)),
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `auction_id` bigint(20) unsigned DEFAULT NULL,
  `owner_id` bigint(20) unsigned DEFAULT NULL,
  `drop_id` bigint(20) unsigned DEFAULT NULL,
  `upload_id` varchar(255) DEFAULT NULL,
  `creator` varchar(255) DEFAULT NULL,
  `owner_wallet` varchar(255) DEFAULT NULL,
  `drop_title` varchar(255) DEFAULT NULL,
  `title` varchar(60) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `media` tinyint(1) DEFAULT 0,
  `type` varchar(10) DEFAULT NULL,
  `bind` int(11) DEFAULT NULL,
  `paired` int(11) DEFAULT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `floorDropPrice` decimal(20,2) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `creation_date` date DEFAULT NULL,
  `size` text DEFAULT NULL,
  `dimension` text DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 0,
  `mint` tinyint(1) DEFAULT 0,
  `rebind` tinyint(1) DEFAULT 1,
  `file_crypt` text DEFAULT NULL,
  `file_hash` text DEFAULT NULL,
  `file_IPFS` text DEFAULT NULL,
  `file_mime` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `hyper` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indicates if this is a hyper EGI',
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `egis_collection_id_foreign` (`collection_id`),
  KEY `egis_updated_by_foreign` (`updated_by`),
  KEY `egis_key_file_index` (`key_file`),
  KEY `egis_user_id_index` (`user_id`),
  KEY `egis_auction_id_index` (`auction_id`),
  KEY `egis_owner_id_index` (`owner_id`),
  KEY `egis_drop_id_index` (`drop_id`),
  KEY `egis_title_index` (`title`),
  CONSTRAINT `egis_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `egis_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epp_milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `epp_milestones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `epp_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'planned',
  `target_value` decimal(20,2) DEFAULT NULL,
  `current_value` decimal(20,2) NOT NULL DEFAULT 0.00,
  `evidence_url` varchar(1024) DEFAULT NULL,
  `evidence_type` varchar(50) DEFAULT NULL,
  `media` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`media`)),
  `target_date` date DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epp_milestones_epp_id_status_index` (`epp_id`,`status`),
  KEY `epp_milestones_epp_id_type_index` (`epp_id`,`type`),
  KEY `epp_milestones_type_index` (`type`),
  KEY `epp_milestones_status_index` (`status`),
  CONSTRAINT `epp_milestones_epp_id_foreign` FOREIGN KEY (`epp_id`) REFERENCES `epps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epp_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `epp_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `epp_id` bigint(20) unsigned NOT NULL,
  `egi_id` bigint(20) unsigned DEFAULT NULL,
  `collection_id` bigint(20) unsigned DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `blockchain_tx_id` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `epp_transactions_blockchain_tx_id_unique` (`blockchain_tx_id`),
  KEY `epp_transactions_egi_id_foreign` (`egi_id`),
  KEY `epp_transactions_collection_id_foreign` (`collection_id`),
  KEY `epp_transactions_epp_id_transaction_type_index` (`epp_id`,`transaction_type`),
  KEY `epp_transactions_epp_id_status_index` (`epp_id`,`status`),
  KEY `epp_transactions_transaction_type_index` (`transaction_type`),
  KEY `epp_transactions_status_index` (`status`),
  CONSTRAINT `epp_transactions_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `epp_transactions_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE SET NULL,
  CONSTRAINT `epp_transactions_epp_id_foreign` FOREIGN KEY (`epp_id`) REFERENCES `epps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `epps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(1024) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `total_funds` decimal(20,2) NOT NULL DEFAULT 0.00,
  `target_funds` decimal(20,2) DEFAULT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epps_manager_id_foreign` (`manager_id`),
  KEY `epps_type_index` (`type`),
  KEY `epps_status_index` (`status`),
  CONSTRAINT `epps_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `error_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `error_code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'error',
  `blocking` varchar(255) NOT NULL DEFAULT 'not',
  `message` text DEFAULT NULL,
  `user_message` text DEFAULT NULL,
  `http_status_code` int(11) DEFAULT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `display_mode` varchar(255) DEFAULT NULL,
  `exception_class` varchar(255) DEFAULT NULL,
  `exception_message` text DEFAULT NULL,
  `exception_code` varchar(255) DEFAULT NULL,
  `exception_file` varchar(255) DEFAULT NULL,
  `exception_line` int(11) DEFAULT NULL,
  `exception_trace` text DEFAULT NULL,
  `request_method` varchar(255) DEFAULT NULL,
  `request_url` text DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `resolved` tinyint(1) NOT NULL DEFAULT 0,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` varchar(255) DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `notified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `error_logs_error_code_index` (`error_code`),
  KEY `error_logs_type_index` (`type`),
  KEY `error_logs_resolved_index` (`resolved`),
  KEY `error_logs_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gdpr_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gdpr_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action_type` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `legal_basis` varchar(100) NOT NULL,
  `data_subject_id` bigint(20) unsigned DEFAULT NULL,
  `data_controller` varchar(255) DEFAULT NULL,
  `data_processor` varchar(255) DEFAULT NULL,
  `purpose_of_processing` varchar(500) DEFAULT NULL,
  `data_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data_categories`)),
  `recipient_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recipient_categories`)),
  `international_transfers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`international_transfers`)),
  `retention_period` varchar(100) DEFAULT NULL,
  `security_measures` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`security_measures`)),
  `context_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context_data`)),
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `checksum` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gdpr_audit_logs_user_id_action_type_index` (`user_id`,`action_type`),
  KEY `gdpr_audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `gdpr_audit_logs_action_type_created_at_index` (`action_type`,`created_at`),
  KEY `gdpr_audit_logs_legal_basis_created_at_index` (`legal_basis`,`created_at`),
  KEY `gdpr_audit_logs_data_subject_id_created_at_index` (`data_subject_id`,`created_at`),
  KEY `gdpr_audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `gdpr_audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gdpr_notification_payloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gdpr_notification_payloads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gdpr_notification_type` varchar(255) NOT NULL,
  `previous_value` text DEFAULT NULL,
  `new_value` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'creator',
  `message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload_status` enum('pending_user_confirmation','user_confirmed_action','user_revoked_consent','user_disavowed_suspicious','error') NOT NULL DEFAULT 'pending_user_confirmation',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gdpr_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gdpr_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` enum('access','rectification','erasure','portability','restriction','objection','data_update','deletion','deletion_executed') NOT NULL,
  `status` enum('pending','in_progress','completed','rejected','cancelled','expired') NOT NULL DEFAULT 'pending',
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`)),
  `notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `processor_role` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gdpr_requests_processed_by_foreign` (`processed_by`),
  KEY `gdpr_requests_user_id_type_index` (`user_id`,`type`),
  KEY `gdpr_requests_user_id_status_index` (`user_id`,`status`),
  KEY `gdpr_requests_type_status_index` (`type`,`status`),
  KEY `gdpr_requests_requested_at_type_index` (`requested_at`,`type`),
  KEY `gdpr_requests_status_index` (`status`),
  KEY `gdpr_requests_expires_at_index` (`expires_at`),
  CONSTRAINT `gdpr_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `gdpr_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `icons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `style` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `html` text NOT NULL,
  `host` varchar(255) NOT NULL,
  `name_on_host` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `likeable_type` varchar(255) NOT NULL,
  `likeable_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `likes_user_id_likeable_id_likeable_type_unique` (`user_id`,`likeable_id`,`likeable_type`),
  KEY `likes_likeable_type_likeable_id_index` (`likeable_type`,`likeable_id`),
  CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`manipulations`)),
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`custom_properties`)),
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`generated_conversions`)),
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`responsive_images`)),
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_model_type_model_id_collection_name_index` (`model_type`,`model_id`,`collection_name`),
  KEY `media_collection_name_created_at_index` (`collection_name`,`created_at`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_payload_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_payload_invitations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint(20) unsigned NOT NULL,
  `proposer_id` bigint(20) unsigned DEFAULT NULL,
  `receiver_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_payload_invitations_collection_id_foreign` (`collection_id`),
  KEY `notification_payload_invitations_proposer_id_foreign` (`proposer_id`),
  KEY `notification_payload_invitations_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `notification_payload_invitations_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_payload_invitations_proposer_id_foreign` FOREIGN KEY (`proposer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_payload_invitations_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_payload_reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_payload_reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint(20) unsigned NOT NULL,
  `egi_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'Type: reservation_expired, superseded, highest, rank_changed',
  `status` enum('info','success','warning','error','pending') NOT NULL DEFAULT 'info' COMMENT 'Notification status/severity',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Notification payload data (amounts, ranks, etc)' CHECK (json_valid(`data`)),
  `message` text DEFAULT NULL COMMENT 'Custom message if different from default',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'When user read/acknowledged the notification',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_payload_reservations_egi_id_foreign` (`egi_id`),
  KEY `idx_user_unread` (`user_id`,`read_at`),
  KEY `idx_reservation_type` (`reservation_id`,`type`),
  KEY `idx_type` (`type`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `notification_payload_reservations_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_payload_reservations_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_payload_reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification_payload_wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_payload_wallets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint(20) unsigned DEFAULT NULL,
  `proposer_id` bigint(20) unsigned DEFAULT NULL,
  `receiver_id` bigint(20) unsigned DEFAULT NULL,
  `wallet` varchar(255) DEFAULT NULL,
  `royalty_mint` double DEFAULT NULL,
  `royalty_rebind` double DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `type` varchar(255) NOT NULL DEFAULT 'update',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_payload_wallets_collection_id_foreign` (`collection_id`),
  KEY `notification_payload_wallets_proposer_id_foreign` (`proposer_id`),
  KEY `notification_payload_wallets_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `notification_payload_wallets_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_payload_wallets_proposer_id_foreign` FOREIGN KEY (`proposer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_payload_wallets_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `view` varchar(255) DEFAULT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `sender_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `outcome` varchar(25) NOT NULL DEFAULT 'pending',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_distributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_distributions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` bigint(20) unsigned NOT NULL,
  `collection_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_type` enum('weak','creator','collector','commissioner','company','epp','trader-pro','vip') NOT NULL COMMENT 'Tipologia utente beneficiario',
  `percentage` decimal(5,2) NOT NULL COMMENT 'Percentuale di distribuzione (es: 15.50%)',
  `amount_eur` decimal(12,2) NOT NULL COMMENT 'Valore in EUR (fonte di verità)',
  `exchange_rate` decimal(20,10) NOT NULL COMMENT 'Tasso EUR/ALGO al momento della transazione',
  `is_epp` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flag per donazioni ambientali',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dati aggiuntivi (wallet_address, platform_role, etc.)' CHECK (json_valid(`metadata`)),
  `distribution_status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, processed, confirmed, failed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_payments_dist_reservation` (`reservation_id`),
  KEY `idx_payments_dist_collection` (`collection_id`),
  KEY `idx_payments_dist_user` (`user_id`),
  KEY `idx_payments_dist_user_type` (`user_type`),
  KEY `idx_payments_dist_epp` (`is_epp`),
  KEY `idx_payments_dist_status` (`distribution_status`),
  KEY `idx_payments_dist_created` (`created_at`),
  KEY `idx_payments_dist_coll_utype` (`collection_id`,`user_type`),
  KEY `idx_payments_dist_res_user` (`reservation_id`,`user_id`),
  KEY `idx_payments_dist_epp_date` (`is_epp`,`created_at`),
  CONSTRAINT `payment_distributions_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_distributions_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_distributions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `privacy_policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `privacy_policies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `document_type` enum('privacy_policy','terms_of_service','cookie_policy','data_processing_agreement','consent_form','gdpr_notice','retention_policy','security_policy') NOT NULL DEFAULT 'privacy_policy',
  `content` longtext NOT NULL,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`summary`)),
  `changes_from_previous` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes_from_previous`)),
  `change_summary` text DEFAULT NULL,
  `previous_version` varchar(20) DEFAULT NULL,
  `effective_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expiry_date` timestamp NULL DEFAULT NULL,
  `review_date` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `requires_consent_refresh` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','under_review','approved','active','superseded','archived','rejected') DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approval_date` timestamp NULL DEFAULT NULL,
  `legal_review_status` enum('pending','in_progress','approved','requires_changes','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `legal_basis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`legal_basis`)),
  `third_party_services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`third_party_services`)),
  `language` varchar(5) NOT NULL DEFAULT 'en',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `legal_reviewer` bigint(20) unsigned DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `change_description` text DEFAULT NULL,
  `previous_version_id` bigint(20) unsigned DEFAULT NULL,
  `notification_sent` tinyint(1) NOT NULL DEFAULT 0,
  `notification_date` timestamp NULL DEFAULT NULL,
  `requires_consent` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `privacy_policies_created_by_foreign` (`created_by`),
  KEY `privacy_policies_approved_by_foreign` (`approved_by`),
  KEY `privacy_policies_is_active_effective_date_index` (`is_active`,`effective_date`),
  KEY `privacy_policies_status_effective_date_index` (`status`,`effective_date`),
  KEY `privacy_policies_effective_date_index` (`effective_date`),
  KEY `privacy_policies_review_date_index` (`review_date`),
  KEY `privacy_policies_language_is_active_index` (`language`,`is_active`),
  KEY `privacy_policies_legal_reviewer_foreign` (`legal_reviewer`),
  KEY `privacy_policies_previous_version_id_foreign` (`previous_version_id`),
  KEY `pp_type_active` (`document_type`,`is_active`),
  KEY `pp_status_effective` (`status`,`effective_date`),
  KEY `pp_legal_review` (`legal_review_status`),
  KEY `pp_requires_consent` (`requires_consent`),
  KEY `pp_language_type` (`language`,`document_type`),
  KEY `pp_notification_sent` (`notification_sent`),
  KEY `pp_expiry_date` (`expiry_date`),
  CONSTRAINT `privacy_policies_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `privacy_policies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `privacy_policies_legal_reviewer_foreign` FOREIGN KEY (`legal_reviewer`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `privacy_policies_previous_version_id_foreign` FOREIGN KEY (`previous_version_id`) REFERENCES `privacy_policies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `privacy_policy_acceptances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `privacy_policy_acceptances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `privacy_policy_id` bigint(20) unsigned NOT NULL,
  `accepted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acceptance_method` varchar(50) NOT NULL,
  `acceptance_type` enum('initial','update','renewal','explicit_consent','implied_consent') NOT NULL,
  `policy_version` varchar(20) NOT NULL,
  `policy_summary` text DEFAULT NULL,
  `changes_highlighted` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes_highlighted`)),
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `device_fingerprint` varchar(255) DEFAULT NULL,
  `session_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`session_data`)),
  `explicit_checkbox` tinyint(1) NOT NULL DEFAULT 0,
  `read_full_policy` tinyint(1) NOT NULL DEFAULT 0,
  `time_spent_reading` int(11) DEFAULT NULL,
  `interaction_evidence` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interaction_evidence`)),
  `was_notified` tinyint(1) NOT NULL DEFAULT 0,
  `notification_sent_at` timestamp NULL DEFAULT NULL,
  `notification_method` varchar(50) DEFAULT NULL,
  `notification_acknowledged_at` timestamp NULL DEFAULT NULL,
  `withdrawn_at` timestamp NULL DEFAULT NULL,
  `withdrawal_method` varchar(50) DEFAULT NULL,
  `withdrawal_reason` text DEFAULT NULL,
  `current_acceptance` tinyint(1) NOT NULL DEFAULT 1,
  `legal_basis` varchar(100) NOT NULL DEFAULT 'consent',
  `compliance_notes` text DEFAULT NULL,
  `requires_new_consent` tinyint(1) NOT NULL DEFAULT 0,
  `acceptance_hash` varchar(64) DEFAULT NULL,
  `verification_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`verification_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_current_acceptance` (`user_id`,`privacy_policy_id`,`current_acceptance`),
  KEY `privacy_policy_acceptances_user_id_current_acceptance_index` (`user_id`,`current_acceptance`),
  KEY `privacy_policy_acceptances_user_id_accepted_at_index` (`user_id`,`accepted_at`),
  KEY `privacy_policy_acceptances_privacy_policy_id_accepted_at_index` (`privacy_policy_id`,`accepted_at`),
  KEY `privacy_policy_acceptances_policy_version_acceptance_type_index` (`policy_version`,`acceptance_type`),
  KEY `privacy_policy_acceptances_accepted_at_index` (`accepted_at`),
  KEY `privacy_policy_acceptances_withdrawn_at_index` (`withdrawn_at`),
  CONSTRAINT `privacy_policy_acceptances_privacy_policy_id_foreign` FOREIGN KEY (`privacy_policy_id`) REFERENCES `privacy_policies` (`id`),
  CONSTRAINT `privacy_policy_acceptances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `processing_restrictions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `processing_restrictions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `restriction_type` varchar(50) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `affected_data_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`affected_data_categories`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `lifted_at` timestamp NULL DEFAULT NULL,
  `lifted_by` varchar(255) DEFAULT NULL,
  `lift_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `processing_restrictions_user_id_is_active_index` (`user_id`,`is_active`),
  KEY `processing_restrictions_restriction_type_is_active_index` (`restriction_type`,`is_active`),
  KEY `processing_restrictions_lifted_at_index` (`lifted_at`),
  CONSTRAINT `processing_restrictions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `egi_id` bigint(20) unsigned NOT NULL,
  `type` enum('weak','strong') NOT NULL DEFAULT 'weak' COMMENT 'Reservation type - may be used for future priority logic',
  `status` enum('active','expired','completed','cancelled','withdrawn') NOT NULL DEFAULT 'active' COMMENT 'Main reservation status',
  `sub_status` enum('pending','highest','superseded','confirmed','minted','withdrawn','expired') NOT NULL DEFAULT 'pending' COMMENT 'Detailed state for pre-launch queue system',
  `amount_eur` decimal(12,2) NOT NULL COMMENT 'Canonical reservation amount in EUR (source of truth)',
  `rank_position` int(10) unsigned DEFAULT NULL COMMENT 'Current position in the offer ranking for this EGI',
  `previous_rank` int(10) unsigned DEFAULT NULL COMMENT 'Previous position before last update',
  `is_highest` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Quick flag for highest current offer',
  `is_current` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this is the current active reservation',
  `display_currency` char(3) NOT NULL DEFAULT 'EUR' COMMENT 'Currency used for display to this user',
  `display_amount` decimal(12,2) DEFAULT NULL COMMENT 'Amount in display currency for transparency',
  `display_exchange_rate` decimal(20,10) DEFAULT NULL COMMENT 'Exchange rate used for display conversion',
  `input_currency` char(3) NOT NULL DEFAULT 'EUR' COMMENT 'Original currency input by user',
  `input_amount` decimal(12,2) NOT NULL COMMENT 'Original amount input by user',
  `input_exchange_rate` decimal(20,10) DEFAULT NULL COMMENT 'Exchange rate at time of input if not EUR',
  `input_timestamp` timestamp NULL DEFAULT NULL COMMENT 'When the reservation was placed',
  `superseded_by_id` bigint(20) unsigned DEFAULT NULL,
  `superseded_at` timestamp NULL DEFAULT NULL COMMENT 'When this reservation was outbid',
  `mint_window_starts_at` timestamp NULL DEFAULT NULL COMMENT 'When this user can start minting (future)',
  `mint_window_ends_at` timestamp NULL DEFAULT NULL COMMENT 'When mint window expires (future)',
  `mint_confirmed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'User confirmed intent to mint (future)',
  `mint_confirmed_at` timestamp NULL DEFAULT NULL COMMENT 'When user confirmed mint intent (future)',
  `payment_method` varchar(20) DEFAULT NULL COMMENT 'Future payment method (algo/card/bank)',
  `payment_amount_eur` decimal(12,2) DEFAULT NULL COMMENT 'Actual payment amount in EUR (future)',
  `payment_currency` varchar(3) DEFAULT NULL COMMENT 'Currency used for payment (future)',
  `payment_amount` decimal(12,2) DEFAULT NULL COMMENT 'Amount in payment currency (future)',
  `payment_exchange_rate` decimal(20,10) DEFAULT NULL COMMENT 'Exchange rate at payment time (future)',
  `payment_executed_at` timestamp NULL DEFAULT NULL COMMENT 'When payment was executed (future)',
  `algo_amount_micro` bigint(20) unsigned DEFAULT NULL COMMENT 'Amount in microALGO for on-chain (future)',
  `algo_tx_id` varchar(128) DEFAULT NULL COMMENT 'Algorand transaction ID (future)',
  `asa_id` varchar(64) DEFAULT NULL COMMENT 'Algorand Standard Asset ID if minted (future)',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional metadata (contact info, notes, etc)' CHECK (json_valid(`metadata`)),
  `user_note` text DEFAULT NULL COMMENT 'Optional note from user about reservation',
  `admin_note` text DEFAULT NULL COMMENT 'Internal admin notes',
  `last_notification_at` timestamp NULL DEFAULT NULL COMMENT 'Last time user was notified about this reservation',
  `notification_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'History of notifications sent' CHECK (json_valid(`notification_history`)),
  `original_currency` varchar(3) NOT NULL DEFAULT 'EUR' COMMENT 'Legacy: Original currency used',
  `original_price` decimal(15,8) DEFAULT NULL COMMENT 'Legacy: Original price',
  `algo_price` bigint(20) unsigned DEFAULT NULL COMMENT 'Legacy: Price in microALGO',
  `exchange_rate` decimal(18,8) DEFAULT NULL COMMENT 'Legacy: Exchange rate',
  `rate_timestamp` timestamp NULL DEFAULT NULL COMMENT 'Legacy: Rate timestamp',
  `fiat_currency` varchar(3) NOT NULL DEFAULT 'EUR' COMMENT 'Legacy: Display currency',
  `offer_amount_fiat` decimal(10,2) DEFAULT NULL COMMENT 'Legacy: Display price in FIAT',
  `offer_amount_algo` bigint(20) unsigned DEFAULT NULL,
  `exchange_timestamp` timestamp NULL DEFAULT NULL COMMENT 'Legacy: Exchange timestamp',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Legacy: Expiration time',
  `contact_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Legacy: Contact data for strong reservations' CHECK (json_valid(`contact_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_status` (`user_id`,`status`),
  KEY `idx_user_egi` (`user_id`,`egi_id`),
  KEY `idx_egi_status` (`egi_id`,`status`),
  KEY `idx_egi_current` (`egi_id`,`is_current`),
  KEY `idx_egi_highest` (`egi_id`,`is_highest`),
  KEY `idx_egi_rank` (`egi_id`,`rank_position`),
  KEY `idx_egi_amount_status` (`egi_id`,`amount_eur`,`status`),
  KEY `idx_rank` (`rank_position`),
  KEY `idx_highest` (`is_highest`),
  KEY `idx_status_sub` (`status`,`sub_status`),
  KEY `idx_superseded` (`superseded_by_id`),
  KEY `idx_superseded_time` (`superseded_at`),
  KEY `idx_mint_start` (`mint_window_starts_at`),
  KEY `idx_mint_end` (`mint_window_ends_at`),
  KEY `idx_mint_confirmed_status` (`mint_confirmed`,`status`),
  KEY `idx_original_currency` (`original_currency`),
  KEY `idx_fiat_currency` (`fiat_currency`),
  KEY `idx_algo_price` (`algo_price`),
  KEY `idx_egi_algo_amount` (`egi_id`,`offer_amount_algo`),
  CONSTRAINT `reservations_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_superseded_by_id_foreign` FOREIGN KEY (`superseded_by_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `security_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `security_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `event_type` varchar(100) NOT NULL,
  `severity` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `status` enum('detected','investigating','resolved','false_positive') NOT NULL DEFAULT 'detected',
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `source` varchar(50) DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint(20) unsigned DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `security_events_resolved_by_foreign` (`resolved_by`),
  KEY `security_events_user_id_event_type_index` (`user_id`,`event_type`),
  KEY `security_events_event_type_severity_index` (`event_type`,`severity`),
  KEY `security_events_severity_status_index` (`severity`,`status`),
  KEY `security_events_created_at_severity_index` (`created_at`,`severity`),
  KEY `security_events_status_index` (`status`),
  KEY `security_events_expires_at_index` (`expires_at`),
  FULLTEXT KEY `security_events_description_fulltext` (`description`),
  CONSTRAINT `security_events_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `security_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trait_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trait_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL COMMENT 'Hex color code for category display',
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `collection_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trait_categories_slug_unique` (`slug`),
  KEY `trait_categories_collection_id_sort_order_index` (`collection_id`,`sort_order`),
  KEY `trait_categories_slug_is_system_index` (`slug`,`is_system`),
  CONSTRAINT `trait_categories_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trait_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trait_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `display_type` enum('text','number','percentage','date','boost_number') NOT NULL DEFAULT 'text',
  `unit` varchar(20) DEFAULT NULL,
  `allowed_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowed_values`)),
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `collection_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trait_types_category_id_slug_collection_id_unique` (`category_id`,`slug`,`collection_id`),
  KEY `trait_types_category_id_slug_index` (`category_id`,`slug`),
  KEY `trait_types_collection_id_is_system_index` (`collection_id`,`is_system`),
  CONSTRAINT `trait_types_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `trait_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trait_types_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(120) NOT NULL,
  `category` enum('authentication','authentication_login','authentication_logout','registration','gdpr_actions','data_access','data_deletion','content_creation','content_modification','platform_usage','system_interaction','security_events','blockchain_activity','media_management','privacy_management','personal_data_update','wallet_management','notification_management') DEFAULT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `privacy_level` enum('standard','high','critical','immutable') NOT NULL DEFAULT 'standard',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activities_user_id_category_index` (`user_id`,`category`),
  KEY `user_activities_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `user_activities_category_created_at_index` (`category`,`created_at`),
  KEY `user_activities_privacy_level_created_at_index` (`privacy_level`,`created_at`),
  KEY `user_activities_expires_at_index` (`expires_at`),
  KEY `user_activities_created_at_index` (`created_at`),
  KEY `user_activities_user_id_category_created_at_index` (`user_id`,`category`,`created_at`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_consent_confirmations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_consent_confirmations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_consent_id` bigint(20) unsigned NOT NULL,
  `notification_id` char(32) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `confirmation_method` varchar(255) NOT NULL DEFAULT 'notification_click',
  `confirmed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_consent_confirmations_user_id_foreign` (`user_id`),
  KEY `user_consent_confirmations_user_consent_id_foreign` (`user_consent_id`),
  CONSTRAINT `user_consent_confirmations_user_consent_id_foreign` FOREIGN KEY (`user_consent_id`) REFERENCES `user_consents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_consent_confirmations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_consents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_consents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `consent_version_id` bigint(20) unsigned NOT NULL,
  `consent_type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `granted` tinyint(1) NOT NULL,
  `legal_basis` varchar(50) NOT NULL,
  `withdrawal_method` varchar(50) DEFAULT NULL,
  `withdrawn_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_consents_consent_version_id_foreign` (`consent_version_id`),
  KEY `user_consents_user_id_consent_type_index` (`user_id`,`consent_type`),
  KEY `user_consents_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `user_consents_consent_type_granted_index` (`consent_type`,`granted`),
  KEY `user_consents_created_at_index` (`created_at`),
  CONSTRAINT `user_consents_consent_version_id_foreign` FOREIGN KEY (`consent_version_id`) REFERENCES `consent_versions` (`id`),
  CONSTRAINT `user_consents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `doc_typo` varchar(255) DEFAULT NULL,
  `doc_num` varchar(255) DEFAULT NULL,
  `doc_issue_date` date DEFAULT NULL,
  `doc_expired_date` date DEFAULT NULL,
  `doc_issue_from` varchar(255) DEFAULT NULL,
  `doc_photo_path_f` varchar(255) DEFAULT NULL,
  `doc_photo_path_r` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected','expired') NOT NULL DEFAULT 'pending',
  `is_encrypted` tinyint(1) NOT NULL DEFAULT 1,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_documents_user_id_unique` (`user_id`),
  CONSTRAINT `user_documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_invoice_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_invoice_preferences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `invoice_name` varchar(255) DEFAULT NULL,
  `invoice_fiscal_code` varchar(255) DEFAULT NULL,
  `invoice_vat_number` varchar(255) DEFAULT NULL,
  `invoice_address` varchar(255) DEFAULT NULL,
  `invoice_city` varchar(255) DEFAULT NULL,
  `invoice_country` varchar(2) DEFAULT NULL,
  `can_issue_invoices` tinyint(1) NOT NULL DEFAULT 0,
  `auto_request_invoice` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_invoice_preferences_user_id_unique` (`user_id`),
  CONSTRAINT `user_invoice_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_organization_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_organization_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `org_name` varchar(255) DEFAULT NULL,
  `org_email` varchar(255) DEFAULT NULL,
  `org_street` varchar(255) DEFAULT NULL,
  `org_city` varchar(255) DEFAULT NULL,
  `org_region` varchar(255) DEFAULT NULL,
  `org_state` varchar(255) DEFAULT NULL,
  `org_zip` varchar(255) DEFAULT NULL,
  `org_site_url` varchar(255) DEFAULT NULL,
  `org_phone_1` varchar(255) DEFAULT NULL,
  `org_phone_2` varchar(255) DEFAULT NULL,
  `org_phone_3` varchar(255) DEFAULT NULL,
  `rea` varchar(255) DEFAULT NULL,
  `org_fiscal_code` varchar(255) DEFAULT NULL,
  `org_vat_number` varchar(255) DEFAULT NULL,
  `is_seller_verified` tinyint(1) NOT NULL DEFAULT 0,
  `can_issue_invoices` tinyint(1) NOT NULL DEFAULT 0,
  `business_type` enum('individual','sole_proprietorship','partnership','corporation','non_profit','other') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_organization_data_user_id_unique` (`user_id`),
  KEY `user_organization_data_org_vat_number_index` (`org_vat_number`),
  CONSTRAINT `user_organization_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_personal_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_personal_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `province` varchar(10) DEFAULT NULL,
  `home_phone` varchar(255) DEFAULT NULL,
  `cell_phone` varchar(255) DEFAULT NULL,
  `work_phone` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other','prefer_not_say') DEFAULT NULL,
  `fiscal_code` varchar(255) DEFAULT NULL,
  `tax_id_number` varchar(255) DEFAULT NULL,
  `allow_personal_data_processing` tinyint(1) NOT NULL DEFAULT 0,
  `processing_purposes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`processing_purposes`)),
  `consent_updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_personal_data_user_id_unique` (`user_id`),
  KEY `user_personal_data_fiscal_code_index` (`fiscal_code`),
  CONSTRAINT `user_personal_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `job_role` varchar(255) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `social_x` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `snapchat` varchar(255) DEFAULT NULL,
  `twitch` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `discord` varchar(255) DEFAULT NULL,
  `telegram` varchar(255) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `annotation` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_profiles_user_id_unique` (`user_id`),
  CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `nick_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `preferred_currency` varchar(3) NOT NULL DEFAULT 'EUR' COMMENT 'Preferred currency for price display',
  `avatar_url` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `usertype` varchar(20) NOT NULL DEFAULT 'creator',
  `current_collection_id` bigint(20) unsigned DEFAULT NULL,
  `consent_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`consent_summary`)),
  `consents_updated_at` timestamp NULL DEFAULT NULL,
  `processing_limitations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`processing_limitations`)),
  `limitations_updated_at` timestamp NULL DEFAULT NULL,
  `has_pending_gdpr_requests` tinyint(1) NOT NULL DEFAULT 0,
  `last_gdpr_request_at` timestamp NULL DEFAULT NULL,
  `gdpr_compliant` tinyint(1) NOT NULL DEFAULT 1,
  `gdpr_status_updated_at` timestamp NULL DEFAULT NULL,
  `data_retention_until` timestamp NULL DEFAULT NULL,
  `retention_reason` enum('active_user','legal_obligation','pending_request','contract_obligation') NOT NULL DEFAULT 'active_user',
  `privacy_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`privacy_settings`)),
  `preferred_communication_method` varchar(20) NOT NULL DEFAULT 'email',
  `last_activity_logged_at` timestamp NULL DEFAULT NULL,
  `total_gdpr_requests` int(10) unsigned NOT NULL DEFAULT 0,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `created_via` varchar(100) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `wallet` text DEFAULT NULL,
  `personal_secret` varchar(255) DEFAULT NULL,
  `is_weak_auth` tinyint(1) DEFAULT NULL,
  `wallet_balance` decimal(20,4) DEFAULT 0.0000,
  `consent` tinyint(1) DEFAULT 0,
  `icon_style` varchar(20) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nick_name_unique` (`nick_name`),
  KEY `users_has_pending_gdpr_requests_index` (`has_pending_gdpr_requests`),
  KEY `users_gdpr_compliant_index` (`gdpr_compliant`),
  KEY `users_last_gdpr_request_at_index` (`last_gdpr_request_at`),
  KEY `users_data_retention_until_index` (`data_retention_until`),
  KEY `users_gdpr_compliant_has_pending_gdpr_requests_index` (`gdpr_compliant`,`has_pending_gdpr_requests`),
  KEY `users_preferred_currency_index` (`preferred_currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `utilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `egi_id` bigint(20) unsigned NOT NULL,
  `type` enum('physical','service','hybrid','digital') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `requires_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `shipping_type` varchar(50) DEFAULT NULL,
  `estimated_shipping_days` int(11) DEFAULT NULL,
  `weight` decimal(10,3) DEFAULT NULL,
  `dimensions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dimensions`)),
  `fragile` tinyint(1) NOT NULL DEFAULT 0,
  `insurance_recommended` tinyint(1) NOT NULL DEFAULT 0,
  `shipping_notes` text DEFAULT NULL,
  `escrow_tier` enum('immediate','standard','premium') NOT NULL DEFAULT 'standard',
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `max_uses` int(11) DEFAULT NULL,
  `current_uses` int(11) NOT NULL DEFAULT 0,
  `activation_instructions` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `utilities_egi_id_unique` (`egi_id`),
  KEY `utilities_type_index` (`type`),
  KEY `utilities_status_index` (`status`),
  KEY `utilities_escrow_tier_index` (`escrow_tier`),
  CONSTRAINT `utilities_egi_id_foreign` FOREIGN KEY (`egi_id`) REFERENCES `egis` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `collection_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `notification_payload_wallets_id` bigint(20) unsigned DEFAULT NULL,
  `wallet` varchar(255) DEFAULT NULL,
  `platform_role` varchar(25) DEFAULT NULL,
  `royalty_mint` double DEFAULT NULL,
  `royalty_rebind` double DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wallets_collection_id_foreign` (`collection_id`),
  KEY `wallets_user_id_foreign` (`user_id`),
  KEY `wallets_notification_payload_wallets_id_foreign` (`notification_payload_wallets_id`),
  KEY `wallet_collection_index` (`wallet`,`collection_id`),
  CONSTRAINT `wallets_collection_id_foreign` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wallets_notification_payload_wallets_id_foreign` FOREIGN KEY (`notification_payload_wallets_id`) REFERENCES `notification_payload_wallets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wallets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*M!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2024_01_15_000001_create_consent_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2024_01_15_000002_create_consent_versions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2024_01_15_000002_create_privacy_policies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2024_01_15_000003_add_many_columns_in_privacy_polices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2024_01_15_000003_create_data_retention_policies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_01_15_000004_create_user_consents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2024_01_15_000005_create_gdpr_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2024_01_15_000006_create_data_exports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2024_01_15_000007_create_user_activities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2024_01_15_000008_create_breach_reports_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2024_01_15_000009_create_privacy_policy_acceptances_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2024_01_15_000010_create_processing_restrictions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2024_01_15_000011_create_consent_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2024_01_15_000012_create_anonymized_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2024_01_15_000013_create_security_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_01_15_000014_create_dpo_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_01_15_000015_create_gdpr_audit_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_11_07_163525_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2024_11_14_090414_create_collections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2024_11_18_122016_create_icons_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2024_12_10_171308_create_egis_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2024_12_10_171834_create_egi_audits_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2024_12_23_143829_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2024_12_27_102951_create_collection_user_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2024_12_27_104339_create_wallets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2024_12_28_131757_create_notification_payload_invitations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_03_29_193100_create_error_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_05_02_120902_create_likes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_05_02_120944_create_reservations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_05_02_132224_create_epp_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_05_16_074109_create_egi_reservation_certificates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_05_26_111141_create_user_domain_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_06_06_174745_add_status_and_withdrawn_at_to_user_consents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_06_10_125504_create_gdpr_notification_payloads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_06_12_093927_create_user_consent_confirmations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_07_02_161852_create_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_07_02_162246_create_biographies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_07_02_162305_create_biography_chapters_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_08_15_095926_create_notification_payload_reservations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_11_13_165429_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_08_20_095612_create_payments_distributions_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_08_20_200853_fix_offer_amount_algo_column_size',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_08_20_201116_fix_offer_amount_algo_in_certificates_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_08_24_185729_add_nick_name_to_users_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_08_24_191602_add_unique_constraint_to_nick_name',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_08_25_092337_fix_consent_types_for_personal_data_processing',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_08_29_192005_create_utilities_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_08_30_080203_create_trait_categories_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_08_30_080211_create_trait_types_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_08_30_080216_create_egi_traits_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_08_31_112243_add_color_to_trait_categories_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_08_31_192021_fix_utilities_foreign_key_constraint',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2025_09_01_122310_add_media_support_to_traits',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2024_12_27_104330_create_notification_payload_wallets_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2025_09_05_103324_add_metadata_to_notification_payload_invitations_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2025_09_05_195956_add_collaboration_participation_consent_type',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2025_09_06_124258_add_wallet_management_to_user_activities_category_enum',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2025_09_06_151754_add_notification_management_to_user_activities_category_enum',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2025_09_18_163532_create_coa_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_09_18_163541_create_coa_snapshot_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_09_18_163549_create_coa_files_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_09_18_163557_create_coa_signatures_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2025_09_18_163607_create_egi_traits_version_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2025_09_18_164915_create_coa_annexes_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2025_09_18_164922_create_coa_events_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2025_09_18_164933_update_coa_files_kind_enum',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2025_09_18_194602_add_cryptographic_fields_to_coa_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2025_09_18_203636_add_creator_info_to_coa_table',20);
