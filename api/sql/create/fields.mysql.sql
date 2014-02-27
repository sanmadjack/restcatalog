CREATE TABLE IF NOT EXISTS `fields` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `display` varchar(255) NOT NULL,
  `data_type` varchar(20) NOT NULL,
  `format` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;
