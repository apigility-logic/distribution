<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\api\controller;


class District
{
    public function lists()
    {
        return model('district')->where('level', '<', 4)->field('id i,parentid p,path p2, title n')->select();
    }

}