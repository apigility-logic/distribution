<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 16:01:10
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

/**
 * Class LeaderStatus
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_leader_status")
 */
class LeaderStatus
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * 身份（领导节点类型）名称
     *
     * @Column(type="string", length=50, nullable=true)
     */
    protected $name;

    /**
     * 分佣比值
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
     * 更新时间
     *
     * @Column(type="datetime", nullable=false)
     */
    protected $update_time;

    /**
     * @OneToMany(targetEntity="Leader", mappedBy="status")
     */
    protected $leaders;

    public function __construct() {
        $this->leaders = new ArrayCollection();
    }
}