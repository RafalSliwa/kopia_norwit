{*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* @category  FMM Modules
* @package   productlabelsandstickers
* @author    FMM Modules
* @copyright FMM Modules
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
{if Tools::version_compare($ps_version, '1.7.0.0', '<') == true}
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
{/if}
{foreach $stickers as $stick}
<script>
{if $stick.x_align != 'center' && $stick.y_align != 'center'}
    {if $stick.x_align == 'left' && $stick.y_align == 'top'}{literal}
    $('#bigpic').parent().parent()
    .prepend("<span {/literal}{if !empty($stick.title) && $stick.text_status > 0}class='fmm_title_text_sticker' {/if}{literal}style='{/literal}text-align:{$stick.x_align|escape:'htmlall':'UTF-8'}{literal};{/literal}{if empty($stick.sticker_image) && $stick.text_status > 0}width:auto;{/if}{literal}display: inline-block; position: absolute; left: 6px; top: 6px;'><span style='{/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}{literal}background-color:{/literal}{$stick.bg_color|escape:'htmlall':'UTF-8'}{/if}{literal};color:{/literal}{$stick.color|escape:'htmlall':'UTF-8'}{literal};font-family:{/literal}{$stick.font|escape:'htmlall':'UTF-8'}{literal};font-size:{/literal}{if $current_page == "index"}{$stick.font_size|escape:'htmlall':'UTF-8'}{elseif $current_page == "category"}{$stick.font_size_listing|escape:'htmlall':'UTF-8'}{else}{$stick.font_size_product|escape:'htmlall':'UTF-8'}{/if}px{literal};'>{/literal}{if !empty($stick.sticker_image)}<img style=\"width:{$stick.sticker_size|escape:'htmlall':'UTF-8'}%\" src='{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}' />{/if}{literal} {/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}<br><i>{$stick.title|escape:'htmlall':'UTF-8'}</i>{/if}{literal}</span></span>");
    {/literal}{elseif $stick.x_align == 'right' && $stick.y_align == 'top'}{literal}
    $('#bigpic').parent().parent()
    .prepend("<span {/literal}{if !empty($stick.title) && $stick.text_status > 0}class='fmm_title_text_sticker' {/if}{literal}style='{/literal}text-align:{$stick.x_align|escape:'htmlall':'UTF-8'}{literal};{/literal}{if empty($stick.sticker_image) && $stick.text_status > 0}width:auto;{/if}{literal}display: inline-block; position: absolute; right: 6px; top: 6px;'><span style='{/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}{literal}background-color:{/literal}{$stick.bg_color|escape:'htmlall':'UTF-8'}{/if}{literal};color:{/literal}{$stick.color|escape:'htmlall':'UTF-8'}{literal};font-family:{/literal}{$stick.font|escape:'htmlall':'UTF-8'}{literal};font-size:{/literal}{if $current_page == "index"}{$stick.font_size|escape:'htmlall':'UTF-8'}{elseif $current_page == "category"}{$stick.font_size_listing|escape:'htmlall':'UTF-8'}{else}{$stick.font_size_product|escape:'htmlall':'UTF-8'}{/if}px{literal};'>{/literal}{if !empty($stick.sticker_image)}<img style=\"width:{$stick.sticker_size|escape:'htmlall':'UTF-8'}%\" src='{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}' />{/if}{literal} {/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}<br><i>{$stick.title|escape:'htmlall':'UTF-8'}</i>{/if}{literal}</span></span>");
    {/literal}{elseif $stick.x_align == 'left' && $stick.y_align == 'bottom'}{literal}
    $('#bigpic').parent().parent()
    .append("<span {/literal}{if !empty($stick.title) && $stick.text_status > 0}class='fmm_title_text_sticker' {/if}{literal}style='{/literal}text-align:{$stick.x_align|escape:'htmlall':'UTF-8'}{literal};{/literal}{if empty($stick.sticker_image) && $stick.text_status > 0}width:auto;{/if}{literal}display: inline-block; position: absolute; left: 6px; bottom: 6px;'><span style='{/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}{literal}background-color:{/literal}{$stick.bg_color|escape:'htmlall':'UTF-8'}{/if}{literal};color:{/literal}{$stick.color|escape:'htmlall':'UTF-8'}{literal};font-family:{/literal}{$stick.font|escape:'htmlall':'UTF-8'}{literal};font-size:{/literal}{if $current_page == "index"}{$stick.font_size|escape:'htmlall':'UTF-8'}{elseif $current_page == "category"}{$stick.font_size_listing|escape:'htmlall':'UTF-8'}{else}{$stick.font_size_product|escape:'htmlall':'UTF-8'}{/if}px{literal};'>{/literal}{if !empty($stick.sticker_image)}<img style=\"width:{$stick.sticker_size|escape:'htmlall':'UTF-8'}%\" src='{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}' />{/if}{literal} {/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}<br><i>{$stick.title|escape:'htmlall':'UTF-8'}</i>{/if}{literal}</span></span>");
    {/literal}{elseif $stick.x_align == 'right' && $stick.y_align == 'bottom'}{literal}
    $('#bigpic').parent().parent()
    .append("<span {/literal}{if !empty($stick.title) && $stick.text_status > 0}class='fmm_title_text_sticker' {/if}{literal}style='{/literal}text-align:{$stick.x_align|escape:'htmlall':'UTF-8'}{literal};{/literal}{if empty($stick.sticker_image) && $stick.text_status > 0}width:auto;{/if}{literal}display: inline-block; position: absolute; right: 6px; bottom: 6px;'><span style='{/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}{literal}background-color:{/literal}{$stick.bg_color|escape:'htmlall':'UTF-8'}{/if}{literal};;color:{/literal}{$stick.color|escape:'htmlall':'UTF-8'}{literal};font-family:{/literal}{$stick.font|escape:'htmlall':'UTF-8'}{literal};font-size:{/literal}{if $current_page == "index"}{$stick.font_size|escape:'htmlall':'UTF-8'}{elseif $current_page == "category"}{$stick.font_size_listing|escape:'htmlall':'UTF-8'}{else}{$stick.font_size_product|escape:'htmlall':'UTF-8'}{/if}px{literal};'>{/literal}{if !empty($stick.sticker_image)}<img style=\"width:{$stick.sticker_size|escape:'htmlall':'UTF-8'}%\" src='{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}' />{/if}{literal} {/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}<br><i>{$stick.title|escape:'htmlall':'UTF-8'}</i>{/if}{literal}</span></span>");
    {/literal}{else}{literal}
    $('#bigpic').parent().parent()
    .append("<span {/literal}{if !empty($stick.title) && $stick.text_status > 0}class='fmm_title_text_sticker' {/if}{literal}style='{/literal}{if empty($stick.sticker_image) && $stick.text_status > 0}width:auto;{/if}{literal}display: inline-block; position: absolute; right: 6px; bottom: 6px;'><span style='{/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}{literal}background-color:{/literal}{$stick.bg_color|escape:'htmlall':'UTF-8'}{/if}{literal};color:{/literal}{$stick.color|escape:'htmlall':'UTF-8'}{literal};font-family:{/literal}{$stick.font|escape:'htmlall':'UTF-8'}{literal};font-size:{/literal}{if $current_page == "index"}{$stick.font_size|escape:'htmlall':'UTF-8'}{elseif $current_page == "category"}{$stick.font_size_listing|escape:'htmlall':'UTF-8'}{else}{$stick.font_size_product|escape:'htmlall':'UTF-8'}{/if}px{literal};'>{/literal}{if !empty($stick.sticker_image)}<img style=\"width:{$stick.sticker_size|escape:'htmlall':'UTF-8'}%\" src='{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}' />{/if}{literal} {/literal}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}<br><i>{$stick.title|escape:'htmlall':'UTF-8'}</i>{/if}{literal}</span></span>");
    {/literal}
    {/if}
{elseif $stick.x_align == 'center' || $stick.y_align == 'center'}{literal}
$('#bigpic').parent().parent()
    .append("{/literal}<span {if !empty($stick.title) && $stick.text_status > 0}class='fmm_title_text_sticker' {/if}style='{if !empty($stick.title) && $stick.text_status > 0}text-align:{$stick.x_align|escape:'htmlall':'UTF-8'};{/if}{if empty($stick.sticker_image) && $stick.text_status > 0}width:auto;{/if}display: inline-block; z-index: 9;position: absolute;{if $stick.x_align == 'center' && $stick.y_align == 'center'}left:0%; width: 100%; text-align: center;top: {$stick.axis|escape:'htmlall':'UTF-8'}%;{elseif $stick.x_align == 'center' && $stick.y_align == 'top'}left:0%; width: 100%; text-align: center;top: 1%;{elseif $stick.x_align == 'center' && $stick.y_align == 'bottom'}left:0%; width: 100%; text-align: center;bottom: 1%;{elseif $stick.x_align == 'left' && $stick.y_align == 'center'}left:0%; top: {$stick.axis|escape:'htmlall':'UTF-8'}%; text-align: left;{elseif $stick.x_align == 'right' && $stick.y_align == 'center'}right:0%; text-align: right; top: {$stick.axis|escape:'htmlall':'UTF-8'}%;{/if} {if !empty($stick.title) && $stick.text_status > 0}width:{$stick.sticker_size|escape:'htmlall':'UTF-8'}%;{/if}'>{if !empty($stick.title) && $stick.text_status > 0}<span style='{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}background-color:{$stick.bg_color|escape:'htmlall':'UTF-8'}{/if};color:{$stick.color|escape:'htmlall':'UTF-8'};font-family:{$stick.font|escape:'htmlall':'UTF-8'};font-size:{if $current_page == "index"}{$stick.font_size|escape:'htmlall':'UTF-8'}{elseif $current_page == "category"}{$stick.font_size_listing|escape:'htmlall':'UTF-8'}{else}{$stick.font_size_product|escape:'htmlall':'UTF-8'}{/if}px;'>{/if}{if !empty($stick.sticker_image)}<img style='box-shadow:unset;{if $stick.text_status <= 0}width:{$stick.sticker_size|escape:'htmlall':'UTF-8'}%{/if}' src='{$base_image|escape:'htmlall':'UTF-8'}{$stick.sticker_image|escape:'htmlall':'UTF-8'}' />{/if}{if !empty($stick.sticker_image)}<br>{/if}{if !empty($stick.title) && $stick.text_status > 0 && $stick.sticker_type == 'text'}<i>{$stick.title|escape:'htmlall':'UTF-8'}</i>{/if}{if !empty($stick.title) && $stick.text_status > 0}</span>{/if}</span>");
{/if}
{literal}
</script>
<style type="text/css">
.fmm_title_text_sticker span { -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; padding: 5px; display: inline-block; text-align: center}
.fmm_title_text_sticker img { border:none!important;display: inline-block; vertical-align: middle; background: transparent !important;}
.fmm_title_text_sticker i { display: inline-block; font-style: normal}

</style>
{/literal}
{/foreach}
{if !empty($stickers_banner.title)}
{literal}
<script>
    $('.our_price_display').after({/literal}'<div style="padding:10px 6px; margin-bottom:10px; text-align:center;background:{$stickers_banner.bg_color|escape:'htmlall':'UTF-8'};color:{$stickers_banner.color|escape:'htmlall':'UTF-8'};border:1px solid {$stickers_banner.border_color|escape:'htmlall':'UTF-8'};font-family:{$stickers_banner.font|escape:'htmlall':'UTF-8'};font-size:{$stickers_banner.font_size|escape:'htmlall':'UTF-8'}px;font-weight:{$stickers_banner.font_weight|escape:'htmlall':'UTF-8'};">{$stickers_banner.title|escape:'htmlall':'UTF-8'}</div>'{literal});
</script>
{/literal}
{/if}


