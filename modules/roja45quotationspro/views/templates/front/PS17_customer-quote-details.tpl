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

{extends file='customer/page.tpl'}

{block name="page_title"}
    {l s='Quote Details: ' mod='roja45quotationspro'}
{/block}

{block name='page_content'}
    <div id="quotation_details" class="col-lg-12">
        <div class="row">
            <div class="col-lg-9">
                <div class="row">
                    <div class="quotation_details_block header">
                        <div class="col-lg-12 col-md-12">
                            <article class="box">
                                <h3>
                                    {l s='Quotation Reference' mod='roja45quotationspro'} {$quotation->reference}
                                </h3>
                            </article>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="quotation_details_block products">
                        <div class="col-lg-12 col-md-12">
                            <article class="box">
                                <div class="quotation-products table" id="quotationProducts">
                                    <div class="tr th">
                                        <div class="td"></div>
                                        <div class="td">
                                            <div class="title_box">{l s='Product' mod='roja45quotationspro'}</div>
                                            <div class="title_box small">&nbsp;</div>
                                        </div>
                                        <div class="td">
                                            <div class="title_box">{l s='Reference' mod='roja45quotationspro'}</div>
                                            <div class="title_box small">&nbsp;</div>
                                        </div>
                                        <div class="td">
                                            <div class="title_box">{l s='Comment' mod='roja45quotationspro'}</div>
                                            <div class="title_box small">&nbsp;</div>
                                        </div>
                                        <div class="td">
                                            <div class="title_box ">{l s='Unit Price' mod='roja45quotationspro'}</div>
                                            <div class="title_box small">{if ($show_taxes)}
                                                    {l s='(tax incl.)' mod='roja45quotationspro'}
                                                {else}
                                                    {l s='(tax excl.)' mod='roja45quotationspro'}
                                                {/if}
                                            </div></span>
                                        </div>
                                        <div class="td">
                                            <div class="title_box ">{l s='Qty' mod='roja45quotationspro'}</div>
                                            <div class="title_box small">&nbsp;</div>
                                        </div>
                                        <div class="td">
                                            <div class="title_box ">{l s='Total' mod='roja45quotationspro'}</div>
                                            <div class="title_box small">{if ($show_taxes)}
                                                    {l s='(tax incl.)' mod='roja45quotationspro'}
                                                {else}
                                                    {l s='(tax excl.)' mod='roja45quotationspro'}
                                                {/if}
                                            </div></span>
                                        </div>
                                        {if ($show_taxes)}
                                            <div class="td">
                                                <div class="title_box ">{l s='Tax' mod='roja45quotationspro'}</div>
                                                <div class="title_box small">&nbsp;</div>
                                            </div>
                                            <div class="td">
                                                <div class="title_box ">{l s='Tax Rate' mod='roja45quotationspro'}</div>
                                                <div class="title_box small">&nbsp;</div>
                                            </div>
                                        {/if}
                                    </div>
                                    {foreach from=$quotation_products item=product key=k}
                                        {include file="module:roja45quotationspro/views/templates/front/PS17_customer-quote-product-line.tpl"}
                                    {/foreach}
                                </div>
                            </article>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {if $quotation->quote_sent=="1"}
                        {if count($shipping) || count($discounts)}
                            <div class="quotation_details_block charges">
                                <div class="col-lg-6 col-md-12">
                                    <article class="box">
                                        {if (count($shipping) || !($quotation->isLocked()))}
                                            <div id="charges_table" class="table">
                                                {include file="module:roja45quotationspro/views/templates/front/PS17_customer-quote-shipping-table.tpl"}
                                            </div>
                                        {/if}
                                    </article>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <article class="box">
                                        {if (count($discounts) || !($quotation->isLocked()))}
                                            <div id="discount_table" class="table">
                                                {include file="module:roja45quotationspro/views/templates/front/PS17_customer-quote-discount-table.tpl"}
                                            </div>
                                        {/if}
                                    </article>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        {/if}

                        <div class="quotation_details_block totals">
                            <div class="offset-lg-6 col-lg-6 col-md-12">
                                <article class="box">
                                    {include file="module:roja45quotationspro/views/templates/front/PS17_customer-quote-quotation-totals.tpl"}
                                </article>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="totals">
                            <div class="col-lg-12 col-md-12">
                                {if ($exchange_rate != 1)}
                                    <article class="box">
                                        <div class="panel panel-total">
                                            <p class="alert alert-warning">
                                                {l s='Your quote has been provided in your requested currency.  Please be aware that currency fluctuations may result in the price you have been quoted changing.  We reserve the right to change or cancel this quote at any time.' mod='roja45quotationspro'}
                                            </p>
                                        </div>
                                    </article>
                                {/if}
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        {if count($documents)}
                            <div class="documents">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <article class="box">
                                            <h4>{l s='Documents' mod='roja45quotationspro'}</h4>
                                            <table class="documents-table">
                                                <tbody>
                                                    <tr>
                                                        {foreach $documents as $document}
                                                            <td class="{$document.file_type}">
                                                                <div class="document-container" title="{$document.display_name}">
                                                                    <p>{$document.display_name|truncate:10}</p>
                                                                    <p><a
                                                                            href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'downloadFile', 'id_roja45_quotation' => $quotation->id_roja45_quotation, 'file' => $document['id_roja45_quotation_document']]}">{l s='Download' mod='roja45quotationspro'}</a>
                                                                    </p>
                                                                </div>
                                                            </td>
                                                        {/foreach}
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </article>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        {/if}
                        {if $quotation->quote_sent=="1"}
                            <div class="actions">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <article class="buttons box">
                                            {if $catalog_mode}
                                                <a href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'submitRequestOrder', 'id_roja45_quotation' => $quotation->id_roja45_quotation]}"
                                                    class="btn btn-default btn-primary btn-add-to-order ajax-add-to-cart">
                                                    <span>{l s='Order' mod='roja45quotationspro'}</span>
                                                </a>
                                            {else}
                                                {if $quotation->expired=="1"}
                                                    <div class="quote-expired">
                                                        <span>{l s='EXPIRED' mod='roja45quotationspro'}</span>
                                                    </div>
                                                {elseif $quotation->ordered=="1"}
                                                    <div class="quote-ordered">
                                                        <span>{l s='ORDERED' mod='roja45quotationspro'}</span>
                                                    </div>
                                                {elseif $quotation->quote_sent=="1"}
                                                    {if $catalog_mode}
                                                        <a title="{l s='Click here to request an order.  You request will be sent to our operators.' mod='roja45quotationspro'}"
                                                            href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'submitRequestOrder', 'id_roja45_quotation' => $quotation->id_roja45_quotation]}"
                                                            class="btn btn-default btn-primary btn-add-to-cart ajax-add-to-cart">{l s='Add To Cart' mod='roja45quotationspro'}</a>
                                                    {else}
                                                        <a title="{l s='Click to add this quote to your cart.' mod='roja45quotationspro'}"
                                                            href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'submitAddToCart', 'id_roja45_quotation' => $quotation->id_roja45_quotation]}"
                                                            class="btn btn-default btn-primary btn-add-to-cart ajax-add-to-cart">{l s='Add To Cart' mod='roja45quotationspro'}</a>
                                                    {/if}
                                                {else}
                                                    <a href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'submitAddToCart', 'id_roja45_quotation' => $quotation->id_roja45_quotation]}"
                                                        class="btn btn-default btn-primary btn-add-to-cart ajax-add-to-cart">
                                                        <span>{l s='Add To Cart' mod='roja45quotationspro'}</span>
                                                    </a>
                                                {/if}
                                            {/if}
                                        </article>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        {/if}
                    {/if}
                </div>
            </div>
            <div class="conversation col-lg-3 col-md-12">
                <div class="row">
                    <div class="send_message">
                        <div class="col-lg-12 col-md-12">
                            <div class="row">
                                <article class="box">
                                    <h3>{l s='Messages' mod='roja45quotationspro'}</h3>
                                </article>
                            </div>
                        </div>
                    </div>
                    <div class="message_history">
                        <article class="box">
                            <section id="customer_message_form" class="message-form">
                                <form id="customerMessageForm"
                                    action="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => 'submitCustomerMessage', 'id_roja45_quotation' => $quotation->id_roja45_quotation]}"
                                    method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="ajax" value="1" />
                                    <input type="hidden" name="id_customer_thread" value="{$id_customer_thread}" />
                                    <section class="form-fields">
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <textarea id="contactform-message" class="form-control" name="message"
                                                    rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <input id="attachment" type="file" name="uploadedfile"
                                                    data-buttontext="{l s='Choose file' mod='roja45quotationspro'}"
                                                    tabindex="-1" />
                                            </div>
                                        </div>
                                    </section>

                                    <footer class="form-footer text-sm-right">

                                        <input type="text" name="url" value="">
                                        <input type="hidden" name="token" value="8831f399153f79c467f54a1cb6d3d6d4">
                                        <button class="btn btn-primary" type="submit" id="submitMessage"
                                            name="submitMessage"
                                            value="{l s='Send' mod='roja45quotationspro'}">{l s='Send' mod='roja45quotationspro'}</button>
                                    </footer>
                                </form>
                                <div id="message_form_modal" style="display:none;">
                                    <div class="modal-wait-icon">
                                        <svg class="spin" xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M17.65 6.35A7.958 7.958 0 0 0 12 4a8 8 0 0 0-8 8a8 8 0 0 0 8 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18a6 6 0 0 1-6-6a6 6 0 0 1 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"
                                                fill="currentColor"></path>
                                        </svg>
                                        <svg version="1.1" id="Livello_1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 595.3 841.9" style="enable-background:new 0 0 595.3 841.9;"
                                            xml:space="preserve">
                                            <path class="st0"
                                                d="M48.8,191.2c0-31.7,25.7-57.5,57.5-57.5h314.4l126.1,126.1v391c0,31.7-25.7,57.5-57.5,57.5H106.3c-31.7,0-57.5-25.7-57.5-57.5V191.2z M163.7,287h114.9v38.3H163.7V287z M431.9,401.9H163.7v38.3h268.2V401.9z M431.9,516.8H316.9v38.3h114.9V516.8z" />
                                        </svg>
                                    </div>
                                </div>
                            </section>
                        </article>
                    </div>
                </div>
                <div class="row">
                    <article class="customer-messages box">
                        {foreach $messages as $message}
                            <div class="message {if $message.id_employee != 0}admin-message{else}customer-message{/if}">
                                <div class="header"><span class="name">{$message.name}</span><span
                                        class="received">{$message.date_add}</span></div>
                                <div class="message-content">{if strlen($message.message) > 100}<a href="#">{/if}<span
                                            class="msg">{$message.message|substr:0:100}</span>{if strlen($message.message) > 100}</a>{/if}
                                </div>
                            </div>
                        {/foreach}
                    </article>
                </div>
            </div>
        </div>
    </div>

{/block}

{block name='page_footer'}
    <a href="{url entity='module' name='roja45quotationspro' controller='QuotationsProFront' params = ['action' => $back]}"
        class="account-link roja-account-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path d="M14 7l-5 5l5 5V7z" />
        </svg>
        <span>{l s='Back' mod='roja45quotationspro'}</span>
    </a>
{/block}