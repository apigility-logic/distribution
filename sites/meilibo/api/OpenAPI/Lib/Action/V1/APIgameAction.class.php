<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class APIgameAction extends BaseAction
{
    static $sign_money = array(
        'firstDay'  => '20',
        'secondDay' => '30',
        'thirdDay'  => '35',
        'fourthDay' => '40',
        'fifthDay'  => '45',
        'sixthDay'  => '50',
    );
    public function giveGift($first_gift=15,$second_gift=113,$third_gift=5){
        $first_gift_info = M("Gift")->where('id='.$first_gift)->find();
        if(!$first_gift_info){
            $first_gift_info = 0;
        }
        $second_gift_info = M("Gift")->where('id='.$second_gift)->find();
        if(!$second_gift_info){
            $second_gift_info = 0;
        }
        $third_gift_info = M("Gift")->where('id='.$third_gift)->find();
        if(!$third_gift_info){
            $third_gift_info = 0;
        }
        echo json_encode(['first_gift'=>$first_gift_info,'second_gift'=>$second_gift_info,'third_gift'=>$third_gift_info]);
    }
    //领取秀币接口
    public function getCoin($uid,$nickname){
        $nowday = date('Ymd',time());
        $now_day = (int)$nowday;
        $user_find = M("getcoin")->where(['uid'=>$uid,'create_day'=>$now_day])->find();
        $user = M("Member")->where(['id'=>$uid])->find();
        $level = getRichlevel($user['spendcoin']);
        $userLevel = $level[0]['levelid'];
        if($userLevel >= 1 && $userLevel<5){
            $get_money = 10;
        }elseif ($userLevel >= 5 && $userLevel < 10) {
            $get_money = 15;
        }elseif ($userLevel >= 10 && $userLevel < 15) {
            $get_money = 20;
        }elseif ($userLevel >= 15 && $userLevel < 20) {
            $get_money = 30;
        }elseif ($userLevel >= 20 && $userLevel < 25) {
            $get_money = 40;
        }elseif ($userLevel >= 25 && $userLevel < 30) {
            $get_money = 50;
        }elseif ($userLevel >= 30 && $userLevel < 35) {
            $get_money = 60;
        }elseif ($userLevel >= 35 && $userLevel < 40) {
            $get_money = 70;
        }elseif ($userLevel >= 40 && $userLevel < 45) {
            $get_money = 80;
        }elseif ($userLevel >= 45 && $userLevel < 50) {
            $get_money = 90;
        }elseif ($userLevel >= 50 && $userLevel < 55) {
            $get_money = 100;
        }elseif ($userLevel >= 55 && $userLevel < 60) {
            $get_money = 110;
        }elseif ($userLevel >= 60 && $userLevel < 65) {
            $get_money = 120;
        }elseif ($userLevel >= 65 && $userLevel < 70) {
            $get_money = 130;
        }elseif ($userLevel >= 70 && $userLevel < 75) {
            $get_money = 140;
        }elseif ($userLevel >= 75 && $userLevel < 80) {
            $get_money = 150;
        }elseif ($userLevel >= 80 && $userLevel < 85) {
            $get_money = 160;
        }elseif ($userLevel >= 85 && $userLevel < 90) {
            $get_money = 170;
        }elseif ($userLevel >= 90 && $userLevel < 95) {
            $get_money = 180;
        }elseif ($userLevel >= 95 && $userLevel < 100) {
            $get_money = 190;
        }else{
            $get_money = 200;
        }
        if($user_find){
            //每次领取时间
            if($user_find['update_time']+300 <= time()){
                //领取次数
                if($user_find['get_number'] < 5){
                    $data = array();
                    $data['nickname']    = $nickname;
                    $data['get_money']   = array('exp', 'get_money+'.$get_money);
                    $data['get_number']  = array('exp', 'get_number+1');
                    $data['update_time'] = time();
                    $user_update = M("getcoin")->where(['uid'=>$uid,'create_day'=>$now_day])->save($data);
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.$get_money);
                    $ss_member = M("Member")->where(['id'=>$uid])->save($coin_data);
                    
                    $message = array();
                    $message['code']    = 0;
                    $message['message'] = 'success';
                    $message['data']    = array('time'=>time(),'leave'=>5-$user_find['get_number']-1);
                    echo json_encode($message);
                    exit;
                }else{
                    $message = array();
                    $message['code']    = 2;
                    $message['message'] = '你的领取次数已用完！';
                    $message['data']    = '';
                    echo json_encode($message);
                    exit;
                }
            }else{
                $message = array();
                $message['code']    = 3;
                $message['message'] = '没有到领取的时间';
                $message['data']    = '';
                echo json_encode($message);
                exit;
            }
        }else{
            $data = array();
            $data['uid']         = $uid;
            $data['nickname']    = $nickname;
            $data['get_money']   = $get_money;
            $data['get_number']  = 1;
            $data['update_time'] = time();
            $data['create_day']  = $now_day;
            $user_add = M("getcoin")->data($data)->add();
            if($user_add){
                $coin_data['coinbalance'] = array('exp', 'coinbalance+'.$get_money);
                $ss_member = M("Member")->where(['id'=>$uid])->save($coin_data);
                
                $message = array();
                $message['code']    = 0;
                $message['message'] = 'success';
                $message['data']    = array('time'=>time(),'leave'=>5-$user_find['get_number']-1);
                echo json_encode($message);
                exit;
            }else{
                $message = array();
                $message['code']    = 1;
                $message['message'] = '领取失败!';
                $message['data']    = array('time'=>time(),'leave'=>5-$user_find['get_number']-1);
                echo json_encode($message);
                exit;
            }
        }
    }
    
    public function getCoinTime($uid){
        $nowday = date('Ymd',time());
        $now_day = (int)$nowday;
        $user = M("Member")->where(['id'=>$uid])->find();
        $level = getRichlevel($user['spendcoin']);
        $userLevel = $level[0]['levelid'];
        if($userLevel >= 1 && $userLevel<5){
            $get_money = 10;
        }elseif ($userLevel >= 5 && $userLevel < 10) {
            $get_money = 15;
        }elseif ($userLevel >= 10 && $userLevel < 15) {
            $get_money = 20;
        }elseif ($userLevel >= 15 && $userLevel < 20) {
            $get_money = 30;
        }elseif ($userLevel >= 20 && $userLevel < 25) {
            $get_money = 40;
        }elseif ($userLevel >= 25 && $userLevel < 30) {
            $get_money = 50;
        }elseif ($userLevel >= 30 && $userLevel < 35) {
            $get_money = 60;
        }elseif ($userLevel >= 35 && $userLevel < 40) {
            $get_money = 70;
        }elseif ($userLevel >= 40 && $userLevel < 45) {
            $get_money = 80;
        }elseif ($userLevel >= 45 && $userLevel < 50) {
            $get_money = 90;
        }elseif ($userLevel >= 50 && $userLevel < 55) {
            $get_money = 100;
        }elseif ($userLevel >= 55 && $userLevel < 60) {
            $get_money = 110;
        }elseif ($userLevel >= 60 && $userLevel < 65) {
            $get_money = 120;
        }elseif ($userLevel >= 65 && $userLevel < 70) {
            $get_money = 130;
        }elseif ($userLevel >= 70 && $userLevel < 75) {
            $get_money = 140;
        }elseif ($userLevel >= 75 && $userLevel < 80) {
            $get_money = 150;
        }elseif ($userLevel >= 80 && $userLevel < 85) {
            $get_money = 160;
        }elseif ($userLevel >= 85 && $userLevel < 90) {
            $get_money = 170;
        }elseif ($userLevel >= 90 && $userLevel < 95) {
            $get_money = 180;
        }elseif ($userLevel >= 95 && $userLevel < 100) {
            $get_money = 190;
        }else{
            $get_money = 200;
        }
        $user_find = M("getcoin")->where(['uid'=>$uid,'create_day'=>$now_day])->find();
        if($user_find){
            $leave_time = $user_find['update_time']+300-time();
            if($leave_time < 0){
                $leave_time = 0;
            }
            if($user_find['get_number'] >= 5){
                $message = array();
                $message['code']    = 2;
                $message['message'] = 'defult';
                $message['data']    = array('leave_time'=>$leave_time,'get_money'=>$get_money);
                echo json_encode($message);
                exit;
            }else{
                $message = array();
                $message['code']    = 0;
                $message['message'] = 'success';
                $message['data']    = array('leave_time'=>$leave_time,'get_money'=>$get_money);
                echo json_encode($message);
                exit;
            }
        }else{
            $message = array();
            $message['code']    = 0;
            $message['message'] = 'success';
            $message['data']    = array('leave_time'=>0,'get_money'=>$get_money); 
            echo json_encode($message);
            exit;
        }
    }
    /*

     * 签到
     *      */
    public function sign($uid){
        $userfind = M('Signreward')->where('uid='.$uid)->find();
        $last_time = date('Y-m-d',$userfind['create_time']);
        $last_day = date_create_from_format('Y-m-d H:i:s',$last_time.' 00:00:00')->getTimestamp();
        foreach (self::$sign_money as $key=>$value){
            $money[] = $value;
        }
        if($last_day+172800 > time() && $last_day+86400 < time()){
            if($userfind['count_day'] == 7){
                $sign_today = 1;
            }else{
                $sign_today = $userfind['count_day']+1;
            }
            if($userfind){
                foreach (self::$sign_money as $key=>$value){
                    $money[] = $value;
                }
                echo json_encode(['code'=>'0','message'=>'success','data'=>['sign_today'=>(string)$sign_today,'sign_money'=>$money]]);
            }else{
                $sign_day = array(
                    'uid'   => $uid,
                    'day_1' => 0,
                    'day_2' => 0,
                    'day_3' => 0,
                    'day_4' => 0,
                    'day_5' => 0,
                    'day_6' => 0,
                    'day_7' => 0,
                    'create_time' => 0,
                    'count_day'   => 0,
                );
                $useradd = M('Signreward')->data($sign_day)->add();
                
                echo json_encode(['code'=>'0','message'=>'success','data'=>['sign_today'=>'1','sign_money'=> $money]]);
            }
        }elseif($last_day+86400 >= time()){
            echo json_encode(['code'=>'1','message'=>'没有到签到时间']);
            exit;
        }else{
            echo json_encode(['code'=>'0','message'=>'success','data'=>['sign_today'=>'1','sign_money'=> $money]]);
            exit;
        }
    }
    /*

     * 签到奖励领取
     *      */
    public function sign_reward($uid){
        $userfind = M('Signreward')->where('uid='.$uid)->find();
        if(!$userfind){
            $sign_day = array(
                'uid'   => $uid,
                'day_1' => self::$sign_money['firstDay'],
                'day_2' => 0,
                'day_3' => 0,
                'day_4' => 0,
                'day_5' => 0,
                'day_6' => 0,
                'day_7' => 0,
                'create_time' => time(),
                'count_day'   => 1,
            );
            $useradd = M('Signreward')->data($sign_day)->add();
            $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
            $useradd = M('Member')->where('id='.$uid)->save($coin_data);
            echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
        }else{
            $last_time = date('Y-m-d',$userfind['create_time']);
            $last_day = date_create_from_format('Y-m-d H:i:s',$last_time.' 00:00:00')->getTimestamp();
            if($userfind['count_day'] == 1){
                if($last_day+172800 > time() && $last_day+86400 < time()){
                    $sign_day['count_day']   = 2;
                    $sign_day['day_2']       = self::$sign_money['secondDay'];
                    $sign_day['create_time'] = time();
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['secondDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['secondDay']]);
                    exit;
                }elseif($last_day+86400 >= time()){
                    echo json_encode(['code'=>'1','message'=>'没有到签到时间']);
                    exit;
                }else{
                    $sign_day = array('day_1'=>self::$sign_money['firstDay'],'day_2'=>0,'day_3'=>0,'day_4'=>0,'day_5'=>0,'day_6'=>0,'day_7'=>0,'create_time'=>time(),'count_day'=>1);
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
                    exit;
                }
            }
            if($userfind['count_day'] == 2){
                if($last_day+172800 > time() && $last_day+86400 < time()){
                    $sign_day['count_day']   = 3;
                    $sign_day['day_3']       = self::$sign_money['thirdDay'];
                    $sign_day['create_time'] = time();
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['thirdDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['thirdDay']]);
                    exit;
                }elseif($last_day+86400 >= time()){
                    echo json_encode(['code'=>'1','message'=>'没有到签到时间']);
                    exit;
                }else{
                    $sign_day = array('day_1'=>self::$sign_money['firstDay'],'day_2'=>0,'day_3'=>0,'day_4'=>0,'day_5'=>0,'day_6'=>0,'day_7'=>0,'create_time'=>time(),'count_day'=>1);
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
                    exit;
                }
            }
            if($userfind['count_day'] == 3){
                if($last_day+172800 > time() && $last_day+86400 < time()){
                    $sign_day['count_day']   = 4;
                    $sign_day['day_4']       = self::$sign_money['fourthDay'];
                    $sign_day['create_time'] = time();
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['fourthDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['fourthDay']]);
                    exit;
                }elseif($last_day+86400 >= time()){
                    echo json_encode(['code'=>'1','message'=>'没有到签到时间']);
                    exit;
                }else{
                    $sign_day = array('day_1'=>self::$sign_money['firstDay'],'day_2'=>0,'day_3'=>0,'day_4'=>0,'day_5'=>0,'day_6'=>0,'day_7'=>0,'create_time'=>time(),'count_day'=>1);
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
                    exit;
                }
            }
            if($userfind['count_day'] == 4){
                if($last_day+172800 > time() && $last_day+86400 < time()){
                    $sign_day['count_day']   = 5;
                    $sign_day['day_5']       = self::$sign_money['fifthDay'];
                    $sign_day['create_time'] = time();
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['fifthDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['fifthDay']]);
                    exit;
                }elseif($last_day+86400 >= time()){
                    echo json_encode(['code'=>'1','message'=>'没有到签到时间']);
                    exit;
                }else{
                    $sign_day = array('day_1'=>self::$sign_money['firstDay'],'day_2'=>0,'day_3'=>0,'day_4'=>0,'day_5'=>0,'day_6'=>0,'day_7'=>0,'create_time'=>time(),'count_day'=>1);
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
                    exit;
                }
            }
            if($userfind['count_day'] == 5){
                if($last_day+172800 > time() && $last_day+86400 < time()){
                    $sign_day['count_day']   = 6;
                    $sign_day['day_6']       = self::$sign_money['sixthDay'];
                    $sign_day['create_time'] = time();
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['sixthDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['sixthDay']]);
                    exit;
                }elseif($last_day+86400 >= time()){
                    echo json_encode(['code'=>'1','message'=>'没有到签到时间']);
                    exit;
                }else{
                    $sign_day = array('day_1'=>self::$sign_money['firstDay'],'day_2'=>0,'day_3'=>0,'day_4'=>0,'day_5'=>0,'day_6'=>0,'day_7'=>0,'create_time'=>time(),'count_day'=>1);
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
                    exit;
                }
            }
            if($userfind['count_day'] == 6){
                if($last_day+172800 > time() && $last_day+86400 < time()){
                    $money = rand(50,100);
                    $sign_day['count_day']   = 7;
                    $sign_day['day_7']       = $money;
                    $sign_day['create_time'] = time();
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.$money);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>$money]);
                    exit;
                }elseif($last_day+86400 >= time()){
                    echo json_encode(['code'=>'1','message'=>'没有到签到时间']);
                    exit;
                }else{
                    $sign_day = array('day_1'=>self::$sign_money['firstDay'],'day_2'=>0,'day_3'=>0,'day_4'=>0,'day_5'=>0,'day_6'=>0,'day_7'=>0,'create_time'=>time(),'count_day'=>1);
                    $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
                    //向用户表添加余额
                    $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
                    $useradd = M('Member')->where('id='.$uid)->save($coin_data);
                    echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
                    exit;
                }
            }
            //重置
            $sign_day = array(
                'day_1' => self::$sign_money['firstDay'],
                'day_2' => 0,
                'day_3' => 0,
                'day_4' => 0,
                'day_5' => 0,
                'day_6' => 0,
                'day_7' => 0,
                'create_time' => time(),
                'count_day'   => 1,
            );
            $userupdate = M('Signreward')->where('uid='.$uid)->save($sign_day);
            //向用户表添加余额
            $coin_data['coinbalance'] = array('exp', 'coinbalance+'.self::$sign_money['firstDay']);
            $useradd = M('Member')->where('id='.$uid)->save($coin_data);
            echo json_encode(['code'=>'0','message'=>'签到成功','data'=>self::$sign_money['firstDay']]);
            exit;
        }
    }
    //房间内向房间外的列表发送改变游戏的值
    public function changeGameType($gameType,$roomid){
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $game_mem = new \Memcached();
	$game_mem->addServer($ip, $port);
        if($game_mem->set($roomid.'gameType',$gameType)){
            $message = array();
            $message['code']    = 0;
            $message['message'] = 'success！';
            echo json_encode($message);
            exit;
        }else{
            $message = array();
            $message['code']    = 1;
            $message['message'] = 'error！';
            echo json_encode($message);
            exit;
        }
    }
    public function getGameType($roomid){
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $game_mem = new \Memcached();
        $game_mem->addServer($ip, $port);
        $gameType = $game_mem->get($roomid.'gameType');
        if($gameType == 0){
            $message = array(
            'code'=>'0',
            'message'=>'no game',
            'gameType'=>$gameType,
        );
        echo json_encode($message);
        exit;
        }
        $message = array(
            'code'=>'0',
            'message'=>'success',
            'gameType'=>$gameType,
        );
        echo json_encode($message);
        exit;
    }
    
    //魅力排行日榜
    public function charmListDay($uid){
        $callback = $_REQUEST['callback'];
        //拼接时间
        $day = date('Y-m-j',time());
        $start_day = $day." 00:00:00";
        $now_start_time = date_create_from_format('Y-m-d H:i:s',$start_day)->getTimestamp();
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $mem = new \Memcache;
        $mem->addServer($ip, $port);
        if(!$mem->get('charmListDay')){
            $uids = M('Coindetail')->distinct(true)->field('touid')->select();
            foreach ($uids as $key => $value){
                $data = array();
                $data['addtime'] = array(['egt',$now_start_time],['lt',$now_start_time+86400]);
                $data['touid'] = $value['touid'];
                $user_money[] = M("Coindetail")->where($data)->field("sum(`coin`) as `coin`,touid")->find();
            }
            //二维数组按照降序排序
            foreach ($user_money as $key=>$value){
                $score[$key] = $value['coin'];
            }
            array_multisort($score,SORT_NUMERIC,SORT_DESC,$user_money);
            //去掉<=0的用户
            foreach ($user_money as $k=>$value){
                if($value['touid'] == NULL || $value['coin'] <= 0){
                    unset($user_money[$k]);
                }
            }
            //去除50后面的数组
            array_splice($user_money, 50);
            //数组内添加昵称
            $num = 0;
            foreach ($user_money as $k=>$value){
                $usernickname = M("Member")->where(['id'=>$value['touid']])->field('nickname,birthday,city,sid,intro,spendcoin')->find();
                $is_attention = M('Attention')->where(['uid'=>$uid,'attuid'=>$value['touid']])->find();
                if($is_attention){
                    $user_money[$num]['is_attention'] = '1';
                }else{
                    $user_money[$num]['is_attention'] = '0';
                }
                $count = M("Attention")->where("attuid=" . $value['touid'])->count();
                $user_money[$num]['fans'] = $count;
                $level = getRichlevel($usernickname['spendcoin']);
                $user_money[$num]['level'] = $level[0]['levelid'];
                $user_money[$num]['nickname'] = $usernickname['nickname'];
                $user_money[$num]['birthday'] = date("Y-m-d",$usernickname['birthday']);
                $user_money[$num]['city'] = $usernickname['city'];
                $user_money[$num]['sid'] = $usernickname['sid'];
                $user_money[$num]['intro'] = $usernickname['intro'];
                $user_money[$num]['avatar'] = '/style/avatar/'.substr(md5($value['touid']),0, 3).'/'.$value['touid'].'_middle.jpg';
                $num++;
            }
            $mem->set('charmListDay',$user_money,0, 3600);
        }else{
            $user_money = $mem->get('charmListDay');
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'charm list','data'=>$user_money]).')';
    }
    //魅力排行总榜
    public function charmListTotal($uid){
        $callback = $_REQUEST['callback'];
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $mem = new \Memcache;
        $mem->addServer($ip, $port);
        if(!$mem->get('charmListTotal')){
            $uids = M('Coindetail')->distinct(true)->field('touid')->select();
            foreach ($uids as $key => $value){
                $data = array();
                $data['touid'] = $value['touid'];
                $user_money[] = M("Coindetail")->where($data)->field("sum(`coin`) as `coin`,touid")->find();
            }
            //二维数组按照降序排序
            foreach ($user_money as $key=>$value){
                $score[$key] = $value['coin'];
            }
            array_multisort($score,SORT_NUMERIC,SORT_DESC,$user_money);
            //去掉<=0的用户
            foreach ($user_money as $k=>$value){
                if($value['touid'] == NULL || $value['coin'] <= 0){
                    unset($user_money[$k]);
                }
            }
            //去除50后面的数组
            array_splice($user_money, 50);
            //数组内添加昵称
            $num = 0;
            foreach ($user_money as $k=>$value){
                $usernickname = M("Member")->where(['id'=>$value['touid']])->field('nickname,birthday,city,sid,intro,spendcoin')->find();
                $count = M("Attention")->where("attuid=" . $value['touid'])->count();
                $user_money[$num]['fans'] = $count;
                $level = getRichlevel($usernickname['spendcoin']);
                $user_money[$num]['level'] = $level[0]['levelid'];
                $is_attention = M('Attention')->where(['uid'=>$uid,'attuid'=>$value['touid']])->find();
                if($is_attention){
                    $user_money[$num]['is_attention'] = '1';
                }else{
                    $user_money[$num]['is_attention'] = '0';
                }
                $user_money[$num]['nickname'] = $usernickname['nickname'];
                $user_money[$num]['birthday'] = date("Y-m-d",$usernickname['birthday']);
                $user_money[$num]['city'] = $usernickname['city'];
                $user_money[$num]['sid'] = $usernickname['sid'];
                $user_money[$num]['intro'] = $usernickname['intro'];
                $user_money[$num]['avatar'] = '/style/avatar/'.substr(md5($value['touid']),0, 3).'/'.$value['touid'].'_middle.jpg';
                $num++;
            }
            $mem->set('charmListTotal',$user_money,0, 86400);
        }else{
            $user_money = $mem->get('charmListTotal');
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'charm list','data'=>$user_money]).')';
    }
    //打赏排行日榜
    public function dedicateListDay($uid){
        $callback = $_REQUEST['callback'];
        //拼接时间
        $day = date('Y-m-j',time());
        $start_day = $day." 00:00:00";
        $now_start_time = date_create_from_format('Y-m-d H:i:s',$start_day)->getTimestamp();
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $mem = new \Memcache;
        $mem->addServer($ip, $port);
        if(!$mem->get('dedicateListDay')){
            $uids = M('Coindetail')->distinct(true)->field('uid')->select();
            foreach ($uids as $key => $value){
                $data = array();
                $data['addtime'] = array(['egt',$now_start_time],['lt',$now_start_time+86400]);
                $data['uid'] = $value['uid'];
                $user_money[] = M("Coindetail")->where($data)->field("sum(`coin`) as `coin`,uid")->find();
            }
            //二维数组按照降序排序
            foreach ($user_money as $key=>$value){
                $score[$key] = $value['coin'];
            }
            array_multisort($score,SORT_NUMERIC,SORT_DESC,$user_money);
            //去掉<=0的用户
            foreach ($user_money as $k=>$value){
                if($value['uid'] == NULL || $value['coin'] <= 0){
                    unset($user_money[$k]);
                }
            }
            //去除50后面的数组
            array_splice($user_money, 50);
            //数组内添加昵称
            $num = 0;
            foreach ($user_money as $k=>$value){
                $usernickname = M("Member")->where(['id'=>$value['uid']])->field('nickname,birthday,city,sid,intro,spendcoin')->find();
                $level = getRichlevel($usernickname['spendcoin']);
                $count = M("Attention")->where("attuid=" . $value['uid'])->count();
                $user_money[$num]['fans'] = $count;
                $is_attention = M('Attention')->where(['uid'=>$uid,'attuid'=>$value['uid']])->find();
                if($is_attention){
                    $user_money[$num]['is_attention'] = '1';
                }else{
                    $user_money[$num]['is_attention'] = '0';
                }
                $user_money[$num]['level'] = $level[0]['levelid'];
                $user_money[$num]['nickname'] = $usernickname['nickname'];
                $user_money[$num]['birthday'] = date("Y-m-d",$usernickname['birthday']);
                $user_money[$num]['city'] = $usernickname['city'];
                $user_money[$num]['sid'] = $usernickname['sid'];
                $user_money[$num]['intro'] = $usernickname['intro'];
                $user_money[$num]['avatar'] = '/style/avatar/'.substr(md5($value['uid']),0, 3).'/'.$value['uid'].'_middle.jpg';
                $num++;
            }
            $mem->set('dedicateListDay',$user_money,0, 3600);
        }else{
            $user_money = $mem->get('dedicateListDay');
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'charm list','data'=>$user_money]).')';
    }
    //打赏排行总榜
    public function dedicateListTotal(){
        $callback = $_REQUEST['callback'];
        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $mem = new \Memcache;
        $mem->addServer($ip, $port);
        if(!$mem->get('charmListTotal')){
            $uids = M('Coindetail')->distinct(true)->field('uid')->select();
            foreach ($uids as $key => $value){
                $data = array();
                $data['uid'] = $value['uid'];
                $user_money[] = M("Coindetail")->where($data)->field("sum(`coin`) as `coin`,uid")->find();
            }
            //二维数组按照降序排序
            foreach ($user_money as $key=>$value){
                $score[$key] = $value['coin'];
            }
            array_multisort($score,SORT_NUMERIC,SORT_DESC,$user_money);
            //去掉<=0的用户
            foreach ($user_money as $k=>$value){
                if($value['uid'] == NULL || $value['coin'] <= 0){
                    unset($user_money[$k]);
                }
            }
            //去除50后面的数组
            array_splice($user_money, 50);
            //数组内添加昵称
            $num = 0;
            foreach ($user_money as $k=>$value){
                $usernickname = M("Member")->where(['id'=>$value['uid']])->field('nickname,birthday,city,sid,intro,spendcoin')->find();
                $level = getRichlevel($usernickname['spendcoin']);
                $count = M("Attention")->where("attuid=" . $value['uid'])->count();
                $user_money[$num]['fans'] = $count;
                $user_money[$num]['level'] = $level[0]['levelid'];
                $user_money[$num]['nickname'] = $usernickname['nickname'];
                $user_money[$num]['birthday'] = date("Y-m-d",$usernickname['birthday']);
                $user_money[$num]['city'] = $usernickname['city'];
                $user_money[$num]['sid'] = $usernickname['sid'];
                $user_money[$num]['intro'] = $usernickname['intro'];
                $user_money[$num]['avatar'] = '/style/avatar/'.substr(md5($value['uid']),0, 3).'/'.$value['uid'].'_middle.jpg';
                $num++;
            }
            $mem->set('charmListTotal',$user_money,0, 86400);
        }else{
            $user_money = $mem->get('charmListTotal');
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'charm list','data'=>$user_money]).')';
    }
    //游戏排行日榜
    public function game_rank(){
        $callback = $_REQUEST['callback'];
        $mem_config = C('MEM_CACHE');
       list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $mem = new \Memcache;
        $mem->addServer($ip, $port);
        if(!$mem->get('game_rank')){
            $game_url = "http://gapi.meilibo.net/Home/Api/game_rank";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $game_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            
            $user_money = json_decode($output,true);
            $num = 0;
            foreach ($user_money as $k=>$value){
                $usernickname = M("Member")->where(['id'=>$value['uid']])->field('birthday,city,sid,intro,spendcoin')->find();
                $level = getRichlevel($usernickname['spendcoin']);
                $count = M("Attention")->where("attuid=" . $value['uid'])->count();
                $user_money[$num]['fans'] = $count;
                $is_attention = M('Attention')->where(['uid'=>$uid,'attuid'=>$value['uid']])->find();
                if($is_attention){
                    $user_money[$num]['is_attention'] = '1';
                }else{
                    $user_money[$num]['is_attention'] = '0';
                }
                $user_money[$num]['level'] = $level[0]['levelid'];
                $user_money[$num]['birthday'] = date("Y-m-d",$usernickname['birthday']);
                $user_money[$num]['city'] = $usernickname['city'];
                $user_money[$num]['sid'] = $usernickname['sid'];
                $user_money[$num]['intro'] = $usernickname['intro'];
                $user_money[$num]['avatar'] = '/style/avatar/'.substr(md5($value['uid']),0, 3).'/'.$value['uid'].'_middle.jpg';
                $num++;
            }
            $mem->set('game_rank',$user_money,0, 3600);
        }else{
            $user_money = $mem->get('game_rank');
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'charm list','data'=>$user_money]).')';
    }
    //获取用户信息
    public function getuserinfo($uid){
        $user = M("Member")->where(['id'=>$uid])->field('nickname,birthday,city,sid,intro,spendcoin')->find();
        $level = getRichlevel($user['spendcoin']);
        $count = M("Attention")->where("attuid=" . $uid)->count();
        $user['fans'] = $count;
        $user['level'] = $level[0]['levelid'];
        echo json_encode($user);
    }
    //添加用户金额
    public function addusercoin($coin,$uid,$token){
        $data = array();
        $data['coinbalance'] = array('exp', "coinbalance+".$coin);
        $coinadd = M("Member")->where(['id'=>$uid])->save($data);
        if($coinadd){
            echo json_encode(['code'=>'0']);
        }else{
            echo json_encode(['code'=>'1']);
        }
    }
    //公共礼物列表
    public function publicGiftList($uid,$token){
        $callback = $_REQUEST['callback'];
        $num = 0;
        $user_gifts = M('coindetail')->where(['touid'=>$uid,'gtype'=>'1'])->order('id desc')->limit(100)->select();
        foreach ($user_gifts as $key=>$value){
            $give_user = M('Member')->where(['id'=>$value['uid']])->find();
            $gift = M('Gift')->where(['id'=>$value['giftid']])->find();
            $list[$num]['give_nickname'] = $give_user['nickname'];
            $list[$num]['gift_name'] = $gift['giftname'];
            $list[$num]['avatar'] = '/style/avatar/'.substr(md5($give_user['id']),0, 3).'/'.$give_user['id'].'_middle.jpg';
            $list[$num]['time'] = date('Y年m月d日 H:i',$value['addtime']);
            $list[$num]['gift_count'] = $value['giftcount'];
            $list[$num]['gift_coin'] = $gift['needcoin'];
            $list[$num]['gift_icon'] = $gift['gifticon'];
            $num++;
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'普通礼物列表','data'=>$list]).')';
    }
    //特殊礼物列表
    public function specialGiftList($uid,$token){
        $callback = $_REQUEST['callback'];
        $num = 0;
        $user_gifts = M('coindetail')->where(['touid'=>$uid,'gtype'=>'4'])->order('id desc')->limit(100)->select();
        foreach ($user_gifts as $key=>$value){
            $give_user = M('Member')->where(['id'=>$value['uid']])->find();
            $gift = M('Gift')->where(['id'=>$value['giftid']])->find();
            $list[$num]['give_nickname'] = $give_user['nickname'];
            $list[$num]['gift_name'] = $gift['giftname'];
            $list[$num]['avatar'] = '/style/avatar/'.substr(md5($give_user['id']),0, 3).'/'.$give_user['id'].'_middle.jpg';
            $list[$num]['time'] = date('Y年m月d日 H:i',$value['addtime']);
            $list[$num]['gift_count'] = $value['giftcount'];
            $list[$num]['gift_coin'] = $gift['needcoin'];
            $list[$num]['gift_icon'] = $gift['gifticon'];
            $num++;
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'普通礼物列表','data'=>$list]).')';
    }
    //提现记录列表
    public function takeMoneyList($uid,$token){
        $callback = $_REQUEST['callback'];
        $user_money = M('Earncash')->where(['uid'=>$uid])->order('id desc')->field('id,uid,cash,status,checktime,type')->select();
        $num = 0;
        foreach ($user_money as $key => $value){
            $user_money[$num]['checktime'] = date('Y-m-d H:i:s',$value['checktime']);
        }
        echo $callback.'('.json_encode(['code'=>0,'message'=>'提现列表','data'=>$user_money]).')';
    }
}
