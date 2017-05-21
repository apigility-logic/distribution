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
            'fields' => ['id', 'user_id', 'profile.nickname', 'is_free', 'order_id', 'order2.pay_status', 'create_time'],
            'extends' => [
                'is_free' => function ($data) {
                    return map([
                        0 => '<span class="label label-warning">否</span>',
                        1 => '<span class="label label-success">是</span>',
                    ], $data['is_free']);
                },
                'order2.pay_status' => function($data) {
                    $pay_status = $data['order2']['pay_status'];
                    return map([
                        0 => '<span class="label label-warning">未支付</span>',
                        1 => '<span class="label label-success">已支付</span>',
                        2 => '<span class="label label-info">全额退款</span>',
                    ], $pay_status);
                },
                'order_id' => function($data) {
                    return href($data['order_id'], '/Admin/GroupOrder/edit', ['id' => $data['order_id']]);
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