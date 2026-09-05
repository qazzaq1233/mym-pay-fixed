<?php
include("../Mym/Common.php");
if($islogin_user==1)exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>忘记密码 - <?php echo $conf['sitename'] ?></title>

	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/material-design-iconic-font.min.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/util.css">
	<link rel="stylesheet" type="text/css" href="../Mym/Assets/Login/static/css/main.css">
</head>

<body>

	<div class="limiter">
		<div class="container-login100" style="background-image: url('../Mym/Assets/Login/static/image/bg-01.jpg');">
			<div class="auth-page">
				<div class="auth-hero">
					<div class="auth-brand">ID</div>
					<p class="auth-kicker">ACCOUNT RECOVERY</p>
					<h1>找回访问权限</h1>
					<p class="auth-desc">通过绑定邮箱完成身份校验，安全恢复商户中心访问能力。</p>
					<div class="auth-metrics">
						<span>邮箱验证</span>
						<span>安全恢复</span>
						<span>风控保护</span>
					</div>
				</div>
				<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
					<form class="login100-form validate-form">
						<span class="login100-form-title p-b-49">找回密码</span>
						<p class="auth-subtitle">输入绑定邮箱，按提示完成验证</p>

					<div class="wrap-input100 validate-input m-b-23" data-validate="请输入邮箱">
						<span class="label-input100">邮箱</span>
						<input id="email" class="input100" type="text" name="email" placeholder="请输入邮箱" autocomplete="off">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>

						<div class="container-login100-form-btn">
							<div class="wrap-login100-form-btn">
								<div class="login100-form-bgbtn"></div>
								<div id="submit" class="login100-form-btn">发送找回邮件</div>
							</div>
						</div><br/>
						
						<div class="container-login100-form-btn">
							<div class="wrap-login100-form-btn auth-secondary-btn">
								<div class="login100-form-bgbtn"></div>
								<a href="Login.php" class="login100-form-btn">返回登录</a>
							</div>
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
    var handlerEmbed = function (captchaObj) {
    	captchaObj.onReady(function () {
    		$("#wait").hide();
    	}).onSuccess(function () {
    		var result = captchaObj.getValidate();
    		if (!result) {
    			return alert('请完成验证');
    		}
    		var user=$("#user").val();
		    var pass=$("#pass").val();
		    var qq=$("#qq").val();
		    var email=$("#email").val();
		    var code=$("#code").val();
    		var ii = layer.load(2, {shade:[0.1,'#fff']});
    		$.ajax({
    			type : "POST",
    			url : "Ajax2.php?act=findpwd",
    			data : {email:email,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
    			dataType : 'json',
    			success : function(data) {
    				layer.close(ii);
    				if(data.code == 1){
    				    layer.alert(data.msg);
    					setTimeout(function () {
					    location.href="./";
					    }, 3000); //延时1秒跳转
    				}else{
    					layer.alert(data.msg);
    					captchaObj.reset();
    				}
    			},
    			error:function(data){
				layer.close(ii);
				layer.msg('服务器错误');
			    }
    		});
    	});
    	$('#submit').click(function () {
    	    var reg=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    		if ($(this).attr("data-lock") === "true") return;
    		if($("input[name='email']").val()==''){
    		    layer.alert('邮箱不能为空！');
    		}else if(!reg.test($("input[name='email']").val())){
    		    layer.alert('邮箱格式不正确！');
    		}else{
    		    captchaObj.verify();
    		}
    	});
    };
    $(document).ready(function(){
    	$.ajax({
    		// 获取id，challenge，success（是否启用failback）
    		url: "Ajax.php?act=Captcha&t=" + (new Date()).getTime(), // 加随机数防止缓存
    		type: "get",
    		dataType: "json",
    		success: function (data) {
    			console.log(data);
    			// 使用initGeetest接口
    			// 参数1：配置参数
    			// 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
    			initGeetest({
    				width: '100%',
    				gt: data.gt,
    				challenge: data.challenge,
    				new_captcha: data.new_captcha,
    				product: "bind", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
    				offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
    				// 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
    			}, handlerEmbed);
    		}
    	});
    });
</script>
</body>

</html>