<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class GroupActionRecord extends Base
{
    public $model = 'group_action_record';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'user_id', 'profile.nickname', 'is_free', 'group_order_id', 'create_time'],
            'extends' => [
                'is_free' => function ($data) {
                    return map([
                        0 => '<span class="label label-warning">否</span>',
                        1 => '<span class="label label-success">是</span>',
                    ], $data['is_free']);
                }
            ]
        ];
    }

    protected function form()
    {
        return [
        ];
    }


}