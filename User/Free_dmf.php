<?php
/**
 * 码子管理
**/
$title='当面付管理';
include './Head.php';
$numrows=$DB->query("SELECT * from pay_dmf WHERE `pid`='{$userrow['pid']}'")->rowCount();
$sql=" `pid`='{$userrow['pid']}'";
$con='共有 <b>'.$numrows.'</b> 条记录';
?>	
<!-- Modal Info -->
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
<!-- End Page Header --> 
<div class="block">
<div class="row">
  <div class="col-lg-12">
    <div class="card">
       <div class="card-body">
        <div class="form-group mb-2">    
              <div>
	            <button data-toggle="modal"  class="btn btn-gradient-info waves-effect waves-light" href="#modalHeaderColorInfo" data-target="#modalHeaderColorInfo" data-id="modalHeaderColorInfo"><i class="fa fa-plus"></i> 新增通道 </button><button type="button" class="btn btn-gradient-primary waves-effect waves-light"><?php echo $con;?></button>
													
              </div>
             </div>
           </div><!-- end col-->
    <div class="card-body">
    <form name="form1" id="form1">
	  <div class="table-responsive">
        <table class="table table-bordered text-nowrap">
          <tr>
			<th>ID</th>
			<th>备注</th>
			<th>添加时间</th>
			<th>操作</th>
		   </tr>
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

$rs=$DB->query("SELECT * FROM pay_dmf WHERE{$sql} order by addtime desc limit $offset,$pagesize");
while($res = $rs->fetch())
{
	echo '<tr><td>'.$res['id'].'</td><td><img src="/Mym/Assets/Icon/alipay.ico" width="16" onerror="this.style.display=\'none\'">'.$res['beizhu'].'</td>
	<td>'.$res['addtime'].'</br></td>';
?>
	&nbsp;<td><a onclick="Del_Qr('<?=$res['id']?>');" class="btn btn-xs btn-warning"><i class="fa fa-trash-o">删除</i></a></td>
	</td>
	<tr>
<?php
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
                                                </nav>
                                        </div> <!-- end table-responsive-->
                                    </div> <!-- end card body-->
                                </div> <!-- end card -->
                            </div><!-- end col-->
                        </div>
                    </div>
                </div>
<div class="modal fade bs-example-modal-center"   id="modalHeaderColorInfo" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">添加当面付</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
		    <div class="modal-body">
<div class="form-group">
<label>当面付应用ID:</label><br>
<input type="text" class="form-control" id="f2fid" value="" name="f2fid">
</div>
<div class="form-group">
<label>支付宝公钥:</label><br>
<input type="text" class="form-control" id="f2fkey" value="" name="f2fkey">
</div>
<div class="form-group">
<label>支付宝商户私钥:</label><br>
<input type="text" class="form-control" id="f2fpublic" value="" name="f2fpublic">
</div>
<div class="form-group">
<label id="beizhu_name">备注:</label><br>
<input type="text" class="form-control" id="beizhu" value="" placeholder="" name="beizhu">
</div>
<div class="form-group">
<label id="LoginQrcode_msg">确保所有信息填写正确哦</label><br><div id="LoginQrcode">
</div>
</br><button type="sumbit" class="btn btn-primary btn-block" onclick="Add_Qr();">确认以上并添加</button>		
				</div>
			  </div>
			</div>
         </div>
       </div>
    </div>

<script src="User_Js/dmf.js"></script>
<?php } include './foot.php'; ?>