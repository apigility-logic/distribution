<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/1
 * Time: 14:43:10
 */
namespace ApigilityLogic\Distribution\Doctrine\Query\CreateFilter;

use ApigilityLogic\Foundation\Doctrine\Query\CreateFilter\AbstractCreateFilter;
use Zend\Math\Rand;
use ZF\Rest\ResourceEvent;

class DistributorCreateFilter extends AbstractCreateFilter
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

        if (!isset($data->code)) $data->code = Rand::getString(8, 'abcdefghijklmnopqrstuvwxyz');

        //var_dump($data); exit();

        return $data;
    }
}