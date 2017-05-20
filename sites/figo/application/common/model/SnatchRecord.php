<?php
namespace app\common\model;

use think\Model;

class SnatchRecord extends Base
{
    public static function getLabel()
    {
        $label = [
            'user_id' => '会员ID',
            'snatch_round_id' => '夺宝轮次',
            'snatch_goods_id' => '商品ID',
            'codes' => '夺宝码',
            'code_num' => '参与次数',
            'profile' => UserProfile::getLabel(),
            'goods' => SnatchGoods::getLabel(),
            'create_time' => '参与时间',
        ];
        return $label;
    }

    public function round()
    {
        return $this->hasOne('SnatchRound', 'id', 'snatch_round_id');
    }

    public function goods()
    {
        return $this->hasOne('SnatchGoods', 'id', 'snatch_goods_id');
    }

    public function profile()
    {
        return $this->hasOne('UserProfile', 'user_id', 'user_id');
    }
}