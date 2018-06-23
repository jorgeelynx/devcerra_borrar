CREATE TABLE IF NOT EXISTS `_PREFIX_paf_field` (
  `id_paf_field` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL,
  `multiple` tinyint(1) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_paf_field`)
)ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_paf_field_lang` (
  `id_paf_field` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `value_default` text NOT NULL,
  PRIMARY KEY (`id_paf_field`,`id_lang`)
)ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_paf_field_value` (
  `id_paf_field_value`  int(11) NOT NULL AUTO_INCREMENT,
  `id_paf_field` int(11) NOT NULL,
  `value_default` int(1) NOT NULL,
  PRIMARY KEY (`id_paf_field_value`)
)ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_paf_field_value_lang` (
  `id_paf_field_value` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id_paf_field_value`,`id_lang`)
)ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_paf_field_product` (
  `id_paf_field_product`  int(11) NOT NULL AUTO_INCREMENT,
  `id_paf_field` int(11) NOT NULL,
  `id_paf_field_value` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `value_default` int(1) NOT NULL,
  PRIMARY KEY (`id_paf_field_product`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_paf_field_product_lang` (
  `id_paf_field_product` int(11) NOT NULL,
  `id_lang` int(11) NOT NULL,
  `label` text NOT NULL,
  PRIMARY KEY (`id_paf_field_product`,`id_lang`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;
