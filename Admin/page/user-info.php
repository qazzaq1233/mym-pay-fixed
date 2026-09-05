<?php
include("../../Mym/Common.php");
if($islogin_admin==1){}else exit("<script language='javascript'>window.location.href='./Login.php';</script>");
$ga = new \lib\GoogleAuthenticator();
//这是生成的密钥，每个用户唯一一个，为用户保存起来用于验证
$secret = $ga->createSecret();
$_SESSION['goid'] = $secret;
$qrCodeUrl = $ga->getQRCodeGoogleUrl($conf['admin_user'], $secret, 'googleVerify');

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>个人中心</title>
    <link rel="stylesheet" href="../assets/libs/layui/css/layui.css"/>
    <link rel="stylesheet" href="../assets/module/admin.css?v=318"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        /* 用户信息 */
        .user-info-head {
            width: 110px;
            height: 110px;
            line-height: 110px;
            position: relative;
            display: inline-block;
            border: 2px solid #eee;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            margin: 0 auto;
        }

        .user-info-head:hover:after {
            content: '\e681';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.3);
            font-size: 28px;
            padding-top: 2px;
            font-style: normal;
            font-family: layui-icon;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .user-info-head img {
            width: 110px;
            height: 110px;
        }

        .user-info-list-item {
            position: relative;
            padding-bottom: 8px;
        }

        .user-info-list-item > .layui-icon {
            position: absolute;
        }

        .user-info-list-item > p {
            padding-left: 30px;
        }

        .layui-line-dash {
            border-bottom: 1px dashed #ccc;
            margin: 15px 0;
        }

        /* 基本信息 */
        #userInfoForm .layui-form-item {
            margin-bottom: 25px;
        }

        /* 账号绑定 */
        .user-bd-list-item {
            padding: 14px 60px 14px 10px;
            border-bottom: 1px solid #e8e8e8;
            position: relative;
        }

        .user-bd-list-item .user-bd-list-lable {
            color: #333;
            margin-bottom: 4px;
        }

        .user-bd-list-item .user-bd-list-oper {
            position: absolute;
            top: 50%;
            right: 10px;
            margin-top: -8px;
            cursor: pointer;
        }

        .user-bd-list-item .user-bd-list-img {
            width: 48px;
            height: 48px;
            line-height: 48px;
            position: absolute;
            top: 50%;
            left: 10px;
            margin-top: -24px;
        }

        .user-bd-list-item .user-bd-list-img + .user-bd-list-content {
            margin-left: 68px;
        }
    </style>
</head>
<body>
<!-- 正文开始 -->
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <!-- 左 -->
        <div class="layui-col-sm12 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 25px;">
                    <div class="text-center layui-text">
                        <div class="user-info-head" id="userInfoHead">
                            <img src="//q4.qlogo.cn/headimg_dl?dst_uin=<?=$conf['qq']?>&spec=640" alt=""/>
                        </div>
                        <h2 style="padding-top: 20px;"><?=$conf['admin_user']?></h2>
                        <p style="padding-top: 8px;">海纳百川，有容乃大</p>
                    </div>
                    <div class="layui-text" style="padding-top: 30px;">
                        <div class="user-info-list-item">
                            <i class="layui-icon layui-icon-username"></i>
                            <p>资深前端工程师</p>
                        </div>
                        <div class="user-info-list-item">
                            <i class="layui-icon layui-icon-release"></i>
                            <p>某某公司 - 某某事业群 - 某某技术部</p>
                        </div>
                        <div class="user-info-list-item">
                            <i class="layui-icon layui-icon-location"></i>
                            <p>中国 • 浙江省 • 杭州市</p>
                        </div>
                        <div class="user-info-list-item">
                            <i class="layui-icon layui-icon-note"></i>
                            <p>JavaScript、HTML、CSS、Vue、Node</p>
                        </div>
                    </div>
                    <div class="layui-line-dash"></div>
                    <h3>标签</h3>
                    <div class="layui-badge-list" style="padding-top: 6px;">
                        <span class="layui-badge layui-bg-gray">很有想法的</span>
                        <span class="layui-badge layui-bg-gray">专注设计</span>
                        <span class="layui-badge layui-bg-gray">辣~</span>
                        <span class="layui-badge layui-bg-gray">大长腿</span>
                        <span class="layui-badge layui-bg-gray">川妹子</span>
                        <span class="layui-badge layui-bg-gray">海纳百川</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- 右 -->
        <div class="layui-col-sm12 layui-col-md9">
            <div class="layui-card">
                <!-- 选项卡开始 -->
                <div class="layui-tab layui-tab-brief" lay-filter="userInfoTab">
                    <ul class="layui-tab-title">
                        <li class="layui-this">基本信息</li>
                        <li>账号绑定</li>
                        <li>谷歌验证</li>
                    </ul>
                    <div class="layui-tab-content">
                        <!-- tab1 -->
                        <div class="layui-tab-item layui-show">
                            <form class="layui-form" id="userInfoForm" lay-filter="userInfoForm"
                                  style="max-width: 400px;padding: 25px 10px 0 0;">
                                <div class="layui-form-item">
                                    <label class="layui-form-label layui-form-required">邮箱:</label>
                                    <div class="layui-input-block">
                                        <input name="email" value="<?=$conf['mail_recv']; ?>" class="layui-input"
                                               lay-verify="required" required/>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label layui-form-required">昵称:</label>
                                    <div class="layui-input-block">
                                        <input name="name" value="Serati Ma" class="layui-input"
                                               lay-verify="required" required/>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">个人简介:</label>
                                    <div class="layui-input-block">
                                        <textarea name="desc" placeholder="个人简介" class="layui-textarea"></textarea>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">街道地址:</label>
                                    <div class="layui-input-block">
                                        <input name="address" value="西湖区工专路 77 号" class="layui-input"/>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">联系电话:</label>
                                    <div class="layui-input-block">
                                        <input name="phone1" value="0752" class="layui-input" style="width: 60px;"/>
                                        <div style="position: absolute;left: 70px;right: 0;top: 0;">
                                            <input name="phone2" value="268888888" class="layui-input"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn" lay-filter="userInfoSubmit" lay-submit>更新基本信息
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- tab1 -->
                        <div class="layui-tab-item" style="padding-bottom: 20px;">
                            <div class="user-bd-list layui-text">
                                <div class="user-bd-list-item">
                                    <div class="user-bd-list-lable">密保手机</div>
                                    <div class="user-bd-list-text">已绑定手机：138****8293</div>
                                    <a class="user-bd-list-oper">修改</a>
                                </div>
                                <div class="user-bd-list-item">
                                    <div class="user-bd-list-lable">密保邮箱</div>
                                    <div class="user-bd-list-text">已绑定邮箱：<?=$conf['mail_recv']; ?></div>
                                    <a class="user-bd-list-oper">修改</a>
                                </div>
                                <div class="user-bd-list-item">
                                    <div class="user-bd-list-img">
                                        <i class="layui-icon layui-icon-login-qq"
                                           style="color: #3492ED;font-size: 48px;"></i>
                                    </div>
                                    <div class="user-bd-list-content">
                                        <div class="user-bd-list-lable">绑定QQ</div>
                                        <div class="user-bd-list-text">当前未绑定QQ账号</div>
                                    </div>
                                    <a class="user-bd-list-oper">绑定</a>
                                </div>
                                <div class="user-bd-list-item">
                                    <div class="user-bd-list-img">
                                        <i class="layui-icon layui-icon-login-wechat"
                                           style="color: #4DAF29;font-size: 48px;"></i>
                                    </div>
                                    <div class="user-bd-list-content">
                                        <div class="user-bd-list-lable">绑定微信</div>
                                        <div class="user-bd-list-text">当前未绑定绑定微信账号</div>
                                    </div>
                                    <a class="user-bd-list-oper">绑定</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="layui-tab-item layui-show">
                            <form class="layui-form" id="userInfoForm" lay-filter="userInfoForm"
                                  style="max-width: 400px;padding: 25px 10px 0 0;">
                                <?php if(!$conf['goid']){ ?>
                                <div class="layui-form-item">
                                    <p>未绑定：未绑定谷歌验证</p>
                                    <p style="color: red;font-size: 15px;word-break: keep-all;">提示：为了你的资金安全，请尽快绑定谷歌验证码</p>
                                    <p>手机端下载地址：<a href="http://m.2265.com/down/302457.html" target="_blank">Android</a>／<a href="https://apps.apple.com/cn/app/google-authenticator/id388497605" target="_blank">IOS</a></p>
                                </div>
                                <div class="layui-card-body text-center" style="height: 126px;box-sizing: border-box;">
                                    <div id="otherQRCode" class="layui-inline"><img src="<?=$qrCodeUrl?>" width="100"></div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label layui-form-required">谷歌验证码:</label>
                                    <div class="layui-input-block">
                                        <input name="code" value="" class="layui-input"
                                               lay-verify="required" required/>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn" lay-filter="userInfoSubmit" lay-submit>绑定谷歌验证
                                        </button>
                                    </div>
                                </div>
                                <?php }else{ ?>
                                <div class="layui-form-item">
                                    <p style="color: red;font-size: 15px;word-break: keep-all;">提示：已绑定谷歌验证码</p>
                                </div>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- //选项卡结束 -->
            </div>
        </div>
    </div>
</div>

<!-- js部分 -->
<script type="text/javascript" src="../assets/libs/layui/layui.js"></script>
<script type="text/javascript" src="../assets/js/common.js?v=318"></script>
<script>
    layui.use(['layer', 'form', 'element', 'admin'], function () {
        var $ = layui.jquery;
        var layer = layui.layer;
        var form = layui.form;
        var element = layui.element;
        var admin = layui.admin;

        /* 选择头像 */
        $('#userInfoHead').click(function () {
            admin.cropImg({
                imgSrc: $('#userInfoHead>img').attr('src'),
                onCrop: function (res) {
                    $('#userInfoHead>img').attr('src', res);
                    parent.layui.jquery('.layui-layout-admin>.layui-header .layui-nav img.layui-nav-img').attr('src', res);
                }
            });
        });

        /* 监听表单提交 */
        form.on('submit(userInfoSubmit)', function (data) {
            var loadIndex = layer.load(2);
            $.post('../Ajax.php?act=gocode', data.field, function (res) {  // 实际项目这里url可以是mData?'user/update':'user/add'
            layer.close(loadIndex);
            if (res.code === 200) {
                layer.msg('设置保存成功！', {icon: 1});
            } else {
                layer.msg(res.msg, {icon: 2});
            }
                
            }, 'json');
            return false;
            layer.msg(JSON.stringify(data.field));
            return false;
        });

    });
</script>
</body>
</html>