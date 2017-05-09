<?php
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../Workerman/Autoloader.php';
Autoloader::setRootPath(__DIR__);

// bussinessWorker 进程
$worker = new BusinessWorker();
// worker名称
$worker->name = 'ChatBusinessWorker';
// bussinessWorker进程数量
$worker->count = 4;
// 服务注册地址
$worker->registerAddress = '127.0.0.1:1236';

//启动时初始化Mem
$worker->onWorkerStart = function() {
	\Application\RoomStatus::init();
	// 定时任务
	\Workerman\Lib\Timer::add(10, function() {
		\Application\RoomStatus::reloadChangedStatus();
	},array(),true);
};
// 如果不是在根目录启动，则运行runAll方法
if(!defined('GLOBAL_START'))
{
    Worker::runAll();
}
