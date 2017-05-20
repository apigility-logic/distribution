<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


use app\common\model\GroupActionRecord;
use think\View;

class GroupAction extends Base
{
    public $model = 'group_action';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'profile.nickname|团长', 'goods_id', 'goods.title', 'group_num', '已团人数', 'create_time|开团时间', 'end_time', 'status|状态'],
            'extends' => [
                'end_time' => function ($data) {
                    return date('Y-m-d H:i:s', $data['end_time']);
                },
                'status' => function ($data) {
                    if ($data['group_num'] == count($data['records'])) {
                        return '<span class="label label-success">已完成</span>';
                    }
                    if ($data['end_time'] < time()) {
                        return '<span class="label label-warning">已结束</span>';
                    }
                },
                '已团人数' => function ($data) {
                    $View = new View();
                    return '<a href="javascript:;" data-toggle="modal" data-target="#modalRecords-' . $data['id'] . ' ">' . count($data['records']) . '</a>' .
                    '<div class="modal fade" id="modalRecords-' . $data['id'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="exampleModalLabel">参团会员</h4>
                          </div>
                          <div class="modal-body">' .
                    $View->fetch('content/table', [
                        'list_data' => model('group_action_record')->with('profile')->where('group_action_id', $data['id'])->select(),
                        'data_table' => [
                            'fields' => ['id', 'user_id', 'profile.nickname', 'is_free', 'create_time'],
                            'extends' => [
                                'is_free' => function ($data) {
                                    return map([
                                        0 => '<span class="label label-warning">否</span>',
                                        1 => '<span class="label label-success">是</span>',
                                    ], $data['is_free']);
                                }
                            ]
                        ],
                        'label' => GroupActionRecord::getLabel(),
                        'has_action' => false,
                    ])
                    . '</div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                          </div>
                        </div>
                      </div>
                    </div>';

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
            ['group_num', \FormHelper::TYPE_TEXT],
            ['group_free_type', \FormHelper::TYPE_RADIO, [
                'options' => [1 => '团长免单', 2 => '随机免单'],
                'default' => 1,
            ]],
            ['group_price', \FormHelper::TYPE_TEXT],
            ['goods_price', \FormHelper::TYPE_TEXT],
            ['content', \FormHelper::TYPE_TEXTAREA],
            ['is_recommend', \FormHelper::TYPE_RADIO, [
                'options' => [0 => '否', 1 => '是'],
                'default' => 1,
            ]],
            ['is_sale', \FormHelper::TYPE_RADIO, [
                'options' => [0 => '下架', 1 => '上架'],
                'default' => 1,
            ]],
        ];
    }


}