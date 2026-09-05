<?php
require_once("../Mym/Common.php");
require_once("../Mym/Mym_Class/AlipayService.php");
if(!$_REQUEST){
    $_REQUEST=file_get_contents('php://input');
}
file_put_contents('log.txt',"\r\n\r\n".date("Y-m-d H:i:s")."\r\n".json_encode($_REQUEST),FILE_APPEND);
$trade_no = daddslashes($_REQUEST['out_trade_no']);
$srow = $DB->query("SELECT * FROM pay_order WHERE trade_no='{$trade_no}' limit 1")->fetch();
$userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$srow['pid']}' limit 1")->fetch();
$userrow_pid=$DB->query("SELECT * FROM pay_dmf WHERE id='{$srow['qr_id']}' limit 1")->fetch();
if(!$srow) exit('该订单号不存在，请返回来源地重新发起请求！');
header('Content-type:text/html; Charset=utf-8');
$alipayPublicKey=$userrow_pid['f2fkey'];//支付宝公钥
$aliPay = new AlipayService_notify($alipayPublicKey);
//验证签名
$result = $aliPay->rsaCheck($_REQUEST,$_REQUEST['sign_type']);
if($result===true && $_REQUEST['trade_status'] == 'TRADE_SUCCESS'){
	Add_log($srow['pid'],'自动回调当面付订单(免挂回调)：'.$trade_no);
	$api_url = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST']."/";
	$msgpay = '尊敬的用户 PID:'.$srow['pid'].'你好<br/><br/>您本次收收款金额为'.$srow['money'].'元<br/><br/>于'.$date.'收款到账<br/><br/>商品名称:'.$srow['name'].'<br/><br/>类型:支付宝当面付<br/><br/>订单号:'.$srow['trade_no'].'<br/><br/>地址:'.$api_url.'<br/>有问题请联系站长QQ'.$conf['qq'];
	if($userrow_pid['email'])$email=$userrow_pid['email'];else$email=$userrow_pid['qq'].'@qq.com';
	if($userrow_pid['yesmail']==1)$send_res = send_mail($userrow_pid,$email, $conf['sitename'].'- 收款到账提醒', $msgpay);
	$url=creat_callback($srow);
	$data=curl_get($url['notify']);
	pay_notify($userrow['pid'],'alipay',$srow['money'],$data,$srow['trade_no']);
}else{
    echo "验证失败";
}
