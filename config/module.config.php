<?php
return [
    'router' => [
        'routes' => [
            'apigility-logic\\distribution.rest.doctrine.distributor' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/distributor[/:distributor_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.chain-level' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/chain-level[/:chain_level_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'apigility-logic\\distribution.rest.doctrine.distributor',
            1 => 'apigility-logic\\distribution.rest.doctrine.chain-level',
        ],
    ],
    'zf-rest' => [
        'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.distributor',
            'route_identifier_name' => 'distributor_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'distributor',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\Distributor::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorCollection::class,
            'service_name' => 'Distributor',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\ChainLevel\ChainLevelResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-level',
            'route_identifier_name' => 'chain_level_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'chain_level',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\ChainLevel::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\ChainLevel\ChainLevelCollection::class,
            'service_name' => 'ChainLevel',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \ApigilityLogic\Distribution\Doctrine\Entity\Distributor::class => [
                'route_identifier_name' => 'distributor_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.distributor',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\DistributorHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.distributor',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\Doctrine\Entity\ChainLevel::class => [
                'route_identifier_name' => 'chain_level_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-level',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\ChainLevelHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\ChainLevel\ChainLevelCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-level',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\DistributorHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\ChainLevel\ChainLevelResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\ChainLevelHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\DistributorHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\Distributor::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => true,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\ChainLevelHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\ChainLevel::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => true,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
    ],
    'zf-content-validation' => [
        'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Validator' => [
            0 => [
                'name' => 'name',
                'required' => false,
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 50,
                        ],
                    ],
                ],
            ],
            1 => [
                'name' => 'code',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StringTrim::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                ],
                'validators' => [
                    0 => [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 50,
                        ],
                    ],
                ],
            ],
            2 => [
                'name' => 'create_time',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
            3 => [
                'name' => 'update_time',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Validator' => [
            0 => [
                'name' => 'level',
                'required' => true,
                'filters' => [
                    0 => [
                        'name' => \Zend\Filter\StripTags::class,
                    ],
                    1 => [
                        'name' => \Zend\Filter\Digits::class,
                    ],
                ],
                'validators' => [],
            ],
            1 => [
                'name' => 'percent',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
    ],
];
