<?php
include("../Mym/Common.php");
$act=daddslashes($_GET['act']);
if($act=='Login'){
	$pid = daddslashes($_GET['pid']);//商户pid/账号
	$key = daddslashes($_GET['key']);//商户key/密码
    if($pid and $key){
		//使用pid或者账号登录
		$userrow_pid = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
		$userrow_user = $DB->query("SELECT * FROM pay_user WHERE user='{$pid}' limit 1")->fetch();
		if(!$userrow_pid and !$userrow_user){
			exit(json_encode(array("code"=>-1,"msg"=>"当前商户PID或账号不存在")));
		}else{
			if($userrow_pid['key'] == $key or $userrow_user['pass'] == $key){
				//登录成功
				if($userrow_pid){
					$userrow_pid['login_type'] = '使用商户PID登录';//登录类型
				}else{
					$userrow_user['login_type'] = '使用商户账号登录';//登录类型
				}
				$result = ($userrow_pid?$userrow_pid:$userrow_user);
				$result['code'] = 1;//登录成功
			}else{
				$result = array("code"=>-1,"msg"=>"当前商户KEY或密码错误");
			}
		}
	}else{
		$result = array("code"=>-1,"msg"=>"输入不全,请检查");
	}
}elseif($act=='Cron'){
  $pid = daddslashes($_GET['pid']);//商户pid
  $key = daddslashes($_GET['key']);//商户key 
  $alipay_login = daddslashes($_GET['alipay_login']);//PC 支付宝在线
  $wxpay_login = daddslashes($_GET['wxpay_login']);//PC 微信在线
  $qqpay_login = daddslashes($_GET['qqpay_login']);//PC QQ钱包在线
  $channel = daddslashes($_GET['channel']);
  $name = daddslashes($_GET['name']);
  $crontime = time()+10;
  $time = time();
  if($pid and $key){
		//使用pid或者账号登录
	$userrow_pid = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
	$userrow_user = $DB->query("SELECT * FROM pay_user WHERE user='{$pid}' limit 1")->fetch();
	if(!$userrow_pid and !$userrow_user){
		exit(json_encode(array("code"=>-1,"msg"=>"当前商户PID或账号不存在")));
	}elseif($userrow_pid['key'] == $key or $userrow_user['pass'] == $key){
		$crontime = time()+90;
		if($name and $channel=='pc_wx_zs'){
		    if($wxpay_login==2){
		        $DB->exec("update `pay_qrlist` set `status`='1',`crontime`='$crontime' where channel='$channel' and beizhu='$name' and `pid`='$pid'");
		    }else{
		        $DB->exec("update `pay_qrlist` set `status`='0',`crontime`='0' where channel='$channel' and beizhu='$name' and `pid`='{$pid}'");
		    }
		    
		}else{
		    if($alipay_login==1)$DB->exec("update `pay_qrlist` set `status`='1',`crontime`='{$crontime}' where type='alipay' and hook_type='1' and `pid`='{$pid}'");
		    if($wxpay_login==1)$DB->exec("update `pay_qrlist` set `status`='1',`crontime`='{$crontime}' where type='wxpay' and hook_type='1' and `pid`='{$pid}'");
		    if($qqpay_login==1)$DB->exec("update `pay_qrlist` set `status`='1',`crontime`='{$crontime}' where type='qqpay' and hook_type='1' and `pid`='{$pid}'");
		}
		
		$result = array("code"=>1,"msg"=>"更新心跳成功 ".$date);
    }
  }
}elseif($act=='Pay_Money'){
    $pid = daddslashes($_GET['pid']);//商户pid
    $key = daddslashes($_GET['key']);//商户密钥
	$type = daddslashes($_GET['type']);//支付方式
	$money = daddslashes($_GET['money']);//支付金额
	$channel = daddslashes($_GET['channel']);
	$name = daddslashes($_GET['name']);
	$time = time();
	if($type=='alipay'){
		$E_type = '支付宝';
	}elseif($type=='qqpay'){
		$E_type = 'QQ钱包';
	}else{
		$E_type = '微信';
	}
	$userrow_pid = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
	if(!$pid || !$type || !$money || !$key)exit(json_encode(array("code"=>-2,"msg"=>"数据不齐全，拒绝访问")));
	if($userrow_pid['key']!=$key){
		exit(json_encode(array("code"=>-1,"msg"=>"当前商户PID，KEY或账号不存在")));
	}else{
	    if($channel=='pc_wx_zs'){
	        $Qr=$DB->query("SELECT * FROM `pay_qrlist` WHERE `beizhu`='$name' limit 1")->fetch();
	        if($Qr){
	            $srow = $DB->query("SELECT * FROM pay_order WHERE qr_id='{$Qr['id']}' and status='0' and pid='{$pid}' and type='{$type}' and price='{$money}' and outtime>'{$time}' limit 1")->fetch();
	            if($srow['status']==0) { 
	                $url=creat_callback($srow);
	                $datm=get_curl($url['notify']);
	                Add_log($pid,'自动回调订单('.$E_type.'挂机PC)：'.$srow['trade_no']);
	                pay_notify($pid,$type,$money,$datm,$srow['trade_no']);
	                $result = array("code"=>1,"msg"=>"订单回调成功","data"=>$datm,"type"=>$E_type,"trade_no"=>$srow['trade_no'],"money"=>$money);
	            }else{
	                $result = array("code"=>-1,"msg"=>"匹配不到订单","type"=>$E_type);
	            }
	        }else{
	            $result = array("code"=>-1,"msg"=>"订单无此号","type"=>$E_type);
	        }
	    }else{
	        $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$pid}' and type='{$type}' and price='{$money}' and outtime>'{$time}' limit 1")->fetch();
			if($srow['status']==0) { //=0则是官方通道
				//发送通知给商户平台
				$url=creat_callback($srow);
				$datm=get_curl($url['notify']);
				Add_log($pid,'自动回调订单('.$E_type.'挂机PC)：'.$srow['trade_no']);
				pay_notify($pid,$type,$money,$datm,$srow['trade_no']);
				$result = array("code"=>1,"msg"=>"订单回调成功","data"=>$datm,"type"=>$E_type,"trade_no"=>$srow['trade_no'],"money"=>$money);
			}else{
			    $result = array("code"=>-1,"msg"=>"匹配不到订单","type"=>$E_type);
			}
	    }
	}
}elseif($act=='user_status'){
    $pid = daddslashes($_GET['pid']);//商户pid
    $key = daddslashes($_GET['key']);//商户密钥
	$type = daddslashes($_GET['type']);//支付方式
	if($type=='alipay'){
		$E_type = '支付宝';
	}elseif($type=='qqpay'){
		$E_type = 'QQ钱包';
	}else{
		$E_type = '微信';
	}
	if(!$pid or !$key or !$type)exit();
	$userrow = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' and key='{$key}' limit 1")->fetch();
	$sub = $conf['sitename'].' - COOKIE失效提醒';
	$msg = '尊敬的：'.$conf['sitename'].'用户'.$pid.',您好! 您在'.$conf['sitename'].'上挂的['.$E_type.']PC挂机，已经掉线,为了不影响您继续使用,请务必去更新,地址:http://'.$_SERVER['HTTP_HOST'];
	$send_res = send_mail($userrow['email'], $sub, $msg);
}elseif($act=='Cookie'){
    $pid = daddslashes($_GET['pid']);//商户pid
    $key = daddslashes($_GET['key']);//商户密钥
    $type = daddslashes($_GET['type']);//支付方式
    $cookie = daddslashes($_GET['cookie']);//cookie
    $userrow_pid = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
	if(!$pid || !$type || !$key || !$userrow_pid)exit(json_encode(array("code"=>-2,"msg"=>"数据不齐全，拒绝访问")));
	if($userrow_pid['key']!=$key){
		exit(json_encode(array("code"=>-1,"msg"=>"当前商户PID，KEY或账号不存在")));
	}else{
       $DB->exec("update `pay_qrlist` set `status`='1',`cookie`='{$cookie}' where type='{$type}' and hook_type='1' and `pid`='{$pid}'");
	}
}elseif($act=='Qrcode'){
    $pid = daddslashes($_GET['pid']);//商户pid
    $key = daddslashes($_GET['key']);//商户密钥
    $type = daddslashes($_GET['type']);//支付方式
    $cookie = daddslashes($_GET['cookie']);//cookie
    $uin = daddslashes($_GET['uin']);
    $beizhu = daddslashes($_GET['beizhu']);
    if($type=='alipay'){
		$E_type = '支付宝';
	}elseif($type=='qqpay'){
		$E_type = 'QQ钱包';
	}else{
		$E_type = '微信';
	}
	$userrow_pid = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
	if(!$pid || !$type || !$key || !$userrow_pid)exit(json_encode(array("code"=>-2,"msg"=>"数据不齐全，拒绝访问")));
	if($userrow_pid['key']!=$key){
		exit(json_encode(array("code"=>-1,"msg"=>"当前商户PID，KEY或账号不存在")));
	}else{
	    $id = $DB->query("SELECT * FROM pay_qrlist WHERE pid='{$pid}' and type='{$type}' and uin='{$uin}' and hook_type='1' limit 1")->fetch();
        if($id){
            if($sqs=$DB->exec("update `pay_qrlist` set `cookie`='{$cookie}',`status`='1',`addtime`='{$date}' where id='{$id['id']}'")){
                $result = array('code'=>2,'msg'=>$E_type.' '.$uin.'更新成功');
            }else{
                $result = array('code'=>-1,'msg'=>$E_type.' '.$uin.'更新失败'.$DB->error());
            }
        }else{
            
            if($DB->exec("insert into `pay_qrlist` (`pid`,`type`,`beizhu`,`uin`,`cookie`,`hook_type`,`addtime`,`endtime`) values ('{$pid}','{$type}','{$uin}','{$uin}','{$cookie}','1','{$date}','{$date}')")){
                $result = array('code'=>1,'msg'=>$E_type.' '.$uin.'添加成功');
            }else{
                $result = array('code'=>-1,'msg'=>$E_type.' '.$uin.'添加失败'.$DB->error());
            }
            
        }
    }
}else{
    $data = 'ok';
}
if($data){
   exit($data);
}else{ 
   exit(json_encode($result));
}