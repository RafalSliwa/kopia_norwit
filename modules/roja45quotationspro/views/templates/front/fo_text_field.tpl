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

<div class="form-group {if isset($class)}{$class|escape:'html':'UTF-8'}_group{/if}">
    <label class="control-label {if isset($required) && $required}required{/if}" title="{$placeholder|escape:'html':'UTF-8'}">
        {$field_label|escape:'html':'UTF-8'}
    </label>
    <div class="row">
        <div class="col-lg-12">
            {if isset($maxchar) || isset($prefix) || isset($suffix)}
            <div class="input-group{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}">
                {/if}
                {if isset($maxchar)}
                    <span id="{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}_counter" class="input-group-addon">
                    <span class="text-count-down">{$maxchar|escape:'html':'UTF-8'}</span>
                </span>
                {/if}
                {if isset($prefix)}
                    <span class="input-group-addon">{$prefix|escape:'html':'UTF-8'}</span>
                {/if}
                <input  type="text"
                        id="{if !empty($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}"
                        name="{if !empty($name)}{$name|escape:'html':'UTF-8'}{else}{$id|escape:'html':'UTF-8'}{/if}"
                        class="{if !empty($class)}{$class|escape:'html':'UTF-8'}{/if}{if !empty($required) && $required} is_required{/if}{if !empty($validationMethod)} validate{/if} form-control form-field"
                        value="{if isset($field_values) && isset($field_values[$name])}{$field_values[$name]|escape:'html':'UTF-8'}{/if}"
                        {if !empty($disabled)}disabled="disabled"{/if}
                        {if !empty($readonly)}readonly="readonly"{/if}
                        {if !empty($onkeyup)}onkeyup="{$onkeyup|escape:'html':'UTF-8'}"{/if}
                        {if !empty($size)} size="{$size|escape:'html':'UTF-8'}"{/if}
                        {if !empty($maxlength)} maxlength="{$maxlength|escape:'html':'UTF-8'}"{/if}
                        {if !empty($required) && $required} required="required" {/if}
                        {if !empty($validationMethod)} data-validate="{$validationMethod|escape:'html':'UTF-8'}" {/if}
                        {if !empty($customregex)} data-custom-regex="{$customregex|escape:'html':'UTF-8'}" {/if}
                        {if !empty($placeholder) && $placeholder} placeholder="{$placeholder|escape:'html':'UTF-8'}"{/if}
                        {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}
                />
                {if isset($suffix)}
                    <span class="input-group-addon">{$suffix|escape:'html':'UTF-8'}</span>
                {/if}
                {if isset($maxchar) || isset($prefix) || isset($suffix)}
            </div>
            {/if}
        </div>
    </div>
</div>