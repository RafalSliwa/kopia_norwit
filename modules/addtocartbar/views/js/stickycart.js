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
$(document).ready(function() {
    stickyProperties();
    prestashop.on('updatedProduct', function(event) {
        $("#stickyImg .images-container").replaceWith(event.product_cover_thumbnails);
        $("#sticky-price .product-prices").replaceWith(event.product_prices);
        $(" .product-variants").replaceWith(event.product_variants);
        stickyProperties();
    });
    $(document).scroll(function() {
        displayStickyBar()
    });
    $("body").on('change', '#sticky-variants select', function(e) {
        var idAttribute = $(this).attr('data-product-attribute');
        $("#add-to-cart-or-refresh #group_" + idAttribute).val($(this).val());
        $("#add-to-cart-or-refresh #group_" + idAttribute).trigger('click');
    });
    $('body').on('change ', `#sticky-variants .input-radio `, (e) => {
        var desiredTitle = e.target.getAttribute('title');
        var $radioButton = $('.input-radio[title="' + desiredTitle + '"]');
        $radioButton.prop('checked', true).trigger('click');
        $radioButton.trigger('click');
    }, );
    $("body").on('change', '#sticky-variants li input.input-color', function(e) {
        var desiredTitle = e.target.getAttribute('title');
        var $radioButton = $('.input-color[title="' + desiredTitle + '"]');
        $radioButton.prop('checked', true).trigger('click');
        $radioButton.trigger('click');
    });
    $("body").on('change', '#add-to-cart-or-refresh #quantity_wanted', function(e) {
        var quantityWanted = parseInt($('#add-to-cart-or-refresh #quantity_wanted').val());
        $('#sticky-cart #quantity_wanted').val(quantityWanted);
    });
    $("body").on('change', '#sticky-cart #quantity_wanted', function(e) {
        var quantityWanted = parseInt($('#sticky-cart #quantity_wanted').val());
        $('#add-to-cart-or-refresh #quantity_wanted').val(quantityWanted);
    });
    $("#add-to-cart-or-refresh #quantity_wanted").TouchSpin({
        verticalbuttons: true,
        verticalbuttons: !0,
        verticalupclass: "material-icons touchspin-up",
        verticaldownclass: "material-icons touchspin-down",
        buttondown_class: "btn btn-touchspin js-touchspin",
        buttonup_class: "btn btn-touchspin js-touchspin",
        min: 1,
        max: 1e6
    });
    var cart = '<i class="material-icons shopping-cart"></i> Add to cart';
    var smallCart = '<i class="material-icons shopping-cart"></i> +';
    if (window.matchMedia('(max-width: 600px)').matches) {
        $('#sticky-cart .add button').html(smallCart);
    }
    $(window).resize(function() {
        if (window.matchMedia('(max-width: 600px)').matches) {
            $('#sticky-cart .add button').html(smallCart);
        } else {
            $('#sticky-cart .add button').html(cart);
        }
    });
});

function displayStickyBar() {
    var windowWidth = $(window).width();
    var y = $(this).scrollTop();
    var topAC = $('#add-to-cart-or-refresh').offset().top;
    if (DEVICE_CHECK_SWITCH == 1) {
        if (y > topAC && !(windowWidth >= STARTING_RESOLUTION && windowWidth <= RESOLUTION_ENDING)) {
            $('#sticky-container').show();
        } else {
            $('#sticky-container').hide();
        }
    } else {
        if (y > topAC) {
            $('#sticky-container').show();
        } else {
            $('#sticky-container').hide();
        }
    }
}
// Attach the resize event listener
$(window).on('resize', displayStickyBar);

function stickyProperties() {
    $('#sticky-container').css(STICKY_POS, 0);
    $('#sticky-container').css('background-color', STICKY_BACK_COLOR);
    $('#sticky-price p').css('color', STICKY_PRO_TEXT_COLOR);
    $('#sticky-variants .add button').css('background-color', STICKY_CART_BTN_BG_COLOR);
    $('#sticky-variants .add button').css('color', STICKY_CART_BTN_TXT_COLOR);
    $('#sticky-price .current-price').css('color', STICKY_PRICE_TXT_COLOR);
    $('#sticky-variants .control-label').css('color', STICKY_ATR_LABEL_COLOR);
    $('#sticky-container').css('border-color', STICKY_BORDER_COLOR);
    $('#sticky-container').css('border-width', STICKY_BORDER_RADIUS + 'px');
    $('#sticky-container').css('height', STICKY_BAR_HEIGHT + 'px');
    if (STICKY_POS == 'bottom') {
        $('body').css('padding-bottom', 500 + 'px');
    }
}