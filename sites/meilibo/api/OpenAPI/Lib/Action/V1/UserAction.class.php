<?php

/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/6
 * Time: 下午12:45.
 */

/**
 * 用户相关接口.
 */
class UserAction extends BaseAction
{
    const MAX_NICKNAME_LEN = 8;

    private $user = null;

    private $username_regex = '/^[a-zA-Z0-9_-]{2,18}$/u';
    private $password_regex = '/^[a-zA-Z0-9_\\!@#$%^&*()]{2,40}$/u';

    protected $default_msg = array(
        'category' => 'user_api',
        'ref' => '用户相关API',
        'links' => array(
            'user_login_url' => array(
                'href' => 'v1/user/login',
                'ref' => '用户登录',
                'method' => 'POST',
                'parameters' => array('username' => 'string, required', 'password' => 'string, required'),
            ),
            'user_auto_login_url' => array(
                'href' => 'v1/user/autoLogin',
                'ref' => '用户自动登录',
                'method' => 'POST',
                'parameters' => array('token' => 'string, required', 'username' => 'string, required'),
            ),
            'user_logout_url' => array(
                'href' => 'v1/user/logout',
                'ref' => '退出登录,删除token',
                'method' => 'POST',
                'parameters' => 'token, required',
            ),
            'user_register_url' => array(
                'href' => 'v1/user/register',
                'ref' => '用户注册接口',
                'method' => 'POST',
                'parameters' => array('username' => 'string, required', 'password' => 'string, required', 'device_no' => 'string, optional'),
            ),
            'user_profile_url' => array(
                'href' => 'v1/user/profile',
                'ref' => '用户资料',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'id' => 'integer, optional, default:current user'),
            ),
            'follow_user_url' => array(
                'href' => 'v1/user/follow',
                'ref' => '关注用户',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'id' => 'integer,required'),
            ),
            'unfollow_user_url' => array(
                'href' => 'v1/user/unfollow',
                'ref' => '取消关注用户',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'id' => 'integer,required'),
            ),
            'followers_lst_url' => array(
                'href' => 'v1/user/followers',
                'ref' => '粉丝列表',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'id' => 'integer,optional,default:current user'),
            ),
            'followees_lst_url' => array(
                'href' => 'v1/user/followees',
                'ref' => '关注列表',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required', 'id' => 'integer,optional,default:current user'),
            ),
            'user_edit_profile_url' => array(
                'href' => 'v1/user/edit',
                'ref' => '修改信息',
                'method' => 'POST',
                'parameters' => array(
                    'token' => 'string, required',
                    'profile' => 'string, require, json_format,example:{"nickname":"我是新的昵称!"}',
                    ),
            ),
            'user_upload_avatar' => array(
                'href' => 'v1/user/uploadAvatar',
                'ref' => '上传用户头像',
                'method' => 'POST',
                'parameters' => array(
                    'token' => 'string, required',
                    ),
            ),
            'user_charge_option_url' => array(
                'href' => 'v1/user/getChargeOption',
                'ref' => '充值列表',
                'method' => 'GET',
                'parameters' => null,
            ),
            'user_income_url' => array(
                'href' => 'v1/user/income',
                'ref' => '收益/折算',
                'method' => 'GET',
                'parameters' => array(
                    'token' => 'string, required',
                    ),
            ),
            'user_income_to_cash' => array(
                'href' => 'v1/user/incomeToCash',
                'ref' => '提现',
                'method' => 'POST',
                'parameters' => array(
                    'token' => 'string, required',
                    'num' => 'float, not required, format: %.2f',
                    ),
            ),
            'user_contribute_list_url' => array(
                'href' => 'v1/user/contributeList',
                'ref' => '粉丝贡献列表',
                'method' => 'GET',
                'parameters' => array(
                    'token' => 'string, required',
                    'user_id' => 'interge, required',
                    'page' => 'int, not required max than zero',
                    ),
            ),
            'user_cash_history' => array(
                'href' => 'v1/user/cashHistory',
                'ref' => '提现记录',
                'method' => 'GET',
                'parameters' => array(
                    'token' => 'string, required',
                    ),
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->user = M('Member');
        //require_once APP_PATH.'../config.inc.php';
        // require_once APP_PATH.'../uc_client/client.php';
    }

    /**
     * 登陆接口.
     *
     * @param string $username
     * @param string $password
     * @param array $thirdPartLoginInfo 第三方登录 信息
     */
    public function login($userInfo = null)
    {
        if (!$this->isPost() && !APP_DEBUG) {
            $this->forbidden();
        }
        if(is_array($userInfo)){
            $user_info = $userInfo;
        } else {
            $this->responseError(L('_USER_DOES_NOT_EXIST_'));
        }
        $uid = $user_info['id'];
        // 如果用户之前自动登录过,还有token,删除原来的token.
        $token = TokenHelper::getInstance()->get($uid);
        if (!empty($token)) {
            TokenHelper::getInstance()->delete($token);
        }
        $this->loginSuccessResp($userInfo['username'], $uid);
    }
    /**
     * 登陆接口(web端登陆).
     *
     * @param string $username
     * @param string $password
     * @param array $thirdPartLoginInfo 第三方登录 信息
     */
    public function loginnew($id,$username)
    {
        if(!empty($id)&&!empty($username)){
            $userInfo['id'] = $id;
            $userInfo['username'] = $username;
            $this->login($userInfo);
        }
    }
    /**
     * 登陆接口(用户名登陆).
     *
     * @param string $username
     * @param string $password
     */
    public function loginphone($username,$password)
    {
        if(!empty($username)&&!empty($password)){
            //密码存在字符和汉字
            if(!preg_match("/^[A-Za-z0-9]+$/",$password)){
                $this->responseError(L('_PASS_ERROR_'));
            }
            //昵称过长或过短
            if(mb_strlen($username,"utf-8") > 8 || mb_strlen($username,"utf-8") < 4){
                 $this->responseError(L('_USER_DOES_NOT_EXIST_'));
            }
            $userInfo['username'] = $username;
            $username = M('member')->where('username ='.$userInfo['username'])->find();
            //用户名不存在
            if(empty($username)){
                $this->responseError(L('_USER_NAME_NOT_EXIST_'));
            }
            $userInfo['password'] = md5($password);
            $userinfo = M('member')->where($userInfo)->find();
            //用户名或者密码错误
            !empty($userinfo) ? $this->login($userinfo) : $this->responseError(L('_USER_MAYBE_NOT_EXIST_'));
        }else{
            //用户名或者密码为空
            $this->responseError(L('_U_P_NOT_EMPTY_'));
        }
    }
    /**
    *
    *   游客登陆
    *
    */
    public function touristLogin(){
        $user_info = M('member')->where('id = '.C('TOURIST_ID'))->find();
        $uid = $user_info['id'];

        $token = md5(md5(C('TOURIST_LOGIN_TOKEN')));
        TokenHelper::getInstance()->set($uid, $token);
        // 存入Token数据,API部分后续使用.
        TokenHelper::getInstance()->set($token, array('uid' => $uid, 'username' => $user_info['username']));
        // 方便Wokerman聊天室使用.
        TokenHelper::getInstance()->set(C('SESSION_PREFIX').$uid.$token, $token);

        $this->loginSuccessResp($user_info['username'], $uid, $token);
        
    }


    /**
     * 处理登陆成功之后的回调.
     *
     * @param $username
     * @param $user_id
     * @param null $token
     *
     * @return mixed
     */
    public static function parseLoginSuccessResp($username, $user_id, $token = null)
    {
        if (empty($token)) {
            $token = TokenHelper::getInstance()->generateToken(C('SALT'));
            TokenHelper::getInstance()->set($user_id, $token);
            // 存入Token数据,API部分后续使用.
            TokenHelper::getInstance()->set($token, array('uid' => $user_id, 'username' => $username));
            // 方便Wokerman聊天室使用.
            TokenHelper::getInstance()->set(C('SESSION_PREFIX').$user_id.$token, $token);
        }
        //写入本次登录时间及IP
        D('member')->where(array('id' => $user_id))->data(array('lastlogtime' => time(), 'lastlogip' => get_client_ip()))->save();
        $user_info = M('member')->where(array('id' => $user_id))->getField('id, nickname, coinbalance, sex, ucuid, curroomnum, avatartime , city, birthday, spendcoin, wxunionid, approveid,beanorignal');
        $resp = $user_info[$user_id];
        $where = " earnbean_low <= ".$resp['spendcoin']." and  ".$resp['spendcoin']." <= earnbean_up ";        
        $resp['token'] = $token;
        $resp['username'] = $username;
        $resp['wxunionid'] = empty($resp['wxunionid']) || is_null($resp['wxunionid']) ?  "" : $resp['wxunionid'];

        $level = getRichlevel($resp['spendcoin']);
        $resp['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
        $resp['avatar'] = getAvatar($resp['avatartime'],$user_id,"middle");
        $resp['approveid'] =  $resp['approveid'] == L('_NO_') || empty($resp['approveid']) || is_null($resp['approveid']) ? L('_NO_APPROVE_') : $resp['approveid'];
        $resp['beanorignal'] = empty($resp['beanorignal'])? '0.00':$resp['beanorignal'];
        $resp['sex'] = empty($resp['sex']) ? 0 : 1;
        
        if( $user_id != C('TOURIST_ID') )
            self::addLoginRecord($user_id);
        return $resp;
    }

    protected static function addLoginRecord( $uid ){
        M("loginrecord")->data(array("uid" => $uid, "logintime" => time()))->add();
    }
    /**
     * 登录成功消息.
     *
     * 登录成功之后:生成Token,放入session,写入本次登录时间及IP.
     *
     * @param string $username
     * @param int    $user_id
     * @param string $token
     */
    public function loginSuccessResp($username, $user_id, $token = null)
    {
        $resp = self::parseLoginSuccessResp($username, $user_id, $token);
        // file_put_contents('/tmp/1-'.date('H:i:s').'.log', date('H:i:s').':'.var_export($resp, true));
        $this->responseSuccess($resp);
    }

    /**
    *
    *   @param token string
    *   @param third_party_id string
    *
    */
    public function bindThirdPartyId($token, $third_party_id = 0){
        $userInfo = TokenHelper::getInstance()->get($token);
        $data['id'] = $userInfo['uid'];
        $data['third_party_id'] = $third_party_id;
        if(M('member')->where(" id = ".$data['id'])->getField("third_party_id") == $third_party_id){
            $this->responseError(L('_BIND_WEIXIN_REPEAT_'));
        }
        if($third_party_id != 0 && is_numeric($third_party_id) ){
            if(M('member')->save($data) == false){
                $this->responseError(L('_BIND_WEIXIN_FAILED_'));
            }else{
                $confirm = new ConfirmAction();
                $this->responseSuccess($confirm->CYConfirm($token, $type = 'login') );
                // $this->responseSuccess("绑定成功");
            }
            
        }else{
            $this->responseError(L('_PARAM_ERROR_'));
        }

    }


    /**
     * 自动登录.
     *
     * @param string $token
     * @param string $username
     */
    public function autoLogin($token = null, $username = null)
    {
        $this->verifyToken($token, $username);
        $result = TokenHelper::getInstance()->get($token);
        if (!isset($result['uid']) || !$result['uid']) {
            $this->responseError(L('_USER_DOES_NOT_EXIST_s'));
        }
        $this->loginSuccessResp($username, $result['uid'], $token);
    }

    /**
     * 退出登录,Token设置为过期.
     *
     * @param token
     */
    public function logout($token = null)
    {
        if (!$this->isPost() && !APP_DEBUG) {
            $this->page404();
        }
        $user_info = TokenHelper::getInstance()->get($token);
        TokenHelper::getInstance()->delete($token);
        //通知workerman那边聊天室下线.
        TokenHelper::getInstance()->delete(C('SESSION_PREFIX').$user_info['uid'].$token);
        $this->responseSuccess();
    }
    /**
     * 退出登录,Token设置为过期.
     * 后台设置强制用户退出(禁用用户时)
     * @param uid
     */
    public function adminloginout()
    {
        $uid = $_POST['uid'];
        $token = TokenHelper::getInstance()->get($uid);
        //通知workerman那边聊天室下线.
        if (!empty($token)) {
            TokenHelper::getInstance()->delete($token);
            $this->responseSuccess();
        }else{
            $this->responseError();
        }
    }
    /**
     * 检查uc_user_register函数的返回值.
     *
     * 如果注册失败,提示对应的错误信息.否则什么也不做.
     *
     * @param $uid
     */
    private function checkRegisterResult($uid)
    {
        if ($uid <= 0) {
            if ($uid == -1) {
                $msg = L('_USER_NAME_E_');
            } elseif ($uid == -2) {
                $msg = L('_REGISTER_E_');
            } elseif ($uid == -3) {
                $msg = L('_ALREADY_USERNAME_');
            } elseif ($uid == -4) {
                $msg = L('_EMAIL_E_');
            } elseif ($uid == -5) {
                $msg = L('_EMAIL_BAN_');
            } elseif ($uid == -6) {
                $msg = L('_ALREADY_EMAIL_');
            } else {
                $msg = L('_UNKNOWN_');
            }
            $this->responseError($msg);
        }
    }

    /**
     * 注册.
     *
     * @param mixed $username
     * @param mixed $password
     */
    public function register($username = null, $password = null)
    {
        //TODO:防止恶意注册.
        if (!$this->isPost() && !APP_DEBUG) {
            $this->page404();
        }
        if(!$this->verifyNameAndPwd($username, $password, true)){
            return;
        }
        //密码存在字符和汉字
        if(!preg_match("/^[A-Za-z0-9]+$/",$password)){
            $this->responseError(L('_PASS_ERROR_'));
        }
        //昵称过长或过短
        if(mb_strlen($username,"utf-8") > 8){
            $this->responseError(L('_NAME_LONG_ERROR_'));
        }
        if(mb_strlen($username,"utf-8") < 4){
            $this->responseError(L('_NAME_SHORT_ERROR_')); 
        }
        //判断是否存在相同用户名
        $userinfo = M('member')->where('username ='.$username)->find();
        if(!empty($userinfo)){
            $this->responseError(L('_ALREADY_USERNAME_'));
        }
        do {
            $roomnum = rand(1000000000, 1999999999);
        } while ($this->checkIt($roomnum) == '');
        $new_user_info = array(
            'username' => $username,
            'nickname' => $username,
            'password' => md5($password),
            'password2' => $this->pswencode($password),
            'regtime' => time(),
            'ucuid' => 0,
            'email' => '',
            'isaudit' => 'y',
            'birthday' => time(),
            'province' => L('Mars'),
            'city' => L('Mars'),
            'curroomnum' => $roomnum,
            'host' => M('Server')->where('isdefault="y"')->getField('server_ip'),
        );
        $this->user->create($new_user_info);

        $user_id = $this->user->add();

        // 注册用户到极光
        $jmessage = new JmessageAction();
        $new_user_info['id'] = $user_id;
        $jmessage->jmRegist($new_user_info);
        
        setRegistRoom($user_id, $roomnum);
        // WTF
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$user_id.','.$roomnum.','.time().')');

        //注册之后帮忙登录成功返回token.
        $this->loginSuccessResp($username, $user_id);
    }

    /**
     * 检查用户名和密码.
     *
     * @param string $username
     * @param string $password
     * @param bool   $check_exist
     * @return bool  是否合法
     */
    private function verifyNameAndPwd($username, $password, $check_exist = false)
    {
        $valid = true;
        if (empty($username) || empty($password)) {
            $this->responseError(L('_U_P_NOT_EMPTY_'));
            $valid = false;
        }
        if (!is_string($username) || !is_string($password)) {
            $this->responseError(L('_PARAM_ERROR_'));
            $valid = false;
        }
        if (!preg_match($this->username_regex, $username)) {
            // 开放给第三方API测试.非常危险
        //    $this->responseError('用户名不合法');
        }
        if (!preg_match($this->password_regex, $password)) {
            $this->responseError(L('_PASS_ILLEGAL_'));
            $valid = false;
        }
        // 俩等号太可怕了.比如0e123 == 0e456
        if ($username === $password) {
            //$this->responseError('username,password should be difference');
        }
        if ($check_exist && $this->isExist($username)) {
            $this->responseError(L('_ALREADY_USERNAME_'));
            $valid = false;
        }
        return $valid;
    }

    /**
     * 检查用户名是否存在.
     *
     * @param string $username
     *
     * @return bool
     */
    private function isExist($username)
    {
        $u = $this->user->where(array('username' => $username))->find();

        return is_array($u);
    }

    /**
     * 个人信息.
     *
     * @param  $uid
     * @param $token
     */
    public function profile($uid = null, $token = null)
    {
        if (!$this->isGet() && !APP_DEBUG) {
            $this->page404();
        }
        if ($uid != null && !is_numeric($uid)) {
            $this->responseSuccess('param [uid] should be integer');
        }
        $info = TokenHelper::getInstance()->get($token);
        $caller_uid = $info['uid'];
        if ($uid  == null) {
            // search himself.
            $followee_uid = isset($info['uid']) ? $info['uid'] : -1;
            $uid = $caller_uid;
        } else {
            $followee_uid = $uid;
        }
        //目前需求:
        //昵称nickname/头像/城市city/封面snap/直播人数计算／房间ID/sex性别/intro个人签名/emceelevel等级
        //$Attention = D("Attention"); 粉丝数量//followers
        //$count = $Attention->where("attuid=" . $_SESSION['uid'])->count();
        // $Attention = D("Attention"); 我的偶像followees
        //$count = $Attention->where("uid=".$_SESSION['uid'])->count();
        //coinbalance 金币.
        $result = $this->user->where(array('id' => $uid))->getField('id, sex, broadcasting, intro, ucuid, nickname, city, snap, curroomnum, vip, earnbean,beanbalance,beanorignal,coinbalance,spendcoin,avatartime,birthday,professional,emotion,province, third_party_id,  wxunionid,onlinenum, is_robot,longitude, latitude,approveid ');
        $user_info = isset($result[$uid]) ? $result[$uid] : null;
        if ($user_info === null) {
            $this->responseError('no such user:['.var_export($uid, true).']:'.$token);
        }

        $hit =M("hitlist")->where(array('uid'=>$this->current_uid,'hituid'=>$uid))->find();
        $isHit = count($hit) > 0 ? 1 : 0;
        //在此处请求下直播流，拿到直播流列表
        //30天之前
        $user_info['avatar'] = getAvatar($user_info['avatartime'],$user_info['id'], 'middle');
        $user_info['snap'] =   $user_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($user_info['avatartime'],$user_info['id'], 'yuan');
        $user_info['sex'] = empty($user_info['sex']) ? 0 : 1;
        $user_info['followers_cnt'] = M('Attention')->where(array('attuid' => $uid))->count();
        $user_info['followees_cnt'] = M('Attention')->where(array('uid' => $uid))->count();
        $record = M('attention')->where("uid = {$caller_uid} and attuid= {$followee_uid}")->find();
        $user_info['is_attention'] = empty($record) ? 0 : 1;
        $user_info['isHit'] = $isHit;
        $user_info['total_contribution'] = $user_info['spendcoin'];
        $user_info['anchorBalance'] = !$user_info['beanorignal'] ? 0 : (double)$user_info['beanorignal'];
        //TODO:方法getEmceelevel需要重构. total_contribution需要确定
        $level = getRichlevel($user_info['spendcoin']);
        $user_info['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
        $user_info['age'] =  empty($user_info['birthday']) || is_null($user_info['birthday']) ? 0 : date("Y") - date("Y", $user_info['birthday'] );
        $user_info['approveid'] =  $user_info['approveid'] == L('_NO_') || empty($user_info['approveid']) || is_null($user_info['approveid']) ? L('_NO_APPROVE_') : $user_info['approveid'];  
        if ($user_info['city'] == L('_PLEASE_SELECT_')) {
            $user_info['city'] = L('Mars');
        }
        if ($user_info['province'] == L('_PLEASE_SELECT_')) {
            $user_info['province'] = L('Mars');
        }
        $user_info['earnbean'] =  (double)$user_info['earnbean'];
        $user_info['beanbalance'] = (double)$user_info['beanbalance'];
        $user_info['coinbalance'] = (double)$user_info['coinbalance'];
        $user_info['beanorignal'] = (double)$user_info['beanorignal'];
        // if($user_info['approve'] != 0){
        //     $approveInfo = M('usersort')->where(array('id'=>$user_info['approve']))->find();
        //     $user_info['approveName'] = $approveInfo['sortname'];
        // }
        $remove_key = array('spendcoin'); // earnbalance
        foreach ($remove_key as $key) {
            unset($user_info[$key]);
        }

        // 贡献帮前3个
        $contribute = M('Coindetail')->query("select ss_member.id userid,avatartime,sum(coin) as ak from ss_coindetail,ss_member where type='expend' and touid={$followee_uid} and ss_member.id=ss_coindetail.uid group by uid  order by ak desc limit 3");
        foreach ($contribute as $key => $item) {
            $contribute[$key] = getAvatar($contribute[$key]['avatartime'],$contribute[$key]['userid']);
        }
        $user_info['contribute'] = $contribute;

        $nowTime = time();
        $thirtyTime = $nowTime - (24 * 3600 * 30);
        $ua = new QiniuAction();
        $user_info['playBackCount'] = M('backstream')->where('uid = "'.$uid.'"')->field('id')->count();


        $user_info['recommendation'] = $this->getRecommenUserByUid($user_info['id']);


        $this->responseSuccess($user_info);
    }

    /**
     * 上传用户头像.
     *
     * @param string token
     */
    public function _uploadAvatar($token)
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $uid = $userInfo['uid'];
        // 文件上传
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->allowExts = array('jpg', 'png');// 设置附件上传类型
        $upload->saveRule = $uid;  // 文件名称
        $upload->savePath = realpath(APP_PATH.'/../').getUploadPath($uid);// 设置附件上传目录
        $upload->thumbRemoveOrigin = false; //设置生成缩略图后移除原图
        $upload->uploadReplace = true;  // 覆盖同名
        // $upload->thumbPrefix = substr($uid, -2);  // 前缀
        $upload->thumbPrefix = '';  // 前缀
        $upload->thumbSuffix = '_big,_middle,_small'; // 后缀
        $upload->thumbExt = 'jpg';

        //设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = true;
         // 设置引用图片类库包路径
        $upload->imageClassPath = 'ORG.Util.Image';
        //设置缩略图最大宽度
        $upload->thumbMaxWidth = '200,120,48';
        //设置缩略图最大高度
        $upload->thumbMaxHeight = '200,120,48';

        if (!is_dir($upload->savePath)) {
            mkdir($upload->savePath, 0777, true);
        }
        if (!$upload->upload()) {
            // 上传错误提示错误信息
            $this->responseError($upload->getErrorMsg());
        } else {
            // 上传成功
            $info = $upload->getUploadFileInfo();
            // $path = $upload->savePath . $upload->saveRule . '.jpg';
            $path = $upload->savePath . $upload->saveRule . '.' . $info[0]['extension']; // '.jpg';
            $sign = $info[0]['hash'];
            $newPath = $upload->savePath . "{$sign}.jpg";
            // rename filename
            rename($path, $newPath);
            // exec("mv $path $newPath");
            $member = M('Member');
            $member->snap = substr($newPath,strlen(__ROOT__),strlen($newPath)-strlen(__ROOT__));
            $member->where(array('id'=>$userInfo['uid']))->save();
            $this->responseSuccess(getAvatar($token,$uid));
        }
    }


	 public function uploadAvatar($token)
	 {
        $userInfo = TokenHelper::getInstance()->get($token);
        $userid = $userInfo['uid'];
		//导入上传类
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();// 实例化上传类
		//设置上传文件大小
		$upload->maxSize = 3145728;
		//设置上传文件类型
		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');
		//设置附件上传目录
		$upload->saveRule = $userid;  // 文件名称
		$upload->savePath  = realpath(APP_PATH.'/../').getUploadPath($userid); // 设置附件上传（子）目录
		$upload->autoSub = false; //是否生成日期文件夹

		$upload->thumbRemoveOrigin = false; //设置生成缩略图后移除原图
		$upload->uploadReplace =  true;  // 覆盖同名
		$upload->thumbPrefix = '';  // 前缀
		$upload->thumbSuffix = '_big,_middle,_small'; // 后缀
		$upload->thumbExt = 'jpg';

		if (!is_dir($upload->savePath)) {
			mkdir($upload->savePath, 0777, true);
		}
		//设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = true;
		 // 设置引用图片类库包路径
		$upload->imageClassPath = 'ORG.Util.Image';
		//设置缩略图最大宽度
		$upload->thumbMaxWidth = '300,120,48';
		//设置缩略图最大高度
		$upload->thumbMaxHeight = '300,120,48';
		// 上传文件
		@$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
            $this->responseError($upload->getErrorMsg());
		}else{// 上传成功 获取上传文件信息
			$member = M('Member');
			$avatartime = time();
            $member->avatartime = $avatartime;
            $member->where(array('id'=>$userInfo['uid']))->save();
            $this->responseSuccess(getAvatar($avatartime,$userid));
		}
	}
     public function uploadRoompic($token)
     {
        $userInfo = TokenHelper::getInstance()->get($token);
        $userid = $userInfo['uid'];
        //导入上传类
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        //设置上传文件大小
        $upload->maxSize = 3145728;
        //设置上传文件类型
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        //设置附件上传目录
        $upload->saveRule = $userid;  // 文件名称
        $upload->savePath  = realpath(APP_PATH.'/../').getUploadPicPath($userid); // 设置附件上传（子）目录
        $upload->autoSub = false; //是否生成日期文件夹

        $upload->thumbRemoveOrigin = false; //设置生成缩略图后移除原图
        $upload->uploadReplace =  true;  // 覆盖同名
        $upload->thumbPrefix = '';  // 前缀
        $upload->thumbSuffix = '_big,_middle,_small'; // 后缀
        $upload->thumbExt = 'jpg';

        if (!is_dir($upload->savePath)) {
            mkdir($upload->savePath, 0777, true);
        }
        //设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = true;
         // 设置引用图片类库包路径
        $upload->imageClassPath = 'ORG.Util.Image';
        //设置缩略图最大宽度
        $upload->thumbMaxWidth = '300,120,48';
        //设置缩略图最大高度
        $upload->thumbMaxHeight = '300,120,48';
        // 上传文件
        @$info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->responseError($upload->getErrorMsg());
        }else{// 上传成功 获取上传文件信息
            $member = M('Member');
            $avatartime = time();
            $member->avatarroomtime = $avatartime;
            $member->where(array('id'=>$userInfo['uid']))->save();
            $this->responseSuccess(getRoompic($avatartime,$userid));
        }
    }


    // 大牛上传视频
    public function uploadFile( $token ){

        $userInfo = TokenHelper::getInstance()->get($token);
        $userid = $userInfo['uid'];
        $roomid = M("member")->where(array("id" => $userid ))->getField("curroomnum");

        $siteurl = D("Siteconfig")->where("id=1")->getField("siteurl");

        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        // $upload->maxSize  = -1 ;// 设置附件上传大小 不限大小
        //设置附件上传目录
        $upload->saveRule = $userid."_".time();  // 文件名称
        $upload->savePath  = realpath(APP_PATH.'/../')."/style/userVideo/".substr(md5($uid),0,3).'/'; // 设置附件上传（子）目录
        $upload->autoSub = false; //是否生成日期文件夹
        $upload->uploadReplace =  true;  // 覆盖同名

        if (!is_dir($upload->savePath)) {
            mkdir($upload->savePath, 0777, true);
        }
        if(!$upload->upload()) {// 上传错误提示错误信息
            $this->responseError($upload->getErrorMsg());
        }else{// 上传成功
            $info = $upload->getUploadFileInfo();
            //保存当前数据对象
            $data = array(
                'uid' => $userid,
                'title' => "合流视频",
                'roomid' => $roomid,
                'status' => "0",
                'address' => "$siteurl/style/userVideo/".substr(md5($uid),0,3).'/'.$info[0]['savename'],
                );
            M('videolist')->add($data);
            $this->responseSuccess("上传成功！");
        }
    }


    /**
     * 用户收益/折算.
     *
     * @param string token
     */
    // public function income($token)
    // {
    //     $userInfo = TokenHelper::getInstance()->get($token);
    //     $userinfo = $this->user->where(array('id' => $userInfo['uid']))->getField('id,beanbalance,alipayname');
    //     $userinfo = array_shift($userinfo);
    //     $earnBean = is_null($userinfo['beanbalance']) ? 0 : $userinfo['beanbalance'];
    //     $probability = M('siteconfig')->where('id=1')->getField('emceededuct');
    //     $earnRMB = sprintf('%.2f', $earnBean * ($probability / 100) );
    //     $this->responseSuccess(array(
    //         'earnbean' => $earnBean,
    //         'rmb' => $earnRMB,
    //         'alipayname'=> !$userinfo['alipayname'] ? '': $userinfo['alipayname'],
    //         ));
    // }
    public function income($token)
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $userinfo = $this->user->where(array('id' => $userInfo['uid']))->getField('id,beanbalance,alipayname');
        $userinfo = array_shift($userinfo);
        $earnBean = is_null($userinfo['beanbalance']) ? 0 : $userinfo['beanbalance'];
        $probability = M('siteconfig')->where('id=1')->getField('cash_proportion');
        $earnRMB = sprintf('%.2f', $earnBean * ($probability / 100) );
        $this->responseSuccess(array(
            'earnbean' => $earnBean,
            'rmb' => $earnRMB,
            'alipayname'=> !$userinfo['alipayname'] ? '': $userinfo['alipayname'],
            ));
    }


    /**
     * 提现.
     *
     * @param string token
     * @param float num
     * @param mixed $account 支付宝账号
     */
    public function incomeToCash($token, $num = 0, $account = '')
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $data = $this->user->where(array('id' => $userInfo['uid']))->getField('id,beanbalance,alipayname');
        $beanbalance = $data[$userInfo['uid']]['beanbalance'];
        $alipayname = $data[$userInfo['uid']]['alipayname'];
        if ( !is_numeric($num) || $num == 0) {
            $this->responseError(L('_CASH_NUM_ILLEGAL'));
        }
        if (!$alipayname && !$account) {
            $this->responseError(L('_NOT_ALIPAY_'));
        } else if (strlen( $account) !== 0 && strcmp($alipayname, $account)) {
            if (!$this->user->where(array('id' => $userInfo['uid']))->setField('alipayname',$account)) {
                $this->responseError('_UPDATE_ALIPAY_E_');
            }
        }
        if (!$beanbalance) {
            $this->responseError(L('_NOT_CASH_COIN_'));
        } elseif ($num * 100 > $beanbalance) {
            $this->responseError(L('_CASH_COIN_E_'));
        }

        // $num = !$num ? $beanbalance : $num;
        $cash = sprintf('%.2f', $num);
        $beanbalance = $num * 100;

        $cashHistory = array(
            'uid' => $userInfo['uid'],
            'cash' => $cash,
            'time' => time(),
            );
        $earnCashModel = M('Earncash');
        if ($earnCashModel->create($cashHistory)) {
            if ($earnCashModel->add()) {
                D('Member')->execute("update ss_member set beanbalance=beanbalance-{$beanbalance} where id={$userInfo['uid']}");
                $this->responseSuccess($cashHistory);
            } else {
                $this->responseError(L('_SYSTEM_BUSY_'));
            }
        }
        $this->responseError(L('_SYSTEM_BUSY_'));
    }

    /**
     * 提现记录.
     *
     * @param token
     */
    public function cashHistory($token)
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $history = M('Earncash')->where(array('uid' => $userInfo['uid']))->select();
        if (!$history) {
            $history = array();
        }
        foreach ($history as $key => $item) {
            $history[$key]['time'] = date('Y-m-d h:i:s', $item['time']);
        }
        $this->responseSuccess(array_reverse($history));
    }
    /**
     * 修改个人信息.
     *
     * @param string $token   token.
     * @param string $profile 个人信息.
     */
    public function edit($token, $profile)
    {
        if (!$this->isPost() && !APP_DEBUG) {
            $this->page404();
        }
        $profile = stripslashes($profile);
        $this->verifyProfile($profile);
        $profile = json_decode($profile, true);
        $profile = $this->sanitationProfile($profile);
        $user_info = TokenHelper::getInstance()->get($token);
        $result = $this->user->where(array('id' => $user_info['uid']))->setField($profile);
        if ($result === false) {
            $this->responseError(L('_OPERATION_FAIL_'));
        }
        $this->responseSuccess(L('_OPERATION_SUCCESS_'));
    }

    /**
     * 关注指定的人,copyt的my/interest.
     *
     * @param int $uid
     */
    public function follow($uid = null,$roomid = NULL)
    {
        if (empty($uid) || !is_numeric($uid)) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        if ($this->current_uid == $uid) {
            $this->responseError(L('_NOT_ATTENTION_SELF_'));
        }
        $follow_user = $this->user->where(array('id' => $uid))->find();
        if (empty($follow_user)) {
            $this->responseError(L('_ATTENTION_DOES_NOT_EXIST_'));
        }
        $isFollow = M("attention") -> where(array("uid"=>$this->current_uid,"attuid"=>$uid))->find();
        if(count($isFollow) > 0){
            $this->responseError(L('_ALREADY_ATTENTION_'));
        }
        //如果被对方拉黑，不可关注
        $isHit = M("hitlist")->where(array('uid'=>$uid,"hituid"=>$this->current_uid))->find();
        if(count($isHit) > 0){
            $this->responseError(L('_UNABLE_ATTENTION_'));
        }
        //如果拉黑对方，移除黑名单
        M("hitlist")->where(array("uid"=>$this->current_uid,"hituid"=>$uid))->delete();

        $attention_obj = D('Attention');
        $table = $attention_obj->getTableName();
        //@TODO: thinkphp 支持 insert ignore 么?
        $now = time();
        $result = $attention_obj->execute("INSERT IGNORE INTO {$table} (uid, attuid,addtime) VALUES ({$this->current_uid}, {$uid}, {$now})");
        if ($result > 0) {
            if($roomid != NULL){
                $userInfo = M('member')->where('id = '.$this->current_uid)->field('id,nickname')->find();
                // ??$data
                $tmp = getRichlevel($userInfo['spendcoin']);
                $level_id = $tmp[0]['levelid'];
                $data = array(
                    "type" => "sysmsg",
                    "content" => L('_BROADCAST_MESSAGE_').$userInfo['nickname'].L('_ATTENTION_AHCHOR_')
                );
                import('Common.Gateway', APP_PATH, '.php');
                Gateway::$registerAddress = C('REGISTER_ADDRESS');
                Gateway::sendToGroup($roomid, json_encode($data));
            }
            $this->responseSuccess(L('_ATTENTION_SUCCESS_'));
        } else {
            $this->responseError('_ATTENTION_FAILED_');
        }
    }

    /**
     * 取消关注的人.
     *
     * @param int $uid
     */
    public function unfollow($uid = null)
    {
        if (empty($uid) || !is_numeric($uid)) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        D('Attention')->where('uid='.$this->current_uid.' and attuid='.$uid)->delete();
        $this->responseSuccess(L('_ATTENTION_CANCELED_'));
    }

    /**
     * 关注对应Uid的人列表(Web端我的偶像).如果UID为空,查询关注自己的.
     *
     * @param null|int $uid  可选,如果空默认为当前用户UID.
     * @param int      $page 可选,页码.默认1
     * @param int      $size 可选,页码大小.默认10
     */
    public function followers($uid = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        // uid 作为偶像,所以第二个参数为false.
        $this->getFollowInfo($uid, false, $page, $size);
    }

    /**
     * UID关注的人列表(Web端我关注的人).如果UID为空,查询自己关注的.
     *
     * @param null|int $uid  可选,如果空默认为当前用户UID.
     * @param int      $page 可选,页码.默认1
     * @param int      $size 可选,页码大小.默认10
     */
    public function followees($uid = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        // uid 作为粉丝,所以第二个参数为true.
        $this->getFollowInfo($uid, true, $page, $size);
    }

     /**
     * UID相互关注的列表。
     *
     * @param null|int $uid  可选,如果空默认为当前用户UID.
     * @param int      $page 可选,页码.默认1
     * @param int      $size 可选,页码大小.默认10
     */
    public function closely($uid = null, $page = 1, $size = self::DEFAULT_PAGE_SIZE)
    {
        if (empty($uid)) {
            $uid = $this->current_uid;
        } elseif (!is_numeric($uid)) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $result['page'] = $page;
        $result['size'] = $size;
        $total_cnt = M('attention')->query("SELECT count(*) count FROM ss_attention a join ss_attention b on b.uid = a.attuid WHERE a.uid = ".$uid." and b.attuid = ".$uid);
        $result['total_cnt'] = intval($total_cnt[0]['count']);
        $result['page_cnt'] = ceil($result['total_cnt'] / $result['size']); 
        $result['list'] = M('attention')->query("SELECT a.id, a.uid, a.attuid, a.addtime, c.longitude, c.latitude, c.lastlogtime FROM ss_attention a join ss_attention b on b.uid = a.attuid  inner join ss_member c on c.id = a.attuid WHERE a.uid = ".$uid." and b.attuid = ".$uid." limit ".($page-1)*$size.",".$page*$size);


        // echo ">>".M('attention')->_sql()."<<";
        // dump($result);
        // exit;
        $follow_lst = $result['list'];
        if (empty($follow_lst)) {
            // 数据为空,直接展示,不执行后面的咯.
            $this->responseSuccess($result);
        }
        // 获取所有用户ID,以减少数据库的相关操作.
        // 如果查询条件$is_fans为true,表示自己是作为粉丝,需要查询自己关注的人,查询条件是uid=> $uid,这时候别人的uid就是attuid.
        // 如果为false,查询条件是'attuid' => $uid,表示关注自己的人. 拿么这时候别人的uid就是uid.
        $id_key = 'attuid' ;
        if (function_exists('array_column')) {
            $id_arr = array_column($follow_lst, $id_key);
        } else {
            $id_arr = array_map(function ($el) use ($id_key) {return $el[$id_key];}, $follow_lst);
        }
        $fields = 'id, nickname, spendcoin, lastlogtime, longitude, birthday, latitude,sex,avatartime';
        $followees_detail = M('member')->where(array('id' => array('in', $id_arr)))->getField($fields);
        foreach ($follow_lst as &$followee) {
            unset($followee['id']); // 取消无用数据.
            $user_id = $followee[$id_key];
            $user_info = $followees_detail[$user_id];
            $user_info['sex'] = empty($user_info['sex']) ? 0 : 1;
            //TODO:方法getEmceelevel需要重构. total_contribution需要确定
            $level = getEmceelevel($user_info['spendcoin']);
            $user_info['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
            // if ($is_fans) {
            //     $user_info['is_attention'] = 1;
            // } else {
            //     // 如果自己是偶像,看一下自己关注了对方么?
            //     $record = M('attention')->where("uid = {$uid} and attuid= {$user_id}")->find();
            //     $user_info['is_attention'] = empty($record) ? 0 : 1;
            // }
            $user_info['age'] =  empty($user_info['birthday']) || is_null($user_info['birthday']) ? 0 : date("Y") - date("Y", $user_info['birthday'] );
            $user_info['avatar'] = getAvatar($user_info['avatartime'],$user_info['id'], 'middle');
            unset($user_info['avatartime']);
            unset($user_info['birthday']);
            unset($user_info['spendcoin']);
            $followee = $user_info;
        }
        $result['list'] = $follow_lst;
        $this->responseSuccess($result);
    }

    /**
     * @param $uid 需要查询的UID
     * @param $is_fans uid是否作为粉丝,是则查询条件是$uid关注的人(偶像). 否则查询的关注$uid的人(粉丝,uid作为偶像)
     * @param $page 页码.
     * @param $size 页码大小.
     */
    private function getFollowInfo($uid, $is_fans, $page, $size)
    {
        if (empty($uid)) {
            $uid = $this->current_uid;
        } elseif (!is_numeric($uid)) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        list($page, $size) = $this->parsePageAndSize($page, $size);
        // $is_fans,查询条件是$uid关注的人(偶像):'uid' => $uid
        // 否则就是查询的关注$uid的人(粉丝): 'attuid' => $uid
        $key = $is_fans ? 'uid' : 'attuid';
        $condition = array($key => $uid);
        $parse = null;
        $result = $this->getResultByConditionWithPager('Attention', 'id, uid, attuid, addtime', $condition, $parse, $page, $size, array('order' => 'addtime desc'));
        $follow_lst = $result['list'];
        if (empty($follow_lst)) {
            // 数据为空,直接展示,不执行后面的咯.
            $this->responseSuccess($result);
        }
        // 获取所有用户ID,以减少数据库的相关操作.
        // 如果查询条件$is_fans为true,表示自己是作为粉丝,需要查询自己关注的人,查询条件是uid=> $uid,这时候别人的uid就是attuid.
        // 如果为false,查询条件是'attuid' => $uid,表示关注自己的人. 拿么这时候别人的uid就是uid.
        $id_key = $is_fans ? 'attuid' : 'uid';
        if (function_exists('array_column')) {
            $id_arr = array_column($follow_lst, $id_key);
        } else {
            $id_arr = array_map(function ($el) use ($id_key) {return $el[$id_key];}, $follow_lst);
        }
        $fields = 'id, nickname, spendcoin, lastlogtime, longitude, birthday, latitude,sex,avatartime,curroomnum,broadcasting,onlinenum,city';
        $followees_detail = M('member')->where(array('id' => array('in', $id_arr)))->getField($fields);
        foreach ($follow_lst as &$followee) {
            unset($followee['id']); // 取消无用数据.
            $user_id = $followee[$id_key];
            $user_info = $followees_detail[$user_id];
            $user_info['sex'] = empty($user_info['sex']) ? 0 : 1;
            //TODO:方法getEmceelevel需要重构. total_contribution需要确定
            $level = getEmceelevel($user_info['spendcoin']);
            $user_info['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
            // if ($is_fans) {
            //     $user_info['is_attention'] = 1;
            // } else {
            //     // 如果自己是偶像,看一下自己关注了对方么?
            //     $record = M('attention')->where("uid = {$uid} and attuid= {$user_id}")->find();
            //     $user_info['is_attention'] = empty($record) ? 0 : 1;
            // }
            $user_info['age'] =  empty($user_info['birthday']) || is_null($user_info['birthday']) ? 0 : date("Y") - date("Y", $user_info['birthday'] );
            $user_info['avatar'] = getAvatar($user_info['avatartime'],$user_info['id'], 'middle');
            $user_info['snap'] = $user_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($user_info['avatartime'],$user_info['id'], 'yuan');
            unset($user_info['avatartime']);
            unset($user_info['birthday']);
            unset($user_info['spendcoin']);
            $followee = $user_info;
        }
        $result['list'] = $follow_lst;
        $this->responseSuccess($result);
    }

    /**
     * 验证个人信息.
     *
     * @param string $profile 个人信息.json编码.
     */
    private function verifyProfile($profile)
    {
        $profile = json_decode($profile, true);
        if (empty($profile) || json_last_error() !== JSON_ERROR_NONE) {
            $this->responseError(L('_NOT_JSON_'));
        }
        foreach ($profile as $key => $value) {
            switch ($key) {
                case 'nickname':
                    //see http://www.php.net/manual/en/mbstring.supported-encodings.php
                    if (!is_string($value) || mb_strlen($value, 'UTF-8') > 20) {
                        $this->responseError(L('_NICKNAME_ERROR_'));
                    }
                    break;
                case 'intro':
                    if (!is_string($value) || mb_strlen($value, 'UTF-8') > 40) {
                        $this->responseError(L('_INTRO_ERROR_'));
                    }
                    break;
                case 'city':
                    if (!is_string($value) || mb_strlen($value, 'UTF-8') > 40) {
                        $this->responseError(L('_INTRO_ERROR_'));
                    }
                    break;
                case 'emotion':
                    if (!is_string($value) || mb_strlen($value, 'UTF-8') > 40) {
                        $this->responseError(L('_INTRO_ERROR_'));
                    }
                    break;
                case 'realname':
                    // TODO:
                    break;
                case 'sex':
                    if (!is_string($value) || !preg_match('/^[01]$/u', $value)) {
                        $this->responseError(L('_SIX_ERROR_'));
                    }
                    break;
                case 'birthday':
                    if (!is_string($value) || validateDate($value)) {
                        $this->responseError(L('_BIRTHDAY_ERROR_'));
                    }
                    if (strtotime($value) >= time()) {
                        $this->responseError(L('_BIRTHDAY_ERROR2_'));
                    }
                    break;
                case 'interest':
                    // TODO:
                    break;
                case 'latitude':
                    // TODO:
                    break;
                case 'longitude':
                    // TODO:
                    break;
                default:
                    $this->responseError("unknown field:[{$key}]");
            }
        }
    }

    /**
     * 净化用户输入的个人资料.
     *
     * @param array $profile.
     *
     * @return array 净化之后的用户信息.
     */
    private function sanitationProfile($profile)
    {
        $filter_map = array(
            'nickname' => 'htmlspecialchars',
            'realname' => 'htmlspecialchars',
            'intro' => 'htmlspecialchars',
            'sex' => 'intval',
            'interest' => 'htmlspecialchars',
            'latitude' => 'floatval',
            'longitude' => 'floatval',

        );

        foreach ($filter_map as $field => $func) {
            if (!isset($profile[$field])) {
                continue;
            }
            $profile[$field] = $func($profile[$field]);
        }

        return $profile;
    }

    /**
     * 获取充值选项列表.
     */
    public function getChargeOption($token)
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $coinbalance = $this->user->where(array('id'=>$userInfo['uid']))->getField('coinbalance');
        $list = M("charge")->field("rmb, diamond, present")->select();
        foreach ($list as $key => $value) {
            $list[$key]['msg'] = '送'.$list[$key]['present'].'星钻';
        }

        // $this->responseSuccess($list);
        $this->responseSuccess(array('coinbalance'=>$coinbalance, 'list'=>$list));
    }

    /**
     * 粉丝贡献列表.
     *
     * @param token string
     * @param page int
     */
    public function contributeList($token, $user_id, $page = 1)
    {
        $result = array(
            'total_cnt' => 0,
            'page' => 0,
            'size' => 0,
            'page_cnt' => 0,
            'list' => array(),
            'sum_coin' => 0,
            );
        $count = M('Coindetail')->query("select COUNT(DISTINCT(uid)) as count from ss_coindetail where type='expend' AND touid={$user_id}");
        if (!$count[0]['count']) {
            $this->responseSuccess($result);

            return;
        }
        $sum_coin = 0;
        $count = $count[0]['count'];

        $listRows = 10;
        $page = !$page ? 1 : $page;
        // $page = (int)$page <= ceil($count/$listRows) ? (int)$page : ceil($count/$listRows);
        $firstRows = ($page - 1) * $listRows;
        $sql = "select ss_member.id as userid,ss_member.birthday as birthday, nickname as username,uid,sex,spendcoin,avatartime,SUM(coin) as coin from ss_coindetail,ss_member where type='expend' AND touid=$user_id and uid=ss_member.id GROUP BY uid ORDER BY coin DESC limit $firstRows, $listRows";
        $contribute = M('Coindetail')->query($sql);
        foreach ($contribute as $key => $item) {
            $contribute[$key]['age'] =  empty($user_info['birthday']) || is_null($user_info['birthday']) ? 0 : date("Y") - date("Y", $user_info['birthday'] );
            $contribute[$key]['avatar'] = getAvatar($item['avatartime'],$item['userid']);
            $contribute[$key]['sex'] = $contribute[$key]['sex'] == 1 ? 1 : 0;
            $level = getRichlevel($contribute[$key]['spendcoin']);
            $contribute[$key]['levelid'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
            //$sum_coin += $contribute[$key]['coin'];
            unset($contribute[$key]['ucuid']);
            unset($contribute[$key]['birthday']);
            unset($contribute[$key]['spendcoin']);
        }
        $sum_coin = M("member")->where('id = '.$user_id)->getField("beanorignal");
        // 更具折算规则计算
        // 这是一个坑！可能折算比例会变
        /*
        $agentuid = M('Member')->where(array('id'=>$user_id))->getField('agentuid');
        if ($agentuid != 0) {
            $ratio = D('Agentfamily')->where('uid='.$agentuid)->getField('uid,familyratio,anchorratio');
            $ratio = $ratio[$agentuid];
            $sum_coin = $sum_coin * ($ratio['anchorratio'] / 100);
        } else {
            //默认的比例
            $site = D('Siteconfig')->find();
            $sum_coin = $sum_coin * ($site['emceededuct'] / 100);
        }
        */

        $result = array(
            'total_cnt' => $count,
            'page' => $page,
            'size' => $listRows,
            'page_cnt' => $page,
            'list' => $contribute,
            'sum_coin' => $sum_coin,
            );
        $this->responseSuccess($result);
    }
    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param string
     **/
    public function setAddress($uid = 0,$province = NULL,$address = NULL,$orientation)
    {
        if($address == NULL || $uid == 0 || $province == NULL){
            return false;
        }
        $data['city'] = $address;
        $data['orientation'] = $orientation;
        $data['province'] = $province;
        $data['orientation'] = "v";
        $rs = M("member")->data($data)->where(array('id'=>$uid))->save();
        if($rs){
            return true;
        }else{
            return false;
        }
    }
    public function getHitlist($token = null){
        $hitList =M("hitlist")->where(array('uid'=>$this->current_uid))->select();
        $uids = "";
        foreach($hitList as $hit){
            $uids .= $hit['hituid'] . ",";
        }
        $uids = substr($uids,0,-1);
        $data['id'] = array("in",$uids);
        $userList = M("member")->where($data)->getField("id, nickname, spendcoin, lastlogtime, longitude, birthday, latitude,sex,avatartime");
        $trueUserList = array();
        foreach($userList as $user_info){
            if($user_info != null){
                $user_info['avatar'] = getAvatar($user_info['avatartime'],$user_info['id'], 'middle');
                $user_info['sex'] = empty($user_info['sex']) ? 0 : 1;
                //TODO:方法getEmceelevel需要重构. total_contribution需要确定
                $level = getEmceelevel($user_info['spendcoin']);
                $user_info['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
                $user_info['age'] =  empty($user_info['birthday']) || is_null($user_info['birthday']) ? 0 : date("Y") - date("Y", $user_info['birthday'] );
                unset($user_info['avatartime']);
                unset($user_info['birthday']);
                unset($user_info['spendcoin']);
                array_push($trueUserList, $user_info);
            }
        }
        $this->responseSuccess($trueUserList);
    }

   /**
     * 拉入黑名单.
     *
     * @param uid int
     * @param hituid int
     */
    public function setHit($token = null,$hituid = 0){
        if($hituid == 0 || !is_numeric($hituid)){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $hit = M("hitlist")->where(array('uid'=>$this->current_uid,"hituid"=>$hituid))->find();
        if(count($hit) > 0){
            $this->responseError(L('_USER_PULL_BLACK_'));
        }
        //清除关注信息
        $where['uid'] = $this->current_uid;
        $where['attuid'] = $hituid;
        M('attention')->where($where)->delete();
        //拉黑开始
        $where['hituid'] = $hituid;
        unset($where['attuid']);
        $where['hittime'] = time();

        $action = new JmessageAction();
        $action->addBlack($this->current_uid,$hituid);

        if(M('hitlist')->data($where)->add()){
            $this->responseSuccess(L('_PULL_BLACK_SUCCESS_'));
        }else{
            $this->responseError(L('_PULL_BLACK_FAIlED_'));
        }
    }

    public function removeHit($token = null,$hituid = 0){
        if($hituid == 0 || !is_numeric($hituid)){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $hit = M("hitlist")->where(array('uid'=>$this->current_uid,"hituid"=>$hituid))->delete();

        $action = new JmessageAction();
        $action->removeBlack($this->current_uid,$hituid);

        if(count($hit) >= 0){
            $this->responseSuccess(L('_REMOVE_BLACK_'));
        }else{
            $this->responseError(L('_REMOVE_BLACK_FAIlED_'));
        }
    }

    /**
     * 更改生日接口.
     *
     * @param token string
     * @param birthday date|string
     */
    public function setBirthday($token = null, $birthday){
        $data = array(
            'id' =>  $uid,
            'birthday'  =>  strtotime($birthday)
            );
        if(M('member')->save($data) >= 0){
            $this->responseSuccess(L('_OPERATION_SUCCESS_'));
        }else{
            $this->responseError(L('_OPERATION_FAIL_'));
        }    $uid = $this->current_uid;
        
    }

    /**
     * 更改情感状态接口.   |0保密1单身2热恋3已婚4同性
     *
     * @param token string
     * @param emotion int
     */
    public function setEmotion($token = null, $emotion = 0){
        $uid = $this->current_uid;
        $data = array(
            'id'    =>  $uid,
            'emotion'  =>  $emotion
            );
        if(M('member')->save($data) >= 0){
            $this->responseSuccess(L('_OPERATION_SUCCESS_'));
        }else{
            $this->responseError(L('_OPERATION_FAIL_'));
        }
    }

    /**
     * 更改省份接口.
     *
     * @param token string
     * @param province string
     */
    public function setProvince($token = null, $province = null ,$city = null ){
        $province = $province == null ? L('_MARS_') : $province;
        $city = $city == null ? L('_MARS_') : $city;
        $uid = $this->current_uid;
        $data = array(
            'id'    =>  $uid,
            'province'  =>  $province,
            'city'  =>  $city
            );
        if(M('member')->save($data) >= 0){
            $this->responseSuccess(L('_OPERATION_SUCCESS_'));
        }else{
            $this->responseError(L('_OPERATION_FAIL_'));
        }
    }


    /**
     * 更改职业接口.
     *
     * @param token string
     * @param professional string
     */
    public function setProfessional($token = null, $professional){
        $uid = $this->current_uid;
        $data = array(
            'id'    =>  $uid,
            'professional'  =>  $professional
            );
        if(M('member')->save($data) >= 0){
            $this->responseSuccess(L('_OPERATION_SUCCESS_'));
        }else{
            $this->responseError(L('_OPERATION_FAIL_'));
        }
    }
    public function accountStatus($type,$account){
        $data = array();
        switch ($type) {
            case 'qq':
                $data['qopenid'] = $account;
                break;
            case 'sina':
                $data['sinaid'] = $account;
                break;
            case 'wechat':
                $data['weixinid'] = $account;
                break;
            case 'sms':
                $data['mobile'] = $account;
                break;
            case 'uid':
                $data['id'] = $account;
                break;
            case 'facebook':
                $data['fb_open_id'] = $account;
                break;
            case 'twitter':
                $data['tw_open_id'] = $account;
                break;
            case 'instagram':
                $data['is_open_id'] = $account;
                break;
            default:
                $this->responseError(L('_TYPE_NOT_'));
        }
        $userInfo = M("member")->where($data)->find();
        if(!empty($userInfo) && ($userInfo['isaudit'] == "n" || $userInfo['isdelete'] == "y")) {
            if($userInfo['isaudit'] == "n"){
                $time = time();
                $list = M("banlist")->where(array('uid'=>$userInfo['id'],'banstatus'=>'0','banduration'=>array('gt',$time)))->select();
                if(count($list) < 1){
                    M("member")->save(array("id"=>$userInfo['id'],"isaudit"=>"y"));
                    $bandata['disbantime'] = $time;
                    $bandata['disbanadmin'] = L('_AUTO_UNLOCK_');
                    $bandata['banstatus'] = "1";
                    M("banlist")->data($bandata)->where(array('uid'=>$userInfo['id']))->save();
                    return;
                }
            }
            $this->responseError(L('_FROZEN_'));
        }
    }
    //用户消费记录
    /**
     * @param token string
     */
    public function getCoindetail($token = null){
        $uid = $this->current_uid;
        $coindetail = M('coindetail')->where('uid = '.$uid.' and type="expend" and action = "sendgift"')
        ->field('id, uid, touid, giftid, giftcount, content, objecticon, coin, showid, addtime')
        ->order('addtime desc')->select();
        $this->responseSuccess($coindetail);
    }
    //用户提现记录
    /**
     * @param token string
     */
    public function getEarncash($token = null){
        $uid = $this->current_uid;
        $earncash = M('earncash')->where('uid = '.$uid)
        ->field('id, uid, cash, time, status, checktime ')
        ->order('time desc')->select();
        $this->responseSuccess($earncash);
    }

    public function convertBean($token,$bean = 0){
        if($bean == 0 || $bean < 0){
            $this->responseError(L('_FROZEN_'));
        }
        $uid = $this->current_uid;
        $data['id'] = $uid;
        $userInfo = M("member")->where($data)->field('id,nickname,beanbalance,coinbalance')->find();
        if($bean > $userInfo['beanbalance']){
            $this->responseError(L('_NO_COIN_'));
        }
        //修改余额
        D('Member')->execute('update ss_member set coinbalance=coinbalance+'
            .$bean.',beanbalance=beanbalance-'.$bean.' where id='.$this->current_uid);
        //添加交易记录
        $Beandetail = D('Beandetail');
        $Beandetail->create();
        $Beandetail->type = 'income';
        $Beandetail->action = 'exchange';
        $Beandetail->uid = $this->current_uid;
        $Beandetail->content = $userInfo['nickname'].L('_CONVERTED_ONE_').$bean.L('_CONVERTED_TOW_');
        $Beandetail->bean = $bean;
        $Beandetail->addtime = time();
        $detailId = $Beandetail->add();
        // $this->responseSuccess("成功兑换了 ".$bean." 到虚拟币账户");
        $this->responseSuccess(L('_OPERATION_SUCCESS_'));
    }

    /**
    *
    *   认证信息
    *   @param token string 通行证
    *
    */
     public function getUsersort($token = null){

         $this->responseSuccess(M('usersort')->where('isapprove = "1" and parentid <> 0 ')->field('sortname')->select());

     }


    /**
     * 用户实名认证
     * @param token string
     * @param name string
     * @param phone string
     * @param sid string
     * @param IDCard string
     * @param authorpic file
     * @param beforepic file
     *
     *
     */
     public function approveCheck($token = null, $name = null, $phone = null, $sid = null, $IDCard = null){
        $uid = $this->current_uid;
        $status = M('approve')->where('uid='.$uid.' and (status="0" or status ="1" )')->select();
        if(count($status) > 0){
            $this->responseSuccess(L('_NOT_REPEAT_'));
        }
        if(empty($uid) || empty($name) || empty($phone) || empty($sid) || empty($IDCard)){
            $this->responseError(L('_INFORMATION_FILL_'));
        }
        if(empty($_FILES['authorpic']['name'])){
            $this->responseError(L('_ID_PHOTO_'));
        }
        if(empty($_FILES['beforepic']['name'])){
            $this->responseError(L('_ID_CARD_'));
        }
        $uploadData = $this->_upload();
        if($uploadData["code"] == "1"){
            $authorpic = $uploadData['data'][0]["savename"];
            $beforepic = $uploadData['data'][1]["savename"];
        }else{
            $this->responseError($uploadData['msg']);
        }
        $uptime = time();
        $sql = "insert into ss_approve( uid, name, card, mobile, card_beforepic, card_authorpic, sid, uptime) values ( %s,'%s','%s','%s','%s','%s','%s',%s)";
        $sql = sprintf($sql,$uid,$name,$IDCard,$phone,$beforepic,$authorpic,$sid,$uptime);
        $result = M("")->execute($sql);
        if($result > 0){
            $this->responseSuccess(L('_SUCCESS_'));
        }else{
            $this->responseSuccess(L('_FAILED_'));
        }
     }


     //upload function
     public function _upload()
     {
        //导入上传类
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        //设置上传文件大小
        $upload->maxSize            = 3145728;
        //设置上传文件类型
        $upload->exts          = array('jpg', 'gif', 'png', 'jpeg');
        //设置附件上传目录
        $upload->saveRule =    'uniqid';  // 文件名称
        $upload->savePath  =      '../style/app/images/idcard/'; // 设置附件上传（子）目录
        $upload->autoSub = false; //是否生成日期文件夹

        if (!is_dir($upload->savePath)) {
            mkdir($upload->savePath, 0777, true);
        }
        @$info   =   $upload->upload();
        $returnData = array();
        if(!$info) {// 上传错误提示错误信息
            $returnData["code"] = "0";
            $returnData["msg"] = $upload->getErrorMsg();
        }else{// 上传成功 获取上传文件信息
            $returnData["code"] = "1";
            $data = array();
            $info = $upload -> getUploadFileInfo();
            $returnData['data'] = $info;
        }
        return $returnData;
    }
    /**
    *   获取用户头像
    *   @param token string 通行证
    *   @param uid int  用户id
    */
    public function getHeadUrl($token = NULL, $uid = 0){
        if($uid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }

        $user_info = M('member')->where('id = '.$uid)->field('id, avatartime')->find();

        if(empty($user_info)){
            $this->responseError(L('_USER_DOES_NOT_EXIST_'));
        }
        $avatar = getAvatar($user_info['avatartime'],$user_info['id'], 'middle');
        $this->responseSuccess($avatar);

    }
    /**
    *   获取分享用户信息
    *   @param uid int  用户id
    */
    public function shareProfile( $uid = 0 ){
        if (!$this->isGet() && !APP_DEBUG) {
            $this->page404();
        }
        if ($uid != null && !is_numeric($uid)) {
            $this->responseSuccess('param [uid] should be integer');
        }

        $result = $this->user->where(array('id' => $uid))->getField('id, sex, broadcasting, intro, ucuid, nickname, city, snap, curroomnum, vip, earnbean,beanbalance,beanorignal,coinbalance,spendcoin,avatartime,birthday,professional,emotion,province, third_party_id');
        $user_info = isset($result[$uid]) ? $result[$uid] : null;
        if ($user_info === null) {
            $this->responseError('no such user:['.var_export($uid, true).']:'.$token);
        }

        $hit =M("hitlist")->where(array('uid'=>$this->current_uid,'hituid'=>$uid))->find();
        $isHit = count($hit) > 0 ? 1 : 0;
        //在此处请求下直播流，拿到直播流列表
        //30天之前
        $user_info['avatar'] = getAvatar($user_info['avatartime'],$user_info['id'], 'middle');
        $user_info['snap'] =   $user_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($user_info['avatartime'],$user_info['id'], 'yuan');
        $user_info['sex'] = empty($user_info['sex']) ? 0 : 1;
        $user_info['followers_cnt'] = M('Attention')->where(array('attuid' => $uid))->count();
        $user_info['followees_cnt'] = M('Attention')->where(array('uid' => $uid))->count();
        $record = M('attention')->where("uid = {$caller_uid} and attuid= {$followee_uid}")->find();
        $user_info['is_attention'] = empty($record) ? 0 : 1;
        $user_info['isHit'] = $isHit;
        $user_info['total_contribution'] = $user_info['spendcoin'];
        $user_info['anchorBalance'] = !$user_info['beanorignal'] ? 0 : $user_info['beanorignal'];
        //TODO:方法getEmceelevel需要重构. total_contribution需要确定
        $level = getRichlevel($user_info['spendcoin']);
        $user_info['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
        $user_info['age'] =  empty($user_info['birthday']) || is_null($user_info['birthday']) ? 0 : date("Y") - date("Y", $user_info['birthday'] );
        $user_info['approveid'] =  $user_info['approveid'] == L('_NO_') || empty($user_info['approveid']) || is_null($user_info['approveid']) ? 0 : 1;
        if ($user_info['city'] == L('_PLEASE_SELECT_')) {
            $user_info['city'] = L('Mars');
        }
        if ($user_info['province'] == L('_PLEASE_SELECT_')) {
            $user_info['province'] = L('Mars');
        }
        // if($user_info['approve'] != 0){
        //     $approveInfo = M('usersort')->where(array('id'=>$user_info['approve']))->find();
        //     $user_info['approveName'] = $approveInfo['sortname'];
        // }
        $remove_key = array('spendcoin'); // earnbalance
        foreach ($remove_key as $key) {
            unset($user_info[$key]);
        }

        // 贡献帮前3个
        $contribute = M('Coindetail')->query("select ss_member.id userid,avatartime,sum(coin) as ak from ss_coindetail,ss_member where type='expend' and touid={$followee_uid} and ss_member.id=ss_coindetail.uid group by uid  order by ak desc limit 3");
        foreach ($contribute as $key => $item) {
            $contribute[$key] = getAvatar($contribute[$key]['avatartime'],$contribute[$key]['userid']);
        }
        $user_info['contribute'] = $contribute;

        $nowTime = time();
        $thirtyTime = $nowTime - (24 * 3600 * 30);
        $ua = new QiniuAction();
        $segments = $ua->getSegmentsArray($user_info['curroomnum'],$thirtyTime,$nowTime);
        $user_info['playBackCount'] = count($segments);

        $this->responseSuccess($user_info);
    }

        /**
     * 收到礼物纪录
     * @param string $token
     * @param int $page
     */
    public function recvGiftDetail($token, $page = 1)
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $uid = $userInfo['uid'];
        // $uid = 1697;
        $page = $page <= 1 ? 1 : $page;

        $count = M('Coindetail')->where(array('touid'=>$uid))->count();
        if ($page > ceil($count/10)) {
            $this->responseSuccess(array());
        }
        $start = ($page - 1) * 10;
        $end = $page * 10;
        $data = M('Coindetail')->query("select m.nickname,g.giftname,g.gifticon,d.addtime from ss_coindetail d,ss_gift g, ss_member m where d.touid={$uid} and g.id=d.giftid and g.isred<>'1' and g.enable=1 and d.uid=m.id order by d.addtime desc limit {$start}, {$end}");
        $result = $tmp = array();
        foreach ($data as $item) {
            $tmp['nickname'] = $item['nickname'];
            $tmp['giftname'] = $item['giftname'];
            $tmp['icon'] = $item['gifticon'];
            $tmp['time'] = date('Y-m-d h:i:s', $item['addtime']);
            $result[] = $tmp;
            $tmp = array();
        }
        $this->responseSuccess($result);
    }

    /**
     * 送出礼物纪录
     * @param string $token
     * @param int $page
     */
    public function sendGiftDetail($token, $page = 1)
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $uid = $userInfo['uid'];
        // $uid = 1680;
        $page = $page <= 1 ? 1 : $page;

        $count = M('Coindetail')->where(array('uid'=>$uid))->count();
        if ($page > ceil($count/10)) {
            $this->responseSuccess(array());
        }
        $start = ($page - 1) * 10;
        $end = $page * 10;
        $data = M('Coindetail')->query("select m.nickname,g.giftname,g.gifticon,d.addtime from ss_coindetail d,ss_gift g, ss_member m where d.uid={$uid} and g.id=d.giftid and g.isred<>'1'and g.enable=1 and d.touid=m.id order by d.addtime desc limit {$start}, {$end}");
        $result = $tmp = array();
        foreach ($data as $item) {
            $tmp['nickname'] = $item['nickname'];
            $tmp['giftname'] = $item['giftname'];
            $tmp['icon'] = $item['gifticon'];
            $tmp['time'] = date('Y-m-d h:i:s', $item['addtime']);
            $result[] = $tmp;
            $tmp = array();
        }
        $this->responseSuccess($result);
    }

    /**
     * 充值记录
     * @param string $token
     * @param int $page
     */
    public function chargedetail($token, $page = 1)
    {
        $userInfo = TokenHelper::getInstance()->get($token);
        $uid = $userInfo['uid'];
        // $uid = 1677;
        $page = $page <= 1 ? 1 : $page;


        $count = M('Chargedetail')->where(array('touid'=>$uid))->count();
        if ($page > ceil($count/10)) {
            $this->responseSuccess(array());
        }
        $start = ($page - 1) * 10;
        $end = $page * 10;

        $data = M('Chargedetail')->where(array('touid'=>$uid,'status'=>'1'))->order('addtime desc')->limit($start, $end)->select();
        $result = $tmp = array();
        foreach($data as $item) {
            $tmp['rmb'] = $item['rmb'];
            $tmp['coin'] = $item['coin'];
            $tmp['time'] = date('Y-m-d h:i:s', $item['addtime']);
            $result[] = $tmp;
            $tmp = array();
        }
        $this->responseSuccess($result);
    }
    /**
     * 粉丝贡献列表.
     *
     * @param token string
     * @param page int
     */
    public function ShareContributeList( $user_id, $page = 1)
    {
        $result = array(
            'total_cnt' => 0,
            'page' => 0,
            'size' => 0,
            'page_cnt' => 0,
            'list' => array(),
            'sum_coin' => 0,
            );
        $count = M('Coindetail')->query("select COUNT(DISTINCT(uid)) as count from ss_coindetail where type='expend' AND touid={$user_id}");
        if (!$count[0]['count']) {
            $this->responseSuccess($result);

            return;
        }
        $sum_coin = 0;
        $count = $count[0]['count'];

        $listRows = 10;
        $page = !$page ? 1 : $page;
        // $page = (int)$page <= ceil($count/$listRows) ? (int)$page : ceil($count/$listRows);
        $firstRows = ($page - 1) * $listRows;
        $sql = "select ss_member.id as userid,ss_member.birthday as birthday, nickname as username,uid,sex,spendcoin,avatartime,SUM(coin) as coin from ss_coindetail,ss_member where type='expend' AND touid=$user_id and uid=ss_member.id GROUP BY uid ORDER BY coin DESC limit $firstRows, $listRows";
        $contribute = M('Coindetail')->query($sql);
        foreach ($contribute as $key => $item) {
            $contribute[$key]['age'] =  empty($user_info['birthday']) || is_null($user_info['birthday']) ? 0 : date("Y") - date("Y", $user_info['birthday'] );
            $contribute[$key]['avatar'] = getAvatar($item['avatartime'],$item['userid']);
            $contribute[$key]['sex'] = $contribute[$key]['sex'] == 1 ? 1 : 0;
            $level = getRichlevel($contribute[$key]['spendcoin']);
            $contribute[$key]['levelid'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
            $sum_coin += $contribute[$key]['coin'];
            unset($contribute[$key]['ucuid']);
            unset($contribute[$key]['birthday']);
            unset($contribute[$key]['spendcoin']);
        }

        // 更具折算规则计算
        // 这是一个坑！可能折算比例会变
        /*
        $agentuid = M('Member')->where(array('id'=>$user_id))->getField('agentuid');
        if ($agentuid != 0) {
            $ratio = D('Agentfamily')->where('uid='.$agentuid)->getField('uid,familyratio,anchorratio');
            $ratio = $ratio[$agentuid];
            $sum_coin = $sum_coin * ($ratio['anchorratio'] / 100);
        } else {
            //默认的比例
            $site = D('Siteconfig')->find();
            $sum_coin = $sum_coin * ($site['emceededuct'] / 100);
        }
        */

        $result = array(
            'total_cnt' => $count,
            'page' => $page,
            'size' => $listRows,
            'page_cnt' => $page,
            'list' => $contribute,
            'sum_coin' => $sum_coin,
            );
        $this->responseSuccess($result);
    }
    /**
     * 根据链接获取二维码
     *
     * @param url 二维码路径
     */
    public function qrcode(){
        $url = $_GET['url'];
        require_once APP_PATH . 'Extension/phpqrcode/phpqrcode.php';
        QRcode::png($url,false,'L',4,2,true);
    }

    /**
     * 设置当前用户的推荐人
     *
     * @uid url 设置推荐人id
     */
    public function setRecommenUser($uid,$token) {

        $has = M("Member")->field("id")->where(array('id'=>$uid))->find();
        if(empty($has)) {
            $this->responseError("推荐人不存在.");
        }

        $has = M("Recommendation")->field("id")->where(array('uid'=>$uid))->find();
        if(!empty($has)) {
            $this->responseError("推荐人已经存在,不能重复设置.");
        }

        $user = TokenHelper::getInstance()->get($token);

        $data = array();
        $data['uid'] = $user['uid'];
        $data['recommen_id'] = $uid;
        $data['date'] = date("Y-m-d H:i:s");

        M('Recommendation')->add($data);
        $this->responseSuccess();
    }

    /**
     * 根据用户id 获取它的推荐人信息
     *
     * @uid url 设置推荐人id
     */
    public function getRecommenUserByUid($uid) {
        $recommen = M('Recommendation')->field("recommen_id")->where(array('uid'=>$uid))->find();

        if(empty($recommen)) {
            return '';
        }

        $user = M("Member")->field("nickname")->where(array('id'=>$recommen['recommen_id']))->find();
        return $user['nickname'];
    }

    /**
     * 举报接口
     * @param  string $token   通行证
     * @param  int $accused 被举报人id
     * @return json          操作状态
     */
    public function report( $token, $accused = null ){
        $uid = $this->current_uid;
        if( empty( $accused ) ){
            $this->responseError( L("_PARAM_ERROR_") );
        }
        $bsid = M('backstream')->where("streamstatus = '1' and uid = $accused ")->getField("id");
        if( !$bsid ){
            $this->responseError( L("_PARAM_ERROR_") );
        }
        if( count(M("report")->where(" bsid=$bsid and uid=$uid ")->select()) ){
            $this->responseError( L("_ALREADY_REPORTED_") );
        }
        $data = array(
            "uid" => $uid,
            "accused" => $accused,
            "bsid" => $bsid,
            "time" => time()
            );
        M("report")->add($data);
        $this->responseSuccess( L("_OPERATION_SUCCESS_") );
    }
    /**
     * 获取C级代理代理用户
     * @param  agentid 代理人ID
     * @return json          操作状态
     */
    public function beActingUser($agentid){
        $agentid = $_GET['agentid'];
        $list = M('member')->where('agentid ='.$agentid)->select();
        $this->responseSuccess($list);
    }
}
