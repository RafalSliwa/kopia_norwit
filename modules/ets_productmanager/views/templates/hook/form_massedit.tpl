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
<script type="text/javascript">
    var Matching_products_text = '{l s='Matching products' mod='ets_productmanager' js=1}';
    var No_product_available_text = '{l s='No products found' mod='ets_productmanager' js=1}';
</script>
{if !$edit_template}
    {$step_massedit_html nofilter}
<form id="massedit_form" class="defaultForm form-horizontal" autocomplete="off" action="{$link->getAdminLink('AdminProductManagerMassiveEdit')|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
<div class="block-massedit-form">
{/if}   
    <div id="fieldset_0" class="panel">
        <div class="form-wrapper">
            <div class="form-group row ets_row">
                <div class="col-lg-8 list_edit_action">
                    <div class="panel ets_mp-panel">
                        <div class="panel-heading">{l s='Filter conditions' mod='ets_productmanager'}</div>
                        <div class="table-responsive clearfix" id="block-filter-products"{if !$conditions} style="display:none"{/if}>
                            <div class="form-group row">
                                <label class="control-label col-lg-4">{l s='How to combine filter conditions' mod='ets_productmanager'}</label>
                                <div class="col-lg-8">
                                    <div class="radio">
                                        <label>
                                            <input name="type_combine_condition" id="type_combine_condition_and" value="and"{if !$type_combine_condition || $type_combine_condition=='and'} checked="checked"{/if} type="radio">
                                            {l s='AND' mod='ets_productmanager'}
                                        </label>
                                    </div>
                                    <div class="radio ">
                                        <label>
                                            <input name="type_combine_condition" id="type_combine_condition_or" value="or"{if $type_combine_condition=='or'} checked="checked"{/if} type="radio">
                                            {l s='OR' mod='ets_productmanager'}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="id_ets_pmn_massedit" value="{$id_ets_pmn_massedit|intval}" name="id_ets_pmn_massedit" />
                            <input type="hidden" id="product_excluded" value="{$product_excluded|escape:'html':'UTF-8'}" name="product_excluded" />
                            <div class="ets_list_form_massedit">
                                <div class="form-group row row_massedit_header header">
                                    <div class="col-lg-3 col-md-3">{l s='Product attribute' mod='ets_productmanager'}</div>
                                    <div class="col-lg-2 col-md-3">{l s='Operator' mod='ets_productmanager'}</div>
                                    <div class="col-lg-6 col-md-5 col massedit_operator_value">{l s='Value' mod='ets_productmanager'}</div>
                                    <div class="col-lg-1 col-md-1 text-center">{l s='Delete' mod='ets_productmanager'}</div>
                                </div>
                                {if $conditions}
                                    {foreach from=$conditions item='condition'}
                                        {$condition.row_html nofilter}
                                    {/foreach}
                                {else}
                                    {$row_form_massedit nofilter}
                                {/if}
                            </div>
                            <div class="help-box"></div>
                            <button class="btn btn-default btn-add-filter" type="button">
                                {l s='Add condition' mod='ets_productmanager'}
                            </button>
                        </div>
                        <div id="block-wait-filter-products"{if $conditions} style="display:none"{/if}>
                            <div class="alert alert-warning">{l s='Please add first condition to start filtering products' mod='ets_productmanager'}</div>
                            <button class="btn btn-default btn-add-filter-product" type="button">
                                {l s='Add condition' mod='ets_productmanager'}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 list_massedit_products">
                    {if $id_ets_pmn_massedit}
                        {$product_list nofilter} 
                    {else}
                        <div class="panel ets_mp-panel">
                            <div class="panel-heading">
                                {l s='Matching products' mod='ets_productmanager'}
                                <span class="panel-heading-action"> </span>
                            </div>
                            <div class="alert alert-warning">{l s='No products found' mod='ets_productmanager'}</div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button id="module_form_submit_btn" class="btn btn-default pull-right" type="submit" value="1" name="btnSubmitMassedit"{if !$id_ets_pmn_massedit} disabled="disabled"{/if} >
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
                                            <a{if $deleted==1 || !$id_ets_pmn_massedit} style="display:none"{/if} onclick="return confirm('{l s='Do you want to delete this mass edit template?' mod='ets_productmanager' js=1}');" class="btn btn-default pull-left btn-delete-template" href="{$link->getAdminLink('AdminProductManagerMassiveEdit')|escape:'html':'UTF-8'}&delMassedit=1&id_ets_pmn_massedit={$id_ets_pmn_massedit|intval}" {l s='Delete' mod='ets_productmanager'} >
                                                  <i class="icon-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <button class="btn btn-default pull-left btn-cancel-template" type="button" name="btnCancel">
                                        <i class="process-icon-cancel"></i>
                                        {l s='Cancel' mod='ets_productmanager'}
                                    </button>
                                    <button id="templates_submit_btn" class="btn btn-default pull-right btn-save-template" type="submit" value="1" name="btnSubmitMassedit" >
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
{if !$edit_template}
</div>
</form>
<div class="form-add-filter" style="display:none">
    {$row_form_massedit nofilter}
</div>
{/if}
<script type="text/javascript">
    $(document).ready(function(){
        if($('.category-tree .has-child').length)
        {
            $('.category-tree .has-child').each(function(){
                if($(this).next('.children').find('li').length==0)
                    $(this).removeClass('has-child');
            })
        }
        if($('.category-tree .category').length)
        {
            $('.category-tree .category').each(function(){
                if($(this).parent().parent().next('.children').length)
                {
                    if($(this).parent().parent().next('.children').length)
                    {
                        $(this).parent().parent().next('.children').show();
                        $(this).parent().addClass('opend');
                    }   
                }
            });
        }
    });
</script>