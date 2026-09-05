<!doctype html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>用户中心</title>
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <link rel="icon" type="image/svg+xml" href="/favicon.ico" id="site-favicon" />

  <style>
    /* 防止页面刷新时白屏的初始样式 */
    html {
      background-color: #fafbfc;
    }

    html.dark {
      background-color: #070707;
    }

    /* 代理商开通页增强样式：对齐参考系统的卡片与规则布局 */
    .agent-page,
    .max-w-\[1140px\].mx-auto {
      max-width: 1100px !important;
      margin: 0 auto !important;
      padding: 0 0 40px !important;
    }

    .agent-page .page-header,
    .max-w-\[1140px\].mx-auto > .text-center,
    .max-w-\[1140px\].mx-auto > div:first-child {
      text-align: center !important;
      padding: 24px 0 20px !important;
    }

    .agent-page .page-title,
    .max-w-\[1140px\].mx-auto h1 {
      font-size: 24px !important;
      line-height: 32px !important;
      font-weight: 700 !important;
      color: #1d2129 !important;
      margin: 0 !important;
    }

    .max-w-\[1140px\].mx-auto h1::before {
      content: "代理商套餐";
      font-size: 24px;
    }

    .max-w-\[1140px\].mx-auto h1 {
      font-size: 0 !important;
    }

    .max-w-\[1140px\].mx-auto h1 + p {
      font-size: 0 !important;
      color: #86909c !important;
      margin: 8px 0 0 !important;
    }

    .max-w-\[1140px\].mx-auto h1 + p::before {
      content: "享受更低折扣，开通即送余额";
      font-size: 14px;
    }

    .category-filter {
      justify-content: center !important;
      text-align: center !important;
      margin-bottom: 18px !important;
      gap: 12px !important;
    }

    .max-w-\[1140px\].mx-auto > .el-row {
      display: flex !important;
      flex-wrap: wrap !important;
      justify-content: center !important;
      gap: 20px !important;
      margin-left: 0 !important;
      margin-right: 0 !important;
      margin-bottom: 28px !important;
    }

    .max-w-\[1140px\].mx-auto > .el-row > .el-col {
      max-width: 360px !important;
      flex: 0 0 360px !important;
      width: 360px !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }

    .max-w-\[1140px\].mx-auto > .el-row.mt-12,
    .max-w-\[1140px\].mx-auto > .el-row.mb-20 {
      display: grid !important;
      grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
      align-items: stretch !important;
      justify-content: stretch !important;
      margin-top: 12px !important;
    }

    .max-w-\[1140px\].mx-auto > .el-row.mt-12 > .el-col,
    .max-w-\[1140px\].mx-auto > .el-row.mb-20 > .el-col {
      max-width: none !important;
      flex: none !important;
      width: 100% !important;
    }

    .max-w-\[1140px\].mx-auto .grid.gap-4.relative,
    .plan-card {
      position: relative !important;
      min-height: 100% !important;
      background: #ffffff !important;
      border: 2px solid #e5e6eb !important;
      border-radius: 12px !important;
      padding: 28px 24px 24px !important;
      cursor: pointer !important;
      transition: all .25s ease !important;
      box-shadow: none !important;
      overflow: visible !important;
    }

    .max-w-\[1140px\].mx-auto .grid.gap-4.relative:hover {
      border-color: #4080ff !important;
      box-shadow: 0 4px 16px rgba(64,128,255,.10) !important;
    }

    .max-w-\[1140px\].mx-auto .grid.gap-4.relative h2 {
      font-size: 20px !important;
      font-weight: 700 !important;
      color: #1d2129 !important;
      margin: 0 0 16px !important;
    }

    .max-w-\[1140px\].mx-auto .grid.gap-4.relative > .absolute.top-2.right-2 {
      top: 12px !important;
      right: 12px !important;
      display: flex !important;
      gap: 6px !important;
      z-index: 2 !important;
    }

    .max-w-\[1140px\].mx-auto .grid.gap-4.relative .el-button.mt-4,
    .max-w-\[1140px\].mx-auto .grid.gap-4.relative button.mt-4 {
      width: 100% !important;
      height: 42px !important;
      border-radius: 8px !important;
      margin: 18px 0 18px !important;
      font-weight: 600 !important;
    }

    .max-w-\[1140px\].mx-auto .flex-1.overflow-auto {
      border-top: 1px solid #f0f0f0 !important;
      padding-top: 14px !important;
      overflow: visible !important;
    }

    .max-w-\[1140px\].mx-auto .flex.items-center.justify-between.gap-2.mb-2,
    .max-w-\[1140px\].mx-auto .flex.items-center.gap-2.mb-2 {
      padding: 5px 0 !important;
      margin-bottom: 0 !important;
      font-size: 13px !important;
      color: #4e5969 !important;
    }

    .plan-price-area {
      display: flex !important;
      align-items: flex-end !important;
      justify-content: flex-start !important;
      gap: 10px !important;
      margin: 0 0 16px !important;
    }

    .plan-price-area .text-red-500,
    .plan-price-area > span:first-child {
      color: #f53f3f !important;
      font-size: 32px !important;
      line-height: 1 !important;
      font-weight: 700 !important;
    }

    .max-w-\[1140px\].mx-auto .feature-tag,
    .max-w-\[1140px\].mx-auto .el-tag {
      border-radius: 4px !important;
    }

    .max-w-\[1140px\].mx-auto .shadow-sm.rounded-xl.bg-white,
    .max-w-\[1140px\].mx-auto > .el-row:last-child .rounded-xl {
      height: 100% !important;
      background: #ffffff !important;
      border: 1px solid #e5e6eb !important;
      border-radius: 12px !important;
      padding: 18px 24px !important;
      box-shadow: none !important;
    }

    .max-w-\[1140px\].mx-auto > .el-row:last-child .rounded-xl:first-child::after {
      content: "重要通知";
      display: inline-block;
      vertical-align: top;
      margin: -2px 0 12px 8px;
      padding: 2px 8px;
      border-radius: 4px;
      background: #fff7e8;
      color: #ff7d00;
      border: 1px solid #ffd8a8;
      font-size: 12px;
      font-weight: 600;
    }

    .max-w-\[1140px\].mx-auto > .el-row:last-child .rounded-xl .el-divider {
      margin: 10px 0 14px !important;
    }

    .max-w-\[1140px\].mx-auto > .el-row:last-child .rounded-xl ul,
    .max-w-\[1140px\].mx-auto > .el-row:last-child .rounded-xl li {
      font-size: 13px !important;
      color: #4e5969 !important;
      line-height: 2 !important;
    }

    .max-w-\[1140px\].mx-auto > .el-row:last-child .rounded-xl .el-alert {
      border-radius: 8px !important;
      background: #fff7e8 !important;
      color: #b25000 !important;
    }

    .el-dialog[aria-label="确认支付"] .el-radio-group,
    .pay-method-group,
    .payment-group,
    .method-group {
      display: flex !important;
      flex-direction: row !important;
      flex-wrap: wrap !important;
      align-items: center !important;
      gap: 10px 12px !important;
      width: 100% !important;
    }

    .el-dialog[aria-label="确认支付"] .el-radio,
    .el-dialog[aria-label="确认支付"] .el-radio.el-radio--large,
    .el-dialog[aria-label="确认支付"] .el-radio.is-bordered,
    .pay-method-group .el-radio,
    .payment-group .el-radio,
    .method-group .el-radio,
    .pay-method-group .el-radio-button,
    .payment-group .el-radio-button,
    .method-group .el-radio-button {
      display: inline-flex !important;
      width: auto !important;
      min-width: 130px !important;
      max-width: 100% !important;
      height: auto !important;
      margin: 0 !important;
      border-radius: 8px !important;
      vertical-align: top !important;
    }

    .el-dialog[aria-label="确认支付"] .el-radio.is-bordered,
    .el-dialog[aria-label="确认支付"] .el-radio.el-radio--large.is-bordered {
      padding: 10px 14px !important;
      min-height: 42px !important;
    }

    .el-dialog[aria-label="确认支付"] .el-radio__label,
    .pay-method-group .el-radio__label,
    .payment-group .el-radio__label,
    .method-group .el-radio__label {
      flex: 1 !important;
      min-width: 0 !important;
    }

    .el-dialog[aria-label="确认支付"] .flex.items-center.gap-2 {
      width: 100% !important;
      flex-wrap: nowrap !important;
      row-gap: 4px !important;
    }

    .el-dialog[aria-label="确认支付"] .balance-hint {
      display: inline-flex !important;
      margin-left: 6px !important;
      color: #86909c !important;
      font-size: 12px !important;
      white-space: nowrap !important;
    }

    .pay-method-group .el-radio-button__inner,
    .payment-group .el-radio-button__inner,
    .method-group .el-radio-button__inner {
      width: auto !important;
      min-width: 130px !important;
      border-radius: 8px !important;
      text-align: center !important;
      justify-content: center !important;
      padding: 9px 16px !important;
    }

    @media (max-width: 992px) {
      .max-w-\[1140px\].mx-auto > .el-row,
      .max-w-\[1140px\].mx-auto > .el-row.mt-12,
      .max-w-\[1140px\].mx-auto > .el-row.mb-20 {
        grid-template-columns: 1fr !important;
      }
    }

    @media (max-width: 640px) {
      .el-dialog[aria-label="确认支付"] .el-radio,
      .el-dialog[aria-label="确认支付"] .el-radio.el-radio--large,
      .el-dialog[aria-label="确认支付"] .el-radio.is-bordered,
      .pay-method-group .el-radio,
      .payment-group .el-radio,
      .method-group .el-radio,
      .pay-method-group .el-radio-button,
      .payment-group .el-radio-button,
      .method-group .el-radio-button {
        min-width: calc(50% - 8px) !important;
      }

      .pay-method-group .el-radio-button__inner,
      .payment-group .el-radio-button__inner,
      .method-group .el-radio-button__inner {
        min-width: 100% !important;
      }
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
  <script type="module" crossorigin src="/User/Assets/assets/index-S-nktW0z-balance20260627.js?v=20260629-mym2"></script>
  <link rel="stylesheet" crossorigin href="/User/Assets/assets/index-BUWc9l2s.css?v=20260629-mym2">
</head>

<body>
  <div id="app"></div>
</body>

</html>