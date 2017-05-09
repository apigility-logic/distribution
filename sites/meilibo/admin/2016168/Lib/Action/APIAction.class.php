<?php

class APIAction extends Action {

    /**
    * API Instance
    */
    public static $instance = null;

	/**
	* Gateway 路劲
	*/
	private static $_gatewayPath = '/GatewayClient/Gateway.php';

	/**
	* Gateway 注册地址
	*/
	private $_registerAddress ;

	public static function Instance()
	{
		if (self::$instance) {
			return self::$instance;
		}


		require_once APP_PATH . 'config.inc.php';
		if (!isset($register_address) || !$register_address) {
			throw new Exception('Register 地址为空');
		}

		self::$instance = new self();
		self::$instance->_registerAddress = $register_address;

		return self::$instance;
	}

	public function Gateway($method,$param)
	{
		require_once APP_VENDOR . self::$_gatewayPath;

		Gateway::$registerAddress = $this->_registerAddress;
		call_user_func_array(array('Gateway',$method), $param);
	}
	
}
