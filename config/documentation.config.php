<?php
return [
    'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
        'description' => '链级分佣模式下，须要配置向上分佣的链级，以及各级的分佣百分比。

- `level` 字段代表向上第几级。
- `percent` 字段代表该链级的分佣百分比。',
        'collection' => [
            'description' => '',
            'GET' => [
                'description' => '',
                'response' => '',
            ],
        ],
        'entity' => [
            'description' => '链级',
            'GET' => [
                'description' => '',
            ],
        ],
    ],
    'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
        'description' => '分销者资源，用于管理分销链。每一个`Distributor`资源是一个节点，每一个节点都可以有一个上游节点。',
    ],
];
