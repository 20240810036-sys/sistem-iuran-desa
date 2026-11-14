-- =============================================================
-- Sistem Informasi Pengelolaan Iuran Sampah Desa
-- database.sql – Schema + Sample Data
-- =============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- Drop tables (urutan terbalik karena FK)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `pembayaran`;
DROP TABLE IF EXISTS `tagihan`;
DROP TABLE IF EXISTS `warga`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `jobs`;

-- -------------------------------------------------------------
-- Table: users
-- -------------------------------------------------------------
CREATE TABLE `users` (
  `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(100)    NOT NULL,
  `email`             VARCHAR(255)    NOT NULL,
  `email_verified_at` TIMESTAMP       NULL,
  `password`          VARCHAR(255)    NOT NULL,
  `role`              ENUM('admin','warga') NOT NULL DEFAULT 'warga',
  `remember_token`    VARCHAR(100)    NULL,
  `created_at`        TIMESTAMP       NULL,
  `updated_at`        TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table: warga
-- -------------------------------------------------------------
CREATE TABLE `warga` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `nama`       VARCHAR(100)    NOT NULL,
  `alamat`     TEXT            NOT NULL,
  `no_hp`      VARCHAR(15)     NOT NULL,
  `created_at` TIMESTAMP       NULL,
  `updated_at` TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `warga_user_id_foreign` (`user_id`),
  CONSTRAINT `warga_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table: tagihan
-- -------------------------------------------------------------
CREATE TABLE `tagihan` (
  `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `bulan`        TINYINT UNSIGNED NOT NULL COMMENT '1-12',
  `tahun`        SMALLINT UNSIGNED NOT NULL,
  `jumlah_iuran` INT UNSIGNED NOT NULL COMMENT 'Dalam rupiah',
  `created_at`   TIMESTAMP NULL,
  `updated_at`   TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tagihan_bulan_tahun_unique` (`bulan`, `tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table: pembayaran
-- -------------------------------------------------------------
CREATE TABLE `pembayaran` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `warga_id`      BIGINT UNSIGNED NOT NULL,
  `tagihan_id`    BIGINT UNSIGNED NOT NULL,
  `tanggal_bayar` DATE            NOT NULL,
  `jumlah_bayar`  INT UNSIGNED    NOT NULL,
  `status`        ENUM('lunas','belum_lunas') NOT NULL DEFAULT 'belum_lunas',
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `pembayaran_warga_status` (`warga_id`, `status`),
  KEY `pembayaran_tagihan_status` (`tagihan_id`, `status`),
  CONSTRAINT `pembayaran_warga_id_foreign`
    FOREIGN KEY (`warga_id`)   REFERENCES `warga` (`id`)   ON DELETE CASCADE,
  CONSTRAINT `pembayaran_tagihan_id_foreign`
    FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Table: sessions (Laravel default)
-- -------------------------------------------------------------
CREATE TABLE `sessions` (
  `id`            VARCHAR(255)    NOT NULL,
  `user_id`       BIGINT UNSIGNED NULL,
  `ip_address`    VARCHAR(45)     NULL,
  `user_agent`    TEXT            NULL,
  `payload`       LONGTEXT        NOT NULL,
  `last_activity` INT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- SAMPLE DATA
-- Password semua akun: "password" (bcrypt hash)
-- =============================================================

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin Desa',    'admin@desa.id',    '$2y$12$GFMjJ7tTUEWR0.fLHiemMu8bwH6sC4SqTqJWnMFSVuHV2L0J8WJXK', 'admin', NOW(), NOW()),
(2, 'Budi Santoso',  'budi@warga.id',   '$2y$12$GFMjJ7tTUEWR0.fLHiemMu8bwH6sC4SqTqJWnMFSVuHV2L0J8WJXK', 'warga', NOW(), NOW()),
(3, 'Siti Rahayu',   'siti@warga.id',   '$2y$12$GFMjJ7tTUEWR0.fLHiemMu8bwH6sC4SqTqJWnMFSVuHV2L0J8WJXK', 'warga', NOW(), NOW()),
(4, 'Ahmad Fauzi',   'ahmad@warga.id',  '$2y$12$GFMjJ7tTUEWR0.fLHiemMu8bwH6sC4SqTqJWnMFSVuHV2L0J8WJXK', 'warga', NOW(), NOW()),
(5, 'Dewi Lestari',  'dewi@warga.id',   '$2y$12$GFMjJ7tTUEWR0.fLHiemMu8bwH6sC4SqTqJWnMFSVuHV2L0J8WJXK', 'warga', NOW(), NOW()),
(6, 'Hendra Gunawan','hendra@warga.id', '$2y$12$GFMjJ7tTUEWR0.fLHiemMu8bwH6sC4SqTqJWnMFSVuHV2L0J8WJXK', 'warga', NOW(), NOW());

INSERT INTO `warga` (`id`, `user_id`, `nama`, `alamat`, `no_hp`, `created_at`, `updated_at`) VALUES
(1, 2, 'Budi Santoso',   'Jl. Melati No. 12, RT 01/RW 02',  '081234567801', NOW(), NOW()),
(2, 3, 'Siti Rahayu',    'Jl. Mawar No. 5, RT 02/RW 01',    '081234567802', NOW(), NOW()),
(3, 4, 'Ahmad Fauzi',    'Jl. Kenanga No. 8, RT 03/RW 02',  '081234567803', NOW(), NOW()),
(4, 5, 'Dewi Lestari',   'Jl. Anggrek No. 15, RT 01/RW 03', '081234567804', NOW(), NOW()),
(5, 6, 'Hendra Gunawan', 'Jl. Dahlia No. 3, RT 04/RW 01',   '081234567805', NOW(), NOW());

INSERT INTO `tagihan` (`id`, `bulan`, `tahun`, `jumlah_iuran`, `created_at`, `updated_at`) VALUES
(1, 1, 2025, 15000, NOW(), NOW()),
(2, 2, 2025, 15000, NOW(), NOW()),
(3, 3, 2025, 15000, NOW(), NOW()),
(4, 4, 2025, 15000, NOW(), NOW()),
(5, 5, 2025, 15000, NOW(), NOW()),
(6, 6, 2025, 15000, NOW(), NOW());

INSERT INTO `pembayaran` (`warga_id`, `tagihan_id`, `tanggal_bayar`, `jumlah_bayar`, `status`, `created_at`, `updated_at`) VALUES
-- Budi
(1, 1, '2025-01-05', 15000, 'lunas', NOW(), NOW()),
(1, 2, '2025-02-07', 15000, 'lunas', NOW(), NOW()),
(1, 3, '2025-03-04', 15000, 'lunas', NOW(), NOW()),
(1, 4, '2025-04-06', 15000, 'lunas', NOW(), NOW()),
(1, 5, '2025-05-03', 15000, 'lunas', NOW(), NOW()),
(1, 6, '2025-06-08', 15000, 'belum_lunas', NOW(), NOW()),
-- Siti
(2, 1, '2025-01-10', 15000, 'lunas', NOW(), NOW()),
(2, 2, '2025-02-12', 15000, 'lunas', NOW(), NOW()),
(2, 3, '2025-03-11', 15000, 'belum_lunas', NOW(), NOW()),
(2, 4, '2025-04-09', 15000, 'lunas', NOW(), NOW()),
(2, 5, '2025-05-14', 15000, 'lunas', NOW(), NOW()),
(2, 6, '2025-06-10', 15000, 'belum_lunas', NOW(), NOW()),
-- Ahmad
(3, 1, '2025-01-03', 15000, 'lunas', NOW(), NOW()),
(3, 2, '2025-02-05', 15000, 'lunas', NOW(), NOW()),
(3, 3, '2025-03-06', 15000, 'lunas', NOW(), NOW()),
(3, 4, '2025-04-04', 15000, 'belum_lunas', NOW(), NOW()),
(3, 5, '2025-05-07', 15000, 'lunas', NOW(), NOW()),
(3, 6, '2025-06-06', 15000, 'lunas', NOW(), NOW()),
-- Dewi
(4, 1, '2025-01-08', 15000, 'lunas', NOW(), NOW()),
(4, 2, '2025-02-09', 15000, 'belum_lunas', NOW(), NOW()),
(4, 3, '2025-03-07', 15000, 'lunas', NOW(), NOW()),
(4, 4, '2025-04-11', 15000, 'lunas', NOW(), NOW()),
(4, 5, '2025-05-10', 15000, 'lunas', NOW(), NOW()),
(4, 6, '2025-06-09', 15000, 'belum_lunas', NOW(), NOW()),
-- Hendra
(5, 1, '2025-01-15', 15000, 'belum_lunas', NOW(), NOW()),
(5, 2, '2025-02-14', 15000, 'lunas', NOW(), NOW()),
(5, 3, '2025-03-13', 15000, 'lunas', NOW(), NOW()),
(5, 4, '2025-04-12', 15000, 'lunas', NOW(), NOW()),
(5, 5, '2025-05-11', 15000, 'belum_lunas', NOW(), NOW()),
(5, 6, '2025-06-13', 15000, 'lunas', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
