<?php
/**
 * upgrade_module_1_3_17
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  upgrade_module_1_3_17
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

/**
 * upgrade_module_1_3_17.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 * @category  Function
 *
 * 2016 ROJA45.COM - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_17($module)
{
    RojaFortyFiveQuotationsProCore::errorLog('Updating: '.$module->name);
    $return = true;
    if (version_compare(_PS_VERSION_, '1.7', '>=') == true) {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR',
            '.product-add-to-cart .btn.add-to-cart'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR',
            'div.product-prices'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR',
            'article.product-miniature'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
            ''
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
            '.button.ajax_add_to_cart_button'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR',
            '.product-price-and-shipping'
        );
    } else {
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR',
            '#add_to_cart'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR',
            '#our_price_display'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR',
            'ul.product_list li.ajax_block_product'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR',
            '.button.ajax_add_to_cart_button'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR',
            '.button.ajax_add_to_cart_button'
        );
        Configuration::updateValue(
            'ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR',
            '.content_price'
        );
    }

    $module->registerHook('displayTop');

    return $return;
}
