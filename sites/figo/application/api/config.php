<?php
//配置文件
return [
    'default_return_type'    => 'json',
    'default_ajax_return'    => 'json',

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
        'SnatchRound' => [
            'title' => '夺宝轮次',
            'model' => 'snatch_round',
            'with' => ['goods'],
            'fields' => [
                'snatch_round' => '*',
                'goods' => '*',
            ],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'SnatchRecord' => [
            'title' => '夺宝记录',
            'model' => 'snatch_record',
            'with' => ['profile', 'goods', 'round'],
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
            'with' => ['profile'],
            'requireAuth' => false,
            'action' => ['lists']
        ],
        'GroupActionRecord' => [
            'title' => '商品参团记录',
            'model' => 'group_action_record',
            'with' => ['profile'],
            'requireAuth' => false,
            'action' => ['lists']
        ]
    ]
];