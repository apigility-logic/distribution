<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 14:09:09
 */

namespace ApigilityLogic\Distribution\Listener;

use ApigilityLogic\Distribution\Event\EventEntityEvent;
use ApigilityLogic\Distribution\Service\CommissionService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * 分佣事件处理，生成分佣记录
 * 事件 EventEntityEvent 发生时，调用 CommissionService 生成分佣记录
 *
 * Class CommissionListener
 * @package ApigilityLogic\Distribution\Doctrine\Listener
 */
class CommissionListener implements ListenerAggregateInterface
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @inheritdoc
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            EventEntityEvent::EVENT_EVENT_ENTITY_CREATE_POST,
            [$this, 'generateCommissions']
        );
    }

    /**
     * @inheritdoc
     */
    public function detach(EventManagerInterface $events)
    {
        // TODO: Implement detach() method.
    }

    public function generateCommissions(EventEntityEvent $event)
    {
        $commission_service = $event->serviceManager->get(CommissionService::class);
        $commission_service->perform($event->getEntity());
    }
}