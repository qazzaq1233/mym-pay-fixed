<?php

$title='支付通道管理';
include './Head.php';
$numrows=$DB->query("SELECT * from pay_qrlist WHERE `pid`='{$userrow['pid']}'")->rowCount();
$sql=" `pid`='{$userrow['pid']}'";
$con='共有 <b>'.$numrows.'</b> 条记录';
$payTypeList = mym_pay_type_list(true);
if(empty($payTypeList))$payTypeList = mym_pay_type_list(false);
$defaultPayType = key($payTypeList);
$payChannelList = array();
foreach($payTypeList as $code=>$item){
    $payChannelList[$code] = mym_pay_channel_list($code, true);
}
?>	

<!-- End Page Header --> 
	<div class="row">
		<div class="col-sm-12">
			<div class="page-title-box">
				<div class="btn-group float-right">
					<ol class="breadcrumb hide-phone p-0 m-0">

					</ol>
				</div>
				<h4 class="page-title"><?=$title?></h4>
			</div>
		</div>
	</div>
<!-- End Page Header --> 
<?php
if (!$user_pass) {
?>
    <div class="row">
        <div class="col-md-12 col-lg-12 col-xl-6">
            <div class="card bg-white m-b-30">
                <div class="card-body new-user">
                    <h4 class="header-title mb-4 mt-0">请先验证二级密码</h4>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>本页面需要验证二级密码才可访问</label>
                                <input type="text" id="pay_pass" name="pay_pass" placeholder="请输入您绑定的二级密码" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-block btn-primary" onclick="pay_pass();">确定</button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
<?php
}else{
?>
 <div class="row">
    <div class="col-lg-12">
        <div class="card mym-toolbar-card">
            <div class="card-body">
           <div class="form-group mb-2">    
              <div class="mym-page-toolbar">
               <div><h4 class="mb-1">通道列表</h4><p class="mb-0 text-muted">管理所有收款账号，可单独测试、设置姓氏、开启或关闭。</p></div>
               <div class="mym-toolbar-actions"><button data-toggle="modal"  class="btn btn-gradient-info waves-effect waves-light" href="#modalHeaderColorInfo" data-target="#modalHeaderColorInfo" data-id="modalHeaderColorInfo"><i class="fa fa-plus"></i> 新增通道 (当前：<a style="color: blue;"><?=$DB->query("SELECT * from pay_qrlist WHERE `pid`='{$userrow['pid']}'")->rowCount()?></a>/最大：<a style="color: blue;"><?=$userrow['type']?></a>)</button><button type="button" class="btn btn-outline-success waves-effect waves-light"><i class="mdi mdi-check mr-2"></i><?php echo $con;?></button></div>
				<a data-toggle="modal" class="modal-basic" href="#modalHeaderColorInfo_Up_Qr_modal" data-target="#modalHeaderColorInfo_Up_Qr_modal" data-id="modalHeaderColorInfo_Up_Qr_modal" id="Up_Qr_modal"></a>
              </div>
             </div> 
           </div><!-- end col-->
           
    <div class="card-body">
    <form name="form1" id="form1">
	  <div class="table-responsive">
        <table class="table table-bordered text-nowrap">
               
          <thead></thead><tr><th>#</th><th>数据</th><th>更新时间/运行时间</th></tr></thead>
		<tbody>
<?php
$pagesize=30;
$pages=intval($numrows/$pagesize);
if ($numrows%$pagesize)
{
 $pages++;
 }
if (isset($_GET['page'])){
$page=intval($_GET['page']);
}
else{
$page=1;
}
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM pay_qrlist WHERE{$sql} and hook_type!=3 order by addtime desc limit $offset,$pagesize");
while($res = $rs->fetch())
{
    $name = explode('|',$res['data_data']);
    $qr_json = json_decode($res['json'], true);
    if(!is_array($qr_json))$qr_json = array();
    $receiver_surname = isset($qr_json['receiver_surname']) ? mym_restore_unicode_text($qr_json['receiver_surname']) : '';
    $receiver_surname_text = $receiver_surname!=='' ? '姓：'.htmlspecialchars($receiver_surname) : '姓：未设置';
    $receiver_surname_js = htmlspecialchars(json_encode($receiver_surname, JSON_UNESCAPED_UNICODE), ENT_QUOTES);
    if($res['qr_status']==1){
        $qr_status = '已开';
    }else{
        $qr_status = '已关';
    }
    $data = cookie_zt($res);
    if($data['status']){
        $jstime = jstime($res['addtime'],3);
    }else{
        $jstime = $res['addtime'];
    }
	echo '<tr><td><b><a href="javascript:showQrlist(\''.$res['id'].'\')" title="点击查看详情">'.$res['id'].'</a></b><br/><br/><font color="green">'.type_yun($res).'</font></td>
	<td><img src="/Mym/Assets/Icon/'.$res['type'].'.ico" width="16" onerror="this.style.display=\'none\'">'.pay_type($res).'<br/>'.htmlspecialchars($res['beizhu']).'<br/><span style="color:#0d6efd;">'.$receiver_surname_text.'</span><br/>'.WxMoney($res).'</br><a href="javascript:showQrlist(\''.$res['id'].'\')" title="点击查看详情">查看详细</a></td>
	<td>'.$data['msg'].'<br/><font color=#00E3E3>'.$name[1].'</font><br/>'.$jstime.'</br>';
	if($res['hook_type']!=1 or $res['channel']=='pc_alijk'){
	    echo '<a href="#Up_Qr_modal" onclick="Get_Qr('.$res['id'].');" class="btn btn-xs btn-success"><i class="fa fa-arrow-circle-o-up"></i> 更新</a>';
	}
	echo '
	&nbsp;<a onclick="Set_Receiver_Surname('.$res['id'].','.$receiver_surname_js.');" class="btn btn-xs btn-primary"><i class="fa fa-user"></i> 设置姓</a>
	&nbsp;<a onclick="Test_Qr('.$res['id'].');" class="btn btn-xs btn-info"><i class="fa fa-play-circle"></i> 测试通道</a>
	&nbsp;<a onclick="Del_Qr_status('.$res['id'].');" class="btn btn-xs btn-warning"><i class="fa fa-trash-o"></i> '.$qr_status.'</a>
	&nbsp;<a onclick="Del_Qr('.$res['id'].');" class="btn btn-xs btn-warning"><i class="fa fa-trash-o"></i> 删除</a>
	</td>
	</td>
	<tr>';
}
?>
        </table>
        </tbody>
    </div>
  </form>										<nav style="float: inline-end;">
<?php
echo'<ul class="pagination">';
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1)
{
echo '<li class="page-item"><a class="page-link" href="?page='.$first.$link.'">首页</a></li>';

} else {
echo '<li class="page-item"><a class="page-link">首页</a></li>';

}
for ($i=1;$i<$page;$i++)
echo '<li class="page-item"><a class="page-link" href="?page='.$i.$link.'">'.$i .'</a></li>';
echo '<li class="page-item active"><a class="page-link">'.$page.'</a></li>';
if($pages>=10)$s=10;
else $s=$pages;
for ($i=$page+1;$i<=$s;$i++)
echo '<li class="page-item"><a class="page-link" href="?page='.$i.$link.'">'.$i .'</a></li>';
echo '';
if ($page<$pages)
{

echo '<li class="page-item"><a class="page-link" href="?page='.$last.$link.'">尾页</a></li>';
} else {

echo '<li class="page-item"><a class="page-link">尾页</a></li>';
}
echo'</ul>';
#分页
?>
<!-- Modal Info -->
<div class="modal fade bs-example-modal-center"   id="modalHeaderColorInfo" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">添加支付通道</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
		    <div class="modal-body">
		        <div class="form-group">
		            <label>支付类型:</label><br>
		            <select class="form-control" name="type" id="type">
		                <?php foreach($payTypeList as $code=>$item){ ?>
		                <option value="<?=htmlspecialchars($code)?>"><?=htmlspecialchars($item['name'])?></option>
		                <?php } ?>
		            </select>
		        </div>
<div id="ChannelTypeL">
    <div class="form-group">
        <label>通道类型:</label><br>
        <select class="form-control" name="channel" id="channel"></select>
    </div>
</div>
<div id="frame_ali" style="display:none;">
	<div class="form-group">
	    <label>选择二维码(剪切好边框再上传哦):</label><span class="glyphicon glyphicon-qrcode"></span>
	    <label for="file"></label><input type="file" id="imgfile" accept="image/*" multiple>
	</div>
</div>
<div id="frame_url" style="">
	<div class="form-group">
	    <label id="url_name">支付宝收款码链接:</label><br>
	    <input type="text" class="form-control" id="qr_url" value="" placeholder="请粘贴支付宝收款码链接，如 alipays://、alipayqr:// 或 https:// 开头的链接" name="qr_url">
	    <small id="url_tips" class="form-text text-muted">填写后发起支付只使用该收款码链接；通道仍按原流程扫码登录 CK 并保持在线。</small>
	</div>
</div>
<div id="frame_custom_qr_url" style="display:none;">
	<div class="form-group">
	    <label id="custom_url_name">自定义收款码链接:</label><br>
	    <input type="text" class="form-control" id="custom_qr_url" value="" placeholder="可选：填写后该通道发起订单将直接使用此收款码链接" name="custom_qr_url">
	    <small id="custom_url_tips" class="form-text text-muted">可选填写：填写后发起支付只使用该收款码链接；通道仍需扫码登录 CK，并保持在线。</small>
	</div>
</div>
<div id="Login_display" style="display:none;">
    <div class="form-group">
        <label >选择免挂/登录服务器:</label><br>
        <select class="form-control" name="Login_Type" id="Login_Type">
        </select>
    </div>
</div>
<div class="form-group">
<label id="beizhu_name">备注:</label><br>
<input type="text" class="form-control" id="beizhu" value="" placeholder="" name="beizhu">
</div>
<div class="form-group">
<label>收款人姓:</label><br>
<input type="text" class="form-control" id="receiver_surname" value="" maxlength="10" placeholder="可选：填写后支付页显示，如：群、张、李" name="receiver_surname">
<small class="form-text text-muted">可选填写，不填则支付页面不显示“姓：”。每个通道可单独设置。</small>
</div>
<div class="form-group">
<label id="LoginQrcode_msg">确保所有信息填写正确哦</label><br><div id="LoginQrcode">
</div></br>
<a id="Wx_Sumbit"><button type="sumbit" class="btn btn-primary btn-block" onclick="Add_Qr();">确认以上并添加</button></a>			
				</div>
			  </div>
			</div>
         </div>
       </div>
    </div>
  <!-- Modal Info -->
</div>
            
	
                                                </nav>
                                        </div> <!-- end table-responsive-->
                                    </div> <!-- end card body-->
                                </div> <!-- end card -->
                            </div><!-- end col-->
                        </div>
                    </div>
                </div>


		<script>
	
function showQrlist(id) {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	var status = ['<span class="label label-primary">未支付</span>','<span class="label label-success">已支付</span>','<span class="label label-red">已退款</span>'];
	$.ajax({
		type : 'GET',
		url : 'Ajax.php?act=Qrlist&id='+id,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var data = data.data;
				var item = '<table class="table table-condensed table-hover" id="orderItem">';
				item += '<tr><td class="info">监控金额</td class="orderTitle"><td colspan="5" class="orderContent">'+data.money+'</a></td>';
				item += '<tr class="orderTitle"><td class="info" class="orderTitle">二维码类型</td><td colspan="5" class="orderContent">'+data.type+'</td></tr>';
				item += '<tr><td class="info" class="orderTitle">总订单详细</td><td colspan="5" class="orderContent">'+data.ali_order+'</td></tr>';
				item += '<tr><td class="info" class="orderTitle">今日详细</td><td colspan="5" class="orderContent">'+data.jr_order+'</td></tr>';
				item += '<tr><td class="info" class="orderTitle">昨日详细</td><td colspan="5" class="orderContent">'+data.zr_order+'</td></tr>';
				item += '</table>';
				var area = [$(window).width() > 480 ? '480px' : '100%'];
				layer.open({
				  type: 1,
				  area: area,
				  title: '二维码详细信息',
				  skin: 'layui-layer-rim',
				  content: item
				});
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
type_ak=0;
type_akb = '';
show = '';
var defaultPayType = <?=json_encode($defaultPayType)?>;
var payChannelOptions = <?=json_encode($payChannelList, JSON_UNESCAPED_UNICODE)?>;

function updateQrUrlField(){
    var payType = $("#type").val();
    var channel = $("#channel").val() || '';

    $("#frame_custom_qr_url").hide();
    $("#custom_url_tips").hide();

    if(payType=='alipay' && channel=='mg_ali'){
        $("#frame_url").hide();
        $("#url_tips").hide();
        orwxpay('');
        $("#frame_custom_qr_url").show();
        $("#custom_url_name").html('支付宝收款码链接:');
        $("#custom_qr_url").attr('placeholder','请粘贴支付宝收款码链接，如 alipays://、alipayqr:// 或 https:// 开头的链接');
        $("#custom_url_tips").html('可选填写：填写后发起支付只使用该支付宝收款码链接；通道仍需扫码登录 CK，并保持在线。').show();
    }else if(payType=='wxpay' && channel=='mg_wx'){
        $("#frame_custom_qr_url").show();
        $("#custom_url_name").html('微信收款码链接:');
        $("#custom_qr_url").attr('placeholder','可选：粘贴微信收款码链接，填写后订单直接走该收款码');
        $("#custom_url_tips").html('可选填写：填写后发起支付只使用该微信收款码链接；通道仍需扫码登录 CK，并保持在线。').show();
    }else if(payType=='qqpay' && channel=='mg_qq'){
        $("#frame_custom_qr_url").show();
        $("#custom_url_name").html('QQ钱包收款码链接:');
        $("#custom_qr_url").attr('placeholder','可选：粘贴QQ钱包收款码链接，填写后订单直接走该收款码');
        $("#custom_url_tips").html('可选填写：填写后发起支付只使用该QQ钱包收款码链接，不再自动生成QQ金额码；通道仍需扫码登录 CK，并保持在线。').show();
    }else if(payType=='usdt'){
        $("#frame_url").show();
        $("#url_name").html('USDT-TRC20地址:');
        $("#qr_url").attr('placeholder','请输入USDT-TRC20收款地址');
        $("#url_tips").html('请确认链类型为 TRC20。').show();
    }else{
        $("#frame_url").hide();
        $("#url_tips").hide();
    }
}

function updatePayTypePanel(payType){
    var list = payChannelOptions[payType] || {};
    var html = '';
    $.each(list, function(code, item){
        html += '<option value="'+code+'">'+item.name+'</option>';
    });
    $("#channel").html(html);
    if(html==''){
        $("#ChannelTypeL").hide();
    }else{
        $("#ChannelTypeL").show();
    }
    jschange(payType);
    updateQrUrlField();
}

if(defaultPayType){
    $("#type").val(defaultPayType);
}
updatePayTypePanel($("#type").val());

$("select[name='type']").change(function(){
    type_akb = $(this).val();
    updatePayTypePanel(type_akb);
	console.log("type_ak=="+type_ak+" && type_akb=='"+type_akb+"'")
});

$("select[name='channel']").change(function(){
    var payType = $("#type").val();
    var channel = $(this).val() || '';
    if(payType=='wxpay'){
        if(channel=='mg_vzq' || channel=='yd_wx' || channel=='yd_wx_sskd' || channel=='yd_wx_gskd'){
            if(channel=='yd_wx_sskd' || channel=='yd_wx_gskd' || channel=='yd_wx'){
                Login_Type(channel);
            }
            $("#frame_ali").hide();//关闭
            $("#beizhu_name").html('备注'); //输出提示
            orwxpay('');
        }else if(channel=='yd_vzq'){
            Login_Type(channel);
            $("#frame_ali").hide();//关闭
            $("#beizhu_name").html('QQ账号'); //输出提示
            orwxpay('');
        }else if(channel=='yd_wx_uos'){
            Login_Type(channel);
            orwxpay(1);
            $("#frame_ali").show();//展开
            $("#beizhu_name").html('备注'); //输出提示
        }else{
            $("#Login_display").hide();
            $("#frame_ali").show();//展开
            $("#beizhu_name").html('微信昵称'); //输出提示
            orwxpay(1);
        }
    }else if(payType=='alipay'){
        $("#Login_display").hide();
        if(channel=='mg_alimp'){
            $("#frame_ali").show();//展开
            $("#beizhu_name").html('备注'); //输出提示
            orwxpay('');
        }else{
            $("#frame_ali").hide();//关闭
            $("#beizhu_name").html('备注'); //输出提示
            orwxpay('');
        }
    }else if(payType=='qqpay'){
        if(channel=='yd_qq'){
            Login_Type(channel);
        }else{
            $("#Login_display").hide();
        }
    }else{
        $("#Login_display").hide();
        $("#frame_ali").hide();
        orwxpay('');
    }
    updateQrUrlField();
});

function jschange(type_akb){
    if(type_akb=='alipay'){
        $("#Login_display").hide();
        $("#frame_ali").hide();//关闭
    }else if(type_akb=='qqpay'){
        $("#frame_ali").hide();//关闭
    }else if(type_akb=='wxpay'){
	    $("#frame_ali").show();//关闭
	}else if(type_akb=='usdt'){
	    $("#frame_ali").hide();//关闭
	    $("#Login_display").hide();
	}else{
	    $("#frame_ali").hide();//关闭
	    $("#Login_display").hide();
	}
	updateQrUrlField();
	setnume(type_akb);
}


function setnume(type_akb){
    var wxChannel = $("#channel").val() || '';
    if(type_akb=='wxpay' && wxChannel!='mg_vzq' && wxChannel!='yd_vzq' && wxChannel.indexOf('yd_')!==0){
        orwxpay(1);
    }else{
        orwxpay('');
    }
    if(type_akb=='wxpay'){
        $("#beizhu_name").html('微信昵称'); //输出提示
        $("#url_name").html('二维码地址:');
    }else if(type_akb=='qqpay'){
        $("#beizhu_name").html('QQ账号'); //输出提示
    }else if(type_akb=='usdt'){
        $("#beizhu_name").html('备注'); //输出提示
        $("#url_name").html('USDT-TRC20地址:');
    }else{
        $("#beizhu_name").html('备注'); //输出提示
    }
}

function orwxpay(i){
    if(i==''){
        $("#Wx_Sumbit").html('</br><button type="sumbit" class="btn btn-primary btn-block" onclick="Add_Qr();">确认以上并添加</button>');
    }else{
        $("#Wx_Sumbit").html('</br><button type="sumbit" class="btn btn-primary btn-block" onclick="GET_wx_QR();">确认以上并添加</button>');
    }
}

function pay_pass(){//POST提交
    var pay_pass= $("#pay_pass").val();
    var ii = layer.load(3, {shade:[0.1,'#fff']});
    $.ajax({
        type : "POST",
        url : "Ajax.php?act=Pay_pass",
        data : {pay_pass},
        dataType : 'json',
        timeout:10000,
        success : function(data) {
            layer.close(ii);
            layer.msg(data.msg);
            if(data.code==1){
                setTimeout(function () {
                    location.reload();
                }, 1000); //延时1秒跳转
            }else if(data.code==0){
                setTimeout(function () {
                    window.location.href = 'userinfo.php';
                }, 1000); //延时1秒跳转
            }
        },error:function(data){
            layer.close(ii);
            layer.msg('服务器错误');
        }
    });
}

function Login_Type(channel){//POST提交
    $("#Login_display").show();//展开
    var ii = layer.load(3, {shade:[0.1,'#fff']});
    $.ajax({
        type : "POST",
        url : "Ajax3.php?act=Login_Type",
        data : {channel},
        dataType : 'json',
        timeout:10000,
        success : function(data) {
            layer.close(ii);
            $('#Login_Type').html(data.html);
        },error:function(data){
            layer.close(ii);
            layer.msg('服务器错误');
        }
    });
}
		</script>
<?php } include './foot.php'; ?>
<div class="modal fade bs-example-modal-center"   id="modalHeaderColorInfo_Up_Qr_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabelL">选择登录方式</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="html">
                    <input type="hidden" name="Up_id" id="Up_id" value=""/>
                    <div class="form-group">
                        <label id="Up_LoginQrcode_msg">请用对应二维码的支付宝或QQ扫码哦</label><br>
                        <div id="Up_LoginQrcode"></div>
                        <a id="Up_Wx_Sumbit"></a>
                    </div>
                </div>
                <div id="html2" style="display:none;">
                </div>
                <div id="AliLogin" style="display:none;">
                    <form name="loginForm">
                        <fieldset>
                            <div class="sl-error" id="J-errorBox" errortype="">
                                <span class="sl-error-text"></span>
                            </div>
                            <div class="form-group">
                                <label id="url_name">邮箱地址/手机号码:</label><br>
                                <input type="text" id="J-input-user" class="form-control" name="logonId" tabindex="1" value="" autocomplete="off" maxlength="100" placeholder="邮箱地址/手机号码" seed="authcenter-input-account" data-widget-cid="widget-3" data-explain="">
                            </div>
                            <div class="form-group">
                                <label id="beizhu_name">密码:</label><br>
                                <input type="password" id="password_rsainput" tabindex="2" name="password_rsainput" class="form-control" oncontextmenu="return false" onpaste="return false" oncopy="return false" oncut="return false" autocomplete="off" value="">
                            </div>
                            <div class="ui-form-item ui-form-item-30pd" id="J-submit">
                                <a id="login" type="sumbit" class="btn btn-primary btn-block" onclick="AliLogin();">登 录</a>
                            </div>
                        </fieldset>
                        <input id="n" name="n" type="hidden" value="">
                    </form>
                </div>
                <div id="AliLogin2" style="display:none;">
                    <form name="loginForm">
                        <fieldset>
                            <div class="sl-error" id="J-errorBox" errortype="">
                                <span class="sl-error-text"></span>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">手机号</div>
                                <input type="text" id="phone" class="form-control" onkeydown="" required="" readonly>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">验证码</div>
                                <input type="text" id="smscode" class="form-control" onkeydown="" placeholder="输入短信验证码">
                            </div>
                            <div class="ui-form-item ui-form-item-30pd" id="J-submit">
                                <a id="login" type="sumbit" class="btn btn-primary btn-block" onclick="AliLogin_Sms();">验证验证码</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div id="AliYunGet" style="display:none;">
                    <p style="text-align: center; font-size: 20px;">自定义配置</p>
                    <div class="input-group">
                        <div class="input-group-addon">应用AppID:</div>
                        <input type="text" id="appid" value="" class="form-control" required/>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">应用私钥:</div>
                        <input type="text" id="appkey2" value="" class="form-control" required/>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">支付宝收款码链接:</div>
                        <input type="text" id="yun_qr_url" value="" class="form-control" placeholder="请粘贴 alipays://、alipayqr:// 或 https:// 开头的收款码链接" required/>
                    </div>
                    <div class="input-group">
                        <div class="input-group-addon">当前账户余额:</div>
                        <input type="text" id="ali_balance" value="" class="form-control" readonly placeholder="保存配置或定时任务成功后自动获取"/>
                    </div>
                    <small class="form-text text-muted" style="margin:5px 0 10px 0;display:block;">余额通过 AppID + 应用私钥调用支付宝官方余额接口获取；接口无权限或签名失败时不会显示。</small>
                    <div class="form-group" style="margin-top:10px;">
                        <label><span style="color:red;">*</span> 订单检查方式</label>
                        <input type="hidden" id="ali_order_check" value="order_amount"/>
                        <div class="btn-group btn-block" role="group" aria-label="订单检查方式">
                            <button type="button" class="btn btn-primary ali-order-check-btn" data-value="order_amount" onclick="setAliOrderCheck('order_amount')">订单号匹配不到则使用金额</button>
                            <button type="button" class="btn btn-outline-primary ali-order-check-btn" data-value="order_no" onclick="setAliOrderCheck('order_no')">订单号</button>
                        </div>
                    </div>
                    <small class="form-text text-muted" style="margin:5px 0 10px 0;display:block;">推荐选择左侧：优先按账单备注中的订单号匹配，匹配失败再按收款金额匹配未支付订单。</small>
                    <input type="submit" id="save" onclick="saveInfo()" class="btn btn-primary btn-block" value="保存">
                </div>
                <div id="AliYunSms" style="display:none;">
                    <form name="loginForm">
                        <fieldset>
                            <div class="sl-error" id="J-errorBox" errortype="">
                                <span class="sl-error-text"></span>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">应用APPID</div>
                                <input type="text" id="appid6" class="form-control" onkeydown="" required="" readonly>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">手机号</div>
                                <input type="text" id="phone2" class="form-control" onkeydown="" required="" readonly>
                            </div>
                            <div class="input-group">
                                <div class="input-group-addon">验证码</div>
                                <input type="text" id="smscode2" class="form-control" onkeydown="" placeholder="输入短信验证码">
                            </div>
                            <div class="ui-form-item ui-form-item-30pd" id="J-submit">
                                <a id="login" type="sumbit" class="btn btn-primary btn-block" onclick="AliYunSms();">验证验证码</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
        </div>
	  </div>
	</div>
  </div>
</div>
<script src="User_Js/channel.js"></script>
<script type="text/javascript" src="./User_Js/Lquery.min.js"></script>