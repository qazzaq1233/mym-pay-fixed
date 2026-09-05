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
header("Content-type:application/json");
$act=daddslashes($_GET['act']);
if($islogin_user==1 or $act=='Login' or $act=='Captcha' or $act=='Reg' or $act=='Emailrt'){}else exit(json_encode(array("code"=>-5,"msg"=>"未登录")));

if($act=='Captcha'){//滑动验证
	$GtSdk = new \lib\GeetestLib('', '');
	$data = array(
		'user_id' => isset($uid)?$uid:'public',
		'client_type' => "web",
		'ip_address' => $ip
	);
	$result = $GtSdk->pre_process($data);
	$_SESSION['gtserver'] = $result['success'];
	exit(json_encode($result));
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
    /*
    $GtSdk = new \lib\GeetestLib($conf['CAPTCHA_ID'], $conf['PRIVATE_KEY']);
	$data = array(
		'user_id' => 'public', # 网站用户id
		'client_type' => "web", # web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
		'ip_address' => $ip # 请在此处传输用户请求验证时所携带的IP
	);

	if ($_SESSION['gtserver'] == 1) {   //服务器正常
		$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
		
		if (!$result) {
		    $_SESSION['pwd_error']++;
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}else{  //服务器宕机,走failback模式
		if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
		    $_SESSION['pwd_error']++;
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}
	*/
    $user=daddslashes(trim($_POST['pid']));
    $pass=daddslashes(trim($_POST['key']));
    $pidrow=$DB->query("SELECT * FROM pay_user WHERE user='{$user}' limit 1")->fetch();
	if(!$user or !$pass){
		$result=array("code"=>-1,"msg"=>"所有参数不能为空");
	}elseif($pidrow['user']==$user) {
		if($pidrow['pass']==$pass){
			$pid = $pidrow['user'];
			$key = $pidrow['pass'];
			$session=md5($pid.$key.$password_hash);
			$expiretime=time()+604800;
			$token=authcode("{$pid}\t{$session}\t{$expiretime}", 'ENCODE', $conf['KEY']);
			setcookie("user_token", $token, time() + 604800);
			$city=get_ip_city($ip)['Result']['Country'];
			$DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('".$pidrow['pid']."','商户账号登录','".$date."','".$ip."','".$city."')");
			Add_log($pid,'使用账号登录成功');
			$result=array("code"=>1,"msg"=>"使用账号登录成功");
		}else{
		    Add_log($pid,'使用账号恶意登录或密码错误，已记录IP：'.$ip);
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
			setcookie("user_token", $token, time() + 604800);
			$city=get_ip_city($ip)['Result']['Country'];
			$DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('".$userrow['pid']."','商户邮箱登录','".$date."','".$ip."','".$city."')");
			Add_log($pid,'使用邮箱登录成功');
            $result=array("code"=>1,"msg"=>"使用邮箱登录成功");
		}else{
		    Add_log($pid,'使用邮箱恶意登录或密码错误，已记录IP：'.$ip);
			$result=array("code"=>-2,"msg"=>"登录失败，密码错误");
		}
	}
}elseif($act=='Reg'){//注册商户
	$user=trim(strip_tags(daddslashes($_POST['user'])));
	$pass=trim(strip_tags(daddslashes($_POST['pass'])));
    $qq=trim(strip_tags(daddslashes($_POST['qq'])));
	$email=trim(strip_tags(daddslashes($_POST['email'])));
	$phone=trim(strip_tags(daddslashes($_POST['phone'])));
	$GtSdk = new \lib\GeetestLib($conf['CAPTCHA_ID'], $conf['PRIVATE_KEY']);
	$data = array(
		'user_id' => 'public', # 网站用户id
		'client_type' => "web", # web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
		'ip_address' => $ip # 请在此处传输用户请求验证时所携带的IP
	);
	if ($_SESSION['gtserver'] == 1) {   //服务器正常
		$result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
		if (!$result) {
		    $_SESSION['pwd_error']++;
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}else{  //服务器宕机,走failback模式
		if (!$GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
		    $_SESSION['pwd_error']++;
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}
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
	    $type = $conf['reg_type']?$conf['reg_type']:'3';
	    $reg_emali = $conf['reg_emali']?$conf['reg_emali']:'1';
	    $sqs=$DB->exec("INSERT INTO `pay_user` (`user`,`pass`,`pid`,`key`,`qq`,`money`,`email`,`type`,`outtime`,`addtime`,`email_status`) VALUES ('{$user}','{$pass}','{$pid}','{$key}','{$qq}','{$money}','{$email}','{$type}','180','{$date}','{$reg_emali}')");
	    if($sqs){
	        $timer = date("Ymd");
	        $sign = md5($pid.$timer.$conf['KEY'].$conf['admin_user']);
	        if(!empty($email) and $reg_emali!=1){
	            $sub = $conf['sitename'].' - 注册成功通知';
	            $msg = '<h2>商户注册成功通知</h2>感谢您注册'.$conf['web_name'].'！<br/>您的登录账号：'.$user.'<br/>您的登录密码：'.$pass.'<br/>您的商户ID：'.$pid.'<br/>您的商户key：'.$key.'<br/>'.$conf['web_name'].'官网：<a href="http://'.$_SERVER['HTTP_HOST'].'/" target="_blank">'.$_SERVER['HTTP_HOST'].'</a><br/>邮箱激活链接：http://'.$_SERVER['HTTP_HOST'].'/User/Ajax.php?act=Emailrt&pid='.$pid.'&sign='.$sign.'&t='.time().'</a><br/>【<a href="'.$siteurl.'" target="_blank">商户管理后台</a>】';
	            send_mail($email, $sub, $msg);
	        }
	        $_SESSION['reg_submit']=time();
	        Add_log($pid,'商户注册成功');
	        $result=array("code"=>1,"msg"=>"申请商户成功！","pid"=>$pid,"key"=>$key);
	    }else{
	        $result=array("code"=>-1,"msg"=>"申请商户失败！".$DB->errorCode());
	    }
	}
}elseif($act=='Emailrt'){
	$pid = daddslashes($_GET['pid']);
	$sign = daddslashes($_GET['sign']);
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
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
    $channel = $_GET['channel'];
    $url = explode("/Ajax.php",$_SERVER['PHP_SELF'])[0];
    $rand=date("YmdHis").rand(11111,99999);
    $Add = copy($_FILES['image_field']['tmp_name'], ROOT.$url.'/QRCODE/'.$rand.'.png');
	if($channel!='yd_wxzsm'){
        $qrcode = qrcode(($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$url.'/QRCODE/'.$rand.'.png');
    }
	if($qrcode){
	    unlink(ROOT.$url.'/QRCODE/'.$rand.'.png');  //如果解码则清理本地二维码图片,否则调用本地二维码
		$result=array("code"=>1,"msg"=>"添加成功，请去登陆更新COOKIE吧","qrcode"=>$qrcode);
		Add_log($pid,'更新COOKIE成功');
	}elseif($Add){
		$result=array("code"=>1,"msg"=>"添加成功，请去登陆更新COOKIE吧","qrcode"=>urlencode($url.'/QRCODE/'.$rand.'.png'));
	}else{
	    $result=array("code"=>-1,"msg"=>"添加二维码失败,存储到本地出问题，错误代码x0000");
	}
}elseif($act=='Add_Qr'){	//添加二维码
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
	$type=daddslashes($_POST['type']);
	$qr_url=daddslashes($_POST['qr_url']);
	$custom_qr_url=trim($_POST['custom_qr_url']);
	$cookie=daddslashes($_POST['cookie']);
	$beizhu=daddslashes(htmlspecialchars($_POST['beizhu']));
	$receiver_surname=isset($_POST['receiver_surname']) ? trim(strip_tags($_POST['receiver_surname'])) : '';
	$receiver_surname=daddslashes(mb_substr($receiver_surname,0,10,'UTF-8'));
	$channel=daddslashes($_POST['channel']);
	$Login_Type=daddslashes($_POST['Login_Type']);
	$ali_order_check=daddslashes($_POST['ali_order_check']);
	if($ali_order_check!='order_no')$ali_order_check='order_amount';
	$nums=$DB->query("SELECT count(*) from pay_qrlist WHERE `pid`='{$userrow['pid']}'")->fetchColumn();
	if(strstr($channel,'mg')){
	    $hook_type = 0;
	}elseif(strstr($channel,'pc')){
	    $hook_type = 1;
	}elseif(strstr($channel,'yd')){
	    $hook_type = 2;
	}else{
	    $hook_type = 0;
	}
	if(!mym_pay_channel_enabled($type,$channel)){
	    exit('{"code":-1,"msg":"该支付类型或通道已被后台关闭，请刷新页面后重新选择！"}');
	}
	if($custom_qr_url!=''){
	    if(!preg_match('/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/|weixin:\/\/|mqqapi:\/\/)/i', $custom_qr_url)){
	        exit('{"code":-1,"msg":"自定义收款码链接格式不正确，请填写 http(s)、alipays、alipayqr、weixin 或 mqqapi 开头的链接！"}');
	    }
	    if(!mym_pay_qr_url_matches_type($type,$custom_qr_url)){
	        exit('{"code":-1,"msg":"自定义收款码链接与所选支付类型不匹配，请检查是否选错了支付宝/QQ/微信通道！"}');
	    }
	    if($type=='alipay' && $channel=='mg_ali' && !preg_match('/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/)/i', $custom_qr_url)){
	        exit('{"code":-1,"msg":"支付宝收款码链接格式不正确，请填写 alipays://、alipayqr:// 或 https:// 开头的链接！"}');
	    }
	}
	if($qr_url!='' && $qr_url!='解码中' && !mym_pay_qr_url_matches_type($type,$qr_url)){
	    exit('{"code":-1,"msg":"收款码链接与所选支付类型不匹配，请检查是否选错了支付宝/QQ/微信通道！"}');
	}
	if($nums>=$userrow['type']){
	    exit('{"code":-1,"msg":"您的配额已上限，请购买配额再次上传！"}');
	}elseif(!$qr_url and $custom_qr_url=='' and $type=='wxpay' and $channel!='mg_vzq' and $hook_type!=2){
		exit('{"code":-1,"msg":"请解析二维码或填写自定义收款码链接！"}');
	}elseif($type=='alipay' and $channel=='mg_ali' and $qr_url!='' and !preg_match('/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/)/i', $qr_url)){
		exit('{"code":-1,"msg":"支付宝收款码链接格式不正确，请填写 alipays://、alipayqr:// 或 https:// 开头的链接！"}');
	}elseif($qr_url=='解码中'){
		exit('{"code":-1,"msg":"上传失败，请先等二维码解码成功再点击上传！"}');
	}elseif($type=='usdt'){
	    $cookie = $qr_url;
	    $status = 1;
	}else{
	    $cookie = 0;
	    $status = 0;
	}
	if($hook_type == 2){
	    $json_data = array();
	    if($type=='alipay' && $channel=='yd_ali'){
	        // 支付宝免 CK 走服务器本地免挂配置，不依赖云端登录服务器。
	        $json_data['ali_order_check'] = $ali_order_check;
	    }else{
	        if(!$Login_Type)exit('{"code":-1,"msg":"请选择登录服务器"}');
	        $json_data['Login_Id'] = $Login_Type;
	    }
	    $json = jsondet($json_data);
	}else{
	    $json = 'NULL';
	}
	if($custom_qr_url!=''){
	    $json_data = array('custom_qr_url'=>$custom_qr_url);
	    if($json!='NULL')$json_data = array_merge(json_decode($json,true), $json_data);
	    $json = jsondet($json_data);
	}
	if($receiver_surname!=''){
	    $json_data = array('receiver_surname'=>$receiver_surname);
	    if($json!='NULL'){
	        $old_json_data = json_decode($json,true);
	        if(!is_array($old_json_data))$old_json_data = array();
	        $json_data = array_merge($old_json_data, $json_data);
	    }
	    $json = jsondet($json_data);
	}
	$sqs=$DB->exec("INSERT INTO `pay_qrlist` (`pid`,`type`, `qr_url`,`cookie`,`pay_user`,`pay_pass`,`money`,`beizhu`,`status`,`hook_type`,`addtime`,`channel`,`json`) VALUES ('{$userrow['pid']}','{$type}','{$qr_url}','{$cookie}','{$user}','{$pass}','0.00','{$beizhu}','{$status}','{$hook_type}','{$date}','{$channel}','{$json}')");
	if($sqs){
		Add_log($userrow['pid'],'添加支付通道');
		$result=array("code"=>1,"msg"=>"添加支付通道成功");
	}else{
		$result=array("code"=>-1,"msg"=>"添加支付通道失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
	}
}elseif($act=='Add_Dmf'){
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
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
}elseif($act=='Del_Qr'){//删除二维码
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
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
}elseif($act=='Del_Dmf'){//删除二维码
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
	$id=daddslashes($_POST['id']);
	$is=$DB->query("SELECT * FROM `pay_dmf` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
		if(!$is){
			$result=array("code"=>-2,"msg"=>"非法操作");
		}else{
			$sql="DELETE FROM `pay_dmf` WHERE `id`='{$id}' limit 1";
			if($DB->exec($sql)){
				Add_log($userrow['pid'],'删除二维码');
				$result=array("code"=>1,"msg"=>"删除成功");
			}else{
				$result=array("code"=>-1,"msg"=>"删除失败");
			}
		}
}elseif($act=='Get_Qr'){	//取二维码数据
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问，有问题请访问官网g.9o3.cn购买程序！')));
	$id=daddslashes($_POST['id'])?daddslashes($_POST['id']):daddslashes($_GET['id']);
	$is=$DB->query("SELECT id,type,beizhu,cookie,hook_type,channel,crontime,json FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"非法操作");
	}else{
		$login_time = time();
		$rs=$DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `status`='1' and `login_time`>'{$login_time}' order by sort ASC");
		while($res = $rs->fetch())
		{
			$data[]=$res;
		}
		if(!$DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `status`='1' and `login_time`>'{$login_time}' limit 1")->fetch()){
			$data[]=array("wx_name"=>"暂无可添加微信","wx_user"=>"请联系站长");
		}
		$is['data_msg'] = $is['data_data'];
		$is['status_status'] = $is['status'];
		$result=array("code"=>1,"id"=>$is['id'],"type"=>$is['type'],"beizhu"=>$is['beizhu'],"data"=>$data,"qrdata"=>$is,'time'=>time()-10);
	}
}elseif($act=='Get_Login_QrCode'){//提交取登录二维码请求
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
	$hook = daddslashes($_POST['hook']);
	$type = daddslashes($_POST['type']);
	$beizhu = daddslashes($_POST['beizhu']);
	$channel = daddslashes($_POST['channel']);
	$Login_Type = daddslashes($_POST['Login_Type']);
	$qr_id = daddslashes($_POST['qr_id']);
	$row=$DB->query("select * from pay_qrlist where id='{$qr_id}' and pid='{$userrow['pid']}' limit 1")->fetch();
	if(!$row)exit(json_encode(array('code'=>-1,'msg'=>'通道不存在或无权限'),JSON_UNESCAPED_UNICODE));
	$json = json_decode($row['json'],true);
	if(!is_array($json))$json = array();
	$needs_yund_login = ($hook==2 && !($type=='alipay' && $channel=='yd_ali'));
	if($needs_yund_login && !$json['Login_Id'])exit(json_encode(array('code'=>-1,'msg'=>'当前通道没有绑定登录服务器，请删除后重新添加并选择登录服务器'),JSON_UNESCAPED_UNICODE));
	$yundrow = $needs_yund_login ? $DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}' limit 1")->fetch() : false;
	$apiurl = $yundrow ? $yundrow['url'] : '';
	if($needs_yund_login && (!$yundrow || !$apiurl))exit(json_encode(array('code'=>-1,'msg'=>'登录服务器不存在或已删除，请检查后台云端配置'),JSON_UNESCAPED_UNICODE));
	if($needs_yund_login && isset($yundrow['status']) && $yundrow['status']!='1')exit(json_encode(array('code'=>-1,'msg'=>'登录服务器已关闭，请先在后台启用云端'),JSON_UNESCAPED_UNICODE));
	if($hook==2 && $type=='wxpay'){
	    if($channel=='yd_vzq'){
	        require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
	        $QqApi = new QqApi($apiurl);
	        $result = $QqApi->Add_QQ($beizhu,$Login_Type);
	        $result=array("code"=>$result['code'],"msg"=>$result['msg'],"id"=>$result['id'],"qr_url"=>$result['qr_url']);
	    }else{
	        require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
	        $WxApi = new WxApi($apiurl);
	        
	        if($channel=='yd_wx'){
	            $result = $WxApi->wxGetLoginQrcode();
	        }elseif($channel=='yd_wx_gskd' or $channel=='yd_wx_sskd'){
	            $result = $WxApi->GetQr();
	            exit(json_encode($result,JSON_UNESCAPED_UNICODE));
	        }
	        if($channel=='yd_wx_uos'){
	            $WxApi = new wxuos($apiurl);
	            $result = $WxApi->get_qrcode();
	            $result['qr_url']=$result['qrurl'];
	            //{"code":1,"guid":"315c0868-8fa3-dee5-bfaf-d5828ed0b248","uuid":"IbsWTf6QDg==","qrurl":"https:\/\/login.weixin.qq.com\/qrcode\/IbsWTf6QDg=="}
	        }
	        $result = array("code"=>$result['code'],"guid"=>$result['guid'],"uuid"=>$result['uuid'],"qr_url"=>$result['qr_url'],"msg"=>$result['msg']);
	    }
	}elseif($hook==2 && $type=='qqpay'){
	    require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
        $QqApi = new QqApi($apiurl);
        $site = $siteurl ? $siteurl : (is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/';
        $result = $QqApi->Add_QQ($beizhu,$Login_Type,$site,$userrow['pid'],$userrow['key']);
        if(!is_array($result))$result = array('code'=>-1,'msg'=>'QQ云端接口无响应','id'=>'','qr_url'=>'');
		$result=array("code"=>intval($result['code']),"msg"=>$result['msg']?$result['msg']:'获取登录二维码失败',"id"=>$result['id']?$result['id']:'',"qr_url"=>$result['qr_url']?$result['qr_url']:'');
	}else{
	    require_once SYSTEM_ROOT.'Mym_Class/QrLogin.Class.php';
		$Login_Qrcode = new Login_Qrcode();
		if($type=='alipay'){
			$data 	= $Login_Qrcode->Get_ALILogin_Qr();
		}else{
			$data	= $Login_Qrcode->Get_QQLogin_Qr();
		}
		$msg 	= '成功获取登录二维码';
		$result['id'] = $data['id'];
		$result['qr_url'] = $data['qr_url'];
		$result=array("code"=>1,"msg"=>'获取登录二维码成功',"id"=>$data['id'],"qr_url"=>$data['qr_url']);
	}
}elseif($act=='Get_Login_Cookie'){//取登录cookie
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
	$id = daddslashes($_POST['id']);
	$hook = daddslashes($_POST['hook']);
	$qr_id = daddslashes($_POST['qr_id']);
	$type = daddslashes($_POST['type']);
	$guid = daddslashes($_POST['guid']);
	$uuid = daddslashes($_POST['uuid']);
	$channel = daddslashes($_POST['channel']);
	$row=$DB->query("select * from pay_qrlist where id='{$qr_id}' and pid='{$userrow['pid']}' limit 1")->fetch();
	if(!$row)exit(json_encode(array('code'=>-1,'msg'=>'通道不存在或无权限'),JSON_UNESCAPED_UNICODE));
	$json = json_decode($row['json'],true);
	if(!is_array($json))$json = array();
	$needs_yund_login = ($row['hook_type']==2 && !($type=='alipay' && $channel=='yd_ali'));
	$yundrow = $needs_yund_login ? $DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}' limit 1")->fetch() : false;
	$apiurl = $yundrow ? $yundrow['url'] : '';
	if($needs_yund_login && (!$yundrow || !$apiurl))exit(json_encode(array('code'=>-1,'msg'=>'登录服务器不存在或已删除，请检查后台云端配置'),JSON_UNESCAPED_UNICODE));
	if($row['hook_type']==2 && $type=='wxpay'){
	    if($channel=='yd_vzq'){
	        require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
	        $QqApi = new QqApi($apiurl);
	        $resdata = $QqApi->Add_Qr($id,$row['beizhu']);
	    }else{
	        require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
	        $WxApi = new WxApi($apiurl);
	        if($channel=='yd_wx'){
	            $resdata = $WxApi->wxCheckLoginQrcode($guid,$uuid);
	        }elseif($channel=='yd_wx_gskd' or $channel=='yd_wx_sskd'){
	            $resdata = $WxApi->Login($guid,$uuid);
	        }
	        if($resdata['code']==1)$resdata['code']=200;
	        $resdata['cookie'] = $guid;
	        $resdata['id'] = $qr_id;
	        if($channel=='yd_wx_uos'){
	            $WxApi = new wxuos($apiurl);
	            $resdata = $WxApi->get_login($guid,$uuid);
	            if($resdata['code']==1){
	                $resdata=['code'=>200,'msg'=>'登录成功','id'=>$id,'cookie'=>$guid];
	            }else{
	                $resdata=['code'=>1,'msg'=>'未登录'];
	            }
	        }
	    }
	}elseif($row['hook_type']==2 && $type=='qqpay'){
	    require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
		$QqApi = new QqApi($apiurl);
		$resdata = $QqApi->Add_Qr($id,$row['beizhu']);
	}else{
	    require_once SYSTEM_ROOT.'Mym_Class/QrLogin.Class.php';
		$Login_Qrcode = new Login_Qrcode();
	    if($type=='alipay'){
	        $resdata = $Login_Qrcode->Check_AliLogin($id);
	    }else{
		    $resdata = $Login_Qrcode->Check_QQLogin($id);
	    }
	}
	$result=array("code"=>$resdata['code'],"msg"=>$resdata['msg'],"id"=>$resdata['id'],"cookie"=>$resdata['cookie']);
	if($result['code']==200 and $result['cookie']){
	    if($row['type'] and $row['channel']=='yd_vzq'){
	        $row['type'] = 'qqpay';
	    }
	    /*
	    $username = username($result['cookie'],$is['type']);
        $name = $username['userName'].'|'.$username['email'];
        if(PayName($username['userName'])){
	       exit('{"code":-1,"msg":"你已经列入，本系统的实体清单，有问题请联系QQ485570653"}');
	    }
	    */
		$ck_next_time=time()+300;
		$ck_money='0.00';
		$Pay_Money=Pay_Money_Get($row['type'],$result['cookie']);
		if(isset($Pay_Money['status']) && $Pay_Money['status'] && isset($Pay_Money['money']) && $Pay_Money['money'] !== '' && $Pay_Money['money'] >= 0){
		    $ck_money=$Pay_Money['money'];
		}
		$ck_json=json_decode($row['json'],true);
		if(!is_array($ck_json))$ck_json=array();
		$ck_json['ck_fail_count']=0;
		$ck_json['ck_last_success']=time();
		$ck_json['ck_last_check']=time();
		$ck_json['ck_last_error']='';
		$ck_json=daddslashes(json_encode($ck_json, JSON_UNESCAPED_UNICODE));
		$sqs=$DB->exec("update `pay_qrlist` set `cookie`='{$result['cookie']}',`money`='{$ck_money}',`status`='1',`crontime`='{$ck_next_time}',`addtime`='{$date}',`data_data`='{$name}',`email_status`='0',`json`='{$ck_json}' where id='{$qr_id}' and pid='{$userrow['pid']}'");
		if($sqs){
		    Add_log($userrow['pid'],'更新二维码: '.$qr_id);
		    $result=array("code"=>200,"msg"=>"更新成功");
		}else{
		    $result=array("code"=>-1,"msg"=>"更新失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
		}
	}
}elseif($act=='Qrlist'){//二维码详细
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
	$id=intval($_GET['id']);
	$row=$DB->query("select * from pay_qrlist where id='{$id}' and pid='{$userrow['pid']}' limit 1")->fetch();
	if(!$row){
		exit('{"code":-1,"msg":"当前订单不存在或未成功选择支付通道！"}');
	}
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
  $row['type'] = '<img src="/Mym/Assets/Icon/'.$row['type'].'.ico" width="16">'.pay_type($row);
	
	$result=array("code"=>0,"msg"=>"succ","data"=>$row);
}elseif($act=='Set_Receiver_Surname'){//设置单个通道收款人姓
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
    $id=intval($_POST['id']);
    $receiver_surname=isset($_POST['receiver_surname']) ? trim(strip_tags($_POST['receiver_surname'])) : '';
    $receiver_surname=daddslashes(mb_substr($receiver_surname,0,10,'UTF-8'));
    if($id<=0)exit(json_encode(array('code'=>-1,'msg'=>'通道ID不能为空'),JSON_UNESCAPED_UNICODE));
    $row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `id`='{$id}' and `pid`='{$userrow['pid']}' limit 1")->fetch();
    if(!$row)exit(json_encode(array('code'=>-1,'msg'=>'通道不存在或无权限'),JSON_UNESCAPED_UNICODE));
    $json_data=json_decode($row['json'],true);
    if(!is_array($json_data))$json_data=array();
    if($receiver_surname===''){
        if(isset($json_data['receiver_surname']))unset($json_data['receiver_surname']);
    }else{
        $json_data['receiver_surname']=$receiver_surname;
    }
    $json = empty($json_data) ? 'NULL' : jsondet($json_data);
    $sqs=$DB->exec("UPDATE `pay_qrlist` SET `json`='{$json}' WHERE `id`='{$id}' and `pid`='{$userrow['pid']}' limit 1");
    if($sqs!==false){
        Add_log($userrow['pid'],'设置通道收款人姓: '.$id);
        $result=array('code'=>1,'msg'=>'收款人姓设置成功');
    }else{
        $result=array('code'=>-1,'msg'=>'收款人姓设置失败'.'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
    }
}elseif($act=='Edit_Qr_Info'){//编辑单个通道收款码和备注
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
    $id=intval($_POST['id']);
    $qr_url=isset($_POST['qr_url']) ? trim($_POST['qr_url']) : '';
    $custom_qr_url=isset($_POST['custom_qr_url']) ? trim($_POST['custom_qr_url']) : '';
    $beizhu=isset($_POST['beizhu']) ? daddslashes(htmlspecialchars($_POST['beizhu'])) : '';
    $receiver_surname=isset($_POST['receiver_surname']) ? trim(strip_tags($_POST['receiver_surname'])) : '';
    $receiver_surname=daddslashes(mb_substr($receiver_surname,0,10,'UTF-8'));
    if($id<=0)exit(json_encode(array('code'=>-1,'msg'=>'通道ID不能为空'),JSON_UNESCAPED_UNICODE));
    $row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `id`='{$id}' and `pid`='{$userrow['pid']}' limit 1")->fetch();
    if(!$row)exit(json_encode(array('code'=>-1,'msg'=>'通道不存在或无权限'),JSON_UNESCAPED_UNICODE));
    if($qr_url!='' && $qr_url!='解码中' && !mym_pay_qr_url_matches_type($row['type'],$qr_url)){
        exit(json_encode(array('code'=>-1,'msg'=>'收款码链接与当前通道支付类型不匹配，请检查是否填错支付宝/QQ/微信收款码'),JSON_UNESCAPED_UNICODE));
    }
    if($custom_qr_url!=''){
        if(!preg_match('/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/|weixin:\/\/|mqqapi:\/\/)/i', $custom_qr_url)){
            exit(json_encode(array('code'=>-1,'msg'=>'自定义收款码链接格式不正确，请填写 http(s)、alipays、alipayqr、weixin 或 mqqapi 开头的链接'),JSON_UNESCAPED_UNICODE));
        }
        if(!mym_pay_qr_url_matches_type($row['type'],$custom_qr_url)){
            exit(json_encode(array('code'=>-1,'msg'=>'自定义收款码链接与当前通道支付类型不匹配，请检查是否填错支付宝/QQ/微信收款码'),JSON_UNESCAPED_UNICODE));
        }
    }
    if($row['type']=='alipay' && $row['channel']=='mg_ali' && $qr_url!='' && !preg_match('/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/)/i', $qr_url)){
        exit(json_encode(array('code'=>-1,'msg'=>'支付宝收款码链接格式不正确，请填写 alipays://、alipayqr:// 或 https:// 开头的链接'),JSON_UNESCAPED_UNICODE));
    }
    if($row['type']=='alipay' && $row['channel']=='mg_ali' && $custom_qr_url!='' && !preg_match('/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/)/i', $custom_qr_url)){
        exit(json_encode(array('code'=>-1,'msg'=>'支付宝自定义收款码链接格式不正确，请填写 alipays://、alipayqr:// 或 https:// 开头的链接'),JSON_UNESCAPED_UNICODE));
    }
    if($qr_url=='解码中')exit(json_encode(array('code'=>-1,'msg'=>'请先等二维码解码成功后再保存'),JSON_UNESCAPED_UNICODE));
    $json_data=json_decode($row['json'],true);
    if(!is_array($json_data))$json_data=array();
    if($custom_qr_url===''){
        if(isset($json_data['custom_qr_url']))unset($json_data['custom_qr_url']);
    }else{
        $json_data['custom_qr_url']=$custom_qr_url;
    }
    if($receiver_surname===''){
        if(isset($json_data['receiver_surname']))unset($json_data['receiver_surname']);
    }else{
        $json_data['receiver_surname']=$receiver_surname;
    }
    $json = empty($json_data) ? 'NULL' : jsondet($json_data);
    $qr_url=daddslashes($qr_url);
    $sqs=$DB->exec("UPDATE `pay_qrlist` SET `qr_url`='{$qr_url}',`beizhu`='{$beizhu}',`json`='{$json}' WHERE `id`='{$id}' and `pid`='{$userrow['pid']}' limit 1");
    if($sqs!==false){
        Add_log($userrow['pid'],'编辑通道收款码: '.$id);
        $result=array('code'=>1,'msg'=>'通道收款码保存成功');
    }else{
        $result=array('code'=>-1,'msg'=>'通道收款码保存失败'.'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
    }
}elseif($act=='Test_Qr_Order'){//测试单个支付通道
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
    // 测试下单会访问第三方通道接口，先释放 session 锁，避免慢接口阻塞用户中心其它请求。
    if(function_exists('session_status') && session_status() === PHP_SESSION_ACTIVE)session_write_close();
    @set_time_limit(20);
    $id=intval($_POST['id']);
    $money_raw=trim($_POST['money']);
    $name=trim(strip_tags(daddslashes($_POST['name'])));
    if($name!='测试商品')$name='测试商品';
    if($id<=0)exit(json_encode(array('code'=>-1,'msg'=>'通道ID不能为空'),JSON_UNESCAPED_UNICODE));
    if(!preg_match('/^\d+(\.\d{1,2})?$/',$money_raw))exit(json_encode(array('code'=>-1,'msg'=>'测试金额格式错误，最多保留2位小数'),JSON_UNESCAPED_UNICODE));
    $money=number_format(floatval($money_raw),2,'.','');
    if($money<=0)exit(json_encode(array('code'=>-1,'msg'=>'测试金额必须大于0'),JSON_UNESCAPED_UNICODE));
    $QR_row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `id`='{$id}' and `pid`='{$userrow['pid']}' limit 1")->fetch();
    if(!$QR_row)exit(json_encode(array('code'=>-1,'msg'=>'通道不存在或无权限'),JSON_UNESCAPED_UNICODE));
    if($QR_row['qr_status']!=1)exit(json_encode(array('code'=>-1,'msg'=>'当前通道已关闭，请先开启后再测试'),JSON_UNESCAPED_UNICODE));
    if($QR_row['status']!=1)exit(json_encode(array('code'=>-1,'msg'=>'当前通道未在线或未完成更新，请先更新通道后再测试'),JSON_UNESCAPED_UNICODE));
    $skip_live_ck_check = (isset($_POST['skip_live_ck_check']) && intval($_POST['skip_live_ck_check'])==1);
    if(!$skip_live_ck_check){
        $ck_check=mym_check_qr_ck_online($QR_row);
        if($ck_check['need_check'] && !$ck_check['online']){
            Add_log($userrow['pid'],'测试支付通道前CK检测异常但继续创建订单：'.$QR_row['id'].'，'.$ck_check['msg']);
        }
    }
    if($QR_row['type']=='usdt' && $money<7)exit(json_encode(array('code'=>-1,'msg'=>'USDT-TRC20 测试金额必须大于等于 7 元'),JSON_UNESCAPED_UNICODE));
    if(!mym_pay_channel_enabled($QR_row['type'],$QR_row['channel']))exit(json_encode(array('code'=>-1,'msg'=>'该支付类型或通道已被后台关闭，无法测试'),JSON_UNESCAPED_UNICODE));
    $pid=$userrow['pid'];
    $type=$QR_row['type'];
    $out_trade_no='TEST'.$id.date('YmdHis').rand(111,999);
    $trade_no=date('YmdHis').rand(11111,99999);
    $notify_url=($_SERVER['SERVER_PORT'] == '443' || $conf['http']==1 ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/User/SDK/notify_url.php';
    $return_url=($_SERVER['SERVER_PORT'] == '443' || $conf['http']==1 ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/User/SDK/return_url.php';
    $outtime=$userrow['outtime']?$userrow['outtime']:$conf['outtime'];
    $outtime=time()+($outtime<180?180:$outtime);
    $time=time();
    if($type=='usdt'){
        $order_price=usdt('usdt',$money);
        $outtime=time()+1200;
    }else{
        $order_price=$money;
    }
    if($QR_row['hook_type']==1){
        $price_row=$DB->query("SELECT * FROM pay_order WHERE price='{$order_price}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1")->fetch();
    }else{
        $price_row=$DB->query("SELECT * FROM pay_order WHERE price='{$order_price}' and qr_id='{$QR_row['id']}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1")->fetch();
    }
    if($price_row){
        $num=1;
        for($x=0;$x<=$num;$x++){
            $order_price=$order_price+0.01;
            if($QR_row['hook_type']==1){
                $Sql="SELECT * FROM pay_order WHERE price='{$order_price}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1";
            }else{
                $Sql="SELECT * FROM pay_order WHERE price='{$order_price}' and qr_id='{$QR_row['id']}' and outtime>'{$time}' and pid='{$pid}' and status='0' limit 1";
            }
            $price_row=$DB->query($Sql)->fetch();
            if(!$price_row){
                $num=0;
            }else{
                $num=$num+1;
            }
        }
    }
    $price=number_format(floatval($order_price),2,'.','');
    if($type=='usdt')$price=$order_price;
    $apitime=time()+10;
    $data=qrdecode($QR_row,$price,$trade_no,array('test_order'=>true,'skip_live_ck_check'=>$skip_live_ck_check));
    if(is_array($data) && isset($data['code']) && intval($data['code'])==-1){
        $retryable = isset($data['retryable']) ? intval($data['retryable']) : 0;
        $err_msg = isset($data['msg']) && $data['msg'] ? $data['msg'] : '测试订单二维码生成失败';
        exit(json_encode(array('code'=>-1,'msg'=>$err_msg,'retryable'=>$retryable),JSON_UNESCAPED_UNICODE));
    }
    if(!is_array($data))$data=array();
    $qr_url=isset($data['qr_url']) ? $data['qr_url'] : '';
    $api_trade_no=isset($data['api_trade_no']) ? $data['api_trade_no'] : 'NULL';
    if(!$api_trade_no)$api_trade_no='NULL';
    if(!$qr_url && !($type=='qqpay' && $QR_row['hook_type']==0 && $QR_row['channel']=='mg_qq'))$qr_url=urlencode($QR_row['qr_url']);
    if($qr_url==='')exit(json_encode(array('code'=>-1,'msg'=>'测试订单二维码为空，请先更新通道收款码后再测试'),JSON_UNESCAPED_UNICODE));
    $sqs=$DB->query("insert into `pay_order` (`trade_no`,`out_trade_no`,`api_trade_no`,`notify_url`,`return_url`,`type`,`pid`,`addtime`,`name`,`money`,`qr_id`,`price`,`pay_id`,`qr_url`,`apitime`,`outtime`,`status`,`date`) values ('{$trade_no}','{$out_trade_no}','{$api_trade_no}','{$notify_url}','{$return_url}','{$type}','{$pid}','{$date}','{$name}','{$money}','{$QR_row['id']}','{$price}','{$ip}','{$qr_url}','{$apitime}','{$outtime}','0','".date('Y-m-d')."')");
    if(!$sqs)exit(json_encode(array('code'=>-1,'msg'=>'创建测试订单失败：'.'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]),JSON_UNESCAPED_UNICODE));
    Add_log($userrow['pid'],'测试支付通道：'.$QR_row['id'].'，订单：'.$trade_no);
    $notice = isset($data['test_order_notice']) && $data['test_order_notice'] ? $data['test_order_notice'].'，' : '';
    $result=array('code'=>1,'msg'=>$notice.'测试订单创建成功，请跳转支付并确认是否能正常回调','trade_no'=>$trade_no,'out_trade_no'=>$out_trade_no,'price'=>$price,'url'=>'/Submit/Mym_Pay.php?trade_no='.$trade_no);
}elseif($act=='Pay_Notify'){//补单并回调
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
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
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
	if(isset($_SESSION['Reset_Token']) && $_SESSION['Reset_Token']>time()-3600){
		exit('{"code":-1,"msg":"请勿频繁修改秘钥"}');
	}
	$key = random(11);
	Add_log($userrow['pid'],'重置key密钥');
	$DB->exec("update `pay_user` set `key` ='{$key}' where `pid`='{$userrow['pid']}'");
	$_SESSION['Reset_Token']=time();
	exit('{"code":1,"msg":"succ"}');
}elseif($act=='User_Code'){
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
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
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
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