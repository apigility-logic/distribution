<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class Job extends Base
{
    public $model = 'job';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'title', 'status'],
            'extends' => [
                'status' => function ($data) {
                    return map([0 => '禁用', 1 => '启用'], $data['status']);
                }
            ]
        ];
    }

    protected function form()
    {
        return [
            //field, type
            ['title', \FormHelper::TYPE_TEXT],
            ['status', \FormHelper::TYPE_SELECT, [0 => '禁用', 1 => '启用']]
        ];
    }


}