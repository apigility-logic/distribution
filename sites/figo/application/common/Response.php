<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/11/23
 * Time: 上午12:33
 */

namespace app\common;
use app\common\Code;

class Response
{

    const ERR_CODE = 'err_code';
    const ERR_MSG = 'err_msg';

    protected static $instance = null;

    public $Response;

    protected function __construct()
    {
        $this->Response = new \think\Response();
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

    public static function getJson($code = Code::ERROR, $err_msg = null)
    {
        return [
            self::ERR_CODE => $code,
            self::ERR_MSG => isset($err_msg) ? $err_msg : Code::getErrorMessage($code)
        ];
    }

    /**
     *
     */
    public static function error($code = Code::ERROR, $err_msg = null, $type = 'json')
    {
        $data = array(
            self::ERR_CODE => $code,
            self::ERR_MSG => isset($err_msg) ? $err_msg : Code::getErrorMessage($code)
        );

        $Response = \think\Response::create($data, $type, 200);
        exit($Response->send());
    }

    // 调用驱动类的方法
    public function __call($method, $params)
    {
        return $this->Response->$method($params);
    }
}