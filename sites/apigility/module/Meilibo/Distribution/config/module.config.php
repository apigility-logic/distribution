<?php
return [
    'router' => [
        'routes' => [
            'meilibo\\distribution.rest.doctrine.meilibo-distributor' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/meilibo/distribution/meilibo-distributor[/:meilibo_distributor_id]',
                    'defaults' => [
                        'controller' => 'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'meilibo\\distribution.rest.doctrine.meilibo-distributor',
        ],
    ],
    'zf-rest' => [
        'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Controller' => [
            'listener' => \Meilibo\Distribution\V1\Rest\MeiliboDistributor\MeiliboDistributorResource::class,
            'route_name' => 'meilibo\\distribution.rest.doctrine.meilibo-distributor',
            'route_identifier_name' => 'meilibo_distributor_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'meilibo_distributor',
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
            'entity_class' => \Meilibo\Distribution\Doctrine\Entity\MeiliboDistributor::class,
            'collection_class' => \Meilibo\Distribution\V1\Rest\MeiliboDistributor\MeiliboDistributorCollection::class,
            'service_name' => 'MeiliboDistributor',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Controller' => [
                0 => 'application/vnd.meilibo\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \Meilibo\Distribution\Doctrine\Entity\MeiliboDistributor::class => [
                'route_identifier_name' => 'meilibo_distributor_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'meilibo\\distribution.rest.doctrine.meilibo-distributor',
                'hydrator' => 'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\MeiliboDistributorHydrator',
            ],
            \Meilibo\Distribution\V1\Rest\MeiliboDistributor\MeiliboDistributorCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'meilibo\\distribution.rest.doctrine.meilibo-distributor',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \Meilibo\Distribution\V1\Rest\MeiliboDistributor\MeiliboDistributorResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\MeiliboDistributorHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\MeiliboDistributorHydrator' => [
            'entity_class' => \Meilibo\Distribution\Doctrine\Entity\MeiliboDistributor::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
    ],
    'zf-content-validation' => [
        'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Controller' => [
            'input_filter' => 'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'Meilibo\\Distribution\\V1\\Rest\\MeiliboDistributor\\Validator' => [
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
            4 => [
                'name' => 'meilibo_user_id',
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
        ],
    ],
];
