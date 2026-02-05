<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class ETS_CFU_Tools
{
    public static function getFormattedName($type)
    {
        return version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? ImageType::getFormattedName($type) : call_user_func(['ImageType', 'get' . 'Formated' . 'Name'], $type);
    }

    public static function isColor($color)
    {
        return preg_match('/^(#[0-9a-fA-F]{6})$/', $color);
    }

    public static function recursiveUnlink($dir, $pattern_ignore_files = array())
    {
        if (@is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                if (is_dir(($each = $dir . DIRECTORY_SEPARATOR . $file))) {
                    self::recursiveUnlink($each);
                } elseif ($pattern_ignore_files) {
                    $flag = 1;
                    foreach ($pattern_ignore_files as $pattern) {
                        if (preg_match($pattern, $file)) {
                            $flag = 0;
                            break;
                        }
                    }
                    if ($flag && file_exists($each))
                        @unlink($each);
                } elseif (file_exists($each))
                    @unlink($each);
            }
        }

        return true;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function getPostMaxSizeBytes()
    {
        $postMaxSizeList = array(@ini_get('post_max_size'), @ini_get('upload_max_filesize'), (int)Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') . 'M');
        $ik = 0;
        foreach ($postMaxSizeList as &$max_size) {
            $bytes = (int)trim($max_size);
            $last = Tools::strtolower($max_size[Tools::strlen($max_size) - 1]);
            switch ($last) {
                case 'g':
                    $bytes *= 1024;
                case 'm':
                    $bytes *= 1024;
                case 'k':
                    $bytes *= 1024;
            }
            if ($bytes == '') {
                unset($postMaxSizeList[$ik]);
            } else
                $max_size = $bytes;
            $ik++;
        }

        return min($postMaxSizeList);
    }

    public static function recursiveCopy($src, $dst)
    {
        if (@is_dir($src)) {
            if (!@is_dir($dst))
                @mkdir($dst, 0755);
            $files = scandir($src);
            if (is_array($files) && count($files) > 0) {
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        self::recursiveCopy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
        } elseif (@file_exists($src)) {
            @copy($src, $dst);
        }
    }

    public static function getDefaultFileType()
    {
        return array(
            'jpg',
            'jpeg',
            'png',
            'gif',
            'pdf',
            'doc',
            'docx',
            'ppt',
            'pptx',
            'odt',
            'avi',
            'ogg',
            'm4a',
            'mov',
            'mp3',
            'mp4',
            'mpg',
            'wav',
            'wmv',
            'zip',
            'rar',
            'txt'
        );
    }

    public static function getServerVars($var)
    {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : '';
    }

    public static function formatFileName($file_name)
    {
        return preg_match('/[\_\(\)\s\%\+]+/', '-', $file_name);
    }

    public static function checkEnableOtherShop($id_module)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int)$id_module . ' AND `id_shop` NOT IN(' . implode(', ', Shop::getContextListShopID()) . ')';
        return Db::getInstance()->executeS($sql);
    }

    public static function activeTab($module_name)
    {
        return Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'tab` SET enabled=1 where module ="' . pSQL($module_name) . '"');
    }

    public static function displayText($content, $tag, $attr_datas = array(), $short_tag = false)
    {
        $text = '<' . $tag . ' ';
        if ($attr_datas) {
            foreach ($attr_datas as $key => $value) {
                if ($value == null)
                    $text .= $key;
                else
                    $text .= $key . '="' . $value . '" ';
            }
        }
        if ($short_tag || $tag == 'img' || $tag == 'br' || $tag == 'path' || $tag == 'input') {
            $text .= ' /' . '>';
        } else {
            $text .= '>';
        }

        if ($tag && $tag != 'img' && $tag != 'input' && $tag != 'br' && !is_null($content))
            $text .= $content;
        if ($tag && $tag != 'img' && $tag != 'path' && $tag != 'input' && $tag != 'br')
            $text .= '<' . '/' . $tag . '>';
        return $text;
    }
}