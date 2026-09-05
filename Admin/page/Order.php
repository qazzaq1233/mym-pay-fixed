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
    <title>用户管理</title>
    <link rel="stylesheet" href="../assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="../assets/module/admin.css?v=318"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /** 数据表格中的select尺寸调整 */
        .layui-table-view .layui-table-cell .layui-select-title .layui-input {
            height: 28px;
            line-height: 28px;
        }

        .layui-table-view [lay-size="lg"] .layui-table-cell .layui-select-title .layui-input {
            height: 40px;
            line-height: 40px;
        }

        .layui-table-view [lay-size="lg"] .layui-table-cell .layui-select-title .layui-input {
            height: 40px;
            line-height: 40px;
        }

        .layui-table-view [lay-size="sm"] .layui-table-cell .layui-select-title .layui-input {
            height: 20px;
            line-height: 20px;
        }

        .layui-table-view [lay-size="sm"] .layui-table-cell .layui-btn-xs {
            height: 18px;
            line-height: 18px;
        }
    </style>
</head>
<body>
<!-- 正文开始 -->
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <!-- 表格工具栏 -->
            <form class="layui-form toolbar">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">搜索类型</label>
                        <div class="layui-input-inline">
                            <select name="column">
                                <option value="">请选择类型</option>
                                <option value="money">金额</option>
                                <option value="price">实付</option>
                                <option value="pid">商户号</option>
                                <option value="name">商品名称</option>
                                <option value="trade_no">系统订单号</option>
                                <option value="out_trade_no">商户订单号</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">搜索内容</label>
                        <div class="layui-input-inline">
                            <input name="value" class="layui-input" placeholder="搜索内容"/>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">支付方式</label>
                        <div class="layui-input-inline">
                            <select name="type" id="type">
                                <option value="">支付方式</option>
                                <option value="alipay">支付宝</option>
                                <option value="wxpay">微信</option>
                                <option value="qqpay">QQ</option>
                                <option value="usdt">USDT</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">搜索状态</label>
                        <div class="layui-input-inline">
                            <select name="dstatus" id="dstatus">
                                <option value="">显示全部</option>
                                <option value="1">只显示已完成</option>
                                <option value="2">只显示未完成</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">&emsp;
                        <button class="layui-btn icon-btn" lay-filter="userTbSearch" lay-submit>
                            <i class="layui-icon">&#xe615;</i>搜索
                        </button>
                    </div>
                </div>
            </form>
            <!-- 数据表格 -->
            <table id="OrderTable" lay-filter="OrderTable"></table>
        </div>
    </div>
</div>
<a style="display: none;" href="" id="vurl" rel="noreferrer" target="_blank"></a>
<!-- 表格操作列 -->
<script type="text/html" id="tbBasicTbBar">
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
    <a class="layui-btn layui-btn-xs" data-dropdown="#userTbDrop{{d.LAY_INDEX}}" no-shade="true">
        更多<i class="layui-icon layui-icon-drop" style="font-size: 12px;margin-right: 0;"></i></a>
    <!-- 下拉菜单 -->
    <ul class="dropdown-menu-nav dropdown-bottom-right layui-hide" id="userTbDrop{{d.LAY_INDEX}}">
        <div class="dropdown-anchor"></div>
        <li><a lay-event="lock"><i class="layui-icon layui-icon-password"></i>改已完成</a></li>
        <li><a lay-event="notify"><i class="layui-icon layui-icon-key"></i>重新通知</a></li>
    </ul>
</script>

<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script>
    //layui.use(['layer', 'form', 'table', 'util', 'admin', 'xmSelect'], function () {
    layui.use(['layer', 'form', 'table', 'util', 'dropdown'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;
        var util = layui.util;
        var admin = layui.admin;
        var xmSelect = layui.xmSelect;
        /* 渲染表格 */
        var insTb = table.render({
            elem: '#OrderTable',
            url: '../Ajax.php?act=Order',
            page: true,
            toolbar: ['<p>',
                '</p>'].join(''),
            cols: [[
                {field: 'trade_no', width: 185, title: '系统订单号', sort: true},
                {field: 'out_trade_no', width: 185, title: '商户订单号'},
                {field: 'name', width: 110, title: '商品名称'},
                {field: 'money', width: 70, title: '金额'},
                {field: 'price', width: 70, title: '实付'},
                {field: 'pid', width: 115, title: '商户号'},
                {field: 'type', width: 90, title: '支付方式', templet: function (d) {
                    if(d.type =='usdt'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16"><font color="green">USDT</font>';
                    }else if(d.type =='alipay'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16"><font color="green">支付宝</font>';
                    }else if(d.type =='wxpay'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16"><font color="green">微信</font>';
                    }else if(d.type =='qqpay'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16"><font color="green">QQ</font>';
                    }
                    }},
                {field: 'status', title: '状态',width: 78, align: 'left', templet: function (d) {
                    if (d.status == 0) {
                        return '<span class="layui-btn layui-btn-primary layui-btn-xs">未支付</span>';
                    }else{
                        return '<span class="layui-btn layui-btn-xs">已支付</span>';
                    }
                    }
                },
                {field: 'addtime', width: 158, title: '创建时间/完成时间', templet: function(d){
                    if(d.endtime){
                        return d.addtime+'<br>'+d.endtime;
                    }else{
                        return d.addtime;
                    }
                }},
                {title: '操作', toolbar: '#tbBasicTbBar', align: 'center', minWidth: 130},
                {field: 'url', title: '下单地址',Width: 130,templet:function(d){
                    return '<a href="http://'+d.url+'" target="_blank">'+d.url+'</a>';
                }}
            ]]
        });

        /* 表格搜索 */
        form.on('submit(userTbSearch)', function (data) {
            insTb.reload({where: data.field, page: {curr: 1}});
            return false;
        });
        form.on('submit(type)', function (data) {
            insTb.reload({where: data.field, page: {curr: 1}});
            return false;
        });
        form.on('submit(dstatus)', function (data) {
            insTb.reload({where: data.field, page: {curr: 1}});
            return false;
        });

        /* 表格工具条点击事件 */
        table.on('tool(OrderTable)', function (obj) {
            if (obj.event === 'edit') { // 修改
                showEditModel(obj.data);
            } else if (obj.event === 'del') { // 删除
                doDel(obj);
            } else if (obj.event === 'reset') { // 重置密码
                resetPsw(obj);
            }
        });

        /* 删除 */
        function doDel(obj) {
            layer.confirm('确定要删除选中数据吗？', {
                skin: 'layui-layer-admin',
                shade: .1
            }, function (i) {
                layer.close(i);
                var loadIndex = layer.load(2);
                $.get('json/ok.json', {
                    id: obj.data ? obj.data.userId : '',
                    ids: obj.ids ? obj.ids.join(',') : ''
                }, function (res) {
                    layer.close(loadIndex);
                    if (res.code === 200) {
                        layer.msg(res.msg, {icon: 1});
                        insTb.reload({page: {curr: 1}});
                    } else {
                        layer.msg(res.msg, {icon: 2});
                    }
                }, 'json');
            });
        }

        /* 修改用户状态 */
        form.on('switch(userTbStateCk)', function (obj) {
            var loadIndex = layer.load(2);
            $.get('json/ok.json', {
                userId: obj.elem.value,
                state: obj.elem.checked ? 0 : 1
            }, function (res) {
                layer.close(loadIndex);
                if (res.code === 200) {
                    layer.msg(res.msg, {icon: 1});
                } else {
                    layer.msg(res.msg, {icon: 2});
                    $(obj.elem).prop('checked', !obj.elem.checked);
                    form.render('checkbox');
                }
            }, 'json');
        });
        
        /* 表格工具条点击事件 */
        table.on('tool(OrderTable)', function (obj) {
            var data = obj.data; // 获得当前行数据
            var loadIndex = layer.load(2);
            if (obj.event === 'del') { // 删除
                $.get('../Ajax.php?act=setStatus',{
                    trade_no:data.trade_no,
                    status:5
                }, function (res) {
                    layer.close(loadIndex);
                    if (res.code === 200) {
                        layer.msg(res.msg, {icon: 1});
                        insTb.reload({page: {curr: 1}});
                    } else {
                        layer.msg(res.msg, {icon: 2});
                    }
                }, 'json');
            } else if (obj.event === 'notify') { // 重置密码
                $.post('../Ajax.php?act=notify',{
                    trade_no:data.trade_no
                }, function (res) {
                    layer.close(loadIndex);
                    if (res.code === 200) {
                        $("#vurl").attr("href",res.url);
                        document.getElementById("vurl").click();
                    } else {
                        layer.msg(res.msg, {icon: 2});
                    }
                }, 'json');
            } else if (obj.event === 'lock') { // 改已完成
                $.get('../Ajax.php?act=setStatus',{
                    trade_no:data.trade_no,
                    status:1
                }, function (res) {
                    layer.close(loadIndex);
                    if (res.code === 200) {
                        layer.msg(res.msg, {icon: 1});
                        insTb.reload({page: {curr: 1}});
                    } else {
                        layer.msg(res.msg, {icon: 2});
                    }
                }, 'json');
            }
            dropdown.hideAll();
        });
    });
</script>
</body>
</html>
