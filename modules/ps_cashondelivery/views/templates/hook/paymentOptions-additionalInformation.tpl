{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

<section id="ps_cashondelivery-paymentOptions-additionalInformation">
  {if isset($cod_disabled) && $cod_disabled}
    <p style="color: #d9534f; font-weight: 600; margin: 10px 0; background: #ffe6e6; padding: 10px; border-left: 4px solid #d9534f;">
      ⚠️ Płatność przy odbiorze dostępna dla zamówień do {$cod_max_amount} zł brutto
    </p>
    <p style="color: #999; font-size: 0.9em; margin: 5px 0 10px 0;">
      Wartość koszyka: {$cart_total} zł - przekroczono limit.
    </p>
  {else}
    <p>{l s='You pay for the merchandise upon delivery' d='Modules.Cashondelivery.Shop'}</p>
    <p style="color: #666; font-size: 0.9em;">Dostępna dla zamówień do {if isset($cod_max_amount)}{$cod_max_amount}{else}15 000{/if} zł brutto</p>
  {/if}
</section>
