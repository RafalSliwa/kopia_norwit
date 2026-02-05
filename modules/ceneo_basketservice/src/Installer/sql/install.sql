CREATE TABLE IF NOT EXISTS `_DB_PREFIX_ceneo_bs`
(
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `ceneo_order_id` TEXT DEFAULT NULL,
    `shop_order_id` INT(11) UNSIGNED DEFAULT NULL,
    `downloaded_at` TEXT DEFAULT NULL,
   `shipping_method` TEXT DEFAULT NULL,
   `payment_method` TEXT DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = _MYSQL_ENGINE_
  DEFAULT CHARSET = UTF8;


CREATE TABLE IF NOT EXISTS `_DB_PREFIX_ceneo_bs_delivery`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(266) DEFAULT NULL,
    `carrier_id` int(11) DEFAULT NULL,
    `countries` varchar(266) DEFAULT NULL,
    `ceneo_carrier_id` varchar(266) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = _MYSQL_ENGINE_
  DEFAULT CHARSET = UTF8;



CREATE TABLE IF NOT EXISTS `_DB_PREFIX_ceneo_bs_carriers`
(
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `carrier_id` INT(11) DEFAULT NULL,
    `ceneo_carrier_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = _MYSQL_ENGINE_
  DEFAULT CHARSET = UTF8;


