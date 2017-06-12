<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/12
 * Time: 11:30:23
 */

namespace ApigilityLogic\Distribution\Event;


use ApigilityLogic\Distribution\Doctrine\Entity\Commission;
use Zend\EventManager\Event;
use Zend\ServiceManager\ServiceManager;

/**
 * 分佣项生成事件
 *
 * Class CommissionEvent
 * @package ApigilityLogic\Distribution\Event
 */
class CommissionEvent extends Event
{
    const EVENT_CHAIN_COMMISSION_CREATE_POST      = 'event_chain_commission_create.post';
    const EVENT_TEAM_COMMISSION_CREATE_POST      = 'event_team_commission_create.post';

    private $entity;

    public function __construct($type)
    {
        parent::__construct($type);
    }

    /**
     * @param Commission $entity
     */
    public function setEntity(Commission $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Commission
     */
    public function getEntity()
    {
        return $this->entity;
    }
}