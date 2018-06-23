<?php
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
 * @revision  33
 */

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_.'/onepagecheckoutps/classes/OnePageCheckoutPSCore.php';

class OnePageCheckoutPS extends OnePageCheckoutPSCore
{
    const VERSION = '1.0.1';

    public $onepagecheckoutps_dir;
    public $onepagecheckoutps_tpl;
    public $translation_dir;
    public $fields_to_capitalize = array('firstname', 'lastname', 'address1', 'address2', 'city', 'company', 'postcode');
    protected $configure_vars = array(
        array('name' => 'OPC_VERSION', 'default_value' => self::VERSION, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_OVERRIDE_CSS', 'default_value' => '', 'is_html' => true, 'is_bool' => false),
        array('name' => 'OPC_OVERRIDE_JS', 'default_value' => '', 'is_html' => true, 'is_bool' => false),
        /* general */
        array('name' => 'OPC_SHOW_DELIVERY_VIRTUAL', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_ID_CONTENT_PAGE',
            'default_value' => '#content-wrapper #main',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_DEFAULT_PAYMENT_METHOD', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_DEFAULT_GROUP_CUSTOMER', 'default_value' => 3, 'is_html' => false, 'is_bool' => false),
        array(
            'name' => 'OPC_GROUPS_CUSTOMER_ADDITIONAL',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_ID_CUSTOMER', 'default_value' => 0, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_VALIDATE_DNI', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REDIRECT_DIRECTLY_TO_OPC', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        /* register - step 1 */
        array('name' => 'OPC_SHOW_BUTTON_REGISTER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_USE_SAME_NAME_CONTACT_DA', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_USE_SAME_NAME_CONTACT_BA', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REQUEST_PASSWORD', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_OPTION_AUTOGENERATE_PASSWORD',
            'default_value' => 1,
            'is_html' => false,
            'is_bool' => true
        ),
        array('name' => 'OPC_ENABLE_INVOICE_ADDRESS', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REQUIRED_INVOICE_ADDRESS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_REQUEST_CONFIRM_EMAIL', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_CHOICE_GROUP_CUSTOMER', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_CHOICE_GROUP_CUSTOMER_ALLOW',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_SHOW_LIST_CITIES_GEONAMES', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_AUTO_ADDRESS_GEONAMES', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_AUTOCOMPLETE_GOOGLE_ADDRESS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_GOOGLE_API_KEY', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_CAPITALIZE_FIELDS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        /* shipping - step 2 */
        array('name' => 'OPC_RELOAD_SHIPPING_BY_STATE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_DESCRIPTION_CARRIER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_IMAGE_CARRIER', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_FORCE_NEED_POSTCODE', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_FORCE_NEED_CITY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array(
            'name'          => 'OPC_MODULE_CARRIER_NEED_POSTCODE',
            'default_value' => '',
            'is_html'       => false
        ),
        array(
            'name'          => 'OPC_MODULE_CARRIER_NEED_CITY',
            'default_value' => '',
            'is_html'       => false
        ),
        /* payment - step 3 */
        //array('name' => 'OPC_SHOW_POPUP_PAYMENT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        //array('name' => 'OPC_PAYMENTS_WITHOUT_RADIO', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        //array('name' => 'OPC_MODULES_WITHOUT_POPUP', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_SHOW_IMAGE_PAYMENT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_DETAIL_PAYMENT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_PAYMENT_NEED_REGISTER', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        /* review - step 4 */
        array('name' => 'OPC_SHOW_LINK_CONTINUE_SHOPPING', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_LINK_CONTINUE_SHOPPING', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_SHOW_ZOOM_IMAGE_PRODUCT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_PRODUCT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_DISCOUNT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_WRAPPING', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_SHIPPING', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_WITHOUT_TAX', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_TAX', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_TOTAL_PRICE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array(
            'name' => 'OPC_SHOW_REMAINING_FREE_SHIPPING',
            'default_value' => 1,
            'is_html' => false,
            'is_bool' => true
        ),
        array('name' => 'OPC_ENABLE_TERMS_CONDITIONS', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_ID_CMS_TEMRS_CONDITIONS', 'default_value' => 0, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_ENABLE_PRIVACY_POLICY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_ID_CMS_PRIVACY_POLICY', 'default_value' => 0, 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_SHOW_WEIGHT', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_REFERENCE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_UNIT_PRICE', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_AVAILABILITY', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_ENABLE_HOOK_SHOPPING_CART', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_COMPATIBILITY_REVIEW', 'default_value' => 0, 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_SHOW_VOUCHER_BOX', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        /* theme */
        array('name' => 'OPC_THEME_BACKGROUND_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_BORDER_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_ICON_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_CONFIRM_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_CONFIRM_TEXT_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_TEXT_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_SELECTED_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_SELECTED_TEXT_COLOR', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_ALREADY_REGISTER_BUTTON', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array(
            'name' => 'OPC_ALREADY_REGISTER_BUTTON_TEXT',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_THEME_LOGIN_BUTTON', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_LOGIN_BUTTON_TEXT', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_VOUCHER_BUTTON', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_THEME_VOUCHER_BUTTON_TEXT', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array('name' => 'OPC_BACKGROUND_BUTTON_FOOTER', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        array(
            'name' => 'OPC_THEME_BORDER_BUTTON_FOOTER',
            'default_value' => '',
            'is_html' => false,
            'is_bool' => false
        ),
        array('name' => 'OPC_CONFIRMATION_BUTTON_FLOAT', 'default_value' => 1, 'is_html' => false, 'is_bool' => true),
        /* social */
        array('name' => 'OPC_SOCIAL_NETWORKS', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
        /* debug mode */
        array('name' => 'OPC_ENABLE_DEBUG', 'default_value' => '0', 'is_html' => false, 'is_bool' => true),
        array('name' => 'OPC_IP_DEBUG', 'default_value' => '', 'is_html' => false, 'is_bool' => false),
    );

    public function __construct()
    {
        $this->prefix_module = 'OPC';
        $this->name          = 'onepagecheckoutps';
        $this->displayName   = 'One Page Checkout PrestaShop';
        $this->tab           = 'checkout';
        $this->version       = '1.0.1';
        $this->author        = 'PresTeamShop';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        
        $this->module_key    = 'f4b7743a760d424aca4799adef34de89';

        if (property_exists($this, 'controllers')) {
            $this->controllers = array('login', 'payment', 'actions');
        }
        
        parent::__construct();

        $this->description      = $this->l('The simplest and  fastest way to increase sales.');
        $this->confirmUninstall = $this->l('Are you sure you want uninstall?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->globals->object = (object) array(
                'customer' => 'customer',
                'delivery' => 'delivery',
                'invoice'  => 'invoice',
        );

        $this->globals->type = (object) array(
                'isAddress'     => 'string',
                'isBirthDate'   => 'string',
                'isDate'        => 'string',
                'isBool'        => 'boolean',
                'isCityName'    => 'string',
                'isDniLite'     => 'string',
                'isEmail'       => 'string',
                'isGenericName' => 'string',
                'isMessage'     => 'text',
                'isName'        => 'string',
                'isPasswd'      => 'password',
                'isPhoneNumber' => 'string',
                'isPostCode'    => 'string',
                'isVatNumber'   => 'string',
                'number'        => 'integer',
                'url'           => 'string',
                'confirmation'  => 'string',
        );

        $this->globals->theme = (object) array(
                'gray'  => 'gray',
                'blue'  => 'blue',
                'black' => 'black',
                'green' => 'green',
                'red'   => 'red',
        );

        $this->globals->lang->object = array(
            'customer' => $this->l('Customer'),
            'delivery' => $this->l('Address delivery'),
            'invoive'  => $this->l('Address invoice'),
        );

        $this->globals->lang->theme = array(
            'gray'  => $this->l('Gray'),
            'blue'  => $this->l('Blue'),
            'black' => $this->l('Black'),
            'green' => $this->l('Green'),
            'red'   => $this->l('Red'),
        );

        $this->onepagecheckoutps_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';
        $this->onepagecheckoutps_tpl = _PS_ROOT_DIR_.'/modules/'.$this->name.'/';
        $this->translation_dir = _PS_MODULE_DIR_.$this->name.'/translations/';

        if (property_exists($this, 'controllers')) {
            $this->controllers = array('login', 'payment');
        }

        $overrides = array(
            'override/controllers/front/OrderController.php'
        );

        $text_override_must_copy      = $this->l('You must copy the file');
        $text_override_at_root        = $this->l('at the root of your store');
        $text_override_create_folders = $this->l('Create folders if necessary.');

        foreach ($overrides as $override) {
            if (!$this->existOverride($override)) {
                if (!$this->copyOverride($override)) {
                    $text_override    = $text_override_must_copy.' "/modules/'.$this->name.'/public/'.$override.'" '
                        .$text_override_at_root.' "/'.$override.'". '.$text_override_create_folders;
                    $this->warnings[] = $text_override;
                }
            } else {
                if (!$this->existOverride($override, '/KEY_'.$this->prefix_module.'_'.$this->version.'/')) {
                    rename(_PS_ROOT_DIR_.'/'.$override, _PS_ROOT_DIR_.'/'.$override.'_BK-'.$this->prefix_module.'-PTS_'.date('Y-m-d'));
                    if (!$this->copyOverride($override)) {
                        $text_override    = $text_override_must_copy.' "/modules/'.$this->name.'/public/'.$override.'" '
                            .$text_override_at_root.' "/'.$override.'". '.$text_override_create_folders;
                        $this->warnings[] = $text_override;
                    }
                }
            }
        }

        //-----------------------------------------------------------------------------------
        $query_cs = new DbQuery();
        $query_cs->from('customer');
        $query_cs->where('id_customer = '.(int) Configuration::get('OPC_ID_CUSTOMER'));
        $result_cs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query_cs);

        $query_csg = new DbQuery();
        $query_csg->from('customer_group');
        $query_csg->where('id_customer = '.(int) Configuration::get('OPC_ID_CUSTOMER'));
        $result_csg = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query_csg);

        if ((!$result_cs || !$result_csg) && Module::isInstalled($this->name)) {
            $this->createCustomerOPC();
        }
        //-----------------------------------------------------------------------------------

        if (Configuration::get('PS_DISABLE_OVERRIDES') == 1 && Validate::isLoadedObject($this->context->employee)) {
            $this->warnings[] = $this->l('This module does not work with the override disabled in your store. Turn off option -Disable all overrides- on -Advanced Parameters--Performance-');
        }

        require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/FieldClass.php';
        require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/FieldControl.php';
        require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/FieldOptionClass.php';
        require_once _PS_MODULE_DIR_.'onepagecheckoutps/classes/PaymentClass.php';

        //Delete fields required, this cause problem on our module.
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('DELETE FROM '._DB_PREFIX_.'required_field');

        if (!array_key_exists('sortby', $this->context->smarty->registered_plugins['modifier'])) {
            $this->context->smarty->registerPlugin('modifier', 'sortby', array($this, 'smartyModifierSortby'));
        }

        $this->checkModulePTS();

        if (isset($this->context->cookie->opc_suggest_address)
            && (!$this->context->customer->isLogged()
                || ($this->context->customer->isLogged() && !isset($this->context->cookie->id_cart)))
        ) {
            unset($this->context->cookie->opc_suggest_address);
        }

        if (!function_exists('curl_init')
            && !function_exists('curl_setopt')
            && !function_exists('curl_exec')
            && !function_exists('curl_close')
        ) {
            $this->errors[] = $this->l('CURL functions not available for registration module.');
        }
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('displayHeader') ||
            !$this->registerHook('displayShoppingCart') ||
            !$this->registerHook('actionShopDataDuplication') ||
            !$this->registerHook('displayAdminOrder') ||
            !$this->registerHook('displayAdminHomeQuickLinks') ||
            !$this->registerHook('actionCarrierUpdate')
        ) {
            return false;
        }

        $this->createCustomerOPC();

        //install field shops
        $this->installLanguageShop();

        //social network for login
        $sc_google = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
        $json_networks = array(
            'facebook' => array(
                'network'       => 'Facebook',
                'name_network'  => 'Facebook',
                'client_id'     => '',
                'client_secret' => '',
                'scope'         => 'email,public_profile',
                'class_icon'    => 'facebook',
            ),
            'google'   => array(
                'network'       => 'Google',
                'name_network'  => 'Google',
                'client_id'     => '',
                'client_secret' => '',
                'scope' => $sc_google,
                'class_icon'    => 'google',
            ),
            'paypal'   => array(
                'network'       => 'Paypal',
                'name_network'  => 'Paypal',
                'client_id'     => '',
                'client_secret' => '',
                'scope' => 'openid profile email address',
                'class_icon'    => 'paypal',
            )
        );
        Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($json_networks));

        //desactiva el tema movil
        Configuration::updateValue('PS_ALLOW_MOBILE_DEVICE', 0);

        //config default group customer
        $id_customer_group = Configuration::get('PS_CUSTOMER_GROUP');
        if (!empty($id_customer_group)) {
            Configuration::updateValue('OPC_DEFAULT_GROUP_CUSTOMER', $id_customer_group);
        }

        $id_country_default = Configuration::get('PS_COUNTRY_DEFAULT');

        //update default country
        $sql_country = 'UPDATE '._DB_PREFIX_.'opc_field_shop fs';
        $sql_country .= ' INNER JOIN '._DB_PREFIX_.'opc_field f ON f.id_field = fs.id_field';
        $sql_country .= ' SET fs.default_value = \''.(int)$id_country_default.'\'';
        $sql_country .= ' WHERE f.name = \'id_country\'';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_country);

        //update state default
        $country = new Country($id_country_default);
        if (Validate::isLoadedObject($country) && $country->contains_states) {
            $states = State::getStatesByIdCountry($id_country_default);

            if (count($states)) {
                $id_state = $states[0]['id_state'];

                if (!empty($id_state)) {
                    $sql_state = 'UPDATE '._DB_PREFIX_.'opc_field_shop fs';
                    $sql_state .= ' INNER JOIN '._DB_PREFIX_.'opc_field f ON f.id_field = fs.id_field';
                    $sql_state .= ' SET fs.default_value = \''.(int)$id_state.'\'';
                    $sql_state .= ' WHERE f.name = \'id_state\'';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_state);
                }
            }
        }

        //remove class_cache.php
        $file_class_cache = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR. 'app'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.(_PS_MODE_DEV_ ? 'dev' : 'prod').DIRECTORY_SEPARATOR.'class_index.php';
        if (file_exists($file_class_cache)) {
            unlink($file_class_cache);
        }

        //remove hook displayOverrideTemplate, else our module dont show.
        if ($ps_legalcompliance = $this->isModuleActive('ps_legalcompliance')) {
            $ps_legalcompliance->unregisterHook('displayOverrideTemplate');
        }

        return true;
    }

    public function uninstall()
    {
        $query = 'DELETE FROM `'._DB_PREFIX_.'customer` WHERE id_customer = '.$this->config_vars['OPC_ID_CUSTOMER'];
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
        $query = 'DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE id_customer = '.(int)$this->config_vars['OPC_ID_CUSTOMER'];
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        $forms = $this->getHelperForm();
        if (is_array($forms)
            && count($forms)
            && isset($forms['forms'])
            && is_array($forms['forms'])
            && count($forms['forms'])
        ) {
            foreach ($forms['forms'] as $key => $form) {
                if (Tools::isSubmit('form-'.$key)) {
                    $this->smarty->assign('CURRENT_FORM', $key);
                    //save form data in configuration
                    $this->saveFormData($form);
                    //show message
                    $this->smarty->assign('show_saved_message', true);
                    break;
                }
            }
        }

        if (Tools::isSubmit('form-review')) {
            Configuration::updateValue('PS_CONDITIONS', $this->config_vars['OPC_ENABLE_TERMS_CONDITIONS']);
            Configuration::updateValue('PS_CONDITIONS_CMS_ID', $this->config_vars['OPC_ID_CMS_TEMRS_CONDITIONS']);
        }

        $this->displayErrors();
        $this->displayForm();

        return $this->html;
    }

    public function saveCustomConfigValue($option, &$config_var_value)
    {
        $config_var_value = $config_var_value;
        switch ($option['name']) {
            case 'redirect_directly_to_opc':
                if (Tools::getIsset('enable_guest_checkout')) {
                    Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 1);
                } else {
                    Configuration::updateValue('PS_GUEST_CHECKOUT_ENABLED', 0);
                }
                break;
        }
    }

    public function downloadFileTranslation()
    {
        $iso_code = Tools::getValue('iso_code');
        $file_name = $iso_code.'.php';
        $file_path = realpath($this->translation_dir.$file_name);

        if (file_exists($file_path)) {
            header("Content-Disposition: attachment; filename=".$iso_code.'.php');
            header("Content-Type: application/octet-stream");
            header("Content-Length: ".filesize($file_path));
            readfile($file_path);
            exit;
        }
    }

    public function shareTranslation()
    {
        $iso_code = Tools::getValue('iso_code');
        $file_name = $iso_code.'.php';
        $file_path = realpath($this->translation_dir.$file_name);

        if (file_exists($file_path)) {
            $file_attachment = array();
            $file_attachment['content'] = Tools::file_get_contents($file_path);
            $file_attachment['name'] = $iso_code.'.php';
            $file_attachment['mime'] = 'application/octet-stream';

            $sql = 'SELECT id_lang FROM '._DB_PREFIX_.'lang WHERE iso_code = "en"';
            $id_lang = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

            if (empty($id_lang)) {
                $sql = 'SELECT id_lang FROM '._DB_PREFIX_.'lang WHERE iso_code = "es"';
                $id_lang = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            }

            $data = Mail::Send(
                $id_lang,
                'test',
                $_SERVER['SERVER_NAME'].' '.$this->l('he shared a translation with you'),
                array(),
                'info@presteamshop.com',
                null,
                null,
                null,
                $file_attachment,
                null,
                _PS_MAIL_DIR_,
                null,
                $this->context->shop->id
            );

            if ($data) {
                return array(
                    'message_code' => self::CODE_SUCCESS,
                    'message' => $this->l('Your translation has been sent, we will consider it for future upgrades of the module')
                );
            }
        }

        return array(
            'message_code' => self::CODE_ERROR,
            'message' => $this->l('An error has occurred to attempt send the translation')
        );
    }

    public function saveTranslations()
    {
        $data_translation = Tools::getValue('array_translation');
        $iso_code_selected = Tools::getValue('lang');

        $file_name = $iso_code_selected.'.php';
        $file_path = realpath($this->translation_dir.$file_name);

        if (!file_exists($file_path)) {
            touch($file_path);
        }

        if (is_writable($file_path)) {
            $line = '';

            $line .= '<?php'."\n";
            $line .= 'global $_MODULE;'."\n";
            $line .= '$_MODULE = array();'."\n";

            foreach ($data_translation as $key => $value) {
                foreach ($value as $data) {
                    $data['key_translation'] = trim($data['key_translation']);
                    $data['value_translation'] = trim($data['value_translation']);

                    if (empty($data['value_translation'])) {
                        continue;
                    }

                    $line .= '$_MODULE[\'<{'.$this->name.'}prestashop>'.$key.'_';
                    $line .= $data['key_translation'].'\']  = \'';
                    $line .= str_replace("'", "\'", $data['value_translation']).'\';'."\n";
                }
            }
            if (!file_put_contents($file_path, $line)) {
                return array(
                    'message_code' => self::CODE_ERROR,
                    'message' => $this->l('An error has occurred while attempting to save the translations')
                );
            } else {
                $path_file_template = dirname(__FILE__).'/../../themes/'._THEME_NAME_.'/modules/'.$this->name.'/translations/'.$iso_code_selected.'.php';
                if (file_exists($path_file_template)) {
                    unlink($path_file_template);
                }

                return array(
                    'message_code' => self::CODE_SUCCESS,
                    'message' => $this->l('The translations have been successfully saved')
                );
            }
        } else {
            return array(
                'message_code' => self::CODE_ERROR,
                'message' => $this->l('An error has occurred while attempting to save the translations')
            );
        }
    }

    public function getTranslations()
    {
        if (isset($this->context->cookie->id_lang)) {
            $id_lang = $this->context->cookie->id_lang;
        } else {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        $iso_code_selected = Language::getIsoById($id_lang);
        if (Tools::isSubmit('iso_code')) {
            $iso_code_selected = Tools::getValue('iso_code');
        }

        $array_translate = $this->readFile($this->name, 'en');

        if (sizeof($array_translate)) {
            $array_translate_lang_selected  = $this->readFile($this->name, $iso_code_selected, true);

            if (Tools::isSubmit('iso_code')) {
                foreach ($array_translate_lang_selected as &$items_array_translate_lang) {
                    if (in_array('', $items_array_translate_lang)) {
                        $items_array_translate_lang['empty_elements'] = true;
                    }
                }

                return array('message_code' => self::CODE_SUCCESS, 'data' => $array_translate_lang_selected);
            }

            foreach ($array_translate as $key_page => $translate_en) {
                foreach ($translate_en as $md5 => $label) {
                    $label = $label;
                    if (!empty($md5) && !empty($key_page)) {
                        $array_translate[$key_page][$md5]['lang_selected'] = '';
                        if (sizeof($array_translate_lang_selected)
                            && isset($array_translate_lang_selected[$key_page][$md5])
                        ) {
                            $array_translate[$key_page][$md5]['lang_selected'] = $array_translate_lang_selected[$key_page][$md5];

                            if (empty($array_translate_lang_selected[$key_page][$md5])) {
                                $array_translate[$key_page]['empty_elements'] = true;
                            }
                        }
                    }
                }
            }
        }

        return $array_translate;
    }

    public function readFile($module, $iso_code, $detail = false)
    {
        $file_name = $iso_code.'.php';
        $file_path = realpath($this->translation_dir.$file_name);

        if (!file_exists($file_path)) {
            return array();
        }

        $file = fopen($file_path, 'r') or exit($this->l('Unable to open file'));

        $array_translate = array();

        while (!feof($file)) {
            $line =  fgets($file);
            $line_explode = explode('=', $line);

            $search_string = strpos($line_explode[0], '<{'.$module.'}prestashop>');

            if (array_key_exists(1, $line_explode) && $search_string) {
                $file_md5 = str_replace("$"."_MODULE['<{".$module."}prestashop>", '', $line_explode[0]);
                $file_md5 = str_replace("']", '', trim($file_md5));

                $explode_file_md5 = explode('_', $file_md5);
                $md5 = array_pop($explode_file_md5);
                $file_name = join('_', $explode_file_md5);


                $label_title = $file_name;
                $description_lang = str_replace(';', '', $line_explode[1]);
                $description_lang = str_replace("'", '', trim($description_lang));

                if ($detail) {
                    $array_translate[$label_title][$md5] = $description_lang;
                } else {
                    $array_translate[$label_title][$md5] = array(
                        $iso_code => str_replace("'", '', $description_lang)
                    );
                }
            }
        }
        fclose($file);

        return $array_translate;
    }

    protected function displayForm()
    {
        //update version module
        //---------------------------------------------------------------------------
        $registered_version = Configuration::get($this->prefix_module.'_VERSION');
        if ($registered_version != $this->version) {
            $this->installTab();

            $this->smarty->assign(array(
                'token' => Tools::encrypt($this->name.'/index'),
                'module_name' => $this->displayName,
                'module_version' => $this->version,
                'url_call' => $this->context->link->getAdminLink('AdminActions'.$this->prefix_module)
            ));

            $this->html = $this->display(__FILE__, 'views/templates/admin/update_version.tpl');

            return;
        }
        //---------------------------------------------------------------------------
        
        $js_files  = array();
        $css_files = array();

        //own bootstrap
        if ($this->context->language->is_rtl) {
            array_push($css_files, $this->_path.'views/css/lib/pts/pts-bootstrap_rtl.css');
        }

        //sortable
        array_push($js_files, $this->_path.'views/js/lib/jquery/plugins/sortable/jquery-sortable.js');
        array_push($css_files, $this->_path.'views/css/lib/jquery/plugins/sortable/jquery-sortable.css');

        //fileinput
        array_push($js_files, $this->_path.'views/js/lib/bootstrap/plugins/fileinput/bootstrap-fileinput.js');
        array_push($css_files, $this->_path.'views/css/lib/bootstrap/plugins/fileinput/bootstrap-fileinput.css');

        //color picker
        array_push($js_files, $this->_path.'views/js/lib/bootstrap/plugins/colorpicker/bootstrap-colorpicker.js');
        array_push($css_files, $this->_path.'views/css/lib/bootstrap/plugins/colorpicker/bootstrap-colorpicker.css');

        //tab drop
        array_push($js_files, $this->_path.'views/js/lib/bootstrap/plugins/tabdrop/tabdrop.js');
        array_push($css_files, $this->_path.'views/css/lib/bootstrap/plugins/tabdrop/tabdrop.css');

        //totalStorage
        array_push($js_files, $this->_path.'views/js/lib/jquery/plugins/total-storage/jquery.total-storage.min.js');

        //array_push($js_files, $this->_path.'views/js/lib/jquery/plugins/linedtextarea/jquery-linedtextarea.js');
        //array_push($css_files, $this->_path.'views/css/lib/jquery/plugins/linedtextarea/jquery-linedtextarea.css');

        $carriers = Carrier::getCarriers(Configuration::get('PS_LANG_DEFAULT'), true, false, false, null, 5);
        $payments = $this->getPaymentModulesInstalled();

        $field_position = $this->getFieldsPosition();

        $default_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $languages        = Language::getLanguages(false);

        //ids lang
        $lang_separator = utf8_encode(chr(164));
        $ids_flag       = array('field_description', 'option_field_description', 'custom_field_description');
        $ids_flag       = join($lang_separator, $ids_flag);
        $iso            = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));

        $server_name = Tools::strtolower($_SERVER['SERVER_NAME']);
        $server_name = str_ireplace('www.', '', $server_name);

        //update files editor with configuration values.
        $this->updateContentCodeEditors();

        $helper_form = $this->getHelperForm();

        //extra tabs for PresTeamShop
        $this->getExtraTabs($helper_form);

        //Asignacion de varibles a tpl de administracion.
        $this->params_back = array(
            'MODULE_PREFIX'                        => $this->prefix_module,
            'DEFAULT_LENGUAGE'                     => $default_language,
            'LANGUAGES'                            => $languages,
            'ISO_LANG'                             => $iso,
            'FLAGS_FIELD_DESCRIPTION'              => $this->displayFlags(
                $languages,
                $default_language,
                $ids_flag,
                'field_description',
                true
            ),
            'FLAGS_CUSTOM_FIELD_DESCRIPTION'       => $this->displayFlags(
                $languages,
                $default_language,
                $ids_flag,
                'custom_field_description',
                true
            ),
            'FLAGS_OPTION_FIELD_DESCRIPTION'       => $this->displayFlags(
                $languages,
                $default_language,
                $ids_flag,
                'option_field_description',
                true
            ),
            'STATIC_TOKEN'                         => Tools::getAdminTokenLite('AdminModules'),
            'HELPER_FORM'                          => $helper_form,
            'JS_FILES'                             => $js_files,
            'CSS_FILES'                            => $css_files,
            'CARRIERS'                             => $carriers,
            'PAYMENTS'                             => $payments,
            'FIELDS_POSITION'                      => $field_position,
            'GLOBALS_JS'                           => Tools::jsonEncode($this->globals),
            'GROUPS_CUSTOMER'                      => Group::getGroups($this->cookie->id_lang),
            'DISPLAY_NAME'                         => $this->displayName,
            'CMS'                                  => CMS::listCms($this->cookie->id_lang),
            'SOCIAL_LOGIN'                         => Tools::jsonDecode($this->config_vars['OPC_SOCIAL_NETWORKS']),
            'SHOP'                                 => $this->context->shop,
            'LINK'                                 => $this->context->link,
            'SHOP_PROTOCOL'                        => Tools::getShopProtocol(),
            'array_label_translate'                => $this->getTranslations(),
            'id_lang'                              => $this->context->language->id,
            'iso_lang_backoffice_shop'             => Language::getIsoById($this->context->employee->id_lang),
            'code_editors'                         => $this->codeEditors(),
            'remote_addr' => Tools::getRemoteAddr()
        );

        parent::displayForm();

        $this->smarty->assign('paramsBack', $this->params_back);
        
        $this->html .= $this->display(__FILE__, 'views/templates/admin/header.tpl');
        $this->html .= $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    private function installLanguageShop($shops = array())
    {
        if (empty($shops)) {
            $shops = Shop::getShops();
            $shops = array_keys($shops);
        } elseif (is_array($shops)) {
            $shops = array_values($shops);
        } else {
            $shops = array($shops);
        }

        $sql_shops = Tools::file_get_contents(dirname(__FILE__).'/sql/shop.sql');
        if ($sql_shops) {
            $sql_shops = str_replace('PREFIX_', _DB_PREFIX_, $sql_shops);
            foreach ($shops as $id_shop) {
                $sql_shop = str_replace('ID_SHOP', $id_shop, $sql_shops);
                $sql_shop = preg_split("/;\s*[\r\n]+/", $sql_shop);

                foreach ($sql_shop as $query_shop) {
                    if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($query_shop))) {
                        return false;
                    }
                }
            }
        }

        //install languages
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $iso_code = 'en';
            if (file_exists(dirname(__FILE__).'/translations/sql/'.$lang['iso_code'].'.sql')) {
                $iso_code = $lang['iso_code'];
            }

            $sql_langs = Tools::file_get_contents(dirname(__FILE__).'/translations/sql/'.$iso_code.'.sql');
            if ($sql_langs) {
                $sql_lang = str_replace('PREFIX_', _DB_PREFIX_, $sql_langs);
                $sql_lang = str_replace('ID_LANG', $lang['id_lang'], $sql_lang);
                foreach ($shops as $id_shop) {
                    $sql_lang_shop = str_replace('ID_SHOP', $id_shop, $sql_lang);
                    $sql_lang_shop = preg_split("/;\s*[\r\n]+/", $sql_lang_shop);

                    foreach ($sql_lang_shop as $query_lang_shop) {
                        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($query_lang_shop))) {
                            return false;
                        }
                    }
                }
            }
        }
    }

    private function createCustomerOPC()
    {
        //create customer module opc
        //--------------------------------------------
        $customer            = new Customer();
        $customer->firstname = 'OPC PTS Not Delete';
        $customer->lastname  = 'OPC PTS Not Delete';
        $customer->email     = 'noreply@presteamshop.com';
        $customer->passwd    = Tools::encrypt('OPC123456');
        $customer->active    = 0;
        $customer->deleted   = 1;

        $cpfuser = $this->isModuleActive('cpfuser');
        $pscielows = $this->isModuleActive('pscielows');
        if ($cpfuser || $pscielows) {
            $customer->document = '.';
            $customer->rg_ie = '.';
            $customer->doc_type = '.';
        }

        if (!$customer->add()) {
            return false;
        } else {
            Configuration::updateValue('OPC_ID_CUSTOMER', $customer->id);
        }
        //--------------------------------------------
    }

    /**
     * Extra tabs for PresTeamShop
     * @param type $helper_form
     */
    private function getExtraTabs(&$helper_form)
    {
        $helper_form['tabs']['translate'] = array(
            'label'   => $this->l('Translate'),
            'href'    => 'translate',
            'icon'    => 'globe'
        );

        $helper_form['tabs']['code_editors'] = array(
            'label'   => $this->l('Code Editors'),
            'href'    => 'code_editors',
            'icon'    => 'code'
        );

        if (file_exists(_PS_MODULE_DIR_.$this->name.'/docs/FAQs.json')) {
            $helper_form['tabs']['faqs'] = array(
                'label' => $this->l('FAQs'),
                'href' => 'faqs',
                'icon' => 'question-circle'
            );
        }

        $helper_form['tabs']['another_modules'] = array(
            'label' => $this->l('Another modules'),
            'href'  => 'another_modules',
            'icon'  => 'cubes',
        );

        $helper_form['tabs']['suggestions'] = array(
            'label'   => $this->l('Suggestions'),
            'href'    => 'suggestions',
            'icon'    => 'pencil'
        );
    }

    public function codeEditors()
    {
        $code_editors = array(
            'css' => array(
                array(
                    'filepath' => realpath(_PS_MODULE_DIR_.$this->name.'/views/css/front/override.css'),
                    'filename' => 'override',
                    'content' => Configuration::get('OPC_OVERRIDE_CSS')
                )
            ),
            'javascript' => array(
                array(
                    'filepath' => realpath(_PS_MODULE_DIR_.$this->name.'/views/js/front/override.js'),
                    'filename' => 'override',
                    'content' => Configuration::get('OPC_OVERRIDE_JS')
                )
            )
        );

        return $code_editors;
    }

    public function updateContentCodeEditors()
    {
        $code_editors = $this->codeEditors();

        foreach ($code_editors as $code_editor) {
            foreach ($code_editor as $value) {
                $filetype = pathinfo($value['filepath']);
                $content = '';
                if ($filetype['extension'] === 'css') {
                    $content = Configuration::get('OPC_OVERRIDE_CSS');
                } elseif ($filetype['extension'] === 'js') {
                    $content = Configuration::get('OPC_OVERRIDE_JS');
                }

                if (!empty($content)) {
                    $this->saveContentCodeEditors($value['filepath'], $content);
                }
            }
        }
    }

    public function saveContentCodeEditors($filepath = null, $content = null)
    {
        $content = (!is_null($content)) ? $content : urldecode(Tools::getValue('content'));
        $filepath = (!is_null($filepath)) ? $filepath : urldecode(Tools::getValue('filepath'));

        if (!file_exists($filepath)) {
            touch($filepath);
        } elseif (is_writable($filepath)) {
            $filetype = pathinfo($filepath);
            if ($filetype['extension'] === 'css') {
                Configuration::updateValue('OPC_OVERRIDE_CSS', $content);
            } elseif ($filetype['extension'] === 'js') {
                Configuration::updateValue('OPC_OVERRIDE_JS', $content);
            }

            $this->fillConfigVars();
            
            file_put_contents($filepath, $content);
        }

        return array('message_code' => self::CODE_SUCCESS, 'message' => $this->l('The code was successfully saved'));
    }
    
    /**
     * Get position of fields
     * @return type array with positions in "group, row, col" order.
     */
    public function getFieldsPosition()
    {
        //get fields
        $fields = FieldClass::getAllFields((int) $this->cookie->id_lang);

        $position = array();
        foreach ($fields as $field) {
            $position[$field->group][$field->row][$field->col] = $field;
        }

        return $position;
    }

    private function getGeneralForm()
    {
        $payment_methods = array(array('id_module' => '', 'name' => '--'));
        $payment_methods_ori = PaymentModule::getInstalledPaymentModules();
        foreach ($payment_methods_ori as $payment) {
            $payment_methods[] = $payment;
        }

        $options = array(
            'enable_debug' => array(
                'name' => 'enable_debug',
                'prefix' => 'chk',
                'label' => $this->l('Sandbox'),
                'type' => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_ENABLE_DEBUG'],
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'depends' => array(
                    'ip_debug' => array(
                        'name' => 'ip_debug',
                        'prefix' => 'txt',
                        'label' => $this->l('IP'),
                        'type' => $this->globals->type_control->textbox,
                        'value' => $this->config_vars['OPC_IP_DEBUG'],
                        'hidden_on' => false
                    )
                )
            ),
            'enable_guest_checkout' => array(
                'name'     => 'enable_guest_checkout',
                'prefix'   => 'chk',
                'label'    => $this->l('Enable guest checkout'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
            ),
            'redirect_directly_to_opc'   => array(
                'name'     => 'redirect_directly_to_opc',
                'prefix'   => 'chk',
                'label'    => $this->l('Show shopping cart before checkout'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC'],
            ),
            'show_delivery_virtual'      => array(
                'name'     => 'show_delivery_virtual',
                'prefix'   => 'chk',
                'label'    => $this->l('Show the delivery address for the purchase of virtual goods'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'],
            ),
            'default_payment_method'     => array(
                'name'           => 'default_payment_method',
                'prefix'         => 'lst',
                'label'          => $this->l('Choose a default payment method'),
                'type'           => $this->globals->type_control->select,
                'data'           => $payment_methods,
                'default_option' => $this->config_vars['OPC_DEFAULT_PAYMENT_METHOD'],
                'option_value'   => 'name',
                'option_text'    => 'name'
            ),
            'default_group_customer'     => array(
                'name'           => 'default_group_customer',
                'prefix'         => 'lst',
                'label'          => $this->l('Add new customers to the group'),
                'type'           => $this->globals->type_control->select,
                'data'           => Group::getGroups($this->cookie->id_lang),
                'default_option' => $this->config_vars['OPC_DEFAULT_GROUP_CUSTOMER'],
                'option_value'   => 'id_group',
                'option_text'    => 'name',
            ),
            'groups_customer_additional' => array(
                'name'             => 'groups_customer_additional',
                'prefix'           => 'lst',
                'label'            => $this->l('Add new customers in other groups'),
                'type'             => $this->globals->type_control->select,
                'multiple'         => true,
                'data'             => Group::getGroups($this->cookie->id_lang),
                'selected_options' => $this->config_vars['OPC_GROUPS_CUSTOMER_ADDITIONAL'],
                'option_value'     => 'id_group',
                'option_text'      => 'name',
                'condition'        => array(
                    'compare'  => $this->config_vars['OPC_DEFAULT_GROUP_CUSTOMER'],
                    'operator' => 'neq',
                    'value'    => 'id_group',
                ),
            ),
            'validate_dni'               => array(
                'name'     => 'validate_dni',
                'prefix'   => 'chk',
                'label'    => $this->l('Validate DNI/CIF/NIF Spain'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_VALIDATE_DNI'],
            ),
            'id_content_page'            => array(
                'name'   => 'id_content_page',
                'prefix' => 'txt',
                'label'  => $this->l('Container page (HTML)'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_ID_CONTENT_PAGE'],
            ),
            'id_customer'                => array(
                'name'    => 'id_customer',
                'prefix'  => 'txt',
                'label'   => $this->l('Customer ID'),
                'type'    => $this->globals->type_control->textbox,
                'value'   => $this->config_vars['OPC_ID_CUSTOMER'],
                'tooltip' => array(
                    'warning' => array(
                        'title'   => $this->l('Warning'),
                        'content' => $this->l('Do not change unless you understand its functionality.'),
                    ),
                ),
            )
        );

        $form = array(
            'tab'     => 'general',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-general',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options
        );

        return $form;
    }

    private function getRegisterForm()
    {
        $options = array(
            'show_button_register' => array(
                'name'     => 'show_button_register',
                'prefix'   => 'chk',
                'label'    => $this->l('Show button "Save Information"'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_BUTTON_REGISTER'],
            ),
            'capitalize_fields' => array(
                'name'     => 'capitalize_fields',
                'prefix'   => 'chk',
                'label'    => $this->l('Capitalize fields'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_CAPITALIZE_FIELDS'],
            ),
            'enable_privacy_policy' => array(
                'name'     => 'enable_privacy_policy',
                'prefix'   => 'chk',
                'label'    => $this->l('Require acceptance of privacy policy before buying'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_ENABLE_PRIVACY_POLICY'],
                'depends'  => array(
                    'id_cms_privacy_policy' => array(
                        'name'           => 'id_cms_privacy_policy',
                        'prefix'         => 'lst',
                        'type'           => $this->globals->type_control->select,
                        'data'           => CMS::listCms($this->cookie->id_lang),
                        'default_option' => $this->config_vars['OPC_ID_CMS_PRIVACY_POLICY'],
                        'hidden_on'      => false,
                        'option_value'   => 'id_cms',
                        'option_text'    => 'meta_title',
                    ),
                )
            ),
            'enable_invoice_address'      => array(
                'name'        => 'enable_invoice_address',
                'prefix'      => 'chk',
                'label'       => $this->l('Request invoice address'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_ENABLE_INVOICE_ADDRESS'],
                'data_toggle' => true,
                'depends'     => array(
                    'required_invoice_address' => array(
                        'name'      => 'required_invoice_address',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Required'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_REQUIRED_INVOICE_ADDRESS'],
                        'hidden_on' => false,
                    ),
                    'use_same_name_contact_ba' => array(
                        'name'      => 'use_same_name_contact_ba',
                        'prefix'    => 'chk',
                        'label' => $this->l('Use the same first name and last name for the customers invoice address'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA'],
                        'hidden_on' => false,
                    ),
                ),
            ),
            'use_same_name_contact_da'    => array(
                'name'     => 'use_same_name_contact_da',
                'prefix'   => 'chk',
                'label'    => $this->l('Use the same first name and last name for the customers delivery address'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA'],
            ),
            'request_confirm_email'       => array(
                'name'     => 'request_confirm_email',
                'prefix'   => 'chk',
                'label'    => $this->l('Request confirmation email'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REQUEST_CONFIRM_EMAIL'],
            ),
            'request_password'            => array(
                'name'     => 'request_password',
                'prefix'   => 'chk',
                'label'    => $this->l('Password request'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_REQUEST_PASSWORD'],
                'depends'  => array(
                    'option_autogenerate_password' => array(
                        'name'      => 'option_autogenerate_password',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Option to auto-generate'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD'],
                        'hidden_on' => false,
                        'class'     => 'option_autogenerate_password',
                    ),
                ),
            ),
            'choice_group_customer'       => array(
                'name'     => 'choice_group_customer',
                'prefix'   => 'chk',
                'label'    => $this->l('Show customer group list'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_CHOICE_GROUP_CUSTOMER'],
                'depends'  => array(
                    'choice_group_customer_allow' => array(
                        'name'             => 'choice_group_customer_allow',
                        'prefix'           => 'lst',
                        'hidden_on'        => false,
                        'type'             => $this->globals->type_control->select,
                        'multiple'         => true,
                        'data'             => Group::getGroups($this->cookie->id_lang),
                        'selected_options' => $this->config_vars['OPC_CHOICE_GROUP_CUSTOMER_ALLOW'],
                        'option_value'     => 'id_group',
                        'option_text'      => 'name',
                        'tooltip'          => array(
                            'warning' => array(
                                'title'   => $this->l('Warning'),
                                'content' => $this->l('If you choose a group then only the selected groups will be shown, otherwise all groups will be shown.'),
                            ),
                        ),
                    ),
                ),
            ),
            'show_list_cities_geonames' => array(
                'name'     => 'show_list_cities_geonames',
                'prefix'   => 'chk',
                'label'    => $this->l('Show list of cities using Geonames.org'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_LIST_CITIES_GEONAMES'],
            ),
            'auto_address_geonames' => array(
                'name'     => 'auto_address_geonames',
                'prefix'   => 'chk',
                'label'    => $this->l('Use address autocomplete from Geonames.org'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_AUTO_ADDRESS_GEONAMES'],
            ),
            'autocomplete_google_address' => array(
                'name'        => 'autocomplete_google_address',
                'prefix'      => 'chk',
                'label'       => $this->l('Use address autocomplete from Google'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_AUTOCOMPLETE_GOOGLE_ADDRESS'],
                'data_toggle' => true,
                'depends'     => array(
                    'google_api_key' => array(
                        'name'      => 'google_api_key',
                        'prefix'    => 'txt',
                        'label'     => $this->l('Google API KEY'),
                        'type'      => $this->globals->type_control->textbox,
                        'value'     => $this->config_vars['OPC_GOOGLE_API_KEY'],
                        'hidden_on' => false,
                    ),
                ),
            ),
        );

        $form = array(
            'tab'     => 'register',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-register',
                    'icon'  => 'save',
                ),
                'delete_address' => array(
                    'label' => $this->l('Delete empty addresses'),
                    'class' => 'delete-address',
                    'icon'  => 'trash',
                )
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getShippingForm()
    {
        $options = array(
            'show_description_carrier' => array(
                'name'     => 'show_description_carrier',
                'prefix'   => 'chk',
                'label'    => $this->l('Show description of carriers'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_DESCRIPTION_CARRIER'],
            ),
            'show_image_carrier'       => array(
                'name'     => 'show_image_carrier',
                'prefix'   => 'chk',
                'label'    => $this->l('Show image of carriers'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_SHOW_IMAGE_CARRIER'],
            ),
            'reload_shipping_by_state' => array(
                'name'     => 'reload_shipping_by_state',
                'prefix'   => 'chk',
                'label'    => $this->l('Reload shipping when changing state'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_RELOAD_SHIPPING_BY_STATE'],
            ),
            'force_need_postcode'      => array(
                'name'        => 'force_need_postcode',
                'prefix'      => 'chk',
                'label'       => $this->l('Require a postal code to be entered'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_FORCE_NEED_POSTCODE'],
                'data_toggle' => true
            ),
            'module_carrier_need_postcode' => array(
                'name'      => 'module_carrier_need_postcode',
                'prefix'    => 'txt',
                'label'     => $this->l('Carrier module that requires a postal code'),
                'type'      => $this->globals->type_control->textbox,
                'value'     => $this->config_vars['OPC_MODULE_CARRIER_NEED_POSTCODE'],
                'hidden_on' => $this->config_vars['OPC_FORCE_NEED_POSTCODE'],
            ),
            'force_need_city'          => array(
                'name'        => 'force_need_city',
                'prefix'      => 'chk',
                'label'       => $this->l('Require a city to be entered'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_FORCE_NEED_CITY'],
                'data_toggle' => true
            ),
            'module_carrier_need_city' => array(
                'name'      => 'module_carrier_need_city',
                'prefix'    => 'txt',
                'label'     => $this->l('Carrier module that requires a city'),
                'type'      => $this->globals->type_control->textbox,
                'value'     => $this->config_vars['OPC_MODULE_CARRIER_NEED_CITY'],
                'hidden_on' => $this->config_vars['OPC_FORCE_NEED_CITY'],
            )
        );

        $form = array(
            'tab'     => 'shipping',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-shipping',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getPaymentForm()
    {
        //$popup_lang = $this->l('If you enable this option, some payment methods stop working. We recommend testing the operation.');

        $options = array(
            'show_image_payment' => array(
                'name'        => 'show_image_payment',
                'prefix'      => 'chk',
                'label'       => $this->l('Show images of payment methods'),
                'label_on'    => $this->l('YES'),
                'label_off'   => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_SHOW_IMAGE_PAYMENT']
            ),
            'show_detail_payment' => array(
                'name'        => 'show_detail_payment',
                'prefix'      => 'chk',
                'label'       => $this->l('Show detailed description of payment methods'),
                'label_on'    => $this->l('YES'),
                'label_off'   => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_SHOW_DETAIL_PAYMENT']
            )
            /*'show_popup_payment'     => array(
                'name'        => 'show_popup_payment',
                'prefix'      => 'chk',
                'label'       => $this->l('Show popup window payment'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_SHOW_POPUP_PAYMENT'],
                'data_toggle' => true,
                'tooltip'     => array(
                    'information' => array(
                        'title'   => $this->l('Information'),
                        'content' => $popup_lang,
                    ),
                ),
            ),
            'payments_without_radio' => array(
                'name'     => 'payments_without_radio',
                'prefix'   => 'chk',
                'label'    => $this->l('Activate compatibility with non-supported payment methods'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_PAYMENTS_WITHOUT_RADIO'],
            ),
            'modules_without_popup'  => array(
                'name'      => 'modules_without_popup',
                'prefix'    => 'ta',
                'label'     => $this->l('Deactivate a modules popup window'),
                'type'      => $this->globals->type_control->textarea,
                'value'     => $this->config_vars['OPC_MODULES_WITHOUT_POPUP'],
                'data_hide' => 'show_popup_payment',
            ),*/
        );

        $form = array(
            'tab'     => 'payment_general',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-payment',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getReviewForm()
    {
        $options = array(
            'enable_terms_conditions'      => array(
                'name'     => 'enable_terms_conditions',
                'prefix'   => 'chk',
                'label'    => $this->l('Require acceptance of terms and conditions before buying'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => $this->config_vars['OPC_ENABLE_TERMS_CONDITIONS'],
                'depends'  => array(
                    'id_cms_temrs_conditions' => array(
                        'name'           => 'id_cms_temrs_conditions',
                        'prefix'         => 'lst',
                        'type'           => $this->globals->type_control->select,
                        'data'           => CMS::listCms($this->cookie->id_lang),
                        'default_option' => $this->config_vars['OPC_ID_CMS_TEMRS_CONDITIONS'],
                        'hidden_on'      => false,
                        'option_value'   => 'id_cms',
                        'option_text'    => 'meta_title',
                    ),
                ),
            ),
            'show_link_continue_shopping'  => array(
                'name'        => 'show_link_continue_shopping',
                'prefix'      => 'chk',
                'label'       => $this->l('Show "Continue Shopping" link'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_SHOW_LINK_CONTINUE_SHOPPING'],
                'data_toggle' => true,
                'depends'  => array(
                    'link_continue_shopping' => array(
                        'name'      => 'link_continue_shopping',
                        'prefix'    => 'txt',
                        'label'     => $this->l('Custom URL for the "Continue shopping" button'),
                        'type'      => $this->globals->type_control->textbox,
                        'value'     => $this->config_vars['OPC_LINK_CONTINUE_SHOPPING'],
                        'hidden_on'   => false,
                        'data_hide' => 'show_link_continue_shopping'
                    )
                )
            ),
            'compatibility_review'         => array(
                'name'        => 'compatibility_review',
                'prefix'      => 'chk',
                'label'       => $this->l('Show compatibility summary'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_COMPATIBILITY_REVIEW'],
                'data_toggle' => true,
                'depends'     => array(
                    'show_voucher_box' => array(
                        'name'        => 'show_voucher_box',
                        'prefix'      => 'chk',
                        'label'       => $this->l('Show voucher box'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'        => $this->globals->type_control->checkbox,
                        'check_on'    => $this->config_vars['OPC_SHOW_VOUCHER_BOX'],
                        'hidden_on'   => true,
                        'tooltip' => array(
                            'warning' => array(
                                'title'   => $this->l('Warning'),
                                'content' => $this->l('So have enabled this option, you must have discounts created to be shown.'),
                            ),
                        )
                    ),
                    'show_zoom_image_product' => array(
                        'name'        => 'show_zoom_image_product',
                        'prefix'      => 'chk',
                        'label'       => $this->l('Show zoom on image product'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'        => $this->globals->type_control->checkbox,
                        'check_on'    => $this->config_vars['OPC_SHOW_ZOOM_IMAGE_PRODUCT'],
                        'hidden_on'   => true
                    ),
                    'show_total_product'           => array(
                        'name'      => 'show_total_product',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total of products'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_PRODUCT'],
                        'hidden_on'   => true
                    ),
                    'show_total_discount'          => array(
                        'name'      => 'show_total_discount',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total discount'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_DISCOUNT'],
                        'hidden_on'   => true
                    ),
                    'show_total_wrapping'          => array(
                        'name'      => 'show_total_wrapping',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show gift wrapping total'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_WRAPPING'],
                        'data_hide' => 'compatibility_review',
                        'hidden_on'   => true
                    ),
                    'show_total_shipping'          => array(
                        'name'      => 'show_total_shipping',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show shipping total'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_SHIPPING'],
                        'hidden_on'   => true
                    ),
                    'show_total_without_tax'       => array(
                        'name'      => 'show_total_without_tax',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total excluding tax'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_WITHOUT_TAX'],
                        'hidden_on'   => true
                    ),
                    'show_total_tax'               => array(
                        'name'      => 'show_total_tax',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total tax'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_TAX'],
                        'hidden_on'   => true
                    ),
                    'show_total_price'             => array(
                        'name'      => 'show_total_price',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show total'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_TOTAL_PRICE'],
                        'data_hide' => 'compatibility_review',
                        'hidden_on'   => true
                    ),
                    'show_remaining_free_shipping' => array(
                        'name'      => 'show_remaining_free_shipping',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show amount remaining to qualify for free shipping'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_REMAINING_FREE_SHIPPING'],
                        'hidden_on'   => true
                    ),
                    'show_weight'                  => array(
                        'name'      => 'show_weight',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show weight'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_WEIGHT'],
                        'hidden_on'   => true
                    ),
                    'show_reference'               => array(
                        'name'      => 'show_reference',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show reference'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_REFERENCE'],
                        'data_hide' => 'compatibility_review',
                        'hidden_on'   => true
                    ),
                    'show_unit_price' => array(
                        'name'      => 'show_unit_price',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show unit price'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_UNIT_PRICE'],
                        'hidden_on'   => true
                    ),
                    'show_availability' => array(
                        'name'      => 'show_availability',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Show availability'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_SHOW_AVAILABILITY'],
                        'hidden_on'   => true
                    ),
                    'enable_hook_shopping_cart'    => array(
                        'name'      => 'enable_hook_shopping_cart',
                        'prefix'    => 'chk',
                        'label'     => $this->l('Enable hook shopping cart'),
                        'label_on' => $this->l('YES'),
                        'label_off' => $this->l('NO'),
                        'type'      => $this->globals->type_control->checkbox,
                        'check_on'  => $this->config_vars['OPC_ENABLE_HOOK_SHOPPING_CART'],
                        'hidden_on'   => true
                    )
                )
            )
        );

        $form = array(
            'tab'     => 'review',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-review',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getThemeForm()
    {
        $options = array(
            'theme_background_color'   => array(
                'name'   => 'theme_background_color',
                'prefix' => 'txt',
                'label'  => $this->l('Background color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_BACKGROUND_COLOR'],
                'color'  => true
            ),
            'theme_border_color'       => array(
                'name'   => 'theme_border_color',
                'prefix' => 'txt',
                'label'  => $this->l('Border color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_BORDER_COLOR'],
                'color'  => true
            ),
            'theme_icon_color'         => array(
                'name'   => 'theme_icon_color',
                'prefix' => 'txt',
                'label'  => $this->l('Color of images'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_ICON_COLOR'],
                'color'  => true
            ),
            'theme_text_color'         => array(
                'name'   => 'theme_text_color',
                'prefix' => 'txt',
                'label'  => $this->l('Text color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_TEXT_COLOR'],
                'color'  => true
            ),
            'theme_selected_color' => array(
                'name'   => 'theme_selected_color',
                'prefix' => 'txt',
                'label'  => $this->l('Carrier and Payment selected color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_SELECTED_COLOR'],
                'color'  => true
            ),
            'theme_selected_text_color' => array(
                'name'   => 'theme_selected_text_color',
                'prefix' => 'txt',
                'label'  => $this->l('Carrier and Payment selected text color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_SELECTED_TEXT_COLOR'],
                'color'  => true
            ),
            'theme_confirm_color'      => array(
                'name'   => 'theme_confirm_color',
                'prefix' => 'txt',
                'label'  => $this->l('Checkout button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_CONFIRM_COLOR'],
                'color'  => true
            ),
            'theme_confirm_text_color' => array(
                'name'   => 'theme_confirm_text_color',
                'prefix' => 'txt',
                'label'  => $this->l('Text color of checkout button'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_CONFIRM_TEXT_COLOR'],
                'color'  => true
            ),
            'already_register_button' => array(
                'name'   => 'already_register_button',
                'prefix' => 'txt',
                'label'  => $this->l('Already register button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_ALREADY_REGISTER_BUTTON'],
                'color'  => true
            ),
            'already_register_button_text' => array(
                'name'   => 'already_register_button_text',
                'prefix' => 'txt',
                'label'  => $this->l('Already register text button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_ALREADY_REGISTER_BUTTON_TEXT'],
                'color'  => true
            ),
            'theme_login_button' => array(
                'name'   => 'theme_login_button',
                'prefix' => 'txt',
                'label'  => $this->l('Login button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_LOGIN_BUTTON'],
                'color'  => true
            ),
            'theme_login_button_text' => array(
                'name'   => 'theme_login_button_text',
                'prefix' => 'txt',
                'label'  => $this->l('Login text button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_LOGIN_BUTTON_TEXT'],
                'color'  => true
            ),
            'theme_voucher_button' => array(
                'name'   => 'theme_voucher_button',
                'prefix' => 'txt',
                'label'  => $this->l('Voucher button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_VOUCHER_BUTTON'],
                'color'  => true
            ),
            'theme_voucher_button_text' => array(
                'name'   => 'theme_voucher_button_text',
                'prefix' => 'txt',
                'label'  => $this->l('Voucher text button color'),
                'type'   => $this->globals->type_control->textbox,
                'value'  => $this->config_vars['OPC_THEME_VOUCHER_BUTTON_TEXT'],
                'color'  => true
            ),
            'confirmation_button_float' => array(
                'name'        => 'confirmation_button_float',
                'prefix'      => 'chk',
                'label'       => $this->l('Show confirmation button float'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'        => $this->globals->type_control->checkbox,
                'check_on'    => $this->config_vars['OPC_CONFIRMATION_BUTTON_FLOAT'],
                'data_toggle' => true,
                'depends'  => array(
                    'background_button_footer' => array(
                        'name'   => 'background_button_footer',
                        'prefix' => 'txt',
                        'label'  => $this->l('Background color float confirmation button'),
                        'type'   => $this->globals->type_control->textbox,
                        'value'  => $this->config_vars['OPC_BACKGROUND_BUTTON_FOOTER'],
                        'color'  => true,
                        'hidden_on' => false,
                        'data_hide' => 'confirmation_button_float'
                    ),
                    'theme_border_button_footer' => array(
                        'name'   => 'theme_border_button_footer',
                        'prefix' => 'txt',
                        'label'  => $this->l('Border color float confirmation button'),
                        'type'   => $this->globals->type_control->textbox,
                        'value'  => $this->config_vars['OPC_THEME_BORDER_BUTTON_FOOTER'],
                        'color'  => true,
                        'hidden_on' => false,
                        'data_hide' => 'confirmation_button_float'
                    )
                )
            ),
        );

        $form = array(
            'tab'     => 'theme',
            'method'  => 'post',
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'class' => 'save-theme',
                    'icon'  => 'save',
                ),
            ),
            'options' => $options,
        );

        return $form;
    }

    private function getRequiredFieldsForm()
    {
        $options = array(
            'field_id'            => array(
                'name'   => 'id_field',
                'prefix' => 'hdn',
                'type'   => 'hidden',
            ),
            'field_object'        => array(
                'name'   => 'field_object',
                'prefix' => 'lst',
                'label'  => $this->l('Object'),
                'type'   => $this->globals->type_control->select,
                'data'   => $this->globals->object,
            ),
            'field_name'          => array(
                'name'   => 'field_name',
                'prefix' => 'txt',
                'label'  => $this->l('Name'),
                'type'   => $this->globals->type_control->textbox,
            ),
            'field_description'   => array(
                'name'      => 'field_description',
                'prefix'    => 'txt',
                'label'     => $this->l('Description'),
                'type'      => $this->globals->type_control->textbox,
                'multilang' => true,
            ),
            'field_type'          => array(
                'name'         => 'field_type',
                'prefix'       => 'lst',
                'label'        => $this->l('Type'),
                'type'         => $this->globals->type_control->select,
                'data'         => $this->globals->type,
                'key_as_value' => true,
            ),
            'field_size'          => array(
                'name'   => 'field_size',
                'prefix' => 'txt',
                'label'  => $this->l('Size'),
                'type'   => $this->globals->type_control->textbox,
            ),
            'field_type_control'  => array(
                'name'   => 'field_type_control',
                'prefix' => 'lst',
                'label'  => $this->l('Type control'),
                'type'   => $this->globals->type_control->select,
                'data'   => $this->globals->type_control,
            ),
            'field_default_value' => array(
                'name'   => 'field_default_value',
                'prefix' => 'txt',
                'label'  => $this->l('Default value'),
                'type'   => $this->globals->type_control->textbox,
            ),
            'field_required'      => array(
                'name'     => 'field_required',
                'prefix'   => 'chk',
                'label'    => $this->l('Required'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => true,
            ),
            'field_active'        => array(
                'name'     => 'field_active',
                'prefix'   => 'chk',
                'label'    => $this->l('Active'),
                'label_on' => $this->l('YES'),
                'label_off' => $this->l('NO'),
                'type'     => $this->globals->type_control->checkbox,
                'check_on' => true,
            ),
        );

        $list = $this->getRequiredFieldList();

        $form = array(
            'id'      => 'form_required_fields',
            'tab'     => 'required_fields',
            'class'   => 'hidden',
            'modal'   => true,
            'actions' => array(
                'save' => array(
                    'label' => $this->l('Save'),
                    'name'  => 'update_field',
                    'icon'  => 'save',
                )
            ),
            'options' => $options,
            'list'    => $list,
        );

        return $form;
    }

    private function getSocialSubTabs()
    {
        $social_networks = Tools::jsonDecode($this->config_vars['OPC_SOCIAL_NETWORKS']);
        $sub_tabs        = array();

        if ($social_networks) {
            foreach ($social_networks as $name => $social_network) {
                $sub_tabs[] = array(
                    'label' => $social_network->name_network,
                    'href'  => 'social_login_'.$name,
                    'icon'  => $social_network->class_icon,
                );
            }
        }

        return $sub_tabs;
    }

    private function getHelperTabs()
    {
        $tabs = array(
            'general'         => array(
                'label' => $this->l('General'),
                'href'  => 'general',
            ),
            'register'        => array(
                'label' => $this->l('Register'),
                'href'  => 'register',
                'icon'  => 'user',
            ),
            'shipping'        => array(
                'label' => $this->l('Shipping'),
                'href'  => 'shipping',
                'icon'  => 'truck',
            ),
            'payment'         => array(
                'label'   => $this->l('Payment'),
                'href'    => 'payment',
                'icon'    => 'credit-card',
                'sub_tab' => array(
                    'payment_general' => array(
                        'label' => $this->l('General'),
                        'href'  => 'payment_general',
                        'icon'  => 'cogs',
                    ),
                    'pay_methods'  => array(
                        'label' => $this->l('Pay methods'),
                        'href'  => 'pay_methods',
                        'icon'  => 'credit-card',
                    ),
                    'ship_pay'     => array(
                        'label' => $this->l('Ship to Pay'),
                        'href'  => 'ship_pay',
                        'icon'  => 'truck',
                    ),
                ),
            ),
            'review'          => array(
                'label' => $this->l('Review'),
                'href'  => 'review',
                'icon'  => 'check',
            ),
            'theme'           => array(
                'label' => $this->l('Theme'),
                'href'  => 'theme',
                'icon'  => 'paint-brush',
            ),
            'required_fields' => array(
                'label' => $this->l('Fields register'),
                'href'  => 'required_fields',
                'icon'  => 'pencil-square-o',
            ),
            'fields_position' => array(
                'label' => $this->l('Fields position'),
                'href'  => 'fields_position',
                'icon'  => 'arrows',
            ),
            'social_login'    => array(
                'label'   => $this->l('Social login'),
                'href'    => 'social_login',
                'icon'    => 'share-alt',
                'sub_tab' => $this->getSocialSubTabs(),
            )
        );

        return $tabs;
    }

    private function getHelperForm()
    {
        $tabs = $this->getHelperTabs();

        $general       = $this->getGeneralForm();
        $register      = $this->getRegisterForm();
        $shipping      = $this->getShippingForm();
        $payment       = $this->getPaymentForm();
        $review        = $this->getReviewForm();
        $theme         = $this->getThemeForm();

        $fields_register = $this->getRequiredFieldsForm();
        $form            = array(
            'title' => $this->l('Menu'),
            'tabs'  => $tabs,
            'forms' => array(
                'general'         => $general,
                'register'        => $register,
                'shipping'        => $shipping,
                'payment_general' => $payment,
                'review'          => $review,
                'theme'           => $theme,
                'fields_register' => $fields_register,
            ),
        );

        return $form;
    }

    private function getPaymentModulesInstalled()
    {
        //get payments
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
			FROM `'._DB_PREFIX_.'module` m
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON (hm.`id_module` = m.`id_module`
                AND hm.id_shop='.(int) $this->context->shop->id.')
            LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
			INNER JOIN `'._DB_PREFIX_.'module_shop` ms ON (m.`id_module` = ms.`id_module`
                AND ms.id_shop='.(int) $this->context->shop->id.')
            WHERE h.`name` = "PaymentOptions"
		');

        if ($result) {
            foreach ($result as &$row) {
                $row['force_display'] = 0;
                $row['name_image'] = $row['name'].'.gif';

                $id_payment = PaymentClass::getIdPaymentBy('name', $row['name']);

                if (!empty($id_payment)) {
                    $payment = new PaymentClass($id_payment);
                    if (Validate::isLoadedObject($payment)) {
                        $row['data']['title']       = $payment->title;
                        $row['data']['description'] = $payment->description;
                        if (!empty($payment->name_image)) {
                            $row['name_image'] = $payment->name_image;
                        }
                        $row['force_display'] = $payment->force_display;

                        $payment->id_module = $row['id_module'];
                        $payment->update();
                    }
                }
            }
        } else {
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE '._DB_PREFIX_.'opc_payment');
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE '._DB_PREFIX_.'opc_payment_lang');
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('TRUNCATE TABLE '._DB_PREFIX_.'opc_payment_shop');
        }

        return $result;
    }

    public function saveSocialLogin()
    {
        $data            = Tools::getValue('data');
        $social_networks = Tools::jsonDecode($this->config_vars['OPC_SOCIAL_NETWORKS']);

        foreach ($data['values'] as $key => $value) {
            $social_networks->{$data['social_network']}->{$key} = trim($value);
        }

        Configuration::updateValue('OPC_SOCIAL_NETWORKS', Tools::jsonEncode($social_networks));

        return array(
            'message_code' => self::CODE_SUCCESS,
            'message'      => $this->l('Social login data updated successful')
        );
    }

    public function getOptionsByField()
    {
        $id_field = Tools::getValue('id_field');
        $options  = FieldOptionClass::getOptionsByIdField($id_field);
        //return result
        return array('message_code' => self::CODE_SUCCESS, 'options' => $options);
    }

    public function saveOptionsByField()
    {
        $id_field = Tools::getValue('id_field');
        $options  = Tools::getValue('options');

        if (!empty($options)) {
            foreach ($options as $option) {
                if (empty($option['id_option']) || (int) $option['id_option'] === 0) {
                    $option['id_option'] = null;
                }

                $field_option = new FieldOptionClass($option['id_option']);

                $description_value = array();
                foreach ($option['description'] as $description) {
                    $description_value[$description['id_lang']] = $description['value'];
                }

                $field_option->id_field    = $id_field;
                $field_option->value       = $option['value'];
                $field_option->description = $description_value;
                $field_option->save();
            }
        }

        $options_to_remove = Tools::getValue('options_to_remove');
        if (!empty($options_to_remove)) {
            foreach ($options_to_remove as $option_to_remove) {
                $field_option = new FieldOptionClass($option_to_remove);
                $field_option->delete();
            }
        }

        //return result
        return array('message_code' => self::CODE_SUCCESS, 'message' => $this->l('Options updated successful.'));
    }

    public function getFieldsByObject()
    {
        $object_name = Tools::getValue('object');
        $fields_db   = FieldClass::getAllFields(
            $this->cookie->id_lang,
            $this->context->shop->id,
            $object_name,
            null,
            null,
            null,
            null,
            true
        );
        $fields = array();
        foreach ($fields_db as $field) {
            $fields[] = array(
                'id_field'    => $field->id,
                'name'        => $field->name,
                'description' => $field->description,
            );
        }
        //return result
        return array('message_code' => self::CODE_SUCCESS, 'fields' => $fields);
    }

    /**
     * Save field positions
     */
    public function saveFieldsPosition()
    {
        //update positions
        $positions = Tools::getValue('positions');
        if (is_array($positions) && count($positions)) {
            foreach ($positions as $row => $cols) {
                if (is_array($cols) && count($cols)) {
                    foreach ($cols as $col => $data) {
                        $field        = new FieldClass($data['id_field']);
                        $field->group = $data['group'];
                        $field->row   = $row;
                        $field->col   = $col;
                        $field->save();
                    }
                }
            }
        }
        //return result
        return array('message_code' => self::CODE_SUCCESS, 'message' => $this->l('Positions updated successful.'));
    }

    /**
     * Toggle required fieldstatus.
     * @return type array
     */
    public function toggleActiveField()
    {
        if (Tools::isSubmit('id_field')) {
            $field_class = new FieldClass((int) Tools::getValue('id_field'));

            if (Validate::isLoadedObject($field_class)) {
                $field_class->active = !$field_class->active;

                if ($field_class->update()) {
                    return array(
                        'message_code' => self::CODE_SUCCESS,
                        'message'      => $this->l('Field updated successful.'),
                    );
                }
            }
        }

        return array(
            'message_code' => self::CODE_ERROR,
            'message'      => $this->l('An error occurred while trying to update.')
        );
    }

    /**
     * Toggle required fieldstatus.
     * @return type array
     */
    public function toggleRequiredField()
    {
        if (Tools::isSubmit('id_field')) {
            $field_class = new FieldClass((int) Tools::getValue('id_field'));

            if (Validate::isLoadedObject($field_class)) {
                $field_class->required = !$field_class->required;

                if ($field_class->update()) {
                    return array(
                        'message_code' => self::CODE_SUCCESS,
                        'message'      => $this->l('Field updated successful.'),
                    );
                }
            }
        }

        return array(
            'message_code' => self::CODE_ERROR,
            'message'      => $this->l('An error occurred while trying to update.')
        );
    }

    /**
     * Remove associations of shipment and payment, then will create again from data form.
     * @return type array
     */
    public function updateShipToPay()
    {
        if (!Tools::isSubmit('payment_carrier')) {
            return array(
                'message_code' => self::CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.')
            );
        }

        $carriers = Tools::getValue('payment_carrier');

        //Reset table asociations
        $query  = 'DELETE FROM '._DB_PREFIX_.'module_carrier WHERE id_shop = '.(int)$this->context->shop->id;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);

        //Create new asociations from form
        $error = false;
        if ($result) {
            foreach ($carriers as $carrier) {
                if (isset($carrier['payments']) && is_array($carrier['payments']) && count($carrier['payments'])) {
                    foreach ($carrier['payments'] as $id_module) {
                        $values = array(
                            'id_reference'  => $carrier['id_reference'],
                            'id_module'     => $id_module,
                            'id_shop'       => (int)$this->context->shop->id,
                        );

                        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->insert('module_carrier', $values)) {
                            $error = true;
                        }
                    }
                }
            }
        }

        if (!$error) {
            return array(
                'message_code' => self::CODE_SUCCESS,
                'message'      => $this->l('The associations are updated correctly.')
            );
        } else {
            return array(
                'message_code' => self::CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.')
            );
        }
    }

    /**
     * Get data of carriers-payment asociation
     * @return type array
     */
    public function getAssociationsShipToPay()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('module_carrier');
        $sql->where('`id_shop` = '.(int)$this->context->shop->id);

        $carriers = Db::getInstance()->executeS($sql);

        return array('message_code' => self::CODE_SUCCESS, 'carriers' => $carriers);
    }

    /**
     * Sort fields.
     * @return type array
     */
    public function updateFieldsPosition()
    {
        if (!Tools::isSubmit('order_fields')) {
            return array('message_code' => self::CODE_ERROR, 'message' => $this->l('Error to update fields position'));
        }

        $order_fields = Tools::getValue('order_fields');
        $position     = 1;
        $errors_field = array();
        $message_code = self::CODE_SUCCESS;

        if (is_array($order_fields) && count($order_fields)) {
            foreach ($order_fields as $id_field) {
                if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
                    'opc_field',
                    array('position' => $position),
                    'id_field = '.$id_field
                )
                ) {
                    $field_class    = new FieldClass((int) $id_field);
                    $errors_field[] = $field_class->name;
                }
                $position++;
            }
        }

        $message = $this->l('Sort positions of fields has been updated successful');
        if (count($errors_field)) {
            $fields       = implode(', ', $errors_field);
            $message      = $this->l('Error to update position for field(s)').': '.$fields;
            $message_code = self::CODE_ERROR;
        }

        return array(
            'message_code' => $message_code,
            'message'      => $message,
        );
    }

    public function removeField()
    {
        $id_field = (int) Tools::getValue('id_field', null);
        if (empty($id_field) || (int) $id_field === 0) {
            return array('message_code' => self::CODE_ERROR, 'message' => $this->l('No field selected to remove.'));
        }

        $field_class = new FieldClass($id_field);
        if ((int) $field_class->is_custom === 0) {
            return array('message_code' => self::CODE_ERROR, 'message' => $this->l('Cannot remove this field.'));
        }

        if (!$field_class->delete()) {
            return array(
                'message_code' => self::CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to remove.')
            );
        }

        return array('message_code' => self::CODE_SUCCESS, 'message' => $this->l('Field remove successful.'));
    }

    /**
     * Save the field data.
     * @return type array
     */
    public function updateField()
    {
        if (!Tools::isSubmit('id_field')) {
            return array(
                'message_code' => self::CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.')
            );
        }

        $id_field = (int) Tools::getValue('id_field', null);
        if (empty($id_field) || (int) $id_field === 0) {
            $id_field = null;
        }

        $field_class = new FieldClass($id_field);

        if (is_null($id_field)) {
            $field_class->is_custom = true;
        }

        $array_description = array();
        $descriptions      = Tools::getValue('description');

        foreach ($descriptions as $description) {
            $array_description[$description['id_lang']] = $description['description'];
        }

        $field_class->description = $array_description;

        //only if field is custom can update data.
        if ($field_class->is_custom) {
            $field_class->name         = Tools::getValue('name');
            $field_class->object       = Tools::getValue('object');
            $field_class->type         = Tools::getValue('type');
            $field_class->size         = (int) Tools::getValue('size');
            $field_class->type_control = Tools::getValue('type_control');
            //shop
            $field_class->group        = $field_class->object;
            $field_class->row          = (int) FieldClass::getLastRowByGroup($field_class->group) + 1;
            $field_class->col          = 0;
        }

        $default_value = Tools::getValue('default_value');
//		if ($field_class->type == $this->globals->type->string)
//			$default_value = Tools::substr($default_value, 0, $field_class->size);

        $field_class->default_value = $default_value;
        $field_class->required      = (int) Tools::getValue('required');
        $field_class->active        = (int) Tools::getValue('active');

        if ($field_class->validateFieldsLang(false) && $field_class->save()) {
            $result = array(
                'message_code'  => self::CODE_SUCCESS,
                'message'       => $this->l('The field was successfully updated.'),
                'description'   => $array_description[$this->cookie->id_lang],
                'default_value' => $field_class->default_value,
            );

            if (is_null($id_field)) {
                $result['id_field'] = $field_class->id;
            }
        } else {
            $result = array(
                'message_code' => self::CODE_ERROR,
                'message'      => $this->l('An error occurred while trying to update.'),
            );
        }

        return $result;
    }

    public function removeImagePayment()
    {
        $errors    = array();
        
        $id_module = Tools::getValue('id_module');
        $name_payment = Tools::getValue('name_module');
        $id_payment = PaymentClass::getIdPaymentBy('id_module', $id_module);

        $paymentClass = new PaymentClass($id_payment);
        $paymentClass->name_image = 'no-image.png';
        $paymentClass->id_module = $id_module;
        $paymentClass->name = $name_payment;
        
        if (!$paymentClass->save()) {
            $errors[] = $this->l('There was an error while trying to delete the image.');
        }

        if (!empty($errors)) {
            return array('message_code' => self::CODE_ERROR, 'message' => implode(', ', $errors));
        } else {
            return array(
                'message_code' => self::CODE_SUCCESS,
                'message'      => $this->l('Image deleted successfully.'),
            );
        }
    }

    /**
     *
     * @param string $name
     * @return type
     */
    public function uploadImage()
    {
        $errors    = array();

        $id_module    = Tools::getValue('id_module');
        $force_display = Tools::getValue('force_display');
        $payment_data = Tools::getValue('payment_data');

        $id_payment = PaymentClass::getIdPaymentBy('id_module', $id_module);
        $payment    = new PaymentClass($id_payment);

        $payment->id_module = $id_module;
        $payment->force_display = $force_display;

        /* update payment image */
        if (count($_FILES)) {
            foreach ($_FILES as $payment_name => $file) {
                $payment_name = $payment_name;

                if (!isset($file['tmp_name']) || is_null($file['tmp_name']) || empty($file['tmp_name'])) {
                    $errors[] = $this->l('Cannot add file because it did not sent');
                }

                if (!ImageManager::isRealImage($file['tmp_name'], $file['type']) && $file['type'] != 'image/png' && $file['type'] != 'image/gif') {
                    $errors[] = $this->l('Image extension not allowed');
                }

                if (empty($errors)) {
                    $path = '';
                    $path_backup = '';
                    $extension = Tools::substr($file['type'], 6);

                    if (!empty($payment->name_image) && $payment->name_image != 'no-image.png') {
                        $path = dirname(__FILE__).'/views/img/payments/'.$payment->name_image;
                        $path_backup = $path.'.backup';

                        if (file_exists($path)) {
                            rename($path, $path_backup);
                        }
                    }

                    $payment->name_image = $payment->name.'.'.$extension;
                    $path = dirname(__FILE__).'/views/img/payments/'.$payment->name_image;

                    if (move_uploaded_file($file['tmp_name'], $path)) {
                        if (!empty($path_backup) && file_exists($path_backup)) {
                            unlink($path_backup);
                        }

                        $payment->save();
                    } else {
                        if (!empty($path_backup)) {
                            rename($path_backup, Tools::substr($path_backup, 0, Tools::strlen($path_backup) - 7));
                        }
                        $errors[] = $this->l('Cannot copy the file');
                    }
                }
            }
        }

        if (Tools::isSubmit('payment_data')) {
            //save description
            $payment_data = Tools::jsonDecode($payment_data);

            if (is_array($payment_data) && count($payment_data)) {
                $title       = array();
                $description = array();
                foreach ($payment_data as $data) {
                    $title[$data->id_lang]       = $data->title;
                    $description[$data->id_lang] = $data->description;
                }

                $payment->title       = $title;
                $payment->description = $description;

                if (!$payment->save()) {
                    $errors[] = $this->l('An error has ocurred while trying save');
                }
            }
        }

        if (!empty($errors)) {
            return array('message_code' => self::CODE_ERROR, 'message' => implode(', ', $errors));
        } else {
            return array(
                'message_code' => self::CODE_SUCCESS,
                'name_image' => count($_FILES) ? $payment->name_image : '',
                'message'      => $this->l('Payment configuration has been updated successfully.'),
            );
        }
    }

    /**
     * List of provider packs
     * @return type array
     */
    public function getRequiredFieldList()
    {
        //get content field list
        $content = FieldClass::getAllFields(null, null, null, null, null, array(), 'f.id_field');

        $actions = array(
            'edit'   => array(
                'action_class' => 'Fields',
                'class'        => 'has-action nohover',
                'icon'         => 'edit',
                'title'        => $this->l('Edit'),
                'tooltip'      => $this->l('Edit'),
            ),
            'remove' => array(
                'action_class' => 'Fields',
                'class'        => 'has-action nohover',
                'icon'         => 'times',
                'title'        => $this->l('Remove'),
                'tooltip'      => $this->l('Remove'),
                'condition'    => array(
                    'field'      => 'is_custom',
                    'comparator' => '1',
                ),
            ),
        );

        $headers  = array(
            'name'          => $this->l('Name'),
            'object'        => $this->l('Object'),
            'description'   => $this->l('Description'),
            'default_value' => $this->l('Default value'),
            'required'      => $this->l('Required'),
            'active'        => $this->l('Active'),
            'actions'       => $this->l('Actions'),
        );
        $truncate = array(
            'description' => 60,
        );

        //use array with action_class (optional for var) and action (action name) for custom actions.
        $status = array(
            'required' => array(
                'action_class' => 'Fields',
                'action'       => 'toggleRequired',
                'class'        => 'has-action',
            ),
            'active'   => array(
                'action_class' => 'Fields',
                'action'       => 'toggleActive',
                'class'        => 'has-action',
            ),
        );

        $color = array(
            'by'     => 'object',
            'colors' => array(
                'customer' => 'primary',
                'delivery' => 'success',
                'invoice'  => 'warning',
            ),
        );

        return array(
            'message_code' => self::CODE_SUCCESS,
            'content'      => $content,
            'table'        => 'table-required-fields',
            'color'        => $color,
            'headers'      => $headers,
            'actions'      => $actions,
            'truncate'     => $truncate,
            'status'       => $status,
            'prefix_row'   => 'field',
        );
    }

    public function hookDisplayAdminHomeQuickLinks()
    {
        $tk = Tools::getAdminTokenLite('AdminModules');
        echo '<li id="onepagecheckoutps_block">
            <a  style="background:#F8F8F8 url(\'../modules/'.$this->name.'/logo.png\') no-repeat 50% 20px"
				href="index.php?controller=adminmodules&configure='.$this->name.'&token='.$tk.'">
                <h4>'.$this->l($this->displayName).'</h4>
            </a>
        </li>';
    }

    public function hookDisplayHeader()
    {
        if (!$this->isModuleActive($this->name) || !$this->isVisible()) {
            return;
        }
        
        if ($this->context->controller->php_self == 'order') {
            if (!$this->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC']
                || (Tools::getIsset('checkout') || Tools::getIsset('rc'))
            ) {
                $this->context->smarty->assign('onepagecheckoutps', $this);

                $this->smarty->assign('paramsFront', array('CONFIGS' => $this->config_vars));
                $html = $this->display(__FILE__, 'views/templates/front/theme.tpl');

                //JS & CSS
                $this->context->controller->addJqueryUI('ui.datepicker');

                if ($this->config_vars['OPC_SHOW_LIST_CITIES_GEONAMES'] ||
                    $this->config_vars['OPC_AUTO_ADDRESS_GEONAMES']
                ) {
                    $this->context->controller->addJS($this->_path.'views/js/lib/bootstrap/plugins/typeahead/bootstrap-typeahead.min.js');
                    $this->context->controller->addJS($this->_path.'views/js/lib/jeoquery.js');
                }

                if ($this->config_vars['OPC_AUTOCOMPLETE_GOOGLE_ADDRESS']) {
                    if (!empty($this->config_vars['OPC_GOOGLE_API_KEY'])) {
                        $google_apy_source = 'https://maps.googleapis.com/maps/api/js?key=';
                        $google_apy_source .= trim($this->config_vars['OPC_GOOGLE_API_KEY']);
                        $google_apy_source .= '&sensor=false&libraries=places&language='.$this->context->language->iso_code;

                        $this->context->controller->registerJavascript(sha1($google_apy_source), $google_apy_source, array('server' => 'remote'));
                    }
                }

                $this->context->controller->addJS($this->_path.'views/js/lib/form-validator/jquery.form-validator.min.js');
                $this->context->controller->addJS($this->_path.'views/js/lib/jquery/plugins/visible/jquery.visible.min.js');
                $this->context->controller->addJS($this->_path.'views/js/lib/jquery/plugins/total-storage/jquery.total-storage.min.js');
                $this->context->controller->addJS($this->_path.'views/js/lib/pts/tools.js');
                $this->context->controller->addJS($this->_path.'views/js/front/onepagecheckoutps.js');
                $this->context->controller->addJS($this->_path.'views/js/front/override.js');

                $this->context->controller->addCSS($this->_path.'views/css/lib/font-awesome/font-awesome.css');
                $this->context->controller->addCSS($this->_path.'views/css/front/onepagecheckoutps.css');
                $this->context->controller->addCSS($this->_path.'views/css/front/onepagecheckoutps_17.css');
                $this->context->controller->addCSS($this->_path.'views/css/front/responsive.css');
                $this->context->controller->addCSS($this->_path.'views/css/front/override.css');

                return $html;
            } else {
                $this->context->controller->addCSS($this->_path.'views/css/front/onepagecheckoutps_17.css');
                $this->context->controller->addCSS($this->_path.'views/css/front/override.css');

                $this->context->controller->addJS($this->_path.'views/js/front/onepagecheckoutps.js');
                $this->context->controller->addJS($this->_path.'views/js/front/override.js');
            }
        } else {
            if ($this->context->controller->php_self == 'cart'
                && !Tools::getIsset('ajax')
                && !Tools::getIsset('token')
                && $this->context->cart->nbProducts() > 0
            ) {
                Tools::redirect('order');
            }
        }
    }

    public function hookActionShopDataDuplication($params)
    {
        $this->installLanguageShop($params['new_id_shop']);
    }

    public function hookDisplayAdminOrder($params)
    {
        $order = new Order($params['id_order']);

        $query = new DbQuery();
        $query->select('fc.value, fl.description field_description, fol.description option_description');
        $query->from('opc_field_cart', 'fc');
        $query->innerJoin('opc_field_lang', 'fl', 'fl.id_field = fc.id_field AND fl.id_lang = '.$this->cookie->id_lang);
        $query->leftJoin(
            'opc_field_option_lang',
            'fol',
            'fc.id_option = fol.id_field_option AND fol.id_lang = '.$this->cookie->id_lang
        );
        $query->where('fc.id_cart = '.$order->id_cart);

        $field_options = Db::getInstance()->executeS($query);

        if (!count($field_options)) {
            return;
        }

        $this->smarty->assign(array(
            'field_options' => $field_options,
        ));

        return $this->display(__FILE__, 'views/templates/hook/order.tpl');
    }

    public function hookActionCarrierUpdate($params)
    {
        $id_carrier_old = $params['id_carrier'];
        $id_carrier_new = $params['carrier']->id;

        Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
            'opc_ship_to_pay',
            array('id_carrier' => $id_carrier_new),
            'id_carrier = '.$id_carrier_old
        );
    }

    public function getMessageError($code_error)
    {
        $errors = array(
            0 => $this->l('I want to configure a custom password.'),
            1 => $this->l('Create an account and enjoy the benefits of a registered customer.'),
            2 => $this->l('Repeat password'),
            3 => $this->l('Confirm email'),
            4 => $this->l('Are you?')
        );

        if (key_exists($code_error, $errors)) {
            return $errors[$code_error];
        }

        return '';
    }

    /**
     * Return the content cms request.
     *
     * @return content html cms
     */
    public function loadCMS()
    {
        $html   = '';
        $id_cms = Tools::getValue('id_cms', '');

        $cms = new CMS($id_cms, $this->context->language->id);
        if (Validate::isLoadedObject($cms)) {
            $html = $cms->content;
        }

        return $html;
    }

    private function saveCustomFile($fields, FieldClass $field)
    {
        $value = '';
        foreach ($fields as $data_field) {
            if ($data_field->name == $field->name) {
                $value = $data_field->value;
                break;
            }
        }

        $values = array(
            'id_field'  => $field->id,
            'id_cart'   => $this->context->cart->id,
            'value'     => $value,
            'id_option' => FieldOptionClass::getIdOptionByIdFieldAndValue($field->id, $value),
        );
        
        Db::getInstance(_PS_USE_SQL_SLAVE_)->insert('opc_field_cart', $values, false, true, Db::REPLACE);
    }

    public function validateFields(
        $fields,
        &$customer,
        &$address_delivery,
        &$address_invoice,
        &$password,
        &$is_set_invoice
    ) {
        $fields_by_object = array();

        foreach ($fields as $field) {
            if ($field->name == 'id') {
                continue;
            }

            //Capitalize campos seleccionados.
            if (in_array($field->name, $this->fields_to_capitalize) && $this->config_vars['OPC_CAPITALIZE_FIELDS']) {
                $field->value = ucwords($field->value);
            }

            $field_db = FieldClass::getField(
                $this->context->language->id,
                $this->context->shop->id,
                $field->object,
                $field->name
            );

            if ($field_db) {
                $field_db->value                                = $field->value;
                $fields_by_object[$field->object][$field->name] = $field_db;

                //if custom, save options
                if ($field_db->is_custom) {
                    $this->saveCustomFile($fields, $field_db);
                }
            }
        }

        foreach ($fields_by_object as $name_object => $fields) {
            if ($name_object == $this->globals->object->customer) {
                if (empty($customer)) {
                    $customer = new Customer();
                }

                $this->addFieldsRequired($fields, $name_object, $customer);
                $this->validateFieldsCustomer($fields, $customer, $password);
            } elseif ($name_object == $this->globals->object->delivery) {
                if (empty($address_delivery)) {
                    $address_delivery = new Address();
                }

                $this->addFieldsRequired($fields, $name_object, $address_delivery);
                $this->validateFieldsAddress($fields, $address_delivery);
            } elseif ($name_object == $this->globals->object->invoice) {
                if (empty($address_invoice)) {
                    $address_invoice = new Address();
                }

                $this->addFieldsRequired($fields, $name_object, $address_invoice);
                $this->validateFieldsAddress($fields, $address_invoice);

                $is_set_invoice = true;
            }
        }
    }

    public function createCustomerAjax()
    {
        $results = array();

        $fields = Tools::jsonDecode(Tools::getValue('fields_opc'));

        $customer         = null;
        $address_delivery = null;
        $address_invoice  = null;
        $password         = null;
        $is_set_invoice   = null;

        $this->validateFields($fields, $customer, $address_delivery, $address_invoice, $password, $is_set_invoice);
        if (!count($this->errors)) {
            $this->createCustomer($customer, $address_delivery, $address_invoice, $password, $is_set_invoice);
            if (!count($this->errors)) {
                $results = array(
                    'isSaved'             => true,
                    'isGuest'             => $customer->is_guest,
                    'id_customer'         => (int) $customer->id,
                    'id_address_delivery' => !empty($address_delivery) ? $address_delivery->id : '',
                    'id_address_invoice'  => !empty($address_invoice) ? $address_invoice->id : '',
                );
            }
        }

        $results['hasError'] = !empty($this->errors);
        $results['errors']   = $this->errors;

        return $results;
    }

    public function createAddressAjax()
    {
        $object = Tools::getValue('object');

        $id_address = $this->createAddress($object);

        if ($object == 'delivery') {
            $this->context->cart->id_address_delivery = $id_address;
        }
        if ($object == 'invoice') {
            $this->context->cart->id_address_invoice = $id_address;
        }

        $this->context->cart->save();

        return $id_address;
    }

    /**
     * Create & login customer.
     *
     * @param object &$customer
     * @param object &$address_delivery
     * @param object &$address_invoice
     * @param string $password
     * @param boolean $is_set_invoice
     */
    public function createCustomer(&$customer, &$address_delivery, &$address_invoice, $password, $is_set_invoice)
    {
        Hook::exec('actionBeforeSubmitAccount');

        if (count($this->context->controller->errors)) {
            $this->errors = $this->context->controller->errors;
        }

        if (Customer::customerExists($customer->email)) {
            if (!Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                $this->errors[] = sprintf(
                    $this->l('The email %s is already in our database. If the information is correct, please login.'),
                    '<b>'.$customer->email.'</b>'
                );
            } else {
                $emailverificationopc = $this->isModuleActive('emailverificationopc');
                if ($emailverificationopc) {
                    $email_verified = $emailverificationopc->validateEmailVerifiedCustomer($customer, true);

                    if (!$email_verified) {
                        $this->warnings[] = $this->l(
                            'The customer was created properly but can not log in the store until you verify your
                            email address in the link sent to your email.'
                        );
                    }
                }

                $customer->is_guest = 1;
            }
        }

        if (!is_null($address_delivery)) {
            if ($this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL']
                || ($this->context->cart->nbProducts() > 0 && !$this->context->cart->isVirtualCart())
            ) {
                $country = new Country($address_delivery->id_country, Configuration::get('PS_LANG_DEFAULT'));
                if (!Validate::isLoadedObject($country)) {
                    $this->errors[] = $this->l('Country cannot be loaded.');
                } elseif ((int) $country->contains_states && !(int) $address_delivery->id_state) {
                    $this->errors[] = $this->l('This country requires you to chose a State.');
                }
            }
        }

        if (!is_null($address_invoice) && $is_set_invoice) {
            $country_invoice = new Country($address_invoice->id_country, Configuration::get('PS_LANG_DEFAULT'));
            if (!Validate::isLoadedObject($country_invoice)) {
                $this->errors[] = $this->l('Country cannot be loaded.');
            } elseif ($this->config_vars['OPC_ENABLE_INVOICE_ADDRESS']
                && $is_set_invoice
                && (int) $country_invoice->contains_states
                && !(int) $address_invoice->id_state
            ) {
                $this->errors[] = $this->l('This country requires you to chose a State.');
            }
        }

        if (!count($this->errors) && !count($this->warnings)) {
            //New Guest customer
            if (Tools::getIsset('is_new_customer') && Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                $customer->is_guest = Tools::getValue('is_new_customer');
            }

            if ($this->config_vars['OPC_CHOICE_GROUP_CUSTOMER'] && Tools::getIsset('group_customer')) {
                $customer->id_default_group = (int) Tools::getValue('group_customer');
            }

            if (!$customer->add()) {
                $this->errors[] = $this->l('An error occurred while creating your account.');
            } else {
                $customer->cleanGroups();

                if ($this->config_vars['OPC_CHOICE_GROUP_CUSTOMER'] && Tools::getIsset('group_customer')) {
                    $customer->addGroups(array((int) Tools::getValue('group_customer')));
                } else {
                    if (!$customer->is_guest) {
                        $customer->addGroups(array((int) $this->config_vars['OPC_DEFAULT_GROUP_CUSTOMER']));
                    } else {
                        $customer->addGroups(array((int) Configuration::get('PS_GUEST_GROUP')));
                    }
                }

                //Registro de grupos adicionales a clientes nuevos.
                $groups_customer_additional = $this->config_vars['OPC_GROUPS_CUSTOMER_ADDITIONAL'];
                if (!empty($groups_customer_additional)) {
                    $groups_customer_additional = explode(',', $groups_customer_additional);
                    if (is_array($groups_customer_additional)) {
                        $customer->addGroups($groups_customer_additional);
                    }
                }

                if (!is_null($address_delivery)) {
                    if (($this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'] || !$this->context->cart->isVirtualCart())) {
                        $address_delivery->id_customer = (int) $customer->id;
                        if ($is_set_invoice) {
                            $address_invoice->id_customer = (int) $customer->id;
                        }

                        if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA']) {
                            $address_delivery->firstname = $customer->firstname;
                            $address_delivery->lastname  = $customer->lastname;
                        }

                        if (!$address_delivery->save()) {
                            $this->errors[] = $this->l('An error occurred while creating your delivery address.');
                        }
                    }
                }

                if (!is_null($address_invoice) && $is_set_invoice) {
                    if (empty($address_invoice->id_customer)) {
                        $address_invoice->id_customer = $customer->id;
                    }

                    if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA']) {
                        $address_invoice->firstname = $customer->firstname;
                        $address_invoice->lastname  = $customer->lastname;
                    }
                    
                    if (!$address_invoice->save()) {
                        $this->errors[] = $this->l('An error occurred while creating your billing address.');
                    }

                    if (is_null($address_delivery)) {
                        $address_delivery = $address_invoice;
                    }
                }

                //if no is sent address delivery and invoice, will create new address.
                if (is_null($address_delivery) && is_null($address_invoice)) {
                    $id_address_new   = $this->createAddress($customer->id);
                    $address_delivery = new Address($id_address_new);
                }

                if (!count($this->errors)) {
                    if (!$customer->is_guest) {
                        $this->sendConfirmationMail($customer, $password);
                    }

                    $emailverificationopc = $this->isModuleActive('emailverificationopc');
                    if ($emailverificationopc) {
                        $email_verified = $emailverificationopc->validateEmailVerifiedCustomer($customer, true);

                        if (!$email_verified) {
                            $this->warnings[] = $this->l(
                                'The customer was created properly but can not log in the store until you verify your
                                email address in the link sent to your email.'
                            );

                            return false;
                        }
                    }

                    //loggin customer
                    $this->context->cookie->id_customer        = (int) $customer->id;
                    $this->context->cookie->customer_lastname  = $customer->lastname;
                    $this->context->cookie->customer_firstname = $customer->firstname;
                    $this->context->cookie->logged             = 1;
                    $customer->logged                          = 1;
                    $this->context->cookie->is_guest           = $customer->isGuest();
                    $this->context->cookie->passwd             = $customer->passwd;
                    $this->context->cookie->email              = $customer->email;

                    // Add customer to the context
                    $this->context->customer = $customer;

                    if (Configuration::get('PS_CART_FOLLOWING')
                        && (empty($this->context->cookie->id_cart)
                        || Cart::getNbProducts($this->context->cookie->id_cart) == 0)
                    ) {
                        $this->context->cookie->id_cart = (int) Cart::lastNoneOrderedCart($this->context->customer->id);
                    }

                    if (is_null($address_delivery) && is_null($address_invoice)) {
                        $address_delivery = new Address();
                    }

                    // Update cart address
                    $this->context->cart->id_customer         = (int) $customer->id;
                    $this->context->cart->secure_key          = $customer->secure_key;
                    $this->context->cart->id_address_delivery = $address_delivery->id;
                    $this->context->cart->id_address_invoice  = $is_set_invoice ?
                        $address_invoice->id : $address_delivery->id;
                    $this->context->cart->update();

                    $delivery_option = Tools::getValue('delivery_option');
                    if (!is_array($delivery_option)) {
                        $delivery_option = array($address_delivery->id => $this->context->cart->id_carrier.',');
                    }

                    $this->context->cart->setDeliveryOption($delivery_option);
                    $this->context->cart->save();
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    $this->context->cookie->write();
                    $this->context->cart->autosetProductAddress();

                    $array_post = array_merge((array) $customer, (array) $address_delivery);

                    foreach ($array_post as $key => $value) {
                        $_POST[$key] = $value;
                    }

                    $recargoequivalencia = $this->isModuleActive('recargoequivalencia');
                    if ($recargoequivalencia) {
                        if (array_key_exists('chkRecargoEquivalencia', $_POST)) {
                            $chkRecargoEquivalencia = Tools::getValue('chkRecargoEquivalencia');
                            if (empty($chkRecargoEquivalencia)) {
                                unset($_POST['chkRecargoEquivalencia']);
                            }
                        }
                    }

                    Hook::exec('actionCustomerAccountAdd', array(
                        '_POST'       => $_POST,
                        'newCustomer' => $customer,
                    ));
                }
            }
        }
    }

    /**
     * sendConfirmationMail
     * @param Customer $customer
     * @return bool
     */
    protected function sendConfirmationMail(Customer $customer, $password)
    {
        if (Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            Mail::Send(
                $this->context->language->id,
                'account',
                Mail::l('Welcome!'),
                array('{firstname}' => $customer->firstname,
                    '{lastname}'  => $customer->lastname,
                    '{email}'     => $customer->email,
                    '{passwd}'    => $password
                ),
                $customer->email,
                $customer->firstname.' '.$customer->lastname
            );
        }
    }

    /**
     * Check the email and password sent, then sing in customer
     *
     * @return array (boolean success, array errors)
     */
    public function loginCustomer()
    {
        $is_logged = false;

        Hook::exec('actionAuthenticationBefore');

        $customer = new Customer();
        $authentication = $customer->getByEmail(
            Tools::getValue('email'),
            Tools::getValue('password')
        );

        if (isset($authentication->active) && !$authentication->active) {
            $this->errors[] = $this->l('Your account isn\'t available at this time, please contact us');
        } elseif (!$authentication || !$customer->id || $customer->is_guest) {
            $this->errors[] = $this->l('The email or password is incorrect. Verify your information and try again.');
        } else {
            if (count($this->errors) == 0) {
                $is_logged = $this->singInCustomer($customer);
            }
        }

        $results = array(
            'success' => $is_logged,
            'errors'  => $this->errors,
        );

        return $results;
    }

    public function singInCustomer($customer)
    {
        $emailverificationopc = $this->isModuleActive('emailverificationopc');
        if ($emailverificationopc) {
            $email_verified = $emailverificationopc->validateEmailVerifiedCustomer($customer);

            if (!$email_verified) {
                $this->errors[] = sprintf(
                    $this->l('To sign in the store must verify your email address on the link sent to %s'),
                    $customer->email
                );

                return false;
            }
        }

        $this->context->updateCustomer($customer);

        Hook::exec('actionAuthentication', array('customer' => $customer));

        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();

        return true;
    }

    /**
     * Return the address of customer logged.
     *
     * @return array (id_address_delivery, id_address_invoice, addresses)
     */
    public function loadAddressesCustomer()
    {
        $result = array();

        if (Validate::isLoadedObject($this->context->customer) && !empty($this->context->customer->id)) {
            $addresses = $this->context->customer->getAddresses($this->context->language->id);

            $result = array(
                'id_address_delivery' => $this->context->cart->id_address_delivery,
                'id_address_invoice'  => $this->context->cart->id_address_invoice,
                'addresses'           => $addresses,
            );
        }

        return $result;
    }

    /**
     * Re-use the address already created without a real customer.
     *
     * @return integer Id address available
     */
    public function getIdAddressAvailable($object = 'delivery')
    {
        $query = new DbQuery();
        $query->select('id_address');
        $query->from('address');
        $query->where('id_customer = '.(int)$this->config_vars['OPC_ID_CUSTOMER']);
        $query->where('id_address NOT IN (SELECT id_address_delivery FROM '._DB_PREFIX_.'cart)');
        $query->where('deleted = 0');
        $query->where('active = 1');

        $id_address = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

        if (!empty($id_address)) {
            if ($this->context->customer->isLogged()) {
                $values = array('id_customer' => (int)$this->context->customer->id);
                $where = 'id_address = '.$id_address;

                Db::getInstance(_PS_USE_SQL_SLAVE_)->update('address', $values, $where);
            }
        } else {
            $id_address = $this->createAddress($object);
        }

        return $id_address;
    }

    /**
     * Verifica que la direccion que tiene el carrito no este ya usada en un pedido, en caso de estarlo
     * se procede a tomar una ya creada del cliente del OPC o crear una nueva.
     *
     * @return integer Id address available
     */
    public function checkAddressOrder()
    {
        $query = new DbQuery();
        $query->from('orders');
        $query->where('id_address_delivery = '.(int)$this->context->cart->id_address_delivery);
        $query->where('id_customer != '.(int)$this->context->cart->id_customer);

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if ($result) {
            $id_address_delivery = $this->getIdAddressAvailable();

            $this->context->cart->id_address_delivery = $id_address_delivery;
            $this->context->cart->id_address_invoice = $id_address_delivery;
            $this->context->cart->update();
        }
    }

    /**
     * Verifica que las direcciones que tiene el carrito existan y no fueran borradas.
     *
     */
    public function checkAddressExist(&$id_address_delivery, &$id_address_invoice)
    {
        $is_same_address = false;
        if ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice) {
            $is_same_address = true;
        }

        if (!empty($this->context->cart->id_address_delivery)) {
            $query = new DbQuery();
            $query->from('address');
            $query->where('id_address = '.(int)$this->context->cart->id_address_delivery);
            $query->where('active = 1');
            $query->where('deleted = 0');

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
            if (!$result) {
                $id_address = $this->getIdAddressAvailable();
                $this->context->cart->id_address_delivery = $id_address;
                $this->context->cart->update();
            } else {
                if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                    if ($result['id_customer'] != $this->config_vars['OPC_ID_CUSTOMER']) {
                        $id_address = $this->getIdAddressAvailable();
                        $this->context->cart->id_address_delivery = $id_address;
                        $this->context->cart->update();
                    }
                } else {
                    if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
                        //si la direccion que tiene el cliente asociada en el carrito
                        //hace parte del cliente del OPC, le cambiamos el customer para reutilizarla.
                        if ($result['id_customer'] == $this->config_vars['OPC_ID_CUSTOMER']) {
                            $address = new Address($this->context->cart->id_address_delivery);
                            $address->id_customer = $this->context->customer->id;
                            $address->update();
                        }
                    }
                }
            }
        }

        //si la direccion enviada por el checkout corresponde a otro cliente del logueado
        //o si la direccion enviada ya no existe.
        if (!empty($id_address_delivery)) {
            $address = new Address($id_address_delivery);

            if ((Validate::isLoadedObject($address) && $address->id_customer != $this->context->customer->id)
                || !Validate::isLoadedObject($address)
            ) {
                $id_address_delivery = null;
            }
        }

        if (!$is_same_address && !empty($this->context->cart->id_address_invoice)) {
            $query = new DbQuery();
            $query->from('address');
            $query->where('id_address = '.(int)$this->context->cart->id_address_invoice);
            $query->where('active = 1');
            $query->where('deleted = 0');

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
            if (!$result) {
                $id_address = $this->getIdAddressAvailable();
                $this->context->cart->id_address_invoice = $id_address;
                $this->context->cart->update();
            } else {
                if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                    if ($result['id_customer'] != $this->config_vars['OPC_ID_CUSTOMER']) {
                        $id_address = $this->getIdAddressAvailable();
                        $this->context->cart->id_address_invoice = $id_address;
                        $this->context->cart->update();
                    }
                } else {
                    if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
                        //si la direccion que tiene el cliente asociada en el carrito
                        //hace parte del cliente del OPC, le cambiamos el customer para reutilizarla.
                        if ($result['id_customer'] == $this->config_vars['OPC_ID_CUSTOMER']) {
                            $address = new Address($this->context->cart->id_address_invoice);
                            $address->id_customer = $this->context->customer->id;
                            $address->update();
                        }
                    }
                }
            }
        }

        //si la direccion enviada por el checkout corresponde a otro cliente del logueado
        //o si la direccion enviada ya no existe.
        if (!empty($id_address_invoice)) {
            $address = new Address($id_address_invoice);

            if ((Validate::isLoadedObject($address) && $address->id_customer != $this->context->customer->id)
                || !Validate::isLoadedObject($address)
            ) {
                $id_address_invoice = null;
            }
        }

        if (($this->context->customer->isLogged() || $this->context->customer->isGuest()) && !empty($this->context->cart->id_address_delivery)) {
            //elimina el problema que el listado de producto del carrito quede con un id de carrito del cliente OPC.
            $sql = 'UPDATE `'._DB_PREFIX_.'cart_product`
                SET `id_address_delivery` = '.(int)$this->context->cart->id_address_delivery.'
                WHERE `id_cart` = '.(int)$this->context->cart->id.'
                    AND `id_shop` = '.(int)$this->context->shop->id;
            Db::getInstance()->execute($sql);
        }
    }

    /**
     * Create address with default values.
     *
     * @param int $id_customer
     * @return int id address created.
     */
    public function createAddress($object = 'delivery')
    {
        $values = array(
            'firstname'  => FieldClass::getDefaultValue($object, 'firstname'),
            'lastname'   => FieldClass::getDefaultValue($object, 'lastname'),
            'address1'   => FieldClass::getDefaultValue($object, 'address1'),
            'city'       => FieldClass::getDefaultValue($object, 'city'),
            'postcode'   => FieldClass::getDefaultValue($object, 'postcode'),
            'id_country' => (int)FieldClass::getDefaultValue($object, 'id_country'),
            'id_state'   => (int)FieldClass::getDefaultValue($object, 'id_state'),
            'alias'      => FieldClass::getDefaultValue($object, 'alias'),
            'date_add'   => date('Y-m-d H:i:s'),
            'date_upd'   => date('Y-m-d H:i:s'),
        );

        if ($this->context->customer->isLogged()) {
            $addresses = $this->context->customer->getAddresses($this->context->language->id);
            $alias_count = count($addresses) + 1;

            $values['alias'] .= ' '.$alias_count;
        } else {
            $values['alias'] .= (version_compare(_PS_VERSION_, '1.6', '>=') ? ' #' : '').date('s');
        }

        $address            = new Address();
        $fields_db_required = $address->getFieldsRequiredDatabase();
        foreach ($fields_db_required as $field) {
            $values[$field['field_name']] = FieldClass::getDefaultValue($object, $field['field_name']);
        }

        if (empty($values['id_country'])) {
            $values['id_country'] = Configuration::get('PS_COUNTRY_DEFAULT');
        }
        
        $field_state = FieldClass::getField($this->context->cookie->id_lang, $this->context->shop->id, $object, 'id_state');
        if ($field_state->active == '0') {
            if (Country::containsStates((int) $values['id_country'])) {
                $states = State::getStatesByIdCountry((int) $values['id_country']);
                if (count($states)) {
                    $values['id_state'] = $states[0]['id_state'];
                }
            }
        }

        if (empty($values['postcode'])) {
            $country = new Country((int) $values['id_country']);
            if (Validate::isLoadedObject($country)) {
                $values['postcode'] = str_replace(
                    'C',
                    $country->iso_code,
                    str_replace(
                        'N',
                        '0',
                        str_replace(
                            'L',
                            'A',
                            $country->zip_code_format
                        )
                    )
                );
            }
        }

        if ($this->context->customer->isLogged()) {
            if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA'] && $object == 'delivery') {
                $values['firstname'] = $this->context->customer->firstname;
                $values['lastname']  = $this->context->customer->lastname;
            }

            if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA'] && $object == 'invoice') {
                $values['firstname'] = $this->context->customer->firstname;
                $values['lastname']  = $this->context->customer->lastname;
            }

            $values['id_customer'] = $this->context->customer->id;
        } else {
            $values['id_customer'] = $this->config_vars['OPC_ID_CUSTOMER'];
        }

        Db::getInstance(_PS_USE_SQL_SLAVE_)->insert('address', $values);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
    }

    /**
     * Support to module 'deliverydays' v1.7.1.0 from samdha.net
     *
     * The method setDate is called from hook header
     */
    public function supportModuleDeliveryDays()
    {
        $module = $this->isModuleActive('deliverydays', 'setDate');
        if ($module) {
            if (Tools::getIsset('deliverydays_day') || Tools::getIsset('deliverydays_timeframe')) {
                $module->setDate(
                    $this->context->cart,
                    Tools::getValue('deliverydays_day'),
                    Tools::getValue('deliverydays_timeframe')
                );
            }
        }
    }

    /**
     * Support to module of Vat
     */
    /*public function supportModuleCheckVat($customer)
    {
    }*/

    /**
     * Support to module CPFUser
     */
    /*public function supportModuleCPFUser(&$customer)
    {
    }*/

    /**
     * Support modules of shipping that use pick up.
     *
     * @param string $module
     * @param object &$carrier
     * @param boolean &$is_necessary_postcode
     * @param boolean &$is_necessary_city
     */
    /*private function supportModulesShipping($module, $address, &$carrier, &$is_necessary_postcode, &$is_necessary_city)
    {
        //remove message unused on validator prestashop.
        $address           = $address;
        $is_necessary_city = $is_necessary_city;

        switch ($module) {

        }

        return false;
    }*/

    /**
     * Check the DNI Spain if is valid.
     *
     * @param string $dni
     * @param int $id_country
     * @return boolean
     */
    public function checkDni($dni, $id_country)
    {
        if ($id_country == 6 && $this->config_vars['OPC_VALIDATE_DNI']) {
            require_once dirname(__FILE__).'/lib/nif-nie-cif.php';

            return isValidIdNumber($dni);
        } else {
            return Validate::isDniLite($dni) ? true : false;
        }
    }

    private function addFieldsRequired(&$fields, $name_object, $object)
    {
        $fields_tmp = array();

        $fields_db_required = $object->getFieldsRequiredDatabase();
        $fields_object      = ObjectModel::getDefinition($object);

        foreach ($fields_db_required as $field) {
            array_push($fields_tmp, $field['field_name']);
        }

        foreach ($fields_object['fields'] as $name_field => $field) {
            if (isset($field['required']) && $field['required'] == 1) {
                array_push($fields_tmp, $name_field);
            }
        }

        array_push($fields_tmp, 'id_country');
        array_push($fields_tmp, 'id_state');

        $fields_db = FieldClass::getAllFields(
            $this->context->cookie->id_lang,
            null,
            $name_object,
            null,
            null,
            $fields_tmp
        );

        foreach ($fields_db as $field) {
            if (!isset($fields[$field->name]) || (isset($fields[$field->name]) && empty($fields[$field->name]->value))) {
                if ($field->name == 'alias') {
                    $field->value = $field->default_value.' #'.date('s');
                } else {
                    $field->value = $field->default_value;
                }

                $fields[$field->name] = $field;
            }

            $fields[$field->name]->required = 1;
        }
    }

    private function validateFieldsCustomer(&$fields, &$customer, &$password)
    {
        foreach ($fields as $name => $field) {
            if ($field->type == 'url') {
                $field->type = 'isUrl';

                if (!empty($field->value) && Tools::substr($field->value, 0, 4) != 'http') {
                    $field->value = 'http://'.$field->value;
                }
            } elseif ($field->type == 'number') {
                $field->type = 'isInt';
            } elseif ($field->type == 'isDate' || $field->type == 'isBirthDate') {
                if (!empty($field->value)) {
                    $field->value = date('Y-m-d', strtotime(str_replace('/', '-', $field->value)));
                }
            }

            if ($name == 'passwd') {
                //if logged the password does not matter
                if ($this->context->customer->isLogged()/* || $this->context->customer->isGuest()*/) {
                    //unset($fields[$name]);
                    continue;
                } else {
                    $password = $field->value;
                    if (!$this->config_vars['OPC_REQUEST_PASSWORD']
                        || ($this->config_vars['OPC_REQUEST_PASSWORD']
                            && $this->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD']
                            && empty($field->value))
                        || (Configuration::get('PS_GUEST_CHECKOUT_ENABLED')
                        && Tools::getValue('is_new_customer') == 1)
                    ) {
                        $password = Tools::passwdGen();
                    }

                    $field->value = Tools::encrypt($password);
                }
            } elseif ($name == 'email') {
                if (empty($field->value)) {
                    $field->value = date('His').'@auto-generated.opc';
                }

                if (!$this->context->customer->isLogged()
                    && Customer::customerExists($field->value)
                    && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')
                    && Tools::getValue('is_new_customer') == 1
                ) {
                    $this->errors[] = $this->l('An account using this email address has already been registered.');
                }
            }

            $valid = call_user_func(array('Validate', $field->type), $field->value);

            //check field required
            if ($field->required == 1 && empty($field->value)) {
                $this->errors[] = sprintf(
                    $this->l('The field %s is required.'),
                    ObjectModel::displayFieldName(
                        $name,
                        get_class($customer),
                        true
                    )
                );
            } elseif (!empty($field->value) && !$valid) {
                $this->errors[] = sprintf(
                    $this->l('The field %s is invalid.'),
                    ObjectModel::displayFieldName(
                        $name,
                        get_class($customer),
                        true
                    )
                );
            }

            if ($field->active == 0 && !empty($customer->{$name})) {
                continue;
            }

            $customer->{$name} = $field->value;
        }

        //$this->supportModuleCPFUser($customer);
    }

    private function validateFieldsAddress(&$fields, &$address)
    {
        foreach ($fields as $name => $field) {
            if ($field->type == 'url') {
                $field->type = 'isUrl';

                if (Tools::substr($field->value, 0, 4) != 'http') {
                    $field->value = 'http://'.$field->value;
                }
            } elseif ($field->type == 'number') {
                $field->type = 'isInt';
            } elseif ($field->type == 'isDate' || $field->type == 'isBirthDate') {
                $field->value = date('Y-m-d', strtotime(str_replace('/', '-', $field->value)));
            }

            $valid = call_user_func(array('Validate', $field->type), $field->value);

            //check field required
            if ($field->required == 1 && empty($field->value)) {
                if ($field->name != 'id_state') {
                    $this->errors[] = sprintf(
                        $this->l('The field %s is required.'),
                        ObjectModel::displayFieldName(
                            $name,
                            get_class($address),
                            true
                        )
                    );
                }
            } elseif (!empty($field->value) && !$valid) {
                //check field validated
                $this->errors[] = sprintf(
                    $this->l('The field %s is invalid.'),
                    ObjectModel::displayFieldName(
                        $name,
                        get_class($address),
                        true
                    )
                );
            }

            if ($field->active == 0 && !empty($address->{$name})) {
                continue;
            }

            $address->{$name} = $field->value;
        }

        if (!count($this->errors)) {
            if ($address->id_country) {
                // Check country
                if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                    $this->errors[] = $this->l('Country cannot be loaded.');
                }

                if ((int) $country->contains_states) {
                    if (!(int) $address->id_state) {
                        $this->errors[] = $this->l('This country requires you to chose a State.');
                    } else {
                        $state = new State((int)$address->id_state);
                        if (Validate::isLoadedObject($state) && $state->id_country != $country->id) {
                            $this->errors[] = $this->l('The selected state does not correspond to the country.');
                        }
                    }
                } else {
                    $address->id_state = null;
                }

                if (!$country->active) {
                    $this->errors[] = $this->l('This country is not active.');
                }

                // Check zip code format
                if ($country->zip_code_format && !$country->checkZipCode($address->postcode)) {
                    //this fix the problem if the field postcode is disabled.
                    if (!empty($address->postcode)) {
                        $this->errors[] = sprintf(
                            $this->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'),
                            str_replace(
                                'C',
                                $country->iso_code,
                                str_replace(
                                    'N',
                                    '0',
                                    str_replace(
                                        'L',
                                        'A',
                                        $country->zip_code_format
                                    )
                                )
                            )
                        );
                    } else {
                        $address->postcode = str_replace(
                            'C',
                            $country->iso_code,
                            str_replace(
                                'N',
                                '0',
                                str_replace(
                                    'L',
                                    'A',
                                    $country->zip_code_format
                                )
                            )
                        );
                    }
                } elseif (empty($address->postcode) && $country->need_zip_code) {
                    $address->postcode = str_replace(
                        'C',
                        $country->iso_code,
                        str_replace(
                            'N',
                            '0',
                            str_replace(
                                'L',
                                'A',
                                $country->zip_code_format
                            )
                        )
                    );
                }
                //$this->errors[] = $this->l('The Zip/Postal code is required.');
                // Check country DNI
                if (!empty($address->dni)) {
                    if ($country->isNeedDni()
                        && (!$address->dni)
                        || !$this->checkDni($address->dni, $address->id_country)
                    ) {
                        $this->errors[] = $this->l('The field identification number is invalid.');
                    }
//					else
//					{
//						$query = new DbQuery();
//						$query->from('address');
//						$query->where(
//							'dni = "'.$address->dni.'"'.
//								($this->context->customer->isLogged() ? ' AND id_customer != '.$this->context->customer->id : '')
//						);
//						if (Db::getInstance()->executeS($query))
//							$this->errors[] = $this->l('The identification number has already been used.');
//					}
                } elseif (!$country->isNeedDni()) {
                    $address->dni = null;
                }
            }

            if (!Validate::isDate($address->date_add)) {
                $address->date_add = date('Y-m-d H:i:s');
            }
            if (!Validate::isDate($address->date_upd)) {
                $address->date_upd = $address->date_add;
            }
        }
    }

    public function isSameAddress($delivery_address, $invoice_address)
    {
        $is_same = true;

        if ($delivery_address->id_country != $invoice_address->id_country) {
            $is_same = false;
        }
        if ($delivery_address->id_state != $invoice_address->id_state) {
            $is_same = false;
        }
        if ($delivery_address->alias != $invoice_address->alias) {
            $is_same = false;
        }
        if ($delivery_address->company != $invoice_address->company) {
            $is_same = false;
        }
        if ($delivery_address->lastname != $invoice_address->lastname) {
            $is_same = false;
        }
        if ($delivery_address->firstname != $invoice_address->firstname) {
            $is_same = false;
        }
        if ($delivery_address->address1 != $invoice_address->address1) {
            $is_same = false;
        }
        if ($delivery_address->address2 != $invoice_address->address2) {
            $is_same = false;
        }
        if ($delivery_address->postcode != $invoice_address->postcode) {
            $is_same = false;
        }
        if ($delivery_address->city != $invoice_address->city) {
            $is_same = false;
        }
        if ($delivery_address->other != $invoice_address->other) {
            $is_same = false;
        }
        if ($delivery_address->phone != $invoice_address->phone) {
            $is_same = false;
        }
        if ($delivery_address->phone_mobile != $invoice_address->phone_mobile) {
            $is_same = false;
        }
        if ($delivery_address->dni != $invoice_address->dni) {
            $is_same = false;
        }

        return $is_same;
    }

    /**
     * Load address customer
     *
     * @return array(hasError, errors, address_delivery, address_invoice, customer)
     */
    public function loadAddress()
    {
        $id_address_delivery = (int) Tools::getValue('delivery_id');
        $id_address_invoice  = (int) Tools::getValue('invoice_id');
        $is_set_invoice      = Tools::getValue('is_set_invoice');

        //get addresses last order
        if (!isset($this->context->cookie->opc_suggest_address)) {
            if ($this->context->customer->isLogged()) {
                $query = 'SELECT o.id_address_delivery, o.id_address_invoice FROM `'._DB_PREFIX_.'orders` AS o';
                $query .= ' INNER JOIN `'._DB_PREFIX_.'address` AS ad ON (ad.id_address = o.id_address_delivery OR ';
                $query .= ' ad.id_address = o.id_address_invoice)';
                $query .= ' WHERE o.id_customer = '.(int)$this->context->customer->id.' AND ad.deleted = 0';
                $query .= ' ORDER BY o.id_order DESC LIMIT 1';

                $result = Db::getInstance()->executeS($query);

                if ($result) {
                    $id_address_delivery_tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_address` FROM '._DB_PREFIX_.'address a WHERE a.deleted = 0 AND a.active = 1 AND a.`id_address` = '.(int)$result[0]['id_address_delivery']);
                    if ($id_address_delivery_tmp) {
                        $id_address_delivery = $id_address_delivery_tmp;
                        $this->context->cart->id_address_delivery = $id_address_delivery;
                    }

                    if ($is_set_invoice || $this->config_vars['OPC_REQUIRED_INVOICE_ADDRESS']) {
                        $id_address_invoice_tmp = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_address` FROM '._DB_PREFIX_.'address a WHERE a.deleted = 0 AND a.active = 1 AND a.`id_address` = '.(int)$result[0]['id_address_invoice']);
                        if ($id_address_invoice_tmp) {
                            $id_address_invoice = $id_address_invoice_tmp;
                            $this->context->cart->id_address_invoice = $id_address_invoice;
                        }
                    }

                    if (!$this->context->cart->update()) {
                        $this->errors[] = $this->l('An error occurred while updating your cart.');
                    }

                    $this->context->cookie->opc_suggest_address = true;
                }
            }
        }

        $this->checkAddressExist($id_address_delivery, $id_address_invoice);

        if (empty($id_address_delivery)
            && empty($id_address_invoice)
            && empty($this->context->cart->id_address_delivery)
            && empty($this->context->cart->id_address_invoice)
            && $this->context->customer->isLogged()
        ) {
            $query = 'SELECT id_address FROM '._DB_PREFIX_.'address WHERE id_customer = '.$this->context->customer->id;
            $query .= ' AND active = 1 AND deleted = 0';
            $id_address = Db::getInstance()->getValue($query);

            if (!empty($id_address)) {
                $id_address_delivery = $id_address;
                $id_address_invoice = $id_address;
            }
        }

        if (empty($id_address_delivery)) {
            $id_address_delivery = $this->context->cart->id_address_delivery;
        }
        if (empty($id_address_invoice)) {
            $id_address_invoice = $id_address_delivery;
        }

        if (empty($id_address_invoice) && empty($id_address_delivery) && $this->context->customer->isLogged()) {
            $id_address_delivery = (int) $this->createAddress($this->context->customer->id);
        }

        $address_delivery = new Address((int) $id_address_delivery);
        $address_invoice  = new Address((int) $id_address_invoice);
        $customer         = $this->context->customer;

        if ($address_invoice->id_customer != $customer->id) {
            $address_invoice  = new Address();
        }
        if ($address_delivery->id_customer != $customer->id) {
            $address_delivery  = new Address();
        }

        if (Validate::isLoadedObject($address_delivery) && Validate::isLoadedObject($customer)) {
            //valida si la fecha es validad y no venga con ceros.
            if (!Validate::isDate($address_delivery->date_add)) {
                $address_delivery->date_add = date('Y-m-d H:i:s');
            }
            if (!Validate::isDate($address_delivery->date_upd)) {
                $address_delivery->date_upd = $address_delivery->date_add;
            }
            
            if ($address_delivery->id_customer != $customer->id) {
                $this->errors[] = $this->l('This address is not yours.');
            } elseif (!Validate::isLoadedObject($address_delivery) || $address_delivery->deleted) {
                $this->errors[] = $this->l('This address is invalid. Sign out of session and login again.');
            } else {
                $this->context->cart->id_address_delivery = $id_address_delivery;

                if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA']) {
                    $address_delivery->firstname = $customer->firstname;
                    $address_delivery->lastname  = $customer->lastname;
                    $address_delivery->update();
                }
                
                if (!$this->context->cart->update()) {
                    $this->errors[] = $this->l('An error occurred while updating your cart.');
                }
            }
        }

        if (Validate::isLoadedObject($address_invoice) && Validate::isLoadedObject($customer)) {
            //valida si la fecha es validad y no venga con ceros.
            if (!Validate::isDate($address_invoice->date_add)) {
                $address_invoice->date_add = date('Y-m-d H:i:s');
            }
            if (!Validate::isDate($address_invoice->date_upd)) {
                $address_invoice->date_upd = $address_invoice->date_add;
            }
            
            if ($address_invoice->id_customer != $customer->id) {
                $this->errors[] = $this->l('This address is not yours.');
            } elseif (!Validate::isLoadedObject($address_invoice) || $address_invoice->deleted) {
                $this->errors[] = $this->l('This address is invalid. Sign out of session and login again.');
            } else {
                $this->context->cart->id_address_invoice = $id_address_invoice;

                if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA']) {
                    $address_invoice->firstname = $customer->firstname;
                    $address_invoice->lastname  = $customer->lastname;
                    $address_invoice->update();
                }
                
                if (!$this->context->cart->update()) {
                    $this->errors[] = $this->l('An error occurred while updating your cart.');
                }
            }
        }

        $result = array(
            'hasError'         => (boolean) count($this->errors),
            'errors'           => $this->errors,
            'address_delivery' => $address_delivery,
            'address_invoice'  => $address_invoice,
            'customer'         => $customer,
        );

        return $result;
    }

    /**
     * Load options shipping.
     *
     * @return array
     */
    public function loadCarrier($order_controller)
    {
        $set_id_customer_opc = false;

        $id_country          = Tools::getValue('id_country');
        $id_state            = Tools::getValue('id_state');
        $postcode            = Tools::getValue('postcode');
        $city                = Tools::getValue('city');
        $id_address_delivery = (int)Tools::getValue('id_address_delivery');
        $id_address_invoice  = (int)Tools::getValue('id_address_invoice');

        if (empty($id_country)) {
            $id_country = (int) FieldClass::getDefaultValue('delivery', 'id_country');
        }

        $is_same_address = false;
        if ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice) {
            $is_same_address = true;
        }

        $this->checkAddressExist($id_address_delivery, $id_address_invoice);

        if (empty($id_address_delivery)) {
            $id_address_delivery = $this->context->cart->id_address_delivery;

            if (empty($id_address_delivery) && !$this->context->customer->isLogged()) {
                $id_address_delivery = $this->getIdAddressAvailable('delivery');

                $this->context->cart->id_address_delivery = $id_address_delivery;
                $this->context->cart->save();
            }
        }
        if (empty($id_address_invoice)) {
            $id_address_invoice = $this->context->cart->id_address_invoice;

            if (empty($id_address_invoice) && !$this->context->customer->isLogged() && !$is_same_address) {
                $id_address_invoice = $this->getIdAddressAvailable('invoice');
            } else {
                $id_address_invoice = $this->context->cart->id_address_delivery;
            }

            $this->context->cart->id_address_invoice = $id_address_invoice;
            $this->context->cart->save();
        }

        $this->checkAddressOrder();

        if (!$this->context->cart->isVirtualCart()) {
            if (!empty($id_country)) {
                $delivery_address = new Address($id_address_delivery);
                $delivery_address->deleted = 0;

                //se hace esta modificacion para poder mostrar transportes sin necesidad de enviar una provincia
                //entonces tomamos la por defecto del checkout o la de la direccion cargada.
                if (empty($id_state)) {
                    if (empty($delivery_address->id_state)) {
                        $id_state = (int) FieldClass::getDefaultValue('delivery', 'id_state');
                    } else {
                        $id_state = $delivery_address->id_state;
                    }
                }

                $country = new Country($id_country);
                if ($country->contains_states && empty($id_state)) {
                    $delivery_address->id_state = null;
                    $delivery_address->save();

                    $this->errors[] = $this->l('Select a state to show the different shipping options.');
                } else {
                    //evaluamos que el pais no contenga estados y que si viene un estado ya sea enviado o puesto por defecto
                    //lo quitamos para evitar problema en el calculo del coste de envio.
                    if (!$country->contains_states && !empty($id_state)) {
                        $id_state = null;
                    }

                    //update country and state sent.
                    $delivery_address->id_country = $id_country;
                    $delivery_address->id_state   = $id_state;

                    if (empty($delivery_address->firstname)) {
                        $delivery_address->firstname = FieldClass::getDefaultValue('delivery', 'firstname');
                    }
                    if (empty($delivery_address->lastname)) {
                        $delivery_address->lastname = FieldClass::getDefaultValue('delivery', 'lastname');
                    }

                    if (Tools::getIsset('postcode')) {
                        if (empty($postcode)) {
                            if (empty($this->context->customer->id) && empty($postcode)) {
                                $delivery_address->postcode = $postcode;
                            }
                        } else {
                            $delivery_address->postcode = $postcode;
                        }
                    }

                    if (!empty($city)) {
                        if (in_array('city', $this->fields_to_capitalize) && $this->config_vars['OPC_CAPITALIZE_FIELDS']) {
                            $city = ucwords($city);
                        }
                        $delivery_address->city = $city;
                    }

                    $fields = array();

                    if (!$this->checkDni($delivery_address->dni, $delivery_address->id_country)) {
                        $delivery_address->dni = '';
                    }

                    $this->validateFieldsAddress($fields, $delivery_address);

                    if (!count($this->errors)) {
                        //si la direccion enviada es cambiada y si esa direccion ya existe en otro pedido
                        //entonces se crea una nueva para no alterar la direccion en los pedidos ya existentes
                        /*if ($delivery_address->isUsed()) {
                            $address_delivery_ori = new Address($delivery_address->id);

                            if (!$this->isSameAddress($delivery_address, $address_delivery_ori)) {
                                $delivery_address->id = null;
                                $delivery_address->alias .= ' 2';
                            }
                        }*/

                        if (!$delivery_address->save()) {
                            $this->errors[] = $this->l('An error occurred while updating your delivery address.');
                        }

                        if (Validate::isLoadedObject($delivery_address)) {
                            //assign opc customer to cookie, customer and cart to calculare fine the prices of carriers
                            if (empty($this->context->cookie->id_customer)) {
                                $module_exception = false;

                                if (!$module_exception) {
                                    $this->context->cookie->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];

                                    if (empty($this->context->customer->id)) {
                                        $this->context->customer = new Customer($this->config_vars['OPC_ID_CUSTOMER']);
                                        $this->context->customer->logged = 1;
                                    }

                                    if (empty($this->context->cart->id_customer)) {
                                        $this->context->cart->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];
                                    }

                                    $set_id_customer_opc = true;
                                }
                            }

                            //update address delivery to cart
                            $this->context->cart->id_address_delivery = $delivery_address->id;
                            if (empty($this->context->cart->id_address_invoice)) {
                                $this->context->cart->id_address_invoice  = $delivery_address->id;
                            }
                            $this->context->cart->update();

                            // Address has changed, so we check if the cart rules still apply
                            CartRule::autoRemoveFromCart();
                            CartRule::autoAddToCart();

                            //zone country is changed. some code use to calculate prices of carriers.
                            $this->context->country->id_zone = Address::getZoneById((int) $delivery_address->id);

                            if (!Address::isCountryActiveById((int) $delivery_address->id)) {
                                $this->errors[] = $this->l('This address is not in a valid area.');
                            }
                        } else {
                            $this->l('This address is invalid. Sign out of session and login again.');
                        }
                    }
                    
                    if (!count($this->errors)) {
                        //$address = new Address($order_controller->getCheckoutSession()->getIdAddressDelivery());
                        $delivery_option = $order_controller->getCheckoutSession()->getSelectedDeliveryOption();
                        $delivery_options = $order_controller->getCheckoutSession()->getDeliveryOptions();

                        if (!count($delivery_options)) {
                            $this->errors[] = $this->l('There are no shipping methods available for your address.');
                        }

                        $is_necessary_postcode = false;
                        $is_necessary_city     = false;

                        $delivery_options_tmp = array();
                        foreach ($delivery_options as $id_carrier => $carrier) {
                            //support module of shipping for pick up.
                            if (!empty($carrier['external_module_name'])) {
                                /*$this->supportModulesShipping(
                                    $carrier['external_module_name'],
                                    $address,
                                    $carrier,
                                    $is_necessary_postcode,
                                    $is_necessary_city
                                );*/
                            }

                            $delivery_options_tmp[$id_carrier] = $carrier;
                        }

                        $delivery_options = $delivery_options_tmp;

                        if (!$is_necessary_postcode) {
                            if ($this->config_vars['OPC_FORCE_NEED_POSTCODE']) {
                                $is_necessary_postcode = true;
                            } else {
                                $carriers_postcode = explode(
                                    ',',
                                    $this->config_vars['OPC_MODULE_CARRIER_NEED_POSTCODE']
                                );
                                foreach ($carriers_postcode as $carrier) {
                                    if ($this->isModuleActive($carrier)) {
                                        $is_necessary_postcode = true;
                                    }
                                }
                            }
                        }

                        if (!$is_necessary_city) {
                            if ($this->config_vars['OPC_FORCE_NEED_CITY']) {
                                $is_necessary_city = true;
                            } else {
                                $carriers_city = explode(',', $this->config_vars['OPC_MODULE_CARRIER_NEED_CITY']);

                                foreach ($carriers_city as $carrier) {
                                    if ($this->isModuleActive($carrier)) {
                                        $is_necessary_city = true;
                                    }
                                }
                            }
                        }

                        if (empty($city) && $is_necessary_city) {
                            $this->errors = $this->l('You need to place a city to show shipping options.');
                        }
                        
                        if (empty($postcode) && $is_necessary_postcode) {
                            $this->errors = $this->l('You need to place a post code to show shipping options.');
                        }

                        $this->context->smarty->assign(array(
                            'id_address' => $order_controller->getCheckoutSession()->getIdAddressDelivery(),
                            'delivery_options' => $delivery_options,
                            'delivery_option' => $delivery_option,
                            'is_necessary_postcode' => $is_necessary_postcode,
                            'is_necessary_city' => $is_necessary_city,
                        ));
                    }
                }
            } else {
                $this->errors[] = $this->l('Select a country to show the different shipping options.');
            }
        }

        $templateVars = array(
            'ONEPAGECHECKOUTPS_IMG' => $this->onepagecheckoutps_dir.'views/img/',
            'CONFIGS' => $this->config_vars,
            'is_virtual_cart' => (int)$order_controller->getCheckoutSession()->getCart()->isVirtualCart(),
            'hasError' => !empty($this->errors),
            'errors' => $this->errors,
            //native vars
            'hookDisplayBeforeCarrier' => Hook::exec('displayBeforeCarrier', array('cart' => $order_controller->getCheckoutSession()->getCart())),
            'hookDisplayAfterCarrier' => Hook::exec('displayAfterCarrier', array('cart' => $order_controller->getCheckoutSession()->getCart())),
            'recyclable' => $order_controller->getCheckoutSession()->isRecyclable(),
            'recyclablePackAllowed' => $order_controller->checkoutDeliveryStep->isRecyclablePackAllowed(),
            'gift' => array(
                'allowed' => $order_controller->checkoutDeliveryStep->isGiftAllowed(),
                'isGift' => $order_controller->getCheckoutSession()->getGift()['isGift'],
                'label' => $this->l('I would like my order to be gift wrapped').$order_controller->checkoutDeliveryStep->getGiftCostForLabel(),
                'message' => $order_controller->getCheckoutSession()->getGift()['message'],
            ),
        );
        
        $this->context->smarty->assign($templateVars);

        if ($set_id_customer_opc) {
            $this->context->customer         = new Customer();
            $this->context->customer->logged = 0;
            unset($this->context->cookie->id_customer);

            $this->context->cart->id_customer = null;
            $this->context->cart->update();
        }

        $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/carrier.tpl');

        return $html;
    }

    /**
     * Load payment methods.
     *
     * @return html
     */
    public function loadPayment()
    {
        $payment_need_register = false;

        $paymentOptionsFinder = new PaymentOptionsFinder();

        $payment_options = $paymentOptionsFinder->present();

        if ($payment_options) {
            foreach ($payment_options as $name_module => &$module_options) {
                foreach ($module_options as &$option) {
                    $path_image = _PS_MODULE_DIR_.$this->name.'/views/img/payments/'.$name_module;

                    $module_payment = Module::getInstanceByName($name_module);

                    $option['id_module_payment'] = $module_payment->id;

                    if (empty($option['logo'])) {
                        if (file_exists($path_image.'.png')) {
                            $option['logo'] = $this->onepagecheckoutps_dir.'views/img/payments/'.$name_module.'.png';
                        } elseif (file_exists($path_image.'.gif')) {
                            $option['logo'] = $this->onepagecheckoutps_dir.'views/img/payments/'.$name_module.'.gif';
                        } elseif (file_exists($path_image.'.jpeg')) {
                            $option['logo'] = $this->onepagecheckoutps_dir.'views/img/payments/'.$name_module.'.jpeg';
                        }
                    }

                    $id_payment = PaymentClass::getIdPaymentBy('id_module', (int) $option['id_module_payment']);

                    $payment = new PaymentClass($id_payment, $this->context->language->id);
                    if (Validate::isLoadedObject($payment)) {
                        if ($payment->name_image == 'no-image.png') {
                            $option['logo'] = '';
                        } else if (!empty($payment->name_image)) {
                            $option['logo'] = $this->onepagecheckoutps_dir.'views/img/payments/'.$payment->name_image;
                        }

                        if ($payment->title) {
                            $option['title_opc'] = $payment->title;
                        }
                        if ($payment->description) {
                            $option['description_opc'] = $payment->description;
                        }

                        $option['force_display'] = $payment->force_display;
                        if ($payment->force_display) {
                            $option['action'] = $this->context->link->getModuleLink(
                                $this->name,
                                'payment',
                                array('pm' => $name_module)
                            );
                        }
                    }
                }
            }
        }

        $templateVars = array(
            'payment_options' => $payment_options,
            'selected_payment_option' => false,
            'CONFIGS' => $this->config_vars,
            'payment_need_register' => $payment_need_register
        );

        $this->context->smarty->assign($templateVars);

        $html = $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/payment.tpl');

        return $html;
    }

    /**
     * Update invoice address.
     *
     * @return array
     */
    public function removeAddressInvoice()
    {
        $this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery;
        $this->context->cart->update();
    }

    /**
     * Update invoice address.
     *
     * @return array
     */
    public function updateAddressInvoice()
    {
        $id_country          = (int) Tools::getValue('id_country');
        $id_state            = (int) Tools::getValue('id_state');
        $postcode            = Tools::getValue('postcode', '');
        $city                = Tools::getValue('city', '');
        $vat_number          = Tools::getValue('vat_number', '');
        $id_address_invoice  = Tools::getValue('id_address_invoice', '');

        if (empty($id_address_invoice)) {
            if (empty($this->context->cart->id_address_invoice) && !$this->context->customer->isLogged()) {
                $id_address_invoice = $this->getIdAddressAvailable('invoice');
            } else {
                $id_address_invoice = $this->context->cart->id_address_invoice;
            }
        }

        if (!empty($id_address_invoice)) {
            if (empty($id_country)) {
                $id_country = (int) FieldClass::getDefaultValue('invoice', 'id_country');
            }
            if (empty($id_state)) {
                $id_state = (int) FieldClass::getDefaultValue('invoice', 'id_state');
            }

            if (empty($city)) {
                $city_tmp = FieldClass::getDefaultValue('invoice', 'city');
                if ($city != '.' && !empty($city)) {
                    $city = $city_tmp;
                }
            }

            $invoice_address = new Address($id_address_invoice);

            //update country and state sent.
            $invoice_address->id_country = $id_country;
            $invoice_address->id_state   = $id_state;
            $invoice_address->vat_number = $vat_number;

            if (!empty($postcode)) {
                $invoice_address->postcode = $postcode;
            } else {
                $invoice_address->postcode = '';
            }

            if (!empty($city)) {
                $invoice_address->city = $city;
            }

            $invoice_address->update();

            $this->context->cart->id_address_invoice = $id_address_invoice;
            $this->context->cart->update();

            if (!$this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'] && $this->context->cart->isVirtualCart()) {
                $this->context->cart->id_address_delivery = $this->context->cart->id_address_invoice;
                $this->context->cart->update();
            }
        }
    }

    /**
     * Load summary of cart.
     *
     * @return html
     */
    public function loadReview()
    {
        $set_id_customer_opc = false;
        if (!$this->context->cookie->id_customer) {
            $this->context->cookie->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];

            if (!$this->context->customer->id) {
                $this->context->customer->id = $this->config_vars['OPC_ID_CUSTOMER'];
            }

            if (!$this->context->cart->id_customer) {
                $this->context->cart->id_customer = $this->config_vars['OPC_ID_CUSTOMER'];
            }

            $set_id_customer_opc = true;

            $this->context->cart->update();
        }

        if (Tools::getIsset('id_country') && Tools::getIsset('id_state')) {
            $id_state = (int) Tools::getValue('id_state');

            //forzamos la zona del pais a que sea la del estado, para calcular bien los precios.
            //esto es un engano al metodo getCarriersForOrder() que toma el $defaultCountry para sacar la zona.
            if (!empty($id_state)) {
                $this->context->country->id_zone = State::getIdZone($id_state);
            }
        }

        if ($old_message = Message::getMessageByCartId((int) $this->context->cart->id)) {
            $this->context->smarty->assign('oldMessage', $old_message['message']);
        }

        $cartPresenter = new CartPresenter();
        $presented_cart = $cartPresenter->present($this->context->cart);

        $conditionsToApproveFinder = new ConditionsToApproveFinder($this->context, $this->context->getTranslator());

        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'ps_stock_management' => Configuration::get('PS_STOCK_MANAGEMENT'),
            'cart' => $presented_cart,
            'customer' => ($this->context->customer->isLogged() ? $this->context->customer : false),
            'onepagecheckoutps' => $this,
            'CONFIGS'               => $this->config_vars,
            'ONEPAGECHECKOUTPS_IMG' => $this->onepagecheckoutps_dir.'views/img/',
            'ONEPAGECHECKOUTPS_TPL' => $this->onepagecheckoutps_tpl,
            'PS_WEIGHT_UNIT'        => Configuration::get('PS_WEIGHT_UNIT'),
            'urls' => $this->context->controller->getTemplateVarUrls(),
            'conditions_to_approve' => $conditionsToApproveFinder->getConditionsToApproveForTemplate(),
            'total_cart' => Tools::displayPrice(
                $this->context->cart->getOrderTotal(),
                new Currency($this->context->cart->id_currency),
                false
            )
        ));

        $summary = $this->context->cart->getSummaryDetails();
        $this->context->smarty->assign($summary);

        $total_free_ship = 0;
        $free_ship       = Tools::convertPrice(
            (float) Configuration::get('PS_SHIPPING_FREE_PRICE'),
            new Currency((int) $this->context->cart->id_currency)
        );

        if (empty($free_ship)) {
            $carrier = new Carrier($this->context->cart->id_carrier);

            if (Validate::isLoadedObject($carrier)) {
                if ($carrier->shipping_method == Carrier::SHIPPING_METHOD_PRICE && $carrier->is_free == 0) {
                    $total_products = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                    $ranges = RangePrice::getRanges((int)$carrier->id);
                    $id_zone = Address::getZoneById((int)$this->context->cart->id_address_delivery);

                    foreach ($ranges as $range) {
                        $query = new DbQuery();
                        $query->select('price');
                        $query->from('delivery');
                        $query->where('id_range_price = '.(int)$range['id_range_price']);
                        $query->where('id_zone = '.(int)$id_zone);
                        $query->where('id_carrier = '.(int)$carrier->id);

                        $cost_shipping = Db::getInstance()->getValue($query);
                        if ($cost_shipping == 0 && $total_products < $range['delimiter1']) {
                            $free_ship = $range['delimiter1'];
                            break;
                        }
                    }
                }
            }
        }

        if ($free_ship) {
            $discounts         = $this->context->cart->getCartRules();
            $total_discounts   = $this->context->cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
            $total_products_wt = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
            $total_free_ship   = $free_ship - ($total_products_wt - $total_discounts);

            foreach ($discounts as $discount) {
                if ($discount['free_shipping'] == 1) {
                    $total_free_ship = 0;
                    break;
                }
            }

            $total_free_ship = Tools::displayPrice($total_free_ship, $this->context->currency);
        }
        $this->context->smarty->assign('free_ship', $total_free_ship);

        $this->addModulesExtraFee();

        if ($set_id_customer_opc) {
            $this->context->customer->id = null;
            unset($this->context->cookie->id_customer);

            $this->context->cart->id_customer = null;
            $this->context->cart->update();
        }

        $html = '';

        // Check minimal amount
        $minimal_purchase = $this->checkMinimalPurchase();
        if (!empty($minimal_purchase)) {
            $html .= '<div class="alert alert-warning">'.$minimal_purchase.'</div>';
        }
        
        if ($this->config_vars['OPC_COMPATIBILITY_REVIEW']) {
            $html .= $this->context->smarty->fetch(_PS_THEME_DIR_.'shopping-cart.tpl');
        } else {
            $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/review.tpl');
        }

        $html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/review_footer.tpl');

        return $html;
    }

    public function addModulesExtraFee()
    {
        $payment_modules_fee = array();
        //comentado hasta que se haga una compatibilidad.
        /*$total = $this->context->cart->getOrderTotal();
        $label_fee = $this->l('Additional fees for payment');
        $label_total = $this->l('Total + Fee');*/

        Media::addJsDef(array('payment_modules_fee' => Tools::jsonEncode($payment_modules_fee)));

        return $payment_modules_fee;
    }

    public function checkMinimalPurchase()
    {
        $msg = '';
        $currency = Currency::getCurrency((int) $this->context->cart->id_currency);
        $minimal_purchase = Tools::convertPrice((float) Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        $total_products = $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        if ($this->isModuleActive('syminimalpurchase')) {
            $customer = new Customer((int)($this->context->customer->id));
            $id_group = $customer->id_default_group;
            $minimal_purchase_groups = Tools::jsonDecode(Configuration::get('syminimalpurchase'));

            if ($minimal_purchase_groups && isset($minimal_purchase_groups->{$id_group})) {
                $minimal_purchase = $minimal_purchase_groups->{$id_group};
            }
        } elseif ($minimumpurchasebycg = $this->isModuleActive('minimumpurchasebycg')) {
            if (!$minimumpurchasebycg->hasAllowedMinimumPurchase()) {
                $minimal_purchase = $minimumpurchasebycg->minimumpurchaseallowed;
            }
        }

        if ($total_products < $minimal_purchase) {
            $msg = sprintf(
                $this->l('A minimum purchase total of %1s (tax excl.) is required to validate your order, current purchase total is %2s (tax excl.).'),
                Tools::displayPrice($minimal_purchase, $currency),
                Tools::displayPrice($total_products, $currency)
            );
        }

        return $msg;
    }

    public function placeOrder($order_controller)
    {
        $password       = '';
        $is_set_invoice = false;

        //check fields are sent
        if (Tools::getIsset('fields_opc')) {
            $fields                          = Tools::jsonDecode(Tools::getValue('fields_opc'));
            $id_customer                     = Tools::getValue('id_customer', null);
            $id_address_delivery             = Tools::getValue('id_address_delivery', null);
            $id_address_invoice              = Tools::getValue('id_address_invoice', null);
            $checkbox_create_invoice_address = Tools::getValue('checkbox_create_invoice_address', null);

            if ($this->context->customer->isLogged()) {
                //En el caso que ya este logueado, pero no sean enviados los ids desde el formulario por algun motivo.
                if (empty($id_customer)) {
                    $id_customer = $this->context->cart->id_customer;

                    if (empty($id_address_delivery)) {
                        $id_address_delivery = $this->context->cart->id_address_delivery;
                    }
                    if (empty($id_address_invoice)) {
                        $id_address_invoice = $this->context->cart->id_address_invoice;
                    }
                } else {
                    if (empty($id_address_delivery) &&
                        ($this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'] || !$this->context->cart->isVirtualCart())) {
                        $id_address_delivery = $this->createAddress($id_customer);
                    }
                    if (empty($id_address_invoice)
                        && (!empty($checkbox_create_invoice_address)
                            || ($this->config_vars['OPC_ENABLE_INVOICE_ADDRESS']
                                && $this->config_vars['OPC_REQUIRED_INVOICE_ADDRESS']))
                    ) {
                        $id_address_invoice = $this->createAddress($id_customer, 'invoice');
                    }
                }
            } elseif (empty($id_address_delivery) && !empty($this->context->cart->id_address_delivery)) {
                $this->checkAddressOrder();
                
                $id_address_delivery = $this->context->cart->id_address_delivery;
            }

            $customer         = new Customer((int) $id_customer);
            $address_delivery = new Address((int) $id_address_delivery);
            $address_invoice  = new Address((int) $id_address_invoice);

            $this->validateFields($fields, $customer, $address_delivery, $address_invoice, $password, $is_set_invoice);

            // Check minimal amount
            $minimal_purchase = $this->checkMinimalPurchase();
            if (!empty($minimal_purchase)) {
                $this->errors[] = $minimal_purchase;
            }

            $this->supportModuleDeliveryDays();

            // If some products have disappear
            foreach ($this->context->cart->getProducts() as $product) {
                $show_message_stock = true;

                if ($show_message_stock
                    && (!$product['active']
                        || !$product['available_for_order']
                        || (!$product['allow_oosp'] && $product['stock_quantity'] < $product['cart_quantity']))
                ) {
                    $this->errors[] = sprintf(
                        $this->l('The product "%s" is not available or does not have stock.'),
                        $product['name']
                    );
                }
            }

            if (!count($this->errors)) {
                if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_DA']) {
                    $address_delivery->firstname = $customer->firstname;
                    $address_delivery->lastname  = $customer->lastname;
                }

                if ($this->config_vars['OPC_USE_SAME_NAME_CONTACT_BA']) {
                    $address_invoice->firstname = $customer->firstname;
                    $address_invoice->lastname  = $customer->lastname;
                }

                if (!$this->context->cart->isVirtualCart()) {
                    Hook::exec('actionCarrierProcess', array('cart' => $this->context->cart));
                }

                if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                    $this->createCustomer($customer, $address_delivery, $address_invoice, $password, $is_set_invoice);

                    if (!count($this->errors)) {
                        //support module Abandoned Cart OPC.
                        Hook::exec('actionACOPCSaveInformation', array(
                            'id_cart' => $this->context->cart->id,
                            'id_customer' => $customer->id
                        ));
                        
                        //if the customer it is same to opc customer, then show it error message
                        if ($customer->id == $this->config_vars['OPC_ID_CUSTOMER']) {
                            $this->errors[] = $this->l('Problem occurred when processing your order, please contact us.');
                        }

                        //$this->supportModuleCheckVat($customer);

                        // Login information have changed, so we check if the cart rules still apply
                        CartRule::autoRemoveFromCart();
                        CartRule::autoAddToCart();

                        if (Tools::getIsset('message')) {
                            $checkout_session = $order_controller->getCheckoutSession();

                            if (method_exists($checkout_session, 'setMessage')) {
                                $checkout_session->setMessage(Tools::getValue('message'));
                            }
                        }

                        return array(
                            'hasError'            => !empty($this->errors),
                            'errors'              => $this->errors,
                            'isSaved'             => true,
                            'isGuest'             => $customer->is_guest,
                            'id_customer'         => (int) $customer->id,
                            'secure_key'          => $this->context->cart->secure_key,
                            'id_address_delivery' => $this->context->cart->id_address_delivery,
                            'id_address_invoice'  => $this->context->cart->id_address_invoice,
                            'token'               => Tools::getToken(false),
                        );
                    }
                } else {
                    //actualizamos la informacion del cliente y sus direcciones si las ha cambio.
                    if ($customer->update()) {
                        $this->context->cookie->customer_lastname  = $customer->lastname;
                        $this->context->cookie->customer_firstname = $customer->firstname;

                        //actualizamos las opciones newsletter y optin directamente
                        //en la base de datos, ya que prestashop no lo hace por algun bug.
                        if ((int) $customer->newsletter == 1) {
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
                                'customer',
                                array('newsletter' => 1),
                                'id_customer = '.$customer->id
                            );
                        }

                        if ((int) $customer->optin == 1) {
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
                                'customer',
                                array('optin' => 1),
                                'id_customer = '.$customer->id
                            );
                        }
                    } else {
                        $this->errors[] = $this->l('An error occurred while creating your account.');
                    }

                    if ($this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'] || !$this->context->cart->isVirtualCart()) {
                        //if is new address, then assign customer logged.
                        if (empty($address_delivery->id_customer)) {
                            $address_delivery->id_customer = $customer->id;
                        }

                        if (!$address_delivery->save()) {
                            $this->errors[] = $this->l('An error occurred while updating your delivery address.');
                        }

                        //en caso que el invoice sea requerido, se pone vacio el id de invoice
                        //para asi crear otra direccion y si cambian los datos de la direccion
                        //se vean reflejados.
                        if ($is_set_invoice && $address_delivery->id == $address_invoice->id) {
                            if (!$this->isSameAddress($address_delivery, $address_invoice)) {
                                $address_invoice->id = null;
                                $address_invoice->alias .= ' 2';
                            }
                        }
                    }

                    //if is new address, then assign customer logged.
                    if ($is_set_invoice && empty($address_invoice->id_customer)) {
                        $address_invoice->id_customer = $customer->id;
                    }

                    if ($is_set_invoice && !$address_invoice->save()) {
                        $this->errors[] = $this->l('An error occurred while creating your delivery address.');
                    }

                    if (!count($this->errors)) {
                        if (!Validate::isLoadedObject($address_delivery) && !$this->config_vars['OPC_SHOW_DELIVERY_VIRTUAL'] && $this->context->cart->isVirtualCart()) {
                            $address_delivery = $address_invoice;
                        }
                        
                        $this->context->cart->id_address_delivery = $address_delivery->id;
                        $this->context->cart->id_address_invoice  = $is_set_invoice ? $address_invoice->id : $address_delivery->id;
                        $this->context->cart->update();

                        $delivery_option = Tools::getValue('delivery_option');
                        $id_address_delivery = Tools::getValue('id_address_delivery');
                        if (!is_array($delivery_option) || empty($id_address_delivery)) {
                            $delivery_option = array($address_delivery->id => $this->context->cart->id_carrier.',');
                        }

                        $this->context->cart->setDeliveryOption($delivery_option);
                        $this->context->cart->update();
                        $this->context->cart->autosetProductAddress();

                        if (Tools::getIsset('message')) {
                            $checkout_session = $order_controller->getCheckoutSession();

                            if (method_exists($checkout_session, 'setMessage')) {
                                $checkout_session->setMessage(Tools::getValue('message'));
                            }
                        }
                    }
                }
            }

            return array(
                'hasError'            => !empty($this->errors),
                'hasWarning'          => !empty($this->warnings),
                'errors'              => $this->errors,
                'warnings'            => $this->warnings,
                'secure_key'          => $this->context->cart->secure_key,
                'id_address_delivery' => $this->context->cart->id_address_delivery,
                'id_address_invoice'  => $this->context->cart->id_address_invoice
            );
        }
    }

    public function deleteEmptyAddressesOPC()
    {
        $query = 'DELETE FROM '._DB_PREFIX_.'address WHERE id_customer = '.(int)$this->config_vars['OPC_ID_CUSTOMER'];
        Db::getInstance()->execute($query);

        $query = new DbQuery();
        $query->select('*');
        $query->from('cart');
        $query->where('id_cart NOT IN (SELECT id_cart FROM '._DB_PREFIX_.'orders)');

        $carts = Db::getInstance()->executeS($query);

        if (count($carts) > 0) {
            foreach ($carts as $cart) {
                $query = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = '.(int)$cart['id_address_delivery'];
                $result = Db::getInstance()->executeS($query);

                if ((int)$cart['id_customer'] == (int)$this->config_vars['OPC_ID_CUSTOMER'] || !$result) {
                    Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'cart WHERE id_cart = '.(int) $cart['id_cart']);
                    Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart = '.(int) $cart['id_cart']);
                    Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'cart_cart_rule WHERE id_cart = '.(int) $cart['id_cart']);
                }
            }
        }

        return array(
            'message_code' => self::CODE_SUCCESS,
            'message' => $this->l('Created temporary addresses were deleted successfully.')
        );
    }

    public function getTemplateVarsOPC($only_register, $show_authentication)
    {
        $language = $this->context->language;

        $countries = Country::getCountries($this->context->language->id, true);
        $countries_js = array();
        $countriesNeedIDNumber = array();
        $countriesNeedZipCode = array();
        $countriesIsoCode = array();

        foreach ($countries as $country) {
            $countriesIsoCode[$country['id_country']] = $country['iso_code'];
            $countriesNeedIDNumber[$country['id_country']] = $country['need_identification_number'];

            if (!empty($country['zip_code_format'])) {
                $countriesNeedZipCode[$country['id_country']] = $country['zip_code_format'];
            }

            if ($country['contains_states'] == 1 && isset($country['states']) && count($country['states']) > 0) {
                foreach ($country['states'] as $state) {
                    if ($state['active'] == 1) {
                        $countries_js[$country['id_country']][] = array(
                            'id' => $state['id_state'],
                            'name' => $state['name'],
                            'iso_code' => $state['iso_code']
                        );
                    }
                }
            }
        }

        $is_set_invoice = false;
        if (isset($this->context->cookie->is_set_invoice)) {
            $is_set_invoice = $this->context->cookie->is_set_invoice;
        }

        $date_format_language = $this->dateFormartPHPtoJqueryUI($language->date_format_lite);

        $opc_social_networks = $this->config_vars['OPC_SOCIAL_NETWORKS'];
        $opc_social_networks = Tools::jsonDecode($opc_social_networks);

        $id_country_delivery_default = FieldClass::getDefaultValue('delivery', 'id_country');
        $iso_code_country_delivery_default = Country::getIsoById($id_country_delivery_default);

        $id_country_invoice_default = FieldClass::getDefaultValue('invoice', 'id_country');
        $iso_code_country_invoice_default = Country::getIsoById($id_country_invoice_default);

        //grid steps
        $position_steps = array(
            0 => array(
                'classes' => ($only_register ? '' : 'col-md-4 col-sm-5').' col-xs-12 col-12',
                'rows' => array(
                    0 => array(
                        'name_step' => 'customer',
                        'classes' => 'col-xs-12 col-12'
                    )
                )
            ),
            1 => array(
                'classes' => 'col-md-8 col-sm-7 col-xs-12 col-12',
                'rows' => array(
                    0 => array(
                        'name_step' => 'carrier',
                        'classes' => 'col-xs-12 col-12 col-md-6'
                    ),
                    1 => array(
                        'name_step' => 'payment',
                        'classes' => 'col-xs-12 col-12 '.($this->context->cart->isVirtualCart() ? 'col-md-12' : 'col-md-6')
                    ),
                    2 => array(
                        'name_step' => 'review',
                        'classes' => 'col-xs-12 col-12'
                    )
                )
            )
        );

        $messageValidate = array(
            'errorGlobal'           => $this->l('This is not a valid.'),
            'errorIsName'           => $this->l('This is not a valid name.'),
            'errorIsEmail'          => $this->l('This is not a valid email address.'),
            'errorIsPostCode'       => $this->l('This is not a valid post code.'),
            'errorIsAddress'        => $this->l('This is not a valid address.'),
            'errorIsCityName'       => $this->l('This is not a valid city.'),
            'isMessage'             => $this->l('This is not a valid message.'),
            'errorIsDniLite'        => $this->l('This is not a valid document identifier.'),
            'errorIsPhoneNumber'    => $this->l('This is not a valid phone.'),
            'errorIsPasswd'         => $this->l('This is not a valid password. Minimum 5 characters.'),
            'errorisBirthDate'      => $this->l('This is not a valid birthdate.'),
            'errorisDate'           => $this->l('This is not a valid date.'),
            'badUrl'                => $this->l('This is not a valid url.').'ex: http://www.domain.com',
            'badInt'                => $this->l('This is not a valid.'),
            'notConfirmed'          => $this->l('The values do not match.'),
            'lengthTooLongStart'    => $this->l('It is only possible enter'),
            'lengthTooShortStart'   => $this->l('The input value is shorter than '),
            'lengthBadEnd'          => $this->l('characters.'),
            'requiredField'         => $this->l('This is a required field.')
        );

        $register_customer = (bool)Tools::getValue('rc', false);
        if (($register_customer == 1 && !$this->context->customer->isLogged()) ||
            ($show_authentication && !$this->context->customer->isLogged())) {
            $register_customer = true;
        }

        $templateVars = array(
            'messageValidate'               => $messageValidate,
            'pts_static_token'              => Tools::encrypt('onepagecheckoutps/index'),
            'static_token'                  => Tools::getToken(false),
            'countries'                     => $countries_js,
            'countriesNeedIDNumber'         => $countriesNeedIDNumber,
            'countriesNeedZipCode'          => $countriesNeedZipCode,
            'countriesIsoCode'              => $countriesIsoCode,
            'position_steps'                => $position_steps,
            'payment_modules_fee'           => $this->addModulesExtraFee(),
            'is_virtual_cart'               => $this->context->cart->isVirtualCart(),
            'hook_create_account_top'       => Hook::exec('displayCustomerAccountFormTop'),
            'hook_create_account_form'      => Hook::exec('displayCustomerAccountForm'),
            'opc_social_networks'           => $opc_social_networks,
            'is_set_invoice'                => $is_set_invoice,
            'register_customer' => $register_customer,
            'OnePageCheckoutPS' => array(
                'date_format_language'          => $date_format_language,
                'id_country_delivery_default'   => $id_country_delivery_default,
                'id_country_invoice_default'    => $id_country_invoice_default,
                'iso_code_country_delivery_default' => $iso_code_country_delivery_default,
                'iso_code_country_invoice_default'  => $iso_code_country_invoice_default,
                'IS_GUEST' => (bool)$this->context->customer->isGuest(),
                'IS_LOGGED' => (bool)$this->context->customer->isLogged(),
                'iso_code_country_invoice_default'  => $iso_code_country_invoice_default,
                'LANG_ISO_ALLOW' => array('es', 'en', 'ca', 'br', 'eu', 'pt', 'eu', 'mx'),
                'CONFIGS' => $this->config_vars,
                'ONEPAGECHECKOUTPS_DIR' => $this->onepagecheckoutps_dir,
                'ONEPAGECHECKOUTPS_IMG' => $this->onepagecheckoutps_dir.'views/img/',
                'PRESTASHOP' => array(
                    'CONFIGS' => array (
                        'PS_TAX_ADDRESS_TYPE' => Configuration::get('PS_TAX_ADDRESS_TYPE'),
                        'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                    ),
                ),
                'Msg' => array(
                    'there_are' => $this->l('There are'),
                    'there_is' => $this->l('There is'),
                    'error' => $this->l('Error'),
                    'errors' => $this->l('Errors'),
                    'field_required' => $this->l('Required'),
                    'dialog_title' => $this->l('Confirm Order'),
                    'no_payment_modules' => $this->l('There are no payment methods available.'),
                    'validating' => $this->l('Validating, please wait'),
                    'error_zipcode' => $this->l('The Zip / Postal code is invalid'),
                    'error_registered_email' => $this->l('An account is already registered with this e-mail'),
                    'error_registered_email_guest' => $this->l('This email is already registered, you can login or fill form again.'),
                    'delivery_billing_not_equal' => $this->l('Delivery address alias cannot be the same as billing address alias'),
                    'errors_trying_process_order' => $this->l('The following error occurred while trying to process the order'),
                    'agree_terms_and_conditions' => $this->l('You must agree to the terms of service before continuing.'),
                    'agree_privacy_policy' => $this->l('You must agree to the privacy policy before continuing.'),
                    'fields_required_to_process_order' => $this->l('You must complete the required information to process your order.'),
                    'check_fields_highlighted' => $this->l('Check the fields that are highlighted and marked with an asterisk.'),
                    'error_number_format' => $this->l('The format of the number entered is not valid.'),
                    'oops_failed' => $this->l('Oops! Failed'),
                    'continue_with_step_3' => $this->l('Continue with step 3.'),
                    'email_required' => $this->l('Email address is required.'),
                    'email_invalid' => $this->l('Invalid e-mail address.'),
                    'password_required' => $this->l('Password is required.'),
                    'password_too_long' => $this->l('Password is too long.'),
                    'password_invalid' => $this->l('Invalid password.'),
                    'addresses_same' => $this->l('You must select a different address for shipping and billing.'),
                    'create_new_address' => $this->l('Are you sure you wish to add a new delivery address? You can use the current address and modify the information.'),
                    'cart_empty' => $this->l('Your shopping cart is empty. You need to refresh the page to continue.'),
                    'dni_spain_invalid' => $this->l('DNI/CIF/NIF is invalid.'),
                    'payment_method_required' => $this->l('Please select a payment method to proceed.'),
                    'shipping_method_required' => $this->l('Please select a shipping method to proceed.'),
                    'select_pickup_point' => $this->l('To select a pick up point is necessary to complete your information and delivery address in the first step.'),
                    'need_select_pickup_point' => $this->l('You need to select on shipping a pickup point to continue with the purchase.'),
                    'select_date_shipping' => $this->l('Please select a date for shipping.'),
                    'confirm_payment_method' => $this->l('Confirmation payment'),
                    'to_determinate' => $this->l('To determinate'),
                    'login_customer' => $this->l('Login'),
                    'processing_purchase' => $this->l('Processing purchase')
                )
            )
        );

        return $templateVars;
    }

    public function callGeonamesJSON()
    {
        $method = Tools::getValue('method');
        $params = http_build_query(Tools::getValue('params'));
        
        $ch = curl_init('http://api.geonames.org/'.$method.'?'.$params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

    /*
     * Matches each symbol of PHP date format standard
     * with jQuery equivalent codeword
     * @author Tristan Jahier
     */
    public function dateFormartPHPtoJqueryUI($php_format)
    {
        $symbols_matching = array(
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y',
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => '',
            'G' => '',
            'h' => '',
            'H' => '',
            'i' => '',
            's' => '',
            'u' => '',
        );
        $jqueryui_format  = '';
        $escaping         = false;
        $size_format      = Tools::strlen($php_format);
        for ($i = 0; $i < $size_format; $i++) {
            $char = $php_format[$i];
            if ($char === '\\') { // PHP date format escaping character
                $i++;
                if ($escaping) {
                    $jqueryui_format .= $php_format[$i];
                } else {
                    $jqueryui_format .= '\''.$php_format[$i];
                }
                $escaping = true;
            } else {
                if ($escaping) {
                    $jqueryui_format .= "'";
                    $escaping = false;
                }
                if (isset($symbols_matching[$char])) {
                    $jqueryui_format .= $symbols_matching[$char];
                } else {
                    $jqueryui_format .= $char;
                }
            }
        }

        return $jqueryui_format;
    }

    /* sorts an array of named arrays by the supplied fields
      code by dholmes at jccc d0t net
      taken from http://au.php.net/function.uasort
      modified by cablehead, messju and pscs at http://www.phpinsider.com/smarty-forum */
    public function smartyModifierSortby($data, $sortby)
    {
        static $sort_funcs = array();

        if (empty($sort_funcs[$sortby])) {
            $code = "\$c=0;";
            foreach (explode(',', $sortby) as $key) {
                $d = '1';
                if (Tools::substr($key, 0, 1) == '-') {
                    $d   = '-1';
                    $key = Tools::substr($key, 1);
                }
                if (Tools::substr($key, 0, 1) == '#') {
                    $key = Tools::substr($key, 1);
                    $code .= "if ( ( \$c = (\$a['$key'] - \$b['$key'])) != 0 ) return $d * \$c;\n";
                } else {
                    $code .= "if ( (\$c = strcasecmp(\$a['$key'],\$b['$key'])) != 0 ) return $d * \$c;\n";
                }
            }
            $code .= 'return $c;';
            $sort_func           = $sort_funcs[$sortby] = create_function('$a, $b', $code);
        } else {
            $sort_func = $sort_funcs[$sortby];
        }

        uasort($data, $sort_func);

        return $data;
    }

    public function includeTpl($tpl, $params)
    {
        $this->smarty->assign($params);
        echo $this->smarty->fetch(_PS_MODULE_DIR_.$this->name.'/views/templates/front/'.$tpl);
    }
}
