/**
 * roja45quotationspro_order.js.
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
  $(document).on('click', '#submitMessage', function (e) {
    e.preventDefault();
    if ($(this).hasClass('disabled')) {
      return;
    }
    roja45quotationspro_summary.submitMessage();
  });

  $(document).on('change', '#roja45quotationspro_use_this_address_for_invoice', function (e) {
    e.preventDefault();
    if ($(this).is(':checked')) {
      $(this).val('1');
    } else {
      $(this).val('0');
    }
  });

  $(document.body).on('click', '.request-quotation', function (e) {
    e.preventDefault();
    if ($(this).hasClass('disabled')) {
      return;
    }
    if (roja45quotationspro_summary.processSend()) {
      $('.btn.btn-default.request-quotation')
        .prop('disabled', 'disabled')
        .addClass('disabled');
      if (
        parseInt(roja45quotationspro_enable_captcha) &&
        roja45quotationspro_enable_captchatype == 1
      ) {
        grecaptcha.execute();
      } else if (
        parseInt(roja45quotationspro_enable_captcha) &&
        roja45quotationspro_enable_captchatype == 2
      ) {
        grecaptcha.ready(function () {
          grecaptcha
            .execute(roja45quotationspro_recaptcha_site_key, {
              action: 'homepage',
            })
            .then(function (token) {
              $('#quotationspro_request_form').append(
                '<input type="hidden" name="g-recaptcha-response" value="' +
                  token +
                  '">'
              );
              roja45quotationspro_summary.submitForm();
            });
        });
      } else {
        roja45quotationspro_summary.submitForm();
      }
    }
  });

  if (roja45quotationspro_enable_captcha) {
    if (roja45quotationspro_enable_captchatype == 0) {
      onloadRecaptchaCallback = function () {
        if (typeof roja45_recaptcha_widgets !== 'undefined') {
          $.each(roja45_recaptcha_widgets, function (index, value) {
            grecaptcha.render(value, {
              sitekey: roja45quotationspro_recaptcha_site_key,
              callback: onRecaptchaSubmitCallback,
            });
          });
        }
      };
      onRecaptchaSubmitCallback = function (token) {
        $('.quote_navigation .request-quotation').removeClass('disabled');
      };
      var url =
        '//www.google.com/recaptcha/api.js?onload=onloadRecaptchaCallback&render=explicit';
      var element = document.createElement('script');
      element.src = url;
      document.body.appendChild(element);
    } else if (roja45quotationspro_enable_captchatype == 1) {
      onRecaptchaInvisibleSubmitCallback = function (response) {
        roja45quotationspro_summary.submitForm();
      };
      var url = '//www.google.com/recaptcha/api.js';
      var element = document.createElement('script');
      element.src = url;
      document.body.appendChild(element);
    } else if (roja45quotationspro_enable_captchatype == 2) {
      $('.quote_navigation .request-quotation').addClass('disabled');
      onloadRecaptchaCallback = function () {
        grecaptcha.ready(function () {
          $('.quote_navigation .request-quotation').removeClass('disabled');
        });
      };
      var url =
        '//www.google.com/recaptcha/api.js?onload=onloadRecaptchaCallback&render=' +
        roja45quotationspro_recaptcha_site_key;
      var element = document.createElement('script');
      element.src = url;
      document.body.appendChild(element);
    }
  }

  $(document).on('change', 'input[name=quote_quantity]', function (e) {
    e.preventDefault();
    roja45quotationspro_summary.updateQty($(this));
  });

  $(document).on('change', 'select[data-field-type=COUNTRY]', function (e) {
    e.preventDefault();
    roja45quotationspro_summary.getStates($(this));
  });

  $('.quotationspro_request_field_container .quotationspro_request_field_collapse').each(function( index ) {
    let target = $(this).attr('data-collapse-target');
    let component = $(target).attr('data-target');
    $(target).addClass('collapsed');
    $(target).show();
    $(component).removeClass('show');
    $(component).removeClass('in');
  });
});

roja45quotationspro_summary = {
  processSend: function () {
    var url = $('#quotationspro_request_form').attr('action');
    var errors = 0;

    $('#quotationspro_request_form .form-field').each(function (index, value) {

      var visible = $(value).is(":visible");
      if (visible == true) {
        $(this).parent().removeClass('form-error').addClass('form-ok');

        if ($(this).hasClass('is_required') && $(this).val().length == 0) {
          $(this).parent().addClass('form-error').removeClass('form-ok');
          errors++;
        } else if (
            $(this).hasClass('is_required') &&
            $(this).attr('data-field-type') == 'ADDRESS_SELECTOR'
        ) {
          if ($(this).val() == 0) {
            let name = $(this).attr('name');
            let target = $(this).closest('#COMPONENT_' + name).find('#LINK_' + name).attr('data-collapse-target');

            if ($(target).hasClass('collapsed')) {
              $(this).parent().addClass('form-error').removeClass('form-ok');
              errors++;
            }
          }
        } else if (
            typeof $(this).attr('data-validate') !== 'undefined' &&
            $(this).attr('data-validate') !== 'none'
        ) {
          if ($(this).val().length > 0) {
            if (
                $(this).attr('name') == 'postcode' &&
                typeof countriesNeedZipCode[
                    $('#id_country option:selected').val()
                    ] !== 'undefined'
            ) {
              var result = window['validate_' + $(this).attr('data-validate')](
                  $(this).val(),
                  countriesNeedZipCode[$('#id_country option:selected').val()]
              );
            } else if ($(this).attr('type') == 'checkbox') {
              var result = window['validate_' + $(this).attr('data-validate')](
                  $(this)
              );
            } else if ($(this).data('validate') == 'isCustom') {
              var result = validate_isCustom(
                  $(this).val(),
                  new RegExp(decodeURIComponent($(this).data('custom-regex')), 'i')
              );
            } else if ($(this).data('validate') == 'isDate') {
              var format = $(this).attr('data-format');
              var result = validate_isDate($(this).val(), format);
            } else {
              var result = window['validate_' + $(this).attr('data-validate')](
                  $(this).val()
              );
            }
            if (!result) {
              $(this).parent().addClass('form-error').removeClass('form-ok');
              errors++;
            }
          }
        }
      }
    });

    if (errors == 0) {
      return true;
    } else {
      return false;
    }
  },

  submitMessage: function () {
    var form = $('#customerMessageForm')[0];
    var formData = new FormData(form);
    $.ajax({
      type: 'POST',
      enctype: 'multipart/form-data',
      url: $('#customerMessageForm').attr('action'),
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      cache: false,
      beforeSend: function () {
        $('#message_form_modal').show();
      },
      success: function (data) {
        if (data.result) {
          var new_message = $('.customer-messages > .message').first().clone();
          new_message.removeClass('admin-message').addClass('customer-message');
          new_message.find('.received').empty().html(data.date_add);
          new_message
            .find('.message-content .msg')
            .empty()
            .html(data.message.substr(0, 100));
          $('.customer-messages').prepend(new_message);
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
        }
        form.reset();
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
        return 0;
      },
      complete: function () {
        $('#message_form_modal').hide();
      },
    });
  },

  submitForm: function () {
    var formData = new FormData();
    var fileInput = $('input[name=uploadedfile]');
    if (fileInput.length > 0) {
      var file = fileInput[0].files[0];
      formData.append('uploadedfile', file);
    }

    formData.append('ajax', 1);
    formData.append('action', 'SubmitInstantRequest');
    formData.append(
      'ROJA45QUOTATIONSPRO_EMAIL',
      $('input[name=ROJA45QUOTATIONSPRO_EMAIL]').val()
    );
    formData.append(
      'ROJA45QUOTATIONSPRO_FIRSTNAME',
      $('input[name=ROJA45QUOTATIONSPRO_FIRSTNAME]').val()
    );
    formData.append(
      'ROJA45QUOTATIONSPRO_LASTNAME',
      $('input[name=ROJA45QUOTATIONSPRO_LASTNAME]').val()
    );
    formData.append(
      'ROJA45QUOTATIONSPRO_CUSTOMER_COPY',
      $('input[name=ROJA45QUOTATIONSPRO_CUSTOMER_COPY]').val()
    );

    var request = {};
    request.columns = [];

    $('#quotationspro_request_form .quotationspro_request.column').each(
      function (i) {
        var column = {};
        var heading = $(this).find('.page-subheading').html();
        column.heading = heading;
        // column id
        var col = $(this).data('column');
        column.num = col;
        column.fields = [];

        $(this)
          .find('.form-field')
          .each(function (j) {
            var label = $(this)
              .closest('.form-group')
              .find('.control-label')
              .html();
            var field = {};
            field.pos = j;
            field.name = $(this).attr('name');
            field.type = $(this).attr('data-field-type');
            field.label = label.trim();
            if (field.type == 'SWITCH') {
              field.value = $('input[name=' + field.name + ']:checked').val();
            } else if (
              field.type == 'COUNTRY' ||
              field.type == 'STATE' ||
              field.type == 'SHIPPING_METHOD' ||
              field.type == 'CUSTOM_SELECT' ||
              field.type == 'ADDRESS_SELECTOR'
            ) {
              field.value = $(this)
                .children('option:selected')
                .attr('data-value');
              field.id = $(this).val();
            } else {
              field.value = $(this).val();
            }
            column.fields[j] = field;
          });
        request.columns[i] = column;
      }
    );
    formData.append('ROJA45QUOTATIONSPRO_FORMDATA', JSON.stringify(request));

    $('#submitRequest').attr('disabled', 'disabled');
    if (roja45quotationspro_instantresponse == 1) {
      if (roja45quotationspro_enable_captcha == 1) {
        if (roja45quotationspro_enable_captchatype == 2) {
          $.ajax({
            url: $('#quotationspro_request_form').attr('action'),
            type: 'post',
            data: {
              ajax: 1,
              action: 'ValidateRecaptcha',
              'g-recaptcha-response': $(
                'input[name=g-recaptcha-response]'
              ).val(),
            },
            dataType: 'json',
            success: function (data) {
              if (data.result) {
                roja45quotationspro_summary.sendRequest(formData);
              } else {
                roja45quotationspro.displayErrorMsg(data.errors);
              }
            },
            error: function (data) {
              roja45quotationspro.displayErrorMsg([
                roja45quotationspro_sent_failed,
              ]);
              return 0;
            },
            complete: function () {},
          });
        }
      } else {
        roja45quotationspro_summary.sendRequest(formData);
      }
    } else {
      $('input[name=ROJA45QUOTATIONSPRO_FORMDATA]').val(
        JSON.stringify(request)
      );
      $('#quote_summary .request-summary-container-modal').show();
      $('#quotationspro_request_form').submit();
    }
  },

  sendRequest: function (formData) {
    $.ajax({
      type: 'POST',
      enctype: 'multipart/form-data',
      url: $('#quotationspro_request_form').attr('action'),
      data: formData,
      processData: false,
      contentType: false,
      cache: false,
      beforeSend: function () {
        $('.quotation_cart .header .ajax_quote_quantity').hide();
        $('.quotation_cart .quote-cart-block').remove();
        $('#quote_summary .request-summary-container-modal').show();
      },
      success: function (data) {
        let response = JSON.parse(data);
        if (response.result) {
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
      },
      complete: function () {
        $('#quote_summary .request-summary-container-modal').hide();
      },
    });
    $('#quotationspro_request_form').fadeOut('fast', function (e) {
      $('#quotationspro_request_form').remove();
      $('.title_block.quote_title').hide();
      $('.title_block.received_title').show();
      $('#quote_summary .quotationspro_request_container').fadeIn();
      $('#roja_desktop_quotecart .header').prependTo(
        '#roja_desktop_quotecart .quotation_cart'
      );
      $('#roja_desktop_quotecart .quotation_cart > a').remove();
      $('#roja_desktop_quotecart .header .ajax_quote_quantity').empty();
      $(
        '#roja_desktop_quotecart .quote-cart-block .block-content .products > dt'
      ).remove();
    });
  },

  updateButtons: function (id_product) {
    var url = $('.request-quote').attr('href');
    $.ajax({
      url:
        url +
        '?submitUpdateSummaryButtons=1&id_product=' +
        id_product +
        '&ajax=1',
      type: 'post',
      dataType: 'json',
      success: function (data) {
        if (data.result == 'success') {
          roja45quotationspro.displaySuccessMsg(data.response);
          if (data.enable == 0) {
            $('.standard-checkout').show();
            $('.request-quote').hide();
          }
        } else if (data.result == 'error') {
          roja45quotationspro.displayErrorMsg(data.errors);
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
      },
      complete: function (data) {},
    });
  },

  updateQty: function (ele, mode) {
    var qty = $(ele)
      .closest('.flex-cell')
      .find('input[name=quote_quantity]')
      .val();
    $('#submitRequest').attr('disabled', 'disabled');
    $.ajax({
      url: roja45_quoationspro_controller,
      type: 'post',
      dataType: 'json',
      data: {
        ajax: 1,
        action: 'submitQuantity',
        id_roja45_quotation_requestproduct: $(ele)
          .closest('div.request-product')
          .attr('data-id-roja45-quotation-requestproduct'),
        quantity: qty,
      },
      success: function (data) {
        if (data.result) {
          //roja45quotationspro.displaySuccessMsg(data.response);
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
      },
      complete: function (data) {
        $('#submitRequest').removeAttr('disabled');
      },
    });
  },
  getStates: function (ele) {
    $.ajax({
      url: roja45_quoationspro_controller,
      type: 'post',
      dataType: 'json',
      data: {
        ajax: 1,
        action: 'getStates',
        id_country: $(
          'select[name="ROJA45QUOTATIONSPRO_CUSTOMER_COUNTRY"]'
        ).val(),
      },
      success: function (data) {
        if (data.result) {
          if (data.states.length > 0) {
            $.each(data.states, function (index, state) {
              $('select[data-field-type=STATE]').append(
                '<option value="' +
                  state.id_state +
                  '">' +
                  state.name +
                  '</option>'
              );
            });
            $('select[data-field-type=STATE]').closest('.form-group').fadeIn();
          } else {
            $('select[data-field-type=STATE]').empty();
            $('select[data-field-type=STATE]').closest('.form-group').fadeOut();
          }
        } else {
          roja45quotationspro.displayErrorMsg(data.errors);
        }
      },
      error: function (data) {
        roja45quotationspro.displayErrorMsg([roja45quotationspro_sent_failed]);
      },
      complete: function (data) {
        $('#submitRequest').removeAttr('disabled');
      },
    });
  },
};
