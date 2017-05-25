<?php
//配置文件
return [
    'default_return_type'    => 'json',
    'default_ajax_return'    => 'json',
    'datetime_format' => false,
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
                'rounds' => 'snatch_round.id,goods_id,code_num,sale_times,sale_rate,lucky_code,lucky_user_id,announce_time',
                'profile' => 'user_id,avatar,nickname',
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
                'snatch_round' => 'id,goods_id,code_unit,code_num,sale_times,sale_rate,lucky_code,lucky_user_id,announce_time,create_time, status',
                'profile' => 'user_id,avatar,nickname',
                'goods' => 'id,title,images'
            ],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'SnatchRecord' => [
            'title' => '夺宝记录',
            'model' => 'snatch_record',
            'with' => ['profile', 'goods', 'round'],
            'fields' => [
                'round' => 'id,sale_rate,lucky_code,lucky_user_id,announce_time,status',
                'goods' => 'id,title,images',
                'profile' => 'user_id,avatar,nickname',
            ],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'GroupGoods' => [
            'title' => '拼团商品',
            'model' => 'group_goods',
            'with' => [],
            'fields' => [
                'group_goods' => [
                    'lists' => 'id,title,cover,group_num,group_price,goods_price',
                    'read' => '*'
                ]
            ],
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
            'with' => ['profile','goods', 'records'],
            'fields' => [
                'profile' => 'user_id,avatar,nickname',
                'goods' => 'id,title,group_price,goods_price,cover'
            ],
            'requireAuth' => ['create'],
            'action' => ['lists', 'create', 'read']
        ],
        'GroupActionRecord' => [
            'title' => '商品参团记录',
            'model' => 'group_action_record',
            'with' => ['profile'],
            'fields' => [
                'group_action_record' => 'user_id',
                'profile' => 'user_id,avatar,nickname',
            ],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'GroupOrder' => [
            'title' => '拼团订单',
            'model' => 'group_order',
            'with' => ['profile', 'address', 'goods'],
            'fields' => [
                'profile' => 'user_id,avatar,nickname',
            ],
            'requireAuth' => true,
            'action' => ['lists', 'read', 'create']
        ],

        'UserOrderShare' => [
            'title' => '晒单',
            'model' => 'user_order_share',
            'requireAuth' => true,
            'action' => ['lists']
        ],

        'UserShare' => [
            'title' => '晒单内容',
            'model' => 'user_share',
            'with' => ['profile'],
            'fields' => [
                'profile' => 'user_id,avatar,nickname',
            ],
            'requireAuth' => ['create', 'delete'],
            'action' => ['lists', 'read', 'create', 'delete']
        ]
    ]
];