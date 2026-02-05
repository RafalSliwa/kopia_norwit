{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

{foreach $languages as $lang}
	<div class="multilang lang_{$lang.id_lang|intval}{if $lang.id_lang != $id_lang_current} hidden{/if}">
		{$single_lang_field = $field}
		{$single_lang_field.value = ''}{if isset($field.value[$lang.id_lang])}{$single_lang_field.value = $field.value[$lang.id_lang]}{/if}
		{$single_lang_field.input_name = "multilang[`$lang.id_lang`][`$field_name`]"}
		{include file="./input.tpl" field = $single_lang_field}
	</div>
{/foreach}
<div class="languages pull-right">
	<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		{foreach $languages as $lang}
			<span class="multilang lang_{$lang.id_lang|intval}{if $lang.id_lang != $id_lang_current} hidden{/if}">{$lang.iso_code|escape:'html':'UTF-8'}</span>
		{/foreach}
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu">
		{foreach $languages as $lang}
			<li><a href="#" class="lang_switcher" data-id-lang="{$lang.id_lang|intval}">{$lang.name|escape:'html':'UTF-8'}</a></li>
		{/foreach}
	</ul>
</div>
{* since 2.7.3 *}
