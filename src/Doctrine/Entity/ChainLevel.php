<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 15:10:31
 */
namespace ApigilityLogic\Distribution\Doctrine\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ChainLevel
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_chain_level")
 */
class ChainLevel
{
    use \ApigilityLogic\Foundation\Doctrine\Field\Id;

    /**
     * 链级
     *
     * @Column(type="integer", length=50, nullable=false)
     */
    protected $level;

    /**
     * 分佣比值
     *
     * @Column(type="decimal", precision=5, scale=2, nullable=false)
     */
    protected $percent;

    /**
     * 此链级关聊的所有分佣项
     *
     * @OneToMany(targetEntity="ChainCommission", mappedBy="event")
     */
    protected $commissions;

    /**
     * 该分佣链级配置所属的标的
     *
     * @ManyToOne(targetEntity="Target", inversedBy="chain_levels")
     * @JoinColumn(name="target_id", referencedColumnName="id")
     */
    protected $target;

    public function __construct() {
        $this->commissions = new ArrayCollection();
    }

    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setPercent($percent)
    {
        $this->percent = $percent;
        return $this;
    }

    public function getPercent()
    {
        return $this->percent;
    }

    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    public function getTarget()
    {
        return $this->target;
    }
}

