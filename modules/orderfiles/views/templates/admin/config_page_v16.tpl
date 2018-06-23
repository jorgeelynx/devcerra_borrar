{*
 * Orderfiles Prestashop module
 *
 * @author    Wiktor Koźmiński
 * @copyright 2017-2017 Wiktor Koźmiński
*}

<form action="" method="post" class="form-horizontal">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Files' mod='orderfiles'}
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Max files per order' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <input type="text" name="config_maxFilesPerOrder" value="{$config->maxFilesPerOrder|escape:'htmlall':'UTF-8'}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Max file size in KB' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <input type="text" name="config_maxFileSizeInKB" value="{$config->maxFileSizeInKB|escape:'htmlall':'UTF-8'}" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Does only super admin should be able to delete files?' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input 
                        name="config_onlySuperAdminDelete" 
                        id="config_onlySuperAdminDelete_on" 
                        value="1" 
                        {if $config->onlySuperAdminDelete}checked="checked"{/if} 
                        type="radio">
                        <label for="config_onlySuperAdminDelete_on">{l s='Yes' mod='orderfiles'}</label>

                        <input 
                        name="config_onlySuperAdminDelete" 
                        id="config_onlySuperAdminDelete_off" 
                        value="0" 
                        {if !$config->onlySuperAdminDelete}checked="checked"{/if} 
                        type="radio">
                        <label for="config_onlySuperAdminDelete_off">{l s='No' mod='orderfiles'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Enable upload panel in customer account' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input 
                        name="config_isCustomerAccountUploadEnabled" 
                        id="config_isCustomerAccountUploadEnabled_on" 
                        value="1" 
                        {if $config->isCustomerAccountUploadEnabled}checked="checked"{/if} 
                        type="radio">
                        <label for="config_isCustomerAccountUploadEnabled_on">{l s='Yes' mod='orderfiles'}</label>

                        <input 
                        name="config_isCustomerAccountUploadEnabled" 
                        id="config_isCustomerAccountUploadEnabled_off" 
                        value="0" 
                        {if !$config->isCustomerAccountUploadEnabled}checked="checked"{/if} 
                        type="radio">
                        <label for="config_isCustomerAccountUploadEnabled_off">{l s='No' mod='orderfiles'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Enable upload panel in shopping cart' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input 
                        name="config_isCartUploadEnabled" 
                        id="config_isCartUploadEnabled_on" 
                        value="1" 
                        {if $config->isCartUploadEnabled}checked="checked"{/if} 
                        type="radio">
                        <label for="config_isCartUploadEnabled_on">{l s='Yes' mod='orderfiles'}</label>

                        <input 
                        name="config_isCartUploadEnabled" 
                        id="config_isCartUploadEnabled_off" 
                        value="0" 
                        {if !$config->isCartUploadEnabled}checked="checked"{/if} 
                        type="radio">
                        <label for="config_isCartUploadEnabled_off">{l s='No' mod='orderfiles'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                </div>
            </div>

        </div>
        <div class="panel-footer">
            <input type="hidden" name="action" value="updateConfig"/>
            <button type="submit" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='orderfiles'}
            </button>
        </div>
    </div>
</form>

<form action="" method="post" class="form-horizontal">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Files stats' mod='orderfiles'}
        </div>
        <div class="panel-body">

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='All files count' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <input type="text"  value="{$fileStats.filescount|escape:'htmlall':'UTF-8'}" disabled="disabled" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Free space' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <input type="text"  value="{$fileStats.freeSpace|escape:'htmlall':'UTF-8'}" disabled="disabled" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Files path' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <input type="text"  value="{$fileStats.absPath|escape:'htmlall':'UTF-8'}" disabled="disabled" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-lg-3 control-label">{l s='Remove files' mod='orderfiles'}</label>
                <div class="col-lg-3">
                    <select name="timeRange">
                        <option value="365">{l s='Older than 1 year' mod='orderfiles'}</option>
                        <option value="180">{l s='Older than 6 months' mod='orderfiles'}</option>
                        <option value="90">{l s='Older than 3 months' mod='orderfiles'}</option>
                        <option value="30">{l s='Older than 1 month' mod='orderfiles'}</option>
                        <option value="14">{l s='Older than 2 weeks' mod='orderfiles'}</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <input type="hidden" name="action" value="removeFiles"/>
                    <button type="submit" class="btn btn-danger">
                        <i class="icon-trash"></i> {l s='Remove' mod='orderfiles'}
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>
