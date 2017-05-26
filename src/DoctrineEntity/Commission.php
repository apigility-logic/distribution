<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:02:01
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
 * Class Commission
 * @package ApigilityLogic\Distribution\DoctrineEntity
 * @Entity @Table(name="al_dist_commission")
 */
class Commission
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * 标题（用于记录字面备注）
     *
     * @Column(type="string", length=200, nullable=true)
     */
    protected $title;

    /**
     * 分佣比值（当前记录的）
     *
     * @Column(type="decimal", precision=5, scale=2, nullable=false)
     */
    protected $percent;

    /**
     * 创建时间
     *
     * @Column(type="datetime", nullable=false)
     */
    protected $create_time;

    /**
     * 分佣项所属的分佣事件
     *
     * @ManyToOne(targetEntity="Event", inversedBy="commissions")
     * @JoinColumn(name="event_id", referencedColumnName="id")
     */
    protected $event;

    /**
     * 接受分佣者
     *
     * @ManyToOne(targetEntity="Distributor", inversedBy="commissions")
     * @JoinColumn(name="distributor_id", referencedColumnName="id")
     */
    protected $distributor;

    /**
     * 接受分佣的领导（团队分佣模式）
     *
     * @ManyToOne(targetEntity="Leader", inversedBy="commissions")
     * @JoinColumn(name="leader_id", referencedColumnName="id")
     */
    protected $leader;

    /**
     * 接受分佣者在当前分佣事件中所处的链级（链级分佣模式）
     *
     * @ManyToOne(targetEntity="ChainLevel", inversedBy="commissions")
     * @JoinColumn(name="chain_level_id", referencedColumnName="id")
     */
    protected $chain_level;

    public function __construct() {
        $this->downstream_distributors = new ArrayCollection();
    }
}