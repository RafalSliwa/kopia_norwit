/**
 * Custom Carrier - Product Save Handler
 * Intercepts PrestaShop 8 product form save and sends customcarrier fields via AJAX.
 */
(function () {
    'use strict';

    function getCustomCarrierFields() {
        var fields = [
            'customcarrier_free_shipping',
            'customcarrier_base_shipping_cost',
            'customcarrier_multiply_by_quantity',
            'customcarrier_free_shipping_quantity',
            'customcarrier_apply_threshold',
            'customcarrier_separate_package',
            'customcarrier_exclude_from_free_shipping',
            'customcarrier_max_quantity_per_package',
            'customcarrier_max_packages',
            'customcarrier_cost_above_max_packages',
            'customcarrier_id_product'
        ];

        var data = {};
        fields.forEach(function (name) {
            var elements = document.querySelectorAll('[name="' + name + '"]');
            if (elements.length === 0) return;

            // Radio buttons - get checked value
            if (elements[0].type === 'radio') {
                elements.forEach(function (el) {
                    if (el.checked) {
                        data[name] = el.value;
                    }
                });
            } else {
                data[name] = elements[0].value;
            }
        });

        return data;
    }

    function saveCustomCarrierSettings() {
        var fields = getCustomCarrierFields();

        // Check if customcarrier fields exist on the page
        if (!fields.customcarrier_id_product) {
            return;
        }

        var formData = new FormData();
        formData.append('ajax', '1');
        formData.append('action', 'saveProductSettings');

        Object.keys(fields).forEach(function (key) {
            formData.append(key, fields[key]);
        });

        fetch(customcarrier_ajax_url, {
            method: 'POST',
            body: formData
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (data.success) {
                console.log('CustomCarrier: product settings saved');
            } else {
                console.error('CustomCarrier: save failed', data.message);
            }
        }).catch(function (error) {
            console.error('CustomCarrier: AJAX error', error);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // PrestaShop 8 product page: intercept form submit
        var form = document.querySelector('form[name="product"]');
        if (!form) {
            form = document.getElementById('product_form');
        }
        if (!form) {
            form = document.querySelector('#main-div form, #product-form');
        }

        if (form) {
            form.addEventListener('submit', function () {
                saveCustomCarrierSettings();
            });
        }

        // Also hook into PrestaShop 8 save buttons (they may use AJAX, not form submit)
        var saveButtons = document.querySelectorAll(
            '.product-footer button[type="submit"], ' +
            'button.btn-primary-reverse.btn-outline-primary, ' +
            '#product_form_save_go_to_catalog_btn, ' +
            '#product_form_save_new_btn, ' +
            '#product_form_save_btn, ' +
            'button[name="product[save]"]'
        );

        saveButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Small delay to let PS8 start its save process
                setTimeout(saveCustomCarrierSettings, 100);
            });
        });
    });
})();
