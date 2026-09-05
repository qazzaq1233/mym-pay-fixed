<?php
/**
 * 码子管理
**/
$title='插件市场';
include './Head.php';
	$numrows=$DB->query("SELECT * from pay_plug WHERE 1")->rowCount();
	$sql=" 1";
	$con='共有 <b>'.$numrows.'</b> 插件';

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
<!-- End Page Header --> 
<div class="block">
<div class="row">
  <div class="col-lg-12">
    <div class="card">
       <div class="card-body">
        <div class="form-group mb-2">    
              <div>
	            <button type="button" class="btn btn-gradient-primary waves-effect waves-light"><?php echo $con;?></button>
													
              </div>
             </div>   
		  
 
           </div><!-- end col-->
    <form name="form1" id="form1">
	  <div class="table-responsive">
        <table class="table table-bordered text-nowrap">
          <tr>
			<th>ID</th>
			<th>标题</th>
			<th>介绍内容</th>
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

$rs=$DB->query("SELECT * FROM pay_plug WHERE{$sql} limit $offset,$pagesize");
while($res = $rs->fetch())
{//(`name`,`type`,`(`name`,`type`,`title`,`download`,`time`)`,`download`,`time`)
	echo '<tr><td>'.$res['id'].'</td><td>'.$res['name'].'</td>
	<td>'.$res['title'].'</td>
	<td>'.$res['time'].'</br></td>';
?>
	&nbsp;<td><a href="<?=$res['download']?>" class="btn btn-xs btn-warning"><i class="fa fa-trash-o">下载</i></a></td>
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

<?php include './foot.php'; ?>