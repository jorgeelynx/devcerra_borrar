<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class Combinationstab extends Module
{
    public function __construct()
    {
        $this->name = 'combinationstab';
        $this->tab = 'smart_shopping';
        $this->version = '2.0.1';
        $this->author = 'mypresta.eu';
        $this->bootstrap = true;
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/product-page-combinations-table.html';
        $this->module_key = '71b6aefe1ff78a01eda3267938caad7a';
        parent::__construct();
        $this->checkforupdates();
        $this->displayName = $this->l('Combinations Tab Pro');
        $this->description = $this->l('Module creates a combinations matrix on each product page in your shop');
        $this->addproduct = $this->l('Add');
        $this->noproductsfound = $this->l('No products found');
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 12 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php'))
        {
            @require_once('../modules/' . $this->name . '/key.php');
        }
        else
        {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php'))
            {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            }
            else
            {
                if (@file_exists('modules/' . $this->name . '/key.php'))
                {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1)
        {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        }
        else
        {
            if (defined('_PS_ADMIN_DIR_'))
            {
                if (Tools::isSubmit('submit_settings_updates'))
                {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') == false)
                {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200))
                    {
                        $actual_version = combinationstabUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (combinationstabUpdate::version($this->version) < combinationstabUpdate::version(Configuration::get('updatev_' . $this->name)))
                    {
                        $this->warning = $this->l('New version available, check http://MyPresta.eu for more informations');
                    }
                }
                if ($display_msg == 1)
                {
                    if (combinationstabUpdate::version($this->version) < combinationstabUpdate::version(combinationstabUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version)))
                    {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    }
                    else
                    {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function install()
    {
        if (parent::install() == false || !Configuration::updateValue('update_' . $this->name, '0') || !$this->registerHook('header') || !$this->registerHook('productFooter') || !$this->registerHook('displayProductTab') || !$this->registerHook('displayProductExtraContent') || !$this->registerHook('displayProductTabContent') || !Configuration::updateValue('ctp_position', 'productFooter') || !Configuration::updateValue('ctp_lasttab', '1') || !Configuration::updateValue('ctp_image', '1') || !Configuration::updateValue('ctp_price', '1') || !Configuration::updateValue('ctp_price_logged', '0') || !Configuration::updateValue('ctp_reference', '1') || !Configuration::updateValue('ctp_ean', '0') || !Configuration::updateValue('ctp_cname', '1') || !Configuration::updateValue('ctp_atc', '1') || !Configuration::updateValue('ctp_atcq', '1') || !Configuration::updateValue('ctp_ajax', '1') || !Configuration::updateValue('ctp_sort', '1') || !Configuration::updateValue('ctp_quantity', '1') || !Configuration::updateValue('ctp_availability', '1') || !Configuration::updateValue('ctp_availability_d', '1') || !Configuration::updateValue('ctp_imagetype', '') || !Configuration::updateValue('ctp_imagetype_zoom', '') || !Configuration::updateValue('ctp_attributes_method', 1) || !Configuration::updateValue('ctp_prices_tax', 0) || !Configuration::updateValue('ctp_color_method', 0) || !Configuration::updateValue('ctp_hide_default', 0) || !Configuration::updateValue('ctp_hide_r', 0) || !Configuration::updateValue('ctp_where', 1) || !Configuration::updateValue('ctp_tabtype', 1) || !Configuration::updateValue('ctp_quantity_pm', 0))
        {
            return false;
        }
        return true;
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $exp = explode('.', $version);
        return $exp[1];
    }

    public function getContent()
    {
        return $this->displayForm();
    }

    public function displayForm()
    {
        return $this->loadModuleMenu() . $this->FormTabsType() . $this->FormConfiguration() . $this->FormRestrictions() . $this->FormUpdates();
    }

    public function loadModuleMenu()
    {

        if (Tools::isSubmit('selecttab'))
        {
            Configuration::updateValue('ctp_lasttab', Tools::getValue('selecttab'));
        }

        if (Configuration::get('ctp_lasttab') == 1)
        {
            $selected1 = 'active';
        }
        else
        {
            $selected1 = '';
        }
        if (Configuration::get('ctp_lasttab') == 2)
        {
            $selected2 = 'active';
        }
        else
        {
            $selected2 = '';
        }
        if (Configuration::get('ctp_lasttab') == 3)
        {
            $selected3 = 'active';
        }
        else
        {
            $selected3 = '';
        }
        $this->context->smarty->assign('selected1', $selected1);
        $this->context->smarty->assign('selected2', $selected2);
        $this->context->smarty->assign('selected3', $selected3);
        $this->context->smarty->assign('module_version', $this->version);
        return $this->display(__file__, 'views/templates/admin/menu.tpl');
    }

    public function FormConfiguration($getFields = false)
    {
        if (Configuration::get('ctp_lasttab') != 1)
        {
            return;
        }
        if (Tools::isSubmit('submit_configform_settings'))
        {
            Configuration::updatevalue('ctp_price', (Tools::getValue('ctp_price') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_price_logged', (Tools::getValue('ctp_price_logged') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_reference', (Tools::getValue('ctp_reference') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_ean', (Tools::getValue('ctp_ean') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_weight', (Tools::getValue('ctp_weight') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_cname', (Tools::getValue('ctp_cname') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_quantity', (Tools::getValue('ctp_quantity') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_atc', (Tools::getValue('ctp_atc') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_atcq', (Tools::getValue('ctp_atcq') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_image', Tools::getValue('ctp_image'));
            Configuration::updatevalue('ctp_fancybox', Tools::getValue('ctp_fancybox'));
            Configuration::updatevalue('ctp_imagetype_zoom', Tools::getValue('ctp_imagetype_zoom'));
            Configuration::updatevalue('ctp_imagetype', Tools::getValue('ctp_imagetype'));
            Configuration::updatevalue('ctp_ajax', (Tools::getValue('ctp_ajax') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_availability', (Tools::getValue('ctp_availability') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_availability_d', (Tools::getValue('ctp_availability_d') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_attributes_method', (Tools::getValue('ctp_attributes_method') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_attr_label', (Tools::getValue('ctp_attr_label') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_sort', (Tools::getValue('ctp_sort') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_sort_attr', (Tools::getValue('ctp_sort_attr') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_sort_price', Tools::getValue('ctp_sort_price', false));
            Configuration::updatevalue('ctp_sort_attrid', Tools::getValue('ctp_sort_attrid'));
            Configuration::updatevalue('ctp_sort_attrby', Tools::getValue('ctp_sort_attrby'));
            Configuration::updateValue('ctp_minqty', Tools::getValue('ctp_minqty', 0));
            Configuration::updateValue('ctp_vold', Tools::getValue('ctp_vold', 0));
            Configuration::updateValue('ctp_hquantity', Tools::getValue('ctp_hquantity', 0));
            Configuration::updateValue('ctp_hvquantity', Tools::getValue('ctp_hvquantity', 0));
            Configuration::updatevalue('ctp_hide_oos', (Tools::getValue('ctp_hide_oos') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_prices_tax', (Tools::getValue('ctp_prices_tax') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_atcb', (Tools::getValue('ctp_atcb') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_color_method', (Tools::getValue('ctp_color_method') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_hide_default', (Tools::getValue('ctp_hide_default') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_hide_r', (Tools::getValue('ctp_hide_r') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_quantity_pm', (Tools::getValue('ctp_quantity_pm') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_block_qty', (Tools::getValue('ctp_block_qty') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_onlypics', (Tools::getValue('ctp_onlypics') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_hide_default_conly', (Tools::getValue('ctp_hide_default_conly') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_psum', (Tools::getValue('ctp_psum') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_hide_price_zero', (Tools::getValue('ctp_hide_price_zero') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_pagination', (Tools::getValue('ctp_pagination') == 1 ? '1' : '0'));
            Configuration::updatevalue('ctp_pagination_nb', (Tools::getValue('ctp_pagination_nb') > 0 ? Tools::getValue('ctp_pagination_nb') : '10'));
            $this->context->controller->confirmations[] = $this->l('Successfully updated');
        }

        $images_types = ImageType::getImagesTypes('products');
        $attributes_color_method = array(
            array(
                'name' => $this->l('Display as color'),
                'id_object' => 1
            ),
            array(
                'name' => $this->l('Display as text'),
                'id_object' => 0
            )
        );
        $attributes_display_method = array(
            array(
                'name' => $this->l('All atributes in one column'),
                'id_object' => 1
            ),
            array(
                'name' => $this->l('Atributes in separated columns'),
                'id_object' => 0
            )
        );
        $options = array(
            array(
                'id_option' => '0',
                'name' => $this->l('No')
            ),
            array(
                'id_option' => '1',
                'name' => $this->l('Yes')
            )
        );
        $options_sort = array(
            array(
                'id_option' => '0',
                'name' => $this->l('Descending')
            ),
            array(
                'id_option' => '1',
                'name' => $this->l('Ascending')
            )
        );

        if ($getFields == false)
        {
            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Configuration'),
                        'icon' => 'icon-wrench'
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display image'),
                            'name' => 'ctp_image',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Enable fancybox zoom'),
                            'name' => 'ctp_fancybox',
                            'desc' => $this->l('Option creates a clickable image of combination. It will open fancybox with zoomed cover picture of combination.'),
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Image type to display'),
                            'name' => 'ctp_imagetype',
                            'desc' => $this->l('Select size of product image, module will show it inside combinations table as a cover image of combination'),
                            'options' => array(
                                'query' => $images_types,
                                'id' => 'name',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Image type used to enlarge thumbnail'),
                            'name' => 'ctp_imagetype_zoom',
                            'desc' => $this->l('Select size of product image, module will show it inside fancybox (zoomed cover image of combination)'),
                            'options' => array(
                                'query' => $images_types,
                                'id' => 'name',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Attributes display method'),
                            'name' => 'ctp_attributes_method',
                            'options' => array(
                                'query' => $attributes_display_method,
                                'id' => 'id_object',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Include label to attribute name'),
                            'name' => 'ctp_attr_label',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            ),
                            'desc' => $this->l('This option - when enabled - will include attribute label(name) near the attribute value.') . '<br/>'
                                . $this->l('Attribute with label example: Color: red, Size: L') . '<br/>'
                                . $this->l('Attribute without label example: red, L')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Color attribute'),
                            'name' => 'ctp_color_method',
                            'desc' => $this->l('If your product will have a color attribute you can decide how module will display it'),
                            'options' => array(
                                'query' => $attributes_color_method,
                                'id' => 'id_object',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Hide out of stock combinations'),
                            'name' => 'ctp_hide_oos',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Hide combinations with price = 0'),
                            'name' => 'ctp_hide_price_zero',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Show only combinations with pictures'),
                            'name' => 'ctp_onlypics',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display price'),
                            'name' => 'ctp_price',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display price only for logged customers'),
                            'name' => 'ctp_price_logged',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Prices without tax'),
                            'name' => 'ctp_prices_tax',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display reference'),
                            'name' => 'ctp_reference',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display EAN13'),
                            'name' => 'ctp_ean',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display weight'),
                            'name' => 'ctp_weight',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display combination name'),
                            'name' => 'ctp_cname',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display quantity'),
                            'name' => 'ctp_quantity',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Hide quantity above defined value'),
                            'name' => 'ctp_hquantity',
                            'desc' => $this->l('If you want to hide exact stock information you can enable this option. Module will hide exact information if your stock will be higher than value defined below'),
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Define quantity value (hide qty above)'),
                            'name' => 'ctp_hvquantity'
                        ),

                        array(
                            'type' => 'select',
                            'label' => $this->l('Display availability'),
                            'name' => 'ctp_availability',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display minimal quantity'),
                            'name' => 'ctp_minqty',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display volume discounts'),
                            'name' => 'ctp_vold',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display availability date'),
                            'name' => 'ctp_availability_d',
                            'desc' => $this->l('This option will display availability date of product that is out of stock. Date wil appear in Availabiltiy column.'),
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display add to cart'),
                            'name' => 'ctp_atc',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display add to cart quantity field'),
                            'name' => 'ctp_atcq',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Display [+] and [-] near quantity field'),
                            'name' => 'ctp_quantity_pm',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Block possibility to increase quantity to values higher than available combination\'s stock'),
                            'name' => 'ctp_block_qty',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Sort feature'),
                            'name' => 'ctp_sort',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            ),
                            'desc' => $this->l('Option when it is enabled gives possibility to sort table by all columns'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Sort by price'),
                            'name' => 'ctp_sort_price',
                            'desc' => $this->l('If enabled - sort by attribute will not work, module will enable option to sort by price only'),
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Sort by attribute'),
                            'name' => 'ctp_sort_attr',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            ),
                            'desc' => $this->l('Option when it is enabled will automatically sort the table by given attribute. When it is enabled it is necessary to define attribute ID below'),
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Attribute ID'),
                            'name' => 'ctp_sort_attrid',
                            'desc' => '<a href="https://mypresta.eu/en/art/basic-tutorials/how-to-get-attribute-group-id-in-prestashop.html" class="_blank">' . $this->l('how to get attribute group ID?') . '</a>',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Sort order'),
                            'name' => 'ctp_sort_attrby',
                            'options' => array(
                                'query' => $options_sort,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Pagination'),
                            'name' => 'ctp_pagination',
                            'desc' => $this->l('If enabled - module will create pagination for matrix. You will be able to explore the list of combinations with pagination feature. If you will enable this option don\'t forget to define number of products per page below.'),
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Number of combinations per page'),
                            'name' => 'ctp_pagination_nb',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('AJAX cart'),
                            'name' => 'ctp_ajax',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Add to cart in bulk'),
                            'desc' => $this->l('Add to cart in bulk works only with AJAX Cart. This option disables default button to add combination to cart. Add to cart in bulk is an option to add several types of products to cart with one mouse click. As a customer you have just to fill out quantity fields near combinations with value you want to add to cart, then below the matrix press "add products to cart" - all of products with filled quantity field will be added to the cart'),
                            'name' => 'ctp_atcb',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('price summary of selected combinations'),
                            'desc' => $this->l('Option - when enabled will show price summary of selected combinations (works with add to cart in bulk feature)'),
                            'name' => 'ctp_psum',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Hide default add to cart feature'),
                            'desc' => $this->l('This option removes default add to cart form from product page, it removes this button from products defined in "product restrictions" section'),
                            'name' => 'ctp_hide_r',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Hide it only if product has combinations'),
                            'name' => 'ctp_hide_default_conly',
                            'options' => array(
                                'query' => $options,
                                'id' => 'id_option',
                                'name' => 'name'
                            )
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    )
                ),
            );

            $helper = new HelperForm();
            $helper->show_toolbar = false;
            $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
            $helper->default_form_language = $lang->id;
            $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
            $this->fields_form = array();
            $helper->id = 'configform_notifications';
            $helper->identifier = 'identifier_configform_notifications';
            $helper->submit_action = 'submit_configform_settings';
            $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->tpl_vars = array(
                'fields_value' => $this->FormConfiguration(true),
                'languages' => $this->context->controller->getLanguages(),
                'id_language' => $this->context->language->id
            );
            return $helper->generateForm(array($fields_form));
        }
        elseif ($getFields == true)
        {
            $fields = array(
                array(
                    'name' => 'ctp_image',
                ),
                array(
                    'name' => 'ctp_fancybox',
                ),
                array(
                    'name' => 'ctp_imagetype',
                ),
                array(
                    'name' => 'ctp_imagetype_zoom',
                ),
                array(
                    'name' => 'ctp_attributes_method',
                ),
                array(
                    'name' => 'ctp_attr_label',
                ),
                array(
                    'name' => 'ctp_color_method',
                ),
                array(
                    'name' => 'ctp_hide_oos',
                ),
                array(
                    'name' => 'ctp_hide_price_zero',
                ),
                array(
                    'name' => 'ctp_onlypics',
                ),
                array(
                    'name' => 'ctp_price',
                ),
                array(
                    'name' => 'ctp_price_logged',
                ),
                array(
                    'name' => 'ctp_prices_tax',
                ),
                array(
                    'name' => 'ctp_reference',
                ),
                array(
                    'name' => 'ctp_ean',
                ),
                array(
                    'name' => 'ctp_weight',
                ),
                array(
                    'name' => 'ctp_cname',
                ),
                array(
                    'name' => 'ctp_quantity',
                ),
                array(
                    'name' => 'ctp_hquantity',
                ),
                array(
                    'name' => 'ctp_hvquantity'
                ),
                array(
                    'name' => 'ctp_availability',
                ),
                array(
                    'name' => 'ctp_minqty',
                ),
                array(
                    'name' => 'ctp_vold',
                ),
                array(
                    'name' => 'ctp_availability_d',
                ),
                array(
                    'name' => 'ctp_atc',
                ),
                array(
                    'name' => 'ctp_atcq',
                ),
                array(
                    'name' => 'ctp_quantity_pm',
                ),
                array(
                    'name' => 'ctp_block_qty',
                ),
                array(
                    'name' => 'ctp_sort',
                ),
                array(
                    'name' => 'ctp_sort_attr',
                ),
                array(
                    'name' => 'ctp_sort_attrid',
                ),
                array(
                    'name' => 'ctp_sort_attrby',
                ),
                array(
                    'name' => 'ctp_sort_price',
                ),
                array(
                    'name' => 'ctp_pagination',
                ),
                array(
                    'name' => 'ctp_pagination_nb',
                ),
                array(
                    'name' => 'ctp_ajax',
                ),
                array(
                    'name' => 'ctp_atcb',
                ),
                array(
                    'name' => 'ctp_psum',
                ),
                array(
                    'name' => 'ctp_hide_r',
                ),
                array(
                    'name' => 'ctp_hide_default_conly',
                ),
            );
            $fields_value = array();
            foreach ($fields AS $field)
            {
                $fields_value[$field['name']] = Configuration::get($field['name']);
            }
            return $fields_value;
        }
    }

    public function FormRestrictions($getFields = false)
    {
        if (Configuration::get('ctp_lasttab') != 2)
        {
            return;
        }
        if (Tools::isSubmit('module_restrictions'))
        {
            $ids = Tools::getValue('ctp_pr_ids');
            Configuration::updatevalue('ctp_pr', (Tools::getValue('ctp_pr') == 1 ? '1' : '0'));
            if ($ids != "")
            {
                Configuration::updatevalue('ctp_pr_ids', implode(",", $ids));
            }
            else
            {
                Configuration::updatevalue('ctp_pr_ids', '');
            }
            $this->context->controller->confirmations[] = $this->l('Successfully updated');
        }

        if (Configuration::get('ctp_pr_ids') == null || Configuration::get('ctp_pr_ids') == false || Configuration::get('ctp_pr_ids') == '')
        {
            $ctp_pr_ids = false;
        }
        else
        {
            $pre_ctp_pr_ids = explode(',', Configuration::get('ctp_pr_ids'));
            foreach ($pre_ctp_pr_ids AS $product)
            {
                $pr = new Product($product, false, $this->context->language->id);
                $ctp_pr_ids[$product] = $pr->name;
            }
        }
        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('ctp_pr', Configuration::get('ctp_pr'));
        $this->context->smarty->assign('ctp_pr_ids', (count($ctp_pr_ids) > 0 ? $ctp_pr_ids : false));
        return $this->display(__file__, 'views/templates/admin/restrictions.tpl');
    }

    public function FormTabsType($getFields = false)
    {
        if (Configuration::get('ctp_lasttab') != 1)
        {
            return;
        }
        if (Tools::isSubmit('module_position'))
        {
            Configuration::updatevalue('ctp_where', Tools::getValue('ctp_where'));
            Configuration::updatevalue('ctp_tabtype', Tools::getValue('ctp_tabtype'));
            $this->context->controller->confirmations[] = $this->l('Successfully updated');
        }

        $this->context->smarty->assign('link', $this->context->link);
        return $this->display(__file__, 'views/templates/admin/tabstype.tpl');
    }

    public function FormUpdates($getFields = false)
    {
        if (Configuration::get('ctp_lasttab') != 3)
        {
            return;
        }
        return $this->checkforupdates(0, 1);
    }

    public function hookheader($params)
    {
        if (Tools::getValue('controller') == 'product')
        {
            $this->context->smarty->assign('ctp_atcb', Configuration::get('ctp_atcb'));
            $this->context->controller->addCss(($this->_path) . 'css/combinationstab.css', 'all');
            $this->context->controller->addJS(($this->_path) . 'js/atc.js', 'all');
            $this->context->controller->addJqueryPlugin('fancybox');
            if (Configuration::get('ctp_atcb') == 1)
            {
                $this->context->controller->addJS(($this->_path) . 'js/combinationstab.js', 'all');
            }
            if (Configuration::get('ctp_sort') == 1)
            {
                $this->context->controller->addJS(($this->_path) . 'js/jquery.tablesorter.min.js', 'all');
            }
        }
    }

    public function hookdisplayProductTab($params)
    {
        $this->context->smarty->assign('ctp_pr', Configuration::get('ctp_pr'));
        $this->context->smarty->assign('ctp_pr_ids', explode(',', Configuration::get('ctp_pr_ids')));
        return $this->display(__file__, 'views/templates/hook/tab.tpl');
    }

    public function hookdisplayProductTabContent($params)
    {
        if (Configuration::get('ctp_where') == 2)
        {
            return $this->prepareTableOptions($params);
        }
    }

    public function hookExtraRight($params)
    {
        return $this->prepareTableOptions($params);
    }

    public function hookExtraLeft($params)
    {
        return $this->prepareTableOptions($params);
    }

    public function hookProductActions($params)
    {
        return $this->prepareTableOptions($params);
    }

    public function hookProductFooter($params)
    {
        if (Configuration::get('ctp_where') == 1)
        {
            return $this->prepareTableOptions($params);
        }
    }

    public function hookdisplayProductExtraContent($params)
    {
        if (Configuration::get('ctp_where') == 2)
        {
            $contents = $this->prepareTableOptions($params, true);
            $ps17tabz[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())->setTitle($this->l('Product variants'))->setContent($contents);
            return $ps17tabz;
        }
        return array();
    }

    public function getVolumeDiscounts($idp, $ida)
    {
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);
        $id_group = (int)Group::getCurrent()->id;
        $id_country = $id_customer ? (int)Customer::getCurrentCountry($id_customer) : (int)Tools::getCountry();

        $id_currency = (int)$this->context->cookie->id_currency;
        $id_product = (int)$idp;
        $id_shop = $this->context->shop->id;

        $quantity_discounts = SpecificPrice::getQuantityDiscounts($id_product, $id_shop, $id_currency, $id_country, $id_group, $ida, false, (int)$this->context->customer->id);

        foreach ($quantity_discounts as &$quantity_discount)
        {
            if ($quantity_discount['id_product_attribute'])
            {
                $combination = new Combination((int)$quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int)$this->context->language->id);
                foreach ($attributes as $attribute)
                {
                    $quantity_discount['attributes'] = $attribute['name'] . ' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int)$quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount')
            {
                $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
            }
        }


        $product = new Product($idp, true, $this->context->language->id);
        $product_price = $product->getPrice(Product::$_taxCalculationMethod == PS_TAX_INC, $ida);
        $tax = (float)$product->getTaxesRate(new Address((int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
        $ecotax_rate = (float)Tax::getProductEcotaxRate($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $ecotax_tax_amount = Tools::ps_round($product->ecotax, 2);
        if (Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX'))
        {
            $ecotax_tax_amount = Tools::ps_round($ecotax_tax_amount * (1 + $ecotax_rate / 100), 2);
        }


        $this->context->smarty->assign(array(
            'quantity_discounts' => $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float)$tax, $ecotax_tax_amount),
            'display_discount_price' => Configuration::get('PS_DISPLAY_DISCOUNT_PRICE'),
            'product' => $product,
            'ida' => $ida
        ));
        return $this->context->smarty->fetch('module:combinationstab/views/templates/hook/volume_discounts.tpl');

    }

    public function formatQuantityDiscounts($specific_prices, $price, $tax_rate, $ecotax_amount)
    {
        foreach ($specific_prices as $key => &$row)
        {
            $row['quantity'] = &$row['from_quantity'];
            if ($row['price'] >= 0)
            {
                // The price may be directly set

                $cur_price = (!$row['reduction_tax'] ? $row['price'] : $row['price'] * (1 + $tax_rate / 100)) + (float)$ecotax_amount;

                if ($row['reduction_type'] == 'amount')
                {
                    $cur_price -= ($row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100));
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                }
                else
                {
                    $cur_price *= 1 - $row['reduction'];
                }

                $row['real_value'] = $price > 0 ? $price - $cur_price : $cur_price;
            }
            else
            {
                if ($row['reduction_type'] == 'amount')
                {
                    if (Product::$_taxCalculationMethod == PS_TAX_INC)
                    {
                        $row['real_value'] = $row['reduction_tax'] == 1 ? $row['reduction'] : $row['reduction'] * (1 + $tax_rate / 100);
                    }
                    else
                    {
                        $row['real_value'] = $row['reduction_tax'] == 0 ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                    }
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] + ($row['reduction'] * $tax_rate) / 100;
                }
                else
                {
                    $row['real_value'] = $row['reduction'] * 100;
                }
            }
            $row['nextQuantity'] = (isset($specific_prices[$key + 1]) ? (int)$specific_prices[$key + 1]['from_quantity'] : -1);
        }
        return $specific_prices;
    }

    public function prepareTableOptions($params, $fetch = false)
    {
        $id_product = Tools::getValue('id_product');
        $product = new Product($id_product, true, $this->context->language->id);
        $combination_images = $product->getCombinationImages($this->context->language->id);
        $combinations = array();
        $matrix_attributes = array();

        $fpget = $product->getAttributeCombinations($this->context->language->id);

        foreach ($fpget as $attr)
        {
            $combinations[$attr['id_product_attribute']]['combination'] = $attr;
            if (!isset($combinations[$attr['id_product_attribute']]['combination_name']))
            {
                $combinations[$attr['id_product_attribute']]['combination_name'] = '';
            }
            $combinations[$attr['id_product_attribute']]['combination_name'] = $combinations[$attr['id_product_attribute']]['combination_name'] . (Configuration::get('ctp_attr_label') == 1 ? $attr['group_name'] . ": ":''). $attr['attribute_name'] . ", ";
            if (isset($combination_images[$attr['id_product_attribute']]['0']))
            {
                $combinations[$attr['id_product_attribute']]['image'] = $combination_images[$attr['id_product_attribute']]['0'];
            }
            else
            {
                $combinations[$attr['id_product_attribute']]['image'] = 0;
            }
            $gr = new AttributeGroupCore($attr['id_attribute_group']);
            $gr_atr = new Attribute($attr['id_attribute']);
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['id'] = $gr_atr->id_attribute_group;
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['light'] = $gr_atr->light;
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['name'] = $attr['attribute_name'];
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['public_name'] = $gr->public_name[$this->context->language->id];
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['type'] = $gr->group_type;
            $combinations[$attr['id_product_attribute']]['attributes'][$gr->position]['color'] = $gr_atr->color;
            //$matrix_attributes[$gr->position][$attr['group_name']] = 1;
            //$matrix_attributes[$gr->position][$gr->public_name[$this->context->language->id]] = $gr_atr->id_attribute_group;
            $matrix_attributes[$gr->position]['name'] = $gr->public_name[$this->context->language->id];
            $matrix_attributes[$gr->position]['id'] = $gr_atr->id_attribute_group;
            $matrix_attributes[$gr->position]['id_value'] = $gr_atr->id;
            ksort($combinations[$attr['id_product_attribute']]['attributes']);
            ksort($matrix_attributes);
        }

        if (!$this->context->cart->id)
        {
            $this->context->smarty->assign('ctp_cartexists', false);
        }
        else
        {
            $this->context->smarty->assign('ctp_cartexists', true);
        }
		 $this->context->smarty->assign('producto', $product->name);
        $this->context->smarty->assign('module', $this);
        $this->context->smarty->assign('ctp_image', Configuration::get('ctp_image'));
        $this->context->smarty->assign('ctp_imagetype', Configuration::get('ctp_imagetype'));
        $this->context->smarty->assign('ctp_price', Configuration::get('ctp_price'));
        $this->context->smarty->assign('ctp_reference', Configuration::get('ctp_reference'));
        $this->context->smarty->assign('ctp_ean', Configuration::get('ctp_ean'));
        $this->context->smarty->assign('ctp_cname', Configuration::get('ctp_cname'));
        $this->context->smarty->assign('ctp_quantity', Configuration::get('ctp_quantity'));
        $this->context->smarty->assign('ctp_hquantity', Configuration::get('ctp_hquantity'));
        $this->context->smarty->assign('ctp_hvquantity', Configuration::get('ctp_hvquantity'));
        $this->context->smarty->assign('ctp_atc', Configuration::get('ctp_atc'));
        $this->context->smarty->assign('ctp_weight', Configuration::get('ctp_weight'));
        $this->context->smarty->assign('ctp_atcq', Configuration::get('ctp_atcq'));
        $this->context->smarty->assign('ctp_ajax', Configuration::get('ctp_ajax'));
        $this->context->smarty->assign('ctp_sort', Configuration::get('ctp_sort'));
        $this->context->smarty->assign('ctp_color_method', Configuration::get('ctp_color_method'));
        $this->context->smarty->assign('ctp_availability', Configuration::get('ctp_availability'));
        $this->context->smarty->assign('ctp_availability_d', Configuration::get('ctp_availability_d'));
        $this->context->smarty->assign('ctp_matrix', $combinations);
        $this->context->smarty->assign('ctp_product', $product);
        $this->context->smarty->assign('ctp_fancybox', Configuration::get('ctp_fancybox'));
        $this->context->smarty->assign('allow_oosp', $product->isAvailableWhenOutOfStock((int)$product->out_of_stock));
        $this->context->smarty->assign('ctp_prices_tax', Configuration::get('ctp_prices_tax'));
        $this->context->smarty->assign('ctp_pr', Configuration::get('ctp_pr'));
        $this->context->smarty->assign('ctp_pr_ids', explode(',', Configuration::get('ctp_pr_ids')));
        $this->context->smarty->assign('ctp_atcb', Configuration::get('ctp_atcb'));
        $this->context->smarty->assign('ctp_hide_default', Configuration::get('ctp_hide_default'));
        $this->context->smarty->assign('ctp_hide_r', Configuration::get('ctp_hide_r'));
        $this->context->smarty->assign('thickbox_image', Configuration::get('ctp_imagetype_zoom'));
        $this->context->smarty->assign('psversion', $this->psversion());
        $this->context->smarty->assign('ctp_attributes_method', Configuration::get('ctp_attributes_method'));
        $this->context->smarty->assign('matrix_attributes', $matrix_attributes);
        $this->context->smarty->assign('ctp_quantity_pm', Configuration::GET('ctp_quantity_pm'));
        $this->context->smarty->assign('ctp_price_logged', Configuration::get('ctp_price_logged'));
        $this->context->smarty->assign('ctp_logged', $this->context->customer->isLogged());
        $this->context->smarty->assign('col_img_dir', _PS_COL_IMG_DIR_);
        $this->context->smarty->assign('ctp_minqty', Configuration::get('ctp_minqty'));
        $this->context->smarty->assign('ctp_vold', Configuration::get('ctp_vold'));
        $this->context->smarty->assign('ctp_sort_price', Configuration::get('ctp_sort_price'));
        $this->context->smarty->assign('theme_col_img_dir', _THEME_COL_DIR_);
        $this->context->smarty->assign('content_dir', $this->context->shop->getBaseURL(true, true));
        $this->context->smarty->assign('link', $this->context->link);
        $this->context->smarty->assign('ctp_pagination', (Configuration::get('ctp_pagination') == 1 ? 1 : 0));
        $this->context->smarty->assign('ctp_pagination_nb', (Configuration::get('ctp_pagination_nb') > 0 ? Configuration::get('ctp_pagination_nb') : 0));

        if ($fetch == false)
        {
            return $this->context->smarty->display('module:combinationstab/views/templates/hook/productfooter.tpl');
        }
        else
        {
            return $this->context->smarty->fetch('module:combinationstab/views/templates/hook/productfooter.tpl');
        }
    }

    public function inconsistency($return)
    {
        return true;
    }
}

class combinationstabUpdate extends combinationstab
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3)
        {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2)
        {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1)
        {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0)
        {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen"))
        {
            if (function_exists("file_get_contents"))
            {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

class combnationstabUpdate extends combinationstab
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3)
        {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2)
        {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1)
        {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0)
        {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen"))
        {
            if (function_exists("file_get_contents"))
            {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

?>