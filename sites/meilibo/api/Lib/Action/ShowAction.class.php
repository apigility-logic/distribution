<?php
class ShowAction extends BaseAction
{
    public function index()
    {
        C('HTML_CACHE_ON', false);
        $User = D("Member");
        //if (empty($_GET["roomnum"])) {
            //$onlineroom = M('Member')->where(array('broadcasting'=>'y'))->order('onlinenum desc')->limit(10)->getField('curroomnum,id');
            //$roominfo = array_rand($onlineroom,1);
            //if(empty($roominfo)){
             //   $_GET["roomnum"] = '';
           // }else{
            //    $_GET["roomnum"] = $roominfo;
           // }
       // }
	if(empty($_GET["roomnum"])){
    		$_GET['roomnum'] = cookie('curroomnum');
        }
        !empty($_GET["token"]) ? $token = $_GET["token"] :$token = '';
        !empty($_SESSION["token"]) ? $token = $_SESSION["token"] :$token = '';
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : null;
        if ($token&&$uid) {
            $this->assign("token",$token);
        }else{
            $touristinfo=array();
            $token = '';
            $this->assign("token","");
        }
         cookie('curroomnum',$_GET['roomnum']);
        //cookie('currenturl',C('WEB_URL')."/Show/index?roomnum=".cookie('curroomnum'));
    	$currenturl = cookie('curroomnum');
    	$this->assign('currenturl',$currenturl);

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
                // $type = $userinfo[0]['id'] != $_SESSION['uid'] ? 'pull' : ($userinfo[0]['broadcasting'] != 'y' || $_SESSION['roomnum'] != $_GET['roomnum']) ? 'push' : 'pull';
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
        if ($_GET["roomnum"] =='1333979551') {
            $output = $this->getApi("OpenAPI/v1/Qiniu/getPullAddress",array('roomID' => $_GET["roomnum"],'device'=>'Redmi'));
            $output =json_decode($output,true);
            $this->assign("stem",$output['data']);
        }else{
            $output = $this->getApi("OpenAPI/v1/Qiniu/getRtmpUrls",array('roomID' => $_GET["roomnum"]));
            $jsondata = json_decode($output);
            $this->assign("stem",$jsondata->data->ORIGIN);  
        }

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
    public function anchorShow(){
        if(!empty($_SESSION['uid'])&&!empty($_SESSION['token'])){
            $userInfo = M('Member')->where('id = '.$_SESSION['uid'])->find();
            $userInfo['token'] = $_SESSION['token'];
            $this->assign('userInfo',$userInfo);

            $site = M('siteconfig')->where('id = 1')->find();
            if( $userInfo['roomstatus'] == 3 ){
                 $limit = '您已被禁播，无法获取相关地址';
            }
            if($site['canlive'] == "n" && $userInfo['approveid'] == "无"){
                 $limit = '完成实名认证后获取相关地址';
            }
            if($site['sign_verification'] == "1" && $userInfo['sign'] == "n"){
                $limit = '完成签约后获取相关地址';
            }
            
            //获取推流地址
            if(!isset($limit)){
                $config = M('siteconfig')->where('id = 1')->find();
                $domain = !empty($config['anchorurl']) ? $config['anchorurl'] : 'http://demo.meilibo.net';
                $url = $domain . '/OpenAPI/v1/Qiniu/getPushAddress';
                $data = array('roomID'=>$userInfo['curroomnum'],'token'=>$_SESSION['token']);
                $info = curlRequest($url,true,$data);
                $info = json_decode($info,1);
                
                $live['host'] = $info['data']['hosts']['publish']['rtmp'].'/'.$info['data']['hub'].'/';
                $live['seceret'] = $info['data']['title'].'?key='.$info['data']['publishKey'];
                $this->assign('live',$live);
            }else{
                $this->assign('limit',$limit);
            }

        }else{//这里暂时用ID 825 游客信息  防止前端报错
            //$userInfo = M('Member')->where('id = 825')->find();
            $this->assign('userInfo','');
        }
        $this->display('Show:anchorshow');
    }

    public function show_headerInfo()
    {
        C('HTML_CACHE_ON', false);
        if (isset($_COOKIE['autoLogin']) && $_COOKIE['autoLogin'] == '1') {
            session('uid', $_COOKIE['userid']);
            //session('ucuid', $_COOKIE['ucuid']);
            session('username', $_COOKIE['username']);
            session('nickname', $_COOKIE["nickname"]);
            session('roomnum', $_COOKIE["roomnum"]);
        }
        if (isset($_SESSION['uid']) && $_SESSION['uid'] > 0){
            $userinfo = D("Member")->find($_SESSION['uid']);
            $this->assign('userinfo', $userinfo);
        }
        $this->display();
    }

    public function show_headerInfo2()
    {
        C('HTML_CACHE_ON', false);
        
        if (isset($_COOKIE['autoLogin']) && $_COOKIE['autoLogin'] == '1') {
            session('uid', $_COOKIE['userid']);
            //session('ucuid', $_COOKIE['ucuid']);
            session('username', $_COOKIE['username']);
            session('nickname', $_COOKIE["nickname"]);
            session('roomnum', $_COOKIE["roomnum"]);
        }

        if (isset($_SESSION['uid']) && $_SESSION['uid'] > 0) {
            echo '<a href="/my/sign_view/" target="_self" title="成为签约主播" class="mplay-off" id="sign_view">成为签约主播<em></em></a>';
            echo '<a href="/'.$_SESSION['roomnum'].'" target="_self" title="我要直播" class="mplay-2" id="startlive">我要直播</a>';
        } else {
            echo '<a href="javascript:UAC.openUAC(0)" target="_self" title="成为签约主播" class="mplay-off" id="sign_view">成为签约主播<em></em></a>';
            echo '<a href="javascript:UAC.openUAC(0)" target="_self" title="我要直播" class="mplay-2" id="startlive">我要直播</a>';
        }
    }

    public function show_getUserBalance()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"0","value":"0"}';
            exit;
        } else {
            $userinfo = D("Member")->find($_SESSION['uid']);
            echo '{"code":"0","value":"' . $userinfo['coinbalance'].'"}';
            exit;
        }
    }

    public function show_indexLogin()
    {
        C('HTML_CACHE_ON', false);
        if ($_COOKIE['autoLogin'] == '1') {
            session('uid', $_COOKIE['userid']);
            //session('ucuid', $_COOKIE['ucuid']);
            session('username', $_COOKIE['username']);
            session('nickname', $_COOKIE["nickname"]);
            session('roomnum', $_COOKIE["roomnum"]);
        }
        $this->display();
    }

    public function show_checkopenuac()
    {
        C('HTML_CACHE_ON',false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '0';
            exit;
        } else {
            echo '1';
            exit;
        }
        
    }

    public function show_getEmergencyNotice()
    {
        C('HTML_CACHE_ON', false);
        echo '{"code":1,"info":"当月累积充值达到500RMB的用户,就可以获得一次抽奖机会！大奖最高10W秀币等你来拿！"}';
        exit;
    }

    public function show_showData()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid'])) {
            $userid = rand(1000,9999);
            $_SESSION['uid'] = -$userid;
        }
        $this->display();
    }

    public function enterspeshow()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }
        
        // 获取用户信息
        $userinfo = D("Member")->find($_SESSION['uid']);
        // 获取主播信息
        $emceeinfo = D("Member")->find($_REQUEST['eid']);
        if ($emceeinfo) {
            if ($_REQUEST['type'] == '1') {
                if ($userinfo['coinbalance'] < $emceeinfo['needmoney']) {
                    echo '{"code":"1","info":"您的余额不足"}';
                    exit;
                } else {
                    D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $emceeinfo['needmoney'] . ',coinbalance=coinbalance-' . $emceeinfo['needmoney'] . ' where id=' . $_SESSION['uid']);
                    // 记入虚拟币交易明细
                    $Coindetail = D("Coindetail");
                    $Coindetail->create();
                    $Coindetail->type = 'expend';
                    $Coindetail->action = 'enterspeshow';
                    $Coindetail->uid = $_SESSION['uid'];
                    $Coindetail->touid = $_REQUEST['eid'];
                    
                    $Coindetail->content = $userinfo['nickname'] . ' 进入了 ' . $emceeinfo['nickname'] . ' 的收费房间';
                    $Coindetail->objectIcon = '/style/images/fei.png';
                    $Coindetail->coin = $emceeinfo['needmoney'];
                    if ($emceeinfo['broadcasting'] == 'y') {
                        $Coindetail->showId = $emceeinfo['showid'];
                    }
                    $Coindetail->addtime = time();
                    $detailId = $Coindetail->add();
                    
                    //被赠送人加豆
                    $beannum = ceil($emceeinfo['needmoney'] * ($this->emceededuct / 100));
                    D("Member")->execute('update ss_member set earnbean=earnbean+' . $beannum . ',beanbalance=beanbalance+' . $beannum . ' where id=' . $_REQUEST['eid']);
                    $Beandetail = D("Beandetail");
                    $Beandetail->create();
                    $Beandetail->type = 'income';
                    $Beandetail->action = 'enterspeshow';
                    $Beandetail->uid = $_REQUEST['eid'];
                    $Beandetail->content = $userinfo['nickname'] . ' 进入了 ' . $emceeinfo['nickname'] . ' 的收费房间';
                    $Beandetail->bean = $beannum;
                    $Beandetail->addtime = time();
                    $detailId = $Beandetail->add();

                    if ($emceeinfo['agentuid'] != 0) {
                        $beannum = ceil($emceeinfo['needmoney'] * ($this->emceeagentdeduct / 100));
                        // D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$emceeinfo['agentuid']);
                        D("Member")->execute('update ss_member set beanbalance2=beanbalance2+' . $beannum . ' where id=' . $emceeinfo['agentuid']);
                        $Emceeagentbeandetail = D("Emceeagentbeandetail");
                        $Emceeagentbeandetail->create();
                        $Emceeagentbeandetail->type = 'income';
                        $Emceeagentbeandetail->action = 'enterspeshow';
                        $Emceeagentbeandetail->uid = $emceeinfo['agentuid'];
                        $Emceeagentbeandetail->content = $userinfo['nickname'] . ' 进入了 ' . $emceeinfo['nickname'] . ' 的收费房间';
                        $Emceeagentbeandetail->bean = $beannum;
                        $Emceeagentbeandetail->addtime = time();
                        $detailId = $Emceeagentbeandetail->add();
                    }
                    
                    session('enter_' . $emceeinfo['showid'], 'y');
                    echo '{"code":"0"}';
                    exit;
                }
            }
            if ($_REQUEST['type'] == '2') {
                if ($emceeinfo['roompsw'] != $_REQUEST['password']) {
                    echo '{"code":"1","info":"进入房间密码错误"}';
                    exit;
                } else {
                    session('enter_' . $emceeinfo['showid'], 'y');
                    echo '{"code":"0"}';
                    exit;
                }
            }
        } else {
            echo '{"code":"1","info":"主播信息有误"}';
            exit;
        }
    }

    public function show_infoWithgwRanking()
    {
        C('HTML_CACHE_ON', false);
        $userinfo = D("Member")->find($_REQUEST['emceeId']);
        if ($userinfo) {
            $this->assign('userinfo', $userinfo);
        }
        $gifts = D('Gift')->order('needcoin desc')->select();
        $this->assign('gifts', $gifts);
        $this->display();
    }

    public function show_getgiftList()
    {
        C('HTML_CACHE_ON', false);
        // $curfansrank = D('Coindetail')->query('SELECT uid,touid,giftcount,sum(coin) as total FROM `ss_coindetail` where type="expend" and showId='.$_GET['showID'].' group by uid order by total desc');
        $curfansrank = D('Coindetail')->query('SELECT uid,touid,giftid,giftcount FROM `ss_coindetail` where type = "expend" and giftid > 0 and giftid < 9999 and showId=' . $_GET['showID'] . '');
        foreach ($curfansrank as $n => $val) {
            $curfansrank[$n]['voo'] = D("Member")->where('id='.$val['uid'])->select();
            $curfansrank[$n]['voo2'] = D("Member")->where('id='.$val['touid'])->select();
        }
    
        echo '{"code":"0","msg":"sucess","giftList":[';
        $i = 1;
        foreach ($curfansrank as $val){
            $giftinfo = D("Gift")->find($val['giftid']);
            $smallIcon = $giftinfo['gifticon_25'];
            echo '{"giftcount":'.$val['giftcount'].',"username":"'.$val['voo'][0]['nickname'].'","giftpath":"'.$smallIcon.'","userid":'.$val['uid'].',"touserid":'.$val['touid'].',"tousername":"'.$val['voo2'][0]['nickname'].'","giftname":"'.$giftinfo['giftname'].'"}';
            if ($i != count($curfansrank)) {
                echo ',';
            }
            $i++;
        }
        echo ']}';
    }

    public function show_getRankByShow()
    {
        C('HTML_CACHE_ON', false);
        // 本场粉丝排行
        $curfansrank = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and showid='.$_GET['showId'].' group by uid order by total desc LIMIT 5');
        if (!$curfansrank) {
            $curfansrank = array();
        }
        foreach ($curfansrank as $n => $val) {
            $curfansrank[$n]['voo'] = D("Member")->where('id='.$val['uid'])->select();
        }
        
        echo '[';
        $i = 1;
        foreach ($curfansrank as $val) {
            $richlevel = getRichlevel($val['voo'][0]['spendcoin']);
            echo '{"amount":'.$val['total'].',"icon":"/style/avatar/'.substr(md5($val['voo'][0]['id']),0,3).'/'.$val['voo'][0]['id'].'_middle.jpg","emceeno":'.$val['voo'][0]['curroomnum'].',"fanlevel":'.$richlevel[0]['levelid'].',"medaltype":0,"nickname":"'.$val['voo'][0]['nickname'].'","userid":'.$val['voo'][0]['id'].'}';
			echo '{"amount":'.$val['total'].',"emceeno":'.$val['voo'][0]['curroomnum'].',"fanlevel":'.$richlevel[0]['levelid'].',"medaltype":0,"nickname":"'.$val['voo'][0]['nickname'].'","userid":'.$val['voo'][0]['id'].'}';
            if ($i != count($curfansrank)) {
                echo ',';
            }
            $i++;
        }
        echo ']';
    }

    public function show_showSongs()
    {
        C('HTML_CACHE_ON', false);
        //if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            //exit;
        //}
        $usersong = D("Usersong");
        $count = $usersong->where('uid=' . $_REQUEST['eid'])->count();
        $listRows = 10;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count, $listRows, $linkFront);
        $usersongs = $usersong->where('uid=' . $_REQUEST['eid'])->limit($p->firstRow . "," . $p->listRows)->order('createtime desc')->select();
        if (is_null($usersongs)) {
            $usersongs = array();
        }
        $this->assign('usersongs', $usersongs);
        $pagecount = ceil($count / $listRows);
        $this->assign('pagecount', $pagecount);
        $this->assign('count', $count);
        $this->display();
    }

    public function show_addSongs()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            exit;
        }

        if ($_SESSION['uid'] != $_REQUEST['eid']) {
            exit;
        }

        for ($i = 1; $i < 6; $i++) {
            if ( isset($_REQUEST['name_'.$i]) && $_REQUEST['name_'.$i] != '' && $_REQUEST['singer_'.$i] != '') {
                $Usersong=D("Usersong");
                $Usersong->create();
                $Usersong->uid = $_REQUEST['eid'];
                $Usersong->songname = urldecode($_REQUEST['name_'.$i]);
                $Usersong->singer = urldecode($_REQUEST['singer_'.$i]);
                $Usersong->createtime = time();
                $songId = $Usersong->add();
            }
        }

        $usersong = D("Usersong");
        $count = $usersong->where('uid=' . $_REQUEST['eid'])->count();
        $listRows = 10;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count, $listRows, $linkFront);
        $usersongs = $usersong->where('uid=' . $_REQUEST['eid'])->limit($p->firstRow . "," . $p->listRows)->order('createTime desc')->select();
        $pagecount = ceil($count / $listRows);

        $echostr = '{"data":{"total":';
        $echostr .= $count;
        $echostr .= ',"page":';
        $echostr .= $pagecount;
        $echostr .= ',"songs":[';
        $i = 1;
        foreach ($usersongs as $val) {
            $echostr .= '{"id":' . $val['id'] . ',"createTime":"' . date('Y/m/d', $val['createtime']) . '","singer":"' . $val['singer'] . '","songName":"' . $val['songname'] . '"}';
            if ($i != count($usersongs)) {
                $echostr .= ',';
            }
            $i++;
        }
        $echostr .= '],"cur":1';
        $echostr .= '},"code":0,"info":""}';
        
        echo $echostr;
        exit;
    }

    public function show_delSong()
    {
        C('HTML_CACHE_ON', false);
        if ($_SESSION['uid'] == $_REQUEST['eid']) {
            D("Usersong")->where('id=' . $_REQUEST["sid"])->delete();
            echo '{"code":"0"}';
            exit;
        } else {
            echo '{"code":"1"}';
            exit;
        }
    }

    public function pickSong()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }
        
        //获取用户信息
        $userinfo = D("Member")->find($_SESSION['uid']);
        //获取主播信息
        $emceeinfo = D("Member")->find($_REQUEST['emceeId']);
        $emceelevel = getEmceelevel($emceeinfo['earnbean']);
        if ($emceelevel[0]['levelid'] > 10) {
            $needcoin = 1500;
        } else if ($emceelevel[0]['levelid'] > 5) {
            $needcoin = 1000;
        } else {
            $needcoin = 500;
        }

        if ($userinfo['coinbalance'] < $needcoin) {
            echo '{"code":"1","info":"您的余额不足"}';
            exit;
        }

        $Showlistsong = D("Showlistsong");
        $Showlistsong->create();
        $Showlistsong->uid = $_REQUEST['emceeId'];
        $Showlistsong->pickuid = $_SESSION['uid'];
        $Showlistsong->songname = urldecode($_REQUEST['songName']);
        $Showlistsong->usernick = $_SESSION['nickname'];
        $Showlistsong->status = 0;
        $Showlistsong->showstatus = '等待同意';
        $Showlistsong->addtime = time();
        $songId = $Showlistsong->add();
        
        echo '{"code":"0"}';
        exit;
    }

    public function show_listSongs()
    {
        C('HTML_CACHE_ON', false);
        $showlistsongs = D("Showlistsong")->where('uid=' . $_REQUEST['eid'])->order('addtime desc')->select();

        $echostr = '{"data":{';
        $echostr .= '"songs":[';
        $i = 1;
        $showlistsongs = !$showlistsongs ? array() : $showlistsongs;
        foreach ($showlistsongs as $val) {
            $echostr .= '{"id":' . $val['id'] . ',"createTime":"' . date('H:i', $val['addtime']) . '","songName":"' . $val['songname'] . '","userNick":"' . $val['usernick'] . '","status":' . $val['status'] . ',"showStatus":"' . $val['showstatus'] . '"}';
            if ($i != count($showlistsongs)) {
                $echostr .= ',';
            }
            $i++;
        }
        $echostr .= ']';
        $echostr .= '},"code":0,"info":""}';
        
        echo $echostr;
    }

    public function show_agreeSong()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }

        if ($_SESSION['uid'] != $_REQUEST['eid']) {
            echo '{"code":"1","info":"您没有权限"}';
            exit;
        }

        $songinfo = D("Showlistsong")->find($_REQUEST['ssid']);
        if ($songinfo) {
            // 获取点歌用户信息
            $userinfo = D("Member")->find($songinfo['pickuid']);
            // 获取主播信息
            $emceeinfo = D("Member")->find($songinfo['uid']);
            $emceelevel = getEmceelevel($emceeinfo['earnbean']);
            if ($emceelevel[0]['levelid'] > 10) {
                $needcoin = 1500;
            } else if ($emceelevel[0]['levelid'] > 5) {
                $needcoin = 1000;
            } else {
                $needcoin = 500;
            }
            if ($userinfo['coinbalance'] < $needcoin) {
                echo '{"code":"1","info":"点歌用户余额不足"}';
                exit;
            }
            
            D("Showlistsong")->execute('update ss_showlistsong set status="1",showstatus="已同意" where id=' . $_REQUEST['ssid']);
            // 扣费
            D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $songinfo['pickuid']);
            // 记入虚拟币交易明细
            $Coindetail = D("Coindetail");
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'sendgift';
            $Coindetail->uid = $songinfo['pickuid'];
            $Coindetail->touid = $songinfo['uid'];
            $Coindetail->giftid = 9999;
            $Coindetail->giftcount = 1;
            $Coindetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 点了一首歌 ' . $songinfo['songname'];
            $Coindetail->objectIcon = '/style/images/gift/song.png';
            $Coindetail->coin = $needcoin;
            if ($emceeinfo['broadcasting'] == 'y') {
                $Coindetail->showId = $emceeinfo['showid'];
            }
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();

            //被赠送人加豆
            // $scale = D('Member')->getByid($songinfo['uid']); // 取出改主播的信息
            // if ($scale['sharingratio'] != '0') { // 优先按照指定的比例算
            if ($emceeinfo['agentuid'] != 0) {
                $ratio = D('Agentfamily')->where('uid='.$emceeinfo['agentuid'])->getField('uid,familyratio,anchorratio');
                $ratio = $ratio[$emceeinfo['agentuid']];
                $beannumAgent = ceil($needcoin * ($ratio['familyratio'] /100));
                $beannum = ceil($needcoin * ($ratio['anchorratio'] / 100));
                 // $beannum = ceil($needcoin * ($scale['sharingratio'] / 100));
            } else { // 默认的比例
                $beannumAgent = 0;
                $beannum = ceil($needcoin * ($this->emceededuct / 100));
            }
            // $beannum = ceil($needcoin * ($this->emceededuct / 100));
            D("Member")->execute('update ss_member set earnbean=earnbean+' . $beannum . ',beanbalance=beanbalance+' . $beannum . ' where id=' . $songinfo['uid']);
            $Beandetail = D("Beandetail");
            $Beandetail->create();
            $Beandetail->type = 'income';
            $Beandetail->action = 'getgift';
            $Beandetail->uid = $songinfo['uid'];
            $Beandetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 点了一首歌 ' . $songinfo['songname'];
            $Beandetail->bean = $beannum;
            $Beandetail->addtime = time();
            $detailId = $Beandetail->add();

            if ($emceeinfo['agentuid'] != 0) {
                // $beannum = ceil($needcoin * ($this->emceeagentdeduct / 100));
                //D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$emceeinfo['agentuid']);
                D("Member")->execute('update ss_member set beanbalance2=beanbalance2+' . $beannumAgent . ' where id=' . $emceeinfo['agentuid']);
                $Emceeagentbeandetail = D("Emceeagentbeandetail");
                $Emceeagentbeandetail->create();
                $Emceeagentbeandetail->type = 'income';
                $Emceeagentbeandetail->action = 'getgift';
                $Emceeagentbeandetail->uid = $emceeinfo['agentuid'];
                $Emceeagentbeandetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 点了一首歌 ' . $songinfo['songname'];
                $Emceeagentbeandetail->bean = $beannumAgent;
                $Emceeagentbeandetail->addtime = time();
                $detailId = $Emceeagentbeandetail->add();
            }

            echo '{"code":"0","userNo":"' . $userinfo['curroomnum'] . '","userId":"' . $userinfo['id'] . '","userName":"' . $songinfo['usernick'] . '","songName":"' . $songinfo['songname'].'"}';
            exit;
        } else {
            echo '{"code":"1","info":"没有该点歌记录"}';
            exit;
        }
    }

    public function show_disAgreeSong()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }

        if ($_SESSION['uid'] != $_REQUEST['eid']) {
            echo '{"code":"1","info":"您没有权限"}';
            exit;
        }

        $songinfo = D("Showlistsong")->find($_REQUEST['ssid']);
        if ($songinfo) {
            D("Showlistsong")->execute('update ss_showlistsong set status="2", showstatus="未同意" where id=' . $_REQUEST['ssid']);
            echo '{"code":"0"}';
            exit;
        } else {
            echo '{"code":"1","info":"没有该点歌记录"}';
            exit;
        }
    }

    public function show_setSongApply()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }
        
        D("Member")->execute('update ss_member set songapply="' . $_REQUEST['apply'] . '" where id=' . $_SESSION['uid']);

        echo '{"code":"0"}';
        exit;
    }

    public function dosendFly()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }
        
        $emceeinfo = D("Member")->find($_REQUEST['eid']);
        if ($_REQUEST['toid'] == 0) {
            $besenduinfo = $emceeinfo;
        } else {
            $besenduinfo = D("Member")->find($_REQUEST['toid']);
        }
        if ($emceeinfo) {
            //判断虚拟币是否足够
            //获取用户信息
            $userinfo = D("Member")->find($_SESSION['uid']);
            $needcoin = 1000;
            if ($userinfo['coinbalance'] < $needcoin) {
                echo '{"code":"1","info":"你的余额不足"}';
                exit;
            }

            D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
            // 记入虚拟币交易明细
            $Coindetail = D("Coindetail");
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'sendgift';
            $Coindetail->uid = $_SESSION['uid'];
            $Coindetail->touid = $besenduinfo['id'];
            $Coindetail->giftid = 999999;
            $Coindetail->giftcount = 1;
            $Coindetail->content = $userinfo['nickname'] . ' 向 ' . $besenduinfo['nickname'] . ' 送了 飞屏1个';
            $Coindetail->objectIcon = '/style/images/gift/feiping.png';
            $Coindetail->coin = $needcoin;
            if ($emceeinfo['broadcasting'] == 'y') {
                $Coindetail->showId = $emceeinfo['showid'];
            }
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();

            // 被赠送人加豆
            $beannum = ceil($needcoin * ($this->emceededuct / 100));
            D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$besenduinfo['id']);
            $Beandetail = D("Beandetail");
            $Beandetail->create();
            $Beandetail->type = 'income';
            $Beandetail->action = 'getgift';
            $Beandetail->uid = $besenduinfo['id'];
            $Beandetail->content = $userinfo['nickname'] . ' 向 ' . $besenduinfo['nickname'] . ' 送了 飞屏1个';
            $Beandetail->bean = $beannum;
            $Beandetail->addtime = time();
            $detailId = $Beandetail->add();

            if ($emceeinfo['agentuid'] != 0) {
                $beannum = ceil($needcoin * ($this->emceeagentdeduct / 100));
                //D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$emceeinfo['agentuid']);
                D("Member")->execute('update ss_member set beanbalance2=beanbalance2+' . $beannum . ' where id=' . $emceeinfo['agentuid']);
                $Emceeagentbeandetail = D("Emceeagentbeandetail");
                $Emceeagentbeandetail->create();
                $Emceeagentbeandetail->type = 'income';
                $Emceeagentbeandetail->action = 'getgift';
                $Emceeagentbeandetail->uid = $emceeinfo['agentuid'];
                $Emceeagentbeandetail->content = $userinfo['nickname'] . ' 向 ' . $besenduinfo['nickname'] . ' 送了 飞屏1个';
                $Emceeagentbeandetail->bean = $beannum;
                $Emceeagentbeandetail->addtime = time();
                $detailId = $Emceeagentbeandetail->add();
            }
            
            echo '{"code":"0"}';
            exit;
        } else {
            echo '{"code":"1","info":"主播信息有误"}';
            exit;
        }
    }

    public function show_bandingNote()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"2"}';
            exit;
        }

        // 获取用户信息
        $userinfo = D("Member")->find($_SESSION['uid']);
        // 获取主播信息
        $emceeinfo = D("Member")->find($_REQUEST['rid']);
        // 获取被贴条人信息
        $betieinfo = D("Member")->find($_REQUEST['recieverId']);
        // 贴条信息
        $ttinfo = D("Tietiao")->find($_REQUEST['noteId']); 
        if ($ttinfo) {
            // 不能给主播贴条
            if ($_REQUEST['recieverId'] == $_REQUEST['rid']) {
                echo '{"code":"2"}';
                exit;
            }
            // 判断虚拟币是否足够
            if ($userinfo['coinbalance'] < $ttinfo['needcoin']) {
                echo '{"code":"1"}';
                exit;
            }
            // 判断此人是否有被贴条，贴条是否还在身上
            $Bandingnotes = D("Bandingnote")->where('uid=' . $_REQUEST['recieverId'] . ' and showId=' . $emceeinfo['showid'] . ' and addtime>' . (time() - 100))->order('addtime desc')->select();
            if ($Bandingnotes) {
                echo '{"code":"3"}';
                exit;
            } else {
                // 写入贴条记录
                $Bandingnote = D("Bandingnote");
                $Bandingnote->create();
                $Bandingnote->uid = $_REQUEST['recieverId'];
                $Bandingnote->showId = $emceeinfo['showid'];
                $Bandingnote->addtime = time();
                $bandId = $Bandingnote->add();

                // 扣费
                D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $ttinfo['needcoin'].',coinbalance=coinbalance-' . $ttinfo['needcoin'] . ' where id=' . $_SESSION['uid']);
                // 记入虚拟币交易明细
                $Coindetail = D("Coindetail");
                $Coindetail->create();
                $Coindetail->type = 'expend';
                $Coindetail->action = 'sendgift';
                $Coindetail->uid = $_SESSION['uid'];
                $Coindetail->touid = $_REQUEST['recieverId'];
                $Coindetail->giftid = 9999999;
                $Coindetail->giftcount = 1;
                $Coindetail->content = $userinfo['nickname'] . ' 给 ' . $betieinfo['nickname'] . ' 贴了一个条';
                $Coindetail->objectIcon = '/style/images/tietiao.png';
                $Coindetail->coin = $ttinfo['needcoin'];
                if ($emceeinfo['broadcasting'] == 'y') {
                    $Coindetail->showId = $emceeinfo['showid'];
                }
                $Coindetail->addtime = time();
                $detailId = $Coindetail->add();

                // 被赠送人加豆
                /*
                    $beannum = ceil($ttinfo['needcoin'] * 0.3);
                    D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$_REQUEST['rid']);
                    $Beandetail = D("Beandetail");
                    $Beandetail->create();
                    $Beandetail->type = 'income';
                    $Beandetail->action = 'getgift';
                    $Beandetail->uid = $_REQUEST['rid'];
                    $Beandetail->content = $userinfo['nickname'].' 给 '.$betieinfo['nickname'].' 贴了一个条';
                    $Beandetail->bean = $beannum;
                    $Beandetail->addtime = time();
                    $detailId = $Beandetail->add();
                */

                echo '{"code":"0","money":"' . $ttinfo['needcoin'] . '"}';
                exit;
            }
        } else {
            echo '{"code":"2"}';
            exit;
        }
    }

    public function show_takeSeat()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }

        $emceeinfo = D("Member")->find($_REQUEST['emceeId']);
        if ($emceeinfo) {
            if ($_REQUEST['count'] <= $emceeinfo['seat' . $_REQUEST['seatid'] . '_count']) {
                echo '{"code":"1","info":"您抢座的沙发数小于当前沙发数"}';
                exit;
            } else {
                //判断虚拟币是否足够
                //获取用户信息
                $userinfo = D("Member")->find($_SESSION['uid']);
                $needcoin = 100 * $_REQUEST['count'];
                if ($userinfo['coinbalance'] < $needcoin) {
                    echo '{"code":"1","info":"您的余额不足"}';
                    exit;
                }

                D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                //记入虚拟币交易明细
                $Coindetail = D("Coindetail");
                $Coindetail->create();
                $Coindetail->type = 'expend';
                $Coindetail->action = 'sendgift';
                $Coindetail->uid = $_SESSION['uid'];
                $Coindetail->touid = $_REQUEST['emceeId'];
                $Coindetail->giftid = 99999;
                $Coindetail->giftcount = $_REQUEST['count'];
                $Coindetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 送了 沙发 ' . $_REQUEST['count'] . ' 个';
                $Coindetail->objectIcon = '/style/images/gift/sofa.png';
                $Coindetail->coin = $needcoin;
                if ($emceeinfo['broadcasting'] == 'y') {
                    $Coindetail->showId = $emceeinfo['showid'];
                }
                $Coindetail->addtime = time();
                $detailId = $Coindetail->add();

                //被赠送人加豆
                if ($emceeinfo['agentuid'] != 0) {
                    $ratio = D('Agentfamily')->where('uid='.$emceeinfo['agentuid'])->getField('uid,familyratio,anchorratio');
                    $ratio = $ratio[$emceeinfo['agentuid']];
                    $beannumAgent = ceil($needcoin * ($ratio['familyratio'] /100));
                    $beannum = ceil($needcoin * ($ratio['anchorratio'] / 100));
                } else {
                    $beannumAgent = 0;
                    $beannum = ceil($needcoin * ($this->emceededuct / 100));
                }
                // $beannum = ceil($needcoin * ($this->emceededuct / 100));
                D("Member")->execute('update ss_member set earnbean=earnbean+' . $beannum . ',beanbalance=beanbalance+' . $beannum . ' where id=' . $_REQUEST['emceeId']);
                $Beandetail = D("Beandetail");
                $Beandetail->create();
                $Beandetail->type = 'income';
                $Beandetail->action = 'getgift';
                $Beandetail->uid = $_REQUEST['emceeId'];
                $Beandetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 送了 沙发 ' . $_REQUEST['count'] . ' 个';
                $Beandetail->bean = $beannum;
                $Beandetail->addtime = time();
                $detailId = $Beandetail->add();

                if($emceeinfo['agentuid'] != 0){
                    // $beannum = ceil($needcoin * ($this->emceeagentdeduct / 100));
                    //D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$emceeinfo['agentuid']);
                    D("Member")->execute('update ss_member set beanbalance2=beanbalance2+' . $beannumAgent . ' where id='. $emceeinfo['agentuid']);
                    $Emceeagentbeandetail = D("Emceeagentbeandetail");
                    $Emceeagentbeandetail->create();
                    $Emceeagentbeandetail->type = 'income';
                    $Emceeagentbeandetail->action = 'getgift';
                    $Emceeagentbeandetail->uid = $emceeinfo['agentuid'];
                    $Emceeagentbeandetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 送了 沙发 ' . $_REQUEST['count'] . ' 个';
                    $Emceeagentbeandetail->bean = $beannumAgent;
                    $Emceeagentbeandetail->addtime = time();
                    $detailId = $Emceeagentbeandetail->add();
                }
                
                D("Member")->execute('update ss_member set seat' . $_REQUEST['seatid'] . '_uid=' . $_SESSION['ucuid'] . ',seat' . $_REQUEST['seatid'] . '_nickname="' . $_SESSION['nickname'] . '",seat' . $_REQUEST['seatid'] . '_count=' . $_REQUEST['count'] . ' where id=' . $_REQUEST['emceeId']);
                $roomnum = D("Member")->field('curroomnum')->where('id='.$_REQUEST['emceeId'])->select();
                //$userIcon = $this->ucurl . 'avatar.php?uid=' . $_SESSION['ucuid'] . '&size=middle';
                $userIcon = "/style/avatar/". substr(md5($_SESSION['uid']),0,3) ."/". $_SESSION['uid'] ."_middle.jpg";
                include_once realpath(__DIR__ . '/../') . '/GatewayClient/Gateway.php';
                include APP_PATH . '/config.inc.php';
                Gateway::$registerAddress = $register_address;
                Gateway::sendToGroup($roomnum[0]['curroomnum'], json_encode(array('type'=>'takeSeat','seatId'=>$_REQUEST['seatid'],'client_name'=>$_SESSION['nickname'],'userIcon'=>$userIcon, 'seatPrice'=>$_REQUEST['count'])));
                
                echo '{"code":"0","userNick":"' . $_SESSION['nickname'] . '","userIcon":"' . $userIcon . '","seatId":"' . $_REQUEST['seatid'] . '","seatPrice":"' . $_REQUEST['count'].'"}';
                exit;
            }    
        } else {
            echo '{"code":"1","info":"主播信息有误"}';
            exit;
        }
    }

    public function show_sendGift()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }

        // 获取用户信息
        $userinfo = D("Member")->find($_SESSION['uid']);
        //$userUcuid = $userinfo['ucuid'];
        $userLevel = getRichlevel($userinfo['spendcoin']);
        $userLevelIDOld = $userLevel[0]['levelid'];


        // 获取被赠送人信息
        $emceeinfo = D("Member")->find($_REQUEST['toid']);
        // 根据gid获取礼物信息
        $giftinfo = D("Gift")->find($_REQUEST['gid']);
        $gidd = $_REQUEST['gid'];
        // 判断虚拟币是否足够
        $needcoin = $giftinfo['needcoin'] * $_REQUEST['count'];
        $kk =  isset($_REQUEST['kk']) ? $_REQUEST['kk'] : '';
        if (trim($kk) != 'kc') {
            if ($userinfo['coinbalance'] < $needcoin) {
                echo '{"code":"1","info":"你的余额不足"}';
                exit;
            }
            
            D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
            $verification = $_SESSION['uid'] . $_REQUEST['gid'] . $_REQUEST['count'] . $needcoin;
            // D("Member") ->where('id='.$_SESSION['uid'])->save(array('isdebit'=>$verification));
            D("Member")->execute("update ss_member set isdebit='{$verification}' where id={$_SESSION['uid']}");
            // 记入虚拟币交易明细
            $Coindetail = D("Coindetail");
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'sendgift';
            $Coindetail->uid = $_SESSION['uid'];
            $Coindetail->touid = $_REQUEST['toid'];
            $Coindetail->giftid = $_REQUEST['gid'];
            $Coindetail->giftcount = $_REQUEST['count'];
            $Coindetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 赠送礼物 ' . $giftinfo['giftname'] . ' ' . $_REQUEST['count'] . ' 个';
            // $smallIcon = str_replace('/50/','/25/',$giftinfo['gifticon']);
            // $smallIcon = str_replace('.png','.gif',$smallIcon);
            $smallIcon = $giftinfo['gifticon_25'];
            $Coindetail->objecticon = $smallIcon;
            $Coindetail->coin = $needcoin;
            if ($emceeinfo['broadcasting'] == 'y') {
                $Coindetail->showid = $emceeinfo['showid'];
            }
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();
            // ($this->emceededuct / 100)
            // 被赠送人加豆
            // ($this->emceededuct / 100)
            // $scale = D('Member')->getByid($songinfo['uid']); // 取出改主播的信息
            // if ($scale['sharingratio'] != '0') { // 优先按照指定的比例算
            if ($emceeinfo['agentuid'] != 0) {
                $ratio = D('Agentfamily')->where('uid='.$emceeinfo['agentuid'])->getField('uid,familyratio,anchorratio');
                $ratio = $ratio[$emceeinfo['agentuid']];
                $beannumAgent = ceil($needcoin * ($ratio['familyratio'] /100));
                $beannum = ceil($needcoin * ($ratio['anchorratio'] / 100));
            } else {//默认的比例
                $beannumAgent = 0;
                $beannum = ceil($needcoin * ($this->emceededuct / 100));
            }
            // 主播秀豆
            $anchorBean = $emceeinfo['beanbalance'] + $beannum;

            D("Member")->execute('update ss_member set earnbean=earnbean+' . $beannum . ',beanbalance=beanbalance+' . $beannum . ' where id=' . $_REQUEST['toid']);
            $Beandetail = D("Beandetail");
            $Beandetail->create();
            $Beandetail->type = 'income';
            $Beandetail->action = 'getgift';
            $Beandetail->uid = $_REQUEST['toid'];
            $Beandetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 赠送礼物 ' . $giftinfo['giftname'] . ' ' . $_REQUEST['count'] . ' 个';
            $Beandetail->bean = $beannum;
            $Beandetail->addtime = time();
            $detailId = $Beandetail->add();
    
            if ($emceeinfo['agentuid'] != 0) {
                // $beannum = ceil($needcoin * ($this->emceeagentdeduct / 100));
                // D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$emceeinfo['agentuid']);
                D("Member")->execute('update ss_member set beanbalance2=beanbalance2+' . $beannumAgent. ' where id=' . $emceeinfo['agentuid']);
                $Emceeagentbeandetail = D("Emceeagentbeandetail");
                $Emceeagentbeandetail->create();
                $Emceeagentbeandetail->type = 'income';
                $Emceeagentbeandetail->action = 'getgift';
                $Emceeagentbeandetail->uid = $emceeinfo['agentuid'];
                $Emceeagentbeandetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 赠送礼物 ' . $giftinfo['giftname'] . ' ' . $_REQUEST['count'] . ' 个';
                $Emceeagentbeandetail->bean = $beannumAgent;
                $Emceeagentbeandetail->addtime = time();
                $detailId = $Emceeagentbeandetail->add();
            }
            $ajaxResult = '{"code":"0","giftPath":"' . $smallIcon . '","giftStyle":"' . $giftinfo['giftstyle'] . '","giftGroup":"' . $giftinfo['sid'] . '","giftType":"' . $giftinfo['gifttype'] . '","toUserNo":"' . $emceeinfo['curroomnum'] . '","isGift":"0","giftLocation":"[]","giftIcon":"' . $giftinfo['gifticon'] . '","giftSwf":"' . $giftinfo['giftswf'] . '","toUserId":"' . $_REQUEST['toid'] . '","toUserName":"' . $emceeinfo['nickname'] . '","userNo":"' . $_SESSION['roomnum'] . '","giftCount":"' . $_REQUEST['count'] . '","userId":"' . $_SESSION['uid'] . '","giftName":"' . $giftinfo['giftname'] . '","userName":"' . $_SESSION['nickname'] . '","giftId":"' . $giftinfo['id'] . '"}';
            $giftStyle = $giftinfo['giftstyle'];
            $giftIcon = $giftinfo['gifticon'];
            $giftSwf = $giftinfo['giftswf'];
            $giftCount = $_REQUEST['count'];
            $giftName = $giftinfo['giftname'];
            $giftId = $giftinfo['id'];
        } else {
            include '/lib/action/daojuset.php';
            $ajaxResult = '{"code":"0","giftPath":"' . $smallIcon . '","giftStyle":"'.'豪华'.'","giftGroup":"' . $giftinfo['sid'] . '","giftType":"' . $giftinfo['gifttype'] . '","toUserNo":"' . $emceeinfo['curroomnum'] . '","isGift":"0","giftLocation":"[]","giftIcon":"' . $daojuicon[$gidd] . '","giftSwf":"' . $daojuswf[$gidd] . '","toUserId":"' . $_REQUEST['toid'] . '","toUserName":"' . $emceeinfo['nickname'] . '","userNo":"' . $_SESSION['roomnum'] . '","giftCount":"'.'0'.'","userId":"' . $_SESSION['uid'] . '","giftName":"' . $gidd . '","userName":"' . $_SESSION['nickname'] . '","giftId":"' . $gidd . '"}';
            $giftStyle = '豪华';
            $giftIcon = $daojuicon[$gidd];
            $giftSwf = $daojuswf[$gidd];
            $giftCount = 0;
            $giftName = $gidd;
            $giftId = $gidd;
        }
        $userLevel = getRichlevel($userinfo['spendcoin']+$needcoin);
        $userLevelIDNew = $userLevel[0]['levelid'];
        /*
        if ($userLevelIDNew > $userLevelIDOld) {
            // TODO  广播升级事件,更新当前用户levelid
        }
        */
        include_once realpath(__DIR__ . '/../') . '/GatewayClient/Gateway.php';
        include APP_PATH . '/config.inc.php';
        $message = array(
                'type'=>'sendGift',
                'giftPath'=>$smallIcon,
                'giftStyle' => $giftStyle,
                'giftGroup'=> $giftinfo['sid'],
                'giftType' => $giftinfo['gifttype'],
                'toUserNo'=> $emceeinfo['curroomnum'],
                'isGift' =>0,
                'anchorBalance'=> $anchorBean,
                'giftLocation'=>array(),
                'giftIcon'=>$giftIcon,
                'giftSwf'=>$giftSwf,
                'toUserId'=>$_REQUEST['toid'],
                'toUserName'=>$emceeinfo['nickname'],
                'userNo'=>$_SESSION['roomnum'],
                'giftCount'=>$giftCount,
                'userId'=>$_SESSION['uid'],
                'giftName'=>$giftName,
                'userName'=> $_SESSION['nickname'],
                'giftId'=>$giftId,
                'from_user_id'=>$_SESSION['uid'],
                'vip'=> (!$userinfo['vip'] ? 0 : $userinfo['vip']),
                'levelid'=> $userLevelIDNew,
                'from_client_name' => $_SESSION['nickname'],
                //'from_client_avatar' => ($this->ucurl.'avatar.php?uid='.$userUcuid.'&size=small'),
                'from_client_avatar' => ('/style/avatar/'.substr(md5($_SESSION['uid']),0,3).'/'.$_SESSION['uid'].'_small.jpg'),
                'type' => 'sendGift',
                'code' => 0,
                'time' => date('H:i', time()),
            );
        print_r($message);
        
        Gateway::$registerAddress = $register_address;
        Gateway::sendToGroup($emceeinfo['curroomnum'], json_encode($message));
        echo $ajaxResult;
        exit();
    }
    
    public function show_sendHb()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }

        // 获取用户信息
        $userinfo = D("Member")->find($_SESSION['uid']);
        // 获取主播信息
        $emceeinfo = D("Member")->find($_REQUEST['eid']);
        // 判断红包是否足够
        if ($userinfo['fundhb'] < 1) {
            echo '{"code":"1","info":"您的红包不足"}';
            exit;
        }
        
        if ($userinfo['sendhb2'] == ($this->sendhb - 1)) {
            D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $this->spendcoin . ',fundhb=fundhb-1,sendhb=sendhb+1,sendhb2=0 where id=' . $_SESSION['uid']);
        } else {
            D("Member")->execute('update ss_member set fundhb=fundhb-1,sendhb=sendhb+1,sendhb2=sendhb2+1 where id=' . $_SESSION['uid']);
        }
        
        if ($emceeinfo['lastgethbtime'] == 0) {
            $gethb = $emceeinfo['gethb'] + 1;
            $gethb_day = $emceeinfo['gethb_day'] + 1;
            $gethb_week = $emceeinfo['gethb_week'] + 1;
            $gethb_month = $emceeinfo['gethb_month'] + 1;
        } else {
            $gethb = $emceeinfo['gethb'] + 1;
            if (date('Y-m-d', $emceeinfo['lastgethbtime']) == date('Y-m-d',time())) {
                $gethb_day = $emceeinfo['gethb_day'] + 1;
            } else {
                $gethb_day = 1;
            }
            if (date('Y', $emceeinfo['lastgethbtime']) == date('Y', time()) 
                && date('W', $emceeinfo['lastgethbtime']) == date('W', time())) 
            {
                $gethb_week = $emceeinfo['gethb_week'] + 1;
            } else {
                $gethb_week = 1;
            }
            if (date('Y-m', $emceeinfo['lastgethbtime']) == date('Y-m', time())) {
                $gethb_month = $emceeinfo['gethb_month'] + 1;
            }  else {
                $gethb_month = 1;
            }
        }
        D("Member")->execute("update ss_member set gethb=" . $gethb . ",gethb_day=" . $gethb_day . ",gethb_week=" . $gethb_week . ",gethb_month=" . $gethb_month . ",hbbalance=hbbalance+1,lastgethbtime=" . time() . " where id=" . $_REQUEST['eid']);
        
        echo '{"code":"0","userNo":"' . $_SESSION['roomnum'] . '","userId":"' . $_SESSION['uid'] . '","userName":"' . $_SESSION['nickname'] . '"}';
        exit;
    }

    public function speaker_handler()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"2"}';
            exit;
        }

        if ($_REQUEST['msg'] == '') {
            echo '{"code":"1"}';
            exit;
        }

        if (strlen($_REQUEST['msg']) > 100) {
            echo '{"code":"5"}';
            exit;
        }

        // 获取用户信息
        $userinfo = D("Member")->find($_SESSION['uid']);
        if ($userinfo['atwill'] == 'y' && $userinfo['awexpire'] > time()) {
            $count = D("Coindetail")->where('uid=' . $_SESSION['uid'] . ' and objecticon="/style/images/fei.png" and coin=0 and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y")')->count();
            if ($count >= 100) {
                $isfree = "n";
            } else {
                $isfree = "y";
            }
        } else {
            $isfree = "n";
        }
            
        if ($isfree == 'n') {
            //判断虚拟币是否足够
            $needcoin = 500;
            if ($userinfo['coinbalance'] < $needcoin) {
                echo '{"code":"3"}';
                exit;
            }
        } else {
            $needcoin = 0;
        }

        D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
        //记入虚拟币交易明细
        $Coindetail = D("Coindetail");
        $Coindetail->create();
        $Coindetail->type = 'expend';
        $Coindetail->action = 'sendgift';
        $Coindetail->uid = $_SESSION['uid'];
        $Coindetail->content = $userinfo['nickname'].' 发送了一条小喇叭';
        $Coindetail->objectIcon = '/style/images/fei.png';
        $Coindetail->coin = $needcoin;
        if ($emceeinfo['broadcasting'] == 'y') {
            $Coindetail->showId = $emceeinfo['showid'];
        }
        $Coindetail->addtime = time();
        $detailId = $Coindetail->add();

        //echo '{"code":"0","msg":"<b class=\"red\">'.$userinfo['nickname'].'('.$userinfo['curroomnum'].')：</b><a href=\"/'.$_REQUEST['emceeId'].'\" target=\"_blank\">'.iconv('gbk','utf-8',$_REQUEST['msg']).'</a>"}';
        echo '{"code":"0","userName":"' . $userinfo['nickname'] . '","userNo":"' . $userinfo['curroomnum'] . '","emceeId":"' . $_REQUEST['emceeId'] . '","msg":"' . $_REQUEST['msg'] . '"}';
        exit;
    }

    public function shutup()
    {
        C('HTML_CACHE_ON',false);
        // 获取用户信息
        $userinfo = D("Member")->find($_REQUEST['uidlist']);
        if ($userinfo) {
            if ($userinfo['showadmin'] == '1') {
                echo '{"code":"1","info":"对方是系统管理员不能禁言"}';
                exit;
            }
            if ($_REQUEST['uidlist'] == $_REQUEST['rid']) {
                echo '{"code":"1","info":"对方是主播不能禁言"}';
                exit;
            }
            $myshowadmin = D("Roomadmin")->where('uid=' . $_REQUEST['rid'] . ' and adminuid=' . $_REQUEST['uidlist'])->order('id asc')->select();
            if ($myshowadmin) {
                echo '{"code":"1","info":"对方是管理员不能禁言"}';
                exit;
            }
            if ($userinfo['vip'] > 0 && $userinfo['vipexpire'] > time()) {
                if ($userinfo['vip'] == 1) {
                    echo '{"code":"1","info":"对方是VIP不能禁言"}';
                    exit;
                }
                if ($userinfo['vip'] == 2) {
                    if ($_SESSION['uid'] == $_REQUEST['rid']) {
                        echo '{"code":"0"}';
                        exit;
                    } else {
                        echo '{"code":"1","info":"对方是VIP不能禁言"}';
                        exit;
                    }
                }
            } else {
                echo '{"code":"0"}';
                exit;
            }
        } else {
            echo '{"code":"1","info":"找不到该用户"}';
            exit;
        }
    }

    public function kick()
    {
        C('HTML_CACHE_ON', false);
        //获取用户信息
        $userinfo = D("Member")->find($_REQUEST['uidlist']);
        if ($userinfo) {
            if ($userinfo['showadmin'] == '1') {
                echo '{"code":"1","info":"对方是系统管理员不能踢出"}';
                exit;
            }
            if ($_REQUEST['uidlist'] == $_REQUEST['rid']) {
                echo '{"code":"1","info":"对方是主播不能踢出"}';
                exit;
            }
            $myshowadmin = D("Roomadmin")->where('uid=' . $_REQUEST['rid'] . ' and adminuid=' . $_REQUEST['uidlist'])->order('id asc')->select();
            if ($myshowadmin) {
                echo '{"code":"1","info":"对方是管理员不能踢出"}';
                exit;
            }
            if ($userinfo['vip'] > 0 && $userinfo['vipexpire'] > time()) {
                if ($userinfo['vip'] == 1) {
                    echo '{"code":"1","info":"对方是VIP不能踢出"}';
                    exit;
                }
                if ($userinfo['vip'] == 2) {
                    if ($_SESSION['uid'] == $_REQUEST['rid']) {
                        echo '{"code":"0"}';
                        exit;
                    } else {
                        echo '{"code":"1","info":"对方是VIP不能踢出"}';
                        exit;
                    }
                }
            } else {
                echo '{"code":"0"}';
                exit;
            }
        } else {
            echo '{"code":"1","info":"找不到该用户"}';
            exit;
        }
    }

    public function toggleShowAdmin()
    {
        C('HTML_CACHE_ON', false);
        if ($_SESSION['uid'] != $_REQUEST['eid']) {
            echo '{"code":"1","info":"您没有权限"}';
            exit;
        }
        
        if ($_REQUEST['state'] == 1) {
            $myshowadmin = D("Roomadmin")->where('uid=' . $_SESSION['uid'] . ' and adminuid=' . $_REQUEST['userid'])->order('id asc')->select();
            if ($myshowadmin) {
                echo '{"code":"0"}';
                exit;
            } else {
                $Roomadmin = D("Roomadmin");
                $Roomadmin->create();
                $Roomadmin->uid = $_SESSION['uid'];
                $Roomadmin->adminuid = $_REQUEST['userid'];
                $Roomadmin->add();
                echo '{"code":"0"}';
                exit;
            }
        } else {
            D("Roomadmin")->where('uid=' . $_SESSION['uid'] . ' and adminuid=' . $_REQUEST['userid'])->delete();
            echo '{"code":"0"}';
            exit;
        }
    }

    public function show_redbaginfo()
    {
        C('HTML_CACHE_ON', false);

        if (!isset($_SESSION['uid']) || $_SESSION['uid'] == '' || $_SESSION['uid'] == null || $_SESSION['uid'] < 0) {
            echo '请<a href="#" onclick="javascript:UAC.openUAC(0); return false;" title="登录">登录</a>或<a href="#" onclick="javascript:UAC.openUAC(1); return false;" title="注册">注册</a>领取红包<br>&nbsp;';
        } else {
            $userinfo = D("Member")->find($_SESSION['uid']);
            echo '</span><span style="color:#cccccc;">您已累积</span><span style="font-weight:bold;color:#FF0000;" id="fundhb">'.$userinfo['fundhb'].'</span><span style="color:#cccccc;">个红包，点击送给主播1个红包<br>已送出</span><span style="font-weight:bold;color:#FF0000;" id="sendhb">'.$userinfo['sendhb'].'</span></span><span style="color:#cccccc;">个红包</span>';
            if ((int)$userinfo['vip'] > 0 && $userinfo['vipexpire'] > time()) {
                echo '<script type="text/javascript" language="javascript">var gethbinterval=setInterval(function(){$("#redBagBox").load(\'/index.php/Show/show_redbaginfo2/\',function (responseText, textStatus, XMLHttpRequest){this;});}, '.($this->gethbinterval*60*1000).');</script>';
            } else {
                echo '<script type="text/javascript" language="javascript">var gethbinterval=setInterval(function(){$("#redBagBox").load(\'/index.php/Show/show_redbaginfo2/\',function (responseText, textStatus, XMLHttpRequest){this;});}, '.($this->vip_gethbinterval*60*1000).');</script>';
            }
        }
    }

    public function show_redbaginfo2()
    {
        C('HTML_CACHE_ON', false);
        
        $userinfo = D("Member")->find($_SESSION['uid']);
        if ($userinfo) {
            if ((int)$userinfo['vip'] > 0 && $userinfo['vipexpire'] > time()) {
                $maxdayfundhb = $this->vip_maxdaygethb;
            } else {
                $maxdayfundhb = $this->maxdaygethb;
            }

            if ($userinfo['lastfundtime'] == 0) {
                $userdayfund = 0;
            } else {
                if (date('Y-m-d', $userinfo['lastfundtime']) != date('Y-m-d', time())) {
                    $userdayfund = 0;
                    D("Member")->execute('update ss_member set dayfund=0 where id=' . $_SESSION['uid']);
                } else {
                    $userdayfund = $userinfo['dayfund'];
                }
            }

            if ($userdayfund < $maxdayfundhb) {
                echo '您已累积<span style="font-weight:bold;color:#FF0000;" id="fundhb">' . ($userinfo['fundhb'] + 1) . '</span>个红包，点击送给主播1个红包<br>已送出<span style="font-weight:bold;color:#FF0000;" id="sendhb">' . $userinfo['sendhb'] . '</span>个红包';
                D("Member")->execute('update ss_member set fundhb=fundhb+1,lastfundtime=' . time() . ',dayfund=dayfund+1 where id=' . $_SESSION['uid']);
            } else {
                echo '您已累积<span style="font-weight:bold;color:#FF0000;" id="fundhb">' . $userinfo['fundhb'] . '</span>个红包，点击送给主播1个红包<br>已送出<span style="font-weight:bold;color:#FF0000;" id="sendhb">' . $userinfo['sendhb'] . '</span>个红包';
                echo '<script type="text/javascript" language="javascript">clearInterval(gethbinterval);</script>';
            }
        } else {
            echo '请<a href="#" onclick="javascript:UAC.openUAC(0); return false;" title="登录">登录</a>或<a href="#" onclick="javascript:UAC.openUAC(1); return false;" title="注册">注册</a>领取红包<br>&nbsp;';
        }
    }
    
    public function getcard()
    {
        $data = D("Member")->where("id={$_SESSION['uid']}")->find();
        $this->ajaxReturn($data);
    }

    public function show_redbagrank()
    {
        C('HTML_CACHE_ON', false);

        $hbRank_day = D('Member')->query('SELECT * FROM `ss_member` where gethb_day>0 and date_format(FROM_UNIXTIME(lastgethbtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") order by gethb_day desc LIMIT 10');
        $this->assign('hbRank_day', $hbRank_day);
        $hbRank_week = D('Member')->query('SELECT * FROM `ss_member` where gethb_week>0 and date_format(FROM_UNIXTIME(lastgethbtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(lastgethbtime),"%u")=date_format(now(),"%u") order by gethb_week desc LIMIT 10');
        $this->assign('hbRank_week', $hbRank_week);
        $hbRank_month = D('Member')->query('SELECT * FROM `ss_member` where gethb_month>0 and  date_format(FROM_UNIXTIME(lastgethbtime),"%m")=date_format(now(),"%m") order by gethb_month desc LIMIT 10');
        $this->assign('hbRank_month', $hbRank_month);
        $hbRank_all = D('Member')->query('SELECT * FROM `ss_member` where gethb>0 order by gethb desc LIMIT 10');
        $this->assign('hbRank_all', $hbRank_all);

        $this->display();
    }
    
    public function getucuid()
    {
        $ucuid = D("Member")->field("ucuid")->where("id={$_GET['uid']}")->find();
        echo $ucuid['ucuid'];
    }


    public function get_gift_list()
    {
        // 礼物 列表
        $liwulist = M()->query("select * from ss_coindetail c  join ss_member m ON m.id = c.uid where c.action = 'sendgift'  order by c.addtime DESC limit 3");
        foreach ($liwulist as $key => $value) {
            $touser = M("member")->where("id = {$liwulist[$key]['touid']} ")->find();
            $giftname = M("gift")->where("id = $liwulist[$key]['giftid']")->find();
            
        }
        $str = "";
        foreach ($liwulist as $key => $value) {
            $time = date("H:i", $value['addtime']);
            $str.="<li><a target='_blank' href='/{$value['curroomnum']}' style='color:#FFFFFF;'>{$time}&nbsp;{$value['content']}</em></a></li>";
        }
        echo $str;
    }
    //id,time,
    public function get_sendgift_list($limit = 30){
        $two_min_ago = time() - 60*2;
        $sendgift_list = M() -> query("select * from ss_coindetail c  join ss_member m ON m.id = c.uid where c.action = 'sendgift' and c.addtime > $two_min_ago order by c.addtime DESC limit $limit");
        //foreach($sendgift_list as $key => $value){
          //  $touser = M("member")->where("id = {$sendgift_list[$key]['touid']} ")->find();
            //$giftname = M("gift")->where("id = $sendgift_list[$key]['giftid']")->find();
        //}
        $str = "";
		if($sendgift_list != null){
			foreach ($sendgift_list as $key => $value) {
				$time = date("h:i", $value['addtime']);
				$str.="<li><a target='_blank' href='/{$value['curroomnum']}' style='color:#FFFFFF;'>{$time}&nbsp;{$value['content']}</em></a></li>";
			}
		}else{
			$str.="<li><a target='_blank' href='#' style='color:#FFFFFF;'>&nbsp;暂无礼物赠送详情</em></a></li>";
		}
        echo $str;
    }
}
