<?php

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/21
 * Time: 下午3:54
 */
namespace app\api\logic;

use app\common\Request;

class GroupOrder
{
    //$data
    //'type' => $type,    //订单类型
    //'group_action' => $group_action,  //拼团数据
    //'goods' => $goods_data, //商品数据
    //'address' => $address   //收货地址

    public function create($data)
    {
        $order_data = [
            'user_id' => $data['user_id'],
            'order_sn' => $this->getOrderSn(),
            'order_fee' => $this->_fee($data),
            'type' => $data['type'],
            'remark' => request()->param('remark', ''),
            'create_time' => time(),
        ];
        $order = model('group_order')->create($order_data);
        $order_id = $order->id;
        $data['order'] = $order;
        $this->_goods($data);
        $this->_address($data);
        if($data['type'] == 2) {
            $this->_group_action($data);
        }
        return $order_id;
    }

    public function getOrderSn(){
        return '20' . date('ymdHis') . rand(1000, 9999);
    }

    protected function _fee($data)
    {
        $fee = 0;
        foreach ($data['goods'] as $goods) {
            $fee += $this->getPrice($data['type'], $goods) * $goods['buy_num'];
        }
        return $fee;
    }

    public function getPrice($type, $goods){
        if ($type == 2) {
            return $goods['group_price'];
        } else {
            return $goods['goods_price'];
        }
    }

    protected function _goods($data)
    {
        $goods_data = [];
        foreach($data['goods'] as $goods) {
            $goods_images = explode(',', $goods['images']);
            $price = $this->getPrice($data['type'], $goods);
            $goods_data[] = [
                'order_id' => $data['order']['id'],
                'goods_id' => $goods['id'],
                'goods_title' => $goods['title'],
                'goods_image' => $goods_images ? $goods_images[0] : '',
                'goods_price' => $price,
                'real_price' => $price,
                'number' => $goods['buy_num'],
                'create_time' => time()
            ];
        }
        return model('group_order_goods')->saveAll($goods_data);
    }

    protected function _group_action($data){
        $record = [
            'group_action_id' => $data['group_action']['id'],
            'user_id' => $data['user_id'],
            'goods_id' => $data['goods'][0]['id'],
            'order_id' => $data['order']['id'],
            'create_time' => time()
        ];
        return model('group_action_record')->create($record);
    }

    protected function _address($data)
    {
        $province = model('district')->title($data['address']['province_id']);
        $city = model('district')->title($data['address']['city_id']);
        $area = model('district')->title($data['address']['area_id']);
        $address_data = [
            'order_id' => $data['order']['id'],
            'consignee' => $data['address']['consignee'],
            'mobile' => $data['address']['mobile'],
            'province_id' => $data['address']['province_id'],
            'city_id' => $data['address']['city_id'],
            'area_id' => $data['address']['area_id'],
            'address' => $province . $city. $area . $data['address']['street'],
            'create_time' => time()
        ];
        return model('group_order_address')->create($address_data);
    }
}