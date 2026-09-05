<?php
include("../Mym/Common.php");
if($islogin_user==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
$conf['qq'] = $conf['qq']?:485570653;
$mym_iframe_mode = isset($_GET['iframe']) && $_GET['iframe'] == '1';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title><?php echo $conf['sitename'] ?> - <?=$title?></title>
        <meta content="Admin Dashboard" name="description" />
        <meta content="Mannatthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <link rel="shortcut icon" href="/favicon.ico">

        <link href="./Assets/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="./Assets/assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="./Assets/assets/css/style.css" rel="stylesheet" type="text/css">
        <link href="./Assets/assets/css/mym-user-modern.css" rel="stylesheet" type="text/css">

  <script src="//cdn.staticfile.org/modernizr/2.8.3/modernizr.min.js"></script>
  <script src="//cdn.staticfile.org/jquery/2.1.4/jquery.min.js"></script>
  <script src="//cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
 
  <script src="../Mym/Assets/Layer/layer.js"></script>

    <script src="//cdn.staticfile.org/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>

  
    </head>


    <body class="fixed-left<?php echo $mym_iframe_mode ? ' mym-iframe-mode' : ''; ?>">
<?php if($mym_iframe_mode){ ?>
        <div class="mym-iframe-content">
<?php }else{ ?>

        <!-- Loader -->
        <div id="preloader"><div id="status"><div class="spinner"></div></div></div>

        <!-- Begin page -->
        <div id="wrapper">

            <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
                    <i class="ion-close"></i>
                </button>

                <!-- LOGO -->
                <div class="topbar-left">
                    <div class="text-center bg-logo">
                        <a href="./" class="logo"><span class="mym-logo-mark"><i class="mdi mdi-wallet"></i></span><span class="mym-logo-text"><?php echo $conf['sitename'] ?></span></a>
                        <!-- <a href="index.html" class="logo"><img src="assets/images/logo.png" height="24" alt="logo"></a> -->
                    </div>
                </div>

                <div class="sidebar-inner slimscrollleft">

                    <div id="sidebar-menu">
                        <ul>
                            <li class="menu-title">商户中心</li>
                            <li>
                                <a href="./" class="waves-effect"><i class="mdi mdi-view-dashboard-outline"></i><span> 商户中心 </span></a>
                            </li>
                            <li>
                                <a href="./Order.php" class="waves-effect"><i class="mdi mdi-clipboard-text-outline"></i><span> 订单管理 </span></a>
                            </li>

                            <li class="menu-title">通道管理</li>
                            <li>
                                <a href="./Free_Qrlist.php" class="waves-effect"><i class="mdi mdi-credit-card-multiple-outline"></i><span> 通道列表 </span></a>
                            </li>
                            <li>
                                <a href="./Free_dmf.php" class="waves-effect"><i class="mdi mdi-qrcode-scan"></i><span> 当面付通道 </span></a>
                            </li>

                            <li class="menu-title">财务与接口</li>
                            <li>
                                <a href="./Pay_Vip.php" class="waves-effect"><i class="mdi mdi-cash-plus"></i><span> 立即充值 </span></a>
                            </li>
                            <li>
                                <a href="./Set.php" class="waves-effect"><i class="mdi mdi-tune"></i><span> 支付设置 </span></a>
                            </li>
                            <li>
                                <a href="./userinfo.php" class="waves-effect"><i class="mdi mdi-api"></i><span> API / 资料 </span></a>
                            </li>
                            <li>
                                <a href="./taocan.php" class="waves-effect"><i class="mdi mdi-package-variant-closed"></i><span> 套餐购买 </span></a>
                            </li>
                            <li>
                                <a href="./plug.php" class="waves-effect"><i class="mdi mdi-puzzle-outline"></i><span> 插件市场 </span></a>
                            </li>

                            <li class="menu-title">账号服务</li>
                            <?php if($conf['qq_qun']) echo '<li><a href="'.$conf['qq_qun'].'" class="waves-effect"><i class="mdi mdi-account-group-outline"></i><span> 加入QQ群 </span></a></li>';?>
                            <li>
                                <a href="Ajax2.php?act=logout" class="waves-effect"><i class="mdi mdi-logout"></i><span> 退出登录 </span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                </div> <!-- end sidebarinner -->
            </div>
            <!-- Left Sidebar End -->

            <!-- Start right Content here -->

            <div class="content-page">
                <!-- Start content -->
                <div class="content">

                    <!-- Top Bar Start -->
                    <div class="topbar">

                        <nav class="navbar-custom">

                            <ul class="list-inline float-right mb-0 mym-top-actions">
                                <li class="list-inline-item hide-phone mym-user-balance">
                                    <span>余额</span><strong>￥<?php echo $userrow['money']; ?></strong>
                                </li>
                                <li class="list-inline-item dropdown notification-list">
                                    <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button"
                                       aria-haspopup="false" aria-expanded="false">
                                        <img src="<?php echo ($userrow['qq'])?'//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$userrow['qq'].'&src_uin='.$userrow['qq'].'&fid='.$userrow['qq'].'&spec=100&url_enc=0&referer=bu_interface&term_type=PC':'../Mym/Assets/img/user.png'?>" alt="user" class="rounded-circle">
                                        <span class="hide-phone mym-top-username"><?php echo $userrow['user']?$userrow['user']:'PID '.$userrow['pid']; ?></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                        <div class="dropdown-item noti-title">
                                            <h5>商户账号</h5>
                                        </div>
                                        <a class="dropdown-item" href="./userinfo.php"><i class="mdi mdi-wallet m-r-5 text-muted"></i> API资料</a>
                                        <a class="dropdown-item" href="./userinfo.php"><i class="mdi mdi-lock-open-outline m-r-5 text-muted"></i> 安全设置</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="Ajax2.php?act=logout"><i class="mdi mdi-logout m-r-5 text-muted"></i> 退出登录</a>
                                    </div>
                                </li>
                            </ul>

                            <ul class="list-inline menu-left mb-0">
                                <li class="float-left">
                                    <button class="button-menu-mobile open-left waves-light waves-effect">
                                        <i class="mdi mdi-menu"></i>
                                    </button>
                                </li>
                                <li class="hide-phone mym-top-title">
                                    <span><?php echo $title; ?></span>
                                    <small>安全 · 高效 · 商户控制台</small>
                                </li>
                            </ul>

                            <div class="clearfix"></div>
                        </nav>
                    </div>
<!-- Top Bar End -->
<?php } ?>


