<?php
include("../../Mym/Common.php");
if ($islogin_admin == 1) {} else exit("<script language='javascript'>window.location.href='./Login.php';</script>");

if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
	if (copy($_FILES['file']['tmp_name'], ROOT . 'Mym/Assets/Img/logo.png')) {
		echo json_encode([
			'code' => 1,
			'img' => '/Mym/Assets/Img/logo.png?r=' . (file_exists(ROOT . 'Mym/Assets/Img/logo.png') ? md5_file(ROOT . 'Mym/Assets/Img/logo.png') : rand(10000, 99999))
		]);
	} else {
		echo json_encode([
			'code' => 0
		]);
	}
	exit;
}
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title>基础表单</title>
	<link rel="stylesheet" href="../assets/libs/layui/css/layui.css" />
	<link rel="stylesheet" href="../assets/module/admin.css?v=318" />
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
		#formBasForm {
			max-width: 700px;
			margin: 30px auto;
		}

		#formBasForm .layui-form-item {
			margin-bottom: 25px;
		}
	</style>
</head>

<body>
	<!-- 正文开始 -->
	<div class="layui-fluid">
		<div class="layui-card">
			<div class="layui-card-body">
				<!-- 表单开始 -->
				<form class="layui-form" id="formBasForm" lay-filter="formBasForm">
					<input type="hidden" name="s" value="1" />

					<button type="button" class="layui-btn" id="ID-upload-demo-btn">
						<i class="layui-icon layui-icon-upload"></i> LOGO上传
					</button>
					<div style="max-width: 100%;">
						<div class="layui-upload-list">
							<img class="layui-upload-img" id="ID-upload-demo-img" src="/Mym/Assets/Img/logo.png?r=<?= file_exists(ROOT . 'Mym/Assets/Img/logo.png') ? md5_file(ROOT . 'Mym/Assets/Img/logo.png') : rand(10000, 99999) ?>" style="width: 100%;">
							<div id="ID-upload-demo-text"></div>
						</div>
						<div style="display: none;" class="layui-progress layui-progress-big" lay-showPercent="yes" lay-filter="filter-demo">
							<div class="layui-progress-bar" lay-percent=""></div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- js部分 -->
	<!-- 请勿在项目正式环境中引用该 layui.js 地址 -->
	<script src="//unpkg.com/layui@2.8.11/dist/layui.js"></script>
	<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
	<!-- <script src="../../Mym/Assets/Login/static/js/jquery-3.2.1.min.js"></script> -->
	<script>
		layui.use(function() {
			var upload = layui.upload;
			var layer = layui.layer;
			var element = layui.element;
			var $ = layui.$;
			// 单图片上传
			var uploadInst = upload.render({
				elem: '#ID-upload-demo-btn',
				url: '', // 此处用的是第三方的 http 请求演示，实际使用时改成您自己的上传接口即可。
				before: function(obj) {
					$('.layui-progress').show();
					// 预读本地文件示例，不支持ie8
					obj.preview(function(index, file, result) {
						$('#ID-upload-demo-img').attr('src', result); // 图片链接（base64）
					});
					element.progress('filter-demo', '0%'); // 进度条复位
					layer.msg('上传中', {
						icon: 16,
						time: 0
					});
				},
				done: function(res) {
					// 若上传失败
					if (res.code <= 0) {
						return layer.msg('上传失败，可能没有文件写入权限');
					}
					// 上传成功的一些操作
					// …
					$('#ID-upload-demo-img').attr('src', res.img);
					$('#ID-upload-demo-text').html(''); // 置空上传失败的状态
				},
				error: function() {
					// 演示失败状态，并实现重传
					var demoText = $('#ID-upload-demo-text');
					demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
					demoText.find('.demo-reload').on('click', function() {
						uploadInst.upload();
					});
				},
				// 进度条
				progress: function(n, elem, e) {
					element.progress('filter-demo', n + '%'); // 可配合 layui 进度条元素使用
					if (n == 100) {
						layer.msg('上传完毕', {
							icon: 1
						});
					}
				}
			});
		});
	</script>
</body>

</html>