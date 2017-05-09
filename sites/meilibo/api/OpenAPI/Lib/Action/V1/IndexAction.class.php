<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/6
 * Time: 上午10:46.
 */
class IndexAction extends BaseAction
{
    protected $default_msg = array(
        'categories' => array(
            'user_api' => array(
                'ref' => '用户相关API',
                'href' => 'v1/user',
                'type' => 'application/json; charset=utf-8',
            ),
            'anchor_api' => array(
                'ref' => '主播相关API',
                'href' => 'v1/anchor',
                'type' => 'application/json; charset=utf-8',
            ),
            'room_api' => array(
                'ref' => '直播间相关API',
                'href' => 'v1/room',
                'type' => 'application/json; charset=utf-8',
            ),
            'gift_api' => array(
                'ref' => '礼物相关API',
                'href' => 'v1/gift',
                'type' => 'application/json; charset=utf-8',
            ),
            'avatar_api' => array(
                'ref' => '头像相关API',
                'href' => 'v1/avatar',
                'type' => 'application/json; charset=utf-8'
            ),
            'payment_api' => array(
                'ref' => '支付相关API',
                'href' => 'v1/payment',
                'type' => 'application/json; charset=utf-8',
            ),
            'third_party_login_api' => array(
                'ref' => '第三方登录API',
                'href' => 'v1/auth',
                'type' => 'application/json; charset=utf-8',
            ),
            'sms_api' => array(
                'ref' => '短信相关API',
                'href' => 'v1/sms',
                'type' => 'application/json; charset=utf8',
            ),
        ),
        'ref' => '目前所支持的API列表',
    );
}
