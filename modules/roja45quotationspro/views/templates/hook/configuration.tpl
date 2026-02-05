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

<script type="text/javascript">
    var id_default_language = {$id_default_language|escape:'html':'UTF-8'};
    var roja45_quotationspro_unknown_error = "{l s='An unexpected error has occurred, please raise this with your support provider.' mod='roja45quotationspro' js=1}";
</script>

<div id="fields_warning_dialog" title="{l s='Warning' mod='roja45quotationspro'}" style="display:none">
    <p>{l s='There are fields in a column that will be removed.  Please delete or move these first.' mod='roja45quotationspro'}
    </p>
</div>

<style>
    .bootstrap input[type=number] {
        -webkit-transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        -webkit-transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        background-color: #f5f8f9;
        background-image: none;
        border: 1px solid #c7d6db;
        border-radius: 3px;
        color: #555;
        display: block;
        font-size: 12px;
        height: 31px;
        line-height: 1.42857;
        padding: 6px 8px;
        transition: border-color .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out, -webkit-box-shadow .15s ease-in-out;
        width: 100%;
    }

    #roja45quotationspro_form li.list-group-item,
    [data-is="ps-tabs"] li.list-group-item {
        padding: 0 !important;
    }

    #roja45quotationspro_form .nav.list-group li a,
    [data-is="ps-tabs"] .nav.list-group li a {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    #roja45quotationspro_form li.list-group-item.active a,
    [data-is="ps-tabs"] li.list-group-item.active a {
        color: white;
    }

    #roja45quotationspro_form li.list-group-item a,
    [data-is="ps-tabs"] li.list-group-item a {
        color: #555;
    }
</style>

<div class="bootstrap">
    <div class="panel">
        <div class="panel-heading"></div>
        <div class="panel-body">
            <form id="roja45quotationspro_form" class="defaultForm form-horizontal"
                action="{$url|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
                <input type="hidden" name="submitConfiguration" value="1">
                <input type="hidden" name="GDPRCompliance" value>
                <div class="row">
                    <div class="col-md-2">
                        <ul class="nav list-group">
                            <li class="list-group-item active">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_general_tab">{l s='General Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_quotationcart_tab">{l s='Quotation Cart Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_shoppingcart_tab">{l s='Shopping Cart Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_quotationorder_tab">{l s='Quotation Order Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_quotationform_tab">{l s='Quotation Form Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_pdf_tab">{l s='Email/PDF Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_security_tab">{l s='Security Settings' mod='roja45quotationspro'}</a>
                            </li>
                            <li class="list-group-item">
                                <a data-toggle="tab"
                                    href="#roja45quotationspro_advanced_tab">{l s='Advanced Settings' mod='roja45quotationspro'}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content col-md-10">
                        <div id="roja45quotationspro_general_tab" class="tab-pane active">
                            <div class="panel" id="fieldset_0">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='General Settings' mod='roja45quotationspro'}
                                </div>

                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select YES to use Prestashop Customer Service system.' mod='roja45quotationspro'}">{l s='Use Customer Service?' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USE_CS"
                                                    id="ROJA45_QUOTATIONSPRO_USE_CS_on" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USE_CS'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USE_CS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USE_CS"
                                                    id="ROJA45_QUOTATIONSPRO_USE_CS_off" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USE_CS'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USE_CS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            if ($('input:radio[name=ROJA45_QUOTATIONSPRO_USE_CS]:checked')
                                            .val() == "0") {
                                                $('.cs_disabled').fadeOut('fast', function() {
                                                    $('.cs_enabled').fadeIn();
                                                });
                                            } else {
                                                $('.cs_enabled').fadeOut('fast', function() {
                                                    $('.cs_disabled').fadeIn();
                                                });
                                            }
                                            $('input:radio[name=ROJA45_QUOTATIONSPRO_USE_CS]').change(
                                        function() {
                                                if ($(
                                                        "input[name='ROJA45_QUOTATIONSPRO_USE_CS']:checked")
                                                    .val() == '0') {
                                                    $('.cs_disabled').fadeOut('fast', function() {
                                                        $('.cs_enabled').fadeIn();
                                                    });
                                                }
                                                if ($(
                                                        "input[name='ROJA45_QUOTATIONSPRO_USE_CS']:checked")
                                                    .val() == '1') {
                                                    $('.cs_enabled').fadeOut('fast', function() {
                                                        $('.cs_disabled').fadeIn();
                                                    });
                                                }
                                            });

                                        });
                                    </script>
                                    <div class="cs_enabled" style="display:none">
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                    title=""
                                                    data-original-title="{l s='Select the Customer Service email account (please ensure this user has permission to send emails with your smtp server)' mod='roja45quotationspro'}">{l s='Customer Service Account' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-7 ">
                                                <select name="ROJA45_QUOTATIONSPRO_CS_ACCOUNT" class="fixed-width-xxl">
                                                    {foreach $contacts AS $contact}
                                                        <option value="{$contact.id_contact|escape:'html':'UTF-8'}"
                                                            {if ($fields_value['ROJA45_QUOTATIONSPRO_CS_ACCOUNT'] == $contact.id_contact)}selected="selected"
                                                            {/if}>{$contact.name|escape:'html':'UTF-8'}
                                                            ({$contact.email|escape:'html':'UTF-8'})</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cs_disabled" style="display:none">
                                        <div class="form-group">
                                            <label class="control-label col-lg-4 required">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                    title=""
                                                    data-original-title="{l s='Quotation requests will be sent using this email address (please ensure this user has permission to send emails with your smtp server)' mod='roja45quotationspro'}">{l s='Email Address' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-7">
                                                <input type="text" name="ROJA45_QUOTATIONSPRO_EMAIL"
                                                    id="ROJA45_QUOTATIONSPRO_EMAIL"
                                                    value="{$fields_value['ROJA45_QUOTATIONSPRO_EMAIL']|escape:'html':'UTF-8'}"
                                                    class="" required="required">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-lg-4 required">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                    title=""
                                                    data-original-title="{l s='Name that will appear in the to field' mod='roja45quotationspro'}">{l s='Email Contact Name' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-7 ">
                                                <input type="text" name="ROJA45_QUOTATIONSPRO_CONTACT_NAME"
                                                    id="ROJA45_QUOTATIONSPRO_CONTACT_NAME"
                                                    value="{$fields_value['ROJA45_QUOTATIONSPRO_CONTACT_NAME']|escape:'html':'UTF-8'}"
                                                    class="" required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Additional email address to blind copy request' mod='roja45quotationspro'}">{l s='BCC Email' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_CONTACT_BCC"
                                                id="ROJA45_QUOTATIONSPRO_CONTACT_BCC"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_CONTACT_BCC']|escape:'html':'UTF-8'}"
                                                class="fixed-width-xxl">
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select Yes to display a label on the product list indicating the product can be quoted.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select Yes to display a label on the product list indicating the product can be quoted.' mod='roja45quotationspro'}">{l s='Display Get Quote Label' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_DISPLAY_LABEL"
                                                    id="ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_DISPLAY_LABEL'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_DISPLAY_LABEL"
                                                    id="ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_DISPLAY_LABEL'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                  title="{l s='Enable this to set the replyTo address for a quotation to the quotation owner.  Customer service email will be CC\'d' mod='roja45quotationspro'}"
                                                  data-original-title="{l s='Enable this to set the replyTo address for a quotation to the quotation owner.  Customer service email will be CC\'d' mod='roja45quotationspro'}">{l s='Quotation Emails to Owner' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER"
                                                       id="ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER_on" value="1"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER"
                                                       id="ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER_off" value="0"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_QUOTATION_EMAIL_TO_OWNER_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    {if !$is_17}
                                        <div class="form-group label_position_hidden" style="display:none;">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                    title="{l s='Select the position of the quote label.' mod='roja45quotationspro'}"
                                                    data-original-title="{l s='Select the position of the quote label.' mod='roja45quotationspro'}">{l s='Label Position' mod='roja45quotationspro'}</span>
                                            </label>

                                            <div class="col-lg-7 ">
                                                <select name="ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION"
                                                    class="fixed-width-xl">
                                                    <option value="quote-box-top-left"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION'] == "quote-box-top-left")}selected="selected"
                                                        {/if}>{l s='Top Left' mod='roja45quotationspro'}</option>
                                                    <option value="quote-box-top-right"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION'] == "quote-box-top-right")}selected="selected"
                                                        {/if}>{l s='Top Right' mod='roja45quotationspro'}</option>
                                                    <option value="quote-box-bottom-left"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION'] == "quote-box-bottom-left")}selected="selected"
                                                        {/if}>{l s='Bottom Left' mod='roja45quotationspro'}</option>
                                                    <option value="quote-box-bottom-right"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DISPLAY_LABEL_POSITION'] == "quote-box-bottom-right")}selected="selected"
                                                        {/if}>{l s='Bottom Right' mod='roja45quotationspro'}</option>
                                                </select>
                                            </div>
                                        </div>
                                    {/if}
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select Yes to automatically enable new products.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select Yes to automatically enable new products.' mod='roja45quotationspro'}">{l s='Auto Enable New Products' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_AUTOENABLENEW"
                                                    id="ROJA45_QUOTATIONSPRO_AUTOENABLENEW_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_AUTOENABLENEW'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_AUTOENABLENEW_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_AUTOENABLENEW"
                                                    id="ROJA45_QUOTATIONSPRO_AUTOENABLENEW_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_AUTOENABLENEW'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_AUTOENABLENEW_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select to hide the add to cart button for quote enabled products' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select to hide the add to cart button for quote enabled products' mod='roja45quotationspro'}">{l s='Hide Add To Cart' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_HIDEADDTOCART"
                                                    id="ROJA45_QUOTATIONSPRO_HIDEADDTOCART_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIDEADDTOCART'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIDEADDTOCART_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_HIDEADDTOCART"
                                                    id="ROJA45_QUOTATIONSPRO_HIDEADDTOCART_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIDEADDTOCART'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIDEADDTOCART_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select to hide the add to quote button for out of stock products. NB Overall stock level, will not check the specific combination stock.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select to hide the add to quote button for out of stock products. NB Overall stock level, will not check the specific combination stock.' mod='roja45quotationspro'}">{l s='Hide Add To Quote When Out Of Stock' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS"
                                                    id="ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS"
                                                    id="ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIDE_ADD_TO_QUOTE_OOS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select to hide the price for quote enabled products' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select to hide the price for quote enabled products' mod='roja45quotationspro'}">{l s='Hide Price' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_HIDEPRICE"
                                                    id="ROJA45_QUOTATIONSPRO_HIDEPRICE_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIDEPRICE'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIDEPRICE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_HIDEPRICE"
                                                    id="ROJA45_QUOTATIONSPRO_HIDEPRICE_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIDEPRICE'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIDEPRICE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Show a registration advice block to customer if quotation options are restricted by customer group' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Show a registration advice block to customer if quotation options are restricted by customer group' mod='roja45quotationspro'}">{l s='Registration suggestion on product page' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION"
                                                    id="ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION"
                                                    id="ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SHOWREGISTRATIONSUGGESTION_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select to send the quotation request directly to admin email as well as back office.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select to send the quotation request directly to admin email as well as back office.' mod='roja45quotationspro'}">{l s='Email Request To Admin' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_EMAILREQUEST"
                                                    id="ROJA45_QUOTATIONSPRO_EMAILREQUEST_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_EMAILREQUEST'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_EMAILREQUEST_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_EMAILREQUEST"
                                                    id="ROJA45_QUOTATIONSPRO_EMAILREQUEST_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_EMAILREQUEST'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_EMAILREQUEST_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Default number of days a quotation is valid for.' mod='roja45quotationspro'}">{l s='Quotes Valid For' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <div class="input-group fixed-width-xxl">
                                                <input type="text" name="ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS"
                                                    id="ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS"
                                                    value="{$fields_value['ROJA45_QUOTATIONSPRO_QUOTE_VALID_DAYS']|escape:'html':'UTF-8'}">
                                                <div class="input-group-addon">{l s='Days' mod='roja45quotationspro'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select the customer group able to request quotations' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select the customer group able to request quotations' mod='roja45quotationspro'}">{l s='Customer Groups' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-8">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>{l s='Group' mod='roja45quotationspro'}</th>
                                                        <th>{l s='Enabled' mod='roja45quotationspro'}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {foreach $customer_groups as $customer_group}
                                                        <tr>
                                                            <td>{$customer_group.name|escape:'html':'UTF-8'}</td>
                                                            <td>
                                                                <input type="checkbox"
                                                                    name="ROJA45_QUOTATIONSPRO_ENABLED_GROUPS[]"
                                                                    value="{$customer_group.id_group|escape:'html':'UTF-8'}"
                                                                    class=""
                                                                    {if in_array($customer_group.id_group, $fields_value['enabled_groups'])}checked="checked"
                                                                    {/if}>
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Allow a quotation request to be sent with no products added.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Allow quotation requests without products' mod='roja45quotationspro'}">{l s='Allow quotation requests without products' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS"
                                                    id="ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS"
                                                    id="ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_NOPRODUCTREQUESTS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                            <div class="row-margin-top alert alert-warning">
                                                {l s='Use this link to direct the user directly to the quote screen: %s' mod='roja45quotationspro' sprintf=[$quotation_request_url]}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Add an option to specify a customization cost per product line' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Add an option to specify a customization cost per product line' mod='roja45quotationspro'}">{l s='Enable product customization cost' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_on"
                                                    value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_off"
                                                    value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select how to apply the customization cost' mod='roja45quotationspro'}">{l s='Product Customization Cost Calculation' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE"
                                                class="fixed-width-xxl">
                                                <option value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE'] == 1)}selected="selected"
                                                    {/if}>{l s='Per Line' mod='roja45quotationspro'}</option>
                                                <option value="2"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_PRODUCT_CUSTOMIZATION_COST_TYPE'] == 2)}selected="selected"
                                                    {/if}>{l s='Per Quantity' mod='roja45quotationspro'}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select the form to use for quotation requests with no products.' mod='roja45quotationspro'}">{l s='Default No Product Quotation Form' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM"
                                                class="fixed-width-xxl">
                                                {foreach $forms AS $form}
                                                    <option value="{$form.id_quotation_form|escape:'html':'UTF-8'}"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DEFAULTNOPRODUCTFORM'] == $form.id_quotation_form)}selected="selected"
                                                        {/if}>{$form.form_name|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>
                        <div id="roja45quotationspro_quotationcart_tab" class="tab-pane">
                            <div class="panel" id="fieldset_01">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Quotation Cart Settings' mod='roja45quotationspro'}
                                </div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select YES to allow multiple products to be added to a quotation request (Default Yes).' mod='roja45quotationspro'}">{l s='Multiple product quotes' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLEQUOTECART"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLEQUOTECART_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLEQUOTECART_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLEQUOTECART"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLEQUOTECART_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLEQUOTECART'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLEQUOTECART_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Show popup summary window when product added to quote cart (Default Yes).' mod='roja45quotationspro'}">{l s='Show add to quote popup' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTPOPUP_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select to show the product price in the quotation request summary' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select to show the product price in the quotation request summary' mod='roja45quotationspro'}">{l s='Show Product Price in Summary' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY"
                                                    id="ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY"
                                                    id="ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SHOWPRICEINSUMMARY_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Redirect the customer after the quote request has been sent successfully.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Redirect the customer after the quote request has been sent successfully.' mod='roja45quotationspro'}">{l s='Customer Redirect' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-5">
                                            <select name="ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER"
                                                id="ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER">
                                                <option value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER'] == '1')}selected="selected"
                                                    {/if}>{l s='No' mod='roja45quotationspro'}</option>
                                                <option value="2"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER'] == '2')}selected="selected"
                                                    {/if}>{l s='Home Page' mod='roja45quotationspro'}</option>
                                                <option value="3"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTE_REDIRECT_CUSTOMER'] == '3')}selected="selected"
                                                    {/if}>{l s='Customer Account' mod='roja45quotationspro'}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select YES to check product is available to order before adding to quote' mod='roja45quotationspro'}">{l s='Check product available' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS"
                                                    id="ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS"
                                                    id="ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ONLYAVAILABLEPRODUCTS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    {if $is_17}
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='When enabled, clicking the quote cart will display a dropdown summary of the contents.' mod='roja45quotationspro'}">{l s='Enable Quote Cart Dropdown' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-7">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY_on"
                                                        value="1"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY'] == "1")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY_off"
                                                        value="0"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY'] == "0")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLEQUOTECARTDROPDOWNSUMMARY_off">{l s='No' mod='roja45quotationspro'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>
                        <div id="roja45quotationspro_shoppingcart_tab" class="tab-pane">
                            <div class="panel" id="fieldset_01">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Shopping Cart Settings' mod='roja45quotationspro'}
                                </div>
                                <div class="form-wrapper">
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Add Request Quote option to cart summary to convert cart contents to a quotation request.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Add Request Quote option to cart summary to convert cart contents to a quotation request.' mod='roja45quotationspro'}">{l s='Request Quote From Cart' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION"
                                                    id="ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION"
                                                    id="ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_CARTSUMMARYQUOTEOPTION_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                  data-original-title="{l s='When enabled, the shopping cart request quote function will clear the contents of the quotation cart' mod='roja45quotationspro'}">{l s='Reset quotation request when converting cart' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-8">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE"
                                                       id="ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE_on" value="1"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE"
                                                       id="ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE_off" value="0"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_CONVERTTOQUOTE_CLEARQUOTE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group" class="">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Add Download PDF option to cart summary screen' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Add Download PDF option to cart summary screen' mod='roja45quotationspro'}">{l s='Cart Summary Download PDF Option' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION"
                                                    id="ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION"
                                                    id="ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_CARTSUMMARYPDFOPTION_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Allow customers to modify a cart created from a quote.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Allow customers to modify a cart created from a quote.' mod='roja45quotationspro'}">{l s='Allow Cart Modifications' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION"
                                                    id="ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION"
                                                    id="ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ALLOW_CART_MODIFICATION_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                  data-original-title="{l s='When enabled, logging in will convert the contents of the shopping cart to a quote.' mod='roja45quotationspro'}">{l s='Convert shopping cart to quote' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE"
                                                       id="ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE_on" value="1"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE"
                                                       id="ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE_off" value="0"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLECONVERTCARTTOQUOTE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>
                        <div id="roja45quotationspro_quotationorder_tab" class="tab-pane">
                            <div class="panel" id="fieldset_01">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Quotation Order Settings' mod='roja45quotationspro'}
                                </div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select a default carrier to add to new received quotation requests.' mod='roja45quotationspro'}">{l s='Default Carrier' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER" class="fixed-width-xxl">
                                                <option value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER'] == 0)}selected="selected"
                                                    {/if}>{l s='None' mod='roja45quotationspro'}</option>
                                                {foreach $carriers AS $carrier}
                                                    <option value="{$carrier.id_carrier|escape:'html':'UTF-8'}"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DEFAULT_CARRIER'] == $carrier.id_carrier)}selected="selected"
                                                        {/if}>{$carrier.name|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Include the quotation handling cost in the shipping total when provided (NB. Disable the Add Handling option in the carrier preferences).' mod='roja45quotationspro'}">{l s='Include handling costs in shipping total' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_INCLUDEHANDLING"
                                                    id="ROJA45_QUOTATIONSPRO_INCLUDEHANDLING_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_INCLUDEHANDLING'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_INCLUDEHANDLING_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_INCLUDEHANDLING"
                                                    id="ROJA45_QUOTATIONSPRO_INCLUDEHANDLING_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_INCLUDEHANDLING'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_INCLUDEHANDLING_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Allow customers to purchase a quote multiple times.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Allow customers to purchase a quote multiple times.' mod='roja45quotationspro'}">{l s='Multiple Customer Orders' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS"
                                                    id="ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS"
                                                    id="ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_MULTIPLECUSTOMERORDERS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                  title="{l s='Disable all discounts taken from cart rule.' mod='roja45quotationspro'}"
                                                  data-original-title="{l s='Disable all discounts taken from cart rule.' mod='roja45quotationspro'}">{l s='Disable all discounts' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_DISABLECARTRULES"
                                                       id="ROJA45_QUOTATIONSPRO_DISABLECARTRULES_on" value="1"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_DISABLECARTRULES'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_DISABLECARTRULES_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_DISABLECARTRULES"
                                                       id="ROJA45_QUOTATIONSPRO_DISABLECARTRULES_off" value="0"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_DISABLECARTRULES'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_DISABLECARTRULES_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>

                                    {if $is_17}
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='Restrict carriers during checkout to carrier assigned to the quotation. NB. Uses an override on the Carrier class.' mod='roja45quotationspro'}">{l s='Restrict checkout carriers' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-7">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio" name="ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER"
                                                        id="ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER_on" value="1"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER'] == "1")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                    <input type="radio" name="ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER"
                                                        id="ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER_off" value="0"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER'] == "0")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_OVERRIDE_CARRIER_off">{l s='No' mod='roja45quotationspro'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                                <div class="row-margin-top alert alert-warning" style>
                                                    {l s='This function uses an override on the Carrier class, use with caution.' mod='roja45quotationspro'}
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>
                        <div id="roja45quotationspro_quotationform_tab" class="tab-pane">
                            <div class="panel" id="fieldset_1">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Quotation Form Settings' mod='roja45quotationspro'}
                                </div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='When enabled a faster repsonse will be returned to the user when
                                                   submitting the quote.  Emails will be sent asynchronously.' mod='roja45quotationspro'}">{l
                                                s='Enable Fast
                                                  Response'
                                                mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_INSTANTRESPONSE"
                                                    id="ROJA45_QUOTATIONSPRO_INSTANTRESPONSE_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_INSTANTRESPONSE'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_INSTANTRESPONSE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_INSTANTRESPONSE"
                                                    id="ROJA45_QUOTATIONSPRO_INSTANTRESPONSE_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_INSTANTRESPONSE'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_INSTANTRESPONSE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select YES to allow customer to attach a file to upload with quotation request' mod='roja45quotationspro'}">{l s='Enable File Uploads' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_FILEUPLOAD_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select YES to allow customer to attach multiple files to upload with quotation request' mod='roja45quotationspro'}">{l s='Enable Multiple File Uploads' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_MULTIPLEFILEUPLOAD_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                  title="{l s='Enable to prevent changes to customer name and email when logged in' mod='roja45quotationspro'}">{l s='Disable Name & Email changes when logged in' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio"
                                                       name="ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES"
                                                       id="ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES_on" value="1"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio"
                                                       name="ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES"
                                                       id="ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES_off" value="0"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_DISABLE_NAMEEMAIL_CHANGES_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Select YES to enable reCAPTCHA on the form.' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select YES to enable reCAPTCHA on the form.' mod='roja45quotationspro'}">{l s='Use reCAPTCHA?' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_CAPTCHA_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select ReCaptcha Type' mod='roja45quotationspro'}">{l s='Select ReCaptcha Type' mod='roja45quotationspro'}</span>
                                        </label>

                                        <div class="col-lg-8">
                                            <select name="ROJA45_QUOTATIONSPRO_CAPTCHATYPE" class="fixed-width-xl"
                                                id="ROJA45_QUOTATIONSPRO_CAPTCHATYPE">
                                                <option value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_CAPTCHATYPE'] == "0")}selected="selected"
                                                    {/if}>{l s='Recaptcha v2 (checkbox)' mod='roja45quotationspro'}
                                                </option>
                                                <option value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_CAPTCHATYPE'] == "1")}selected="selected"
                                                    {/if}>{l s='Recaptcha v2 (invisible)' mod='roja45quotationspro'}
                                                </option>
                                                <option value="2" {if ($fields_value['ROJA45_QUOTATIONSPRO_CAPTCHATYPE'] == "2")
                                                }selected="selected" {/if}>
                                                    {l s='Recaptcha v3' mod='roja45quotationspro'}</option>
                                            </select>
                                            <div class="row-margin-top alert alert-warning" style>
                                                {l s='NB. If you use the fast response option, you must use recaptcha v3 only, v2 is not compatible.' mod='roja45quotationspro'}
                                            </div>
                                        </div>
                                    </div>
                                    <div id="fields_warning_dialog" class="recaptcha_hidden"
                                        title="{l s='Notice' mod='roja45quotationspro'}" style="display:none">
                                        <label class="control-label col-lg-4">
                                        </label>
                                        <div class="col-lg-7">
                                            <p>{l s='To use Google reCaptcha you should first register your site here: ' mod='roja45quotationspro'}<a
                                                    href="https://www.google.com/recaptcha/"
                                                    target="_blank">{l s='Google Recaptcha' mod='roja45quotationspro'}</a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group recaptcha_hidden" style="display:none;">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Enter your reCaptcha site key here.' mod='roja45quotationspro'}">{l s='reCaptcha Site Key' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE"
                                                id="ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_RECAPTCHA_SITE']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group recaptcha_hidden" style="display:none;">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Enter your secret reCaptcha key here.' mod='roja45quotationspro'}">{l s='reCaptcha Secret Key' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <input type="password" name="ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET"
                                                id="ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_RECAPTCHA_SECRET']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                  title="{l s='Select YES to enable the feature to automatically create new addresses for customers on the form.' mod='roja45quotationspro'}"
                                                  data-original-title="{l s='Select YES to enable the feature to automatically create new addresses for customers on the form.' mod='roja45quotationspro'}">{l s='Enable Automatic New Address Generation' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE"
                                                       id="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE_on" value="1"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE"
                                                       id="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE_off" value="0"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_ENABLE_AUTO_CREATE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group new_addresses_hidden" style="display:none;">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                  data-original-title="{l s='Enter your delivery address field name here.' mod='roja45quotationspro'}">{l s='Delivery Address Field Name' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY"
                                                   id="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY"
                                                   value="{$fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_DELIVERY']|escape:'html':'UTF-8'}"
                                                   class="">
                                        </div>
                                    </div>

                                    <div class="form-group new_addresses_hidden" style="display:none;">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                  data-original-title="{l s='Enter your invoice address field name here.' mod='roja45quotationspro'}">{l s='Invoice Address Field Name' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE"
                                                   id="ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE"
                                                   value="{$fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_ADDRESS_INVOICE']|escape:'html':'UTF-8'}"
                                                   class="">
                                        </div>
                                    </div>
                                    
                                </div><!-- /.form-wrapper -->
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>

                        <div id="roja45quotationspro_pdf_tab" class="tab-pane">
                            <div class="panel">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Email/PDF Settings' mod='roja45quotationspro'}
                                </div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Enable the new pdf and email editor templates (NB. This will use new templates incompatible with the previous templates, including any template overrides you have created).' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Enable the new pdf and email editor templates (NB. This will use new templates incompatible with the previous templates, including any template overrides you have created).' mod='roja45quotationspro'}">{l s='Enable Email/PDF editor' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USE_PS_PDF"
                                                    id="ROJA45_QUOTATIONSPRO_USE_PS_PDF_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USE_PS_PDF'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USE_PS_PDF_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USE_PS_PDF"
                                                    id="ROJA45_QUOTATIONSPRO_USE_PS_PDF_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USE_PS_PDF'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USE_PS_PDF_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select the Cart Quotation Request PDF template' mod='roja45quotationspro'}">{l s='Cart Quotation Request PDF Template' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF"
                                                class="fixed-width-xxl">
                                                {foreach $pdf_templates AS $pdf_template}
                                                    <option
                                                        value="{$pdf_template.id_roja45_quotation_answer|escape:'html':'UTF-8'}"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_REQUEST_PDF'] == $pdf_template.id_roja45_quotation_answer)}selected="selected"
                                                        {/if}>{$pdf_template.name|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select the Customer Quotation PDF template' mod='roja45quotationspro'}">{l s='Customer Quotation PDF Template' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_QUOTATION_PDF" class="fixed-width-xxl">
                                                {foreach $pdf_templates AS $pdf_template}
                                                    <option
                                                        value="{$pdf_template.id_roja45_quotation_answer|escape:'html':'UTF-8'}"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_QUOTATION_PDF'] == $pdf_template.id_roja45_quotation_answer)}selected="selected"
                                                        {/if}>{$pdf_template.name|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                </div><!-- /.form-wrapper -->
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>
                        <div id="roja45quotationspro_security_tab" class="tab-pane">
                            <div class="panel" id="fieldset_3">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Security Settings' mod='roja45quotationspro'}
                                </div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title=""
                                                data-original-title="{l s='Enable options to assign/reassign quotations to employees.' mod='roja45quotationspro'}">{l s='Assign Quotations' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <div class="col-lg-7">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS_on"
                                                        value="1"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS'] == "1")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS_off"
                                                        value="0"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS'] == "0")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLE_QUOTATION_ASSIGNS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title=""
                                                data-original-title="{l s='New quotations are assigned by default owner, only default owner can see new quotations (If No, new quotations are available to all)' mod='roja45quotationspro'}">{l s='Assign New Quotations' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <div class="col-lg-7">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS"
                                                        id="ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS_on" value="1"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS'] == "1")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS"
                                                        id="ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS_off"
                                                        value="0"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS'] == "0")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ASSIGN_NEW_QUOTATIONS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title=""
                                                data-original-title="{l s='Assign new quotations to this profile by default.' mod='roja45quotationspro'}">{l s='Default Owner Group' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_DEFAULT_OWNER"
                                                class="fixed-width-xxl">
                                                {foreach $profiles AS $profile}
                                                    <option value="{$profile.id_profile|escape:'html':'UTF-8'}"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DEFAULT_OWNER'] == $profile.id_profile)}selected="selected"
                                                        {/if}>{$profile.name|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title=""
                                                data-original-title="{l s='When sending, assign quotations to this employee if not owned by anyone (defaults to first admin account).' mod='roja45quotationspro'}">{l s='Assign unassigned quotation to employee' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE"
                                                class="fixed-width-xxl">
                                                <option value="0">{l s='Default' mod='roja45quotationspro'}</option>
                                                {foreach $employees AS $employee}
                                                    <option value="{$employee.id_employee|escape:'html':'UTF-8'}"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_DEFAULT_EMPLOYEE'] == $employee.id_employee)}selected="selected"
                                                        {/if}>{$employee.firstname|escape:'html':'UTF-8'} {$employee.lastname|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title=""
                                                data-original-title="{l s='Employees can only view and edit assigned quotations.' mod='roja45quotationspro'}">{l s='Employees Only Edit Assigned' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <div class="col-lg-7">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE_on" value="1"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE'] == "1")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE_off" value="0"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE'] == "0")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_HIDE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title=""
                                                data-original-title="{l s='Allow employees to reassign quotations (Can release only if no).' mod='roja45quotationspro'}">{l s='Employees can reassign quotations' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <div class="col-lg-7">
                                                <span class="switch prestashop-switch fixed-width-lg">
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN_on"
                                                        value="1"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN'] == "1")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                    <input type="radio"
                                                        name="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN"
                                                        id="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN_off"
                                                        value="0"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN'] == "0")}checked="checked"
                                                        {/if}>
                                                    <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLE_EMPLOYEE_REASSIGN_off">{l s='No' mod='roja45quotationspro'}</label>
                                                    <a class="slide-button btn"></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>
                        <div id="roja45quotationspro_advanced_tab" class="tab-pane">
                            <div class="panel" id="fieldset_3">
                                <div class="panel-heading">
                                    <i class="icon-cogs"></i>{l s='Advanced Settings' mod='roja45quotationspro'}
                                </div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Enable customized quotation reference numbers' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Enable customized quotation reference numbers' mod='roja45quotationspro'}">{l s='Custom Quotation References?' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_CUSTOMREFERENCE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="customreference_hidden" style="display:none;">
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                    title=""
                                                    data-original-title="{l s='Quotation reference format' mod='roja45quotationspro'}">{l s='Quotation reference format' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-7">
                                                <input type="text" name="ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT"
                                                    id="ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT"
                                                    value="{$fields_value['ROJA45_QUOTATIONSPRO_REFERENCE_FORMAT']|escape:'html':'UTF-8'}"
                                                    class="">
                                                <div class="row-margin-top alert alert-info" style="">
                                                    <p>{l s='The reference has a maximum length of 32 characters' mod='roja45quotationspro'}
                                                    </p>
                                                    <p>{l s='The following tokens are available' mod='roja45quotationspro'}
                                                    </p>
                                                    <p>[sequential n]
                                                        {l s='The module will find the last quotation reference number used, and increment the value.  The string will be padded to the value of n.' mod='roja45quotationspro'}
                                                    </p>
                                                    <p>[random n]
                                                        {l s='The module will generate a random string to the length of n.' mod='roja45quotationspro'}
                                                    </p>
                                                    <p>[day]
                                                        {l s='The module will insert the two digit day date.' mod='roja45quotationspro'}
                                                    </p>
                                                    <p>[month]
                                                        {l s='The module will insert the two digit month.' mod='roja45quotationspro'}
                                                    </p>
                                                    <p>[year]
                                                        {l s='The module will insert the two digit year.' mod='roja45quotationspro'}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Product Combination Ordering' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Select the ordering method for product combinations when adding to a quotation in the back office' mod='roja45quotationspro'}">{l s='Product Combination Ordering' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER" class="fixed-width-xl">
                                                <option value="sortAttributeById"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER'] == "sortAttributeById")}selected="selected"{/if}>{l s='Attribute ID' mod='roja45quotationspro'}</option>
                                                <option value="sortAttributeByGroupName"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER'] == "sortAttributeByGroupName")}selected="selected"{/if}>{l s='Combination Group Name' mod='roja45quotationspro'}</option>
                                                <option value="sortAttributeByAttrName"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_PRODUCT_COMBINATION_ORDER'] == "sortAttributeByAttrName")}selected="selected"{/if}>{l s='Combination Attribute Name' mod='roja45quotationspro'}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select YES to exclude the customer group discount when adding the product to the quotation if the product already has a separate discount.' mod='roja45quotationspro'}">{l s='Disable Accumulate Discount Group' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP"
                                                    id="ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP"
                                                    id="ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_DISABLE_ACCUMULATEDISCOUNTGROUP_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Select YES to use ajax in quotation cart.' mod='roja45quotationspro'}">{l s='Enable Ajax?' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USEAJAX"
                                                    id="ROJA45_QUOTATIONSPRO_USEAJAX_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USEAJAX'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USEAJAX_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USEAJAX"
                                                    id="ROJA45_QUOTATIONSPRO_USEAJAX_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USEAJAX'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USEAJAX_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='When enabled javascript will be used to try to correctly place page elements.' mod='roja45quotationspro'}">{l s='Enable Javascript Page Changes' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USEJS"
                                                    id="ROJA45_QUOTATIONSPRO_USEJS_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USEJS'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USEJS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USEJS"
                                                    id="ROJA45_QUOTATIONSPRO_USEJS_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USEJS'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USEJS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Highlight quotations in the quotation list when expiring' mod='roja45quotationspro'}">{l s='Highlight Expring Quotations' mod='roja45quotationspro'}</span>
                                        </label>

                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES"
                                                    id="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES"
                                                    id="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_QUOTES_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Days before expiry to show warning' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Days before expiry to show warning' mod='roja45quotationspro'}">{l s='Quote Expiry Warning (days)' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING"
                                                class="fixed-width-xl">
                                                <option value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "1")}selected="selected"
                                                    {/if}>1</option>
                                                <option value="2"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "2")}selected="selected"
                                                    {/if}>2</option>
                                                <option value="3"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "3")}selected="selected"
                                                    {/if}>3</option>
                                                <option value="4"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "4")}selected="selected"
                                                    {/if}>4</option>
                                                <option value="5"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "5")}selected="selected"
                                                    {/if}>5</option>
                                                <option value="6"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "6")}selected="selected"
                                                    {/if}>6</option>
                                                <option value="7"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "7")}selected="selected"
                                                    {/if}>7</option>
                                                <option value="8"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "8")}selected="selected"
                                                    {/if}>8</option>
                                                <option value="9"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "9")}selected="selected"
                                                    {/if}>9</option>
                                                <option value="10"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "10")}selected="selected"
                                                    {/if}>10</option>
                                                <option value="11"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "11")}selected="selected"
                                                    {/if}>11</option>
                                                <option value="12"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "12")}selected="selected"
                                                    {/if}>12</option>
                                                <option value="13"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "13")}selected="selected"
                                                    {/if}>13</option>
                                                <option value="14"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "14")}selected="selected"
                                                    {/if}>14</option>
                                                <option value="15"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "15")}selected="selected"
                                                    {/if}>15</option>
                                                <option value="16"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "16")}selected="selected"
                                                    {/if}>16</option>
                                                <option value="17"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "17")}selected="selected"
                                                    {/if}>17</option>
                                                <option value="18"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "18")}selected="selected"
                                                    {/if}>18</option>
                                                <option value="19"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "19")}selected="selected"
                                                    {/if}>19</option>
                                                <option value="20"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "20")}selected="selected"
                                                    {/if}>20</option>
                                                <option value="21"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "21")}selected="selected"
                                                    {/if}>21</option>
                                                <option value="22"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "22")}selected="selected"
                                                    {/if}>22</option>
                                                <option value="23"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "23")}selected="selected"
                                                    {/if}>23</option>
                                                <option value="24"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "24")}selected="selected"
                                                    {/if}>24</option>
                                                <option value="25"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "25")}selected="selected"
                                                    {/if}>25</option>
                                                <option value="26"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "26")}selected="selected"
                                                    {/if}>26</option>
                                                <option value="27"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "27")}selected="selected"
                                                    {/if}>27</option>
                                                <option value="28"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "28")}selected="selected"
                                                    {/if}>28</option>
                                                <option value="29"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "29")}selected="selected"
                                                    {/if}>29</option>
                                                <option value="30"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_WARNING'] == "30")}selected="selected"
                                                    {/if}>30</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                title="{l s='Days before expiry to show alert' mod='roja45quotationspro'}"
                                                data-original-title="{l s='Days before expiry to show alert' mod='roja45quotationspro'}">{l s='Quote Expiry Alert (days)' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7 ">
                                            <select name="ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT"
                                                class="fixed-width-xl">
                                                <option value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "1")}selected="selected"
                                                    {/if}>1</option>
                                                <option value="2"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "2")}selected="selected"
                                                    {/if}>2</option>
                                                <option value="3"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "3")}selected="selected"
                                                    {/if}>3</option>
                                                <option value="4"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "4")}selected="selected"
                                                    {/if}>4</option>
                                                <option value="5"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "5")}selected="selected"
                                                    {/if}>5</option>
                                                <option value="6"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "6")}selected="selected"
                                                    {/if}>6</option>
                                                <option value="7"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "7")}selected="selected"
                                                    {/if}>7</option>
                                                <option value="8"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "8")}selected="selected"
                                                    {/if}>8</option>
                                                <option value="9"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "9")}selected="selected"
                                                    {/if}>9</option>
                                                <option value="10"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "10")}selected="selected"
                                                    {/if}>10</option>
                                                <option value="11"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "11")}selected="selected"
                                                    {/if}>11</option>
                                                <option value="12"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "12")}selected="selected"
                                                    {/if}>12</option>
                                                <option value="13"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "13")}selected="selected"
                                                    {/if}>13</option>
                                                <option value="14"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "14")}selected="selected"
                                                    {/if}>14</option>
                                                <option value="15"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "15")}selected="selected"
                                                    {/if}>15</option>
                                                <option value="16"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "16")}selected="selected"
                                                    {/if}>16</option>
                                                <option value="17"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "17")}selected="selected"
                                                    {/if}>17</option>
                                                <option value="18"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "18")}selected="selected"
                                                    {/if}>18</option>
                                                <option value="19"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "19")}selected="selected"
                                                    {/if}>19</option>
                                                <option value="20"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "20")}selected="selected"
                                                    {/if}>20</option>
                                                <option value="21"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "21")}selected="selected"
                                                    {/if}>21</option>
                                                <option value="22"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "22")}selected="selected"
                                                    {/if}>22</option>
                                                <option value="23"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "23")}selected="selected"
                                                    {/if}>23</option>
                                                <option value="24"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "24")}selected="selected"
                                                    {/if}>24</option>
                                                <option value="25"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "25")}selected="selected"
                                                    {/if}>25</option>
                                                <option value="26"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "26")}selected="selected"
                                                    {/if}>26</option>
                                                <option value="27"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "27")}selected="selected"
                                                    {/if}>27</option>
                                                <option value="28"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "28")}selected="selected"
                                                    {/if}>28</option>
                                                <option value="29"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "29")}selected="selected"
                                                    {/if}>29</option>
                                                <option value="30"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_HIGHTLIGHT_EXPIRING_ALERT'] == "30")}selected="selected"
                                                    {/if}>30</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Replace zero prices with text' mod='roja45quotationspro'}">{l s='Replace zero prices with text' mod='roja45quotationspro'}</span>
                                        </label>

                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE"
                                                    id="ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE"
                                                    id="ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_REPLACE_ZERO_PRICE_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Show emails and PDF using quotation tax setting (Default: Use customer group setting to ensure front end prices match quotation)' mod='roja45quotationspro'}">{l s='Send emails and PDF with quotation tax setting' mod='roja45quotationspro'}</span>
                                        </label>

                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING"
                                                    id="ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING_on"
                                                    value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING"
                                                    id="ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING_off"
                                                    value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SEND_QUOTATION_FORCE_TAX_SETTING_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Allow 0 stock items on a quotation to be purchased, when Prestashop is configured to disallow out of stock orders. NB. Uses an override on the Product class.' mod='roja45quotationspro'}">{l s='0 Stock Orders' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT"
                                                    id="ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT"
                                                    id="ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_OVERRIDE_PRODUCT_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                            <div class="row-margin-top alert alert-warning" style>
                                                {l s='This function uses an override on the Product class, use with caution.' mod='roja45quotationspro'}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Use alternative method to track emails, and removing the tracking code in the email subject line. NB. Uses an override on the AdminCustomerThreadsController class.' mod='roja45quotationspro'}">{l s='Use Email Header Tracking' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER"
                                                    id="ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio"
                                                    name="ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER"
                                                    id="ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_OVERRIDE_THREADCONTROLLER_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                            <div class="row-margin-top alert alert-warning" style>
                                                {l s='This function uses an override on the AdminCustomerThreadsController class, use with caution.' mod='roja45quotationspro'}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product page selector used to hide the add to cart button (only change for thrid party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product Page Add To Cart Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTADDTOCARTSELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product page selector used to hide the price (only change for third party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product Page Price Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTPRICESELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product page selector used to select the quantity field (only change for third party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product Page Quantity Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTQTYSELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product list item selector (only change for third party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product List Item Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTLISTITEMSELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product list selector used to locate the element that contains buttons in the product list (only change for third party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product List Button Block Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTLISTBUTTONSELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product list selector used to select add to cart button (only change for third party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product List Add To Cart Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTLISTADDTOCARTSELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product list selector used to hide the price (only change for third party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product List Price Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTLISTPRICESELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='The product list selector used to insert the Get Quote flag (only change for third party themes if the default value does not work' mod='roja45quotationspro'}">{l s='Product List Flag Selector' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-4">
                                            <input type="text" name="ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR"
                                                id="ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR"
                                                value="{$fields_value['ROJA45_QUOTATIONSPRO_PRODUCTLISTFLAGSELECTOR']|escape:'html':'UTF-8'}"
                                                class="">
                                        </div>
                                    </div>
                                    {if $is_17}
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='Quote cart nav selector (for themes that uses JS to relocate the nav bar elements - PS1.7)' mod='roja45quotationspro'}">{l s='Quote cart navigation selector' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-4">
                                                <input type="text"
                                                    name="ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR"
                                                    id="ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR"
                                                    value="{$fields_value['ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTNAVSELECTOR']|escape:'html':'UTF-8'}"
                                                    class="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                    data-original-title="{l s='Quote cart position selector when moved for small screens (for themes that uses JS to relocate the nav bar elements - PS1.7)' mod='roja45quotationspro'}">{l s='Quote cart position selector' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-4">
                                                <input type="text" name="ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR"
                                                    id="ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR"
                                                    value="{$fields_value['ROJA45_QUOTATIONSPRO_RESPONSIVEQUOTECARTSELECTOR']|escape:'html':'UTF-8'}"
                                                    class="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-lg-4">
                                                <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                    title="{l s='Select whether horizontal or vertical touchspin layout.' mod='roja45quotationspro'}"
                                                    data-original-title="{l s='Select whether horizontal or vertical touchspin layout.' mod='roja45quotationspro'}">{l s='Touchspin Layout' mod='roja45quotationspro'}</span>
                                            </label>
                                            <div class="col-lg-7 ">
                                                <select name="ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT" class="fixed-width-xl">
                                                    <option value="0"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'] == "0")}selected="selected"
                                                        {/if}>{l s='Horizontal' mod='roja45quotationspro'}</option>
                                                    <option value="1"
                                                        {if ($fields_value['ROJA45_QUOTATIONSPRO_TOUCHSPINLAYOUT'] == "1")}selected="selected"
                                                        {/if}>{l s='Vertical' mod='roja45quotationspro'}</option>
                                                </select>
                                            </div>
                                        </div>
                                    {/if}
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Show all available carriers in quotation editor (override restrict by country/state zone)' mod='roja45quotationspro'}">{l s='Show carriers for all zones.' mod='roja45quotationspro'}</span>
                                        </label>

                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS"
                                                    id="ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS"
                                                    id="ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_SHOW_ALL_ZONE_CARRIERS_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Change the temporary directory from the standard PHP temp directory to the Prestashop cache directory.' mod='roja45quotationspro'}">{l s='Use PS Cache Directory for PDF Creation' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR"
                                                    id="ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR"
                                                    id="ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_USE_PS_CACHE_TMPDIR_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                                                data-original-title="{l s='Display debug informaiton when rendering the PDF' mod='roja45quotationspro'}">{l s='Debug PDF Render' mod='roja45quotationspro'}</span>
                                        </label>

                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG_on" value="1"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG"
                                                    id="ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG_off" value="0"
                                                    {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                    for="ROJA45_QUOTATIONSPRO_ENABLE_PDF_DEBUG_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-lg-4">
                                            <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                                                  title="{l s='Enable logging' mod='roja45quotationspro'}"
                                                  data-original-title="{l s='Enable logging' mod='roja45quotationspro'}">{l s='Enable Logging' mod='roja45quotationspro'}</span>
                                        </label>
                                        <div class="col-lg-7">
                                            <span class="switch prestashop-switch fixed-width-lg">
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLELOG"
                                                       id="ROJA45_QUOTATIONSPRO_ENABLELOG_on" value="1"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLELOG'] == "1")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLELOG_on">{l s='Yes' mod='roja45quotationspro'}</label>
                                                <input type="radio" name="ROJA45_QUOTATIONSPRO_ENABLELOG"
                                                       id="ROJA45_QUOTATIONSPRO_ENABLELOG_off" value="0"
                                                       {if ($fields_value['ROJA45_QUOTATIONSPRO_ENABLELOG'] == "0")}checked="checked"
                                                    {/if}>
                                                <label
                                                        for="ROJA45_QUOTATIONSPRO_ENABLELOG_off">{l s='No' mod='roja45quotationspro'}</label>
                                                <a class="slide-button btn"></a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button type="submit"
                                        class="roja45quotations_submitConfiguration btn btn-default btn btn-default pull-right"><i
                                            class="process-icon-save"></i>{l s='Save Settings' mod='roja45quotationspro'}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <h5 class="h5">{l s='Enable Products' mod='roja45quotationspro'}</h5>
        </div>
        <div class="panel-body">
            <p>{l s='Enable your first products for quotation here:' mod='roja45quotationspro'} <a
                    href="{$roja45quotationspro_enable}"
                    target="_blank">{l s='Enable Products' mod='roja45quotationspro'}</a></p>
        </div>
    </div>
    {if $roja45_auth_key}
        <iframe
            src="{$roja45_license_controller|escape:'html':'UTF-8'}validate?nogutter&api_key={$roja45_api_key|escape:'url':'UTF-8'}&auth_key={$roja45_auth_key|escape:'url':'UTF-8'}&customer_email={$roja45_auth_email|escape:'url':'UTF-8'}&shop_url={$shop_url|escape:'url':'UTF-8'}&purchased_from={$module_source|escape:'url':'UTF-8'}&module_name={$module_name|escape:'url':'UTF-8'}&domain={$roja45_domain|escape:'url':'UTF-8'}"
            style="width: 100%; height: 125px; border: none"></iframe>
    {/if}
</div>
<div id="roja45_quotation_modal">
    <div id="roja45_quotation_modal_dialog" class="roja45-quotation-modal-dialog">
        <div id="modal_wait_icon">
            <i class="icon-refresh icon-spin animated"></i>
            <p>{l s='Please Wait' mod='roja45quotationspro'}</p>
        </div>
    </div>
</div>