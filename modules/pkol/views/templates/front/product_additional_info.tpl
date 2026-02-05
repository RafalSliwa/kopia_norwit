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
<input type="hidden" id="endpointurl" name="endpointurl" value="{$moduleDir}" />
<input type="hidden" name="productId" id="productId" value="{$product.id}" />
{$checkProductResponse|cleanHtml nofilter}

<style>
    .modal-body .pkol_widget,
    .modal-body .pko_container,
    .modal-footer .pkol_widget,
    .modal-footer .pko_container {
        display: none !important;
    }
</style>
