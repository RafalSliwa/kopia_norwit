{**
 * Custom Carrier - Product Transport Settings Tab
 * Displayed in Product > Shipping section
 *}

<div class="customcarrier-product-settings card mt-3">
    <div class="card-header">
        <h3 class="card-header-title">
            <i class="material-icons">local_shipping</i>
            {l s='Custom Carrier Settings' mod='customcarrier'}
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            {l s='Configure shipping cost rules for this product. These settings will be used by the Custom Carrier module to calculate shipping costs.' mod='customcarrier'}
        </div>

        {* Warnings container for conflicting options *}
        <div id="customcarrier_warnings_container"></div>

        {* Free Shipping - Unconditional *}
        <div class="form-group row">
            <label class="form-control-label col-lg-3">
                {l s='Free shipping' mod='customcarrier'}
            </label>
            <div class="col-lg-9">
                <div class="input-group">
                    <span class="ps-switch ps-switch-lg">
                        <input type="radio"
                               name="customcarrier_free_shipping"
                               id="customcarrier_free_shipping_off"
                               value="0"
                               {if !isset($customcarrier_settings.free_shipping) || !$customcarrier_settings.free_shipping}checked{/if}>
                        <label for="customcarrier_free_shipping_off">{l s='No' mod='customcarrier'}</label>
                        <input type="radio"
                               name="customcarrier_free_shipping"
                               id="customcarrier_free_shipping_on"
                               value="1"
                               {if isset($customcarrier_settings.free_shipping) && $customcarrier_settings.free_shipping}checked{/if}>
                        <label for="customcarrier_free_shipping_on">{l s='Yes' mod='customcarrier'}</label>
                        <span class="slide-button"></span>
                    </span>
                </div>
                <small class="form-text text-muted">
                    {l s='If enabled, this product will always have free shipping (highest priority).' mod='customcarrier'}
                </small>
            </div>
        </div>

        {* Conditional fields - hidden when free_shipping is ON *}
        <div id="customcarrier_conditional_fields">

            {* Base Shipping Cost *}
            <div class="form-group row" id="customcarrier_cost_fields">
                <label class="form-control-label col-lg-3">
                    {l s='Base shipping cost (gross)' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group money-type" style="max-width: 200px;">
                        <input type="number"
                               name="customcarrier_base_shipping_cost"
                               id="customcarrier_base_shipping_cost"
                               class="form-control"
                               value="{if isset($customcarrier_settings.base_shipping_cost)}{$customcarrier_settings.base_shipping_cost|floatval}{else}0{/if}"
                               min="0"
                               step="0.01">
                        <div class="input-group-append">
                            <span class="input-group-text">{if isset($customcarrier_currency_sign)}{$customcarrier_currency_sign}{else}zł{/if} {l s='gross' mod='customcarrier'}</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {l s='Base shipping cost for this product (gross price with VAT).' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Multiply by Quantity *}
            <div class="form-group row" id="customcarrier_multiply_fields">
                <label class="form-control-label col-lg-3">
                    {l s='Multiply by quantity' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="ps-switch ps-switch-lg">
                            <input type="radio"
                                   name="customcarrier_multiply_by_quantity"
                                   id="customcarrier_multiply_by_quantity_off"
                                   value="0"
                                   {if !isset($customcarrier_settings.multiply_by_quantity) || !$customcarrier_settings.multiply_by_quantity}checked{/if}>
                            <label for="customcarrier_multiply_by_quantity_off">{l s='No' mod='customcarrier'}</label>
                            <input type="radio"
                                   name="customcarrier_multiply_by_quantity"
                                   id="customcarrier_multiply_by_quantity_on"
                                   value="1"
                                   {if isset($customcarrier_settings.multiply_by_quantity) && $customcarrier_settings.multiply_by_quantity}checked{/if}>
                            <label for="customcarrier_multiply_by_quantity_on">{l s='Yes' mod='customcarrier'}</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                    <small class="form-text text-muted">
                        {l s='If enabled, shipping cost = base cost × quantity.' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Free Shipping Quantity Threshold *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Free shipping from quantity' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group" style="max-width: 200px;">
                        <input type="number"
                               name="customcarrier_free_shipping_quantity"
                               id="customcarrier_free_shipping_quantity"
                               class="form-control"
                               value="{if isset($customcarrier_settings.free_shipping_quantity)}{$customcarrier_settings.free_shipping_quantity|intval}{else}0{/if}"
                               min="0"
                               step="1">
                        <div class="input-group-append">
                            <span class="input-group-text">{l s='pcs' mod='customcarrier'}</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {l s='Free shipping when customer orders at least this many items. Set 0 to disable.' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Free Shipping From Product Price *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Free shipping from product price' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group" style="max-width: 250px;">
                        <input type="number"
                               name="customcarrier_free_shipping_from_price"
                               id="customcarrier_free_shipping_from_price"
                               class="form-control"
                               value="{if isset($customcarrier_settings.free_shipping_from_price) && $customcarrier_settings.free_shipping_from_price}{$customcarrier_settings.free_shipping_from_price|floatval}{else}{/if}"
                               min="0"
                               step="0.01"
                               placeholder="0">
                        <div class="input-group-append">
                            <span class="input-group-text">{if isset($customcarrier_currency_sign)}{$customcarrier_currency_sign}{else}zł{/if} {l s='gross' mod='customcarrier'}</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {l s='Free shipping when product gross price is equal or greater than this amount. Leave empty to disable.' mod='customcarrier'}
                        <br>
                        <strong>{l s='Example:' mod='customcarrier'}</strong> {l s='Setting 3500 PLN → products costing 3500 PLN and more have free shipping.' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Apply Amount Threshold *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Apply cart value threshold' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="ps-switch ps-switch-lg">
                            <input type="radio"
                                   name="customcarrier_apply_threshold"
                                   id="customcarrier_apply_threshold_off"
                                   value="0"
                                   {if !isset($customcarrier_settings.apply_threshold) || !$customcarrier_settings.apply_threshold}checked{/if}>
                            <label for="customcarrier_apply_threshold_off">{l s='No' mod='customcarrier'}</label>
                            <input type="radio"
                                   name="customcarrier_apply_threshold"
                                   id="customcarrier_apply_threshold_on"
                                   value="1"
                                   {if isset($customcarrier_settings.apply_threshold) && $customcarrier_settings.apply_threshold}checked{/if}>
                            <label for="customcarrier_apply_threshold_on">{l s='Yes' mod='customcarrier'}</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                    <small class="form-text text-muted">
                        {l s='If enabled, free shipping when cart value exceeds the free shipping threshold (configured in carrier wizard: Shipping > Carriers > Edit > Shipping costs).' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Separate Package *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Separate package' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="ps-switch ps-switch-lg">
                            <input type="radio"
                                   name="customcarrier_separate_package"
                                   id="customcarrier_separate_package_off"
                                   value="0"
                                   {if !isset($customcarrier_settings.separate_package) || !$customcarrier_settings.separate_package}checked{/if}>
                            <label for="customcarrier_separate_package_off">{l s='No' mod='customcarrier'}</label>
                            <input type="radio"
                                   name="customcarrier_separate_package"
                                   id="customcarrier_separate_package_on"
                                   value="1"
                                   {if isset($customcarrier_settings.separate_package) && $customcarrier_settings.separate_package}checked{/if}>
                            <label for="customcarrier_separate_package_on">{l s='Yes' mod='customcarrier'}</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                    <small class="form-text text-muted">
                        {l s='If enabled, this product is always shipped separately (e.g., oversized items). Cost calculated independently.' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Exclude from Free Shipping *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Exclude from free shipping' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="ps-switch ps-switch-lg">
                            <input type="radio"
                                   name="customcarrier_exclude_from_free_shipping"
                                   id="customcarrier_exclude_from_free_shipping_off"
                                   value="0"
                                   {if !isset($customcarrier_settings.exclude_from_free_shipping) || !$customcarrier_settings.exclude_from_free_shipping}checked{/if}>
                            <label for="customcarrier_exclude_from_free_shipping_off">{l s='No' mod='customcarrier'}</label>
                            <input type="radio"
                                   name="customcarrier_exclude_from_free_shipping"
                                   id="customcarrier_exclude_from_free_shipping_on"
                                   value="1"
                                   {if isset($customcarrier_settings.exclude_from_free_shipping) && $customcarrier_settings.exclude_from_free_shipping}checked{/if}>
                            <label for="customcarrier_exclude_from_free_shipping_on">{l s='Yes' mod='customcarrier'}</label>
                            <span class="slide-button"></span>
                        </span>
                    </div>
                    <small class="form-text text-muted">
                        {l s='If enabled, this product will never get free shipping (ignores all free shipping rules).' mod='customcarrier'}
                    </small>
                </div>
            </div>

            <hr class="my-4">

            {* NEW: Max Quantity Per Package *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Max quantity per package' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group" style="max-width: 200px;">
                        <input type="number"
                               name="customcarrier_max_quantity_per_package"
                               id="customcarrier_max_quantity_per_package"
                               class="form-control"
                               value="{if isset($customcarrier_settings.max_quantity_per_package)}{$customcarrier_settings.max_quantity_per_package|intval}{else}{/if}"
                               min="0"
                               step="1"
                               placeholder="0">
                        <div class="input-group-append">
                            <span class="input-group-text">{l s='pcs' mod='customcarrier'}</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {l s='Maximum quantity that fits in one package. If exceeded, multiple packages will be charged. Set 0 to disable (unlimited).' mod='customcarrier'}
                        <br>
                        <strong>{l s='Example:' mod='customcarrier'}</strong> {l s='Max 2 pcs per package at 60 zł → 4 pcs = 2 packages = 120 zł' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Max Weight Per Package *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Max weight per package' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group" style="max-width: 200px;">
                        <input type="number"
                               name="customcarrier_max_weight_per_package"
                               id="customcarrier_max_weight_per_package"
                               class="form-control"
                               value="{if isset($customcarrier_settings.max_weight_per_package) && $customcarrier_settings.max_weight_per_package}{$customcarrier_settings.max_weight_per_package|floatval}{else}{/if}"
                               min="0"
                               step="0.01"
                               placeholder="0">
                        <div class="input-group-append">
                            <span class="input-group-text">{Configuration::get('PS_WEIGHT_UNIT')|default:'kg'}</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {l s='Maximum weight per package. Uses product weight from PrestaShop. Set 0 to disable (use quantity instead).' mod='customcarrier'}
                        <br>
                        <strong>{l s='Example:' mod='customcarrier'}</strong> {l s='Max 30 kg per package → 3 shovels (12 kg each = 36 kg) = 2 packages' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* Max Packages *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Max packages' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group" style="max-width: 200px;">
                        <input type="number"
                               name="customcarrier_max_packages"
                               id="customcarrier_max_packages"
                               class="form-control"
                               value="{if isset($customcarrier_settings.max_packages)}{$customcarrier_settings.max_packages|intval}{else}{/if}"
                               min="0"
                               step="1"
                               placeholder="0">
                        <div class="input-group-append">
                            <span class="input-group-text">{l s='packages' mod='customcarrier'}</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {l s='Maximum number of packages before alternative cost applies. Set 0 to disable (unlimited packages).' mod='customcarrier'}
                        <br>
                        <strong>{l s='Example:' mod='customcarrier'}</strong> {l s='Max 2 packages → 5 pcs exceeds limit (needs 3 packages)' mod='customcarrier'}
                    </small>
                </div>
            </div>

            {* NEW: Cost Above Max Packages *}
            <div class="form-group row">
                <label class="form-control-label col-lg-3">
                    {l s='Cost when exceeding max packages (gross)' mod='customcarrier'}
                </label>
                <div class="col-lg-9">
                    <div class="input-group money-type" style="max-width: 200px;">
                        <input type="number"
                               name="customcarrier_cost_above_max_packages"
                               id="customcarrier_cost_above_max_packages"
                               class="form-control"
                               value="{if isset($customcarrier_settings.cost_above_max_packages)}{$customcarrier_settings.cost_above_max_packages|floatval}{else}{/if}"
                               min="0"
                               step="0.01"
                               placeholder="0">
                        <div class="input-group-append">
                            <span class="input-group-text">{if isset($customcarrier_currency_sign)}{$customcarrier_currency_sign}{else}zł{/if} {l s='gross' mod='customcarrier'}</span>
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        {l s='Alternative shipping cost when number of packages exceeds max packages limit (gross price with VAT). Leave empty to continue multiplying base cost.' mod='customcarrier'}
                        <br>
                        <strong>{l s='Example:' mod='customcarrier'}</strong> {l s='Max 2 pcs/package, max 2 packages at 60 zł each, but 5+ pcs (3 packages) = 140 zł total' mod='customcarrier'}
                    </small>
                </div>
            </div>

        </div>

        <input type="hidden" name="customcarrier_id_product" value="{$customcarrier_id_product|intval}">
    </div>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var freeShippingOn = document.getElementById('customcarrier_free_shipping_on');
        var freeShippingOff = document.getElementById('customcarrier_free_shipping_off');
        var conditionalFields = document.getElementById('customcarrier_conditional_fields');

        function toggleConditionalFields() {
            var isHidden = freeShippingOn.checked;
            conditionalFields.style.display = isHidden ? 'none' : 'block';
        }

        freeShippingOn.addEventListener('change', toggleConditionalFields);
        freeShippingOff.addEventListener('change', toggleConditionalFields);
        toggleConditionalFields();

        // AJAX save: intercept PS8 product form save
        var ajaxUrl = '{$customcarrier_ajax_url_web|escape:"javascript":"UTF-8" nofilter}';

        function getCustomCarrierFields() {
            var fields = [
                'customcarrier_free_shipping',
                'customcarrier_base_shipping_cost',
                'customcarrier_multiply_by_quantity',
                'customcarrier_free_shipping_quantity',
                'customcarrier_free_shipping_from_price',
                'customcarrier_apply_threshold',
                'customcarrier_separate_package',
                'customcarrier_exclude_from_free_shipping',
                'customcarrier_max_quantity_per_package',
                'customcarrier_max_weight_per_package',
                'customcarrier_max_packages',
                'customcarrier_cost_above_max_packages',
                'customcarrier_id_product'
            ];
            var data = {};
            fields.forEach(function(name) {
                var elements = document.querySelectorAll('[name="' + name + '"]');
                if (elements.length === 0) return;
                if (elements[0].type === 'radio') {
                    elements.forEach(function(el) {
                        if (el.checked) data[name] = el.value;
                    });
                } else {
                    data[name] = elements[0].value;
                }
            });
            return data;
        }

        function saveCustomCarrierSettings() {
            var fields = getCustomCarrierFields();
            if (!fields.customcarrier_id_product) return;

            var formData = new FormData();
            formData.append('ajax', '1');
            formData.append('action', 'saveProductSettings');
            Object.keys(fields).forEach(function(key) {
                formData.append(key, fields[key]);
            });

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'include'
            }).then(function(response) {
                return response.json();
            }).then(function(data) {
                if (data.success) {
                    console.log('CustomCarrier: product settings saved');
                } else {
                    console.error('CustomCarrier: save failed', data.message);
                }
            }).catch(function(error) {
                console.error('CustomCarrier: AJAX error', error);
            });
        }

        // Hook into all PS8 save buttons
        var saveButtons = document.querySelectorAll(
            'button[type="submit"].btn-primary, ' +
            '#product_form_save_go_to_catalog_btn, ' +
            '#product_form_save_btn, ' +
            'button[name="product[save]"], ' +
            '.product-footer button[type="submit"]'
        );
        saveButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                setTimeout(saveCustomCarrierSettings, 200);
            });
        });

        // Also intercept form submit
        var form = document.querySelector('form[name="product"], #product_form, form.product-page');
        if (form) {
            form.addEventListener('submit', function() {
                saveCustomCarrierSettings();
            });
        }

        // ==========================================
        // Conflicting Options Detection
        // ==========================================
        var warningsContainer = document.getElementById('customcarrier_warnings_container');

        var warningMessages = {
            free_exclude: '{l s='Warning: "Free shipping" is blocked by "Exclude from free shipping". Product will NOT have free shipping.' mod='customcarrier' js=1}',
            qty_exclude: '{l s='Warning: "Free shipping from quantity" is blocked by "Exclude from free shipping". Quantity threshold will not work.' mod='customcarrier' js=1}',
            threshold_exclude: '{l s='Warning: "Apply cart value threshold" is blocked by "Exclude from free shipping". Threshold will not provide free shipping.' mod='customcarrier' js=1}',
            threshold_separate: '{l s='Warning: "Apply cart value threshold" is ignored when "Separate package" is enabled. Product will always be charged.' mod='customcarrier' js=1}',
            multiply_maxqty: '{l s='Info: "Multiply by quantity" is ignored when "Max quantity per package" is set. System counts packages instead of units.' mod='customcarrier' js=1}',
            multiply_no_basecost: '{l s='ERROR: "Multiply by quantity" requires "Base shipping cost" > 0. Without it, cost will always be 0!' mod='customcarrier' js=1}',
            maxqty_no_basecost: '{l s='ERROR: "Max quantity per package" requires "Base shipping cost" > 0. Without it, cost will be unpredictable!' mod='customcarrier' js=1}'
        };

        function getRadioValue(name) {
            var radios = document.querySelectorAll('input[name="' + name + '"]');
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) return radios[i].value;
            }
            return '0';
        }

        function getInputValue(id) {
            var el = document.getElementById(id);
            return el ? parseInt(el.value) || 0 : 0;
        }

        function getFloatInputValue(id) {
            var el = document.getElementById(id);
            return el ? parseFloat(el.value) || 0 : 0;
        }

        function checkConflictingOptions() {
            var warnings = [];

            var freeShipping = getRadioValue('customcarrier_free_shipping') === '1';
            var excludeFromFree = getRadioValue('customcarrier_exclude_from_free_shipping') === '1';
            var applyThreshold = getRadioValue('customcarrier_apply_threshold') === '1';
            var separatePackage = getRadioValue('customcarrier_separate_package') === '1';
            var multiplyByQty = getRadioValue('customcarrier_multiply_by_quantity') === '1';
            var freeShippingQty = getInputValue('customcarrier_free_shipping_quantity');
            var maxQtyPerPackage = getInputValue('customcarrier_max_quantity_per_package');

            // 1. free_shipping + exclude_from_free_shipping
            if (freeShipping && excludeFromFree) {
                warnings.push({ type: 'warning', msg: warningMessages.free_exclude });
            }

            // 2. free_shipping_quantity + exclude_from_free_shipping
            if (freeShippingQty > 0 && excludeFromFree) {
                warnings.push({ type: 'warning', msg: warningMessages.qty_exclude });
            }

            // 3. apply_threshold + exclude_from_free_shipping
            if (applyThreshold && excludeFromFree) {
                warnings.push({ type: 'warning', msg: warningMessages.threshold_exclude });
            }

            // 4. apply_threshold + separate_package
            if (applyThreshold && separatePackage) {
                warnings.push({ type: 'warning', msg: warningMessages.threshold_separate });
            }

            // 5. multiply_by_quantity + max_quantity_per_package (info, not error)
            if (multiplyByQty && maxQtyPerPackage > 0) {
                warnings.push({ type: 'info', msg: warningMessages.multiply_maxqty });
            }

            // 6. max_quantity_per_package > 0 but base_shipping_cost = 0 (ERROR!)
            var baseShippingCost = getFloatInputValue('customcarrier_base_shipping_cost');
            if (maxQtyPerPackage > 0 && baseShippingCost <= 0) {
                warnings.push({ type: 'danger', msg: warningMessages.maxqty_no_basecost });
            }

            // 7. multiply_by_quantity = 1 but base_shipping_cost = 0 (ERROR!)
            if (multiplyByQty && baseShippingCost <= 0) {
                warnings.push({ type: 'danger', msg: warningMessages.multiply_no_basecost });
            }

            // Render warnings
            if (warnings.length > 0) {
                var html = '';
                warnings.forEach(function(w) {
                    var alertClass = 'alert-info';
                    var icon = 'info';
                    if (w.type === 'warning') {
                        alertClass = 'alert-warning';
                        icon = 'warning';
                    } else if (w.type === 'danger') {
                        alertClass = 'alert-danger';
                        icon = 'error';
                    }
                    html += '<div class="alert ' + alertClass + '" style="margin-bottom: 10px;">';
                    html += '<i class="material-icons" style="vertical-align: middle; margin-right: 8px;">' + icon + '</i>';
                    html += w.msg;
                    html += '</div>';
                });
                warningsContainer.innerHTML = html;
                warningsContainer.style.display = 'block';
            } else {
                warningsContainer.innerHTML = '';
                warningsContainer.style.display = 'none';
            }
        }

        // Attach listeners to all relevant inputs
        var optionInputs = [
            'customcarrier_free_shipping_on',
            'customcarrier_free_shipping_off',
            'customcarrier_exclude_from_free_shipping_on',
            'customcarrier_exclude_from_free_shipping_off',
            'customcarrier_apply_threshold_on',
            'customcarrier_apply_threshold_off',
            'customcarrier_separate_package_on',
            'customcarrier_separate_package_off',
            'customcarrier_multiply_by_quantity_on',
            'customcarrier_multiply_by_quantity_off'
        ];

        optionInputs.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', checkConflictingOptions);
            }
        });

        // Also listen to number inputs
        var numberInputs = ['customcarrier_free_shipping_quantity', 'customcarrier_max_quantity_per_package', 'customcarrier_base_shipping_cost'];
        numberInputs.forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', checkConflictingOptions);
                el.addEventListener('change', checkConflictingOptions);
            }
        });

        // Initial check on page load
        checkConflictingOptions();
    });
</script>
