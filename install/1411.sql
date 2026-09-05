INSERT INTO `pay_config` (`k`, `v`) VALUES
('paymali', '尊敬的用户 PID:[pid]你好&lt;br&gt;\n您本次收收款金额为[money]元&lt;br&gt;\n&lt;br&gt;\n于[date]收款到账&lt;br&gt;\n类型: 微信店员收款&lt;br&gt;\n商品名称:[name]&lt;br&gt;\n商品订单:[trade_no]&lt;br&gt;\n有问题请联系站长QQ[qq]&lt;br&gt;');

ALTER TABLE `pay_qrlist`
MODIFY COLUMN `qr_url` varchar(1000) DEFAULT NULL;