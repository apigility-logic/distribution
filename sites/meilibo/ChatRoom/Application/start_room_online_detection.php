<?php 
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \GatewayWorker\Lib\Db;
use \GatewayWorker\Lib\Gateway;

/**
 * 找出数据库中所有标记主播在线broadcasting=y的uid，
 * 通过消息系统判断其是否真的在线，
 * 如果检测多次不在线，
 * 则将其数据库中的对应记录设置为不在线broadcasting=n。
 *
 * 这个脚本的作用是：
 * 其它cdn厂商缺少断流回调或者回调因为网络等原因没有没成功，
 * 需要将已经断流下线的主播从数据库中设置为下线
 */

// 自动加载类
require_once __DIR__ . '/../Workerman/Autoloader.php';

// 没配置\Config\Site::$enableRoomOnlineDetect 则不开启
if (empty(\Config\Site::$enableRoomOnlineDetect)) {
    return;
}

// 检测时间间隔
define('UID_ONLINE_DETECT_INTERVAL', 5);
// 检测多少次不在线将其数据库设置为不在线
define('UID_ONLINE_DETECT_LIMIT', 2);

// worker 进程
$worker_cron = new worker();
// 设置名称，方便status时查看
$worker_cron->name = 'detectRoomOnlineWorker';
// 设置进程数，固定为1
$worker_cron->count = 1;

// 通过消息系统检测主播不在线次数，检测 UID_ONLINE_DETECT_LIMIT 次都不在线则设置起数据库为不在线
// ['uid'=>count, 'uid'=>count, ...]
$offline_count = array();

$worker_cron->onWorkerStart = function() {
    Gateway::$registerAddress = \Config\Site::$registerAddress;
    \Workerman\Lib\Timer::add(5, function() {
        global $offline_count;
        // 查找数据库中所有标记在线的主播
        $all_online_uid = Db::instance('dbDefault')->column("select id from ss_member where broadcasting='y'");
        if ($all_online_uid) {
            foreach ($all_online_uid as $uid) {
                // 消息系统中uid是否在线
                $online = Gateway::isUidOnline($uid);
                // 不在线
                if (!$online) {
                    // 不在线次数+1
                    $offline_count[$uid] = isset($offline_count[$uid]) ? $offline_count[$uid] + 1 : 1;
                    // 超过限制则更新数据库
                    if ($offline_count[$uid] >= UID_ONLINE_DETECT_LIMIT) {
                        echo "detected uid: $uid is offline in chat and update ss_member broadcasting='no'\n";
                        Db::instance('dbDefault')->query("update ss_member set broadcasting='n' where id=$uid");
                        unset($offline_count[$uid]);
                    }
                // 在线，删除对应uid不在线数据
                } else {
                    unset($offline_count[$uid]);
                }
            }
        }
    });
};
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
