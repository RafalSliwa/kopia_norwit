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

<div id="contact-rich" class="contact-rich block">
	<h4 class="block_title hidden-md-down">{l s='Store information' d='Shop.Theme.Global'}</h4>
	<h4 class="block_title hidden-lg-up" data-target="#contact_rich_toggle" data-toggle="collapse">
		{l s='Our information' d='Shop.Theme.Global'}
		<span class="pull-xs-right">
		  <span class="navbar-toggler collapse-icons">
			<i class="fa-icon add"></i>
			<i class="fa-icon remove"></i>
		  </span>
		</span>
	</h4>
  <div id="contact_rich_toggle" class="block_content collapse">
	  <div class="">
		<div class="icon"><i class="fa fa-map-marker"></i></div>
		<div class="data">{$contact_infos.address.formatted nofilter}</div>
	  </div>
	  {if $contact_infos.phone}
		<hr/>
		<div class="">
		  <div class="icon"><i class="fa fa-phone"></i></div>
		  <div class="data">
			{l s='Call us:' d='Shop.Theme.Global'}<br/>
			<a href="tel:{$contact_infos.phone}">{$contact_infos.phone}</a>
		   </div>
		</div>
	  {/if}
	  {if $contact_infos.fax}
		<hr/>
		<div class="">
		  <div class="icon"><i class="fa fa-fax"></i></div>
		  <div class="data">
			{l s='Fax:' d='Shop.Theme.Global'}<br/>
			{$contact_infos.fax}
		  </div>
		</div>
	  {/if}
	  {if $contact_infos.email}
		<hr/>
		<div class="">
		  <div class="icon"><i class="fa fa-envelope-o"></i></div>
		  <div class="data email">
			{l s='Email us:' d='Shop.Theme.Global'}<br/>
		   {mailto address=$contact_infos.email encode="javascript"}
		   </div>		   
		</div>
	  {/if}
	</div>
</div>
