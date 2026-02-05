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

 if (typeof(fwkErrors) == 'undefined') {
    if (typeof btpm_framework === 'undefined') {
        var btpm_framework = { 'labels' : [] };
    }

    var fwkErrors = {
        displayErrors: function(errorData) {
            if (typeof(errorData) === 'string') {
                jsonData = {
                    'hasError': true,
                    'errors': { errorData }
                };
            } else {
                jsonData = errorData;
            }
            // User errors display
            if (jsonData.hasError) {
                var errors = '';
                for (error in jsonData.errors) {
                    // IE6 bug fix
                    if (error != 'indexOf') {
                        errors += $('<div />').html(jsonData.errors[error]).html() + "<br />";
                    }
                }
                if (typeof(jsonData.labelErrorTitle) !== 'undefined') {
                    var modalTitle = jsonData.labelErrorTitle;
                } else {
                    var modalTitle = typeof btpm_framework.labels.errorOccured !== 'undefined' ? btpm_framework.labels.errorOccured : 'Error';
                }
                if (!!$.alert) {
                    $.alert({
                        title: modalTitle,
                        type: 'red',
                        content: errors,
                        columnClass: 'col-xl-6 offset-xl-3 col-xl-offset-3 col-lg-6 offset-lg-3 col-lg-offset-3 col-md-8 offset-md-2 col-md-offset-2 col-xs-10 offset-xs-1 col-xs-offset-1',
                    });
                } else if (!!$.prototype.fancybox) {
                    $.fancybox.open([
                        {
                            type: 'inline',
                            autoScale: true,
                            minHeight: 30,
                            content: '<p class="fancybox-error">' + errors + '</p>'
                        }
                    ], {
                        padding: 0
                    });
                } else {
                    alert(errors);
                }
            }
        },
    };
}