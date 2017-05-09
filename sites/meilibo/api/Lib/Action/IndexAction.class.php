<?php
class IndexAction extends BaseAction
{
    
    public function searchroom()
    {
        $roomnum = $_GET['roomnum'];
        $res = M("member")->where("curroomnum=" . $roomnum)->select();
        
        if ($res == null) {
            echo json_encode("0");
            exit;
        } else {
            echo json_encode("1");
            exit;
        }
    }
    
    public function randenterroom()
    {
        $users = D('Member')->where('')->order('rand()')->limit(1)->select();
        if ($users) {
            header("Content-type: text/html; charset=utf-8"); 
            echo "<script>location.href='/".$users[0]['curroomnum']."';</script>";
            exit;
        } else {
            header("Content-type: text/html; charset=utf-8"); 
            echo "<script>alert('暂无房间');self.close();</script>";
            exit;
        }
    }

    public function index()
    {
	
	
	$iii=0;	
	//如果用户是登录的状态下进入了邀请链接 不生成
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] <= 0) {
            if (isset($_GET['marketuid']) && $_GET['marketuid']) {
                // 通过推广连接进入
                $marketDirector = M('Member')->find($_GET['marketuid']);
                if (!empty($marketDirector)) {
                    cookie("market_agent_uid",$_GET['marketuid']);
                    cookie("market_create_time", time());
                    // setcookie("market_agent_uid",$_GET['marketuid']);
                    // setcookie("market_create_time", time());
                }
            }
        }
	

        //首页右侧发现”心“主播 
        //$xinemcees = M("member")->where('bigpic<>"" and recommend="y"')->order('rand()')->limit(5)->select();
	//var_dump($xinemcees);
        //if (isset($_GET['ajax']) && $_GET['ajax'] == 'getemcee') {
        //    $this->ajaxReturn($xinemcees);
        //    exit;
        //}
        //$this->assign("xinemcees", $xinemcees);
        /*<!--
            描述：明星日排行
        -->*/

        $emceeRank_day1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 0,5');
        $a = 0;
	
//jiancha
        foreach ($emceeRank_day1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_day1[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        $this->assign("emceeRank_day1", $emceeRank_day1);
        /*<!--
            描述：明星周排行
        -->*/ 
        $emceeRank_week1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 0,5');
        $a = 0;
        foreach ($emceeRank_week1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $emceeRank_week1[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_week1[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        $this->assign("emceeRank_week1", $emceeRank_week1);
        
        /*<!--
            描述：明星月排行
        -->*/
        $emceeRank_month1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 0,5');
        $a = 0;
        foreach ($emceeRank_month1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $emceeRank_month1[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_month1[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        $this->assign("emceeRank_month1", $emceeRank_month1);
        
	
         /*<!--
            描述：明星总排行
        -->*/
        $emceeRank_all1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 0,5');
        $a = 0;
        foreach ($emceeRank_all1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $emceeRank_all1[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_all1[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        //var_dump($emceeRank_all);
        $this->assign("emceeRank_all1", $emceeRank_all1);
        //var_dump($emceeRank_all1);
        //富豪榜    
         /*<!--
            描述：富豪日榜
        -->*/
        $richRank_day1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 0,5');
        $a = 0;
        foreach ($richRank_day1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $richRank_day1[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_day1[$a]['richlevel'] = $richlevel;
            $a++;
        }
	
        $this->assign("richRank_day1", $richRank_day1);
        /*<!--
            描述：富豪周榜
        -->*/
        $richRank_week1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 0,5');
        $a = 0;
        foreach ($richRank_week1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $richRank_week1[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_week1[$a]['richlevel'] = $richlevel;
            $a++;
        }

        $this->assign("richRank_week1", $richRank_week1);

        $richRank_month1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 0,5');
        $a = 0;
        foreach ($richRank_month1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $richRank_month1[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_month1[$a]['richlevel'] = $richlevel;
            $a++;
        }
	
	//如果用户是登录的状态下进入了邀请链接 不生成
        $this->assign("richRank_month1", $richRank_month1);

        // 查询出富豪总榜的前5条
        $richRank_all1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 5');
        $a = 0;
        foreach ($richRank_all1 as $k => $vo) {
            $userinfo = D("Member")->field('id,curroomnum,earnbean,nickname')->find($vo['uid']);
            $emceeRank_day1[$a]['userinfo'] = $userinfo;
            $richRank_all1[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_all1[$a]['richlevel'] = $richlevel;
            $a++;
        }

        $this->assign("richRank_all1", $richRank_all1);
        
        // 热门家族推荐 
        $zbcount=M("member")->query('select count(*) as total,agentuid from `ss_member` where agentuid>0 group by agentuid order by total desc');
        $data = array();
        $a = 0;
        foreach ($zbcount as $k => $v) {
            $aid = $v['agentuid'];
            //var_dump($aid);
            $agentinfo = M("member")->where("id=$aid")->select();
            $zbcount = M("member")->query("select count(*) as total from `ss_member` where agentuid=$aid ");
            $data[$a] = $agentinfo;
            $data[$a]['zbtotal'] = $zbcount;
            $a++;
        }
        $this->assign("data", $data);
        
        //$usersorts = D('Usersort')->where('')->field('id,sortname')->order('orderno asc')->limit(10)->select();

        $usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
       
        foreach ($usersorts as $n => $val) {
            $usersorts[$n]['voo'] = D("Usersort")->where('parentid=' . $val['id'])->order('orderno')->select();
        }
        $this->assign('usersorts', $usersorts);

        $recusers = D('Member')->where('bigpic<>"" and idxrec="y" and broadcasting="y"')->field('nickname,curroomnum,bigpic,online,broadcasting,virtualguest')->order('idxrectime desc')->limit(2)->select();
        $this->assign('recusers', $recusers);

        $rollpics = M('rollpic')->where('moc = "pc"')->field('picpath,title,linkurl')->order('orderno asc')->limit(10)->select();
        $this->assign('rollpics', $rollpics);
       
        $announces = D('Announce')->where('')->field('id,title,addtime')->order('addtime desc')->limit(3)->select();
        $this->assign('announces', $announces);

        // 明星日 周 月 总榜
        $emceeRank_day = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 6');
        $this->assign('emceeRank_day', $emceeRank_day);
        $emceeRank_week = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%U") group by uid order by total desc LIMIT 6');
        $this->assign('emceeRank_week', $emceeRank_week);
        $emceeRank_month = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 6');
        $this->assign('emceeRank_month', $emceeRank_month);
        $emceeRank_all = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 6');
        $this->assign('emceeRank_all', $emceeRank_all);

        // 富豪日 周 月 总榜
        $richRank_day = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 6');
        $this->assign('richRank_day', $richRank_day);
        $richRank_week = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 6');
        $this->assign('richRank_week', $richRank_week);
        $richRank_month = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 6');
        $this->assign('richRank_month', $richRank_month);
        $richRank_all = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 6');
        $this->assign('richRank_all', $richRank_all);


        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $memc_obj = new Memcached();
        $memc_obj->addServer($ip, $port);
        $room_cache = $memc_obj->get(C('HOT_ANCHOR_LIST'));
        if ($room_cache !== false) {
            $room_cache = json_decode($room_cache, true);
            $room_lst = array_slice($room_cache, 0, 11, true);
        } else {
            $room_lst = array();
        }
        $room_num_arr = array();
        foreach ($room_lst as $room_num => $online) {
            $room_num_arr[] = $room_num;
        }
        // 不要交换curroomnum的顺序.
        //$fields = 'curroomnum, id, nickname, ucuid, snap, city, online, virtualguest, isvirtual,broadcasting,earnbean';
        $fields = 'curroomnum, id, nickname, snap, city, online, virtualguest, isvirtual,broadcasting,earnbean';
        if (empty($room_num_arr)) {
            $condition = '1 != 1';
        } else {
            $condition = 'curroomnum in ('.implode(',', $room_num_arr).')';
        }

        $anchor_lst = M("member")->where($condition)->getField($fields);
        $hot_anchor = array();
        // 使用room_lst是为了保证有序,但是如果数据库没有对应的信息,得过滤掉.
        foreach ($room_lst as $room_num => $online) {
            $anchor_info = isset($anchor_lst[$room_num]) ? $anchor_lst[$room_num] : null;
            if ($anchor_info === null) {
                continue;
            }
            $virtual_cnt = 0;
            if ($anchor_info['isvirtual'] == 'y' && $anchor_info['virtualguest'] > 0) {
                $virtual_cnt = $anchor_info['virtualguest'];
            }
            $live_state = '';
            if ($anchor_info['broadcasting'] =='y') {
                $live_state = "<em class='png24 live-tip'>直播中</em>";
            } else if (empty($anchor_info['offlinevideo'])) {
                $live_state = "<em class='png24 live-tip' style='height:17px;width:54px;'><img src='/style/images/luxiang.png' /></em>";
            }
            $emceelevel = getEmceelevel($anchor_info['earnbean']);
            $level_id = isset($emceelevel[0]['levelid']) ? $emceelevel[0]['levelid'] : 0;
            $hot_anchor[] = array(
                'id' => $anchor_info['id'],
                'curroomnum' => $room_num,
                'online' => $virtual_cnt + $online,
                'snap' => empty($anchor_info['snap']) ? '/style/images/default.gif' : $anchor_info['snap'],
                'nickname' => $anchor_info['nickname'],
                'live_state' => $live_state,
                'emceelevel' => $level_id,
            );
        }

        // 热门主播.
        $this->assign('hot_anchor', $hot_anchor);

        // 获取推荐会员列表
        $recommend = M("member")->where("recommend = 'y'")->limit(10)->order('rand()')->select();
        // 推荐主播.
        foreach ($recommend as $k => $anchor) {
            $virtual_guest = 0;
            if ($anchor['isvirtual'] == 'y' && $anchor['virtualguest'] > 0) {
                //当前房间虚拟
                $virtual_guest = (int)$anchor['virtualguest'];
            }
            $online_key = C('ROOM_ONLINE_NUM_PREFIX').$anchor['curroomnum'];
            $online_info = $memc_obj->get($online_key);
            if ($online_info !== false) {
                $online_info = json_decode($online_info, true);
                $real_cnt = (int)$online_info['all_num'];
            } else {
                $real_cnt = 0;
            }
            $recommend[$k]['online'] = $virtual_guest + $real_cnt;
        }

        $this->assign("recommend", $recommend);

        // 调取用户分类
        $bsort = M("usersort")->where("parentid=0")->select();
        $this->assign("bsort", $bsort);
        // 调取公告
        $announce = M("announce")->order("addtime")->limit(5)->select();
        $this->assign("announce", $announce);
        // 获取等级
        $emceelevel = M("emceelevel")->select();
        $this->assign("emceelevel", $emceelevel);
       
	
	 $_SESSION['currenturl'] = C('WEB_URL');
        $this->display();
    }

    public function listboboByCategoreis()
    {
        //$onlinecount = D('Member')->where('')->sum('online');
        //$this->assign('onlinecount', $onlinecount);
        $virtualcount = D('Member')->where('isvirtual="y"')->count();
        $onlinecount = 0;
        $onlineemcee = D('Member')->where('online>0')->select();
        foreach ($onlineemcee as $val) {
            if ($val['broadcasting'] == "y") {
                if ($val['virtualguest'] > 0) {
                    $onlinecount = $onlinecount + $val['online'] + $val['virtualguest'] + $virtualcount;
                } else {
                    $onlinecount = $onlinecount + $val['online'];
                }
            } else {
                $onlinecount = $onlinecount + $val['online'];
            }
        }
        $this->assign('onlinecount', $onlinecount);

        //$usersorts = D('Usersort')->where('')->field('id,sortname')->order('orderno asc')->limit(10)->select();
        $usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
        foreach ($usersorts as $n => $val) {
            $usersorts[$n]['voo'] = D("Usersort")->where('parentid=' . $val['id'])->order('orderno')->select();
        }
        $this->assign('usersorts', $usersorts);

        $this->display();
    }

    public function listEmceeCategoreis2()
    {
        //$onlinecount = D('Member')->where('')->sum('online');
        //$this->assign('onlinecount', $onlinecount);
        $virtualcount = D('Member')->where('isvirtual="y"')->count();
        $onlinecount = 0;
        $onlineemcee = D('Member')->where('online>0')->select();
        foreach ($onlineemcee as $val) {
            if ($val['broadcasting'] == "y") {
                if ($val['virtualguest'] > 0) {
                    $onlinecount = $onlinecount + $val['online'] + $val['virtualguest'] + $virtualcount;
                } else {
                    $onlinecount = $onlinecount + $val['online'];
                }
            } else {
                $onlinecount = $onlinecount + $val['online'];
            }
        }
        $this->assign('onlinecount', $onlinecount);

        //$usersorts = D('Usersort')->where('')->field('id,sortname')->order('orderno asc')->limit(10)->select();
        $usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
        foreach ($usersorts as $n => $val) {
            $usersorts[$n]['voo'] = D("Usersort")->where('parentid=' . $val['id'])->order('orderno')->select();
        }
        $this->assign('usersorts', $usersorts);

        $this->display();
    }

    public function listbobomajorType()
    {
        if ($_GET['type'] == 'hot') {
            $users = D('Member')->where('broadcasting="y"')->field('nickname,curroomnum,maxonline,earnbean,snap,starttime,online,virtualguest')->order('online desc')->limit(100)->select();
            $this->assign('users', $users);
            $this->display();
            exit;
        }

        if ($_GET['sid'] != '') {
            $sortinfo = D("Usersort")->find($_GET['sid']);
            $this->assign('sortinfo', $sortinfo);
            $condition = 'sid='.$_GET['sid'];
        }
        
        if ($sortinfo['sortname'] == '特约') {
            //$users = D('Member')->where('fakeuser="y"')->order('online desc')->limit(100)->select();
            $users = D('Member')->where('fakeuser="y"')->order('rand()')->limit(20)->select();
            //$users2 = array();
            //$i = 0;
            //foreach($users as $val){
                ////$body = file_get_contents('http://xiu.56.com/api/userFlvApi.php?room_user_id='.$val['56_room_user_id']);
                //$curl = curl_init(); 
                //curl_setopt($curl, CURLOPT_URL, 'http://xiu.56.com/api/userFlvApi.php?room_user_id='.$val['56_room_user_id']);
                //curl_setopt($curl, CURLOPT_HEADER, 1);
                //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                //$data = curl_exec($curl);
                //curl_close($curl);
                //if(strstr($data,"status=1")){
                    //array_push($users2,$users[$i]);
                //}
                //$i++;
            //}
            //$this->assign('users', $users2);
            $this->assign('users', $users);
        } else {
            $users = D('Member')->where($condition . ' and broadcasting="y"')->field('nickname,curroomnum,maxonline,earnbean,snap,starttime,online,virtualguest')->order('online desc')->limit(100)->select();
            $this->assign('users', $users);
        }
        
        $this->display();
    }

    public function listbobo()
    {
        if ($_GET['sid'] != '') {
            $sortinfo = D("Usersort")->find($_GET['sid']);
            $this->assign('sortinfo', $sortinfo);
            $condition = 'sid=' . $_GET['sid'];
        }
        
        if ($sortinfo['sortname'] == '特约') {
            //$users = D('Member')->where('fakeuser="y"')->order('online desc')->limit(100)->select();
            $users = D('Member')->where('fakeuser="y"')->order('rand()')->limit(20)->select();
            //$users2 = array();
            //$i = 0;
            //foreach($users as $val){
                ////$body = file_get_contents('http://xiu.56.com/api/userFlvApi.php?room_user_id='.$val['56_room_user_id']);
                //$curl = curl_init(); 
                //curl_setopt($curl, CURLOPT_URL, 'http://xiu.56.com/api/userFlvApi.php?room_user_id='.$val['56_room_user_id']);
                //curl_setopt($curl, CURLOPT_HEADER, 1);
                //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                //$data = curl_exec($curl);
                //curl_close($curl);
                //if(strstr($data,"status=1")){
                    //array_push($users2,$users[$i]);
                //}
                //$i++;
            //}
            //$this->assign('users', $users2);
            $this->assign('users', $users);
        } else {
            $users = D('Member')->where($condition . ' and broadcasting="y"')->field('nickname,curroomnum,maxonline,earnbean,snap,starttime,online,virtualguest')->order('online desc')->limit(100)->select();
            $this->assign('users', $users);
        }
        
        $this->display();
    }

    public function listbobo2()
    {
        $num = isset($_GET['num']) ? $_GET['num'] : '';
        $get_order = isset($_GET['order']) ? $_GET['order'] : '';
        $get_map1 = isset($_GET['setmap1']) ? $_GET['setmap1'] : '';
        $get_province = isset($_GET['province']) ? $_GET['province'] : '';
        if ($get_province != "" && $get_province != "所有地区") {
            $map['province'] = $get_province;
        }
        
        if ($get_order == "d") {
            $order = "id";
        } else {
            $order = "'online desc'";
        }
        if ($num == null) $num = 0;
        
        if ($get_map1 != "0" && $get_map1 != null) {
            // 调取等级区间
            $earnbean_low = M("emceelevel")->where("levelid = $get_map1")->getField("earnbean_low");
            $earnbean_up = M("emceelevel")->where("levelid = $get_map1")->getField("earnbean_up");
            $map['earnbean'] = array("BETWEEN", "$earnbean_low, $earnbean_up");
        }

        if ($_GET['sid'] != '') {
            $sortinfo = D("Usersort")->find($_GET['sid']);
            $this->assign('sortinfo', $sortinfo);
            $map['sid'] = $_GET['sid'];
        }
        // $map['broadcasting'] = "y";
        if ($sortinfo['sortname'] == '特约') {
            //$users = D('Member')->where('fakeuser="y"')->order('online desc')->limit(100)->select();
            $users = D('Member')->where('fakeuser="y"')->order('rand()')->limit(20)->select();
            //$users2 = array();
            //$i = 0;
            //foreach($users as $val){
                ////$body = file_get_contents('http://xiu.56.com/api/userFlvApi.php?room_user_id='.$val['56_room_user_id']);
                //$curl = curl_init(); 
                //curl_setopt($curl, CURLOPT_URL, 'http://xiu.56.com/api/userFlvApi.php?room_user_id='.$val['56_room_user_id']);
                //curl_setopt($curl, CURLOPT_HEADER, 1);
                //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                //$data = curl_exec($curl);
                //curl_close($curl);
                //if(strstr($data,"status=1")){
                    //array_push($users2,$users[$i]);
                //}
                //$i++;
            //}
            //$this->assign('users', $users2);
            $this->assign('users', $users);
        } else {
            $users = D('Member')->where($map)->field('id,nickname,curroomnum,maxonline,earnbean,snap,starttime,online,virtualguest,offlinevideo,broadcasting')->order($order)->limit("$num,16")->select();
            $a = 0;
            if($users != null){
                foreach ($users as $vo) {
                    if ($vo['broadcasting'] == 'y') {
                        $users[$a]['live_state'] = "<em class='png24 live-tip'>直播中</em>";
                    } else if ($vo['offlinevideo'] != null && $vo['offlinevideo'] != "") {
                        $users[$a]['live_state'] = "<em class='png24 live-tip' style='height:17px;width:54px;position: absolute;top: 0;right: 0;'><img src='/style/images/luxiang.png' /></em>";
                    } else {
                        $users[$a]['live_state'] = "";
                    }
                    $a++;
                }            
            }
            ///var_dump($users);
            $this->assign('users', $users);
        }
        if ($users == NULL) {
            exit();
        }
        $this->display();
    }

    public function findbobo_ajax()
    {
        $user = D("Member");
        $count = $user->where('bigpic<>"" and recommend="y" and broadcasting="y"')->count();
        $listRows = 9;
        import("@.ORG.Page");
        $p = new Page($count, $listRows, $linkFront);
        $users = $user->where('bigpic<>"" and recommend="y" and broadcasting="y"')->limit($p->firstRow . "," . $p->listRows)->order('rand()')->select();
        $this->assign('users', $users);
        $this->assign('count', $count);
        
        $this->display();
    }

    
}
