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

 if (typeof(fwkUtils) == 'undefined') {
    var fwkUtils = {
        updateDatas: function(jsonData) {
            if (typeof(jsonData.reloadPage) !== 'undefined') {
                window.location = jsonData.redirectUrl;
                window.location.reload(true);
            }
            if (typeof(jsonData.redirectUrl) !== 'undefined') {
                window.location = jsonData.redirectUrl;
            }
        },
    };
}