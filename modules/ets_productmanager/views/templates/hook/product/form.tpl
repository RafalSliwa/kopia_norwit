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
{if isset($step_massedit_html)}
    {$step_massedit_html nofilter}
{/if}
<form id="ets_pmn_product_form" action="" method="post" enctype="multipart/form-data">
    <div id="fieldset_0" class="panel">
        <div class="form-wrapper">
            <div class="form-group row ets_row">
                <div class="col-lg-8 list_edit_action">
                    <div class="panel ets_mp-panel">
                        <div class="panel-heading">{l s='Edit action' mod='ets_productmanager'}</div>
                        <div class="table-responsive clearfix">
                            <div class="col-lg-3 ets_pmn_product_tab_left">
                                <ul class="ets_pmn_product_tab">
                                    {foreach from=$product_tabs item='product_tab'}
                                        <li class="ets_pmn_tab{if $current_tab==$product_tab.tab} active{/if}" data-tab="{$product_tab.tab|strtolower|escape:'html':'UTF-8'}">{$product_tab.name|escape:'html':'UTF-8'}</li>
                                    {/foreach}
                                </ul>
                            </div>
                            <div class="col-lg-9 ets_pmn_product_tab_right">
                                <div class="ets_pmn_product_tab_content">
                                    <div class="ets_pmn-form-content">
                                        {foreach from=$product_tabs item='product_tab'}
                                            <div class="ets_pmn_tab_content {$product_tab.tab|strtolower|escape:'html':'UTF-8'}{if $current_tab==$product_tab.tab} active{/if}" data-tab="{$product_tab.tab|strtolower|escape:'html':'UTF-8'}">
                                                {$product_tab.content_html nofilter}        
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
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
            <a class="btn btn-default btn-back-1" href="{$link->getAdminLink('AdminProductManagerMassiveEdit')|escape:'html':'UTF-8'}&editpmn_massedit=1&id_ets_pmn_massedit={$id_ets_pmn_massedit|intval}" title="">
                <i class="process-icon-svg svg_fill_gray svg_fill_hover_white">
                    <svg class="w_25 h_25" width="25" height="25" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1408 960v-128q0-26-19-45t-45-19h-502l189-189q19-19 19-45t-19-45l-91-91q-18-18-45-18t-45 18l-362 362-91 91q-18 18-18 45t18 45l91 91 362 362q18 18 45 18t45-18l91-91q18-18 18-45t-18-45l-189-189h502q26 0 45-19t19-45zm256-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>
                </i> {l s='Back' mod='ets_productmanager'}
            </a>
            <input type="hidden" name="id_ets_pmn_massedit" value="{$id_ets_pmn_massedit|intval}" />
            <button name="submitSaveMasseActionEdit" type="submit" class="btn btn-default form-control-submit pull-right">
                <i class="process-icon-next"></i> 
                {l s='Next' mod='ets_productmanager'}
            </button>
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