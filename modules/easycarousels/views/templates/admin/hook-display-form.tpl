{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

<div class="panel clearfix">
	<div class="title-container">
		{l s='Display settings for [1]%s[/1]' mod='easycarousels' sprintf=[$hook_name] tags=['<span class="b">']}
		<a href="#" class="icon-times hide-settings" title="{l s='Hide' mod='easycarousels'}"></a>
	</div>
	<form action="" method="post" class="form-horizontal">
	<div class="form-group col-lg-12">
		<label class="control-label col-lg-3" for="custom_class">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Custom class that will be applied to container holding all carousels in this hook' mod='easycarousels'}">{l s='Container class' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<input id="custom_class" type="text" name="settings[custom_class]" value="{$settings.custom_class|escape:'html':'UTF-8'}">
		</div>
	</div>
	<div class="form-group col-lg-12">
		<label class="control-label col-lg-3">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='If tab names overlap container, they will be dynamically transformed to a compact dropdown list' mod='easycarousels'}">{l s='Compact tabs' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<select class="switch-select{if $settings.compact_tabs} yes{/if}" name="settings[compact_tabs]">
				<option value="0"{if !$settings.compact_tabs} selected{/if}>{l s='No' mod='easycarousels'}</option>
				<option value="1"{if $settings.compact_tabs} selected{/if}>{l s='Yes' mod='easycarousels'}</option>
			</select>
		</div>
	</div>
	<div class="form-group col-lg-12">
		<label class="control-label col-lg-3">
			<span class="label-tooltip" data-toggle="tooltip" title="{l s='Load carousels dynamically after all other site contents have been loaded' mod='easycarousels'}">{l s='Dynamic load' mod='easycarousels'}</span>
		</label>
		<div class="col-lg-2">
			<select class="switch-select reverse{if !$settings.instant_load} yes{/if}" name="settings[instant_load]">
				<option value="1"{if $settings.instant_load} selected{/if}>{l s='No' mod='easycarousels'}</option>
				<option value="0"{if !$settings.instant_load} selected{/if}>{l s='Yes' mod='easycarousels'}</option>
			</select>
		</div>
	</div>
	<div class="p-footer clearfix">
		<input type="hidden" name="hook_name" value="{$hook_name|escape:'html':'UTF-8'}">
		<input type="hidden" name="settings_type" value="{$settings_type|escape:'html':'UTF-8'}">
		<button class="saveHookSettings btn btn-default">
			<i class="process-icon-save"></i>
			{l s='Save' mod='easycarousels'}
		</button>
	</div>
	</form>
</div>
{* since 2.7.3 *}
