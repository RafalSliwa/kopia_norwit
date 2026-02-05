{*
*  @author    Amazzing <mail@mirindevo.com>
*  @copyright Amazzing
*  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{if $banners}
	<div class="cb {$hook_name|escape:'html':'UTF-8'} clearfix" data-hook="{$hook_name|escape:'html':'UTF-8'}">
		{foreach $banners as $id_wrapper => $w}
			{if empty($w.banners) || empty($w.settings)}{continue}{/if}
			{$settings = $w.settings}
			{if !empty($settings.item_w)}
				<style type="text/css">
					{foreach $settings.item_w as $res => $iw}
						{if $res}@media(max-width:{$res|intval}px)
							{ldelim}
						{/if}.w-{$id_wrapper|intval} .cb-item
						{ldelim}width:{$iw|floatval}%
						{rdelim}
						{if $res}
							{rdelim}
						{/if}
					{/foreach}
				</style>
			{/if}
			<div class="cb-wrapper w-{$id_wrapper|intval} type-{$settings.display_type|intval}{if !empty($settings.custom_class)} {$settings.custom_class|escape:'html':'UTF-8'}{/if}"
				data-wrapper="{$id_wrapper|intval}">
				{if $settings.is_carousel}
					<div class="cb-carousel {if $settings.bx}pre-bx{else}swiper{/if}"
						data-settings="{$settings.carousel|json_encode|escape:'html':'UTF-8'}">
						{if !$settings.bx}<div class="swiper-wrapper">{/if}
						{/if}
						{foreach $w.banners as $id => $banner}
							<div id="cb-{$id|intval}"
								class="cb-item{if $settings.is_carousel && !$settings.bx} swiper-slide{/if}{if !empty($banner.css_class)} {$banner.css_class|escape:'html':'UTF-8'}{/if}{if !empty($banner.active_item)} active{/if}">
								{if $settings.display_type == 4}
									<div class="cb-item-title">
										{if !empty($banner.ac_title)}{$banner.ac_title|escape:'html':'UTF-8'}{else}{$id|intval}{/if}
									</div>
								{/if}
								<div class="cb-item-content" {if $settings.display_type == 4 && empty($banner.active_item)}
									style="display:none;" {/if}>
									{if !empty($banner.img.src)}
										{if !empty($banner.link.href)}<a href="{$banner.link.href|escape:'html':'UTF-8'}"
												{if isset($banner.link._blank)} target="_blank" {/if}>{/if}
												<img src="{$banner.img.src|escape:'html':'UTF-8'}"
													class="cb-img{if !empty($banner.img_hover.src)} hover-src"
													data-hover-src="{basename($banner.img_hover.src)|escape:'html':'UTF-8'}{/if}"
													width="{$banner.img.w|intval}" height="{$banner.img.h|intval}"
													{if !empty($banner.img.alt)} alt="{$banner.img.alt|escape:'html':'UTF-8'}"
														{/if}{if !empty($banner.img.title)} title="{$banner.img.title|escape:'html':'UTF-8'}"
														{/if}{if !empty($banner.img.webp_fallback)}
														onerror="this.onerror='';this.src=this.src+'.{$banner.img.webp_fallback|escape:'html':'UTF-8'}';"
													{/if} loading="lazy">
												{if !empty($banner.link.href)}</a>{/if}
										{/if}
										{if !empty($banner.html)}
											<div
												class="custom-html{if !empty($banner.html_class)} {$banner.html_class|escape:'html':'UTF-8'}{/if}">
												{$banner.html nofilter}{* can not be escaped *}
											</div>
										{/if}
									</div>
									<div
										class="nr-custom-html{if !empty($banner.html_class)} {$banner.html_class|escape:'html':'UTF-8'}{/if}">
										{$banner.html nofilter}{* can not be escaped *}
									</div>
								</div>
							{/foreach}
							{if $settings.is_carousel}
								{if !$settings.bx}
							</div>{/if}</div>
					{/if}
				</div>
			{/foreach}
		</div>
	{/if}
	{* since 3.0.0 *}