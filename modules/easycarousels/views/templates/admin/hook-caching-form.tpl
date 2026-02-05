{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

<div class="panel clearfix">
	<div class="title-container">
		{l s='Caching settings for [1]%s[/1]' mod='easycarousels' sprintf=[$hook_name] tags=['<span class="b">']}
		<a href="#" class="icon-times hide-settings" title="{l s='Hide' mod='easycarousels'}"></a>
	</div>
	{if !empty($settings.blocked)}
		<div class="alert alert-warning nomargin">
			{l s='Some carousels in this hook can not be cached' mod='easycarousels'}.
			{l s='Please contact module developer for additional details' mod='easycarousels'}.
		</div>
	{else}
	<form action="" method="post" class="form-horizontal">
	<div class="form-group col-lg-12 main-caching-option">
		<label class="control-label col-lg-3">
			{l s='Activate caching for this hook' mod='easycarousels'}
		</label>
		<div class="col-lg-2">
			<select class="caching-options switch-select{if $settings.time} yes{/if}" name="settings[time]">
				<option value="0">{l s='No' mod='easycarousels'}</option>
				<option value="3600"{if $settings.time == 3600} selected{/if}>{l s='Yes - reset every hour' mod='easycarousels'}</option>
				<option value="21600"{if $settings.time == 21600} selected{/if}>{l s='Yes - reset every 6 hours' mod='easycarousels'}</option>
				<option value="43200"{if $settings.time == 43200} selected{/if}>{l s='Yes - reset every 12 hours' mod='easycarousels'}</option>
				<option value="86400"{if $settings.time == 86400} selected{/if}>{l s='Yes - reset daily (24h)' mod='easycarousels'}</option>
				<option value="604800"{if $settings.time == 604800} selected{/if}>{l s='Yes - reset weekly (168h)' mod='easycarousels'}</option>
				{*<option value="2592000"{if $settings.cache.time == 2592000} selected{/if}>Yes, reset every 30 Days (720 hours)</option>*}
			</select>
		</div>
		<div class="col-lg-7 caching-info{if !$caching_info || !$settings.time} hidden{/if}">
			<span class="grey-note inline-block">{$caching_info|escape:'html':'UTF-8'}</span>
			<button type="button" class="btn btn-default clearHookCache inline-block"><i class="icon-trash"></i> {l s='Clear cache' mod='easycarousels'}</button>
		</div>
	</div>
	<div class="form-group col-lg-12 related-caching-option{if !$settings.time} hidden{/if}">
		<label class="control-label col-lg-3">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Set YES, if taxes/discounts can be different depending on country' mod='easycarousels'}">{l s='Different cache for each country' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<select class="switch-select{if $settings.country} yes{/if}" name="settings[country]">
				<option value="0">{l s='No' mod='easycarousels'}</option>
				<option value="1"{if $settings.country} selected{/if}>{l s='Yes' mod='easycarousels'}</option>
			</select>
		</div>
	</div>
	<div class="form-group col-lg-12 related-caching-option{if !$settings.time} hidden{/if}">
		<label class="control-label col-lg-3">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Set YES, if taxes/discounts can be different depending on user group' mod='easycarousels'}">{l s='Different cache for each user group' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<select class="switch-select{if $settings.group} yes{/if}" name="settings[group]">
				<option value="0">{l s='No' mod='easycarousels'}</option>
				<option value="1"{if $settings.group} selected{/if}>{l s='Yes' mod='easycarousels'}</option>
			</select>
		</div>
	</div>
	{foreach $settings.check_ids as $obj_type => $value}
		<input type="hidden" name="settings[check_ids][{$obj_type|escape:'html':'UTF-8'}]" value="{$value|intval}">
	{/foreach}
	<input type="hidden" name="settings[adjust_required]" value="1">
	<div class="p-footer clearfix">
		<input type="hidden" name="hook_name" value="{$hook_name|escape:'html':'UTF-8'}">
		<input type="hidden" name="settings_type" value="{$settings_type|escape:'html':'UTF-8'}">
		<button class="saveHookSettings btn btn-default">
			<i class="process-icon-save"></i>
			{l s='Save' mod='easycarousels'}
		</button>
	</div>
	</form>
	{/if}
</div>
{* since 2.7.7 *}
