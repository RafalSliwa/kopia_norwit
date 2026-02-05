-- NorwitGCLID - tabele instalacyjne
-- Obsługuje: gclid, wbraid (iOS), gbraid, ga_client_id

-- Główna tabela tracking
CREATE TABLE IF NOT EXISTS `PREFIX_norwit_gclid` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `gclid` VARCHAR(255) DEFAULT NULL,
    `wbraid` VARCHAR(255) DEFAULT NULL,
    `gbraid` VARCHAR(255) DEFAULT NULL,
    `ga_client_id` VARCHAR(255) DEFAULT NULL,
    `session_id` VARCHAR(255) DEFAULT NULL,
    `zadarma_number` VARCHAR(50) DEFAULT NULL,
    `phone_displayed` VARCHAR(50) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `landing_page` TEXT DEFAULT NULL,
    `referrer` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    `conversion_sent` TINYINT(1) DEFAULT 0,
    `conversion_value` DECIMAL(10,2) DEFAULT NULL,
    `conversion_type` VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_gclid` (`gclid`),
    INDEX `idx_wbraid` (`wbraid`),
    INDEX `idx_gbraid` (`gbraid`),
    INDEX `idx_ga_client_id` (`ga_client_id`),
    INDEX `idx_zadarma_number` (`zadarma_number`),
    INDEX `idx_session_id` (`session_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kolejka konwersji do wysłania do Google Ads
CREATE TABLE IF NOT EXISTS `PREFIX_norwit_conversion_queue` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `gclid_record_id` INT(11) NOT NULL,
    `gclid` VARCHAR(255) DEFAULT NULL,
    `wbraid` VARCHAR(255) DEFAULT NULL,
    `gbraid` VARCHAR(255) DEFAULT NULL,
    `conversion_action` VARCHAR(50) NOT NULL,
    `conversion_value` DECIMAL(10,2) NOT NULL,
    `call_datetime` DATETIME NOT NULL,
    `call_duration` INT(11) DEFAULT 0,
    `status` ENUM('pending', 'sent', 'error') DEFAULT 'pending',
    `error_message` TEXT DEFAULT NULL,
    `retry_count` INT(11) DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    `sent_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_gclid_record_id` (`gclid_record_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
