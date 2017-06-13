<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/13
 * Time: 10:00:37
 */

namespace ApigilityLogic\Distribution\Doctrine\Entity;


use ApigilityLogic\Finance\Doctrine\Entity\Customer;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Class DistributionCustomer
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity
 */
class DistributionCustomer extends Customer
{
    /**
     * Finance客户对应的分佣者
     *
     * @OneToOne(targetEntity="Distributor", inversedBy="distributionCustomer")
     * @JoinColumn(name="distributor_id", referencedColumnName="id")
     */
    protected $distributor;

    public function setDistributor($distributor)
    {
        $this->distributor = $distributor;
        return $this;
    }

    public function getDistributor()
    {
        return $this->distributor;
    }
}