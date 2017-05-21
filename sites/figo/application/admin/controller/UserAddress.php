<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class UserAddress extends Base
{
    public $model = 'user_address';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'profile.nickname', 'consignee', 'mobile', 'address', 'is_default', 'create_time'],
            'with' => 'profile',
            'extends' => [
                'address' => function ($data) {
                    $province = model('district')->title($data['province_id']);
                    $city = model('district')->title($data['city_id']);
                    $area = model('district')->title($data['area_id']);
                    return $province . $city . $area . $data['street'];
                },
                'is_default' => function ($data) {
                    return map([0 => '<span class="label label-info">否</span>', 1 => '<span class="label label-success">是</span>'], $data['is_default']);
                }
            ]
        ];
    }

    protected function form()
    {
        if($this->data){
            $this->data['district'] = "{$this->data['province_id']},{$this->data['city_id']},{$this->data['area_id']}";
        }
        return [
            //field, type
            ['consignee', \FormHelper::TYPE_TEXT],
            ['mobile', \FormHelper::TYPE_TEXT],
            ['district', \FormHelper::TYPE_DISTRICT, [
                'ganged' => ['province', 'city', 'area']
            ]],
            ['street', \FormHelper::TYPE_TEXT],
            ['is_default', \FormHelper::TYPE_RADIO, [
                'options' => [0 => '否', 1 => '是'],
                'default' => 1,
            ]],
        ];
    }


}