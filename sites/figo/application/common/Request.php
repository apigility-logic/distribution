<?php
namespace app\common;

use think\Loader;

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/11/22
 * Time: 下午11:28
 */
class Request
{
    protected static $instance;

    protected $uri = '';

    protected $user_id = null;

    protected function __construct()
    {
        $this->uri = '/' . request()->module() . '/' . request()->controller() . '/' . request()->action();
    }

    /**
     * 初始化
     * @access public
     * @return \think\Request
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 用户ID
     * @return int|mixed|null
     */
    public function getUserId()
    {
        if (is_null($this->user_id)) {
            $token = request()->param('token');
            $Http = new \HttpHelper();
            $this->user_id = 1;
//            $response = $Http->post('http://zhibo.mimilove520.com/OpenAPI/v1/user/autoLogin', [
//                'token' => $token
//            ]);
        }
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * 调用驱动类的方法
     */
    public function __call($method, $params)
    {
        if (empty($params)) {
            return request()->$method();
        } else {
            return request()->$method($params);
        }
    }
}