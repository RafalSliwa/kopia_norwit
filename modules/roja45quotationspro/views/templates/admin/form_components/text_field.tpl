{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author          Roja45 <support@roja45.com>
*  @copyright       2016 roja45.com
*}

<div class="form-group">
    <label class="control-label col-lg-3 {if isset($required) && $required}required{/if}">
        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{$field_label|escape:'html':'UTF-8'}">{$field_label|escape:'html':'UTF-8'}</span>
    </label>

    {if isset($field_languages) && ($field_languages|@count gt 1)}
        {foreach $field_languages as $language}
            {if $field_languages|count > 1}
                <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                <div class="col-lg-7">
            {/if}
        {if isset($maxchar) || isset($prefix) || isset($suffix)}
            <div class="input-group{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}">
        {/if}
            {if isset($maxchar)}
                <span id="{if isset($id)}{$id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
                <span class="text-count-down">{$maxchar|escape:'html':'UTF-8'}</span>
                </span>
            {/if}
            {if isset($prefix)}
                <span class="input-group-addon">{$prefix|escape:'html':'UTF-8'}</span>
            {/if}
            <input type="text"
                   id="{if isset($id)}{$id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                   name="{if isset($name)}{$name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                   class="{if isset($class)}{$class|escape:'html':'UTF-8'}{/if} {if isset($required) && $required}is_required validate form-control{/if}"
                   {if isset($onkeyup)}onkeyup="{$onkeyup|escape:'html':'UTF-8'}"{/if}
                   {if isset($onfocusout)}onfocusout="{$onfocusout|escape:'html':'UTF-8'}"{/if}
                    {if isset($size)}size="{$size|escape:'html':'UTF-8'}"{/if}
                    {if isset($maxlength)} maxlength="{$maxlength|escape:'html':'UTF-8'}"{/if}
                    {if isset($required) && $required} required="required" {/if}
                    {if isset($validationMethod)} data-validate="{$validationMethod|escape:'html':'UTF-8'}" {/if}
                    {if isset($placeholder) && $placeholder} placeholder="{$placeholder|escape:'html':'UTF-8'}"{/if}
                    {if isset($language)}data-iso-code="{$language.iso_code|escape:'html':'UTF-8'}"{/if}
                    {if isset($default_value)}value="{$default_value|escape:'html':'UTF-8'}"{/if}
            />
            {if isset($text_input.suffix)}
                <span class="input-group-addon">{$text_input.suffix|escape:'html':'UTF-8'}</span>
            {/if}
        {if isset($text_input.maxchar) || isset($text_input.prefix) || isset($text_input.suffix)}
            </div>
        {/if}
            {if $field_languages|count > 1}
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-secondary dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                        {$language.iso_code|escape:'html':'UTF-8'}
                        <i class="icon-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$field_languages item=language}
                            <li><a href="javascript:hideOtherLanguage({$language.id_lang|escape:'html':'UTF-8'});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                        {/foreach}
                    </ul>
                </div>
                </div>
            {/if}
        {/foreach}
    {else}
        <div class="col-lg-7">
            {if isset($maxchar) || isset($prefix) || isset($suffix)}
            <div class="input-group{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}">
                {/if}
                {if isset($maxchar)}
                    <span id="{if isset($id)}{$id|escape:'html':'UTF-8'}_{$default_lang|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}_{$default_lang|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
                <span class="text-count-down">{$maxchar|escape:'html':'UTF-8'}</span>
            </span>
                {/if}
                {if isset($prefix)}
                    <span class="input-group-addon">{$prefix|escape:'html':'UTF-8'}</span>
                {/if}
                <input type="text"
                       id="{if isset($id)}{$id|escape:'html':'UTF-8'}{if isset($default_lang)}_{$default_lang|escape:'html':'UTF-8'}{/if}{else}{$name|escape:'html':'UTF-8'}{if isset($default_lang)}_{$default_lang|escape:'html':'UTF-8'}{/if}{/if}"
                       name="{if isset($name)}{$name|escape:'html':'UTF-8'}{if isset($default_lang)}_{$default_lang|escape:'html':'UTF-8'}{/if}{else}{$id|escape:'html':'UTF-8'}_{$default_lang|escape:'html':'UTF-8'}{/if}"
                       class="{if isset($class)}{$class|escape:'html':'UTF-8'}{/if} {if isset($required) && $required}is_required validate form-control{/if}"
                       {if isset($onkeyup)}onkeyup="{$onkeyup|escape:'html':'UTF-8'}{/if}"
                       {if isset($onfocusout)}onfocusout="{$onfocusout|escape:'html':'UTF-8'}{/if}"
                        {if isset($size)} size="{$size|escape:'html':'UTF-8'}"{/if}
                        {if isset($maxlength)} maxlength="{$maxlength|escape:'html':'UTF-8'}"{/if}
                        {if isset($required) && $required} required="required" {/if}
                        {if isset($validationMethod)} data-validate="{$validationMethod|escape:'html':'UTF-8'}" {/if}
                        {if isset($placeholder) && $placeholder} placeholder="{$placeholder|escape:'html':'UTF-8'}"{/if}
                        {if isset($default_value)}value="{$default_value|escape:'html':'UTF-8'}"{/if}
                />
                {if isset($text_input.suffix)}
                    <span class="input-group-addon">{$text_input.suffix|escape:'html':'UTF-8'}</span>
                {/if}
                {if isset($text_input.maxchar) || isset($text_input.prefix) || isset($text_input.suffix)}
            </div>
            {/if}
        </div>
    {/if}

    <div class="col-lg-2">
        <span class="input-group-addon" data-toggle="tooltip" data-html="true" title="" style="display: none;">{l s='Already In Use!' mod='roja45quotationspro'}</span>
    </div>
</div>