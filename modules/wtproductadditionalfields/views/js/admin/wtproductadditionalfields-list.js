/**
 * 2017 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2017 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */
$(function () {
    $('a.list-action-enable').on('click', function () {
        var _this = $(this);
        $.ajax({
            url: _this.attr('href'),
            data: 'ajax=1&action=changeActive',
            type: 'POST',
            dataType: 'json',
            success: function (json) {
                if (json.error) {
                    showErrorMessage(json.error);
                }

                if (json.success) {
                    if (json.active == 1) {
                        _this.removeClass('action-disabled').addClass('action-enabled').parent().find('i.icon-check').removeClass('hidden').parent().find('i.icon-remove').addClass('hidden');
                    } else {
                        _this.removeClass('action-enabled').addClass('action-disabled').parent().find('i.icon-remove').removeClass('hidden').parent().find('i.icon-check').addClass('hidden');
                    }
                    showSuccessMessage(json.success);
                }

            }
        });
        return false;
    });
});
