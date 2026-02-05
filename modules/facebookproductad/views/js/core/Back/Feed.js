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

 if (typeof(fwkFeed) == 'undefined') {
    var fwkFeed = {
        modalName : '',
        setModalName: function(element) {
            fwkFeed.modalName = element;
        },
        getModalName: function() {
            return fwkFeed.modalName;
        },
        init: function() {
            $('.modal[data-feed-modal]').on('show.bs.modal', function(e) {
                fwkFeed.cleanModal();
            });
        },
        cleanModal: function () {
            $(this).removeData('bs.modal');
        }
    };
}

$(document).ready(function() {
    fwkFeed.init();
});