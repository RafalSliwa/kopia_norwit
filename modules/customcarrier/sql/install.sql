-- Custom Carrier Module - Installation SQL
-- Table for product transport settings

CREATE TABLE IF NOT EXISTS `PREFIX_customcarrier_product` (
    `id_customcarrier_product` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_product` INT(11) UNSIGNED NOT NULL,
    `free_shipping` TINYINT(1) NOT NULL DEFAULT 0,
    `base_shipping_cost` DECIMAL(20,6) NOT NULL DEFAULT 0.000000,
    `multiply_by_quantity` TINYINT(1) NOT NULL DEFAULT 0,
    `free_shipping_quantity` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `apply_threshold` TINYINT(1) NOT NULL DEFAULT 0,
    `separate_package` TINYINT(1) NOT NULL DEFAULT 0,
    `exclude_from_free_shipping` TINYINT(1) NOT NULL DEFAULT 0,
    `max_quantity_per_package` INT(11) DEFAULT NULL COMMENT 'Maksymalna ilość produktu w jednej paczce',
    `max_weight_per_package` DECIMAL(20,6) DEFAULT NULL COMMENT 'Maksymalna waga na paczkę (kg)',
    `max_packages` INT(11) DEFAULT NULL COMMENT 'Maksymalna liczba paczek',
    `cost_above_max_packages` DECIMAL(20,6) DEFAULT NULL COMMENT 'Koszt palety gdy paczki > max_packages',
    `free_shipping_from_price` DECIMAL(20,6) DEFAULT NULL COMMENT 'Darmowa wysyłka gdy cena produktu >= tej wartości',
    `date_add` DATETIME NOT NULL,
    `date_upd` DATETIME NOT NULL,
    PRIMARY KEY (`id_customcarrier_product`),
    UNIQUE KEY `id_product` (`id_product`),
    KEY `idx_free_shipping` (`free_shipping`),
    KEY `idx_separate_package` (`separate_package`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
