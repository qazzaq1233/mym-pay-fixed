<?php
include("../../Mym/Common.php");
/* *
 * 功能：支付异步通知页面
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 */
//商品名称
$name = daddslashes($_GET['name']);
$money = daddslashes($_GET['money']);
$row=$DB->query("SELECT * FROM pay_user WHERE pid='{$conf['zero_pid']}' limit 1")->fetch();
$zero_INTL_pid  = $row['pid'];
$zero_INTL_key  = $row['key'];
$notify_pid = intval($_GET['pid']);
if($name == '测试商品' && $notify_pid){
    $test_userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$notify_pid}' limit 1")->fetch();
    if($test_userrow){
        $zero_INTL_pid  = $test_userrow['pid'];
        $zero_INTL_key  = $test_userrow['key'];
    }
}
//计算得出通知验证结果
$verify_result = verifyNotify($zero_INTL_key);
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代

	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
	
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
	//商户订单号

	$out_trade_no = daddslashes($_GET['out_trade_no']);

	//支付交易号

	$trade_no = daddslashes($_GET['trade_no']);

	//交易状态
	$trade_status = daddslashes($_GET['trade_status']);

	//支付方式
	$type = daddslashes($_GET['type']);


if ($_GET['trade_status'] == 'TRADE_SUCCESS') {
	if(strpos($name,'额度充值')!== false){
		$pid = daddslashes(explode('额度充值',$name)[0]);
		$name = daddslashes(explode($pid,$name)[1]);
		if($name == '额度充值' and $_GET['pid']==$conf['zero_pid']){
		    $money1 = $_GET['money'] * $conf['ed_money'];
		    $DB->exec("update `pay_user` set `money`=`money`+'{$money1}' where pid='{$pid}'");
		    echo "success";exit;
		}
	}elseif(strstr($name,'购买额度套餐')){
	    //152283148购买额度套餐|普通优惠套餐|1
	    $pid = daddslashes(explode('购买额度套餐',$name)[0]);
	    $pack_name = daddslashes(explode('|',$name)[1]);
	    $pack_id = daddslashes(explode('|',$name)[2]);
	    if($_GET['pid']==$conf['zero_pid']){
	        $pack=$DB->query("SELECT * FROM `pay_taocan` WHERE `name`='{$pack_name}' and `id`='{$pack_id}' limit 1")->fetch();
	        $row=$DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
	        if($row['user_vip_time']>date("Y-m-d H:i:s")){
	            $time = strtotime($row['user_vip_time']);
	        }else{
	            $time = time();
	        }
	        
	        $user_vip_time = $time+($pack['time']*2592000);
	        $user_vip_time = date("Y-m-d H:i:s",$user_vip_time);
	        if($pack){
	            $DB->exec("update `pay_user` set `money`=`money`+'{$pack['edu']}',`user_vip`='{$pack['id']}',`user_vip_time`='{$user_vip_time}' where pid='{$pid}'");
	            echo "success";exit;
	        }else{
	            exit('fail');
	        }
	    }
	    
	}elseif(strpos($name,'通道配额')!== false){
	    $pid = daddslashes(explode('通道配额',$name)[0]);
		$name = daddslashes(explode('通道配额',$name)[1]);
        $DB->exec("update `pay_user` set `type`=`type`+'{$name}' where pid='{$pid}'");
        echo "success";exit;
    }elseif($name == '申请商户' and $_GET['pid']==$conf['zero_pid']){
        $row=$DB->query("select * from `pay_regcode` where `trade_no`='{$out_trade_no}' order by id desc limit 1")->fetch();
		$user = daddslashes(explode('|',$row['data'])[1]);
		$pass = daddslashes(explode('|',$row['data'])[2]);
		$email = daddslashes(explode('|',$row['data'])[3]);
		$qq = daddslashes(explode('|',$row['data'])[4]);
		$clientip = daddslashes(explode('|',$row['data'])[6]);
		$pid='1'.mt_rand(10000000,99999999);
		$key = random(11);
		$money =$conf['reg_money']?$conf['reg_money']:'0.00';
        $type = $conf['reg_type']?$conf['reg_type']:'3';
        $reg_emali = $conf['reg_emali']?$conf['reg_emali']:'1';
        $sqs=$DB->exec("INSERT INTO `pay_user` (`user`,`pass`,`pid`,`key`,`qq`,`money`,`email`,`type`,`outtime`,`addtime`,`email_status`) VALUES ('{$user}','{$pass}','{$pid}','{$key}','{$qq}','{$money}','{$email}','{$type}','180','{$date}','{$reg_emali}')");
		if($sqs and !empty($email) and $reg_emali!=1){
		  $timer = date("Ymd");
	      $sign = md5($pid.$timer.$conf['KEY'].$conf['admin_user']);
          $sub = $conf['sitename'].' - 注册成功通知';
	      $msg = '<h2>商户注册成功通知</h2>感谢您注册'.$conf['web_name'].'！<br/>您的登录账号：'.$user.'<br/>您的登录密码：'.$pass.'<br/>您的商户ID：'.$pid.'<br/>您的商户key：'.$key.'<br/>'.$conf['web_name'].'官网：<a href="http://'.$_SERVER['HTTP_HOST'].'/" target="_blank">'.$_SERVER['HTTP_HOST'].'</a><br/>邮箱激活链接：http://'.$_SERVER['HTTP_HOST'].'/User/Ajax.php?act=Emailrt&pid='.$pid.'&sign='.$sign.'&t='.time().'</a><br/>【<a href="'.$siteurl.'" target="_blank">商户管理后台</a>】';
		  send_mail($email, $sub, $msg);
		}
		$DB->exec("update `pay_regcode` set `status` ='1' where `id`='{$row['id']}'");
		echo "success";exit;
	}
}
    echo "success";exit;
}
else {
    echo "fail";
}
?>