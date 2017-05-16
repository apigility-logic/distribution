<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用行为扩展定义文件
return [
    // 应用初始化
    'app_init'     => [
        'app\\common\\behavior\\AppInit'
    ],
    // 应用开始
    'app_begin'    => [
        'app\\common\\behavior\\AppBegin'
    ],
    // 模块初始化
    'module_init'  => [
        'app\\common\\behavior\\ModuleInit'
    ],
    // 操作开始执行
    'action_begin' => [
        'app\\common\\behavior\\ActionBegin'
    ],
    // 视图内容过滤
    'view_filter'  => [
        'app\\common\\behavior\\ViewFilter'
    ],
    // 日志写入
    'log_write'    => [
        'app\\common\\behavior\\LogWrite'
    ],
    // 应用结束
    'app_end'      => [
        'app\\common\\behavior\\AppEnd'
    ],
    // 输出结束标签位（V5.0.1+）
    'response_end' => [
        'app\\common\\behavior\\ResponseEnd'
    ]
];
