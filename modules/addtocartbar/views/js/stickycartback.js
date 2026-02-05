/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2023
 *  @license   Single domain
 */
var mod_url = sticky_action_url; //html content
var error_msg = 'Product is already in selection list';
var rec_type_for_device = DEVICE_CHECK_SWITCH;
var sticky_on_off = STICKY_ON_OFF;
var product_types = STICKY_PRO_TYPE_TOGGLE;
var sticky_category = STICKY_CATEGORY;
var sticky_exe_product = STICKY_EXC_PRODUCTS_ON_OFF;
var psversion = _ps_version;

$(document).ready(function () {
        // Add a change event handler to the "Check All" checkbox
      $('#checkAllCheckbox').change(function() {
        // Get the checked status of the "Check All" checkbox
        var isChecked = $(this).prop('checked');
    
        // Set the checked status of all checkboxes with the name "categoryBox[]"
        $('input[name="categoryBox[]"]').prop('checked', isChecked);
      });
      
        // Find the form-groups with the specific label texts
        var formGroups_checkbox = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText.includes("SelectDevicestodisableviewDesktopviewTabletviewPhoneview");
        });
        var formGroups_resolution = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText.includes("SetResolution:StartingResolution:EndingResolution:");
        });
        var formGroups_date = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText.includes("SelectDate");
        });
        var formGroups_product_type = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText.includes("SelectspecificproducttypetodisplaystickyAddtocart");
        });
        var formGroup_select_category = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText.includes("Selectcategories");
        });
        var formGroup_find_product = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText.includes("Findproductstoinclude");
        });


        var formGroup = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText === "DevicerecognizationResolutionUserAgent";
        });
        var radiobutton = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText === "StickybarOn/OffOnOff";
        });
        var radioButtonSpecificPRoduct = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText === "ProductTypesEnableDisable";
        });
        var radioButtonSelectCategory = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText === "CategoriesEnableDisable";
        });
        var radioButtonSFindProduct = $(".form-group").filter(function () {
            var labelText = $(this).find("label").text().replace(/\s+/g, '');
            return labelText === "FindProductsEnableDisable";
        });


        if (psversion <= "1.7.7.6") {
            
            $("#DEVICE_CHECK_SWITCH_on").next("label").text("Resolution");
            $("#DEVICE_CHECK_SWITCH_off").next("label").text("User Agent");
            $("input[name='DEVICE_CHECK_SWITCH']").change(function () {
                // Perform your task here
                if ($(this).is(":checked")) {
                    var selectedValue = $(this).val();
                    console.log("Selected value: " + selectedValue);
                    // Perform additional actions based on the selected value
                    if (selectedValue === "1") {
                        formGroups_checkbox.hide();
                        formGroups_resolution.show();


                    } else if (selectedValue === "0") {
                        formGroups_resolution.hide();
                        formGroups_checkbox.show();

                    }
                }
            });
            $("#STICKY_ON_OFF_on").next("label").text("On");
            $("#STICKY_ON_OFF_off").next("label").text("Off");
            $("input[name='STICKY_ON_OFF']").change(function () {
                // Perform your task here
                if ($(this).is(":checked")) {
                    var selectedValue = $(this).val();
                    // Perform additional actions based on the selected value
                    if (selectedValue === "1") {
                        formGroups_date.show();
                    } else if (selectedValue === "0") {
                        formGroups_date.hide();

                    }
                }
            });
            $("#STICKY_PRO_TYPE_TOGGLE").next("label").text("Enable");
            $("#STICKY_PRO_TYPE_TOGGLE").next("label").text("Disable");
            $("input[name='STICKY_PRO_TYPE_TOGGLE']").change(function () {
                // Perform your task here
                if ($(this).is(":checked")) {
                    var selectedValue = $(this).val();
                    // Perform additional actions based on the selected value
                    if (selectedValue === "1") {
                        formGroups_product_type.show();
                    } else if (selectedValue === "0") {
                        formGroups_product_type.hide();

                    }
                }
            });
            $("#STICKY_CATEGORY").next("label").text("Enable");
            $("#STICKY_CATEGORY").next("label").text("Disable");
            $("input[name='STICKY_CATEGORY']").change(function () {
                // Perform your task here
                if ($(this).is(":checked")) {
                    var selectedValue = $(this).val();
                    // Perform additional actions based on the selected value
                    if (selectedValue === "1") {
                        formGroup_select_category.show();
                    } else if (selectedValue === "0") {
                        formGroup_select_category.hide();

                    }
                }
            });
            $("#STICKY_EXC_PRODUCTS_ON_OFF").next("label").text("Enable");
            $("#STICKY_EXC_PRODUCTS_ON_OFF").next("label").text("Disable");
            $("input[name='STICKY_EXC_PRODUCTS_ON_OFF']").change(function () {
                // Perform your task here
                if ($(this).is(":checked")) {
                    var selectedValue = $(this).val();
                    // Perform additional actions based on the selected value
                    if (selectedValue === "1") {
                        formGroup_find_product.show();
                    } else if (selectedValue === "0") {
                        formGroup_find_product.hide();

                    }
                }
            });


        }
        else {
            //Find the radio buttons inside the form group
            var radioButtons = formGroup.find("input[type='radio']");
            // Add a function to the toggle event of the radio buttons
            radioButtons.on("change", function () {
                // Perform the desired action based on the selected radio button
                var selectedValue = $(this).val();
                if (selectedValue === "1") {
                    formGroups_checkbox.hide();
                    formGroups_resolution.show();
                } else if (selectedValue === "0") {
                    formGroups_resolution.hide();
                    formGroups_checkbox.show();
                }
            });

            var SpecificPRoduct = radioButtonSpecificPRoduct.find("input[type='radio']");
            SpecificPRoduct.on("change", function () {
                var selectedValue = $(this).val();
                if (selectedValue === "1") {
                    formGroups_product_type.show();
                } else if (selectedValue === "0") {
                    formGroups_product_type.hide();
                }
            });
            var radioButtonStickyBar = radiobutton.find("input[type='radio']");
            radioButtonStickyBar.on("change", function () {
                var selectedValue = $(this).val();
                if (selectedValue === "1") {
                    formGroups_date.show();
                } else if (selectedValue === "0") {
                    formGroups_date.hide();
                }
            });
            var radiobuttonCategory = radioButtonSelectCategory.find("input[type='radio']");
            radiobuttonCategory.on("change", function () {
                var selectedValue = $(this).val();
                if (selectedValue === "1") {
                    formGroup_select_category.show();
                } else if (selectedValue === "0") {
                    formGroup_select_category.hide();
                }
            });
            var radiobuttonProduct = radioButtonSFindProduct.find("input[type='radio']");
            radiobuttonProduct.on("change", function () {
                var selectedValue = $(this).val();
                if (selectedValue === "1") {
                    formGroup_find_product.show();
                } else if (selectedValue === "0") {
                    formGroup_find_product.hide();
                }
            });
        }

        // Hide the form-groups
        if (STICKY_ON_OFF == 1) {
            formGroups_date.show();

        } else {
            formGroups_date.hide();
        }
        if (STICKY_PRO_TYPE_TOGGLE == 1) {
            formGroups_product_type.show();

        } else {
            formGroups_product_type.hide();
        }
        if (STICKY_EXC_PRODUCTS_ON_OFF == 1) {
            formGroup_find_product.show();

        } else {
            formGroup_find_product.hide();
        }
        if (STICKY_CATEGORY == 1) {
            formGroup_select_category.show();

        } else {
            formGroup_select_category.hide();
        }
        if (DEVICE_CHECK_SWITCH == 1) {
            formGroups_checkbox.hide();

        } else {
            formGroups_resolution.hide();
        }
});

function getRelProducts(e) {
    var search_q_val = $(e).val();
    if (typeof search_q_val !== 'undefined' && search_q_val) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: mod_url + '&q=' + search_q_val,
            success: function (data) {
                var quicklink_list = '<li class="rel_breaker" onclick="relClearData();"><i class="material-icons">&#xE14C;</i></li>';
                $.each(data, function (index, value) {
                    var fixitname = data[index]['name'];
                    var namefixed = fixitname.replace(/'/, "\\'");
                    if (typeof data[index]['id'] !== 'undefined') quicklink_list += '<li onclick="relSelectThis(' + data[index]['id'] + ',' + data[index]['id_product_attribute'] + ',\'' + namefixed + '\',\'' + data[index]['image'] + '\');"><img src="' + data[index]['image'] + '" width="60"> ' + data[index]['name'] + '</li>';
                });
                if (data.length == 0) {
                    quicklink_list = '';
                }
                $('#rel_holder').html('<ul>' + quicklink_list + '</ul>');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });
    } else {
        $('#rel_holder').html('');
    }
}

function relSelectThis(id, ipa, name, img) {
    if ($('#row_' + id).length > 0) {
        showErrorMessage(error_msg);
    } else {
        var draw_html = '<li id="row_' + id + '" class="media"><div class="media-left"><img src="' + img + '" class="media-object image"></div><div class="media-body media-middle"><span class="label">' + name + '&nbsp;(ID:' + id + ')</span><i onclick="relDropThis(this);" class="material-icons delete">clear</i></div><input type="hidden" value="' + id + '" name="STICKY_EXC_PRODUCTS[]"></li>'
        $('#rel_holder_temp ul').append(draw_html);
    }
}

function relClearData() {
    $('#rel_holder').html('');
}

function relDropThis(e) {
    $(e).parent().parent().remove();
}
$(document).ready(function () {
    var selected = $("#STICKY_POS").find(":selected").text();
    if (selected == 'bottom') {
        $('#STICKY_BODY_PAD').parent().parent().show();
    } else {
        $('#STICKY_BODY_PAD').parent().parent().hide();
    }
    $("#STICKY_POS").change(function () {
        selected = $("#STICKY_POS").find(":selected").text();
        if (selected == 'bottom') {
            $('#STICKY_BODY_PAD').parent().parent().show();
        } else {
            $('#STICKY_BODY_PAD').parent().parent().hide();
        }
    });
});
