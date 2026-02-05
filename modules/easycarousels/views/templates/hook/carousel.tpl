{**
*
* @author    Amazzing <mail@mirindevo.com>
* @copyright Amazzing
* @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
**}

{$c_settings = $carousel.settings.carousel}
{$tpl_settings = $carousel.settings.tpl}
<div id="{$carousel.identifier|escape:'html':'UTF-8'}"
	class="easycarousel {$tpl_settings.custom_class|escape:'html':'UTF-8'}{if $in_tabs} ec-tab-pane{else} carousel_block{/if}{if !empty($custom_class)} {$custom_class|escape:'html':'UTF-8'}{/if}{if empty($carousel.name) && $c_settings.n} nav_without_name{/if}{if !$ec_is_mobile}{if $c_settings.n == 2} n-hover{/if}{if $c_settings.p == 2} p-hover{/if}{/if}">
	{if !$in_tabs && !empty($carousel.name)}
		<h3 class="title_block carousel_title">
			{if $tpl_settings.view_all == 2 && !empty($carousel.view_all_link)}<a
				href="{$carousel.view_all_link|escape:'html':'UTF-8'}">{/if}
				{$carousel.name|escape:'html':'UTF-8'}
				{if $tpl_settings.view_all == 2 && !empty($carousel.view_all_link)}</a>{/if}
		</h3>
	{/if}
	{if !empty($carousel.description)}<div class="carousel-description">
		{$carousel.description nofilter}{* can not be escaped *}</div>{/if}
	<div class="block_content">
		{$is_carousel = $c_settings.type == 1}
		<div class="c_container {if $is_carousel}ecarousel{if $carousel.is_swiper} swiper{/if}{else if $c_settings.type == 2}scroll-x{else}ec-grid{/if} xl-{$c_settings.i|intval} lg-{$c_settings.i_1200|intval} md-{$c_settings.i_992|intval} sm-{$c_settings.i_768|intval} xs-{$c_settings.i_480|intval}"
			{if $is_carousel} data-settings="{$c_settings|json_encode|escape:'html':'UTF-8'}"
				{/if}{if !empty($c_settings.normalize_h)} data-nh="1" {/if}{if !empty($tpl_settings.second_image)}
			data-si="1" {/if}>
			{if $carousel.is_swiper}<div class="swiper-wrapper">{/if}
				{foreach $carousel.items as $column_items}
					<div class="c_col{if $carousel.is_swiper} swiper-slide{/if}">
						{foreach $column_items as $i}
							<div class="c_item">
								{if $carousel.item_type == 'product'}{$product = $i}{else}{$item = $i}{/if}
								{include file=$carousel.item_tpl type=$carousel.type settings=$tpl_settings}
							</div>
						{/foreach}
					</div>
				{/foreach}
				{if $carousel.is_swiper}
			</div>{/if}
		</div>
		{if $is_carousel && !empty($c_settings.p)}<div class="sw-pgn"></div>{/if}
	</div>
	{if $tpl_settings.view_all == 1 && !empty($carousel.view_all_link)}
		<div class="view-all">
			<a href="{$carousel.view_all_link|escape:'html':'UTF-8'}">{l s='View all' mod='easycarousels'}</a>
		</div>
	{/if}
</div>
{* since 2.7.7 *}