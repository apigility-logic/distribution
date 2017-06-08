<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 16:57:43
 */

namespace ApigilityLogic\Distribution\Service;


use ApigilityLogic\Distribution\Doctrine\Entity\Event;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;

class CommissionService
{
    /**
     * @var EntityManager
     */
    private $em;

    function __construct(ServiceManager $serviceManager)
    {
        $this->em = $serviceManager->get('Doctrine\ORM\EntityManager');
    }

    /**
     * 执行一次分佣计算
     * @param $type
     * @return null
     */
    public function perform(Event $event_entity)
    {
        return null;
    }
}