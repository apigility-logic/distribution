<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class SnatchRecord extends Base
{
    public $model = 'snatch_record';

    protected function dataTable()
    {
        return [
            'fields' => ['snatch_round_id', 'profile.nickname', 'snatch_goods_id', 'code_num', 'codes', 'create_time'],
            'extends' => [
                'codes' => function ($data) {
                    return '<a href="javascript:;" data-toggle="modal" data-target="#modalCodes-' . $data['id'] . ' "><i class="fa fa-list"></i> 查看</a>' .
                        '<div class="modal fade" id="modalCodes-' . $data['id'] . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="exampleModalLabel">夺宝码</h4>
                          </div>
                          <div class="modal-body">'.
                            $data['codes']
                            .'</div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                          </div>
                        </div>
                      </div>
                    </div>';
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