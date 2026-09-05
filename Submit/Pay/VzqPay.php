<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="zh-cn">
<meta name="renderer" content="webkit">
<title>微信支付</title>
<link href="./assets/css/pay.css?2023004" rel="stylesheet" media="screen">
</head>
<body>
<div class="body">
<h1 class="mod-title">
    <span class="ico_log ico-weixin" style="margin-top: 5px;"></span>
</h1>
<div class="mod-ct">
    <div class="amount">￥<?=$srow['price']?></div>
<?php if($receiver_surname!==''){?><div style="color:#e60012;font-size:20px;font-weight:bold;margin:8px 0;">姓：<?=$receiver_surname_html?></div><?php }?>
<div class="qr-image" id="qrcode">
</div>
<span style="color: red;font-size: 23px; font-weight: bold;">请在浏览器内点击按钮付款</span></br>
<span style="color: red;font-size: 23px; font-weight: bold;">一定要在浏览器内，禁止电脑付款</span>
<div class="appcenter" style="margin-top: 20px; background-color: rgb(33, 136, 56); display: none;" id="h5JumpQq">
    <a style="font-size: 20px; font-weight: bold;color: #fff;">微信付款1(需安装QQ且登陆） </a>
</div>
<div class="appcenter" style="margin-top: 20px; background-color: rgb(33, 136, 56); display: none;" id="h5JumpSina"> 
<a style="font-size: 20px; font-weight: bold;color: #fff;">微信付款2(需安装微博) </a> 
</div>
<div class="warringMsg" style="color: #ffa100; font-size: 14px;">
    <span>请勿修改金额 或 重复支付，否则可能无法到账，造成的损失平台概不负责。</span>
</div>
<div class="time-item" style="padding-top: 10px"> 
    <div class="time-item"> <h1></h1> </div> 
    <div class="time-item"> <h1>订单:<?=$srow['trade_no']?></h1> </div>
    <div class="hidden" id="coundown" style="display: block;">
        <strong id="hour_show" style="display: none">0时</strong>
        <strong id="minute_show" style="color:#3090c0;font-size: 20px;">0分</strong>
        <strong id="second_show" style="color:#3090c0;font-size: 20px;">0秒</strong>
    </div> 
</div>
</div>
</div>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script>
    var u = navigator.userAgent;
    console.log(navigator.userAgent.match())
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Adr') > -1; //android终端

    var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
    function test() {
        if (isAndroid == true) {
            return 'Android';
        } else if(isiOS == true) {
            return 'iOS';
        }
    }
    if(test() == 'Android'){
        $("#h5JumpQq").show();
        $("#h5JumpSina").show();
    }else if(test() == 'iOS'){
        $("#h5JumpQq").show();
        $("#h5JumpSina").show();
    }
    $("#h5JumpQq").click(function(){
        window.location.href='<?=$qqh5?>';
	});
	$("#h5JumpSina").click(function(){
        window.location.href='<?=$wbh5?>';
	});
	<?php if($device=='pc'){?>
    $('#qrcode').qrcode({
        text: window.location.href,
        width: 230,
        height: 230,
        foreground: "#000000",
        background: "#ffffff",
        typeNumber: -1
    });
    <?php }?>
    var intDiff = parseInt('<?=$outtime?>'); //倒计时总秒数量
        timer(intDiff);

    function timer(intDiff) {
        console.log('start');
        console.log(intDiff);
        window.setInterval(function(){
        var day=0,
            hour=0,
            minute=0,
            second=0;//时间默认值
            if(intDiff > 0){
                day = Math.floor(intDiff / (60 * 60 * 24));
                hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
            }
            if (minute <= 9)
                minute = '0' + minute;
            if (second <= 9)
                second = '0' + second;
            $('#hour_show').html('<s id="h"></s>' + hour + '时');
            $('#minute_show').html(minute + '分');
            $('#second_show').html(second + '秒');
            intDiff--;
        }, 1000);
    }
    // 检查是否支付完成
    function loadmsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "Mym_Get.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {trade_no: "<?php echo $srow['trade_no']?>"}, //post数据
            success: function (result, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (result.code == 200) {
					layer.msg('支付成功，正在跳转中...', {icon: 16,shade: 0.1,time: 15000});
                    window.location.href=result.data.backurl;
                }else if(result.code == '-1'){
                    layer.confirm(result.msg, {
                        icon: 2,
                        title: '支付失败',
                        btn: ['确认'] //按钮
                    }, function(){
                        location.href=result.data.backurl
                    });
                }else{
                    setTimeout("loadmsg()", 1000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout("loadmsg()", 1000);
                } else { //异常
                    setTimeout("loadmsg()", 3000);
                }
            }
        });
    }
    window.onload = loadmsg();
    
    window.onload = function(){
	    var device = '<?=$device?>';//支付方式
	    if(device=='mobile'){
	        layer.msg('正在自动唤醒微信...', {shade: 0,time: 1000});
	        setTimeout(window.location.href="<?=$qqh5?>", 3000);
	    }
	}
</script>
</body>
</html>