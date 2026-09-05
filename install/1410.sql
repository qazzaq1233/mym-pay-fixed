ALTER TABLE `pay_user`
ADD COLUMN `pay_template` varchar(32) DEFAULT 'default';

ALTER TABLE `pay_user`
ADD COLUMN `pay_tzqq` int(1) DEFAULT '0';

ALTER TABLE `pay_user`
ADD COLUMN `pay_tzali` int(1) DEFAULT '0';

ALTER TABLE `pay_user`
ADD COLUMN `pay_tzwx` int(1) DEFAULT '0';

ALTER TABLE `pay_order`
ADD COLUMN `api_trade_no` varchar(64) DEFAULT NULL;

ALTER TABLE `pay_qrlist`
ADD COLUMN `json` text DEFAULT NULL;

CREATE TABLE IF NOT EXISTS `pay_yund` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(1) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `url` varchar(64) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

UPDATE `pay_config` SET `v` = '21232f297a57a5a743894a0e4a801fc3' WHERE `pay_config`.`k` = 'admin_user';
UPDATE `pay_config` SET `v` = 'e10adc3949ba59abbe56e057f20f883e' WHERE `pay_config`.`k` = 'admin_pass';