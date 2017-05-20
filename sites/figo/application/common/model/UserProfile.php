<?php
namespace app\common\model;

use think\Model;

class UserProfile extends SoftDeleteBase
{
    public static function getLabel()
    {
        $label = [
            'avatar' => '头像',
            'nickname' => '昵称'
        ];
        return array_merge(parent::getLabel(), $label);
    }
}