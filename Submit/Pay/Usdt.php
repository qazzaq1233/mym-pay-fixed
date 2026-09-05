<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta name="bing-analysis-id" content="1d1k2w32381c1c1l">
    <meta charset="utf-8">
    <title>TRC20-USDT - 最好的虚拟交易方式！</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
    <script src="//lib.baomitu.com/clipboard.js/1.7.1/clipboard.min.js"></script>
    <style>
        .hr-top {
            margin-top: 20px;
            border-top: 1px dashed #e5e5e5;
            padding: 10px 0;
            position: relative;
        }

        .mobile {
            display: none;
        }


        .zhuanzhang_box {
            font-size: 16px;
            margin: 20px 20px;
            font-weight: bolder;
            border: 1px dashed gainsboro;
            padding: 20px 0;
            border-radius: 20px;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        }

        .account_box {
            border: 1px dashed gainsboro;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            padding: 20px 0;
            margin: 20px 20px 0 20px;
            box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        }

        .copy {
            font-size: 14px;
            display: inline-block;
            padding: 2px 5px;
            position: relative;
            top: -2px;
            background: linear-gradient(to bottom, #f5fffa, #cefbe361);
            border-radius: 5px;
            border: 1px dashed #8080806e;
            outline: none;
        }

        .copy:hover {
            color: #14d267;
        }
    </style>
    <link href="Usdt/css/pay.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class=" body">
    <h1 class="mod-title">
        <span class="ico_log ico-usdt" style="width: 32px;height: 38px;"></span><b style="font-size: 20px;color: #0ba798;">TRC20-USDT</b>
    </h1>


    <div class="mod-ct" style="margin-top: 2px;">

        <div class="pc">
            <div style="font-size: 25px;padding-top: 15px;">请支付 <b style="color: red;">
                    <?=$price?></b> <b style="color: #28dd81;">USDT</b>
            </div>
            <div class="qrcode-img-wrapper" data-role="qrPayImgWrapper">
                
                    <div style="margin: 5px;display: block;" id="qrcode"></div>
                
            </div>

            <div class="account_box">
                <table>
                    <tbody>
                    <?php if($receiver_surname!==''){?>
                    <tr>
                        <td>姓:</td>
                        <td style="color: #e60012;font-weight:bold;"><?=$receiver_surname_html?></td>
                    </tr>
                    <?php }?>
                    <tr>
                        <td>地址:</td>
                        <td style="color: #26bc0d;">
                            <?=$QR_row['cookie']?>
                            <button class="copy copyAccount" data-clipboard-text="<?=$QR_row['cookie']?>">复制</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="zhuanzhang_box">
                <div style="color: #0a53be;">先复制钱包地址，扫码后也可以复制</div>
                <div style="color: red;">数额填写：<b style="color: #0eae23;font-size: 20px;"><?=$price?></b> <b style="color: #0ba798;">USDT</b>，否则不到账
                </div>
            </div>
        </div>

        <div class="footer">
            <div>
                <div class="tip ">
                    <div style="margin-bottom: 10px;"><b style="font-size: 18px;">请在</b>
                        <strong id="minute_show"><s id="min">0分</s></strong>
                        <strong id="second_show"><s id="sec">0秒</s></strong>
                        <b style="font-size: 18px;">完成支付!</b>
                    </div>
                </div>
            </div>
            <div class="detail" id="orderDetail">
                <dl class="detail-ct" id="desc" style="display: none;">
                    <dt>金额</dt>
                    <dd><?=$price?> USDT</dd>
                    <dt>商户订单：</dt>
                    <dd id="ordernum"><?=$trade_no?></dd>
                    <dt>创建时间：</dt>
                    <dd><?=$srow['addtime']?></dd>
                    <dt>状态</dt>
                    <dd>等待支付</dd>
                </dl>

                <a href="javascript:void(0)" class="arrow" style="z-index: 999999"><i class="ico-arrow"></i></a>
            </div>
        </div>
    </div>

<script type="text/javascript">
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
			$("#min").html(minute +"分");
			$("#sec").html(second +"秒");
		}
		intDiff--
		}, 1000);
    }
	order();
	updateQrOk = 0;
	updateQrImg= 0;
	updateQrNo = 0;
	function order(){
        $.get("Mym_Get.php",{trade_no: "<?php echo $trade_no?>"},function(result){
			//成功
     		if(result.code == '200' && updateQrOk==0){
				updateQrOk==1;
         		window.clearInterval(orderlst);
				layer.msg('支付成功，正在跳转中...');
				window.location.href=result.data.backurl;
     		}
     		//支付二维码
     		if(result.code == '100' && updateQrImg==0){
 				updateQrImg = 1;
				var pay_type = '<?=$type?>';//支付方式
				$("#qrcode").html('<img id="qrcode_load" src="<?=$qr_url?>">');//输出过期二维码提示图片
				$('#amount').text(<?=$price?>)
				loading = false;
				$(".loading-loader").hide();
				if(pay_type == "alipay"){
					
				}
				if(pay_type == "wxpay"){
					
				}
				if(pay_type == "qqpay"){
					
				}
			}
         	//订单已经超时
     		if(result.code == '-1' && updateQrNo==0){
				updateQrNo==1;
				$("#divTime").html("<small style='color:red; font-size:22px'>"+ result.msg +"</small>");
     			window.clearInterval(orderlst);
     			layer.confirm(result.msg, {
     			  icon: 2,
     			  title: '支付失败',
   				  btn: ['确认'] //按钮
   				}, function(){
					location.href=result.data.backurl
   				});
         	}
			
     	},"JSON");
	}
	//周期监听 
	orderlst = window.setInterval(function () {
		order();
	}, 2000); 
    $(function(){
        timer(intDiff);
    });
    //点击小箭头事件
    $('#orderDetail a').click(function () {
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
    $('.copyAccount').click(function () {
        let clipboard = new Clipboard(".copyAccount");
        clipboard.on('success', function (e) {
            layer.msg("钱包地址复制成功");
        });
    });
    </script>


</div></body></html>