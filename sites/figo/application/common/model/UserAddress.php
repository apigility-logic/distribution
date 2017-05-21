<?php
namespace app\common\model;

use think\Model;

class UserAddress extends SoftDeleteBase
{
    public static function getLabel()
    {
        $label = [
            'consignee' => '收件人',
            'mobile' => '手机号码',
            'province_id' => '省ID',
            'city_id' => '市ID',
            'area_id' => '区ID',
            'address' => '地址',
            'street' => '街道',
            'is_default' => '默认',
            'district' => '地区',
            'profile' => UserProfile::getLabel()
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function profile()
    {
        return $this->hasOne('UserProfile', 'user_id', 'user_id')->field($this->getFields('profile'));
    }

}