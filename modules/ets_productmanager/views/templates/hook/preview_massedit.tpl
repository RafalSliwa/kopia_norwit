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
{$step_massedit_html nofilter}
<form id="preview_massedit_form" autocomplete="off" class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="form-wrapper">
            <div class="form-group row ets_row">
                <div class="col-lg-8 list_edit_action">
                    <div class="form-group list_action_edit">
                        <div class="panel ets_mp-panel">
                            <div class="panel-heading">
                                {l s='Edit action' mod='ets_productmanager'}
                            </div>
                            <div class="table-responsive clearfix">
                                <table class="table configuration alltab_ss list-pmn_products ets_pmn_massedit_preview">
                                    <thead>
                                        <tr class="nodrag nodrop">
                                            <th class="field">{l s='Field' mod='ets_productmanager'}</th>
                                            <th class="action">{l s='Action' mod='ets_productmanager'}</th>
                                            <th class="lang_name">{l s='Language' mod='ets_productmanager'}</th>
                                            <th class="value">{l s='Value' mod='ets_productmanager'}</th>
                                        </tr>
                                    </thead>
                                    {if $edit_actions}
                                        {foreach from=$edit_actions item='edit_action'}
                                            {if $edit_action.languages}
                                                {if count($edit_action.languages)>1}
                                                    {foreach from=$edit_action.languages key='key' item='language'}
                                                        <tr>
                                                            {if $key==0}
                                                                <td class="field" rowspan="{$edit_action.languages|count|intval}">{$edit_action.field|escape:'html':'UTF-8'}</td>
                                                                <td class="action" rowspan="{$edit_action.languages|count|intval}">{$edit_action.condition|escape:'html':'UTF-8'}</td>
                                                            {/if}
                                                            <td class="lang_name">{$language.lang_name|escape:'html':'UTF-8'}</td>
                                                            <td class="value">{$language.value_lang nofilter}</td>
                                                        </tr>
                                                    {/foreach}
                                                {else}
                                                    {foreach from=$edit_action.languages key='key' item='language'}
                                                        <tr>
                                                            <td class="field">{$edit_action.field|escape:'html':'UTF-8'}</td>
                                                            <td class="action">{$edit_action.condition|escape:'html':'UTF-8'}</td>
                                                            <td class="lang_name">{$language.lang_name|escape:'html':'UTF-8'}</td>
                                                            <td class="value">{$language.value_lang nofilter}</td>
                                                        </tr>
                                                    {/foreach}
                                                {/if}
                                            {else}
                                                <tr>
                                                    <td class="field">{$edit_action.field|escape:'html':'UTF-8'}</td>
                                                    <td class="action">{$edit_action.condition|escape:'html':'UTF-8'}</td>
                                                    <td class="lang_name">--</td>
                                                    <td class="value">{if $edit_action.condition=='active_all' || $edit_action.condition=='disable_all'}--{else}{$edit_action.value nofilter}{/if}</td>
                                                </tr>
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 list_massedit_products">
                    {$product_list nofilter}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <a class="btn btn-default pull-left btn-back-2" href="{$link->getAdminLink('AdminProductManagerMassiveEdit')|escape:'html':'UTF-8'}&startmassedit=1&id_ets_pmn_massedit={$id_ets_pmn_massedit|intval}">
                <i class="process-icon-svg svg_fill_gray svg_fill_hover_white">
                    <svg class="w_25 h_25" width="25" height="25" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 960v-128q0-26-19-45t-45-19h-502l189-189q19-19 19-45t-19-45l-91-91q-18-18-45-18t-45 18l-362 362-91 91q-18 18-18 45t18 45l91 91 362 362q18 18 45 18t45-18l91-91q18-18 18-45t-18-45l-189-189h502q26 0 45-19t19-45zm256-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                </i> {l s='Back' mod='ets_productmanager'}
            </a>
            <input name="id_ets_pmn_massedit" value="{$id_ets_pmn_massedit|intval}" type="hidden" />
            {if $edit_actions && $totalEditProducts}
                <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="submitSaveMasseEditProduct">
                    <i class="process-icon-svg svg_fill_gray svg_fill_hover_white">
                        <svg class="w_25 h_25" width="25" height="25" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M888 1184l116-116-152-152-116 116v56h96v96h56zm440-720q-16-16-33 1l-350 350q-17 17-1 33t33-1l350-350q17-17 1-33zm80 594v190q0 119-84.5 203.5t-203.5 84.5h-832q-119 0-203.5-84.5t-84.5-203.5v-832q0-119 84.5-203.5t203.5-84.5h832q63 0 117 25 15 7 18 23 3 17-9 29l-49 49q-14 14-32 8-23-6-45-6h-832q-66 0-113 47t-47 113v832q0 66 47 113t113 47h832q66 0 113-47t47-113v-126q0-13 9-22l64-64q15-15 35-7t20 29zm-96-738l288 288-672 672h-288v-288zm444 132l-92 92-288-288 92-92q28-28 68-28t68 28l152 152q28 28 28 68t-28 68z"/></svg>
                    </i>
                    {l s='Edit now' mod='ets_productmanager'}
                </button>
            {/if}
        </div>
        <div class="ets_product_popup ets_popup-save-massage">
            <div class="popup_content table">
                <div class="popup_content_tablecell">
                    <div class="popup_content_wrap" style="position: relative">
                        <span class="close_popup" title="Close">+</span>
                        <div id="block-form-popup-save-massage">
                            <div id="fieldset_0" class="panel">
                                <div class="panel-heading">{l s='Save mass edit template' mod='ets_productmanager'}</div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label required">{l s='Mass Edit name' mod='ets_productmanager'}</label>
                                        <div class="col-lg-7">
                                            <input type="text" class="form-control" name="massedit_name" id="massedit_name" value="{$massedit_name|escape:'html':'UTF-8'}" required />
                                        </div>
                                        <div class="col-lg-1">
                                            <a{if !$massedit_name} style="display:none"{/if} onclick="return confirm('{l s='Do you want to delete this mass edit template?' mod='ets_productmanager' js=1}');" class="btn btn-default pull-left btn-delete-template" href="{$link->getAdminLink('AdminProductManagerMassiveEdit')|escape:'html':'UTF-8'}&delMassedit=1&id_ets_pmn_massedit={$id_ets_pmn_massedit|intval}" title="{l s='Delete' mod='ets_productmanager'} ">
                                                  <i class="icon-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <input type="hidden" name="id_massedit" value="{$id_ets_pmn_massedit|intval}" />
                                    <button class="btn btn-default pull-left btn-cancel-template" type="button" name="btnCancel">
                                        <i class="process-icon-cancel"></i>
                                        {l s='Cancel' mod='ets_productmanager'}
                                    </button>
                                    <button id="templates_submit_btn" class="btn btn-default pull-right btn-save-template" type="submit" value="1" name="btnSubmitSaveNameMassedit" >
                                        <i class="process-icon-save"></i>
                                        {l s='Save' mod='ets_productmanager'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="fieldset_1" class="panel complete_massedit_form" style="display:none;">
    <form autocomplete="off" class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
        <div class="form-wrapper">
            <div class="clearfix"></div>
            <div class="alert-info">
                {l s='Editing...' mod='ets_productmanager'} {$totalEditProducts|intval} {l s='product(s)' mod='ets_productmanager'}
                <div class="dot-flashing"></div>
            </div>
            <div class="complete list_massedit_products">
            </div>
        </div>
        <div class="panel-footer">
            <a class="btn btn-default pull-left btn-view-log" href="{$link->getAdminLink('AdminProductManagerMassiveEditLog')|escape:'html':'UTF-8'}" style="display:none">
                <i class="process-icon-svg svg_fill_gray svg_fill_hover_white">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="eye" class="svg-inline--fa fa-eye fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg>
                </i> {l s='View all edit log' mod='ets_productmanager'}
            </a>
            <a class="btn btn-default pull-right" href="{$link->getAdminLink('AdminProductManagerMassiveEdit')|escape:'html':'UTF-8'}&editpmn_massedit=1&id_ets_pmn_massedit={$id_ets_pmn_massedit|intval}">
                <i class="process-icon-svg svg_fill_gray svg_fill_hover_white">
                    <svg class="w_25 h_25" width="25" height="25" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="sync-alt" class="svg-inline--fa fa-sync-alt fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M370.72 133.28C339.458 104.008 298.888 87.962 255.848 88c-77.458.068-144.328 53.178-162.791 126.85-1.344 5.363-6.122 9.15-11.651 9.15H24.103c-7.498 0-13.194-6.807-11.807-14.176C33.933 94.924 134.813 8 256 8c66.448 0 126.791 26.136 171.315 68.685L463.03 40.97C478.149 25.851 504 36.559 504 57.941V192c0 13.255-10.745 24-24 24H345.941c-21.382 0-32.09-25.851-16.971-40.971l41.75-41.749zM32 296h134.059c21.382 0 32.09 25.851 16.971 40.971l-41.75 41.75c31.262 29.273 71.835 45.319 114.876 45.28 77.418-.07 144.315-53.144 162.787-126.849 1.344-5.363 6.122-9.15 11.651-9.15h57.304c7.498 0 13.194 6.807 11.807 14.176C478.067 417.076 377.187 504 256 504c-66.448 0-126.791-26.136-171.315-68.685L48.97 471.03C33.851 486.149 8 475.441 8 454.059V320c0-13.255 10.745-24 24-24z"></path></svg>
                </i> 
                {l s='Edit again' mod='ets_productmanager'}
            </a>
        </div>
        <div class="ets_product_popup ets_popup-save-massage">
            <div class="popup_content table">
                <div class="popup_content_tablecell">
                    <div class="popup_content_wrap" style="position: relative">
                        <span class="close_popup" title="Close">+</span>
                        <div id="block-form-popup-save-massage">
                            <div id="fieldset_0" class="panel">
                                <div class="panel-heading">{l s='Save mass edit template' mod='ets_productmanager'}</div>
                                <div class="form-wrapper">
                                    <div class="form-group">
                                        <label class="col-lg-3 control-label required">{l s='Mass Edit name' mod='ets_productmanager'}</label>
                                        <div class="col-lg-7">
                                            <input type="text" class="form-control" name="massedit_name" id="massedit_name" value="{$massedit_name|escape:'html':'UTF-8'}" required />
                                        </div>
                                        <div class="col-lg-1">
                                            <a{if !$massedit_name} style="display:none"{/if} onclick="return confirm('{l s='Do you want to delete this mass edit template?' mod='ets_productmanager' js=1}');" class="btn btn-default pull-left btn-delete-template" href="{$link->getAdminLink('AdminProductManagerMassiveEdit')|escape:'html':'UTF-8'}&delMassedit=1&id_ets_pmn_massedit={$id_ets_pmn_massedit|intval}" title="{l s='Delete' mod='ets_productmanager'} ">
                                                  <i class="icon-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <input type="hidden" name="id_massedit" value="{$id_ets_pmn_massedit|intval}" />
                                    <button class="btn btn-default pull-left btn-cancel-template" type="button" name="btnCancel">
                                        <i class="process-icon-cancel"></i>
                                        {l s='Cancel' mod='ets_productmanager'}
                                    </button>
                                    <button id="templates_submit_btn" class="btn btn-default pull-right btn-save-template" type="submit" value="1" name="btnSubmitSaveNameMassedit" >
                                        <i class="process-icon-save"></i>
                                        {l s='Save' mod='ets_productmanager'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>