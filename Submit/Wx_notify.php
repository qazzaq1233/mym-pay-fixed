<?php
include("../Mym/Common.php");
/*
 * 发起支付回调API
 *2021/09/03   by MYM qq: 485570653
 */
if(count($_GET))$queryArr=$_GET;else$queryArr=$_POST;
$ipip = explode(',',$conf['wxip']);
if(!in_array($ip,$ipip)){
	exit("IP验证不通过".$ip."，请在后台加快回调添加白名单");
}
switch(daddslashes($queryArr['act'])){
case 'FREEWXPAY':
    $name = daddslashes(trim($queryArr['name']));//商户pid
	$money = daddslashes(trim($queryArr['money']));//支付金额
	$wx_name = daddslashes(trim($queryArr['wx_name']));
	$Qr=$DB->query("SELECT * FROM `pay_qrlist` WHERE `beizhu`='$name' limit 1")->fetch();
	if($wx_name and $Qr['cookie']==0 and !$Qr['wx_name']){
	    if($DB->exec("update `pay_qrlist` set `status`='1',`endtime`='{$date}',`cookie`='Login_Yes',`wx_name`='{$wx_name}' WHERE `beizhu`='{$name}'")){
	        exit('更新微信状态成功：'.$name.'平台状态没有成功，已自动为您更新状态，下次执行则回调');
	    }else{
	        exit('平台无此用户，可解除绑定！->'.$name.'绑定->'.$wx_name);
	    }
	}else{
	    $userrow_pid = $DB->query("SELECT * FROM pay_user WHERE pid='{$Qr['pid']}' limit 1")->fetch();
	    $pid = $Qr['pid'];
	    $time = time();
	    if(!$userrow_pid){
	        $result = array("code"=>-1,"msg"=>"当前商户PID或账号不存在");
	    }else{
	        $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$pid}' and type='wxpay' and price='{$money}' and outtime>'{$time}' limit 1")->fetch();
	        if($srow and $srow['status']==0){//发送通知给商户平台
	            $url=creat_callback($srow);
	            $datm=get_curl($url['notify']);
	            Add_log($pid,'自动回调订单(微信店员'.$name.')：'.$srow['trade_no']);
	            pay_notify($pid,'wxpay',$money,$datm,$srow['trade_no']);
	            if($userrow_pid['money_mail']==1)paymali($srow,$userrow_pid['email'],'微信自动回调订单(微信店员'.$name.')');
	            exit(($data?$data:'success'));
	        }else{
	            $result = array("code"=>-1,"msg"=>"此金额匹配不到任何订单");
	        }
	    }
	}
    
	
break;
case 'Authcode_De':
//暂时备用
break;
default:
	exit('{"code":-9,"msg":"No Act"}');
break;
}


	if($result)
		exit(json_encode($result,JSON_UNESCAPED_UNICODE));
	else
		exit($data);
?>