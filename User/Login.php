<?php
include("../Mym/Common.php");
if(isset($_GET['logout'])){
	setcookie("user_token", "", time() - 604800);
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已成功注销本次登陆！');window.location.href='./Login.php';</script>");
}elseif($islogin_user==1){
	exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>登入 - <?php echo $conf['sitename'] ?></title>

	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/util.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/main.css">
</head>

<body>
    <style type="text/css">video{position:  fixed;right: 0px; bottom: 0px; min-width: 100%;  min-height: 100%; height: auto; width: auto; z-index: -11; } </style>
	<div class="limiter">
		<div class="container-login100" style="background-image: url('../Mym/Assets/Login/static/image/bg-01.jpg');">
			<div class="auth-page">
				<div class="auth-hero">
					<div class="auth-brand">ID</div>
					<p class="auth-kicker">MERCHANT ID CENTER</p>
					<h1><?php echo $conf['sitename'] ?></h1>
					<p class="auth-desc">统一商户身份入口，安全连接支付、订单、结算与数据资产。</p>
					<div class="auth-metrics">
						<span>风控校验</span>
						<span>实时入驻</span>
						<span>快捷登录</span>
					</div>
				</div>
				<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
					<form class="login100-form validate-form">
						<span class="login100-form-title p-b-49">欢迎回来</span>
						<p class="auth-subtitle">登录商户中心，继续管理你的业务</p>

					<div class="wrap-input100 validate-input m-b-23" data-validate="请输入用户名">
						<span class="label-input100">用户名</span>
						<input id="pid" class="input100" type="text" name="username" placeholder="请输入用户名" autocomplete="off">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="请输入密码">
						<span class="label-input100">密码</span>
						<input id="key" class="input100" type="password" name="pass" placeholder="请输入密码">
						<span class="focus-input100" data-symbol="&#xf190;"></span>
					</div>

					<div class="text-right p-t-8 p-b-31">
						<a href="findpwd.php">忘记密码？</a>
					</div>

						<div class="container-login100-form-btn">
							<div class="wrap-login100-form-btn">
								<div class="login100-form-bgbtn"></div>
								<div id="login" class="login100-form-btn">登录商户中心</div>
							</div>
						</div><br/>
						
						<div class="container-login100-form-btn">
							<div class="wrap-login100-form-btn auth-secondary-btn">
								<div class="login100-form-bgbtn"></div>
								<a href="Reg.php" class="login100-form-btn">创建商户账号</a>
							</div>
						</div>

					<div class="txt1 text-center p-t-54 p-b-20">
						<span>第三方登录</span>
					</div>

					<div class="flex-c-m">
						<!--<a href="#" class="login100-social-item bg1">
							<i class="fa fa-wechat"></i>
						</a>-->

						<a href="Connect.php" class="login100-social-item bg2">
							<i class="fa fa-qq"></i>
						</a>
						<!--

						<a href="#" class="login100-social-item bg3">
							<i class="fa fa-weibo"></i>
						</a>-->
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>

<script src="../Mym/Assets/Login/static/js/jquery-3.2.1.min.js"></script>
<script src="../Mym/Assets/Login/static/js/main.js"></script>
<script src="../Mym/Assets/Layer/layer.js"></script>
<script src="../Mym/Assets/Js/gt.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var captchaReady = false;
        var captchaObj = null;

        function submitLogin(geetestData) {
            var pid = $.trim($("#pid").val());
            var key = $.trim($("#key").val());

            if (pid === '') {
                layer.alert('请输入用户名');
                return false;
            }
            if (key === '') {
                layer.alert('请输入密码');
                return false;
            }

            var postData = $.extend({pid: pid, key: key}, geetestData || {});
            var ii = layer.load(2, {shade:[0.1,'#fff']});

            $.ajax({
                type: "POST",
                url: "Ajax.php?act=Login",
                data: postData,
                dataType: 'json',
                success: function(data) {
                    layer.close(ii);
                    if (data.code == 1) {
                        layer.alert(data.msg);
                        setTimeout(function () {
                            location.href = "./";
                        }, 1000);
                    } else if (data.code == 1001) {
                        $("#frame_ali").show();
                    } else {
                        layer.alert(data.msg || '登录失败，请检查账号密码');
                        if (captchaObj) captchaObj.reset();
                    }
                },
                error: function() {
                    layer.close(ii);
                    layer.msg('服务器错误');
                    if (captchaObj) captchaObj.reset();
                }
            });
        }

        $('#login').off('click.authLogin').on('click.authLogin', function () {
            if ($(this).attr("data-lock") === "true") return false;
            if (captchaReady && captchaObj) {
                captchaObj.verify();
            } else {
                submitLogin();
            }
            return false;
        });

        $.ajax({
            url: "Ajax.php?act=Captcha&t=" + (new Date()).getTime(),
            type: "get",
            dataType: "json",
            success: function (data) {
                if (!data || !data.gt || !data.challenge || typeof initGeetest !== 'function') {
                    captchaReady = false;
                    return;
                }
                initGeetest({
                    width: '100%',
                    gt: data.gt,
                    challenge: data.challenge,
                    new_captcha: data.new_captcha,
                    product: "bind",
                    offline: !data.success
                }, function(obj) {
                    captchaObj = obj;
                    captchaReady = true;
                    captchaObj.onReady(function () {
                        $("#wait").hide();
                    }).onSuccess(function () {
                        var result = captchaObj.getValidate();
                        if (!result) {
                            layer.alert('请完成验证');
                            return;
                        }
                        submitLogin({
                            geetest_challenge: result.geetest_challenge,
                            geetest_validate: result.geetest_validate,
                            geetest_seccode: result.geetest_seccode
                        });
                    });
                });
            },
            error: function () {
                captchaReady = false;
            }
        });
    });
</script>
</body>

</html>