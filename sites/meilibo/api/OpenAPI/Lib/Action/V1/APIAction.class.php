<?php
/**
* API 供PC端使用
*
*/
class APIAction extends BaseAction {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	* 内部接口给PC端调用生成七牛流地址
	*
	* @param int $roomID
	*/
	public function interQiniuStream($roomID)
	{
		if (!localRequest() || !$this->isPost()) {
			$this->responseError(L('_INTERNAL_INTERFACE_'));
		}

		$qiniu = new QiniuAction();
		$qiniu->getPullAddress($roomID);
	}

}