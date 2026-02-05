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
{extends file='page.tpl'}

{block name='page_header_container'}
    {block name='page_title'}
        <header class="page-header">
            {if isset($numberProducts) && $numberProducts > 0}
            <h4 class="title_block quote_title">{l s='Your Request' mod='roja45quotationspro'}
                <span id="summary_products_quantity">({$numberProducts} {if $numberProducts == 1}{l s='product' mod='roja45quotationspro'}{else}{l s='products' mod='roja45quotationspro'}{/if})</span>
            </h4>
            {elseif ($roja45quotationspro_noproductrequest)}
            <h4 class="title_block quote_title">{l s='Your Request' mod='roja45quotationspro'}</h4>
            {/if}
            <h4 class="title_block received_title" style="display: none;">{l s='Request Received' mod='roja45quotationspro'}</h4>
        </header>
    {/block}
{/block}

{block name='page_content_container'}
    <section id="quote_summary" class="row">
        <div id="request-summary-content" class="">
            <form action="{$roja45_quoationspro_controller}" method="post" id="quotationspro_request_form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="submitRequest"/>
                <input type="hidden" name="ROJA45QUOTATIONSPRO_FORMDATA"/>

                {assign var='current_step' value='summary'}

                {include file='_partials/form-errors.tpl' errors=$errors}

                {if !$empty}
                <div id="request-summary-products" class="request-summary-container col-lg-12">
                    <div class="flex-table" role="rowgroup">
                        <div class="column">
                            <div class="flex-row header {if $roja45quotationspro_showpriceinsummary}show-price{/if} {if $quotation_has_customizations}has-customizations{/if}">
                                <div class="flex-cell quote-product-image first" role="columnheader">{l s='Product' mod='roja45quotationspro'}</div>
                                <div class="flex-cell quote-product-description" role="columnheader">{l s='Description' mod='roja45quotationspro'}</div>
                                {if $quotation_has_customizations}
                                    <div class="flex-cell quote-product-price" role="columnheader">{l s='Customizations' mod='roja45quotationspro'}</div>
                                {/if}
                                {if $roja45quotationspro_showpriceinsummary}
                                    <div class="flex-cell quote-product-price" role="columnheader">{l s='Unit Price' mod='roja45quotationspro'} {if $display_tax}{l s='(inc.)' mod='roja45quotationspro'}{else}{l s='(exc.)' mod='roja45quotationspro'}{/if}</div>
                                {/if}
                                <div class="flex-cell quote-product-quantity" role="columnheader">{l s='Quantity' mod='roja45quotationspro'}</div>
                                <div class="flex-cell quote-delete last_item" role="columnheader"></div>
                            </div>
                            {foreach $quotation_products as $product}
                                <div class="request-product flex-row {if $roja45quotationspro_showpriceinsummary}show-price{/if}"
                                     data-id-roja45-quotation-requestproduct="{$product.id_roja45_quotation_requestproduct}">
                                    <div class="flex-cell quote-product-image" role="cell">
                                        <a href="{$product.link|escape:'html':'UTF-8'}">
                                            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}"
                                                 alt="{$product.name|escape:'html':'UTF-8'}"
                                                    {if isset($smallSize)}
                                                width="{$smallSize.width|escape:'html':'UTF-8'}"
                                            height="{$smallSize.height|escape:'html':'UTF-8'}"{/if}/>
                                        </a>
                                    </div>
                                    <div class="flex-cell quote-product-description" role="cell">
                                        <div class="flex-cell-content">
                                            <h4 class="rental-summary-line product-name">
                                                <a href="{$product.link|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a>
                                            </h4>
                                            {if isset($product.attributes) && $product.attributes}
                                                <div>
                                                    <a href="{$product.link|escape:'html':'UTF-8'}">{$product.attributes|escape:'html':'UTF-8'}</a>
                                                </div>
                                            {/if}
                                            {if isset($product.manufacturer)}
                                                <div>
                                                    <small class="quote_ref">{$product.manufacturer|escape:'html':'UTF-8'}</small>
                                                </div>
                                                <div>
                                                    <small class="quote_ref">{l s='SKU:' mod='roja45quotationspro'} {$product.reference|escape:'html':'UTF-8'}</small>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                    {if $quotation_has_customizations}
                                    <div class="flex-cell quote-product-customizations" role="cell">
                                        <div class="flex-cell-content">
                                            {if count($product.customizations)}
                                            <div class="customizations">
                                                <h5><a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$product.id_roja45_quotation_requestproduct}">{l s='Show Customization' mod='roja45quotationspro'}</a></h5>
                                            </div>
                                            <div class="modal fade customization-modal" id="product-customizations-modal-{$product.id_roja45_quotation_requestproduct}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='roja45quotationspro'}">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                            <h4 class="modal-title">{l s='Product customization' mod='roja45quotationspro'}</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            {foreach $product.customizations as $customization}
                                                                <div class="product-customization-line row">
                                                                    <div class="col-sm-3 col-xs-4 label">
                                                                        {$customization.name}
                                                                    </div>
                                                                    <div class="col-sm-9 col-xs-8 value">
                                                                        {if $customization.type == 1}
                                                                            {$customization.value}
                                                                        {elseif $customization.type == 0}
                                                                            <img src="{$customization.image_small}">
                                                                        {/if}
                                                                    </div>
                                                                </div>
                                                            {/foreach}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {/if}
                                        </div>
                                    </div>
                                    {/if}
                                    {if $roja45quotationspro_showpriceinsummary}
                                        <div class="flex-cell quote-product-price" role="cell">
                                            <div class="flex-cell-content">
                                                <div class="product-prices">
                                                    {if $roja45quotationspro_replace_zero_price && $product.product_price==0}
                                                        <div class="product-price h5">
                                                            <div class="current-price">
                                                                <div><span>{$roja45quotationspro_replace_zero_price_text}</span></div>
                                                            </div>
                                                        </div>
                                                    {else}
                                                    {if $product.product_discounted}
                                                        <div class="product-discount">
                                                            <span class="regular-price">{if $display_tax}{$product.product_price_without_reduction_inc_formatted|escape:'html':'UTF-8'}{else}{$product.product_price_without_reduction_formatted|escape:'html':'UTF-8'}{/if}</span>
                                                        </div>
                                                        <div class="product-price h5 has-discount">
                                                            <div class="current-price">
                                                                <div><span>{if $display_tax}{$product.product_price_inc_formatted|escape:'html':'UTF-8'}{else}{$product.product_price_formatted|escape:'html':'UTF-8'}{/if}</span></div>
                                                                <div><span class="discount discount-percentage">{l s='%discount%% Discount' sprintf=['%discount%' => $product.product_discount] mod='roja45quotationspro'}</span></div>
                                                            </div>
                                                        </div>
                                                    {else}
                                                        <div class="product-price h5">
                                                            <div class="current-price">
                                                                <div><span>{if $display_tax}{$product.product_price_inc_formatted|escape:'html':'UTF-8'}{else}{$product.product_price_formatted|escape:'html':'UTF-8'}{/if}</span></div>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                    <div class="flex-cell quote-product-quantity roja45quotationspro_button_container" role="cell">
                                        <div class="flex-cell-content col_quote_quantity"
                                             data-title="{l s='Quantity' mod='roja45quotationspro'}"
                                             data-id-product="{$product.id_product|escape:'html':'UTF-8'}"
                                             data-id-product-attribute="{$product.id_product_attribute|escape:'html':'UTF-8'}">
                                            <input
                                                    type="text"
                                                    name="quote_quantity"
                                                    data-touchspin-vertical="{$roja45quotationspro_touchspin}"
                                                    value="{$product.quote_quantity|escape:'html':'UTF-8'}"
                                                    class="quote_quantity_wanted input-group"
                                                    min="{$product.minimal_quantity|escape:'html':'UTF-8'}"
                                                    aria-label="{l s='Quantity' mod='roja45quotationspro'}">
                                        </div>
                                    </div>
                                    <div class="flex-cell quote-delete" role="cell">
                                        <div class="flex-cell-content delete">
                                            <div class="center">
                                            <a rel="nofollow" title="{l s='Delete' mod='roja45quotationspro'}" class="quote_quantity_delete btn-roja-delete-item"
                                               id="{$product.id_product|escape:'html':'UTF-8'}_{$product.id_product_attribute|escape:'html':'UTF-8'}_{if $displayQuantity > 0}nocustom{else}0{/if}"
                                               href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'deleteFromQuote', 'id_roja45_quotation_requestproduct' => $product.id_roja45_quotation_requestproduct]}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                            </a>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
                {else}
                    {if !$roja45quotationspro_noproductrequest}
                    <p class="alert alert-warning">{l s='Your request is empty.' mod='roja45quotationspro'}</p>
                    {/if}
                {/if}

                <div class="request-summary-container col-lg-12">
                    <h3 class="page-heading">{l s='Please provide the following information' mod='roja45quotationspro'}</h3>

                    <div class="quotationspro_request_container col-lg-12">
                        <div class="row">
                            {for $col_counter=1 to $columns}
                                <div id="quotationspro_request_column_{$col_counter}" data-column="{$col_counter}" class="quotationspro_request column col-lg-{$col_width} col-md-12 col-xs-12">
                                    <div class="row">
                                        <div class="quotationspro_request_column_container col-lg-12">
                                            {if (isset( $form.$col_counter.settings.column_heading ))}
                                                <h3 class="page-subheading">{$form.$col_counter.settings.column_heading}</h3>
                                            {/if}
                                            {foreach $form.$col_counter.fields as $name => $fields}
                                                <a id="COLLAPSE_{$name}" class="collapse-link" style="display:none;" data-toggle="collapse" data-target="#COMPONENT_{$name}"><span class="open-text">{l s='Add New Address' mod='roja45quotationspro'}</span><span class="close-text">{l s='Close' mod='roja45quotationspro'}</span></a>
                                                <div id="COMPONENT_{$name}" class="quotationspro_request_field_container collapse show in" aria-expanded="true">
                                                {foreach $fields as $pos => $field}
                                                    {if $field.enabled}
                                                    {if $field.collapse}<div id="LINK_{$name}" class="quotationspro_request_field_collapse" data-collapse-target="#COLLAPSE_{$field.collapse}"></div>{/if}

                                                    {if $field.type=='TEXT'}
                                                        {include
                                                        file="module:roja45quotationspro/views/templates/front/fo_text_field.tpl"
                                                        id=$field.id
                                                        name=$field.name
                                                        field_label=$field.label
                                                        field_type=$field.field_type
                                                        required=$field.required
                                                        disabled=$field.disabled
                                                        readonly=$field.readonly
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
                                                        file="module:roja45quotationspro/views/templates/front/fo_textarea_field.tpl"
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
                                                        file="module:roja45quotationspro/views/templates/front/fo_checkbox_field.tpl"
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
                                                        file="module:roja45quotationspro/views/templates/front/fo_select_field.tpl"
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
                                                        display_as=$field.display_as
                                                        }
                                                    {elseif $field.type=='SWITCH'}
                                                        {include
                                                        file="module:roja45quotationspro/views/templates/front/fo_switch_field.tpl"
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
                                                        file="module:roja45quotationspro/views/templates/front/fo_date_field.tpl"
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
                                                    {elseif $field.type=='DATEPERIOD'}
                                                        {include
                                                        file="module:roja45quotationspro/views/templates/front/fo_date_period_field.tpl"
                                                        id=$field.id
                                                        name=$field.name
                                                        default='0'
                                                        field_label=$field.label
                                                        start_field_label=$field.start_label
                                                        end_field_label=$field.end_label
                                                        field_type=$field.field_type
                                                        field_description=$field.description
                                                        required=$field.required
                                                        format=$field.format
                                                        class=$field.class
                                                        validationMethod='isDate'
                                                        }
                                                    {elseif $field.type=='HEADER'}
                                                        {include
                                                        file='module:roja45quotationspro/views/templates/front/fo_header_field.tpl'
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
                                </div>
                            {/for}
                        </div>
                        {if $roja45quotationspro_enable_fileupload}
                            <div class="row">
                                <div id="quotationspro_request_column_file" class="quotationspro_request column col-lg-12 col-md-12">
                                    <div class="quotationspro_request_column_container col-lg-12">
                                        <div class="form-group _group">
                                            <label class="control-label">
                                                {l s='Attach File' mod='roja45quotationspro'}
                                            </label>
                                            <div class="row">
                                                <div class="col-lg-12">
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
                    <div class="quotationspro_request notes col-lg-12">
                        <div class="row button-container">
                            <div class="col-lg-6">
                                <div class="row">
                                    <span class="required-field-indicator"><sup>&#42</sup>{l s='Required field' mod='roja45quotationspro'}</span>
                                </div>
                            </div>
                            <div class="customer-copy-checkbox offset-lg-4 col-lg-2">
                                <div class="row">
                                    <label class="field-label pull-right" for="ROJA45QUOTATIONSPRO_CUSTOMER_COPY"><input checked="checked" type="checkbox" class="form-control" id="ROJA45QUOTATIONSPRO_CUSTOMER_COPY" name="ROJA45QUOTATIONSPRO_CUSTOMER_COPY"/> {l s='Send copy to yourself' mod='roja45quotationspro'}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="quote_navigation clearfix col-lg-12">
                        {if $roja45quotationspro_enable_captcha}
                            {if $roja45quotationspro_enable_captchatype == 0}
                                <button type="submit"
                                        id="submitRequest"
                                        class="btn btn-primary request-quotation button-medium disabled"
                                        title="{l s='Request Quote' mod='roja45quotationspro'}">
                                    {l s='Request Quote' mod='roja45quotationspro'}
                                </button>
                            {elseif ($roja45quotationspro_enable_captchatype == 1)}
                                <button class="btn btn-primary request-quotation button-medium"
                                        id="submitRequest"
                                        data-sitekey="{$roja45quotationspro_recaptcha_site_key}"
                                        data-callback='onRecaptchaInvisibleSubmitCallback'>
                                    {l s='Request Quote' mod='roja45quotationspro'}
                                </button>
                            {elseif ($roja45quotationspro_enable_captchatype == 2)}
                                <button type="submit"
                                        id="submitRequest"
                                        class="btn btn-primary request-quotation button-medium disabled"
                                        title="{l s='Request Quote' mod='roja45quotationspro'}">
                                    {l s='Request Quote' mod='roja45quotationspro'}
                                </button>
                            {/if}
                        {else}
                            <button type="submit"
                                    id="submitRequest"
                                    class="btn btn-default btn-primary request-quotation button-medium"
                                    title="{l s='Request Quote' mod='roja45quotationspro'}">
                                {l s='Request Quote' mod='roja45quotationspro'}
                            </button>
                        {/if}
                        <a href="{$home_url}"
                           class="button-exclusive btn btn-default btn-roja-continue-shopping"
                           data-icon="{$roja45quotationspro_iconpack}"
                           title="{l s='Continue shopping' mod='roja45quotationspro'}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M14 7l-5 5l5 5V7z"/></svg>
                            {l s='Continue shopping' mod='roja45quotationspro'}
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <div class="request-summary-container-modal">
            <div class="modal-wait-text-container">
                <div class="modal-wait-icon">
                    <svg class="spin" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24">
                        <path d="M17.65 6.35A7.958 7.958 0 0 0 12 4a8 8 0 0 0-8 8a8 8 0 0 0 8 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18a6 6 0 0 1-6-6a6 6 0 0 1 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" fill="currentColor"></path>
                    </svg>
                </div>
                <div class="modal-wait-text">
                    <p>{l s='Please Wait' mod='roja45quotationspro'}</p>
                </div>
            </div>
        </div>
        <div class="quotationspro_request_container" style="display:none;">
            <p>{l s='Many thanks, we have received your request.  We will contact you with your quotation as soon as possible.' mod='roja45quotationspro'}</p>
            {if $isLogged}
                <p>{l s='You can check the status of your quote from your account area: ' mod='roja45quotationspro'}<a href="{$account_link}">{l s='My Account' mod='roja45quotationspro'}</a></p>
            {/if}
            <p class="quote_navigation clearfix">
                <a href="{$home_url}" title="{l s='Home' mod='roja45quotationspro'}" class="pull-right button-exclusive btn btn-default btn-roja-home">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3L2 12h3v8z"/></svg>
                    {l s='Home' mod='roja45quotationspro'}
                </a>
            </p>
        </div>
    </section>
{/block}

{block name='page_footer_container'}
    <footer class="page-footer">
        {if ($roja45quotationspro_enable_captchatype == 0)}
            <script type="text/javascript">
                {if (($roja45quotationspro_enable_captcha==1) && (null !== $roja45quotationspro_recaptcha_site_key) )}
                var roja45quotationspro_recaptcha_site_key = "{$roja45quotationspro_recaptcha_site_key}";
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
    </footer>
{/block}
