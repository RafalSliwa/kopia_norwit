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
<script type="text/javascript">
var delete_file_comfirm ='{l s='Do you want to delete this file?' mod='ets_productmanager' js=1}'
</script>
<script type="text/javascript" src="{$module_dir|escape:'html':'UTF-8'}views/js/associated.js"></script>
<link href="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/ui/themes/base/jquery.ui.datepicker.css" rel="stylesheet" type="text/css"/>
<link href="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/ui/themes/base/jquery.ui.theme.css" rel="stylesheet" type="text/css"/>
<form class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="panel-heading">{l s='Edit associated file' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            <div id="virtual_product" class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <h2>{l s='Does this product have an associated file?' mod='ets_productmanager'}</h2>
                    </div>
                    <div class="col-lg-6">
                        <fieldset class="">
                            <div id="form_step3_virtual_product_is_virtual_file">
                                <div class="radio">
                                    <label class="">
                                        <input id="form_step3_virtual_product_is_virtual_file_1" name="is_virtual_file" value="1" type="radio"{if $productDownload && $productDownload.active} checked="checked"{/if} />
                                        {l s='Yes' mod='ets_productmanager'}
                                    </label>
                                    <label class="">
                                        <input id="form_step3_virtual_product_is_virtual_file_0" name="is_virtual_file" value="0" type="radio" {if !$productDownload || ($productDownload && !$productDownload.active)} checked="checked"{/if} />
                                        {l s='No' mod='ets_productmanager'}
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div id="virtual_product_content" class="row" style="">
                    <input id="virtual_product_filename" name="virtual_product_filename" value="{if $productDownload}{$productDownload.filename|escape:'html':'UTF-8'}{/if}" type="hidden" />
                    <input name="virtual_product_id" type="hidden" value="{if $productDownload}{$productDownload.id_product_download|intval}{/if}"/>
                    <div class="col-md-12">
                        <fieldset class="form-group">
                            <label class="form-control-label">{l s='File' mod='ets_productmanager'}</label>
                            <span class="help-box">
                                <span>{l s='Upload a file from your computer (40MB max.)' mod='ets_productmanager'}</span>
                            </span>
                            <div id="form_step3_virtual_product_file_input" class="{if $productDownload && $productDownload.active && $productDownload.filename}hide{else}show{/if}">
                                <div class="custom-file">
                                    <input id="form_step3_virtual_product_file" class="custom-file-input" name="virtual_product_file_uploader" type="file" />
                                    <label class="custom-file-label" for="form_step3_virtual_product_file"> {l s='Choose file(s)' mod='ets_productmanager'} </label>
                                </div>
                            </div>
                            <div id="form_step3_virtual_product_file_details" class="{if $productDownload && $productDownload.active && $productDownload.filename}show{else}hide{/if}">
                              {if $productDownload && $productDownload.active && $productDownload.filename}
                                  <a href="{$link_download_file|escape:'html':'UTF-8'}" target="_blank" class="btn btn-default btn-sm download ets_pmn_download_file">{l s='Download file' mod='ets_productmanager'}</a>
                                  <a href="{$link_delete_file|escape:'html':'UTF-8'}" class="btn btn-danger btn-sm delete ets_pmn_delete_file">{l s='Delete this file' mod='ets_productmanager'}</a>
                              {/if}
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="form-group">
                            <label class="form-control-label">{l s='File name' mod='ets_productmanager'}</label>
                            <span class="help-box" title="">
                                <span>{l s='The full file name with its extension (e.g. Book.pdf)' mod='ets_productmanager'}</span>
                            </span>
                            <input id="form_step3_virtual_product_name" class="form-control" name="virtual_product_name" type="text" value="{$productDownload.display_filename|escape:'html':'UTF-8'}" />
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="form-group">
                            <label class="form-control-label">{l s='Number of allowed downloads' mod='ets_productmanager'}</label>
                            <span class="help-box">
                                <span>{l s='Number of downloads allowed per customer. Set to 0 for unlimited downloads.' mod='ets_productmanager'}</span>
                            </span>
                            <input id="form_step3_virtual_product_nb_downloadable" class="form-control" name="virtual_product_nb_downloadable" type="text" value="{$productDownload.nb_downloadable|intval}" />
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="form-group">
                            <label class="form-control-label">{l s='Expiration date' mod='ets_productmanager'}</label>
                            <span class="help-box" title="">
                                <span>{l s='If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.' mod='ets_productmanager'}</span>
                            </span>
                            <div class="input-group datepicker">
                                <input class="form-control" id="form_step3_virtual_product_expiration_date" autocomplete="off" name="virtual_product_expiration_date" placeholder="YYYY-MM-DD" type="text" value="{if $productDownload.date_expiration!='0000-00-00'}{$productDownload.date_expiration|escape:'html':'UTF-8'}{/if}" />
                                <div class="input-group-append">
                                    <div class="input-group-text">

                                            <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/></svg>

                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="hidden" name="product_virtual" value="1" />
            <button type="button" name="btnCancel" class="btn btn-default pull-left">
            <i class="process-icon-cancel svg_process-icon">
                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>

            </i> {l s='Cancel' mod='ets_productmanager'}</button>
            <input type="hidden" name="id_product" value="{$id_product|intval}" />
            <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitAssociatedFilesProduct">
            <i class="process-icon-save ets_svg_process">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                </i>
                {l s='Save' mod='ets_productmanager'}
            </button>
        </div>
    </div>
</form>