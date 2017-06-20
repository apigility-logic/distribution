<?php
/**
 * Created by PhpStorm.
 * User: figo-007
 * Date: 2017/6/20
 * Time: 16:28:37
 */

namespace ApigilityLogic\Distribution\Doctrine\Entity;

use ApigilityLogic\Foundation\Doctrine\Field;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Column;

/**
 * Class Setting
 * @package ApigilityLogic\Distribution\Doctrine\Entity
 * @Entity @Table(name="al_dist_setting")
 */
class Setting
{
    use Field\Id;

    /**
     * 链级分佣模式的默认基准比值
     *
     * @Column(type="decimal", precision=5, scale=2, nullable=false)
     */
    protected $chain_mode_base_percent;

    /**
     * 团队分佣模式的默认基准比值
     *
     * @Column(type="decimal", precision=5, scale=2, nullable=false)
     */
    protected $team_mode_base_percent;

    public function setChainModeBasePercent($chain_mode_base_percent)
    {
        $this->chain_mode_base_percent = $chain_mode_base_percent;
        return $this;
    }

    public function getChainModeBasePercent()
    {
        return $this->chain_mode_base_percent;
    }

    public function setTeamModeBasePercent($team_mode_base_percent)
    {
        $this->team_mode_base_percent = $team_mode_base_percent;
        return $this;
    }

    public function getTeamModeBasePercent()
    {
        return $this->team_mode_base_percent;
    }
}