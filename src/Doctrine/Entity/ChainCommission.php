<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/5/26
 * Time: 19:07:43
 */
namespace ApigilityLogic\Distribution\Doctrine\Entity;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;

/**
 * @Entity
 */
class ChainCommission extends Commission
{
    /**
     * 接受分佣者在当前分佣事件中所处的链级（链级分佣模式）
     *
     * @ManyToOne(targetEntity="ChainLevel", inversedBy="commissions")
     * @JoinColumn(name="chain_level_id", referencedColumnName="id")
     */
    protected $chain_level;

    public function setChainLevel($chain_level)
    {
        $this->chain_level = $chain_level;
        return $this;
    }

    public function getChainLevel()
    {
        return $this->chain_level;
    }
}