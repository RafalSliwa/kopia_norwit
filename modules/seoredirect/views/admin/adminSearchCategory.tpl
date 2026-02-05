<script>
    var currentToken = '{$currentToken}';
    {literal}
    $(document).ready(function () {
        (function ($) {
            $.fn.goTo = function () {
                $('html, body').animate({
                    scrollTop: $(this).offset().top + 'px'
                }, 'fast');
                return this; // for chaining...
            }
        })(jQuery);

        $('#seor_emptycat').on('change', function() {
            if (this.value != 0) {
                $('.cronJobDivCategories').show();
                $('#seor_emptycat_redirect_type').parent().parent().show();
            } else {
                $('.cronJobDivCategories').hide();
                $('#seor_emptycat_redirect_type').parent().parent().hide();
            }
        });
        $('#seor_emptycat').change();

        $('#seor_pos').on('change', function() {
            if (this.value != 0) {
                $('#seor_re, #seor_pos_redirect_type, #ajax_choose_product, #category_autocomplete_input, #product_autocomplete_input').parent().parent().show();
                $('#seor_dontato_on').parent().parent().parent().show();
            } else {
                $('#seor_re, #seor_pos_redirect_type, #ajax_choose_product, #category_autocomplete_input, #product_autocomplete_input').parent().parent().hide();
                $('#seor_dontato_on').parent().parent().parent().hide();
            }
        });
        $('#seor_pos').on('change', function() {
            if (this.value == 4 || this.value == 6 || this.value == 7) {
                $('#seor_pos_category').show();
                if (this.value == 6 || this.value == 7)
                {
                    $('#seor_pos_category .alert-info-unavailable-to-order').hide();
                }
                else
                {
                    $('#seor_pos_category .alert-info-unavailable-to-order').show();
                }
            }
            else
            {
                $('#seor_pos_category').hide();
            }
        });
        $('#seor_pos').on('change', function() {
            if (this.value != 0 && this.value != 4 && this.value != 6 && this.value != 7) {
                $('.cronJobDiv').show();
            } else {
                $('.cronJobDiv').hide();
            }
        });
        $('#seor_pos').change();



        $('#{/literal}{$input_array_name}{literal}_category_autocomplete_input')
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
                            mytab[mytab.length] = {data: data[i], value: data[i].name+' (ID #'+data[i].id_category+')'};
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
                var $divAdded = $('#{/literal}{$input_array_name}_{literal}addCategories');
                    var exclude = [];
                    var selected = $('#{/literal}{$input_array_name}_{literal}addCategories input');
                    for (var i = 0; i < selected.length; i++)
                        exclude.push(selected[i].value);
                    var ps_div = '';

                    if ($.inArray(data.id_category, exclude) == -1) {
                        ps_div = '<div id="{/literal}{$input_array_name}_{literal}selected_category_' + data.id_category + '" class="form-control-static margin-form col-lg-6"><input type="hidden" name="{/literal}{$input_array_name}{literal}[]" value="' + data.id_category + '" class="{/literal}{$input_array_name}{literal}"/><button type="button" class="btn btn-default remove-product" name="' + data.id_category + '" onclick="deleteCategory(\'{/literal}{$input_array_name}\',{literal}' + data.id_category + ')">' + img + '<i class="icon-remove text-danger"></i></button>&nbsp;' + data.name + '</div>';
                        $divAdded.show().html($divAdded.html() + ps_div);
                    }
            });
        });
        function deleteCategory(name, id) {
            $("#"+name.trim()+"_"+"selected_category_" + id).remove();
        }
    {/literal}
</script>

<div class="col-lg-9">
    <div id="ajax_choose_product" class="clearfix">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="input-group">
                <input id="{$input_array_name}_category_autocomplete_input" name="" type="text" class="text ac_input" value=""/>
                <input id="lang_spy" type="hidden" value="{$id_langg}"/>
                <span class="input-group-addon"><i class="icon-search"></i></span>
            </div>
            <div id="{$input_array_name}_addCategories">
                {if $categories_array != false}
                    {foreach $categories_array AS $category}
                        <div id="{$input_array_name}_selected_category_{$category->id}" class="form-control-static margin-form col-lg-6"><input type="hidden" name="{$input_array_name}[]" value="{$category->id}" class="{$input_array_name}">
                            <button type="button" class="btn btn-default remove-product" name="{$category->id}" onclick="deleteCategory('{$input_array_name}',{$category->id})"><i class="icon-remove text-danger"></i></button>&nbsp;{$category->name}</div>
                    {/foreach}
                {/if}
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            {if $input_array_name=='seor_oos_cat_include'}
                <div class="alert alert-warning">
                    {l d='Modules.Seoredirect.Adminsearchcategory' s='Module will work for products from selected categories only' mod='seoredirect'}.
                </div>
                <div class="alert alert-info">
                    {l d='Modules.Seoredirect.Adminsearchcategory' s='Type the name of category and select it from the list, it will be added to list of categories.' mod='seoredirect'}<br/>
                </div>
                {else}
                <div class="alert alert-warning">
                    {l d='Modules.Seoredirect.Adminsearchcategory' s='Module will not disable / redirect products from these categories when product will be out of stock' mod='seoredirect'}.
                    {l d='Modules.Seoredirect.Adminsearchcategory' s='This option checks only main product category (it does not check other associations)' mod='seoredirect'}.
                </div>
                <div class="alert alert-info">
                    {l d='Modules.Seoredirect.Adminsearchcategory' s='Type the name of category and select it from the list, it will be added to list of exclusions.' mod='seoredirect'}<br/>
                </div>
            {/if}
        </div>
    </div>
</div>