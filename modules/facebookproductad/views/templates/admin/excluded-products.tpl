{*
*
* Dynamic Ads + Pixel
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*}
<div class="bootstrap" id="gmc" style="min-width: 300px;">
    <h4 class="text-center"><i class="fa fa-exclamation-circle"></i> {l s='Excluded products by this rule' mod='facebookproductad'} </h4>
    <div class="mt-3"></div>
    <div class="clr_hr"></div>
    <div class="clr_20"></div>

    {if !empty($aProductsData)}
        <table class="table table-bordered">
            <thead>
            <th class="text-center"><b>{l s='id' mod='facebookproductad'}</b></th>
            <th class="text-center"><b>{l s='Product name' mod='facebookproductad'}</b></th>
            </thead>
            <tbody>
            {foreach from=$aProductsData item=aProduct key=sKey}
                <tr class="text-center">
                    <td> {$aProduct.id|escape:'htmlall':'UTF-8'} </td>
                    <td> {$aProduct.name|escape:'htmlall':'UTF-8'}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    {else}
        <p class="alert alert-danger">
            {l s='No product affected by this rule' mod='facebookproductad'}
        </p>
    {/if}
</div>
