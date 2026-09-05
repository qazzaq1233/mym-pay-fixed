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
        <div class="layui-card-header">网站信息设置</div>
        <div class="layui-card-body">
            <form class="layui-form" lay-filter="site">
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">网站名称:</label>
                    <div class="layui-input-block">
                        <input name="sitename" value="<?=$conf['sitename']; ?>" class="layui-input"
                               lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">首页标题:</label>
                    <div class="layui-input-block">
                        <input name="title" value="<?=$conf['title']; ?>" class="layui-input"
                               lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">关键字:</label>
                    <div class="layui-input-block">
                        <input name="keywords" value="<?=$conf['keywords']; ?>" class="layui-input"
                               lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">网站描述:</label>
                    <div class="layui-input-block">
                        <input name="description" value="<?=$conf['description']; ?>" class="layui-input"
                               lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">客服ＱＱ:</label>
                    <div class="layui-input-block">
                        <input name="qq" value="<?=$conf['qq']; ?>" class="layui-input"
                               lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">加群链接:</label>
                    <div class="layui-input-block">
                        <input name="qq_qun" value="<?=$conf['qq_qun']; ?>" class="layui-input"
                                lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">极限验证码ID:</label>
                    <div class="layui-input-block">
                        <input name="captcha_id" value="<?=$conf['captcha_id']; ?>" class="layui-input"
                               lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">极限验证码密钥:</label>
                    <div class="layui-input-block">
                        <input name="captcha_key" value="<?=$conf['captcha_key']; ?>" class="layui-input"
                               lay-verType="tips" lay-verify="required" required/>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">用户中心弹出公告:</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="modal" rows="5" placeholder="不填写则不显示弹出公告"><?=$conf['modal']?></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">首页底部排版:</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="footer" rows="3" placeholder="可填写备案号等"><?=$conf['footer']?></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button id="dialogBtnLoad" class="layui-btn" lay-filter="formBasSubmit" lay-submit>&emsp;提交&emsp;</button>
                    </div>
                </div>
            </form>
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