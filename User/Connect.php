<?php
/**
 * QQ互联
**/
include("../Mym/Common.php");//&& $conf['login_qq']!=1

if(isset($_GET['act']) && $_GET['act']=='qrlogin' ){
	if(isset($_SESSION['findpwd_qq']) && $qq=daddslashes($_SESSION['findpwd_qq'])){
		$user_row=$DB->query("SELECT * FROM pay_user WHERE qq='{$qq}' limit 1")->fetch();
		unset($_SESSION['findpwd_qq']);
		if($user_row){
			$pid=$user_row['pid'];
			$key=$user_row['key'];
			if($islogin_user==1){
				exit('{"code":-1,"msg":"当前QQ已绑定商户ID:'.$pid.'，请勿重复绑定！"}');
			}
			$isrow=$DB->query("SELECT * FROM pay_user WHERE user='{$user_row['user']}' limit 1")->fetch();
			if($isrow and $isrow['pass']){
				$pid = $isrow['user'];
				$key = $isrow['pass'];
			}else{
				$pid = $user_row['pid'];
				$key = $user_row['key'];
			}
			$session=md5($pid.$key.$password_hash);
			$expiretime=time()+604800;
			$token=authcode("{$pid}\t{$session}\t{$expiretime}", 'ENCODE', $conf['KEY']);
			setcookie("user_token", $token, time() + 604800);
			$result=array("code"=>0,"msg"=>"登录成功！正在跳转到用户中心","url"=>"./");
			$city=get_ip_city($ip)['Result']['Country'];
			$DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('".$user_row['pid']."','商户QQ扫码快捷登录','".$date."','".$ip."','".$city."')");
		}elseif($islogin_user==1){
			$result=array("code"=>0,"msg"=>"已成功登陆！","url"=>"./index.php");
		}else{
			$result=array("code"=>0,"msg"=>"系统不存在此绑定QQ,请检查重试","url"=>"./Login.php");
		}
	}else{
		$result=array("code"=>-1, "msg"=>"验证失败，请重新扫码");
	}
	exit(json_encode($result));
}elseif(isset($_GET['act']) && $_GET['act']=='login'){
    
}
if($islogin2==1 && !isset($_GET['bind'])){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}else{
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>QQ扫码登录 - <?php echo $conf['sitename'] ?></title>

	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/util.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/main.css">
</head>

<body>

	<div class="limiter">
		<div class="container-login100" style="background-image: url('../Mym/Assets/Login/static/image/bg-01.jpg');">
			<div class="auth-page auth-page-compact">
				<div class="auth-hero">
					<div class="auth-brand">ID</div>
					<p class="auth-kicker">QUICK SIGN IN</p>
					<h1>QQ 快捷登录</h1>
					<p class="auth-desc">使用已绑定 QQ 扫码完成身份校验，快速进入商户中心。</p>
				</div>
				<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
					<span class="login100-form-title p-b-49">QQ登录</span>
					<p class="auth-subtitle">扫码确认后自动完成登录</p>
					<div class="flex-c-m">
					<div id="qrimg" class="list-group-item">
				</div>
				
			</div>
					<div class="flex-c-m auth-qr-tip">
						<span class="flex-c-m" id="loginmsg">请使用QQ手机版扫描二维码</span><span id="loginload" style="padding-left: 10px;color: #2563eb;">.</span>
					</div>
					<div class="list-group-item" id="mobile" style="display:none;"><button type="button" id="mlogin" onclick="mloginurlnew()" class="btn btn-warning btn-block">跳转QQ快捷登录</button><br/><button type="button" onclick="qrlogin()" class="btn btn-success btn-block">我已完成登录</button></div>
				</div>
			</div>
		</div>
	</div>

<script src="../Mym/Assets/Login/static/js/jquery-3.2.1.min.js"></script>
<script src="../Mym/Assets/Login/static/js/main.js"></script>
<script src="../Mym/Assets/Layer/layer.js"></script>
<script src="./Assets/js/qrlogin.js"></script>

</body>
</html>
<?php }?>