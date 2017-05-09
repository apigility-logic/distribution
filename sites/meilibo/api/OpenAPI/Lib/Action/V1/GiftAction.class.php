

<?php
/**
 * Created by PhpStorm.
 * User: shellvon
 * Date: 16/4/21
 * Time: 下午4:24.
 */
class GiftAction extends  BaseAction
{
    protected $default_msg = array(
        'category' => 'gift_api',
        'ref' => '礼物相关API',
        'links' => array(
            'gift_collection_url' => array(
                'href' => 'v1/gift/collection',
                'ref' => '礼物列表',
                'method' => 'GET',
                'parameters' => array('token' => 'string, required'),
            ),
            'send_gift_url' => array(
                'href' => 'v1/gift/send',
                'ref' => '送礼物',
                'method' => 'POST',
                'parameters' => array(
                    'token' => 'string, required',
                    'to_uid' => 'integer, required',
                    'gift_id' => 'integer, required',
                    'count' => 'integer, required',
                ),
            ),
        ),
    );

    /**
     * 礼物列表.
     */
    public function collection()
    {
        //礼物
        $gifts = D('Gift')->where(array('enable'=>1))->order('needcoin asc')->getField('id, gifticon, needcoin, giftname,isred,rednum');
        $end_with = function ($haystack, $needle) {
            $length = strlen($needle);
            if ($length == 0) {
                return true;
            }

            return substr($haystack, -$length) === $needle;
        };
        $gift_without_gif = array();
        foreach ($gifts as $gift_id => $gift) {
            // 安卓端不支持gif,得去掉gif的表情.
           if (!$end_with($gift['gifticon'], '.gif')) {
               $gift_without_gif[] = $gift;
           }
        }
        //特殊礼物的调整到第一位
        foreach ($gift_without_gif as $key => $value){
            if($value['id'] == 20){
                $special_gift = $gift_without_gif[$key];
                unset($gift_without_gif[$key]);
                array_unshift($gift_without_gif, $special_gift);
            }
        }
        $this->responseSuccess($gift_without_gif);
    }


    //TODO: 修改它.
    /**
     * @param null $to_uid  送的目标用户ID.
     * @param null $gift_id 礼物ID.
     * @param null $count   礼物数量.
     * @param null $kk      我不知道这是什么(你不知道是什么就不要加备注)
     */
    public function send($to_uid = null, $gift_id = null, $count = null, $kk = null)
    {
        if(empty($to_uid)){
            !empty($_POST['to_uid']) ? $to_uid = $_POST['to_uid'] :$to_uid = null;
        }
        if(empty($gift_id)){
            !empty($_POST['gift_id']) ? $to_uid = $_POST['gift_id'] :$gift_id = null;
        } 
        if(empty($count)){
            !empty($_POST['count']) ? $to_uid = $_POST['count'] :$count = null;
        } 
        if (!APP_DEBUG && !$this->isPost()) {
            $this->forbidden();
        }
        if (empty($gift_id) || empty($count) || empty($to_uid)) {
            $this->responseError(L('_PARAM_ERROR_'));
        } elseif (!(is_numeric($to_uid) && is_numeric($gift_id) && is_numeric($count))) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        if ($this->current_uid == $to_uid) {
            $this->responseError(L('_GIFT_GIVE_YOURSELF_'));
        }
        //
        $user_info = D('Member')->find($this->current_uid);
        // 获取被赠送人信息
        $emcee_info = D('Member')->find($to_uid);
        // 根据gid获取礼物信息
        $gift_info = D('Gift')->find($gift_id);

        if (empty($gift_info)) {
            $this->responseError(L('_GIFT_DOES_NOT_EXIST_'));
        }
        if (empty($emcee_info)) {
            $this->responseError(L('_TOUID_ILLEGAL_'));
        }
        // 判断虚拟币是否足够
        $need_coin = $gift_info['needcoin'] * $count;

        $site_config = D('Siteconfig');
        $site = $site_config->find();

        if (trim($kk) != 'kc') {
            if ($user_info['coinbalance'] < $need_coin) {
                $this->responseError(L('_NO_COIN_'));
            }
            D('Member')->execute('update ss_member set spendcoin=spendcoin+'
                .$need_coin.',coinbalance=coinbalance-'.$need_coin.' where id='.$this->current_uid);
            $verification = $this->current_uid.$gift_id.$count.$need_coin;
            D('Member')->execute("update ss_member set isdebit='{$verification}' where id={$this->current_uid}");

            // 记入虚拟币交易明细
            $Coindetail = D('Coindetail');
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'sendgift';
            $Coindetail->uid = $this->current_uid;
            $Coindetail->touid = $to_uid;
            $Coindetail->giftid = $gift_id;
            $Coindetail->gtype = $gift_info['sid'];
            $Coindetail->giftcount = $count;
            $Coindetail->content = $user_info['nickname'].L('_GIFT_SENT_').$emcee_info['nickname'].L('_A_GIFT_').$gift_info['giftname'].' X '.$count;
            $smallIcon = $gift_info['gifticon_25'];
            $Coindetail->objecticon = $smallIcon;
            $Coindetail->coin = $need_coin;
            if ($emcee_info['broadcasting'] == 'y') {
                $Coindetail->showid = $emcee_info['showid'];
            }
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();
            
            //向游戏服务器兑换接口发送信息
            if($gift_info['sid'] == 3){
                $game_url = "http://gapi.meilibo.net/Home/Api/add_gold_key";
                $game_data = array('uid'=>$this->current_uid,'giftcount'=>$count,'giftcoin'=>$need_coin);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $game_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $game_data);
                curl_exec($ch);
                curl_close($ch);
            }
            // $scale = D('Member')->getByid($to_uid); //取出改主播的信息
            // if ($scale['sharingratio'] != '0') { // 优先按照指定的比例算
            //     $beannum = $need_coin * ($scale['sharingratio'] / 100);
            // $scale = D('Member')->getByid($songinfo['uid']); // 取出改主播的信息
            // if ($scale['sharingratio'] != '0') { // 优先按照指定的比例算
            
            //特殊礼物和游戏礼物的比例调整
            
            if($gift_info['sid'] == 3){
               $gift_ratio = M('Giftsort')->where(['orderno'=>$gift_info['sid']])->find();
               $beannum = $need_coin * (int)$gift_ratio['ratio'] / 100;
            }elseif($gift_info['sid'] == 4){
                $data = array(
                    'uid'     => $this->current_uid,
                    'to_uid'  => $to_uid,
                    'gift_id' => $gift_id,
                    'count'   => $count,
                    'status'  => 0,
                    'create_time'=>time(),
                );
                M('Exclusivegift')->data($data)->add();
               $gift_ratio = M('Giftsort')->where(['orderno'=>$gift_info['sid']])->find();
               $beannum = $need_coin * (int)$gift_ratio['ratio'] / 100;
            }else{
            
                if ($emcee_info['agentuid'] != 0) {
                    $ratio = D('Agentfamily')->where('uid='.$emcee_info['agentuid'])->getField('uid,familyratio,anchorratio');
                    $ratio = $ratio[$emcee_info['agentuid']];
                    $beannumAgent = $need_coin * ($ratio['familyratio'] /100);
                    $beannum = $need_coin * ($ratio['anchorratio'] / 100);
                } else {
                    //默认的比例
                    $beannumAgent = 0;
                    $beannum = $need_coin * ($site['emceededuct'] / 100);
                }
            }
            $anchorBean = $emcee_info['beanbalance'] + $need_coin;
            $anchorBeanOrignal = $emcee_info['beanorignal'] + $need_coin;

            D('Member')->execute('update ss_member set earnbean=earnbean+'.$beannum.
                ',beanbalance=beanbalance+'.$beannum.',beanorignal=beanorignal+'.$need_coin.' where id='.$to_uid);
            $Beandetail = D('Beandetail');
            $Beandetail->create();
            $Beandetail->type = 'income';
            $Beandetail->action = 'getgift';
            $Beandetail->gtype = $gift_info['sid'];
            $Beandetail->uid = $to_uid;
            $Beandetail->content = $user_info['nickname'].L('_GIFT_SENT_').$emcee_info['nickname'].L('_A_GIFT_').$gift_info['giftname'].' X '.$count;

            $Beandetail->bean = $beannum;
            $Beandetail->addtime = time();
            $detailId = $Beandetail->add();

            if ($emcee_info['agentuid'] != 0) {
                // $beannum = $need_coin * ($site['emceeagentdeduct'] / 100);
                D('Member')->execute('update ss_member set beanbalance2=beanbalance2+'.
                    $beannumAgent.' where id='.$emcee_info['agentuid']);
                $Emceeagentbeandetail = D('Emceeagentbeandetail');
                $Emceeagentbeandetail->create();
                $Emceeagentbeandetail->type = 'income';
                $Emceeagentbeandetail->action = 'getgift';
                $Emceeagentbeandetail->uid = $emcee_info['agentuid'];
                $Emceeagentbeandetail->content = $user_info['nickname'].L('_GIFT_SENT_').$emcee_info['nickname'].L('_A_GIFT_').$gift_info['giftname'].' X '.$count;
                $Emceeagentbeandetail->bean = $beannumAgent;
                $Emceeagentbeandetail->addtime = time();
                $detailId = $Emceeagentbeandetail->add();
            }
            $tmp = getRichlevel($user_info['spendcoin'] + $need_coin);
            $level_id = $tmp[0]['levelid'];

            //计算连击值
            $mcKey = "combo_hit_".$to_uid."_".$this->current_uid."_".$gift_id;
            if(!$this->mmc->get($mcKey)) {
                $this->mmc->set($mcKey,0,3600*5);
            }
            $comboHit = $this->mmc->increment($mcKey, 1);


            $data = array(
                "isred" => $gift_info['isred'],
                'giftPath' => $smallIcon,
                'giftStyle' => $gift_info['giftstyle'],
                'giftGroup' => $gift_info['sid'],
                'giftType' => $gift_info['gifttype'],
                'toUserNo' => $emcee_info['curroomnum'],
                'isGift' => 0,
                'comboHit' =>  $comboHit,
                'anchorBalance'=> $anchorBeanOrignal,
                'giftLocation' => array(),
                'giftIcon' => $gift_info['gifticon'],
                'giftSwf' => $gift_info['giftswf'],
                'toUserId' => $to_uid,
                'toUserName' => $emcee_info['nickname'],
                'userNo' => $user_info['curroomnum'],
                'giftCount' => $count,
                'userId' => $this->current_uid,
                'giftName' => $gift_info['giftname'],
                'userName' => $user_info['nickname'],
                'giftId' => $gift_info['id'],
                'from_client_name' => $user_info['nickname'],
                'from_client_avatar' => getAvatar($user_info['avatartime'],$user_info['id'], 'small'),
                'type' => 'sendGift',
                'code' => 0,
                'time' => date('H:i', time()),
                'from_user_id'=> $this->current_uid,
                'vip'=> (!$user_info['vip'] ? 0 : $user_info['vip']),
                'levelid'=> $level_id,
            );

            import('Common.Gateway', APP_PATH, '.php');
            Gateway::$registerAddress = C('REGISTER_ADDRESS');
            $client_id = Gateway::getClientIdByUid($this->current_uid);
            if (!empty($client_id)) {
                $session = Gateway::getSession($client_id[0]);
                $session['levelid'] = $level_id;
                Gateway::updateSession($client_id['0'], $session);
            }
            Gateway::sendToGroup($emcee_info['curroomnum'], json_encode($data));
            $this->responseSuccess(array('coinbalance' => M('member')->where('id='.$this->current_uid)->getField('coinbalance') ) );
        } else {
        }
        $this->forbidden();
    }


    /*
            红包列表
     */

    public function sendredgift($token = null,$to_uid = null,$gift_id = null,$count = 1, $kk = null){
        if (!APP_DEBUG && !$this->isPost()) {
            $this->forbidden();
        }
        if (empty($gift_id) || empty($to_uid)) {
            $this->responseError(L('_PARAM_ERROR_'));
        } elseif (!(is_numeric($to_uid) && is_numeric($gift_id))) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        // if ($this->current_uid == $to_uid) {
        //     $this->responseError('不可以给自己发礼物');
        // }


        $user_info = D('Member')->find($this->current_uid);
        // 获取被赠送人信息
        $emcee_info = D('Member')->find($to_uid);
        // 根据gid获取礼物信息
        $gift_info = D('Gift')->find($gift_id);

        if (empty($gift_info)) {
            $this->responseError(L('_NOT_GIFT_'));
        }
        if (empty($emcee_info)) {
            $this->responseError('to_uid参数不合法!');
        }
        // 判断虚拟币是否足够
        $need_coin = $gift_info['needcoin'];

        $site_config = D('Siteconfig');
        $site = $site_config->find();

        if (trim($kk) != 'kc') {
            if ($user_info['coinbalance'] < $need_coin) {
                $this->responseError(L('_NO_COIN_'));
            }
            D('Member')->execute('update ss_member set spendcoin=spendcoin+'
                .$need_coin.',coinbalance=coinbalance-'.$need_coin.' where id='.$this->current_uid);
            $verification = $this->current_uid.$gift_id.'1'.$need_coin;
            D('Member')->execute("update ss_member set isdebit='{$verification}' where id={$this->current_uid}");

            // 记入虚拟币交易明细
            $Coindetail = D('Coindetail');
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'sendgift';
            $Coindetail->uid = $this->current_uid;
            $Coindetail->touid = $to_uid;
            $Coindetail->giftid = $gift_id;
            $Coindetail->giftcount = $count;
            $Coindetail->content = $user_info['nickname'].
                L('_IN_').$emcee_info['nickname'].L('_SENT_GIFT_').$gift_info['giftname'].' X 1';
            $smallIcon = $gift_info['gifticon_25'];
            $Coindetail->objecticon = $smallIcon;
            $Coindetail->coin = $need_coin;
            if ($emcee_info['broadcasting'] == 'y') {
                $Coindetail->showid = $emcee_info['showid'];
            }
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();

            //添加进红包发送记录表
            $sendData["uid"] = $this->current_uid;
            $sendData["roomid"] = $emcee_info['curroomnum'];
            $sendData["actiontime"] = time();
            $sendData["count"] = $gift_info['rednum'];
            $sendData["amount"] = $gift_info['needcoin'];
            $sendData["giftid"] = $gift_info['id'];
            $redId = M("redpacketsend")->add($sendData);

            // $sql = "insert into ss_redpacketsend(uid,roomid,actiontime,count,amount,giftid) values (%s,'%s',%s,%s,%s,%s)";
            // $sql = sprintf($sql,$this->current_uid,$emcee_info['curroomnum'],time(),$gift_info['rednum'],$gift_info['needcoin'],$gift_info['id']);
            // $redId = M("redpacketsend")->execute($sql);


            $tmp = getRichlevel($user_info['spendcoin'] + $need_coin);
            $level_id = $tmp[0]['levelid'];

            $data = array(
                "isred" => 1,
                "redId" => $redId,
                "amount" => $gift_info['needcoin'],
                "count" => $gift_info['rednum'],
                'giftCount' => 1,
                'giftPath' => $smallIcon,
                'giftStyle' => $gift_info['giftstyle'],
                'giftGroup' => $gift_info['sid'],
                'giftType' => $gift_info['gifttype'],
                'toUserNo' => $emcee_info['curroomnum'],
                'isGift' => 0,
                'anchorBalance'=> $anchorBeanOrignal,
                'giftLocation' => array(),
                'giftIcon' => $gift_info['gifticon'],
                'giftSwf' => $gift_info['giftswf'],
                'toUserId' => $to_uid,
                'toUserName' => $emcee_info['nickname'],
                'userNo' => $user_info['curroomnum'],
                'userId' => $this->current_uid,
                'giftName' => $gift_info['giftname'],
                'userName' => $user_info['nickname'],
                'giftId' => $gift_info['id'],
                'from_client_name' => $user_info['nickname'],
                'from_client_avatar' => getAvatar($user_info['avatartime'],$user_info['id'], 'small'),
                'type' => 'sendGift',
                'code' => 0,
                'time' => date('H:i', time()),
                'from_user_id'=> $this->current_uid,
                'vip'=> (!$user_info['vip'] ? 0 : $user_info['vip']),
                'levelid'=> $level_id,
            );

            // var_dump($data);
            import('Common.Gateway', APP_PATH, '.php');
            Gateway::$registerAddress = C('REGISTER_ADDRESS');
            Gateway::sendToGroup($emcee_info['curroomnum'], json_encode($data));
            $this->responseSuccess(array('coinbalance' => M('member')->where('id='.$this->current_uid)->getField('coinbalance') ));
            // TODO: 修改.
        } else {
        }
        $this->forbidden();
    }
    /*
     * 
     * 游戏赢到指定金额的飞屏特效
     *      */
    public function gameWinFly($token = null,$win_money = null,$game_id = null){
        if($game_id == 1){
            $game_name = '智勇三张';
        }elseif ($game_id == 2) {
            $game_name = '开心牛仔';
        }elseif ($game_id == 3) {
            $game_name = '猫鼠乱斗';
        }elseif ($game_id == 4){
            $game_name = '海盗船长';
        }elseif ($game_id == 5){
            $game_name = '水果精灵';
        }elseif ($game_id == 6){
            $game_name = '百人牛牛';
        }
        $user_find = M('Member')->where(['id'=> $this->current_uid])->find();
        if($win_money >= 10000){
            $data = array(
                'avatar'=>'/style/avatar/'.substr(md5($this->current_uid),0, 3).'/'.$this->current_uid.'_middle.jpg',
                'client_name'=>$user_find['nickname'],
                'content'=>'在'.$game_name.'游戏中获得了',
                'win_money'=>$win_money,
                'type'=>'gameWinFly'
            );
            import('Common.Gateway', APP_PATH, '.php');
            Gateway::$registerAddress = C('REGISTER_ADDRESS');
            Gateway::sendToAll(json_encode($data));
            echo json_encode(['message'=>'success']);
        }
    }
    /*
     * 
     * 主播进入房间时向观众发送他选择的游戏
     *      */
    public function changeGame($token = null,$game_id = '0',$roomid){
        $data = array(
            'data'=> (string) $game_id,
            'type'=>'gameChange'
        );
        import('Common.Gateway', APP_PATH, '.php');
        Gateway::$registerAddress = C('REGISTER_ADDRESS');
        Gateway::sendToGroup($roomid, json_encode($data));
        echo json_encode(['message'=>'success']);
    }
    /*
    * TODO:验证红包
    *
    *
     */
    protected function checkredgift($roomid = null,$red_id = null){
        $returnData[] = array();
        if (!APP_DEBUG && !$this->isPost()) {
            $this->forbidden();
        }
        if (empty($red_id) || empty($roomid)) {
            $returnData['Code'] = "0";
            $returnData['data'] = NULL;
            $returnData['msg'] = L('_PARAM_ERROR_');
            return $returnData;
        }
        // elseif (is_numeric($red_id) && is_numeric($roomid)) {
        //     $returnData['Code'] = "0";
        //     $returnData['data'] = NULL;
        //     $returnData['msg'] = "参数必须为整数";
        //     return $returnData;
        // }
        $redSelSQL = "select * from ss_redpacketsend where sendid = ".$red_id ." limit 0,1";
        $redEntity = M()->query($redSelSQL); // 获取红包主体信息
        if($redEntity == null){
            $returnData['Code'] = "0";
            $returnData['data'] = NULL;
            $returnData['msg'] = L('_GET_RED_ERROR_');
            return $returnData;
        }else{
            foreach($redEntity as $tempRed){
                $redEntity = $tempRed;
            }
            if($redEntity["roomid"] != $roomid){
                $returnData['Code'] = "0";
                $returnData['data'] = NULL;
                $returnData['msg'] = L('_NOT_RED_REGION_');
                return $returnData;
            }
            $isExSQL = "select * from ss_redpacketrec where sendid = ".$red_id." and uid = ".$this->current_uid;
            $isEx = M() -> query($isExSQL);
            if(count($isEx) >= 1){
                $returnData['Code'] = "0";
                $returnData['msg'] = L('_ALREADY_RECEIVED_');
                return $returnData;
            }

            $redrecSQL = "select count(id) count,sum(amount) amount from ss_redpacketrec where sendid = ".$red_id;
            $redRec = M() -> query($redrecSQL);
            foreach($redRec as $temp){
                $spendCount = $temp['count']; //获取已经发放的红包总数
                $spendAmount = $temp['amount'];
            }
            if($spendCount >= $redEntity['count']){
                $returnData['Code'] = "0";
                $returnData['data'] = NULL;
                $returnData['msg'] = L('_RED_IS_NOT_');
                return $returnData;
            }
            $data["amountSum"] = $redEntity['amount']; // 红包总金额
            $data["amount"] = $spendAmount; //红包已发送金额
            $data['countSum'] = $redEntity['count']; //红包总份数
            $data['count'] = $spendCount; //红包已发送份数

            $returnData['Code'] = "1";
            $returnData['data'] = $data;
            return $returnData;
        }
    }
    //返回剩余金额和剩余红包数量
    /**
    * @param $dataArr 当前红包主体的总额和已失去的额度
     */
    protected function getHaveMoney($dataArr){
        $size = $dataArr['countSum'] - $dataArr['count']; //获取剩下的份数
        $haveMoney = ($dataArr['amountSum'] - $dataArr['amount']) / 100; //获取剩下的金额
        if($size == 1){
            return $haveMoney * 100;
        }
        if($size <= 0){
            return;
        }
        if($haveMoney <= 0){
            return;
        }
        $money = $this -> getRandomMoney($size,$haveMoney); //获取随机生成的红包金额
        return $money * 100;  //因为在传入的时候/100了，所以返回的时候应该还原
    }
    /**
    * @param $roomid 房间号
    * @param $red_id 红包ID
    *
    * 抢红包
     */
    public function robredgift($roomid = null,$red_id = null){
        if (!APP_DEBUG && !$this->isPost()) {
            $this->forbidden();
        }
        if (empty($red_id) || empty($roomid)) {
            $this->responseError(L('_PARAM_ERROR_'));
        }
        // elseif (is_numeric($red_id) && is_numeric($roomid)) {
        //     $this->responseError('参数必须为整数');
        // }
        //检查红包信息

        $result = $this->checkredgift($roomid,$red_id);
        if($result['Code'] == "1"){
            //计算抢到多少钱
            $amount = $this -> getHaveMoney($result['data']); //抢到的红包钱
            //写入数据库
            $insertSQL = "insert into ss_redpacketrec (uid,actiontime,amount,sendid) values (%s,%s,%s,%s)";
            $insertSQL = sprintf($insertSQL,$this->current_uid,time(),$amount,$red_id);
            M() -> execute($insertSQL);
            $updateSQL = "update ss_member set coinbalance = coinbalance +".$amount." where id = ".$this->current_uid;
            M() -> execute($updateSQL);

            //返回当前信息 ??
            //
            //
            //
            //
            //
            $data['coinbalance'] = M('member')->where('id='.$this->current_uid)->getField('coinbalance');
            $data['amount'] = $amount;
            $data['uid'] = $this->current_uid;
            M('Member')->where('id='.$this->current_uid)->setInc('beanbalance',$data['amount']);
            $this->responseSuccess($data);
        }else{
            $this->responseError($result['msg']);
        }

    }

    /**
    * @param $size 红包大小/多少个
    * @param $haveMoney 红包剩余金额
    * 该函数默认最低为0.01元
    * 红包处理时需先将金额 / 100再传入
    **/
    protected function getRandomMoney($size,$haveMoney){
        if($size == 1){
            $money = $haveMoney;
            return $money;
        }
        $min = 0.01;
        $max = (double)$haveMoney / $size * 2;
        $money = $this -> randomFloat() * $max;
        $money = $money <= $min ? $min : $money;
        $money = floor($money * 100) / 100;
        return $money;
    }
    //随机值
    protected function randomFloat($min = 0, $max = 1) {
        $num = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return $this -> getdigits(2,$num);
    }
    //浮动位数
    protected function getdigits($place,$str){
        return sprintf("%.".$place."f",$str);
    }

    /**
      * 发送弹幕
      *
      **/
    public function sendBarrage($token = null, $roomid, $content){
        $uid = $this->current_uid;

        $adminer = json_decode($this->mmc->get("PHPCHAT_ROOM_".$roomid),true);

        if(isset($adminer['disableMsg'][$uid])   && (time() < $adminer['disableMsg'][$uid])) {
            $this->responseError(L('您已經被禁止發言'));
        }

        $needCoin = 30;
        $userInfo = M('member')->where('id = '.$uid)->field('id, coinbalance, spendcoin,avatartime,
nickname')->find();
        if($userInfo['coinbalance'] < $needCoin){
            $this->responseError(L('_NO_COIN_'));
        }
        $userInfo['coinbalance']-=$needCoin;
        $userInfo['spendcoin']+=$needCoin;
        $anchorInfo = M('member')->where('curroomnum = '.$roomid)->field('id,nickname,beanorignal')->find();
        // 记入虚拟币交易明细
        $Coindetail = D('Coindetail');
        $Coindetail->create();
        $Coindetail->type = 'expend';
        $Coindetail->action = 'barrage';
        $Coindetail->uid = $this->current_uid;
        $Coindetail->touid = $anchorInfo['id'];
        $Coindetail->giftid = 1;
        $Coindetail->giftcount = 1;

        $Coindetail->content = $userInfo['nickname'].L('_IN_').$anchorInfo['nickname'].L('_A_GIFT_')."发送弹幕";

        $Coindetail->objecticon = 'barrage';
        $Coindetail->coin = $needCoin;
        $Coindetail->addtime = time();
        $detailId = $Coindetail->add();

        if(M('member')->save($userInfo) > 0){
            //默认的比例
            $site = M('Siteconfig')->where('id=1')->find();
            $beannum = $needCoin * ($site['emceededuct'] / 100);
            D('Member')->execute('update ss_member set earnbean=earnbean+'.$beannum.
                ',beanbalance=beanbalance+'.$beannum.',beanorignal=beanorignal+'.$needCoin.' where id='.$anchorInfo['id']);
            $Beandetail = D('Beandetail');
            $Beandetail->create();
            $Beandetail->type = 'income';
            $Beandetail->action = 'getgift';
            $Beandetail->gtype = 1;
            $Beandetail->uid = $anchorInfo['id'];
            $Beandetail->content = $userInfo['nickname'].L('_GIFT_SENT_').$anchorInfo['nickname'].L('_A_GIFT_')."发送弹幕";

            $Beandetail->bean = $beannum;
            $Beandetail->addtime = time();
            $Beandetail->add();


            $tmp = getRichlevel($userInfo['spendcoin'] + $needCoin);
            $level_id = $tmp[0]['levelid'];
            $data = array(
                "avatar" => getAvatar($userInfo['avatartime'],$userInfo['id'], 'small'),
                "content" => $content,
                'from_client_name' => $userInfo['nickname'],
                "from_user_id" => $this->current_uid,
                "levelid" => $level_id,
                'time' => date('H:i', time()),
                "fly" => "FlyMsg",
                'vip'=> (!$userInfo['vip'] ? 0 : $userInfo['vip']),
                'type' => 'SendPubMsg',
                'anchorBalance'=>$anchorInfo['beanorignal'] + $needCoin."",
            );
            import('Common.Gateway', APP_PATH, '.php');
            Gateway::$registerAddress = C('REGISTER_ADDRESS');
            Gateway::sendToGroup($roomid, json_encode($data));
            $res['coinbalance'] = M('member')->where('id='.$uid)->getField('coinbalance');

            $client_id = Gateway::getClientIdByUid($this->current_uid);
            if (!empty($client_id)) {
                $session = Gateway::getSession($client_id[0]);
                $session['levelid'] = $level_id;
                Gateway::updateSession($client_id['0'], $session);
            }

            $this->responseSuccess($res);

        }else{
            $this->responseError(L('_SEND_FAILED_'));
        }
    }

    /**
     * 手机获取列表
     * 
     */  
    public function getShareGiftList(){
        $gift_list = M("gift")->where(" isred = '99' and enable = '2' ")->select();
        $this->responseSuccess($gift_list);

    }       
}
