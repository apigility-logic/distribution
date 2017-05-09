<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/5/15
 * Time: 上午12:10.
 */
class LoginBaseAction extends Action
{
    protected $supported_type = array('qq', 'wechat', 'sina');

    public function _initialize()
    {
        // 导入需要认证的基类.
        import('@.ORG.ThinkOauth');
        import('@.ORG.Wechat');
        require_once APP_PATH.'/config.inc.php';
    }

    /**
     * 检查注册结果.
     *
     * @param $uid 用户UCUID.
     *
     * @return bool|string 成功返回true,失败返回错误原因.
     */
    private function checkRegisterResult($uid)
    {
        if ($uid <= 0) {
            if ($uid == -1) {
                $msg = '用户名不合法';
            } elseif ($uid == -2) {
                $msg = '包含不允许注册的词语';
            } elseif ($uid == -3) {
                $msg = '用户名已经存在';
            } elseif ($uid == -4) {
                $msg = 'Email 格式有误';
            } elseif ($uid == -5) {
                $msg = 'Email 不允许注册';
            } elseif ($uid == -6) {
                $msg = '该 Email 已经被注册';
            } else {
                $msg = '未知错误';
            }

            return $msg;
        }

        return true;
    }

    /**
     * 默认的登录页面.
     */
    public function index()
    {
        $type = isset($_GET['type']) ? strtolower($_GET['type']) : '';
        if (!in_array($type, $this->supported_type)) {
            exit('Not Supported');
        }
        // 如果是微信,特殊处理.
        if ($type == 'wechat') {
            $appid = C('WX_APPID');
            $redirect = C('WX_DOMAIN');
            $scope = 'snsapi_userinfo';
            $url = "https://open.weixin.qq.com/connect/qrconnect?appid=$appid&redirect_uri=$redirect&response_type=code&scope=$scope&state=STATE#wechat_redirect";
            redirect($url);
        }
        $sdk = \ThinkOauth::getInstance($type);
        redirect($sdk->getRequestCodeURL());
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
    private function registerFromThird($nickname, $username, $sex = 0, $ext = null)
    {
        // 初始化一个密码(不要32bit的,不然ucenter会SB.)
        $pwd = md5('7h1stH4VeRy5eCr4tKey!!Random').rand(0, 9);
        // 并发的时候要SB.
        $room_num = rand(0, 10000).time();
        $new_user = array(
            'nickname' => $nickname,
            'username' => uniqid().rand(0,9), // Ucenter限制15个长度,第三方登录不应该出现用户名重复的提示.目前随机.
            'password' => md5($pwd),
            'password2' => $this->pswencode($pwd),
            'email' => md5(time().rand(0,1000)).'@meilibo.net',
            'regtime' => time(),
            'curroomnum' => $room_num,
            'sex' => $sex,
        );
        $ucuid = uc_user_register($new_user['username'], $pwd, $new_user['email']);
        $result = $this->checkRegisterResult($ucuid);
        if ($result !== true) {
            throw_exception($result);
        }
        $new_user['ucuid'] = $ucuid;

        if (isset($ext)) {
            $new_user['intro'] = isset($ext['intro']) ? $ext['intro'] : '';
        }

        return $new_user;
    }

    /**
     * QQ登录回调接口.
     *
     * 如果QQ验证通过,试图从系统中查询用户信息,不存在则注册一个,然后返回这个用户信息.
     *
     * @param $token 回调的token信息.
     *
     * @return mixed 本系统中的用户信息.
     */
    protected function qq($token)
    {
        $qq = \ThinkOauth::getInstance('qq', $token);
        $data = $qq->call('user/get_user_info');
        if ($data['ret'] != 0) {
            throw_exception("获取腾讯QQ用户信息失败：{$data['msg']}");
        }
        $open_id = $token['openid'];
        $user_info = D('Member')->where(array('qopenid' => $open_id))->find();
        if (!empty($user_info)) {
            return $user_info;
        }
        $new_user = $this->registerFromThird($data['nickname'], 'qq_'.$data['nickname'],  $data['gender'] == '男' ? '0' : '1');
        $new_user['qopenid'] = $open_id;
        $uid = D('Member')->add($new_user);
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');

        return D('Member')->where(array('id' => $uid))->find();
    }

    /**
     * 新浪登录回调接口.
     *
     * 如果微博验证通过,试图从系统中查询用户信息,不存在则注册一个,然后返回这个用户信息.
     *
     * @param $token 回调的token信息.
     *
     * @return mixed 本系统中的用户信息.
     */
    protected function sina($token)
    {
        $sina = \ThinkOauth::getInstance('sina', $token);
        $data = $sina->call('users/show', "uid={$sina->openid()}");
        if (isset($data['error_code'])) {
            throw_exception("获取新浪微博用户信息失败：{$data['error']}");
        }
        $open_id = $token['openid'];
        $user_info = D('Member')->where(array('sinaid' => $open_id))->find();
        if (!empty($user_info)) {
            return $user_info;
        }
        $sex = $data['gender'] == 'm' ? 0 : 1;
        $ext = array('intro' => $data['description']);
        $new_user = $this->registerFromThird($data['screen_name'], 'sina_'.$data['name'], $sex, $ext);
        $new_user['sinaid'] = $open_id; // sina ID
        $uid = D('Member')->add($new_user);
        D('Roomnum')->execute('insert into ss_roomnum(uid,num,addtime) values('.$uid.','.$new_user['curroomnum'].','.time().')');

        return D('Member')->where(array('id' => $uid))->find();
    }

    /**
     * 微信登录回调接口.
     *
     * 如果微信验证通过,试图从系统中查询用户信息,不存在则注册一个,然后返回这个用户信息.
     *
     * @param $token 回调的token信息.
     *
     * @return mixed 本系统中的用户信息.
     */
    protected function wechat($token)
    {
        throw_exception('暂未实现');
    }

    public function _empty()
    {
        header("HTTP/1.1 404 Page Not Found");exit;
    }

    /**
     * 加密.
     *
     * @param $txt 原始字符串.
     * @param string $key 密钥.
     *
     * @return string 加密结果.
     */
    protected function pswencode($txt, $key = 'youst')
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+_)(*&^%$#@!~';
        $nh = rand(0, 64);
        $ch = $chars[$nh];
        $mdKey = md5($key.$ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = base64_encode($txt);
        $tmp = '';
        $k = 0;
        for ($i = 0; $i < strlen($txt); ++$i) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh + strpos($chars, $txt[$i]) + ord($mdKey[$k++])) % 64;
            $tmp .= $chars[$j];
        }

        return $ch.$tmp;
    }

    /**
     * 解密.
     *
     * @param $txt 待解密字符串.
     * @param string $key 密钥.
     *
     * @return string 原始字符串.
     */
    protected function pswdecode($txt, $key = 'youst')
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+_)(*&^%$#@!~';
        $ch = $txt[0];
        $nh = strpos($chars, $ch);
        $mdKey = md5($key.$ch);
        $mdKey = substr($mdKey, $nh % 8, $nh % 8 + 7);
        $txt = substr($txt, 1);
        $tmp = '';
        $k = 0;
        for ($i = 0; $i < strlen($txt); ++$i) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = strpos($chars, $txt[$i]) - $nh - ord($mdKey[$k++]);
            while ($j < 0) {
                $j += 64;
            }
            $tmp .= $chars[$j];
        }

        return base64_decode($tmp);
    }
}
