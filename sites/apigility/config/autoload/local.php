<?php
return [
    // 配置doctrine2数据库连接
    'doctrine' => array(
        'connection' => array(
            // default connection name
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'mysql',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'abc123',
                    'dbname'   => 'apigility',
                )
            )
        )
    )
];
