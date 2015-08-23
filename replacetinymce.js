/* global $, CodeMirror */

$(document).ready(function () {

    function setupCodeMirror (elt) {
        CodeMirror.fromTextArea(elt, {
            lineWrapping: true,
            lineNumbers: true,
            mode: 'htmlmixed'
        });
    }

    $('textarea.autoload_rte, textarea.rte')
        .removeClass('rte autoload_rte')
        .each(function (_, elt) {

            var $elt = $(elt);

            if ($elt.is(':visible') || !('MutationObserver' in window)) {
                setupCodeMirror(elt);
            } else {
                /**
                 * This gets a bit tricky: codemirror messes up the layout a bit
                 * when initializing it on a textarea that is hidden.
                 * Since this usually happens when the textareas are hidden inside
                 * translatable fields, we observe the DOM and setup codemirror
                 * only when the textarea becomes visible!
                 */
                var container = $elt.closest('.translatable-field').get(0);
                if (container) {
                    var observer = new MutationObserver(function () {
                        if ($elt.is(':visible')) {
                            setupCodeMirror(elt);
                            observer.disconnect();
                        }
                    });
                    observer.observe(container, {
                        attributes: true
                    });
                }
            }
        })
    ;
});
