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
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;
if($islogin_user==1){}else exit(json_encode(array("code"=>-5,"msg"=>"未登录")));

switch($act){
case 'getcount':
    $orderarray = array(
        'count1'=>$DB->query("SELECT count(*) from pay_order WHERE `pid`='{$userrow['pid']}' ")->fetchColumn(),
        'count2'=>$DB->query("SELECT count(*) from pay_order WHERE `pid`='{$userrow['pid']}' and status='1'")->fetchColumn(),
        'count3'=>$DB->query("SELECT sum(money) from pay_order WHERE `pid`='{$userrow['pid']}' and status='1'")->fetchColumn()
        );
    if(!$count5)$count5=0;
    $lastday=date("Y-m-d",strtotime("-1 day"));
    $today=date("Y-m-d");
    $rs=$DB->query("SELECT * from pay_order WHERE `pid`='{$userrow['pid']}' and status=1 and date='$today'");
    $order_today=array('alipay'=>0,'tenpay'=>0,'qqpay'=>0,'wxpay'=>0,'all'=>0);
    while($row = $rs->fetch())
    {
        $order_today[$row['type']]+=$row['money'];
        $order_today[$row['type']]=round($order_today[$row['type']],2);
    }
    $order_today['all']=$order_today['alipay']+$order_today['tenpay']+$order_today['qqpay']+$order_today['wxpay'];
    $rs=$DB->query("SELECT * from `pay_order` WHERE `pid`='{$userrow['pid']}' and `status`=1 and date='$lastday'");
    $order_lastday=array('alipay'=>0,'qqpay'=>0,'wxpay'=>0,'all'=>0);
    while($row = $rs->fetch())
    {
        $order_lastday[$row['type']]+=$row['money'];
        $order_lastday[$row['type']]=round($order_lastday[$row['type']],2);
    }
    $order_lastday['all']=$order_lastday['alipay']+$order_lastday['tenpay']+$order_lastday['qqpay']+$order_lastday['wxpay'];
    $data['order_today']=$order_today;
    $data['order_lastday']=$order_lastday;
    if($res['user_vip']>=1){
        $pack=$DB->query("SELECT * FROM `pay_taocan` WHERE `id`='{$userrow['user_vip']}' limit 1")->fetch();
    }else{
        $pack['name'] = '无';
    }
    $rs = $DB->query("SELECT * FROM pay_notice where status='1' order by id ASC limit 20");

    while($row = $rs->fetch())
    {
        $title[]=$row['datatxt'];
        $css[]=$row['color'];
    }
	if($userrow){
        $result = array('code'=>$islogin_user,'orderarray'=>$orderarray,'data'=>$data,'user'=>$userrow,'user_vip_time'=>date("Y年m月d日",strtotime($userrow['user_vip_time'])),'vip_name'=>$pack['name'],'url'=>$siteurl,'title'=>$title,'css'=>$css);
    }else{
        $result = array('code'=>-1,'msg'=>'未登录平台');
    }
    exit(json_encode($result));
    break;
case 'AliLogin':
    $id = daddslashes($_POST['id']);
    $user = daddslashes($_POST['user']);
    $pass = daddslashes($_POST['pass']);
    $n = daddslashes($_POST['n']);
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Yun.Api.php';
    $Yun_Login = new Yun_Login();
	$result = $Yun_Login->AliLogin($user,$pass,$n);
	if($result['code']==200 and $result['cookie']){
	    $username = username($result['cookie'],'alipay');
	    $name = $username['userName'].'|'.$username['email'];
	    if(PayName($username['userName'])){
	       exit('{"code":-1,"msg":"你已经列入，本系统的实体清单，有问题请联系QQ2945080486"}');
	    }
		$sqs=$DB->exec("update `pay_qrlist` set `cookie`='{$result['cookie']}',`money`='0.00',`status`='1',`addtime`='{$date}',`data_data`='{$name}',`email_status`='0' where `id`='{$id}' and `pid`='{$userrow['pid']}'");
		if($sqs){
		    Add_log($userrow['pid'],'更新二维码: '.$id);
		    $result=array("code"=>200,"msg"=>$result['msg']);
		}else{
		    $result=array("code"=>-1,"msg"=>"更新失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
		}
	}
	exit(json_encode($result));
    break;
case 'AliLogin_Sms':
    $id = daddslashes($_POST['id']);
    $smscode = daddslashes($_POST['smscode']);
    $securityId = daddslashes($_POST['securityId']);
    $ALIPAYJSESSIONID = daddslashes($_POST['ALIPAYJSESSIONID']);
    $_form_token = daddslashes($_POST['_form_token']);
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Yun.Api.php';
    $Yun_Login = new Yun_Login();
	$result = $Yun_Login->AliLogin_Sms($smscode,$securityId,$ALIPAYJSESSIONID,$_form_token);
	if($result['code']==200 and $result['cookie']){
	    $username = username($result['cookie'],'alipay');
	    $name = $username['userName'].'|'.$username['email'];
	    if(PayName($username['userName'])){
	       exit('{"code":-1,"msg":"你已经列入，本系统的实体清单，有问题请联系QQ2945080486"}');
	    }
		$sqs=$DB->exec("update `pay_qrlist` set `cookie`='{$result['cookie']}',`money`='0.00',`status`='1',`addtime`='{$date}',`data_data`='{$name}',`email_status`='0' where `id`='{$id}' and `pid`='{$userrow['pid']}'");
		if($sqs){
		    Add_log($userrow['pid'],'更新二维码: '.$id);
		    $result=array("code"=>200,"msg"=>$result['msg']);
		}else{
		    $result=array("code"=>-1,"msg"=>"更新失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
		}
	}
	exit(json_encode($result));
    break;
case 'AliYunGet':
    $id = daddslashes($_POST['id']);
    $result = $DB->query("SELECT a.appid,a.appkey2,q.qr_url,q.money,q.json FROM pay_qrlist q LEFT JOIN pay_alidata a ON a.qr_id=q.id AND a.pid=q.pid WHERE q.id='{$id}' and q.pid='{$userrow['pid']}' limit 1")->fetch();
    $qr_json = json_decode($result['json'],true);
    if(!is_array($qr_json))$qr_json = [];
    $result['ali_order_check'] = $qr_json['ali_order_check'] ? $qr_json['ali_order_check'] : 'order_amount';
    unset($result['json']);
    exit(json_encode($result));
    break;
case 'AliYunApp':
    $id = daddslashes($_POST['id']);
    $row = $DB->query("SELECT * FROM pay_qrlist WHERE id='{$id}' and pid='{$userrow['pid']}' limit 1")->fetch();
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Yun.Api.php';
    $Yun_Ali_App = new Yun_Ali_App();
    $result = $Yun_Ali_App->app($row);
    exit(json_encode($result));
    break;
case 'AliYunSet':
    $id = daddslashes($_POST['id']);
    $appid = daddslashes($_POST['appid']);
    $cookie = daddslashes($_POST['cookie']);
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Yun.Api.php';
    $Yun_Ali_App = new Yun_Ali_App();
    $result = $Yun_Ali_App->AliYunSet($appid,$cookie);
    if($result['PublicKey']){
        $appkey = $result['PublicKey'];
    }
    if($result['code']==200){
        $srow = $DB->query("SELECT * FROM pay_alidata WHERE qr_id='{$id}' and pid='{$userrow['pid']}' limit 1")->fetch();
        if($srow){
            if($result['PublicKey']){
                $appkey = $result['PublicKey'];
            }
            if($appid!=$srow['appid']){
                $sqs = $DB->exec("update `pay_alidata` set `appid`='{$appid}' where qr_id='{$id}' and pid='{$userrow['pid']}'");
            }
            if($appkey!=$srow['appkey']){
                $sqs = $DB->exec("update `pay_alidata` set `appkey`='{$appkey}' where qr_id='{$id}' and pid='{$userrow['pid']}'");
            }
            if($appkey2!=$srow['appkey2']){
                $sqs = $DB->exec("update `pay_alidata` set `appkey2`='{$appkey2}' where qr_id='{$id}' and pid='{$userrow['pid']}'");
            }
            if($sqs){
                $result=array("code"=>1,"msg"=>"更新支付宝免挂成功");
            }else{
                $result=array("code"=>-1,"msg"=>"更新支付宝免挂失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
            }
        }else{
            $sqs=$DB->exec("INSERT INTO `pay_alidata` (`pid`, `qr_id`, `appid`, `appkey`, `appkey2`) VALUES ('{$userrow['pid']}','{$id}', '{$appid}', '{$appkey}', '{$appkey2}')");
            if($sqs){
                Add_log($userrow['pid'],'添加支付宝免挂'.$id);
                $DB->exec("update `pay_qrlist` set `status`='1' where pid='{$userrow['pid']}' and id='{$id}'");
                $result=array("code"=>1,"msg"=>"添加支付宝免挂成功");
            }else{
                $result=array("code"=>-1,"msg"=>"添加支付宝免挂失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
            }
        }
    }
    exit(json_encode($result));
    break;
case 'AliYunSms':
    $id = daddslashes($_POST['id']);
    $appid = daddslashes($_POST['appid']);
    $phone = daddslashes($_POST['phone']);
    $smscode = daddslashes($_POST['smscode']);
    $cookie = daddslashes($_POST['cookie']);
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Yun.Api.php';
    $Yun_Ali_App = new Yun_Ali_App();
    $result = $Yun_Ali_App->AliYunSms($appid,$phone,$smscode,$cookie);
    if($result['PublicKey']){
        $appkey = $result['PublicKey'];
    }
    if($result['code']==200){
        $srow = $DB->query("SELECT * FROM pay_alidata WHERE qr_id='{$id}' and pid='{$userrow['pid']}' limit 1")->fetch();
        if($srow){
            if($appid!=$srow['appid']){
                $sqs = $DB->exec("update `pay_alidata` set `appid`='{$appid}' where qr_id='{$id}' and pid='{$userrow['pid']}'");
            }
            if($appkey!=$srow['appkey']){
                $sqs = $DB->exec("update `pay_alidata` set `appkey`='{$appkey}' where qr_id='{$id}' and pid='{$userrow['pid']}'");
            }
            if($appkey2!=$srow['appkey2']){
                $sqs = $DB->exec("update `pay_alidata` set `appkey2`='{$appkey2}' where qr_id='{$id}' and pid='{$userrow['pid']}'");
            }
            if($sqs){
                $result=array("code"=>1,"msg"=>"更新支付宝免挂成功");
            }else{
                $result=array("code"=>-1,"msg"=>"更新支付宝免挂失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
            }
        }else{
            $sqs=$DB->exec("INSERT INTO `pay_alidata` (`pid`, `qr_id`, `appid`, `appkey`, `appkey2`) VALUES ('{$userrow['pid']}','{$id}', '{$appid}', '{$appkey}', '{$appkey2}')");
            if($sqs){
                Add_log($userrow['pid'],'添加支付宝免挂'.$id);
                $DB->exec("update `pay_qrlist` set `status`='1' where pid='{$userrow['pid']}' and id='{$id}'");
                $result=array("code"=>1,"msg"=>"添加支付宝免挂成功");
            }else{
                $result=array("code"=>-1,"msg"=>"添加支付宝免挂失败".'['.$DB->errorInfo()[1].'] '.$DB->errorInfo()[2]);
            }
        }
    }
    exit(json_encode($result));
case 'Login_Type':
    $channel = daddslashes($_POST['channel']);
    if($channel=='mg_vzq' or $channel=='yd_vzq' or $channel=='mg_qq' or $channel=='yd_qq'){
        /*
        $html.='<option value="9">手表协议-1</option>';
        $html.='<option value="10">手表协议-2</option>';
        $html.='<option value="11">手表协议-3</option>';
        $html.='<option value="12">手表协议-4</option>';
        $html.='<option value="13">手表协议-5</option>';
        $html.='<option value="14">手表协议-6</option>';
        $html.='<option value="15">手表协议-7</option>';
        $html.='<option value="16">手表协议-8</option>';
        */
        $rs=$DB->query("SELECT * FROM pay_yund WHERE type=3 and status=1 order by addtime ASC");
        while($res = $rs->fetch())
        {
            $html.='<option value="'.$res['id'].'">'.$res['name'].'</option>';
        }
    }elseif($channel=='yd_wx'){
        $rs=$DB->query("SELECT * FROM pay_yund WHERE type=1 and status=1 order by addtime ASC");
        while($res = $rs->fetch())
        {
            $html.='<option value="'.$res['id'].'">'.$res['name'].'</option>';
        }
    }elseif($channel=='yd_wx_uos'){
        $rs=$DB->query("SELECT * FROM pay_yund WHERE type=4 and status=1 order by addtime ASC");
        while($res = $rs->fetch())
        {
            $html.='<option value="'.$res['id'].'">'.$res['name'].'</option>';
        }
    }elseif($channel=='yd_wx_gskd' or $channel=='yd_wx_sskd'){
        $rs=$DB->query("SELECT * FROM `pay_yund` WHERE `type` = 2 AND `status` = 1");
        while($res = $rs->fetch())
        {
            $html.='<option value="'.$res['id'].'">'.$res['name'].'</option>';
        }
    }
    $result = ['code','html'=>$html];
    exit(json_encode($result));
    break;
case 'skdtypeuser':
    $id = daddslashes($_POST['id']);
    $cookie = daddslashes($_POST['cookie']);
    $is=$DB->query("SELECT json FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
    if($is){
        require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
        $json = json_decode($is['json'],true);
        $WxApi = new WxApi($DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}'")->fetch()['url']);
        $row = $WxApi->Get_account_id($cookie);
        if($row['errcode']==0){
            foreach ($row['data']['account_list'] as $item)
            {
                $html.='<option value="'.$item['account_id'].'">'.$item['account_name'].'</option>';
            }
            $result = ['code'=>200,'html'=>$html];
        }
        
    }
    
    exit(json_encode($result));
    break;
case 'skdtypeSet':
    $id = daddslashes($_POST['id']);
    $account_id = daddslashes($_POST['account_id']);
    $is=$DB->query("SELECT id,json FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$id}' limit 1")->fetch();
    if($is){
        $json = jsondet(json_decode($is['json'],true),['account_id'=>$account_id]);
        if($DB->exec("update `pay_qrlist` set `json`='{$json}' WHERE `id`='{$is['id']}'")){
            $result = ['code'=>200,'msg'=>'设置成功'];
        }else{
            $result = ['code'=>-1,'msg'=>'设置失败'];
        }
    }else{
        $result = ['code'=>-1,'msg'=>'你无权限'];
    }
    exit(json_encode($result));
    break;
default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}