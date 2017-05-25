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

    public function getMillisecond()
    {
        $micro_time = microtime(true);
        $time = intval($micro_time);
        return intval(($micro_time - $time) * 1000);
    }

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
            'create_time' => time()
        ];
        $round = model('snatch_round')->create($data);
        if ($round) {
            model('snatch_round_code')->create([
                'round_id' => $round['id'],
                'code_num' => $goods['code_num'],
                'sale_times' => 0,
                'codes' => join(',', $this->genCodes($goods['code_num'])),
                'create_time' => time()
            ]);
        }
        return $round;
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
        $round = model('snatch_round')->find($round_id);
        $round_code = model('snatch_round_code')->lock(true)->where('round_id', $round_id)->find();
        $codes = explode(',', $round_code['codes']);
        $user_codes = [];
        for ($i = $round_code['sale_times']; $i < $round_code['sale_times'] + $code_num && $i < $round_code['code_num']; $i++) {
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
                'ip' => request()->ip(0, true),
                'millisecond' => $this->getMillisecond(),
                'create_time' => time(),
            ]);
            $round['sale_times'] += $user_codes_num;
            $round['sale_rate'] = floatval($round['sale_times']) / $round_code['code_num'];
            Db::table('snatch_round_code')->where('round_id', $round_id)->update(['sale_times' => $round['sale_times']]);
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

    /**
     * 揭晓
     * @param int $goods_info
     * @param int $goods_times
     * @param int $number
     * @param int $last_consume_id
     * @return type
     */
    public function announce($round_id)
    {
        model('snatch_round')->where('id', $round_id)->save([
            'status' => 2,
            'announce_time' => time() + 3 * 60
        ]);
        $goods_id = $goods_info['id'];
        $need_num = $goods_info['price'] / self::UNIT_PRICE;
        list($time_count, $count_data) = $this->getCountData($last_consume_id);
        $lucky_number = fmod($time_count, $need_num) + 1000001;
        $data = array(
            'sale_times' => array('exp', "`sale_times` + {$number}"),
            'announce_time' => $this->_timestamp + 60 * 3,
            'announce_millisecond' => $this->_millisecond,
            'time_count' => $time_count,
            'count_data' => json_encode($count_data),
            'lucky_number' => $lucky_number,
            'last_consume_id' => $last_consume_id,
            'user_id' => $this->getLuckyUserId($goods_id, $goods_times, $lucky_number),
        );
        $where = array('goods_id' => $goods_id, 'goods_times' => $goods_times);
        $ret = M('goods_data')->where($where)->save($data);
        if ($ret) {
            $goods_order_id = $this->addGoodsOrder($goods_id, $goods_times);
            if ($goods_order_id) {
                return $this->addGoodsOrderLog(array(
                    'goods_order_id' => $goods_order_id,
                    'action_user_id' => 0,
                    'action_user' => '乐淘系统',
                ), 1);
            }
            return $goods_order_id;
        }
        return $ret;
    }

    public function getLuckyCode($round_id)
    {
        $round_code = model('snatch_round_code')->where('round_id', $round_id)->find();
        $lucky_code = 0;
        if ($round_code) {
            $code_num = $round_code['code_num'];
            $pos = rand(0, $code_num);
            $codes = explode(',', $round_code['codes']);
            $lucky_code = $codes[$pos];
        }
        return $lucky_code;
    }

    public function getLuckyUserId($round_id, $code)
    {

    }

}