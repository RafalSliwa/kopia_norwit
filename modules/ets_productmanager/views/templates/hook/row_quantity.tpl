{*
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
*}
{if $has_attribute}
    <div class="popup_change_product field_name_attribute">
        <div class="content">
            <a href="{$link_product|escape:'html':'UTF-8'}#tab-step3"> {$quantity|intval} </a>
        </div>
        <a class="btn tooltip-link product-edit-popup" href="#" title="" onclick="etsGetFormPopupProduct($(this),'sav_quantity');return false;">

                <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg">
                    <path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/>
                </svg>

        </a>
    </div>
{else}
    <div class="span_change_product content field_name_attribute">
        <a href="{$link_product|escape:'html':'UTF-8'}#tab-step3"> {$quantity|intval} </a>
    </div>
    <div class="wapper-change-product text " style="display: none;">
        <input class="form-control" name="sav_quantity" value="{$quantity|intval}" type="text" />
    </div>
{/if}