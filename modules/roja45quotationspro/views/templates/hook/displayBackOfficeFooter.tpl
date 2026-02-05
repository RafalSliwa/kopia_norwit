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

<div id="roja45_immersive_modal">
    <div id="roja45_immersive_modal_dialog" class="roja45-roja45-immersive-modal-dialog">
        <div id="modal_wait_icon">
            <i class="icon-refresh icon-spin animated"></i>
            <p>{l s='Please Wait' mod='roja45quotationspro'}</p>
        </div>
    </div>

    <div id="roja45_immersive_confirm_dialog" class="ui-dialog" style="display:none">
        <div class="ui-dialog-titlebar"></div>
        <div class="ui-dialog-content">
            <p>{l s='You have warnings' mod='roja45quotationspro'}</p>
        </div>
        <div class="ui-dialog-buttonpane"></div>
    </div>
    <style type="text/css" style="display: none">
        #roja45_immersive_modal_dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            background: black;
            width: 100%;
            height: 100%;
            z-index: 9999;
            opacity: 0.7;
        }

        #roja45_immersive_modal_dialog #modal_wait_icon {
            position: absolute;
            color: white;
            width: 25%;
            text-align: center;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #roja45_immersive_modal_dialog #modal-wait-icon i {
            font-size: 50px;
        }

        #roja45_immersive_modal_dialog p {
            font-size: 40px;
            margin: 0;
        }

        #roja45_immersive_modal_dialog .icon-refresh {
            font-size: 40px;
        }

        #roja45_immersive_confirm_dialog {
            width: 200px;
            height: 50px;
            border-radius: 3px;
            position: absolute;
            z-index: 999;
            right: 0;
            bottom: 0;
        }

        #roja45_immersive_confirm_dialog.error {
            background-color: rgba(232, 30, 27, 0.5);
        }

        #roja45_immersive_confirm_dialog.warning {
            background-color: rgba(226, 173, 61, 0.5);
        }

        #roja45_immersive_confirm_dialog.success {
            background-color: rgba(40, 226, 10, 0.50);
        }

        #immersive-error-dialog {

        }

        #immersive-warning-dialog {

        }

        #growls.immersive {
            top: 95px;
            right: 10px;
        }
    </style>
    <script type="text/javascript">
        if ($('body > #roja45_immersive_modal').length) {
            $('.ps_back-office #footer #roja45_immersive_modal').last().remove();
        } else {
            $(document.body).append($('.ps_back-office #footer #roja45_immersive_modal'));
        }
        if (typeof roja45global == "undefined") {
            roja45global = {
                bindSwapSave : function()
                {
                    if ($('#selectedSwap option').length !== 0)
                        $('#selectedSwap option').attr('selected', 'selected');
                    else
                        $('#availableSwap option').attr('selected', 'selected');
                },
                bindSwapButton : function (prefix_button, prefix_select_remove, prefix_select_add)
                {
                    $('#'+prefix_button+'Swap').on('click', function(e) {
                        e.preventDefault();
                        $('#' + prefix_select_remove + 'Swap option:selected').each(function() {
                            $('#' + prefix_select_add + 'Swap').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
                            $(this).remove();
                        });
                        $('#selectedSwap option').prop('selected', true);
                    });
                },
                toggleModal : function ()
                {
                    $('#roja45_immersive_modal_dialog').toggle();
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
        }
        {if isset($requires_multiselect) && $requires_multiselect==1}
        if (typeof $('#addSwap') !== undefined && typeof $("#removeSwap") !== undefined &&
            typeof $('#selectedSwap') !== undefined && typeof $('#availableSwap') !== undefined)
        {
            roja45global.bindSwapButton('add', 'available', 'selected');
            roja45global.bindSwapButton('remove', 'selected', 'available');
            $('button:submit').click(roja45global.bindSwapSave);
        }
        {/if}
    </script>
</div>