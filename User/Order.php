<?php
/**
 * 订单列表
**/
$title='订单列表';
include './Head.php';
$my=isset($_GET['my'])?$_GET['my']:$_POST['my'];

if($my=='search') {
    $column = trim(daddslashes($_GET['column']));
    $value = trim(daddslashes($_GET['value']));
	$sql=" `{$column}`='{$value}'";
	$numrows=$DB->query("SELECT * from pay_order WHERE `pid`='{$userrow['pid']}' and{$sql}")->rowCount();
	$con='包含 '.$_GET['value'].' 的共有 <b>'.$numrows.'</b> 条记录';
	$link='&my=search&column='.$column.'&value='.$value;
}elseif($my=='date_search'){//搜索
	$adddate = trim(daddslashes($_POST['adddate']));
	$enddate = trim(daddslashes($_POST['enddate']));
	$sql=" addtime>='{$adddate}' and addtime<='{$enddate}'";
	$numrows=$DB->query("SELECT * from pay_order WHERE `pid`='{$userrow['pid']}' and{$sql}")->rowCount();
	$con='这个时间段共有 <b>'.$numrows.'</b> 条记录';
	$link='&my=date_search&adddate='.$adddate.'&enddate='.$enddate;
}else{
	$numrows=$DB->query("SELECT * from pay_order WHERE `pid`='{$userrow['pid']}' and 1")->rowCount();
	$sql=" `pid`='{$userrow['pid']}'";
	$con='共有 <b>'.$numrows.'</b> 条记录';
}
?>
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
                                    <div class="card">
                                        <div class="card-body">
                <div>
                <form action="" method="GET" class="form-inline"><input type="hidden" name="my" value="search">
                     <div class="form-group"><select name="column" class="form-control"><option value="out_trade_no">商户订单</option><option value="trade_no">订单号</option><option value="pid">商户号</option><option value="name">商品名称</option><option value="money">金额</option><option value="type">支付方式</option></select></div>
                        <div class="form-group"><input type="text" class="form-control" name="value" placeholder="搜索内容"></div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">条件搜索</button>
                </form>
                
 
	
		<button type="button" class="btn btn-outline-success waves-effect waves-light"><i class="mdi mdi-check mr-2"></i><?php echo $con;?></button>
      </div>
      </div>
<div class="block">
	<form name="form1" id="form1">
	  <div class="table-responsive">
        <table class="table table-bordered text-nowrap">
        <thead><tr><th>交易号/订单号</th><th>名称</th><th>金额/浮动</th><th>二维码ID/备注</th><th>创建/完成</th><th>方式/状态</th><th>操作</th></tr></thead>
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

$rs=$DB->query("SELECT * FROM pay_order WHERE `pid`='{$userrow['pid']}' and{$sql} order by addtime desc limit $offset,$pagesize");
while($res = $rs->fetch())
{
	$row=$DB->query("SELECT * FROM `pay_qrlist` WHERE `pid`='{$userrow['pid']}' and `id`='{$res['qr_id']}' limit 1")->fetch();
	echo '<tr><td>'.$res['trade_no'].'<br/>'.$res['out_trade_no'].'</td>
	<td>'.$res['name'].'</td>
	<td><b>￥ '.$res['money'].'<br/>￥ '.$res['price'].'</b></td>
	<td>['.hook_type($row).']->'.$res['qr_id'].'→'.($row['beizhu']?$row['beizhu']:'无备注').'<br/>['.price_zt($res).']</td>
	<td>'.$res['addtime'].'<br/>'.$res['endtime'].'</td>
    <td><img src="/Mym/Assets/Icon/'.$res['type'].'.ico" width="16" onerror="this.style.display=\'none\'">'.pay_type($res).'<br>'.order_zt($res).'</td>';?>
	<td><a onclick="Instant_Notify('<?=$res['trade_no']?>');" class="btn btn-danger btn-xs">补单回调</a></td> 
	<?php echo '</tr>';
}
?>
            </tbody>
        </table>

          
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
    </div>
  </div>
  <script type="text/javascript">
  function Instant_Notify(trade_no){//补单并给回调
		if(trade_no==''){layer.alert('请确保各项不能为空！');return false;}
		  	var ii = layer.load(3, {shade:[0.1,'#fff']});
		  	$.ajax({
				type : "POST",
				url : "./Ajax.php?act=Pay_Notify",
				data : {trade_no:trade_no},
				dataType : 'json',
				timeout:15000,
				success : function(data) {					  
					  layer.close(ii);
					  layer.msg(data.msg);
					  if(data.code==1){
						setTimeout(function () {
							location.href="./Order.php";
						}, 1000); //延时1秒跳转
					  }
				},
				error:function(data){
					layer.close(ii);
					layer.msg('服务器错误');
					}
			});
		  	
}
  </script>
  <?php } include './foot.php'; ?>