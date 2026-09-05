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
    <title>通道展示配置</title>
    <link rel="stylesheet" href="../assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="../assets/module/admin.css?v=318"/>
</head>
<body>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-header">支付方式管理</div>
        <div class="layui-card-body">
            <div class="layui-alert layui-alert-primary" style="margin-bottom: 12px;">先配置支付宝、微信、QQ钱包、USDT 等支付方式；点击某个支付方式后，下方只展示该支付方式下的通道。自定义代码建议使用小写字母、数字、下划线。</div>
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" id="addTypeBtn"><i class="layui-icon layui-icon-add-1"></i> 新增支付方式</button>
            </div>
            <table id="typeTable" lay-filter="typeTable"></table>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header"><span id="channelTitle">通道管理</span></div>
        <div class="layui-card-body">
            <div class="layui-btn-container">
                <button class="layui-btn layui-btn-sm" id="addChannelBtn"><i class="layui-icon layui-icon-add-1"></i> 新增通道</button>
            </div>
            <table id="channelTable" lay-filter="channelTable"></table>
        </div>
    </div>
</div>

<script type="text/html" id="typeTbState">
    <input type="checkbox" lay-filter="typeTbStateCk" value="{{d.code}}" lay-skin="switch" lay-text="启用|关闭" {{d.status==1?'checked':''}} style="display: none;"/>
</script>
<script type="text/html" id="channelTbState">
    <input type="checkbox" lay-filter="channelTbStateCk" value="{{d.code}}" lay-skin="switch" lay-text="启用|关闭" {{d.status==1?'checked':''}} style="display: none;"/>
</script>
<script type="text/html" id="typeTbBar">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>
</script>
<script type="text/html" id="channelTbBar">
    <a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>
</script>
<script type="text/html" id="editDialog">
    <form id="editForm" lay-filter="editForm" class="layui-form model-form">
        <input name="group" type="hidden"/>
        <input name="is_new" type="hidden"/>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">代码:</label>
            <div class="layui-input-block">
                <input name="code" placeholder="只能填写小写字母、数字、下划线" class="layui-input" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item channel-type-item" style="display:none;">
            <label class="layui-form-label layui-form-required">所属支付方式:</label>
            <div class="layui-input-block">
                <select name="type" id="editTypeSelect"></select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label layui-form-required">名称:</label>
            <div class="layui-input-block">
                <input name="name" placeholder="请输入展示名称" class="layui-input" lay-verify="required" required/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">排序:</label>
            <div class="layui-input-block">
                <input name="sort" placeholder="数字越小越靠前" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">状态:</label>
            <div class="layui-input-block">
                <input type="checkbox" name="status" value="1" lay-skin="switch" lay-text="启用|关闭"/>
            </div>
        </div>
        <div class="layui-form-item text-right">
            <button class="layui-btn" lay-filter="editSubmit" lay-submit>保存</button>
            <button class="layui-btn layui-btn-primary" type="button" ew-event="closeDialog">取消</button>
        </div>
    </form>
</script>

<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script>
layui.use(['layer', 'form', 'table', 'admin'], function () {
    var $ = layui.jquery;
    var layer = layui.layer;
    var form = layui.form;
    var table = layui.table;
    var admin = layui.admin;

    var currentType = '';
    var currentTypeName = '';
    var typeList = [];

    $('#addTypeBtn').on('click', function(){
        showEditModel('types', {code:'', name:'', sort:999, status:1}, true);
    });
    $('#addChannelBtn').on('click', function(){
        if(!currentType){ layer.msg('请先选择一个支付方式', {icon: 2}); return; }
        showEditModel('channels', {code:'', type:currentType, name:'', sort:999, status:1}, true);
    });

    var typeTable = table.render({
        elem: '#typeTable',
        url: '../Ajax.php?act=pay_channel_config&group=types',
        page: false,
        cellMinWidth: 100,
        cols: [[
            {field: 'code', title: '支付方式代码', width: 160},
            {field: 'name', title: '支付方式名称'},
            {field: 'sort', title: '排序', width: 90},
            {field: 'status', title: '状态', templet: '#typeTbState', width: 120},
            {title: '操作', toolbar: '#typeTbBar', align: 'center', width: 100}
        ]],
        done: function(res){
            typeList = res.data || [];
            if(!currentType && res.data && res.data.length > 0){
                selectType(res.data[0]);
            }
            setTimeout(function(){
                $('#typeTable').next('.layui-table-view').find('.layui-table-body tbody tr').each(function(){
                    var index = $(this).data('index');
                    if(res.data[index] && res.data[index].code === currentType){
                        $(this).addClass('layui-table-click').siblings().removeClass('layui-table-click');
                    }
                });
            }, 0);
        }
    });

    var channelTable = table.render({
        elem: '#channelTable',
        url: '../Ajax.php?act=pay_channel_config&group=channels',
        where: {type: '__none__'},
        page: false,
        cellMinWidth: 100,
        text: {none: '请先点击上方支付方式，或当前支付方式下暂无通道'},
        cols: [[
            {field: 'code', title: '通道代码', width: 170},
            {field: 'type_name', title: '所属支付方式', width: 130},
            {field: 'name', title: '通道名称'},
            {field: 'sort', title: '排序', width: 90},
            {field: 'status', title: '状态', templet: '#channelTbState', width: 120},
            {title: '操作', toolbar: '#channelTbBar', align: 'center', width: 100}
        ]]
    });

    table.on('row(typeTable)', function (obj) {
        selectType(obj.data);
        obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
    });

    table.on('tool(typeTable)', function (obj) {
        if (obj.event === 'edit') showEditModel('types', obj.data);
    });
    table.on('tool(channelTable)', function (obj) {
        if (obj.event === 'edit') showEditModel('channels', obj.data);
    });

    form.on('switch(typeTbStateCk)', function (obj) {
        saveItem('types', obj.elem.value, {status: obj.elem.checked ? 1 : 0}, function(ok){
            if(!ok){ $(obj.elem).prop('checked', !obj.elem.checked); form.render('checkbox'); }
            if(ok && currentType === obj.elem.value){ channelTable.reload({where: {type: currentType}}); }
        }, false);
    });
    form.on('switch(channelTbStateCk)', function (obj) {
        saveItem('channels', obj.elem.value, {status: obj.elem.checked ? 1 : 0}, function(ok){
            if(!ok){ $(obj.elem).prop('checked', !obj.elem.checked); form.render('checkbox'); }
        }, false);
    });

    function selectType(data) {
        currentType = data.code;
        currentTypeName = data.name;
        $('#channelTitle').text(currentTypeName + ' - 通道管理');
        channelTable.reload({where: {type: currentType}});
    }

    function showEditModel(group, data, isNew) {
        isNew = !!isNew;
        admin.open({
            type: 1,
            title: (isNew ? '新增' : '编辑') + (group === 'types' ? '支付方式' : '通道'),
            content: $('#editDialog').html(),
            success: function (layero, dIndex) {
                var typeOptions = '';
                $.each(typeList, function(i, item){
                    typeOptions += '<option value="'+item.code+'">'+item.name+'（'+item.code+'）</option>';
                });
                $(layero).find('#editTypeSelect').html(typeOptions);
                if(group === 'channels'){
                    $(layero).find('.channel-type-item').show();
                }
                form.val('editForm', {
                    group: group,
                    is_new: isNew ? 1 : 0,
                    code: data.code,
                    type: data.type || currentType,
                    name: data.name,
                    sort: data.sort,
                    status: data.status == 1 ? 1 : 0
                });
                if(!isNew){
                    $(layero).find('input[name="code"]').prop('readonly', true).addClass('layui-disabled');
                }
                if(data.status == 1){
                    $(layero).find('input[name="status"]').prop('checked', true);
                }
                form.render();
                form.on('submit(editSubmit)', function (formData) {
                    formData.field.status = formData.field.status ? 1 : 0;
                    saveItem(group, formData.field.code, formData.field, function(ok){
                        if(ok)layer.close(dIndex);
                    });
                    return false;
                });
                $(layero).children('.layui-layer-content').css('overflow', 'visible');
            }
        });
    }

    function saveItem(group, code, field, callback, showSuccess) {
        if(typeof showSuccess === 'undefined')showSuccess = true;
        var postData = {
            group: group,
            code: code
        };
        if(typeof field.is_new !== 'undefined')postData.is_new = field.is_new;
        if(typeof field.type !== 'undefined')postData.type = field.type;
        if(typeof field.name !== 'undefined')postData.name = field.name;
        if(typeof field.sort !== 'undefined')postData.sort = field.sort;
        if(typeof field.status !== 'undefined')postData.status = field.status;
        var loadIndex = layer.load(2);
        $.post('../Ajax.php?act=save_pay_channel_config', postData, function (res) {
            layer.close(loadIndex);
            if (res.code === 200) {
                if(showSuccess)layer.msg(res.msg, {icon: 1});
                if(showSuccess){
                    if(group === 'types' && field.is_new == 1){
                        currentType = code;
                        currentTypeName = field.name;
                    }
                    if(group === 'channels' && field.type){
                        currentType = field.type;
                    }
                    typeTable.reload();
                    channelTable.reload({where: {type: currentType}});
                }
                if(callback)callback(true);
            } else {
                layer.msg(res.msg, {icon: 2});
                if(callback)callback(false);
            }
        }, 'json');
    }
});
</script>
</body>
</html>
