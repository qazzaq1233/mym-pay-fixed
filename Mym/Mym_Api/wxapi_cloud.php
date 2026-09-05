<?php

class wxapi_cloud
{
    public function __construct($url_api,$url,$authcode){
        $this->url_api=$url_api;
        $this->url=$url;
        $this->authcode=$authcode;
    }
    
    //获取二维码
    public function get_qrcode($guid=null){
        return $this->___curl($this->url_api.'?act=qrcode','guid='.$guid);
    }
    
    //登录
    public function get_login($guid,$uuid){
        $data=['guid'=>$guid,'uuid'=>$uuid,'system'=>'mym','url'=>$this->url,'authcode'=>$this->authcode];
        $data=$this->data_sign(json_encode($data));
        return $this->___curl($this->url_api.'?act=login','data='.urlencode($data));
    }
    
    //快捷登录
    public function get_login_push($guid){
        $data=['guid'=>$guid,'system'=>'mym','url'=>$this->url,'authcode'=>$this->authcode];
        $data=$this->data_sign(json_encode($data));
        return $this->___curl($this->url_api.'?act=login_','data='.urlencode($data));
    }
    
    //发送快捷登录弹窗
    public function get_push($guid){
        return $this->___curl($this->url_api.'?act=push','guid='.$guid);
    }
    
    //心跳获取
    public function get_heart($guid){
        return $this->___curl($this->url_api.'?act=heart','guid='.$guid);
    }
    
    //获取消息
    public function get_msg($guid){
        return $this->___curl($this->url_api.'?act=msg','guid='.$guid);
    }
    static function ___curl($url,$post=0,$referer=0,$cookie=0,$header=0){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: keep-alive";
        $httpheader[] = "author: LeavePay&&MymPay";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if($header){
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }
        if($cookie){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if($referer){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    static function data_sign($string, $operation = 'ENCODE', $key = 'MymPay&LeavePay_iujorjninejghjenvejfwgjkow', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}
$wxapi=new wxapi_cloud('',1,'waegggwagwea');
//var_dump($wxapi->get_qrcode('a0f971d1-9510-6461-a4d8-4aed448e2ad1'));
//var_dump($wxapi->get_push('a0f971d1-9510-6461-a4d8-4aed448e2ad1','QYbs-JSVjg=='));
//var_dump($wxapi->get_login_push('a0f971d1-9510-6461-a4d8-4aed448e2ad1','Qeiy-APVew=='));
var_dump($wxapi->get_msg('a0f971d1-9510-6461-a4d8-4aed448e2ad1','Qeiy-APVew=='));
