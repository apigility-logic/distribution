<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/20
 * Time: 17:37:32
 */

namespace ApigilityLogic\Distribution\Doctrine\Query\CreateFilter;

use Zend\ServiceManager\ServiceManager;

class EventCreateFilterFactory
{
    public function __invoke(ServiceManager $services)
    {
        return new EventCreateFilter($services);
    }
}