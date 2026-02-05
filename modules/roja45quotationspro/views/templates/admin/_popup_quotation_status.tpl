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
<!-- views/templates/admin/_popup_quotation_status.tpl -->
<div class="quotationspro_request_dialog_overlay" style="display:none;"></div>
<div id="quotationspro_update_status_alert_dialog" class="quotationspro_update_status_alert_dialog quotationspro_dialog modal-dialog" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">{l s='Set Status' mod='roja45quotationspro'}</h3>
        </div>
        <div class="modal-body">
            <label for="quotation_status">Select Quotation Status:</label>
            <select class="form-control" name="quotation_status" id="quotation_status">
                {foreach from=$options item='option'}
					<option value="{$option.id|escape:'html':'UTF-8'}">{$option.name}</option>
				{/foreach}
            </select>
        </div>
        <div class="modal-footer">
            <div class="col-lg-12">
                <button type="button" id="cancelButton" class="btn btn-secondary saving pull-left">
                    {l s='Cancel' mod='roja45quotationspro'}
                </button>
                <button type="button" id="confirmButton" class="btn btn-primary saving pull-right">
                    {l s='Update' mod='roja45quotationspro'}
                </button>
            </div>
        </div>
    </div>
</div>

