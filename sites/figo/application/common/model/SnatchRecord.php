<?php
namespace app\common\model;

use think\Model;

class SnatchRecord extends Base
{
    public static function getLabel()
    {
        $label = [
            'user_id' => '会员ID',
            'round_id' => '夺宝轮次',
            'goods_id' => '商品ID',
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
        return $this->hasOne('SnatchRound', 'id', 'round_id')->with('profile')->field($this->getFields('round'));
    }

    public function goods()
    {
        return $this->hasOne('SnatchGoods', 'id', 'goods_id')->field($this->getFields('goods'));
    }

    public function profile()
    {
        return $this->hasOne('UserProfile', 'user_id', 'user_id')->field($this->getFields('profile'));
    }
}