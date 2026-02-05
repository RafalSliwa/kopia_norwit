/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2025 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER
 * support@mypresta.eu
 */

$(document).ready(function () {
    $('.gototab').click(function () {
            $('#' + $(this).attr('title') + 'link').click();
            $.scrollTo('#' + $(this).attr('title') + 'link', 1200);
        }
    );
});