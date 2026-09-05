<?php
// +----------------------------------------------------------------------
// | Quotes [ 只为给用户更好的体验]**[我知道发出来有人会盗用,但请您留版权]
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: MYM  <485570653@qq.com>        Mymcode          盗用不留版权,你就不配拿去!
// +----------------------------------------------------------------------
// | Date: 2021年03月24日
// +----------------------------------------------------------------------

/**
 * 支付设置
**/
$title='支付设置';
include './Head.php';
$mblist = \lib\PayTemplate::getList();
?>
<!-- APP MAIN ==========-->
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
			<div class="row">
				<div class="col-lg-6">
					<div class="card">
						<div class="card-body">
						    <div class="card-body new-user">
                  <h4 class="header-title mb-4 mt-0">支付配置</h4>
					    <div class="panel-body">
					        <div class="form-group">
                                <label>支付模板</label>
                                <select class="form-control" id="pay_template" name="pay_template" default="<?php echo $userrow['pay_template']?>">
								<?php 
								echo '<option value="'.$userrow['pay_template'].'">'.$userrow['pay_template'].'</option>';
								foreach ($mblist as $item)
								{
								    if($item!=$userrow['pay_template']){
								        echo '<option value="'.$item.'">'.$item.'</option>';
								    }
								}
								?>
								</select>
                            </div>
                            <div class="form-group">
                                <label>收款邮箱提醒</label>
                                <select class="form-control" id="money_mail" name="money_mail" default="<?php echo $userrow['money_mail']?>">
								<?php if($userrow['money_mail']==0){?>
									<option value="0">[模式①]关闭</option>
									<option value="1">[模式②]开启</option>
								<?php }elseif($userrow['money_mail']==1){?>
									<option value="1">[模式②]开启</option>
									<option value="0">[模式①]关闭</option>
								<?php }?>
								</select>
                            </div>
                            <div class="form-group">
                                <label>免输入收款模式</label>
                                <select class="form-control" id="free" name="free" default="<?php echo $userrow['free']?>">
								<?php if($userrow['free']==0){?>
									<option value="0">[模式①]关闭免输入</option>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="2">[模式③]锁死免输入模式(风控值：中)</option>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
									<option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
								    <option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
								<?php }elseif($userrow['free']==1){?>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="0">[模式①]关闭免输入(风控值：低)</option>
									<option value="2">[模式③]锁死免输入模式(风控值：中)</option>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
									<option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
								    <option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
								<?php }elseif($userrow['free']==2){?>
									<option value="2">[模式③]锁死免输入模式(风控值：中)</option>
									<option value="0">[模式①]关闭免输入(风控值：低)</option>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
									<option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
								    <option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
								<?php }elseif($userrow['free']==3){?>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
									<option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
								    <option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
									<option value="0">[模式①]关闭免输入(风控值：低)</option>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="2">[模式③]锁死免输入模式(风控值：低)</option>
								<?php }elseif($userrow['free']==4){?>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
									<option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
								    <option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
									<option value="0">[模式①]关闭免输入(风控值：低)</option>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="2">[模式③]锁死免输入模式(风控值：低)</option>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
								<?php }elseif($userrow['free']==5){?>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
									<option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
									<option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
									<option value="0">[模式①]关闭免输入(风控值：低)</option>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="2">[模式③]锁死免输入模式(风控值：低)</option>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
								<?php }elseif($userrow['free']==6){?>//⑦⑧
								    <option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
								    <option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
									<option value="0">[模式①]关闭免输入(风控值：低)</option>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="2">[模式③]锁死免输入模式(风控值：低)</option>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
								<?php }elseif($userrow['free']==7){?>
								    <option value="7">[模式⑧]跳转蚂蚁森林免输入模式(风控值：低)</option>
									<option value="0">[模式①]关闭免输入(风控值：低)</option>
									<option value="1">[模式②]转账免输模式(风控值：低)</option>
									<option value="2">[模式③]锁死免输入模式(风控值：低)</option>
									<option value="3">[模式④]小钱袋免输入模式(风控值：高)</option>
									<option value="4">[模式⑤]跳转银行卡免输入模式(风控值：低)</option>
									<option value="5">[模式⑥]跳转花呗免输入模式(风控值：低)</option>
									<option value="6">[模式⑦]跳转滴滴免输入模式(风控值：低)</option>
								<?php }?>
								</select>
                            </div>
                            <div class="form-group">
                                <label>联系方式开关</label>
                                <select class="form-control" id="mali" name="mali" default="<?php echo $userrow['mali']?>">
								<?php if($userrow['mali']==0){?>
									<option value="0">[模式①]关闭</option>
									<option value="1">[模式②]开启</option>
								<?php }elseif($userrow['mali']==1){?>
									<option value="1">[模式②]开启</option>
									<option value="0">[模式①]关闭</option>
								<?php }?>
								</select>
							</div>
							<div class="form-group">
                                <label>订单自动补单</label>
                                <select class="form-control" id="Order_Money" name="Order_Money" default="<?php echo $userrow['Order_Money']?>">
								<?php if($userrow['Order_Money']==0){?>
									<option value="0">[模式①]关闭</option>
									<option value="1">[模式②]开启</option>
								<?php }elseif($userrow['Order_Money']==1){?>
									<option value="1">[模式②]开启</option>
									<option value="0">[模式①]关闭</option>
								<?php }?>
								</select>
                            </div>
                            <div class="form-group">
                                <label>支付语音提示</label>
                                <select class="form-control" id="music" name="music" default="<?php echo $userrow['music']?>">
								<?php if($userrow['music']==0){?>
									<option value="0">[模式①]关闭</option>
									<option value="1">[模式②]开启</option>
								<?php }else{?>
									<option value="1">[模式②]开启</option>
									<option value="0">[模式①]关闭</option>
								<?php }?>
								</select>
                            </div>
                        </div>
				</div>
          	</div>
        </div>
 	</div>
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
			    <div class="card-body new-user">
					<h4 class="header-title mb-4 mt-0">对接配置</h4>
					<div class="panel-body">
					        <div class="form-group">
                                <label>支付宝引导浏览器支付</label>
                                <select class="form-control" id="pay_tzali" name="pay_tzali" default="<?php echo $userrow['pay_tzali']?>">
								<?php if($userrow['pay_tzali']==0){?>
									<option value="0">[模式①]关闭</option>
									<option value="1">[模式②]开启</option>
								<?php }else{?>
									<option value="1">[模式②]开启</option>
									<option value="0">[模式①]关闭</option>
								<?php }?>
								</select>
                            </div>
                            <div class="form-group">
                                <label>微信引导浏览器支付</label>
                                <select class="form-control" id="pay_tzwx" name="pay_tzwx" default="<?php echo $userrow['pay_tzwx']?>">
								<?php if($userrow['pay_tzwx']==0){?>
									<option value="0">[模式①]关闭</option>
									<option value="1">[模式②]开启</option>
								<?php }else{?>
									<option value="1">[模式②]开启</option>
									<option value="0">[模式①]关闭</option>
								<?php }?>
								</select>
                            </div>
                            <div class="form-group">
                                <label>QQ引导浏览器支付</label>
                                <select class="form-control" id="pay_tzqq" name="pay_tzqq" default="<?php echo $userrow['pay_tzqq']?>">
								<?php if($userrow['pay_tzqq']==0){?>
									<option value="0">[模式①]关闭</option>
									<option value="1">[模式②]开启</option>
								<?php }else{?>
									<option value="1">[模式②]开启</option>
									<option value="0">[模式①]关闭</option>
								<?php }?>
								</select>
                            </div>
							<div class="form-group">
                                <label>支付宝收款模式</label>
                                <select class="form-control" id="alipay_pay_open" name="alipay_pay_open" default="<?php echo $userrow['alipay_pay_open']?>" onclick="setALIPAY()">
								<?php if($userrow['alipay_pay_open']==0){?>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
									<option value="3">[模式④]掉线临时对接当面付</option>
								<?php }elseif($userrow['alipay_pay_open']==1){?>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
									<option value="3">[模式④]掉线临时对接当面付</option>
								<?php }elseif($userrow['alipay_pay_open']==2){?>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="3">[模式④]掉线临时对接当面付</option>
									<?php }elseif($userrow['alipay_pay_open']==3){?>
									<--<option value="3">[模式④]掉线临时对接当面付</option>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
								<?php }?>
								</select>
                            </div>
							<div id="setALIPAY" style="<?php echo $userrow['alipay_pay_open']==2?null:'display:none;'; ?>">
							<div class="form-group">
                                <label>其他易支付URL</label>
                                <input type="text" id="alipay_api_url" name="alipay_api_url" value="<?=$userrow['alipay_api_url']?>" class="form-control" >
                            </div>
							<div class="form-group">
                                <label>其他易支付PID</label>
                                <input type="text" id="alipay_api_pid" name="alipay_api_pid" value="<?=$userrow['alipay_api_pid']?>" class="form-control" >
                            </div>
							<div class="form-group">
                                <label>其他易支付KEY</label>
                                <input type="text" id="alipay_api_key" name="alipay_api_key" value="<?=$userrow['alipay_api_key']?>" class="form-control" >
                            </div>
                            </div>
							
							<div class="form-group">
                                <label>QQ钱包收款模式</label>
                                <select class="form-control" id="qqpay_pay_open" name="qqpay_pay_open" default="<?php echo $conf['qqpay_pay_open']?>" onclick="setQQPAY(this);">
								<?php if($userrow['qqpay_pay_open']==0){?>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
								<?php }elseif($userrow['qqpay_pay_open']==1){?>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
								<?php }else{?>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="1">[模式②]掉线可继续收款</option>
								<?php }?>
								</select>
                            </div>
							<div id="setQQPAY" style="<?php echo $userrow['qqpay_pay_open']==2?null:'display:none;'; ?>">
							<div class="form-group">
                                <label>其他易支付URL</label>
                                <input type="text" id="qqpay_api_url" name="qqpay_api_url" value="<?=$userrow['qqpay_api_url']?>" class="form-control" >
                            </div>
							<div class="form-group">
                                <label>其他易支付PID</label>
                                <input type="text" id="qqpay_api_pid" name="qqpay_api_pid" value="<?=$userrow['qqpay_api_pid']?>" class="form-control" >
                            </div>
							<div class="form-group">
                                <label>其他易支付KEY</label>
                                <input type="text" id="qqpay_api_key" name="qqpay_api_key" value="<?=$userrow['qqpay_api_key']?>" class="form-control" >
                            </div>
                            </div>
                            <div class="form-group">
                                <label>微信收款模式</label>
                                <select class="form-control" id="wxpay_pay_open" name="wxpay_pay_open" default="<?php echo $conf['wxpay_pay_open']?>" onclick="setWXPAY(this);">
								<?php if($userrow['wxpay_pay_open']==0){?>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
								<?php }elseif($userrow['wxpay_pay_open']==1){?>
									<option value="1">[模式②]掉线可继续收款</option>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
								<?php }else{?>
									<option value="2">[模式③]掉线临时对接其他易支付</option>
									<option value="0">[模式①]掉线不可收款</option>
									<option value="1">[模式②]掉线可继续收款</option>
								<?php }?>
								</select>
                            </div>
							<div id="setWXPAY" style="<?php echo $userrow['wxpay_pay_open']==2?null:'display:none;'; ?>">
							<div class="form-group">
                                <label>其他易支付URL</label>
                                <input type="text" id="wxpay_api_url" name="wxpay_api_url" value="<?=$userrow['wxpay_api_url']?>" class="form-control" >
                            </div>
							<div class="form-group">
                                <label>其他易支付PID</label>
                                <input type="text" id="wxpay_api_pid" name="wxpay_api_pid" value="<?=$userrow['wxpay_api_pid']?>" class="form-control" >
                            </div>
							<div class="form-group">
                                <label>其他易支付KEY</label>
                                <input type="text" id="wxpay_api_key" name="wxpay_api_key" value="<?=$userrow['wxpay_api_key']?>" class="form-control" >
                            </div>
                        </div>
					</div>
					<div class="panel-body">
					    <div class="form-group">
					        <label>订单超时时间(秒):</label>
					        <input type="text" id="outtime" name="outtime" value="<?=$userrow['outtime']?>" class="form-control" ><font color="green">如不设置默认"<?php echo $conf['outtime'];?>"秒,5分钟超时请填写"300"</font>
					    </div>  
                    </div>
				</div>
            </div>
        </div>
        </div>
	    <div class="col-md-12 col-lg-12 col-xl-12">
        <div class="card bg-white m-b-30">
        <div class="card-body new-user">
		<div class="panel-body">
		<div class="form-group">
		<button type="sumbit" class="btn btn-primary btn-block" onclick="pay_set();">确定修改</button>
        </div> 
		</div><!-- .row -->
        </div>
        </div>
        </div> </br></br>
  <script type="text/javascript">
function setALIPAY(){
    var alipay_pay_open= $("#alipay_pay_open").val(); 
	if(alipay_pay_open == 2){
        var user= $("#user").val(); 
		$("#setALIPAY").show();
		$("#setaALIPAY").hide();
	}else if(alipay_pay_open==3){
	    //$("#setaALIPAY").show();
		$("#setALIPAY").hide();
	}else{
	    $("#setALIPAY").hide();
	    $("#setaALIPAY").hide();
	}
}
function setaALIPAY(){
    var alipay_pay_open= $("#alipay_pay_open").val(); 
	if(alipay_pay_open == 3){ 
		var user= $("#user").val();
		$("#setaALIPAY").show();
	}else{
		$("#setALIPAY").hide();
	}
}
function setQQPAY(){
    var qqpay_pay_open= $("#qqpay_pay_open").val(); 
	if(qqpay_pay_open == 2){
        var user= $("#user").val(); 
		$("#setQQPAY").show();
	}else{
		$("#setQQPAY").hide();
	}
}
function setWXPAY(){
    var wxpay_pay_open= $("#wxpay_pay_open").val(); 
	if(wxpay_pay_open == 2){
        var user= $("#user").val(); 
		$("#setWXPAY").show();
	}else{
		$("#setWXPAY").hide();
	}
}
function pay_set(){//POST提交
    var outtime= $("#outtime").val();
    var alipay_pay_open= $("#alipay_pay_open").val(); 
	var alipay_api_url= $("#alipay_api_url").val(); 
	var alipay_api_pid= $("#alipay_api_pid").val(); 
	var alipay_api_key= $("#alipay_api_key").val(); 
	var qqpay_pay_open= $("#qqpay_pay_open").val(); 
	var qqpay_api_url= $("#qqpay_api_url").val(); 
	var qqpay_api_pid= $("#qqpay_api_pid").val(); 
	var qqpay_api_key= $("#qqpay_api_key").val();
	var wxpay_pay_open= $("#wxpay_pay_open").val(); 
	var wxpay_api_url= $("#wxpay_api_url").val(); 
	var wxpay_api_pid= $("#wxpay_api_pid").val(); 
	var wxpay_api_key= $("#wxpay_api_key").val();
	var moneymail= $("#money_mail").val();
	var Order_Money= $("#Order_Money").val();
	var pay_template= $("#pay_template").val();
	var music= $("#music").val();
	var free = $("#free").val();
	var mali = $('#mali').val();
	var pay_tzqq = $("#pay_tzqq").val();
	var pay_tzwx = $("#pay_tzwx").val();
	var pay_tzali = $("#pay_tzali").val();
	var ii = layer.load(3, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "Ajax2.php?act=Pay_set",
		data : {outtime,alipay_pay_open,alipay_api_url,alipay_api_pid,alipay_api_key,qqpay_pay_open,qqpay_api_url,qqpay_api_pid,qqpay_api_key,wxpay_pay_open,wxpay_api_url,wxpay_api_pid,wxpay_api_key,moneymail,free,mali,Order_Money,music,pay_template,pay_tzqq,pay_tzali,pay_tzwx},
		dataType : 'json',
		timeout:10000,
		success : function(data) {					  
			layer.close(ii);
			layer.msg(data.msg);
			if(data.code==1){
				setTimeout(function () {
					location.href="?";
				}, 1000); //延时1秒跳转
			}
		},error:function(data){
			layer.close(ii);
			layer.msg('服务器错误');
			}
	});
}

  </script>
  <?php } include './foot.php'; ?>