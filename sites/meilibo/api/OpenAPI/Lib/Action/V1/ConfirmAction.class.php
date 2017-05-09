<?php
/**
* 供第三方各种验证使用
*
*/
class ConfirmAction extends BaseAction {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	*
	*	验证接口
	*	
	*	@param string type
	*	@param string token
	*	@param int online
	*
	*/
	public function CYConfirm( $token = null, $type = null, $online = 0) {

		if(empty($type) || is_null($type) ) {
        	return L('_PARAM_ERROR_');
		}
		$userInfo = TokenHelper::getInstance()->get($token);
		if( $userInfo['uid'] == C("TOURIST_ID") ){
			return L('_TOURIST_SIGN_');
		}
		// $this->responseSuccess($cy_user);
		$cy_user = M("member")->where(" id = ". $userInfo['uid'] )->field('id, third_party_id, channel_id')->find();
		if( count($cy_user) > 0 ){
			$curl_url = "http://api.chaoyu.tv:8090/apilive/";
			$signkey = "FXFDTOU!G9001";
			$data = array( 
				"user_id" => $cy_user['third_party_id'],
				"member_id" => $cy_user['id'],
				);
			switch ($type) {
				case 'login':
					if ( is_numeric($cy_user['third_party_id']) ){
						$data["sign"] = md5($cy_user['third_party_id'].$cy_user['id'].$signkey);
				        $url = $this->curlRequest($curl_url."login_confirm", false, $data);
						return $this->sendConfirm($url);
					}else{
				       	return  L('_CONFIRM_NOT_BIND_') ;
				    }
					break;
				case 'channel':
					if ( $cy_user['channel_id'] != 0 && $online != 0 ) {
						$data["channel_id"] = $cy_user['channel_id'];
						$data["online"] = $online;
						$data["time"] = time();
						$data["sign"] = md5($cy_user['third_party_id'].$cy_user['id'].$cy_user['channel_id'].$online.$data['time'].$signkey);
						$url = $this->curlRequest($curl_url."channel_confirm", false, $data);
						return $this->sendConfirm($url);
					}else{
			        	return  L('_PERSONAL_LIVE_') ;
				    }
					break;
				
				default:
				    return  L('_VERIFY_FAILED_') ;
					break;
			}

		}else{
        	return  L('_USER_DOES_NOT_EXIST_');
		}
	}

	private function sendConfirm($json){
        $json = (Array)json_decode($json,true);
        if ( count($json) ) 
        {
        	return $json;
        }else
        {
        	return  L('_CHANNEL_NOT_DATA_') ;
        }
	}

}