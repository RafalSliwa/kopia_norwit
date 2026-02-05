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

if (typeof(fwkDefaultsLoaded) === 'undefined') {
    if (typeof btpm_framework === 'undefined') {
        var btpm_framework = { 'labels' : [] };
    }
    fwkjconfirm.defaults = {
        title: false,
        content: false,
        contentLoaded: function(){
        },
        icon: '',
        confirmButton: typeof btpm_framework.labels.confirm !== 'undefined' ? btpm_framework.labels.confirm : 'Yes',
        cancelButton: typeof btpm_framework.labels.cancel !== 'undefined' ? btpm_framework.labels.cancel : 'No',
        confirmButtonClass: 'btn-success',
        cancelButtonClass: 'btn-warning',
        theme: 'white bootstrap',
        animation: 'zoom',
        closeAnimation: 'scale',
        animationSpeed: 500,
        animationBounce: 1.2,
        keyboardEnabled: false,
        rtl: false,
        confirmKeys: [13], // ENTER key
        cancelKeys: [27], // ESC key
        container: 'body',
        confirm: function () {
        },
        cancel: function () {
        },
        backgroundDismiss: false,
        autoClose: false,
        closeIcon: null,
        columnClass: 'col-xl-4 offset-xl-4 col-xl-offset-4 col-lg-6 offset-lg-3 col-lg-offset-3 col-md-8 offset-md-2 col-md-offset-2 col-xs-10 offset-xs-1 col-xs-offset-1',
        onOpen: function(){
        },
        onClose: function(){
        },
        onAction: function(){
        }
    };
    fwkDefaultsLoaded = true;
}