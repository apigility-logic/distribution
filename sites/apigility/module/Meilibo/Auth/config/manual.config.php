<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2016/11/30
 * Time: 16:32
 */
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'meilibo-auth' => [
        'api-host' => 'zhibo.mimilove520.com'
    ],
    'zf-mvc-auth' => [
        'authentication' => [
            'adapters' => [
                'meilibo' => [
                    'adapter' => \Meilibo\Auth\AuthenticationAdapter::class,
                ],
            ],
        ],
    ],
    'service_manager'    => [
        'factories'    => [
            \Meilibo\Auth\AuthenticationAdapter::class => InvokableFactory::class,
        ],
    ],
];