<?php

define('APP_NAME', 'OpenAPI');
define('APP_PATH', './');
define('APP_DEBUG', true);
define("__ROOT__",realpath(__DIR__.'/../'));

function putLog($content) {
    $fd = fopen("/tmp/b.log", 'a+');
    fwrite($fd, $content . "\n");

    fclose($fd);
}

// 加载框架入口文件
foreach (glob('Common/*.php') as $file) {
    $f_info = pathinfo($file);
    if ($f_info['basename'] != 'common.php') {
        require_once ($file);
    }
}
require_once '../sign/ThinkPHP/ThinkPHP.php';
