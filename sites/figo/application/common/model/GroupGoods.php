<?php
namespace app\common\model;

use think\Model;

class GroupGoods extends SoftDeleteBase
{
    public static function getLabel()
    {
        $label = [
            'title' => '商品标题',
            'images' => '商品图片',
            'content' => '商品详情',
            'group_num' => '拼团人数',
            'group_price' => '拼团价',
            'goods_price' => '商品价',
            'group_free_type' => '免单模式',
            'is_recommend' => '是否推荐',
            'is_sale' => '上下架',
        ];
        return array_merge(parent::getLabel(), $label);
    }
}