ALTER TABLE `pay_qrlist`
ADD COLUMN `channel` varchar(64) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `pay_daili` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `ip` varchar(200) DEFAULT NULL,
  `do` int(32) DEFAULT NULL,
  `user` varchar(200) DEFAULT NULL,
  `pass` varchar(200) DEFAULT NULL,
  `addtime` datetime NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;