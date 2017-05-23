<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/23
 * Time: 上午10:06
 */

namespace app\api\logic;


use think\Config;

class Group
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
     * 添加初始数据
     * @param type $goods_id
     * @param type $times
     */
    public function addData($goods_id, $goods_times, $num)
    {
        $data = array(
            'goods_id' => $goods_id,
            'goods_times' => $goods_times,
            'sale_times' => 0,
            'shuffle_data' => join(',', $this->genCode($num)),
            'count_data' => ''
        );
        return M('goods_data')->add($data);
    }

}