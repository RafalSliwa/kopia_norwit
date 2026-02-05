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

<a href="{$href|escape:'html':'UTF-8'}" title="{$action|escape:'html':'UTF-8'}" class="assign btn btn-default btn-assign-user">
    <i class="icon-user"></i>
</a>

<div id="quotationspro_assign_user_dialog" class="quotationspro_assign_user_dialog quotationspro_dialog modal-dialog" style="display:none" data-controller="{$controller_url}">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">{l s='Assign User' mod='roja45quotationspro'}</h3>
            <span class="cross btn-close" title="{l s='Close window' mod='roja45quotationspro'}"></span>
        </div>
        <div class="modal-body">
            <div class="employee-list">
                <ul>
                {foreach $employees as $employee}
                    <li>
                        <span class="employee-name">{$employee.firstname} {$employee.lastname}</span>
                        <button class="btn btn-default btn-assign" data-quotation-id="{$id_roja45_quotation}" value="{$employee.id_employee}">{l s='Select' mod='roja45quotationspro'}</button>
                    </li>
                {/foreach}
                    <li>
                        <span class="employee-name">{l s='Unassign' mod='roja45quotationspro'}</span>
                        <button class="btn btn-default btn-assign" data-quotation-id="{$id_roja45_quotation}" value="0">{l s='Select' mod='roja45quotationspro'}</button>
                    </li>
                </ul>
            </div>
            <div id="roja45_quotation_modal_overlay" class="roja45_quotation_modal_overlay">
                <div id="roja45_quotation_modal_dialog" class="roja45-quotation-modal-dialog">
                    <div id="modal_wait_icon">
                        <i class="icon-refresh icon-spin animated"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer quotationspro_request buttons">
            <div class="button-container">
                <button type="button" id="ClosePreview"
                        class="btn btn-secondary btn-lg btn-close pull-left">
                    <i class="icon-remove"></i>
                    {l s='Close' mod='roja45quotationspro'}
                </button>
            </div>
        </div>
    </div>
</div>
