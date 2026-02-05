<div class="panel">
    <div class="panel-heading">
        <i class="icon-truck"></i> Edycja zbiorcza - Czas dostawy
    </div>

    <form method="get" class="form-inline" style="margin-bottom: 20px;">
        <input type="hidden" name="controller" value="AdminNorwitDelivery">
        <input type="hidden" name="token" value="{$token}">

        <div class="row">
            <div class="col-md-2">
                <label>Kategoria</label>
                <select name="filter_category" class="form-control">
                    <option value="0">Wszystkie kategorie</option>
                    {foreach from=$categories item=category}
                        <option value="{$category.id_category}" {if $filter_category == $category.id_category}selected{/if}>
                            {$category.name}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="col-md-2">
                <label>Producent</label>
                <select name="filter_manufacturer" class="form-control">
                    <option value="0">Wszyscy producenci</option>
                    {foreach from=$manufacturers item=manufacturer}
                        <option value="{$manufacturer.id_manufacturer}" {if $filter_manufacturer == $manufacturer.id_manufacturer}selected{/if}>
                            {$manufacturer.name}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="col-md-2">
                <label>Stan magazynowy</label>
                <select name="filter_out_of_stock" class="form-control">
                    <option value="0" {if $filter_out_of_stock == 0}selected{/if}>Wszystkie produkty</option>
                    <option value="1" {if $filter_out_of_stock == 1}selected{/if}>Tylko brak w magazynie</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Szukaj</label>
                <input type="text" name="filter_search" value="{$filter_search|escape:'html'}" class="form-control" placeholder="Nazwa lub indeks">
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-default form-control">
                    <i class="icon-search"></i> Filtruj
                </button>
            </div>
        </div>
    </form>

    <form method="post" action="{$current_url}">
        <input type="hidden" name="filter_category" value="{$filter_category}">
        <input type="hidden" name="filter_manufacturer" value="{$filter_manufacturer}">
        <input type="hidden" name="filter_out_of_stock" value="{$filter_out_of_stock}">
        <input type="hidden" name="filter_search" value="{$filter_search|escape:'html'}">

        <div class="alert alert-info">
            <div class="row">
                <div class="col-md-6">
                    <label>
                        <input type="checkbox" name="apply_bulk" value="1" id="apply_bulk">
                        Zastosuj ten sam tekst do wszystkich zaznaczonych:
                    </label>
                    <input type="text" name="bulk_available_later" class="form-control" placeholder="np. Dostawa 14 dni" style="margin-top: 5px;">
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" class="btn btn-default" onclick="selectAll()">
                        <i class="icon-check"></i> Zaznacz wszystkie
                    </button>
                    <button type="button" class="btn btn-default" onclick="deselectAll()">
                        <i class="icon-remove"></i> Odznacz wszystkie
                    </button>
                </div>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="30"><input type="checkbox" id="select_all" onclick="toggleAll(this)"></th>
                    <th width="60">ID</th>
                    <th>Nazwa</th>
                    <th width="120">Indeks</th>
                    <th width="80">Stan</th>
                    <th width="300">Tekst czasu dostawy</th>
                </tr>
            </thead>
            <tbody>
                {if $products}
                    {foreach from=$products item=product}
                        <tr>
                            <td>
                                <input type="checkbox" name="product_ids[]" value="{$product.id_product}" class="product-checkbox">
                            </td>
                            <td>{$product.id_product}</td>
                            <td>
                                <a href="{$link->getAdminLink('AdminProducts', true, ['id_product' => $product.id_product, 'updateproduct' => 1])}" target="_blank">
                                    {$product.name|truncate:60:'...'}
                                </a>
                            </td>
                            <td>{$product.reference|escape:'html'}</td>
                            <td class="{if $product.quantity <= 0}text-danger{else}text-success{/if}">
                                {$product.quantity|intval}
                            </td>
                            <td>
                                <input type="text" name="available_later[{$product.id_product}]" value="{$product.available_later|escape:'html'}" class="form-control input-sm">
                            </td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="6" class="text-center">Nie znaleziono produktów</td>
                    </tr>
                {/if}
            </tbody>
        </table>

        {if $products}
            <div class="panel-footer">
                <button type="submit" name="submitBulkUpdate" class="btn btn-primary">
                    <i class="icon-save"></i> Zapisz zmiany
                </button>
                <span class="text-muted" style="margin-left: 15px;">
                    Wyświetlam {$products|count} produktów
                </span>
            </div>
        {/if}
    </form>
</div>

<script>
function toggleAll(checkbox) {
    var checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
}

function selectAll() {
    var checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = true;
    });
    document.getElementById('select_all').checked = true;
}

function deselectAll() {
    var checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = false;
    });
    document.getElementById('select_all').checked = false;
}
</script>
