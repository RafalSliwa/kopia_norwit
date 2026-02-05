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
        <div class="panel-heading">{l s='Edit module logo' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="form-control-label">{l s='Logo' mod='ets_productmanager'}</label>
                <input class="form-control" name="logo" value="" type="file" />
                <div class="ets-czf-logo-preview">
                    {if $module_logo|escape:'html':'UTF-8'}
                        <img class="logo-product" src="{$module_logo|escape:'html':'UTF-8'}" />
                        <button class="btn-remove-logo js-ets-czf-remove-logo" data-id="{$id_product|intval}">

                                <svg class="w_14 h_14" width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>

                        </button>
                    {/if}
                </div>
                <div class="clearfix"></div>
                <div class="ets-czf-upload-logo mt-2">
                    <button class="btn btn-primary js-ets-czf-upload-logo" data-id="{$id_product|intval}">{l s='Upload' mod='ets_productmanager'}</button>
                </div>
                
            </div>
            
        </div>
    </div>
</form>