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

{capture name=path}{l s='Your Quotation Request' mod='roja45quotationspro'}{/capture}

<div class="block">
    <div class="quotationspro_request_dialog_overlay"></div>
    <form action="{$roja45_quoationspro_controller|escape:'htmlall':'UTF-8'}" method="post" id="quotationspro_request_form" enctype="multipart/form-data">
        <input type="hidden" name="action" value="submitRequest"/>
        <input type="hidden" name="ROJA45QUOTATIONSPRO_FORMDATA"/>
        {if isset($numberProducts) && $numberProducts > 0}
        <h4 id="quote_title" class="title_block quote_title">{l s='Request summary' mod='roja45quotationspro'}
            <span id="summary_products_quantity">({$numberProducts|escape:'html':'UTF-8'} {if $numberProducts == 1}{l s='product' mod='roja45quotationspro'}{else}{l s='products' mod='roja45quotationspro'}{/if})
        </h4>
        {elseif ($roja45quotationspro_noproductrequest)}
            <h4 id="quote_title" class="title_block quote_title">{l s='Quotation Request' mod='roja45quotationspro'}</h4>
        {/if}
        {assign var='current_step' value='summary'}
        {include file="$tpl_dir./errors.tpl"}

        {if !$empty}
            {if isset($lastProductAdded) AND $lastProductAdded}
                <div class="quote_last_product">
                    <div class="quote_last_product_header">
                        <div class="left">{l s='Last product added' mod='roja45quotationspro'}</div>
                    </div>
                    <a class="quote_last_product_img"
                       href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, $lastProductAdded.id_shop)|escape:'html':'UTF-8'}">
                        <img src="{$link->getImageLink($lastProductAdded.link_rewrite, $lastProductAdded.id_image, 'small_default')|escape:'html':'UTF-8'}"
                             alt="{$lastProductAdded.name|escape:'html':'UTF-8'}"/>
                    </a>
                    <div class="quote_last_product_content">
                        <p class="product-name">
                            <a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'html':'UTF-8'}">
                                {$lastProductAdded.name|escape:'html':'UTF-8'}
                            </a>
                        </p>
                        {if isset($lastProductAdded.attributes) && $lastProductAdded.attributes}
                            <small>
                                <a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'html':'UTF-8'}">
                                    {$lastProductAdded.attributes|escape:'html':'UTF-8'}
                                </a>
                            </small>
                        {/if}
                    </div>
                </div>
            {/if}
            {if isset($errors) && $errors}

            {else}
            <div id="request-summary-content" class="table_block table-responsive">
                <table id="quote_summary"
                       class="table table-bordered {if $PS_STOCK_MANAGEMENT}stock-management-on{else}stock-management-off{/if}">
                    <thead>
                    <tr>
                        <th class="quote_product first_item">{l s='Product' mod='roja45quotationspro'}</th>
                        <th class="quote_description item">{l s='Description' mod='roja45quotationspro'}</th>
                        {if $quotation_has_customizations}
                            <th class="quote-product-price" role="columnheader">{l s='Customizations' mod='roja45quotationspro'}</th>
                        {/if}
                        {if $PS_STOCK_MANAGEMENT}
                            {assign var='col_span_subtotal' value='3'}
                            <th class="quote_avail item text-center">{l s='Availability' mod='roja45quotationspro'}</th>
                        {else}
                            {assign var='col_span_subtotal' value='2'}
                        {/if}
                        <th class="quote_quantity item text-center">{l s='Qty' mod='roja45quotationspro'}</th>
                        <th class="quote_delete last_item">&nbsp;</th>
                    </tr>
                    </thead>

                    <tbody>
                    {assign var='odd' value=0}
                    {assign var='have_non_virtual_products' value=false}
                    {foreach $quotation_products as $product}
                        {if $product.is_virtual == 0}
                            {assign var='have_non_virtual_products' value=true}
                        {/if}
                        {assign var='productId' value=$product.id_product|escape:'html':'UTF-8'}
                        {assign var='productAttributeId' value=$product.id_product_attribute|escape:'html':'UTF-8'}
                        {assign var='odd' value=($odd+1)%2}
                        {* Display the product line *}
                        {include file="./request-summary-product-line.tpl" productLast=$product@last productFirst=$product@first}
                    {/foreach}
                    {assign var='last_was_odd' value=$product@iteration%2}

                    </tbody>
                </table>
            </div>
            {/if}
        {else}
            {if !$roja45quotationspro_noproductrequest}
                <p class="alert alert-warning">{l s='Your request is empty.' mod='roja45quotationspro'}</p>
            {/if}
        {/if}
        <h3 class="page-heading">{l s='Please provide the following  information' mod='roja45quotationspro'}</h3>

        <div class="quotationspro_request_container col-lg-12">
            <div class="row">
        {for $col_counter=1 to $columns}
            <div id="quotationspro_request_column_{$col_counter|escape:'html':'UTF-8'}" data-column="{$col_counter|escape:'html':'UTF-8'}" class="quotationspro_request column col-xs-12 col-md-{$col_width|escape:'html':'UTF-8'}">
                <div class="quotationspro_request_column_container col-xs-12">
                    {if (isset( $form.$col_counter.settings.column_heading ))}
                        <h3 class="page-subheading">{$form.$col_counter.settings.column_heading|escape:'html':'UTF-8'}</h3>
                    {/if}
                    {foreach $form.$col_counter.fields as $name => $fields}
                    <a id="COLLAPSE_{$name}" class="collapse-link" style="display:none;" data-toggle="collapse" data-target="#COMPONENT_{$name}"><span class="open-text">{l s='Add New Address' mod='roja45quotationspro'}</span><span class="close-text">{l s='Close' mod='roja45quotationspro'}</span></a>
                    <div id="COMPONENT_{$name}" class="quotationspro_request_field_container collapse in" aria-expanded="true">

                        {foreach $fields as $pos => $field}
                            {if $field.enabled}
                            {if $field.collapse}<div id="LINK_{$name}" class="quotationspro_request_field_collapse" data-collapse-target="#COLLAPSE_{$field.collapse}"></div>{/if}

                                {if $field.type=='TEXT'}
                                    {include
                                    file='./fo_text_field.tpl'
                                    id=$field.id
                                    name=$field.name
                                    field_label=$field.label
                                    field_type=$field.field_type
                                    required=$field.required
                                    class=$field.class
                                    validationMethod=$field.validation
                                    customregex=$field.custom_regex
                                    size=$field.size
                                    placeholder=$field.description
                                    suffix=$field.suffix
                                    prefix=$field.prefix
                                    maxlength=$field.maxlength
                                    }
                                {elseif $field.type=='TEXTAREA'}
                                    {include
                                    file='./fo_textarea_field.tpl'
                                    id=$field.id
                                    name=$field.name
                                    field_label=$field.label
                                    field_type=$field.field_type
                                    required=$field.required
                                    class=$field.class
                                    placeholder=$field.description
                                    field_description=$field.description
                                    rows=$field.rows
                                    }
                                {elseif $field.type=='CHECKBOX'}
                                    {include
                                    file='./fo_checkbox_field.tpl'
                                    id=$field.id
                                    name=$field.name
                                    default='0'
                                    field_label=$field.label
                                    field_type=$field.field_type
                                    field_description=$field.description
                                    required=$field.required
                                    class=$field.class
                                    }
                                {elseif $field.type=='SELECT'}
                                    {include
                                    file='./fo_select_field.tpl'
                                    id=$field.id
                                    name=$field.name
                                    default='0'
                                    field_label=$field.label
                                    field_type=$field.field_type
                                    field_description=$field.description
                                    required=$field.required
                                    class=$field.class
                                    options=$field.options
                                    key_options=$field.key_options
                                    value_options=$field.value_options
                                    }
                                {elseif $field.type=='SWITCH'}
                                    {include
                                    file='./fo_switch_field.tpl'
                                    id=$field.id
                                    name=$field.name
                                    default='0'
                                    field_label=$field.label
                                    field_type=$field.field_type
                                    field_description=$field.description
                                    required=$field.required
                                    class=$field.class
                                    }
                                {elseif $field.type=='DATE'}
                                    {include
                                    file='./fo_date_field.tpl'
                                    id=$field.id
                                    name=$field.name
                                    default='0'
                                    field_label=$field.label
                                    field_type=$field.field_type
                                    field_description=$field.description
                                    required=$field.required
                                    format=$field.format
                                    class=$field.class
                                    validationMethod='isDate'
                                    }
                                {elseif $field.type=='HEADER'}
                                    {include
                                    file='./fo_header_field.tpl'
                                    id=$field.id
                                    name=$field.name
                                    field_label=$field.label
                                    field_type=$field.field_type
                                    class=$field.class
                                    }
                                {/if}
                            {/if}
                            {if ($is_show_checkbox_reuse_address && $name == $field_address_delivery && $field.name == $last_item_can_append_checkbox_address)}
                                {include
                                file="module:roja45quotationspro/views/templates/front/fo_checkbox_field.tpl"
                                id="roja45quotationspro_use_this_address_for_invoice"
                                name="ROJA45QUOTATIONSPRO_USE_THIS_ADDRESS_FOR_INVOICE"
                                default='1'
                                field_label=""
                                field_type=CHECKBOX
                                field_description={l s='Use this address for invoice too' mod='roja45quotationspro'}
                                required=0
                            }
                            {/if}
                        {/foreach}
                    </div>
                    {/foreach}
                </div>
            </div>
        {/for}
            </div>
        {if $roja45quotationspro_enable_fileupload}
            <div id="quotationspro_request_column_file" class="quotationspro_request column col-xs-12 col-md-12">
            <div class="quotationspro_request_column_container col-xs-12">
                <div class="form-group _group">
                    <label class="control-label">
                        {l s='Attach File' mod='roja45quotationspro'}
                    </label>
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="hidden" name="UploadReceipt" value="1"/>
                            {if $roja45quotationspro_enable_multiplefileupload}
                                <input name="uploadedfile[]" type="file" value="" multiple accept=".pdf,.jpg,.jpeg,.png,.gix,.txt,.zip"/>
                            {else}
                                <input name="uploadedfile" type="file" value="" accept=".pdf,.jpg,.jpeg,.png,.gix,.txt,.zip"/>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            </div>
        {/if}
        {if $roja45quotationspro_enable_captcha}
            {if $roja45quotationspro_enable_captchatype == 1}
                <div class="g-recaptcha"
                     data-sitekey="{$roja45quotationspro_recaptcha_site_key}"
                     data-callback="onRecaptchaInvisibleSubmitCallback"
                     data-error-callback="onRecaptchaInvisibleSubmitCallbackError"
                     data-expired-callback="onRecaptchaInvisibleSubmitCallbackError"
                     data-size="invisible">
                </div>
            {elseif $roja45quotationspro_enable_captchatype == 0}
                <div class="clearfix">
                    <div class="quotationspro_request captcha col-lg-12">
                        <div class="captcha-block">
                            <div id="sendQuotationsPro" class="g-recaptcha"
                                 data-sitekey="{$roja45quotationspro_recaptcha_site_key}">
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        {/if}
        </div>
        <div class="quotationspro_request notes col-xs-12">
            <div class="row button-container">
                <div class="col-xs-6">
                    <div class="row">
                        <span class="required-field-indicator"><sup>&#42</sup>{l s='Required field' mod='roja45quotationspro'}</span>
                    </div>
                </div>
                <div class="customer-copy-checkbox col-xs-6">
                    <div class="row">
                        <label class="field-label pull-right" for="ROJA45QUOTATIONSPRO_CUSTOMER_COPY"><input type="checkbox" class="form-control" id="ROJA45QUOTATIONSPRO_CUSTOMER_COPY" name="ROJA45QUOTATIONSPRO_CUSTOMER_COPY"/> {l s='Send copy to yourself' mod='roja45quotationspro'}</label>
                    </div>
                </div>
            </div>
        </div>
        <p class="quote_navigation clearfix">
            {if $roja45quotationspro_enable_captcha}
                {if $roja45quotationspro_enable_captchatype == 0}
                    <button type="submit"
                            id="submitRequest"
                            class="button btn btn-default request-quotation button-medium disabled"
                            title="{l s='Request Quote' mod='roja45quotationspro'}">
                            <span>{l s='Request Quote' mod='roja45quotationspro'}
                                {if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-right right"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-right"></i>{else}<i class="icon-chevron-right right"></i>{/if}
                        </span>
                    </button>
                {elseif ($roja45quotationspro_enable_captchatype == 1)}
                    <button class="button btn btn-default request-quotation button-medium"
                            id="submitRequest"
                            data-sitekey="{$roja45quotationspro_recaptcha_site_key|escape:'html':'UTF-8'}"
                            data-callback='onRecaptchaInvisibleSubmitCallback'
                            data-error-callback="onRecaptchaInvisibleSubmitCallbackError"
                            data-expired-callback="onRecaptchaInvisibleSubmitCallbackError">
                    <span>{l s='Request Quote' mod='roja45quotationspro'}
                        {if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-right right"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-right"></i>{else}<i class="icon-chevron-right right"></i>{/if}
                    </span>
                    </button>
                {elseif ($roja45quotationspro_enable_captchatype == 2)}
                    <button type="submit"
                            id="submitRequest"
                            class="button btn btn-default request-quotation button-medium disabled"
                            title="{l s='Request Quote' mod='roja45quotationspro'}">
                        <span>{l s='Request Quote' mod='roja45quotationspro'}
                            {if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-right right"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-right"></i>{else}<i class="icon-chevron-right right"></i>{/if}
                    </span>
                    </button>
                {/if}
            {else}
                <button type="submit"
                        id="submitRequest"
                        class="button btn btn-default request-quotation button-medium"
                        title="{l s='Request Quote' mod='roja45quotationspro'}">
                            <span>{l s='Request Quote' mod='roja45quotationspro'}
                                {if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-right right"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-right"></i>{else}<i class="icon-chevron-right right"></i>{/if}
                        </span>
                </button>
            {/if}
            <a href="{$home_url}"
               class="button-exclusive btn btn-default"
               title="{l s='Continue shopping' mod='roja45quotationspro'}">
                {if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-left"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-left"></i>{else}<i class="icon-chevron-left"></i>{/if}{l s='Continue shopping' mod='roja45quotationspro'}
            </a>
        </p>
        <div class="clear"></div>
        {strip}
            {addJsDefL name=txtProduct}{l s='product' mod='roja45quotationspro' js=1}{/addJsDefL}
            {addJsDefL name=txtProducts}{l s='products' mod='roja45quotationspro' js=1}{/addJsDefL}
        {/strip}
    </form>
    <div id="quotationspro_request_container" style="display:none;">
        <h4 class="title_block received_title">{l s='Request Received' mod='roja45quotationspro'}</h4>
        <div class="box">
            <p>{l s='Many thanks, we have received your request.  We will contact you with your quotation as soon as possible.' mod='roja45quotationspro'}</p>
            {if $isLogged}
                <p>{l s='You can check the status of your quote from your account area: ' mod='roja45quotationspro'}<a
                            href="{$account_link}">{l s='My Account' mod='roja45quotationspro'}</a></p>
            {/if}
        </div>
        <p class="quote_navigation clearfix">
            <a href="{$home_url}" title="{l s='Home' mod='roja45quotationspro'}" class="pull-right button-exclusive btn btn-default">
                {l s='Home' mod='roja45quotationspro'}
                {if $roja45quotationspro_iconpack=='1'}<i class="icon-chevron-right right"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-chevron-right"></i>{else}<i class="icon-chevron-right right"></i>{/if}
            </a>
        </p>
    </div>
</div>

{if ($roja45quotationspro_enable_captchatype == 0)}
<script type="text/javascript">
    {if (($roja45quotationspro_enable_captcha==1) && (null !== $roja45quotationspro_recaptcha_site_key) )}
    var roja45quotationspro_recaptcha_site_key = "{$roja45quotationspro_recaptcha_site_key|escape:'html':'UTF-8'}";
    if (typeof roja45_recaptcha_widgets == 'undefined') {
        roja45_recaptcha_widgets = [];
    }
    roja45_recaptcha_widgets.push('sendQuotationsPro');
    var roja45quotationspro_enable_captcha = true;
    {else}
    var roja45quotationspro_enable_captcha = false;
    {/if}
</script>
{/if}

{addJsDefL name='roja_fileDefaultHtml'}{l s='No file selected' mod='roja45quotationspro' js=1}{/addJsDefL}
{addJsDefL name='roja_fileButtonHtml'}{l s='Choose File' mod='roja45quotationspro' js=1}{/addJsDefL}
