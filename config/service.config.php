<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2016/11/16
 * Time: 14:52
 */

use ApigilityLogic\Distribution\Service;

return [
    'service_manager' => array(
        'factories' => array(
            Service\CommissionService::class => Service\CommissionServiceFactory::class
        ),
    )
];