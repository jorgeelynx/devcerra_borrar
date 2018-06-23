<?php
/**
 * 2017 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2017 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

class WtProductAdditionalFields extends Module
{
    public $language;

    public function __construct()
    {
        $this->name = 'wtproductadditionalfields';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'WeeTeam';
        $this->module_key = '7023009b397ccff7b16996ee208c5905';
        parent::__construct();
        $this->displayName = $this->l('Product Additional Fields');
        $this->description = '';
        require_once('classes/PAField.php');
        require_once('classes/PAFieldValue.php');
        require_once('classes/PAFieldProductValue.php');
        $this->initLanguage();
    }

    public function initLanguage()
    {
        $this->language = array(
            'id' => $this->l('ID'),
            'name' => $this->l('Name'),
            'description' => $this->l('Description'),
            'type' => $this->l('Type'),
            'active' => $this->l('Active'),
            'position' => $this->l('Position'),
            'delete_selected' => $this->l('Delete selected'),
            'confirm_delete_selected' => $this->l('Delete selected items?'),
            'text' => $this->l('Text'),
            'textarea' => $this->l('Textarea'),
            'textarea_rich' => $this->l('Rich Textare'),
            'switch' => $this->l('Switch'),
            'radio' => $this->l('Radio'),
            'checkbox' => $this->l('Checkbox'),
            'select' => $this->l('Select'),
            'multiple' => $this->l('Multiple'),
            'enabled' => $this->l('Enabled'),
            'disabled' => $this->l('Disabled'),
            'default_value' => $this->l('Default Value'),
            'save' => $this->l('Save'),
            'success' => $this->l('Update successful'),
            'add' => $this->l('Add new PAfield'),
            'delete' => $this->l('Delete'),
            'values' => $this->l('Values'),
            'select_change' =>
                $this->l('When changing type the previous data of this object field will be permanently deleted'),
            'text_yes' => $this->l('Yes'),
            'text_no' => $this->l('No'),
        );
    }


    public function install()
    {
        $tab_name = array(
            'en' => 'Product Additional Fields',
            'fr' => 'Produit Champs Additionnels',
            'ru' => 'Дополнительные поля товара',
        );
        $sql_file = dirname(__FILE__) . '/install/install.sql';
        return (
            parent::install() &&
            $this->loadSQLFile($sql_file) &&
            $this->installTab('AdminCatalog', 'AdminPAField', $tab_name) &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayFooterProduct') &&
            $this->registerHook('displayTextAreaRich') &&
            $this->registerHook('actionProductUpdate')
        );
    }

    public function hookDisplayTextAreaRich($params)
    {
        return $params['data_field'];
    }

    public function hookDisplayFooterProduct($params)
    {
        $id_product = $params['product']['id_product'];
        $data = PAField::getDataProduct((int)$id_product, $this->context);
        $this->context->smarty->assign(array(
            'data' => $data,
            'title' => $this->displayName,
            'id_product' => (int)$id_product,
        ));
        return $this->display(__FILE__, $this->name . '.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ($this->context->controller instanceof ProductController) {
            $this->context->controller->addCSS(
                _MODULE_DIR_ . $this->name . '/views/css/' . $this->name . '.css',
                'all'
            );
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        if ($this->context->controller->controller_name == 'AdminProducts') {
            $this->context->controller->addJS($this->_path . 'views/js/admin/' . $this->name . '-product.js');
        }
    }

    public function uninstall()
    {
        $sql_file = dirname(__FILE__) . '/install/uninstall.sql';
        return (
            parent::uninstall() &&
            $this->loadSQLFile($sql_file) &&
            $this->uninstallTab('AdminPAField'));
    }

    public function loadSQLFile($sql_file)
    {
        $sql_content = Tools::file_get_contents($sql_file);
        $sql_content = str_replace('_PREFIX_', _DB_PREFIX_, $sql_content);
        $sql_content = str_replace('_MYSQL_ENGINE_', _MYSQL_ENGINE_, $sql_content);
        $sql_requests = preg_split("/;\s*[\r\n]+/", $sql_content);
        $result = true;
        foreach ($sql_requests as $request) {
            if (!empty($request)) {
                $result &= Db::getInstance()->execute(trim($request));
            }
        }
        return $result;
    }

    public function installTab($parent, $class_name, $name)
    {
        $tab = new Tab();
        $tab->id_parent = (int)Tab::getIdFromClassName($parent);
        $tab->name = array();
        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            if (is_array($name)) {
                if (isset($name[$language['iso_code']])) {
                    $tab->name[$language['id_lang']] = $name[$language['iso_code']];
                } elseif (isset($name['en'])) {
                    $tab->name[$language['id_lang']] = $name['en'];
                } else {
                    foreach ($name as $value) {
                        $tab->name[$language['id_lang']] = $value;
                        break;
                    }
                }
            } else {
                $tab->name[$language['id_lang']] = $name;
            }
        }
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        return $tab->add();
    }

    public function uninstallTab($class_name)
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        $tab = new Tab((int)$id_tab);
        return $tab->delete();
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $tpl = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/' . 'product_tab.tpl'
        );
        $product_id = (int)$params['id_product'];
        $fields = PAField::getPAFields();
        $languages = Language::getLanguages();
        foreach ($fields as $field) {
            /* @var  PAField $field */
            $field->values = $field->getPAFieldProductValues($product_id);

            if (empty($field->values)) {
                $field->values = $field->getPAFieldValue();
            }

        }

        $tpl->assign(array(
            'fields' => $fields,
            'languages' => $languages,
            'url_add_item' =>
                $this->context->link->getAdminLink('AdminPAField', false) .
                '&token=' . Tools::getAdminTokenLite('AdminPAField'),
            'id_lang' => $this->context->language->id,
        ));
        return $tpl->fetch();
    }

    public function hookActionProductUpdate($product)
    {
        static $tmp = 0;
        if ($tmp) {
            return;
        }
        $tmp++;
        $fields = Tools::getValue('product_field');
        if (!is_array($fields)) {
            return;
        }
        foreach ($fields as $field) {
            $id_field = $field['id_paf_field'];
            $pafield = new PAField((int)$id_field);
            $delete = true;
            $product_field_ids = array();
            foreach ($field['values'] as $key => $product_field) {
                $id_product_field = $product_field['id_paf_field_product'];
                $id_product_field_value = $product_field['id_paf_field_value'];
                $pieldProduct = new PAFieldProductValue((int)$id_product_field);
                $pieldProduct->label = $product_field['label'];
                $pieldProduct->id_paf_field = $id_field;
                $pieldProduct->id_paf_field_value = $id_product_field_value;
                $pieldProduct->id_product = (int)$product['id_product'];
                switch ($pafield->type) {
                    case PAField::FIELD_TYPE_TEXT:
                    case PAField::FIELD_TYPE_TEXTAREA:
                    case PAField::FIELD_TYPE_TEXTAREA_RICH:
                        $pieldProduct->value_default = 1;
                        break;
                    case PAField::FIELD_TYPE_SWITCH:
                    case PAField::FIELD_TYPE_CHECKBOX:
                        if (!empty($product_field['value_default'])) {
                            $pieldProduct->value_default = 1;
                        } else {
                            $pieldProduct->value_default = 0;
                        }
                        break;
                    case PAField::FIELD_TYPE_RADIO:
                    case PAField::FIELD_TYPE_SELECT:
                        if (isset($field['value_default']) && (int)$field['value_default'] == $key) {
                            $pieldProduct->value_default = 1;
                        } else {
                            $pieldProduct->value_default = 0;
                        }
                        break;
                }
                if (Validate::isLoadedObject($pieldProduct)) {
                    if (!empty($product_field['remove'])) {
                        $pieldProduct->delete();
                    } else {
                        $product_field_ids[] = $pieldProduct->id_paf_field_product;
                        $pieldProduct->update();
                    }
                } else {
                    if ($delete) {
                        $delete = false;
                        $product_values = $pafield->getPAFieldProductValues((int)$product['id_product']);
                        foreach ($product_values as $value) {
                            /* @var PAFieldProductValue $value */
                            if (!in_array($value->id_paf_field_product, $product_field_ids)) {
                                $value->delete();
                            }
                        }
                    }
                    if (empty($product_field['remove'])) {
                        $pieldProduct->add();
                    }
                }
            }
        }
    }
}
