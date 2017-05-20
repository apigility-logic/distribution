<?php
namespace app\common\model;

use think\Model;

class AuthGroupAccess extends Model
{
    public function groups()
    {
        return $this->hasMany('AuthGroup', 'id', 'group_id');
    }

    /**
     * 更新用户组
     * @param int $uid
     * @param string $group_id
     * @return array
     */
    public function updateUserGroup($uid, $group_id)
    {
        $this->where(['uid' => $uid])->delete();
        if ($group_id) {
            $this->isUpdate(false)->save(['uid' => $uid, 'group_id' => $group_id]);
        }
        return $uid;
    }

    public function addUserGroup($uid, $group_id)
    {
        return $this->isUpdate(false)->save(array('uid' => $uid, 'group_id' => $group_id));
    }
}