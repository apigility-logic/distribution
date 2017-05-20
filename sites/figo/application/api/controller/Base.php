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

    public function __construct()
    {

    }

    public function success()
    {
        return $this->response()->getJson(Code::SUCCESS);
    }

    public function error($code = Code::ERROR)
    {
        return $this->response()->getJson($code);
    }

    protected function request(){
        return Request::instance();
    }

    protected function response(){
        return Response::instance();
    }

}