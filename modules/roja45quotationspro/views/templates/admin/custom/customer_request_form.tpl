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

{if (isset($request_data))}
<table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse" cellSpacing=0 cellPadding=0 border=0>
    <tr>
        {if (count($request_data->columns))}
            {assign var="width" value=100/$request_data->columns|@count}
            {else}
            {assign var="width" value=100}
        {/if}

{foreach $request_data->columns as $column}
        <td width="{$width}%" style="vertical-align:top;">
    {if isset($column->heading) && $column->heading}
    <table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse" cellSpacing=0 cellPadding=0 border=0>
        <tr>
            <td class="text-right" style="width:100%;border:1px solid #D6D4D4;border-bottom: 0;background-color: #fbfbfb;padding: 5px;">
                <span class="title" style="font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-weight:500;font-size:15px;color: #444444;text-transform:uppercase;line-height:20px;">{$column->heading|escape:'html':'UTF-8'}</span>
            </td>
        </tr>
    </table>
    {/if}
    {foreach $column->fields as $field}
    <table class="table table-recap" bgcolor="#ffffff" style="width:100%;border-collapse:collapse" cellSpacing=0 cellPadding=0 border=0>
        <tr>
            <td colspan=1 class="text-right" style="width:40%;border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #fbfbfb;padding: 5px;">
                <span style="font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-weight:500;font-size:13px;color: #444444;text-transform:uppercase;">{$field->label|escape:'html':'UTF-8'}</span>
            </td>
            {if isset($field->type) && ($field->type=='CHECKBOX')}
            <td colspan=1 class="text-right" style="width:60%;border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #ffffff;padding: 5px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{if isset($field->value) && ($field->value == "1")}{l s='CHECKED' mod='roja45quotationspro'}{else}{l s='UNCHECKED' mod='roja45quotationspro'}{/if}</span>
            </td>
            {elseif isset($field->type) && ($field->type=='SWITCH')}
            <td colspan=1 class="text-right" style="width:60%;border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #ffffff;padding: 5px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{if isset($field->value) && ($field->value == "1")}{l s='SELECTED' mod='roja45quotationspro'}{else}{l s='UNSELECTED' mod='roja45quotationspro'}{/if}</span>
            </td>
            {else}
            <td colspan=1 class="text-right" style="width:60%;border:0px solid #D6D4D4;border-bottom: 1px solid #d6d4d4;background-color: #ffffff;padding: 5px;">
                <span style="color: #444444;font-family: Helvetica, 'Open Sans', Arial, sans-serif;font-size: 13px;">{if isset($field->value)}{$field->value|escape:'html':'UTF-8'}{/if}</span>
            </td>
            {/if}
        </tr>
    </table>
    {/foreach}
        </td>
{/foreach}
    </tr>
</table>
{/if}
