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

{capture name=path}{l s='Payment' mod='imoje'}{/capture}
<div class="text-center" style="text-align:center; margin-bottom:25px">
		<span id="text-tip">
                {l s='Please wait, in the next step you will proceed leasing.' mod='leasenow'}
		</span>
</div>
{if isset($ga_key) && $ga_key}{include file="module:leasenow/views/templates/front/_ga.tpl" ga_key=$ga_key}{/if}
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function (event) {
        if (window.location.hash === '#processed') {
            window.location.replace("{$checkout_link|escape:'htmlall':'UTF-8'}");
            document.getElementById('text-tip').innerHTML = '{$text_return_to_checkout|escape:'htmlall':'UTF-8'}';
        } else {
            window.location.hash = '#processed';
            window.location.replace("{$redirect_url|escape:'htmlall':'UTF-8'}");
        }
    });
</script>
