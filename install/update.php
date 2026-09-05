<?php
error_reporting(0);
require '../Mym/Config.php';
require '../Mym/authcode.php';

@header('Content-Type: text/html; charset=UTF-8');

try{
	$db=new PDO("mysql:host=".$dbconfig['host'].";dbname=".$dbconfig['dbname'].";port=".$dbconfig['port'],$dbconfig['user'],$dbconfig['pwd']);
}catch(Exception $e){
	exit('链接数据库失败:'.$e->getMessage());
}
date_default_timezone_set("PRC");
$date = date("Y-m-d");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
$db->exec("set sql_mode = ''");
$db->exec("set names utf8");

$version = 0;
if($rs = $db->query("SELECT v FROM pay_config WHERE k='version'")){
	$version = $rs->fetchColumn();
}
if($version<1408){
    $sqls = file_get_contents('1408.sql');
	$sqls=explode(';', $sqls);
    $sqls[]="UPDATE `pay_config` SET `v` = '1408' where `k` = 'version'";
}elseif($version<1409){
    $sqls = file_get_contents('1409.sql');
	$sqls=explode(';', $sqls);
    $sqls[]="UPDATE `pay_config` SET `v` = '1409' where `k` = 'version'";
}elseif($version<1410){
    $sqls = file_get_contents('1410.sql');
	$sqls=explode(';', $sqls);
    $sqls[]="UPDATE `pay_config` SET `v` = '1410' where `k` = 'version'";
}elseif($version<1411){
    $sqls = file_get_contents('1411.sql');
	$sqls=explode(';', $sqls);
    $sqls[]="UPDATE `pay_config` SET `v` = '1411' where `k` = 'version'";
}elseif($version<1412){
    $sqls = file_get_contents('1411.sql');
	$sqls=explode(';', $sqls);
    $sqls[]="UPDATE `pay_config` SET `v` = '1412' where `k` = 'version'";
}elseif($version<DB_VERSION){
    $sqls = file_get_contents(DB_VERSION.'.sql');
	$sqls=explode(';', $sqls);
    $sqls[]="UPDATE `pay_config` SET `v` = '".DB_VERSION."' where `k` = 'version'";
}else{
	exit('你的网站已经升级到最新版本了');
}
$success=0;$error=0;$errorMsg=null;
foreach ($sqls as $value) {
	$value=trim($value);
	if(!empty($value)){
		$value = str_replace('pre_','pay_',$value);
		if($db->exec($value)===false){
			$error++;
			$dberror=$db->errorInfo();
			$errorMsg.=$dberror[2]."<br>";
		}else{
			$success++;
		}
	}
}
echo '成功执行SQL语句'.$success.'条！<br/>';
if (file_exists('../Mym/cache/auth/authcode.ini')) {
    unlink('../Mym/cache/auth/authcode.ini');
}
if($errorMsg){
}
echo '<hr/><a href="/">点此返回首页</a>';
?>