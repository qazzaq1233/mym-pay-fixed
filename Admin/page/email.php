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
</head>
<body>
<!-- 正文开始 -->
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">邮箱与短信配置</div>
        <div class="layui-card-body">
            <!-- 表单开始 -->
            <form class="layui-form" lay-filter="formBasForm">
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">发信模式:</label>
                    <div class="layui-input-inline">
                        <select name="mail_cloud" default="<?=$conf['mail_cloud']?>">
                            <?php if($conf['mail_cloud']==0){
                                echo '<option value="0">SMTP发信</option><option value="1">搜狐Sendcloud</option><option value="2">阿里云邮件推送</option>';
                            }elseif($conf['mail_cloud']==1){
                                echo '<option value="1">搜狐Sendcloud</option><option value="2">阿里云邮件推送</option><option value="0">SMTP发信</option>';
                            }else{
                                echo '<option value="2">阿里云邮件推送</option><option value="0">SMTP发信</option><option value="1">搜狐Sendcloud</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php if($conf['mail_cloud']==0){?>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">SMTP服务器:</label>
                        <div class="layui-input-block">
                            <input name="mail_smtp" value="<?=$conf['mail_smtp']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">SMTP端口:</label>
                        <div class="layui-input-block">
                            <input name="mail_port" value="<?=$conf['mail_port']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">邮箱账号:</label>
                        <div class="layui-input-block">
                            <input name="mail_name" value="<?=$conf['mail_name']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">邮箱密码:</label>
                        <div class="layui-input-block">
                            <input name="mail_pwd" value="<?=$conf['mail_pwd']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                <?php }else{ ?>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">API_USER:</label>
                        <div class="layui-input-block">
                            <input name="mail_apiuser" value="<?=$conf['mail_apiuser']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">API_KEY:</label>
                        <div class="layui-input-block">
                            <input name="mail_apikey" value="<?=$conf['mail_apikey']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">发信邮箱:</label>
                        <div class="layui-input-block">
                            <input name="mail_name2" value="<?=$conf['mail_name2']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                <?php }?>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">收信邮箱:</label>
                        <div class="layui-input-block">
                            <input name="mail_recv" id="mail_recv" value="<?=$conf['mail_recv']; ?>" class="layui-input"
                               lay-verType="tips" >
                            <?php if($conf['mail_name']){?>[<a id="emali_null">给 <?=$conf['mail_recv']?$conf['mail_recv']:$conf['mail_name']?> 发一封测试邮件</a>]<?php }?>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">支付通知模板:</label>
                        <div class="layui-input-block">
                            <textarea class="layui-textarea" name="paymali" rows="5" placeholder="支付通知模板"><?=$conf['paymali']?></textarea>
                        </div>
                    </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button id="dialogBtnLoad" class="layui-btn" lay-filter="formBasSubmit" lay-submit>&emsp;提交&emsp;</button>
                    </div>
                </div>
            </form>
            <!-- //表单结束 -->
        </div>
    </div>
</div>

<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script src="../../Mym/Assets/Login/static/js/jquery-3.2.1.min.js"></script>
<script>
$("select[name='mail_cloud']").change(function(){
	if($(this).val() == 0){
		$("#frame_set1").show();
		$("#frame_set2").hide();
	}else{
		$("#frame_set1").hide();
		$("#frame_set2").show();
	}
});
</script>
<script>
    layui.use(['layer', 'form', 'laydate'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var laydate = layui.laydate;

        /* 渲染laydate */
        laydate.render({
            elem: '#formBasDateSel',
            trigger: 'click',
            range: true
        });
        
        /* 监听表单提交 */
        form.on('submit(formBasSubmit)', function (data) {
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
        
        $("#emali_null").click(function(){
            var emali = $("#mail_recv").val();
            var loadIndex = layer.load(2);
            $.post('../Ajax.php?act=mailtest', emali, function (res) {  // 实际项目这里url可以是mData?'user/update':'user/add'
            layer.close(loadIndex);
            if (res.code === 200) {
                layer.msg(res.msg, {icon: 1});
            } else {
                layer.msg(res.msg, {icon: 2});
            }
                
            }, 'json');
            return false;
        });
    });
</script>
</body>
</html>