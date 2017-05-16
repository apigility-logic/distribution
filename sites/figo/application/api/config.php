<?php
//配置文件
return [
    'default_return_type'    => 'json',
    'default_ajax_return'    => 'json',

    'access' => [
        'UserAddress' => [
            'model' => 'user_address',
            'requireAuth' => true,
            'action' => ['create', 'update', 'read', 'lists']
        ],
    ]
];