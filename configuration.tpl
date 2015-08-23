<style>
    #replacetinymce-status {
        text-align: right;
    }

    #replacetinymce-status > span {
        display: none;
    }
</style>

<div class="panel">
    <div class="row">
        <div class="col-lg-6">
            <p class="lead">{l s='Since you\'re an HTML warrior, we thought you\'d probably want to modify your CSS the easy way too!' mod='replacetinymce'}</p>
            <p>{l s='You\'re editing the code found in you current theme\'s file: %1$s' sprintf=[$stylesheetRelativePath]}</p>
        </div>
        <div class="col-lg-6" id="replacetinymce-status">
            <span id="replacetinymce-error" class="label label-danger"><span data-placeholder></span></span>
            <span id="replacetinymce-success" class="label label-success"><span data-placeholder></span></span>
            <span id="replacetinymce-info" class="label label-info"><span data-placeholder></span></span>
        </div>
    </div>

    <textarea id="replacetinymce-stylesheet">{$stylesheetContents|escape:'html'}</textarea>
    <div class="panel-footer">
		<button type="button" class="btn btn-default pull-right" id="replacetinymce-save-button">
			<i class="process-icon-save"></i>{l s='Save' mod='replacetinymce'}
		</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        function feedback (type, reason) {
            console.log($('#replacetinymce-status > span'));
            $('#replacetinymce-status > span').hide();
            var selector = '#replacetinymce-error';
            if (type === 'success') {
                selector = '#replacetinymce-success';
            } else if (type === 'info') {
                selector = '#replacetinymce-info';
            }
            $(selector + ' > [data-placeholder]').html(reason);
            $(selector).show();
        }

        var editor = CodeMirror.fromTextArea(document.getElementById('replacetinymce-stylesheet'), {
            lineWrapping: true,
            lineNumbers: true,
            mode: 'css'
        });

        $('#replacetinymce-save-button').on('click', function () {
            var stylesheetContents = editor.getValue();
            feedback('info', "{l s='Saving, please wait...' js=1 mod='replacetinymce'}");
            $.post(window.location + '&saveStylesheetContents=1', {
                stylesheetContents: stylesheetContents
            }, null, 'json').then(function (resp) {
                feedback(resp.ok ? 'success' : 'error', resp.reason);
            }).fail(function () {
                feedback('error', "{l s='Woops, server replied with an error.' js=1 mod='replacetinymce'}");
            });
        });
    });
</script>
