{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{* <span class="hidden-md-up">{l s='Language:' d='Shop.Theme.Global'}</span> *}

<div class="language-selector dropdown js-dropdown">
	<span class="expand-more" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="language-dropdown">
	<span><img class="lang-flag lazyload" alt="{$current_language.iso_code}" data-src="{$urls.img_lang_url}{$current_language.id_lang}.jpg"></span>
		{$current_language.iso_code}
		<span class="dropdown-arrow"></span>
	</span>
	<ul class="dropdown-menu" aria-labelledby="language-dropdown">
	{foreach from=$languages item=language}
		<li {if $language.id_lang == $current_language.id_lang} class="current" {/if}>
			<a href="{url entity='language' id=$language.id_lang}" class="dropdown-item">
				<img class="lang-flag lazyload" alt="{$language.name_simple}" data-src="{$urls.img_lang_url}{$language.id_lang}.jpg">{$language.name_simple}
			</a>
		</li>
	{/foreach}
	</ul>
</div> 
