import { d as defineComponent, r as ref, o as onMounted, ak as h } from "./index-S-nktW0z-balance20260627.js";

const apiGet = async (url) => {
  const res = await fetch(url, { credentials: "include" });
  return await res.json();
};

const apiPost = async (url, data = {}) => {
  const body = new URLSearchParams();
  Object.keys(data).forEach((key) => body.append(key, data[key] == null ? "" : data[key]));
  const res = await fetch(url, {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
    body,
  });
  return await res.json();
};

const cleanMessage = (data, fallback = "操作完成") => data && (data.msg || data.message) ? (data.msg || data.message) : fallback;

const isValidLoginQrUrl = (url) => {
  if (typeof url !== "string") return false;
  const value = url.trim();
  return value !== "" && value !== "undefined" && value !== "null" && /^(https?:\/\/|data:image\/|\/)/i.test(value);
};

export default defineComponent({
  name: "QrChannelList",
  setup() {
    const loading = ref(false);
    const saving = ref(false);
    const rows = ref([]);
    const config = ref({ types: [], channels: {}, total: 0, max: 0, payPassVerified: true, payPassSet: true });
    const total = ref(0);
    const page = ref(1);
    const limit = ref(30);
    const payPass = ref("");
    const addVisible = ref(false);
    const surnameVisible = ref(false);
    const testVisible = ref(false);
    const aliConfigVisible = ref(false);
    const editVisible = ref(false);
    const updateVisible = ref(false);
    const currentRow = ref(null);
    const loginOptions = ref([]);
    const updateBusy = ref(false);
    const updateStarted = ref(false);
    const addForm = ref({ type: "", channel: "", qr_url: "", custom_qr_url: "", beizhu: "", receiver_surname: "", Login_Type: "", ali_order_check: "order_amount" });
    const surnameForm = ref({ id: "", receiver_surname: "" });
    const testForm = ref({ id: "", money: "0.01", name: "测试商品" });
    const aliForm = ref({ id: "", appid: "", appkey2: "", qr_url: "", money: "", ali_order_check: "order_amount" });
    const editForm = ref({ id: "", type: "", typeName: "", qr_url: "", custom_qr_url: "", beizhu: "", receiver_surname: "" });
    const updateForm = ref({ id: "", type: "", typeLabel: "", channel: "", hook: 0, beizhu: "", loginId: "", loginQrId: "", qrUrl: "", guid: "", uuid: "", message: "", error: "", wxClerks: [] });
    let updateTimer = 0;

    const notify = (message) => window.alert(message);
    const ask = (message) => window.confirm(message);

    const selectedChannels = () => {
      const type = addForm.value.type;
      return config.value.channels && config.value.channels[type] ? config.value.channels[type] : [];
    };

    const needsLoginType = (channel) => {
      return ["mg_vzq", "yd_vzq", "mg_qq", "yd_qq", "yd_wx", "yd_wx_uos", "yd_wx_gskd", "yd_wx_sskd"].includes(channel);
    };

    const parseLoginOptions = (html) => {
      const div = document.createElement("div");
      div.innerHTML = `<select>${html || ""}</select>`;
      return Array.from(div.querySelectorAll("option")).map((option) => ({ value: option.value, label: option.textContent || option.value }));
    };

    const loadLoginOptions = async () => {
      loginOptions.value = [];
      addForm.value.Login_Type = "";
      if (!needsLoginType(addForm.value.channel)) return;
      try {
        const data = await apiPost("/User/Ajax3.php?act=Login_Type", { channel: addForm.value.channel });
        loginOptions.value = parseLoginOptions(data.html || "");
        if (loginOptions.value.length) addForm.value.Login_Type = loginOptions.value[0].value;
      } catch (e) {
        loginOptions.value = [];
      }
    };

    const loadConfig = async () => {
      const data = await apiGet("/api/index.php/user/channels/config");
      config.value = data.data || config.value;
      if (!addForm.value.type && config.value.types && config.value.types.length) {
        addForm.value.type = config.value.types[0].code;
        const firstChannels = selectedChannels();
        addForm.value.channel = firstChannels.length ? firstChannels[0].code : addForm.value.type;
      }
    };

    const loadList = async () => {
      loading.value = true;
      try {
        const data = await apiGet(`/api/index.php/user/channels/list?page=${page.value}&limit=${limit.value}`);
        rows.value = data.data && data.data.list ? data.data.list : [];
        total.value = Number(data.total || 0);
        if (data.data && data.data.needsPayPass) config.value.payPassVerified = false;
      } finally {
        loading.value = false;
      }
    };

    const refresh = async () => {
      await loadConfig();
      await loadList();
    };

    const verifyPayPass = async () => {
      if (!payPass.value) return notify("请输入二级密码");
      saving.value = true;
      try {
        const data = await apiPost("/api/index.php/user/channels/pay-pass", { pay_pass: payPass.value });
        if (data.code === 0) {
          config.value.payPassVerified = true;
          payPass.value = "";
          await refresh();
        } else {
          notify(cleanMessage(data, "验证失败"));
        }
      } finally {
        saving.value = false;
      }
    };

    const openAdd = async () => {
      addForm.value = { type: config.value.types && config.value.types[0] ? config.value.types[0].code : "", channel: "", qr_url: "", custom_qr_url: "", beizhu: "", receiver_surname: "", Login_Type: "", ali_order_check: "order_amount" };
      const firstChannels = selectedChannels();
      addForm.value.channel = firstChannels.length ? firstChannels[0].code : addForm.value.type;
      addVisible.value = true;
      await loadLoginOptions();
    };

    const onTypeChange = async (event) => {
      addForm.value.type = event.target.value;
      const channels = selectedChannels();
      addForm.value.channel = channels.length ? channels[0].code : addForm.value.type;
      await loadLoginOptions();
    };

    const onChannelChange = async (event) => {
      addForm.value.channel = event.target.value;
      await loadLoginOptions();
    };

    const submitAdd = async () => {
      saving.value = true;
      try {
        const data = await apiPost("/User/Ajax.php?act=Add_Qr", addForm.value);
        if (Number(data.code) === 1) {
          addVisible.value = false;
          notify(cleanMessage(data, "添加成功"));
          await refresh();
        } else {
          notify(cleanMessage(data, "添加失败"));
        }
      } finally {
        saving.value = false;
      }
    };

    const openSurname = (row) => {
      currentRow.value = row;
      surnameForm.value = { id: row.id, receiver_surname: row.receiverSurname || "" };
      surnameVisible.value = true;
    };

    const submitSurname = async () => {
      saving.value = true;
      try {
        const data = await apiPost("/User/Ajax.php?act=Set_Receiver_Surname", surnameForm.value);
        if (Number(data.code) === 1) {
          surnameVisible.value = false;
          await loadList();
        }
        notify(cleanMessage(data, "保存完成"));
      } finally {
        saving.value = false;
      }
    };

    const openTest = (row) => {
      currentRow.value = row;
      testForm.value = { id: row.id, money: "0.01", name: "测试商品" };
      testVisible.value = true;
    };

    const submitTest = async () => {
      saving.value = true;
      try {
        const data = await apiPost("/User/Ajax.php?act=Test_Qr_Order", testForm.value);
        if (Number(data.code) === 1) {
          testVisible.value = false;
          if (ask(`${cleanMessage(data, "测试订单创建成功")}\n\n是否立即打开支付页面？`)) {
            window.open(data.url, "_blank");
          }
        } else {
          notify(cleanMessage(data, "测试订单创建失败"));
        }
      } finally {
        saving.value = false;
      }
    };

    const toggleStatus = async (row) => {
      if (!ask(`确定要${row.qrStatus === 1 ? "关闭" : "开启"}通道 #${row.id} 吗？`)) return;
      const data = await apiPost("/User/Ajax2.php?act=Del_Qr_status", { id: row.id });
      notify(cleanMessage(data, "操作完成"));
      await loadList();
    };

    const deleteRow = async (row) => {
      if (!ask(`确定删除通道 #${row.id} 吗？此操作不可恢复。`)) return;
      const data = await apiPost("/User/Ajax.php?act=Del_Qr", { id: row.id });
      notify(cleanMessage(data, "操作完成"));
      await refresh();
    };

    const openEditQr = (row) => {
      currentRow.value = row;
      editForm.value = {
        id: row.id,
        type: row.type || "",
        typeName: row.typeName || "",
        qr_url: row.qrUrl || "",
        custom_qr_url: row.customQrUrl || "",
        beizhu: row.beizhu || "",
        receiver_surname: row.receiverSurname || "",
      };
      editVisible.value = true;
    };

    const submitEditQr = async () => {
      saving.value = true;
      try {
        const data = await apiPost("/User/Ajax.php?act=Edit_Qr_Info", editForm.value);
        if (Number(data.code) === 1) {
          editVisible.value = false;
          await loadList();
        }
        notify(cleanMessage(data, "保存完成"));
      } finally {
        saving.value = false;
      }
    };

    const openAliConfig = async (row) => {
      currentRow.value = row;
      aliForm.value = { id: row.id, appid: "", appkey2: "", qr_url: "", money: "", ali_order_check: "order_amount" };
      aliConfigVisible.value = true;
      try {
        const data = await apiPost("/User/Ajax3.php?act=AliYunGet", { id: row.id });
        if (data) {
          aliForm.value.appid = data.appid || "";
          aliForm.value.appkey2 = data.appkey2 || "";
          aliForm.value.qr_url = data.qr_url || "";
          aliForm.value.money = data.money || "";
          aliForm.value.ali_order_check = data.ali_order_check || "order_amount";
        }
      } catch (e) {}
    };

    const submitAliConfig = async () => {
      saving.value = true;
      try {
        const data = await apiPost("/User/Ajax2.php?act=user_settle_save", {
          id: aliForm.value.id,
          appid: aliForm.value.appid,
          appkey2: aliForm.value.appkey2,
          qr_url: aliForm.value.qr_url,
          ali_order_check: aliForm.value.ali_order_check,
        });
        if (Number(data.code) === 1) {
          aliConfigVisible.value = false;
          await loadList();
        }
        notify(cleanMessage(data, "保存完成"));
      } finally {
        saving.value = false;
      }
    };

    const clearUpdateTimer = () => {
      if (updateTimer) window.clearTimeout(updateTimer);
      updateTimer = 0;
    };

    const closeUpdate = async (reload = false) => {
      clearUpdateTimer();
      updateVisible.value = false;
      updateBusy.value = false;
      updateStarted.value = false;
      if (reload) await loadList();
    };

    const loginTypeName = () => {
      if (updateForm.value.type === "alipay") return "支付宝";
      if (updateForm.value.type === "qqpay" || updateForm.value.channel === "yd_vzq") return "QQ";
      return "微信";
    };

    const pollLoginCookie = async () => {
      if (!updateVisible.value || !updateForm.value.loginQrId) return;
      try {
        const data = await apiPost("/User/Ajax.php?act=Get_Login_Cookie", {
          id: updateForm.value.loginQrId,
          type: updateForm.value.type,
          qr_id: updateForm.value.id,
          hook: updateForm.value.hook,
          guid: updateForm.value.guid,
          uuid: updateForm.value.uuid,
          channel: updateForm.value.channel,
        });
        if (Number(data.code) === 200) {
          updateForm.value.message = cleanMessage(data, "更新成功");
          notify(updateForm.value.message);
          await closeUpdate(true);
          return;
        }
        if (Number(data.code) === -1) {
          updateForm.value.error = cleanMessage(data, "登录失败，请重新获取二维码");
          updateBusy.value = false;
          return;
        }
        updateForm.value.message = cleanMessage(data, "等待扫码确认中，请保持本页面打开...");
      } catch (e) {
        updateForm.value.message = "接口暂未返回，继续等待扫码确认...";
      }
      updateTimer = window.setTimeout(pollLoginCookie, 1500);
    };

    const startLoginUpdate = async () => {
      clearUpdateTimer();
      updateBusy.value = true;
      updateStarted.value = true;
      updateForm.value.error = "";
      updateForm.value.qrUrl = "";
      updateForm.value.loginQrId = "";
      updateForm.value.message = "正在获取登录二维码...";
      try {
        const data = await apiPost("/User/Ajax.php?act=Get_Login_QrCode", {
          qr_id: updateForm.value.id,
          type: updateForm.value.type,
          beizhu: updateForm.value.beizhu,
          hook: updateForm.value.hook,
          Login_Type: updateForm.value.loginId,
          channel: updateForm.value.channel,
        });
        if (Number(data.code) === 1 && isValidLoginQrUrl(data.qr_url)) {
          updateForm.value.loginQrId = data.id || "";
          updateForm.value.qrUrl = String(data.qr_url || "").trim();
          updateForm.value.guid = data.guid || "";
          updateForm.value.uuid = data.uuid || "";
          updateForm.value.message = `请使用${loginTypeName()}扫码登录，扫码后保持本页面打开等待自动更新。`;
          updateTimer = window.setTimeout(pollLoginCookie, 1200);
        } else {
          updateForm.value.error = cleanMessage(data, "获取登录二维码失败");
          updateBusy.value = false;
        }
      } catch (e) {
        updateForm.value.error = "获取登录二维码失败，服务器返回异常";
        updateBusy.value = false;
      }
    };

    const openUpdate = async (row) => {
      if (row.type === "alipay" && row.channel === "yd_ali") return openAliConfig(row);
      currentRow.value = row;
      updateForm.value = { id: row.id, type: row.type, typeLabel: row.typeName || "", channel: row.channel || "", hook: Number(row.hookType || 0), beizhu: row.beizhu || "", loginId: row.json && row.json.Login_Id ? row.json.Login_Id : "", loginQrId: "", qrUrl: "", guid: "", uuid: "", message: "正在读取通道信息...", error: "", wxClerks: [] };
      updateVisible.value = true;
      updateStarted.value = false;
      updateBusy.value = false;
      clearUpdateTimer();
      try {
        const data = await apiPost("/User/Ajax.php?act=Get_Qr", { id: row.id });
        if (Number(data.code) === 1 || data.id) {
          updateForm.value.id = data.id || row.id;
          updateForm.value.type = data.qrdata && data.qrdata.type ? data.qrdata.type : row.type;
          updateForm.value.channel = data.qrdata && data.qrdata.channel ? data.qrdata.channel : row.channel;
          updateForm.value.hook = data.qrdata && data.qrdata.hook_type != null ? Number(data.qrdata.hook_type) : Number(row.hookType || 0);
          updateForm.value.beizhu = data.beizhu || row.beizhu || "";
          updateForm.value.loginId = row.json && row.json.Login_Id ? row.json.Login_Id : "";
          if (updateForm.value.type === "wxpay" && updateForm.value.hook === 0 && updateForm.value.channel !== "mg_vzq") {
            updateForm.value.wxClerks = Array.isArray(data.data) ? data.data : [];
            updateForm.value.message = "该微信通道需要按下方账号发送店员邀请，完成后再刷新通道状态。";
          } else if (updateForm.value.type === "alipay" && updateForm.value.hook === 0) {
            updateForm.value.message = "请选择扫码登录更新支付宝 CK；账号密码登录仍建议临时走旧接口，后续再单独新 UI 化。";
          } else {
            await startLoginUpdate();
          }
        } else {
          updateForm.value.error = cleanMessage(data, "读取通道失败");
        }
      } catch (e) {
        updateForm.value.error = "读取通道失败，服务器返回异常";
      }
    };

    const pageCount = () => Math.max(1, Math.ceil(total.value / limit.value));
    const prevPage = async () => { if (page.value > 1) { page.value -= 1; await loadList(); } };
    const nextPage = async () => { if (page.value < pageCount()) { page.value += 1; await loadList(); } };

    const textInput = (label, value, onInput, placeholder = "", type = "text") => h("label", { class: "wb-field" }, [
      h("span", null, label),
      h("input", { value, type, placeholder, onInput: (e) => onInput(e.target.value) }),
    ]);

    const selectInput = (label, value, options, onChange) => h("label", { class: "wb-field" }, [
      h("span", null, label),
      h("select", { value, onChange }, options.map((item) => h("option", { value: item.value || item.code }, item.label || item.name || item.code))),
    ]);

    const button = (label, onClick, type = "default", disabled = false) => h("button", { class: `wb-btn wb-btn-${type}`, disabled, onClick }, label);

    const tag = (text, type = "default") => h("span", { class: `wb-tag wb-tag-${type}` }, text);

    const modal = (visible, title, body, footer, onClose) => visible.value ? h("div", { class: "wb-modal-mask" }, [
      h("div", { class: "wb-modal" }, [
        h("div", { class: "wb-modal-head" }, [h("strong", null, title), h("button", { class: "wb-close", onClick: () => { visible.value = false; if (onClose) onClose(); } }, "x")]),
        h("div", { class: "wb-modal-body" }, body),
        h("div", { class: "wb-modal-foot" }, footer),
      ]),
    ]) : null;

    const renderPayPass = () => h("div", { class: "wb-card wb-paypass" }, [
      h("h2", null, "请先验证二级密码"),
      h("p", null, "通道列表涉及收款账号和 CK 配置，需要先验证二级密码。"),
      h("div", { class: "wb-inline-form" }, [
        h("input", { type: "password", value: payPass.value, placeholder: "请输入二级密码", onInput: (e) => payPass.value = e.target.value, onKeyup: (e) => { if (e.key === "Enter") verifyPayPass(); } }),
        button(saving.value ? "验证中..." : "确定", verifyPayPass, "primary", saving.value),
      ]),
      !config.value.payPassSet ? h("p", { class: "wb-muted" }, "当前账号还没有设置二级密码，请到 API / 资料页面设置。") : null,
    ]);

    const renderTable = () => h("div", { class: "wb-card" }, [
      h("div", { class: "wb-table-wrap" }, [
        h("table", { class: "wb-table" }, [
          h("thead", null, h("tr", null, ["ID", "通道", "状态", "余额", "更新时间/账号", "操作"].map((name) => h("th", null, name)))),
          h("tbody", null, rows.value.length ? rows.value.map((row) => h("tr", { key: row.id }, [
            h("td", null, [h("strong", null, `#${row.id}`), h("div", { class: "wb-muted" }, row.hookLabel)]),
            h("td", null, [h("div", { class: "wb-main" }, row.typeName), h("div", { class: "wb-muted" }, row.beizhu || "未填写备注"), row.receiverSurname ? h("div", { class: "wb-blue" }, `姓：${row.receiverSurname}`) : h("div", { class: "wb-muted" }, "姓：未设置")]),
            h("td", null, [tag(row.online ? "在线" : "离线", row.online ? "success" : "danger"), h("div", { class: "wb-muted wb-status" }, row.statusText), h("div", null, tag(row.qrStatusText, row.qrStatus === 1 ? "success" : "warning"))]),
            h("td", null, row.moneyText || `￥ ${row.money}`),
            h("td", null, [h("div", null, row.accountText || "--"), h("div", { class: "wb-muted" }, row.addtime || "--")]),
            h("td", null, h("div", { class: "wb-actions" }, [
              button(row.type === "alipay" && row.channel === "yd_ali" ? "免挂配置" : "更新", () => openUpdate(row), "success"),
              button("编辑收款码", () => openEditQr(row), "primary"),
              button("设置姓", () => openSurname(row), "primary"),
              button("测试", () => openTest(row), "info"),
              button(row.qrStatus === 1 ? "关闭" : "开启", () => toggleStatus(row), "warning"),
              button("删除", () => deleteRow(row), "danger"),
            ])),
          ])) : h("tr", null, h("td", { colspan: 6, class: "wb-empty" }, loading.value ? "加载中..." : "暂无通道"))),
        ]),
      ]),
      h("div", { class: "wb-pagination" }, [button("上一页", prevPage, "default", page.value <= 1), h("span", null, `第 ${page.value} / ${pageCount()} 页，共 ${total.value} 条`), button("下一页", nextPage, "default", page.value >= pageCount())]),
    ]);

    const renderAddModal = () => modal(addVisible, "新增支付通道", [
      selectInput("支付类型", addForm.value.type, (config.value.types || []).map((item) => ({ value: item.code, label: item.name })), onTypeChange),
      selectedChannels().length ? selectInput("通道类型", addForm.value.channel, selectedChannels().map((item) => ({ value: item.code, label: item.name })), onChannelChange) : null,
      loginOptions.value.length ? selectInput("免挂/登录服务器", addForm.value.Login_Type, loginOptions.value, (e) => addForm.value.Login_Type = e.target.value) : null,
      addForm.value.type === "alipay" && addForm.value.channel === "yd_ali" ? selectInput("订单检查", addForm.value.ali_order_check, [{ value: "order_amount", label: "订单号优先，匹配不到再按金额" }, { value: "order_no", label: "只按订单号" }], (e) => addForm.value.ali_order_check = e.target.value) : null,
      textInput(addForm.value.type === "usdt" ? "USDT-TRC20 地址" : "收款码链接", addForm.value.qr_url, (v) => addForm.value.qr_url = v, "可选，按通道要求填写"),
      textInput("自定义收款码链接", addForm.value.custom_qr_url, (v) => addForm.value.custom_qr_url = v, "可选：填写后发起订单将直接使用此链接"),
      textInput("备注", addForm.value.beizhu, (v) => addForm.value.beizhu = v, "例如账号昵称、用途"),
      textInput("收款人姓", addForm.value.receiver_surname, (v) => addForm.value.receiver_surname = v, "可选：群、张、李"),
    ], [button("取消", () => addVisible.value = false), button(saving.value ? "提交中..." : "确认添加", submitAdd, "primary", saving.value)]);

    const renderSurnameModal = () => modal(surnameVisible, `设置收款人姓 #${surnameForm.value.id}`, [
      textInput("收款人姓", surnameForm.value.receiver_surname, (v) => surnameForm.value.receiver_surname = v, "不填则支付页不显示"),
    ], [button("取消", () => surnameVisible.value = false), button("保存", submitSurname, "primary", saving.value)]);

    const renderTestModal = () => modal(testVisible, `测试通道 #${testForm.value.id}`, [
      textInput("测试金额", testForm.value.money, (v) => testForm.value.money = v, "0.01", "number"),
      textInput("商品名称", testForm.value.name, (v) => testForm.value.name = v, "测试商品"),
      h("p", { class: "wb-muted" }, "将只使用当前通道发起测试订单，用于验证支付和回调是否正常。"),
    ], [button("取消", () => testVisible.value = false), button("发起测试订单", submitTest, "primary", saving.value)]);

    const renderAliConfigModal = () => modal(aliConfigVisible, `支付宝免挂配置 #${aliForm.value.id}`, [
      textInput("AppID", aliForm.value.appid, (v) => aliForm.value.appid = v, "支付宝开放平台 AppID"),
      h("label", { class: "wb-field" }, [h("span", null, "应用私钥"), h("textarea", { value: aliForm.value.appkey2, placeholder: "请粘贴应用私钥", onInput: (e) => aliForm.value.appkey2 = e.target.value })]),
      textInput("支付宝收款码链接", aliForm.value.qr_url, (v) => aliForm.value.qr_url = v, "alipays://、alipayqr:// 或 https://"),
      selectInput("订单检查", aliForm.value.ali_order_check, [{ value: "order_amount", label: "订单号优先，匹配不到再按金额" }, { value: "order_no", label: "只按订单号" }], (e) => aliForm.value.ali_order_check = e.target.value),
      aliForm.value.money ? h("p", { class: "wb-muted" }, `当前余额：￥ ${aliForm.value.money}`) : null,
    ], [button("取消", () => aliConfigVisible.value = false), button("保存免挂配置", submitAliConfig, "primary", saving.value)]);

    const renderEditModal = () => modal(editVisible, `编辑收款码 #${editForm.value.id}`, [
      h("p", { class: "wb-muted" }, `当前通道：${editForm.value.typeName || editForm.value.type}。保存后不用删除通道，新订单会直接使用新的收款码配置。`),
      textInput(editForm.value.type === "usdt" ? "USDT-TRC20 地址" : "收款码链接", editForm.value.qr_url, (v) => editForm.value.qr_url = v, "通道原始收款码链接，可留空按通道逻辑处理"),
      textInput("自定义收款码链接", editForm.value.custom_qr_url, (v) => editForm.value.custom_qr_url = v, "填写后发起订单优先使用此链接；清空则取消自定义链接"),
      textInput("备注", editForm.value.beizhu, (v) => editForm.value.beizhu = v, "例如账号昵称、用途"),
      textInput("收款人姓", editForm.value.receiver_surname, (v) => editForm.value.receiver_surname = v, "可选：群、张、李；清空则不显示"),
      h("p", { class: "wb-muted" }, "提示：支付宝免 CK 的 AppID、私钥和收款码仍在“免挂配置”里维护；这里主要用于普通收款码和自定义收款码快速修改。"),
    ], [button("取消", () => editVisible.value = false), button(saving.value ? "保存中..." : "保存收款码", submitEditQr, "primary", saving.value)]);

    const renderUpdateModal = () => modal(updateVisible, `更新通道 #${updateForm.value.id}`, [
      h("div", { class: "wb-update-box" }, [
        h("div", { class: "wb-main" }, `${updateForm.value.typeLabel || loginTypeName()} / ${updateForm.value.hook === 2 ? "云端/免挂" : updateForm.value.hook === 1 ? "挂机" : "免挂"}`),
        updateForm.value.message ? h("p", { class: "wb-muted" }, updateForm.value.message) : null,
        updateForm.value.error ? h("p", { class: "wb-error" }, updateForm.value.error) : null,
        updateForm.value.wxClerks && updateForm.value.wxClerks.length ? h("div", { class: "wb-clerk-list" }, updateForm.value.wxClerks.map((item) => h("div", { class: "wb-clerk" }, [h("strong", null, item.wx_name || "可用微信"), h("span", null, item.wx_user || "请联系站长")])) ) : null,
        updateForm.value.type === "alipay" && updateForm.value.hook === 0 && !updateStarted.value ? h("div", { class: "wb-tip" }, "扫码登录可直接在新 UI 内完成；账号密码登录链路包含短信验证，后续如确实需要再继续补齐。") : null,
        updateForm.value.qrUrl ? h("div", { class: "wb-qr" }, [h("img", { src: updateForm.value.qrUrl, alt: "登录二维码" }), h("div", { class: "wb-muted" }, "扫码后请回到本页等待自动更新 CK")]) : null,
      ]),
    ], [
      updateForm.value.type === "alipay" && updateForm.value.hook === 0 && !updateStarted.value ? button("扫码登录", startLoginUpdate, "primary", updateBusy.value) : null,
      updateForm.value.error ? button("重新获取", startLoginUpdate, "primary", updateBusy.value) : null,
      button("关闭", () => closeUpdate(false)),
    ], () => closeUpdate(false));

    onMounted(refresh);

    return () => h("div", { class: "wb-channel-page" }, [
      h("style", null, `.wb-channel-page{padding:4px 2px 24px;color:#1f2937}.wb-card{background:#fff;border:1px solid #edf0f5;border-radius:16px;box-shadow:0 10px 30px rgba(15,23,42,.06);padding:18px;margin-bottom:16px}.wb-head{display:flex;justify-content:space-between;gap:16px;align-items:center}.wb-head h1{font-size:22px;margin:0 0 6px}.wb-muted{color:#6b7280;font-size:13px;line-height:1.65}.wb-blue{color:#2563eb;font-size:13px}.wb-error{color:#b91c1c;background:#fef2f2;border:1px solid #fecaca;padding:8px 10px;border-radius:10px}.wb-tip{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px;color:#475569;font-size:13px}.wb-stats{display:flex;gap:10px;flex-wrap:wrap}.wb-stat{padding:10px 14px;border-radius:12px;background:#f8fafc;border:1px solid #eef2f7}.wb-table-wrap{overflow:auto}.wb-table{width:100%;border-collapse:collapse}.wb-table th{background:#f8fafc;color:#64748b;text-align:left;font-weight:600}.wb-table th,.wb-table td{padding:13px 12px;border-bottom:1px solid #edf2f7;vertical-align:middle}.wb-main{font-weight:600}.wb-status{max-width:220px;line-height:1.5}.wb-tag{display:inline-flex;align-items:center;padding:3px 9px;border-radius:999px;font-size:12px;margin:2px 4px 2px 0;background:#f1f5f9;color:#475569}.wb-tag-success{background:#ecfdf5;color:#047857}.wb-tag-danger{background:#fef2f2;color:#b91c1c}.wb-tag-warning{background:#fff7ed;color:#c2410c}.wb-actions{display:flex;gap:7px;flex-wrap:wrap}.wb-btn{border:1px solid #d9e2ec;background:#fff;color:#334155;border-radius:9px;padding:7px 11px;cursor:pointer;font-size:13px}.wb-btn:disabled{opacity:.55;cursor:not-allowed}.wb-btn-primary{background:#2563eb;border-color:#2563eb;color:#fff}.wb-btn-success{background:#16a34a;border-color:#16a34a;color:#fff}.wb-btn-info{background:#0891b2;border-color:#0891b2;color:#fff}.wb-btn-warning{background:#f59e0b;border-color:#f59e0b;color:#fff}.wb-btn-danger{background:#dc2626;border-color:#dc2626;color:#fff}.wb-empty{text-align:center;color:#94a3b8;padding:36px!important}.wb-pagination,.wb-inline-form{display:flex;align-items:center;gap:10px;justify-content:flex-end;margin-top:14px}.wb-inline-form{justify-content:flex-start}.wb-inline-form input,.wb-field input,.wb-field select,.wb-field textarea{width:100%;box-sizing:border-box;border:1px solid #dce3ec;border-radius:10px;padding:9px 11px;font-size:14px;background:#fff;color:#111827}.wb-field textarea{min-height:110px;resize:vertical}.wb-field{display:block;margin-bottom:13px}.wb-field span{display:block;font-size:13px;color:#475569;margin-bottom:6px}.wb-modal-mask{position:fixed;inset:0;background:rgba(15,23,42,.38);display:flex;align-items:center;justify-content:center;z-index:9999;padding:18px}.wb-modal{width:min(620px,96vw);max-height:92vh;overflow:auto;background:#fff;border-radius:18px;box-shadow:0 20px 60px rgba(15,23,42,.22)}.wb-modal-head,.wb-modal-foot{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:16px 18px;border-bottom:1px solid #eef2f7}.wb-modal-foot{border-top:1px solid #eef2f7;border-bottom:0;justify-content:flex-end}.wb-modal-body{padding:18px}.wb-close{border:0;background:#f1f5f9;border-radius:50%;width:28px;height:28px;cursor:pointer;color:#475569}.wb-paypass h2{margin:0 0 8px}.wb-update-box{display:flex;flex-direction:column;gap:12px}.wb-qr{text-align:center;padding:12px;border-radius:14px;background:#f8fafc;border:1px solid #e2e8f0}.wb-qr img{width:220px;height:220px;object-fit:contain;border:1px solid #dbeafe;background:#fff;border-radius:12px}.wb-clerk-list{display:grid;gap:8px}.wb-clerk{display:flex;justify-content:space-between;gap:10px;padding:10px 12px;border-radius:10px;background:#f8fafc;border:1px solid #e2e8f0}@media(max-width:760px){.wb-head{display:block}.wb-table th,.wb-table td{white-space:nowrap}.wb-actions{min-width:240px}}`),
      h("div", { class: "wb-card wb-head" }, [
        h("div", null, [h("h1", null, "通道列表"), h("div", { class: "wb-muted" }, "新 UI 原生通道管理，不再嵌入旧通道列表页面。")]),
        h("div", { class: "wb-stats" }, [h("div", { class: "wb-stat" }, `当前：${config.value.total || total.value}`), h("div", { class: "wb-stat" }, `最大：${config.value.max || 0}`), button("刷新", refresh), button("新增通道", openAdd, "primary", !config.value.payPassVerified)]),
      ]),
      !config.value.payPassVerified ? renderPayPass() : renderTable(),
      renderAddModal(),
      renderSurnameModal(),
      renderTestModal(),
      renderAliConfigModal(),
      renderEditModal(),
      renderUpdateModal(),
    ]);
  },
});
