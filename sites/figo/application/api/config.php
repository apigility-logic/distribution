<?php
//配置文件
return [
    'default_return_type'    => 'json',
    'default_ajax_return'    => 'json',
    'session'                => [
        'id'             => '',
        'var_session_id' => '',
        'prefix'         => 'think',
        'type'           => '',
        'auto_start'     => false,
    ],

    'access' => [
        'UserProfile' => [
            'title' => '会员信息',
            'model' => 'user_profile',
            'fields' => [
                'user_profile' => '*',
            ],
            'requireAuth' => true,
            'action' => ['update', 'read']
        ],
        'UserAddress' => [
            'title' => '会员收货地址',
            'model' => 'user_address',
            'fields' => [
                'user_address' => '*',
            ],
            'requireAuth' => true,
            'action' => ['create', 'update', 'read', 'lists', 'delete']
        ],
        'SnatchGoods' => [
            'title' => '夺宝商品',
            'model' => 'snatch_goods',
            'with' => ['rounds'],
            'fields' => [
                'rounds' => 'snatch_round.id,goods_id,lucky_code,lucky_user_id,avatar,nickname,announce_time',
                'goods' => '*',
            ],
            'requireAuth' => false,
            'action' => ['lists', 'read']
        ],
        'SnatchRound' => [
            'title' => '夺宝轮次',
            'model' => 'snatch_round',
            'with' => ['goods','profile'],
            'fields' => [
                'snatch_round' => 'id,goods_id,code_unit,code_num,sale_times,lucky_code,lucky_user_id,create_time, status',
                'profile' => 'user_id,avatar,nickname'
            ],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'SnatchRecord' => [
            'title' => '夺宝记录',
            'model' => 'snatch_record',
            'with' => ['profile', 'goods', 'round'],
            'fields' => [
                'profile' => 'user_id,avatar,nickname',
            ],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'GroupGoods' => [
            'title' => '拼团商品',
            'model' => 'group_goods',
            'with' => [],
            'requireAuth' => false,
            'action' => ['lists', 'read']
        ],
        'GroupCategory' => [
            'title' => '拼团分类',
            'model' => 'group_category',
            'with' => [],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'GroupAction' => [
            'title' => '商品开团',
            'model' => 'group_action',
            'with' => ['profile','goods'],
            'fields' => [
                'profile' => 'user_id,avatar,nickname',
            ],
            'requireAuth' => ['create'],
            'action' => ['lists', 'create', 'read']
        ],
        'GroupActionRecord' => [
            'title' => '商品参团记录',
            'model' => 'group_action_record',
            'with' => ['profile'],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'GroupOrder' => [
            'title' => '拼团订单',
            'model' => 'group_order',
            'with' => ['profile', 'address', 'goods'],
            'requireAuth' => true,
            'action' => ['lists', 'read', 'create']
        ]
    ]
];