{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{$id_banner = $banner.id_banner}
<div class="cb-item clearfix{if !empty($full)} full{/if}" data-id="{$id_banner|intval}">
	<form method="post" action="" class="form-horizontal">
	<div class="cb-header clearfix">
		<input type="checkbox" value="{$id_banner|intval}" class="cb-box" title="{$id_banner|intval}">
		<span class="cb-name">
			<span class="cb-preview">
				{if !empty($banner.img_preview)}
					<span style="background-image:url({$banner.img_preview|escape:'html':'UTF-8'})"></span>
				{else}
					<span>{if !empty($banner.html_preview)}HTML{/if}</span>
				{/if}
			</span>
			<span class="cb-label">
				<input type="text" name="data[label]" value="{$banner.label|escape:'html':'UTF-8'}" placeholder="{l s='Banner name' mod='custombanners'}">
				<span class="txt">{$banner.label|escape:'html':'UTF-8'}</span>
			</span>
			{if !empty($banner.exc_note)}<span class="exc-note">{$banner.exc_note|escape:'html':'UTF-8'}</span>{/if}
		</span>
		<span class="actions">
			{if $banner.days_before_publish > 0}
				<span class="pub-note alert-info">{l s='Will be published in %d day(s)' mod='custombanners' sprintf=$banner.days_before_publish}</span>
			{else if $banner.days_expired > 0}
				<span class="pub-note alert-warning">{l s='Expired %d day(s) ago' mod='custombanners' sprintf=$banner.days_expired}</span>
			{/if}
			{foreach $device_types as $device_type => $colum_name}
				<div class="status{if $banner.$colum_name} active{/if} inline-block for-{$device_type|escape:'html':'UTF-8'}">
					<label class="icon-{$device_type|escape:'html':'UTF-8'}" title="{$device_type|escape:'html':'UTF-8'}">
						<input type="checkbox" name="data[{$colum_name|escape:'html':'UTF-8'}]" data-param="{$colum_name|escape:'html':'UTF-8'}" value="1" class="toggleable_param hidden"{if $banner.$colum_name} checked{/if}>
					</label>
				</div>
			{/foreach}
			<i class="dragger act icon icon-arrows-v icon-2x"></i>
			<div class="btn-group pull-right">
				<button type="button" title="{l s='Edit' mod='custombanners'}" class="editBanner btn btn-default">
					<i class="icon-pencil"></i> {l s='Edit' mod='custombanners'}
				</button>
				<button type="button" title="{l s='Scroll Up' mod='custombanners'}" class="scrollUp btn btn-default">
					<i class="icon icon-minus"></i> {l s='Cancel' mod='custombanners'}
				</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<i class="icon-caret-down"></i>
				</button>
				<ul class="dropdown-menu">
					<li class="dont-hide">
						<div class="toggle-hook-list dropdown-action"><i class="icon-copy"></i> {l s='Copy to hook' mod='custombanners'}</div>
						<div class="dynamic-hook-list" style="display:none;">
							<button type="button" class="btn btn-default copyToAnotherHook">{l s='OK' mod='custombanners'}</button>
						</div>
					</li>
					<li class="dont-hide">
						<div class="toggle-hook-list dropdown-action"><i class="icon-arrow-left"></i> {l s='Move to hook' mod='custombanners'}</div>
						<div class="dynamic-hook-list" style="display:none;">
							<button type="button" class="btn btn-default moveToAnotherHook">{l s='OK' mod='custombanners'}</button>
						</div>
					</li>
					<li>
						<div class="deleteBanner dropdown-action">
							<i class="icon icon-trash"></i>
							{l s='Delete' mod='custombanners'}
						</div>
					</li>
				</ul>
			</div>
		</span>
	</div>
	{if !empty($full)}
	<div class="cb-details{if !empty($banner.content.img)} show-img-fields{/if}" style="display:none;">
		<div class="ajax-errors alert alert-danger" style="display:none"></div>
		{foreach $input_fields as $f_key => $field}
		{$multilang = !empty($field.multilang)}
		<div class="form-group {$f_key|escape:'html':'UTF-8'}{if !empty($field.group_class)} {$field.group_class|escape:'html':'UTF-8'}{/if}{if $multilang && !isset($banner.content.$f_key)} empty{/if}">
			<label class="control-label col-lg-1">
				<span{if isset($field.tooltip)} class="label-tooltip" data-toggle="tooltip" title="{$field.tooltip|escape:'html':'UTF-8'}"{/if}>
					{$field.label|escape:'html':'UTF-8'}
				</span>
				{if $multilang}
					<a href="#" class="show-field" title="{l s='Add' mod='custombanners'}"><i class="icon-plus"></i></a>
				{/if}
			</label>
			<div class="col-lg-10 clearfix">
				{if $multilang}
					{foreach $languages as $lang}
						{$id_lang = $lang.id_lang}
						<div class="multilang lang-{$id_lang|intval}" data-lang="{$id_lang|intval}" style="{if $id_lang != $id_lang_current}display: none;{/if}">
							{$value = ''}{if isset($banner.content.$f_key.$id_lang)}{$value = $banner.content.$f_key.$id_lang}{/if}
							{include file="./input.tpl" field=$field name="data[content][$id_lang][$f_key]" value=$value}
						</div>
					{/foreach}
				{else}
					{$value = ''}{if isset($banner.$f_key)}{$value = $banner.$f_key}{/if}
					{include file="./input.tpl" field=$field name="data[$f_key]" value=$value}
				{/if}
			</div>
			<div class="col-lg-1">
				{if $multilang}
					<div class="cb-langs">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							{foreach $languages as $lang}
								<span class="multilang lang-{$lang.id_lang|intval}" style="{if $lang.id_lang != $id_lang_current}display:none;{/if}">{$lang.iso_code|escape:'html':'UTF-8'}</span>
							{/foreach}
							<i class="icon-caret-down"></i>
						</button>
						<ul class="dropdown-menu">
							{foreach $languages as $lang}
							<li>
								<a href="#" onclick="event.preventDefault(); selectLanguage($(this), {$lang.id_lang|intval})">
									{$lang.name|escape:'html':'UTF-8'}
								</a>
							</li>
							{/foreach}
						</ul>
					</div>
					<i class="icon-times hide-field act" title="{l s='Remove' mod='custombanners'}"></i>
				{else if !empty($field.type == 'date')}
					<a href="#" class="clear-date {$f_key|escape:'html':'UTF-8'} hidden">
					<i class="icon-eraser"></i> {l s='Clear date' mod='custombanners'}</a>
				{/if}
			</div>
		</div>
		{/foreach}
		<div class="p-footer">
			{foreach ['id_banner', 'id_wrapper', 'hook_name', 'position'] as $key}
				<input type="hidden" name="data[{$key|escape:'html':'UTF-8'}]" value="{$banner[$key]|escape:'html':'UTF-8'}">
			{/foreach}
			<button type="button" class="saveBanner btn btn-default">
				<i class="process-icon-save"></i>
				{l s='Save' mod='custombanners'}
			</button>
			<label class="label-text">{l s='Copy data to other languages' mod='custombanners'}:</label>
			{foreach $input_fields as $name => $field}
				{if !empty($field.multilang)}
				<label class="label-checkbox{if !empty($field.group_class)} {$field.group_class|escape:'html':'UTF-8'}{/if}">
					<input type="checkbox" name="{if in_array($name, $cb->img_fields)}img_{/if}lang_source[{$name|escape:'html':'UTF-8'}]" value="{$id_lang_current|intval}" class="lang-source">
					{$field.label|escape:'html':'UTF-8'}
				</label>
				{/if}
			{/foreach}
			<label class="label-checkbox"><input type="checkbox" class="check-all-data"> {l s='All' mod='custombanners'}</label>
			{if !empty($multishop_note)}
				<div class="alert-info multishop-note">{l s='NOTE: Changes will be saved for more than one shop' mod='custombanners'}</div>
			{/if}
		</div>
	</div>
	{/if}
	</form>
	{* for quick search *}
	<input type="hidden" class="qs-exc-type" value="{if !empty($banner.exceptions.page.type)}{$banner.exceptions.page.type|escape:'html':'UTF-8'}{/if}">
	<input type="hidden" class="qs-exc-ids" value="{if !empty($banner.exceptions.page.ids)}{$banner.exceptions.page.ids|escape:'html':'UTF-8'}{/if}">
</div>
{* since 3.0.1 *}
