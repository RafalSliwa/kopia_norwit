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
<div class="row">
    <div class="col-md-12">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <label>
                                <input id="available_for_order" name="available_for_order" value="1"{if isset($valueFieldPost.available_for_order) && $valueFieldPost.available_for_order} checked="checked"{/if} type="checkbox" />
                                {l s='Available for order' mod='ets_productmanager'}
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h2>{l s='Condition & References' mod='ets_productmanager'}</h2>
                        </div>
                    </div>
                    <div class="row">
                        <fieldset class="col-md-6 form-group">
                            <label class="form-control-label">
                                {l s='Condition' mod='ets_productmanager'}
                                <span class="help-box">
                                    <span>{l s='Not all shops sell new products. This option enables you to indicate the condition of the product. It can be required on some marketplaces.' mod='ets_productmanager'}</span>
                                </span>
                            </label>
                            <select id="form_step6_condition" class="custom-select custom-select" name="condition">
                                <option value="new"{if isset($valueFieldPost.condition)&& $valueFieldPost.condition == 'new'} selected="selected"{/if}>{l s='New' mod='ets_productmanager'}</option>
                                <option value="used"{if isset($valueFieldPost.condition) && $valueFieldPost.condition == 'used'} selected="selected"{/if}>{l s='Used' mod='ets_productmanager'}</option>
                                <option value="refurbished"{if isset($valueFieldPost.condition) && $valueFieldPost.condition == 'refurbished'} selected="selected"{/if}>{l s='Refurbished' mod='ets_productmanager'}</option>
                            </select>
                        </fieldset>
                        {if isset($valueFieldPost.show_condition)}
                            <fieldset class="col-md-6 form-group">
                                <label class="form-control-label"> </label>
                                <div class="checkbox">
                                    <label for="form_step6_show_condition">
                                        <input id="form_step6_show_condition" name="show_condition" value="1" type="checkbox"{if isset($valueFieldPost.show_condition) &&  $valueFieldPost.show_condition}  checked="checked"{/if}/>
                                        {l s='Display condition on product page' mod='ets_productmanager'}
                                    </label>
                                </div>
                            </fieldset>
                        {/if}
                    </div>
                    <div class="row">
                        {if isset($valueFieldPost.isbn)}
                            <fieldset class="col-md-6 form-group">
                                <label class="form-control-label" for="form_step6_isbn">
                                    {l s='ISBN' mod='ets_productmanager'}
                                    <span class="help-box">
                                        <span>{l s='The International Standard Book Number (ISBN) is used to identify books and other publications.' mod='ets_productmanager'}</span>
                                    </span>
                                </label>
                                <input id="form_step6_isbn" class="form-control" name="isbn" type="text" value="{$valueFieldPost.isbn|escape:'html':'UTF-8'}" />
                            </fieldset>
                        {/if}
                        <fieldset class="col-md-6 form-group">
                            <label class="form-control-label" for="form_step6_ean13">
                                {l s='EAN-13 or JAN barcode' mod='ets_productmanager'}
                                <span class="help-box">
                                    <span>{l s='This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.' mod='ets_productmanager'}</span>
                                </span>
                            </label>
                            <input id="form_step6_ean13" class="form-control" name="ean13" type="text" value="{if isset($valueFieldPost.ean13)}{$valueFieldPost.ean13|escape:'html':'UTF-8'}{/if}" />
                        </fieldset>
                    </div>
                    <div class="row">
                        <fieldset class="col-md-4 form-group">
                            <label class="form-control-label" for="form_step6_upc">
                                {l s='UPC barcode' mod='ets_productmanager'}
                                <span class="help-box">
                                    <span>{l s='This type of product code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.' mod='ets_productmanager'}</span>
                                </span>
                            </label>
                            <input id="" name="upc" class="form-control" type="text" value="{if isset($valueFieldPost.upc)}{$valueFieldPost.upc|escape:'html':'UTF-8'}{/if}"/>
                        </fieldset>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="custom_fields" class="mt-3">
                                <h2>{l s='Customization' mod='ets_productmanager'}</h2>
                                <p class="subtitle">{l s='Customers can personalize the product by entering some text or by providing custom image files.' mod='ets_productmanager'}</p>
                                <ul class='customFieldCollection nostyle' data-prototype='<div class="row">
    <input type="hidden" id="form_step6_custom_fields___name___id_customization_field" name="custom_fields[__name__][id_customization_field]" class="form-control" />
  
  <div class="col-md-4">
    <fieldset class="form-group">
        <label class="form-control-label">{l s='Label' mod='ets_productmanager' js=1}</label>
        <div class="translations tabbable" id="form_step6_custom_fields___name___label">
            <div class="translationsFields tab-content">
                {foreach from =$languages item='lang'}
                    <div data-locale="{$lang.iso_code|escape:'html':'UTF-8'}" class="translationsFields-form_step6_custom_fields___name___label_{$lang.id_lang|intval} tab-pane translation-field translatable-field lang-{$lang.id_lang|intval} {if $lang.id_lang==$id_lang_default} show active{/if}  translation-label-{$lang.iso_code|escape:'html':'UTF-8'}" {if $lang.id_lang!=$id_lang_default} style="display:none"{/if}>
                        <div class="col-xs-12 col-sm-12">
                            <input type="text" id="form_step6_custom_fields___name___label_{$lang.id_lang|intval}" name="custom_fields[__name__][label][{$lang.id_lang|intval}]" required="required" class="form-control" />
                        </div>
                    </div> 
                {/foreach} 
            </div>
        </div>
    </fieldset>
  </div>
  <div class="col-md-3">
    <fieldset class="form-group">
      <label class="form-control-label">{l s='Type' mod='ets_productmanager' js=1}</label>
      
      <select id="form_step6_custom_fields___name___type" name="custom_fields[__name__][type]" class="c-select custom-select"><option value="1">{l s='Text' mod='ets_productmanager' js=1}</option><option value="0">{l s='File' mod='ets_productmanager' js=1}</option></select>
    </fieldset>
  </div>
  <div class="col-md-1">
    <fieldset class="form-group">
      <label class="form-control-label">&nbsp;</label>
      <a class="btn btn-block delete" ><svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></a>
    </fieldset>
  </div>
  <div class="col-md-4">
    <fieldset class="form-group">
      <div>
        <label class="form-control-label">&nbsp;</label>
      </div>
      <div class="required-custom-field">
        <div class="checkbox">                          
            <label><input type="checkbox"
         data-toggle="switch" class="tiny" id="form_step6_custom_fields___name___require" name="custom_fields[__name__][require]" value="1" />
Required</label>
          </div>
      </div>
    </fieldset>
  </div>
</div>'>
                                    {if isset($valueFieldPost.customizationFields) && $valueFieldPost.customizationFields}
                                        {foreach from =$valueFieldPost.customizationFields key='index' item ='customizationField'}
                                            <li>
                                                <div class="row">
                                                    <input id="form_step6_custom_fields_{$index|intval}_id_customization_field" class="form-control" name="custom_fields[{$index|intval}][id_customization_field]" type="hidden" value="{$customizationField->id|intval}" />
                                                    <div class="col-md-4 col-xs-10 option_custom">
                                                        <fieldset class="form-group">
                                                            <label class="form-control-label">{l s='Label' mod='ets_productmanager'}</label>
                                                            <div id="form_step6_custom_fields_{$index|intval}_label" class="translations tabbable">
                                                                <div class="translationsFields tab-content">
                                                                    {foreach from=$languages item='lang'}
                                                                        <div data-locale="{$lang.iso_code|escape:'html':'UTF-8'}" class="translationsFields-form_step6_custom_fields_{$index|intval}_label_{$lang.id_lang|intval} tab-pane translation-field translatable-field lang-{$lang.id_lang|intval} {if $lang.id_lang==$id_lang_default} show active{/if}  translation-label-{$lang.iso_code|escape:'html':'UTF-8'}" {if $lang.id_lang!=$id_lang_default} style="display:none"{/if}>
                                                                            <div class="col-xs-12 col-sm-12">
                                                                                <input type="text" id="form_step6_custom_fields_{$index|intval}_label_{$lang.id_lang|intval}" name="custom_fields[{$index|intval}][label][{$lang.id_lang|intval}]" required="required" class="form-control" value="{$customizationField->name[$lang.id_lang]|escape:'html':'UTF-8'}"/>
                                                                            </div>
                                                                        </div> 
                                                                    {/foreach}
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-3 col-xs-10">
                                                        <fieldset class="form-group">
                                                            <label class="form-control-label">{l s='Type' mod='ets_productmanager'}</label>
                                                            <select id="form_step6_custom_fields_{$index|intval}_type" class="c-select custom-select" name="custom_fields[{$index|intval}][type]">
                                                                <option value="1"{if $customizationField->type==1} selected="selected"{/if}>{l s='Text' mod='ets_productmanager'}</option>
                                                                <option value="0"{if $customizationField->type==0} selected="selected"{/if}>{l s='File' mod='ets_productmanager'}</option>
                                                            </select>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-1 col-xs-2">
                                                        <fieldset class="form-group">
                                                            <label class="form-control-label">&nbsp;</label>
                                                            <a class="btn btn-block delete">
                                                                <svg width="14" height="14" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg>
                                                            </a>
                                                        </fieldset>
                                                    </div>
                                                    <div class="col-md-4 col-xs-12">
                                                        <fieldset class="form-group">
                                                          <div>
                                                                <label class="form-control-label xs-hide">&nbsp;</label>
                                                          </div>
                                                          <div class="required-custom-field">
                                                            <div class="checkbox">                          
                                                                <label><input data-toggle="switch" class="tiny" id="form_step6_custom_fields_{$index|intval}_require" name="custom_fields[{$index|intval}][required]" value="1" type="checkbox"{if $customizationField->required} checked="checked"{/if} /> {l s='Required' mod='ets_productmanager'}</label>
                                                            </div>
                                                          </div>
                                                        </fieldset>
                                                    </div>
                                                </div>
                                            </li>
                                        {/foreach}
                                    {/if}
                                </ul>
                                <a class="btn btn-outline-secondary add ets_addfile_customization" href="#">
                                    <i class="fa fa-plus-circle"></i>
                                    {l s='Add a customization field' mod='ets_productmanager'}
                                </a>
                            </div>
                            <
                        </div>
                    </div>  
                    <br />
                    <div class="row">
                        <div class="col-md-12">
                            <h2>{l s='Attached files' mod='ets_productmanager'}</h2>
                            <p class="subtitle">{l s='Add files that customers can download directly on the product page (instructions, manual, recipe, etc.)' mod='ets_productmanager'}.</p>
                            <div class="js-options-no-attachments{if isset($valueFieldPost.attachments) && $valueFieldPost.attachments} hide{/if}">
                                <p>{l s='There is no attachment yet.' mod='ets_productmanager'}</p>
                            </div>
                            <div id="product-attachments" class="panel panel-default {if isset($valueFieldPost.attachments) || (isset($valueFieldPost.attachments)&& !$valueFieldPost.attachments)} hide{/if}">
                                <div class="panel-body js-options-with-attachments ">
                                    <div>
                                        <table style="width: 100%;">
                                            <thead class="thead-default">
                                                <tr>
                                                    <th>{l s='Title' mod='ets_productmanager'}</th>
                                                    <th >{l s='File name' mod='ets_productmanager'}</th>
                                                    <th>{l s='Type' mod='ets_productmanager'}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {if isset($valueFieldPost.attachments) && $valueFieldPost.attachments}
                                                    {foreach from = $valueFieldPost.attachments item='attachment'}
                                                        <tr>
                                                            <td >{$attachment.name|escape:'html':'UTF-8'}</td>
                                                            <td>{$attachment.file_name|escape:'html':'UTF-8'}</td>
                                                            <td> {$attachment.mime|escape:'html':'UTF-8'}</td>
                                                        </tr>
                                                    {/foreach}
                                                {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-12">
                            <a class="btn btn-outline-secondary mb-2 collapsed ets_add_attachfile" href="#collapsedForm" data-toggle="collapse" aria-expanded="false" aria-controls="collapsedForm">
                                <i class="fa fa-plus-circle"></i>
                                {l s='Attach a new file' mod='ets_productmanager'}
                            </a>
                            <fieldset id="collapsedForm" class="collapse" style="">
                                <div id="form_step6_attachment_product">
                                        <div class="form-group">
                                            <div class="custom-file">
                                                <input id="form_step6_attachment_product_file" class="custom-file-input" name="attachment_product_file" data-multiple-files-text="%count% file(s)" data-locale="en" type="file" />
                                                <label class="custom-file-label" for="form_step6_attachment_product_file">{l s='Choose file' mod='ets_productmanager'}</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input id="form_step6_attachment_product_name" class="form-control" name="attachment_product_name" placeholder="Title" type="text" />
                                        </div>
                                        <div class="form-group">
                                            <input id="form_step6_attachment_product_description" class="form-control" name="attachment_product_description" placeholder="Description" type="text" />
                                        </div>
                                        <div class="form-group">
                                            <button id="form_step6_attachment_product_add" class="btn-outline-primary pull-right btn" type="button" name="attachment_product_add">{l s='Add' mod='ets_productmanager'}</button>
                                            <button id="form_step6_attachment_product_cancel" class="btn-outline-secondary pull-right mr-1 btn collapsed" type="button" name="attachment_product_cancel" data-toggle="collapse" data-target="#collapsedForm" aria-expanded="false">{l s='Cancel' mod='ets_productmanager'}</button>
                                        </div>
                                        <div class="clearfix"></div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div id="html_form_supplier">
                        {$html_form_supplier nofilter}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>