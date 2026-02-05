/**
 * roja45ajaxcart.js.
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

$(document).ready(function () {
    if (roja45quotationspro_usejs) {
        $('<div class="float-xs-right" id="_mobile_quotecart"></div>').insertAfter('#_mobile_cart');
    }

    prestashop.on(
        'updatedProduct',
        function (event) {
            var id_product = 0;
            $('a.add-to-quote').each(function() {
                $('#quote_quantity_wanted').val(event.product_minimal_quantity);
                $('#quote_quantity_wanted').attr('min', event.product_minimal_quantity);
                $(this).attr('data-id-product-attribute', event.id_product_attribute);
                $(this).attr('data-quantity', $('#quote_quantity_wanted').val());
                var url = $(this).attr('data-url') + '&id_product=' + $(this).attr('data-id-product') + '&id_product_attribute=' + event.id_product_attribute + '&quantity=' + $('#quote_quantity_wanted').val();
                $(this).attr('href', url);
                if (!$(this).parent().hasClass('bootstrap-touchspin')) {
                    $('#quote_quantity_wanted').TouchSpin({
                        verticalbuttons: true,
                        verticalupclass: 'rojaquotationspro-icons touchspin-up',
                        verticaldownclass: 'rojaquotationspro-icons touchspin-down',
                        buttondown_class: 'btn btn-touchspin js-touchspin',
                        buttonup_class: 'btn btn-touchspin js-touchspin',
                        min: event.product_minimal_quantity,
                        max: 1000000
                    });
                }
            });
        }
    );

    prestashop.on(
        'responsive update',
        function (event) {
            var selector = roja45quotationspro_responsivecartnavselector.substring(1, roja45quotationspro_responsivecartnavselector.length);
            if ((roja45quotationspro_usejs>0) && $(roja45quotationspro_responsivecartnavselector).length) {
                if (event.mobile) {
                    var target = $(roja45quotationspro_responsivecartnavselector).children(":first");
                    if (target.length) {
                        $('#_mobile_quotecart').append(target);
                    }
                } else {
                    var target = $('#_mobile_quotecart').first();
                    if (target.length) {
                        $(roja45quotationspro_responsivecartnavselector).append(target);
                    }
                }
            }
        }
    );

    if (typeof(prestashop.responsive) != 'undefined') {
        if (prestashop.responsive.current_width < prestashop.responsive.min_width) {
            prestashop.emit('responsive update', {
                mobile: prestashop.responsive.mobile
            });
        }
    } else {
        if (window.innerWidth < 768) {
            prestashop.emit('responsive update', {
                mobile: window.innerWidth < 768
            });
        }
    }

    $(document).on('click', '.ajax_add_quote_button', function (e) {
        e.preventDefault();
        var idProduct =  parseInt($(this).attr('data-id-product'));
        var idProductAttribute =  parseInt($(this).attr('data-id-product-attribute'));
        var quantity =  parseInt($(this).closest('.roja45quotationspro_button_container').find(roja45quotationspro_productselector_qty).val());
        var minimalQuantity = 1;
        if (!quantity) {
            minimalQuantity =  parseInt($(this).attr('data-minimal-quantity'));
            quantity = minimalQuantity;
        }
        if (prestashop.responsive.mobile) {
            var element = $('#_mobile_quotecart');
        } else {
            var element = $('#_desktop_quotecart');
        }
        if (element.is(":visible")) {
            var position = element.attr('data-position');
            if (!position) {
                var nav = element.closest('nav');
                if (nav.length > 0) {
                    position = 'nav';
                } else {
                    position = 'top';
                }
            }
        }

        if ($(this).attr('disabled') != 'disabled') {
            roja45quotationspro_quotationscart.add(
                idProduct,
                idProductAttribute,
                false,
                this,
                minimalQuantity,
                quantity,
                position
            );
        }
    });

    $(document).on('click', 'button.add-to-quote', function (e) {
        var quantity =  parseInt($('#quote_quantity_wanted').val());
        $(this).closest('#roja45quotationspro_buttons_block').find('input[name=quantity]').val(quantity);
    });

    $(document).off('click', '.quote_block_list .ajax_quote_block_remove_link').on('click', '.quote_block_list .ajax_quote_block_remove_link', function (e) {
        e.preventDefault();
        // Customized product management
        var customizationId = 0;
        var productId = 0;
        var productAttributeId = 0;
        var firstCut = $(this).closest('dt').attr('data-id').replace('quote_block_product_', '');
        var ids = firstCut.split('_');
        productId = parseInt(ids[0]);

        if (typeof(ids[1]) != 'undefined') {
            productAttributeId = parseInt(ids[1]);
        }
        roja45quotationspro_quotationscart.remove(productId, productAttributeId);
    });

    $(document).on('click', '#layer_quote .cross, #layer_quote .continue, .layer_quote_overlay', function(e){
        e.preventDefault();
        $('.layer_quote_overlay').hide();
        $('#layer_quote').fadeOut('fast');
    });

    $('#columns #layer_quote, #columns .layer_quote_overlay').detach().prependTo('#columns');

    if (roja45quotationspro_enable_quote_dropdown && !prestashop.responsive.mobile) {
        $(document).on('click' , '._desktop_quotecart .quotation_cart a.quotation-cart-link', function(e) {
            e.preventDefault();
            if ($(this).closest('.quotation_cart').hasClass('collapsed')) {
                $(this).closest('.quotation_cart').removeClass('collapsed');
                $(this).closest('.quotation_cart .roja45quotationspro_block').removeClass('opened');
            } else {
                $(this).closest('.quotation_cart').addClass('collapsed');
                $(this).closest('.quotation_cart .roja45quotationspro_block').addClass('opened');
            }
        });

        $(document).on('click' , '.quotation_cart .remove-from-cart', function(e) {
            e.preventDefault();
            var ele = $(this);
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: $(this).attr('href'),
                async: true,
                cache: false,
                beforeSend: function () {
                },
                success: function (data) {
                    if (data.result) {
                        ele.closest('.quotation_cart').addClass('collapsed');
                        if (ele.closest('dl.products').find('dt').length > 1) {
                            ele.closest('dt').remove();
                        } else {
                            ele.closest('.quote-cart-block').remove();
                        }
                        $('.quotation_cart .ajax_quote_quantity').text(data.number_products);
                    } else {
                    }
                },
                error: function (data) {
                },
                complete: function (data) {
                }
            });

        });
    }

    if ($('#roja45quotationspro-modal').length > 0) {
        $('#roja45quotationspro-modal').appendTo('body');
    }
});

var roja45quotationspro_quotationscart = {
    nb_total_products: 0,

    showModal: function(modal) {
        if(roja45quotationspro_enablequotecartpopup) {
            $('#roja45quotationspro-modal').empty().append(modal);
            $('body').one('click', '#roja45quotationspro-modal', function (event) {
                if (event.target.id === 'roja45quotationspro-modal') {
                    $('#roja45quotationspro-modal').modal('hide');
                }
            });
            /*
            $('#roja45quotationspro-modal').remove();
            var $body = $('body');
            $body.append(modal);
            $body.one('click', '#roja45quotationspro-modal', function (event) {
                if (event.target.id === 'roja45quotationspro-modal') {
                    $('#roja45quotationspro-modal').modal('hide');
                }
            });

             */
            $('#roja45quotationspro-modal').modal('show');
        }
    },

    expand: function () {
        if ($('.cart_block_list').hasClass('collapsed')) {
            $('.cart_block_list.collapsed').slideDown({
                duration: 450,
                complete: function () {
                    $(this).parent().show(); // parent is hidden in global.js::accordion()
                    $(this).addClass('expanded').removeClass('collapsed');
                }
            });

            // save the expand statut in the user cookie
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: baseDir + 'modules/blockcart/blockcart-set-collapse.php' + '?rand=' + new Date().getTime(),
                async: true,
                cache: false,
                data: 'ajax_blockcart_display=expand',
                complete: function () {
                    $('.block_cart_expand').fadeOut('fast', function () {
                        $('.block_cart_collapse').fadeIn('fast');
                    });
                }
            });
        }
    },

    // add a product in the cart via ajax
    add: function (idProduct, idCombination, addedFromProductPage, callerElement, minimalQuantity, quantity, position) {
        if (addedFromProductPage) {
            $('#add_to_quote_cart button').prop('disabled', 'disabled').addClass('disabled');
            $('.filled').removeClass('filled');
        } else {
            $(callerElement).prop('disabled', 'disabled');
            $(callerElement).addClass('disabled');
        }

        $.ajax({
            url: roja45quotationspro_controller,
            headers: { "cache-control": "no-cache" },
            type: 'post',
            dataType: 'json',
            data: {
                'action' : 'addProductToRequest',
                'ajax' : 1,
                'quantity' : ((quantity && quantity != null) ? quantity : minimalQuantity),
                'id_product' : idProduct,
                'id_product_attribute' : ((idCombination && idCombination != null) ? idCombination : '0'),
                'position' : position,
                'mobile' : prestashop.responsive.mobile,
            },
            beforeSend: function () {
                $(this).attr('disabled', 'disabled');
                $(this).addClass('disabled');
            },
            success: function (data) {
                if (data.result) {
                    if (prestashop.responsive.mobile) {
                        var selector = roja45quotationspro_responsivecartnavselector.substring(1, roja45quotationspro_responsivecartnavselector.length);
                        var template = data.template.replaceAll(selector, '_mobile_quotecart');
                        $('#_mobile_quotecart').replaceWith(template);
                    } else {
                        var template = data.template;
                        $(roja45quotationspro_responsivecartnavselector).replaceWith(template);
                    }
                    roja45quotationspro_quotationscart.showModal(data.modal);
                } else {
                    roja45quotationspro.displayErrorMsg([roja45quotationspro_added_failed]);
                    roja45quotationspro.displayErrorMsg(data.errors);
                }
            },
            error: function (data) {
                roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
            },
            complete: function (data) {
                $(callerElement).removeAttr('disabled');
                $(callerElement).removeClass('disabled');
            }
        });
    },

    //remove a product from the cart via ajax
    remove: function (idProduct, idCombination) {
        $.ajax({
            url: roja45quotationspro_controller,
            type: 'post',
            dataType: 'json',
            data: {
                'action' : 'deleteProductFromRequest',
                'ajax' : 1,
            },
            beforeSend: function () {
                roja45quotationspro.toggleWaitDialog();
            },
            success: function (data) {
                if (data.result == 'success') {
                    roja45quotationspro.displaySuccessMsg([roja45quotationspro_deleted_success]);
                    roja45quotationspro_quotationscart.updateQuote(data);
                } else if (data.result == 'error') {
                    roja45quotationspro.displayErrorMsg([roja45quotationspro_deleted_failed]);
                    roja45quotationspro.displayErrorMsg(data.errors);
                } else {
                    roja45quotationspro.displayErrorMsg([roja45quotationspro_unknown_error]);
                }
            },
            error: function (data) {
                roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
            },
            complete: function (data) {
                roja45quotationspro.toggleWaitDialog();
            }
        });
    },

    swapChildren: function (obj1, obj2) {
        var temp = obj2.children().detach();
        obj2.empty().append(obj1.children().detach());
        obj1.append(temp);
    }
};


