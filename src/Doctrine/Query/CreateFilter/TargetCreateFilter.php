<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/6
 * Time: 18:20:05
 */
namespace ApigilityLogic\Distribution\Doctrine\Query\CreateFilter;

use ApigilityLogic\Foundation\Doctrine\Query\CreateFilter\AbstractCreateFilter;
use ZF\Rest\ResourceEvent;

class TargetCreateFilter extends AbstractCreateFilter
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
        $this->setUpdateTime($data);

        return $data;
    }
}