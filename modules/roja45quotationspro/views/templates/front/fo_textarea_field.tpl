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
            <div class="textarea">
                <textarea id="{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}"
                          name="{if isset($name)}{$name|escape:'html':'UTF-8'}{else}{$id|escape:'html':'UTF-8'}{/if}"
                          class="{if isset($class)}{$class|escape:'html':'UTF-8'}{/if}{if isset($required) && $required} is_required{/if}{if isset($validationMethod)} validate{/if} form-control form-field"
                          {if isset($rows)} rows="{$rows|escape:'html':'UTF-8'}"{/if}
                          {if isset($placeholder) && $placeholder} placeholder="{$placeholder|escape:'html':'UTF-8'}"{/if}
                        {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}></textarea>
            </div>
        </div>
    </div>
</div>