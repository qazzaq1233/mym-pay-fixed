<?php
include_once(dirname(__DIR__).'/Mym/Common.php');
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
?>
<!doctype html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title><?php echo htmlspecialchars($conf['sitename'], ENT_QUOTES, 'UTF-8'); ?> - 后台管理系统</title>
  <meta name="keywords" content="<?php echo htmlspecialchars(isset($conf['keywords']) ? $conf['keywords'] : '', ENT_QUOTES, 'UTF-8'); ?>" />
  <meta name="description" content="<?php echo htmlspecialchars(isset($conf['description']) ? $conf['description'] : '', ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="icon" type="image/svg+xml" href="/favicon.ico" id="site-favicon" />

  <style>
    /* 防止页面刷新时白屏的初始样式 */
    html {
      background-color: #fafbfc;
    }

    html.dark {
      background-color: #070707;
    }
  </style>

  <script>
    // 初始化 html class 主题属性
    ; (function () {
      try {
        if (typeof Storage === 'undefined' || !window.localStorage) {
          return
        }

        const themeType = localStorage.getItem('sys-theme')
        if (themeType === 'dark') {
          document.documentElement.classList.add('dark')
        }
      } catch (e) {
        console.warn('Failed to apply initial theme:', e)
      }
    })()
  </script>
  <script type="module" crossorigin src="/Admin/assets-vue/index-D0JoGcHT.js"></script>
  <link rel="stylesheet" crossorigin href="/Admin/assets-vue/index-ECYKK_67.css">
</head>

<body>
  <div id="app"></div>

</body>

</html>