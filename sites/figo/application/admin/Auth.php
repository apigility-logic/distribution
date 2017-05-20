<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/17
 * Time: 上午12:29
 */

namespace app\admin;


class Auth
{

    const AUTH_SUCCESS = 0;
    const AUTH_USER_NOT_FIND = 1;
    const AUTH_PASSWORD_ERROR = 2;

    public static $Auth = null;

    /**
     * 获取Auth对象
     * @return object
     */
    public static function getAuth()
    {
        if (is_null(self::$Auth)) {
            self::$Auth = new \Auth();
        }
        return self::$Auth;
    }

    /**
     * 是否已登录认证
     * @return boolean
     */
    public static function isAuth()
    {
        $auth_config = config('AUTH_CONFIG');
        return session($auth_config['AUTH_SESSION_KEY']) ? TRUE : FALSE;
    }

    /**
     * 检查权限
     * @param name string|array  需要验证的规则列表,|或者，&并且
     * @param uid  int           认证用户的id
     * @param string mode        执行check的模式
     * @return boolean           通过验证返回true;失败返回false
     */
    public static function checkAuth($name, $uid = 0, $type = 1, $mode = 'url')
    {
        if (false === self::checkPriv($name, $uid, $type, $mode)) {
            if (IS_AJAX) {
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode(array('status' => 0, 'info' => '没有权限')));
            }
            header('Content-Type:text/html; charset=utf-8');
            exit('没有访问权限');
        }
    }

    /**
     * 检查权限
     * @param name string|array  需要验证的规则列表,|或者，&并且
     * @param uid  int           认证用户的id
     * @param string mode        执行check的模式
     * @return boolean           通过验证返回true;失败返回false
     */
    public static function checkPriv($name, $uid = 0, $type = 1, $mode = 'url')
    {
        if (self::isAdmin()) {//超级管理员
            return TRUE;
        }
        $Auth = self::getAuth();
        empty($uid) && $uid = self::getUid();
        $or_name = explode('|', $name);
        foreach ($or_name as $row) {
            $and_name = explode('&', $row);
            if ($Auth->check(join(',', $and_name), $uid, $type, $mode, 'and')) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查登录认证，未认证则跳转登录界面
     * @param type $redirect_url
     */
    public static function requireAuth($redirect_url = '')
    {
        empty($redirect_url) && $redirect_url = U('Auth/index');
        if (!self::isAuth()) {
            redirect($redirect_url);
        }
        !defined('UID') && define('UID', self::getUid());
    }

    /**
     * 是否为超级管理员
     * @return boolean
     */
    public static function isAdmin()
    {
        if (self::isAuth()) {
            $uid = self::getUid();
            if (in_array($uid, explode(',', config('ADMIN_UID')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查是否为超级管理员，否则跳转登录界面
     * @param type $redirect_url
     */
    public static function requireAdmin($redirect_url = '')
    {
        empty($redirect_url) && $redirect_url = Url('Auth/index');
        if (!self::isAdmin()) {
            redirect($redirect_url);
        }
    }

    /**
     * 检查用户名密码是否正确
     * @param string $username 用户名
     * @param string $password 密码
     * @return int
     */
    public static function check($username, $password)
    {
        $user_info = self::getUserInfo($username);
        if (empty($user_info)) {
            return self::AUTH_USER_NOT_FIND;
        }
        if (\Util::passwordMd5($password, $user_info['password_salt']) === $user_info['password']) {
            return self::AUTH_SUCCESS;
        } else {
            return self::AUTH_PASSWORD_ERROR;
        }
    }

    /**
     * 获取用户信息
     * @staticvar array $user_info
     * @param string $username
     * @return array
     */
    public static function getUserInfo($username = '')
    {
        if ('' === $username) {
            return session('user_info');
        }
        static $user_info = array();
        if (!isset($user_info[$username])) {
            $data = model('admin_user')->where(['username' => $username])->find();
            if ($data) {
                $user_info[$username] = $data;
            }
        }
        return $user_info[$username];
    }

    /**
     * 获取用户ID
     * @return int
     */
    public static function getUid()
    {
        $user_info = self::getUserInfo();
        return isset($user_info['id']) ? $user_info['id'] : 0;
    }

    /**
     * 保存登录认证信息
     * @param string $username
     */
    public static function saveAuth($username)
    {
        $user_info = self::getUserInfo($username);
        $auth_config = config('AUTH_CONFIG');
        session($auth_config['AUTH_SESSION_KEY'], TRUE);
        session('user_info', $user_info);
    }

    /**
     * 获取用户所在组
     * @param int $uid 用户ID
     * @return array 用户组
     */
    public static function getUserGroup($uid)
    {
        $data = model('auth_group')->with('groups')->where(['auth_group_access.uid' => $uid])->select();
        return $data;
    }

    /**
     * @return array|string
     */
    public static function getGroup()
    {
        $user_info = self::getUserInfo();
        $group_id = $user_info['group_id'];
        return $group_id;
    }

}