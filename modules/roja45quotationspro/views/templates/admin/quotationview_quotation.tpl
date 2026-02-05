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
<!--
<div class="panel-heading">
    <i class="icon-file"></i>{l s='Quotation' mod='roja45quotationspro'}</span>
    {if $quotation->id_cart}<span class="badge cart-badge"><a href="{$link->getAdminLink('AdminCarts', true)|escape:"html":"UTF-8"}&id_cart={$quotation->id_cart|escape:"html":"UTF-8"}&viewcart" target="_blank">{l s='Cart: #' mod='roja45quotationspro'}{$quotation->id_cart|escape:"html":"UTF-8"}</a></span>{/if}
    {if ($quotation->id_cart>0)  && ($quotation->id_order==0)}
    <div class="panel-heading-action">
        <a class="btn btn-secondary btn-lg" id="resetCart" href="#">
            <i class="icon-edit"></i>
            {l s='Reset Cart' mod='roja45quotationspro'}
        </a>
    </div>
    {/if}
</div>
-->
<div class="form-horizontal">
    <form id="roja45quotation_form" class="defaultForm form-horizontal"
        action="{$link->getAdminLink('AdminQuotationsPro')|escape:'html':'UTF-8'}" method="post"
        enctype="multipart/form-data" novalidate="">
        <input type="hidden" name="id_roja45_quotation" value="{$quotation->id}" />
        {capture name="TaxMethod"}{l s='tax excl.' mod='roja45quotationspro'}{/capture}
        {if (!$quotation->calculate_taxes)}
            <input type="hidden" name="TaxMethod" value="0">
        {else}
            <input type="hidden" name="TaxMethod" value="1">
        {/if}
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-charges clearfix">
                    <div class="panel-heading">
                        {l s='Products' mod='roja45quotationspro'}
                        <span class="badge">{$quotation_products|@count|escape:'html':'UTF-8'}</span>
                        <span class="panel-heading-action">
                            {if !$quotation->is_template}
                                <button id="add_quotation_product" type="button"
                                    class="btn btn-primary add-product disabled-while-saving"
                                    {if ($quotation->isLocked()) || $deleted}disabled="disabled" {/if}>
                                    <i class="icon-plus"></i>
                                    {l s='Add Products' mod='roja45quotationspro'}
                                </button>
                            {/if}
                        </span>
                        <span class="badge saving-indicator"
                            style="display:none;">{l s='Saving...' mod='roja45quotationspro'}</span>
                    </div>
                    <div class="panel-body" style="padding-left: 0;padding-right: 0;">
                        <div class="table-responsive" style="position: relative;">
                            {include file='./_product_line.tpl'}
                        </div>
                    </div>
                    <div class="panel-footer">
                        <button type="button" title="{l s='Delete Selected' mod='roja45quotationspro'}"
                            class="btn btn-primary btn-delete-selected-products pull-right disabled-while-saving"
                            {if $deleted}disabled="disabled" {/if}>
                            <i class="icon-trash"></i>

                        </button>
                        <button type="button" title="{l s='Save' mod='roja45quotationspro'}"
                            class="btn btn-primary btn-save-selected-products disabled-while-saving"
                            {if $deleted}disabled="disabled" {/if}>
                            <i class="icon-save"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <div class="panel panel-charges clearfix">
                    <div class="panel-heading">
                        {l s='Shipping' mod='roja45quotationspro'}
                        <span class="badge">{$charges|@count|escape:'html':'UTF-8'}</span>
                        <span class="panel-heading-action">
                            {if !$quotation->is_template}
                                <a id="add_quotation_charge" class="btn btn-primary btn-add-charge" href="#"
                                    {if ($quotation->isLocked()) || $deleted}style="display:none;" {/if}>
                                    {l s='Add Shipping Offer' mod='roja45quotationspro'}
                                </a>
                            {/if}
                        </span>
                    </div>

                    {if (sizeof($shipping) || !($quotation->isLocked()))}
                        <div class="current-edit" id="charges_form" style="display:none;">
                            {include file='./_charge_form.tpl'}
                        </div>
                        <div id="charges_table">
                            {include file='./_charge_table.tpl'}
                        </div>
                    {/if}
                </div>
                <div class="panel panel-vouchers clearfix">
                    <div class="panel-heading">
                        {l s='Discounts' mod='roja45quotationspro'}
                        <span class="badge">{$discounts|@count|escape:'html':'UTF-8'}</span>
                        <span class="panel-heading-action">
                            {if !$quotation->is_template}
                                <a id="desc-discount-new" class="btn btn-primary btn-add-discount" href="#"
                                    {if ($quotation->isLocked()) || $deleted}style="display:none;" {/if}>
                                    {l s='Add Discounts' mod='roja45quotationspro'}
                                </a>
                            {/if}
                        </span>
                    </div>
                    {if (sizeof($discounts) || !($quotation->isLocked()))}
                        <div class="current-edit" id="voucher_form" style="display:none;">
                            {include file='./_discount_form.tpl'}
                        </div>
                        <div id="discount_table">
                            {include file='./_discount_table.tpl'}
                        </div>
                    {/if}
                </div>
            </div>
            <div class="col-xs-6">
                <div id="totals_panel" class="panel panel-total">
                    <div class="panel-heading">
                        {l s='Totals' mod='roja45quotationspro'}
                    </div>
                    {include file='./_quotation_totals.tpl'}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-actions clearfix">
                    <div class="panel-heading">
                        {l s='Actions' mod='roja45quotationspro'}
                    </div>
                    <div class="panel-body">
                        {if !$quotation->is_template}
                            <button type="button" class="btn btn-secondary btn-lg saveAsTemplate disabled-while-saving">
                                <i class="icon-files-o"></i>
                                {l s='Save As Template' mod='roja45quotationspro'}
                            </button>
                            {if $has_account}
                                <button type="button" id="addQuotationToOrder"
                                    class="btn btn-secondary btn-lg disabled-while-saving">
                                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                                    {l s='Create Order' mod='roja45quotationspro'}
                                </button>
                            {/if}
                        {else}
                            <button type="button"
                                class="btn btn-secondary btn-lg pull-right createQuote disabled-while-saving">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                {l s='Create Quote' mod='roja45quotationspro'}
                            </button>
                        {/if}

                        {if !$quotation->is_template}
                            <button type="button"
                                onclick="location.href='{$quotationspro_link}&action=downloadPDFQuotation&id_roja45_quotation={$quotation->id}'"
                                id="downloadPDFQuotation"
                                class="btn btn-secondary btn-lg downloadPDFQuotation disabled-while-saving">
                                <i class="icon-file-pdf-o" aria-hidden="true"></i>
                                {l s='Download PDF' mod='roja45quotationspro'}
                            </button>
                            <button type="button" id="sendCustomerQuotation"
                                class="btn btn-primary btn-lg sendCustomerQuotation disabled-while-saving pull-right"
                                {if !$has_account}disabled="disabled" {/if}>
                                <i class="icon-envelope-o" aria-hidden="true"></i>
                                {l s='Review & Send' mod='roja45quotationspro'}
                            </button>
                        </div>
                    {/if}
                    {if !$has_account}
                        <div class="col-sm-12" style="margin-top: 5px;">
                            <div class="row pull-left">
                                <label
                                    class="label label-danger">{l s='No Customer Account' mod='roja45quotationspro'}</label>
                            </div>
                        </div>

                    {/if}
                    {if !$in_shop_context}
                        <div class="col-sm-12" style="margin-top: 5px;">
                            <div class="row pull-left">
                                <label
                                    class="label label-danger">{l s='You need to be in the SHOP context to create orders, change to the store for this quotation.' mod='roja45quotationspro'}</label>
                            </div>
                        </div>
                    {/if}
                </div>

            </div>

            {if ($show_exchange_rate == 1)}
                <div class="row" style="margin-top: 10px;">
                    <div class="col-sm-12">
                        <div class="panel panel-total">
                            <p class="alert alert-warning">
                                {l s='The quotation is displaying in the selected currency.  Please be aware that currency fluctuations may result in changes to the prices you have previously saved.' mod='roja45quotationspro'}
                            </p>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </form>
</div>