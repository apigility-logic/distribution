<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 17:02:01
 */

namespace ApigilityLogic\Distribution\Service;

use Zend\ServiceManager\ServiceManager;

class CommissionServiceFactory
{
    public function __invoke(ServiceManager $services)
    {
        return new CommissionService($services);
    }
}