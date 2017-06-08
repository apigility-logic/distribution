<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 14:48:56
 */

namespace ApigilityLogic\Distribution\Doctrine\Listener;

use ApigilityLogic\Distribution\Doctrine\Entity\Event as DistributionEventEntity;
use ApigilityLogic\Distribution\Doctrine\Event\EventEntityEvent;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use Zend\EventManager\SharedEventManagerInterface;

class EventEntityListener implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    protected $sharedListeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $listener =  $events->attach(
            'ZF\Apigility\Doctrine\DoctrineResource',
            DoctrineResourceEvent::EVENT_CREATE_POST,
            [$this, 'triggerEventEntityEvent']
        );

        if (! $listener) {
            $listener = [$this, 'triggerEventEntityEvent'];
        }

        $this->sharedListeners[] = $listener;
    }

    public function triggerEventEntityEvent(DoctrineResourceEvent $event)
    {
        if ($event->getEntity() instanceof DistributionEventEntity) {
            // 触发事件
            $event_entity_event = new EventEntityEvent(EventEntityEvent::EVENT_EVENT_ENTITY_CREATE_POST, $this);
            $event_entity_event->setEntity($event->getEntity());
            $this->getEventManager()->triggerEvent($event_entity_event);
        }
    }
}