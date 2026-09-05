<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
 <title>在线支付 - <?php echo $typeName ?> - 网上支付 安全快速！</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<style>
body{background:#f2f2f4;}
body,html{width:100%;height:100%;}
*,:after,:before{box-sizing:border-box;}
*{margin:0;padding:0;}
img{max-width:100%;}
#header{height:60px;border-bottom:2px solid #eee;background-color:#fff;text-align:center;line-height:60px;}
#header h1{font-size:20px;}
#main{overflow:hidden;margin:0 auto;padding:20px;padding-top:80px;width:992px;max-width:100%;}
#main .left{float:left;width:40%;box-shadow:0 0 60px #b5f1ff;}
.left p{margin:10px auto;}
.make{padding-top:15px;border-radius:10px;background-color:#fff;box-shadow:0 3px 3px 0 rgba(0,0,0,.05);color:#666;text-align:center;transition:all .2s linear;}
.make .qrcode{margin:auto;}
.make .money{margin-bottom:0;color:#f44336;font-weight:600;font-size:18px;}
.info{padding:15px;width:100%;border-radius:0 0 10px 10px;background:#32343d;color:#f2f2f2;text-align:center;font-size:14px;}
#main .right{float:right;padding-top:25px;width:60%;color:#ccc;text-align:center;}
@media (max-width:768px){
#main{padding-top:30px;}
#main .left{width:100%;}
#main .right{display:none;}
}
</style>
        <link rel="stylesheet" type="text/css" href="<?php echo PAYSTATIC_ROOT?>css/qrcode.css">
</head>
<body>
<div id="main">
	<div class="left">
		<div class="make">
		    <p><img src="<?php echo PAYSTATIC_ROOT?>img/<?=$type?>-logo.png" alt="" style="height:30px;"></p>
			<p>商品名称：<?=$srow['name']?></p>
			<p class="money" id="price" style="font-weight:bold; color:green"><?=$price?></p>
            <center>
                <p class="qrcode" id="qrcode"><img id="qrcode_load" src="<?php echo PAYSTATIC_ROOT?>img/loading.gif" style="display: block;"></img></p>
            </center>
            <center>
				<a id="payh5url"></a>
			</center>
			<div class="info">
				<!--<a id="copy_p" style="color: red;font-size: 15px;word-break: keep-all;">【复制金额】</a>-->
				<?php if($type=='alipay'){
				    echo '<div onclick="open();" style="color: red;font-size: 15px;word-break: keep-all;">付不了款点我</div>';
				}?>
				<?php if($QR_row['crontime']>time() and $QR_row['status']!=1)echo '<p style="color: red;font-size: 17px;word-break: keep-all;">商家账号状态未在线</p>';?>
				<p id="divTime">正在获取二维码,请稍等...</p>
				<?php if($receiver_surname!==''){?><p style="color:#ffeb3b;font-weight:bold;">姓：<?=$receiver_surname_html?></p><?php }?>
				<p>商户订单号：<?=$trade_no?></p>
				<?php if($userrow['mali']==1){?>
				<p>联系QQ：<?=$userrow['qq']?></p>
				<?php }if($userrow['mali']==2){?>
				<p>联系：<?=$userrow['set_mali']?></p>
				<?php }?>
				<p>请使用<?=$typeName?>扫一扫</p>
			</div>
		</div>
	</div>
	<div class="right">
		<img src="<?php echo PAYSTATIC_ROOT?>img/<?=$type?>-sys.png" alt="">
	</div>
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
    if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) { //判断iPhone|iPad|iPod|iOS
        client = 'iOS';
    } else if (/(Android)/i.test(navigator.userAgent)) { //判断Android
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
/*
    layer.open({
        type: 1 //Page层类型
        ,area: ['500px', '300px']
        ,title: 'Hello layer'
        ,shade: 0.6 //遮罩透明度
        ,maxmin: true //允许全屏最小化
        ,anim: 1 //0-6的动画形式，-1不开启
        ,content: '<div style="padding:50px;">这是一个非常普通的页面层，传入了自定义的 html</div>'
    });    
*/
    
        //自定义标题风格 
        var tips_pop = layer.open({
            time:3,
            shadeClose:1,
            anim:'up',
            shade:'background-color: rgba(0,0,0,.5)',
            className: 'popuo-pay-tips', 
            title: [ '温馨提示', 'background-color: #FF4351; color:#fff;' ],
            content: '本次请按金额元支付。请勿修改金额，否则无法自动到账哦',
            btn: '我知道了', 
            yes: function(index){ 
                $('.layui-m-layer').hide(); 
            }
        });
        setTimeout(function () {
            $('.layui-m-layer').hide(); 
        },10000);
    
    var intDiff = parseInt('<?=$outtime?>');//倒计时总秒数量
    function timer(intDiff){
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
		if (minute <= 9) minute = '0' + minute;
		if (second <= 9) second = '0' + second;
		if (hour <= 0 && minute <= 0 && second <= 0) {
			
		}else{
			$("#divTime").html("二维码有效时间:<small style='color:red; font-size:26px'>" + minute + "</small>分<small style='color:red; font-size:26px'>" + second + "</small>秒,失效勿付");
		}
		intDiff--
		}, 1000);
     } 
	 
     $(function(){
         timer(intDiff);
     });

    
	updateQrOk = 0;
	order(0);
     //订单监控  {订单监控}
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
					$("#divTime").html("<small style='color:red; font-size:22px'>"+ result.msg +"</small>");
					$("#qrcode").html('<img id="qrcode_load" src="<?php echo PAYSTATIC_ROOT?>img/pay_ok.png">');//输出过期二维码提示图片
					//回调页面
					layer.msg('支付成功，正在跳转中...');
					window.location.href=result.data.backurl;
                }else if(result.code == '-1'){
                    $("#qrcode").html('<img id="qrcode_load" src="<?php echo PAYSTATIC_ROOT?>img/qrcode_timeout.png">');//输出过期二维码提示图片
                    $("#divTime").html("<small style='color:red; font-size:22px'>"+ result.msg +"</small>");
                    layer.confirm(result.msg, {
                        icon: 2,
                        title: '支付失败',
                        btn: ['确认'] //按钮
                    }, function(){
                        location.href=result.data.backurl
                    });
                }else{
                    var pay_type = '<?=$type?>';//支付方式
                    var is_money = <?=$srow['money']?>;
                    var price = '<?=$srow['price']?>';
                    var qrcode = '<?=$qr_url?>';
                    
                    if(window.navigator.userAgent.match(/(phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|BlackBerry|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Symbian|Windows Phone)/i)){
                        $("#payh5url").html('<a type="button" href="<?=$payh5url?>" class="btn btn-lg btn-block btn-success" style="font-size:13px" target="_blank">唤醒<?=$typeName?>APP支付</a>');//H5按钮2
					}
					if(updateQrOk==0){
                        updateQrOk=1;
                        if(is_money!=price){
                            layer.alert('温馨提示:'+is_money+'金额已被其他用户金额占用,请您务必付款<font color=red>'+price+'</font>元,<font color=red>多付一分或者少付一分都不能到账</font>!', {
                                icon: 1,
                                skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                            });
                        }
                        $('#price').html('￥'+price+'<font color=red style="font-size:15px">(不可多付或少付)</font>');//输出真实付款金额
                        $("#qrcode").html('<img id="qrcode_load" src="'+ qrcode +'">');//输出过期二维码提示图片
                    }
                    setTimeout(function(){ order(0); }, 700);
                }
            },
            error: function(){
                setTimeout(function(){ order(retry + 1); }, retry < 3 ? 1000 : 2000);
            }
        });
    }
	//周期监听 
	window.onload = function(){
	    var pay_type = '<?=$type?>';//支付方式
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