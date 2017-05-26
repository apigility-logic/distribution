<?php
/**
 * Created by PhpStorm.
 * User: zhengzhaowei
 * Date: 2017/5/14
 * Time: 上午1:04
 */

namespace app\api\controller;

use app\api\logic\Snatch;
use app\common\Code;
use app\common\Request;
use think\Db;

class SnatchRecord extends Base
{
    public function create()
    {
        $user_id = Request::instance()->getUserId();
        if (empty($user_id)) {
            return $this->error(Code::APP_TOKEN_ERROR);
        }
        $goods_id = request()->param('goods_id');
        $code_num = request()->param('code_num');
        $Snatch = new Snatch();
        Db::startTrans();
        try {
            $round_id = $Snatch->getLastRoundId($goods_id);
            if(empty($round_id)) {
                $round = $Snatch->addRound($goods_id);
                $round_id = $round['id'];
            }
            $Snatch->createCodes($user_id, $round_id, $code_num);
            Db::commit();
            return $this->success();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            //exit($e->getMessage());
            //exit(model('snatch_record')->getLastSql());
            return $this->error();
        }
    }

    public function rank()
    {
        $round_id = request()->param('round_id');
        \app\common\model\SnatchRecord::setFields(['profile' => 'user_id,avatar,nickname']);
        $data = model('snatch_record')->with('profile')
            ->field('user_id,round_id,ip,millisecond,create_time,sum(`code_num`) as code_num')
            ->where('round_id', $round_id)
            ->group('user_id,round_id')
            ->order('code_num desc')
            ->limit(10)
            ->select();
        foreach($data as $row){
            $row['code_num'] = intval($row['code_num']);
        }
        $response = ['list' => $data];
        return $response;
    }

}