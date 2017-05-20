<?php
namespace app\common\model;

use think\Model;

class UserToken extends SoftDeleteBase
{

    public function getUid($token){
        return 1;
    }
}