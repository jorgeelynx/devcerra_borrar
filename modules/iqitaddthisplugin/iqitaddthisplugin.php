<?php
/**
 * 2017 IQIT-COMMERCE.COM
 *
 * NOTICE OF LICENSE
 *
 *  @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
 *  @copyright 2017 IQIT-COMMERCE.COM
 *  @license   Commercial license (You can not resell or redistribute this software.)
 *
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class IqitAddthisPlugin extends Module implements WidgetInterface
{
    protected $templateFile;

    public function __construct()
    {
        $this->name = 'iqitaddthisplugin';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'IQIT-COMMERCE.COM';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('IQITADDTHISPLUGIN - Addthis plugin on product page');
        $this->description = $this->l('Show Addthis plugin on product page');

        $this->templateFile = 'module:'.$this->name.'/views/templates/hook/'.$this->name.'.tpl';
    }

    public function install()
    {
        $this->_clearCache($this->templateFile);
        return (parent::install() and Configuration::updateValue('addthisplugin_id', '0') &&
            $this->registerHook('displayProductAdditionalInfo'));
    }

    public function uninstall()
    {
        return (Configuration::deleteByName('addthisplugin_id') and parent::uninstall());
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitModule')) {
            Configuration::updateValue('addthisplugin_id', Tools::getValue('addthisplugin_id'));
            $output .= $this->displayConfirmation($this->l('Configuration updated'));
            $this->_clearCache($this->templateFile);
        }

        $output .= $this->renderForm();

        return $output;
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs'
                ),
                'description' =>
                $this->l('To use own statistic of social sharing, you need to create account on') . ' <a target="_blank" href="https://www.addthis.com/"><strong>' .
                $this->l('Addthis.com Site') . '</strong></a> ' . $this->l('Then create free inline sharing tool buttons and copy widget id which should have format like ra-50d44b832bee7204'),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Addthis ID:'),
                        'name' => 'addthisplugin_id',
                        'desc' => $this->l('Input your own Addthis id ex.: ra-50d44b832bee7204 for analitycs'),
                    ),
                ),
                'submit' => array(
                    'name' => 'submitModule',
                    'title' => $this->l('Save'),
                ),
            ),
        );

        if (Shop::isFeatureActive()) {
            $fields_form['form']['description'] = $this->l('The modifications will be applied to') . ' ' . (Shop::getContext() == Shop::CONTEXT_SHOP ? $this->l('shop') . ' ' . $this->context->shop->name : $this->l('all shops'));
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'addthisplugin_id' => Tools::getValue('addthisplugin_id', Configuration::get('addthisplugin_id')),
        );
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        if (!$this->isCached($this->templateFile, $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId());
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        return array(
            'addthisplugin_id' => Configuration::get('addthisplugin_id'),
        );
    }
}
