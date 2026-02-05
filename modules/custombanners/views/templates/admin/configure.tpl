{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if !empty($js_vars)}
<script type="text/javascript">{foreach $js_vars as $name => $value}
	var {$name} = {$value|json_encode nofilter}{* can not be escaped *};
{/foreach}</script>
{/if}

{$show_secret_fields = Tools::isSubmit('showsecretfields')}
<div class="cb horizontal-tabs clearfix{if $cb->is_16} ps-16{/if}{if !$show_secret_fields} hide-secret-fields{/if}">
	<a href="#items" class="nav-tab-name first active"><i class="icon-image"></i> {l s='Banners' mod='custombanners'}</a>
	<a href="#optimize" class="nav-tab-name"><i class="icon-tachometer"></i> {l s='Image optimization' mod='custombanners'}</a>
	<a href="#customcode" class="nav-tab-name"><i class="icon-code"></i> {l s='Custom CSS/JS' mod='custombanners'}</a>
	<a href="#importer" class="nav-tab-name"><i class="icon-file-zip-o"></i> {l s='Import/export' mod='custombanners'}</a>
	<a href="#info" class="nav-tab-name"><i class="icon-info-circle"></i> {l s='Information' mod='custombanners'}</a>
</div>

<div class="alert alert-danger general-ajax-errors" style="display:none;"></div>
{foreach $cb_errors as $error}
	<div class="alert alert-danger">
		{$error nofilter}{* can not be escaped *} <i class="icon icon-times" data-dismiss="alert"></i>
	</div>
{/foreach}
{if $files_update_warnings}
	<div class="alert alert-warning">
		{l s='Some of your customized files have been updated in the new version' mod='custombanners'}
		<ul>
		{foreach $files_update_warnings as $file => $identifier}
			<li>
				{$file|escape:'html':'UTF-8'}
				<span class="warning-advice">
					{l s='Make sure you update this file in your theme folder, and insert the following code to the last line' mod='custombanners'}:
					<span class="code">{$identifier|escape:'html':'UTF-8'}</span>
				</span>
			</li>
		{/foreach}
		</ul>
	</div>
{/if}

<div id="cb" class="cb horizontal-tabs-content{if !$show_secret_fields} hide-secret-fields{/if}">
	<div id="items" class="tab-panel panel all-items active">
		{$selected_hook = Tools::getValue('hook', current(array_keys($hooks)))}
		<form class="settings form-horizontal row">
			<label class="control-label col-lg-1" for="hookSelector">{l s='Hook' mod='custombanners'}</label>
			<div class="col-lg-3">
				<select class="hookSelector">
					{foreach $hooks as $hk => $qty}
						<option value="{$hk|escape:'html':'UTF-8'}"{if $hk == $selected_hook} selected{/if}>
							{$hk|escape:'html':'UTF-8'} ({$qty|intval})
						</option>
					{/foreach}
				</select>
			</div>
			<div class="col-lg-8 hook-settings">
				<button class="btn btn-default callSettings" data-settings="exceptions">
					<i class="icon-ban"></i> {l s='Exceptions' mod='custombanners'}
				</button>
				<button class="btn btn-default callSettings" data-settings="positions">
					<i class="icon-arrows-alt"></i> {l s='Module positions' mod='custombanners'}
				</button>
				<div class="quick-search inline-block absolute transparent">
					<a href="#" class="toggleSearch inline-block"><i class="icon-search"></i> {l s='Quick search' mod='custombanners'}</a>
					<div class="quick-search-content inline-block">
						<div class="inline-block">
							<select class="searchBy">
								<option value="banner_name">{l s='By Banner name' mod='custombanners'}</option>
								<option value="product">{l s='By Product associations' mod='custombanners'}</option>
								<option value="category">{l s='By Category associations' mod='custombanners'}</option>
								<option value="manufacturer">{l s='By Manufacturer associations' mod='custombanners'}</option>
								<option value="supplier">{l s='By Supplier associations' mod='custombanners'}</option>
								<option value="cms">{l s='By CMS associations' mod='custombanners'}</option>
						</select>
						</div>
						<div class="inline-block">
							<input type="text" class="searchByValue">
						</div>
						<a href="#" class="icon-times closeSearch toggleSearch"></a>
					</div>
				</div>
				<button class="addWrapper btn btn-default pull-right">
					<i class="icon-th"></i> {l s='New Wrapper' mod='custombanners'}
				</button>
			</div>
		</form>
		<div id="settings-content" style="display:none;">{* filled dinamically *}</div>
		{foreach array_keys($hooks) as $hk}
			<div id="{$hk|escape:'html':'UTF-8'}" class="hook-content{if $hk == $selected_hook} active{/if}">
				{if substr($hk, 0, 20) == 'displayCustomBanners'}
				<div class="alert alert-info">
					{l s='In order to display this hook, insert the following code to any tpl' mod='custombanners'}: <strong>{literal}{hook h='{/literal}{$hk|escape:'html':'UTF-8'}{literal}'}{/literal}</strong>
				</div>
				{/if}
				<div class="wrappers-container">
					{if isset($banners.$hk)}
						{foreach $banners.$hk as $id_wrapper => $banners_in_wrapper}
							{include file="./wrapper-form.tpl" banners=$banners_in_wrapper id_wrapper=$id_wrapper}
						{/foreach}
					{/if}
				</div>
			</div>
		{/foreach}
		<div class="no-matches hidden">
			<i class="icon-warning-sign"></i>
			{l s='No matches' mod='custombanners'}
		</div>
		<div class="classes-wrapper" style="display:none;">
		<div class="predefined-classes round-border">
			<i class="caret-t"></i>
			<div class="col-md-6">
				{foreach $bs_classes as $class => $width}
					<div class="row">
						<label class="control-label grey-note col-md-11">
							{l s='Banner width for displays, wider than %d' mod='custombanners' sprintf=$width}:
						</label>
						<div class="col-md-1">
							<div class="multiclass">
								<i class="icon-bars cursor-pointer"></i>
								<div class="list" style="display:none;">
									<i class="caret-l"></i>
									<ul>
									{for $col=1 to 12}
										<li class="" data-class="col-{$class|escape:'html':'UTF-8'}-{$col|intval}">
											<span class="cl"><span class="fragment">col-{$class|escape:'html':'UTF-8'}-</span>{$col|intval}</span>
											<span class="grey-note">{($col*100/12)|round:2|floatval}%</span>
										</li>
									{/for}
									</ul>
								</div>
							</div>
						</div>
					</div>
				{/foreach}
			</div>
			<div class="col-md-6">
				<div class="row">
					<label class="control-label grey-note col-md-8">
						<span class="label-tooltip" data-toggle="tooltip" title="{l s='Use this class if you want to place a caption over the image' mod='custombanners'}">{l s='Place HTML over the image' mod='custombanners'}:</span>
					</label>
					<div class="col-md-4">
						<div class="cl" data-class="html-over">html-over</div>
					</div>
				</div>
				<div class="row">
					<label class="control-label grey-note col-md-8">
						<span class="label-tooltip" data-toggle="tooltip" title="{l s='Affects all images within banner container if no other overrides are applied' mod='custombanners'}">
						{l s='Round borders for the images' mod='custombanners'}:
						</span>
					</label>
					<div class="col-md-4">
						<div class="cl" data-class="img-rb">img-rb</div>
					</div>
				</div>
			</div>
		</div>
		</div>
		<div class="config-footer">
			<div class="btn-group bulk-actions dropup">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					{l s='Bulk actions' mod='custombanners'} <span class="icon-caret-up"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#"	class="bulk-select"><i class="icon-check-sign"></i> {l s='Check all' mod='custombanners'}</a></li>
					<li><a href="#" class="bulk-unselect"><i class="icon-check-empty"></i> {l s='Uncheck all' mod='custombanners'}</a></li>
					<li class="divider"></li>
					<li>
						<span class="status-label"><i class="icon-check on"></i> {l s='Enable' mod='custombanners'}:</span>
						{foreach $device_types as $device_type => $input_name}
							<a href="#" class="icon-{$device_type|escape:'html':'UTF-8'} device-icon" title="{$device_type|escape:'html':'UTF-8'}" data-bulk-act="{$input_name|escape:'html':'UTF-8'}" data-bulk-value="1"></a>
						{/foreach}
					</li>
					<li>
						<span class="status-label"><i class="icon-times off"></i> {l s='Disable' mod='custombanners'}:</span>
						{foreach $device_types as $device_type => $input_name}
							<a href="#" class="icon-{$device_type|escape:'html':'UTF-8'} device-icon" title="{$device_type|escape:'html':'UTF-8'}" data-bulk-act="{$input_name|escape:'html':'UTF-8'}" data-bulk-value="0"></a>
						{/foreach}
					</li>
					<li class="dont-hide">
						<a href="#" class="toggle-hook-list"><i class="icon icon-copy"></i> {l s='Copy to hook' mod='custombanners'}</a>
						<div class="dynamic-hook-list" style="display:none;">
							<button class="btn btn-default" data-bulk-act="copy">{l s='OK' mod='custombanners'}</button>
						</div>
					</li>
					<li class="dont-hide">
						<a href="#" class="toggle-hook-list"><i class="icon icon-arrow-left"></i> {l s='Move to hook' mod='custombanners'}</a>
						<div class="dynamic-hook-list" style="display:none;">
							<button class="btn btn-default" data-bulk-act="move">{l s='OK' mod='custombanners'}</button>
						</div>
					</li>
					<li class="divider"></li>
					<li><a href="#" class="conf-required" data-bulk-act="delete"><i class="icon-trash"></i> {l s='Delete' mod='custombanners'}</a></li>
				</ul>
			</div>
			<div class="secret-element pull-right">
				<button type="button" class="btn btn-default conf-required no-selection-required" data-bulk-act="deleteUnusedImages">
					{l s='Delete usused images' mod='custombanners'}
				</button>
				<button type="button" class="btn btn-default conf-required no-selection-required" data-bulk-act="deleteAll">
					{l s='Delete all banners' mod='custombanners'}
				</button>
			</div>
		</div>
	</div>
	<div id="optimize" class="tab-panel panel">
		<div class="info-note alert-info">
			<div class="all-img-data">
				{l s='Number of uploaded images' mod='custombanners'}: <span class="total-num b">{$optimization_data.images.num|intval}</span><br>
				{l s='Average compression ratio' mod='custombanners'}: <span class="avg-compression b">{$optimization_data.images.compression|floatval}%</span>
				<a href="{$info_links.documentation|escape:'html':'UTF-8'}#page=4" target="_blank" class="label-tooltip b" title="{l s='Read more about image compression' mod='custombanners'}">
					<i class="icon-question-circle"></i>
				</a>
			</div>
		</div>
		<div class="form-group clearfix">
			<label class="col-md-3 control-label text-right">{l s='Image compression method' mod='custombanners'}</label>
			<div class="col-md-9 o-field">
				<select name="optimizer" class="selectOptimizer">
					{foreach $optimization_data.optimizers as $k => $o}
						<option value="{$k|escape:'html':'UTF-8'}"{if $o->active} selected{/if}>{$o->label|escape:'html':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>
		{foreach $optimization_data.optimizers as $k => $o}
			<form action="" class="optimizer-form{if $o->active} active{/if}" data-optimizer="{$k|escape:'html':'UTF-8'}">
				<div class="clearfix">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-9">
						<div class="o-info">
							{include file = $cb->getTemplatePath('views/templates/admin/optimizer-how-to.tpl')}
							{if !empty($o->supported_formats)}
								<div class="o-formats">
									{l s='Supported formats for compression' mod='custombanners'}:
									<span class="green">{implode(', ', $o->supported_formats)|upper|escape:'html':'UTF-8'}</span>
								</div>
							{/if}
						</div>
					</div>
				</div>
				{foreach $o->fields as $f_name => $f}
					<div class="form-group clearfix">
						<label class="col-md-3 control-label text-right">
							<span{if !empty($f.tooltip)} class="label-tooltip" title="{$f.tooltip|escape:'html':'UTF-8'}"{/if}>
								{$f.label|escape:'html':'UTF-8'}
							</span>
						</label>
						<div class="col-md-9 o-field" data-name="{$f_name|escape:'html':'UTF-8'}">
							<input type="text" name="o_settings[{$f_name|escape:'html':'UTF-8'}]" value="{$f.value|escape:'html':'UTF-8'}" class="o-field-value">
						</div>
					</div>
				{/foreach}
				<input type="hidden" name="o_identifier" value="{$k|escape:'html':'UTF-8'}">
			</form>
		{/foreach}
		<div class="form-group clearfix">
			<label class="col-md-3 control-label"> </label>
			<div class="col-md-9 optimizer-actions">
				<button type="button" class="btn uppercase saveOptimizer">
					<i class="icon-save"></i> {l s='Save settings' mod='custombanners'}
				</button>
				<button type="button" class="btn btn-primary regenerateThumbs">
					<span class="stop">
						<i class="loading-indicator"></i> <span class="processed-num"></span>
					</span>
					<span class="start">
						<i class="icon-play"></i> {l s='Regenerate thumbnails' mod='custombanners'}
					</span>
				</button>
				<span class="o-size-stats hidden">{l s='Totally saved' mod='custombanners'}: <span class="dynamic-value b"></span></span>
			</div>
		</div>
	</div>
	<div id="customcode" class="tab-panel">
		<div class="panel customcode clearfix">
			<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.8.1/ace.js" integrity="sha512-qqjLKA1tYKWxtpKReCrmE8DNYVa+/gNzzeJ6SZaTi+3J+KdTXUlS3AZtcPydvb0rXWtdwE4/KCS4RjfMGeil6g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
			{foreach $custom_code as $type => $code}
				<div class="custom-code {$type|escape:'html':'UTF-8'}">
					<div class="custom-code-title">{l s='Custom [1]%s[/1]' mod='custombanners' sprintf=$type tags=['<span class="uppercase b">']}</div>
					<div id="code{$type|escape:'html':'UTF-8'}" class="custom-code-content" data-type="{$type|escape:'html':'UTF-8'}">{$code|escape:'html':'UTF-8'}</div>
					<div class="custom-code-backup hidden {$type|escape:'html':'UTF-8'}">{$code|escape:'html':'UTF-8'}</div>
					<div class="custom-code-actions clearfix text-right">
						<button type="button" class="btn btn-default pull-left processCustomCode" data-type="{$type|escape:'html':'UTF-8'}" data-action="Save">
						<i class="icon-save"></i> {l s='Save' mod='custombanners'}</button>
						<span class="reset-note for-{$type|escape:'html':'UTF-8'} hidden">
							{l s='Code was updated in editor. You can [1]Save it[/1] now, or [2]Undo[/2] last action' mod='custombanners' tags=['<span class="saveCode">', '<span class="undoCodeAction">']}.
						</span>
						<div class="btn-group pull-right">
							<button type="button" class="btn btn-default toggleResetOptions"><i class="icon-undo"></i> {l s='Reset' mod='custombanners'}</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="icon-caret-down"></i></button>
							<ul class="dropdown-menu">
								<li><a href="#" class="processCustomCode" data-type="{$type|escape:'html':'UTF-8'}" data-action="GetInitial">
								{l s='Reset to initial code, that was used when this page was loaded' mod='custombanners'}</a></li>
								<li><a href="#" class="processCustomCode" data-type="{$type|escape:'html':'UTF-8'}" data-action="GetOriginal">
								{l s='Reset to original code, that was used when module was installed' mod='custombanners'}</a></li>
							</ul>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
		<div class="panel library clearfix">
			<form method="post" class="form-horizontal library-form" action="" enctype="multipart/form-data">
				<label class="inline-block weight-normal">{l s='Slider library' mod='custombanners'}</label>
				<div class="inline-block library-options-holder">
					<select name="type">{foreach $slider_library.options as $val => $name}
						<option value="{$val|escape:'html':'UTF-8'}"{if $slider_library.data.type == $val} selected{/if}>{$name|escape:'html':'UTF-8'}</option>
					{/foreach}</select>
				</div>
				<label class="inline-block weight-normal">
					<input type="checkbox" name="load" value="1"{if $slider_library.data.load} checked{/if}>
					{l s='Load selected library' mod='custombanners'}
					<span class="grey-note">({l s='Uncheck, if it is already loaded by another module' mod='custombanners'})</span>
				</label>
			</form>
		</div>
	</div>
	<div id="importer" class="tab-panel panel importer clearfix">
		<div class="info-note alert-info">{include file = $cb->getTemplatePath('views/templates/admin/importer-how-to.tpl')}</div>
		<form action="" method="post" class="export-form" enctype="multipart/form-data">
			<input type="hidden" name="action" value="exportBannersData">
			<button type="submit" class="exportBannersData btn btn-default">
				<i class="icon-download icon-rotate-180"></i> {l s='Export all banners' mod='custombanners'}
			</button>
		</form>
		<form action="" method="post" class="import-form" enctype="multipart/form-data">
			<input type="file" name="zipped_banners_data" style="display:none;">
			<button type="button" class="importBannersData btn btn-default">
				<i class="icon-download"></i> {l s='Import banners' mod='custombanners'}
			</button>
		</form>
	</div>
	<div id="info" class="tab-panel panel">
		<div class="info-row">
			{l s='Current version:' mod='custombanners'} <b>{$cb->version|escape:'html':'UTF-8'}</b>
		</div>
		<div class="info-row">
			<a href="{$info_links.changelog|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-code-fork"></i> {l s='Changelog' mod='custombanners'}
			</a>
		</div>
		<div class="info-row">
			<a href="{$info_links.documentation|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-book"></i> {l s='Documentation' mod='custombanners'}
			</a>
		</div>
		<div class="info-row">
			<a href="{$info_links.contact|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-envelope"></i> {l s='Contact us' mod='custombanners'}
			</a>
		</div>
		<div class="info-row">
			<a href="{$info_links.modules|escape:'html':'UTF-8'}" target="_blank">
				<i class="icon-download"></i> {l s='Our modules' mod='custombanners'}
			</a>
		</div>
	</div>
</div>
{* since 3.0.1 *}
