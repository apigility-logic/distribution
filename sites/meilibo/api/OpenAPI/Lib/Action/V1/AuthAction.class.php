<?php

/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/5/15
 * Time: 下午10:27.
 */
class AuthAction extends BaseAction
{
    /**
     * @var array 目前支持的第三方登录类型.
     */
    protected $supported_type = array('qq', 'wechat', 'sina', 'facebook','twitter','instagram');

    /**
     * 文档.
     *
     * @var array
     */
    protected $default_msg = array(
        'category' => 'third_party_login_api',
        'ref' => '第三方登录接口',
        'links' => array(
            'login_api' => array(
                'href' => 'v1/auth/login',
                'ref' => '用户登录',
                'method' => 'POST',
                'parameters' => array('openid' => '第三方登录的OpenID, required', 'type' => '第三方登录平台,目前支持(sina/qq/wechat), required', 'payload' => '第三方登录之后返回的回调用户信息, required'),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        //require_once APP_PATH.'../config.inc.php';
        // require_once APP_PATH.'../uc_client/client.php';
    }

	/**
     * 第三方登录接口.
     *
     * @param $openid  The openid.
     * @param $type    平台类型,比如QQ/Sina/Wechat.
     * @param $payload 平台回调数据.
     */
	 //
    public function login($openid, $type, $payload)
    {
        $type = strtolower($type);
        if (!in_array($type, $this->supported_type)) {
            $this->responseError(L('_UNSUPPORTED_TYPE_'));
        }

        $payload = json_decode(stripcslashes($payload), true);

        if (empty($payload)) {
            $this->responseError(L('_NOT_JSON_'));
        }
        $ua = new UserAction();
        //验证账号状态是否可以登录
        $ua->accountStatus($type,$openid);
        switch ($type) {
            case 'qq':
                $user = $this->QQ($openid, $payload);
                break;
            case 'sina':
                $user = $this->Sina($openid, $payload);
                break;
            case 'wechat':
                $user = $this->Wechat($openid, $payload);
                break;
            case 'facebook':
                $user = $this->Facebook($openid, $payload);
                break;
            case 'twitter':
                $user = $this->Twitter($openid, $payload);
                break;
            case 'instagram':
                $user = $this->Instagram($openid,$payload);
                break;

            default:
                $this->responseError(L('_UNSUPPORTED_TYPE_'));
        }
        $ua->login($user);
    }

    private function Instagram($openid,$data) {
        $user_info = D('Member')->where(array('is_open_id' => $openid))->find();
        if (!empty($user_info)) {
            return $user_info;
        }
        $sex = 1;
        $new_user = $this->registerFromThird($openid, 'is_'.$data['data']['username'], $sex, null);
        $new_user['tw_open_id'] = $openid;
        $new_user['terminal'] = $data['data']['terminal'];

        $uid = D('Member')->add($new_user);

        $new_user['id'] = $uid;
        //$jmessage = new JmessageAction();
        //$jmessage->jmRegist($new_user);

        //拼装新的UserName，将他改成唯一的UserName
        $newUsername = "mei_".$uid;
        M()->execute("update ss_member set username = '".$newUsername."' where id = $uid");
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');
        setRegistRoom($uid, $new_user['curroomnum']);

        return D('Member')->where(array('id' => $uid))->find();
    }

    private function Twitter($openid,$data) {
        $user_info = D('Member')->where(array('tw_open_id' => $openid))->find();
        if (!empty($user_info)) {
            return $user_info;
        }
        // http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html#.E7.AC.AC.E5.9B.9B.E6.AD.A5.EF.BC.9A.E6.8B.89.E5.8F.96.E7.94.A8.E6.88.B7.E4.BF.A1.E6.81.AF.28.E9.9C.80scope.E4.B8.BA_snsapi_userinfo.29
        // 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知,我们系统0是男性,1是女性
        // 所以只要不是女性,就默认是男性
        $sex = ($data['gender'] == 'male') ? 0 : 1;
        $new_user = $this->registerFromThird($openid, 'tw_'.$data['name'], $sex, null);
        $new_user['tw_open_id'] = $openid;
        $new_user['terminal'] = $data['terminal'];

        $uid = D('Member')->add($new_user);

        $new_user['id'] = $uid;
        //$jmessage = new JmessageAction();
        //$jmessage->jmRegist($new_user);

        //拼装新的UserName，将他改成唯一的UserName
        $newUsername = "mei_".$uid;
        M()->execute("update ss_member set username = '".$newUsername."' where id = $uid");
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');
        setRegistRoom($uid, $new_user['curroomnum']);

        return D('Member')->where(array('id' => $uid))->find();
    }

    private function Facebook($openid,$data){
        $user_info = D('Member')->where(array('fb_open_id' => $openid))->find();

        if (!empty($user_info)) {
            return $user_info;
        }
        // http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html#.E7.AC.AC.E5.9B.9B.E6.AD.A5.EF.BC.9A.E6.8B.89.E5.8F.96.E7.94.A8.E6.88.B7.E4.BF.A1.E6.81.AF.28.E9.9C.80scope.E4.B8.BA_snsapi_userinfo.29
        // 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知,我们系统0是男性,1是女性
        // 所以只要不是女性,就默认是男性
        $sex = ($data['gender'] == 'male') ? 0 : 1;
        $new_user = $this->registerFromThird($openid, 'fb_'.$data['name'], $sex, null);
        $new_user['fb_open_id'] = $openid;
        $new_user['terminal'] = $data['terminal'];

        $uid = D('Member')->add($new_user);

        $new_user['id'] = $uid;
        //$jmessage = new JmessageAction();
        //$jmessage->jmRegist($new_user);

        //拼装新的UserName，将他改成唯一的UserName
        $newUsername = "mei_".$uid;
        M()->execute("update ss_member set username = '".$newUsername."' where id = $uid");
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');
        setRegistRoom($uid, $new_user['curroomnum']);

        return D('Member')->where(array('id' => $uid))->find();
    }
	
	
    /**
     * 利用第三方平台返回的用户信息在ucenter中注册一个用户.并且返回该用户信息.
     *
     * @param string $nickname 昵称.
     * @param string $username 用户名.
     * @param int    $sex      性别.
     * @param mixed  $ext      额外信息,比如生日,签名等.(TODO:暂时不支持.).
     *
     * @return array 新的用户信息.
     */
    private function registerFromThird($openid, $nickname, $sex = 0, $ext = null)
    {
        $pwd = md5('7h1stH4VeRy5eCr4tKey!!Random').rand(0, 9);
        $room_num = rand(100, 10000).time();
        $new_user = array(
            'nickname' => $nickname,
            'username' => $openid, 
            'password' => md5($pwd),
            'password2' => $this->pswencode($pwd),
            'email' => '',
            'regtime' => time(),
            'curroomnum' => $room_num,
            'sex' => $sex,
        );

        if (isset($ext)) {
            $new_user['intro'] = isset($ext['intro']) ? htmlspecialchars($ext['intro']) : '';
        }
        return $new_user;
    }

    
    /**
     * QQ登录回调接口.
     *
     * 如果QQ验证通过,试图从系统中查询用户信息,不存在则注册一个,然后返回这个用户信息.
     *
     * @param $openid    回调的openid.
     * @param $data 第三方回调数据.
     *
     * @return mixed 本系统中的用户信息.
     */
    private function QQ($openid, $data)
    {
        $user_info = D('Member')->where(array('qopenid' => $openid))->find();
        if (!empty($user_info)) {
            return $user_info;
        }
        $sex = $data['gender'] == '男' ? '0' : '1';
        $new_user = $this->registerFromThird($openid, 'qq_'.$data['nickname'], $sex);
        $new_user['qopenid'] = $openid;
        $new_user['birthday'] = time();
        $new_user['terminal'] = $data['terminal'];

        $uid = D('Member')->add($new_user);

        $new_user['id'] = $uid;
        //$jmessage = new JmessageAction();
        //$jmessage->jmRegist($new_user);

		//拼装新的UserName，将他改成唯一的UserName
		$newUsername = "mei_".$uid;
		M()->execute("update ss_member set username = '".$newUsername."' where id = $uid");
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');
        setRegistRoom($uid, $new_user['curroomnum']);
        return D('Member')->where(array('id' => $uid))->find();
    }

    /**
     * 新浪登录回调接口.
     *
     * 如果微博验证通过,试图从系统中查询用户信息,不存在则注册一个,然后返回这个用户信息.
     *
     * @param $openid    回调的openid.
     * @param $data 第三方回调数据.
     *
     * @return mixed 本系统中的用户信息.
     */
    private function Sina($openid, $data)
    {
        $user_info = D('Member')->where(array('sinaid' => $openid))->find();
        if (!empty($user_info)) {
            return $user_info;
        }
        $sex = $data['gender'] == 'm' ? 0 : 1;
        $ext = array('intro' => $data['description']);
        $new_user = $this->registerFromThird($openid, 'sina_'.$data['name'], $sex, $ext);
        $new_user['sinaid'] = $openid; // sina ID
        $new_user['birthday'] = time();
        $new_user['terminal'] = $data['terminal'];

        $uid = D('Member')->add($new_user);

        $new_user['id'] = $uid;
        //$jmessage = new JmessageAction();
        //$jmessage->jmRegist($new_user);
		//拼装新的UserName，将他改成唯一的UserName
		$newUsername = "mei_".$uid;
		M()->execute("update ss_member set username = '".$newUsername."' where id = $uid");
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');
        setRegistRoom($uid, $new_user['curroomnum']);

        return D('Member')->where(array('id' => $uid))->find();
    }

    /**
     * 微信登录回调接口.
     *
     * 如果微信验证通过,试图从系统中查询用户信息,不存在则注册一个,然后返回这个用户信息.
     *
     * @param $openid    回调的openid.
     * @param $unionid    回调的unionid
     * @param $data 第三方回调数据.
     *
     * @return mixed 本系统中的用户信息.
     */
       private function Wechat($openid, $data)
    {
        //$this->responseSuccess($data['headimgurl']);
        // $data = json_decode($data, true);
        $user_info = D('Member')->where(array('wxunionid' => $data['unionid']))->find();
        if (!empty($user_info)) {
            //if( empty($user_info['wxopenid']) || empty($user_info['weixinid']) ) {
                //$user_info['wxopenid'] = $openid; // sina ID
                $user_info['weixinid'] = $openid; // sina ID
                D('Member')->save($user_info);
            //}
            return $user_info;
        }
        
        $user_info = D('Member')->where(array('weixinid' => $openid))->find();
        if (!empty($user_info)) {
            if (isset($data['web'])){
                $user_info['wxopenid'] = $openid; // 公衆號
            }
            //if( empty($user_info['wxopenid']) || empty($user_info['weixinid']) ) {
            $user_info['weixinid'] = $openid; // 開放平臺
            D('Member')->save($user_info);
            //}
            return $user_info;
        }
        // http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html#.E7.AC.AC.E5.9B.9B.E6.AD.A5.EF.BC.9A.E6.8B.89.E5.8F.96.E7.94.A8.E6.88.B7.E4.BF.A1.E6.81.AF.28.E9.9C.80scope.E4.B8.BA_snsapi_userinfo.29
        // 用户的性别，值为1时是男性，值为2时是女性，值为0时是未知,我们系统0是男性,1是女性
        // 所以只要不是女性,就默认是男性
        $sex = $data['sex'] != 1 ? 0 : 1;
        $new_user = $this->registerFromThird($openid, 'wechat_'.$data['nickname'], $sex, null);
        
        $new_user['weixinid'] = $openid; // sina ID
        $new_user['wxopenid'] = $openid;
        $new_user['wxunionid'] = $data['unionid']; // sina ID
        $new_user['birthday'] = time();
        $new_user['terminal'] = $data['terminal'];

        $uid = D('Member')->add($new_user);

        $new_user['id'] = $uid;
        //$jmessage = new JmessageAction();
        //$jmessage->jmRegist($new_user);
        //是否有头像 没有就保存到服务器
        $savePath  = __ROOT__.'/style/avatar/'.substr(md5($uid),0,3);
        $image = $this->getimg(substr($data['headimgurl'],0,-1).'132'); 
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }
        file_put_contents($savePath.'/'.$uid.'_middle.jpg',$image);
        file_put_contents($savePath.'/'.$uid.'_big.jpg',$image);
        file_put_contents($savePath.'/'.$uid.'_small.jpg',$image);
        //拼装新的UserName，将他改成唯一的UserName
        $newUsername = "mei_".$uid;
        M()->execute("update ss_member set username = '".$newUsername."' , avatartime = '".time()."' where id = $uid");
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');
        setRegistRoom($uid, $new_user['curroomnum']);


        
        return D('Member')->where(array('id' => $uid))->find();
    }
    /**
     *  绑定微信
     * @param $openid    回调的openid.
     * @param $unionid    回调的unionid
     * @param $token 通行证.
     */
    public function bindWeixin($token = null,$openid = null, $unionid = null){
        if (!$this->isPost() && !APP_DEBUG) {
            $this->responseError(L('_MUST_BE_POST_'));
        }
        if(empty($openid) || empty($unionid)){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $user_info = TokenHelper::getInstance()->get($token);
        $data['wxunionid'] = $unionid;
        if(  count(M('member')->where($data)->select() ) > 0 ){
            $this->responseError(L('_BIND_WEIXIN_REPEAT_'));
        }
        $data['weixinid'] = $openid;
        $data['id'] = $user_info['uid'];
        if ( M('member')->save($data) >= 0){
            $this->responseSuccess(L('_BIND_WEIXIN_SUCCESS_'));
        };
            $this->responseError(L('_BIND_WEIXIN_FAILED_'));
    }
    /**
     *  扫码微信登录回调
     */
    public function logincallback(){
        $app_id = C("WEIXIN_OPEN.APPID");
        $app_secret = C("WEIXIN_OPEN.APPSECRET");
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$app_id.'&secret='.$app_secret.'&code='.$_GET['code'].'&grant_type=authorization_code';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $arr=json_decode($json,1);
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $json =  curl_exec($ch);
        curl_close($ch);
        $jsondata = $json;
        $jsondata = json_decode($jsondata);
        $login_data = array(
            "openid" => $jsondata->openid,
            "type" => "wechat",
            "payload" => $json
        );
        //验证该用户是否登录过，没有就创建新用户
        $login_url = C('WEB_URL')."/OpenAPI/V1/Auth/login";
        $userinfo = $this->curlRequest($login_url, true, $login_data);
        $info=json_decode($userinfo,1);  

        if (!empty($info)) {
            $_SESSION['uid']      = $info['data']['id'];
            $_SESSION['token']    = $info['data']['token'];
            $_SESSION['username'] = $info['data']["nickname"];
            $_SESSION['nickname'] = $info['data']["nickname"];
            $_SESSION['roomnum']  = $info['data']["curroomnum"];
            $_COOKIE['nickname']  = $info['data']['nickname'];
            $_COOKIE['roomnum']   = $info['data']["curroomnum"];
            $_COOKIE['userid']    = $info['data']["id"];
            $_COOKIE['token']     = $info['data']["token"];

        //echo "<script>window.location.href='".$_GET['url']."'</script>";
            if (!empty($_GET['url'])){ 
	    	echo "<script>window.location.href='".C('WEB_URL')."/Show/index/roomnum/".$_GET['url']."'</script>";
            }else{
		echo "<script>window.location.href='".C('WEB_URL')."'</script>";
	    }
	}else{
            echo "<script>window.location.href='".C('WEB_URL')."'</script>";
        }
    }
    /*
    *获取图片函数
    */
    public function getimg($url) {
        $hander = curl_init();
        curl_setopt($hander,CURLOPT_URL,$url);
        curl_setopt($hander,CURLOPT_HEADER,0);
        curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($hander,CURLOPT_RETURNTRANSFER,true);
        //以数据流的方式返回数据,当为false是直接显示出来
        curl_setopt($hander,CURLOPT_TIMEOUT,60);
        $output = curl_exec($hander);
        curl_close($hander);
        return  $output;
    }

}
