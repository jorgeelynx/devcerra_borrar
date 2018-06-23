<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from MigrationPro MMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the MigrationPro MMC is strictly forbidden.
 * In order to obtain a license, please contact us: migrationprommc@gmail.com
 *
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe MigrationPro MMC
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la MigrationPro MMC est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter la MigrationPro MMC a l'adresse: migrationprommc@gmail.com
 *
 *  @author    Edgar I.
 * @copyright Copyright (c) 2012-2016 MigrationPro MMC
 * @license   Commercial license
 * @package   MigrationPro: Prestashop To PrestaShop
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

@ini_set('max_execution_time', 0);
@ini_set('error_reporting', 1);
@ini_set('memory_limit', '-1');


require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/MigrationProMapping.php');
require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/MigrationProProcess.php');
require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/MigrationProData.php');
require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/MigrationProMigratedData.php');
require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/EDClient.php');
require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/EDQuery.php');
require_once(_PS_MODULE_DIR_ . 'migrationpro/classes/EDImport.php');

class MigrationPro extends Module
{
    protected $wizard_steps;

    public function __construct()
    {
        $this->name = 'migrationpro';
        $this->tab = 'migration_tools';
        $this->version = '3.2.0';
        $this->author = 'MigrationPro';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '9581b42794aee77c5be55b1342e37671';

        parent::__construct();

        $this->displayName = $this->l('MigrationPro: PrestaShop To PrestaShop Migration Tool');
        $this->description = $this->l('MigrationPro is an easy, fast and safe migration way of your data.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        // Prepare tab
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminMigrationPro';
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'MigrationPro';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;

        include(dirname(__FILE__) . '/sql/install.php');

        Configuration::updateValue('migrationpro_module_path', $this->local_path);

        if (!$tab->add() ||
            !parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('backOfficeHeader')
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');

        Configuration::deleteByName('migrationpro_module_path');

        $id_tab = (int)Tab::getIdFromClassName('AdminMigrationPro');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function initWizard()
    {
        $this->wizard_steps = array(
            'name'  => 'migrationpro_wizard',
            'steps' => array(
                array(
                    'title' => $this->l('Connection'),
                ),
                array(
                    'title' => $this->l('Configuration'),
                ),
                array(
                    'title' => $this->l('Migration'),
                )
            )
        );
    }

    // steps form fields
    public function renderStepOne()
    {
        $this->fields_form = array(
            'form' => array(
                'id_form' => 'step_migrationpro_connection',
                'legend'  => array(
                    'title' => $this->l('Start New Migration'),
                    'icon'  => 'icon-AdminTools'
                ),
                'input'   => array(
                    array(
                        'type'     => 'text',
                        'label'    => $this->l('Source Url'),
                        'name'     => 'source_shop_url',
                        'required' => true,
                        'hint'     => array(
                            $this->l('The source PrestaShop Url.'),
                            $this->l(
                                'Enter a valid URL. Protocol is required (http://, https:// or ftp://)'
                            )
                        )
                    ),
                    array(
                        'type'     => 'text',
                        'label'    => $this->l('Token'),
                        'name'     => 'source_shop_token',
                        'required' => true,
                        'hint'     => $this->l('The access token from source PrestaShop')
                    )
                )
            )
        );

        $fields_value = $this->getStepOneFieldsValues();

        return $this->renderGenericForm(array('form' => $this->fields_form), $fields_value);
    }

    public function renderStepTwo()
    {
        $mappings = MigrationProMapping::listMapping(true);
        if (empty($mappings)) {
            return false;
        }
        // Shops mapping
        $multiShopsInputs = array();

        foreach ($mappings['multi_shops'] as $key => $val) {
            $multiShopsInputs[] = array(
                'type'          => 'select',
                'label'         => $val['source_name'],
                'name'          => "map[multi_shops][$key]",
                'required'      => true,
                'options'       => array(
                    'query' => Shop::getShops(),
                    'id'    => 'id_shop',
                    'name'  => 'name'
                ),
//                'condition' => Shop::isFeatureActive(),
                'default_value' => Shop::getContextShopID(),
                'hint'          => $this->l('Select target shop, wich you want migrate this shop')
            );
        }

        $multiShops = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Shops'),
                    'icon'  => 'icon-shopping-cart'
                ),
                'input'   => $multiShopsInputs
            )
        );

        // Currencies Mapping
        $currenciesInputs = array();

        foreach ($mappings['currencies'] as $key => $val) {
            $currenciesInputs[] = array(
                'type'     => 'select',
                'label'    => $val['source_name'],
                'hint'     => $this->l(
                    'Select target Cart currencies properly Source Prestashop Store currencies. This is needed for Creating currencies in Target Prestashop Store'
                ),
                'name'     => "map[currencies][$key]",
                'required' => true,
                'options'  => array(
                    'query' => Currency::getCurrencies(),
                    'id'    => 'id_currency',
                    'name'  => 'name'
                ),
//                'default_value' => Currency::getCurrent()
            );
        }

        $currencies = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Currencies'),
                    'icon'  => 'icon-money'
                ),
                'input'   => $currenciesInputs
            )
        );

        // Languages Mapping
        $languagesInputs = array();

        foreach ($mappings['languages'] as $key => $val) {
            $languagesInputs[] = array(
                'type'     => 'select',
                'label'    => $val['source_name'],
                'hint'     => $this->l(
                    'Select target Cart languages properly source Prestashop Store languages. This is needed for Creating languages in Target Prestashop Store.'
                ),
                'name'     => "map[languages][$key]",
                'required' => true,
                'options'  => array(
                    'query' => array_merge(
                        array(array('id_lang' => 0, 'name' => 'none')),
                        Language::getLanguages()
                    ),
                    'id'    => 'id_lang',
                    'name'  => 'name'
                ),
            );
        }

        $languages = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Languages'),
                    'icon'  => 'icon-AdminParentLocalization'
                ),
                'input'   => $languagesInputs
            )
        );


        // Orders Status Mapping
        $ordersStatusInputs = array();

        foreach ($mappings['order_states'] as $key => $val) {
            $ordersStatusInputs[] = array(
                'type'     => 'select',
                'label'    => $val['source_name'],
                'hint'     => $this->l(
                    'Select target Cart order status properly sorce Prestashop Store order status. This is needed for Creating order status in Target Prestashop Store.'
                ),
                'name'     => "map[order_states][$key]",
                'required' => true,
                'options'  => array(
                    'query' => OrderState::getOrderStates($this->context->language->id),
                    'id'    => 'id_order_state',
                    'name'  => 'name'
                ),
            );
        }

        $ordersStatus = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Order status'),
                    'icon'  => 'icon-time'
                ),
                'input'   => $ordersStatusInputs
            )
        );


        // Customer Groups Mapping
        $customerGroupInputs = array();

        foreach ($mappings['customer_groups'] as $key => $val) {
            $customerGroupInputs[] = array(
                'type'     => 'select',
                'label'    => $val['source_name'],
                'hint'     => $this->l(
                    'Select target Cart Customer Group properly source Prestashop Store Customer Group. This is needed for Creating Customer Group in Target Prestashop Store.'
                ),
                'name'     => "map[customer_groups][$key]",
                'required' => true,
                'options'  => array(
                    'query' => Group::getGroups($this->context->language->id),
                    'id'    => 'id_group',
                    'name'  => 'name'
                ),
            );
        }

        $customerGroup = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Customer Groups'),
                    'icon'  => 'icon-AdminParentCustomer'
                ),
                'input'   => $customerGroupInputs
            )
        );

        //entities to migrate
        $entitiesToMigrate = array(
            array(
                'type'    => 'switch',
                'label'   => $this->l("Select All"),
                'hint'    => $this->l('Select All Boxes'),
                'name'    => 'entities_select_all',
                'id'      => 'entities_select_all',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => $this->l("Taxes"),
                'hint'    => $this->l('For migrate Taxes enable this option'),
                'name'    => 'entities_taxes',
                'id'      => 'entities_taxes',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Manufacturers",
                'hint'    => $this->l('For migrate Manufacturers enable this option'),
                'name'    => 'entities_manufacturers',
                'id'      => 'entities_manufacturers',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Categories",
                'hint'    => $this->l('For migrate Categories enable this option'),
                'name'    => 'entities_categories',
                'id'      => 'entities_categories',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Carriers",
                'hint'    => $this->l('For migrate Carriers enable this option'),
                'name'    => 'entities_carriers',
                'id'      => 'entities_carriers',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
//            array(
//                'type'    => 'switch',
//                'label'   => "Warehouse",
//                'hint'    => $this->l('For migrate WareHouse enable this option'),
//                'name'    => 'entities_warehouse',
//                'id'      => 'entities_warehouse',
//                'is_bool' => true,
//                'values'  => array(
//                    array(
//                        'id'    => 'active_on',
//                        'value' => true,
//                        'label' => $this->l('Enabled')
//                    ),
//                    array(
//                        'id'    => 'active_off',
//                        'value' => false,
//                        'label' => $this->l('Disabled')
//                    )
//                )
//
//            ),
            array(
                'type'    => 'switch',
                'label'   => "Products",
                'hint'    => $this->l('For migrate Products enable this option'),
                'name'    => 'entities_products',
                'id'      => 'entities_products',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Catalog price rules",
                'hint'    => $this->l('For migrate Catalog price rules enable this option'),
                'name'    => 'entities_catalog_price_rules',
                'id'      => 'entities_catalog_price_rules',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Employees",
                'hint'    => $this->l('For migrate Employees enable this option'),
                'name'    => 'entities_employees',
                'id'      => 'entities_employees',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Customers",
                'hint'    => $this->l('For migrate Customers enable this option'),
                'name'    => 'entities_customers',
                'id'      => 'entities_customers',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Cart rules",
                'hint'    => $this->l('For migrate Cart rules enable this option'),
                'name'    => 'entities_cart_rules',
                'id'      => 'entities_cart_rules',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Orders",
                'hint'    => $this->l('For migrate Orders enable this option'),
                'name'    => 'entities_orders',
                'id'      => 'entities_orders',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "CMS",
                'hint'    => $this->l('For migrate CMS enable this option'),
                'name'    => 'entities_cms',
                'id'      => 'entities_cms',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "SEO",
                'hint'    => $this->l('For migrate SEO enable this option'),
                'name'    => 'entities_metas',
                'id'      => 'entities_metas',
                'is_bool' => true,
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            )
        );

        $entitiesToMigrate = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Data to Migrate'),
                    'icon'  => 'icon-AdminCatalog'
                ),
                'input'   => $entitiesToMigrate
            )
        );

        // Additional Options

        $advancedOptionsArray = array(
            array(
                'type'    => 'switch',
                'label'   => $this->l("Select All"),
                'hint'    => $this->l('Select All Boxes'),
                'name'    => 'force_select_all',
                'id'      => 'force_select_all',
                'is_bool' => true,
                'desc'    => $this->l('You can select all options if you want'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => $this->l("Clean Target"),
                'hint'    => $this->l('This feature delete all informations of target shop before Migration'),
                'name'    => 'clear_data',
                'id'      => 'clear_data',
                'is_bool' => true,
                'desc'    => $this->l('Delete target shops all information'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep Manufacturers ID",
                'hint'    => $this->l('if there is no another problem, we recommend keep old Manufacturers ID'),
                'name'    => 'force_manufacturer_ids',
                'id'      => 'force_manufacturer_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old Manufacturers ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )
            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep Categories ID",
                'hint'    => $this->l('If you want keep SEO of your old Shop, we recomend keep old Categories Id'),
                'name'    => 'force_category_ids',
                'id'      => 'force_category_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old Categories ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )
            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep Carriers ID",
                'hint'    => $this->l('if there is no another problem, we recommend keep old Carriers ID'),
                'name'    => 'force_carrier_ids',
                'id'      => 'force_carrier_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old Carriers ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )
            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep Products ID",
                'hint'    => $this->l('If you want keep SEO of your old Shop, we recomend keep old Products Id'),
                'name'    => 'force_product_ids',
                'id'      => 'force_product_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old Products ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep Catalog price rules ID",
                'hint'    => $this->l('If you want keep SEO of your old Shop, we recomend keep old Catalog price rules Id'),
                'name'    => 'force_catalogPriceRule_ids',
                'id'      => 'force_catalogPriceRule_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old Catalog price rules ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep Customers ID",
                'hint'    => $this->l('if there is no another problem, we recommend keep old Customers ID'),
                'name'    => 'force_customer_ids',
                'id'      => 'force_customer_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old Customers ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )

            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep Orders ID",
                'hint'    => $this->l('if there is no another problem, we recommend keep old Orders ID'),
                'name'    => 'force_order_ids',
                'id'      => 'force_order_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old Orders ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )
            ),
            array(
                'type'    => 'switch',
                'label'   => "Keep CMS ID",
                'hint'    => $this->l('if there is no another problem, we recommend keep old CMS ID'),
                'name'    => 'force_cms_ids',
                'id'      => 'force_cms_ids',
                'is_bool' => true,
                'desc'    => $this->l('This option keep old CMS ID on the new Store'),
                'values'  => array(
                    array(
                        'id'    => 'active_on',
                        'value' => true,
                        'label' => $this->l('Enabled')
                    ),
                    array(
                        'id'    => 'active_off',
                        'value' => false,
                        'label' => $this->l('Disabled')
                    )
                )
            )
        );

        $advancedOptions = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Utility Options'),
                    'icon'  => 'icon-AdminTools'
                ),
                'input'   => $advancedOptionsArray
            )
        );

        $additionalOptionInputs = array();

        $additionalOptionInputs[] = array(
            'type'    => 'switch',
            'label'   => "Data Validation",
            'hint'    => $this->l('If this feature disabled the module will migarte only the valid data proper to target PrestaShop'),
            'name'    => 'ps_validation_errors',
            'id'      => 'ps_validation_errors',
            'is_bool' => true,
            'desc'    => $this->l('Each PrestaShop version have different Validation rules. 
                                       If you Want Migrate Only the Valid Data proper to Target PS select this feature to off. 
                                       If you want All of your data turn on this feature then the module give you the Validtion Errors
                                       then you need to correct them from source manualy'),
            'values'  => array(
                array(
                    'id'    => 'active_on',
                    'value' => true,
                    'label' => $this->l('Enabled')
                ),
                array(
                    'id'    => 'active_off',
                    'value' => false,
                    'label' => $this->l('Disabled')
                )
            )
        );

//        $additionalOptionInputs[] = array(
//                'type'    => 'switch',
//                'label'   => $this->l("Migrate recent data"),
//                'hint'    => $this->l('This feature migrates data that don\'t exists in the target'),
//                'name'    => 'migrate_recent_data',
//                'id'      => 'migrate_recent_data',
//                'is_bool' => true,
//                'desc'    => $this->l('Migrate only recent data (works only after first migration)'),
//                'values'  => array(
//                    array(
//                        'id'    => 'active_on',
//                        'value' => true,
//                        'label' => $this->l('Enabled')
//                    ),
//                    array(
//                        'id'    => 'active_off',
//                        'value' => false,
//                        'label' => $this->l('Disabled')
//                    )
//                )
//
//            );

        $additionalOptions = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Additional Options'),
                    'icon'  => 'icon-AdminTools'
                ),
                'input'   => $additionalOptionInputs
            )
        );

        $speedOptionsInputs = array();
        $speedOptionsInputs[] = array(
            'type'  => 'text',
            'label' => $this->l('Select Migration Speed:'),
            'name'  => "speed",
            'hint'  => $this->l(
                'You can select your migration speed with this option, by default this options equal to normal, if you want use this options we recommend consider your servers power.'
            ),
            'id'    => 'input_speed_range_slider',
        );

        $speedOptions = array(
            'form' => array(
                'id_form' => 'step_migrationpro_configuration',
                'legend'  => array(
                    'title' => $this->l('Advanced Options'),
                    'icon'  => 'icon-AdminTools'
                ),
                'input'   => $speedOptionsInputs
            )
        );


        $fields_value = $this->getStepTwoFieldsValues();

        return $this->renderGenericForm(
            array(
                $multiShops,
                $currencies,
                $languages,
                $ordersStatus,
                $customerGroup,
                $entitiesToMigrate,
                $advancedOptions,
                $additionalOptions,
                $speedOptions
            ),
            $fields_value
        );
    }

    public function renderStepThree()
    {
        if (count($lastExecutingProcesses = MigrationProProcess::getAll())) {
            for ($i = 0; $i <= count($lastExecutingProcesses) - 1; $i++) {
                if ($lastExecutingProcesses[$i]['type'] == 'cart_rules') {
                    unset($lastExecutingProcesses[$i]);
                }
            }
            $this->context->smarty->assign('processes', $lastExecutingProcesses);
        }

        $this->context->smarty->assign(array(
            'percent' => MigrationProProcess::calculateImportedDataPercent(),
        ));

        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/view.tpl');

        return $output;
    }

    // form fields values
    public function getStepOneFieldsValues()
    {
        return array(
            'source_shop_url'   => Configuration::get($this->name . '_url'),
            'source_shop_token' => Configuration::get($this->name . '_token'),
        );
    }

    public function getStepTwoFieldsValues()
    {
        $mappings = MigrationProMapping::listMapping();

        $fieldValues = array(
            'entities_taxes'               => 0,
            'entities_manufacturers'       => 0,
            'entities_categories'          => 0,
            'entities_carriers'            => 0,
            'entities_products'            => 0,
            'entities_warehouse'           => 0,
            'entities_catalog_price_rules' => 0,
            'entities_employees'           => 0,
            'entities_customers'           => 0,
            'entities_cart_rules'          => 0,
            'entities_orders'              => 0,
            'entities_cms'                 => 0,
            'entities_metas'               => 0,
            'entities_select_all'          => 0,
            'force_category_ids'           => 0,
            'force_carrier_ids'            => 0,
            'force_product_ids'            => 0,
            'force_catalogPriceRule_ids'   => 0,
            'force_customer_ids'           => 0,
            'force_cartRule_ids'           => 0,
            'clear_data'                   => 0,
            'force_order_ids'              => 0,
            'force_manufacturer_ids'       => 0,
            'force_cms_ids'                => 0,
            'force_select_all'             => 0,
            'migrate_recent_data'          => 0,
            'ps_validation_errors'         => 0,
            'speed'                        => 'Normal'
        );
        if (!empty($mappings)) {
            foreach ($mappings as $map) {
                $fieldValues['map[' . $map['type'] . '][' . $map['id_mapping'] . ']'] = $map['local_id'];
            }
        }

        return $fieldValues;
    }

    // form fields value validation

    // helper functions for form
    public function renderGenericForm($fields_form, $fields_value, $tpl_vars = array())
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->tpl_vars = array_merge(
            array(
                'fields_value' => $fields_value,
                'languages'    => $this->context->controller->getLanguages(),
                'id_language'  => $this->context->language->id
            ),
            $tpl_vars
        );

        return $helper->generateForm($fields_form);
    }

    public function getFieldValue($key)
    {
        return Tools::getValue($key);
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitMigrationproModule')) == true) {
            $this->postProcess();
        }

        $this->initWizard();
        $this->context->smarty->assign('module_dir', $this->_path);
        $howToIntroduction = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');

        $this->context->smarty->assign(
            array(
                'wizard_steps'      => $this->wizard_steps,
                'validate_url'      => $this->context->link->getAdminLink('AdminMigrationPro'),
                'multistore_enable' => Shop::isFeatureActive(),
                'wizard_contents'   => array(
                    'contents' => array(
                        0 => $howToIntroduction . $this->renderStepOne(),
                        1 => $this->renderStepTwo(),
                        2 => ''
                    )
                ),
                'labels'            => array(
                    'next'     => $this->l('Next'),
                    'previous' => $this->l(
                        'Previous'
                    ),
                    'finish'   => $this->l('Migrate')
                )
            )
        );
        $output = '';
        $processObject = MigrationProProcess::getActiveProcessObject();
        if (Validate::isLoadedObject($processObject) && $lastExecutingProcesses = MigrationProProcess::getAll()) {
            if (count($lastExecutingProcesses = MigrationProProcess::getAll())) {
                $this->context->smarty->assign('processes', $lastExecutingProcesses);
            }
//            $this->context->smarty->assign('processes', $lastExecutingProcesses);
        }

        $this->context->smarty->assign(
            array(
                'percent' => MigrationProProcess::calculateImportedDataPercent(),
            )
        );

        $output .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/wizard.tpl');

        return $output;//.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMigrationproModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon'  => 'icon-cogs',
                ),
                'input'  => array(
                    array(
                        'type'    => 'switch',
                        'label'   => $this->l('Live mode'),
                        'name'    => 'MIGRATIONPRO_LIVE_MODE',
                        'is_bool' => true,
                        'desc'    => $this->l('Use this module in live mode'),
                        'values'  => array(
                            array(
                                'id'    => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id'    => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col'    => 3,
                        'type'   => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc'   => $this->l('Enter a valid email address'),
                        'name'   => 'MIGRATIONPRO_ACCOUNT_EMAIL',
                        'label'  => $this->l('Email'),
                    ),
                    array(
                        'type'  => 'password',
                        'name'  => 'MIGRATIONPRO_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'MIGRATIONPRO_LIVE_MODE'        => Configuration::get('MIGRATIONPRO_LIVE_MODE', true),
            'MIGRATIONPRO_ACCOUNT_EMAIL'    => Configuration::get(
                'MIGRATIONPRO_ACCOUNT_EMAIL',
                'contact@prestashop.com'
            ),
            'MIGRATIONPRO_ACCOUNT_PASSWORD' => Configuration::get('MIGRATIONPRO_ACCOUNT_PASSWORD', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path . '/views/js/migrationpro_wizard.js');
            $this->context->controller->addJS($this->_path . '/views/js/smartWizardMigrationPro.js');
            $this->context->controller->addJS($this->_path . '/views/js/rangeslider/ion.rangeSlider.min.js');
            $this->context->controller->addJqueryPlugin('typewatch');
            $this->context->controller->addCSS($this->_path . 'views/css/migrationpro.css');
            $this->context->controller->addCSS($this->_path . 'views/css/rangeslider/ion.rangeSlider.css');
            $this->context->controller->addCSS($this->_path . 'views/css/rangeslider/ion.rangeSlider.skinModern.css');
        }
    }
}
