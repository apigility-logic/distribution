<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


use app\common\Curd;

class AdminUser extends Base
{
    public $model = 'admin_user';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'username', 'real_name', 'status', 'create_time'],
            'extends' => [
                'status' => function ($data) {
                    return map([
                        0 => '<span class="label label-danger">禁用</span>',
                        1 => '<span class="label label-success">正常</span>',
                        2 => '<span class="label label-warning">冻结</span>'
                    ], $data['status']);
                }
            ]
        ];
    }

    protected function form()
    {
        return [
            //field, type
            ['username', $this->data ? \FormHelper::TYPE_STATIC : \FormHelper::TYPE_TEXT],
            ['real_name', \FormHelper::TYPE_TEXT],
            ['password', \FormHelper::TYPE_PASSWORD],
            ['confirm_password', \FormHelper::TYPE_PASSWORD],
            ['group_id', \FormHelper::TYPE_SELECT, [
                'options' => [0 => '选择角色'],
                'model' => ['auth_group', 'id', 'title']
            ]],
            ['status', \FormHelper::TYPE_SELECT, [
                'options' => [0 => '禁用', 1 => '正常', 2 => '冻结'],
                'default' => 1,
            ]],
        ];
    }

    public function insert($callback = null)
    {
        $this->save();
    }

    public function update($callback = null)
    {
        $this->save();
    }

    //新增或更新用户数据
    protected function save()
    {
        $request = request();
        $id = $request->post('id');
        $username = $request->post('username');
        $real_name = $request->post('real_name');
        $password = $request->post('password');
        $confirm_password = $request->post('confirm_password');
        $group_id =$request->post('group_id');
        $status = $request->post('status');
        $data = [
            'real_name' => $real_name,
            'group_id' => $group_id,
            'status' => $status
        ];
        if ($password !== '' || $confirm_password !== '') {
            if ($password != $confirm_password) {
                $this->error('两次密码输入不一致');
            }
        }
        if ($id) {
            //编辑
            $data['id'] = $id;
            if ($password !== '') {
                $password_salt = \Util::createRandomString(8);
                $password_md5 = \Util::passwordMd5($password, $password_salt);
                $data['password'] = $password_md5;
                $data['password_salt'] = $password_salt;
            }
            if (false !== model('admin_user')->isUpdate(true)->save($data)) {
                $referer = Url(request()->module() . '/' . request()->controller() . '/lists');
                $this->success('保存成功', request()->param('referer', $referer));
            } else {
                $this->error(model('admin_user')->getError());
            }
        } else {
            //新增
            $data['username'] = $username;
            $password_salt = \Util::createRandomString(8);
            $password_md5 = \Util::passwordMd5($password, $password_salt);
            $data['password'] = $password_md5;
            $data['password_salt'] = $password_salt;
            $data['create_time'] = time();
            $Curd = new Curd($this->model, $data);
            $res = $Curd->create();
            if (!isset($res['id'])) {
                $this->error($Curd->getError());
            }
            $this->success('保存成功', Url('AdminUser/lists'));
        }
    }


}