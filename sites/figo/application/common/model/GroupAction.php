<?php
namespace app\common\model;

use think\Model;

class GroupAction extends Base
{
    public static function getLabel()
    {
        $label = [
            'id' => '拼团ID',
            'user_id' => '会员ID',
            'goods_id' => '商品ID',
            'group_num' => '拼团人数',
            'profile' => UserProfile::getLabel(),
            'goods' => GroupGoods::getLabel(),
            'end_time' => '结束时间',
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function profile()
    {
        return $this->hasOne('UserProfile', 'user_id', 'user_id');
    }

    public function goods()
    {
        return $this->hasOne('GroupGoods', 'id', 'goods_id');
    }

    public function records()
    {
        return $this->hasMany('GroupActionRecord', 'group_action_id', 'id');
    }
}