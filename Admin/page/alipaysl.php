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
            <form class="layui-form" id="formBasForm" lay-filter="formBasForm">
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">开放注册:</label>
                    <div class="layui-input-inline">
                        <select name="alipay_kg">
                            <?php if($conf['alipay_kg']==1){
                                echo '<option value="1">开启</option><option value="0">关闭</option>';
                            }else{
                                echo '<option value="0">关闭</option><option value="1">开启</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">应用APPID：</label>
                    <div class="layui-input-block">
                        <input name="alipay_appid" value="<?=$conf['alipay_appid']; ?>" class="layui-input"
                               lay-verType="tips" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">支付宝公钥：</label>
                    <div class="layui-input-block">
                        <textarea name="alipay_appg" rows="2" class="layui-textarea" placeholder=""><?=$conf['alipay_appg']; ?></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">应用私钥：</label>
                    <div class="layui-input-block">
                        <textarea name="alipay_apps" rows="2" class="layui-textarea" placeholder=""><?=$conf['alipay_apps']; ?></textarea>
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
    });
</script>
</body>
</html>