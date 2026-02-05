/**
 * roja45productadmin.js.
 *
 * @author    Roja45
 * @copyright 2016 Roja45
 * @license   license.txt
 *
 * 2016 ROJA45 - All rights reserved.
 *
 * DISCLAIMER
 * Changing this file will render any support provided by us null and void.
 */

$(document).ready(function() {
    id_language = default_language;
    $('select[name=type]').on('change', function(e) {
        e.preventDefault();
        if ($(this).val()=='1') {
            $('#roja45_quotationspro_answer_form .text_template_group').hide();
        } else {
            $('#roja45_quotationspro_answer_form .text_template_group').show();
        }
    }).trigger('change');

    $('.btn-preview-template').on('click', function(e) {
        e.preventDefault();
        roja45quotationspro.loadMessageTemplate($(this).attr('data-type'));
    });

    $(document).on('click', '#quotationspro_message_dialog .cross, #quotationspro_message_dialog .btn-close', function (e) {
        e.preventDefault();
        $('.quotationspro_request_dialog_overlay').removeClass('roja45quotationspro-darken-background');
        $('.quotationspro_request_dialog_overlay').hide();
        $('#quotationspro_message_dialog').fadeOut('fast');
        $('#quotationspro_message_dialog').off('click');
        $('#quotationspro_message_dialog').off('keypress');
    });

    roja45quotationspro.tinySetup({
        mode : "textareas",
        selector: '.html_template',
        plugins: "lists advlist colorpicker link image table media code table autoresize",
        toolbar: 'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,bullist,numlist,table,image,media,fontselect,formatselect',
        extended_valid_elements: '+*[*]',
        valid_children : '+body[style]',
        force_br_newlines : false,
        force_p_newlines : false,
        forced_root_block : '',
        content_css: roja45_template_css,
        allow_conditional_comments: true,
        theme: 'silver',
        protect: [
            /\<!-- \/?(foreach quotation_products|end foreach quotation_products) --\>/g
        ]
    });
});

roja45quotationspro = {
    tinySetup : function (config)
    {
        if (typeof tinymce === 'undefined') {
            setTimeout(function() {
                roja45quotationspro.tinySetup(config);
            }, 100);
            return;
        }

        tinymce.init(config);
    },

    loadMessageTemplate : function(preview_type) {
        if (typeof id_language === 'undefined') {
            id_language == default_language;
        }

        var datatype = 'json';
        var responseType = 'text';
        if (preview_type=='text') {
            var content = $.trim($('#text_template_'+id_language).val());
        } else if (preview_type == 'html') {
            var editor = tinyMCE.get('html_template_'+id_language);
            var content = editor.getContent();
        } else if (preview_type == 'pdf') {
            var editor = tinyMCE.get('html_template_'+id_language);
            var content = editor.getContent();
            var datatype = 'binary';
            var responseType = 'arraybuffer';
        }
        $.ajax({
            type: "POST",
            url: controller_url,
            data: {
                ajax : 1,
                token : token,
                id_language : id_language,
                id_roja45_quotation_answer : $('#id_roja45_quotation_answer').val(),
                type : roja45_template_type,
                action : 'previewMessage',
                content : content,
                preview_type : preview_type,
                xhrFields: {
                    responseType: responseType
                },
            },
            beforeSend : function() {
                roja45quotationspro.toggleModal();
            },
            success: function (data) {
                if (preview_type === 'pdf') {
                    try {
                        var json = JSON.parse(data);
                        $.each(json.errors, function (index, value) {
                            roja45quotationspro.displayErrorMsg(value);
                        });
                    } catch(err) {
                        var $iframe = $('#preview_iframe');
                        $iframe.ready(function() {
                            $iframe.contents().find('body').empty().append(
                                '<object data="data:application/pdf;base64,'+data+'#zoom=90&scrollbar=0&toolbar=1&navpanes=1" type="application/pdf" style="height:100%;width:100%"></object>'
                            );
                            $('#quotationspro_message_dialog').toggle();
                        });
                    }
                } else {
                    var json = JSON.parse(data);
                    if (json.result == 1) {
                        window.open(json.url, '_blank');
                    } else if (json.result == 2) {
                        var $iframe = $('#preview_iframe');
                        $iframe.ready(function() {
                            $iframe.contents().find('body').empty().append(json.content);
                            $('#quotationspro_message_dialog').toggle();
                        });
                    } else {
                        $.each(json.errors, function (index, value) {
                            roja45quotationspro.displayErrorMsg(value);
                        });
                    }
                }
            },
            error: function (request, status, error) {
                jAlert("Unable to load answer.\n\nstatus: '" + status + "'\nerror: '" + error + "'\nresponseText:\n" + request.responseText);
            },
            complete: function () {
                roja45quotationspro.toggleModal();
            }
        });

    },

    toggleModal : function ()
    {
        $('#roja45_quotation_modal_dialog').toggle();
    },

    displaySuccessMsg : function (msg)
    {
        $.growl.notice({
            duration: 3000,
            location: 'immersive',
            title: 'Success',
            message: msg
        });
    },

    displayWarningMsg : function (msg)
    {
        $.growl.warning({
            duration: 6000,
            location: 'immersive',
            title: 'Warning',
            message: msg
        });
    },

    displayErrorMsg : function (msg) {
        $.growl.error({
            duration: 10000,
            location: "immersive",
            title: 'Error',
            message: msg
        });
    }
}
