<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class GroupCategory extends TreeBase
{
    public $model = 'group_category';

    protected function config(){
        return [
            'name' => '商品分类'
        ];
    }

}