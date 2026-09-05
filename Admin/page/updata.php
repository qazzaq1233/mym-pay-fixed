<?php
include("../../Mym/Common.php");
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
function zipExtract($src, $dest)
{
    $zip = new ZipArchive();
    if ($zip->open($src)===true) {
        $zip->extractTo($dest);
        $zip->close();
        return true;
    }
    return false;
}
function get_zip_originalsize($filename, $path)
{
    //先判断待解压的文件是否存在
    if (!file_exists($filename)) {
        die("文件 $filename 不存在！");
    }
    $starttime = explode(' ', microtime()); //解压开始的时间

    //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
    $filename = iconv("utf-8", "gb2312", $filename);
    $path = iconv("utf-8", "gb2312", $path);
    //打开压缩包
    $resource = zip_open($filename);
    $i = 1;
    //遍历读取压缩包里面的一个个文件
    while ($dir_resource = zip_read($resource)) {
        //如果能打开则继续
        if (zip_entry_open($resource, $dir_resource)) {
            //获取当前项目的名称,即压缩包里面当前对应的文件名
            $file_name = $path.zip_entry_name($dir_resource);
            //以最后一个“/”分割,再用字符串截取出路径部分
            $file_path = substr($file_name, 0, strrpos($file_name, "/"));
       
            //如果路径不存在，则创建一个目录，true表示可以创建多级目录
            if (!is_dir($file_path)) {
                mkdir($file_path, 0777, true);
            }
            //如果不是目录，则写入文件
            if (!is_dir($file_name)) {
                //读取这个文件
                $file_size = zip_entry_filesize($dir_resource);
                //最大读取6M，如果文件过大，跳过解压，继续下一个
                if ($file_size<(1024*1024*30)) {
                    $file_content = zip_entry_read($dir_resource, $file_size);
                    file_put_contents($file_name, $file_content);
                } else {
                    echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".iconv("gb2312", "utf-8", $file_name)." </p>";
                }
            }
            //关闭当前
            zip_entry_close($dir_resource);
        }
    }
    //关闭压缩包
    zip_close($resource);
    $endtime = explode(' ', microtime()); //解压结束的时间
    $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
    $thistime = round($thistime, 3); //保留3为小数
    return "解压完毕！，本次解压花费：$thistime 秒。";
}

function httpcopy($url, $file="", $timeout=60)
{
    $file = empty($file) ? pathinfo($url, PATHINFO_BASENAME) : $file;
    $dir = pathinfo($file, PATHINFO_DIRNAME);
    !is_dir($dir) && @mkdir($dir, 0777, true);
    $url = str_replace(" ", "%20", $url);
  
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        if (@file_put_contents($file, $temp) && !curl_error($ch)) {
            return $file;
        } else {
            return false;
        }
    } else {
        $opts = array(
      "http"=>array(
      "method"=>"GET",
      "header"=>"",
      "timeout"=>$timeout)
    );
        $context = stream_context_create($opts);
        if (@copy($url, $file, $context)) {
            //$http_response_header
            return $file;
        } else {
            return false;
        }
    }
}
function download_zip($zip_name, $zip_url)
{
    ob_start(); //打开输出
    readfile($zip_url); //输出图片文件
    $zip = ob_get_contents(); //得到浏览器输出
    ob_end_clean(); //清除输出并关闭
    file_put_contents($zip_name, $zip);
    mkdir('../../'.dirname(__FILE__).$zip_name, 0777);
    @chmod('../../'.dirname(__FILE__).$zip_name, 0777);
    return $zip_name;
}
function unzip_file($file, $destination)
{
    // 实例化对象
    $zip = new ZipArchive() ;
    //打开zip文档，如果打开失败返回提示信息
    if ($zip->open($file) !== true) {
        die("Could not open archive");
    }
    //将压缩文件解压到指定的目录下
    $zip->extractTo($destination);
    //关闭zip文档
    $zip->close();
    echo 'Archive extracted to directory';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>基础表单</title>
    <link rel="stylesheet" href="../assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="../assets/module/admin.css?v=318"/>
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
            <!-- 开始 -->
            <a class="list-group-item">写入文件①（推荐）<?php if (is_writable('./')) {
                echo '<font color="green">可用√</font>';
            } else {
                echo '<font color="black">不支持</font>';
            }
            ?>
            </a>
            <?php
            $act = isset($_GET['act']) ? $_GET['act'] : null;
            switch ($act) {
                default:
                    echo '<div class="alert alert-info">当前为开源本地版，已禁用旧版远程自动更新与授权检测。</div>';
                    echo '<hr/>';
                    echo '<div class="well">如需升级数据库结构，请使用 /install/update.php；如需升级程序文件，请手动备份后覆盖新版源码。</div>';
                break;
                case 'do':
                    echo '<div class="alert alert-warning">开源本地版不再支持从旧授权服务器自动下载更新包，请手动更新源码。</div>';
                    echo '<a href="?">返回</a>';
                    break;
            }
            ?>
            <p><iframe src="../../readme.txt" style="width:100%;height:465px;"></iframe></p>
            <!-- //结束 -->
        </div>
    </div>
</div>

<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script src="../../Mym/Assets/Login/static/js/jquery-3.2.1.min.js"></script>
<script>
    layui.use(['layer', 'form', 'laydate'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var laydate = layui.laydate;

        /* 渲染laydate */
        laydate.render({
            elem: '#formBasDateSel',
            trigger: 'click',
            range: true
        });

        /* 监听表单提交 */
        form.on('submit(formBasSubmit)', function (data) {
            var loadIndex = layer.load(2);
            $.post('../Ajax.php?act=Set', data.field, function (res) {  // 实际项目这里url可以是mData?'user/update':'user/add'
            layer.close(loadIndex);
            if (res.code === 0) {
                layer.msg('设置保存成功！', {icon: 1});
            } else {
                layer.msg(res.msg, {icon: 2});
            }
                
            }, 'json');
            return false;
        });

    });
</script>
</body>
</html>