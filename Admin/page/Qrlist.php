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
                                <option value="pid">商户PID</option>
                                <option value="id">二维码ID</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">搜索内容</label>
                        <div class="layui-input-inline">
                            <input name="value" class="layui-input" placeholder="搜索内容"/>
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
            <table id="userTable" lay-filter="userTable"></table>
        </div>
    </div>
</div>

<!-- 表格操作列 -->
<script type="text/html" id="tbBasicTbBar">
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
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
            elem: '#userTable',
            url: '../Ajax.php?act=Qrlist',
            page: true,
            cols: [[
                {field: 'id', width: 60, title: 'ID'},
                {field: 'pid', width: 105, title: '商户PID'},
                {field: 'beizhu', width: 130, title: '备注'},
                {field: 'type', width: 110, title: '类型', templet: function (d) {
                    if(d.type =='usdt'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16">'+d.pay_type;
                    }else if(d.type =='alipay'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16">'+d.pay_type;
                    }else if(d.type =='wxpay'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16">'+d.pay_type;
                    }else if(d.type =='qqpay'){
                        return '<img src="/Mym/Assets/Icon/'+d.type+'.ico" width="16">'+d.pay_type;
                    }
                    }},
                {field: 'money', width: 95, title: '监控余额'},
                {field: 'jrzpfcgje', width: 95, title: '今日收入'},
                {field: 'jrzkl', width: 95, title: '今日成率'},
                {field: 'zrzpfcgje', width: 95, title: '昨日收入'},
                {field: 'zrzkl', width: 95, title: '昨日成率'},
                {field: 'zpfcgje', width: 95, title: '总收入'},
                {field: 'status', width: 170, title: '状态'},
                {field: 'time', width: 190, title: '在线时长'},
                {field: 'addtime', width: 158, title: '添加时间'},
                {title: '操作', toolbar: '#tbBasicTbBar', align: 'center', minWidth: 65}
            ]]
        });

        /* 表格搜索 */
        form.on('submit(userTbSearch)', function (data) {
            insTb.reload({where: data.field, page: {curr: 1}});
            return false;
        });

        /* 表格工具条点击事件 */
        table.on('tool(userTable)', function (obj) {
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
                $.post('../Ajax.php?act=Del_Qr', {
                    id: obj.data ? obj.data.id : '',
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
        table.on('tool(tbBasicTable)', function (obj) {
            var data = obj.data; // 获得当前行数据
            if (obj.event === 'edit') { // 修改
                layer.msg('点击了修改');
            } else if (obj.event === 'del') { // 删除
                layer.msg('点击了删除');
            } else if (obj.event === 'view') { // 查看
                layer.msg('点击了查看');
            } else if (obj.event === 'reset') { // 重置密码
                layer.msg('点击了重置密码');
            } else if (obj.event === 'lock') { // 锁定
                layer.msg('点击了锁定');
            }
            dropdown.hideAll();
        });

    });
</script>
</body>
</html>
