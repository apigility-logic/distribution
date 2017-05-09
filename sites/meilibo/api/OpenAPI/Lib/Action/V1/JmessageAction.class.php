<?php
/**
 * 极光推送API
 */
class JmessageAction extends BaseAction {
	// HOST
	private $host = 'https://api.im.jpush.cn';
	private $reportHost = 'https://report.im.jpush.cn/v2';
	private $pushHost = 'https://api.jpush.cn/v3';

	private $path = '/v1/users/';
	private $client;
	protected function init(){
		require_once APP_PATH . 'Extension/jpush-api-php/autoload.php';
		
		$this->client = new \JPush\Client(C('JMESSAGE.APPKEY'), C('JMESSAGE.SECRET'));
	}
	public function jmRegist($user)
	{
		$params = array(
        	array(
			'username' => 'meilibo'.$user['id'],
			'password' => 'meilibo'.$user['id'],
			'appkey'   => C('JMESSAGE.APPKEY'),
			'nickname' => $user['nickname'],
			'gender'   => $user['sex'] == 0 ? 1 : 2,
			)
        );
		$params = json_encode($params);
		$this->request($this->path, $params, 'POST');

		// 更新昵称
		$this->updateNickName($user);

		// 第一次登陆消息
		$this->firstLogin('meilibo'.$user['id']);
	}

	/**
	 * 更新用户信息
	 */
	public function updateNickName($user)
	{
		$params = array(
			'nickname' => $user['nickname'],
		);
		$this->request($this->path."meilibo{$user['id']}/", json_encode($params), 'PUT');
	}

	/**
	 * 添加黑名单到极光
	 */
	public function addBlack($uid,$blackUid)
	{
		$username = "meilibo".$uid;
		$res = $this->request("/v1/users/{$username}/blacklist",json_encode(array("meilibo{$blackUid}")),"PUT");
	}

	/**
	 * 删除黑名单
	 */
	public function removeBlack($uid,$blackUid)
	{
		$username = "meilibo".$uid;
		$res = $this->request("/v1/users/{$username}/blacklist",json_encode(array("meilibo{$blackUid}")),"DELETE");
	}

	/**
	 * 发起请求
	 */
	protected function request($path, $params, $method)
	{
		$header[] = 'Authorization: Basic ' . base64_encode(C('JMESSAGE.APPKEY').':'.C('JMESSAGE.SECRET'));
        $header[] = 'Accept: application/json';
        $header[] = 'Content-Type: application/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    	curl_setopt($ch, CURLOPT_URL, $this->host.$path);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    	$response = curl_exec($ch);

    	curl_close($ch);

		return $response;
	}

	public function firstLogin($username)
	{
		$data = array();
		$data['version'] = 1;
		$data['target_type'] = "single";
		$data['target_id'] = $username;
		$data['from_type'] = "admin";
		$data['from_id'] = "admin";
		$data['msg_type'] = 'text';

		$data['msg_body'] = array('extras'=>array(),'text'=>L('_JMS_FIRST_LOGIN_'));

		$res = $this->request("/v1/messages",json_encode($data),'POST');
	}

	/**
	 * 发起请求
	 */
	protected function requestReport($url, $data, $isPost)
	{
		$header[] = 'Authorization: Basic ' . base64_encode(C('JMESSAGE.APPKEY').':'.C('JMESSAGE.SECRET'));
        $header[] = 'Accept: application/json';
        $header[] = 'Content-Type: application/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);        
        } else {
            $url =  $url . '?' . http_build_query($data);
        }
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    	$response = curl_exec($ch);

    	curl_close($ch);

		return $response;
	}
	public function getChatMsgForWeb(){
		$this->getChatMsg($_POST['username'],$_POST['targetuser']);
	}	
    /**
     * 获取最近七天内的聊天记录，最多获取七天，1000条
     * @param string $username 极光管理员
     * @param string $targetuser 目标用户名极光
     * @return jsonArray 七天以前的聊天记录
     */
    public function getChatMsg( $username = null, $targetuser = null){
	    if( empty($username) ){
	            $this->responseError("用户名为空！");
	    }
	    $allChatMsg = array();
	    $path = $this->reportHost."/users/".$username."/messages";
	    // $i=0;
	    $allMsg = array();
	    $count = 1000;
	    do{
	        if( empty( $cursor ) ){
	                $data = array(
	                'count' => $count,
	                'begin_time' => date("Y-m-d H:i:s", strtotime("-1 week")),
	                'end_time' => date("Y-m-d H:i:s"),
	                );
	        }else{
	                $data = array('cursor'=>$cursor);
	        }
	        $res = $this->requestReport( $path, $data, false);
	        $messages = json_decode($res,true);
	        if( !empty( $targetuser ) ){
	            foreach ($messages['messages'] as $key => $message) {
	                if( $message['from_id'] != $targetuser && $message['target_id'] != $targetuser ){
	                    unset($messages['messages'][$key]);
	                }
	            }
	        }
	        $cursor = $messages['cursor'];
	        $allMsg = array_merge( $allMsg, $messages['messages']);
	        // if( $i++ > 10)
	        // 	break;
	   	}while ( $messages['count'] == $count ) ;
	   	$allChatMsg[$targetuser] = array(
	   		'total' => count($allMsg),
	   		'messages' => $allMsg
	   		);
 // $msg = array_slice( $allChatMsg[$targetuser]['messages'], -(($page-1)*($count)+1), $count );
 //   	$messages = array(
 //   			'total' => $allChatMsg[$targetuser]['total'],
 //   			'count' => count($msg),
 //   			'messages' => $msg,
 //   		);

	    $this->responseSuccess($allChatMsg);
	    //$this->responseSuccess($messages);
	}
	/**
	 * 推送信息
	 * @param  array $Ids 推送用户别名（我们使用ID）
	 * @return code      HTTP状态码
	 */
    public function messagePush( $Ids, $name = null){
        $this->init();
        $res = $this->client->push()
        ->setPlatform('all')
        //->addAllAudience()
        ->addAlias($Ids)
        ->setNotificationAlert('您关注的用户 '.$name.' 正在直播，快来看我直播吧！')
        ->send();
        return $res['http_code'];
	}

}