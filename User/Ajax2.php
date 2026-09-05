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
@header('Content-Type: application/json; charset=UTF-8');
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;
if($islogin_user==1 or $act=='getqrpic' or $act=='qrlogin' or $act=='findpwd'){}else exit(json_encode(array("code"=>-5,"msg"=>"未登录")));

if($act=='groupinfo'){
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
    $id=daddslashes($_POST['id'])?daddslashes($_POST['id']):daddslashes($_GET['id']);
    $pack=$DB->query("SELECT * FROM `pay_vip` WHERE `id`='{$id}' limit 1")->fetch();
    if(!$pack){
        exit('{"code":-1,"msg":"此套餐不存在"}');
    }else{
        $name = $type_name.'|'.$pack['name'];
        $name = $type_name.'|'.$pack['name'];
        $time = $pack['time']*2626560+time();
        $time = date("Y-m-d",$time);
    }
    $trade_no=date("YmdHis").rand(11111,99999);
    $result=array("code"=>0,"msg"=>"成功",'price'=>$pack['money'],"id"=>$pack['id'],"name"=>$pack['name'],"time"=>$time);
}elseif($act=='taocan'){
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
    $id=daddslashes($_POST['id']);
    $type=daddslashes($_POST['type']);
    $pack=$DB->query("SELECT * FROM `pay_taocan` WHERE `id`='{$id}' limit 1")->fetch();
    if(!$pack){
        exit('{"code":-1,"msg":"此额度套餐不存在"}');
    }else{
        $type_name = '购买额度套餐';
    }
    $trade_no=date("YmdHis").rand(11111,99999);
    $result=array("code"=>0,"msg"=>"创建支付订单成功","trade_no"=>$trade_no,"money"=>$pack['money'],"id"=>$pack['id'],"type_name"=>$type_name,"name"=>$pack['name'],"url"=>'SDK/epayapi.php?type='.$type.'&WIDout_trade_no='.$trade_no.'&WIDtotal_fee='.$pack['money'].'&WIDsubject='.$userrow['pid'].'购买额度套餐|'.$pack['name'].'|'.$pack['id']);
}elseif($act=='Pay_set'){//支付设置
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'有问题请访问官网g.9o3.cn购买程序！')));
	$outtime=intval($_POST['outtime'])?intval($_POST['outtime']):0;
	$alipay_pay_open=intval($_POST['alipay_pay_open']);
	$alipay_api_url=daddslashes(htmlspecialchars($_POST['alipay_api_url']));
	$alipay_api_pid=daddslashes(htmlspecialchars($_POST['alipay_api_pid']));
	$alipay_api_key=daddslashes(htmlspecialchars($_POST['alipay_api_key']));
	$qqpay_pay_open=intval($_POST['qqpay_pay_open']);
	$qqpay_api_url=daddslashes(htmlspecialchars($_POST['qqpay_api_url']));
	$qqpay_api_pid=daddslashes(htmlspecialchars($_POST['qqpay_api_pid']));
	$qqpay_api_key=daddslashes(htmlspecialchars($_POST['qqpay_api_key']));
	$wxpay_pay_open=intval($_POST['wxpay_pay_open']);
	$wxpay_api_url=daddslashes(htmlspecialchars($_POST['wxpay_api_url']));
	$wxpay_api_pid=daddslashes(htmlspecialchars($_POST['wxpay_api_pid']));
	$wxpay_api_key=daddslashes(htmlspecialchars($_POST['wxpay_api_key']));
	$Order_Money = daddslashes(htmlspecialchars($_POST['Order_Money']));
	$pay_template = daddslashes(htmlentities($_POST['pay_template']));
	$money_mail=intval($_POST['moneymail']);
	$free = intval($_POST['free']);
	$mali = intval($_POST['mali']);
	$music = intval($_POST['music']);
	$pay_tzqq = intval($_POST['pay_tzqq']);
	$pay_tzwx = intval($_POST['pay_tzwx']);
	$pay_tzali = intval($_POST['pay_tzali']);
	
	$is=$DB->query("SELECT * FROM `pay_user` WHERE `pid`='{$userrow['pid']}'limit 1")->fetch();
	if(!$is){
		$result=array("code"=>-2,"msg"=>"非法操作");
	}elseif($outtime>1200){
		$result=array("code"=>-2,"msg"=>"修改失败,支付超时时间最大不能超过1200秒");
	}else{
 		$sqs=$DB->exec("update `pay_user` set `outtime`='{$outtime}',`alipay_pay_open`='{$alipay_pay_open}',`alipay_api_url`='{$alipay_api_url}',`alipay_api_pid`='{$alipay_api_pid}',`alipay_api_key`='{$alipay_api_key}',`qqpay_pay_open`='{$qqpay_pay_open}',`qqpay_api_url`='{$qqpay_api_url}',`qqpay_api_pid`='{$qqpay_api_pid}',`qqpay_api_key`='{$qqpay_api_key}',`wxpay_pay_open`='{$wxpay_pay_open}',`wxpay_api_url`='{$wxpay_api_url}',`wxpay_api_pid`='{$wxpay_api_pid}',`wxpay_api_key`='{$wxpay_api_key}',`money_mail`='{$money_mail}',`mali`='{$mali}',`free`='{$free}',`Order_Money`='{$Order_Money}',`music`='{$music}',`pay_template`='{$pay_template}',`pay_tzali`='{$pay_tzali}',`pay_tzqq`='{$pay_tzqq}',`pay_tzwx`='{$pay_tzwx}' where pid='{$userrow['pid']}'");
		if($sqs){
			Add_log($userrow['pid'],'修改商户支付配置信息');
			$result=array("code"=>1,"msg"=>"修改成功");
		}else{
			$result=array("code"=>-1,"msg"=>"修改失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
		} 
	}
}elseif($act=='getqrpic'){
    $result = Login_Qr();
    $result =array("saveOK"=>0,"qrsig"=>$result['qrsig'],"data"=>$result['data'],"url"=>$result['qrcode']);
}elseif($act=='qrlogin'){
    $qrsig = daddslashes($_GET['qrsig']);
    $result = qrlogin($qrsig);
    if($result['uin'] && $result['skey']){
	   $_SESSION['findpwd_qq']=$result['uin'];
       $result = array('saveOK'=>$result['saveOK'],'uin'=>$result['uin'],'skey'=>$result['skey'],'pskey'=>$result['pskey'],'superkey'=>$result['superkey'],'nick'=>$result['nick']);
    }else{
        $result = array('saveOK'=>$result['saveOK'],'msg'=>$result['msg']);
    }
}elseif($act=='findpwd'){
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
		    $_SESSION['pwd_error']++;
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}else{  //服务器宕机,走failback模式
		if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
			//echo '{"status":"success"}';
		}else{
		    $_SESSION['pwd_error']++;
			exit('{"code":-1,"msg":"验证失败，请重新验证"}');
		}
	}
    $email=daddslashes(htmlspecialchars($_POST['email']));
    $rew=$DB->query("SELECT * FROM pay_user WHERE email='{$email}' limit 1")->fetch();
    if($_SESSION['pwd_error']==3||($_SESSION['pwd_time']&&$_SESSION['pwd_time']<time())){
	      $result=array("code"=>-1,"msg"=>"登录失败，请5分钟后再次尝试x.-1");
	      $_SESSION['pwd_time']=time()+300;
    }elseif(!$rew){
        $_SESSION['pwd_error']++;
        exit('{"code":-1,"msg":"你的账号不存在于平台，请重新检查"}');
    }else{
        $msg = '您的账号'.$rew['user'].'密码'.$rew['pass'];
        $sqs = send_mail($email, $conf['sitename'].'- 找回密码', $msg);
        if($sqs){
           $result = array('code'=>1,'msg'=>'请求成功请检查邮箱是否收到，若没有收到，请联系网站管理进行反馈');
        }else{
            $result = array('code'=>-1,'msg'=>'请求失败，错误代码x-1000');
        }
    }
}elseif($act=='logout'){
	setcookie("user_token", "", time() - 604800);
	setcookie("pay_pass", "", time() - 43200);
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已成功注销本次登陆！');window.location.href='./Login.php';</script>");
}elseif($act=='Pay_pass'){
    $pay_pass=MD5(daddslashes($_POST['pay_pass']));
    if (empty($pay_pass)) {
        exit('{"code":-1,"msg":"二级密码不能为空"}');
    }
    if (empty($userrow['pay_pass'])) {
        exit('{"code":0,"msg":"您还没有设置二级密码"}');
    }
    if ($userrow['pay_pass'] == $pay_pass) {
        setcookie("pay_pass", authcode($pay_pass, 'ENCODE', $conf['KEY']), time() + 43200);
        $result = array("code" => 1, "msg" => "验证成功！");
    }else{
        $result = array("code" => -1, "msg" => "验证失败，二级密码错误");
    }
}elseif($act=='edit_pwd'){
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
    $oldpwd = daddslashes($_POST['oldpwd']);
    $newpwd = daddslashes($_POST['newpwd']);
    $newpwd2 = daddslashes($_POST['newpwd2']);
    if($newpwd==$newpwd2){
        if($userrow['pass'] and $oldpwd){
            if($oldpwd==$userrow['pass']){
                if($DB->exec("update `pay_user` set `pass`='{$newpwd}' where pid='{$userrow['pid']}'")){
                    Add_log($userrow['pid'],'修改商户密码');
				    $result=array("code"=>1,"msg"=>"修改密码成功");
                }else{
                    $result = array("code" =>-1,"msg" =>"修改失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
                }
            }else{
                $result = array("code"=>-1, "msg" =>"旧密码不对！");
            }
        }else{
            $result = array("code"=>-1,"msg"=>"旧密码不能为空！");
        }
    }else{
        $result = array("code" => -1, "msg" => "两次输入密码不一致！");
    }

}elseif($act=='Del_Qr_status'){
    if(!$user_pass)exit(json_encode(array('code'=>-1,'msg'=>'禁止恶意访问！')));
	$id=daddslashes($_POST['id']);
    $is=$DB->query("SELECT * FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
    if($is){
        if($is['qr_status']==1){
            $DB->exec("update `pay_qrlist` set `nums`='0',`qr_status`='0' WHERE `id`='{$is['id']}'");//更新数据
        }else{
            $DB->exec("update `pay_qrlist` set `nums`='0',`qr_status`='1' WHERE `id`='{$is['id']}'");//更新数据
        }
        $result=array("code"=>1,"msg"=>"更新数据成功");
    }else{
        $result=array("code"=>-1,"msg"=>"二维码不存在！");
    }
}elseif($act=='edit_pass'){
    $oldpwd = daddslashes($_POST['oldpwd']);
    $newpwd = daddslashes($_POST['newpwd']);
    $newpwd2 = daddslashes($_POST['newpwd2']);
    if($newpwd==$newpwd2){
        $newpwd = MD5($newpwd);
        if($userrow['pay_pass'] and $oldpwd){
            if(MD5($oldpwd)==$userrow['pay_pass']){
                if($DB->exec("update `pay_user` set `pay_pass`='{$newpwd}' where pid='{$userrow['pid']}'")){
                    Add_log($userrow['pid'],'修改二级密码');
				    $result=array("code"=>1,"msg"=>"修改二级密码成功");
                }else{
                    $result = array("code" =>-1,"msg" =>"修改失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
                }
            }else{
                 $result = array("code"=>-1, "msg" =>"旧密码不对！");
            }
        }else{
            if($DB->exec("update `pay_user` set `pay_pass`='{$newpwd}' where pid='{$userrow['pid']}'")){
                Add_log($userrow['pid'],'设置二级密码');
			    $result=array("code"=>1,"msg"=>"设置二级密码成功");
            }else{
                $result = array("code" =>-1,"msg" =>"修改失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
            }
        }
    }else{
        $result = array("code" => -1, "msg" => "两次输入密码不一致！");
    }
}elseif($act=='user_settle_save'){
    $id=daddslashes($_POST['id']);
    $appid=daddslashes($_POST['appid']);
    $appkey2=daddslashes($_POST['appkey2']);
    $qr_url=daddslashes($_POST['qr_url']);
    $ali_order_check=daddslashes($_POST['ali_order_check']);
    if($ali_order_check!='order_no')$ali_order_check='order_amount';
    if(!$appid || !$appkey2 || !$qr_url){
        $result=array("code"=>-1,"msg"=>"请确保AppID、应用私钥和支付宝收款码链接不能为空");
    }elseif(!preg_match('/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/)/i', $qr_url)){
        $result=array("code"=>-1,"msg"=>"支付宝收款码链接格式不正确，请填写 alipays://、alipayqr:// 或 https:// 开头的链接");
    }else{
        $qrrow = $DB->query("SELECT id,json FROM pay_qrlist WHERE id='{$id}' and pid='{$userrow['pid']}' limit 1")->fetch();
        $qr_json = json_decode($qrrow['json'],true);
        if(!is_array($qr_json))$qr_json = [];
        $qr_json['ali_order_check'] = $ali_order_check;
        $qr_json = daddslashes(json_encode($qr_json));
        $save_money = null;
        $balance_msg = '';
        $balance_data = ['appid'=>$appid,'appkey'=>'','appkey2'=>$appkey2];
        $balance_ret = AlipayDataBillBalanceQueryRequest($balance_data);
        $balance_json = json_decode($balance_ret,true);
        if(is_array($balance_json) && $balance_json['code']=='10000' && isset($balance_json['available_amount'])){
            $save_money = daddslashes($balance_json['available_amount']);
            $balance_msg = '，当前余额：￥'.$save_money;
        }else{
            $balance_err = is_array($balance_json) ? ($balance_json['sub_msg']?$balance_json['sub_msg']:$balance_json['msg']) : '支付宝接口响应异常';
            $balance_msg = '，余额获取失败：'.$balance_err;
        }
        $money_sql = $save_money!==null ? ",`money`='{$save_money}'" : '';
        $srow = $DB->query("SELECT * FROM pay_alidata WHERE qr_id='{$id}' and pid='{$userrow['pid']}' limit 1")->fetch();
        if($srow){
            $sqs = $DB->exec("update `pay_alidata` set `appid`='{$appid}',`appkey2`='{$appkey2}' where qr_id='{$id}' and pid='{$userrow['pid']}'");
            $sqs2 = $DB->exec("update `pay_qrlist` set `qr_url`='{$qr_url}',`json`='{$qr_json}'{$money_sql},`status`='1' where id='{$id}' and pid='{$userrow['pid']}'");
            if($sqs!==false && $sqs2!==false){
                Add_log($userrow['pid'],'更新支付宝免挂'.$id);
                $result=array("code"=>1,"msg"=>"更新支付宝免挂成功".$balance_msg);
            }else{
                $result=array("code"=>-1,"msg"=>"更新支付宝免挂失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
            }
        }else{
            $sqs=$DB->exec("INSERT INTO `pay_alidata` (`qr_id`, `pid`, `appid`, `appkey`, `appkey2`) VALUES ('{$id}','{$userrow['pid']}', '{$appid}', '', '{$appkey2}')");
            $sqs2=$DB->exec("update `pay_qrlist` set `qr_url`='{$qr_url}',`json`='{$qr_json}'{$money_sql},`status`='1' where id='{$id}' and pid='{$userrow['pid']}'");
            if($sqs && $sqs2!==false){
                Add_log($userrow['pid'],'添加支付宝免挂'.$id);
                $result=array("code"=>1,"msg"=>"添加支付宝免挂成功".$balance_msg);
            }else{
                $result=array("code"=>-1,"msg"=>"添加支付宝免挂失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
            }
        }
    }
}
exit(json_encode($result));