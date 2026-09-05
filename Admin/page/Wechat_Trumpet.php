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
                                <option value="wx_name">微信昵称</option>
                                <option value="beizhu">微信备注</option>
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
<script type="text/html" id="userTbBar">
    {{# if(d.hook_type == 1){  }}
        <a class="layui-btn layui-btn-xs" lay-event="update">更新</a>
    {{# } }}
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">修改</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
<!-- 表单弹窗 -->
<script type="text/html" id="userEditDialog">
    <form id="userEditForm" lay-filter="userEditForm" class="layui-form model-form">
        <input name="id" type="hidden"/>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">监控服务端:</label>
            <div class="layui-input-block">
                <select name="hook_type">
                    <option value="">请选择类型</option>
                    <option value="0">PC监控</option>
                    <option value="1">云端监控</option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">微信账号(必填):</label>
            <div class="layui-input-block">
                <input name="wx_user" placeholder="请输入微信账号" class="layui-input"
                       lay-verType="tips" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">微信昵称(必填):</label>
            <div class="layui-input-block">
                <input name="wx_name" placeholder="请输入微信昵称" class="layui-input"
                       lay-verType="tips" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">微信备注:</label>
            <div class="layui-input-block">
                <input name="beizhu" placeholder="请输入微信备注" class="layui-input"
                       lay-verType="tips" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">排序(越小越靠前):</label>
            <div class="layui-input-block">
                <input name="sort" value="50" placeholder="请输入排序" class="layui-input"
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
            url: '../Ajax.php?act=Wechat_Trumpet',
            page: true,
            toolbar: ['<p>',
                '<button lay-event="add" class="layui-btn layui-btn-sm icon-btn"><i class="layui-icon">&#xe654;</i>添加</button>&nbsp;',
                '<button lay-event="del" class="layui-btn layui-btn-sm layui-btn-danger icon-btn"><i class="layui-icon">&#xe640;</i>删除</button>',
                '</p>'].join(''),
            cellMinWidth: 100,
            cols: [[
                {type: 'checkbox'},
                {field: 'id', title: 'ID', width: 60},
                {field: 'hook_type', title: '运行端',width: 100, align: 'left', templet: function (d) {
                    if (d.hook_type == 0) {
                        return '<font color="green">PC监控端</font>';
                    }else{
                        return '<font color="green">云端监控</font>';
                    }
                    }
                },
                {field: 'wx_user', title: '微信店员账号', width: 150},
                {field: 'wx_name', title: '微信店员昵称', width: 150},
                {field: 'beizhu', title: '备注', width: 120},
                {field: 'addtime', title: '添加时间', width: 165},
                {field: 'login_time', title: '当前在线状态', width: 80},
                {title: '操作', toolbar: '#userTbBar', align: 'center', minWidth: 200}
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
            } else if (obj.event === 'update') {
                update(obj);
            }
        });
        
        /* 表格头工具栏点击事件 */
        table.on('toolbar(userTable)', function (obj) {
            if (obj.event === 'add') { // 添加
                showEditModel();
            }
        });
        
        // 最大化最小化
        function update(obj){
            parent.layui.admin.open({
                type: 2,
                title: '更新云端',
                shade: 0,
                maxmin: true,
                resize: true,
                area: ['640px', '480px'],
                content: 'page/tpl/updata.php?id='+obj.data.id,
                success: function (layero) {
                    $(layero).attr('minleft', '250px');
                }
            });
            //Get_Wxyun(obj.data.id);
        }

        /* 显示表单弹窗 */
        function showEditModel(mData) {
            admin.open({
                type: 1,
                title: (mData ? '修改' : '添加') + '监控端',
                content: $('#userEditDialog').html(),
                success: function (layero, dIndex) {
                    // 回显表单数据
                    form.val('userEditForm', mData);
                    // 表单提交事件
                    form.on('submit(userEditSubmit)', function (data) {
                        var loadIndex = layer.load(2);
                        $.post(mData ? '../Ajax.php?act=Edit_Wechet_Tp' : '../Ajax.php?act=Add_Wechet_Tp', data.field, function (res) {  // 实际项目这里url可以是mData?'user/update':'user/add'
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
                $.post('../Ajax.php?act=Del_Wechet_Tp', {
                    id: obj.data.id
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
