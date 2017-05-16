<?php
namespace app\common\model;

use think\Model;

class UserProfile extends Base
{
    public static function getLabel()
    {
        return [
            'avatar' => '头像',
            'nickname' => '昵称'
        ];
    }
}