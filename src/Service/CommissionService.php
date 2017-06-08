<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/8
 * Time: 16:57:43
 */

namespace ApigilityLogic\Distribution\Service;


use ApigilityLogic\Distribution\Doctrine\Entity\ChainCommission;
use ApigilityLogic\Distribution\Doctrine\Entity\ChainEvent;
use ApigilityLogic\Distribution\Doctrine\Entity\Distributor;
use ApigilityLogic\Distribution\Doctrine\Entity\Event;
use ApigilityLogic\Distribution\Doctrine\Entity\TeamEvent;
use Doctrine\ORM\EntityManager;
use Zend\Hydrator\ClassMethods;
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

    public function create($type, $data)
    {
        $data['create_time'] = new \DateTime();

        $entity = new $type;
        $hydrator = new ClassMethods();
        $hydrator->hydrate($data, $entity);

        $this->em->persist($entity);
        $this->em->flush();

        return $entity;
    }

    /**
     * 执行一次分佣计算
     * @param Event $event_entity
     */
    public function perform(Event $event_entity)
    {
        if ($event_entity instanceof ChainEvent) $this->performChainCommission($event_entity);
        if ($event_entity instanceof TeamEvent) $this->performTeamCommission($event_entity);
    }

    /**
     * 链级分佣
     * @param ChainEvent $event_entity
     */
    private function performChainCommission(ChainEvent $event_entity)
    {
        if ($event_entity->getTarget()->getChainLevels()->count() > 0) {
            foreach ($event_entity->getTarget()->getChainLevels() as $chainLevel) {
                $commission_distributor = $this->getDistributorByChainLevel(
                    $event_entity->getDistributor(),
                    $chainLevel->getLevel());

                if ($commission_distributor) {
                    // 找到分佣者
                    $this->create(ChainCommission::class, [
                        'title' => $chainLevel->getLevel() . '级链式分佣'. $chainLevel->getPercent() .'%',
                        'percent' => $chainLevel->getPercent(),
                        'amount' => ($event_entity->getAmount() * $event_entity->getBasePercent() / 100)
                            * $chainLevel->getPercent() / 100,
                        'distributor' => $commission_distributor,
                        'chain_level' => $chainLevel,
                        'event' => $event_entity
                    ]);
                }
            }
        }
    }

    /**
     * 团队分佣
     * @param TeamEvent $event_entity
     */
    private function performTeamCommission(TeamEvent $event_entity)
    {

    }

    /**
     * 根据链级数向上查找分佣者
     * @param Distributor $source_distributor
     * @param $level
     * @return Distributor|null
     */
    private function getDistributorByChainLevel(Distributor $source_distributor, $level)
    {
        $current_distributor = $source_distributor;

        for ($i = (int)$level; $i>0; $i--) {
            if ($current_distributor && $current_distributor->getUpstreamDistributor()) {
                $current_distributor = $current_distributor->getUpstreamDistributor();
            } else {
                $current_distributor = null;
            }
        }

        return $current_distributor;
    }
}