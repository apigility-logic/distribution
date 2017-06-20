<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/20
 * Time: 17:15:55
 */

namespace ApigilityLogic\Distribution\Service;

use Zend\ServiceManager\ServiceManager;

class SettingServiceFactory
{
    public function __invoke(ServiceManager $services)
    {
        return new SettingService($services);
    }
}