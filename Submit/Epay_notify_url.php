<?php
require_once('../Mym/Common.php');
$out_trade_no=daddslashes($_GET['out_trade_no']);
$sitename=base64_decode(daddslashes($_GET['sitename']));
$srow=$DB->query("SELECT * FROM pay_order WHERE trade_no='{$out_trade_no}' limit 1")->fetch();
if(!$srow)sysmsg('该订单号不存在，请返回来源地重新发起请求！');
$userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$srow['pid']}' limit 1")->fetch();

//$epay_url = $conf['local_domain'];

//计算得出通知验证结果
$verify_result = verifyNotify($userrow[$srow['type'].'_api_key']);
if($verify_result) {//验证成功	
	//商户订单号
	$out_trade_no = daddslashes($_GET['out_trade_no']);
	//支付宝交易号
	$trade_no = daddslashes($_GET['trade_no']);
	//交易状态
	$trade_status = daddslashes($_GET['trade_status']);
	//支付方式
	$type = daddslashes($_GET['type']);

    if($_GET['trade_status'] == 'TRADE_SUCCESS' && $srow['status']==0) { //=0则是官方通道
			//发送通知给商户平台
			Add_log($srow['pid'],'掉线对接易支付自动回调订单：'.$trade_no);
			$url=creat_callback($srow);
			$data=get_curl($url['notify']);
			//exit($data);
    }
    echo "success";		//请不要修改或删除
}else{
    echo "验证失败";
}
?>