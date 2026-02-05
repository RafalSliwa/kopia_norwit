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
<div class="form-group row massive_edit_head">
    {assign var='massedit_selected' value=0}
    <label class="col-lg-2 form-control-label">{l s='Mass Edit template' mod='ets_productmanager'}</label>
    <div class="col-lg-8">
        <select class="list-templates-massives">
            <option value="1">---</option>
            {if $massedits}
                {foreach from= $massedits item='massedit'}
                    <option value="{$massedit.id_ets_pmn_massedit|escape:'html':'UTF-8'}"{if $id_ets_pmn_massedit==$massedit.id_ets_pmn_massedit} selected="selected" {assign var='massedit_selected' value=1}{/if}>{$massedit.name|escape:'html':'UTF-8'}</option>
                {/foreach}
            {/if}
        </select>
    </div>
    <div class="col-lg-1"><button class="btn-save-msssive-template-popup btn btn-default"{if !$massedit_selected} disabled="disabled"{/if}>{if !$massedit_selected}{l s='Save' mod='ets_productmanager'}{else}{l s='Update' mod='ets_productmanager'}{/if}</button></div>
</div>