$("#Sumbit").html('</br><button type="sumbit" class="btn btn-primary btn-block" onclick="Add_Qr();">确认以上并添加</button>');

function Del_Qr(id) { //删除二维吗
    var confirmobj = layer.confirm('此操作将会删除此数据，是否确定？', {
            btn: ['确定', '取消']
        },
        function() {
            var ii = layer.load(2, {
                shade: [0.1, '#fff']
            });
            $.ajax({
                type: 'POST',
                url: "Ajax.php?act=Del_Dmf",
                data: {
                    id
                },
                dataType: 'json',
                success: function(data) {
                    layer.close(ii);
                    if (data.code == 1) {
                        layer.alert(data.msg, {
                                icon: 1
                            },
                            function() {
                                location.href = "?";
                            });
                    } else {
                        layer.alert(data.msg);
                    }
                },
                error: function(data) {
                    layer.msg('服务器错误');
                    return false;
                }
            });
        },
        function() {
            layer.close(confirmobj);
        });
}

function Add_Qr() { //添加
    var f2fid = $("#f2fid").val();
    var f2fkey = $("#f2fkey").val();
    var f2fpublic = $("#f2fpublic").val();
    var beizhu = $("#beizhu").val();
    var ii = layer.load(5, {
        shade: [0.1, '#fff']
    });
    $.ajax({
        type: "POST",
        url: "Ajax.php?act=Add_Dmf",
        data: {
            f2fid,
            f2fkey,
            f2fpublic,
            beizhu
        },
        dataType: 'json',
        timeout: 15000,
        //ajax请求超时时间15s
        success: function(data) {
            layer.close(ii);
            if (data.code == 1) {
                layer.msg(data.msg);
                setTimeout(function() {
                        location.href = "?";
                    },
                    3000); //延时1秒跳转
            } else if (data.code == -2) {
                layer.msg(data.msg);
            } else {
                layer.alert(data.msg, { icon: 2 }, function() { location.href = "./"; }); //跳转
            }
        },
        error: function(data) {
            layer.close(ii);
            layer.msg('操作失败,服务器错误');
        }
    });
}