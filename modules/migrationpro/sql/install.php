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

$sql = array();

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'migrationpro_data`;
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'migrationpro_data` (
`id_data` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(3) NOT NULL,
  `source_id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  PRIMARY KEY (`id_data`),
  UNIQUE KEY `type_source_id` (`type`,`source_id`),
  KEY `type` (`type`)
)';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'migrationpro_mapping`;
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'migrationpro_mapping` (
`id_mapping` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `local_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_mapping`)
)';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'migrationpro_process`;
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'migrationpro_process` (
`id_process` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `total` int(11) NOT NULL,
  `imported` int(11) NOT NULL,
  `id_source` int(11) NOT NULL,
  `error` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `time_start` timestamp NOT NULL,
  `finish` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_process`)
)';

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'migrationpro_migrated_data`;
CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'migrationpro_migrated_data` (
`id_data` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(100) NOT NULL,
  `source_id` int(11) NOT NULL,
  `local_id` int(11) NOT NULL,
  PRIMARY KEY (`id_data`),
  UNIQUE KEY `entity_type_source_id` (`entity_type`,`source_id`)
)';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
