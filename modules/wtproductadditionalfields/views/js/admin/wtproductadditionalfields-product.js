/**
 * 2017 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2017 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

var WTPAF = {
    add: function (field, type) {
        $.ajax({
            url: WTPAFurl,
            type: 'POST',
            data: {
                ajax: 1,
                action: 'getItemForm',
                field: field,
                type: type,
                counter: WTPAFcounter['id_' + field],
                id_product: $('#form_id_product').val()
            },
            success: function (html) {
                $('#btn_wt_add_item_'+field).remove();
                $('#wt_paf_tab_' + field).append(html);
                if (parseInt(type) == 3) {
                    WTPAF.initTinySetup();
                }
            }

        });
    },
    initTinySetup: function () {
        tinySetup({
            editor_selector: 'autoload_rte',
            setup: function (ed) {
                ed.on('keydown', function (ed, e) {
                    tinyMCE.triggerSave();
                    textarea = $('#' + tinymce.activeEditor.id);
                    var max = textarea.parent('div').find('span.counter').data('max');
                    if (max != 'none') {
                        count = tinyMCE.activeEditor.getBody().textContent.length;
                        rest = max - count;
                        if (rest < 0)
                            textarea.parent('div').find('span.counter').html('<span style="color:red;">Maximum ' + max + ' caractères : ' + rest + '</span>');
                        else
                            textarea.parent('div').find('span.counter').html(' ');
                    }
                });
                ed.on('blur', function (ed) {
                    tinyMCE.triggerSave();
                });
            }
        });
    },
    remove: function (element, id_paf_field) {
        var button_add = $('#btn_wt_add_item_' + id_paf_field).parent().html();
        $('#btn_wt_add_item_' + id_paf_field).remove();
        $(element).prev('input.remove_item').val('true').parents('.form-group.row').hide();
        $('div.for_btn_add_item_' + id_paf_field + ':visible:last').html(button_add);
    }
};


