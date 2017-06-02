<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/2
 * Time: 15:55:52
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
 * 分销标的物
 *
 * Class Target
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_target")
 */
class Target
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * 名称
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
     * 此标的物所关聊的所有分佣链级
     *
     * @OneToMany(targetEntity="ChainLevel", mappedBy="target")
     */
    protected $chain_levels;

    /**
     * 此标的物所发生的所有分佣事件
     *
     * @OneToMany(targetEntity="Event", mappedBy="target")
     */
    protected $event;

    public function __construct() {
        $this->chain_levels = new ArrayCollection();
    }
}