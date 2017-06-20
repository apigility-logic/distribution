<?php
namespace ApigilityLogic\Distribution\Listener;

use ApigilityLogic\Distribution\Event\CommissionEvent;
use ApigilityLogic\Finance\Doctrine\Entity\Ledger;
use ApigilityLogic\Finance\Service\LedgerService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\ServiceManager\ServiceManager;

/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/12
 * Time: 11:55:17
 */
class LedgerListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var ServiceManager
     */
    private $sm;

    /**
     * @var LedgerService
     */
    private $ledgerService;

    function __construct(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
        $this->ledgerService = $this->sm->get(LedgerService::class);
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            CommissionEvent::EVENT_CHAIN_COMMISSION_CREATE_POST,
            [$this, 'generateLedgers']
        );

        $this->listeners[] = $events->attach(
            CommissionEvent::EVENT_TEAM_COMMISSION_CREATE_POST,
            [$this, 'generateLedgers']
        );
    }

    /**
     * 生成记账数据
     *
     * @param CommissionEvent $event
     * @throws \Exception
     */
    public function generateLedgers(CommissionEvent $event)
    {
        $accounts = $event->getEntity()->getDistributor()->getDistributionCustomer()->getAccounts();
        $account_cumulative = null;
        $account_balance = null;

        foreach ($accounts as $account) {
            if ($account->getName() === 'cumulative') $account_cumulative = $account;
            if ($account->getName() === 'balance') $account_balance = $account;
        }

        if (empty($account_cumulative) || empty($account_balance)) {
            throw new \Exception('找不到账户');
        }

        $this->ledgerService->createLedger((object)[
            'account' => $account_cumulative,
            'amount' => $event->getEntity()->getAmount(),
            'amount_type' => Ledger::AMOUNT_TYPE_DEBIT
        ]);

        $this->ledgerService->createLedger((object)[
            'account' => $account_balance,
            'amount' => $event->getEntity()->getAmount(),
            'amount_type' => Ledger::AMOUNT_TYPE_DEBIT
        ]);
    }
}