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
            $Snatch->createCodes($user_id, $goods_id, $code_num);
            Db::commit();
            exit('test');
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            exit($e->getMessage());
        }
    }

}