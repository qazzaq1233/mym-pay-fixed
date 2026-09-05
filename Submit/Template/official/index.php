<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>在线支付 - <?php echo $typeName ?> - 安全收银台</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="stylesheet" type="text/css" href="<?php echo PAYSTATIC_ROOT?>css/qrcode.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
html,body{width:100%;min-height:100%;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Microsoft YaHei",Arial,sans-serif;background:#f5f7fb;color:#1f2937;}
img{max-width:100%;border:0;}
a{text-decoration:none;}
.pay-page{min-height:100vh;background:linear-gradient(180deg,#eef7ff 0,#f5f7fb 42%,#f7f8fb 100%);}
.pay-topbar{height:68px;background:#fff;border-bottom:1px solid #e5e7eb;box-shadow:0 2px 10px rgba(15,23,42,.03);}
.pay-topbar-inner{width:1080px;max-width:92%;height:68px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;}
.pay-brand{display:flex;align-items:center;gap:12px;font-size:18px;font-weight:600;color:#111827;}
.pay-brand-icon{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#1677ff,#13c2c2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:700;}
.pay-security{font-size:13px;color:#6b7280;display:flex;align-items:center;gap:8px;}
.pay-security-dot{width:8px;height:8px;border-radius:50%;background:#16a34a;box-shadow:0 0 0 4px rgba(22,163,74,.12);}
.pay-main{width:1080px;max-width:92%;margin:36px auto 0;display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:24px;align-items:start;}
.pay-card{background:#fff;border:1px solid #e8edf3;border-radius:14px;box-shadow:0 14px 40px rgba(15,23,42,.08);overflow:hidden;}
.pay-card-header{padding:24px 28px;border-bottom:1px solid #eef2f7;display:flex;align-items:center;justify-content:space-between;gap:16px;}
.pay-title{display:flex;align-items:center;gap:12px;}
.pay-logo{height:32px;min-width:32px;object-fit:contain;}
.pay-title-text{font-size:18px;font-weight:600;color:#111827;}
.pay-subtitle{margin-top:4px;font-size:13px;color:#6b7280;}
.pay-status{padding:7px 12px;border-radius:999px;background:#ecfdf3;color:#15803d;font-size:13px;white-space:nowrap;}
.pay-content{padding:28px;display:grid;grid-template-columns:1fr 260px;gap:28px;align-items:center;}
.order-info{min-width:0;}
.info-row{padding:14px 0;border-bottom:1px solid #f1f5f9;display:flex;align-items:flex-start;justify-content:space-between;gap:18px;}
.info-row:first-child{padding-top:0;}
.info-row:last-child{border-bottom:0;}
.info-label{font-size:14px;color:#6b7280;white-space:nowrap;}
.info-value{font-size:14px;color:#111827;text-align:right;word-break:break-all;}
.money-box{margin-top:22px;padding:20px;border-radius:12px;background:#f8fbff;border:1px solid #e5f0ff;}
.money-label{font-size:13px;color:#6b7280;margin-bottom:6px;}
.money-value{font-size:34px;line-height:1.15;font-weight:700;color:#1677ff;letter-spacing:-.5px;}
.money-value font{font-size:13px!important;font-weight:500!important;letter-spacing:0!important;}
.money-tips{margin-top:10px;font-size:13px;color:#ef4444;}
.qr-panel{padding:18px;border:1px solid #edf2f7;border-radius:14px;background:#fff;text-align:center;box-shadow:0 8px 24px rgba(15,23,42,.05);}
.qr-title{font-size:14px;font-weight:600;color:#111827;margin-bottom:14px;}
.qrcode{width:210px;height:210px;margin:0 auto;display:flex;align-items:center;justify-content:center;background:#fff;border:1px solid #edf2f7;border-radius:10px;overflow:hidden;}
.qrcode img{width:200px;height:200px;object-fit:contain;display:block;}
.h5-btn-wrap{margin-top:14px;}
#payh5url a,.h5-pay-btn{display:inline-flex;align-items:center;justify-content:center;width:100%;height:42px;border-radius:8px;background:#1677ff;color:#fff!important;font-size:14px;font-weight:600;box-shadow:0 8px 18px rgba(22,119,255,.22);}
.pay-info-bar{padding:18px 28px;background:#fbfdff;border-top:1px solid #eef2f7;display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;}
.pay-mini{display:flex;align-items:center;gap:10px;color:#4b5563;font-size:13px;min-width:0;}
.pay-mini-icon{width:28px;height:28px;border-radius:50%;background:#eaf3ff;color:#1677ff;display:flex;align-items:center;justify-content:center;font-size:14px;flex:0 0 auto;}
.pay-side{background:#fff;border:1px solid #e8edf3;border-radius:14px;box-shadow:0 14px 40px rgba(15,23,42,.06);padding:26px;text-align:center;}
.pay-side-title{font-size:17px;font-weight:600;color:#111827;margin-bottom:8px;}
.pay-side-desc{font-size:13px;line-height:1.8;color:#6b7280;margin-bottom:20px;}
.pay-side-img{width:260px;max-width:100%;margin:0 auto 20px;display:block;}
.pay-steps{display:grid;gap:10px;text-align:left;}
.pay-step{display:flex;align-items:flex-start;gap:10px;padding:12px;border-radius:10px;background:#f8fafc;color:#4b5563;font-size:13px;line-height:1.5;}
.pay-step-num{width:20px;height:20px;border-radius:50%;background:#1677ff;color:#fff;font-size:12px;display:flex;align-items:center;justify-content:center;flex:0 0 auto;margin-top:1px;}
.pay-footer{width:1080px;max-width:92%;margin:26px auto 0;padding-bottom:30px;text-align:center;color:#94a3b8;font-size:12px;}
.pay-alert{margin-top:12px;color:#ef4444;font-size:13px;cursor:pointer;}
#divTime{color:#1677ff;font-weight:600;}
@media (max-width:900px){
    .pay-main{grid-template-columns:1fr;margin-top:18px;}
    .pay-content{grid-template-columns:1fr;}
    .pay-side{display:none;}
    .pay-info-bar{grid-template-columns:1fr;}
}
@media (max-width:520px){
    .pay-topbar{height:58px;}
    .pay-topbar-inner{height:58px;max-width:94%;}
    .pay-brand{font-size:16px;}
    .pay-security{display:none;}
    .pay-main{max-width:94%;}
    .pay-card-header{padding:18px;align-items:flex-start;flex-direction:column;}
    .pay-content{padding:18px;gap:20px;}
    .info-row{display:block;}
    .info-value{text-align:left;margin-top:7px;}
    .money-value{font-size:30px;}
    .qrcode{width:205px;height:205px;}
    .qrcode img{width:195px;height:195px;}
    .pay-info-bar{padding:16px 18px;}
}
</style>
</head>
<body>
<div class="pay-page">
    <div class="pay-topbar">
        <div class="pay-topbar-inner">
            <div class="pay-brand">
                <div class="pay-brand-icon">¥</div>
                <div>官方收银台</div>
            </div>
            <div class="pay-security"><span class="pay-security-dot"></span>安全支付环境已开启</div>
        </div>
    </div>

    <div class="pay-main">
        <div class="pay-card">
            <div class="pay-card-header">
                <div class="pay-title">
                    <img class="pay-logo" src="<?php echo PAYSTATIC_ROOT?>img/<?=$type?>-logo.png" alt="<?=$typeName?>">
                    <div>
                        <div class="pay-title-text"><?=$typeName?>支付</div>
                        <div class="pay-subtitle">请确认订单信息后完成付款</div>
                    </div>
                </div>
                <div class="pay-status">等待支付</div>
            </div>

            <div class="pay-content">
                <div class="order-info">
                    <div class="info-row">
                        <div class="info-label">商品名称</div>
                        <div class="info-value"><?=$srow['name']?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">商户订单号</div>
                        <div class="info-value"><?=$trade_no?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">支付方式</div>
                        <div class="info-value"><?=$typeName?></div>
                    </div>
                    <?php if($receiver_surname!==''){?>
                    <div class="info-row">
                        <div class="info-label">收款人姓</div>
                        <div class="info-value" style="color:#ef4444;font-weight:600;">姓：<?=$receiver_surname_html?></div>
                    </div>
                    <?php }?>
                    <?php if($userrow['mali']==1){?>
                    <div class="info-row">
                        <div class="info-label">联系QQ</div>
                        <div class="info-value"><?=$userrow['qq']?></div>
                    </div>
                    <?php }if($userrow['mali']==2){?>
                    <div class="info-row">
                        <div class="info-label">联系方式</div>
                        <div class="info-value"><?=$userrow['set_mali']?></div>
                    </div>
                    <?php }?>
                    <?php if($QR_row['crontime']>time() and $QR_row['status']!=1){?>
                    <div class="info-row">
                        <div class="info-label">账号状态</div>
                        <div class="info-value" style="color:#ef4444;">商家账号状态未在线</div>
                    </div>
                    <?php }?>
                    <div class="money-box">
                        <div class="money-label">应付金额</div>
                        <div class="money-value" id="price">￥<?=$price?></div>
                        <div class="money-tips">请严格按照页面显示金额支付，不可多付或少付。</div>
                    </div>
                    <?php if($type=='alipay'){
                        echo '<div class="pay-alert" onclick="open();">支付宝无法付款？点此查看提示</div>';
                    }?>
                </div>

                <div class="qr-panel">
                    <div class="qr-title">请使用<?=$typeName?>扫一扫</div>
                    <div class="qrcode" id="qrcode"><img id="qrcode_load" src="<?php echo PAYSTATIC_ROOT?>img/loading.gif" style="display:block;"></div>
                    <div class="h5-btn-wrap"><a id="payh5url"></a></div>
                </div>
            </div>

            <div class="pay-info-bar">
                <div class="pay-mini"><span class="pay-mini-icon">1</span><span id="divTime">正在获取二维码，请稍等...</span></div>
                <div class="pay-mini"><span class="pay-mini-icon">2</span><span>付款完成后页面将自动跳转</span></div>
                <div class="pay-mini"><span class="pay-mini-icon">3</span><span>请勿重复支付同一订单</span></div>
            </div>
        </div>

        <div class="pay-side">
            <div class="pay-side-title"><?=$typeName?>扫码支付</div>
            <div class="pay-side-desc">打开对应支付客户端，扫描左侧二维码完成付款。付款成功后请等待系统自动确认。</div>
            <img class="pay-side-img" src="<?php echo PAYSTATIC_ROOT?>img/<?=$type?>-sys.png" alt="扫码示意">
            <div class="pay-steps">
                <div class="pay-step"><span class="pay-step-num">1</span><span>确认商品名称和支付金额。</span></div>
                <div class="pay-step"><span class="pay-step-num">2</span><span>使用<?=$typeName?>扫描二维码。</span></div>
                <div class="pay-step"><span class="pay-step-num">3</span><span>按页面金额完成付款，等待到账。</span></div>
            </div>
        </div>
    </div>

    <div class="pay-footer">本页面由安全收银台提供技术支持，请在二维码有效期内完成支付。</div>
</div>
</body>
</html>
<?php
if($userrow['music']==1){
?>
<audio autoplay=""><source src="<?php echo $mp3;?>"></audio>
<?php }?>
<script src="<?php echo $cdnpublic?>clipboard.js/1.7.1/clipboard.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script>
function judgeClient() {
    let client = '';
    if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {
        client = 'iOS';
    } else if (/(Android)/i.test(navigator.userAgent)) {
        client = 'Android';
    } else {
        client = 'PC';
    }
    return client;
}
function Is_Wx_Al(){
    var ua = window.navigator.userAgent;
    if (/MicroMessenger/.test(ua)) {
        return 'wxpay';
    } else if (/AlipayClient/.test(ua)) {
        return 'alipay';
    } else if (/QQ/.test(ua)) {
        return 'qqpay';
    } else {
        return '其他浏览器';
    }
}
</script>
<script>
var clipboard = new Clipboard('#copy_p', {
    text: function() {
        return $("#price").text();
    }
});

clipboard.on('success', function(e) {
    layer.msg("复制成功,请使用复制金额付款");
});

clipboard.on('error', function(e) {
    document.querySelector('#price');
    layer.msg("复制失败,请手动复制一下");
});

if(Is_Wx_Al()=='alipay'){
    $("#payBtn").slideUp();
}else if(Is_Wx_Al()=='wxpay'){
    $("#payBtn").slideDown();
}else{
    $("#payBtn").slideUp();
}
</script>
<script type="text/javascript">
var tips_pop = layer.open({
    time:3,
    shadeClose:1,
    anim:'up',
    shade:'background-color: rgba(0,0,0,.5)',
    className: 'popuo-pay-tips',
    title: [ '温馨提示', 'background-color: #1677ff; color:#fff;' ],
    content: '本次请按页面显示金额支付。请勿修改金额，否则无法自动到账。',
    btn: '我知道了',
    yes: function(index){
        $('.layui-m-layer').hide();
    }
});
setTimeout(function () {
    $('.layui-m-layer').hide();
},10000);

var intDiff = parseInt('<?=$outtime?>');
function timer(intDiff){
    window.setInterval(function(){
        var day=0,
            hour=0,
            minute=0,
            second=0;
        if(intDiff > 0){
            day = Math.floor(intDiff / (60 * 60 * 24));
            hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
            minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
            second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
        }
        if (minute <= 9) minute = '0' + minute;
        if (second <= 9) second = '0' + second;
        if (hour <= 0 && minute <= 0 && second <= 0) {

        }else{
            $("#divTime").html("二维码有效时间：<small style='color:#ef4444;font-size:20px'>" + minute + "</small>分<small style='color:#ef4444;font-size:20px'>" + second + "</small>秒，失效勿付");
        }
        intDiff--
    }, 1000);
}

$(function(){
    timer(intDiff);
});

updateQrOk = 0;
order(0);
function order(retry) {
    retry = retry || 0;
    $.ajax({
        type: "GET",
        dataType: "json",
        cache: false,
        timeout: 12000,
        url: "./Mym_Get.php",
        data: {trade_no: "<?php echo $trade_no?>", t: new Date().getTime()},
        success: function (result) {
            if (result.code == 200) {
                $("#divTime").html("<small style='color:#16a34a;font-size:20px'>"+ result.msg +"</small>");
                $("#qrcode").html('<img id="qrcode_load" src="<?php echo PAYSTATIC_ROOT?>img/pay_ok.png">');
                layer.msg('支付成功，正在跳转中...');
                window.location.href=result.data.backurl;
            }else if(result.code == '-1'){
                $("#qrcode").html('<img id="qrcode_load" src="<?php echo PAYSTATIC_ROOT?>img/qrcode_timeout.png">');
                $("#divTime").html("<small style='color:#ef4444;font-size:20px'>"+ result.msg +"</small>");
                layer.confirm(result.msg, {
                    icon: 2,
                    title: '支付失败',
                    btn: ['确认']
                }, function(){
                    location.href=result.data.backurl
                });
            }else{
                var pay_type = '<?=$type?>';
                var is_money = <?=$srow['money']?>;
                var price = '<?=$srow['price']?>';
                var qrcode = '<?=$qr_url?>';

                if(window.navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)){
                    $("#payh5url").html('<a type="button" href="<?=$payh5url?>" class="h5-pay-btn" target="_blank">唤醒<?=$typeName?>APP支付</a>');
                }
                if(updateQrOk==0){
                    updateQrOk=1;
                    if(is_money!=price){
                        layer.alert('温馨提示：'+is_money+'金额已被其他用户金额占用，请您务必付款<font color=red>'+price+'</font>元，<font color=red>多付一分或者少付一分都不能到账</font>！', {
                            icon: 1,
                            skin: 'layer-ext-moon'
                        });
                    }
                    $('#price').html('￥'+price+'<font color=red style="font-size:13px;margin-left:6px;">(不可多付或少付)</font>');
                    $("#qrcode").html('<img id="qrcode_load" src="'+ qrcode +'">');
                }
                setTimeout(function(){ order(0); }, 700);
            }
        },
        error: function(){
            setTimeout(function(){ order(retry + 1); }, retry < 3 ? 1000 : 2000);
        }
    });
}
window.onload = function(){
    var pay_type = '<?=$type?>';
    if(pay_type=='alipay'){
        var url_scheme = 'alipayqr://platformapi/startapp?saId=10000007&qrcode=<?=$h5api?>';
        layer.msg('正在自动唤醒支付宝...', {shade: 0,time: 1000});
        window.location.href = url_scheme;
    }else if(pay_type=='qqpay'){
        var url_scheme = 'QQH5.php?trade_no=<?=$trade_no?>';
        if(judgeClient() =='Android' ){
            layer.msg('正在自动唤醒QQ...', {shade: 0,time: 1000});
            window.location.href = url_scheme;
        }
    }
    setTimeout(function(){ order(0); }, 1000);
}
</script>
</body>
</html>
