<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/13
 * Time: 10:38:01
 */

namespace ApigilityLogic\Distribution\Listener;


use ApigilityLogic\Distribution\Doctrine\Entity\DistributionCustomer;
use ApigilityLogic\Distribution\Doctrine\Entity\Distributor;
use ApigilityLogic\Finance\Doctrine\Entity\Account;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceManager;
use ZF\Apigility\Doctrine\Server\Event\DoctrineResourceEvent;

/**
 * 监听系统事件，当有[分销节点 ApigilityLogic\Distribution\Doctrine\Entity\Distributor]实体被创建时，
 * 调用 ApigilityLogic\Finance 组件的服务，
 * 创建 对应的的[记账客户 ApigilityLogic\Distribution\Doctrine\Entity\DistributionCustomer]，
 * 同时为该记账客户创建两个记账账户：cumulative[累计账户]，balance[余额账户]。
 *
 * Class DistributionCustomerListener
 * @package ApigilityLogic\Distribution\Listener
 */
class DistributionCustomerListener
{
    protected $sharedListeners = [];

    private $sm;

    /**
     * @var EntityManager
     */
    private $em;

    function __construct(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
        $this->em = $serviceManager->get(EntityManager::class);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $listener =  $events->attach(
            'ZF\Apigility\Doctrine\DoctrineResource',
            DoctrineResourceEvent::EVENT_CREATE_POST,
            [$this, 'generateFinanceCustomer']
        );

        if (! $listener) {
            $listener = [$this, 'generateFinanceCustomer'];
        }

        $this->sharedListeners[] = $listener;
    }

    public function generateFinanceCustomer(DoctrineResourceEvent $event)
    {
        $distributor = $event->getEntity();
        if ($distributor instanceof Distributor) {
            // 生成Finance组件的Customer对象和Account对象
            $customer = new DistributionCustomer();
            $customer->setName($distributor->getName())
                ->setDistributor($distributor)
                ->setCreateTime(new \DateTime())
                ->setUpdateTime(new \DateTime());

            $this->em->persist($customer);
            $this->em->flush();

            $account_cumulative = new Account();
            $account_cumulative->setName('cumulative')
                ->setCustomer($customer)
                ->setCreateTime(new \DateTime())
                ->setUpdateTime(new \DateTime());
            $this->em->persist($account_cumulative);

            $account_balance = new Account();
            $account_balance->setName('balance')
                ->setCustomer($customer)
                ->setCreateTime(new \DateTime())
                ->setUpdateTime(new \DateTime());
            $this->em->persist($account_balance);

            $this->em->flush();
        }
    }
}