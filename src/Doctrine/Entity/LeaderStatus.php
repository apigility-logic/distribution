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
use ApigilityLogic\Foundation\Doctrine\Field;

/**
 * Class LeaderStatus
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_leader_status")
 */
class LeaderStatus
{
    use Field\Id;
    use Field\Name; // 身份（领导节点类型）名称
    use Field\CreateTime;
    use Field\UpdateTime;
    use Field\Percent;

    /**
     * @OneToMany(targetEntity="Leader", mappedBy="status")
     */
    protected $leaders;

    public function __construct() {
        $this->leaders = new ArrayCollection();
    }
}