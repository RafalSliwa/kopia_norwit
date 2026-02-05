/**
 * roja45quotationsadmin.js.
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
  // Init all events
  roja45quotationspro.initialiseEvents();
  note_box = $('#note-detail-div');
  note_box.dialog({
    autoOpen: false,
    title: 'Title',
    show: 'fade',
    hide: 'fade',
  });

  $(document).on('click', '#claimRequest', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_claimrequest,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.claimRequest(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '#releaseRequest', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_releaserequest,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.releaseRequest(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '#resetCart', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_resetcart,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.resetCart(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '#createCustomerAccount', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_createcustomeraccount,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.createCustomerAccount(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '#sendCustomerQuotation', function (e) {
    e.preventDefault();
    roja45quotationspro.sendCustomerQuotation(0);
  });

  $(document).on('click', '#sendCustomerMessage', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.sendCustomerMessage(target);
  });

  $(document).on('click', '#saveQuotation', function (e) {
    roja45quotationspro.toggleModal();
  });

  $(document).on('click', '#updateTemplate', function (e) {
    e.preventDefault();
    roja45quotationspro.updateTemplate($(this));
  });

  $(document).on('click', '#addQuotationToOrder', function (e) {
    e.preventDefault();
    var target = $(e.target);
    if (roja45_quote_sent == 0) {
      $.confirm({
        text: roja45quotationspro_txt_quotationnotsent,
        title: roja45quotationspro_txt_confirm,
        confirm: function () {
          roja45quotationspro.addQuotationToOrder(target);
        },
        cancel: function () {
          // nothing to do
        },
        confirmButton: roja45quotationspro_confirmbutton,
        cancelButton: roja45quotationspro_cancelbutton,
        post: true,
        confirmButtonClass: 'btn-danger',
        cancelButtonClass: 'btn-default',
        dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
      });
    } else {
      roja45quotationspro.addQuotationToOrder(target);
    }
  });

  $(document).on('click', '.createQuote', function (e) {
    e.preventDefault();
    var target = $(this);
    $.confirm({
      text: roja45quotationspro_txt_createquote,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.createQuote(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '#deleteQuotation', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_deletequotation,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.deleteQuotation(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', 'button.saveAsTemplate', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.saveAsTemplate(target);
  });

  $(document).on('click', 'button.add-document', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.addDocument(target);
  });

  $(document).on('click', '#sendMessageForm', function (e) {
    tinyMCE.triggerSave();
  });

  $(document).on('click', '#loadMessageTemplate', function (e) {
    e.preventDefault();
    roja45quotationspro.loadMessageTemplate();
  });

  $(document).on('click', '#add_quotation_product', function (e) {
    $('#quotationspro_addproduct_modal').modal();
  });

  $(document).on('change', '#tax_country', function (e) {
    e.preventDefault();
    //roja45quotationspro.submitSetCountry($(this));
    var target = $(this).attr('data-target-states');
    roja45quotationspro.submitGetStates($(this).val(), target);
  });

  $(document).on('change', '#tax_state', function (e) {
    e.preventDefault();
    roja45quotationspro.submitSetState($(this));
  });

  $(document).on('change', '#customer_address_country', function (e) {
    e.preventDefault();
    var target = $(this).attr('data-target-states');
    roja45quotationspro.submitGetStates($(this).val(), target);
  });

  $(document).on(
    'change',
    '#quotationspro_addproduct_modal select.product_quotation_id_product_attribute',
    function (e) {
      var optionSelected = $('option:selected', this);
      var retailPrice = optionSelected.attr('data-retail-price');
      var minimalQuantity = optionSelected.attr('data-minimal-quantity');
      var stock = optionSelected.attr('data-stock');
      var row = $(this).closest('tr.product-line-row');
      row
        .find(
          'input[name="product_quotation[' +
            row.attr('data-product-id') +
            '][product_price_tax_excl]"]'
        )
        .val(retailPrice);
      row
        .find(
          'input[name="product_quotation[' +
            row.attr('data-product-id') +
            '][product_quantity]"]'
        )
        .val(minimalQuantity);

      row.find('.product_quotation_stock').val(stock);
      if (stock <= 0) {
        row.find('.out-of-stock').show();
      } else {
        row.find('.out-of-stock').hide();
      }
    }
  );

  $(document).on(
      'change',
      '#quotationspro_addproduct_modal input.product_quotation_qty',
      function (e) {
        let ele = $(this);
        let id_product = ele.closest('tr.product-line-row').attr('data-product-id');
        let id_product_attribute = ele.closest('tr.product-line-row').find('select.product_quotation_id_product_attribute').val();
        let product_quantity = parseInt(ele.closest('tr.product-line-row').find('input.product_quotation_qty').val());
        let has_volume_discount = parseInt(ele.closest('tr.product-line-row').find('input.product_quotation_has_volume_discount').val());

        if (has_volume_discount > 0) {
          $.ajax({
            url: quotationspro_link,
            type: 'POST',
            data: {
              ajax: 1,
              action: 'getPriceVolumeDiscount',
              id_roja45_quotation: id_roja45_quotation,
              id_product: id_product,
              id_product_attribute: id_product_attribute,
              product_quantity: product_quantity,
            },
            dataType: 'json',
            beforeSend: function () {
              roja45quotationspro.toggleModal();
            },
            success: function (data) {
              if (data.result) {
                //window.location = data.redirect;
                ele.closest('tr.product-line-row').find('input.product_quotation_price_tax_excl').val(data.offer_price_currency);
              } else {
                roja45quotationspro.displayErrorMsg(data.errors);
              }
            },
            complete: function (e) {
              roja45quotationspro.toggleModal();
            },
          });
        }
      }
  );

  $(document).on('change', '#quotation_answer_template', function (e) {
    e.preventDefault();
    var target = $(e.target);
    var id_roja45_quotation_answer = $('#quotation_answer_template').val();
    roja45quotationspro.cancelSendQuotationForm(target);
    roja45quotationspro.sendCustomerQuotation(id_roja45_quotation_answer);
  });

  $(document).on('change', 'select[name=page_number]', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.searchProducts(target);
  });

  $(document).on(
    'click',
    '#quotationspro_addproduct_modal .btn-search-products',
    function (e) {
      e.preventDefault();
      var target = $(e.target);
      roja45quotationspro.searchProducts(target);
    }
  );

  $(document).on('change', 'input.image-upload', function (e) {
    e.preventDefault();
    var id_roja45_quotation_product = $(this).attr(
      'data-id-roja45-quotation-product'
    );
    //  .closest('tr.product-line-row')
    // .attr('data-id-roja45-quotation-product');

    // append file input to form data
    var fileInput = $(this)
      .closest('tr.product-line-row')
      .find('input.image-upload');
    var file = fileInput[0].files[0];
    var formData = new FormData();
    formData.append('uploadImage', file);
    formData.append('ajax', 1);
    formData.append('action', 'uploadProductImage');
    formData.append('id_roja45_quotation', id_roja45_quotation);
    formData.append('id_roja45_quotation_product', id_roja45_quotation_product);

    $.ajax({
      url: quotationspro_link,
      type: 'POST',
      data: formData,
      contentType: false,
      dataType: 'json',
      cache: false,
      processData: false,
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (e) {
        roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on('click', '.btn-delete-custom-image', function (e) {
    e.preventDefault();
    var id_roja45_quotation_product = $(this)
      .closest('tr.product-line-row')
      .attr('data-id-roja45-quotation-product');

    $.ajax({
      url: quotationspro_link,
      type: 'POST',
      data: {
        ajax: 1,
        action: 'deleteProductImage',
        id_roja45_quotation: id_roja45_quotation,
        id_roja45_quotation_product: id_roja45_quotation_product,
      },
      dataType: 'json',
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (e) {
        roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on(
    'change',
    '#quotationspro_addproduct_modal .add-product-checkbox',
    function (e) {
      e.preventDefault();
      let id_product = $(this).closest('tr').attr('data-product-id');
      let id_product_attribute = $(this)
        .closest('tr')
        .find('#product_quotation_id_product_attribute')
        .val();

      if ($(this).prop('checked')) {
        let id_product_attribute = $(this)
          .closest('tr')
          .find('select.product_quotation_id_product_attribute')
          .val();
        let product_price_tax_incl = $(this)
          .closest('tr')
          .find('input.product_quotation_price_tax_incl')
          .val();
        let product_price_tax_excl = $(this)
          .closest('tr')
          .find('input.product_quotation_price_tax_excl')
          .val();
        let product_quantity = $(this)
          .closest('tr')
          .find('input.product_quotation_qty')
          .val();
        let product_discount = $(this)
          .closest('tr')
          .find('input.product_quotation_discount')
          .val();
        let product_discount_type = $(this)
          .closest('tr')
          .find('select.product_quotation_discount_type')
          .val();
        let comment = $(this)
          .closest('tr')
          .find('input.product_quotation_comment')
          .val();

        $(this)
          .closest('tr')
          .find('input.disable_after_add')
          .attr('disabled', 'disabled');
        $('#quotationspro_addproduct_form').append(
          '<input type="hidden" name="selected_product_ids[' +
            id_product +
            '][id_product]" value="' +
            id_product +
            '"/>'
        );
        if (id_product_attribute) {
          $('#quotationspro_addproduct_form').append(
            '<input type="hidden" name="selected_product_ids[' +
              id_product +
              '][id_product_attribute]" value="' +
              id_product_attribute +
              '"/>'
          );
        }
        $('#quotationspro_addproduct_form').append(
          '<input type="hidden" name="selected_product_ids[' +
            id_product +
            '][product_price_tax_incl]" value="' +
            product_price_tax_incl +
            '"/>'
        );
        $('#quotationspro_addproduct_form').append(
          '<input type="hidden" name="selected_product_ids[' +
            id_product +
            '][product_price_tax_excl]" value="' +
            product_price_tax_excl +
            '"/>'
        );
        $('#quotationspro_addproduct_form').append(
          '<input type="hidden" name="selected_product_ids[' +
            id_product +
            '][product_quantity]" value="' +
            product_quantity +
            '"/>'
        );
        $('#quotationspro_addproduct_form').append(
          '<input type="hidden" name="selected_product_ids[' +
            id_product +
            '][product_discount]" value="' +
            product_discount +
            '"/>'
        );
        $('#quotationspro_addproduct_form').append(
          '<input type="hidden" name="selected_product_ids[' +
            id_product +
            '][product_discount_type]" value="' +
            product_discount_type +
            '"/>'
        );
        $('#quotationspro_addproduct_form').append(
          '<input type="hidden" name="selected_product_ids[' +
            id_product +
            '][comment]" value="' +
            comment +
            '"/>'
        );
      } else {
        $(this)
          .closest('tr')
          .find('input.disable_after_add')
          .attr('disabled', false);
        $('#quotationspro_addproduct_form')
          .find('input[name^="selected_product_ids[' + id_product + ']"')
          .remove();
      }

      var num_checked = $('#quotationspro_addproduct_form').find(
        'input[name^="selected_product_ids"]'
      ).length;
      if (num_checked > 0) {
        $('#addCloseSelectedProducts').removeClass('disabled');
      } else if (num_checked == 0) {
        $('#addCloseSelectedProducts').addClass('disabled');
      }
    }
  );

  $(document).on(
    'change',
    'input.product_quotation_quantity,input.product_quotation_comment,input.product_quotation_price_tax_excl,input.product_quotation_price_tax_incl,select.product_quotation_id_product_attribute',
    function (e) {
      let checkbox = $(this).closest('tr').find('input.add-product-checkbox');
      if (checkbox.prop('checked')) {
        checkbox.trigger('click');
        checkbox.trigger('click');
      }
    }
  );

  $(document).on(
    'click',
    '#quotationspro_addproduct_modal .btn-add-close-selected-products',
    function (e) {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: quotationspro_link,
        async: true,
        dataType: 'json',
        data: $('#quotationspro_addproduct_form').serialize(),
        beforeSend: function () {
          roja45quotationspro.toggleModal();
        },
        success: function (data) {
          if (data.result) {
            window.location = data.redirect;
          } else {
            roja45quotationspro.displayErrorMsg(data.errors);
            roja45quotationspro.toggleModal();
          }
        },
        error: function (data) {
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected
          );
          $('#quotationspro_addproduct_modal').modal('toggle');
          roja45quotationspro.toggleModal();
        },
        complete: function () {
          $('#quotationspro_addproduct_form')
            .find('input[name^="selected_product_ids"]')
            .remove();
          $('#quotationspro_addproduct_form')
            .find('input.add-product-checkbox')
            .prop('checked', false);
        },
      });
    }
  );

  $(document).on(
    'click',
    '#quotationspro_addproduct_modal .btn-close-add-products, #quotationspro_addproduct_modal button.close',
    function (e) {
      $('#quotationspro_addproduct_form')
        .find('input[name^="selected_product_ids"]')
        .remove();
      $('#quotationspro_addproduct_form')
        .find('input.add-product-checkbox')
        .prop('checked', false);

      if ($(this).attr('data-dirty') == '1') {
        e.preventDefault();
        $(this).attr('data-dirty', '0');
        roja45quotationspro.toggleModal();
        location.reload(true);
      }
    }
  );

  $(document).on(
    'click',
    '#quotationspro_addproduct_modal .btn-reset-search',
    function (e) {
      e.preventDefault();
      $('#quotationspro_addproduct_modal input[name=multiple_search]').val('');
      $('#quotationspro_addproduct_modal select[name=product_category]').val(0);
      $('#quotationspro_addproduct_modal input[name=results_per_page]').val(10);
      $('#quotationspro_addproduct_modal select[name=page_number]').fadeOut(
        'fast',
        function () {
          $('#quotationspro_addproduct_modal input[name=page_number]').fadeIn();
        }
      );
      $('#quotationspro_addproduct_modal select[name=page_number]').val(1);
      $('#quotationspro_addproduct_modal .modal-body .results').fadeOut(
        'fast',
        function () {
          $('#quotationspro_addproduct_modal .modal-body .results').empty();
        }
      );
    }
  );

  $(document).on('click', '#submitAddProductToQuotation', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.submitAddProductToQuotation(target);
  });

  $(document).on('click', '.btn-delete-quotation-product', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_deleteproduct,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.submitDeleteProductFromQuotation(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '.btn-delete-selected-products', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_deleteproduct,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.submitDeleteSelectedProductsFromQuotation(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '.edit_product_quotation_change_link', function (e) {
    roja45quotationspro.editProductLine(e);
  });

  $(document).on(
    'change',
    '#quotationProducts tr.product-line-row .product_quotation_editable',
    function (e) {
      $(this).closest('tr').find('.btn-save-quotation-product').show();
      $(this).closest('tr').find('.hide-when-dirty').addClass('hidden-dirty');
    }
  );

  $(document).on(
    'change',
    '#quotationProducts tr.product-line-row .product_quotation_discount',
    function (e) {
      $(this).closest('tr').find('input.product_changed').val('1');
      $(this).closest('tr').find('.product_price').val('');
    }
  );
  $(document).on(
    'change',
    '#quotationProducts tr.product-line-row .product_price',
    function (e) {
      $(this).closest('tr').find('input.product_changed').val('1');
      $(this).closest('tr').find('.product_quotation_discount').val('');
    }
  );

  $(document).on(
    'click',
    '#quotationProducts tr.product-line-row .btn-save-quotation-product',
    function (e) {
      roja45quotationspro.submitUpdateProductLines($(this).closest('tr'));
    }
  );

  $(document).on('change', 'input.product_quotation_quantity', function (e) {
    e.preventDefault();
    $(this).closest('tr').find('input.product_changed').val('1');
    roja45quotationspro.getCatalogPriceRules($(this));
  });

  $(document).on('change', 'input.product_quotation_comment', function (e) {
    e.preventDefault();
    $(this).closest('tr').find('input.product_changed').val('1');
  });

  $(document).on('click', '#product_quotation_select_all', function (e) {
    if ($(this).prop('checked')) {
      $('#quotationProducts input.select-quotation-product').prop(
        'checked',
        true
      );
    } else {
      $('#quotationProducts input.select-quotation-product').prop(
        'checked',
        false
      );
    }
  });

  $(document).on('click', '.btn-save-selected-products', function (e) {
    roja45quotationspro.submitUpdateProductLines(
      $('#quotationProducts tr.product-line-row')
    );
  });

  $(document).on(
    'click',
    '.cancel_product_quotation_change_link',
    function (e) {
      e.preventDefault();
      current_product = null;
      $('.edit_product_quotation_fields').hide();
      $('.add_product_quotation_fields').show();
      var element_list = $(
        '.customized-' +
          $(this).parent().parent().find('.edit_product_id_order_detail').val()
      );
      if (!element_list.length)
        element_list = $(this).closest('.product-line-row');
      element_list.find('td .product_price_show').show();
      element_list.find('td .product_quantity_show').show();
      element_list.find('td .product_price_edit').hide();
      element_list.find('td .product_comment_edit').hide();
      element_list.find('td .product_quantity_edit').hide();
      element_list.find('.edit_product_quotation_change_link').parent().show();
      element_list.find('button.submitProductQuotationChange').hide();
      element_list.find('.cancel_product_quotation_change_link').hide();
      element_list.find('td .deposit_required_show').show();
      element_list.find('td .deposit_required_edit').hide();
    }
  );

  $(document).on('click', '#add_quotation_charge', function (e) {
    $('.quotation_action').fadeOut();
    $('.panel-charges #charges_form').slideDown();
    e.preventDefault();
  });

  $(document).on('click', '#cancel_add_charge', function (e) {
    $('#charges_form').slideUp();
    if ($('#charges_table .charge_row').length > 0) has_charge = 1;
    else has_charge = 0;
    if (has_charge) $('.charges-vouchers').show();
    $('.quotation_action').fadeIn();
    e.preventDefault();
  });

  $(document).on('click', 'button.submit-new-charge', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.submitNewCharge(target);
  });

  $(document).on('click', '.btn-delete-charge', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_deletecharge,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.submitDeleteCharge(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '.btn-save-charge', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.submitSaveCharge(target);
  });

  $(document).on('click', '.btn-add-discount', function (e) {
    e.preventDefault();
    $('.quotation_action').fadeOut();
    $('.panel-vouchers,#voucher_form').slideDown();
  });

  $(document).on('click', '#quotationspro_createorder', function (e) {
    e.preventDefault();
    roja45quotationspro.submitNewCustomerOrder();
  });

  $(document).on('click', '#quotationspro_savetemplate', function (e) {
    e.preventDefault();
    roja45quotationspro.submitSaveTemplate();
  });

  $(document).on('click', '#cancel_add_voucher', function (e) {
    $('#voucher_form').slideUp();

    if ($('#discount_table .discount_row').length > 0) has_voucher = 1;
    else has_voucher = 0;
    if (has_voucher) $('.panel-vouchers').show();
    $('.quotation_action').fadeIn();
    e.preventDefault();
  });

  $(document).on('click', 'button.submitNewVoucher', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.submitAddDiscount(target);
  });

  $(document).on('click', 'a.submitDeleteVoucher', function (e) {
    e.preventDefault();
    var target = $(e.target);
    $.confirm({
      text: roja45quotationspro_txt_deletediscount,
      title: roja45quotationspro_txt_confirm,
      confirm: function () {
        roja45quotationspro.submitDeleteDiscount(target);
      },
      cancel: function () {
        // nothing to do
      },
      confirmButton: roja45quotationspro_confirmbutton,
      cancelButton: roja45quotationspro_cancelbutton,
      post: true,
      confirmButtonClass: 'btn-danger',
      cancelButtonClass: 'btn-default',
      dialogClass: 'bootstrap modal-dialog', // Bootstrap classes for large modal
    });
  });

  $(document).on('click', '#submitQuotationNote', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.submitAddNote(target);
  });

  $(document).on('click', '.btn.btn-search-account', function (e) {
    e.preventDefault();
    var firstname = $('#customer_firstname').val();
    var lastname = $('#customer_lastname').val();
    var email = $('#customer_email').val();
    if (email) {
      var search = email;
    } else {
      var search = [firstname, lastname].filter(Boolean).join(' ');
    }

    if (search.length == 0) {
      roja45quotationspro.displayErrorMsg(
        roja45_quotationspro_error_nocustomersearchcriteria
      );
      return;
    }
    var customerController = $(this).attr('data-customer-controller');
    $.ajax({
      type: 'GET',
      url: customerController,
      async: true,
      dataType: 'json',
      data: {
        ajax: '1',
        tab: 'AdminCustomers',
        action: 'searchCustomers',
        customer_search: search,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        roja45quotationspro.toggleModal();
        if (data.found) {
          $('.quotationspro_request_dialog_overlay').addClass(
            'roja45quotationspro-darken-background'
          );
          $('.quotationspro_request_dialog_overlay').css('width', '100%');
          $('.quotationspro_request_dialog_overlay').css('height', '100%');
          $('.quotationspro_request_dialog_overlay').show();
          $(
            '#quotationspro_select_customer .quotationspro_request.modal-body'
          ).empty();
          var html = '';
          $.each(data.customers, function (index, value) {
            html += '<div class="customerCard">';
            html += '<div class="panel">';
            html +=
              '<div class="panel-heading">' +
              this.firstname +
              ' ' +
              this.lastname;
            html +=
              '<span class="pull-right">#' + this.id_customer + '</span></div>';
            html += '<span>' + this.email + '</span>';
            html +=
              '<button type="button" data-customer-id="' +
              this.id_customer +
              '" data-customer-email="' +
              this.email +
              '" data-customer-firstname="' +
              this.firstname +
              '" data-customer-lastname="' +
              this.lastname +
              '" class="btn-select-customer btn btn-primary pull-right"><i class="icon-arrow-right"></i>' +
              roja45quotationspro_txt_select +
              '</button>';
            html += '</div>';
            html += '</div>';
          });
          $('#quotationspro_select_customer .quotationspro_request.modal-body').append(html);
          $('#quotationspro_request_dialog .page-heading').html($('input[name=DIALOG_HEADING]').val());
          $('#quotationspro_select_customer').fadeIn('fast');
        } else {
          $('#quotationspro_select_customer .quotationspro_request.modal-body').empty();
          var html = '';
          html += '<div class="customerCard">';
          html += '<div class="panel">';
          html +=
            '<div class="panel-content">' +
            roja45_quotationspro_error_nocustomeraccountsfound +
            '</div>';
          html +=
            '<div class="panel-footer"><button type="button" class="btn-create-customer btn btn-primary pull-right"><i class="icon-arrow-right"></i>' +
            roja45quotationspro_txt_create_customer +
            '</button></div>';
          html += '</div>';
          html += '</div>';
          $('#quotationspro_select_customer .quotationspro_request.modal-body').append(html);
          $('#quotationspro_request_dialog .page-heading').html($('input[name=DIALOG_HEADING]').val());
          $('#quotationspro_select_customer').fadeIn('fast');
        }
      },
      error: function (data) {
        console.log(response);
        roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on('click', '.btn.btn-create-customer', function (e) {
    e.preventDefault();
    var firstname = $('#customer_firstname').val();
    var lastname = $('#customer_lastname').val();
    var email = $('#customer_email').val();

    if (firstname.length == 0) {
      roja45quotationspro.displayErrorMsg(
        roja45_quotationspro_error_nocustomername
      );
      return;
    }
    if (lastname.length == 0) {
      roja45quotationspro.displayErrorMsg(
        roja45_quotationspro_error_nocustomerlastname
      );
      return;
    }
    if (email.length == 0) {
      roja45quotationspro.displayErrorMsg(
        roja45_quotationspro_error_nocustomeremail
      );
      return;
    }
    $.ajax({
      type: 'GET',
      url: quotationspro_link,
      async: true,
      dataType: 'json',
      data: {
        ajax: '1',
        action: 'submitCreateCustomerAccount',
        id_roja45_quotation: id_roja45_quotation,
        firstname: firstname,
        lastname: lastname,
        email: email,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          $('.quotationspro_request_dialog_overlay').addClass(
            'roja45quotationspro-darken-background'
          );
          $('.quotationspro_request_dialog_overlay').css('width', '100%');
          $('.quotationspro_request_dialog_overlay').css('height', '100%');
          $('.quotationspro_request_dialog_overlay').hide();
          $('#quotationspro_select_customer').hide();
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          roja45quotationspro.toggleModal();
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_createaccount
          );
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  });

  $(document).on('click', '.btn-select-customer', function (e) {
    e.preventDefault();
    $.ajax({
      type: 'GET',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitSelectCustomer',
        id_roja45_quotation: id_roja45_quotation,
        id_customer: $(this).attr('data-customer-id'),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
          roja45quotationspro.toggleModal();
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {
        //roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on('click', '.ajax-add-address', function (e) {
    e.preventDefault();
    $('#add_customer_address').slideDown();
  });

  $(document).on('click', '.ajax-close-add-address', function (e) {
    e.preventDefault();
    $('#add_customer_address').slideUp();
  });

  $(document).on('click', '.ajax-save-customer-address', function (e) {
    $.ajax({
      type: 'GET',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitCreateCustomerAddress',
        id_roja45_quotation: id_roja45_quotation,
        address_alias: $('#add_customer_address #customer_address_alias').val(),
        address_firstname: $(
          '#add_customer_address #customer_address_firstname'
        ).val(),
        address_lastname: $(
          '#add_customer_address #customer_address_lastname'
        ).val(),
        address_line1: $('#add_customer_address #customer_address_line1').val(),
        address_line2: $('#add_customer_address #customer_address_line2').val(),
        address_city: $('#add_customer_address #customer_address_city').val(),
        address_zip: $('#add_customer_address #customer_address_zip').val(),
        address_country_id: $(
          '#add_customer_address #customer_address_country'
        ).val(),
        address_state_id: $(
          '#add_customer_address #customer_address_state'
        ).val(),
        address_telephone: $(
          '#add_customer_address #customer_address_telephone'
        ).val(),
        company: $('#add_customer_address #customer_company').val(),
        dni: $('#add_customer_address #customer_dni').val(),
        vat_number: $('#add_customer_address #customer_vat_number').val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.message);
          window.location = data.redirect;
        } else {
          roja45quotationspro.toggleModal();
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
        }
      },
      error: function (data) {
        roja45quotationspro.toggleModal();
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
      },
      complete: function () {
        //roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on('click', '.ajax-update-read-link', function (e) {
    const id_customer_message = $(this).attr('data-id-message');
    $.ajax({
      type: 'GET',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitUpdateMessageReadFlag',
        id_roja45_quotation: id_roja45_quotation,
        id_customer_message: $(this).attr('data-id-message'),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.read) {
          window.location = data.redirect;
        } else {
          $('.ajax-update-read-link-' + id_customer_message)
            .find('i.icon-remove')
            .removeClass('hidden');
          $('.ajax-update-read-link-' + id_customer_message)
            .find('i.icon-check')
            .addClass('hidden');
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on('click', '.btn.btn-set-status', function (e) {
    $.ajax({
      type: 'GET',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitSetQuotationStatus',
        id_roja45_quotation: id_roja45_quotation,
        id_status: $('#quotation_status').val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result && data.result == 'success') {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
          roja45quotationspro.toggleModal();
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {
        //roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on('click', '#sendMessageToCustomer', function (e) {
    e.preventDefault();
    roja45quotationspro.sendMessageToCustomer();
  });

  $(document).on('click', '#sendQuotationToCustomer', function (e) {
    e.preventDefault();
    roja45quotationspro.sendQuotationToCustomer();
  });

  $(document).on('click', '#cancelSendQuotationToCustomer', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.cancelSendQuotationForm(target);
  });

  $(document).on('click', '#cancelSendMessageToCustomer', function (e) {
    e.preventDefault();
    var target = $(e.target);
    roja45quotationspro.cancelSendMessageForm(target);
  });

  $(document).on('click', '#addQuotationToAccount', function (e) {
    e.preventDefault();
    $.ajax({
      type: 'GET',
      url: quotationspro_link + query,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'addQuotationToAccount',
        id_roja45_quotation: id_roja45_quotation,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result && data.result == 'success') {
          roja45quotationspro.displaySuccessMsg(roja45quotationspro_success);
          $('#quotation-pipeline .in-cart.label-danger').fadeOut(
            'fast',
            function () {
              $('#quotation-pipeline .in-cart.label-success').fadeIn();
            }
          );
        } else {
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected
          );
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected + '\n' + data.responseText
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  });

  $(document).on(
    'click',
    '#quotationspro_request_dialog .cross, #quotationspro_save_template .cross, #quotationspro_select_customer .cross, .btn.btn-secondary.btn-close, #customer_document .cross',
    function (e) {
      e.preventDefault();
      roja45quotationspro.closeDialog();
    }
  );

  $(document).on(
    'click',
    '#quotationspro_quotation_dialog .cross',
    function (e) {
      e.preventDefault();
      roja45quotationspro.cancelSendQuotationForm();
    }
  );

  $(document).on('click', '#quotationspro_message_dialog .cross', function (e) {
    e.preventDefault();
    roja45quotationspro.cancelSendMessageForm();
  });

  $('#expires').datetimepicker({
    minDate: new Date(),
    dateFormat: roja45_quotations_dateformat,
    timeFormat: roja45_quotations_timeformat,
    numberOfMonths: 2,
    showButtonPanel: false,
    showSecond: true,
  });

  $('#quote_name').focus();

  $('.product_quotation_editable').on('keydown', function (event) {
    if (event.key == 'Enter') {
      event.preventDefault();
      $(this).trigger('change');
    }
  });

  if ($('#roja45quotation_form tr.product-line-row').length > 1) {
    $('#quotationProducts').sortable({
      containerSelector: 'table',
      itemPath: '> tbody',
      itemSelector: 'tr',
      placeholder: '<tr class="placeholder"><td colspan="15"></td></tr>',
      handle: 'i.icon-arrows',
      onDragStart: function ($item, container, _super) {
        if (!container.options.drop) $item.clone().insertAfter($item);
        _super($item, container);
      },
      onDrop: function ($item, container, _super) {
        _super($item, container);
        var parameter = new Array();
        var position = new Array();
        $('#quotationProducts tr.product-line-row').each(function () {
          parameter.push($(this).attr('data-id-roja45-quotation-product'));
        });
        $(this)
          .children()
          .each(function (index) {
            position.push(index + 1);
          });

        $.ajax({
          url: quotationspro_link,
          method: 'POST',
          data: {
            ajax: 1,
            token: token,
            action: 'submitUpdateProductPosition',
            id_roja45_quotation: id_roja45_quotation,
            quotation_product_ids: parameter,
            position: position,
          },
          beforeSend: function () {
            $('#roja45quotation_form').addClass('saving');
            $('#roja45quotation_form tr').addClass('saving');
          },
          success: function (response) {
            console.log(response);
          },
          error: function (xhr, response) {
            console.log(xhr.status);
          },
          complete: function () {
            $('#roja45quotation_form').removeClass('saving');
            $('#roja45quotation_form tr').removeClass('saving');
          },
        });
      },
    });
  }
});

roja45quotationspro = {
  tinySetup: function (config) {
    if (typeof tinyMCE === 'undefined') {
      setTimeout(function () {
        roja45quotationspro.tinySetup(config);
      }, 100);
      return;
    }

    tinyMCE.init(config);
  },

  myCustomOnInit: function () {
    $('form *:input[type!=hidden]:first').focus();
  },

  initialiseEvents: function () {
    $('select#product_quotation_id_product_attribute').unbind('change');
    $('select#product_quotation_id_product_attribute').change(function () {
      $('#add_product_quotation_price_tax_incl').val(
        current_product.combinations[$(this).val()].price_tax_incl
      );
      $('#add_product_quotation_price_tax_excl').val(
        current_product.combinations[$(this).val()].price_tax_excl
      );
      roja45quotationspro.addProductRefreshTotal();
    });

    $('#cancelAddProductToQuotation')
      .off('click')
      .on('click', function () {
        $('.quotation_action .btn-group').show();
        $('#addProductToQuotationProductSearch').val('');
        $('#addProductToQuotationProductQuantity').val('');
        $('#addProductToQuotationProductPriceTaxIncl').val('');
        $('#addProductToQuotationProductPriceTaxExcl').val('');
        $('#addProductToQuotationProductAttribute').empty();
        $('#product_quotation_product_attribute_area').hide();
        $('.product-line-row .product_wholesale_price').empty();
        $('#new_quotation_product').slideUp('fast');
      });

    $('#discount_type')
      .off('change')
      .on('change', function () {
        // Percent type
        if ($(this).val() == 1) {
          $('#discount_value_field').show();
          $('#discount_currency_sign').hide();
          $('#discount_percent_symbol').show();
          $('#with_tax').show();
          $('#with_tax').val(1);
          $('#with_tax').removeAttr('disabled');
        } else if ($(this).val() == 2) {
          $('#discount_value_field').show();
          $('#discount_percent_symbol').hide();
          $('#discount_currency_sign').show();
          $('#discount_tax_rate').closest('.form-group').show();
          $('#with_tax').show();
          $('#with_tax').val(2);
          $('#with_tax').attr('disabled', 'disabled');
        } else if ($(this).val() == 3) {
          $('#discount_value_field').hide();
          $('#with_tax').hide();
        }
      });

    $('#apply_discount')
      .off('change')
      .on('change', function (e) {
        if ($(this).val() == 1) {
          $('#discount_form_select_product').hide();
        } else {
          $('#discount_form_select_product').show();
        }
      });

    $('#charge_type')
      .off('change')
      .on('change', function (e) {
        if ($(this).val() == 'shipping') {
          $('#charge-method-block').show();
          $('#charge_method').val(2);
          $('#charge-method-block').hide();
          $('#charge-name-block').hide();
          $('#carriers-block').show();
          $('#available-taxes-block').hide();
          $('#charge_value').addClass('disabled');
          $('#charges_warning').hide();
        } else if ($(this).val() == 'general') {
          $('#charge-method-block').show();
          $('#charge-name-block').show();
          $('#charge-method-block').show();
          $('#carriers-block').hide();
          $('#available-taxes-block').hide();
          $('#charge_name').val('');
          $('#charge_value').val('0.00');
          $('#charge_value').removeClass('disabled');
          $('#charges_warning').slideDown();
        } else if ($(this).val() == 'handling') {
          $('#charge-method-block').show();
          $('#charge_method').val(2);
          $('#charge-method-block').hide();
          $('#charge-name-block').show();
          $('#charge_name').val('');
          $('#carriers-block').hide();
          $('#available-taxes-block').hide();
          $('#charge_value').val('0.00');
          $('#charge_value').removeClass('disabled');
          $('#charges_warning').slideDown();
        } else {
        }
      })
      .trigger('change');

    $('#carriers')
      .off('change')
      .on('change', function (e) {
        $('#charge_handling_field').hide();
        var selected = $(this).val();
        if (selected > 0) {
          var name = $(this)
            .find('option[value=' + selected + ']')
            .attr('data-name');
          var rate = $(this)
            .find('option[value=' + selected + ']')
            .attr('data-rate');
          var is_module = $(this)
            .find('option[value=' + selected + ']')
            .attr('data-is-module');
          var is_free = $(this)
            .find('option[value=' + selected + ']')
            .attr('data-is-free');
          $('#charge_name').val(name);
          if (is_free == 1) {
            $('#charge_value').val(0.0);
          } else {
            $.ajax({
              type: 'GET',
              url: quotationspro_link,
              cache: false,
              dataType: 'json',
              data: {
                ajax: 1,
                token: token,
                action: 'submitGetCarrierCharge',
                id_roja45_quotation: id_roja45_quotation,
                id_carrier: $(this).val(),
              },
              beforeSend: function () {
                roja45quotationspro.toggleModal();
              },
              success: function (data) {
                if (data.result) {
                  $('#handling_warning').hide();
                  if (data.shipping_handling) {
                    $('#charge_handling').val(data.shipping_handling);
                    $('#charge_handling_field').show();
                  }
                  if (data.include_handling && data.shipping_handling) {
                    $('#handling_warning').show();
                  }
                  $('#charge_value').val(data.shipping_cost);
                } else {
                  $.each(data.errors, function (index, value) {
                    roja45quotationspro.displayErrorMsg(value);
                  });
                }
              },
              error: function (data) {
                roja45quotationspro.displayErrorMsg(
                  roja45_quotationspro_error_unexpected
                );
                roja45quotationspro.displayErrorMsg(data.responseText);
              },
              complete: function () {
                roja45quotationspro.toggleModal();
              },
            });
          }
        } else {
          $('#charge_name').val('');
          $('#charge_value').val('');
        }
      });

    $('#charge_type').val('shipping').change();

    $('#charge_method')
      .off('change')
      .on('change', function () {
        // Percent type
        if ($(this).val() == 1) {
          $('charge_value_field').show();
          $('#charge_currency_sign').hide();
          $('#charge_value_help').hide();
          $('#charge_percent_symbol').show();
        }
        // Amount type
        else if ($(this).val() == 2) {
          $('#charge_value_field').show();
          $('#charge_percent_symbol').hide();
          $('#charge_value_help').show();
          $('#charge_currency_sign').show();
        }
      })
      .trigger('change');

    $('.quotation-note-link').each(function (key, value) {
      $(this)
        .off('click')
        .on('click', function (e) {
          e.preventDefault();
          var html = $(this).parent().find('.note-detail-div');
          note_box.html(html.html());
          note_box.dialog('option', 'title', html.data('received'));

          setTimeout(function () {
            note_box.dialog('open');
          }, 200);
        });
    });

    $('.delete-quotation-note-link').each(function (key, value) {
      $(this)
        .off('click')
        .on('click', function (e) {
          e.preventDefault();
          var element = $(this);
          $.ajax({
            type: 'POST',
            url: quotationspro_link,
            cache: false,
            dataType: 'json',
            data: {
              ajax: 1,
              token: token,
              action: 'deleteQuotationNote',
              id_roja45_quotation: id_roja45_quotation,
              id_roja45_quotation_note: $(this).attr(
                'data-id-roja45-quotation-note'
              ),
            },
            beforeSend: function () {
              roja45quotationspro.toggleModal();
            },
            success: function (data) {
              if (data.result) {
                roja45quotationspro.displaySuccessMsg(data.response);
                window.location = data.redirect;
              } else {
                $.each(data.errors, function (index, value) {
                  roja45quotationspro.displayErrorMsg(value);
                });
              }
            },
            error: function (data) {
              roja45quotationspro.displayErrorMsg(
                roja45_quotationspro_error_unexpected
              );
            },
            complete: function () {
              //roja45quotationspro.toggleModal();
            },
          });
        });
    });

    $('.delete-customer-message').each(function (key, value) {
      $(this)
        .off('click')
        .on('click', function (e) {
          e.preventDefault();
          alert('Delete not implemented');
        });
    });

    roja45quotationspro.initProductLineEvents();
  },

  initProductLineEvents: function () {
    $('#addProductToQuotationProductSearch')
      .autocomplete(quotationspro_link, {
        minChars: 3,
        max: 10,
        width: 500,
        selectFirst: false,
        scroll: false,
        dataType: 'json',
        highlightItem: true,
        formatItem: function (data, i, max, value, term) {
          var title =
            data.name + ' - ' + data.reference + ' - ' + data.formatted_price;
          if (data.supplier) {
            title = title + ' - ' + data.supplier;
          }
          return title;
        },
        parse: function (data) {
          var products = new Array();
          if (typeof data.products != 'undefined')
            for (var i = 0; i < data.products.length; i++)
              products[i] = {
                data: data.products[i],
                value: data.products[i].name,
              };
          return products;
        },
        extraParams: {
          ajax: true,
          ajax: true,
          action: 'searchProducts',
          id_lang: id_lang,
          id_shop: id_shop,
          id_roja45_quotation: id_roja45_quotation,
          id_currency: id_currency,
          product_search: function () {
            return $('#addProductToQuotationProductSearch').val();
          },
        },
      })
      .result(function (event, data, formatted) {
        if (!data) {
          $('#new_quotation_product input, #new_quotation_product select').each(
            function () {
              if ($(this).attr('id') != 'addProductToQuotationProductSearch')
                $(
                  '#new_quotation_product input, #new_quotation_product select, #new_quotation_product button'
                ).attr('disabled', true);
            }
          );
        } else {
          var ele = $('#addProductToQuotationProductSearch').closest(
            'tr.product-line-row'
          );
          $(
            '#new_quotation_product input, #new_quotation_product select, #new_quotation_product button'
          ).removeAttr('disabled');
          // Keep product variable
          current_product = data;
          $('#addProductToQuotationProductSearch').val(data.name);
          ele.find('.product_quotation_id').val(data.id_product);
          ele.find('.product_quotation_quantity').val(1);

          var qty_text = $('#addProductToQuotationProductSearch')
            .closest('#new_quotation_product')
            .find('th.qty-header')
            .text();
          qty_text = qty_text + ' (' + data.quantity + ')';
          $('#addProductToQuotationProductSearch')
            .closest('#new_quotation_product')
            .find('th.qty-header')
            .text(qty_text);
          ele.find('.product_quotation_quantity').val(1);

          ele
            .find('.product_quotation_price_tax_incl')
            .val(data.price_tax_incl);
          ele
            .find('.product_quotation_price_tax_excl')
            .val(data.price_tax_excl);
          ele
            .find('.product_quotation_product_wholesale_price')
            .val(this.wholesale_price_formatted);
          roja45quotationspro.editProductRefreshTotal(
            $('#submitAddProductToQuotation')
          );

          if (current_product.combinations.length !== 0) {
            // Reset combinations list
            $('#addProductToQuotationProductAttribute').html('');
            var defaultAttribute = 0;
            $.each(current_product.combinations, function () {
              $('#addProductToQuotationProductAttribute').append(
                '<option value="' +
                  this.id_product_attribute +
                  '" data-qty-in-stock="' +
                  this.qty_in_stock +
                  '" data-price-excl="' +
                  this.price_tax_excl +
                  '"  data-price-excl-formatted="' +
                  this.price_tax_excl_formatted +
                  '" data-price-incl="' +
                  this.price_tax_incl +
                  '"  data-price-incl-formatted="' +
                  this.price_tax_incl_formatted +
                  '" data-wholesale-price="' +
                  this.wholesale_price +
                  '"' +
                  '" data-wholesale-price-formatted="' +
                  this.wholesale_price_formatted +
                  '"' +
                  (this.default_on == 1 ? ' selected="selected"' : '') +
                  '>' +
                  this.attributes +
                  '</option>'
              );
              if (this.default_on == 1) {
                defaultAttribute = this.id_product_attribute;
                ele
                  .find('.product_quotation_price_tax_incl')
                  .val(this.price_tax_incl);
                ele
                  .find('.product_quotation_price_tax_excl')
                  .val(this.price_tax_excl);
                ele
                  .find('.product_quotation_product_wholesale_price')
                  .val(this.wholesale_price_formatted);
              }
            });
            $('#product_quotation_product_attribute_area').show();
          } else {
            $('addProductToQuotationProductAttribute').html('');
            $('#product_quotation_product_attribute_area').hide();
          }
        }
      });
  },

  closeDialog: function () {
    $('.quotationspro_request_dialog_overlay').removeClass(
      'roja45quotationspro-darken-background'
    );
    $('.quotationspro_request_dialog_overlay').hide();
    $('#quotationspro_request_dialog').fadeOut('fast');
    $('#quotationspro_request_dialog').off('click');
    $('#quotationspro_request_dialog').off('keypress');

    $('#quotationspro_save_template').fadeOut('fast');
    $('#quotationspro_save_template').off('click');
    $('#quotationspro_save_template').off('keypress');

    $('#quotationspro_select_customer').fadeOut('fast');
    $('#quotationspro_select_customer').off('click');
    $('#quotationspro_select_customer').off('keypress');

    $('#quotationspro_message_dialog').fadeOut('fast');
    $('#quotationspro_message_dialog').off('click');
    $('#quotationspro_message_dialog').off('keypress');

    $('#quotationspro_quotation_dialog').fadeOut('fast');
    $('#quotationspro_quotation_dialog').off('click');
    $('#quotationspro_quotation_dialog').off('keypress');

    $('#quotationspro_add_document').fadeOut('fast');
    $('#quotationspro_add_document').off('click');
    $('#quotationspro_add_document').off('keypress');
  },

  sendCustomerQuotation: function (id_roja45_quotation_answer) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        action: 'loadCustomerQuotation',
        ajax: 1,
        token: token,
        id_roja45_quotation: id_roja45_quotation,
        id_roja45_quotation_answer: id_roja45_quotation_answer,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          $('#quotationspro_quotation_dialog .modal-body')
            .empty()
            .append(data.view);
          if (data.can_edit) {
            roja45quotationspro.tinySetup({
              selector: '#final-quotation-response',
              autofocus: false,
              plugins:
                'colorpicker link image table media advlist code table autoresize',
              browser_spellcheck: true,
              toolbar1:
                'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,bullist,numlist,table,image,media,fontselect,formatselect',
              toolbar2: '',
              //content_style : (lang_is_rtl === '1' ? "body {direction:rtl;}" : ""),
              content_style: '',
              skin: 'prestashop',
              menubar: false,
              statusbar: false,
              relative_urls: false,
              convert_urls: false,
              entity_encoding: 'raw',
              extended_valid_elements:
                'em[class|name|id],@[role|data-*|aria-*]',
              valid_children: '+*[*]',
              valid_elements: '*[*]',
              init_instance_callback: 'changeToMaterial',
              rel_list: [{ title: 'nofollow', value: 'nofollow' }],
              setup: function (editor) {
                editor.on('init', function (args) {
                  editor.execCommand('fontName', false, 'Arial');
                  editor.execCommand('fontSize', false, '2');
                });
              },
              init_instance_callback: function (editor) {
                tinyMCE
                  .get('final-quotation-response')
                  .setContent(data.content);
                //$('#quotationspro_quotation_dialog input[name=message_subject]').val(data.message_subject);
                //$('#quotationspro_quotation_dialog input[name=message_template]').val('roja45_customer_quote');
                $('#quotationspro_quotation_dialog').toggle();
              },
            });
          } else {
            $('#loaded-quotation-answer').empty().append(data.content);
            $(
              '#quotationspro_quotation_dialog #final-quotation-response'
            ).hide();
            $(
              '#quotationspro_quotation_dialog .quotation_email_subject'
            ).hide();
            $(
              '#quotationspro_quotation_dialog input[name=message_template]'
            ).val('roja45_customer_quote');
            $('#quotationspro_quotation_dialog').toggle();
          }
        } else {
          if (data.errors) {
            $.each(data.errors, function (index, value) {
              roja45quotationspro.displayErrorMsg(value);
            });
          }
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          "Unable to load answer.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  },

  sendCustomerMessage: function () {
    $('#quotationspro_message_dialog').toggle();
  },

  loadMessageTemplate: function () {
    roja45quotationspro.toggleModal();
    var template = $('#select_quotation_answer').val();
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'loadMessageTemplate',
        viewroja45_quotation: 1,
        id_roja45_quotation: id_roja45_quotation,
        template: template,
        id_roja45_quotation_answer: $('#select_quotation_answer')
          .find(':selected')
          .data('id-answer'),
        type: $('#select_quotation_answer').find(':selected').data('type'),
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.tinySetup({
            selector: '#mce_customer_message',
            autofocus: false,
            plugins:
              'colorpicker link image table media advlist code table autoresize',
            browser_spellcheck: true,
            toolbar1:
              'code,colorpicker,bold,italic,underline,strikethrough,blockquote,link,bullist,numlist,table,image,media,fontselect,formatselect',
            toolbar2: '',
            //content_style : (lang_is_rtl === '1' ? "body {direction:rtl;}" : ""),
            content_style: '',
            skin: 'prestashop',
            menubar: false,
            statusbar: false,
            relative_urls: false,
            convert_urls: false,
            entity_encoding: 'raw',
            extended_valid_elements: 'em[class|name|id],@[role|data-*|aria-*]',
            valid_children: '+*[*]',
            valid_elements: '*[*]',
            init_instance_callback: 'changeToMaterial',
            rel_list: [{ title: 'nofollow', value: 'nofollow' }],
            setup: function (editor) {
              editor.on('init', function (args) {
                editor.execCommand('fontName', false, 'Arial');
                editor.execCommand('fontSize', false, '2');
              });
            },
            init_instance_callback: function (editor) {
              tinyMCE.get('mce_customer_message').setContent(data.content);
              $('#sendMessageToCustomer').addClass('message');
              $('#sendMessageForm input[name=message_subject]').val(
                data.message_subject
              );
              $('#sendMessageForm input[name=message_template]').val(template);
              roja45quotationspro.toggleModal();
            },
          });
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          "Unable to load answer.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  sendMessageToCustomer: function () {
    tinyMCE.triggerSave();
    var serializedForm = $('#sendMessageForm').serialize();
    var query =
      '&ajax=1&token=' +
      token +
      '&action=submitSendMessageForm&id_roja45_quotation=' +
      id_roja45_quotation;
    $.ajax({
      type: 'POST',
      url: quotationspro_link + query,
      cache: false,
      dataType: 'json',
      data: serializedForm,
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          if (data.redirect) {
            window.location = data.redirect;
          }
          roja45quotationspro.displaySuccessMsg(roja45quotationspro_success);
          $('#addQuotationToCart').fadeIn();
          $('.quotationOptionsDropdown').fadeIn();
          $('#quotation-pipeline .quote-sent.label-danger').fadeOut(
            'fast',
            function () {
              $('#quotation-pipeline .quote-sent.label-success').fadeIn();
            }
          );
          var editor = tinyMCE.get('mce_customer_message');
          if (editor) {
            editor.remove();
          }

          $('#current_status').fadeOut(400, function () {
            $('#current_status').text(data.status.status);
            $('#current_status').css('background-color', data.status.color);
            $('#current_status').fadeIn();
          });
          $('#quotationspro_buttons').fadeOut(400, function () {
            $('#quotationspro_buttons').empty();
            $('#quotationspro_buttons').append(data.buttons);
            $('#quotationspro_buttons').fadeIn();
          });
          $('#sendMessageForm input[name=message_subject]').val('');
          $('#mce_customer_message').val('');
          $('#quotationspro_message_dialog').toggle();
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
        }
        roja45quotationspro.toggleModal();
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected + '\n' + data.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  sendQuotationToCustomer: function () {
    tinyMCE.triggerSave();
    var serializedForm = $('#sendQuotationForm').serialize();
    var query =
      '&ajax=1&token=' +
      token +
      '&action=submitSendQuotationForm&id_roja45_quotation=' +
      id_roja45_quotation;
    $.ajax({
      type: 'POST',
      url: quotationspro_link + query,
      cache: false,
      dataType: 'json',
      data: serializedForm,
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.message);
          if (data.redirect) {
            window.location = data.redirect;
          }
          $('#addQuotationToCart').fadeIn();
          $('.quotationOptionsDropdown').fadeIn();
          $('#quotation-pipeline .quote-sent.label-danger').fadeOut(
            'fast',
            function () {
              $('#quotation-pipeline .quote-sent.label-success').fadeIn();
            }
          );
          var editor = tinyMCE.get('final-quotation-response');
          if (typeof editor !== 'undefined') {
            editor.remove();
          }

          $('#sendMessageToCustomer').removeClass('quote');
          $('#current_status').fadeOut(400, function () {
            $('#current_status').text(data.status.status);
            $('#current_status').css('background-color', data.status.color);
            $('#current_status').fadeIn();
          });
          $('#quotationspro_buttons').fadeOut(400, function () {
            $('#quotationspro_buttons').empty();
            $('#quotationspro_buttons').append(data.buttons);
            $('#quotationspro_buttons').fadeIn();
          });
          $('#sendMessageForm input[name=message_subject]').val('');
          $('#quotationspro_quotation_dialog').toggle();
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected + '\n' + data.responseText
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  },

  getCatalogPriceRules: function (target) {
    let id_product = target.closest('tr').attr('data-product-id');
    let id_product_attribute = 0;
    let qty = target.val();
    if (
      target.closest('tr').find('#product_quotation_id_product_attribute')
        .length > 0
    ) {
      id_product_attribute = target
        .closest('tr')
        .find('#product_quotation_id_product_attribute')
        .val();
    }
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      async: true,
      dataType: 'json',
      data: {
        ajax: '1',
        action: 'getCatalogPriceRules',
        id_currency: id_currency,
        id_roja45_quotation: id_roja45_quotation,
        id_product: id_product,
        id_product_attribute: id_product_attribute,
        qty: qty,
      },
      beforeSend: function () {
        //roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          target.closest('tr').find('.product_price').val(data.price);
          target
            .closest('tr')
            .find('.product_price')
            .closest('td')
            .find('.bulk-discount-indicator')
            .show();
        } else {
          target
            .closest('tr')
            .find('.product_price')
            .closest('td')
            .find('.bulk-discount-indicator')
            .hide();
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        //$('#quotationspro_addproduct_modal').modal('toggle');
      },
      complete: function () {
        // roja45quotationspro.toggleModal();
      },
    });
  },

  searchProducts: function (target) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      async: true,
      dataType: 'json',
      data: {
        ajax: '1',
        action: 'getProducts',
        id_currency: id_currency,
        id_roja45_quotation: id_roja45_quotation,
        multiple_search: $(
          '#quotationspro_addproduct_modal input[name=multiple_search]'
        ).val(),
        product_category: $(
          '#quotationspro_addproduct_modal select[name=product_category]'
        ).val(),
        results_per_page: $(
          '#quotationspro_addproduct_modal input[name=results_per_page]'
        ).val(),
        page_number: $(
          '#quotationspro_addproduct_modal select[name=page_number]'
        ).val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          $('#quotationspro_addproduct_modal .modal-body .results')
            .empty()
            .append(data.view)
            .fadeIn();
          if (data.pages > 1) {
            $(
              '#quotationspro_addproduct_modal select[name=page_number]'
            ).empty();
            for (i = 1; i <= data.pages; i++) {
              $(
                '#quotationspro_addproduct_modal select[name=page_number]'
              ).append('<option value=' + i + '>' + i + '</option>');
            }

            $(
              '#quotationspro_addproduct_modal input[name=page_number]'
            ).fadeOut('fast', function () {
              $(
                '#quotationspro_addproduct_modal select[name=page_number]'
              ).fadeIn();
            });
          } else {
            $(
              '#quotationspro_addproduct_modal select[name=page_number]'
            ).fadeOut('fast', function () {
              $(
                '#quotationspro_addproduct_modal input[name=page_number]'
              ).fadeIn();
            });
          }

          $('#quotationspro_addproduct_modal select[name=page_number]').val(
            data.page_number
          );

          $(
            '#quotationspro_addproduct_form table tbody tr.product-line-row'
          ).each(function () {
            let id_product = $(this).attr('data-product-id');
            let selected = $('#quotationspro_addproduct_form').find(
              'input[name^="selected_product_ids[' + id_product + ']"]'
            ).length;
            if (selected > 0) {
              $(this).find('input[type=checkbox]').prop('checked', true);
            }
          });

          $('#new_quotation_product .product_quotation_id_product_attribute').select2({
            dropdownParent: $('#quotationspro_addproduct_modal')
          });
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          $('#quotationspro_addproduct_modal').modal('toggle');
        }
      },
      error: function (data) {
        console.log(data.responseText)
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        $('#quotationspro_addproduct_modal').modal('toggle');
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  },

  cancelSendQuotationForm: function (target) {
    var editor = tinyMCE.get('final-quotation-response');
    $('#quotationspro_quotation_dialog').toggle();
    if (editor) {
      editor.remove();
    }
    $('#sendQuotationForm input[name=message_subject]').val('');
  },

  cancelSendMessageForm: function (target) {
    $('#quotationspro_message_dialog').toggle();
    var editor = tinyMCE.get('mce_customer_message');
    if (editor) {
      editor.remove();
    }

    $('#sendMessageForm input[name=message_subject]').val('');
    $('#mce_customer_message').val('');
  },

  submitSetCurrency: function (target) {
    var id_roja45_quotation = parseInt(
      $('input[name=id_roja45_quotation]').val()
    );
    if (id_roja45_quotation) {
      $.ajax({
        type: 'POST',
        url: quotationspro_link,
        cache: false,
        dataType: 'json',
        data: {
          ajax: 1,
          token: token,
          action: 'setCurrency',
          id_roja45_quotation: id_roja45_quotation,
          id_currency: target.val(),
        },
        beforeSend: function () {
          roja45quotationspro.toggleModal();
        },
        success: function (data) {
          if (data.result) {
            roja45quotationspro.displaySuccessMsg(roja45quotationspro_success);
            $('#quotation_panel').empty();
            $('#quotation_panel').append(data.view);
            roja45quotationspro.initialiseEvents();
          } else {
            $.each(data.errors, function (index, value) {
              roja45quotationspro.displayErrorMsg(value);
            });
          }
        },
        error: function (data) {
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected + '\n' + data.responseText
          );
        },
        complete: function () {
          roja45quotationspro.toggleModal();
        },
      });
    }
  },

  submitGetStates: function (id_country, target) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        action: 'getStates',
        id_country: id_country,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          if (data.states.length > 0) {
            $('#' + target)
              .closest('.form-group')
              .show();
            $('#' + target).empty();
            $.each(data.states, function (key, value) {
              $('#' + target).append(
                $('<option></option>')
                  .attr('value', value.id_state)
                  .text(value.name)
              );
            });
          } else {
            $('#' + target)
              .closest('.form-group')
              .hide();
            $('#' + target).val(0);
          }
        } else {
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected
          );
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected + '\n' + data.responseText
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  },

  submitSetCountry: function (target) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        action: 'setCountry',
        id_roja45_quotation: id_roja45_quotation,
        id_country: target.val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected
          );
          roja45quotationspro.toggleModal();
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected + '\n' + data.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  submitSetState: function (target) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'setState',
        id_roja45_quotation: id_roja45_quotation,
        id_state: target.val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(roja45quotationspro_success);
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected + '\n' + data.responseText
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  },

  submitSetLanguage: function (target) {
    $.ajax({
      type: 'GET',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'setLanguage',
        id_roja45_quotation: id_roja45_quotation,
        id_lang: target.val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          if (data.reload) {
            window.location = data.reload;
          }
          roja45quotationspro.displaySuccessMsg(roja45quotationspro_success);
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
        }
        roja45quotationspro.initialiseEvents();
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  },

  submitAddNote: function (target) {
    if ($('#customer_note #noteContent').val() <= 0) {
      roja45quotationspro.displayErrorMsg(txt_add_note_no_note_content);
      return false;
    }
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitQuotationNote',
        id_roja45_quotation: id_roja45_quotation,
        note: $('#customer_note #noteContent').val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
      },
      complete: function () {
        //roja45quotationspro.toggleModal();
      },
    });
  },

  submitAddDiscount: function (target) {
    if ($('#voucher_form #discount_name').val() <= 0) {
      roja45quotationspro.displayErrorMsg(txt_add_discount_no_discount_name);
      return false;
    }

    if ($('#voucher_form #discount_value').val() <= 0) {
      roja45quotationspro.displayErrorMsg(txt_add_discount_no_discount_value);
      return false;
    }

    var discount_name = target
      .closest('#voucher_form')
      .find('#discount_name')
      .val();

    var discount_type = target
      .closest('#voucher_form')
      .find('#discount_type')
      .val();

    var discount_value = target
      .closest('#voucher_form')
      .find('#discount_value')
      .val();

    var discount_tax_group = target
      .closest('#voucher_form')
      .find('#discount_tax_rate')
      .val();

    var discount_tax = target
      .closest('#voucher_form')
      .find('#discount_tax')
      .val();

    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitAddDiscount',
        id_roja45_quotation: id_roja45_quotation,
        discount_name: discount_name,
        discount_type: discount_type,
        discount_value: discount_value,
        discount_tax: discount_tax,
        discount_tax_group: discount_tax_group,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {
        // roja45quotationspro.toggleModal();
      },
    });
  },

  submitDeleteDiscount: function (target) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitDeleteVoucher',
        id_roja45_quotation: id_roja45_quotation,
        id_roja45_quotation_charge: target.attr(
          'data-id-roja45-quotation-charge'
        ),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
    return false;
  },

  /*
    editProductLine : function(e) {
        e.preventDefault();
        var target = $(e.target)
        var id_roja45_quotation_product = target.closest('tr.product-line-row').find('input.product_quotation_id').val()
        $.ajax({
            type: 'POST',
            url: quotationspro_link,
            cache: false,
            dataType: 'json',
            data : {
                'ajax': 1,
                'token': token,
                'action': 'loadQuotationProduct',
                'id_roja45_quotation': id_roja45_quotation,
                'id_roja45_quotation_product':id_roja45_quotation_product
            },
            beforeSend : function() {
                roja45quotationspro.toggleModal();
            },
            success : function(data)
            {
                if (data.result && data.result=='success') {
                    $('.quotation_action').fadeOut();
                    $('.add_product_quotation_fields').hide();
                    $('.edit_product_quotation_fields').show();
                    $('.cancel_product_quotation_change_link:visible').trigger('click');
                    roja45quotationspro.closeAddProduct();
                    current_product = data.product;
                    var element_list = $('.customized-' + target.closest('tr.product-line-row').find('.product_quotation_id').val());
                    if (!element_list.length)
                        element_list = target.closest('.product-line-row');
                    var current_total = element_list.find('td.total_product').html();
                    element_list.find('td .product_price_show').hide();
                    element_list.find('td .product_quantity_show').hide();
                    element_list.find('td .product_comment_edit').show();
                    element_list.find('td .product_price_edit').show();
                    element_list.find('td .product_quantity_edit').show();
                    element_list.find('td .deposit_required_show').hide();
                    element_list.find('td .deposit_required_edit').show();
                    element_list.find('td.cancelCheck').hide();
                    element_list.find('td.cancelQuantity').hide();
                    $('td.quotation_action').attr('colspan', 3);
                    $('th.edit_product_quotation_fields').show();
                    element_list.find('.edit_product_quotation_change_link').parent().hide();
                    element_list.find('button.submitProductQuotationChange').focus().show();
                    element_list.find('.cancel_product_quotation_change_link').show();
                    $('.quotation_action').fadeIn();

                } else if(data.result && data.result=='error') {
                    $('.add_product_quotation_fields, .quotation_action').show();
                    $('.edit_product_quotation_fields').hide();
                    roja45quotationspro.displayErrorMsg([data.error]);
                }
            },
            error: function(data) {
                roja45quotationspro.displayErrorMsg(roja45_quotationspro_error_unexpected);
            },
            complete: function() {
                roja45quotationspro.toggleModal();
            }
        });
    },

     */
  submitUpdateProductLines: function (rows) {
    var data = [];
    $.each(rows, function (index, row) {
      let target = $(row);
      var id_roja45_quotation_product = target
        .closest('tr.product-line-row')
        .find('input.product_quotation_id')
        .val();
      var product_changed = target
        .closest('tr.product-line-row')
        .find('input.product_changed')
        .val();
      var wholesale_price = target
        .closest('tr.product-line-row')
        .find('td.wholesale_price')
        .attr('data-wholesale-price');
      var list_price = target
        .closest('tr.product-line-row')
        .find('td.column-unitprice')
        .attr('data-list-price');
      var product_price = target
        .closest('tr.product-line-row')
        .find('td .product_price')
        .val();
      var product_comment = target
        .closest('tr.product-line-row')
        .find('td .product_comment')
        .val();
      var product_quotation_deposit_amount = target
        .closest('tr.product-line-row')
        .find('td .product_quotation_deposit_amount');
      if (product_quotation_deposit_amount.length == 0) {
        product_quotation_deposit_amount = 100;
      } else {
        product_quotation_deposit_amount =
          product_quotation_deposit_amount.val();
      }
      var product_quotation_discount = target
        .closest('tr.product-line-row')
        .find('td .product_quotation_discount')
        .val();
      var product_quotation_discount_type = target
        .closest('tr.product-line-row')
        .find('td .product_quotation_discount_type')
        .val();
      var product_quotation_customization_cost = target
        .closest('tr.product-line-row')
        .find('td .product_quotation_customization_cost')
        .val();
      var product_quotation_customization_cost_type = target
        .closest('tr.product-line-row')
        .find('td .product_customization_cost_type')
        .val();

      var product_quotation_quantity = target
        .closest('tr.product-line-row')
        .find('td .product_quotation_quantity')
        .val();

      var product_price_subtotal = target
        .closest('tr.product-line-row')
        .find('td.total_product')
        .attr('data-subtotal-exc');

      var product_price_subtotal_excl = target
        .closest('tr.product-line-row')
        .find('td .product_price_subtotal_excl')
        .val();

      if (product_quotation_quantity <= 0) {
        roja45quotationspro.displayErrorMsg(
          txt_add_product_no_product_quantity
        );
        return false;
      }
      if (
        target.closest('tr.product-line-row').find('td .product_price').val() <
        0
      ) {
        roja45quotationspro.displayErrorMsg(txt_add_product_no_product_price);
        return false;
      }
      data[index] = {
        id_roja45_quotation_product: id_roja45_quotation_product,
        wholesale_price: wholesale_price,
        list_price: list_price,
        product_changed: product_changed,
        product_price: product_price,
        product_comment: product_comment,
        product_quotation_deposit_amount: product_quotation_deposit_amount,
        product_quotation_quantity: product_quotation_quantity,
        product_discount: product_quotation_discount,
        product_quotation_discount_type: product_quotation_discount_type,
        product_quotation_customization_cost:
          product_quotation_customization_cost,
        product_quotation_customization_cost_type:
          product_quotation_customization_cost_type,
        product_quotation_customization_cost,
        product_price_subtotal: product_price_subtotal,
        product_price_subtotal_excl: product_price_subtotal_excl,
      };
      target.closest('tr.product-line-row').addClass('saving');
    });

    if (
      $('#quotationProducts tr.saving').length > 0 &&
      !$('#roja45quotation_form').hasClass('saving')
    ) {
      $('.disabled-while-saving').prop('disabled', function (i, v) {
        return !v;
      });
      $('#roja45quotation_form .badge.saving-indicator').show();
      $('#roja45quotation_form').addClass('saving');
    }
    roja45quotationspro
      .submitPostProductData(data)
      .then((data) => {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.response);
          $('#totals_panel').fadeOut('fast', function () {
            $(this).empty().append(data.totals_html).fadeIn();
          });
          $.each(data.products, function (index, data) {
            let target = $(
              '#quotationProducts tr[data-id-roja45-quotation-product="' +
                data.id_roja45_quotation_product +
                '"]'
            );
            target
              .find('td.total_product input.product_price_subtotal_excl')
              .val(data.total_to_pay_exc);

            target
              .find('td.total_product')
              .attr('data-subtotal-exc', data.total_to_pay_exc);
            target
              .find('td.column-quoteprice input.product_price')
              .val(data.product_price_currency);

            target
              .find('td.productDiscount input.product_quotation_discount')
              .val('');
            if (data.product_discount > 0) {
              target
                .find('td.productDiscount input.product_quotation_discount')
                .val(data.product_discount);
            }

            target.find('td.total_product_tax').html(data.total_tax_formatted);
            target.find('td.total_product_profit').html(data.profit_formatted);
            target.find('td.hidden-dirty').removeClass('hidden-dirty');
            target.removeClass('saving');
            target.find('.btn-save-quotation-product').hide();
          });
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          target.removeClass('saving');
        }
      })
      .catch((error) => {
        $('#quotationProducts tr.saving').removeClass('saving');
        console.log(error.responseText);
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
      })
      .finally(() => {
        if ($('#quotationProducts tr.saving').length == 0) {
          $('.disabled-while-saving').prop('disabled', function (i, v) {
            return !v;
          });
          $('#roja45quotation_form .badge.saving-indicator').hide();
          $('#roja45quotation_form').removeClass('saving');
        }
      });
  },

  submitPostProductData: function (products) {
    return new Promise((resolve, reject) => {
      $.ajax({
        type: 'POST',
        url: quotationspro_link,
        cache: false,
        dataType: 'json',
        data: {
          ajax: 1,
          token: token,
          action: 'updateQuotationProducts',
          id_roja45_quotation: id_roja45_quotation,
          products: products,
        },
        success: function (data) {
          resolve(data);
        },
        error: function (error) {
          reject(error);
        },
      });
    });
  },

  submitAddProductToQuotation: function (target) {
    if ($('input#addProductToQuotationProductSearch').val() == 0) {
      roja45quotationspro.displayErrorMsg(txt_add_product_no_product);
      return false;
    }

    if ($('input#add_product_quotation_product_quantity').val() == 0) {
      roja45quotationspro.displayErrorMsg(txt_add_product_no_product_quantity);
      return false;
    }

    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'addProductToQuotation',
        id_roja45_quotation: id_roja45_quotation,
        id_product: $(
          '#new_quotation_product input.product_quotation_id'
        ).val(),
        id_product_attribute: $(
          '#new_quotation_product select.product_quotation_id_product_attribute'
        ).val(),
        product_quantity: $(
          '#new_quotation_product input.product_quotation_quantity'
        ).val(),
        product_comment: $(
          '#new_quotation_product #addProductToQuotationProductComment'
        ).val(),
        retail_price: $(
          '#new_quotation_product #addProductToQuotationProductPriceTaxExcl'
        ).val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        roja45quotationspro.displayErrorMsg(
          "Unable to add the product to the quotation.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {
        //roja45quotationspro.toggleModal();
      },
    });
  },

  submitDeleteProductFromQuotation: function (target) {
    roja45quotationspro.toggleModal();
    var id_product = target
      .closest('.product-line-row')
      .find('td .product_id')
      .val();
    var id_roja45_quotation_product = target
      .closest('tr.product-line-row')
      .find('input.product_quotation_id')
      .val();
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'deleteProductOnQuotation',
        id_roja45_quotation: id_roja45_quotation,
        id_roja45_quotation_product: id_roja45_quotation_product,
        id_product: id_product,
      },
      success: function (data) {
        if (data.result && data.result == 'success') {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else if (data.result && data.result == 'error') {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          "Unable to add the product to the quotation.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  submitDeleteSelectedProductsFromQuotation: function (target) {
    var product_ids = [];
    $('input.select-quotation-product:checked').each(function (e) {
      var id_roja45_quotation_product = $(this)
        .closest('tr')
        .attr('data-id-roja45-quotation-product');
      product_ids.push(id_roja45_quotation_product);
    });

    if (product_ids.length > 0) {
      roja45quotationspro.toggleModal();
      $.ajax({
        type: 'POST',
        url: quotationspro_link,
        cache: false,
        dataType: 'json',
        data: {
          ajax: 1,
          token: token,
          action: 'deleteProductsOnQuotation',
          id_roja45_quotation: id_roja45_quotation,
          product_ids: product_ids,
        },
        success: function (data) {
          if (data.result && data.result == 'success') {
            roja45quotationspro.displaySuccessMsg(data.response);
            window.location = data.redirect;
          } else if (data.result && data.result == 'error') {
            roja45quotationspro.displayErrorMsg(data.errors);
            roja45quotationspro.toggleModal();
          }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          jAlert(
            "Unable to add the product to the quotation.\n\ntextStatus: '" +
              textStatus +
              "'\nerrorThrown: '" +
              errorThrown +
              "'\nresponseText:\n" +
              XMLHttpRequest.responseText
          );
          roja45quotationspro.toggleModal();
        },
        complete: function () {},
      });
    }
  },

  submitNewCharge: function (target) {
    var action = $('#charge_type').val();
    if (action == 'shipping') {
      action = 'submitShippingCharge';
    } else if ('general' || 'handling') {
      action = 'submitNewCharge';
    }
    if ($('#charges_form #charge_name').val().length == 0) {
      jAlert(txt_add_charge_no_charge_name);
      return false;
    }
    if ($('#charges_form #charge_value').val().length == 0) {
      jAlert(txt_add_charge_no_charge_value);
      return false;
    }

    var charge_name = target
      .closest('#charges_form')
      .find('#charge_name')
      .val();
    var charge_type = target
      .closest('#charges_form')
      .find('#charge_type')
      .val();
    var charge_method = target
      .closest('#charges_form')
      .find('#charge_method')
      .val();
    var charge_default = 0;
    if (
      target
        .closest('#charges_form')
        .find('input[name=charge_default]')
        .prop('checked')
    ) {
      charge_default = 1;
    }
    //var charge_default = target.closest('#charges_form').find('input[name=charge_default]:checked').prop('checked', true).val(0)
    //var charge_default = target.closest('#charges_form').find('input[name="charge_default[0]"').val();
    var charge_value = target
      .closest('#charges_form')
      .find('#charge_value')
      .val();
    var charge_handling = target
      .closest('#charges_form')
      .find('#charge_handling')
      .val();
    var carriers = target.closest('#charges_form').find('#carriers').val();
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: action,
        id_roja45_quotation: id_roja45_quotation,
        charge_name: charge_name,
        charge_type: charge_type,
        charge_value: charge_value,
        charge_handling: charge_handling,
        charge_method: charge_method,
        charge_default: charge_default,
        carriers: carriers,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result && data.result == 'success') {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
          //updateAmounts(data.quotation);
          //roja45quotationspro.updateChargeForm(data.charge_table);
          //roja45quotationspro.updateTotals(data.quotation_details);
        } else if (data.result && data.result == 'error') {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          roja45_quotationspro_error_unexpected +
            "\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
    return false;
  },

  submitDeleteCharge: function (target) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitDeleteCharge',
        id_roja45_quotation: id_roja45_quotation,
        id_roja45_quotation_charge: target
          .closest('tr.charge_row')
          .attr('data-id-quotation-charge'),
        charge: target.closest('tr.charge_row').attr('data-charge'),
        charge_wt: target.closest('tr.charge_row').attr('data-charge-wt'),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  submitSaveCharge: function (target) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitSaveCharge',
        id_roja45_quotation: id_roja45_quotation,
        id_carrier: target.closest('tr.charge_row').attr('data-id-carrier'),
        id_roja45_quotation_charge: target
          .closest('tr.charge_row')
          .attr('data-id-quotation-charge'),
        charge_amount: target
          .closest('tr')
          .find('input[name=charge_amount]')
          .val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unexpected
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  submitNewCustomerOrder: function () {
    roja45quotationspro.toggleModal();
    $('#quotationspro_form').submit();
  },

  submitSaveTemplate: function () {
    roja45quotationspro.toggleModal();
    $('#quotationspro_save_template_form').submit();
  },

  stopAjaxQuery: function () {
    if (typeof ajaxQueries == 'undefined') ajaxQueries = new Array();
    for (i = 0; i < ajaxQueries.length; i++) ajaxQueries[i].abort();
    ajaxQueries = new Array();
  },

  updateQuotationTotals: function () {
    $('.quotation_total_discounts');
    $('.quotation_total_charges');
    $('.quotation_total_exc');
    $('.quotation_total_inc');
  },

  editProductRefreshTotal: function (element) {
    element = element.closest('tr.product-line-row');
    var element_list = [];

    var quantity = parseInt(
      element.find('td input.product_quotation_quantity').val()
    );
    if (quantity < 1 || isNaN(quantity)) quantity = 1;
    if (use_taxes)
      var price = parseFloat(
        element.find('td input.product_quotation_price_tax_incl').val()
      );
    else
      var price = parseFloat(
        element.find('td input.product_quotation_price_tax_excl').val()
      );

    if (price < 0 || isNaN(price)) price = 0;

    var total = roja45quotationspro.makeTotalProductCaculation(quantity, price);
    element
      .find('td.total_product')
      .html(
        formatCurrency(total, currency_format, currency_sign, currency_blank)
      );
  },

  makeTotalProductCaculation: function (quantity, price) {
    return Math.round(quantity * price * 100) / 100;
  },

  updateProductsTable: function (view) {
    html = $(view);
    html.find('#quotationProducts').hide();
    //$('tr#new_invoice').hide();
    $('#new_quotation_product').hide();

    // Initialize fields
    roja45quotationspro.closeAddProduct();

    $('#quotationProducts tr.product-line-row').remove();
    $('#quotationProducts tbody #new_quotation_product').before(html);
    /*
        $('tr#new_quotation_product').before(html);
        html.find('td').each(function() {
            if (!$(this).is('.product_invoice'))
                $(this).fadeIn('slow');
        });
        */
  },

  updateAmounts: function (quotation) {
    $('#total_products td.amount').fadeOut('slow', function () {
      $(this).html(
        formatCurrency(
          parseFloat(quotation.total_products_wt),
          currency_format,
          currency_sign,
          currency_blank
        )
      );
      $(this).fadeIn('slow');
    });
    $('#total_discounts td.amount').fadeOut('slow', function () {
      $(this).html(
        formatCurrency(
          parseFloat(quotation.total_discount),
          currency_format,
          currency_sign,
          currency_blank
        )
      );
      $(this).fadeIn('slow');
    });
    if (quotation.total_discount > 0) $('#total_discounts').slideDown('slow');
    $('#total_wrapping td.amount').fadeOut('slow', function () {
      $(this).html(
        formatCurrency(
          parseFloat(quotation.total_wrapping),
          currency_format,
          currency_sign,
          currency_blank
        )
      );
      $(this).fadeIn('slow');
    });
    if (quotation.total_wrapping_tax_incl > 0)
      $('#total_wrapping').slideDown('slow');
    $('#total_shipping td.amount').fadeOut('slow', function () {
      $(this).html(
        formatCurrency(
          parseFloat(quotation.total_shipping),
          currency_format,
          currency_sign,
          currency_blank
        )
      );
      $(this).fadeIn('slow');
    });
    $('#total_order td.amount').fadeOut('slow', function () {
      $(this).html(
        formatCurrency(
          parseFloat(quotation.total_to_pay),
          currency_format,
          currency_sign,
          currency_blank
        )
      );
      $(this).fadeIn('slow');
    });
    $('#total_quotation td.amount').fadeOut('slow', function () {
      $(this).html(
        formatCurrency(
          parseFloat(quotation.total_to_pay_wt),
          currency_format,
          currency_sign,
          currency_blank
        )
      );
      $(this).fadeIn('slow');
    });
    $('.alert').slideDown('slow');
    $('#product_number').fadeOut('slow', function () {
      var old_quantity = parseInt($(this).html());
      $(this).html(old_quantity + 1);
      $(this).fadeIn('slow');
    });
  },

  closeAddProduct: function () {
    $('#new_quotation_product').slideUp();
    // Initialize fields
    $('#new_quotation_product select, #new_quotation_product input').each(
      function () {
        if (!$(this).is('.button')) $(this).val('');
      }
    );
    $('#product_quotation_product_quantity').val('1');
    $('#product_quotation_product_attribute_id option').remove();
    $('#product_quotation_id_product_attribute').hide();
    current_product = null;
  },

  updateDiscountForm: function (discount_form_html) {
    $('#discount_table').html(discount_form_html);

    $('#voucher_form').slideUp();

    if ($('#discount_table .discount_row').length > 0) {
      has_voucher = 1;
    } else {
      has_voucher = 0;
    }

    $('.quotation_action').fadeIn();
  },

  updateChargeForm: function (charge_form_html) {
    $('#charges_table').html(charge_form_html);

    $('#charges_form').slideUp();

    if ($('#charges_table .charge_row').length > 0) {
      has_charges = 1;
    } else {
      has_charges = 0;
    }

    $('.quotation_action').fadeIn();
  },

  updateProductLines: function (product_lines) {
    $('#quotationProducts').fadeOut('fast', function () {
      $('#quotationProducts tbody').html(product_lines);
      $('#quotationProducts').fadeIn();
      roja45quotationspro.initProductLineEvents();
    });
  },

  updateTotals: function (product_lines) {
    $('#totals_panel').fadeOut('fast', function () {
      $('#totals_panel').html(product_lines);
      $('#totals_panel').fadeIn();
    });
  },

  updateNotesForm: function (charge_form_html) {
    $('#notes_table').html(charge_form_html);

    if ($('#notes_table .note_row').length > 0) {
      has_notes = 1;
    } else {
      has_notes = 0;
    }
  },

  createCustomerAccount: function (ele) {
    roja45quotationspro.toggleModal();
    var query =
      'ajax=1&token=' +
      token +
      '&action=createCustomerAccount&id_roja45_quotation=' +
      id_roja45_quotation +
      '&firstname=' +
      ele.attr('data-customer-firstname') +
      '&lastname=' +
      ele.attr('data-customer-lastname') +
      '&email=' +
      ele.attr('data-customer-email');
    var ajax_query = $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: query,
      success: function (data) {
        if (data.result) {
          roja45quotationspro.displaySuccessMsg(data.response);
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(data.error);
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          roja45_quotationspro_error_createaccount +
            "\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
    });
  },

  addQuotationToOrder: function (target) {
    $('.quotationspro_request_dialog_overlay').addClass(
      'roja45quotationspro-darken-background'
    );
    $('#quotationspro_request_dialog .page-heading').html(
      $('input[name=DIALOG_HEADING]').val()
    );

    $('.quotationspro_request_dialog_overlay').css('width', '100%');
    $('.quotationspro_request_dialog_overlay').css('height', '100%');
    $('.quotationspro_request_dialog_overlay').show();

    $('#quotationspro_request_dialog').fadeIn('fast');
  },

  saveAsTemplate: function (target) {
    $('.quotationspro_request_dialog_overlay').addClass(
      'roja45quotationspro-darken-background'
    );
    $('#quotationspro_request_dialog .page-heading').html(
      $('input[name=DIALOG_HEADING]').val()
    );

    $('.quotationspro_request_dialog_overlay').css('width', '100%');
    $('.quotationspro_request_dialog_overlay').css('height', '100%');
    $('.quotationspro_request_dialog_overlay').show();

    $('#quotationspro_save_template').fadeIn('fast');
  },

  addDocument: function (target) {
    $('.quotationspro_request_dialog_overlay').addClass(
      'roja45quotationspro-darken-background'
    );
    $('#quotationspro_request_dialog .page-heading').html(
      $('input[name=DIALOG_HEADING]').val()
    );

    $('.quotationspro_request_dialog_overlay').css('width', '100%');
    $('.quotationspro_request_dialog_overlay').css('height', '100%');
    $('.quotationspro_request_dialog_overlay').show();

    $('#quotationspro_add_document').fadeIn('fast');
  },

  updateTemplate: function (ele) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        action: 'updateTemplate',
        ajax: 1,
        id_roja45_template: ele
          .closest('form')
          .find('input[name=id_roja45_quotation]')
          .val(),
        valid_for: ele.closest('form').find('input[name=valid_for]').val(),
        tax_country: ele.closest('form').find('select[name=tax_country]').val(),
        tax_state: ele.closest('form').find('select[name=tax_state]').val(),
        ROJA45_QUOTATIONSPRO_ENABLE_TAXES: ele
          .closest('form')
          .find('input[name=ROJA45_QUOTATIONSPRO_ENABLE_TAXES]')
          .val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          "Unable to load answer.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
    });
  },

  createQuote: function (ele) {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        action: 'createQuote',
        ajax: 1,
        id_roja45_template: ele
          .closest('form')
          .find('input[name=id_roja45_quotation]')
          .val(),
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          "Unable to load answer.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
    });
  },

  deleteQuotation: function () {
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        action: 'deleteQuotation',
        ajax: 1,
        token: token,
        id_roja45_quotation: id_roja45_quotation,
      },
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        roja45quotationspro.toggleModal();
        jAlert(
          "Unable to load answer.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
      },
    });
  },

  claimRequest: function () {
    roja45quotationspro.toggleModal();
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitClaimQuotation',
        id_roja45_quotation: id_roja45_quotation,
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          $.each(data.errors, function (index, value) {
            roja45quotationspro.displayErrorMsg(value);
          });
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        roja45quotationspro.displayErrorMsg(
          roja45_quotationspro_error_unabletoclaim +
            "\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  releaseRequest: function () {
    roja45quotationspro.toggleModal();
    $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: {
        ajax: 1,
        token: token,
        action: 'submitReleaseQuotation',
        id_roja45_quotation: id_roja45_quotation,
      },
      success: function (data) {
        if (data.result) {
          window.location = data.redirect;
        } else {
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected
          );
          roja45quotationspro.toggleModal();
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          "Unable to claim the quotation.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
        roja45quotationspro.toggleModal();
      },
      complete: function () {},
    });
  },

  resetCart: function () {
    var query =
      'ajax=1&token=' +
      token +
      '&action=submitResetCart&id_roja45_quotation=' +
      id_roja45_quotation;
    var ajax_query = $.ajax({
      type: 'POST',
      url: quotationspro_link,
      cache: false,
      dataType: 'json',
      data: query,
      beforeSend: function () {
        roja45quotationspro.toggleModal();
      },
      success: function (data) {
        if (data.result) {
          if (data.result == 'success') {
            $('.cart-badge').fadeOut();
          } else {
            roja45quotationspro.displayErrorMsg(
              roja45_quotationspro_error_unexpected
            );
          }
        } else
          roja45quotationspro.displayErrorMsg(
            roja45_quotationspro_error_unexpected
          );
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        jAlert(
          "Unable to claim the quotation.\n\ntextStatus: '" +
            textStatus +
            "'\nerrorThrown: '" +
            errorThrown +
            "'\nresponseText:\n" +
            XMLHttpRequest.responseText
        );
      },
      complete: function () {
        roja45quotationspro.toggleModal();
      },
    });
  },

  toggleModal: function () {
    $('#roja45_quotation_modal_dialog').toggle();
  },

  toggleSavingIndicator: function () {
    $('.disabled-while-saving').prop('disabled', function (i, v) {
      return !v;
    });
    $('#roja45quotation_form .badge.saving-indicator').fadeToggle('fast');
  },

  displaySuccessMsg: function (msg) {
    $.growl.notice({
      duration: 3000,
      location: 'immersive',
      title: 'Success',
      message: msg,
    });
  },

  displayWarningMsg: function (msg) {
    $.growl.warning({
      duration: 6000,
      location: 'immersive',
      title: 'Warning',
      message: msg,
    });
  },

  displayErrorMsg: function (msg) {
    $.growl.error({
      duration: 10000,
      location: 'immersive',
      title: 'Error',
      message: msg,
    });
  },
};
