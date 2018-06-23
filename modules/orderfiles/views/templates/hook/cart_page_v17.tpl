{*
 * Orderfiles Prestashop module
 *
 * @author    Wiktor Koźmiński
 * @copyright 2017-2017 Wiktor Koźmiński
*}

<div class="card v17" id="block-order-files">
    <div class="card-block">
        <h1 class="h1">{l s='Files attached with this cart' mod='orderfiles'}</h1>
    </div>
    <hr>
    <div class="card-block">
        <table class="table table-striped table-bordered table-labeled">
            <thead class="thead-default">
                <tr>
                    <th>{l s='File name' mod='orderfiles'}</th>
                    <th>{l s='Description' mod='orderfiles'}</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                {*
                {foreach from=$files item=f}
                <tr class="file-row" file-id="{$f->getId()}">
                    <td>{$f->name}</td>
                    <td>{$f->desc}</td>
                    <td></td>
                </tr>
                {/foreach}
                *}
                <tr>
                    <td class="new-file-area"></td>
                    <td>
                        {l s='File description (optional)' mod='orderfiles'}<br/>
                        <textarea name="file_desc" class="form-control" rows="2"></textarea>
                    </td>
                    <td>
                        <button class="btn btn-primary send-file-btn">
                            <span><i class="icon-plus-sign"></i> {l s='Add file' mod='orderfiles'}</span>
                        </button>
                        <div class="error-notif" style="display: none;">
                            <i class="material-icons">highlight_off</i>
                            <p>
                                {l s='Unfortunately your file was not uploaded' mod='orderfiles'}<br>
                                {l s='Reason' mod='orderfiles'}: <span class="error-text"></span>
                            </p>
                        </div>
                            {* {l s='Your file is being uploaded' mod='orderfiles'}
                            <div class="progress"></div> *}
                        <div class="uploading-box" style="display: none;">
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    var files = JSON.parse('{$files|json_encode nofilter}');
    var fileRemoveURL = '{$link->getModuleLink('orderfiles', 'ShoppingCartFiles', ['ajax' => 1, 'action' => 'FileRemove']) nofilter}';
    var DICT_REMOVE = '{l s='Delete' mod='orderfiles'}';

    window[ addEventListener ? 'addEventListener' : 'attachEvent' ]( addEventListener ? 'load' : 'onload', function() {

        if (!window.OrderFilesUploader) {
            console.error('OrderFilesUploader class not found!');
            return;
        }
        {literal}
        var uplBox = document.querySelector('.uploading-box');
        var sendFileBtn = document.querySelector('.send-file-btn');

        var progBar = new ProgressBar.Line(uplBox, {
          strokeWidth: 4,
          easing: 'easeInOut',
          duration: 200,
          color: '#FFEA82',
          trailColor: '#eee',
          trailWidth: 1,
          svgStyle: {width: '100%', height: '100%'},
          text: {
            style: {
              // Text color.
              // Default: same as stroke color (options.color)
              color: '#999',
              position: 'absolute',
              right: '0',
              top: '15px',
              padding: 0,
              margin: 0,
              transform: null
            },
            autoStyleContainer: false
          },
          step: (state, bar) => {
            bar.setText(Math.round(bar.value() * 100) + ' %');
          }
        });
        {/literal}

        var ofu = new OrderFilesUploader({
            dropArea: document.querySelector('.new-file-area'),
            fileDescInput: document.querySelector('textarea[name=file_desc]'),
            sendFileBtn: sendFileBtn,
            uploadUrl: '{url entity=module name=orderfiles controller=ShoppingCartFiles params=['ajax' => 1, 'action' => 'FileUpload'] }',
            hintText: '{l s='Upload new file (drag or click here)' mod='orderfiles'}',

            onFileSent: function(res) {
                uplBox.style.display = 'none';
                sendFileBtn.style.display = null;

                try {
                    res = JSON.parse(res);
                } catch (e){
                    return notifError('{l s='Server did not respond correctly' mod='orderfiles'}');
                }

                if (res && res.error) return notifError(res.error);

                // location.reload();
                files.push(res.file);
                renderFilesTable(files);
            },

            onProgress: function(loaded, total) {
                sendFileBtn.style.display = 'none';
                uplBox.style.display = null;
                progBar.animate(loaded / total);
            }
        });

        renderFilesTable(files);

        function notifError(errorText) {
            var errBox = document.querySelector('.error-notif');
            ofu.setCurrentFile(null);
            errBox.style.display = null;
            sendFileBtn.style.display = 'none';
            errBox.querySelector('.error-text').innerHTML = errorText;

            setTimeout(function() {
                errBox.style.display = 'none';
                sendFileBtn.style.display = null;
            }, 5000);
        }

        function renderFilesTable(filesModels) {
            var body = document.querySelector('#block-order-files tbody');
            var fileRows = document.querySelectorAll('tr.file-row');
            for (var i = 0; i < fileRows.length; i++) {
                fileRows[i].parentElement.removeChild(fileRows[i]);
            }

            for (var i = filesModels.length - 1; i >= 0; i--) {
                var tr = document.createElement('tr');
                var f = filesModels[i];
                tr.className = 'file-row';
                tr.setAttribute('file-id', f.id);
                tr.innerHTML = 
                    '<td>'+f.name+'<br/>'+moment.unix(f.crTimestamp).fromNow()+'</td>' +
                    '<td>'+f.desc+'</td>' +
                    '<td><a data-remove-file="'+f.id+'" href="#"><i class="icon icon-trash"></i> '+DICT_REMOVE+'</a></td>';

                body.insertBefore(tr, body.firstChild);

                var deleteLink = tr.querySelector('a[data-remove-file]').addEventListener('click', removeFileEvent);
            }

        }

        function removeFileEvent(event){
            event.preventDefault();
            var fileId = parseInt(this.getAttribute('data-remove-file'));
            if (!fileId || !confirm('{l s='Are you sure you want to delete this file?' mod='orderfiles'}')) return;

            var req = new XMLHttpRequest();
            req.onreadystatechange = function() {
                if (req.readyState != 4) return;
                if (req.status > 199 && req.status < 300) { 
                    var idx = files.map(function(f) { return f.id+""; }).indexOf(fileId+"");
                    if (idx != -1) files.splice(idx, 1);
                    return renderFilesTable(files); 
                }
            }

            req.open('POST', fileRemoveURL);
            req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            req.send('remove_file_id='+fileId); 
        }
    });

</script>