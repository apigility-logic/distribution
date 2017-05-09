<?php

class GuardAction extends BaseAction
{

    public function index()
    {
        if (!isset($_SESSION['uid']) || !isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您没有登录，请登录');
        } else {
            $current_room_id = intval($_GET['id']);
            $condition = array(
                'curroomnum' => $current_room_id,
            );
            //守护
            $condition = array(
                'anchorid' =>  M('Member')->where($condition)->getField('id'),
                'maturitytime' => array('gt', time()),
                'userid' => $_SESSION['uid'],
            );
            $guard = D('guard')->where($condition)->find();
            if (!empty($guard)) { //如果查询到该信息.
                $expired_time =  date('Y-m-d', $guard['maturitytime']);
            } else {
                $expired_time = null;
            }
            $this->assign('showid', $_GET['id']);
            $this->assign('expired_time', $expired_time);
            $this->display();
        }
    }
    public function buyTool()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo '{"msg":"请重新登录"}';
            exit;
        }
        $userinfo = M('Member')->find($_SESSION['uid']);
        if ($_GET['toolsubid'] == 1) {
            $needcoin = 20000;
            $duration = 3600 * 24 * 30 * 1;
            $duration1 = "1个月";
        } elseif ($_GET['toolsubid'] == 2) {
            $needcoin = 40000;
            $duration = 3600 * 24 * 30 * 3;
            $duration1 = "3个月";
        } elseif ($_GET['toolsubid'] == 3) {
            $needcoin = 70000;
            $duration = 3600 * 24 * 30 * 6;
            $duration1 = "6个月";
        } elseif ($_GET['toolsubid'] == 4) {
            $needcoin = 130000;
            $duration = 3600 * 24 * 30 * 12;
            $duration1 = "12个月";
        }else if ($_GET['toolsubid'] == 5) {
            $needcoin = 20000;
            $duration = 3600 * 24 * 30 * 1;
            $duration1 = "1个月";
        } elseif ($_GET['toolsubid'] == 6) {
            $needcoin = 40000;
            $duration = 3600 * 24 * 30 * 3;
            $duration1 = "3个月";
        } elseif ($_GET['toolsubid'] == 7) {
            $needcoin = 70000;
            $duration = 3600 * 24 * 30 * 6;
            $duration1 = "6个月";
        } elseif ($_GET['toolsubid'] == 8) {
            $needcoin = 130000;
            $duration = 3600 * 24 * 30 * 12;
            $duration1 = "12个月";
        }

        // 守护限制20人
        $anchorinfo =  D('Member')->where('curroomnum=' . $_GET['toolid'])->find();
        $anchor_state = M("guard")->where("anchorid = {$anchorinfo['id']} and userid = {$_SESSION['uid']}")->find();
        // 如果现在不是守护状态执行
        if ($anchor_state == null || $anchor_state['maturitytime'] < time()) {
            $count =  D('guard')->where("anchorid = {$anchorinfo['id']}")->count();//查询已守护
            if ($count >= 20) {
                echo '{"msg":"当前守护人数已满！"}';
                exit;
            }
        }


        if($userinfo['coinbalance'] < $needcoin){
        	echo '{"msg":"余额不足,请充值"}';
        	exit;
        }else{
            if ($anchorinfo['agentuid'] != 0) {
                $ratio = D('Agentfamily')->where('uid='.$anchorinfo['agentuid'])->getField('uid,familyratio,anchorratio');
                $ratio = $ratio[$anchorinfo['agentuid']];
                $beannumAgent = ceil($needcoin * ($ratio['familyratio'] /100));
                $beannum = ceil($needcoin * ($ratio['anchorratio'] / 100));
            } else {
                $beannumAgent = 0;
                $beannum = ceil($needcoin * ($this->emceededuct / 100));
            }
            // $data['']
            D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
            $userinfo =M ('Member')->where('id=' . $_SESSION['uid'])->find();
            if ($userinfo['daoju9expire'] < time()){
                D('Member')->execute('update ss_member set daoju9="y",daoju9expire=' . (time() + $duration) . ' where id=' . $_SESSION['uid']);
            } else {
                D('Member')->execute('update ss_member set daoju9="y",daoju9expire=daoju9expire+' . $duration . ' where id=' . $_SESSION['uid']);
            }
            D('Member')->execute('update ss_member set beanbalance=beanbalance+' . $beannum . ' where curroomnum=' . $_GET['toolid']);
            D('Member')->execute('update ss_member set beanbalance2=beanbalance2+' . $beannumAgent . ' where id='.$anchorinfo['agentuid']);

            $condition['anchorid'] = $anchorinfo['id'];
            $condition['userid'] = $_SESSION['uid'];
            $condition['_logic'] = "and";
            if ($guardinfo = M('guard')->where($condition)->select()) {
                if (time() < $guardinfo[0]['maturitytime']) {
                    D('guard')->execute('update ss_guard set maturitytime=maturitytime+'.$duration. ' where anchorid='.$anchorinfo['id']);
                    //写入消费明细
                    $Coindetail = D("Coindetail");
                    $Coindetail->create();
                    $Coindetail->type = 'expend';
                    $Coindetail->action = 'buy';
                    $Coindetail->uid = $_SESSION['uid'];
                    $Coindetail->giftcount = 1;
                    $Coindetail->content = '您购买了 ' . $duration1 . ' 守护';
                    $Coindetail->objectIcon = '/style/images/shou.png';
                    $Coindetail->coin = $needcoin;
                    $Coindetail->addtime = time();
                    $detailId = $Coindetail->add();
                } else {
                    D('guard')->execute('update ss_guard set maturitytime=' . time() + $duration . ' where anchorid=' . $_GET['toolid']);
                    // 写入消费明细
                    $Coindetail = D("Coindetail");
                    $Coindetail->create();
                    $Coindetail->type = 'expend';
                    $Coindetail->action = 'buy';
                    $Coindetail->uid = $_SESSION['uid'];
                    $Coindetail->giftcount = 1;
                    $Coindetail->content = '您购买了 ' . $duration1 . ' 守护';
                    $Coindetail->objectIcon = '/style/images/shou.png';
                    $Coindetail->coin = $needcoin;
                    $Coindetail->addtime = time();
                    $detailId = $Coindetail->add();
                }
            } else {
                // echo '{"msg":"xx44444xxx"}';
                $data['cleartime'] = time();
                $data['maturitytime'] = time() + $duration;
                $data['anchorid'] = $anchorinfo['id'];
                $data['userid'] = $_SESSION['uid'];
                D('guard')->add($data);
                // 写入消费明细
                $Coindetail = D("Coindetail");
                $Coindetail->create();
                $Coindetail->type = 'expend';
                $Coindetail->action = 'buy';
                $Coindetail->uid = $_SESSION['uid'];
                $Coindetail->giftcount = 1;
                $Coindetail->content = '您购买了 ' . $duration1 . '守护';
                $Coindetail->objectIcon = '/style/images/shou.png';
                $Coindetail->coin = $needcoin;
                $Coindetail->addtime = time();
                $detailId = $Coindetail->add();
            }


            include_once realpath(__DIR__ . '/../') . '/GatewayClient/Gateway.php';
            include APP_PATH . '/config.inc.php';
            Gateway::$registerAddress = $register_address;
            $icon = '/style/avatar/' . substr(md5($_SESSION['uid']),0,3) . '/' . $_SESSION['uid'] . '_middle.jpg';
            Gateway::sendToGroup($_GET['toolid'], json_encode(array('type'=>'takeGuard','icon'=>$icon,'nickname'=>$_SESSION['nickname'],'')));
            echo '{"msg":"购买成功"}';
        }
    }
}
