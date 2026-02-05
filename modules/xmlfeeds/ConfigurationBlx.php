<?php
/**
 * 2010-2025 Bl Modules.
 *
 * If you wish to customize this module for your needs,
 * please contact the authors first for more information.
 *
 * It's not allowed selling, reselling or other ways to share
 * this file or any other module files without author permission.
 *
 * @author    Bl Modules
 * @copyright 2010-2025 Bl Modules
 * @license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ConfigurationBlx
{
    public static function update($name, $value)
    {
        if (is_object($value)) {
            $value = (array)$value;
        }

        if (is_array($value)) {
            $paramsS = json_encode($value);
        } else {
            $paramsS = $value;
        }

        self::delete($name);

        return Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'blmod_xml_configuration
            (`name`, `value`, `updated_at`)
            VALUES
            ("'.pSQL($name).'", "'.pSQL($paramsS).'", "'.pSQL(date('Y-m-d H:i:s')).'")
        ');
    }

    public static function get($name)
    {
        return Db::getInstance()->getValue('SELECT c.value
            FROM '._DB_PREFIX_.'blmod_xml_configuration c
            WHERE c.name = "'.pSQL($name).'"');
    }

    public static function delete($name)
    {
        return Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'blmod_xml_configuration 
            WHERE `name` = "'.pSQL($name).'"');
    }
}
