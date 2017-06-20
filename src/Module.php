<?php
namespace ApigilityLogic\Distribution;

use ApigilityLogic\Distribution\Listener\CommissionListener;
use ApigilityLogic\Distribution\Listener\DistributionCustomerListener;
use ApigilityLogic\Distribution\Listener\EventEntityListener;
use ApigilityLogic\Distribution\Listener\LedgerListener;
use ApigilityLogic\Distribution\Service\CommissionService;
use ZF\Apigility\Provider\ApigilityProviderInterface;
use Zend\Config\Config;
use Zend\Mvc\MvcEvent;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        $doctrine_config = new Config(include __DIR__ . '/../config/doctrine.config.php');
        $service_config = new Config(include __DIR__ . '/../config/service.config.php');
        $manual_config = new Config(include __DIR__ . '/../config/manual.config.php');

        $module_config = new Config(include __DIR__ . '/../config/module.config.php');
        $module_config->merge($doctrine_config);
        $module_config->merge($service_config);
        $module_config->merge($manual_config);

        return $module_config;
    }

    public function getAutoloaderConfig()
    {
        return [
            'ZF\Apigility\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }

    public function onBootstrap(MvcEvent $e)
    {
        $app      = $e->getTarget();
        $services = $app->getServiceManager();
        $events   = $app->getEventManager();

        $sharedEvents = $events->getSharedManager();

        $distribution_customer_listener = new DistributionCustomerListener($services);
        $distribution_customer_listener->attachShared($sharedEvents);

        $event_entity_listener = new EventEntityListener($services);
        $event_entity_listener->attachShared($sharedEvents);

        $commission_listener = new CommissionListener();
        $commission_listener->attach($event_entity_listener->getEventManager());

        $ledger_listener = new LedgerListener($services);
        $ledger_listener->attach($services->get(CommissionService::class)->getEventManager());
    }
}
