/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2025 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */

$(document).ready(function () {
    if (typeof extratabspro_id_product !== 'undefined') {
        if (window.location.href.indexOf("extratabspro") > -1) {
            if (_PS_VERSION_[0] >= 8) {
                $('#tab_hooks a').click();
                $('#product_extra_modules-tab-nav a').click();
                function show_ppb(){
                    $('button[data-target="module-extratabspro"]').click();
                };
                window.setTimeout(show_ppb, 500 );

            } else {
                $('.module-selection').show();
            }
            $('.modules-list-select').val('module-extratabspro').trigger('change');
            if (typeof _PS_VERSION_ !== 'undefined') {
                if (_PS_VERSION_[0] != 8) {
                    $('.module-render-container').hide();
                }
            } else {
                $('.module-render-container').hide();
            }
            $(`.module-extratabspro`).show();
        }

        $('.etab_feature_selected div').click(function () {
            $(this).remove();
        });
        $(".ex_supplier").keypress(function () {
            $.post(extratabspro_url + "ajax_extratabspro.php", {search_supplier: $(".ex_supplier").val()}, function (data) {
                $(".ex_search_supplier").html(data);
            })
        });
        $(".etab_feature_s").keypress(function () {
            $.post(extratabspro_url + "ajax_extratabspro.php", {search_feature: $(".etab_feature_s").val()}, function (data) {
                $(".etab_feature_s_result").html(data);
            });
        });
        $(".ex_search").keypress(function () {
            $.post(extratabspro_url + "ajax_extratabspro.php", {search: $(".ex_search").val()}, function (data) {
                $(".ex_search_result").html(data);
            })
        });

        $(".ex_search_product").keypress(function () {
            $.post(extratabspro_url + "ajax_extratabspro.php", {search_product: $(".ex_search_product").val()}, function (data) {
                $(".ex_search_products").html(data);
            })
        });

        $(".ex_search_manuf").keypress(function () {
            $.post(extratabspro_url + "ajax_extratabspro.php", {search_manufacturer: $(".ex_search_manuf").val()}, function (data) {
                $(".ex_search_manufacturers").html(data);
            })
        });

        var $mySlides = $("#productextratab");
        $mySlides.sortable({
            opacity: 0.6,
            cursor: "move",
            update: function () {
                var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
                $.post(extratabspro_url + "ajax_extratabspro.php", order);
            }
        });
        $mySlides.hover(function () {
                $(this).css("cursor", "move");
            },
            function () {
                $(this).css("cursor", "auto");
            });

        $('#load_all_tabs').click(function () {
            var postoptions = "action=LoadAllTabs&token=" + token;
            $.post(extratabspro_url + "ajax_extratabspro.php", postoptions, function (data) {
                $('#tabs_loader').html(data);
                initDroppableTokes();
            });
        });

        $('.timepicker').datetimepicker({
            format: 'HH:mm:ss'
        });


        if (typeof initDroppableTokens === "function") {
            initDroppableTokens();
        }
    }
});


function changeMain(clicked, iso) {
    clicked.parent().parent().parent().find('.dropdown-toggle').html(iso + '<i class="icon-caret-down"></i>');
}

function extratabsprosubmit() {
    var theForm = $("#EXTRAglobalsettings").clone();
    $('<form id="EXTRAform3" name="EXTRAform3" style="display:none!important;" action="' + $("#EXTRAglobalsettings").attr('action') + '" method="POST"><div id="EXTRAform2"></div></form>').appendTo('body');
    $('#EXTRAform2').replaceWith(theForm);
    $("#EXTRAform3 select[name='extratabspro_stock']").val($("#extraTabsProDiv #EXTRAglobalsettings select[name='extratabspro_stock']").val());
    $("#EXTRAform3 select[name='extratabspro_cms']").val($("#extraTabsProDiv #EXTRAglobalsettings select[name='extratabspro_cms']").val());
    $("#EXTRAform3 select[name='extratabspro_everywhere']").val($("#extraTabsProDiv #EXTRAglobalsettings select[name='extratabspro_everywhere']").val());
    $("#EXTRAform3 select[name='extratabspro_allconditions']").val($("#extraTabsProDiv #EXTRAglobalsettings select[name='extratabspro_allconditions']").val());
    $("#EXTRAform3 select[name='extratabspro_allshops']").val($("#extraTabsProDiv #EXTRAglobalsettings select[name='extratabspro_allshops']").val());
    $("#EXTRAform3 select[name='extratabspro_cms_body']").val($("#extraTabsProDiv #EXTRAglobalsettings select[name='extratabspro_cms_body']").val());
    $("#EXTRAform3 select[name='etab_feature']").val($("#extraTabsProDiv #EXTRAglobalsettings select[name='etab_feature']").val());
    EXTRAform3.submit();
}

function addClass(id) {
    tinyMCE.execCommand('mceToggleEditor', false, id);
}

function removeClass(id) {
    tinyMCE.execCommand('mceToggleEditor', false, id);
}

function extratab_unhook(id_tab, id_product) {
    var r = confirm(extratabspro_delete_message);
    if (r == true) {
        var postoptions = "id_tab=" + id_tab + "&token" + token + "&id_product=" + id_product + "&action=unhookTab";
        $.post(extratabspro_url + "ajax_extratabspro.php", postoptions, function (data) {
            $("#productextratab_" + id_tab).fadeOut('slow');
        });
    }
}

function extratab_remove(id_tab) {
    var r = confirm(extratabspro_delete_permanent_message);
    if (r == true) {
        var postoptions = "id_tab=" + id_tab + "&token" + token + "&action=removeTab";
        $.post(extratabspro_url + "ajax_extratabspro.php", postoptions, function (data) {
            $("#productextratab_" + id_tab).fadeOut('slow');
            $("#productExtratab_" + id_tab).fadeOut('slow');
        });
    }
}

function extratab_toggle(id_tab) {
    var postoptions = "id_tab=" + id_tab + "&token" + token + "&action=toggleTab";
    $.post(extratabspro_url + "ajax_extratabspro.php", postoptions, function (data) {
        eval(data);
    });
}

function etab_addinput(name, id) {
    $('.etab_feature_selected').append('<div><input type="hidden" name="etab_feature_v[]" value="' + id + '"/>' + name + ' <span class="remove" onclick="$(this).parent().remove();"></span></div>');
}

function initDroppableTokes() {
    $("#productextratab").droppable({
        accept: '#extraTabsProDivTemplates .tokenfield  .token',
        drop: function (event, ui) {
            var draggableId = ui.draggable.attr("id");
            addTabToProduct(extratabspro_id_product, draggableId);
        }
    });

    $("#extraTabsProDivTemplates .tokenfield .token").draggable({
        revert: function (event, ui) {
            $(this).data("uiDraggable").originalPosition = {
                top: 0,
                left: 0
            };
            return !event;
        }
    });

    $("#extraTabsProDivTemplates .tokenfield .token").mousedown(function (e) {
        $("#productextratab").addClass("ui-state-highlight");
        $(this).addClass("zIndex4");
    }).mouseup(function () {
        $("#productextratab").removeClass("ui-state-highlight");
        $(this).removeClass("zIndex4");
    });

    function addTabToProduct(id_product, id_tab) {
        var postoptions = "id_tab=" + id_tab + "&id_product=" + id_product + "&action=AddTabToProduct&token=" + token;
        $.post(extratabspro_url + "ajax_extratabspro.php", postoptions, function (data) {
            $("#productextratab alert").fadeOut('slow');
            $("#" + id_tab).fadeOut('slow');
            $("#productextratab").removeClass("ui-state-highlight");
            eval(data);
        });
    }
}