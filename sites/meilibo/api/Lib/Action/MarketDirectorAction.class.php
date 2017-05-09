<?php
/**
* 市场推广连接
*/

class MarketDirectorAction extends BaseAction {
	/**
	* 生成推广连接
	*/
	public function generateMarketUrl()
	{
		if (!isset($_SESSION['uid']) || !$_SESSION['uid']) {
			$this->error('您还未登录');
		}
		$marketUrl = $this->domainroot . '/Index/index?marketuid=' . $_SESSION['uid'];
		$this->assign('marketUrl', $marketUrl);
		$this->display();
	}
}