/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2017 PresTeamShop
 * @license   see file: LICENSE.txt
 * @category  PrestaShop
 * @category  Module
 */

$(function(){
    AppOPC.init();

    //$('#checkbox_create_account_guest').trigger('click');
});

var AppOPC = {
    $opc: false,
    $opc_step_one: false,
    $opc_step_two: false,
    $opc_step_three: false,
    $opc_step_review: false,
    initialized: false,
    load_offer: true,
    is_valid_all_form: false,
    jqOPC: typeof $jqOPC === typeof undefined ? $ : $jqOPC,
    init: function(){
        AppOPC.initialized = true;
        AppOPC.$opc = $('#onepagecheckoutps');
        AppOPC.$opc_step_one = $('#onepagecheckoutps div#onepagecheckoutps_step_one');
        AppOPC.$opc_step_two = $('#onepagecheckoutps div#onepagecheckoutps_step_two');
        AppOPC.$opc_step_three = $('#onepagecheckoutps div#onepagecheckoutps_step_three');
        AppOPC.$opc_step_review = $('#onepagecheckoutps div#onepagecheckoutps_step_review');

        if (typeof OnePageCheckoutPS !== typeof undefined)
        {
            if (typeof jeoquery !== typeof undefined) {
                jeoquery.defaultCountryCode = OnePageCheckoutPS.iso_code_country_delivery_default;
                jeoquery.defaultLanguage = prestashop.language.iso_code;
                jeoquery.defaultData.lang = prestashop.language.iso_code;
            }

            //launch validate fields
            if (typeof $.formUtils !== typeof undefined && typeof $.validate !== typeof undefined){
                $.formUtils.loadModules('prestashop.js, security.js, brazil.js', OnePageCheckoutPS.ONEPAGECHECKOUTPS_DIR + 'views/js/lib/form-validator/');
                $.validate({
                    form: 'div#onepagecheckoutps #form_login, div#onepagecheckoutps #form_onepagecheckoutps',
                    validateHiddenInputs: true,
                    language : messageValidate,
                    onError: function () {
                        AppOPC.is_valid_all_form = false;
                    },
                    onSuccess: function () {
                        AppOPC.is_valid_all_form = true;

                        return false;
                    }
                });
            }

            $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE)
                .css({
                    margin: 0
                })
                .addClass('opc_center_column')
                .removeClass('col-sm-push-3');

            Address.launch();
            Fronted.launch();
            if (!OnePageCheckoutPS.REGISTER_CUSTOMER)
            {
                $(OnePageCheckoutPS.CONFIGS.OPC_ID_CONTENT_PAGE).css({width: '100%'});

                Carrier.launch();
                Payment.launch();
                Review.launch();
            }

            $('div#onepagecheckoutps #onepagecheckoutps_step_one input[data-validation*="isBirthDate"]').datepicker({
                dateFormat: OnePageCheckoutPS.date_format_language,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                yearRange: '-100:+0',
                isRTL: parseInt(prestashop.language.is_rtl)
            });
            $('div#onepagecheckoutps #onepagecheckoutps_step_one input[data-validation*="isDate"]').datepicker({
                dateFormat: OnePageCheckoutPS.date_format_language,
                //cuando necesita des-habilitar dias anteriores al actual
                //minDate: 0,
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                yearRange: '-100:+0',
                isRTL: parseInt(prestashop.language.is_rtl)
            });
        }else{//redirect to checkout if have he option OPC_REDIRECT_DIRECTLY_TO_OPC actived
            $('.cart-detailed-actions a').attr('href', prestashop.urls.current_url + '?checkout=1');
            $('form#voucher').attr('action', prestashop.urls.current_url);

            var href_delete_voucher = $('a.price_discount_delete').attr('href');
            if (typeof(href_delete_voucher) != 'undefined'){
                href_delete_voucher = href_delete_voucher.split('?');
                $('a.price_discount_delete').attr('href', prestashop.urls.current_url + '?' + href_delete_voucher[1]);
            }
        }
    }
}

var Fronted = {
    launch: function(){
        $('div#onepagecheckoutps #opc_show_login').click(function(){
            Fronted.showModal({type:'normal', title:$('#opc_login').attr('title'), title_icon:'fa-pts-user', content:$('#opc_login')});
        });

        /*if (AppOPC.$opc.hasClass('rc')) {
            AppOPC.$opc.find('#opc_show_login').trigger('click');
        }*/

        AppOPC.$opc.find('#opc_login').on('click', '#btn_login', Fronted.loginCustomer);
        AppOPC.$opc.find('#div_onepagecheckoutps_login').on('click', '#btn_logout', function(){
            $.totalStorageOPC('id_address_delivery', null);
            $.totalStorageOPC('id_address_invoice', null);
        });
        AppOPC.$opc.on('click', '#btn_continue_shopping', function(){
            var link = $('div#onepagecheckoutps #btn_continue_shopping').data('link');
            if (typeof link === typeof undefined) {
                link = prestashop.urls.pages.index;
            }
            window.location = link;
        });
        AppOPC.$opc.on('click', '#btn-logout', function(e){
            window.location = $(e.currentTarget).data('link');
        });

        AppOPC.$opc.find('#opc_login #txt_login_password').keypress(function(e){
            var code = (e.keyCode ? e.keyCode : e.which);

            if (code == 13)
                Fronted.loginCustomer();
       });

       //evita copiar, pegar y cortar en los campos de confirmacion.
        /*AppOPC.$opc_step_one.find('#customer_conf_email, #customer_conf_passwd').bind("cut copy paste", function(e) {
            e.preventDefault();
        });*/
    },
    openCMS: function(params){
        var param = $.extend({}, {
            id_cms: ''
        }, params);

        var data = {
            url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
            is_ajax: true,
            dataType: 'html',
            action: 'loadCMS',
            id_cms: param.id_cms
        };

        var _json = {
            data: data,
            beforeSend: function() {
                Fronted.loadingBig(true);
            },
            success: function(html) {
                if (!$.isEmpty(html)){
                    Fronted.showModal({name: 'cms_modal', content: html});
//                    Fronted.showModal({name: 'cms_modal', content: html, callback_close: function () {
//                        Fronted.loadingBig(false); return true;
//                    }});
                }
            },
            complete: function(){
                Fronted.loadingBig(false);
            }
        };
        $.makeRequest(_json);
    },
    loadingBig: function(show){
        if (show) {
            /*var window_height = $('#onepagecheckoutps').height();
            var window_width = $('#onepagecheckoutps').width();
            var min_top = 50;
            var max_top = (window_height - 22) - min_top;
            //var opc = $('#onepagecheckoutps').offset();

            //ubicamos el icono de loading_big
            AppOPC.$opc.find('.loading_big i').css({
                left: (window_width / 2) - 22,
                top: min_top
            });

            AppOPC.$opc.find('.loading_big').show();

            $(window).scroll(function() {
                var top = $(window).scrollTop() + min_top;

                if (top >= min_top && top <= max_top) {
                    AppOPC.$opc.find('.loading_big i').css({
                        top: top
                    });
                }
            });*/

            AppOPC.$opc.addClass('opc_overlay');

            if ($(window).width() >= 1024) {
                AppOPC.$opc.find('.loading_big').show();
            } else {
                $('body').append('<div id="opc_loading">'+OnePageCheckoutPS.Msg.processing_purchase+'<i class="fa-pts fa-pts-spin fa-pts-refresh"></i></div>');
                $('div#opc_loading').addClass('animate');
            }
        } else {
            AppOPC.$opc.removeClass('opc_overlay');

            if ($(window).width() >= 1024) {
                AppOPC.$opc.find('.loading_big').hide();
            } else {
                $('div#opc_loading').remove();
            }
        }
    },
    showModal: function(params){
        var param = $.extend({}, {
            name: 'opc_modal',
            type: 'normal',
            title: '',
            title_icon: '',
            message: '',
            content: '',
            close: true,
            button_close: false,
            size: '',
            callback: '',
            callback_close: ''
        }, params);

        $('#'+param.name).remove();

        var windows_height = $(window).height();
        //var windows_width = $(window).width();

        var parent_content = '';
        if (typeof param.content === 'object'){
            parent_content = param.content.parent();
        }

        var $modal = $('<div/>').attr({id:param.name, 'class':'modal fade', role:'dialog'});
        var $modal_dialog = $('<div/>').attr({'class':'modal-dialog ' + param.size});
        var $modal_header = $('<div/>').attr({'class':'modal-header'});
        var $modal_content = $('<div/>').attr({'class':'modal-content'});
        var $modal_body = $('<div/>').attr({'class':'modal-body'});
        var $modal_footer = $('<div/>').attr({'class':'modal-footer'});
        var $modal_button_close = $('<button/>')
                .attr({type:'button', 'class':'close'})
                .click(function(){
                    $('#'+param.name).modal('hide');
                })
                .append('<i class="fa-pts fa-pts-close"></i>');
        var $modal_button_close_footer = $('<button/>')
            .attr({type:'button', 'class':'btn btn-default'})
            .click(function(){
                $('#'+param.name).modal('hide');
            })
            .append('OK');
        var $modal_title = '';

        if (typeof param.message === 'array'){
            var message_html = '';
            $.each(param.message, function(i, message){
                message_html += '- ' + message + '<br/>';
            });
            param.message =  message_html;
        }

        if (param.type == 'error'){
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa-pts fa-pts-times-circle fa-pts-2x" style="color:red"></i>')
                .append(param.message);
        }else if (param.type == 'warning'){
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa-pts fa-pts-warning fa-pts-2x" style="color:orange"></i>')
                .append(param.message);
        }
        else{
            $modal_title = $('<span/>')
                .attr({'class':'panel-title'})
                .append(param.close ? $modal_button_close : '')
                .append('<i class="fa-pts '+param.title_icon+' fa-pts-1x"></i>')
                .append(param.title);
        }

        $modal_header.append($modal_title);
        $modal_content.append($modal_header);

        if (param.type == 'normal'){
            if (typeof param.content === 'object'){
                param.content.removeClass('hidden').appendTo($modal_body);
            }else{
                $modal_body.append(param.content);
            }

            $modal_content.append($modal_body);

            if (param.button_close){
                $modal_footer.append($modal_button_close_footer);
                $modal_content.append($modal_footer);
            }
        }

        $modal_dialog.append($modal_content);
        $modal.append($modal_dialog);

        $modal.on('hide.bs.modal', function(){
            if (!param.close){
                return false;
            } else {
                if (typeof param.callback_close !== typeof undefined && typeof param.callback_close === 'function') {
                    if (!param.callback_close()) {
                        return false;
                    }
                }

                if (!$.isEmpty(parent_content)) {
                    param.content.appendTo(parent_content).addClass('hidden');
                }

                $('body').removeClass('modal-open');
            }
        });

        $('div#onepagecheckoutps').prepend($modal);

        $('#'+param.name).modal('show');

        if (!$('#'+param.name).hasClass('in')) {
            $('#'+param.name).addClass('in').css({display : 'block'});
        }

        var paddingTop = 0
        if (windows_height > $modal_dialog.height()) {
            paddingTop = (windows_height - $modal_dialog.height()) / 2;
        }

        $('#'+param.name).css({
            paddingTop: paddingTop
        });

        Fronted.loadingBig(false);

        if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
            param.callback();

        //fix problem with module: pakkelabels_shipping
        $('.pakkelabels_modal-backdrop').remove();

        window.scrollTo(0, $('div#onepagecheckoutps').offset().top);
    },
    loginCustomer: function(){
        var email = $('#opc_login #txt_login_email').val();
        var password = $('#opc_login #txt_login_password').val();
        var login_success = false;

        var data = {
            is_ajax: true,
            action: 'loginCustomer',
            email: email,
            password: password
        };

        $('div#onepagecheckoutps #form_login').submit();

        if (AppOPC.is_valid_all_form) {
            //no its use makeRequest because dont work.. error weird.
            $.ajax({
                type: 'POST',
                url: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
                cache: false,
                dataType: 'json',
                data: data,
                beforeSend: function() {
                    $('#opc_login #btn_login').attr('disabled', 'true');
                    $('#opc_login .loading_small').show();
                    $('#opc_login .alert').empty().addClass('hidden');
                },
                success: function(json) {
                    if(json.success) {
                        $.totalStorageOPC('id_address_delivery', null);
                        $.totalStorageOPC('id_address_invoice', null);

                        if ($('div#onepagecheckoutps #onepagecheckoutps_step_review_container').length > 0) {
                            window.parent.location.reload();
                        } else {
                            if (parseInt($('.shopping_cart .ajax_cart_quantity').text()) > 0){
                                window.parent.location = prestashop.urls.current_url;
                            } else {
                                window.parent.location = prestashop.urls.base_url;
                            }
                        }

                        login_success = true;
                    } else {
                        if(json.errors){
                            $('#opc_login .alert').html('&bullet; ' + json.errors.join('<br>&bullet; ')).removeClass('hidden');
                        }
                    }
                },
                complete: function(){
                    if (!login_success) {
                        $('#opc_login #btn_login').removeAttr('disabled');
                        $('#opc_login .loading_small').hide();
                    }
                }
            });
        }
    },
    openWindow: function (url){
		var LeftPosition = (screen.width) ? (screen.width-700)/2 : 0;
		var TopPosition = (screen.height) ? (screen.height-500)/2 : 0;
		window.open(url,'','height=500,width=600,top='+(TopPosition-10)+',left='+LeftPosition+',toolbar=no,directories=no,status=no,menubar=no,modal=yes,scrollbars=yes');
	}
}

var Address = {
    id_customer: 0,
    id_address_delivery: 0,
    id_address_invoice: 0,
    delivery_vat_number: false,
    invoice_vat_number: false,
    launch: function(){
        $('div#onepagecheckoutps #field_customer_id').addClass('hidden');

        $('div#onepagecheckoutps #btn_save_customer').click(Address.createCustomer);

        //just allow lang with weird characters
        if ($.inArray(prestashop.language.iso_code, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0)
            $('#customer_firstname, #customer_lastname').validName();

        $('div#onepagecheckoutps').on('blur', '#customer_email', function(e){
            Address.checkEmailCustomer($(e.currentTarget).val());
        });

        $('div#onepagecheckoutps').on("click", "#div_privacy_policy span.read", function(){
            Fronted.openCMS({id_cms : OnePageCheckoutPS.CONFIGS.OPC_ID_CMS_PRIVACY_POLICY});
        });

        //validation brazil cpf
        if ($('#onepagecheckoutps_step_one #br_document_cpf').length > 0) {
            $('#onepagecheckoutps_step_one #br_document_cpf').attr('data-validation', 'cpf').removeClass('validate');
        }

        //evita espacios al inicio y final en los campos del registro.
        AppOPC.$opc_step_one.find('input.customer, input.delivery, input.invoice, #customer_conf_passwd, #customer_conf_email').on('paste', function(e){
            var $element = $(e.currentTarget);
            setTimeout(function () {
                $element.val($.trim($element.val()));
            }, 100);
        });

        AppOPC.$opc_step_one.find('.container_help_invoice span').click(function(){
            $('#onepagecheckoutps_step_one #li_invoice_address a').trigger('click');
        });

        if (OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL || $('div#onepagecheckoutps #onepagecheckoutps_step_two #input_virtual_carrier').length <= 0){
            if ($.inArray(prestashop.language.iso_code, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0){
                $('div#onepagecheckoutps #delivery_firstname, div#onepagecheckoutps #delivery_lastname').validName();
                $('div#onepagecheckoutps #delivery_address1, div#onepagecheckoutps #delivery_address2, div#onepagecheckoutps #delivery_city').validAddress();
            }

            if (!OnePageCheckoutPS.IS_LOGGED) {
                $('div#onepagecheckoutps #field_delivery_id').addClass('hidden');
            }

			//en el caso que el id_country este desactivado
			if ($('div#onepagecheckoutps select#delivery_id_country').length <= 0) {
                Address.updateState({object: 'delivery', id_country: OnePageCheckoutPS.id_country_delivery_default});
            }

            Address.initPostCodeGeonames({object: 'delivery'});

            $('div#onepagecheckoutps')
                .on('change', '#delivery_city', function(){
                    $('#delivery_city_list').val('');
                })
                .on('change', 'select#delivery_id_state', function(event){
                    Address.getCitiesByState({object: 'delivery'});

                    if (OnePageCheckoutPS.CONFIGS.OPC_RELOAD_SHIPPING_BY_STATE) {
                        Carrier.getByCountry();
                    }
                })
                .on('change', 'select#delivery_id_country', function(event){
                    Address.isNeedDniByCountryId({object: 'delivery'});
                    Address.isNeedPostCodeByCountryId({object: 'delivery'});
                    Address.updateState({object: 'delivery', id_country: $(event.currentTarget).val()});
                    Address.initPostCodeGeonames({object: 'delivery'});
                    Carrier.getByCountry();

                    if (typeof event.originalEvent !== typeof undefined && AppOPC.$opc.find('input#delivery_postcode').length > 0 && !$.isEmpty(AppOPC.$opc.find('input#invoice_postcode').val())) {
                        AppOPC.$opc.find('input#delivery_postcode').validate();
                    }

                    if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_LIST_CITIES_GEONAMES) {
                        $('#onepagecheckoutps_step_one #delivery_city_list').empty().hide();
                        $('#onepagecheckoutps_step_one #delivery_city').val('');
                    }

                    Address.loadAutocompleteAddress();
                })
                .on('change', 'select#delivery_id', function(e){
                    if (!$.isEmpty($(e.currentTarget).val()))
                        Address.load({object: 'delivery'});
                    else{
                        Address.createAddressAjax({object: 'delivery'});

                        //Address.clearFormByObject('delivery');
                        //Carrier.update({load_carriers: true});
                    }
                })
                .on('click', 'input#checkbox_create_account_guest', Address.checkGuestAccount)
                .on('click', 'input#checkbox_create_account', Address.checkGuestAccount);

            Address.checkGuestAccount();
            Address.isNeedDniByCountryId({object: 'delivery'});
            Address.isNeedPostCodeByCountryId({object: 'delivery'});
            Address.getCityByPostCode({object: 'delivery'});

            //$("div#onepagecheckoutps select#delivery_id option[value='" + OnePageCheckoutPS.id_address_delivery + "']").attr('selected', 'selected');
        }

        if(OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS){
            if (typeof $.totalStorageOPC !== typeof undefined) {
                if ($.totalStorageOPC('create_invoice_address')) {
                    $('div#onepagecheckoutps #checkbox_create_invoice_address').attr('checked', 'true');
                }
            }

            if ($.inArray(prestashop.language.iso_code, OnePageCheckoutPS.LANG_ISO_ALLOW) == 0){
                $('div#onepagecheckoutps #invoice_firstname, div#onepagecheckoutps #invoice_lastname').validName();
                $('div#onepagecheckoutps #invoice_address1, div#onepagecheckoutps #invoice_address2, div#onepagecheckoutps #invoice_city').validAddress();
            }

            if (!OnePageCheckoutPS.IS_LOGGED) {
                $('div#onepagecheckoutps #field_invoice_id').addClass('hidden');
            }

            if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS) {
                Address.checkNeedInvoice();

                $('div#onepagecheckoutps').on('click', 'input#checkbox_create_invoice_address', function(event){
                    Address.checkNeedInvoice();

                    if ($(event.currentTarget).is(':checked')) {
                        Address.updateAddressInvoice();
                    } else {
                        Address.removeAddressInvoice();
                    }
                });
            }

			//en el caso que el id_country este desactivado
			if ($('div#onepagecheckoutps select#invoice_id_country').length <= 0) {
				Address.updateState({object: 'invoice', id_country: OnePageCheckoutPS.id_country_invoice_default});
            }

            Address.initPostCodeGeonames({object: 'invoice'});

            $('div#onepagecheckoutps')
                .on('change', '#invoice_city', function(){
                    $('#invoice_city_list').val('');
                })
                .on('change', 'select#invoice_id_state', function(){
                    Address.getCitiesByState({object: 'invoice'});
                    Address.updateAddressInvoice();
                })
                .on('change', 'select#invoice_id_country', function(event){
                    Address.isNeedDniByCountryId({object: 'invoice'});
                    Address.isNeedPostCodeByCountryId({object: 'invoice'});
                    Address.updateState({object: 'invoice', id_country: $(event.currentTarget).val()});
                    Address.updateAddressInvoice();
                    Address.initPostCodeGeonames({object: 'invoice'});

                    if (typeof event.originalEvent !== typeof undefined && AppOPC.$opc.find('input#invoice_postcode').length > 0 && !$.isEmpty(AppOPC.$opc.find('input#invoice_postcode').val())) {
                        AppOPC.$opc.find('input#invoice_postcode').validate();
                    }

                    if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_LIST_CITIES_GEONAMES) {
                        $('#onepagecheckoutps_step_one #invoice_city_list').empty().hide();
                        $('#onepagecheckoutps_step_one #invoice_city').val('');
                    }

                    Address.loadAutocompleteAddress();
                })
                .on('change', 'select#invoice_id', function(e){
                    if (!$.isEmpty($(e.currentTarget).val())) {
                        Address.load({object: 'invoice'});
                    } else {
                        Address.createAddressAjax({object: 'invoice'});
                        //Address.clearFormByObject('invoice');
                    }
                });

			Address.isNeedDniByCountryId({object: 'invoice'});
			Address.isNeedPostCodeByCountryId({object: 'invoice'});
			Address.getCityByPostCode({object: 'invoice'});
        }

        Address.load();
    },
    initPostCodeGeonames: function(params){
        var param = $.extend({}, {
            object: 'delivery'
        }, params);

        if (OnePageCheckoutPS.CONFIGS.OPC_AUTO_ADDRESS_GEONAMES && AppOPC.$opc_step_one.find('#'+param.object+'_postcode').length > 0){
            var $id_country = $('#onepagecheckoutps_step_one #'+param.object+'_id_country');
            var iso_code_country = '';

            if ($id_country.length > 0) {
                iso_code_country = $id_country.find('option:selected').data('iso-code');
            } else {
                iso_code_country = OnePageCheckoutPS.iso_code_country_delivery_default;
            }

            $('#onepagecheckoutps_step_one #'+param.object+'_postcode').jeoPostCodeAutoComplete({
                country: iso_code_country,
                callback: function(data){
                    $('#onepagecheckoutps_step_one #'+param.object+'_postcode').val(data.postalCode);
                    $('#onepagecheckoutps_step_one #'+param.object+'_city_list').val(data.name);
                    $('#onepagecheckoutps_step_one #'+param.object+'_city').val(data.name);

                    if ($('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-text="'+data.adminName2+'"]').length <= 0) {
                        $('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-iso-code="'+data.countryCode + '-' + data.adminCode2+'"]').attr('selected', 'true');
                    } else {
                        $('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-text="'+data.adminName2+'"]').attr('selected', 'true');
                    }

                    if (typeof is_necessary_postcode !== typeof undefined && is_necessary_postcode) {
                        $('#onepagecheckoutps_step_one #'+param.object+'_postcode').trigger('blur');
                    } else if(typeof is_necessary_city !== typeof undefined && is_necessary_city) {
                        $('#onepagecheckoutps_step_one #'+param.object+'_city').trigger('blur');
                    }

                    if (typeof is_necessary_postcode !== typeof undefined
                        && !is_necessary_postcode
                        && typeof is_necessary_postcode !== typeof undefined
                        && !is_necessary_postcode)
                    {
                        $('#onepagecheckoutps_step_one #'+param.object+'_id_state [data-text="'+data.adminName2+'"]').trigger('change');
                    }
                }
            });
        }
    },
    getCityByPostCode: function(params){
        var param = $.extend({}, {
            object: 'delivery'
        }, params);

        if (1==2) {
            var $city_list = $('#onepagecheckoutps_step_one #'+param.object+'_city_list');

            if ($city_list.length <= 0 || ($city_list.length > 0 && !$city_list.is(':visible'))) {
                var $id_country = $('#onepagecheckoutps_step_one #'+param.object+'_id_country');
                var $postcode = $('#onepagecheckoutps_step_one #'+param.object+'_postcode');
                var $city = $('#onepagecheckoutps_step_one #'+param.object+'_city');

                if ($postcode.length > 0 && $city.length > 0) {
                    $postcode.jeoPostalCodeLookup({
                        country: $id_country.find('option:selected').data('iso-code'),
                        target: $city
                    });
                }
            }
        }
    },
    getCitiesByState: function(params){
        var param = $.extend({}, {
            object: 'delivery'
        }, params);

        if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_LIST_CITIES_GEONAMES) {
            var $id_country = $('#onepagecheckoutps_step_one #'+param.object+'_id_country');
            var $id_state = $('#onepagecheckoutps_step_one #'+param.object+'_id_state');
            var iso_code_country = '';

            if ($id_country.length > 0) {
                iso_code_country = $id_country.find('option:selected').data('iso-code');
            } else {
                iso_code_country = OnePageCheckoutPS.iso_code_country_delivery_default;
            }

            var name_state = $.trim($id_state.find('option:selected').data('text'));

            if ($id_state.length > 0 && !$.isEmpty(name_state)) {
                var cities = Array();
                var current_city = $('#onepagecheckoutps_step_one #'+param.object+'_city').val();

                jeoquery.getGeoNames(
                  'search',
                  {
                      q: name_state,
                      country: iso_code_country,
                      featureClass: 'P',
                      style: 'full'
                  },
                  function(data){
                    //ordenar array de objetos por una propiedad en especifico
                    function dynamicSort(property) {
                        var sortOrder = 1;
                        if(property[0] === "-") {
                            sortOrder = -1;
                            property = property.substr(1);
                        }
                        return function (a,b) {
                            var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
                            return result * sortOrder;
                        }
                    }

                    $.each(data.geonames, function(i, item){
                        if ($.inArray(item.name, cities) == -1) {
                            cities.push({name: $.trim(item.name), postcode: item.adminCode3});
                        }
                    });
                    cities.sort(dynamicSort('name'));

                    var $city_list = $('#onepagecheckoutps_step_one #'+param.object+'_city_list');
                    if ($city_list.length <= 0) {
                        $city_list = $('<select/>')
                            .attr({
                                id: param.object+'_city_list',
                                class: 'form-control input-sm not_unifrom not_uniform'
                            })
                            .on('change', function(event){
                                var option_selected = $(event.currentTarget).find('option:selected');

                                $('#onepagecheckoutps_step_one #'+param.object+'_city').val($(option_selected).attr('value')).trigger('blur');
                                $('#onepagecheckoutps_step_one #'+param.object+'_postcode').val($(option_selected).attr('data-postcode'));
                            }
                        );
                    } else {
                        $city_list.html('').show();
                    }

                    var $option = $('<option/>')
                        .attr({
                            value: ''
                        }).append('--');
                    $option.appendTo($city_list);
                    $.each(cities, function(i, city) {
                        var $option = $('<option/>')
                            .attr({
                                'value': city.name,
                                'data-postcode': city.postcode
                            }).append(city.name);

                        if (city == current_city) {
                            $option.attr('selected', 'true');
                        }

                        $option.appendTo($city_list);
                    });
                    $('#onepagecheckoutps_step_one #field_'+param.object+'_city').append($city_list);
                });
            } else {
                $('#onepagecheckoutps_step_one #'+param.object+'_city_list').hide();
            }
        }
    },
    loadAddressesCustomer: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        var data = {
            url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
            is_ajax: true,
            action: 'loadAddressesCustomer'
        };
        var _json = {
            data: data,
            success: function(json) {
                if(typeof json.addresses !== typeof undefined){
                    $delivery_id = $('div#onepagecheckoutps #delivery_id');
                    $invoice_id = $('div#onepagecheckoutps #invoice_id');

                    if ($delivery_id.length > 0){
                        if (OnePageCheckoutPS.IS_LOGGED && OnePageCheckoutPS.IS_GUEST) {
                            $('#onepagecheckoutps_step_one #field_delivery_id').parent().hide();
                        } else {
                            $delivery_id.find('option').prop('selected', false)
                            $delivery_id.find('option:not(:first)').remove();

                            $.each(json.addresses, function(i, address){
                                var $option = $('<option/>')
                                    .attr({
                                        value: address.id_address,
                                    }).append(address.alias);

                                if (json.id_address_delivery == address.id_address){
                                    $option.prop('selected', true);
                                }

                                $option.appendTo($delivery_id);
                            });

                            if (typeof $.totalStorageOPC !== typeof undefined) {
                                var id_address_delivery = $.totalStorageOPC('id_address_delivery');
                                if (id_address_delivery) {
                                    var $option = $('div#onepagecheckoutps #delivery_id option[value='+ id_address_delivery +']');

                                    if ($option.length > 0) {
                                        $option.attr('selected', 'true');
                                    }
                                }
                            }
                        }
                    }

                    if ($invoice_id.length > 0){
                        if (OnePageCheckoutPS.IS_LOGGED && OnePageCheckoutPS.IS_GUEST) {
                            $('#onepagecheckoutps_step_one #field_invoice_id').parent().hide();
                        } else {
                            $invoice_id.find('option:not(:first)').remove();

                            $.each(json.addresses, function(i, address){
                                var $option = $('<option/>')
                                    .attr({
                                        value: address.id_address,
                                    }).append(address.alias);

                                if (json.id_address_invoice == address.id_address){
                                    $option.prop('selected', true);
                                }

                                $option.appendTo($invoice_id);
                            });

                            if (typeof $.totalStorageOPC !== typeof undefined) {
                                var id_address_invoice = $.totalStorageOPC('id_address_invoice');
                                if (id_address_invoice) {
                                    var $option = $('div#onepagecheckoutps #invoice_id option[value='+ id_address_invoice +']');

                                    if ($option.length > 0) {
                                        $option.attr('selected', 'true');
                                    }
                                }
                            }
                        }
                    }
                }
            },
            complete: function() {
                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                    param.callback();
                }
            }
        };
        $.makeRequest(_json);
    },
    createAddressAjax: function(params){
        var param = $.extend({}, {
            callback: '',
            object: 'delivery'
        }, params);

        var data = {
            url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
            is_ajax: true,
            dataType : 'html',
            action: 'createAddressAjax',
            object: param.object
        };
        var _json = {
            data: data,
            success: function(id_address) {
                if (!$.isEmpty(id_address)) {
                    if (typeof $.totalStorageOPC !== typeof undefined) {
                        if (param.object == 'delivery') {
                            $.totalStorageOPC('id_address_delivery', id_address)
                        }
                        if (param.object == 'invoice') {
                            $.totalStorageOPC('id_address_invoice', id_address)
                        }
                    }

                    var callback = function() {
                        Address.clearFormByObject(param.object);
                    }

                    Address.loadAddressesCustomer({callback: callback});
                }
            },
            complete: function() {
                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                    param.callback();
                }
            }
        };
        $.makeRequest(_json);
    },
    createCustomer: function(){
        //validate fields
        $('div#onepagecheckoutps #form_onepagecheckoutps').submit();

        //cuando hay informacion sin llenar en facturacion, para que el usuario sepa que hay que llenarlo, se ubica en esa seccion
        if (!AppOPC.is_valid_all_form) {
            if ($('#delivery_address_container .required.has-error').length == 0 && $('#invoice_address_container .required.has-error').length > 0) {
                $('#onepagecheckoutps_step_one #li_delivery_address .nav-link, #onepagecheckoutps_step_one #delivery_address_container').removeClass('active');
                $('#onepagecheckoutps_step_one #li_invoice_address .nav-link, #onepagecheckoutps_step_one #invoice_address_container').removeClass('active');

                $('#onepagecheckoutps_step_one #li_invoice_address .nav-link, #onepagecheckoutps_step_one #invoice_address_container').addClass('active');
            }
        }

        //privacy policy
        if (AppOPC.is_valid_all_form && OnePageCheckoutPS.CONFIGS.OPC_ENABLE_PRIVACY_POLICY && !OnePageCheckoutPS.IS_LOGGED && !OnePageCheckoutPS.IS_GUEST && !$('div#onepagecheckoutps #onepagecheckoutps_step_one #privacy_policy').is(':checked')){
            $('div#onepagecheckoutps #onepagecheckoutps_step_one #div_privacy_policy').addClass('alert alert-warning');

            Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

            AppOPC.is_valid_all_form = false;
        }

        if (AppOPC.is_valid_all_form){
            var invoice_id = '';
            var fields = Review.getFields();

            if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS && $('div#onepagecheckoutps #checkbox_create_invoice_address').length > 0){
                if ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked')){
                    invoice_id = $('#invoice_id').val();
                }
            }else{
                invoice_id = $('#invoice_id').val();
            }

            var _extra_data = Review.getFieldsExtra({});
            var _data = $.extend({}, _extra_data, {
                'url_call'				: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
                'is_ajax'               : true,
                'dataType'              : 'json',
                'action'                : (OnePageCheckoutPS.IS_LOGGED ? 'placeOrder' : 'createCustomerAjax'),
                'id_customer'           : (!$.isEmpty(AppOPC.$opc_step_one.find('#customer_id').val()) ? AppOPC.$opc_step_one.find('#customer_id').val() : ''),
                'id_address_delivery'   : (!$.isEmpty(AppOPC.$opc_step_one.find('#delivery_id').val()) ? AppOPC.$opc_step_one.find('#delivery_id').val() : ''),
                'id_address_invoice'    : invoice_id,
                'is_new_customer'       : (AppOPC.$opc_step_one.find('#checkbox_create_account_guest').is(':checked') ? 0 : 1),
                'fields_opc'            : JSON.stringify(fields),
            });

            var _json = {
                data: _data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').show();
                },
                success: function(data) {
                    if (data.isSaved && (!OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked'))){
                        AppOPC.$opc_step_one.find('#customer_id').val(data.id_customer);
                        AppOPC.$opc_step_one.find('#customer_email, #customer_conf_email, #customer_passwd, #customer_conf_passwd').attr({'disabled': 'true', 'data-validation-optional' : 'true'});

                        $('#div_onepagecheckoutps_login, #field_customer_passwd, #field_customer_conf_passwd, #field_customer_email, #field_customer_conf_email, div#onepagecheckoutps #onepagecheckoutps_step_one_container .account_creation, #field_choice_group_customer').addClass('hidden');
                    }

                    if (data.hasError){
                        Fronted.showModal({type:'error', message : '&bullet; ' + data.errors.join('<br>&bullet; ')});
                    }else{
                        if (typeof $.totalStorageOPC !== typeof undefined) {
                            $.totalStorageOPC('id_address_delivery', data.id_address_delivery);
                            $.totalStorageOPC('id_address_invoice', data.id_address_invoice);
                        }

                        if (!OnePageCheckoutPS.IS_LOGGED && !OnePageCheckoutPS.IS_GUEST) {
                            if (prestashop.cart.products_count > 0){
                                window.parent.location = prestashop.urls.current_url;
                            } else {
                                window.parent.location = prestashop.urls.pages.my_account;
                            }

                            $('div#onepagecheckoutps #btn_save_customer').attr('disabled', 'true');
                        } else {
                            var callback = function(){
                                if (!OnePageCheckoutPS.IS_VIRTUAL_CART) {
                                    Carrier.getByCountry();
                                } else {
                                    Payment.getByCountry();
                                }
                            };

                            Address.loadAddressesCustomer({callback: callback});
                        }
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').hide();
                }
            };
            $.makeRequest(_json);
        }
    },
    load: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        var loaded = false;

        if (!$.isEmpty($("#delivery_id").val())) {
            Address.id_address_delivery = $("#delivery_id").val();
            $.totalStorageOPC('id_address_delivery', Address.id_address_delivery);
        } else {
            if (typeof $.totalStorageOPC !== typeof undefined) {
                if ($.totalStorageOPC('id_address_delivery')) {
                    Address.id_address_delivery = $.totalStorageOPC('id_address_delivery');
                }
            }
        }

        if (!$.isEmpty($("#invoice_id").val())) {
            Address.id_address_invoice = $("#invoice_id").val();
            $.totalStorageOPC('id_address_invoice', Address.id_address_invoice);
        } else {
            if (typeof $.totalStorageOPC !== typeof undefined) {
                if ($.totalStorageOPC('id_address_invoice')) {
                    Address.id_address_invoice = $.totalStorageOPC('id_address_invoice');
                }
            }
        }

        var callback = function(){
            Address.getCitiesByState({object: 'delivery'});
            if ($('#onepagecheckoutps_step_one_container #invoice_address_container').length > 0) {
                Address.getCitiesByState({object: 'invoice'});
            }

            if (is_virtual_cart && !loaded){
                if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_DELIVERY_VIRTUAL && $('div#onepagecheckoutps #delivery_id_country').length > 0){
                    $('div#onepagecheckoutps #delivery_id_country').trigger('change');
                }else{
                    Payment.getByCountry();
                }
            }else{
                if ($('div#onepagecheckoutps #delivery_id_country').length > 0 && !OnePageCheckoutPS.IS_LOGGED){
                    $('div#onepagecheckoutps #delivery_id_country').trigger('change');
                } else {
                    if (!is_virtual_cart)
                        Carrier.getByCountry();
                }
            }

            Address.loadAutocompleteAddress();
        }

        if(OnePageCheckoutPS.IS_LOGGED || OnePageCheckoutPS.IS_GUEST){
            var data = {
                url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'loadAddress',
                delivery_id: Address.id_address_delivery,
                invoice_id: Address.id_address_invoice,
                is_set_invoice: AppOPC.$opc_step_one.find('input#checkbox_create_invoice_address').is(':checked')
            };
            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').show();
                },
                success: function(json) {
                    if(!json.hasError && (!$.isEmpty(json.customer.id) || !$.isEmpty(json.address_delivery.id) || !$.isEmpty(json.address_invoice.id))){
                        Address.id_address_delivery = $.isEmpty(json.address_delivery.id) ? 0 : json.address_delivery.id;
                        Address.id_address_invoice = $.isEmpty(json.address_invoice.id) ? 0 : json.address_invoice.id;
                        Address.id_customer = $.isEmpty(json.customer.id) ? 0 : json.customer.id;

                        if ($('div#onepagecheckoutps #delivery_id option').length <= 1) {
                            Address.loadAddressesCustomer();
                        }

                        var object_load = '.customer, '+(param.object == '' ? '.delivery, .invoice' : '.' + param.object);

                        //load customer, delivery or invoice data
                        $('div#onepagecheckoutps #onepagecheckoutps_step_one').find(object_load).each(function(i, field){
                            var $field = $(field);
                            var name = $field.attr('data-field-name');
                            var default_value = $field.attr('data-default-value');
                            var object = '';

                            if ($field.hasClass('customer')){
                                var value = json.customer[name];
                                object = 'customer';
                            }else if ($field.hasClass('delivery')){
                                var value = json.address_delivery[name];
                                object = 'delivery';
                            }else if ($field.hasClass('invoice')){
                                var value = json.address_invoice[name];
                                object = 'invoice';
                            }

                            $check_invoice = $('div#onepagecheckoutps input#checkbox_create_invoice_address');
                            if (object == 'invoice' && !OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS && !$check_invoice.is(':checked')){
								$('div#onepagecheckoutps #onepagecheckoutps_step_one #invoice_id').val('');
                                return;
							}

                            if (/*name == 'id_country' || */name == 'id_state') {
                                return;
                            }

							if (value == '0000-00-00')
                                value = '';

                            if ($field.is(':checkbox')){
                                if (parseInt(value))
                                    $field.attr('checked', 'true');
                                else
                                    $field.removeAttr('checked');
                            }else if ($field.is(':radio')){
                                if ($field.val() == value)
                                    $field.attr('checked', 'true');
                            }else{
                                if (name == 'birthday'){
                                    var date_value = value.split('-');
                                    var date_string = OnePageCheckoutPS.date_format_language.replace('dd', date_value[2]);
                                    date_string = date_string.replace('mm', date_value[1]);
                                    date_string = date_string.replace('yy', date_value[0]);

                                    $field.val(date_string);
                                }else{
                                    $field.val(value);
                                }

                                //do not show values by default on input text
                                if ($field.is(':text'))
                                    if (value == default_value)
                                        $field.val('');
                            }

                            if (name == 'email'){
                                if ((OnePageCheckoutPS.IS_LOGGED && !OnePageCheckoutPS.IS_GUEST) || !OnePageCheckoutPS.PRESTASHOP.CONFIGS.PS_GUEST_CHECKOUT_ENABLED){
                                    $field.attr('disabled', 'true').addClass('disabled');
                                }else{
                                    $('div#onepagecheckoutps #onepagecheckoutps_step_one #customer_conf_email').val($field.val());
                                }
                            }
                        });

                        Address.isNeedDniByCountryId({object: 'delivery'});
                        Address.updateState({object: 'delivery', id_state_default: json.address_delivery['id_state']});
                        Address.isNeedDniByCountryId({object: 'invoice'});
                        Address.updateState({object: 'invoice', id_state_default: json.address_invoice['id_state']});

                        if (OnePageCheckoutPS.IS_VIRTUAL_CART){
                            Payment.getByCountry();

                            loaded = true;
                        }
                    } else {
                        if (json.hasError){
                            Fronted.showModal({type:'error', message : json.errors});
                        } else if (json.hasWarning){
                            Fronted.showModal({type:'warning', message : json.warnings});
                        }
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').hide();

                    callback();
                }
            };
            $.makeRequest(_json);
        } else {
            callback();
        }
    },
    loadAutocompleteAddress: function() {
        if (OnePageCheckoutPS.CONFIGS.OPC_AUTOCOMPLETE_GOOGLE_ADDRESS && !$.isEmpty(OnePageCheckoutPS.CONFIGS.OPC_GOOGLE_API_KEY) && typeof google.maps.places !== typeof undefined) {
            if ($('#delivery_address1').length > 0)
            {
                var iso_code_country = null;
                var $id_country = $('div#onepagecheckoutps select#delivery_id_country');

                if ($id_country.length > 0) {
                    iso_code_country = $id_country.find('option:selected').data('iso-code');
                } else {
                    iso_code_country = OnePageCheckoutPS.iso_code_country_delivery_default;
                }

                Address.autocomplete_delivery = new google.maps.places.Autocomplete(
                    (document.getElementById('delivery_address1')),
                    {types: ['geocode'], componentRestrictions: {country: iso_code_country}}
                );
                google.maps.event.addListener(Address.autocomplete_delivery, 'place_changed', function() {
                    Address.fillInAddress('delivery', Address.autocomplete_delivery);
                });
            }

            if ($('#invoice_address1').length > 0)
            {
                var iso_code_country = null;
                var $id_country = $('div#onepagecheckoutps select#invoice_id_country');

                if ($id_country.length > 0) {
                    iso_code_country = $id_country.find('option:selected').data('iso-code');
                } else {
                    iso_code_country = OnePageCheckoutPS.iso_code_country_invoice_default;
                }

                Address.autocomplete_invoice = new google.maps.places.Autocomplete(
                    (document.getElementById('invoice_address1')),
                    {types: ['geocode'], componentRestrictions: {country: iso_code_country}}
                );

                google.maps.event.addListener(Address.autocomplete_invoice, 'place_changed', function() {
                    Address.fillInAddress('invoice', Address.autocomplete_invoice);
                });
            }
        }
    },
    fillInAddress: function(address, autocomplete) {
        Address.componentForm = {
            postal_code: {index: 0, type: 'long_name', field: address + '_postcode'},
            locality: {index: 1, type: 'long_name', field: address + '_city'},
            administrative_area_level_1: {index: 2, type: 'select', field: address + '_id_state'},
            administrative_area_level_2: {index: 3, type: 'select', field: address + '_id_state'},
            administrative_area_level_3: {index: 4, type: 'select', field: address + '_id_state'},
            country: {index: 5, type: 'select', field: address + '_id_country'},
            /*street_number: {index: 6, type: 'short_name', field: address + '_address1'},
            route: {index: 7, type: 'long_name', field: address + '_address1'},
            premise: {index: 8, type: 'short_name', field: address + '_address1'}*/
        };

        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        //reset
        $.each(Address.componentForm, function(c, component) {
            if (component.type !== 'select' && component.field != (address + '_address1')) {
                $('#' + component.field).val('');
            }
        });

        var components = [];
        var components_state = [];

        $.each(place.address_components, function(a, component) {
            if (typeof Address.componentForm[component.types[0]] !== typeof undefined) {
                var field = Address.componentForm[component.types[0]].field;
                var type = Address.componentForm[component.types[0]].type;
                var index = Address.componentForm[component.types[0]].index;

                components[index] = {
                    field: field,
                    type: type,
                    name: component.types[0],
                    short_name: component.short_name,
                    long_name: component.long_name,
                    value: (typeof component[type] !== typeof undefined) ? component[type] : component.long_name
                };
            }
        });

        $.each(components, function(c, component) {
            if (typeof component !== typeof undefined) {
                if (component.type === 'select') {
                    if (component.name === 'country') {
                        $('#' + address + '_id_country option').prop('selected', false);
                        $('#' + address + '_id_country option[data-iso-code="' + component.short_name + '"]').prop('selected', true);
                        $('#' + address + '_id_country').trigger('change');
                        Address.getCitiesByState({object: address});
                    } else if (typeof $('#' + address + '_id_state')[0] !== typeof undefined) {
                        components_state.push(component)

                        Address.callBackState = function() {
                            var id_state = '';

                            $.each(components_state, function(c, component_state) {
                                if ($('#' + address + '_id_state option[data-iso-code="' + component_state.short_name + '"]').length > 0) {
                                    id_state = $('#' + address + '_id_state option[data-iso-code="' + component_state.short_name + '"]').val();

                                    return false;
                                }else if ($('#' + address + '_id_state option[data-text="' + component_state.value + '"]').length > 0) {
                                    id_state = $('#' + address + '_id_state option[data-text="' + component_state.value + '"]').val();

                                    return false;
                                }
                            });
                            $('#' + address + '_id_state option').prop('selected', false);
                            $('#' + address + '_id_state').val(id_state);
                        }
                    }
                } else {
                    if (component.field != (address + '_address1')) {
                        $('#' + component.field).val(component.value);
                    }
                }
            }
        });

        //dispatch inputs events
        if (typeof is_necessary_postcode !== typeof undefined && is_necessary_postcode) {
            $('#onepagecheckoutps_step_one #'+address+'_postcode').trigger('blur');
        } else if(typeof is_necessary_city !== typeof undefined && is_necessary_city) {
            $('#onepagecheckoutps_step_one #'+address+'_city').trigger('blur');
        }
    },
    updateAddressInvoice: function(params){
        var param = $.extend({}, {
            callback: '',
            load_review: true
        }, params);

        if (OnePageCheckoutPS.PRESTASHOP.CONFIGS.PS_TAX_ADDRESS_TYPE == 'id_address_invoice' || (is_virtual_cart && ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked') || OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS))){
            var data = {
                url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'updateAddressInvoice',
                dataType: 'html'
            };

            if ($('div#onepagecheckoutps #invoice_id_country').length > 0)
                data['id_country'] = $('div#onepagecheckoutps #invoice_id_country').val();

            if ($('div#onepagecheckoutps #invoice_id_state').length > 0)
                data['id_state'] = $('div#onepagecheckoutps #invoice_id_state').val();

            if ($('div#onepagecheckoutps #invoice_postcode').length > 0)
                data['postcode'] = $('div#onepagecheckoutps #invoice_postcode').val();

            if ($('div#onepagecheckoutps #invoice_city').length > 0)
                data['city'] = $('div#onepagecheckoutps #invoice_city').val();

            if ($('div#onepagecheckoutps #invoice_id').length > 0)
                data['id_address_invoice'] = $('div#onepagecheckoutps #invoice_id').val();

            if ($('div#onepagecheckoutps #invoice_vat_number').length > 0)
                data['vat_number'] = $('div#onepagecheckoutps #invoice_vat_number').val();

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').show();
                },
                success: function() {
                    Carrier.getByCountry();
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').hide();

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }
    },
    removeAddressInvoice: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        if (!$('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked')){
            var data = {
                url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'removeAddressInvoice',
                dataType: 'html'
            };

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').show();
                },
                success: function() {
                    Carrier.getByCountry();
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_one_container .loading_small').hide();

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }
    },
    geolocate: function(event) {
        $(event.currentTarget).off('focus');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = new google.maps.LatLng(
                position.coords.latitude, position.coords.longitude);
                autocomplete.setBounds(new google.maps.LatLngBounds(geolocation,
                    geolocation));
            });
        }
    },
    updateState: function(params) {
        var param = $.extend({}, {
            object: '',
            id_state_default: '',
            id_country: ''
        }, params);

        var states = null;
        if (!$.isEmpty(param.object)) {
            var $id_country = $('div#onepagecheckoutps select#' + param.object + '_id_country');
            var $id_state = $('div#onepagecheckoutps select#' + param.object + '_id_state');
            var id_country = null;

            if ($id_country.length > 0) {
                id_country = $id_country.val();
            } else {
                if (param.object == 'delivery') {
                    id_country = OnePageCheckoutPS.id_country_delivery_default;
                } else if (param.object == 'invoice') {
                    id_country = OnePageCheckoutPS.id_country_invoice_default;
                }
            }

			var states = countries[id_country];

            //delete states
            $id_state.find('option').remove();

            if (!$.isEmpty(states)) {
                //empty option
                var $option = $('<option/>')
                    .attr({
                        value: '',
                    }).append('--');
                 $option.appendTo($id_state);

                $.each(states, function(i, state) {
                    var $option = $('<option/>')
                        .attr({
                            'data-text': state.name,
                            'data-iso-code': state.iso_code,
                            value: state.id,
                        }).append(state.name);

                    if (param.id_state_default == state.id) {
                        $option.attr('selected', 'true');
                    }

                    $option.appendTo($id_state);
                });

                if (typeof Address.callBackState === 'function') {
                    Address.callBackState();
                } else {
                    //auto select state.
                    if ($.isEmpty($id_state.find('option:selected').val())){
                        var default_value = $id_state.attr('data-default-value');

                        if (default_value != '0') {
                            //$id_state.val(default_value);
                        } else {
                            $id_state.find(':eq(1)').attr('selected', 'true');
                        }
                    }
                }

                if (param.object == 'delivery' || (param.object == 'invoice' && ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked') || OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS))){
                    $id_state.attr('data-validation', 'required').addClass('required');
                }
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').find('sup').html('*');
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').show();
            }
            else {
                $id_state.removeAttr('data-validation').removeClass('required');
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').find('sup').html('');
                $('div#onepagecheckoutps #field_' + param.object + '_id_state').hide();
            }
        }
    },
    checkNeedInvoice: function(){
        if ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked') || OnePageCheckoutPS.CONFIGS.OPC_REQUIRED_INVOICE_ADDRESS){
            Address.isNeedDniByCountryId({object: 'invoice'});
            Address.updateState({object: 'invoice'});

            $('div#onepagecheckoutps #invoice_address_container .fields_container div.lock_controls').remove();

            $('div#onepagecheckoutps #invoice_address_container .invoice.required').each(function(i, item){
                $(item).removeAttr('data-validation-optional');
            });

            if (typeof $.totalStorageOPC !== typeof undefined) {
                $.totalStorageOPC('create_invoice_address', true);
            }
        }else{
            $('div#onepagecheckoutps #invoice_address_container .fields_container').prepend('<div class="lock_controls"></div>');

            $('div#onepagecheckoutps #invoice_address_container .invoice.required').each(function(i, item){
                $(item).attr('data-validation-optional', 'true').trigger('reset');
            });

            if (typeof $.totalStorageOPC !== typeof undefined) {
                $.totalStorageOPC('create_invoice_address', false);
            }
        }
    },
    checkGuestAccount: function(){
        if (OnePageCheckoutPS.PRESTASHOP.CONFIGS.PS_GUEST_CHECKOUT_ENABLED){
            if ($('div#onepagecheckoutps #checkbox_create_account_guest').is(':checked')){
                $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                    .fadeIn()
                    .addClass('required');
                $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('*');
                $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').removeAttr('data-validation-optional').val('');
            }else{
                $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                    .fadeOut()
                    .removeClass('required')
                    .trigger('reset');
                $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('');
                $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').attr('data-validation-optional', 'true');
            }
        }else{
            if (OnePageCheckoutPS.CONFIGS.OPC_REQUEST_PASSWORD && OnePageCheckoutPS.CONFIGS.OPC_OPTION_AUTOGENERATE_PASSWORD){
                if ($('div#onepagecheckoutps #checkbox_create_account').is(':checked')){
                    $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                        .fadeIn()
                        .addClass('required');
                    $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('*');
                    $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').removeAttr('data-validation-optional').val('');
                }else{
                    $('div#onepagecheckoutps #field_customer_passwd, div#onepagecheckoutps #field_customer_conf_passwd')
                        .fadeOut()
                        .removeClass('required')
                        .trigger('reset');
                    $('div#onepagecheckoutps #field_customer_passwd sup, div#onepagecheckoutps #field_customer_conf_passwd sup').html('');
                    $('div#onepagecheckoutps #customer_passwd, div#onepagecheckoutps #customer_conf_passwd').attr('data-validation-optional', 'true');
                }
            }
        }
    },
    isNeedDniByCountryId: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        if (!$.isEmpty(param.object)){
            var id_country = null;
            var $id_country = $('#onepagecheckoutps_step_one select#' + param.object + '_id_country');

            if ($id_country.length > 0) {
                id_country = $id_country.val();
            } else {
                if (param.object == 'delivery') {
                    id_country = OnePageCheckoutPS.id_country_delivery_default;
                } else if (param.object == 'invoice') {
                    id_country = OnePageCheckoutPS.id_country_invoice_default;
                }
            }

            if (!$.isEmpty(id_country) && typeof countries !== typeof undefined && $('#field_' + param.object + '_dni').length > 0){
                if (countriesNeedIDNumber[id_country]){
                    if ((param.object === 'invoice' && $('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked'))
                            || param.object === 'delivery') {
                        $('#field_' + param.object + '_dni').addClass('required').show();
                        $('#field_' + param.object + '_dni sup').html('*');
                        $('#' + param.object + '_dni').removeAttr('data-validation-optional').addClass('required');
                    } else {
                        $('#field_' + param.object + '_dni').removeClass('required').hide();
                        $('#field_' + param.object + '_dni sup').html('');
                        $('#' + param.object + '_dni').attr('data-validation-optional', 'true').removeClass('required');
                    }
                } else {
                    if ($('#' + param.object + '_dni').attr('data-required') == '0'){
                        $('#field_' + param.object + '_dni').removeClass('required');
                        $('#field_' + param.object + '_dni sup').html('');
                        $('#' + param.object + '_dni').attr('data-validation-optional', 'true').removeClass('required');
                    }
                }
            }
        }
    },
	isNeedPostCodeByCountryId: function(params){
        var param = $.extend({}, {
            object: ''
        }, params);

        if (!$.isEmpty(param.object)){
            var $id_country = $('#onepagecheckoutps_step_one select#' + param.object + '_id_country');

            if ($id_country.length > 0) {
                id_country = $id_country.val();
            } else {
                if (param.object == 'delivery') {
                    id_country = OnePageCheckoutPS.id_country_delivery_default;
                } else if (param.object == 'invoice') {
                    id_country = OnePageCheckoutPS.id_country_invoice_default;
                }
            }

            if (!$.isEmpty(id_country) && typeof countries !== typeof undefined && $('#field_' + param.object + '_postcode').length > 0){
                if (!$.isEmpty(countriesNeedZipCode[id_country])){
                    var format = countriesNeedZipCode[id_country];
                    format = format.replace(/N/g, '0');
                    format = format.replace(/L/g, 'A');
                    format = format.replace(/C/g, countriesIsoCode[id_country]);
                    $('#' + param.object + '_postcode').attr('data-default-value', format);

					$('#field_' + param.object + '_postcode').addClass('required').show();
					$('#field_' + param.object + '_postcode sup').html('*');

					if (param.object === 'delivery' || (param.object === 'invoice' && $('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked'))) {
						$('#' + param.object + '_postcode').removeAttr('data-validation-optional').addClass('required');
                    }
                } else {
                    if ($('#' + param.object + '_postcode').attr('data-required') == '0'){
                        $('#field_' + param.object + '_postcode').removeClass('required');
                        $('#field_' + param.object + '_postcode sup').html('');
                        $('#' + param.object + '_postcode').attr('data-validation-optional', 'true').removeClass('required');
                    }
                }
            }
        }
    },
    checkEmailCustomer: function(email){
        var data = {
            url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
            is_ajax: true,
			dataType: 'html',
            action: 'checkRegisteredCustomerEmail',
            email: email
        };

        if (!$.isEmpty(email) && $.isEmail(email)){
            var _json = {
                data: data,
                success: function(data) {
                    if (data != 0){
                        var callback = function(){
                            $('#form_login #txt_login_email').val($('#onepagecheckoutps_step_one #customer_email').val());

                            $('#email_check_modal .modal-footer').append('<button type="button" class="btn btn-primary" onclick="$(\'div#onepagecheckoutps button.close\').trigger(\'click\');$(\'div#onepagecheckoutps #opc_show_login\').trigger(\'click\')" style="margin-left: 15px;">'+OnePageCheckoutPS.Msg.login_customer+'</button>');
                        }
                        if (OnePageCheckoutPS.PRESTASHOP.CONFIGS.PS_GUEST_CHECKOUT_ENABLED){
                            Fronted.showModal({name: 'email_check_modal', type:'normal', content : OnePageCheckoutPS.Msg.error_registered_email_guest, button_close: true, callback: callback});
                        } else {
                            Fronted.showModal({name: 'email_check_modal', type:'normal', content : OnePageCheckoutPS.Msg.error_registered_email, button_close: true, callback: callback});
                        }
                    }
                }
            };
            $.makeRequest(_json);
        }
    },
    clearFormByObject: function(object){
        $('div#onepagecheckoutps #onepagecheckoutps_step_one').find('.'+object).each(function(i, field){
            $field = $(field);

            if ($field.is(':text')){
                $field.val('');
            }

            if ($field.attr('data-field-name') == 'id_country') {
                $field.val($field.attr('data-default-value')).trigger('change');
            }
        });
    }
}

var Carrier = {
    id_delivery_option_selected : 0,
    launch: function(){
        if (!is_virtual_cart){
            $('div#onepagecheckoutps #gift_message').empty();

            $('div#onepagecheckoutps #onepagecheckoutps_step_two_container')
                .on('click', '.delivery_option .delivery_option_logo', function(event){
                    var $option_radio = $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio');
                    if (!$option_radio.is(':checked')) {
                        $option_radio.attr('checked', true).trigger('change');
                    }
                })
                .on('click', '.delivery_option .carrier_delay', function(event){
                    var $option_radio = $(event.currentTarget).parents('.delivery_option').find('.delivery_option_radio');
                    if (!$option_radio.is(':checked')) {
                        if ($(event.currentTarget).find('#selulozenka, #paczkomatyinpost_selected, .btn.btn-warning').length <= 0) {//support module 'ulozenka'
                            $option_radio.attr('checked', true).trigger('change');
                        }
                    }
                })
                .on('change', '.delivery_option_radio', function (event){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two .delivery_option').removeClass('selected alert alert-info');
                    $(this).parent().parent().parent().addClass('selected alert alert-info');

                    Carrier.update({delivery_option_selected: $(event.currentTarget), load_carriers: true, load_payments: false, load_review: false});
                })
                .on('change', '#recyclable', Carrier.update)
                .on('blur', '#gift_message', Carrier.update)
                .on('blur', '#id_planning_delivery_slot', Carrier.update)//support module planningdeliverycarrier
                .on('click', '#gift', function (event){
                    Carrier.update({load_payments : true});

                    if ($(event.currentTarget).is(':checked'))
                        $('div#onepagecheckoutps #gift_div_opc').removeClass('hidden');
                    else
                        $('div#onepagecheckoutps #gift_div_opc').addClass('hidden');
                });
        }
    },
    getByCountry: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER)
            return;

        if (!is_virtual_cart){
            var extra_params = '';
            $.each(document.location.search.substr(1).split('&'),function(c,q){
                if (q != undefined && q != ''){
                    var i = q.split('=');
                    if ($.isArray(i)){
                        extra_params += '&' + i[0].toString();
                        if (i[1].toString() != undefined)
                            extra_params += '=' + i[1].toString();
                    }
                }
            });

            var data = {
                url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime() + extra_params,
                is_ajax: true,
                action: 'loadCarrier',
                dataType: 'html'
            };

            $address_delivery = AppOPC.$opc_step_one.find('#delivery_id');
            $address_invoice = AppOPC.$opc_step_one.find('#invoice_id');

            if ($('div#onepagecheckoutps #delivery_id_country option').length > 0)
                data['id_country'] = $('div#onepagecheckoutps #delivery_id_country').val();

            if ($('div#onepagecheckoutps #delivery_id_state option').length > 0)
                data['id_state'] = $('div#onepagecheckoutps #delivery_id_state').val();

            if ($('div#onepagecheckoutps #delivery_postcode').length > 0)
                data['postcode'] = $('div#onepagecheckoutps #delivery_postcode').val();

            if ($('div#onepagecheckoutps #delivery_city').length > 0)
                data['city'] = $('div#onepagecheckoutps #delivery_city').val();

            if ($address_delivery.length > 0)
                data['id_address_delivery'] = $address_delivery.val();

            if ($address_invoice.length > 0)
                data['id_address_invoice'] = $address_invoice.val();

            if ($('div#onepagecheckoutps #delivery_vat_number').length > 0)
                data['vat_number'] = $('div#onepagecheckoutps #delivery_vat_number').val();

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').show();

                    //support carrier module "packetery"
                    if (typeof window.prestashopPacketeryInitialized !== typeof undefined)
                        window.prestashopPacketeryInitialized = undefined;
                },
                success: function(html) {
                    if (!$.isEmpty(html)){
                        $('div#onepagecheckoutps #onepagecheckoutps_step_two').html(html);

                        if (typeof id_carrier_selected !== typeof undefined)
                            $('div#onepagecheckoutps .delivery_option_radio[value="'+id_carrier_selected+',"]').attr('checked', true);

                        if ($('div#onepagecheckoutps #gift').is(':checked'))
                            $('div#onepagecheckoutps #gift_div_opc').show();

                        //support module deliverydays
                        if($('#deliverydays_day option').length > 1){
                            $('#deliverydays_day option:eq(1)').attr('selected', 'true');
                        }

                        if($('div#onepagecheckoutps #onepagecheckoutps_step_two').find('.alert-warning').length <= 0)
                            Carrier.update({load_payments: true});
                        else{
                            Payment.getByCountry();
                            Review.display();
                        }
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').hide();

                    $(document).trigger('opc-load-carrier:completed', {});

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }else{
            Payment.getByCountry();
            Review.display();
        }
    },
    update: function(params){
        var param = $.extend({}, {
            delivery_option_selected: $('div#onepagecheckoutps .delivery_option_radio:checked'),
            load_carriers: false,
            load_payments: false,
            load_review: true,
            callback: ''
        }, params);

        if (!is_virtual_cart){
            var data = {
                url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
                is_ajax: true,
                action: 'updateCarrier',
                dataType: 'html',
                recyclable: ($('#recyclable').is(':checked') ? $('#recyclable').val() : ''),
                gift: ($('#gift').is(':checked') ? $('#gift').val() : ''),
                gift_message: (!$.isEmpty($('#gift_message').val()) ? $('#gift_message').val() : '')
            };

            if ($(param.delivery_option_selected).length > 0)
                data[$(param.delivery_option_selected).attr('name')] = $(param.delivery_option_selected).val();

            $('#onepagecheckoutps_step_two input[type="text"]:not(.customer, .delivery, .invoice),#onepagecheckoutps_step_two input[type="hidden"]:not(.customer, .delivery, .invoice), #onepagecheckoutps_step_two select:not(.customer, .delivery, .invoice)').each(function(i, input){
                var name = $(input).attr('name');
                var value = $(input).val();

                if (!$.isEmpty(name))
                    data[name] = value;
            });

            var _json = {
                data: data,
                beforeSend: function() {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').show();
                },
                success: function(json) {
                    if (json.hasError){
                        Fronted.showModal({type:'error', message : json.errors});
                    } else if (json.hasWarning){
                        Fronted.showModal({type:'warning', message : json.warnings});
                    }
                },
                complete: function(){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two_container .loading_small').hide();

                    $(document).trigger('opc-update-carrier:completed', {});

                    if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'carrier' && AppOPC.load_offer ) {
                        AppOPC.load_offer = false;
                        mustCheckOffer = undefined;
                        checkOffer(function() {
                            //Fronted.closeDialog();
                        });
                    }

                    if(param.load_carriers)
                        Carrier.getByCountry();
                    if(param.load_payments)
                        Payment.getByCountry();
                    if(param.load_review && !param.load_payments)
                        Review.display();

                    if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                        param.callback();
                }
            };
            $.makeRequest(_json);
        }
    }
}

var Payment = {
    id_payment_selected: '',
    launch: function(){
        $("div#onepagecheckoutps #onepagecheckoutps_step_three")
            .on('click', '.module_payment_container', function(event){
                if (!$(event.target).hasClass('payment_radio')) {
                    $(event.currentTarget).find('.payment_radio').trigger('click').trigger('change');
                }
            })
            .on("change", "input[name=method_payment]", function(event){
                var $payment_module = $(event.currentTarget);
                var $payment_module_url = $payment_module.next();
                var name_module = $payment_module.val();

                $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee').addClass('hidden');

                $.each(payment_modules_fee, function(name_module_fee, payment){
                    var various_payment = false;
                    var name_module_alt = name_module + '_' + payment.id;

                    if (name_module_alt == name_module_fee) {
                        //support module custompaymentmethod
                        if ($.strpos(name_module_fee, 'custompaymentmethod') !== false) {
                            var url_payment = $payment_module_url.val();
                            var arr_url_payment = url_payment.split('=');
                            var id_payment = arr_url_payment[1];

                            if (id_payment == payment.id) {
                                various_payment = true;
                            }
                        }
                    }

                    if (name_module == name_module_fee || various_payment){
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee').removeClass('hidden');
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_label').text(payment.label_fee);
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_price').text(payment.fee);
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_total_price_label').text(payment.label_total);
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_total_price').text(payment.total_fee);

                        if (typeof payment.fee_tax !== typeof undefined && !$.isEmpty(payment.fee_tax)) {
                            $('div#onepagecheckoutps #onepagecheckoutps_step_review .extra_fee_tax').removeClass('hidden');
                            $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_tax_label').text(payment.label_fee_tax);
                            $('div#onepagecheckoutps #onepagecheckoutps_step_review #extra_fee_tax_price').text(payment.fee_tax);
                        }

                        return false;
                    }
                });

                Payment.id_payment_selected = $(this).attr('id');

                $('div#onepagecheckoutps #onepagecheckoutps_step_three .module_payment_container').removeClass('selected alert alert-info');
                $('div#onepagecheckoutps #onepagecheckoutps_step_three .payment_content_html').addClass('hidden');
                $(this).parents('.module_payment_container').addClass('selected alert alert-info').find('.payment_content_html').removeClass('hidden');
            });
    },
    getByCountry: function(params){
        var param = $.extend({}, {
            callback: '',
            show_loading: true
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER)
            return;

        if ($('div#onepagecheckoutps #onepagecheckoutps_step_two').find('.alert-warning').length > 0) {
            $('div#onepagecheckoutps #onepagecheckoutps_step_three').html('<p class="alert alert-warning col-xs-12">'+OnePageCheckoutPS.Msg.shipping_method_required+'</p>');

            Review.display();
            return;
        }

        var extra_params = '';
        $.each(document.location.search.substr(1).split('&'),function(c,q){
            if (q != undefined && q != ''){
                var i = q.split('=');
                if ($.isArray(i)){
                    extra_params += '&' + i[0].toString();
                    if (i[1].toString() != undefined)
                        extra_params += '=' + i[1].toString();
                }
            }
        });

        var data = {
            url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime() + extra_params,
            is_ajax: true,
            dataType: 'html',
            action: 'loadPayment'
        };

        var _json = {
            data: data,
            beforeSend: function() {
                if(param.show_loading) {
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three_container .loading_small').show();
                }
            },
            success: function(html) {
                $('div#onepagecheckoutps #onepagecheckoutps_forms').html('');
                $('div#onepagecheckoutps #onepagecheckoutps_step_three').html(html);

                if (!$.isEmpty(Payment.id_payment_selected)){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container #' + Payment.id_payment_selected).parent().parent().trigger('click');
                } else if ($('#onepagecheckoutps_step_three #payment_method_container .module_payment_container').length == 1){
                    $('#onepagecheckoutps_step_three #payment_method_container .module_payment_container').trigger('click');
                } else if (!$.isEmpty(OnePageCheckoutPS.CONFIGS.OPC_DEFAULT_PAYMENT_METHOD)){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three #payment_method_container [value="'+ OnePageCheckoutPS.CONFIGS.OPC_DEFAULT_PAYMENT_METHOD + '"]').parent().parent().trigger('click');
                }

                $('div#onepagecheckoutps #onepagecheckoutps_step_three .module_payment_container.selected').find('.payment_content_html').removeClass('hidden');

                //support module paypalpro
                if(typeof $('#pppro_form') !== typeof undefined && !OnePageCheckoutPS.IS_LOGGED)
                    $('#pppro_form #pppro_cc_fname, #pppro_form #pppro_cc_lname').val('');
            },
            complete: function(){
                if(param.show_loading)
                    $('div#onepagecheckoutps #onepagecheckoutps_step_three_container .loading_small').hide();

                $(document).trigger('opc-load-payment:completed', {});

                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function') {
                    param.callback();
                } else {
                    Review.display();
                }
            }
        };
        $.makeRequest(_json);
    },
    change: function(){
        if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'payment_method') ) {
//            Payment.validateSelected();
        } else {
            AppOPC.load_offer = false;
            checkOffer(function() {
//                Payment.validateSelected();
            });
        }
    }
}

var Review = {
    message_order: '',
    launch: function(){
        AppOPC.$opc_step_review.find('.remove-from-cart').off('click');

        AppOPC.$opc_step_review
            .on('click', '.bootstrap-touchspin-up, .bootstrap-touchspin-down, .remove-from-cart', function(e){
                e.preventDefault();
                e.stopPropagation();

                var url_call = '';
                var $input = $(e.currentTarget).parents('.bootstrap-touchspin').find('.cart-line-product-quantity');

                if ($(e.currentTarget).hasClass('bootstrap-touchspin-up')) {
                    url_call = $input.data('up-url');
                } else if ($(e.currentTarget).hasClass('bootstrap-touchspin-down')) {
                    url_call = $input.data('down-url');
                } else {
                    url_call = $(e.currentTarget).attr('href');
                }

                var _json = {
                    data: {
                        url_call: url_call,
                        action: 'update',
                        ajax: 1,
                        token: static_token
                    },
                    beforeSend: function() {
                        $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').show();
                    },
                    success: function(json) {
                        if (json.success) {
                            Review.updateCartSummary(json);
                        } else if (json.hasError && json.errors.length > 0) {
                            $(e.currentTarget).val(json.quantity);

                            Fronted.showModal({type:'error', message : '&bullet; ' + json.errors.join('<br>&bullet; ')});

                            $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').hide();
                        }
                    }
                };
                $.makeRequest(_json);
            })
            .on("click", "#conditions-to-approve a", function(e){
                e.preventDefault();
                e.stopPropagation();

                Fronted.openCMS({id_cms : OnePageCheckoutPS.CONFIGS.OPC_ID_CMS_TEMRS_CONDITIONS});
            })
            .on("click", "#submitAddDiscount, .cart_discount .cart_quantity_delete", Review.processDiscount)
            .on("click", "#btn_place_order", function(){
                if (parseInt(OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO) && $('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length <= 0) {
                    window.scrollTo(0, $('#onepagecheckoutps').offset().top);
                    $('#onepagecheckoutps_step_three').addClass('alert alert-warning');
                    return false;
                }else{
                    Review.placeOrder();
                }
            })
            .on("change", '#cgv', function(e) {
                if (typeof $.totalStorageOPC !== typeof undefined) {
                    if ($(e.target).is(':checked')) {
                        $.totalStorageOPC('cms_terms_condifitions', true);
                    } else {
                        $.totalStorageOPC('cms_terms_condifitions', false);
                    }
                }

                if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'terms' && AppOPC.load_offer ) {
                    if ( $(e.target).is(':checked') ) {
                        if ( !offerApplied ) {
                            AppOPC.load_offer = false;
                            checkOffer(function() {
                                $(e.target).unbind('change');
                                //Fronted.closeDialog();
                            });
                        }
                    }
                }
            })
            .on("click", "#payment_paypal_express_checkout", function(){
                $('#paypal_payment_form').submit();
            })
            .on('blur', '.cart-line-product-quantity', function(e){
                var before_qty = $(e.currentTarget).attr('value');
                var actual_qty = $(e.currentTarget).val();

                if (actual_qty == 0) {
                    $(e.currentTarget).val(before_qty);
                } else {
                    var operation = 'down';
                    var qty = actual_qty - before_qty;

                    if (qty != 0) {
                        var url_call = $(e.currentTarget).data('update-url');

                        if (qty > 0) {
                            operation = 'up';
                        }

                        var _json = {
                            data: {
                                url_call: url_call,
                                action: 'update',
                                ajax: 1,
                                token: static_token,
                                op: operation,
                                qty:  Math.abs(qty)
                            },
                            beforeSend: function() {
                                $('div#onepagecheckoutps #btn_place_order').attr('disabled', 'true');
                                $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').show();
                            },
                            success: function(json) {
                                if (json.success) {
                                    Review.updateCartSummary(json);
                                } else if (json.hasError && json.errors.length > 0) {
                                    $(e.currentTarget).val(json.quantity);

                                    Fronted.showModal({type:'error', message : '&bullet; ' + json.errors.join('<br>&bullet; ')});

                                    $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').hide();
                                }
                            }
                        };
                        $.makeRequest(_json);
                    }
                }
            })
            .on("blur", "#div_leave_message #message", function(){
                Review.message_order = $(this).val();
            });
    },
    updateCartSummary: function (json){
        //update cart
        if ($('.blockcart').length > 0) {
            var refreshURL = $('.blockcart').data('refresh-url');

            $.post(refreshURL, {}).then(function (resp) {
                $('.blockcart').replaceWith($(resp.preview).find('.blockcart'));
            });
        }

        if (typeof json !== typeof undefined){
            if (json.is_virtual_cart){
                $('#onepagecheckoutps_step_two_container').remove();
                $('#onepagecheckoutps_step_three_container').removeClass('col-md-6');

                if (!OnePageCheckoutPS.SHOW_DELIVERY_VIRTUAL) {
                    $('#onepagecheckoutps_step_one #li_delivery_address').remove();
                    $('#onepagecheckoutps_step_one #li_invoice_address').addClass('active');
                    $('#onepagecheckoutps_step_one #delivery_address_container').remove();
                    $('#onepagecheckoutps_step_one #invoice_address_container').addClass('active');
                }

                OnePageCheckoutPS.IS_VIRTUAL_CART = true;

                Payment.getByCountry();
                Review.display();
            }else{
                if (typeof json.load === typeof undefined){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').show();

                    Carrier.getByCountry();
                }
            }
        }
    },
    display: function(params){
        var param = $.extend({}, {
            callback: ''
        }, params);

        if (OnePageCheckoutPS.REGISTER_CUSTOMER)
            return;

        if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_TERMS_CONDITIONS)
            var cgv = $('#cgv').is(':checked');

        var id_country = !$.isEmpty($('#delivery_id_country').val()) ? $('#delivery_id_country').val() : '';
        var id_state = !$.isEmpty($('#delivery_id_state').val()) ? $('#delivery_id_state').val() : '';

        var data = {
            url_call: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
            is_ajax: true,
            dataType: 'html',
            action: 'loadReview',
            id_country: id_country,
            id_state: id_state
        };

        var _json = {
            data: data,
            beforeSend: function() {
                $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').show();
            },
            success: function(html) {
                $("div#onepagecheckoutps #onepagecheckoutps_step_review").html(html);

                if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_TERMS_CONDITIONS && cgv)
                    $('div#onepagecheckoutps #cgv').attr('checked', 'true');

                $('div#onepagecheckoutps input[name="method_payment"]:checked').trigger('change');
            },
            complete: function(){
                $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').hide();

                $(document).trigger('opc-load-review:completed', {});

                //if no exist carriers, do not show cost shipping
                if ($('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container p.alert-warning').length > 0){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .item_total:not(.cart_total_product)').hide();
                }

                //remove express checkout paypal on review
                $('#container_express_checkout').remove();

                if (OnePageCheckoutPS.CONFIGS.OPC_SHOW_ZOOM_IMAGE_PRODUCT) {
                    //image zoom on product list.
                    $('div#onepagecheckoutps #order-detail-content .cart_item a > img').mouseenter(function(event){
                        $('div#onepagecheckoutps #order-detail-content .image_zoom').hide();
                        $(event.currentTarget).parents('.image_product').find('.image_zoom').show();
                    });
                    $('div#onepagecheckoutps #order-detail-content .image_zoom').click(function(event){
                        $(event.currentTarget).toggle();
                    });
                    $('div#onepagecheckoutps #order-detail-content .image_zoom').hover(function(event){
                        $(event.currentTarget).show();
                    }, function(event){
                        $(event.currentTarget).hide();
                    });
                }

                if (typeof $.totalStorageOPC !== typeof undefined) {
                    if ($.totalStorageOPC('cms_terms_condifitions')) {
                        $("#onepagecheckoutps_step_review #cgv").attr('checked', 'true');
                    }
                }

                var intervalLoadJavaScriptReview = setInterval(
                    function() {
                        loadJavaScriptReview();
                        clearInterval(intervalLoadJavaScriptReview);
                    }
                    , (typeof csoc_prefix !== 'undefined' ? 5001 : 0));

                //last minute opc
                if ( typeof mustCheckOffer !== 'undefined' && event_dispatcher !== undefined && event_dispatcher === 'init' && AppOPC.load_offer ) {
                    AppOPC.load_offer = false;
                    mustCheckOffer = undefined;

                    setTimeout(checkOffer, time_load_offer * 1000);
                }

                if (OnePageCheckoutPS.CONFIGS.OPC_CONFIRMATION_BUTTON_FLOAT && !OnePageCheckoutPS.CONFIGS.OPC_PAYMENTS_WITHOUT_RADIO){
                    var $container_float_review = $("div#onepagecheckoutps div#onepagecheckoutps_step_review #container_float_review");
                    var $container_float_review_point = $("div#onepagecheckoutps div#onepagecheckoutps_step_review #container_float_review_point");

                    $(window).scroll(function() {
                        if (AppOPC.$opc.find('.loading_big').is(':visible')) {
                            $container_float_review.removeClass('stick_buttons_footer');
                        } else {
                            if (!$container_float_review_point.visible() && $(window).height() > 640) {
                                if ($container_float_review_point.offset().top > $(window).scrollTop()){
                                    $container_float_review.addClass('stick_buttons_footer').css({width : $('#onepagecheckoutps_step_review').outerWidth()});
                                }
                            } else {
                                $container_float_review.removeClass('stick_buttons_footer').removeAttr('style');
                            }
                        }
                    });

                    $(window).resize(function(){
                        $(window).trigger('scroll');
                    });
                    $(window).trigger('scroll');
                }

                if (typeof FB !== typeof undefined && typeof FB.XFBML.parse == 'function') {
                    FB.XFBML.parse();
                }

                $('div#onepagecheckoutps #onepagecheckoutps_step_review_container #message').val(Review.message_order);

                if (typeof getAppliedOffers !== typeof undefined && typeof getAppliedOffers === 'function') {
                     getAppliedOffers();
                }

                //$('#btn_continue_shopping').attr('data-link', document.referrer);

                $(document).trigger('opc-load-review:completed', {});

                if (typeof param.callback !== typeof undefined && typeof param.callback === 'function')
                    param.callback();
            }
        };
        $.makeRequest(_json);
    },
    processDiscount: function(e) {
        $element = $(e.currentTarget);

        var _data = {
             url_call: prestashop.urls.pages.cart,
            action: 'update',
            ajax: 1,
            token: static_token
        }

        if ($element.is('i')) {
            _data.deleteDiscount = $element.data('id-cart-rule');
        } else {
            _data.addDiscount = 1;
            _data.discount_name = AppOPC.$opc_step_review.find('#discount_name').val();
        }

        var _json = {
            data: _data,
            beforeSend: function() {
                $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').show();
            },
            success: function(json) {
                if (json.hasError){
                    $('div#onepagecheckoutps #onepagecheckoutps_step_review_container .loading_small').hide();
                    Fronted.showModal({type:'error', message : '&bullet; ' + json.errors.join('<br>&bullet; ')});
                } else {
                    if ($('#onepagecheckoutps_step_two #input_virtual_carrier').length > 0){
                        Payment.getByCountry();
                    }else{
                        Carrier.getByCountry();
                    }
                }
            },
            complete: function(){
                $('#onepagecheckoutps_step_review #submitAddDiscount').attr('disabled', false);
            }
        };
        $.makeRequest(_json);
    },
    getFields: function(){
        var fields = Array();

        var $paypalpro_payment_form = $('#onepagecheckoutps_step_three #paypalpro-payment-form');

        $('div#onepagecheckoutps div#onepagecheckoutps_step_one .customer, \n\
            div#onepagecheckoutps div#onepagecheckoutps_step_one .delivery, \n\
            div#onepagecheckoutps div#onepagecheckoutps_step_one .invoice')
        .each(function(i, field){
            if ($(field).is('span'))
                return true;

            var name = $(field).attr('data-field-name');
            var value = '';
            var object = '';

            if ($.isEmpty(name))
                return true;

            if ($(field).hasClass('customer')){
                object = 'customer';
            }else if ($(field).hasClass('delivery')){
                object = 'delivery';
            }else if ($(field).hasClass('invoice')){
                object = 'invoice';
            }

            if (object == 'invoice' && $('div#onepagecheckoutps #checkbox_create_invoice_address').length > 0) {
                if (!$('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked'))
                    return true;
            }

            if (!$.isEmpty(object)){
                if ($(field).is(':checkbox')){
                    value = $(field).is(':checked') ? 1 : 0;
                }else if ($(field).is(':radio')){
                    var tmp_value = $('input[name="' + name + '"]:checked').val();
                    if (typeof tmp_value !== typeof undefined)
                        value = tmp_value;
                }else{
                    value = $(field).val();

                    if (value === null)
                        value = '';
                }

                if ($.strpos(value, '\\')){
                    value = addslashes(value);
                }

                if ($.strpos(value, '\n')){
                    value = value.replace(/\n/gi, '\\n');
                }

                if (!$.isEmpty(value) && typeof value == 'string'){
                    value = value.replace(/\"/g, '\'');
                }

                value = $.trim(value);

                fields.push({'object' : object, 'name' : name, 'value' : value});

                //support payment module: StripeJS
                if (typeof stripe_billing_address !== typeof undefined && object == 'invoice') {
                    stripe_billing_address[name] = value;

                    if (name == 'id_country') {
                        stripe_billing_address['country'] = $(field).find('option:selected').data('text');
                    }
                }

                //support payment module: nPaypalPro - prestashop - 1.3.7
                if(object == 'customer' && $paypalpro_payment_form.length > 0) {
                    if (name == 'firstname') {
                        $paypalpro_payment_form.find('.paypalpro-firstname').val(value);
                    }
                    if (name == 'lastname') {
                        $paypalpro_payment_form.find('.paypalpro-lastname').val(value);
                    }
                }
            }
        });

        return fields;
    },
    getFieldsExtra: function(_data){
        $('div#onepagecheckoutps #form_onepagecheckoutps input[type="text"]:not(.customer, .delivery, .invoice), div#onepagecheckoutps #form_onepagecheckoutps input[type="hidden"]:not(.customer, .delivery, .invoice), div#onepagecheckoutps #form_onepagecheckoutps select:not(.customer, .delivery, .invoice)').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).val();

            if (name == 'action') {
                return;
            }

            //compatibilidad modulo eydatepicker
            if (name == 'shipping_date_raw')
                name = 'shipping_date';

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        $('div#onepagecheckoutps #form_onepagecheckoutps input[type="checkbox"]:not(.customer, .delivery, .invoice)').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).is(':checked') ? $(input).val() : '';

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        $('div#onepagecheckoutps #form_onepagecheckoutps input[type="radio"]:not(.customer, .delivery, .invoice):checked').each(function(i, input){
            var name = $(input).attr('name');
            var value = $(input).val();

            if (!$.isEmpty(name))
                _data[name] = value;
        });

        delete _data['id_customer'];
        _data['id_customer'];
        _data['id_customer'];

        return _data;
    },
    placeOrder: function(params){
        var param = $.extend({}, {
            validate_payment: true,
            position_element: null
        }, params);

        if ($('#onepagecheckoutps_step_two .delivery_option.selected div.extra_info_carrier a.select_pickup_point').length > 0){
            alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

            $('#onepagecheckoutps_step_two .delivery_option.selected div.extra_info_carrier a.select_pickup_point').trigger('click');

            return false;
        }

        //support module: packetery v2.0.0rc2 (ZLab Solutions)
        if (AppOPC.$opc_step_two.find('.delivery_option.selected #packetery-widget select.js-name').length > 0) {
            if ($.isEmpty(AppOPC.$opc_step_two.find('#packetery-widget select.js-name option:selected').val())) {
                alert(OnePageCheckoutPS.Msg.need_select_pickup_point);

                return false;
            }
        }

        $('div#onepagecheckoutps #btn_place_order').attr('disabled', 'true');

        //return fields if the validation is ok
        var fields = Review.validateAllForm({validate_payment: param.validate_payment});

        if (fields && AppOPC.is_valid_all_form){
            var invoice_id = '';

            if (OnePageCheckoutPS.CONFIGS.OPC_ENABLE_INVOICE_ADDRESS && $('div#onepagecheckoutps #checkbox_create_invoice_address').length > 0){
                if ($('div#onepagecheckoutps #checkbox_create_invoice_address').is(':checked')){
                    invoice_id = $('#invoice_id').val();
                }
            }else{
                invoice_id = $('#invoice_id').val();
            }

            var _extra_data = Review.getFieldsExtra({});
            var _data = $.extend({}, _extra_data, {
                'url_call'				: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
                'is_ajax'               : true,
                'action'                : 'placeOrder',
                'id_customer'           : (!$.isEmpty(AppOPC.$opc_step_one.find('#customer_id').val()) ? AppOPC.$opc_step_one.find('#customer_id').val() : ''),
                'id_address_delivery'   : (!$.isEmpty(AppOPC.$opc_step_one.find('#delivery_id').val()) ? AppOPC.$opc_step_one.find('#delivery_id').val() : ''),
                'id_address_invoice'    : invoice_id,
                'fields_opc'            : JSON.stringify(fields),
                'message'               : (!$.isEmpty(AppOPC.$opc_step_review.find('#message').val()) ? AppOPC.$opc_step_review.find('#message').val() : ''),
                'is_new_customer'       : (AppOPC.$opc_step_one.find('#checkbox_create_account_guest').is(':checked') ? 0 : 1),
                'token'                 : static_token
            });

			var _json = {
				data: _data,
				beforeSend: function() {
					Fronted.loadingBig(true);
                    window.scrollTo(0, AppOPC.$opc.outerHeight() / 3);
				},
				success: function(data) {
					if (data.isSaved && (!OnePageCheckoutPS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked'))){
                        AppOPC.$opc_step_one.find('#customer_id').val(data.id_customer);
                        AppOPC.$opc_step_one.find('#customer_email, #customer_conf_email, #customer_passwd, #customer_conf_passwd').attr({'disabled': 'true', 'data-validation-optional' : 'true'}).addClass('disabled').trigger('reset');

                        $('#div_onepagecheckoutps_login, #field_customer_passwd, #field_customer_conf_passwd, div#onepagecheckoutps #onepagecheckoutps_step_one_container .account_creation, #field_choice_group_customer, #field_customer_checkbox_create_account').addClass('hidden');
                    }

                    if (data.hasError){
                        Fronted.showModal({type:'error', message : '&bullet; ' + data.errors.join('<br>&bullet; ')});
                    } else if (data.hasWarning){
                        Fronted.showModal({type:'warning', message : '&bullet; ' + data.warnings.join('<br>&bullet; ')});
                    } else {
                        if (typeof $.totalStorageOPC !== typeof undefined) {
                            $.totalStorageOPC('id_address_delivery', data.id_address_delivery);
                            $.totalStorageOPC('id_address_invoice', data.id_address_invoice);
                        }

                        if (!OnePageCheckoutPS.PRESTASHOP.CONFIGS.PS_GUEST_CHECKOUT_ENABLED || $('#checkbox_create_account_guest').is(':checked')){
                            $('div#onepagecheckoutps #field_delivery_id, div#onepagecheckoutps #field_invoice_id').removeClass('hidden');
                            $('div#onepagecheckoutps #field_customer_checkbox_create_account_guest').addClass('hidden');
                        }

                        //plugin last minute offer
                        if ( !AppOPC.load_offer || typeof mustCheckOffer === 'undefined' || (event_dispatcher !== undefined && event_dispatcher !== 'confirm') ) {
                            window['checkOffer'] = function(callback) {
                                callback();
                            };
                        }

                        if ($('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length > 0){
                            confirmFreeOrder();
                            return;
                        }

                        if (param.validate_payment === true){
                            var $payment_selected = AppOPC.$opc_step_three.find('#' + Payment.id_payment_selected + ':checked');
                            var url_payment = $payment_selected.next().val();

                            if (!$.isEmpty(url_payment) && $.isUrlValid(url_payment)) {
                                window.location = url_payment;
                            } else {
                                $payment_selected.parents('.module_payment_container.selected').find('form')[0].submit();
                            }
                        }
                    }
				},
                complete: function() {
                },
				error: function(data){
					alert(data);
					Fronted.loadingBig(false);
				}
			};
			$.makeRequest(_json);
        }
    },
    validateAllForm: function(params){
        var param = $.extend({}, {
            validate_payment: true
        }, params);

        //validate fields
        $('div#onepagecheckoutps #form_onepagecheckoutps').submit();

        //cuando hay informacion sin llenar en facturacion, para que el usuario sepa que hay que llenarlo, se ubica en esa seccion
        if (!AppOPC.is_valid_all_form) {
            if ($('#delivery_address_container .required.has-error').length == 0 && $('#invoice_address_container .required.has-error').length > 0) {
                $('#onepagecheckoutps_step_one #li_delivery_address .nav-link, #onepagecheckoutps_step_one #delivery_address_container').removeClass('active');
                $('#onepagecheckoutps_step_one #li_invoice_address .nav-link, #onepagecheckoutps_step_one #invoice_address_container').removeClass('active');

                $('#onepagecheckoutps_step_one #li_invoice_address .nav-link, #onepagecheckoutps_step_one #invoice_address_container').addClass('active');
            }
        }

        if (AppOPC.is_valid_all_form){
            $('div#onepagecheckoutps #onepagecheckoutps_step_two').removeClass('alert alert-danger');
            $('div#onepagecheckoutps #onepagecheckoutps_step_three').removeClass('alert alert-warning');
            $('div#onepagecheckoutps #onepagecheckoutps_step_review #conditions-to-approve label').removeClass('alert alert-warning');
            $('div#onepagecheckoutps #onepagecheckoutps_step_one #div_privacy_policy').removeClass('alert alert-warning');

            //validate shipping
            if ($('div#onepagecheckoutps #onepagecheckoutps_step_two .delivery_options_address').length >= 0 && !is_virtual_cart){
                var id_carrier = $('div#onepagecheckoutps #onepagecheckoutps_step_two .delivery_option_radio:checked').val();

                if (!$.isEmpty(id_carrier)){
                    Carrier.id_delivery_option_selected = id_carrier;

                    AppOPC.is_valid_all_form = true;
                }else{
                    Carrier.id_delivery_option_selected = null;
                    $('div#onepagecheckoutps #onepagecheckoutps_step_two #shipping_container').addClass('alert alert-warning');

                    Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.shipping_method_required});

                    AppOPC.is_valid_all_form = false;
                }
            }

            //validate payments
            if(AppOPC.is_valid_all_form && param.validate_payment === true){
                if ($('div#onepagecheckoutps #onepagecheckoutps_step_three #free_order').length <= 0){
                    var payment = $('div#onepagecheckoutps #onepagecheckoutps_step_three input[name="method_payment"]:checked');

                    if (payment.length > 0){
                        Payment.id_payment_selected = $(payment).attr('id');

                        AppOPC.is_valid_all_form = true;
                    }else{
                        Payment.id_payment_selected = '';

						//support module payment: Pay
						if (!$.isEmpty($('#securepay_cardNo').val()) &&
							!$.isEmpty($('#securepay_cardSecurityCode').val()) &&
							!$.isEmpty($('#securepay_cardExpireMonth').val()) &&
							!$.isEmpty($('#securepay_cardExpireYear').val()))
						{
							AppOPC.is_valid_all_form = true;
						}
						else
						{
							$('div#onepagecheckoutps #onepagecheckoutps_step_three').addClass('alert alert-warning');

							Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.payment_method_required});

							AppOPC.is_valid_all_form = false;
						}
                    }
                }
            }

            //terms conditions
            if (AppOPC.is_valid_all_form && AppOPC.$opc_step_review.find('#conditions-to-approve').length){
                AppOPC.$opc_step_review.find('#conditions-to-approve input').each(function(i, condition) {
                    if (!$(condition).is(':checked')) {
                        $(condition).parent().addClass('alert alert-warning');

                        AppOPC.is_valid_all_form = false;
                    }
                });

                if (!AppOPC.is_valid_all_form) {
                    Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_terms_and_conditions});
                }
            }

            //privacy policy
            if (AppOPC.is_valid_all_form && OnePageCheckoutPS.ENABLE_PRIVACY_POLICY && !OnePageCheckoutPS.IS_LOGGED && !$('div#onepagecheckoutps #onepagecheckoutps_step_one #privacy_policy').is(':checked')){
                $('div#onepagecheckoutps #onepagecheckoutps_step_one #div_privacy_policy').addClass('alert alert-warning');

                Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.agree_privacy_policy});

                AppOPC.is_valid_all_form = false;
            }

            //if all is rigth, then get all fields
            if (AppOPC.is_valid_all_form){
                $('div#onepagecheckoutps #btn_place_order').removeAttr('disabled');

                return Review.getFields();
            }
        }else{
            Fronted.showModal({type: 'warning', message: OnePageCheckoutPS.Msg.fields_required_to_process_order + '\n' + OnePageCheckoutPS.Msg.check_fields_highlighted});
        }

        $('div#onepagecheckoutps #btn_place_order').removeAttr('disabled');

        return false;
    }
}

function updateExtraCarrier(id_delivery_option, id_address)
{
	$.ajax({
		type: 'POST',
		url: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
		cache: false,
		dataType : "json",
		data: 'is_ajax=true'
			+'&action=updateExtraCarrier'
			+'&id_address='+id_address
			+'&id_delivery_option='+id_delivery_option
			+'&token='+static_token
			+'&allow_refresh=1',
		success: function(jsonData)
		{
			$('#HOOK_EXTRACARRIER_'+id_address).html(jsonData['content']);
		}
	});
}

function confirmFreeOrder()
{
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: prestashop.urls.current_url + '?rand=' + new Date().getTime(),
		cache: false,
		dataType : "html",
		data: 'ajax=true&method=makeFreeOrder&token=' + static_token ,
		success: function(html)
		{
			$('#btn_place_order').removeClass('disabled');
			var array_split = html.split(':');
			if (array_split[0] == 'freeorder')
			{
				if (!$('#checkbox_create_account_guest').is(':checked') && !OnePageCheckoutPS.IS_LOGGED)
					document.location.href = prestashop.urls.pages.guest_tracking+'?id_order='+encodeURIComponent(array_split[1])+'&email='+encodeURIComponent(array_split[2]);
				else
					document.location.href = prestashop.urls.pages.history;
			}else{
                            //Fronted.closeDialog();
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log('ERROR AJAX: ' + textStatus, errorThrown);
        }
	});
}

function updateCarrierSelectionAndGift(){}
function updateCarrierList(){}
function updatePaymentMethods(){}
function updatePaymentMethodsDisplay(){}
function cleanSelectAddressDelivery(){}

//compatibilidad modulo crosselling
function loadJavaScriptReview(){
    $(function(){
//        if($('#crossselling_list').length > 0)
//        {
//        	//init the serialScroll for thumbs
//        	cs_serialScrollNbImages = $('#crossselling_list li').length;
//        	cs_serialScrollNbImagesDisplayed = 5;
//        	cs_serialScrollActualImagesIndex = 0;
//        	$('#crossselling_list').serialScroll({
//        		items:'li',
//        		prev:'a#crossselling_scroll_left',
//        		next:'a#crossselling_scroll_right',
//        		axis:'x',
//        		offset:0,
//        		stop:true,
//        		onBefore:cs_serialScrollFixLock,
//        		duration:300,
//        		step: 1,
//        		lazy:true,
//        		lock: false,
//        		force:false,
//        		cycle:false
//        	});
//        	$('#crossselling_list').trigger( 'goto', [ (typeof cs_middle !== 'undefined' ? cs_middle : middle)-3] );
//        }

//        $('#onepagecheckoutps_step_review #gift-products_block .ajax_add_to_cart_button').die('click');

//
            $('#onepagecheckoutps_step_review .ajax_add_to_cart_button').unbind('click').click(function(event){
                var idProduct = 0;

                if (!$.isEmpty($(event.currentTarget).attr('data-id-product')))
                    idProduct = $(event.currentTarget).attr('data-id-product');
                else
                    idProduct =  $(this).attr('rel').replace('ajax_id_product_', '');

                if ($('#onepagecheckoutps_step_review #gift-products_block').length > 0){
                    event.preventDefault();
                    window.location = $(event.currentTarget).attr('href');

                    return false;
                }

                if (!$.isEmpty(idProduct)){
                    ajaxCart.add(idProduct, null, false, this);
                    Carrier.getByCountry();

                    return false;
                }
            });
//        }

        $('#onepagecheckoutps_step_review .ajax_add_to_cart_button').css({visibility: 'visible'});

        //compatibilidad con modulo CheckoutFields
        if (typeof checkoutfields !== 'undefined')
            checkoutfields.bindAjaxSave();

        //compatibilidad con modulo paragonfaktura
        $('#pfform input').click(function(){
            var value = $('#pfform input:checked').val();
            var id_cart = $('#pfform #pf_id').val();
            $.ajax({
              type: "POST",
              url: "modules/paragonfaktura/save.php",
              data: { value: value, id_cart: id_cart }
            }).done(function( msg ) {

            });
		});
    });
}

function opc_callback_error_payment(name_module, params) {
    if (name_module == 'braintree') {
        $("div#onepagecheckoutps .loading_big").hide();

        if (typeof params.errorMsg !== typeof undefined && params.errorMsg) {
            Fronted.showModal({type: 'warning', message: params.msg});
        }
    }
}
function addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
  //    input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}

function version_compare(v1, v2, operator) { // eslint-disable-line camelcase
  //       discuss at: http://locutus.io/php/version_compare/
  //      original by: Philippe Jausions (http://pear.php.net/user/jausions)
  //      original by: Aidan Lister (http://aidanlister.com/)
  // reimplemented by: Kankrelune (http://www.webfaktory.info/)
  //      improved by: Brett Zamir (http://brett-zamir.me)
  //      improved by: Scott Baker
  //      improved by: Theriault (https://github.com/Theriault)
  //        example 1: version_compare('8.2.5rc', '8.2.5a')
  //        returns 1: 1
  //        example 2: version_compare('8.2.50', '8.2.52', '<')
  //        returns 2: true
  //        example 3: version_compare('5.3.0-dev', '5.3.0')
  //        returns 3: -1
  //        example 4: version_compare('4.1.0.52','4.01.0.51')
  //        returns 4: 1

  // Important: compare must be initialized at 0.
  var i
  var x
  var compare = 0

  var vm = {
    'dev': -6,
    'alpha': -5,
    'a': -5,
    'beta': -4,
    'b': -4,
    'RC': -3,
    'rc': -3,
    '#': -2,
    'p': 1,
    'pl': 1
  }

  var _prepVersion = function (v) {
    v = ('' + v).replace(/[_\-+]/g, '.')
    v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.')
    return (!v.length ? [-8] : v.split('.'))
  }

  var _numVersion = function (v) {
    return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10))
  }

  v1 = _prepVersion(v1)
  v2 = _prepVersion(v2)
  x = Math.max(v1.length, v2.length)
  for (i = 0; i < x; i++) {
    if (v1[i] === v2[i]) {
      continue
    }
    v1[i] = _numVersion(v1[i])
    v2[i] = _numVersion(v2[i])
    if (v1[i] < v2[i]) {
      compare = -1
      break
    } else if (v1[i] > v2[i]) {
      compare = 1
      break
    }
  }
  if (!operator) {
    return compare
  }

  switch (operator) {
    case '>':
    case 'gt':
      return (compare > 0)
    case '>=':
    case 'ge':
      return (compare >= 0)
    case '<=':
    case 'le':
      return (compare <= 0)
    case '===':
    case '=':
    case 'eq':
      return (compare === 0)
    case '<>':
    case '!==':
    case 'ne':
      return (compare !== 0)
    case '':
    case '<':
    case 'lt':
      return (compare < 0)
    default:
      return null
  }
}

var reload_init_opc = setInterval(function(){
    if (typeof AppOPC !== typeof undefined){
        if(!AppOPC.initialized)
            AppOPC.init();
        else
            clearInterval(reload_init_opc)
    }
}, 2000);