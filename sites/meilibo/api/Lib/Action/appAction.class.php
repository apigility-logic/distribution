<?php
class appAction extends BaseAction
{
    /**
     * 仅APP可以查看.
     */
    protected static $configATMWeiXin = array();
    public function _initialize()
    { 

	if(isset($_GET['test'])) {
		echo "here";exit;
	}
		 
        // 过滤掉PC允许访问的页面 share,index
        $pc_page =  strcasecmp(ACTION_NAME, 'about_yinsi')==0 ? true :strcasecmp(ACTION_NAME, 'share')==0 ? true : (strcasecmp(ACTION_NAME, 'index') == 0 ? true : false);
        if (!ismobile() && !$pc_page) {
            header("HTTP/1.1 403 Forbidden");
            echo "<h1 style='text-align:center;'> 403 Forbidden </h1>";
            exit;
        }
        static::$configATMWeiXin = C('ATM_WEIXIN');

    }
    public function index()
    {
        $this->display();
    }
    public function about()
    {
        $this->display();
    }
    public function about_gongyue()
    {
        $this->display();
    }
    public function about_lianxi()
    {
        $this->display();
    }
    public function about_tiaokuan()
    {
        $this->display();
    }
    public function about_yinsi()
    {
        $this->display();
    }

    public function feedback()
    {
        if (!empty($_POST) && $_POST['content'] != '') {
            $data = array(
                'uid'=>$_POST['id'],
                'title'=>isset($_POST['title']) ? $_POST['title'] : '',
                'content'=>$_POST['content'],
                );

            $message = !M("Feedback")->add($data) ? '提交失败' : '提交成功';
            $this->assign('message', $message);
            $this->assign('title',$data['title']);
            $this->assign('content',$data['content']);
            $this->display();
        }

        $id = !isset($_GET['id']) ? '' : $_GET['id'];
        $this->assign("id", $id);
        $this->assign("message",'');
        $this->assign('title','');
        $this->assign('content','');
        $this->display();
    }
    /**
     * 等级页面.
     */
    public function lev()
    {
        $uid = !empty($_GET['uid']) ? $_GET['uid'] : '0'; //UID
        $data['u_id'] = $uid;
        $userInfo = M("member")->where(array('id' => $uid))->getField('id,spendcoin');
        // $userInfo = $result[0];
        // $level = getRichlevel($userInfo[$uid]['spendcoin']); //等级
        $richLevel = M("richlevel")->where("spendcoin_up >= ".$userInfo[$uid]." and spendcoin_low <=" . $userInfo[$uid])->find();
        $needExpire = $richLevel['spendcoin_up'] - $richLevel['spendcoin_low']; //级别之间的进阶经验值 例如1到2级之间 需要1000经验 则needCoin就是1000
        $haveExpire = $userInfo[$uid] - $richLevel['spendcoin_low']; //
        $level = $richLevel['levelid'];
        $ratio = sprintf($haveExpire / $needExpire) * 100;
        $this->assign('ratio',$ratio);
        $this->assign('need', $needExpire);
        $this->assign("have",$haveExpire);
        $this->assign('level', $level);
        $this->display();
    }
    public function lev_index()
    {
        $uid = !empty($_GET['uid']) ? $_GET['uid'] : '0'; //UID
        $data['u_id'] = $uid;
        $userInfo = M("member")->where(array('id' => $uid))->getField('id,spendcoin');
        // $userInfo = $result[0];
        // $level = getRichlevel($userInfo[$uid]['spendcoin']); //等级
        $richLevel = M("richlevel")->where("spendcoin_up >= ".$userInfo[$uid]." and spendcoin_low <=" . $userInfo[$uid])->find();
        $needExpire = $richLevel['spendcoin_up'] - $richLevel['spendcoin_low']; //级别之间的进阶经验值 例如1到2级之间 需要1000经验 则needCoin就是1000
        $haveExpire = $userInfo[$uid] - $richLevel['spendcoin_low']; //
        $level = $richLevel['levelid'];
        $ratio = sprintf($haveExpire / $needExpire) * 100;
        $this->assign('ratio',$ratio);
        $this->assign('need', $needExpire);
        $this->assign("have",$haveExpire);
        $this->assign('level', $level);
        $this->display();
    }
    public function lev_intro(){
        $this->display();
    }
    public function lev_detalis(){
        $this->display();
    }

    private function createRandomStr($length){
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';//62个字符
        $strlen = 62;
        while($length > $strlen){
            $str .= $str;
            $strlen += 62;
        }
        $str = str_shuffle($str);
        return substr($str,0,$length);
    }

    //微信jssdk   通过access_token返回jsapi_ticket
    public function getAccesstoken($url){

        $wxConfig = C("ATM_WEIXIN");


        //初始化获取assesstoken
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$wxConfig['APPID'].'&secret='.$wxConfig['APPCECRET']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);

        $access_token = json_decode($output,true);//获取到了access_token

        //初始化获取jsapi_ticket
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token['access_token'].'&type=jsapi');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        $output       = json_decode($output,true);

        $jsapi_ticket = $output['ticket'];
        //}else{
        //$jsapi_ticket =session('jsapi_ticket_'.$merchant->id);
        //}

        $randStr  = strtolower($this->createRandomStr(10));


        $data['appId'] = $wxConfig['APPID'];
        $data['nonceStr'] = $randStr;
        $data['timestamp'] = time();
        $data['debug'] = true;
        $str = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$randStr.'&timestamp='.$data['timestamp'].'&url='.$url;

        $data['signature'] = sha1($str);
        return $data;
    }

    public function wx_index(){
        // 用户登录后信息
        $userinfo = array();
        $uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
        if( $uid > 0 ){
            $userinfo = M('member')->where('id='.$uid)->field('id, curroomnum, nickname, username, coinbalance,earnbean, spendcoin,avatartime')->find();
            $level = D('emceelevel')->where('earnbean_up>='.$userinfo['spendcoin'].' and earnbean_low<='.$userinfo['spendcoin'])->field('levelid,levelname')->order('levelid asc')->select();
            $userinfo['level'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            $userinfo['token'] = isset($_GET['token']) ? $_GET['token'] : '';
            $userinfo['avatar'] = getAvatarApi($userinfo['avatartime'], $uid, 'middle');     
        }
        //用户是否登陆
        if( count($userinfo) ){
            $userinfo['islogin'] = "true";
        }else{
            $userinfo['islogin'] = "false";
        }
        $this->assign('userinfo', $userinfo);
        $this->display();
    }
    /**
     * 分享页面.
     *xiaowu 获取房间号 用户ID 头像
     */
    public function share2()
    {
        $room_num = isset($_GET['current_room']) ? $_GET['current_room'] : 'invalid room';
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
        if (!is_numeric($room_num)) {
            $this->_empty('房间号不正确.');
        }
        //主播信息
        $current_user  = M('Member')->where(array('curroomnum'=>$room_num))->field(" id, username, nickname,broadcasting, curroomnum, onlinenum, beanorignal,avatartime,orientation")->find();
        if (empty($current_user)) {
            $this->_empty('主播不存在.');
        }
        //PC端直接跳轉 不在繼續
        if (!ismobile()){
            $config = M('Siteconfig')->find();
            $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
            $url = $domain.'/'.$_GET['current_room'];
            echo "<script>window.location.href='".$url."'</script>";
        }
        $current_user['head_url'] = getAvatarApi($current_user['avatartime'],$current_user['id'],'big');
        $where = array(
            'ss_member.id' => $current_user['id'],
            'ss_coindetail.type' => 'expend'
            );
        $current_user['coin'] = M('member')->join("ss_coindetail on ss_coindetail.touid = ss_member.id")->where($where)->getField("sum(coin)");
       
        // 用户登录后信息
        $userinfo = array();
        if( $uid > 0 ){
            $userinfo = M('member')->where('id='.$uid)->field('id, curroomnum, nickname, username, coinbalance,earnbean, spendcoin')->find();
            $level = D('emceelevel')->where('earnbean_up>='.$userinfo['spendcoin'].' and earnbean_low<='.$userinfo['spendcoin'])->field('levelid,levelname')->order('levelid asc')->select();
            $userinfo['level'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            $userinfo['token'] = isset($_SESSION['token']) ? $_SESSION['token'] : '';
        }
        //用户是否登陆
        if( count($userinfo) ){
            $userinfo['islogin'] = "true";
        }else{
            $userinfo['islogin'] = "false";
        }

        $roomId = $room_num;
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
        $curl_url = $domain . '/OpenAPI/v1/Qiniu/getRtmpUrls';
        $app_url = $domain . '/OpenAPI/v1/Qiniu/getHlsUrls';
        $data = array('roomID'=>$roomId);
        $url = $this->curlRequest($curl_url, false, $data);
        $url = (Array)json_decode($url,true);

        $aurl = $this->curlRequest($app_url, false, $data);
        $aurl = (Array)json_decode($aurl,true);
        //主播列表
        $backstream = M('backstream')->field('uid, title')->select();
        $finalBS = array();
        foreach ($backstream as $key => $one_backstream) {

            $finalBS[$one_backstream['uid']] = $one_backstream['title'];
        }
        $finalArr = array();
        $members = M('member')->limit(6)->field('onlinenum, id, curroomnum, sid, avatartime')->order('broadcasting desc, onlinenum desc')->select();
        foreach ($members as $member) {
            $member['avatar'] = $member['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatarApi($member['avatartime'], $member['id'], 'middle');
            $member['snap'] = $member['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatarApi($member['avatartime'], $member['id'], 'yuan');
            if(array_key_exists($member['id'], $finalBS)){
                $member['roomTitie'] = $finalBS[$member['id']];
            }else{
                $member['roomTitie'] = "";
            }
            unset($member['avatartime']);
            array_push($finalArr,$member);
        }
        $member_all = array();
         //获取用户ID,房间号
        $member_all = M('Member')->field('id,curroomnum')->limit(6)->select();
        //获取礼物列表
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $game_mem = new \Memcached();
        $game_mem->addServer($ip, $port);
        $game_type = $game_mem->get($current_user['curroomnum'].'gameType');
        $this->assign('gameType', $game_type);

        $this->assign('member_all', $member_all);
        $this->assign('url',@$url['data']['ORIGIN']);
        $this->assign('app_url',@$aurl['data']['ORIGIN']);
        $this->assign('domain',$domain);
        $this->assign('finalArr',$finalArr);
        $this->assign('user', $current_user);
        $this->assign('userinfo', $userinfo);

        //$this->assign('wxConfig',json_encode($this->getAccesstoken("{$domain}/app/share?current_room={$domain}")));

        
        if($current_user['orientation'] == 'v') {
            $this->display();
        } else {
            $this->display("app:share_horizontal");
        }
    }
    
    public function share()
    {
        $room_num = isset($_GET['current_room']) ? $_GET['current_room'] : 'invalid room';
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
        if (!is_numeric($room_num)) {
            $this->_empty('房间号不正确.');
        }
        //主播信息
        $current_user  = M('Member')->where(array('curroomnum'=>$room_num))->field(" id, username, nickname,broadcasting, curroomnum, onlinenum, beanorignal,avatartime,orientation")->find();
        if (empty($current_user)) {
            $this->_empty('主播不存在.');
        }
        //PC端直接跳轉 不在繼續
        if (!ismobile()){
            $config = M('Siteconfig')->find();
            $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
            $url = $domain.'/'.$_GET['current_room'];
            echo "<script>window.location.href='".$url."'</script>";
        }
        $current_user['head_url'] = getAvatarApi($current_user['avatartime'],$current_user['id'],'big');
        $where = array(
            'ss_member.id' => $current_user['id'],
            'ss_coindetail.type' => 'expend'
            );
        $current_user['coin'] = M('member')->join("ss_coindetail on ss_coindetail.touid = ss_member.id")->where($where)->getField("sum(coin)");
       
        // 用户登录后信息
        $userinfo = array();
        if( $uid > 0 ){
            $userinfo = M('member')->where('id='.$uid)->field('id, curroomnum, nickname, username, coinbalance,earnbean, spendcoin')->find();
            $level = D('emceelevel')->where('earnbean_up>='.$userinfo['spendcoin'].' and earnbean_low<='.$userinfo['spendcoin'])->field('levelid,levelname')->order('levelid asc')->select();
            $userinfo['level'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            $userinfo['token'] = isset($_SESSION['token']) ? $_SESSION['token'] : '';
        }
        //用户是否登陆
        if( count($userinfo) ){
            $userinfo['islogin'] = "true";
        }else{
            $userinfo['islogin'] = "false";
        }

        $roomId = $room_num;
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
        $curl_url = $domain . '/OpenAPI/v1/Qiniu/getRtmpUrls';
        $app_url = $domain . '/OpenAPI/v1/Qiniu/getHlsUrls';
        $data = array('roomID'=>$roomId);
        $url = $this->curlRequest($curl_url, false, $data);
        $url = (Array)json_decode($url,true);

        $aurl = $this->curlRequest($app_url, false, $data);
        $aurl = (Array)json_decode($aurl,true);
        //主播列表
        $backstream = M('backstream')->field('uid, title')->select();
        $finalBS = array();
        foreach ($backstream as $key => $one_backstream) {

            $finalBS[$one_backstream['uid']] = $one_backstream['title'];
        }
        $finalArr = array();
        $members = M('member')->limit(6)->field('onlinenum, id, curroomnum, sid, avatartime')->order('broadcasting desc, onlinenum desc')->select();
        foreach ($members as $member) {
            $member['avatar'] = $member['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatarApi($member['avatartime'], $member['id'], 'middle');
            $member['snap'] = $member['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatarApi($member['avatartime'], $member['id'], 'yuan');
            if(array_key_exists($member['id'], $finalBS)){
                $member['roomTitie'] = $finalBS[$member['id']];
            }else{
                $member['roomTitie'] = "";
            }
            unset($member['avatartime']);
            array_push($finalArr,$member);
        }
        $member_all = array();
        //获取用户ID,房间号
        $member_all = M('Member')->field('id,curroomnum')->limit(6)->select();
        //获取礼物列表
        $this->assign('member_all', $member_all);
        $this->assign('url',@$url['data']['ORIGIN']);
        $this->assign('app_url',@$aurl['data']['ORIGIN']);
        $this->assign('domain',$domain);
        $this->assign('finalArr',$finalArr);
        $this->assign('user', $current_user);
        $this->assign('userinfo', $userinfo);

        //$this->assign('wxConfig',json_encode($this->getAccesstoken("{$domain}/app/share?current_room={$domain}")));

        if($current_user['orientation'] == 'v') {
            $this->display();
        } else {
            $this->display("app:share_horizontal");
        }
    }


    public function share_profile(){
        $uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
        if($uid == 0 || empty($uid) ){
            echo "参数错误";
            return ;
        }
        $userinfo = M('member')->where('id='.$uid)->field('id, intro, curroomnum, avatartime, nickname, username, coinbalance, spendcoin')->find();
        $level = D('emceelevel')->where('earnbean_up>='.$userinfo['spendcoin'].' and earnbean_low<='.$userinfo['spendcoin'])->field('levelid,levelname')->order('levelid asc')->select();
        // dump($level);
        // echo $userinfo['intro']."|".strlen($userinfo['intro'])."|".$userinfo['spendcoin'];
        // $userinfo['intro'] = empty($userinfo['intro']) ? "" : $userinfo['intro'];
        $userinfo['level'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
        $userinfo['avatar'] = $userinfo['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatarApi($userinfo['avatartime'], $userinfo['id'], 'middle');
        $userinfo['attention'] = M('attention')->where('uid='.$userinfo['id'])->count();
        $userinfo['token'] = isset($_GET['token']) ? $_GET['token'] : '';
        $userinfo['current_room'] = isset($_GET['current_room']) ? $_GET['current_room'] : '';
        $this->assign('userinfo', $userinfo);
        $this->display();
    }
    public function share_anchor(){
        $uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
        if($uid == 0 || empty($uid) ){
            echo "参数错误";
            return ;
        }
        $user['id'] = $uid;
        $user['current_room'] = isset($_GET['current_room']) ? $_GET['current_room'] : '';
        $user['token'] = isset($_GET['token']) ? $_GET['token'] : '';
        $attentions = M('attention')->where('uid='.$uid)->select();
        $result = array();
        foreach ($attentions as $attention) {
            $userinfo = array();
            $userinfo = M('member')->where('id='.$attention['attuid'])->field('id, intro, curroomnum, sex, avatartime, nickname, username, coinbalance, spendcoin')->find();
            $level = D('emceelevel')->where('earnbean_up>='.$userinfo['spendcoin'].' and earnbean_low<='.$userinfo['spendcoin'])->field('levelid,levelname')->order('levelid asc')->select();
            // dump($level);
            // echo $userinfo['intro']."|".strlen($userinfo['intro'])."|".$userinfo['spendcoin'];
            // $userinfo['intro'] = empty($userinfo['intro']) ? "" : $userinfo['intro'];
            $userinfo['level'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '1';
            $userinfo['avatar'] = $userinfo['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatarApi($userinfo['avatartime'], $userinfo['id'], 'middle');
            array_push($result, $userinfo) ;
        }
        $this->assign('userinfos', $result);
        $this->assign('user', $user);
        $this->assign('token', isset($_GET['token']) ? $_GET['token'] : '');
        $this->display();
    }
    public function share_profileUpdate(){
        $uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
        if($uid == 0 || empty($uid) ){
            echo "参数错误";
            return ;
        }        
        $userinfo = M('member')->where('id='.$uid)->field('id, nickname, intro, avatartime')->find();
        $userinfo['avatar'] = $userinfo['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatarApi($userinfo['avatartime'], $userinfo['id'], 'middle');
        $userinfo['current_room'] = isset($_GET['current_room']) ? $_GET['current_room'] : '';
        $this->assign('userinfo', $userinfo);
        $this->assign('token', isset($_GET['token']) ? $_GET['token'] : '');
        $this->display();
    }

    public function editUserinfo()
    {
        $userid = $_POST['uid'];
        $nickname = $_POST['nickname'];
        $intro = $_POST['intro'];
        if( empty($userid) || empty($nickname) || empty($intro)){
            $this->assign("Message","参数错误");
            $this->display("_error");
            exit;
        }
        //导入上传类
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        //设置上传文件大小
        $upload->maxSize = 3145728;
        //设置上传文件类型
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        //设置附件上传目录
        $upload->saveRule = $userid;  // 文件名称
        $upload->savePath  = realpath(APP_PATH.'/').getUploadPath($userid); // 设置附件上传（子）目录
        $upload->autoSub = false; //是否生成日期文件夹

        $upload->thumbRemoveOrigin = false; //设置生成缩略图后移除原图
        $upload->uploadReplace =  true;  // 覆盖同名
        $upload->thumbPrefix = '';  // 前缀
        $upload->thumbSuffix = '_big,_middle,_small'; // 后缀
        $upload->thumbExt = 'jpg';

        if (!is_dir($upload->savePath)) {
            mkdir($upload->savePath, 0777, true);
        }
        //设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = true;
         // 设置引用图片类库包路径
        $upload->imageClassPath = 'ORG.Util.Image';
        //设置缩略图最大宽度
        $upload->thumbMaxWidth = '200,120,48';
        //设置缩略图最大高度
        $upload->thumbMaxHeight = '200,120,48';
        // 上传文件
        @$info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            if($upload->getErrorMsg() != '没有选择上传文件'){
                $this->assign("Message",$upload->getErrorMsg());
                $this->display("_error");
                exit;
            }
        }
        $member = M('Member');
        $avatartime = time();
        $data['avatartime'] = $avatartime;
        $data['nickname'] = $nickname;
        $data['intro'] = $intro;
        $data['id'] = $userid;
        
        $member->save($data);
        $this->assign("Message","修改成功");
        $this->display("_success");
        exit;
    }
    //分享页登陆
    public function share_login(){
        //是微信浏览器打开
        $room_num = isset($_GET['current_room']) ? $_GET['current_room'] : 'invalid room';
        if (!(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false)){
            $config = M('Siteconfig')->find();
            $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';
            $appid = static::$configATMWeiXin['APPID'];
            $AppSecret = static::$configATMWeiXin['APPSECRET'];
            $redirect_uri = $domain."/OpenAPI/V1/Payment/weixinCallback";
            $response_type = "code";
            $scope = "snsapi_userinfo";
            $state = 'ShareLogin|'.$room_num;
            $grant_type = "authorization_code";
            $data = array(
                "appid" =>  $appid,
                "redirect_uri" =>  $redirect_uri,
                "response_type" =>  $response_type,
                "scope" =>  $scope,
                "state" =>  $state,
                );
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?".http_build_query($data)."#wechat_redirect";
            echo "<script>window.location.href='".$url."'</script>";
        }else{
            echo "<h2>先去微信访问页面登陆吧！<h2><br/><h4>别催啦，<h2>程序猿<h2>正在辛苦建设该功能中...<h4>";
        }
    }

    //审核页面
    public function approve(){
        if(!isset($_GET['uid']) || empty($_GET['uid'])){
            $this->assign("Message","请通过APP中进入该页");
            $this->display("_error");
            exit;
        }
        $uid = $_GET['uid'];
        $current_user  = M('Member')->where(array('id'=>$uid))->find();
        if($current_user == null){
            $this->assign("Message","请通过APP中进入该页");
            $this->display("_error");
            exit;
        }
        $isApprove = M("approve")->where(array('uid'=>$uid))->select();
        if($isApprove != NULL){
            foreach ($isApprove as $value) {
                if($value['status'] == '0'){
                    $this->assign("Message","正在审核中，请耐心等待");
                    $this->display("_success");
                    exit;
                }else if($value['status'] == "1"){
                    $this->assign("Message","您已审核通过，无须再次提交");
                    $this->display("_success");
                    exit;
                }
            }
        }
        //返回认证类型列表
        $sql = "select * from ss_usersort where parentid <> 0 and isapprove = '1'";
        $approveList = M("Usersort")->where("parentid <> 0 and isapprove = '1'")->select();
        $this->assign("approveList",$approveList);
        $this->assign("uid",$uid);
        $this->display();
    }
    public function approveCheck(){
        $uid = $_POST['uid'];
        //先判断验证码是否正确
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $sid = $_POST['sid'];
        $IDCard = $_POST['idcard'];
        if(empty($uid) || empty($name) || empty($phone) || empty($sid) || empty($IDCard)){
            $this->assign("Message","请填写完整您的信息哟~~");
            $this->display("_error");
            exit;
        }
        if(empty($_FILES['authorpic']['name'])){
            $this->assign("Message","请上传手持身份证照片");
            $this->display("_error");
            exit;
        }
        if(empty($_FILES['beforepic']['name'])){
            $this->assign("Message","请上传身份证正面照片");
            $this->display("_error");
            exit;
        }
        $uploadData = $this->_upload();
        if($uploadData["code"] == "1"){
            $authorpic = $uploadData['data'][0]["savename"];
            $beforepic = $uploadData['data'][1]["savename"];
        }else{
            $this->_empty($uploadData['msg']);
        }
        $uptime = time();
        $sql = "insert into ss_approve( uid, name, card, mobile, card_beforepic, card_authorpic, sid, uptime) values ( %s,'%s','%s','%s','%s','%s','%s',%s)";
        $sql = sprintf($sql,$uid,$name,$IDCard,$phone,$beforepic,$authorpic,$sid,$uptime);
        $result = M("")->execute($sql);
        if($result > 0){
            $this->assign("Message","提交成功，请等待工作人员审核");
            $this->display("_success");
            exit;
        }else{
            $this->assign("Message","提交失败~~");
            $this->display("_error");
            exit;
        }
    }
    public function approve_index(){
        if(!isset($_GET['uid']) || empty($_GET['uid'])){
            $this->assign("Message","请通过APP中进入该页");
            $this->display("_error");
            exit;
        }
        $uid = $_GET['uid'];
        $this->assign("uid",$uid);
        $this->display();
    }
    public function _empty($message = null){
        header("HTTP/1.1 404 Not Found");
        $message = is_null($message) ? "Oh~~~页面不存在哦~~" : $message;
        echo "<h1 style='text-align:center;'> {$message} </h1>";
        exit;
    }
    public function _success($message = "提交成功 ！"){
        $this->assign("Message",$message);
        $this->display();
    }
    public function _error($message = "提交失败了.."){
        $this->assign("Message",$message);
        $this->display();
    }

    public function help_and_feedback(){
        $this->display();
    }
    public function block(){
        $this->display();
    }
    public function recharge_problem(){
        $this->display();
    }
    public function see(){
        $this->display();
    }
    public function hot(){
        $this->display();
    }
    public function number(){
        $this->display();
    }
    public function opinion(){
        $this->display();
    }
    public function recharge(){
        $this->display();
    }
    public function appeal(){
        $this->display();
    }
    public function appeal_problem(){
        $this->display();
    }
    public function broadcast_problem(){
        $this->display();
    }
    public function title(){
        $this->display();
    }
    public function account_problem(){
        $this->display();
    }
    public function blockSearch(){
        if(empty($_POST['uid'])){
            $data['status'] = 0;
            $data['errormsg'] = "请求错误";
            echo json_encode($data);
            exit;
        }
        $uid = $_POST['uid'];
        $banlists = M('banlist')->where('uid = '.$uid)->order('id desc')->select();
        $userInfo = M("member")->where(array('id'=>$uid))->field('nickname,isaudit')->find();
        $banCount = count($banlists);
        if($banCount > 0 && $userInfo['isaudit'] == 'n'){
            $data['status'] = 1;
            $data['uid'] = $uid;
            $data['nickname'] = $userInfo['nickname'];
            $data['data'] = array();
            $tempData['bantime'] = date("Y-m-d",$banlists[$banCount-1]['bantime']);
            $tempData['time'] = floor(($banlists[$banCount-1]['banduration'] - $banlists[$banCount-1]['bantime']) / (24 * 60 * 60));
            $tempData['banduration'] = date("Y-m-d",$banlists[$banCount-1]['banduration']);
            array_push($data['data'],$tempData);
            echo json_encode($data);
            exit;
        }else{
            $data['status'] = 0;
            $data['errormsg'] = "暂无封号记录";
            echo json_encode($data);
            exit;
        }
    }

     public function _upload()
     {
        //导入上传类
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        //设置上传文件大小
        $upload->maxSize            = 3145728;
        //设置上传文件类型
        $upload->exts          = array('jpg', 'gif', 'png', 'jpeg');
        //设置附件上传目录
        $upload->saveRule =    'uniqid';  // 文件名称
        $upload->savePath  =      './style/app/images/idcard/'; // 设置附件上传（子）目录
        $upload->autoSub = false; //是否生成日期文件夹

        if (!is_dir($upload->savePath)) {
            mkdir($upload->savePath, 0777, true);
        }
        @$info   =   $upload->upload();
        $returnData = array();
        if(!$info) {// 上传错误提示错误信息
            $returnData["code"] = "0";
            $returnData["msg"] = $upload->getErrorMsg();
        }else{// 上传成功 获取上传文件信息
            $returnData["code"] = "1";
            $data = array();
            $info = $upload -> getUploadFileInfo();
            $returnData['data'] = $info;
        }
        return $returnData;
    }

    /**
    * 发起CURL请求
    *
    * @param string url
    * @param bool isPost
    * @param array $data
    */
    protected function curlRequest($url, $isPost, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);        
        } else {
            $url =  $url . '?' . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    /**
    * 微信授权
    *
    */
    public function weixinAuth() {
        $appid = static::$configATMWeiXin['APPID'];
        $AppSecret = static::$configATMWeiXin['APPSECRET'];
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';
        $redirect_uri = $domain."/OpenAPI/V1/Payment/weixinCallback";
        $response_type = "code";
        $scope = "snsapi_userinfo";
        $state = 'weixinCashCallback';
        $grant_type = "authorization_code";
        $data = array(
            "appid" =>  $appid,
            "redirect_uri" =>  $redirect_uri,
            "response_type" =>  $response_type,
            "scope" =>  $scope,
            "state" =>  $state,
            );
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?".http_build_query($data)."#wechat_redirect";
        echo "<script>window.location.href='".$url."'</script>";
    }
    /**
    * 微信提现
    *
    */
    // public function weixinCash($id = 0, $nickname = null, $wxopenid = null, $beanbalance = 0, $avatartime = 0) {
    //         $head_url = getAvatarApi( $avatartime, $id);
    //         $data = array(
    //             'id' => $id,
    //             'nickname' => $nickname,
    //             'wxopenid' => $wxopenid,
    //             'beanbalance' => $beanbalance / 100,
    //             'head_url' => $head_url,
    //             'wxid' => $wxopenid,
    //             'wxcash_url' => $wxcash_url,
    //             );
    //         $this->assign('userinfo', $data);
    //         $this->display();        
    //}
    public function weixinCash($id = 0, $nickname = null, $wxopenid = null, $beanbalance = 0, $avatartime = 0) {
            $head_url = getAvatarApi( $avatartime, $id);
            $probability = M('siteconfig')->where('id=1')->getField('cash_proportion');
            $earnRMB = sprintf('%.2f', $beanbalance*($probability/100) );
            $data = array(
                'id' => $id,
                'nickname' => $nickname,
                'wxopenid' => $wxopenid,
                'beanbalance' => $earnRMB,
                'head_url' => $head_url,
                'wxid' => $wxopenid,
                'wxcash_url' => $wxcash_url,
                );
            $this->assign('userinfo', $data);
            $this->display();        
    }
    /*
            请求提现接口
    */
    public function requestAPI(){
        if ( $_POST['openid'] && $_POST['cash']){
            // $wxcash_url = C('WXCASH_URL');
            $config = M('Siteconfig')->find();
            $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';
            $wxcash_url = $domain."/OpenAPI/V1/Payment/appWeixinCash";
            $cash = $_POST['cash']*100;
            $data = array(
                'openid' => $_POST['openid'],
                'cash' => $_POST['cash'],
                );
            $json = $this->curlRequest( $wxcash_url, true, $data);
            $json = json_decode($json);
            if( $json->code == "0" ){
                    $this->assign("Message", $json->data);
                    $this->display("_success");
                    exit;
            }else{
                    $this->assign("Message", $json->msg);
                    $this->display("_error");
                    exit;
            }
        }else{
            $this->assign("Message","非法请求");
            $this->display("_error");
            exit;
        }
    }


    /**
    * 微信支付相关配置
    * @var array
    */
    protected static $configWeiXin = array();

    public function __construct()
    {
        parent::__construct();
        static::$configWeiXin = C('ATM_WEIXIN');
    }

    /**
    * 微信APP支付接口
    *
    * @param string $token
    * @param int    $num  金额，RMB
    */
    public function weixinpay($uid, $num)
    {
        $wxopenid = M('member')->where('id = '.$uid)->getField('wxopenid');
        $result = $this->createWixinOrder($orderno = $this->createOrder($uid, $num), 1, $wxopenid);
        $arr = array();
        $arr['msg'] = $this->initPrepayData($result);
        if (strcmp($result['RETURN_CODE'], 'FAIL') === 0) {
            echo ($result['RETURN_MSG']);
            $arr['error_code'] = 1;
        }
        $arr['error_code'] = 0;
        echo json_encode($arr);
    }

    /**
    *   更新余额
    */
    public function getCoinBalance( $uid = null ){
        if(isset($uid) && empty($uid) && $uid == null ){
            echo "参数错误";
            exit;
        }
        echo M('member')->where("id = ".$uid )->getField("coinbalance");
    }

    /**
    * 向微信请求订单生成API
    *
    * @param int $orderNo
    * @param int $num
    */
    public function createWixinOrder($orderNo, $num, $wxopenid)
    {
        $xml = $this->initOrderData($orderNo ,$num, $wxopenid);
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
           "appId"=>  $prepayData['APPID'],     //公众号名称，由商户传入     
           "nonceStr"=>  $this->getRandomStr(), //随机串     
           "package"=>  'prepay_id='.$prepayData['PREPAY_ID'],     
           "signType"=>  "MD5",         //微信签名方式：     
           "timeStamp"=> time()."",         //时间戳，自1970年以来的秒数     
        );
        // ksort($appData);
        $str = $this->arrayToKeyValueString($appData);
        $appData['paySign'] = $this->getSign($str);
        return $appData;
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
        if (!( $id =$this->createConsumerOrder($uid, $orderNo, $orderTime,$num, $type))) {
            $this->responseError('创建订单失败');
        }

        return $id . "_" . $orderNo;
    }
    /**
    * 充值比例
    */
    protected static $ratio = 10;
    /**
    * 生成订单
    * @param int $uid
    * @param int $time
    * @param mixed $num  RMB
    * @param int $type 支付平台 0:支付宝 1：微信
    */
    protected static function createConsumerOrder($uid, $orderNo, $time, $num, $type, $content = '', $dealid = 0)
    {
        $charge = D('Chargedetail');
        $charge->create();
        $charge->uid = $uid;
        $charge->touid = $uid;
        $charge->rmb = $num;
        $charge->coin = $num * static::$ratio;
        $charge->status = '0';
        $charge->addtime = $time;
        $charge->orderno = $orderNo;
        $charge->proxyuid = 0;
        $charge->content = $content;
        $charge->dealid = $dealid;
        $charge->platform = $type;

        return $charge->add();
    }

    protected function initOrderData($out_trade_no, $total_free, $wxopenid)
    {
        $nonce_str = $this->getRandomStr();
        $param = array(
            'appid'=> static::$configWeiXin['APPID'],
            'body'=>'喵榜直播',
            'detail'=>'喵榜直播秀币购买',
            'fee_type'=> 'CNY',
            'mch_id'=> static::$configWeiXin['MCHID'],
            'nonce_str'=>$this->getRandomStr(),
            'notify_url'=>static::$configWeiXin['NOTIFY_URL'],
            'openid'=>$wxopenid,
            'out_trade_no'=> $out_trade_no,
            'spbill_create_ip'=>$_SERVER["REMOTE_ADDR"],
            'time_expire'=>date("YmdHms",strtotime("+2 hours")),
            'time_start'=>date("YmdHms"),
            'total_fee'=> $total_free,
            'trade_type'=>'JSAPI',
            );
        $str = $this->arrayToKeyValueString($param);
        $param['sign'] = $this->getSign($str);
        return $this->arrToXML($param);
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
    * 数组格式 array('大写xml的tag'    =>  'xml的value');
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
    *                               ----------------------------
    */
    protected function joinApiKey($str)
    {
        return $str . "key=".static::$configWeiXin['APIKEY'];
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
     *                              ----------------------------
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
        // if($useCert == true){
        //     curl_setopt($ch,CURLOPT_URL,  static::$configATMWeiXin['CASH_HTTPS']);
        //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
        //     curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        //     curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        //     //设置证书
        //     //使用证书：cert 与 key 分别属于两个.pem文件
        //     curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        //     curl_setopt($ch,CURLOPT_SSLCERT, static::$configATMWeiXin['SSLCERT_PATH']);
        //     curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        //     curl_setopt($ch,CURLOPT_SSLKEY, static::$configATMWeiXin['SSLKEY_PATH']);
        // }else{
            curl_setopt($ch,CURLOPT_URL, static::$configATMWeiXin['PLACE_ORDER']);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
            //post提交方式
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //设置header
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // }
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
            return "<xml><return_code>FAIL</return_code><return_msg>系统错误，请稍后重试</return_msg></xml>";
        }
    }

    public function search(){
        $girlList = M('member')->where("broadcasting = 'y'  and sex =1 ")->field('id,onlinenum')->order("onlinenum desc")->limit(3)->select();
        foreach($girlList as &$item) {
            $item['photo'] = getAvatar("123123",$item['id']);
        }


        $boyList = M('member')->where("broadcasting = 'y'  and sex =0 ")->field('id,onlinenum')->order("onlinenum desc")->limit(3)->select();
        foreach($boyList as &$item) {
            $item['photo'] = getAvatar("123123",$item['id']);
        }

        $todayList =  M('member')->where("broadcasting = 'y'")->field('id,onlinenum,nickname')->order("onlinenum desc")->limit(10)->select();
        foreach($todayList as &$item) {
            $item['photo'] = getAvatar("123123",$item['id']);
        }

        $this->assign("todayList",$todayList);
        $this->assign("girlList",$girlList);
        $this->assign("boyList",$boyList);
        $this->display();
    }

    public function charm_rank($id = 4289){
        $info = M('member')->where('id = '.$id)->find();
        $this->assign('userinfo',$info);
        $this->display();
    }

    public function play_rank(){
        $this->display();
    }
}
