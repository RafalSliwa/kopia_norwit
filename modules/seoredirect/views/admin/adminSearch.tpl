{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA PL MILOSZ MYSZCZUK VATEU PL9730945634
* @copyright 2010-2024 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
<script type="text/javascript">
    var img = '';
    {if $version < 1.6}
    img = '<img src="../img/admin/delete.gif" />';
    {/if}
    {literal}
    var version = "{/literal}{$version|escape:'htmlall':'UTF-8'}{literal}";
    {/literal}
    {if $version >= 1.7}
    {literal}
    var version = "{/literal}{$version|escape:'htmlall':'UTF-8'}{literal}";
    $(document).ready(function () {
        var link = "{/literal}{Context::getContext()->link->getAdminLink('AdminSeoredirectSearch')}{literal}";
        var lang = jQuery('#lang_spy').val();
        $("#product_autocomplete_input")
            .autocomplete(
                link, {
                    minChars: 3,
                    max: 10,
                    width: 500,
                    selectFirst: false,
                    scroll: false,
                    dataType: "json",
                    formatItem: function (data, i, max, value, term) {
                        return value;
                    },
                    parse: function (data) {
                        var mytab = new Array();
                        for (var i = 0; i < data.length; i++)
                            mytab[mytab.length] = {data: data[i], value: data[i].id + ' - ' + data[i].name};
                        return mytab;
                    },
                    extraParams: {
                        forceJson: 1,
                        ajax: 1,
                        disableCombination: 1,
                        exclude_packs: 0,
                        exclude_virtuals: 0,
                        limit: 20,
                        id_lang: lang
                    }
                }
            )
            .result(function (event, data, formatted) {
                var $divAccessories = $('#addProducts');

                    var exclude = [];
                    var selected = $('#addProducts input');
                    for (var i = 0; i < selected.length; i++)
                        exclude.push(selected[i].value);
                    var ps_div = '';

                    if ($.inArray(data.id_product, exclude) == -1) {
                        ps_div = '<div id="selected_product_' + data.id + '" class="form-control-static margin-form col-lg-6"><input type="hidden" name="{/literal}{$input_array_name}{literal}[]" value="' + data.id + '" class="{/literal}{$input_array_name}{literal}[]"/><button type="button" class="btn btn-default remove-product" name="' + data.id + '" onclick="deleteProduct(' + data.id + ')">' + img + '<i class="icon-remove text-danger"></i></button>&nbsp;' + data.name + '</div>';
                        $divAccessories.show().html($divAccessories.html() + ps_div);
                    }
            });
    });
    {/literal}
    {else}
    {literal}
    $(document).ready(function () {
        var link = "{/literal}{$linkk->getPageLink('search')|escape:'htmlall':'UTF-8'}{literal}";
        var lang = jQuery('#lang_spy').val();
        $("#product_autocomplete_input")
            .autocomplete(
                link, {
                    minChars: 3,
                    max: 10,
                    width: 500,
                    selectFirst: false,
                    scroll: false,
                    dataType: "json",
                    formatItem: function (data, i, max, value, term) {
                        return value;
                    },
                    parse: function (data) {
                        var mytab = new Array();
                        for (var i = 0; i < data.length; i++)
                            mytab[mytab.length] = {data: data[i], value: data[i].id_product + ' - ' + data[i].pname};
                        return mytab;
                    },
                    extraParams: {
                        ajaxSearch: 1,
                        id_lang: lang
                    }
                }
            )
            .result(function (event, data, formatted) {
                var $divAccessories = $('#addProducts');
                if (data.id_product.length > 0 && data.pname.length > 0) {
                    var exclude = [];
                    var selected = $('.masspc_products');
                    for (var i = 0; i < selected.length; i++)
                        exclude.push(selected[i].value);
                    var ps_div = '';

                    if ($.inArray(data.id_product, exclude) == -1) {
                        ps_div = '<div id="selected_product_' + data.id_product + '" class="form-control-static margin-form col-lg-6"><input type="hidden" name="{/literal}{$input_array_name}{literal}[]" value="' + data.id_product + '" class="{/literal}{$input_array_name}{literal}"/><button type="button" class="btn btn-default remove-product" name="' + data.id_product + '" onclick="deleteProduct(' + data.id_product + ')">' + img + '<i class="icon-remove text-danger"></i></button>&nbsp;' + data.pname + '</div>';
                        $divAccessories.show().html($divAccessories.html() + ps_div);
                    }

                }
            });
    });
    {/literal}
    {/if}
    {literal}
    function deleteProduct(id) {
        $("#selected_product_" + id).remove();
    }
    {/literal}
</script>


<div class="col-lg-9">
    <div id="ajax_choose_product" class="clearfix">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="input-group">
                <input id="product_autocomplete_input" name="" type="text" class="text ac_input" value=""/>
                <input id="lang_spy" type="hidden" value="{$id_langg}"/>
                <span class="input-group-addon"><i class="icon-search"></i></span>
            </div>
            <div id="addProducts">
                {if $products != false}
                    {foreach $products AS $product}
                        <div id="selected_product_{$product->id}" class="form-control-static margin-form col-lg-6"><input type="hidden" name="seor_pos_exclude[]" value="{$product->id}" class="seor_pos_exclude">
                            <button type="button" class="btn btn-default remove-product" name="{$product->id}" onclick="deleteProduct({$product->id})"><i class="icon-remove text-danger"></i></button>&nbsp;{$product->name}</div>
                    {/foreach}
                {/if}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="alert alert-warning">
                {l d='Modules.Seoredirect.Adminsearch' s='Module will not disable / redirect defined products when product will be out of stock' mod='seoredirect'}
            </div>
            <div class="alert alert-info">
                {l d='Modules.Seoredirect.Adminsearch' s='Type the name of product and select it from the list, it will be added to list of exclusions.' mod='seoredirect'}<br/>
                {l d='Modules.Seoredirect.Adminsearch' s='If you dont see the search results, try to add missing products to catalog or rebuild search index:' mod='seoredirect'} <strong><a href="{Context::getContext()->link->getAdminLink('AdminSearchConf')}" target="_blank">{l d='Modules.Seoredirect.Adminsearch' s='search settings' mod='seoredirect'}</a></strong>
            </div>
        </div>
    </div>
</div>