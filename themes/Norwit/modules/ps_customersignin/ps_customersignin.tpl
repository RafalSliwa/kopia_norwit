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

{hook h='displayStWishlistTopLink'}
{hook h='displayStCompareTopLink'}

<div class="user-info user-info-desktop">
  {if $logged}
    <span class="account_text">
      <a class="account" href="{$urls.pages.my_account}"
        title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">
        <span class="">{l s='My Account' d='Shop.Theme.Global'}</span>
      </a></span>
  {else}
    <span class="account_text">
      <a class="" href="{$urls.pages.my_account}"
        title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">
        {l s='Login' d='Shop.Theme.Global'}
      </a>
      /
      <a href="{$urls.pages.register}"
        data-link-action="display-register-form">{l s='Register' d='Shop.Theme.Global'}</a></span>
  {/if}
  </span>
</div>

<div class="user-info user-info-mobile dropdown js-dropdown">
  <span class="user-info-title _gray-darker" data-toggle="dropdown">
    <span class="user-icon"></span>
    <span class="user-menu">{l s='ACCOUNT' d='Shop.Theme.Catalog'}</span>
  </span>

  <ul class="dropdown-menu">
    {if $logged}
      <li>
        <a class="account dropdown-item" href="{$urls.pages.my_account}"
          title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">
          <span class="">{$customerName}</span>
        </a>
      </li>
      <li>
        <a class="logout dropdown-item" href="{$urls.actions.logout}" rel="nofollow">
          {l s='Logout' d='Shop.Theme.Global'}
        </a>
      </li>
    {else}
      <li>
        <a class="dropdown-item" href="{$urls.pages.my_account}"
          title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}" rel="nofollow">
          {l s='Login' d='Shop.Theme.Global'}
        </a>
      </li>
      <li>
        <a href="{$urls.pages.register}" data-link-action="display-register-form">
          {l s='Register' d='Shop.Theme.Global'}
        </a>
      </li>
    {/if}
  </ul>
</div>