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
<script type="">
var cover_text = '{l s='Cover' mod='ets_productmanager' js=1}';
var delete_image_comfirm ='{l s='Do you want to delete this image?' mod='ets_productmanager' js=1}';
</script>
<script type="text/javascript" src="{$_PS_JS_DIR_|escape:'html':'UTF-8'}jquery/plugins/fancybox/jquery.fancybox.js"></script>
<script type="text/javascript" src="{$module_dir|escape:'html':'UTF-8'}/views/js/multi_upload.js?time={time()|escape:'html':'UTF-8'}"></script>
<form class="defaultForm form-horizontal" action="" method="post" enctype="multipart/form-data" novalidate="">
    <div id="fieldset_0" class="panel">
        <div class="panel-heading">{l s='Edit image' mod='ets_productmanager'}: {$product_name|escape:'html':'UTF-8'}</div>
        <div class="ets_pmn_errors"></div>
        <div class="form-wrapper">
            <div id="product-images-container">
                <div id="product-images-dropzone" class="panel dropzone ui-sortable {if $images}dz-started{/if} col-md-12" style="">
                    <input type="hidden" id="ets_pmn_id_product" value="{$product_class->id|intval}" />
                    <input type="hidden" id="form_switch_language" value="{$lang_default->iso_code|escape:'html':'UTF-8'}" />
                    <div id="product-images-dropzone-error" class="text-danger"></div>
                    <div id="list-images-product">
                        {if $images}
                            {foreach from=$images item='image'}
                                <div id="images-{$image.id_image|intval}" class="dz-preview dz-image-preview dz-complete ui-sortable-handle ets_pmn_edit_image" data-id="{$image.id_image|intval}" url-delete="{$image.link_delete|escape:'html':'UTF-8'}" url-update="{$image.link_update|escape:'html':'UTF-8'}">
                                    <div class="dz-image bg" data-url="{$image.link|escape:'html':'UTF-8'}" style="background-image: url('{$image.link|escape:'html':'UTF-8'}-home_default.{$image.format|escape:'html':'UTF-8'}?time={time()|escape:'html':'UTF-8'}');">
                                          <button type="button" class="btn btn-sm btn-link ets_pmn_delete_image" title="{l s='Delete' mod='ets_productmanager'}" data-id="{$image.id_image|intval}">

                                                <svg class="w_14 h_14" width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                          </button>
                                    </div>
                                    <div class="dz-progress">
                                        <span class="dz-upload" style="width: 100%;"></span>
                                    </div>
                                    <div class="dz-error-message">
                                        <span data-dz-errormessage=""></span>
                                    </div>
                                    <div class="dz-success-mark"></div>
                                    <div class="dz-error-mark"></div>
                                    {if $image.cover}
                                        <div class="iscover">{l s='Cover' mod='ets_productmanager'}</div>
                                    {/if}
                                </div>
                            {/foreach}
                        {/if} 
                        <div id="form-images">
                            <input id="ets_pmn_multiple_images" name="multiple_imamges[]" multiple="multiple" type="file" />
                            <label for="ets_pmn_multiple_images">
                                <div class="dz-default dz-message openfilemanager dz-clickable">
                                    <i class="icon icon-add-photo"></i> <br />
                                    {l s='Select files' mod='ets_productmanager'} <br />
                                    <small>
                                        {l s='Recommended size 800 x 800px for default theme.' mod='ets_productmanager'}<br />
                                        {l s='JPG, GIF or PNG format.' mod='ets_productmanager'}
                                    </small>
                                </div> 
                                <div class="dz-preview disabled openfilemanager dz-clickable">
                                    <div>
                                        <span>+</span>
                                    </div>
                                </div>
                            </label>    
                        </div>
                    </div>
                </div>
                <div id="product-images-form-container">
                </div>
            </div>
        </div>
    </div>
</form>