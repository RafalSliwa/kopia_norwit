/**
 * roja45quotationspro.js.
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

(function($) {
    $.fn.inputFilter = function(inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = "";
            }
        });
    };
}(jQuery));

$(document).ready(function () {
    if (typeof prestashop !== 'undefined' && prestashop.page.page_name=='product') {
        //if ($.inArray(roja45quotationspro_id_product.toString(), roja45quotationspro_enabled)>-1) {
        if ($('#roja45quotationspro_buttons_block').length>0) {
            $('body').addClass('roja45-quotable-product');
            if (roja45_hide_add_to_cart && roja45quotationspro_usejs) {
                $(roja45quotationspro_productselector_addtocart).hide();
                $(roja45quotationspro_productselector_addtocart).addClass('roja45_hidden');
            }
            if (roja45_hide_price) {
                $(roja45quotationspro_productselector_price).hide();
                $(roja45quotationspro_productselector_price).addClass('roja45_hidden');
            }

            if (roja45quotationspro_usejs) {
                $('#roja45quotationspro_buttons_block').insertAfter(roja45quotationspro_productselector_addtocart);
            }
        }
    }

    $('.quote_quantity_wanted').each(function (index, element) {
        if ($(this).attr('data-touchspin-vertical') == 1) {
            $('.quote_quantity_wanted').TouchSpin({
                verticalbuttons: roja45quotationspro_touchspin,
                verticalupclass: 'rojaquotationspro-icons touchspin-up',
                verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                buttondown_class: 'btn btn-touchspin js-touchspin',
                buttonup_class: 'btn btn-touchspin js-touchspin',
                min: 1,
                max: 1000000,
                verticalbuttons: true
            });
        } else {
            $('.quote_quantity_wanted').TouchSpin({
                verticalbuttons: roja45quotationspro_touchspin,
                verticalupclass: 'rojaquotationspro-icons touchspin-up',
                verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                buttondown_class: 'btn btn-touchspin js-touchspin',
                buttonup_class: 'btn btn-touchspin js-touchspin',
                min: 1,
                max: 1000000,
                verticalbuttons: false
            });
        }
    });

    prestashop.on(
        'updateProduct',
        function (event) {
            //if (typeof roja45quotationspro_id_product !== 'undefined' && $.inArray(roja45quotationspro_id_product, roja45quotationspro_enabled)>-1) {
            if ($('#roja45quotationspro_buttons_block').length>0) {
                $('a.btn.add-to-quote').addClass('disabled');
            }
        }
    );

    prestashop.on(
        'updatedProduct',
        function (event) {
            //if (typeof roja45quotationspro_id_product !== 'undefined' && $.inArray(roja45quotationspro_id_product.toString(), roja45quotationspro_enabled)>-1) {
            if ($('#roja45quotationspro_buttons_block').length>0) {
                if (roja45_hide_add_to_cart) {
                    $(roja45quotationspro_productselector_addtocart).hide();
                    $(roja45quotationspro_productselector_addtocart).addClass('roja45_hidden');
                }
                if (roja45_hide_price) {
                    $(roja45quotationspro_productselector_price).hide();
                    $(roja45quotationspro_productselector_price).addClass('roja45_hidden');
                }
                $('a.btn.add-to-quote').removeClass('disabled');
                $('.quote_quantity_wanted').TouchSpin({
                    verticalbuttons: roja45quotationspro_touchspin,
                    verticalupclass: 'rojaquotationspro-icons touchspin-up',
                    verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                    buttondown_class: 'btn btn-touchspin js-touchspin',
                    buttonup_class: 'btn btn-touchspin js-touchspin',
                    min: 1,
                    max: 1000000
                });
                if (roja45quotationspro_usejs) {
                    $(roja45quotationspro_productselector_addtocart).parent('form').find('#roja45quotationspro_buttons_block').first().remove();
                    $('#roja45quotationspro_buttons_block').insertAfter(roja45quotationspro_productselector_addtocart);
                }
            }
        }
    );

    $(window).on('show.bs.modal', function(event) {
        var id = event.target.id;
        var ele = $('div.quickview #roja45quotationspro_buttons_block');
        if (id.includes('quickview-modal') && ele.length>0) {
            var qty_element = ele.find('.quote_quantity_wanted');
            var vertical = parseInt(qty_element.attr('data-touchspin-vertical'));
            if (vertical == 1) {
                qty_element.TouchSpin({
                    verticalbuttons: roja45quotationspro_touchspin,
                    verticalupclass: 'rojaquotationspro-icons touchspin-up',
                    verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                    buttondown_class: 'btn btn-touchspin js-touchspin',
                    buttonup_class: 'btn btn-touchspin js-touchspin',
                    min: 1,
                    max: 1000000,
                    verticalbuttons: true
                });
            } else {
                qty_element.TouchSpin({
                    verticalbuttons: roja45quotationspro_touchspin,
                    verticalupclass: 'rojaquotationspro-icons touchspin-up',
                    verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                    buttondown_class: 'btn btn-touchspin js-touchspin',
                    buttonup_class: 'btn btn-touchspin js-touchspin',
                    min: 1,
                    max: 1000000,
                    verticalbuttons: false
                });
            }

            if (roja45_hide_add_to_cart && roja45quotationspro_usejs) {
                $(roja45quotationspro_productselector_addtocart).hide();
                $(roja45quotationspro_productselector_addtocart).addClass('roja45_hidden');
                $('#roja45quotationspro_buttons_block').insertAfter(roja45quotationspro_productselector_addtocart);
            } else if (roja45quotationspro_usejs) {
                $('#roja45quotationspro_buttons_block').insertAfter(roja45quotationspro_productselector_addtocart);
            }
            if (roja45_hide_price) {
                $(roja45quotationspro_productselector_price).hide();
                $(roja45quotationspro_productselector_price).addClass('roja45_hidden');
            }
        }
    });

    prestashop.on(
        'updateProductList',
        function (event) {
            $(roja45quotationspro_productlistitemselector + ' .roja45quotationspro.product.enabled').each(function (index, element) {
                if (!$(this).closest('article').find(roja45quotationspro_productlistselector_flag + ' .quote').length && (roja45quotationspro_show_label == 1)) {
                    var ele = $(this).closest('article');
                    ele.find(roja45quotationspro_productlistselector_flag).append('<li class="product-flag quote">' + roja45quotationspro_quote_link_text + '</li>');
                }
                if (roja45_hide_price) {
                    $(this).closest(roja45quotationspro_productlistitemselector).find(roja45quotationspro_productlistselector_price).hide();
                }
                if (roja45_hide_add_to_cart && roja45quotationspro_usejs) {
                    $(this).closest(roja45quotationspro_productlistitemselector).find(roja45quotationspro_productlistselector_addtocart).hide();
                }
            });

            $('.quote_quantity_wanted').each(function (index, element) {
                if ($(this).attr('data-touchspin-vertical') == 1) {
                    $('.quote_quantity_wanted').TouchSpin({
                        verticalbuttons: roja45quotationspro_touchspin,
                        verticalupclass: 'rojaquotationspro-icons touchspin-up',
                        verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                        buttondown_class: 'btn btn-touchspin js-touchspin',
                        buttonup_class: 'btn btn-touchspin js-touchspin',
                        min: 1,
                        max: 1000000,
                        verticalbuttons: true
                    });
                } else {
                    $('.quote_quantity_wanted').TouchSpin({
                        verticalbuttons: roja45quotationspro_touchspin,
                        verticalupclass: 'rojaquotationspro-icons touchspin-up',
                        verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                        buttondown_class: 'btn btn-touchspin js-touchspin',
                        buttonup_class: 'btn btn-touchspin js-touchspin',
                        min: 1,
                        max: 1000000,
                        verticalbuttons: false
                    });
                }
            });
        }
    );

    prestashop.on(
        'afterUpdateProductList',
        function (event) {
            $(roja45quotationspro_productlistitemselector + ' .roja45quotationspro.product.enabled').each(function (index, element) {
                if (!$(this).closest('article').find(roja45quotationspro_productlistselector_flag + ' .quote').length && (roja45quotationspro_show_label == 1)) {
                    var ele = $(this).closest('article');
                    ele.find(roja45quotationspro_productlistselector_flag).append('<li class="product-flag quote">' + roja45quotationspro_quote_link_text + '</li>');
                }
                if (roja45_hide_price) {
                    $(this).closest(roja45quotationspro_productlistitemselector).find(roja45quotationspro_productlistselector_price).hide();
                }
                if (roja45_hide_add_to_cart && roja45quotationspro_usejs) {
                    $(this).closest(roja45quotationspro_productlistitemselector).find(roja45quotationspro_productlistselector_addtocart).hide();
                }
            });

            $('.quote_quantity_wanted').each(function (index, element) {
                if ($(this).attr('data-touchspin-vertical') == 1) {
                    $('.quote_quantity_wanted').TouchSpin({
                        verticalbuttons: roja45quotationspro_touchspin,
                        verticalupclass: 'rojaquotationspro-icons touchspin-up',
                        verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                        buttondown_class: 'btn btn-touchspin js-touchspin',
                        buttonup_class: 'btn btn-touchspin js-touchspin',
                        min: 1,
                        max: 1000000,
                        verticalbuttons: true
                    });
                } else {
                    $('.quote_quantity_wanted').TouchSpin({
                        verticalbuttons: roja45quotationspro_touchspin,
                        verticalupclass: 'rojaquotationspro-icons touchspin-up',
                        verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                        buttondown_class: 'btn btn-touchspin js-touchspin',
                        buttonup_class: 'btn btn-touchspin js-touchspin',
                        min: 1,
                        max: 1000000,
                        verticalbuttons: false
                    });
                }
            });
        }
    );

    $(roja45quotationspro_productlistitemselector + ' .roja45quotationspro.product.enabled').each(function (index, element) {
        if (!$(this).closest(roja45quotationspro_productlistitemselector).find(roja45quotationspro_productlistselector_flag + ' .quote').length && (roja45quotationspro_show_label == 1)) {
            var ele = $(this).closest(roja45quotationspro_productlistitemselector);
            ele.find(roja45quotationspro_productlistselector_flag).append('<li class="product-flag quote">' + roja45quotationspro_quote_link_text + '</li>');
        }
        if (roja45_hide_price) {
            $(this).closest(roja45quotationspro_productlistitemselector).find(roja45quotationspro_productlistselector_price).hide();
        }
        if (roja45_hide_add_to_cart && roja45quotationspro_usejs) {
            $(this).closest(roja45quotationspro_productlistitemselector).find(roja45quotationspro_productlistselector_addtocart).hide();
        }
    });

    $('.datepicker').each(function() {
        var format = $(this).attr('data-format');
        $(this).datepicker({
            prevText: '',
            nextText: '',
            dateFormat: format
        });
    });

    $('.quote_quantity_wanted').inputFilter(function(value) {
        return /^\d*$/.test(value);    // Allow digits only, using a RegExp
    });
});

roja45quotationspro = {
    quotationpro_addlabel: function (ele, id_product) {
        if (id_product && (roja45quotationspro_show_label == 1)) {
            var url = ele.closest('.ajax_block_product').find('.product_img_link').attr('href');
            ele.closest('.ajax_block_product').find('.product-image-container').append('<a class="quote-box ' + roja45quotationspro_label_position + '" href="' + url + '"><span class="quote-label">' + roja45quotationspro_quote_link_text + '</span></a>');
        }
    },

    displaySuccessMsg : function ( msgs ) {
        $.each(msgs, function(index, value) {
            $.growl({
                title: roja45quotationspro_success_title,
                message: value,
                duration: 3000,
                style: 'notice'
            });
        });
    },

    displayWarningMsg : function ( msgs ) {
        $.each(msgs, function(index, value) {
            $.growl({
                title: roja45quotationspro_warning_title,
                message: value,
                duration: 5000,
                style: 'warning'
            });
        });
    },

    displayErrorMsg : function ( msgs ) {
        $.each(msgs, function(index, value) {
            $.growl({
                title: roja45quotationspro_error_title,
                message: value,
                duration: 10000,
                style: 'error'
            });
        });
    }
}
