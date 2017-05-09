<?php
/**
* 七牛上传
*/
class QiniuUploadAction extends BaseAction {
	private $token = null;
	public function __construct()
	{
		parent::__construct();
	}
	protected function init()
	{
		require_once APP_PATH . 'Extension/Qiniu/functions.php';
		use Qiniu\Auth;
  		use Qiniu\Storage\UploadManager;
		//初始化鉴权对象
		$auth = new Auth(C('QINIU.ACCESS_KEY'), C('QINIU.SECRET_KEY'));
		//空间名
		$bucket = "";
		//回调数组
		$backData = array(
			'callbackUrl'=>'http://www.cxzfb.cn/index.php',
			'callbackBody' => '{"fname":"$(fname)", "fkey":"$(key)", "desc":"$(x:desc)", "uid":' . $uid . '}'
			);
		$this->token = $auth->uploadToken($bucket,null,3600,$backData);
	}
	public function uploadTest(){
		$this->init();
		echo $this->token;
	}

}