{*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    ING Lease Now
 *  @copyright 2022-now ING Lease Now
 *  @license   GNU General Public License
 */
*}

<div class="leasenow-wrap bootstrap">

    <div class="leasenow-half">
        {if $leasenow_msg}
            <div class="bootstrap">
                <div class="{if $leasenow_msg.type == 'error'}alert alert-danger{else}alert alert-success{/if}">{$leasenow_msg.message|escape:'htmlall':'UTF-8'}</div>
            </div>
        {/if}
        <form id="leasenow-configuration" method="post" name="leasenow-configuration">
            <div id="leasenow">

                <div id="leasenow_data">
                    <label for="leasenow_store_id">{l s='Store ID' mod='leasenow'}</label>
                    <div class="margin-form">
                        <input type="text" class="text" name="leasenow_store_id" id="leasenow_store_id"
                               value="{$leasenow_store_id|escape:'htmlall':'UTF-8'}"/>
                    </div>
                    <label for="leasenow_service_id">{l s='Secret' mod='leasenow'}</label>
                    <div class="margin-form">
                        <input type="text" class="text" name="leasenow_secret" id="leasenow_secret"
                               value="{$leasenow_secret|escape:'htmlall':'UTF-8'}"/>
                    </div>

                    <hr/>

                    <label for="leasenow_sandbox">{l s='Sandbox' mod='leasenow'}</label>
                    <div class="margin-form">
                        <select name="leasenow_sandbox" class="leasenow-sel-en">
                            <option value="0"
                                    {if $leasenow_sandbox == 0}selected{/if}>{l s='Off' mod='leasenow'}</option>
                            <option value="1"
                                    {if $leasenow_sandbox == 1}selected{/if}>{l s='On' mod='leasenow'}</option>
                        </select>
                    </div>

                    <label for="leasenow_sandbox_store_id">{l s='Sandbox store ID' mod='leasenow'}</label>
                    <div class="margin-form">
                        <input type="text" class="text" name="leasenow_sandbox_store_id" id="leasenow_sandbox_store_id"
                               value="{$leasenow_sandbox_store_id|escape:'htmlall':'UTF-8'}"/>
                    </div>
                    <label for="leasenow_sandbox_secret">{l s='Sandbox secret' mod='leasenow'}</label>
                    <div class="margin-form">
                        <input type="text" class="text" name="leasenow_sandbox_secret" id="leasenow_sandbox_secret"
                               value="{$leasenow_sandbox_secret|escape:'htmlall':'UTF-8'}"/>
                    </div>

                    <hr/>
                    <label for="leasenow_payment_title">{l s='Payment title' mod='leasenow'}</label>
                    <div class="margin-form">
                        <input type="text" class="text" name="leasenow_payment_title" id="leasenow_payment_title"
                               value="{$leasenow_payment_title|escape:'htmlall':'UTF-8'}"
                               placeholder="{$leasenow_payment_title_default|escape:'htmlall':'UTF-8'}"/>
                    </div>
                    <label for="leasenow_button_checkout">{l s='Display as payment method' mod='leasenow'}</label>
                    <div class="margin-form">
                        <select name="leasenow_button_checkout" class="leasenow-sel-en">
                            <option value="0"
                                    {if $leasenow_button_checkout == 0}selected{/if}>{l s='Off' mod='leasenow'}</option>
                            <option value="1"
                                    {if $leasenow_button_checkout == 1}selected{/if}>{l s='On' mod='leasenow'}</option>
                        </select>
                    </div>
                    <hr/>

                    <label for="leasenow_button_product">{l s='Display on product page' mod='leasenow'}</label>
                    <div class="margin-form">
                        <select name="leasenow_button_product" class="leasenow-sel-en">
                            <option value="0"
                                    {if $leasenow_button_product == 0}selected{/if}>{l s='Off' mod='leasenow'}</option>
                            <option value="1"
                                    {if $leasenow_button_product == 1}selected{/if}>{l s='On' mod='leasenow'}</option>
                        </select>
                    </div>
                    <label for="leasenow_button_product_scale">{l s='Image size as a percentage' mod='leasenow'}</label>
                    <div class="margin-form">
                        <input type="text" class="text" name="leasenow_button_product_scale" id="leasenow_button_product_scale"
                               value="{$leasenow_button_product_scale|escape:'htmlall':'UTF-8'}"
                               placeholder="{$leasenow_button_product_scale|escape:'htmlall':'UTF-8'}"/>
                    </div>
                    <hr/>
                    <label for="leasenow_button_cart">{l s='Display on cart page' mod='leasenow'}</label>
                    <div class="margin-form">
                        <select name="leasenow_button_cart" class="leasenow-sel-en">
                            <option value="0"
                                    {if $leasenow_button_cart == 0}selected{/if}>{l s='Off' mod='leasenow'}</option>
                            <option value="1"
                                    {if $leasenow_button_cart == 1}selected{/if}>{l s='On' mod='leasenow'}</option>
                        </select>
                    </div>
                    <label for="leasenow_button_cart_scale">{l s='Image size as a percentage' mod='leasenow'}</label>
                    <div class="margin-form">
                        <input type="text" class="text" name="leasenow_button_cart_scale" id="leasenow_button_cart_scale"
                               value="{$leasenow_button_cart_scale|escape:'htmlall':'UTF-8'}"
                               placeholder="{$leasenow_button_cart_scale|escape:'htmlall':'UTF-8'}"/>
                    </div>
                    <hr/>
                    <label for="leasenow_rel_no_follow">{l s='Add nofollow to link' mod='leasenow'}</label>
                    <div class="margin-form">
                        <select name="leasenow_rel_no_follow" class="leasenow-sel-en">
                            <option value="0"
                                    {if $leasenow_rel_no_follow == 0}selected{/if}>{l s='Off' mod='leasenow'}</option>
                            <option value="1"
                                    {if $leasenow_rel_no_follow == 1}selected{/if}>{l s='On' mod='leasenow'}</option>
                        </select>
                    </div>
                </div>
                <div class="margin-form">
                    <input id="submit-leasenow-configuration" type="submit" class="btn btn-primary" name="submitLeaseNow"
                           value="{l s='Save' mod='leasenow'}"/>
                </div>
            </div>
        </form>
    </div>
</div>
