<?php
/* *
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
?>
<?php
include("../Mym/Common.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<!-- This web page is copied by the "https://bazhan.wang" -->
<head>
    <meta charset="utf-8">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
    <title>支付测试-<?php echo $conf['sitename'] ?></title>
    <meta name="description" content="支付测试">
    <meta name="keywords" content="支付测试">

    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta http-equiv="Expires" content="0">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/common.css">
     <link href="/User/Assets/assets/css/icons.css" rel="stylesheet" type="text/css">
     <link rel="stylesheet" href="https://css.letvcdn.com/lc04_yinyue/201612/19/20/00/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <div class="content">
            <div class="box" id="detail-box">
                <div class="top-logo">
                    <img src="/Mym/Assets/Img/logo.png" alt="支付测试">
                </div>
              
                <p class="p2">支付测试-<?php echo $conf['sitename'] ?></p>
                <div class="input-group">  
                <form name=alipayment action=epayapi.php method=post target="_self">
            <div class="input-group">            
              <span class="input-group-addon"><span class="fas fa-life-ring"></span></span>
               <input size="30" name="WIDout_trade_no" value="<?php echo date("YmdHis").mt_rand(100,999); ?>"  class="form-control" placeholder="商户订单号" />
               </div>
            <br/>
            <div class="input-group">
              <span class="input-group-addon"><span class="dripicons-to-do"></span></span>
              <input size="30" name="WIDsubject" value="测试商品" class="form-control" placeholder="商品名称" required="required" />               
            </div>
            <br/>
            <div class="input-group">
              <span class="input-group-addon"><span class="glyphicon glyphicon-yen"></span></span>
              <input size="30" name="WIDtotal_fee" value="1" class="form-control" placeholder="付款金额" required="required"/>                   
            </div>                  
<br/> 
<center>
<div class="btn-group btn-group-justified" role="group" aria-label="...">
  <div class="btn-group" role="group">
    <button type="radio" name="type" value="alipay" ><img src="/Mym/Assets/Icon/alipay.ico" width="18px" title="-1" />支付宝</button>
  </div>
  <div class="btn-group" role="group">
    <button type="radio" name="type" value="qqpay"><img src="/Mym/Assets/Icon/qqpay.ico" width="18px" title="-1" />QQ钱包</button>
  </div>
  <div class="btn-group" role="group">
    <button type="radio" name="type" value="wxpay" ><img src="/Mym/Assets/Icon/wxpay.ico" width="18px" title="-1" />微信</button>
  </div>
  <div class="btn-group" role="group">
    <button type="radio" name="type" value="usdt" ><img src="/Mym/Assets/Icon/usdt.ico" width="18px" title="-1" />USDT</button>
  </div>
</div>
              
            </div>
        </div>
    </div>
  <script type="text/javascript">
        function setScale() {
            let designWidth = 500; //设计稿的宽度，根据实际项目调整
            let designHeight = 1000; //设计稿的高度，根据实际项目调整
            let scale =
                document.documentElement.clientWidth /
                document.documentElement.clientHeight <
                designWidth / designHeight ?
                document.documentElement.clientWidth / designWidth :
                document.documentElement.clientHeight / designHeight;
            document.querySelector(
                '.container'
            ).style.transform = `scale(${scale}) translate(-50%)`;
            document.querySelector('.container').style.width = designWidth;
            document.querySelector('.container').style.height = designHeight;
        }
        setScale();
        window.onresize = function() {
            setScale();
        };
    </script>
   
   

</body>

</html>