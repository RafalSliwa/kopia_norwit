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
*  @license          /license.txtr
*}

<div id="container-quotations" class="clearfix">
    <div class="leadin"></div>
    <div class="row">
        <div class="tab-content col-md-12">
            <div class="row">
                <div class="col-lg-7">
                    <div class="panel clearfix">
                        <div class="panel-heading">
                            <span>
                            <i class="icon-inbox"></i>
                                {l s='Template' mod='roja45quotationspro'}<span class="badge">{$template_name|escape:"html":"UTF-8"}</span>
                            </span>
                        </div>
                        <form id="roja45quotationspro_form" class="defaultForm form-horizontal" action="{$quotationspro_link|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
                            <input type="hidden" name="action" value="submitCreateQuote">
                            <input type="hidden" name="id_roja45_quotation_template" value="{$id_roja45_quotation_template|escape:'html':'UTF-8'}">
                            <div class="panel-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Shop' mod='roja45quotationspro'}</label>
                                    <div class="col-lg-4">
                                        <p class="form-control-static">{$shop_name|escape:"html":"UTF-8"}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Template Name' mod='roja45quotationspro'}</label>
                                    <div class="col-lg-8">
                                        <input type="text" disabled="disabled" name="template_name" id="template_name" value="{$template_name|escape:"html":"UTF-8"}"/>
                                    </div>
                                </div>


                                {if $currencies|@count > 1}
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">
                                            {l s='Currency' mod='roja45quotationspro'}
                                        </label>
                                        <div class="col-lg-5">
                                            <select class="form-control" name="quote_currency" id="quote_currency">
                                                {foreach $currencies as $currencyObj}
                                                    <option value="{$currencyObj.id_currency|escape:"html":"UTF-8"}"
                                                            {if ($id_currency==$currencyObj.id_currency)}selected="selected"{/if}>{$currencyObj.name|escape:"html":"UTF-8"}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                {else}
                                    <input type="hidden" name="quote_currency" value="{$id_currency|escape:"html":"UTF-8"}"/>
                                {/if}
                                <div class="row enable_taxes">
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">{l s='Display Taxes' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-9">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_TAXES"
                                                       id="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_on"
                                                       value=1
                                                       {if !$allow_edit}disabled="disabled"{/if}
                                                       {if ($use_taxes == 1)}checked="checked"{/if}>
                                                <label for="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_TAXES"
                                                       id="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_off"
                                                       value=0
                                                       {if !$allow_edit}disabled="disabled"{/if}
                                                       {if ($use_taxes == 0)}checked="checked"{/if}>
                                                <label for="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="panel-footer">
                            <div id="quotationspro_buttons" class="row">
                                <button type="button" class="btn btn-secondary btn-lg pull-right createQuote disabled-while-saving">
                                    {l s='Create Quote' mod='roja45quotationspro'}
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-eye-close"></i> {l s='Customer Documents' mod='roja45quotationspro'} <span
                                    class="badge">{count($quotation_documents)|escape:"html":"UTF-8"}</span>
                        </div>
                        <div class="panel panel-notes notes-container" >
                            {if !count($quotation_documents)}
                                <div class="alert alert-info">{l s='No documents available.' mod='roja45quotationspro'}</div>
                            {/if}
                            <div id="notes_table" {if count($quotation_documents) == 0} style="display:none;"{/if}>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th><span class="title_box ">{l s='Name' mod='roja45quotationspro'}</span></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {foreach from=$quotation_documents item=document}
                                            <tr class="note_row">
                                                <td>{$document['display_name']|escape:'html':'UTF-8'}</td>
                                                <td class="text-right">
                                                    <form id="customer_document" class="form-horizontal" method="post" action="{$quotationspro_link|escape:'html':'UTF-8'}" enctype="multipart/form-data">
                                                        <input type="hidden" name="action" value="deleteDocument"/>
                                                        <input type="hidden" name="id_roja45_quotation_template" value="{$id_roja45_quotation_template|escape:"html":"UTF-8"}"/>
                                                        <input type="hidden" name="id_roja45_quotation_template_document" value="{$document['id_roja45_quotation_template_document']|escape:"html":"UTF-8"}"/>
                                                        <a href="{$document['link']}" target="_blank" class="btn btn-primary">
                                                            <i class="icon-download"></i>
                                                        </a>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="icon-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {if $allow_edit}
                        <div class="panel panel-total note-container">
                            <div class="panel-heading">
                                {l s='Add a document' mod='roja45quotationspro'}
                            </div>
                            <form id="customer_document" class="form-horizontal" method="post" action="{$quotationspro_link|escape:'html':'UTF-8'}" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="addDocument"/>
                                <input type="hidden" name="id_roja45_quotation_template" value="{$id_roja45_quotation_template|escape:"html":"UTF-8"}"/>
                                {if count($documents)}
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">{l s='Saved Documents' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-5">
                                            <select class="form-control" name="available_document" id="available_document">
                                                <option value="0">-</option>
                                                {foreach $documents as $document}
                                                    <option value="{$document.id_roja45_document|escape:"html":"UTF-8"}">{$document.display_name|escape:"html":"UTF-8"}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                {/if}
                                <div class="form-group">
                                    <label class="control-label col-lg-3">{l s='Add Document' mod='roja45quotationspro'}</label>
                                    <div class="col-lg-5">
                                        <input id="addDocument" type="file" name="document" class="hide" accept=".pdf"/>
                                        <div class="dummyfile input-group">
                                            <span class="input-group-addon"><i class="icon-file"></i></span>
                                            <input id="document-name" type="text" class="disabled" name="filename" readonly />
                                            <span class="input-group-btn">
                                                <button id="document-selectbutton" type="button" name="submitAddAttachments" class="btn btn-secondary">
                                                    <i class="icon-folder-open"></i> {l s='Choose a file' mod='roja45quotationspro'}
                                                </button>
                                                <script>
                                                    $(document).ready(function(){
                                                        $('#document-selectbutton').click(function(e){
                                                            $('#addDocument').trigger('click');
                                                        });
                                                        $('#addDocument').change(function(e){
                                                            var val = $(this).val();
                                                            var file = val.split(/[\\/]/);
                                                            $('#document-name').val(file[file.length-1]);
                                                        });
                                                    });
                                                </script>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <button type="submit" id="submitAddDocument" class="btn btn-secondary btn-sml pull-right">
                                            <i class="icon-save"></i>
                                            {l s='Save' mod='roja45quotationspro'}
                                        </button>
                                    </div>
                                </div>
                                <span id="document_feedback"></span>
                            </form>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="panel" id="quotation_panel">
                        <div class="panel-heading">
                            <i class="icon-file"></i>{l s='Template' mod='roja45quotationspro'}</span>
                        </div>
                        <div class="form-horizontal">
                            <form id="roja45quotation_form" class="defaultForm form-horizontal"
                                  action="{$link->getAdminLink('AdminQuotationsPro')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data"
                                  novalidate="">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="panel panel-charges clearfix">
                                            <div class="panel-heading">
                                                {l s='Products' mod='roja45quotationspro'}
                                                <span class="badge">{$quotation_products|@count|escape:'html':'UTF-8'}</span>
                                                {if $allow_edit}
                                                <span class="panel-heading-action">
                                                    <button id="add_quotation_product" type="button" class="btn btn-primary add-product disabled-while-saving">
                                                        <i class="icon-plus"></i>
                                                        {l s='Add Products' mod='roja45quotationspro'}
                                                    </button>
                                                </span>
                                                {/if}
                                                <span class="badge saving-indicator" style="display:none;">{l s='Saving...' mod='roja45quotationspro'}</span>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive" style="position: relative;">
                                                    <table class="quotation-content table" id="quotationProducts">
                                                        <thead>
                                                        <tr>
                                                            <th class="column-select">
                                                                <label class="checkbox-container">
                                                                    <input id="product_quotation_select_all" type="checkbox" class="select-quotation-product-all" name="product_quotation_select_all">
                                                                    <span class="checkbox-checkmark"></span>
                                                                </label>
                                                            </th>
                                                            <th class="column-image"></th>
                                                            <th class="column-title"><span class="title_box">{l s='Product' mod='roja45quotationspro'}</span></th>
                                                            <th class="column-comment"><span class="title_box">{l s='Comment' mod='roja45quotationspro'}</span></th>
                                                            <th class="column-wholesale">
                                                                <span class="title_box ">{l s='Wholesale' mod='roja45quotationspro'}</span>
                                                            </th>
                                                            <th class="column-unitprice">
                                                                <span class="title_box">{l s='Unit Price' mod='roja45quotationspro'}</span>
                                                                <small class="text-muted">{if ($use_taxes)}{l s='tax incl.' mod='roja45quotationspro'}{else}{l s='tax excl.' mod='roja45quotationspro'}{/if}</small>
                                                            </th>
                                                            <th class="column-quoteprice">
                                                                <span class="title_box ">{l s='Quote' mod='roja45quotationspro'}</span>
                                                                <small class="text-muted">{if ($use_taxes)}{l s='tax incl.' mod='roja45quotationspro'}{else}{l s='tax excl.' mod='roja45quotationspro'}{/if}</small>
                                                            </th>
                                                            <th class="column-quantity"><span class="title_box ">{l s='Qty' mod='roja45quotationspro'}</span></th>
                                                            <th class="column-total">
                                                                <span class="title_box ">{l s='Total' mod='roja45quotationspro'}</span>
                                                                <small class="text-muted">{if ($use_taxes)}{l s='tax incl.' mod='roja45quotationspro'}{else}{l s='tax excl.' mod='roja45quotationspro'}{/if}</small>
                                                            </th>
                                                            {if ($use_taxes)}
                                                                <th class="column-taxpaid">
                                                                    <span class="title_box ">{l s='Tax' mod='roja45quotationspro'}</span>
                                                                </th>
                                                                <th class="column-taxrate">
                                                                    <span class="title_box ">{l s='Rate' mod='roja45quotationspro'}</span>
                                                                </th>
                                                            {/if}
                                                            <th class="column-profit">
                                                                <span class="title_box ">{l s='Total Profit' mod='roja45quotationspro'}</span>
                                                            </th>

                                                            <th class="column-buttons add_product_quotation_fields"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        {foreach from=$quotation_products item=product key=k}
                                                            <tr class="product-line-row" data-id-roja45-quotation-product="{$product['id_roja45_quotation_template_product']|escape:'html':'UTF-8'}">
                                                                <input type="hidden" name="product_quotation[product_quotation_id]" class="product_quotation_id" value="{$product['id_roja45_quotation_template_product']|escape:'html':'UTF-8'}"/>
                                                                <input type="hidden" name="product_quotation[id_product]" class="product_id" value="{$product['id_product']|escape:'html':'UTF-8'}"/>
                                                                {if $product.deleted}
                                                                    <td class="column-select"></td>
                                                                    <td class="column-image"></td>
                                                                    <td class="column-title">{l s='Product no longer available' mod='roja45quotationspro'}</td>
                                                                    <td class="column-comment"></td>
                                                                    <td class="column-wholesale"></td>
                                                                    <td class="column-unitprice"></td>
                                                                    <td class="column-quoteprice"></td>
                                                                    <td class="column-quantity"></td>
                                                                    <td class="column-total"></td>
                                                                    {if ($use_taxes)}
                                                                        <td class="column-taxpaid">
                                                                        </td>
                                                                        <td class="column-taxrate">
                                                                        </td>
                                                                    {/if}
                                                                    <td class="column-profit"></td>
                                                                    <td class="column-buttons">
                                                                        <button type="button" title="{l s='Delete' mod='roja45quotationspro'}" class="btn btn-secondary delete_product_quotation_line">
                                                                            <i class="icon-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                {else}
                                                                    <td class="column-select">
                                                                        <label class="checkbox-container">
                                                                            <input type="checkbox" class="select-quotation-product" name="product_quotation[selected]">
                                                                            <span class="checkbox-checkmark"></span>
                                                                        </label>
                                                                    </td>
                                                                    <td class="column-image">{if isset($product.image_tag)}<img src="{$product.image_tag|escape:'htmlall':'UTF-8'}" alt="{$product['product_title']|escape:'html':'UTF-8'}" class="img img-thumbnail" width="{$product['image_width']}" height="{$product['image_height']}"/>{/if}</td>
                                                                    <td class="column-title">
                                                                            <span class="productName">{$product['product_title']|escape:'html':'UTF-8'}</span><br/>
                                                                            {if $product.reference}{l s='Reference #:' mod='roja45quotationspro'} {$product.reference|escape:'html':'UTF-8'}<br/>{/if}
                                                                            {if $product.supplier_reference}{l s='Supplier #' mod='roja45quotationspro'} {$product.supplier_reference|escape:'html':'UTF-8'}{/if}

                                                                    </td>
                                                                    <td class="column-comment">
                                                                        <input type="text"
                                                                               name="product_quotation[comment]"
                                                                               class="product_quotation_editable product_quotation_comment product_comment"
                                                                                {if !$allow_edit}disabled="disabled"{/if}
                                                                               value="{if isset($product.comment)}{$product.comment|escape:'htmlall':'UTF-8'}{/if}"/>
                                                                    </td>
                                                                    <td class="column-wholesale wholesale_price" data-wholesale-price="{$product.wholesale_price}">
                                                                        {$product.wholesale_price_formatted}
                                                                    </td>
                                                                    <td class="column-unitprice">
                                                                        {if ($use_taxes)}
                                                                            <span>{$product.list_price_incl_formatted}</span>
                                                                        {else}
                                                                            <span>{$product.list_price_excl_formatted}</span>
                                                                        {/if}
                                                                    </td>
                                                                    <td class="column-quoteprice">
                                                                        {if ($use_taxes)}
                                                                            <div class="fixed-width-md">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'}</div>
                                                                                    <input type="text"
                                                                                           name="product_quotation[product_price_tax_incl]"
                                                                                           {if !$allow_edit}disabled="disabled"{/if}
                                                                                           class="product_quotation_editable product_quotation_price_tax_incl product_price"
                                                                                           value="{Tools::ps_round(Tools::convertPrice($product['unit_price_tax_incl'], $currency), 6)|escape:'html':'UTF-8'}"/>
                                                                                </div>
                                                                            </div>
                                                                        {else}
                                                                            <div class="fixed-width-md">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-addon">{$currency->sign|escape:'html':'UTF-8'}</div>
                                                                                    <input type="text"
                                                                                           name="product_quotation[product_price_tax_excl]"
                                                                                           {if !$allow_edit}disabled="disabled"{/if}
                                                                                           class="product_quotation_editable product_quotation_price_tax_excl product_price"
                                                                                           value="{Tools::ps_round(Tools::convertPrice($product['unit_price_tax_excl'], $currency), 6)|escape:'html':'UTF-8'}"/>
                                                                                </div>
                                                                            </div>
                                                                        {/if}
                                                                    </td>
                                                                    <td class="column-quantity productQuantity">
                                                                        <input type="number"
                                                                               name="product_quotation[product_quantity]"
                                                                               class="product_quotation_editable form-control fixed-width-xs product_quotation_quantity"
                                                                               {if !$allow_edit}disabled="disabled"{/if}
                                                                               value="{$product['qty']|escape:'html':'UTF-8'}"/>
                                                                    </td>
                                                                    <td class="column-total total_product hide-when-dirty">
                                                                        {if ($use_taxes)}
                                                                            {$product['product_price_subtotal_incl_formatted']}
                                                                        {else}
                                                                            {$product['product_price_subtotal_incl_formatted']}
                                                                        {/if}
                                                                    </td>
                                                                    {if ($use_taxes)}
                                                                        <td class="column-taxpaid total_product_tax hide-when-dirty">
                                                                            {$product.tax_paid_formatted}
                                                                        </td>
                                                                        <td class="column-taxrate">
                                                                            {$product.tax_rate_formatted}
                                                                        </td>
                                                                    {/if}
                                                                    <td class="column-profit total_product_profit hide-when-dirty">
                                                                        {$product['product_profit_subtotal_excl_formatted']}
                                                                    </td>
                                                                    <td class="column-buttons quotation_action text-right">
                                                                        {if $allow_edit}
                                                                            <div class="btn-group">
                                                                                <button type="button" title="{l s='Delete' mod='roja45quotationspro'}" class="btn btn-primary btn-delete-quotation-product disabled-while-saving">
                                                                                    <i class="icon-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        {/if}
                                                                    </td>
                                                                {/if}
                                                            </tr>
                                                        {/foreach}
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>

                                            <div class="panel-footer">
                                                {if $allow_edit}
                                                <button type="button" class="btn btn-primary btn-delete-selected-products pull-right disabled-while-saving">
                                                    <i class="icon-trash"></i>
                                                    {l s='Delete Selected' mod='roja45quotationspro'}
                                                </button>
                                                <button type="button" class="btn btn-primary btn-save-selected-products pull-right disabled-while-saving">
                                                    <i class="icon-save"></i>
                                                    {l s='Save All' mod='roja45quotationspro'}
                                                </button>
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="panel panel-charges clearfix">
                                            <div class="panel-heading">
                                                {l s='Charges' mod='roja45quotationspro'}
                                                <span class="badge">{$charges|@count|escape:'html':'UTF-8'}</span>
                                                {if $allow_edit}
                                                <span class="panel-heading-action">
                                                    <a id="add_quotation_charge" class="btn btn-primary btn-add-charge" href="#">
                                                        {l s='Add Charges' mod='roja45quotationspro'}
                                                    </a>
                                                </span>
                                                {/if}
                                            </div>

                                            {if (sizeof($charges))}
                                                <div class="current-edit" id="charges_form" style="display:none;">
                                                    <div class="form-horizontal well">
                                                        <div class="form-group">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Charge Type' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <select class="form-control" name="charge_type" id="charge_type">
                                                                    <option value="general">{l s='General' mod='roja45quotationspro'}</option>
                                                                    <option value="shipping">{l s='Shipping' mod='roja45quotationspro'}</option>
                                                                    <option value="handling">{l s='Handling' mod='roja45quotationspro'}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group" id="charge-name-block" style="display:none;">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Name' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <input class="form-control" type="text" name="charge_name" id="charge_name" value=""/>
                                                            </div>
                                                        </div>

                                                        <div class="form-group" id="carriers-block" style="display:none;">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Carriers' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <select class="form-control" name="carriers" id="carriers">
                                                                    <option value="0">{l s='Select a carrier' mod='roja45quotationspro'}</option>
                                                                    {foreach $carriers as $carrier}
                                                                        <option value="{$carrier['carrier']->id|escape:"html":"UTF-8"}"
                                                                                data-name="{$carrier['carrier']->name|escape:"html":"UTF-8"}"
                                                                                data-rate="{$carrier['shipping']|escape:"html":"UTF-8"}"
                                                                                data-rate-formatted="{displayPrice price=Tools::ps_round(Tools::convertPrice($carrier['shipping'], $currency), 2) currency=$currency->id}">{$carrier['carrier']->name|escape:"html":"UTF-8"}</option>
                                                                    {/foreach}
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group" id="charge-method-block" style="display:none;">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Charge Method' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <select class="form-control" name="charge_method" id="charge_method">
                                                                    <option value="1">{l s='Percent' mod='roja45quotationspro'}</option>
                                                                    <option value="2">{l s='Amount' mod='roja45quotationspro'}</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="form-group" id="available-taxes-block" style="display:none;">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Charge Method' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <select class="form-control" name="tax_id" id="tax_id">
                                                                    <option value="1">{l s='Tax 1' mod='roja45quotationspro'}</option>
                                                                    <option value="2">{l s='Tax 2' mod='roja45quotationspro'}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="charge_value_field" class="form-group">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Value (exc.)' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <div class="input-group">
                                                                    <div class="input-group-addon">
                                                                        <span id="charge_currency_sign" style="display: none;">{$currency->sign|escape:"html":"UTF-8"}</span>
                                                                        <span id="charge_percent_symbol">%</span>
                                                                    </div>
                                                                    <input id="charge_value" class="form-control" type="text" name="charge_value"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <p id="handling_warning" class="alert alert-warning" style="display:none;">{l s='You have configured this module to include handling and the carrier is configured to include handling, this should be disable to avoid an incorrect value being recorded.' mod='roja45quotationspro'}</p>
                                                        <p id="charges_warning" class="alert alert-warning" style="display:none;">{l s='Handling and General charges are advisory to the customer only, they will not be included in a cart order.  See the documentation for more details.' mod='roja45quotationspro'}</p>
                                                        <div class="row">
                                                            <div class="col-lg-9 col-lg-offset-3">
                                                                <button class="btn btn-secondary" type="button" id="cancel_add_charge">
                                                                    <i class="icon-remove text-danger"></i>
                                                                    {l s='Cancel' mod='roja45quotationspro'}
                                                                </button>
                                                                <button class="btn btn-secondary submit-new-charge" type="submit" name="submitNewCharge">
                                                                    <i class="icon-ok text-success"></i>
                                                                    {l s='Add' mod='roja45quotationspro'}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="charges_table">
                                                    <div class="table-responsive" style="overflow: auto;">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th><span class="title_box ">{l s='Charge' mod='roja45quotationspro'}</span></th>
                                                                <th><span class="title_box ">{l s='Type' mod='roja45quotationspro'}</span></th>
                                                                <th><span class="title_box ">{l s='Value (exc)' mod='roja45quotationspro'}</span></th>
                                                                {if $use_taxes}
                                                                    <th><span class="title_box ">{l s='Value (inc.)' mod='roja45quotationspro'}</span></th>
                                                                {/if}
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            {foreach from=$charges item=charge}
                                                                <tr class="charge_row"
                                                                    data-id-quotation-charge="{$charge['id_roja45_quotation_template_charge']|escape:"html":"UTF-8"}"
                                                                    data-charge="{$charge['charge_amount']}"
                                                                    data-charge-wt="{$charge['charge_amount_wt']}">
                                                                    <td>{$charge['charge_name']|escape:"html":"UTF-8"}</td>
                                                                    <td>{if $charge['charge_type']=='SHIPPING'}{l s='Shipping' mod='roja45quotationspro'}{elseif $charge['charge_type']=='HANDLING'}{l s='Handling' mod='roja45quotationspro'}{/if}</td>
                                                                    <td>
                                                                        <span>{$charge['charge_amount_formatted']}</span>
                                                                    </td>
                                                                    {if $use_taxes}
                                                                        <td>
                                                                            <span>{$charge['charge_amount_wt_formatted']}</span>
                                                                        </td>
                                                                    {/if}
                                                                    <td>
                                                                        {if $allow_edit}
                                                                        <a href="#" class="submit-delete-charge pull-right"
                                                                           data-id-roja45-quotation="{$charge['id_roja45_quotation_template']|escape:"html":"UTF-8"}"
                                                                           data-id-roja45-quotation-charge="{$charge['id_roja45_quotation_template_charge']|escape:"html":"UTF-8"}">
                                                                            <i class="icon-minus-sign"></i>
                                                                            {l s='Delete' mod='roja45quotationspro'}
                                                                        </a>
                                                                        {/if}
                                                                    </td>
                                                                </tr>
                                                            {/foreach}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="panel panel-vouchers clearfix">
                                            <div class="panel-heading">
                                                {l s='Discounts' mod='roja45quotationspro'}
                                                <span class="badge">{$discounts|@count|escape:'html':'UTF-8'}</span>
                                                {if $allow_edit}
                                                <span class="panel-heading-action">
                                                    <a id="desc-discount-new" class="btn btn-primary btn-add-discount" href="#">
                                                        {l s='Add Discounts' mod='roja45quotationspro'}
                                                    </a>
                                                </span>
                                                {/if}
                                            </div>
                                            {if (sizeof($discounts))}
                                                <div class="current-edit" id="voucher_form" style="display:none;">
                                                    <div class="form-horizontal well">
                                                        <div class="form-group">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Name' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <input class="form-control" type="text" name="discount_name" id="discount_name" value="" />
                                                            </div>
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Type' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <select class="form-control" name="discount_type" id="discount_type">
                                                                    <option value="1">{l s='Percent' mod='roja45quotationspro'}</option>
                                                                    <option value="2">{l s='Amount' mod='roja45quotationspro'}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div id="discount_value_field" class="form-group">
                                                            <label class="control-label col-lg-3">
                                                                {l s='Value' mod='roja45quotationspro'}
                                                            </label>
                                                            <div class="col-lg-9">
                                                                <div class="input-group">
                                                                    <div class="input-group-addon">
                                                                        <span id="discount_currency_sign" style="display: none;">{$currency->sign|escape:"html":"UTF-8"}</span>
                                                                        <span id="discount_percent_symbol">%</span>
                                                                    </div>
                                                                    <input id="discount_value" class="form-control" type="text" name="discount_value"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-lg-9 col-lg-offset-3">
                                                                <button class="btn btn-secondary" type="button" id="cancel_add_voucher">
                                                                    <i class="icon-remove text-danger"></i>
                                                                    {l s='Cancel' mod='roja45quotationspro'}
                                                                </button>
                                                                <button class="btn btn-secondary submitNewVoucher" type="submit" name="submitNewVoucher">
                                                                    <i class="icon-ok text-success"></i>
                                                                    {l s='Add' mod='roja45quotationspro'}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="discount_table">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th><span class="title_box ">{l s='Discount name' mod='roja45quotationspro'}</span></th>
                                                                <th><span class="title_box ">{l s='Value' mod='roja45quotationspro'} {if $use_taxes}{l s='(inc)' mod='roja45quotationspro'}{else}{l s='(exc)' mod='roja45quotationspro'}{/if}</span></th>
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            {foreach from=$discounts item=discount}
                                                                <tr class="discount_row">
                                                                    <td>{$discount['charge_name']|escape:"html":"UTF-8"}</td>
                                                                    <td>
                                                                        {if ($discount['charge_method']=='PERCENTAGE')}
                                                                            {$discount['charge_value']|escape:"html":"UTF-8"|string_format:"%.2f"}%
                                                                        {elseif ($discount['charge_method']=='VALUE')}
                                                                            {displayPrice price=Tools::ps_round(Tools::convertPrice($discount['charge_value'], $currency), 2) currency=$currency->id}
                                                                        {/if}
                                                                    </td>
                                                                    <td>
                                                                        {if $allow_edit}
                                                                        <a href="#" class="submitDeleteVoucher pull-right"
                                                                           data-id-roja45-quotation="{$discount['id_roja45_quotation_template']|escape:"html":"UTF-8"}"
                                                                           data-id-roja45-quotation-charge="{$discount['id_roja45_quotation_template_charge']|escape:"html":"UTF-8"}">
                                                                            <i class="icon-minus-sign"></i>
                                                                            {l s='Delete' mod='roja45quotationspro'}
                                                                        </a>
                                                                        {/if}
                                                                    </td>
                                                                </tr>
                                                            {/foreach}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-6">
                                    </div>
                                    <div class="col-xs-6">
                                        <div id="totals_panel" class="panel panel-total">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="row pull-right">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-secondary btn-lg pull-right createQuote disabled-while-saving">
                                                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                                    {l s='Create Quote' mod='roja45quotationspro'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="quotationspro_request_dialog_overlay"></div>

<div id="quotationspro_addproduct_modal" class="quotationspro_addproduct_modal modal" aria-hidden="false"
     style="display: none;">
    <form action="{$quotationspro_link|escape:'html':'UTF-8'}" method="post" id="quotationspro_addproduct_form" class="std box">
        <input type="hidden" name="ajax" value="1"/>
        <input type="hidden" name="action" value="addSelectedProducts"/>
        <input type="hidden" name="id_roja45_quotation_template" value="{$id_roja45_quotation_template|escape:'html':'UTF-8'}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">X</span>
                        <span class="sr-only">{l s='Close' mod='roja45quotationspro'}</span>
                    </button>
                    <h4 id="modalTitle"
                        class="modal-title">{l s='Select Products' mod='roja45quotationspro'}</h4>
                </div>
                <div id="modalBody" class="modal-body row">
                    <div class="col-lg-12 search">
                        <div class="form-horizontal">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='Product Name' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <input type="text"
                                               autocomplete="false"
                                               name="product_name"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='Reference' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <input type="text"
                                               autocomplete="false"
                                               name="product_reference"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='Category' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <select class="form-control" name="product_category">
                                            <option value="0">-</option>
                                            {foreach $categories as $category}
                                                <option value="{$category.id_category|escape:"html":"UTF-8"}">{$category.name|escape:"html":"UTF-8"}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='# per page' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <input type="text"
                                               name="results_per_page"
                                               value="10"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='Page #' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <input type="text"
                                               name="page_number"
                                               disabled
                                               value="1"/>
                                        <select class="form-control" name="page_number" style="display:none">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-group">
                                    <label class="col-lg-12">&nbsp;</label>
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary btn-search-products" title="{l s='Search' mod='roja45quotationspro'}"><i class="icon-search"></i></button>
                                        <button class="btn btn-secondary btn-reset-search" title="{l s='Reset' mod='roja45quotationspro'}"><i class="icon-refresh"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 results"></div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary btn-close-add-products pull-left"
                       data-dismiss="modal">{l s='Close' mod='roja45quotationspro'}</a>
                    <a id="addCloseSelectedProducts" class="btn btn-primary btn-add-close-selected-products disabled">{l s='Add Selected' mod='roja45quotationspro'}</a>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="roja45_quotation_modal">
    <div id="roja45_quotation_modal_dialog" class="roja45-quotation-modal-dialog">
        <div id="modal_wait_icon">
            <i class="icon-refresh icon-spin animated"></i>
            <p>{l s='Please Wait' mod='roja45quotationspro'}</p>
        </div>
    </div>
</div>

<script type="text/javascript">
    var quotationspro_link = '{$quotationspro_link}';
    var id_lang = {$current_id_lang|escape:'html':'UTF-8'};
    var id_roja45_quotation_template = {$id_roja45_quotation_template|escape:'html':'UTF-8'};
    var id_shop = {$id_shop|escape:'html':'UTF-8'};
    var id_currency = {$id_currency|escape:'html':'UTF-8'};
    var currency_sign = '{$currency_sign|escape:'html':'UTF-8'}';
    var currency_format = '{$currency_format|escape:'html':'UTF-8'}';
    var currency_blank = {$currency_blank|escape:'html':'UTF-8'};
    var has_voucher = {$has_voucher|escape:'html':'UTF-8'};
    var has_charges = {$has_charges|escape:'html':'UTF-8'};
    var use_taxes = {$use_taxes|escape:'html':'UTF-8'};
    var priceDisplayPrecision = {$priceDisplayPrecision|escape:'html':'UTF-8'};
    var roja45_quotations_dateformat = "{$roja45_quotations_dateformat|escape:'html':'UTF-8'}";

    var roja45_quotationspro_error_unabletoclaim = '{l s='An unexpected error occurred while trying to claim this request.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_nocustomername = '{l s='No firstname provided.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_nocustomerlastname = '{l s='No lastname provided.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_nocustomeremail = '{l s='No email address provided.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_nocustomeraccountsfound = '{l s='No accounts found.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_nocustomersearchcriteria = '{l s='You should provide a firstname, lastname, or email address.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_unabletorelease = '{l s='An unexpected error occurred while trying to release this request.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_createaccount = '{l s='An unexpected error occurred while trying to create the customer account.' mod='roja45quotationspro' js=1}';
    var roja45_quotationspro_error_unexpected = '{l s='An unexpected error has occurred while trying to complete your request' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_success = '{l s='Updated Successfully' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_select = '{l s='Select' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_confirm = '{l s='Are you sure?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_confirmbutton = '{l s='Confirm' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_cancelbutton = '{l s='Close' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_adddiscount = '{l s='Are you sure you want to apply this discount to the quotation?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_deletediscount = '{l s='Are you sure you want to delete this discount from the quotation?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_addcharge = '{l s='Are you sure you want to apply this charge to the quotation?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_deletecharge = '{l s='Are you sure you want to delete this charge from the quotation?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_addproduct = '{l s='Are you sure you want to add this product to the quotation?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_deleteproduct = '{l s='Are you sure you want to delete this product from the quotation?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_addnote = '{l s='Are you sure you want to add this note to the quotation' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_createorder = '{l s='Are you sure you want to create an order for the customer?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_create_customer = '{l s='Create customer account?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_createcustomeraccount = '{l s='Are you sure you want to create this customer account?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_sendcustomerquotation = '{l s='Are you sure you want to send this quotation?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_sendcustomermessage = '{l s='Are you sure you want to send this message?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_claimrequest = '{l s='Are you sure you want to claim this request?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_releaserequest = '{l s='Are you sure you want to release this request?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_resetcart = '{l s='Are you sure you want to reset the associated cart?' mod='roja45quotationspro' js=1}';
    var txt_add_product_stock_issue = '{l s='Are you sure you want to add this quantity?' mod='roja45quotationspro' js=1}';
    var txt_add_product_new_invoice = '{l s='Are you sure you want to create a new invoice?' mod='roja45quotationspro' js=1}';
    var txt_add_product_no_product = '{l s='Error: No product has been selected' mod='roja45quotationspro' js=1}';
    var txt_add_product_no_product_quantity = '{l s='Error: Quantity of products must be set' mod='roja45quotationspro' js=1}';
    var txt_add_product_no_product_price = '{l s='Error: Product price must be set' mod='roja45quotationspro' js=1}';
    var txt_add_discount_no_discount_name = '{l s='You must specify a name in order to create a new discount.' mod='roja45quotationspro' js=1}';
    var txt_add_discount_no_discount_value = '{l s='You must provide a value for the new discount.' mod='roja45quotationspro' js=1}';
    var txt_add_charge_no_charge_name = '{l s='You must specify a name in order to add a charge to the quotation.' mod='roja45quotationspro' js=1}';
    var txt_add_charge_no_charge_value = '{l s='You must provide a value for the new charge.' mod='roja45quotationspro' js=1}';
    var txt_enable_taxes_country_missing = '{l s='You must set a country for tax calculations to work.' mod='roja45quotationspro' js=1}';
    var txt_no_addresses_available = '{l s='No addresses available.' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_deletequotation = '{l s='Are you sure you want to delete this quote?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_createquote = '{l s='Are you sure you want to create a new quote using this template?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_quotationnotsent = '{l s='This quotation has not been sent to the customer, are you sure you want to create the order?' mod='roja45quotationspro' js=1}';
    var roja45quotationspro_txt_savetemplate = '{l s='Are you sure you want to save this as a template?' mod='roja45quotationspro' js=1}';
</script>


