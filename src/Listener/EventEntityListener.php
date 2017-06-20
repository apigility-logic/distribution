<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 14:48:56
 */

namespace ApigilityLogic\Distribution\Listener;

use ApigilityLogic\Distribution\Doctrine\Entity\Event as DistributionEventEntity;
use ApigilityLogic\Distribution\Event\EventEntityEvent;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceManager;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * 监听系统事件，当有[分销事件ApigilityLogic\Distribution\Doctrine\Entity\Event]实体被创建时，
 * 创建一个模块级的事件 ApigilityLogic\Distribution\Event\EventEntityEvent 。
 *
 * Class EventEntityListener
 * @package ApigilityLogic\Distribution\Listener
 */
class EventEntityListener implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    protected $sharedListeners = [];

    private $sm;

    function __construct(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }

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
            $event_entity_event = new EventEntityEvent($this->sm);
            $event_entity_event->setTarget($this);
            $event_entity_event->setEntity($event->getEntity());
            $this->getEventManager()->triggerEvent($event_entity_event);
        }
    }
}