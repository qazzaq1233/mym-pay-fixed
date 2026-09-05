<?php
$title='个人资料';
include './Head.php';
?>
<?php
$mod=isset($_GET['mod'])?$_GET['mod']:'api';

?>
<div class="block">
 <div id="content" class="app-content" role="main">
    <div class="app-content-body ">
<div class="wrapper-md control">
	<div class="row">
		<div class="col-sm-12">
			<div class="page-title-box">
				<div class="btn-group float-right">
					<ol class="breadcrumb hide-phone p-0 m-0">

					</ol>
				</div>
				<h4 class="page-title"><?=$title?></h4>
			</div>
		</div>
	</div>
 <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <!-- Nav tabs -->
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-toggle="tab" href="#grxx" role="tab">API信息</a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-toggle="tab" href="#xgmm" role="tab">修改密码</a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-toggle="tab" href="#xgmmm" role="tab">二级密码</a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-toggle="tab" href="#xgzl" role="tab">修改资料</a>
                    </li>
                </ul>
    <div class="tab-content">
        <div class="tab-pane active p-3" id="grxx" role="tabpanel">
            <p class="font-14 mb-0">
                <form class="form-horizontal devform">
                <div class="form-group">
                    <label class="col-sm-2 control-label">接口地址</label>
                    <div class="col-sm-9">
                        <div class="input-group"><input class="form-control" type="text" value="<?php echo $siteurl?>" readonly><div class="btn btn-outline-success waves-effect waves-light"><a href="javascript:;" class="copy-btn" data-clipboard-text="<?php echo $siteurl?>" title="点击复制"><i class="fa fa-copy"></i></a></div>
                        </div>
					</div>
			</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">商户ID</label>
					<div class="col-sm-9">
						<div class="input-group"><input class="form-control" type="text" value="<?php echo $userrow['pid']?>" readonly><div class="btn btn-outline-success waves-effect waves-light"><a href="javascript:;" class="copy-btn" data-clipboard-text="<?php echo $userrow['pid']?>" title="点击复制"><i class="fa fa-copy"></i></a></div></div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">商户密钥</label>
					<div class="col-sm-9">
						<div class="input-group"><input class="form-control" type="text" value="<?php echo $userrow['key']?>" readonly><div class="btn btn-outline-success waves-effect waves-light"><a href="javascript:;" class="copy-btn" data-clipboard-text="<?php echo $userrow['key']?>" title="点击复制"><i class="fa fa-copy"></i></a></div></div>
					</div>
				</div>
				<div class="line line-dashed b-b line-lg pull-in"></div>
				<div class="form-group">
				  <div class="col-sm-offset-2 col-sm-4"><a href="https://mzf.5v1.com/User/Doc.php" class="btn btn-sm btn-success" target="_blank">查看开发文档</a>&nbsp;&nbsp;<a href="javascript:resetKey()" class="btn btn-sm btn-danger">重置密钥</a>
				 </div>
				</div>
			    </form>
			</p>
			</div>
			<div class="tab-pane p-3" id="xgmm" role="tabpanel">
			    <p class="font-14 mb-0">
			    <form class="form-horizontal devform">
				<div class="form-group"><div class="col-sm-offset-2 col-sm-4"><h4>修改登录密码：</h4></div></div>
				<?php if(!empty($userrow['pass'])){?>
				<div class="form-group">
					<label class="col-sm-2 control-label">旧密码</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="oldpwd" value="">
					</div>
				</div>
				<?php }?>
				<div class="form-group">
					<label class="col-sm-2 control-label">新密码</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="newpwd" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">重复密码</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="newpwd2" value="">
					</div>
				</div>
				<div class="form-group">
				  <div class="col-sm-offset-2 col-sm-4"><input type="button" id="changePwd" value="修改密码" class="btn btn-primary form-control"/><br/>
				 </div>
				</div>
			</form>
            </p>
            </div>
            <div class="tab-pane p-3" id="xgmmm" role="tabpanel">
			    <p class="font-14 mb-0">
			    <form class="form-horizontal devform">
				<div class="form-group"><div class="col-sm-offset-2 col-sm-4"><h4>修改二级密码：</h4></div></div>
				<?php if(!empty($userrow['pay_pass'])){?>
				<div class="form-group">
					<label class="col-sm-2 control-label">旧密码</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="passpwd" value="">
					</div>
				</div>
				<?php }?>
				<div class="form-group">
					<label class="col-sm-2 control-label">新密码</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="passpwd1" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">重复密码</label>
					<div class="col-sm-9">
						<input class="form-control" type="password" name="passpwd2" value="">
					</div>
				</div>
				<div class="form-group">
				  <div class="col-sm-offset-2 col-sm-4"><input type="button" id="pay_pass" value="修改密码" class="btn btn-primary form-control"/><br/>
				 </div>
				</div>
			</form>
            </p>
            </div>
            <div class="tab-pane p-3" id="xgzl" role="tabpanel">
                <p class="font-14 mb-0">
			<div class="tab-content">
		<div class="tab-pane ng-scope active">
			<form class="form-horizontal devform">
				<div class="line line-dashed b-b line-lg pull-in"></div>
				<div class="form-group"><div class="col-sm-offset-2 col-sm-4"><h4>联系方式设置：</h4></div></div>
				<div class="form-group">
					<label class="col-sm-2 control-label">邮箱</label>
					<div class="col-sm-9">
						<div class="input-group">
						<input class="form-control" type="text" name="email" value="<?php echo $userrow['email']?>" disabled>
						<a class="btn btn-sm btn-success" id="checkbind">修改绑定</a>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">ＱＱ</label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="qq" value="<?php echo $userrow['qq']?>">
					</div>
				</div>
				<div class="form-group">
				  <div class="col-sm-offset-2 col-sm-4"><input type="button" id="editInfo" value="确定修改" class="btn btn-primary form-control"/><br/>
				 </div>
				</div>
                                                      </p>   
                                                </div>
                                            </div>
            
                                        </div>
                                    </div>
                                </div>
                            </div>
            
     <input type="hidden" id="situation" value="">
 <div id="content" class="app-content" role="main">
    <div class="app-content-body ">
		<div class="modal inmodal fade" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
					
						<h4 class="modal-title">验证密保信息</h4>
					</div>
					<div class="modal-body">
<div class="list-group-item">密保邮箱：<?php echo $userrow['email']?></div>
<div class="list-group-item">
<div class="input-group">
<input type="text" name="code" placeholder="输入验证码" class="form-control" required>
<a class="btn btn-sm btn-success" id="sendcode">获取验证码</a>
</div>
</div>
<button type="button" id="verifycode" class="btn btn-primary btn-block">确定</button>
<div id="embed-captcha"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-white" data-dismiss="modal">关闭</button>
					</div>
				</div>
			</div>
		</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script src="<?php echo $cdnpublic?>clipboard.js/1.7.1/clipboard.min.js"></script>
<script src="../Mym/Assets/Js/gt.js"></script>
<script>
	$("#checkbind").click(function(){
	    $('#myModal').modal('show');
	});
</script>

<script>
$(document).ready(function(){
	var clipboard = new Clipboard('.copy-btn');//btn btn-outline-success waves-effect waves-light
	clipboard.on('success', function (e) {
		layer.msg('复制成功！', {icon: 1});
	});
	clipboard.on('error', function (e) {
		layer.msg('复制失败，请长按链接后手动复制', {icon: 2});
	});
	$("#changePwd").click(function(){
		var oldpwd=$("input[name='oldpwd']").val();
		var newpwd=$("input[name='newpwd']").val();
		var newpwd2=$("input[name='newpwd2']").val();
		if(oldpwd==''){layer.alert('旧密码不能为空');return false;}
		if(newpwd==''||newpwd2==''){layer.alert('新密码不能为空');return false;}
		if(newpwd!=newpwd2){layer.alert('两次输入密码不一致！');return false;}
		if(oldpwd==newpwd){layer.alert('旧密码和新密码相同！');return false;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax2.php?act=edit_pwd",
			data : {oldpwd:oldpwd,newpwd:newpwd,newpwd2:newpwd2},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert(data.msg, {icon: 1}, function(){window.location.reload()});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#pay_pass").click(function(){
		var oldpwd=$("input[name='passpwd']").val();
		var newpwd=$("input[name='passpwd1']").val();
		var newpwd2=$("input[name='passpwd2']").val();
		if(newpwd==''||newpwd2==''){layer.alert('新密码不能为空');return false;}
		if(newpwd!=newpwd2){layer.alert('两次输入密码不一致！');return false;}
		if(oldpwd==newpwd){layer.alert('旧密码和新密码相同！');return false;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax2.php?act=edit_pass",
			data : {oldpwd:oldpwd,newpwd:newpwd,newpwd2:newpwd2},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert(data.msg, {icon: 1}, function(){window.location.reload()});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
});
function resetKey(){
	var confirmobj = layer.confirm('是否确认重置对接密钥？重置后需要重新登录', {
	  btn: ['确定','取消']
	}, function(){
		$.ajax({
			type : 'POST',
			url : 'Ajax.php?act=Reset_key',
			data : 'submit=do',
			dataType : 'json',
			success : function(data) {
				if(data.code == 0){
					layer.alert('重置密钥成功，程序购买联系QQ2945080486！', {icon:1}, function(){window.location.reload()});
				}else{
					layer.alert(data.msg, {icon:2});
				}
			},
			error:function(data){
				layer.msg('服务器错误');
				return false;
			}
		});
	}, function(){
		layer.close(confirmobj);
	});
}
</script>
<script>
function invokeSettime(obj){
    var countdown=60;
    settime(obj);
    function settime(obj) {
        if (countdown == 0) {
            $(obj).attr("data-lock", "false");
            $(obj).text("获取验证码");
            countdown = 60;
            return;
        } else {
			$(obj).attr("data-lock", "true");
            $(obj).attr("disabled",true);
            $(obj).text("(" + countdown + ") s 重新发送");
            countdown--;
        }
        setTimeout(function() {
                    settime(obj) }
                ,1000)
    }
}
var handlerEmbed = function (captchaObj) {
	var target;
	captchaObj.onReady(function () {
		$("#wait").hide();
	}).onSuccess(function () {
		var result = captchaObj.getValidate();
		if (!result) {
			return alert('请完成验证');
		}
		var situation=$("#situation").val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=User_Code",
			data : {situation:situation,target:target,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					new invokeSettime("#sendcode");
					layer.msg('发送成功，请注意查收！');
				}else{
					layer.alert(data.msg);
					captchaObj.reset();
				}
			} 
		});
	});
	$('#sendcode').click(function () {
		if ($(this).attr("data-lock") === "true") return;
		captchaObj.verify();
	});
	// 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
};
$(document).ready(function(){
	var items = $("select[default]");
	for (i = 0; i < items.length; i++) {
		$(items[i]).val($(items[i]).attr("default")||1);
	}
	$("select[name='stype']").change(function(){
		var input = $("select[name='stype'] option:selected").attr("input");
		$("#typename").html(input);
		if($(this).val() == 2){
			$("#getopenid_form").show();
		}else{
			$("#getopenid_form").hide();
		}
	});
	$("select[name='stype']").change();
	$("#editSettle").click(function(){
		var stype=$("select[name='stype']").val();
		var account=$("input[name='account']").val();
		var username=$("input[name='username']").val();
		if(account=='' || username==''){layer.alert('请确保各项不能为空！');return false;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=edit_settle",
			data : {stype:stype,account:account,username:username},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert('修改成功！', {icon:1});
				}else if(data.code == 2){
					$("#situation").val("settle");
					$('#myModal').modal('show');
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#editInfo").click(function(){
		var email=$("input[name='email']").val();
		var qq=$("input[name='qq']").val();
		var url=$("input[name='url']").val();
		var keylogin=$("select[name='keylogin']").val();
		if(email=='' || qq=='' || url==''){layer.alert('请确保各项不能为空！');return false;}
		if(email.length>0){
			var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
			if(!reg.test(email)){layer.alert('邮箱格式不正确！');return false;}
		}
		if (url.indexOf(" ")>=0){
			url = url.replace(/ /g,"");
		}
		if (url.toLowerCase().indexOf("http://")==0){
			url = url.slice(7);
		}
		if (url.toLowerCase().indexOf("https://")==0){
			url = url.slice(8);
		}
		if (url.slice(url.length-1)=="/"){
			url = url.slice(0,url.length-1);
		}
		$("input[name='url']").val(url);
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=edit_info",
			data : {email:email,qq:qq,url:url,keylogin:keylogin},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert('修改成功！', {icon:1});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#editChannelInfo").click(function(){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		var setting = {};
		$("input[name^='setting']").each(function(i, el) {
			setting[el.name] =$(this).val();
		});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=edit_channel_info",
			data : setting,
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert('修改成功！', {icon:1});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#editMode").click(function(){
		var mode=$("select[name='mode']").val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=edit_mode",
			data : {mode:mode},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert('修改成功！', {icon:1});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#checkbind").click(function(){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "GET",
			url : "Ajax.php?act=checkbind",
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					$("#situation").val("bind");
					$('#myModal2').modal('show');
				}else if(data.code == 2){
					$("#situation").val("mibao");
					$('#myModal').modal('show');
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#editBind").click(function(){
		var phone=$("input[name='phone_n']").val();
		var email=$("input[name='email_n']").val();
		var code=$("input[name='code_n']").val();
		if(code==''){layer.alert('请输入验证码！');return false;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=edit_bind",
			data : {phone:phone,email:email,code:code},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert('修改绑定成功！', {icon:1}, function(){window.location.reload()});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#editBindPhone").click(function(){
		var phone=$("input[name='phone_s']").val();
		var code=$("input[name='code_s']").val();
		if(code==''){layer.alert('请输入验证码！');return false;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=edit_bind",
			data : {phone:phone,code:code},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.alert('修改绑定成功！', {icon:1}, function(){window.location.reload()});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#verifycode").click(function(){
		var code=$("input[name='code']").val();
		var situation=$("#situation").val();
		if(code==''){layer.alert('请输入验证码！');return false;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "Ajax.php?act=verifycode",
			data : {code:code},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.msg('验证成功！', {icon:1});
					$('#myModal').modal('hide');
					if(situation=='settle'){
						$("#editSettle").click();
					}else if(situation=='mibao'){
						$("#situation").val("bind");
						$('#myModal2').modal('show');
					}else if(situation=='bind'){
						$('#myModal2').modal('hide');
						window.location.reload();
					}
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$.ajax({
		// 获取id，challenge，success（是否启用failback）
		url: "Ajax.php?act=Captcha&t=" + (new Date()).getTime(), // 加随机数防止缓存
		type: "get",
		asysn: true,
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
<?php include './foot.php'; ?>