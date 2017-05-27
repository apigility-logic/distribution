<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:01:52
 */
namespace ApigilityLogic\Distribution\DoctrineEntity;

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

/**
 * Class Event
 * @package ApigilityLogic\Distribution\DoctrineEntity
 * @Entity @Table(name="al_dist_event")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 */
class Event
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * 交易事件的发生额
     *
     * @Column(type="decimal", precision=11, scale=2, nullable=false)
     */
    protected $amount;

    /**
     * 基准比值（当前记录的）
     *
     * @Column(type="decimal", precision=5, scale=2, nullable=false)
     */
    protected $base_percent;

    /**
     * 创建时间
     *
     * @Column(type="datetime", nullable=false)
     */
    protected $create_time;

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

    public function __construct() {
        $this->downstream_distributors = new ArrayCollection();
        $this->commissions = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
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

    public function setCreateTime($create_time)
    {
        $this->create_time = $create_time;
        return $this;
    }

    public function getCreateTime()
    {
        return $this->create_time;
    }
}