{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

{if $field.type == 'mce'}
	<textarea name="{$field.input_name|escape:'html':'UTF-8'}" class="mce">{$field.value}{* can not be escaped *}</textarea>
{else}
	<input type="text" name="{$field.input_name|escape:'html':'UTF-8'}" value="{$field.value|escape:'html':'UTF-8'}"{if !empty($field.input_class)} class="{$field.input_class|escape:'html':'UTF-8'}"{/if}>
{/if}
{* since 2.7.3 *}
