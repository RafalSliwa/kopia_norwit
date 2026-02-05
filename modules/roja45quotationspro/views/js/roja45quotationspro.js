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

if (
  typeof $.uniform !== 'undefined' &&
  typeof $.uniform.defaults !== 'undefined'
) {
  if (typeof roja_fileDefaultHtml !== 'undefined')
    $.uniform.defaults.fileDefaultHtml = roja_fileDefaultHtml;
  if (typeof roja_fileButtonHtml !== 'undefined')
    $.uniform.defaults.fileButtonHtml = roja_fileButtonHtml;
}

$(document).ready(function () {
  if (typeof page_name !== 'undefined' && page_name == 'product') {
    //if ($.inArray(roja45quotationspro_id_product.toString(), roja45quotationspro_enabled)>-1) {
    if ($('#roja45quotationspro_buttons_block').length > 0) {
      $('body').addClass('roja45-quotable-product');

      if (parseInt(roja45quotationspro_usejs) == 1) {
        if (
          roja45quotationspro_catalog_mode &&
          $('#buy_block #roja45quotationspro_buttons_block').length > 0
        ) {
          $('#roja45quotationspro_buttons_block').appendTo('.pb-right-column');
        } else {
          $('#product .box-cart-bottom').prepend(
            $('#product #roja45quotationspro_buttons_block')
          );
        }
      }

      if (roja45_hide_add_to_cart) {
        $(roja45quotationspro_productselector_addtocart).hide();
        $(roja45quotationspro_productselector_addtocart).addClass(
          'roja45_hidden'
        );
      }
      if (roja45_hide_price) {
        $(roja45quotationspro_productselector_price).parent().hide();
        $(roja45quotationspro_productselector_price)
          .parent()
          .addClass('roja45_hidden');
      }
    }
  }

  roja45quotationspro.quotationpro_updateproductlist();

  $(document).ajaxComplete(function (data) {
    roja45quotationspro.quotationpro_updateproductlist();
  });

  $('#add_quote_button').on('click', function (e) {
    e.preventDefault();
    var quantity = $(roja45quotationspro_productselector_qty).val();
    var href = $(this).attr('href');
    var id_product = $(this).attr('data-id-product');
    var id_product_attribute = $(this).attr('data-id-product-attribute');
    var minimal_quantity = $(this).attr('data-minimal-quantity');
    href =
      href +
      '&id_product=' +
      id_product +
      '&id_product_attribute=' +
      id_product_attribute +
      '&minimal_quantity=' +
      minimal_quantity +
      '&qty=' +
      quantity;
    window.location = href;
  });

  $('.datepicker').each(function () {
    var format = $(this).attr('data-format');
    $(this).datepicker({
      prevText: '',
      nextText: '',
      dateFormat: format,
    });
  });

  $(window).bind('hashchange', function () {
    if (typeof productHasAttributes !== 'undefined' && productHasAttributes) {
      var id_product_attribute = $('#idCombination').val();
      $('#quote_block input[name=id_product_attribute]').val(
        id_product_attribute
      );
      $('#ajax_add_quote_button').attr(
        'data-id-product-attribute',
        id_product_attribute
      );
      $('#add_quote_button').attr(
        'data-id-product-attribute',
        id_product_attribute
      );

      var minimal_quantity = $('#ajax_add_quote_button').attr(
        'data-minimal-quantity'
      );
      var id_product = $('#ajax_add_quote_button').attr('data-id-product');
      var url =
        roja45quotationspro_controller +
        '&action=addToQuote&id_product=' +
        id_product +
        '&id_product_attribute=' +
        id_product_attribute +
        '&minimal_quantity=' +
        minimal_quantity;

      $('#ajax_add_quote_button').attr('href', url);
    }
  });

  $('#quote_block input[name=id_product_attribute]').val(
    $('#idCombination').val()
  );
  $('#ajax_add_quote_button').attr(
    'data-id-product-attribute',
    $('#idCombination').val()
  );
});

roja45quotationspro = {
  init: function () {
    $('#quotationspro_request_dialog')
      .off('click')
      .on('click', '.ajax-request-quotation', function (e) {
        e.preventDefault();
        roja45quotationspro.processSend($(this).closest('form').attr('action'));
      });
    $('#quotationspro_request_dialog')
      .off('keypress')
      .on('keypress', function (e) {
        if (e.which == 13) {
          roja45quotationspro.processSend($(this).find('form').attr('action'));
        }
      });

    $(
      '#quotationspro_request_dialog #firstname, #quotationspro_request_dialog #lastname'
    )
      .off('focusout')
      .on('focusout', function (e) {
        $(this).val(roja45quotationspro.toTitleCase($(this).val()));
      });
  },

  toTitleCase: function (str) {
    str = str.toLowerCase();
    return str.replace(/(?:^|\s)\w/g, function (match) {
      return match.toUpperCase();
    });
  },

  quotationpro_updateproductlist: function (ele) {
    $(
      roja45quotationspro_productlistitemselector +
        ' .roja45quotationspro.product.enabled'
    ).each(function (index, element) {
      var id_product = parseInt($(this).attr('data-id-product'));
      var id_product_attribute = parseInt(
        $(this).attr('data-id-product-attribute')
      );
      var minimal_quantity = parseInt($(this).attr('data-minimal-quantity'));
      roja45quotationspro.quotationpro_addlabel($(this), id_product);
      roja45quotationspro.quotationpro_addbutton(
        $(this),
        id_product,
        id_product_attribute,
        minimal_quantity
      );
      if (roja45_hide_add_to_cart) {
        roja45quotationspro.quotationpro_hideaddtocart($(this));
      }
      if (roja45_hide_price) {
        roja45quotationspro.quotationpro_hideprice($(this));
      }
    });
  },

  quotationpro_hideaddtocart: function (ele) {
    ele
      .closest(roja45quotationspro_productlistitemselector)
      .find(
        roja45quotationspro_productlistselector_buttons +
          ' ' +
          roja45quotationspro_productlistselector_addtocart
      )
      .hide();
  },

  quotationpro_hideprice: function (ele) {
    ele
      .closest(roja45quotationspro_productlistitemselector)
      .find(roja45quotationspro_productlistselector_price)
      .hide();
  },

  quotationpro_addlabel: function (ele, id_product) {
    if (id_product && roja45quotationspro_show_label == 1) {
      var url = ele
        .closest('.product-container')
        .find('.product_img_link')
        .attr('href');
      if (
        !ele
          .closest('.product-container')
          .find('.product-image-container .quote-label').length
      ) {
        ele
          .closest('.product-container')
          .find('.product-image-container')
          .append(
            '<a class="quote-box ' +
              roja45quotationspro_label_position +
              '" href="' +
              url +
              '"><span class="quote-label">' +
              roja45quotationspro_quote_link_text +
              '</span></a>'
          );
      }
    }
  },

  quotationpro_addbutton: function (
    ele,
    id_product,
    id_product_attribute,
    minimal_quantity
  ) {
    if (id_product) {
      if (
        ele
          .closest(roja45quotationspro_productlistitemselector)
          .find(roja45quotationspro_productlistselector_buttons).length == 1 &&
        ele
          .closest(roja45quotationspro_productlistitemselector)
          .find(roja45quotationspro_productlistselector_buttons + ' .btn-quote')
          .length == 0
      ) {
        if (roja45_quotation_useajax) {
          ele
            .closest(roja45quotationspro_productlistitemselector)
            .find(roja45quotationspro_productlistselector_buttons)
            .prepend(
              '<a href=' +
                roja45quotationspro_controller +
                '&action=addToQuote&id_product=' +
                id_product +
                '&id_product_attribute=' +
                id_product_attribute +
                '&minimal_quantity=' +
                minimal_quantity +
                '" data-id-product="' +
                id_product +
                '" data-id-product-attribute="' +
                id_product_attribute +
                '" data-minimal-quantity="' +
                minimal_quantity +
                '" class="button lnk_view btn btn-default btn-quote ajax_add_quote_button"><span>' +
                roja45quotationspro_button_addquote +
                '</span></a>'
            );
        } else {
          ele
            .closest(roja45quotationspro_productlistitemselector)
            .find(roja45quotationspro_productlistselector_buttons)
            .prepend(
              '<a href=' +
                roja45quotationspro_controller +
                '&action=addToQuote&id_product=' +
                id_product +
                '&id_product_attribute=' +
                id_product_attribute +
                '&minimal_quantity=' +
                minimal_quantity +
                '" data-id-product="' +
                id_product +
                '" data-id-product-attribute="' +
                id_product_attribute +
                '" data-minimal-quantity="' +
                minimal_quantity +
                '" class="button lnk_view btn btn-default btn-quote"><span>' +
                roja45quotationspro_button_addquote +
                '</span></a>'
            );
        }
      }
    }
  },

  toggleWaitDialog: function () {
    if (typeof toggleRoja45GlobalWaitDialog === 'function') {
      toggleRoja45GlobalWaitDialog();
    } else {
      if ($('.roja45-quotation-modal').hasClass('invisible')) {
        $('.roja45-quotation-modal').removeClass('invisible');
        $('.roja45-quotation-modal').fadeIn();
      } else {
        $('.roja45-quotation-modal').fadeOut();
        $('.roja45-quotation-modal').addClass('invisible');
      }
    }
  },

  displaySuccessMsg: function (msgs) {
    $.each(msgs, function (index, value) {
      $.growl({
        title: roja45quotationspro_success_title,
        message: value,
        duration: 3000,
        style: 'notice',
      });
    });
  },

  displayWarningMsg: function (msgs) {
    $.each(msgs, function (index, value) {
      $.growl({
        title: roja45quotationspro_warning_title,
        message: value,
        duration: 5000,
        style: 'warning',
      });
    });
  },

  displayErrorMsg: function (msgs) {
    $.each(msgs, function (index, value) {
      $.growl({
        title: roja45quotationspro_error_title,
        message: value,
        duration: 10000,
        style: 'error',
      });
    });
  },
};
