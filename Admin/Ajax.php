<?php
include("../Mym/Common.php");
@header('Content-Type: application/json; charset=UTF-8');
$act=$_GET['act'];
if($islogin_admin==1 or $act=='Login' or $act=='Captcha'){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
if($act=='Login'){//登录后台
if(!checkRefererHost())exit('{"code":403}');
	$admin_user=daddslashes($_POST['admin_user']);
	$admin_pass=daddslashes($_POST['admin_pass']);
	$code=daddslashes($_POST['code']);
	if(!$admin_user or !$admin_pass){
		$result=array("code"=>-1,"msg"=>"所有参数不能为空");
	}elseif($code && md5($admin_user)==$conf['admin_user'] && md5($admin_pass)==$conf['admin_pass']  && $conf['goid']){
	    $goid = $conf['goid'];
	    $ga = new \lib\GoogleAuthenticator();
	    $checkResult = $ga->verifyCode($goid, $code, 1);
	    if($checkResult){
	        $session=md5($conf['admin_user'].$conf['admin_pass'].$password_hash);
	        $token=authcode("{$user}\t{$session}", 'ENCODE', $conf['KEY']);
	        setcookie("admin_token", $token, time() + 604800);
	        $city=get_ip_city($ip)['Result']['Country'];
	        $DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('0','后台管理员登陆','".$date."','".$ip."','".$city."')");
	        $result=array("code"=>1,"msg"=>"登录成功");
	    }else{
	        exit(json_encode(["code"=>-1,"msg"=>"登录失败,谷歌验证码错误"]));
	    }
	}elseif(md5($admin_user)==$conf['admin_user'] && md5($admin_pass)==$conf['admin_pass'] && !$conf['goid']) {
		$session=md5($conf['admin_user'].$conf['admin_pass'].$password_hash);
		$token=authcode("{$user}\t{$session}", 'ENCODE', $conf['KEY']);
		setcookie("admin_token", $token, time() + 604800);
		$city=get_ip_city($ip)['Result']['Country'];
		$DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('0','后台管理员登陆','".$date."','".$ip."','".$city."')");
		$result=array("code"=>1,"msg"=>"登录成功");
	}elseif(md5($admin_user) != $conf['admin_user']) {
		$result=array("code"=>-2,"msg"=>"登录失败,账号错误");
	}elseif(md5($admin_pass) != $conf['admin_pass']) {
		$result=array("code"=>-3,"msg"=>"登录失败,密码错误");
	}else{
	    $result=array("code"=>-4,"msg"=>"登录失败,谷歌验证码错误");
	}
}elseif($act=='Captcha'){//滑动验证
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
}elseif($act=='iptype'){
    $result = [
	['name'=>'0_X_FORWARDED_FOR', 'ip'=>real_ip(0), 'city'=>get_ip_city(real_ip(0))['Result']['Country']],
	['name'=>'1_X_REAL_IP', 'ip'=>real_ip(1), 'city'=>get_ip_city(real_ip(1))['Result']['Country']],
	['name'=>'2_REMOTE_ADDR', 'ip'=>real_ip(2), 'city'=>get_ip_city(real_ip(2))['Result']['Country']]
	];
}elseif($act=='Get_Qr'){	//取二维码数据
	$id=daddslashes($_POST['id'])?daddslashes($_POST['id']):daddslashes($_GET['id']);
	$is=$DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `id`='{$id}' limit 1")->fetch();
    $result=array("code"=>1,"id"=>$is['id'],"type"=>'微信',"beizhu"=>htmlspecialchars($is['beizhu']));
}elseif($act=='Get_Login_QrCode'){//提交取登录二维码请求
		require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
        $WxApi = new WxApi();
		$result = $WxApi->GetQr();
	    $result = array("code"=>$result['code'],"guid"=>$result['guid'],"uuid"=>$result['uuid'],"qr_url"=>$result['qr_url'],"msg"=>$result['msg']);
}elseif($act=='Get_Login_Cookie'){//取登录cookie
		$id = daddslashes($_GET['id']);
		$hook = daddslashes($_GET['hook']);
		$qr_id = daddslashes($_GET['qr_id']);
		$type = daddslashes($_GET['type']);
		$guid = daddslashes($_GET['guid']);
	    $uuid = daddslashes($_GET['uuid']);
		require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
        $WxApi = new WxApi();
		$result = $WxApi->Login($guid,$uuid);
	    $result = array("code"=>$result['code'],"id"=>$qr_id,"cookie"=>$guid,"msg"=>$result['msg'],"nickName"=>$result['nickName'],"user"=>$result['user']);
}elseif($act=='Up_Qr'){//更新二维码
	$wx_user=daddslashes($_POST['wx_user']);
	$wx_name=daddslashes($_POST['wx_name']);
	$id=daddslashes($_POST['id']);
	$cookie=daddslashes($_POST['cookie']);
	$time = time()+120;
	$sqs=$DB->exec("update `pay_wechat_trumpet` set `wx_user`='{$wx_user}',`wx_name`='{$wx_name}',`cookie`='{$cookie}',`status`='1',`addtime`='{$date}',`login_time`='{$time}' where id='{$id}'");
	if($sqs){
		Add_log($userrow['pid'],'更新二维码');
		$result=array("code"=>1,"msg"=>"更新成功");
	}else{
		$result=array("code"=>-1,"msg"=>"更新失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
	}
}elseif($act=='Add_Wechet_Tp'){//添加微信免挂店员
	$wx_user= daddslashes($_POST['wx_user']);
	$wx_name= daddslashes($_POST['wx_name']);
    $beizhu= daddslashes(htmlspecialchars($_POST['beizhu']));
	$sort= daddslashes($_POST['sort']);
	$hook_type= daddslashes($_POST['hook_type']);
	$sds=$DB->exec("INSERT INTO `pay_wechat_trumpet` (`wx_user`,`wx_name`,`beizhu`, `sort`, `login_time`,`hook_type`,`status`,`addtime`) VALUES ('{$wx_user}','{$wx_name}','{$beizhu}','{$sort}','0','{$hook_type}','1','{$date}')");
	if($sds){
		$result=array("code"=>200,"msg"=>"添加微信免挂店员成功");
	}else{
		$result=array("code"=>-1,"msg"=>"添加店员失败");
	}
}elseif($act=='Edit_Wechet_Tp'){	//修改微信免挂店员
	$id= daddslashes($_POST['id']);		
	$wx_user= daddslashes($_POST['wx_user']);
	$wx_name= daddslashes($_POST['wx_name']);
    $beizhu= daddslashes(htmlspecialchars($_POST['beizhu']));
	$sort= daddslashes($_POST['sort']);
	$status=is_numeric(daddslashes($_POST['status']))?intval(daddslashes($_POST['status'])):1;
	$is=$DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `id`='{$id}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"此记录不存在");
	}else{
		$sqs=$DB->exec("update `pay_wechat_trumpet` set `wx_user`='{$wx_user}',`wx_name`='{$wx_name}',`beizhu`='{$beizhu}',`sort` ='{$sort}',`status` ='{$status}' where id='{$id}'");
		if($sqs){
			Add_log('admin','修改微信免挂店员');
			$result=array("code"=>200,"msg"=>"修改微信免挂店员成功");
		}else{
			$result=array("code"=>-1,"msg"=>"修改微信免挂店员失败");
		}
	}
}elseif($act=='Del_Wechet_Tp'){//删除微信店员
	$id=daddslashes($_POST['id']);
	$is=$DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `id`='{$id}' limit 1")->fetch();
		if(!$is){
			$result=array("code"=>-2,"msg"=>"此记录不存在");
		}else{
			$sql="DELETE FROM `pay_wechat_trumpet` WHERE `id`='{$id}' limit 1";
			if($DB->exec($sql)){
				Add_log('admin','删除微信免挂店员');
				$result=array("code"=>200,"msg"=>"删除微信免挂店员成功");
			}else{
				$result=array("code"=>-1,"msg"=>"删除微信免挂店员失败");
			}
		}
}elseif($act=='WesetStatus'){	//删除修改微信号
	$id=daddslashes(trim($_GET['id']));
	$status=is_numeric(daddslashes($_GET['status']))?intval(daddslashes($_GET['status'])):exit('{"code":200}');
	if($status==5){
		if($DB->exec("DELETE FROM `pay_qrlist` WHERE `id`='{$id}'"))
			exit('{"code":200}');
		else
			exit('{"code":400,"msg":"删除失败！['.$DB->error().']"}');
	}elseif($status==0){
		if($DB->exec("update `pay_qrlist` set `status`='{$status}',`cookie`=NULL,`endtime`='{$date}' where `id`='{$id}' limit 1")!==false)
			exit('{"code":200}');
		else
			exit('{"code":400,"msg":"修改失败！['.$DB->error().']"}');
	}elseif($status==1){
		if($DB->exec("update `pay_qrlist` set `status`='{$status}',`cookie`='Login_Yes',`endtime`='{$date}' where `id`='{$id}' limit 1")!==false)
			exit('{"code":200}');
		else
			exit('{"code":400,"msg":"修改失败！['.$DB->error().']"}');
	}else{
		if($DB->exec("update `pay_qrlist` set `status`='{$status}',`endtime`='{$date}' where `id`='{$id}' limit 1")!==false)
			exit('{"code":200}');
		else
			exit('{"code":400,"msg":"修改失败！['.$DB->error().']"}');
	}
}elseif($act=='User_status'){
    $pid = daddslashes($_POST['pid']);
    $is=$DB->query("SELECT * FROM `pay_user` WHERE `pid`='{$pid}' limit 1")->fetch();
    if($is['status']==1){
        if($DB->exec("update `pay_user` set `status`='0' where pid='{$pid}'")){
            $result = array('code'=>200,'msg'=>'商户封禁成功');
        }else{
            $result = array('code'=>-1,'msg'=>'商户封禁失败'.$DB->errorInfo()[2]);
        }
    }else{
        if($DB->exec("update `pay_user` set `status`='1' where pid='{$pid}'")){
            $result = array('code'=>200,'msg'=>'商户解除封禁成功');
        }else{
            $result = array('code'=>-1,'msg'=>'商户解除封禁失败'.$DB->errorInfo()[2]);
        }
    }
}elseif($act=='User_mali_status'){
    $pid = daddslashes($_POST['pid']);
    $is=$DB->query("SELECT * FROM `pay_user` WHERE `pid`='{$pid}' limit 1")->fetch();
    if($is['email_status']==1){
        if($DB->exec("update `pay_user` set `email_status`='0' where pid='{$pid}'")){
            $result = array('code'=>200,'msg'=>'邮箱解除状态成功');
        }else{
            $result = array('code'=>-1,'msg'=>'邮箱解除状态失败'.$DB->errorInfo()[2]);
        }
    }else{
        if($DB->exec("update `pay_user` set `email_status`='1' where pid='{$pid}'")){
            $result = array('code'=>200,'msg'=>'邮箱状态激活成功');
        }else{
            $result = array('code'=>-1,'msg'=>'邮箱状态激活失败'.$DB->errorInfo()[2]);
        }
    }
}elseif($act=='Add_user'){//添加商户
	$pid='1'.mt_rand(10000000,99999999);//商户PID,系统随机给出的		
	$key= daddslashes($_POST['key'])?daddslashes($_POST['key']):random(10);
    $qq= daddslashes($_POST['qq']);
    $user= daddslashes($_POST['user']);
    $pass= daddslashes($_POST['pass']);
    $money= daddslashes($_POST['money'])?daddslashes($_POST['money']):$conf['reg_money'];
    $type= daddslashes($_POST['type'])?daddslashes($_POST['type']):3;
    $emali = $qq.'@qq.com';
	$sds=$DB->exec("INSERT INTO `pay_user` (`pid`,`key`, `user`, `pass`, `money`, `email`, `qq`,`type`,`email_status`,`addtime`) VALUES ('{$pid}','{$key}','{$user}','{$pass}','{$money}','{$emali}','{$qq}','{$type}','1','{$date}')");
	if($sds){
		Add_log('admin',"添加用户成功,PID:".$pid);
		$result=array("code"=>200,"msg"=>"添加用户成功,PID:".$pid);
	}else{
		$result=array("code"=>-1,"msg"=>"添加用户失败".$DB->errorInfo()[2]);
	}
}elseif($act=='Edit_user'){	//修改商户
	$pid= daddslashes($_POST['pid']);
	$key= daddslashes($_POST['key']);
	$pass= daddslashes($_POST['pass']);
    $qq= daddslashes($_POST['qq']);
    $email= daddslashes($_POST['email']);
    $email_status= daddslashes($_POST['email_status'])?daddslashes($_POST['email_status']):1;
	$user_vip_time= daddslashes($_POST['user_vip_time'].date(" H:i:s"));
	$money= daddslashes($_POST['money']);
	$type= daddslashes($_POST['type']);
	$is=$DB->query("SELECT * FROM `pay_user` WHERE `pid`='{$pid}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"此记录不存在");
	}else{
		$sqs=$DB->exec("update `pay_user` set `key`='{$key}',`qq`='{$qq}',`money`='{$money}',`type`='{$type}',`email`='{$email}',`email_status`='{$email_status}' where pid='{$pid}'");
		if($_POST['user_vip_time'])$DB->exec("update `pay_user` set `user_vip_time`='{$user_vip_time}' where pid='{$pid}'");
		if($_POST['pass'])$DB->exec("update `pay_user` set `pass`='{$pass}' where pid='{$pid}'");

		if($sqs){
			Add_log('admin',"修改用户成功,PID:".$pid);
			$result=array("code"=>200,"msg"=>"修改用户成功");
		}else{
			$result=array("code"=>-1,"msg"=>"修改用户失败".$DB->errorInfo()[2]);
		}
	}
}elseif($act=='Del_user'){//删除商户
	$pid=daddslashes($_POST['pid']);
	$is=$DB->query("SELECT * FROM `pay_user` WHERE `pid`='{$pid}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"此用户记录不存在");
	}else{
		$sql="DELETE FROM `pay_user` WHERE `pid`='{$pid}' limit 1";
		if($DB->exec($sql)){
			$DB->exec("DELETE FROM `pay_qrlist` WHERE `pid`='{$pid}'");
			Add_log('admin',"删除用户成功,PID:".$pid);
			$result=array("code"=>200,"msg"=>"删除用户成功,旗下二维码也一并被清空");
		}else{
			$result=array("code"=>-1,"msg"=>"删除用户失败");
		}
	}
}elseif($act=='Del_Qr'){//删除二维码
	$id=daddslashes($_POST['id']);
	$is=$DB->query("SELECT * FROM `pay_qrlist` WHERE `id`='{$id}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"非法操作");
	}else{
		$sql="DELETE FROM `pay_qrlist` WHERE `id`='{$id}' limit 1";
		if($DB->exec($sql)){
			Add_log('admin',"删除二维码成功");
			$result=array("code"=>200,"msg"=>"删除成功");
		}else{
			$result=array("code"=>-1,"msg"=>"删除失败");
		}
	}
}elseif($act=='setStatus'){	//删除订单
	$trade_no=daddslashes($_GET['trade_no']);
	$status=is_numeric(daddslashes($_GET['status']))?intval(daddslashes($_GET['status'])):exit('{"code":200,"msg":"修改成功"}');
	if($status==5){
		Add_log('admin',"删除订单成功");
		if($DB->exec("DELETE FROM pay_order WHERE trade_no='{$trade_no}'"))
			exit('{"code":200,"msg":"删除订单成功"}');
		else
			exit('{"code":400,"msg":"删除订单失败！['.$DB->error().']"}');
	}else{
		if($DB->exec("update pay_order set status='{$status}' where trade_no='{$trade_no}'")!==false)
			exit('{"code":200}');
		else
			exit('{"code":400,"msg":"修改订单失败！['.$DB->error().']"}');
	}
}elseif($act=='notify'){	//重新通知
	$trade_no=daddslashes($_POST['trade_no']);
	$row=$DB->query("SELECT * FROM `pay_order` WHERE `trade_no`='{$trade_no}' limit 1")->fetch();
	if(!$row)
		exit('{"code":-1,"msg":"当前订单不存在！"}');
	$url=creat_callback($row);
	Add_log('admin',"人工总后台回调订单：".$trade_no);
	exit('{"code":200,"msg":"重新通知成功","url":"'.($_POST['isreturn']==1?$url['return']:$url['notify']).'"}');
}elseif($act=='operation'){	//批量操作订单
	$status=is_numeric(daddslashes($_POST['status']))?intval(daddslashes($_POST['status'])):exit('{"code":-1,"msg":"请选择操作"}');
	$checkbox=daddslashes($_POST['checkbox']);
	$i=0;
	foreach($checkbox as $trade_no){
		if($status==4)$DB->exec("DELETE FROM pay_order WHERE trade_no='{$trade_no}'");
		else $DB->exec("update pay_order set status='{$status}' where trade_no='{$trade_no}' limit 1");
		$i++;
	}
	exit('{"code":0,"msg":"成功改变'.$i.'条订单状态"}');
}elseif($act=='Set'){	//修改后台配置信息
    $_POST = daddslashes($_POST);
	foreach($_POST as $k=>$v){
		saveSetting($k, $v);
	}
	$ad=$CACHE->clear();
	Add_log('admin',"修改设置状态");
	if($ad)exit('{"code":0,"msg":"succ"}');
	else exit('{"code":-1,"msg":"修改设置失败"}');
}elseif($act=='setNotice'){	//修改公告状态
	$id=intval($_GET['id']);
	$status=intval($_GET['status']);
	Add_log('admin',"修改公告状态");
	$sql = "UPDATE pay_notice SET status='{$status}' WHERE id='{$id}'";
	if($DB->exec($sql))exit('{"code":200,"msg":"修改状态成功！"}');
	else exit('{"code":-1,"msg":"修改状态失败['.$DB->error().']"}');
}elseif($act=='Emali'){
    $type = daddslashes($_POST['type']);
    $email = daddslashes($_POST['email']);
    $min = daddslashes($_POST['min']);
    $name = daddslashes($_POST['name']);
    
    if ($type == 1) {
        $pagesize=30;
        $pages=intval($numrows/$pagesize);
        if ($numrows%$pagesize)
        {
            $pages++;
        }
        if (isset($_GET['page'])){
            $page=intval($_GET['page']);
        }else{
            $page=1;
        }
        $offset=$pagesize*($page - 1);
        $sql=" 1";
        $rs=$DB->query("SELECT * FROM pay_user WHERE{$sql}");
        while ($res = $rs->fetch()) {
            $result = send_mail($res['email'], $min, $name);
        }
    } else {
        $result = send_mail($_POST['email'], $min, $name);
    }
    if($result) {
      exit('{"code":1,"msg":"发送成功"}');
      Add_log('admin',"邮件发送");
    }else{
      exit('{"code":-1,"msg":"发送错误"}');
    }
}elseif($act=='User'){
    $column=daddslashes($_GET['column']);
    $value=daddslashes($_GET['value']);
    $result['code'] = 0;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    if($value) {
        $sql=" `{$column}`='{$value}'";
        $numrows=$DB->query("SELECT * from pay_user WHERE{$sql}")->rowCount();
        $con='包含 '.$value.' 的共有 <b>'.$numrows.'</b> 个用户';
    }else{
        $numrows=$DB->query("SELECT * from pay_user WHERE 1")->rowCount();
        $sql=" 1";
    }
    $result['count'] = $numrows;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize)
    {
        $pages++;
    }
    if (isset($_GET['page'])){
        $page=intval($_GET['page']);
    }else{
        $page=1;
    }
    $offset=$pagesize*($page - 1);
    $rs=$DB->query("SELECT * FROM pay_user WHERE{$sql} order by addtime desc limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $Order['pid']=$res['pid'];
        $Order['key']=$res['key'];
        $Order['user']=$res['user'];
        $Order['pass']=$res['pass'];
        $Order['money']=$res['money'];
        $Order['qq']=$res['qq'];
        $Order['type']=$res['type'];
        $Order['email']=$res['email'];
        $Order['status']=$res['status'];
        $Order['email_status']=$res['email_status'];
        $Order['addtime']=$res['addtime'];
        $Order['endtime']=$res['endtime'];
        $result['data'][] = $Order;
    }
}elseif($act=='Order'){
    $column=daddslashes($_GET['column']);
    $value=daddslashes($_GET['value']);
    $sqls="";
    $result['code'] = 0;
    if(isset($_GET['pid']) && !empty($_GET['pid'])) {
        $pid = intval($_GET['pid']);
        $sqls.=" AND `pid`='$pid'";
    }
    
    if(isset($_GET['dstatus']) && $_GET['dstatus']>0) {
        $dstatus = intval($_GET['dstatus']);
        if($dstatus==2){
            $dstatus = 0;
            }
        $sqls.=" AND status={$dstatus}";
    }
    if(daddslashes($_GET['type'])){
        $sqls.=" AND type='".daddslashes($_GET['type'])."'";
    }
    if(!empty($value)) {
        if($column=='name'){
            $sql=" `{$column}` like '%{$value}%'";
        }else{
            $sql=" `{$column}`='{$value}'";
        }
        $sql.=$sqls;
        $numrows=$DB->getColumn("SELECT count(*) from pre_order WHERE {$sql}");
        
    }else{
        $numrows=$DB->getColumn("SELECT count(*) from pre_order WHERE 1");
        $sql=" 1";
    }
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=ceil($numrows/$pagesize);
    $page=isset($_GET['page'])?intval($_GET['page']):1;
    $offset=$pagesize*($page - 1);
    
    $rs=$DB->query("SELECT * FROM pre_order WHERE{$sql} order by addtime desc limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $Order['trade_no']=$res['trade_no'];
        $Order['out_trade_no']=$res['out_trade_no'];
        $Order['name']=$res['name'];
        $Order['pid']=$res['pid'];
        $Order['price']=$res['price'];
        $Order['money']=$res['money'];
        $Order['type']=$res['type'];
        $Order['status']=$res['status'];
        $Order['addtime']=$res['addtime'];
        $Order['endtime']=$res['endtime'];
        $Order['url']=getdomain($res['notify_url']);
        $result['data'][] = $Order;
    }
    
}elseif($act=='Qrlist'){
    $column = daddslashes($_GET['column']);
    $value = daddslashes($_GET['value']);
    $result['code'] = 0;
    if($value) {
        $sql=" `{$column}`='{$value}'";
        $numrows=$DB->query("SELECT * from pay_qrlist WHERE{$sql}")->rowCount();
    }else{
        $numrows=$DB->query("SELECT * from pay_qrlist WHERE 1")->rowCount();
        $sql=" 1";
    }
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize)
    {
        $pages++;
        
    }
    if (isset($_GET['page'])){
        $page=intval($_GET['page']);
    }else{
        $page=1;
    }
    $offset=$pagesize*($page - 1);
    $today=date("Y-m-d");
    $lastday=date("Y-m-d",strtotime("-1 day"));
    $rs=$DB->query("SELECT * FROM pay_qrlist WHERE{$sql} order by addtime desc limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $qr_id=$res['id'];
        $jrzddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and date='{$today}'")->fetchColumn();
        $jrzcgddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status='1' and date='{$today}'")->fetchColumn();
        $zrzddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and date='{$lastday}'")->fetchColumn();
        $zrzcgddsl=$DB->query("SELECT count(*) from pay_order where qr_id='{$qr_id}' and status='1' and date='{$lastday}'")->fetchColumn();
        $Order['id']=$res['id'];
        $Order['pid']=$res['pid'];
        $Order['type']=$res['type'];
        $Order['money']=WxMoney($res);
        
        $Order['beizhu']=htmlspecialchars($res['beizhu']);
        $Order['jrzpfcgje']=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status='1' and date='{$today}'")->fetchColumn();//今日成功金额
        if(!$Order['jrzpfcgje']){
            $Order['jrzpfcgje']=0.00;
        }
        $Order['jrzkl'] = (round((($jrzcgddsl?$jrzcgddsl:1) / ($jrzddsl?$jrzddsl:1)),2)*100).'%';
        
        $Order['zrzpfcgje']=$DB->query("SELECT sum(money) from pay_order where qr_id='{$qr_id}' and status='1' and date='{$lastday}'")->fetchColumn();//今日成功金额
        if(!$Order['zrzpfcgje']){
            $Order['zrzpfcgje']=0.00;
        }
        $Order['zrzkl'] = (round((($zrzcgddsl?$zrzcgddsl:1) / ($zrzddsl?$zrzddsl:1)),2)*100).'%';
        $Order['zpfcgje']=$DB->query("SELECT sum(price) from pay_order where qr_id='{$qr_id}' and status='1'")->fetchColumn();//总成功金额
        if(!$Order['zpfcgje']){
            $Order['zpfcgje']=0.00;
        }
        $data = cookie_zt($res);
        if($data['status']){
            $time = jstime($res['addtime'],3);
        }else{
            $time="<font color=red>无在线时长</font>";
        }
        $Order['pay_type'] = pay_type($res);
        $Order['addtime']=$res['addtime'];
        $Order['time'] = $time;
        $Order['status']=$data['msg'];
        $result['data'][] = $Order;
    }
}elseif($act=='Wechat_Trumpet'){
    $column=daddslashes($_GET['column']);
    $value=daddslashes($_GET['value']);
    $sqls="";
    $result['code'] = 0;
    if(!empty($value)) {
        if($column=='name'){
            $sql=" `{$column}` like '%{$value}%'";
        }else{
            $sql=" `{$column}`='{$value}'";
        }
        $sql.=$sqls;
    }else{
        $sql=" 1";
        $sql.=$sqls;
    }
    if($my=='search'||$_GET['value']) {
        $column = daddslashes($_GET['column']);
        $value = daddslashes($_GET['value']);
        $sql=" `{$column}`='{$value}'";
        $numrows=$DB->query("SELECT * from pay_wechat_trumpet WHERE{$sql}")->rowCount();
    }else{
        $numrows=$DB->query("SELECT * from pay_wechat_trumpet WHERE 1")->rowCount();
        $sql=" 1";
    }
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=ceil($numrows/$pagesize);
    $page=isset($_GET['page'])?intval($_GET['page']):1;
    $offset=$pagesize*($page - 1);
    $rs=$DB->query("SELECT * FROM pay_wechat_trumpet WHERE{$sql} order by sort desc limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $Order['id']=$res['id'];
        $Order['wx_user']=$res['wx_user'];
        $Order['wx_name']=$res['wx_name'];
        $Order['beizhu']=htmlspecialchars($res['beizhu']);
        $Order['addtime']=$res['addtime'];
        $Order['hook_type']=$res['hook_type'];
        $Order['login_time'] = wachat_login_zt($res['login_time']);
        $Order['status'] = wachat_zt($res['status']);
        $result['data'][] = $Order;
    }
}elseif($act=='Notice'){
    $result['code'] = 0;
    $numrows=$DB->query("SELECT * from pay_notice WHERE 1")->rowCount();
    $result['count'] = $numrows;
    $list=$DB->query("SELECT * FROM pay_notice WHERE 1 order by sort ASC");
    foreach($list as $row){
        $Order['id'] = $row['id'];
        $Order['title'] = $row['title'];
        $Order['color']  = $row['color'];
        $Order['datatxt']  = $row['datatxt'];
        $Order['html'] = '<em class="fa fa-fw fa-volume-up"></em><font color="'.$row['color'].'">'.$row['datatxt'].'</font>';
        $Order['status'] = $row['status'];
        $Order['addtime'] = $row['addtime'];
        $result['data'][] = $Order;
    }
}elseif($act=='template'){
    $result['code'] = 0;
    $mblist = \lib\Template::getList();
    foreach($mblist as $template){
        $table['title'] = $template;
        $result['data'][] = $table;
    }
}elseif($act=='pay_channel_config'){
    $group = daddslashes($_GET['group']);
    if($group!='channels')$group='types';
    $type = isset($_GET['type']) ? daddslashes($_GET['type']) : '';
    $config = mym_pay_channel_config();
    $result['code'] = 0;
    $result['data'] = array();
    foreach($config[$group] as $code=>$item){
        if($group=='channels' && $type!='' && $item['type']!=$type)continue;
        $row = array();
        $row['code'] = $code;
        $row['name'] = htmlspecialchars($item['name']);
        $row['status'] = intval($item['status']);
        $row['sort'] = intval($item['sort']);
        if($group=='channels'){
            $row['type'] = $item['type'];
            $row['type_name'] = isset($config['types'][$item['type']]) ? htmlspecialchars($config['types'][$item['type']]['name']) : $item['type'];
        }
        $result['data'][] = $row;
    }
    $result['count'] = count($result['data']);
}elseif($act=='save_pay_channel_config'){
    $group = daddslashes($_POST['group']);
    $code = strtolower(trim($_POST['code']));
    $is_new = isset($_POST['is_new']) ? intval($_POST['is_new']) : 0;
    if($group!='types' && $group!='channels')exit('{"code":-1,"msg":"参数错误"}');
    if(!preg_match('/^[a-z0-9_]{2,32}$/', $code))exit('{"code":-1,"msg":"代码格式错误，只能填写 2-32 位小写字母、数字、下划线"}');
    $config = mym_pay_channel_config();
    if($is_new){
        if(isset($config[$group][$code]))exit('{"code":-1,"msg":"代码已存在，请换一个"}');
        $name = trim($_POST['name']);
        if($name==='')exit('{"code":-1,"msg":"名称不能为空"}');
        $sort = isset($_POST['sort']) ? intval($_POST['sort']) : 999;
        $status = isset($_POST['status']) ? (intval($_POST['status']) ? 1 : 0) : 1;
        if($group=='channels'){
            $type = strtolower(trim($_POST['type']));
            if(!isset($config['types'][$type]))exit('{"code":-1,"msg":"所属支付方式不存在"}');
            $config[$group][$code] = array('type'=>$type, 'name'=>strip_tags($name), 'sort'=>$sort, 'status'=>$status);
        }else{
            $config[$group][$code] = array('name'=>strip_tags($name), 'sort'=>$sort, 'status'=>$status);
        }
    }else{
        if(!isset($config[$group][$code]))exit('{"code":-1,"msg":"通道不存在"}');
        if(isset($_POST['type']) && $group=='channels'){
            $type = strtolower(trim($_POST['type']));
            if(!isset($config['types'][$type]))exit('{"code":-1,"msg":"所属支付方式不存在"}');
            $config[$group][$code]['type'] = $type;
        }
        if(isset($_POST['name'])){
            $name = trim($_POST['name']);
            if($name==='')exit('{"code":-1,"msg":"名称不能为空"}');
            $config[$group][$code]['name'] = strip_tags($name);
        }
        if(isset($_POST['sort'])){
            $config[$group][$code]['sort'] = intval($_POST['sort']);
        }
        if(isset($_POST['status'])){
            $config[$group][$code]['status'] = intval($_POST['status']) ? 1 : 0;
        }
    }
    if(mym_save_pay_channel_config($config)){
        Add_log('admin','修改通道展示配置: '.$code);
        $result=array('code'=>200,'msg'=>'保存成功');
    }else{
        $result=array('code'=>-1,'msg'=>'保存失败'.$DB->errorInfo()[2]);
    }
}elseif($act=='dll'){
    $result['code'] = 0;
    $numrows=$DB->query("SELECT * from pay_plug WHERE 1")->rowCount();
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize)
    {
        $pages++;
    }
    if (isset($_GET['page'])){
        $page=intval($_GET['page']);
    }else{
        $page=1;
    }
    $offset=$pagesize*($page - 1);
    $rs=$DB->query("SELECT * FROM pay_plug WHERE 1 order by id ASC limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $table['id'] = $res['id'];
        $table['type'] = $res['type'];
        $table['name'] = $res['name'];
        $table['logimg'] = $res['logimg'];
        $table['title'] = $res['title'];
        $table['author'] = $res['author'];
        $table['download'] = $res['download'];
        $table['time'] = $res['time'];
        $result['data'][] = $table;
    }
}elseif($act=='Add_dll'){
    $name = daddslashes($_POST['name']);
    $type = daddslashes($_POST['type']);
    $url = daddslashes($_POST['download']);
    $text = daddslashes($_POST['title']);
    $sds=$DB->exec("INSERT INTO `pay_plug` (`name`,`type`,`title`,`download`,`time`) VALUES ('{$name}','{$type}','{$text}','{$url}','{$date}')");
	if($sds){
		Add_log('admin',"添加插件成功".$ip);
		$result=array("code"=>200,"msg"=>"添加插件成功");
	}else{
		$result=array("code"=>-1,"msg"=>"添加插件失败".$DB->errorInfo()[2]);
	}
}elseif($act=='Edit_dll'){
    $id = intval($_POST['id']);
    $name = daddslashes($_POST['name']);
    $type = daddslashes($_POST['type']);
    $url = daddslashes($_POST['download']);
    $text = daddslashes($_POST['title']);
    $is=$DB->query("SELECT * FROM `pay_plug` WHERE `id`='{$id}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"此用户记录不存在");
	}else{
		$sqs=$DB->exec("update `pay_plug` set `name`='{$name}',`type`='{$type}',`title`='{$text}',`download`='{$url}' where id='{$id}'");
		if($sqs){
			Add_log('admin',"修改插件成功,ip:".$ip);
			$result=array("code"=>200,"msg"=>"修改插件成功");
		}else{
			$result=array("code"=>-1,"msg"=>"修改插件失败".$DB->errorInfo()[2]);
		}
	}
}elseif($act=='Del_dll'){//删除插件
	$id=daddslashes($_POST['id']);
	$is=$DB->query("SELECT * FROM `pay_plug` WHERE `id`='{$id}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"非法操作");
	}else{
		$sql="DELETE FROM `pay_plug` WHERE `id`='{$id}' limit 1";
		if($DB->exec($sql)){
			Add_log('admin',"删除插件成功");
			$result=array("code"=>200,"msg"=>"删除成功");
		}else{
			$result=array("code"=>-1,"msg"=>"删除失败");
		}
	}
}elseif($act=='taocan'){
    $result['code'] = 0;
    $numrows=$DB->query("SELECT * from pay_taocan WHERE 1")->rowCount();
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize)
    {
        $pages++;
    }
    if (isset($_GET['page'])){
        $page=intval($_GET['page']);
    }else{
        $page=1;
    }
    $offset=$pagesize*($page - 1);
    $rs=$DB->query("SELECT * FROM pay_taocan WHERE 1 order by sort ASC limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $table['id'] = $res['id'];
        $table['time'] = $res['time'];
        $table['status'] = $res['status'];
        $table['name'] = $res['name'];
        $table['money'] = $res['money'];
        $table['sort'] = $res['sort'];
        $table['edu'] = $res['edu'];
        $result['data'][] = $table;
    }
}elseif($act=='Add_taocan'){//添加套餐
    $name = daddslashes($_POST['name']);
    $money = daddslashes($_POST['money']);
    $edu = daddslashes($_POST['edu']);
    $time = daddslashes($_POST['time']);
    $sort = daddslashes($_POST['sort']);
    $sds=$DB->exec("INSERT INTO `pay_taocan` (`name`,`edu`,`money`,`time`,`sort`,`status`,`addtime`) VALUES ('{$name}','{$edu}','{$money}','{$time}','{$sort}','1','{$date}')");
    if($sds){
        Add_log('admin',"添加额度套餐成功！");
        $result=array("code"=>200,"msg"=>"添加额度套餐成功！");
    }else{
        $result=array("code"=>-1,"msg"=>"添加额度套餐失败".$DB->errorInfo()[2]);
    }
}elseif($act=='Edit_taocan'){	//修改套餐
    $id = daddslashes($_POST['id']);
    $name = daddslashes($_POST['name']);
    $edu = daddslashes($_POST['edu']);
    $money = daddslashes($_POST['money']);
    $time = daddslashes($_POST['time']);
    $sort = daddslashes($_POST['sort']);
    $is=$DB->query("SELECT * FROM `pay_taocan` WHERE `id`='{$id}' limit 1")->fetch();
    if(!$is){
        $result=array("code"=>-2,"msg"=>"此额度套餐记录不存在");
    }else{
        $sqs=$DB->exec("update `pay_taocan` set `name`='{$name}',`edu`='{$edu}',`money`='{$money}',`time`='{$time}',`sort`='{$sort}' where id='{$id}'");
        if($sqs){
            Add_log('admin',"修改额度套餐成功,ID:".$id);
            $result=array("code"=>200,"msg"=>"修改额度套餐成功");
        }else{
            $result=array("code"=>-1,"msg"=>"修改额度套餐失败".$DB->errorInfo()[2]);
        }
    }
}elseif($act=='Del_taocan'){//删除套餐
    $id=daddslashes($_POST['id']);
    $is=$DB->query("SELECT * FROM `pay_package` WHERE `id`='{$id}' limit 1")->fetch();
    if(!$is){
        $result=array("code"=>-2,"msg"=>"此套餐记录不存在");
    }else{
        $sql="DELETE FROM `pay_package` WHERE `id`='{$id}' limit 1";
        if($DB->exec($sql)){
            Add_log('admin',"删除套餐成功,ID:".$id);
            $result=array("code"=>1,"msg"=>"删除套餐成功！");
        }else{
            $result=array("code"=>-1,"msg"=>"删除用户失败".$DB->errorInfo()[2]);
        }
    }
}elseif($act=='taocan_status'){
    $id = daddslashes($_POST['id']);
    $is=$DB->query("SELECT * FROM `pay_taocan` WHERE `id`='{$id}' limit 1")->fetch();
    if($is['status']==1){
        if($DB->exec("update `pay_taocan` set `status`='0' where id='{$id}'")){
            $result = array('code'=>200,'msg'=>'下架成功');
        }else{
            $result = array('code'=>-1,'msg'=>'下架失败'.$DB->errorInfo()[2]);
        }
    }else{
        if($DB->exec("update `pay_taocan` set `status`='1' where id='{$id}'")){
            $result = array('code'=>200,'msg'=>'上架成功');
        }else{
            $result = array('code'=>-1,'msg'=>'上架失败'.$DB->errorInfo()[2]);
        }
    }
}elseif($act=='daili'){
    $result['code'] = 0;
    $numrows=$DB->query("SELECT * from pay_daili WHERE 1")->rowCount();
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize)
    {
        $pages++;
    }
    if (isset($_GET['page'])){
        $page=intval($_GET['page']);
    }else{
        $page=1;
    }
    $offset=$pagesize*($page - 1);
    $rs=$DB->query("SELECT * FROM pay_daili WHERE 1 order by addtime ASC limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $table['id'] = $res['id'];
        $table['name'] = $res['name'];
        $table['ip'] = $res['ip'];
        $table['do'] = $res['do'];
        $table['user'] = $res['user'];
        $table['pass'] = $res['pass'];
        $table['addtime'] = $res['addtime'];
        $result['data'][] = $table;
    }
}elseif($act=='Add_daili'){//添加代理
    $name = daddslashes($_POST['name']);
    $ip = daddslashes($_POST['ip']);
    $do = daddslashes($_POST['do']);
    $user = daddslashes($_POST['user']);
    $pass = daddslashes($_POST['pass']);
    $sds=$DB->exec("INSERT INTO `pay_daili` (`name`, `ip`, `do`, `user`, `pass`, `addtime`) VALUES ('{$name}', '{$ip}', '{$do}', '{$user}', '{$pass}', '{$date}');");
    if($sds){
        Add_log('admin',"添加代理成功！");
        $result=array("code"=>200,"msg"=>"添加代理成功！");
    }else{
        $result=array("code"=>-1,"msg"=>"添加代理失败".$DB->errorInfo()[2]);
    }
}elseif($act=='Notify'){
    $column = daddslashes($_GET['column']);
    $value = daddslashes($_GET['value']);
    if($_GET['my']=='search'||$value) {
        $sql=" `{$column}`='{$value}'";
        $numrows=$DB->query("SELECT * from pay_notify WHERE{$sql}")->rowCount();
        $con='包含 '.$value.' 的共有 <b>'.$numrows.'</b> 个二维码';
    }else{
        $numrows=$DB->query("SELECT * from pay_notify")->rowCount();
        $nums=$DB->query("SELECT * from pay_notify WHERE pay_msg='success'")->rowCount();
        $sql=" 1";
        $con='系统共有 <b>'.$numrows.'</b> 个收款记录';
    }
    $result['code'] = 0;
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize)
    {
        $pages++;
    }
    if (isset($_GET['page'])){
        $page=intval($_GET['page']);
    }else{
        $page=1;
    }
    $offset=$pagesize*($page - 1);

    $rs=$DB->query("SELECT * FROM pay_notify WHERE{$sql} order by addtime desc limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $pack=$DB->query("SELECT * FROM `pay_order` WHERE `trade_no`='{$res['trade_no']}' limit 1")->fetch();
        $ms = strtotime($res['addtime']) - strtotime($pack['addtime']);
        $table['trade_no'] = $res['trade_no'];
        $table['pid'] = $res['pid'];
        $table['type'] = $res['type'];
        $table['money'] = $res['money'];
        $table['ms'] = $ms;
        $table['pay_msg'] = $res['pay_msg'];
        $table['addtime'] = $res['addtime'];
        $result['data'][] = $table;
    }
}elseif($act=='Log'){
    $column = daddslashes($_GET['column']);
    $value = daddslashes($_GET['value']);
    if($value) {
        $sql=" `{$column}`='{$value}'";
        $numrows=$DB->query("SELECT * from pay_log WHERE{$sql}")->rowCount();
        $con='包含 '.$value.' 的共有 <b>'.$numrows.'</b> 条记录';
        $link='&my=search&column='.$column.'&value='.$value;
    }else{
        $numrows=$DB->query("SELECT * from pay_log WHERE 1")->rowCount();
        $sql=" 1";
        $con='共有 <b>'.$numrows.'</b> 条记录';
    }
    $result['code'] = 0;
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=ceil($numrows/$pagesize);
    $page=isset($_GET['page'])?intval($_GET['page']):1;
    $offset=$pagesize*($page - 1);
    $rs=$DB->query("SELECT * FROM pay_log WHERE{$sql} order by id desc limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $table['id'] = $res['id'];
        $table['pid'] = ($res['pid']>0?'<a href="./User.php?my=search&column=pid&value='.$res['pid'].'" target="_blank">'.$res['pid'].'</a>':'管理员');
        $table['type'] = $res['type'];
        $table['ip'] = $res['ip'];
        $table['date'] = $res['date'];
        $result['data'][] = $table;
    }
}elseif($act=='gocode'){
    $code = $_POST['code'];
    $goid = $_SESSION['goid'];
    $ga = new \lib\GoogleAuthenticator();
    $checkResult = $ga->verifyCode($goid, $code, 1);
    if($checkResult){
        saveSetting('goid', $goid);
        $ad=$CACHE->clear();
        Add_log('admin',"修改设置状态");
        if($ad)exit('{"code":200,"msg":"succ"}');
        else exit('{"code":-1,"msg":"修改设置失败"}');
    }else{
        $result = ['code'=>-1,'msg'=>'验证码错误或失效！'];
    }
}elseif($act=='mailtest'){
	$mail_name = $conf['mail_recv']?$conf['mail_recv']:$conf['mail_name'];
	if(!empty($mail_name)){
	$result=send_mail($mail_name,'邮件发送测试。','这是一封测试邮件！<br/><br/>来自：'.$siteurl);
	if($result==1)
		$result=array("code"=>200,"msg"=>"邮件发送成功");
	else
	    $result=array("code"=>-1,"msg"=>'邮件发送失败！'.$result);
	}
	else
		$result=array("code"=>-2,"msg"=>'您还未设置邮箱！');
}elseif($act=='account'){
    $user = daddslashes($_POST['user']);
	$oldpwd=daddslashes($_POST['oldpwd']);
	$newpwd=daddslashes($_POST['newpwd']);
	$newpwd2=daddslashes($_POST['newpwd2']);
	if(!empty($newpwd) && !empty($newpwd2)){
		if(md5($oldpwd)!=$conf['admin_pass']){
		    exit('{"code":"-1","msg":"旧密码不正确！"}');
		}
		if($newpwd!=$newpwd2){
		    exit('{"code":"-2","msg":"两次输入的密码不一致！"}');
		}
		saveSetting('admin_pass',md5($newpwd));
		if($user){
		    saveSetting('admin_user',md5($user));
		}
		Add_log('admin',"修改后台密码");
	}
	$ad=$CACHE->clear();
	if($ad){
	    $result=array("code"=>200,"msg"=>"修改成功！请重新登录");
	}else{
	    $result=array("code"=>-1,"msg"=>'修改失败！'.$DB->error());
	}
}elseif($act=='Add_Notice'){
    $title=daddslashes($_POST['title']);
    $datatxt=daddslashes($_POST['datatxt']);
    $sort=intval($_POST['sort']);
    $color=daddslashes(trim($_POST['color']));
    if(!$title || !$datatxt || !$sort){
        exit('{"code":"-1","msg":"公告标题、内容不能为空"}');
    } else {
        $sds=$DB->exec("INSERT INTO `pay_notice` (`title`, `datatxt`, `color`, `sort`, `addtime`, `status`) VALUES ('{$title}','{$datatxt}', '{$color}', '{$sort}', '{$date}', 1)");
        if($sds){
            $result=array("code"=>200,"msg"=>"添加公告成功");
        }else{
            $result=array("code"=>-1,"msg"=>'添加公告失败！'.$DB->error());
        }
    }
}elseif($act=='Edit_Notice'){
    $id=intval($_POST['id']);
    $rows=$DB->query("SELECT * FROM `pay_notice` WHERE `id`='{$id}' limit 1")->fetch();
    if(!$rows)exit('{"code":"-1","msg":"当前公告不存在"}');
    $title=daddslashes($_POST['title']);
    $datatxt=daddslashes($_POST['datatxt']);
    $sort=intval($_POST['sort']);
    $color=daddslashes(trim($_POST['color']));
    if(!$title || !$datatxt || !$sort){
        exit('{"code":"-1","msg":"公告标题、内容不能为空"}');
    } else {
        $sds=$DB->exec("UPDATE `pay_notice` SET `title`='{$title}',`datatxt`='{$datatxt}',`sort`='{$sort}',`color`='{$color}' WHERE `id`='{$id}'");
        if($sds){
            $result=array("code"=>200,"msg"=>"修改公告成功！");
        }else{
            $result=array("code"=>-1,"msg"=>'修改公告失败！'.$DB->error());
        }
    }
}elseif($act=='Del_Notice'){
    $id=intval($_POST['id']);
	$sql = "DELETE FROM pay_notice WHERE id='{$id}'";
	if($DB->exec($sql))exit('{"code":200,"msg":"删除公告成功！"}');
	else exit('{"code":-1,"msg":"删除公告失败['.$DB->error().']"}');

}elseif($act=='yund'){
    $result['code'] = 0;
    $numrows=$DB->query("SELECT * from pay_daili WHERE 1")->rowCount();
    $result['count'] = $numrows;
    $pagesize=intval($_GET['limit'])?intval($_GET['limit']):15;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize)
    {
        $pages++;
    }
    if (isset($_GET['page'])){
        $page=intval($_GET['page']);
    }else{
        $page=1;
    }
    $offset=$pagesize*($page - 1);
    $rs=$DB->query("SELECT * FROM pay_yund WHERE 1 order by addtime ASC limit $offset,$pagesize");
    while($res = $rs->fetch())
    {
        $table['id'] = $res['id'];
        $table['name'] = $res['name'];
        $table['type'] = $res['type'];
        $table['url'] = $res['url'];
        $table['status'] = $res['status'];
        $table['addtime'] = $res['addtime'];
        $result['data'][] = $table;
    }
}elseif($act=='Add_yund'){//添加云端
    $name = daddslashes(trim($_POST['name']));
    $type = daddslashes($_POST['type']);
    $url = daddslashes(trim($_POST['url']));
    if($type==='')exit('{"code":"-1","msg":"请选择云端类型"}');
    if(!$name || !$url)exit('{"code":"-1","msg":"云端地区和云端地址不能为空"}');
    if(!preg_match('/^https?:\/\//i',$url))exit('{"code":"-1","msg":"云端地址必须以 http:// 或 https:// 开头"}');
    $url = rtrim($url,'/').'/';
    $sds=$DB->exec("INSERT INTO `pay_yund` (`name`, `type`, `url`, `addtime`) VALUES ('{$name}', '{$type}', '{$url}', '{$date}');");
    if($sds){
        Add_log('admin',"添加云端成功！");
        $result=array("code"=>200,"msg"=>"添加云端成功！");
    }else{
        $result=array("code"=>-1,"msg"=>"添加云端失败".$DB->errorInfo()[2]);
    }
}elseif($act=='Edit_yund'){
    $id = daddslashes($_POST['id']);
    $type = daddslashes($_POST['type']);
    $url = daddslashes(trim($_POST['url']));
    $name = daddslashes(trim($_POST['name']));
    if(!$id || $type==='' || !$name || !$url){
        exit('{"code":"-1","msg":"内容不能为空"}');
    }elseif(!preg_match('/^https?:\/\//i',$url)){
        exit('{"code":"-1","msg":"云端地址必须以 http:// 或 https:// 开头"}');
    } else {
        $url = rtrim($url,'/').'/';
        $sds=$DB->exec("UPDATE `pay_yund` SET `type`='{$type}',`url`='{$url}',`name`='{$name}' WHERE `id`='{$id}'");
        if($sds!==false){
            $result=array("code"=>200,"msg"=>"修改云端成功！");
        }else{
            $result=array("code"=>-1,"msg"=>'修改云端失败！'.$DB->error());
        }
    }
}elseif($act=='Del_yund'){//删除云端
	$id=daddslashes($_POST['id']);
	$is=$DB->query("SELECT * FROM `pay_yund` WHERE `id`='{$id}' limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"非法操作");
	}else{
		$sql="DELETE FROM `pay_yund` WHERE `id`='{$id}' limit 1";
		if($DB->exec($sql)){
			Add_log('admin',"删除云端成功");
			$result=array("code"=>200,"msg"=>"删除成功");
		}else{
			$result=array("code"=>-1,"msg"=>"删除失败");
		}
	}
}elseif($act=='yund_status'){
    $id = daddslashes($_POST['id']);
    $is=$DB->query("SELECT * FROM `pay_yund` WHERE `id`='{$id}' limit 1")->fetch();
    if($is['status']==1){
        if($DB->exec("update `pay_yund` set `status`='0' where id='{$id}'")){
            $result = array('code'=>200,'msg'=>'成功停用');
        }else{
            $result = array('code'=>-1,'msg'=>'停用失败'.$DB->errorInfo()[2]);
        }
    }else{
        if($DB->exec("update `pay_yund` set `status`='1' where id='{$id}'")){
            $result = array('code'=>200,'msg'=>'启用成功');
        }else{
            $result = array('code'=>-1,'msg'=>'启用失败'.$DB->errorInfo()[2]);
        }
    }
}else{
	$result=array("code"=>-9,"msg"=>"参数错误");
}

if($result)
	exit(json_encode($result));
else
	exit($data);
?>