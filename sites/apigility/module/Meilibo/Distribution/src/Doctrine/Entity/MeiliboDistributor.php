<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/27
 * Time: 17:10:32
 */
namespace Meilibo\Distribution\Doctrine\Entity;

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
use ApigilityLogic\Distribution\Doctrine\Entity\Distributor as ApigilityLogicDistributor;

/**
 * Class Distributor
 * @package Meilibo\Distribution\Doctrine\Entity
 * @Entity
 */
class MeiliboDistributor extends ApigilityLogicDistributor
{
    /**
     * 美丽播用户标识
     *
     * @Column(type="string", length=50, nullable=true)
     */
    protected $meilibo_user_id;
}