<?php
class loginAction extends Action
{

    public function qq()
    {
        $referer = $_SERVER['HTTP_REFERER'];
        session('login_referer', $referer);
        import('ORG.API.qqConnectAPI');
        $qc1 = new QC();
        $qc1->qq_login();
    }

    public function qqCallback()
    {
        import('ORG.API.qqConnectAPI');
        $qc = new QC();
        $token = $qc->qq_callback();
        $openid = $qc->get_openid();
        $qq = new QC($token, $openid);
        $arr = $qq->get_user_info();
        $useropenid = D('Member')->where('qopenid=' . "'" . $openid . "'")->select();
        $url = session('login_referer');

        if ($useropenid) {
            D('member')->where('id=' . $useropenid[0]['id'])->setField('lastlogtime', time());
            D('member')->where('id=' . $useropenid[0]['id'])->setField('lastlogip', get_client_ip());
            session('uid', $useropenid[0]['id']);
            session('ucuid', $useropenid[0]['ucuid']);
            session('username', $useropenid[0]['username']);
            session('nickname', $useropenid[0]['nickname']);
            session('roomnum', $useropenid[0]['curroomnum']);
            cookie('userid', $useropenid[0]['id'], 3600000);
            cookie('username', $useropenid[0]['username'], 3600000);
            cookie('nickname', $useropenid[0]['nickname'], 3600000);
            cookie('roomnum', $useropenid[0]['curroomnum'], 3600000);
            if (!($url == 'http://net.cn/index.php/Passport/usercenter/')) {
                header("Location:$url");
            } else {
                header('Location:http://net.cn');
            }
        } else {
            include './config.inc.php';
            include './uc_client/client.php';
            $data['nickname'] = $arr['nickname'];
            $data['password'] = md5('12221g0y');
            $regt = time();
            $email = $regt . "@163.com";//邮箱
            $uid = uc_user_register($arr['nickname'], $data['password'], $email);
            
            do {
                $roomnum = rand(10000000, 39999999);
            } while (checkIt($roomnum) == '');

            $data['regtime'] = time();
            $data['qopenid'] = $openid;
            $data['username'] = $roomnum;
            $data['curroomnum'] =$roomnum;
            $data['ucuid'] =$uid;
            
            if (D('Member')->add($data)) {
                $useropenid = D('Member')->where('qopenid=' . "'" . $openid . "'")->select();
                session('uid', $useropenid[0]['id']);
                session('ucuid', $useropenid[0]['ucuid']);
                session('username', $useropenid[0]['username']);
                session('nickname', $useropenid[0]['nickname']);
                session('roomnum', $useropenid[0]['curroomnum']);
                cookie('userid', $useropenid[0]['id'],3600000);
                cookie('username', $useropenid[0]['username'],3600000);
                cookie('nickname', $useropenid[0]['nickname'],3600000);
                cookie('roomnum', $useropenid[0]['curroomnum'],3600000);

                if (!($url == 'http://net.cn/index.php/Passport/usercenter/')) {
                    header("Location:$url");
                } else {
                    header('Location:http://net.cn');
                }
             } else {
                 echo '未知错误,稍后重试';
             }
        }
    }

}
