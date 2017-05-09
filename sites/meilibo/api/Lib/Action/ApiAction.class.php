<?php
class ApiAction extends BaseAction
{
    // 获取首页轮播
    public function rollpic()
    {
        $rollpics = D("Rollpic")->where("")->order('orderno')->select();
        $this->ajaxReturn($rollpics);
    }

    // 登录
    public function dologin()
    {
        $username = $_GET["tef"];
        $password = md5($_GET["jskda"]);
        $Member = D('Member');
        $arr = array('username'=>$username, 'password'=>$password);
        $user = $Member->where($arr)->select();
        
        if ($user) {
            // 写入本次登录时间及IP
            // 写入SESSION
            $_SESSION['uid'] = $user[0]['id'];
            session('username', $_POST["tef"]);
            setcookie('info', $_SESSION['uid'], time() * 3600 * 24 * 7);
            $json=json_encode(array('data' => '登陆成功'));
            echo $_GET['callback'] . "(" . $json . ")";
            exit;
        } else {
            $json = json_encode(array('data' => '登录失败'));
            echo $_GET['callback'] . "(" . $json . ")";
            exit;
        }
    }
    
    // 注册
    public function getuserid()
    {
        if (!isset($_SESSION['uid'])) {
            if (isset($_COOKIE['info'])) {
                $_SESSION['uid'] = $_COOKIE['info'];
                echo $_GET['callback'] . "(" . $json . ")";
                exit;
            }
            $json = json_encode('未登录');
            echo $_GET['callback'] . "(" . $json . ")";
            exit ;
        }
        $uid = $_SESSION['uid'];
        
        //$user = D('Member') -> where('id=' . $uid) -> field('id') -> find();
        $json = json_encode($uid); 
        echo $_GET['callback'] . "(" . $json . ")";

    }

    public function getuserinfo()
    {
        if (!isset($_SESSION['uid'])) {
            $json = json_encode(array('code' => 0, 'info' => '未登录'));
            echo $_GET['callback'] . "(" . $json . ")";
            exit ;
        }
        $uid = $_SESSION['uid'];
        $user = D('Member')->where('id=' . $uid)->find();
        $json = json_encode($user);
        echo $_GET['callback'] . "(" . $json . ")";

    }

    public function loginout()
    {
        unset($_SESSION['uid']);
        setcookie("info", '');
        echo $_GET['callback'] . '(' . json_encode(array('data' => '退出成功')) . ')';
    }

    // 粉丝(捧我的人)
    public function fans()
    {
        // $uid=$_REQUEST['uid']?$_REQUEST['uid']:'';
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $action = $_REQUEST['action'] ? $_REQUEST['action'] : '';
        
        if ($uid == '') {
            $uid = $_COOKIE['info'];
        } 
        if ($uid == '') {
            echo '用户不存在';
            exit;
        }
        if ($action == 'ls') {
            // 过去30天粉丝排行
            $monthfansrank = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and action="sendgift" and touid='.$uid.' and addtime>'.(time() - 2592000).' group by uid order by total desc LIMIT 5');
            foreach ($monthfansrank as $n => $val) {
                $monthfansrank[$n]['voo'] = D("Member")->where('id=' . $val['uid'])->select();
            }
            echo $_GET['callback'] . "(" . json_encode($monthfansrank) . ")";
            //$this->ajaxReturn($monthfansrank);
            exit;
        }
        if ($action == 'sp') {
            //超级粉丝排行
            $superfansrank = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and action="sendgift" and touid='.$uid.' group by uid order by total desc LIMIT 5');
            foreach ($superfansrank as $n => $val) {
                $superfansrank[$n]['voo'] = D("Member")->where('id=' . $val['uid'])->select();
            }
            if (!$superfansrank) {
                $this->ajaxReturn(array('data' => '0'));
                exit;
            } else {
                echo $_GET['callback'] . "(" . json_encode($superfansrank) . ")";
                exit;
            }
        }
    
        $data = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and action="sendgift" and touid=' . $uid . ' group by uid order by total desc ');
        
        $uids;
        foreach ($data as $v) {
            $uids .= $v['uid'] . ',';
        }
        $uids = substr($uids, 0, strlen($uids) - 1);
        $data = D('Member')->query("select id,nickname,sex  from ss_member where id in({$uids})");
        echo $_GET['callback'] . "(" . json_encode($data) . ")";
        exit;
    }

    public function show_headerInfo()
    {
        C('HTML_CACHE_ON', false);

        if ($_COOKIE['autoLogin'] == '1') {
            session('uid', $_COOKIE['userid']);
            session('username', $_COOKIE['username']);
            session('nickname', $_COOKIE["nickname"]);
            session('roomnum', $_COOKIE["roomnum"]);
        }

        if ($_SESSION['uid'] && $_SESSION['uid'] > 0) {
            $userinfo = D("Member") -> find($_SESSION['uid']);
            $this -> assign('userinfo', $userinfo);
        }
        $this -> display();

    }
    
    /*
     * 
     * 活动轮播api
     * */
     public function activityrollpic()
     {
         $rollpic = M("huodongrollpic")->select();
         $this->ajaxReturn($rollpic);
         
     }
     
     //活动分类
     public function huodongfenlei()
     {
         $fenlei = M("huodongfenlei")->select();
         $this->ajaxReturn($fenlei);
     }
     
     //获取活动分类下文章
     public function getactivityauthor()
     {
         $id = $_GET['aid'];
         $data = D('announce')->where('fid=' . $id)->select();
         $this->ajaxReturn($data); 
     }
     
     //获取文章具体内容
     public function getArticle()
     {
          $id = $_GET['aid'];
          $data = D('announce')->where('id=' . $id)->find();
          
          $this->ajaxReturn($data);
     }
     
     //注册账号
     public function register()
     {
         $name = isset($_GET['name']) ? $_GET['name'] : '';
         $password = isset($_GET['password']) ? $_GET['password'] : '';
         $confirm = isset($_GET['confirm']) ? $_GET['confirm'] : '';
         //$email=isset($_GET['email'])?$_GET['email']:'';
         $code = md5($_GET['code']);
        
        
        if ($_SESSION['verify'] != $code) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '验证码不正确')) . ')';
            exit;
        }
        if (count($name) < 6 or count($password) < 6 or count($confirm)) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '用户名密码长度不得小于六位')) . ')';
            exit;
        }
        if (empty($name) || empty($password) || empty($confirm)) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '信息填写不完整')) . ')';
            //$this->ajaxReturn('信息填写不完整');
            exit;
        }
        if ($password != $confirm) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '注册失败')) . ')';
            //$this->ajaxReturn('注册失败');
            exit;
        }
        $user = D('Member')->where('username=' . $name)->find();
        if ($user) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '用户已存在')) . ')';
            //$this->ajaxReturn('用户已存在');
            exit;
        }
        $name = addslashes($name);
        $password = addslashes($password);
        $data = D("Member")->add(array('username' => $name, 'password' => md5($password), 'regtime' => time()));
        if ($_SESSION['verify'] != $_GET['code']) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '验证码')) . ')';
            exit;
        }
        if ($data) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '注册成功')) . ')';
            // $this->ajaxReturn('注册成功');
            exit;
        }
        echo $_GET['callback'] . '(' . json_encode(array('data' => '注册失败')) . ')';
        // $this->ajaxReturn('注册失败');
        exit;
    }
    
     //获取礼物列表
    public function getgiflist()
    {
        $gifts = D("Gift")->where("")->order('sid asc,needcoin asc')->select();
        $json = json_encode($gifts);
        echo $_GET['callback'] . "(" . $json . ")";
        // $this->ajaxReturn($gifts);
    }
    
    //根据id获取用户
    public function getuserlist()
    {
        $userid = $_GET['users'];
        $data = D('Member')->where("id in(" . $userid . ")")->field("id,username,sex,spendcoin,vip,earnbean")->select();
        echo $_GET['callback'] . "(" . json_encode($data) . ")";     
    }
    
    //获取用户信息
    public function getusers()
    {
        $uid = $_REQUEST['uid'];
        if ($uid == '') {
            echo '{"code":"0"}';
            exit;
        } 
        
        //goodnum
        $goodnum = D("roomnum")->field('num')->where('uid=' . $uid)->find();
    
        $data = D("Member")->where('id=' . $uid)->field("id,username,sex,spendcoin,vip,earnbean")->find();
        //获取富豪等级id
        /*$richlevel=D("richlevel")->select();
        
        foreach($richlevel as $v){
            
            if($data['spendcoin']>$v['spendcoing_low']&&$data['spendcoin']<$v['spendcoing_up']){
                $data['richlevel']=$v['levelname'];
                unset($data['spendcoin']);echo 1;
            }
        }
        if(!isset($data['richlevel'])){
            $data['richlevel']=$richlevel[0]['levelname'];
            unset($data['spendcoin']);
        } */
        $richlevel = getRichlevel($data['spendcoin']);
        $emceelevel = getEmceelevel($data['earnbean']);
        unset($data['earnbean']);
        $data['level'] = $emceelevel[0]['levelid'];
        $data['richlevel'] = $richlevel[0]['levelid'];
        $data['goodnum'] = $goodnum;
        if (!isset($data)) {
            echo $_GET['callback'] . "(0)";
            //$this->ajaxReturn('0');
            exit;
        }
        
        $userinfo = null;
        $userinfo = D('Member')->where('id=' . $uid)->find();
        
        $myshowadmin = D("Roomadmin")->where('uid=' . $_GET['rid'] . ' and adminuid=' . $uid)->order('id asc')->select();
        if ($_SESSION['roomnum'] == $_GET['roomnum']) {
            $data['userType'] = 50;
        } else if($userinfo['showadmin'] == 1 || $myshowadmin) {
            $data['userType'] = 40;
        } else {
            $data['userType'] = 30;
        }
        
        echo $_GET['callback'] . "(" . json_encode($data) . ")";
        exit;
        // $this->ajaxReturn($data); 
    }
    
    public function show_sendGift()
    {
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }

        //获取用户信息
        $userinfo = D("Member")->find($_SESSION['uid']);
        //获取被赠送人信息
        $emceeinfo = D("Member")->find($_REQUEST['toid']);
        //根据gid获取礼物信息
        $giftinfo = D("Gift")->find($_REQUEST['gid']);
        if (empty($giftinfo)) {
            echo json_encode(array('code' => 1, 'info' => 'invalid params'));
            die;
        }
        $gidd = $_REQUEST['gid'];
        //判断虚拟币是否足够
        $needcoin = $giftinfo['needcoin'] * $_REQUEST['count'];
        $smallIcon = $giftinfo['gifticon_25'];

        $toUserNo = $_REQUEST['toid'];
        $kk =  $_REQUEST['kk'];
        if (trim($kk) != 'kc') {
            if ($userinfo['coinbalance'] < $needcoin) {
                $arr = array('code' => '1', 'info' => '余额不足');
                echo $_GET['callback'] . "(" . json_encode($arr) . ")";
                exit;
            }
        
            D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
            $verification = $_SESSION['uid'] . $_REQUEST['gid'] . $_REQUEST['count'] . $needcoin;
            //D("Member") ->where('id='.$_SESSION['uid'])->save(array('isdebit'=>$verification));
            D("Member")->execute("update ss_member set isdebit='{$verification}' where id={$_SESSION['uid']}");
            //记入虚拟币交易明细
            $Coindetail = D("Coindetail");
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'sendgift';
            $Coindetail->uid = $_SESSION['uid'];
            $Coindetail->touid = $_REQUEST['toid'];
            $Coindetail->giftid = $_REQUEST['gid'];
            $Coindetail->giftcount = $_REQUEST['count'];
            $Coindetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'] . ' 赠送礼物 ' . $giftinfo['giftname'] . ' ' . $_REQUEST['count'] . ' 个';
            //$smallIcon = str_replace('/50/','/25/',$giftinfo['gifticon']);
            //$smallIcon = str_replace('.png','.gif',$smallIcon);

            $Coindetail->objectIcon = $smallIcon;
            $Coindetail->coin = $needcoin;
            if ($emceeinfo['broadcasting'] == 'y') {
                $Coindetail->showId = $emceeinfo['showid'];
            }
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();
             // ($this->emceededuct / 100)
            //被赠送人加豆
            // $scale = D('Member')->getByid($songinfo['uid']); // 取出改主播的信息
            // if ($scale['sharingratio'] != '0') { // 优先按照指定的比例算
            if ($emceeinfo['agentuid'] != 0)
                $ratio = D('Member')->where('id='.$emceeinfo['agentuid'])->getField('id,familyratio,anchorratio');
                $ratio = $ratio[$songinfo[$emceeinfo['agentuid']]];
                $beannumAgent = ceil($needcoin * ($ratio['familyratio'] /100));
                $beannum = ceil($needcoin * ($ratio['anchorratio'] / 100));
                 // $beannum = ceil($needcoin * ($scale['sharingratio'] / 100));
            } else {//默认的比例
                $beannumAgent = 0;
                $beannum = ceil($needcoin * ($this->emceededuct / 100));
            }
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
                //D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$emceeinfo['agentuid']);
                D("Member")->execute('update ss_member set beanbalance2=beanbalance2+' . $beannumAgent . ' where id=' . $emceeinfo['agentuid']);
                $Emceeagentbeandetail = D("Emceeagentbeandetail");
                $Emceeagentbeandetail->create();
                $Emceeagentbeandetail->type = 'income';
                $Emceeagentbeandetail->action = 'getgift';
                $Emceeagentbeandetail->uid = $emceeinfo['agentuid'];
                $Emceeagentbeandetail->content = $userinfo['nickname'] . ' 向 ' . $emceeinfo['nickname'].' 赠送礼物 ' . $giftinfo['giftname'] . ' ' . $_REQUEST['count'] . ' 个';
                $Emceeagentbeandetail->bean = $beannumAgent;
                $Emceeagentbeandetail->addtime = time();
                $detailId = $Emceeagentbeandetail->add();
            }
            $arr = array('code' => '0', 'giftPath' => $smallIcon, 'giftStyle' => $giftinfo['giftstyle'], 'giftGroup' => $giftinfo['sid'], 'giftType' => $giftinfo['gifttype'], 'toUserNo' => $toUserNo, 'isGift' => '0', 'giftLocation' => '[]', 'giftIcon' => $giftinfo['gifticon'], 'giftSwf' => $giftinfo['giftswf'], 'toUserId' => $_REQUEST['toid'], 'toUserName' => $emceeinfo['nickname'], 'userNo' => $_SESSION['roomnum'], 'giftCount' => $_REQUEST['count'], 'userId' => $_SESSION['uid'], 'giftName' => $giftinfo['giftname'], 'userName' => $_SESSION['nickname'], 'giftId' => $giftinfo['id']);
            $json = json_encode($arr);
            echo $_GET['callback'] . "(" . $json . ")";
            exit;
        }else{

            include '/lib/action/daojuset.php';
        
            $arr = array('code' => '0', 'giftPath' => $smallIcon, 'giftStyle' => '豪华', 'giftGroup' => $giftinfo['sid'], 'giftType' => $giftinfo['gifttype'], 'toUserNo' => $emceeinfo['curroomnum'], 'isGift' => '0', 'giftLocation' => '[]', 'giftIcon' => $daojuicon[$gidd], 'giftSwf' => $daojuswf[$gidd], 'toUserId' => $_REQUEST['toid'], 'toUserName' => $emceeinfo['nickname'], 'userNo' => $_SESSION['roomnum'], 'giftCount' => '0', 'userId' => $_SESSION['uid'], 'giftName' => $gidd, 'userName' => $_SESSION['nickname'], 'giftId' => $gidd);
            $json = json_encode($arr);
            echo $_GET['callback'] . "(" . $json . ")";
            //echo '{"code":"0","giftPath":"'.$smallIcon.'","giftStyle":"'.'豪华'.'","giftGroup":"'.$giftinfo['sid'].'","giftType":"'.$giftinfo['gifttype'].'","toUserNo":"'.$emceeinfo['curroomnum'].'","isGift":"0","giftLocation":"[]","giftIcon":"'.$daojuicon[$gidd].'","giftSwf":"'.$daojuswf[$gidd].'","toUserId":"'.$_REQUEST['toid'].'","toUserName":"'.$emceeinfo['nickname'].'","userNo":"'.$_SESSION['roomnum'].'","giftCount":"'.'0'.'","userId":"'.$_SESSION['uid'].'","giftName":"'.$gidd.'","userName":"'.$_SESSION['nickname'].'","giftId":"'.$gidd.'"}';
            exit;
        }
    }
    
    //禁言验证用户权限api
    public function shutup()
    {
        //获取房主id
        $rid = isset($_GET['rid']) ? $_GET['rid'] : '';
        //获取房间id
        $roomid = isset($_GET['roomid']) ? $_GET['roomid'] : '';
        //根据roomid获取房间信息
        $room = M('roomnum')->where('num=' . $roomid)->find();
        // 获取操作者id
        $adminuid = isset($_GET['adminuid']) ? $_GET['adminuid'] : '';
        // 查询操作者是否是参数rid的房间管理
        $myshowadmin = D("Roomadmin")->where('uid=' . $rid . ' and adminuid=' . $adminuid)->order('id asc')->select();
       
        if ($rid == '' || $roomid == '') {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '参数不正确')) . ")";
            //echo '{"code":"1","info":"参数不正确"}';
            exit;
        }
            
        // 判断值是否有为负数的参数
        if ($rid < 0 || $roomid < 0 || $adminuid < 0) {
             echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '参数不能为负数')) . ")";
            //echo '{"code":"1","info":"参数不能为负数"}';
            exit;
        }

        if ($rid == $_GET['uidlist']) {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '不能对主播操作')) . ")";
            //echo '{"code":"1","info":"不能对主播操作"}';
            exit;
        }
        
        if ($adminuid == $_GET['uidlist']) {
             echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '不能对自己操作')) . ")";
            // echo '{"code":"1","info":"不能对自己操作"}';
            exit;
        }
        
        // 判断参数rid是否为参数roomid房间的房主
        if ($room['uid'] != $rid) {
             echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '房间id和rid不匹配')) . ")";
            //echo '{"code":"1","info":"房间id和rid不匹配"}';
            exit;
        }
        
        // 判断操作者是否为房间管理员
        if ($myshowadmin) {
            $this->permissions();
            exit;
        }
        
        // 判断操作者是否为房间主播(房主)
        if ($rid == $adminuid) {
            $this->permissions();
            exit;
        }
        
        $ruser = D("Member")->where('id=' . $adminuid)->find();
        
        // 判断操作者是否是系统管理员
        if ($ruser['showadmin'] == '1') {
            $this->permissions();
            // 判断操作者是否是vip
        } else if ($ruser['vip'] == '1') {
            $this->permissions();
        } else {
             echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '权限不足')) . ")";
            //echo '{"code":"权限不足"}';
        }
    }
    
    public function permissions()
    {
        //获取用户信息
        $userinfo = D("Member")->find($_REQUEST['uidlist']);
        if ($userinfo) {
            if ($userinfo['showadmin'] == '1') {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是系统管理员不能禁言')) . ")";
                // echo '{"code":"1","info":"对方是系统管理员不能禁言"}';
                exit;
            }
            
            if ($_REQUEST['uidlist'] == $_REQUEST['rid']) {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是主播不能禁言')) . ")";
                // echo '{"code":"1","info":"对方是主播不能禁言"}';
                exit;
            }
            
            $myshowadmin = D("Roomadmin")->where('uid=' . $_REQUEST['rid'] . ' and adminuid=' . $_REQUEST['uidlist'])->order('id asc')->select();
            if ($myshowadmin) {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是管理员不能禁言')) . ")";
                //echo '{"code":"1","info":"对方是管理员不能禁言"}';
                exit;
            }
            
            if ($userinfo['vip'] > 0 && $userinfo['vipexpire'] > time()) {
                if ($userinfo['vip'] == 1) {
                    echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是VIP不能禁言')) . ")";
                    //echo '{"code":"1","info":"对方是VIP不能禁言"}';
                    exit;
                }
                if ($userinfo['vip'] == 2) {
                    if ($_SESSION['uid'] == $_REQUEST['rid']) {
                        echo $_GET['callback'] . "(" . json_encode(array('code' => '0')) . ")";
                        //echo '{"code":"0"}';
                        exit;
                    } else {
                        echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是VIP不能禁言')) . ")";
                        //echo '{"code":"1","info":"对方是VIP不能禁言"}';
                        exit;
                    }
                }
            } else {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '0')) . ")";
                //echo '{"code":"0"}';
                exit;
            }
        } else {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '找不到该用户')) . ")";
            //echo '{"code":"1","info":"找不到该用户"}';
            exit;
        }
    }
    
    // 获取主播id根据roomid
    public function getrid()
    {
        $roomid = $_GET['roomid'];
        $rid = D('Member')->where('curroomnum=' . $roomid)->find();
        echo $_GET['callback'] . "(" . json_encode(array('rid' => $rid['id'])) . ")";
    }
    
    // 恢复发言权限认证
    public function restore()
    {
        // 获取操作者id
        $adminuid = isset($_GET['adminuid']) ? $_GET['adminuid'] : '';
        // 获取房间id
        $roomnum = isset($_GET['roomid']) ? $_GET['roomid'] : '';
        // 获取主播id
        $rid = isset($_GET['rid']) ? $_GET['rid'] : '';
        $room = D('roomnum')->where('num=' . $roomnum)->find();
    
        // 查询uid是否是rid的管理员
        $myshowadmin = D("Roomadmin")->where('uid=' . $rid . ' and adminuid=' . $adminuid)->find();
        if ($rid == '' || $roomnum == '' || $adminuid == '') {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '参数不正确')) . ")";
            //echo '{"code":"1","info":"参数不正确"}';
            exit;
        }
        if ($adminuid < 0 || $roomnum < 0 || $rid < 0) {
            echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'操作失败')).")";
            //echo '{"code":"1","info":"操作失败"}';
            exit;
        }
        if ($rid == $_REQUEST['uidlist']) {
             echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '不能对主播操作')) . ")";
            //echo '{"code":"1","info":"不能对主播操作"}';
            exit;
        }
        if ($adminuid == $_GET['uidlist']) {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '不能对自己操作')) . ")";
            //echo '{"code":"1","info":"不能对自己操作"}';
            exit;
        }
        if ($room['uid'] != $rid) {
            // echo '{"code":"1","info":"房间id和rid不匹配"}';
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '房间id和rid不匹配')) . ")";
            exit;
        }
        if ($adminuid == $rid) {
            $this->restorepermissions();
            exit;
        }
        $user = D('Member')->where('id=' . $adminuid)->find();
        if ($user['showadmin'] == '1') {
            $this->restorepermissions();
        } else if ($user['vip'] == '1') {
            $this->restorepermissions();
        } else if ($myshowadmin) {
            $this->restorepermissions();
        } else {
            echo '{"code":"1","info":"权限不足"}';
            exit;
        }
    }
    
    public function restorepermissions()
    {
        // 获取用户信息
        $userinfo = D("Member")->find($_REQUEST['uidlist']);
        if ($userinfo) {
            if ($userinfo['showadmin'] == '1') {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是系统管理员不能操作')) . ")";
                //echo '{"code":"1","info":"对方是系统管理员不能操作"}';
                exit;
            }
            if ($_REQUEST['uidlist'] == $_REQUEST['rid']) {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是主播不能操作')) . ")";
                //echo '{"code":"1","info":"对方是主播不能操作"}';
                exit;
            }
            $myshowadmin = D("Roomadmin")->where('uid=' . $_REQUEST['rid'] . ' and adminuid=' . $_REQUEST['uidlist'])->order('id asc')->select();
            if ($myshowadmin) {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是管理员不能操作')) . ")";
                //echo '{"code":"1","info":"对方是管理员不能操作"}';
                exit;
            }
            if ($userinfo['vip'] > 0 && $userinfo['vipexpire'] > time()) {
                if ($userinfo['vip'] == 1) {
                    echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是VIP不能操作')) . ")";
                    //echo '{"code":"1","info":"对方是VIP不能操作"}';
                    exit;
                }
                if ($userinfo['vip'] == 2) {
                    if ($_SESSION['uid'] == $_REQUEST['rid']) {
                        echo $_GET['callback'] . "(" . json_encode(array('code' => '0')) . ")";
                        //echo '{"code":"0"}';
                        exit;
                    } else {
                        echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '对方是VIP不能操作')) . ")";
                        //echo '{"code":"1","info":"对方是VIP不能操作"}';
                        exit;
                    }
                }
            } else {
                echo $_GET['callback'] . "(" . json_encode(array('code' => '0')) . ")";
                //echo '{"code":"0"}';
                exit;
            }
        } else {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '找不到该用户')) . ")";
            //echo '{"code":"1","info":"找不到该用户"}';
            exit;
        }
    }
    
    public function kick()
    {
        // 获取rid
        $rid = isset($_GET['rid']) ? $_GET['rid'] : '';
        // 获取房间id
        $roomnum = isset($_GET['roomid']) ? $_GET['roomid'] : '';
        // 操作者id
        $adminuid = isset($_GET['adminuid']) ? $_GET['adminuid'] : '';
        
        $room = D('roomnum')->where('num=' . $roomnum)->find();
        $myshowadmin = D("Roomadmin")->where('uid=' . $rid . ' and adminuid=' . $adminuid)->find();
        if ($rid == '' || $roomnum == '' || $adminuid == '') {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => $rid . '-' . $roomnum . '-' . $adminuid)) . ")";
            // echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'参数不能为空')).")";
            // echo '{"code":"1","info":"参数不能为空"}';
            exit;
        }    
        // 判断值是否有为负数的参数
        if ($rid < 0 || $roomnum < 0 || $adminuid < 0) {
             echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '参数不能为负数')) . ")";
            //echo '{"code":"1","info":"参数不能为负数"}';
            exit;
        }
        if ($rid == $_GET['uidlist']) {
            echo '{"code":"1", "info":"不能对踢主播"}';
            exit;
        }
        if ($adminuid == $_GET['uidlist']) {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '不能对自己操作')) . ")";
            //echo '{"code":"1","info":"不能对自己操作"}';
            exit;
        }
        if ($room['uid'] != $rid) {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '房间id和rid不匹配')) . ")";
            //echo '{"code":"1","info":"房间id和rid不匹配"}';
            exit;
        }
        if ($rid == $adminuid) {
            $this->kickpermissions();
            exit;
        }
        $user = D('Member')->where('id=' . $adminuid)->find();
        if ($user['showadmin'] == '1') {
            $this->kickpermissions();
        } else if ($user['vip'] == '1') {
            $this->kickpermissions();
        } else if ($myshowadmin) {
            $this->kickpermissions();
        } else {
            echo $_GET['callback'] . "(" . json_encode(array('code' => '1', 'info' => '权限不足')) . ")";
            //echo '{"code":"1","info":"权限不足"}';
        }
    }
    
    public function kickpermissions()
    {
        //获取用户信息
        $userinfo = D("Member") -> find($_REQUEST['uidlist']);
        if ($userinfo) {
            if ($userinfo['showadmin'] == '1') {
                echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'对方是系统管理员不能踢出')).")";
                //echo '{"code":"1","info":"对方是系统管理员不能踢出"}';
                exit ;
            }
            if ($_REQUEST['uidlist'] == $_REQUEST['rid']) {
                echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'对方是主播不能踢出')).")";
                //echo '{"code":"1","info":"对方是主播不能踢出"}';
                exit ;
            }
            $myshowadmin = D("Roomadmin") -> where('uid=' . $_REQUEST['rid'] . ' and adminuid=' . $_REQUEST['uidlist']) -> order('id asc') -> select();
            if ($myshowadmin) {
                echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'对方是管理员不能踢出')).")";
                //echo '{"code":"1","info":"对方是管理员不能踢出"}';
                exit ;
            }
            if ($userinfo['vip'] > 0 && $userinfo['vipexpire'] > time()) {
                if ($userinfo['vip'] == 1) {
                    echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'对方是VIP不能踢出')).")";
                    //echo '{"code":"1","info":"对方是VIP不能踢出"}';
                    exit ;
                }
                if ($userinfo['vip'] == 2) {
                    if ($_SESSION['uid'] == $_REQUEST['rid']) {
                        echo $_GET['callback']."(".json_encode(array('code'=>'0')).")";
                        //echo '{"code":"0"}';
                        exit ;
                    } else {
                        echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'对方是VIP不能踢出')).")";
                        //echo '{"code":"1","info":"对方是VIP不能踢出"}';
                        exit ;
                    }
                }
            } else {
                echo $_GET['callback']."(".json_encode(array('code'=>'0')).")";
                //echo '{"code":"0"}';
                exit ;
            }
        } else {
            echo $_GET['callback']."(".json_encode(array('code'=>'1','info'=>'找不到该用户')).")";
           //echo '{"code":"1","info":"找不到该用户"}';
            exit ;
        }
    }
    
    public function getuserinfo2()
    {
        $uid = $_SESSION['uid'];
        $data;
        $data['fensi'] = D("attention")->field('attuid')->where('uid=' . $uid)->select();
        if ($data['fensi']) {
            $guanzhu = D("attention")->field("uid")->where("attuid=" . $uid)->select();
            if ($guanzhu) {
                $data['guanzhu'] = count($guanzhu[0]);
            }
        } else {
            $data['guanzhu'] = 0;
            echo $_GET['callback'] . "(" . json_encode(array('data' => '0')) . ")";
            exit;
        }
        
        echo  $_GET['callback'] . "(" . json_encode(array('data' => $data)) . ")";
        //$this->ajaxReturn($data);
    }
    
    //修改资料
    public function edituserinfo()
    {
        $name = $_GET['name'];
        $sex = $_GET['sex'];
        $interest = $_GET['interest'];
        $birthday = $_GET['birthday'];
        $arr = array($name, $sex, $interest, $birthday);
        
        for ($i = 0; $i < 3; $i++) {
            if (empty($arr[$i])) {
                echo $_GET['callback'] . '(' . json_encode(array('data' => '有为空的选项')) . ')';
                exit;
            }
            $arr[$i] = addslashes($arr[$i]);
        }
        $result = D("Member")->where('id=' . $_SESSION['uid'])->save(array('nickname'=>$name,'sex'=>$sex,'interest'=>$interest,'birthday'=>$birthday));
        if ($result) {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '修改成功')) . ')';
        } else {
            echo $_GET['callback'] . '(' . json_encode(array('data' => '修改失败')) . ')';
        }
    }
    
    public function rand()
    {   
        //随机修改离线视频
        $arr = D("Member")->field("id")->select();
        
        foreach ($arr as $v) {
            $num = rand(1,3);
            $sql = "update ss_member set offlinevideo='http://video.meilibo.net/552{$num}.flv' where id={$v['id']}";
            // echo $sql;
            D("Member")->execute($sql);
        }
    }
    
}
