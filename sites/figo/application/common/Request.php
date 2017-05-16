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

    protected $params = [];

    protected $app_log_id = 0;

    protected $app = null;

    protected $user_id = null;

    protected $admin_user_id = null;

    protected $uri_limit = [];

    protected $Request;

    protected function __construct()
    {
        if (!empty($_REQUEST)) {
            $params = $_REQUEST;
        } else {
            $params = json_decode(file_get_contents('php://input'), true);
            !is_array($params) && $params = [];
        }
        $this->params = \think\Request::instance()->input($params);
        $this->uri = '/' . \think\Request::instance()->path();
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
     * 获取所有请求参数,post or raw
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * 获取参数
     * @param $key
     * @param null $default
     * @return null
     */
    public function getParam($key, $default = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * 设置参数
     * @param $key
     * @param $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setDefaultParam($key, $value)
    {
        $param = $this->getParam($key);
        if(is_null($param)) {
            $this->setParam($key, $value);
        }
    }

    /**
     * 用户ID
     * @return int|mixed|null
     */
    public function getUserId()
    {
        if (is_null($this->user_id)) {
            $token = $this->getParam('token');
            $this->user_id = Loader::model('UserToken')->getUid($token);
        }
        return $this->user_id;
    }

    // 调用驱动类的方法
    public function __call($method, $params)
    {
        if(empty($params)){
            return \think\Request::instance()->$method();
        } else {
            return \think\Request::instance()->$method($params);
        }

    }
}