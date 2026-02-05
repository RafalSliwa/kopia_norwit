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
        <div class="panel-heading">{l s='Edit customization' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            <div class="row">
                <div class="col-md-12">
                    <div id="custom_fields" class="mt-3">
                        <p class="subtitle">{l s='Customers can personalize the product by entering some text or by providing custom image files.' mod='ets_productmanager'}</p>
                        <ul class='customFieldCollection nostyle' data-prototype='<div class="row">
<input type="hidden" id="form_step6_custom_fields___name___id_customization_field" name="custom_fields[__name__][id_customization_field]" class="form-control" />

<div class="col-md-4 customFieldCollection-col col-label">
<fieldset class="form-group">
<label class="form-control-label">{l s='Label' mod='ets_productmanager' js=1}</label>
<div class="translations tabbable" id="form_step6_custom_fields___name___label">
    <div class="translationsFields tab-content">
        {foreach from =$languages item='lang'}
            <div data-locale="{$lang.iso_code|escape:'html':'UTF-8'}" class="translationsFields-form_step6_custom_fields___name___label_{$lang.id_lang|intval} tab-pane translation-field translatable-field lang-{$lang.id_lang|intval} {if $lang.id_lang==$id_lang_default} show active{/if}  translation-label-{$lang.iso_code|escape:'html':'UTF-8'}" {if $lang.id_lang!=$id_lang_default} style="display:none"{/if}>
                <div class="col-lg-10">
                    <input type="text" id="form_step6_custom_fields___name___label_{$lang.id_lang|intval}" name="custom_fields[__name__][label][{$lang.id_lang|intval}]" required="required" class="form-control" />
                </div>
                {if count($languages) >1}
                    <div class="col-lg-2">
                        <div class="toggle_form">
                            <button class="btn btn-default dropdown-toggle dropdown-toggle-poup" type="button" tabindex="-1">
                                {$lang.iso_code|escape:'html':'UTF-8'}
                                <i class="icon-caret-down"></i>
                            </button>
                        </div>
                        <ul class="dropdown-menu">
                            {foreach $languages item='language'}
                                <li>
                                    <a class="hideOtherLanguagePopup" href="#" tabindex="-1" data-id-lang="{$language.id_lang|intval}">{$language.name|escape:'html':'UTF-8'}</a>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                {/if}
            </div> 
        {/foreach} 
    </div>
</div>
</fieldset>
</div>
<div class="col-md-3 customFieldCollection-col col-type">
<fieldset class="form-group">
<label class="form-control-label">{l s='Type' mod='ets_productmanager' js=1}</label>

<select id="form_step6_custom_fields___name___type" name="custom_fields[__name__][type]" class="c-select custom-select"><option value="1">{l s='Text' mod='ets_productmanager' js=1}</option><option value="0">{l s='File' mod='ets_productmanager' js=1}</option></select>
</fieldset>
</div>
<div class="col-md-1 customFieldCollection-col col-delete">
<fieldset class="form-group">
<label class="form-control-label">&nbsp;</label>
<a class="btn btn-block delete" >

                                                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>

</a>
</fieldset>
</div>
<div class="col-md-4 customFieldCollection-col col-req">
<fieldset class="form-group">
<div>
<label class="form-control-label">&nbsp;</label>
</div>
<div class="required-custom-field">
<div class="checkbox">                          
    <label><input type="checkbox"
 data-toggle="switch" class="tiny" id="form_step6_custom_fields___name___require" name="custom_fields[__name__][require]" value="1" />
Required</label>
  </div>
</div>
</fieldset>
</div>
</div>'>
                            {if $customizationFields}
                                {foreach from =$customizationFields key='index' item ='customizationField'}
                                    <li>
                                        <div class="row">
                                            <input id="form_step6_custom_fields_{$index|intval}_id_customization_field" class="form-control" name="custom_fields[{$index|intval}][id_customization_field]" type="hidden" value="{$customizationField->id|intval}" />
                                            <div class="col-xs-10 col-md-4 customFieldCollection-col col-label">
                                                <fieldset class="form-group">
                                                    <label class="form-control-label">{l s='Label' mod='ets_productmanager'}</label>
                                                    <div id="form_step6_custom_fields_{$index|intval}_label" class="translations tabbable">
                                                        <div class="translationsFields tab-content">
                                                            {foreach from=$languages item='lang'}
                                                                <div data-locale="{$lang.iso_code|escape:'html':'UTF-8'}" class="row translationsFields-form_step6_custom_fields_{$index|intval}_label_{$lang.id_lang|intval} tab-pane translation-field translatable-field lang-{$lang.id_lang|intval} {if $lang.id_lang==$id_lang_default} show active{/if}  translation-label-{$lang.iso_code|escape:'html':'UTF-8'}" {if $lang.id_lang!=$id_lang_default} style="display:none"{/if}>
                                                                    <div class="col-lg-10">
                                                                        <input type="text" id="form_step6_custom_fields_{$index|intval}_label_{$lang.id_lang|intval}" name="custom_fields[{$index|intval}][label][{$lang.id_lang|intval}]" required="required" class="form-control" value="{if isset($customizationField->name[$lang.id_lang])} {$customizationField->name[$lang.id_lang]|escape:'html':'UTF-8'}{/if}"/>
                                                                    </div>
                                                                    {if count($languages) >1}
                                                                        <div class="col-lg-2">
                                                                            <div class="toggle_form">
                                                                                <button class="btn btn-default dropdown-toggle dropdown-toggle-poup" type="button" tabindex="-1">
                                                                                    {$lang.iso_code|escape:'html':'UTF-8'}
                                                                                    <i class="icon-caret-down"></i>
                                                                                </button>
                                                                            </div>
                                                                            <ul class="dropdown-menu">
                                                                                {foreach $languages item='language'}
                                                                                    <li>
                                                                                        <a class="hideOtherLanguagePopup" href="#" tabindex="-1" data-id-lang="{$language.id_lang|intval}">{$language.name|escape:'html':'UTF-8'}</a>
                                                                                    </li>
                                                                                {/foreach}
                                                                            </ul>
                                                                        </div>
                                                                    {/if}
                                                                </div> 
                                                            {/foreach}
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-3 col-xs-10 customFieldCollection-col col-type">
                                                <fieldset class="form-group">
                                                    <label class="form-control-label">{l s='Type' mod='ets_productmanager'}</label>
                                                    <select id="form_step6_custom_fields_{$index|intval}_type" class="c-select custom-select" name="custom_fields[{$index|intval}][type]">
                                                        <option value="1"{if $customizationField->type==1} selected="selected"{/if}>{l s='Text' mod='ets_productmanager'}</option>
                                                        <option value="0"{if $customizationField->type==0} selected="selected"{/if}>{l s='File' mod='ets_productmanager'}</option>
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-1 col-xs-2 customFieldCollection-col col-delete">
                                                <fieldset class="form-group">
                                                    <label class="form-control-label">&nbsp;</label>
                                                    <a class="btn btn-block delete">

                                                            <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>

                                                    </a>
                                                </fieldset>
                                            </div>
                                            <div class="col-md-4 customFieldCollection-col col-req">
                                                <fieldset class="form-group">
                                                    <label class="form-control-label xs-hide">&nbsp;</label>
                                                  <div class="required-custom-field">
                                                    <div class="checkbox">                          
                                                        <label><input data-toggle="switch" class="tiny" id="form_step6_custom_fields_{$index|intval}_require" name="custom_fields[{$index|intval}][required]" value="1" type="checkbox"{if $customizationField->required} checked="checked"{/if} /> {l s='Required' mod='ets_productmanager'}</label>
                                                    </div>
                                                  </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </li>
                                {/foreach}
                            {/if}
                        </ul>
                        <a class="btn btn-outline-secondary add ets_addfile_customization" href="#">

                                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>

                            {l s='Add a customization field' mod='ets_productmanager'}
                        </a>
                    </div>
                </div>
            </div>  
        </div>
        <div class="panel-footer">
            <button type="button" name="btnCancel" class="btn btn-default pull-left">
            <i class="process-icon-cancel svg_process-icon">
                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>

            </i> {l s='Cancel' mod='ets_productmanager'}</button>
            <input type="hidden" name="id_product" value="{$id_product|intval}" />
            <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitCustomizationProduct">
            <i class="process-icon-save ets_svg_process">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                </i>
                {l s='Save' mod='ets_productmanager'}
            </button>
        </div>
    </div>
</form>
<script type="text/javascript">
var id_lang_current = '{$id_lang_default|intval}';
</script>