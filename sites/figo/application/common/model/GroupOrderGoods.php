<?php
namespace app\common\model;

use think\Model;

class GroupOrderGoods extends Base
{
    public static function getLabel()
    {
        $label = [
            'goods_id' => '商品ID',
            'goods_title' => '商品标题',
            'goods_image' => '商品图片',
            'goods_price' => '商品价格',
            'real_price' => '实际售价',
        ];
        return array_merge(parent::getLabel(), $label);
    }

}