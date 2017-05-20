<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class SnatchRound extends Base
{
    public $model = 'snatch_round';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'snatch_goods_id', 'goods.title', 'code_num', 'code_unit', 'sale_times', 'sale_rate', 'status', 'create_time'],
            'extends' => [
                'sale_rate' => function ($data) {
                    $percent = $data['sale_rate'] * 100;
                    return '<div style="width:100px;">' .
                    '<div class="progress progress-xs">
                      <div class="progress-bar progress-bar-danger" style="width: ' . $percent . '%"></div>
                    </div>' .
                    "<span>{$percent}%</span>" .
                    '</div>';
                },
                'status' => function ($data) {
                    return map([
                        1 => '<span class="label label-success">进行中</span>',
                        2 => '<span class="label label-warning">已结束</span>',
                    ], $data['status']);
                },
            ]
        ];
    }

    protected function form()
    {
        return [
        ];
    }


}