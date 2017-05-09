<?php
class MessageAction extends  BaseAction
{
    public function sendMsg($token = null,$to_uid = 0,$content = null){
        if (!APP_DEBUG && !$this->isPost()) {
            $this->forbidden();
        }
        if($msg == NULL || $to_uid == 0){
            $this->responseError(L('_PARAM_ERROR_'));
        }
        if ($this->current_uid == $to_uid) {
            $this->responseError(L('_MESSAGE_GIVE_YOURSELF_'));
        }
        //判断是否被拉黑
        $hitRs = M("hitlist")->where(array(array('uid'=>$this->current_uid,"hituid"=>$to_uid),array('uid'=>$to_uid,"hituid"=>$this->current_uid),"_logic"=>"or"))->find();
        if(count($hitRs) > 0){
            $this->responseError(L('_FRIENDSHIP_BROKE_'));
        }else{
            $userInfo = M("member")->where(array('id'=>$this->current_uid))->field('id,sex,nickname, coinbalance, spendcoin,avatartime,vip')->find();
            $touserInfo = M("member")->where(array('id'=>$to_uid))->field('id,nickname')->find();
            $tmp = getRichlevel($userInfo['spendcoin']);
            $level_id = $tmp[0]['levelid'];
            $data = array(
                "type" => "SendPrvMsg",
                "from_client_name" => $userInfo['nickname'],
                "from_user_id" => $userInfo['id'],
                // "sex" => isset($userInfo['sex']) ? $userInfo['sex'] : 0,
                "vip" => (!$userInfo['vip'] ? 0 : $userInfo['vip']),
                "levelid" => $level_id,
                'avatar'=> getAvatar($userInfo['avatartime'],$userInfo['id'], 'small'),
                "to_client_name" => $touserInfo['nickname'],
                "to_user_id" => $touserInfo['id'],
                'content' => $content,
                "pub" => "1",
                "time" => date("H:i",time()),
            );
            //在这里发送个消息给他
            import('Common.Gateway', APP_PATH, '.php');
            Gateway::$registerAddress = C('REGISTER_ADDRESS');
            Gateway::sendToUid($touserInfo['id'], json_encode($data));
            $this->responseSuccess(L('_OPERATION_SUCCESS_'));
            // TODO: 修改.
        }
        $this->forbidden();
    }
    
}
