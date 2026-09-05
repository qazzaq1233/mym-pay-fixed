<?php
include("../../Mym/Common.php");
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
$mod=isset($_GET['mod'])?$_GET['mod']:null;
if($mod=='Cleancache'){
    $CACHE->clear();
    if(function_exists("opcache_reset"))@opcache_reset();
    showmsg('清理系统设置缓存成功！',1);
}elseif($mod=='Cleanorder'){
    $DB->exec("DELETE FROM `pay_order` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
    $DB->exec("OPTIMIZE TABLE `pay_order`");
    showmsg('删除30天前订单记录成功！',1);
}elseif($mod=='Cleanordera'){
    $DB->exec("DELETE FROM `pay_notify` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
    $DB->exec("OPTIMIZE TABLE `pay_notify`");
    showmsg('删除30天前回调记录成功！',1);
}elseif($mod=='Cleanorderi'){
    $days = daddslashes($_GET['days']);
    if(!$days){     
        showmsg('请确保每项不能为空',3);
    }else{
        $DB->exec("DELETE FROM `pay_order` WHERE date<'".date("Y-m-d",strtotime("-{$days} days"))."'");
        $DB->exec("OPTIMIZE TABLE `pay_order`");
        showmsg('删除订单记录成功！',1);
    }
}elseif($mod=='user'){
    $DB->exec("DELETE FROM `pay_user` WHERE email_status=0");
    $DB->exec("OPTIMIZE TABLE `pay_user`");
    showmsg('删除无效用户成功！',1);
}elseif($mod=='Cleanrecordi' && $_POST['do']=='submit'){
    $days = intval($_POST['days']);
    if(!$days){
        showmsg('请确保每项不能为空',3); 
    }else{
        $DB->exec("DELETE FROM `pay_record` WHERE date<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."'");
        $DB->exec("OPTIMIZE TABLE `pay_record`");
        showmsg('删除资金明细成功！',1);
    }
}else{
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
            <div class="layui-card-body">
                <div class="layui-form-item layui-row">
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <a href="./clean.php?mod=user" onclick="return confirm('你确实要清理删除无效用户吗？');" class="layui-btn">清理删除无效用户</a>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <a href="./clean.php?mod=Cleanorder" onclick="return confirm('你确实要清理删除无效用户吗？');" class="layui-btn">删除30天前订单记录</a>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <a href="./clean.php?mod=Cleanordera" onclick="return confirm('你确实要清理删除无效用户吗？');" class="layui-btn">删除30天前回调记录</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-card">
        <label class="layui-form-label layui-form-required">天前的订单记录:</label>
        <form action="./clean.php?mod=Cleanorderi" method="get" role="form">
        <div class="layui-input-block">
            <input name="mod" type="hidden" value="Cleanorderi"/>
            <input name="days" value="30" class="layui-input" lay-vertype="tips" lay-verify="required" required="">
            <button class="layui-btn" lay-submit>立即删除</button>
        </div>
        </form>
    </div>
</div>
</body>
</html>
<?php
} ?>