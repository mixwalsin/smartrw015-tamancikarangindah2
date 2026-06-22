-- Migration 003: Kas Module for RW/RT finance

CREATE TABLE IF NOT EXISTS `kas_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `kas_type` ENUM('rw', 'rt') NOT NULL,
    `transaction_type` ENUM('pemasukan', 'pengeluaran') NOT NULL,
    `description` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kas_categories_type` (`kas_type`, `transaction_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kas_transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kas_type` ENUM('rw', 'rt') NOT NULL,
    `rt_id` TINYINT UNSIGNED NULL,
    `transaction_type` ENUM('pemasukan', 'pengeluaran') NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `description` TEXT NULL,
    `date` DATE NOT NULL,
    `created_by` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `bukti_file` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kas_tx_type` (`kas_type`, `transaction_type`),
    INDEX `idx_kas_tx_date` (`date`),
    INDEX `idx_kas_tx_status` (`status`),
    CONSTRAINT `fk_kas_tx_category` FOREIGN KEY (`category_id`) REFERENCES `kas_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_kas_tx_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_kas_tx_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_kas_tx_rt` FOREIGN KEY (`rt_id`) REFERENCES `rt` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kas_balance` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kas_type` ENUM('rw', 'rt') NOT NULL,
    `rt_id` TINYINT UNSIGNED NULL,
    `balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `last_updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uq_kas_balance` (`kas_type`, `rt_id`),
    INDEX `idx_kas_balance_type` (`kas_type`),
    CONSTRAINT `fk_kas_balance_rt` FOREIGN KEY (`rt_id`) REFERENCES `rt` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kas_balance_history` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kas_type` ENUM('rw', 'rt') NOT NULL,
    `rt_id` TINYINT UNSIGNED NULL,
    `balance` DECIMAL(15,2) NOT NULL,
    `transaction_id` BIGINT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_kas_balance_history_type` (`kas_type`, `rt_id`),
    INDEX `idx_kas_balance_history_created` (`created_at`),
    CONSTRAINT `fk_kas_balance_history_rt` FOREIGN KEY (`rt_id`) REFERENCES `rt` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_kas_balance_history_tx` FOREIGN KEY (`transaction_id`) REFERENCES `kas_transactions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
