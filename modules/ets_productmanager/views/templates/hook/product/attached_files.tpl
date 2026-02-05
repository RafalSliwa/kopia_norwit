{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<form class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="panel-heading">{l s='Edit attached files' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            <div id="form_attached_file">
                <div class="row">
                    <div class="col-md-12">
                        <p class="subtitle">{l s='Add files that customers can download directly on the product page (instructions, manual, recipe, etc.)' mod='ets_productmanager'}.</p>
                        <div class="js-options-no-attachments{if $attachments} hide{/if}">
                            <p>{l s='There is no attachment yet.' mod='ets_productmanager'}</p>
                        </div>
                        <div id="product-attachments" class="panel panel-default {if !$attachments} hide{/if}">
                            <div class="panel-body js-options-with-attachments ">
                                <div>
                                    <table style="width: 100%;">
                                        <thead class="thead-default">
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th>{l s='Title' mod='ets_productmanager'}</th>
                                                <th >{l s='File name' mod='ets_productmanager'}</th>
                                                <th>{l s='Type' mod='ets_productmanager'}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {if $attachments}
                                                {foreach from = $attachments item='attachment'}
                                                    <tr>
                                                        <td><input type="checkbox" name="product_attachments[]" value="{$attachment.id_attachment|intval}" data-id_product="{$id_product|intval}" data-id_attachment="{$attachment.id_attachment|intval}" class="product_attachments"{if $attachment.id_product} checked="checked"{/if} /></td>
                                                        <td>{$attachment.name|escape:'html':'UTF-8'}</td>
                                                        <td>{$attachment.file_name|escape:'html':'UTF-8'}</td>
                                                        <td> {$attachment.mime|escape:'html':'UTF-8'}</td>
                                                    </tr>
                                                {/foreach}
                                            {/if}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <br />
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-outline-secondary mb-2 collapsed ets_add_attachfile" href="#collapsedForm" data-toggle="collapse" aria-expanded="false" aria-controls="collapsedForm">

                                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>

                            {l s='Attach a new file' mod='ets_productmanager'}
                        </a>
                        <fieldset id="collapsedForm" class="collapse" style="">
                            <div id="form_step6_attachment_product">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input id="form_step6_attachment_product_file" class="custom-file-input" name="attachment_product_file" data-multiple-files-text="%count% file(s)" data-locale="en" type="file" />
                                        <label class="custom-file-label" for="form_step6_attachment_product_file">{l s='Choose file' mod='ets_productmanager'}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input id="form_step6_attachment_product_name" class="form-control" name="attachment_product_name" placeholder="Title" type="text" />
                                </div>
                                <div class="form-group">
                                    <input id="form_step6_attachment_product_description" class="form-control" name="attachment_product_description" placeholder="Description" type="text" />
                                </div>
                                <div class="form-group">
                                    <button id="form_step6_attachment_product_add" class="btn-outline-primary pull-right btn" type="button" name="attachment_product_add" data-product-id="{$id_product|intval}">{l s='Add' mod='ets_productmanager'}</button>
                                    <button id="form_step6_attachment_product_cancel" class="btn-outline-secondary pull-right mr-1 btn collapsed" type="button" name="attachment_product_cancel" data-toggle="collapse" data-target="#collapsedForm" aria-expanded="false">{l s='Cancel' mod='ets_productmanager'}</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>