<?php
// 定义开源版标识
define('MYM_OPEN_SOURCE', true);
define('MYM_VERSION', '2.7.0-fixed');

// 调试模式开关 - 生产环境请设为false
define('MYM_DEBUG', false);

// 错误处理
if (MYM_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

@header("content-Type: text/html; charset=utf-8");
// 安全头
@header("X-Content-Type-Options: nosniff");
@header("X-Frame-Options: SAMEORIGIN");
@header("X-XSS-Protection: 1; mode=block");

// 时区设置（移除重复设置）
date_default_timezone_set('Asia/Shanghai');

// Session配置（PHP 8兼容优化）
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

define('SYS_KEY', 'Apis');
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('TEMPLATE_ROOT', ROOT.'Template/');
define('PAYTEMPLATE_ROOT', ROOT.'Submit/Template/');

$date = date("Y-m-d H:i:s");
$is_defend = true;

if (is_file(SYSTEM_ROOT.'360_Safe/360webscan.php')) {//360网站卫士
    require_once(SYSTEM_ROOT.'360_Safe/360webscan.php');
}

require_once(SYSTEM_ROOT."Mym_Class/Autoloader.php"); //自动载入
Autoloader::register();
include_once(SYSTEM_ROOT."Mym_Class/Security.php");

if(!function_exists("is_https")){
	function is_https() {
		if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443){
			return true;
		}elseif(isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')){
			return true;
		}elseif(isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https'){
			return true;
		}elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
			return true;
		}elseif(isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https'){
			return true;
		}elseif(isset($_SERVER['HTTP_EWS_CUSTOME_SCHEME']) && $_SERVER['HTTP_EWS_CUSTOME_SCHEME'] == 'https'){
			return true;
		}
		return false;
	}
}

$siteurl = (is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/';

if(file_exists(SYSTEM_ROOT."Config.php")){
    include(SYSTEM_ROOT."Config.php");
}else{
	header('Content-type:text/html;charset=utf-8');
	echo '你还没安装！<a href="/install/">点此安装</a>';
	exit();
}

$dbconfig['dbqz'] = 'pay';

if(!defined('SQLITE') && (!$dbconfig['user']||!$dbconfig['pwd']||!$dbconfig['dbname'])){//检测安装
	header('Content-type:text/html;charset=utf-8');
	echo '你还没安装！<a href="/install/">点此安装</a>';
	exit();
}

try{
    $DB = new \lib\PdoHelper($dbconfig);
}catch(Exception $e){
    exit('链接数据库失败:'.MYM_DEBUG ? $e->getMessage() : '请检查数据库配置');
}

require_once SYSTEM_ROOT.'Mym_Class/lib/Cache.Class.php'; //Cache.ph
require_once SYSTEM_ROOT."authcode.php"; // 开源版兼容入口

$CACHE=new CACHE();
$conf=$CACHE->pre_fetch();

// 开源版跳过版本强制升级检测
if (!defined('MYM_OPEN_SOURCE') && isset($conf['version']) && $conf['version'] < DB_VERSION) {
    if (empty($install)) {
		header('Content-type:text/html;charset=utf-8');
        echo '请先完成网站升级！<a href="/install/update.php"><font color=red>点此升级</font></a>';
        exit;
    }
}

$conf_defaults = array(
    'KEY' => 'mym-open-source-local-key',
    'CAPTCHA_ID' => isset($conf['captcha_id']) ? $conf['captcha_id'] : '',
    'PRIVATE_KEY' => isset($conf['captcha_key']) ? $conf['captcha_key'] : '',
    'goid' => '',
    'proxy' => 0,
    'proxy_type' => 'http',
    'cdnpublic' => 0,
    'qrpublic' => 1,
    'template' => 'default',
    'sitename' => 'MYM码支付',
    'keywords' => '',
    'description' => '',
    'qq' => '',
    'cronkey' => 'Mymcode',
);

foreach ($conf_defaults as $conf_key => $conf_value) {
    if (!isset($conf[$conf_key]) || $conf[$conf_key] === '') {
        $conf[$conf_key] = $conf_value;
    }
}

require_once SYSTEM_ROOT.'Mym_Class/Function.php';
$ip = real_ip();
require_once SYSTEM_ROOT.'Mym_Class/Pay_function.php';
require_once SYSTEM_ROOT.'Mym_Class/Display.php';
require_once SYSTEM_ROOT.'Mym_Class/Login.Class.php';
require_once SYSTEM_ROOT.'Mym_Api/Mym.Api.php';

// API配置 - 保留原配置，失效的API会自动降级
$mzfapi = 'http://api.iizi.cn/';
$qqcode = base64_decode("aHR0cDovLzQzLjEzOC4yMjMuMTYxOjgwOTAvP2FjdD1xcmNvZGUmcXE9");
$qqyuncode = base64_decode('aHR0cDovLzQzLjEzOC4yMjMuMTYxOjg1NzkvQXBpL1FyQ29kZT8=');
$decode = array(
    'aHR0cHM6Ly9jbGkuaW0vYXBpcy91cC9kZXFyaW1n',
    'aHR0cHM6Ly9jbGkuaW0vQXBpL0Jyb3dzZXIvZGVxcg==',
    'aHR0cHM6Ly9hcGkudW9tZy5jb20vYXBpL3FyLmVuY29kZT91cmw9',
    'aHR0cHM6Ly9hcGkuZmx5NjMuY29tL2hvbWUvc3RhdGljL3BocC9kZWNvZGVyL2FwaS5waHA=',
    'aHR0cDovL2ptLnN2aXBsdC5jbi9xcmNvZGUvZGVjb2RlP3VybD0='
);
$appkey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAngNw4RAJZTh3N5TBPTpFJIkrTNN7WXXDcG48f2k9rre92Vm0hDW000E88WpNN3MaRJ/Ue5t/a0zzjRPCqZ4hSdyEhceOK+DDYhe6Aj2ngMnBYOOpNXLWQMVcZasBTVVI2dErZ5N4qRBFQYhcyPauiVtaMJ6S+LTVE2GKj7k18+Qaqasf42SQcrAks8VNdvcmQi4cCPz5Z6PUE1jy5fOnFQ2TtXft+vU3xOgocc1EL1cS2/umZkhq+nH7/BIr1iNkdIWqy+U8P+uF/wTM1R31Z+DpHYb8XEiMriuc7gMcVxbTTK1TetJKcD6iRs+HrIRvl+Dr0haf/1TtDKijzdim1QIDAQAB';
$appkey2 = 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCjiaZXp15KJnkYTd4KBmBa7UOK6rNLE+ZKDZkSsAglJcorvQWNuDxpW50DY25y1VDBjJSQL3++pm72OJLunbgWNLDtGK9owH1a9lhXEA8ZVX3NCoGi2TLqfvxFHWQDxNZwo8XgyGDOUcml4xs30bg8MNb2giVW8JERPXGH5XyoHhib2dDTId9ZKzPC9AYt2oj+npe0/0ZnvVroycnlWKqwytzSd5eeuk7lJxCZiM8jFcalXuH7TBl+kPMckP1qeo+TTy2bU294j2QiHCcJLAbPAYkYxvfjSqvDGBfJH+CW/58lhIrmGoBO7nEdFY5mMj1yjUcfR7LnUlP4mrxUvdiVAgMBAAECggEAUru5hpCql/K8wnnqQcIWDwoHaIjx6aKIl6And5fMlxZ2IAiBfb/d+CMb3PH5l2ipcTFwmz0ccFP8wN3AH610yu8fLuOVIfZ3tIP6DxmVuehRO2D8Uml1Y8KYV1LWHT4Ain2gBub4aohf7mdlhqi3fuPqbE1NHJ2ZllIyVGDZ3qaEGLBbGPonWNqwo+8zQnFcJm4W1qUH08PeL4lQWud7tyK8teE62k3UnZIuKdFvc46k2/FxuNovzffdIgKy6updpoAXJ4u5qCpDCTPRWMkpPQsE86Txunc9l5WmLFZ/SIV/XqSotlaUDYFo6J/yaygiLbDhv0EMOcvRL7mfT6o7QQKBgQDP74puUAdd7fFluYiPckVMX3ZPQrBNe+kz7mHWcBSqfXVayvF/YIELggmAcBNO0t/9ij5hkILHLgyQbYJl0WBXcVBzvImPx8NwpiyMlj1L/Qd3UtVSrJXdLKxY4D9Cvizfs17llv/bJFa/NzPUWZL4BVmw6MSyAQVmMQHL/OO6cQKBgQDJVuLBX6FBXOEbOM6C4ZEnxpqKdUJQQDOqTI6S5sMsdkY2lGhC+fROChsWLkPGHyniQeij+qx4O5BmKMzYhZSsw6KJSmTG8dV6R3aLXVUFf8gQ9w9apVZh9TSv3Gb+UxeDwFkm32HZG2K/J7rtRYwO2JLVXU4f0SLVFyjFZxrqZQKBgEmBFP9f3OrQVRgvmN2UeHjB+jGUknwhhFNuPjmujy+hf92jhfEQLS3jPvafJ8QieTnIJ7sXeZNtbNWVUJYriJIApX70M/CWnjjxFShxZ6O4A26j4nMCPUvdIeOdCd/PGE/PuYkRcsqFswCPRAwZygFQ6t7Fudpuz/jK9Cam892RAoGANqA5M4fzo11EwfL+rKnwjR8oTMqVrFpO6jSNNjQf3g9U63gGda2FaCr7wF/bCYTpAzconFzlsFVQzzbgpRpRBTKrBZ7GiueQKPX8psEy0SQjLt8pLknPjxJNMi2VUAlRRvDH/3D6BkKU3xIzeC63WkvOQs9m4+EFF1WKPUzFE7UCgYBeWiBEbEy9HbSvfUELXQ7QPlmP9x3jht7p/J3JZjdCDehb5AGH0x2nG1P46WUi9TmQJE870+AFdvDHzrDIGK448Eo7Jc9UwH3CAQqlMpRbukdrChJuRN/tL0s+ECzrTYA4sIuxtkRkxTvOM5lHKmjHwosVyOnGVwli9x8lIke+oA==';

// CDN配置
if($conf['cdnpublic']==1){
    $cdnpublic = '//lib.baomitu.com/';
}elseif($conf['cdnpublic']==2){
    $cdnpublic = 'https://cdn.bootcdn.net/ajax/libs/';
}elseif($conf['cdnpublic']==4){
    $cdnpublic = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/';
}elseif($conf['cdnpublic']==5){
    $cdnpublic = '//mymapi.top/cdn/';
}else{
    $cdnpublic = '//cdn.staticfile.org/';
}

// 二维码API配置
if($conf['qrpublic']==1){
    $qrcodeapi = "//qun.qq.com/qrcode/index?data=";
}elseif($conf['qrpublic']==2){
    $qrcodeapi = "//api.qrserver.com/v1/create-qr-code/?size=150%C3%97150&data=";
}elseif($conf['qrpublic']==3){
    $qrcodeapi = "//blog.jjonline.cn/project/qrcode/?size=150&label=MYMPAY&data=";
}elseif($conf['qrpublic']==4){
    $qrcodeapi = "//ksurl.cn/createqrcode?contentType=URL&toShortUrl=false&content=";
}elseif($conf['qrpublic']==0){
    $qrcodeapi = '//api.iizi.cn/QR?test=';
}elseif($conf['qrpublic']==5){
    $qrcodeapi = '//minico.qq.com/qrcode/get?type=2&r=2&size=150&text=';
}else{
    $qrcodeapi = "//api.qrserver.com/v1/create-qr-code/?size=150%C3%97150&data=";
}

$mymapi = 'https://mymapi.top/';
?>
