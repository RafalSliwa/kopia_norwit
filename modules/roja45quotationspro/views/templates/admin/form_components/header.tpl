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

<div id="HEADER" class="field_elements_form defaultForm form-horizontal" data-action="{$url|escape:'html':'UTF-8'}" style="display:none;">
    <form id="text_field_form"  method="post" data-form-type='HEADER'>
        <div class="panel" id="text_field_element">
            <div class="panel-heading">
                <i class="icon-cogs"></i>{l s='Header Field Settings' mod='roja45quotationspro'}
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
                {include file='./textarea_field.tpl'
                field_languages=$languages
                default_lang=$defaultFormLanguage
                id='form_element_label'
                name='form_element_label'
                field_label={l s='Section Name' mod='roja45quotationspro'}
                required='1'
                validationMethod='isText'
                maxlength='255'
                placeholder={l s='Enter the section name' mod='roja45quotationspro'}
                }
                {include
                    file='./text_field.tpl'
                    name='form_element_class'
                    field_label={l s='Custom Class' mod='roja45quotationspro'}
                    required='0'
                    validationMethod='isText'
                    placeholder={l s='Enter a custom class to be applied' mod='roja45quotationspro'}
                }
            </div><!-- /.form-wrapper -->
            {include file='./footer.tpl'}
        </div> <!-- /text_field_form -->
    </form>
</div>