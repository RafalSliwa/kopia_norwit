{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

<div class="easycarousels {$display_settings.custom_class|escape:'html':'UTF-8'}">
	{foreach $carousels_in_hook as $id_wrapper => $carousels}
		{$w_settings = []}{if !empty($wrappers_settings.$id_wrapper)}{$w_settings = $wrappers_settings.$id_wrapper}{/if}
		<div class="c-wrapper w-{$id_wrapper|intval}{if !empty($w_settings.custom_class)} {$w_settings.custom_class|escape:'html':'UTF-8'}{/if}">
		{if !empty($carousels.in_tabs)}
			<div class="in_tabs clearfix{if $display_settings.compact_tabs} compact_on{/if}">
				<ul id="{$hook_name|escape:'html':'UTF-8'}_{$id_wrapper|intval}_easycarousel_tabs" class="ec-tabs closed">
					{foreach array_values($carousels.in_tabs) as $k => $carousel}
						{if !$k}
							<li class="responsive_tabs_selection title_block">
								<a href="#" onclick="event.preventDefault();"><span class="selection">{$carousel.name|escape:'html':'UTF-8'}</span></a>
							</li>
						{/if}
						<li class="{if !$k}first active{/if} carousel_title">
							<a href="#{$carousel.identifier|escape:'html':'UTF-8'}" class="ec-tab-link">{if isset($carousel.name)}{$carousel.name|escape:'html':'UTF-8'}{/if}</a>
						</li>
					{/foreach}
				</ul>
				<div class="ec-tabs-content">
				{$custom_class = 'active'}
				{foreach $carousels.in_tabs as $carousel}
					{include file=$carousel_tpl carousel = $carousel in_tabs = true custom_class = $custom_class}
					{$custom_class = ''}
				{/foreach}
				</div>
			</div>
		{/if}
		{if !empty($carousels.one_by_one)}
			<div class="one_by_one clearfix">
				{foreach $carousels.one_by_one as $carousel}
					{include file=$carousel_tpl carousel = $carousel in_tabs=false}
				{/foreach}
			</div>
		{/if}
		</div>
	{/foreach}
</div>
{if Tools::getValue('ajax')}{literal}<script type="text/javascript">try{ec.activateTabs();ec.prepareVisibleCarousels();}catch(e){}</script>{/literal}{/if}
{* since 2.7.5 *}
