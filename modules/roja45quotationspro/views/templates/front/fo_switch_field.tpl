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
    <label class="control-label {if isset($required) && $required}required{/if}" title="{$field_description|escape:'html':'UTF-8'}">
        {$field_label|escape:'html':'UTF-8'}
    </label>
    <div class="row">
        <div class="col-lg-12">
            <div class="checkbox">
                <span class="switch prestashop-switch form-control-valign">
                    <label class="radio-inline"
                           for="{$name|escape:'html':'UTF-8'}_on">
                        <span class="custom-radio">
                            <input type="radio"
                                   name="{$name|escape:'html':'UTF-8'}"
                                   id="{$id|escape:'html':'UTF-8'}_on"
                                   value="1"{if $default == 1}
                                   class="{if isset($class)}{$class|escape:'html':'UTF-8'}{/if}{if isset($required) && $required} is_required{/if}{if isset($validationMethod)} validate{/if} form-control"
                                   checked="checked"{/if}
                                    {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}/>
                            <span></span>
                        </span>
                        {l s='Yes' mod='roja45quotationspro'}
                    </label>
                    <label class="radio-inline"
                           for="{$name|escape:'html':'UTF-8'}_off">
                        <span class="custom-radio">
                            <input type="radio"
                                   name="{$name|escape:'html':'UTF-8'}"
                                   id="{$id|escape:'html':'UTF-8'}_off"
                                   value="0"{if $default == 0}
                                    class="{if isset($class)}{$class|escape:'html':'UTF-8'}{/if}{if isset($required) && $required} is_required{/if}{if isset($validationMethod)} validate{/if} form-control form-field"
                                    checked="checked"{/if}
                                    {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}/>
                            <span></span>
                        </span>
                        {l s='No' mod='roja45quotationspro'}
                    </label>
                </span>
            </div>
        </div>
    </div>
</div>