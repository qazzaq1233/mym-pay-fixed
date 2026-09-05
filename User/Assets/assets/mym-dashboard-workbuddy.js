import { d as defineComponent, r as ref, o as onMounted, ak as h } from "./index-S-nktW0z-balance20260627.js";

const apiGet = async (url) => {
  const res = await fetch(url, { credentials: "include" });
  return await res.json();
};

const money = (value) => `￥${Number(value || 0).toFixed(2)}`;

export default defineComponent({
  name: "MymDashboard",
  setup() {
    const loading = ref(false);
    const info = ref({ recent_orders: [] });

    const load = async () => {
      loading.value = true;
      try {
        const data = await apiGet("/api/index.php/user/dashboard");
        info.value = data.data || {};
      } finally {
        loading.value = false;
      }
    };

    const stat = (title, value, sub, color = "blue") => h("div", { class: `mym-stat mym-stat-${color}` }, [
      h("div", { class: "mym-stat-title" }, title),
      h("div", { class: "mym-stat-value" }, value),
      h("div", { class: "mym-stat-sub" }, sub),
    ]);

    const statusTag = (row) => {
      const status = Number(row.status || 0);
      const type = status === 1 ? "success" : status === 2 ? "danger" : "warning";
      return h("span", { class: `mym-tag mym-tag-${type}` }, row.status_text || (status === 1 ? "已支付" : "待支付"));
    };

    const renderOrders = () => {
      const list = Array.isArray(info.value.recent_orders) ? info.value.recent_orders : [];
      return h("div", { class: "mym-card" }, [
        h("div", { class: "mym-card-head" }, [h("h3", null, "最近订单"), h("a", { href: "#/order-manage" }, "查看全部")]),
        list.length ? h("table", { class: "mym-table" }, [
          h("thead", null, h("tr", null, ["订单号", "商品", "金额", "支付方式", "状态", "时间"].map((name) => h("th", null, name)))),
          h("tbody", null, list.map((row) => h("tr", { key: row.trade_no }, [
            h("td", null, row.trade_no || "--"),
            h("td", null, row.name || "--"),
            h("td", null, money(row.money)),
            h("td", null, row.type || "--"),
            h("td", null, statusTag(row)),
            h("td", null, row.addtime || "--"),
          ]))),
        ]) : h("div", { class: "mym-empty" }, loading.value ? "加载中..." : "暂无订单记录"),
      ]);
    };

    onMounted(load);

    return () => h("div", { class: "mym-dashboard" }, [
      h("style", null, `.mym-dashboard{padding:4px 2px 24px;color:#1f2937}.mym-hero{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:16px;margin-bottom:16px}.mym-card,.mym-welcome{background:#fff;border:1px solid #edf0f5;border-radius:18px;box-shadow:0 10px 30px rgba(15,23,42,.06);padding:20px}.mym-welcome{background:linear-gradient(135deg,#eef4ff,#ffffff)}.mym-title{font-size:24px;font-weight:700;margin:0 0 8px}.mym-muted{color:#64748b;font-size:13px;line-height:1.7}.mym-quick{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.mym-btn{display:inline-flex;align-items:center;justify-content:center;border-radius:10px;padding:9px 13px;border:1px solid #dbe3ef;background:#fff;color:#334155;text-decoration:none;font-size:13px}.mym-btn-primary{background:#2563eb;border-color:#2563eb;color:#fff}.mym-btn-success{background:#16a34a;border-color:#16a34a;color:#fff}.mym-balance{display:grid;gap:12px}.mym-money{font-size:28px;font-weight:800;color:#111827}.mym-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:16px}.mym-stat{background:#fff;border:1px solid #edf0f5;border-radius:16px;padding:17px;box-shadow:0 10px 30px rgba(15,23,42,.05)}.mym-stat-title{font-size:13px;color:#64748b}.mym-stat-value{font-size:24px;font-weight:800;margin:10px 0;color:#111827}.mym-stat-sub{font-size:12px;color:#94a3b8}.mym-stat-green .mym-stat-value{color:#16a34a}.mym-stat-orange .mym-stat-value{color:#f59e0b}.mym-stat-purple .mym-stat-value{color:#7c3aed}.mym-card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}.mym-card-head h3{margin:0;font-size:18px}.mym-card-head a{color:#2563eb;text-decoration:none;font-size:13px}.mym-table{width:100%;border-collapse:collapse}.mym-table th{background:#f8fafc;color:#64748b;text-align:left;font-weight:600}.mym-table th,.mym-table td{padding:12px;border-bottom:1px solid #edf2f7;font-size:13px}.mym-tag{display:inline-flex;padding:3px 9px;border-radius:999px;font-size:12px}.mym-tag-success{background:#ecfdf5;color:#047857}.mym-tag-warning{background:#fff7ed;color:#c2410c}.mym-tag-danger{background:#fef2f2;color:#b91c1c}.mym-empty{text-align:center;color:#94a3b8;padding:46px 0}@media(max-width:980px){.mym-hero,.mym-grid{grid-template-columns:1fr}.mym-table th,.mym-table td{white-space:nowrap}.mym-card{overflow:auto}}`),
      h("div", { class: "mym-hero" }, [
        h("div", { class: "mym-welcome" }, [
          h("div", { class: "mym-title" }, `欢迎回来，${info.value.nickname || info.value.user || "商户"}`),
          h("div", { class: "mym-muted" }, `商户ID：${info.value.pid || "--"}。这里显示的是 MYM 码支付真实业务概览，不再使用授权系统模板。`),
          h("div", { class: "mym-quick" }, [
            h("a", { class: "mym-btn mym-btn-primary", href: "#/order-manage" }, "订单管理"),
            h("a", { class: "mym-btn mym-btn-success", href: "#/channel/qrlist" }, "通道列表"),
            h("a", { class: "mym-btn", href: "#/finance/recharge" }, "立即充值"),
            h("a", { class: "mym-btn", href: "#/finance/userinfo" }, "API / 资料"),
          ]),
        ]),
        h("div", { class: "mym-card mym-balance" }, [
          h("div", { class: "mym-muted" }, "账户余额"),
          h("div", { class: "mym-money" }, money(info.value.balance)),
          h("div", { class: "mym-muted" }, `可结算余额：${money(info.value.settle_balance)} ｜ 费率：${info.value.rate || "按后台配置"}`),
          h("a", { class: "mym-btn mym-btn-primary", href: "#/finance/recharge" }, "充值余额"),
        ]),
      ]),
      h("div", { class: "mym-grid" }, [
        stat("总订单", info.value.orders_count || 0, `今日订单 ${info.value.today_orders_count || 0}`),
        stat("已支付金额", money(info.value.revenue), `本月 ${money(info.value.monthly_revenue)}`, "green"),
        stat("今日收款", money(info.value.today_revenue), `已支付订单 ${info.value.paid_orders_count || 0}`, "orange"),
        stat("支付通道", `${info.value.online_channels_count || 0}/${info.value.channels_count || 0}`, "在线 / 总通道", "purple"),
      ]),
      renderOrders(),
    ]);
  },
});
