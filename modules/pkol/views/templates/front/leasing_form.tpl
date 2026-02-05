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
<form id="leasing" action="{$lease_url}" method="POST">
    <input type="hidden" name="shopId" value="{$shopID}" />
    <input type="hidden" name="orderId" value="{$cart_id}" />
    <input type="hidden" name="returnLink" value="{$return_link}" />
    <input type="hidden" name="uniqueItemQuantity" value="{$unique_item_quantity}" />

    {foreach $products as $n => $product}
        <input type="hidden" name="productName{$n + 1}" value="{$product.name}" />
        <input type="hidden" name="productPrice{$n + 1}" value="{$product.price_with_tax}" />
        <input type="hidden" name="productNetPrice{$n + 1}" value="{$product.price_without_tax}" />
        <input type="hidden" name="productQuantity{$n + 1}" value="{$product.quantity}" />
        <input type="hidden" name="productCategory{$n + 1}" value="{$product.category}" />
        <input type="hidden" name="productVatRate{$n + 1}" value="{$product.vat_rate}" />
    {/foreach}

    <input type="hidden" name="totalValue" value="{$total_with_tax}" />
    <input type="hidden" name="totalNetValue" value="{$total_without_tax}" />
    <input type="hidden" name="source" value="{$type}" />
</form>
<script>
    document.getElementById("leasing").submit();
</script>
