<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
  exit;
 
class Wpintops extends Module
{
    public $output;
    public $input_type;
    public $wordpress_data;
    
    public function __construct()
    {
        $this->name = 'wpintops';
        $this->tab = 'front_office_features';
        $this->version = '1.7.6';
        $this->author = 'Jose Aguilar';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = "3c534c52d10808b5ea946f6f7e69c286";
        
        if (version_compare(_PS_VERSION_, '1.6', '<')) 
            $this->input_type = 'radio';
        else
            $this->input_type = 'switch';

        parent::__construct();

        $this->displayName = $this->l('WordPress Inside');
        $this->description = $this->l('Wordpress into Prestashop.');
        
        require_once dirname(__FILE__).'/classes/WordpressData.php';
        $this->wordpress_data = new WordpressData();
    }
  
    public function install() {
        Configuration::updateValue('WPINTOPS_SERVER', 'localhost');
        Configuration::updateValue('WPINTOPS_BDUSER', '');
        Configuration::updateValue('WPINTOPS_BDPASS', '');
        Configuration::updateValue('WPINTOPS_BDNAME', '');
        Configuration::updateValue('WPINTOPS_BDNAMEPREFIX', '');
        Configuration::updateValue('WPINTOPS_WPURL', '');
        Configuration::updateValue('WPINTOPS_HOMEPAGE', 1);
        Configuration::updateValue('WPINTOPS_LEFTCOLUMN', 0);
        Configuration::updateValue('WPINTOPS_RIHTCOLUMN', 0);
        Configuration::updateValue('WPINTOPS_FOOTER', 0);
        Configuration::updateValue('WPINTOPS_ORDER', 'DESC');
        Configuration::updateValue('WPINTOPS_ORDERBY', 'post_date');
        Configuration::updateValue('WPINTOPS_SHOWPOSTCATEGORY', 0);
        Configuration::updateValue('WPINTOPS_SHOWTITLE', 1);
        Configuration::updateValue('WPINTOPS_SHOWPOSTDATE', 0);
        Configuration::updateValue('WPINTOPS_SHOWEXCERPT', 0);
        Configuration::updateValue('WPINTOPS_SHOWIMAGE', 1);
        Configuration::updateValue('WPINTOPS_THUMBNAIL', 'thumbnail');
        
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            Configuration::updateValue('WPINTOPS_POSTSLIMITS', 8);
            Configuration::updateValue('WPINTOPS_NB', 4);
            Configuration::updateValue('WPINTOPS_IMAGEWIDTH', 126);
            Configuration::updateValue('WPINTOPS_IMAGEHEIGHT', 126);
        }
        else {
            Configuration::updateValue('WPINTOPS_POSTSLIMITS', 12);
            Configuration::updateValue('WPINTOPS_NB', 6);
            Configuration::updateValue('WPINTOPS_IMAGEWIDTH', 150);
            Configuration::updateValue('WPINTOPS_IMAGEHEIGHT', 150);
        }
        
        Configuration::updateValue('WPINTOPS_TARGETBLANK', 1);
        Configuration::updateValue('WPINTOPS_CATEGORY', '');
        Configuration::updateValue('WPINTOPS_FRIENDLY_URL', 0);
        
        
        return parent::install() && 
                $this->registerHook('displayLeftColumn') && 
                $this->registerHook('displayRightColumn') &&
                $this->registerHook('displayHome') &&
                $this->registerHook('displayFooter') &&
                $this->registerHook('displayHeader');
    }
  
    public function uninstall() {
        Configuration::deleteByName('WPINTOPS_SERVER');
        Configuration::deleteByName('WPINTOPS_BDUSER');
        Configuration::deleteByName('WPINTOPS_BDPASS');
        Configuration::deleteByName('WPINTOPS_BDNAME');
        Configuration::deleteByName('WPINTOPS_BDNAMEPREFIX');
        Configuration::deleteByName('WPINTOPS_WPURL');
        Configuration::deleteByName('WPINTOPS_HOMEPAGE');
        Configuration::deleteByName('WPINTOPS_LEFTCOLUMN');
        Configuration::deleteByName('WPINTOPS_RIGHTCOLUMN');
        Configuration::deleteByName('WPINTOPS_FOOTER');
        Configuration::deleteByName('WPINTOPS_POSTSLIMITS');
        Configuration::deleteByName('WPINTOPS_ORDER');
        Configuration::deleteByName('WPINTOPS_ORDERBY');
        Configuration::deleteByName('WPINTOPS_SHOWPOSTCATEGORY');
        Configuration::deleteByName('WPINTOPS_SHOWTITLE');
        Configuration::deleteByName('WPINTOPS_SHOWPOSTDATE');
        Configuration::deleteByName('WPINTOPS_SHOWEXCERPT');
        Configuration::deleteByName('WPINTOPS_SHOWIMAGE');
        Configuration::deleteByName('WPINTOPS_THUMBNAIL');
        Configuration::deleteByName('WPINTOPS_IMAGEWIDTH');
        Configuration::deleteByName('WPINTOPS_IMAGEHEIGHT');
        Configuration::deleteByName('WPINTOPS_TARGETBLANK');
        Configuration::deleteByName('WPINTOPS_CATEGORY');
        Configuration::deleteByName('WPINTOPS_FRIENDLY_URL');
        Configuration::deleteByName('WPINTOPS_NB');
        
        if (!parent::uninstall())
            return false;
        return true;
    }
    
    public function postProcess() {
        if (Tools::isSubmit('submitDataConnexion')) {
            Configuration::updateValue('WPINTOPS_SERVER', Tools::getValue('WPINTOPS_SERVER'));
            Configuration::updateValue('WPINTOPS_BDUSER', Tools::getValue('WPINTOPS_BDUSER'));
            Configuration::updateValue('WPINTOPS_BDPASS', Tools::getValue('WPINTOPS_BDPASS'));
            Configuration::updateValue('WPINTOPS_BDNAME', Tools::getValue('WPINTOPS_BDNAME'));
            Configuration::updateValue('WPINTOPS_BDNAMEPREFIX', Tools::getValue('WPINTOPS_BDNAMEPREFIX'));
            Configuration::updateValue('WPINTOPS_WPURL', Tools::getValue('WPINTOPS_WPURL'));
            
            $this->wordpress_data = new WordpressData();    
            
            if ($this->wordpress_data->existTable()) {
                if ($this->wordpress_data->conexion->connect_errno == 0 && Configuration::get('WPINTOPS_WPURL') != '')
                    $this->output .= $this->displayConfirmation($this->l('The data connexion was successfully added.'));
                else
                    $this->output .= $this->displayError(utf8_encode($this->wordpress_data->conexion->connect_error));
            }
            else {
                $this->output .= $this->displayError($this->l('The data base prefix is incorrect.'));
            }
	}
        
        if (Tools::isSubmit('submitSettings')) {   
            Configuration::updateValue('WPINTOPS_HOMEPAGE', Tools::getValue('WPINTOPS_HOMEPAGE'));
            Configuration::updateValue('WPINTOPS_LEFTCOLUMN', Tools::getValue('WPINTOPS_LEFTCOLUMN'));
            Configuration::updateValue('WPINTOPS_RIGHTCOLUMN', Tools::getValue('WPINTOPS_RIGHTCOLUMN'));
            Configuration::updateValue('WPINTOPS_FOOTER', Tools::getValue('WPINTOPS_FOOTER'));
            Configuration::updateValue('WPINTOPS_POSTSLIMITS', Tools::getValue('WPINTOPS_POSTSLIMITS'));
            Configuration::updateValue('WPINTOPS_ORDER', Tools::getValue('WPINTOPS_ORDER'));
            Configuration::updateValue('WPINTOPS_ORDERBY', Tools::getValue('WPINTOPS_ORDERBY'));
            Configuration::updateValue('WPINTOPS_SHOWPOSTCATEGORY', Tools::getValue('WPINTOPS_SHOWPOSTCATEGORY'));
            Configuration::updateValue('WPINTOPS_SHOWTITLE', Tools::getValue('WPINTOPS_SHOWTITLE'));
            Configuration::updateValue('WPINTOPS_SHOWPOSTDATE', Tools::getValue('WPINTOPS_SHOWPOSTDATE'));
            Configuration::updateValue('WPINTOPS_SHOWEXCERPT', Tools::getValue('WPINTOPS_SHOWEXCERPT'));
            Configuration::updateValue('WPINTOPS_THUMBNAIL', Tools::getValue('WPINTOPS_THUMBNAIL'));
            Configuration::updateValue('WPINTOPS_IMAGEWIDTH', Tools::getValue('WPINTOPS_IMAGEWIDTH'));
            Configuration::updateValue('WPINTOPS_IMAGEHEIGHT', Tools::getValue('WPINTOPS_IMAGEHEIGHT'));
            Configuration::updateValue('WPINTOPS_TARGETBLANK', Tools::getValue('WPINTOPS_TARGETBLANK'));
            Configuration::updateValue('WPINTOPS_CATEGORY', Tools::getValue('WPINTOPS_CATEGORY'));            
            Configuration::updateValue('WPINTOPS_FRIENDLY_URL', Tools::getValue('WPINTOPS_FRIENDLY_URL'));
            Configuration::updateValue('WPINTOPS_NB', Tools::getValue('WPINTOPS_NB'));
            $this->output .= $this->displayConfirmation($this->l('The settings was successfully added.'));	
	}
    }
    
    public function getContent() {     
        
        $this->postProcess();
        
        if ($this->wordpress_data->conexion->connect_errno == 0 && Configuration::get('WPINTOPS_WPURL') != '' && $this->wordpress_data->existTable()) {
            $this->output .= $this->displayInformation();
            $this->output .= $this->displayFormSettings();
        } 
        else
            $this->output .= $this->displayFormConnect();
        
        return $this->output;
    }
    
    public function displayFormConnect() {
        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language)
            $languages[$k]['is_default'] = (int)$language['id_lang'] == Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'wpintops';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $languages;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = true;
        $helper->toolbar_scroll = false;
        //$helper->toolbar_btn = $this->initToolbar();
        $helper->title = $this->displayName.' '.$this->l('version').' '.$this->version;
        $helper->submit_action = 'submitDataConnexion';

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Connexion Wordpress'),
                'image' => $this->_path.'logo.gif'
            ),
            
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Server'),
                    'name' => 'WPINTOPS_SERVER',
                    'col' => 4,
                    'required' => true,
                    'desc' => $this->l('For example: localhost, your ip or domain.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Database user'),
                    'name' => 'WPINTOPS_BDUSER',
                    'col' => 4,
                    'required' => true,
                ),
                array(
                    'type' => 'password',
                    'label' => $this->l('Database password'),
                    'name' => 'WPINTOPS_BDPASS',
                    'col' => 4,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Database name'),
                    'name' => 'WPINTOPS_BDNAME',
                    'col' => 4,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Database table prefix'),
                    'name' => 'WPINTOPS_BDNAMEPREFIX',
                    'col' => 4,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Wordpress URL'),
                    'name' => 'WPINTOPS_WPURL',
                    'col' => 4,
                    'required' => true,
                ),
                
            ),
            'submit' => array(
                'name' => 'submitDataConnexion',
                'title' => $this->l('Save'),
            ),
        );
        
        $helper->fields_value['WPINTOPS_SERVER'] = Configuration::get('WPINTOPS_SERVER');
        $helper->fields_value['WPINTOPS_BDUSER'] = Configuration::get('WPINTOPS_BDUSER');
        $helper->fields_value['WPINTOPS_BDPASS'] = Configuration::get('WPINTOPS_BDPASS');
        $helper->fields_value['WPINTOPS_BDNAME'] = Configuration::get('WPINTOPS_BDNAME');
        $helper->fields_value['WPINTOPS_BDNAMEPREFIX'] = Configuration::get('WPINTOPS_BDNAMEPREFIX');
        $helper->fields_value['WPINTOPS_WPURL'] = Configuration::get('WPINTOPS_WPURL');
        
        $mysqli_not_exist = '';
        if (!$this->wordpress_data->mysqli_exist()) 
            $mysqli_not_exist .= $this->displayError($this->l('You must install extension MySQLi in your server. Contact with your hosting service.'));

        return $mysqli_not_exist.$helper->generateForm($this->fields_form);
    }
    
    public function displayInformation() {
        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'name' => $this->displayName, 
            'version' => $this->version, 
            'description' => $this->description,
            'iso_code' => $this->context->language->iso_code,
        ));
        
	return $this->context->smarty->fetch($this->local_path.'views/templates/admin/information.tpl');
    }
    
    public function displayFormSettings() {
        
        $this->wordpress_data->fillSizes();
        
        $this->orderby = array(
            array(
                'name' => 'post_title'
            ),
            array(
                'name' => 'ID'
            ),
            array(
                'name' => 'post_date'
            ),
            /*array(
                'name' => 'RANDOM()'
            ),*/
        );
        
        $this->order = array(
            array(
                'name' => 'DESC'
            ),
            array(
                'name' => 'ASC'
            ),
        );
        
        $categories = $this->wordpress_data->getCategories();
        
        $languages = Language::getLanguages(false);
        foreach ($languages as $k => $language)
            $languages[$k]['is_default'] = (int)$language['id_lang'] == Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'wpintops';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $languages;
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = true;
        $helper->toolbar_scroll = false;
        //$helper->toolbar_btn = $this->initToolbar();
        $helper->title = $this->displayName.' '.$this->l('version').' '.$this->version;
        $helper->submit_action = 'submitSettings';

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Settings'),
                'image' => $this->_path.'logo.gif'
            ),
            'input' => array(
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show in homepage?'),
                    'name' => 'WPINTOPS_HOMEPAGE',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show in left column?'),
                    'name' => 'WPINTOPS_LEFTCOLUMN',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show in right column?'),
                    'name' => 'WPINTOPS_RIGHTCOLUMN',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show in footer?'),
                    'name' => 'WPINTOPS_FOOTER',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show posts of category'),
                    'name' => 'WPINTOPS_SHOWPOSTCATEGORY',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('You can display posts from one category or all categories.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
		  'type' => 'select',
		  'label' => $this->l('Select Category posts'),
		  'name' => 'WPINTOPS_CATEGORY',
		  'required' => false,
		  'options' => array(
			'query' => $categories,
			'id' => 'term_id',
			'name' => 'name'
		  )
		),
                array(
                    'type' => 'text',
                    'label' => $this->l('Posts limit'),
                    'name' => 'WPINTOPS_POSTSLIMITS',
                    'col' => 2,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Number items by line in homepage'),
                    'name' => 'WPINTOPS_NB',
                    'col' => 2,
                    'required' => true,
                ),
                array(
		  'type' => 'select',
		  'label' => $this->l('Order by'),
		  'name' => 'WPINTOPS_ORDERBY',
		  'required' => false,
		  'options' => array(
			'query' => $this->orderby,
			'id' => 'name',
			'name' => 'name'
		  )
		),
                array(
		  'type' => 'select',
		  'label' => $this->l('Order format'),
		  'name' => 'WPINTOPS_ORDER',
		  'required' => false,
		  'options' => array(
			'query' => $this->order,
			'id' => 'name',
			'name' => 'name'
		  )
		),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show title'),
                    'name' => 'WPINTOPS_SHOWTITLE',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Only in home.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show post date'),
                    'name' => 'WPINTOPS_SHOWPOSTDATE',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Only in home.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Show excerpt'),
                    'name' => 'WPINTOPS_SHOWEXCERPT',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'desc' => $this->l('Only in home.'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
		  'type' => 'select',
		  'label' => $this->l('Thumbnail type'),
		  'name' => 'WPINTOPS_THUMBNAIL',
		  'required' => false,
		  'options' => array(
			'query' => $this->wordpress_data->img_sizes,
			'id' => 'name',
			'name' => 'name'
		  )
		),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image width'),
                    'name' => 'WPINTOPS_IMAGEWIDTH',
                    'col' => 2,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image height'),
                    'name' => 'WPINTOPS_IMAGEHEIGHT',
                    'col' => 2,
                    'required' => true,
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('URL Friendly'),
                    'name' => 'WPINTOPS_FRIENDLY_URL',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
                array(
                    'type' => $this->input_type,
                    'label' => $this->l('Target blank'),
                    'name' => 'WPINTOPS_TARGETBLANK',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
            ),
            
            'submit' => array(
                'name' => 'submitSettings',
                'title' => $this->l('Save'),
            ),
        );
        
        $helper->fields_value['WPINTOPS_HOMEPAGE'] = Configuration::get('WPINTOPS_HOMEPAGE');
        $helper->fields_value['WPINTOPS_LEFTCOLUMN'] = Configuration::get('WPINTOPS_LEFTCOLUMN');
        $helper->fields_value['WPINTOPS_RIGHTCOLUMN'] = Configuration::get('WPINTOPS_RIGHTCOLUMN');
        $helper->fields_value['WPINTOPS_FOOTER'] = Configuration::get('WPINTOPS_FOOTER');
        $helper->fields_value['WPINTOPS_POSTSLIMITS'] = Configuration::get('WPINTOPS_POSTSLIMITS');
        $helper->fields_value['WPINTOPS_ORDERBY'] = Configuration::get('WPINTOPS_ORDERBY');
        $helper->fields_value['WPINTOPS_ORDER'] = Configuration::get('WPINTOPS_ORDER');
        $helper->fields_value['WPINTOPS_NB'] = Configuration::get('WPINTOPS_NB');
        $helper->fields_value['WPINTOPS_SHOWPOSTCATEGORY'] = Configuration::get('WPINTOPS_SHOWPOSTCATEGORY');
        $helper->fields_value['WPINTOPS_CATEGORY'] = Configuration::get('WPINTOPS_CATEGORY');
        $helper->fields_value['WPINTOPS_SHOWTITLE'] = Configuration::get('WPINTOPS_SHOWTITLE');
        $helper->fields_value['WPINTOPS_SHOWPOSTDATE'] = Configuration::get('WPINTOPS_SHOWPOSTDATE');
        $helper->fields_value['WPINTOPS_SHOWEXCERPT'] = Configuration::get('WPINTOPS_SHOWEXCERPT');
        $helper->fields_value['WPINTOPS_THUMBNAIL'] = Configuration::get('WPINTOPS_THUMBNAIL');
        $helper->fields_value['WPINTOPS_IMAGEWIDTH'] = Configuration::get('WPINTOPS_IMAGEWIDTH');
        $helper->fields_value['WPINTOPS_IMAGEHEIGHT'] = Configuration::get('WPINTOPS_IMAGEHEIGHT');
        $helper->fields_value['WPINTOPS_SHOWIMAGE'] = Configuration::get('WPINTOPS_SHOWIMAGE');
        $helper->fields_value['WPINTOPS_FRIENDLY_URL'] = Configuration::get('WPINTOPS_FRIENDLY_URL');
        $helper->fields_value['WPINTOPS_TARGETBLANK'] = Configuration::get('WPINTOPS_TARGETBLANK');

        return $helper->generateForm($this->fields_form);
    }
    
    public function hookDisplayLeftColumn($params) {   
        if ($this->wordpress_data->conexion->connect_errno == 0 && Configuration::get('WPINTOPS_WPURL') != '' && Configuration::get('WPINTOPS_LEFTCOLUMN') == 1) {
            $this->context->smarty->assign(
                array(
                    'posts' => $this->wordpress_data->getPosts(),
                    'target' => Configuration::get('WPINTOPS_TARGETBLANK')
                )
            );

            return $this->display(__FILE__, 'wpintops-widget.tpl');
        }
    }
   
    public function hookDisplayRightColumn($params) {
        if ($this->wordpress_data->conexion->connect_errno == 0 && Configuration::get('WPINTOPS_WPURL') != '' && Configuration::get('WPINTOPS_RIGHTCOLUMN') == 1) {
            return $this->hookDisplayLeftColumn($params);
        }
    }
    
    public function hookDisplayHome($params) {
        if ($this->wordpress_data->conexion->connect_errno == 0 && Configuration::get('WPINTOPS_WPURL') != '' && Configuration::get('WPINTOPS_HOMEPAGE') == 1) {
            $this->context->smarty->assign(
                array(
                    'posts' => $this->wordpress_data->getPosts(),
                    'showtitle' => Configuration::get('WPINTOPS_SHOWTITLE'),
                    'showpostdate' => Configuration::get('WPINTOPS_SHOWPOSTDATE'),
                    'showexcerpt' => Configuration::get('WPINTOPS_SHOWEXCERPT'),
                    'showimage' => Configuration::get('WPINTOPS_SHOWIMAGE'),
                    'width' => Configuration::get('WPINTOPS_IMAGEWIDTH'),
                    'height' => Configuration::get('WPINTOPS_IMAGEHEIGHT'),
                    'target' => Configuration::get('WPINTOPS_TARGETBLANK'),
                    'nb' => Configuration::get('WPINTOPS_NB')
                )
            );

            return $this->display(__FILE__, 'wpintops-home.tpl');
        }
    }
    
    public function hookDisplayFooterTop($params)
    {
        return $this->hookDisplayHome($params);
    }
    
    public function hookDisplayFooter($params) {
        if ($this->wordpress_data->conexion->connect_errno == 0 && Configuration::get('WPINTOPS_WPURL') != '' && Configuration::get('WPINTOPS_FOOTER') == 1) {
            $this->context->smarty->assign(
                array(
                    'posts' => $this->wordpress_data->getPosts(),
                    'target' => Configuration::get('WPINTOPS_TARGETBLANK')
                )
            );

            return $this->display(__FILE__, 'wpintops-footer.tpl');
        }
    }
   
    public function hookDisplayHeader() {
        if (version_compare(_PS_VERSION_, '1.6', '<')) 
            $this->context->controller->addCSS($this->_path.'views/css/1.5/wpintops.css', 'all');
        else
            $this->context->controller->addCSS($this->_path.'views/css/1.6/wpintops.css', 'all');
    }
}
?>
