<?php
include("../../Mym/Common.php");
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>基础表单</title>
    <link rel="stylesheet" href="../assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="../assets/module/admin.css?v=318"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        #formBasForm {
            max-width: 700px;
            margin: 30px auto;
        }

        #formBasForm .layui-form-item {
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
<!-- 正文开始 -->
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <!-- 表单开始 -->
            <form class="layui-form" id="userInfoForm">
            <div class="layui-card-body">
                <div class="layui-form-item layui-row">
                    <div class="layui-inline layui-col-md4">
                        <label class="layui-form-label layui-form-required">计划任务访问密钥:</label>
                        <div class="layui-input-block">
                            <input name="cronkey" placeholder="请输计划任务访问密钥" value="<?=$conf['cronkey']; ?>" class="layui-input" lay-vertype="tips" lay-verify="required" required="">
                        </div>
                    </div>
                    <div class="layui-inline layui-col-md4">
                        <label class="layui-form-label layui-form-required">微信IP白名单访问:</label>
                        <div class="layui-input-block">
                            <input name="wxip" placeholder="如果有多个IP的话，请使用,进行分隔，注意请使用小写逗号" value="<?=$conf['wxip']; ?>" class="layui-input" lay-vertype="tips" lay-verify="required" required="">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-filter="userInfoSubmit" lay-submit>更新</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">会员用户到期检测，监控半小时一次  <a href="javascript:copy();" id="copy" class="copy-btn layui-btn layui-btn-xs" data-clipboard-text="<?=$siteurl;?>Cron.php?act=user&key=<?=$conf['cronkey']; ?>" title="点击复制">复制</a></div>
        <div class="layui-card-header"><?=$siteurl;?>Cron.php?act=user&key=<?=$conf['cronkey']; ?></div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">订单数据自动清理，监控每天一次   <a href="javascript:copy();" id="copy" class="copy-btn layui-btn layui-btn-xs" data-clipboard-text="<?=$siteurl;?>Cron.php?act=order&key=<?=$conf['cronkey']; ?>" title="点击复制">复制</a></div>
        <div class="layui-card-header"><?=$siteurl;?>Cron.php?act=order&key=<?=$conf['cronkey']; ?></div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">自动补单计划，计划监控1-30秒(可补QQ，支付宝)，需要配合Shell脚本！
   <a href="javascript:copy();" id="copy" class="copy-btn layui-btn layui-btn-xs" data-clipboard-text="<?=$siteurl;?>Cron.php?act=Order_notify&key=<?=$conf['cronkey']; ?>" title="点击复制">复制</a></div>
        <div class="layui-card-header"><?=$siteurl;?>Cron.php?act=Order_notify&key=<?=$conf['cronkey']; ?></div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">云端通道监控 1秒，需要配合Shell脚本！
   <a href="javascript:copy();" id="copy" class="copy-btn layui-btn layui-btn-xs" data-clipboard-text="<?=$siteurl;?>Cron.php?act=wxyun&key=<?=$conf['cronkey']; ?>" title="点击复制">复制</a></div>
        <div class="layui-card-header"><?=$siteurl;?>Cron.php?act=wxyun&key=<?=$conf['cronkey']; ?></div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">微信云端店员监控 1秒，需要配合Shell脚本！
   <a href="javascript:copy();" id="copy" class="copy-btn layui-btn layui-btn-xs" data-clipboard-text="<?=$siteurl;?>Cron.php?act=wxpayyun&key=<?=$conf['cronkey']; ?>" title="点击复制">复制</a></div>
        <div class="layui-card-header"><?=$siteurl;?>Cron.php?act=wxpayyun&key=<?=$conf['cronkey']; ?></div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">正常免挂 1~10秒，需要配合Shell脚本！
   <a href="javascript:copy();" id="copy" class="copy-btn layui-btn layui-btn-xs" data-clipboard-text="<?=$siteurl;?>Cron.php?key=<?=$conf['cronkey']; ?>" title="点击复制">复制</a></div>
        <div class="layui-card-header"><?=$siteurl;?>Cron.php?key=<?=$conf['cronkey']; ?></div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">Shell脚本</div>
        <div class="split-item">
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin<br>
export PATH<br>
step=1 #设置1秒的监控速度<br>
for (( i = 0; i < 60; i=(i+step) )); do<br>
curl -sS --connect-timeout 10 -m 60 '监控的地址'<br>
endDate=`date +"%Y-%m-%d %H:%M:%S"`<br>
echo "★[$endDate] Successful"<br>
sleep $step<br>
done<br>
exit 0<br></div>
    </div>
    <div class="layui-card">
        <p><img src="/Mym/Assets/Img/cron_png.png"  /></p>
    </div>
</div>
            <!-- //表单结束 -->


<!-- js部分 -->
<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/clipboard.js/1.4.0/clipboard.min.js"></script>
<script>
    layui.use(['layer', 'form', 'element', 'admin'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var element = layui.element;
        var admin = layui.admin;

        /* 监听表单提交 */
        form.on('submit(userInfoSubmit)', function (data) {
            var loadIndex = layer.load(2);
            $.post('../Ajax.php?act=Set', data.field, function (res) {  // 实际项目这里url可以是mData?'user/update':'user/add'
            layer.close(loadIndex);
            if (res.code === 0) {
                layer.msg('设置保存成功！', {icon: 1});
            } else {
                layer.msg(res.msg, {icon: 2});
            }
                
            }, 'json');
            return false;
        });
        $("#copy").click(function () {
            copy();
        });
    });
        function copy(){
            var clipboard = new Clipboard('.copy-btn');//btn btn-outline-success waves-effect waves-light
            clipboard.on('success', function (e) {
                layer.msg('复制成功！', {icon: 1});
            });
            clipboard.on('error', function (e) {
                layer.msg('复制失败，请长按链接后手动复制', {icon: 2});
            });
        }
</script>
</body>
</html>