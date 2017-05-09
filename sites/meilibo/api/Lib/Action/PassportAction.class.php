<?php
class PassportAction extends BaseAction
{
    protected $mem_host = "127.0.0.1";
    
    protected $mem_port = "11211";
    public function usercenter()
    {
        C('HTML_CACHE_ON', false);
        $this->display();
    }
	
	public function checkLogin(){  
		C('HTML_CACHE_ON', false);
        include './config.inc.php';
		$phone = $_POST["phone"];
		$captcha = $_POST["captcha"];
        $url = 'http://'.$_SERVER["SERVER_NAME"]."/OpenAPI/V1/SMS/verify?phone=".$phone."&captcha=".$captcha;
		$data = curlGet($url);
		$data = json_decode($data,true);
		if($data['msg'] == "ok"){
				$userinfo = D("Member")->where('username="' . $data['data']["username"].'"')->find();
                if ($userinfo['isaudit'] == 'n' || $userinfo['isdelete'] == 'y') {
                    $data["code"] = "001009";
                    echo json_encode($data);
                    exit;
                }else{
                    session('uid', $userinfo['id']);
                    session('username', $userinfo['username']);
                    session('nickname', $userinfo['nickname']);
                    session('roomnum', $userinfo['curroomnum']);
                    cookie('userid',  $userinfo['id'], 2500000);
                    cookie('username',  $userinfo['username'], 2500000);
                    cookie('nickname',  $userinfo['nickname'], 2500000);
                    cookie('roomnum',  $userinfo['curroomnum'], 2500000);
                    $token = md5('stringSalt' .  $userinfo['id'] . rand(0,99999));
                    cookie('token', $token, 2500000);
                    $mem = new Memcache();
                    if (!$mem->connect($mem_host, $mem_port) || !$mem->set($session_prefix .  $userinfo['id'] . $token , $token, 0, 2500000)|| !$mem->set($token, array('uid' =>  $userinfo[0]['id'], 'username' => $_REQUEST["userName"]) , 2500000 ) ) {
                        $data["code"] = "001020";
                        echo json_encode($data);
                    }
                    $data["code"] = "000000";
                    echo json_encode($data);
                }
		}else{
			$data["code"] = "001004";
			echo json_encode($data);
		}
	}
	
	public function dologin(){
        C('HTML_CACHE_ON', false);
        include './config.inc.php';
		$userinfo = D("Member")->where('username="' . $_REQUEST["userName"] . '"')->select(); 
		if (!$userinfo) {
                    //echo '数据暂无';
			exit;
		} else {
			if ($userinfo[0]['password'] !== md5($_REQUEST["password"])){
				echo '{"code":"001004"}';
				exit;
			}
			if ($userinfo[0]['isaudit'] == 'n' || $userinfo[0]['isdelete'] == 'y') {
				echo '{"code":"001009"}';
				exit;
			} else {
				D("Member")->where('id=' . $userinfo[0]['id'])->setField('lastlogtime', time());
				D("Member")->where('id=' . $userinfo[0]['id'])->setField('lastlogip', get_client_ip());
				session('uid', $userinfo[0]['id']);
				session('username', $_REQUEST["userName"]);
				session('nickname', $userinfo[0]['nickname']);
				session('roomnum', $userinfo[0]['curroomnum']);
				cookie('userid', $userinfo[0]['id'], 2500000);
				cookie('username', $_REQUEST["userName"], 2500000);
				cookie('nickname', $userinfo[0]['nickname'], 2500000);
				cookie('roomnum', $userinfo[0]['curroomnum'], 2500000);
				cookie('autoLogin', $_REQUEST['autoLogin'], 2500000);

                //$output = $this->getApi("OpenAPI/V1/User/loginSuccessResp",array('username' => $_REQUEST["userName"],'user_id' => $userinfo[0]['id']));
                $output = $this->getApi("OpenAPI/V1/User/loginnew",array('id'=>$userinfo[0]['id'],'username'=>$_REQUEST["userName"]));
                $data = json_decode($output,true);
                session('token', $data['data']['token']);
                cookie('token', $data['data']['token'], 2500000);
				echo '{"code":"000000","user":[{"userName":"' . $_REQUEST["userName"] . '","userId":"' . $userinfo[0]['id'] . '","nick":"' . $_REQUEST["userName"].'","token":"' . $data['data']['token'] . '","mem_token":"' . $data['data']['token'] .'"}]}';
				exit;
			}
		}
	}
	public function doreg()
    {
        C('HTML_CACHE_ON', false);
        if ($this->openreg != 'y') {
            echo '{"code":"001500","info":"暂不开放注册"}';
            exit;
        }
        if ($_SESSION['verify'] != md5($_GET['validateCode'])) {
            echo '{"code":"001001"}';
            exit;
        }
        include './config.inc.php';
        $user = D("Member")->where('username ="' . $_REQUEST['userName'] . '"')->select();
        if ($user) {
            echo '{"code":"001190","info":"用户已存在"}';
            exit;
        }
       
		$User = D("Member");
		$User->create();
		$User->username = $_GET['userName'];
		$User->nickname = $_GET['userName'];
		$User->password = md5($_GET['password']);
		$User->password2 = $this->pswencode($_GET['password']);
		$User->email = $_GET['email'];
		$User->isaudit = $this->regaudit;
		$User->regtime = time();
		$roomnum = 99999; 
		if(cookie("market_agent_uid") != ""){
			$marketDirector = M('Member')->find(cookie("market_agent_uid"));
			if (!empty($marketDirector)) {
				$User->marketuid = cookie("market_agent_uid");
				cookie("market_agent_uid",null);
				cookie("market_create_time",null);
			}
		}
		do {    
			$roomnum = rand(1000000000, 1999999999);   
		} while (checkIt($roomnum) == '');
		$User->curroomnum = $roomnum;
		$User->host = $this->defaultserver;
		$User->canlive = $this->canlive;
		$userId = $User->add();

		D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values(' . $userId . ',' . $roomnum . ',' . time() . ')');

		include APP_PATH.'/config.inc.php';
		$mem = new Memcache();
		$mem->connect($mem_host, $mem_port);
		$status = array(
			'owner'=>$userId,
			'adminer'=>array(),
			'kicked'=>array(),
			'disableMsg'=>array(),
			'maxonline'=>100,
			);
		$mem->set($room_status_prefix.$roomnum, json_encode($status));

		if ($this->regaudit == 'y') {
			D("Member")->where('id=' . $userId)->setField('lastlogtime', time());
			D("Member")->where('id=' . $userId)->setField('lastlogip', get_client_ip());
			session('uid', $userId);
			session('username', $_GET['userName']);
			session('nickname', $_GET['userName']);
			session('roomnum', $roomnum);
			cookie('userid', $userId, 2500000);
			cookie('username', $_REQUEST["userName"], 2500000);
			cookie('nickname', $_REQUEST["userName"], 2500000);
			cookie('roomnum', $roomnum, 2500000);
			cookie('autoLogin', '0', 2500000);
            
            $output = $this->getApi("OpenAPI/V1/User/loginnew",array('id'=>$userId,'username'=>$_GET['userName']));
            $data = json_decode($output,true);
            session('token', $data['data']['token']);
            cookie('token', $data['data']['token'], 2500000);
            echo '{"code":"000000","user":[{"userName":"' . $_REQUEST["userName"] . '","userId":"' . $userId . '","nick":"' . $_REQUEST["userName"].'","token":"' . $data['data']['token'] . '","mem_token":"' . $data['data']['token'] .'"}]}';
            exit;
			//$token = md5('stringSalt' . $userId . rand(0,99999));
			//cookie('token', $token, 2500000);
			// $mem = new Memcache();
			// if (!$mem->connect($mem_host, $mem_port) || !$mem->set($session_prefix . $userId . $token , $token, 0, 2500000)|| !$mem->set($token, array('uid' =>  $userinfo[0]['id'], 'username' => $_REQUEST["userName"]) , 2500000 ) ) {
			// 	echo '{"code":"0010020"}';
			// 	exit;
			// }

			// echo '{"code":"000000","userName":"' . $_GET['userName'] . '","userId":"' . $userId . '","nick":"' . $_GET['userName'] . '"}';
			// exit;
		} else {
			echo '{"code":"001190","info":"注册失败.."}';
			exit;
		}
    }
    public function dologin1()
    {
        C('HTML_CACHE_ON', false);
        /* if($_SESSION['verify'] != md5($_REQUEST['validateCode'])) {
            echo '{"code":"001001"}';
            exit;
        } */

        include './config.inc.php';
        //include './uc_client/client.php';
        //list($uid, $username, $password, $email) = uc_user_login($_REQUEST["userName"], $_REQUEST["password"]);
        //if ($uid > 0) {
            $userinfo = D("Member")->where('username="' . $_REQUEST["userName"] . '"')->select();
            if (!$userinfo) {
                $User=D("Member");
                $User->create();
                $User->username = $_REQUEST["userName"];
                $User->nickname = $_REQUEST["userName"];
                $User->password = md5($_REQUEST["password"]);
                $User->password2 = $this->pswencode($_REQUEST["password"]);
                $User->email = $email;
                $User->isaudit = $this->regaudit;
                $User->regtime = time();
                $roomnum = 99999;
                do {    
                    $roomnum = rand(1000000000, 9999999999);
                } while (checkIt($roomnum) == '');
                $User->curroomnum = $roomnum;
                //$User->ucuid = $uid;
                $User->host = $this->defaultserver;
                $User->canlive = $this->canlive;
                $userId = $User->add();

                D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values(' . $userId . ', ' . $roomnum . ',' . time() . ')');

                if ($this->regaudit =='n') {
                    echo '{"code":"001009"}';
                    exit;
                } else {
                    //å†™å…¥æœ¬æ¬¡ç™»å½•æ—¶é—´åŠIP
                    D("Member")->where('id=' . $userId)->setField('lastlogtime', time());
                    D("Member")->where('id=' . $userId)->setField('lastlogip', get_client_ip());
                    session('uid', $userId);
                    //session('ucuid', $uid);
                    session('username', $_REQUEST["userName"]);
                    session('nickname', $_REQUEST["userName"]);
                    session('roomnum', $roomnum);
                    cookie('userid', $userId, 2500000);
                    //cookie('ucuid', $uid, 2500000);
                    cookie('username', $_REQUEST["userName"], 2500000);
                    cookie('nickname', $_REQUEST["userName"], 2500000);
                    cookie('roomnum', $roomnum, 2500000);
                    cookie('autoLogin', '0', 2500000);


                    echo '{"code":"000000","user":[{"userName":"'.$_REQUEST["userName"].'","userId":"'.$userId.'","nick":"'.$_REQUEST["userName"].'"}]}';
                    exit;
                }
            } else {
				if ($userinfo[0]['password'] !== md5($_REQUEST["password"])){
					echo '{"code":"001004"}';
					exit;
				}
                if ($userinfo[0]['isaudit'] == 'n' || $userinfo[0]['isdelete'] == 'y') {
                    echo '{"code":"001009"}';
                    exit;
                } else {
                    D("Member")->where('id=' . $userinfo[0]['id'])->setField('lastlogtime', time());
                    D("Member")->where('id=' . $userinfo[0]['id'])->setField('lastlogip', get_client_ip());
                    session('uid', $userinfo[0]['id']);

                    session('username', $_REQUEST["userName"]);
                    session('nickname', $userinfo[0]['nickname']);
                    session('roomnum', $userinfo[0]['curroomnum']);
                    cookie('userid', $userinfo[0]['id'], 2500000);

                    cookie('username', $_REQUEST["userName"], 2500000);
                    cookie('nickname', $userinfo[0]['nickname'], 2500000);
                    cookie('roomnum', $userinfo[0]['curroomnum'], 2500000);
                    cookie('autoLogin', $_REQUEST['autoLogin'], 2500000);

                    echo '{"code":"0000001","user":[{"userName":"' . $_REQUEST["userName"] . '","userId":"' . $userinfo[0]['id'] . '","nick":"' . $_REQUEST["userName"] . '"}]}';
                    exit;
                }
            }
			/*
        } elseif ($uid == -1) {
            echo '{"code":"001004"}';
            exit;
        } elseif ($uid == -2) {
            echo '{"code":"001004"}';
            exit;
        } else {
            echo '{"code":"001004"}';
            exit;
        }
		*/
    }

    public function doreg1()
    {
        C('HTML_CACHE_ON', false);
        if ($this->openreg != 'y') {
            echo '{"code":"001500","info":"å½“å‰ç¦æ­¢æ³¨å†Œæ–°ç”¨æˆ"}';
            exit;
        }

        if ($_SESSION['verify'] != md5($_GET['validateCode'])) {
            echo '{"code":"001001"}';
            exit;
        }

        include './config.inc.php';
        //include './uc_client/client.php';
        $user = D("Member")->where('username ="' . $_REQUEST['userName'] . '"')->select();
        if ($user) {
            echo '{"code":"001190","info":"ç”¨æˆ·åé‡å¤}';
            exit;
        }
        //$uid = uc_user_register($_GET['userName'], $_GET['password'], $_GET['email']);
        /*if ($uid <= 0) {
            if ($uid == -1) {
                echo '{"code":"001190","info":"ç”¨æˆ·åä¸åˆæ³•"}';
                exit;
            } elseif ($uid == -2) {
                echo '{"code":"001190","info":"åŒ…å«ä¸å…è®¸æ³¨å†Œçš„è¯è¯­"}';
                exit;
            } elseif ($uid == -3) {
                echo '{"code":"001002"}';
                exit;
            } elseif ($uid == -4) {
                echo '{"code":"001190","info":"Email æ ¼å¼æœ‰è¯¯'.$_GET['email'].'"}';
                exit;
            } elseif ($uid == -5) {
                echo '{"code":"001190","info":"Email ä¸å…è®¸æ³¨å†}';
                exit;
            } elseif ($uid == -6) {
                echo '{"code":"001190","info":"è¯Email å·²ç»è¢«æ³¨å†}';
                exit;
            } else {
                echo '{"code":"001190","info":"æœªçŸ¥é”™è¯¯"}';
                exit;
            }
        } else {*/
            $User = D("Member");
            $User->create();
            $User->username = $_GET['userName'];
            $User->nickname = $_GET['userName'];
            $User->password = md5($_GET['password']);
            $User->password2 = $this->pswencode($_GET['password']);
            $User->email = $_GET['email'];
            $User->isaudit = $this->regaudit;
            $User->regtime = time();
            $roomnum = 99999; 

            if(cookie("market_agent_uid") != ''){
                $User -> marketuid = cookie("market_agent_uid");
                setcookie("market_agent_uid","");
                setcookie("market_create_time","");
            }

            //èŽ·å–æŽ¨èäº                       
            // $url = $_SERVER['HTTP_HOST'];            
            // $RecommendID = M("member")->where("RecommendID = 'baidu12.com'")->getField("id");            
            // $User->RecommendID = $RecommendID;


            do {    
                $roomnum = rand(1000000000, 1999999999);   
            } while (checkIt($roomnum) == '');
            $User->curroomnum = $roomnum;
            //$User->ucuid = $uid;
            $User->host = $this->defaultserver;
            $User->canlive = $this->canlive;
            $userId = $User->add();

            D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values(' . $userId . ',' . $roomnum . ',' . time() . ')');

            include APP_PATH.'/config.inc.php';
            $mem = new Memcache();
            $mem->connect($mem_host, $mem_port);
            $status = array(
                'owner'=>$userId,
                'adminer'=>array(),
                'kicked'=>array(),
                'disableMsg'=>array(),
                'maxonline'=>100,
                );
            $mem->set($room_status_prefix.$roomnum, json_encode($status));

            if ($this->regaudit == 'y') {
                //å†™å…¥æœ¬æ¬¡ç™»å½•æ—¶é—´åŠIP
                D("Member")->where('id=' . $userId)->setField('lastlogtime', time());
                D("Member")->where('id=' . $userId)->setField('lastlogip', get_client_ip());
                session('uid', $userId);
                //session('ucuid', $uid);
                session('username', $_GET['userName']);
                session('nickname', $_GET['userName']);
                session('roomnum', $roomnum);
                cookie('userid', $userId, 2500000);
                //cookie('ucuid', $uid, 2500000);
                cookie('username', $_REQUEST["userName"], 2500000);
                cookie('nickname', $_REQUEST["userName"], 2500000);
                cookie('roomnum', $roomnum, 2500000);
                cookie('autoLogin', '0', 2500000);

                $token = md5('stringSalt' . $userId . rand(0,99999));
                cookie('token', $token, 2500000);
                $mem = new Memcache();
                if (!$mem->connect($mem_host, $mem_port) || !$mem->set($session_prefix . $userId . $token , $token, 0, 2500000)|| !$mem->set($token, array('uid' =>  $userinfo[0]['id'], 'username' => $_REQUEST["userName"]) , 2500000 ) ) {
                    echo '{"code":"0010020"}';
                    exit;
                }

                echo '{"code":"000000","userName":"' . $_GET['userName'] . '","userId":"' . $userId . '","nick":"' . $_GET['userName'] . '"}';
                exit;
            } else {
                echo '{"code":"001190","info":"æ³¨å†ŒæˆåŠŸï¼Œç­‰å¾…ç®¡ç†å‘˜å®¡æ ¸"}';
                exit;
            }
        //}
    }

    public function findBackPwdPage()
    {
        C('HTML_CACHE_ON', false);
        $this->display();
    }

    public function findBackPwd()
    {
        C('HTML_CACHE_ON', false);
        $userinfo = D("Member")->where('username="' . $_REQUEST["userName"] . '"')->select();
        if ($userinfo) {
            if ($userinfo[0]['email'] != $_REQUEST["email"]) {
                echo '{"code":"000002","info":"é‚®ç®±ä¸åŒ¹é…}';
                exit;
            }
            include './config.inc.php';
            //include './uc_client/client.php';

            $userpassword = $this->pswdecode($userinfo[0]['password2']);
            //å‘é‚®ä»            $mailconfig = D('Mailconfig')->find(1);
            if (!$mailconfig) {
                echo '{"code":"000003","info":"é‚®ä»¶å‘é€å¤±è´}';
                exit;
            }
            $subject = $this->sitename."ä¼šå‘˜æ‰¾å›žå¯†ç ";
            $message = $mailconfig['fpasswd_mailtpl'];
            $find = array(
                '{$siteurl}',
                '{$sitelogo}',
                '{$useremail}',
                '{$username}',
                '{$userpassword}',
                '{$adminemail}',
                '{$sitename}'
            );
            $replace = array(
                $this->siteurl,
                $this->sitelogo,
                $userinfo[0]['email'],
                $userinfo[0]['username'],
                $userpassword,
                $this->adminemail,
                $this->sitename,
            );
            $message = str_replace($find, $replace, $message);
            //$res = uc_mail_queue($userinfo[0]['id'], $userinfo[0]['email'], $subject, $message);
            //if (empty($res)) {
            //    echo '{"code":"000003","info":"é‚®ä»¶å‘é€å¤±è´}';
            //    exit;
            //} else{
            //    echo '{"code":"000000"}';
            //    exit;
            //}
        } else {
            echo '{"code":"000001","info":"æ‰¾ä¸åˆ°è¯¥ç”¨æˆ·å}';
            exit;
        }
    }

    public function findBackPwdSuccess()
    {
        C('HTML_CACHE_ON', false);
        $this->display();
    }

    public function logout()
    {
        if (!isset($_SESSION['uid']) || !$_SESSION['uid']) {
            redirect('/index.php');
        }
        $userId = $_SESSION['uid'];
        include APP_PATH.'/config.inc.php';

        cookie('token', null);

        C('HTML_CACHE_ON', false);
        session('uid', null);
        //session('ucuid', null);
        session('username', null);
        session('nickname', null);
        session('roomnum', null);
        cookie('userid', null, 2500000);
        //cookie('ucuid', null, 2500000);
        cookie('username', null, 2500000);
        cookie('nickname', null, 2500000);
        cookie('autoLogin', null, 2500000);
        cookie('roomnum', null, 2500000);

        if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'redirect') {
            redirect('/index.php');
        } else {
            echo "data='';";
            exit;
        }

        session_destroy();
    }

    public function checkusername()
    {
        C('HTML_CACHE_ON', false);
        $user = D("Member")->where('username ="' . $_REQUEST['userName'] . '"')->select();
        if ($user) {
            echo '1';
            exit;
        } else {
            foreach ($this->denyusername as $k) {
                if ($k == "") {
                    continue;
                }
                if (strstr($_REQUEST['userName'], $k) != '') {
                    echo '1';
                    exit;
                }
            }
            echo '0';
            exit;
        }
    }

    public function checkemail()
    {
        C('HTML_CACHE_ON', false);
        $user = D("Member")->where('email ="' . trim(str_replace('%40', '@', $_REQUEST['email'])) . '"')->select();
        if ($user) {
            echo '1';
            exit;
        } else {
            echo '0';
            exit;
        }
    }

    public function resSuccess()
    {
        $this->display();
    }
}