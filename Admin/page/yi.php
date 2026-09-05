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
        <div class="layui-card-header">网站运营配置</div>
        <div class="layui-card-body">
            <!-- 表单开始 -->
            <form class="layui-form" lay-filter="formBasForm">
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">静态资源CDN:</label>
                    <div class="layui-input-inline">
                        <select name="cdnpublic" default="<?=$conf['cdnpublic']?>">
                            <?php if($conf['cdnpublic']==0){
                                echo '<option value="0">七牛云CDN</option>
                            <option value="1">360CDN</option>
                            <option value="2">BootCDN</option>
                            <option value="4">今日头条CDN</option>
                            <option value="5">MYM官方</option>';
                            }else if($conf['cdnpublic']==1){
                                echo '<option value="1">360CDN</option>
                            <option value="0">七牛云CDN</option>
                            <option value="2">BootCDN</option>
                            <option value="4">今日头条CDN</option>
                            <option value="5">MYM官方</option>';
                            }else if($conf['cdnpublic']==2){
                                echo '<option value="2">BootCDN</option>
                            <option value="1">360CDN</option>
                            <option value="0">七牛云CDN</option>
                            <option value="4">今日头条CDN</option>
                            <option value="5">MYM官方</option>';
                            }else if($conf['cdnpublic']==4){
                                echo '<option value="4">今日头条CDN</option>
                            <option value="2">BootCDN</option>
                            <option value="1">360CDN</option>
                            <option value="0">七牛云CDN</option>
                            <option value="5">MYM官方</option>';
                            }else if($conf['cdnpublic']==5){
                                echo '<option value="5">MYM官方</option>
                            <option value="4">今日头条CDN</option>
                            <option value="2">BootCDN</option>
                            <option value="1">360CDN</option>
                            <option value="0">七牛云CDN</option>
                            ';
                            }?>
                        </select>
                    </div>
                    <label class="layui-form-label layui-form-required">二维码生成API:</label>
                    <div class="layui-input-inline">
                        <select name="qrpublic" default="<?=$conf['qrpublic']?>">
                            <?php if($conf['qrpublic']==0){
                                echo '<option value="0">MYM官方API</option>
                            <option value="1">QQ接口</option>
                            <option value="5">QQ接口2</option>
                            <option value="2">qrserver</option>
                            <option value="3">晶晶</option>
                            <option value="4">快手接口</option>';
                            }else if($conf['qrpublic']==1){
                                echo '<option value="1">QQ接口</option>
                                <option value="5">QQ接口2</option>
                            <option value="0">MYM官方</option>
                            <option value="2">qrserver</option>
                            <option value="3">晶晶</option>
                            <option value="4">快手接口</option>';
                            }else if($conf['qrpublic']==2){
                                echo '<option value="2">qrserver</option>
                            <option value="1">QQ接口</option>
                            <option value="5">QQ接口2</option>
                            <option value="0">MYM官方</option>
                            <option value="4">快手接口</option>
                            <option value="3">晶晶</option>';
                            }else if($conf['qrpublic']==3){
                                echo '<option value="3">晶晶</option>
                            <option value="2">qrserver</option>
                            <option value="0">MYM官方</option>
                            <option value="1">QQ接口</option>
                            <option value="5">QQ接口2</option>
                            <option value="4">快手接口</option>
                            ';
                            }else if($conf['qrpublic']==4){
                                echo '<option value="4">快手接口</option>
                            <option value="3">晶晶</option>
                            <option value="2">qrserver</option>
                            <option value="1">QQ接口</option>
                            <option value="0">MYM官方</option>
                            <option value="5">QQ接口2</option>';
                            }else if($conf['qrpublic']==5){
                                echo '<option value="5">QQ接口2</option>
                            <option value="3">晶晶</option>
                            <option value="2">qrserver</option>
                            <option value="1">QQ接口</option>
                            <option value="0">MYM官方</option>';
                            }?>
                        </select>
                    </div>
                    <label class="layui-form-label layui-form-required">USDT汇率:</label>
                    <div class="layui-input-inline">
                        <select name="flpublic">
                            <?php if($conf['flpublic']==1){
                                echo '<option value="1">欧易交易汇率API</option><option value="0">百度美元汇率API</option>';
                            }else{
                                echo '<option value="0">百度美元汇率API</option><option value="1">欧易交易所汇率API</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">开放注册:</label>
                    <div class="layui-input-inline">
                        <select name="reg_open">
                            <?php if($conf['reg_open']==1){
                                echo '<option value="1">开启</option><option value="0">关闭</option>';
                            }else{
                                echo '<option value="0">关闭</option><option value="1">开启</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <label class="layui-form-label layui-form-required">邮箱验证:</label>
                    <div class="layui-input-inline">
                        <select name="reg_email">
                            <?php if($conf['reg_email']==1){
                                echo '<option value="1">关闭邮箱验证</option><option value="0">开启邮箱验证</option>';
                            }else{
                                echo '<option value="0">开启邮箱验证</option><option value="1">关闭邮箱验证</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <label class="layui-form-label layui-form-required">强制HTTPS:</label>
                    <div class="layui-input-inline">
                        <select name="http">
                            <?php if($conf['http']==1){
                                echo '<option value="1">开启强制</option><option value="0">关闭强制</option>';
                            }else{
                                echo '<option value="0">关闭强制</option><option value="1">开启强制</option>';
                            }
                            ?>
                        </select>
                        <pre><font color="green">解决CDN设置HTTPS问题</font></pre>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">支付宝模式:</label>
                    <div class="layui-input-inline">
                        <select name="ail_cloud">
                            <?php if($conf['ail_cloud']==0){
                                echo '<option value="0">使用本地模式</option><option value="1">云端互联模式(未开放使用，后果自负)</option><option value="2">使用Api模式</option>';
                            }elseif($conf['ail_cloud']==1){
                                echo '<option value="1">云端互联模式(未开放使用，后果自负)</option><option value="2">使用Api模式</option><option value="0">使用本地模式</option>';
                            }else{
                                echo '<option value="2">使用Api模式</option><option value="0">使用本地模式</option><option value="1">云端互联模式(未开放使用，后果自负)</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <label class="layui-form-label layui-form-required">QQ模式:</label>
                    <div class="layui-input-inline">
                        <select name="qq_cloud">
                            <?php if($conf['qq_cloud']==0){
                                echo '<option value="0">使用本地模式</option><option value="1">云端互联模式(未开放使用，后果自负)</option><option value="2">使用Api模式</option>';
                            }elseif($conf['qq_cloud']==1){
                                echo '<option value="1">云端互联模式(未开放使用，后果自负)</option><option value="2">使用Api模式</option><option value="0">使用本地模式</option>';
                            }else{
                                echo '<option value="2">使用Api模式</option><option value="0">使用本地模式</option><option value="1">云端互联模式(未开放使用，后果自负)</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div id="frame_ali" style="<?=$conf['ail_cloud']<2?'display:none;':null; ?>">
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">支付宝API:</label>
                        <div class="layui-input-block">
                            <input name="ail_cloud_api" value="<?=$conf['ail_cloud_api']; ?>" class="layui-input"
                               lay-verType="tips" >
                               <font color="green">例如 http://127.0.0.1/</font>
                        </div>
                    </div>
                </div>
                <div id="frame_ali" style="<?=$conf['qq_cloud']<2?'display:none;':null; ?>">
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">QQAPI:</label>
                        <div class="layui-input-block">
                            <input name="qq_cloud_api" value="<?=$conf['qq_cloud_api']; ?>" class="layui-input"
                               lay-verType="tips" >
                               <font color="green">例如 http://127.0.0.1/</font>
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">注册赠送额度:</label>
                    <div class="layui-input-block">
                        <input name="reg_money" value="<?=$conf['reg_money']; ?>" class="layui-input"
                               lay-verType="tips" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">注册赠送配额:</label>
                    <div class="layui-input-block">
                        <input name="reg_type" value="<?=$conf['reg_type']; ?>" class="layui-input"
                               lay-verType="tips" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">购买配额价格/个:</label>
                    <div class="layui-input-block">
                        <input name="ed_type" value="<?=$conf['ed_type']; ?>" class="layui-input"
                               lay-verType="tips" >
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">充值比例1元=?额度:</label>
                    <div class="layui-input-block">
                        <input name="ed_money" value="<?=$conf['ed_money']; ?>" class="layui-input"
                               lay-verType="tips" >
                            <pre><font color="green">1元=30  10=300  那么费率就差不多是百分之3 别人跑1000量,你赚30</font></pre>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">注册付费:</label>
                    <div class="layui-input-inline">
                        <select name="reg_pay">
                            <?php if($conf['reg_pay']==1){
                                echo '<option value="1">开启</option><option value="0">关闭</option>';
                            }else{
                                echo '<option value="0">关闭</option><option value="1">开启</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div id="setform2" style="<?=$conf['reg_pay']==0?'display:none;':null; ?>">
                    <div class="layui-form-item">
                        <label class="layui-form-label layui-form-required">注册付费金额:</label>
                        <div class="layui-input-block">
                            <input name="reg_pay_price" value="<?=$conf['reg_pay_price']; ?>" class="layui-input"
                               lay-verType="tips" >
                        </div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label layui-form-required">收款商户PID:</label>
                    <div class="layui-input-block">
                        <input name="zero_pid" value="<?=$conf['zero_pid']; ?>" class="layui-input"
                               lay-verType="tips" >
                        <font color="green">收款商户ID跟注册、套餐、配额、在线测试页面商户ID同步</font>
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