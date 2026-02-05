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
    <div class="panel roja-kpi-container">
        <div class="row justify-content-around">
            <div class="roja-kpi-container box-stats">
                <div class="roja-kpi-content">
                    <i class="icon-circle" style="color:{$status->color|escape:'html':'UTF-8'}"></i>
                    <span class="title">{l s='Status' mod='roja45quotationspro'}</span>
                    <div class="kpi-description">
                        <div class="subtitle"></div>
                        <div class="value">{$status->status|escape:"html":"UTF-8"}</div>
                    </div>
                </div>
            </div>
            <div class="roja-kpi-container box-stats">
                <div class="roja-kpi-content">
                    <i class="icon-user"></i>
                    <span class="title">{l s='Owner' mod='roja45quotationspro'}</span>
                    <div class="kpi-description">
                        <div class="subtitle"></div>
                        <div class="value">
                            {if isset($quotation) && $quotation->id_employee > 0}{$employee->firstname|escape:"html":"UTF-8"}&nbsp;{$employee->lastname|escape:"html":"UTF-8"}{else}{l s='UNASSIGNED' mod='roja45quotationspro'}{/if}
                        </div>
                    </div>
                </div>
            </div>
            <div class="roja-kpi-container box-stats">
                <div class="roja-kpi-content">
                    <i class="icon-list-alt"></i>
                    <span class="title">{l s='Reference' mod='roja45quotationspro'}</span>
                    <div class="kpi-description">
                        <div class="subtitle"></div>
                        <div class="value">{if isset($quotation->reference)}{$quotation->reference}{/if}</div>
                    </div>
                </div>
            </div>
            <div class="roja-kpi-container box-stats">
                <div class="roja-kpi-content">
                    <i class="icon-calendar-empty"></i>
                    <span class="title">{l s='Received' mod='roja45quotationspro'}</span>
                    <div class="kpi-description">
                        <div class="subtitle"></div>
                        <div class="value">
                            {if isset($quotation->date_add)}{dateFormat date=$quotation->date_add full=false}{/if}</div>
                    </div>
                </div>
            </div>
            {if ($quotation->purchase_date && $quotation->purchase_date != "0000-00-00 00:00:00")}
                <div class="roja-kpi-container box-stats">
                    <div class="roja-kpi-content">
                        <i class="icon-calendar-empty"></i>
                        <span class="title">{l s='Ordered On' mod='roja45quotationspro'}</span>
                        <div class="kpi-description">
                            <div class="subtitle"></div>
                            <div class="value">
                                {if isset($quotation)}{dateFormat date=$quotation->purchase_date full=false}{/if}</div>
                        </div>
                    </div>
                </div>
                <div class="roja-kpi-container box-stats">
                    <div class="roja-kpi-content">
                        <i class="icon-money"></i>
                        <span class="title">{l s='Order Total' mod='roja45quotationspro'}</span>
                        <div class="kpi-description">
                            <div class="subtitle"></div>
                            <div class="value">
                                {if isset($total_paid)}{displayPrice price=$total_paid currency=$currency->id}{/if}</div>
                        </div>
                    </div>
                </div>
            {else}
                <div class="roja-kpi-container box-stats">
                    <div class="roja-kpi-content">
                        <i class="icon-book"></i>
                        <span class="title">{l s='Products' mod='roja45quotationspro'}</span>
                        <div class="kpi-description">
                            <div class="subtitle"></div>
                            <div class="value">{$quotation_products|count}</div>
                        </div>
                    </div>
                </div>
                <div class="roja-kpi-container box-stats">
                    <div class="roja-kpi-content">
                        <i class="icon-money"></i>
                        <span class="title">{l s='Quotation Total' mod='roja45quotationspro'}</span>
                        <div class="kpi-description">
                            <div class="subtitle"></div>
                            <div class="value">
                                {if isset($quotation)}{displayPrice price=Tools::ps_round(Tools::convertPrice($total_price, $currency), 2) currency=$currency->id}{/if}
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
            <div class="roja-kpi-container box-stats">
                <div class="roja-kpi-content">
                    <i class="icon-calendar-empty"></i>
                    <span class="title">{l s='Expires' mod='roja45quotationspro'}</span>
                    <div class="kpi-description">
                        <div class="subtitle"></div>
                        <div class="value">
                            {if ($quotation->expiry_date != '0000-00-00 00:00:00')}{if isset($expiry_date_formatted)}{$expiry_date_formatted|escape:"html":"UTF-8"}{/if}
                            {/if}</div>
                    </div>
                </div>
            </div>
            <div class="roja-kpi-container box-stats">
                <div class="roja-kpi-content">
                    <i class="icon-comments"></i>
                    <span class="title">{l s='Messages' mod='roja45quotationspro'}</span>
                    <div class="kpi-description">
                        <div class="subtitle blink">{if $unread > 0}<p class="messages-highlight">{l s='New Message' mod='roja45quotationspro'}</p>{/if}</div>
                        <div class="value">{$messages|count}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="tab-content col-md-12">
            <div class="row">
                <div class="col-lg-2">
                    <div class="panel clearfix">
                        <div class="panel-heading">
                            <span {if $deleted}style="color: grey !important;font-style:italic !important;" {/if}>
                                <i class="icon-inbox"></i>
                                {l s='Customer' mod='roja45quotationspro'}
                                {if $deleted}
                                    - {l s='(DELETED)' mod='roja45quotationspro'}
                                {/if}
                            </span>

                            <div class="panel-heading-action">
                                <button class="btn btn-primary btn-lg" id="releaseRequest" href=""
                                    {if isset($quotation) && $quotation->id_employee == 0}style="display:none;" {/if}>
                                    <i class="icon-edit"></i>
                                    {l s='Release' mod='roja45quotationspro'}
                                </button>
                                <button class="btn btn-primary btn-lg {if $deleted}disabled{/if}" id="claimRequest"
                                    href="" {if isset($quotation) && $quotation->id_employee > 0}style="display:none;"
                                    {/if}>
                                    <i class="icon-edit"></i>
                                    {l s='Claim' mod='roja45quotationspro'}
                                </button>
                            </div>
                        </div>
                        <form id="roja45quotationspro_form" class="defaultForm form-horizontal"
                            action="{$quotationspro_link|escape:'html':'UTF-8'}&submiteditroja45_quotationspro=1"
                            method="post" enctype="multipart/form-data" novalidate="">
                            <input type="hidden" name="id_roja45_quotation"
                                value="{$quotation->id|escape:'html':'UTF-8'}">
                            {if isset($quotation) && $quotation->id_employee > 0}
                                <input type="hidden" name="customer_employee" id="customer_employee" disabled="disabled"
                                    value="{$employee->firstname|escape:"html":"UTF-8"}&nbsp;{$employee->lastname|escape:"html":"UTF-8"}" />
                            {else}
                                <input type="hidden" name="customer_employee" id="customer_employee" disabled="disabled"
                                    value="{l s='UNASSIGNED' mod='roja45quotationspro'}" />
                            {/if}
                            <div class="panel-body">
                                <div class="form-horizontal">
                                    <!--
                                {if isset($multistore_active) && $multistore_active}
                                <div class="form-group">
                                    <label class="form-control-label label-on-top col-12">{l s='Shop' mod='roja45quotationspro'}</label>
                                    <div class="col-lg-12">
                                        <p class="form-control-static">{$quotation->shop_name|escape:"html":"UTF-8"}</p>
                                    </div>
                                </div>
                                {/if}
                                -->

                                    <div class="form-group">
                                        <label
                                            class="form-control-label label-on-top col-12">{l s='Quote Name' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-12">
                                            <input type="text" name="quote_name" id="quote_name"
                                                value="{$quotation->quote_name|escape:"html":"UTF-8"}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label
                                            class="form-control-label label-on-top col-lg-12">{l s='Firstname' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-12">
                                            <input type="text" {if $has_account}disabled="disabled" {/if}
                                                name="firstname" id="customer_firstname"
                                                value="{$quotation->firstname|escape:"html":"UTF-8"}"
                                                onfocus='if (this.value == "pending...") this.value=""' />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            class="form-control-label label-on-top col-lg-12">{l s='Lastname' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-12">
                                            <input type="text" {if $has_account}disabled="disabled" {/if}
                                                name="lastname" id="customer_lastname"
                                                value="{$quotation->lastname|escape:"html":"UTF-8"}"
                                                onfocus='if (this.value == "pending...") this.value=""' />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            class="form-control-label label-on-top col-12">{l s='Customer Email' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-12">
                                            <input type="text" {if $has_account}disabled="disabled" {/if} name="email"
                                                id="customer_email" value="{$quotation->email|escape:"html":"UTF-8"}"
                                                onfocus='if (this.value == "pending...") this.value=""' />
                                        </div>
                                        {if !$has_account}
                                            <div class="col-lg-12">
                                                <label class=""
                                                    style="color: red;margin-top: 7px;font-weight:600;">{l s='No Customer Account' mod='roja45quotationspro'}</label>
                                            </div>
                                        {/if}
                                    </div>
                                    {if $has_account}
                                        <div class="form-group">
                                            <label
                                                class="form-control-label label-on-top col-12">{l s='Customer Group' mod='roja45quotationspro'}</label>
                                            <div class="col-lg-12">
                                                <input type="text" disabled="disabled" name="group"
                                                    id="customer_group" value="{$customer_group_name|escape:"html":"UTF-8"}" />
                                            </div>
                                        </div>
                                        {if $customer_group_discount > 0}
                                        <div class="form-group">
                                            <label
                                                    class="form-control-label label-on-top col-12">{l s='Customer Group Discount' mod='roja45quotationspro'}</label>
                                            <div class="col-lg-12">
                                                <div class="input-group">
                                                    <input type="text" disabled="disabled" name="group"
                                                           id="customer_group" value="{$customer_group_discount|escape:"html":"UTF-8"}" />
                                                    <div class="input-group-addon">%</div>

                                                </div>

                                            </div>
                                        </div>
                                        {/if}
                                    {/if}
                                    <div class="form-group" {if $has_account}style="display:none;" {/if}>
                                        <label class="form-control-label label-on-top col-12"></label>
                                        <div class="col-lg-12">
                                            <button data-controller="{$quotationspro_link|escape:'htmlall':'UTF-8'}"
                                                type="text" name="create_account" id="create_account" value="1"
                                                class="btn btn-sm btn-secondary btn-create-customer">{l s='Create Account' mod='roja45quotationspro'}</button>
                                            <button
                                                data-customer-controller="{$link->getAdminLink('AdminCustomers')|escape:'htmlall':'UTF-8'}"
                                                type="text" name="find_account" id="find_account" value="1"
                                                class="btn btn-sm btn-secondary btn-search-account">{l s='Find Account' mod='roja45quotationspro'}</button>
                                        </div>
                                    </div>
                                    {if $has_account}
                                        {if count($addresses)}
                                            <div class="form-group">
                                                <label
                                                    class="form-control-label label-on-top col-12">{l s='Invoice Address' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <select class="form-control" name="customer_main_address"
                                                        id="customer_main_address">
                                                        <option value="0" {if !isset($invoice_address)}selected="selected"
                                                            {/if}>{l s='Select address..' mod='roja45quotationspro'}</option>
                                                        {foreach $addresses as $address}
                                                            <option value="{$address.id_address|escape:"html":"UTF-8"}"
                                                                {if (isset($invoice_address) && ($invoice_address->id==$address.id_address))}selected="selected"
                                                                {/if}>{$address.alias|escape:"html":"UTF-8"}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        {/if}
                                        {if count($addresses)}
                                            <div class="form-group">
                                                <label
                                                    class="form-control-label label-on-top col-12">{l s='Delivery Address' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <select class="form-control" name="customer_delivery_address"
                                                        id="customer_delivery_address">
                                                        <option value="0" {if !isset($delivery_address)}selected="selected"
                                                            {/if}>{l s='Select address..' mod='roja45quotationspro'}</option>
                                                        {foreach $addresses as $address}
                                                            <option value="{$address.id_address|escape:"html":"UTF-8"}"
                                                                {if (isset($delivery_address) && ($delivery_address->id==$address.id_address))}selected="selected"
                                                                {/if}>{$address.alias|escape:"html":"UTF-8"}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        {/if}
                                        <div class="form-group">
                                            <div class="col-lg-12">
                                                <button href="#"
                                                    class="btn btn-primary {if $has_account==1}add_customer_address_link ajax-add-address {else} hidden{/if}">{l s='Add customer address' mod='roja45quotationspro'}</button>
                                            </div>
                                        </div>
                                        <div id="add_customer_address" class="form-group add-address-container">
                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Address Alias' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_alias"
                                                        id="customer_address_alias" class="" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Customer Name' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_firstname"
                                                        id="customer_address_firstname" class=""
                                                        value="{$quotation->firstname|escape:"html":"UTF-8"}" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Customer Lastname' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_lastname"
                                                        id="customer_address_lastname" class=""
                                                        value="{$quotation->lastname|escape:"html":"UTF-8"}" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Address' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_line1"
                                                        id="customer_address_line1" class="" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-lg-12">{l s='Address (2)' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_line2"
                                                        id="customer_address_line2" class="" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Address City/Town' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_city"
                                                        id="customer_address_city" class="" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Zip/postal code' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_zip" id="customer_address_zip"
                                                        class="" value="" />
                                                </div>
                                            </div>

                                            <div class="form-group" {if !count($states)}style="display:none;" {/if}>
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='State' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <select id="customer_address_state" name="customer_address_state">
                                                        <option value="0" {if ($quotation->id_state==0)}selected="selected"
                                                            {/if}>{l s='Select a state' mod='roja45quotationspro'}</option>
                                                        {foreach $states as $state}
                                                            <option value="{$state.id_state|escape:'htmlall':'UTF-8'}"
                                                                {if ($quotation->id_state==$state.id_state)}selected="selected"
                                                                {/if}>{$state.name|escape:'htmlall':'UTF-8'}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Country' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <select id="customer_address_country" name="customer_address_country"
                                                        data-target-states="customer_address_state">
                                                        {foreach $countries as $country}
                                                            <option value="{$country.id_country|escape:'htmlall':'UTF-8'}"
                                                                {if ($quotation->id_country==$country.id_country)}selected="selected"
                                                                {/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12"><span
                                                        class="text-danger">*</span>{l s='Telephone' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_address_telephone"
                                                        id="customer_address_telephone" class="" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12">{l s='Company' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_company" id="customer_company"
                                                        class="" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-lg-12">{l s='Identification number' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_dni" id="customer_dni" class=""
                                                        value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label
                                                    class="col-lg-12">{l s='VAT Number' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <input type="text" name="customer_vat_number" id="customer_vat_number"
                                                        class="" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-12"></label>
                                                <div class="col-lg-12">
                                                    <button href="#"
                                                        class="btn btn-secondary ajax-close-add-address pull-left">{l s='Close' mod='roja45quotationspro'}</button>
                                                    <a href="#"
                                                        class="btn btn-primary ajax-save-customer-address pull-right">{l s='Save' mod='roja45quotationspro'}</a>
                                                </div>
                                            </div>
                                        </div>

                                    {/if}

                                    <div class="form-group">
                                        <label
                                            class="form-control-label label-on-top col-12">{l s='Expires' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-12">
                                            <input type="text" name="expires" id="expires"
                                                value="{if ($quotation->expiry_date != '0000-00-00 00:00:00')}{if isset($expiry_date_formatted)}{$expiry_date_formatted|escape:"html":"UTF-8"}{/if} {/if}" />
                                        </div>
                                    </div>


                                    {if $languages|@count > 1}
                                        <div class="form-group">
                                            <label class="form-control-label label-on-top col-12">
                                                {l s='Language' mod='roja45quotationspro'}
                                            </label>
                                            <div class="col-lg-12">
                                                <select class="form-control" name="quote_language" id="quote_language">
                                                    {foreach $languages as $language}
                                                        <option value="{$language.id_lang|escape:"html":"UTF-8"}"
                                                            {if ($quotation->id_lang==$language.id_lang)}selected="selected"
                                                            {/if}>{$language.name|escape:"html":"UTF-8"}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    {else}
                                        <div class="row">
                                            <div class="form-group">
                                                <input type="hidden" name="quote_language" id="quote_language"
                                                    value="{$languages[0].id_lang|escape:"html":"UTF-8"}" />
                                                <label
                                                    class="form-control-label label-on-top col-12">{l s='Language' mod='roja45quotationspro'}</label>
                                                <div class="col-lg-12">
                                                    <p class="form-control-static">
                                                        {if isset($languages[0].name)}
                                                            {$languages[0].name|escape:"html":"UTF-8"}
                                                        {else}
                                                            {l s='n/a' mod='roja45quotationspro'}
                                                        {/if}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    {if $currencies|@count > 1}
                                        <div class="form-group">
                                            <label class="form-control-label label-on-top col-12">
                                                {l s='Currency' mod='roja45quotationspro'}
                                            </label>
                                            <div class="col-lg-12">
                                                <select class="form-control" name="quote_currency" id="quote_currency">
                                                    {foreach $currencies as $currencyObj}
                                                        <option value="{$currencyObj.id_currency|escape:"html":"UTF-8"}"
                                                            {if ($quotation->id_currency==$currencyObj.id_currency)}selected="selected"
                                                            {/if}>{$currencyObj.name|escape:"html":"UTF-8"}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    {else}
                                        <input type="hidden" name="quote_currency"
                                            value="{$quotation->id_currency|escape:"html":"UTF-8"}" />
                                    {/if}

                                    {if (!$invoice_address && !$delivery_address)}
                                        <div class="form-group">
                                            <label class="form-control-label label-on-top col-12">
                                                {l s='Select country' mod='roja45quotationspro'}
                                            </label>
                                            <div class="col-lg-12">
                                                <select class="form-control" name="tax_country" id="tax_country"
                                                    data-target-states="tax_state">
                                                    <option value="0">{l s='Select country' mod='roja45quotationspro'}
                                                    </option>
                                                    {foreach $countries as $country}
                                                        <option value="{$country.id_country|escape:"html":"UTF-8"}"
                                                            {if ($quotation->id_country==$country.id_country)}selected="selected"
                                                            {/if}>{$country.name|escape:"html":"UTF-8"}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                        {if (count($states) > 0)}
                                            <div class="form-group">
                                                <label class="form-control-label label-on-top col-12">
                                                    {l s='Select state' mod='roja45quotationspro'}
                                                </label>
                                                <div class="col-lg-12">
                                                    <select class="form-control" name="tax_state" id="tax_state"
                                                        {if $currencies|@count ==0}disabled="disabled" {/if}>
                                                        <option value="0">{l s='Select state' mod='roja45quotationspro'}
                                                        </option>
                                                        {foreach $states as $state}
                                                            <option value="{$state.id_state|escape:"html":"UTF-8"}"
                                                                {if ($quotation->id_state==$state.id_state)}selected="selected"
                                                                {/if}>{$state.name|escape:"html":"UTF-8"}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        {/if}
                                    {/if}
                                    <div class="form-group">
                                        <label
                                            class="form-control-label label-on-top col-12">{l s='Display Taxes' mod='roja45quotationspro'}</label>
                                        <div class="col-lg-12">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_TAXES"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_on" value="1"
                                                    {if ($quotation->calculate_taxes == 1)}checked="checked" {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_TAXES"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_off" value="0"
                                                    {if ($quotation->calculate_taxes == 0)}checked="checked" {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_TAXES_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>

                                        </div>
                                    </div>
                                    {if $show_taxes != $use_taxes}
                                        <div class="form-group">
                                            <div class="col-lg-12">
                                                <p class="alert alert-warning">
                                                    {if $show_taxes}{l s='The customer group is configured to use taxes, all correspondance with the customer will include tax.' mod='roja45quotationspro'}{else}{l s='The customer group is configured to not use taxes, all correspondance with the customer will not include tax.' mod='roja45quotationspro'}{/if}
                                                </p>
                                            </div>
                                        </div>
                                    {/if}
                                    <div class="form-group">
                                        <label class="form-control-label label-on-top col-12">
                                            {l s='Select Tax Address' mod='roja45quotationspro'}
                                        </label>
                                        <div class="col-lg-12">
                                            <select class="form-control" name="customer_tax_address"
                                                id="customer_tax_address">
                                                <option value="21"
                                                    {if ($quotation->id_address_tax==RojaQuotation::TAX_INVOICE_ADDRESS)}selected="selected"
                                                    {/if}>{l s='Use Invoice Address' mod='roja45quotationspro'}</option>
                                                <option value="22"
                                                    {if ($quotation->id_address_tax==RojaQuotation::TAX_DELIVERY_ADDRESS)}selected="selected"
                                                    {/if}>{l s='Use Delivery Address' mod='roja45quotationspro'}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div id="quotationspro_buttons" class="row">
                                    {include file='./quotationview_buttons.tpl'}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-10">
                    <div class="row">
                        <div class="col-lg-6">
                            <div id="quote_request_details" class="panel header-panel">
                                <div class="panel-heading">
                                    <i class="icon-file"></i>{l s='Request Details' mod='roja45quotationspro'}</span>
                                </div>
                                <div class="form-horizontal">
                                    <div class="row">
                                        {if $old_form_data}
                                            {foreach $request AS $key => $field}
                                                <div class="row">
                                                    <label
                                                        class="control-label col-lg-5">{$field['name']|escape:"html":"UTF-8"}</label>
                                                    <div class="col-lg-7">
                                                        <p class="form-control-static">{$field['value']|escape:"html":"UTF-8"}
                                                        </p>
                                                    </div>
                                                </div>
                                            {/foreach}
                                        {else}
                                            {if $request}
                                                {assign var="col_width" value=12/count($request)}
                                                {foreach $request AS $key => $column}
                                                    <div class="col-lg-{$col_width|escape:'htmlall':'UTF-8'}">
                                                        {foreach $column AS $key => $field}
                                                            <div class="row">
                                                                <label
                                                                    class="control-label col-lg-5">{$field['label']|escape:"html":"UTF-8"}
                                                                    : </label>
                                                                <div class="col-lg-7">
                                                                    <p class="form-control-static">
                                                                        {if empty($field['value'])}-{else}{$field['value']|escape:"html":"UTF-8"}{/if}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                {/foreach}
                                            {/if}
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {if $quotation->id}
                            <div class="col-lg-6">
                                <div class="panel header-panel">
                                    <div class="panel-heading">
                                        <i class="icon-eye-close"></i> {l s='Customer Documents' mod='roja45quotationspro'}
                                        <span class="badge">{count($quotation_documents)|escape:"html":"UTF-8"}</span>
                                        <div class="panel-heading-action">
                                            <button id="add_quotation_document" type="button"
                                                class="btn btn-primary add-document">
                                                <i class="icon-plus"></i>
                                                {l s='Add Document' mod='roja45quotationspro'}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        {if !count($quotation_documents)}
                                            <div class="alert alert-info">
                                                {l s='No documents available.' mod='roja45quotationspro'}</div>
                                        {/if}
                                        <div id="notes_table" {if count($quotation_documents) == 0} style="display:none;"
                                            {/if}>
                                            {include file='./_documents_table.tpl'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                    {if $quotation->id}
                        <div class="row">
                            <div class="col-xs-12">
                                {include file='./quotationview_quotation.tpl'}
                            </div>
                        </div>
                    {/if}
                </div>
            </div>

            {if $quotation->id}
                <div class="row">
                    <div class="col-xs-6">
                        <div class="panel">
                            <div class="panel-heading">
                                <i class="icon-eye-close"></i> {l s='Orders Raised' mod='roja45quotationspro'} <span
                                    class="badge">{count($quotation_orders)|escape:"html":"UTF-8"}</span>
                            </div>
                            <div class="alert alert-info">
                                {l s='All orders created from this quotation.' mod='roja45quotationspro'}</div>
                            <div class="panel panel-notes notes-container">
                                {if $quotation_orders}
                                    <div id="notes_table">
                                        {include file='./_orders_table.tpl'}
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <i class="icon-eye-close"></i> {l s='Customer Notes' mod='roja45quotationspro'} <span
                                    class="badge">{count($notes)|escape:"html":"UTF-8"}</span>
                            </div>
                            <div class="alert alert-info">
                                {l s='Notes will be displayed to all employees but not to customers.' mod='roja45quotationspro'}
                            </div>
                            <div class="panel panel-notes notes-container">
                                <div id="notes_table" {if count($notes) == 0} style="display:none;" {/if}>
                                    {include file='./_notes_table.tpl'}
                                </div>
                            </div>
                            <div class="panel panel-total note-container">
                                <div class="panel-heading">
                                    {l s='Add a private note' mod='roja45quotationspro'}
                                </div>
                                <form id="customer_note" class="form-horizontal" method="post"
                                    action="{$quotationspro_link|escape:'html':'UTF-8'}">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <textarea name="note" id="noteContent"
                                                onkeyup="$('#submitQuotationNote').removeAttr('disabled');">{if isset($customer_note)}{$customer_note|escape:"html":"UTF-8"}{/if}</textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <button type="submit" id="submitQuotationNote"
                                                class="btn btn-secondary btn-lg pull-right" disabled="disabled">
                                                <i class="icon-save"></i>
                                                {l s='Save' mod='roja45quotationspro'}
                                            </button>
                                        </div>
                                    </div>
                                    <span id="note_feedback"></span>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="panel">
                            <div class="panel-heading">
                                <i class="icon-envelope"></i> {l s='Last Message' mod='roja45quotationspro'}
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <th width="25%"><span
                                                class="title_box">{l s='Received' mod='roja45quotationspro'}</span></th>
                                        <th width="65%"><span
                                                class="title_box">{l s='Message' mod='roja45quotationspro'}</span></th>
                                        <th width="10%"><span
                                                class="title_box">{l s='Read' mod='roja45quotationspro'}</span></th>
                                    </thead>
                                    <tbody>
                                        {if $last_message}
                                            <tr>
                                                <td>{dateFormat date=$last_message['date_add'] full=true}</td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{$customer_message_link}&id_customer_thread={$last_message.id_customer_thread|escape:"html":"UTF-8"|escape:'htmlall':'UTF-8'}">
                                                        {$last_message['message']|truncate:100|escape:"html":"UTF-8"}...
                                                    </a>
                                                </td>
                                                <td>
                                                    <a class="list-action-enable ajax_table_link ajax-update-read-link ajax-update-read-link-{$last_message.id_customer_message} {if ($last_message['read']==0)}action-read{else}action-not-read{/if}"
                                                        href="#" data-id-message="{$last_message.id_customer_message}">
                                                        <i class="icon-check {if ($last_message['read']==0)}hidden{/if}"
                                                            style="color:#06c000"></i>
                                                        <i class="icon-remove {if ($last_message['read']==1)}hidden{/if}"
                                                            style="color:#ef4444"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        {/if}
                                    </tbody>
                                </table>
                            </div>
                            {if $last_message && $last_message['read']=='0'}
                                <div class="panel-footer">
                                    <button id="sendCustomerMessage" href="#response-selection-link"
                                        style="font-size:15px;cursor:pointer;" class="btn btn-secondary btn-lg pull-right">
                                        {l s='Reply' mod='roja45quotationspro'}
                                    </button>
                                </div>
                            {/if}
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <i class="icon-envelope"></i> {l s='Message History' mod='roja45quotationspro'} <span
                                    class="badge">{count($messages)|escape:"html":"UTF-8"}</span>
                            </div>
                            <div class="panel-body">
                                {if count($messages)}
                                    <table class="table">
                                        <thead>
                                            <th><span class="title_box">{l s='Received' mod='roja45quotationspro'}</span></th>
                                            <th><span class="title_box">{l s='Message' mod='roja45quotationspro'}</span></th>
                                            <th><span class="title_box">{l s='Attachment' mod='roja45quotationspro'}</span></th>
                                            <th><span class="title_box">{l s='Read' mod='roja45quotationspro'}</span></th>
                                        </thead>
                                        {foreach $messages as $message}
                                            <tr>
                                                <td>{$message['date_add']|escape:'html':'UTF-8'}</td>
                                                <td>
                                                    <a target="_blank"
                                                        href="{$customer_message_link}&id_customer_thread={$message.id_customer_thread|escape:"html":"UTF-8"}">
                                                        {$message['message']|truncate:100|escape:"html":"UTF-8"}...
                                                    </a>
                                                </td>
                                                <td>
                                                    {if !empty($message.file_name)}
                                                        <a data-file="{$message['file_name'] nofilter}" target="_blank"
                                                            rel="noopener noreferrer nofollow" href="{$message['file_loc']}">
                                                            <i class="icon-file"></i>
                                                        </a>
                                                    {/if}
                                                </td>
                                                <td>
                                                    <a class="list-action-enable ajax_table_link ajax-update-read-link ajax-update-read-link-{$message.id_customer_message} {if ($message['read']==0)}action-read{else}action-not-read{/if}"
                                                        href="#" data-id-message="{$message.id_customer_message}">
                                                        <i class="icon-check {if ($message['read']==0)}hidden{/if}"
                                                            style="color:#06c000"></i>
                                                        <i class="icon-remove {if ($message['read']==1)}hidden{/if}"
                                                            style="color:#ef4444"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                {elseif isset($customer) && isset($customer->firstname) & isset($customer->lastname)}
                                    <p class="text-muted text-center">
                                        {l s='%s %s has never contacted you' sprintf=[$customer->firstname, $customer->lastname] mod='roja45quotationspro'}
                                    </p>
                                {/if}
                            </div>
                            <div class="panel-footer">
                                <button id="sendCustomerMessage" href="#response-selection-link"
                                    style="font-size:15px;cursor:pointer;" class="btn btn-secondary btn-lg pull-right">
                                    {l s='Send Message to Customer' mod='roja45quotationspro'}
                                </button>
                            </div>
                        </div>
                        <div class="panel">
                            <div class="panel-heading">
                                <i class="icon-envelope"></i> {l s='Set Status' mod='roja45quotationspro'}
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-lg-3">
                                        {l s='Set Status' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-5">
                                        <select class="form-control" name="quotation_status" id="quotation_status">
                                            {foreach $quotation_statuses as $quotation_status}
                                                <option
                                                    value="{$quotation_status.id_roja45_quotation_status|escape:"html":"UTF-8"}"
                                                    {if ($quotation->id_roja45_quotation_status==$quotation_status.id_roja45_quotation_status)}selected="selected"
                                                    {/if}>{$quotation_status.status|escape:"html":"UTF-8"}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <a href="#"
                                    class="btn btn-primary btn-set-status">{l s='Set Status' mod='roja45quotationspro'}</a>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>

<div class="quotationspro_request_dialog_overlay"></div>

<div id="quotationspro_quotation_dialog" class="quotationspro_quotation_dialog quotationspro_dialog modal-dialog"
    style="display:none">
    <form id="sendQuotationForm" method="post" action="{$quotationspro_link|escape:'html':'UTF-8'}"
        enctype="multipart/form-data" novalidate="">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{l s='Send Message' mod='roja45quotationspro'}</h3>
                <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer quotationspro_request buttons">
                <div class="button-container">
                    <button type="submit" id="cancelSendQuotationToCustomer" class="btn btn-secondary btn-lg pull-left">
                        <i class="icon-remove"></i>
                        {l s='Cancel' mod='roja45quotationspro'}
                    </button>
                    <button type="submit" id="sendQuotationToCustomer" class="btn btn-primary btn-lg pull-right">
                        {l s='Send' mod='roja45quotationspro'}
                        <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="quotationspro_message_dialog" class="quotationspro_message_dialog quotationspro_dialog modal-dialog"
    style="display:none">
    <form id="sendMessageForm" method="post" action="{$quotationspro_link|escape:'html':'UTF-8'}"
        enctype="multipart/form-data" novalidate="">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{l s='Send Message' mod='roja45quotationspro'}</h3>
                <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="message_template">
                <input type="hidden" name="id_roja45_quotation" value="{$quotation->id|escape:"html":"UTF-8"}">
                <div>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-lg-3">
                                {l s='Load template' mod='roja45quotationspro'}
                            </label>
                            <div class="col-lg-5">
                                <select class="form-control" name="select_quotation_answer"
                                    id="select_quotation_answer">
                                    {foreach $templates AS $template}
                                        <option data-id-answer="{$template.id|escape:'html':'UTF-8'}"
                                            data-type="{$template.type|escape:'html':'UTF-8'}"
                                            value="{$template.id|escape:'html':'UTF-8'}">
                                            {$template.name|escape:"html":"UTF-8"}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <a id="response-selection-link"></a>
                                <button type="submit" id="loadMessageTemplate" class="btn btn-secondary">
                                    <i class="icon-download"></i>
                                    {l s='Load' mod='roja45quotationspro'}
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">
                                {l s='Subject' mod='roja45quotationspro'}
                            </label>
                            <div class="col-lg-5">
                                <input type="text" name="message_subject"
                                    value="{if isset($message_subject)}{$message_subject|escape:'htmlall':'UTF-8'}{/if}" />
                            </div>
                        </div>
                        {if count($quotation_documents)}
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    {l s='Attach Documents' mod='roja45quotationspro'}
                                </label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="select_quotation_documents[]"
                                        id="select_quotation_documents" multiple>
                                        {foreach $quotation_documents AS $document}
                                            <option value="{$document.id_roja45_quotation_document|escape:'html':'UTF-8'}">
                                                {$document.display_name|escape:"html":"UTF-8"}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
                <div id="loaded_customer_message">
                    <textarea id="mce_customer_message" name="response_content" class="rte autoload_rte"></textarea>
                </div>
            </div>
            <div class="modal-footer quotationspro_request buttons">
                <div class="button-container">
                    <button type="submit" id="cancelSendMessageToCustomer" class="btn btn-secondary btn-lg pull-left">
                        <i class="icon-remove"></i>
                        {l s='Cancel' mod='roja45quotationspro'}
                    </button>
                    <button type="submit" id="sendMessageToCustomer" class="btn btn-primary btn-lg pull-right">
                        {l s='Send' mod='roja45quotationspro'}
                        <i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="quotationspro_request_dialog" class="quotationspro_request_dialog quotationspro_dialog modal-dialog"
    style="display:none">
    <form action="{$quotationspro_link|escape:'html':'UTF-8'}&action=submitNewCustomerOrder" method="post"
        id="quotationspro_form" class="std box">
        <input type="hidden" name="id_roja45_quotation" value="{$quotation->id|escape:'html':'UTF-8'}">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{l s='Select a Payment Method' mod='roja45quotationspro'}</h3>
                <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
            </div>
            <div id="quotationspro_request_column_12" class="quotationspro_request modal-body">
                <div class="quotationspro_request_column_container">
                    <div class="form-group">
                        <label class="control-label">{l s='Payment Method' mod='roja45quotationspro'}</label>
                        <select name="payment_method">
                            {foreach from=$payment_methods item=payment_method}
                                <option value="{$payment_method.name|escape:'htmlall':'UTF-8'}">
                                    {$payment_method.displayName|escape:'htmlall':'UTF-8'}
                                    ({$payment_method.name|escape:'htmlall':'UTF-8'})</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{l s='Initial Status' mod='roja45quotationspro'}</label>
                        <select name="order_state">
                            {foreach from=$order_states item=order_state}
                                <option value="{$order_state.id_order_state|escape:'htmlall':'UTF-8'}">
                                    {$order_state.name|escape:'htmlall':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer quotationspro_request buttons">
                <div class="button-container">
                    <a id="quotationspro_createorder" class="btn btn-primary btn-create-order" href="#"
                        title="{l s='Create Order' mod='roja45quotationspro'}" rel="nofollow">
                        <span>{l s='Create Order' mod='roja45quotationspro'}</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="quotationspro_addproduct_modal" class="quotationspro_addproduct_modal modal" aria-hidden="false"
    style="display: none;">
    <form action="{$quotationspro_link|escape:'html':'UTF-8'}" method="post" id="quotationspro_addproduct_form"
        class="std box">
        <input type="hidden" name="ajax" value="1" />
        <input type="hidden" name="action" value="addSelectedProducts" />
        <input type="hidden" name="id_roja45_quotation" value="{$quotation->id|escape:'html':'UTF-8'}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">X</span>
                        <span class="sr-only">{l s='Close' mod='roja45quotationspro'}</span>
                    </button>
                    <h4 id="modalTitle" class="modal-title">{l s='Select Products' mod='roja45quotationspro'}</h4>
                </div>
                <div id="modalBody" class="modal-body row">
                    <div class="col-lg-12 search">
                        <div class="form-horizontal">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='Multiple search (Name, Reference, UPC, EAN)' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <input type="text" autocomplete="off" name="multiple_search" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='Category' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <select class="form-control" name="product_category">
                                            <option value="0">-</option>
                                            {foreach $categories as $category}
                                                <option value="{$category.id_category|escape:"html":"UTF-8"}">
                                                    {$category.name|escape:"html":"UTF-8"}</option>
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
                                        <input type="text" name="results_per_page" value="10" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <div class="form-group">
                                    <label class="col-lg-12">
                                        {l s='Page #' mod='roja45quotationspro'}
                                    </label>
                                    <div class="col-lg-12">
                                        <input type="text" name="page_number" disabled value="1" />
                                        <select class="form-control" name="page_number" style="display:none">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="col-lg-12">&nbsp;</label>
                                    <div class="col-lg-12">
                                        <button class="btn btn-primary btn-search-products"
                                            title="{l s='Search' mod='roja45quotationspro'}"><i
                                                class="icon-search"></i></button>
                                        <button class="btn btn-secondary btn-reset-search"
                                            title="{l s='Reset' mod='roja45quotationspro'}"><i
                                                class="icon-refresh"></i></button>
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
                    <a id="addCloseSelectedProducts"
                        class="btn btn-primary btn-add-close-selected-products disabled">{l s='Add Selected' mod='roja45quotationspro'}</a>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="quotationspro_save_template" class="quotationspro_save_template quotationspro_dialog modal-dialog"
    style="display:none">
    <form action="{$quotationspro_link|escape:'html':'UTF-8'}&action=saveAsTemplate" method="post"
        id="quotationspro_save_template_form" class="std box">
        <input type="hidden" name="id_roja45_quotation" value="{$quotation->id|escape:'html':'UTF-8'}">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{l s='Please provide the template name' mod='roja45quotationspro'}</h3>
                <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
            </div>
            <div id="quotationspro_request_column_12" class="quotationspro_request modal-body">
                <div class="quotationspro_request_column_container">
                    <div class="form-group">
                        <label class="control-label">{l s='Template Name' mod='roja45quotationspro'}</label>
                        <input type="text" name="template_name"
                            value="{if !empty($quotation->template_name)}{$quotation->template_name|escape:'html':'UTF-8'}{elseif !empty($quotation->quote_name)}{$quotation->quote_name|escape:'html':'UTF-8'}{/if}" />
                    </div>
                </div>
            </div>
            <div class="modal-footer quotationspro_request buttons">
                <div class="button-container">
                    <a id="quotationspro_savetemplate" class="btn btn-primary btn-save-template" href="#"
                        title="{l s='Save Template' mod='roja45quotationspro'}" rel="nofollow">
                        <span>{l s='Save Template' mod='roja45quotationspro'}</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="quotationspro_select_customer" class="quotationspro_select_customer quotationspro_dialog modal-dialog"
    style="display:none">
    <form action="{$quotationspro_link|escape:'html':'UTF-8'}&action=selectCustomer" method="post"
        id="quotationspro_select_customer_form" class="std box">
        <input type="hidden" name="id_roja45_quotation" value="{$quotation->id|escape:'html':'UTF-8'}">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{l s='Please select the customer' mod='roja45quotationspro'}</h3>
                <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
            </div>
            <div class="quotationspro_request modal-body">

            </div>
            <div class="modal-footer quotationspro_request buttons">
                <div class="button-container">
                    <a class="btn btn-secondary btn-close" href="#" title="{l s='Close' mod='roja45quotationspro'}"
                        rel="nofollow">
                        <span>{l s='Close' mod='roja45quotationspro'}</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="quotationspro_add_document" class="quotationspro_add_document quotationspro_dialog modal-dialog"
    style="display:none">
    <form id="customer_document" class="form-horizontal" method="post"
        action="{$quotationspro_link|escape:'html':'UTF-8'}" enctype="multipart/form-data">
        <input type="hidden" name="id_roja45_quotation" value="{$quotation->id|escape:'html':'UTF-8'}">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{l s='Add Document' mod='roja45quotationspro'}</h3>
                <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="addDocument" />
                <input type="hidden" name="id_roja45_quotation" value="{$id_roja45_quotation|escape:"html":"UTF-8"}" />
                <div class="form-wrapper">
                    {if count($documents)}
                        <div class="form-group">
                            <label
                                class="form-control-label label-on-top col-12">{l s='Saved Documents' mod='roja45quotationspro'}</label>
                            <div class="col-12">
                                <select class="form-control" name="available_document" id="available_document">
                                    <option value="0">-</option>
                                    {foreach $documents as $document}
                                        <option value="{$document.id_roja45_document|escape:"html":"UTF-8"}">
                                            {$document.display_name|escape:"html":"UTF-8"}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    {/if}
                    <div class="form-group">
                        <label
                            class="form-control-label label-on-top col-12">{l s='Add Document' mod='roja45quotationspro'}</label>
                        <div class="col-12">
                            <input id="addDocument" type="file" name="document" class="hide"
                                accept=".pdf,.jpg,.jpeg,.png,.gix,.txt,.zip" />
                            <div class="dummyfile input-group">
                                <span class="input-group-addon"><i class="icon-file"></i></span>
                                <input id="document-name" type="text" class="disabled" name="filename" readonly />
                                <span class="input-group-btn">
                                    <button id="document-selectbutton" type="button" name="submitAddAttachments"
                                        class="btn btn-secondary">
                                        <i class="icon-folder-open"></i> {l s='Choose a file' mod='roja45quotationspro'}
                                    </button>
                                    <script>
                                        $(document).ready(function() {
                                            $('#document-selectbutton').click(function(e) {
                                                $('#addDocument').trigger('click');
                                            });
                                            $('#addDocument').change(function(e) {
                                                var val = $(this).val();
                                                var file = val.split(/[\\/]/);
                                                $('#document-name').val(file[file.length - 1]);
                                            });
                                        });
                                    </script>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-lg-12">
                    <button type="submit" id="submitAddDocument" class="btn btn-secondary btn-sml pull-right">
                        <i class="icon-save"></i>
                        {l s='Save' mod='roja45quotationspro'}
                    </button>
                </div>
            </div>
            <span id="document_feedback"></span>
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
    var id_roja45_quotation = {$id_roja45_quotation|escape:'html':'UTF-8'};
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
    var roja45_quotations_timeformat = "{$roja45_quotations_timeformat|escape:'html':'UTF-8'}";
    var roja45_quote_sent = {$roja45_quote_sent|escape:'html':'UTF-8'};

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