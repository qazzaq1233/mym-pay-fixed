<?php
class QqApi {
    protected $Api_Url 	= null;
    function __construct($Api_Url = 'NULL')
    {
        $count = substr_count($Api_Url,"/");
        if($count==2){
            $this->Api_Url = $Api_Url.'/';
        }else{
            $this->Api_Url = $Api_Url;
        }
    }

function qqPcActionUrls($action){
    $base = rtrim($this->Api_Url,'/');
    if(!$base || $base == 'NULL')return array();
    $urls = array();
    if(preg_match('/\/QQ_Pc$/i',$base)){
        $urls[] = $base.'/'.$action;
    }else{
        $urls[] = $base.'/QQ_Pc/'.$action;
        $urls[] = $base.'/index.php/QQ_Pc/'.$action;
        $urls[] = $base.'/api/QQ_Pc/'.$action;
        $urls[] = $base.'/Api/QQ_Pc/'.$action;
    }
    return array_values(array_unique($urls));
}

function qqPcHttpPost($url,$params=array()){
    $post = is_array($params) ? http_build_query($params) : $params;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/json, text/plain, */*",
        "Content-Type: application/x-www-form-urlencoded",
        "Accept-Language: zh-CN,zh;q=0.8",
        "Connection: close"
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36");
    $body = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array('body'=>$body,'errno'=>$errno,'error'=>$error,'http_code'=>$http_code,'url'=>$url);
}

function qqPcRequest($action,$params=array()){
    $urls = $this->qqPcActionUrls($action);
    if(!$urls)return array('code'=>-1,'msg'=>'QQ云端地址为空','_request_error'=>true);
    $last = null;
    foreach($urls as $url){
        $res = $this->qqPcHttpPost($url,$params);
        $last = $res;
        if($res['errno']){
            continue;
        }
        $body = trim((string)$res['body']);
        $json = json_decode($body,true);
        if(is_array($json)){
            $json['_request_url'] = $url;
            $json['_http_code'] = $res['http_code'];
            return $json;
        }
        if($res['http_code'] == 404){
            continue;
        }
        $summary = mb_substr(strip_tags($body),0,120,'UTF-8');
        return array('code'=>-1,'msg'=>'QQ云端接口未返回JSON：HTTP '.$res['http_code'].'，请求 '.$url.($summary?'，响应：'.$summary:''),'_request_error'=>true,'_request_url'=>$url,'_http_code'=>$res['http_code']);
    }
    $msg = '当前云端地址未找到 QQ_Pc 接口，请确认后台云端地址是否为 QQ_Pc 接口根地址';
    if($last){
        if($last['errno']){
            $msg .= '，最后错误：'.$last['error'];
        }else{
            $msg .= '，最后请求：'.$last['url'].'，HTTP '.$last['http_code'];
        }
    }
    return array('code'=>-1,'msg'=>$msg,'_request_error'=>true,'_request_url'=>$last?$last['url']:'','_http_code'=>$last?$last['http_code']:0);
}

function formatQqPcError($data,$default='云端接口返回异常'){
    if(!is_array($data))return $default;
    if(isset($data['msg']) && $data['msg'])return $data['msg'];
    if(isset($data['message']) && $data['message'])return $data['message'];
    if(isset($data['error']) && $data['error'])return $data['error'];
    return $default;
}

function normalizeQqPcQrUrl($qr_url){
    $qr_url = trim((string)$qr_url);
    if(!$qr_url)return '';
    if(preg_match('/^(https?:\/\/|data:image\/|\/)/i',$qr_url))return $qr_url;
    if(preg_match('/^[A-Za-z0-9+\/]+=*$/',$qr_url) && strlen($qr_url)>100){
        return 'data:image/png;base64,'.$qr_url;
    }
    $base = rtrim($this->Api_Url,'/');
    if($base && strpos($qr_url,'/')!==false)return $base.'/'.ltrim($qr_url,'/');
    return $qr_url;
}

function LoginStatus($uid){
    $ret = $this->qqPcRequest('IsLoginStatus',array('uid'=>$uid));
    if(!is_array($ret) || !isset($ret['code']))return null;
    if(isset($ret['_request_error']) && $ret['_request_error']){
        return array('code'=>-1,'msg'=>$this->formatQqPcError($ret,'QQ_Pc云端状态检测失败'),'id'=>$uid,'cookie'=>'','raw'=>$ret);
    }
    if((string)$ret['code'] === '1'){
        return array('code'=>200,'msg'=>'登录成功','id'=>$uid,'cookie'=>$uid,'raw'=>$ret);
    }elseif((string)$ret['code'] === '0'){
        return array('code'=>1,'msg'=>'请扫描二维码登录','id'=>$uid,'cookie'=>'','raw'=>$ret);
    }elseif((string)$ret['code'] === '-1'){
        return array('code'=>-1,'msg'=>'uid参数错误','id'=>$uid,'cookie'=>'','raw'=>$ret);
    }elseif((string)$ret['code'] === '-2'){
        return array('code'=>-1,'msg'=>'没有找到Uid','id'=>$uid,'cookie'=>'','raw'=>$ret);
    }
    return array('code'=>-1,'msg'=>$this->formatQqPcError($ret,'云端登录状态异常'),'id'=>$uid,'cookie'=>'','raw'=>$ret);
}

function Add_QQ($qq,$type=9,$site='',$pid='',$key='',$token='jiaowoliangzai'){
    // 新版 QQ_Pc 云端协议：/QQ_Pc/CreateID -> /QQ_Pc/QRCode
    // 参考插件 yun_qqpay_lz 会把站点、商户 PID、商户 KEY 和 token 打包成 base64 JSON 传给云端。
    if($site && $pid && $key){
        $payload = base64_encode(json_encode(array(
            'site'=>$site,
            'pid'=>$pid,
            'key'=>$key,
            'token'=>$token
        )));
        $create = $this->qqPcRequest('CreateID',array('data'=>$payload));
        if(is_array($create) && isset($create['code'])){
            if((string)$create['code'] !== '1'){
                return array('code'=>-1,'msg'=>$this->formatQqPcError($create,'创建QQ云端客户端失败'),'id'=>'','qr_url'=>'','raw'=>$create);
            }
            $uid = isset($create['uid']) && $create['uid'] ? $create['uid'] : (isset($create['id']) && $create['id'] ? $create['id'] : '');
            if(!$uid){
                return array('code'=>-1,'msg'=>'创建QQ云端客户端失败：云端未返回uid','id'=>'','qr_url'=>'','raw'=>$create);
            }
            $qrcode = $this->qqPcRequest('QRCode',array('uid'=>$uid));
            if(!is_array($qrcode) || !isset($qrcode['code'])){
                return array('code'=>-1,'msg'=>'获取QQ登录二维码失败：云端响应异常','id'=>$uid,'qr_url'=>'','raw'=>$qrcode);
            }
            if((string)$qrcode['code'] !== '1'){
                return array('code'=>-1,'msg'=>$this->formatQqPcError($qrcode,'获取QQ登录二维码失败'),'id'=>$uid,'qr_url'=>'','raw'=>$qrcode);
            }
            $qr_url = '';
            foreach(array('url','qr_url','qrcode','qrCode','qr','data') as $field){
                if(isset($qrcode[$field]) && is_string($qrcode[$field]) && $qrcode[$field]){
                    $qr_url = $qrcode[$field];
                    break;
                }
            }
            $qr_url = $this->normalizeQqPcQrUrl($qr_url);
            if(!$qr_url){
                return array('code'=>-1,'msg'=>'获取QQ登录二维码失败：云端未返回二维码图片地址','id'=>$uid,'qr_url'=>'','raw'=>$qrcode);
            }
            return array('code'=>1,'msg'=>'获取登录二维码成功','id'=>$uid,'uid'=>$uid,'qr_url'=>$qr_url,'raw'=>$qrcode);
        }
    }

    // 兼容旧版 MYM 云端协议：/Api/Add_QQ
    // 协议类型，1=安卓QQ，2=手表QQ，3=aPadQQ，4=企业QQ，5=企点QQ，6=TIMQQ，7=iPadQQ，8=iPhoneQQ，9=MacQQ，企点和企业协议时，密码必须填写
    $url = $this->Api_Url."Api/Add_QQ?id={$qq}&type=".rand(10,16);
    $ret = json_decode(get_curl($url),true);
    if(!is_array($ret)){
        return array('code'=>-1,'msg'=>'旧版QQ云端接口响应异常，请检查云端地址：'.$url,'id'=>'','qr_url'=>'');
    }
    return $ret;
}

function Add_Qr($id,$beizhu){
    // 新版 QQ_Pc 云端协议只需要轮询登录状态，登录成功后把 uid 作为本地 cookie 标识保存。
    $status = $this->LoginStatus($id);
    if(is_array($status))return $status;

    // 兼容旧版 MYM 云端协议：/Api/Add_Qr
    $url = $this->Api_Url.'Api/Add_Qr?id='.$id.'&qq='.$beizhu;
    $ret = json_decode(get_curl($url),true);
    return $ret;
}
function Add_Cookie($qq){
    $url = $this->Api_Url.'Api/Add_Cookie?qq='.$qq;
    $ret = json_decode(get_curl($url),true);
    return $ret;
}

function Add_Yun_Qrcode($cookie,$money,$beizhu,$options=array()){
    if(!is_array($options))$options = array();
    $cookie = trim((string)$cookie);
    $p_skey = getSubstr($cookie,"p_skey=", ";");
    $qq = getSubstr($cookie,"qluin=", ";");
    if(!$qq && strpos($cookie,'qluin=')!==false){
        $qq = trim(explode('qluin=',$cookie)[1]);
        if(strpos($qq,';')!==false)$qq = trim(explode(';',$qq)[0]);
    }
    if(!$p_skey || !$qq){
        return array('retcode'=>-1,'retmsg'=>'QQ钱包cookie缺少p_skey或qluin，无法生成金额码');
    }
    $url = 'https://mqq.tenpay.com/cgi-bin/qr_code/qr_code_generate.cgi?ver=2.0&chv=3';
    $beizhu = $beizhu?$beizhu:'Mym Pay H5';
    $fee = intval(round(floatval($money) * 100));
    if($fee <= 0)return array('retcode'=>-1,'retmsg'=>'金额不正确，无法生成QQ金额码');
    $data = $this->etaencode('pskey='.$p_skey.'&extend=explain%3D'.rawurlencode($beizhu).'&skey_type=2&trans_fee='.$fee.'&skey=&uin='.$qq.'&type=1&h_net_type=WIFI&h_model=android_mqq&h_edition=95&h_location=68C327592F243273E28B4A871C9B5C46%7C%7CMI%209%7C11%2Csdk30%7C1%7C&h_qq_guid=C63839CDC59839396E48EB4F7E1D8682&h_qq_appid=537124039&h_exten=');
    $data = 'req_text='.$data.'&msgno='.$qq.date("Ymd").time().'&skey=&skey_type=2&random=0';
    $ret = get_curl($url,$data);
    if($ret === false || trim((string)$ret)===''){
        return array('retcode'=>-1,'retmsg'=>'QQ金额码接口请求超时或无响应','retryable'=>1);
    }
    $decoded = $this->etadecode($ret);
    $json = json_decode(trim($decoded),true);
    if(!is_array($json))return array('retcode'=>-1,'retmsg'=>'QQ金额码接口响应异常','retryable'=>1);
    if(isset($json['retcode']) && intval($json['retcode'])!=0 && empty($json['retmsg']))$json['retmsg']='QQ金额码接口返回错误：'.$json['retcode'];
    return $json;
}

function vzqqrcode($row,$price){
    $data = $this->qqwallet($row);
    if($data['code']==-1){
        exit(sysmsg($data['msg'].'No.1'));
    }else{
        $qbskey = $data['skey'];
    }
    $price=$price+$data['money'];
    return $this->qrcode_ad($row,$price,$qbskey);
    
}

function qrcode_ad($row,$price,$qbskey){
    $Cookie = base64_decode($row['cookie']);
    $uin = getSubstr($Cookie,"qluin=", ";");
    if(!$uin && strpos($Cookie,"qluin=")!==false){
        $uin = trim(explode("qluin=",$Cookie)[1]);
        if(strpos($uin,';')!==false)$uin = trim(explode(';',$uin)[0]);
    }
	$p_skey = getSubstr($Cookie,"p_skey=", ";");
	$skey = getSubstr($Cookie,"skey=", ";");
	$payee_uin = $row['beizhu'] ? preg_replace('/\D/','',$row['beizhu']) : '';
	if(!$payee_uin)$payee_uin = $uin;
	if(!$uin || !$p_skey || !$qbskey || !$payee_uin){
	    return '';
	}
	$url = 'https://mqq.tenpay.com/cgi-bin/qwallet_app/qpayment_transaction.cgi?ver=2.0&chv=3';
	$fee = intval(round(floatval($price) * 100));
	$data = 'pskey='.$p_skey.'&payee_nick=&come_from=2&payee_uin='.$payee_uin.'&memo='.rawurlencode($row['beizhu']?$row['beizhu']:'Mym Pay').'&source=3&skey_type=2&total_fee='.$fee.'&skey='.$skey.'&uin='.$uin.'&h_net_type=WIFI&h_model=android_mqq&h_edition=86&h_location=CDC7BC237126988EBB4A1F7D53E73429%7C%7CM2012K11C%7C12%2Csdk31%7C1C1612E2C3EF11331C853585BE44D97F%7CD41D8CD98F00B204E9800998ECF8427E%7C0%7C&h_qq_guid=1C1612E2C3EF11331C853585BE44D97F&h_qq_appid=537101852&h_exten=';
	$post = 'req_text='.$this->etaencode($data).'&msgno='.$uin.date("Ymd").time().'&skey=&skey_type=2&random=0';
	$ret = get_curl($url,$post);
	$json = json_decode($this->etadecode($ret),true);
	if(!is_array($json) || intval($json['retcode'])!=0){
	    return '';
	}else{
	    $token_id = $json['token_id'];
	    $url = 'https://myun.tenpay.com/cgi-bin/clientv1.0/qpay_gate.cgi?ver=1.0&chv=3';
        $data = 'pskey='.$p_skey.'&pskey_scene=client&skey_type=2&come_from=2&token_id='.$token_id.'&skey='.$qbskey.'&uin='.$uin.'&model_xml=<deviceinfo><MANUFACTURER name="Xiaomi"><MODEL name="M2012K11C"><VERSION_RELEASE name="12"><VERSION_INCREMENTAL name="V13.0.13.0.SKKCNXM"><DISPLAY name="SKQ1.211006.001 test-keys"></DISPLAY></VERSION_INCREMENTAL></VERSION_RELEASE></MODEL></MANUFACTURER></deviceinfo>&device_id=19944ff4cbed4244&h_net_type=WIFI&h_model=android_mqq&h_edition=86&h_location=CDC7BC237126988EBB4A1F7D53E73429%7C%7CM2012K11C%7C12%2Csdk31%7C1C1612E2C3EF11331C853585BE44D97F%7CD41D8CD98F00B204E9800998ECF8427E%7C0%7C&h_qq_guid=1C1612E2C3EF11331C853585BE44D97F&h_qq_appid=537101852&h_exten=';
        $post = 'req_text='.$this->etaencode($data).'&skey_type=2&msgno='.$uin.date("Ymd").time().'&skey=&random=0';
        $ret = $this->etadecode(get_curl($url,$post));
        $json = json_decode($ret,true);
        if(!is_array($json) || intval($json['retcode'])!=0){
            return '';
        }
        $sdk_url = $json['balance_info']['miniapp']['url'];
        if(!$sdk_url || strpos($sdk_url,"path=")===false)return '';
        if(get_device_type()=='ios'){
            return urlencode(urlencode(explode("path=",$sdk_url)[1]));
        }else{
            return explode("path=",$sdk_url)[1];
        }
	}
}

function qrcode_ios($row,$price,$qbskey){
    $Cookie = base64_decode($row['cookie']);
    $uin = explode("qluin=",$Cookie)[1];
    $skey = getSubstr($Cookie,"skey=", ";");
    $p_skey = getSubstr($Cookie,"p_skey=", ";");
    $url = 'https://myun.tenpay.com/cgi-bin/clientv1.0/wal_bank_query.cgi';
    $post = 'chv=3&token_id=&h_edition=106&msgno='.$uin.date("Ymd").time().'&h_model=ios_iphone_mqq&bargainor_id=0&pay_type=YDT|FASTPAY&skey='.$qbskey.'&query_type=QPAY_CHARGE&h_qq_guid=85BF8FD1DEB4F3622FAF2A8E2577B571&h_net_type=WIFI&h_qq_appid=537151352&ver=2.0&h_location=82A34DF6-3C92-4A3B-B01B-F8A2D8C63FE5||iPhone X|15.6|||0&h_pkg_name=com.tencent.mqq&user_info=1&unbind_flag=0&pskey='.$p_skey.'&uin='.$uin.'&skey_type=0';
    $res = get_curl('https://myun.tenpay.com/cgi-bin/clientv1.0/wal_bank_query.cgi',$post);
    $json = json_decode($res,true);
    return $json['miniapp']['sdk_url'];
}

function qqwallet($row){
    $Cookie = base64_decode($row['cookie']);
    $uin = explode("qluin=",$Cookie)[1];
    $skey = getSubstr($Cookie,"skey=", ";");
    $p_skey = getSubstr($Cookie,"p_skey=", ";");
    $url = 'https://myun.tenpay.com/cgi-bin/clientv1.0/qwallet.cgi?ver=2.0&chv=3';
    $data = 'pskey='.$p_skey.'&pskey_scene=client&skey=&skey_type=2&app_info=appid%230%7Cbargainor_id%230%7Cchannel%23wallet&uin='.$uin.'&need_suggest=1&h_net_type=WIFI&h_model=android_mqq&h_edition=86&h_location=CDC7BC237126988EBB4A1F7D53E73429%7C%7CM2012K11C%7C12%2Csdk31%7C1C1612E2C3EF11331C853585BE44D97F%7CD41D8CD98F00B204E9800998ECF8427E%7C0%7C&h_qq_guid=1C1612E2C3EF11331C853585BE44D97F&h_qq_appid=537101852&h_exten=';
    $post = 'req_text='.$this->etaencode($data).'&skey_type=2&msgno='.$uin.date("Ymd").time().'&skey=';
    $ret = get_curl($url,$post);
    
    $ret = $this->etadecode($ret);
    
    $json = json_decode($ret,true);
    
    if($json['retcode']==0 and $json['retmsg']=='ok'){
        $money = $json['balance']/100;
        return ['code'=>1,'money'=>$money,'skey'=>$json['skey'],'name'=>$json['purchaser_true_name'],'qq'=>$json['purchaser_id']];
    }else{
        return ['code'=>-1,'msg'=>'Url:1 '.$json['retmsg']];
    }
}

function qqhbqcode($QR_row,$price,$trade_no){
    $Cookie=base64_decode($QR_row['cookie']);
    $uin = explode("qluin=",$Cookie)[1];
	$p_skey = getSubstr($Cookie,"p_skey=", ";");
	$money = $price*100;

	$json = json_decode(trim($ret),true);
	$data = 'pskey='.$p_skey.'&subchannel=0&hb_from_type=0&skin_id=0&bus_type=1&skin_from=0&channel=1&type=1&wishing=%E5%A4%A7%E5%90%89%E5%A4%A7%E5%88%A9&skey_type=2&total_amount='.$money.'&recv_type=1&total_num=1&recv_uin='.$QR_row['qqh'].'&name=yun%E6%99%95&skey=&uin='.$uin.'&h_net_type=WIFI&h_model=android_mqq&h_edition=95&h_location=4ED972BE5165260AFF6DD5CB74A047DE%7C%7CNOH-NX9%7C10%2Csdk29%7C0%7C&h_qq_guid=F8D8C010BBEE662EC476CD1262714691&h_qq_appid=537145523&h_exten=';//发送红包到的QQ
	$data = $this->etaencode($data);
	$data = 'req_text='.$data.'&msgno='.$uin.''.date("Ymd").time().'&skey=&skey_type=2&random=0';
	$ret = get_curl('https://mqq.tenpay.com/cgi-bin/hongbao/qpay_hb_pack.cgi?ver=2.0&chv=3',$data);
	$ret = $this->etadecode($ret);
		
	$json = json_decode(trim($ret),true);
		
	$data = 'pskey='.$p_skey.'&skey_type=2&come_from=2&token_id='.$json['token_id'].'&wxpay_auth='.urlencode($json['pay_channel'][1]['info']).'&uin='.$uin.'&h_net_type=WIFI&h_model=android_mqq&h_edition=95&h_location=4ED972BE5165260AFF6DD5CB74A047DE%7C%7CNOH-NX9%7C10%2Csdk29%7C0%7C&h_qq_guid=F8D8C010BBEE662EC476CD1262714691&h_qq_appid=537145523&h_exten=';
	$data = $this->etaencode($data);
	$data = 'req_text='.$data.'&msgno='.$uin.''.date("Ymd").time().'&skey=&skey_type=2&random=0';
	$ret = get_curl('https://mqq.tenpay.com/cgi-bin/hongbao/qpay_hb_wxpack.cgi?ver=2.0&chv=3',$data);
	$ret = $this->etadecode($ret);
	
	$json = json_decode(trim($ret),true);
	$prepayid = getSubstr($ret,'prepayid=','&sign');
	$nonceStr = getSubstr($json['wxpay_sdk'],'noncestr=','&package');
	$partnerid = getSubstr($ret,'partnerid=','&prepayid');
	$prepayId = getSubstr($ret,'prepayid=','&sign');
	$ts = getSubstr($ret,'&ts=','","');
	$sign = getSubstr($ret,'&sign=','&token_id');
	return ['nonceStr'=>$nonceStr,'partnerid'=>$partnerid,'prepayId'=>$prepayId,'ts'=>$ts,'sign'=>$sign];
}

function etaencode($data){
    $privatekey = '9973e345';
    if(strlen($data) %16){
        $data = str_pad($data,strlen($data) + 16 - strlen($data) % 16, '\0');
    }
    $etaencode = openssl_encrypt($data,'DES-ECB',$privatekey,OPENSSL_NO_PADDING);
    return strtoupper(bin2hex($etaencode));
}

function etadecode($string){
    $privatekey = '9973e345';
    $etadata = hex2bin(trim($string));
    $etadecode = openssl_decrypt($etadata,'DES-ECB',$privatekey,OPENSSL_NO_PADDING);
    return trim($etadecode);
}

function get_curl($url, $post=0, $referer=0, $cookie=0, $header=0, $ua=0, $nobaody=0, $addheader=0)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	$httpheader[] = "Accept: application/json";
	$httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
	$httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
	$httpheader[] = "Connection: close";
	if($addheader){
		$httpheader = array_merge($httpheader, $addheader);
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
	if ($post) {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	if ($header) {
		curl_setopt($ch, CURLOPT_HEADER, true);
	}
	if ($cookie) {
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
	}
	if($referer){
		if($referer==1){
			curl_setopt($ch, CURLOPT_REFERER, 'https://h5.qzone.qq.com/mqzone/index');
		}else{
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
	}
	if ($ua) {
		curl_setopt($ch, CURLOPT_USERAGENT, $ua);
	}
	else {
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
	}
	if ($nobaody) {
		curl_setopt($ch, CURLOPT_NOBODY, 1);
	}
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
}