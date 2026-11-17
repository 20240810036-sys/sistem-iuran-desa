-- ============================================================
-- REVISI: Sistem Pembayaran Digital Iuran Sampah
-- Jalankan SETELAH php artisan migrate (atau gunakan ini untuk deploy manual)
-- ============================================================

-- 1. Ubah kolom status di tabel pembayaran (tambah nilai baru)
ALTER TABLE `pembayaran`
    MODIFY COLUMN `status` ENUM('menunggu_verifikasi','lunas','ditolak','belum_lunas')
        NOT NULL DEFAULT 'menunggu_verifikasi';

-- 2. Tambah kolom baru di tabel pembayaran
ALTER TABLE `pembayaran`
    ADD COLUMN `payment_method`   ENUM('transfer_bank','qris','ewallet','tunai') NULL AFTER `status`,
    ADD COLUMN `payment_channel`  VARCHAR(100) NULL AFTER `payment_method`,
    ADD COLUMN `proof_of_payment` VARCHAR(255) NULL AFTER `payment_channel`,
    ADD COLUMN `verification_note` TEXT NULL AFTER `proof_of_payment`;

-- 3. Buat tabel payment_settings
CREATE TABLE IF NOT EXISTS `payment_settings` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type`           ENUM('bank','ewallet','qris') NOT NULL,
    `provider_name`  VARCHAR(100) NOT NULL,
    `account_number` VARCHAR(50)  NULL,
    `account_name`   VARCHAR(100) NULL,
    `qr_image`       VARCHAR(255) NULL,
    `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`     TIMESTAMP    NULL,
    `updated_at`     TIMESTAMP    NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seeder data awal (opsional — bisa juga via php artisan db:seed)
-- ============================================================
INSERT INTO `payment_settings` (`type`, `provider_name`, `account_number`, `account_name`, `is_active`, `created_at`, `updated_at`) VALUES
('bank',    'BCA',       '1234567890',  'Bendahara RT', 1, NOW(), NOW()),
('bank',    'BRI',       '0987654321',  'Bendahara RT', 1, NOW(), NOW()),
('bank',    'BNI',       '1122334455',  'Bendahara RT', 0, NOW(), NOW()),
('bank',    'Mandiri',   '5566778899',  'Bendahara RT', 0, NOW(), NOW()),
('ewallet', 'DANA',      '08123456789', 'Bendahara RT', 1, NOW(), NOW()),
('ewallet', 'OVO',       '08123456789', 'Bendahara RT', 1, NOW(), NOW()),
('ewallet', 'GoPay',     '08123456789', 'Bendahara RT', 0, NOW(), NOW()),
('ewallet', 'ShopeePay', '08123456789', 'Bendahara RT', 0, NOW(), NOW()),
('qris',    'QRIS',      NULL,          NULL,           1, NOW(), NOW());
