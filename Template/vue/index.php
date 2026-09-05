<?php
// 新版首页模板，由 WorkBuddy 从 Vite 构建产物适配。
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <base href="/" />
  <title><?php echo htmlspecialchars(isset($conf['title']) && $conf['title'] ? $conf['title'] : $conf['sitename'], ENT_QUOTES, 'UTF-8'); ?></title>
  <meta name="keywords" content="<?php echo htmlspecialchars(isset($conf['keywords']) ? $conf['keywords'] : '', ENT_QUOTES, 'UTF-8'); ?>" />
  <meta name="description" content="<?php echo htmlspecialchars(isset($conf['description']) ? $conf['description'] : '', ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="icon" type="image/svg+xml" href="/favicon.ico" id="site-favicon" />
  <script type="module" crossorigin src="/assets/index-CUkUkWCZ.js"></script>
  <link rel="stylesheet" crossorigin href="/assets/index-DFHfiD2K.css">
</head>

<body>
  <div id="app"></div>
</body>

</html>