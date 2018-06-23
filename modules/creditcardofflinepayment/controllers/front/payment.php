<?php
/**
* Credit card offline payment
*
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2017 idnovate.com
*  @license   See above
*/

class CreditCardOfflinePaymentPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(_PS_JS_DIR_.'validate.js');

        if (Configuration::get('CCOFFLINE_PAYMENT_STYLE') == 2) {
            $this->addJS(_MODULE_DIR_.$this->module->name.'/views/js/card/jquery.card.js');
        }
    }

    public function init()
    {
        if (Configuration::get('CCOFFLINE_LEFT_COLUMN')) {
            $this->display_column_left = true;
        } else {
            $this->display_column_left = false;
        }

        if (Configuration::get('CCOFFLINE_RIGHT_COLUMN')) {
            $this->display_column_right = true;
        } else {
            $this->display_column_right = false;
        }

        parent::init();
    }

    public function initContent()
    {
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        try {
            parent::initContent();

            if (!$this->context->cart->nbProducts()) {
                Tools::redirect('index.php');
            }

            //Validate cart
            if ($this->context->cart->id_customer == 0
                || $this->context->cart->id_address_delivery == 0
                || $this->context->cart->id_address_invoice == 0
                || !$this->module->active) {
                Tools::redirect('index.php?controller=order&step=1');
            }

             //Validate cart amount is between limits
            if (!$this->module->checkAmounts($this->context->cart)) {
                Tools::redirect('index.php?controller=order&step=1');
            }

            // Check that this payment option is still available in case the customer
            // changed his address just before the end of the checkout process
            $authorized = false;

            foreach (Module::getPaymentModules() as $module) {
                if ($module['name'] == 'creditcardofflinepayment') {
                    $authorized = true;
                    break;
                }
            }

            if (!$authorized) {
                die($this->module->l('This payment method is not available.', 'payment'));
            }

            $customer = new Customer($this->context->cart->id_customer);
            if (!Validate::isLoadedObject($customer)) {
                Tools::redirect('index.php?controller=order&step=1');
            }

            if (!$this->module->checkCurrency($this->context->cart)) {
                Tools::redirect('index.php?controller=order');
            }

            $this->context->smarty->assign(array(
                'cc_path'                       => _MODULE_DIR_.$this->module->name.'/',
                'nbProducts'                    => $this->context->cart->nbProducts(),
                'default_currency'              => new Currency(Configuration::get('PS_CURRENCY_DEFAULT')),
                'currency'                      => new Currency((int)$this->context->cookie->id_currency),
                'id_currency'                   => (int)$this->context->cookie->id_currency,
                'total'                         => Tools::displayPrice($this->context->cart->getOrderTotal(true)),
                'issuers'                       => CreditCardOfflinePaymentBrands::getIssuers(),
                'CCOFFLINE_REQUIREISSUERNAME'   => Configuration::get('CCOFFLINE_REQUIREISSUERNAME') == '1' ? true : false,
                'CCOFFLINE_REQUIREDISSUERNAME'  => Configuration::get('CCOFFLINE_REQUIREDISSUERNAME') == '1' ? true : false,
                'CCOFFLINE_REQUIRECED'          => Configuration::get('CCOFFLINE_REQUIRECED') == '1' ? true : false,
                'CCOFFLINE_REQUIREDCED'         => Configuration::get('CCOFFLINE_REQUIREDCED') == '1' ? true : false,
                'CCOFFLINE_REQUIREADDRESS'      => Configuration::get('CCOFFLINE_REQUIREADDRESS') == '1' ? true : false,
                'CCOFFLINE_REQUIREDADDRESS'     => Configuration::get('CCOFFLINE_REQUIREDADDRESS') == '1' ? true : false,
                'CCOFFLINE_REQUIREZIPCODE'      => Configuration::get('CCOFFLINE_REQUIREZIPCODE') == '1' ? true : false,
                'CCOFFLINE_REQUIREDZIPCODE'     => Configuration::get('CCOFFLINE_REQUIREDZIPCODE') == '1' ? true : false,
                'CCOFFLINE_REQUIRECITY'         => Configuration::get('CCOFFLINE_REQUIRECITY') == '1' ? true : false,
                'CCOFFLINE_REQUIREDCITY'        => Configuration::get('CCOFFLINE_REQUIREDCITY') == '1' ? true : false,
                'CCOFFLINE_REQUIRESTATE'        => Configuration::get('CCOFFLINE_REQUIRESTATE') == '1' ? true : false,
                'CCOFFLINE_REQUIREDSTATE'       => Configuration::get('CCOFFLINE_REQUIREDSTATE') == '1' ? true : false,
                'CCOFFLINE_REQUIRECOUNTRY'      => Configuration::get('CCOFFLINE_REQUIRECOUNTRY') == '1' ? true : false,
                'CCOFFLINE_REQUIREDCOUNTRY'     => Configuration::get('CCOFFLINE_REQUIREDCOUNTRY') == '1' ? true : false,
                'CCOFFLINE_REQUIRECARDNUMBER'   => Configuration::get('CCOFFLINE_REQUIRECARDNUMBER') == '1' ? true : false,
                'CCOFFLINE_REQUIREDCARDNUMBER'  => Configuration::get('CCOFFLINE_REQUIREDCARDNUMBER') == '1' ? true : false,
                'CCOFFLINE_REQUIREISSUER'       => Configuration::get('CCOFFLINE_REQUIREISSUER') == '1' ? true : false,
                'CCOFFLINE_REQUIREDISSUER'      => Configuration::get('CCOFFLINE_REQUIREDISSUER') == '1' ? true : false,
                'CCOFFLINE_REQUIREEXP'          => Configuration::get('CCOFFLINE_REQUIREEXP') == '1' ? true : false,
                'CCOFFLINE_REQUIREDEXP'         => Configuration::get('CCOFFLINE_REQUIREDEXP') == '1' ? true : false,
                'CCOFFLINE_REQUIRECVV'          => Configuration::get('CCOFFLINE_REQUIRECVV') == '1' ? true : false,
                'CCOFFLINE_REQUIREDCVV'         => Configuration::get('CCOFFLINE_REQUIREDCVV') == '1' ? true : false,
                'CCOFFLINE_REQUIREPIN'          => Configuration::get('CCOFFLINE_REQUIREPIN') == '1' ? true : false,
                'CCOFFLINE_REQUIREDPIN'         => Configuration::get('CCOFFLINE_REQUIREDPIN') == '1' ? true : false,
                'CCOFFLINE_YEARS'               => Configuration::get('CCOFFLINE_YEARS'),
                'CCOFFLINE_DISPLAYISSUERS'      => Configuration::get('CCOFFLINE_DISPLAYISSUERS'),
                'CCOFFLINE_DISPLAYICONS'        => Configuration::get('CCOFFLINE_DISPLAYICONS'),
                'CCOFFLINE_PAYMENT_STYLE'       => Configuration::get('CCOFFLINE_PAYMENT_STYLE') == 2 ? true : false,
                'cc_path'                       => $this->module->getPathUri(),
                'OPC_SHOW_POPUP_PAYMENT'        => Configuration::get('OPC_SHOW_POPUP_PAYMENT'),
                'validate'                      => false,
            ));

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->setTemplate('payment_execution.tpl');
            } elseif (version_compare(_PS_VERSION_, '1.7', '<')) {
                $this->setTemplate('payment_execution_16.tpl');
            } else {
                $this->setTemplate('module:creditcardofflinepayment/views/templates/front/payment_execution_17.tpl');
            }
        } catch (Exception $e) {
            return false;
        }
    }
}
