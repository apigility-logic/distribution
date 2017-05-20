<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\admin\controller;


use app\common\Media;

class UserProfile extends Base
{
    public $model = 'user_profile';

    protected function dataTable()
    {
        return [
            'fields' => ['id', 'avatar', 'nickname', 'create_time'],
            'extends' => [
                'avatar' => function ($data) {
                    return '<img src="' . Media::getUrl(Media::thumb($data['avatar'], 200, 200)) . '" width="40" height="40"/>';
                },
            ]
        ];
    }

    protected function form()
    {
        return [
            //field, type
            ['avatar', \FormHelper::TYPE_IMAGE],
            ['nickname', \FormHelper::TYPE_TEXT],
        ];
    }


}