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
<div id="blockEmailSubscription_{$hookName}" class="block_newsletter">
  <div class="row">
    <div class="newsletter-detail">
      <h3 class="title"><span class="news1">{l s='Subscribe to newsletter' d='Shop.Theme.Global'}</span></h3>
      <span class="desc">{l s='Subscribe to latest newsletter to get news about special discounts.' d='Shop.Theme.Global'}</span>
    </div>
    <div class="block_content">
      <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
          <div class="newsletter-form">
           <input
              class="btn btn-primary pull-xs-right hidden-xs-down"
              name="submitNewsletter"
              type="submit"
              value="{l s='Subscribe' d='Shop.Theme.Actions'}"
            >
            <input
              class="btn btn-primary pull-xs-right hidden-sm-up"
              name="submitNewsletter"
              type="submit"
              value="{l s='OK' d='Shop.Theme.Actions'}"
            >
            <div class="input-wrapper">
              <input
                name="email"
                type="text"
                value="{$value}"
                placeholder="{l s='Your Email Address' d='Shop.Forms.Labels'}"
                aria-labelledby="block-newsletter-label"
                required
              >
            </div>
            <input type="hidden" name="blockHookName" value="{$hookName}" />
            <input type="hidden" name="action" value="0">
            <div class="clearfix"></div>
          </div>
         
          {if $msg}
			<p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
			  {$msg}
			</p>
		  {/if}
		  <div class="newsletter-message">
              {if $conditions}
                <p>{$conditions}</p>
              {/if}
          </div>
              {hook h='displayNewsletterRegistration'}
		  {if isset($id_module)}
                {hook h='displayGDPRConsent' id_module=$id_module}
              {/if}
      </form>
    </div>
  </div>
</div> 