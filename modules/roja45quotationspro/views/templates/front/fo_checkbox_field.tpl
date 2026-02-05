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
    <div class="col-lg-12">
        <div class="checkbox custom-checkbox">
            <input type="checkbox"
                   class="form-control form-field {if !empty($required) && $required} is_required{/if}"
                   name="{$name|escape:'html':'UTF-8'}"
                   id="{$id|escape:'html':'UTF-8'}"
                    {if !empty($required) && $required} required="required" data-validate="isChecked"{/if}
                   value="1" {if isset($default) && $default == '1'}checked="checked"{/if}
                    {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}
            />
            <span class="checkbox"> {if $roja45quotationspro_iconpack=='2'}<i class="material-icons rtl-no-flip checkbox-checked"></i>{elseif ($roja45quotationspro_iconpack=='3')}<i class="fa fa-check rtl-no-flip checkbox-checked"></i>{elseif ($roja45quotationspro_iconpack=='1')}<i class="icon-check rtl-no-flip checkbox-checked"></i>{else}<i class="material-icons rtl-no-flip checkbox-checked"></i>{/if}</span>
            {$field_description|escape:'html':'UTF-8'}
        </div>
    </div>
</div>