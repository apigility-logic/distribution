<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/7
 * Time: 15:52:07
 */

namespace ApigilityLogic\Distribution\Doctrine\Query\CreateFilter;

use ApigilityLogic\Foundation\Doctrine\Query\CreateFilter\AbstractCreateFilter;
use ZF\Rest\ResourceEvent;

class EventCreateFilter extends AbstractCreateFilter
{
    /**
     * @param ResourceEvent $event
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data)
    {
        $this->setCreateTime($data);

        return $data;
    }
}