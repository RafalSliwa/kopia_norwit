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
<form id="pkol_form" action="{$leaseUrl}" method="POST">
    <input type="hidden" name="shopId" value="{$shopId}" />
    <input type="hidden" name="returnLink" value="{$returnLink}" />
    <input type="hidden" name="uniqueItemQuantity" value="{$uniqueItemQuantity}" />

    {foreach $products as $index => $product}
        <input type="hidden" name="productName{$index+1}" value="{$product.productName|escape:'html':'UTF-8'}" />
        <input type="hidden" name="productPrice{$index+1}" value="{$product.productPrice}" />
        <input type="hidden" name="productNetPrice{$index+1}" value="{$product.netValue}" />
        <input type="hidden" name="productQuantity{$index+1}" value="{$product.quantity}" />
        <input type="hidden" name="productCategory{$index+1}" value="{$product.categoryId}" />
        <input type="hidden" name="productVatRate{$index+1}" value="{$product.vatRate}" />
        <input type="hidden" name="productAvatarUrl{$index+1}" value="{$product.productAvatarUrl}" />
    {/foreach}

    <input type="hidden" name="totalValue" value="{$totalValue}" />
    <input type="hidden" name="totalNetValue" value="{$totalNetValue}" />
    <input type="hidden" name="source" value="BASKET" />
</form>

<style>
    .modal-body .pkol_widget, .modal-body .pko_container,
    .modal-footer .pkol_widget, .modal-footer .pko_container { display: none!important; }
</style>
<link rel="stylesheet" href="{$moduleDir}views/css/pkol-public.css">
<input type="hidden" id="endpointurl" name="endpointurl" value="{$moduleDir}" />

<div class="check-product-response">
    {$checkProductResponse|cleanHtml nofilter}
</div>

