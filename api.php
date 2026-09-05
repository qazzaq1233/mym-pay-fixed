<?php
$nosession = true;
require "./Mym/Common.php";
if(count($_GET))$queryArr=$_GET;else$queryArr=$_POST;
$act=isset($queryArr['act'])?daddslashes($queryArr['act']):null;
$url=daddslashes($queryArr['url']);

if($act=='query')
{
	$pid=intval($queryArr['pid']);
	$key=daddslashes($queryArr['key']);
	$row=$DB->getRow("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1");
	if($row){
		if($key==$row['key']){
			$orders=$DB->query("SELECT count(*) from pay_order WHERE pid={$pid}");

			$lastday=date("Y-m-d",strtotime("-1 day")).' 00:00:00';
			$today=date("Y-m-d").' 00:00:00';
			$order_today=$DB->query("SELECT sum(money) from pay_order where pid={$pid} and status=1 and endtime>='$today'")->fetchColumn();

			$order_lastday=$DB->query("SELECT sum(money) from pay_order where pid={$pid} and status=1 and endtime>='$lastday' and endtime<'$today'")->fetchColumn();

			$result=array("code"=>1,"pid"=>$pid,"key"=>$key,"type"=>$row['settle_id'],"active"=>$row['status'],"money"=>$row['money'],"account"=>$row['account'],"username"=>$row['username'],"settle_money"=>$conf['settle_money'],"settle_fee"=>$conf['settle_fee'],"money_rate"=>$conf['money_rate'],"orders"=>$orders,"order_today"=>$order_today,"order_lastday"=>$order_lastday);
		}else{
			$result=array("code"=>-2,"msg"=>"KEY校验失败");
		}
	}else{
		$result=array("code"=>-3,"msg"=>"PID不存在");
	}
}
elseif($act=='order'){
	$pid=intval($queryArr['pid']);
	$key=daddslashes($queryArr['key']);
	$row=$DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
	if($row){
		if($key==$row['key']){
			if(isset($queryArr['trade_no'])){
				$trade_no=daddslashes($queryArr['trade_no']);
				$row=$DB->query("SELECT * FROM pay_order WHERE pid='{$pid}' and trade_no='{$trade_no}' limit 1")->fetch();
			}elseif(isset($queryArr['out_trade_no'])){
				$out_trade_no=daddslashes($queryArr['out_trade_no']);
				$row=$DB->query("SELECT * FROM pay_order WHERE pid='{$pid}' and out_trade_no='{$out_trade_no}' limit 1")->fetch();
			}else{
				exit('{"code":-4,"msg":"参数不完整"}');
			}
			if($row['status']==2)$row['status']=0;
			if($row){
				$result=array("code"=>1,"msg"=>"查询订单号成功！","trade_no"=>$row['trade_no'],"out_trade_no"=>$row['out_trade_no'],"type"=>$row['type'],"pid"=>$row['pid'],"addtime"=>$row['addtime'],"endtime"=>$row['endtime'],"name"=>$row['name'],"money"=>$row['money'],"status"=>$row['status']);
			}else{
				$result=array("code"=>-1,"msg"=>"订单号不存在");
			}
		}else{
			$result=array("code"=>-2,"msg"=>"KEY校验失败");
		}
	}else{
		$result=array("code"=>-3,"msg"=>"PID不存在");
	}
}elseif($act=='orders'){
	$pid=intval($queryArr['pid']);
	$key=daddslashes($queryArr['key']);
	$limit=$queryArr['limit']?intval($queryArr['limit']):10;
	if($limit>50)$limit=50;
	$row=$DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
	if($row){
		if($key==$row['key']){
			$rs=$DB->query("SELECT * FROM pay_order WHERE pid='{$pid}' order by trade_no desc limit {$limit}");
			while($row=$rs->fetch()){
				$data[]=$row;
			}
			if($rs){
				$result=array("code"=>1,"msg"=>"查询订单记录成功！","data"=>$data);
			}else{
				$result=array("code"=>-1,"msg"=>"查询订单记录失败！");
			}
		}else{
			$result=array("code"=>-2,"msg"=>"KEY校验失败");
		}
	}else{
		$result=array("code"=>-3,"msg"=>"PID不存在");
	}
}elseif($act=='qrlist'){
    $i = $queryArr['i'];
    $t = $queryArr['t'];
    $result = yun_qrlist($i,$t);
}elseif($act=='update'){
    $appname = "即时到账辅助工具";//软件名称
    $notice = "欢迎使用即时到帐支付宝，财付通.微信即时到账辅助工具";//软件公告
    $result = array('appname' => $appname, 'notice' => $notice);
}elseif($act=='login'){
    $pid=intval($queryArr['pid']);
	$key=daddslashes($queryArr['key']);
    $row = $DB->query("SELECT * FROM `pay_user` WHERE `pid`='{$pid}' limit 1")->fetch();
    if ($queryArr['alipay'] == 1) {
        $ali_login = time() + 150;
    } else {
        $ali_login = $row['ali_login'];
    }
    if ($queryArr['wxpay'] == 1) {
        $wx_login = time() + 150;
    } else {
        $wx_login = $row['wx_login'];
    }
    if ($queryArr['qqpay'] == 1) {
        $qq_login = time() + 150;
    } else {
        $qq_login = $row['qq_login'];
    }
    if (!$row) {
        $result = array('code' => -1, 'msg' => 'pid错误');
    } elseif ($row['key'] != $key) {
        $result = array('code' => -2, 'msg' => 'key错误');
    } else {
        //$DB->query("update `pay_user` set `ali_login`='{$ali_login}',`alipid`='{$queryArr['alipid']}',`wx_login`='{$wx_login}',`qq_login`='{$qq_login}' where `pid`='{$pid}' limit 1");
        $result = array("code" => 1, "msg" => "登录成功!", "pid" => $row['pid'], "qq" => $row['qq'], "money" => $row['money'], "username" => $row['username'], "account" => $row['account'], "ali_login" => $row['ali_login'], "wx_login" => $row['wx_login'], "qq_login" => $row['qq_login'], "rate" => $row['rate'], "issmrz" => $row['issmrz'], "active" => $row['active'], "paymb" => $row['paymb']);
    }
}elseif($act=='Mcode_notify'){
    $pid=intval($queryArr['pid']);
	$key=daddslashes($queryArr['key']);
    $type = daddslashes($queryArr['type']);
    $money = number_format((float)daddslashes($queryArr['money']), 2, '.', '');
    $userrow = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();

    if (!$userrow) {
        $result = array('code' => -1, 'msg' => 'PID错误');
    } elseif ($userrow['key'] != $key) {
        $result = array('code' => -2, 'msg' => 'KEY错误');
    } else {
        $time = time();
        $money_sql = number_format((float)$money, 2, '.', '');
        $Qr=$DB->query("SELECT * FROM `pay_qrlist` WHERE `pid`='{$pid}' AND `type`='{$type}' AND `channel`='pc_alijk' AND `status`='1' AND `qr_status`='1' order by id desc limit 1")->fetch();
        if(!$Qr){
            $result = array("code"=>-1,"msg"=>"未找到在线的扫码CK通道");
        }else{
            $sql = "status='0' AND pid='{$pid}' AND type='{$type}' AND qr_id='{$Qr['id']}' AND outtime>'{$time}' AND ROUND(price,2)='{$money_sql}'";
            $srow = $DB->query("SELECT * FROM pay_order WHERE {$sql} order by addtime desc limit 1")->fetch();
            if($srow and $srow['status']==0){//发送通知给商户平台
                $url=creat_callback($srow);
                $datm=do_notify($url['notify']);
                if($datm)$datm='success';
                Add_log($pid,'自动回调订单(扫码CK监控)：'.$srow['trade_no']);
                pay_notify($pid,$type,$money_sql,$datm,$srow['trade_no']);
                exit('success');
            }else{
                $result = array("code"=>-1,"msg"=>"此金额匹配不到任何订单");
            }
        }
    }
}else{
	$result=array("code"=>-5,"msg"=>"No Act!");
}

exit(json_encode($result,JSON_UNESCAPED_UNICODE));

?>