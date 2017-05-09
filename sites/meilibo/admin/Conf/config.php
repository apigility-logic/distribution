<?php return array (
	'APP_DEBUG'=>true,
	'DB_FIELD_CACHE'=>false,
	'HTML_CACHE_ON'=>false,
  'DB_TYPE' => 'mysql', 
  'DB_HOST' => '127.0.0.1',
  'DB_NAME' => 'meilibo',
  'DB_USER' => 'root',
  'DB_PWD' => '120e1f3958',
  'DB_PORT' => '3306',
  'DB_PREFIX' => 'ss_',
  'SHOW_ERROR_MSG' => true,
  'HTML_CACHE_ON' => '0',
  'HTML_CACHE_RULES' => 
  array (
    '*' => 
    array (
      0 => '{$_SERVER.REQUEST_URI|md5}',
      1 => 300,
    ),
  ),
  'HTML_CACHE_TIME' => '605',
  'HTML_READ_TYPE' => '0',
  'HTML_FILE_SUFFIX' => '.html',
  'TMPL_ACTION_ERROR' => 'Public:error',
  'TMPL_ACTION_SUCCESS' => 'Public:success', 
  'DEFAULT_THEME' => 'Newtpl',
  'PAYMENT' => array(
    /*'WEIXIN' => array(
        'APPID' => 'wx6602759b44b3f81f',
        "MCHID" => '1228769502',
        'APIKEY'=> 'D4FF6168E5DC4452A46364ACF842301B',
        'PLACE_ORDER'=>'https://api.mch.weixin.qq.com/pay/unifiedorder',
        'NOTIFY_URL'=> 'http://demo.meilibo.net/OpenAPI/v1/payment/weixinNotify',
        ),
    */
    'ATM_WEIXIN' => array(
        'APPID' => 'wxb3071156b5cc72ed',
        "MCHID" => '1366934602',
        'APIKEY'=> 'D4FF6168E5DC4452A46364ACF842301B',
        'PLACE_ORDER'=>'https://api.mch.weixin.qq.com/pay/unifiedorder',
        'NOTIFY_URL'=> 'http://demo.meilibo.net/OpenAPI/v1/payment/weixinNotify',
        'CASH_HTTPS'=>'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',
        'SSLCERT_PATH' => realpath(__DIR__.'/../').'/Key/apiclient_cert.pem',
        'SSLKEY_PATH' => realpath(__DIR__.'/../').'/Key/apiclient_key.pem',
        'CHECK_NAME' => 'OPTION_CHECK',
        ),
  ),

);?>
