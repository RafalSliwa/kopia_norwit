<?php

/**
 * Replaces the string order reference with numeric one
 *
 * @package   gmnumeric
 * @author    Dariusz Tryba (contact@greenmousestudio.com)
 * @copyright Copyright (c) Green Mouse Studio (http://www.greenmousestudio.com)
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class Order extends OrderCore
{

    public static function generateReference()
    {
        if (!Module::isEnabled('gmnumeric')) {
            return parent::generateReference();
        }
        $isRandom = Configuration::get('GMNUMERIC_RANDOM');
        $prefix = Configuration::get('GMNUMERIC_PREFIX');
        $prefixLength = strlen($prefix);
        $restLength = 9 - $prefixLength;
        if ($isRandom) {
            $reference = Tools::passwdGen($restLength, 'NUMERIC');
        } else {
            $query = 'SELECT `AUTO_INCREMENT`
                     FROM INFORMATION_SCHEMA.TABLES
                 WHERE TABLE_SCHEMA = \''._DB_NAME_.'\'
                 AND TABLE_NAME = \''._DB_PREFIX_.'orders\'';
            $nextAutoInc = (int) Db::getInstance()->getValue($query);
            $maxQuery = 'SELECT MAX(`id_order`) FROM `'._DB_PREFIX_.'orders`';
            $maxOrderId = (int) Db::getInstance()->getValue($maxQuery);
            $nextOrderId = max($maxOrderId + 1, $nextAutoInc);
            $zeros = Configuration::get('GMNUMERIC_ZEROS');
            if ($zeros == 'on') {
                $reference = sprintf('%0'.$restLength.'d', $nextOrderId);
            } else {
                $reference = $nextOrderId;
            }
        }
        return $prefix.$reference;
    }
}
