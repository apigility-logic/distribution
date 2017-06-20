<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2016/11/30
 * Time: 16:32
 */
return [
    'zf-apigility-doctrine-query-create-filter' => [
        'factories' => [
            \ApigilityLogic\Distribution\Doctrine\Query\CreateFilter\DistributorCreateFilter::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \ApigilityLogic\Distribution\Doctrine\Query\CreateFilter\TargetCreateFilter::class => \Zend\ServiceManager\Factory\InvokableFactory::class,
            \ApigilityLogic\Distribution\Doctrine\Query\CreateFilter\EventCreateFilter::class => \ApigilityLogic\Distribution\Doctrine\Query\CreateFilter\EventCreateFilterFactory::class,
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorResource::class => [
                'query_create_filter' => \ApigilityLogic\Distribution\Doctrine\Query\CreateFilter\DistributorCreateFilter::class,
            ],
            \ApigilityLogic\Distribution\V1\Rest\ChainEvent\ChainEventResource::class => [
                'query_create_filter' => \ApigilityLogic\Distribution\Doctrine\Query\CreateFilter\EventCreateFilter::class,
            ],
        ],
    ],
];