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
            'apigility-logic\\distribution.rest.doctrine.chain-event' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/chain-event[/:chain_event_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.commission' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/commission[/:commission_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.distribution-customer' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/distribution-customer[/:distribution_customer_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'apigility-logic\\distribution.rest.doctrine.distributor',
            1 => 'apigility-logic\\distribution.rest.doctrine.chain-level',
            2 => 'apigility-logic\\distribution.rest.doctrine.chain-event',
            3 => 'apigility-logic\\distribution.rest.doctrine.commission',
            4 => 'apigility-logic\\distribution.rest.doctrine.distribution-customer',
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
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\ChainEvent\ChainEventResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-event',
            'route_identifier_name' => 'chain_event_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'chain_event',
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
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\ChainEvent::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\ChainEvent\ChainEventCollection::class,
            'service_name' => 'ChainEvent',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\Commission\CommissionResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.commission',
            'route_identifier_name' => 'commission_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'commission',
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
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\Commission::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\Commission\CommissionCollection::class,
            'service_name' => 'Commission',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\DistributionCustomer\DistributionCustomerResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.distribution-customer',
            'route_identifier_name' => 'distribution_customer_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'distribution_customer',
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
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\DistributionCustomer::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\DistributionCustomer\DistributionCustomerCollection::class,
            'service_name' => 'DistributionCustomer',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Controller' => 'HalJson',
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
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Controller' => [
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
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/json',
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
                'max_depth' => 1
            ],
            \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.distributor',
                'is_collection' => true,
                'max_depth' => 1
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
            \ApigilityLogic\Distribution\Doctrine\Entity\ChainEvent::class => [
                'route_identifier_name' => 'chain_event_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-event',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\ChainEventHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\ChainEvent\ChainEventCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-event',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\Doctrine\Entity\Commission::class => [
                'route_identifier_name' => 'commission_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.commission',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\CommissionHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Commission\CommissionCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.commission',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\Doctrine\Entity\DistributionCustomer::class => [
                'route_identifier_name' => 'distribution_customer_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.distribution-customer',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\DistributionCustomerHydrator',
                'max_depth' => 1
            ],
            \ApigilityLogic\Distribution\V1\Rest\DistributionCustomer\DistributionCustomerCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.distribution-customer',
                'is_collection' => true,
                'max_depth' => 1
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
            \ApigilityLogic\Distribution\V1\Rest\ChainEvent\ChainEventResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\ChainEventHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Commission\CommissionResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\CommissionHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\DistributionCustomer\DistributionCustomerResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\DistributionCustomerHydrator',
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
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\ChainEventHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\ChainEvent::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => true,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\CommissionHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\Commission::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => true,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\DistributionCustomerHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\Doctrine\Entity\DistributionCustomer::class,
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
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Validator',
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
            2 => [
                'required' => false,
                'validators' => [],
                'filters' => [],
                'name' => 'target',
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Validator' => [
            0 => [
                'name' => 'amount',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            1 => [
                'name' => 'base_percent',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'create_time',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Validator' => [
            0 => [
                'name' => 'amount',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            1 => [
                'name' => 'create_time',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'title',
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
                            'max' => 200,
                        ],
                    ],
                ],
            ],
            3 => [
                'name' => 'percent',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\DistributionCustomer\\Validator' => [
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
                'name' => 'create_time',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'update_time',
                'required' => false,
                'filters' => [],
                'validators' => [],
            ],
        ],
    ],
];
