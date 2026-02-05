{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='page.tpl'}

{block name='page_content_container'}
	<section id="content" class="page-home">
		<div class="display-top-inner">
			<div class="container">
				{block name='page_content_top'}{/block}
				{hook h='displayTopColumn'}
			</div>
		</div>

		{* Slider HOOk-1*}
		{hook h='displayCustomBanners1'}

		{hook h='displayEasyCarousel1'}

		{* Hit of the week HOOk-2*}
		{block name = 'hit_of_the_week'}
			<div class="hit-of-the-week" id="blackweek">
				 <h3 class="hit-of-the-week-title">{l s='Hot deals' d='Shop.Theme.Catalog'}</h3>
				<p class="pse-p">
					Sprawdź <b class="pse-b">aktualne promocje</b> na sprzęt budowlany. <b class="pse-b">Hit tygodnia</b> i <b
						class="pse-b">wyprzedaże</b> to doskonała okazja na oszczędność. Skorzystaj, zanim znikną.
				</p>
				{hook h='displayCustomBanners2'}
			</div>
             <div class="hit-of-the-week" id="blackweek-desktop">
				{hook h='displayCustomBanners7'}
			</div>			
        {/block}


		{hook h='displayEasyCarousel2'}

		{* Product of the Month HOOK-3 *}
		{block name = 'product-of-the-month'}
			<div class="product-of-the-month">
				<h3 class="product-of-the-month-title">{l s='Top machines of the month' d='Shop.Theme.Catalog'}</h3>
				<p class="pse-p">
					Sprawdź <b class="pse-b">topowe maszyny</b>, które cieszą się <b class="pse-b">największą popularnością</b>
					w tym miesiącu. To sprzęt wybierany przez <b class="pse-b">profesjonalistów</b> – nie przegap najlepszych
					ofert!
				</p>
				{hook h='displayCustomBanners3'}
			</div>
		{/block}

		{hook h='displayEasyCarousel4'}

		{* OUR VIDEOS HOOK-4 *}
		{block name = 'our-videos'}
			<div class="our-videos">
				<h3 class="our-videos-title">{l s='Our Videos' d='Shop.Theme.Catalog'}</h3>
				<p class="pse-p">
					<b class="pse-b">Porady</b>, <b class="pse-b">testy</b> i <b class="pse-b">prezentacje sprzętu
						budowlanego</b> w jednym miejscu. Zobacz, jak działają maszyny, poznaj ich <b class="pse-b">zalety</b> i
					dowiedz się, jak wybrać <b class="pse-b">najlepsze rozwiązanie</b> dla siebie.
				</p>
				{hook h='displayCustomBanners4'}
			</div>
		{/block}

		{hook h='displayEasyCarousel5'}

		{hook h='displayCustomBanners5'}

		{block name='page_content'}
			{block name='hook_home'}
				{$HOOK_HOME nofilter}
			{/block}
		{/block}

	</section>
{/block}