<?php
namespace app\api\behavior;

use app\common\Request;

/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 16/11/22
 * Time: 下午11:10
 */
class GroupAction
{
    public function createBefore(&$params)
    {
        $goods_id = $params['goods_id'];
        $goods = model('group_goods')->get($goods_id);
        if ($goods) {
            $params['group_num'] = $goods['group_num'];
            $params['end_time'] = $goods['group_days'] * 86400 + time();
        }
    }

    public function lists(&$params)
    {
        if (isset($params['list']) && count($params['list']) > 0) {
            $ids = \ArrayHelper::extract_value($params['list'], 'id');
            $records = model('group_action_record')->field('group_action_id, count(*) as total')->where('id', 'in', $ids)->group('group_action_id')->select();
            $records_hash = \ArrayHelper::hash($records, 'group_action_id');
            foreach ($params['list'] as $row) {
                $row['remain'] = isset($records_hash[$row['id']]) ? $row['group_num'] - $records_hash[$row['id']]['total'] : 0;
            }
        }
    }

}