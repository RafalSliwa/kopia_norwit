/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Software License Agreement
 * that is bundled with this package in the file LICENSE.txt.
 *
 *  @author    Peter Sliacky (Prestasmart)
 *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

tc_confirmOrderValidations['lowcostexpress'] = function() {
  // if lowcostexpress carrier variables are not set, skip validation
  if (typeof ajax_url_mfb === 'undefined' || typeof carrier_ids === 'undefined' || $("input[name=selected_relay_code]").length == 0) {
    return true;
  }

  let selected_carrier_id = $('.delivery-option input[type=radio]:checked').val();
  selected_carrier_id = selected_carrier_id.split(',')[0];

  if (carrier_ids.indexOf(selected_carrier_id) > 0 && $("input[name=selected_relay_code]").val().length == 0) {
       alert(errormessage);
       $.scrollTo($('#relay_container'), 800);
       e.preventDefault();
       return false;
   }
  return true;
}

checkoutShippingParser.lowcostexpress = {
  after_load_callback: function(deliveryOptionIds) {
    $.getScript(tcModuleBaseUrl + '/../lowcostexpress/views/js/delivery_locations.js', function() {
      // delivery_locations.js loaded, now execute initialization, as by default, this is done in carrier extra hook
      // in DOMContentLoaded event handler, but that is triggered too early (when delivery_locations.js is not yet loaded)

        // Trigger map display toggle when a carrier service is selected
        $('form#js-delivery input[type="radio"]').change(function(e) {
            toggle_map_display(e);
        });

        // move in DOM to prevent compatibility issues with Common Services' modules
        if($("#relay_container").length>0)
        {
            $('#relay_dummy_container').remove();
        } else {
            $('#relay_dummy_container').insertAfter($('#extra_carrier'));
            $('#relay_dummy_container').attr('id', 'relay_container');
        }

        // Trigger map display toggle on first load
        toggle_map_display();

    });
  },
}
