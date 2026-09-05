DROP TABLE IF EXISTS `pay_config`;
CREATE TABLE `pay_config` (
  `k` varchar(32) NOT NULL,
  `v` text,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `pay_config` (`k`, `v`) VALUES
('version', '1410'),
('admin_user', '21232f297a57a5a743894a0e4a801fc3'),
('admin_pass', 'e10adc3949ba59abbe56e057f20f883e'),
('gonggao', '欢迎使用本支付,有问题请咨询客服处理'),
('sitename', 'MYM码支付'),
('title', 'MYM码支付_支付宝免签约_微信免签_QQ钱包免签约接口_优云宝_秒冲宝_码支付'),
('keywords', 'Mymcode二维码Pay系统-支付免签约支付,个人支付宝即时到账接口,支付宝免签约接口,支付宝即时到账接口,微信免签接口,微信免签,支付宝辅助收款,API支付对接,码支付,Mpay支付,优云宝_秒冲宝,MYM码支付官网'),
('description', 'Mymcode二维码Pay系统-支付免签支付专为个人、企业收款而生的支付工具。为支付宝、微信支付的个人账户、企业账号，提供即时到账收款API。安全可靠，费率低。'),
('mail_type', '本地发件二'),
('mail_smtp', ''),
('mail_port', '25'),
('mail_name', ''),
('mail_pwd', ''),
('webwh', '关闭维护'),
('blockname', '百度云|摆渡|云盘|点券|芸盘|萝莉|罗莉|网盘|黑号|q币|Q币|扣币|qq货币|QQ货币|花呗|baidu云|bd云|吃鸡|透视|自瞄|后座|穿墙|脚本|外挂|模拟|辅助|检测|武器|套装'),
('blockalert', '温馨提醒该商品禁止出售，如有疑问请联系网站客服！'),
('captcha_id', 'b31335edde91b2f98dacd393f6ae6de8'),
('captcha_key', '170d2349acef92b7396c7157eb9d8f47'),
('pay_work_name', '<option value=\"支付问题\">支付问题</option>\n<option value=\"网站问题\">网站问题</option>\n<option value=\"二维码登录问题\">二维码登录问题</option>\n<option value=\"功能问题\">功能问题</option>\n<option value=\"发现BUG\">发现BUG</option>\n<option value=\"只想和你唠唠嗑\">只想和你唠唠嗑</option>'),
('reg_open', '1'),
('reg_pay', '0'),
('test_open', '1'),
('cronkey', 'Mymcode'),
('footer', 'Mymcode码支付'),
('captcha_open_login', '1'),
('login_qq', '2'),
('qq', '2945080486'),
('template', 'default'),
('proxy', '0'),
('proxy_server', ''),
('proxy_port', ''),
('proxy_user', ''),
('proxy_pwd', ''),
('login_qq_appid', ''),
('login_qq_appkey', ''),
('ail_cloud', '0'),
('wxapi', 'http://leszimacyd.8b2.com'),
('reg_email', '1'),
('qq_qun', ''),
('reg_money', '100'),
('reg_type', '10'),
('ed_type', '1'),
('ed_money', '100'),
('reg_pay_price', ''),
('zero_pid', ''),
('ail_cloud_api', ''),
('qq_cloud', '0'),
('mail_cloud', '0'),
('mail_apiuser', ''),
('mail_apikey', ''),
('mail_name2', ''),
('mail_recv', ''),
('qq_cloud_api', ''),
('wxip', '');

DROP TABLE IF EXISTS `pay_alidata`;
CREATE TABLE IF NOT EXISTS `pay_alidata` (
  `qr_id` int(5) NOT NULL,
  `pid` int(11) DEFAULT NULL,
  `appid` varchar(64) DEFAULT NULL,
  `appkey` text,
  `appkey2` text,
  PRIMARY KEY (`qr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_dmf`;
CREATE TABLE `pay_dmf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `f2fid` varchar(32) DEFAULT NULL,
  `f2fkey` varchar(688) DEFAULT NULL,
  `f2fpublic` varchar(1688) DEFAULT NULL,
  `beizhu` varchar(32) DEFAULT NULL,
  `nums` int(11) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_daili`;
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


DROP TABLE IF EXISTS `pay_yund`;
CREATE TABLE IF NOT EXISTS `pay_yund` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(1) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `url` varchar(64) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `pay_log`;
CREATE TABLE `pay_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(188) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_notice`;
CREATE TABLE `pay_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL COMMENT '公告标题',
  `datatxt` varchar(488) DEFAULT NULL COMMENT '公告内容',
  `color` varchar(20) DEFAULT NULL COMMENT '公告颜色',
  `sort` int(5) DEFAULT NULL COMMENT '公告排序',
  `status` int(1) DEFAULT '1' COMMENT '状态',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;


INSERT INTO `pay_notice` (`id`, `title`, `datatxt`, `color`, `sort`, `status`, `addtime`) VALUES
(1, '联系客服', '有问题请联系QQ2945080486', '#0052cc', 50, 1, '2022-04-28 13:00:39'),
(2, '警告', '本平台严禁一切淫秽、涉赌、政治、钓鱼、诈骗、理财、借贷、封建迷信等非法网站接入使用！新站点：https://mzf.5v1.com/', '#ff0000', 50, 1, '2021-06-25 12:59:31'),
(3, '快速指南', '添加收款码，并扫码登入CK状态，即可开启即时到账收款！', '#0052cc', 50, 1, '2021-06-25 13:00:39'),
(4, '注意事项', '微信添加过二维码后不可改昵称，支付宝需要关闭余额自动转到余额宝，详见使用官网帮助https://g.9o3.cn/。', '#0052cc', 50, 1, '2021-06-25 13:00:55');


DROP TABLE IF EXISTS `pay_notify`;
CREATE TABLE `pay_notify` (
  `trade_no` varchar(64) NOT NULL,
  `pid` varchar(32) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `pay_msg` varchar(300) DEFAULT '',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`trade_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_order`;
CREATE TABLE `pay_order` (
  `trade_no` varchar(64) NOT NULL,
  `out_trade_no` varchar(64) DEFAULT NULL,
  `api_trade_no` varchar(64) DEFAULT NULL,
  `notify_url` varchar(288) DEFAULT NULL,
  `return_url` varchar(288) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `pid` varchar(14) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `qr_id` varchar(255) DEFAULT NULL,
  `qr_url` varchar(500) DEFAULT NULL,
  `pay_id` varchar(32) DEFAULT NULL,
  `apitime` varchar(32) DEFAULT NULL,
  `outtime` varchar(32) DEFAULT NULL,
  `addtime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  `date` date DEFAULT NULL,
  PRIMARY KEY (`trade_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_package`;
CREATE TABLE `pay_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `days` int(11) DEFAULT '1',
  `quota` int(11) DEFAULT '100',
  `price` decimal(10,2) DEFAULT '0.00',
  `introduce` text,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_plug`;
CREATE TABLE `pay_plug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) DEFAULT NULL COMMENT '类型',
  `name` varchar(20) DEFAULT NULL COMMENT '名称',
  `logimg` varchar(200) DEFAULT NULL COMMENT 'logo照片',
  `title` varchar(300) DEFAULT NULL COMMENT '介绍内容',
  `author` varchar(5) DEFAULT NULL COMMENT '插件作者',
  `download` varchar(400) DEFAULT NULL COMMENT '下载地址',
  `time` datetime DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_qrlist`;
CREATE TABLE `pay_qrlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` varchar(32) DEFAULT NULL,
  `qr_id` varchar(200) DEFAULT NULL,
  `qr_url` varchar(1000) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  `beizhu` varchar(32) DEFAULT NULL,
  `wx_name` varchar(50) DEFAULT NULL,
  `pay_user` varchar(88) DEFAULT '0',
  `pay_pass` varchar(88) DEFAULT '0',
  `data_data` varchar(88) DEFAULT NULL,
  `cookie` text DEFAULT NULL,
  `json` text DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  `nums` int(11) DEFAULT '0',
  `crontime` varchar(32) DEFAULT '0',
  `hook_type` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `email_status` int(1) DEFAULT '0',
  `Order_time` varchar(32) DEFAULT '0',
  `uin` varchar(64) DEFAULT '0',
  `channel` varchar(64) DEFAULT NULL,
  `qr_status` int(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_regcode`;
CREATE TABLE `pay_regcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(11) DEFAULT NULL COMMENT '类型',
  `code` varchar(32) DEFAULT NULL COMMENT '验证码',
  `to` varchar(20) DEFAULT NULL COMMENT '邮箱地址',
  `time` int(11) DEFAULT NULL COMMENT '间隔时间',
  `ip` varchar(20) DEFAULT NULL COMMENT '发送者IP',
  `data` varchar(88) DEFAULT NULL COMMENT '注册订单数据',
  `trade_no` varchar(32) DEFAULT NULL COMMENT '订单号',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_risk`;
CREATE TABLE `pay_risk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(15) NOT NULL DEFAULT '0',
  `type` int(1) NOT NULL DEFAULT '0',
  `url` varchar(64) DEFAULT NULL,
  `content` varchar(64) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_taocan`;
CREATE TABLE `pay_taocan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL COMMENT '名称',
  `edu` decimal(10,2) DEFAULT '0.00' COMMENT '购买额度',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `time` int(1) NOT NULL DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_user`;
CREATE TABLE `pay_user` (
  `pid` varchar(32) NOT NULL,
  `key` varchar(32) DEFAULT NULL,
  `user` varchar(32) DEFAULT NULL,
  `pass` varchar(32) DEFAULT NULL,
  `email` varchar(32) DEFAULT NULL,
  `qq` varchar(10) DEFAULT NULL,
  `social_uid` varchar(32) DEFAULT NULL,
  `nickname` varchar(32) DEFAULT NULL,
  `money` decimal(10,2) DEFAULT NULL,
  `outtime` int(10) DEFAULT NULL,
  `pay_template` varchar(32) DEFAULT 'default',
  `alipay_pay_open` int(1) DEFAULT '0',
  `qqpay_pay_open` int(1) DEFAULT '0',
  `wxpay_pay_open` int(1) DEFAULT '0',
  `alipay_api_url` varchar(64) DEFAULT NULL,
  `alipay_api_pid` varchar(32) DEFAULT NULL,
  `alipay_api_key` varchar(32) DEFAULT NULL,
  `qqpay_api_url` varchar(64) DEFAULT NULL,
  `qqpay_api_pid` varchar(32) DEFAULT NULL,
  `qqpay_api_key` varchar(32) DEFAULT NULL,
  `wxpay_api_url` varchar(64) DEFAULT NULL,
  `wxpay_api_pid` varchar(32) DEFAULT NULL,
  `wxpay_api_key` varchar(32) DEFAULT NULL,
  `user_vip_time` datetime DEFAULT NULL,
  `user_vip` int(1) DEFAULT '0',
  `addtime` datetime DEFAULT NULL,
  `email_status` int(11) DEFAULT '0',
  `status` int(1) DEFAULT '1',
  `money_mail` int(1) DEFAULT '0',
  `type` int(11) DEFAULT '3',
  `mali` int(1) DEFAULT '0',
  `music` int(1) DEFAULT '1',
  `free` int(1) DEFAULT '0',
  `Order_Money` int(1) DEFAULT '0',
  `pay_pass` varchar(64) DEFAULT '',
  `pay_tzqq` int(1) DEFAULT '0',
  `pay_tzali` int(1) DEFAULT '0',
  `pay_tzwx` int(1) DEFAULT '0',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `pay_wechat_trumpet`;
CREATE TABLE `pay_wechat_trumpet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wx_user` varchar(50) DEFAULT NULL,
  `wx_name` varchar(50) DEFAULT NULL,
  `beizhu` varchar(488) DEFAULT NULL,
  `cookie` text,
  `login_time` bigint(20) DEFAULT NULL,
  `sort` int(5) DEFAULT NULL,
  `hook_type` int(1) DEFAULT '0',
  `status` int(1) DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;