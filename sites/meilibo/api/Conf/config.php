<?php

return array(
    'APP_DEBUG' => true,
    'DB_FIELD_CACHE' => false,
    'HTML_CACHE_ON' => false,
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'mysql',
    'WEB_URL' => 'http://zhibo.mimilove520.com',
    'DB_NAME' => 'meilibo',
    'DB_USER' => 'root',
    'DB_PWD' => 'abc123',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'ss_',
    'SHOW_ERROR_MSG' => true,
    'HTML_CACHE_ON' => '0',
    'HTML_CACHE_RULES' => array(
        '*' => array(
          0 => '{$_SERVER.REQUEST_URI|md5}',
          1 => 300,
        ),
    ),
    'HTML_CACHE_TIME' => '605',
    'HTML_READ_TYPE' => '0',
    'HTML_FILE_SUFFIX' => '.html',
    'TMPL_ACTION_ERROR' => 'Public:error',
    'TMPL_ACTION_SUCCESS' => 'Public:success',
    'DEFAULT_THEME' => '168',

    'MEM_CACHE' => array(
        'expired_time' => 24 * 60 * 60 * 30,
        'mem_server' => 'memcached:11211',
    ),

    'ROOM_ONLINE_NUM_PREFIX' => 'ROOM_ONLINE_NUM_',  // 房间在线人数 前缀.
    'ROOM_CLIENTS_PREFIX' => 'ROOM_SORTED_CLIENTS_', // 房间在线列表 前缀.
    'ROOM_LIST' => 'MEILIBO_ALL_CHATROOM_LIST',  // 所有房间列表.
    'HOT_ANCHOR_LIST' => 'ROOM_SORTED_HOT_LIST', // 热门主播列表.
    'ROOM_COUNT' => 'ROOM_COUNT_ALL', //所有房间数.
    'LOAD_EXT_CONFIG' => 'oauth,wechat',
    'PHP_CHAT_SESSION_PREFIX' => 'PHPCHAT_SESSIONP',

    'WXCASH_URL' => 'http://zhibo.mimilove520.com/OpenAPI/V1/Payment/appWeixinCash',
    'ATM_WEIXIN' => array(
          'APPID' => 'wx156b5cc72ed',
          "MCHID" => '1934602',
          'APIKEY'=> 'D6168E5DC4452A46364ACF842301B',
          'APPCECRET'=> '5c7b97579902f05fce3363ae0f1f',
          'PLACE_ORDER'=>'https://api.mch.weixin.qq.com/pay/unifiedorder',
          'NOTIFY_URL'=> 'http://zhibo.mimilove520.com/OpenAPI/v1/payment/weixinNotify',
          'CASH_HTTPS'=>'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',
          'SSLCERT_PATH' => realpath(__DIR__.'/../').'/Key/apiclient_cert.pem',
          'SSLKEY_PATH' => realpath(__DIR__.'/../').'/Key/apiclient_key.pem',
          'CHECK_NAME' => 'OPTION_CHECK',
    ),
    'WEIXIN_OPEN' => array(
        'APPID' => 'wx9729fa1f25f',
        'APPSECRET'=> 'c764e8ef2e257e006346a0152b2c4',
    
    ),
);
