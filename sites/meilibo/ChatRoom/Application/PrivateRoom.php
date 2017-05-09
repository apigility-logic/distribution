<?php

namespace Application;

use \Workerman\Autoloader;
use \GatewayWorker\Lib\Mem;
use \GatewayWorker\Lib\Db;
use \Application\Asset\AI;

require_once __DIR__ . '/GatewayClient/Gateway.php';
\Gateway::$registerAddress = \Config\Site::$registerAddress;

/**
 * 更新日志
 * 28/12/2016	初始化随机机器人数量500-800,每五分钟增加0-20
 */
class PrivateRoom {
    /*

     * 收费私密直播
     * 
     *      */

    public static function timingCharge() {
        //获取在线房间
        echo "PrivateRoom\n";
        $allOnLineRoom = join(',', Crontab::getAllOnLineRoom());
        print_r($allOnLineRoom);
        $online_room_id = array();
        $private_room_uid = array();
        //遍历所有在线房间
        $online_room = Db::instance('dbDefault')->query("SELECT `id`,`uid`,`roomid` FROM `ss_backstream` WHERE `streamstatus` = '1' AND `roomid` in " . "(" . $allOnLineRoom . ")");
//        print_r($online_room);
        if (!empty($online_room)) {
            //取出房间id
            foreach ($online_room as $key => $value) {
                $arr_ss_backstream_id[] = $value['id'];
            }
            //所有在线房间id转换成字符串
            $str_ss_backstream_id = join(',', $arr_ss_backstream_id);
            //查询是否是此类型的房间
            $online_private_room = Db::instance('dbDefault')->query("SELECT * FROM `ss_privatelimit` WHERE `ptid` = 4 AND `bsid` IN " . "(" . $str_ss_backstream_id . ")");
            if (!empty($online_private_room)) {
                //取出ss_privatelimit表的bisd
                foreach ($online_private_room as $key => $value) {
                    $arr_ss_privatelimit_bsid[] = $value['bsid'];
                }
                $str_ss_privatelimit_bsid = join(',', $arr_ss_privatelimit_bsid);
                //再次通过privatelimit确认对应的房间号
                $ss_backstream = Db::instance('dbDefault')->query("SELECT `id`,`uid`,`roomid` FROM `ss_backstream` WHERE `streamstatus` = '1' AND `id` in " . "(" . $str_ss_privatelimit_bsid . ")");
                print_r($ss_backstream);
                echo "ss_backstream\n";
                foreach ($ss_backstream as $key => $value) {
                    //user_info当前房间的用户信息
                    foreach (\Gateway::getClientSessionsByGroup($value['roomid']) as $user => $user_info) {
                        if ($user_info['user_id'] != $value['uid']) {
                            $private_room_uid[] = $user_info['user_id'];
                        }
                    }
                    //当前房间的所有用户
                    if (!empty($private_room_uid)) {
                        $str_private_room_uid = join(',', $private_room_uid);
                        $ss_chargeroom = Db::instance('dbDefault')->query("SELECT * FROM `ss_chargeroom` WHERE `uid` in " . "(" . $str_private_room_uid . ")" . " AND `anchor_id` = " . $value['uid'] . " AND `update_time`+60 <" . time());
                        print_r($ss_chargeroom);
                        echo "ss_chargeroom\n";
                        foreach ($ss_chargeroom as $k => $v) {
                            $message = array(
                                'type' => 'chargerRoomTime',
                                'data' => array('money'=>$v['money'],'create_time'=>$v['create_time'],'update_time'=>$v['update_time']),
                            );
                            \Gateway::sendToUid($v['uid'], json_encode($message));
                            $arr_ss_chargeroom_uid[] = $v['uid'];
                        }
                        if (!empty($arr_ss_chargeroom_uid)) {
                            $str_ss_chargeroom_uid = join(',', $arr_ss_chargeroom_uid);
                            $userInfo = Db::instance('dbDefault')->query("SELECT `coinbalance`,`id` FROM `ss_member` WHERE `id` in " . "(" . $str_ss_chargeroom_uid . ")" . " AND `coinbalance` >= 0");
                        }
                    }
                    if (!empty($userInfo) && !empty($ss_chargeroom)) {
                        foreach ($userInfo as $k => $v) {
                            
                            echo $v['coinbalance'] . "\n";
                            if ($v['coinbalance'] <= 10) {
                                //用户余额不足，踢出用户
                                $message = array(
                                    'type' => 'error.kicked',
                                    'content' => '你的余额不足！',
                                );
                                \Gateway::sendToUid($v['id'], json_encode($message));
                                unset($userInfo[$k]);
                                unset($v['id']);
                            }
                            if ($v['coinbalance'] <= 50) {
                                $coinbalance = $v['coinbalance'] - 10;
                                $message = array(
                                    'type' => 'balanceHint',
                                    'content' => '你的余额还剩' . $coinbalance . '，请及时充值！',
                                );
                                \Gateway::sendToUid($v['id'], json_encode($message));
                            }
                        }
                        foreach ($userInfo as $k => $v) {
                            $arr_userInfo_id[] = $v['id'];
                        }
                        if(!empty($arr_userInfo_id)){
                            $str_userInfo_id = join(',', $arr_userInfo_id);
                            Db::instance('dbDefault')->query("UPDATE `ss_chargeroom` SET `money` = `money` + 10, `update_time` = " . time() . " WHERE `uid` IN " . "(" . $str_userInfo_id . ")" . " AND `anchor_id` = " . $value['uid']);
                            //扣钱操作
                            Db::instance('dbDefault')->query("UPDATE `ss_member` SET `coinbalance` = `coinbalance` - 10 WHERE `id` IN " . "(" . $str_private_room_uid . ")");
                            $ss_backstream_user_count = \Gateway::getClientCountByGroup($value['roomid']) - 1;
                            Db::instance('dbDefault')->query("UPDATE `ss_member` SET `beanbalance` = `beanbalance` + " . $ss_backstream_user_count . "*10, `beanorignal` = `beanorignal` + " . $ss_backstream_user_count . "*10 WHERE `id` = " . $value['uid']);
                        }
                    }
                }
            }
        }
    }

    public static function changeChargeRoom($user_id = 0) {
        $online_room = Db::instance('dbDefault')->query("SELECT `id` FROM `ss_backstream` WHERE `streamstatus` = '1' AND `uid` = " . $user_id);
        if ($online_room) {
            $change_room = Db::instance('dbDefault')->insert('ss_privatelimit')->cols(array(
                        'bsid' => $online_room[0]['id'],
                        'ptid' => 4,
                        'prerequisite' => 'qwe',
                    ))->query();
            if ($change_room) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

}
