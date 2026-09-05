<?php
include("../../Mym/Common.php");
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>卡片列表</title>
    <link rel="stylesheet" href="../assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="../assets/module/admin.css?v=318"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /** 项目列表样式 */
        .project-list-item {
            background-color: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 4px;
            cursor: pointer;
            transition: all .2s;
        }

        .project-list-item:hover {
            box-shadow: 0 2px 10px rgba(0, 0, 0, .15);
        }

        .project-list-item .project-list-item-cover {
            width: 100%;
            height: 220px;
            display: block;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        .project-list-item-body {
            padding: 20px;
        }

        .project-list-item .project-list-item-body > h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 12px;
        }

        .project-list-item .project-list-item-text {
            height: 44px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .project-list-item .project-list-item-desc {
            position: relative;
        }

        .project-list-item .project-list-item-desc .time {
            color: #999;
            font-size: 12px;
        }

        .project-list-item .project-list-item-desc .ew-head-list {
            position: absolute;
            right: 0;
            top: 0;
        }

        .ew-head-list .ew-head-list-item {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 1px solid #fff;
            margin-left: -10px;
        }

        .ew-head-list .ew-head-list-item:first-child {
            margin-left: 0;
        }

        /** // 项目列表样式结束 */

        /** 应用列表样式 */
        .application-list-item {
            background-color: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 4px;
            cursor: pointer;
            transition: all .2s;
        }

        .application-list-item:hover {
            box-shadow: 0 2px 10px rgba(0, 0, 0, .15);
        }

        .application-list-item .application-list-item-header {
            padding: 16px 12px 0 12px;
        }

        .application-list-item .application-list-item-header .head {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .application-list-item .application-list-item-header > h2 {
            color: #333;
            font-size: 18px;
            display: inline-block;
        }

        .application-list-item .application-list-item-body {
            padding: 12px 12px 12px 50px;
            font-size: 0;
        }

        .application-list-item .application-list-item-body .text-num-item {
            display: inline-block;
            width: 50%;
            font-size: 26px;
            color: #666;
        }

        .application-list-item .application-list-item-body .text-num-item .text-num-item-title {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }

        .application-list-item .application-list-item-body .text-num-item small {
            font-size: 16px;
        }

        .application-list-item .application-list-item-tool {
            font-size: 0;
            background-color: #FAFAFA;
            border-top: 1px solid #e8e8e8;
            padding: 10px 0 5px 0;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .application-list-item .application-list-item-tool .application-list-item-tool-item {
            display: inline-block;
            width: 25%;
            font-size: 18px;
            text-align: center;
            color: #999;
            border-right: 1px solid #e8e8e8;
            box-sizing: border-box;
            cursor: pointer;
        }

        .application-list-item .application-list-item-tool .application-list-item-tool-item:last-child {
            border-right: none;
        }

        /** // 应用列表样式结束 */

        /** 文章列表样式 */
        .article-list-item {
            border-bottom: 1px solid #e8e8e8;
            margin-top: 16px;
            position: relative;
        }

        .article-list-item > h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 12px;
        }

        .article-list-item > .layui-badge-rim {
            position: absolute;
            right: 0;
            top: 0;
        }

        .article-list-item .layui-badge-list .layui-badge {
            padding-top: 0;
            padding-bottom: 0;
        }

        .article-list-item .article-list-item-text {
            margin-bottom: 12px;
        }

        .article-list-item .article-list-item-desc {
            margin-bottom: 12px;
        }

        .article-list-item .article-list-item-desc .head {
            width: 20px;
            height: 20px;
            border-radius: 50%;
        }

        .article-list-item .article-list-item-desc > * {
            vertical-align: middle;
        }

        .article-list-item .article-list-item-tool {
            color: #666;
            margin-bottom: 5px;
        }

        .article-list-item .article-list-item-tool .article-list-item-tool-item {
            border-right: 1px solid #e8e8e8;
            padding: 0 15px;
            cursor: pointer;
        }

        .article-list-item .article-list-item-tool .article-list-item-tool-item:first-child {
            padding-left: 0;
        }

        .article-list-item .article-list-item-tool .article-list-item-tool-item:last-child {
            border-right: none;
            padding-right: 0;
        }

        .article-list-item .article-list-item-tool .article-list-item-tool-item > * {
            vertical-align: middle;
        }

        .article-list-item .article-list-item-tool .article-list-item-tool-item.star-active {
            color: #01AAED;
        }

        .article-list-item .article-list-item-tool .article-list-item-tool-item.star-active .layui-icon-rate:before {
            content: "\e67a";
        }

        /** // 文章列表样式结束 */
    </style>
</head>
<body>

<!-- 正文开始 -->
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">网站模板设置</div>
        <div class="layui-tab layui-tab-brief">
            
            <div id="template">
                <p class="layui-form-label">当前模板:<?=$conf['template']?></p>
                <div class="layui-input-block">
                    <img layer-src="/Template/<?=$conf['template']?>/preview.png" src="/Template/<?=$conf['template']?>/preview.png" width="280px" height="100%">
                </div>
            </div>
            <br>
            <hr><br>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show" style="padding-top: 20px;">
                    <div class="layui-row layui-col-space30" id="demoCardList1"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 项目模板 -->
<script type="text/html" id="demoCardItem1">
    <div class="layui-col-md3">
        <div class="project-list-item">
            <a onclick="changeTemplate('{{d.title}}');"><img class="project-list-item-cover" src="/Template/{{d.title}}/preview.png"/>
            <div class="project-list-item-body">
                <h2>{{d.title}}</h2>
            </div>
        </div>
    </div>
</script>

<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script src="../../Mym/Assets/Login/static/js/jquery-3.2.1.min.js"></script>
<script>
    layui.use(['layer', 'dataGrid', 'element', 'dropdown'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var dataGrid = layui.dataGrid;

        // 项目
        $.get('../Ajax.php?act=template', function (res) {
            dataGrid.render({
                elem: '#demoCardList1',
                templet: '#demoCardItem1',
                data: res.data,
                page: {limit: 8, limits: [10, 20, 50]}
            });
        });

        dataGrid.on('item(demoCardList1)', function (obj) {
            //layer.msg('点击了第' + (obj.index + 1) + '个');
        });
        
        
    });
        function changeTemplate(template){
            $.post({
                url : '../Ajax.php?act=Set',
                data : {template:template},
                dataType : 'json',
                success : function(res) {
                    if (res.code === 0) {
                        layer.msg('设置保存成功！', {icon: 1});
                    } else {
                        layer.msg(res.msg, {icon: 2});
                    }
                }
            });
        }
</script>
</body>
</html>