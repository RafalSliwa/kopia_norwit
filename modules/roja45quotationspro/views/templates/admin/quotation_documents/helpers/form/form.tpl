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


{extends file="helpers/form/form.tpl"}

{block name="field"}
	{if $input.type == 'file_lang'}
		<div class="col-lg-8">
			{foreach from=$languages item=language}
				{if $languages|count > 1}
					<div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display:none"{/if}>
				{/if}
				<div class="form-group">
					<div class="col-lg-9">
						<input id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" type="file" name="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}" class="hide" accept="{$input.accept|escape:'htmlall':'UTF-8'}"/>
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-name" type="text" class="disabled" name="filename" readonly />
							<span class="input-group-btn">
								<button id="{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-selectbutton" type="button" name="submitAddAttachments" class="btn btn-secondary">
									<i class="icon-folder-open"></i> {l s='Choose a file' mod='roja45quotationspro'}
								</button>
							</span>
						</div>
					</div>
					{if $languages|count > 1}
					<div class="col-lg-2">
						<button type="button" class="btn btn-secondary dropdown-toggle" tabindex="-1" data-toggle="dropdown">
							{$language.iso_code|escape:'htmlall':'UTF-8'}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							{foreach from=$languages item=lang}
							<li><a href="javascript:hideOtherLanguage({$lang.id_lang|escape:'htmlall':'UTF-8'});" tabindex="-1">{$lang.name|escape:'htmlall':'UTF-8'}</a></li>
							{/foreach}
						</ul>
					</div>
					{/if}
				</div>
				<div class="form-group">
					{if isset($fields_value['file_name'][$language.id_lang])}
						<label class="control-label">
							{l s='File:' mod='roja45quotationspro'} {$fields_value['file_name'][$language.id_lang]|escape:'htmlall':'UTF-8'}
						</label>
					{/if}
				</div>
				{if $languages|count > 1}
					</div>
				{/if}
				<script>
				$(document).ready(function(){
					$('#{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}-selectbutton').click(function(e){
						$('#{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}').trigger('click');
					});
					$('#{$input.name|escape:'htmlall':'UTF-8'}_{$language.id_lang|escape:'htmlall':'UTF-8'}').change(function(e){
						var val = $(this).val();
						var file = val.split(/[\\/]/);
						$('#{$input.name}_{$language.id_lang}-name').val(file[file.length-1]);
					});
				});
			</script>
			{/foreach}
			{if isset($input.desc) && !empty($input.desc)}
				<p class="help-block">
					{$input.desc|escape:'htmlall':'UTF-8'}
				</p>
			{/if}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
