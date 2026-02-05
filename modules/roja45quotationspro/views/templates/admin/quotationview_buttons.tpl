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

{if !$quotation->is_template}
    <button type="submit"
            id="saveQuotation"
            title="{l s='Save' mod='roja45quotationspro'}"
            class="btn btn-primary btn-save-quotation btn-lg disabled-while-saving">
        <i class="icon-save"></i>
    </button>
{else}
    <button type="submit"
            id="updateTemplate"
            title="{l s='Save' mod='roja45quotationspro'}"
            class="btn btn-secondary btn-lg disabled-while-saving">
        <i class="icon-save"></i>
    </button>
{/if}


<button type="button"
        id="deleteQuotation"
        class="btn btn-primary btn-lg btn-delete-quotation pull-right disabled-while-saving"
        title="{l s='Delete' mod='roja45quotationspro'}">
    <i class="icon-trash"></i>
</button>
