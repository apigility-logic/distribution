<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class SnatchGoods extends Base
{
    public $model = 'snatch_goods';

    protected function dataTable()
    {
        return [
            'fields' => ['title', 'intro', 'code_unit', 'code_num', 'goods_num', 'status'],
            'extends' => [
                'status' => function ($data) {
                    return map([
                        0 => '<span class="label label-danger">禁用</span>',
                        1 => '<span class="label label-success">正常</span>',
                    ], $data['status']);
                }
            ]
        ];
    }

    protected function form()
    {
        return [
            //field, type
            ['title', \FormHelper::TYPE_TEXT],
            ['images', \FormHelper::TYPE_IMAGES],
            ['intro', \FormHelper::TYPE_TEXT],
            ['code_unit', \FormHelper::TYPE_TEXT],
            ['code_num', \FormHelper::TYPE_TEXT],
            ['goods_num', \FormHelper::TYPE_TEXT],
            ['content', \FormHelper::TYPE_EDITOR],
            ['status', \FormHelper::TYPE_RADIO, [
                'options' => [0 => '禁用', 1 => '正常'],
                'default' => 1,
            ]],
        ];
    }


}