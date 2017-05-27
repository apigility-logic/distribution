<?php
return [
    'router' => [
        'routes' => [
            'apigility-logic\\distribution.rest.doctrine.chain-level' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/chain-level[/:chain_level_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.distributor' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/distributor[/:distributor_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller',
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
            'apigility-logic\\distribution.rest.doctrine.event' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/event[/:event_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.chain-commission' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/chain-commission[/:chain_commission_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Controller',
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
            'apigility-logic\\distribution.rest.doctrine.leader' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/leader[/:leader_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.leader-status' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/leader-status[/:leader_status_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.team-commission' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/team-commission[/:team_commission_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Controller',
                    ],
                ],
            ],
            'apigility-logic\\distribution.rest.doctrine.team-event' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/apigility-logic/distribution/team-event[/:team_event_id]',
                    'defaults' => [
                        'controller' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'zf-versioning' => [
        'uri' => [
            0 => 'apigility-logic\\distribution.rest.doctrine.chain-level',
            1 => 'apigility-logic\\distribution.rest.doctrine.distributor',
            2 => 'apigility-logic\\distribution.rest.doctrine.commission',
            3 => 'apigility-logic\\distribution.rest.doctrine.event',
            4 => 'apigility-logic\\distribution.rest.doctrine.chain-commission',
            5 => 'apigility-logic\\distribution.rest.doctrine.chain-event',
            6 => 'apigility-logic\\distribution.rest.doctrine.leader',
            7 => 'apigility-logic\\distribution.rest.doctrine.leader-status',
            8 => 'apigility-logic\\distribution.rest.doctrine.team-commission',
            9 => 'apigility-logic\\distribution.rest.doctrine.team-event',
        ],
    ],
    'zf-rest' => [
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\ChainLevel::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\ChainLevel\ChainLevelCollection::class,
            'service_name' => 'ChainLevel',
        ],
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Distributor::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorCollection::class,
            'service_name' => 'Distributor',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Commission::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\Commission\CommissionCollection::class,
            'service_name' => 'Commission',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\Event\EventResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.event',
            'route_identifier_name' => 'event_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'event',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Event::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\Event\EventCollection::class,
            'service_name' => 'Event',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\ChainCommission\ChainCommissionResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-commission',
            'route_identifier_name' => 'chain_commission_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'chain_commission',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\ChainCommission::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\ChainCommission\ChainCommissionCollection::class,
            'service_name' => 'ChainCommission',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\ChainEvent::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\ChainEvent\ChainEventCollection::class,
            'service_name' => 'ChainEvent',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\Leader\LeaderResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.leader',
            'route_identifier_name' => 'leader_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'leader',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Leader::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\Leader\LeaderCollection::class,
            'service_name' => 'Leader',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\LeaderStatus\LeaderStatusResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.leader-status',
            'route_identifier_name' => 'leader_status_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'leader_status',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\LeaderStatus::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\LeaderStatus\LeaderStatusCollection::class,
            'service_name' => 'LeaderStatus',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\TeamCommission\TeamCommissionResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.team-commission',
            'route_identifier_name' => 'team_commission_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'team_commission',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\TeamCommission::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\TeamCommission\TeamCommissionCollection::class,
            'service_name' => 'TeamCommission',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Controller' => [
            'listener' => \ApigilityLogic\Distribution\V1\Rest\TeamEvent\TeamEventResource::class,
            'route_name' => 'apigility-logic\\distribution.rest.doctrine.team-event',
            'route_identifier_name' => 'team_event_id',
            'entity_identifier_name' => 'id',
            'collection_name' => 'team_event',
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
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\TeamEvent::class,
            'collection_class' => \ApigilityLogic\Distribution\V1\Rest\TeamEvent\TeamEventCollection::class,
            'service_name' => 'TeamEvent',
        ],
    ],
    'zf-content-negotiation' => [
        'controllers' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Controller' => 'HalJson',
            'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Controller' => 'HalJson',
        ],
        'accept-whitelist' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Controller' => [
                0 => 'application/vnd.apigility-logic\\distribution.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content-type-whitelist' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Controller' => [
                0 => 'application/json',
            ],
            'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Controller' => [
                0 => 'application/json',
            ],
        ],
    ],
    'zf-hal' => [
        'metadata_map' => [
            \ApigilityLogic\Distribution\DoctrineEntity\ChainLevel::class => [
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
            \ApigilityLogic\Distribution\DoctrineEntity\Distributor::class => [
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
            \ApigilityLogic\Distribution\DoctrineEntity\Commission::class => [
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
            \ApigilityLogic\Distribution\DoctrineEntity\Event::class => [
                'route_identifier_name' => 'event_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.event',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\EventHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Event\EventCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.event',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\DoctrineEntity\ChainCommission::class => [
                'route_identifier_name' => 'chain_commission_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-commission',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\ChainCommissionHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\ChainCommission\ChainCommissionCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.chain-commission',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\DoctrineEntity\ChainEvent::class => [
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
            \ApigilityLogic\Distribution\DoctrineEntity\Leader::class => [
                'route_identifier_name' => 'leader_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.leader',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\LeaderHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Leader\LeaderCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.leader',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\DoctrineEntity\LeaderStatus::class => [
                'route_identifier_name' => 'leader_status_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.leader-status',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\LeaderStatusHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\LeaderStatus\LeaderStatusCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.leader-status',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\DoctrineEntity\TeamCommission::class => [
                'route_identifier_name' => 'team_commission_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.team-commission',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\TeamCommissionHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\TeamCommission\TeamCommissionCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.team-commission',
                'is_collection' => true,
            ],
            \ApigilityLogic\Distribution\DoctrineEntity\TeamEvent::class => [
                'route_identifier_name' => 'team_event_id',
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.team-event',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\TeamEventHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\TeamEvent\TeamEventCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'apigility-logic\\distribution.rest.doctrine.team-event',
                'is_collection' => true,
            ],
        ],
    ],
    'zf-apigility' => [
        'doctrine-connected' => [
            \ApigilityLogic\Distribution\V1\Rest\ChainLevel\ChainLevelResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\ChainLevelHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Distributor\DistributorResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\DistributorHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Commission\CommissionResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\CommissionHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Event\EventResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\EventHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\ChainCommission\ChainCommissionResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\ChainCommissionHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\ChainEvent\ChainEventResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\ChainEventHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\Leader\LeaderResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\LeaderHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\LeaderStatus\LeaderStatusResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\LeaderStatusHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\TeamCommission\TeamCommissionResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\TeamCommissionHydrator',
            ],
            \ApigilityLogic\Distribution\V1\Rest\TeamEvent\TeamEventResource::class => [
                'object_manager' => 'doctrine.entitymanager.orm_default',
                'hydrator' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\TeamEventHydrator',
            ],
        ],
    ],
    'doctrine-hydrator' => [
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\ChainLevelHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\ChainLevel::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\DistributorHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Distributor::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\CommissionHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Commission::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\EventHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Event::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\ChainCommissionHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\ChainCommission::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\ChainEventHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\ChainEvent::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\LeaderHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\Leader::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\LeaderStatusHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\LeaderStatus::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\TeamCommissionHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\TeamCommission::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\TeamEventHydrator' => [
            'entity_class' => \ApigilityLogic\Distribution\DoctrineEntity\TeamEvent::class,
            'object_manager' => 'doctrine.entitymanager.orm_default',
            'by_value' => false,
            'strategies' => [],
            'use_generated_hydrator' => true,
        ],
    ],
    'zf-content-validation' => [
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Distributor\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\ChainEvent\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Validator',
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Controller' => [
            'input_filter' => 'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Validator',
        ],
    ],
    'input_filter_specs' => [
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
                'name' => 'create_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'update_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Commission\\Validator' => [
            0 => [
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
            1 => [
                'name' => 'percent',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'create_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Event\\Validator' => [
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
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\ChainCommission\\Validator' => [
            0 => [
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
            1 => [
                'name' => 'percent',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'create_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
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
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\Leader\\Validator' => [
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
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'update_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\LeaderStatus\\Validator' => [
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
                'name' => 'percent',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'create_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            3 => [
                'name' => 'update_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamCommission\\Validator' => [
            0 => [
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
            1 => [
                'name' => 'percent',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
            2 => [
                'name' => 'create_time',
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
        'ApigilityLogic\\Distribution\\V1\\Rest\\TeamEvent\\Validator' => [
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
                'required' => true,
                'filters' => [],
                'validators' => [],
            ],
        ],
    ],
    'zf-mvc-auth' => [
        'authorization' => [
            'ApigilityLogic\\Distribution\\V1\\Rest\\ChainLevel\\Controller' => [
                'collection' => [
                    'GET' => true,
                    'POST' => false,
                    'PUT' => false,
                    'PATCH' => false,
                    'DELETE' => false,
                ],
                'entity' => [
                    'GET' => false,
                    'POST' => false,
                    'PUT' => false,
                    'PATCH' => false,
                    'DELETE' => false,
                ],
            ],
        ],
    ],
];
