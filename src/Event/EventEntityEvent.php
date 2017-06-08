<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 14:24:57
 */

namespace ApigilityLogic\Distribution\Event;

use Zend\EventManager\Event;
use ApigilityLogic\Distribution\Doctrine\Entity\Event as EventEntity;
use Zend\ServiceManager\ServiceManager;

class EventEntityEvent extends Event
{
    const EVENT_EVENT_ENTITY_CREATE_POST      = 'event_entity_create.post';

    private $entity;

    public $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        parent::__construct(self::EVENT_EVENT_ENTITY_CREATE_POST);
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity(EventEntity $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EventEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }
}