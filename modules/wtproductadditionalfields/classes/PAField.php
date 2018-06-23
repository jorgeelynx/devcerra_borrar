<?php
/**
 * 2017 WeeTeam
 *
 * @author    WeeTeam <info@weeteam.net>
 * @copyright 2017 WeeTeam
 * @license   http://www.gnu.org/philosophy/categories.html (Shareware)
 */

class PAField extends ObjectModel
{
    public $id_paf_field;
    public $type = 2;
    public $multiple = 0;
    public $active = 0;
    public $position = 0;

    public $name;
    public $description;
    public $value_default;

    const FIELD_TYPE_TEXT = 1;
    const FIELD_TYPE_TEXTAREA = 2;
    const FIELD_TYPE_TEXTAREA_RICH = 3;
    const FIELD_TYPE_SWITCH = 4;
    const FIELD_TYPE_RADIO = 5;
    const FIELD_TYPE_CHECKBOX = 6;
    const FIELD_TYPE_SELECT = 7;
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'paf_field',
        'primary' => 'id_paf_field',
        'multilang' => true,
        'fields' => array(
            'type' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'multiple' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'name' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isString',
                'size' => 254
            ),
            'description' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 65534
            ),
            'value_default' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml'
            ),
        ),
        /*'associations' => array(
            'values' => array(
                'type' => self::HAS_MANY,
                'field' => 'id_paf_field_value',
                'association' => 'paf_field_value',
                'object' => 'PAFieldValue',
            ),
        ),*/
    );

    public function delete()
    {
        $values = $this->getPAFieldValue();
        foreach ($values as $value) {
            /* @var PAFieldValue $value */
            $value->delete();
        }
        $products = $this->getPAFieldProductValues();
        foreach ($products as $product) {
            /* @var PAFieldProductValue $product */
            $product->delete();
        }
        return parent::delete();
    }

    public function getPAFieldValue()
    {
        return PAFieldValue::getPAFieldValueByPAField($this->id);
    }

    public function getPAFieldProductValues($id_product = null)
    {
        return PAFieldProductValue::getPAFieldProductValues($this->id, $id_product);
    }

    public function updatePosition($way, $position, $idFeature = null)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT `position`, `id_paf_field`
			FROM `' . _DB_PREFIX_ . 'paf_field`
			WHERE `id_paf_field` = ' . (int)($idFeature ? $idFeature : $this->id) . '
			ORDER BY `position` ASC')
        ) {
            return false;
        }

        foreach ($res as $paf_field) {
            if ((int)$paf_field['id_paf_field'] == (int)$this->id) {
                $moved_paf_field = $paf_field;
            }
        }

        if (!isset($moved_paf_field) || !isset($position)) {
            return false;
        }

        return (Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'paf_field`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way
                    ? '> ' . (int)$moved_paf_field['position'] . ' AND `position` <= ' . (int)$position
                    : '< ' . (int)$moved_paf_field['position'] . ' AND `position` >= ' . (int)$position))
            && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'paf_field`
			SET `position` = ' . (int)$position . '
			WHERE `id_paf_field`=' . (int)$moved_paf_field['id_paf_field']));
    }

    public function preActionSave()
    {
        $languages = Language::getLanguages();
        if ($this->type < 4) {
            foreach ($languages as $language) {
                $this->value_default[$language['id_lang']] =
                    Tools::getValue('value_default_' . $this->type . '_' . $language['id_lang']);
            }
        } elseif ($this->type == 4 && !(int)Tools::getValue('multiple')) {
            foreach ($languages as $language) {
                $this->value_default[$language['id_lang']] = (int)Tools::getValue('value_default_' . $this->type);
            }
        }
    }

    public function postActionSave()
    {
        $paf_values = Tools::getValue('pafield');
        $languages = Language::getLanguages();
        switch ($this->type) {
            case PAField::FIELD_TYPE_TEXT:
            case PAField::FIELD_TYPE_TEXTAREA:
            case PAField::FIELD_TYPE_TEXTAREA_RICH:
                $paf_values_db = $this->getPAFieldValue();
                if ($paf_values_db) {
                    $paf_values = array(
                        array(
                            'value_default' => 1,
                            'id_paf_field_value' => $paf_values_db[0]->id_paf_field_value,
                            'label' => $this->value_default,
                        ),
                    );
                } else {
                    $paf_values = array(
                        array(
                            'value_default' => 1,
                            'id_paf_field_value' => 0,
                            'label' => $this->value_default,
                        ),
                    );
                }
                break;
            case PAField::FIELD_TYPE_SWITCH:
                if (!(int)Tools::getValue('multiple')) {
                    $paf_values_db = $this->getPAFieldValue();
                    if ($paf_values_db) {
                        $paf_values = array(
                            array(
                                'value_default' => 1,
                                'id_paf_field_value' => $paf_values_db[0]->id_paf_field_value,
                                'label' => $this->value_default,
                            ),
                        );
                    } else {
                        $label = array();
                        foreach ($languages as $language) {
                            $label[$language['id_lang']] = 'no label';
                        }
                        $paf_values = array(
                            array(
                                'value_default' => $this->value_default[Configuration::get('PS_LANG_DEFAULT')],
                                'id_paf_field_value' => 0,
                                'label' => $label,
                            ),
                        );
                    }
                }
                break;
            case PAField::FIELD_TYPE_RADIO:
            case PAField::FIELD_TYPE_SELECT:
                $value_default = Tools::getValue('pafield_value_default');
                foreach ($paf_values as $key => $item) {
                    $paf_values[$key]['value_default'] = ($value_default !== false && $value_default == $key) ? 1 : 0;
                }
                break;
            case PAField::FIELD_TYPE_CHECKBOX:
                break;

            default:
                return;
        }

        foreach ($paf_values as $key => $item) {
            $object = new PAFieldValue($item['id_paf_field_value']);
            $object->id_paf_field = $this->id;
            $object->value_default = isset($item['value_default']) ? $item['value_default'] : 0;
            $label_default = $item['label'][Configuration::get('PS_LANG_DEFAULT')];
            if (empty($label_default)) {
                foreach ($item['label'] as $label_lang) {
                    if ($label_lang) {
                        $label_default = $label_lang;
                        break;
                    }
                }
            }
            foreach ($languages as $language) {
                $object->label[$language['id_lang']] =
                    (!empty($item['label'][$language['id_lang']])) ?
                        $item['label'][$language['id_lang']] : $label_default;
            }
            if (Validate::isLoadedObject($object)) {
                if (!empty($item['remove']) || (empty($label_default) && $this->type > 3)) {
                    $object->delete();
                } else {
                    $object->update();

                    if ($this->type > 3) {
                        $product_values = PAFieldProductValue::getPAFieldProductValue($object->id_paf_field_value);
                        foreach ($product_values as $product_value) {
                            /* @var PAFieldProductValue $product_value */
                            $product_value->label = $object->label;
                            $product_value->save();
                        }
                    }


                }
            } elseif (empty($item['remove']) && (!empty($label_default) || $this->type < 4)) {
                $object->add();

                if ($this->type > 3) {
                    $select = Db::getInstance()->executeS(
                        'SELECT DISTINCT(`id_product`) FROM `' . _DB_PREFIX_ . 'paf_field_product`  ' .
                        'WHERE `id_paf_field` = ' . (int)$object->id_paf_field
                    );

                    foreach ($select as $select_item) {
                        $paf_product_value = new PAFieldProductValue();
                        $paf_product_value->id_paf_field_value = $object->id;
                        $paf_product_value->id_paf_field = $this->id;
                        $paf_product_value->id_product = $select_item['id_product'];
                        $paf_product_value->label = $object->label;
                        if ($this->type == PAField::FIELD_TYPE_SWITCH || $this->type == PAField::FIELD_TYPE_CHECKBOX) {
                            $paf_product_value->value_default = $object->value_default;
                        } else {
                            $paf_product_value->value_default = 0;
                        }
                        $paf_product_value->add();
                    }
                }
            }
        }
    }

    public function actionObjectPAFieldAddBefore()
    {
        if ($this->position <= 0) {
            $this->position = self::getHigherPosition() + 1;
        }
        $this->preActionSave();
    }

    public function actionObjectPAFieldAddAfter()
    {
        $this->postActionSave();
    }

    public function actionObjectPAFieldUpdateBefore()
    {

        $this->preActionSave();
    }

    public function actionObjectPAFieldUpdateAfter()
    {
        $this->postActionSave();

        /* echo "<pre>";
         print_r($_POST);
         echo "</pre>";
         die;*/

    }

    public function add($auto_date = true, $null_values = false)
    {
        $this->actionObjectPAFieldAddBefore();
        $result = parent::add($auto_date, $null_values);
        $this->actionObjectPAFieldAddAfter();
        return $result;
    }

    public function update($null_values = false)
    {
        $this->actionObjectPAFieldUpdateBefore();
        $result = parent::update($null_values);
        $this->actionObjectPAFieldUpdateAfter();
        return $result;

    }

    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `' . _DB_PREFIX_ . 'paf_field`';
        $position = DB::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }

    public static function getPAFields()
    {
        $result = array();
        $sql =
            "SELECT `id_paf_field` " .
            "FROM " . _DB_PREFIX_ . "paf_field " .
            "WHERE `active` = 1 ORDER BY `position`";
        $select = Db::getInstance()->executeS($sql);
        foreach ($select as $item) {
            $result[] = new PAField($item['id_paf_field']);
        }
        return $result;
    }

    public static function getDataProduct($id_product, $context = null)
    {
        if (!$context instanceof Context) {
            $context = Context::getContext();
        }
        $db = Db::getInstance();
        $sql = "SELECT pf.id_paf_field, pf.type, pf.multiple, pfl.name," .
            " pfp.value_default, pfp.id_paf_field_product, pfpl.label" .
            " FROM `" . _DB_PREFIX_ . "paf_field` pf" .
            " LEFT JOIN `" . _DB_PREFIX_ . "paf_field_lang` pfl ON(pf.`id_paf_field` = pfl.`id_paf_field`)" .
            " LEFT JOIN `" . _DB_PREFIX_ . "paf_field_product` pfp ON (pf.`id_paf_field` = pfp.`id_paf_field`)" .
            " LEFT JOIN `" . _DB_PREFIX_ . "paf_field_product_lang` pfpl" .
            " ON (pfp.`id_paf_field_product` = pfpl.`id_paf_field_product`)" .
            " WHERE pfp.`id_product` = " . (int)$id_product .
            " AND pfl.`id_lang` = " . (int)$context->language->id .
            " AND pfpl.`id_lang` = " . (int)$context->language->id .
            " AND pf.`active` = 1 ORDER BY pf.`position`";
        $select = $db->executeS($sql);
        $result = array();
        $value = array();
        foreach ($select as $item) {
            $value[$item['id_paf_field']][] = array(
                'value_default' => $item['value_default'],
                'label' => $item['label'],
                'id_paf_field_product' => $item['id_paf_field_product'],
            );
            $result[$item['id_paf_field']] = array(
                'type' => $item['type'],
                'multiple' => $item['multiple'],
                'name' => $item['name'],
                'values' => $value[$item['id_paf_field']],
            );
        }
        return $result;
    }
}
