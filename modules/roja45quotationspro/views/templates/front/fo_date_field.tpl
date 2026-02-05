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
            <div class="date-field">
                <div class="input-group">
                    <input id="{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}"
                           name="{$name|escape:'html':'UTF-8'}"
                           type="text"
                           data-hex="true"
                           data-format="{if isset($format) && $format}{$format|escape:'html':'UTF-8'}{else}dd/mm/yy{/if}"
                            {if isset($validationMethod)} data-validate="{$validationMethod|escape:'html':'UTF-8'}" {/if}
                           class="form-control single-datepicker validate form-field {if !empty($class)}{$class|escape:'html':'UTF-8'}{/if}"
                            {if isset($format) && $format} placeholder="({$format|escape:'html':'UTF-8'})"{else} placeholder="(dd/mm/yy)"{/if}
                            {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}
                           style="position: relative; z-index: 998;"/>
                    <span class="input-group-addon">
                         <svg viewBox="0 0 24 24"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
      window.addEventListener('load', function () {

        let dp = $("#{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}");
        var format = dp.attr('data-format');

        dp.datepicker({
          prevText: '',
          nextText: '',
          dateFormat: format
        });

        {if $class=='datepicker_date_start'}
        dp.datepicker("option", "onSelect", function(selectedDate) {
          let id = "#{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}";
          id = id.replace('datepicker_start_date', 'datepicker_end_date');
          let end_date = $(id);
          end_date.datepicker('option', 'minDate', selectedDate);
          end_date.datepicker('setDate', selectedDate);
        });
        {elseif $class=='datepicker_date_end'}

        {else}

        {/if}
      }, false);
    </script>
</div>