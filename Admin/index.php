<?php
include("../Mym/Common.php");
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="//q4.qlogo.cn/headimg_dl?dst_uin=485570653&spec=640" rel="icon">
    <title><?=$conf['sitename']?> - 后台管理系统</title>
    <meta name="keywords" content="<?php echo $conf['keywords']?>">
    <meta name="description" content="<?php echo $conf['description']?>">
    <link rel="stylesheet" href="assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="assets/module/admin.css?v=318"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <!-- 头部 -->
    <div class="layui-header">
        <div class="layui-logo">
            <img src="assets/images/logo.png"/>
            <cite>&nbsp;Mym Pay</cite>
        </div>
        <ul class="layui-nav layui-layout-left">
            <li class="layui-nav-item" lay-unselect>
                <a ew-event="flexible" title="侧边伸缩"><i class="layui-icon layui-icon-shrink-right"></i></a>
            </li>
            <li class="layui-nav-item" lay-unselect>
                <a ew-event="refresh" title="刷新"><i class="layui-icon layui-icon-refresh-3"></i></a>
            </li>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item" lay-unselect>
                <a ew-event="message" title="消息">
                    <i class="layui-icon layui-icon-notice"></i>
                    <span class="layui-badge-dot"></span>
                </a>
            </li>
            <li class="layui-nav-item" lay-unselect>
                <a ew-event="note" title="便签"><i class="layui-icon layui-icon-note"></i></a>
            </li>
            <li class="layui-nav-item layui-hide-xs" lay-unselect>
                <a ew-event="fullScreen" title="全屏"><i class="layui-icon layui-icon-screen-full"></i></a>
            </li>
            <li class="layui-nav-item layui-hide-xs" lay-unselect>
                <a ew-event="lockScreen" title="锁屏"><i class="layui-icon layui-icon-password"></i></a>
            </li>
            <li class="layui-nav-item" lay-unselect>
                <a>
                    <img src="assets/images/head.jpg" class="layui-nav-img">
                    <cite>管理员</cite>
                </a>
                <dl class="layui-nav-child">
                    <dd lay-unselect><a ew-href="page/user-info.php">个人中心</a></dd>
                    <dd lay-unselect><a ew-event="psw">修改密码</a></dd>
                    <hr>
                    <dd lay-unselect><a ew-event="logout" data-url="Login.php?logout">退出</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item" lay-unselect>
                <a ew-event="theme" title="主题"><i class="layui-icon layui-icon-more-vertical"></i></a>
            </li>
        </ul>
    </div>

    <!-- 侧边栏 -->
    <div class="layui-side">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree arrow2" lay-filter="admin-side-nav" lay-shrink="_all">
                <li class="layui-nav-item">
                    <a lay-href="page/workplace.php"><i class="layui-icon layui-icon-home"></i>&emsp;<cite>控制台</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a lay-href="page/Order.php"><i class="layui-icon layui-icon-cart"></i>&emsp;<cite>订单记录</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a lay-href="page/Qrlist.php"><i class="layui-icon layui-icon-app"></i>&emsp;<cite>收款账号</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a lay-href="page/User.php"><i class="layui-icon layui-icon-user"></i>&emsp;<cite>用户管理</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a lay-href="page/Wechat_Trumpet.php"><i class="layui-icon layui-icon-login-wechat"></i>&emsp;<cite>微信店员</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a lay-href="page/cron.php"><i class="layui-icon layui-icon-read"></i>&emsp;<cite>计划任务</cite></a>
                </li>
                <li class="layui-nav-item">
                    <a><i class="layui-icon layui-icon-set"></i>&emsp;<cite>系统配置</cite></a>
                    <dl class="layui-nav-child">
                        <dd><a lay-href="page/site.php">网站信息配置</a></dd>
                        <dd><a lay-href="page/yi.php">网站运营配置</a></dd>
                        <dd><a lay-href="page/Notice.php">网站公告配置</a></dd>
                        <dd><a lay-href="page/template.php">首页模板配置</a></dd>
                        <dd><a lay-href="page/email.php">邮箱与短信配置</a></dd>
                        <dd><a lay-href="page/logo.php">网站Logo配置</a></dd>
                        <!--<dd><a lay-href="page/alipaysl.php">支付宝服务商</a></dd>-->
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a><i class="layui-icon layui-icon-cols"></i>&emsp;<cite>更多配置</cite></a>
                    <dl class="layui-nav-child">
                        <dd><a lay-href="page/yund.php">云端管理</a></dd>
                        <dd><a lay-href="page/pay_channel.php">通道展示配置</a></dd>
                        <dd><a lay-href="page/dll.php">插件管理</a></dd>
                        <dd><a lay-href="page/taocan.php">额度套餐</a></dd>
                        
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a><i class="layui-icon layui-icon-more"></i>&emsp;<cite>其他数据</cite></a>
                    <dl class="layui-nav-child">
                        <dd><a lay-href="page/Notify.php">回调日记</a></dd>
                        <dd><a lay-href="page/log.php">操作日志</a></dd>
                        <dd><a lay-href="page/clean.php">数据清理</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a lay-href="page/updata.php"><i class="layui-icon layui-icon-release"></i>&emsp;<cite>系统更新</cite></a>
                </li>
            </ul>
        </div>
    </div>

    <!-- 主体部分 -->
    <div class="layui-body"></div>
    <!-- 底部 -->
    <div class="layui-footer layui-text">
        copyright © 2021-2024 <a href="http://g.9o3.cn" target="_blank">http://g.9o3.cn</a> all rights reserved.
        <span class="pull-right">Version <?php echo VERSIONS;?></span>
    </div>
</div>

<!-- 加载动画 -->
<div class="page-loading">
    <div class="ball-loader">
        <span></span><span></span><span></span><span></span>
    </div>
</div>

<!-- js部分 -->
<script type="text/javascript" src="./assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="./assets/js/common.js?v=318"></script>
<script>
    
    layui.use(['index', 'admin'], function() {
        var $ = layui.jquery;
        var index = layui.index;
        var admin = layui.admin;
        var setter = admin.setter;
        if (setter.cacheTab == false) {
            admin.putSetting("pageTabs", true);
            location.reload();
        } else {
            admin.putSetting("pageTabs", true);
        }
        index.loadHome({
            menuPath: 'page/workplace.php',
            menuName: '<i class="layui-icon layui-icon-home"></i>'
        });
    });
</script>
</body>
</html>