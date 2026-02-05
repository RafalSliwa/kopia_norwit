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
<input type="hidden" name="message_template">
<input type="hidden" name="id_roja45_quotation" value="{$id_roja45_quotation|escape:"html":"UTF-8"}">
<div>
    <div class="form-horizontal">
        {if count($quotation_email_templates)}
            <div class="form-group quotation_template">
                <label class="control-label col-lg-3">
                    {l s='Email Template' mod='roja45quotationspro'}
                </label>
                <div class="col-lg-5">
                    <select class="form-control" name="quotation_answer_template" id="quotation_answer_template">
                        {foreach $quotation_email_templates as $template}
                            <option value="{$template.id_roja45_quotation_answer|escape:'html':'UTF-8'}" {if $default_email_template==$template.id_roja45_quotation_answer}selected="selected"{/if}>{$template.name|escape:"html":"UTF-8"}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/if}
        <div class="form-group quotation_email_subject">
            <label class="control-label col-lg-3">
                {l s='Subject' mod='roja45quotationspro'}
            </label>
            <div class="col-lg-5">
                <input id="quotation_email_subject" name="message_subject" type="text" value="{$message_subject|escape:'htmlall':'UTF-8'}" style="display: inline-block;width:75%"/>
            </div>
        </div>
        {if count($quotation_documents)}
            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Attach Documents' mod='roja45quotationspro'}
                </label>
                <div class="col-lg-5">
                    <select class="form-control"
                            name="select_quotation_documents[]"
                            id="select_quotation_documents"
                            multiple>
                        {foreach $quotation_documents AS $document}
                            <option value="{$document.id_roja45_quotation_document|escape:'html':'UTF-8'}">{$document.display_name|escape:"html":"UTF-8"}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        {/if}
    </div>
</div>

<div id="loaded-quotation-answer">
                    <textarea id="final-quotation-response"
                              name="response_content"
                              class="rte autoload_rte"></textarea>
</div>