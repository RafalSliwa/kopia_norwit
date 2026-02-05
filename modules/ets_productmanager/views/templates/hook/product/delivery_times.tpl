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
<form class="defaultForm form-horizontal etspm-form-delivery-time" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="panel-heading">{l s='Edit delivery time' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            <div class="form-group form-group-delivery-time">
                <div class="delivery-time-chooses">
                    <div class="radio">
                        <label class="">
                            <input id="additional_delivery_times_0" name="additional_delivery_times" value="0" type="radio"{if $additional_delivery_times==0} checked="checked"{/if}/>
                            {l s='None' mod='ets_productmanager'}
                        </label>
                    </div>
                    <div class="radio">
                        <label class="">
                            <input id="additional_delivery_times_1" name="additional_delivery_times" value="1" type="radio"{if $additional_delivery_times==1} checked="checked"{/if} />
                            {l s='Default delivery time' mod='ets_productmanager'}
                        </label>
                    </div>
                    <div class="radio">
                        <label class="">
                            <input id="additional_delivery_times_2" name="additional_delivery_times" value="2" type="radio"{if $additional_delivery_times==2} checked="checked"{/if} />
                            {l s='Specific delivery time to this product' mod='ets_productmanager'}
                        </label>
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
            <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitDeliveryTimesProduct">
            <i class="process-icon-save ets_svg_process">
                    <svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M512 1536h768v-384h-768v384zm896 0h128v-896q0-14-10-38.5t-20-34.5l-281-281q-10-10-34-20t-39-10v416q0 40-28 68t-68 28h-576q-40 0-68-28t-28-68v-416h-128v1280h128v-416q0-40 28-68t68-28h832q40 0 68 28t28 68v416zm-384-928v-320q0-13-9.5-22.5t-22.5-9.5h-192q-13 0-22.5 9.5t-9.5 22.5v320q0 13 9.5 22.5t22.5 9.5h192q13 0 22.5-9.5t9.5-22.5zm640 32v928q0 40-28 68t-68 28h-1344q-40 0-68-28t-28-68v-1344q0-40 28-68t68-28h928q40 0 88 20t76 48l280 280q28 28 48 76t20 88z"/></svg>
                </i>
                {l s='Save' mod='ets_productmanager'}
            </button>
        </div>
    </div>
</form>


