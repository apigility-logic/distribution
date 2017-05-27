<?php
namespace app\common\model;

use think\Model;

class SnatchOrder extends Base
{
    public static function getLabel()
    {
        $label = [
            'order_sn' => '订单号',
            'user_id' => '会员ID',
            'type' => '订单类型',
            'shipping_fee' => '运费',
            'order_fee' => '订单金额',
            'shipping_status' => '发货状态',
            'pay_status' => '支付状态',
            'order_status' => '订单状态',
            'remark' => '备注',
            'goods' => '订单商品',
            'profile' => UserProfile::getLabel()
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function profile()
    {
        return $this->hasOne('UserProfile', 'user_id', 'user_id')->field($this->getFields('profile'));
    }

    public function goods()
    {
        return $this->hasOne('SnatchGoods', 'id', 'goods_id')->field($this->getFields('goods'));
    }

    public function address()
    {
        return $this->hasOne('SnatchOrderAddress', 'order_id', 'id')->field($this->getFields('address'));
    }
}