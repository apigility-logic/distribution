<?php
namespace app\common\model;

use think\Model;

class SnatchGoods extends SoftDeleteBase
{
    public static function getLabel()
    {
        $label = [
            'title' => '商品标题',
            'intro' => '商品描述',
            'images' => '商品图片',
            'code_unit' => '夺宝码单价',
            'code_num' => '夺宝码个数',
            'goods_num' => '商品个数',
            'status' => '状态',
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function rounds()
    {
        return $this->hasMany('SnatchRound', 'goods_id', 'id')->with('profile')
            ->field($this->getFields('rounds'))
            ->order('snatch_round.id desc')->limit(10);
    }
}