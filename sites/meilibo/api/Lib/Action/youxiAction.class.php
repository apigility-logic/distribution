<?php
class youxiAction extends BaseAction
{
    
    public function index()
    {
        // 轮播
        $rollpics = D('xiuchang_voterollpic')->where('')->field('picpath,title,linkurl')->order('orderno asc')->limit(3)->select();
        $this->assign('rollpics', $rollpics);
        // 首页右侧发现”心“主播 
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

        // 游戏
        $condition = "id!='0' and size='2'";
        $orderby = "";
        $member = D("Member");
        $count = $member->where($condition)->count();
        
        $listRows = 20;
        $linkFront = 'broadcasting desc,recommend desc';
        import("@.ORG.Page5");
        $p = new Page($count, $listRows, $linkFront);
        $members = $member->limit($p->firstRow . "," . $p->listRows)->where($condition)->order($orderby)->select();
        
        // 查询出等级
        $a = 0;
        foreach ($members as $vo) {
            $emceelevel = getEmceelevel($vo['earnbean']);
            $members[$a]["emceelevel"] = $emceelevel[0]['levelid'];
            $a++;
        }
        
        $p->setConfig('header', '条');
        $page = $p->show();
        
        $this->assign('page', $page);
        $this->assign('members', $members);
        
        $this->display();
    }
}