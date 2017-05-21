<?php
namespace app\common\model;

use think\Model;

class GroupOrderAddress extends Base
{
    public static function getLabel()
    {
        $label = [
            'order_id' => '订单ID',
            'consignee' => '收件人',
            'mobile' => '手机号码',
            'province_id' => '省ID',
            'city_id' => '市ID',
            'area_id' => '区ID',
            'address' => '收货地址',
        ];
        return array_merge(parent::getLabel(), $label);
    }
    
}