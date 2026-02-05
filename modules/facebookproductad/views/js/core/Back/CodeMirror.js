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
    $(document).on('click', 'a[href="#css"]', function () {
        setEditorCSS();
    });
});

function setEditorCSS() {
    if (typeof (editor_css) == 'undefined') {
        editor_css = CodeMirror.fromTextArea(document.getElementById("advancedStyles"), {
            mode: "css",
            lineNumbers: true,
            autofocus: true
        });
    }
}
