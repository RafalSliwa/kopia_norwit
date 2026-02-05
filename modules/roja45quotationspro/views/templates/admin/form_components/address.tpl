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

<div id="ADDRESS" class="field_elements_form defaultForm form-horizontal" data-form-type='ADDRESS' data-action="{$url|escape:'html':'UTF-8'}" style="display:none;">
    <form id="address_field_form" name="address_field_form"  method="post" data-form-type='ADDRESS' data-group="true">
        <div class="panel" id="address_field_element">
            <div class="panel-heading">
                <i class="icon-cogs"></i>{l s='Address Fields Settings' mod='roja45quotationspro'}
            </div>

            <div class="panel-body">

            <div class="alert alert-danger" style="display:none"></div>
                <div class="form-wrapper">
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Field Name' mod='roja45quotationspro'})

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
                            default_value='CUSTOMER_ADDRESS'
                            }
                            <input name="form_element_id" type="hidden" value="CUSTOMER_ADDRESS"/>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Company' mod='roja45quotationspro'} ({l s='Optional' mod='roja45quotationspro'})

                        </div>

                        <div class="panel-body">
                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_COMPANY"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="0"/>
                            <input name="form_element_validation" type="hidden" value="isName"/>
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
                            default_value='Company'
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
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_COMPANY"/>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Address Line 1' mod='roja45quotationspro'}

                        </div>

                        <div class="panel-body">
                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="1"/>
                            <input name="form_element_validation" type="hidden" value="isAddress"/>
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
                            default_value='Address'
                            }
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_address_description'
                            field_label={l s='Description' mod='roja45quotationspro'}
                            required='0'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                            }
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS"/>
                        </div>

                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Address Line 2' mod='roja45quotationspro'} ({l s='Optional' mod='roja45quotationspro'})

                        </div>

                        <div class="panel-body">
                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS2"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="0"/>
                            <input name="form_element_validation" type="hidden" value="isAddress"/>
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
                            default_value='Address 2'
                            }
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_description'
                            field_label={l s='Description' mod='roja45quotationspro'}
                            required='1'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                            }
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_ADDRESS2"/>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='City' mod='roja45quotationspro'}

                        </div>

                        <div class="panel-body">
                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_CITY"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="1"/>
                            <input name="form_element_validation" type="hidden" value="isCityName"/>
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
                            default_value='City'
                            }
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_description'
                            field_label={l s='Description' mod='roja45quotationspro'}
                            required='1'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                            }
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_CITY"/>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Zip/postal Code' mod='roja45quotationspro'}

                        </div>

                        <div class="panel-body">
                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_ZIP"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="1"/>
                            <input name="form_element_validation" type="hidden" value="isPostCode"/>
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
                            default_value='Zip/postal Code'
                            }
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_description'
                            field_label={l s='Description' mod='roja45quotationspro'}
                            required='1'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                            }
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_ZIP"/>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='State' mod='roja45quotationspro'}
                        </div>
                        <div class="panel-body">

                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_STATE"/>
                            <input name="form_element_type" type="hidden" value="SELECT"/>
                            <input name="form_element_required" type="hidden" value="1"/>
                            <input name="form_element_contents" type="hidden" value="4"/>
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
                            default_value='State'
                            }
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_description'
                            field_label={l s='Description' mod='roja45quotationspro'}
                            required='1'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                            }
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_STATE"/>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Country' mod='roja45quotationspro'}

                        </div>
                        <div class="panel-body">

                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_COUNTRY"/>
                            <input name="form_element_type" type="hidden" value="SELECT"/>
                            <input name="form_element_required" type="hidden" value="1"/>
                            <input name="form_element_contents" type="hidden" value="2"/>
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
                            default_value='Country'
                            }
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_description'
                            field_label={l s='Description' mod='roja45quotationspro'}
                            required='1'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                            }
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_COUNTRY"/>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='Phone' mod='roja45quotationspro'}

                        </div>
                        <div class="panel-body">

                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_PHONE"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="1"/>
                            <input name="form_element_validation" type="hidden" value="isPhoneNumber"/>
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
                            default_value='Phone'
                            }
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_description'
                            field_label={l s='Description' mod='roja45quotationspro'}
                            required='1'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                            }
                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_PHONE"/>
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='VAT Number' mod='roja45quotationspro'} ({l s='Optional' mod='roja45quotationspro'})

                        </div>
                        <div class="panel-body">

                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_VAT_NUMBER"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="0"/>
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_label'
                            field_label={l s='Label' mod='roja45quotationspro'}
                            required='0'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Label' mod='roja45quotationspro'}
                            default_value='VAT Number'
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
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_VAT_NUMBER"/>
                        </div>
                    </div>
                
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-cogs"></i>{l s='DNI' mod='roja45quotationspro'} ({l s='Optional' mod='roja45quotationspro'})

                        </div>
                        <div class="panel-body">
                            <input name="form_element_name" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_DNI"/>
                            <input name="form_element_type" type="hidden" value="TEXT"/>
                            <input name="form_element_required" type="hidden" value="0"/>
                            {include
                            file='./text_field.tpl'
                            field_languages=$languages
                            default_lang=$defaultFormLanguage
                            name='form_element_label'
                            field_label={l s='Label' mod='roja45quotationspro'}
                            required='0'
                            validationMethod='isText'
                            maxlength='255'
                            placeholder={l s='Enter Your Field Label' mod='roja45quotationspro'}
                            default_value='DNI'
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
                                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Enabled' mod='roja45quotationspro'}">{l s='Enabled' mod='roja45quotationspro'}</span>
                                </label>

                                <div class="col-lg-9 ">
                                    <select name="form_element_enabled" class="fixed-width-xl">
                                        <option value="1" selected="selected">{l s='Yes' mod='roja45quotationspro'}</option>
                                        <option value="0">{l s='No' mod='roja45quotationspro'}</option>
                                    </select>
                                </div>
                            </div>
                            <input name="form_element_id" type="hidden" value="ROJA45QUOTATIONSPRO_CUSTOMER_DNI"/>
                        </div>
                    </div>
            </div><!-- /.form-wrapper -->
            </div><!-- /.form-wrapper -->

            {include file='./footer.tpl'}
        </div><!-- /date_field_form -->
    </form><!-- /date_field_form -->
</div>
