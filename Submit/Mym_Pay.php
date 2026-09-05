<?php
if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    die('require PHP > 5.6 !');
}
include("../Mym/Common.php");
$act = daddslashes($_GET['act']);
$trade_no=daddslashes($_GET['trade_no']);
$sitename=base64_decode(daddslashes($_GET['sitename']));
$srow=$DB->query("SELECT * FROM pay_order WHERE trade_no='{$trade_no}' limit 1")->fetch();
if(!$srow)sysmsg('该订单号不存在，请返回来源地重新发起请求！');
$userrow=$DB->query("SELECT * FROM pay_user WHERE pid='{$srow['pid']}' limit 1")->fetch();
$QR_row=$DB->query("SELECT * FROM pay_qrlist WHERE id='{$srow['qr_id']}' limit 1")->fetch();
$QR_json=json_decode($QR_row ? $QR_row['json'] : '',true);
if(!is_array($QR_json))$QR_json=array();
$receiver_surname=isset($QR_json['receiver_surname']) ? mym_restore_unicode_text($QR_json['receiver_surname']) : '';
$receiver_surname_html=htmlspecialchars($receiver_surname);
$outtime = $srow['outtime']-time();
$price   = $srow['price'];
$type    = $srow['type'];
$device = getDevice()['device'];
if($type == 'wxpay'){
	$typeName = '微信';
}elseif($type == 'qqpay'){
	$typeName = 'QQ钱包';
}elseif($type == 'alipay'){
	$typeName = '支付宝';
}else{
    $typeName = 'USDT';
}
if(($device!='mobile' and $device!='pc') and $userrow['pay_tzqq']==1 and $type=='qqpay'){
    include './Pay/tz.php';exit;
}elseif(($device!='mobile' and $device!='pc') and $userrow['pay_tzali']==1 and $type=='alipay'){
    include './Pay/tz.php';exit;
}elseif(($device!='mobile' and $device!='pc') and $userrow['pay_tzwx']==1 and $type=='wxpay'){
    include './Pay/tz.php';exit;
}
$mp = rand(1,3);
if($mp==1){
    $mp3 = "//dict.youdao.com/dictvoice?audio=尊敬的客户，你好，你本次订单金额为".$srow['price']."元，请按照金额进行付款，否则会导致支付失败&le=zh";
}elseif($mp==2){
    $mp3= "//dict.youdao.com/dictvoice?audio=尊敬的用户，你本次交易金额为".$srow['price']."元记得不要付错了哟&le=zh&keyfrom=speaker-target";
}else{
    $mp3= "//dict.youdao.com/dictvoice?audio=请注意查看付款金额避免支付失败&le=zh&keyfrom=speaker-target";
}

if($act=='dmf'){
    require_once("../Mym/Mym_Class/Alipay_Class.php");
    $f2fid           = $Dmf_row['f2fid'];		//应用的APP
    $f2fkey          = $Dmf_row['f2fkey'];      //支付宝公钥
    $f2fpubli        = $Dmf_row['f2fpublic'];	//商户私钥
    $aop = new AopClient ();
    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    $aop->appId = $f2fid;
    $aop->rsaPrivateKey = $f2fpubli;
    $aop->alipayrsaPublicKey=$f2fkey;
    $aop->apiVersion = '1.0';
    $aop->signType = 'RSA2';
    $aop->postCharset='GBK';
    $aop->format='json';
    
    $object = new stdClass();
    $object->out_trade_no = $trade_no;
    $object->total_amount = $srow['money'];
    $object->subject = $srow['name'];
    
    $json = json_encode($object);
    
    $request = new AlipayTradePrecreateRequest();
    $request->setNotifyUrl(($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/Submit/F2f_notify.php');
    $request->setBizContent($json);
    exit(json_encode($request));
    $result = $aop->execute ( $request);/* 
    
    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    $resultCode = $result->$responseNode->code;
    exit(json_encode($result->$responseNode));
    if(!empty($resultCode)&&$resultCode == 10000){
        
    }
    */
    /*
    require_once("../Mym/Mym_Class/AlipayService.php");
    $Dmf_row=$DB->query("SELECT * FROM pay_dmf WHERE id='{$srow['qr_id']}' limit 1")->fetch();
    $f2fid          = $Dmf_row['f2fid'];		//应用的APP
    $f2fpubli        = $Dmf_row['f2fpublic'];	//商户私钥
    $notifyUrl		= ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/Submit/F2f_notify.php';
    $signType = 'RSA2';			//签名算法类型，支持RSA2和RSA，推荐使用RSA2
    
    $aliPay = new AlipayService_dmf();
    $aliPay->setAppid($f2fid);
    $aliPay->setReturnUrl($notifyUrl);
    $aliPay->setNotifyUrl($notifyUrl);
    $aliPay->setRsaPrivateKey($f2fpubli);
    $aliPay->setTotalFee($srow['money']);
    $aliPay->setOutTradeNo($trade_no);
    $aliPay->setOrderName($srow['name']);
    $result = $aliPay->doPay();
    $result = $result['alipay_trade_precreate_response'];
    if($result['code'] && $result['code']=='10000'){
        $ALIPAYSAAS=1;
        $h5payurl = $result['qr_code'];
        $qrcode =$qrcodeapi.urlencode($result['qr_code']);
    }else{
        echo $result['msg'].' : '.$result['sub_msg'];
        sysmsg("当面付配置异常，请检查配置");
    }
    $alipayh5url_1 = "alipays://platformapi/startapp?saId=10000007&qrcode=".urlencode($result['qr_code']);
    include './Pay/dmf.php';
    */
}elseif($QR_row['channel']=='mg_vzq' or $QR_row['channel']=='yd_vzq'){
    $outtime    = ($srow['outtime']-time());
    $qqh5       = 'mqqapi://wxminiapp/launch?src_type=internal&version=1&channel_id=1&user_name=gh_b2f9cc238009&app_type=0&ext=extmsgtes&_vacf=qw&path='.urldecode($srow['qr_url']);
    if(get_device_type()=='ios'){
        $wbh5       = 'sinaweibo://wbdiversion?username=gh_b2f9cc238009&path='.urldecode(urldecode(urldecode($srow['qr_url'])));
    }else{
        $wbh5       = 'sinaweibo://wbdiversion?username=gh_b2f9cc238009&path='.urldecode($srow['qr_url']);
    }
    include './Pay/VzqPay.php';
}elseif($QR_row['channel']=='yd_wx_gskd' or $QR_row['channel']=='yd_wx_sskd'){
    $outtime    = ($srow['outtime']-time());
    $qrcode = SYSTEM_ROOT."cache/qrcode/{$trade_no}.png";
    include './Pay/Skd.php';
}elseif($type=='alipay' and $QR_row['channel']=='pc_alijk'){
    $userId = getSubstr(base64_decode($QR_row['cookie']), "CLUB_ALIPAY_COM=", ";");
    $payh5url = "alipayqr://platformapi/startapp?saId=20000032&url=alipayqr%3A%2F%2Fplatformapi%2Fstartapp%3FappId%3D20000123%26actionType%3Dscan%26biz_data%3D%7B%2522a%2522%253A%2522{$price}%2522%252C%2522s%2522%253A%2522money%2522%252C%2522u%2522%253A%2522{$userId}%2522%252C%2522m%2522%253A%2522{$trade_no}%2522%7D";
    $qr_url = $qrcodeapi.urlencode($siteurl.'/Submit/Mym_Pay.php?trade_no='.$trade_no);
    include './Pay/alipayfk.php';
}elseif($type=='usdt'){
    $qr_url = $qrcodeapi.urlencode(urldecode($srow['qr_url']));
    include './Pay/Usdt.php';
}else {
    
    $pay_qr_content = urldecode($srow['qr_url']);
    if(strstr($pay_qr_content, '.png')){
        if(preg_match('/^https?:\/\//i', $pay_qr_content)){
            $qr_url = $pay_qr_content;
        }else{
            $qr_url = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/'.$pay_qr_content;
        }
    }else{
        $qr_url = $qrcodeapi.urlencode($pay_qr_content);
    }
    if($type == 'wxpay'){
        $typeName = '微信';
    }elseif($type == 'qqpay'){
        $typeName = 'QQ钱包';
    }else{
        $typeName = '支付宝';
    }
    $h5api = $srow['qr_url'];
    if($type=='alipay'){
        $userId = getSubstr(base64_decode($QR_row['cookie']), "CLUB_ALIPAY_COM=", ";");
        if ($QR_row['channel']=='mg_alimp') {
            $payh5url = "alipayqr://platformapi/startapp?saId=10000007&qrcode=".($srow['qr_url']);
        }else{
            if($userrow['free']<=1){
                $payh5url = "https://render.alipay.com/p/s/i?scheme=".urlencode('alipayqr://platformapi/startapp?saId=10000007&qrcode=').$srow['qr_url'];
            }else{
                $payh5url = "https://render.alipay.com/p/s/i?scheme=".$srow['qr_url'];
            }
        }
    }else if($type=='qqpay'){
        $payh5url = "QQH5.php?trade_no={$trade_no}";
    }else{
        $payh5url = "weixin://";
    }
    
    $mod = isset($_GET['mod'])?isset($_GET['mod']):'index';
    $loadfile = \lib\PayTemplate::load($mod);
    include $loadfile;
}