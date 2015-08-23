/* global $, CodeMirror */

$(document).ready(function () {
    $('.autoload_rte, .rte')
        .removeClass('rte autoload_rte')
        .each(function (_, elt) {
            if (elt.tagName.toLowerCase() === 'textarea') {
                CodeMirror.fromTextArea(elt, {
                    lineWrapping: true,
                    lineNumbers: true,
                    mode: 'htmlmixed'
                });
            }
        })
    ;
});
