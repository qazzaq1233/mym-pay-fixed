<?php
class WxApi {
    protected $Api_Url 	= null;
    function __construct($Api_Url = 'NULL')
    {
        $count = substr_count($Api_Url,"/");
        if($count==2){
            $this->Api_Url = $Api_Url.'/';
        }else{
            $this->Api_Url = $Api_Url;
        }
    }

function GetQr($type=null,$daili=null)//获取登录二维码
{
    if($type=='ipad'){
        $url = $this->Api_Url."VXAPI/Login/GetQR";
        $post = json_encode([
            'DeviceID'=>'string',
            'DeviceName'=>'string',
            'OSModel'=>'string',
            'Proxy'=>[
                'ProxyIp'=>$daili['ip'].':'.$daili['do'],
                'ProxyUser'=>$daili['user'],
                'ProxyPassword'=>$daili['pass']
            ]
        ]);
        
        $response = $this->Yun_Get($url,$post);
        $response = json_decode($response,true);
        if($response['Code']==1){
            $result = array("code"=>1,"msg"=>"获取成功","uuid"=>$response['Data']['Uuid'],"guid"=>$guid,"qr_url"=>$response['Data']['QrBase64']);
        }else{
            $result=array("code"=>-1,"msg"=>"获取失败");
        }
        exit(json_encode($result));
    }
    $url = $this->Api_Url."api/Client/WXCreate";
    $response = $this->Yun_Get($url,1);
    $response = json_decode($response,true);
    $guid = $response['data']['Guid'];
    $url = $this->Api_Url."api/Login/WXGetLoginQrcode";
    $post = json_encode(['Guid'=>$response['data']['Guid']]);
    $response = $this->Yun_Get($url,$post);
    $response = json_decode($response,true);
    
    if($response['data']['qrcode']){
       $result=array("code"=>1,"msg"=>"获取成功","uuid"=>$response['data']['uuid'],"guid"=>$guid,"qr_url"=>$response['data']['qrcode']);
    }else{
       $result=array("code"=>-1,"msg"=>"获取失败");
    }
    return $result;
}

function Login($guid,$uuid,$type=null)//验证登录
{
    if($type=='ipad'){
        $url = $this->Api_Url."VXAPI/Login/CheckQR?uuid=".$uuid;
        $response = json_decode($this->Yun_Get($url,1),true);
        if($response['Code']==0){
            if($response['Data']['status']==1){
                $result=array("code"=>2,"msg"=>"等待确认中","state"=>$response['Data']['status']);
            }elseif($response['Message']=='登陆成功' or $response['Message']=='\u767b\u9646\u6210\u529f'){
                $wxid=$response['Data']['acctSectResp']['userName'];
                $url = $this->Api_Url."/VXAPI/Login/Newinit?wxid={$wxid}";
                $response = json_decode($this->Yun_Get($url,1),true);
                $result=array("code"=>1,"msg"=>"登录成功","guid"=>$guid,"wxid"=>$wxid);
            }
        }
    }else{
        $url = $this->Api_Url."api/Login/WXCheckLoginQrcode";
        $post = json_encode(['Guid'=>$guid,'Uuid'=>$uuid]);
        $response = json_decode($this->Yun_Get($url,$post),true);
        if($response['data']['state']==0){
            $result=array("code"=>0,"msg"=>"等待扫码中","state"=>$response['data']['state']);
        }elseif($response['data']['state']==1){
            $result=array("code"=>2,"msg"=>"等待确认中","state"=>$response['data']['state']);
        }elseif($response['data']['state']==4){
            $result=array("code"=>-1,"msg"=>"系统错误,请联系管理员","state"=>$response['data']['state']);
        }elseif($response['data']['state']==2){
            $WXLogin=$this->Api_Url."api/Login/WXLoginManual";
            $pass=$response['data']['wxnewpass'];
            $wxid=$response['data']['wxid'];
            $post = json_encode(['Guid'=>$guid,'Channel'=>0,'UserName'=>$wxid,'Password'=>$pass,'Slider'=>true]);
            $res2=json_decode($this->Yun_Get($WXLogin,$post),true);
            if($res2['data']['Code']==1){
                $json = json_decode($res2['data']['Message'],true);
                $result=array("code"=>1,"msg"=>"登录成功","guid"=>$guid,'nickName'=>$json['NickName'],'user'=>($json['Alias']?$json['Alias']:$json['UserName']));
            }else{
            
            }
        }
    }
    
    return $result;
}

function WXSyncMsg($guid,$type=null)//获取微信信息
{
    if($type=='ipad'){
        $url = $this->Api_Url."VXAPI/Msg/Sync";
        $post = json_encode(['Scene'=>0,'Synckey'=>'string','Wxid'=>$guid]);
    }else{
        $url = $this->Api_Url."api/Message/WXSyncMsg";
        $post = json_encode(['Guid'=>$guid]);
    }
    
    return json_decode($this->Yun_Get($url,$post),true);
}

function WXHeartBeatY($guid,$type=null)
{
    if($type=='ipad'){
        $url = $this->Api_Url."VXAPI/Login/HeartBeat?wxid={$guid}";
        $post = 1;
    }else{
        $url = $this->Api_Url."api/Heartbeat/WXHeartBeat";
        $post = json_encode(['Guid'=>$guid]);
    }
    $response = json_decode($this->Yun_Get($url,$post),true);
    return json_decode($this->Yun_Get($url,$post),true);
}

function WXJSLogin($guid,$appid='wx28be8489b7a36aaa')
{
    $url = $this->Api_Url."api/Auth/WXJSLogin";
    $post = json_encode(['Guid'=>$guid,'AppId'=>$appid]);
    return json_decode($this->Yun_Get($url,$post),true)['data']['code'];
}

function baodindy($code,$token)
{
    $v = '5.132.7';
    $response = json_decode(get_curl('https://payapp.weixin.qq.com/qrapp/user/login?v='.$v.'&js_code='.$code),true);
    if($response['retcode']==0){
        $sid = $response['data']['sid'];
        $url = 'https://payapp.weixin.qq.com/qrapp/user/notifierbindinfo?sid='.$sid.'&v='.$v;
        //{"v":"5.132.7","token":"i_vhUagMq8TRuUDKDRetyuog","sid":"AAHTO1q2PfyRi-MhI4tp-0-ihU-1iBdKShch96aiNxwGJg"}
        $post = json_encode(['v'=>$v,'token'=>$token,'sid'=>$sid]);
        $response = json_decode(get_curl($url,$post),true);
        if($response['retcode']==0){
            $payee_nickname = $response['data']['payee_nickname'];
            $payee_openid = $response['data']['payee_openid'];
            $name = $payee_nickname;
            $url = 'https://payapp.weixin.qq.com/qrapp/user/confirmbind?sid='.$sid.'&v='.$v;
            $post = json_encode(['v'=>$v,'token'=>$token,'payee_openid'=>$payee_openid,'sid'=>$sid]);
            $response = json_decode(get_curl($url,$post,'https://servicewechat.com/wx28be8489b7a36aaa/969/page-frame.html',0,0,'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36 MicroMessenger/7.0.9.501 NetType/WIFI MiniProgramEnv/Windows WindowsWechat'),true);
            if($response['msg']=='succeed'){
                $test = '添加店员成功';
            }else{
                $test = '添加店员失败';
            }
        }else{
            $test = '获取邀请人信息失败';
        }
    }else{
        $test = '获取邀请人信息失败';
    }
}
/*---------------------收款单个人版--------------------------*/

function Get_Qrcode($row,$money,$trade_no){
    $cookie = $row['cookie'];
    $row = json_decode($row['json'],true);
    $account_id = $row['account_id'];
    if(!$row['sid']){
        $sid = $this->Get_sid($cookie);
    }else{
        $sid = $row['sid'];
    }
    $money=$money*100;
    $Url = "https://payapp.wechatpay.cn/receiptwxmgr/receipt/create?miniprogram_version=3.14.2&fee={$money}&remark={$trade_no}&remark_pic_urls=&option_list=%5B%5D&account_id={$account_id}&account_type=2&sid={$sid}";
    $Referer = "https://servicewechat.com/wx264e9b6d4d484f51/173/page-frame.html";
    $sResult = get_curl($Url,0,$Referer);
    $json = json_decode($sResult,true);
    $receipt_id = $json['data']['receipt']['receipt_id'];
    if($json['errcode']==268446167){
        sysmsg($json['msg']);
    }elseif($json['errcode']==0){
        $Url = "https://payapp.wechatpay.cn/receiptwxmgr/receipt/getwxacode?miniprogram_version=3.14.2&wxacode_path_type=1&receipt_id={$receipt_id}&account_id={$account_id}&account_type=2&sid={$sid}";
        $sResult = get_curl($Url,0,$Referer);
        $json = json_decode($sResult,true);
        if($json['errcode']==0){
                $file = SYSTEM_ROOT."cache/qrcode/";
                if(!file_exists($file))mkdir($file);
                $r = file_put_contents($file.$trade_no.".png", base64_decode($json['data']['qrcode']));
                return ['code'=>200,'trade_no'=>$receipt_id];
        }else{
            sysmsg('获取二维码失败');
        }
    }else{
        sysmsg('获取receipt_id失败');
    }
}


/*---------------------收款单商家版--------------------------*/

function Get_sid($cookie){//获取收款单SID
    $code = $this->WXJSLogin($cookie,'wx264e9b6d4d484f51');
    $Url = "https://payapp.wechatpay.cn/receiptwxmgr/account/list?miniprogram_version=3.14.1&js_code={$code}";
    $sid = json_decode(get_curl($Url),true)['sid'];
    return $sid;
}

function Get_Sid_Cron($cookie,$sid){//监控sid是否失效失效更新
    $Url = "https://payapp.wechatpay.cn/receiptwxmgr/account/list?miniprogram_version=3.14.1&sid={$sid}";
    $Referer = "https://servicewechat.com/wx264e9b6d4d484f51/173/page-frame.html";
    $sResult = get_curl($Url,0,$Referer);
    $json = json_decode($sResult,true);
    if($json['errcode']==0){
        return ['code'=>200];
    }else{
        return ['code'=>100,'sid'=>$this->Get_sid($cookie)];
    }
}

function Get_account_id($cookie){//获取全部商户
    $sid = $this->Get_sid($cookie);
    $Url = "https://payapp.wechatpay.cn/receiptwxmgr/account/list?miniprogram_version=3.14.1&sid={$sid}";
    $Referer = "https://servicewechat.com/wx264e9b6d4d484f51/173/page-frame.html";
    $sResult = get_curl($Url,0,$Referer);
    $json = json_decode($sResult,true);
    return $json;
}

function Get_shop_id($row,$money,$trade_no){//获取shop_id
    $cookie = $row['cookie'];
    $row = json_decode($row['json'],true);
    $account_id = $row['account_id'];
    if(!$row['sid']){
        $sid = $this->Get_sid($cookie);
    }else{
        $sid = $row['sid'];
    }
    $money=$money*100;
    $Url = "https://payapp.wechatpay.cn/receiptmdmgr/account/get?miniprogram_version=3.14.1&account_id={$account_id}&account_type=1&sid={$sid}";
    $Referer = "https://servicewechat.com/wx264e9b6d4d484f51/173/page-frame.html";
    $sResult = get_curl($Url,0,$Referer);
    $json = json_decode($sResult,true);
    if($json['errcode']==0){
        if(count($json['data']['auth_shop_list'])==1){
            $Url = "https://payapp.wechatpay.cn/receiptmdmgr/receipt/create?miniprogram_version=3.14.1&fee={$money}&remark={$trade_no}&remark_pic_urls=&option_list=%5B%5D&shop_id=".$json['data']['auth_shop_list'][0]['shop_id']."&account_id={$account_id}&account_type=1&sid={$sid}";
            $sResult = get_curl($Url,0,$Referer);
            $json = json_decode($sResult,true);
            if($json['errcode']==0){
                $Url = "https://payapp.wechatpay.cn/receiptmdmgr/receipt/getwxacode?miniprogram_version=3.14.1&wxacode_path_type=1&receipt_id=".$json['data']['receipt']['receipt_id']."&account_id={$account_id}&account_type=1&sid={$sid}";
                $receipt_id = $json['data']['receipt']['receipt_id'];
                $sResult = get_curl($Url,0,$Referer);
                $json = json_decode($sResult,true);
                if($json['errcode']==0){
                    $file = SYSTEM_ROOT."cache/qrcode/";
                    if(!file_exists($file))mkdir($file);
                    $r = file_put_contents($file.$trade_no.".png", base64_decode($json['data']['qrcode']));
                    return ['code'=>200,'trade_no'=>$receipt_id];
                }
            }
        }else {
            return ['code'=>200,'msg'=>'获取成功','data'=>$json['data']['auth_shop_list']];
        }
    }else{
        return ['code'=>-1,'msg'=>'获取shop_id失败'];
    }
}

function Order_Cron($row,$receipt_id){//监控订单是否支付
    
    $qr = json_decode($row['json'],true);
    $sid = $qr['sid'];
    $account_id = $qr['account_id'];

    if($row['channel']=='yd_wx_sskd'){
        $Url = "https://payapp.wechatpay.cn/receiptmdmgr/receipt/detailv3?miniprogram_version=3.14.1&receipt_id={$receipt_id}&page_index=1&page_size=10&account_id={$account_id}&account_type=1&sid={$sid}";
    }else{
        $Url = "https://payapp.wechatpay.cn/receiptwxmgr/receipt/detail?miniprogram_version=3.14.2&receipt_id={$receipt_id}&page_index=1&page_size=10&account_id={$account_id}&account_type=2&sid={$sid}";
    }
    $Referer = "https://servicewechat.com/wx264e9b6d4d484f51/173/page-frame.html";
    $sResult = get_curl($Url,0,$Referer);
    $json = json_decode($sResult,true);
    if($json['data']['receipt']['order'][0]['state']=='STATE_PAY_SUCCESS'){
        $money = $json['data']['receipt']['order'][0]['fee']/100;
        return ['code'=>200,'money'=>$money];
    }else{
        return ['code'=>100];
    }
}


/*---------------------微信免输入云端协议---------------------*/

function wxGetLoginQrcode()//获取登录二维码
{
    $url=$this->Api_Url."api/Client/WXCreate";
    $data = array('Cloudkey'=>'SHDX4DMY','IsDaili'=>0,'Address'=>'','Port'=>'','UserName'=>'','Password'=>'');
    $res=$this->get_curl($url,$data);
    $qrcode=$this->Api_Url."api/Login/WXGetLoginQrcode";
    $guid1=$res['msg'];
    $guid = array('Guid'=>$guid1,'Cloudkey'=>'SHDX4DMY','Moshi'=>1);
    $resq=$this->get_curl($qrcode,$guid);
    if($resq['data']['qrcode']){
       $result=array("code"=>1,"msg"=>"获取成功","uuid"=>$resq['data']['uuid'],"guid"=>"$guid1","qr_url"=>'data:image/png;base64,'.$resq['data']['qrcode']);
    }else{
       $result=array("code"=>-1,"msg"=>"获取成功");
    }
    return $result;
}

function wxCheckLoginQrcode($guid,$uuid)//验证登录
{
	
    $WXCheck=$this->Api_Url."api/Login/WXCheckLoginQrcode";
    $post="{\"Cloudkey\":\"SHDX4DMY\",\"Guid\":\"$guid\",\"Uuid\":\"$uuid\"}";
    $res=json_decode($this->http_post_yun($WXCheck,$post),true);
    if($res['data']['state']==0){
        $result=array("code"=>0,"msg"=>"等待扫码中","state"=>$res['data']['state']);
    }elseif($res['data']['state']==1){
        $result=array("code"=>2,"msg"=>"等待确认中","state"=>$res['data']['state']);
    }elseif($res['data']['state']==4){
        $result=array("code"=>-1,"msg"=>"系统错误,请联系管理员","state"=>$res['data']['state']);
    }elseif($res['data']['state']==2){
        $WXLogin=$this->Api_Url."api/Login/WXLoginManual";
        $pass=$res['data']['wxnewpass'];
        $wxid=$res['data']['wxid'];
        $post="{\"Cloudkey\":\"SHDX4DMY\",\"Guid\":\"$guid\",\"Channel\":1,\"UserName\":\"$wxid\",\"Password\":\"$pass\"}";
        $res2=json_decode($this->http_post_yun($WXLogin,$post),true);
        $result=array("code"=>1,"msg"=>"登录成功","guid"=>$guid);
    }
    return $result;
}

function WXTransferSetF2FFee($guid,$Fee,$Description)//取收款固定码
{
    $WXTransferSetF2FFee=$this->Api_Url."api/Cloud/WXTransferSet";
    $Fee=$Fee*100;
    $post = '{
    "Guid": "'.$guid.'",
    "Cloudkey": "SHDX4DMY",
    "Fee": '.$Fee.',
    "Description": "'.$Description.'"
    }';
    $res=json_decode($this->http_post_yun($WXTransferSetF2FFee,$post),true);
    $arrs=$this->stripslashes_deep($res['data']['reqText']['buffer']);
    return json_decode($arrs,true)['pay_url'];
}

function WXHeartBeat($guid)//账户心跳检测
{ 
    $WXHeartBeat=$this->Api_Url."api/Client/WXKaleBeat";
    $post="{\"Guid\":\"$guid\"}";
    $res=json_decode($this->http_post_yun($WXHeartBeat,$post),true);
    if($res['code']==1){
        $result=array("code"=>1,"msg"=>"心跳正常");
    }else{
        $result=array("code"=>-1,"msg"=>"账户已经离线");
    }
    return $result;
}

function Wx_Get_Money($guid)
{
    $wxapi = $this->Api_Url.'api/Cloud/SuccessOrder';
    $post = array('Cloudkey'=>'SHDX4DMY','Guid'=>$guid,'Moshi'=>'wxpay_cloud');
    $resq= $this->get_curl($wxapi,$post);
    return $resq;
}


protected function Yun_Get($url,$post=null)
{
    //$url = explode(":",$url)[1];
    $curl = curl_init();
    curl_setopt_array($curl, [
        
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_HTTPHEADER => [
            "content-type: application/json-patch+json"
        ],
    ]);
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}

protected function get_curl($url, $post = 0, $cookie = 0, $header = 0, $nobaody = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $klsf[] = 'accept: text/plain"';
        $klsf[] = 'Content-Type: application/json-patch+json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $klsf);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        }
        if ($header) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        if ($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if ($nobaody) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        curl_setopt($ch, CURLOPT_TIMEOUT,60);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return json_decode($ret,true);
    }
protected function stripslashes_deep ( $value ){
$value = is_array ( $value ) ?
array_map ( 'stripslashes_deep' , $value ) :
stripslashes ( $value );
return $value ;
}
protected function http_post_yun($url,  $data){
    $Header =  array('Content-Type: application/json-patch+json');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $Header);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($ch, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $sResult = curl_exec($ch);
    if($sError=curl_error($ch)){
        die($sError);
    }
    curl_close($ch);
    return $sResult;
}
}

class wxuos
{
    public function __construct($url_api){
        global $authcode;
        $count = substr_count($url_api,"/");
        if($count==2){
            $this->url_api = $url_api.'/';
        }else{
            $this->url_api = $url_api;
        }
        $Url = $_SERVER['HTTP_HOST'];
        if(strstr($Url,':443') or strstr($Url,':80')){
            $Url = trim($Url,":443");
            $Url = trim($Url,":80");
        }
        $this->url=$Url;
        $this->authcode=$authcode;
    }
    
    //获取二维码
    public function get_qrcode($guid=null){
        return json_decode($this->___curl($this->url_api.'?act=qrcode','guid='.$guid),true);
    }
    
    //登录
    public function get_login($guid,$uuid){
        $data=['guid'=>$guid,'uuid'=>$uuid,'system'=>'mym','url'=>$this->url,'authcode'=>$this->authcode];
        $data=$this->data_sign(json_encode($data));
        return json_decode($this->___curl($this->url_api.'?act=login','data='.urlencode($data)),true);
    }
    
    //快捷登录
    public function get_login_push($guid){
        $data=['guid'=>$guid,'system'=>'mym','url'=>$this->url,'authcode'=>$this->authcode];
        $data=$this->data_sign(json_encode($data));
        return $this->___curl($this->url_api.'?act=login_','data='.urlencode($data));
    }
    
    //发送快捷登录弹窗
    public function get_push($guid){
        return $this->___curl($this->url_api.'?act=push','guid='.$guid);
    }
    
    //心跳获取
    public function get_heart($guid){
        return $this->___curl($this->url_api.'?act=heart','guid='.$guid);
    }
    
    //获取消息
    public function get_msg($guid){
        return json_decode($this->___curl($this->url_api.'?act=msg','guid='.$guid),true);
    }
    static function ___curl($url,$post=0,$referer=0,$cookie=0,$header=0){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $httpheader[] = "Accept: */*";
        $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
        $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
        $httpheader[] = "Connection: keep-alive";
        $httpheader[] = "author: LeavePay&&MymPay";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        if($post){
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        if($header){
            curl_setopt($ch, CURLOPT_HEADER, TRUE);
        }
        if($cookie){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if($referer){
            curl_setopt($ch, CURLOPT_REFERER, $referer);
        }
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $ret = curl_exec($ch);
        curl_close($ch);
        return $ret;
    }
    static function data_sign($string, $operation = 'ENCODE', $key = 'MymPay&LeavePay_iujorjninejghjenvejfwgjkow', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}