<?php
namespace app\common\behavior;

use app\common\Code;
use app\common\Request;
use app\common\Response;

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/11/22
 * Time: 下午11:10
 */
class AppEnd
{
    public function run(&$params)
    {
        $module = Request::instance()->module();
        $controller = Request::instance()->controller();
        $action = Request::instance()->action();

        $response = $params->getData();

        $behavior = "\\app\\{$module}\\behavior\\{$controller}";
        if (class_exists($behavior)) {
            $Behavior = new $behavior();
            $method = $action . 'End';
            if(method_exists($Behavior, $method)) {
                $Behavior->$method($response);
            }
        }
        $params->data($response);
    }

}