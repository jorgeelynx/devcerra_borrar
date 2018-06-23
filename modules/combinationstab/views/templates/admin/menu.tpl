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
<link href="../modules/combinationstab/css.css" rel="stylesheet" type="text/css" />
<form name="selectform1" id="selectform1" action="" method="post"><input type="hidden" name="selecttab" value="1"></form>
<form name="selectform2" id="selectform2" action="" method="post"><input type="hidden" name="selecttab" value="2"></form>
<form name="selectform3" id="selectform3" action="" method="post"><input type="hidden" name="selecttab" value="3"></form>
<div id='cssmenu'>
    <ul>
        <li><a>v{$module_version}</a></li>
        <li class='{$selected1}'><a href='#' onclick="selectform1.submit()"><span>{l s='Configuration' mod='combinationstab'}</span></a></li>
        <li class='{$selected2}'><a href='#' onclick="selectform2.submit()"><span>{l s='Product Restrictions' mod='combinationstab'}</span></a></li>
        <li style='position:relative; display:inline-block; float:right;'><a href='http://mypresta.eu/contact.html' target='_blank'><span>{l s='Support' mod='combinationstab'}</span></a></li>
        <li class='{$selected3}'  style='position:relative; display:inline-block; float:right;'> <a href="#" onclick="selectform3.submit()"><span>{l s='Updates' mod='combinationstab'}</span></a></li>
    </ul>
</div>
<script>
    function select_undelected()
    {
        if ($('select[name="ctp_attributes_method"] option:selected').val() == 0) {
            $('select[name="ctp_attr_label"]').parent().parent().hide();
        } else {
            $('select[name="ctp_attr_label"]').parent().parent().show();
        }

        if ($('select[name="ctp_sort"] option:selected').val() == 0) {
            $('select[name="ctp_sort_price"]').parent().parent().hide();
            $('select[name="ctp_sort_attr"]').parent().parent().hide();
            $('input[name="ctp_sort_attrid"]').parent().parent().hide();
            $('select[name="ctp_sort_attrby"]').parent().parent().hide();
        } else {
            $('select[name="ctp_sort_price"]').parent().parent().show();
            $('select[name="ctp_sort_attr"]').parent().parent().show();
            $('input[name="ctp_sort_attrid"]').parent().parent().show();
            $('select[name="ctp_sort_attrby"]').parent().parent().show();
        }

    }

    $('document').ready(function(){
        select_undelected();
        $('select[name="ctp_sort"], select[name="ctp_sort_price"], select[name="ctp_attributes_method"], select[name="ctp_sort_attr"]').change(function(){
            select_undelected();
        });
    });
</script>