-- Tenants data import generated from local database

SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `tenants` (`id`, `name`, `slug`, `code`, `entity_type`, `email`, `phone`, `address`, `vat_number`, `settings`, `is_active`, `trial_ends_at`, `subscription_ends_at`, `notes`, `created_at`, `updated_at`, `data`, `deleted_at`)
VALUES (1, 'Florence EGI', 'florence-egi', 'FEGI', 'company', NULL, NULL, NULL, NULL, '{"features": {"marketplace": true, "nft_minting": true}}', 1, NULL, NULL, NULL, '2025-11-04 21:28:37', '2025-11-04 21:28:37', NULL, NULL)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `slug`=VALUES(`slug`), `code`=VALUES(`code`), `entity_type`=VALUES(`entity_type`), `email`=VALUES(`email`), `phone`=VALUES(`phone`), `address`=VALUES(`address`), `vat_number`=VALUES(`vat_number`), `settings`=VALUES(`settings`), `is_active`=VALUES(`is_active`), `trial_ends_at`=VALUES(`trial_ends_at`), `subscription_ends_at`=VALUES(`subscription_ends_at`), `notes`=VALUES(`notes`), `created_at`=VALUES(`created_at`), `updated_at`=VALUES(`updated_at`), `data`=VALUES(`data`), `deleted_at`=VALUES(`deleted_at`);

INSERT INTO `tenants` (`id`, `name`, `slug`, `code`, `entity_type`, `email`, `phone`, `address`, `vat_number`, `settings`, `is_active`, `trial_ends_at`, `subscription_ends_at`, `notes`, `created_at`, `updated_at`, `data`, `deleted_at`)
VALUES (2, 'Comune di Firenze', 'comunedifirenze', NULL, 'pa', 'comune.di.firenze@gmail.com', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, '2025-11-05 08:19:35', '2025-11-05 08:19:35', NULL, NULL)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `slug`=VALUES(`slug`), `code`=VALUES(`code`), `entity_type`=VALUES(`entity_type`), `email`=VALUES(`email`), `phone`=VALUES(`phone`), `address`=VALUES(`address`), `vat_number`=VALUES(`vat_number`), `settings`=VALUES(`settings`), `is_active`=VALUES(`is_active`), `trial_ends_at`=VALUES(`trial_ends_at`), `subscription_ends_at`=VALUES(`subscription_ends_at`), `notes`=VALUES(`notes`), `created_at`=VALUES(`created_at`), `updated_at`=VALUES(`updated_at`), `data`=VALUES(`data`), `deleted_at`=VALUES(`deleted_at`);

SET FOREIGN_KEY_CHECKS=1;
