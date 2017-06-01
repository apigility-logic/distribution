<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 19:08:15
 */
namespace ApigilityLogic\Distribution\Doctrine\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;

/**
 * @Entity
 */
class TeamCommission extends Commission
{
    /**
     * 接受分佣的领导（团队分佣模式）
     *
     * @ManyToOne(targetEntity="Leader", inversedBy="commissions")
     * @JoinColumn(name="leader_id", referencedColumnName="id")
     */
    protected $leader;
}