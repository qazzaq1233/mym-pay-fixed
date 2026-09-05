<?php

function mym_pay_channel_defaults()
{
    return array(
        'types' => array(
            'alipay' => array('name' => '支付宝', 'status' => 1, 'sort' => 10),
            'wxpay' => array('name' => '微信', 'status' => 1, 'sort' => 20),
            'qqpay' => array('name' => 'QQ钱包', 'status' => 1, 'sort' => 30),
            'usdt' => array('name' => 'USDT-TRC20', 'status' => 1, 'sort' => 40),
        ),
        'channels' => array(
            'mg_ali' => array('type' => 'alipay', 'name' => '免挂->收款码', 'status' => 1, 'sort' => 10),
            'mg_alimp' => array('type' => 'alipay', 'name' => '免挂->收款名片', 'status' => 1, 'sort' => 20),
            'yd_ali' => array('type' => 'alipay', 'name' => '免挂->免CK(不掉线)', 'status' => 1, 'sort' => 30),
            'mg_wx' => array('type' => 'wxpay', 'name' => '免挂->收款码->店员', 'status' => 1, 'sort' => 10),
            'mg_wx_jy' => array('type' => 'wxpay', 'name' => '免挂->经营码->店员', 'status' => 1, 'sort' => 20),
            'mg_wx_sn' => array('type' => 'wxpay', 'name' => '免挂->商业码->店员', 'status' => 1, 'sort' => 30),
            'mg_wx_skd' => array('type' => 'wxpay', 'name' => '免挂->收款单->店员', 'status' => 1, 'sort' => 40),
            'mg_wx_zs' => array('type' => 'wxpay', 'name' => '免挂->赞赏码->站长', 'status' => 1, 'sort' => 50),
            'pc_wx' => array('type' => 'wxpay', 'name' => '挂机->收款码', 'status' => 1, 'sort' => 60),
            'pc_wx_jy' => array('type' => 'wxpay', 'name' => '挂机->经营码', 'status' => 1, 'sort' => 70),
            'pc_wx_sn' => array('type' => 'wxpay', 'name' => '挂机->商业码', 'status' => 1, 'sort' => 80),
            'pc_wx_skd' => array('type' => 'wxpay', 'name' => '挂机->收款单', 'status' => 1, 'sort' => 90),
            'pc_wx_zs' => array('type' => 'wxpay', 'name' => '挂机->赞赏码', 'status' => 1, 'sort' => 100),
            'yd_wx_uos' => array('type' => 'wxpay', 'name' => '云端->UOS云端（通用收款）', 'status' => 1, 'sort' => 110),
            'yd_wx' => array('type' => 'wxpay', 'name' => '云端->收款码免输入(Mac)', 'status' => 1, 'sort' => 120),
            'yd_wx_gskd' => array('type' => 'wxpay', 'name' => '云端->收款单个人版免输入(Windows)', 'status' => 1, 'sort' => 130),
            'yd_wx_sskd' => array('type' => 'wxpay', 'name' => '云端->收款单商家版免输入(Windows)', 'status' => 1, 'sort' => 140),
            'mg_vzq' => array('type' => 'wxpay', 'name' => '免挂->微信转QQ', 'status' => 1, 'sort' => 150),
            'yd_vzq' => array('type' => 'wxpay', 'name' => '云端->微信转QQ', 'status' => 1, 'sort' => 160),
            'mg_wx_qqhb' => array('type' => 'wxpay', 'name' => '免挂->微信QQ红包H5(开发中)', 'status' => 1, 'sort' => 170),
            'yd_wx_qqhb' => array('type' => 'wxpay', 'name' => '云端->微信QQ红包H5(开发中)', 'status' => 1, 'sort' => 180),
            'mg_qq' => array('type' => 'qqpay', 'name' => '免挂->收款码', 'status' => 1, 'sort' => 10),
            'yd_qq' => array('type' => 'qqpay', 'name' => '云端->收款码', 'status' => 1, 'sort' => 20),
            'usdt' => array('type' => 'usdt', 'name' => 'USDT-TRC20', 'status' => 1, 'sort' => 10),
        )
    );
}

function mym_pay_channel_config()
{
    global $conf;
    $defaults = mym_pay_channel_defaults();
    $saved = array();
    if(!empty($conf['pay_channel_config'])){
        $saved = json_decode(html_entity_decode($conf['pay_channel_config']), true);
        if(!is_array($saved))$saved = array();
    }
    foreach(array('types','channels') as $group){
        if(!isset($saved[$group]) || !is_array($saved[$group]))$saved[$group] = array();
        foreach($defaults[$group] as $code => $item){
            if(!isset($saved[$group][$code]) || !is_array($saved[$group][$code])){
                $saved[$group][$code] = $item;
            }else{
                $saved[$group][$code] = array_merge($item, $saved[$group][$code]);
            }
        }
        foreach($saved[$group] as $code => $item){
            if(!is_array($item)){
                unset($saved[$group][$code]);
                continue;
            }
            $saved[$group][$code]['code'] = $code;
            if(!isset($saved[$group][$code]['name']) || trim($saved[$group][$code]['name'])==='')$saved[$group][$code]['name'] = $code;
            if(!isset($saved[$group][$code]['status']))$saved[$group][$code]['status'] = 1;
            if(!isset($saved[$group][$code]['sort']))$saved[$group][$code]['sort'] = 999;
            if($group=='channels' && (!isset($saved[$group][$code]['type']) || !isset($saved['types'][$saved[$group][$code]['type']]))){
                unset($saved[$group][$code]);
                continue;
            }
            $saved[$group][$code]['status'] = intval($saved[$group][$code]['status']) ? 1 : 0;
            $saved[$group][$code]['sort'] = intval($saved[$group][$code]['sort']);
        }
    }
    uasort($saved['types'], function($a, $b){ return intval($a['sort']) - intval($b['sort']); });
    uasort($saved['channels'], function($a, $b){
        if($a['type'] == $b['type'])return intval($a['sort']) - intval($b['sort']);
        return strcmp($a['type'], $b['type']);
    });
    return $saved;
}

function mym_save_pay_channel_config($config)
{
    global $DB,$CACHE;
    $json = daddslashes(json_encode($config, JSON_UNESCAPED_UNICODE));
    $ret = $DB->query("REPLACE INTO `pay_config` SET `v`='{$json}',`k`='pay_channel_config'");
    if($CACHE)$CACHE->clear();
    return $ret;
}

function mym_pay_type_list($enabled_only=true)
{
    $config = mym_pay_channel_config();
    $list = array();
    foreach($config['types'] as $code => $item){
        if($enabled_only && intval($item['status']) != 1)continue;
        $list[$code] = $item;
    }
    return $list;
}

function mym_pay_channel_list($type=null,$enabled_only=true)
{
    $config = mym_pay_channel_config();
    $list = array();
    foreach($config['channels'] as $code => $item){
        if($type !== null && $item['type'] != $type)continue;
        if($enabled_only && intval($item['status']) != 1)continue;
        if($enabled_only && isset($config['types'][$item['type']]) && intval($config['types'][$item['type']]['status']) != 1)continue;
        $list[$code] = $item;
    }
    return $list;
}

function mym_pay_channel_enabled($type,$channel='')
{
    $config = mym_pay_channel_config();
    if(!isset($config['types'][$type]) || intval($config['types'][$type]['status']) != 1)return false;
    if($channel === '')return true;
    if(!isset($config['channels'][$channel]))return false;
    if($config['channels'][$channel]['type'] != $type)return false;
    return intval($config['channels'][$channel]['status']) == 1;
}

function mym_pay_channel_name($code,$group='channels')
{
    $config = mym_pay_channel_config();
    if(isset($config[$group][$code]))return $config[$group][$code]['name'];
    return $code;
}

function cookie_zt($res,$status=null)//cookie检测
{
global $DB,$conf,$date;
	if($res['status']!=1){
		if($res['type']=='wxpay' and $res['hook_type']==0)
			if($res['channel']=='mg_vzq' or $res['channel']=='mg_wx_uos'){
			    return ['msg'=>'<font color=red>本地未登录</font>','status'=>false];
			}elseif($res['channel']=='yd_vzq' or $res['channel']=='mg_wx_uos'){
			    return ['msg'=>'<font color=red>云端未登录</font>','status'=>false];
			}else{
			    return ['msg'=>'<font color=red>未绑定店员或已解绑->'.$res['endtime'].'</font>','status'=>false];
			}
		elseif($res['hook_type']==0)
			return ['msg'=>'<font color=red>CK状态失效->'.$res['endtime'].'</font>','status'=>false];
		elseif($res['hook_type']==1)
			return ['msg'=>'<font color=red>软件不在线或已掉线</font>','status'=>false];
		elseif($res['type']=='wxpay' and $res['hook_type']==2)
		    return ['msg'=>'<font color=red>云端不在线或已掉线</font>','status'=>false];
		elseif($res['type']=='alipay' and $res['hook_type']==2)
		    return ['msg'=>'<font color=red>'.($res['channel']=='yd_ali'?'免挂配置异常或未配置':'云端配置异常或未配置').'</font>','status'=>false];
		else
		    return ['msg'=>'<font color=red>云端不在线或已掉线</font>','status'=>false];
	}else{
		if($res['type']=='wxpay'){
		    if($res['channel']=='mg_vzq' or $res['channel']=='yd_vzq'){
		        if($res['hook_type']==0){
		            return ['msg'=>'<font color=green>本地在线中</font>','status'=>true];
		        }else{
		            return ['msg'=>'<font color=green>云端在线中</font>','status'=>true];
		        }
		    }else{
		        $login_time = time();
		        $login_wxpay = $DB->query("SELECT * FROM `pay_wechat_trumpet` WHERE `status`='1' and `login_time`>'{$login_time}' and `wx_name`='{$res['wx_name']}' limit 1")->fetch();
		        if($login_wxpay){
		            return ['msg'=>'<font color=green>店员在线中</font>','status'=>true];
		        }elseif($res['hook_type']==0){
		            return ['msg'=>'<font color=red>绑定的店员已掉线,请联系站长处理</font>','status'=>false];
		        }elseif($res['hook_type']==1 and $res['crontime']>time()){
		            return ['msg'=>'<font color=green>软件在线中</font>','status'=>true];
		        }elseif($res['hook_type']==1 and $res['crontime']<time()){
		            return ['msg'=>'<font color=red>软件不在线或已掉线</font>','status'=>false];
		        }else{
		            return ['msg'=>'<font color=green>云端在线中</font>','status'=>true];
		        }
		    }
		}elseif($res['hook_type']==0){
		    if(function_exists('mym_qr_needs_ck_check') && mym_qr_needs_ck_check($res) && $res['crontime']<time()){
		        $qr_json = json_decode(isset($res['json']) ? $res['json'] : '', true);
		        if(!is_array($qr_json))$qr_json = array();
		        $fail_count = isset($qr_json['ck_fail_count']) ? intval($qr_json['ck_fail_count']) : 0;
		        $last_success = isset($qr_json['ck_last_success']) ? intval($qr_json['ck_last_success']) : 0;
		        $grace_time = 1800;
		        if($last_success>0 && time()-$last_success<7200){
		            return ['msg'=>'<font color=green>CK保活中，等待下次检测</font>','status'=>true];
		        }
		        if($fail_count>0 && $fail_count<20){
		            return ['msg'=>'<font color=#ff9900>CK检测异常，保活重试中('.$fail_count.'/20)</font>','status'=>true];
		        }
		        if($res['crontime'] + $grace_time > time()){
		            return ['msg'=>'<font color=green>本地在线中，等待保活检测</font>','status'=>true];
		        }
		        return ['msg'=>'<font color=#ff9900>CK等待保活检测，请确认定时任务运行</font>','status'=>true];
		    }
		    return ['msg'=>'<font color=green>本地在线中</font>','status'=>true];
		}elseif($res['type']=='alipay' and $res['hook_type']==2){
		    if($res['channel']=='yd_ali'){
		        if($DB->query("SELECT * FROM `pay_alidata` WHERE `qr_id`='{$res['id']}' limit 1")->fetch()){
		            return ['msg'=>'<font color=green>免挂在线中</font>','status'=>true];
		        }else{
		            return ['msg'=>'<font color=red>支付宝免挂未配置应用</font>','status'=>false];
		        }
		    }
		    if($DB->query("SELECT * FROM `pay_alidata` WHERE `qr_id`='{$res['id']}' limit 1")->fetch()){
		        return ['msg'=>'<font color=green>云端在线中</font>','status'=>true];
		    }else{
		        return ['msg'=>'<font color=red>支付宝云端未配置应用</font>','status'=>false];
		    }
		}elseif($res['type']=='qqpay' and $res['hook_type']==2 and $res['status']==1){
		    return ['msg'=>'<font color=green>云端在线中</font>','status'=>true];
		}elseif($res['type']=='qqpay' and $res['hook_type']==2){
		    return ['msg'=>'<font color=red>云端不在线或已掉线</font>','status'=>false];
		}elseif($res['hook_type']==1 and $res['crontime']>time()){
		    return ['msg'=>'<font color=green>PC软件正常在线</font>','status'=>true];
		}elseif($res['hook_type']==1 and $res['crontime']<time()){
		    return ['msg'=>'<font color=red>PC软件不在线或已掉线</font>','status'=>false];
		}
	}
}

function cookie_zt_pc($zt,$crontime)//cookie检测
{
	if($zt==1 and $crontime>time())
		return '<font color=green>PC软件正常在线</font>';
	else
		return '<font color=red>PC软件不在线或已掉线</font>';
}

function price_zt($res)//出码状态
{
	if($res['price']==-0.01)
		return '<font color=red>站点不行,请提醒站长</font>';
	elseif($res['price']>0.00)
		return '<font color=green>通讯完成</font>';
	elseif($res['apittime']>=time())
		return '<font color=red>正在与站点通讯...</font>';
	else
		return '<font color=red>当前订单重复</font>';
}

function pay_type($res)//支付方式中文化
{
    $name = '';
    $channels = mym_pay_channel_list(null,false);
    $types = mym_pay_type_list(false);
    if(isset($res['channel']) && $res['channel']!='' && isset($channels[$res['channel']])){
        $name = mym_pay_channel_name($res['channel'], 'channels');
    }elseif(isset($res['type']) && isset($types[$res['type']])){
        $name = mym_pay_channel_name($res['type'], 'types');
    }
    if($name!='')return '<font color=green>'.htmlspecialchars($name).'</font>';
    if(strstr($res['type'], 'qqhpay'))return '<font color=green>QQ红包</font>';
}
function wachat_login_zt($login_time)//微信店员在线状态
{
	if($login_time>=time())
		return '<font color=green>在线</font>';
	else
		return '<font color=red>不在线</font>';
}
function wachat_zt($status)//上下架状态
{
	if($status==1)
		return '<font color=green>已上架</font>';
	else
		return '<font color=red>已下架</font>';
}

function order_zt($res)//订单状态
{
	if($res['status']==1)
		return '<font color=green>已完成</font>';
	elseif($res['status']==2)
		return '<font color=red>订单失效</font>';
	else
		return '<font color=red>未完成</font>';
}

function hook_type($res)
{
    if($res['type']==1){
        return '<font color=red>挂机版</font>';
    }elseif($res['type']==2){
        return '<font color=red>云端版</font>';
    }else{
        return '<font color=red>本地版</font>';
    }
}

function type_yun($res)
{
    if($res['hook_type']==0){
        return '免挂';
    }elseif($res['hook_type']==1){
        return '挂机';
    }else{
        return '云端';
    }
}

function WxMoney($row){
    if($row['type']=='wxpay'){
        if($row['channel']=='mg_vzq' or $row['channel']=='yd_vzq'){
            return '<b>￥ '.$row['money'].'</b>';
        }else{
            return '不支持此功能';
        }
    }else{
        return '<b>￥ '.$row['money'].'</b>';
    }
}

?>