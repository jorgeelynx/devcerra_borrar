<?php
/**
 * OrderEdit
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2016 silbersaiten
 * @version   1.2.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

class AdminOrderEditController extends AdminOrdersControllerCore
{

    public function initToolbar()
    {
        $res = parent::initToolbar();
        unset($this->toolbar_btn['new']);
        return $res;
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['new_order']);
    }

    public function createTemplate($tpl_name)
    {
        if (!class_exists('OrderEdit', false)) {
            require_once(_PS_MODULE_DIR_.'orderedit/orderedit.php');

            new OrderEdit();
        }

        $tpl_path = OrderEdit::getTplPath();

        if (file_exists($tpl_path.$tpl_name)) {
            return $this->context->smarty->createTemplate($tpl_path.$tpl_name, $this->context->smarty);
        } else {
            return parent::createTemplate($tpl_name);
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS(_MODULE_DIR_.'orderedit/views/css/style.css');

        if ($this->tabAccess['edit'] == 1) {
            $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
            if ($this->display == 'edit' || $this->display == 'view') {
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/timepicker/jquery-ui-timepicker-addon.js');
                $this->addCSS(_MODULE_DIR_.'orderedit/views/css/jquery-ui-timepicker-addon.css');
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/timepicker/jquery-ui-sliderAccess.js');
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/editor.js');

                //$this->assignDownloadProducts();
            } elseif ($this->display == null) {
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/list.js');
            }

            Hook::exec('orderEditHeader');
        }
    }

    protected function l($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true)
    {
        unset($class);
        unset($addslashes);
        unset($htmlentities);
        return Translate::getModuleTranslation('orderedit', $string, get_class($this));
    }

    public function renderView()
    {
        parent::renderView();

        $pm = array();

        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);

            if (Validate::isLoadedObject($module)) {
                $pm[$module->name] = $module->displayName;
            }
        }
        $order = new Order((int)Tools::getValue('id_order'));
        if (!array_key_exists($order->module, $pm)) {
            $pm[$order->module] = $order->payment;
        }

        //get saved custom tax rate of product from table, not from tax calculator
        $details = OrderDetail::getList((int)Tools::getValue('id_order'));
        foreach ($details as $detail) {
            if ($detail['total_price_tax_incl'] != $detail['total_price_tax_excl']) {
                if ($detail['tax_rate'] == 0) {
                    $sql = 'SELECT * FROM `'._DB_PREFIX_.'order_detail_tax`
                    WHERE `id_order_detail` = '.(int)$detail['id_order_detail'];
                    $taxes = Db::getInstance()->executeS($sql);
                    $tax_temp = array();
                    foreach ($taxes as $tax) {
                        $obj = new Tax($tax['id_tax']);
                        $tax_temp[] = sprintf($this->l('%1$s%2$s%%'), ($obj->rate + 0), '&nbsp;');
                    }

                    $this->tpl_view_vars['products'][$detail['id_order_detail']]['tax_rate'] = implode(', ', $tax_temp);
                } else {
                    $this->tpl_view_vars['products'][$detail['id_order_detail']]['tax_rate'] = $detail['tax_rate'];                    
                }
            }

            $this->tpl_view_vars['products'][$detail['id_order_detail']]['id_tax'] = (int)Db::getInstance()->getValue(
                'SELECT id_tax
                FROM `'._DB_PREFIX_.'order_detail_tax`
                WHERE id_order_detail='.(int)$detail['id_order_detail']
            );
        }

        if (property_exists($this->context->smarty, 'inheritance_merge_compiled_includes')) {
            $this->context->smarty->inheritance_merge_compiled_includes = false;
        }

        $helper = new HelperView($this);

        $this->context->smarty->assign(array(
            'iem' => (int)$this->context->cookie->id_employee,
            'iemp' => $this->context->cookie->passwd,
            'orderedit_tpl_dir' => _PS_MODULE_DIR_.'/orderedit/views/templates/admin/_configure/order_edit',
            'ajax_path' => $this->context->link->getAdminLink('AdminModules', false).'&configure=orderedit&token='.Tools::getAdminTokenLite('AdminModules').'&ajax=true',
            'ORDEREDIT_HOOK_BEFORE_PRODUCT_LIST' => Hook::exec('orderEditBeforeProductList'),
            'ORDEREDIT_HOOK_TOP' => Hook::exec('orderEditTop'),
            'carriers' => $this->getCarriersList((int)Tools::getValue('id_order')),
            'taxes' => Tax::getTaxes($this->context->language->id, true),
            'pm' => $pm,
        ));

        require_once(_PS_MODULE_DIR_.'orderedit/orderedit.php');

        $helper->module = new OrderEdit();
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_view_vars;

        if (!is_null($this->base_tpl_view)) {
            $helper->base_tpl = $this->base_tpl_view;
        }

        $view = $helper->generateView();

        return $view;
    }

    public function getCarriersList($id_order)
    {
        $result = Carrier::getCarriers(
            $this->context->language->id,
            true,
            false,
            false,
            null,
            Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
        );

        $temp = array();
        foreach ($result as $car) {
            $temp[] = $car['id_carrier'];
        }

        if ($id_order) {
            $order = new Order((int)$id_order);
            $ships = $order->getShipping();
            foreach ($ships as $ship) {
                if (array_key_exists('id_carrier', $ship)
                    && $ship['id_carrier']
                    && !in_array($ship['id_carrier'], $temp)) {
                    $sql = 'SELECT c.*, cl.delay
                    FROM `'._DB_PREFIX_.'carrier` c
                    LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl
                    ON (c.`id_carrier` = cl.`id_carrier`
                        AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
                    WHERE c.`id_carrier` = '.$ship['id_carrier'];
                    $carrier = Db::getInstance()->getRow($sql);
                    $carrier['name'] .= $this->l(' (deleted)');
                    $result[] = $carrier;
                    $temp[] = $ship['id_carrier'];
                }
            }
        }

        return $result;
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);
        $this->page_header_toolbar_btn['back-to-list'] = array(
            'href' => $this->context->link->getAdminLink('AdminOrders'),
            'desc' => $this->l('Back to List'),
            'icon' => 'process-icon-back'
        );

        $this->page_header_toolbar_btn['cancel'] = array(
            'href' => $this->context->link->getAdminLink('AdminOrders').'&vieworder&id_order='.$obj->id,
            'desc' => $this->l('Cancel'),
            'icon' => 'process-icon-close'
        );

        $this->page_header_toolbar_btn['save-adcms'] = array(
            'js' => '$(\'button[name=ordereditOrderSave]\').click();',
            'desc' => $this->l('Save'),
            'icon' => 'process-icon-save'
        );

        if (Tools::getIsset('updateorder')) {
            return $this->renderView();
        }

        if (!$this->loadObject(true)) {
            return;
        }

        return parent::renderForm();
    }
}
