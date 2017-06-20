<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/7
 * Time: 15:52:07
 */

namespace ApigilityLogic\Distribution\Doctrine\Query\CreateFilter;

use ApigilityLogic\Distribution\Doctrine\Entity\ChainEvent;
use ApigilityLogic\Distribution\Doctrine\Entity\TeamEvent;
use ApigilityLogic\Distribution\Service\SettingService;
use ApigilityLogic\Foundation\Doctrine\Query\CreateFilter\AbstractCreateFilter;
use Zend\ServiceManager\ServiceManager;
use ZF\Rest\ResourceEvent;

class EventCreateFilter extends AbstractCreateFilter
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return SettingService
     */
    private function getSettingService()
    {
        return $this->serviceManager->get(SettingService::class);
    }

    /**
     * @param ResourceEvent $event
     * @param string $entityClass
     * @param array $data
     * @return array
     */
    public function filter(ResourceEvent $event, $entityClass, $data)
    {
        $this->setCreateTime($data);

        // 如果没有指定base_percent，那么从默认配置中读取
        if (!isset($data->base_percent)) {
            $setting = $this->getSettingService()->getSetting();

            $entity_temp = new $entityClass;
            if ($entity_temp instanceof ChainEvent) $data->base_percent = $setting->getChainModeBasePercent();
            if ($entity_temp instanceof TeamEvent) $data->base_percent = $setting->getTeamModeBasePercent();
        }

        return $data;
    }
}