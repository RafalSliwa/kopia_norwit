{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45 <support@roja45.com>
*  @copyright  		2016 roja45.com
*}

<table class="quotation-content table" id="quotationProducts">
    {foreach from=$quotation_products item=product name=product key=k}
        {if $smarty.foreach.product.iteration == 1}
            <thead>
                <tr class="quotation-products">
                    <th class="column-select">
                        <label class="checkbox-container">
                            <input id="product_quotation_select_all" type="checkbox" class="select-quotation-product-all"
                                name="product_quotation_select_all">
                            <span class="checkbox-checkmark"></span>
                        </label>
                    </th>
                    <th class="column column-image"></th>
                    <th class="column column-title"><span class="title_box">{l s='Product' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="column column-wholesale">
                        <span class="title_box ">{l s='Wholesale' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="column column-unitprice">
                        <span class="title_box">{l s='Unit Price' mod='roja45quotationspro'}
                            <small>({l s='exc.' mod='roja45quotationspro'})</small></span>
                        <small class="text-muted">{if ($use_taxes)}({l s='tax' mod='roja45quotationspro'}){/if}</small>
                    </th>
                    <th class="column column-discount">
                        <span class="title_box "><i class="icon-help icon-info-circle"
                                title="{l s='Enter a discount to apply to the item, either a fixed amount a percentage.' mod='roja45quotationspro'}"></i>{l s='Discount' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="column column-quoteprice">

                        <span class="title_box "><i class="icon-help icon-info-circle"
                                title="{l s='Enter the price you would like to offer to the customer for this item, exc. tax.' mod='roja45quotationspro'}"></i>{l s='Quote' mod='roja45quotationspro'}
                            ({$currency->sign|escape:'html':'UTF-8'})</span>
                        <small class="text-muted">({l s='exc. tax' mod='roja45quotationspro'})</small>
                    </th>
                    {if $enable_customization_cost > 0}
                        <th class="column column-customizationprice">

                            <span class="title_box "><i class="icon-help icon-info-circle"
                                    title="{l s='Specify a customization cost per line or per quantity.' mod='roja45quotationspro'}"></i>{l s='Customization' mod='roja45quotationspro'}
                                ({$currency->sign|escape:'html':'UTF-8'})</span>
                            <small class="text-muted">({l s='exc. tax' mod='roja45quotationspro'})</small>
                        </th>
                    {/if}
                    <th class="column column-quantity"><span class="title_box ">{l s='Qty' mod='roja45quotationspro'}</span>
                    </th>
                    <th class="column column-total">
                        <span class="title_box ">{l s='Total' mod='roja45quotationspro'}
                            ({$currency->sign|escape:'html':'UTF-8'})</span>
                        <small class="text-muted">({l s='exc. tax' mod='roja45quotationspro'})</small>
                    </th>
                    {if $quotation_has_additional_shipping > 0}
                    <th class="column column-shipping">
                        <span class="title_box ">{l s='Shipping' mod='roja45quotationspro'}</span>
                        <small class="text-muted">({l s='exc. tax' mod='roja45quotationspro'})</small>
                    </th>
                    {/if}
                    <th class="column column-taxpaid">
                        <span class="title_box ">{l s='Tax' mod='roja45quotationspro'}</span>
                    </th>
                    {if $quotation_has_ecotax > 0}
                        <th class="column column-taxrate">
                            <span class="title_box ">{l s='Ecotax' mod='roja45quotationspro'}</span>
                            <small
                                class="text-muted">{if ($use_taxes)}{l s='tax incl.' mod='roja45quotationspro'}{else}{l s='tax excl.' mod='roja45quotationspro'}{/if}</small>
                        </th>
                    {/if}
                    <th class="column column-profit">
                        <span class="title_box ">{l s='Profit' mod='roja45quotationspro'}</span>
                        <small
                                class="text-muted">{l s='tax excl.' mod='roja45quotationspro'}</small>
                    </th>
                    {if $deposit_enabled}
                        <th class="column column-deposit">
                            <span class="title_box ">{l s='Deposit %' mod='roja45quotationspro'}</span>
                        </th>
                    {/if}
                </tr>
            </thead>
            <tbody>
            {/if}
            <tr class="quotation-products product-line-row"
                data-id-roja45-quotation-product="{$product['id_roja45_quotation_product']|escape:'html':'UTF-8'}">
                <input type="hidden" name="product_quotation[product_quotation_id]" class="product_quotation_id"
                    value="{$product['id_roja45_quotation_product']|escape:'html':'UTF-8'}" />
                <input type="hidden" name="product_quotation[id_product]" class="product_id"
                    value="{$product['id_product']|escape:'html':'UTF-8'}" />
                <input type="hidden" name="product_quotation[product_changed]" class="product_changed" value="0" />
                {if $product.deleted}
                    <td class="column column-select"></td>
                    <td class="column column-image"></td>
                    <td class="column column-title">{l s='Product no longer available' mod='roja45quotationspro'}</td>
                    <td class="column column-wholesale"></td>
                    <td class="column column-unitprice"></td>
                    <td class="column column-discount"></td>
                    <td class="column column-quoteprice"></td>
                    <td class="column column-quantity"></td>
                    <td class="column column-total"></td>
                    <td class="column column-shipping"></td>
                    <td class="column column-taxpaid">
                    </td>
                    <td class="column column-ecotaxrate">
                    </td>
                    <td class="column column-profit"></td>
                    {if $deposit_enabled}
                        <td class="column column-deposit"></td>
                    {/if}
                    <td class="column column-buttons">
                        <button type="button" title="{l s='Delete' mod='roja45quotationspro'}"
                            class="btn btn-secondary delete_product_quotation_line" {if $deleted}disabled="disabled" {/if}>
                            <i class="icon-trash"></i>
                        </button>
                    </td>
                {else}
                    <td class="column-select">
                        <i title="{l s='Drag to change order' mod='roja45quotationspro'}" class="icon-arrows"></i>
                        <label class="checkbox-container">
                            <input type="checkbox" class="select-quotation-product" name="product_quotation[selected]">
                            <span class="checkbox-checkmark"></span>
                        </label>
                    </td>
                    <td class="column column-image">
                        {if isset($product.image_tag)}
                            <div class="product-image-container">
                                <img src="{$product.image_tag|escape:'htmlall':'UTF-8'}"
                                    alt="{$product['product_title']|escape:'html':'UTF-8'}" class="img img-thumbnail"
                                    width="{$product['image_width']}" height="{$product['image_height']}"></img>
                                {if !empty($product['custom_image'])}
                                    <div class="image-delete-container">
                                        <a href="#" class="btn-delete-custom-image"><i class="icon-trash"></i></a>
                                    </div>
                                    <div class="image-download-container">
                                        <a href="{$product['image_url']}" class="download-custom-image" target='_blank'><i
                                                class="icon-download"></i></a>
                                    </div>
                                {/if}
                            </div>
                        {/if}
                        <div class="image-upload-container">
                            <input name="submitProductImageUpload" type="hidden" value="0" accept=".jpg,.png,.jpeg" />

                            <label for="actual_btn_{$product['id_roja45_quotation_product']|escape:'html':'UTF-8'}">
                                <input class="image-upload" type="file"
                                    id="actual_btn_{$product['id_roja45_quotation_product']|escape:'html':'UTF-8'}"
                                    data-id-roja45-quotation-product="{$product['id_roja45_quotation_product']|escape:'html':'UTF-8'}" />
                                <i class="icon-upload"></i>
                            </label>
                        </div>

                    </td>
                    <td class="column column-title">
                        <a href="{$product.admin_link}" target="_blank">
                            <span class="productName">{$product['product_title']|escape:'html':'UTF-8'}</span><br />
                            {if $product.attributes}<span
                                class="productAttributes">{$product['attributes']|escape:'html':'UTF-8'}</span><br />{/if}
                            {if $product.reference}{l s='Reference #:' mod='roja45quotationspro'}
                            {$product.reference|escape:'html':'UTF-8'}<br />{/if}
                            {if $product.supplier_reference}{l s='Supplier #' mod='roja45quotationspro'}
                            {$product.supplier_reference|escape:'html':'UTF-8'}{/if}
                        </a>
                        <input type="text" name="product_quotation[comment]" {if ($quotation->isLocked())}disabled="disabled"
                            {/if} class="product_quotation_editable product_quotation_comment product_comment"
                            placeholder="{l s='Add comment' mod='roja45quotationspro'}"
                            value="{if isset($product.comment)}{$product.comment|escape:'htmlall':'UTF-8'}{/if}" />
                    </td>
                    <td class="column column-wholesale wholesale_price" data-wholesale-price="{$product.wholesale_price}">
                        {$product.wholesale_price_formatted}
                    </td>
                    <td class="column column-unitprice" data-list-price="{$product.list_price_excl_without_reduction}">
                        <span>{$product.list_price_excl_without_reduction_formatted}</span>
                        {if ($use_taxes)}<small
                                class="text-muted">({$product.list_price_without_reduction_tax_formatted})</small>
                        {/if}
                    </td>
                    <td class="column column-discount productDiscount">
                        <div class="input-group">
                            <input type="text" class="product_quotation_editable form-control product_quotation_discount"
                                name="product_quotation[product_discount]"
                                value="{$product['product_discount']|escape:'html':'UTF-8'}" />
                            <div class="input-group-addon custom">
                                <select name="product_quotation[product_discount_type]" class="product_quotation_discount_type">
                                    <option value="percentage" {if isset($product.discount_type)}
                                            {if $product.discount_type=='percentage'}selected="selected" {/if}
                                        {else}selected="selected"
                                        {/if}>%</option>
                                    <option value="fixed"
                                        {if isset($product.discount_type) && $product.discount_type!='percentage'}selected="selected"
                                        {/if}>{$currency->sign|escape:'html':'UTF-8'}</option>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td class="column column-quoteprice">
                        <input type="text" name="product_quotation[product_price_tax_excl]"
                            {if ($quotation->isLocked())}disabled="disabled" {/if}
                            class="product_quotation_editable product_quotation_price_tax_excl product_price"
                            value="{$product['unit_price_tax_excl_currency']|escape:'html':'UTF-8'}" />
                    </td>

                    {if $enable_customization_cost > 0}
                        <td class="column column-customizationprice">
                            <div class="input-group">
                                <input type="text"
                                    name="{if ($use_taxes)}product_quotation[customization_cost_inc]{else}product_quotation[customization_cost_exc]{/if}"
                                    {if ($quotation->isLocked())}disabled="disabled" {/if}
                                    class="product_quotation_editable {if ($use_taxes)}product_customization_cost_incl{else}product_customization_cost_excl{/if} product_quotation_customization_cost"
                                    value="{$product['customization_cost_exc']|escape:'html':'UTF-8'}" />
                                <div class="input-group-addon custom">
                                    <select name="product_quotation[product_customization_cost_type]"
                                        class="product_customization_cost_type">
                                        <option value="1" {if isset($product['customization_cost_type'])}
                                                {if $product['customization_cost_type']=='1'}selected="selected" {/if}
                                            {else}selected="selected"
                                            {/if}>
                                            {l s='Line' mod='roja45quotationspro'}</option>
                                        <option value="2"
                                            {if isset($product['customization_cost_type']) && $product['customization_cost_type']=='2'}selected="selected"
                                            {/if}>{l s='Qty' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    {/if}
                    <td class="column column-quantity productQuantity">
                        <input type="number" name="product_quotation[product_quantity]"
                            {if ($quotation->isLocked())}disabled="disabled" {/if}
                            class="product_quotation_editable form-control product_quotation_quantity"
                            value="{$product['qty']|escape:'html':'UTF-8'}" />
                    </td>
                    <td class="column column-total total_product hide-when-dirty"
                        data-subtotal-exc="{$product['product_price_subtotal_excl']}">
                        <input type="text" name="product_quotation[product_price_subtotal_excl]"
                            {if ($quotation->isLocked())}disabled="disabled" {/if}
                            class="product_quotation_editable product_price_subtotal_excl"
                            value="{$product['product_price_subtotal_excl_currency']|escape:'html':'UTF-8'}" />

                    </td>
                    {if $quotation_has_additional_shipping > 0}
                        <td class="column column-additional-shipping total_product_tax hide-when-dirty fixed-width-sm">
                            <div>{$product.additional_shipping_cost_formatted}</div>
                        </td>
                    {/if}
                    <td class="column column-taxpaid total_product_tax hide-when-dirty fixed-width-sm">
                        <div>{$product.tax_paid_formatted}</div>
                        <div><small>({$product.tax_rate_formatted})</small></div>
                    </td>
                    {if $quotation_has_ecotax > 0}
                        <td class="column column-ecotaxrate">
                            {if ($use_taxes)}{$product.product_ecotax_total_inc}{else}{$product.product_ecotax_total_exc}{/if}
                        </td>
                    {/if}
                    <td class="column column-profit total_product_profit hide-when-dirty">
                        {$product['product_profit_subtotal_excl_formatted']}
                    </td>
                    {if $deposit_enabled}
                        <td class="deposit_required">
                            <span class="deposit_required_show">{$product['deposit_amount']|escape:'html':'UTF-8'}</span>
                            <div class="deposit_required_edit" style="display:none;">
                                <div class="fixed-width-md">
                                    <input type="text" name="product_quotation[deposit_amount]"
                                        class="product_quotation_editable product_quotation_deposit_amount product_deposit_amount"
                                        value="{$product.deposit_amount|escape:'htmlall':'UTF-8'}" />
                                </div>
                            </div>
                        </td>
                        <td class="total_to_pay hide-when-dirty">
                            {if ($use_taxes)}
                                {$product['product_price_deposit_incl_formatted']}
                            {else}
                                {$product['product_price_deposit_excl_formatted']}
                            {/if}
                        </td>
                    {/if}
                {/if}
            </tr>

            {if count($product['customizations'])}

                <tr class="product-line-customization-row">
                    <td colspan="4">
                        <table class="table">
                            <tr>
                                <td><strong>{l s='Customizations' mod='roja45quotationspro'}</strong></td>
                            </tr>
                            {foreach $product['customizations'] as $customization}
                                <tr>
                                    <td>
                                        <div class="form-horizontal">
                                            {if ($customization.type == Product::CUSTOMIZE_FILE)}
                                                <div class="form-group">
                                                    <span class="col-lg-4 control-label"><strong>{$customization.name}</strong></span>
                                                    <div class="col-lg-8">
                                                        <a href="{$smarty.const._THEME_PROD_PIC_DIR_}{$customization.value}"
                                                            target="_blank" class="_blank">
                                                            <img class="img-thumbnail"
                                                                src="{$smarty.const._THEME_PROD_PIC_DIR_}{$customization.value}_small"
                                                                alt="" />
                                                        </a>
                                                    </div>
                                                </div>
                                            {elseif ($customization.type == Product::CUSTOMIZE_TEXTFIELD)}
                                                <div class="form-group">
                                                    <span class="col-lg-4 control-label"><strong>{$customization.name}</strong></span>
                                                    <div class="col-lg-8">
                                                        <p class="form-control-static">{$customization.value}</p>
                                                    </div>
                                                </div>
                                            {/if}
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        </table>

                    </td>
                </tr>

            {/if}
        {/foreach}
    </tbody>
</table>
