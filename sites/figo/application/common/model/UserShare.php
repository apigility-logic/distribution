<?php
namespace app\common\model;

use think\Model;

class UserShare extends SoftDeleteBase
{
    public static function getLabel()
    {
        $label = [
            'type' => '类型',
            'target_id' => '',
            'goods_id' => '商品ID',
            'content' => '分享内容',
            'images' => '分享图片',
            'snatchGoods' => SnatchGoods::getLabel(),
            'groupGoods' => GroupGoods::getLabel()
        ];
        return array_merge(parent::getLabel(), $label);
    }

    public function snatchGoods()
    {
        return $this->hasOne('snatch_goods', 'id', 'goods_id')->field($this->getFields('snatchGoods'));
    }

    public function snatchRound()
    {
        return $this->hasOne('snatch_round', 'id', 'target_id')->field($this->getFields('snatchRound'));
    }

    public function groupGoods()
    {
        return $this->hasOne('group_goods', 'id', 'goods_id')->field($this->getFields('goodsGoods'));
    }

}