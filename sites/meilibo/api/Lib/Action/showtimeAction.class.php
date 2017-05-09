<?php
class showtimeAction extends BaseAction
{
    public function index()
    {
        // 轮播
        $rollpics = D('xiuchang_voterollpic')->where('')->field('picpath,title,linkurl')->order('orderno asc')->limit(3)->select();
        $this->assign('rollpics', $rollpics);
        
        //首页右侧发现”心“主播 
        $xinemcees = M("member")->where('bigpic<>"" and recommend="y"')->order('rand()')->limit(5)->select();
        //var_dump($xinemcees);
        if (isset($_GET['ajax']) && $_GET['ajax'] == 'getemcee') {
            $this->ajaxReturn($xinemcees);
            exit;
        }
        $this->assign("xinemcees", $xinemcees);
        
        // 调取公告
        $announce = M("announce")->order("addtime")->limit(5)->select();
        $this->assign("announce", $announce);

        // 秀场
        $condition = "id!='0' and size!='2'";
        $orderby = "broadcasting desc, recommend desc";
        $member = D("Member");
        $count = $member->where($condition)->count();
        
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page5");
        $p = new Page($count, $listRows, $linkFront);
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $p->firstRow = $listRows * ($_GET['page'] - 1);
        }
        $members = $member->limit($p->firstRow . "," . $p->listRows)->where($condition)->order($orderby)->select();
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
            $members[$idx]['emceelevel'] = $level_id;
            $members[$idx]['online'] = $virtual_guest + $real_cnt;
        }
        
        $p->setConfig('header', '条');
        $page = $p->show();
        
        $this->assign('page', $page);
        $this->assign('pages', ceil($count/$listRows));
        $this->assign('num',$p->firstRow/20 + 1);
        $this->assign('members', $members);

        $this->display();
    }
}