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
 */
function validate(evt) {
    var theEvent = evt || window.event;

    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
        // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};
$(document).ready(function () {
    var shopIdObj = $('#shopId');
    shopIdObj.keyup(function () {
        if (/\D/g.test(this.value)) {
            // Filter non-digits from input value.
            this.value = this.value.replace(/\D/g, '');
        }
    });
    var test = getUrlParameter('test');
    var shopId = shopIdObj.val();
    var secretkey = $('#secretkey').val();
    var x = 0;
    $('.form-group').each(function () {
        $(this).addClass('element_' + x);
        x++;
    })
    if (shopId !== '' && secretkey !== '') {
        var url = document.location.href;
        $('<div class="text_connection" style="text-align:right;">' +
            '<a href="' + url + '&test=1">' +
            '<div class="btn btn-warning">' + translations.testConnection + '</div>' +
            '</a>' +
            '</div>').insertAfter('.element_2');
    }

    if ($("#disable_fields").length > 0 && $("#disable_fields").val() !== 'ok') {
        $('input#shopId').parent().parent().prepend(
            '<h2 style="text-align:center;color:red; font-size:14px;">' + translations.invalidConfig + '</h2>'
        );
    } else {
        if ($("#disable_fields").val() === 'ok') {
            $('input#shopId').parent().parent().prepend(
                '<h2 style="text-align:center;color:green; font-size:14px;">' + translations.validConfig + '</h2>'
            );
        }
    }
})
