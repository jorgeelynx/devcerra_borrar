{*
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* We are experts and professionals in PrestaShop
*
* @author    PresTeamShop.com <support@presteamshop.com>
* @copyright 2011-2017 PresTeamShop
* @license   see file: LICENSE.txt
* @category  PrestaShop
* @category  Module
*}

{literal}
    <script>
        $(function(){
            $('#btn_update_version_module').on('click', function(){
                $.ajax({
                    type: 'POST',
                    url: {/literal}'{$url_call|escape:'quotes':'UTF-8'}'{literal},
                    data: {
                        is_ajax: true,
                        action: 'updateVersion',
                        token: {/literal}'{$token|escape:'htmlall':'UTF-8'}'{literal},
                        dataType: 'html'
                    },
                    beforeSend: function () {
                        $('#btn_update_version_module').attr('disabled', true).addClass('disabled');
                    },
                    success: function (data) {
                        if (data == 'OK') {
                            location.reload();
                        }
                    }
                });
            });
        });
    </script>
{/literal}

<div class="bootstrap panel">
    <div class="alert alert-warning">
        {l s='We have detected you uploaded the new version' mod='onepagecheckoutps'} <b>{$module_version|escape:'htmlall':'UTF-8'}</b> {l s='of our module' mod='onepagecheckoutps'} <b>{$module_name|escape:'htmlall':'UTF-8'}</b>.
        <br/><br/>
        {l s='To proceed with the update, you need to click here' mod='onepagecheckoutps'}: <input id="btn_update_version_module" type="button" class="btn btn-primary btn-xs" value="{l s='Update now' mod='onepagecheckoutps'}" />
    </div>
</div>