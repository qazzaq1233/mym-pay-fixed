<?php
class Yun_Login {//账号密码登录
    protected $Api_Url_Url 	= null;
    function __construct($authcode = 'NULL')
    {
        $this->AliApi_Url 	= 'http://auth.iizi.cn/Api/AliLogin.php';
    }
    
    function alilogin($user,$pass,$ua){
        $Url = $this->AliApi_Url.'?act=login';
        $Post = "logonId={$user}&password_rsainput={$pass}&ua={$ua}";
        $response = get_curl($Url,$Post,0,0,0,$_SERVER['HTTP_USER_AGENT']);
        return json_decode($response,true);
    }
    
    function AliLogin_Sms($smscode,$securityId,$ALIPAYJSESSIONID,$_form_token){
        $Url = $this->AliApi_Url.'?act=sms';
        $Post = "smscode={$smscode}&securityId={$securityId}&ALIPAYJSESSIONID={$ALIPAYJSESSIONID}&_form_token={$_form_token}";
        $response = get_curl($Url,$Post,0,0,0,$_SERVER['HTTP_USER_AGENT']);
        return json_decode($response,true);
    }
}

class Yun_Ali_App {//账号密码登录
    protected $Api_Url_Url 	= null;
    function __construct($Url = 'NULL')
    {
        $this->Api_Url 	= 'http://auth.iizi.cn/Api/Uid.php';
    }
    
    function app($row){
        $Url = $this->Api_Url.'?do=app';
        $Post  = "cookie={$row['cookie']}";
        $response = get_curl($Url,$Post,0,0,0,$_SERVER['HTTP_USER_AGENT']);
        return json_decode($response,true);
    }
    
    function AliYunSet($appid,$cookie){
        $Url = $this->Api_Url.'?do=appset';
        $Post  = "appid={$appid}&cookie={$cookie}";
        $response = get_curl($Url,$Post,0,0,0,$_SERVER['HTTP_USER_AGENT']);
        return json_decode($response,true);
    }
    
    function AliYunSms($appid,$phone,$smscode,$cookie){
        $Url = $this->Api_Url.'?do=appsme';
        $Post  = "appid={$appid}&phone={$phone}&code={$smscode}&cookie={$cookie}";
        $response = get_curl($Url,$Post,0,0,0,$_SERVER['HTTP_USER_AGENT']);
        return json_decode($response,true);
    }
}