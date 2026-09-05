<?php
include("../Mym/Common.php");
if($islogin_user==1)exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>注册 - <?php echo $conf['sitename'] ?></title>

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
					<p class="auth-kicker">MERCHANT ID CENTER</p>
					<h1>创建商户身份</h1>
					<p class="auth-desc">完成基础资料后即可开通商户后台，接入支付能力与订单管理。</p>
					<div class="auth-metrics">
						<span>资料加密</span>
						<span>快速开通</span>
						<span>统一管理</span>
					</div>
				</div>
				<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
					<form class="login100-form validate-form">
						<span class="login100-form-title p-b-49">立即注册</span>
						<p class="auth-subtitle">填写商户资料，开启你的支付业务</p>

					<div class="wrap-input100 validate-input m-b-23" data-validate="请输入用户名">
						<span class="label-input100">用户名</span>
						<input id="user" class="input100" type="text" name="user" placeholder="请输入用户名" autocomplete="off">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="请输入密码">
						<span class="label-input100">密码</span>
						<input id="pass" class="input100" type="password" name="pass" placeholder="请输入密码">
						<span class="focus-input100" data-symbol="&#xf190;"></span>
					</div><br>
					
					<div class="wrap-input100 validate-input m-b-23" data-validate="请输入QQ">
						<span class="label-input100">QQ</span>
						<input id="qq" class="input100" type="text" name="qq" placeholder="请输入QQ" autocomplete="off">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>
					
					<div class="wrap-input100 validate-input m-b-23" data-validate="请输入邮箱">
						<span class="label-input100">邮箱</span>
						<input id="email" class="input100" type="text" name="email" placeholder="请输入邮箱" autocomplete="off">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
					</div>

						<div class="container-login100-form-btn">
							<div class="wrap-login100-form-btn">
								<div class="login100-form-bgbtn"></div>
								<div id="submit" class="login100-form-btn">创建商户账号</div>
							</div>
						</div><br/>
						
						<div class="container-login100-form-btn">
							<div class="wrap-login100-form-btn auth-secondary-btn">
								<div class="login100-form-bgbtn"></div>
								<a href="Login.php" class="login100-form-btn">已有账号，去登录</a>
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
    			url : "Ajax.php?act=Reg",
    			data : {user:user,pass:pass,qq:qq,email:email,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
    			dataType : 'json',
    			success : function(data) {
    				layer.close(ii);
    				if(data.code == 1){
    				    layer.alert(data.msg);
    					setTimeout(function () {
					    location.href="./";
					    }, 1000); //延时1秒跳转
    				}else if(data.code == 2){
    					var paymsg = '';
    					$.each(data.paytype, function(key, value) {
    							paymsg+='<button class="btn btn-default btn-block" onclick="window.location.href=\'SDK/epayapi.php?type='+value.name+'&WIDout_trade_no='+data.trade_no+'&WIDsubject=%E7%94%B3%E8%AF%B7%E5%95%86%E6%88%B7\'" style="margin-top:10px;"><img width="20" src="/Mym/Assets/Icon/'+value.name+'.ico" class="logo">'+value.showname+'</button>';
    					});
    					layer.alert('<center><h2>￥ '+data.need+'</h2><hr>'+paymsg+'<hr>提示：支付完成后即可直接登录</center>',{
    						btn:[],
    						title:'支付确认页面',
    						closeBtn: false
    					});
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
    		if ($(this).attr("data-lock") === "true") return;
    		var reg=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
    		var reg2=/[1-9]([0-9]{5,11})/;
    		
    		if($("input[name='user']").val()==''){
    		    layer.alert('账号不能为空！');
    		}else if($("input[name='pass']").val()==''){
    		    layer.alert('密码不能为空！');
    		}else if($("input[name='qq']").val()==''){
    		    layer.alert('QQ不能为空！');
    		}else if($("input[name='email']").val()==''){
    		    layer.alert('邮箱不能为空！');
    		}else if(!reg2.test($("input[name='qq']").val())){
    		    layer.alert('QQ格式不正确！');
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