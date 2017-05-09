<?php
/**
*Mem 相关
*/
namespace GatewayWorker\Lib;


class Mem
{
	/*
	*Mem 实例
	*/
	protected static $instance = null;

	/*
	*Config
	*/
	protected static $config = array();
	public function __construct()
	{
	}

	public static function init()
	{
		if (!static::$instance) {
			static::$instance = new \Memcache();
		}
		self::connect();
	}

	protected static function config()
	{
		static::$config = (array) new \Config\Mem();
	}

	protected static function connect() 
	{
		self::config();
		try {
			static::$instance->connect(static::$config['mem_host'], static::$config['mem_port']);
		} catch(\Exception $e) { }
	}

	public static function __callStatic($method, $params)
	{
		if (!static::$instance) {
			self::init();
		}
		try {
			$ret = call_user_func_array(array(static::$instance, $method),$params);
			return $ret;
		} catch(\Exception $e) {
			echo $e->getMessage();
		}
	}
}