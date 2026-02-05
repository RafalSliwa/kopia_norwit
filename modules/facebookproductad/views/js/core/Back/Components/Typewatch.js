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

 if (typeof(fwkTypewatch) == 'undefined') {
    var fwkTypewatch = {
        element : '',
        setElement: function(element) {
            fwkTypewatch.element = element;
        },
        getElement: function() {
            return fwkTypewatch.element;
        },
        showSearching: function() {
            $('[data-input-' + fwkTypewatch.getElement() + '-loading]').removeClass('hide');
        },
        hideSearching: function() {
            $('[data-input-' + fwkTypewatch.getElement() + '-loading]').addClass('hide');
        },
        showNotFound: function() {
            $('[data-input-' + fwkTypewatch.getElement() + '-not-found]').removeClass('hide');
        },
        hideNotFound: function() {
            $('[data-input-' + fwkTypewatch.getElement() + '-not-found]').addClass('hide');
        },
        showError: function(errorMessage) {
            $('[data-input-' + fwkTypewatch.getElement() + '-error]').removeClass('hide');
            $('[data-input-' + fwkTypewatch.getElement() + '-error] .alert-text').html(errorMessage);
        },
        hideError: function() {
            $('[data-input-' + fwkTypewatch.getElement() + '-error]').addClass('hide');
        },
        showResults: function(items) {
            $.each(items, function (i, item) {
                $('[name=' + fwkTypewatch.getElement() + ']').append($('<option>', { 
                    value: parseInt(item.id),
                    text : item.name 
                }));
            });

            $('[name=' + fwkTypewatch.getElement() + ']').removeClass('hide');
        },
        emptyResults: function() {
            $('[name=' + fwkTypewatch.getElement() + ']').html('');

            $('[name=' + fwkTypewatch.getElement() + ']').append($('<option>', { 
                value: 0,
                text : btpm_framework.labels.noItemChoosen 
            }));

            $('[name=' + fwkTypewatch.getElement() + ']').addClass('hide');
        },
        searchProducts: function(element) {
            fwkTypewatch.setElement(element);

            $.ajax({
                type: "POST",
                url: btpm_framework.urls.moduleConfiguration,
                async: true,
                dataType: "json",
                data : {
                    ajax: "1",
                    action: "searchProducts",
                    query: $('input[name=' + fwkTypewatch.getElement() + '-search]').val(),
                    excludeIds: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsExcludeIds'),
                    disableCombination: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsDisableCombination'),
                    excludeVirtuals: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsExcludeVirtuals'),
                    excludePacks: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsExcludePacks'),
                    excludeCombinations: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsExcludeCombinations'),
                    excludeCustomizations: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsExcludeCustomizations'),
                },
                beforeSend: function() {
                    fwkTypewatch.emptyResults();
                    fwkTypewatch.hideError();
                    fwkTypewatch.hideNotFound();
                    fwkTypewatch.showSearching();
                },
                complete: function() {
                    fwkTypewatch.hideSearching();
                },
                success : function(res)
                {
                    if (typeof(res.found) !== 'undefined' && !res.found) {
                        fwkTypewatch.showNotFound();
                    } else {
                        fwkTypewatch.showResults(res);
                    }
                },
                error: function() {
                    fwkTypewatch.showError(btpm_framework.labels.errorOccured);
                }
            });
        },
        searchCategories: function(element) {
            fwkTypewatch.setElement(element);

            $.ajax({
                type: "POST",
                url: btpm_framework.urls.moduleConfiguration,
                async: true,
                dataType: "json",
                data : {
                    ajax: "1",
                    action: "searchCategories",
                    query: $('input[name=' + fwkTypewatch.getElement() + '-search]').val(),
                    excludeIds: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsExcludeIds'),
                },
                beforeSend: function() {
                    fwkTypewatch.emptyResults();
                    fwkTypewatch.hideError();
                    fwkTypewatch.hideNotFound();
                    fwkTypewatch.showSearching();
                },
                complete: function() {
                    fwkTypewatch.hideSearching();
                },
                success : function(res)
                {
                    if (typeof(res.found) !== 'undefined' && !res.found) {
                        fwkTypewatch.showNotFound();
                    } else {
                        fwkTypewatch.showResults(res);
                    }
                },
                error: function() {
                    fwkTypewatch.showError(btpm_framework.labels.errorOccured);
                }
            });
        },
        searchCustomers: function(element) {
            fwkTypewatch.setElement(element);

            $.ajax({
                type: "POST",
                url: btpm_framework.urls.moduleConfiguration,
                async: true,
                dataType: "json",
                data : {
                    ajax: "1",
                    action: "searchCustomers",
                    query: $('input[name=' + fwkTypewatch.getElement() + '-search]').val(),
                    excludeIds: $('input[name=' + fwkTypewatch.getElement() + '-search]').data('paramsExcludeIds'),
                },
                beforeSend: function() {
                    fwkTypewatch.emptyResults();
                    fwkTypewatch.hideError();
                    fwkTypewatch.hideNotFound();
                    fwkTypewatch.showSearching();
                },
                complete: function() {
                    fwkTypewatch.hideSearching();
                },
                success : function(res)
                {
                    if (typeof(res.found) !== 'undefined' && !res.found) {
                        fwkTypewatch.showNotFound();
                    } else {
                        fwkTypewatch.showResults(res);
                    }
                },
                error: function() {
                    fwkTypewatch.showError(btpm_framework.labels.errorOccured);
                }
            });
        },
    };
}