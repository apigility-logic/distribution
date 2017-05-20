<?php
namespace app\common\model;

use think\Model;

class AuthGroup extends Model
{
    public function access()
    {
        return $this->hasOne('AuthGroupAccess', 'group_id', 'id');
    }

}