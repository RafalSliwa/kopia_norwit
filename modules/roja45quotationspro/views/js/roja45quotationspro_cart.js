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

    // TODO - If normal mode
    // roja45quotationspro_quotationscart.overrideButtonsInThePage();
    // TODO - If Catalog mode
    // TODO - Add buttons

    var current_timestamp = parseInt(new Date().getTime() / 1000);

    if (
        typeof $('.ajax_quote_quantity').html() == 'undefined' ||
        (typeof generated_date != 'undefined' && generated_date != null && (parseInt(generated_date) + 30) < current_timestamp)
    ) {
        roja45quotationspro_quotationscart.refresh();
    }

    var is_touch_enabled = false;
    if ('ontouchstart' in document.documentElement) {
        is_touch_enabled = true;
    }

    $(document).on('click', '#header .quotation_cart > a:first', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Simulate hover when browser says device is touch based
        if (is_touch_enabled) {
            if ($(this).next('.roja45quotationspro_block:visible').length) {
                $("#header .roja45quotationspro_block").stop(true, true).slideUp(450);
                $("#header .quotation_cart").addClass('collapsed');
            } else if (roja45quotationspro_quotationscart.nb_total_products > 0 || parseInt($('.ajax_quote_quantity').html()) > 0) {
                $("#header .roja45quotationspro_block").stop(true, true).slideDown(450);
                $("#header .quotation_cart").removeClass('collapsed');
            }
            return;
        } else {
            if (roja45quotationspro_quotationscart.nb_total_products > 0 || parseInt($('.ajax_quote_quantity').html()) > 0) {
                if ($("#header .quotation_cart").hasClass('collapsed')) {
                    $("#header .quotation_cart").removeClass('collapsed');
                    $("#header .quotation_cart .roja45quotationspro_block").stop(true, true).slideDown(450);
                } else {
                    $("#header .quotation_cart").addClass('collapsed');
                    $("#header .quotation_cart .roja45quotationspro_block").stop(true, true).slideUp(450);
                }

            }
        }

    });
    $(document).click(function (e) {
        if (!$("#header .quotation_cart").hasClass('collapsed')) {
            $("#header .quotation_cart").addClass('collapsed');
            $("#header .quotation_cart .roja45quotationspro_block").stop(true, true).slideUp(450);
        }
    });

    $(document).on('click', '#cart_navigation input', function (e) {
        $(this).prop('disabled', 'disabled').addClass('disabled');
        $(this).closest("form").get(0).submit();
    });


    $(document).off('click', '.ajax_add_quote_button').on('click', '.ajax_add_quote_button', function (e) {
        e.preventDefault();

        var idProduct =  $(this).attr('data-id-product');
        var idProductAttribute =  parseInt($(this).attr('data-id-product-attribute'));
        var quantity = $(roja45quotationspro_productselector_qty).val();
        var minimalQuantity =  $(this).attr('data-minimal-quantity');
        if (!minimalQuantity) {
            minimalQuantity = 1;
        }

        if ($(this).attr('disabled') != 'disabled') {
            roja45quotationspro_quotationscart.add(idProduct, idProductAttribute, false, this, minimalQuantity, quantity);
        }
    });

    $(document).off('click', '.quote_block_list .ajax_quote_block_remove_link').on('click', '.quote_block_list .ajax_quote_block_remove_link', function (e) {
        e.preventDefault();
        var customizationId = 0;
        var productId = 0;
        var productAttributeId = 0;
        //retrieve idProduct and idCombination from the displayed product in the block cart
        var firstCut = $(this).closest('dt').attr('data-id').replace('quote_block_product_', '');
        var ids = firstCut.split('_');
        productId = parseInt(ids[0]);

        if (typeof(ids[1]) != 'undefined') {
            productAttributeId = parseInt(ids[1]);
        }

        var id_roja45_quotation_request = $(this).attr('data-id-roja45-quotation-request');
        var id_roja45_quotation_requestprodcuct = $(this).attr('data-id-roja45-quotation-request-product');
        // Removing product from the cart
        roja45quotationspro_quotationscart.remove(id_roja45_quotation_request, id_roja45_quotation_requestprodcuct);
    });

    $(document).on('click', '#layer_quote .cross, #layer_quote .continue, .layer_quote_overlay', function(e){
        e.preventDefault();
        $('.layer_quote_overlay').hide();
        $('#layer_quote').fadeOut('fast');
    });

    $('#columns #layer_quote, #columns .layer_quote_overlay').detach().prependTo('#columns');
});

var roja45quotationspro_quotationscart = {
    nb_total_products: 0,

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

    // Fix display when using back and previous browsers buttons
    refresh: function () {
        /*
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: baseUri + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'controller=cart&ajax=true&token=' + static_token,
            success: function (jsonData) {
                roja45quotationspro_quotationscart.updateCart(jsonData);
            }
        });
        */
        // TODO - Use cookie to store selected products
    },

    // Update the cart information
    updateQuoteInformation: function (jsonData, addedFromProductPage) {
        roja45quotationspro_quotationscart.updateQuote(jsonData);
        //reactive the button when adding has finished
        if (addedFromProductPage) {
            $('#add_to_cart button').removeProp('disabled').removeClass('disabled');
            if (!jsonData.hasError || jsonData.hasError == false)
                $('#add_to_cart button').addClass('added');
            else
                $('#add_to_cart button').removeClass('added');
        }
        else
            $('.ajax_add_to_cart_button').removeProp('disabled');
    },

    // add a product in the cart via ajax
    add: function (idProduct, idCombination, addedFromProductPage, callerElement, minimalQuantity, quantity) {
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
                'id_product_attribute' : ((idCombination && idCombination != null) ? idCombination : '0')
            },
            beforeSend: function () {
                $(this).attr('disabled', 'disabled');
                $(this).addClass('disabled');
                roja45quotationspro.toggleWaitDialog();
            },
            success: function (data) {
                if (data.result) {
                    roja45quotationspro.displaySuccessMsg([roja45quotationspro_added_success]);
                    roja45quotationspro_quotationscart.updateQuoteInformation(data);
                    if (idCombination) {
                        $(data.quotation_products).each(function(){
                            if (this.id_product != undefined && this.id_product == parseInt(idProduct) && this.id_product_attribute == parseInt(idCombination)) {
                                roja45quotationspro_quotationscart.updateLayer(this);
                            }
                        });
                    } else {
                        $(data.quotation_products).each(function(){
                            if (this.id_product != undefined && this.id_product == parseInt(idProduct)) {
                                roja45quotationspro_quotationscart.updateLayer(this);
                            }
                        });
                    }
                    $(callerElement).addClass('added');
                } else {
                    roja45quotationspro.displayErrorMsg([roja45quotationspro_added_failed]);
                    roja45quotationspro.displayErrorMsg(data.errors);
                }
            },
            error: function (data) {
                console.log(data.responseText);
                roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
            },
            complete: function (data) {
                $(callerElement).removeAttr('disabled');
                $(callerElement).removeClass('disabled');
                roja45quotationspro.toggleWaitDialog();
            }
        });
    },

    //remove a product from the cart via ajax
    remove: function (id_roja45_quotation_request, id_roja45_quotation_requestproduct) {
        $.ajax({
            url: roja45quotationspro_controller,
            type: 'post',
            dataType: 'json',
            data: {
                'action' : 'deleteProductFromRequest',
                'id_roja45_quotation_request' : id_roja45_quotation_request,
                'id_roja45_quotation_requestproduct' : id_roja45_quotation_requestproduct,
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
                console.log(data.responseText);
                roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
            },
            complete: function (data) {
                roja45quotationspro.toggleWaitDialog();
            }
        });
    },

    //hide the products displayed in the page but no more in the json data
    hideOldProducts: function (jsonData) {
        //delete an eventually removed product of the displayed cart (only if cart is not empty!)
        if ($('.quote_block_list:first dl.products').length > 0) {
            var removedProductId = null;
            var removedProductData = null;
            var removedProductDomId = null;
            //look for a product to delete...
            $('.quote_block_list:first dl.products dt').each(function () {
                //retrieve idProduct and idCombination from the displayed product in the block cart
                var domIdProduct = $(this).data('id');
                var firstCut = domIdProduct.replace('quote_block_product_', '');
                var ids = firstCut.split('_');

                //try to know if the current product is still in the new list
                var stayInTheCart = false;
                for (aProduct in jsonData.quotation_products) {
                    //we've called the variable aProduct because IE6 bug if this variable is called product
                    //if product has attributes
                    if (jsonData.quotation_products[aProduct]['id_product'] == ids[0] && (!ids[1] || jsonData.quotation_products[aProduct]['id_product_attribute'] == ids[1])) {
                        stayInTheCart = true;
                        // update the product customization display (when the product is still in the cart)
                        //roja45quotationspro_quotationscart.hideOldProductCustomizations(jsonData.products[aProduct], domIdProduct);
                    }
                }
                //remove product if it's no more in the cart
                if (!stayInTheCart) {
                    removedProductId = $(this).data('id');
                    if (removedProductId != null) {
                        var firstCut = removedProductId.replace('quote_block_product_', '');
                        var ids = firstCut.split('_');

                        $('dt[data-id="' + removedProductId + '"]').addClass('strike').fadeTo('slow', 0, function () {
                            $(this).slideUp('slow', function () {
                                $(this).remove();
                                // If the cart is now empty, show the 'no product in the cart' message and close detail
                                if ($('.roja45quotationspro_block:first dl.products dt').length == 0) {
                                    $('.ajax_quote_quantity').html('0');
                                    $("#header .roja45quotationspro_block").stop(true, true).slideUp(200);
                                    $('.ajax_quote_no_product:hidden').slideDown(450);
                                    $('.roja45quotationspro_block dl.products').remove();
                                }
                            });
                        });
                        $('dd[data-id="quote_block_combination_of_' + ids[0] + (ids[1] ? '_' + ids[1] : '') + (ids[2] ? '_' + ids[2] : '') + '"]').fadeTo('fast', 0, function () {
                            $(this).slideUp('fast', function () {
                                $(this).remove();
                            });
                        });
                    }
                }
            });
        }
    },

    hideOldProductCustomizations: function (product, domIdProduct) {
        var customizationList = $('ul[data-id="customization_' + product['id_product'] + '_' + product['id_product_attribute'] + '"]');
        if (customizationList.length > 0) {
            $(customizationList).find("li").each(function () {
                $(this).find("div").each(function () {
                    var customizationDiv = $(this).data('id');
                    var tmp = customizationDiv.replace('deleteCustomizableProduct_', '');
                    var ids = tmp.split('_');
                    if ((parseInt(product.id_product_attribute) == parseInt(ids[2])) && !roja45quotationspro_quotationscart.doesCustomizationStillExist(product, ids[0]))
                        $('div[data-id="' + customizationDiv + '"]').parent().addClass('strike').fadeTo('slow', 0, function () {
                            $(this).slideUp('slow');
                            $(this).remove();
                        });
                });
            });
        }

        var removeLinks = $('.deleteCustomizableProduct[data-id="' + domIdProduct + '"]').find('.ajax_quote_block_remove_link');
        if (!product.hasCustomizedDatas && !removeLinks.length)
            $('div[data-id="' + domIdProduct + '"]' + ' span.remove_link').html('<a class="ajax_quote_block_remove_link" rel="nofollow" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + product['id_product'] + '&amp;ipa=' + product['id_product_attribute'] + '&amp;token=' + static_token + '"> </a>');

    },

    doesCustomizationStillExist: function (product, customizationId) {
        var exists = false;

        $(product.customizedDatas).each(function () {
            if (this.customizationId == customizationId) {
                exists = true;
                // This return does not mean that we found nothing but simply break the loop
                return false;
            }
        });
        return (exists);
    },

    // Update product quantity
    updateProductQuantity: function (product, quantity) {
        $('dt[data-id=quote_block_product_' + product.id_product + '_' + (product.id_product_attribute ? product.id_product_attribute : '0') + '] .quantity').fadeTo('fast', 0, function () {
            $(this).text(quantity);
            $(this).fadeTo('fast', 1, function () {
                $(this).fadeTo('fast', 0, function () {
                    $(this).fadeTo('fast', 1, function () {
                        $(this).fadeTo('fast', 0, function () {
                            $(this).fadeTo('fast', 1);
                        });
                    });
                });
            });
        });
    },

    //display the products witch are in json data but not already displayed
    displayNewProducts: function (jsonData) {
        //add every new products or update displaying of every updated products
        $(jsonData.quotation_products).each(function () {
            //fix ie6 bug (one more item 'undefined' in IE6)
            if (this.id_roja45_quotation_request != undefined) {
                //create a container for listing the products and hide the 'no product in the cart' message (only if the cart was empty)

                if ($('.roja45quotationspro_block:first dl.products').length == 0) {
                    $('.quote_block_no_products').before('<dl class="products"></dl>');
                    $('.quote_block_no_products').hide();
                }
                //if product is not in the displayed cart, add a new product's line
                var domIdProduct = this.id_product + '_' + (this.id_product_attribute ? this.id_product_attribute : '0');
                var domIdProductAttribute = this.id_product + '_' + (this.id_product_attribute ? this.id_product_attribute : '0');

                if ($('dt[data-id="quote_block_product_' + domIdProduct + '"]').length == 0) {
                    var productId = parseInt(this.id_product);
                    var productAttributeId = (this.attributes ? parseInt(this.attributes) : 0);
                    var content = '<dt class="unvisible" data-id="quote_block_product_' + domIdProduct + '">';
                    var name = $.trim($('<span />').html(this.name).text());
                    //name = (name.length > 12 ? name.substring(0, 10) + '...' : name);
                    content += '<a class="quote-product-image" href="' + this.link + '" title="' + name + '"><img  src="' + this.image_quote_cart + '" alt="' + this.name + '"></a>';
                    content += '<div class="quote-product-info"><div class="product-name">' + '<span class="quantity-formated"><span class="quantity">' + this.quote_quantity + '</span>&nbsp;x&nbsp;</span><a href="' + this.link + '" title="' + this.name + '" class="quote_block_product_name">' + name + '</a></div>';

                    if (this.attributes) {
                        content += '<div class="product-atributes"><a href="' + this.link + '" title="' + this.name + '">' + this.attributes + '</a></div>';
                    }

                    if (typeof(this.is_gift) == 'undefined' || this.is_gift == 0) {
                        content += '<span class="remove_link"><a rel="nofollow" class="ajax_quote_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;token=' + static_token + (this.attributes ? '&amp;ipa=' + parseInt(this.idCombination) : '') + '"> </a></span>';
                    } else {
                        content += '<span class="remove_link"></span>';
                    }

                    content += '</dt>';
                    if (this.attributes)
                        content += '<dd data-id="quote_block_combination_of_' + domIdProduct + '" class="unvisible">';
                    if (this.hasCustomizedDatas)
                        content += roja45quotationspro_quotationscart.displayNewCustomizedDatas(this);
                    if (this.attributes) content += '</dd>';

                    $('.roja45quotationspro_block dl.products').append(content);
                } else {
                    var jsonProduct = this;
                    if ($.trim($('dt[data-id="quote_block_product_' + domIdProduct + '"] .quantity').html()) != jsonProduct.quote_quantity || $.trim($('dt[data-id="quote_block_product_' + domIdProduct + '"] .price').html()) != jsonProduct.priceByLine) {
                        roja45quotationspro_quotationscart.updateProductQuantity(jsonProduct, jsonProduct.quote_quantity);
                        // Customized product
                        if (jsonProduct.hasCustomizedDatas) {
                            customizationFormatedDatas = roja45quotationspro_quotationscart.displayNewCustomizedDatas(jsonProduct);
                            if (!$('ul[data-id="customization_' + domIdProductAttribute + '"]').length) {
                                if (jsonProduct.hasAttributes)
                                    $('dd[data-id="quote_block_combination_of_' + domIdProduct + '"]').append(customizationFormatedDatas);
                                else
                                    $('.roja45quotationspro_block dl.products').append(customizationFormatedDatas);
                            }
                            else {
                                $('ul[data-id="customization_' + domIdProductAttribute + '"]').html('');
                                $('ul[data-id="customization_' + domIdProductAttribute + '"]').append(customizationFormatedDatas);
                            }
                        }
                    }
                }
                $('.roja45quotationspro_block dl.products .unvisible').slideDown(450).removeClass('unvisible');

                var removeLinks = $('dt[data-id="quote_block_product_' + domIdProduct + '"]').find('a.ajax_quote_block_remove_link');
                if (this.hasCustomizedDatas && removeLinks.length)
                    $(removeLinks).each(function () {
                        $(this).remove();
                    });
            }
        });
    },

    displayNewCustomizedDatas: function (product) {
        var content = '';
        var productId = parseInt(product.id);
        var productAttributeId = typeof(product.id_product_attribute) == 'undefined' ? 0 : parseInt(product.id_product_attribute);
        var hasAlreadyCustomizations = $('ul[data-id="customization_' + productId + '_' + productAttributeId + '"]').length;

        if (!hasAlreadyCustomizations) {
            if (!product.hasAttributes)
                content += '<dd data-id="cart_block_combination_of_' + productId + '" class="unvisible">';
            if ($('ul[data-id="customization_' + productId + '_' + productAttributeId + '"]').val() == undefined)
                content += '<ul class="cart_block_customizations" data-id="customization_' + productId + '_' + productAttributeId + '">';
        }

        $(product.customizedDatas).each(function () {
            var done = 0;
            customizationId = parseInt(this.customizationId);
            productAttributeId = typeof(product.id_product_attribute) == 'undefined' ? 0 : parseInt(product.id_product_attribute);
            content += '<li name="customization"><div class="deleteCustomizableProduct" data-id="deleteCustomizableProduct_' + customizationId + '_' + productId + '_' + (productAttributeId ? productAttributeId : '0') + '"><a rel="nofollow" class="ajax_cart_block_remove_link" href="' + baseUri + '?controller=cart&amp;delete=1&amp;id_product=' + productId + '&amp;ipa=' + productAttributeId + '&amp;id_customization=' + customizationId + '&amp;token=' + static_token + '"></a></div>';

            // Give to the customized product the first textfield value as name
            $(this.datas).each(function () {
                if (this['type'] == CUSTOMIZE_TEXTFIELD) {
                    $(this.datas).each(function () {
                        if (this['index'] == 0) {
                            content += ' ' + this.truncatedValue.replace(/<br \/>/g, ' ');
                            done = 1;
                            return false;
                        }
                    })
                }
            });

            // If the customized product did not have any textfield, it will have the customizationId as name
            if (!done)
                content += customizationIdMessage + customizationId;
            if (!hasAlreadyCustomizations) content += '</li>';
            // Field cleaning
            if (customizationId) {
                $('#uploadable_files li div.customizationUploadBrowse img').remove();
                $('#text_fields input').attr('value', '');
            }
        });

        if (!hasAlreadyCustomizations) {
            content += '</ul>';
            if (!product.hasAttributes) content += '</dd>';
        }
        return (content);
    },

    updateLayer: function (product) {
        $('#layer_quote_product_title').text(product.name);
        $('#layer_quote_product_attributes').text('');
        if (product.hasAttributes && product.hasAttributes == true) {
            $('#layer_quote_product_attributes').html(product.attributes);
        }

        $('#layer_quote_product_price').text(product.price);
        $('#layer_quote_product_quantity').text(product.quantity);
        $('.layer_quote_img').html('<img class="layer_quote_img img-responsive" src="' + product.image_quote_cart + '" alt="' + product.name + '" title="' + product.name + '" />');

        var n = (parseInt($(window).scrollTop())+100) + 'px';

        $('.layer_quote_overlay').css('width', '100%');
        $('.layer_quote_overlay').css('height', '100%');
        $('.layer_quote_overlay').show();
        //$('#layer_quote').css({'top': n}).fadeIn('fast');
        $('#layer_quote').fadeIn('fast');
        //crossselling_serialScroll();
    },

    //genarally update the display of the cart
    updateQuote: function (jsonData) {
        roja45quotationspro_quotationscart.updateQuoteEverywhere(jsonData);
        roja45quotationspro_quotationscart.hideOldProducts(jsonData);
        roja45quotationspro_quotationscart.displayNewProducts(jsonData);
        //update 'first' and 'last' item classes
        $('.roja45quotationspro_block .products dt').removeClass('first_item').removeClass('last_item').removeClass('item');
        $('.roja45quotationspro_block .products dt:first').addClass('first_item');
        $('.roja45quotationspro_block .products dt:not(:first,:last)').addClass('item');
        $('.roja45quotationspro_block .products dt:last').addClass('last_item');
    },

    //update general cart informations everywhere in the page
    updateQuoteEverywhere: function (jsonData) {
        this.nb_total_products = jsonData.quotation_products_num;
        if (parseInt(jsonData.quotation_products_num) > 0) {
            $('.ajax_quote_no_product').hide();
            $('.ajax_quote_quantity').text(jsonData.quotation_products_total);
            $('.ajax_quote_quantity').fadeIn('slow');

            if (parseInt(jsonData.quotation_products_num) > 1) {
                $('.ajax_quote_product_txt').each(function () {
                    $(this).hide();
                });
                $('.ajax_quote_product_txt_s').each(function () {
                    $(this).show();
                });
            }
            else {
                $('.ajax_quote_product_txt').each(function () {
                    $(this).show();
                });
                $('.ajax_quote_product_txt_s').each(function () {
                    $(this).hide();
                });
            }
        }
        else {
            $('.ajax_quote_quantity, .ajax_quote_product_txt_s, .ajax_quote_product_txt, .ajax_quote_total').each(function () {
                $(this).hide();
            });
            $('.ajax_quote_no_product').show('slow');
        }
    },


};

