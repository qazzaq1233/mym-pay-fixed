ALTER TABLE `pay_user`
DROP `user_vip`;
ALTER TABLE `pay_order`
ADD COLUMN `date` date DEFAULT '2023-06-01';
ALTER TABLE `pay_taocan`
ADD COLUMN `time` int(1) NOT NULL DEFAULT '1';
ALTER TABLE `pay_user`
ADD COLUMN `user_vip` int(1) DEFAULT '0';