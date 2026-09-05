<?php
/**
 * MYM 开源版兼容入口。
 *
 * 原加密版把版本常量、授权码和远程授权状态放在该文件中。
 * 开源版缺失此文件时，Mym/Common.php 会 require_once 失败；
 * 如果继续请求旧授权服务，也会被“版本过于老旧”提示阻断。
 *
 * 这里改为本地自洽模式：不联网、不拦截、不强制升级。
 */
if (!defined('MYM_OPEN_SOURCE')) {
    define('MYM_OPEN_SOURCE', true);
}

// 与 install/install.sql 的 pay_config.version=1410 保持一致，避免安装后首页被升级提示拦截。
if (!defined('DB_VERSION')) {
    define('DB_VERSION', 1410);
}

if (!defined('VERSION')) {
    define('VERSION', '1.4.10-open');
}

if (!defined('VERSIONS')) {
    define('VERSIONS', VERSION);
}

// 保留变量名兼容老代码，但不再代表远程授权码。
if (!isset($authcode) || $authcode === '') {
    $authcode = 'open-source-local';
}

// 给部分旧代码一个固定的本地通过状态。
$auth_status = array(
    'code' => 1,
    'status' => 1,
    'msg' => '开源本地版已启用',
);
