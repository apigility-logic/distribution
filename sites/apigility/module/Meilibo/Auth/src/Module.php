<?php
namespace Meilibo\Auth;

use Zend\Config\Config;
use ZF\Apigility\Provider\ApigilityProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        //$doctrine_config = new Config(include __DIR__ . '/../config/doctrine.config.php');
        //$service_config = new Config(include __DIR__ . '/config/service.config.php');
        $manual_config = new Config(include __DIR__ . '/../config/manual.config.php');

        $module_config = new Config(include __DIR__ . '/../config/module.config.php');
        //$module_config->merge($doctrine_config);
        //$module_config->merge($service_config);
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
        $app      = $e->getApplication();
        $events   = $app->getEventManager();
        $services = $app->getServiceManager();

        $events->attach(
            'authentication',
            function ($e) use ($services) {
                $listener = $services->get('ZF\MvcAuth\Authentication\DefaultAuthenticationListener');
                $adapter = $services->get('Meilibo\Auth\AuthenticationAdapter');
                $listener->attach($adapter);
            },
            1000
        );
    }
}
