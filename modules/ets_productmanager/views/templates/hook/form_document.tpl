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
        <div class="panel-heading">{l s='Edit documentation' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            {foreach $docFields as $key=>$field}
                <div class="form-group ets-czf-item-lang-input">
                    <div class="input-group locale-input-group js-locale-input-group d-flex">
                        {foreach $czfLanguages as $lang}
                            <div class="context-text-wrapper doc-lang-body flex-fill js-locale-input js-locale-{$lang.iso_code|escape:'html':'UTF-8'} {if $lang.id_lang != $activeLang.id_lang}d-none{/if}">
                                {if $field.type !== 'hidden'}
                                    <label class="form-control-label {if $key == 'doc_name'}label-doc-name{/if} {if (isset($field.required) && $field.required) || ($docFields.doc_file.value[$lang.id_lang] && $key == 'doc_name')}required{/if}" data-lang="{$lang.id_lang|escape:'html':'UTF-8'}">{$field.label|escape:'html':'UTF-8'}</label>
                                    <div class="{if $key == 'doc_name'}field-doc-name{/if}" data-lang="{$lang.id_lang|escape:'html':'UTF-8'}">

                                        {if $field.type == 'text'}
                                            <input type="text" name="ets_czf[{$key|escape:'html':'UTF-8'}][{$lang.id_lang|escape:'html':'UTF-8'}]" class="form-control js-ets-czf-doc-name" value="{if isset($field.value[$lang.id_lang])}{$field.value[$lang.id_lang]|escape:'html':'UTF-8'}{/if}" />
                                        {elseif $field.type == 'textarea'}
                                            <textarea name="ets_czf[{$key|escape:'html':'UTF-8'}][{$lang.id_lang|escape:'html':'UTF-8'}]" class="form-control ets-czf-doc-desc-input">{if isset($field.value[$lang.id_lang])}{$field.value[$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                                        {elseif $field.type == 'file'}
                                            <input type="file" class="form-control js-czf-doc-file" name="ets_czf_{$key|escape:'html':'UTF-8'}[{$lang.id_lang|escape:'html':'UTF-8'}]" />
                                            <p class="text-muted ct_des">{l s='Accepted formats: zip, pdf. Limit:' mod='ets_productmanager'} {$maxFileSize|escape:'html':'UTF-8'}{l s='MB' mod='ets_productmanager'}</p>
                                            <div class="doc-file-preview" data-product-id="{$idProduct|intval}">
                                                {if $key == 'doc_file' && $field.value[$lang.id_lang]}
                                                    {include 'module:ets_customfields/views/templates/hook/document_file.tpl' fileName=$field.value[$lang.id_lang] fileSize=$docFields.doc_size.value[$lang.id_lang] fileType=$docFields.doc_type.value[$lang.id_lang] idLang=$lang.id_lang idCzfProduct=$czfFields.id_ets_czf_product linkDownload=$baseLinkDownloadDoc|cat:'&idLang='|cat:$lang.id_lang fileDisplayName=$field.doc_display_name[$lang.id_lang] nbDownload=$field.nbDownload[$lang.id_lang]}
                                                {/if}
                                            </div>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        {/foreach}
                        {if count($czfLanguages) > 1 && $field.type !== 'hidden'}
                            <div class="dropdown js-ets-czf-dropdown-switch-lang">
                                <button class="btn btn-outline-secondary dropdown-toggle js-locale-btn" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        id="ets-czf-content-txt-{$key|escape:'html':'UTF-8'}">
                                    {$activeLang.iso_code|escape:'html':'UTF-8'}
                                </button>
                                <div class="dropdown-menu"
                                     aria-labelledby="ets-czf-content-txt-{$key|escape:'html':'UTF-8'}">
                                    {foreach $czfLanguages as $lang}
                                        <span class="dropdown-item js-locale-item js-ets-czf-lang-item"
                                              data-locale="{$lang.iso_code|escape:'html':'UTF-8'}">{$lang.name|escape:'html':'UTF-8'}</span>
                                    {/foreach}
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
        <div class="panel-footer">
            <button type="button" name="btnCancel" class="btn btn-default pull-left">
            <i class="process-icon-cancel svg_process-icon">
                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>

            </i> 
            {l s='Cancel' mod='ets_productmanager'}
            </button>
            <input type="hidden" name="id_product" value="{$idProduct|intval}" />
            <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitDocumentProduct">
                <i class="process-icon-save ets_svg_process">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                </i>
                {l s='Save' mod='ets_productmanager'}
            </button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function(){
        $('.js-ets-czf-dropdown-switch-lang .js-ets-czf-lang-item').click(function () {
            var isoCode = $(this).attr('data-locale');
            $('.js-ets-czf-dropdown-switch-lang .js-locale-btn').html(isoCode);
            $('.ets-czf-item-lang-input .js-locale-input-group .js-locale-input:not(.js-locale-' + isoCode + ')').addClass('d-none');
            $('.ets-czf-item-lang-input .js-locale-input-group .js-locale-' + isoCode).removeClass('d-none');
        });
    });
</script>