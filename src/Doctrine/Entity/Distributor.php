<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:01:39
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

/**
 * Class Distributor
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_distributor")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 */
class Distributor
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * 分销者名称
     *
     * @Column(type="string", length=50, nullable=true)
     */
    protected $name;

    /**
     * 推荐码，每个分销者都有一个唯一的推荐码，当他推荐朋友注册发展下级时，让朋友输入此推荐码来关联上级
     *
     * @Column(type="string", length=50, nullable=false)
     */
    protected $code;

    /**
     * 创建时间
     *
     * @Column(type="datetime", nullable=true)
     */
    protected $create_time;

    /**
     * 更新时间
     *
     * @Column(type="datetime", nullable=true)
     */
    protected $update_time;

    /**
     * 下游（下级）分销者集合
     *
     * @OneToMany(targetEntity="Distributor", mappedBy="upstream_distributor")
     */
    protected $downstream_distributors;

    /**
     * 上游（上级）分销者
     *
     * @ManyToOne(targetEntity="Distributor", inversedBy="downstream_distributors")
     * @JoinColumn(name="upstream_distributor_id", referencedColumnName="id")
     */
    protected $upstream_distributor;

    /**
     * 分销者对应的领导对象（如果有的话）
     *
     * @OneToOne(targetEntity="Leader", mappedBy="distributor")
     */
    protected $leader;

    /**
     * 此分销者发生的所有分佣事件
     *
     * @OneToMany(targetEntity="Event", mappedBy="distributor")
     */
    protected $events;

    /**
     * 接受过的所有分佣项
     *
     * @OneToMany(targetEntity="Commission", mappedBy="distributor")
     */
    protected $commissions;

    public function __construct() {
        $this->downstream_distributors = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->commissions = new ArrayCollection();
    }
}