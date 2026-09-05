<?php
// +----------------------------------------------------------------------
// | Quotes [ 只为给用户更好的体验]**[我知道发出来有人会盗用,但请您留版权]
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MYM  <485570653@qq.com>        Mymcode          盗用不留版权,你就不配拿去!
// +----------------------------------------------------------------------
// | Date: 2021年09月05日
// +----------------------------------------------------------------------

/**
 * 商户设置
**/
$title='商户设置';
include './Head.php';
if($_POST['WIDout_trade_no'] and $_POST['WIDtotal_fee'] and $_POST['WIDsubject']=='额度充值'){
    $WIDsubject = $userrow['pid'].'额度充值';
	exit("<script language='javascript'>window.location.href='./SDK/epayapi.php?WIDout_trade_no=".$_POST['WIDout_trade_no']."&WIDsubject=".$WIDsubject."&WIDtotal_fee=".$_POST['WIDtotal_fee']."&type=".$_POST['type']."';</script>");
}elseif($_POST['WIDout_trade_no'] and $_POST['WIDsubject'] and $_POST['txt']){
    $money = $_POST['txt']*$conf['ed_type'];
    $WIDsubject = $userrow['pid'].'通道配额'.$_POST['txt'];
	exit("<script language='javascript'>window.location.href='./SDK/epayapi.php?WIDout_trade_no=".$_POST['WIDout_trade_no']."&WIDsubject=".$WIDsubject."&WIDtotal_fee=".$money."&type=".$_POST['type']."';</script>");
}

?>
<!-- APP MAIN ==========-->
<div class="row">
	<div class="col-sm-12">
		<div class="page-title-box">
		    <h4 class="mt-0 header-title">  <?php echo $title;?></h4>
		    <div class="row">
		        <div class="col-md-12 col-lg-12 col-xl-12">
		            <div class="card bg-white m-b-30">
		                <div class="card-body new-user">
		                    <ul class="nav nav-tabs">
		                        <li class="active">
		                            <a onclick="jsform(1);" class="btn btn-success mb-2 mr-2">在线充值额度</a>
		                        </li>
		                        <li class="active">
		                            <a onclick="jsform(2);" class="btn btn-warning mb-2 mr-2">在线充值配额</a>
		                        </li>
		                        <li class="active">
		                            <a onclick="jsform(3);" class="btn btn-gradient-info waves-effect waves-light">当前商户测试</a>
		                        </li>
		                    </ul>
<!---------------------------------------------------------------------->
<div id="overview" style="display: block;">
  <form action="./Pay_Vip.php" method="post" target="_self">
    <div class="form-control" style="background-color: #edf1f2; border-color: #eee;"><font color="color:#5FB878;"><strong>温馨提示：</strong> 充值额度[比例:1$=<?=$conf['ed_money']?>额度]</font>
    </div>
	  <div class="modal-body">
		<div class="form-group">
		  <label id="beizhu_name">商户订单号:</label><br>
		  <input type="text" class="form-control" id="WIDout_trade_no" value="<?= date("YmdHis").mt_rand(100,999)?>" name="WIDout_trade_no">
		</div>
		<div class="form-group">
		  <label id="beizhu_name">商品名称:</label><br>
		  <input type="text" class="form-control" id="WIDsubject" value="额度充值" name="WIDsubject">
		</div>
		<div class="form-group">
		  <label id="beizhu_name">付款金额:</label><br>
		  <input type="text" class="form-control" id="WIDtotal_fee" value="1.00" name="WIDtotal_fee">
		</div>
		<div class="form-group">
		  <label>支付类型:</label><br>
		  <select class="form-control" name="type" id="type"><option value="alipay">支付宝</option><option value="wxpay">微信</option><option value="qqpay">QQ钱包</option><option value="usdt">USDT</option></select>
		</div>
		<button class="btn btn-block btn-info" type="submit">确 认</button>	
	  </div>
	</form></div>
<div id="overview2" style="display: none;">
  <form action="./Pay_Vip.php" method="post" target="_self">
    <div class="form-control" style="background-color: #edf1f2; border-color: #eee;"><font color="color:#5FB878;"><strong> 温馨提示：</strong> 购买配额元<?=$conf['ed_type']?>/1个</font></div>
	  <div class="modal-body">
		<div class="form-group">
		  <label id="beizhu_name">商户订单号:</label><br>
		  <input type="text" class="form-control" id="WIDout_trade_no" value="<?= date("YmdHis").mt_rand(100,999)?>" name="WIDout_trade_no">
		</div>
		<div class="form-group">
		  <label id="beizhu_name">商品名称:</label><br>
		  <input type="text" class="form-control" id="WIDsubject" value="通道配额" name="WIDsubject">
		</div>
		<div class="form-group">
		  <label>购买数量:</label><br>
		  <select class="form-control" name="txt" id="txt"><option value="1">购买配额1个</option><option value="3">购买配额3个</option><option value="6">购买配额6个</option>
		  </select>
		</div>
		<div class="form-group">
		  <label>支付类型:</label><br>
		  <select class="form-control" name="type" id="type">
		      <option value="alipay">支付宝</option><option value="wxpay">微信</option><option value="qqpay">QQ钱包</option><option value="usdt">USDT</option>
		  </select>
		</div>
		<button class="btn btn-block btn-info" type="submit">确 认</button>	
	  </div>
	</form></div>
 <div id="overview3" style="display:none;">
  <form action="./SDK/epayapi.php" method="post" target="_self">
    <div class="form-control" style="background-color: #edf1f2; border-color: #eee;"><font color="color:#5FB878;"><strong>温馨提示：</strong> 此处是测试商户支付！！！</font>
    </div>
	  <div class="modal-body">
		<div class="form-group">
		  <label id="beizhu_name">商户订单号:</label><br>
		  <input type="text" class="form-control" id="WIDout_trade_no" value="<?= date("YmdHis").mt_rand(100,999)?>" name="WIDout_trade_no">
		</div>
		<div class="form-group">
		  <label id="beizhu_name">商品名称:</label><br>
		  <input type="text" class="form-control" id="WIDsubject" value="测试商品" name="WIDsubject">
		</div>
		<div class="form-group">
		  <label id="beizhu_name">付款金额:</label><br>
		  <input type="text" class="form-control" id="WIDtotal_fee" value="0.72" name="WIDtotal_fee">
		</div>
		<div class="form-group">
		  <label>支付类型:</label><br>
		  <select class="form-control" name="type" id="type">
		      <option value="alipay">支付宝</option><option value="wxpay">微信</option><option value="qqpay">QQ钱包</option><option value="usdt">USDT</option>
		  </select>
		</div>
		<button class="btn btn-block btn-info" type="submit">确 认</button>	
	  </div>
	</form></div>
<!-----------------------------666666----------------------------------------->
</div>
			        </div>
                      </div>
			        </div>
			    </div>
		    </div>
	    </div>
	</div>
</div>
<script>
    function jsform(i){
        if(i==1){
            $("#overview2").hide();//关闭
            $("#overview3").hide();//关闭
            $("#overview").show();//展开
        }else if(i==2){
            $("#overview").hide();//关闭
            $("#overview3").hide();//关闭
            $("#overview2").show();//展开
        }else{
            $("#overview").hide();//关闭
            $("#overview2").hide();//关闭
            $("#overview3").show();//展开 
        }
    }
</script>
<?php
	include './foot.php';
?>