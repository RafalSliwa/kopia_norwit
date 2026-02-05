{*
* 2016 ROJA45
* All rights reserved.
*
* DISCLAIMER
*
* Changing this file will render any support provided by us null and void.
*
*  @author 			Roja45
*  @copyright  		2016 Roja45
*  @license          /license.txt
*}

<div id="ADDRESS_SELECTOR" class="field_elements_form defaultForm form-horizontal" data-form-type='ADDRESS_SELECTOR' data-action="{$url|escape:'html':'UTF-8'}" style="display:none;">
    <form id="address_field_form" method="post" data-form-type='ADDRESS_SELECTOR' data-group="true">

        <div class="panel" id="address_field_element">
            <div class="panel-heading">
                <i class="icon-cogs"></i>{l s='Address Selector Fields Settings' mod='roja45quotationspro'}
            </div>

            <div class="panel-body">
                <div class="alert alert-danger" style="display:none"></div>
                    <div class="form-wrapper">
                        <div class="panel">
                            <div class="panel-heading">
                                <i class="icon-cogs"></i>{l s='My Addresses' mod='roja45quotationspro'}
                                <div class="panel-heading-action">
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span aria-hidden="true">X</span>
                                        <span class="sr-only">Close</span>
                                    </button>
                                </div>
                            </div>
                            <div class="panel-body">
                                {include file='./text_field.tpl'
                                name='form_element_name'
                                field_label={l s='Field Name' mod='roja45quotationspro'}
                                required='1'
                                validationMethod='isText'
                                maxlength='255'
                                placeholder={l s='Enter the name of the field' mod='roja45quotationspro'}
                                onfocusout='$(this).val($(this).val().replace(/\s/g, \'_\'));'
                                }
                                <input name="form_element_type" type="hidden" value="SELECT"/>
                                <input name="form_element_required" type="hidden" value="0"/>
                                <input name="form_element_contents" type="hidden" value="5"/>
                                <input name="form_element_displayas" type="hidden" value="0"/>
                                {include
                                file='./text_field.tpl'
                                field_languages=$languages
                                default_lang=$defaultFormLanguage
                                name='form_element_label'
                                field_label={l s='Label' mod='roja45quotationspro'}
                                required='1'
                                validationMethod='isText'
                                maxlength='255'
                                placeholder={l s='Enter Your Field Label' mod='roja45quotationspro'}
                                default_value='My Addresses'
                                }
                                {include
                                file='./text_field.tpl'
                                field_languages=$languages
                                default_lang=$defaultFormLanguage
                                name='form_element_description'
                                field_label={l s='Description' mod='roja45quotationspro'}
                                required='0'
                                validationMethod='isText'
                                maxlength='255'
                                placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                                }
                                <div class="form-group">
                                    <label class="control-label col-lg-3">
                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Collapse Field' mod='roja45quotationspro'}">{l s='Collapse Field' mod='roja45quotationspro'}</span>
                                    </label>
                                    <div class="col-lg-9 ">
                                        <select name="form_element_collapse" class="">
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-lg-3">
                                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Required' mod='roja45quotationspro'}">{l s='Required' mod='roja45quotationspro'}</span>
                                    </label>
                                    <div class="col-lg-9 ">
                                        <select name="form_element_required" class="">
                                            <option value="1">{l s='Yes' mod='roja45quotationspro'}</option>
                                            <option value="0" selected="selected">{l s='No' mod='roja45quotationspro'}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /.form-wrapper -->
                </div><!-- /.panel-body -->

            {include file='./footer.tpl'}
        </div><!-- /date_field_form -->
    </form><!-- /date_field_form -->
</div>
