/*!
 * PKO Leasing Integration Module
 *
 * @package    PrestaShop Modules
 * @subpackage PKO Leasing Integration
 * @author     PKO Leasing
 * @license    MIT License
 * @copyright  2024 PKO Leasing
 * @link       https://www.pkoleasing.pl/
 *
 * Ten plik zawiera skrypty JavaScript dla modułu PKO Leasing Integration.
 */
if (document.getElementById("lease_click")) {
    document.getElementById("lease_click").addEventListener("click", function () {
        document.getElementById("pkol_form").submit();
    });
}
if (typeof prestashop !== 'undefined') {
    prestashop.on('updatedProduct', function (event) {
        // var title = event.product_title;
        let product_att = event.id_product_attribute;
        let pid = $('input[name="id_product"]').val();
        var url = $('#endpointurl').val() + '?att=' + product_att + '&pid=' + pid;
        $.ajax({
            type: 'POST',
            url: url,
            cache: false,
            data: {
                method: 'test',
                ajax: true
            },
            success: function (result) {
                if (result) {
                    var check = JSON.parse(result);
                    $('input[name="productName1"]').attr('value', check.product_name);
                    $('input[name="productPrice1"]').attr('value', check.price_with_tax);
                    $('input[name="productNetPrice1"]').attr('value', check.price_without_tax);
                    $('input[name="totalValue"]').attr('value', check.price_with_tax);
                    $('input[name="totalNetValue"]').attr('value', check.price_without_tax);
                    $('input[name="returnLink"]').attr('value', check.url);
                    $(document).on('click', '#lease_click', function () {
                        $('#pkol_form').submit();
                    })
                }
            }
        });


    });
    prestashop.on(
        'updateCart',
        function (event) {
            if (event && event.reason) {
                var url = $('#endpointurl').val();
                $.ajax({
                    type: 'POST',
                    url: url,
                    cache: false,
                    data: {
                        method: 'test',
                        ajax: true
                    },
                    success: function (result) {
                        if (result) {
                            var check = JSON.parse(result);
                            if ($('body').attr('id') !== 'product') {
                                var pkolForm = $('#pkol_form');
                                $('.pko_container').remove();
                                $('.pkol_widget').remove();
                                pkolForm.html(check.result);
                                $(check.button).insertBefore(pkolForm);
                            }
                            if (document.getElementById("lease_click")) {
                                document.getElementById("lease_click").addEventListener("click", function () {
                                    document.getElementById("pkol_form").submit();
                                });
                            }
                        }
                    }
                });
            }
        }
    );
}
