<?php
class pcAction extends BaseAction{
    public function index(){
        C('HTML_CACHE_ON', false);
        $User = D("Member");
        if (empty($_GET["roomnum"])) {
            $onlineroom = M('Member')->where(array('broadcasting'=>'y'))->order('onlinenum desc')->limit(10)->getField('curroomnum,id');
            $roominfo = array_rand($onlineroom,1);
            if(empty($roominfo)){
                $_GET["roomnum"] = '';
            }else{
                $_GET["roomnum"] = $roominfo;
            }
        }
        !empty($_GET["token"]) ? $token = $_GET["token"] :$token = '';
        !empty($_SESSION["token"]) ? $token = $_SESSION["token"] :$token = '';
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : null;
        if ($token&&$uid) {
            $this->assign("token",$token);
        }else{
            $json = $this->getApi("OpenAPI/v1/User/touristlogin",array());
            $touristinfo = json_decode($json,1);
            $touristinfo = $touristinfo['data'];
            $token = $touristinfo['token'];
            $this->assign("token",$touristinfo['token']);
        }
        $_SESSION['curroomnum'] = $_GET['roomnum'];
        $_SESSION['currenturl'] = $_SERVER['PHP_SELF'];
        $userinfo = $User->where('curroomnum='.$_GET["roomnum"].'')->select();
        if (is_null($userinfo) || empty($userinfo)) {
            $this->error('哎呀，房间号不存在～～～');
        }
        $this->assign('roomnum', $_GET["roomnum"]);
        //粉丝
        $followers_cnt = M('Attention')->where(array('attuid' => $userinfo[0]['id']))->count();
        if($followers_cnt == ''){
            $followers_cnt = 0;
        }
        
        $this->assign("followers_cnt",$followers_cnt);
        if ($userinfo) {
            $type = 'pull';
            if (isset($_SESSION['uid']) && $_SESSION['uid'] > 0 ) {
                if ($_SESSION['roomnum'] == $userinfo[0]['curroomnum']) {
                    $type = 'push';
                }
            }
            updateQiniuStream($userinfo[0]['curroomnum']);
            $param = getStreamParam($userinfo[0]['curroomnum'], $type);
            $userinfo[0]['liveAppName'] = $param['appName'];
            $userinfo[0]['liveAppStream'] = $param['stream'];
            $user_info = array(
                'id' => $userinfo[0]['id'],
                'username' => $userinfo[0]['username'],
                'nickname' => $userinfo[0]['nickname'],
                'curroomnum' => $userinfo[0]['curroomnum'],
                'liveAppName' => $userinfo[0]['liveAppName'],
                'liveAppStream' => $userinfo[0]['liveAppStream'],
                'broadcasting' => $userinfo[0]['broadcasting'],
                'liveAppStream' => $userinfo[0]['liveAppStream'],
                'beanorignal' => $userinfo[0]['beanorignal'],
                'orientation' => $userinfo[0]['orientation'],
                'broadcasting' => $userinfo[0]['broadcasting']
                );
            $this->assign('userinfo',$user_info);
        } else {
            $numinfo = D("Roomnum")->where('num='.$_GET["roomnum"].'')->select();
            if ($numinfo) {
                $userinfo = $User->find($numinfo[0]['uid']);
                redirect('/' . $userinfo['curroomnum']);
            } else {
                $this->assign('jumpUrl', __APP__);
                $this->error('该房间不存在');
            }
        }
        
        //房间排行
        $rixing = M()->query("select *,sum(c.coin) gongxian from ss_coindetail c  join ss_member m ON m.id = c.uid where c.touid = {$userinfo[0]['id']} and date_format(FROM_UNIXTIME(c.addtime),'%m-%d-%Y')=date_format(now(),'%m-%d-%Y') group by c.uid order by gongxian DESC limit 6");
        $this->assign("rixing",$rixing);
        
        $zhouxing = M()->query("select *,sum(c.coin) gongxian from ss_coindetail c  join ss_member m ON m.id = c.uid where c.touid = {$userinfo[0]['id']} and date_format(FROM_UNIXTIME(c.addtime),'%Y')=date_format(now(),'%Y') and date_format(FROM_UNIXTIME(c.addtime),'%u')=date_format(now(),'%u') group by c.uid  order by gongxian DESC limit 6");
        $this->assign("zhouxing",$zhouxing);

        $zongxing = M()->query("select *,sum(c.coin) gongxian from ss_coindetail c  join ss_member m ON m.id = c.uid   and c.touid = {$userinfo[0]['id']} group by c.uid order by gongxian DESC limit 6");
        $this->assign("zongxing",$zongxing);
        //直播间流
        $output = $this->getApi("OpenAPI/v1/Qiniu/getRtmpUrls",array('roomID' => $_GET["roomnum"]));
        $jsondata = json_decode($output);
        $this->assign("stem",$jsondata->data->ORIGIN);
        //DEMO流
        // $output = $this->getApi("OpenAPI/v1/Qiniu/getPullAddress ",array('roomID' => $_GET["roomnum"],'token'=>$token));
        // $jsondata = json_decode($output);
        // $this->assign("stem",$jsondata->data);
        if (!empty($uid)) {
            $self = $User->where('id='.$uid)->find();
            $selfinfo['id'] = $self['id'];
            $selfinfo['username'] = $self['username'];
            $selfinfo['nickname'] = $self['nickname'];
            $selfinfo['curroomnum'] = $self['curroomnum'];
            $selfinfo['broadcasting'] = $self['broadcasting'];
            $selfinfo['coinbalance'] = $self['coinbalance'];
            $selfinfo['sex'] = $self['sex'];
            $level = D('Richlevel')->where('spendcoin_up>='.$self['spendcoin'].' and spendcoin_low<='.$self['spendcoin'])->field('levelid,levelname')->order('levelid asc')->select();
            $selfinfo['level'] = $level[0]['levelid'];
        }else{
            $selfinfo = $touristinfo;
        }
        $this->assign('selfinfo', $selfinfo);
        //关注
        if (!empty($uid)) {
            $attuid =  $userinfo[0]['id'];
            $data = M("attention")->where("uid = ".$uid." and attuid = ".$attuid)->find();
        } else {
            $data = null;
        }

        if ($data == null) {
            $attr_state = 0;
        } else { 
            $attr_state = 1;
        }

        $this->assign('attr_state', $attr_state);

        $ip = get_client_ip();
        import('ORG.Net.IpLocation');
        $ipclass = new IpLocation('UTFWry.dat');
        $area = $ipclass->getlocation($ip);
        $address = mb_substr($area['country'], 0, 6);
        $this->assign('address', $address);
        $this->display('Show:indexpc');
    }
}
