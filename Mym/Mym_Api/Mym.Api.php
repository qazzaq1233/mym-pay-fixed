<?php
function PayName($name)
{
    // 开源版不再调用旧远程名单接口，默认放行本地收款账号。
    return true;
}

function Login_Qr()
{
    return array('code' => -1, 'msg' => '开源本地版已禁用旧远程扫码登录服务');
}

function qrlogin($id)
{
    return array('code' => -1, 'msg' => '开源本地版已禁用旧远程扫码登录服务');
}

function proxy()
{
    global $ip;
    $city=get_ip_city()['Result']['Country'];
    if(stristr($city,"北京市"))$province='110100';
    if(stristr($city,"天津市"))$province='120000';
    if(stristr($city,"河北省"))$province='130000';
    if(stristr($city,"山西省"))$province='140000';
    if(stristr($city,"内蒙古自治区"))$province='150000';
    if(stristr($city,"辽宁省"))$province='210000';
    if(stristr($city,"吉林省"))$province='220000';
    if(stristr($city,"黑龙江省"))$province='230000';
    if(stristr($city,"江苏省"))$province='320000';
    if(stristr($city,"浙江省"))$province='330000';
    if(stristr($city,"安徽省"))$province='340000';
    if(stristr($city,"福建省"))$province='350000';
    if(stristr($city,"江西省"))$province='360000';
    if(stristr($city,"山东省"))$province='370000';
    if(stristr($city,"河南省"))$province='410000';
    if(stristr($city,"河北省"))$province='420000';
    if(stristr($city,"湖南省"))$province='430000';
    if(stristr($city,"广东省"))$province='440000';
    if(stristr($city,"广西自治区"))$province='450000';
    if(stristr($city,"海南省"))$province='460000';
    if(stristr($city,"重庆市"))$province='500100';
    if(stristr($city,"四川省"))$province='510000';
    if(stristr($city,"贵州省"))$province='520000';
    if(stristr($city,"云南省"))$province='530000';
    if(stristr($city,"西藏自治区"))$province='540000';
    if(stristr($city,"陕西省"))$province='610000';
    if(stristr($city,"甘肃省"))$province='620000';
    if(stristr($city,"青海省"))$province='630000';
    if(stristr($city,"宁夏省"))$province='640000';
    if(stristr($city,"新疆自治区"))$province='650000';

    $url = 'http://api1.ydaili.cn/tools/MeasureApi.ashx?action=EAPI&secret=FE7ABE7E444BEC6CF13C2DA47182C2E7C05A9D8C9A96DD52&number=1&orderId=27086&format=txt&province='.$province;
    $get = file_get_contents($url);
    $proxy = explode(':',$get);
    return array("ip"=>$proxy[0],"do"=>$proxy[1]);

}


function pngupload($file)
{
	$url = "http://upload.huluxia.com/upload/v3/image?platform=2&gkey=000000&app_version=4.1.1.8.1&versioncode=343&market_id=tool_tencent&_key=2B6511EE3B4DC6A1CCB88B01D510EFE92DFA89813D94F0897832946CA7F7C947ED114AEF14216C3A1D9F042947BFB6F47DAA2D684B1C1245&device_code=%5Bd%5Dd37f9cdb-384a-4c7e-978d-4f52171ba110&use_type=2&sign=474F42076A753C71C2A1193A0251BCB2&timestamp=1652350467334&nonce_str=yo4wYfLoGbjMwGongQSaQzZDkQnqTKZT";
    $post = array(
        'name' =>$file,
		'file' => new \CURLFile(realpath($file))
        );
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
function qrcode($qrcode)
{
    global $decode;
    //$decode = base64_decode($decode);
	$token = '24.5b80fd8271e8d4add4ee242435ccc470.2592000.1636815682.282335-24995014';
    $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/qrcode?access_token=' . $token;
    $bodys = array(
    'url' => $qrcode
    );
	$qr_url= '';
	if(!$qr_url){
		$post['img'] = $qrcode;
	    $result = get_curl(base64_decode($decode[0]),'img='.$qrcode);
	    $qr_url = json_decode($result, true)['data']['RawData'];
	}
	if(!$qr_url){
	    $result = get_curl('https://cli.im/Api/Browser/deqr','data='.$qrcode);
	    $qr_url = json_decode($result, true)['data']['RawData'];
	}
	if(!$qr_url){
		$result = get_curl('https://api.uomg.com/api/qr.encode?url='.$qrcode);
	    $qr_url = json_decode($result, true)['qrurl'];
	}
	if(!$qr_url){
	    $result = get_curl(base64_decode($decode[3]),'imgurl='.$qrcode);
	    $qr_url = json_decode($result, true)['qrtext'];
	}
	if(!$qr_url){
        $res = request_post($url, $bodys);
        $qr_url = json_decode($res, true)['codes_result'][0]['text'][0];
	}
	return $qr_url;
}

function curl_Update($url){
	$ch=curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 4.4.1; zh-cn; R815T Build/JOP40D) AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0 MQQBrowser/4.5 Mobile Safari/533.1');
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	$content=curl_exec($ch);
	curl_close($ch);
	return($content);
}
function Update($VERSION){
    return array(
        'code' => 1,
        'msg' => '当前为开源本地版，已禁用远程版本检查',
        'version' => defined('VERSIONS') ? VERSIONS : $VERSION,
        'url' => ''
    );
}

function AuthUpdate(){
    return array(
        'code' => 1,
        'msg' => '当前为开源本地版，已禁用远程授权检查',
        'status' => 1
    );
}

function username($Cookie,$type)
{
    $Cookie = base64_decode($Cookie);
    if($type=='qqpay'){
        $uin = explode("qluin=",$Cookie);
        $p_skey = 'p_skey='.getSubstr($Cookie,"p_skey=", ";").';';
        $QQUrl = 'https://myun.tenpay.com/cgi-bin/clientv1.0/qwallet_account_list.cgi?limit=10&offset=0&s_time='.date("Y-m-d").'&time_type=0&source_type=7&pay_type=2&ref_param=&skey=&skey_type=2&uin='.$uin[1];
        $QQreferer = 'https://myun.tenpay.com/mqq/myun/trade/record.shtml?_wv=1027&_wvx=10';
        $result = get_curl($QQUrl,0,$QQreferer,$p_skey,0);
        $json = json_decode($result, true);
        if($json['retcode']==0 and $json['retmsg']=="OK"){
            return ['code'=>1,'msg'=>'成功获取数据','userName'=>$json['true_name'],'email'=>$uin[1]];
		}else{
		    return ['code'=>-1,'msg'=>'获取失败'];
		}
    }else{
        $ALIPAYJSESSIONID = 'ALIPAYJSESSIONID='.getSubstr($Cookie,"ALIPAYJSESSIONID=", ";").';';
        $Url_Referer = "https://mrchportalweb.alipay.com/interface/login/index/queryuser";
        $Url_Referer2 = "https://my.alipay.com/portal/i.htm";
        $referer = $Url_Referer.'?&t='.time();
        $result = get_curl($Url_Referer,0,$referer,$ALIPAYJSESSIONID,0);
        $json = json_decode($result, true);
        if($json['stat']!='deny'){
            AddAliOrder($Cookie);//首次登录自动开通账单
            tables_csv($Cookie);//设置账单表格排序
            return ['code'=>1,'msg'=>'成功获取数据','userName'=>$json['data']['realName'],'email'=>$json['data']['loginId']];
        }else{
            return ['code'=>-1,'msg'=>'获取失败'];
        }
    }
}

function AddAliOrder($Cookie)
{
    $ctoken = getSubstr($Cookie,"ctoken=", ";");
    $pid = getSubstr($Cookie, "CLUB_ALIPAY_COM=", ";");
    $str = 'userId='.$pid.'&_input_charset=gbk';
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://mbillexprod.alipay.com/enterprise/fundAccountUserRule.json?ctoken='.$ctoken.'&_output_charset=utf-8',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>$str,
      CURLOPT_HTTPHEADER => array(
          'Connection: keep-alive',
          'Accept: application/json',
          'Referer: https://b.alipay.com/',
          'Accept-Language: zh-CN,zh;q=0.9',
          'sec-ch-ua: "Chromium";v="110", "Not A(Brand";v="24", "Google Chrome";v="110"',
          'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
          'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
          'cookie: '.$Cookie
          ),
          ));
    $response = curl_exec($curl);
    curl_close($curl);
   return json_decode($response,true);
}

function tables_csv($Cookie){//设置账单表格排序
    $Url = 'https://mbillexprod.alipay.com/enterprise/customTableStyle.json?_reqFromMicro_&ctoken=&_output_charset=utf-8';
    $post = 'tableId=BILL_CENTER_REALTIME_BILL_ACCOUNT_LOG&tableStyle=%5B%7B%22key%22%3A%22tradeTime%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A18%2C%22field%22%3A%22%E5%85%A5%E8%B4%A6%E6%97%B6%E9%97%B4%22%7D%2C%7B%22key%22%3A%22tradeNo%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A17%2C%22field%22%3A%22%E6%94%AF%E4%BB%98%E5%AE%9D%E4%BA%A4%E6%98%93%E5%8F%B7%22%7D%2C%7B%22key%22%3A%22orderNo%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A16%2C%22field%22%3A%22%E5%95%86%E6%88%B7%E8%AE%A2%E5%8D%95%E5%8F%B7%22%7D%2C%7B%22key%22%3A%22othersideInfo%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A15%2C%22field%22%3A%22%E5%AF%B9%E6%96%B9%E4%BF%A1%E6%81%AF%22%7D%2C%7B%22key%22%3A%22accountType%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A14%2C%22field%22%3A%22%E8%B4%A6%E5%8A%A1%E7%B1%BB%E5%9E%8B%22%7D%2C%7B%22key%22%3A%22tradeAmount%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A13%2C%22field%22%3A%22%E6%94%B6%E6%94%AF%E9%87%91%E9%A2%9D%EF%BC%88%E5%85%83%EF%BC%89%22%7D%2C%7B%22key%22%3A%22balance%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A12%2C%22field%22%3A%22%E8%B4%A6%E6%88%B7%E4%BD%99%E9%A2%9D%EF%BC%88%E5%85%83%EF%BC%89%22%7D%2C%7B%22key%22%3A%22buyerMemo%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A11%2C%22field%22%3A%22%E4%BB%98%E6%AC%BE%E5%A4%87%E6%B3%A8%22%7D%2C%7B%22key%22%3A%22transMemo%22%2C%22show%22%3Atrue%2C%22lock%22%3Afalse%2C%22order%22%3A10%2C%22field%22%3A%22%E5%A4%87%E6%B3%A8%22%7D%2C%7B%22key%22%3A%22showType%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A9%2C%22field%22%3A%22%E6%94%B6%E6%94%AF%E7%B1%BB%E5%9E%8B%22%7D%2C%7B%22key%22%3A%22accountLogId%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A8%2C%22field%22%3A%22%E6%B5%81%E6%B0%B4%E5%8F%B7%22%7D%2C%7B%22key%22%3A%22goodsTitle%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A7%2C%22field%22%3A%22%E5%95%86%E5%93%81%E5%90%8D%E7%A7%B0%22%7D%2C%7B%22key%22%3A%22bizOrigNo%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A6%2C%22field%22%3A%22%E4%B8%9A%E5%8A%A1%E5%9F%BA%E7%A1%80%E8%AE%A2%E5%8D%95%E5%8F%B7%22%7D%2C%7B%22key%22%3A%22bizNos%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A5%2C%22field%22%3A%22%E4%B8%9A%E5%8A%A1%E8%AE%A2%E5%8D%95%E5%8F%B7%22%7D%2C%7B%22key%22%3A%22billSource%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A4%2C%22field%22%3A%22%E4%B8%9A%E5%8A%A1%E8%B4%A6%E5%8D%95%E6%9D%A5%E6%BA%90%22%7D%2C%7B%22key%22%3A%22bizDesc%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A3%2C%22field%22%3A%22%E4%B8%9A%E5%8A%A1%E6%8F%8F%E8%BF%B0%22%7D%2C%7B%22key%22%3A%22storeInfo%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A2%2C%22field%22%3A%22%E9%97%A8%E5%BA%97%E4%BF%A1%E6%81%AF%22%7D%2C%7B%22key%22%3A%22otherBizFullName%22%2C%22show%22%3Afalse%2C%22lock%22%3Afalse%2C%22order%22%3A1%2C%22field%22%3A%22%E4%B8%9A%E5%8A%A1%E5%AF%B9%E6%96%B9%E4%BF%A1%E6%81%AF%22%7D%5D&_input_charset=gbk';
    return get_curl($Url,$post,'https://b.alipay.com/',$Cookie);
}

function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($curl);//运行curl
        curl_close($curl);
        
        return $data;
    }