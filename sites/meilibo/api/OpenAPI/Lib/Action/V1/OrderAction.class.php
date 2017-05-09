<?php

class OrderAction extends BaseAction {

	/**
	* 生成订单
	* @param int $uid
	* @param int $time
	* @param mixed $num  RMB
	* @param int $type 支付平台 0:支付宝 1：微信
	*/
	public static function createConsumerOrder($uid, $orderNo, $time, $rmb, $coin, $type, $content = '', $dealid = 0)
	{
		$charge = D('Chargedetail');
		$charge->create();
		$charge->uid = $uid;
		$charge->touid = $uid;
		$charge->rmb = $rmb;
		$charge->coin = !$coin ? 0 : $coin;
		$charge->status = '0';
		$charge->addtime = time();
		$charge->orderno = $orderNo;
		$charge->proxyuid = 0;
		$charge->content = $content;
		$charge->dealid = $dealid;
		$charge->platform = $type;

		$id = $charge->add();

		return $id;
	}

	/**
	* 更新订单状态，已完成支付
	*
	* @param int $transaction_id  微信订单号
	* @param int $out_trade_no 系统订单号
	* @param int $total_fee 金额
	*/
	public static function updateOrderFromWeixin($trade_no, $out_trade_no, $total_fee)
	{
		static::updateOrder($trade_no, $out_trade_no, $total_fee, 1);
	}

	/**
	* 更新订单状态，已完成支付
	*
	* @param int $trade_no  支付宝订单号
	* @param int $out_trade_no 系统订单号
	* @param int $total_fee 交易金额
	*/
	public static function updateOrderFromAli($trade_no, $out_trade_no, $total_fee)
	{
			return static::updateOrder($trade_no, $out_trade_no, $total_fee, 0);
	}

	/**
	 * 更新订单
	 * @param int $platform 0:支付宝，1:微信
	 * @return bool
	 */
	protected function updateOrder($trade_no, $out_trade_no, $total_fee, $platform = 0)
	{
		//拆分订单
        $orderid = explode("_", $out_trade_no);
        $id = $orderid[0];
        $uid = $orderid[1];
        $touid = $orderid[2];
        $orderno = "{$uid}_{$touid}_{$orderid[3]}";
        // 订单是否已经处理
        $Chargedetail = M('Chargedetail');
		$exist = $Chargedetail->where(array('id'=>$id, 'status'=>'1', 'orderno'=>$orderno))->getField('id');

		if ($exist != '') {
			return false;
		}

        $data = array('status'=>'1', 'dealid'=>$trade_no, 'platform'=>$platform);
        //更新记录
        $Chargedetail->where(array('id'=>$id))->save($data);
        $coin = $Chargedetail->where(array('id'=>$id, 'status'=>'1', 'orderno'=>$orderno))->getField('coin');
        $Member = M("Member");
        $Member->execute('update ss_member set coinbalance=coinbalance+' . $coin . ' where id=' . $touid);

        $marketid = $Member->where("id=$touid")->getField("marketid");
        $market = $Member->where("id=". $marketid)->field("id, coinbalance, ratio")->find();
        if( $market['ratio'] > 0 ){
        	$market['coinbalance'] += round ($coin * $market['ratio'] );
        }else{
        	$market['coinbalance'] += round ( $coin * M('siteconfig')->where("id=1")->getField("marketratio") );
        }
        $Member->save($market);
        $record = array(
        	'type'	=>	'expend',
        	'action'	=>	'sendgift',
        	'uid'	=>	0,
        	'touid'	=>	$marketid,
        	'giftid'=>	-1,
        	'giftcount'	=>	1,
        	'content'	=>	'系统返现：'.$market['coinbalance'],
        	'objecticon'=>	'',
        	'coin'	=>	$market['coinbalance'],
        	'showid'=>	0,
        	'addtime'	=>	time(),
        	'gtype'	=>	0
        	);
		M('coindetail')->add($record);
	return true;
	}
	/**
	* APP 查询支付结果
	*
	*/
	public function queryWeixinPayResult($token,$orderNo)
	{
		$order = M('Chargedetail');
		$userInfo = TokenHelper::getInstance()->get($token);
		$result = $order->where(array('uid'=>$userInfo['uid'], 'touid'=>$userInfo['uid'],'orderno'=>$orderNo))->find();
		if (!$result) {
			$this->responseError(L('_ORDER_DOES_NOT_EXIST_'));
		}

		// if ($result['status'] == '0') {
		// 	$this->queryFromWeixin();
		// }
		foreach($result as $key => $value) {
			if (!in_array($key, array('uid','rmb','coin','addtime','orderno','platform'))) {
				unset($result[$key]);
				continue ;
			}

			if (strcmp($key, 'addtime') == 0) {
				$result[$key] = date('Y-m-d h:i:s', $value);
			} else if (strcmp($key, 'platform') == 0) {
				$result[$key] = $value == 0 ? L('_ALIPAY_') : L('_WECHAT_');
			}
		}

		$this->responseSuccess($result);
	}

	/**
	* 调用微信查询API，查询支付接口
	*
	*/
	public function queryFromWeixin()
	{
		//TODO ...
	}

	/**
	* 更新苹果支付订单状态
	*
	* @param int $uid
	* @param int $order_id
	* @param int $purchase RMB
	* @return bool
	*/
	public static function updateOrderFromApple($uid, $order_id, $num)
	{
		$charge = M('Chargedetail');
		$charge->status = '1';
		$result1 = $charge->where(array('id'=>$order_id))->save();

		$certificate = M('Appcertificate');
		$certificate->checked = 1;
		$result2 = $certificate->where(array('order_id'=>$order_id))->save();
		if (!$result1 || !$result2) {
			return 0;
		}

		// $num = (int)($purchase * static::$ratio);
		$result = M("Member")->execute('update ss_member set coinbalance=coinbalance+' . $num . ' where id=' . $uid);
		if ($result !== 0) {
			$coinbalance = M("Member")->where(array('id'=>$uid))->getField('coinbalance');
			return $coinbalance > 0 ? $coinbalance : 0;
		}

		$charge = M('Chargedetail');
		$charge->status = '0';
		$charge->where(array('id'=>$order_id))->save();

		$certificate = M('Appcertificate');
		$certificate->checked = 0;
		$result2 = $certificate->where(array('order_id'=>$order_id))->save();
		return 0;
	}

	/**
	* 检查苹果支付凭证是否已经验证过
	* @param int $transaction_id
	*
	* @return bool
	*/
	public static function checkTrans($transaction_id = 0)
	{
		$order_id = M("Chargedetail")->where(array('dealid'=>$transaction_id, 'status'=>'1'))->getField('id');
		if (!$order_id) {
			return false;
		}

		return (bool) M('Appcertificate')->where(array('order_id'=>$order_id))->getField('checked');
	}
}
