{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
{block name='head_charset'}
<meta charset="utf-8">
{/block}
{block name='head_ie_compatibility'}
<meta http-equiv="x-ua-compatible" content="ie=edge">
{/block}

{block name='head_seo'}
  <title>{block name='head_seo_title'}{$page.meta.title}{/block}</title>
  {block name='hook_after_title_tag'}
    {hook h='displayAfterTitleTag'}
  {/block}
  <meta name="description" content="{block name='head_seo_description'}{$page.meta.description}{/block}">
  <!--<meta name="keywords" content="{block name='head_seo_keywords'}{$page.meta.keywords}{/block}">-->
{if $page.page_name == 'category' || $page.page_name == 'manufacturer' || $page.page_name == 'prices-drop' || $page.page_name == 'new-products' || $page.page_name == 'best-sales'}
     {if !$listing.products|count}
     {$page.meta.robots = 'noindex, follow, noodp, noydir'}
     {/if}
{/if}
{assign var="url1" value=$smarty.server.REQUEST_URI}
{if $url1|strstr:'koszyk' || $url1|strstr:'?order=' || $url1|strstr:'szukaj?controller=' || $url1|strstr:'/blog/szukaj/' || $url1|strstr:'/blog/tag/' || $url1|strstr:'/blog/kategoria/' || $url1|strstr:'/blog/autor' || $url1|strstr:'/rejestracja' || $url1|strstr:'/logowanie' || $url1|strstr:'/odzyskiwanie-hasla' || $url1|strstr:'/mapa-strony' || $url1|strstr:'/blog/kategorie' || $url1|strstr:'/blog/polecane'}
     {$page.meta.robots = 'noindex, follow, noodp, noydir'}
{/if}
    
  {if $page.meta.robots !== 'index'}
    <meta name="robots" content="{$page.meta.robots}">
  {/if}
  {if $page.meta.robots === 'index'}
     {assign var="url" value=$smarty.server.REQUEST_URI}
     {assign var="domain" value=$smarty.server.HTTP_HOST}
     {if $url|strstr:'page='}
        {if $url|strstr:'page=1' and preg_match('/page=\d$/', $url)}
            <link rel="canonical" href="https://{$domain}{$smarty.server.REQUEST_URI|regex_replace:"/\?.*$/":""}">
        {else}
            <link rel="canonical" href="https://{$domain}{$smarty.server.REQUEST_URI}">
        {/if}
     {elseif $page.page_name == 'product'}
        <link rel="canonical" href="{$page.canonical}">
     {else}
        <link rel="canonical" href="https://{$domain}{$smarty.server.REQUEST_URI|regex_replace:"/\?.*$/":""}">
     {/if}
  {/if}
  {block name='head_hreflang'}
      {foreach from=$urls.alternative_langs item=pageUrl key=code}
            <link rel="alternate" href="{$pageUrl}" hreflang="{$code}">
      {/foreach}
  {/block}
  
  {block name='head_microdata'}
    {include file="_partials/microdata/head-jsonld.tpl"}
  {/block}
  
  {block name='head_microdata_special'}{/block}
  
  {block name='head_pagination_seo'}
    {include file="_partials/pagination-seo.tpl"}
  {/block}

  {block name='head_open_graph'}
    <meta property="og:title" content="{$page.meta.title}" />
    <meta property="og:description" content="{$page.meta.description}" />
    <meta property="og:url" content="{$urls.current_url}" />
    <meta property="og:site_name" content="{$shop.name}" />
    {if !isset($product) && $page.page_name != 'product'}<meta property="og:type" content="website" />{/if}
  {/block}  
{/block}

{block name='head_viewport'}
<meta name="viewport" content="width=device-width, initial-scale=1">
{/block}

{block name='head_icons'}
<link rel="icon" type="image/vnd.microsoft.icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
<link rel="shortcut icon" type="image/x-icon" href="{$shop.favicon}?{$shop.favicon_update_time}">
{/block}


<!-- Codezeel added -->
<link href="//fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

{block name='stylesheets'}
  {include file="_partials/stylesheets.tpl" stylesheets=$stylesheets}
{/block}

{block name='javascript_head'}
  {include file="_partials/javascript.tpl" javascript=$javascript.head vars=$js_custom_vars}
{/block}

{block name='hook_header'}
  {$HOOK_HEADER nofilter}
{/block}

{block name='hook_extra'}{/block}
