<?php
/**
 *  @author    Amazzing <mail@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class Product extends ProductCore
{
    public function getAccessories($id_lang, $active = true)
    {
        return !empty(Context::getContext()->ec_accessories) ? [] : parent::getAccessories($id_lang, $active);
    }
}
