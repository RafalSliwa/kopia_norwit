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

{extends file=$layout}

{block name='content'}

    {capture name=path}{l s='Payment' mod='leasenow'}{/capture}
    <div class="text-center">{l s='We have received your application form. If you have any additional questions, please do not hesitate to contact us.' mod='leasenow'}</div>
    <br>
    {if isset($ga_key) && $ga_key}{include file="module:leasenow/views/templates/front/_ga.tpl" ga_key=$ga_key ga_conversion=$ga_conversion}{/if}
{/block}
