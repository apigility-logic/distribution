<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 17/5/3
 * Time: 下午2:56
 */

namespace app\api\controller;

use app\common\Code;
use app\common\Request;
use app\common\Response;

class Base
{
    public $Request;
    public $Response;

    public function __construct()
    {
        $this->Request = Request::instance();
        $this->Response = Response::instance();
    }

    public function success()
    {
        return $this->Response->getJson(Code::SUCCESS);
    }

    public function error($code = Code::ERROR)
    {
        return $this->Response->getJson($code);
    }

}