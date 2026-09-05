<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>我要付款</title>
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, viewport-fit=cover"
    />
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/timer.jquery/0.9.0/timer.jquery.js"></script>
    <script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
    <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
    <link
      rel="stylesheet"
      type="text/css"
      href="https://res.wx.qq.com/t/wx_fed/weui-source/res/2.5.16/weui.min.css"
    />
    <style>
      body {
        /*background-color: var(--weui-BG-0);*/
        padding: 0 16px;
        font-size: 14px;
        font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto,
          Helvetica Neue, Arial, Noto Sans, sans-serif, apple color emoji,
          segoe ui emoji, Segoe UI Symbol, noto color emoji;
        font-family: PingFangSC-Regular;
        background: #ffffff;
        overflow: hidden;
      }

      a {
        text-decoration: none;
      }

      .content_body {
        min-height: 100%;
        margin: 0 auto;
        box-sizing: border-box;
        text-align: center;
      }

      .content {
        margin-top: 0;
      }

      .foot {
        position: fixed;
        width: 100%;
        bottom: 0;
        left: 0;
        padding-left: 16px;
        padding-right: 16px;
        padding-bottom: constant(safe-area-inset-bottom);
        padding-bottom: env(safe-area-inset-bottom);
        box-sizing: border-box;
      }

      .check_button {
        flex-grow: 1;
        padding: 9px 0;
        outline: none;
        border-radius: 8px;
        border: none;
        font-size: 18px;
        color: #000;
      }

      .copy_btn {
        display: inline-block;
        border-radius: 8px;
        color: #fff;
        background-color: #07c160;
        padding: 0.3rem;
      }

      .container {
        display: flex;
        justify-content: space-between;
      }

      .check_button.highlight {
        color: #fff;
        background-color: #07c160;
      }

      .check_button + .check_button {
        margin-left: 8px;
      }

      .img {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 10rem;
        padding: 0 0.24rem;
        background: url(//cdn.100b.cn/kspay/img/wyfks.png) no-repeat;
        background-size: 100%;
        background-position-y: -0.25rem;
        box-sizing: border-box;
        color: #eeeeee;
        text-align: left;
      }

      .img_content {
        margin: 0;
        margin-left: 26px;
      }

      .vendor_name {
        margin-top: 12px;
        font-size: 18px;
      }

      .tips {
        color: #fff;
        background-color: #01aa90;
        border-radius: 50px;
        box-sizing: border-box;
        padding-left: 12px;
        max-height: none;
        min-height: 30px;
        display: flex;
        align-items: center;
      }

      .content {
        font-size: 20px;
      }

      .fee {
        color: firebrick;
        font-size: 32px;
        font-weight: bold;
      }

      .head {
        text-align: center;
        margin: 0;
        font-size: 14px;
        color: #666666;
      }

      .click_jump {
        display: block;
      }

      .fat,
      .fat2 {
        color: #fdd99c;
        font-weight: 800;
        font-size: 20px;
      }

      .custom-block.tip {
        padding: 8px 16px;
        background-color: rgba(64, 158, 255, 0.1);
        border-radius: 4px;
        border-left: 5px solid #409eff;
        margin: 20px 0;
      }

      .custom-block .custom-block-title {
        font-size: 20px;
        font-weight: 700;
      }

      .tip_words {
        font-size: 0.9rem;
        color: #303133;
        font-weight: 700;
      }

      .timespan {
        font-size: 1.2rem;
        color: firebrick;
      }

      .danger {
        color: #f56c6c;
      }

      #copied {
        display: none;
      }

      .weui-icon_msg {
        width: 1rem !important;
      }
      .icon-box {
        margin: 0 !important;
      }
      .icon-box__desc {
        margin: 0 !important;
        font-size: 12px;
        color: #888;
      }
    </style>
  </head>

  <body>
    <div class="content_body">
      <p class="head">付款前请确认商户信息</p>
      <div class="img">
        <div class="img_content">商户：<span class="vendor_name"></span></div>
        <div class="img_content fat">请截图二维码      使用微信扫一扫完成付款</div>
        <div class="img_content fat2"></div>
      </div>
      <div class="qrcode" onclick="imgClick()"><img src="../Mym/cache/qrcode/<?=$trade_no?>.png" height="200" width="200" /></div>
      
      <div
        class="content"
        id="copy_btn"
        data-clipboard-action="copy"
        data-clipboard-target="#fee"
      >
        ￥<span class="fee" id="fee"></span>元
        <span class="icon-box__desc" id="copied">
          <i
            role="img"
            title="成功"
            aria-describedby="tip_1"
            class="weui-icon-success weui-icon_msg"
          ></i
          >已复制
        </span>
        <span
          role="button"
          class="weui-btn weui-btn_mini weui-btn_primary weui-wa-hotarea"
          >复制</span
        >
      </div>
      <?php if($receiver_surname!==''){?><div class="content" style="color:#e60012;font-weight:bold;">姓：<?=$receiver_surname_html?></div><?php }?>
      <div class="content">有效期：<span class="timespan"></span></div>
    </div>
    <div class="tip custom-block">
      <p class="tip_words danger">金额不要出错，否则无法保证订单正确完成！</p>
      <p class="tip_words danger">点击金额可以复制应付金额，防止出错</p>
      <p class="tip_words">订单有效时间三分钟，请注意过期时间</p>
    </div>
    <div class="foot">
      <div class="container">
        <button class="check_button" onclick="closeWindow()">取消</button>
        <button class="check_button highlight" onclick="jump()">完成</button>
      </div>
    </div>

  </body>
  <script>
    $(".vendor_name").html(" <?=$shopname?>");
    window.trade_type = "";
    window.query_times = 0;
    window.scends = 3 * 60;
    $(".fee").html(<?=$price?>);
    window.onload = function () {
      if (!isWeiXin()) {
        $(".fat").html("请长按二维码保存");
        $(".fat2").html("在微信扫一扫识别");
      }
      // 第一个复制按钮
      var clipboard = new Clipboard("#copy_btn");
      clipboard.on("success", function (e) {
        $("#copied").css("display", "inline-block");
        e.clearSelection();
      });
      // 弹框的复制按钮
      var copy_dialog = new Clipboard("#copy_dialog");
      copy_dialog.on("success", function (e) {
        $("#copied").css("display", "inline-block");
        e.clearSelection();
      });
      // 弹框
      tip();
    };


    // 判断是否微信浏览器
    function isWeiXin() {
      var ua = window.navigator.userAgent.toLowerCase();
      if (ua.match(/MicroMessenger/i) == "micromessenger") {
        return true;
        return false;
      } else {
      }
    }

    // dialog 对话框
    function tip() {
      var $iosDialog1 = $("#iosDialog1");
      $iosDialog1.fadeIn(200);
      $iosDialog1.attr("aria-hidden", "false");
      $iosDialog1.attr("tabindex", "0");
      $iosDialog1.trigger("focus");
    }

    // 隐藏对话框
    function hiddenDialog() {
      var $iosDialog1 = $("#iosDialog1");
      $iosDialog1.fadeOut(200);
      $iosDialog1.attr("aria-hidden", "true");
      $iosDialog1.removeAttr("tabindex");
    }
        window.scends = 3 * 60;
 
    // 倒计时 
    function counClock() { 
      let min = "0"; 
      let sec = "0"; 
      if (window.scends > 0) { 
        min = Math.floor(window.scends / 60); 
        sec = Math.floor(window.scends % 60); 
        if (sec < 10) { 
          sec = "0" + sec; 
        } 
        window.scends--; 
        $(".timespan").text(min + "分" + sec + "秒"); 
      } else { 
        $(".timespan").text("订单已失效，请回来源地重新下单"); 
      } 
    }
    //counClock();
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
			$(".timespan").html("<small style='color:red; font-size:26px'>订单二维码已过期</small>");
		}else{
			$(".timespan").text(minute + "分" + second + "秒"); 
		}
		intDiff--
		}, 1000);
     } 
	 
     $(function(){
         timer(intDiff);
     });
    // 检查是否支付完成
    function loadmsg() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "Mym_Get.php",
            timeout: 10000, //ajax请求超时时间10s
            data: {trade_no: "<?php echo $trade_no?>"}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 200) {
					alert('支付成功，正在跳转中...', {icon: 16,shade: 0.1,time: 15000});
                    window.location.href=data.data.backurl;
                }else if(data.code == '-1'){
                    layer.confirm(data.msg, {
                        icon: 2,
                        title: '支付失败',
                        btn: ['确认'] //按钮
                    }, function(){
                        window.location.href=data.data.backurl
                    });
                }else{
                    setTimeout("loadmsg()", 2000);
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
</html>