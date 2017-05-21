<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\api\controller;

use app\common\Code;
use app\common\Request;

class GroupOrder extends Base
{
    public function create()
    {
        $user_id = Request::instance()->getUserId();
        if (empty($user_id)) {
            return $this->error(Code::APP_TOKEN_ERROR);
        }
        //检查地址
        $address_id = request()->param('address_id');
        $address = model('UserAddress')->where(['user_id' => $user_id, 'id' => $address_id])->find();
        if (empty($address)) {
            return $this->error(Code::USER_ADDRESS_NOT_EXIST);
        }
        //检查商品
        $goods = $_POST['goods'];
        $goods_data = [];
        foreach ($goods as $row) {
            $data = model('GroupGoods')->get($row['id']);
            if (empty($data)) {
                return $this->error(Code::GOODS_NOT_EXIST);
            }
            if ($data['is_sale'] != 1) {
                return $this->error(Code::GOODS_NOT_SALE);
            }
            $data['buy_num'] = $row['number'];
            $goods_data[] = $data;
        }
        //检查拼团
        $type = request()->param('type');
        $group_action = [];
        if ($type == 2) {
            //商品数只有一个
            if (count($goods_data) != 1 && $goods[0]['number'] != 1) {
                return $this->error();
            }
            $group_action_id = request()->param('group_action_id');
            $group_action = model('group_action')->get($group_action_id);
            if (empty($group_action)) {
                //不存在
                return $this->error(Code::GROUP_ACTION_NOT_EXIST);
            }
            if ($group_action['end_time'] < time()) {
                //已结束
                return $this->error(Code::GROUP_ACTION_END);
            }
            if ($group_action['status'] == 2) {
                return $this->error(Code::GROUP_ACTION_FULL);
            }
            $record_count = model('group_action_record')
                ->join('group_order','group_action_record.order_id = group_order.id')
                ->where('group_action_id', $group_action_id)
                ->where('order_status', 'in', [0,1,2])
                ->count();
            if ($record_count >= $group_action['group_num']) {
                return $this->error(Code::GROUP_ACTION_FULL);
            }
        }

        $id = model('GroupOrder', 'logic')->create([
            'user_id' => $user_id,
            'type' => $type,    //订单类型
            'group_action' => $group_action,    //拼团数据
            'goods' => $goods_data, //商品数据
            'address' => $address   //收货地址
        ]);

        return ['id' => $id];
    }

}