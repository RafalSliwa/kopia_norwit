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

<div class="block-contact col-md-4 links wrapper">
  
   		<h3 class="text-uppercase block-contact-title hidden-sm-down"><a href="{$urls.pages.stores}">{l s='Contact us' d='Shop.Theme.Global'}</a></h3>
      
		<div class="title clearfix hidden-md-up" data-target="#block-contact_list" data-toggle="collapse">
		  <span class="h3">{l s='Contact us' d='Shop.Theme.Global'}</span>
		  <span class="pull-xs-right">
			  <span class="navbar-toggler collapse-icons">
				<i class="fa-icon add"></i>
				<i class="fa-icon remove"></i>
			  </span>
		  </span>
		</div>
	  
	  <ul id="block-contact_list" class="collapse">
	  <li class="contact">
	  	<i class="fa fa-map-marker"></i>
	  	<span>{$contact_infos.address.formatted nofilter}</span>
      </li>
      
      {if $contact_infos.email && $display_email}
        <li>
		<i class="fa fa-envelope-o"></i>
	  <span>{mailto address=$contact_infos.email encode="javascript"}</span>
		</li>
      {/if}


      {if $contact_infos.fax}
        <li>
		<i class="fa fa-fax"></i>
        {* [1][/1] is for a HTML tag. *}
        {l
          s='[1]%fax%[/1]'
          sprintf=[
            '[1]' => '<span>',
            '[/1]' => '</span>',
            '%fax%' => $contact_infos.fax
          ]
          d='Shop.Theme.Global'
        }
		</li>
      {/if}
      	  {if $contact_infos.phone}
        <li class="phone">
		<i class="fa fa-phone"></i>
        {* [1][/1] is for a HTML tag. *}
        {l s='[1]%phone%[/1]'
          sprintf=[
          '[1]' => "<a href='tel:{$contact_infos['phone']|replace:' ':''}'>",
          '[/1]' => '</a>',
          '%phone%' => $contact_infos.phone
          ]
          d='Shop.Theme.Global'
        }
		</li>
      {/if}
      
	  </ul>
  
</div>