<?php

if (version_compare(PHP_VERSION, '7.1.0', '<')) {
    die('require PHP > 7.1 !');
}
include("./Mym/Common.php");

// 默认启用新适配的 Vue 首页模板；如需回退旧首页，可在后台模板配置改为其他模板。
if ((!isset($conf['template']) || $conf['template'] === '' || $conf['template'] === 'default') && file_exists(TEMPLATE_ROOT.'vue/index.php')) {
    $conf['template'] = 'vue';
}

$mod = isset($_GET['mod']) ? trim($_GET['mod']) : 'index';
if ($mod === '' || !preg_match('/^[a-zA-Z0-9]+$/', $mod)) {
    $mod = 'index';
}
$loadfile = \lib\Template::load($mod);
include $loadfile;