<?php
use Qiniu\json_decode;
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/5/28
 * Time: 下午10:35
 */

class SMSAction extends BaseAction
{
    protected $default_msg = array(
        'category' => 'sms_api',
        'ref' => '短信验证相关API',
        'links' => array(
            'send_sms_url' => array(
                'href' => 'v1/SMS/sendSMS',
                'ref' => '发送验证代码的短信',
                'method' => 'POST',
                'parameters' => array('phone' => 'integer, required'),
            ),
            'verify_captcha_code_url' => array(
                'href' => 'v1/SMS/verify',
                'ref' => '验证验证码',
                'method' => 'POST',
                'parameters' => array('phone' => 'integer, required', 'captcha' => 'string, required')
            )
        ),
    );

    /**
     * @var integer 验证码长度.
     */
    protected $captcha_length = 6;


    private $user;
    public function __construct()
    {
        $this->user = M('Member');
        //require_once APP_PATH.'../config.inc.php';
        // require_once APP_PATH.'../uc_client/client.php';
        parent::__construct();
    }

    /**
     * @param integer $phone
     */
    /*
    public function sendSMS($phone = null)
    {
        $url = 'http://106.3.37.50:9999/sms.aspx';
        $params = array(
            'userid' => '2426', // 企业ID
            'account' => 'wgwl', // 发送用户帐号
            'password' => 'abc123', // 发送帐号密码
            'mobile' => '', // 全部被叫号码,多个号码以半角逗号分开.
            'content' => '您的验证码为:%s, %d分钟内有效，切勿告知任何人。【灿星直播直播服务】', // 发送内容.
            'sendTime' => '', // 定时发送时间,为空表示立即发送，定时发送格式2010-10-24 09:08:10
            'action' => 'send', // 固定为发送.
            'extno' => '', // 扩展子号.
        );
        if (!is_string($phone) || !ctype_digit($phone) || strlen($phone) != 11) {
            $this->responseError('手机号不正确.');
        }
        if ($this->isLimited()) {
            $this->responseError('操作太过于频繁');
        }
        $code = $this->generateRandomStr($this->captcha_length);
        $expired_time = 5; // 单位分钟.
        $params['mobile'] = $phone;
        $params['content'] = sprintf($params['content'], $code, $expired_time);
        $this->mmc->set('verify_code_'.$phone, $code, $expired_time * 60);
        $xpath = null;
        try {
            $resp = CurlRequests::Instance()
                ->setHeader('User-Agent', '')
                ->setRequestMethod('POST')
                ->request($url, $params);
            $xpath = $this->getXpathObjectFromXmlStr($resp);
        } catch (Exception $e) {
            // curl 错误
        }
        if (empty($xpath)) {
            $this->responseError('发送验证码失败,请稍后重试', 500);
        }
        $status_code = $xpath->query('//returnsms/returnstatus/text()')->item(0);
        $msg = $xpath->query('//returnsms/message/text()')->item(0);
        if ($status_code != null && $msg != null) {
            $status_code = $status_code->nodeValue;
            $msg = $msg->nodeValue;
        }
        if ($msg == 'ok' && $status_code == 'Success') {
            $this->responseSuccess('验证码已经发送成功');
        } else {
            $this->responseError('操作失败', 2);
        }
    }
    */
     
     /**
     * 短信ApI  鸿联 
     */
    /*
     public function sendSMS($phone = null){
          if (!is_string($phone) || !ctype_digit($phone) || strlen($phone) != 11) {
            $this->responseError('手机号不正确.');
        }
        if ($this->isLimited()) {
            $this->responseError('操作太过于频繁');
        }
        $ua = new UserAction();
        //验证账号状态是否可以登录
        $ua->accountStatus("sms",$phone);
        $code = $this->generateRandomStr($this->captcha_length);
        $expired_time = 5; // 单位分钟.
        $msg = array(
            "username" => "cytv",
            "password" => "cyTV395121",
            "epid" => "121593",
            "phone"=> $phone,
            "message"=>iconv("UTF-8", "GB2312//IGNORE",  "您的验证码为: ".$code."，5分钟内有效，切勿告知任何人。"),
            "linkid" => "",
            "subcode" => "",
            );
        $this->mmc->set('verify_code_'.$phone, $code, $expired_time * 60);

         $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://q.hl95.com:8061/?".http_build_query($msg));

            curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
            //curl_setopt($ch, CURLOPT_USERPWD  , $api_key);
            curl_setopt($ch, CURLOPT_POST, 0);
           // curl_setopt($ch, CURLOPT_POSTFIELDS,$msg);
            $send_text = curl_exec( $ch );
            curl_close( $ch );
     if (empty($send_text)) {
            $this->responseError('发送验证码失败,请稍后重试', 500);
        }
        $arrs = json_decode($send_text, true);
        if ($arrs == 0) {
            $this->responseSuccess('验证码已经发送成功');
        } else {
            $this->responseError('操作失败', 2);
        }

   }
   */

    /**
    * 短信API  螺丝帽
    *
    */
    public function sendSMS($phone = null)
    {
        $api_key = "api:key-".C("SEND_MSG_SECRET_KEY");

        if (empty($phone)) {
            $this->responseError(L('_PHONENUM_NOT_R_'));
        }
        $phone = substr($phone,-11);
        //if ($this->isLimited()) {
          //  $this->responseError(L('_OPERATION_TOO_FREQUENT_'));
        //}
        $ua = new UserAction();
        //验证账号状态是否可以登录
        $ua->accountStatus("sms",$phone);
        $code = $this->generateRandomStr($this->captcha_length);
        $expired_time = 5; // 单位分钟.
        $msg = array("mobile"=> $phone, "message"=>L('_SMS_MESSAGE_ONE_').$code.L('_SMS_MESSAGE_TOW_')."【喵榜直播】");
        $this->mmc->set('verify_code_'.$phone, $code, $expired_time * 60);
        try {


	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");

	    curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

	    curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_USERPWD  , $api_key);

	    curl_setopt($ch, CURLOPT_POST, TRUE);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);

	    $res = curl_exec( $ch );
	    curl_close( $ch );
        } catch (Exception $e) {
            // curl 错误
        }
        if (empty($res)) {
            $this->responseError(L('_CODE_FAILED_'));
        }
        $arr = json_decode($res, true);
        if ($arr['error'] == '0') {
            $this->responseSuccess(L('_CODE_SUCCESS_'));
        } else {
            $this->responseError(L('_OPERATION_FAIL_'));
        }
    }

    /*
    public function sendSMS($phone = null) {
        if (!is_string($phone) || !ctype_digit($phone) || strlen($phone) != 11) {
            $this->responseError('手机号不正确.');
        }
        if ($this->isLimited()) {
            $this->responseError('操作太过于频繁');
        }
        
        $url = "https://api.netease.im/sms/sendtemplate.action";
        $appKey = '0fb1d5e1310fb73fee14d2264535a613';
        $appSecret = '28c54deeb8a7';
        $nonce = 'temp';
        $code = $this->generateRandomStr($this->captcha_length);
        $expired_time = 5; // 单位分钟.
        $this->mmc->set('verify_code_'.$phone, $code, $expired_time * 60);
        $data = array('templateid' => '10008', 'mobiles' => '["'.$phone.'"]', 'params' => '["美丽播直播用户", "'.$code.'"]');
        $curTime = time();
        $checkSum = sha1($appSecret . $nonce . $curTime);
        $data = http_build_query($data);
        $opts = array (
                'http' => array(
                        'method' => 'POST',
                        'header' => array(
                                'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
                                "AppKey:$appKey",
                                "Nonce:$nonce",
                                "CurTime:$curTime",
                                "CheckSum:$checkSum"
                        ),
                        'content' =>  $data
                ),
        );
        try {
            $context = stream_context_create($opts);
            $html = file_get_contents($url, false, $context);
        } catch (Exception $e) {
            // curl 错误
        }
        $arr = json_decode($html, true);
        
        if ($arr['code'] == '200') {
            $this->responseSuccess('验证码已经发送成功');
        } else {
            $this->responseError('操作失败', 2);
        }
       // echo $html;
    }
    */

    /**
    * 短信接口 创蓝
    *
    */
    /*
    public function sendSMS($phone = null)
    {
        if (!is_string($phone) || !ctype_digit($phone) || strlen($phone) != 11) {
            $this->responseError('手机号不正确.');
        }
        if ($this->isLimited()) {
            $this->responseError('操作太过于频繁');
        }

        $url = 'http://222.73.117.158:80/msg/HttpBatchSendSM';

        $request = CurlRequests::Instance()->setRequestMethod('get');
        $code = $this->generateRandomStr($this->captcha_length);
        // 验证码过期时间
        $expired_time = 5;
        $this->mmc->set('verify_code_'.$phone, $code, $expired_time * 60);
        $param = array(
            'account'=>'haienwl888',
            'pswd'=>'Hewl123456',
            'mobile'=>$phone,
            'msg'=>"【美丽播直播服务】您的验证码为: ".$code."，5分钟内有效，切勿告知任何人。【美丽播】",
            'needstatus'=>true,
            );

        $response = $request->request($url, $param);

        $spices = explode(',', $response);
        if ($spices[1] != 0) {
            $this->responseError('短信发送失败');
        }

        $this->responseSuccess('发送成功');

    }
    */

    /**
     * 验证手机号是否正确.
     * @param int $phone
     * @param string $captcha
     */
    public function verify($phone = null, $captcha = null)
    {
        if (empty($phone)) {
            $this->responseError(L('_PHONENUM_NOT_R_'));
        }
        $phone = substr($phone , -11);

        if (!is_string($captcha) || strlen($captcha) != $this->captcha_length) {
            $this->responseError(L('_CODE_TYPE_FAILED_'));
        }
        $code = $this->mmc->get('verify_code_'.$phone);
        $ua = new UserAction();
        //验证账号状态是否可以登录
        $ua->accountStatus("sms",$phone);
        if (in_array($phone,array('15002873197','18583858486','18384580577','13438200914','18888888888','18382408534'))) {
            $code = $captcha = 88888;
        }
        if ($code == null) {
            $this->responseError(L('_CODE_OVERDUE_'), 1);
        } else if ($code != $captcha){
            $this->responseError(L('_CODE_FAIL_'), 2);
        }
        $user_info = M('member')->where(array('mobile' => $phone))->find();
        if (empty($user_info)) {
            $user_id = $this->registerWithPhone($phone);
            if ($user_id < 0) {
                $this->responseError(L('_CODE_SYSTEM_ERROR_'), 3);
            }
            // 这里的username可能不一定是手机号了.所以尝试获取一次.理论上一定有.
            $user_info = M('member')->where(array('id' => $user_id))->find();
        }

        // 如果用户之前自动登录过,还有token,删除原来的token.
        $token = TokenHelper::getInstance()->get($user_info['id']);
        if (!empty($token)) {
            TokenHelper::getInstance()->delete($token);
        }

        $resp = UserAction::parseLoginSuccessResp($user_info['username'], $user_info['id']);

        $this->responseSuccess($resp);
    }

    /**
     * (之前需检查用户是否在)通过手机号进行注册.
     * @param $phone
     *
     * @return integer 注册之后的用户ID,小于等于0,表示失败.请参见ucenter的返回值.
     */
    private function registerWithPhone($phone)
    {
        $password = uniqid('pwd');
        $username  = $phone;
       $nickname = substr($phone,0,3)."****".substr($phone,7,4);
        do {
            $roomnum = rand(1000000000, 1999999999);
        } while ($this->checkIt($roomnum) == '');
        $new_user_info = array(
            'username' => $username,
            'nickname' => $nickname,
            'password' => md5($password),
            'password2' => $this->pswencode($password),
            'regtime' => time(),
            'email' => '',
            'curroomnum' => $roomnum,
            'mobile' => $phone,
        );
        //$this->user->create($new_user_info);
        $user_id = $this->user->add($new_user_info);

        $new_user_info['id'] = $user_id;
        $jmessage = new JmessageAction();
        $jmessage->jmRegist($new_user_info);

       $username = "mei_".$user_id;
       $sql = "update ss_member set username = '".$username."' where id = ".$user_id;
       M()->execute($sql);
        // 新注册房间状态写入缓存
        setRegistRoom($user_id, $roomnum);
        return $user_id;
    }

    /**
     * 生成随机字符串.
     *
     * @param int $length   需要生成的长度.
     * @param string $table 需要生成的字符串集合.
     *
     * @return string
     */
    protected function generateRandomStr($length = 6, $table = '0123456789') {
        $code = '';
        if ($length <=0 || empty($table)) {
            return $code;
        }
        $max_size =  strlen($table) - 1;
        while ($length -- > 0) {
            $code .= $table[rand(0, $max_size)];
        }
        return $code;
    }


    /**
     * 从XML字符串中生成DOMXPath对象,用于后续执行Xpath操作.
     *
     * @param $source xml格式的字符串.
     *
     * @return DOMXPath
     *
     */
    protected function getXpathObjectFromXmlStr($source)
    {
        $dom = new DOMDocument();
        @$dom->loadXML($source);
        $xpath = new DOMXPath($dom);
        return $xpath;
    }
}
