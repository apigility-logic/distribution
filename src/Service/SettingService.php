<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/20
 * Time: 17:15:29
 */

namespace ApigilityLogic\Distribution\Service;

use ApigilityLogic\Distribution\Doctrine\Entity\Setting;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;

class SettingService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ServiceManager
     */
    private $sm;

    function __construct(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
        $this->em = $serviceManager->get('Doctrine\ORM\EntityManager');
    }

    /**
     * @return null|Setting
     */
    public function getSetting()
    {
        return $this->em->getRepository(Setting::class)->find(1);
    }
}