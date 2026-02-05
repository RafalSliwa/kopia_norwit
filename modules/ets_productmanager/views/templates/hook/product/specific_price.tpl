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
<div class="card card-block">
    <h4>
        <b>{l s='Specific price conditions' mod='ets_productmanager'}</b>
    </h4>
    <fieldset class="specific_price">
        <label>{l s='For' mod='ets_productmanager'}</label>
        <div class="row form-group">
            <div class="specific_price_id_currency col-lg-3">
                <select name="specific_price_id_currency" class="form-control">
                    <option value="0">{l s='All currencies' mod='ets_productmanager'}</option>
                    {if $currencies}
                        {foreach from=$currencies item='currency'}
                            <option value="{$currency.id_currency|intval}"{if $specific_price->id_currency==$currency.id_currency} selected="selected"{/if} >{$currency.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
            <div class="specific_price_id_country col-lg-3">
                <select name="specific_price_id_country" class="form-control">
                    <option value="0">{l s='All countries' mod='ets_productmanager'}</option>
                    {if $countries}
                        {foreach from = $countries item='country'}
                            <option value="{$country.id_country|intval}"{if $specific_price->id_country==$country.id_country} selected="selected"{/if}>{$country.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
            <div class="specific_price_id_group col-lg-3">
                <select name="specific_price_id_group" class="form-control">
                    <option value="0">{l s='All groups' mod='ets_productmanager'}</option>
                    {if $groups}
                        {foreach from=$groups item='group'}
                            <option value="{$group.id_group|intval}" {if $specific_price->id_group==$group.id_group} selected="selected"{/if}>{$group.name|escape:'html':'UTF-8'}</option>
                        {/foreach}
                    {/if}
                </select>
            </div>
            <div class="specific_price_id_customer col-lg-3">
                <input id="specific_price_id_customer_hide" value="{$specific_price->id_customer|intval}" type="hidden" name="specific_price_id_customer" />
                <input type="text" placeholder="{l s='All customers' mod='ets_productmanager'}" value="{if $specific_price->id_customer}{$specific_price->id_customer|intval}{/if}" id="specific_price_id_customer" />
                {if $specific_price->id_customer}
                    <div class="customer_selected">{$specific_price_customer->firstname|escape:'html':'UTF-8'}&nbsp;{$specific_price_customer->lastname|escape:'html':'UTF-8'}&nbsp;<span class="delete_customer_search">delete</span></div>
                {/if}
            </div>
        </div>
        <div class="row">
            <div id="specific-price-combination-selector" class="col-lg-6 ">
                <fieldset class="form-group">
                    <label>{l s='Combinations' mod='ets_productmanager'}</label>
                    <select name="specific_price_id_product_attribute">
                        <option value="0">{l s='Apply to all combinations' mod='ets_productmanager'}</option>
                        {if $productAttributes}
                            {foreach from = $productAttributes item = 'productAttribute'}
                                <option value="{$productAttribute.id_product_attribute|intval}"{if $specific_price->id_product_attribute==$productAttribute.id_product_attribute} selected="selected"{/if} >{$productAttribute.name_attribute|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        {/if}
                    </select>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label>{l s='Available from' mod='ets_productmanager'}</label>
                    <div class="input-group datepicker">
                        <input id="" class="form-control ets_datepicker" name="specific_price_from" autocomplete="off" placeholder="YYYY-MM-DD" value="{if $specific_price->from!='0000-00-00 00:00:00'}{$specific_price->from|escape:'html':'UTF-8'}{/if}" type="text" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                    <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/></svg>

                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label>{l s='Available to' mod='ets_productmanager'}</label>
                    <div class="input-group datepicker">
                        <input id="" class="form-control ets_datepicker" name="specific_price_to" autocomplete="off" placeholder="YYYY-MM-DD" value="{if $specific_price->to!='0000-00-00 00:00:00'}{$specific_price->to|escape:'html':'UTF-8'}{/if}" type="text" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                    <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M192 1664h288v-288h-288v288zm352 0h320v-288h-320v288zm-352-352h288v-320h-288v320zm352 0h320v-320h-320v320zm-352-384h288v-288h-288v288zm736 736h320v-288h-320v288zm-384-736h320v-288h-320v288zm768 736h288v-288h-288v288zm-384-352h320v-320h-320v320zm-352-864v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm736 864h288v-320h-288v320zm-384-384h320v-288h-320v288zm384 0h288v-288h-288v288zm32-480v-288q0-13-9.5-22.5t-22.5-9.5h-64q-13 0-22.5 9.5t-9.5 22.5v288q0 13 9.5 22.5t22.5 9.5h64q13 0 22.5-9.5t9.5-22.5zm384-64v1280q0 52-38 90t-90 38h-1408q-52 0-90-38t-38-90v-1280q0-52 38-90t90-38h128v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h384v-96q0-66 47-113t113-47h64q66 0 113 47t47 113v96h128q52 0 90 38t38 90z"/></svg>

                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-2">
                <fieldset class="form-group">
                    <label>{l s='Starting at' mod='ets_productmanager'}</label>
                    <div class="input-group">
                        <input id="specific_price_from_quantity" class="form-control" name="specific_price_from_quantity" value="{if $specific_price->from_quantity}{$specific_price->from_quantity|intval}{else}1{/if}" type="text" />
                        <div class="input-group-append">
                            <span class="input-group-text">{l s='Unit(s)' mod='ets_productmanager'}</span>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <h4><b>{l s='Impact on price' mod='ets_productmanager'}</b></h4>
        <div class="row">
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label>{l s='Product price (tax excl.)' mod='ets_productmanager'}</label>
                    <div class="input-group money-type">
                        <div class="input-group-prepend">
                            <span class="input-group-text">{$default_currency->sign|escape:'html':'UTF-8'} </span>
                        </div>
                        <input class="form-control text-sm-right" type="text" name="specific_price_product_price" {if !$specific_price->price || $specific_price->price==-1}disabled="disabled"{/if} value="{if $specific_price->price && $specific_price->price!=-1}{$specific_price->price|floatval}{/if}" />
                    </div>
                </fieldset>
            </div>
            <div class="col-md-3">
                <fieldset class="form-group specific_price_leave_bprice">
                    <label>&nbsp;</label>
                    <div class="checkbox">
                        <label>
                            <input id="specific_price_leave_bprice" name="specific_price_leave_bprice" value="1"{if !$specific_price->price || $specific_price->price==-1} checked="checked"{/if} type="checkbox" />
                            {l s='Leave initial price' mod='ets_productmanager'}
                        </label>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label class="required">{l s='Apply a discount of' mod='ets_productmanager'}</label>
                    <div class="input-group money-type">
                          <div class="input-group-prepend">
                                <span class="input-group-text">{if $specific_price->reduction_type=='amount' || !$specific_price->reduction_type}{$default_currency->sign|escape:'html':'UTF-8'}{else}%{/if} </span>
                          </div>
                          <input id="specific_price_sp_reduction" class="form-control text-sm-right" name="specific_price_sp_reduction" value="{if $specific_price->reduction_type=='amount'}{$specific_price->reduction|escape:'html':'UTF-8'}{else}{$specific_price->reduction*100|escape:'html':'UTF-8'}{/if}" type="text" />
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label class="xs-hide">&nbsp;</label>
                    <select id="specific_price_sp_reduction_type" name="specific_price_sp_reduction_type" class="custom-select">
                        <option value="amount"{if $specific_price->reduction_type=='amount'} selected="selected"{/if}>{$default_currency->sign|escape:'html':'UTF-8'}</option>
                        <option value="percentage"{if $specific_price->reduction_type=='percentage'} selected="selected"{/if} >%</option>
                    </select>
                </fieldset>
            </div>
            <div class="col-lg-3">
                <fieldset class="form-group">
                    <label class="xs-hide">&nbsp;</label>
                    <select id="specific_price_sp_reduction_tax" name="specific_price_sp_reduction_tax" class="custom-select" style="">
                        <option value="0"{if $specific_price->reduction_tax==0} selected="selected"{/if}>{l s='Tax excluded' mod='ets_productmanager'}</option>
                        <option value="1"{if $specific_price->reduction_tax==1} selected="selected"{/if}>{l s='Tax included' mod='ets_productmanager'}</option>
                    </select>
                </fieldset>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 text-sm-right">
                <button type="button" id="specific_price_cancel" name="specific_price_cancel" class="btn-outline-secondary js-cancel btn">{l s='Cancel' mod='ets_productmanager'}</button>
                <input type="hidden" value="1" name="submitSavePecificPrice" />
                <input name="id_product" value="{$id_product|intval}" type="hidden"/>
                <input type="hidden" name="id_specific_price" id="id_specific_price" value="{$specific_price->id|intval}" />
                <button type="button" id="specific_price_save" name="specific_price_save" class="btn-outline-primary js-save btn">{l s='Apply' mod='ets_productmanager'}</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </fieldset>
</div>