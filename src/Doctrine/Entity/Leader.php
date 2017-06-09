<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:01:29
 */
namespace ApigilityLogic\Distribution\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use ApigilityLogic\Foundation\Doctrine\Field;

/**
 * Class Leader
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_leader")
 */
class Leader
{
    use Field\Id;
    use Field\Name;
    use Field\CreateTime;
    use Field\UpdateTime;

    /**
     * 领导的身份类型
     *
     * @ManyToOne(targetEntity="LeaderStatus", inversedBy="leaders")
     * @JoinColumn(name="leader_status_id", referencedColumnName="id")
     */
    protected $status;

    /**
     * 领导对应的分销者节点
     *
     * @OneToOne(targetEntity="Distributor", inversedBy="leader")
     * @JoinColumn(name="distributor_id", referencedColumnName="id")
     */
    protected $distributor;

    /**
     * 领导的所有分佣项
     *
     * @OneToMany(targetEntity="TeamCommission", mappedBy="leader")
     */
    protected $commissions;

    public function __construct() {
        $this->commissions = new ArrayCollection();
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return LeaderStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

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