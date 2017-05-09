<?php return array (
	'APP_DEBUG'=>true,
	'DB_FIELD_CACHE'=>false,
	'HTML_CACHE_ON'=>false,
  'DB_TYPE' => 'mysql', 
  'DB_HOST' => "#{meilibo.WEB.DBHOST}",
  'DB_NAME' => "#{meilibo.WEB.DBNAME}",
  'DB_USER' => "#{meilibo.WEB.DBUSER}",
  'DB_PWD' => "#{meilibo.WEB.DBPWD}",
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
);?>
