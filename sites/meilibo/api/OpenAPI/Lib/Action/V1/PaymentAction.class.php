<?php
class PaymentAction extends BaseAction {
	protected $default_msg = array(
        'category' => 'gift_api',
        'ref' => '礼物相关API',
        'links' => array(
            'gift_collection_url' => array(
                'href' => 'v1/payment/',
                'ref' => '支付接口',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required'),
            ),
        ),
    );

	/**
	* 充值比例
	*/
	protected static $list ;

	/**
	* 微信支付相关配置
	* @var array
	*/
	protected static $configWeiXin = array();

	/**
	* 支付接口 开关，方便上架AppStore
	*/
	public function paySwitch()
	{
		if (!C('PAYMENT.SWITCH')) {
			$this->responseError('off');
		}
		
		$this->responseSuccess('on');
	}

	/**
	* 支付宝支付相关配置
	*/
	protected static $configAli = array();

	/*
	*微信提现
	*/
	protected static $configATMWeiXin = array();

	public function __construct()
	{

		parent::__construct();
		self::$list = M("charge")->order("chargeid asc")->getField('rmb, diamond+present');
		static::$configWeiXin = C('PAYMENT.ATM_WEIXIN');
		static::$configATMWeiXin = C('PAYMENT.ATM_WEIXIN');
		static::$configAli = C('PAYMENT.ALIPAY');
	}

	/**
	* 微信APP支付接口
	*
	* @param string $token
	* @param int    $num  金额，RMB
	*/
	public function appWeixin($token, $num)
	{
		// $num = 1;
		// $num *= 100;
		$userInfo = TokenHelper::getInstance()->get($token);
		$result = $this->createWixinOrder($this->createOrder($userInfo['uid'], $num), $num * 100);

		if (strcmp($result['RETURN_CODE'], 'FAIL') === 0) {
			$this->responseError($result['RETURN_MSG']);
		}

		$this->responseSuccess($this->initPrepayData($result));
	}

	/**
	* 微信APP提现接口
	*
	* @param string $token
	* @param int    $cash  金额，RMB
	* @param string    $openid 	授权获得openid，必须
	* @param string    $realname 	提现是输入的真实姓名，必须与实名认证一致,可不填
	*/
	public function appWeixinCash($cash = 0, $openid = null)
	{
		// $this->responseError("该功能屏蔽");
		if (!$this->isPost() && !APP_DEBUG) {
			$this->responseError(L('_MUST_BE_POST_'));
		}
		if($openid == null){
			$this->responseError(L('_AUTH_WECHAT_'));
		}		
		if(empty($cash) && $cash  < 100){
			$this->responseError(L('_WECHAT_CASH_COIN_'));
		}
		$member = M('member');
		$userInfo = $member->where('wxopenid = "'.$openid.'"')->field('id, beanbalance')->find();
		if( count($userInfo) == 0) {
			$this->responseError(L('_AUTH_WECHAT_'));
		}
		// $translate = M('earncash')->where('uid = '.$userInfo['id'].' and time > '.strtotime('-1 month'))->select();
		// if(count($translate) > 0 ){
		// 	$this->responseError(L('_WECHAT_CASH_F_'));
		// }
		
		if($cash > $userInfo['beanbalance'] ){
			$this->responseError(L('_NO_COIN_'));
		}
		$member->startTrans();
		$probability = M('siteconfig')->where('id=1')->field('emceededuct, cash_proportion')->find();
		$beanbalance = $member->where('id='.$userInfo['id'])->setDec('beanbalance',round( $cash/( $probability['cash_proportion']/100) ) );
		//$beanorignal = $member->where('id='.$userInfo['id'])->setDec('beanorignal',intval($cash/($probability['emceededuct']/100)/($probability['cash_proportion']/100)));
		$earncash_data = array(
			'uid'	=>	$userInfo['id'],
			'cash'	=>	round($cash, 2),
			'time'	=>	time(),
			'status'	=>	'待审核',
			'type'	=>	'2',
		);
		$createEarncash = M('earncash')->data($earncash_data)->add();
		if($beanbalance != false && count($probability) > 0 && $createEarncash > 0){// && $beanorignal != false
			$member->commit();
			$this->responseSuccess(L('_CASH_SUCCESS_'));
		}else{
			$member->rollback();
		}

		$this->responseSuccess(L('_CASH_SUCCESS_'));
	}

	/**
	* 支付宝APP支付接口
	*
	* @param string $token
	* @param int $num 金额 RMB
	*/
	public function aliPay($token, $num = 1)
	{
		$userInfo = TokenHelper::getInstance()->get($token);
		$orderNo = $this->createOrder($userInfo['uid'], $num, 0);
		$data = $this->initAlipayData($orderNo, $num);
	}

	/**
	* 支付宝异步回调
	*/
	public function aliNotify()
	{
		$params = $_POST;
		if (empty($params) || !isset($params['out_trade_no']) || $params['seller_id'] != self::$configAli['PARTNER'] || $params['trade_status'] != 'TRADE_SUCCESS') {	echo "fail";return;}

		if ($this->getSignVerify($params) && OrderAction::updateOrderFromAli($params['trade_no'], $params['out_trade_no'], $params['total_amount'])) {
			echo "success";
		} else {
			echo "fail";
		}
	}
	
	/**
	 * 验证签名
	 * @param array $params
	 * @return bool
	 */
        protected function getSignVerify($params = array())
        {
		$sign = $params['sign'];
		unset($params['sign_type']);
		unset($params['sign']);
		$params = $this->argSort($params);
		$str = '';
		foreach($params as $key => $value) {
			$str = $str . "{$key}=" . $value . "&";
		}
		$str = substr($str, 0, -1);
		return $this->rsaVerify($str, $sign);
        }
	
	/**
	 * 支付宝签名验证
	 * @param string $str 待验证字符串
	 * @param string $sign 签名结果
	 * @return bool
	 */
	protected function rsaVerify($str = '', $sign = '')
	{
		$public = openssl_pkey_get_public(file_get_contents(realpath(APP_PATH).'/Key/rsa_public_key.pem'));
		$verify = openssl_verify($str, base64_decode($sign), $public);
		openssl_free_key($public);

		return $verify  == 1 ? true : false;	
	}
	/**
     * 获取远程服务器ATN结果,验证返回URL
     * @param $notify_id 通知校验ID
     * @return 服务器ATN结果
     * 验证结果集：
     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
     * true 返回正确信息
     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
     */
	protected function getResponse($notify_id) {
		$config = C('PAYMENT.ALIPAY');
		$transport = strtolower(trim($config['TRANSPORT']));
		$partner = trim($config['PARTNER']);
		$veryfy_url = '';
		if($transport == 'https') {
			$veryfy_url = $config['HTTPS_VERIFY_URL'];
		}
		else {
			$veryfy_url = $config['HTTP_VERIFY_URL'];
		}
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = getHttpResponseGET($veryfy_url, $config['CACERT']);

		return $responseTxt;
	}

	/**
	* 微信扫码支付
	*
	* @param int $num 金额
	*/
	public function binaryCodeWeixin($num)
	{
		$result = $this->createWixinOrder($this->createOrder($num, 1), $num);
		if (strcmp($result['return_code'], 'FAIL') === 0) {
			// $this->responseError($result['return_msg']);
		}

		require_once realpath(__DIR__ .'/../../../').'/Extension/phpqrcode/phpqrcode.php';
		$pngUrl = 'http://meilibo.cxtv.kaduoxq.com';
		if (isset($result['code_url'])) {
			$pngUrl = $result['code_url'];
		}
		Qrcode::png($pngUrl);
	}

	/**
	* 统一下单接口
	*
	* @param int    $num  金额，RMB
	* @param int    $type 支付平台 0:支付宝 1:微信
	*/
	protected function createOrder($uid, $num, $type = 0)
	{

		$orderTime = time();
		$orderNo = "{$uid}_{$uid}_".time().rand(99,99999);
		$coin = !isset(self::$list[$num]) ? 0 : self::$list[$num];
		if (!( $id = OrderAction::createConsumerOrder($uid, $orderNo, $orderTime, $num, $coin, $type))) {
			$this->responseError(L('_CREATE_ORDER_FAILED_'));
		}

		return $id . "_" . $orderNo;
	}

	/**
	* 向微信请求订单生成API
	*
	* @param int $orderNo
	* @param int $num
	*/
	public function createWixinOrder($orderNo, $num)
	{
		$xml = $this->initOrderData($orderNo ,$num);
		$response = $this->postXmlCurl($xml);
		$result = $this->xmlToArr($response);
		return $result;
	}
	/**
	* 调用支付接口
	*
	* @param array $pre
	*/
	protected function initPrepayData($prepayData)
	{
		$appData = array(
			'appid' => $prepayData['APPID'],
			'partnerid' => $prepayData['MCH_ID'],
			'prepayid' => $prepayData['PREPAY_ID'],
			'package' => 'Sign=WXPay',
			'noncestr' => $this->getRandomStr(),
			'timestamp' => time()."",
			);

		ksort($appData);
		$str = $this->arrayToKeyValueString($appData);
		$appData['sign'] = $this->getSign($str);
		return $appData;
	}

	/**
	* 初始化请求支付宝的数据，返回给APP
	*
	* @param int $orderNo
	*/
	public function initAlipayData($orderNo, $num)
	{
		$param = array(
			'app_id'=>static::$configAli['APP_ID'],
			'biz_content'=>array(
				'timeout_express'=>'90m',
				'seller_id'=>self::$configAli['SELLER_ID'],
				'product_code'=>'QUICK_MSECURITY_PAY',
				//'total_amount'=>sprintf("%.2f", $num),
				'total_amount'=>'0.01',
				'subject'=>L('_BUY_COIN_'),
				'out_trade_no'=>$orderNo,
				),
			'charset'=>'utf-8',
			'method'=>'alipay.trade.app.pay',
			'notify_url'=>static::$configAli['NOTIFY_URL'],
			'sign_type'=>'RSA',
			'timestamp'=>date('Y-m-d h:i:s', time()),
			'version'=>'1.0',
			);
		$param = $this->argSort($this->paraFilter($param));
		$test = "";
		while (list ($key, $val) = each ($param)) {
			$test = $test . $key."=".urlencode($val)."&";
		}
		
		$paramStr = $this->createLinkstring($param);
		$sign = urlencode($this->rsaSign($paramStr, realpath(APP_PATH).'/Key/rsa_private_key.pem'));
		$ret = $test . "sign={$sign}";
		$this->responseSuccess($ret);
	}

	/**
	* RSA签名
	* @param $data 待签名数据
	* @param $private_key_path 商户私钥文件路径
	* return 签名结果
	*/
	protected function rsaSign($data, $private_key_path) {
	    $pubKey = file_get_contents($private_key_path);
	    $res = openssl_get_privatekey($pubKey);
	    openssl_sign($data, $sign, $res);
	    openssl_free_key($res);
	    return base64_encode($sign);
	}

	/**
	* 除去数组中的空值和签名参数
	* @param $para 签名参数组
	* return 去掉空值与签名参数后的新签名参数组
	*/
	protected function paraFilter($para) {
		$para_filter = array();
		while (list ($key, $val) = each ($para)) {
			if($key == "sign" || $val == "") { // sign_type
				continue;
			} else if ($key == 'biz_content') {
				$para_filter[$key] = json_encode($para[$key]);
			} else {
				$para_filter[$key] = $para[$key];
			}
		}
		return $para_filter;
	}

	/**
	* 对数组排序
	* @param $para 排序前的数组
	* return 排序后的数组
	*/
	protected function argSort($para) {
		ksort($para);
		reset($para);
		return $para;
	}

	/**
	* 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
	* @param $para 需要拼接的数组
	* return 拼接完成以后的字符串
	*/
	protected function createLinkstring($para) {
		$arg  = "";
		while (list ($key, $val) = each ($para)) {
			$arg.=$key."=".$val."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);

		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

		return $arg;
	}


	/**
	* 微信支付回调
	*/
	public function weixinNotify()
	{
		// 获取POST数据
		$asynResp = file_get_contents("php://input");
		// 解析XML文件
		$response = pregWeixinData($asynResp);
		if (!is_array($response) || $response['result_code'] != 'SUCCESS') {
			return ;
		}
		// 更新订单信息，并更新余额
		OrderAction::updateOrderFromWeixin($response['transaction_id'], $response['out_trade_no'], $response['total_fee']/100);
		$returnArr = array('return_code'=>'SUCCESS','return_msg'=>'OK');
		echo $this->arrToXML($returnArr, true);
	}

	protected function initOrderData($out_trade_no, $total_free)
	{
		$nonce_str = $this->getRandomStr();
		$param = array(
			'appid'=> static::$configWeiXin['APPID'],
			'body'=>L('_LIVE_'),
			'detail'=>L('_BUY_COIN_'),
			'fee_type'=> 'CNY',
			'mch_id'=> static::$configWeiXin['MCHID'],
			'nonce_str'=>$this->getRandomStr(),
			'notify_url'=>static::$configWeiXin['NOTIFY_URL'],
			'out_trade_no'=> $out_trade_no,
			'spbill_create_ip'=>$_SERVER["REMOTE_ADDR"],
			'time_expire'=>date("YmdHms",strtotime("+2 hours")),
			'time_start'=>date("YmdHms"),
			'total_fee'=> $total_free,
			'trade_type'=>'APP',
			);
		$str = $this->arrayToKeyValueString($param);
		$param['sign'] = $this->getSign($str);
		return $this->arrToXML($param);
	}

	/**
	*	初始化提现数据
	*								----------------------------
	*/
	protected function initCashOrderData($openid, $realname, $cash, $uid)
	{
		$nonce_str = $this->getRandomStr();
		$param = array(
			'amount' => $cash,
			'check_name'=> static::$configATMWeiXin['CHECK_NAME'],
			'desc'=>L('_LIVE_CASH_'),
			'mch_appid'=> static::$configATMWeiXin['APPID'],
			'mchid'=> static::$configATMWeiXin['MCHID'],
			'nonce_str'=>$this->getRandomStr(),
			'openid'=> $openid,
			'partner_trade_no'=> $this->createOrderNo($uid),
			're_user_name'=> $realname,
			'spbill_create_ip'=>$_SERVER["REMOTE_ADDR"],
			);
		$str = $this->arrayToKeyValueString($param);
		$param['sign'] = $this->getSign($str);
		return $this->arrToXML($param);
	}
	protected function createOrderNo($uid){
	   	do {
            $orderNo = date('YmdHis').$uid.rand(100,999);
        }while(count(M('earncach')->where('selfOrderNo="'.$orderNo.'"')->select()) == 0 );
        return $orderNo;
	}
	/**
	* 数组转XML
	*/
	protected function arrToXML($param, $cdata = false)
	{
		$xml = "<xml>";
		$cdataPrefix = $cdataSuffix = '';
		if ($cdata) {
			$cdataPrefix = '<![CDATA[';
			$cdataSuffix = ']]>';
		}

		foreach($param as $key => $value) {
			$xml .= "<{$key}>{$cdataPrefix}{$value}{$cdataSuffix}</$key>";
		}
		$xml .= "</xml>";

		return $xml;
	}
	/**
	* XML转数组
	* 数组格式 array('大写xml的tag'	=>	'xml的value');
	* 数组所有键为大写！！！-----重要！
	*/
	protected function xmlToArr($xml)
	{
		$parser = xml_parser_create();
        xml_parse_into_struct($parser, $xml, $data, $index);
		$arr = array();
		foreach ($data as $key => $value) {
            $arr[$value['tag']] = $value['value'];
        }
		return $arr;
	}
	/**
	* 获取签名
	*/
	public function getSign($str)
	{
		$str = $this->joinApiKey($str);
		return strtoupper(md5($str));
	}
	/**
	* 拼接API密钥
	*								----------------------------
	*/
	protected function joinApiKey($str)
	{
		return $str . "key=".static::$configATMWeiXin['APIKEY'];
		// return $str . "key=D4FF6168E5DC4452A46364ACF842301B";
	}

	protected function arrayToKeyValueString($param)
	{
		$str = '';
		foreach($param as $key => $value) {
			$str = $str . $key .'=' . $value . '&';
		}
		return $str;
	}

	protected function getRandomStr()
	{
		return md5('meilibo' . microtime() . 'weixin' . rand(100,9999));
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 *
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws WxPayException
	 *								----------------------------
	 */
	private static function postXmlCurl($xml, $useCert = false, $second = 30)
	{

		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

		//如果有配置代理这里就设置代理
		/*
		if(static::$configWeiXin['CURL_PROXY_HOST'] != "0.0.0.0" && static::$configWeiXin['CURL_PROXY_PORT'] != 0){
			curl_setopt($ch,CURLOPT_PROXY, static::$configWeiXin['CURL_PROXY_HOST']);
			curl_setopt($ch,CURLOPT_PROXYPORT, static::$configWeiXin['CURL_PROXY_PORT']);
		}
		*/
		if($useCert == true){
			curl_setopt($ch,CURLOPT_URL,  static::$configATMWeiXin['CASH_HTTPS']);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, static::$configATMWeiXin['SSLCERT_PATH']);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, static::$configATMWeiXin['SSLKEY_PATH']);
		}else{
			curl_setopt($ch,CURLOPT_URL, static::$configWeiXin['PLACE_ORDER']);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
			//post提交方式
			curl_setopt($ch, CURLOPT_POST, TRUE);
			//设置header
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
		}
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			// throw new Exception("curl出错，错误码:$error");
			return "<xml><return_code>FAIL</return_code><return_msg>".L('_NOT_SUPPERT_')."</return_msg></xml>";
		}
	}

	/**
    * 微信授权
    *
    */
    public function weixinCallback( $code = 0, $state = 0 ) {
       	if($code != 0){
       		$config = M('Siteconfig')->find();
			$domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';
			if( count( $arr = explode("|", $state) ) ){
				$state = $arr[0];
				$curroomnum = $arr[1];
			}
       		switch ( $state ) {
       			case 'weixinCashCallback':
       				$appid = static::$configATMWeiXin['APPID'];
					$AppSecret = static::$configATMWeiXin['APPCECRET'];
			   		$data = array(
			   			"appid" => $appid,
			   			"secret" => $AppSecret,
			   			"code" => $code,
			   			"grant_type" => "authorization_code"
			   			);
					$url = "https://api.weixin.qq.com/sns/oauth2/access_token";
			   		$back_url = $domain."/app/weixinCash";
			   		$err_url = $domain."/app/weixinAuth";
					$json = $this->curlRequest($url, false, $data);
					$json = json_decode($json);
					//$save['wxopenid'] = $json->openid;
					$where['wxunionid'] = $json->unionid;
					// $confirm = M("member")->where( $where )->find();
					// if(empty($confirm))
					//$confirm = M("member")->where( $where )->save( $save );
					//$where['wxopenid'] = $json->openid;
					$userinfo = M("member")->where( $where )->field( "id, nickname, wxopenid, beanbalance, avatartime" )->find();
					if( count( $userinfo ) > 0 ){
						// $userinfo = M("member")->where( $where )->field( "id, nickname, wxopenid, beanbalance, avatartime" )->find();
						echo "<script>window.location.href='".$back_url."?".http_build_query($userinfo)."'</script>";
					}
					echo "<script>alert('".L('_BIND_WEIXIN_FAILED_')."');'</script>";
					break;
       			case "ShareLogin":
       				$appid = static::$configATMWeiXin['APPID'];
					$AppSecret = static::$configATMWeiXin['APPCECRET'];
			   		$data = array(
			   			"appid" => $appid,
			   			"secret" => $AppSecret,
			   			"code" => $code,
			   			"grant_type" => "authorization_code"
			   			);
					$url = "https://api.weixin.qq.com/sns/oauth2/access_token";	
					$json = $this->curlRequest($url, false, $data);
					$json = json_decode($json);
					$info_data = array(
			   			"access_token" => $json->access_token,
			   			"openid" => $json->openid,
			   			"lang" => "zh_CN"
			   			);

					$info_url = "https://api.weixin.qq.com/sns/userinfo";
					$info = $this->curlRequest($info_url, false, $info_data);
					$info = json_decode($info);
					$info->web = 1;//之後區分是分享頁登錄還是APP微信登錄
					$info = json_encode($info);

					$login_data = array(
			   			"openid" => $json->openid,
			   			"type" => "wechat",
			   			"payload" => $info
			   			);


					$login_url = $domain."/OpenAPI/V1/Auth/login";
					$userinfo = $this->curlRequest($login_url, true, $login_data);
					$userinfo = json_decode($userinfo, true);

					$_SESSION['uid'] = $userinfo['data']['id'];
					$_SESSION['token'] = $userinfo['data']['token'];
					//微信大厅登录
					if($curroomnum == 'hall'){
						$back_url = $domain."/app/wx_index?uid=".$userinfo['data']['id'];
						echo "<script>window.location.href='".$back_url."'</script>";
       				    break;
					}
			   		$back_url = $domain."/app/share";
					echo "<script>window.location.href='".$back_url."?current_room=".$curroomnum."'</script>";
       				break;
       		}
        	
       	}else{
       		$this->responseError(L('_DATA_TYPE_INVALID_'));
       	}
		   
    }
    private function weixinAuth($code){
		
    }
    public function withdrawSwitch() {
            $this->responseSuccess('off');
    }
    /*
    *第三方平台充值成功之后调用该接口 给用户加值 生成订单记录等
    *
    */
    public function thirdPayBack() {
    	/*
    	method     POST
    	uid        用户id 必须
		orderNo    三方平台订单号 必须 string
		cash       充值金额 必须 int 单位 分
		coin       充值虚拟币 必须 int 
		platform   三方平台名称 必须 string
    	*/
    	$uid = $_POST['uid']; 
    	$orderNo = $_POST['orderNo']; 
    	$cash = $_POST['cash']; 
    	$coin = $_POST['coin'];
    	$platform = $_POST['platform'];

    	if(empty($uid)){
    		$this->responseError('用户ID错误');
    	}
    	if(empty($orderNo)){
    		$this->responseError('订单号为空');
    	}
    	if(empty($cash)){
    		$this->responseError('金额为空');
    	}
    	if(empty($platform)){
    		$this->responseError('平台名称为空');
    	}
    	$userInfo = M('member')->where('id = '.$uid)->find();
    	if(empty($userInfo)){
    		$this->responseError('用户ID错误');
    	}

		$charge = D('Chargedetail');
		$charge->create();
		$charge->uid = $uid;
		$charge->touid = $uid;
		$charge->rmb = $cash/100;
		$charge->coin = !$coin ? 0 : $coin;
		$charge->status = '1';
		$charge->addtime = time();
		$charge->orderno = $orderNo;
		$charge->proxyuid = 0;
		$charge->content = $platform;
		$charge->dealid = 0;
		$charge->platform = 3;

		$id = $charge->add();

		if($id){
			$this->responseSuccess('加值成功');
		}else{
			$this->responseError('加值失败');
		}
    }
}

