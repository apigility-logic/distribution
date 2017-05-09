<?php
if (!defined('THINK_PATH')) exit();
return array(
'DB_TYPE'=>'mysql',
'DB_HOST'=>'mysql',
'DB_NAME'=>'meilibo',
'DB_USER'=>'root',
'DB_PWD'=>'abc123',
'DB_PORT'=>'3306',
'DB_PREFIX'=>'ss_',
'SHOW_ERROR_MSG' => true,
'REGISTER_ADDRESS' => 'meilibo_chatroom:1236',
'MEMCACHE' => 'memcached:11211',
'QINIU'=>array(
        'HUB'=>'meilibo',
        'TITLE'=>'MEI',
        'PUBLIC_KEY'=>'acea-b36b-48fa-a99b-0166b5222dbd',
        'PUBLIC_SECURITY'=>'static',
        'ACCESS_KEY'=>'OoNft9s_vAfp_Wad0sUD8SKx8OGRhnMl62M',
        'SECRET_KEY'=>'Nm6RJL2o1LNN2Tb_MQ6Jl4VIrtd-ToNyqVLsU',
        ),

);

?>
