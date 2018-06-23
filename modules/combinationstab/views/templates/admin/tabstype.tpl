{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-2017 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}
<form name="combinationstab_position_form" method="post" action="">
    <div class="panel" id="combinationstab_position">
        <h3><i class="icon-wrench"></i> {l s='Position of combinations table' mod='combinationstab'}</h3>
        <div class="defaultForm form-horizontal">
            <div class="form-wrapper">
                <div class="form-group">
                    <label class="control-label col-lg-3">
                        {l s='Where you want to put table?' mod='combinationstab'}
                    </label>
                    <div class="col-lg-9">
                        <select name="ctp_where" class=" fixed-width-xl" id="ctp_where">
                            <option value="1" {if Configuration::get('ctp_where') == 1}selected="selected"{/if}>{l s='Product footer section' mod='combinationstab'}</option>
                            <option value="2" {if Configuration::get('ctp_where') == 2}selected="selected"{/if}>{l s='Product tabs section' mod='combinationstab'}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info">
            {l s='Select type of tabs module will create' mod='combinationstab'}
        </div>
        <table style="width:100%; text-align:center;">
            <tr>
                <td>
                    <img style="cursor:pointer;" src="../modules/combinationstab/img/15.gif" onclick="$('.ps15view').attr('checked',true);"/>
                </td>
                <td>
                    <img style="cursor:pointer;" src="../modules/combinationstab/img/16.gif" onclick="$('.ps16view').attr('checked',true);"/>

                </td>
            </tr>
            <tr>
                <td>{l s='Tabbed view like default tabs in' mod='combinationstab'} PrestaShop 1.5, 1.7<br/><input type="radio" name="ctp_tabtype" class="ps15view" value="15" {if Configuration::get('ctp_tabtype') == 15}checked="checked"{/if}></td>
                <td>{l s='Wide horizontal bars like default tabs in' mod='combinationstab'} PrestaShop 1.6<br/><input type="radio" name="ctp_tabtype" class="ps16view" value="16" {if Configuration::get('ctp_tabtype') == 16}checked="checked"{/if}></td>
            </tr>
        </table>
        <div class="clearfix"></div>
        <div class="panel-footer">
            <a href="#" onclick="combinationstab_position_form.submit()" class="btn btn-default pull-right">
                <input type="hidden" name="module_position" value="1"/>
                <i class="process-icon-save"></i>
                {l s='Save' mod='combinationstab'}
            </a>
        </div>
    </div>
</form>