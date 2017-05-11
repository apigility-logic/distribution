<?php

return array(
    //'配置项'=>'配置值'
    'APP_GROUP_LIST' => 'v1',
    'DEFAULT_GROUP' => '', //项目分组设定
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'mysql',
    'DB_NAME' => 'meilibo',
    'DB_USER' => 'root',
    'DB_PWD' => 'abc123',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'ss_',
    'SALT' => 'hello_world',
    'WEB_URL' => 'http://zhibo.mimilove520.com',
    'LANG_SWITCH_ON' => true, // 不开启语言包功能，仅仅加载框架语言文件直接返回
    'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
    'DEFAULT_LANG' => 'zh-cn', // 默认语言
    'LANG_LIST'        => 'zh-cn,zh-tw,en-us', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'     => 'lang', // 默认语言切换变量

    'TOURIST_LOGIN_TOKEN' => 'the_monster',
    'TOURIST_ID' => '825',
    'GM_UID' => '825,836',   //配置推荐官方用户，用 , 号隔开
    'SESSION_PREFIX' => 'PHPCHAT_SESSION_',
    'MEM_CACHE' => array(
        'expired_time' => 24 * 60 * 60 * 30,
        'mem_server' => '127.0.0.1:11211',
    ),
    'ROBOT'=>array(
            // 永久在线房间列表

            '1333979551' => 'http://o9mkwt4eu.bkt.clouddn.com/1576146834.mp4',
            '1962399804' => 'http://o9mkwt4eu.bkt.clouddn.com/1576146834.mp4',
	    '1601285048' => 'http://o9mkwt4eu.bkt.clouddn.com/1576146834.mp4',	
	),
    'URL_CASE_INSENSITIVE' => true,
    'ROOM_ONLINE_NUM_PREFIX' => 'ROOM_ONLINE_NUM_',  // 房间在线人数 前缀.
    'ROOM_CLIENTS_PREFIX' => 'ROOM_SORTED_CLIENTS_', // 房间在线列表 前缀.
    'ROOM_LIST' => 'MEILIBO_ALL_CHATROOM_LIST',  // 所有房间列表.
    'HOT_ANCHOR_LIST' => 'ROOM_SORTED_HOT_LIST', // 热门主播列表.
    'ROOM_COUNT' => 'ROOM_COUNT_ALL', //所有房间数. 
    'ROOM_STATUS_PREFIX'=>'PHPCHAT_ROOM_', // 房间状态信息，房主/禁言列表等.
    'LIVE_STATUS_PREFIX' => 'ROOM_ANCHOR_ONLINE_PREFIX_',
    'REGISTER_ADDRESS' => '11.113:1236',
    'QINIU_LIVE_ROOM_ADDR' => 'QINIU_DEMO_ADDR_XXXXXX_',
    'JMESSAGE' => array(
        'APPKEY' => 'd6c4ba516dc1702bf597fd3d',
        'SECRET' => 'b67b8a6bc75420df78cb3799',
        ),
    'PAYMENT' => array(
        'SWITCH'=>false,
        'ATM_WEIXIN' => array(
            'APPID' => '071156b5cc72ed',
            "MCHID" => '16934602',
            'APIKEY'=> 'F6168E5DC4452A46364ACF842301B',
            'APPCECRET'=> '5cb7a87b97579902f05fce3363ae0f1f',
            'PLACE_ORDER'=>'https://api.mch.weixin.qq.com/pay/unifiedorder',
            'NOTIFY_URL'=> 'http://zhibo.mimilove520.com/OpenAPI/v1/payment/weixinNotify',
            'CASH_HTTPS'=>'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',
            'SSLCERT_PATH' => realpath(__DIR__.'/../').'/Key/apiclient_cert.pem',
            'SSLKEY_PATH' => realpath(__DIR__.'/../').'/Key/apiclient_key.pem',
            'CHECK_NAME' => 'OPTION_CHECK',
            ),
        'ALIPAY' => array(
            'SUBJECT'=>'喵榜直播秀币购买',
            'SERVICE'=>'mobile.securitypay.pay',
            'PARTNER'=>2711910502712,
            'INPUT_CHARSET'=>'utf-8',
            'SELLER_ID'=>'etvcom@163.com',
            'TRANSPORT'=>'http',
            'NOTIFY_URL'=>'http://zhibo.mimilove520.com/my/alipay_d_notify/',
            'HTTP_VERIFY_URL'=>'http://notify.alipay.com/trade/notify_query.do?',
            'HTTTS_VERIFY_URL'=>'https://mapi.alipay.com/gateway.do?service=notify_verify&',
            'CACERT'=> realpath(__DIR__.'/../').'/Key/cacert.pem',
            ),
        'APPLE' => array(
            'ENV'=>'sandbox',
            'VERIFY_URL_SANDBOX'=>'https://sandbox.itunes.apple.com/verifyReceipt',
            'VERIFY_URL_PRODUCT'=>'https://buy.itunes.apple.com/verifyReceipt',
            ),
        ),
    'QINIU'=>array(
        'HUB'=>'miaobang',
        'TITLE'=>'miao',
        'PUBLIC_KEY'=>'8b7b1c0f-2b38-465c-870d-34b40f3598fb',
        'PUBLIC_SECURITY'=>'static',
        'ACCESS_KEY'=>'UjQtR44KF9GjR4sr7pfYE6YAkLcJKcHAu2cgXZNj',
        'SECRET_KEY'=>'4val2k94jipFkX3w_kpWmUNaBeizozAukyBp51od',
        ),
    'WEIXIN_OPEN' => array(
        'APPID' => 'wx382569729fa1f25f',
        'APPSECRET'=> 'ca89764e8ef2e257e006346a0152b2c4',
    ),
   "SEND_MSG_SECRET_KEY"=> '5252b7f1f0ef4faee628d525afdfe2ba',
);

