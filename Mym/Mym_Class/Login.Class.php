<?php
$password_hash='!@#%!s!0';
//登录判断逻辑  开始
if(isset($_COOKIE["admin_token"])) //后台登陆验证
{
	$token=authcode(daddslashes($_COOKIE['admin_token']), 'DECODE', $conf['KEY']);
	list($user, $sid) = explode("\t", $token);
	$session=md5($conf['admin_user'].$conf['admin_pass'].$password_hash);
	if($session==$sid) {
		$islogin_admin=1;
	}else{
		$islogin_admin='登录不成功';
	}
}
if(isset($_COOKIE["user_token"])) //前台登陆验证
{
	$token=authcode(daddslashes($_COOKIE['user_token']), 'DECODE', $conf['KEY']);
	list($pid, $sid, $expiretime) = explode("\t", $token);
	$userrow=$DB->query("SELECT * FROM pay_user WHERE user='{$pid}' limit 1")->fetch();
	if($userrow and $userrow['pass']){
		$pid = $userrow['user'];
		$key = $userrow['pass'];
	}else{
		$userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
		$pid = $userrow['pid'];
		$key = $userrow['key'];
	}
	$session=md5($pid.$key.$password_hash);
	if($session==$sid && $expiretime>time()) {
		$pid = $userrow['pid'];
		$key = $userrow['key'];
		if($userrow['email_status']!=1){
		    sysmsg("请检查邮箱完成邮箱验证，如果获取不到邮箱，请联系管理QQ".$conf['qq']);
		}elseif($userrow['status']!=1){
		    sysmsg("商户已被封禁，由于您违规操作导致账号封禁，详情请联系管理QQ".$conf['qq']);
		}
		$islogin_user=1;
	}else{
		$islogin_user='登录不成功';
	}
}


if($islogin_user==1 && (isset($_COOKIE["pay_pass"])!=$userrow['pay_pass']))
{
    $user_pass = false;
}else{
    $user_pass = true;
}
function pay_pass($cookie){
    global $userrow;
    if(authcode($cookie, 'DECODE', $conf['KEY'])!==$userrow['pay_pass']){
        return false;
    }else{
        return true;
    }
}

//登录判断逻辑 结束
?>
