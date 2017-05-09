<?php
class PrivateAction extends BaseAction{
    public function __construct()
    {
        parent::__construct();
    }


    /**
    *   获取私密类型接口
    *   返回data数组
    *   @param token string 通行证
    *
    **/
    public function getPrivateType($token){
        $this->responseSuccess(M('privatetype')->select());
    }

    /**
    *   获取房间私密限制数据
    *   @param token string 通行证
    *   @param roomid bigint 房间号
    *   @param starttime int 开始时间
    */
    // public function getPrivateLimit($token = NULL, $uid = NULL, $starttime =NULL ){
    //     if(empty($starttime)){
    //         $this->responseError(L('_PARAM_ERROR_'));
    //     }
    //     $bsid = M('backstream')->where('starttime = '.$starttime.' and uid = '.$uid)->getField("id");
    //     // if(empty($bsid)){
    //     //     $this->responseError(L('_NOT_OPEN_'));
    //     // }
    //     $data = M('privatelimit')->where('bsid='.$bsid)->find();
    //     if(count($data) <= 0 ){
    //         $this->responseError(L('_NOT_OPEN_'));
    //     }
	   //  $data['ptname'] = M('privatetype')->where('id='.$data['ptid'])->getField("name");
    //     $data['come'] = 0;
    //     if($this->queryPrivateRecord($token, $data['id'], $this->current_uid, $uid)){
    //             $data['come'] = 1;
    //     }
    //     if($data['ptid'] == 1){
    //         unset($data['prerequisite']);
    //     }
	   // $this->responseSuccess($data);
    // }
    public function getPrivateLimit($token = NULL, $uid = NULL, $starttime =NULL ){
        if(empty($starttime)){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $bsid = M('backstream')->where('starttime = '.$starttime.' and uid = '.$uid)->getField("id");
        $data = M('privatelimit')->where('bsid='.$bsid)->find();
        if(count($data) > 0 ){
            $data['ptname'] = M('privatetype')->where('id='.$data['ptid'])->getField("name");
            $data['come'] = 0;
            if($this->queryPrivateRecord($token, $data['id'], $this->current_uid, $uid)){
                    $data['come'] = 1;
            }
            if($data['ptid'] == 1){
                unset($data['prerequisite']);
            }
        }else{
            $data['ptname'] = '';
            $data['come']   = 0;
        }
       $this->responseSuccess($data);
    }
    public function getNowPrivateLimitForWeb(){
        $this->getNowPrivateLimit($_POST['token'],$_POST['uid']);
     }
    /**
    *   获取房间私密限制数据
    *   @param token string 通行证
    *   @param roomid bigint 房间号
    */
    public function getNowPrivateLimit($token = NULL, $uid = NULL ){

        $bsid = M('backstream')->where('streamstatus = "1" and uid = '.$uid)->getField("id");
        if( $bsid ){
            $online = 1;
        }else{
            $online = 0;
        }
        $data = M('privatelimit')->where('bsid='.$bsid)->find();
        $data['ptname'] = M('privatetype')->where('id='.$data['ptid'])->getField("name");
        $data['come'] = 0;
        $data['online'] = $online;
        if($this->queryPrivateRecord($token, $data['id'], $this->current_uid, $uid)){
                $data['come'] = 1;
        }
        if($data['ptid'] == 1){
            unset($data['prerequisite']);
        }
        $this->responseSuccess($data);
    }
    /**
    *   恢复开放房间|私密恢复
    *   @param token string 通行证
    *   @param plid int //privatelimit表id 私密限制id
    */
    public function privateRecovery($plid = 0){
        if($plid == 0 ){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        if(M('privatelimit')->where('id='.$plid)->delete()){
            $this->responseSuccess(L('_SUCCESS_'));
        }else{
            $this->responseError(L('_FAILED_'));
        }
    }
    /**
    *   生成私密记录  用于记录用户进入主播房间记录
    *   @param token string 通行证
    *   @param plid int 私密限制id
    *   @param uid int 用户id
    *   @param aid int 主播id
    */
    public function createPrivateRecord($token = NULL, $plid = 0, $uid = 0, $aid = 0, $coinbalance = 0){
        if($plid == 0 || $uid == 0 || $aid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }

        $data = array(
            'plid'  =>  $plid,
            'uid'   =>  $uid,
            'aid'   =>  $aid
        );
        if(M('privaterecord')->data($data)->add() > 0){
            if($coinbalance == 0)
                $this->responseSuccess(L('_SUCCESS_'));
            else
                $this->responseSuccess(array('coinbalance'=>$coinbalance));

        }else{
            $this->responseError(L('_FAILED_'));
        }
    }
    /**
    *   查询私密访问记录
    *   @param token string 通行证
    *   @param plid int 私密限制id
    *   @param uid int 用户id
    *   @param aid int 主播id
    */
    private function queryPrivateRecord($token = NULL, $plid = 0, $uid = 0, $aid = 0){
        $data = array(
            'plid'  =>  $plid,
            'uid'   =>  $uid,
            'aid'   =>  $aid
        );
        if(count(M('privaterecord')->where($data)->select()) > 0){
            return true;
        }else{
            return false;
        }
    }
    //checkPrivatePassweb端接口
    public function checkPrivatePassForWeb(){
        $this->checkPrivatePass($_POST['token'],$_POST['plid'],$_POST['prerequisite'],$_POST['uid'],$_POST['aid']);
    }
    /**
    *   验证条件
    *   验证密码调用接口
    *   @param string token     通行证
    *   @param int plid     privatelimit的id 私密限制id
    *   @param string prerequisite  进入房间的前提条件
    *   @param int uid      用户id
    *   @param int aid      主播id
    */
    public function checkPrivatePass($token = NULL, $plid = 0, $prerequisite = NULL, $uid = 0, $aid = 0){
        if($plid == 0 || empty($prerequisite) || $uid == 0|| $aid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }

        //判断是否已经进入过该房间
        if($this->queryPrivateRecord($token, $plid, $uid, $aid)){
            $this->responseSuccess(L('_WELCOME_'));
        }
        $prerequsite_this = M('privatelimit')->where('id='.$plid)->getField('prerequisite');
        if(empty($prerequsite_this)){
            $this->responseError(L('_SELECT_NOT_EXIST_'));
        }
        if($prerequsite_this == $prerequisite){
            $this->createPrivateRecord($token, $plid, $uid, $aid);
        }else{
            $this->responseError(L('_PASS_ERROR_'));
        }
    }
    // web端checkPrivateCharge接口
    public function checkPrivateChargeForWeb(){
        $this->checkPrivateCharge($_POST['token'],$_POST['plid'],$_POST['uid'],$_POST['aid']);
    }
    /**
    *   验证条件
    *   验证收票调用接口
    *   @param string token     通行证
    *   @param string prerequisite  进入房间的前提条件
    *   @param int plid     privatelimit的id 私密限制id
    *   @param int uid      用户id
    *   @param int aid      主播id
    */
       public function checkPrivateCharge($token = NULL, $plid = 0,   $uid = 0, $aid = 0){
        if($plid == 0 || $uid == 0|| $aid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        //判断是否已经进入过该房间
        if($this->queryPrivateRecord($token, $plid, $uid, $aid)){
            $this->responseSuccess(L('_WELCOME_'));
        }
        $prerequisite = M('privatelimit')->where('id='.$plid)->getField('prerequisite');
        if(empty($prerequisite)){
            $this->responseError(L('_SELECT_NOT_EXIST_'));
        }
        if(!is_numeric($prerequisite)){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        $userInfo = M('member')->where('id = '.$uid)->field('id, coinbalance,spendcoin,nickname')->find();
        $anchorInfo = M('member')->where('id = '.$aid)->field('id, coinbalance, spendcoin,nickname')->find();
        if($userInfo['coinbalance'] == null){
            $this->responseError(L('_USER_DOES_NOT_EXIST_'));
        }
        // echo (int)$user_level[0][levelid]." >= ".(int)$level;
        if((int)$userInfo['coinbalance'] >= (int)$prerequisite){
            M('member')->where('id='.$uid)->setDec('coinbalance',$prerequisite);
            M('member')->where('id='.$aid)->setInc('beanorignal',$prerequisite); 
            $probability = M('siteconfig')->where('id=1')->getField('cash_proportion');
            $emceededuct = M('siteconfig')->where('id=1')->getField('emceededuct');
            $prerequisite = $prerequisite * $probability / 100 *$emceededuct/100 ;

            M('member')->where('id='.$aid)->setInc('beanbalance',$prerequisite);

            // 记入虚拟币交易明细
            $Coindetail = D('Coindetail');
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'ticket';
            $Coindetail->uid = $this->current_uid;
            $Coindetail->touid = $anchorInfo['id'];
            $Coindetail->giftid = 1;
            $Coindetail->giftcount = 1;
            $Coindetail->content = $userInfo['nickname'].L('_IN_').$anchorInfo['nickname'].L('_BUY_TICKET_');
            $Coindetail->objecticon = 'TICKET';
            $Coindetail->coin = $prerequisite;
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();

            $this->createPrivateRecord($token, $plid, $uid, $aid, ($userInfo['coinbalance'] - $prerequisite) );
        }else{
            $this->responseError(L('_NO_COIN_'));
        }
    }
    //checkPrivatePassweb端接口
    public function checkPrivateLevelForWeb(){
        $this->checkPrivateLevel($_POST['token'],$_POST['plid'],$_POST['uid'],$_POST['aid']);
    }
    /**
    *   验证条件
    *   验证等级调用接口
    *   @param string token     通行证
    *   @param string prerequisite  进入房间的前提条件
    *   @param int plid     privatelimit的id 私密限制id
    *   @param int uid      用户id
    *   @param int aid      主播id
    */
    public function checkPrivateLevel($token = NULL, $plid = 0,  $uid = 0, $aid = 0){
       if($plid == 0  || $uid == 0|| $aid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        //判断是否已经进入过该房间
        if($this->queryPrivateRecord($token, $plid, $uid, $aid)){
            $this->responseSuccess(L('_WELCOME_'));
        }
        $level = M('privatelimit')->where('id='.$plid)->getField('prerequisite');
        if(empty($level)){
            $this->responseError(L('_SELECT_NOT_EXIST_'));
        }
        if(!is_numeric($level)){
            $this->responseError(L('_PARAM_ERROR_'));
        }

        $spendcoin = M('member')->where('id = '.$uid)->getField('spendcoin');
        if($spendcoin == null){
            $this->responseError(L('_USER_DOES_NOT_EXIST_'));
        }
        $user_level = getRichlevel($spendcoin);
        // echo (int)$user_level[0][levelid]." >= ".(int)$level;
        if((int)$user_level[0][levelid] >= (int)$level){
            $this->createPrivateRecord($token, $plid, $uid, $aid);
        }else{
            $this->responseError(L('_NOT_ENOUGH_LEVEL_'));
        }
    }
    /**
    *   验证条件
    *   验证用户金额是否足够进入房间的接口
    *   @param string token     通行证
    *   @param string prerequisite  进入房间的前提条件
    *   @param int plid     privatelimit的id 私密限制id
    *   @param int uid      用户id
    *   @param int aid      主播id
    */
       public function checkUserMoney($token = NULL, $plid = 0,   $uid = 0, $aid = 0){
        if($plid == 0 || $uid == 0|| $aid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        //判断是否已经进入过该房间
        if($this->queryPrivateRecord($token, $plid, $uid, $aid)){
            $this->responseSuccess(L('_WELCOME_'));
        }
        $prerequisite = M('privatelimit')->where('id='.$plid)->find();
        if(empty($prerequisite)){
            $this->responseError(L('_SELECT_NOT_EXIST_'));
        }
//        if(!is_numeric($prerequisite)){
//            $this->responseError(L('_PARAM_ERROR_'));
//        }
        $userInfo = M('member')->where('id = '.$uid)->field('id, coinbalance,spendcoin,nickname')->find();
        $anchorInfo = M('member')->where('id = '.$aid)->field('id, coinbalance, spendcoin,nickname,curroomnum')->find();
        if($userInfo['coinbalance'] == null){
            $this->responseError(L('_USER_DOES_NOT_EXIST_'));
        }
        if((int)$userInfo['coinbalance'] >= 10){
            $chargeRoom_data['uid'] = $uid;
            $chargeRoom_data['anchor_id'] = $aid;
            $chargeRoom_find = M('Chargeroom')->where($chargeRoom_data)->find();
            if(!$chargeRoom_find){
                // 记入虚拟币交易明细
                $chargeRoom_data['room_id'] = $anchorInfo['curroomnum'];
                $chargeRoom_data['money'] = 10;
                $chargeRoom_data['create_time'] = time();
                $chargeRoom_data['update_time'] = time();
                M('Chargeroom')->data($chargeRoom_data)->add();
            }else{
                $chargeRoom_update['create_time'] = time();
                $chargeRoom_update['update_time'] = time();
                $chargeRoom_data['money'] = 10;
                M('Chargeroom')->where($chargeRoom_data)->save($chargeRoom_update);
            }
            M('member')->where('id='.$uid)->setDec('coinbalance',10);
            $data = array();
            $data['beanbalance'] = array('exp','`beanbalance`+10');
            $data['beanorignal'] = array('exp','`beanorignal`+10');
            M('member')->where('id='.$aid)->save($data);
            $this->createPrivateRecord($token, $plid, $uid, $aid);
        }else{
            $this->responseError(L('_NO_COIN_'));
        }
    }

}
?>
