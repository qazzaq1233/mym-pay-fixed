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
    <title>公告设置</title>
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
        <div class="layui-card-body">
            <!-- 数据表格 -->
            <table id="userTable" lay-filter="userTable"></table>
        </div>
    </div>
</div>

<!-- 表格操作列 -->
<script type="text/html" id="userTbBar">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<!-- 表格状态列 -->
<script type="text/html" id="userTbState">
    <input type="checkbox" lay-filter="userTbStateCk" value="{{d.status}}" lay-skin="switch"
           lay-text="显示|隐藏" {{d.status==1?'checked':''}} style="display: none;"/>
    <p style="display: none;">{{d.status==1?'显示':'隐藏'}}</p>
</script>
<!-- 表单弹窗 -->
<script type="text/html" id="userEditDialog">
    <form id="userEditForm" lay-filter="userEditForm" class="layui-form model-form">
        <input name="id" type="hidden"/>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">名称:</label>
            <div class="layui-input-block">
                <input name="name" placeholder="请输入名称" class="layui-input"
                       />
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">IP:</label>
            <div class="layui-input-block">
                <input name="ip" value="" placeholder="请输入IP" class="layui-input"
                       lay-verType="tips" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">端口:</label>
            <div class="layui-input-block">
                <input name="do" value="" placeholder="请输入端口" class="layui-input"
                       lay-verType="tips" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">账号:</label>
            <div class="layui-input-block">
                <input name="user" value="" placeholder="请输入账号" class="layui-input"
                       lay-verType="tips" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">密码:</label>
            <div class="layui-input-block">
                <input name="pass" value="" placeholder="请输入密码" class="layui-input"
                       lay-verType="tips" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item text-right">
            <button class="layui-btn" lay-filter="userEditSubmit" lay-submit>保存</button>
            <button class="layui-btn layui-btn-primary" type="button" ew-event="closeDialog">取消</button>
        </div>
    </form>
</script>

<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script>
    layui.use(['layer', 'form', 'table', 'util', 'admin', 'xmSelect'], function () {
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
            url: '../Ajax.php?act=daili',
            page: true,
            toolbar: ['<p>',
                '<button lay-event="add" class="layui-btn layui-btn-sm icon-btn"><i class="layui-icon">&#xe654;</i>添加</button>&nbsp;',
                '<button lay-event="del" class="layui-btn layui-btn-sm layui-btn-danger icon-btn"><i class="layui-icon">&#xe640;</i>删除</button>',
                '</p>'].join(''),
            cellMinWidth: 100,
            cols: [[
                {type: 'checkbox'},
                {field: 'id', title: 'ID', width: 50},
                {field: 'name', title: '名称', width: 100},
                {field: 'ip', title: 'IP', width: 180},
                {field: 'do', title: '端口', width: 180},
                {field: 'user', title: '账号', width: 180},
                {field: 'pass', title: '密码', width: 180},
                {title: '操作', toolbar: '#userTbBar', align: 'center', minWidth: 120}
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
            }
        });

        /* 表格头工具栏点击事件 */
        table.on('toolbar(userTable)', function (obj) {
            if (obj.event === 'add') { // 添加
                showEditModel();
            } else if (obj.event === 'del') { // 删除
                var checkRows = table.checkStatus('userTable');
                if (checkRows.data.length === 0) {
                    layer.msg('请选择要删除的数据', {icon: 2});
                    return;
                }
                var ids = checkRows.data.map(function (d) {
                    return d.userId;
                });
                doDel({ids: ids});
            }
        });

        /* 显示表单弹窗 */
        function showEditModel(mData) {
            admin.open({
                type: 1,
                title: (mData ? '修改' : '添加') + '代理',
                content: $('#userEditDialog').html(),
                success: function (layero, dIndex) {
                    // 回显表单数据
                    form.val('userEditForm', mData);
                    // 表单提交事件
                    form.on('submit(userEditSubmit)', function (data) {
                        var loadIndex = layer.load(2);
                        $.post(mData ? '../Ajax.php?act=Edit_daili' : '../Ajax.php?act=Add_daili', data.field, function (res) {  // 实际项目这里url可以是mData?'user/update':'user/add'
                            layer.close(loadIndex);
                            if (res.code === 200) {
                                layer.close(dIndex);
                                layer.msg(res.msg, {icon: 1});
                                insTb.reload({page: {curr: 1}});
                            } else {
                                layer.msg(res.msg, {icon: 2});
                            }
                        }, 'json');
                        return false;
                    });
                    
                    // 禁止弹窗出现滚动条
                    $(layero).children('.layui-layer-content').css('overflow', 'visible');
                }
            });
        }

        /* 删除 */
        function doDel(obj) {
            layer.confirm('确定要删除选中数据吗？', {
                skin: 'layui-layer-admin',
                shade: .1
            }, function (i) {
                layer.close(i);
                var loadIndex = layer.load(2);
                $.post('../Ajax.php?act=Del_dll', {
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
    });
</script>
</body>
</html>
