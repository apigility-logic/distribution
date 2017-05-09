<?php
class shopAction extends BaseAction
{

    public function getroominfo()
    {
        C('HTML_CACHE_ON', false);
        header('Content-Type: text/xml');
        $roominfo = D("Member")->where('curroomnum=' . $_GET["roomnum"] . '')->select();
        if ($roominfo) {
            if ($roominfo[0]['fakeuser'] == 'y') {
                $body = file_get_contents('http://xiu.56.com/api/userFlvApi.php?room_user_id=' . $roominfo[0]['56_room_user_id']);
                if (strstr($body, "status=1")) {
                    echo '<?xml version="1.0" encoding="UTF-8"?>';
                    echo '<ROOT>';
                    echo '<broadcasting>yy</broadcasting>';
                    $bodyArray = explode("%3D", $body);
                    $bodyArray2 = explode("&", $bodyArray[1]);
                    $token = $bodyArray2[0];
                    echo '<token>' . $token . '</token>';
                    echo '</ROOT>';
                } else {
                    echo '<?xml version="1.0" encoding="UTF-8"?>';
                    echo '<ROOT>';
                    echo '<broadcasting>n</broadcasting>';
                    echo '<offlinevideo></offlinevideo>';
                    echo '</ROOT>';
                }
            } else {
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<ROOT>';
                echo '<broadcasting>' . $roominfo[0]['broadcasting'] . '</broadcasting>';
                if ($roominfo[0]['broadcasting'] == 'y') {
                    $roomtype = $roominfo[0]['roomtype'];
                    if ($roomtype == 1) {
                        if ($_SESSION['enter_' . $roominfo[0]['showid']] == 'y') {
                            $roomtype = 0;
                        }
                    }
                    if ($roomtype == 2) {
                        if ($_SESSION['enter_' . $roominfo[0]['showid']] == 'y') {
                            $roomtype = 0;
                        }
                    }
                    // 判断是否VIP以及金钥匙
                    $viewerinfo = D("Member")->find($_SESSION['uid']);
                    if ($roominfo[0]['online'] >= $roominfo[0]['maxonline']) {
                        if (((int)$viewerinfo['vip'] > 0 && $viewerinfo['vipexpire'] > time()) || ($viewerinfo['goldkey'] == 'y' && $viewerinfo['gkexpire'] > time())) {
                        } else {
                            $roomtype = 3;
                        }
                    }
                    if ($_SESSION['uid'] == $roominfo[0]['id']) {
                        $roomtype = 0;
                    }
                    if ($viewerinfo['showadmin'] == '1') {
                        $roomtype = 0;
                    }
                    echo '<roomtype>' . $roomtype . '</roomtype>';
                } else {
                    echo '<offlinevideo>' . $roominfo[0]['offlinevideo'] . '</offlinevideo>';
                }
                echo '</ROOT>';
            }
        } else {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<ROOT>';
            echo '</ROOT>';
        }
    }

    public function getuserinfo()
    {
        C('HTML_CACHE_ON', false);
        header('Content-Type: text/xml');
        if (!isset($_SESSION['uid'])) {
            $userid = rand(1000, 9999);
            $_SESSION['uid'] = -$userid;
        }
        $roominfo = D("Member")->where('curroomnum=' . $_GET["roomnum"] . '')->select();
        $roomrichlevel = getRichlevel($roominfo[0]['spendcoin']);
        $roomemceelevel = getEmceelevel($roominfo[0]['earnbean']);
        if ((int)$roominfo[0]['virtualguest'] > 0 ) {
            $virtualusers = D('Member')->where('isvirtual="y"')->order('rand()')->select();
            $virtualusers_str = '';
            foreach ($virtualusers as $val) {
                $richlevel = getRichlevel($val['spendcoin']);
                $virtualusers_str .= $val['id'] . '$$' . $val['nickname'] . '$$' . $val['curroomnum'] . '$$' . $val['vip'] . '$$' . $richlevel[0]['levelid'] . '$$' . $val['spendcoin'] . '***';
            }
        }
        
        if ($_SESSION['uid'] < 0) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<ROOT>';
            echo '<err>no</err>';
            echo '<Badge></Badge>';
            echo '<familyname></familyname>';
            echo '<goodnum></goodnum>';
            echo '<h>0</h>';
            echo '<level>0</level>';
            echo '<richlevel>0</richlevel>';
            echo '<spendcoin>0</spendcoin>';
            echo '<sellm>0</sellm>';
            echo '<sortnum></sortnum>';
            echo '<userType>20</userType>';
            echo '<userid>' . $_SESSION['uid'] . '</userid>';
            echo '<username>游客' . $_SESSION['uid'] . '</username>';
            echo '<vip>0</vip>';
            if ($roominfo[0]['fakeuser'] == 'y') {
                echo '<fakeroom>y</fakeroom>';
                echo '<roomBadge></roomBadge>';
                echo '<roomfamilyname></roomfamilyname>';
                echo '<roomgoodnum>' . $roominfo[0]['curroomnum'] . '</roomgoodnum>';
                echo '<roomlevel>' . $roomemceelevel[0]['levelid'] . '</roomlevel>';
                echo '<roomrichlevel>' . $roomrichlevel[0]['levelid'] . '</roomrichlevel>';
                echo '<roomuserid>' . $roominfo[0]['id'] . '</roomuserid>';
                echo '<roomusername>' . $roominfo[0]['nickname'] . '</roomusername>';
                echo '<roomvip>1</roomvip>';
            } else {
                echo '<fakeroom>n</fakeroom>';
            }

            if ($roominfo[0]['broadcasting'] == 'y') {
                echo '<virtualguest>' . $roominfo[0]['virtualguest'] . '</virtualguest>';
                echo '<virtualusers_str>' . $virtualusers_str . '</virtualusers_str>';
            } else {
                echo '<virtualguest>0</virtualguest>';
                echo '<virtualusers_str></virtualusers_str>';
            }
            echo '</ROOT>';
        } else {
            $userinfo = D("Member")->find($_SESSION['uid']);
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<ROOT>';
            echo '<err>no</err>';
            echo '<Badge></Badge>';
            echo '<familyname></familyname>';
            echo '<goodnum>' . $_SESSION['roomnum'] . '</goodnum>';
            echo '<h>0</h>';
            echo '<level>' . $emceelevel[0]['levelid'] . '</level>';
            echo '<richlevel>' . $richlevel[0]['levelid'] . '</richlevel>';
            echo '<spendcoin>' . $userinfo['spendcoin'] . '</spendcoin>';
            echo '<sellm>' . $userinfo['sellm'] . '</sellm>';
            if ($_SESSION['roomnum'] == $_GET['roomnum']) {
                echo '<sortnum></sortnum>';
                echo '<userType>50</userType>';
            } else {
                echo '<sortnum></sortnum>';
                $myshowadmin = D("Roomadmin")->where('uid=' . $roominfo[0]['id'] . ' and adminuid=' . $_SESSION['uid'])->order('id asc')->select();
                if ($userinfo['showadmin'] == '1' || $myshowadmin) {
                    echo '<userType>40</userType>';
                } else {
                    echo '<userType>30</userType>';
                }
            }
            echo '<userid>' . $_SESSION['uid'] . '</userid>';
            echo '<username>' . $_SESSION['nickname'] . '</username>';
            if ($userinfo['vipexpire'] > time()) {
                echo '<vip>' . $userinfo['vip'] . '</vip>';
            } else {
                echo '<vip>0</vip>';
            }

            if ($roominfo[0]['fakeuser'] == 'y') {
                echo '<fakeroom>y</fakeroom>';
                echo '<roomBadge></roomBadge>';
                echo '<roomfamilyname></roomfamilyname>';
                echo '<roomgoodnum>' . $roominfo[0]['curroomnum'] . '</roomgoodnum>';
                echo '<roomlevel>' . $roomemceelevel[0]['levelid'] . '</roomlevel>';
                echo '<roomrichlevel>' . $roomrichlevel[0]['levelid'] . '</roomrichlevel>';
                echo '<roomuserid>' . $roominfo[0]['id'] . '</roomuserid>';
                echo '<roomusername>' . $roominfo[0]['nickname'] . '</roomusername>';
                echo '<roomvip>1</roomvip>';
            } else {
                echo '<fakeroom>n</fakeroom>';
            }

            if ($roominfo[0]['broadcasting'] == 'y' || $_SESSION['roomnum'] == $_GET['roomnum']) {
                echo '<virtualguest>' . $roominfo[0]['virtualguest'] . '</virtualguest>';
                echo '<virtualusers_str>' . $virtualusers_str . '</virtualusers_str>';
            } else {
                echo '<virtualguest>0</virtualguest>';
                echo '<virtualusers_str></virtualusers_str>';
            }
            echo '</ROOT>';
        }
    }
    
    public function createroom()
    {
        C('HTML_CACHE_ON', false);
        header('Content-Type: text/xml');
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $err = "您尚未登录，请登录后重试";
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<ROOT>';
            echo '<err>yes</err>';
            echo '<msg>' . $err . '</msg>';
            echo '</ROOT>';
            exit;
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        if ($userinfo['canlive'] == 'n') {
            $err = "您暂时没有直播权限";
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<ROOT>';
            echo '<err>yes</err>';
            echo '<msg>' . $err . '</msg>';
            echo '</ROOT>';
            exit;
        }
        if ($_REQUEST['roomtype'] == '1') {
            // 判断用户虚拟币是否足够
            if ($userinfo['coinbalance'] < 100) {
                $err = "您的余额不足";
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<ROOT>';
                echo '<err>yes</err>';
                echo '<msg>' . $err . '</msg>';
                echo '</ROOT>';
                exit;
            } else {
                // 扣费
                D("Member")->execute('update ss_member set spendcoin=spendcoin+100,coinbalance=coinbalance-100 where id=' . $_SESSION['uid']);
                // 记入虚拟币交易明细
                $Coindetail = D("Coindetail");
                $Coindetail->create();
                $Coindetail->type = 'expend';
                $Coindetail->action = 'createspeshow';
                $Coindetail->uid = $_SESSION['uid'];
                $Coindetail->content = $userinfo['nickname'] . ' 创建了一个收费房间';
                $Coindetail->objectIcon = '/style/images/fei.png';
                $Coindetail->coin = 100;
                $Coindetail->addtime = time();
                $detailId = $Coindetail->add();
            }
        }
        if ($_REQUEST['roomtype'] == '2') {
            // 判断用户虚拟币是否足够
            if ($userinfo['coinbalance'] < 50) {
                $err = "您的余额不足";
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<ROOT>';
                echo '<err>yes</err>';
                echo '<msg>' . $err . '</msg>';
                echo '</ROOT>';
                exit;
            } else {
                //扣费
                D("Member")->execute('update ss_member set spendcoin=spendcoin+50,coinbalance=coinbalance-50 where id=' . $_SESSION['uid']);
                //记入虚拟币交易明细
                $Coindetail = D("Coindetail");
                $Coindetail->create();
                $Coindetail->type = 'expend';
                $Coindetail->action = 'createspeshow';
                $Coindetail->uid = $_SESSION['uid'];
                $Coindetail->content = $userinfo['nickname'] . ' 创建了一个密码房间';
                $Coindetail->objectIcon = '/style/images/fei.png';
                $Coindetail->coin = 50;
                $Coindetail->addtime = time();
                $detailId = $Coindetail->add();
            }
        }
        $User = D("Member");
        $User->create();
        $User->id = $_SESSION['uid'];
        $User->broadcasting = 'y';
        $showId = time();
        $User->showId = $showId;
        $User->starttime = time();
        $User->roomtype = $_REQUEST['roomtype'];
        if ($_REQUEST['roomtype'] == '1') {
            $User->needmoney = $_REQUEST['needmoney'];
        }
        if ($_REQUEST['roomtype'] == '2') {
            $User->roompsw = $_REQUEST['roompsw'];
        }
        $userId = $User->save();
        // 删除重复的 某些情况出现重复记录
        // D("Liverecord")->execute('delete from ss_liverecord   where showId=' . $showid);
        D("Liverecord")->where('showId=' . $showId)->delete();
        // 删除结束时间为空的
        $uiid = $_SESSION['uid'];
        D("Liverecord")->where("endtime=0 and uid='{$uiid}'")->delete();
        // 新加一条直播记录
        $Liverecord = D("Liverecord");
        $Liverecord->create();
        $Liverecord->roomtype = $_REQUEST['roomtype'];
        $Liverecord->uid = $_SESSION['uid'];
        $Liverecord->showId = $showId;
        $Liverecord->starttime = $showId;
        $Liverecord->sign = $userinfo['sign'];
        $liveId = $Liverecord->add();
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<ROOT>';
        echo '<err>no</err>';
        echo '<showId>' . $showId . '</showId>';
        echo '</ROOT>';
    }

    public function do_myfamily_edit()
    {
        $model = M("agentfamily");
        if ($model->create()) {
            $model->id = $_POST['id'];
            $model->familyname = $_POST['familyname'];
            $model->familyinfo = $_POST['familyinfo'];
            if ($model->save()) {
                $this->success("资料更新成功！");
            } else {
                $this->error("资料更新失败！");
            }
        } else {
            $this->error($model->getError());
        }
    }

    public function enterroom()
    {
        C('HTML_CACHE_ON', false);
        $userinfo = D("Member")->where('curroomnum=' . $_REQUEST['roomid'] . '')->select();
        D("Member")->execute('update ss_member set online=online+1 where curroomnum=' . $_REQUEST['roomid']);
        //if($userinfo[0]['broadcasting'] == 'y'){
            D("Liverecord")->execute('update ss_liverecord set entercount=entercount+1 where showId=' . $userinfo[0]['showid']);
        //}
    }

    public function enterroom2(){
        C('HTML_CACHE_ON', false);
        $userinfo = D("Member")->where('curroomnum=' . $_REQUEST['roomid'] . '')->select();
        D("Member")->execute('update ss_member set online=online+1 where curroomnum=' . $_REQUEST['roomid']);
        //if($userinfo[0]['broadcasting'] == 'y'){
            D("Liverecord")->execute('update ss_liverecord set entercount=entercount+1 where showId=' . $userinfo[0]['showid']);
        //}
    }

    public function exitroom(){
        C('HTML_CACHE_ON', false);
        $userinfo = D("Member")->find($_REQUEST['uid']);
        if($userinfo && $_REQUEST['roomid'] == $userinfo['curroomnum']){
            if($userinfo['broadcasting'] == 'y'){
                D("Member")->execute('update ss_member set ispublic="1",SongApply="1",broadcasting="n",showId=0,seat1_ucuid=0,seat1_nickname="",seat1_count=0,seat2_ucuid=0,seat2_nickname="",seat2_count=0,seat3_ucuid=0,seat3_nickname="",seat3_count=0,seat4_ucuid=0,seat4_nickname="",seat4_count=0,seat5_ucuid=0,seat5_nickname="",seat5_count=0 where id=' . $_REQUEST['uid']);
                //写入当次直播记录的结束时间
                D("Liverecord")->execute('update ss_liverecord set endtime='.time() . ' where showId=' . $userinfo['showid']);
            }
            else{
                D("Member")->execute('update ss_member set seat1_ucuid=0,seat1_nickname="",seat1_count=0,seat2_ucuid=0,seat2_nickname="",seat2_count=0,seat3_ucuid=0,seat3_nickname="",seat3_count=0,seat4_ucuid=0,seat4_nickname="",seat4_count=0,seat5_ucuid=0,seat5_nickname="",seat5_count=0 where id=' . $_REQUEST['uid']);
            }
        }
        D("Member")->execute('update ss_member set online=online-1 where curroomnum=' . $_REQUEST['roomid'] . ' and online > 0');
    }

    // flash 发送过来的结束
        public function exitroom2(){
        C('HTML_CACHE_ON', false);
        $uid = $_SESSION['uid'];
        $err =     $uid."".$_REQUEST['roomnum'];
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $err = "您尚未登录，请登录后重试";
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<ROOT>';
            echo '<err>yes</err>';
            echo '<msg>' . $err . '</msg>';
            echo '</ROOT>';
            exit;
        }
     
        $userinfo = D("Member")->find($uid);
        if($userinfo && $_REQUEST['roomnum'] == $userinfo['curroomnum']){
         
                D("Member")->execute('update ss_member set ispublic="1",SongApply="1",broadcasting="n",showId=0,seat1_ucuid=0,seat1_nickname="",seat1_count=0,seat2_ucuid=0,seat2_nickname="",seat2_count=0,seat3_ucuid=0,seat3_nickname="",seat3_count=0,seat4_ucuid=0,seat4_nickname="",seat4_count=0,seat5_ucuid=0,seat5_nickname="",seat5_count=0 where id=' . $_REQUEST['uid']);
                //写入当次直播记录的结束时间
                D("Liverecord")->execute('update ss_liverecord set endtime='.time() . ' where showId=' . $userinfo['showid']);
        }
     
    }

    public function resetonline(){
        C('HTML_CACHE_ON', false);
        D("Member")->execute('update ss_member set online=0 where host="' . $_REQUEST['host'] . '"');
    }

    public function makesnap2(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '&err=nologin';
            exit;
        }
        $prefix = date('Y-m');
        $uploadPath = '/style/snap/' . $prefix . '/';
        $realPath = __DIR__ . '/../..'.$uploadPath;
        if(!is_dir($realPath)){
            mkdir($realPath, 0777, true);
        }
        $filename = md5($_SESSION['roomnum']) . '.jpg';
        if (isset($GLOBALS["HTTP_RAW_POST_DATA"]))
        {
            $png = gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]);
            file_put_contents($realPath.$filename, $png);
            D("Member")->query('update ss_member set snap="' . $uploadPath.$filename . '" where id=' . $_SESSION['uid']);
            echo "ok";
        }
    }

    public function makesnap(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '&err=nologin';
            exit;
        }
        $w = 160;
        $h = 120;
        $img = imagecreatetruecolor($w, $h);
        imagefill($img, 0, 0, 0xFFFFFF);
        $rows = 0;
        $cols = 0;
        $dataArr = explode("|", $_POST['imgdata']);
        for($rows = 0; $rows < $h; $rows++){
            $c_row = explode(",", $dataArr[$rows]);
            for($cols = 0; $cols < $w; $cols++){
                $value = $c_row[$cols];
                if($value != ""){
                    $hex = $value;
                    while(strlen($hex) < 6){
                        $hex = "0" . $hex;
                    }
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                    $test = imagecolorallocate($img, $r, $g, $b);
                    imagesetpixel($img, $cols, $rows, $test);
                }
            }
        }
        //D("Siteconfig")->query('update ss_siteconfig set imgdata="' . $tmpstr . '" where id=1');
        $prefix = date('Y-m');
        $uploadPath = '/style/snap/' . $prefix . '/';
        if(!is_dir(' . ' . $uploadPath)){
            mkdir(' . ' . $uploadPath);
        }
        $filename = md5($_SESSION['roomnum']) . '.jpg';
        imagejpeg($img, ' . ' . $uploadPath.$filename, 90);
        D("Member")->query('update ss_member set snap="' . $uploadPath.$filename . '" where id=' . $_SESSION['uid']);
        echo '&snap=' . $uploadPath.$filename . '?t='.time();
        exit;
    }

    public function setBulletin(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"info":"您尚未登录"}';
            exit;
        }
        if($_SESSION['uid'] != $_REQUEST['eid']){
            echo '{"info":"您不是该房间主人"}';
            exit;
        }
        $User = D("Member");
        $User->create();
        $User->id = $_SESSION['uid'];
        if($_REQUEST['bt'] == 2){
            $User->announce = $_REQUEST['t'];
            $User->annlink = $_REQUEST['u'];
        }
        if($_REQUEST['bt'] == 3){
            $User->announce2 = $_REQUEST['t'];
            $User->ann2link = $_REQUEST['u'];
        }
        $userId = $User->save();
        echo '{"code":"0"}';
        exit;
    }

    public function setBackground(){
        C('HTML_CACHE_ON', false);
        header("Content-type: text/html; charset=utf-8"); 
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo "<script>alert('您尚未登录');</script>";
            exit;
        }
        if($_SESSION['uid'] != $_REQUEST['eid']){
            echo "<script>alert('您不是该房间主人');</script>";
            exit;
        }
        //上传缩略图
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = 1048576 ;
        //设置上传文件类型
        $upload->allowExts  = explode(',','jpg');
        //设置上传目录
        //每个用户一个文件夹
        $prefix = date('Y-m');
        $uploadPath =  './style/bgimg/' . $prefix . '/';
        if(!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $upload->savePath =  $uploadPath;
        $upload->saveRule = uniqid;
        //执行上传操作
        if(!$upload->upload()) {
            // 捕获上传异常
            echo "<script>alert('".$upload->getErrorMsg()."');</script>";
            exit;
        }
        else{
            $uploadList = $upload->getUploadFileInfo();
            $picpath = '/style/bgimg/' . $prefix . '/' . $uploadList[0]['savename'];
        }

        D("Member")->execute('update ss_member set bgimg="' . $picpath . '" where id=' . $_SESSION['uid']);        
        echo "<script>document.domain='".$this->domainroot."';alert('上传成功');window.parent.playerMenu.setBackground2('".$picpath."');</script>";
        exit;
    }

    public function cancelBackground(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"code":"1"}';
            exit;
        }
        if($_SESSION['uid'] != $_REQUEST['eid']){
            echo '{"code":"2"}';
            exit;
        }
        D("Member")->execute('update ss_member set bgimg="" where id=' . $_SESSION['uid']);
        echo '{"code":"0"}';
        exit;
    }

    public function setOfflineVideo(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }
        if($_SESSION['uid'] != $_REQUEST['eid']){
            echo '{"code":"2","info":"您不是该房间主人"}';
            exit;
        }
        D("Member")->execute('update ss_member set offlinevideo="' . $_REQUEST['url'] . '" where id=' . $_SESSION['uid']);
        echo '{"code":"0"}';
        exit;
    }

    public function cancelOfflineVideo(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"code":"1","info":"您尚未登录"}';
            exit;
        }
        if($_SESSION['uid'] != $_REQUEST['eid']){
            echo '{"code":"2","info":"您不是该房间主人"}';
            exit;
        }
        D("Member")->execute('update ss_member set offlinevideo="" where id=' . $_SESSION['uid']);
        echo '{"code":"0"}';
        exit;
    }

    public function setPublicChat(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"state":"3","info":"您尚未登录"}';
            exit;
        }

        if($_SESSION['uid'] != $_REQUEST['eid']){
            echo '{"state":"3","info":"您不是该房间主人"}';
            exit;
        }
        D("Member")->execute('update ss_member set ispublic="' . $_REQUEST['flag'] . '" where id=' . $_SESSION['uid']);
        echo '{"state":"' . $_REQUEST['flag'] . '"}';
        exit;
    }

    public function wishing(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"state":"3","info":"您尚未登录"}';
            exit;
        }

        if($_REQUEST['action'] == 'isWished'){
            $userwishs = D("Wish")->where('uid=' . $_SESSION['uid'] . ' and date_format(FROM_UNIXTIME(wishtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y")')->order('id asc')->select();
            if($userwishs){
                echo '1';
                exit;
            }
            else{
                echo '0';
                exit;
            }
        }
        if($_REQUEST['action'] == 'save'){
            //判断虚拟币是否足够
            //添加许愿
            $Wish=D("Wish");
            $Wish->create();
            $Wish->uid = $_SESSION['uid'];
            if($_REQUEST['type'] == '1'){
                $Wish->wish = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">' . $_REQUEST['num'] . '</strong>个' . $_REQUEST['giftName'];
            }
            if($_REQUEST['type'] == '2'){
                $Wish->wish = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">' . $_REQUEST['num'] . '</strong>人热捧';
            }
            $Wish->wishtime = time();
            $wishId = $Wish->add();
            echo '{"wishedFlag":"1","wishType":"' . $_REQUEST['type'] . '","count":"' . $_REQUEST['num'] . '","giftName":"' . $_REQUEST['giftName'] . '"}';
            exit;
        }
    }

    public function sign_view(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        if($userinfo['sign'] == 'y'){
            $this->assign('jumpUrl', __APP__);
            $this->error('您已是签约主播，更改资料请联系客服');
        }
        $this->display();
    }

    public function do_sign_view(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        //上传缩略图
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = 1048576 ;
        //设置上传文件类型
        $upload->allowExts  = explode(',','jpg,png');
        //设置上传目录
        //每个用户一个文件夹
        $prefix = date('Y-m');
        $uploadPath =  './style/bigpic/' . $prefix . '/';
        if(!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $upload->savePath =  $uploadPath;
        $upload->saveRule = uniqid;
        //执行上传操作
        if(!$upload->upload()) {
            // 捕获上传异常 
            if($upload->getErrorMsg() != '没有选择上传文件'){
                //echo "<script>alert('".$upload->getErrorMsg()."');<///script>";
                //exit;
                $this->error($upload->getErrorMsg());
                exit;
            }
        }
        else{
            $uploadList = $upload->getUploadFileInfo();
            $picpath = '/style/bigpic/' . $prefix . '/' . $uploadList[0]['savename'];
        }
        $User = D('Member');
        $vo = $User->create();
        if(!$vo) {
            $this->error($User->getError());
        } else {
            $User->sign = 'i';
            $User->bigpic = $picpath;
            $User->save();
            $this->assign('jumpUrl', __APP__);
            $this->success('签约审核中，请等待管理员与您联系');
        }
        $this->display();
    }

    public function index(){
        $this->display();
    }
    
    public function index_lianghao()
    {
        $four_goodnums = D('Goodnum')->where('length=4 and issale="n"')->order('rand()')->limit(15)->select();
        $this->assign('four_goodnums', $four_goodnums);
        
        $five_goodnums = D('Goodnum')->where('length=5 and issale="n"')->order('rand()')->limit(15)->select();
        $this->assign('five_goodnums', $five_goodnums);
        
        $six_goodnums = D('Goodnum')->where('length=6 and issale="n"')->order('rand()')->limit(15)->select();
        $this->assign('six_goodnums', $six_goodnums);
        
        $seven_goodnums = D('Goodnum')->where('length=7 and issale="n"')->order('rand()')->limit(4)->select();
        $this->assign('seven_goodnums', $seven_goodnums);
        
        $eight_goodnums = D('Goodnum')->where('length=8 and issale="n"')->order('rand()')->limit(15)->select();
        $this->assign('eight_goodnums', $eight_goodnums);
        
        $this->display();
    }
    
    public function index_zuojia()
    {
        $toolsinfo = D("Tools")->where('status=1')->select();
        // echo "<pre>";
        // print_r($toolsinfo);
        $this->assign('tools', $toolsinfo);
        $this->display();
    }
    public function index_shouhu()
    {
        $this->display();
    }
    public function myfavor(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $favors = D("Favor")->where("uid=".$_SESSION['uid'])->order('addtime desc')->select();
        foreach($favors as $n=> $val){
            $favors[$n]['voo']=D("Member")->where('id=' . $val['favoruid'])->select();
        }
        $this->assign('favors', $favors);
        $this->display();
    }

    public function delfavor(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $fidArr = explode(",", $_GET['fid']);
        foreach ($fidArr as $k){
            $favorinfo = D("Favor")->find($k);
            if($favorinfo && $favorinfo['uid'] == $_SESSION['uid']){
                D("Favor")->where('id=' . $k)->delete();
            }
        }
        $this->assign('jumpUrl', __URL__."/myfavor/");
        $this->success('操作成功');
    }

    public function bookmark_add(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"state":"1"}';
            exit;
        }
        $favors = D("Favor")->where('uid=' . $_SESSION['uid'] . ' and favoruid=' . $_REQUEST['emceeid'])->order('id asc')->select();
        if($favors){
            echo '{"state":"0","op":"repeat"}';
            exit;
        }
        else{
            $Favor=D("Favor");
            $Favor->create();
            $Favor->uid = $_SESSION['uid'];
            $Favor->favoruid = $_REQUEST['emceeid'];
            $favorId = $Favor->add();
            if($favorId > 0){
                echo '{"state":"0","op":"cancle"}';
                exit;
            }
            else{
                echo '{"state":"1"}';
                exit;
            }
        }
    }

    public function bookmark_cancle(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"state":"1"}';
            exit;
        }
        D("Favor")->where('uid=' . $_SESSION['uid'] . ' and favoruid=' . $_REQUEST['emceeid'])->delete();
        echo '{"state":"0","op":""}';
        exit;
    }

    public function mygods(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $Attention = D("Attention");
        $count = $Attention->where("uid=".$_SESSION['uid'])->count();
        $listRows = 12;
        import("@.ORG.Page2");
        $p = new Page($count,$listRows,$linkFront);
        $attentions = $Attention->where("uid=".$_SESSION['uid'])->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
        foreach($attentions as $n=> $val){
            $attentions[$n]['voo']=D("Member")->where('id=' . $val['attuid'])->select();
        }
        $page = $p->show();
        $this->assign('attentions',$attentions);
        $this->assign('count',$count);
        $this->assign('page',$page);
        //我捧的人
        $mypengusers = D('Coindetail')->query('SELECT touid,sum(coin) as total FROM `ss_coindetail` where type="expend" and uid=' . $_SESSION['uid'] . ' and touid>0 group by touid order by total desc LIMIT 5');
        foreach($mypengusers as $n=> $val){
            $mypengusers[$n]['voo']=D("Member")->where('id=' . $val['touid'])->select();
        }
        $this->assign('mypengusers', $mypengusers);
        $this->display();
    }

    public function cancelInterest(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        D("Attention")->where('uid=' . $_SESSION['uid'] . ' and attuid=' . $_REQUEST['uid'])->delete();
        //$this->assign('jumpUrl', __URL__."/mygods/");
        //$this->success('操作成功');
        echo '1';
        exit;
    }

    public function interest(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $Attention=D("Attention");
        $Attention->create();
        $Attention->uid = $_SESSION['uid'];
        $Attention->attuid = $_REQUEST['uid'];
        $attId = $Attention->add();
        if($attId > 0){
            echo '1';
            exit;
        }
        else{
            echo '0';
            exit;
        }
    }

    //查询关注状态
    public function Attention()
    {
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $arr['code'] = "-1";
        }  
        $uid = $_SESSION['uid'];
        $attuid =  $_GET['attuid'];
        $data = M("attention")->where("uid = $uid and attuid=$attuid")->find();
        if($data==null)
        {
            $arr['code'] = 0;
        }
        else
        { 
            $arr['code'] = 1;
        }
        echo json_encode($arr); 
    }

    public function mynumbers(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $mynos = D("Roomnum")->where("uid=".$_SESSION['uid'])->order('addtime asc')->select();
        foreach ($mynos as $key => $value) {
            if($value['expiretime']!=0 and $value['expiretime']<time()){
                M('Roomnum')->where(array('uid'=>$value['uid'],'expiretime'=>$value['expiretime']))->delete();
                $num = M('Roomnum')->where(array('uid'=>$value['uid'],'original'=>'y'))->find();
                $data['curroomnum'] = $num['num'];
                D('Member')->where(array('id'=>$value['uid']))->save($data);          
                session('roomnum',$num['num']);
                cookie('roomnum',$num['num'],360000);
            }
        }
        $myno = D("Roomnum")->where("uid=".$_SESSION['uid'])->order('addtime asc')->select();
        $this->assign('mynos', $myno);
        $attentions = D("Attention")->where("uid=".$_SESSION['uid'])->order('addtime desc')->select();
        foreach($attentions as $n=> $val){
            $attentions[$n]['voo']=D("Member")->where('id=' . $val['attuid'])->select();
        }
        $this->assign('attentions', $attentions);
        $this->display();
    }

    public function setcurroomnum(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        if($_GET["roomnum"] == '') {
            $this->assign('jumpUrl', __APP__ . '/my/');
            $this->error('缺少参数或参数不正确');
        }
        else{
            $numinfo = D("Roomnum")->where('num=' . $_GET["roomnum"] . '')->select();
            if($numinfo){
                if($numinfo[0]['uid'] == $_SESSION['uid']){
                    D("Member")->execute('update ss_member set curroomnum=' . $_GET["roomnum"] . ' where id=' . $_SESSION['uid']);
                    session('roomnum',$_GET["roomnum"]);
                    cookie('roomnum',$_GET["roomnum"],3600000);
                    $this->assign('jumpUrl', __APP__ . '/my/mynumbers/');
                    $this->success('启用成功');
                }
                else{
                    $this->assign('jumpUrl', __APP__ . '/my/mynumbers/');
                    $this->error('您不是该房间号的主人');
                }
            }
            else{
                $this->assign('jumpUrl', __APP__ . '/my/mynumbers/');
                $this->error('没有该房间号');
            }
        }
    }

    public function transroomnum(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo 'error';
            exit;
        }
        if($_GET["roomnum"] == '' || $_GET["grantId"] == '')
        {
            echo 'error';
            exit;
        }
        else{
            $numinfo = D("Roomnum")->where('num=' . $_GET["roomnum"] . '')->select();
            if($numinfo){
                if($numinfo[0]['uid'] == $_SESSION['uid']){
                    if($_GET["grantId"] == $_SESSION['uid']){
                        echo 'error';
                        exit;
                    }
                    else{
                        D("Roomnum")->execute('update ss_roomnum set uid=' . $_GET["grantId"] . ' where num=' . $_GET["roomnum"]);
                        //写一条记录到ss_giveaway
                        $Giveaway = D("Giveaway");
                        $Giveaway->create();
                        $Giveaway->uid = $_SESSION['uid'];
                        $Giveaway->touid = $_GET["grantId"];
                        $Giveaway->content = '(' . $_GET["roomnum"] . ')';
                        $Giveaway->objectIcon = '/style/images/gnum.png';
                        $giveId = $Giveaway->add();
                        echo 'success';
                        exit;
                    }
                }
                else{
                    echo 'error';
                    exit;
                }
            }
            else{
                echo 'error';
                exit;
            }
        }
    }

    public function  mytools(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function toolItem(){
        $this->display();
    }

    public function buyTool(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            echo '{"msg":"请重新登录"}';
            exit;
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $richlevel = getRichlevel($userinfo['spendcoin']);
        switch ($_GET['toolid']){
            case '1':
                //判断用户富豪级别
                if($richlevel[0]['levelid'] < 10){
                    echo '{"msg":"限10富及以上等级购买"}';
                    exit;
                }
                if($_GET['toolsubid']  == 1){
                    $needcoin = 20000;
                    $duration = 3600 * 24 * 30 * 1;
                    $duration2 = '一个月';
                }
                else if($_GET['toolsubid']  == 2){
                    $needcoin = 4800;
                    $duration = 3600 * 24 * 30 * 3;
                    $duration2 = '三个月';
                }
                else if($_GET['toolsubid']  == 3){
                    $needcoin = 8400;
                    $duration = 3600 * 24 * 30 * 6;
                    $duration2 = '六个月';
                }
                else if($_GET['toolsubid']  == 4){
                    $needcoin = 120000;
                    $duration = 3600 * 24 * 30 * 12;
                    $duration2 = '十二个月';
                }
                if($userinfo['coinbalance'] < $needcoin){
                    echo '{"msg":"您的余额不足"}';
                    exit;
                }
                else{
                    $expireTime;
                    if($userinfo['vipexpire'] == 0){
                        D("Member")->execute('update ss_member set vip="1",vipexpire=vipexpire+' . (time() + $duration) . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        $expireTime = time() + $duration;
                    }
                    else{
                        D("Member")->execute('update ss_member set vip="1",vipexpire=vipexpire+' . $duration . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        $expireTime = $userinfo['vipexpire'] + $duration;
                    }
                    include './config.inc.php';
                    $mem = new Memcache();
                    if ($mem->connect($mem_host,$mem_port)) {
                        $mem->set($user_vip_prefix.$_SESSION['uid'], json_encode(array('vip'=>1,'expire'=>$expireTime)));
                    }
                    //写入消费明细
                    $Coindetail = D("Coindetail");
                    $Coindetail->create();
                    $Coindetail->type = 'expend';
                    $Coindetail->action = 'buy';
                    $Coindetail->uid = $_SESSION['uid'];
                    $Coindetail->giftcount = 1;
                    $Coindetail->content = '您购买了 ' . $duration2 . ' 至尊VIP';
                    $Coindetail->objectIcon = '/style/images/vip1.png';
                    $Coindetail->coin = $needcoin;
                    $Coindetail->addtime = time();
                    $detailId = $Coindetail->add();
                    echo '{"msg":"购买成功"}';
                    exit;
                }
                break;
            case '2':
                //判断用户富豪级别
                if($richlevel[0]['levelid'] < 3){
                    echo '{"msg":"限3富及以上等级购买"}';
                    exit;
                }
                if($userinfo['vip'] == '1' && $userinfo['vipexpire'] > time()){
                    echo '{"msg":"您已经是至尊VIP了"}';
                    exit;
                }
                if($_GET['toolsubid']  == 5){
                    $needcoin = 15000;
                    $duration = 3600 * 24 * 30 * 1;
                    $duration2 = '一个月';
                }
                else if($_GET['toolsubid']  == 6){
                    $needcoin = 4000;
                    $duration = 3600 * 24 * 30 * 3;
                    $duration2 = '三个月';
                }
                else if($_GET['toolsubid']  == 7){
                    $needcoin = 6500;
                    $duration = 3600 * 24 * 30 * 6;
                    $duration2 = '六个月';
                }
                else if($_GET['toolsubid']  == 8){
                    $needcoin = 100000;
                    $duration = 3600 * 24 * 30 * 12;
                    $duration2 = '十二个月';
                }
                if($userinfo['coinbalance'] < $needcoin){
                    echo '{"msg":"您的余额不足"}';
                    exit;
                }
                else{
                    $expireTime;
                    if($userinfo['vipexpire'] < time()){
                        $expireTime = time() + $duration;
                        D("Member")->execute('update ss_member set vip="2",vipexpire=' . (time() + $duration) . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                    }
                    else{
                        $expireTime = $userinfo['vipexpire'] +$duration;
                        D("Member")->execute('update ss_member set vip="2",vipexpire=vipexpire+' . $duration . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                    }
                    include './config.inc.php';
                    $mem = new Memcache();
                    if ($mem->connect($mem_host,$mem_port)) {
                        $mem->set($user_vip_prefix.$_SESSION['uid'], json_encode(array('vip'=>2,'expire'=>$expireTime)));
                    }
                    //写入消费明细
                    $Coindetail = D("Coindetail");
                    $Coindetail->create();
                    $Coindetail->type = 'expend';
                    $Coindetail->action = 'buy';
                    $Coindetail->uid = $_SESSION['uid'];
                    $Coindetail->giftcount = 1;
                    $Coindetail->content = '您购买了 ' . $duration2 . ' VIP';
                    $Coindetail->objectIcon = '/style/images/vip2.png';
                    $Coindetail->coin = $needcoin;
                    $Coindetail->addtime = time();
                    $detailId = $Coindetail->add();
                    echo '{"msg":"购买成功"}';
                    exit;
                }
                break;
            case '3':
                if($_GET['toolsubid']  == 9){
                    $needcoin = 15000;
                    $duration = 3600 * 24 * 30 * 1;
                    $duration2 = '一个月';
                }
                if($userinfo['coinbalance'] < $needcoin){
                    echo '{"msg":"您的余额不足"}';
                    exit;
                }
                else{
                    $expireTime;
                    if($userinfo['gkexpire'] < time()){
                        $expireTime = time() + $duration;
                        D("Member")->execute('update ss_member set goldkey="y",gkexpire=' . (time() + $duration) . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                    }
                    else{
                        $expireTime = $userinfo['gkexpire'] + $duration;
                        D("Member")->execute('update ss_member set goldkey="y",gkexpire=gkexpire+' . $duration . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                    }
                    include './config.inc.php';
                    $mem = new Memcache();
                    if ($mem->connect($mem_host,$mem_port)) {
                        $mem->set($user_vip_prefix.$_SESSION['uid'], json_encode(array('goldkey'=>1,'expire'=>$expireTime)));
                    }
                    //写入消费明细
                    $Coindetail = D("Coindetail");
                    $Coindetail->create();
                    $Coindetail->type = 'expend';
                    $Coindetail->action = 'buy';
                    $Coindetail->uid = $_SESSION['uid'];
                    $Coindetail->giftcount = 1;
                    $Coindetail->content = '您购买了 ' . $duration2 . ' 金钥匙';
                    $Coindetail->objectIcon = '/style/images/goldkey.png';
                    $Coindetail->coin = $needcoin;
                    $Coindetail->addtime = time();
                    $detailId = $Coindetail->add();
                    echo '{"msg":"购买成功"}';
                    exit;
                }
                break;
            case '4':
                if($_GET['toolsubid']  == 10){
                    $needcoin = 50000;
                    $duration = 3600 * 24 * 30 * 1;
                    $duration2 = '一个月';
                }elseif($_GET['toolsubid'] == 11){
                    $needcoin = 50000;
                    $duration = 3600 * 24 * 30 * 1;
                    $duration2 = '一个月';
                } else {
                    $needcoin = 88888;
                    $duration = 3600 * 24 * 30 * 1;
                    $duration2 = '一个月';
                }
                if($userinfo['coinbalance'] < $needcoin){
                    echo '{"msg":"您的余额不足"}';
                    exit;
                }
                else{
                    $expireTime;
                    $atwill;
                    if($needcoin==50000) {
                        $atwill = 1;
                        if ($userinfo['awexpire'] < time()) {
                            $expireTime = time() + $duration;
                            D("Member")->execute('update ss_member set atwill="1",awexpire=' . (time() + $duration) . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        } else {
                            $expireTime = $userinfo['awexpire'] + $duration;
                            D("Member")->execute('update ss_member set atwill="1",awexpire=awexpire+' . $duration . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        }
                    }elseif($needcoin==40000){
                        $atwill = 2;
                        if ($userinfo['awexpire'] < time()) {
                            $expireTime = time() + $duration;
                            D("Member")->execute('update ss_member set atwill="2",awexpire=' . (time() + $duration) . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        } else {
                            $expireTime = $userinfo['awexpire'] + $duration;
                            D("Member")->execute('update ss_member set atwill="2",awexpire=awexpire+' . $duration . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        }
                    } else {
                        $atwill = 3;
                        if ($userinfo['awexpire'] < time()) {
                            $expireTime = time() + $duration;
                            D("Member")->execute('update ss_member set atwill="3",awexpire=' . (time() + $duration) . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        } else {
                            $expireTime = $userinfo['awexpire'] + $duration;
                            D("Member")->execute('update ss_member set atwill="3",awexpire=awexpire+' . $duration . ',spendcoin=spendcoin+' . $needcoin . ',coinbalance=coinbalance-' . $needcoin . ' where id=' . $_SESSION['uid']);
                        }
                    }
                    require_once './config.inc.php';
                    $mem = new Memcache();
                    if ($mem->connect($mem_host, $mem_port)) {
                        $mem->set($user_vip_prefix.$_SESSION['uid'], json_encode(array('atwill'=>$atwill,'expire'=>$expireTime)));
                    }
                    //写入消费明细
                    $Coindetail = D("Coindetail");
                    $Coindetail->create();
                    $Coindetail->type = 'expend';
                    $Coindetail->action = 'buy';
                    $Coindetail->uid = $_SESSION['uid'];
                    $Coindetail->giftcount = 1;
                    $Coindetail->content = '您购买了 ' . $duration2 . ' 随意说';
                    $Coindetail->objectIcon = '/style/images/vip1.png';
                    $Coindetail->coin = $needcoin;
                    $Coindetail->addtime = time();
                    $detailId = $Coindetail->add();
                    echo '{"msg":"购买成功"}';
                    exit;
                }
                break;
        }
    }

    public function wishing_wishing(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        //礼物
        $gifts = D('Giftsort')->query('select * from ss_giftsort order by orderno asc');
        foreach($gifts as $n=> $val){
            $gifts[$n]['voo']=D("Gift")->where('sid=' . $val['id'])->select();
        }
        $this->assign('gifts',$gifts);
        $this->display();
    }

    public function mymanagers(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $roomadmins = D("Roomadmin")->where("uid=".$_SESSION['uid'])->order('addtime desc')->select();
        foreach($roomadmins as $n=> $val){
            $roomadmins[$n]['voo']=D("Member")->where('id=' . $val['adminuid'])->select();
        }
        $this->assign('roomadmins', $roomadmins);
        $this->display();
    }

    public function toggleEmceeShowAdmin(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $myshowadmin = D("Roomadmin")->where('uid=' . $_SESSION['uid'] . ' and adminuid=' . $_REQUEST['userid'])->order('id asc')->select();
        
        include './config.inc.php';
        $mem = new Memcache();
        if ($mem->connect($mem_host, $mem_port)) {
            $ret = json_decode($mem->get($sys_and_room_adminer_change),true);
            if (is_null($ret) || empty($ret)) {
                 $ret = array();
            }

            $tmp = json_decode($mem->get($room_status_prefix), true);
            if (is_null($tmp)) {
                $tmp = array('owner'=>$_SESSION['uid'], 'adminer'=>array(), 'kicked'=>array(), 'disableMsg'=>array());
            }
            if (!$myshowadmin) {
                 $ret[$_SESSION['roomnum']]['add'][$_REQUEST['userid']] = $_REQUEST['userid'];
                 unset($ret[$_SESSION['roomnum']]['remove'][$_REQUEST['userid']]);

                 $tmp['adminer'][$_REQUEST['userid']] = $_REQUEST['userid'];
            } else {
                $ret[$_SESSION['roomnum']]['remove'][$_REQUEST['userid']] = $_REQUEST['userid'];
                 unset($ret[$_SESSION['roomnum']]['add'][$_REQUEST['userid']]);
                 unset($tmp['adminer'][$_REQUEST['userid']]);
            }
             $mem->set($room_status_prefix, json_encode($tmp));

            if (!$mem->set($room_adminer_change, json_encode($ret))) {
                $this->error('设置管理员权限失败');
            }
        } else {
            $this->error('设置管理员权限失败');
        }
        
        if($myshowadmin){
            D("Roomadmin")->where('uid=' . $_SESSION['uid'] . ' and adminuid=' . $_REQUEST['userid'])->delete();
            echo '1';
            exit;
        }
        else{
            $Roomadmin=D("Roomadmin");
            $Roomadmin->create();
            $Roomadmin->uid = $_SESSION['uid'];
            $Roomadmin->adminuid = $_REQUEST['userid'];
            $Roomadmin->add();
            echo '0';
            exit;
        }
    }

    public function familyIJoin(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function familyICreate(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }
    
    public function familyBadge()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function familyPrerogative()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function familyOperationLog()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function myfans()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $Attention = D("Attention");
        $count = $Attention->where("attuid=" . $_SESSION['uid'])->count();
        $listRows = 12;
        import("@.ORG.Page2");
        $p = new Page($count, $listRows, $linkFront);
        $attentions = $Attention->where("attuid=" . $_SESSION['uid'])->limit($p->firstRow . "," . $p->listRows)->order('addtime desc')->select();
        foreach ($attentions as $n => $val) {
            $attentions[$n]['voo']=D("Member")->where('id=' . $val['uid'])->select();
        }
        $page = $p->show();
        $this->assign('attentions', $attentions);
        $this->assign('count', $count);
        $this->assign('page', $page);
        // 捧我的人
        $mypengusers = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and uid>0 and touid=' . $_SESSION['uid'] . ' group by uid order by total desc LIMIT 5');
        foreach ($mypengusers as $n => $val) {
            $mypengusers[$n]['voo'] = D("Member")->where('id=' . $val['uid'])->select();
        }
        $this->assign('mypengusers', $mypengusers);
        $this->display();
    }

    public function center_editinfo()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function do_info_edit()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $User = D('Member');
        $vo = $User->create();
        if (!$vo) {
            $this->error($User->getError());
        } else {
            if ($_POST['province'] != '请选择.. . ') {
                $User->province = $_POST['province'];
            }
            if ($_POST['city'] != '请选择.. . ') {
                $User->city = $_POST['city'];
            }
            if (!empty($_POST['intro'])) {
                $User->intro = $_POST['intro'];
            }
            if (!empty($_POST['realname'])) {
                $User->realname = $_POST['realname'];
            }
            
            $User->save();
            session('nickname', $_POST["nickname"]);
            cookie('nickname', $_POST["nickname"], 3600000);
            
            $this->assign('jumpUrl', __APP__ . '/my/center_editinfo/');
            $this->success('保存成功');
        }
    }
    
    public function center_headerpic()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function center_updatepwd()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $this->display();
    }

    public function do_info_changepass(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $User = D('Member');
        $vo = $User->create();
        if(!$vo) {
            $this->error($User->getError());
        } else {
            if($_POST['newpass'] != ''){
                if($_POST['oldpass'] == ''){
                    $this->error('原始密码不能为空');
                }
                if($_POST['newpass'] != $_POST['newpwd_1']){
                    $this->error('两次新密码不一致');
                }
include './config.inc.php';
/*
include './uc_client/client.php';
$ucresult = uc_user_edit($_SESSION['username'], $_POST['oldpass'], $_POST['newpass']);
if($ucresult == -1) {
    $this->error('旧密码不正确');
} elseif($ucresult == -4) {
    $this->error('Email 格式有误');
} elseif($ucresult == -5) {
    $this->error('不允许注册');
} elseif($ucresult == -6) {
    $this->error('该 Email 已经被注册');
}*/
            }
            $User->password = md5($_POST['newpass']);
            $User->password2 = $this->pswencode($_POST['newpass']);
            $User->save();
            $this->assign('jumpUrl', __APP__ . "/my/center_updatepwd/");
            $this->success('修改成功');
        }
    }

    public function giftcount(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $getgifts = D('Coindetail')->query('SELECT objectIcon,sum(giftcount) as total FROM `ss_coindetail` where type="expend" and action="sendgift" and touid=' . $_SESSION['uid'] . ' group by giftid order by total desc');
        $this->assign('getgifts', $getgifts);
        $sendgifts = D('Coindetail')->query('SELECT objectIcon,sum(giftcount) as total FROM `ss_coindetail` where type="expend" and action="sendgift" and uid=' . $_SESSION['uid'] . ' group by giftid order by total desc');
        $this->assign('sendgifts', $sendgifts);
        $this->display();
    }

    public function receivegift(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        if($_GET['begin'] != '' && $_GET['end'] != ''){
            $beginArr = explode("-", $_GET['begin']);
            $starttime = mktime(0,0,0,$beginArr[1],$beginArr[2],$beginArr[0]);
            $endArr = explode("-", $_GET['end']);
            $endtime = mktime(0,0,0,$endArr[1],$endArr[2],$endArr[0]);
            $condition = 'addtime>=' . $starttime . ' and addtime<=' . $endtime;
        }
        else{
            $condition = 'date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y")';
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $Coindetail = D("Coindetail");
        $count = $Coindetail->where('type="expend" and action="sendgift" and touid=' . $_SESSION['uid'] . ' and ' . $condition)->count();
        $listRows = 20;
        import("@.ORG.Page2");
        $p = new Page($count,$listRows,$linkFront);
        $getgifts = $Coindetail->where('type="expend" and action="sendgift" and touid=' . $_SESSION['uid'] . ' and ' . $condition)->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
        foreach($getgifts as $n=> $val){
            $getgifts[$n]['voo']=D("Member")->where('id=' . $val['uid'])->select();
        }
        $page = $p->show();
        $this->assign('getgifts',$getgifts);
        $this->assign('count',$count);
        $pagecount = ceil($count/$listRows);
        if($pagecount == 0){$pagecount = 1;}
        $this->assign('pagecount',$pagecount);
        $this->assign('page',$page);
        $this->display();
    }

    public function sendgift(){
        C('HTML_CACHE_ON', false);
        if(!isset($_SESSION['uid']) || $_SESSION['uid'] < 0){
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        if($_GET['begin'] != '' && $_GET['end'] != ''){
            $beginArr = explode("-", $_GET['begin']);
            $starttime = mktime(0,0,0,$beginArr[1],$beginArr[2],$beginArr[0]);
            $endArr = explode("-", $_GET['end']);
            $endtime = mktime(0,0,0,$endArr[1],$endArr[2],$endArr[0]);
            $condition = 'addtime>=' . $starttime . ' and addtime<=' . $endtime;
        }
        else{
            $condition = 'date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y")';
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $Coindetail = D("Coindetail");
        $count = $Coindetail->where('type="expend" and action="sendgift" and uid=' . $_SESSION['uid'] . ' and ' . $condition)->count();
        $listRows = 20;
        import("@.ORG.Page2");
        $p = new Page($count,$listRows,$linkFront);
        $sendgifts = $Coindetail->where('type="expend" and action="sendgift" and uid=' . $_SESSION['uid'] . ' and ' . $condition)->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
        foreach($sendgifts as $n=> $val){
            $sendgifts[$n]['voo']=D("Member")->where('id=' . $val['touid'])->select();
        }
        $page = $p->show();
        $this->assign('sendgifts',$sendgifts);
        $this->assign('count',$count);
        $pagecount = ceil($count/$listRows);
        if($pagecount == 0){$pagecount = 1;}
        $this->assign('pagecount',$pagecount);
        $this->assign('page',$page);
        $this->display();
    }

    public function mybills()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        if ($_GET['begin'] != '' && $_GET['end'] != '') {
            $beginArr = explode("-", $_GET['begin']);
            $starttime = mktime(0, 0, 0, $beginArr[1], $beginArr[2], $beginArr[0]);
            $endArr = explode("-", $_GET['end']);
            $endtime = mktime(0, 0, 0, $endArr[1], $endArr[2], $endArr[0]);
            $condition = 'addtime>=' . $starttime . ' and addtime<=' . $endtime;
        } else {
            $condition = 'date_format(FROM_UNIXTIME(addtime), "%m-%Y") = date_format(now(), "%m-%Y")';
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $Coindetail = D("Coindetail");
        $count = $Coindetail->where('type="expend" and uid=' . $_SESSION['uid'] . ' and ' . $condition)->count();
        $listRows = 20;
        import("@.ORG.Page2");
        $p = new Page($count, $listRows, $linkFront);
        $consumes = $Coindetail->where('type="expend" and uid=' . $_SESSION['uid'] . ' and ' . $condition)->limit($p->firstRow . "," . $p->listRows)->order('addtime desc')->select();
        $page = $p->show();
        $this->assign('consumes', $consumes);
        $this->assign('count', $count);
        $pagecount = ceil($count / $listRows);
        if ($pagecount == 0) {
            $pagecount = 1;
        }
        $this->assign('pagecount', $pagecount);
        $this->assign('page', $page);
        $this->display();
    }

    public function getpresent()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $Giveaway = D("Giveaway");
        $count = $Giveaway->where('uid=0 and touid=' . $_SESSION['uid'])->count();
        $listRows = 10;
        import("@.ORG.Page3");
        $p = new Page($count, $listRows, $linkFront);
        $systemsendtome = $Giveaway->where('uid=0 and touid=' . $_SESSION['uid'])->limit($p->firstRow . "," . $p->listRows)->order('addtime desc')->select();
        $page = $p->show();
        $this->assign('systemsendtome', $systemsendtome);
        $this->assign('page', $page);
        $count2 = $Giveaway->where('uid>0 and touid=' . $_SESSION['uid'])->count();
        $listRows2 = 10;
        import("@.ORG.Page4");
        $p = new Page4($count2, $listRows2, $linkFront);
        $othersendtome = $Giveaway->where('uid>0 and touid=' . $_SESSION['uid'])->limit($p->firstRow . "," . $p->listRows)->order('addtime desc')->select();
        foreach ($othersendtome as $n => $val) {
            $othersendtome[$n]['voo']=D("Member")->where('id=' . $val['uid'])->select();
        }
        $page2 = $p->show();
        $this->assign('othersendtome', $othersendtome);
        $this->assign('page2', $page2);
        $this->display();
    }

    public function getSystemPresentation()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $Giveaway = D("Giveaway");
        $count = $Giveaway->where('uid=0 and touid=' . $_SESSION['uid'])->count();
        $listRows = 10;
        import("@.ORG.Page3");
        $p = new Page($count, $listRows, $linkFront);
        $systemsendtome = $Giveaway->where('uid=0 and touid=' . $_SESSION['uid'])->limit($p->firstRow . "," . $p->listRows)->order('addtime desc')->select();
        $page = $p->show();
        $this->assign('systemsendtome', $systemsendtome);
        $this->assign('page', $page);
        $this->display();
    }

    public function getEmceenoPresentation()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $Giveaway = D("Giveaway");
        $count2 = $Giveaway->where('uid>0 and touid=' . $_SESSION['uid'])->count();
        $listRows2 = 10;
        import("@.ORG.Page4");
        $p = new Page4($count2, $listRows2, $linkFront);
        $othersendtome = $Giveaway->where('uid>0 and touid=' . $_SESSION['uid'])->limit($p->firstRow . "," . $p->listRows)->order('addtime desc')->select();
        foreach ($othersendtome as $n => $val) {
            $othersendtome[$n]['voo'] = D("Member")->where('id=' . $val['uid'])->select();
        }
        $page2 = $p->show();
        $this->assign('othersendtome', $othersendtome);
        $this->assign('page2', $page2);
        $this->display();
    }
    
    public function myshowlist()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        if ($_GET['date'] != '') {
            $condition = 'date_format(FROM_UNIXTIME(starttime), "%Y%m")="' . $_GET['date'] . '"';
        } else {
            $condition = 'date_format(FROM_UNIXTIME(starttime), "%m-%Y") = date_format(now(), "%m-%Y")';
        }
        $liverecords = D('Liverecord')->query('SELECT date_format(FROM_UNIXTIME(starttime),"%Y年%m月%d日") as livedate FROM `ss_liverecord` where uid=' . $_SESSION['uid'] . ' and ' . $condition . ' group by livedate order by livedate desc');
        $this->assign('liverecords', $liverecords);
        $this->display();
    }
    
    public function listAward()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }
    
    public function bl_list()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function charge()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        if($_GET['ProxyUserID'] != ''){
            $proxyuserinfo = D("Member")->find($_GET['ProxyUserID']);
            if($proxyuserinfo){
                $proxyusername = $proxyuserinfo['nickname'];
                $proxyuserid = $proxyuserinfo['id'];
            }
            else{
                $proxyusername = '无';
                $proxyuserid = 0;
            }
            $this->assign('proxyusername', $proxyusername);
            $this->assign('proxyuserid', $proxyuserid);
        }
        $proxyusers = D('Member')->where('sellm="1"')->field('id,nickname')->order('id desc')->select();
        $this->assign('proxyusers', $proxyusers);
        $this->display();
    }

    public function ajaxcheckuser()
    {
        C('HTML_CACHE_ON', false);
        header("Content-type: text/html; charset=utf-8"); 
        $User = D("Member");
        if ($_GET["roomnum"] == '') {
            exit;
        } else {
            $userinfo = $User->where('curroomnum=' . $_GET["roomnum"] . '')->select();
            if ($userinfo) {
                echo $userinfo[0]['nickname'];
            } else {
                exit;
            }
        }
    }

    public function chargepay()
    {
        C('HTML_CACHE_ON', false);
        header("Content-type: text/html; charset=utf-8"); 
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        if ($_POST['c_ChargeType'] == '1') {
            $chargetouid = $_SESSION['uid'];
        } else {
            $touserinfo = D("Member")->where('curroomnum=' . $_POST["c_DestUserName"] . '')->select();
            if ($touserinfo) {
                $chargetouid = $touserinfo[0]['id'];
            } else {
                $chargetouid = $_SESSION['uid'];
            }
        }
        
        // 新增支付宝 即时到帐
        if ($_POST['c_PPPayID'] == 'alipay_d') {
                 // 获取后台设置的 配置信息
                 $siteconfig = M("siteconfig")->where("id='1'")->find();
                // ↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
                // 合作身份者id，以2088开头的16位纯数字
                $alipay_config['partner']        = $siteconfig['alipay_d_partner'];
                //安全检验码，以数字和字母组成的32位字符
                $alipay_config['key']            = $siteconfig['alipay_d_key'];
                $alipay_config['seller_email'] =$siteconfig['alipay_d_email'];
                //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
                //签名方式 不需修改
                $alipay_config['sign_type']    = strtoupper('MD5');
                //字符编码格式 目前支持 gbk 或 utf-8
                $alipay_config['input_charset']= strtolower('utf-8');
                //ca证书路径地址，用于curl中ssl校验
                //请保证cacert.pem文件在当前文件夹目录中
                $alipay_config['cacert']    = getcwd() . '\\cacert.pem';
                //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
                $alipay_config['transport']    = 'http';
                //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
                    require_once "./alipay_d/lib/alipay_submit.class.php";
                    ///支付记录
                    $Chargedetail = D("Chargedetail");
                    $Chargedetail->create();
                    $Chargedetail->uid = $_SESSION['uid'];
                    $Chargedetail->touid = $chargetouid;
                    $Chargedetail->rmb = $_POST['c_Money1'];
                    $Chargedetail->coin = $_POST['c_Money1'] * $this->ratio;
                    $Chargedetail->status = '0';
                    $Chargedetail->addtime = time();
                    $Chargedetail->orderno = $_SESSION['uid'] . '_' . $chargetouid . '_'.date('YmdHis');
                    if($_GET['ProxyUserID'] != ''){
                        $Chargedetail->proxyuid = $_GET['ProxyUserID'];
                    }
                    $detailId = $Chargedetail->add();    
                    //支付记录                        
            /**************************请求参数**************************/
                //支付类型
                $payment_type = "1";
                //必填，不能修改
                //服务器异步通知页面路径
                $notify_url = $this->siteurl ."/my/alipay_d_notify/";
                //需http://格式的完整路径，不能加?id=123这类自定义参数
                //页面跳转同步通知页面路径
                $return_url =  $this->siteurl ."/my/alipay_d_return/";
                //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
                //必填
                //商户订单号  //结构 充值记录 id _  充值用户uid _ 充值到的账户 UID  _ 时间戳
                $out_trade_no = $detailId . '_' . $_SESSION['uid'] . '_' . $chargetouid . '_'.date('YmdHis');  
                //商户网站订单系统中唯一订单号，必填
                //订单名称
                $subject =$_SESSION['username'];
                //必填
                //付款金额
                $total_fee = $_POST['c_Money1'];
                //必填
                //订单描述
                $body = $this->sitename."在线充值";
                //商品展示地址
                $show_url = $this->sitename;
                //需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
                //防钓鱼时间戳
                $anti_phishing_key = "";
                //若要使用请调用类文件submit中的query_timestamp函数
                //客户端的IP地址
                $exter_invoke_ip = "";
                //非局域网的外网IP地址，如：221.0.0.1
                /************************************************************/
                //构造要请求的参数数组，无需改动
                $parameter = array(
                        "service" => "create_direct_pay_by_user",
                        "partner" => trim($alipay_config['partner']),
                        "payment_type"    => $payment_type,
                        "notify_url"    => $notify_url,
                        "return_url"    => $return_url,
                        "seller_email"    => trim($alipay_config['seller_email']),
                        "out_trade_no"    => $out_trade_no,
                        "subject"    => $subject,
                        "total_fee"    => $total_fee,
                        "body"    => $body,
                        "show_url"    => $show_url,
                        "anti_phishing_key"    => $anti_phishing_key,
                        "exter_invoke_ip"    => $exter_invoke_ip,
                        "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
                );

                //建立请求
                $alipaySubmit = new AlipaySubmit($alipay_config);
                $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
                echo $html_text;
        }
        // 新增支付宝 即时到帐        
        if ($_POST['c_PPPayID'] == '1_ICBC-NET-B2C') {
            $merchantAcctId = $this->bill_MerchantAcctID;
            $key = $this->bill_key;
            $inputCharset = "1";
            $bgUrl = $this->siteurl . "/my/payreceive/";
            $version = "v2.0";
            $language = "1";
            $signType = "1";
            $payerName = $_SESSION['username'];
            $payerContactType = "1";
            $payerContact = "";
            $orderId = date('YmdHis');
            $orderAmount = $_POST['c_Money1'] * 100;
            $orderTime = date('YmdHis');
            $productName = $this->sitename . "在线充值";
            $productNum = "1";
            $productId = "";
            $productDesc = $this->sitename."在线充值";
            $ext1 = "";
            $ext2 = "";
            $payType = "00";
            $redoFlag = "0";
            $pid = "";
            $signMsgVal = $this->appendParam($signMsgVal, "inputCharset", $inputCharset);
            $signMsgVal = $this->appendParam($signMsgVal, "bgUrl", $bgUrl);
            $signMsgVal = $this->appendParam($signMsgVal, "version", $version);
            $signMsgVal = $this->appendParam($signMsgVal, "language", $language);
            $signMsgVal = $this->appendParam($signMsgVal, "signType", $signType);
            $signMsgVal = $this->appendParam($signMsgVal, "merchantAcctId", $merchantAcctId);
            $signMsgVal = $this->appendParam($signMsgVal, "payerName", $payerName);
            $signMsgVal = $this->appendParam($signMsgVal, "payerContactType", $payerContactType);
            $signMsgVal = $this->appendParam($signMsgVal, "payerContact", $payerContact);
            $signMsgVal = $this->appendParam($signMsgVal, "orderId", $orderId);
            $signMsgVal = $this->appendParam($signMsgVal, "orderAmount", $orderAmount);
            $signMsgVal = $this->appendParam($signMsgVal, "orderTime", $orderTime);
            $signMsgVal = $this->appendParam($signMsgVal, "productName", $productName);
            $signMsgVal = $this->appendParam($signMsgVal, "productNum", $productNum);
            $signMsgVal = $this->appendParam($signMsgVal, "productId", $productId);
            $signMsgVal = $this->appendParam($signMsgVal, "productDesc", $productDesc);
            $signMsgVal = $this->appendParam($signMsgVal, "ext1", $ext1);
            $signMsgVal = $this->appendParam($signMsgVal, "ext2", $ext2);
            $signMsgVal = $this->appendParam($signMsgVal, "payType", $payType);
            $signMsgVal = $this->appendParam($signMsgVal, "redoFlag", $redoFlag);
            $signMsgVal = $this->appendParam($signMsgVal, "pid", $pid);
            $signMsgVal = $this->appendParam($signMsgVal, "key", $key);
            $signMsg = strtoupper(md5($signMsgVal));
            $Chargedetail = D("Chargedetail");
            $Chargedetail->create();
            $Chargedetail->uid = $_SESSION['uid'];
            $Chargedetail->touid = $chargetouid;
            $Chargedetail->rmb = $_POST['c_Money1'];
            $Chargedetail->coin = $_POST['c_Money1'] * $this->ratio;
            $Chargedetail->status = '0';
            $Chargedetail->addtime = time();
            $Chargedetail->orderno = $orderId;
            if($_GET['ProxyUserID'] != ''){
                $Chargedetail->proxyuid = $_GET['ProxyUserID'];
            }
            $detailId = $Chargedetail->add();
            echo '<form name="kqPay" method="post" action="https://www.99bill.com/gateway/recvMerchantInfoAction.htm">';
            echo '    <input type="hidden" name="inputCharset" value="' . $inputCharset . '"/>';
            echo '    <input type="hidden" name="bgUrl" value="' . $bgUrl . '"/>';
            echo '    <input type="hidden" name="version" value="' . $version . '"/>';
            echo '    <input type="hidden" name="language" value="' . $language . '"/>';
            echo '    <input type="hidden" name="signType" value="' . $signType . '"/>';
            echo '    <input type="hidden" name="signMsg" value="' . $signMsg . '"/>';
            echo '    <input type="hidden" name="merchantAcctId" value="' . $merchantAcctId . '"/>';
            echo '    <input type="hidden" name="payerName" value="' . $payerName . '"/>';
            echo '    <input type="hidden" name="payerContactType" value="' . $payerContactType . '"/>';
            echo '    <input type="hidden" name="payerContact" value="' . $payerContact . '"/>';
            echo '    <input type="hidden" name="orderId" value="' . $orderId . '"/>';
            echo '    <input type="hidden" name="orderAmount" value="' . $orderAmount . '"/>';
            echo '    <input type="hidden" name="orderTime" value="' . $orderTime . '"/>';
            echo '    <input type="hidden" name="productName" value="' . $productName . '"/>';
            echo '    <input type="hidden" name="productNum" value="' . $productNum . '"/>';
            echo '    <input type="hidden" name="productId" value="' . $productId . '"/>';
            echo '    <input type="hidden" name="productDesc" value="' . $productDesc . '"/>';
            echo '    <input type="hidden" name="ext1" value="' . $ext1 . '"/>';
            echo '    <input type="hidden" name="ext2" value="' . $ext2 . '"/>';
            echo '    <input type="hidden" name="payType" value="' . $payType . '"/>';
            echo '    <input type="hidden" name="redoFlag" value="' . $redoFlag . '"/>';
            echo '    <input type="hidden" name="pid" value="' . $pid . '"/>';
            echo '</form>';
            echo '<script type="text/javascript">';
            echo "    document.forms['kqPay'].submit();";
            echo '</script>';
        }
        //--------------支付宝--------------------------------------------------------------
        if ($_POST['c_PPPayID'] == '17_JIUYOU-NET') {
            include "./alipay/alipay.config.php";
            include "./alipay/lib/alipay_submit.class.php";
             //支付类型
            $service="create_direct_pay_by_user";
            $payment_type = "1";
            //必填，不能修改
            //服务器异步通知页面路径
             $notify_url = $this->siteurl ."/my/apayreceive/";
            //需http://格式的完整路径，不能加?id=123这类自定义参数
            //页面跳转同步通知页面路径
            $return_url = $this->siteurl ."/my/apayreceive/";
            //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
            //商户订单号
            $out_trade_no = date('YmdHis');
            //商户网站订单系统中唯一订单号，必填
            //订单名称
            $subject = $_SESSION['username'];
            //必填
            //付款金额
            $total_fee = $_POST['c_Money1'];
            //必填
            //订单描述
            $body = $this->sitename . "在线充值";
            //商品展示地址
            $show_url = $this->sitename;
            //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
            //防钓鱼时间戳
            $anti_phishing_key = "";
            //若要使用请调用类文件submit中的query_timestamp函数
            //客户端的IP地址
            $exter_invoke_ip = "";
            //非局域网的外网IP地址，如：221.0.0.1
            $Chargedetail = D("Chargedetail");
            $Chargedetail->create();
            $Chargedetail->uid = $_SESSION['uid'];
            $Chargedetail->touid = $chargetouid;
            $Chargedetail->rmb = $_POST['c_Money1'];
            $Chargedetail->coin = $_POST['c_Money1'] * $this->ratio;
            $Chargedetail->status = '0';
            $Chargedetail->addtime = time();
            $Chargedetail->orderno = $out_trade_no;
            if ($_GET['ProxyUserID'] != '') {
                $Chargedetail->proxyuid = $_GET['ProxyUserID'];
            }
            $detailId = $Chargedetail->add();
            // 构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => "create_direct_pay_by_user",
                "partner" => trim($alipay_config['partner']),
                "seller_email" => trim($alipay_config['seller_email']),
                "payment_type"    => $payment_type,
                "notify_url"    => $notify_url,
                "return_url"    => $return_url,
                "out_trade_no"    => $out_trade_no,
                "subject"    => $subject,
                "total_fee"    => $total_fee,
                "body"    => $body,
                "show_url"    => $show_url,
                "anti_phishing_key"    => $anti_phishing_key,
                "exter_invoke_ip"    => $exter_invoke_ip,
                "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
            );
            //建立请求
            $alipaySubmit = new AlipaySubmit($alipay_config);
            $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认支付");
            echo $html_text;
        }
//-----------------------------------------------------------------
        if ($_POST['c_PPPayID'] == '14_SZX-NET' ) {
            if ($_POST['c_PPPayID'] == '14_SZX-NET') {
                if ($_POST['paycardType'] == 'chinamobile') {
                    $merchantAcctId = "1002225194002";
                    $key = "J6B5GECXJTK7CJFS";
                }
                if ($_POST['paycardType'] == 'chinaunion') {
                    $merchantAcctId = "1002225194003";
                    $key = "5CD8UKG7I8LGRWCM";
                }
                if ($_POST['paycardType'] == 'chinatelecom') {
                    $merchantAcctId = "1002225194004";
                    $key = "LH4RAD7NXSDNYF5B";
                }
            }
            if ($_POST['c_PPPayID'] == '17_JIUYOU-NET') {
                if ($_POST['gamecardType'] == 'zongyou') {
                    $merchantAcctId = "1002225194010";
                    $key = "54HHYTGSII9ZW2HW";
                }
                if ($_POST['gamecardType'] == 'netease') {
                    $merchantAcctId = "1002225194009";
                    $key = "YF6MWZW4Q35EXEQX";
                }
                if($_POST['gamecardType'] == 'sohu'){
                    $merchantAcctId="1002225194008";
                    $key="YDI8US7J97FSKR7F";
                }
                if ($_POST['gamecardType'] == 'wanmei') {
                    $merchantAcctId = "1002225194007";
                    $key = "7S94QYTU4EXWUUF8";
                }
                if ($_POST['gamecardType'] == 'snda') {
                    $merchantAcctId = "1002225194006";
                    $key = "Z2HYNHZYR4GRFMNS";
                }
                if ($_POST['gamecardType'] == 'junnet') {
                    $merchantAcctId = "1002225194005";
                    $key = "SDD9JIUHJFNQJK7J";
                }
            }
            $inputCharset = "1";
            $bgUrl = $this->siteurl . "/my/card_payreceive/";
            $pageUrl = "";
            $version = "v2.0";
            $language = "1";
            $signType = "1";
            $payerName = $_SESSION['username'];
            $payerContactType = "1";
            $payerContact = "";
            $orderId = date('YmdHis');
            $orderAmount = $_POST['c_Money1'] * 100;
            $payType ="42";
            //$cardNumber=$this->encrypt($_POST['paycard_num'],$key);
            //$cardPwd=$this->encrypt($_POST['paycard_psw'],$key);
            $cardNumber = "";
            $cardPwd = "";
            $fullAmountFlag = "0";
            $orderTime = date('YmdHis');
            $productName = urlencode($this->sitename . '在线充值');
            $productNum = "1";
            $productId = "";
            $productDesc = urlencode($this->sitename . '在线充值');
            $ext1 = "";
            $ext2 = "";
            if ($_POST['c_PPPayID'] == '14_SZX-NET') {
                if ($_POST['paycardType'] == 'chinamobile') {
                    $bossType = "0";
                }
                if ($_POST['paycardType'] == 'chinaunion') {
                    $bossType = "1";
                }
                if ($_POST['paycardType'] == 'chinatelecom') {
                    $bossType = "3";
                }
            }
            if ($_POST['c_PPPayID'] == '17_JIUYOU-NET') {
                if ($_POST['gamecardType'] == 'zongyou') {
                    $bossType = "15";
                }
                if ($_POST['gamecardType'] == 'netease') {
                    $bossType = "14";
                }
                if ($_POST['gamecardType'] == 'sohu') {
                    $bossType = "13";
                }
                if ($_POST['gamecardType'] == 'wanmei') {
                    $bossType = "12";
                }
                if ($_POST['gamecardType'] == 'snda') {
                    $bossType = "10";
                }
                if ($_POST['gamecardType'] == 'junnet') {
                    $bossType = "4";
                }
            }
            $signMsgVal = $this->appendParam($signMsgVal, "inputCharset", $inputCharset);
            $signMsgVal = $this->appendParam($signMsgVal, "bgUrl", $bgUrl);
            $signMsgVal = $this->appendParam($signMsgVal, "pageUrl", $pageUrl);
            $signMsgVal = $this->appendParam($signMsgVal, "version", $version);
            $signMsgVal = $this->appendParam($signMsgVal, "language", $language);
            $signMsgVal = $this->appendParam($signMsgVal, "signType", $signType);
            $signMsgVal = $this->appendParam($signMsgVal, "merchantAcctId", $merchantAcctId);
            $signMsgVal = $this->appendParam($signMsgVal, "payerName", $payerName);
            $signMsgVal = $this->appendParam($signMsgVal, "payerContactType", $payerContactType);
            $signMsgVal = $this->appendParam($signMsgVal, "payerContact", $payerContact);
            $signMsgVal = $this->appendParam($signMsgVal, "orderId", $orderId);
            $signMsgVal = $this->appendParam($signMsgVal, "orderAmount", $orderAmount);
            $signMsgVal = $this->appendParam($signMsgVal, "payType", $payType);
            $signMsgVal = $this->appendParam($signMsgVal, "cardNumber", $cardNumber);
            $signMsgVal = $this->appendParam($signMsgVal, "cardPwd", $cardPwd);
            $signMsgVal = $this->appendParam($signMsgVal, "fullAmountFlag", $fullAmountFlag);
            $signMsgVal = $this->appendParam($signMsgVal, "orderTime", $orderTime);
            $signMsgVal = $this->appendParam($signMsgVal, "productName", $productName);
            $signMsgVal = $this->appendParam($signMsgVal, "productNum", $productNum);
            $signMsgVal = $this->appendParam($signMsgVal, "productId", $productId);
            $signMsgVal = $this->appendParam($signMsgVal, "productDesc", $productDesc);
            $signMsgVal = $this->appendParam($signMsgVal, "ext1", $ext1);
            $signMsgVal = $this->appendParam($signMsgVal, "ext2", $ext2);
            $signMsgVal = $this->appendParam($signMsgVal, "bossType", $bossType);
            $signMsgVal = $this->appendParam($signMsgVal, "key", $key);
            $signMsg = strtoupper(md5($signMsgVal));
            $Chargedetail = D("Chargedetail");
            $Chargedetail->create();
            $Chargedetail->uid = $_SESSION['uid'];
            $Chargedetail->touid = $chargetouid;
            $Chargedetail->rmb = $_POST['c_Money1'];
            $Chargedetail->coin = $_POST['c_Money1'] * $this->ratio;
            $Chargedetail->status = '0';
            $Chargedetail->addtime = time();
            $Chargedetail->orderno = $orderId;
            if ($_GET['ProxyUserID'] != '') {
                $Chargedetail->proxyuid = $_GET['ProxyUserID'];
            }
            $detailId = $Chargedetail->add();
            echo '<form name="kqPay" method="post" action="http://www.99bill.com/szxgateway/recvMerchantInfoAction.htm">';
            echo '    <input type="hidden" name="inputCharset" value="' . $inputCharset . '"/>';
            echo '    <input type="hidden" name="bgUrl" value="' . $bgUrl . '"/>';
            echo '    <input type="hidden" name="pageUrl" value="' . $pageUrl . '">';
            echo '    <input type="hidden" name="version" value="' . $version . '"/>';
            echo '    <input type="hidden" name="language" value="' . $language . '"/>';
            echo '    <input type="hidden" name="signType" value="' . $signType . '"/>';
            echo '    <input type="hidden" name="merchantAcctId" value="' . $merchantAcctId . '"/>';
            echo '    <input type="hidden" name="payerName" value="' . $payerName . '"/>';
            echo '    <input type="hidden" name="payerContactType" value="' . $payerContactType . '"/>';
            echo '    <input type="hidden" name="payerContact" value="' . $payerContact . '"/>';
            echo '    <input type="hidden" name="orderId" value="' . $orderId . '"/>';
            echo '    <input type="hidden" name="orderAmount" value="' . $orderAmount . '"/>';
            echo '    <input type="hidden" name="payType" value="' . $payType . '"/>';
            echo '    <input type="hidden" name="cardNumber" value="' . $cardNumber . '">';
            echo '    <input type="hidden" name="cardPwd" value="' . $cardPwd . '">';
            echo '    <input type="hidden" name="fullAmountFlag" value="' . $fullAmountFlag . '">';
            echo '    <input type="hidden" name="orderTime" value="' . $orderTime . '"/>';
            echo '    <input type="hidden" name="productName" value="' . $productName . '"/>';
            echo '    <input type="hidden" name="productNum" value="' . $productNum . '"/>';
            echo '    <input type="hidden" name="productId" value="' . $productId . '"/>';
            echo '    <input type="hidden" name="productDesc" value="' . $productDesc . '"/>';
            echo '    <input type="hidden" name="ext1" value="' . $ext1 . '"/>';
            echo '    <input type="hidden" name="ext2" value="' . $ext2 . '"/>';
            echo '    <input type="hidden" name="bossType" value="' . $bossType . '"/>';
            echo '    <input type="hidden" name="signMsg" value="' . $signMsg . '"/>';
            echo '</form>';
            echo '<script type="text/javascript">';
            echo "    document.forms['kqPay'].submit();";
            echo '</script>';
        }
    }

    public function dumppost(){
        dump($_POST);
    }
    
    //支付宝即时到帐  返回处理
    public function alipay_d_return(){
        
    }    

    public function alipay_d_notify()
    {
        //获取后台设置的 配置信息
        $siteconfig = M("siteconfig")->where("id='1'")->find();
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']        = $siteconfig['alipay_d_partner'];
        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key']            = $siteconfig['alipay_d_key'];
        $alipay_config['seller_email'] = $siteconfig['alipay_d_email'];
        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('MD5');
        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset'] = strtolower('utf-8');
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd() . '\\cacert.pem';
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        require_once("./alipay_d/lib/alipay_notify.class.php");
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];
            //交易金额
            $total_fee = $_POST['total_fee'];
            //充值判断修改状态
            $orderid = explode("_", $out_trade_no);
            $id = $orderid[0];
            $uid = $orderid[1];
            $touid = $orderid[2];
            $data = array();
            $data['status'] = '1';
            $data['dealId'] = $trade_no;
            //更新记录
            M("Chargedetail")->where("id='$id' and uid='$uid' and touid='$touid' ")->save($data);
            D("Member")->execute('update ss_member set coinbalance=coinbalance+' . ($total_fee * $this->ratio) . ' where id=' . $touid);
            //更新会员余额
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";        //请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }    
    }    

    //支付宝即时到帐  返回处理    
    
//------------------------支付宝返回处理----------------------------------------------

public function apayreceive(){
        C('HTML_CACHE_ON', false);
include "./alipay/alipay.config.php";
include "./alipay/lib/alipay_notify.class.php";
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();        
        $rtnOk=0;
        $rtnUrl = "";
        if($verify_result) {//验证成功
        //商户订单号
    $out_trade_no = $_REQUEST['out_trade_no'];
$orderId=$out_trade_no;
    //支付宝交易号
    $trade_no = $_REQUEST['trade_no'];
    $dealId=$trade_no;
    $total_fee = $_REQUEST['total_fee'];
    $payAmount=$total_fee;
    //交易状态
    $trade_status = $_REQUEST['trade_status'];
        echo $trade_status;
            switch($trade_status){
                case "TRADE_SUCCESS":
                    $chargeinfo = D("Chargedetail")->where('orderno="' . $orderId . '"')->select();
                    //$rows = M('Chargedetail')->where('uid="' . $chargeinfo[0]["uid"] . '"')->select();
                    $rows = M('Chargedetail')->where(array('uid'=>$chargeinfo[0]["uid"],'dealId'=>array('neq','')))->select();
                    if($chargeinfo && $chargeinfo[0]['status'] == '0'){
                        if(!count($rows)){//判断是不是首充
                            $cartime = 24*3600*30;
                            if($chargeinfo[0]['rmb']<200){
                                $dutime = 24*3600*7;
                                if($userinfo['daoju14expire'] < time()){//用于更新时间
                                D("Member")->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju7="y",Daoju7expire='.(time()+$cartime) . '  where id=' . $chargeinfo[0]['touid']);
                            } else {
                                D("Member")->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju7="y",Daoju7expire=Daoju14expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                            }
                            }elseif($chargeinfo[0]['rmb']>199 and $chargeinfo[0]['rmb']<500){//充值200元送小毛驴,注意每个道具在数据库有对应的字段
                                    $dutime = 24*3600*30;
                                    if($userinfo['daoju12expire'] < time()){//用于更新时间
                                    D('Member')->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju12="y",Daoju12expire='.(time()+$cartime) . ' where id=' . $chargeinfo[0]['touid']);
                                } else {
                                    D('Member')->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju12="y",Daoju12expire=Daoju12expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                                }
                            }elseif($chargeinfo[0]['rmb']>499 and $chargeinfo[0]['rmb']<1000){//充值500元送悍马,注意每个道具在数据库有对应的字段
                                    $dutime = 24*3600*30;
                                    if($userinfo['daoju10expire'] < time()){//用于更新时间
                                    D('Member')->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju4="y",Daoju4expire='.(time()+$cartime) . ' where id=' . $chargeinfo[0]['touid']);
                                } else {
                                    D('Member')->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju4="y",Daoju4expire=Daoju4expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                                }
                        } else {
                                $this->sendLiang($chargeinfo[0]['touid']);//送靓号
                                if($userinfo['daoju5expire'] < time()){//用于更新时间
                                D("Member")->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju1=y,Daoju1expire='.(time()+$cartime) . ' where id=' . $chargeinfo[0]['touid']);
                            } else {
                                D("Member")->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju1=y,Daoju1expire=Daoju5expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                            }
                        }
                    }
                        D("Chargedetail")->execute('update ss_chargedetail set dealId="' . $dealId . '",status="1" where orderno="' . $orderId . '"');
                        D("Member")->execute('update ss_member set coinbalance=coinbalance+'.($payAmount*$this->ratio) . ' where id=' . $chargeinfo[0]['touid']);
                        if($chargeinfo[0]['touid'] != $chargeinfo[0]['uid']){
                            $Giveaway = D("Giveaway");
                            $Giveaway->create();
                            $Giveaway->uid = $chargeinfo[0]['uid'];
                            $Giveaway->touid = $chargeinfo[0]['touid'];
                            $Giveaway->content = ($payAmount*$this->ratio) . '梦想币';
                            $Giveaway->objectIcon = '/style/images/coin.png';
                            $giveId = $Giveaway->add();
                        }
                        //充值代理
                        if($chargeinfo[0]['proxyuid'] != 0){
                            $beannum = ceil((($payAmount)*$this->ratio) * ($this->payagentdeduct / 100));
                            //D("Member")->execute('update ss_member set earnbean=earnbean+' . $beannum . ',beanbalance=beanbalance+' . $beannum . ' where id=' . $chargeinfo[0]['proxyuid']);
                            D("Member")->execute('update ss_member set beanbalance3=beanbalance3+' . $beannum . ' where id=' . $chargeinfo[0]['proxyuid']);
                            $Payagentbeandetail = D("Payagentbeandetail");
                            $Payagentbeandetail->create();
                            $Payagentbeandetail->type = 'income';
                            $Payagentbeandetail->action = 'charge';
                            $Payagentbeandetail->uid = $chargeinfo[0]['proxyuid'];
                            $Payagentbeandetail->content = '充值代理收入';
                            $Payagentbeandetail->bean = $beannum;
                            $Payagentbeandetail->addtime = time();
                            $detailId = $Payagentbeandetail->add();
                        }
                    }
                    $rtnOk = 1;
                    $rtnUrl = $this->siteurl . "/my/payresult/type/success/";
                    break;
                default:
                    $rtnOk = 1;
                    $rtnUrl = $this->siteurl . "/my/payresult/type/error/";
                    break;
            }
        }
        echo "<script>alert('会员充值成功');window.close();</script>";
        exit;
    }

    public function payreceive(){
        C('HTML_CACHE_ON', false);
        $merchantAcctId=trim($_REQUEST['merchantAcctId']);
        $key=$this->bill_key;
        $version=trim($_REQUEST['version']);
        $language=trim($_REQUEST['language']);
        $signType=trim($_REQUEST['signType']);
        $payType=trim($_REQUEST['payType']);
        $bankId=trim($_REQUEST['bankId']);
        $orderId=trim($_REQUEST['orderId']);
        $orderTime=trim($_REQUEST['orderTime']);
        $orderAmount=trim($_REQUEST['orderAmount']);
        $dealId=trim($_REQUEST['dealId']);
        $bankDealId=trim($_REQUEST['bankDealId']);
        $dealTime=trim($_REQUEST['dealTime']);
        $payAmount=trim($_REQUEST['payAmount']);
        $fee=trim($_REQUEST['fee']);
        $ext1=trim($_REQUEST['ext1']);
        $ext2=trim($_REQUEST['ext2']);
        $payResult=trim($_REQUEST['payResult']);
        $errCode=trim($_REQUEST['errCode']);
        $signMsg=trim($_REQUEST['signMsg']);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"merchantAcctId", $merchantAcctId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"version", $version);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"language", $language);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"signType", $signType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payType", $payType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bankId", $bankId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderId", $orderId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderTime", $orderTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderAmount", $orderAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealId", $dealId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bankDealId", $bankDealId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealTime", $dealTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payAmount", $payAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"fee", $fee);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext1", $ext1);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext2", $ext2);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payResult", $payResult);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"errCode", $errCode);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"key", $key);
        $merchantSignMsg= md5($merchantSignMsgVal);
        $rtnOk=0;
        $rtnUrl = "";
        if(strtoupper($signMsg)==strtoupper($merchantSignMsg)){
            switch($payResult){
                case "10":
                    $chargeinfo = D("Chargedetail")->where('orderno="' . $orderId . '"')->select();
                    $rows = M('Chargedetail')->where(array('uid'=>$chargeinfo[0]["uid"],'dealId'=>array('neq','')))->select();
                    if($chargeinfo && $chargeinfo[0]['status'] == '0'){
                            if(!count($rows)){//判断是不是首充
                                $cartime = 24*3600*30;
                                if($chargeinfo[0]['rmb']<200){
                                    $dutime = 24*3600*7;
                                    if($userinfo['daoju14expire'] < time()){//用于更新时间
                                    D("Member")->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju7="y",Daoju7expire='.(time()+$cartime) . '  where id=' . $chargeinfo[0]['touid']);
                                } else {
                                    D("Member")->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju7="y",Daoju7expire=Daoju14expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                                }
                                }elseif($chargeinfo[0]['rmb']>199 and $chargeinfo[0]['rmb']<500){//充值200元送小毛驴,注意每个道具在数据库有对应的字段
                                        $dutime = 24*3600*30;
                                        if($userinfo['daoju12expire'] < time()){//用于更新时间
                                        D('Member')->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju12="y",Daoju12expire='.(time()+$cartime) . ' where id=' . $chargeinfo[0]['touid']);
                                    } else {
                                        D('Member')->execute('update ss_member set vip=2,vipexpire='.(time()+$dutime) . ',Daoju12="y",Daoju12expire=Daoju12expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                                    }
                                }elseif($chargeinfo[0]['rmb']>500 and $chargeinfo[0]['rmb']<1000){//充值500元送悍马,注意每个道具在数据库有对应的字段
                                        $dutime = 24*3600*30;
                                        if($userinfo['daoju10expire'] < time()){//用于更新时间
                                        D('Member')->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju4="y",Daoju4expire='.(time()+$cartime) . ' where id=' . $chargeinfo[0]['touid']);
                                    } else {
                                        D('Member')->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju4="y",Daoju4expire=Daoju4expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                                    }
                            } else {
                                    $this->sendLiang($chargeinfo[0]['touid']);//送靓号
                                    if($userinfo['daoju5expire'] < time()){//用于更新时间
                                    D("Member")->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju1=y,Daoju1expire='.(time()+$cartime) . ' where id=' . $chargeinfo[0]['touid']);
                                } else {
                                    D("Member")->execute('update ss_member set vip=1,vipexpire='.(time()+$dutime) . ',Daoju1=y,Daoju1expire=daoju5expire+' . $cartime . ' where id=' . $chargeinfo[0]['touid']);
                                }
                            }
                        }//充值赠送代码结束
                        D("Chargedetail")->execute('update ss_chargedetail set dealId="' . $dealId . '",status="1" where orderno="' . $orderId . '"');
                        D("Member")->execute('update ss_member set coinbalance=coinbalance+'.(($payAmount/100)*$this->ratio) . ' where id=' . $chargeinfo[0]['touid']);
                        if($chargeinfo[0]['touid'] != $chargeinfo[0]['uid']){
                            $Giveaway = D("Giveaway");
                            $Giveaway->create();
                            $Giveaway->uid = $chargeinfo[0]['uid'];
                            $Giveaway->touid = $chargeinfo[0]['touid'];
                            $Giveaway->content = (($payAmount/100)*$this->ratio) . '梦想币';
                            $Giveaway->objectIcon = '/style/images/coin.png';
                            $giveId = $Giveaway->add();
                        }
                        //充值代理
                        if($chargeinfo[0]['proxyuid'] != 0){
                            $beannum = ceil((($payAmount/100)*$this->ratio) * ($this->payagentdeduct / 100));
                            //D("Member")->execute('update ss_member set earnbean=earnbean+' . $beannum . ',beanbalance=beanbalance+' . $beannum . ' where id=' . $chargeinfo[0]['proxyuid']);
                            D("Member")->execute('update ss_member set beanbalance3=beanbalance3+' . $beannum . ' where id=' . $chargeinfo[0]['proxyuid']);
                            $Payagentbeandetail = D("Payagentbeandetail");
                            $Payagentbeandetail->create();
                            $Payagentbeandetail->type = 'income';
                            $Payagentbeandetail->action = 'charge';
                            $Payagentbeandetail->uid = $chargeinfo[0]['proxyuid'];
                            $Payagentbeandetail->content = '充值代理收入';
                            $Payagentbeandetail->bean = $beannum;
                            $Payagentbeandetail->addtime = time();
                            $detailId = $Payagentbeandetail->add();
                        }
                    }
                    $rtnOk = 1;
                    $rtnUrl = $this->siteurl . "/my/payresult/type/success/";
                    break;
                default:
                    $rtnOk = 1;
                    $rtnUrl = $this->siteurl . "/my/payresult/type/error/";
                    break;
            }
        }
        else{
            $rtnOk = 1;
            $rtnUrl = $this->siteurl . "/my/payresult/type/error/";
        }
        echo '<result>' . $rtnOk . '</result><redirecturl>' . $rtnUrl . '</redirecturl>';
        exit;
    }

    public function card_payreceive(){
        C('HTML_CACHE_ON', false);
        $merchantAcctId=trim($_REQUEST['merchantAcctId']);
        if($_REQUEST['merchantAcctId'] == '1002225194010'){
            $key='54HHYTGSII9ZW2HW';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194009'){
            $key='YF6MWZW4Q35EXEQX';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194008'){
            $key='YDI8US7J97FSKR7F';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194007'){
            $key='7S94QYTU4EXWUUF8';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194006'){
            $key='Z2HYNHZYR4GRFMNS';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194004'){
            $key='LH4RAD7NXSDNYF5B';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194005'){
            $key='SDD9JIUHJFNQJK7J';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194003'){
            $key='5CD8UKG7I8LGRWCM';
        }
        if($_REQUEST['merchantAcctId'] == '1002225194002'){
            $key='J6B5GECXJTK7CJFS';
        }
        $version=trim($_REQUEST['version']);
        $language=trim($_REQUEST['language']);
        $payType=trim($_REQUEST['payType']);
        $cardNumber=trim($_REQUEST['cardNumber']);
        $cardPwd=trim($_REQUEST['cardPwd']);
        $orderId=trim($_REQUEST['orderId']);
        $orderAmount=trim($_REQUEST['orderAmount']);
        $dealId=trim($_REQUEST['dealId']);
        $orderTime=trim($_REQUEST['orderTime']);
        $ext1=trim($_REQUEST['ext1']);
        $ext2=trim($_REQUEST['ext2']);
        $payAmount=trim($_REQUEST['payAmount']);
        $billOrderTime=trim($_REQUEST['billOrderTime']);
        $payResult=trim($_REQUEST['payResult']);
        $bossType=trim($_REQUEST['bossType']);
        $receiveBossType=trim($_REQUEST['receiveBossType']);
        $receiverAcctId=trim($_REQUEST['receiverAcctId']);
        $signType=trim($_REQUEST['signType']);
        $signMsg=trim($_REQUEST['signMsg']);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"merchantAcctId", $merchantAcctId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"version", $version);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"language", $language);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payType", $payType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"cardNumber", $cardNumber);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"cardPwd", $cardPwd);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderId", $orderId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderAmount", $orderAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealId", $dealId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderTime", $orderTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext1", $ext1);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext2", $ext2);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payAmount", $payAmount);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"billOrderTime", $billOrderTime);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payResult", $payResult);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"signType", $signType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bossType", $bossType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"receiveBossType", $receiveBossType);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"receiverAcctId", $receiverAcctId);
        $merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"key", $key);
        $merchantSignMsg= md5($merchantSignMsgVal);
        $rtnOk=0;
        $rtnUrl = "";
        if(strtoupper($signMsg)==strtoupper($merchantSignMsg)){
            switch($payResult){
                case "10":
                    $chargeinfo = D("Chargedetail")->where('orderno="' . $orderId . '"')->select();
                    if ($chargeinfo && $chargeinfo[0]['status'] == '0') {
                        D("Chargedetail")->execute('update ss_chargedetail set dealId="' . $dealId . '",status="1" where orderno="' . $orderId . '"');
                        D("Member")->execute('update ss_member set coinbalance=coinbalance+'.(($payAmount/100)*$this->ratio) . ' where id=' . $chargeinfo[0]['touid']);
                        if ($chargeinfo[0]['touid'] != $chargeinfo[0]['uid']) {
                            $Giveaway = D("Giveaway");
                            $Giveaway->create();
                            $Giveaway->uid = $chargeinfo[0]['uid'];
                            $Giveaway->touid = $chargeinfo[0]['touid'];
                            $Giveaway->content = (($payAmount / 100) * $this->ratio) . '梦想币';
                            $Giveaway->objectIcon = '/style/images/coin.png';
                            $giveId = $Giveaway->add();
                        }
                        // 充值代理
                        if ($chargeinfo[0]['proxyuid'] != 0) {
                            $beannum = ceil((($payAmount / 100) * $this->ratio) * ($this->payagentdeduct / 100));
                            //D("Member")->execute('update ss_member set earnbean=earnbean+' . $beannum . ',beanbalance=beanbalance+' . $beannum . ' where id=' . $chargeinfo[0]['proxyuid']);
                            D("Member")->execute('update ss_member set beanbalance3=beanbalance3+' . $beannum . ' where id=' . $chargeinfo[0]['proxyuid']);
                            $Payagentbeandetail = D("Payagentbeandetail");
                            $Payagentbeandetail->create();
                            $Payagentbeandetail->type = 'income';
                            $Payagentbeandetail->action = 'charge';
                            $Payagentbeandetail->uid = $chargeinfo[0]['proxyuid'];
                            $Payagentbeandetail->content = '充值代理收入';
                            $Payagentbeandetail->bean = $beannum;
                            $Payagentbeandetail->addtime = time();
                            $detailId = $Payagentbeandetail->add();
                        }
                    }
                    $rtnOk = 1;
                    $rtnUrl = $this->siteurl . "/my/payresult/type/success/";
                    break;
                default:
                    $rtnOk = 1;
                    $rtnUrl = $this->siteurl . "/my/payresult/type/error/";
                    break;
            }
        } else {
            $rtnOk = 1;
            $rtnUrl = $this->siteurl . "/my/payresult/type/error/";
        }
        echo '<result>' . $rtnOk . '</result><redirecturl>' . $rtnUrl . '</redirecturl>';
        exit;
    }

    public function payresult()
    {
        C('HTML_CACHE_ON', false);
        header("Content-type: text/html; charset=utf-8");
        if ($_GET['type'] == 'success') {
            echo '充值成功 <a href="' . __URL__ . '/chargelist/">返回</a>';
        } else {
            echo '充值失败 <a href="' . __URL__ . '/chargelist/">返回</a>';
        }
    }

    private function appendParam($returnStr, $paramId, $paramValue)
    {
        C('HTML_CACHE_ON', false);
        if ($returnStr != "") {
            if ($paramValue != "") {
                $returnStr.= "&" . $paramId . "=" . $paramValue;
            }
        } else {
            if ($paramValue != "") {
                $returnStr = $paramId . "=" . $paramValue;
            }
        }
        return $returnStr;
    }

    public function encrypt($encrypt, $key = "")
    {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $encrypt, MCRYPT_MODE_ECB, $iv);
        $encode = base64_encode($passcrypt);
        return $encode;
    }

    public function decrypt($decrypt, $key = "")
    {
        $decoded = base64_decode($decrypt);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_ECB, $iv);
        return $decrypted;
    }

    public function userbalance()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function chargelist()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $Chargedetail = D("Chargedetail");
        $condition = "uid=" . $_SESSION['uid'];
        if ($_GET['c_StartTime'] != '') {
            $timeArr = explode("-", $_GET['c_StartTime']);
            $unixtime = mktime(0, 0, 0, $timeArr[1], $timeArr[2], $timeArr[0]);
            $condition .= ' and addtime>=' . $unixtime;
        }
        if ($_GET['c_EndTime'] != '') {
            $timeArr = explode("-", $_GET['c_EndTime']);
            $unixtime = mktime(23, 59, 59, $timeArr[1], $timeArr[2], $timeArr[0]);
            $condition .= ' and addtime<=' . $unixtime;
        }
        $count = $Chargedetail->where($condition)->count();
        $listRows = 20;
        import("@.ORG.Page");
        $p = new Page($count, $listRows, $linkFront);
        $charges = $Chargedetail->where($condition)->limit($p->firstRow . "," . $p->listRows)->order('addtime desc')->select();
        foreach ($charges as $n => $val) {
            $charges[$n]['voo']=D("Member")->where('id=' . $val['touid'])->select();
        }
        $page = $p->show();
        $this->assign('charges', $charges);
        $this->assign('count', $count);
        $pagecount = ceil($count / $listRows);
        if ($pagecount == 0) {
            $pagecount = 1;
        }
        $this->assign('pagecount', $pagecount);
        $this->assign('page', $page);
        $totalcharge = D("Chargedetail")->query('select sum(rmb) as total from ss_chargedetail where uid=' . $_SESSION['uid'] . ' and status="1"');
        if ($totalcharge[0]['total'] != '') {
            $totalpay = $totalcharge[0]['total'];
        } else {
            $totalpay = 0;
        }
        $this->assign('totalpay', $totalpay);
        $this->display();
    }

    public function securityset()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function securitypassbind()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function securityfindpassbind()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function securityemailbind()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function securityqabind()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function helplist()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function helpview()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $this->display();
    }

    public function exchange()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $exchanges = D("Beandetail")->where("uid=" . $_SESSION['uid'] . ' and type="expend" and action="exchange"')->order('addtime desc')->select();
        $this->assign('exchanges', $exchanges);
        $this->display();
    }

    public function doExchange()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo 'notlogin';
            exit;
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        if ($userinfo['beanbalance'] < $_REQUEST['changelimit']) {
            echo 'noenoughbean';
            exit;
        }
        D("Member")->execute('update ss_member set coinbalance=coinbalance+' . $_REQUEST['changelimit'] . ',beanbalance=beanbalance-' . $_REQUEST['changelimit'] . ' where id=' . $_SESSION['uid']);
        $Beandetail = D("Beandetail");
        $Beandetail->create();
        $Beandetail->type = 'expend';
        $Beandetail->action = 'exchange';
        $Beandetail->uid = $_SESSION['uid'];
        $Beandetail->content = '兑换秀币';
        $Beandetail->bean = $_REQUEST['changelimit'];
        $Beandetail->addtime = time();
        $detailId = $Beandetail->add();
        $Coindetail = D("Coindetail");
        $Coindetail->create();
        $Coindetail->type = 'income';
        $Coindetail->action = 'exchange';
        $Coindetail->uid = $_SESSION['uid'];
        $Coindetail->content = $_REQUEST['changelimit'] . '个秀豆兑换';
        $Coindetail->coin = $_REQUEST['changelimit'];
        $Coindetail->addtime = time();
        $detailId = $Coindetail->add();
        echo '000000';
        exit;
    }

    public function settlement()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->assign('jumpUrl', __APP__);
            $this->error('您尚未登录');
        }

        $userinfo = D("Member")->find($_SESSION['uid']);
        $this->assign('userinfo', $userinfo);
        $settlements = D("Beandetail")->where("uid=" . $_SESSION['uid'] . ' and type="expend" and action="settlement"')->order('addtime desc')->select();
        $this->assign('settlements', $settlements);
        $this->display();
    }

    public function freezeIncome()
    {
        C('HTML_CACHE_ON', false);
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo 'notlogin';
            exit;
        }
        D("Member")->execute('update ss_member set freezeincome=' . $_REQUEST['freezeincome'] . ',freezestatus="' . $_REQUEST['freezestatus'] . '" where id=' . $_SESSION['uid']);
        echo '000000';
        exit;
    }

    public function activity()
    {
        $this->display();
    }

    public function zaegg()
    {
        C('HTML_CACHE_ON', false);
        header("Content-type: text/html; charset=utf-8"); 
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            echo "echostr=nologin";
            exit;
        }

        $eggset = D('Eggset');
        $eggsetinfo = $eggset->find(1);
        if (!$eggsetinfo) {
            echo "echostr=syserror";
            exit;
        }
        $userinfo = D("Member")->find($_SESSION['uid']);
        if ($userinfo['coinbalance'] < $eggsetinfo['onceneedcoin']) {
            echo "echostr=coinnotenough&needcoin=" . $eggsetinfo['onceneedcoin'];
            exit;
        } else {
            //扣费
            D("Member")->execute('update ss_member set spendcoin=spendcoin+' . $eggsetinfo['onceneedcoin'] . ',coinbalance=coinbalance-' . $eggsetinfo['onceneedcoin'] . ' where id=' . $_SESSION['uid']);
            //记入虚拟币交易明细
            $Coindetail = D("Coindetail");
            $Coindetail->create();
            $Coindetail->type = 'expend';
            $Coindetail->action = 'zaegg';
            $Coindetail->uid = $_SESSION['uid'];
            $Coindetail->content = '砸蛋1次花费';
            $Coindetail->objectIcon = '/style/images/fei.png';
            $Coindetail->coin = $eggsetinfo['onceneedcoin'];
            $Coindetail->addtime = time();
            $detailId = $Coindetail->add();
            $randKey = mt_rand(1, 100);
            if ($randKey <= $eggsetinfo['wincoin_odds']) {
                $wincoin = $eggsetinfo['wincoin'];
            } elseif ($randKey <= $eggsetinfo['wincoin_odds'] + $eggsetinfo['wincoin2_odds']) {
                $wincoin = $eggsetinfo['wincoin2'];
            } elseif ($randKey <= $eggsetinfo['wincoin_odds'] + $eggsetinfo['wincoin2_odds'] + $eggsetinfo['wincoin3_odds']) {
                $wincoin = $eggsetinfo['wincoin3'];
            } elseif ($randKey <= $eggsetinfo['wincoin_odds'] + $eggsetinfo['wincoin2_odds'] + $eggsetinfo['wincoin3_odds'] + $eggsetinfo['wincoin4_odds']) {
                $wincoin = $eggsetinfo['wincoin4'];
            } else {
                $wincoin = 0;
            }
            if ($wincoin == 0) {
                echo "echostr=failed";
                exit;
            } else {
                // 给用户赠送相应奖励
                D("Member")->execute('update ss_member set coinbalance=coinbalance+' . $wincoin . ' where id=' . $_SESSION['uid']);
                D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime) values(0,' . $_SESSION['uid'] . ',"' . $wincoin . '","砸蛋奖励","/style/images/coin.png",' . time() . ')');
                echo "echostr=win&wincoin=" . $wincoin;
                exit;
            }
        }
    }

    //充值1000元送靓号
    public function sendLiang($uid)
    {
        $dutime = time() + (24 * 3600 * 30);
        $liang = rand(100000, 999999);
        $data['uid'] = $uid;
        $data['num'] = $liang;
        $data['addtime'] = time();
        $data['expiretime'] = $dutime;
        $data['original'] = 'y';
        // $res = M('roomnum')->add($data);
        $res = D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime,expiretime,original) values(' . $uid . ',' . $liang . ',' . time() . ',' . $dutime . ',"n")');
        if (!$res) {
            $this->sendLiang($uid);
        }
    }

    public function car()
    {
        $this->display();
    }

    public function myfamily()
    {
        $uid = $_SESSION['uid'];
        $res = M("agentfamily")->where("uid='$uid' && zhuangtai='已通过'")->select();
        $this->assign("jzinfo", $res);
        $this->display();
    }

    public function myfamilyimg()
    {
        $uid = $_SESSION['uid'];
        $res = M("agentfamily")->where("uid='$uid' && zhuangtai='已通过'")->select();
        //var_dump($res);
        $this->assign("jzinfo", $res);
        //var_dump($_POST);
        if (!empty($_POST)) {
            import("ORG.Net.UploadFile");
            //实例化上传类
            $upload = new UploadFile();
            $upload->maxSize = 3145728;
            //设置文件上传类型
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
            //设置文件上传位置
            $upload->savePath = "./style/Familyimg/";//这里说明一下，由于ThinkPHP是有入口文件的，所以这里的./style是指网站根目录下的style文件夹
            //设置文件上传名(按照时间)
            $upload->saveRule = "time";
            if (!$upload->upload()) {
                $this->error($upload->getErrorMsg());
            } else {
                //上传成功，获取上传信息
                $info = $upload->getUploadFileInfo();
            }
            $savename = $info[0]['savename'];
            $model = M("agentfamily");
            if ($model->create()) {
                $model->id = $_POST['id'];
                $model->familyimg=$savename;
                if ($model->save()) {
                    $this->success("封面更新成功！");
                } else {
                    $this->error("封面更新失败！");
                }
            } else {
                $this->error($model->getError());
            }
        }
        $this->display();
    }

    //我的家族成员列表
    public  function  myfamilyemcee()
    {
        $condition = 'agentuid=' . $_SESSION['uid'];
        if ($_GET['start_time'] != '') {
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0, 0, 0, $timeArr[1], $timeArr[2], $timeArr[0]);
            $condition .= ' and addtime>=' . $unixtime;
        }
        if ($_GET['end_time'] != '') {
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0, 0, 0, $timeArr[1], $timeArr[2], $timeArr[0]);
            $condition .= ' and addtime<=' . $unixtime;
        }
        if ($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户ID或用户名') {
            if (preg_match("/^\d*$/", $_GET['keyword'])) {
                $condition .= ' and (id=' . $_GET['keyword'] . ' or username like \'%' . $_GET['keyword'] . '%\')';
            } else {
                $condition .= ' and username like \'%' . $_GET['keyword'] . '%\'';
            }
        }
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count, $listRows, $linkFront);
        $members = $member->limit($p->firstRow . "," . $p->listRows)->where($condition)->order($orderby)->select();
        $p->setConfig('header', '条');
        $page = $p->show();
        $this->assign('page', $page);
        $this->assign('members', $members);
        $this->display();
    }

    //我的家族管理
    public function  sqmyfamily()
    {
        $agentid = $_SESSION['uid'];
        $count = M("sqjoinfamily")->where("familyid=" . $agentid . " and zhuangtai=0")->count();
        //带分页关联用户信息
        import("ORG.Util.Page");
        $p = new Page($count, 15);
        /*    $p->setConfig('header','条');*/
        $fix = C('DB_PREFIX');
        $field = "m.nickname,m.curroomnum,m.earnbean,sq.*";
        $res = M('sqjoinfamily sq')->field($field)->join("{$fix}member m ON m.id=sq.uid")->where("familyid=" . $agentid . " and zhuangtai=0")->limit($p->firstRow . "," . $p->listRows)->select();
        /*$page = $p->show();*/
        $a = 0;
        foreach ($res as $k => $vo) {
            $emceelevel = getEmceelevel($vo['earnbean']);
            $res[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        /*$this->assign("page", $page);*/
        $this->assign("lists", $res);
        $this->page = $p->show();
        $this->display();
    }

    public function edit_sqmyfamily()
    {
        $sqid = $_GET['sqid'];
        //根据申请id 得到申请用户的相关信息
        $sqinfo = M("sqjoinfamily")->where("id=" . $sqid)->select();
        $userid = $sqinfo[0]['uid'];
        $zhuangtai = $sqinfo[0]['zhuangtai'];
        if ($zhuangtai == 0) {
            $dqzhuangtai = "未审核";
        } elseif ($zhuangtai == 1) {
            $dqzhuangtai = "已通过";
        } elseif ($zhuangtai == 2) {
            $dqzhuangtai = "未通过";
        }
        $userinfo = M("member")->where("id=" . $userid)->select();
        $emceelevel = getEmceelevel($userinfo[0]['earnbean']);
        $userinfo[0]["emceelevel"] = $emceelevel;
        $this->assign("dqzhuangtai", $dqzhuangtai);
        $this->assign("userinfo", $userinfo);
        $this->assign("sqinfo", $sqinfo);
        //接收提交信息更改状态
        if (!empty($_POST)) {
            $agentuid = $_SESSION['uid'];
            // var_dump($agentuid);
            $squid = $_POST['uid'];
            $sqid = $_POST['id'];
            $newzhuangtai = $_POST['zhuangtai'];
            $sqmodel = M("sqjoinfamily");
            $mmodel = M("member");
            $sqmodel->id = $sqid;
            $sqmodel->shtime = time();
            $sqmodel->zhuangtai = $newzhuangtai;
            if ($sqmodel->save()) {
                $mmodel->id = $squid;
                if ($newzhuangtai == '1') {
                    $mmodel->agentuid = $agentuid;
//                  $mmodel->sharingratio = 40;
                } else {
                    $mmodel->agentuid = 0;
                }
                if ($mmodel->save()) {
                    $this->success("更新成功");
                } else {
                    $this->error("更新失败");
                }
            } else {
                $this->error("更新失败");
            }
        }
        $this->display();
    }

    public function del_sqmyfamily()
    {
        $sqid = $_GET['sqid'];
        $uid = M("sqjoinfamily")->where("id=" . $sqid)->getField("uid");
        $res = M("sqjoinfamily")->where("id=" . $sqid)->delete();
        if ($res) {
            $this->success("删除成功");
        } else {
            $this->error("删除失败");
        }
    }

    public function setCar()
    {
        $carinfo = $_GET['carinfo'];
        $data['daoju1'] = 'n';
        $data['daoju2'] = 'n';
        $data['daoju3'] = 'n';
        $data['daoju4'] = 'n';
        $data['daoju5'] = 'n';
        $data['daoju6'] = 'n';
        $data['daoju7'] = 'n';
        $data['daoju8'] = 'n';
        $data['daoju9'] = 'n';
        $data['daoju10'] = 'n';
        $data['daoju11'] = 'n';
        $data['daoju12'] = 'n';
        $data['daoju13'] = 'n';
        $data['daoju14'] = 'n';
        M('member')->where('id=' . $_SESSION['uid'])->save($data);
        $setcar[$carinfo] = 'y';
        M('member')->where('id=' . $_SESSION['uid'])->save($setcar);
        $this->success('启用成功');
    }

}