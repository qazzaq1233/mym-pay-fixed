<?php
//if (!defined('IN_CRONLITE')) exit();
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
if (strpos($useragent, 'iphone') != false || strpos($useragent, 'ipod') != false) {
    $alert = '<img src="//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rLrNvRzmibibqrjTFj5g2kzGyoQj3ViartAEQ/0" class="icon-safari" /> <span id="openm">Safari打开</span>';
} else if (strpos($useragent, 'micromessenger') != false) {
    $alert = '<img src="//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rLbNVmztN9ia6GPRJ0IFicucFTr4Pp8xzibsw/0" class="icon-safari" /> <span id="openm">浏览器打开</span>';
} else {
    $alert = '<img src="//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rOCTm6gtUeQKX7m84xg47iaVosibGckrP0JQ/0" class="icon-safari" /> <span id="openm">浏览器打开</span>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>请使用浏览器打开进行付款</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta name="format-detection" content="telephone=no"/>
    <meta content="false" name="twcClient" id="twcClient"/>
    <meta name="aplus-touch" content="1"/>
    <style>
body,html{width:100%;height:100%}
*{margin:0;padding:0}
body{background-color:#fff}
.top-bar-guidance{font-size:15px;color:#fff;height:70%;line-height:1.8;padding-left:20px;padding-top:20px;background:url(//gw.alicdn.com/tfs/TB1eSZaNFXXXXb.XXXXXXXXXXXX-750-234.png) center top/contain no-repeat}
.top-bar-guidance .icon-safari{width:25px;height:25px;vertical-align:middle;margin:0 .2em}
.app-download-tip{margin:0 auto;width:290px;text-align:center;font-size:15px;color:#2466f4;background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAcAQMAAACak0ePAAAABlBMVEUAAAAdYfh+GakkAAAAAXRSTlMAQObYZgAAAA5JREFUCNdjwA8acEkAAAy4AIE4hQq/AAAAAElFTkSuQmCC) left center/auto 15px repeat-x}
.app-download-tip .guidance-desc{background-color:#fff;padding:0 5px}
.app-download-btn{display:block;width:214px;height:40px;line-height:40px;margin:18px auto 0 auto;text-align:center;font-size:18px;color:#2466f4;border-radius:20px;border:.5px #2466f4 solid;text-decoration:none}
    </style>
</head>
<?php echo $gg;?>
<body>
<div class="top-bar-guidance">
    <p>点击右上角<?php echo $alert?></p>
    <p>可以继续进行付款哦~</p>
    <?php if($receiver_surname!==''){?><p>收款人姓：<?php echo $receiver_surname_html;?></p><?php }?>
</div>
<div class="app-download-tip">
    <span class="guidance-desc">您也可以复制本站网址，到其它浏览器打开</span>
</div>
<a class="app-download-btn" id="J_BtnDowanloadApp">点此继续访问</a>
<a style="display: none;" href="" id="vurl" rel="noreferrer"></a>

<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script>
function openu(u){
document.getElementById("vurl").href= u;
document.getElementById("vurl").click();
}
var url = window.location.href;
	document.querySelector('body').addEventListener('touchmove', function (event) {
		event.preventDefault();
	});
	if(navigator.userAgent.indexOf("QQ/") > -1){
		openu("ucbrowser://"+url);
		openu("mttbrowser://url="+url);
		openu("baiduboxapp://browse?url="+url);
		openu("googlechrome://browse?url="+url);
		$("html").on("click",function(){
			openu("ucbrowser://"+url);
			openu("mttbrowser://url="+url);
			openu("baiduboxapp://browse?url="+url);
			openu("googlechrome://browse?url="+url);
		});
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
</script>
</body>
</html>