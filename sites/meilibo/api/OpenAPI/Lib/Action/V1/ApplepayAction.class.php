<?php

/**
* 苹果支付相关接口
*
* $order_id 支付记录表自增ID
* $product_id 苹果支付返回的商品ID
* $transaction_id 苹果订单ID
* $order_num 系统内部订单号
*
*/
class ApplepayAction extends BaseAction {

	/**
	* 苹果支付比例
	*/
	protected $ratio = 0.7;

	/**
	* 充值比例
	*/
	protected static $list;

	/**
	* 苹果支付配置
	*/
	protected $config = array();

	public function __construct()
	{
		parent::__construct();
		self::$list = M("charge")->order("chargeid asc")->getField('rmb, diamond+present');
		$config = C('PAYMENT.APPLE');
		if (empty($config)) {
			throw new Exception(L('_MISS_APPLE_CONFIG_'));
		}
		$this->config = $config;
	}

	/**
	* 获取验证地址，分为沙盒和正式环境
	*
	* @return string
	*/
	protected function getVerifyUrl()
	{
		return $this->config['ENV'] == 'sandbox' ? $this->config['VERIFY_URL_SANDBOX'] : $this->config['VERIFY_URL_PRODUCT'];
	}

	/**
	* 获取商品ID
	*
	*/
	public function getProductID()
	{
		$product = array(
			array('id' => '10star', 'desc'=>'10星钻'),
			array('id' => '42star', 'desc'=>'42星钻'),
			array('id' => '210star', 'desc'=>'210星钻'),
			);

		$this->responseSuccess($product);
	}

	/**
	* 处理APP支付后发过来的数据
	*
	* @param string $token 登录凭证
	* @param int    $product_id 苹果商品号
	* @param int    $transaction_id 订单号
	* @param time   $purchase_date 购买时间
	* @param int    $purchase_num  金额
	* @param string $certificate 支付凭证
	*
	* @return SUCCESS|FAILED
	*/
	public function checkPayment($token, $product_id, $transaction_id, $purchase_date, $purchase_num, $certificate)
	{
		if (!$this->isPost() && !APP_DEBUG) {
			$this->responseError(L('_MUST_BE_POST_'));
		}

		// 是否发来购买凭证
		$msg = $this->checkTransaction($transaction_id, $certificate);
		if ($msg) {
			$this->responseError($msg);
		}

		$userinfo = TokenHelper::getInstance()->get($token);
		// $purchase_num = !isset($this->list[$purchase_num]) ? 0 : $this->list[$purchase_num]; //$this->ratio * $purchase_num;
		// 创建支付订单
		$order_id = $this->createAppleRecord($userinfo['uid'], $purchase_date, $purchase_num, $product_id, $transaction_id, $certificate);

		if (!$this->checkResponse($this->sendReceiptData($certificate))) {
			$this->responseError(L('_APPLE_CHECK_FAILED_'));
		}

		// 更新订单，并返回金币余额
		$coin = $this->updateOrder($userinfo['uid'], $order_id, $purchase_num);
		if (!$coin) {
			$this->responseError(L('_APPLE_SYSTEM_ERROR_'));
		}

		$this->responseSuccess($coin);
	}

	/**
	* @param int $transaction_id 订单号
	* @param string $certificate 支付凭证
	*
	* @return string
	*/
	protected function checkTransaction($transaction_id, $certificate)
	{
		if (!$certificate) {
			return '支付凭证为空';
		}

		$result = OrderAction::checkTrans($transaction_id) ? '支付已完成' : '';
		return $result;
	}

	/**
	* 创建苹果支付订单
	*
	* @param int $uid
	* @param time $purchase_date
	* @param int $purchase_num
	* @param int $transaction_id 订单号
	* @param int $certificate 凭证
	*
	* @return int 订单ID
	*/
	protected function createAppleRecord($uid, $purchase_date, $purchase_num, $product_id, $transaction_id, $certificate)
	{
		if (!$product_id || !$transaction_id || !$purchase_date || !$purchase_num) {
			$this->responseError(L('_PARAM_ERROR_'));
		}

		$coin = !isset(self::$list[$purchase_num]) ? 0 : self::$list[$purchase_num];
		// 系统内部订单号
		$order_num = "{$uid}_{$uid}_".time().rand(99,99999);
		// 自增订单号
		$order_id = OrderAction::createConsumerOrder($uid, $order_num, $purchase_date, $purchase_num, $coin, 2, $product_id, $transaction_id);
		//保存支付凭证
		$this->storageCertificate($order_id, $certificate);

		return $order_id;
	}

	/**
	* 更新订单
	*
	* @param int $uid
	* @param int $order_id
	* @param int $purchase_num
	*/
	protected function updateOrder($uid, $order_id, $purchase_num)
	{
		return OrderAction::updateOrderFromApple($uid, $order_id, self::$list[$purchase_num]);
	}

	/**
	* 发送数据到Appstore校验凭证
	*
	* @param string $certificat
	* @return string
	*/
	protected function sendReceiptData($certificate)
	{
		$data = json_encode(array('receipt-data'=>$certificate));
		/*
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, 'https://sandbox.itunes.apple.com/verifyReceipt');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$response = curl_exec($ch);
		curl_close($ch);
		*/

		$request = CurlRequests::Instance()->setRequestMethod('post')->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);
		$response = $request->request($this->getVerifyUrl(), $data);

        return $response;
	}

	/**
	* 校验AppStore返回数据
	*
	* @param string $response json格式，appstore返回结果
	* @return bool
	*/
	protected function checkResponse($response = '')
	{
		$response =  json_decode($response, true);
		if (!is_array($response) || empty($response)) {
			return false;
		}

		return $response['status'] === 0 ? true : false;
	}

	/**
	* 写入appstore支付凭证
	*
	* @param int $order_id
	* @param string $certificate app发过来的支付凭证
	*
	* @return bool
	*/
	public function storageCertificate($order_id, $certificate)
	{
		$Certificate = D('Appcertificate');
		$Certificate->order_id = $order_id;
		$Certificate->certificate = $certificate;
		$Certificate->checked = 0;

		return $Certificate->where(array('order_id'=>$order_id))->add();
	}

}