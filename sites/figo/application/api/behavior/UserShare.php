<?php
namespace app\api\behavior;

use app\common\Request;

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/11/22
 * Time: 下午11:10
 */
class UserShare
{

    public function listsBefore(&$params)
    {
        if(!isset($params['cond'])) {
            $params['cond'] = [];
        }
        $params['cond']['status'] = ['in', [0,1]];
    }

    public function createBefore(&$params)
    {
        $type = $params['type'];
        $order_id = $params['order_id'];
    }

}