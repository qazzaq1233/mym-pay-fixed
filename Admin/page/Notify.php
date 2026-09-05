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
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
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
            url: '../Ajax.php?act=Notify',
            page: true,
            cellMinWidth: 100,
            cols: [[
                {field: 'trade_no', title: '系统订单号', width: 220},
                {field: 'pid', title: '商户PID', width: 120},
                {field: 'type', width: 110, title: '支付方式', templet: function (d) {
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
                {field: 'money', title: '收款金额', width: 100},
                {field: 'ms', title: '回调速度', width: 100, templet: function (d) {
                    if(d.ms < 999){
                        return '<font color=green>  '+d.ms+'s</font>';
                    }else{
                        return '<font color=red>异常</font>';
                    }
                }},
                {field: 'pay_msg', title: '回调状态', width: 100 ,templet: function (d) {
                    if(d.pay_msg =='success'){
                        return '<font color=green>回调成功</font>';
                    }else{
                        return '<font color=red>回调失败</font>';
                    }
                }},
                {field: 'addtime', title: '收款时间(系统检测时间)', width: 220},
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
