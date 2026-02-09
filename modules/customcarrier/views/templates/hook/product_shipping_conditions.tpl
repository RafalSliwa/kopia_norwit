{**
 * Custom Carrier - Shipping Conditions Tab
 *
 * Displays shipping cost calculation rules on product page
 *
 * Available variables:
 * - $settings: array - Product shipping settings
 * - $currency_sign: string - Currency symbol
 * - $trans: array - Translations
 *}

<div id="customcarrier-shipping-conditions" class="customcarrier-conditions-content">
    <div class="customcarrier-conditions-wrapper">
        {if isset($settings.base_shipping_cost) && $settings.base_shipping_cost > 0}
        <div class="condition-item">
            <span class="condition-label">{$trans.base_shipping_cost}</span>
            <span class="condition-value">{$settings.base_shipping_cost|string_format:"%.2f"} {$currency_sign}</span>
        </div>
        {/if}

        {if isset($settings.max_quantity_per_package) && $settings.max_quantity_per_package > 0}
        <div class="condition-item">
            <span class="condition-label">{$trans.maximum_quantity_per_package}</span>
            <span class="condition-value">{$settings.max_quantity_per_package} {$trans.pcs}</span>
        </div>

        <div class="condition-explanation">
            <h4>{$trans.how_it_works}</h4>
            <ul class="condition-list">
                <li>
                    <strong>1-{$settings.max_quantity_per_package} {$trans.pcs}:</strong>
                    {$settings.base_shipping_cost|string_format:"%.2f"} {$currency_sign} ({$trans.one_package})
                </li>
                {if isset($settings.package_cost_above_max) && $settings.package_cost_above_max > 0}
                <li>
                    <strong>{($settings.max_quantity_per_package * 2) + 1}+ {$trans.pcs}:</strong>
                    {$settings.package_cost_above_max|string_format:"%.2f"} {$currency_sign} ({$trans.large_package_pallet})
                </li>
                <li class="condition-note">
                    {$trans.for_quantities_between|sprintf:($settings.max_quantity_per_package + 1):($settings.max_quantity_per_package * 2)}
                </li>
                {else}
                <li>
                    <strong>{$settings.max_quantity_per_package + 1}+ {$trans.pcs}:</strong>
                    {$trans.multiple_packages}
                </li>
                {/if}
            </ul>
        </div>
        {/if}

        {if isset($settings.max_weight_per_package) && $settings.max_weight_per_package > 0}
        <div class="condition-item">
            <span class="condition-label">{$trans.maximum_weight_per_package}</span>
            <span class="condition-value">{$settings.max_weight_per_package|string_format:"%.2f"} kg</span>
        </div>
        {/if}

        {if isset($settings.max_packages) && $settings.max_packages > 0}
        <div class="condition-item">
            <span class="condition-label">{$trans.maximum_number_of_packages}</span>
            <span class="condition-value">{$settings.max_packages}</span>
        </div>

        {if isset($settings.cost_above_max_packages) && $settings.cost_above_max_packages > 0}
        <div class="condition-item">
            <span class="condition-label">{$trans.cost_above_max_packages}</span>
            <span class="condition-value">{$settings.cost_above_max_packages|string_format:"%.2f"} {$currency_sign} ({$trans.pallet_delivery})</span>
        </div>
        {/if}
        {/if}

        {if isset($settings.free_shipping_from_price) && $settings.free_shipping_from_price > 0}
        <div class="condition-item condition-highlight">
            <span class="condition-label">{$trans.free_shipping_from}</span>
            <span class="condition-value">{$settings.free_shipping_from_price|string_format:"%.2f"} {$currency_sign}</span>
        </div>
        {/if}

        {if isset($settings.separate_package) && $settings.separate_package == 1}
        <div class="condition-item condition-highlight">
            <span class="condition-label">{$trans.separate_package}</span>
            <span class="condition-value">{$trans.yes}</span>
        </div>
        <p class="condition-note">
            {$trans.separate_package_note}
        </p>
        {/if}

        {if isset($settings.apply_threshold) && $settings.apply_threshold == 1}
        <div class="condition-item condition-highlight">
            <span class="condition-label">{$trans.free_shipping_threshold}</span>
            <span class="condition-value">{$trans.applies}</span>
        </div>
        <p class="condition-note">
            {$trans.threshold_note}
        </p>
        {/if}
    </div>
</div>

<style>
.customcarrier-conditions-content {
    font-family: inherit;
}

.customcarrier-conditions-wrapper {
    max-width: 800px;
}

.customcarrier-conditions-wrapper h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #333;
}

.condition-item {
    padding: 12px 0;
}

.condition-label {
    font-weight: 500;
    color: #000;
}

.condition-value {
    font-weight: 600;
    color: #000;
    font-size: 1.1rem;
}

.condition-highlight {
    padding: 12px 0;
}

.condition-highlight .condition-label {
    color: #000;
}

.condition-highlight .condition-value {
    color: #000;
}

.condition-note {
    font-size: 0.9rem;
    color: #000;
    font-style: italic;
    margin-top: 8px;
}

.condition-explanation {
    margin-top: 20px;
    padding: 15px 0;
    background-color: #f8f9fa;
    border-radius: 4px;
    border-left: 4px solid #0066cc;
}

.condition-explanation h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #000;
}

.condition-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.condition-list li {
    padding: 8px 0;
    line-height: 1.6;
}

.condition-list li strong {
    color: #000;
}

.condition-list li.condition-note {
    font-size: 0.9rem;
    color: #000;
    font-style: italic;
    padding-left: 0;
}
</style>
