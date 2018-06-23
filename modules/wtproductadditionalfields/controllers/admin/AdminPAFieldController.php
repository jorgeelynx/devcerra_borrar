<?php
/**
 * 2017 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2017 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

class AdminPAFieldController extends ModuleAdminController
{
    protected $position_identifier = 'id_paf_field';
    /* @var WtProductAdditionalFields $module */
    public $module;

    public function __construct()
    {
        $this->table = 'paf_field';
        $this->className = 'PAField';
        $this->list_id = 'paf_field';
        $this->identifier = 'id_paf_field';
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';
        $this->lang = true;
        $this->bootstrap = true;
        $this->default_form_language = Configuration::get('PS_LANG_DEFAULT');
        parent::__construct();
        $this->fields_list = array(
            'id_paf_field' => array(
                'title' => $this->module->language['id'],
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->module->language['name'],
                'align' => 'left',
                'lang' => true,
            ),
            'description' => array(
                'title' => $this->module->language['description'],
                'align' => 'left',
                'lang' => true,
            ),
            'type' => array(
                'title' => $this->module->language['type'],
                'align' => 'center',
                'type' => 'text',
                'callback' => 'getFieldTypeName',
                'search' => false
            ),
            'active' => array(
                'title' => $this->module->language['active'],
                'align' => 'center',
                'type' => 'bool',
                'active' => 'active',
                'class' => 'fixed-width-xs'
            ),
            'position' => array(
                'title' => $this->module->language['position'],
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
                'lang' => false,
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->language['delete_selected'],
                'icon' => 'icon-trash',
                'confirm' => $this->module->language['confirm_delete_selected'],
            )
        );
        $this->fields_form = array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->language['name'],
                    'name' => 'name',
                    'lang' => true,
                    'required' => true
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->language['description'],
                    'name' => 'description',
                    'cols' => 50, 'rows' => 5,
                    'lang' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->language['type'],
                    'name' => 'type',
                    'default_value' => PAField::FIELD_TYPE_TEXTAREA,
                    'options' => array(
                        'query' => array(
                            array(
                                'id_type' => PAField::FIELD_TYPE_TEXT,
                                'name' => $this->module->language['text'],
                            ),
                            array(
                                'id_type' => PAField::FIELD_TYPE_TEXTAREA,
                                'name' => $this->module->language['textarea'],
                            ),
                            array(
                                'id_type' => PAField::FIELD_TYPE_TEXTAREA_RICH,
                                'name' => $this->module->language['textarea_rich'],
                            ),
                            array(
                                'id_type' => PAField::FIELD_TYPE_SWITCH,
                                'name' => $this->module->language['switch'],
                            ),
                            array(
                                'id_type' => PAField::FIELD_TYPE_RADIO,
                                'name' => $this->module->language['radio'],
                            ),
                            array(
                                'id_type' => PAField::FIELD_TYPE_CHECKBOX,
                                'name' => $this->module->language['checkbox'],
                            ),
                            array(
                                'id_type' => PAField::FIELD_TYPE_SELECT,
                                'name' => $this->module->language['select'],
                            ),
                        ),
                        'id' => 'id_type',
                        'name' => 'name')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->language['multiple'],
                    'name' => 'multiple',
                    'values' => array(
                        array(
                            'id' => 'multiple_1',
                            'value' => 1,
                            'label' => $this->module->language['enabled'],
                        ),
                        array(
                            'id' => 'multiple_0',
                            'value' => 0,
                            'label' => $this->module->language['disabled'],
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->language['active'],
                    'name' => 'active',
                    'values' => array(
                        array(
                            'id' => 'active_1',
                            'value' => 1,
                            'label' => $this->module->language['enabled'],
                        ),
                        array(
                            'id' => 'active_0',
                            'value' => 0,
                            'label' => $this->module->language['disabled'],
                        )
                    ),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'position',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->language['default_value'],
                    'name' => 'value_default_' . PAField::FIELD_TYPE_TEXT,
                    'class' => 'value',
                    'lang' => true,
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->language['default_value'],
                    'name' => 'value_default_' . PAField::FIELD_TYPE_TEXTAREA,
                    'class' => 'value',
                    'lang' => true,

                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->language['default_value'],
                    'name' => 'value_default_' . PAField::FIELD_TYPE_TEXTAREA_RICH,
                    'class' => 'value autoload_rte',
                    'lang' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->language['default_value'],
                    'name' => 'value_default_' . PAField::FIELD_TYPE_SWITCH,
                    'class' => 'value',
                    'values' => array(
                        array(
                            'value' => 1,
                            'label' => $this->module->language['enabled'],
                        ),
                        array(
                            'value' => 0,
                            'label' => $this->module->language['disabled'],
                        )
                    ),
                ),

            ),
            'submit' => array('title' => $this->module->language['save'])
        );
    }

    public function ajaxProcessGetItemForm()
    {
        $field = new PAField((int)Tools::getValue('field'));
        if (!Validate::isLoadedObject($field)) {
            return;
        }
        $counter = (int)Tools::getValue('counter');
        switch ($field->type) {
            case PAField::FIELD_TYPE_TEXT:
            case PAField::FIELD_TYPE_TEXTAREA:
            case PAField::FIELD_TYPE_TEXTAREA_RICH:
                $tpl = $this->context->smarty->createTemplate(
                    _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/' . 'product_tab.tpl'
                );
                $fields = array();
                $field->values = $field->getPAFieldValue();

                $fields[] = $field;

                $languages = Language::getLanguages();

                $tpl->assign(array(
                    'fields' => $fields,
                    'languages' => $languages,
                    'counter' => $counter,
                    'add_field' => true,
                    'url_add_item' =>
                        $this->context->link->getAdminLink('AdminPAField', false) .
                        '&token=' . Tools::getAdminTokenLite('AdminPAField'),
                    'id_lang' => $this->context->language->id,
                ));
                break;
            default:
                return;
        }
        die ($tpl->fetch());
    }

    public function ajaxProcessChangeActive()
    {
        $json = array();
        $paf_field = new PAField(Tools::getValue($this->identifier));
        if ($paf_field->id) {
            try {
                $paf_field->active = $paf_field->active ? 0 : 1;
                $paf_field->save();
            } catch (Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        if (!$json) {
            $json['success'] = $this->module->language['success'];
            $json[$this->identifier] = $paf_field->id;
            $json['active'] = $paf_field->active;
        }
        die(Tools::jsonEncode($json));
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }


    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_paf_field'] = array(
                'href' => self::$currentIndex . '&addpaf_field&token=' . $this->token,
                'desc' => $this->module->language['add'],
                'icon' => 'process-icon-new'
            );

        } elseif ($this->display == 'edit') {

            $this->page_header_toolbar_btn['delete'] = array(
                'href' => self::$currentIndex .
                    '&token=' . $this->token .
                    '&id_paf_field=' .
                    (int)Tools::getValue('id_paf_field') .
                    '&deletepaf_field',

                'desc' => $this->module->language['delete'],
            );

            $this->page_header_toolbar_btn['save'] = array(
                'href' => '#',
                'desc' => $this->module->language['save']
            );
        }
        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        /* @var PAField $this ->object */
        if (Validate::isLoadedObject($this->object)) {
            switch ($this->object->type) {
                case PAField::FIELD_TYPE_SWITCH:
                    if (!$this->object->multiple) {
                        $this->object->value_default = (int)$this->object->value_default[$this->context->language->id];
                    }
                    break;
                default:
                    $this->object->{'value_default_' . $this->object->type} = $this->object->value_default;
            }
        }

        $tpl = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/' . 'message.tpl'
        );
        $tpl->assign('select_change', $this->module->language['select_change']);
        return $tpl->fetch() . parent::renderForm();
    }

    public function ajaxProcessRenderFormPAFieldValue()
    {
        $object = new PAField((int)Tools::getValue('id_paf_field'));
        if (Validate::isLoadedObject($object) && $object->type == (int)Tools::getValue('type')) {
            $values = $object->getPAFieldValue();
        }
        if (empty($values)) {
            $values[] = new PAFieldValue();
        }
        $tpl = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/' . 'pafieldvalue.tpl'
        );
        $tpl->assign(array(
            'values' => $values,
            'text_yes' => $this->module->language['text_yes'],
            'text_no' => $this->module->language['text_no'],
            'type' => (int)Tools::getValue('type'),
            'languages' => Language::getLanguages(),
            'id_lang' => (int)Tools::getValue('id_lang'),
            'label' => $this->module->language['values'],
        ));
        die($tpl->fetch());
    }

    public function processUpdate()
    {
        /* @var PAField $this ->object */
        if ($this->object->type != (int)Tools::getValue('type')) {
            $pafield_values = $this->object->getPAFieldValue();
            foreach ($pafield_values as $item) {
                /* @var PAFieldValue $item */
                $item->delete();
            }
        }
        parent::processUpdate();
    }

    public function ajaxProcessGetItemPAFieldValue()
    {
        $tpl = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/' . 'pafieldvalue.tpl'
        );
        $tpl->assign(array(
            'values' => array(new PAFieldValue()),
            'type' => (int)Tools::getValue('type'),
            'text_yes' => $this->module->language['text_yes'],
            'text_no' => $this->module->language['text_no'],
            'languages' => Language::getLanguages(),
            'id_lang' => (int)Tools::getValue('id_lang'),
            'counter' => (int)Tools::getValue('counter'),
            'label' => $this->module->language['values'],
        ));

        die($tpl->fetch());
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $prefix = ($this->display == 'add' || $this->display == 'edit') ? '-form' : '-list';
        $this->addJS($this->module->getPathUri() . 'views/js/admin/' . $this->module->name . $prefix . '.js');
    }

    public function getFieldTypeName($param)
    {
        $types = array(
            PAField::FIELD_TYPE_TEXTAREA => $this->module->language['textarea'],
            PAField::FIELD_TYPE_TEXTAREA_RICH => $this->module->language['textarea_rich'],
            PAField::FIELD_TYPE_TEXT => $this->module->language['text'],
            PAField::FIELD_TYPE_RADIO => $this->module->language['radio'],
            PAField::FIELD_TYPE_CHECKBOX => $this->module->language['checkbox'],
            PAField::FIELD_TYPE_SWITCH => $this->module->language['switch'],
            PAField::FIELD_TYPE_SELECT => $this->module->language['select']
        );

        return $types[$param];
    }

    public function ajaxProcessUpdatePositions()
    {
        if ($this->access('edit')) {
            $way = (int)Tools::getValue('way');
            $id_paf_field = (int)Tools::getValue('id');
            $positions = Tools::getValue('paf_field');

            $new_positions = array();
            foreach ($positions as $v) {
                if (!empty($v)) {
                    $new_positions[] = $v;
                }
            }

            foreach ($new_positions as $position => $value) {
                $pos = explode('_', $value);

                if (isset($pos[2]) && (int)$pos[2] === $id_paf_field) {
                    if ($paf_field = new PAField((int)$pos[2])) {
                        if (isset($position) && $paf_field->updatePosition($way, $position, $id_paf_field)) {
                            echo 'ok position ' . (int)$position . ' for field ' . (int)$pos[1] . '\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update field ' .
                                (int)$id_paf_field . ' to position ' . (int)$position . ' "}';
                        }
                    } else {
                        echo '{"hasError" : true, "errors" : "This field (' .
                            (int)$id_paf_field . ') can t be loaded"}';
                    }
                    break;
                }
            }
        }
    }
}
