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
use ApigilityLogic\Distribution\Doctrine\Entity\Commission;
use ApigilityLogic\Distribution\Doctrine\Entity\Distributor;
use ApigilityLogic\Distribution\Doctrine\Entity\Event;
use ApigilityLogic\Distribution\Doctrine\Entity\LeaderStatus;
use ApigilityLogic\Distribution\Doctrine\Entity\TeamCommission;
use ApigilityLogic\Distribution\Doctrine\Entity\TeamEvent;
use ApigilityLogic\Distribution\Event\CommissionEvent;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Hydrator\ClassMethods;
use Zend\ServiceManager\ServiceManager;

class CommissionService implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

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
                    $commission = $this->create(ChainCommission::class, [
                        'title' => $chainLevel->getLevel() . '级链式分佣'. $chainLevel->getPercent() .'%',
                        'percent' => $chainLevel->getPercent(),
                        'amount' => ($event_entity->getAmount() * $event_entity->getBasePercent() / 100)
                            * $chainLevel->getPercent() / 100,
                        'distributor' => $commission_distributor,
                        'chain_level' => $chainLevel,
                        'event' => $event_entity
                    ]);

                    if ($commission instanceof ChainCommission) $this->triggerCommissionEvent($commission);
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
        $distributor = $event_entity->getDistributor();
        $last_leader_status = null;

        do {
            $distributor = $distributor->getUpstreamDistributor();

            if ($distributor && $distributor->getLeader()) {
                // 找到领导节点
                $leader = $distributor->getLeader();
                $commission_data = [
                    'title' => '团队分佣，' . $leader->getStatus()->getName() . $leader->getStatus()->getPercent() .'%',
                    'percent' => $leader->getStatus()->getPercent(),
                    'amount' => $event_entity->getAmount() * $event_entity->getBasePercent() / 100,
                    'distributor' => $distributor,
                    'leader' => $leader,
                    'event' => $event_entity
                ];

                if (!($last_leader_status instanceof LeaderStatus)) {
                    // 首次找到
                    $commission_data['amount'] *= $leader->getStatus()->getPercent() / 100;
                    $commission = $this->create(TeamCommission::class, $commission_data);
                    if ($commission instanceof TeamCommission) $this->triggerCommissionEvent($commission);
                } elseif ((float)$distributor->getLeader()->getStatus()->getPercent() > (float)$last_leader_status->getPercent()) {
                    // 非首次，且等级上一次找到的更高
                    $commission_data['amount'] *= ($leader->getStatus()->getPercent()-$last_leader_status->getPercent()) / 100;
                    $commission = $this->create(TeamCommission::class, $commission_data);
                    if ($commission instanceof TeamCommission) $this->triggerCommissionEvent($commission);
                }

                $last_leader_status = $distributor->getLeader()->getStatus()->getId();
            }
        } while($distributor && $distributor->getUpstreamDistributor());
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

    /**
     * 触发分佣项生成事件
     *
     * @param Commission $entity
     * @throws \Exception
     */
    private function triggerCommissionEvent(Commission $entity)
    {
        if ($entity instanceof ChainCommission) {
            $commission_event = new CommissionEvent(CommissionEvent::EVENT_CHAIN_COMMISSION_CREATE_POST ,$this->sm);
        } elseif ($entity instanceof TeamCommission) {
            $commission_event = new CommissionEvent(CommissionEvent::EVENT_TEAM_COMMISSION_CREATE_POST ,$this->sm);
        } else {
            throw new \Exception('未知的Commission类型');
        }

        $commission_event->setTarget($this);
        $commission_event->setEntity($entity);
        $this->getEventManager()->triggerEvent($commission_event);
    }
}