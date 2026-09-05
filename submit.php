<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>正在为您跳转到支付页面，请稍候...</title>
    <style type="text/css">
        body {margin:0;padding:0;}
        p {position:absolute;
            left:50%;top:50%;
            width:500px;height:50px;
            margin:-60px 0 0 -260px;
            padding:50px;font:bold 28px/60px "宋体", Arial;
            background:#f9fafc url(Mym/Assets/lmg/load.gif) no-repeat 20px 26px;
            text-indent:22px;border:1px solid #c5d0dc;}
        #waiting {font-family:Arial;}
    </style>
<?php
require './Mym/Common.php'; 
@header('Content-Type: text/html; charset=UTF-8');

if(isset($_GET['pid'])){
	$queryArr=$_GET;
}else{
	$queryArr=$_POST;
}

$pid=intval($queryArr['pid']);
$type=daddslashes($queryArr['type']);
$money=daddslashes($queryArr['money']);
if(empty($pid))sysmsg('PID不存在');
$time = time();
$userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
if($userrow['money']<=0)sysmsg('今日额度已不足，请联系客服QQ：'.$userrow['qq'].'进行处理');
$out_trade_no=daddslashes($queryArr['out_trade_no']);
$notify_url=strip_tags(daddslashes($queryArr['notify_url']));
$return_url=strip_tags(daddslashes($queryArr['return_url']));
$name=strip_tags(daddslashes($queryArr['name']));
$sitename=urlencode(base64_encode(daddslashes($queryArr['sitename'])));

if(empty($out_trade_no))sysmsg('订单号(out_trade_no)不能为空');
if(empty($notify_url))sysmsg('通知地址(notify_url)不能为空');
if(empty($return_url))sysmsg('回调地址(return_url)不能为空');
if(empty($name))sysmsg('商品名称(name)不能为空');
if(empty($money))sysmsg('金额(money)不能为空');
if($money<=0 || !is_numeric($money))sysmsg('金额不合法');
if(!preg_match('/^[a-zA-Z0-9.\_\-]+$/',$out_trade_no))sysmsg('订单号(out_trade_no)格式不正确');
use \lib\PayUtils;
$prestr=PayUtils::createLinkstring(PayUtils::argSort(PayUtils::paraFilter($queryArr)));
if(!PayUtils::md5Verify($prestr, $queryArr['sign'], $userrow['key']))sysmsg('签名校验失败，请返回重试！');
if($userrow['status']!=1)sysmsg("商户已被封禁，由于您违规操作导致账号封禁，详情请联系管理QQ".$conf['qq']);
	$login_time = time()-30;
	if($type=='wxpay'){
	    if($userrow[$type.'_pay_open']==0){
	        $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `hook_type`='0' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' and `json` like '%custom_qr_url%' order by nums asc limit 1")->fetch();
	        if(!$QR_row)$QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `hook_type`='0' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' order by nums asc limit 1")->fetch();
	        if($QR_row && $QR_row['hook_type']==0){
	           $login_time = time();
			   $IS_QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `qr_status`='1' and `status`='1' and `pid`='{$userrow['pid']}' and hook_type='1' order by nums asc limit 1")->fetch();
			   if(!$IS_QR_row){//查询是否有免挂版在线
				  $login_wxpay = $DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `status`='1' and `login_time`>='{$login_time}' and `wx_name`='{$QR_row['wx_name']}' limit 1")->fetch();
				  if(!$login_wxpay)sysmsg('<h2>微信绑定的店员软件掉线,请重试,若再不行请联系站长处理哦<h2>');
			   }
	        }else{
	            $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `qr_status`='1' and `status`='1' and `pid`='{$userrow['pid']}' order by nums asc limit 1")->fetch();
	            if(!$QR_row){
	                sysmsg('<h2>微信支付下单失败,暂无收款账户,或掉线<h2>');
	            }
	            
	        }
	    }elseif($userrow['wxpay_pay_open']==2){
	        $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' and `json` like '%custom_qr_url%' order by nums asc limit 1")->fetch();
	        if(!$QR_row)$QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' order by nums asc limit 1")->fetch();
	        if($QR_row && $QR_row['hook_type']==0){
	           $login_time = time();
			   $IS_QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `qr_status`='1' and `status`='1' and `pid`='{$userrow['pid']}' and hook_type='1' order by nums asc limit 1")->fetch();
			   if(!$IS_QR_row){//查询是否有免挂版在线
				  $login_wxpay = $DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `status`='1' and `login_time`>='{$login_time}' and `wx_name`='{$QR_row['wx_name']}' limit 1")->fetch();
				  if($login_wxpay)$userrow['wxpay_pay_open']=0;
			   }
	        }else{
	            $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `qr_status`='1' and `status`='1' and `pid`='{$userrow['pid']}' order by nums asc limit 1")->fetch();
	            if($QR_row){
	                $userrow['wxpay_pay_open']=0;
	            }
	        }
	    }else{
	        $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' order by nums asc limit 1")->fetch();
	    }
	}elseif($type=='alipay'){
	    $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' and `json` like '%custom_qr_url%' order by nums asc limit 1")->fetch();
	    if(!$QR_row)$QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' and (hook_type!='0' or crontime>'{$login_time}') order by nums asc limit 1")->fetch();
	    if($userrow['alipay_pay_open']==0){
	        if(!$QR_row)sysmsg('<h2>支付宝支付下单失败,暂无收款账户,或掉线<h2>');
	        if($QR_row['hook_type']==1 and $QR_row['crontime']<$login_time)sysmsg('<h2>支付宝支付下单失败,暂无收款账户,或掉线<h2>');
	    }elseif($userrow['alipay_pay_open']==2){
	       if($QR_row)$userrow['alipay_pay_open']=0;
	    }elseif($userrow['alipay_pay_open']==3){
	        if($QR_row and $QR_row['crontime']>$login_time){
	            $userrow['alipay_pay_open']=0;
	        }else{
	            $Dmf_row=$DB->query("SELECT * FROM pay_dmf WHERE `pid`='{$userrow['pid']}' order by nums asc limit 1")->fetch();
	            if(!$Dmf_row)sysmsg('<h2>支付宝支付下单失败,暂无收款账户,或掉线<h2>');
	        }
	    }else{
	        $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' order by nums asc limit 1")->fetch();
	    }
	}elseif($type=='qqpay'){
	    $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `crontime`>'{$login_time}' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' and `json` like '%custom_qr_url%' order by nums asc limit 1")->fetch();
	    if(!$QR_row)$QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `crontime`>'{$login_time}' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' order by nums asc limit 1")->fetch();
	    if($userrow['qqpay_pay_open']==0){
	        if(!$QR_row){
	            sysmsg('<h2>QQ钱包支付下单失败,暂无收款账户,或掉线1<h2>');
	        }elseif($QR_row['crontime']<$login_time){
	            sysmsg('<h2>QQ钱包支付下单失败,暂无收款账户,或掉线2<h2>');
	        }
	    }elseif($userrow['qqpay_pay_open']==2){
	        if($QR_row)$userrow['qqpay_pay_open']=0;
	    }else{
	        $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' order by nums asc limit 1")->fetch();
	    }
	}elseif($type=='usdt'){
	    if(!$QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='{$type}' and `pid`='{$userrow['pid']}' and `qr_status`='1' and `status`='1' order by nums asc limit 1")->fetch()){
	        sysmsg('<h2>USDT-TRC20下单失败,暂无收款账户,或掉线<h2>');
	    }
	}
	if(!mym_assert_qr_row_for_type($QR_row,$type)){
	    sysmsg('<h2>支付通道配置异常：所选通道与支付类型不匹配，请检查通道配置<h2>');
	}

	//记录设备调用排序
	$QR_rowa = $DB->exec("update `pay_qrlist` set `nums` ='1' where `id`='{$QR_row['id']}' and `qr_status`='1'");
	//最后一张可用的二维码
	$QR_is=$DB->query("SELECT * FROM `pay_qrlist` WHERE `status`='1' and `type`='{$type}' and `pid`='{$userrow['pid']}' and `nums`='0' and `id`!='{$QR_row['id']}' and `qr_status`='1' order by nums asc limit 1")->fetch();
	//调用到最后一个设备可用后重置所有二维码调用排序次数
	if(!$QR_is)$DB->exec("update `pay_qrlist` set `nums`='0' WHERE `type`='{$type}' and `pid`='{$userrow['pid']}'");
	//当面付轮训调用
	if($userrow[$type.'_pay_open']==3 and $QR_row['status']==0){
	   $DB->exec("update `pay_dmf` set `nums` =`nums`+'1' where `id`='{$Dmf_row['id']}'");
	   $Dmf_is=$DB->query("SELECT * FROM `pay_dmf` WHERE `status`='1' and `pid`='{$userrow['pid']}' order by addtime desc limit 1")->fetch();
	if($Dmf_row['id']==$Dmf_is['id'])$DB->exec("update `pay_dmf` set `nums`='0' WHERE `pid`='{$userrow['pid']}'");
	   $QR_row['id']=$Dmf_row['id'];
	}
	$outtime=$userrow['outtime']?$userrow['outtime']:$conf['outtime'];//订单过期时间设定
	$outtime=time()+($outtime<180?180:$outtime);
	$time = time();
	if($type=='usdt'){
	    $srow['money'] = usdt('usdt',$money);
	    $outtime=time()+1200;
	}else{
	    $srow['money'] = $money;
	}
	
	if($QR_row['hook_type']==1){
	    $price=$DB->query("SELECT * FROM pay_order WHERE price='{$srow['money']}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1")->fetch();
	}else{
	    $price=$DB->query("SELECT * FROM pay_order WHERE price='{$srow['money']}' and qr_id='{$QR_row['id']}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1")->fetch();
	}
	if(!$price){
	    if($type!='usdt'){
	        $srow['money'] = $money;
	    }
	}else{
	    
		//循环匹配递增金额
		$num = 1;
		for ($x=0; $x<=$num; $x++) {
			$srow['money'] = $srow['money'] + 0.01;//每次增加0.01
			if($QR_row['hook_type']==1){
			    $Sql = "SELECT * FROM pay_order WHERE price='{$srow['money']}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1";
			}else{
			    $Sql = "SELECT * FROM pay_order WHERE price='{$srow['money']}' and qr_id='{$QR_row['id']}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1";
			}
			$price=$DB->query($Sql)->fetch();
			if(!$price){
				$srow['money'] = $srow['money'];
				$num = 0;//跳出(停止)循环
			}else{
				$num = $num+1;;//继续循环匹配
			}
		} 
	}
	
	$price = $srow['money'];
	$trade_no=date("YmdHis").rand(11111,99999);
	$apitime=time()+10;//超过此时间则放弃此二维码
	
	$data = qrdecode($QR_row,$price,$trade_no);
	if(is_array($data) && isset($data['code']) && intval($data['code'])==-1){
	    sysmsg('<h2>'.htmlspecialchars($data['msg']).'<h2>');
	}
	$qr_url = $data['qr_url'];
	$api_trade_no = $data['api_trade_no'];
	if(!$api_trade_no)$api_trade_no=NULL;
	if(!$qr_url && !($type=='qqpay' && $QR_row['hook_type']==0 && $QR_row['channel']=='mg_qq'))$qr_url = urlencode($QR_row['qr_url']);
	if(!$DB->query("insert into `pay_order` (`trade_no`,`out_trade_no`,`api_trade_no`,`notify_url`,`return_url`,`type`,`pid`,`addtime`,`name`,`money`,`qr_id`,`price`,`pay_id`,`qr_url`,`apitime`,`outtime`,`status`,`date`) values ('".$trade_no."','".$out_trade_no."','".$api_trade_no."','".$notify_url."','".$return_url."','".$type."','".$pid."','".$date."','".$name."','".$money."','".$QR_row['id']."','".$price."','".$ip."','".$qr_url."','".$apitime."','".$outtime."','0','".date("Y-m-d")."')"))sysmsg('创建订单失败，请返回重试！');

echo'<p>正在为您跳转到支付页面，请稍候...</p></body></html>';

if($QR_row['status']==0 and $userrow[$type.'_pay_open']==2)
{
	$DB->query("update `pay_order` set `price` ='{$money}' where `trade_no`='{$trade_no}'");
	exit("<script>window.location.href='Submit/Epay_Api.php?trade_no={$trade_no}&sitename={$sitename}';</script>");
}elseif($QR_row['status']==1 and $userrow[$type.'_pay_open']==2 and $type=='wxpay'){
    $DB->query("update `pay_order` set `price` ='{$money}' where `trade_no`='{$trade_no}'");
	exit("<script>window.location.href='Submit/Epay_Api.php?trade_no={$trade_no}&sitename={$sitename}';</script>");
}
if($QR_row['status']==0 and $userrow[$type.'_pay_open']==3)
{
	$DB->query("update `pay_order` set `price` ='{$money}' where `trade_no`='{$trade_no}'");
	exit("<script>window.location.href='Submit/Mym_Pay.php?act=dmf&trade_no={$trade_no}';</script>");
}
if($type){
	exit("<script>window.location.href='Submit/Mym_Pay.php?trade_no={$trade_no}';</script>");
}else{
	echo "<script>window.location.href='Submit/default.php?trade_no={$trade_no}&sitename={$sitename}';</script>";
}

?>