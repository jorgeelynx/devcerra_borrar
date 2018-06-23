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
 */

use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

class OrderController extends OrderControllerCore
{
    /*
    * module: onepagecheckoutps
    * date: 2017-09-16
    * version: 1.0.1
    */

    /* KEY_OPC_1.0.1 */
    public $ssl = true;
    public $php_self = 'order';
    public $page_name = 'checkout';
    public $checkoutDeliveryStep;

    public $name_module = 'onepagecheckoutps';
    public $onepagecheckoutps;
    public $onepagecheckoutps_dir;
    public $opc_fields;
    public $is_active_module;
    private $only_register = false;
    private $show_authentication = false;

    public function init()
    {
        $this->onepagecheckoutps = Module::getInstanceByName($this->name_module);
        $this->onepagecheckoutps_dir = __PS_BASE_URI__.'modules/'.$this->name_module.'/';

        $this->show_authentication = false;

        /*if ($this->onepagecheckoutps->isModuleActive('checkvat')) {
            $show_authentication = true;
        }*/

        if ((Tools::getIsset('rc')
            || $this->show_authentication)
            && !$this->context->customer->isLogged()
            && Validate::isLoadedObject($this->context->cart)
        ) {
            $this->display_column_right = true;
            $this->display_column_left  = true;
        } else {
            $this->display_column_right = false;
            $this->display_column_left  = false;
        }

        parent::init();

        if (Validate::isLoadedObject($this->onepagecheckoutps)
            && $this->onepagecheckoutps->isModuleActive($this->name_module)
        ) {
            $this->is_active_module = true;
        } else {
            $this->is_active_module = false;
        }

        if (!$this->onepagecheckoutps->checkModulePTS()) {
            $this->is_active_module = false;
        }

        if (!$this->onepagecheckoutps->isVisible()) {
            $this->is_active_module = false;
        }

        if (!$this->is_active_module) {
            return;
        }

        $emailverificationopc = $this->onepagecheckoutps->isModuleActive('emailverificationopc');
        if ($emailverificationopc) {
            if (isset($this->context->cookie->check_account)) {
                $require_email_verified = Configuration::get('EVOPC_REQUIRE_VERIFY_EMAIL');

                if ($require_email_verified) {
                    $url = $this->context->link->getModuleLink('emailverificationopc', 'verifyemail', array(
                        'token' => Tools::encrypt('emailverificationopc/index')
                    ));
                    Tools::redirect($url);
                }
            }
        }

        if (empty($this->context->cart->id_address_delivery) && !$this->context->customer->isLogged()) {
            $this->context->cart->id_address_delivery = $this->onepagecheckoutps->getIdAddressAvailable('delivery');
            if (empty($this->context->cart->id_address_invoice)) {
                $this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery;
            }

            $this->context->cart->save();
        }

        if (Tools::getIsset('rc')) {
            $this->only_register = true;
        } else if ($this->show_authentication && (!$this->context->customer->isLogged() && !$this->context->customer->isGuest())) {
            $this->only_register = true;
        }

        if ($this->only_register) {
            if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
                $meta_authentication = Meta::getMetaByPage('my-account', $this->context->language->id);
            } else {
                $meta_authentication = Meta::getMetaByPage('authentication', $this->context->language->id);
            }

            $this->context->smarty->assign('meta_title', $meta_authentication['title']);
            $this->context->smarty->assign('meta_description', $meta_authentication['description']);
        }

        $this->context->smarty->assign('show_authentication', $this->show_authentication);
        $this->context->smarty->assign('register_customer', $this->only_register);

        //si no tiene direcciones el cliente logueado, creamos una, de lo contrario nos da error el initContent.
        if (Validate::isLoadedObject($this->context->customer) && $this->context->customer->isLogged()) {
            $address_customer = $this->context->customer->getAddresses($this->context->cookie->id_lang);

            if (empty($address_customer)) {
                $id_address_delivery = $this->onepagecheckoutps->getIdAddressAvailable('delivery');

                $this->context->cart->id_address_delivery = $id_address_delivery;
                $this->context->cart->id_address_invoice = $id_address_delivery;
                $this->context->cart->update();
            }
        }
    }

    public function initContent()
    {
        parent::initContent();

        if (!$this->is_active_module) {
            return;
        }

        if ($this->onepagecheckoutps->config_vars['OPC_REDIRECT_DIRECTLY_TO_OPC']
            && !Tools::getIsset('rc')
            && !Tools::getIsset('checkout')
        ) {
            $presenter = new CartPresenter();
            $presented_cart = $presenter->present($this->context->cart, true);

            $this->context->smarty->assign(array(
                'cart' => $presented_cart,
                'static_token' => Tools::getToken(false),
            ));

            if (count($presented_cart['products']) > 0) {
                $this->setTemplate('checkout/cart');
            } else {
                $this->context->smarty->assign(array(
                    'allProductsLink' => $this->context->link->getCategoryLink(Configuration::get('PS_HOME_CATEGORY')),
                ));
                $this->setTemplate('checkout/cart-empty');
            }
        } else {
            $language = $this->context->language;
            $smarty = $this->context->smarty;

            $selected_country = (int) FieldClass::getDefaultValue('delivery', 'id_country');
            if (!$this->context->customer->isLogged() && (Configuration::get('PS_GEOLOCATION_ENABLED'))) {
                if ($this->context->country->active) {
                    $selected_country = $this->context->country->id;
                }
            }

            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
                $countries = Carrier::getDeliveredCountries($language->id, true, true);
            } else {
                $countries = Country::getCountries($language->id, true);
            }

            //-----------------------------------------------------------------------------
            //GROUP CUSTOMER
            //-----------------------------------------------------------------------------
            $groups            = Group::getGroups($this->context->cookie->id_lang);
            $groups_availables = '';

            if (!empty($this->onepagecheckoutps->config_vars['OPC_CHOICE_GROUP_CUSTOMER_ALLOW'])) {
                $groups_availables = explode(
                    ',',
                    $this->onepagecheckoutps->config_vars['OPC_CHOICE_GROUP_CUSTOMER_ALLOW']
                );
            }

            foreach ($groups as $key => $group) {
                if (is_array($groups_availables)) {
                    if (!in_array($group['id_group'], $groups_availables)) {
                        unset($groups[$key]);
                    }
                }
            }
            //-----------------------------------------------------------------------------

            $opc_fields          = array();
            $opc_fields_position = array();
            $is_need_invoice     = false;

            $fields = FieldControl::getAllFields($this->context->cookie->id_lang);

            foreach ($fields as $field) {
                if (!$field->active || (Tools::getIsset('rc') && $field->is_custom)) {
                    continue;
                }

                $field->capitalize = false;
                if (in_array($field->name, $this->onepagecheckoutps->fields_to_capitalize)
                    && $this->onepagecheckoutps->config_vars['OPC_CAPITALIZE_FIELDS']
                ) {
                    $field->capitalize = true;
                }

                if ($field->object == $this->onepagecheckoutps->globals->object->customer) {
                    if ($this->onepagecheckoutps->config_vars['OPC_CHOICE_GROUP_CUSTOMER']) {
                        $new_field = new FieldControl();

                        $new_field->name          = 'group_customer';
                        $new_field->id_control    = 'group_customer';
                        $new_field->name_control  = 'group_customer';
                        $new_field->object        = 'customer';
                        $new_field->description   = $this->onepagecheckoutps->getMessageError(4);
                        $new_field->type          = 'isInt';
                        $new_field->size          = '11';
                        $new_field->type_control  = 'select';
                        $new_field->default_value = ($this->context->customer->isLogged() ? $this->context->customer->id_default_group : '');
                        $new_field->required      = false;
                        $new_field->is_custom     = false;
                        $new_field->active        = true;
                        $new_field->options       = array(
                            'empty_option' => true,
                            'value'        => 'id_group',
                            'description'  => 'name',
                            'data'         => $groups
                        );

                        $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                    }

                    if ($field->name == 'id_gender') {
                        $genders = array();
                        foreach (Gender::getGenders() as $i => $gender) {
                            $genders[$i]['id_gender'] = $gender->id_gender;
                            $genders[$i]['name']      = $gender->name;
                        }

                        $field->options = array(
                            'value'       => 'id_gender',
                            'description' => 'name',
                            'data'        => $genders
                        );
                    } elseif ($field->name == 'passwd') {
                        if ($this->context->customer->isLogged() || $this->context->customer->isGuest()) {
                            continue;
                        }

                        if ($this->onepagecheckoutps->config_vars['OPC_REQUEST_PASSWORD'] &&
                            $this->onepagecheckoutps->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD'] &&
                            !Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                            $new_field = new FieldControl();

                            $new_field->name          = 'checkbox_create_account';
                            $new_field->id_control    = 'checkbox_create_account';
                            $new_field->name_control  = 'checkbox_create_account';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->onepagecheckoutps->getMessageError(0);
                            $new_field->type          = 'isBool';
                            $new_field->size          = '0';
                            $new_field->type_control  = 'checkbox';
                            $new_field->default_value = '0';
                            $new_field->required      = false;
                            $new_field->is_custom     = false;
                            $new_field->active        = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }

                        //add checkbox guest checkout
                        if (Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                            $new_field = new FieldControl();

                            $new_field->name          = 'checkbox_create_account_guest';
                            $new_field->id_control    = 'checkbox_create_account_guest';
                            $new_field->name_control  = 'checkbox_create_account_guest';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->onepagecheckoutps->getMessageError(1);
                            $new_field->type          = 'isBool';
                            $new_field->size          = '0';
                            $new_field->type_control  = 'checkbox';
                            $new_field->default_value = '0';
                            $new_field->required      = false;
                            $new_field->is_custom     = false;
                            $new_field->active        = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }

                        if ($this->onepagecheckoutps->config_vars['OPC_REQUEST_PASSWORD']) {
                            //add field password
                            $field->name_control = 'passwd_confirmation';

                            if ((int) $this->onepagecheckoutps->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD']) {
                                $field->required = false;
                            } else {
                                $field->required = true;
                            }

                            $opc_fields[$field->object.'_'.$field->name] = $field;

                            //add field confirmation password
                            $new_field = new FieldControl();

                            $new_field->name          = 'conf_passwd';
                            $new_field->id_control    = 'customer_conf_passwd';
                            $new_field->name_control  = 'passwd';
                            $new_field->object        = 'customer';
                            $new_field->description   = $this->onepagecheckoutps->getMessageError(2);
                            $new_field->type          = 'confirmation';
                            $new_field->size          = '32';
                            $new_field->type_control  = 'textbox';
                            $new_field->default_value = '';

                            if ((int) $this->onepagecheckoutps->config_vars['OPC_OPTION_AUTOGENERATE_PASSWORD']) {
                                $new_field->required = false;
                            } else {
                                $new_field->required = true;
                            }

                            $new_field->is_custom = false;
                            $new_field->active    = true;
                            $new_field->is_passwd = true;

                            $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                        }

                        continue;
                    } elseif ($field->name == 'email') {
                        if (!$this->context->customer->isLogged() && !$this->context->customer->isGuest()) {
                            //add field email
                            $field->name_control                         = 'email_confirmation';
                            $opc_fields[$field->object.'_'.$field->name] = $field;

                            if ($this->onepagecheckoutps->config_vars['OPC_REQUEST_CONFIRM_EMAIL']) {
                                //add field confirmation email
                                $new_field = new FieldControl();

                                $new_field->name                                     = 'conf_email';
                                $new_field->id_control                               = 'customer_conf_email';
                                $new_field->name_control                             = 'email';
                                $new_field->object                                   = 'customer';
                                $new_field->description = $this->onepagecheckoutps->getMessageError(3);
                                $new_field->type                                     = 'confirmation';
                                $new_field->size                                     = '128';
                                $new_field->type_control                             = 'textbox';
                                $new_field->default_value                            = '';
                                $new_field->required                                 = $field->required;
                                $new_field->is_custom                                = false;
                                $new_field->active                                   = true;
                                $opc_fields[$new_field->object.'_'.$new_field->name] = $new_field;
                            }

                            continue;
                        }
                    }
                } elseif ($field->object == $this->onepagecheckoutps->globals->object->delivery) {
                    if ($this->onepagecheckoutps->config_vars['OPC_USE_SAME_NAME_CONTACT_DA']) {
                        if ($field->name == 'firstname') {
                            continue;
                        } elseif ($field->name == 'lastname') {
                            continue;
                        }
                    }
                } elseif ($field->object == $this->onepagecheckoutps->globals->object->invoice) {
                    if ($this->onepagecheckoutps->config_vars['OPC_ENABLE_INVOICE_ADDRESS']) {
                        if ($this->onepagecheckoutps->config_vars['OPC_USE_SAME_NAME_CONTACT_BA']) {
                            if ($field->name == 'firstname') {
                                continue;
                            } elseif ($field->name == 'lastname') {
                                continue;
                            }
                        }

                        if ($this->onepagecheckoutps->config_vars['OPC_REQUIRED_INVOICE_ADDRESS']) {
                            $is_need_invoice = true;
                        }
                    }
                }

                if ($field->name == 'id_country') {
                    $field->default_value = $selected_country;
                    $field->options       = array(
                        'empty_option' => true,
                        'value'        => 'id_country',
                        'description'  => 'name',
                        'data'         => $countries
                    );
                }

                if ($field->name == 'vat_number') {
                    $module = $this->onepagecheckoutps->isModuleActive('vatnumber');
                    if ($module) {
                        if (Configuration::get('VATNUMBER_MANAGEMENT') || Configuration::get('VATNUMBER_CHECKING')) {
                            $field->type = 'isVatNumber';
                        }
                    }
                }

                $opc_fields[$field->object.'_'.$field->name] = $field;
            }

            $fields_position = $this->onepagecheckoutps->getFieldsPosition();
            if ($fields_position) {
                foreach ($fields_position as $group => $rows) {
                    foreach ($rows as $row => $fields) {
                        foreach ($fields as $position => $field) {
                            if ($field->name == 'id' && $group == 'customer') {
                                if (isset($opc_fields[$field->object.'_group_customer'])) {
                                    $index = $field->object.'_group_customer';
                                    $opc_fields_position[$group][$row - 2][$position - 1] = $opc_fields[$index];
                                }
                            }

                            //aditional field before
                            if ($field->name == 'passwd') {
                                if (isset($opc_fields[$field->object.'_checkbox_create_account'])) {
                                    $index = $field->object.'_checkbox_create_account';
                                    $opc_fields_position[$group][-1][-1] = $opc_fields[$index];
                                }
                                if (isset($opc_fields[$field->object.'_checkbox_create_account_guest'])) {
                                    $index = $field->object.'_checkbox_create_account_guest';
                                    $opc_fields_position[$group][-1][-1] = $opc_fields[$index];
                                }
                            }

                            //field
                            if (isset($opc_fields[$field->object.'_'.$field->name])) {
                                $index = $field->object.'_'.$field->name;
                                $opc_fields_position[$group][$row][$position] = $opc_fields[$index];
                            }

                            //aditional field after
                            if ($field->name == 'passwd') {
                                if (isset($opc_fields[$field->object.'_conf_passwd'])) {
                                    $index                                            = $field->object.'_conf_passwd';
                                    $opc_fields_position[$group][$row][$position + 1] = $opc_fields[$index];
                                }
                            } elseif ($field->name == 'email') {
                                if (isset($opc_fields[$field->object.'_conf_email'])) {
                                    $index                                            = $field->object.'_conf_email';
                                    $opc_fields_position[$group][$row][$position + 1] = $opc_fields[$index];
                                }
                            }
                        }
                    }
                }
            }

            $smarty->assign(array(
                'OPC_GLOBALS'     => $this->onepagecheckoutps->globals,
                'OPC_FIELDS'      => $opc_fields_position,
                'is_need_invoice' => $is_need_invoice
            ));
            Media::addJsDef(array('is_need_invoice' => $is_need_invoice));

            $this->onepagecheckoutps->addModulesExtraFee();

            $templateVars = $this->onepagecheckoutps->getTemplateVarsOPC($this->only_register, $this->show_authentication);

            $this->context->smarty->assign($templateVars);
            Media::addJsDef($templateVars);

            $this->setTemplate('../../../modules/'.$this->name_module.'/views/templates/front/onepagecheckoutps');
        }
    }

    public function getCheckoutSession()
    {
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectPresenter,
            new PriceFormatter()
        );

        $session = new CheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );

        return $session;
    }

    protected function bootstrap()
    {
        $translator = $this->getTranslator();

        $session = $this->getCheckoutSession();

        $this->checkoutProcess = new CheckoutProcess(
            $this->context,
            $session
        );

        $this->checkoutDeliveryStep = new CheckoutDeliveryStep(
            $this->context,
            $translator
        );

        $this->checkoutDeliveryStep
            ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
            ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
            ->setIncludeTaxes(
                !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer)
                && (int) Configuration::get('PS_TAX')
            )
            ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
            ->setGiftCost(
                $this->context->cart->getGiftWrappingPrice(
                    $this->checkoutDeliveryStep->getIncludeTaxes()
                )
            );

        $this->checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context,
                $translator,
                $this->makeLoginForm(),
                $this->makeCustomerForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context,
                $translator,
                $this->makeAddressForm()
            ))
            ->addStep($this->checkoutDeliveryStep)
            ->addStep(new CheckoutPaymentStep(
                $this->context,
                $translator,
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->context,
                    $translator
                )
            ));
    }

    public function updateCarrier()
    {
        $this->checkoutDeliveryStep->handleRequest(Tools::getAllValues());
    }

    public function postProcess()
    {
        parent::postProcess();

        $this->bootstrap();

        if (!$this->is_active_module) {
            return;
        }

        if (Tools::getIsset('is_ajax')) {
            define('_PTS_SHOW_ERRORS_', true);

            $data_type = 'json';
            if (Tools::isSubmit('dataType')) {
                $data_type = Tools::getValue('dataType');
            }

            $action = Tools::getValue('action');
            if (method_exists($this, $action)) {
                switch ($data_type) {
                    case 'html':
                        die($this->$action());
                    case 'json':
                        $response = Tools::jsonEncode($this->$action());
                        die($response);
                    default:
                        die('Invalid data type.');
                }
            } elseif (method_exists($this->onepagecheckoutps, $action)) {
                switch ($data_type) {
                    case 'html':
                        die($this->onepagecheckoutps->$action($this));
                    case 'json':
                        $response = Tools::jsonEncode($this->onepagecheckoutps->$action($this));
                        die($response);
                    default:
                        die('Invalid data type.');
                }
            } else {
                switch ($action) {
                    case 'updateExtraCarrier':
                        // Change virtualy the currents delivery options
                        $delivery_option = $this->context->cart->getDeliveryOption();
                        $delivery_option[(int) Tools::getValue('id_address')] = Tools::getValue('id_delivery_option');
                        $this->context->cart->setDeliveryOption($delivery_option);
                        $this->context->cart->save();
                        $return = array(
                            'content' => Hook::exec(
                                'displayCarrierList',
                                array(
                                    'address' => new Address((int) Tools::getValue('id_address'))
                                )
                            )
                        );
                        die(Tools::jsonEncode($return));
                    case 'checkRegisteredCustomerEmail':
                        $data = Customer::customerExists(Tools::getValue('email'), true);
                        die(Tools::jsonEncode((int) $data));
                    case 'checkVATNumber':
                        $errors = array();
                        $vat_number = Tools::getValue('vat_number', '');
                        $id_address = $this->context->cart->id_address_delivery;

                        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice') {
                            $id_address = $this->context->cart->id_address_invoice;
                        }

                        if (!empty($vat_number)) {
                            if (Configuration::get('VATNUMBER_MANAGEMENT')) {
                                include_once _PS_MODULE_DIR_.'vatnumber/vatnumber.php';
                                if (class_exists('VatNumber', false) && Configuration::get('VATNUMBER_CHECKING')) {
                                    $errors = VatNumber::WebServiceCheck($vat_number);
                                }
                            }
                        }

                        if (!empty($id_address)) {
                            $address = new Address($id_address);
                            $address->vat_number = $vat_number;
                            $address->save();
                        }

                        die(Tools::jsonEncode($errors));
                }
            }
        }
    }
}
