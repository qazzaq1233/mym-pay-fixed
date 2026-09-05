<?php
// Vue/Vite 模板 API 兼容层：把新模板的 /api/... 请求映射到原 MYM 码支付数据结构。
// 该文件尽量只做适配，不改动原有 User/Ajax.php、Admin/Ajax.php 的旧接口。

$authorization = '';
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authorization = trim($_SERVER['HTTP_AUTHORIZATION']);
} elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $authorization = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
}
if (empty($_COOKIE['user_token']) && $authorization !== '') {
    $_COOKIE['user_token'] = preg_replace('/^Bearer\s+/i', '', $authorization);
}

include_once(dirname(__DIR__).'/Mym/Common.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: authorization, content-type, x-requested-with');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

function api_success($data = array(), $message = 'success', $extra = array())
{
    $payload = array_merge(array(
        'code' => 0,
        'message' => $message,
        'msg' => $message,
        'data' => $data,
    ), $extra);
    exit(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function api_error($message = '请求失败', $code = 400, $data = null)
{
    http_response_code($code === 401 ? 401 : 200);
    exit(json_encode(array(
        'code' => $code,
        'message' => $message,
        'msg' => $message,
        'data' => $data,
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function api_input($key, $default = '')
{
    if (isset($_POST[$key])) return trim($_POST[$key]);
    if (isset($_GET[$key])) return trim($_GET[$key]);
    return $default;
}

function api_route_path()
{
    $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    if ($path === '') {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
        if ($script && strpos($uri, $script) === 0) {
            $path = substr($uri, strlen($script));
        } else {
            $path = preg_replace('#^/api#', '', $uri);
        }
    }
    $path = '/'.trim($path, '/');
    $path = preg_replace('#^/index\.php/#i', '/', $path);
    $path = preg_replace('#/index\.php$#i', '', $path);
    return $path === '/' ? '/' : $path;
}

function api_site_config()
{
    global $conf, $siteurl;
    $siteName = isset($conf['sitename']) && $conf['sitename'] ? $conf['sitename'] : 'MYM码支付';
    $title = isset($conf['title']) && $conf['title'] ? $conf['title'] : $siteName;
    $favicon = $siteurl.'favicon.ico';
    return array(
        'site_name' => $siteName,
        'site_title' => $title,
        'site_logo' => $favicon,
        'site_favicon' => $favicon,
        'seo_keywords' => isset($conf['keywords']) ? $conf['keywords'] : '',
        'seo_description' => isset($conf['description']) ? $conf['description'] : '',
        'icp_beian' => isset($conf['footer']) ? $conf['footer'] : '',
        'qq' => isset($conf['qq']) ? $conf['qq'] : '',
        'customer_service_qq' => isset($conf['qq']) ? $conf['qq'] : '',
        'cc_login_enabled' => '0',
        'cc_login_url' => '',
        'cc_login_appid' => '',
        'cc_login_platforms' => '',
        'register_enabled' => isset($conf['reg_open']) ? (string)$conf['reg_open'] : '1',
    );
}

function api_require_login()
{
    global $islogin_user;
    if ($islogin_user != 1) {
        api_error('未登录或登录已过期', 401);
    }
}

function api_user_payload($row)
{
    return array(
        'user_id' => isset($row['pid']) ? $row['pid'] : '',
        'userId' => isset($row['pid']) ? $row['pid'] : '',
        'pid' => isset($row['pid']) ? $row['pid'] : '',
        'key' => isset($row['key']) ? $row['key'] : '',
        'username' => isset($row['user']) ? $row['user'] : '',
        'userName' => isset($row['user']) ? $row['user'] : '',
        'nickname' => !empty($row['nickname']) ? $row['nickname'] : (isset($row['user']) ? $row['user'] : ''),
        'email' => isset($row['email']) ? $row['email'] : '',
        'qq' => isset($row['qq']) ? $row['qq'] : '',
        'phone' => isset($row['phone']) ? $row['phone'] : '',
        'avatar' => '',
        'balance' => isset($row['money']) ? (float)$row['money'] : 0,
        'money' => isset($row['money']) ? (float)$row['money'] : 0,
        'rebate_balance' => 0,
        'recommend_code' => isset($row['pid']) ? $row['pid'] : '',
        'recommend_id' => null,
        'agent_id' => null,
        'roles' => array('R_USER'),
        'status' => isset($row['status']) ? (string)$row['status'] : '1',
        'created_at' => isset($row['addtime']) ? $row['addtime'] : '',
        'updated_at' => isset($row['addtime']) ? $row['addtime'] : '',
        'email_status' => isset($row['email_status']) ? (int)$row['email_status'] : 0,
        'user_vip' => isset($row['user_vip']) ? (int)$row['user_vip'] : 0,
        'user_vip_time' => isset($row['user_vip_time']) ? $row['user_vip_time'] : '',
    );
}

function api_table_columns($table)
{
    static $cache = array();
    global $DB;
    if (isset($cache[$table])) return $cache[$table];
    $cols = array();
    $stmt = $DB->query('SHOW COLUMNS FROM `pay_'.$table.'`');
    if ($stmt) {
        while ($row = $stmt->fetch()) {
            $cols[$row['Field']] = true;
        }
    }
    $cache[$table] = $cols;
    return $cols;
}

function api_column_exists($table, $column)
{
    $cols = api_table_columns($table);
    return isset($cols[$column]);
}

function api_login()
{
    global $DB, $conf, $password_hash, $date, $ip;
    $account = daddslashes(api_input('account', api_input('username', api_input('pid'))));
    $password = daddslashes(api_input('password', api_input('key')));
    if ($account === '' || $password === '') {
        api_error('账号和密码不能为空', 422);
    }

    $row = $DB->query("SELECT * FROM pay_user WHERE user='{$account}' OR email='{$account}' OR pid='{$account}' limit 1")->fetch();
    if (!$row || $row['pass'] !== $password) {
        api_error('登录失败，账号或密码错误', 400);
    }
    if (isset($row['status']) && (int)$row['status'] !== 1) {
        api_error('商户已被封禁，请联系管理员', 403);
    }

    $tokenId = !empty($row['user']) ? $row['user'] : $row['pid'];
    $tokenKey = !empty($row['user']) ? $row['pass'] : $row['key'];
    $session = md5($tokenId.$tokenKey.$password_hash);
    $expiretime = time() + 604800;
    $token = authcode("{$tokenId}\t{$session}\t{$expiretime}", 'ENCODE', $conf['KEY']);
    setcookie('user_token', $token, time() + 604800, '/');

    if (function_exists('Add_log')) {
        Add_log($row['pid'], '使用新版模板登录成功');
    }
    $DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('".$row['pid']."','新版模板登录','".$date."','".$ip."','')");

    api_success(api_user_payload($row), '登录成功', array('token' => $token));
}

function api_register()
{
    global $DB, $conf, $date, $ip;
    $user = daddslashes(trim(strip_tags(api_input('username', api_input('user')))));
    $pass = daddslashes(trim(strip_tags(api_input('password', api_input('pass')))));
    $qq = daddslashes(trim(strip_tags(api_input('qq'))));
    $email = daddslashes(trim(strip_tags(api_input('email'))));

    if (isset($conf['reg_open']) && (int)$conf['reg_open'] === 0) api_error('未开放商户申请', 403);
    if ($user === '' || $pass === '') api_error('账号和密码不能为空', 422);
    if (strlen($user) < 5 || strlen($pass) < 6) api_error('请填写 5 位以上账号和 6 位以上密码', 422);
    if ($email !== '' && !preg_match('/^[A-z0-9._-]+@[A-z0-9._-]+\.[A-z0-9._-]+$/', $email)) api_error('邮箱格式不正确', 422);
    if ($DB->query("select * from pay_user where user='{$user}' limit 1")->fetch()) api_error('该用户名已存在', 409);
    if ($email !== '' && $DB->query("select * from pay_user where email='{$email}' limit 1")->fetch()) api_error('该邮箱已经注册过商户', 409);
    if ($qq !== '' && $DB->query("select * from pay_user where qq='{$qq}' limit 1")->fetch()) api_error('当前 QQ 已存在', 409);

    $pid = '1'.mt_rand(10000000, 99999999);
    while ($DB->query("select pid from pay_user where pid='{$pid}' limit 1")->fetch()) {
        $pid = '1'.mt_rand(10000000, 99999999);
    }
    $key = random(11);
    $money = isset($conf['reg_money']) && $conf['reg_money'] !== '' ? $conf['reg_money'] : '0.00';
    $type = isset($conf['reg_type']) && $conf['reg_type'] !== '' ? $conf['reg_type'] : '3';
    $emailStatus = isset($conf['reg_email']) && $conf['reg_email'] !== '' ? $conf['reg_email'] : '1';

    $ok = $DB->exec("INSERT INTO `pay_user` (`user`,`pass`,`pid`,`key`,`qq`,`money`,`email`,`type`,`outtime`,`addtime`,`email_status`,`status`) VALUES ('{$user}','{$pass}','{$pid}','{$key}','{$qq}','{$money}','{$email}','{$type}','180','{$date}','{$emailStatus}','1')");
    if (!$ok) api_error('申请商户失败：'.$DB->error(), 500);
    if (function_exists('Add_log')) Add_log($pid, '新版模板注册商户成功');
    $row = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
    api_success(api_user_payload($row), '申请商户成功');
}

function api_profile()
{
    global $DB, $userrow;
    api_require_login();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
        $updates = array();
        foreach (array('nickname', 'email', 'qq') as $field) {
            $value = api_input($field, null);
            if ($value !== null && api_column_exists('user', $field)) {
                $updates[$field] = daddslashes(strip_tags($value));
            }
        }
        if (!empty($updates)) {
            $sets = array();
            foreach ($updates as $k => $v) {
                $sets[] = "`{$k}`='{$v}'";
            }
            $DB->exec("UPDATE pay_user SET ".implode(',', $sets)." WHERE pid='{$userrow['pid']}'");
            $userrow = $DB->query("SELECT * FROM pay_user WHERE pid='{$userrow['pid']}' limit 1")->fetch();
        }
    }
    api_success(api_user_payload($userrow));
}

function api_dashboard()
{
    global $DB, $userrow;
    api_require_login();
    $pid = daddslashes($userrow['pid']);
    $today = date('Y-m-d 00:00:00');
    $month = date('Y-m-01 00:00:00');
    $orders = (int)$DB->query("SELECT count(*) FROM pay_order WHERE pid='{$pid}'")->fetchColumn();
    $paidOrders = (int)$DB->query("SELECT count(*) FROM pay_order WHERE pid='{$pid}' AND status=1")->fetchColumn();
    $pendingOrders = (int)$DB->query("SELECT count(*) FROM pay_order WHERE pid='{$pid}' AND status=0")->fetchColumn();
    $todayOrders = (int)$DB->query("SELECT count(*) FROM pay_order WHERE pid='{$pid}' AND addtime>='{$today}'")->fetchColumn();
    $revenue = (float)$DB->query("SELECT IFNULL(SUM(money),0) FROM pay_order WHERE pid='{$pid}' AND status=1")->fetchColumn();
    $todayRevenue = (float)$DB->query("SELECT IFNULL(SUM(money),0) FROM pay_order WHERE pid='{$pid}' AND status=1 AND addtime>='{$today}'")->fetchColumn();
    $monthly = (float)$DB->query("SELECT IFNULL(SUM(money),0) FROM pay_order WHERE pid='{$pid}' AND status=1 AND addtime>='{$month}'")->fetchColumn();
    $channels = (int)$DB->query("SELECT count(*) FROM pay_qrlist WHERE pid='{$pid}' AND hook_type!=3")->fetchColumn();
    $onlineChannels = (int)$DB->query("SELECT count(*) FROM pay_qrlist WHERE pid='{$pid}' AND hook_type!=3 AND status=1 AND qr_status=1")->fetchColumn();
    $recentOrders = array();
    $stmt = $DB->query("SELECT trade_no,out_trade_no,name,money,type,status,addtime,endtime FROM pay_order WHERE pid='{$pid}' ORDER BY addtime DESC LIMIT 8");
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $recentOrders[] = array(
                'trade_no' => $row['trade_no'],
                'out_trade_no' => $row['out_trade_no'],
                'name' => $row['name'],
                'money' => isset($row['money']) ? (float)$row['money'] : 0,
                'type' => $row['type'],
                'status' => (int)$row['status'],
                'status_text' => ((int)$row['status'] === 1 ? '已支付' : ((int)$row['status'] === 2 ? '已过期' : '待支付')),
                'addtime' => $row['addtime'],
                'endtime' => $row['endtime'],
            );
        }
    }
    api_success(array(
        'nickname' => !empty($userrow['nickname']) ? $userrow['nickname'] : $userrow['user'],
        'pid' => isset($userrow['pid']) ? $userrow['pid'] : '',
        'user' => isset($userrow['user']) ? $userrow['user'] : '',
        'balance' => isset($userrow['money']) ? (float)$userrow['money'] : 0,
        'settle_balance' => isset($userrow['settle_money']) ? (float)$userrow['settle_money'] : 0,
        'monthly_revenue' => $monthly,
        'today_revenue' => $todayRevenue,
        'orders_count' => $orders,
        'paid_orders_count' => $paidOrders,
        'pending_orders_count' => $pendingOrders,
        'today_orders_count' => $todayOrders,
        'revenue' => $revenue,
        'channels_count' => $channels,
        'online_channels_count' => $onlineChannels,
        'rate' => isset($userrow['rate']) ? $userrow['rate'] : '',
        'recent_orders' => $recentOrders,
    ));
}

function api_orders()
{
    global $DB, $userrow;
    api_require_login();
    $pid = daddslashes($userrow['pid']);
    $page = max(1, (int)api_input('page', 1));
    $limit = max(1, min(100, (int)api_input('limit', 20)));
    $offset = ($page - 1) * $limit;
    $total = (int)$DB->query("SELECT count(*) FROM pay_order WHERE pid='{$pid}'")->fetchColumn();
    $stmt = $DB->query("SELECT * FROM pay_order WHERE pid='{$pid}' ORDER BY addtime DESC LIMIT {$offset},{$limit}");
    $rows = $stmt ? $stmt->fetchAll() : array();
    $list = array();
    foreach ($rows as $row) {
        $status = ((int)$row['status'] === 1) ? 'paid' : 'pending';
        if ((int)$row['status'] === 2) $status = 'expired';
        $list[] = array(
            'id' => $row['trade_no'],
            'trade_no' => $row['trade_no'],
            'out_trade_no' => $row['out_trade_no'],
            'order_type' => 'recharge',
            'product' => array('name' => $row['name']),
            'package' => null,
            'title' => $row['name'],
            'amount' => isset($row['money']) ? (float)$row['money'] : 0,
            'money' => isset($row['money']) ? (float)$row['money'] : 0,
            'payment_method' => $row['type'] === 'wxpay' ? 'wechat' : ($row['type'] === 'qqpay' ? 'qqpay' : $row['type']),
            'status' => $status,
            'created_at' => $row['addtime'],
            'updated_at' => $row['endtime'],
            'pay_url' => '',
        );
    }
    api_success($list, 'success', array(
        'total' => $total,
        'current_page' => $page,
        'per_page' => $limit,
        'list' => $list,
    ));
}

function api_balance_logs()
{
    global $DB, $userrow;
    api_require_login();
    $pid = daddslashes($userrow['pid']);
    $page = max(1, (int)api_input('page', 1));
    $limit = max(1, min(100, (int)api_input('limit', 20)));
    $offset = ($page - 1) * $limit;
    $total = (int)$DB->query("SELECT count(*) FROM pay_log WHERE pid='{$pid}'")->fetchColumn();
    $stmt = $DB->query("SELECT * FROM pay_log WHERE pid='{$pid}' ORDER BY date DESC LIMIT {$offset},{$limit}");
    $rows = $stmt ? $stmt->fetchAll() : array();
    $list = array();
    foreach ($rows as $row) {
        $list[] = array(
            'id' => $row['id'],
            'amount' => 0.00,
            'balance_before' => isset($userrow['money']) ? (float)$userrow['money'] : 0,
            'balance_after' => isset($userrow['money']) ? (float)$userrow['money'] : 0,
            'type' => 'admin_adjust',
            'remark' => $row['type'],
            'created_at' => $row['date'],
        );
    }
    api_success($list, 'success', array('total' => $total, 'current_page' => $page, 'per_page' => $limit, 'list' => $list));
}

function api_payment_config()
{
    global $conf;
    $alipay = !isset($conf['alipay_api']) || $conf['alipay_api'] !== '0';
    api_success(array(
        'pay_method_alipay' => '1',
        'pay_method_wechat' => '1',
        'pay_method_wxpay' => '1',
        'pay_method_qqpay' => '1',
        'pay_method_balance' => '1',
        'methods' => array(
            array('value' => 'alipay', 'label' => '支付宝', 'icon' => 'ri:alipay-line'),
            array('value' => 'wechat', 'label' => '微信支付', 'icon' => 'ri:wechat-pay-line'),
            array('value' => 'qqpay', 'label' => 'QQ钱包', 'icon' => 'ri:qq-line'),
            array('value' => 'balance', 'label' => '余额', 'icon' => 'ri:wallet-3-line'),
        ),
    ));
}

function api_purchase_info()
{
    global $DB, $userrow;
    api_require_login();
    $packages = array();
    $stmt = $DB->query("SELECT * FROM pay_taocan WHERE status=1 ORDER BY sort ASC,id ASC");
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $packages[] = array(
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'price' => (float)$row['money'],
                'amount' => (float)$row['money'],
                'quota' => (float)$row['edu'],
                'edu' => (float)$row['edu'],
                'days' => isset($row['time']) ? (int)$row['time'] : 0,
                'description' => '购买额度：'.$row['edu'],
            );
        }
    }
    api_success(array(
        'balance' => isset($userrow['money']) ? (float)$userrow['money'] : 0,
        'packages' => $packages,
        'products' => array(),
        'payment_methods' => array('alipay', 'wechat', 'qqpay', 'balance'),
    ));
}

function api_recharge()
{
    global $siteurl, $userrow;
    api_require_login();
    $amount = (float)api_input('amount', 0);
    $method = api_input('payment_method', 'alipay');
    if ($amount <= 0) api_error('请输入有效的充值金额', 422);
    $typeMap = array('wechat' => 'wxpay', 'wxpay' => 'wxpay', 'alipay' => 'alipay', 'qqpay' => 'qqpay', 'qq' => 'qqpay', 'usdt' => 'usdt');
    $type = isset($typeMap[$method]) ? $typeMap[$method] : 'alipay';
    $outTradeNo = date('YmdHis').mt_rand(100, 999);
    $subject = $userrow['pid'].'额度充值';
    $payUrl = $siteurl.'User/SDK/epayapi.php?WIDout_trade_no='.rawurlencode($outTradeNo).'&WIDsubject='.rawurlencode($subject).'&WIDtotal_fee='.rawurlencode(number_format($amount, 2, '.', '')).'&type='.rawurlencode($type);
    api_success(array(
        'pay_url' => $payUrl,
        'trade_no' => $outTradeNo,
        'amount' => $amount,
        'payment_method' => $method,
    ), '订单已创建');
}

function api_popup_announcements()
{
    global $DB, $conf;
    $list = array();
    $stmt = $DB->query("SELECT * FROM pay_notice WHERE status=1 ORDER BY sort DESC,id DESC LIMIT 5");
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $list[] = array(
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'content' => $row['datatxt'],
                'color' => $row['color'],
                'created_at' => $row['addtime'],
            );
        }
    }
    if (!$list && !empty($conf['gonggao'])) {
        $list[] = array('id' => 1, 'title' => '系统公告', 'content' => $conf['gonggao'], 'color' => '#0052cc', 'created_at' => date('Y-m-d H:i:s'));
    }
    api_success($list);
}

function api_clean_text($value)
{
    return trim(html_entity_decode(strip_tags((string)$value), ENT_QUOTES, 'UTF-8'));
}

function api_channel_hook_label($row)
{
    if (isset($row['type']) && $row['type'] === 'alipay' && isset($row['channel']) && $row['channel'] === 'yd_ali') {
        return '免挂';
    }
    if (function_exists('type_yun')) return api_clean_text(type_yun($row));
    if (isset($row['hook_type']) && (int)$row['hook_type'] === 1) return '挂机';
    if (isset($row['hook_type']) && (int)$row['hook_type'] === 2) return '云端';
    return '免挂';
}

function api_channel_type_name($row)
{
    $channels = function_exists('mym_pay_channel_list') ? mym_pay_channel_list(null, false) : array();
    $types = function_exists('mym_pay_type_list') ? mym_pay_type_list(false) : array();
    if (isset($row['channel']) && isset($channels[$row['channel']])) return $channels[$row['channel']]['name'];
    if (isset($row['type']) && isset($types[$row['type']])) return $types[$row['type']]['name'];
    return isset($row['type']) ? $row['type'] : '';
}

function api_channels_config()
{
    global $DB, $userrow, $user_pass;
    api_require_login();
    $types = function_exists('mym_pay_type_list') ? mym_pay_type_list(true) : array();
    $channelsRaw = function_exists('mym_pay_channel_list') ? mym_pay_channel_list(null, true) : array();
    $channels = array();
    foreach ($types as $code => $item) {
        $channels[$code] = array();
    }
    foreach ($channelsRaw as $code => $item) {
        $type = isset($item['type']) ? $item['type'] : '';
        if (!isset($channels[$type])) $channels[$type] = array();
        $channels[$type][] = array(
            'code' => $code,
            'type' => $type,
            'name' => isset($item['name']) ? $item['name'] : $code,
            'status' => isset($item['status']) ? (int)$item['status'] : 1,
            'sort' => isset($item['sort']) ? (int)$item['sort'] : 0,
        );
    }
    $typeList = array();
    foreach ($types as $code => $item) {
        $typeList[] = array(
            'code' => $code,
            'name' => isset($item['name']) ? $item['name'] : $code,
            'status' => isset($item['status']) ? (int)$item['status'] : 1,
            'sort' => isset($item['sort']) ? (int)$item['sort'] : 0,
        );
    }
    $total = $DB->query("SELECT count(*) FROM pay_qrlist WHERE pid='{$userrow['pid']}'")->fetchColumn();
    api_success(array(
        'types' => $typeList,
        'channels' => $channels,
        'total' => (int)$total,
        'max' => isset($userrow['type']) ? (int)$userrow['type'] : 0,
        'payPassVerified' => $user_pass ? true : false,
        'payPassSet' => !empty($userrow['pay_pass']),
    ));
}

function api_channels_list()
{
    global $DB, $userrow, $user_pass;
    api_require_login();
    if (!$user_pass) {
        api_success(array('list' => array(), 'needsPayPass' => true), '需要验证二级密码', array('total' => 0, 'current_page' => 1, 'per_page' => 20));
    }
    $page = max(1, (int)api_input('page', 1));
    $limit = max(1, min(100, (int)api_input('limit', 30)));
    $offset = ($page - 1) * $limit;
    $pid = daddslashes($userrow['pid']);
    $total = (int)$DB->query("SELECT count(*) FROM pay_qrlist WHERE pid='{$pid}' and hook_type!=3")->fetchColumn();
    $stmt = $DB->query("SELECT * FROM pay_qrlist WHERE pid='{$pid}' and hook_type!=3 ORDER BY addtime DESC LIMIT {$offset},{$limit}");
    $list = array();
    if ($stmt) {
        while ($row = $stmt->fetch()) {
            $json = json_decode(isset($row['json']) ? $row['json'] : '', true);
            if (!is_array($json)) $json = array();
            $nameParts = explode('|', isset($row['data_data']) ? $row['data_data'] : '');
            $statusInfo = function_exists('cookie_zt') ? cookie_zt($row) : array('msg' => '', 'status' => false);
            $receiver = isset($json['receiver_surname']) ? (function_exists('mym_restore_unicode_text') ? mym_restore_unicode_text($json['receiver_surname']) : $json['receiver_surname']) : '';
            $list[] = array(
                'id' => (int)$row['id'],
                'pid' => isset($row['pid']) ? $row['pid'] : '',
                'type' => isset($row['type']) ? $row['type'] : '',
                'channel' => isset($row['channel']) ? $row['channel'] : '',
                'typeName' => api_channel_type_name($row),
                'hookType' => isset($row['hook_type']) ? (int)$row['hook_type'] : 0,
                'hookLabel' => api_channel_hook_label($row),
                'beizhu' => isset($row['beizhu']) ? $row['beizhu'] : '',
                'qrUrl' => isset($row['qr_url']) ? $row['qr_url'] : '',
                'customQrUrl' => isset($json['custom_qr_url']) ? $json['custom_qr_url'] : '',
                'receiverSurname' => $receiver,
                'money' => isset($row['money']) ? (float)$row['money'] : 0,
                'moneyText' => function_exists('WxMoney') ? api_clean_text(WxMoney($row)) : ('￥ '.(isset($row['money']) ? $row['money'] : '0.00')),
                'status' => isset($row['status']) ? (int)$row['status'] : 0,
                'online' => !empty($statusInfo['status']),
                'statusText' => isset($statusInfo['msg']) ? api_clean_text($statusInfo['msg']) : '',
                'qrStatus' => isset($row['qr_status']) ? (int)$row['qr_status'] : 0,
                'qrStatusText' => (isset($row['qr_status']) && (int)$row['qr_status'] === 1) ? '已开启' : '已关闭',
                'accountText' => isset($nameParts[1]) ? $nameParts[1] : '',
                'addtime' => isset($row['addtime']) ? $row['addtime'] : '',
                'crontime' => isset($row['crontime']) ? (int)$row['crontime'] : 0,
                'json' => $json,
            );
        }
    }
    api_success(array('list' => $list, 'needsPayPass' => false), 'success', array('total' => $total, 'current_page' => $page, 'per_page' => $limit));
}

function api_channel_pay_pass()
{
    global $userrow, $conf;
    api_require_login();
    $raw = trim(api_input('pay_pass', ''));
    if ($raw === '') api_error('二级密码不能为空', 422);
    if (empty($userrow['pay_pass'])) api_success(array('redirect' => '/finance/userinfo'), '您还没有设置二级密码');
    $payPass = md5(daddslashes($raw));
    if ($userrow['pay_pass'] == $payPass) {
        setcookie('pay_pass', authcode($payPass, 'ENCODE', $conf['KEY']), time() + 43200, '/');
        api_success(array(), '验证成功');
    }
    api_error('验证失败，二级密码错误', 400);
}

function api_menus()
{
    api_success(array(
        array(
            'name' => 'LegacyProfileShortcut',
            'path' => '/account/profile',
            'meta' => array('title' => 'API / 资料', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/userinfo.php?iframe=1', 'hideInMenu' => true),
        ),
        array(
            'name' => 'Dashboard',
            'path' => '/dashboard',
            'component' => '/dashboard/console',
            'meta' => array('title' => '商户中心', 'icon' => 'ri:pie-chart-line', 'roles' => array('R_USER'), 'keepAlive' => false, 'fixedTab' => true),
        ),
        array(
            'name' => 'OrderManage',
            'path' => '/order-manage',
            'meta' => array('title' => '订单管理', 'icon' => 'ri:file-list-3-line', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/Order.php?iframe=1'),
        ),
        array(
            'name' => 'ChannelManage',
            'path' => '/channel',
            'component' => '/index/index',
            'meta' => array('title' => '通道管理', 'icon' => 'ri:bank-card-line', 'roles' => array('R_USER')),
            'children' => array(
                array('path' => 'qrlist', 'name' => 'QrChannelList', 'component' => '/channel/qrlist', 'meta' => array('title' => '通道列表', 'roles' => array('R_USER'))),
                array('path' => 'dmf', 'name' => 'FacePayChannel', 'meta' => array('title' => '当面付通道', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/Free_dmf.php?iframe=1')),
            ),
        ),
        array(
            'name' => 'FinanceApi',
            'path' => '/finance',
            'component' => '/index/index',
            'meta' => array('title' => '财务与接口', 'icon' => 'ri:wallet-3-line', 'roles' => array('R_USER')),
            'children' => array(
                array('path' => 'recharge', 'name' => 'LegacyRecharge', 'meta' => array('title' => '立即充值', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/Pay_Vip.php?iframe=1')),
                array('path' => 'pay-setting', 'name' => 'LegacyPaySetting', 'meta' => array('title' => '支付设置', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/Set.php?iframe=1')),
                array('path' => 'userinfo', 'name' => 'LegacyUserInfo', 'meta' => array('title' => 'API / 资料', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/userinfo.php?iframe=1')),
                array('path' => 'package', 'name' => 'LegacyPackage', 'meta' => array('title' => '套餐购买', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/taocan.php?iframe=1')),
                array('path' => 'plugin', 'name' => 'LegacyPlugin', 'meta' => array('title' => '插件市场', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/plug.php?iframe=1')),
            ),
        ),
        array(
            'name' => 'LogoutLegacy',
            'path' => '/logout-legacy',
            'meta' => array('title' => '退出登录', 'icon' => 'ri:logout-box-r-line', 'roles' => array('R_USER'), 'isIframe' => true, 'link' => '/User/Ajax2.php?act=logout'),
        ),
    ));
}

function api_empty_page()
{
    api_success(array(), 'success', array('total' => 0, 'current_page' => 1, 'per_page' => 20, 'list' => array()));
}

function api_feature_disabled($feature = '该功能')
{
    api_success(array(
        'enabled' => false,
        'message' => $feature.'暂未接入原 MYM 码支付系统，已做兼容占位。',
    ), $feature.'暂未开放');
}

function api_article_detail()
{
    $id = (int)api_input('id', 0);
    if ($id <= 0) {
        api_error('文章不存在', 404);
    }
    api_success(array(
        'id' => $id,
        'title' => '文章内容暂未接入',
        'author' => 'system',
        'content' => '<p>当前 MYM 码支付原系统没有对应文章详情表，接口已兼容，后续可接入公告或文章模块。</p>',
        'created_at' => date('Y-m-d H:i:s'),
    ));
}

function api_ticket_detail()
{
    $id = (int)api_input('id', 0);
    if ($id <= 0) {
        api_error('工单不存在', 404);
    }
    api_success(array(
        'id' => $id,
        'subject' => '工单功能暂未接入',
        'content' => '当前原系统没有工单数据表，接口已做兼容占位。',
        'status' => 'closed',
        'replies' => array(),
        'created_at' => date('Y-m-d H:i:s'),
    ));
}

function api_social_bindings()
{
    api_success(array(
        'qq' => null,
        'wechat' => null,
        'github' => null,
        'items' => array(),
    ));
}

function api_send_reset_email()
{
    api_feature_disabled('邮箱找回密码');
}

function api_reset_password()
{
    api_feature_disabled('重置密码');
}

function api_placeholder_success()
{
    api_success(array());
}

function api_public_ok()
{
    api_success(array('ok' => true, 'public' => true));
}

function api_index_send_code()
{
    $qq = api_input('qq');
    $email = api_input('email');
    if ($qq === '' || $email === '') {
        api_error('请填写 QQ 和邮箱', 422);
    }
    api_success(array(
        'sent' => true,
        'enabled' => false,
    ), '下载验证码模块暂未接入邮件发送，接口已兼容');
}

function api_index_verify_download()
{
    api_success(array(
        'verified' => false,
        'enabled' => false,
        'download_url' => '',
        'update_url' => '',
        'message' => '下载验证模块暂未接入原 MYM 码支付系统，已做兼容占位。',
    ), '下载验证模块暂未开放');
}

function api_admin_login()
{
    global $conf, $password_hash, $DB, $date, $ip;
    $adminUser = daddslashes(api_input('admin_user', api_input('username', api_input('account'))));
    $adminPass = daddslashes(api_input('admin_pass', api_input('password', api_input('pass'))));
    $code = daddslashes(api_input('code'));
    if ($adminUser === '' || $adminPass === '') {
        api_error('后台账号和密码不能为空', 422);
    }
    if (md5($adminUser) !== $conf['admin_user']) {
        api_error('登录失败，账号错误', 400);
    }
    if (md5($adminPass) !== $conf['admin_pass']) {
        api_error('登录失败，密码错误', 400);
    }
    if (!empty($conf['goid'])) {
        if ($code === '') {
            api_error('请输入谷歌验证码', 422);
        }
        $ga = new \lib\GoogleAuthenticator();
        if (!$ga->verifyCode($conf['goid'], $code, 1)) {
            api_error('登录失败，谷歌验证码错误', 400);
        }
    }
    $session = md5($conf['admin_user'].$conf['admin_pass'].$password_hash);
    $token = authcode("{$adminUser}\t{$session}", 'ENCODE', $conf['KEY']);
    setcookie('admin_token', $token, time() + 604800, '/');
    if (isset($DB)) {
        $city = function_exists('get_ip_city') ? get_ip_city($ip)['Result']['Country'] : '';
        $DB->exec("insert into `pay_log` (`pid`,`type`,`date`,`ip`,`city`) values ('0','新版后台管理员登陆','".$date."','".$ip."','".$city."')");
    }
    api_success(api_admin_profile_payload(), '登录成功', array('token' => $token));
}

function api_admin_captcha_config()
{
    api_success(array('captcha_type' => 'none', 'geetest_id' => '', 'enabled' => false));
}

function api_admin_captcha_image()
{
    api_success(array('image' => '', 'captcha_key' => '', 'enabled' => false));
}

function api_admin_upload_image()
{
    global $siteurl;
    api_require_admin();
    if (empty($_FILES)) {
        api_success(array('url' => '', 'src' => '', 'path' => ''), '暂无上传文件');
    }
    $file = reset($_FILES);
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        api_error('上传文件无效', 422);
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'))) {
        api_error('不支持的图片格式', 422);
    }
    $dir = dirname(__DIR__).'/assets/upload/vue/';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $name = date('YmdHis').mt_rand(1000, 9999).'.'.$ext;
    $target = $dir.$name;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        api_error('保存上传文件失败', 500);
    }
    $url = $siteurl.'assets/upload/vue/'.$name;
    api_success(array('url' => $url, 'src' => $url, 'path' => 'assets/upload/vue/'.$name), '上传成功');
}

function api_require_admin()
{
    global $islogin_admin;
    if ($islogin_admin != 1) {
        api_error('后台登录已过期，请重新登录', 401);
    }
}

function api_admin_profile_payload()
{
    global $conf;
    return array(
        'id' => 1,
        'user_id' => 1,
        'username' => 'admin',
        'userName' => 'admin',
        'nickname' => '管理员',
        'avatar' => '',
        'roles' => array('R_SUPER', 'R_ADMIN'),
        'email' => '',
        'qq' => isset($conf['qq']) ? $conf['qq'] : '',
        'status' => 1,
    );
}

function api_admin_count($table, $where = '1')
{
    global $DB;
    return (int)$DB->query("SELECT count(*) FROM `pay_{$table}` WHERE {$where}")->fetchColumn();
}

function api_admin_sum($table, $field, $where = '1')
{
    global $DB;
    $value = $DB->query("SELECT IFNULL(SUM(`{$field}`),0) FROM `pay_{$table}` WHERE {$where}")->fetchColumn();
    return round((float)$value, 2);
}

function api_admin_metric($table, $dateField, $sumField = '')
{
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $week = date('Y-m-d 00:00:00', strtotime('-6 days'));
    $month = date('Y-m-01 00:00:00');
    if ($sumField) {
        return array(
            'total' => api_admin_sum($table, $sumField),
            'today' => api_admin_sum($table, $sumField, "{$dateField}>='{$today} 00:00:00' AND {$dateField}<='{$today} 23:59:59'"),
            'yesterday' => api_admin_sum($table, $sumField, "{$dateField}>='{$yesterday} 00:00:00' AND {$dateField}<='{$yesterday} 23:59:59'"),
            'week' => api_admin_sum($table, $sumField, "{$dateField}>='{$week}'"),
            'month' => api_admin_sum($table, $sumField, "{$dateField}>='{$month}'"),
        );
    }
    return array(
        'total' => api_admin_count($table),
        'today' => api_admin_count($table, "{$dateField}>='{$today} 00:00:00' AND {$dateField}<='{$today} 23:59:59'"),
        'yesterday' => api_admin_count($table, "{$dateField}>='{$yesterday} 00:00:00' AND {$dateField}<='{$yesterday} 23:59:59'"),
        'week' => api_admin_count($table, "{$dateField}>='{$week}'"),
        'month' => api_admin_count($table, "{$dateField}>='{$month}'"),
    );
}

function api_admin_order_status($status)
{
    $status = (int)$status;
    if ($status === 1) return 'paid';
    if ($status === 2) return 'expired';
    if ($status === 3) return 'cancelled';
    return 'pending';
}

function api_admin_order_payload($row)
{
    return array(
        'id' => $row['trade_no'],
        'trade_no' => $row['trade_no'],
        'out_trade_no' => $row['out_trade_no'],
        'api_trade_no' => isset($row['api_trade_no']) ? $row['api_trade_no'] : '',
        'pid' => $row['pid'],
        'user_id' => $row['pid'],
        'username' => $row['pid'],
        'name' => $row['name'],
        'title' => $row['name'],
        'product_name' => $row['name'],
        'type' => $row['type'],
        'payment_method' => $row['type'],
        'money' => isset($row['money']) ? (float)$row['money'] : 0,
        'price' => isset($row['price']) ? (float)$row['price'] : 0,
        'amount' => isset($row['money']) ? (float)$row['money'] : 0,
        'status' => api_admin_order_status($row['status']),
        'status_value' => (int)$row['status'],
        'notify_url' => isset($row['notify_url']) ? $row['notify_url'] : '',
        'return_url' => isset($row['return_url']) ? $row['return_url'] : '',
        'created_at' => $row['addtime'],
        'updated_at' => $row['endtime'],
        'addtime' => $row['addtime'],
        'endtime' => $row['endtime'],
    );
}

function api_admin_page_params()
{
    $page = max(1, (int)api_input('current', api_input('page', 1)));
    $limit = max(1, min(100, (int)api_input('size', api_input('limit', 20))));
    return array($page, $limit, ($page - 1) * $limit);
}

function api_admin_page_success($list, $total, $page, $limit)
{
    api_success(array(
        'records' => $list,
        'list' => $list,
        'rows' => $list,
        'items' => $list,
        'total' => (int)$total,
        'count' => (int)$total,
        'current' => (int)$page,
        'page' => (int)$page,
        'size' => (int)$limit,
        'limit' => (int)$limit,
    ), 'success', array(
        'records' => $list,
        'list' => $list,
        'total' => (int)$total,
        'current' => (int)$page,
        'size' => (int)$limit,
    ));
}

function api_admin_dashboard()
{
    global $DB;
    api_require_admin();
    $today = date('Y-m-d');
    $orderTrends = array();
    for ($i = 6; $i >= 0; $i--) {
        $day = date('Y-m-d', strtotime('-'.$i.' days'));
        $count = api_admin_count('order', "addtime>='{$day} 00:00:00' AND addtime<='{$day} 23:59:59'");
        $total = api_admin_sum('order', 'money', "status=1 AND addtime>='{$day} 00:00:00' AND addtime<='{$day} 23:59:59'");
        $orderTrends[] = array('date' => date('m-d', strtotime($day)), 'count' => $count, 'total' => $total);
    }
    $recent = array();
    $stmt = $DB->query("SELECT * FROM pay_order ORDER BY addtime DESC LIMIT 8");
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $recent[] = api_admin_order_payload($row);
        }
    }
    api_success(array(
        'users' => api_admin_metric('user', 'addtime'),
        'recharge' => api_admin_metric('order', 'addtime', 'money'),
        'orders' => api_admin_metric('order', 'addtime'),
        'payments' => api_admin_metric('order', 'addtime', 'money'),
        'order_trends' => $orderTrends,
        'recent_orders' => $recent,
        'server' => array(
            'os' => PHP_OS,
            'arch' => php_uname('m'),
            'hostname' => function_exists('gethostname') ? gethostname() : '',
            'go_ver' => 'PHP '.PHP_VERSION,
            'cpus' => 1,
            'user' => 'www',
            'pid' => function_exists('getmypid') ? getmypid() : 0,
            'start_time' => date('Y-m-d H:i:s', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time()),
            'provider' => 'MYM码支付',
        ),
        'today' => array(
            'orders' => api_admin_count('order', "addtime>='{$today} 00:00:00'"),
            'amount' => api_admin_sum('order', 'money', "status=1 AND addtime>='{$today} 00:00:00'"),
        ),
    ));
}

function api_admin_menus()
{
    api_require_admin();
    api_success(array(
        array('path' => '/dashboard', 'name' => 'Dashboard', 'component' => '/dashboard/console', 'meta' => array('title' => 'menus.dashboard.title', 'icon' => 'ri:pie-chart-line', 'roles' => array('R_SUPER', 'R_ADMIN'), 'fixedTab' => true)),
        array('path' => '/order', 'name' => 'Order', 'component' => '/order/index', 'meta' => array('title' => 'menus.order.title', 'icon' => 'ri:file-list-3-line', 'roles' => array('R_SUPER', 'R_ADMIN'))),
        array('path' => '/user-manage', 'name' => 'UserManage', 'component' => '/index/index', 'meta' => array('title' => 'menus.userManage.title', 'icon' => 'ri:group-line', 'roles' => array('R_SUPER', 'R_ADMIN')), 'children' => array(
            array('path' => 'list', 'name' => 'UserManageList', 'component' => '/user-manage/list', 'meta' => array('title' => 'menus.userManage.list', 'keepAlive' => true)),
            array('path' => 'balance-log', 'name' => 'BalanceLog', 'component' => '/user-manage/balance-log', 'meta' => array('title' => 'menus.userManage.balanceLog', 'keepAlive' => true)),
            array('path' => 'login-log', 'name' => 'LoginLog', 'component' => '/user-manage/login-log', 'meta' => array('title' => 'menus.userManage.loginLog', 'keepAlive' => true)),
        )),
        array('path' => '/announcement', 'name' => 'Announcement', 'component' => '/announcement/index', 'meta' => array('title' => 'menus.announcement.title', 'icon' => 'ri:megaphone-line', 'roles' => array('R_SUPER', 'R_ADMIN'))),
    ));
}

function api_admin_users_list()
{
    global $DB;
    api_require_admin();
    list($page, $limit, $offset) = api_admin_page_params();
    $keyword = daddslashes(api_input('keyword', api_input('search', '')));
    $where = '1';
    if ($keyword !== '') {
        $where .= " AND (`pid` LIKE '%{$keyword}%' OR `user` LIKE '%{$keyword}%' OR `email` LIKE '%{$keyword}%' OR `qq` LIKE '%{$keyword}%')";
    }
    $total = api_admin_count('user', $where);
    $stmt = $DB->query("SELECT * FROM pay_user WHERE {$where} ORDER BY addtime DESC LIMIT {$offset},{$limit}");
    $list = array();
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $item = api_user_payload($row);
            $item['id'] = $row['pid'];
            $item['password'] = '';
            $item['type'] = isset($row['type']) ? $row['type'] : '';
            $item['created_at'] = isset($row['addtime']) ? $row['addtime'] : '';
            $list[] = $item;
        }
    }
    api_admin_page_success($list, $total, $page, $limit);
}

function api_admin_orders_list()
{
    global $DB;
    api_require_admin();
    list($page, $limit, $offset) = api_admin_page_params();
    $keyword = daddslashes(api_input('keyword', api_input('search', '')));
    $where = '1';
    if ($keyword !== '') {
        $where .= " AND (`trade_no` LIKE '%{$keyword}%' OR `out_trade_no` LIKE '%{$keyword}%' OR `pid` LIKE '%{$keyword}%' OR `name` LIKE '%{$keyword}%')";
    }
    $total = api_admin_count('order', $where);
    $stmt = $DB->query("SELECT * FROM pay_order WHERE {$where} ORDER BY addtime DESC LIMIT {$offset},{$limit}");
    $list = array();
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $list[] = api_admin_order_payload($row);
        }
    }
    api_admin_page_success($list, $total, $page, $limit);
}

function api_admin_announcements_list()
{
    global $DB;
    api_require_admin();
    list($page, $limit, $offset) = api_admin_page_params();
    $total = api_admin_count('notice');
    $stmt = $DB->query("SELECT * FROM pay_notice WHERE 1 ORDER BY sort ASC,id DESC LIMIT {$offset},{$limit}");
    $list = array();
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $list[] = array(
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'content' => $row['datatxt'],
                'datatxt' => $row['datatxt'],
                'color' => $row['color'],
                'sort' => isset($row['sort']) ? (int)$row['sort'] : 0,
                'status' => isset($row['status']) ? (int)$row['status'] : 1,
                'created_at' => $row['addtime'],
                'addtime' => $row['addtime'],
            );
        }
    }
    api_admin_page_success($list, $total, $page, $limit);
}

function api_admin_logs_list($loginOnly = false)
{
    global $DB;
    api_require_admin();
    list($page, $limit, $offset) = api_admin_page_params();
    $where = $loginOnly ? "type LIKE '%登录%' OR type LIKE '%登陆%'" : '1';
    $total = api_admin_count('log', $where);
    $stmt = $DB->query("SELECT * FROM pay_log WHERE {$where} ORDER BY date DESC LIMIT {$offset},{$limit}");
    $list = array();
    if ($stmt) {
        foreach ($stmt->fetchAll() as $row) {
            $list[] = array(
                'id' => (int)$row['id'],
                'pid' => $row['pid'],
                'user_id' => $row['pid'],
                'username' => $row['pid'] == 0 ? 'admin' : $row['pid'],
                'type' => $row['type'],
                'remark' => $row['type'],
                'content' => isset($row['data']) ? $row['data'] : '',
                'ip' => $row['ip'],
                'city' => $row['city'],
                'created_at' => $row['date'],
                'date' => $row['date'],
                'amount' => 0,
            );
        }
    }
    api_admin_page_success($list, $total, $page, $limit);
}

function api_admin_set_balance()
{
    global $DB, $date;
    api_require_admin();
    $pid = daddslashes(api_input('pid', api_input('id', '')));
    $money = api_input('money', api_input('balance', ''));
    if ($pid === '' || !is_numeric($money)) api_error('参数不完整', 422);
    $row = $DB->query("SELECT * FROM pay_user WHERE pid='{$pid}' limit 1")->fetch();
    if (!$row) api_error('商户不存在', 404);
    $money = number_format((float)$money, 2, '.', '');
    $DB->exec("UPDATE pay_user SET money='{$money}' WHERE pid='{$pid}'");
    if (function_exists('Add_log')) Add_log('admin', '新版后台调整商户余额 PID:'.$pid.' 金额:'.$money);
    api_success(array('pid' => $pid, 'money' => (float)$money), '余额已更新');
}

function api_admin_unsupported($feature = '该后台功能')
{
    api_success(array('enabled' => false, 'records' => array(), 'list' => array(), 'total' => 0), $feature.'暂未接入原 MYM 码支付系统，已做兼容占位');
}

$route = api_route_path();
$method = $_SERVER['REQUEST_METHOD'];

switch ($route) {
    case '/admin/api':
        api_success(array('name' => 'MYM Vue API', 'version' => 'compat'));
        break;
    case '/admin/api/captcha/config':
        api_admin_captcha_config();
        break;
    case '/admin/api/captcha/image':
        api_admin_captcha_image();
        break;
    case '/admin/api/login':
        api_admin_login();
        break;
    case '/admin/api/upload/image':
        api_admin_upload_image();
        break;
    case '/admin/api/check':
        api_require_admin();
        api_success(api_admin_profile_payload());
        break;
    case '/admin/api/admin/profile':
        api_require_admin();
        api_success(api_admin_profile_payload());
        break;
    case '/admin/api/dashboard':
        api_admin_dashboard();
        break;
    case '/admin/api/menus':
        api_admin_menus();
        break;
    case '/admin/api/users/list':
        api_admin_users_list();
        break;
    case '/admin/api/orders/list':
        api_admin_orders_list();
        break;
    case '/admin/api/announcements/list':
        api_admin_announcements_list();
        break;
    case '/admin/api/balance-logs/list':
    case '/admin/api/admin/logs':
        api_admin_logs_list(false);
        break;
    case '/admin/api/login-logs/list':
        api_admin_logs_list(true);
        break;
    case '/admin/api/user/set-balance':
        api_admin_set_balance();
        break;
    case '/admin/api/admin/log/clear-all':
        api_require_admin();
        api_success(array(), '日志清理暂未自动执行，请在旧后台确认后操作');
        break;
    case '/admin/api/products/list':
    case '/admin/api/product-packages/list':
    case '/admin/api/product-updates/list':
    case '/admin/api/product-tags/list':
    case '/admin/api/product-recommends/list':
    case '/admin/api/licenses/list':
    case '/admin/api/cards/list':
    case '/admin/api/agents/list':
    case '/admin/api/agent-categories/list':
    case '/admin/api/tickets/list':
    case '/admin/api/articles/list':
    case '/admin/api/article-categories/list':
    case '/admin/api/integrations/list':
        api_admin_unsupported('后台扩展列表');
        break;
    case '/user/site-config':
        api_success(api_site_config());
        break;
    case '/auth/public-check':
        api_public_ok();
        break;
    case '/user/captcha/config':
        api_success(array('captcha_type' => 'none', 'geetest_id' => '', 'enabled' => false));
        break;
    case '/user/captcha/image':
        api_success(array('image' => '', 'captcha_key' => '', 'enabled' => false));
        break;
    case '/user/login':
        api_login();
        break;
    case '/user/register':
        api_register();
        break;
    case '/user/logout':
        setcookie('user_token', '', time() - 3600, '/');
        api_success(array(), '退出成功');
        break;
    case '/user/profile':
        api_profile();
        break;
    case '/user/dashboard':
        api_dashboard();
        break;
    case '/user/orders':
        api_orders();
        break;
    case '/user/channels/config':
        api_channels_config();
        break;
    case '/user/channels/list':
        api_channels_list();
        break;
    case '/user/channels/pay-pass':
        api_channel_pay_pass();
        break;
    case '/user/balance-logs':
        api_balance_logs();
        break;
    case '/user/payment-config':
        api_payment_config();
        break;
    case '/user/purchase-info':
        api_purchase_info();
        break;
    case '/user/recharge':
        api_recharge();
        break;
    case '/user/popup-announcements':
        api_popup_announcements();
        break;
    case '/user/licenses/list':
    case '/user/my-agent-licenses':
    case '/user/articles':
    case '/user/tickets':
    case '/user/invitees/list':
    case '/user/commission/logs':
        api_empty_page();
        break;
    case '/user/article/detail':
        api_article_detail();
        break;
    case '/user/ticket/detail':
        api_ticket_detail();
        break;
    case '/user/agents':
        api_success(array());
        break;
    case '/user/agent-info':
        api_success(array('agents' => array(), 'rebate_balance' => 0));
        break;
    case '/user/check-exists':
        api_success(array('exists' => false));
        break;
    case '/user/social/bindings':
        api_social_bindings();
        break;
    case '/user/send-reset-email':
        api_send_reset_email();
        break;
    case '/user/reset-password':
        api_reset_password();
        break;
    case '/user/coupon/verify':
        api_success(array('valid' => false, 'discount_amount' => 0, 'message' => '优惠券功能暂未接入'));
        break;
    case '/user/product-updates':
        api_success(array());
        break;
    case '/user/change-password':
    case '/user/purchase':
    case '/user/redeem-card':
    case '/user/commission/transfer':
    case '/user/create-sub-agent':
    case '/user/purchase-agent':
    case '/user/ticket/create':
    case '/user/ticket/reply':
    case '/user/ticket/close':
    case '/user/licenses/edit':
    case '/user/licenses/operate':
    case '/user/claim-license-init':
    case '/user/claim-license-verify':
    case '/user/generate-license-code':
    case '/user/social/login':
    case '/user/social/bind':
    case '/user/social/bind-with-token':
    case '/user/social/register-with-token':
    case '/user/social/unbind':
        api_placeholder_success();
        break;
    case '/v3/system/menus/simple':
    case '/menus':
        api_menus();
        break;
    case '/index/products':
    case '/index/product-detail':
    case '/index/product-updates':
    case '/recommends':
        api_success(array());
        break;
    case '/index/check-agent':
        api_success(array('is_agent' => false));
        break;
    case '/index/send-code':
        api_index_send_code();
        break;
    case '/index/verify-download':
        api_index_verify_download();
        break;
    default:
        api_error('接口未适配：'.$route, 404);
}
