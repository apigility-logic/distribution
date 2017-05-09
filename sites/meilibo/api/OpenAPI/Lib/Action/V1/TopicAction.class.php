<?php
class TopicAction extends BaseAction{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * undocumented function summary
     *
     * 创建话题
     *
     * @param string string 标题字段
     * @param uid int 用户id
     * @param roomid bigint 房间号
     * @param ptid int 私密类型 => 对应privatetype表id
     **/
    public function createTopic($string = NULL,$uid = 0,$roomid = 0, $ptid = 0, $prerequisite = NULL, $third_party_id = 0, $channel_id = 0, $channel_name = null, $token =null, $ischarge = '0', $cost = 0)
    {

        if($uid == 0 || $roomid == 0){
            return false;
        }
        //0,1,2
        //0代表已经关闭,1代表已经正在直播且是最后一条记录,2代表批量关闭，但不清楚是否正在直播的最后一条
        //我首先存进去的应该是，是最后一条，但不清楚是否真的在直播
        //在脚本跑起的时候，脚本是检测七牛上该流是否存在，不存在的话先改成2，代表不清楚应该
        $time = time();
        $status = 0;
        if($string == NULL){
            //存回播
            M()->execute("update ss_backstream set streamstatus = '0' where roomid = ".$roomid);
            $streamData['uid'] = $uid;
            $streamData['starttime'] = $time;
            $streamData['roomid'] = $roomid;
            $streamData['streamstatus'] = '1';            
            $streamData['ischarge'] = $ischarge;
            $streamData['cost'] = $cost;
            $status = M("backstream")->data($streamData)->add();
        }else{
            $arr = array();
            $string = htmlspecialchars_decode($string);  //解码
            preg_match_all('/\#([^\#]+?)\#/',$string,$arr); // 依照正则表达式替换出 #???# 格式的话��
            foreach($arr[0] as $temparr){
                $string = str_replace($temparr,"",$string);
            }
            if(strlen($string) < 1){
                $string = " ";
            }
            $topicList = $arr[1]; // 得到已剔��号的话题名称
            $topicList = array_unique($topicList);


            //拼装查询现有话题的名称
            $key = "";
            foreach($topicList as $topic){
                $key .= $topic.",";

            }
            $key = substr($key,0,-1);
            $haveListdata['title'] = array("in",$key);
            //查询数据库中已有的话题，并删除掉数组中的这个话题
            $haveList = M("topic")->where($haveListdata)->select();
            if($haveList != NULL){
                $ids = "";
                foreach($haveList as $have){
                    if(in_array($have['title'],$topicList)){
                        $ids .= $have['id'] .",";
                        $delKey = array_search($have['title'],$topicList);
                        unset($topicList[$delKey]);
                    }
                }
            }
            //将新建的话题添加进数据库 add new topic to database
            if(count($topicList) > 0){
                foreach($topicList as $temp){
                    $data['title'] = $temp;
                    $data['uid'] = $uid;
                    $data['createtime'] = $time;
                    $id = M("topic")->data($data)->add();
                    $ids .= $id . ",";
                }
            }
            //这里是添加回放链接
            $ids = substr($ids,0,-1);
            M()->execute("update ss_backstream set streamstatus = '0' where roomid = ".$roomid);
            // M("backstream")->where(array("roomid",$roomid))->save(array('streamstatus','0'));
            $tempstreamData['title'] = $string;
            $tempstreamData['topics'] = $ids;
            $tempstreamData['uid'] = $uid;
            $tempstreamData['roomid'] = $roomid;
            $tempstreamData['starttime'] = $time;
            $tempstreamData['streamstatus'] = '1';
            $tempstreamData['ischarge'] = $ischarge;
            $tempstreamData['cost'] = $cost;
            $status = M("backstream")->data($tempstreamData)->add();
        }
        if ( $third_party_id != 0 ) {
            $data_third_party['third_party_id'] = intval($third_party_id);
            M( 'member' )->where( "id = " . $uid )->save($data_third_party);
        }
        $data_channel['channel_id'] = intval($channel_id);
        $data_channel['channel_name'] = $channel_name;
        M( 'member' )->where( "id = " . $uid )->save($data_channel);
        if($ptid != 0 && !empty($prerequisite)){
            if(count(M('privatetype')->where('id = '.$ptid)->select()) > 0){
                $bsid = M('backstream')->where('streamstatus = "1" and uid = '.$uid)->getField('id');
                if($bsid > 0){
                   $data = array(
                       'bsid' => $bsid,
                       'ptid' => $ptid,
                       'prerequisite'  =>  $prerequisite
                   );
                   if(M('privatelimit')->data($data)->add() > 0 ){
                        $data['plid'] = M('privatelimit')->getLastInsID();
                        $data['privatemsg'] = L('_PRIVATE_SUCCESS_');
                   }else{
                       $data['privatemsg'] = L('_PRIVATE_FAILED_');
                   }
                }else{
                    $data['privatemsg'] = L('_PRIVATE_NOT_OPEN_LIVE_');
                }
            }else{
                $data['privatemsg'] = L('_PRIVATE_TYPE_IS_NOT_EXIST_');
            }

        }else{
            $data['privatemsg'] = L('_PRIVATE_NOTIS_');
        }
        if($status > 0){
            $confirm = new ConfirmAction();
            $data['callback_data'] = $confirm->CYConfirm($token = $token, $type = 'channel', $online = 1);
            $data['createroom'] = L('_OPERATION_SUCCESS_');

            //查询粉丝用户id
            $Ids = M('attention')->where('attuid='.$uid)->getField('uid', true);
            
            // //发送推送
            // $JPush = new JmessageAction();
            // $maxLength = 999;
            // $array = array_chunk($Ids, $maxLength);
            // $name = M('member')->where('id='.$uid)->getField('nickname');
            // foreach ($array as $key => $value) {
            //     $res = $JPush->messagePush( $value , $name);
            // }
            // $data['jpush'] = $res;

            return $data;

        }else{
            return L('_ADD_ERROR_');
        }
    }
    /**
     *
     * 该方法用于模糊查询所有话题列表
     *
     * @param token string
     * @param title 模糊查询
     *
     **/
    public function getTopic($title = NULL,$count = 0)
    {
        //在下面执行查询匹配的动作并返回

        if($title == NULL){
            $topic = M('topic')->select();
        }else{
            $where['title']=array('like','%'.$title.'%');
            $where['promote'] = "1";
            $where['_logic'] = "or";
            $topic = M('topic')->where($where)->select();
        }
        $backstream = M('backstream')->where('streamstatus = "1"')->select();
        $all_data = array();
        $topic_title = array();
        $topic_title_end = array();
        $i = 0;
        $j = 0;
        foreach ($topic as $key => $one_topic) {
            $n = 0;
            foreach ($backstream as $one_backstream) {
                $topics = explode(",", $one_backstream['topics']);
                //计算发起该话题用户数量
                if(in_array($one_topic['id'], $topics)){
                        $n++;
                    }
                //一共有几个匹配到的话题
                if(!in_array($one_topic['title'], $topic_title)){
                    if(in_array($one_topic['id'], $topics)){
                        $topic_title[++$i] = $one_topic['title'];
                    }
                }
            }
            if($one_topic['promote'] == "1"){
                $temp = array(
                    'topic_id'      =>  $one_topic['id'],
                    'topic_title'   =>  $one_topic['title'],
                    'topic_promote'   =>  $one_topic['promote'],
                    'user_num'     =>  $n
                    );
                array_unshift($topic_title_end,$temp);
                $j ++;
            }else{
                //存入数据
                if($n != 0){
                    $topic_title_end[] = array(
                        'topic_id'      =>  $one_topic['id'],
                        'topic_title'   =>  $one_topic['title'],
                        'topic_promote'   =>  $one_topic['promote'],
                        'user_num'     =>  $n
                        );
                    $j ++;
                }
            }
            if($count > 0){
                if($j == $count){
                    break;
                }
            }
        }

        $all_data = array(
            'topic' =>  $topic_title_end,
            'all_num'   =>  $j);

        //错误打印信息
        //$this->responseError('Error提示 ???');
        //成功打印信息 数组或字符串

        $this->responseSuccess($all_data);
        //$this->responseSuccess('数组或者字符串提示 ???');
        //返回的数据应该有 话题名称和参与话题的人数
    }

    /**
     * undocumented function summary
     *
     * 该方法用��
     *
     * @param int $topicId 话题ID
     **/
    public function getTopicUser($topicId)
    {
        //这个是查出所有的在线主播回放列表
        $backstream = M('backstream')->where('streamstatus = "1"')->select();
        $uids = "";
        $titles = array();
        $finalTopic = array();
        foreach($backstream as $singleStream){
            $topics = explode(",", $singleStream['topics']);
            $tempData['id'] = array("in",$singleStream['topics']);
            $topicsTitle = M("topic")->where($tempData)->field("id,title")->select();
            if(in_array($topicId, $topics)){
                $uids .= $singleStream['uid'] .",";
                $titles[$singleStream['uid']] = $singleStream['title'];
                $tempArray = array();
                foreach($topicsTitle as $topic){
                    array_push($tempArray,$topic);
                }
                $finalTopic[$singleStream['uid']] = $tempArray;
            }
        }
        $uids = substr($uids,0,-1);
        $where_uid['id'] = array('in',$uids);
        $members = M('member')->where($where_uid)->field('id,nickname,online,sid,curroomnum,avatartime')->select();
        //这个是将avatar和snap添加进数组
        $trueMember = array();
        foreach ($members as $member) {
            $member['avatar'] = $member['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatar($member['avatartime'], $member['id'], 'middle');
            $member['snap'] = $member['avatartime'] <= 0 ? '/style/images/default.gif' :getAvatar($member['avatartime'], $member['id'], 'yuan');
            $member['roomTitle'] = $titles[$member['id']];
            $member['roomTopic'] = $finalTopic[$member['id']];
            unset($member['avatartime']);
            array_push($trueMember,$member);
        }

        $this->responseSuccess($trueMember);
    }
}
?>
