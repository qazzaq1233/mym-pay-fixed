<?php
require_once('../Mym/Common.php');
$trade_no=daddslashes($_GET['trade_no']);
$srow=$DB->query("SELECT * FROM pay_order WHERE trade_no='{$trade_no}' limit 1")->fetch();
if(!$srow){
    $result=array("code"=>-1,"msg"=>'该订单号不存在',"data"=>array("backurl"=>'/'));
    exit(json_encode($result,JSON_UNESCAPED_UNICODE));
}

function mym_pay_success_result($srow){
    $url=creat_callback($srow);//订单支付成功
    $data=array("backurl"=>$url['return']);
    return array("code"=>200,"msg"=>'订单支付成功',"data"=>$data);
}

if($srow['status']==1){
    $result=mym_pay_success_result($srow);
}elseif($srow['outtime']<=time()){//订单支付超时
	$data=array("backurl"=>'http://'.getdomain($srow['return_url']));
	$result=array("code"=>-1,"msg"=>'订单支付超时',"data"=>$data);
}elseif($srow['price']<=0.00){//二维码获取失败
	$data=array("backurl"=>'http://'.getdomain($srow['return_url']));
	$result=array("code"=>-1,"msg"=>'订单异常,请稍后重试',"data"=>$data);
}else{
    $result=array("code"=>100,"msg"=>'请扫码支付');
    $QR_row=$DB->query("SELECT * FROM pay_qrlist WHERE id='{$srow['qr_id']}' and status='1' limit 1")->fetch();
    if($QR_row){
        if($QR_row['type']=='usdt'){
            check_usdt($QR_row,true);
        }elseif($QR_row['hook_type']==0 and $QR_row['type']!='wxpay'){
            check_money_notify($QR_row,true);
        }elseif($QR_row['channel']=='yd_vzq'){
            yunck_money_notify($QR_row,true);
        }elseif($QR_row['hook_type']==2 and $QR_row['type']=='wxpay'){
            wxyun_time_cron($QR_row,true);
        }elseif($QR_row['hook_type']==2){
            yunck_money_notify($QR_row,true);
        }

        // 本次轮询触发检测后立即重新读取订单状态，避免已到账却还要等下一轮轮询才跳转。
        $new_srow=$DB->query("SELECT * FROM pay_order WHERE trade_no='{$trade_no}' limit 1")->fetch();
        if($new_srow && $new_srow['status']==1){
            $result=mym_pay_success_result($new_srow);
        }
    }
}
exit(json_encode($result,JSON_UNESCAPED_UNICODE));
?>