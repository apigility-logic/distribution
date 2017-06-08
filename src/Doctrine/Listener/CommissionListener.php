<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 14:09:09
 */

namespace ApigilityLogic\Distribution\Doctrine\Listener;

use ApigilityLogic\Distribution\Doctrine\Event\EventEntityEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;

/**
 * 分佣处理，生成分佣记录
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
        return null;
    }
}