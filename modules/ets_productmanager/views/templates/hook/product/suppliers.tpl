{*
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
*}
<form class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="panel-heading">{l s='Edit supplier' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            <div id="html_form_supplier">
                <div class="row">
                    <div class="col-md-12">
                        <h2>{l s='Suppliers' mod='ets_productmanager'}</h2>
                        <p class="alert alert-info">{l s='This interface allows you to specify the suppliers of the current product and its combinations, if any.' mod='ets_productmanager'}<br />
                        {l s='You can specify supplier references according to previously associated suppliers.' mod='ets_productmanager'}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group panel-default">
                        <div class="panel-body">
                            <div>
                                <table id="form_step6_suppliers" class="table">
                                    <thead class="thead-default">
                                        <tr>
                                            <th width="70%">{l s='Choose the suppliers associated with this product' mod='ets_productmanager'}</th>
                                            <th width="30%">{l s='Default supplier' mod='ets_productmanager'}</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        {foreach from=$suppliers item='supplier'}
                                            <tr>
                                                <td>
                                                    <div class="checkbox">
                                                        <label for="form_step6_suppliers_{$supplier.id_supplier|intval}">
                                                            <input id="form_step6_suppliers_{$supplier.id_supplier|intval}" name="id_suppliers[]" value="{$supplier.id_supplier|intval}"{if $supplier.checked} checked="checked"{/if} type="checkbox" class="change_supplier" />
                                                            {$supplier.name|escape:'html':'UTF-8'}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="radio">
                                                        <label for="form_step6_default_supplier_{$supplier.id_supplier|intval}">
                                                            <input id="form_step6_default_supplier_{$supplier.id_supplier|intval}" name="id_supplier_default" value="{$supplier.id_supplier|intval}" type="radio" {if $id_supplier_default==$supplier.id_supplier} checked="checked"{/if} {if !$supplier.checked} style="display:none"{/if}/>
                                                            {$supplier.name|escape:'html':'UTF-8'}
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-12">
                        <div id="supplier_combination_collection" class="">
                            <h2>{l s='Supplier reference(s)' mod='ets_productmanager'}</h2>
                            <p class="alert alert-info">{l s='You can specify product reference(s) for each associated supplier. Click "Save" after changing selected suppliers to display the associated product references.' mod='ets_productmanager'}</p>
                            <div class="row">
                                {if $suppliers}
                                    {foreach from=$suppliers item='supplier'}
                                        {$supplier.product_suppliers nofilter}
                                    {/foreach}
                                {/if}
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="button" name="btnCancel" class="btn btn-default pull-left">
            <i class="process-icon-cancel svg_process-icon">
                <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg>

            </i> {l s='Cancel' mod='ets_productmanager'}</button>
            <input type="hidden" name="id_product" value="{$id_product|intval}" />
            <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitSuppliersProduct">
                <i class="process-icon-save ets_svg_process">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                </i>
                {l s='Save' mod='ets_productmanager'}
            </button>
        </div>
    </div>
</form>