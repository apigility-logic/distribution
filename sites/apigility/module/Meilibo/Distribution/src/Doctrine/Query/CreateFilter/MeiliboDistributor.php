<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/1
 * Time: 14:43:10
 */
namespace Meilibo\Distribution\Doctrine\Query\CreateFilter;

use Zend\Math\Rand;
use ZF\Apigility\Doctrine\Server\Query\CreateFilter\AbstractCreateFilter;
use ZF\Rest\ResourceEvent;

class MeiliboDistributor extends AbstractCreateFilter
{
    /**
     * @param ResourceEvent $event
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data)
    {
        if (!isset($data->create_time)) $data->create_time = time();
        if (!isset($data->update_time)) $data->update_time = time();

        if (!isset($data->code)) $data->code = Rand::getString(8, 'abcdefghijklmnopqrstuvwxyz');

        //var_dump($data); exit();

        return $data;
    }
}