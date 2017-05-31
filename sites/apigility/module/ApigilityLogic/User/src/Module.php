<?php
namespace ApigilityLogic\User;

use Zend\Config\Config;
use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        //$doctrine_config = new Config(include __DIR__ . '/../config/doctrine.config.php');
        //$service_config = new Config(include __DIR__ . '/config/service.config.php');
        $manual_config = new Config(include __DIR__ . '/../config/manual.config.php');
        $oauth2_doctrine_config = new Config(include __DIR__ . '/../config/oauth2.doctrine-orm.global.php');

        $module_config = new Config(include __DIR__ . '/../config/module.config.php');
        //$module_config->merge($doctrine_config);
        //$module_config->merge($service_config);
        $module_config->merge($manual_config);
        $module_config->merge($oauth2_doctrine_config);

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
}
