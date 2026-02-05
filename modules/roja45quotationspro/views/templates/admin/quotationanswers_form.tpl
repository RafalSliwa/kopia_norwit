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
    var controller_url = '{$controller_url}';
    var roja45_template_css = '{$roja45_template_css}';
    var roja45_template_type = '{$roja45_template_type}';
    var roja45_quotationspro_error_unabletoclaim = '{l s='An unexpected error occurred while trying to claim this request.' mod='roja45quotationspro' js=1}';
</script>

<form id="roja45_quotationspro_answer_form" class="defaultForm form-horizontal QuotationAnswers" action="{$controller_url}" method="post" enctype="multipart/form-data" novalidate="">
    <input type="hidden" name="id_roja45_quotation_answer" id="id_roja45_quotation_answer" value="{$id_roja45_quotation_answer}">
    <input type="hidden" name="submitAddroja45_quotationspro_answer" value="1">

    <div class="panel">
        <div class="panel-heading">
            <i class="icon-time"></i>{l s='Quotation Answers' mod='roja45quotationspro'}
        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-2 required">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title=""></i>{l s='Name' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-7">
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                            <div class="col-lg-10">
                        {/if}
                        <input type="text" name="name_{$language.id_lang}" id="name_{$language.id_lang}" value="{if isset($fields_value['name'][$language.id_lang])}{$fields_value['name'][$language.id_lang]|escape:'html':'UTF-8'}{/if}" class="" required="required">
                        {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-secondary dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2 required">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title=""></i>{l s='Email Subject' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-7">
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                            <div class="col-lg-9">
                        {/if}
                        <input type="text" name="subject_{$language.id_lang}" id="subject_{$language.id_lang}" value="{if isset($fields_value['subject'][$language.id_lang])}{$fields_value['subject'][$language.id_lang]|escape:'html':'UTF-8'}{/if}" class="" required="required">
                        {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-secondary dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2 required">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">{l s='Template Name' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-10">
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                            <div class="col-lg-10">
                        {/if}
                        <input type="text" name="template_{$language.id_lang}" id="template_{$language.id_lang}" value="{if isset($fields_value['template'][$language.id_lang])}{$fields_value['template'][$language.id_lang]|escape:'html':'UTF-8'}{/if}" class="" required="required">
                        {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-secondary dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-2">{l s='Type' mod='roja45quotationspro'}</label>
                <div class="col-lg-9">
                    <select name="type" class=" fixed-width-xl" id="type">
                        <option value="1" {if $fields_value['type']==1}selected="selected"{/if}>{l s='PDF' mod='roja45quotationspro'}</option>
                        <option value="2" {if $fields_value['type']==2}selected="selected"{/if}>{l s='Email' mod='roja45quotationspro'}</option>
                        <option value="3" {if $fields_value['type']==3}selected="selected"{/if}>{l s='Old Answer Templates' mod='roja45quotationspro'}</option>
                    </select>
                </div>
            </div>

            <div class="form-group html_template_group">
                <label class="control-label col-lg-2">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">{l s='HTML Template' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-10">
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                            <div class="col-lg-10">
                        {/if}

                        <textarea name="html_template_{$language.id_lang}" id="html_template_{$language.id_lang}" class="html_template">{if isset($fields_value['html_template'][$language.id_lang])}{$fields_value['html_template'][$language.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                        {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-secondary dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
            {if $email_template}
            <div class="form-group text_template_group">
                <label class="control-label col-lg-2">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">{l s='Text Template' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-10">
                    {foreach $languages as $language}
                        {if $languages|count > 1}
                            <div class="form-group translatable-field lang-{$language.id_lang}"{if $language.id_lang != $defaultFormLanguage} style="display:none;"{/if}>
                            <div class="col-lg-10">
                        {/if}

                        <textarea name="text_template_{$language.id_lang}"
                                  id="text_template_{$language.id_lang}"
                                  class="text_template"
                                  rows="{if isset($fields_value['text_template_lines'][$language.id_lang])}{$fields_value['text_template_lines'][$language.id_lang]|escape:'html':'UTF-8'}{/if}">{if isset($fields_value['text_template'][$language.id_lang])}{$fields_value['text_template'][$language.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                        {if $languages|count > 1}
                            </div>
                            <div class="col-lg-2">
                                <button type="button" class="btn btn-secondary dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                                    {$language.iso_code}
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    {foreach from=$languages item=language}
                                        <li>
                                            <a href="javascript:hideOtherLanguage({$language.id_lang});" tabindex="-1">{$language.name}</a>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
            {/if}
            {if !$email_template}
            <div class="form-group">
                <label class="control-label col-lg-2">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="">{l s='Custom CSS' mod='roja45quotationspro'}
                    </span>
                </label>
                <div class="col-lg-7">
                    <textarea name="custom_css" id="custom_css" rows="10" class="">{$fields_value['custom_css']|escape:'html':'UTF-8'}</textarea>
                </div>
            </div>
            {/if}
        </div><!-- /.form-wrapper -->

        <div class="panel-footer">
            <button type="submit" class="btn btn-secondary btn-reset-template pull-right" title="{l s='Reset the forms back to the originals. *** WARNING *** All modifications for this template will be deleted.' mod='roja45quotationspro'}" name="submitResetroja45_quotationspro_answer"> {l s='RESET' mod='roja45quotationspro'}</button>
            <button type="submit" value="1" id="roja45_quotationspro_answer_form_submit_btn" name="submitAddroja45_quotationspro_answer" class="btn btn-secondary pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='roja45quotationspro'}
            </button>
            <button type="submit" value="1" id="roja45_quotationspro_answer_form_submit_btn" name="submitAddroja45_quotationspro_answerAndStay" class="btn btn-secondary pull-right">
                <i class="process-icon-save"></i> {l s='Save & Stay' mod='roja45quotationspro'}
            </button>
            <a class="btn btn-secondary" id="roja45_quotationspro_answer_form_cancel_btn" onclick="javascript:window.history.back();">
                <i class="process-icon-cancel"></i> {l s='Cancel' mod='roja45quotationspro'}
            </a>
            {if $email_template}
            <button type="button" data-type="text" data-element="text_template_" class="btn btn-secondary btn-preview-template pull-right" name="submitOptionsroja45_quotationspro_answer"><i class="process-icon-preview"></i> {l s='Preview TEXT' mod='roja45quotationspro'}</button>
            <button type="button" data-type="html" data-element="html_template_" class="btn btn-secondary btn-preview-template pull-right" name="submitOptionsroja45_quotationspro_answer"><i class="process-icon-preview"></i> {l s='Preview HTML' mod='roja45quotationspro'}</button>
            {else}
                <button type="button" data-type="pdf" data-element="html_template_" class="btn btn-secondary btn-preview-template pull-right" name="submitOptionsroja45_quotationspro_answer"><i class="process-icon-preview"></i> {l s='Preview PDF' mod='roja45quotationspro'}</button>
            {/if}

        </div>
    </div>
</form>

<div id="quotationspro_message_dialog" class="quotationspro_message_dialog quotationspro_dialog modal-dialog" style="display:none">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">{l s='Preview' mod='roja45quotationspro'}</h3>
            <span class="cross" title="{l s='Close window' mod='roja45quotationspro'}"></span>
        </div>
        <div class="modal-body">
            <div id="template_preview">
                <iframe id="preview_iframe"></iframe>
            </div>
        </div>
        <div class="modal-footer quotationspro_request buttons">
            <div class="button-container">
                <button type="button" id="ClosePreview"
                        class="btn btn-secondary btn-lg btn-close pull-left">
                    <i class="icon-remove"></i>
                    {l s='Close' mod='roja45quotationspro'}
                </button>
            </div>
        </div>
    </div>
</div>

<div id="roja45_quotation_modal">
    <div id="roja45_quotation_modal_dialog" class="roja45-quotation-modal-dialog">
        <div id="modal_wait_icon">
            <i class="icon-refresh icon-spin animated"></i>
            <p>{l s='Please Wait' mod='roja45quotationspro'}</p>
        </div>
    </div>
</div>
