<?php
/**
 * Name:财付通QQ钱包登陆码协议 取支付订单协议 [INTL码支付]
 */
error_reporting(0);
class Login_Qrcode {
	
    protected $QQ_UA = 'Host: myun.tenpay.com
Connection: keep-alive
Accept: application/json
cache-control: no-cache, no-store
X-Requested-With: XMLHttpRequest
User-Agent: Mozilla/5.0 (Linux; Android 7.1.1; OPPO A83 Build/N6F26Q; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.120 MQQBrowser/6.2 TBS/045520 Mobile Safari/537.36 V1_AND_SQ_8.6.0_1672_YYB_D N_8060010 QQ/8.6.0.5210 NetType/WIFI WebP/0.3.0 Pixel/720 StatusBarHeight/37 SimpleUISwitch/0 QQTheme/1000 InMagicWin/0 StudyMode/0';
	protected $ALI_UA = 'Accept: image/gif, image/jpeg, image/pjpeg, application/x-ms-application, application/xaml+xml, application/x-ms-xbap, */*
Referer: https://auth.alipay.com/login/index.htm?goto=https%3A%2F%2Fmy.alipay.com%2Fportal%2Fi.htm
Accept-Language: zh-Hans-CN,zh-Hans;q=0.5
Accept-Encoding: gzip, deflate
User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko
Host: authet2.alipay.com
Connection: Keep-Alive
Cache-Control: no-cache';
	protected $ALI_API_URL = '42.193.53.4';
	
    function __construct($proxy=null) {
        if (explode(':', $proxy) [0] and explode(':', $proxy) [1]) {
            $this->proxy = array(explode(':', $proxy) [0], explode(':', $proxy) [1]); //代理IP
        } else {
            $this->proxy = '';
        }
    }
	
    /**
     * 获取支付宝登录二维码
     * @qq  QQ号
     */
    function Get_AliLogin_Qr() {
		$url='https://auth.alipay.com/login/index.htm';
		$url='https://auth.alipay.com/login/index.htm?goto=https%3A%2F%2Fb.alipay.com%2Fpage%2Fmessage%2FtasksDetail%3FbizData%3D%257B%2522platformCode%2522%253A%2522O%2522%252C%2522taskType%2522%253A%2522INTERFACE_AUTH%2522%252C%2522agentOpParam%2522%253A%257B%2522redirectUri%2522%253A%2522http%253A%252F%252Fa.iizi.cn%252Fdata.php%2522%252C%2522appTypes%2522%253A%255B%2522WEBAPP%2522%252C%2522PUBLICAPP%2522%255D%252C%2522isvAppId%2522%253A%25222019080866149495%2522%257D%257D';
        $data = $this->Get_curl_header($url,0,0,$this->ALI_UA);
		preg_match('/securityId: "(.*?)",/',$data['body'],$match);
		$authcenter_qrcode_login = $match[1];
		preg_match('/s.sid = "(.*?)"/',$data['body'],$match);
		$authcenter_querypwd_login = $match[1];
		$rds_form_token = $this->getSubstr($data['body'], '<input type="hidden" value="', '" name="rds_form_token"/>');
		$alieditUid = $this->getSubstr($data['body'], '<input type="hidden" id="alieditUid" name="alieditUid" value="', '" />');
		preg_match('/ALIPAYJSESSIONID=(.*?);/',$data['header'],$match);
		$ALIPAYJSESSIONID = $match[1];
		$AliQCode = 'https://qr.alipay.com/_d?_b=PAI_LOGIN_DY&amp;securityId='.urlencode($authcenter_qrcode_login);
		$LoginQrCode = "//minico.qq.com/qrcode/get?type=2&r=2&size=250&text=".urlencode($AliQCode);
		return array("code"=>1,"qr_url"=>$LoginQrCode,"id"=>$authcenter_qrcode_login.'MYM-Pay'.$authcenter_querypwd_login.'MYM-Pay'.$rds_form_token.'MYM-Pay'.$alieditUid);
    }
	
    /**
     * 检测支付宝登录并获取Cookie
     */
    function Check_AliLogin($id) {
		$intlpay = explode("MYM-Pay",$id);
		$url = 'https://securitycore.alipay.com/barcode/barcodeProcessStatus.json?';
		$post_intl['securityId'] = $intlpay[0];
		$post_intl['_callback'] = 'light.request._callbacks.callback2';
		$post_intl = http_build_query($post_intl);
        $data_intl= $this->Get_curl($url.$post_intl,0,0,0,0,$this->ALI_UA);
		$url='https://authet2.alipay.com/login/index.htm';
		$post['support'] = '000001';
		$post['needTransfer'] = '';
		$post['CtrlVersion'] = '1,1,0,1';
		$post['loginScene'] = 'index';
		$post['redirectType'] = '';
		$post['personalLoginError'] = '';
   		$post['goto'] = 'https://www.alipay.com/';
   		$post['goto'] = 'https://b.alipay.com/page/message/tasksDetail?bizData=%7B%22platformCode%22%3A%22O%22%2C%22taskType%22%3A%22INTERFACE_AUTH%22%2C%22agentOpParam%22%3A%7B%22redirectUri%22%3A%22http%3A%2F%2Fa.iizi.cn%2Fdata.php%22%2C%22appTypes%22%3A%5B%22WEBAPP%22%2C%22PUBLICAPP%22%5D%2C%22isvAppId%22%3A%222019080866149495%22%7D%7D';
    	$post['errorVM'] = '';
		$post['sso_hid'] = '';
		$post['site'] = '';
   		$post['errorGoto'] = '';
		$post['rds_form_token'] = $intlpay[2];
   		$post['json_tk'] = '';
    	$post['method'] = 'qrCodeLogin';
    	$post['logonId'] = '';
    	$post['superSwitch'] = 'true';
    	$post['noActiveX'] = 'false';
    	$post['passwordSecurityId'] = $intlpay[1];//urlencode
    	$post['qrCodeSecurityId'] = $intlpay[0];
    	$post['scid'] = '';
    	$post['password_input'] = '';
    	$post['J_aliedit_using'] = 'true';
    	$post['password'] = '';
    	$post['J_aliedit_key_hidn'] = 'password';
    	$post['J_aliedit_uid_hidn'] = 'alieditUid';
    	$post['alieditUid'] = $intlpay[3];
    	$post['REMOTE_PCID_NAME'] = '_seaside_gogo_pcid';
   		$post['_seaside_gogo_pcid'] = '';
    	$post['_seaside_gogo_'] = '';;
    	$post['_seaside_gogo_p'] = '';
   		$post['J_aliedit_prod_type'] = '';
   		$post['security_activeX_enabled'] = 'true';
    	$post['checkCode'] = '';
    	$post['idPrefix'] = '';
    	$post['preCheckTimes'] = 5;
		$post['ua'] = 'dxlTasyfLePewE7ifH7HyT5wJ5cASsGqMaYnOomEiyTBeHyI6CezVWDWcDouca6W6Y4Svep9ulZ8H0cF1X4Mgi.JZTbQL3NddYS7bCmG.cFh45fZXR3kTmjsMi3xByeTW2V5hnaat0y1OOiv8qoAfKgaUuigtJAp3UL2QgUVrpASMRKdStX0h.hzFfH26FHtMkCmnf1nRcw74yljdFFMC03XWUBNZDhPUI0aL76t.NVxaOJQngu.KFQoPrVjSWYgym6MackvvBhmL37Y0s4H.ROLsAdVrDnLoQR7y07wcwWbUSqq.6AdBebIIVg1RHjyn3K9ahqPk_HOBlXyg6_voFZWwvoFlVAZ9c_ARvTidwDCE.18sT9z2ELtWGaAVClk6HN0HXMQUwOH7Rr7sfpn3zp__eOAe75qTBmYMNXnYnChZmqWOAaVdJAcFpjoUtwtwwqcZvdvoX4_UH.06SpF.Z0i85GWt4jSkki5ijEyvav5KeLQX6Tvj7MziuxQasAOVX6CHZu62D3FhWwj1cesYq9iKyzNmhMcqc.ULS88i3oq2vZko8vOI3BFufd0GcYAMI.YS8a4IqoaE4ydLO4ALR.8WuHvpOZiilHq.hOZogZB2QoQApBuo5smKdhzGlcybdYtsoxPtD_jIRXtf7aRzIWtIlcgEHk6RyOhjsA7bSuWurcukkCAvTZtO05xq6s_lmjMUVNeyS34DpiEXKJlqmbi.amq3hj2Oph0AZtvPb8LvZJy8V.aYdcC4RH9UeAOEzzJgpeiiAAUcMRexpGs5ZDcBFdgn4MIXfq2aTIUFVHf0W3in7tGaeNmx1MUIWHHTL_3SmNYyPVaEo5qZzNLTMrc318MBiIFjcTmaub1IJw7IZefBjspVK9bzzYMwhe0ljkUqoCoshjN9rlXjKyJ.vsXLosDb7KEKmWejetEAPRlw2e49JmWJj5ohGrOGzlsTzyMUtY07nlv3gjXWkaUaE';
		$post = http_build_query($post);
        if(!strpos($data_intl,'waiting') and !strpos($data_intl,'scanned')){$data = $this->Get_curl_header($url,$post,0,$this->ALI_UA);}
		$data = iconv("GBK", "UTF-8", $data['header']);
		$ALIPAYJSESSIONID = explode("ALIPAYJSESSIONID=",$data)[2];
		$ALIPAYJSESSIONID = explode("Domain=",$ALIPAYJSESSIONID)[0];
		$ALIPAYJSESSIONID = explode(";",$ALIPAYJSESSIONID)[0];
		$ctoken = explode("ctoken=",$data)[2];
		$ctoken = explode("Domain=",$ctoken)[0];
		$CLUB_ALIPAY_COM = explode("CLUB_ALIPAY_COM=",$data)[1];
		$CLUB_ALIPAY_COM = explode("Domain=",$CLUB_ALIPAY_COM)[0];
		$CLUB_ALIPAY_COM = explode(";",$CLUB_ALIPAY_COM)[0];
		
		$JSESSIONID = explode("JSESSIONID=",$data)[1];
		$JSESSIONID = explode(";",$JSESSIONID)[0];
		$alipay = explode("alipay=",$data)[1];
		$alipay = explode(";",$alipay)[0];
		$spanner = explode("spanner=",$data)[1];
		$spanner = explode(";",$spanner)[0];
		if($ALIPAYJSESSIONID and $CLUB_ALIPAY_COM){
		    get_curl('https://shanghu.alipay.com/error.htm?funCode=FUN.USER&causeCode=ERROR.CUSTOMER.NOT.EXIST',0,0,'ALIPAYJSESSIONID='.$ALIPAYJSESSIONID);
		    $cookies = 'zone=GZ00C; JSESSIONID='.$JSESSIONID.'; ali_apache_tracktmp="uid='.$CLUB_ALIPAY_COM.'; "; IntroKey=true; session.cookieNameId=ALIPAYJSESSIONID; ALIPAYJSESSIONID='.$ALIPAYJSESSIONID.'GZ00; ctoken='.$ctoken.'; CLUB_ALIPAY_COM='.$CLUB_ALIPAY_COM.'; ';
		    get_curl('https://b.alipay.com/page/message/tasksDetail?bizData=%7B%22platformCode%22%3A%22O%22%2C%22taskType%22%3A%22INTERFACE_AUTH%22%2C%22agentOpParam%22%3A%7B%22redirectUri%22%3A%22http%3A%2F%2Fa.iizi.cn%2Fdata.php%22%2C%22appTypes%22%3A%5B%22WEBAPP%22%2C%22PUBLICAPP%22%5D%2C%22isvAppId%22%3A%222019080866149495%22%7D%7D',0,0,$cookies);
		}
		if(strpos($data_intl,'waiting')){
			$msg = '等待扫码中';
			$code = 1;
		}elseif(strpos($data_intl,'scanned')){
			$msg = '等待确认中';
			$code = 2;
		}else{
			$msg = '登录成功';
			$code = 200;
		}
		if($msg>0){
		  return array("code"=>$code,"msg"=>$msg,"cookie"=>base64_encode($cookies));
		}else{
		  return array("code"=>$code,"msg"=>$msg,"cookie"=>base64_encode($cookies));//
	    }
    }
    /**
     * 获取QQ财付通登录二维码
     * @qq  QQ号
     */
    function Get_QQLogin_Qr() {
		$url='https://ssl.ptlogin2.tenpay.com/ptqrshow?appid=546000248&e=2&l=M&s=3&d=72&v=4&t=0.5409099'.time().'&daid=120&pt_3rd_aid=0';
        $data = $this->Get_curl_header($url,0,0,$this->QQ_UA);
		preg_match('/qrsig=(.*?);/',$data['header'],$match);
		return array("code"=>1,"qr_url"=>'data:image/png;base64,'.base64_encode($data['body']),"id"=>$match[1]);
    }
	
    /**
     * 检测QQ登录并获取P_skey
     */
    function Check_QQLogin($qrsig) {
		$url = 'https://ssl.ptlogin2.tenpay.com/ptqrlogin?u1=https%3A%2F%2Fwww.tenpay.com%2Fv2%2Fres%2Fjs%2Fyui%2Fbuild%2Flogin%2Fptlogin.shtml&ptqrtoken='.$this->getqrtoken($qrsig).'&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=0-1-'.time().'0000&js_ver=21032613&js_type=1&login_sig='.$this->getqrtoken($qrsig).'&pt_uistyle=34&aid=546000248&daid=120&';
		$cookie = 'qrsig='.$qrsig.'; ';
		$ret = $this->Get_curl($url,0,$url,$cookie,1,$this->QQ_UA);
		preg_match("/ptuiCB\('(.*?)'\)/", $ret, $arr);
		$r=explode("','",str_replace("', '","','",$arr[1]));
		preg_match("/uin=o(.*?);/", $ret, $qq);
		$uin_a = explode("o00000",'o'.$qq[1])[1];
		$uin_b = explode("o0000",'o'.$qq[1])[1];
		$uin_c = explode("o000",'o'.$qq[1])[1];
		$uin_d = explode("o00",'o'.$qq[1])[1];
		$uin_e = explode("o0",'o'.$qq[1])[1];
		if($uin_a){
			$qq = $uin_a;
		}elseif($uin_b){
			$qq = $uin_b;
		}elseif($uin_c){
			$qq = $uin_c;
		}elseif($uin_d){
			$qq = $uin_d;
		}elseif($uin_e){
			$qq = $uin_e;
		}else{
			$qq = $qq[1];
		}
		preg_match("/skey=(.*?);/", $ret, $matchs);
		$skey = $matchs[1];
		preg_match("/superkey=(.*?);/", $ret, $matchs);
		$superkey = $matchs[1];
		if(strpos($r[4],'成功')){$data = $this->Get_curl($r[2],0,0,0,1);}
		if($data) {
			preg_match("/p_skey=(.*?);/", $data, $matchs);
			$p_skey = $matchs[1];
			preg_match("/pgv_info=(.*?);/", $data, $matchs);
			$pgv_info = $matchs[1];
			preg_match("/pgv_pvid=(.*?);/", $data, $matchs);
			$pgv_pvid = $matchs[1];
			if($qq and $p_skey){
			  $cookies = 'superkey='.$superkey.'; skey='.$skey.'; p_skey='.$p_skey.'; qluin='.$qq;
			}
		}
		if(strpos($r[4],'二维码已失效')){
			$msg = '二维码已失效';
			$code = 4;
		}elseif(strpos($r[4],'未失效')){
			$msg = '等待扫码中';
			$code = 1;
		}elseif(strpos($r[4],'认证中')){
			$msg = '等待确认中';
			$code = 2;
		}else{
			$msg = '登录成功';
			$code = 200;
		}		
		if($msg>0){
		return array("code"=>$code,"msg"=>$msg,"cookie"=>base64_encode($cookies));
		}else{
		return array("code"=>$code,"msg"=>$msg,"name"=>$r[5],"cookie"=>base64_encode($cookies),"id"=>$qrsig);
        }
    }
	/* 取QQ钱包订单信息
	https://myun.tenpay.com/cgi-bin/clientv1.0/qwallet_account_list.cgi?limit=10&offset=0&s_time=2020-04-01&time_type=0&source_type=7&pay_type=2&ref_param=&skey=&skey_type=2&uin=2645147933
	*/
	
	
	
	
	function getqrtoken($qrsig){
        $len = strlen($qrsig);
        $hash = 0;
        for($i = 0; $i < $len; $i++){
            $hash += (($hash << 5) & 2147483647) + ord($qrsig[$i]) & 2147483647;
			$hash &= 2147483647;
        }
        return $hash & 2147483647;
	}
	private function Get_curl_header($url,$post,$cookie,$ua, $get_login_cookie = 0, $login_cookie_data = 0){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$httpheader[] = "Accept: */*";
		$httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
		$httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
		$httpheader[] = "Connection: keep-alive";
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.25 Safari/537.36 Core/1.70.3741.400 QQBrowser/10.5.3863.400');
        }
        if ($get_login_cookie) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $get_login_cookie);
        }
        if ($login_cookie_data) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $login_cookie_data);
        }
		$ret = curl_exec($ch);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($ret, 0, $headerSize);
		$body = substr($ret, $headerSize);
		$ret=array();
		$ret['header']=$header;
		$ret['body']=$body;
		curl_close($ch);
		return $ret;
	}
    /**
		$json_decode = json_decode($A,$B);
     * Get_curl
     */
    function Get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0, $get_login_cookie = 0, $login_cookie_data = 0, $proxy = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($proxy) { // 有代理IP就使用代理
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]); //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy[1]); //代理服务器端口
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        }
        // 伪造客户端来源IP
        $xforip = rand(1, 254) . "." . rand(1, 254) . "." . rand(1, 254) . "." . rand(1, 254);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('CLIENT-IP:' . $xforip, 'X-FORWARDED-FOR:' . $xforip));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept:*/*";
        $httpheader[] = "Accept-Encoding:gzip,deflate,sdch";
        $httpheader[] = "Accept-Language:zh-CN,zh;q=0.8";
        $httpheader[] = "Connection:close";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($referer) {
            if ($referer == 1) {
                curl_setopt($ch, CURLOPT_REFERER, 'http://m.qzone.com/infocenter?g_f=');
            } else {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }
        }
        if ($ua) {
            curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        } else {
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.25 Safari/537.36 Core/1.70.3741.400 QQBrowser/10.5.3863.400');
        }
        if ($nobaody) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        if ($get_login_cookie) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $get_login_cookie);
        }
        if ($login_cookie_data) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $login_cookie_data);
        }
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    function getSubstr($str, $leftStr, $rightStr) {
        $left = strpos($str, $leftStr);
        //echo '左边:'.$left;
        $right = strpos($str, $rightStr, $left);
        //echo '<br>右边:'.$right;
        if ($left < 0 or $right < $left) return '';
        return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
    }
    function short_url($url) { //新浪短网址生成
        $short_url_api = 'http://t.jusitan.com/api/urlCreate';
        $post = 'url=' . urlencode($url);
        $data = $this->Get_curl($short_url_api, $post);
        $explode = explode('</span><span class="col-blue02" id="shorturl">', $data);
        $explode = explode('</span>', $explode[1]);
        $json = json_decode($data, true);
        return $json['list'];
    }
}
?>