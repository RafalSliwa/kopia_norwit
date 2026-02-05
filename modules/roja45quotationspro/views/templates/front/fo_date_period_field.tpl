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
        <div class="col-lg-6">
            <div class="date-field">
                {if $start_field_label}
                    <label class="control-label">{$start_field_label|escape:'html':'UTF-8'}</label>
                {/if}
                <div class="input-group">
                    <input id="{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}_start"
                           name="{$name|escape:'html':'UTF-8'}_start"
                           type="text"
                           data-hex="true"
                           data-format="{if isset($format) && $format}{$format|escape:'html':'UTF-8'}{else}dd/mm/yy{/if}"
                            {if isset($validationMethod)} data-validate="{$validationMethod|escape:'html':'UTF-8'}" {/if}
                           class="form-control multi-datepicker validate form-field"
                            {if isset($format) && $format} placeholder="({$format|escape:'html':'UTF-8'})"{else} placeholder="(dd/mm/yy)"{/if}
                            {if !empty($field_type) && $field_type} data-field-type="{$field_type|escape:'html':'UTF-8'}"{/if}
                           style="position: relative; z-index: 998;"/>
                    <span class="input-group-addon">
                        <svg viewBox="0 0 24 24"><path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20a2 2 0 0 0 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/></svg>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="date-field">
                {if $end_field_label}
                    <label class="control-label">{$end_field_label|escape:'html':'UTF-8'}</label>
                {/if}
                <div class="input-group">
                    <input id="{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}_end"
                           name="{$name|escape:'html':'UTF-8'}_end"
                           type="text"
                           data-hex="true"
                           data-format="{if isset($format) && $format}{$format|escape:'html':'UTF-8'}{else}dd/mm/yy{/if}"
                            {if isset($validationMethod)} data-validate="{$validationMethod|escape:'html':'UTF-8'}" {/if}
                           class="form-control multi-datepicker validate form-field"
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
        let start = $("#{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}_start");
        let end = $("#{if isset($id)}{$id|escape:'html':'UTF-8'}{else}{$name|escape:'html':'UTF-8'}{/if}_end");

        var format = start.attr('data-format');
        start.datepicker({
          prevText: '',
          nextText: '',
          dateFormat: format,
          onSelect: function (selectedDate, obj) {
            end.datepicker('option', 'minDate', selectedDate);
            end.datepicker('setDate', selectedDate);
          },
        });


        var format = end.attr('data-format');
        end.datepicker({
          prevText: '',
          nextText: '',
          dateFormat: format
        });
      }, false);
    </script>
</div>