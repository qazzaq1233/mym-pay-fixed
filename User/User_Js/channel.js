
//************************************以下是拉起弹窗更新  支付系统交流群：153308462***********************************

function Get_Qr(id) { //拉起弹窗更新
    $("#Up_LoginQrcode").html(''); //清空登陆码
    ZT_QrCode_ID = 0; //云端登陆二维码ID
    ZT_QrCode_URL = 0; //判断是否已经获取到二维码
    Up_QrCode_cookie = 0; //判断是否已经获取COOKIE
    Wx_guid = '';
    WX_uuid = '';
    Login_Type = '';
    $("#AliLogin").hide();//关闭
    $("#AliLogin2").hide();//关闭
    $("#html2").hide();//关闭
    $("#AliYunSms").hide();//关闭
    $("#Login_display").hide();//关闭
    $("#Login_display2").hide();//关闭
    $("#html").show();//展开
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    $.ajax({
        type: "POST",
        url: "Ajax.php?act=Get_Qr",
        data: {
            id
        },
        dataType: 'json',
        timeout: 15000,
        //ajax请求超时时间15s
        success: function(data) {
            layer.close(ii);
            if (data.id) {
                $(function() {
                    setTimeout(function() {
                        var buy = document.getElementById('Up_Qr_modal');
                        buy.click();
                    },200);
                });
                $("#Up_Qr_modal").click(function() {
                    console.log("MYM牛逼牛逼！")
                });
                $("#Up_LoginQrcode_msg").html('<center>正在提交获取二维码请求...</center>'); //输出提示
                if (data.type == 'alipay'){
                    var is_type = '支付宝';
                }else if (data.type == 'qqpay'){
                    var is_type = 'QQ';
                }else{
                    var is_type = '微信';
                }
                $("#Up_id").val(data.id);
                $("#Up_type").val(is_type);
                $("#Up_beizhu").val(data.beizhu); //备注
                Up_id = data.qrdata.id;
                type = data.qrdata.type; //类型
                beizhu = data.beizhu; //备注
                hook = data.qrdata.hook_type;
                cookie = data.qrdata.cookie;
                channel = data.qrdata.channel;
                json = data.qrdata.json;
                if (data.type == 'wxpay' && hook==0 && channel!='mg_vzq') {
                    var paymsg = '';
                    $.each(data.data,function(key, value){
                        paymsg += '<button class="btn btn-default btn-block" style="margin-top:10px;"><img width="20" src="/Mym/Assets/Icon/wxpay.ico" class="logo">[' + value.wx_name + '] 账号->' + value.wx_user + '</button>';
                    });
                    $("#Up_beizhu_name").html('微信昵称'); //输出提示
                    $("#Up_LoginQrcode_msg").html(paymsg + '<br>请添加以上任意一个微信为好友,并发送店员邀请小程序(搜索[收款小账本]->收款店员->添加店员->邀请微信朋友成为店员->发送给当前微信号即可)'); //输出提示
                    $("#Up_LoginQrcode").html('<small style="color:red; font-size:16px">每次微信CK失效都点击一次更新获取最新微信号再发送邀请小程序</small><br>请注意看以下的操作<br>发送之后一段时间未绑定,请联系客服进行审核<br>如果你的微信提示绑定成功了,刷新平台cookie还没更新,请解绑店员再发发送绑定邀请<br>如果在10分钟内未绑定,想要再次绑定的时候必须点击“更新cookie”才能再次发送店员绑定,否则绑定成功了平台cookie也不会更新<br>成功登录的微信请不要随意更换昵称,如果更换昵称,需要删除二维码重新登录绑定!'); //输出登录二维码
                }else if(data.type == 'alipay' && hook==0){
                    $("#html").hide();//关闭
                    $("#AliYunGet").hide();//关闭
                    $("#html2").show();//展开
                    $("#exampleModalLabelL").html('<center>选择登录方式</center>'); //输出提示
                    html = '<a id="QrLogin"><button type="sumbit" class="btn btn-primary btn-block" onclick="QR_setInterval();">扫码登录</button></a><br>';
                    html = html+'<a id="MmLogin"><button type="sumbit" class="btn btn-primary btn-block" onclick="AliLogin_DL();">账号密码登录</button></a>';
                    $("#html2").html(html);
                }else if(data.type == 'alipay' && hook==2){
                    $("#exampleModalLabelL").html('<center>支付宝免挂配置</center>'); //输出提示
                    AliYunGet();
                }else if(data.type == 'qqpay' && hook==2){
                    /*
                    $("#exampleModalLabelL").html('<center>选择登录方式</center>'); //输出提示
                    $("#html").hide();//关闭
                    $("#Login_display").show();//展开
                    */
                    QR_setInterval();
                }else if(data.type == 'wxpay' && hook==2){
                    /*
                    $("#exampleModalLabelL").html('<center>选择登录方式</center>'); //输出提示
                    $("#html").hide();//关闭
                    $("#Login_display2").show();//展开
                    */
                    if(data.qrdata.crontime>data.time){
                        $("#exampleModalLabelL").html('<center>设置商户</center>'); //输出提示
                        skdtypeuser();
                    }else{
                        QR_setInterval();
                    }
                } else {
                    QR_setInterval();
                }
            }
        },
        error: function(data) {
            layer.close(ii);
            layer.msg('操作失败,服务器错误,ID：' + id + data);
            setTimeout(function() {
                location.href = "?";
            },3000); //延时1秒跳转
        }
    });
}

$("select[name='Login_Type']").change(function(){
	Login_Type = $(this).val();
	if(Login_Type!=''){
	    QR_setInterval();
	}
	console.log(Login_Type);
});

$("select[name='Login_Type2']").change(function(){
	Login_Type = $(this).val();
	if(Login_Type!=''){
	    QR_setInterval();
	}
	console.log(Login_Type);
});

function skdtypeuser() {
    $("#html").hide();//关闭
    $("#html2").hide();//关闭
    $("#AliLogin").hide();//关闭
    $("#AliLogin2").hide();//关闭
    $("#AliYunGet").hide();//关闭
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=skdtypeuser",
        data: {id:Up_id,cookie,json},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        if(data.code==200){
            html='<div class="form-group"><label>选择商户:</label><br><select class="form-control" name="account_id" id="account_id">'+data.html+'</select></div>';
            html = html+'</br><button type="sumbit" class="btn btn-primary btn-block" onclick="skdtypeSet();">更新商户配置</button>';
            $("#html2").html(html);//展开
            $("#html2").show();//展开
        }else{
            layer.msg(data.msg);
            setTimeout(function() {
                location.href = "?";
            },3000); //延时1秒跳转
        }
    }});
}

function skdtypeSet(){
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    var account_id=$("#account_id").val();
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=skdtypeSet",
        data: {id:Up_id,account_id},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        layer.close(ii);
        if(data.code==200){
            layer.alert(data.msg, { icon: 1 }, function() { location.href = "?"; }); //跳转
        }else{
            layer.msg(data.msg);
            setTimeout(function() {
                location.href = "?";
            },3000); //延时1秒跳转
        }
    }});
}


//************************************以下是更新免CK配置  支付系统交流群：153308462*************************************

function setAliOrderCheck(value) {
    if(value!='order_no')value='order_amount';
    $("#ali_order_check").val(value);
    $(".ali-order-check-btn").removeClass('btn-primary').addClass('btn-outline-primary');
    $(".ali-order-check-btn[data-value='"+value+"']").removeClass('btn-outline-primary').addClass('btn-primary');
}

function AliYunGet() {
    $("#html").hide();//关闭
    $("#html2").hide();//关闭
    $("#AliLogin").hide();//关闭
    $("#AliLogin2").hide();//关闭
    $("#AliYunGet").show();//展开
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=AliYunGet",
        data: {id:Up_id},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        if(data){
            $("#appid").val(data.appid);
            $("#appkey2").val(data.appkey2);
            $("#yun_qr_url").val(data.qr_url);
            $("#ali_balance").val(data.money ? '￥ '+data.money : '');
            setAliOrderCheck(data.ali_order_check || 'order_amount');
        }else{
            $("#appid").val('');
            $("#appkey2").val('');
            $("#yun_qr_url").val('');
            $("#ali_balance").val('');
            setAliOrderCheck('order_amount');
        }
    }});
}

function AliYunApp() {
    $("#html").hide();//关闭
    $("#html2").hide();//关闭
    $("#AliLogin").hide();//关闭
    $("#AliLogin2").hide();//关闭
    $("#AliYunGet").hide();//关闭
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=AliYunApp",
        data: {id:Up_id},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        if(data.code==200){
            html='<div class="form-group"><label>选择应用:</label><br><select class="form-control" name="appid_num" id="appid_num">'+data.html+'</select></div>';
            html = html+'</br><button type="sumbit" class="btn btn-primary btn-block" onclick="AliYunSet();">更新应用配置</button>';
            $("#html2").html(html);//展开
            $("#html2").show();//展开
        }else{
            layer.msg(data.msg);
            setTimeout(function() {
                location.href = "?";
            },3000); //延时1秒跳转
        }
    }});
}

function AliYunSet(){
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    var appid=$("#appid_num").val();
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=AliYunSet",
        data: {id:Up_id,appid,cookie},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        layer.close(ii);
        if(data.code==200){
            layer.alert(data.msg, { icon: 1 }, function() { location.href = "?"; }); //跳转
        }else if(data.code==1001){
            layer.msg(data.msg);
            var phone = document.getElementById("phone2");
            phone.setAttribute("value",data.phone);
            var appidset = document.getElementById("appid6");
            appidset.setAttribute("value",appid);
            $("#html").hide();//关闭
            $("#html2").hide();//关闭
            $("#AliLogin").hide();//关闭
            $("#AliLogin2").hide();//关闭
            $("#AliYunGet").hide();//关闭
            $("#AliYunSms").show();//展开
        }else{
            layer.msg(data.msg);
            setTimeout(function() {
                location.href = "?";
            },3000); //延时1秒跳转
        }
    }});
}

function saveInfo() {
	var appid=$.trim($("#appid").val());
	var appkey2=$.trim($("#appkey2").val());
	var qr_url=$.trim($("#yun_qr_url").val());
	var ali_order_check=$("#ali_order_check").val() || 'order_amount';
	if(appid=='' || appkey2=='' || qr_url==''){layer.alert('请确保AppID、应用私钥和支付宝收款码链接不能为空！');return false;}
	if(!/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/)/i.test(qr_url)){
		layer.alert('支付宝收款码链接格式不正确，请填写 alipays://、alipayqr:// 或 https:// 开头的链接！');
		return false;
	}
	$('#save').val('Loading');
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "Ajax2.php?act=user_settle_save",
		data : {id:Up_id,appid,appkey2,qr_url,ali_order_check},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 1){
				layer.alert(data.msg, { icon: 1 }, function() { location.href = "?"; }); //跳转
				//listTable();
			}else{
				layer.alert(data.msg, { icon: 2 }, function() { location.href = "?"; }); //跳转
			}
			$('#save').val('保存');
		} 
	});
}

function AliYunSms() { 
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    var smscode=$("#smscode2").val();
    var phone=$("#phone2").val();
    var appid=$("#appid6").val();
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=AliYunSms",
        data: {id:Up_id,appid,phone,smscode,cookie},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        layer.close(ii);
        if(data.code=='200'){
            layer.alert(data.msg, { icon: 1 }, function() { location.href = "?"; }); //跳转
        }else{
            layer.msg(data.msg);
        }
    },error: function(data) {
        layer.close(ii);
        layer.msg('操作失败,服务器错误,ID：' + Up_id);
        setTimeout(function() {
            location.href = "?";
        },3000); //延时1秒跳转
    }
    });
}


//************************************以下是更新账号密码登录  支付系统交流群：153308462***********************************

function AliLogin_DL() {
    $("#exampleModalLabelL").html('<center>支付宝账号密码登录</center>'); //输出提示
    $("#html").hide();//关闭
    $("#html2").hide();//关闭
    $("#AliLogin2").hide();//关闭
    $("#AliLogin").show();//展开
}

function AliLogin() { 
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    var user=$("#J-input-user").val();
	var pass=$("#password_rsainput").val();
	var n=$("#n").val();
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=AliLogin",
        data: {id:Up_id,user,pass,n},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        layer.close(ii);
        if(data.code=='200'){
            layer.alert(data.msg, { icon: 1 }, function() { location.href = "?"; }); //跳转
        }else if(data.code=='1001'){
            layer.msg(data.msg);
            var phone = document.getElementById("phone");
            phone.setAttribute("value",data.data.phone);
            securityId = data.data.securityId;
            ALIPAYJSESSIONID = data.data.ALIPAYJSESSIONID;
            _form_token = data.data._form_token;
            $("#AliLogin").hide();//关闭
            $("#AliLogin2").show();//展开
        }else{
            layer.msg(data.msg);
        }
    },error: function(data) {
        layer.close(ii);
        layer.msg('操作失败,服务器错误,ID：' + Up_id);
        setTimeout(function() {
            location.href = "?";
        },3000); //延时1秒跳转
    }
    });
}

function AliLogin_Sms() { 
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    var smscode=$("#smscode").val();
    $.ajax({
        type: "POST",
        url: "Ajax3.php?act=AliLogin_Sms",
        data: {id:Up_id,smscode,securityId,ALIPAYJSESSIONID,_form_token},
        dataType: 'json',
        timeout: 15000,
    success: function(data) {
        layer.close(ii);
        if(data.code=='200'){
            layer.alert(data.msg, { icon: 1 }, function() { location.href = "?"; }); //跳转
        }else{
            layer.msg(data.msg);
        }
    },error: function(data) {
        layer.close(ii);
        layer.msg('操作失败,服务器错误,ID：' + Up_id);
        setTimeout(function() {
            location.href = "?";
        },3000); //延时1秒跳转
    }
    });
}


//*************************************以下是更新cookie JS  支付系统交流群：153308462*************************************

console.log("您使用的是MYM码支付系统");

function isValidLoginQrUrl(qrUrl) {
    if (typeof qrUrl !== 'string') return false;
    qrUrl = $.trim(qrUrl);
    if (qrUrl === '' || qrUrl === 'undefined' || qrUrl === 'null') return false;
    return /^(https?:\/\/|data:image\/|\/)/i.test(qrUrl);
}

function INTL_Zero_setInterval() {
    //开始获取登陆二维码
    if (ZT_QrCode_ID == 0) {
        var ii = layer.load(5, {
            shade: [0.1, '#fff']
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "Ajax.php?act=Get_Login_QrCode",
            timeout: 10000, //ajax请求超时时间10s
            data: {qr_id: Up_id,type: type, beizhu: beizhu, hook: hook, Login_Type, channel}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                layer.close(ii);
                if (data && data.code == 1 && isValidLoginQrUrl(data.qr_url)) {
                    ZT_QrCode_ID = data.id;
                    ZT_QrCode_URL = $.trim(data.qr_url);
                    if(type=='wxpay' && hook==2){
                        Wx_guid = data.guid;
                        WX_uuid = data.uuid;
                    }
                    if (type == 'alipay') var is_type = '"支付宝"手机摄像头扫一扫,<small style="color:red; font-size:16px">并关闭支付宝自动转入余额宝,否则到账不回调</small>';
                    else if (type == 'qqpay' || channel=='yd_vzq') var is_type = '"QQ"截图发送给QQ好友并识别图片登陆或手机摄像头扫一扫';
                    else var is_type = '"微信"截图发送给QQ好友并识别图片登陆或手机摄像头扫一扫';
                    $("#Up_LoginQrcode_msg").html('<center>请您使用' + is_type + '->"2分钟内"扫以下码登录,扫码之后请返回此页面等待1分钟,如超过1分钟则系统登录失败,请您再次重试</center>'); //输出提示
                    $("#Up_LoginQrcode").html('<center><img align="center" id="qrcodeimg" alt="加载中..." src="' + ZT_QrCode_URL + '" title="扫码登录" width="200" height="200" style=" position: relative; border: green solid 1px;"></center>'); //输出登录二维码
                    setTimeout("INTL_Zero_setInterval()", 1000);
                } else {
                    ZT_QrCode_ID = 0;
                    ZT_QrCode_URL = 0;
                    var msg = (data && data.msg) ? data.msg : '获取登录二维码失败';
                    if(data && data.code == 1 && !isValidLoginQrUrl(data.qr_url)){
                        msg = '云端返回的二维码地址无效，请检查云端接口返回格式';
                    }
                    $("#Up_LoginQrcode_msg").html('<center style="color:red;">' + msg + '</center>');
                    $("#Up_LoginQrcode").html('');
                    layer.alert(msg, { icon: 2 }, function() { location.href = "?"; });
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout("INTL_Zero_setInterval()", 1000);
                } else { //异常
                    ZT_QrCode_ID = 0;
                    ZT_QrCode_URL = 0;
                    var msg = '获取登录二维码失败：服务器返回异常';
                    if(XMLHttpRequest && XMLHttpRequest.responseText){
                        msg += '，请检查 Ajax.php 输出是否为 JSON';
                    }
                    $("#Up_LoginQrcode_msg").html('<center style="color:red;">' + msg + '</center>');
                    $("#Up_LoginQrcode").html('');
                    layer.alert(msg, { icon: 2 }, function() { location.href = "?"; });
                }
            }
        });
    }
    console.log('支付系统交流群：153308462');
    //开始检测登陆获取COOKIE并自动更新
    if (ZT_QrCode_ID != 0 && Up_QrCode_cookie == 0) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "Ajax.php?act=Get_Login_Cookie",
            timeout: 10000, //ajax请求超时时间10s
            data: {id: ZT_QrCode_ID, type: type, qr_id: Up_id, hook:hook, guid:Wx_guid, uuid:WX_uuid, channel}, //post数据
            success: function (data, textStatus) {
                //从服务器得到数据，显示数据并继续查询
                if (data.code == 200) {
                    Up_QrCode_cookie = data.cookie;
                    layer.alert(data.msg, { icon: 1 }, function() { location.href = "?"; }); //跳转
                    //QR_setInterval(1);
                } else if (data.code == -1) {
                    if(data.code == 2){
                        $("#Up_LoginQrcode_msg").html('<center>扫码成功，请在手机上点击确认登录...</center>'); //输出提示
                        $("#Up_LoginQrcode").html('<center><img align="center" id="qrcodeimg" alt="加载中..." src="/Mym/Assets/Icon/pay_ok.png" title="扫码成功" width="200" height="200" style=" position: relative; border: green solid 1px;"></center>'); //输出登录二维码
                        setTimeout("INTL_Zero_setInterval()", 1000);
                    }else if(data.code == -1){
                        ZT_QrCode_ID = 0;
                        ZT_QrCode_URL = 0;
                        layer.alert(data.msg, { icon: 2 }, function() { location.href = "?"; }); //跳转
                    }
                } else{
                    setTimeout("INTL_Zero_setInterval()", 1000);
                }
            },
            //Ajax请求超时，继续查询
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                if (textStatus == "timeout") {
                    setTimeout("INTL_Zero_setInterval()", 1000);
                } else { //异常
                    setTimeout("INTL_Zero_setInterval()", 3000);
                }
            }
        });
    }
}

function QR_setInterval(type){
    //var myVar = setInterval(function(){ INTL_Zero_setInterval() }, 5000);
    window.onload = INTL_Zero_setInterval();
    if(type){
        //clearInterval(myVar);//判断变量，是否有值，有责停止运行
    }else{
        $("#Login_display").hide();//关闭
        $("#Login_display2").hide();//关闭
        $("#html2").hide();//关闭
        $("#exampleModalLabelL").html('<center>更新二维码</center>'); //输出提示
        ZT_QrCode_ID = 0;
        html = '<input type="hidden" name="Up_id" id="Up_id" value=""/>';
        html = html+'<div class="form-group">';
        html = html+'<label id="Up_LoginQrcode_msg">请用对应二维码的支付宝或QQ扫码哦</label><br>';
        html = html+'<div id="Up_LoginQrcode"></div>';
        html = html+'<a id="Up_Wx_Sumbit"></a>';
        html = html+'</div>';
        $("#html").html(html);
        $("#html").show();//展开
    }
}
console.log("作者联系方式QQ485570653");


//*************************************以下是更新其他操作 JS  支付系统交流群：153308462*************************************

$(document).ready(function() {
    var type = $("#type").val();
    $('.picurl > input').bind('focus mouseover',
        function() {
            if (this.value) {
                this.select()
            }
        });
    $("input[type='file']").change(function(e) {
        $('#qr_url').val('解码中');
        Upload(this.files)
    });
});

function Upload() {
    var ii = layer.load(3, {
        shade: [0.1, '#fff']
    });
    var file = document.getElementById("imgfile").files[0];
    var formData = new FormData();
    formData.append('image_field', file);
    var type = $("#type").val();
    var channel = $("#channel").val() || type;
    $.ajax({
        url: "Ajax.php?act=Add_Qrcode&channel="+channel,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        mimeType: "multipart/form-data",
        dataType: 'json',
        success: function(data) {
            if (data.code == 1) {
                layer.close(ii);
                $('#qr_url').val(data.qrcode);
                var type = $("#type").val();
                if (type == 'wxpay') {
                    $("#beizhu_name").html('微信昵称'); //输出提示
                    $("#beizhu").val(''); //输出提示
                    $("#LoginQrcode_msg").html('以上必须填写即将登陆微信的完整[微信昵称],不要带花里胡哨的昵称,否则无法成功登陆上'); //输出提示
                    $("#Wx_Sumbit").html('</br><button type="sumbit" class="btn btn-primary btn-block" onclick="GET_wx_QR();">确认以上并添加</button>');
                } else if (data.qrcode) {
                    $("#Wx_Sumbit").html('</br><button type="sumbit" class="btn btn-primary btn-block" onclick="Add_Qr();">确认以上并添加</button>');
                } else {
                    layer.msg('添加或解二维码失败');
                }
            } else {
                layer.close(ii);
                layer.msg(data.msg);
                setTimeout(function() {
                    location.href = "?";
                },3000); //延时1秒跳转
            }
        },
        error: function(data) {
            layer.close(ii);
            layer.msg('请剪切边框或更换其他二维码重试');
            setTimeout(function() {
                location.href = "?";
            },3000); //延时1秒跳转
        }
    });
}

function isQrUrlMatchesPayType(type, url) {
    url = decodeURIComponent($.trim(url || '')).toLowerCase();
    if(url === '')return true;
    if(type === 'alipay')return !/(qianbao\.qq\.com|tenpay\.com|mqqapi:\/\/|weixin:\/\/|wxp:\/\/)/i.test(url);
    if(type === 'qqpay')return !/(alipays?:\/\/|alipayqr:\/\/|alipay\.com|render\.alipay\.com|ds\.alipay\.com)/i.test(url);
    if(type === 'wxpay')return !/(alipays?:\/\/|alipayqr:\/\/|alipay\.com|render\.alipay\.com|ds\.alipay\.com)/i.test(url);
    return true;
}

function Add_Qr() { //添加二维码
    var type = $("#type").val();
    var qr_url = $.trim($("#qr_url").val());
    var custom_qr_url = $.trim($("#custom_qr_url").val());
    var hook_type = $("#hook_type").val();
    var beizhu = $("#beizhu").val();
    var receiver_surname = $.trim($("#receiver_surname").val());
    var Login_Type = $("#Login_Type").val();
    var ali_order_check = 'order_amount';
    var channel = $("#channel").val() || type;

    if(type=='alipay' && channel=='mg_ali' && custom_qr_url!='' && !/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/)/i.test(custom_qr_url)){
        layer.alert('支付宝收款码链接格式不正确，请填写 alipays://、alipayqr:// 或 https:// 开头的链接！');
        return false;
    }
    if(custom_qr_url!='' && !/^(https?:\/\/|alipays:\/\/|alipayqr:\/\/|weixin:\/\/|mqqapi:\/\/)/i.test(custom_qr_url)){
        layer.alert('自定义收款码链接格式不正确，请填写 http(s)、alipays、alipayqr、weixin 或 mqqapi 开头的链接！');
        return false;
    }
    if(custom_qr_url!='' && !isQrUrlMatchesPayType(type, custom_qr_url)){
        layer.alert('自定义收款码链接与所选支付类型不匹配，请检查是否选错了支付宝/QQ/微信通道！');
        return false;
    }
    if(qr_url!='' && qr_url!='解码中' && !isQrUrlMatchesPayType(type, qr_url)){
        layer.alert('收款码链接与所选支付类型不匹配，请检查是否选错了支付宝/QQ/微信通道！');
        return false;
    }

    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    $.ajax({
        type: "POST",
        url: "Ajax.php?act=Add_Qr",
        data: {
            type,
            qr_url,
            custom_qr_url,
            channel,
            Login_Type,
            beizhu,
            receiver_surname,
            ali_order_check
        },
        dataType: 'json',
        timeout: 15000,
        success: function(data) {
            layer.close(ii);
            if (data.code == 1) {
                layer.msg(data.msg);
                setTimeout(function() {
                    location.href = "?";
                },3000); //延时1秒跳转
            } else if (data.code == -2) {
                layer.msg(data.msg);
            } else {
                layer.alert(data.msg, { icon: 2 }, function() { location.href = "?"; }); //跳转
            }
        },
        error: function(data) {
            layer.close(ii);
            layer.msg('操作失败,服务器错误');
        }
    });
}

function Set_Receiver_Surname(id, receiver_surname) {
    receiver_surname = receiver_surname || '';
    var html = '<div style="padding:15px 20px 5px 20px;">';
    html += '<div class="form-group"><label>收款人姓</label><input type="text" maxlength="10" class="form-control" id="set_receiver_surname" value="'+receiver_surname.replace(/"/g, '&quot;')+'" placeholder="可选：如 群、张、李，不填则支付页不显示"></div>';
    html += '<div class="form-group"><small style="color:#888;">每个通道可单独设置。填写后支付页面会显示“姓：X”，不填写则不显示。</small></div>';
    html += '</div>';
    layer.open({
        type: 1,
        title: '设置收款人姓 #' + id,
        area: ['420px'],
        content: html,
        btn: ['保存', '取消'],
        yes: function(index){
            Submit_Receiver_Surname(id, index);
        }
    });
}

function Submit_Receiver_Surname(id, layerIndex) {
    var receiver_surname = $.trim($('#set_receiver_surname').val());
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type: 'POST',
        url: 'Ajax.php?act=Set_Receiver_Surname',
        data: {id: id, receiver_surname: receiver_surname},
        dataType: 'json',
        timeout: 15000,
        success: function(data) {
            layer.close(ii);
            if(data.code == 1){
                layer.alert(data.msg || '保存成功', {icon: 1}, function(){
                    layer.close(layerIndex);
                    location.href = '?';
                });
            }else{
                layer.alert(data.msg || '保存失败');
            }
        },
        error: function(){
            layer.close(ii);
            layer.msg('服务器错误');
        }
    });
}

function Test_Qr(id) {
    var html = '<div style="padding:15px 20px 5px 20px;">';
    html += '<div class="form-group"><label>测试金额</label><input type="number" step="0.01" min="0.01" class="form-control" id="test_qr_money" value="0.01" placeholder="请输入测试金额"></div>';
    html += '<div class="form-group"><label>商品名称</label><input type="text" class="form-control" id="test_qr_name" value="测试商品" readonly></div>';
    html += '<div class="form-group"><small style="color:#888;">将只使用当前这一个已添加账号发起订单，用于测试支付和回调是否正常。测试商品名称固定为“测试商品”，便于系统用当前商户密钥验证回调。</small></div>';
    html += '</div>';
    layer.open({
        type: 1,
        title: '测试通道 #' + id,
        area: ['420px'],
        content: html,
        btn: ['发起测试订单', '取消'],
        yes: function(index){
            Submit_Test_Qr(id, index);
        }
    });
}

function Submit_Test_Qr(id, layerIndex) {
    var money = $.trim($('#test_qr_money').val());
    var name = $.trim($('#test_qr_name').val());
    if(money=='' || isNaN(money) || parseFloat(money)<=0){
        layer.alert('请输入正确的测试金额');
        return false;
    }
    if(name==''){
        layer.alert('商品名称不能为空');
        return false;
    }
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type: 'POST',
        url: 'Ajax.php?act=Test_Qr_Order',
        data: {id: id, money: money, name: name},
        dataType: 'json',
        timeout: 25000,
        success: function(data) {
            layer.close(ii);
            if(data.code == 1){
                layer.close(layerIndex);
                layer.confirm(data.msg || '测试订单创建成功，是否立即跳转支付？', {
                    btn: ['立即支付', '查看订单号']
                }, function(){
                    window.open(data.url, '_blank');
                }, function(){
                    layer.alert('测试订单号：' + data.trade_no + '<br>商户订单号：' + data.out_trade_no + '<br>实付金额：' + data.price);
                });
            }else{
                layer.alert(data.msg || '测试订单创建失败');
            }
        },
        error: function(xhr, status){
            layer.close(ii);
            var msg = '服务器错误';
            if(status === 'timeout'){
                msg = '生成订单超时，通道接口响应较慢，请稍后重试';
            }else if(xhr && xhr.responseText){
                msg = '服务器错误：' + String(xhr.responseText).replace(/<[^>]+>/g,'').substr(0,120);
            }
            layer.alert(msg);
        }
    });
}

function Del_Qr_status(id) { 
    var ii = layer.load(2, {
        shade: [0.1, '#fff']
    });
    var confirmobj = layer.confirm('操作数据，是否确定？', {
            btn: ['确定', '取消']
    },function() {
        $.ajax({
            type: 'POST',
            url: "Ajax2.php?act=Del_Qr_status",
            data: {id},
            dataType: 'json',
            success: function(data) {
                layer.close(ii);
                if (data.code == 1) {
                    layer.alert(data.msg, {
                        icon: 1
                    },function() {
                        location.href = "?";
                    });
                } else {
                    layer.alert(data.msg);
                }
            },
            error: function(data) {
                layer.msg('服务器错误');
                return false;
            }
        });
    },
    function() {
        layer.close(confirmobj);
    });
}

function Del_Qr(id) { //删除二维吗
    var confirmobj = layer.confirm('此操作将会删除此数据，是否确定？', {
        btn: ['确定', '取消']
    },
    function() {
        var ii = layer.load(2, {
            shade: [0.1, '#fff']
        });
        $.ajax({
            type: 'POST',
            url: "Ajax.php?act=Del_Qr",
            data: {
                id
        },
        dataType: 'json',
        success: function(data) {
            layer.close(ii);
            if (data.code == 1) {
                layer.alert(data.msg, {
                    icon: 1
                },function() {
                    location.href = "?";
                });
            } else {
                layer.alert(data.msg);
            }
        },error: function(data) {
            layer.msg('服务器错误');
            return false;
        }
        });
    },function() {
        layer.close(confirmobj);
    });
}


function GET_wx_QR() {
    var beizhu = $("#beizhu").val();
    if (beizhu == '') {
        layer.alert('微信昵称不能为空！');
        return false;
    }
    Add_Qr();
}