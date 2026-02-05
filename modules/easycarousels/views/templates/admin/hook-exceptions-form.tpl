{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

<div class="panel clearfix">
	<form action="" method="post">
	<div class="exc-settings">
		<label class="inline-label">
			{l s='Display this hook' mod='easycarousels'}
		</label>
		<div class="inline-block">
			<select name="exceptions_type">
				<option value="1">{l s='on all pages except checked' mod='easycarousels'}</option>
				<option value="2"{if $settings.type == 2} selected{/if}>{l s='only on checked pages' mod='easycarousels'}</option>
			</select>
		</div>
		<a href="#" class="chk-action checkall">{l s='Check all' mod='easycarousels'}</a>
		<a href="#" class="chk-action uncheckall">{l s='Unheck all' mod='easycarousels'}</a>
		<a href="#" class="chk-action invert">{l s='Invert selection' mod='easycarousels'}</a>
	</div>
	<div class="exc-list">
		{foreach $settings.exceptions as $exc_group}
			<div class="exc-group clearfix">
				<h4>{$exc_group.group_name|escape:'html':'UTF-8'}</h4>
				{foreach $exc_group.values key=controller item=checked}
					<label class="label-checkbox col-lg-3" title="{$controller|escape:'html':'UTF-8'}">
						<input type="checkbox" name="exceptions[]" value="{$controller|escape:'html':'UTF-8'}"{if $checked} checked="checked"{/if}> {$controller|escape:'html':'UTF-8'}
					</label>
				{/foreach}
			</div>
		{/foreach}
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
{* since 2.5.0 *}
