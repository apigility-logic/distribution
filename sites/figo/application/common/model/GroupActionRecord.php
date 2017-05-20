<?php
namespace app\common\model;

use think\Model;

class GroupActionRecord extends Base
{
    public static function getLabel()
    {
        $label = [
            'user_id' => '会员ID',
            'group_action_id' => '拼团ID',
            'is_free' => '是否免单',
            'group_order_id' => '订单ID',
            'profile' => UserProfile::getLabel()
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function profile()
    {
        return $this->hasOne('UserProfile', 'user_id', 'user_id');
    }
}