<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:02:01
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
 * Class Commission
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_commission")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 */
class Commission
{
    use \ApigilityLogic\Foundation\Doctrine\Field\Id;

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

    public function __construct() {
        $this->downstream_distributors = new ArrayCollection();
    }
}