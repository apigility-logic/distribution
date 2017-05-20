<?php
namespace app\common\model;

use think\Model;

class AdminUser extends SoftDeleteBase
{
    public $keyword = 'username|real_name';

    public static function getLabel()
    {
        $label = [
            'username' => '账号',
            'real_name' => '姓名',
            'password' => '密码',
            'confirm_password' => '重复密码',
            'status' => '状态',
            'group_id' => '角色组',
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function modifyPassword($username, $password)
    {
        $password_salt = \Util::createRandomString(8);
        $password_md5 = \Util::passwordMd5($password, $password_salt);
        return $this->where(['username' => $username])->save([
            'password' => $password_md5,
            'password_salt' => $password_salt
        ]);
    }

    /**
     * 更新用户密码
     * @param int $id
     * @param string $password
     * @return int
     */
    public function modifyPasswordByUid($id, $password)
    {
        $password_salt = \Util::createRandomString(8);
        $password_md5 = \Util::passwordMd5($password, $password_salt);
        return $this->where([
            'id' => $id
        ])->save([
            'password' => $password_md5,
            'password_salt' => $password_salt
        ]);
    }



}