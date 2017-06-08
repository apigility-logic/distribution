<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:01:52
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
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use ApigilityLogic\Foundation\Doctrine\Field;

/**
 * Class Event
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_event")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 */
class Event
{
    use Field\Id;
    use Field\CreateTime;
    use Field\Amount;

    /**
     * 基准比值（当前记录的）
     *
     * @Column(type="decimal", precision=5, scale=2, nullable=false)
     */
    protected $base_percent;

    /**
     * 分佣事件发生的分销链节点
     *
     * @ManyToOne(targetEntity="Distributor", inversedBy="events")
     * @JoinColumn(name="distributor_id", referencedColumnName="id")
     */
    protected $distributor;

    /**
     * 事件产生的所有分佣项
     *
     * @OneToMany(targetEntity="Commission", mappedBy="event")
     */
    protected $commissions;

    /**
     * 发生结束发件的标的物
     *
     * @ManyToOne(targetEntity="Target", inversedBy="event")
     * @JoinColumn(name="target_id", referencedColumnName="id")
     */
    protected $target;

    public function __construct() {
        $this->downstream_distributors = new ArrayCollection();
        $this->commissions = new ArrayCollection();
    }

    public function setBasePercent($base_percent)
    {
        $this->base_percent = $base_percent;
        return $this;
    }

    public function getBasePercent()
    {
        return $this->base_percent;
    }

    public function setDistributor($distributor)
    {
        $this->distributor = $distributor;
        return $this;
    }

    /**
     * @return Distributor
     */
    public function getDistributor()
    {
        return $this->distributor;
    }

    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return Target
     */
    public function getTarget()
    {
        return $this->target;
    }
}