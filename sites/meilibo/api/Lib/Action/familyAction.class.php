<?php
class familyAction extends BaseAction
{
    
    public function sqjoinfamily()
    {
        $familyid = $_GET['familyid'];
        // var_dump($familyid);
        $uid = $_SESSION['uid'];
        // var_dump($uid);
        // 判断用户是否登录
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->error('您尚未登录', "__URL__/index");
        }
            
        // 根据用户ID查询出相关的信息
        $res = M("member")->where("id=" . $uid)->getField("emceeagent");
        if ($res == 'y') {
            $this->error("您拥有自己的家族，不允许加入其它家族","__URL__/index");
        }
        //判断用户是否加入过家族
        $agentuid = M("member")->where("id=" . $uid)->getField("agentuid");
        if ($agentuid != '0') {
            $this->error("您已经加入过家族。。", "__URL__/index");
        }
        // 判断用户是否已经提交过申请
        $sqinfo = M("sqjoinfamily")->where("uid=" . $uid)->order("sqtime desc")->limit(1)->select();
        //   var_dump($sqinfo);
        $zhuangtai = $sqinfo[0]["zhuangtai"];
        // var_dump($zhuangtai);
        //0:未审核;1:已通过；2：未通过；
        if ($zhuangtai == "0") {
            $this->error("您有一条申请记录正在审核中，请等待审核");
        }
    
        // 符合条件  插入申请记录
        $model = M("sqjoinfamily");
        $model->uid = $uid;
        $model->familyid = $familyid;
        $model->sqtime = time();
        if ($model->add()) {
            $this->success("申请成功，请等待审核", "__URL__/index");
        } else {
            $this->error("申请失败，请再次申请", "__URL__/index");
        }
    }
    
    // 申请成为代理城建家族
    public function sqagent()
    {
        $uid = $_SESSION['uid'];
        // 判断用户是否登录
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] < 0) {
            $this->error('您尚未登录', "__URL__/index");
        }
        // 根据用户ID查询出相关的信息
        $res = M("member")->where("id=" . $uid)->getField("emceeagent");
        if ($res == 'y') {
            $this->error("您已经创建过家族", "__URL__/index");
        }
        //根据用户id判断用户是否提交过申请
        $sqinfo = M("agentfamily")->where("uid=" . $uid)->order("sqtime desc")->limit(1)->select();

        $zhuangtai = $sqinfo[0]["zhuangtai"];
  
        if ($zhuangtai == "未审核") {
            $this->error("您提交的申请，正在等待系统审核。。。", "__URL__/index");
        }
   
        if ($zhuangtai == "未通过") {
            $this->assign("zhuangtai", "对不起您的上次申请未通过，请认真填写！");
        }

        $fmodel = M("agentfamily");
        if (!empty($_POST)) {
            import("ORG.Net.UploadFile");  
            // 实例化上传类  
            $upload = new UploadFile(); 
            $upload->maxSize = 3145728;  
            //设置文件上传类型  
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
            //设置文件上传位置  
            $upload->savePath = "./style/Familyimg/"; //这里说明一下，由于ThinkPHP是有入口文件的，所以这里的./style是指网站根目录下的style文件夹  
            //设置文件上传名(按照时间)  
            $upload->saveRule = "time";  
            if (!$upload->upload()) {  
                $this->error($upload->getErrorMsg());
            } else {  
                //上传成功，获取上传信息  
                $info = $upload->getUploadFileInfo();
            }
            $savename = $info[0]['savename']; 
              
             //var_dump($savename);
            $vo = $fmodel->create();
        
            $fmodel->familyimg = $savename;
            $fmodel->sqtime = time();
            $fmodel->uid = $uid;
            if (!$vo) {
                $this->error($fmodel->getError());
            } else {
                $annId = $fmodel->add();
                if ($annId) {
                    $this->success("添加成功！");
                } else {
                    $this->error("添加失败！");
                }
            }
        }

        $this->display();
    }

    public function jiazunei()
    {
        $agentid = $_GET['agent'];
        $this->assign("agentid", $agentid);
        // var_dump($agentid);
        // 得到家族信息
        $familyinfo = M("agentfamily")->where("uid='$agentid' && zhuangtai='已通过'")->select();
        // var_dump($familyinfo);
        $this->assign("familyinfo", $familyinfo);
        // 最新加入家族的主播列表5人
        $new = M("sqjoinfamily")->where("familyid='$agentid' && zhuangtai='1'")->order("shtime desc")->limit(5)->select();
        $fix = C('DB_PREFIX');
        //$field = "m.curroomnum,m.ucuid,sq.uid,sq.shtime";
        $field = "m.curroomnum,m.id,sq.uid,sq.shtime";
        $newjoin = M('sqjoinfamily sq')->field($field)->join("{$fix}member m ON sq.uid=m.id")->where("familyid='$agentid' && zhuangtai='1'")->order("shtime desc")->limit(5)->select();
        $this->assign("newjoin", $newjoin); 
          
          //var_dump($new);
        //根据得到的id 来得到指定代理人的信息
        $agentinfo = M("member")->where("id=$agentid")->select();
        $this->assign("agentinfo", $agentinfo);
        //得到当前主播的等级
        $agentlevel = getEmceelevel($agentinfo[0]['earnbean']);
        $this->assign("agentlevel",$agentlevel);
        //当前代理下的 主播人数
        $total = M("member")->where("agentuid=$agentid")->count();
        $this->assign("total", $total);
        $User = M('User'); // 实例化User对象
        import('ORG.Util.Page');// 导入分页类
       
        $Page       = new Page($total, 20);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        // 当前代理下的所有主播列表
        $emceeinfo = M("member")->where("agentuid=$agentid")->limit($Page->firstRow . ',' . $Page->listRows)->select();
        
        // 得到主播的等级信息
        $a = 0;
        foreach ($emceeinfo as $k => $v) {
            $emceelevel = getEmceelevel($v['earnbean']);
            $emceeinfo[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        //var_dump($emceeinfo);
        $this->assign("emceeinfo", $emceeinfo);
        $this->assign("page", $show);
        // var_dump($show);
        //人气
        $rq = D('Liverecord')->query("SELECT sum(entercount) as total FROM `ss_liverecord` where uid=$agentid");
        
        $rqtotal = $rq[0]["total"];
        
        $zbid = M("member")->field("id")->where("agentuid=$agentid")->select();
        //var_dump($zbid);
        $a = 0;
        $uid = array();
        foreach ($zbid as $k => $v) {
            $uid[$a] = $v['id'];
            $a++;
        }
        //var_dump($uid);
        
        $a = 0;
        foreach ($zbid as $k => $v) {
            $emceeid = $v['id'];
            $emceerq = D('Liverecord')->query("SELECT sum(entercount) as total FROM `ss_liverecord` where uid=$emceeid");
            //var_dump($emceerq);
            $rqtotal = $rqtotal + $emceerq[0][$total];
            $a++;
            
        }
        $this->assign("rqtotal", $rqtotal);
        //var_dump($rqtotal);
            
        //当前代理下的在线人气主播主播
        $olrqzb = D('Member')->where(" broadcasting='y' and isdelete='n' and agentuid=$agentid")->field('nickname,curroomnum,bigpic,online,virtualguest,agentuid,online')->order('online desc')->limit(4)->select();
        //var_dump($olrqzb);
        $this->assign("olrqzb", $olrqzb);
        //当前家族下的明星榜
        $emceeRank_month = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1');
        $a = 0;
        foreach ($emceeRank_month as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $emceeRank_month[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_month[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        $this->assign("emceeRank_month", $emceeRank_month);
        
        $emceeRank_month4 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1,4');
        $a = 0;
        $b = 1;
        foreach ($emceeRank_month4 as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $emceeRank_month4[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_month4[$a]['emceelevel'] = $emceelevel;
            $emceeRank_month4[$a]['xuhao'] = ($b + 1);
            $b++;
            $a++;
        }
        
        $this->assign("emceeRank_month4", $emceeRank_month4);
        $emceeRank_all = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 1');
        $a = 0;
        foreach ($emceeRank_all as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $emceeRank_all[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_all[$a]['emceelevel'] = $emceelevel;
            $a++;
        }
        //var_dump($emceeRank_all);
        $this->assign("emceeRank_all", $emceeRank_all);
        $emceeRank_all4 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 1,4');
        $a = 0;
        $b = 1;
        foreach ($emceeRank_all4 as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $emceeRank_all4[$a]['userinfo'] = $userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $emceeRank_all4[$a]['emceelevel'] = $emceelevel;
            $emceeRank_all4[$a]['xuhao'] = ($b + 1);
            $b++;
            $a++;
        }
        // var_dump($emceeRank_all4);
        $this->assign("emceeRank_all4", $emceeRank_all4);
    
        // 当前家族下的富豪榜        
        // 查询出富豪月榜的前5条
        $richRank_month = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1');
        $a = 0;
        foreach ($richRank_month as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $richRank_month[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_month[$a]['richlecel'] = $richlevel;
            $a++;
        }

        $this->assign("richRank_month", $richRank_month);
        $richRank_month4 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1,4');
        $a = 0;
        $b = 1;
        foreach ($richRank_month4 as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $richRank_month4[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_month4[$a]['richlecel'] = $richlevel;
            $richRank_month4[$a]['xuhao'] = ($b + 1);
            $b++;
            $a++;
        }
        $this->assign("richRank_month4", $richRank_month4);
        $richRank_all = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 1');
        $a = 0;
        foreach ($richRank_all as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $richRank_all[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_all[$a]['richlecel'] = $richlevel;
            $a++;
        }

        $this->assign("richRank_all", $richRank_all);
        $richRank_all4 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 1,4');
        $a = 0;
        $b = 1;
        foreach ($richRank_all4 as $k => $vo) {
            $userinfo = D("Member")->find($vo['uid']);
            $richRank_all4[$a]['userinfo'] = $userinfo;
            $richlevel = getRichlevel($userinfo['spendcoin']);
            $richRank_all4[$a]['richlecel'] = $richlevel;
            $richRank_all4[$a]['xuhao'] = ($b + 1);
            $b++;
            $a++;
        }
        $this->assign("richRank_all4", $richRank_all4);
            
        $this->display();
    }
    public function jiazu_desc(){
        if (!isset($_GET['familyid'])) {
            $this->error("参数错误");
        }
        $familyid = $_GET['familyid'];
        if($familyid == 0){
            $this->error("您未加入家族或未登录");
        }
        $familyDetail = M('Agentfamily')->find($_GET['familyid']);
        if (!$familyDetail) {
            $this->error("家族不存在");
        }
        $familyDetail['sqtime'] = date('Y-m-d h:i:s',$familyDetail['sqtime']);

        $memberModel = M('Member');
        $zbcounts = $memberModel->execute("select count(*) from ss_member as mem,ss_agentfamily as family where family.id={$familyid} and family.uid=mem.agentuid and sign='y'");
        $memcounts = $memberModel->execute("select count(*) from ss_member as mem,ss_agentfamily as family where family.id={$familyid} and family.uid=mem.agentuid");
        $familyDetail['zbcounts'] = $zbcounts;
        $familyDetail['memcounts'] = $memcounts;

        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page5");
        $p = new Page($zbcounts, $listRows, $linkFront);
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $p->firstRow = $listRows * ($_GET['page'] - 1);
        }

        $members = $memberModel->limit($p->firstRow . "," . $p->listRows)->where(array('agentuid'=>$familyDetail['uid']))->select();
        if (!$members) {
            $members = array();
        }

        $mem_config = C('MEM_CACHE');
        list($ip, $port) =  explode(':', $mem_config['mem_server']);
        $memc_obj = new Memcached();
        $memc_obj->addServer($ip, $port);

        // 在线人数和等级.
        foreach ($members as $idx => $anchor) {
            $emceelevel = getEmceelevel($anchor['earnbean']);
            $level_id = isset($emceelevel[0]['levelid']) ? $emceelevel[0]['levelid'] : 0;
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
            $members[$idx]['snap'] = !$anchor['snap'] ? '/style/images/default.gif' : $anchor['snap'];
            $members[$idx]['emceelevel'] = $level_id;
            $members[$idx]['online'] = $virtual_guest + $real_cnt;
            $members[$idx]['broadcasting'] = $anchor['broadcasting'] == 'y' ? '在线' : '离线';
        }
        
        $p->setConfig('header', '条');
        $page = $p->show();
        
        $this->assign('page', $page);
        $this->assign('pages', ceil($zbcounts/$listRows));
        $this->assign('num',$p->firstRow/20 + 1);
        $this->assign("members",$members);
        $this->assign("info",$familyDetail);

        $this->display();
    }
    public function index()
    {
        $memberModel = M('Member');
        $count = $memberModel->where('agentuid>0')->group('agentuid')->count();
        $listRows = 20;
        $linkFront = '';
        $pages = ceil($count / $listRows);
        import("@.ORG.Page5");
        $p = new Page($count, $listRows, $linkFront);
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] <= $pages) {
            $p->firstRow = $listRows * ($_GET['page'] - 1);
            $num = $_GET['page'];
        } else {
            $num = 1;
        }
        
        $members = $memberModel->field('agentuid')->where('agentuid>0')->group('agentuid')->select();
        
        $data = array();
        $a = 0;
        $familyInfoBuffer = array();
        if (!$members) {
            $members = array();
        }
        foreach ($members as $k => $v) {
            $aid = $v['agentuid'];
            if ( !isset($familyInfoBuffer[$aid]) ) {
                $tmp = $memberModel->query("select mem.nickname,family.id as familyID,family.familyname,family.familyimg,family.sqtime from ss_member as mem,ss_agentfamily as family where mem.id={$aid} and mem.id=family.uid and family.zhuangtai='已通过'");
                $familyInfoBuffer[$aid] = array_shift($tmp);
                $familyInfoBuffer[$aid]['sqtime'] = date('Y-m-d h:i:s', $familyInfoBuffer[$aid]['sqtime']);
            }
            $zbcounts = $memberModel->query("select count(*) as total from `ss_member` where agentuid=$aid and sign='y'");
            $memcounts = $memberModel->query("select count(*) as total from `ss_member` where agentuid=$aid");
            $data[$a]=$familyInfoBuffer[$aid];
            $data[$a]['zbtotal'] = $zbcounts[0]['total'];
            $data[$a]['memtotal'] = $memcounts[0]['total'];
            $a++;
        }
        //查询家族信息中是否存在创建的家族
        //判断是否登录以及是否为0 如果不存在
        $agentuid = 0;
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] <= 0) {
            $agentuid = 0;
        }else{
            $uid = $_SESSION['uid'];
            $emceeagent = M("member")->where("id=" . $uid)->getField("emceeagent");
            $memagentuid = M("member")->where("id=" . $uid)->getField("agentuid");
            if ($emceeagent == 'y') {
                $familyModel = M('Agentfamily');
                $where = 'uid = '.$uid;
                $agentuid = $familyModel->where($where)->getField("id");
            }else{
                //判断用户是否加入过家族
                if($memagentuid != 0){
                    $familyModel = M('Agentfamily');
                    $where = 'uid = '.$memagentuid;
                    $agentuid = $familyModel->where($where)->getField("id");
                }
            }
        }
        $this->assign("new","0");
        $this->assign("agentuid", $agentuid);
        $this->assign("data", $data);
        $this->assign("num", $num);
        $this->assign("pages", $pages);
        
        /*
        //最新主播代理三人
        $res = M("member")->where("emceeagent='y'")->order("emceeagenttime desc")->limit(3)->select();
        $this->assign("newagent", $res);
        //var_dump($res);
        $a = 0;
        foreach ($res as $k => $v) {
            $agentuid = $v['id'];
            $zbcount = M("member")->where("agentuid=$agentuid")->count();
            //var_dump($zbcount);
            $emceelevel = getEmceelevel($v['earnbean']);
            //var_dump($emceelevel);
            $res[$a]['emceelevel'] = $emceelevel;
            $res[$a]['zbtotal'] = $zbcount;
            $a++;
        }    
        $this->assign("newagent", $res);
        //查询出在线家族主播 被推荐的
        $recusers = D('Member')->where('bigpic<>"" and broadcasting="y" and isdelete="n" and agentuid!=0 ')->field('nickname,curroomnum,bigpic,online,virtualguest,agentuid')->order('rectime desc')->limit(10)->select();
        //根据agentuid得到相应的家族信息
        $a = 0;
        if (!empty($recusers)) {
            foreach ($recusers as $k => $v) {
                $uid = $v['agentuid'];
                
                $agentinfo = M("member")->where("id='$uid'")->getField("nickname");
                $recusers[$a]['agentinfo'] = $agentinfo;
                $a++;
            }
        }
       
        $this->assign("onlinezb", $recusers);
        */
        //$richRank_weekq3 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3');
        $this->display();
    }
    public function new_jiazu(){
        // 先将所有通过审核的家族查询出来，
        // 然后用家族ID去循环查询是否有成员
        // 如果没有成员就直接添加进data，数量加1
        $data = array();
        $count = 0;
        $AgentModel = M('Agentfamily');
        $memberModel = M('Member');
        $AgentList = $AgentModel->query('select ss_agentfamily.id as familyID,ss_agentfamily.familyname,ss_agentfamily.uid,ss_agentfamily.familyimg,ss_agentfamily.sqtime,ss_member.nickname nickname from ss_agentfamily INNER join ss_member on ss_agentfamily.uid = ss_member.id where ss_agentfamily.zhuangtai = "已通过" order by ss_agentfamily.sqtime DESC');
        $AgentList = !$AgentList ? array() : $AgentList;
        //包含家族名称 族长 创建时间 图片
        foreach ($AgentList as $Temp) {
            $members = $memberModel->where('agentuid = '.$Temp['uid'])->select();
            if($members == null){
                //如果为0，添加至数组中
                $Temp['sqtime'] = date('Y-m-d h:i:s', $Temp['sqtime']);
                $data[] = $Temp;
                $count++;
            }
        }
        $listRows = 20;
        $linkFront = '';
        $pages = ceil($count / $listRows);
        import("@.ORG.Page5");
        $p = new Page($count, $listRows, $linkFront);
        if (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] <= $pages) {
            $p->firstRow = $listRows * ($_GET['page'] - 1);
            $num = $_GET['page'];
        } else {
            $num = 1;
        }

        //查询家族信息中是否存在创建的家族
        //判断是否登录以及是否为0 如果不存在
        $agentuid = 0;
        if (!isset($_SESSION['uid']) || $_SESSION['uid'] <= 0) {
            $agentuid = 0;
        }else{
            $uid = $_SESSION['uid'];
            $emceeagent = M("member")->where("id=" . $uid)->getField("emceeagent");
            $memagentuid = M("member")->where("id=" . $uid)->getField("agentuid");
            if ($emceeagent == 'y') {
                $familyModel = M('Agentfamily');
                $where = 'uid = '.$uid;
                $agentuid = $familyModel->where($where)->getField("id");
            }else{
                //判断用户是否加入过家族
                if($memagentuid != 0){
                    $familyModel = M('Agentfamily');
                    $where = 'uid = '.$memagentuid;
                    $agentuid = $familyModel->where($where)->getField("id");
                }
            }
        }
        $this->assign("data",$data);
        $this->assign("new","1");
        $this->assign("agentuid", $agentuid);
        $this->assign("num", $num);
        $this->assign("pages", $pages);
        $this->display("index");
    }
    /**
    * 搜索家族
    */
    public function search()
    {
        if (!isset($_GET['name']) || $_GET['name'] == '') {
            $this->redirect("/family");
        }

        $familyModel = M('Agentfamily');
        $where = 'familyname like "%'.$_GET['name'].'%"';
        $count = $familyModel->where($where)->count();

        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page5");
        $p = new Page($count, $listRows, $linkFront);
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $p->firstRow = $listRows * ($_GET['page'] - 1);
        }

        $families = $familyModel->limit($p->firstRow . "," . $p->listRows)->where($where)->select();
        if (!$families) {
            $families = array();
        }

        $memberModel = M('Member');
        foreach ($families as $key => $family) {
            $aid = $family['uid'];
            $userName = $memberModel->where("id={$aid}")->getField('id,username,nickname');
            if (!$userName) {
                $count--;
                continue;
            }

            $anchorCount = $memberModel->where("agentuid={$aid} and sign='y'")->count();
            $memberCount = $memberModel->where("agentuid={$aid}")->count();
            $families[$key]['sqtime'] = date('Y-m-d h:i:s',$family['sqtime']);
            $families[$key]['nickname'] = !$userName[$aid]['nickname'] ? $userName[$aid]['username'] : $userName[$aid]['nickname'];
            $families[$key]['anchorCount'] = $anchorCount;
            $families[$key]['memberCount'] = $memberCount;
        }

        $p->setConfig('header', '条');
        $page = $p->show();
        
        $this->assign('page', $page);
        $this->assign('keyword', $_GET['name']);
        $this->assign('pages', ceil($count/$listRows));
        $this->assign('num',$p->firstRow/20 + 1);
        $this->assign('families', $families);

        $this->display();

    }
    
}