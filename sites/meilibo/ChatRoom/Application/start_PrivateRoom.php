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
use \Workerman\Autoloader;
use \Application\PrivateRoom;

// 自动加载类
require_once __DIR__ . '/../Workerman/Autoloader.php';

// worker 进程
$worker_cron = new worker("Websocket://0.0.0.0:4646");
// 设置名称，方便status时查看
$worker_cron->name = 'PricateRoomworker';
// 设置进程数，worker进程数建议与cpu核数相同
$worker_cron->count = 1;

$worker_cron->onWorkerStart = function() {
		\Workerman\Lib\Timer::add(10,function() {
                    PrivateRoom::timingCharge();
		},array(),true);
};
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
