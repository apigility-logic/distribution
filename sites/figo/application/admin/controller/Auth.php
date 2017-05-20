<?php
namespace app\admin\controller;

use app\admin\Menu;
use think\Controller;

class Auth extends Controller
{

    //登录
    public function login()
    {
        if (!request()->isPost()) {
            return $this->fetch();
        }
        $username = request()->param('username');
        $password = request()->param('password');
//        $verify = request()->param('verify');
//        /* 检测验证码 TODO: */
//        if (!check_verify($verify, 'login')) {
//            $this->error('验证码输入错误！');
//        }

        $status = \app\admin\Auth::check($username, $password);
        switch ($status) {
            case \app\admin\Auth::AUTH_SUCCESS:
                \app\admin\Auth::saveAuth($username);
                $uid = \app\admin\Auth::getUid();
                $group_id = \app\admin\Auth::getGroup();
                $Menu = new Menu();
                $url = $Menu->firstTopMenu($group_id);
                model('admin_login_log')->save([
                    'admin_id' => $uid,
                    'type' => 1,
                    'create_time' => time()
                ]);
                $this->success('登录成功', Url($url));
                break;
            case \app\admin\Auth::AUTH_USER_NOT_FIND:
                $this->error('登录名不存在');
                break;
            case \app\admin\Auth::AUTH_PASSWORD_ERROR:
                $this->error('登录名或密码错误');
                break;
        }
    }

    //退出登录
    public function logout()
    {
        $uid = \app\admin\Auth::getUid();
        if ($uid) {
            model('admin_login_log')->save([
                'admin_id' => $uid,
                'type' => 2,
                'create_time' => time()
            ]);
        }
        session_destroy();
        $this->redirect('Auth/login');
    }

    public function verify()
    {
        $verify = new \Think\Verify();
        $verify->entry('login');
    }
}
