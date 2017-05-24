<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/23
 * Time: 上午10:06
 */

namespace app\api\logic;


use think\Config;
use think\Db;

class Snatch
{
    /**
     * 生成夺宝码
     * @param $num
     * @return array
     */
    public function genCodes($num)
    {
        $code = array();
        for ($i = 1; $i <= $num; $i++) {
            $code[] = Config::get('group_code_base') + $i;
        }
        shuffle($code);
        return $code;
    }

    /**
     * 新增夺宝轮次
     * @param $goods_id
     * @param $code_num
     * @param $code_unit
     * @return false|int
     */
    public function addRound($goods_id)
    {
        $goods = model('snatch_goods')->get($goods_id);
        $data = [
            'goods_id' => $goods_id,
            'code_num' => $goods['code_num'],
            'code_unit' => $goods['code_unit'],
            'sale_times' => 0,
            'sale_rate' => 0,
            'codes' => join(',', $this->genCodes($goods['code_num'])),
            'create_time' => time()
        ];
        return model('snatch_round')->create($data);
    }

    /**
     * 获取夺宝码
     * @param $user_id
     * @param $round_id
     * @param $code_num
     * @param int $level
     * @return array|void
     */
    public function createCodes($user_id, $round_id, $code_num, $level = 1)
    {
        if ($level > 5) {
            return;
        }
        $round = model('snatch_round')->lock(true)->find($round_id);
        $codes = explode(',', $round['codes']);
        $user_codes = [];
        for ($i = $round['sale_times']; $i < $round['sale_times'] + $code_num && $i < $round['code_num']; $i++) {
            $user_codes[] = $codes[$i];
        }
        $user_codes_num = count($user_codes);
        if ($user_codes_num > 0) {
            Db::table('snatch_record')->insert([
                'round_id' => $round_id,
                'goods_id' => $round['goods_id'],
                'user_id' => $user_id,
                'codes' => join(',', $user_codes),
                'code_num' => $user_codes_num,
                'create_time' => time(),
            ]);
            $round['sale_times'] += $user_codes_num;
            $round['sale_rate'] = floatval($round['sale_times']) / $round['code_num'];
            Db::table('snatch_round')->where('id', $round_id)->update($round->getData());
        }
        if ($user_codes_num < $code_num) {
            $new_round = $this->addRound($round['goods_id']);
            if ($new_round) {
                //新的一轮
                $new_codes = $this->createCodes($user_id, $new_round['id'], $code_num - $user_codes_num, $level + 1);
            }
        }
        return $user_codes;
    }

    public function getLastRoundId($goods_id)
    {
        return model('snatch_round')->where(['goods_id' => $goods_id])->max('id');
    }

}