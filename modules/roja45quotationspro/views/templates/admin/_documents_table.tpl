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

<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th><span class="title_box ">{l s='Name' mod='roja45quotationspro'}</span></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$quotation_documents item=document}
            <tr class="note_row">
                <td>{$document['display_name']|escape:'html':'UTF-8'}</td>
                <td class="text-right">
                    <form id="customer_document" class="form-horizontal" method="post" action="{$quotationspro_link|escape:'html':'UTF-8'}" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="deleteDocument"/>
                        <input type="hidden" name="id_roja45_quotation" value="{$id_roja45_quotation|escape:"html":"UTF-8"}"/>
                        <input type="hidden" name="id_roja45_quotation_document" value="{$document['id_roja45_quotation_document']|escape:"html":"UTF-8"}"/>
                        <a href="{$quotationspro_link|escape:'html':'UTF-8'}&action=downloadFile&id_roja45_quotation={$id_roja45_quotation|escape:"html":"UTF-8"}&id_roja45_quotation_document={$document['id_roja45_quotation_document']|escape:'html':'UTF-8'}" class="btn btn-primary">
                            <i class="icon-download"></i>
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
