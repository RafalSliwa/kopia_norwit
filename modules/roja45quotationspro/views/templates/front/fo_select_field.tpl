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

<div class="form-group selector1 {if isset($class)}{$class|escape:'html':'UTF-8'}_group{/if}">
    <label class="control-label {if isset($required) && $required}required{/if}" title="{$field_description|escape:'html':'UTF-8'}">
        {$field_label|escape:'html':'UTF-8'}
    </label>
    <div class="row">
        <div class="col-lg-12">
            {if $display_as==0}
            <select name="{$name|escape:'html':'UTF-8'}"
                    class="{if isset($class)}{$class|escape:'html':'UTF-8'}{/if} select-field form-control form-control-select form-field {if isset($required) && $required}is_required{/if}"
                    id="{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}"
                    {if isset($size)}size="{$size|escape:'html':'UTF-8'}"{/if}
                    {if isset($onchange)}onchange="{$onchange|escape:'html':'UTF-8'}"{/if}
                    {if !empty($required) && $required} required="required" {/if}
                    {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}>
                {if isset($options.default)}
                    <option data-id="{$options.default.value|escape:'html':'UTF-8'}" data-value="{$options.default.label|escape:'html':'UTF-8'}" value="{$options.default.value|escape:'html':'UTF-8'}">{$options.default.label|escape:'html':'UTF-8'}</option>
                {/if}
                {foreach $options AS $key=>$option}
                    {assign var='key' value=$key_options}
                    {if $option.$key == "-"}
                        <option value="">-</option>
                    {else}
                        <option data-id="{$option.$key|escape:'html':'UTF-8'}" data-value="{$option.$value_options|escape:'html':'UTF-8'}" value="{$option.$key|escape:'html':'UTF-8'}">{$option.$value_options|escape:'html':'UTF-8'}</option>
                    {/if}
                {/foreach}
            </select>
                {else}
                {foreach $options AS $index=>$option}
                    {assign var='key' value=$key_options}
                    <input type="radio"
                           id="{$name|escape:'html':'UTF-8'}_{$option.$key|escape:'html':'UTF-8'}"
                           name="{$name|escape:'html':'UTF-8'}"
                           value="{$option.$key|escape:'html':'UTF-8'}"
                            {if !empty($required) && $required} required="required" {/if}
                           {if $option@iteration == 1}checked="checked"{/if}
                           class="hidden-sm-up radio-label"
                           data-id="{$option.$key|escape:'html':'UTF-8'}">
                    <label for="{$name|escape:'html':'UTF-8'}_{$option.$key|escape:'html':'UTF-8'}"
                           class="btn btn-default btn-primary button-label">{$option.$value_options|escape:'html':'UTF-8'}</label>
                {/foreach}
            {/if}
        </div>
    </div>
</div>