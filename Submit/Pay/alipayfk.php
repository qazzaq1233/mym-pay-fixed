<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://cdn.staticfile.org/layer/2.3/skin/layer.css" id="layui_layer_skinlayercss" style=""></head>
<body>&#xFEFF;
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta name="apple-mobile-web-app-capable" content="no">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="format-detection" content="telephone=no,email=no">
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Cache" content="no-cache">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta itemprop="name" content="→→→点击这里继续进行支付←←←">
    <meta itemprop="description" name="description" content="扫码支付">
    
    <title>在线支付 - <?php echo $typeName ?> - 网上支付 安全快速！</title>
    <link rel="shortcut icon" href="https://csjhq.gitee.io/web/favicon.ico">

    <link href="./Template/111111/assets/css/yu.css?" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="./Template/111111/assets/js/yu.js?"></script>
    <link href="./Template/111111/assets/css/pay.css?" rel="stylesheet" media="screen">
    <link href="//lib.baomitu.com/css/bank_flash.css?" rel="stylesheet" media="screen">
    <link href="<?php echo $cdnpublic?>toastr.js/2.1.4/toastr.min.css" rel="stylesheet">
    <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<div class="body">
    <h1 class="mod-title">
        <!--<span class="ico_log ico-alipay"></span>-->
        <img src="./Template/default/assets/img/<?=$type?>-logo.png" alt="" style="height:30px;">
    </h1>
	<div class="mod-ct">
        <div class="order">
    </div>
    <div class="amount" id="money" style="position: relative;">￥<span id="copy_money"><?=$price?></span> 
        <div style="position: absolute;font-size: 10px;top: 0px;left: 70%;"><a href="#" id="copy_p" style="color: blue;font-size: 16px;word-break: keep-all;">【复制金额】</a></div>
    </div>
    <div class="amount" id="moneycs" style="position: relative;"></div>
    <div class="time-item" style="padding-top: 10px;color:red">
        <div class="time-item" id="msg2"><h1>请在规定时间内支付<?=$price?>，勿多付少付</h1></div>
        <?php if($receiver_surname!==''){?><div class="time-item" style="color:#333;font-size:18px;font-weight:bold;">姓：<?=$receiver_surname_html?></div><?php }?>
    </div>
    <div class="qrcode-img-wrapper" data-role="qrPayImgWrapper">
        <div data-role="qrPayImg" class="qrcode-img-area">
            <div class="ui-loading qrcode-loading" data-role="qrPayImgLoading" style="display: none;"></div>
            <div style="position: relative;display: inline-block;">	
            <center><p class="qrcode" id="qrcode"><img id="qrcode_load" src="./Template/default/assets/img/loading.gif" style="display: block;"></img></p></center>
        </div>
    </div>
</div>
<div class="payweixinbtn" style="display: none;padding-top: 10px">
    <a target="_blank" download="" id="downloadbtn" class="btn btn-primary" style="display:none">截屏二维码到【微信扫一扫】</a>
    <div class="tps_btn" style="padding-top: 10px;">
        <a href="weixin://" id="copy_payurl" style="color: #fff;text-decoration: none; text-align: center;padding: .55rem 0; display: inline-block; width: 88%; border-radius: .3rem; font-size: 14px;background-color: #428bca; border: 1px solid #428bca;letter-spacing:normal;font-weight: normal">截屏扫码&nbsp;&nbsp;或&nbsp;&nbsp;点此复制链接到微信内打开</a>
    </div>
</div>
<div class="iospayweixinbtn" style="display: none;padding-top: 10px">
    <div class="tps_btn" style="padding-top: 10px;"><a href="weixin://" id="copy_payurl" style="color: #fff;text-decoration: none; text-align: center;padding: .55rem 0; display: inline-block; width: 88%; border-radius: .3rem; font-size: 14px;background-color: #428bca; border: 1px solid #428bca;letter-spacing:normal;font-weight: normal">截屏扫码&nbsp;&nbsp;或&nbsp;&nbsp;点此复制链接到微信内打开</a></div>
</div>

<div class="time-item" style="padding-top: 10px;color:red">
    <strong id="hour_show"><s id="h"></s>0时</strong>
    <strong id="minute_show"><s></s>00分</strong>
    <strong id="second_show"><s></s>00秒</strong>
    <div class="time-item" id="msg3"><h1>支付完成后，请在此页面_等待自动跳转</h1> </div>
</div>

<div class="tps_btn" style="padding-top: 10px;"><a id="h5_ali" href="<?=$payh5url?>" target="_blank" style="color: #fff;text-decoration: none; text-align: center;padding: .55rem 0; display: inline-block; width: 88%; border-radius: .3rem; font-size: 14px;background-color: #428bca; border: 1px solid #428bca;letter-spacing:normal;font-weight: normal">立即启动<?php echo $typeName?>APP支付</a></div>

<button type="button" id="wx_tz" onclick="call('wechatTimeline')" style="display:none;">朋友圈_自动</button>

<div class="tip-text"></div>
<div class="detail" id="orderDetail">
    <dl class="detail-ct" id="desc" style="display: none;">
        <dt>商品名称</dt><dd>支付测试|UID:1000</dd>
        <dt>商户订单号</dt><dd><?=$trade_no?></dd>
        <dt>系统订单号</dt><dd><?=$srow['out_trade_no']?></dd>
        <dt>创建时间</dt><dd><?=$srow['addtime']?></dd>
        <dt>过期时间</dt><dd><?php echo date("Y-m-d H:i:s",$srow['outtime']);?></dd>
        <dt>重要提醒</dt><dd>支付完成后，请返回此页面等待自动跳转</dd>
    </dl>
    <a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
</div>
</div>
</div>

<script src="<?php echo $cdnpublic?>clipboard.js/1.7.1/clipboard.min.js"></script>
<!-- Toastr -->
<script src="//lib.baomitu.com/toastr.js/2.1.4/toastr.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script>
function judgeClient() {
    let client = '';
    if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) { //判断iPhone|iPad|iPod|iOS
        client = 'iOS';
    } else if (/(Android)/i.test(navigator.userAgent)) { //判断Android
        client = 'Android';
    } else {
        client = 'PC';
    }
    return client;
}
var clipboard = new Clipboard('#copy_p', {
    text: function() {
        return $("#copy_money").text();
    }
});

var clipboard_payurl = new Clipboard('#copy_payurl', {
    text: function() {
        return $("#copy_payurl_wxtxt").text()+"请粘贴到微信聊天框后进入支付。支付完成后，务必返回网页支付页面";
    }
});

var clipboard_payurl_1 = new Clipboard('#copy_payurl_1', {
    text: function() {
        return $("#copy_payurl_1").text()+"请粘贴到微信聊天框后进入支付。支付完成后，务必返回网页支付页面";
    }
});

var clipboard_payurl_qq = new Clipboard('#copy_payurl_qq', {
    text: function() {
        return $("#copy_payurl_qqtxt").text()+"请粘贴到QQ聊天框后进入，长按扫描二维码支付。支付完成后，务必返回网页支付页面";
    }
});

clipboard.on('success', function(e) {
    toastr.success("复制成功,请使用复制金额付款");
});

clipboard_payurl.on('success', function(e) {
    toastr.success("复制成功,请将链接粘贴到微信聊天框后进入");
});

clipboard_payurl_1.on('success', function(e) {
    toastr.success("复制成功,请将链接粘贴到微信聊天框后进入");
});

clipboard_payurl_qq.on('success', function(e) {
    toastr.success("复制成功,请将链接粘贴到QQ聊天框后进入");
});

clipboard.on('error', function(e) {
	document.querySelector('#copy_money');
    toastr.warning("复制失败,请手动复制一下");
});

clipboard_payurl.on('error', function(e) {
	document.querySelector('#copy_payurl');
    toastr.warning("复制失败,请手动复制一下");
});

clipboard_payurl_1.on('error', function(e) {
	document.querySelector('#copy_payurl_1');
    toastr.warning("复制失败,请手动复制一下");
});

clipboard_payurl_qq.on('error', function(e) {
	document.querySelector('#copy_payurl_qq');
    toastr.warning("复制失败,请手动复制一下");
});

var clipboarda = new Clipboard('#copy_trade_no', {
    text: function() {
		return $("#copy_trade_no2").text();
    }
});

clipboarda.on('success', function(e) {
    toastr.success("复制成功,请备注复制的订单号");
});

clipboarda.on('error', function(e) {
	document.querySelector('#copy_trade_no2');
    toastr.warning("复制失败,请手动复制一下");
});

</script>
<script type="text/javascript">    
  	var ischeck = false;   //true 正在检测  false 没有检测
  	var ischeck_wechat = false;//true 正在检测  false 没有检测
    var priceIstype ='2';
    var myTimer;
    var strcode = '';
    updateQrOk=0;
    var pay_type = '<?=$type?>';//支付方式
	var is_money = <?=$srow['money']?>;
	var price = '<?=$srow['price']?>';
	var qrcode = '<?=$qr_url?>';
	if(updateQrOk==0){
	    updateQrOk=1;
	    if(is_money!=price){
	        layer.alert('温馨提示:'+is_money+'金额已被其他用户金额占用,请您务必付款<font color=red>'+price+'</font>元,<font color=red>多付一分或者少付一分都不能到账</font>!', {
                icon: 1,
                skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
            });
        }
        $('#price').html('￥'+price+'<font color=red style="font-size:15px">(不可多付或少付)</font>');//输出真实付款金额
        $("#qrcode").html('<img id="qrcode_load" src="'+ qrcode +'" width="200" height="200">');//输出过期二维码提示图片
    }
	
    function timer(intDiff) {
        myTimer = window.setInterval(function () {
            var day = 0,
                hour = 0,
                minute = 0,
                second = 0;//时间默认值
            if (intDiff > 0) {
                day = Math.floor(intDiff / (60 * 60 * 24));
                hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
            }
            if (minute <= 9) minute = '0' + minute;
            if (second <= 9) second = '0' + second;
            $('#hour_show').html('<s id="h"></s>' + hour + '时');
            $('#minute_show').html('<s></s>' + minute + '分');
            $('#second_show').html('<s></s>' + second + '秒');
            if (hour <= 0 && minute <= 0 && second <= 0) {
                clearInterval(myTimer);
            }
            intDiff--;
        }, 1000);
    }

    //周期监听 
	window.onload = function(){
	    var pay_type = '<?=$type?>';//支付方式
	    var ua = '<?=$device?>';
	    if(pay_type=='alipay' && ua!='pc'){
	        var url_scheme = '<?=$payh5url?>';
	        layer.msg('正在自动唤醒支付宝...', {shade: 0,time: 1000});
	        setTimeout(window.location.href=url_scheme, 3000);
	        //window.location.href = url_scheme;
	    }else if(pay_type=='qqpay'){
	        var url_scheme = 'QQH5.php?trade_no=<?=$trade_no?>';
	        if(judgeClient() =='Android' ){
	            layer.msg('正在自动唤醒QQ...', {shade: 0,time: 1000});
	            setTimeout(window.location.href=url_scheme, 3000);
	        }
	    }
		setTimeout("order()", 2000);
	}
    
    //订单监控  {订单监控}
	function order() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "./Mym_Get.php",
            data: {trade_no: "<?php echo $trade_no?>"},
            success: function (result) {
                if (result.code == 200) {
					$("#divTime").html("<small style='color:red; font-size:22px'>"+ result.msg +"</small>");
					$("#qrcode").html('<img id="qrcode_load" src="./Template/default/assets/img/pay_ok.png">');//输出过期二维码提示图片
					//回调页面
					layer.msg('支付成功，正在跳转中...');
					window.location.href=result.data.backurl;
                }else if(result.code == '-1'){
                    $("#qrcode").html('<img id="qrcode_load" src="./Template/default/assets/img/qrcode_timeout.png">');//输出过期二维码提示图片
                    $("#divTime").html("<small style='color:red; font-size:22px'>"+ result.msg +"</small>");
                    layer.confirm(result.msg, {
                        icon: 2,
                        title: '支付失败',
                        btn: ['确认'] //按钮
                    }, function(){
                        location.href=result.data.backurl
                    });
                }else{
                    if(window.navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)){
                        if(pay_type == 'qqpay'){
                            $("#alipayh5url").html('<a type="button" href="QQH5.php?trade_no=<?=$trade_no?>" class="btn btn-lg btn-block btn-success" style="font-size:13px" target="_blank">唤醒QQ</a>');//H5按钮2
                        }else if(pay_type == 'wxpay'){
                            $("#alipayh5url").html('<a type="button" href="weixin://" class="btn btn-lg btn-block btn-success" style="font-size:13px" target="_blank">唤醒微信</a>');//H5按钮2
                        }else if(pay_type == 'alipay'){
                            $("#alipayh5url").html('<a type="button" href="<?=$alipayh5url?>" class="btn btn-lg btn-block btn-success" style="font-size:13px" target="_blank">唤醒支付宝APP支付</a>');//H5按钮2
                        }
						
					}
					
                    setTimeout("order()", 1000);
                }
            }
        });
    }


    function isWeixin() { 
        var ua = window.navigator.userAgent.toLowerCase(); 
        if (ua.match(/MicroMessenger/i) == 'micromessenger') { 
            return 1;
        } else { 
            return 0;
        } 
    }

    function isMobile() {
        var ua = navigator.userAgent.toLowerCase();
        _long_matches = 'googlebot-mobile|android|avantgo|blackberry|blazer|elaine|hiptop|ip(hone|od)|kindle|midp|mmp|mobile|o2|opera mini|palm( os)?|pda|plucker|pocket|psp|smartphone|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce; (iemobile|ppc)|xiino|maemo|fennec';
        _long_matches = new RegExp(_long_matches);
        _short_matches = '1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-';
        _short_matches = new RegExp(_short_matches);
        if (_long_matches.test(ua)) {
            return 1;
        }
        user_agent = ua.substring(0, 4);
        if (_short_matches.test(user_agent)) {
            return 1;
        }
        return 0;
    }
    function isQQ(){
    	var ua = window.navigator.userAgent.toLowerCase(); 
        if (ua.match(/MQQBrowser/i) == 'MQQBrowser') { 
            return 1;
        } else { 
            return 0;
        } 
    }
    $().ready(function(){
        //默认6分钟过期
        $('#orderDetail .arrow').click(function (event) {
		    if ($('#orderDetail').hasClass('detail-open')) {
		        $('#orderDetail .detail-ct').slideUp(500, function () {
		            $('#orderDetail').removeClass('detail-open');
		        });
		    } else {
		        $('#orderDetail .detail-ct').slideDown(500, function () {
		            $('#orderDetail').addClass('detail-open');
		        });
		    }
		});
        timer("<?=$outtime?>");
        var istype = "<?=$type?>";
        var suremoney = "1";
        var uaa = navigator.userAgent;
        var isiOS = !!uaa.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
        if (isMobile() == 1){
            if (isWeixin() == 1 && istype == 'wxpay'){
                //微信内置浏览器+微信支付
                $("#showtext").html('打开-->长按识别');
            } else if(isQQ() == 1 && istype == 'qqpay'){
            	//QQ内置浏览器+QQ支付
                alert("请长按二维码图片后,点【扫描二维码】");//这样在QQ内置浏览器付款后才能返回到支付页面
            	$("#showtext").text("长按二维码识别");
            }else{
                //其他手机浏览器+支付宝支付
                if (isWeixin() == 0 && istype == 'alipay'){
                    $(".paybtn").show();
                    // if(priceIstype=="2"&&!isiOS){
                    //     $('#alipayform').submit();
                    // }
                    $('#msg').html("<h1>请付款1.00元，勿多付少付</h1>");
                    //$(".qrcode-img-wrapper").remove();
                    $(".tip").remove();
                    $(".foot").remove();                                      

                }else /*{*/
                    if (isWeixin() == 0 && istype == 'wxpay'){
                        //其他手机浏览器+微信支付
                        //IOS的排除掉
                        if (isiOS){
                            $('.iospayweixinbtn').attr('style','padding-top: 15px;');
                        }else{
                            $(".payweixinbtn").attr('style','padding-top: 15px;');
                        }                      
                        $("#showtext").html("打开微信支付[扫一扫]");
                    }
                //}
            }
        }
    });
</script>
</body>
</html>