<?php
// +----------------------------------------------------------------------
// | Quotes [ 只为给用户更好的体验]**[我知道发出来有人会盗用,但请您留版权]
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 墨友铭  <485570653@qq.com>          盗用不留版权,你就不配拿去!
// +----------------------------------------------------------------------
// | Date: 2022年01月19日
// +----------------------------------------------------------------------

include("../Mym/Common.php");
$act=daddslashes($_GET['act']);
if($islogin_user==1 or $act=='Login' or $act=='Captcha' or $act=='Reg' or $act=='Emailrt'){}else exit(json_encode(array("code"=>-5,"msg"=>"未登录")));
if($act=='Captcha'){//滑动验证
	$GtSdk = new \lib\GeetestLib($conf['CAPTCHA_ID'], $conf['PRIVATE_KEY']);
	$data = array(
		'user_id' => isset($uid)?$uid:'public', # 网站用户id
		'client_type' => "web", # web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
		'ip_address' => $ip # 请在此处传输用户请求验证时所携带的IP
	);
	$status = $GtSdk->pre_process($data, 1);
	$_SESSION['gtserver'] = $status;
	$_SESSION['user_id'] = isset($uid)?$uid:'public';
	exit($GtSdk->get_response_str());
}elseif($act=='checkbind'){
	if($conf['verifytype']==1 && (empty($userrow['phone']) || strlen($userrow['phone'])!=11)){
		exit('{"code":1,"msg":"bind"}');
	}elseif($conf['verifytype']==0 && (empty($userrow['email']) || strpos($userrow['email'],'@')===false)){
		exit('{"code":1,"msg":"bind"}');
	}elseif(isset($_SESSION['verify_ok']) && $_SESSION['verify_ok']===$uid){
		exit('{"code":1,"msg":"bind"}');
	}else{
		exit('{"code":2,"msg":"need verify"}');
	}
}elseif($act=='Sendcode'){//获取注册验证码
	$GtSdk = new \lib\GeetestLib($conf['CAPTCHA_ID'], $conf['PRIVATE_KEY']);

	$data = array(
		'user_id' => 'public', # 网站用户id
		'client_type' => "web", # web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
		'ip_address' => $ip # 请在此处传输用户请求验证时所携带的IP
	);

	if ($_SESSION['gtserver'] == 1) {   //服务器正常
		$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
		
		if ($result) {
		    $result = array("code"=>1,"msg"=>"验证成功","status"=>"success");
		} else{
			$result =array("code"=>-1,"msg"=>"验证失败，请重新验证");
		}
	}else{  //服务器宕机,走failback模式
		if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
			$result = array("code"=>1,"msg"=>"验证成功","status"=>"success");
		}else{
			$result = array("code"=>-1,"msg"=>"验证失败，请重新验证");
		}
	}
}elseif($act=="Login"){
    $GtSdk = new \lib\GeetestLib($conf['CAPTCHA_ID'], $conf['PRIVATE_KEY']);

	$data = array(
		'user_id' => 'public', # 网站用户id
		'client_type' => "web", # web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
		'ip_address' => $ip # 请在此处传输用户请求验证时所携带的IP
	);

	if ($_SESSION['gtserver'] == 1) {   //服务器正常
		$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
		
		if ($result) {
			//echo '{"status":"success"}';
		} else{
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}else{  //服务器宕机,走failback模式
		if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
			//echo '{"status":"success"}';
		}else{
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}
    $user=$_POST['pid'];
    $pass=$_POST['key'];
    $pidrow=$DB->query("SELECT * FROM pay_user WHERE user='{$user}' limit 1")->fetch();
	if(!$user or !$pass){
		$result=array("code"=>-1,"msg"=>"所有参数不能为空");
	}elseif($pidrow['user']==$user) {
		if($pidrow['pass']==$pass && $result){
			$pid = $pidrow['pid'];
			$key = $pidrow['key'];
			$session=md5($pid.$key.$password_hash);
			$expiretime=time()+604800;
			$token=authcode("{$pid}\t{$session}\t{$expiretime}", 'ENCODE', $conf['KEY']);
			setcookie("user_token", $token, time() + 604800);
			$city=get_ip_city($ip)['Result']['Country'];
			$DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('".$pidrow['pid']."','商户账号登录','".$date."','".$ip."','".$city."')");
			Add_log($pid,'使用账号登录成功');
			$result=array("code"=>1,"msg"=>"使用账号登录成功");
		}else{
			$result=array("code"=>-1,"msg"=>"登录失败，密码错误");
		}
	}else{
		$userrow=$DB->query("SELECT * FROM pay_user WHERE email='{$user}' limit 1")->fetch();
		if($pass==$userrow['pass'] && $result){
			$pid = $userrow['pid'];
			$key = $userrow['key'];
			$session=md5($pid.$key.$password_hash);
			$expiretime=time()+604800;
			$token=authcode("{$pid}\t{$session}\t{$expiretime}", 'ENCODE', $conf['KEY']);
			setcookie("user_token1", $token, time() + 604800);
			setcookie("user_token", $token, time() + 604800);
			$city=get_ip_city($ip)['Result']['Country'];
			$DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('".$userrow['pid']."','商户邮箱登录','".$date."','".$ip."','".$city."')");
			Add_log($pid,'使用邮箱登录成功');
            $result=array("code"=>1,"msg"=>"使用邮箱登录成功");
		}else{
			$result=array("code"=>-2,"msg"=>"登录失败，密码错误");
		}
	}
}elseif($act=='Reg'){//注册商户
    $GtSdk = new \lib\GeetestLib($conf['CAPTCHA_ID'], $conf['PRIVATE_KEY']);

	$data = array(
		'user_id' => 'public', # 网站用户id
		'client_type' => "web", # web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
		'ip_address' => $ip # 请在此处传输用户请求验证时所携带的IP
	);

	if ($_SESSION['gtserver'] == 1) {   //服务器正常
		$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
		
		if ($result) {
			//echo '{"status":"success"}';
		} else{
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}else{  //服务器宕机,走failback模式
		if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
			//echo '{"status":"success"}';
		}else{
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}
	$user=trim(strip_tags(daddslashes($_POST['user'])));
	$pass=trim(strip_tags(daddslashes($_POST['pass'])));
    $qq=trim(strip_tags(daddslashes($_POST['qq'])));
	$email=trim(strip_tags(daddslashes($_POST['email'])));
	$phone=trim(strip_tags(daddslashes($_POST['phone'])));

	if($conf['reg_open']==0)exit('{"code":-1,"msg":"未开放商户申请"}');
	if(isset($_SESSION['reg_submit']) && $_SESSION['reg_submit']>time()-180){
		exit('{"code":-1,"msg":"请勿频繁注册"}');
	}
	$row=$DB->query("select * from pay_user where user='$user' limit 1")->fetch();
	if($row){
		exit('{"code":-1,"msg":"该用户名已存在，如需找回商户信息，请返回登录页面点击找回商户"}');
	}
	$row=$DB->query("select * from pay_user where email='$email' limit 1")->fetch();
	if($row){
		exit('{"code":-1,"msg":"该邮箱已经注册过商户，如需找回商户信息，请返回登录页面点击找回商户"}');
	}
	$row=$DB->query("select * from pay_user where qq='$qq' limit 1")->fetch();
	if($row){
		exit('{"code":-1,"msg":"当前QQ已存在，如需找回商户信息，请返回登录页面点击找回商户"}');
	}
	if(strlen($user)<5 or strlen($pass)<6){
		exit('{"code":-1,"msg":"请填写6位以上的账号密码！"}');
	}
	if($conf['verifytype']==0 && !preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $email)){
		exit('{"code":-1,"msg":"邮箱格式不正确"}');
	}
	if($conf['reg_pay']==1){
		$gid=$DB->query("select * from `pay_user` where `pid`='{$conf['zero_pid']}' limit 1")->fetch();
		if($gid===false)exit('{"code":-1,"msg":"注册收款商户ID不存在"}');
		$trade_no=date("YmdHis").rand(11111,99999);
		$out_trade_no=date("YmdHis").rand(111,999);
		
		$data = $type.'|'.$user.'|'.$pass.'|'.$email.'|'.$qq.'|'.$phone.'|'.$ip;
		$time = time();
		$sds=$DB->exec("insert into `pay_regcode` (`type`,`to`,`time`,`trade_no`,`data`,`ip`,`status`) values ('1','$email','$time','$trade_no','$data','$ip','0')");
		if($sds){
			$wxpay = array("id"=>1,"name"=>"wxpay","showname"=>"微信");
			$qqpay = array("id"=>2,"name"=>"qqpay","showname"=>"QQ钱包");
			$alipay = array("id"=>3,"name"=>"alipay","showname"=>"支付宝");
			$result=array("code"=>2,"msg"=>"订单创建成功！","trade_no"=>$trade_no,"need"=>$conf['reg_pay_price'],"paytype"=>array("1"=>$wxpay,"2"=>$qqpay,"3"=>$alipay));
		}else{
			exit('{"code":-1,"msg":"订单创建失败！'.$DB->errorCode().'"}');
		}
	}else{
	$pid='1'.mt_rand(10000000,99999999);
	$key = random(11);
	$money =$conf['reg_money']?$conf['reg_money']:'0.00';
	$sqs=$DB->exec("INSERT INTO `pay_user` (`user`,`pass`,`pid`,`key`,`qq`,`email`,`money`,`outtime`,`addtime`) VALUES ('{$user}','{$pass}','{$pid}','{$key}','{$qq}','{$email}','{$money}','180','{$date}')");
	if($sqs){
		$timer = date("Ymd");
		$sign = md5($pid.$timer.$conf['KEY'].$conf['admin_user']);
        $sub = $conf['sitename'].' - 注册成功通知';
		$msg = '<h2>商户注册成功通知</h2>感谢您注册'.$conf['web_name'].'！<br/>您的登录账号：'.$user.'<br/>您的登录密码：'.$pass.'<br/>您的商户ID：'.$pid.'<br/>您的商户key：'.$key.'<br/>'.$conf['web_name'].'官网：<a href="http://'.$_SERVER['HTTP_HOST'].'/" target="_blank">'.$_SERVER['HTTP_HOST'].'</a><br/>邮箱激活链接：http://'.$_SERVER['HTTP_HOST'].'/User/Ajax.php?act=Emailrt&pid='.$pid.'&sign='.$sign.'&t='.time().'</a><br/>【<a href="'.$siteurl.'" target="_blank">商户管理后台</a>】';
		$result = send_mail($email, $sub, $msg);
		$_SESSION['reg_submit']=time();
		Add_log($pid,'商户注册成功');
		$result=array("code"=>1,"msg"=>"申请商户成功！","pid"=>$pid,"key"=>$key);
	}else{
		$result=array("code"=>-1,"msg"=>"申请商户失败！".$DB->errorCode());
	}
	}
}elseif($act=='Emailrt'){
	$pid = $_GET['pid'];
	$sign = $_GET['sign'];
	$timer = date("Ymd");
	$key = md5($pid.$timer.$conf['KEY'].$conf['admin_user']);
	$email_status=$DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();

   if($email_status['email_status']==0){
      if($sign==$key){
          $sds=$DB->query("update pay_user set email_status='1'  where pid='{$pid}'");
          if($sds){
            sysmsg("邮箱已经激活"); 
            Add_log($pid,'邮箱已经激活');
          }else{
            sysmsg("系统错误请联系管理处理，错误代码".$DB->errorCode());
          }
	  }else{
	     sysmsg("邮箱激活链接错误，请重新获取！");
	     Add_log($pid,'邮箱激活链接错误，请重新获取');
	  }
   }else{
	  sysmsg("邮箱已经激活过了，请勿重复激活");
   }
}elseif($act=='Add_Qrcode'){//上传二维码
	$file = $_FILES['image_field']['tmp_name'];
	$res = pngupload($file);
	$json= json_decode($res,true);
	$qrcode = qrcode($json['url']);
	if($qrcode){
		$result=array("code"=>1,"msg"=>"添加成功，请去登陆更新COOKIE吧","qrcode"=>$qrcode);
		Add_log($pid,'更新COOKIE成功');
	  }else{
		$result=array("code"=>1,"msg"=>"添加成功，请去登陆更新COOKIE吧","qrcode"=>$json['url']);
	}
}elseif($act=='Add_Qr'){	//添加二维码
	$type=daddslashes($_POST['type']);
	$qr_url=daddslashes($_POST['qr_url']);
	$cookie=daddslashes($_POST['cookie']);
	$code=daddslashes($_POST['code']);
	$beizhu=daddslashes(htmlspecialchars($_POST['beizhu']));
	$hook_type=0;
    if($type=='wxyun'){
        $type='wxpay';
        $hook_type=2;
    }elseif($type=='qqyun'){
        $type='qqpay';
        $hook_type=2;
    }elseif($type=='wxpay' || $type=='qqpay'){
        if(!$qr_url){
			$result=array("code"=>-1,"msg"=>"上传失败，请先等二维码解码成功再点击上传");
	}
    }
		$sqs=$DB->exec("INSERT INTO `pay_qrlist` (`pid`,`type`, `qr_url`,`cookie`,`money`,`beizhu`,`status`,`hook_type`,`addtime`) VALUES ('{$userrow['pid']}','{$type}','{$qr_url}','0','0.00','{$beizhu}','0','{$hook_type}','{$date}')");
		if($sqs){
		  Add_log($userrow['pid'],'添加二维码');
		  $result=array("code"=>1,"msg"=>"添加二维码成功");
		}else{
		  $result=array("code"=>-1,"msg"=>"添加二维码失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
		}
	
}elseif($act=='Add_Qr_Gu'){	//添加二维码
	$type=daddslashes($_POST['type']);
	$qr_id=daddslashes($_POST['qr_id']);
	$qr_url=daddslashes($_POST['qr_url']);
	$money=daddslashes($_POST['money']);
	$srow = $DB->query("SELECT * FROM pay_qrcode WHERE pid='{$userrow['pid']}' and type='{$type}' and qr_id='{$qr_id}' and money='{$money}'  limit 1")->fetch();
	if($srow){
		$result=array("code"=>-1,"msg"=>"添加二维码失败,当前id金额只能添加一次");
	}elseif(!$qr_url){
		$result=array("code"=>-2,"msg"=>"确保所有项不能为空");
	}elseif($qr_url=='解码中'){
			$result=array("code"=>-1,"msg"=>"上传失败，请先等二维码解码成功再点击上传");
	}else{
		$sqs=$DB->exec("INSERT INTO `pay_qrcode` (`pid`,`type`,`qr_id`, `qr_url`,`money`,`addtime`) VALUES ('{$userrow['pid']}','{$type}','{$qr_id}','{$qr_url}','{$money}','{$date}')");
		if($sqs){
			Add_log($userrow['pid'],'添加固定金额码'.$money);
			$result=array("code"=>1,"msg"=>"添加固码成功");
		}else{
			$result=array("code"=>-1,"msg"=>"添加固码失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
		}
	}
}elseif($act=='Add_Dmf'){
	$f2fid=daddslashes($_POST['f2fid']);
	$f2fkey=daddslashes($_POST['f2fkey']);
	$f2fpublic=daddslashes($_POST['f2fpublic']);
	$beizhu=daddslashes($_POST['beizhu']);
	$srow = $DB->query("SELECT * FROM pay_dmf WHERE pid='{$userrow['pid']}' and f2fid='{$f2fid}' limit 1")->fetch();
	if($srow){
		$result=array("code"=>-1,"msg"=>"添加失败,当前id只能添加一次");
	}elseif(!$f2fid||!$f2fkey||!$f2fpublic){
	    $result=array("code"=>-1,"msg"=>"添加失败,数据不能为空");
	}else{
	    $sqs=$DB->exec("INSERT INTO `pay_dmf` (`pid`,`f2fid`,`f2fkey`, `f2fpublic`,`beizhu`,`addtime`) VALUES ('{$userrow['pid']}','{$f2fid}','{$f2fkey}','{$f2fpublic}','{$beizhu}','{$date}')");
	    if($sqs){
			Add_log($userrow['pid'],'添加当面付'.$f2fid);
			$result=array("code"=>1,"msg"=>"添加当面付成功");
		}else{
			$result=array("code"=>-1,"msg"=>"添加当面付失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
		}
	}

}elseif($act=='Up_Qr'){//更新二维码
	$id=daddslashes($_POST['id'])?daddslashes($_POST['id']):daddslashes($_GET['id']);
	$cookie=daddslashes($_POST['cookie'])?daddslashes($_POST['cookie']):daddslashes($_GET['cookie']);
	$is=$DB->query("SELECT * FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
	$Pay_Money= $Pay_Money_Api->Get_pay_money($is['type'],$cookie);
	$username = username($cookie,$is['type']);
	$name = $username['userName'].'|'.$username['email'];
	$str = str_replace('[', '', $name);
	$name = str_replace('}', '', $str);
	if(!$is){
			$result=array("code"=>-2,"msg"=>"非法操作");
		}else{
			$sqs=$DB->exec("update `pay_qrlist` set `cookie`='{$cookie}',`money`='{$Pay_Money['money']}',`status`='1',`addtime`='{$date}',`data_data`='[$name}' where id='{$id}'");
			if($sqs){
				Add_log($userrow['pid'],'更新二维码');
				$result=array("code"=>1,"msg"=>"更新成功");
			}else{
				$result=array("code"=>-1,"msg"=>"更新失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
			}
		}
}elseif($act=='Del_Qr'){//删除二维码
	$id=daddslashes($_POST['id']);
	$is=$DB->query("SELECT * FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
		if(!$is){
			$result=array("code"=>-2,"msg"=>"非法操作");
		}else{
			$sql="DELETE FROM `pay_qrlist` WHERE `id`='{$id}' limit 1";
			if($DB->exec($sql)){
				Add_log($userrow['pid'],'删除二维码');
				$result=array("code"=>1,"msg"=>"删除成功");
			}else{
				$result=array("code"=>-1,"msg"=>"删除失败");
			}
		}
}elseif($act=='Get_Qr'){	//取二维码数据
	$id=daddslashes($_POST['id'])?daddslashes($_POST['id']):daddslashes($_GET['id']);
	$is=$DB->query("SELECT * FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"非法操作");
	}else{
		$login_time = time();
		$rs=$DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `status`='1' and `login_time`>='{$login_time}' order by sort ASC");
		while($res = $rs->fetch())
		{
			$data[]=$res;
		}
		if(!$DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `status`='1' and `login_time`>='{$login_time}' limit 1")->fetch()){
			$data[]=array("wx_name"=>"暂无可添加微信","wx_user"=>"请联系站长");
		}
		$is['data_msg'] = $is['data_data'];
		$is['status_status'] = $is['status'];
		$result=array("code"=>1,"id"=>$is['id'],"type"=>$is['type'],"beizhu"=>$is['beizhu'],"data"=>$data,"qrdata"=>$is);
	}
}elseif($act=='Get_Login_QrCode'){//提交取登录二维码请求
		$hook = $_POST['hook']?$_POST['hook']:$_GET['hook'];
		$type = $_POST['type']?$_POST['type']:$_GET['type'];
		$beizhu = $_POST['beizhu']?$_POST['beizhu']:$_GET['beizhu'];
		$qr_id = $_POST['qr_id']?$_POST['qr_id']:$_GET['qr_id'];
		$is=$DB->query("SELECT * FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$qr_id['id']}' limit 1")->fetch();
		if($hook==2 && $type=='wxpay'){
		   $result = wxGetLoginQrcode();
	       $result = array("code"=>$result['code'],"guid"=>$result['guid'],"uuid"=>$result['uuid'],"qr_url"=>$result['qr_url'],"msg"=>$result['msg']);
		}elseif($hook==2 && $type=='qqpay'){
		    $result = Yun_Login_Qr($beizhu);
		    $result=array("code"=>$result['code'],"msg"=>$result['msg'],"id"=>$result['id'],"qr_url"=>$result['qr_url']);
		}else{
		    $result = Get_Login_Qr($type);
		    $result=array("code"=>$result['code'],"msg"=>$result['msg'],"id"=>$result['id'],"qr_url"=>$result['qr_url']);
		}
}elseif($act=='Get_Login_Cookie'){//取登录cookie
		$id = daddslashes($_POST['id']?$_POST['id']:$_GET['id']);
		$hook = $_POST['hook']?$_POST['hook']:$_GET['hook'];
		$qr_id = daddslashes($_POST['qr_id']?$_POST['qr_id']:$_GET['qr_id']);
		$type = daddslashes($_POST['type']?$_POST['type']:$_GET['type']);
		$guid = daddslashes($_POST['guid']?$_POST['guid']:$_GET['guid']);
	    $uuid = daddslashes($_POST['uuid']?$_POST['uuid']:$_GET['uuid']);
	    $row=$DB->query("select * from pay_qrlist where id='$qr_id' limit 1")->fetch();
		if($hook==2 && $type=='wxpay'){
		   $result = wxCheckLoginQrcode($guid,$uuid);
	       $result = array("code"=>$result['code'],"id"=>$qr_id,"cookie"=>$guid,"msg"=>$result['msg']);
		}elseif($hook==2 && $type=='qqpay'){
		    /*
		    $result = Yun_upDate($row['beizhu'])['cookie'];
		    if($result){
		        $result=array("code"=>1,"msg"=>'等待登录中...',"id"=>$row['beizhu'],"cookie"=>$result); 
		    }else{
		       $result = Yun_Login_Ck($row['beizhu']);
		       $result=array("code"=>$result['code'],"msg"=>'等待登录中...',"id"=>$result['id'],"cookie"=>$result['cookie']); 
		    }*/
		    $result = Yun_Login_Ck($row['beizhu']);
		    $result=array("code"=>$result['code'],"msg"=>'等待登录中...',"id"=>$result['id'],"cookie"=>$result['cookie']);
		    
		}else{
		  $result = Check_Login_Ck($type,$id);
		  if($type=='wxpay')$result ="";
			 $result=array("code"=>$result['code'],"msg"=>'等待登录中...',"id"=>$qr_id,"cookie"=>$result['cookie']);
		}
}elseif($act=='Qrlist'){	//二维码详细
	$id=trim($_GET['id']);
	$row=$DB->query("select * from pay_qrlist where id='$id' limit 1")->fetch();
	if(!$row)
		exit('{"code":-1,"msg":"当前订单不存在或未成功选择支付通道！"}');
  $today=date("Y-m-d").' 00:00:00';
  $today2=date("Y-m-d").' 23:59:59';
  $lastday=date("Y-m-d",strtotime("-1 day")).' 00:00:00';
  $lastday2=date("Y-m-d",strtotime("-1 day")).' 23:59:59';
  $qr_id=$row['id'];
	 // //商户总订单数量
  $ddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}'")->fetchColumn();
  //商户总成功订单数量
  $zcgddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status='1'")->fetchColumn();
  //商户总未完成订单数量
  $zwwcddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status!='1'")->fetchColumn();
  //商户总跑分订单金额
  $zpfddje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}'")->fetchColumn();
  //商户总跑分成功金额
  $zpfcgje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status='1'")->fetchColumn();
  //商户总跑分未完成金额
  $zpfwwcje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status!='1'")->fetchColumn();
  //今日商户总订单数量
  $jrzddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and addtime>='$today' and addtime<='$today2'")->fetchColumn();
  //今日商户总成功订单数量
  $jrzcgddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status='1' and addtime>='$today' and addtime<='$today2'")->fetchColumn();
  //今日商户总未完成订单数量
  $jrzwwcddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status!='1' and addtime>='$today' and addtime<='$today2'")->fetchColumn();
  //今日商户总跑分订单金额
  $jrzpfddje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and addtime>='$today' and addtime<='$today2'")->fetchColumn();
  //今日商户总跑分成功金额
  $jrzpfcgje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status='1' and endtime>='$today' and endtime<='$today2'")->fetchColumn();
  //今日商户总跑分未完成金额
  $jrzpfwwcje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status!='1' and addtime>='$today' and addtime<='$today2'")->fetchColumn();
  //昨日商户总订单数量
  $zrzddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and addtime>='$lastday' and addtime<='$lastday2'")->fetchColumn();
  //昨日商户总成功订单数量
  $zrzcgddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status='1' and addtime>='$lastday' and addtime<='$lastday2'")->fetchColumn();
  //昨日商户总未完成订单数量
  $zrzwwcddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status!='1' and addtime>='$lastday' and addtime<='$lastday2'")->fetchColumn();
  //昨日商户总跑分订单金额
  $zrzpfddje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and addtime>='$lastday' and addtime<='$lastday2'")->fetchColumn();
  //昨日商户总跑分成功金额
  $zrzpfcgje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status='1' and endtime>='$lastday' and endtime<='$lastday2'")->fetchColumn();
  //昨日商户总跑分未完成金额
  $zrzpfwwcje=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status!='1' and addtime>='$lastday' and addtime<='$lastday2'")->fetchColumn();
  
  $row['ali_order'] = '总订单：'.$ddsl.'<br>'.'已完成：'.$zcgddsl.'<br>'.'未完成：'.$zwwcddsl.'<br>'.'成功率：'.(round((($zcgddsl?$zcgddsl:1) / ($ddsl?$ddsl:1)),2)*100).'%</td>
	<td>'.'总金额：'.$zpfddje.'<br>'.'已完成：'.$zpfcgje.'<br>'.'未完成：'.$zpfwwcje;
	
  $row['jr_order'] = '总订单：'.$jrzddsl.'<br>'.'已完成：'.$jrzcgddsl.'<br>'.'未完成：'.$jrzwwcddsl.'<br>'.'成功率：'.(round((($jrzcgddsl?$jrzcgddsl:1) / ($jrzddsl?$jrzddsl:1)),2)*100).'%</td>
	<td>'.'总金额：'.$jrzpfddje.'<br>'.'已完成：'.$jrzpfcgje.'<br>'.'未完成：'.$jrzpfwwcje;
	
  $row['zr_order'] = '总订单：'.$zrzddsl.'<br>'.'已完成：'.$zrzcgddsl.'<br>'.'未完成：'.$zrzwwcddsl.'<br>'.'成功率：'.(round((($zrzcgddsl?$zrzcgddsl:1) / ($zrzddsl?$zrzddsl:1)),2)*100).'%</td>
	<td>'.'总金额：'.$zrzpfddje.'<br>'.'已完成：'.$zrzpfcgje.'<br>'.'未完成：'.$zrzpfwwcje;
  $row['type'] = '<img src="/Mym/Assets/Icon/'.$row['type'].'.ico" width="16">'.pay_type($row['type']);
	
	$result=array("code"=>0,"msg"=>"succ","data"=>$row);

}elseif($act=='Pay_Notify'){//补单并回调
	$trade_no=daddslashes($_POST['trade_no']);
	$srow=$DB->query("SELECT * FROM pay_order WHERE `pid`='{$userrow['pid']}' and `trade_no`='{$trade_no}' limit 1")->fetch();
	if($srow['pid']==$userrow['pid'] && $srow){
	   $url=creat_callback($srow);
	   $data=curl_get($url['notify']);
	   Add_log($userrow['pid'],'人工补单回调：'.$trade_no);
	   $result=array("code"=>1,"msg"=>"补单成功,并成功回调,接口返回数据：".$data);
	}else{
	   $result=array("code"=>-1,"msg"=>"Oh, no补单失败啦！");
}
}elseif($act=='Reset_key'){//重置key密钥
	if(isset($_SESSION['Reset_Token']) && $_SESSION['Reset_Token']>time()-3600){
		exit('{"code":-1,"msg":"请勿频繁修改秘钥"}');
	}
	$key = random(11);
	Add_log($userrow['pid'],'重置key密钥');
	$DB->exec("update `pay_user` set `key` ='{$key}' where `pid`='{$userrow['pid']}'");
	$_SESSION['Reset_Token']=time();
	exit('{"code":1,"msg":"succ"}');
}elseif($act=='User_Code'){
    $sub = $conf['sitename'].' - 修改资料验证码';
	$code = rand(1111111,9999999);
	
	$msg = '您的验证码是：'.$code;
	$msg = '<table style="border-collapse:collapse; width:100%;background: #f9f9fc"><tr><td colspan="3" style="height: 50px"></td></tr><tr><td style="width: 10%;"></td><td align="center"><div style="min-width: 320px;max-width: 660px;"><table style="width:100%; border-collapse:collapse; box-shadow: 0px 0px 13px 0px rgba(82, 63, 105, 0.05);background: #ffffff;border-radius: 5px;color: #6c7293;font-family: &#39;微软雅黑&#39;,Microsoft YaHei"><tr><td style="padding:35px 0 20px 0;text-align: center;" colspan="3"><img src="//'.$_SERVER['HTTP_HOST'].'/Mym/Assets/Img/logo.png" style="width: 120px;" title="logo"  /></td></tr><tr><td style="width: 22px"></td><td><table style="width: 100%;border-collapse:collapse;color: #6c7293;font-family: &#39;微软雅黑&#39;,Microsoft YaHei"><tr><td style="font-size: 18px;">尊敬的客户，</td></tr><tr><td style="padding: 10px;font-size: 14px;">感谢您选择'.$conf['sitename'].'<br/>本次请求的验证码为：</td></tr><tr><td><table style="width: 100%;border-collapse:collapse;font-family: &#39;微软雅黑&#39;,Microsoft YaHei"><tr><td style="width: 30%"></td><td style="color: #1dc9b7;background: #e8f9f8;border-radius: 5px;padding: 10px;font-weight: bold;font-size: 24px; text-align: center;font-family: &#39;微软雅黑&#39;,Microsoft YaHei">'.$code.'</td><td style="width: 30%"></td></tr></table></td></tr><tr><td style="padding: 10px;font-size: 14px;">	致敬，<br  />'.$conf['sitename'].'</td></tr></table></td><td style="width: 22px"></td></tr><tr><td style="text-align: center; color: #a7abc3;font-size: 12px; padding: 10px 0 25px 0;" colspan="3">此为系统邮件，请勿回复。</td></tr><tr><td style="background: #1e1e2d;color: #a9a7bc;padding: 18px 0;border-radius: 0 0 5px 5px;font-size: 12px;text-align: center;" colspan="3">&copy; '.$conf['sitename'].' 2021 - 2022</td></tr></table></div></td><td style="width: 10%;"></td></tr><tr><td colspan="3" style="height: 50px"></td></tr></table>';
	$result = send_mail($userrow['email'], $sub, $msg);
		if($result==true){
			if($DB->exec("insert into `pay_regcode` (`type`,`code`,`to`,`time`,`ip`,`status`) values ('3','".$code."','".$userrow['email']."','".time()."','".$ip."','0')")){
				$_SESSION['send_mail']=time();
				Add_log($userrow['pid'],'修改资料验证码'.$code);
				exit('{"code":1,"msg":"获取成功"}');
			}else{
				exit('{"code":-1,"msg":"写入数据库失败。'.$DB->error().'"}');
			}
		}else{
			file_put_contents('mail.log',$result);
			exit('{"code":-1,"msg":"邮件发送失败"}');
		}
}elseif($act=='verifycode'){
	$code=trim(daddslashes($_POST['code']));
    $row=$DB->query("select * from pay_regcode where type=3 and code='$code' and `to`='{$userrow['email']}' limit 1")->fetch();
	if(!$row){
		exit('{"code":-1,"msg":"验证码不正确！"}');
	}
	if($row['time']<time()-3600 || $row['status']>0){
		exit('{"code":-1,"msg":"验证码已失效，请重新获取"}');
	}
	$_SESSION['verify_ok']=$userrow['pid'];
	$DB->exec("update `pre_regcode` set `status` ='1' where `id`='{$row['id']}'");
	exit('{"code":1,"msg":"succ"}');

}else{
	$result=array("code"=>-9,"msg"=>"参数错误");
}
if($result)
	exit(json_encode($result,JSON_UNESCAPED_UNICODE));
else
	exit($data);
?>