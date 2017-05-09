<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/19
 * Time: 下午6:34.
 */
class RoomAction extends  BaseAction
{
    protected $default_msg = array(
        'category' => 'room_api',
        'ref' => '直播房间相关API',
        'links' => array(
            'entry_offline_room_url' => array(
                'href' => 'v1/room/entryOfflineRoom',
                'ref' => '直播已关闭状态信息',
                'method' => 'GET',
                'parameters' => array('roomnum' => 'string, required'),
            	),
            ),
        );

    public function __construct()
    {
        parent::__construct();
        //require_once APP_PATH.'../config.inc.php';
        // require_once APP_PATH.'../uc_client/client.php';
    }
    /**
    * 返回已关闭房间状��  
    * PC端访问
    */
    public function entryOfflineRoomForPc(){
        $this->entryOfflineRoom($_POST['roomid']);
    }
    /**
    * 返回已关闭房间状��    */
    //time 
    public function entryOfflineRoom($roomnum)
    {
    	if (!$roomnum) {
    		$this->responseError(L('_PARAM_ERROR_'));
    	}

    	$mem_obj = TokenHelper::getInstance();
        $room_cache = $mem_obj->get(C('ROOM_ONLINE_NUM_PREFIX').$roomnum);
        $room_cache = json_decode($room_cache,true);

        if (!$room_cache) {
            $room_cache = array('all_num'=>0,'viewer_num'=>0);
        }
        $info = M('Member')->where(array('curroomnum' => $roomnum))->getField('id,starttime,agentuid,nickname,avatartime');
        $id = key($info);
        $info = $info[$id];
        $starttime = $info['starttime'];
        $nickname = $info['nickname'];
        $avatar = getAvatar($info['avatartime'],$info['id'], 'middle');
        $redgift = M("gift")->where(array('isred'=>"1"))->select();
        $sql = "select sum(coin) as cn from ss_coindetail where type='expend' and touid={$id} and barrage != 'barrage' and addtime>={$starttime}";
        if($redgift != null){
            $redIds = "";
            foreach($redgift as $red){
                $redIds .= $red['id'] . ",";
            }
            $condition = " and giftid not in(" . substr($redIds,0,-1). ")";
            $sql .= $condition;
        }
        $earn = M('Coindetail')->query($sql);
        $earn = !$earn[0]['cn'] ? 0 : $earn[0]['cn'];

        /*
        if ($info['agentuid'] != 0) {
            $ratio = D('Agentfamily')->where('uid='.$info['agentuid'])->getField('uid,familyratio,anchorratio');
            $ratio = $ratio[$info['agentuid']];
            $earn = $earn * ($ratio['anchorratio'] / 100);
        } else {
            //默认的比��            $site = D('Siteconfig')->find();
            $earn = $earn * ($site['emceededuct'] / 100);
        }
        */
        $key = C('LIVE_STATUS_PREFIX').$roomnum;
        TokenHelper::getInstance()->delete($key);
        
        $this->responseSuccess(array('coin'=>$earn, 'client'=>$room_cache['all_num'], 'avatar'=>$avatar, 'nickname'=>$nickname ));
    }

    /**
    *
    * 0 : 可以直播
    * 1 : 没有签约
    * 2 : 时间段不允许直播
    * 3 : 其他错误
    */
	public function canLive($uid = null,$token = null){
        $this->responseSuccess('0');
        return ;
		$nowHour = date("H",time());
		if($nowHour >= 2 && $nowHour < 8){
    		$this->responseSuccess('2');
			exit;
		}else{
			if ($uid != null && !is_numeric($uid) && !empty($uid)) {
				$this->responseSuccess('3');
			}
			$info = TokenHelper::getInstance()->get($token);
			if($info != null){
				$followee_uid = isset($info['uid']) ? $info['uid'] : -1;
				$caller_uid = $followee_uid;
			}else{
				$this->responseSuccess('3');
			}
			$result = M('Member')->where(array('id' => $caller_uid))->getField('sign');
			if($result != "n"){
				$this->responseSuccess('0');
			}else{
				$this->responseError('1');
				exit;
			}
		}
	}
    //根据房间id获取是否在直播
     /**
     *
     * @param  [int] $uid [主播id]
     *
     */
    public function getBroadcasting($uid = null){
        if(empty($uid)){
            $this->responseError(L('_PARAM_ERROR_'));
        }else{
            if(M('member')->where("id = ".$uid)->getField('broadcasting')=='y'){
                $this->responseSuccess(true);
            }else{
                $this->responseSuccess(false);
            }
        }
    }
    /**
     * 返回主播房间下所有管理员信息
     * @param  [type] $uid [主播ID]
     *
     */
    public function getAdmin($uid = null){
        if(!is_numeric($uid)){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        //获取房间所有管理员UID
        $sql = "select adminuid,addtime from ss_roomadmin where uid = {$uid}";
        $AdminEntity = M() -> query($sql);
        if($AdminEntity != null){
            //拼装UID，进行查询管理员用户信息
            $userSQL = "select id, sex, intro, nickname, city, snap, curroomnum, vip, beanorignal,spendcoin,avatartime from ss_member where id in (%s);";
            $Ids;
            foreach($AdminEntity as $Admin){
                $Ids .= $Admin['adminuid'] . ",";
            }
            $Ids = substr($Ids,0,-1);
            $userSQL = sprintf($userSQL,$Ids);
            $result = M()->query($userSQL);
            $adminList = array();
            //遍历重置用户信息
            foreach($result as $user_info){
                if($user_info != null){
                    $user_info['avatar'] = getAvatar($user_info['avatartime'],$user_info['id'], 'middle');
                    $user_info['snap'] =  $user_info['avatartime'] <= 0 ? '/style/images/default.gif' : getAvatar($user_info['avatartime'],$user_info['id'], 'yuan');
                    $user_info['sex'] = empty($user_info['sex']) ? 0 : 1;
                    $user_info['total_contribution'] = $user_info['spendcoin'];
                    $user_info['anchorBalance'] = !$user_info['beanorignal'] ? 0 : $user_info['beanorignal'];
                    //TODO:方法getEmceelevel需要重�� total_contribution需要确��                    $level = getRichlevel($user_info['spendcoin']);
                    $user_info['emceelevel'] = isset($level[0]['levelid']) && $level[0]['levelid'] ? $level[0]['levelid'] : '0';
                    if ($user_info['city'] == L('_PLEASE_SELECT_')) {
                        $user_info['city'] = L('Mars');
                    }
                    array_push($adminList, $user_info);
                }
            }
            $this->responseSuccess($adminList);
        }
        $this->responseError("没有管理员");
    }
    /*PC创建直播间
    */
    public function createRoomForWeb(){
        
        $token = $_POST['token'];
        $roomid = $_POST['roomid'];
        $title =  $_POST['title'];
        $type =  $_POST['type'];
        $topic =  $_POST['topic'];
        $password =  $_POST['password'];
        $province = '';
        $address = '';

        switch ($_POST['type']) {
            case '密码进房':
                $ptid = 1;
                break;
            case '收取门票':
                $ptid = 2;
                break;
            case '限制等级':
                $ptid = 3;
                break;
            default:
                $ptid = 0;
                $prerequisite = '';
                break;
        }
        //$ptid = 0;  私密类型 1密码进房 2收取门票 3限制等级 0无限制
        $prerequisite = $password;//私密信息

        // $data = strtolower($status) == 'on' ? array('broadcasting'=>'y','starttime'=>time()) : array('broadcasting'=>'n');
        // $cond = array('id' => $this->current_uid);
        // //$result = $this->user->where($cond)->save($data);
        // $room = $this->user->where($cond)->getField('curroomnum');

        // $key = C('LIVE_STATUS_PREFIX').$roomid;
        // // 写入memcache标识该APP正在直播.
        // TokenHelper::getInstance()->set($key, 'y',  0);

        $this->createRoom($token,$roomid,$title,$province,$address,$ptid,$prerequisite,0,0,'','0',0,'h');
    }
    /**
     * 删除管理��     * @param  [int] $uid      [主播ID]
     * @param  [int] $adminuid [管理员ID]
     */
    public function delAdmin($token = null,$uid = null,$adminuid = null){
        if(!is_numeric($uid) || !is_numeric($adminuid)){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $info = TokenHelper::getInstance()->get($token);
        $caller_uid = $info['uid'];
        if($caller_uid === $uid){
            $sql = "delete from ss_roomadmin where uid = {$uid} and adminuid = {$adminuid}";
            $rows = M()->execute($sql);
            if($rows >= 0){
                $this->responseSuccess(L('_SUCCESS_'));
            }
        }else{
            $this->responseError(L('_VALID_ACCESS_'));
        }

    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param type var Description
     *
     * @param token string 通行证
     * @param roomid bigint 房间号
     * @param title string 标题
     * @param ptid int 直播私密类型 有就传  对应privatetype表id
     * @param prerequisite string(15) 前提条件 密码最多8位
     *
     *
     *
     **/
    public function createRoom($token = NULL,$roomid = 0,$title = NULL,$province = NULL,$address = NULL, $ptid = 0, $prerequisite = NULL, $third_party_id = 0, $channel_id = 0, $channel_name=null, $ischarge = '0', $cost = 0,$orientation = 'h' )
    {
        
        $info = TokenHelper::getInstance()->get($token);
        if($info == NULL){
            $this->responseError("no such user ".$info['uid']);
        }
        $uid = $info['uid'];
        if( count( M('member')->where("roomstatus = '3' and id = ".$uid)->find() ) > 0 ){
            $this->responseError(L('_BAN_PLAY_'));
        }
        if($roomid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        
        if(M('siteconfig')->where('id=1')->getField('canlive') == "n" && M('member')->where('id = '.$uid)->getField('approveid') == "无"){
             $this->responseError('请实名认证');
        }
        if(M('siteconfig')->where('id=1')->getField('sign_verification') == "1" && M('member')->where('id = '.$uid)->getField('sign') == "n"){
            $this->responseError(L('_VERIFICATION_'));
        }
        $ban_time = M('siteconfig')->where("id = 1 ")->field(" room_start_time, room_stop_time ")->find();
        $now_time = time();
        if($ban_time['room_stop_time'] <= $now_time && $now_time < $ban_time['room_start_time'] ){
            $this->responseError(L('_CLOSE_LIVE_TIME_'));
        }
        if($address != NULL && $province != NULL){
            $User = new UserAction();
            $User->setAddress($uid,$province,$address,$orientation);
        }
        //更新开播时间
        M('member')->where('id = '.$uid)->setField('starttime',time());

    	//查看是否是游戏直播（新添加的）
    	$gameType = $_REQUEST['gameType'];
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
    	$game_mem = new \Memcached();
    	$game_mem->addServer($ip, $port);
    	$game_mem->set($roomid.'gameType',$gameType); 

        $Topic = new TopicAction();
        $rs = $Topic->createTopic($title, $uid, $roomid, $ptid, $prerequisite, $third_party_id, $channel_id, $channel_name, $token, $ischarge, $cost);

        $key = C('LIVE_STATUS_PREFIX').$roomid;
        // 写入memcache标识该APP正在直播.
        TokenHelper::getInstance()->set($key, 'y',  0);
        $this->responseSuccess($rs);
    }

    public function onRoomOrientationChange($roomId,$orientation ) {
        $data = array();
        $data['orientation'] = $orientation;
        $rs = M("member")->data($data)->where(array('curroomnum'=>$roomId))->save();

        $this->responseSuccess($rs);
    }

    public function getRoomBack($roomid){
        $time = time();
        $startTime = $time - 60*60*24*30;
        $endTime = time();
        $Qiniu = new QiniuAction();
        $Segments = $Qiniu ->getSegmentsArray($roomid,$startTime,$endTime); //七牛的视频回放列
        $backStream = M("backstream")->where(array('starttime'=>array('egt',$startTime),'starttime'=>array('elt',$endTime),'roomid'=>$roomid))->order("starttime desc")->select(); //数据库中的回放列表记
        $topicList = M("topic")->select();
        $finalList = array();
        // 因为我们数据库记录starttime时间与七牛时间可能出现延迟不符，所以需要在这里过滤
        // 保留我们数据库数据量，以我们数据库为准来找七牛的数据
        // 当我们数据库里的starttime时间处于七牛的时间的区间内或七牛两条记录之间 即代表是那段区间的七牛数据
        // 不要想看懂上一句，因为我自己都读不懂。但是我知道我是什么意思，因为这种复杂的地方好像应该留点注释，不写注释不好意思的
        foreach ($backStream as $key => $stream) {
             foreach($Segments as $segKey=>$Segment){

                 if( ( $stream['starttime'] <= $Segments[$segKey]['end'] ) 
                    &&  $stream['starttime'] >= ( isset($Segments[$segKey+1]['end']) ? $Segments[$segKey+1]['end'] : 0 ) ){
                     if(!empty($backStream[$key]['topics']))
                     {
                         $where['id'] = array("in",$backStream[$key]['topics']);
                         $topicList = M("topic")->where($where)->field('id,title')->select();
                         $List['topic'] = $topicList;
                     }else{
                         $List['topic'] = NULL;
                     }
                     if(strlen($backStream[$key]['title']) > 1){
                         $List['title'] = $backStream[$key]['title'];
                     }else{
                         $List['title'] = NULL;
                     }
                     $List['roomid'] = $roomid;
                     $List['starttime'] = $Segments[$segKey]['start'];
                     $List['endtime'] = $Segments[$segKey]['end'];
                     $List['localtime'] = $backStream[$key]['starttime'];
                     $List['streamstatus'] = $backStream[$key]['streamstatus'];
                     $List['viewernum'] = $backStream[$key]['viewer'];
                     array_push($finalList,$List);
                }
            }
        }
        $this->responseSuccess($finalList);
    }
    public static function getRoomBackByStarttime($roomid, $starttime){
        $time = time();
        $startTime = $time - 60*60*24*30;
        $endTime = time();
        $Qiniu = new QiniuAction();
        $Segments = $Qiniu ->getSegmentsArray($roomid,$startTime,$endTime); //七牛的视频回放列
        $backStream = M("backstream")->where(array('starttime'=>$starttime,'roomid'=>$roomid))->select(); 
        //数据库中的回放列表记
        $finalList = array();
        // 因为我们数据库记录starttime时间与七牛时间可能出现延迟不符，所以需要在这里过滤
        // 保留我们数据库数据量，以我们数据库为准来找七牛的数据
        // 当我们数据库里的starttime时间处于七牛的时间的区间内或七牛两条记录之间 即代表是那段区间的七牛数据
        // 不要想看懂上一句，因为我自己都读不懂。但是我知道我是什么意思，因为这种复杂的地方好像应该留点注释，不写注释不好意思的
        foreach ($backStream as $key => $stream) {
             foreach($Segments as $segKey=>$Segment){

                 if( ( $stream['starttime'] <= $Segments[$segKey]['end'] ) 
                    &&  $stream['starttime'] >= ( isset($Segments[$segKey+1]['end']) ? $Segments[$segKey+1]['end'] : 0 ) ){
                     if(!empty($backStream[$key]['topics']))
                     {
                         $where['id'] = array("in",$backStream[$key]['topics']);
                         $topicList = M("topic")->where($where)->field('id,title')->select();
                         $List['topic'] = $topicList;
                     }else{
                         $List['topic'] = NULL;
                     }
                     if(strlen($backStream[$key]['title']) > 1){
                         $List['title'] = $backStream[$key]['title'];
                     }else{
                         $List['title'] = NULL;
                     }
                     $List['roomid'] = $roomid;
                     $List['starttime'] = $Segments[$segKey]['start'];
                     $List['endtime'] = $Segments[$segKey]['end'];
                     $List['localtime'] = $backStream[$key]['starttime'];
                     $List['streamstatus'] = $backStream[$key]['streamstatus'];
                     array_push($finalList,$List);
                     break;
                }
            }
        }
        return $finalList;
    }
    /**
     *  删除回播记录,断连接之前请求
     *  
     *  @param string token 通行证 
     */
    public function deleteRoomBack($token){
	$this->responseError('Useless API');
        $info = TokenHelper::getInstance()->get($token);
        $backstream_dao = M('backstream');
        $backstream_dao->startTrans();
        $backstream = $backstream_dao->where('uid = '.$info['uid'].' and streamstatus = "1"')->find();
        $plid = M('privatelimit')->where('bsid = '.$backstream['id'])->getField('id');
        $del_pl = M('privatelimit')->where('bsid = '.$backstream['id'])->delete();
        $del_pr = M('privaterecord')->where('plid = '.$plid)->delete();        
        $del_bs = $backstream_dao->where('uid = '.$info['uid'].' and starttime = '.$starttime)->delete();
        if($del_bs != false){
            $backstream_dao->commit();
            $this->responseSuccess(L('_SUCCESS_'));
        }else{
            $backstream_dao->rollback();
        }
        $this->responseError(L('_SELECT_NOT_EXIST_'));
    }

    /**
     *  删除回播记录
     *  
     *  @param string token 通行证 
     *  @param int starttime 回播开始时间 
     */
    public function deleteBackstream($token, $starttime = 0){
        if($starttime == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $info = TokenHelper::getInstance()->get($token);
        $backstream_dao = M('backstream');
        $backstream_dao->startTrans();
        $backstream = $backstream_dao->where('uid = '.$info['uid'].' and starttime = '.$starttime)->find();
        $plid = M('privatelimit')->where('bsid = '.$backstream['id'])->getField('id');
        $del_pl = M('privatelimit')->where('bsid = '.$backstream['id'])->delete();
        $del_pr = M('privaterecord')->where('plid = '.$plid)->delete();
        $del_bs = $backstream_dao->where('uid = '.$info['uid'].' and starttime = '.$starttime)->delete();
        if($del_bs != false){
            $backstream_dao->commit();
            $this->responseSuccess(L('_SUCCESS_'));
        }else{
            $backstream_dao->rollback();
        }
        $this->responseError(L('_SELECT_NOT_EXIST_'));
    }

    /**
     *  举报房间
     *
     *  @param int $roomId 被举报房间id
     */
    public function report($user_id) {
        if(empty($user_id)) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        if( count(M("Report")->where(" accused={$user_id} and uid={$this->current_uid} ")->select()) ){
            $this->responseError( L("_ALREADY_REPORTED_") );
        }

        $data = array(
            "accused" => $user_id,
            "uid" => $this->current_uid,
            "time" => time(),
            'bsid' => 0,
        );
        M("Report")->add($data);
        $this->responseSuccess( L("_OPERATION_SUCCESS_") );
    }
}
