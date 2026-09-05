<?php
// 程序安装文件
error_reporting(0);
date_default_timezone_set("PRC");
@header('Content-Type: text/html; charset=UTF-8');

$databaseFile = '../Mym/Config.php'; // 数据库配置文件
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$step = ($step >= 1 && $step <= 5) ? $step : 1;
$jump = isset($_GET['jump']) ? intval($_GET['jump']) : 0;
$errorMsg = '';
$success = 0;
$error = 0;
$lock_status = true;

if (file_exists('install.lock')) {
    exit('<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>已安装</title><style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;font-family:Arial,"Microsoft YaHei",sans-serif;background:linear-gradient(135deg,#eef2ff,#f8fbff 45%,#ede9fe);color:#1f2937}.box{max-width:560px;margin:20px;padding:34px;border-radius:28px;background:rgba(255,255,255,.72);box-shadow:0 24px 80px rgba(91,33,182,.18),inset 0 1px 0 rgba(255,255,255,.9);border:1px solid rgba(255,255,255,.75);backdrop-filter:blur(18px);text-align:center}.box h2{margin:0 0 12px;color:#4c1d95}.box p{line-height:1.8;margin:0}</style></head><body><div class="box"><h2>系统已安装</h2><p>如需重新安装，请手动删除 <b>install/install.lock</b> 文件后再访问安装程序。</p></div></body></html>');
}

function clearpack() {
    $array = glob('../epay_release*');
    if (is_array($array)) {
        foreach ($array as $dir) {
            if (is_file($dir)) @unlink($dir);
        }
    }
    $array = glob('../epay_update*');
    if (is_array($array)) {
        foreach ($array as $dir) {
            if (is_file($dir)) @unlink($dir);
        }
    }
}

function random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}

function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function php_config_value($str) {
    return str_replace(array('\\', "'"), array('\\\\', "\\'"), (string)$str);
}

if ($step == 3) {
    if ($jump == 1) {
        if (file_exists($databaseFile)) {
            include $databaseFile;
        }
        if (empty($dbconfig['user']) || empty($dbconfig['pwd']) || empty($dbconfig['dbname'])) {
            $errorMsg = '请先填写好数据库并保存后再安装！';
        }
    } else {
        $host = isset($_POST['host']) ? trim($_POST['host']) : '';
        $port = isset($_POST['port']) ? trim($_POST['port']) : '3306';
        $user = isset($_POST['user']) ? trim($_POST['user']) : '';
        $pwd = isset($_POST['pwd']) ? trim($_POST['pwd']) : '';
        $database = isset($_POST['database']) ? trim($_POST['database']) : '';

        if ($host === '' || $port === '' || $user === '' || $pwd === '' || $database === '') {
            $errorMsg = '请填写完整所有数据库信息！';
        } elseif (!preg_match('/^[0-9]{1,5}$/', $port)) {
            $errorMsg = '数据库端口格式不正确！';
        }

        $dbconfig = array(
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'pwd' => $pwd,
            'dbname' => $database
        );

        $config = "<?php\n" .
            "/*数据库配置*/\n" .
            "\$dbconfig=array(\n" .
            "    'host' => '" . php_config_value($host) . "', //数据库服务器\n" .
            "    'port' => " . intval($port) . ", //数据库端口\n" .
            "    'user' => '" . php_config_value($user) . "', //数据库用户名\n" .
            "    'pwd' => '" . php_config_value($pwd) . "', //数据库密码\n" .
            "    'dbname' => '" . php_config_value($database) . "' //数据库名\n" .
            ");\n";
    }

    if (empty($errorMsg)) {
        try {
            $DB = new PDO("mysql:host=" . $dbconfig['host'] . ";dbname=" . $dbconfig['dbname'] . ";port=" . $dbconfig['port'], $dbconfig['user'], $dbconfig['pwd']);
        } catch (Exception $e) {
            if ($e->getCode() == 2002) {
                $errorMsg = '连接数据库失败：数据库地址填写错误或服务未启动！';
            } elseif ($e->getCode() == 1045) {
                $errorMsg = '连接数据库失败：数据库用户名或密码错误！';
            } elseif ($e->getCode() == 1049) {
                $errorMsg = '连接数据库失败：数据库名不存在！';
            } else {
                $errorMsg = '连接数据库失败：' . $e->getMessage();
            }
        }
        if (empty($errorMsg)) {
            $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $DB->exec("set sql_mode = ''");
            $DB->exec("set names utf8");
            $mysqlversion = $DB->query("select version()")->fetchColumn();
            if (version_compare($mysqlversion, '5.5.3', '<')) {
                $errorMsg = 'MySQL数据库版本太低，需要 MySQL 5.6 或以上版本！';
            }
            if ($jump != 1 && !file_put_contents($databaseFile, $config)) {
                $errorMsg = '保存失败，请确保网站根目录及 Mym/Config.php 有写入权限';
            }
        }
    }
} elseif ($step == 4) {
    if (file_exists($databaseFile)) {
        include $databaseFile;
    }
    if (empty($dbconfig['user']) || empty($dbconfig['pwd']) || empty($dbconfig['dbname'])) {
        $errorMsg = '请先填写好数据库并保存后再安装！';
    } else {
        try {
            $DB = new PDO("mysql:host=" . $dbconfig['host'] . ";dbname=" . $dbconfig['dbname'] . ";port=" . $dbconfig['port'], $dbconfig['user'], $dbconfig['pwd']);
        } catch (Exception $e) {
            $errorMsg = '连接数据库失败：' . $e->getMessage();
        }
        if (empty($errorMsg) && $jump != 1) {
            $dbqz = 'pay';
            $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $DB->exec("set sql_mode = ''");
            $DB->exec("set names utf8");
            $sqls = file_get_contents('install.sql');
            $sqls = explode(';', $sqls);
            $sqls[] = "INSERT INTO `pay_config` VALUES ('syskey', '" . random(32) . "')";
            $sqls[] = "INSERT INTO `pay_config` VALUES ('build', '" . date("Y-m-d") . "')";
            $success = 0;
            $error = 0;
            $errorMsg = '';
            foreach ($sqls as $value) {
                $value = trim($value);
                if (empty($value)) continue;
                $value = str_replace('pre_', $dbqz . '_', $value);
                if ($DB->exec($value) === false) {
                    $dberror = $DB->errorInfo();
                    if (!empty($dberror[2])) {
                        $error++;
                        $errorMsg .= h($dberror[2]) . "<br>";
                    } else {
                        $success++;
                    }
                } else {
                    $success++;
                }
            }
        }
        if (empty($errorMsg)) {
            $lock_status = file_put_contents("install.lock", '安装锁');
            clearpack();
            $step = 5;
        }
    }
}

$steps = array('阅读协议', '数据库配置', '环境检测', '写入数据', '安装完成');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Mym码支付 - 安装程序</title>
    <link href="../Mym/Assets/Login/static/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html { min-height: 100%; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Inter", "PingFang SC", "Microsoft YaHei", Arial, sans-serif;
            color: #172033;
            background:
                radial-gradient(circle at 15% 12%, rgba(124, 58, 237, .28), transparent 28%),
                radial-gradient(circle at 86% 18%, rgba(14, 165, 233, .22), transparent 30%),
                radial-gradient(circle at 78% 82%, rgba(236, 72, 153, .18), transparent 26%),
                linear-gradient(135deg, #f6f7ff 0%, #eef5ff 42%, #f8f1ff 100%);
            overflow-x: hidden;
        }
        body:before, body:after {
            content: "";
            position: fixed;
            width: 380px;
            height: 380px;
            border-radius: 999px;
            filter: blur(12px);
            opacity: .72;
            pointer-events: none;
            z-index: 0;
        }
        body:before { left: -130px; top: 90px; background: linear-gradient(135deg, rgba(99,102,241,.26), rgba(14,165,233,.18)); }
        body:after { right: -110px; bottom: -120px; background: linear-gradient(135deg, rgba(168,85,247,.22), rgba(244,114,182,.18)); }
        a { color: #5b21b6; text-decoration: none; }
        a:hover { color: #7c3aed; text-decoration: none; }
        .install-shell {
            position: relative;
            z-index: 1;
            width: min(1080px, calc(100% - 32px));
            margin: 38px auto;
        }
        .hero {
            position: relative;
            overflow: hidden;
            padding: 34px 36px;
            border-radius: 34px;
            color: #fff;
            background: linear-gradient(135deg, rgba(91,33,182,.95), rgba(124,58,237,.86) 48%, rgba(14,165,233,.78));
            box-shadow: 0 30px 100px rgba(79, 70, 229, .26);
        }
        .hero:before {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: 33px;
            border: 1px solid rgba(255,255,255,.36);
            pointer-events: none;
        }
        .hero h1 { margin: 0 0 10px; font-size: 32px; font-weight: 800; letter-spacing: .5px; }
        .hero p { margin: 0; opacity: .88; font-size: 15px; }
        .glass-card {
            margin-top: 22px;
            border-radius: 30px;
            border: 1px solid rgba(255,255,255,.72);
            background: rgba(255,255,255,.68);
            box-shadow: 0 24px 80px rgba(79,70,229,.15), inset 0 1px 0 rgba(255,255,255,.92);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            overflow: hidden;
        }
        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 24px 28px 10px;
        }
        .card-top h2 { margin: 0; font-size: 22px; font-weight: 800; color: #1f1b4d; }
        .badge-soft {
            display: inline-flex;
            align-items: center;
            height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            color: #4c1d95;
            background: rgba(124,58,237,.10);
            border: 1px solid rgba(124,58,237,.16);
            font-weight: 700;
        }
        .stepper {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            padding: 18px 28px 24px;
            margin: 0;
            list-style: none;
        }
        .step-item {
            position: relative;
            min-height: 86px;
            padding: 16px 14px;
            border-radius: 22px;
            background: rgba(255,255,255,.58);
            border: 1px solid rgba(255,255,255,.72);
            box-shadow: inset 8px 8px 18px rgba(148,163,184,.14), inset -8px -8px 18px rgba(255,255,255,.72);
            color: #64748b;
        }
        .step-no {
            display: inline-flex;
            width: 28px;
            height: 28px;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            border-radius: 50%;
            font-size: 13px;
            font-weight: 800;
            background: rgba(100,116,139,.12);
            color: #64748b;
        }
        .step-title { display: block; font-weight: 800; font-size: 14px; }
        .step-item.done, .step-item.active { color: #1f1b4d; }
        .step-item.done .step-no { background: rgba(16,185,129,.16); color: #059669; }
        .step-item.active {
            background: linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.56));
            border-color: rgba(124,58,237,.28);
            box-shadow: 0 16px 44px rgba(124,58,237,.16), inset 0 1px 0 rgba(255,255,255,.95);
        }
        .step-item.active .step-no { background: linear-gradient(135deg, #6d28d9, #0ea5e9); color: #fff; }
        .content-panel { padding: 0 28px 30px; }
        .glass-section {
            padding: 26px;
            border-radius: 26px;
            border: 1px solid rgba(255,255,255,.72);
            background: rgba(255,255,255,.56);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
        }
        .agreement {
            max-height: 430px;
            overflow: auto;
            padding-right: 8px;
            color: #243044;
            line-height: 1.86;
            font-size: 15px;
        }
        .agreement h3 { margin-top: 0; color: #312e81; font-size: 21px; font-weight: 800; }
        .agreement h4 { margin-top: 22px; color: #3b0764; font-weight: 800; }
        .form-horizontal .control-label { color: #334155; font-weight: 800; }
        .form-control {
            height: 46px;
            border-radius: 15px;
            border: 1px solid rgba(148,163,184,.28);
            background: rgba(255,255,255,.68);
            box-shadow: inset 5px 5px 12px rgba(148,163,184,.13), inset -5px -5px 12px rgba(255,255,255,.78);
        }
        .form-control:focus {
            border-color: rgba(124,58,237,.55);
            box-shadow: 0 0 0 4px rgba(124,58,237,.12), inset 0 1px 0 rgba(255,255,255,.9);
        }
        .btn {
            border: none;
            border-radius: 15px;
            padding: 11px 18px;
            font-weight: 800;
            transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
        }
        .btn:hover { transform: translateY(-1px); opacity: .96; }
        .btn-primary, .btn-success {
            color: #fff !important;
            background: linear-gradient(135deg, #6d28d9, #0ea5e9);
            box-shadow: 0 12px 28px rgba(79,70,229,.24);
        }
        .btn-warning { color: #fff !important; background: linear-gradient(135deg, #f59e0b, #ef4444); box-shadow: 0 12px 28px rgba(239,68,68,.18); }
        .btn-info { color: #fff !important; background: linear-gradient(135deg, #0891b2, #2563eb); }
        .btn-default, .btn-secondary { color: #4c1d95 !important; background: rgba(255,255,255,.72); border: 1px solid rgba(124,58,237,.18); }
        .alert {
            border: none;
            border-radius: 18px;
            line-height: 1.7;
            word-break: break-word;
        }
        .alert-success { color: #065f46; background: rgba(16,185,129,.14); }
        .alert-danger { color: #991b1b; background: rgba(239,68,68,.13); }
        .alert-info, .list-group-item-info { color: #075985; background: rgba(14,165,233,.12); }
        .action-list { display: grid; gap: 12px; margin-top: 14px; }
        .list-group, .list-group-item { border: none; background: transparent; box-shadow: none; }
        .list-group-item {
            margin-bottom: 10px;
            border-radius: 18px !important;
            background: rgba(255,255,255,.55);
            border: 1px solid rgba(255,255,255,.72);
        }
        .tips {
            margin-top: 12px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
        }
        .footer-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 20px;
        }
        @media (max-width: 820px) {
            .install-shell { width: calc(100% - 20px); margin: 16px auto; }
            .hero { padding: 26px 22px; border-radius: 26px; }
            .hero h1 { font-size: 25px; }
            .card-top { flex-direction: column; align-items: flex-start; padding: 20px 18px 8px; }
            .stepper { grid-template-columns: 1fr; padding: 14px 18px 18px; gap: 8px; }
            .step-item { min-height: auto; display: flex; align-items: center; gap: 10px; padding: 12px 14px; }
            .step-no { margin: 0; flex: 0 0 28px; }
            .content-panel { padding: 0 18px 22px; }
            .glass-section { padding: 18px; }
            .agreement { max-height: 390px; }
        }
    </style>
</head>
<body>
<div class="install-shell">
    <section class="hero">
        <h1>欢迎使用 Mym 码支付系统</h1>
        <p>玻璃拟态安装向导 · 检测环境、写入配置、安装数据表一步完成</p>
    </section>

    <section class="glass-card">
        <div class="card-top">
            <h2>安装向导</h2>
            <span class="badge-soft">第 <?php echo intval($step); ?> 步 / 共 5 步</span>
        </div>

        <ul class="stepper">
            <?php foreach ($steps as $index => $title) { $num = $index + 1; ?>
                <li class="step-item <?php echo $num < $step ? 'done' : ($num == $step ? 'active' : ''); ?>">
                    <span class="step-no"><?php echo $num < $step ? '✓' : $num; ?></span>
                    <span class="step-title"><?php echo h($title); ?></span>
                </li>
            <?php } ?>
        </ul>

        <div class="content-panel">
            <div class="glass-section">
                <?php if ($step == 1) { ?>
                    <div class="agreement">
                        <h3>Mym码支付系统使用协议</h3>
                        <p><strong>嗨，您好！欢迎使用 Mym 码支付系统。</strong></p>
                        <p>感谢您选择使用 Mym 码支付系统，Mym 码支付系统是稳定、强大、先进的码支付平台解决方案之一，采用 PHP + MySQL 技术开发，适合个人、中小规模工作室以及企业使用，可降低开发成本并提升客户服务效率。</p>
                        <p>本系统支持 H5 等收款相关能力，程序小巧、安装简单、页面自动适应电脑及手机，可开启或停用插件，操作方便。</p>
                        <h4>为了使你正确并合法地使用本软件，请在使用前阅读下面的协议条款：</h4>
                        <p>本授权协议适用且仅适用于 Mym 码支付系统任何版本，Mym 码支付系统官方对本授权协议保留最终解释权和修改权。</p>
                        <p><strong>承诺：</strong>您确认在成为用户之前已充分阅读、理解并接受本协议的全部内容，一旦使用本服务，即表示同意遵循本协议之所有约定。</p>
                        <h4>一、协议许可的权利</h4>
                        <p>1、您可以在完全遵守最终用户授权协议的基础上，将本软件应用于非商业用途。</p>
                        <p>2、您可以在协议规定的约束和限制范围内修改源代码或界面风格以适应网站要求。</p>
                        <p>3、您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容相关的法律义务。</p>
                        <h4>二、免责声明</h4>
                        <p>1、本软件及所附带的文件按现状提供，不提供任何明确或隐含担保。</p>
                        <p>2、用户出于自愿使用本软件，须了解使用风险，相关问题由使用者自行承担。</p>
                        <p>3、您一旦确认本协议并安装 Mym 码支付系统，即视为完全理解并接受本协议的各项条款。</p>
                        <h4>三、使用须知</h4>
                        <p>源码仅供内部分析研究，使用本产品后不得用于非法用途，不得违反国家法律法规，否则一切后果自行承担。</p>
                        <p>协议发布时间：2024年09月03日</p>
                        <p>Mym站长交流群：<a href="https://qm.qq.com/q/ysNSrCbnGi" target="_blank">点击加入</a></p>
                    </div>
                    <div class="footer-actions">
                        <a class="btn btn-primary" href="?step=2">同意协议，开始安装</a>
                    </div>
                <?php } elseif ($step == 2) { ?>
                    <form class="form-horizontal" action="?step=3" method="post" autocomplete="off">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">数据库地址</label>
                            <div class="col-sm-10"><input type="text" name="host" class="form-control" value="localhost" required></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">数据库端口</label>
                            <div class="col-sm-10"><input type="text" name="port" class="form-control" value="3306" required></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">数据库用户名</label>
                            <div class="col-sm-10"><input type="text" name="user" class="form-control" required></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">数据库密码</label>
                            <div class="col-sm-10"><input type="password" name="pwd" class="form-control" required></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">数据库名称</label>
                            <div class="col-sm-10"><input type="text" name="database" class="form-control" required></div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-success btn-block">确认无误，下一步</button>
                                <div class="tips">如果已事先填写好 Config.php 数据库配置，可 <a href="?step=3&jump=1">点击此处跳过配置</a>。</div>
                            </div>
                        </div>
                    </form>
                <?php } elseif ($step == 3) { ?>
                    <?php if (!empty($errorMsg)) { ?>
                        <div class="alert alert-danger text-center" role="alert"><?php echo $errorMsg; ?></div>
                        <div class="action-list"><a href="javascript:history.back(-1)" class="btn btn-info btn-block">返回上一页</a></div>
                    <?php } else { ?>
                        <div class="alert alert-success text-center" role="alert">数据库连接成功，配置文件保存成功！</div>
                        <?php if ($DB->query("select * from pay_config")) { ?>
                            <div class="list-group-item list-group-item-info text-center">检测到你已安装过 Mym 码支付</div>
                            <div class="action-list">
                                <a href="?step=4&jump=1" class="btn btn-info btn-block">跳过安装数据表</a>
                                <a href="?step=4" onclick="if(!confirm('全新安装将会清空或覆盖部分数据，是否继续？')){return false;}" class="btn btn-warning btn-block">强制全新安装</a>
                            </div>
                        <?php } else { ?>
                            <div class="action-list"><a href="?step=4" class="btn btn-success btn-block">立即安装数据表</a></div>
                        <?php } ?>
                    <?php } ?>
                <?php } elseif ($step == 4) { ?>
                    <div class="alert alert-danger" role="alert"><?php echo $errorMsg ? $errorMsg : '安装数据表失败，请检查数据库权限或 SQL 文件。'; ?></div>
                    <div class="action-list">
                        <a href="?step=4" class="btn btn-warning btn-block">点此重试</a>
                        <a href="javascript:history.back(-1)" class="btn btn-info btn-block">返回上一页</a>
                    </div>
                <?php } elseif ($step == 5) { ?>
                    <?php if ($success > 0) { ?><div class="alert alert-success" role="alert">成功执行 SQL 语句 <?php echo intval($success); ?> 条，失败 <?php echo intval($error); ?> 条。</div><?php } ?>
                    <ul class="list-group">
                        <li class="list-group-item">1、系统已成功安装完毕。</li>
                        <li class="list-group-item">2、后台地址：<a href="/Admin/" target="_blank">/Admin/</a>，默认密码：123456</li>
                        <li class="list-group-item">3、请及时修改后台管理员密码。</li>
                        <?php if (!$lock_status) { ?><li class="list-group-item"><span style="color:#dc2626;">4、当前空间不支持本地文件写入，请自行在 /install/ 目录建立 install.lock 文件。</span></li><?php } ?>
                        <li class="list-group-item"><a href="/" class="btn btn-primary btn-block">进入网站首页</a></li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </section>
</div>
<script src="../Mym/Assets/Login/static/js/jquery-3.2.1.min.js"></script>
<script src="../Mym/Assets/Login/doc/js/bootstrap.min.js"></script>
</body>
</html>
