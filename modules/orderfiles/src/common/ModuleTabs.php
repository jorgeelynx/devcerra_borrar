<?php
/**
 * Orderfiles Prestashop module
 *
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Wiktor Koźmiński
 *  @copyright 2017-2017 Silver Rose Wiktor Koźmiński
 *  @license   LICENSE.txt
 */

namespace Orderfiles\Common;

class ModuleTabs
{
    public static $moduleName = 'orderfiles';

    public static function install()
    {
        $parentId = \Tab::getIdFromClassName('AdminParentModules');
        // if (!$parentId) {
        //     return false;
        // }

        self::add('OrderFiles', 'AdminOrderfilesMain', $parentId ? $parentId : -1);
        return true;
    }

    private static function add($name, $className, $parent = 0)
    {
        // if (\Tab::getIdFromClassName($className)) {
        //     return 0;
        // }

        $tab = new \Tab();
        $tab->active = 1;
        $tab->name = array();

        foreach (\Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $name;
        }

        $tab->class_name = $className;
        $tab->id_parent = $parent;
        $tab->module = self::$moduleName;
        $tab->add();

        return $tab->id;
    }

    /**
     * Uninstall tabs.
     * @return boolean
     */
    public static function uninstall()
    {
        $tabs = array(
            'AdminOrderfilesMain',
        );

        foreach ($tabs as $tabName) {
            $tab = \Tab::getInstanceFromClassName($tabName);
            if ($tab) {
                $tab->delete();
            }
        }

        return true;
    }
}
