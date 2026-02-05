<div class="panel">
    <div class="panel-heading">
        <i class="icon-truck"></i> {l s='Bulk Shipping Settings' mod='customcarrier'}
        <span class="badge">{$total_products}</span>
    </div>

    {* Success/Error messages *}
    {if !empty($success_messages)}
        {foreach from=$success_messages item=msg}
            <div class="alert alert-success">{$msg}</div>
        {/foreach}
    {/if}
    {if !empty($error_messages)}
        {foreach from=$error_messages item=msg}
            <div class="alert alert-danger">{$msg}</div>
        {/foreach}
    {/if}

    {* ========== FILTER FORM (GET) ========== *}
    <form method="get" class="form-inline bulk-filter-form" id="filterForm">
        <input type="hidden" name="controller" value="AdminCustomCarrierBulk">
        <input type="hidden" name="token" value="{$bulk_token}">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label>{l s='Manufacturer' mod='customcarrier'}</label>
                    <div class="autocomplete-wrapper" style="position:relative;">
                        <input type="hidden" name="filter_manufacturer" id="filterManufacturerValue"
                               value="{$filter_manufacturer}">
                        <input type="text" id="filterManufacturerInput"
                               class="form-control" style="width: 100%;"
                               placeholder="{l s='All manufacturers' mod='customcarrier'}"
                               value="{if $filter_manufacturer > 0}{foreach from=$manufacturers item=mfr}{if $mfr.id_manufacturer == $filter_manufacturer}{$mfr.name|escape:'html'}{/if}{/foreach}{/if}"
                               autocomplete="off">
                        <div id="manufacturerSuggestions" class="autocomplete-suggestions"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <label>{l s='Category' mod='customcarrier'}</label>
                    <div class="autocomplete-wrapper" style="position:relative;">
                        <input type="hidden" name="filter_category" id="filterCategoryValue"
                               value="{$filter_category}">
                        <input type="text" id="filterCategoryInput"
                               class="form-control" style="width: 100%;"
                               placeholder="{l s='All categories' mod='customcarrier'}"
                               value="{if $filter_category > 0}{foreach from=$categories item=cat}{if $cat.id_category == $filter_category}{$cat.name|escape:'html'}{/if}{/foreach}{/if}"
                               autocomplete="off">
                        <div id="categorySuggestions" class="autocomplete-suggestions"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label>{l s='Shipping settings' mod='customcarrier'}</label>
                    <select name="filter_has_settings" class="form-control" style="width: 100%;">
                        <option value="-1">{l s='All products' mod='customcarrier'}</option>
                        <option value="1" {if $filter_has_settings == 1}selected{/if}>
                            {l s='With settings' mod='customcarrier'}
                        </option>
                        <option value="0" {if $filter_has_settings == 0}selected{/if}>
                            {l s='Without settings' mod='customcarrier'}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label>{l s='Search' mod='customcarrier'}</label>
                    <input type="text" name="filter_search"
                           value="{$filter_search|escape:'html'}"
                           class="form-control" style="width: 100%;"
                           placeholder="{l s='Name or reference' mod='customcarrier'}">
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-default">
                            <i class="icon-search"></i> {l s='Filter' mod='customcarrier'}
                        </button>
                        <a href="{$current_url}" class="btn btn-default" title="{l s='Reset' mod='customcarrier'}">
                            <i class="icon-eraser"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <hr>

    {* ========== BULK UPDATE FORM (POST) ========== *}
    <form method="post" action="{$current_url}" id="bulkForm">
        <input type="hidden" name="token" value="{$bulk_token}">
        {* Preserve filters *}
        <input type="hidden" name="filter_category" value="{$filter_category}">
        <input type="hidden" name="filter_manufacturer" value="{$filter_manufacturer}">
        <input type="hidden" name="filter_search" value="{$filter_search|escape:'html'}">
        <input type="hidden" name="filter_has_settings" value="{$filter_has_settings}">
        <input type="hidden" name="page" value="{$current_page}">

        {* ========== WARNINGS CONTAINER ========== *}
        <div id="bulk_warnings_container" style="display:none;"></div>

        {* ========== BULK SETTINGS PANEL ========== *}
        <div class="panel panel-default" id="bulk-settings-panel">
            <div class="panel-heading" style="cursor:pointer;" onclick="toggleBulkPanel()">
                <i class="icon-cogs"></i> {l s='Bulk shipping values' mod='customcarrier'}
                <small class="text-muted">
                    — {l s='Check which fields to apply, then set their values' mod='customcarrier'}
                </small>
                <span class="pull-right"><i class="icon-chevron-down" id="bulk-panel-icon"></i></span>
            </div>
            <div class="panel-body" id="bulk-panel-body">
                <div class="row">
                    <div class="col-md-6">
                        {* Free shipping *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="free_shipping"
                                       class="bulk-field-check" data-target="bulk_free_shipping">
                            </div>
                            <label class="col-xs-5">{l s='Free shipping' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <select name="bulk_free_shipping" class="form-control input-sm" disabled>
                                    <option value="0">{l s='No' mod='customcarrier'}</option>
                                    <option value="1">{l s='Yes' mod='customcarrier'}</option>
                                </select>
                            </div>
                        </div>

                        {* Base shipping cost *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="base_shipping_cost"
                                       class="bulk-field-check" data-target="bulk_base_shipping_cost">
                            </div>
                            <label class="col-xs-5">{l s='Base shipping cost' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="bulk_base_shipping_cost"
                                           class="form-control" value="0" min="0" step="0.01" disabled>
                                    <span class="input-group-addon">{$currency_sign}</span>
                                </div>
                            </div>
                        </div>

                        {* Multiply by quantity *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="multiply_by_quantity"
                                       class="bulk-field-check" data-target="bulk_multiply_by_quantity">
                            </div>
                            <label class="col-xs-5">{l s='Multiply by quantity' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <select name="bulk_multiply_by_quantity" class="form-control input-sm" disabled>
                                    <option value="0">{l s='No' mod='customcarrier'}</option>
                                    <option value="1">{l s='Yes' mod='customcarrier'}</option>
                                </select>
                            </div>
                        </div>

                        {* Free shipping quantity threshold *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="free_shipping_quantity"
                                       class="bulk-field-check" data-target="bulk_free_shipping_quantity">
                            </div>
                            <label class="col-xs-5">{l s='Free shipping from qty' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="bulk_free_shipping_quantity"
                                           class="form-control" value="0" min="0" step="1" disabled>
                                    <span class="input-group-addon">{l s='pcs' mod='customcarrier'}</span>
                                </div>
                            </div>
                        </div>

                        {* Free shipping from product price *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="free_shipping_from_price"
                                       class="bulk-field-check" data-target="bulk_free_shipping_from_price">
                            </div>
                            <label class="col-xs-5">{l s='Free shipping from product price' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="bulk_free_shipping_from_price"
                                           class="form-control" value="" min="0" step="0.01" placeholder="0" disabled>
                                    <span class="input-group-addon">{$currency_sign}</span>
                                </div>
                            </div>
                        </div>

                        {* Apply cart value threshold (from carrier wizard) *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="apply_threshold"
                                       class="bulk-field-check" data-target="bulk_apply_threshold">
                            </div>
                            <label class="col-xs-5">{l s='Apply cart value threshold' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <select name="bulk_apply_threshold" class="form-control input-sm" disabled>
                                    <option value="0">{l s='No' mod='customcarrier'}</option>
                                    <option value="1">{l s='Yes' mod='customcarrier'}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        {* Separate package *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="separate_package"
                                       class="bulk-field-check" data-target="bulk_separate_package">
                            </div>
                            <label class="col-xs-5">{l s='Separate package' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <select name="bulk_separate_package" class="form-control input-sm" disabled>
                                    <option value="0">{l s='No' mod='customcarrier'}</option>
                                    <option value="1">{l s='Yes' mod='customcarrier'}</option>
                                </select>
                            </div>
                        </div>

                        {* Exclude from free shipping *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="exclude_from_free_shipping"
                                       class="bulk-field-check" data-target="bulk_exclude_from_free_shipping">
                            </div>
                            <label class="col-xs-5">{l s='Exclude from free shipping' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <select name="bulk_exclude_from_free_shipping" class="form-control input-sm" disabled>
                                    <option value="0">{l s='No' mod='customcarrier'}</option>
                                    <option value="1">{l s='Yes' mod='customcarrier'}</option>
                                </select>
                            </div>
                        </div>

                        {* Max quantity per package *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="max_quantity_per_package"
                                       class="bulk-field-check" data-target="bulk_max_quantity_per_package">
                            </div>
                            <label class="col-xs-5">{l s='Max qty per package' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="bulk_max_quantity_per_package"
                                           class="form-control" value="" min="0" step="1" placeholder="0" disabled>
                                    <span class="input-group-addon">{l s='pcs' mod='customcarrier'}</span>
                                </div>
                            </div>
                        </div>

                        {* Max weight per package *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="max_weight_per_package"
                                       class="bulk-field-check" data-target="bulk_max_weight_per_package">
                            </div>
                            <label class="col-xs-5">{l s='Max weight per package' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="bulk_max_weight_per_package"
                                           class="form-control" value="" min="0" step="0.01" placeholder="0" disabled>
                                    <span class="input-group-addon">{Configuration::get('PS_WEIGHT_UNIT')|default:'kg'}</span>
                                </div>
                            </div>
                        </div>

                        {* Max packages *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="max_packages"
                                       class="bulk-field-check" data-target="bulk_max_packages">
                            </div>
                            <label class="col-xs-5">{l s='Max packages' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="bulk_max_packages"
                                           class="form-control" value="" min="0" step="1" placeholder="0" disabled>
                                    <span class="input-group-addon">{l s='packages' mod='customcarrier'}</span>
                                </div>
                            </div>
                        </div>

                        {* Cost above max packages *}
                        <div class="form-group row bulk-field-row">
                            <div class="col-xs-1">
                                <input type="checkbox" name="bulk_fields[]" value="cost_above_max_packages"
                                       class="bulk-field-check" data-target="bulk_cost_above_max_packages">
                            </div>
                            <label class="col-xs-5">{l s='Cost when exceeding max packages' mod='customcarrier'}</label>
                            <div class="col-xs-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="bulk_cost_above_max_packages"
                                           class="form-control" value="" min="0" step="0.01" placeholder="0" disabled>
                                    <span class="input-group-addon">{$currency_sign}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {* ========== ACTION BAR ========== *}
        <div class="bulk-action-bar">
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="btn btn-default btn-sm" onclick="bulkSelectAll()">
                        <i class="icon-check-sign"></i> {l s='Select all' mod='customcarrier'}
                    </button>
                    <button type="button" class="btn btn-default btn-sm" onclick="bulkDeselectAll()">
                        <i class="icon-check-empty"></i> {l s='Deselect all' mod='customcarrier'}
                    </button>
                    <span class="text-muted bulk-count-info">
                        <span id="selectedCount">0</span> {l s='selected' mod='customcarrier'}
                    </span>
                </div>
                <div class="col-md-6 text-right">
                    <button type="submit" name="submitBulkShipping" class="btn btn-primary"
                            onclick="return confirmBulkUpdate();">
                        <i class="icon-save"></i> {l s='Apply to selected products' mod='customcarrier'}
                    </button>
                </div>
            </div>
        </div>

        {* ========== PRODUCT TABLE ========== *}
        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed" id="bulkProductTable">
                <thead>
                    <tr>
                        <th width="30">
                            <input type="checkbox" id="selectAllCheckbox" onclick="toggleAllProducts(this)">
                        </th>
                        <th width="60">ID</th>
                        <th>{l s='Product' mod='customcarrier'}</th>
                        <th width="100">{l s='Reference' mod='customcarrier'}</th>
                        <th width="130">{l s='Manufacturer' mod='customcarrier'}</th>
                        <th width="90" class="text-right">{l s='Price' mod='customcarrier'}</th>
                        <th width="130">{l s='Category' mod='customcarrier'}</th>
                        <th width="70" class="text-center">
                            {l s='Free shipping' mod='customcarrier'}
                        </th>
                        <th width="80" class="text-right">
                            {l s='Cost' mod='customcarrier'}
                        </th>
                        <th width="60" class="text-center">
                            {l s='×Qty' mod='customcarrier'}
                        </th>
                        <th width="60" class="text-center">
                            {l s='Threshold' mod='customcarrier'}
                        </th>
                        <th width="70" class="text-center">
                            {l s='Separate' mod='customcarrier'}
                        </th>
                        <th width="70" class="text-center">
                            {l s='Exclude' mod='customcarrier'}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$products item=product}
                    <tr class="{if !$product.has_settings}text-muted{/if}">
                        <td>
                            <input type="checkbox" name="product_ids[]"
                                   value="{$product.id_product}" class="product-checkbox"
                                   onchange="updateSelectedCount()">
                        </td>
                        <td>{$product.id_product}</td>
                        <td>
                            <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product.id_product, 'updateproduct' => 1])}"
                               target="_blank" title="{$product.product_name|escape:'html'}">
                                {$product.product_name|truncate:60:'...'|escape:'html'}
                            </a>
                        </td>
                        <td>{$product.reference|escape:'html'}</td>
                        <td>{$product.manufacturer_name|escape:'html'}</td>
                        <td class="text-right">
                            {if $product.product_price_gross}
                                {$product.product_price_gross|string_format:"%.2f"} {$currency_sign}
                            {else}
                                <span class="text-muted">—</span>
                            {/if}
                        </td>
                        <td>{$product.category_name|escape:'html'}</td>
                        <td class="text-center">
                            {if $product.free_shipping}
                                <span class="label label-success"><i class="icon-check"></i></span>
                            {/if}
                        </td>
                        <td class="text-right">
                            {if $product.has_settings}
                                {$product.base_shipping_cost|string_format:"%.2f"} {$currency_sign}
                            {else}
                                <span class="text-muted">—</span>
                            {/if}
                        </td>
                        <td class="text-center">
                            {if $product.multiply_by_quantity}
                                <span class="label label-info"><i class="icon-check"></i></span>
                            {/if}
                        </td>
                        <td class="text-center">
                            {if $product.apply_threshold}
                                <span class="label label-warning"><i class="icon-check"></i></span>
                            {/if}
                        </td>
                        <td class="text-center">
                            {if $product.separate_package}
                                <span class="label label-danger"><i class="icon-check"></i></span>
                            {/if}
                        </td>
                        <td class="text-center">
                            {if $product.exclude_from_free_shipping}
                                <span class="label label-danger"><i class="icon-check"></i></span>
                            {/if}
                        </td>
                    </tr>
                    {foreachelse}
                    <tr>
                        <td colspan="13" class="text-center">
                            <p class="alert alert-warning">
                                {l s='No products found' mod='customcarrier'}
                            </p>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

        {* ========== PAGINATION ========== *}
        {if $total_pages > 1}
        <div class="text-center">
            <ul class="pagination">
                {if $current_page > 1}
                    <li>
                        <a href="{$current_url}&filter_category={$filter_category}&filter_manufacturer={$filter_manufacturer}&filter_search={$filter_search|escape:'url'}&filter_has_settings={$filter_has_settings}&per_page={$per_page}&page={$current_page - 1}">&laquo;</a>
                    </li>
                {/if}
                {assign var=startPage value=max(1, $current_page - 5)}
                {assign var=endPage value=min($total_pages, $current_page + 5)}
                {for $p=$startPage to $endPage}
                    <li {if $p == $current_page}class="active"{/if}>
                        <a href="{$current_url}&filter_category={$filter_category}&filter_manufacturer={$filter_manufacturer}&filter_search={$filter_search|escape:'url'}&filter_has_settings={$filter_has_settings}&per_page={$per_page}&page={$p}">{$p}</a>
                    </li>
                {/for}
                {if $current_page < $total_pages}
                    <li>
                        <a href="{$current_url}&filter_category={$filter_category}&filter_manufacturer={$filter_manufacturer}&filter_search={$filter_search|escape:'url'}&filter_has_settings={$filter_has_settings}&per_page={$per_page}&page={$current_page + 1}">&raquo;</a>
                    </li>
                {/if}
            </ul>
        </div>
        {/if}

        <div class="panel-footer text-muted">
            <div class="row">
                <div class="col-md-6">
                    {l s='Showing' mod='customcarrier'} {$products|count}
                    {l s='of' mod='customcarrier'} {$total_products}
                    {l s='products' mod='customcarrier'}
                    {if $total_pages > 1}
                        — {l s='Page' mod='customcarrier'} {$current_page}/{$total_pages}
                    {/if}
                </div>
                <div class="col-md-6 text-right">
                    <label style="display: inline-block; margin-right: 5px;">{l s='Products per page:' mod='customcarrier'}</label>
                    <select id="perPageSelect" class="form-control" style="width: auto; display: inline-block;">
                        <option value="50" {if $per_page == 50}selected{/if}>50</option>
                        <option value="100" {if $per_page == 100}selected{/if}>100</option>
                        <option value="200" {if $per_page == 200}selected{/if}>200</option>
                        <option value="500" {if $per_page == 500}selected{/if}>500</option>
                        <option value="1000" {if $per_page == 1000}selected{/if}>1000</option>
                        <option value="0" {if $per_page == 0}selected{/if}>{l s='All' mod='customcarrier'}</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    // Toggle bulk settings panel
    function toggleBulkPanel() {
        var body = document.getElementById('bulk-panel-body');
        var icon = document.getElementById('bulk-panel-icon');
        if (body.style.display === 'none') {
            body.style.display = 'block';
            icon.className = 'icon-chevron-down';
        } else {
            body.style.display = 'none';
            icon.className = 'icon-chevron-right';
        }
    }

    // Enable/disable bulk field inputs based on checkbox
    document.querySelectorAll('.bulk-field-check').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var row = this.closest('.bulk-field-row');
            var inputs = row.querySelectorAll('input[type="number"], select:not([name="bulk_fields[]"])');
            inputs.forEach(function(input) {
                input.disabled = !checkbox.checked;
            });
        });
    });

    // Select all products
    function bulkSelectAll() {
        document.querySelectorAll('.product-checkbox').forEach(function(cb) {
            cb.checked = true;
        });
        document.getElementById('selectAllCheckbox').checked = true;
        updateSelectedCount();
    }

    // Deselect all products
    function bulkDeselectAll() {
        document.querySelectorAll('.product-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        updateSelectedCount();
    }

    // Toggle all products via header checkbox
    function toggleAllProducts(source) {
        document.querySelectorAll('.product-checkbox').forEach(function(cb) {
            cb.checked = source.checked;
        });
        updateSelectedCount();
    }

    // Update selected count display
    function updateSelectedCount() {
        var count = document.querySelectorAll('.product-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = count;
    }

    // Confirm before bulk update
    function confirmBulkUpdate() {
        var selectedProducts = document.querySelectorAll('.product-checkbox:checked').length;
        var selectedFields = document.querySelectorAll('.bulk-field-check:checked').length;

        if (selectedProducts === 0) {
            alert('{l s="No products selected." mod="customcarrier" js=1}');
            return false;
        }
        if (selectedFields === 0) {
            alert('{l s="No shipping fields selected to apply." mod="customcarrier" js=1}');
            return false;
        }

        return confirm(
            '{l s="Are you sure you want to update shipping settings for" mod="customcarrier" js=1} '
            + selectedProducts
            + ' {l s="products" mod="customcarrier" js=1}?'
        );
    }

    // Initial count
    updateSelectedCount();

    // ========== Conflicting Options Detection ==========
    var bulkWarningsContainer = document.getElementById('bulk_warnings_container');

    var bulkWarningMessages = {
        free_exclude: '{l s='Warning: "Free shipping" is blocked by "Exclude from free shipping". Products will NOT have free shipping.' mod='customcarrier' js=1}',
        qty_exclude: '{l s='Warning: "Free shipping from quantity" is blocked by "Exclude from free shipping". Quantity threshold will not work.' mod='customcarrier' js=1}',
        price_exclude: '{l s='Warning: "Free shipping from product price" is blocked by "Exclude from free shipping". Price threshold will not work.' mod='customcarrier' js=1}',
        threshold_exclude: '{l s='Warning: "Apply cart value threshold" is blocked by "Exclude from free shipping". Threshold will not provide free shipping.' mod='customcarrier' js=1}',
        threshold_separate: '{l s='Warning: "Apply cart value threshold" is ignored when "Separate package" is enabled. Products will always be charged.' mod='customcarrier' js=1}',
        multiply_maxqty: '{l s='Info: "Multiply by quantity" is ignored when "Max qty per package" is set. System counts packages instead of units.' mod='customcarrier' js=1}',
        multiply_no_basecost: '{l s='ERROR: "Multiply by quantity" requires "Base shipping cost" > 0. Without it, cost will always be 0!' mod='customcarrier' js=1}',
        maxqty_no_basecost: '{l s='ERROR: "Max qty per package" requires "Base shipping cost" > 0. Without it, cost will be unpredictable!' mod='customcarrier' js=1}'
    };

    function isBulkFieldChecked(fieldName) {
        var checkbox = document.querySelector('input[name="bulk_fields[]"][value="' + fieldName + '"]');
        return checkbox && checkbox.checked;
    }

    function getBulkSelectValue(selectName) {
        var select = document.querySelector('select[name="' + selectName + '"]');
        return select ? select.value : '0';
    }

    function getBulkInputValue(inputName) {
        var input = document.querySelector('input[name="' + inputName + '"]');
        return input ? parseInt(input.value) || 0 : 0;
    }

    function getBulkFloatValue(inputName) {
        var input = document.querySelector('input[name="' + inputName + '"]');
        return input ? parseFloat(input.value) || 0 : 0;
    }

    function checkBulkConflicts() {
        var warnings = [];

        // Get which fields are checked and their values
        var freeShippingChecked = isBulkFieldChecked('free_shipping');
        var freeShippingValue = getBulkSelectValue('bulk_free_shipping') === '1';

        var excludeChecked = isBulkFieldChecked('exclude_from_free_shipping');
        var excludeValue = getBulkSelectValue('bulk_exclude_from_free_shipping') === '1';

        var thresholdChecked = isBulkFieldChecked('apply_threshold');
        var thresholdValue = getBulkSelectValue('bulk_apply_threshold') === '1';

        var separateChecked = isBulkFieldChecked('separate_package');
        var separateValue = getBulkSelectValue('bulk_separate_package') === '1';

        var multiplyChecked = isBulkFieldChecked('multiply_by_quantity');
        var multiplyValue = getBulkSelectValue('bulk_multiply_by_quantity') === '1';

        var freeQtyChecked = isBulkFieldChecked('free_shipping_quantity');
        var freeQtyValue = getBulkInputValue('bulk_free_shipping_quantity');

        var maxQtyChecked = isBulkFieldChecked('max_quantity_per_package');
        var maxQtyValue = getBulkInputValue('bulk_max_quantity_per_package');

        // 1. free_shipping=YES + exclude=YES
        if (freeShippingChecked && freeShippingValue && excludeChecked && excludeValue) {
            warnings.push({ type: 'warning', msg: bulkWarningMessages.free_exclude });
        }

        // 2. free_shipping_quantity>0 + exclude=YES
        if (freeQtyChecked && freeQtyValue > 0 && excludeChecked && excludeValue) {
            warnings.push({ type: 'warning', msg: bulkWarningMessages.qty_exclude });
        }

        // 2b. free_shipping_from_price>0 + exclude=YES
        var freePriceChecked = isBulkFieldChecked('free_shipping_from_price');
        var freePriceValue = getBulkFloatValue('bulk_free_shipping_from_price');
        if (freePriceChecked && freePriceValue > 0 && excludeChecked && excludeValue) {
            warnings.push({ type: 'warning', msg: bulkWarningMessages.price_exclude });
        }

        // 3. apply_threshold=YES + exclude=YES
        if (thresholdChecked && thresholdValue && excludeChecked && excludeValue) {
            warnings.push({ type: 'warning', msg: bulkWarningMessages.threshold_exclude });
        }

        // 4. apply_threshold=YES + separate_package=YES
        if (thresholdChecked && thresholdValue && separateChecked && separateValue) {
            warnings.push({ type: 'warning', msg: bulkWarningMessages.threshold_separate });
        }

        // 5. multiply_by_quantity=YES + max_quantity_per_package>0
        if (multiplyChecked && multiplyValue && maxQtyChecked && maxQtyValue > 0) {
            warnings.push({ type: 'info', msg: bulkWarningMessages.multiply_maxqty });
        }

        // 6. max_quantity_per_package > 0 but base_shipping_cost = 0 (ERROR!)
        var baseCostChecked = isBulkFieldChecked('base_shipping_cost');
        var baseCostValue = getBulkFloatValue('bulk_base_shipping_cost');
        if (maxQtyChecked && maxQtyValue > 0 && baseCostChecked && baseCostValue <= 0) {
            warnings.push({ type: 'danger', msg: bulkWarningMessages.maxqty_no_basecost });
        }

        // 7. multiply_by_quantity = YES but base_shipping_cost = 0 (ERROR!)
        if (multiplyChecked && multiplyValue && baseCostChecked && baseCostValue <= 0) {
            warnings.push({ type: 'danger', msg: bulkWarningMessages.multiply_no_basecost });
        }

        // Render warnings
        if (warnings.length > 0) {
            var html = '';
            warnings.forEach(function(w) {
                var alertClass = 'alert-info';
                var icon = 'icon-info-sign';
                if (w.type === 'warning') {
                    alertClass = 'alert-warning';
                    icon = 'icon-warning-sign';
                } else if (w.type === 'danger') {
                    alertClass = 'alert-danger';
                    icon = 'icon-exclamation-sign';
                }
                html += '<div class="alert ' + alertClass + '" style="margin-bottom: 10px;">';
                html += '<i class="' + icon + '" style="margin-right: 8px;"></i>';
                html += w.msg;
                html += '</div>';
            });
            bulkWarningsContainer.innerHTML = html;
            bulkWarningsContainer.style.display = 'block';
        } else {
            bulkWarningsContainer.innerHTML = '';
            bulkWarningsContainer.style.display = 'none';
        }
    }

    // Attach listeners to bulk field checkboxes and their inputs
    document.querySelectorAll('.bulk-field-check').forEach(function(checkbox) {
        checkbox.addEventListener('change', checkBulkConflicts);
    });

    document.querySelectorAll('#bulk-panel-body select, #bulk-panel-body input[type="number"]').forEach(function(input) {
        input.addEventListener('change', checkBulkConflicts);
        input.addEventListener('input', checkBulkConflicts);
    });

    // Initial check
    checkBulkConflicts();

    // ========== Reusable Autocomplete ==========
    function initAutocomplete(config) {
        var input = document.getElementById(config.inputId);
        var hiddenInput = document.getElementById(config.hiddenId);
        var suggestionsBox = document.getElementById(config.suggestionsId);
        var selectedIndex = -1;

        function showSuggestions(filter) {
            var html = '';
            var query = filter.toLowerCase();
            var matches = [];

            if (query === '' || config.allLabel.toLowerCase().indexOf(query) !== -1) {
                matches.push({ldelim}id: 0, name: config.allLabel{rdelim});
            }

            for (var i = 0; i < config.items.length; i++) {
                if (query === '' || config.items[i].name.toLowerCase().indexOf(query) !== -1) {
                    matches.push(config.items[i]);
                }
            }

            if (matches.length === 0) {
                suggestionsBox.style.display = 'none';
                return;
            }

            for (var j = 0; j < matches.length; j++) {
                var highlighted = matches[j].name;
                if (query.length > 0) {
                    var regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                    highlighted = highlighted.replace(regex, '<strong>$1</strong>');
                }
                html += '<div class="autocomplete-item" data-id="' + matches[j].id + '" data-name="' + matches[j].name.replace(/"/g, '&quot;') + '">'
                      + highlighted + '</div>';
            }

            suggestionsBox.innerHTML = html;
            suggestionsBox.style.display = 'block';
            selectedIndex = -1;

            suggestionsBox.querySelectorAll('.autocomplete-item').forEach(function(item) {
                item.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    hiddenInput.value = this.getAttribute('data-id');
                    input.value = (this.getAttribute('data-id') == 0) ? '' : this.getAttribute('data-name');
                    suggestionsBox.style.display = 'none';
                });
            });
        }

        input.addEventListener('input', function() {
            showSuggestions(this.value);
            hiddenInput.value = '0';
        });

        input.addEventListener('focus', function() {
            showSuggestions(this.value);
        });

        input.addEventListener('blur', function() {
            setTimeout(function() {
                suggestionsBox.style.display = 'none';
            }, 200);
        });

        input.addEventListener('keydown', function(e) {
            var items = suggestionsBox.querySelectorAll('.autocomplete-item');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateHL(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, 0);
                updateHL(items);
            } else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                items[selectedIndex].dispatchEvent(new Event('mousedown'));
            } else if (e.key === 'Escape') {
                suggestionsBox.style.display = 'none';
            }
        });

        function updateHL(items) {
            items.forEach(function(item, i) {
                item.classList.toggle('active', i === selectedIndex);
            });
            if (selectedIndex >= 0 && items[selectedIndex]) {
                items[selectedIndex].scrollIntoView({ldelim}block: 'nearest'{rdelim});
            }
        }
    }

    // Manufacturer autocomplete
    initAutocomplete({ldelim}
        inputId: 'filterManufacturerInput',
        hiddenId: 'filterManufacturerValue',
        suggestionsId: 'manufacturerSuggestions',
        allLabel: '{l s="All manufacturers" mod="customcarrier" js=1}',
        items: [
            {foreach from=$manufacturers item=manufacturer name=mfrloop}
                {ldelim}id: {$manufacturer.id_manufacturer}, name: '{$manufacturer.name|escape:"javascript"}'{rdelim}{if !$smarty.foreach.mfrloop.last},{/if}
            {/foreach}
        ]
    {rdelim});

    // Category autocomplete
    initAutocomplete({ldelim}
        inputId: 'filterCategoryInput',
        hiddenId: 'filterCategoryValue',
        suggestionsId: 'categorySuggestions',
        allLabel: '{l s="All categories" mod="customcarrier" js=1}',
        items: [
            {foreach from=$categories item=category name=catloop}
                {ldelim}id: {$category.id_category}, name: '{$category.name|escape:"javascript"}'{rdelim}{if !$smarty.foreach.catloop.last},{/if}
            {/foreach}
        ]
    {rdelim});

    // Handle per-page dropdown change
    document.getElementById('perPageSelect').addEventListener('change', function() {
        var perPage = this.value;
        var url = '{$current_url}'
            + '&filter_category={$filter_category}'
            + '&filter_manufacturer={$filter_manufacturer}'
            + '&filter_search={$filter_search|escape:"javascript"}'
            + '&filter_has_settings={$filter_has_settings}'
            + '&per_page=' + perPage
            + '&page=1'; // Reset to page 1 when changing per-page limit
        window.location.href = url;
    });
</script>
