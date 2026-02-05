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

<a class="ets-cfu-product" href="{$product.link nofilter}">
    {if isset($product.image) && $product.image|trim !== ''}
        <img src="{$product.image nofilter}" alt="{$product.name|escape:'html':'UTF-8'}" style="width: 45px;height:auto;vertical-align: middle;">
    {/if}
    {$product.name|escape:'html':'UTF-8'}
</a>