<?php
$title='用户中心';
include './Head.php';
?>
   <style>
       * {
         padding: 0;
         margin: 0;
         }
       body {
         background-color: ;
       }
      .alerts {
          
         position:fixed;
         margin:auto;
         top: 10%;
         width:86%;
         right:0;
         left: 0;
         
         transform：translateX(-50%);
         transform：translateY(50%);
         background-color: #fff;
         border-radius:8px;
         overflow:hidden;
         box-shadow: 0 0 200px 8px #999;
        z-index: 999
      }
      
      .alerts_top {  
         positon: relative;
         padding: 20px 0;
         text-align:center;
         background-color:#008fff;   
         color: #fff;
      }
      .alerts_top h3 {
         color: #fff;
         
      }
      .alerts_top .btngb{
         display: block;
         positon:absolute;
         top: 4px;
         right: 5px;
      }
      .alerts_text {
         padding: 16px 20px;
      }
      .alerts_text p {
        height: 20px;
        line-height: 20px;
        font-size: 14px;
      }
      .alerts .btnw {
         padding: 10px 20px 25px;
         text-align: right;
      }
      .alerts .btnw a {
         padding: 7px 10px;
         background-color: #d1ebff;
         color: #00a2ff;
         border-radius: 4px;
         font-size: 14px;
         text-decoration:none;
         margin-left: 10px;
      }
      .alerts .btnw a:nth-child(2){
        color: #ff0035;
        background-color: #ffe2e8;
        
      }
      .alerts-zd {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 99;
    
      }
      .ggt {
         width: 100%;
         height: 40px;
         margin-bottom: 30px;
      }
      .ggt img {
          width: 100%;
          height: 40px;
      }
       footer {
         background-color: #202d40;
         padding:26px 26px;
         color: #fff;
       }
       .xqy-border {
         border: 1.5px solid #eee;
         border-radius: 4px;
         padding: 10px 15px;
       }
       .xqy-email {
         padding: 6px 0;
         margin: 15px 0 10px;
         text-align:center;
         border-bottom: 1px solid #fff;
       }
       .beianNumber-des {
         text-align: center;
         margin-bottom: -10px;
       }
       
       .fyouqing a{
         display: inline-block;
         margin-right: 8px;
         color: #f7f9fb;
       }
       .banquan {
         text-align:center;
         margin: 50px 0 0;
       }
       .banquan a{
         color: #fff
       }
      @media  screen and  (min-width: 768px) {
         .alerts {
             width: 720px;
          }
         .xqy-border {
             border: none;
         }
      }
      #hiddenElement {
  display: none;
}
.my-3 {
  margin-top: 2rem !important;
  margin-bottom: 2rem !important;
}
    </style>
  </head>
  <body>
	<?php if($conf['modal']){?>
	<div class="alerts">
       <div class="alerts_top">
           <h3>欢迎来到本站</h3>
          <p>春暖花开，奔你而来</p>
       </div>
       <div class="alerts_text">
           <font color="<?=$conf['modal']?>" </font></a>
        <h5><?= htmlspecialchars_decode($conf['modal']) ?><br></h5>
       </div> 
       <div class="btnw"><a id="dj" href="JavaScript:;">了解</a></div>
    </div>
	
    <div class="alerts-zd"></div>
    <?php }?>

	<div class="row">
		<div class="col-sm-12">
			<div class="page-title-box">
				<div class="btn-group float-right">
					<ol class="breadcrumb hide-phone p-0 m-0">

					</ol>
				</div>
				<h4 class="page-title">总数据表</h4>
			</div>
		</div>
	</div>
	<!-- end page title end breadcrumb -->
	<div class="mym-dashboard-hero">
		<div>
			<h2>欢迎回来，<?php echo $userrow['user']?$userrow['user']:'商户 '.$userrow['pid']; ?></h2>
			<p>实时查看订单、通道、额度与接口状态，保持收款链路稳定运行。</p>
		</div>
		<div class="mym-hero-actions">
			<a href="./Free_Qrlist.php" class="btn btn-light"><i class="mdi mdi-credit-card-multiple-outline"></i> 管理通道</a>
			<a href="./Set.php" class="btn btn-light"><i class="mdi mdi-tune"></i> 支付设置</a>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="row mym-stat-row">
				<div class="col-lg-3">
					<div class="card">
						<div class="card-body">
							<div class="icon-contain">
								<div class="row">
									<div class="col-2 align-self-center">
										<i class="fas fa-tasks text-gradient-success"></i>
									</div>
									<div class="col-10 text-right">
										<h5 class="mt-0 mb-1">
											<span id="count1"></span>
										</h5>
										<p class="mb-0 font-12 text-muted">总订单数</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="card">
						<div class="card-body justify-content-center">
							<div class="icon-contain">
								<div class="row">
									<div class="col-2 align-self-center">
										<i class="far fa-gem text-gradient-danger"></i>
									</div>
									<div class="col-10 text-right">
										<h5 class="mt-0 mb-1">
											<span id="count2"></span>
										</h5>
										<p class="mb-0 font-12 text-muted">成功订单</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="card">
						<div class="card-body">
							<div class="icon-contain">
								<div class="row">
									<div class="col-2 align-self-center">
										<i class="fas fa-users text-gradient-warning"></i>
									</div>
									<div class="col-10 text-right">
										<h5 class="mt-0 mb-1">
											<span id="count3"></span>
										</h5>
										<p class="mb-0 font-12 text-muted">完成金额</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="card ">
						<div class="card-body">
							<div class="icon-contain">
								<div class="row">
									<div class="col-2 align-self-center">
										<i class="fas fa-database text-gradient-primary"></i>
									</div>
									<div class="col-10 text-right">
										<h5 class="mt-0 mb-1">
											<?php echo $userrow['money'];?>
										</h5>
										<p class="mb-0 font-12 text-muted">我的额度</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="alert alert-outline-purple b-round" role="alert">
				<strong>送给你一句话：</strong><strong id="hitokoto"><strong id="hitokoto_text">:D 获取中...</strong>
			</div>
			<script>
  fetch('https://v1.hitokoto.cn')
    .then(response => response.json())
    .then(data => {
      const hitokoto = document.querySelector('#hitokoto_text')
      hitokoto.href = `https://v1.hitokoto.cn/?c=f&encode=text`
      hitokoto.innerText = data.hitokoto
    })
    .catch(console.error)
</script>





<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="header-title pb-3 mt-0">三网数据表格</h5>
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr class="align-self-center">
                <th>通道支付</th>
                <th>今日收入</th>
                <th>昨天收入</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><img src="/Mym/Assets/Img/alipay.jpeg" alt="" class="thumb-sm rounded-circle mr-2">支付宝</td>
                <td><span class="badge badge-boxed  badge-soft-success"><span id="order_today_alipay"></span>￥</span></span>￥</td>
                <td><span class="badge badge-boxed  badge-soft-primary"><span id="order_lastday_alipay"></span>￥</span></span>￥</td>
              </tr>
              <tr>
                <td><img src="/Mym/Assets/Img/weixin.jpeg" alt="" class="thumb-sm rounded-circle mr-2">微信</td>
                <td><span class="badge badge-boxed  badge-soft-success"><span id="order_today_wxpay"></span>￥</span></span>￥</td>
                <td><span class="badge badge-boxed  badge-soft-primary"><span id="order_lastday_wxpay"></span>￥</span></span>￥</td>
              </tr>
              <tr>
                <td><img src="/Mym/Assets/Img/qq.jpeg" alt="" class="thumb-sm rounded-circle mr-2">财付通</td>
                <td><span class="badge badge-boxed  badge-soft-success"><span id="order_today_qqpay"></span>￥</span></span>￥</td>
                <td><span class="badge badge-boxed  badge-soft-primary"><span id="order_lastday_qqpay"></span>￥</span></span>￥</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<div class="col-lg-6">
  <div class="card mym-quick-card">
    <div class="card-body">
      <h4 class="mt-0 header-title">快捷菜单</h4>
      <div class="row button-row mym-quick-grid">
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='./Free_Qrlist.php'"><i class="mdi mdi-credit-card-multiple-outline"></i> 通道列表</button>
          </div>
        </div>
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='./Pay_Vip.php'"><i class="mdi mdi-cash-plus"></i> 立即充值</button>
          </div>
        </div>
        <hr class="my-3">
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='./Set.php'"><i class="mdi mdi-tune"></i> 支付设置</button>
          </div>
        </div>
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='./Order.php'"><i class="mdi mdi-clipboard-text-outline"></i> 订单管理</button>
          </div>
        </div>        
        <hr class="my-3">
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='./userinfo.php'"><i class="mdi mdi-api"></i> API资料</button>
          </div>
        </div>
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='./Free_dmf.php'"><i class="mdi mdi-qrcode-scan"></i> 当面付配置</button>
          </div>
        </div>
        <hr class="my-3">
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='./plug.php'"><i class="mdi mdi-puzzle-outline"></i> 插件市场</button>
          </div>
        </div>
        <div class="col-6">
          <div class="button-items">
            <button type="button" class="btn btn-outline-purple btn-lg btn-block" onclick="location.href='Ajax2.php?act=logout'"><i class="mdi mdi-logout"></i> 退出登录</button>
          </div>
        </div>        
      </div>
    </div>
  </div>
</div>

</div>


<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h4 class="mt-0 header-title">系统公告</h4>
        <ul class="list-group list-group-lg no-bg auto">
          <?php 
          $rs = $DB->query("SELECT * FROM pay_notice where status='1' order by id ASC limit 20");
          while ($res = $rs->fetch()) {?>
            <a class="list-group-item">
              <span class="pull-right"></span>
              <img border="0" width="32" src="Assets/img/gg.gif">
              <font color="<?php echo $res['color']?$res['color']:null ?>"><?php echo $res['datatxt'] ?></font>
            </a>
          <?php }?>
        </ul>
      </div>
    </div>
  </div>



<div class="col-lg-6">
  <div class="card">
    <div class="card-body">
      <h4 class="mt-0 header-title">商户资料</h4>
      <table class="table table-striped">
        <thead>
          <tr>
            <th style="width: 50%;">
              <span class="" data-clipboard-text="./" style="cursor:pointer;"></span>
              <i class="fa fa-th-large fa-fw text-muted"></i>
              API地址
            </th>
            <td>
              <?php echo $siteurl ?>

            </td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <span class="" data-clipboard-text="./" style="cursor:pointer;"></span>
              <i class="fa fa-th-large fa-fw text-muted"></i>
              PID：
            </td>
            <td>
              <?php echo $userrow['pid'] ?><span style="color: red; font-size: 12px; margin-left: 10px;">请不要把这些信息泄露！！！</span>
            </td>
          </tr>
          <tr>
            <td>
              <span class="" data-clipboard-text="./" style="cursor:pointer;"></span>
              <i class="fa fa-th-large fa-fw text-muted"></i>
              KEY:
            </td>
            <td>
              <?php echo $userrow['key'] ?><span style="color: red; font-size: 12px; margin-left: 10px;">请不要把这些信息泄露！！！</span>
            </td>
          </tr>
          <tr>
            <td>
              商户额度：
            </td>
            <td>
              <?php echo $userrow['money'] ?>
            
            </td>
          </tr>
          <tr>
            <td>
              套餐到期：
            </td>
            <td>
              <?php echo $userrow['user_vip_time'] ?>
            
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
      </div>
    </div>
  </div>
</div>

<script>
$("#dj").click(function() {
    $(".alerts").hide(500);
    $(".alerts-zd").hide();
});
$(document).ready(function(){
	$.ajax({
		type : "GET",
		url : "Ajax3.php?act=getcount",
		dataType : 'json',
		async: true,
		success : function(data) {
		    if(data.code==1){
		        $('#count1').html(data.orderarray.count1);
		        $('#count2').html(data.orderarray.count2);
		        $('#count3').html(data.orderarray.count3);
		        $('#order_today_alipay').html(data.data.order_today.alipay);
		        $('#order_today_wxpay').html(data.data.order_today.wxpay);
		        $('#order_today_qqpay').html(data.data.order_today.qqpay);
		        $('#order_lastday_alipay').html(data.data.order_lastday.alipay);
		        $('#order_lastday_wxpay').html(data.data.order_lastday.wxpay);
		        $('#order_lastday_qqpay').html(data.data.order_lastday.qqpay);
		        $('#money').html(data.user.money);
		        $('#money2').html(data.user.money);
		        $('#pid').html(data.user.pid);
		        $('#key').html(data.user.key);
		        $('#user_vip_time').html(data.user_vip_time);
		        $('#vip_name').html(data.vip_name);
		        $('#url').html(data.url)
		    }
		}
	});
});
</script>
<?php include './foot.php'; ?>