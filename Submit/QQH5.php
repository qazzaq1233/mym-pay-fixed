<?php
include("../Mym/Common.php");
$trade_no=daddslashes($_GET['trade_no']);
$srow=$DB->query("SELECT * FROM pay_order WHERE trade_no='{$trade_no}' limit 1")->fetch();
if(!$srow)sysmsg('该订单号不存在，请返回来源地重新发起请求！');
if(!$srow['qr_url']){
    $qrcode = urlencode($DB->query("SELECT * FROM pay_qrlist WHERE id='{$srow['qr_id']}' limit 1")->fetch()['qr_url']);
}else {
   $qrcode = $srow['qr_url'];
}if
(!$_SESSION[$trade_no.'QR_H5']){
    $json =['code'=>1,'url'=>base64_encode('https://qun.qq.com/qrcode/index?data='.$qrcode.'&size=300?key=1E69337BC3D6004A9D18FB80982A7608&p=mymqqh5&p_key=fa8f9a9af9a&skey_type=2&url=&trade_no='.date("YmdHis").rand(11111,99999).'&f=wallet&shewm=mympay&mzfgw=&h5_ali=1&ali_uid=&h5_wx=1&h5_qq=1&subtitle=&_wv=131452&k=uh0SMX96&t='.time().rand(11111,99999).'.html')];
    if($json['code']==1){
        if (get_device_type()=='android') {
            $_SESSION[$trade_no.'QR_H5'] = 'mqqapi://forward/url?version=1&src_type=web&souce=oicqzone.com&version=1&src_type=web&url_prefix='.$json['url'].'';
        }else{
            $_SESSION[$trade_no.'QR_H5'] = 'mqqopensdkapi://bizAgent/qm/qr?url='.$qrcode;
        }
        header('location:'.$_SESSION[$trade_no.'QR_H5']);
    }else{
        
    }
}else{
    header('location:'.$_SESSION[$trade_no.'QR_H5']);
}
