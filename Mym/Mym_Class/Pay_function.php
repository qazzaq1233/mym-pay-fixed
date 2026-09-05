<?php
// +----------------------------------------------------------------------
// | Quotes [ 只为给用户更好的体验]**[我知道发出来有人会盗用,但请您留版权]
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MYM  <485570653@qq.com>        Mymcode          盗用不留版权,你就不配拿去!
// +----------------------------------------------------------------------
// | Date: 2021年09月04日
// +----------------------------------------------------------------------

function Yun_WxPay($row){
    global $DB,$conf,$date;
    $timess = time()+120;
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
    $json = json_decode($row['json'],true);
    $WxApi = new WxApi($DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}'")->fetch()['url']);
    if(($row['login_time']-20)<time()){
        if($WxApi->WXHeartBeatY($row['cookie'])['data']['baseResponse']['ret'] ==0){
            $DB->exec("update `pay_wechat_trumpet` set `login_time`='{$timess}' WHERE `id`='{$row['id']}'");
        }else{
            return '微信云端已经掉线';
        }
    }elseif($row['status']==1){
        $WXSyncMsg = $WxApi->WXSyncMsg($row['cookie']);
        //file_put_contents('1.txt', json_encode($WXSyncMsg),FILE_APPEND);
        //exit(json_encode($WXSyncMsg));
        if($WXSyncMsg['data']['Result']['AddMsgs']){
            foreach ($WXSyncMsg['data']['Result']['AddMsgs'] as $item){//个人收款
                if(strstr($item['Content']['String'], '微信收款助手') and strstr($item['Content']['String'], '已存入店长') and $item['ToUserName']['String']=='wxid_cxs6cd21tcyu22' and !$money){//店员收款
                    $money = getSubstr($item['PushContent'],'收款到账','元');
                    $name = getSubstr($item['Content']['String'],'已存入店长','(**');
                }elseif(strstr($item['Content']['String'], '赞赏到账通知') and !$money){//赞赏码收款
                    $money = getSubstr($item['PushContent'],'二维码赞赏到账','元');//二维码赞赏到账0.01元
                    if(!$money){
                        preg_match('/二维码赞赏到账(.*?)元/i',$item['PushContent'],$money);
                        $money=$money[1];//个人码/经营
                    }
                    $name = $row['wx_name'];
                    
                }elseif(strstr($item['Content']['String'], '收款单到账通知')  and !$money){//收款单收款
                    $money = getSubstr($item['PushContent'],'收款单到账','元');
                    $name = $row['wx_name'];
                    
                }elseif(strstr($item['Content']['String'], '邀请你成为他的店员')){
                    //$name = getSubstr($item['PushContent'],'[小程序]','邀请你成为他的店员');
                    $token = getSubstr($item['Content']['String'],'token=','</publisherId>');
                    if(!$token){
                        $token = getSubstr($item['Content']['String'],"token=","]]><");  //苹果手机
                    }
                    
                    if($token){
                        $code = $WxApi->WXJSLogin($row['cookie'],'wx28be8489b7a36aaa');
                        if($code){
                            $test = $WxApi->baodindy($code,$token);
                        }else{
                            $test = '获取小程序code失败';
                        }
                    }else{
                        $test = '获取信息token失败';
                    }
                }elseif(strstr($item['Content']['String'], '微信支付收款') and strstr($item['Content']['String'], '已存入零钱')){
                    preg_match('/支付收款(.*?)元/i',$item['Content']['String'],$money);
                    $money=$money[1];//个人码/经营
                    $name = $row['wx_name'];
                    //exit($money);
                }
                //exit($test.$name);
                $Qr=$DB->query("SELECT * FROM `pay_qrlist` WHERE `type`='wxpay' and `beizhu`='{$name}' and `wx_name`='{$row['wx_name']}' and `qr_status`='1' limit 1")->fetch();
                if($name and $Qr['cookie']==0 and !$Qr['wx_name']){
                    if($DB->exec("update `pay_qrlist` set `status`='1',`endtime`='{$date}',`cookie`='Login_Yes',`wx_name`='{$row['wx_name']}' WHERE `beizhu`='{$name}'")){
                        exit('更新微信状态成功：'.$name.'平台状态没有成功，已自动为您更新状态，下次执行则回调');
                    }else{
                        exit('平台无此用户，可解除绑定！->'.$name.'绑定->'.$row['wx_name']);
                    }
                }elseif($name){
                    $userrow_pid = $DB->query("SELECT * FROM pay_user WHERE pid='{$Qr['pid']}' limit 1")->fetch();
                    $pid = $Qr['pid'];
                    $time = time();
                    if(!$userrow_pid){
                        $test = "当前商户PID或账号不存在";
                    }else{
                        $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$pid}' and type='wxpay' and price='{$money}' and outtime>'{$time}' limit 1")->fetch();
                        if($srow and $srow['status']==0){
                            
                            $url=creat_callback($srow);
                            $datm=do_notify($url['notify']);
                            Add_log($pid,'自动回调订单(云端微信店员'.$name.')：'.$srow['trade_no']);
                            if($datm)$datm='success';
                            pay_notify($pid,'wxpay',$money,$datm,$srow['trade_no']);
                            if($rew['money_mail']==1)paymali($srow,$rew['email'],$E_type.'自动回调订单(云端店员'.$name.')');
                            exit(($data?$data:'success'));
                        }else{
                            $test = "此金额匹配不到任何订单".$money;
                        }
                    }
                }
                return $name.$test;
            }
            return $name.$money;
        }
        
    }
    return $row['wx_name'];
}

function check_usdt($row,$send = null){
    global $DB,$conf;
    $Pay_Money = Pay_Money_Get($row['type'],$row['cookie']);
    $timess = time()+rand(10,25);
    foreach (usdt_Order($row['cookie']) as $item){
        $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and type='usdt' and price='{$item['money']}' and outtime>'{$item['time']}' and addtime<'{$item['addtime']}' limit 1")->fetch();
                //获取订单：获取接口支付金额，判断超时时间小于现在，获取接口的支付备注
    	if($srow and $srow['status']==0){//判断是否有并且，订单超时时间小于现在，则执行下一步
    	    $url=creat_callback($srow);
    	    $datm = do_notify($url['notify']);
    	    if($datm)$datm='success';
    	    pay_notify($srow['pid'],'usdt',$srow['price'],$datm,$srow['trade_no']);
    	    if($rew['money_mail']==1)paymali($srow,$rew['email'],'USDT自动回调订单(免挂回调)');
    	}
    }
    $DB->exec("update `pay_qrlist` set `money`='{$Pay_Money['money']}',`crontime`='{$timess}' WHERE `id`='{$row['id']}'");
    return 'usdt-'.$Pay_Money['money'].'_';
}

function mym_qr_needs_ck_check($row)
{
    if(!is_array($row))return false;
    if(isset($row['hook_type']) && intval($row['hook_type']) !== 0)return false;
    if(!in_array($row['type'], array('alipay','qqpay','qqhpay','vzqpay')))return false;
    if(empty($row['cookie']))return false;
    if($row['type']=='alipay' && $row['channel']=='mg_ali' && !empty($row['qr_url']) && empty($row['cookie']))return false;
    return true;
}

function mym_qr_ck_json($row)
{
    $json = json_decode(isset($row['json']) ? $row['json'] : '', true);
    return is_array($json) ? $json : array();
}

function mym_qr_ck_save_json($row,$json,$extra_sql='')
{
    global $DB;
    $json_text = daddslashes(json_encode($json, JSON_UNESCAPED_UNICODE));
    $DB->exec("update `pay_qrlist` set `json`='{$json_text}'{$extra_sql} WHERE `id`='{$row['id']}'");
}

function mym_qr_ck_record_success($row)
{
    $json = mym_qr_ck_json($row);
    $json['ck_fail_count'] = 0;
    $json['ck_last_success'] = time();
    $json['ck_last_check'] = time();
    $json['ck_last_error'] = '';
    mym_qr_ck_save_json($row,$json,",`status`='1'");
}

function mym_qr_ck_record_failure($row,$reason)
{
    $json = mym_qr_ck_json($row);
    $fail_count = isset($json['ck_fail_count']) ? intval($json['ck_fail_count']) : 0;
    $fail_count++;
    $last_success = isset($json['ck_last_success']) ? intval($json['ck_last_success']) : 0;
    $json['ck_fail_count'] = $fail_count;
    $json['ck_last_check'] = time();
    $json['ck_last_error'] = $reason;
    $retry_time = time()+rand(180,300);
    $max_fail = 20;
    $success_grace = 7200;
    $should_offline = ($fail_count >= $max_fail && ($last_success <= 0 || time()-$last_success > $success_grace));
    if(!$should_offline){
        mym_qr_ck_save_json($row,$json,",`status`='1',`crontime`='{$retry_time}'");
        return array('online'=>true,'keep_online'=>true,'fail_count'=>$fail_count,'max_fail'=>$max_fail,'last_success'=>$last_success);
    }
    mym_qr_ck_save_json($row,$json);
    return array('online'=>false,'keep_online'=>false,'fail_count'=>$fail_count,'max_fail'=>$max_fail,'last_success'=>$last_success);
}

function mym_mark_qr_ck_offline($row,$reason='COOKIE失效',$send_notice=false)
{
    global $DB,$conf,$date;
    if(empty($date))$date = date("Y-m-d H:i:s");
    $name_arr = explode('|', isset($row['data_data']) ? $row['data_data'] : '');
    $name = isset($name_arr[1]) ? $name_arr[1] : '';
    $rew = $DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
    if($row['type']=='alipay'){
        $E_type = '支付宝';
    }elseif($row['type']=='qqpay' || $row['type']=='vzqpay'){
        $E_type = 'QQ钱包';
    }elseif($row['type']=='qqhpay'){
        $E_type = 'QQ红包';
    }else{
        $E_type = $row['type'];
    }
    $json = mym_qr_ck_json($row);
    $json['ck_offline_at'] = time();
    $json['ck_last_error'] = $reason;
    $json_text = daddslashes(json_encode($json, JSON_UNESCAPED_UNICODE));
    $sub = $conf['sitename'].' - COOKIE失效提醒';
    $msg = '尊敬的：'.$conf['sitename'].'用户'.$name.',您好! 您在'.$conf['sitename'].'上挂的['.$E_type.']COOKIE失效了ID:'.$row['id'].',为了不影响您继续使用,请务必去更新,地址:http://'.$_SERVER['HTTP_HOST'];
    $DB->exec("update `pay_qrlist` set `status`='0',`crontime`='0',`endtime`='{$date}',`email_status`='1',`json`='{$json_text}' WHERE `id`='{$row['id']}'");
    if($send_notice && isset($row['email_status']) && intval($row['email_status'])==0 && !empty($rew['email']))send_mail($rew['email'], $sub, $msg);
    return $E_type.'ID：'.$row['id'].'--'.$reason.'  ';
}

function mym_check_qr_ck_online($row)
{
    if(!mym_qr_needs_ck_check($row))return array('need_check'=>false,'online'=>true,'msg'=>'无需COOKIE检测');
    $Pay_Money = Pay_Money_Get($row['type'],$row['cookie']);
    if(isset($Pay_Money['status']) && $Pay_Money['status']){
        mym_qr_ck_record_success($row);
        return array('need_check'=>true,'online'=>true,'msg'=>'COOKIE在线','money'=>isset($Pay_Money['money'])?$Pay_Money['money']:0);
    }
    $cookie_raw = base64_decode($row['cookie']);
    if($row['type']=='alipay' && strpos($cookie_raw,'ALIPAYJSESSIONID=')!==false && strpos($cookie_raw,'CLUB_ALIPAY_COM=')!==false){
        mym_qr_ck_record_success($row);
        return array('need_check'=>true,'online'=>true,'msg'=>'COOKIE在线，余额接口暂不可用','money'=>isset($row['money'])?$row['money']:0,'balance_error'=>true);
    }
    $reason = 'CK检测接口临时失败或超时';
    $state = mym_qr_ck_record_failure($row,$reason);
    if($state['online']){
        return array('need_check'=>true,'online'=>true,'msg'=>'CK检测异常，已保活重试('.$state['fail_count'].'/'.$state['max_fail'].')','money'=>isset($row['money'])?$row['money']:0,'transient_error'=>true,'fail_count'=>$state['fail_count']);
    }
    return array('need_check'=>true,'online'=>false,'msg'=>'CK连续检测失败，已判定掉线，请重新扫码更新通道','money'=>isset($Pay_Money['money'])?$Pay_Money['money']:-1,'fail_count'=>$state['fail_count']);
}

function mym_alipay_ck_bill_notify($row,$rew,$E_type)
{
    global $DB;
    $Get_Order = getAliOrder($row['cookie']);
    if(!is_array($Get_Order) || !isset($Get_Order['result']['detail']) || !is_array($Get_Order['result']['detail'])){
        return array('checked'=>true,'result'=>'');
    }
    $time = time();
    foreach ($Get_Order['result']['detail'] as $item){
        $order_money = isset($item['tradeAmount']) ? number_format((float)$item['tradeAmount'], 2, '.', '') : '';
        $order_memo = isset($item['transMemo']) ? daddslashes(trim($item['transMemo'])) : '';
        $order_time = isset($item['tradeTime']) ? strtotime($item['tradeTime']) : 0;
        if(!$order_money || $order_money <= 0)continue;
        $time_sql = '';
        if($order_time>0)$time_sql = " and outtime>='{$order_time}' and addtime<='".date("Y-m-d H:i:s", $order_time)."'";
        if($order_memo){
            $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and type='{$row['type']}' and qr_id='{$row['id']}' and price='{$order_money}' and (trade_no='{$order_memo}' or out_trade_no='{$order_memo}') and outtime>'{$time}'{$time_sql} order by addtime desc limit 1")->fetch();
        }else{
            $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and type='{$row['type']}' and qr_id='{$row['id']}' and price='{$order_money}' and outtime>'{$time}'{$time_sql} order by addtime desc limit 1")->fetch();
        }
        if($srow && $srow['status']==0 && $srow['outtime']>time()){
            $trade_no=$srow['trade_no'];
            $url = creat_callback($srow);
            $log = do_notify($url['notify']);
            Add_log($row['pid'],'支付宝CK账单回调订单：'.$trade_no);
            if($log)$log='success';
            pay_notify($row['pid'],$row['type'],$order_money,$log,$trade_no);
            if($rew['money_mail']==1)paymali($srow,$rew['email'],$E_type.'CK账单回调订单');
            return array('checked'=>true,'result'=>$E_type.'ID：'.$row['id'].'--账单收款：'.$order_money.'支付成功订单号:'.$trade_no.'  ');
        }
    }
    return array('checked'=>true,'result'=>'');
}

function check_money_notify($row,$send = null){
    global $DB,$conf;
    $date = date("Y-m-d H:i:s");
    $timess=time()+rand(10,25);//当前时间戳 +30秒(下次执行时间)
	if($row['type']=='alipay'){
		$E_type = '支付宝';
	}elseif($row['type']=='qqpay'){
		$E_type = 'QQ钱包';
	}elseif($row['type']=='wxpay'){
		$E_type = '微信';
		if($row['channel']=='mg_vzq' or $row['channel']=='yd_vzq'){
		    $E_type = '微信转QQ';
		    $row['type']='qqpay';
		}
	}elseif($row['type']=='qqhpay'){
	    $E_type = 'QQ红包';
	}
	if($send==true){
        $time = time()-1;
        if($row['crontime']==$time){
            return $E_type.'-'.$row['id'].'访问太频繁了，请稍后再次访问';
        }
    }
	$name_arr = explode('|', isset($row['data_data']) ? $row['data_data'] : '');
	$name = isset($name_arr[1]) ? $name_arr[1] : '';
	$rew=$DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
	if($row['type']=='alipay' && $row['channel']=='mg_ali' && !empty($row['qr_url']) && empty($row['cookie'])){
	    $DB->exec("update `pay_qrlist` set `crontime`='{$timess}' WHERE `id`='{$row['id']}'");
	    return $E_type.'ID：'.$row['id'].'--手动收款码链接模式，无需COOKIE自动生成  ';
	}
	if($row['type']=='alipay' && $row['hook_type']==2 && !empty($row['qr_url'])){
	    $DB->exec("update `pay_qrlist` set `crontime`='{$timess}' WHERE `id`='{$row['id']}'");
	    return $E_type.'ID：'.$row['id'].'--免CK手动收款码链接模式，无需COOKIE自动生成  ';
	}
	$Qr_Money = 0;
	if($row['type']=='alipay'){
	    // 支付页轮询先查最近账单，命中订单立即回调；避免先跑余额/CK检测导致回调慢。
	    $bill_check = mym_alipay_ck_bill_notify($row,$rew,$E_type);
	    if(!empty($bill_check['result'])){
	        $DB->exec("update `pay_qrlist` set `status`='1',`crontime`='{$timess}' WHERE `id`='{$row['id']}'");
	        return $bill_check['result'];
	    }
	}
	$ck_check = mym_check_qr_ck_online($row);
	if($ck_check['need_check'] && !$ck_check['online']){
	    return mym_mark_qr_ck_offline($row,'COOKIE失效或已掉线',$send==true);
	    }else{
	        $Pay_Money = array('money'=>isset($ck_check['money'])?$ck_check['money']:0, 'status'=>true);
	        if($row['type']=='alipay' && isset($ck_check['balance_error']) && $ck_check['balance_error']){
	            // 支付宝账单已优先检查；余额接口不可用时不再用旧余额参与差额匹配。
	            $Pay_Money['money'] = isset($row['money']) ? $row['money'] : 0;
	        }
	        if($Pay_Money['money']>$row['money']){
	        $Qr_Money=bcsub($Pay_Money['money'],$row['money'],2);
	    }
	$time = time();
	if($Qr_Money and $row['type']=='qqhpay'){//判断是否是收入金额
	    $Order = Pay_Qqhpay_Order($row);
	    foreach ($Order['records'] as $item)
	    {
	        $price = $item['price']/100;
	        $api_trade_no = $item['sp_billno'];
	        $srow = $DB->query("SELECT * FROM pay_order WHERE `api_trade_no`='{$api_trade_no}' and status='0' limit 1")->fetch();
	        if($srow){
	            $trade_no=$srow['trade_no'];
	            $url = creat_callback($srow);
	            $log = do_notify($url['notify']);
	            
	            Add_log($row['pid'],'自动回调订单(免挂回调)：'.$trade_no);
	            if($log)$log='success';
	            pay_notify($row['pid'],$row['type'],$Qr_Money,$log,$trade_no);
	            if($rew['money_mail']==1){
	                paymali($srow,$rew['email']);
	            }
	        }
	    }
	}else if($Qr_Money){
	    $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and `qr_id`='{$row['id']}' and price='{$Qr_Money}' and outtime>'{$time}' limit 1")->fetch();
	    if($srow && $srow['status']==0 && $srow['outtime']>time()){
	       $trade_no=$srow['trade_no'];
	       $url = creat_callback($srow);
	       $log = do_notify($url['notify']);
	       
	       Add_log($row['pid'],'自动回调订单(免挂回调)：'.$trade_no);
	       if($log)$log='success';
	       pay_notify($row['pid'],$row['type'],$Qr_Money,$log,$trade_no);
	       if($rew['money_mail']==1){
	           paymali($srow,$rew['email']);
	       }
	    }
	}
	
	if(isset($Pay_Money['money']) && $Pay_Money['money'] !== null && $Pay_Money['money'] !== '' && $Pay_Money['money'] >= 0){
	    $DB->exec("update `pay_qrlist` set `money`='{$Pay_Money['money']}',`status`='1',`crontime`='{$timess}' WHERE `id`='{$row['id']}'");//更新数据
	}else{
	    $DB->exec("update `pay_qrlist` set `status`='1',`crontime`='{$timess}' WHERE `id`='{$row['id']}'");//更新数据
	}
	if(!isset($ck_check['transient_error']) || !$ck_check['transient_error'])mym_qr_ck_record_success($row);
	if(!$result){
	    if(isset($ck_check['transient_error']) && $ck_check['transient_error']){
	        $balance_msg = $ck_check['msg'];
	    }else{
	        $balance_msg = isset($ck_check['balance_error']) && $ck_check['balance_error'] ? '余额接口暂不可用，已保持CK在线' : '余额：'.$Pay_Money['money'].'元';
	    }
	    $result=$E_type.'ID：'.$row['id'].'--'.$balance_msg.'  ';
	}
	}
	return $result;
}

function yunck_money_notify($row,$send = null){
    global $DB,$conf;
    $date = date("Y-m-d H:i:s");
    $timess=time()+rand(10,25);//当前时间戳 +30秒(下次执行时间)
    $Pay_Money = ['money'=>null];
	if($row['type']=='alipay'){
		$E_type = '支付宝';
	}elseif($row['type']=='qqpay'){
		$E_type = 'QQ钱包';
	}elseif($row['type']=='wxpay'){
		$E_type = '微信';
		if($row['channel']=='mg_vzq' or $row['channel']=='yd_vzq'){
		    $E_type = '微信转QQ';
		    $row['type']='qqpay';
		}
	}elseif($row['type']=='qqhpay'){
	    $E_type = 'QQ红包';
	}
	$name_arr = explode('|', isset($row['data_data']) ? $row['data_data'] : '');
	$name = isset($name_arr[1]) ? $name_arr[1] : '';
	$rew=$DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
	if($send==true){
        $time = time()-1;
        if($row['crontime']==$time){
            return $E_type.'-'.$row['id'].'访问太频繁了，请稍后再次访问';
        }
    }
    if($row['type']=='qqpay'){
        $qr_json = json_decode($row['json'],true);
        if(!is_array($qr_json))$qr_json = [];
        if($row['hook_type']==2 && $row['channel']=='yd_qq' && $qr_json['Login_Id'] && $row['cookie'] && strpos($row['cookie'],'qluin=')===false && strpos(base64_decode($row['cookie']),'qluin=')===false){
            require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
            $apiurl = $DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$qr_json['Login_Id']}'")->fetch()['url'];
            $QqApi = new QqApi($apiurl);
            $loginStatus = $QqApi->LoginStatus($row['cookie']);
            if(is_array($loginStatus) && $loginStatus['code']==200){
                $DB->exec("update `pay_qrlist` set `crontime`='{$timess}',`status`='1' WHERE `id`='{$row['id']}'");
                return $E_type.'ID：'.$row['id'].'--QQ_Pc云端在线  ';
            }else{
                $msg_text = is_array($loginStatus) ? $loginStatus['msg'] : 'QQ_Pc云端状态检测失败';
                $DB->exec("update `pay_qrlist` set `status`='0',`data_data`='0',`endtime`='{$date}',`email_status`='1' WHERE `id`='{$row['id']}'");
                return $E_type.'ID：'.$row['id'].'--'.$msg_text.'  ';
            }
        }
        $Pay_Money = Pay_Money_Get($row['type'],$row['cookie']);
        if($Pay_Money['status']==false){
            if($row['hook_type']==2 && $row['channel']=='yd_qq'){
                $DB->exec("update `pay_qrlist` set `crontime`='{$timess}' WHERE `id`='{$row['id']}'");
                return $E_type.'ID：'.$row['id'].'--QQ钱包余额接口检测失败，云端通道已保持在线，请以云端回调状态为准  ';
            }
            require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
            $json = json_decode($row['json'],true);
            $apiurl = $DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}'")->fetch()['url'];
            $QqApi = new QqApi($apiurl);
            $result = $QqApi->Add_Cookie($row['beizhu']);
            if($result['code']==1){
                $DB->exec("update `pay_qrlist` set `cookie`='{$result['cookie']}' WHERE `id`='{$row['id']}'");//更新失效数据
            }else{
                $sub = $conf['sitename'].' - COOKIE失效提醒';$msg = '尊敬的：'.$conf['sitename'].'用户'.$name.',您好! 您在'.$conf['sitename'].'上挂的['.$E_type.']COOKIE失效了ID:'.$row['id'].',为了不影响您继续使用,请务必去更新,地址:http://'.$_SERVER['HTTP_HOST'];$DB->exec("update `pay_qrlist` set `status`='0',`data_data`='0',`endtime`='{$date}',`email_status`='1' WHERE `id`='{$row['id']}'");//更新失效数据
                if($send==true and $row['email_status']==0)
                {
                    $send_res = send_mail($rew['email'], $sub, $msg);
                }
            }
        }else{
            
            if($Pay_Money['money']>$row['money'])
            {
                $Qr_Money=bcsub($Pay_Money['money'],$row['money'],2);
            }
            if($Qr_Money){//判断是否是收入金额
                $time = time();
                $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and `qr_id`='{$row['id']}' and price='{$Qr_Money}' and outtime>'{$time}' order by addtime desc limit 1")->fetch();
                if($srow && $srow['status']==0 && $srow['outtime']>time()){
                    $trade_no=$srow['trade_no'];
                    $url = creat_callback($srow);
                    $log = do_notify($url['notify']);
                    Add_log($row['pid'],'自动回调订单(云端回调)：'.$trade_no);
                    if($log)$log='success';
                    pay_notify($row['pid'],$row['type'],$Qr_Money,$log,$trade_no);
                    if($rew['money_mail']==1){
                        paymali($srow,$rew['email'],$E_type.'自动回调订单(云端回调)');
                    }
                }
            }
        }
	}else{
	    $data = $DB->query("SELECT * FROM pay_alidata WHERE qr_id='{$row['id']}' limit 1")->fetch();
	    $qr_json = json_decode($row['json'],true);
	    if(!is_array($qr_json))$qr_json = [];
	    $ali_order_check = $qr_json['ali_order_check'] ? $qr_json['ali_order_check'] : 'order_amount';
	    if($ali_order_check!='order_no')$ali_order_check='order_amount';
	    if(!$data || !$data['appid'] || !$data['appkey2']){
	        $result = $E_type.'ID：'.$row['id'].'--支付宝免挂应用数据未配置完整  ';
	    }else{
	        $order = AlipayDataBillAccountlogQueryRequest($data);
	        $json = json_decode($order,true);
	        if(!is_array($json))$json = [];
	        $time = time();
	        $rew=$DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
	        if($json['code']=='10000' && is_array($json['detail_list'])){
	            foreach ($json['detail_list'] as $item)
	            {
	                $pay_type = trim($item['pay_type']);
	                if($pay_type && $pay_type!='ALIPAYACCOUNT')continue;
	                $direction = trim($item['direction']);
	                $bill_type = trim($item['type']);
	                $trans_memo = trim(str_replace('请勿添加备注-', '', $item['trans_memo']));
	                $trans_amount = trim($item['trans_amount']);
	                $third_order_no = trim($item['alipay_order_no']);
	                if(!$third_order_no)$third_order_no = trim($item['merchant_order_no']);
	                if(!$third_order_no)$third_order_no = 'ALI'.md5($item['trans_dt'].'|'.$trans_amount.'|'.$trans_memo.'|'.$item['balance']);
	                $third_order_no = daddslashes($third_order_no);
	                $trans_time = strtotime($item['trans_dt']);
	                if($third_order_no){
	                    $used_order = $DB->query("SELECT trade_no FROM pay_order WHERE api_trade_no='{$third_order_no}' and status='1' limit 1")->fetch();
	                    if($used_order)continue;
	                }
	                if($item['merchant_order_no'] && $bill_type!='交易')continue;
	                if($direction && $direction!='收入')continue;
	                if($bill_type && $bill_type!='转账' && $bill_type!='交易')continue;
	                if($trans_amount<=0)continue;
	                $srow = null;
	                if($trans_memo){
	                    $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and qr_id='{$row['id']}' and pid='{$row['pid']}' and type='{$row['type']}' and price='{$trans_amount}' and (trade_no='{$trans_memo}' or out_trade_no='{$trans_memo}') and outtime>'{$time}' limit 1")->fetch();
	                }
	                if(!$srow && $ali_order_check=='order_amount'){
	                    $addtime_sql = '';
	                    if($trans_time>0)$addtime_sql = " and addtime<='".date("Y-m-d H:i:s", $trans_time)."' and outtime>='{$trans_time}'";
	                    $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and qr_id='{$row['id']}' and pid='{$row['pid']}' and type='{$row['type']}' and price='{$trans_amount}' and outtime>'{$time}'{$addtime_sql} order by addtime desc limit 1")->fetch();
	                }
	                if($srow && $srow['status']==0 && $srow['outtime']>time()){
	                    $trade_no=$srow['trade_no'];
	                    if($third_order_no)$DB->exec("update `pay_order` set `api_trade_no`='{$third_order_no}' where `trade_no`='{$srow['trade_no']}' and (`api_trade_no` is null or `api_trade_no`='')");
	                    $srow['api_trade_no'] = $third_order_no;
	                    $url=creat_callback($srow);
	                    $log = do_notify($url['notify']);
	                    $result = "PID：".$row['id']."账单收款：".$trans_amount.'支付成功订单号:'.$trade_no;
	                    Add_log($row['pid'],'支付宝免挂账单回调订单(免CK回调)：'.$trade_no);
	                    if($log)$log='success';
	                    pay_notify($row['pid'],$row['type'],$trans_amount,$log,$trade_no);
	                    if($rew['money_mail']==1)paymali($srow,$rew['email'],$E_type.'免挂账单回调订单(免CK回调)');
	                    break;
	                }
	            }
	        }else{
	            $alipay_msg = $json['sub_msg']?$json['sub_msg']:$json['msg'];
	            $bill_error = $E_type.'ID：'.$row['id'].'--账单查询失败：'.$alipay_msg.'  ';
	        }
	        $need_balance = ($send !== true || intval($row['crontime']) < time()-30 || (isset($row['money']) && floatval($row['money']) <= 0));
	        if($need_balance){
	            $Pay_Data = AlipayDataBillBalanceQueryRequest($data);
	            $Pay_Data = json_decode($Pay_Data,true);
	            if(!is_array($Pay_Data))$Pay_Data = [];
	            if($Pay_Data['code']=='10000'){
	                $bill_error = null;
	                $Pay_Money['money'] = $Pay_Data['available_amount'];
	                $Qr_Money=bcsub($Pay_Data['available_amount'],$row['money'],2);
	                // 支付宝免 CK 模式只用账单明细回调订单，余额接口仅用于更新余额显示。
	                // 不能再用余额差额回调，否则用户私下转入同金额后，新订单会被历史余额差额误判为已支付。
	            }
	        }else{
	            $Pay_Money['money'] = $row['money'];
	        }
	        if(!$result && $bill_error)$result = $bill_error;
	    }
    }
	if(isset($Pay_Money['money']) && $Pay_Money['money'] !== null && $Pay_Money['money'] !== '' && $Pay_Money['money'] >= 0){
	    $DB->exec("update `pay_qrlist` set `money`='{$Pay_Money['money']}',`status`='1',`crontime`='{$timess}' WHERE `id`='{$row['id']}'");//更新数据
	}else{
	    $DB->exec("update `pay_qrlist` set `status`='1',`crontime`='{$timess}' WHERE `id`='{$row['id']}'");//更新数据
	}
	if(!$result){$result=$E_type.'ID：'.$row['id'].'--'.'余额：'.$Pay_Money['money'].'元  ';
	}
	
	return $result;
}
function wxyun_time_cron($row,$send = null){
    global $DB,$conf;
    if($row['channel']=='yd_wx'){
        iMac_cron($row,$send);
    }elseif($row['channel']=='yd_wx_uos'){
        WxUos_Cron($row,$send);
    }else{
        Windows_cron($row,$send);
    }
}

function WxUos_Cron($row,$send = null){
    global $DB,$conf,$date;
    $rew=$DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
    $json = json_decode($row['json'],true);
    $WxApi = new wxuos($DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}'")->fetch()['url']);
    $data = $WxApi->get_msg($row['cookie']);
    if($data['code']!=1){
        $sub = $conf['sitename'].' - COOKIE失效提醒';
		$msg = '尊敬的：'.$conf['sitename'].'用户'.$name.',您好! 您在'.$conf['sitename'].'上挂的['.$E_type.']COOKIE失效了ID:'.$row['id'].',为了不影响您继续使用,请务必去更新,地址:http://'.$_SERVER['HTTP_HOST'];
		$DB->exec("update `pay_qrlist` set `status`='0',`data_data`='0',`endtime`='{$date}',`email_status`='1' WHERE `id`='{$row['id']}'");//更新失效数据
		if($send==true and $row['email_status']==0){
		    $send_res = send_mail($rew['email'], $sub, $msg);
		}
        return false;
    }
    foreach ($data['data'] as $item){//"微信支付收款0.72元(老顾客第20次消费)"
        preg_match('/微信支付收款(.*?)元/i',$item['title'],$money);
        preg_match('/二维码赞赏到账(.*?)元/i',$item['title'],$money2);
        $time=$item['time'];
        if(!$money[1] and $money2[1])continue;//跳过此订单
        if($money[1])$money=$money[1];
        if($money2[2])$money=$money2[1];
        $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and `qr_id`='{$row['id']}' and price='{$money}' and outtime>'{$time}' limit 1")->fetch();
	    if($srow['status']==0 && $srow['outtime']>time()){
	       $trade_no=$srow['trade_no'];
	       $url = creat_callback($srow);
	       $log = do_notify($url['notify']);
	       Add_log($row['pid'],'微信云端回调订单(云端回调)：'.$trade_no);
	       if($log)$log='success';
	       pay_notify($row['pid'],$row['type'],$money,$log,$trade_no);
	       
	       if($rew['money_mail']==1){
	           paymali($srow,$rew['email'],'微信云端回调订单(云端回调)：'.$trade_no);
	       }
	    }
    }
    
    if($data['code']==1){
        $timess=time()+rand(10,25);//当前时间戳 +30秒(下次执行时间)
        $DB->exec("update `pay_qrlist` set `crontime`='{$timess}' WHERE `id`='{$row['id']}'");//更新数据
    }
}

function Windows_cron($row,$send = null){
    global $DB,$conf;
    
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
    $json = json_decode($row['json'],true);
    $WxApi = new WxApi($DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}'")->fetch()['url']);
    $date = date("Y-m-d H:i:s");
    $timess=time()+20;//当前时间戳 +30秒(下次执行时间)
    if($rew['channel']=='yd_wx_gskd'){
        $E_type = "收款单个人版";
    }elseif($row['channel']=='yd_wx_sskd'){
        $E_type = "收款单商家版";
    }
    $rew=$DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
    if($WxApi->WXHeartBeatY($row['cookie'])['data']['baseResponse']['ret'] ==0){
        $sidcode = $WxApi->Get_Sid_Cron($row['cookie'],$json['sid']);
        if($sidcode['code']==200){
            $DB->exec("update `pay_qrlist` set `crontime`='{$timess}' WHERE `id`='{$row['id']}'");
        }else{
            $json = jsondet(json_decode($row['json'],true),['sid'=>$sidcode['sid']]);
            $DB->exec("update `pay_qrlist` set `crontime`='{$timess}',`json`='{$json}' WHERE `id`='{$row['id']}'");
        }
        $trade_no = daddslashes($_GET['trade_no']);
        if($trade_no){
            $srow = $DB->query("SELECT * FROM pay_order WHERE trade_no='{$trade_no}' limit 1")->fetch();
            $result = $WxApi->Order_Cron($row,$srow['api_trade_no']);
            if($result['code']==200 and $result['money']==$srow['price']){
                if($srow['status']==0 && $srow['outtime']>time()){
                    $trade_no=$srow['trade_no'];
                    $url=creat_callback($srow);
                    $log = do_notify($url['notify']);
                    return '微信云端回调订单：'.$trade_no.' 金额：'.$item['money'];
                }
            }
        }else{
            $time = time()-30;
            $rs=$DB->query("SELECT * from pay_order WHERE api_trade_no!='' and status='0' and outtime>'{$time}'");
            while($srow = $rs->fetch())
            {
                $result = $WxApi->Order_Cron($row,$srow['api_trade_no']);
                if($result['code']==200 and $result['money']==$srow['price']){
                    if($srow['status']==0 && $srow['outtime']>time()){
                        $trade_no=$srow['trade_no'];
                        $url=creat_callback($srow);
                        $log = do_notify($url['notify']);
                        Add_log($row['pid'],'微信云端回调订单(免挂回调)：'.$trade_no);
                        if($log)$log='success';
                        pay_notify($row['pid'],$row['type'],$item['money'],$log,$trade_no);
                        if($rew['money_mail']==1)paymali($srow,$rew['email'],$E_type.'云端回调订单(免挂回调)');
                        
                        return '微信云端回调订单：'.$trade_no.' 金额：'.$item['money'];
                    }
                }
            }
        }
	}else{
		$sub = $conf['sitename'].' - COOKIE失效提醒';
		$msg = '尊敬的：'.$conf['sitename'].'用户,您好! 您在'.$conf['sitename'].'上挂的['.$E_type.']COOKIE失效了,为了不影响您继续使用,请务必去更新,地址:http://'.$_SERVER['HTTP_HOST'];
		if($send==true and $row['email_status']==0)$send_res = send_mail($rew['email'], $sub, $msg);
		   $DB->exec("update `pay_qrlist` set `status`='0',`cookie`='0',`data_data`='0',`endtime`='{$date}',`email_status`='1' WHERE `id`='{$row['id']}'");//更新失效数据
	}
	
	return $row['id'].'-'.$E_type.'<br>';
}

function iMac_cron($row,$send = null){
    global $DB,$conf;
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
    $json = json_decode($row['json'],true);
    $WxApi = new WxApi($DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}'")->fetch()['url']);
    $date = date("Y-m-d H:i:s");
    $time = time();
    $timess=time()+20;//当前时间戳 +30秒(下次执行时间)
    $E_type = '微信';
    $rew=$DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
	$WXHeart= $WxApi->WXHeartBeat($row['cookie']);
    if($WXHeart['code']=='1'){
		$DB->exec("update `pay_qrlist` set `crontime`='{$timess}' WHERE `id`='{$row['id']}'");
		$WxMoney = $WxApi->Wx_Get_Money($row['cookie']);
    	if($WxMoney['data']){
	    	foreach ($WxMoney['data'] as $item)
            {
	    	    $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and type='{$row['type']}' and price='{$item['money']}' and outtime>'{$time}' and trade_no='{$item['trade_no']}' limit 1")->fetch();
	    	    if(!empty($srow)){
    	            if($srow['status']==0 && $srow['outtime']>time()){
    		           $trade_no=$srow['trade_no'];
    		           $url=creat_callback($srow);
    		           $log = do_notify($url['notify']);
    		           
    		           Add_log($row['pid'],'微信云端回调订单(免挂回调)：'.$trade_no);
    		           if($log)$log='success';
		               pay_notify($row['pid'],$row['type'],$item['money'],$log,$trade_no);
		               if($rew['money_mail']==1)paymali($srow,$rew['email'],$E_type.'云端回调订单(免挂回调)');
		               return '微信云端回调订单：'.$trade_no.' 金额：'.$item['money'];
    		        }
    	    	}
	        }
    	}
	}else{
		$sub = $conf['sitename'].' - COOKIE失效提醒';
		$msg = '尊敬的：'.$conf['sitename'].'用户,您好! 您在'.$conf['sitename'].'上挂的['.$E_type.']COOKIE失效了,为了不影响您继续使用,请务必去更新,地址:http://'.$_SERVER['HTTP_HOST'];
		if($send==true and $row['email_status']==0)$send_res = send_mail($rew['email'], $sub, $msg);
		   $DB->exec("update `pay_qrlist` set `status`='0',`cookie`='0',`data_data`='0',`endtime`='{$date}',`email_status`='1' WHERE `id`='{$row['id']}'");//更新失效数据
	}
	
	return $row['id'].'-'.$E_type.'<br>';
}

function Order_notify($row)
{
    global $DB,$conf;

    $time = time();
    $rew=$DB->query("SELECT * FROM pay_user WHERE pid='{$row['pid']}' limit 1")->fetch();
    if($row['type']=='alipay'){
        $type = '支付宝';
        if($row['channel']=='mg_ali' && !empty($row['qr_url']) && empty($row['cookie'])){
            $timess = time()+rand(60,120);
            $DB->exec("update `pay_qrlist` set `Order_time`='{$timess}' WHERE `id`='{$row['id']}'");
            return 'ID：'.$row['id'].'--Type：支付宝手动收款码链接--';
        }
        if($row['hook_type']==2 && !empty($row['qr_url'])){
            $timess = time()+rand(60,120);
            $DB->exec("update `pay_qrlist` set `Order_time`='{$timess}' WHERE `id`='{$row['id']}'");
            return 'ID：'.$row['id'].'--Type：支付宝免CK手动收款码链接--';
        }
        $Get_Order = getAliOrder($row['cookie']);
        if($Get_Order['result']['detail']){
            foreach ($Get_Order['result']['detail'] as $item)//循环获取json数组数据
            {
                $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and type='{$row['type']}' and price='{$item['tradeAmount']}' and outtime<'{$time}' and trade_no='{$item['transMemo']}' limit 1")->fetch();
                //获取订单：获取接口支付金额，判断超时时间小于现在，获取接口的支付备注
                if(!empty($srow)){
    	            if($srow['status']==0 and $srow['outtime']<time()){//判断是否有并且，订单超时时间小于现在，则执行下一步
    	                $url=creat_callback($srow);
    	                $log = do_notify($url['notify']);
    	                if($log)$log='success';
    	                $msg = '系统自动补单成功，补单的金额：'.$item['tradeAmount'].'订单号是：'.$item['transMemo'].'付款时间：'.$item['tradeTime'];
    	                send_mail($rew['email'], $conf['sitename'].'- 支付宝自动补单', $msg);
    	                Add_log($row['pid'],'支付宝自动补单订单：'.$srow['trade_no']);
    	                pay_notify($row['pid'],$row['type'],$item['money'],$log,$srow['trade_no']);
    	                $log = '支付宝自动补单订单：'.$srow['trade_no'].' 金额：'.$item['money'];
    	            }
    	        }
            }
        }
    }elseif($row['type']=='qqpay'){
        $type = 'QQ钱包';
        $Cookie=base64_decode($row['cookie']);
        $uin = explode("qluin=",$Cookie);
		$skey = getSubstr($Cookie,"skey=", ";");
		$p_skey = "p_skey=".getSubstr($Cookie,"p_skey=", ";").";";
		$Url = 'https://myun.tenpay.com/cgi-bin/clientv1.0/qwallet_record_list.cgi?limit=20&offset=0&s_time=2021-07-10&ref_param=&source_type=7&time_type=0&bill_type=0&uin='.$uin[1];
		$Get_Order = json_decode(get_curl($Url,0,0,$p_skey), true);
		if($Get_Order['records']){
		    foreach ($Get_Order['records'] as $item)//循环获取json数组数据
		    {
		        $money = $item['amount']/100;
		        $order_time = strtotime($item['create_time']);
		        $srow = $DB->query("SELECT * FROM pay_order WHERE status='0' and pid='{$row['pid']}' and type='{$row['type']}' and price='{$money}' and outtime>'{$order_time}' and addtime<'{$item['create_time']}' limit 1")->fetch();
		        ////订单超时时间大于支付时间，并且订单发起时间小于订单支付时间
                if(!empty($srow) and $srow['outtime']<time()){//判断是否有并且，订单超时时间小于现在，则执行下一步
    	            if($srow['status']==0){
    	                $url=creat_callback($srow);
    	                $log = do_notify($url['notify']);
    	                if($log)$log='success';
    	                $msg = '系统自动补单成功，补单的金额：'.$money.'订单号是：'.$srow['trade_no'].'付款时间：'.$item['create_time'];
    	                send_mail($rew['email'], $conf['sitename'].'- QQ自动补单', $msg);
    	                Add_log($row['pid'],'QQ自动补单订单：'.$srow['trade_no']);
    	                pay_notify($row['pid'],$row['type'],$money,$log,$srow['trade_no']);
    	                $log = 'QQ自动补单订单：'.$srow['trade_no'].' 金额：'.$money;
    	            }
    	        }
	    	}
		}
    }else{
        $type = 'QQ钱包';
        $Cookie=base64_decode($row['cookie']);
        $uin = explode("qluin=",$Cookie);
		$skey = getSubstr($Cookie,"skey=", ";");
		$p_skey = "p_skey=".getSubstr($Cookie,"p_skey=", ";").";";
		$Url = 'https://myun.tenpay.com/cgi-bin/clientv1.0/qwallet_record_list.cgi?limit=20&offset=0&s_time=2021-07-10&ref_param=&source_type=7&time_type=0&bill_type=0&uin='.$uin[1];
		$Get_Order = json_decode(get_curl($Url,0,0,$p_skey), true);
		if($Get_Order['records']){
		    foreach ($Get_Order['records'] as $item)//循环获取json数组数据
		    {
	            $api_trade_no = $item['sp_billno'];
	            $srow = $DB->query("SELECT * FROM pay_order WHERE `api_trade_no`='{$api_trade_no}' and status='0' limit 1")->fetch();
		        ////订单超时时间大于支付时间，并且订单发起时间小于订单支付时间
                if(!empty($srow) and $srow['outtime']<time()){//判断是否有并且，订单超时时间小于现在，则执行下一步
    	            if($srow['status']==0){
    	                $url=creat_callback($srow);
    	                $log = do_notify($url['notify']);
    	                if($log)$log='success';
    	                $msg = '系统自动补单成功，补单的金额：'.$money.'订单号是：'.$srow['trade_no'].'付款时间：'.$item['create_time'];
    	                send_mail($rew['email'], $conf['sitename'].'- QQ自动补单', $msg);
    	                Add_log($row['pid'],'QQ自动补单订单：'.$srow['trade_no']);
    	                pay_notify($row['pid'],$row['type'],$money,$log,$srow['trade_no']);
    	                $log = 'QQ自动补单订单：'.$srow['trade_no'].' 金额：'.$money;
    	            }
    	        }
	    	}
		}
    }
    $timess = time()+rand(60,120);
    $DB->exec("update `pay_qrlist` set `Order_time`='{$timess}' WHERE `id`='{$row['id']}'");//更新数据
    if(!$log){
        return 'ID：'.$row['id'].'--Type：'.$type.'--';
    }else{ 
        return $log;
    }
}


function pay_notify($pid,$type,$money,$msg,$trade_no)
{
    global $DB,$date;
    $DB->query("insert into `pay_notify` (`pid`,`type`,`money`,`pay_msg`,`addtime`,`trade_no`) values ('".$pid."','".$type."','".$money."','".$msg."','".$date."','".$trade_no."')");
}

//添加操作记录
function Add_log($pid,$type) {
	global $DB,$ip,$date;
	$city=get_ip_city($ip)['Result']['Country'];
	if($pid=='admin')$pid=0;
	$DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('".$pid."','".$type."','".$date."','".$ip."','".$city."')");
	return '添加成功'; 
}

function submit_pay($parameter,$method = 'POST',$key = null,$HTTP_HOST = null){//发起支付支付
    global $conf;
    if($conf['http']==1){
        $HTTP_HOST = 'https://'.$_SERVER['HTTP_HOST'].'/submit.php';
    }else{
        $HTTP_HOST = $HTTP_HOST?$HTTP_HOST.'submit.php':($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/submit.php';
    }
	

	$sign_type = 'MD5';
	$sign = MD5("money=".$parameter['money']."&name=".$parameter['name']."&notify_url=".$parameter['notify_url']."&out_trade_no=".$parameter['out_trade_no']."&pid=".$parameter['pid']."&return_url=".$parameter['return_url']."&type=".$parameter['type'].$key);
	return '<body onLoad="document.uncome.submit()">
	<form name="uncome" action="'.$HTTP_HOST.'" method="'.$method.'">
	<input type="hidden" name="pid"  value="'.$parameter['pid'].'">
	<input type="hidden" name="type"  value="'.$parameter['type'].'">
	<input type="hidden" name="out_trade_no"  value="'.$parameter['out_trade_no'].'">
	<input type="hidden" name="notify_url"  value="'.$parameter['notify_url'].'">
	<input type="hidden" name="return_url"  value="'.$parameter['return_url'].'">
	<input type="hidden" name="name"  value="'.$parameter['name'].'">
	<input type="hidden" name="money"  value="'.$parameter['money'].'">
	<input type="hidden" name="sign"  value="'.$sign.'">
	<input type="hidden" name="sign_type"  value="'.$sign_type.'">';
}

function verifyNotify($key){//发起支付的签名验证
	if(empty($_GET)) {//判断POST来的数组是否为空
			return false;
	}
	$mysgin  = MD5("money=".$_GET['money']."&name=".$_GET['name']."&out_trade_no=".$_GET['out_trade_no']."&pid=".$_GET['pid']."&trade_no=".$_GET['trade_no']."&trade_status=TRADE_SUCCESS&type=".$_GET['type'].$key);
	if($mysgin == $_GET['sign']) {
		return true;
	}
	else {
		return false;
		//return $mysgin;
	}
}

function creat_callback($data){//异步回调
	global $DB, $conf, $date;
	$userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$data['pid']}' limit 1")->fetch();
	$sign = md5("money=".$data['money']."&name=".$data['name']."&out_trade_no=".$data['out_trade_no']."&pid=".$data['pid']."&trade_no=".$data['trade_no']."&trade_status=TRADE_SUCCESS&type=".$data['type'].$userrow['key']);
	$array=array('pid'=>$data['pid'],'trade_no'=>$data['trade_no'],'out_trade_no'=>$data['out_trade_no'],'type'=>$data['type'],'name'=>$data['name'],'money'=>$data['money'],'trade_status'=>'TRADE_SUCCESS');
	$urlstr=http_build_query($array);
	$ss = $userrow['money']-$data['money'];
	if($data['status']==0 and $ss>=0.01){
	    $DB->query("update `pay_user` set `money`=`money`-'{$data['money']}' where pid='{$data['pid']}'");
	}elseif($data['status']==0 and $ss<0){
	    $DB->query("update `pay_user` set `money`='0.00' where pid='{$data['pid']}'");
	}
	$DB->query("update `pay_order` set `status` ='1',`endtime` ='{$date}' where `trade_no`='{$data['trade_no']}'");
	if(strpos($data['notify_url'],'?'))
		$url['notify']=$data['notify_url'].'&'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
	else
		$url['notify']=$data['notify_url'].'?'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
	if(strpos($data['return_url'],'?'))
		$url['return']=$data['return_url'].'&'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
	else
		$url['return']=$data['return_url'].'?'.$urlstr.'&sign='.$sign.'&sign_type=MD5';
	return $url;
}

function callback_sign($data,$name){ //获取签名算法  接口需要发送提交云端
	global $DB, $conf;
	$userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$data['pid']}' limit 1")->fetch();
	$sign = MD5("money=".$data['money']."&name=".$data['name']."&out_trade_no=".$data['out_trade_no']."&pid=".$data['pid']."&trade_no=".$data['trade_no']."&trade_status=TRADE_SUCCESS&type=".$data['type'].$userrow['key']);
	return $sign;
}


function getQQNick($qq){//获取QQ网名
   $get_info = file_get_contents('http://r.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?get_nick=1&uins='.$qq);
    //转换编码
    $get_info = mb_convert_encoding($get_info, "UTF-8", "GBK");
    //对获取的json数据进行截取并解析成数组
    $name = json_decode(substr($get_info,17,-1),true);
	return $name[$qq][6];
}

function mym_pay_qr_url_matches_type($type,$qr_url)
{
    $type = strtolower(trim((string)$type));
    $qr_url = trim(rawurldecode(urldecode((string)$qr_url)));
    if($qr_url==='')return true;
    if($type=='alipay'){
        if(preg_match('/(qianbao\.qq\.com|tenpay\.com|mqqapi:\/\/|weixin:\/\/|wxp:\/\/)/i',$qr_url))return false;
        return true;
    }
    if($type=='qqpay' || $type=='qqhpay'){
        if(preg_match('/(alipays?:\/\/|alipayqr:\/\/|alipay\.com|render\.alipay\.com|ds\.alipay\.com)/i',$qr_url))return false;
        return true;
    }
    if($type=='wxpay'){
        if(preg_match('/(alipays?:\/\/|alipayqr:\/\/|alipay\.com|render\.alipay\.com|ds\.alipay\.com)/i',$qr_url))return false;
        return true;
    }
    return true;
}

function mym_assert_qr_row_for_type($QR_row,$type,$context='')
{
    if(!is_array($QR_row) || !$QR_row)return false;
    if(isset($QR_row['type']) && $QR_row['type'] != $type)return false;
    $json_data = json_decode(isset($QR_row['json']) ? $QR_row['json'] : '', true);
    if(!is_array($json_data))$json_data = array();
    if(!empty($json_data['custom_qr_url']) && !mym_pay_qr_url_matches_type($type,$json_data['custom_qr_url']))return false;
    if(!empty($QR_row['qr_url']) && !mym_pay_qr_url_matches_type($type,$QR_row['qr_url']))return false;
    return true;
}

function qrdecode($QR_row,$price,$trade_no,$options=array())
{
    global $DB, $conf;
    if(!is_array($options))$options = array();
    $result = array('api_trade_no'=>'NULL');
    $qr_url = '';
    $json_data = json_decode($QR_row['json'], true);
    if(!is_array($json_data))$json_data = array();
    if(!empty($json_data['custom_qr_url'])){
        $custom_qr_url = $json_data['custom_qr_url']==1 ? $QR_row['qr_url'] : $json_data['custom_qr_url'];
        if(!mym_pay_qr_url_matches_type($QR_row['type'],$custom_qr_url)){
            return array('code'=>-1,'msg'=>'当前通道收款码链接与支付类型不匹配，请检查通道配置','qr_url'=>'','api_trade_no'=>'NULL');
        }
        $result['qr_url'] = rawurlencode($custom_qr_url);
        return $result;
    }
    $userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$QR_row['pid']}' limit 1")->fetch();
    if($QR_row['type']=="qqpay"){
        if($QR_row['hook_type']==0 && $QR_row['channel']=='mg_qq'){
            require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
            $cookie = base64_decode($QR_row['cookie']);
            if(strpos($cookie,'qluin=')!==false && strpos($cookie,'p_skey=')!==false){
                $QqApi = new QqApi();
                $ret = $QqApi->Add_Yun_Qrcode($cookie,$price,$trade_no,$options);
                if(is_array($ret) && isset($ret['retmsg']) && strtolower($ret['retmsg'])=='success' && !empty($ret['auth_code'])){
                    $qq = getSubstr($cookie,"qluin=", ";");
                    if(!$qq && strpos($cookie,'qluin=')!==false)$qq = trim(explode('qluin=',$cookie)[1]);
                    if(strpos($qq,';')!==false)$qq = trim(explode(';',$qq)[0]);
                    $qr_url = 'https://i.qianbao.qq.com/wallet/sqrcode.htm?m=tenpay&f=wallet&a=1&ac='.strFilter($ret['auth_code']).'&u='.$qq.'&n=Mym+H5+Pay';
                }else{
                    $msg = is_array($ret) && !empty($ret['retmsg']) ? $ret['retmsg'] : 'QQ钱包设置金额接口返回异常';
                    $retryable = is_array($ret) && isset($ret['retryable']) ? intval($ret['retryable']) : 0;
                    if(!empty($options['test_order']) && $retryable && !empty($QR_row['qr_url'])){
                        $result['qr_url'] = rawurlencode($QR_row['qr_url']);
                        $result['test_order_notice'] = 'QQ金额码临时生成失败，已回退使用通道原始收款码';
                        return $result;
                    }
                    return array('code'=>-1,'msg'=>'QQ钱包免挂金额码生成失败：'.$msg,'qr_url'=>'','api_trade_no'=>'NULL','retryable'=>$retryable);
                }
            }else{
                return array('code'=>-1,'msg'=>'QQ钱包免挂通道缺少 qluin 或 p_skey，请重新扫码登录后再测试收款','qr_url'=>'','api_trade_no'=>'NULL');
            }
        }else{
            $qr_url=$QR_row['qr_url'];
        }
    }elseif($QR_row['type']=='qqhpay'){
        require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
        $QqApi = new QqApi();
        $data = $QqApi->qqhbqcode($QR_row,$price,$trade_no);
		$qr_url = 'weixin://app/wxf0a80d0ac2e82aa7/pay/?nonceStr='.$data['nonceStr'].'&package=Sign%3DWXPay&partnerId='.$data['partnerid'].'&prepayId='.$data['prepayId'].'&timeStamp='.$data['ts'].'&sign='.$data['sign'];
		$result['api_trade_no'] = $json['send_listid'];
	}elseif($QR_row['type']=="alipay"){
	    if($QR_row['channel']=='mg_ali' && !empty($QR_row['qr_url'])){
	        $qr_url=$QR_row['qr_url'];
	    }elseif($QR_row['channel']=='mg_alimp'){
	        $qr_url=$QR_row['qr_url'];
	    }else{
	        $userId = getSubstr(base64_decode($QR_row['cookie']), "CLUB_ALIPAY_COM=", ";");
	        if($userId){
	            if($userrow['free']==0){//关闭免输入
	                //$qr_url = 'alipays://platformapi/startapp?appId=09999988&actionType=toAccount&goBack=NO&amount=&userId='.$userId.'&memo=';
		            //$qr_url = 'https://www.alipay.com/?from=pc&appId=09999988&actionType=toAccount&goBack=NO&amount=&userId='.$userId.'&memo=';
		            $qr_url = 'https://ds.alipay.com/?from=pc&appId=20000116&actionType=toAccount&goBack=NO&amount=&userId='.$userId.'&memo=';
	            }elseif($userrow['free']==1){//转账免输模式
	                $qr_url = 'https://www.alipay.com/?from=pc&appId=09999988&actionType=toAccount&goBack=NO&amount='.$price.'&userId='.$userId.'&memo='.$trade_no;//$qr_url = 'alipays://platformapi/startapp?appId=09999988&actionType=toAccount&goBack=NO&amount='.$price.'&userId='.$userId.'&memo='.$trade_no;
			        $qr_url = 'https://ds.alipay.com/?from=pc&appId=20000116&actionType=toAccount&goBack=NO&amount='.$price.'&userId='.$userId.'&memo='.$trade_no;
			    }elseif($userrow['free']==2){//锁死免输入模式
			        //$qr_url = 'alipays://platformapi/startapp?appId=20000123&actionType=scan&biz_data={"s": "money", "u": "'.$userId.'", "a": "'.$price.'", "m": "'.$trade_no.'"}';
			        //$qr_url = 'https://www.alipay.com/?appId=20000123&actionType=scan&biz_data='.urlencode('{"s":"money","u":"'.$userId.'","a":"'.$price.'","m":"'.$trade_no.'"}');
		        $qr_url = 'alipays://platformapi/startapp?appId=20000120&url=https%3A%2F%2Fwww.alipay.com%2F%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%257B%2522s%2522%253A%2522money%2522%252C%2522u%2522%253A%2522'.$userId.'%2522%252C%2522a%2522%253A%2522'.$price.'%2522%252C%2522m%2522%253A%2522'.$trade_no.'%2522%257D';
		       $qr_url = "alipayqr://platformapi/startapp?appId=20000123&actionType=scan&biz_data={%22a%22%3A%22{$price}%22%2C%22s%22%3A%22money%22%2C%22u%22%3A%22{$userId}%22%2C%22m%22%3A%22{$trade_no}%22}";

    			}elseif($userrow['free']==3){//小钱袋免输入模式
    			    $qr_url = 'alipays://platformapi/startapp?appId=2021003172644079&page=pages%2Ftransfer%2Ftransfer%3Famount%3D'.$price.'%26chInfo%3DmoneyBox%26remark%3D'.$trade_no.'%26uid%3D'.$userId;//77700259
    			    //$qr_url = 'alipays://platformapi/startapp?appId=2018100961599704&page=pages%2Ftransfer%2Ftransfer%3Famount%3D'.$price.'%26chInfo%3DmoneyBox%26frontBizNo%3D'.$trade_no.'%26remark%3D'.$trade_no.'%26uid%3D'.$userId;
    			}elseif($userrow['free']==4){//跳转银行卡免输入模式
    			    //$qr_url = 'alipays://platformapi/startapp?appId=60000105&url='.urlencode('https://www.alipay.com/?appId=20000123&actionType=scan&biz_data={"s":"money","u":"'.$userId.'","a":"'.$price.'","m":"'.$trade_no.'"}');
    			    $qr_url = 'alipays://platformapi/startapp?appId=60000105&url=https%3A%2F%2Fwww.alipay.com%2F%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%257B%2522s%2522%253A%2522money%2522%252C%2522u%2522%253A%2522'.$userId.'%2522%252C%2522a%2522%253A%2522'.$price.'%2522%252C%2522m%2522%253A%2522'.$trade_no.'%2522%257D';
    			}elseif($userrow['free']==5){//跳转花呗免输入模式
     			    //$qr_url = 'alipays://platformapi/startapp?appId=20000199&url='.urlencode('https://www.alipay.com/?appId=20000123&actionType=scan&biz_data={"s":"money","u":"'.$userId.'","a":"'.$price.'","m":"'.$trade_no.'"}');
     			    //$qr_url = 'alipays://platformapi/startapp?appId=20000199&actionType=scan&biz_data={"s": "money", "u": "'.$userId.'", "a": "'.$price.'", "m": "'.$trade_no.'"}';
			    $qr_url = 'alipays://platformapi/startapp?appId=20000199&url=https%3A%2F%2Fwww.alipay.com%2F%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%257B%2522s%2522%253A%2522money%2522%252C%2522u%2522%253A%2522'.$userId.'%2522%252C%2522a%2522%253A%2522'.$price.'%2522%252C%2522m%2522%253A%2522'.$trade_no.'%2522%257D';
    			}elseif($userrow['free']==6){//跳转滴滴
    			    $qr_url = 'alipays://platformapi/startapp?appId=20000778&url=https%3A%2F%2Fwww.alipay.com%2F%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%257B%2522s%2522%253A%2522money%2522%252C%2522u%2522%253A%2522'.$userId.'%2522%252C%2522a%2522%253A%2522'.$price.'%2522%252C%2522m%2522%253A%2522'.$trade_no.'%2522%257D';
     			}elseif($userrow['free']==7){//跳转蚂蚁森林
    			    $qr_url = 'alipays://platformapi/startapp?appId=60000002&url=https%3A%2F%2Fwww.alipay.com%2F%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%257B%2522s%2522%253A%2522money%2522%252C%2522u%2522%253A%2522'.$userId.'%2522%252C%2522a%2522%253A%2522'.$price.'%2522%252C%2522m%2522%253A%2522'.$trade_no.'%2522%257D';
    			}
    		}elseif(strstr($res, 'User%2FQRCODE%2F')){
    		    $qr_url=$QR_row['qr_url'];
    		}else{
    		    $qr_url=$QR_row['qr_url'];
    		}
	    }
	}elseif($QR_row['type']=="wxpay"){
	    if($QR_row['channel']=='mg_vzq' or $QR_row['channel']=='yd_vzq'){
	        require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
	        $QqApi = new QqApi();
	        $qr_url = $QqApi->vzqqrcode($QR_row,$price);
	    }elseif($QR_row['hook_type']==2){
	        if($QR_row['channel']=='yd_wx_uos')return ['qr_url'=>rawurlencode($QR_row['qr_url'])];
	        require_once SYSTEM_ROOT.'Mym_Api/Mym.Wx.Api.php';
	        $json = json_decode($QR_row['json'],true);
	        $WxApi = new WxApi($DB->query("SELECT * FROM `pay_yund` WHERE `id` = '{$json['Login_Id']}'")->fetch()['url']);
	        if($QR_row['channel']=='yd_wx_sskd'){
	            $ret = $WxApi->Get_shop_id($QR_row,$price,$trade_no);
	            $result['api_trade_no'] = $ret['trade_no'];
	            $qr_url = "";
	        }elseif($QR_row['channel']=='yd_wx_gskd'){
	            $ret = $WxApi->Get_Qrcode($QR_row,$price,$trade_no);
	            $result['api_trade_no'] = $ret['trade_no'];
	            $qr_url = "";
	        }else{
	            $qr_url = $WxApi->WXTransferSetF2FFee($QR_row['cookie'],$price,$trade_no);
	        }
	        if(!$qr_url){
	            $qr_url=$QR_row['qr_url'];
	        }
	    }else{
	        $qr_url=$QR_row['qr_url'];
	    }
	}elseif(strstr($res, 'User%2FQRCODE%2F')){
	    $qr_url=$QR_row['qr_url'];
	}else{
	    $qr_url=$QR_row['qr_url'];
	}
	if(!$result['api_trade_no'])$result['api_trade_no']='NULL';
	$result['qr_url']=rawurlencode($qr_url);
	return $result;
	
}


function strFilter($str){
    $str = str_replace('￥', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('=', '%3D', $str);
    return trim($str);
}

function Pay_Money_Get($type,$Cookie)
{
    global $conf;
    if($type=='alipay'){
        if($conf['ail_cloud']==0 or $conf['ail_cloud']==1 or !$conf['ail_cloud']){
            $Cookie=base64_decode($Cookie);
            $Url = 'https://shanghu.alipay.com/error.htm?funCode=FUN.USER&causeCode=ERROR.CUSTOMER.NOT.EXIST';
            $ALIPAYJSESSIONID = getSubstr($Cookie,'ALIPAYJSESSIONID=',';');
            $JSESSIONID = getSubstr($Cookie,'JSESSIONID=',';');
            $Cookie = 'JSESSIONID='.$JSESSIONID.'; ALIPAYJSESSIONID='.$ALIPAYJSESSIONID.'; session.cookieNameId=ALIPAYJSESSIONID;';
            $res = mb_convert_encoding(http_Money_Get($Url,$Cookie),'utf-8','GB2312');
            if(!strstr($res, '可用余额')){
                $Url = 'https://lab.alipay.com/user/assets/queryBalance.json?ctoken=';
                $res = http_Money_Get($Url,$Cookie);
                $json = json_decode($res,true);
                if($json['stat']=='deny'){
                    $money  = -1;
                    $status = false;
                }else{
                    $money = $json['availableAmount'];
                    $status = true;
                }
            }else{
                $money = getSubstr($res,'</span><span class="detail-value aside-amount "><em class="aside-available-amount">','</em>元</span></li>');
                $status = true;
            }
        }elseif($conf['ail_cloud']==2){
            $Url = $conf['ail_cloud_api'].'/api.php?act=qrlist&i='.$Cookie.'&t='.time();
            $res = get_curl($Url);
            $json = json_decode($res, true);
            if($json['status']==true){
                $money = $json['money'];
                $status = true;
            }else{
                $money  = -1;
                $status = false;
            }
        }
    }elseif($type=='qqpay' or $type=='vzqpay' or $type=='qqhpay'){
        $Cookie=base64_decode($Cookie);
        $uin = getSubstr($Cookie,"qluin=", ";");
        if(!$uin && strpos($Cookie,"qluin=")!==false){
            $uin = trim(explode("qluin=",$Cookie)[1]);
        }
		$skey = getSubstr($Cookie,"skey=", ";");
		$p_skey = getSubstr($Cookie,"p_skey=", ";");
		if(!$uin || !$p_skey){
			$money  = -1;
			$status = false;
		}else{
			$qq_cookie = 'p_skey='.$p_skey.'; skey='.$skey.'; qluin='.$uin.'; uin=o'.str_pad($uin, 10, '0', STR_PAD_LEFT).';';
			$Url = 'https://myun.tenpay.com/cgi-bin/clientv1.0/qwallet_account_list.cgi?limit=10&offset=0&s_time='.date('Y-m-d',strtotime('-30 days')).'&time_type=0&source_type=7&pay_type=2&ref_param=&skey=&skey_type=2&uin='.$uin;
			$res = get_curl($Url,0,0,$qq_cookie);
			$json = json_decode($res, true);
			if(is_array($json) && isset($json['retcode']) && intval($json['retcode'])===0){
				if(isset($json['records'][0]['balance'])){
					$money = trim(($json['records'][0]['balance']/100));
				}elseif(isset($json['balance'])){
					$money = trim(($json['balance']/100));
				}else{
					$money = '0.00';
				}
				$status = true;
			}else{
				$money  = -1;
				$status = false;
			}
		}
    }else{
        $Url = 'https://apilist.tronscan.org/api/account?address='.$Cookie;
        $res = get_curl($Url);
		$json = json_decode($res, true);
		if($json['trc20token_balances'][0]){
		    $money = (float)round($json['trc20token_balances'][0]['balance']/1000000,2);
		    $status = true;
		}else{
		    $money  = '0.00';
			$status = true;
		}
    }
    return array("status" => $status,"money" => $money,"time" => time(),"cookie" => $Cookie);
}


function mkdirs($dir, $mode = 0777)
{
    if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;

    if (!mkdirs(dirname($dir), $mode)) return FALSE;

    return @mkdir($dir, $mode);
}

function http_Money_Get($url, $cookie, $timeout = 8, $connect_timeout = 3)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	$header = ["User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36", "Referer: https://shanghu.alipay.com/user/myAccount/index.htm"];
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$content = curl_exec($ch);
	curl_close($ch);
	return $content;
}

function usdt($type,$money){
    global $mymapi,$conf;
    if($type!='usdt'){
        return $money;
    }
    if($conf['flpublic']==1){
        $ret = get_curl($mymapi.'api.php?act=usdt');
        //exit($ret);
        $json = json_decode($ret);
        if($json->code==0){
            return $money/$json->money;
        }
    }else{
        $ret = get_curl("https://sp0.baidu.com/5LMDcjW6BwF3otqbppnN2DJv/finance.pae.baidu.com/vapi/async/v1?from_money=%E4%BA%BA%E6%B0%91%E5%B8%81&to_money=%E7%BE%8E%E5%85%83&from_money_num={$money}&srcid=5293&sid=282626_284830_110085_287513_287067_287700_287836_287168_280169_288370_283782_288270_287981_288710_288713_288717_288742_288747_288748_284553_287634_281879_288152_284820_289082_265881_289541_289948_289955_282932_290205_290178_290365_286491_290555_290562_282553_282805_287977_290976_291233_290521_277936_290424_256739_290666_288253_291481_290056_288559_286862_291710_291726_290567_283016_291948_282228_292167_292082_292247_292250_292251_292355_287174_287718_282466_292508_292345_292710_292773_292786_292413_292460_292454_292822_289739&cb=jsonp_1705301850137_11480");
        $json = jsonp_decode($ret,true);
        if($json['ResultCode']==0){
            $money = round($json['Result'][0]['DisplayData']['resultData']['tplData']['money2_num'],2);
        }
    }
    return $money;
}

function AlipayDataBillBalanceQueryRequest($data)
{//支付宝商家账户当前余额查询
    require_once SYSTEM_ROOT.'Mym_Class/Alipay_Class.php';
    $aop = new AopClient();
    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    $aop->appId = $data['appid'];
    $aop->rsaPrivateKey = $data['appkey2'];//开发者私钥
    if(!empty($data['appkey']))$aop->alipayrsaPublicKey=$data['appkey'];//支付宝公钥
    $aop->apiVersion = '1.0';
    $aop->signType = 'RSA2';
    $aop->postCharset='UTF-8';
    $aop->format='json';
    $request = new AlipayDataBillBalanceQueryRequest ();
    $request->setBizContent("");
    try{
        $result = $aop->execute ( $request);
    }catch(Exception $e){
        return json_encode(['code'=>'-1','msg'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
    }
    if(!$result)return json_encode(['code'=>'-1','msg'=>'支付宝接口请求失败'],JSON_UNESCAPED_UNICODE);
    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    //$resultCode = $result->$responseNode->code;
    if(!isset($result->$responseNode))return json_encode(['code'=>'-1','msg'=>'支付宝接口响应异常'],JSON_UNESCAPED_UNICODE);
    return json_encode($result->$responseNode,JSON_UNESCAPED_UNICODE);
}

function AlipayDataBillAccountlogQueryRequest($data)
{//支付宝商家账户账务明细查询
    require_once SYSTEM_ROOT.'Mym_Class/Alipay_Class.php';
    $aop = new AopClient ();
    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    $aop->appId = trim($data['appid']);
    $aop->rsaPrivateKey = trim($data['appkey2']);//开发者私钥
    if(!empty($data['appkey']))$aop->alipayrsaPublicKey=trim($data['appkey']);//支付宝公钥
    $aop->apiVersion = '1.0';
    $aop->signType = 'RSA2';
    $aop->postCharset='UTF-8';
    $aop->format='json';
    $request = new AlipayDataBillAccountlogQueryRequest ();
    
    $Post = ['start_time'=>date("Y-m-d H:i:s",time()-600),'end_time'=>date("Y-m-d H:i:s")];
    $request->setBizContent(json_encode($Post));
    try{
        $result = $aop->execute ( $request);
    }catch(Exception $e){
        return json_encode(['code'=>'-1','msg'=>$e->getMessage()],JSON_UNESCAPED_UNICODE);
    }
    if(!$result)return json_encode(['code'=>'-1','msg'=>'支付宝接口请求失败'],JSON_UNESCAPED_UNICODE);
    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    if(!isset($result->$responseNode))return json_encode(['code'=>'-1','msg'=>'支付宝接口响应异常'],JSON_UNESCAPED_UNICODE);
    return json_encode($result->$responseNode,JSON_UNESCAPED_UNICODE);
}

function AlipayOpenAuthTokenAppRequest(){
    global $DB,$conf;
    require_once SYSTEM_ROOT.'Mym_Class/Alipay_Class.php';
    $data = file_get_contents("php://input");
    $data = json_decode($data,true); 
    if(empty($data)){
        $data = $_REQUEST;
    }
    //echo $_SERVER['HTTP_USER_AGENT'];
    echo $_SERVER['HTTP_REFERER'];
    if(strstr($_SERVER['HTTP_REFERER'], 'a.iizi.cn') or strstr($_SERVER['HTTP_REFERER'], 'auth.iizi.cn') or strstr($_SERVER['HTTP_REFERER'], 'api.iizi.cn')){
        exit(json_encode($data));
    }else{
        sysmsg('创建失败，请返回重试！');
    }
    
    $aop = new AopClient ();
    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    $aop->appId = '2019080866149495';
    $aop->rsaPrivateKey = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDCNxe2vyJpWxnoVEmQySM/6Z+9e6e4HaiY2biy8sSxVOqLxIlVLV8dwnevNWoKgvHoOV5dQwZTKa7SOOjsQAJxoU2tKyfitz1kwNCoFQV1U1LtdjuEidBg7pVnb4exTU+jJPxkeQK3GmAdMWL57F9/AdFKb8SIMRSHawIBA3IQqdMVWypEisRcNyUJBe9MmoDA35cUI93BWLd5TPHWZh1urxNM3Z1UwrTDRCTaeOGIxl6jUHihluepURsZxdRnXHX0M3O6kGxLlHeANu6PaLQBgxXJD3QYl6jsuuNxVpqy++qtNUKggCRY+4RZgpKQYs1f+HTQU25BuSkujDQ4tr/jAgMBAAECggEAJFBk0LjAWG6+U2CfOMFDJAos4iMi4xw4kAv1qxAVkwrLqEKhYxnOtSPNeSdFop2FWeSQLmP/5MDgfVJCyxnU2yZL/dsZ5BxbEGG1Ihh8IsnnsZrv2gJiwh0aNnL2LkLEZz7dKnQt+8qkuhCn7w8xc/AFECQB3W0/52osv7/DLjYfW6F+GCczoiHqJmSJeJpn8OxRrxqJETphB31L1KbKEzOP4inlLAxHXZAskTRdHTR7wAX17dF6Plx+cKTHuqv3cQqYUtecjP9w8IVDLIMORSvvvEsnmJ7858rFiBwBIQokWozXZVDBG9i8G8H57Z8UncheKxAgEv7ELqSlF8bsIQKBgQDoNkkbAwinCENSQlGqih4Y5wuk7um/mRBEma1DrAGEoLOtWMAoJQGpe105CBgo6QRhLJ4RJ2T42le4N0irnu53GV1v6oGpNw5XnkfMCWU7qOVlTvA0QF920uh9mc1NvGD6PwkMMmh3LrsKBafmxcOFMrySveSHEaT2kPoFkxyhmwKBgQDWHFjtljc0Eke3XE/pEMnbz4tGGRsHOUzJ6KRVFj2ust93DFp5SEqv6AV2F6IlRFi4eHUxtUmfQicJnTA6+Dwlm5Oti0kPnNFgL6/Mn7Rxh+jAzyFh8ZG0BwbOklrFZliktNflqWDev4BdJzqcFvqnoZO14NTerr7FKIWHF1VDWQKBgAd4/79DyLp/VJNIGRKw4SkR/ljva0xEI7bhbyb3WREojr+sVHq2PihzFNvp+8UNQpvR8MBCkUhE1n/SH5+OPMROZ6hbVpLYd7iwGkVhpAVYeRFaifZUf/316Y9pLKcswb4r2yGuWZhEQ7ad2fpeMN2PdWwPelQyaCmbHsChLFGzAoGAMII7o5hk6Wc62FARyrwC/8oFw9vsQ7a3rwcGNEDVuL7N4irqVJAMW841bovsMIVLlH+2DY0FIQ/byFHUm4eiGOMmkir8Yo0k4qXrLnEAcLhaA1TqO0Z/vEnbQPHnntGRQ7+1KlM1n0HD01cP8E1EfLYewRYvJERy9Dg6CGFA8AECgYEA1KFSNeQ5FTX46w1jYJUelZYkQJ/afL294iiJElohN63iEcAovcEyi4Qk1Al4rj+/0e0mmVZLN3W4MM/hkP8YI2h5rgVSqe9tIjujPy0QMJB+pHIog1phSLioC46k7Bboh3CqjUNSq/GYC6aNJhiRFARejVH10jgPfDtJVBXG6DE=';
    $aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmLXC2DfakaTQRbmDDavkDWNra16Z7jpfmJP80/9PAojWU8ADyAmtGrWBco4TZ3eL8iipJlOpcef/MNF7E1jIBaefpzCn3ZnUDhLO0vrsxmormZTrMEF5eP2HCQqk2f2OHV8O3iu+vCoTfrpjjqrym88x+rt2ZVlXwTr1aaSnYsfO4DtLnSNgkHv6E0R0/filNsotsOtp02LNFE2tQC6Geuzc00BwoaYxuaUCV5GlP7R5eU+r0SGLaUoHRPZHrmFV7d1A1IfUUrLdftfrCJk5cJUMpPux6tWxrlJ91ahlZ5AQFzH9xAymeru8fv+qAVV+Lm7z/SZEx1K433YyvW5Q9QIDAQAB';
    $aop->apiVersion = '1.0';
    $aop->signType = 'RSA2';
    $aop->postCharset='UTF-8';
    $aop->format='json';
    $request = new AlipayOpenAuthTokenAppRequest ();
    
    $POST = json_encode(['grant_type'=>'authorization_code','code'=>$data['app_auth_code']]);
    $request->setBizContent($POST);
    $result = $aop->execute ( $request); 
    
    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    $resultCode = $result->$responseNode->code;
    exit(json_encode($result->$responseNode));
}

function usdt_Order($address){
    $ret = get_curl('https://apilist.tronscan.org/api/contract/events?address='.$address.'&start=0&limit=50');
    $json = json_decode($ret,true);
    foreach ($json['data'] as $la){
        if($la['transferToAddress'] == $address and $la['amount']>1000000){
            $result[] = [
                'addtime'=> date("Y-m-d H:i:s",$la['timestamp'] / 1000),
                'time' => $la['timestamp'] / 1000,
                'money' => round($la['amount']/1000000,2),
                't_url' => $la['transferFromAddress']
            ];
        }
    }
    return $result;
}

function Email_Msg($email,$srow)
{
    global $DB,$conf,$date;
    if($srow['type']=='alipay'){
		$E_type = '支付宝';
	}elseif($srow['type']=='qqpay'){
		$E_type = 'QQ钱包';
	}else{
		$E_type = '微信';
	}
    $api_url = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST']."/";
    $msg = '尊敬的用户 PID:'.$srow['pid'].'你好<br/><br/>您本次收收款金额为'.$srow['price'].'元<br/><br/>于'.$date.'收款到账<br/><br/>类型:'.$E_type.'<br/><br/>商品名称:'.$srow['name'].'<br/><br/>商品订单:'.$srow['trade_no'].'<br/><br/>地址:'.$api_url.'<br/>有问题请联系站长QQ'.$conf['qq'];
    send_mail($email, $conf['sitename'].'- 收款到账提醒', $msg);
}



function getAliOrder($cookie)
{
    $Cookie=base64_decode($cookie);
    $startDateInput=rawurlencode(date("Y-m-d H:i:s", time()-(60*20)));//获取10分钟之内的订单
    $startDateInput=rawurlencode(date("Y-m-d H:i:s", time()-86400-86400));//获取10分钟之内的订单
    $endDateInput= rawurlencode(date("Y-m-d H:i:s",strtotime('now')));
    $ctoken = getSubstr($Cookie,"ctoken=", ";");
    $pid = getSubstr($Cookie, "CLUB_ALIPAY_COM=", ";");
    $str='endDateInput='.$endDateInput.'0&precisionQueryKey=tradeNo&precisionQueryValue=&showType=1&startDateInput='.$startDateInput.'&billUserId='.$pid.'&pageNum=1&pageSize=100&startTime='.$startDateInput.'&endTime='.$endDateInput.'&status=1&queryEntrance=1&sortTarget=tradeTime&activeTargetSearchItem=tradeNo&accountType=&sortType=0&startAmount&endAmount&targetMainAccount&precisionValue&goodsTitle&total=0&_input_charset=gbk';
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://mbillexprod.alipay.com/enterprise/fundAccountDetail.json?ctoken='.$ctoken,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_CONNECTTIMEOUT => 3,
      CURLOPT_TIMEOUT => 6,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>$str,
      CURLOPT_HTTPHEADER => array(
          'authority: mbillexprod.alipay.com',
          'sec-ch-ua: "Google Chrome";v="93", " Not;A Brand";v="99", "Chromium";v="93"',
          'accept: application/json, text/javascript, */*; q=0.01',
          'content-type: application/x-www-form-urlencoded; charset=UTF-8',
          'x-requested-with: XMLHttpRequest',
          'sec-ch-ua-mobile: ?0',
          'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Mobile Safari/537.36',
          'origin: https://mbillexprod.alipay.com',
          'sec-fetch-site: same-origin',
          'sec-fetch-mode: cors',
          'sec-fetch-dest: empty',
          'referer: https://business.alipay.com/user/mbillexprod/account/detail',
          'accept-language: zh-CN,zh;q=0.9,en;q=0.8',
          'cookie: '.base64_decode($cookie)
          ),
          ));
    $response = curl_exec($curl);
    curl_close($curl);
    //$res=$response
    //return empty($res)?json_decode(mb_convert_encoding($response,'UTF-8','GBK'),true):$res;
    return json_decode(mb_convert_encoding($response,'UTF-8','GBK'),true);
}

function Facetofaceredenvelope($url,$id){//QQ面对面红包领取协议
    global $DB,$conf;
    require_once SYSTEM_ROOT.'Mym_Api/Mym.Qq.Api.php';
    $QqApi = new QqApi();
    $QR_row=$DB->query("SELECT * FROM pay_qrlist WHERE id='{$id}' limit 1")->fetch();
    $uin = explode("qluin=",$QR_row['cookie'])[1];
    $p_skey = "p_skey=".getSubstr($QR_row['cookie'],"p_skey=", ";").";";
    $url = explode("mqq.tenpay.com/qrhb?c=",$url)[1];
    $data = 'pskey='.$p_skey.'&agreement=0&channel=2048&skey_type=2&name=%E3%80%80&skey=v09dfabcd1c6391bdc19db0e14a7be64&qr_data='.urlencode($url).'&uin='.$uin.'&h_net_type=WIFI&h_model=android_mqq&h_edition=63&h_location=26444B211E59C73668D1BEA9DAE72DD5%7C%7CNOH-NX9%7C10%2Csdk29%7CCFF2B3F0900E22E4CF26588E58265BB5%7CD41D8CD98F00B204E9800998ECF8427E%7C0%7C&h_qq_guid=1279440B9948F0F31AEEC7BE02852EE3&h_qq_appid=537143922&h_exten=';
    $data = $QqApi->etaencode($data);
    $data = 'ver=2.0&chv=3&req_text='.$data.'&msgno='.$uin.''.date("Ymd").time().'&skey=&skey_type=2&random=0';
    $url = 'https://mqq.tenpay.com/cgi-bin/hongbao/qpay_hb_qr_grab.cgi';
    $ret = get_curl($url,$data);
    $ret = $QqApi->etadecode($ret);
    return $ret;
}

function Pay_Qqhpay_Order($row)
{
    global $conf;
    $Cookie=base64_decode($row['cookie']);
    $uin = explode("qluin=",$Cookie);
	$skey = getSubstr($Cookie,"skey=", ";");
	$p_skey = "p_skey".getSubstr($Cookie,"p_skey", ";").";";
	$Url = 'https://myun.tenpay.com/cgi-bin/clientv1.0/qwallet_record_list.cgi?limit=15&offset=0&s_time=2022-06-26&ref_param=&source_type=7&time_type=0&bill_type=0&uin='.$uin[1];
	$res = get_curl($Url,0,0,$p_skey);
	$res = json_decode($res,true);
    return $res;
}

function jsondet($data,$add=false,$name=false,$det=null){
    foreach ($data as $key=>$value)
    {
        if($row[$key]==$name and $name!=false){
            $row[$key] = daddslashes($det);
        }else{
            $row[$key] = daddslashes($value);
        }
    }
    if($add){
        foreach ($add as $key=>$value)
        {
            $row[$key] = daddslashes($value);
        }
    }
    return json_encode($row, JSON_UNESCAPED_UNICODE);
}

function paymali($row,$email,$zf){
    global $conf,$date;
    $msg = $conf['paymali'];
    $api_url = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST']."/";
	if(strpos($msg,'[pid]')!==false){
		$msg = str_replace('[pid]', $row['pid'], $msg);
	}
	if(strpos($msg,'[money]')!==false){
		$msg = str_replace('[money]', $row['price'], $msg);
	}
	if(strpos($msg,'[qq]')!==false){
		$msg = str_replace('[qq]', $conf['qq'], $msg);
	}
	if(strpos($msg,'[date]')!==false){
	    if(!$row['endtime']){
	        $row['endtime']=$date;
	    }
		$msg = str_replace('[date]', $row['endtime'], $msg);
	}
	if(strpos($msg,'[url]')!==false){
		$msg = str_replace('[url]', $api_url, $msg);
	}
	if(strpos($msg,'[trade_no]')!==false){
		$msg = str_replace('[trade_no]', $row['trade_no'], $msg);
	}
	if(strpos($msg,'[name]')!==false){
		$msg = str_replace('[name]', $row['name'], $msg);
	}
	if(strpos($msg,'[type]')!==false){
		$msg = str_replace('[type]', $zf, $msg);
	}
	send_mail($email, $conf['sitename'].'- 收款到账提醒', $msg);
	return;
}

// 开源版移除原作者硬编码到期拦截。
// 原逻辑：time() > 1735653011 时直接 sysmsg() 阻断全站访问。
// 1735653011 对应 2025-01-01 左右，当前时间超过后会显示“版本过于老旧”。
if (false) {
    sysmsg('', true);
}