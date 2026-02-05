{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}


<div id="new_quotation_product" class="panel results-container form-horizontal">
    <div class="panel-heading">
        <p>{$total_results} {l s='Results' mod='roja45quotationspro'}</p>
    </div>
    <div class="panel-body">
        <table class="table">
            <thead>
            <tr>
                <th></th>
                <th>{l s='Product' mod='roja45quotationspro'}</th>
                <th>{l s='Combination' mod='roja45quotationspro'}</th>
                <th>{l s='Comment' mod='roja45quotationspro'}</th>
                <th>{l s='Wholesale Price' mod='roja45quotationspro'}</th>
                <th>{l s='Retail Price' mod='roja45quotationspro'}{l s='(tax excl.)' mod='roja45quotationspro'}</th>
                <th>{l s='Discount' mod='roja45quotationspro'}</th>
                <th>{l s='Qty' mod='roja45quotationspro'}</th>
                <th>{l s='Stock Level' mod='roja45quotationspro'}</th>
                <th>{l s='Select' mod='roja45quotationspro'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach $products as $product}
            <tr class="product-line-row" data-product-id="{$product.id_product}">
                <td>{if isset($product.image_url)}<img src="{$product.image_url|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" class="img img-thumbnail" />{/if}</td>
                <td>
                    <div class="row">
                        <a href="{$product.admin_link}" target="_blank">
                            <span class="productName">{$product.name|escape:'html':'UTF-8'}</span><br/>
                            {if $product.reference}{l s='Reference #:' mod='roja45quotationspro'} {$product.reference|escape:'html':'UTF-8'}<br/>{/if}
                            {if $product.supplier_reference}{l s='Supplier #' mod='roja45quotationspro'} {$product.supplier_reference|escape:'html':'UTF-8'}{/if}
                        </a>
                    </div>
                </td>
                <td>
                    <div class="row">
                        <div id="product_quotation_product_attribute_area">
                            {if count($product.combinations)}
                            <select id="product_quotation_id_product_attribute_{$product.id_product}" name="product_quotation[{$product.id_product}][id_product_attribute]"
                                    class="product_quotation_id_product_attribute">
                                {foreach $product.combinations as $combination}
                                <option data-minimal-quantity="{$combination.minimal_quantity}" data-stock="{$combination.qty_in_stock}" data-retail-price="{$combination.price_tax_excl}" value="{$combination.id_product_attribute}">{$combination.attributes}</option>
                                {/foreach}
                            </select>
                            {/if}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="row">
                        <div id="product_quotation_product_comment">
                            <input type="text"
                                   class="product_quotation_comment disable_after_add"
                                   name="product_quotation[{$product.id_product}][comment]"/>
                        </div>
                    </div>
                </td>
                <td class="product_wholesale_price">
                    <span>{$product.wholesale_price_formatted}</span>
                </td>
                <td class="product_unit_price">
                    <div class="input-group">
                        <div class="input-group-addon">
                            {$currency->sign|escape:'html':'UTF-8'}
                        </div>
                        <input type="text"
                               name="product_quotation[{$product.id_product}][product_price_tax_excl]"
                               class="product_quotation_price_tax_excl product_price disable_after_add"
                               value="{$product.price_tax_excl}"/>
                    </div>
                </td>
                <td class="productDiscount">
                    <div class="input-group">
                    <input type="text"
                           class="form-control product_quotation_discount disable_after_add"
                           name="product_quotation[{$product.id_product}][product_discount]"
                           value="{if $product.price_tax_incl_reduction_amount}{$product.price_tax_incl_reduction_amount}{/if}"/>
                        <div class="input-group-addon custom">
                            <select
                                    name="product_quotation[{$product.id_product}][product_discount_type]"
                                    class="product_quotation_discount_type"
                            >
                                <option value="percentage" {if isset($product.reduction_type)}{if $product.reduction_type=='percentage'}selected="selected"{/if}{else}selected="selected"{/if}>%</option>
                                <option value="fixed" {if isset($product.reduction_type) && $product.reduction_type!='percentage'}selected="selected"{/if}>{$currency->sign|escape:'html':'UTF-8'}</option>
                            </select>
                        </div>
                    </div>
                    <div class="bulk-discount-indicator" {if !$product.specific_price}style="display:none;"{/if}>{l s='Price Rule To Apply' mod='roja45quotationspro'}</div>
                </td>
                <td class="productQuantity">
                    <input type="number"
                           class="form-control product_quotation_qty disable_after_add"
                           name="product_quotation[{$product.id_product}][product_quantity]"
                           min="{$product.minimal_quantity}"
                           value="{$product.minimal_quantity}"/>
                    <div class="bulk-discount-indicator" {if !$product.has_volume_discount}style="display:none;"{/if}>{l s='Volume Discount' mod='roja45quotationspro'} <span class="from_quantity">{if isset($product.volume_discount_from) && ($product.volume_discount_from>0)} >  {$product.volume_discount_from}{/if}</span></div>
                    <input type="hidden"
                           class="product_quotation_has_volume_discount"
                           name="product_quotation[{$product.id_product}][has_volume_discount]"
                           value="{if $product.has_volume_discount}1{else}0{/if}"/>
                </td>
                <td class="stockLevel">
                    <input type="number"
                           disabled="disabled"
                           class="form-control product_quotation_stock negative_stock"
                           name="product_quotation[{$product.id_product}][stock_level]"
                           min="{$product.quantity}"
                           value="{$product.quantity}"/>
                    <div class="out-of-stock" {if $product.quantity > 0}style="display:none;"{/if}>{l s='Out of stock' mod='roja45quotationspro'}</div>

                </td>
                <td>
                    <label class="checkbox-container" style="padding-bottom: 20px;">
                        <input type="checkbox" class="add-product-checkbox" name="product_quotation[{$product.id_product}][selected]">
                        <span class="checkbox-checkmark"></span>
                    </label>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
