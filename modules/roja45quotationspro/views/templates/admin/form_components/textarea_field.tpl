{*
* 2016 ROJA45.COM
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45 <support@roja45.com>
*  @copyright  		2016 roja45.com
*}

<div class="form-group">
    <label class="control-label col-lg-3 {if isset($required) && $required}required{/if}">
        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" {if isset($field_description)}data-original-title="{$field_description|escape:'html':'UTF-8'}"{/if}>{$field_label|escape:'html':'UTF-8'}</span>
    </label>
    {if isset($field_languages) && ($field_languages|@count gt 1)}
        {foreach $field_languages as $language}
            {if $field_languages|count > 1}
                <div class="translatable-field lang-{$language.id_lang|escape:'html':'UTF-8'}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
                <div class="col-lg-7">
            {/if}
            <textarea id="{if isset($id)}{$id|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                      name="{if isset($name)}{$name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}_{$language.id_lang|escape:'html':'UTF-8'}{/if}"
                      {if isset($input.class)}class="{$input.class|escape:'html':'UTF-8'}"{/if}
                     {if isset($rows)}rows="{$rows|escape:'html':'UTF-8'}"{/if}></textarea>
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
                <textarea
                        id="{if isset($id)}{$id|escape:'html':'UTF-8'}{if isset($defaultFormLanguage)}_{$defaultFormLanguage|escape:'html':'UTF-8'}{/if}{else}{$name|escape:'html':'UTF-8'}{if isset($defaultFormLanguage)}_{$defaultFormLanguage|escape:'html':'UTF-8'}{/if}{/if}"
                        name="{if isset($name)}{$name|escape:'html':'UTF-8'}{if isset($defaultFormLanguage)}_{$defaultFormLanguage|escape:'html':'UTF-8'}{/if}{else}{$id|escape:'html':'UTF-8'}_{$defaultFormLanguage|escape:'html':'UTF-8'}{/if}"
                        class="{if isset($input.class)} {$input.class|escape:'html':'UTF-8'}{/if}"
                        {if isset($rows)} rows="{$rows|escape:'html':'UTF-8'}"{/if}></textarea>
        </div>
    {/if}
</div>
