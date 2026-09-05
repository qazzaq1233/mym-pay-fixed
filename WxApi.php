<?php 
require './Mym/Common.php'; 
$act=$_GET['act']?$_GET['act']:$_POST['act'];
$ipip = explode(',',$conf['wxip']);
if(!in_array($ip,$ipip)){
	exit("IP验证不通过".$ip."，请在后台加快回调添加白名单");
}
if($act=='pay_zt'){//更新状态
	$qr_id=daddslashes($_GET['qr_id']?$_GET['qr_id']:$_POST['qr_id']);
	$data_data=daddslashes($_GET['data_data']?$_GET['data_data']:$_POST['data_data']);//返回信息
	$cookie=daddslashes($_GET['cookie']?$_GET['cookie']:$_POST['cookie']);//cookie
	$qr_id=daddslashes(urldecode($qr_id));
	$data_data=daddslashes(urldecode($data_data));
	$cookie=daddslashes(urldecode($cookie));
	$status = 0;
	if($cookie){
		$status = 1;
		$data_data = ($data_data?$data_data:'更新成功');
	}else{
		$status = 4;
	}
	$issql = $DB->exec("update `pay_qrlist` set `status`='{$status}',`addtime`='{$date}',`data_data`='{$data_data}',`cookie`='{$cookie}' WHERE `qr_id`='{$qr_id}'");//更新微数据
	if($issql){
		echo '更新状态成功：'.$qr_id;
	}else{
		echo '更新状态失败,可能不存在此二维码';
	}
}elseif($act=='wxpay_zt_Out'){//更新微信状态
	$intl_wx_name=daddslashes($_GET['intl_wx_name']?$_GET['intl_wx_name']:$_POST['intl_wx_name']);//店员微信名
	$wx_name=daddslashes($_GET['wx_name']?$_GET['wx_name']:$_POST['wx_name']);//绑定微信名
	$cookie=daddslashes($_GET['cookie']?$_GET['cookie']:$_POST['cookie']);//cookie
	$intl_wx_name=daddslashes(urldecode($intl_wx_name));
	$wx_name=daddslashes(urldecode($wx_name));
	$cookie=daddslashes(urldecode($cookie));
	$status = 0;
	if($cookie=='Login_Yes'){
		$status = 1;
	}
	if($wx_name)$issql = $DB->exec("update `pay_qrlist` set `status`='{$status}',`endtime`='{$date}',`cookie`='{$cookie}',`wx_name`='{$intl_wx_name}' WHERE `beizhu`='{$wx_name}'");//更新微信失效数据
	if($issql){
		echo '更新微信状态成功：'.$wx_name;
	}else{
		echo '更新微信状态失败,可能不存在此微信二维码'.$intl_wx_name;
	}
}elseif($act=='Up_Wechat_Trumpet'){//更新微信在线心跳
	$login_time = time()+120;
	$intl_wx_name=daddslashes($_GET['intl_wx_name']?$_GET['intl_wx_name']:$_POST['intl_wx_name']);//店员微信名
	$intl_wx_name=daddslashes(urldecode($intl_wx_name));
	$issql = $DB->exec("update `pay_wechat_trumpet` set `login_time`='{$login_time}',`status`='1' WHERE `wx_name`='{$intl_wx_name}'");
	if($issql){
		echo '更新微信心跳成功：'.$intl_wx_name;
	}else{
		echo '更新微信心跳失败,可能不存在此微信店员小号';
	}
}elseif($act=='Up_Wechat_Trumpet2'){
    $intl_wx_name=daddslashes(urldecode($_GET['intl_wx_name']));
    $login_time = time()+120;
    $wx_name = explode("|",$intl_wx_name);
    if($wx_name[1]){
        $i = 0;
        foreach ($wx_name as $item)//循环获取json数组数据
        {
            if($DB->exec("update `pay_wechat_trumpet` set `login_time`='{$login_time}',`status`='1' WHERE `wx_name`='{$item}'")){
                $i++;
            }
        }
        if($i<0){
            echo '更新微信心跳失败,可能不存在此微信店员小号';
        }else{
            echo '更新微信心跳成功：'.$i.'个';
        }
    }else{
        $issql = $DB->exec("update `pay_wechat_trumpet` set `login_time`='{$login_time}',`status`='1' WHERE `wx_name`='{$intl_wx_name}'");
        if($issql){
            echo '更新微信心跳成功：'.$intl_wx_name;
        }else{
            echo '更新微信心跳失败,可能不存在此微信店员小号';
        }
    }
}elseif($act=='wxpay_name'){
    echo 'no';
}else{
	//echo '参数错误';
}

?>