<?php
/**
* 电视频道相关api
*
*/
class ChannelAction extends BaseAction {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	* 获取电视频道列表
	*
	* @param string token 
	*/
	public function getChannelList ( )
	{
		$curl_url = "http://api.chaoyu.tv:8090/apilive/channel_list";
        $data = array( );
        $url = $this->curlRequest($curl_url, false, $data);
        $url = (Array)json_decode($url,true);
        if ( count($url) ) 
        {
        	$this->responseSuccess( $url );
        }
        else
        {
        	$this->responseError( L('_CHANNEL_NOT_DATA_'));
        }
	}


}