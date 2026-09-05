<?php
if(preg_match('/Baiduspider/', $_SERVER['HTTP_USER_AGENT']))exit;
$nosession = true;
require './Mym/Common.php';
$time=time();//当前时间戳
$act=$_GET['act'];
$key=daddslashes($_GET['key']);
$limit=10;//每次执行条数
if($key!=$conf['cronkey']){
    exit('您好，您的KEY数据错误，请重新输入');
}
if($act=='wxyun' or $act=='yund'){//云端监控
	$rs=$DB->query("SELECT * from pay_qrlist WHERE status='1' and hook_type='2' and crontime<'{$time}' order by rand() limit {$limit}");
	while($row = $rs->fetch())
	{	
	    if($row['type']!='wxpay' or $row['channel']=='yd_vzq'){
	        echo yunck_money_notify($row,true);
	    }elseif($row['type']=='wxpay'){
	        
	        echo wxyun_time_cron($row,true);
	    }
	}
}elseif($act=='Order_notify'){//自动补单监控
    $rs=$DB->query("SELECT * from pay_qrlist WHERE status='1' and type!='wxpay' and Order_time<'{$time}' order by rand() limit {$limit}");
	while($row = $rs->fetch())
	{	
		$data = Order_notify($row);
		echo $data;
	}
}elseif($act=='user'){//会员状态更改
    $rs=$DB->query("SELECT * from pay_user WHERE `user_vip_time`<'{$date}' and `user_vip`>='1' order by rand() limit {$limit}");
    while($row = $rs->fetch())
    {
        $pack=$DB->query("SELECT * FROM `pay_taocan` WHERE `id`='{$row['user_vip']}' limit 1")->fetch();
        if($row['money']>$pack['edu']){
            $money = $row['money']-$pack['edu'];
        }else{
            $money = 0.00;
        }
        $DB->exec("update `pay_user` set `money`='{$money}',`user_vip` ='0' where `pid`='{$row['pid']}'");
    }
}elseif($act=='order'){
    $DB->exec("DELETE FROM `pay_order` WHERE status=0 and date<'".date("Y-m-d",strtotime("-2 days"))."'");
    $DB->exec("OPTIMIZE TABLE `pay_order`");
    echo '数据清理成功'.$date;
    
}elseif($act=='wxpayyun'){
    $rs=$DB->query("SELECT * from pay_wechat_trumpet WHERE status='1' and hook_type='1' order by rand() limit {$limit}");
	while($row = $rs->fetch())
	{	
	    $data = Yun_WxPay($row);
		echo $data;
	}
}elseif($key==$conf['cronkey']){//普通免挂监控
	//遍历所有正常二维码
	$rs=$DB->query("SELECT * from pay_qrlist WHERE status='1' and hook_type='0' and type!='wxpay' and crontime<'{$time}' order by rand() limit {$limit}");
	while($row = $rs->fetch())
	{	
	    if($row['type']=='usdt'){
	        $data = check_usdt($row,true);
	    }else{
	        $data = check_money_notify($row,true);
	    }
		echo $data;
	}
	$rs=$DB->query("SELECT * from pay_qrlist WHERE status='1' and hook_type='0' and channel='mg_vzq' and crontime<'{$time}' order by rand() limit {$limit}");
	while($row = $rs->fetch())
	{
	    $data = check_money_notify($row,true);
		echo $data;
	}
	if(!$data or !$rs->fetch())
	echo 'Cron Ok ';
}
?>