<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:01:29
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

/**
 * Class Leader
 * @package ApigilityLogic\Distribution\DoctrineEntity
 * @Entity @Table(name="al_dist_leader")
 */
class Leader
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * 领导名称
     *
     * @Column(type="string", length=50, nullable=true)
     */
    protected $name;

    /**
     * 创建时间
     *
     * @Column(type="datetime", nullable=false)
     */
    protected $create_time;

    /**
     * 更新时间
     *
     * @Column(type="datetime", nullable=false)
     */
    protected $update_time;

    /**
     * 领导的身份类型
     *
     * @ManyToOne(targetEntity="LeaderStatus", inversedBy="leaders")
     * @JoinColumn(name="product_id", referencedColumnName="id")
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
     * @OneToMany(targetEntity="Commission", mappedBy="leader")
     */
    protected $commissions;

    public function __construct() {
        $this->commissions = new ArrayCollection();
    }
}