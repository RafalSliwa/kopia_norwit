/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
$(document).ready(function(){
    if(pmn_is17)
    {
        if($('#categories').length)
        {
            var html ='<div class="form-group mb-4">\n' +
                '        <h2>'+pmn_private_note_text+'</h2>\n' +
                '        <div class="row">\n' +
                '            <div class="col-xl-12 col-lg-12" id="form_step6_note_product_field" >\n' +
                '                    <textarea id="form_step6_note_product" name="form_step6_note_product" style="width:100%">'+ets_pmn_product_note+'</textarea>\n' +
                '                    <input id="id_note_product" type="hidden" value="'+pmn_id_product+'" />\n' +
                '            </div>\n' +
                '        </div>\n' +
                '    </div>';
            $('#categories').append(html);
        }
    }
    else {
        if($('#product-informations').length)
        {
            var html ='<div class="from_product_note_is16">\n' +
                '        <div class="form-group">\n' +
                '            <label class="control-label col-lg-3" for="form_step6_note_product">\n' +
                '                <span>'+pmn_private_note_text+'</span>\n' +
                '            </label>\n' +
                '            <div class="col-lg-9">\n' +
                '                <div class="col-xl-12 col-lg-12" id="form_step6_note_product_field">\n' +
                '                    <textarea id="form_step6_note_product" name="form_step6_note_product" style="width:100%">'+ets_pmn_product_note+'</textarea>\n' +
                '                    <input id="id_note_product" type="hidden" value="'+pmn_id_product+'" />\n' +
                '                </div>\n' +
                '            </div>\n' +
                '        </div>\n' +
                '    </div>';
        }
    }
    $(document).on('change','#form_step6_note_product',function(e){
        $.ajax({
            url: pmn_link_ajax,
            data: {
                changePrivateNoteProduct:1,
                private_note : $('#form_step6_note_product').val(),
                id_note_product:$('#id_note_product').val(),
            },
            type: 'post',
            dataType: 'json',
            success: function(json){
                if(json.success)
                    showSuccessMessage(json.success);
                if(json.error)
                    showErrorMessage(json.error);
            },
            error: function(xhr, status, error)
            {

            }
        });
    });
});