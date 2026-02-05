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

<div id="SELECT" class="field_elements_form defaultForm form-horizontal" data-action="{$url|escape:'html':'UTF-8'}" style="display:none;">
    <form id="select_field_form" data-form-type='SELECT' method="post" enctype="multipart/form-data" novalidate="">
    <div class="panel" id="select_field_element">
        <div class="panel-heading">
            <i class="icon-cogs"></i>{l s='Select Field Settings' mod='roja45quotationspro'}
        </div>
        <div class="alert alert-danger" style="display:none">
        </div>
        <div class="form-wrapper">
            {include file='./text_field.tpl'
            name='form_element_name'
            field_label={l s='Field Name' mod='roja45quotationspro'}
            required='1'
            validationMethod='isText'
            maxlength='255'
            placeholder={l s='Enter the name of the field' mod='roja45quotationspro'}
            onkeyup='$(this).val($(this).val().replace(/\s/g, \'_\'));'
            }
            <div class="form-group">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                          data-original-title="{l s='Required' mod='roja45quotationspro'}">{l s='Required' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-9 ">
                    <select name="form_element_required" class="fixed-width-xl">
                        <option value="1">{l s='Yes' mod='roja45quotationspro'}</option>
                        <option value="0" selected="selected">{l s='No' mod='roja45quotationspro'}</option>
                    </select>
                </div>
            </div>
            {include
            file='./text_field.tpl'
            field_languages=$languages
            default_lang=$defaultFormLanguage
            id='form_element_label'
            field_label={l s='Label' mod='roja45quotationspro'}
            required='1'
            validationMethod='isText'
            maxlength='255'
            placeholder={l s='Enter Your Field Label' mod='roja45quotationspro'}
            }
            {include
            file='./text_field.tpl'
            field_languages=$languages
            default_lang=$defaultFormLanguage
            id='form_element_description'
            field_label={l s='Description' mod='roja45quotationspro'}
            required='1'
            validationMethod='isText'
            maxlength='255'
            placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
            }

            <div class="form-group">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                          data-original-title="{l s='Contents' mod='roja45quotationspro'}">{l s='Contents' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-9 ">
                    <select name="form_element_contents" class="form_element_contents fixed-width-xl">
                        <option value="0" selected="selected">{l s='Select' mod='roja45quotationspro'}</option>
                        <option value="1">{l s='Custom' mod='roja45quotationspro'}</option>
                        <option value="2">{l s='Countries' mod='roja45quotationspro'}</option>
                        <option value="3">{l s='Shipping Methods' mod='roja45quotationspro'}</option>
                        <option value="4">{l s='States' mod='roja45quotationspro'}</option>
                    </select>
                </div>
            </div>
            <div class="field-dropdown-custom" style="display:none;">
                {include file='./textarea_field.tpl'
                field_languages=$languages
                default_lang=$defaultFormLanguage
                name='form_element_select_options'
                field_label={l s='Select Options' mod='roja45quotationspro'}
                field_description={l s='Enter Your Select Options (1 Per line)' mod='roja45quotationspro'}
                placeholder={l s='Enter Your Select Options (1 Per line)' mod='roja45quotationspro'}
                required='1'
                rows='5'
                maxlength='255'
                }
            </div>
            <div class="form-group">
                <label class="control-label col-lg-3">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title=""
                          data-original-title="{l s='Display As' mod='roja45quotationspro'}">{l s='Display As' mod='roja45quotationspro'}</span>
                </label>
                <div class="col-lg-9 ">
                    <select name="form_element_displayas" class="form_element_displayas fixed-width-xl">
                        <option value="0" selected="selected">{l s='Drop Down' mod='roja45quotationspro'}</option>
                        <option value="1">{l s='Radio Buttons' mod='roja45quotationspro'}</option>
                    </select>
                </div>
            </div>
            {include file='./text_field.tpl'
            name='form_element_class'
            field_label={l s='Custom Class' mod='roja45quotationspro'}
            required='0'
            validationMethod='isText'
            placeholder={l s='Enter a custom class to be applied' mod='roja45quotationspro'}
            }
        </div><!-- /.form-wrapper -->

        {include file='./footer.tpl'}

    </div><!-- /select_field_form -->
    </form>
</div>
