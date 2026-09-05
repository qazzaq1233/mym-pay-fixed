<?php
$id = $_GET['id'];
?>
<div class="form-group">
    <p><label id="Up_LoginQrcode_msg">请用对应二维码的支付宝或QQ扫码哦</label></p><br>
    <div id="Up_LoginQrcode"></div>
    <a id="Up_Wx_Sumbit"></a>
</div>
<!--<script type="text/javascript" src="../../assets/js/wxyun.js"></script>-->
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/layer/3.5.1/layer.min.js"></script>
<script>
Get_Wxyun('<?=$id?>');
function Add_Wxyun() { //添加二维码
    var type = "wxpay";
    var beizhu = "微信云端";
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    $.ajax({
        type: "POST",
        url: "../../Ajax.php?act=Add_Qr",
        data: {
            type,
            beizhu
        },
        dataType: 'json',
        timeout: 15000,
        //ajax请求超时时间15s
        success: function(data) {
            layer.close(ii);
            if (data.code == 1) {
                layer.msg(data.msg);
                setTimeout(function() {
                        location.href = "?";
                    },
                    3000); //延时1秒跳转
            } else if (data.code == -2) {
                layer.msg(data.msg);
            } else {
                layer.alert(data.msg, { icon: 2 }, function() { location.href = "./"; }); //跳转
            }
        },
        error: function(data) {
            layer.close(ii);
            layer.msg('操作失败,服务器错误');
        }
    });
}


function Get_Wxyun(id) { //拉起弹窗更新
    $("#Up_LoginQrcode").html(''); //清空登陆码
    ZT_WXYUN_ID = 'INTL'; //云端登陆二维码ID
    ZT_QrCode_URL = 0; //判断是否已经获取到二维码
    Up_QrCode_cookie = 0; //判断是否已经获取COOKIE
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    $.ajax({
        type: "POST",
        url: "../../Ajax.php?act=Get_Qr",
        data: {
            id
        },
        dataType: 'json',
        timeout: 15000,
        //ajax请求超时时间15s
        success: function(data) {
            layer.close(ii);
            if (data.id) {
                $("#Up_Ali_Qr_msg").html('<center>正在提交获取二维码请求...</center>'); //输出提示
                $("#Up_LoginQrcode_msg").html('<center>正在提交获取二维码请求...</center>'); //输出提示
                var is_type = '微信';
                $("#Up_id").val(data.id);
                $("#Up_type").val(is_type);
                Up_id = data.id;
                type = data.type; //类型
                beizhu = data.beizhu; //备注
                ZT_WXYUN_ID = 0;
                //周期监听 
                WXYUN_setInterval();
                

            }
        },
        error: function(data) {
            layer.close(ii);
            layer.msg('操作失败,服务器错误,ID：' + id + data);
            setTimeout(function() {
                    location.href = "?";
                },
                3000); //延时1秒跳转
        }
    });
}


function WXPAY_setInterval() {
    //开始获取登陆二维码
    if (ZT_WXYUN_ID == 0) {
        var ii = layer.load(5, {
            shade: [0.1, '#fff']
        });
        $.get("../../Ajax.php?act=Get_Login_QrCode", {
            type: type,
            hook: '2',
            beizhu: beizhu
        },
        function(data) {
            layer.close(ii);
            if (data.qr_url != '') {
                ZT_QrCode_GUID = data.guid;
                ZT_QrCode_UUID = data.uuid;
                ZT_QrCode_URL = data.qr_url;
                ZT_WXYUN_ID = 1;
                var is_type = '"微信"手机摄像头扫一扫,<small style="color:red; font-size:16px"></small>';
                $("#Up_LoginQrcode_msg").html('<center>请您使用' + is_type + '->5分钟内扫以下码登录,扫码之后请返回此页面等待<br/>如超过5分钟则系统登录失败,请您再次重试<br/><br/>特别注意！！！！！！！<br/>微信登录地区：广东广州<br/>微信登录设备： Windows<br/>登录的时候如果需要填写这些信息的话，请按照这个来填写</center>'); //输出提示
                $("#Up_LoginQrcode").html('<center><img align="center" id="qrcodeimg" alt="加载中..." src="' + data.qr_url + '" title="扫码登录" width="200" height="200" style=" position: relative; border: green solid 1px;"></center>'); //输出登录二维码
            } else if (data.code == -1) {
                layer.close(ii);
                layer.msg(data.msg + '获取登录二维码失败');
                setTimeout(function() {
                    location.href = "?";
                },3000); //延时1秒跳转
            }
        },"JSON");
    }

    //开始检测登陆获取COOKIE并自动更新
    if (ZT_WXYUN_ID != 0 && Up_QrCode_cookie == 0) {
        //layer.msg(ZT_QrCode_ID+'微信等待绑定中...');
        $.get("../../Ajax.php?act=Get_Login_Cookie", {
                guid: ZT_QrCode_GUID,
                uuid: ZT_QrCode_UUID,
                type: type,
                qr_id: Up_id,
                hook: '2'
            },
            function(data) {
                if (data.code == 1) {
                    Up_QrCode_cookie = data.cookie;
                    layer.msg('扫码登录成功,正在更新数据...');
                    Up_Qr(data.cookie,data.user,data.nickName);
                } else if (data.code == -1) {
                    ZT_QrCode_ID = 0;
                    ZT_QrCode_URL = 0;
                    layer.close(ii);
                    setTimeout(function() {
                            location.href = "?";
                        },
                        3000); //延时1秒跳转
                } else if (data.code == 2) {
                    //layer.msg(data.msg+'正在检测COOKIE完整性...'+ZT_QrCode_ID);
                    $("#Up_LoginQrcode_msg").html('<center>扫码成功，请在手机上点击确认登录...</center>'); //输出提示Mym/Assets/Icon
                    $("#Up_LoginQrcode").html('<center><img align="center" id="qrcodeimg" alt="加载中..." src="/Mym/Assets/Icon/pay_ok.png" title="扫码成功" width="200" height="200" style=" position: relative; border: green solid 1px;"></center>'); //输出登录二维码
                }
            },
            "JSON");
    }
}
function Up_Qr(cookie,wx_user,wx_name) { //更新二维码
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    $.ajax({
        type: "POST",
        url: "../../Ajax.php?act=Up_Qr",
        data: {
            id: Up_id,
            cookie,
            wx_name,
            wx_user
        },
        dataType: 'json',
        timeout: 15000,
        //ajax请求超时时间15s
        success: function(data) {
            layer.close(ii);
            layer.msg(data.msg);
            if (data.code == 1) {
                setTimeout(function() {
                    location.href = "?";
                }, );
            }
        },
        error: function(data) {
            layer.close(ii);
            layer.msg('操作失败,服务器错误');
        }
    });
}
function WXYUN_setInterval(){
//周期监听 
window.setInterval(function() {
        WXPAY_setInterval();
    },
    3000);
}
</script>