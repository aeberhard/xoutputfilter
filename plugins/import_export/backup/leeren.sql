## Redaxo Database Dump Version 4
## Prefix rex_
## charset utf-8

DROP TABLE IF EXISTS `rex_420_xoutputfilter`;
CREATE TABLE `rex_420_xoutputfilter` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `typ` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `lang` int(11) NOT NULL DEFAULT '0',
  `marker` text NOT NULL,
  `html` text NOT NULL,
  `allcats` tinyint(1) NOT NULL DEFAULT '0',
  `subcats` tinyint(1) NOT NULL DEFAULT '0',
  `once` tinyint(1) NOT NULL DEFAULT '0',
  `categories` text NOT NULL,
  `insertbefore` tinyint(1) NOT NULL DEFAULT '0',
  `excludeids` text NOT NULL,
  `useragent` text NOT NULL,
  `dataarea` text NOT NULL,
  `validfrom` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `validto` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `rex_420_xoutputfilter` WRITE;
/*!40000 ALTER TABLE `rex_420_xoutputfilter` DISABLE KEYS */;


/*!40000 ALTER TABLE `rex_420_xoutputfilter` ENABLE KEYS */;
UNLOCK TABLES;

