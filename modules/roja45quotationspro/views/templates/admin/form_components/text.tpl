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

<div id="TEXT" class="field_elements_form defaultForm form-horizontal" data-action="{$url|escape:'html':'UTF-8'}" style="display:none;">
    <form id="text_field_form"  method="post" data-form-type='TEXT'>
        <div class="panel" id="text_field_element">
            <div class="panel-heading">
                <i class="icon-cogs"></i>{l s='Text Field Settings' mod='roja45quotationspro'}
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
                    onfocusout='$(this).val($(this).val().replace(/\s/g, \'_\'));'
                }
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Required' mod='roja45quotationspro'}">{l s='Required' mod='roja45quotationspro'}</span>
                    </label>
                    <div class="col-lg-9 ">
                        <select name="form_element_required" class="fixed-width-xl">
                            <option value="1">{l s='Yes' mod='roja45quotationspro'}</option>
                            <option value="0" selected="selected">{l s='No' mod='roja45quotationspro'}</option>
                        </select>
                    </div>
                </div>
                {include file='./text_field.tpl'
                field_languages=$languages
                default_lang=$defaultFormLanguage
                id='form_element_label'
                name='form_element_label'
                field_label={l s='Label' mod='roja45quotationspro'}
                required='1'
                validationMethod='isText'
                maxlength='255'
                placeholder={l s='Enter Your Field Label' mod='roja45quotationspro'}
                }
                {include file='./text_field.tpl'
                field_languages=$languages
                default_lang=$defaultFormLanguage
                id='form_element_description'
                name='form_element_description'
                field_label={l s='Description' mod='roja45quotationspro'}
                required='1'
                validationMethod='isText'
                maxlength='255'
                placeholder={l s='Enter Your Field Description' mod='roja45quotationspro'}
                }
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="{l s='Validation Method' mod='roja45quotationspro'}">{l s='Validation Method' mod='roja45quotationspro'}</span>
                    </label>

                    <div class="col-lg-9 ">
                        <select name="form_element_validation" class="fixed-width-xl">
                            <option value="none" selected="selected">{l s='No Validation' mod='roja45quotationspro'}</option>
                            <option value="isName">{l s='Is Name' mod='roja45quotationspro'}</option>
                            <option value="isEmail">{l s='Is Email' mod='roja45quotationspro'}</option>
                            <option value="isPhoneNumber">{l s='Is Phone Number' mod='roja45quotationspro'}</option>
                            <option value="isAddress">{l s='Is Address' mod='roja45quotationspro'}</option>
                            <option value="isCityName">{l s='Is City Name' mod='roja45quotationspro'}</option>
                            <option value="isPostCode">{l s='Is Post Code' mod='roja45quotationspro'}</option>
                            <option value="isMessage">{l s='Is Message' mod='roja45quotationspro'}</option>
                            <option value="isDniLite">{l s='Is DNI' mod='roja45quotationspro'}</option>
                            <option value="isPasswd">{l s='Is Password' mod='roja45quotationspro'}</option>
                            <option value="isCustom">{l s='Custom Regex' mod='roja45quotationspro'}</option>
                        </select>
                    </div>
                </div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('select[name=form_element_validation]').change( function(e) {
                            var selected = $( "select[name=form_element_validation] option:selected").val();
                            if (selected == 'isCustom' ) {
                                $('input[name=form_element_validation_custom]').closest('.form-group').fadeIn();
                            } else {
                                $('input[name=form_element_validation_custom]').closest('.form-group').fadeOut();
                            }
                        }).trigger('change');
                    });
                </script>
                {include
                    file='./text_field.tpl'
                    name='form_element_validation_custom'
                    field_label={l s='Custom Validation Regex' mod='roja45quotationspro'}
                    required='0'
                    placeholder={l s='Enter your validation regular expression' mod='roja45quotationspro'}
                }
                {include
                    file='./text_field.tpl'
                    name='form_element_class'
                    field_label={l s='Custom Class' mod='roja45quotationspro'}
                    required='0'
                    validationMethod='isText'
                    placeholder={l s='Enter a custom class to be applied' mod='roja45quotationspro'}
                }
                {include
                    file='./text_field.tpl'
                    name='form_element_length'
                    field_label={l s='Field Length' mod='roja45quotationspro'}
                    required='0'
                    validationMethod='isNumber'
                    placeholder={l s='Enter the maximum length of the field' mod='roja45quotationspro'}
                }
                {include
                    file='./text_field.tpl'
                    field_languages=$languages
                    name='form_element_prefix'
                    field_label={l s='Field Prefix' mod='roja45quotationspro'}
                    required='0'
                    placeholder={l s='Enter a field prefix to be displayed' mod='roja45quotationspro'}
                }
                {include
                    file='./text_field.tpl'
                    field_languages=$languages
                    name='form_element_suffix'
                    field_label={l s='Field Suffix' mod='roja45quotationspro'}
                    required='0'
                    placeholder={l s='Enter a field suffix to be displayed' mod='roja45quotationspro'}
                }
            </div><!-- /.form-wrapper -->
            {include file='./footer.tpl'}
        </div> <!-- /text_field_form -->
    </form>
</div>