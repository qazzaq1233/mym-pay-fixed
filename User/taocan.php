<?php
$title='购买套餐';
include './Head.php';
$pack=$DB->query("SELECT * FROM `pay_taocan` WHERE `id`='{$userrow['user_vip']}' limit 1")->fetch();
?>
<link rel="stylesheet" href="css/style.css">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>

	<div id="content" class="app-content" role="main">
    <div class="app-content-body ">
      	</div>
							</div>
<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
			<div class="btn-group float-right">
				<ol class="breadcrumb hide-phone p-0 m-0">
				</ol>
			</div>
			<h4 class="page-title">购买套餐</h4>
		</div>
	</div>
</div>
<div class="wrapper-md control">
	<div class="row">
		<div class="col-12">
			<div class="card">
			    
	    <div class="col-lg-12" id="infoFrame" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display:none;">
	<div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 center-block" style="float: none;">
	<button class="btn btn-default btn-block" onclick="back()"><i class="fa fa-reply"></i>&nbsp;返回列表</button>
	<div class="panel panel-default">
		<div class="panel-heading font-bold">
			<i class="fa fa-shopping-cart"></i>&nbsp;购买会员
		</div>
		<div class="panel-body">
        <form class="form-horizontal devform">
            <input type="hidden" name="group_id" value="">
				<div class="form-group">
					<label class="col-sm-3 control-label">会员等级</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="group_name" value="" readonly="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">售价</label>
					<div class="col-sm-8">
						<input class="form-control" type="text" name="group_price" value="" readonly="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">支付方式</label>
					<div class="col-sm-8">
						<div class="radio">
						<label class="i-checks"><input type="radio" name="type" value="alipay"><i></i><img src="/Mym/Assets/Icon/alipay.ico" width="18px" title="-1">支付宝</label>&nbsp;
						<label class="i-checks"><input type="radio" name="type" value="qqpay"><i></i><img src="/Mym/Assets/Icon/qqpay.ico" width="18px" title="-1">QQ钱包</label>&nbsp;
						<label class="i-checks"><input type="radio" name="type" value="wxpay"><i></i><img src="/Mym/Assets/Icon/wxpay.ico" width="18px" title="-1">微信</label>&nbsp;
						<label class="i-checks"><input type="radio" name="type" value="usdt"><i></i><img src="/Mym/Assets/Icon/usdt.ico" width="18px" title="-1">USDT</label>&nbsp;
						</div>
					</div>
				</div>
				<div class="form-group">
				  <div class="col-sm-offset-3 col-sm-8"><input type="button" id="submit" value="立即购买" class="btn btn-success form-control"/><br/>
				 </div>
				</div>
			</form>
		</div>
	</div>
	</div>
	</div>
</div>
							<!-- end page title end breadcrumb -->
						 <div class="row" id="listFrame">
                                <div class="col-lg-12">
                                    <div class="card">
                                        
										    
	<div class="list-group-item">
		<div class="panel-body">
		<div class="line line-dashed b-b line-lg pull-in"></div>
<section id="pricePlans">
    <ul id="plans">
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

$rs=$DB->query("SELECT * FROM pay_taocan order by sort ASC limit $offset,$pagesize");
while($res = $rs->fetch())
{
    if($res['status']==0){
		$button = '<li class="button"><a><font color="red">售停</font></a></li>';
    }else{
    	$button = '<li class="button"><a href="javascript:buy('.$res['id'].')">立即购买</a></li>';
    }
    if($res['time']==1){
        $time = '一个月';
    }elseif($res['time']==3){
        $time = '三个月</option>';
    }elseif($res['time']==6){
        $time = '半年';
    }else{
        $time = '一年';
    }
	echo'<li class="plan">
			<ul class="planContainer">
				<li class="title"><h2>'.$res['name'].'</h2></li>
				<li class="price"><p>'.$res['money'].'￥/<span>RMB</span></p></li>
				<li>
					<li>额度 <span>'.$res['edu'].' ￥</span></li>
					<li>有效期： <span>'.$time.'</span></li>
					<li><font color="red">'.$res['text'].'</font></li>
				</li>
					'.$button.'
			</ul>
		</li>';
					
					
}
?>
    </ul> 
</section>
	</div>
	</div>
	</div>
	</div>
	</diy>

<script>
function buy(id){
	var ii = layer.load();
	$.ajax({
		type: "POST",
		dataType: "json",
		data: {id:id},
		url: "Ajax2.php?act=taocan",
		success: function (data, textStatus) {
			layer.close(ii);
			if (data.code == 0) {
				$("input[name='group_id']").val(id);
				$("input[name='group_name']").val(data.name);
				$("input[name='group_price']").val(data.money);
				$("#listFrame").slideUp();
				$("#infoFrame").slideDown();
			}else{
				layer.alert(data.msg, {icon: 0});
			}
		},
		error: function (data) {
			layer.msg('服务器错误', {icon: 2});
		}
	});
}

function back(){
	$("#listFrame").slideDown();
	$("#infoFrame").slideUp();
}
$(document).ready(function(){
	$("input[name=type]:first").attr("checked",true);
	$("#submit").click(function(){
		var csrf_token=$("input[name='csrf_token']").val();
		var id=$("input[name='group_id']").val();
		var typeid=$("input[name=type]:checked").val();
		var ii = layer.load();
		$.ajax({
			type: "POST",
			dataType: "json",
			data: {id:id, type:typeid, csrf_token:csrf_token},
			url: "Ajax2.php?act=taocan",
			success: function (data, textStatus) {
				layer.close(ii);
				if (data.code == 0) {
					window.location.href=data.url;
				}else if (data.code == 1) {
					layer.alert(data.msg, {icon: 1}, function(){ window.location.reload() });
				}else{
					layer.alert(data.msg, {icon: 2});
				}
			},
			error: function (data) {
				layer.msg('服务器错误', {icon: 2});
			}
		});
		return false;
	})
});
</script>
<?php include './foot.php'; ?>