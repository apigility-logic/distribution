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
            $code[] = Config::get('snatch_code_base') + $i;
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
            $record_id = Db::table('snatch_record')->insert([
                'round_id' => $round_id,
                'goods_id' => $round['goods_id'],
                'user_id' => $user_id,
                'codes' => join(',', $user_codes),
                'code_num' => $user_codes_num,
                'ip' => request()->ip(0, true),
                'millisecond' => $this->getMillisecond(),
                'create_time' => time(),
            ], false, true);
            $round['sale_times'] += $user_codes_num;
            $round['sale_rate'] = floatval($round['sale_times']) / $round_code['code_num'];
            Db::table('snatch_round_code')->where('round_id', $round_id)->update(['sale_times' => $round['sale_times']]);
            Db::table('snatch_round')->where('id', $round_id)->update($round->getData());
            if ($round['sale_rate'] == 1) {
                $this->announce($round, $record_id);
            }
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

    /**
     * 最新一轮
     * @param $goods_id
     * @return mixed
     */
    public function getLastRoundId($goods_id)
    {
        return model('snatch_round')->where(['goods_id' => $goods_id])->max('id');
    }

    /**
     * 计算数据,全网最后一百条购买记录
     * @param $record_id
     * @return array
     */
    public function getCountData($record_id)
    {
        $data = model('snatch_record')->alias('sr')
            ->field('sr.create_time,millisecond,sr.user_id,nickname,avatar')
            ->join('user_profile up', 'up.user_id = sr.user_id')
            ->where('sr.id', '<=', $record_id)
            ->limit(100)
            ->order('sr.id desc')
            ->select();
        foreach ($data as $row) {
            $row['count_value'] = date('His', $row['create_time']) . str_pad($row['millisecond'], 3, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * 揭晓
     * @param $round_id
     * @param $record_id
     * @return false|int
     */
    public function announce($round, $record_id)
    {
        $count_data = $this->getCountData($record_id);
        $lucky_code = 0;
        $snatch_code_base = Config::get('snatch_code_base');
        foreach ($count_data as $row) {
            $lucky_code = ($lucky_code + $row['count_value']) % $round['code_num'];
        }
        $lucky_code += $snatch_code_base + 1;
        $lucky_record = model('snatch_record')->where('round_id', $round['id'])->whereLike('codes', '%' . $lucky_code . '%')->find();
        $model = model('snatch_round')->where('id', $round['id'])->update([
            'status' => 2,
            'announce_time' => time() + 3 * 60, //3分钟后揭晓
            'lucky_code' => $lucky_code,
            'lucky_user_id' => $lucky_record['user_id'],
            'lucky_time' => $lucky_record['create_time'],
            'count_data' => json_encode($count_data)
        ]);
        $this->createOrder([
            'user_id' => $lucky_record['user_id'],
            'round_id' => $round['id'],
            'goods_id' => $round['goods_id'],
            'create_time' => time()
        ]);
        return $model;
    }

    public function getOrderSn()
    {
        return '10' . date('ymdHis') . rand(1000, 9999);
    }

    public function createOrder($data)
    {
        $data['order_sn'] = $this->getOrderSn();
        $data['create_time'] = time();
        $data['address_id'] = 0;
        $data['shipping_status'] = 0;
        $data['order_status'] = 0;
        return model('snatch_order')->create($data);
    }

}