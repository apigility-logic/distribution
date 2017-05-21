<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


class GroupGoods extends Base
{
    public $model = 'group_goods';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'title','goods_price', 'group_price', 'group_num', 'group_free_type', 'is_recommend', 'is_sale'],
            'extends' => [
                'group_free_type' => function ($data) {
                    return map([
                        1 => '团长免单',
                        2 => '随机免单',
                    ], $data['group_free_type']);
                },
                'is_recommend' => function ($data) {
                    return map([
                        0 => '<span class="label label-warning">否</span>',
                        1 => '<span class="label label-info">是</span>',
                    ], $data['is_sale']);
                },
                'is_sale' => function ($data) {
                    return map([
                        0 => '<span class="label label-warning">下架</span>',
                        1 => '<span class="label label-success">上架</span>',
                    ], $data['is_sale']);
                }
            ]
        ];
    }

    protected function form()
    {
        return [
            //field, type
            ['title', \FormHelper::TYPE_TEXT],
            ['images', \FormHelper::TYPE_IMAGES],
            ['group_num', \FormHelper::TYPE_TEXT],
            ['group_free_type', \FormHelper::TYPE_RADIO, [
                'options' => [1 => '团长免单', 2 => '随机免单'],
                'default' => 1,
            ]],
            ['group_price', \FormHelper::TYPE_TEXT],
            ['goods_price', \FormHelper::TYPE_TEXT],
            ['content', \FormHelper::TYPE_EDITOR, [
                'attr' => [
                    'style' => "height: 500px;"
                ]
            ]],
            ['is_recommend', \FormHelper::TYPE_RADIO, [
                'options' => [0 => '否', 1 => '是'],
                'default' => 1,
            ]],
            ['is_sale', \FormHelper::TYPE_RADIO, [
                'options' => [0 => '下架', 1 => '上架'],
                'default' => 1,
            ]],
        ];
    }


}