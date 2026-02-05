{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

{$id_carousel = $carousel.id_carousel}
<div class="c-item clearfix" data-id="{$id_carousel|intval}">
<form method="post" action="" class="form-horizontal desktop" data-device="desktop">
	<div class="carousel-header clearfix">
		<input type="checkbox" value="{$id_carousel|intval}" class="carousel-box" title="{l s='Bulk actions' mod='easycarousels'}">
		<span class="carousel-name">
			{if empty($carousel.name)}{$ec->getCarouselName($carousel.type)|escape:'html':'UTF-8'}{else}{$carousel.name|escape:'html':'UTF-8'}{/if}
			{if !empty($carousel.exc_note)}<span class="exc-note">{$carousel.exc_note|escape:'html':'UTF-8'}</span>{/if}
		</span>
		<span class="actions pull-right">
			<label class="label-checkbox">
				<input type="checkbox" name="in_tabs" value="1" class="toggleable_param"{if $carousel.in_tabs} checked{/if}>
				{l s='Grouped in tabs' mod='easycarousels'}
			</label>
			<label class="activateCarousel list-action-enable action-{if $carousel.active == 1}enabled{else}disabled{/if}">
				<i class="icon-check"></i>
				<i class="icon-remove"></i>
				<input type="checkbox" name="active" value="1" class="toggleable_param hidden"{if $carousel.active} checked{/if}>
			</label>
			<div class="btn-group pull-right">
				<button type="button" class="editCarousel btn btn-default" data-id="{$id_carousel|intval}">
					<span class="hidden-on-open"><i class="icon-pencil"></i> {l s='Edit' mod='easycarousels'}</span>
					<span class="hidden-on-closed"><i class="icon icon-minus"></i> {l s='Cancel' mod='easycarousels'}</span>
				</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<i class="icon-caret-down"></i>
				</button>
				<ul class="dropdown-menu">
					<li><a  href="#" class="deleteCarousel"><i class="icon icon-trash"></i> {l s='Delete' mod='easycarousels'}</a></li>
				</ul>
				<i class="dragger icon icon-arrows-v icon-2x"></i>
			</div>
		</span>
	</div>
	{if $full}
	<div class="carousel-details" style="display:none;">
		<div class="ajax_errors" style="display:none;"></div>
		<div class="col-lg-4">
			<div class="form-group">
				<label class="control-label col-lg-6" for="carousel_type">
					{l s='Carousel type' mod='easycarousels'}
				</label>
				<div class="col-lg-6">
					<select name="type" id="carousel_type">
						{foreach $type_names as $group_name => $options}
						<optgroup label="{$group_name|escape:'html':'UTF-8'}">
							{foreach $options as $input_name => $display_name}
								<option value="{$input_name|escape:'html':'UTF-8'}"{if $carousel.type == $input_name} selected{/if}>
									{$display_name|escape:'html':'UTF-8'}
								</option>
							{/foreach}
						</optgroup>
						{/foreach}
					</select>
				</div>
			</div>
		</div>

		{foreach $multilang_fields as $field_name => $field}
			<div class="col-lg-{$field.cols.wrapper|intval}">
				<div class="form-group">
					<label class="control-label col-lg-{$field.cols.label|intval}">
						<span{if $field.tooltip} class="label-tooltip" data-toggle="tooltip" title="{$field.tooltip|escape:'html':'UTF-8'}"{/if}>
							{$field.display_name|escape:'html':'UTF-8'}
						</span>
					</label>
					<div class="col-lg-{$field.cols.input|intval} has-lang-selector">
						{if !$id_carousel && $field_name == 'name'}{$field.input_class = 'name-not-saved'}{/if}
						{include file="./multilang-input.tpl"}
					</div>
				</div>
			</div>
		{/foreach}

		{function renderSettings s_type='' s_name='' rows=6 cols=3}
		{$multiple_devices = isset($multidevice_settings.$s_type)}
		<div class="{$s_type|escape:'html':'UTF-8'}-settings">
			{if $s_name}
				<div class="col-lg-12">
					<h4 class="col-lg-10 col-lg-offset-2 settings-title">
						<span class="txt">{$s_name|escape:'html':'UTF-8'}</span>
						{if $multiple_devices}
							{foreach $device_types as $d_type => $name}<a href="#" class="device-type {$d_type|escape:'html':'UTF-8'}" title="{$name|escape:'html':'UTF-8'}" data-type="{$d_type|escape:'html':'UTF-8'}"><i class="icon-{$d_type|escape:'html':'UTF-8'}"></i></a>{/foreach}
						{/if}
					</h4>
				</div>
			{/if}
			{$last = $cols - 1}
			{for $col = 0 to $last}
				{if $col != $last}
					{$column_fields = array_slice($fields[$s_type], $col*$rows, $rows)}
				{else}  {* make sure all remaining options are included in last column *}
					{$column_fields = array_slice($fields[$s_type], $col*$rows)}
				{/if}
				{if $column_fields}
				<div class="col-lg-{12/$cols|intval} f-col">
				{foreach $column_fields as $k => $field}
					{if $s_type == 'carousel' && $col > 1}{$grid = [10,2]}{else if $s_type == 'exceptions'}{$grid = [2,10]}{else}{$grid = [6,6]}{/if}
					{if !empty($field.separator)}
						<div class="form-group{if isset($field.class)} {$field.class|escape:'html':'UTF-8'}{/if}"><h4 class="field-separator col-lg-offset-{$grid[1]|intval}">{$field.separator|escape:'html':'UTF-8'}:</h4></div>
					{/if}
					<div class="form-group{if isset($field.class)} {$field.class|escape:'html':'UTF-8'}{/if}">
						<label class="control-label col-lg-{$grid[0]|intval}">
							<span{if isset($field.tooltip)} class="label-tooltip" data-toggle="tooltip" title="{$field.tooltip}{* cannot be escaped *}"{/if}>
								{$field.name}{* cannot be escaped *}
							</span>
						</label>
						{foreach array_keys($device_types) as $device_type}
						{if $device_type == 'desktop'}{$device_suffix = ''}{else}{$device_suffix = '_'|cat:$device_type}{/if}
						{if !$multiple_devices && $device_suffix}{continue}{/if}
						{$id = $k|cat:'_'|cat:$id_carousel|cat:$device_suffix}
						{$value = $field.value}
						{if isset($carousel.settings.$s_type.$k)}
							{* this is original (desktop) saved value *}
							{$value = $carousel.settings.$s_type.$k}
						{/if}
						{$custom_value = false}
						{$s_type_with_suffix = $s_type|cat:$device_suffix}
						{if $device_suffix && isset($carousel.settings.$s_type_with_suffix.$k)}
							{* replace original value with custom value for current device, if is available *}
							{$value = $carousel.settings.$s_type_with_suffix.$k}
							{$custom_value = true}
						{/if}
						{$input_name = 'settings['|cat:$s_type_with_suffix|cat:']['|cat:$k|cat:']'}
						<div class="col-lg-{$grid[1]|intval}{if $device_suffix} device-input-wrapper {$device_type|escape:'html':'UTF-8'} {if $custom_value}un{/if}locked{/if}">
							{if $device_suffix}
								<a href="#" class="lock-device-settings"><i class="icon-lock"></i><i class="icon-unlock-alt"></i></a>
								<div class="lock-overlay"><div class="same-note">{if $grid[1] > 3}{l s='Same as on desktop' mod='easycarousels'}{/if}</div></div>
							{/if}
							<div class="input-content">
							{if $field.type == 'switcher'}
								{$yes_no = in_array($k, ['consider_cat', 'last_visited', 'same_man', 'a', 'ah', 'normalize_h', 'l', 'title_one_line'])}
								<select class="switch-select{if $value} yes{/if}" name="{$input_name|escape:'html':'UTF-8'}">
									<option value="0"{if !$value} selected{/if}>
										{if $yes_no}{l s='No' mod='easycarousels'}{else}{l s='Hide' mod='easycarousels'}{/if}
									</option>
									<option value="1"{if $value} selected{/if}>
										{if $yes_no}{l s='Yes' mod='easycarousels'}{else}{l s='Show' mod='easycarousels'}{/if}
									</option>
								</select>
							{else if $field.type == 'select'}
								<select name="{$input_name|escape:'html':'UTF-8'}" class="select_{$k|escape:'html':'utf-8'}">
									{foreach $field.select as $val => $display_name}
										<option value="{$val|escape:'html':'UTF-8'}"{if isset($field.option_classes[$val])} class="{$field.option_classes[$val]|escape:'html':'utf-8'}"{/if}{if $val|cat:'' == $value|cat:''} selected{/if}>{$display_name|escape:'html':'UTF-8'}</option>
									{/foreach}
								</select>
							{else if $s_type == 'exceptions'}
								{$value = []}{if isset($carousel.settings.exceptions)}{$value = $carousel.settings.exceptions}{/if}
								{foreach $field.selectors as $key => $selector}
									<div class="exceptions-block {$key|escape:'html':'UTF-8'}{if $value && $value[$key]['ids']} has-ids{/if}">
										<select name="settings[exceptions][{$key|escape:'html':'UTF-8'}][type]" class="exc {$key|escape:'html':'UTF-8'}">
											{foreach $selector as $k => $opt}
												{$selested = $value && $value[$key]['type'] == $k}
												<option value="{$k|escape:'html':'UTF-8'}"{if $selested} selected{/if}>{$opt|escape:'html':'UTF-8'}</option>
											{/foreach}
										</select>
										<div class="input-group exc-ids">
											<span class="input-group-addon">
												<span class="label-tooltip" data-toggle="tooltip" title="{l s='For example: 11,15,18' mod='easycarousels'}">
													{l s='IDs' mod='easycarousels'}
												</span>
											</span>
											<input type="text" name="settings[exceptions][{$key|escape:'html':'UTF-8'}][ids]" value="{if $value && $value[$key]['ids']}{$value[$key]['ids']|escape:'html':'UTF-8'}{/if}" class="ids">
										</div>
									</div>
								{/foreach}
							{else}
								{$use_group = !empty($field.prefix) || !empty($field.suffix)}
							    {if $use_group}
							    	<div class="input-group">
							        {if !empty($field.prefix)}<span class="input-group-addon">{$field.prefix|escape:'html':'UTF-8'}</span>{/if}
							    {/if}
								<input type="text" name="{$input_name|escape:'html':'UTF-8'}" id="{$id|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}">
								{if $use_group}
							        {if !empty($field.suffix)}<span class="input-group-addon">{$field.suffix|escape:'html':'UTF-8'}</span>{/if}
							        </div>
							    {/if}
							{/if}
							</div>
						</div>
						{/foreach}
					</div>
				{/foreach}
				</div>
				{/if}
			{/for}
		</div>
		{/function}

		{renderSettings s_type='special' s_name='' rows=1}
		{renderSettings s_type='php' s_name='' rows=2}
		{capture name='template_settings_txt'}{l s='Template settings' mod='easycarousels'}{/capture}
		{capture name='carousel_settings_txt'}{l s='Carousel settings' mod='easycarousels'}{/capture}
		{capture name='exceptions_txt'}{l s='Exceptions' mod='easycarousels'}{/capture}
		{renderSettings s_type='tpl' s_name=$smarty.capture.template_settings_txt rows=7}
		{renderSettings s_type='carousel' s_name=$smarty.capture.carousel_settings_txt}
		{renderSettings s_type='exceptions' s_name=$smarty.capture.exceptions_txt cols=1}

		<div class="p-footer">
			<input type="hidden" name="hook_name" value="{$carousel.hook_name|escape:'html':'UTF-8'}">
			<input type="hidden" name="position" value="{$carousel.position|intval}">
			<input type="hidden" name="id_wrapper" value="{$carousel.id_wrapper|intval}">
			<button id="saveCarousel" class="btn btn-default">
				<i class="process-icon-save"></i>
				{l s='Save' mod='easycarousels'}
			</button>
			{if $multishop_warning}
				<div class="inline-block info-note alert-info">{l s='NOTE: Changes will be saved for more than one shop' mod='easycarousels'}</div>
			{/if}
		</div>
	</div>
	{/if}
</form>
</div>
{* since 2.7.5 *}
