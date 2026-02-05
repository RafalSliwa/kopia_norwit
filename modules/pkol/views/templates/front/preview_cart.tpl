{*
 * PKO Leasing Integration Module
 *
 * @package    PrestaShop Modules
 * @subpackage PKO Leasing Integration
 * @author     PKO Leasing
 * @license    MIT License
 * @copyright  2024 PKO Leasing
 * @link       https://www.pkoleasing.pl/
 *
 *}
<input type="hidden" name="shopId" value="{$shopId}" />
<input type="hidden" name="returnLink" value="{$returnLink}" />
<input type="hidden" name="uniqueItemQuantity" value="{$uniqueItemQuantity}" />

{assign var="customIndex" value=1}
{foreach from=$products item=product}
    <input type="hidden" name="productName{$customIndex}" value="{$product.productName}" />
    <input type="hidden" name="productPrice{$customIndex}" value="{$product.productPrice}" />
    <input type="hidden" name="productNetPrice{$customIndex}" value="{$product.productNetPrice}" />
    <input type="hidden" name="productQuantity{$customIndex}" value="{$product.productQuantity}" />
    <input type="hidden" name="productCategory{$customIndex}" value="{$product.productCategory}" />
    <input type="hidden" name="productVatRate{$customIndex}" value="{$product.productVatRate}" />
    <input type="hidden" name="productAvatarUrl{$customIndex}" value="{$product.productAvatarUrl}" />
    {assign var="customIndex" value=$customIndex+1}
{/foreach}

<input type="hidden" name="totalValue" value="{$totalValue}" />
<input type="hidden" name="totalNetValue" value="{$totalNetValue}" />
<input type="hidden" name="source" value="BASKET" />
