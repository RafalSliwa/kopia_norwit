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
<div class="form-group">
    <h2>{l s='Ceneo feed' mod='ceneo_xml'}</h2>
    <div class="form-check mb-3">
        <input value="1" name="exclude" type="checkbox" class="form-check-input"
               id="exclude_product"{if $exclude_from_ceneo|intval == 1} checked{/if}>
        <label class="form-check-label" for="exclude_product">
	        {l s='Exclude product from Ceneo feed' mod='ceneo_xml'}
        </label>
    </div>

    <div class="form-check mb-3">
        <label class="form-check-label" for="ceneo_basket">
	        {l s='Visibility of the offer in the Ceneo Buy Now service' mod='ceneo_xml'}
        </label>
        <select name="basket" class="form-control" id="ceneo_basket">
            <option{if $ceneo_basket|intval == 2} selected{/if} value="2">
	            {l s='Default' mod='ceneo_xml'}
            </option>
            <option{if $ceneo_basket|intval == 1} selected{/if} value="1">
	            {l s='Yes' mod='ceneo_xml'}
            </option>
            <option{if $ceneo_basket|intval == 0} selected{/if}
		            value="0">
	            {l s='No' mod='ceneo_xml'}
            </option>
        </select>
    </div>

    <div class="form-check">

        <label class="form-check-label" for="avail">
	        {l s='Ceneo availability' mod='ceneo_xml'}
        </label>
        <select name="avail" class="form-control" id="ceneo_avail">
            {foreach from=$availabilities item=value}


		            <option{if $ceneo_avail == $value.key|intval} selected{/if}
				            value="{$value.key|intval}">{$value.name|escape:'html':'UTF-8'}</option>


            {/foreach}
        </select>
    </div>

</div>

{if $info}
    <div class="alert alert-info">
	    {$info|escape:'htmlall':'UTF-8'}
    </div>
{/if}
