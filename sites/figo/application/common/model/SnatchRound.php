<?php
namespace app\common\model;

use think\Model;

class SnatchRound extends Base
{
    public static function getLabel()
    {
        $label = [
            'snatch_goods_id' => '商品ID',
            'code_unit' => '夺宝码单价',
            'code_num' => '夺宝码个数',
            'sale_times' => '已购个数',
            'sale_rate' => '已购进度',
            'status' => '状态',
            'goods' => SnatchGoods::getLabel()
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function goods(){
        return $this->hasOne('snatch_goods', 'id', 'snatch_goods_id');
    }
}