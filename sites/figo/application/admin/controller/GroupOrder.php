<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


use app\common\model\GroupGoods;

class GroupOrder extends Base
{
    public $model = 'group_order';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'order_sn', 'profile.nickname|购买会员', 'order_fee', 'shipping_status', 'pay_status', 'order_status', 'create_time'],
            'extends' => [
                'shipping_status' => function ($data) {
                    return map([
                        0 => '<span class="label label-warning">未发货</span>',
                        1 => '<span class="label label-info">已发货</span>',
                        2 => '<span class="label label-success">已收货</span>',
                    ], $data['shipping_status']);
                },
                'pay_status' => function ($data) {
                    return map([
                        0 => '<span class="label label-warning">未支付</span>',
                        1 => '<span class="label label-success">已支付</span>',
                        2 => '<span class="label label-info">全额退款</span>',
                    ], $data['pay_status']);
                },
                'order_status' => function ($data) {
                    return map([
                        0 => '<span class="label label-warning">未确认</span>',
                        1 => '<span class="label label-info">已确认</span>',
                        2 => '<span class="label label-success">已完成</span>',
                        3 => '<span class="label label-default">已取消</span>',
                    ], $data['order_status']);
                }
            ]
        ];
    }

    protected function form()
    {
        return [
            ['order_sn', \FormHelper::TYPE_STATIC],
            ['order_fee', \FormHelper::TYPE_STATIC],
            ['goods', \FormHelper::TYPE_LIST, [
                'fields' => ['goods_id|商品ID', 'goods_title|商品标题', 'goods_image|商品图片', 'real_price|售价'],
                'extends' => [],
                'label' => GroupGoods::getLabel(),
            ]],
            ['shipping_status', \FormHelper::TYPE_RADIO, [
                'options' => [
                    0 => '未发货',
                    1 => '已发货',
                    2 => '已收货'
                ]
            ]],
            ['pay_status', \FormHelper::TYPE_RADIO, [
                'options' => [
                    0 => '未支付',
                    1 => '已支付',
                    2 => '全额退款'
                ]
            ]],
            ['order_status', \FormHelper::TYPE_RADIO, [
                'options' => [
                    0 => '未确认',
                    1 => '已确认',
                    2 => '已完成',
                    3 => '已取消'
                ]
            ]],
            ['create_time', \FormHelper::TYPE_STATIC]
        ];
    }


}