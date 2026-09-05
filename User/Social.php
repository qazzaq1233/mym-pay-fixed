<?php 
require_once ('../Core/Common.php');
require_once ("../Core/Core_Class/Oauth.class.php");
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']. '/';//获取本地域名
$allapi	 = $conf['Instant_url'];//QQ快捷登录API地址
$Oauth = new Oauth();
header("Content-Type: text/html; charset=UTF-8");
if ($_GET['code']&&!$_GET['my']) {
    $array = $Oauth->callback();
    $social_uid	  	=	 $array['social_uid'];//固定值 可作为账号
    $access_token 	=	 $array['access_token'];//固定值 可作为密码
    $gender		  	=	 $array['gender'];//性别
	$nickname	 	=	 match_chinese($array['nickname']);//QQ名称
    $figureurl_qq_1 =	 $array['figureurl_qq_1'];//大小为40×40像素的QQ头像URL
    $figureurl_qq_2	=	 $array['figureurl_qq_2'];//[大小为100×100像素的QQ头像URL。不是所有的用户都拥有QQ的100×100的头像。]
	$vip	 	 	=	 $array['vip'];//标识用户是否为黄钻用户（0：不是；1：是）
    $level			=	 $array['level'];//黄钻等级
	$is_yellow_year_vip= $array['is_yellow_year_vip'];//标识是否为年费黄钻用户（0：不是； 1：是）
	
	$_SESSION['social_uid']	  		=	 $social_uid;//固定值 可作为账号
    $_SESSION['access_token']	  	=	 $access_token;//固定值 可作为密码
    $_SESSION['gender']  			=	 $gender;//性别
	$_SESSION['nickname']		  	=	 $nickname;//QQ名称
    $_SESSION['figureurl_qq_1']  	=	 $figureurl_qq_1;//大小为40×40像素的QQ头像URL
    $_SESSION['figureurl_qq_2'] 	=	 $figureurl_qq_2;//[大小为100×100像素的QQ头像URL。不是所有的用户都拥有QQ的100×100的头像。]
	
	
	

if($social_uid or $access_token){
	$Is_qq_id=$DB->query("SELECT * FROM pay_cloud WHERE qq_id='{$social_uid}' limit 1")->fetch();
	if(!$Is_qq_id){
		$rs=$DB->query("SELECT * FROM pay_cloud WHERE nickname!='' and qq_id=''");
		while($res = $rs->fetch())
		{
			if(MD5($nickname) == MD5($res['nickname'])){
				$cloud_res = $res;
				$cloud_id = $res['id'];
				$cloud_qq = $res['qq'];
				$nickname = '自动绑定';
			}
		}
		if(!$cloud_id){
			echo'<script>alert("注意：首次登陆【不允许QQ网名有特殊符号】,如有特殊符号请先修改再多登录两次试试,当前登录QQ未检测到旗下存在授权域名,如有误请联系客服处理!");location.href="./Buy.php"</script>';
		}else{
			$is_check = $DB->query("SELECT * FROM `pay_check` WHERE `status`='1' and `qq`='{$cloud_res['qq']}' limit 1")->fetch();
			if(!$is_check){
				exit("<script language='javascript'>alert('您的授权已被禁封！');window.location.href='/';</script>");
			}else{
				$Is_qq_id['qq_id'] = $Is_qq_id['qq_id']?$Is_qq_id['qq_id']:$social_uid;
				$Is_qq_id['qq'] = $Is_qq_id['qq']?$Is_qq_id['qq']:$cloud_qq;
				$session=md5($Is_qq_id['qq_id'].$Is_qq_id['qq'].$password_hash);
				$expiretime=time()+604800;
				$token=authcode("{$Is_qq_id['qq_id']}\t{$session}\t{$expiretime}", 'ENCODE', $conf['KEY']);
				setcookie("user_token", $token, time() + 604800);
				$DB->exec("update `pay_cloud` set `qq_id` = '{$social_uid}' WHERE `id`='{$cloud_id}' limit 1");
				echo'<script>alert("首次登陆并成功'.$nickname.'授权QQ：'.$cloud_qq.'，欢迎光临!");location.href="./"</script>';
			}
		}
	}else{
		$is_check = $DB->query("SELECT * FROM `pay_check` WHERE `status`='1' and `qq`='{$Is_qq_id['qq']}' limit 1")->fetch();
		if(!$is_check){
			exit("<script language='javascript'>alert('您的授权已被禁封！');window.location.href='/';</script>");
		}else{
			$session=md5($Is_qq_id['qq_id'].$Is_qq_id['qq'].$password_hash);
			$expiretime=time()+604800;
			$token=authcode("{$Is_qq_id['qq_id']}\t{$session}\t{$expiretime}", 'ENCODE', $conf['KEY']);
			setcookie("user_token", $token, time() + 604800);
			echo'<script>alert("'.$nickname.'('.$Is_qq_id['qq'].')，欢迎回来!");location.href="./"</script>';
		}
	}
}else{
	echo'<script>alert("02错误信息，请联系客服处理!");location.href="./"</script>';
}

	
	}elseif(isset($_GET['logout'])){	
		setcookie("user_token", "", time() - 604800);
		exit("<script language='javascript'>alert('您已成功注销本次登陆！');window.location.href='/';</script>");
	}else{	
	//授权QQ快捷登陆赋值昵称
	$rs=$DB->query("SELECT * FROM `pay_cloud` WHERE qq_id='' order by rand() limit 3");
	while($res = $rs->fetch())
	{
		$getQQNick = match_chinese(getQQNick($res['qq']));
		$DB->exec("update `pay_cloud` set `nickname` = '{$getQQNick}' WHERE `id`='{$res['id']}' limit 1");
	}
    $Oauth->login();
} 
?>
