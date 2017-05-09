<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/5/14
 * Time: 下午11:24.
 */
class OAuthAction extends LoginBaseAction
{
    /**
     * 登录回调.
     */
    public function Login()
    {
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $code = isset($_GET['code']) ? $_GET['code'] : null;
        if (!in_array($type, $this->supported_type)) {
            exit('Not Supported');
        }
        $sdk = \ThinkOauth::getInstance($type);
        try {
            $token = $sdk->getAccessToken($code, null);
            // 授权查询用户信息,如果没有,则创建对应的用户信息,如果失败,抛出异常.
            $user_info = $this->$type($token);
        } catch (Exception $e) {
            die($e->getMessage());
        }
        $raw_pwd = $this->pswdecode($user_info['password2']);

        // 开始做登录.
        // list($uid, $username, $password, $email) = uc_user_login($user_info['username'], $raw_pwd);
        // // 从这里来的,理论上不应该出现一下case.
        // if ($uid == -1) {
        //     die('无此用户');
        // } elseif ($uid == -2) {
        //     die('用户名或密码错误');
        // } elseif ($uid == -3) {
        //     // 没看懂Ucenter里面用的secques意思
        //     die('系统错误');
        // }

        //写入本次登录时间及IP
        D('Member')->where('id='.$user_info['id'])->setField('lastlogtime', time());
        D('Member')->where('id='.$user_info['id'])->setField('lastlogip', get_client_ip());
        // Do What The Fuck !!!
        session('uid', $user_info['id']);
        //session('ucuid', $user_info['ucuid']);
        session('username', $user_info['username']);
        session('nickname', $user_info['nickname']);
        session('roomnum', $user_info['curroomnum']);
        cookie('userid', $user_info['id'], 2500000);
        //cookie('ucuid', $user_info['ucuid'], 2500000);
        cookie('username', $user_info['username'], 2500000);
        cookie('nickname', $user_info['nickname'], 2500000);
        cookie('roomnum', $user_info['curroomnum'], 2500000);

        // I don't know why, but it work ...
        $ucsynlogin = uc_user_synlogin($uid);

        $token = md5('stringSalt'.$user_info['id'].rand(0, 99999));
        cookie('token', $token, 2500000);
        $mem_config = C('MEM_CACHE');
        list($ip, $port) = explode(':', $mem_config['mem_server']);
        $memc_obj = new Memcached();
        $memc_obj->addServer($ip, $port);
        $memc_obj->set(C('PHP_CHAT_SESSION_PREFIX').$user_info['id'].$token, $token, 2500000);
        // TODO:获取到跳转之前的页面,然后跳转.
        redirect('/');
    }
}
