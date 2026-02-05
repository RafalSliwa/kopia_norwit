/**
 *
 * Framework
 *
 * @author Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 *
 ****/

$(document).ready(function() {
    $('div#addons-rating-container p.dismiss a').click(function() {
        $('div#addons-rating-container').hide(500);
        $.ajax({type : "POST", url: window.location, data: {dismissRating: 1} });
        return false;
    });

    // Copy to clipboard
    $('#module_form [data-copy]').click(function() {
        $toCopy = $(this).data('copy');
        $text = $($toCopy).text();
        if (!$text.length) {
            $text = $($toCopy).val();
        }

        if ($text.length) {
            navigator.clipboard.writeText($text);
            $(this).css('color', '#13bb13');
        } else {
            $(this).css('color', '#c92835');
        }
    });

    // Show/hide switch
    $('#module_form [data-related]').change(function () {
        var val = parseInt($(this).val());
        $relatedTo = $(this).data('related');

        if (val) {
            $('.form-group.' + $relatedTo).removeClass('related-option-disabled hide');
        } else {
            $('.form-group.' + $relatedTo).addClass('related-option-disabled hide');
        }
    });
});