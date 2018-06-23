/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

function QtyChange(id, qty) {
    if (+$('#' + id).val() > qty) {
        $('#' + id).val(qty);
    }
}

function pagination() {
    if (ct_pagination == 1) {
        var req_num_row = ct_pagination_nb;
        var $tr = $('#ct_matrix tbody tr');
        var total_num_row = $tr.length;
        var num_pages = 0;
        if (total_num_row % req_num_row == 0) {
            num_pages = total_num_row / req_num_row;
        }
        if (total_num_row % req_num_row >= 1) {
            num_pages = total_num_row / req_num_row;
            num_pages++;
            num_pages = Math.floor(num_pages++);
        }
        for (var i = 1; i <= num_pages; i++) {
            $('#ct_pagination').append("<li class='btn'><a href='#'>" + i + "</a></li>");
        }

        $('#ct_pagination li:first-child').addClass('active current');
        $tr.each(function (i) {
            $(this).hide();
            if (i + 1 <= req_num_row) {
                $tr.eq(i).show();
            }

        });

        $('#ct_pagination a').click(function (e) {
            $('#ct_pagination a').parent().removeClass('active current');
            $(this).parent().addClass('active current');
            e.preventDefault();
            $tr.hide();
            var page = $(this).text();
            var temp = page - 1;
            var start = temp * req_num_row;
            //alert(start);

            for (var i = 0; i < req_num_row; i++) {
                $tr.eq(start + i).show();
            }
        });
    }
}

$(document).ready(function () {
    function getColNb(className) {
        var rowIndex = $('.' + className).parent().index('#ct_matrix tbody tr');
        var tdIndex = $('.' + className).index('#ct_matrix tbody tr:eq(' + rowIndex + ') td');
        return tdIndex;
    }

    if (typeof ctp_sort !== 'undefined') {
        if (ctp_sort == 1) {
            if (ctp_sort_attr == 1) {
                tdIndex = getColNb('ctd_attr_group_' + ctp_sort_attrid);
                if (tdIndex >= 0) {
                    $("#ct_matrix").tablesorter({
                        sortList: [[tdIndex, ctp_sort_attrby]]
                    });
                    $("#ct_matrix").tablesorter({
                        sortList: [[tdIndex, ctp_sort_attrby]]
                    });
                }
            } else {
                $("#ct_matrix").tablesorter({});
            }
        }
    }

    if (ctp_fancybox == 1) {
        $("a.fancybox").fancybox({
            'speedIn': 600,
            'speedOut': 200,
            'width': '80%',
            'height': '80%',
            'autoSize': false,
            'overlayShow': false,
            'fitToView': true
        });
    }
    pagination();
});

var ajaxCart = {
    add: function (idProduct, idCombination, addedFromProductPage, callerElement, quantity, whishlist) {
        var $body = $('body');
        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: cart_url + '?rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: 'action=update&add=1&ajax=true&qty=' + ((quantity && quantity != null) ? quantity : '1') + '&id_product=' + idProduct + '&token=' + static_token + ( (parseInt(idCombination) && idCombination != null) ? '&ipa=' + parseInt(idCombination) : '' + '&id_customization=' + ((typeof customizationId !== 'undefined') ? customizationId : 0)),
            success: function (jsonData, textStatus, jqXHR) {
                prestashop.emit('updateCart', {
                    reason: {
                        idProduct: idProduct,
                        idProductAttribute: idCombination,
                        linkAction: 'add-to-cart'
                    }
                });
            }
        });
    }
}