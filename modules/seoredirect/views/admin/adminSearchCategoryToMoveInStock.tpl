<script>
    var currentToken = '{$currentToken}';
    {literal}
    $(document).ready(function () {
        $('.remove_category_to_move_instock').click(function(){
            $(this).parent().html('{/literal}{l d='Modules.Seoredirect.Adminsearchcategorytomoveinstock' s='Category:' mod='seoredirect'}{literal} ');
            $('.categoryBoxDefInStock').val('');
        });
        $('#seor_pins').on('change', function() {
            if (this.value == 1 || this.value == 2) {
                $('#seor_pins_category').show();
                if (this.value == 2) {
                    $('#seor_pins_category_info_default').show();
                } else {
                    $('#seor_pins_category_info_default').hide();
                }
            } else {
                $('#seor_pins_category').hide();
                $('#seor_pins_category_info_default').hide();
            }
        });
        $('#category_autocomplete_input_move_instock')
            .autocomplete(
                {/literal}'{Context::getContext()->link->getAdminLink('AdminSeoRedirectSettings')}'{literal}, {
                    minChars: 2,
                    max: 50,
                    width: 500,
                    selectFirst: false,
                    scroll: false,
                    dataType: 'json',
                    formatItem: function (data, i, max, value, term) {
                        return value;
                    },
                    parse: function (data) {
                        var mytab = new Array();
                        for (var i = 0; i < data.length; i++)
                            mytab[mytab.length] = {data: data[i], value: data[i].name + ' (ID #' + data[i].id_category + ')'};
                        return mytab;
                    },
                    extraParams: {
                        controller: 'AdminSeoRedirectSettings',
                        token: currentToken,
                        categoriesFilter: 1,
                        ajax: 1
                    }
                }
            )
            .result(function (event, data, formatted) {
                $('.selected_category_to_move_instock').html(data.name);
                $('.categoryBoxDefInStock').val(data.id_category);
            });
        $('#seor_pins').change();
    });
    {/literal}
</script>

<div class="row">
    <div class="clearfix" style="font-style: normal!important;">
        <div class="col-lg-3">
            <div class="alert alert-info">
                <strong>{l d='Modules.Seoredirect.Adminsearchcategorytomoveinstock' s='Search for category' mod='seoredirect'}</strong><br/>
                {l d='Modules.Seoredirect.Adminsearchcategorytomoveinstock' s='Module will assign/unassign product to/from this category depending on selected option above' mod='seoredirect'}
            </div>
            <div class="alert alert-warning" id="seor_pins_category_info_default">
                {l d='Modules.Seoredirect.Adminsearchcategorytomoveinstock' s='If selected category will be a default product category - module will not unassign product from this category. Product must have default category association.' mod='seoredirect'}
            </div>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-9">
            <div class="input-group">
                <span class="input-group-addon selected_category_to_move_instock">{if Configuration::get('seor_pins_category') != false}{$category_to_move_instock->name} <button type="button" class="btn btn-default remove_category_to_move_instock" style="padding: 0px 5px; font-size: 10px;">{l d='Modules.Seoredirect.Adminsearchcategorytomoveinstock' s='remove' mod='seoredirect'}</button>{else}{l d='Modules.Seoredirect.Adminsearchcategorytomoveinstock' s='Category:' mod='seoredirect'}{/if} </span>
                <input id="category_autocomplete_input_move_instock" name="" type="text" class="text ac_input" value=""/>
                <input id="lang_spy" type="hidden" value="{$id_langg}"/>
                <span class="input-group-addon"><i class="icon-search"></i></span>
                <input type="hidden" class="categoryBoxDefInStock" name="categoryBoxDefInStock" value="{if Configuration::get('seor_pins_category') != false}{Configuration::get('seor_pins_category')}{/if}"/>
            </div>
        </div>
    </div>
</div>