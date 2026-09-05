<?php
include("../../Mym/Common.php");
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
$count1=$DB->query("SELECT count(*) from pay_order")->fetchColumn();
$count2=$DB->query("SELECT count(*) from pay_user")->fetchColumn();
$count3=$DB->query("SELECT count(*) from pay_qrlist")->fetchColumn();
$count4=$DB->query("SELECT sum(money) from pay_order")->fetchColumn();
$count5=$DB->query("SELECT sum(money) from pay_order where status='1'")->fetchColumn();
//$count6=$DB->query("SELECT sum(money) from pay_order where status='0'")->fetchColumn();

$date1 = date("Y-m-d");
$date2 = date("Y-m-d",strtotime("-1 day"));
if($count4==''){
    $count4=0;
}
if($count5==''){
    $count5=0;
}
$rs=$DB->query("SELECT * from pay_order where status=1 and date='$date1'");
$order_today=array('alipay'=>0,'qqpay'=>0,'wxpay'=>0,'usdt'=>0,'all'=>0);
while($row = $rs->fetch())
{
	$order_today[$row['type']]+=$row['money'];
	$order_today[$row['type']]=round($order_today[$row['type']],2);
}
$order_today['all']=$order_today['alipay']+$order_today['usdt']+$order_today['qqpay']+$order_today['wxpay'];

$rs=$DB->query("SELECT * from pay_order where status=1 and date='$date2'");
$order_lastday=array('alipay'=>0,'qqpay'=>0,'wxpay'=>0,'usdt'=>0,'all'=>0);
while($row = $rs->fetch())
{
	$order_lastday[$row['type']]+=$row['money'];
	$order_lastday[$row['type']]=round($order_lastday[$row['type']],2);
}
$order_lastday['all']=$order_lastday['alipay']+$order_lastday['usdt']+$order_lastday['qqpay']+$order_lastday['wxpay'];

$data['order_today']=$order_today;
$data['order_lastday']=$order_lastday;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>工作台</title>
    <link rel="stylesheet" href="../assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="../assets/module/admin.css?v=318"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /** 应用快捷块样式 */
        .console-app-group {
            padding: 16px;
            border-radius: 4px;
            text-align: center;
            background-color: #fff;
            cursor: pointer;
            display: block;
        }

        .console-app-group .console-app-icon {
            width: 32px;
            height: 32px;
            line-height: 32px;
            margin-bottom: 6px;
            display: inline-block;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            font-size: 32px;
            color: #69c0ff;
        }

        .console-app-group:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, .08);
        }

        /** //应用快捷块样式 */

        /** 小组成员 */
        .console-user-group {
            position: relative;
            padding: 10px 0 10px 60px;
        }

        .console-user-group .console-user-group-head {
            width: 32px;
            height: 32px;
            position: absolute;
            top: 50%;
            left: 12px;
            margin-top: -16px;
            border-radius: 50%;
        }

        .console-user-group .layui-badge {
            position: absolute;
            top: 50%;
            right: 8px;
            margin-top: -10px;
        }

        .console-user-group .console-user-group-name {
            line-height: 1.2;
        }

        .console-user-group .console-user-group-desc {
            color: #8c8c8c;
            line-height: 1;
            font-size: 12px;
            margin-top: 5px;
        }

        /** 卡片轮播图样式 */
        .admin-carousel .layui-carousel-ind {
            position: absolute;
            top: -41px;
            text-align: right;
        }

        .admin-carousel .layui-carousel-ind ul {
            background: 0 0;
        }

        .admin-carousel .layui-carousel-ind li {
            background-color: #e2e2e2;
        }

        .admin-carousel .layui-carousel-ind li.layui-this {
            background-color: #999;
        }

        /** 广告位轮播图 */
        .admin-news .layui-carousel-ind {
            height: 45px;
        }

        .admin-news a {
            display: block;
            line-height: 70px;
            text-align: center;
        }

        /** 最新动态时间线 */
        .layui-timeline-dynamic .layui-timeline-item {
            padding-bottom: 0;
        }

        .layui-timeline-dynamic .layui-timeline-item:before {
            top: 16px;
        }

        .layui-timeline-dynamic .layui-timeline-axis {
            width: 9px;
            height: 9px;
            left: 1px;
            top: 7px;
            background-color: #cbd0db;
        }

        .layui-timeline-dynamic .layui-timeline-axis.active {
            background-color: #0c64eb;
            box-shadow: 0 0 0 2px rgba(12, 100, 235, .3);
        }

        .dynamic-card-body {
            box-sizing: border-box;
            overflow: hidden;
        }

        .dynamic-card-body:hover {
            overflow-y: auto;
            padding-right: 9px;
        }

        /** 优先级徽章 */
        .layui-badge-priority {
            border-radius: 50%;
            width: 20px;
            height: 20px;
            padding: 0;
            line-height: 18px;
            border-width: 2px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<!-- 正文开始 -->
<div class="layui-fluid ew-console-wrapper">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    订单总数<span class="layui-badge layui-badge-green pull-right">总</span>
                </div>
                <div class="layui-card-body">
                    <p class="lay-big-font"><?=$count1?></p>
                </div>
            </div>
        </div>
        <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    完成金额<span class="layui-badge layui-badge-green pull-right">总</span>
                </div>
                <div class="layui-card-body">
                    <p class="lay-big-font"><span style="font-size: 26px;line-height: 1;">¥ </span><?=$count5?></p>
                </div>
            </div>
        </div>
        <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    码子总数<span class="layui-badge layui-badge-green pull-right">总</span>
                </div>
                <div class="layui-card-body">
                    <p class="lay-big-font"><?=$count3;?></p>
                </div>
            </div>
        </div>
        <div class="layui-col-xs12 layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    商户总数
                    <span class="icon-text pull-right" lay-tips="指标说明" lay-direction="4" lay-offset="5px,5px">
                        <i class="layui-icon layui-icon-tips"></i>
                    </span>
                </div>
                <div class="layui-card-body">
                    <p class="lay-big-font"><?=$count2?> <span style="font-size: 24px;line-height: 1;">位</span></p>
                </div>
            </div>
        </div>
    </div>
    <!-- 快捷方式 -->
    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm6" style="padding-bottom: 0;">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group" ew-href="page/User.php" ew-title="用户管理">
                        <i class="console-app-icon layui-icon layui-icon-group"
                           style="font-size: 26px;padding-top: 3px;margin-right: 6px;"></i>
                        <div class="console-app-name">用户</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group">
                        <i class="console-app-icon layui-icon layui-icon-chart" style="color: #95de64;"></i>
                        <div class="console-app-name">分析</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group">
                        <i class="console-app-icon layui-icon layui-icon-cart" style="color: #ff9c6e;"></i>
                        <div class="console-app-name">商品</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group" ew-href="page/Order.php" ew-title="订单">
                        <i class="console-app-icon layui-icon layui-icon-form"
                           style="color: #b37feb;font-size: 30px;"></i>
                        <div class="console-app-name">订单</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6" style="padding-bottom: 0;">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group">
                        <i class="console-app-icon layui-icon layui-icon-layer"
                           style="color: #ffd666;font-size: 34px;"></i>
                        <div class="console-app-name">票据</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group">
                        <i class="console-app-icon layui-icon layui-icon-email"
                           style="color: #5cdbd3;font-size: 36px;"></i>
                        <div class="console-app-name">消息</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group">
                        <i class="console-app-icon layui-icon layui-icon-note"
                           style="color: #ff85c0;font-size: 28px;"></i>
                        <div class="console-app-name">标签</div>
                    </div>
                </div>
                <div class="layui-col-xs6 layui-col-sm3">
                    <div class="console-app-group">
                        <i class="console-app-icon layui-icon layui-icon-slider" style="color: #ffc069;"></i>
                        <div class="console-app-name">配置</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8 layui-col-sm6">
            <div class="layui-row layui-col-space15">
                
                
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">订单收入统计</div>
                        <div class="layui-card-body">
                            <table class="layui-table" lay-skin="line">
                                <colgroup>
                                    <col width="40"/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col/>
                                    <col width="160"/>
                                </colgroup>
                                <thead>
                                <tr>
                                    <td align="center">#</td>
                                    <td align="center">支付宝</td>
                                    <td align="center">微信支付</td>
                                    <td align="center">QQ钱包</td>
                                    <td align="center">USDT</td>
                                    <td align="center">总计</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td align="center">今日</td>
                                    <td align="center"><span class="text-success"><?=$data['order_today']['alipay']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_today']['wxpay']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_today']['qqpay']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_today']['usdt']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_today']['all']?></span></td>
                                </tr>
                                <tr>
                                    <td align="center">昨日</td>
                                    <td align="center"><span class="text-success"><?=$data['order_lastday']['alipay']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_lastday']['wxpay']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_lastday']['qqpay']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_lastday']['usdt']?></span></td>
                                    <td align="center"><span class="text-success"><?=$data['order_lastday']['all']?></span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md4 layui-col-sm6">
            <div class="layui-card">
                <div class="layui-card-header">版本信息</div>
                <div class="layui-card-body">
                    <table class="layui-table layui-text">
                        <colgroup>
                            <col width="90">
                            <col>
                        </colgroup>
                        <tbody>
                        <script type="text/html" ew-tpl>
                            <tr>
                                <td>当前版本</td>
                                <td>v<?php echo VERSIONS;?> &emsp; 开源本地版</td>
                            </tr>
                            <tr>
                                <td>PHP版本</td>
                                <td><?php echo phpversion() ?><?php if(ini_get('safe_mode')) { echo '线程安全'; } else { echo '非线程安全'; } ?></td>
                            </tr>
                        </script>
                        <tr>
                            <td>SQL版本</td>
                            <td><?php $DB_VERSION = $DB->query("select VERSION()")->fetch(); echo $DB_VERSION[0]; ?></td>
                        </tr>
                        <tr>
                            <td>运行模式</td>
                            <td>本地开源版，已移除远程授权依赖</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script>
    layui.use(['layer', 'carousel', 'element'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var carousel = layui.carousel;
        var device = layui.device();

        // 渲染轮播
        carousel.render({
            elem: '#workplaceNewsCarousel',
            width: '100%',
            height: '70px',
            arrow: 'none',
            autoplay: true,
            trigger: device.ios || device.android ? 'click' : 'hover',
            anim: 'fade'
        });

    });
</script>
</body>
</html>
