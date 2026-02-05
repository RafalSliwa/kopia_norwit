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
    <input type="hidden" name="productName1" value="{$productName1|escape:'html':'UTF-8'}" />
    <input type="hidden" name="productPrice1" value="{$productPrice1}" />
    <input type="hidden" name="productNetPrice1" value="{$productNetPrice1}" />
    <input type="hidden" name="productQuantity1" value="{$productQuantity1}" />
    <input type="hidden" name="productCategory1" value="{$productCategory1}" />
    <input type="hidden" name="productVatRate1" value="{$productVatRate1}" />
    <input type="hidden" name="productAvatarUrl1" value="{$productAvatarUrl1}" />
    <input type="hidden" name="totalValue" value="{$totalValue}" />
    <input type="hidden" name="totalNetValue" value="{$totalNetValue}" />
    <input type="hidden" name="source" value="{$source}" />
</form>
