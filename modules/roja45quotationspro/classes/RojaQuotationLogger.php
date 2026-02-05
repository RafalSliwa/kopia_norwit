<?php
/**
 * RojaQuotationLogger.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  RojaQuotation
 *
 * @link      https://toolecommerce.com/
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * RojaQuotationLogger.
 * 2023 TOOLE - Inter-soft.com
 * All rights reserved.
 *
 * DISCLAIMER
 *
 * Changing this file will render any support provided by us null and void.
 *
 * @author    Toole <support@toole.com>
 * @copyright 2023 TOOLE - Inter-soft.com
 * @license   license.txt
 * @category  TooleAmazonMarketTool
 */

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RojaQuotationLogger
{
    private static Logger $logger;

    private static function getLogger($level)
    {
        if (!isset(self::$logger)) {
            self::$logger = new Logger("quotations-logger");

            $formatter = new JsonFormatter();
            $rotating_handler = new RotatingFileHandler(__DIR__ . "/../log/quotationspro.log", 7);
            $rotating_handler->setFormatter($formatter);

            if (Configuration::get('ROJA45_QUOTATIONSPRO_ENABLELOG', null, null, null, false)) {
                self::$logger->pushHandler($rotating_handler);
            } else {
                self::$logger->pushHandler(new \Monolog\Handler\NullHandler());
            }
        }

        return self::$logger;
    }

    public static function disableLogging()
    {
        self::pushHandler(new \Monolog\Handler\NullHandler());
    }

    public static function info($message, $context = [], $level = Logger::INFO)
    {
        self::getLogger($level)->info($message, $context);
    }

    public static function debug($message, $context = [], $level = Logger::DEBUG)
    {
        self::getLogger($level)->debug($message, $context);
    }

    public static function error($message, $context = [], $level = Logger::DEBUG)
    {
        self::getLogger($level)->error($message, $context);
    }
}