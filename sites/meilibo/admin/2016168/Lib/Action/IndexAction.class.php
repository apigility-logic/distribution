<?php
require_once APP_PATH . 'Extension/php-sdk-7.1.2/autoload.php';
// 引入鉴权类
use Qiniu\Auth;

// 引入上传类
use Qiniu\Storage\UploadManager;

class IndexAction extends Action {
 /**
    *
    * 权限控制，在此数组里的才能允许鉴黄用户调用
    *
    * @var array
    */
    protected $accessControl = array("login", "dologin","logout","index","mainFrame","leftFrame","listplayerpic","GetShotPicList","SetShotStatus","GetDisableList","GetFloatPicList","listplayerpic_ed");
    protected $publicControl = array("login", "dologin","logout","index","mainframe","leftframe","verify","do_edit_pwd","editpassword");




    /**
    * 鉴黄用户名后缀
    *
    * @var string "_jianhuang"
    */
    protected $_roleJianhuangSuffix = '_jianhuang';

    public function limitlist(){
        $list = M("limitgroup")->select();
        $this->assign("list",$list);
        $this->display();
    }
    public function addlimit(){
        $list = M("backactionlist")->select();
        $seeList = array();
        $editList = array();
        $delList = array();
        foreach($list as $temp){
            if($temp['actiontype'] == "0"){
                $seeList[] = $temp;
            }else if($temp['actiontype'] == "1"){
                $editList[] = $temp;
            }else{
                $delList[] = $temp;
            }
        }
        $this->assign("seeList",$seeList);
        $this->assign("editList",$editList);
        $this->assign("delList",$delList);
        $this->display();
    }
    public function do_addlimit(){
        $limits = $_POST['limits'];
        $list;
        foreach($limits as $limit){
            $list .= $limit . ",";
        }
        $list = substr($list,0,-1);
        $name = $_POST["name"];
        $sql = "insert into ss_limitgroup(name,list)values('{$name}','{$list}')";
        $rs = M()->execute($sql);
        if ($rs > 0){
            $this->success("添加成功");
        }else {
            $this->error("添加失败");
        }
    }
    public function editlimit(){
        $list = M("backactionlist")->select();
        $seeList = array();
        $editList = array();
        $delList = array();
        foreach($list as $temp){
            if($temp['actiontype'] == "0"){
                $seeList[] = $temp;
            }else if($temp['actiontype'] == "1"){
                $editList[] = $temp;
            }else{
                $delList[] = $temp;
            }
        }
        $this->assign("seeList",$seeList);
        $this->assign("editList",$editList);
        $this->assign("delList",$delList);
        $id = $_GET['id'];
        $info = M("limitgroup")->where("id = ".$id)->find();
        $limits = explode(",",$info['list']);
        $this->assign("info",$info);
        $this->assign("limits",$limits);
        $this->display();
    }
    public function do_editlimit(){
        $id = $_POST['id'];
        $limits = $_POST['limits'];
        $list;
        foreach($limits as $limit){
            $list .= $limit . ",";
        }
        $list = substr($list,0,-1);
        $name = $_POST["name"];
        $sql = "update ss_limitgroup set name = '{$name}',list = '{$list}' where id = {$id}";
        $rs = M()->execute($sql);
        if ($rs > 0){
            $this->success("修改成功");
        }else {
            $this->error("修改失败");
        }
    }
    public function dellimit(){
        $id = $_GET['id'];
        $rs = M("limitgroup")->where("id = ".$id)->delete();
        if($rs > 0){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    public function actionList(){
        $Entity = M("backactionlist")->select();
        $this->assign("list",$Entity);
        $this->display();
    }
    public function addactionlist(){
        $this->display();
    }
    public function editactionlist(){
        $id = $_GET['id'];
        $Entity = M("backactionlist")->where("id = ".$id)->find();
        if($Entity == null){
            $Entity = array();
        }
        $this->assign("info",$Entity);
        $this->display();
    }
    public function do_add_action(){
        $type = $_POST['type'];
        $name = $_POST['name'];
        $intro = $_POST['intro'];
        $sql = "insert into ss_backactionlist(actiontype,name,intro) values('{$type}','{$name}','{$intro}')";
        $rs = M()->execute($sql);
        if($rs > 0){
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }
    public function do_edit_action(){
        $id = $_POST['id'];
        $type = $_POST['type'];
        $name = $_POST['name'];
        $intro = $_POST['intro'];
        $sql = "update ss_backactionlist set actiontype ='{$type}',name='{$name}',intro='{$intro}' where id = {$id}";
        $rs = M()->execute($sql);
        if($rs > 0){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function del_action(){
        $id = $_GET['id'];
        $rs = M("backactionlist")->where("id = ".$id)->delete();
        if($rs > 0){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    //家族代理申请相关
    public function del_sqagent(){
        $sqid=$_GET['sqid'];
        //var_dump($sqid);
        $res=M("agentfamily")->where("id=".$sqid)->delete();
        if($res){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
    public function editfamily(){
        $sqid=$_GET['sqid'];
        $uid=M("agentfamily")->where("id=".$sqid)->getField('uid');
        $fix= C('DB_PREFIX');
        $field="m.nickname,m.earnbean,af.*";
        $sqinfo = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("m.id=".$uid)->select();
        $emceelevel = getEmceelevel($sqinfo[0]['earnbean']);
        $sqinfo[0]['emceelevel']=$emceelevel;
        $this->assign("sqinfo",$sqinfo);

        $this->display();
    }

    public function do_edit_sqagent(){
        //var_dump($_POST);
        //根据接收到的信心更新数据库 需要更新 agentfamily表中的状态字段 以及member 表中的emceeagent字段
        $zhuangtai=$_POST['zhuangtai'];
        if ($_POST['familyratio'] + $_POST['anchorratio'] >= 100) {
            $this->error("家族和主播分成比例之和大于100！");
        }
        if (!$_POST['zhuangtai']) {
            $this->error("请选择审核状态");
        }
        $afmodel=M("Agentfamily");
        $mmodel=M("Member");
        if(!empty($_POST)){
            $data = array(
                'shtime'=>time(),
                'zhuangtai'=>$zhuangtai,
                'familyratio'=>$_POST['familyratio'],
                'anchorratio'=>$_POST['anchorratio']
                );
            if($afmodel->where('id='.$_POST['id'])->save($data)){

                    $mmodel->id=$_POST['uid'];
                    if($zhuangtai=="已通过"){
                    $mmodel->emceeagent="y";
                    }else{

                        $mmodel->emceeagent="n";
                    }

                    $mmodel->emceeagenttime=time();
                    if($mmodel->save()){
                        $this->success("审核成功");
                    }else{
                        $this->error("审核失败，重新审核");
                    }

            }else{
                $this->error("审核失败");
            }
        }else{
          $this->error($afmodel->getError());
        }
    }
    public function listfamilynocheck(){
        $count=M("agentfamily")->where("zhuangtai='未审核'")->count();
        //使用联合查询带分页 查询出申请用户的相关信息
        import("@.ORG.Page");
        $p = new Page($count,20);
        $p->setConfig('header','条');
        $page = $p->show();
        $fix= C('DB_PREFIX');
        $field="m.nickname,m.earnbean,af.*";
        $res = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("zhuangtai='未审核'")->limit($p->firstRow.",".$p->listRows)->select();
        //根据查到的earnbean 查询用户等级
        $a=0;
        foreach($res as $k=>$vo){
        $emceelevel = getEmceelevel($vo['earnbean']);
        $res[$a]['emceelevel']=$emceelevel;
        $a++;
        }
        $this->assign("page",$page);
        $this->assign("lists",$res);
        $this->display();
    }
    public function listfamily(){
        $count=M("agentfamily")->where("zhuangtai='已通过'")->count();
        //使用联合查询带分页 查询出申请用户的相关信息
        import("@.ORG.Page");
        $p = new Page($count,20);
        $p->setConfig('header','条');
        $page = $p->show();
        $fix= C('DB_PREFIX');
        $field="m.nickname,m.earnbean,af.*";
        $res = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("zhuangtai='已通过'")->limit($p->firstRow.",".$p->listRows)->select();
        //根据查到的earnbean 查询用户等级
        $a=0;
        foreach($res as $k=>$vo){
        $emceelevel = getEmceelevel($vo['earnbean']);
        $res[$a]['emceelevel']=$emceelevel;
        $a++;
        }
        $this->assign("page",$page);
        $this->assign("lists",$res);
        $this->display();

    }
    public function listfamilyno(){
        $count=M("agentfamily")->where("zhuangtai='未通过'")->count();
        //使用联合查询带分页 查询出申请用户的相关信息
        import("@.ORG.Page");
        $p = new Page($count,20);
        $p->setConfig('header','条');
        $page = $p->show();
        $fix= C('DB_PREFIX');
        $field="m.nickname,m.earnbean,af.*";
        $res = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("zhuangtai='未通过'")->limit($p->firstRow.",".$p->listRows)->select();
        //根据查到的earnbean 查询用户等级
        $a=0;
        foreach($res as $k=>$vo){
        $emceelevel = getEmceelevel($vo['earnbean']);
        $res[$a]['emceelevel']=$emceelevel;
        $a++;
        }
        $this->assign("page",$page);
        $this->assign("lists",$res);
        $this->display();
    }



    //活动页面轮播管理
    public function listactrollpic(){
        $hdrollpics = M("huodongrollpic")->where("")->order('orderno')->select();
        //var_dump($hdrollpics);
        $this->assign("hdrollpics",$hdrollpics);
        $this->display();
    }

    public function save_huodongrollpic()
    {
        $Edit_ID = $_POST['id'];
        $Edit_Orderno = $_POST['orderno'];
        $Edit_Picpath = $_POST['picpath'];
        $Edit_Linkurl = $_POST['linkurl'];
        $Edit_DelID = $_POST['ids'];

        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            M("huodongrollpic")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            M("huodongrollpic")->execute('update ss_huodongrollpic set picpath="'.$Edit_Picpath[$i].'",linkurl="'.$Edit_Linkurl[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
        }


        if($_POST['add_orderno'] != '' && $_FILES['pic']['tmp_name'] != '' && $_POST['add_linkurl'] != ''){
            //上传图片
            import("@.ORG.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = 1048576 ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,png');
            //设置上传目录
            //每个用户一个文件夹
            $prefix = date('Y-m');
            $uploadPath =  'style/huodongrollpic/'.$prefix.'/';
            if(!is_dir($uploadPath)){
                mkdir($uploadPath,0777,true);
            }
            $upload->savePath =  $uploadPath;
            $upload->saveRule = uniqid;
            //执行上传操作
            if(!$upload->upload()) {
                // 捕获上传异常
                if($upload->getErrorMsg() != '没有选择上传文件'){
                    $this->error($upload->getErrorMsg());
                }
            }
            else{
                $uploadList = $upload->getUploadFileInfo();
                $rollpicpath = '/style/huodongrollpic/'.$prefix.'/'.$uploadList[0]['savename'];
            }
            $Rollpic = M("huodongrollpic");
            $Rollpic->create();
            $Rollpic->orderno = $_POST['add_orderno'];
            $Rollpic->picpath = $rollpicpath;
            $Rollpic->linkurl = $_POST['add_linkurl'];
            $Rollpic->addtime = time();
            $rollpicID = $Rollpic->add();
        }


        $this->assign('jumpUrl',__URL__."/listactrollpic/");
        $this->success('操作成功');
    }


    //活动分类管理
    public function del_huodongfenlei(){
            $fenleiid=$_GET["fenleiid"];

        $res=M("announce")->where("fid=".$fenleiid)->select();

        if(!empty($res)){
            $this->error("请先删除当前分类下的文章！");

        }else{
        $del=M("huodongfenlei")->where("id=".$fenleiid)->delete();
        if($del){
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
        }
    }

    public function  editacttype(){
        $fenleiid=$_GET["fenleiid"];
        //var_dump($fenleiid);
        $res=M("huodongfenlei")->where("id=".$fenleiid)->find();
        //var_dump($res);

        $hmodel=M("huodongfenlei");
        if(!empty($_POST)){
            if($hmodel->create()){
                if($hmodel->save()){
                    $this->success("修改成功！","__URL__/listacttype");
                }else{
                    $this->error("修改失败！");
                }
            }else{
                $this->error($hmodel->getError());
            }

        }
        $this->assign("fenlei",$res);
        $this->display();
    }
    public function listacttype(){
        //查询出所有的活动分类
        $res=M("huodongfenlei")->select();
        $this->assign("huodongfenleis",$res);

        $this->display();
    }
    public function addacttype(){

        $hmodel=M("huodongfenlei");
        if(!empty($_POST)){
          if(!empty($_POST['title'])){
            if($hmodel->create()){
                $hmodel->addtime=time();
                if($hmodel->add()){
                    $this->success("添加分类成功","__URL__/listacttype");
                }else{
                    $this->error("添加失败");
                }
            }else{
                $this->error($hmodel->getError());
            }
           }else{
              $this->error("分类标题不能为空！");
        }
        }


        $this->display();
    }

    protected function autoLimit(){

        $ACTION_NAME = strtolower(ACTION_NAME);
        if(in_array($ACTION_NAME, $this->publicControl)){
            //如果当前的ACTION在公共权限组中
            return true;
        }
        //admin为最高用户，直接过滤
        if($_SESSION['adminname'] == "admin" && $_SESSION["adminid"] == 1){
            return true;
        }
        //取出用户的权限信息，可能会包括多个权限组
        $uid = $_SESSION['adminid'];
        $sql = "select limitgroup from ss_admin where id = $uid";
        $limit = M()->query($sql);
        if($limit == null){
            return false;
        }
        $limitids = $limit[0]['limitgroup'];
        //limitList是一个数据数组，将取出的权限组拼装成string然后分解成数组
        $limitList = M("limitgroup")->where("id in (".$limitids .")")->select(); //查出所有权限组的权限信息
        $limitStr;
        foreach($limitList as $Temp){
            $limitStr .= $Temp['list'] .",";
        }
        $limitStr = substr($limitStr,0,-1);
        // $limitArr = explode(",",$limitStr);
        // $limitArr = array_unique($limitArr); //过滤后重复字段的权限数组
        // $limitSQLIds;
        // foreach($limitArr as $tempLimit){
        //  $limitSQLIds = $tempLimit . ",";
        // }
        // $limitSQLIds = substr($limitSQLIds,0,-1);

        //获取用户权限的所有名称
        $UserLimit = M("backactionlist")->field("name")->where("id in (" . $limitStr . ")")->group("name")->select();
        $FinalLimits = array();
        foreach($UserLimit as $temp){
            $temp = strtolower($temp['name']);
            $FinalLimits[] = $temp;
        }
        //不在公共权限组中时，验证用户权限
        if(in_array($ACTION_NAME,$FinalLimits)){
            //如果当前ACTION在用户权限组中
            return true;
        }
        return false;

    }
    function _initialize(){
        C('HTML_CACHE_ON',false);


        if(!$this->autoLimit()){
            $this->error("您的权限暂时不支持执行此操作");
            exit;
        }
        // $this->accessControl();
        $curUrl = base64_encode($_SERVER["REQUEST_URI"]);
        if($_SESSION['lock_screen'] == 1 && !strpos($_SERVER["REQUEST_URI"],'login')){
            session('manager',null);
            session('lock_screen',0);
            session('trytimes',0);

            $this->assign('jumpUrl',__URL__."/login/return/".$curUrl);
            $this->error('请登录后操作');
        }

        if(!strpos($_SERVER["REQUEST_URI"],'login') && !strpos($_SERVER["REQUEST_URI"],'verify') && !strpos($_SERVER["REQUEST_URI"],'logout') && !$_SESSION['manager'])
        {
            $this->assign('jumpUrl',__URL__."/login/return/".$curUrl);
            $this->error('请登录后操作');
        }
    }

    // 空操作定义
    public function _empty() {
        $this->assign('jumpUrl',__URL__.'/mainFrame');
        $this->error('此操作不存在');
    }

    public function verify()
    {
        import("ORG.Util.Image");
         ob_clean();
        Image::buildImageVerify(4,1,'png');
    }

    public function login()
    {
        if($_GET['return']!=''){
            $this->assign('returnurl', $_GET['return']);
        }
        $this->display();
    }

    public function dologin()
    {
        if(md5($_POST['code']) != $_SESSION['verify']){
            //$this->error('验证码错误,请检查!');
            $array = array(
                'code' => 0,
                'info' => '验证码错误,请检查!'
                );
            echo json_encode($array);exit;
        }

        $username = $_POST["username"];
        $password = md5($_POST["password"]);

        $adminDao = D('Admin');
        $admin = $adminDao->where("adminname='".$username."' and password='".$password."'")->select();
        if($admin){
            //写入本次登录时间及IP
            //$adminDao->setField('lastlogtime',time(),"id=".$admin[0]['id']);
            //$adminDao->setField('lastlogip',get_client_ip(),"id=".$admin[0]['id']);
            $adminDao->execute('update ss_admin set lastlogtime='.time().',lastlogip="'.get_client_ip().'" where id='.$admin[0]['id']);

            //写入SESSION
            session('adminid',$admin[0]['id']);
            session('adminname',$_POST["username"]);
            session('manager','y');

            if($_POST['next_action']!=''){
                $this->assign('jumpUrl',base64_decode($_POST['next_action']));
            }
            else{
                $this->assign('jumpUrl',__URL__);
            }
            $array = array(
                'code' => 1,
                'info' => '登录成功'
                );
            echo json_encode($array);exit;
            //$this->success('登录成功');
        }else{
             $array = array(
                'code' => 0,
                'info' => '用户名或密码错误,请检查!'
                );
            echo json_encode($array);exit;
        }
    }

    function logout()
    {
        session('adminid',null);
        session('adminname',null);
        session('manager',null);
        $this->assign('jumpUrl',__URL__.'/login/');
        $this->success('退出成功');
    }

    public function index()
    {
        $adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
        $this->assign("adminqmenus",$adminqmenus);

        $this->display();
    }
    public function leftFrame()
    {
        $adminmenus = D("Adminmenu")->where("parentid=".$_GET['menuid'])->order('id')->select();
        foreach($adminmenus as $n=> $val){
            $adminmenus[$n]['voo']=D("Adminmenu")->where('parentid='.$val['id'])->order('id')->select();

        }

        if($_GET['menuid'] == 1){
            $adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
            $this->assign("adminqmenus",$adminqmenus);
        }

        $this->assign("adminmenus",$adminmenus);

        $this->display();
    }

    public function mainFrame()
    {
        $admin = D("Admin")->find($_SESSION["adminid"]);
        $this->assign('admin',$admin);
        $adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
        $this->assign("adminqmenus",$adminqmenus);

        $this->display();
    }

    public function public_map()
    {
        $adminmenus = D("Adminmenu")->where("parentid=0")->order('id')->select();
        foreach($adminmenus as $n=> $val){
            $adminmenus[$n]['voo']=D("Adminmenu")->where('parentid='.$val['id'])->order('id')->select();
            foreach($adminmenus[$n]['voo'] as $n2=> $val2){
                $adminmenus[$n]['voo'][$n2]['voo2']=D("Adminmenu")->where('parentid='.$val2['id'])->order('id')->select();
            }
        }
        $this->assign("adminmenus",$adminmenus);
        $this->display();
    }

    public function public_current_pos()
    {
        $menu = D("Adminmenu")->find($_GET["menuid"]);
        if($menu){
            echo $menu['position'];
        }
    }

    public function public_ajax_add_panel()
    {
        $menu = D("Adminmenu")->find($_POST["menuid"]);
        if($menu){
            $qmenu = D("Adminqmenu")->where("adminid=".$_SESSION['adminid']." and menuid=".$_POST["menuid"])->select();
            if(!$qmenu && $menu['url'] !=''){
                $qmenu = D("Adminqmenu")->execute("insert into ss_adminqmenu(adminid,menuid,menuname,url,addtime) values(".$_SESSION['adminid'].",".$_POST["menuid"].",'".$menu['menuname']."','".$menu['url']."',".time().")");
            }
        }

        $adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
        foreach($adminqmenus as $n=> $val){
            echo "<span><a onclick='paneladdclass(this);' target='right' href='".$val['url']."'>".$val['menuname']."</a>  <a class='panel-delete' href='javascript:delete_panel(".$val['menuid'].");'></a></span>";
        }
    }

    public function public_ajax_delete_panel()
    {
        D("Adminqmenu")->where('adminid='.$_SESSION["adminid"].' and menuid='.$_POST["menuid"])->delete();

        $adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
        foreach($adminqmenus as $n=> $val){
            echo "<span><a onclick='paneladdclass(this);' target='right' href='".$val['url']."'>".$val['menuname']."</a>  <a class='panel-delete' href='javascript:delete_panel(".$val['menuid'].");'></a></span>";
        }
    }



    public function public_session_life()
    {
        session('adminid',$_SESSION['adminid']);
        session('adminname',$_SESSION['adminname']);
        session('manager','y');
    }

    public function public_lock_screen()
    {
        session('lock_screen',1);
    }

    public function public_login_screenlock()
    {
        $password = md5($_REQUEST["lock_password"]);

        $adminDao = D('Admin');
        $admin = $adminDao->where("adminname='".$_SESSION['adminname']."' and password='".$password."'")->select();
        if($admin){
            echo '1';
            session('lock_screen',0);
            session('trytimes',0);
            exit;
        }
        else{
            if($_SESSION['trytimes'] == 3){
                echo '3';
                exit;
            }

            if($_SESSION['trytimes'] == ''){
                echo '2|2';
                session('trytimes',1);
                exit;
            }
            else{
                echo '2|'.(2-$_SESSION['trytimes']);
                session('trytimes',($_SESSION['trytimes']+1));
                exit;
            }
        }
    }

    public function editpassword()
    {
        if($_GET['action'] == 'public_password_ajx'){
            $password = md5($_GET["old_password"]);
            $admin = D("Admin")->where("adminname='".$_SESSION["adminname"]."' and password='".$password."'")->select();
            if($admin){
                echo '1';
            }
            else{
                echo '0';
            }
            exit;
        }

        $admin = D("Admin")->find($_SESSION["adminid"]);
        $this->assign('admin',$admin);

        $this->display();
    }

    public function do_edit_pwd()
    {

        $this->error('该功能被屏蔽！');



        // if($_POST['new_password'] == ''){
        //     $this->assign('jumpUrl',__URL__."/editpassword/");
        //     $this->success('修改成功');
        // }

        // $oldpassword = md5($_POST["old_password"]);
        // $adminDao = D('Admin');
        // $admininfo = $adminDao->where("adminname='".$_SESSION["adminname"]."' and password='".$oldpassword."'")->select();
        // if($admininfo){
        //     $vo = $adminDao->create();
        //     if(!$vo) {
        //         $this->error($adminDao->getError());
        //     }else{
        //         $adminDao->password = md5($_POST['new_password']);
        //         $adminDao->save();

        //         $this->assign('jumpUrl',__URL__."/editpassword/");
        //         $this->success('修改成功');
        //     }
        // }
        // else{
        //     $this->error('旧密码输入错误');
        // }
    }



    public function cache_all()
    {
        $this->deldir('../Runtime');

        $referer = $_SERVER['HTTP_REFERER'];
        $urlArr = explode("/2016168/", $referer);
        if($urlArr[1] == ''){
            $this->assign('jumpUrl',__URL__.'/mainFrame');
        }
        $this->success('缓存更新成功');
    }

    protected function deldir($dir) {
        if (!file_exists($dir)){
            return true;
        }
        else{
            @chmod($dir, 0777);
        }
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                }
                else {
                    $this->deldir($fullpath);
                }
            }
        }

        closedir($dh);

        if(rmdir($dir)) {
            return true;
        }
        else {
            return false;
        }
    }

    //设置
    public function listsyspara()
    {   
      
        $siteconfig = D("Siteconfig")->find(1);
        if($siteconfig){
            $this->assign('siteconfig',$siteconfig);
        }
        else{
            $this->assign('jumpUrl',__URL__.'/mainFrame');
            $this->error('系统参数读取错误');
        }
      
        $select=M('keyword');
             $orderby = 'id desc';
                $count = $select->count();
                $listRows = 20;
                $linkFront = '';
                import("@.ORG.Page");
                $p = new Page($count,$listRows,$linkFront);

                $select_data = $select->limit($p->firstRow.",".$p->listRows)->order($orderby)->select();
          // print_r($select_data);
                $p->setConfig('header','条');
                $page = $p->show();
                $this->assign('page',$page);
                $this->assign('select_data',$select_data);
                //数据搜索
       
              //  $this->display('listsyspara');
        

        $this->display();
        
    }
    public function show_keyword(){
         if(isset($_POST['keyword_search'])){
              $form_data=$_POST['keyword_search'];
              $_SESSION['keywords']=$form_data;
              $like_data['key_word']=array('like','%'.$form_data.'%');
              $key_word=M('keyword');
              $words=$key_word->where($like_data)->select();
              $this->assign('show_data',$words);
              $this->display('show_keyword');

            //  $this->assign('show_data',$words);
            //  $this->display('listsyspara');
         }
    }

    public function delete_key(){
            if(isset($_GET['del'])){
            $del_id['id']=$_GET['del'];
            $dels=M('keyword');
            if($dels->where($del_id)->delete()){
                $this->success('删除成功@');
            }else{
                $this->error('删除失败@！');
            }
        }
    }
     public function delete_show_key()
     {
         if(isset( $_SESSION['keywords'])){
                   $like_data['key_word']=array('like','%'. $_SESSION['keywords'].'%');
                   $key_word=M('keyword');
                   $words=$key_word->where($like_data)->select();
                   $this->assign('show_data',$words);                
              if(isset($_GET['del_key'])){
                        $del_id['id']=$_GET['del_key'];
                        $dels=M('keyword');
                  if($dels->where($del_id)->delete()){
                           echo "<script>alert('删除成功');history.go(-1);</script>";  
                        $this->display('show_keyword'); 
                }else{
                           echo "<script>alert('已经删除成功！,请不要重复操作！@谢谢');history.go(-1);</script>";  
                      }
                }
        }else{
             $this->display('listsyspara');
        }
    }
 
    public function add_keyword(){
        if(isset($_POST['add_keyword'])){
            $key_data=$_POST['add_keyword'];
            $arrs=explode(',',$key_data);
            if(is_array($arrs)){
                    $keyword=M('keyword');  
                foreach($arrs as $key=>$value)
                {
                        $datas['key_word']=$value;
                   if(!$keyword->data($datas)->add()){
                        $this->error('添加的关键字失败@！');
                     }else{
                        $this->success('添加的关键字成功@');
                    };
               }
                            }
        }
    }
    public function robot_option(){
        if(isset($_GET['options'])){
            $robot_switch['id']=1;
            $robot_switch['robot_switch']=$_GET['options'];
            $obj_robot=M('Siteconfig');
            $affected= $obj_robot->data($robot_switch)->save();
           if($affected==false){
                $this->error('机器人操作失败');
           }else{
               $this->success('机器人操作成功');

           }

        }
    }

    public function save_syspara()
    {
        $siteconfig = D('Siteconfig');

        $vo = $siteconfig->create();
        if(!$vo) {
            $this->assign('jumpUrl',__URL__.'/listsyspara/');
            $this->error('修改失败');
        }else{
            $siteconfig->save();

            $smsid=$_POST['smsid'];
            $smskey=$_POST['smskey'];
            $cdn=$_POST['cdn'];
            $fps=$_POST['fps'];
            $zddk=$_POST['zddk'];
            $pz=$_POST['pz'];
            $zjg=$_POST['zjg'];
            $cdnl=$_POST['cdnl'];
            $height=$_POST['height'];
            $width=$_POST['width'];
            $apkversion=$_POST['apkversion'];
            $ipaversion=$_POST['ipaversion'];
            $sign_verification=$_POST['sign_verification'];
            $ios_goods=$_POST['ios_goods'];
            $canlive = $_POST['canlive'];
            $room_stop_time= strtotime($_POST['room_stop_time']);
            $room_start_time= strtotime($_POST['room_start_time']);
            if ( $room_start_time < $room_stop_time ) {
                $this->error('直播设置->直播关闭时间段开始时间小于关闭时间');
            }
            $sql="update ss_siteconfig set smsid='{$smsid}',smskey='{$smskey}',
            cdn='{$cdn}',fps='{$fps}',zddk='{$zddk}',pz='{$pz}',zjg='{$zjg}',
            cdnl='{$cdnl}',height='{$height}',width='{$width}',apkversion='{$apkversion}',
            ipaversion='{$ipaversion}',sign_verification='{$sign_verification}',
            ios_goods='{$ios_goods}', room_start_time='{$room_start_time}', room_stop_time='{$room_stop_time}',,canlive='{$canlive}'
            where id=1";
            M('siteconfig')->execute($sql);
            $this->assign('jumpUrl',__URL__.'/listsyspara/');
            $this->success('修改成功');
        }
    }

    public function listdeduct()
    {
        $siteconfig = D("Siteconfig")->find(1);
        if($siteconfig){
            $this->assign('siteconfig',$siteconfig);
        }
        else{
            $this->assign('jumpUrl',__URL__.'/mainFrame');
            $this->error('系统参数读取错误');
        }
        $this->display();
    }
    //验证比例是否是0-100之间的数
    public function CheckProp($str){
        if(is_numeric($str)){
            if($str >= 0 && $str <= 100){
                return true;
            }else{
                // 数字区域不对
                return false;
            }
        }else{
            // 不是数字
            return false;
        }
    }
    public function save_deduct()
    {   
        if(!$this -> CheckProp((double)$_POST["emceededuct"]) || !$this -> CheckProp((double)$_POST["marketratio"])){
            $this->assign('jumpUrl',__URL__.'/listdeduct/');
            $this->error('输入的比例范围:0-100');
            exit;
        }
        $siteconfig =  D("Siteconfig");
        $data = array(
            'id'    =>  '1',
            'emceededuct' => round( $_POST['emceededuct'], 2),
            'marketratio' => round( $_POST['marketratio'], 2),
            'cash_proportion' => round( $_POST['cash_proportion'], 2)
            ); 
        if( $siteconfig->save( $data ) === false ) {
            $this->assign('jumpUrl',__URL__.'/listdeduct/');
            $this->error('修改失败');
        }else{
            $this->assign('jumpUrl',__URL__.'/listdeduct/');
            $this->success('修改成功');
        }
    }

    public function listrollpic(){
        $rollpics = D("Rollpic")->where("")->order('moc','desc')->select();
        $this->assign("rollpics",$rollpics);
        $this->display();
    }
     public function save_rollpic()
    {
        $Edit_ID = $_POST['id'];
        $Edit_Orderno = $_POST['orderno'];
        $Edit_Picpath = $_POST['picpath'];
        $Edit_Linkurl = $_POST['linkurl'];
        $Edit_Uid = $_POST['uid'];
        $Edit_Moc = $_POST['moc'];
        $Edit_DelID = $_POST['ids'];
        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("Rollpic")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            if($Edit_Uid[$i] != 0){
                if( count( M('member')->where('id='.$Edit_Uid[$i])->select() ) <= 0) {
                    $this->error("用户id不存在");
                }
            }
            D("Rollpic")->execute('update ss_rollpic set picpath="'.$Edit_Picpath[$i].'",linkurl="'.$Edit_Linkurl[$i].'",uid="'.$Edit_Uid[$i].'",moc = "'.$Edit_Moc[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
        }

        if($_FILES['pic']['tmp_name'] != '' ){
            //上传图片
            import("@.ORG.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = 1048576 ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,png');
            //设置上传目录
            //每个用户一个文件夹
            $prefix = date('Y-m');
            $uploadPath =  APP_WEB.'/style/rollpic/'.$prefix.'/';
            if(!is_dir($uploadPath)){
                mkdir($uploadPath,0777,true);
            }
            $upload->savePath =  $uploadPath;
            $upload->saveRule = uniqid;
            //执行上传操作
            if(!$upload->upload()) {
                // 捕获上传异常
                if($upload->getErrorMsg() != '没有选择上传文件'){
                    $this->error($upload->getErrorMsg());
                }
            }
            else{
                $uploadList = $upload->getUploadFileInfo();
                $rollpicpath = '/style/rollpic/'.$prefix.'/'.$uploadList[0]['savename'];
            }
            $Rollpic = D('Rollpic');
            $Rollpic->create();
            $Rollpic->orderno = $_POST['add_orderno'];
            $Rollpic->picpath = $rollpicpath;
            $Rollpic->linkurl = $_POST['add_linkurl'];
            $Rollpic->uid = $_POST['add_uid'] ?  $_POST['add_uid'] : 0;
            $Rollpic->moc = $_POST['add_moc'];
            $Rollpic->addtime = time();
            $rollpicID = $Rollpic->add();
        }


        $this->assign('jumpUrl',__URL__."/listrollpic/");
        $this->success('操作成功');
    }

    public function listannounce()
    {
        $condition = '';

        $orderby = 'addtime desc';
        $announce = D("Announce");
        $count = $announce->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $announces = $announce->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('announces',$announces);

        $this->display();
    }

    public function addannounce(){
        //查询出当前所有的分类
        $fenleis=M("huodongfenlei")->select();
        $this->assign("fenlei",$fenleis);

        $this->display();
    }

    public function do_add_announce(){
        //var_dump($_POST);
        $announce=D("Announce");
          if(!empty($_POST)){
            import("ORG.Net.UploadFile");
            //实例化上传类
            $upload = new UploadFile();
            $upload->maxSize = 3145728;
            //设置文件上传类型
            $upload->allowExts = array('jpg','gif','png','jpeg');
            //设置文件上传位置
            $upload->savePath = APP_WEB."/style/Uploads/";//这里说明一下，由于ThinkPHP是有入口文件的，所以这里的./Public是指网站根目录下的Public文件夹
            //设置文件上传名(按照时间)
            $upload->saveRule = "time";
            if (!$upload->upload()){
                $this->error($upload->getErrorMsg());
            }else{
                //上传成功，获取上传信息
                $info = $upload->getUploadFileInfo();
            }
          $savename = $info[0]['savename'];




        $vo = $announce->create();

        $announce->fengmian=$savename;
    //var_dump($vo);



        if(!$vo) {
            $this->error($announce->getError());
        }else{
            $annId = $announce->add();

        }
    }
        $this->assign('jumpUrl',__URL__."/listannounce/");
        $this->success('添加成功');
    }

    public function editannounce(){
        $fenleis=M("huodongfenlei")->select();
        $this->assign("fenlei",$fenleis);
        if($_GET['annid'] == ''){
            $this->error('参数错误');
        }
        else{
            $anninfo = D("Announce")->getById($_GET["annid"]);
            if($anninfo){
                $this->assign('anninfo',$anninfo);
            }
            else{
                $this->error('找不到该公告');
            }
        }

        $this->display();
    }

    public function do_edit_announce(){
        if($_POST["id"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $anninfo = D("Announce")->getById($_POST["id"]);
            if(!$anninfo){
                $this->error('该公告不存在');
            }
        }

        $announce=D("Announce");
        $vo = $announce->create();
        if(!$vo) {
            $this->error($announce->getError());
        }else{
            $announce->save();
        }

        $this->assign('jumpUrl',__URL__."/editannounce/annid/".$_POST['id']);
        $this->success('修改成功');
    }

    public function del_announce(){
        if($_GET["annid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Announce");
            $anninfo = $dao->getById($_GET["annid"]);
            if($anninfo){
                $dao->where('id='.$_GET["annid"])->delete();
                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该公告');
            }
        }
    }

    public function opt_announce()
    {
        $dao = D("Announce");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $anninfo = $dao->getById($array[$i]);
                        if($anninfo){
                            $dao->where('id='.$array[$i])->delete();
                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function listadmin()
    {
        $adminusers = D("Admin")->where("")->order('addtime')->select();
        $this->assign("adminusers",$adminusers);
        $this->display();
    }

    public function editadmin(){
        header("Content-type: text/html; charset=utf-8");
        if($_GET['adminid'] == ''){
            echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
        else{
            $admininfo = D("Admin")->find($_GET["adminid"]);
            if($admininfo){
                $this->assign('admininfo',$admininfo);
            }
            else{
                echo '<script>alert(\'找不到该管理员\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            }
        }
        $LimitList = M("limitgroup")->select();
        $LimitList = !$LimitList ? array() : $LimitList;
        $this->assign("limitgroup",$LimitList);
        $this->display();
    }

    public function do_edit_adminuser(){
        header("Content-type: text/html; charset=utf-8");
        $id = $_POST["id"];
        $group = $_POST["group"];
        $limits;
        foreach ($group as $temp) {
            $limits .= $temp . ",";
        }
        $limits = substr($limits,0,-1);
        if($_POST["password"] != ""){
            $password = md5($_POST["password"]);
            $sql = "update ss_admin set password = '{$password}',limitgroup = '{$limits}' where id= '{$id}'";
        }else{
            $sql = "update ss_admin set limitgroup = '{$limits}' where id= '{$id}'";
        }
        $rs = M()->execute($sql);
        if($rs >= 0) {
            echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }else{
            echo '<script>alert(\'修改失败\');window.top.art.dialog({id:"edit"}).close();</script>';
        }
    }

    public function del_adminuser(){
        if($_GET["adminid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Admin");
            $admininfo = $dao->find($_GET["adminid"]);
            if($admininfo){
                $dao->where('id='.$_GET["adminid"])->delete();
                $this->assign('jumpUrl',__URL__.'/listadmin/');
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该管理员');
            }
        }
    }

    public function addadmin(){
        $LimitList = M("limitgroup")->select();
        $LimitList = !$LimitList ? array() : $LimitList;
        if($_GET['clientid'] == 'username'){
            $admininfo = D("Admin")->where("adminname='".$_GET['username']."'")->select();
            if($admininfo){
                echo '0';
                exit;
            }
            else{
                echo '1';
                exit;
            }
        }
        $this->assign("limitgroup",$LimitList);
        $this->display();
    }

    public function do_add_adminuser(){
        if($_POST['adminname'] == ''){
            $this->error('用户名不能为空');
        }
        if($_POST['password'] == ''){
            $this->error('密码不能为空');
        }
        $password = md5($_POST["password"]);
        $adminname = $_POST["adminname"];
        $group = $_POST["group"];
        $limits;
        foreach ($group as $temp) {
            $limits .= $temp . ",";
        }
        $limits = substr($limits,0,-1);
        $addtime = time();
        $sql = "insert into ss_admin(adminname,password,limitgroup,addtime) values('{$adminname}','{$password}','{$limits}',{$addtime})";
        $rs = M()->execute($sql);
        if($rs > 0) {
            $this->assign('jumpUrl',__URL__.'/listadmin/');
            $this->success('添加成功');
        }else{
            $this->error('添加失败');
        }
    }

    //用户1
    public function listuser()
    {
        $condition = 'isdelete="n" and is_robot = 0 ';
        if(!empty($_GET['start_time'])){
            $condition .= ' and regtime>='.strtotime($_GET['start_time']);
        }
        if(!empty($_GET['end_time'])){
            $condition .= ' and regtime<='.strtotime($_GET['end_time']+1);
        }
        if(!empty($_GET['keyword']) && $_GET['keyword'] != '请输入用户ID或用户名'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\' or nickname like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and (username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\') ';
            }
        }
        if(!empty($_GET['sign'])){
            $condition .= ' and sign="'.$_GET['sign'].'"';
        }
        if(!empty($_GET['emceeagent'])){
            $condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
        }
        if(!empty($_GET['payagent'])){
            $condition .= ' and payagent="'.$_GET['payagent'].'"';
        }
        if(!empty($_GET['recommend'])){
            switch ($_GET['recommend']) {
                // case '0':
                //     $condition .= ' and recommend="n" and idxrec="n" ';
                //     break;
                case '1':
                    $condition .= ' and recommend="y"  ';
                    break;
                case '2':
                    $condition .= ' and idxrec="y" ';
                    break;
                // case '3':
                //     $condition .= ' and recommend="y" and idxrec="y" ';
                //     break;
                default:
                    break;
            }
        }
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);

        $members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();

        $banlists = M('banlist')->where('banstatus = "0"')->select();
        $this->assign('banlists',$banlists);
        // echo $member->getLastSql();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('members',$members);
        $this->display();
    }

    public function test() {




    }

    public function test2() {
        $memcache = new Memcache();
        $memcache->addServer(C('MEMCACHE'));

        print_r(json_decode($memcache->get("PHPCHAT_ROOM_1589571455"),true));
    }

    /**
     * 获取在线的主播列表
     */
    public function onlivelist() {
        $list = M("Member")->where(array('broadcasting'=>'y'))->field('id,nickname,curroomnum,beanorignal,onlinenum,starttime')->select();
        $this->assign('onlivelist',$list);
        $this->display();
    }

    /**
     * 在线直播房间
     */
    public function onroomlive() {
        $memcache = new Memcache();
        $memcache->addServer(C('MEMCACHE'));

        $roomId = intval($_GET['room_id']);
        $uid = intval($_GET['uid']);
        import('Common.Gateway', APP_PATH, '.php');
        Gateway::$registerAddress = C('REGISTER_ADDRESS');
        $count = Gateway::getClientCountByGroup($roomId);
        //获取该房间的缓存信息
        $memcache->get("PHPCHAT_ROOM_".$roomId);
        $roomInfo = json_decode($memcache->get("PHPCHAT_ROOM_".$roomId),true);
        //获取禁言的用户id组
        if(isset($roomInfo['disableMsg']) && is_array($roomInfo['disableMsg'])) {
            foreach($roomInfo['disableMsg'] as $key => $v) {
                //如果未过期
                if($v>=time()){
                    $userIds[] = $key;
                }
            }
        }
        //获取被踢的用户id组
        if(isset($roomInfo['kicked']) && is_array($roomInfo['kicked'])) {
            foreach($roomInfo['kicked'] as $key => $v) {
                //如果未过期
                if($v>=time()){
                    $userIds[] = $key;
                }
            }
        }

        //获取这个主播房间的房间管理员
        $adminerUser = M()->query("select a.id,a.uid,a.adminuid,b.nickname,b.spendcoin,b.regtime,b.lastlogtime from  ss_roomadmin as a left join ss_member as b on(a.adminuid=b.id) where a.uid = {$uid}");

        //获取被禁言或者被踢的用户
        $users = M("Member")->where("id in (" . implode(',',$userIds).")" )->field('id,nickname,spendcoin,regtime,lastlogtime')->select();
        $userMap = array();
        foreach($users as $user) {
            $userMap[$user['id']] = $user;
        }

        //获取被禁言用户的信息
        $disableMsgUser = array();
        if(isset($roomInfo['disableMsg']) && is_array($roomInfo['disableMsg'])) {
            foreach($roomInfo['disableMsg'] as $key => $v) {
                //如果时间未过期
                if($v>=time()){
                    $userIds[] = $key;
                    if(isset($userMap[$key])) {
                        $tmp = $userMap[$key];
                        $tmp['expireTime'] = $v;
                        $disableMsgUser[] = $tmp;
                    }
                }
            }
        }

        //湖区被踢用户的信息
        $kickedUser = array();
        if(isset($roomInfo['kicked']) && is_array($roomInfo['kicked'])) {
            foreach($roomInfo['kicked'] as $key => $v) {
                //如果时间未过期
                if($v>=time()){
                    $userIds[] = $key;
                    if(isset($userMap[$key])) {
                        $tmp = $userMap[$key];
                        $tmp['expireTime'] = $v;

                        $kickedUser[] = $tmp;
                    }
                }
            }
        }
        //将房间号，和主播uid传到前台
        $this->assign("roomid",$roomId);
        $this->assign("uid",$uid);
        $this->assign("count",$count);
        $this->assign('adminerUser',$adminerUser);
        $this->assign('disableMsgUser',$disableMsgUser);
        $this->assign('kickedUser',$kickedUser);
        $this->display();
    }

    /**
     * 移除房间管理员
     * 1. 移除表ss_roomadmin中uid与adminuid的组合
     * 2. 缓存中的该房间对应的房间的管理修改
     */
    public function del_roomadmin(){
        //获取房间号和主播uid
        $roomId = intval($_GET['roomid']);
        $uid = intval($_GET['uid']);
        //获取房间管理员id
        $adminuid = intval($_GET["adminuid"]);
        $memcache = new Memcache();
        $memcache->addServer(C('MEMCACHE'));
        //获取该房间的缓存信息
        $memcache->get("PHPCHAT_ROOM_".$roomId);
        $roomInfo = json_decode($memcache->get("PHPCHAT_ROOM_".$roomId),true);
        //删除表中数据
        $result = M("roomadmin")->where("uid={$uid} and adminuid={$adminuid}")->delete();
        //如果删除成功
        if ($result){
            $adminer = $roomInfo["adminer"];
            $replace = array();
            foreach($adminer as $k=>$v){
                if($v!=$adminuid){
                    $replace[$k]=$v;
                }
            }
            $roomInfo["adminer"]=$replace;
            //删除成功清除缓存里面对应的管理员id
            $memcache->set("PHPCHAT_ROOM_".$roomId,json_encode($roomInfo));
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 恢复被禁言的用户，对应房间缓存的被禁言用户修改
     */
    public function recovery_user()
    {
        //获取房间号和会员uid
        $roomId = intval($_GET['roomid']);
        $uid = intval($_GET['uid']);
        $memcache = new Memcache();
        $memcache->addServer(C('MEMCACHE'));
        //获取该房间的缓存信息
        $memcache->get("PHPCHAT_ROOM_".$roomId);
        $roomInfo = json_decode($memcache->get("PHPCHAT_ROOM_".$roomId),true);
        $disableMsg = $roomInfo["disableMsg"];
        $replace = array();
        foreach($disableMsg as $k=>$v){
            if($k!=$uid){
                $replace[$k]=$v;
            }
        }
        $roomInfo["disableMsg"]=$replace;
        $memcache->set("PHPCHAT_ROOM_".$roomId,json_encode($roomInfo));
        //清除session里面的对应的禁言信息
        import('Common.Gateway', APP_PATH, '.php');
        Gateway::$registerAddress = C('REGISTER_ADDRESS');
        $client_id = Gateway::getClientIdByUid($uid);
        if (!empty($client_id)) {
            $session = Gateway::getSession($client_id[0]);
            unset($session['disableMsg']);
            unset($session['expire']);
            Gateway::setSession($client_id[0], $session);
        }
        return $this->success("恢复成功");
    }

    /**
     * 恢复被踢的会员，对应房间中被踢的会员修改
     */
    public function recovery_kicked()
    {
        //获取房间号和主播uid
        $roomId = intval($_GET['roomid']);
        $uid = intval($_GET['uid']);
        $memcache = new Memcache();
        $memcache->addServer(C('MEMCACHE'));
        //获取该房间的缓存信息
        $memcache->get("PHPCHAT_ROOM_".$roomId);
        $roomInfo = json_decode($memcache->get("PHPCHAT_ROOM_".$roomId),true);
        $kicked = $roomInfo["kicked"];
        $replace = array();
        foreach($kicked as $k=>$v){
            if($k!=$uid){
                $replace[$k]=$v;
            }
        }
        $roomInfo["kicked"]=$replace;
        $memcache->set("PHPCHAT_ROOM_".$roomId,json_encode($roomInfo));
        return $this->success("恢复成功");
    }

    public function doIsaudit(){
        $id = $_GET['id'];
        $end_time = $_GET['end_time'];
        $url = $_GET['return'];
        $bantime = strtotime($end_time);
        if( $bantime < time()){
            $this->error("选择时间必须大于今天");
        }else{
            $mdata = array(
                'id'    =>  $id,
                'isaudit'   =>  'n'
                );
            $bandata = array(
                'uid'   =>  $id,
                'banduration'   =>  $bantime,
                'bantime'   =>  time(),
                'banadmin'  =>  $_SESSION['adminname']
                );

            $data = array(
                'type'    => 'exit',
                'message' => '您已经被禁止登陆',
            );

            import('Common.Gateway', APP_PATH, '.php');
            Gateway::$registerAddress = C('REGISTER_ADDRESS');
            Gateway::sendToUid($id,json_encode($data));

            // echo M('member')->save($mdata);
            // echo "<br>".M('member')->_sql();
            // echo M('banlist')->data($bandata)->add();
            // echo "<br>".M('banlist')->_sql();
            $banlist_dao = M('banlist');
            if($banlist_dao->where('uid = '.$id.' and banstatus = "0"')->select()){
                $this->error("该用户已被禁用");
            }else{
                $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
                $url = $domain . '/OpenAPI/v1/user/adminloginout';
                $data = array('uid'=>$id);
                $info = $this->curlRequest($url,true,$data);
                $info = json_decode($info);
                if(M('member')->save($mdata) && $banlist_dao->data($bandata)->add() && $info->code = 'ok'){
                    $this->assign('jumpUrl',base64_decode($_GET['return']));
                    $this->success("成功禁用");
                }else{
                    $this->error("禁用失败，请稍后重试...");
                }
            }
        }
    }
    public function editaudit()
    {
        $id = $_GET['userid'];
        $member = M('member')->where('id = '.$id)->field('isaudit')->find();
        if($member['isaudit'] == 0 ){
            $this->assign('id', $id);
            $this->display();
        }elseif($member['isaudit'] == 1){
            $this->error("该用户已被禁用");
        }else{
            $this->error("参数错误");

        }
    }
    public function edituser(){
        if($_GET['userid'] == ''){
            echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
        else{
            $userinfo = D("Member")->getById($_GET["userid"]);
            if($userinfo){
                $userinfo['bigpic'] = getAvatar($userinfo['avatartime'],$userinfo['id'], "big");
                $this->assign('userinfo',$userinfo);

                $usersorts = D("Usersort")->where("parentid <> 0 and isapprove = '0'")->order('addtime')->select();
                foreach($usersorts as $n=> $val){
                    $usersorts[$n]['voo']=D("Usersort")->where('parentid='.$val['id'])->order('addtime')->select();
                }
                $this->assign("usersorts",$usersorts);

                $servers = D("Server")->where("")->order('addtime')->select();
                $this->assign("servers",$servers);
            }
            else{
                echo '<script>alert(\'找不到该用户\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            }
        }

        $this->display();
    }

    public function do_edit_user(){
        header("Content-type: text/html; charset=utf-8");
        if($_POST["id"] == '')
        {
            echo '<script>alert(\'缺少参数或参数不正确\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            exit;
        }
        else{
            $userinfo = D("Member")->getById($_POST["id"]);
            if(!$userinfo){
                echo '<script>alert(\'该用户不存在\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
                exit;
            }
        }
        if (!empty($_FILES['bigpic']['tmp_name'])){
               $avatartime = 0;
            //if(empty($_FILES['bigpic']['tmp_name']) || empty($_FILES['bigpic2']['tmp_name'])){
                //上传缩略图
                import('ORG.Net.UploadFile');
                $upload = new UploadFile();// 实例化上传类
                //设置上传文件大小
                $upload->maxSize = 3145728;
                //设置上传文件类型
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
                //设置附件上传目录
                $upload->saveRule = $userinfo['id'];  // 文件名称
                $upload->savePath  = realpath(APP_PATH.'/../').getUploadPath($userinfo['id']); // 设置附件上传（子）目录
                $upload->autoSub = false; //是否生成日期文件夹

                $upload->thumbRemoveOrigin = false; //设置生成缩略图后移除原图
                $upload->uploadReplace =  true;  // 覆盖同名
                $upload->thumbPrefix = '';  // 前缀
                $upload->thumbSuffix = ',_big,_middle,_small'; // 后缀
                $upload->thumbExt = 'jpg';

                if (!is_dir($upload->savePath)) {
                    mkdir($upload->savePath, 0777, true);
                }
                //设置需要生成缩略图，仅对图像文件有效
                $upload->thumb = true;
                 // 设置引用图片类库包路径
                $upload->imageClassPath = 'ORG.Util.Image';
                //设置缩略图最大宽度
                $upload->thumbMaxWidth = '400,200,120,48';
                //设置缩略图最大高度
                $upload->thumbMaxHeight = '400,200,120,48';
                // 上传文件
                @$info   =   $upload->upload();
                if(!$info) {// 上传错误提示错误信息
                    if($upload->getErrorMsg() != '没有选择上传文件'){
                        $this->error($upload->getErrorMsg());
                    }
                }else{// 上传成功 获取上传文件信息
                    $avatartime = time();
                }
            //}
        }
        $Member=D("Member");
        $vo = $Member->create();

        if(!$vo) {
            $this->error($Member->getError());
        }else{
            $vipexpire = strtotime($Member->vipexpire);
            if ($Member->vip != 0 && $vipexpire < time()) {
                echo '<script>alert(\'请选择正确VIP时间！\')</script>';
                exit;
            }
            $Member->vipexpire = $vipexpire;


  //密码
            // if($_POST['newpwd'] != ''){
            //     include APP_PATH.'config.inc.php';
            //     include APP_PATH.'uc_client/client.php';
            //     $ucresult = uc_user_edit($userinfo['username'], '', $_POST['newpwd'], $userinfo['email'], 1);
            //     if($ucresult == -1) {
            //         $this->error('旧密码不正确');
            //     } elseif($ucresult == -4) {
            //         $this->error('Email 格式有误');
            //     } elseif($ucresult == -5) {
            //         $this->error('不允许注册');
            //     } elseif($ucresult == -6) {
            //         $this->error('该 Email 已经被注册');
            //     }
            // }
            $Member->birthday = strtotime($_POST['birthday']);
            $Member->password = md5($_POST['newpwd']);
            $Member->password2 = $this->pswencode($_POST['newpwd']);
            if (!empty($_FILES['bigpic']['tmp_name'])){
                $Member->avatartime = $avatartime;
            }

            if($_POST['agentname'] != ''){
                if($_POST['agentname'] == $userinfo['username']){
                    $error = '自已不能做自己的代理';
                }
                else{
                    $agentinfo = D("Member")->where('username="'.$_POST['agentname'].'"')->select();
                    if($agentinfo){
                        if($agentinfo[0]['emceeagent'] == 'n'){
                            $error = '指定的代理人没有代理权限';
                        }
                        else{
                            $Member->agentuid = $agentinfo[0]['id'];
                        }
                    }
                    else{
                        $error = '没有找到指定的代理人信息';
                    }
                }
            }
            else{
                $Member->agentuid = 0;
            }
            if(!$this -> CheckProp($_POST["ratio"])){
                $error = '输入的比例范围:0-100';
                $Member->ratio = 100;
            }else{
                $Member->ratio = $_POST['ratio'];
            }
            if(strlen($_POST['linkurl']) > 400 ){
                $error = '链接地址长度不能大于400';
            }else{
                $Member->linkurl = $_POST['linkurl'];
            }
            if($_POST['payagent'] == 'y'){
                $Member->sellm = '1';
            }
            else{
                $Member->sellm = '0';
            }
            if($_POST['idxrec'] == 'y'){
                $Member->idxrec = 'y';
                $Member->idxrectime = time();
            }
            else{
                $Member->idxrec = 'n';
            }

            $Member->save();

        }
        include APP_PATH.'config.inc.php';
        $mem = new Memcache();
        $mem->connect($mem_host, $mem_port);
                //系统管理员
        if ($userinfo['showadmin'] != $_POST['showadmin']) {
                 $ret = json_decode($mem->get($sys_adminer_change),true);
                 $mem->delete($sys_adminer_change);
                 if (is_null($ret) || empty($ret)) {
                     $ret = array();
                 }
                 if ($_POST['showadmin']) {
                     $ret['add'][$userinfo['id']] = $userinfo['id'];
                     unset($ret['remove'][$userinfo['id']]);
                 } else {
                     $ret['remove'][$userinfo['id']] = $userinfo['id'];
                     unset($ret['add'][$userinfo['id']]);
                 }

                 if (!$mem->set($sys_adminer_change, json_encode($ret))) {
                     $this->error('设置管理员权限失败');
                 }
        }
        $roomStatuKey = $room_status_prefix . $userinfo['curroomnum'];;
        $statu = json_decode($mem->get($roomStatuKey),true);
        $method = 'replace';
        if (is_null($statu)) {
            $method = 'set';
            $statu = array();
            $statu['owner'] = $userinfo['id'];
            $roomAdmin = D("Roomadmin")->where('uid=' . $userinfo['id'])->select();
            foreach($roomAdmin as $item) {
                $statu['adminer'][$item['adminuid']] = $item['adminuid'];
            }
        }
        $statu['maxonline'] = $_POST['maxonline'];
        $mem->$method($roomStatuKey,json_encode($statu));

        //vip
        switch ($_POST['vip']) {
            case 0:
                $expire = $userinfo['vip'] == 0 ? 0 : 3*30*24*60*60;;
                break;
            case 1:
            case 2:
                $expire = $vipexpire;
                break;
            default:
                $expire = 0;
        }
        if ($expire) {
            $mem->set($user_vip_prefix.$userinfo['id'], json_encode(array('vip'=>$_POST['vip'],'expire'=>$expire)));
        }


        $zddk=$_POST['zddk'];
        $pz=$_POST['pz'];
        $fps=$_POST['fps'];
        $zjg=$_POST['zjg'];
        $height=$_POST['height'];
        $width=$_POST['width'];
        $sql="update ss_member set pz='{$pz}',fps='{$fps}',zjg='{$zjg}',zddk='{$zddk}',height='{$height}',width='{$width}' where id={$_POST['id']}";

        $Member->execute($sql);
        echo '<script>alert(\'修改成功_'.$error.'\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
    }


    public function pswencode($txt,$key='youst'){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+_)(*&^%$#@!~";
        $nh = rand(0,64);
        $ch = $chars[$nh];
        $mdKey = md5($key.$ch);
        $mdKey = substr($mdKey,$nh%8, $nh%8+7);
        $txt = base64_encode($txt);
        $tmp = '';
        $i=0;$j=0;$k = 0;
        for ($i=0; $i<strlen($txt); $i++) {
            $k = $k == strlen($mdKey) ? 0 : $k;
            $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
            $tmp .= $chars[$j];
        }
        return $ch.$tmp;
    }

    public function checkIt($number)
    {
        $modes = array(
            '######', 'AAAAAA', 'AAABBB', 'AABBCC', 'ABCABC', 'ABBABB', 'AABAA', 'AAABB', 'AABBB', '#####', 'AAAAA', '####', 'AAAA', 'AABB', 'ABBA', 'AAAB', 'ABAB', 'AAA', '###', 'AAAAAAAB', 'AAAAAABC', 'AAAAABCD', 'AAABBBCD', 'AAABBBC', 'AABBBCDE', 'AABBBCD', 'AABBBC', 'AAABBCDE', 'AAABBCD', 'AAABBC', 'AAAABCDE', 'AAAABCD', 'AAAABC', 'AAAAB', 'AABBCDEF', 'AABBCDE', 'AABBCD', 'AABBC', 'AAABCDEF', 'AAABCDE', 'AAABCD', 'AAABC', 'AAAB', 'AABBCCDE', 'AABBCCD'); //前后排序有优先级,只要有一个匹配,后面的就不再检索了
        $result = ' ';
        foreach ($modes as $mode) {
            $len = strlen($mode);
            $s = substr($number, -$len);
            $temp = array();
            $match = true;
            for ($i=0; $i<$len; $i++) {
                if ($mode[$i]=='#') {
                    if (!isset($temp['step'])) {
                        $temp['step'] = 0;
                        $temp['current'] = intval($s[$i]);
                    }
                    elseif ($temp['step'] == 0) {
                        $temp['step'] = $temp['current'] - intval($s[$i]);
                        if ($temp['step'] != -1 && $temp['step'] != 1) {
                            $match = false;
                            break;
                        }
                        else {
                            $temp['current'] = intval($s[$i]);
                        }
                    }
                    else {
                        $step = $temp['current'] - intval($s[$i]);
                        if ($step != $temp['step']) {
                            $match = false;
                            break;
                        }
                        else {
                            $temp['current'] = intval($s[$i]);
                        }
                    }
                }
                else {
                    if (isset($temp[$mode[$i]])) {
                        if ($s[$i] != $temp[$mode[$i]]) {
                            $match = false;
                            break;
                        }
                    }
                    else {
                        $temp[$mode[$i]] = $s[$i];
                    }
                }
            }
            if ($match) {
                $result = $mode;
                break;
            }
        }
        return $result;
    }

    public function del_user(){
        if($_GET["userid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Member");
            $userinfo = $dao->getById($_GET["userid"]);
            if($userinfo){
                $dao->query('update ss_member set isdelete="y" where id='.$_GET["userid"]);
                /*
                D("Attention")->where('uid='.$_GET["userid"].' or attuid='.$_GET["userid"])->delete();
                D("Bandingnote")->where('uid='.$_GET["userid"])->delete();
                D("Beandetail")->where('uid='.$_GET["userid"])->delete();
                D("Chargedetail")->where('uid='.$_GET["userid"])->delete();
                D("Coindetail")->where('uid='.$_GET["userid"].' or touid='.$_GET["userid"])->delete();
                D("Emceeagentbeandetail")->where('uid='.$_GET["userid"])->delete();
                D("Favor")->where('uid='.$_GET["userid"].' or favoruid='.$_GET["userid"])->delete();
                D("Giveaway")->where('uid='.$_GET["userid"].' or touid='.$_GET["userid"])->delete();
                D("Liverecord")->where('uid='.$_GET["userid"])->delete();
                D("Member")->where('id='.$_GET["userid"])->delete();
                D("Payagentbeandetail")->where('uid='.$_GET["userid"])->delete();
                D("Roomadmin")->where('uid='.$_GET["userid"].' or adminuid='.$_GET["userid"])->delete();
                D("Roomnum")->where('uid='.$_GET["userid"])->delete();
                D("Showlistsong")->where('uid='.$_GET["userid"].' or pickuid='.$_GET["userid"])->delete();
                D("Usersong")->where('uid='.$_GET["userid"])->delete();
                D("Wish")->where('uid='.$_GET["userid"])->delete();
                */

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该用户');
            }
        }
    }

    public function opt_user()
    {
        $dao = D("Member");
        switch ($_GET['action']){
            case 'disaudit':
                if($_GET['userid'] != ''){
                    $dao->query('update ss_member set isaudit="n" where id='.$_GET['userid']);
                }
                $this->assign('jumpUrl',base64_decode($_REQUEST['return']).'#'.time());
                $this->success('操作成功');
                break;
            case 'audit':
                if($_GET['userid'] != ''){
                    $dao->query('update ss_member set isaudit="y" where id='.$_GET['userid']);
                    M('banlist')->query('update ss_banlist set banstatus = "1" ,disbantime='.time().', disbanadmin="'.$_SESSION['adminname'].'" where banstatus = "0" and uid='.$_GET['userid']);
                }
                $this->assign('jumpUrl',base64_decode($_REQUEST['return']).'#'.time());
                $this->success('操作成功');
                break;
            case 'restore':
                if($_GET['userid'] != ''){
                    $dao->query('update ss_member set isdelete="n" where id='.$_GET['userid']);
                }
                $this->assign('jumpUrl',base64_decode($_REQUEST['return']).'#'.time());
                $this->success('操作成功');
                break;
            case 'restorebat':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $userinfo = $dao->getById($array[$i]);
                        if($userinfo){
                            $dao->query('update ss_member set isdelete="n" where id='.$array[$i]);
                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;
            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $userinfo = $dao->getById($array[$i]);
                        if($userinfo){
                            $dao->query('update ss_member set isdelete="y" where id='.$array[$i]);
                            /*
                            D("Attention")->where('uid='.$array[$i].' or attuid='.$array[$i])->delete();
                            D("Bandingnote")->where('uid='.$array[$i])->delete();
                            D("Beandetail")->where('uid='.$array[$i])->delete();
                            D("Chargedetail")->where('uid='.$array[$i])->delete();
                            D("Coindetail")->where('uid='.$array[$i].' or touid='.$array[$i])->delete();
                            D("Emceeagentbeandetail")->where('uid='.$array[$i])->delete();
                            D("Favor")->where('uid='.$array[$i].' or favoruid='.$array[$i])->delete();
                            D("Giveaway")->where('uid='.$array[$i].' or touid='.$array[$i])->delete();
                            D("Liverecord")->where('uid='.$array[$i])->delete();
                            D("Member")->where('id='.$array[$i])->delete();
                            D("Payagentbeandetail")->where('uid='.$array[$i])->delete();
                            D("Roomadmin")->where('uid='.$array[$i].' or adminuid='.$array[$i])->delete();
                            D("Roomnum")->where('uid='.$array[$i])->delete();
                            D("Showlistsong")->where('uid='.$array[$i].' or pickuid='.$array[$i])->delete();
                            D("Usersong")->where('uid='.$array[$i])->delete();
                            D("Wish")->where('uid='.$array[$i])->delete();
                            */
                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function listsignuser()
    {
        $condition = 'isdelete="n" and sign<>"n"';
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and regtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and regtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户ID或用户名或昵称'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\'';
            }
        }
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        $p->setConfig('header','条');
        $page = $p->show();

        $banlists = M('banlist')->where('banstatus = "0"')->select();
        $this->assign('banlists',$banlists);

        $this->assign('page',$page);
        $this->assign('members',$members);

        $this->display();
    }

    public function listonlineuser()
    {
        $condition = 'isdelete="n" and broadcasting="y"';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and regtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and regtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户ID或用户名或昵称'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\'';
            }
        }
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('members',$members);

        $this->display();
    }

    public function adduser(){
        $this->display();
    }

    public function do_add_user(){
        include APP_PATH.'config.inc.php';
        $User=D("Member");
        $User->create();
        $User->username = $_POST['username'];
        $User->nickname = $_POST['username'];
        $User->birthday = time();
        $User->password = md5($_POST['password']);
        $User->password2 = $this->pswencode($_POST['password']);
        $User->email = $_POST['email'];
        $User->isaudit = 'y';
        $User->regtime = time();
        $roomnum = 99999;
        do {
            $roomnum = rand(1000000000,1999999999);
        } while ($this->checkIt($roomnum)=='');
        $User->curroomnum = $roomnum;
        $defaultserver = D("Server")->where('isdefault="y"')->select();
        if($defaultserver){
            $User->host = $defaultserver[0]['server_ip'];
        }
        $userId = $User->add();

        D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values('.$userId.','.$roomnum.','.time().')');

        $this->assign('jumpUrl',__URL__.'/listuser/');
        $this->success('添加成功');
    }

    public function listdeluser()
    {
        $condition = 'isdelete="y"';
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and regtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and regtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户ID或用户名或昵称'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\' ';
            }
        }
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        $p->setConfig('header','条');
        $page = $p->show();

        $banlists = M('banlist')->where('banstatus = "0"')->select();
        $this->assign('banlists',$banlists);

        $this->assign('page',$page);
        $this->assign('members',$members);

        $this->display();
    }

    public function listusersort()
    {
        $usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
        foreach($usersorts as $n=> $val){
            $usersorts[$n]['voo']=D("Usersort")->where('parentid='.$val['id'])->order('orderno')->select();
        }
        $this->assign("usersorts",$usersorts);
        $this->display();
    }

    public function usersortlistorder()
    {
        $Edit_ID = $_POST['id'];
        $Edit_OrderID = $_POST['orderno'];

        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            D("Usersort")->execute('update ss_usersort set orderno='.$Edit_OrderID[$i].' where id='.$Edit_ID[$i]);
        }

        $this->assign('jumpUrl',__URL__."/listusersort/");
        $this->success('修改成功');
    }

    public function del_usersort()
    {
        D("Usersort")->where('id='.$_GET['sid'].' or parentid='.$_GET['sid'])->delete();
        if($_GET['type'] == 'sub'){
            D("Member")->execute('update ss_member set sid=0 where sid='.$_GET['sid']);
        }
        else{
            D("Member")->execute('update ss_member set sid=0 where sid in (select id from ss_usersort where parentid='.$_GET['sid'].')');
        }

        $this->assign('jumpUrl',__URL__."/listusersort/");
        $this->success('删除成功');
    }

    public function usersort()
    {
        $usersort_dao = D("Usersort");
        $usersorts = $usersort_dao->where("parentid=0")->order('orderno')->select();
        $where_us_id['id'] = $_GET['pid'];
        $this_us = $usersort_dao->where($where_us_id)->field('id,isapprove')->find();
        $this->assign("usersorts",$usersorts);
        $this->assign("us",$this_us);
        $this->display();
    }

    public function do_add_usersort()
    {
        if($_POST['sortname'] != ''){
            $Usersort = D('usersort');
            $data['parentid'] =  $_POST['parentid'];
            $data['sortname'] =  $_POST['sortname'];
            $data['isapprove'] =  $_POST['isapprove'];
            $where_id['id'] = $data['parentid'];
            $parent = $Usersort->where($where_id)->find();
            if($parent['isapprove'] == $data['isapprove'] || $data['parentid'] == 0){
                $sortID = $Usersort->data($data)->add();
            }else{
                $this->error('添加失败，与上级类别不一致！');
            }
        }

        if($sortID){
            $this->assign('jumpUrl',__URL__."/listusersort/");
            $this->success('添加成功');
        }
        else{
            $this->error('添加失败');
        }
    }

    public function edit_usersort()
    {

        if($_GET["sid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Usersort");
            $sortinfo = $dao->getById($_GET["sid"]);
            if($sortinfo){
                $usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
                $this->assign("usersorts",$usersorts);

                $this->assign('sortinfo',$sortinfo);
            }
            else{
                $this->error('找不到该类别');
            }
        }

        $this->display();
    }

    public function do_edit_usersort()
    {
        if($_POST["id"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $where_id['id'] = $_POST['id'];
            $dao = D('usersort');
            $usersort = $dao->where($where_id)->find();
            if($usersort){
                $usersort['parentid'] =  $_POST['parentid'];
                $usersort['sortname'] =  $_POST['sortname'];
                $usersort['isapprove'] =  $_POST['isapprove'];
                if($usersort['parentid'] != 0){
                    $where_parent_id['id'] = $usersort['parentid'];
                    $parent = $dao->where($where_parent_id)->find();
                    if($parent['isapprove'] == $usersort['isapprove']){
                        if( $dao->where($where_id)->save($usersort)){
                            $this->assign('jumpUrl',__URL__."/edit_usersort/sid/".$_POST["id"]);
                            $this->success('修改成功');
                        }else{
                           $this->error($dao->getError());
                        }
                    }else{
                        $this->error('修改失败，与上级类别不一致！');
                    }
                }

            }
            else{
                $this->error('找不到该类别');
            }
        }
    }

    public function listbobolevel(){
        $emceelevels = D("Emceelevel")->where("")->order('levelid asc')->select();
        $this->assign("emceelevels",$emceelevels);
        $this->display();
    }

    public function save_emceelevel()
    {
        $Edit_ID = $_POST['id'];
        $Edit_levelid = $_POST['levelid'];
        $Edit_levelname = $_POST['levelname'];
        $Edit_earnbean_low = $_POST['earnbean_low'];
        $Edit_earnbean_up = $_POST['earnbean_up'];
        $Edit_DelID = $_POST['ids'];

        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("Emceelevel")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            D("Emceelevel")->execute('update ss_emceelevel set levelid='.$Edit_levelid[$i].',levelname="'.$Edit_levelname[$i].'",earnbean_low='.$Edit_earnbean_low[$i].',earnbean_up='.$Edit_earnbean_up[$i].' where id='.$Edit_ID[$i]);
        }

        if($_POST['add_levelid'] != '' && $_POST['add_levelname'] != '' && $_POST['add_earnbean_low'] != '' && $_POST['add_earnbean_up'] != ''){
            $EmceeLevel = D('Emceelevel');
            $EmceeLevel->create();
            $EmceeLevel->levelid = $_POST['add_levelid'];
            $EmceeLevel->levelname = $_POST['add_levelname'];
            $EmceeLevel->earnbean_low = $_POST['add_earnbean_low'];
            $EmceeLevel->earnbean_up = $_POST['add_earnbean_up'];
            $EmceeLevel->addtime = time();
            $levelID = $EmceeLevel->add();
        }

        $this->assign('jumpUrl',__URL__."/listbobolevel/");
        $this->success('操作成功');
    }

    public function listrichlevel(){
        $richlevels = D("Richlevel")->where("")->order('levelid asc')->select();
        $this->assign("richlevels",$richlevels);
        $this->display();
    }

    public function save_richlevel()
    {
        $Edit_ID = $_POST['id'];
        $Edit_levelid = $_POST['levelid'];
        $Edit_levelname = $_POST['levelname'];
        $Edit_spendcoin_low = $_POST['spendcoin_low'];
        $Edit_spendcoin_up = $_POST['spendcoin_up'];
        $Edit_DelID = $_POST['ids'];

        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("Richlevel")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            D("Richlevel")->execute('update ss_richlevel set levelid='.$Edit_levelid[$i].',levelname="'.$Edit_levelname[$i].'",spendcoin_low='.$Edit_spendcoin_low[$i].',spendcoin_up='.$Edit_spendcoin_up[$i].' where id='.$Edit_ID[$i]);
        }

        if($_POST['add_levelid'] != '' && $_POST['add_levelname'] != '' && $_POST['spendcoin_low'] != '' && $_POST['spendcoin_up'] != ''){
            $RichLevel = D('Richlevel');
            $RichLevel->create();
            $RichLevel->levelid = $_POST['add_levelid'];
            $RichLevel->levelname = $_POST['add_levelname'];
            $RichLevel->earnbean_low = $_POST['add_spendcoin_low'];
            $RichLevel->earnbean_up = $_POST['add_spendcoin_up'];
            $RichLevel->addtime = time();
            $levelID = $RichLevel->add();
        }

        $this->assign('jumpUrl',__URL__."/listrichlevel/");
        $this->success('操作成功');
    }

    public function listgiftsort(){
        $giftsorts = D("Giftsort")->where("")->order('orderno asc')->select();
        $this->assign("giftsorts",$giftsorts);
        $this->display();
    }

    public function save_giftsort()
    {
        $Edit_ID = $_POST['id'];
        $Edit_orderno = $_POST['orderno'];
        $Edit_sortname = $_POST['sortname'];
        $Edit_DelID = $_POST['ids'];
        $Edit_ratio = $_POST['ratio'];

        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("Giftsort")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            D("Giftsort")->execute('update ss_giftsort set orderno='.$Edit_orderno[$i].',sortname="'.$Edit_sortname[$i].'",ratio='.$Edit_ratio[$i].' where id='.$Edit_ID[$i]);
            D("Gift")->execute('update ss_gift set sid=0 where sid='.$Edit_ID[$i]);
        }

        if($_POST['add_orderno'] != '' && $_POST['add_sortname'] != ''){
            $Giftsort = D('Giftsort');
            $Giftsort->create();
            $Giftsort->orderno = $_POST['add_orderno'];
            $Giftsort->sortname = $_POST['add_sortname'];
            $Giftsort->addtime = time();
            $sortID = $Giftsort->add();
        }

        $this->assign('jumpUrl',__URL__."/listgiftsort/");
        $this->success('操作成功');
    }

    public function listgift(){
        $giftsorts = D("Giftsort")->where("")->order('orderno asc')->select();
        $this->assign("giftsorts",$giftsorts);

        $gifts = D("Gift")->where("")->order('sid asc,needcoin asc')->select();
        $this->assign("gifts",$gifts);

        $this->display();
    }

    public function save_gift()
    {
        //上传图片
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = 1048576 ;
        //设置上传文件类型
        $upload->allowExts  = explode(',','gif,jpg,png,swf');
        //设置上传目录
        //每个用户一个文件夹
        $prefix = 'gift';
        $uploadPath =  '../style/images/'.$prefix.'/';
        if(!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $upload->savePath =  $uploadPath;
        $upload->saveRule = uniqid;
        //执行上传操作
        if(!$upload->upload()) {
            // 捕获上传异常
            if($upload->getErrorMsg() != '没有选择上传文件'){
                $this->error($upload->getErrorMsg());
            }
        }
        else{
            $uploadList = $upload->getUploadFileInfo();
            foreach($uploadList as $picval){
                if($picval['key'] == 0){
                    $giftIcon_25 = '/style/images/'.$prefix.'/'.$picval['savename'];
                }
                if($picval['key'] == 1){
                    $giftIcon = '/style/images/'.$prefix.'/'.$picval['savename'];
                }
                if($picval['key'] == 2){
                    $giftSwf = '/style/images/'.$prefix.'/'.$picval['savename'];
                }
            }
        }
        $Edit_isRed = $_POST['isred'];
        $Edit_redNum = $_POST['rednum'];
        $Edit_ID = $_POST['id'];
        $Edit_sid = $_POST['sid'];
        $Edit_giftname = $_POST['giftname'];
        $Edit_needcoin = $_POST['needcoin'];
        $Edit_giftIcon_25 = $_POST['giftIcon_25'];
        $Edit_giftIcon = $_POST['giftIcon'];
        $Edit_giftSwf = $_POST['giftSwf'];
        $Edit_DelID = $_POST['ids'];
        $Edit_Enable = $_POST['enable'];
        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("Gift")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            //判断是不是红包
            if($Edit_isRed[$i] == "1"){
                if($Edit_needcoin[$i] < $Edit_redNum[$i]){
                    $this->error('红包个数至少应大于金额');
                }
                D("Gift")->execute('update ss_gift set sid='.$Edit_sid[$i].',giftname="'.$Edit_giftname[$i].'",needcoin='.$Edit_needcoin[$i].',giftIcon_25="'.$Edit_giftIcon_25[$i].'",giftIcon="'.$Edit_giftIcon[$i].'",isred = "'.$Edit_isRed[$i].'",rednum = '.$Edit_redNum[$i].',giftSwf="'.$Edit_giftSwf[$i].'", enable="'.$Edit_Enable[$i].'" where id='.$Edit_ID[$i]);
            }else{
                D("Gift")->execute('update ss_gift set sid='.$Edit_sid[$i].',giftname="'.$Edit_giftname[$i].'",needcoin='.$Edit_needcoin[$i].',giftIcon_25="'.$Edit_giftIcon_25[$i].'",giftIcon="'.$Edit_giftIcon[$i].'",isred = "'.$Edit_isRed[$i].'",rednum = 0,giftSwf="'.$Edit_giftSwf[$i].'", enable="'.$Edit_Enable[$i].'" where id='.$Edit_ID[$i]);
            }
        }

        if($_POST['add_giftname'] != '' && $_POST['add_needcoin'] != '' && $giftIcon_25 != '' && $giftIcon != ''){
            // $Gift = M('Gift');
            // $Gift->create();
            $data = array();
            if($_POST['add_isred'] == "1"){
                $data['isred'] = $_POST['add_isred'];
                $data['rednum'] = $_POST['add_rednum'];
            }else{
                $data['isred'] = $_POST['add_isred'];
            }

            $data['sid'] = $_POST['add_sid'];
            $data['giftname'] = $_POST['add_giftname'];
            $data['needcoin'] = $_POST['add_needcoin'];
            $data['enable'] = $_POST['add_enable'];
            $data['gifticon_25'] = $giftIcon_25;
            $data['gifticon'] = $giftIcon;
            if($giftSwf != ''){
                $data['giftswf'] = $giftSwf;
            }
            $data['addtime'] = time();
            $giftID = M('gift')->add($data);
        }

        $this->assign('jumpUrl',__URL__."/listgift/");
        $this->success('操作成功');
    }

    public function listgoodno()
    {
        $condition = 'id>0';
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入靓号号码'){
            $condition .= ' and num like \'%'.$_GET['keyword'].'%\'';
        }
        if($_GET['length'] != ''){
            $condition .= ' and length='.$_GET['length'];
        }
        if($_GET['issale'] != ''){
            $condition .= ' and issale="'.$_GET['issale'].'"';
        }
        if($_GET['owneruid'] != '' && $_GET['owneruid'] != '请输入用户UID'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and owneruid='.$_GET['owneruid'];
            }
        }

        $orderby = 'id desc';
        $goodnum = D("Goodnum");
        $count = $goodnum->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $goodnums = $goodnum->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('goodnums',$goodnums);

        $this->display();
    }

    public function editgoodno(){
        if($_GET['numid'] == ''){
            echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
        else{
            $numinfo = D("Goodnum")->getById($_GET["numid"]);
            if($numinfo){
                if($numinfo['issale'] == 'y'){
                    echo '<script>alert(\'该靓号已销售不可修改\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
                }
                $this->assign('numinfo',$numinfo);
            }
            else{
                echo '<script>alert(\'找不到该靓号\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            }
        }

        $this->display();
    }

    public function do_edit_goodnum(){
        header("Content-type: text/html; charset=utf-8");
        if($_POST["id"] == '')
        {
            echo '<script>alert(\'缺少参数或参数不正确\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            exit;
        }
        else{
            $numinfo = D("Goodnum")->getById($_POST["id"]);
            if(!$numinfo){
                echo '<script>alert(\'该靓号不存在\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
                exit;
            }
        }

        $Goodnum=D("Goodnum");
        $vo = $Goodnum->create();
        if(!$vo) {
            $this->error($Goodnum->getError());
        }else{

            $Goodnum->save();
        }

        echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';

    }

    public function givegoodno(){
        if($_GET['numid'] == ''){
            echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
        else{
            $numinfo = D("Goodnum")->getById($_GET["numid"]);
            if($numinfo){
                if($numinfo['issale'] == 'y'){
                    echo '<script>alert(\'该靓号已销售不可赠送\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
                }
                $this->assign('numinfo',$numinfo);
            }
            else{
                echo '<script>alert(\'找不到该靓号\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            }
        }

        $this->display();
    }

    public function do_give_goodnum(){
        header("Content-type: text/html; charset=utf-8");
        if($_POST["id"] == '')
        {
            echo '<script>alert(\'缺少参数或参数不正确\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            exit;
        }
        else{
            $numinfo = D("Goodnum")->getById($_POST["id"]);
            if(!$numinfo){
                echo '<script>alert(\'该靓号不存在\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
                exit;
            }
        }

        if($_POST['givetouid'] == ''){
            echo '<script>alert(\'赠送对象UID不能为空\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            exit;
        }
        else{
            $emceeinfo = D("Member")->getById($_POST['givetouid']);
            if($emceeinfo){
                D("Roomnum")->execute('delete from ss_roomnum where num="'.$numinfo['num'].'"');
                D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime,expiretime,original) values('.$_POST['givetouid'].','.$numinfo['num'].','.time().',0,"n")');
                D("Goodnum")->execute('update ss_goodnum set issale="y",owneruid='.$_POST['givetouid'].',remark="管理员赠送" where id='.$_POST["id"]);
                D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime) values(0,'.$_POST['givetouid'].',"('.$numinfo['num'].')","系统赠送","/style/images/gnum.png",'.time().')');
            }
            else{
                echo '<script>alert(\'找不到该赠送对象\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
                exit;
            }
        }

        echo '<script>alert(\'赠送成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';

    }

    public function del_goodnum(){
        if($_GET["numid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Goodnum");
            $numinfo = $dao->getById($_GET["numid"]);
            if($numinfo){
                if($numinfo['issale'] == 'y'){
                    $this->error('该靓号已销售不可删除');
                }
                $dao->where('id='.$_GET["numid"])->delete();
                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该靓号');
            }
        }
    }

    public function opt_goodnum()
    {
        $dao = D("Goodnum");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $numinfo = $dao->getById($array[$i]);
                        if($numinfo){
                            if($numinfo['issale'] == 'n'){
                                $dao->where('id='.$array[$i])->delete();
                            }
                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function recycle_goodnum(){
        if($_GET["numid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Goodnum");
            $numinfo = $dao->getById($_GET["numid"]);
            if($numinfo){
                $emceeoldnum = D("Roomnum")->where('uid='.$numinfo['owneruid'].' and original="y"')->select();
                D("Roomnum")->execute('delete from ss_roomnum where num="'.$numinfo['num'].'"');
                $dao->execute('update ss_goodnum set issale="n",owneruid=0,remark="" where id='.$_GET["numid"]);
                D("Member")->execute('update ss_member set curroomnum='.$emceeoldnum[0]['num'].' where id='.$numinfo['owneruid']);

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功收回');
            }
            else{
                $this->error('找不到该靓号');
            }
        }
    }

    public function addgoodno(){
        $this->display();
    }

    public function do_add_goodnum(){
        if($_POST['num'] == ''){
            $this->error('靓号不能为空');
        }

        if($_POST['price'] == ''){
            $this->error('价格不能为空');
        }

        $numinfo = D("Goodnum")->where('num='.$_POST['num'])->select();
        if($numinfo){
            $this->error('该靓号已存在');
        }

        $goodnum = D('Goodnum');
        $vo = $goodnum->create();
        if(!$vo) {
            $this->error($goodnum->getError());
        }else{
            $goodnum->length = strlen($_POST['num']);
            $goodnum->add();

            $this->assign('jumpUrl',__URL__.'/listgoodno/');
            $this->success('添加成功');
        }
    }

    public function addgoodno1(){
        $this->display();
    }

    public function do_add_goodnum_bat(){
        set_time_limit(0);

        header('Content-Type: text/html;charset=utf-8');
        //ignore_user_abort(true);
        ob_end_flush();
        echo '<style>body { font:normal 12px/20px Arial, Verdana, Lucida, Helvetica, simsun, sans-serif; color:#313131; }</style>';
        echo str_pad("",1000);
        echo '准备开始添加...<br>';
        flush();

        for($i=(int)$_POST['startnum'];$i<=(int)$_POST['endnum'];$i++)
        {
            echo '正在添加靓号'.$i.' ';
            $numinfo = D("Goodnum")->where('num='.$i)->select();
            if($numinfo){
                echo '已存在';
            }
            else{
                D("Goodnum")->execute('insert into ss_goodnum(num,length,price,addtime) values('.$i.','.strlen($i).','.$_POST['price'].','.time().')');
                echo '添加成功';
            }
            echo '<br>';
        }
        echo '批量添加完毕';
    }

    public function listeggset()
    {
        $eggsetinfo = D("Eggset")->find(1);
        if($eggsetinfo){
            $this->assign('eggsetinfo',$eggsetinfo);
        }
        else{
            $this->assign('jumpUrl',__URL__.'/mainFrame');
            $this->error('系统参数读取错误');
        }
        $this->display();
    }

    public function save_eggset()
    {
        $eggset = D('Eggset');
        $vo = $eggset->create();
        if(!$vo) {
            $this->assign('jumpUrl',__URL__.'/listeggset/');
            $this->error('修改失败');
        }else{
            $eggset->save();

            $this->assign('jumpUrl',__URL__.'/listeggset/');
            $this->success('修改成功');
        }
    }

    public function listeggwinrecord(){
        $condition = 'remark="砸蛋奖励"';
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != ''){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if($keyuinfo){
                $condition .= ' and touid='.$keyuinfo[0]['id'];
            }
            else{
                $this->error('没有该用户的记录');
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and touid='.$_GET['keyword'];
            //}
        }

        $orderby = 'id desc';
        $giveaway = D("Giveaway");
        $count = $giveaway->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $giveaways = $giveaway->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($giveaways as $n=> $val){
            $giveaways[$n]['voo']=D("Member")->where('id='.$val['touid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('giveaways',$giveaways);

        $this->display();
    }



    //财务
    public function listpay()
    {
        $siteconfig = D("Siteconfig")->find(1);
        if($siteconfig){
            $this->assign('siteconfig',$siteconfig);
        }
        else{
            $this->assign('jumpUrl',__URL__.'/mainFrame');
            $this->error('系统参数读取错误');
        }
        $this->display();
    }

    public function save_onlinepay()
    {
        $siteconfig = D('Siteconfig');
        $vo = $siteconfig->create();
        if(!$vo) {
            $this->assign('jumpUrl',__URL__.'/listpay/');
            $this->error('修改失败');
        }else{
            $siteconfig->save();

            $this->assign('jumpUrl',__URL__.'/listpay/');
            $this->success('修改成功');
        }
    }

    public function listchargerecord(){
        $condition = 'id>0';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名或交易号'){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                if($keyuinfo){
                    $condition .= ' and (uid='.$keyuinfo[0]['id'].' or orderno="'.$_GET['keyword'].'")';
                }
                else{
                    $condition .= ' and orderno="'.$_GET['keyword'].'"';
                }
            }
            else{
                if($keyuinfo){
                    $condition .= ' and uid='.$keyuinfo[0]['id'];
                }
                else{
                    $this->error('没有该用户的记录');
                }
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and (uid='.$_GET['keyword'].' or orderno="'.$_GET['keyword'].'")';
            //}
        }
        if($_GET['status'] != ''){
            $condition .= ' and status="'.$_GET['status'].'"';
        }
        $orderby = 'id desc';
        $chargedetail = D("Chargedetail");
        $count = $chargedetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $charges = $chargedetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($charges as $n=> $val){
            $charges[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
            $charges[$n]['voo2']=D("Member")->where('id='.$val['touid'])->select();
            if($val['touid'] != 0){
                $charges[$n]['voo3']=D("Member")->where('id='.$val['proxyuid'])->select();
            }
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('charges',$charges);

        $charges_all = $chargedetail->where($condition)->order($orderby)->select();
        $this->assign('charges_all',$charges_all);

        $this->display();
    }

    public function del_chargerecord(){
        if($_GET["chargeid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Chargedetail");
            $chargeinfo = $dao->getById($_GET["chargeid"]);
            if($chargeinfo){
                $dao->where('id='.$_GET["chargeid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该交易记录');
            }
        }
    }

    public function opt_chargerecord()
    {
        $dao = D("Chargedetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $chargeinfo = $dao->getById($array[$i]);
                        if($chargeinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function addcointouser(){
        $this->display();
    }

    public function do_addcointouser(){
        $coin = $_POST['addcoin'];
        if (!is_integer(1*$coin)) {
            $this->error('充值金额不正确!');
        }
        if($_POST['uid'] != ''){
            $userinfo = D("Member")->where('id="'.$_POST['uid'].'"')->select();
            if($userinfo){
                $current_coin = $userinfo[0]['coinbalance'];

                if($_POST['math'] == 'plus'){
                    // 2**32 -1
                    if ($current + $coin > 4294967295) {
                    $this->error('无法完成充值，因为充值金额过大!');
                    }
                    D("Member")->execute('update ss_member set coinbalance=coinbalance+'.$coin.' where id='.$userinfo[0]['id']);

                    D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime,operator,operatorip) values(0,'.$userinfo[0]['id'].',"'.$coin.'","系统赠送","/style/images/coin.png",'.time().',"'.$_SESSION['adminname'].'","'.get_client_ip().'")');
                }
                if($_POST['math'] == 'subtract'){
                    if ($current - $coin <= 0) {
                        // 小于0继续扣钱？
                        // $coin = $current;
                    }
                    D("Member")->execute('update ss_member set coinbalance=coinbalance-'.$coin.' where id='.$userinfo[0]['id']);

                    D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime,operator,operatorip) values(0,'.$userinfo[0]['id'].',"-'.$coin.'","系统抵扣","/style/images/coin.png",'.time().',"'.$_SESSION['adminname'].'","'.get_client_ip().'")');
                }
                $this->assign('jumpUrl',__URL__.'/addcointouser/');
                $this->success('操作成功');
            }
            else{
                $this->error('未找到该用户');
            }
        }
        else{
            $this->error('请填写相关选项');
        }
    }

    public function listcoin(){
        $condition = 'id>0 ';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime >= '.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime <= '.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户ID'){
            $condition .= ' and uid = '.$_GET['keyword'];
        }
        if($_GET['keyword2'] != ''  && $_GET['keyword2'] != '请输入用户ID'){
            $condition .= ' and touid = '.$_GET['keyword2'];
        }
        $orderby = 'id desc';
        $coindetail = D("Coindetail");
        $count = $coindetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $details = $coindetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($details as $n=> $val){
            $details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
            if($val['touid'] != 0){
                $details[$n]['voo2']=D("Member")->where('id='.$val['touid'])->select();
            }
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('details',$details);

        $this->display();
    }

    public function del_coindetail(){
        if($_GET["detailid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Coindetail");
            $detailinfo = $dao->getById($_GET["detailid"]);
            if($detailinfo){
                $dao->where('id='.$_GET["detailid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该消费记录');
            }
        }
    }

    public function opt_coindetail()
    {
        $dao = D("Coindetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $detailinfo = $dao->getById($array[$i]);
                        if($detailinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

        public function listaddcoinrecord(){
        $condition = 'uid = 0 and remark="系统赠送"';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'and addtime >= '.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]) + 86400;
            $condition .= ' and addtime <= '.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户ID'){
            $condition .= ' and touid='.$_GET['keyword'];
        }

        $orderby = 'id desc';
        $giveaway = D("Giveaway");
        $count = $giveaway->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $giveaways = $giveaway->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($giveaways as $n=> $val){
            $giveaways[$n]['voo']=D("Member")->where('id='.$val['touid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('giveaways',$giveaways);

        $this->display();
    }

    public function listbean(){
        $condition = 'id>0';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if($keyuinfo){
                $condition .= ' and uid='.$keyuinfo[0]['id'];
            }
            else{
                $this->error('没有该用户的记录');
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and uid='.$_GET['keyword'];
            //}
        }
        $orderby = 'id desc';
        $beandetail = D("Beandetail");
        $count = $beandetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $details = $beandetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($details as $n=> $val){
            $details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('details',$details);

        $this->display();
    }

    public function del_beandetail(){
        if($_GET["detailid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Beandetail");
            $detailinfo = $dao->getById($_GET["detailid"]);
            if($detailinfo){
                $dao->where('id='.$_GET["detailid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该记录');
            }
        }
    }

    public function opt_beandetail()
    {
        $dao = D("Beandetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $detailinfo = $dao->getById($array[$i]);
                        if($detailinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }
  //原主播收入统计函数 fix by zdd
    public function countboboincome(){
        $where = '( sign= "y" or sign = "n" or sign = "c")';
        if(!empty($_GET['keyword']) && $_GET['keyword']!='请输入用户ID'){
            $where .= ' and id = '.$_GET['keyword'];
        }
        $count = D("Member")->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $emcces = D("Member")->limit($p->firstRow.",".$p->listRows)->where($where)->select();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('emccesList',$emcces);
        $this->display();
    }


    public function listbobopay(){
        $condition = 'type="expend" and action="settlement"';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if($keyuinfo){
                $condition .= ' and uid='.$keyuinfo[0]['id'];
            }
            else{
                $this->error('没有该用户的记录');
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and uid='.$_GET['keyword'];
            //}
        }
        $orderby = 'id desc';
        $beandetail = D("Beandetail");
        $count = $beandetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $details = $beandetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($details as $n=> $val){
            $details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('details',$details);

        $this->display();
    }

    public function del_emccepayrecord(){
        if($_GET["recordid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Beandetail");
            $detailinfo = $dao->getById($_GET["recordid"]);
            if($detailinfo){
                $dao->where('id='.$_GET["recordid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该记录');
            }
        }
    }

    public function opt_emccepayrecord()
    {
        $dao = D("Beandetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $detailinfo = $dao->getById($array[$i]);
                        if($detailinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function editbobopay(){
        header("Content-type: text/html; charset=utf-8");
        if($_GET['recordid'] == ''){
            echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
        else{
            $recordinfo = D("Beandetail")->find($_GET["recordid"]);
            if($recordinfo){
                $this->assign('recordinfo',$recordinfo);
                $userinfo = D("Member")->find($recordinfo["uid"]);
                $this->assign('userinfo',$userinfo);
            }
            else{
                echo '<script>alert(\'找不到该记录\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            }
        }

        $this->display();
    }

    public function do_editbobopay(){
        header("Content-type: text/html; charset=utf-8");
        $beandetail = D('Beandetail');
        $vo = $beandetail->create();
        if(!$vo) {
            echo '<script>alert(\''.$admin->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
        }else{
            $beandetail->save();

            echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
    }

    public function listboboagentbean(){
        $condition = 'id>0';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if($keyuinfo){
                $condition .= ' and uid='.$keyuinfo[0]['id'];
            }
            else{
                $this->error('没有该用户的记录');
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and uid='.$_GET['keyword'];
            //}
        }
        $orderby = 'id desc';
        $beandetail = D("Emceeagentbeandetail");
        $count = $beandetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $details = $beandetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($details as $n=> $val){
            $details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('details',$details);

        $this->display();
    }

    public function del_emceeagentbeandetail(){
        if($_GET["detailid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Emceeagentbeandetail");
            $detailinfo = $dao->getById($_GET["detailid"]);
            if($detailinfo){
                $dao->where('id='.$_GET["detailid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该记录');
            }
        }
    }

    public function opt_emceeagentbeandetail()
    {
        $dao = D("Emceeagentbeandetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $detailinfo = $dao->getById($array[$i]);
                        if($detailinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function countboboagentincome(){
        set_time_limit(0);

        header('Content-Type: text/html;charset=utf-8');
        //ignore_user_abort(true);
        ob_end_flush();
        echo '<style>body { font:normal 12px/20px Arial, Verdana, Lucida, Helvetica, simsun, sans-serif; color:#313131; }</style>';
        echo str_pad("",1000);
        echo '准备开始统计...<br>';
        flush();

        $emcces = D("Member")->where('emceeagent="y"')->order('regtime desc')->select();
        echo '共有'.count($emcces).'个主播代理<br>';
        foreach($emcces as $n=> $val){
            if ( connection_aborted() )
            {
                exit;
            }
            echo '正在统计主播代理 '.$val['nickname'].'<br>';
            //if($val['freezestatus'] == '1'){
                //if(($val['beanbalance'] - $val['freezeincome']) > 0){
                    //D("Member")->execute('update ss_member set freezeincome=0,freezestatus="0" where id='.$val['id']);
                //}
            //}
            //if(($val['beanbalance'] - $val['freezeincome']) > 0){
                //$costbean = $val['beanbalance'] - $val['freezeincome'];
            if($val['beanbalance2'] > 0){
                $costbean = $val['beanbalance2'];

                D("Member")->execute('update ss_member set beanbalance2=beanbalance2-'.$costbean.' where id='.$val['id']);

                $Beandetail = D("Emceeagentbeandetail");
                $Beandetail->create();
                $Beandetail->type = 'expend';
                $Beandetail->action = 'settlement';
                $Beandetail->uid = $val['id'];
                $Beandetail->content = '系统结算';
                $Beandetail->bean = $costbean;
                $Beandetail->addtime = time();
                $detailId = $Beandetail->add();
            }
        }
        echo '<a href="'.__URL__.'/listboboagentpay/">返回</a>';
    }

    public function listboboagentpay(){
        $condition = 'type="expend" and action="settlement"';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if($keyuinfo){
                $condition .= ' and uid='.$keyuinfo[0]['id'];
            }
            else{
                $this->error('没有该用户的记录');
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and uid='.$_GET['keyword'];
            //}
        }
        $orderby = 'id desc';
        $beandetail = D("Emceeagentbeandetail");
        $count = $beandetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $details = $beandetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($details as $n=> $val){
            $details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('details',$details);

        $this->display();
    }

    public function del_emcceagentpayrecord(){
        if($_GET["recordid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Emceeagentbeandetail");
            $detailinfo = $dao->getById($_GET["recordid"]);
            if($detailinfo){
                $dao->where('id='.$_GET["recordid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该记录');
            }
        }
    }

    public function opt_emcceagentpayrecord()
    {
        $dao = D("Emceeagentbeandetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $detailinfo = $dao->getById($array[$i]);
                        if($detailinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function editboboagentpay(){
        header("Content-type: text/html; charset=utf-8");
        if($_GET['recordid'] == ''){
            echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
        else{
            $recordinfo = D("Emceeagentbeandetail")->find($_GET["recordid"]);
            if($recordinfo){
                $this->assign('recordinfo',$recordinfo);
                $userinfo = D("Member")->find($recordinfo["uid"]);
                $this->assign('userinfo',$userinfo);
            }
            else{
                echo '<script>alert(\'找不到该记录\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            }
        }

        $this->display();
    }

    public function do_editboboagentpay(){
        header("Content-type: text/html; charset=utf-8");
        $beandetail = D('Emceeagentbeandetail');
        $vo = $beandetail->create();
        if(!$vo) {
            echo '<script>alert(\''.$admin->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
        }else{
            $beandetail->save();

            echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
    }

    public function listpayagentbean(){
        $condition = 'id>0';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if($keyuinfo){
                $condition .= ' and uid='.$keyuinfo[0]['id'];
            }
            else{
                $this->error('没有该用户的记录');
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and uid='.$_GET['keyword'];
            //}
        }
        $orderby = 'id desc';
        $beandetail = D("Payagentbeandetail");
        $count = $beandetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $details = $beandetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($details as $n=> $val){
            $details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('details',$details);

        $this->display();
    }

    public function del_payagentbeandetail(){
        if($_GET["detailid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Payagentbeandetail");
            $detailinfo = $dao->getById($_GET["detailid"]);
            if($detailinfo){
                $dao->where('id='.$_GET["detailid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该记录');
            }
        }
    }

    public function opt_payagentbeandetail()
    {
        $dao = D("Payagentbeandetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $detailinfo = $dao->getById($array[$i]);
                        if($detailinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }
    //统计充值代理收入
    public function countpayagentincome(){

        set_time_limit(0);
        header('Content-Type: text/html;charset=utf-8');
        //ignore_user_abort(true);
        ob_end_flush();
        echo '<style>body { font:normal 12px/20px Arial, Verdana, Lucida, Helvetica, simsun, sans-serif; color:#313131; }</style>';
        echo str_pad("",1000);
        echo '准备开始统计...<br>';
        flush();
        $emcces = D("Member")->where('payagent="y"')->order('regtime desc')->select();
        echo '共有'.count($emcces).'个充值代理<br>';
        foreach($emcces as $n=> $val){
            if ( connection_aborted() )
            {
                exit;
            }
            echo '正在统计充值代理 '.$val['nickname'].'<br>';
            //if($val['freezestatus'] == '1'){
                //if(($val['beanbalance'] - $val['freezeincome']) > 0){
                    //D("Member")->execute('update ss_member set freezeincome=0,freezestatus="0" where id='.$val['id']);
                //}
            //}
            //if(($val['beanbalance'] - $val['freezeincome']) > 0){
                //$costbean = $val['beanbalance'] - $val['freezeincome'];
            if($val['beanbalance3'] > 0){
                $costbean = $val['beanbalance3'];
                D("Member")->execute('update ss_member set beanbalance3=beanbalance3-'.$costbean.' where id='.$val['id']);
                $Beandetail = D("Payagentbeandetail");
                $Beandetail->create();
                $Beandetail->type = 'expend';
                $Beandetail->action = 'settlement';
                $Beandetail->uid = $val['id'];
                $Beandetail->content = '系统结算';
                $Beandetail->bean = $costbean;
                $Beandetail->addtime = time();
                $detailId = $Beandetail->add();
            }
        }
        echo '<a href="'.__URL__.'/listpayagentpay/">返回</a>';
    }
    public function admin_payagentincome(){
        $sql="select uid,sum(bean) as bean from ss_payagentbeandetail group by uid";
        $data=M()->query($sql);
        foreach($data as $k=>$v){
            $userinfo=M('Member')->field('username')->where('id='.$v['uid'].' and payagent="y"')->find();
            $data[$k]['username']=$userinfo['username'];
        }
        $this->assign('data',$data);
        $this->display();

    }

    public function listpayagentpay(){
        $condition = 'type="expend" and action="settlement"';
        $conf = M('Siteconfig')->find(1);
        $domainroot = $conf['domainroot'];
        if(!strstr($domainroot,"http://")){
            $domainroot = "http://".$domainroot;
        }
        $this -> assign("domainroot",$domainroot);
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= 'addtime>='.$unixtime;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
            $keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
            if($keyuinfo){
                $condition .= ' and uid='.$keyuinfo[0]['id'];
            }
            else{
                $this->error('没有该用户的记录');
            }

            //if(preg_match("/^\d*$/",$_GET['keyword'])){
                //$condition .= ' and uid='.$_GET['keyword'];
            //}
        }
        $orderby = 'id desc';
        $beandetail = D("Payagentbeandetail");
        $count = $beandetail->where($condition)->count();
        $listRows = 100;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $details = $beandetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        foreach($details as $n=> $val){
            $details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
        }
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('details',$details);

        $this->display();
    }

    public function del_payagentpayrecord(){
        if($_GET["recordid"] == '')
        {
            $this->error('缺少参数或参数不正确');
        }
        else{
            $dao = D("Payagentbeandetail");
            $detailinfo = $dao->getById($_GET["recordid"]);
            if($detailinfo){
                $dao->where('id='.$_GET["recordid"])->delete();

                $this->assign('jumpUrl',base64_decode($_GET['return']));
                $this->success('成功删除');
            }
            else{
                $this->error('找不到该记录');
            }
        }
    }

    public function opt_payagentpayrecord()
    {
        $dao = D("Payagentbeandetail");
        switch ($_GET['action']){

            case 'del':
                if(is_array($_REQUEST['ids'])){
                    $array = $_REQUEST['ids'];
                    $num = count($array);
                    for($i=0;$i<$num;$i++)
                    {
                        $detailinfo = $dao->getById($array[$i]);
                        if($detailinfo){
                            $dao->where('id='.$array[$i])->delete();

                        }
                    }
                }
                $this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
                $this->success('操作成功');
                break;

        }
    }

    public function editpayagentpay(){
        header("Content-type: text/html; charset=utf-8");
        if($_GET['recordid'] == ''){
            echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
        else{
            $recordinfo = D("Payagentbeandetail")->find($_GET["recordid"]);
            if($recordinfo){
                $this->assign('recordinfo',$recordinfo);
                $userinfo = D("Member")->find($recordinfo["uid"]);
                $this->assign('userinfo',$userinfo);
            }
            else{
                echo '<script>alert(\'找不到该记录\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
            }
        }

        $this->display();
    }

    public function do_edit_payagentpayrecord(){
        header("Content-type: text/html; charset=utf-8");
        $beandetail = D('Payagentbeandetail');
        $vo = $beandetail->create();
        if(!$vo) {
            echo '<script>alert(\''.$admin->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
        }else{
            $beandetail->save();

            echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
        }
    }

    //界面
    public function listtemplate()
    {
        $this->display();
    }
/*
    public function tpl_updatefilename() {
        $filepath = '../'.$_POST['style'].'/config.php';
        if (file_exists($filepath)) {
            $style_info = include $filepath;
        }

        $file_explan = isset($_POST['file_explan']) ? $_POST['file_explan'] : '';
        if (!isset($style_info['file_explan'])) $style_info['file_explan'] = array();
        $style_info['file_explan'] = array_merge($style_info['file_explan'], $file_explan);
        @file_put_contents($filepath, '<?php return '.var_export($style_info, true).';?>');
        $this->success('修改成功');
    }

    public function admin_dirlist()
    {
        $this->display();
    }

    public function admin_dirlist2()
    {
        $this->display();
    }

    public function edit_file(){
        $basedir = realpath("../");
        $fp=fopen($basedir.base64_decode($_GET['file']),"r");
        $contents=fread($fp,filesize($basedir.base64_decode($_GET['file'])));
        $contents = str_replace('</textarea>', '&lt;/textarea>', $contents);
        $this->assign('contents',$contents);
        $this->display();
    }

    public function do_edit_file(){
        $basedir = realpath("../");
        $fp=fopen($basedir.$_POST['file'],"wb");
        $contents = str_replace('&lt;/textarea>', '</textarea>', $_POST['str']);
        fputs($fp,stripslashes($contents));
        fclose($fp);
        $this->assign('jumpUrl',__URL__."/admin_dirlist2/?action=chdr&file=".base64_encode($_POST['wdir']));
        $this->success('保存成功');
    }
    */
    //数据库操作
    public function admin_database(){
        $model = new Model();
        $li = $model->query("show table status");
        //dump($li);
        $count_free_data = 0;
        $count_data = 0;
        $j = 0;
        for($i=0;$i<count($li);$i++){
            if(preg_match("/^ss_+[a-zA-Z0-9_-]+$/",$li[$i]['Name'])){
                $li[$i]['Data_length']+=$li[$i]['Index_length'];
                $li[$i]['Data_length']=round(floatval($li[$i]['Data_length']/1024),2);
                $count_free_data+=$li[$i]['Data_free'];
                $count_data+=$li[$i]['Data_length'];
                $list[$j]->Name=$li[$i]['Name'];
                $list[$j]->Rows=$li[$i]['Rows'];
                $list[$j]->Data_length=$li[$i]['Data_length'];
                $list[$j]->Data_free=$li[$i]['Data_free'];
                $j++;
            }
        }
        $this->assign("list",$list);
        $this->assign("count_free_data",$count_free_data);
        $this->assign("count_data",$count_data);
        $this->display();
    }
/*
    public function repair_table(){
        if(!empty($_GET['name'])) {
            $model=new Model();
            $list=$model->query("repair table ".$_GET['name']);
            if($list!==false){
                $this->assign('jumpUrl',__URL__."/admin_database/");
                $this->success('修复成功');
            }
            else{
                $this->assign('jumpUrl',__URL__."/admin_database/");
                $this->error('修复失败');
            }
        }
        else{
            $this->error('参数错误！');
        }
    }
*/
    public function optimize_table(){
        if(!empty($_GET['name'])) {
            $model=new Model();
            $list=$model->query("optimize table ".$_GET['name']);
            if($list!==false){
                $this->assign('jumpUrl',__URL__."/admin_database/");
                $this->success('优化成功');
            }
            else{
                $this->assign('jumpUrl',__URL__."/admin_database/");
                $this->error('优化失败');
            }
        }else{
            $this->error('参数错误！');
        }
    }
/*
    public function exec_sql(){
        if(!empty($_POST['sqlquery'])) {
            $model=new Model();
            $list=$model->query($_POST['sqlquery']);
            if($list!==false){
                $this->assign('jumpUrl',__URL__."/admin_database/");
                $this->success('sql语句成功执行了');
            }
            else{
                $this->assign('jumpUrl',__URL__."/admin_database/");
                $this->error('sql语句执行失败');
            }
        }else{
            $this->error('SQL语句不能为空！');
        }
    }

    public function backup_database(){
        $model = new Model();
        $li = $model->query("show table status");
        $j = 0;
        for($i=0;$i<count($li);$i++){
            if(preg_match("/^ss_+[a-zA-Z0-9_-]+$/",$li[$i]['Name'])){
                $list[$j]->Name=$li[$i]['Name'];
                $j++;
            }
        }
        $this->assign("list",$list);

        $this->display();
    }

    public function restore_database(){
        $this->display();
    }

    public function listenvelope()
    {
        $redbagsetinfo = D("Siteconfig")->find(1);
        if($redbagsetinfo){
            $this->assign('redbagsetinfo',$redbagsetinfo);
        }
        else{
            $this->assign('jumpUrl',__URL__.'/mainFrame');
            $this->error('系统参数读取错误');
        }
        $this->display();
    }
*/
    public function save_redbagset()
    {
        $redbagset = D('Siteconfig');
        $vo = $redbagset->create();
        if(!$vo) {
            $this->assign('jumpUrl',__URL__.'/listenvelope/');
            $this->error('修改失败');
        }else{
            $redbagset->save();

            $this->assign('jumpUrl',__URL__.'/listenvelope/');
            $this->success('修改成功');
        }
    }
    //统计主播收入wp写
    public function admin_statisticsanchor(){
        $sql="select uid,sum(bean) as bean from ss_beandetail group by uid";
        $data=M()->query($sql);
        foreach($data as $k=>$v){
            $userinfo=M('Member')->field('username')->where('id='.$v['uid'])->find();
            $data[$k]['username']=$userinfo['username'];
        }
        $this->assign('data',$data);
        $this->display();
    }
    public function admin_countagentanchor(){
        $sql="select uid,sum(bean) as bean from ss_Emceeagentbeandetail group by uid";
        $data=M()->query($sql);
        foreach($data as $k=>$v){
            $userinfo=M('Member')->field('username')->where('id='.$v['uid'])->find();
            $data[$k]['username']=$userinfo['username'];
        }
        $this->assign('data',$data);
        $this->display();

    }

    public function truedel()
    {
        echo $_GET['uid'];
        M("member")->delete($_GET['uid']);
        $this->success("");
    }


    public function showvoterollpic(){
        $rollpics = D("xiuchang_voterollpic")->where("")->order('orderno')->select();
        $this->assign("rollpics",$rollpics);
        $this->display();
    }

    public function save_showvoterollpic()
    {
        //上传图片
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = 1048576 ;
        //设置上传文件类型
        $upload->allowExts  = explode(',','jpg,png');
        //设置上传目录
        //每个用户一个文件夹
        $prefix = date('Y-m');
        $uploadPath =  APP_WEB.'/style/rollpic/'.$prefix.'/';
        if(!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $upload->savePath =  $uploadPath;
        $upload->saveRule = uniqid;
        //执行上传操作
        if(!$upload->upload()) {
            // 捕获上传异常
            if($upload->getErrorMsg() != '没有选择上传文件'){
                $this->error($upload->getErrorMsg());
            }
        }
        else{
            $uploadList = $upload->getUploadFileInfo();
            $rollpicpath = '/style/rollpic/'.$prefix.'/'.$uploadList[0]['savename'];
        }

        $Edit_ID = $_POST['id'];
        $Edit_Orderno = $_POST['orderno'];
        $Edit_Picpath = $_POST['picpath'];
        $Edit_Linkurl = $_POST['linkurl'];
        $Edit_DelID = $_POST['ids'];

        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("xiuchang_voterollpic")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            D("xiuchang_voterollpic")->execute('update ss_xiuchang_voterollpic set picpath="'.$Edit_Picpath[$i].'",linkurl="'.$Edit_Linkurl[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
        }

        if($_POST['add_orderno'] != '' && $rollpicpath != '' && $_POST['add_linkurl'] != ''){
            $Rollpic = D('xiuchang_voterollpic');
            $Rollpic->create();
            $Rollpic->orderno = $_POST['add_orderno'];
            $Rollpic->picpath = $rollpicpath;
            $Rollpic->linkurl = $_POST['add_linkurl'];
            $Rollpic->addtime = time();
            $rollpicID = $Rollpic->add();
        }

        $this->assign('jumpUrl',__URL__."/showvoterollpic/");
        $this->success('操作成功');
    }

    public function gamevotepic(){
        $rollpics = D("youxi_voterollpic")->where("")->order('orderno')->select();
        $this->assign("rollpics",$rollpics);
        $this->display();
    }

    public function save_gamevotepic()
    {
        //上传图片
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize  = 1048576 ;
        //设置上传文件类型
        $upload->allowExts  = explode(',','jpg,png');
        //设置上传目录
        //每个用户一个文件夹
        $prefix = date('Y-m');
        $uploadPath =  APP_WEB.'/style/rollpic/'.$prefix.'/';
        if(!is_dir($uploadPath)){
            mkdir($uploadPath);
        }
        $upload->savePath =  $uploadPath;
        $upload->saveRule = uniqid;
        //执行上传操作
        if(!$upload->upload()) {
            // 捕获上传异常
            if($upload->getErrorMsg() != '没有选择上传文件'){
                $this->error($upload->getErrorMsg());
            }
        }
        else{
            $uploadList = $upload->getUploadFileInfo();
            $rollpicpath = '/style/rollpic/'.$prefix.'/'.$uploadList[0]['savename'];
        }

        $Edit_ID = $_POST['id'];
        $Edit_Orderno = $_POST['orderno'];
        $Edit_Picpath = $_POST['picpath'];
        $Edit_Linkurl = $_POST['linkurl'];
        $Edit_DelID = $_POST['ids'];

        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("youxi_voterollpic")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            D("youxi_voterollpic")->execute('update ss_youxi_voterollpic set picpath="'.$Edit_Picpath[$i].'",linkurl="'.$Edit_Linkurl[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
        }

        if($_POST['add_orderno'] != '' && $rollpicpath != '' && $_POST['add_linkurl'] != ''){
            $Rollpic = D('youxi_voterollpic');
            $Rollpic->create();
            $Rollpic->orderno = $_POST['add_orderno'];
            $Rollpic->picpath = $rollpicpath;
            $Rollpic->linkurl = $_POST['add_linkurl'];
            $Rollpic->addtime = time();
            $rollpicID = $Rollpic->add();
        }

        $this->assign('jumpUrl',__URL__."/gamevotepic/");
        $this->success('操作成功');
    }

    /**
    * 统计报表
    */
    public function bitable()
    {
        // TODO  分页
        $starttime = 0;
        $endtime = time();
        $data = M('Earncash')->query("select earn.id,earn.uid,earn.cash,earn.time,earn.status,mem.username,mem.nickname from ss_earncash as earn,ss_member as mem where earn.time>={$starttime} and earn.time<={$endtime} and earn.uid=mem.id");

        if (!$data) {
            $data = array();
        }
        foreach ($data as $k => $val) {
            $data[$k]['time'] = date('Y-m-d h:i:s',$val['time']);
        }
        $this->assign('history', $data);
        $this->display();
    }
    /*
    更改提现状态
     */
    public function changeStatus(){
        $ids = $_POST['ids'];
        $method = $_POST['method'];
        $checktime = time();
        if($method == "yes"){
            $sql = "update ss_earncash set status = '审核通过',checktime = $checktime where id in ($ids)";
        }else if($method == "no"){
            $sql = "update ss_earncash set status = '审核不通过',checktime = $checktime where id in ($ids)";
        }else{
            $sql = "update ss_earncash set status = '待审核',checktime = $checktime where id in ($ids)";
        }
        $data = M('Earncash')->query($sql);
        echo "修改成功";
    }
    /**
    * 导出提现记录
    */
    public function exportexcel()
    {
        if (!isset($_GET['start_time']) || !isset($_GET['end_time'])) {
            echo "请选择时间";
            exit;
        }
        if(isset($_GET['status']) && !empty($_GET['status'])){
            $status = "ss_earncash.status  = '".$_GET['status']."'";
        }else{
            $status = " 1 ";
        }
        $where_type = "0";
        if(isset($_GET['earn_type']) && !empty($_GET['earn_type'])){
            $where_type = "ss_earncash.type  = '".$_GET['earn_type']."'";
        }else{
            $where_type = " 1 ";
        }
        $nowTime = time();
        $startTime = strtotime($_GET['start_time']);
        $endTime = strtotime($_GET['end_time']);
        if($nowTime < $endTime || $nowTime < $startTime || $startTime > $endTime){
            echo "请选择正确的时间段";
            exit;
        }
        $csvStr = $this->getCSV($startTime, $endTime, $status, $where_type);
        $file = '/tmp/cash.csv'; 
        file_put_contents($file, $csvStr);
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header("Content-Length: ". filesize($file));
        //echo $csvStr;
        readfile($file);
    }

    protected function getCSV($startTime,$endTime,$status, $where_type)
    {
        $join = "ss_member on ss_member.id = ss_earncash.uid";
        $where = "ss_earncash.time>={$startTime} and ss_earncash.time <={$endTime} and {$status} and {$where_type}";
        $field = "ss_earncash.id id, ss_earncash.uid uid, ss_member.username username, ss_earncash.cash cash, ss_member.alipayname alipayname, ss_earncash.wexinOrderNo wexinOrderNo,ss_earncash.selfOrderNo selfOrderNo,ss_earncash.status status, ss_earncash.type type "; 
        $data = M('earncash')->join($join)->where($where)->field($field)->select();

        // echo M('earncash')->_sql();
        // exit;
        $csv = iconv('UTF-8', 'GB2312', '"序号","用户ID","用户名","金额","支付宝账号","微信支付订单号", "微信商户订单号","状态","提现类型"'."\r\n");
        
        $id = 1;
        foreach($data as $val) {
            $val['id'] = $id++;
            switch ($val['type']) {
                case '1':
                    $val['type'] = "支付宝提现";
                    break;
                case '2':
                    $val['type'] = "微信提现";
                    break;

                default:
                    $val['type'] = "默认";
                    break;
            }
            $csv .= iconv('UTF-8', 'GB2312', implode(",", $val)."\r\n");
        }
        $csv = substr($csv, 0, -2);
        return $csv;
    }
    /**
    * 视频截图展示
    */
    public function listplayerpic()
    {
        $this -> display();
    }
    public function listplayerpic_ed()
    {
        $this -> display();
    }
    public function GetShotPicList()
    {
        $code = $_POST["code"];
        if($code != "Get"){
            echo "非法操作";
            exit;
        }
        $now = time();
        $time = $now - 30;
        $ShotEnt = M("roomshot")->limit('100')->where("rs_time > $time and rs_time < $now")->group("rs_roomid")->order("RAND()")->select();
        // $ShotEnt = M("roomshot")->limit('100')->group("rs_roomid")->order("RAND()")->select();
        $ShotEnt = !$ShotEnt ? array() : $ShotEnt;
        if (!empty($ShotEnt)) {
            array_walk($ShotEnt, function(&$item) { $item['rs_time'] = date('Y-m-d h:i:s', $item['rs_time']); return $item; });
        }


        $roomIds;
        foreach($ShotEnt as $temp){
            $roomIds .= $temp['rs_roomid'] . ",";
        }
        $Shot = array();
        $roomIds = substr($roomIds,0,-1);
        $sql = "select curroomnum,roomstatus from ss_member where curroomnum in (".$roomIds.")";
        $rooms = M() -> query($sql);
        foreach($rooms as $room){
            foreach($ShotEnt as $h){
                if($room['curroomnum'] == $h["rs_roomid"]){
                    $h["status"] = $room['roomstatus'];
                    array_push($Shot,$h);
                }
            }
        }

        $json['shot'] = $Shot;
        // $statusTime = $now - 24*60*60;
        // $LogIds = M("roomshotlog")->where("checktime > $statusTime")->group("roomid")->field("max(logid) as logid")->select();
        // $idsSql = "select * from ss_roomshotlog where logid in (";
        // foreach($LogIds as $Log){
        //         $idsSql .= $Log['logid'].",";
        // }

        // $idsSql = substr($idsSql,0,-1);
        // $idsSql .= ")";
        // $RoomLog = M()->query($idsSql);
        // $RoomLog = !$RoomLog ? array() : $RoomLog;

        // $json['log'] = $RoomLog;

        echo json_encode($json);
    }
    public function GetFloatPicList()
    {
        $roomid = $_POST["roomid"];
        if(empty($roomid)){
            exit;
        }
        $time = time()+20;
        $oldtime = time()-20;
        $sql = "select * from ss_roomshot where rs_roomid = '".$roomid."' order by rs_time desc limit 2"; //rs_time > $oldtime and rs_time < $time and
        $ShotEnt = M()->query($sql);
        if (!empty($ShotEnt)) {
            array_walk($ShotEnt, function(&$item) { $item['rs_time'] = date('Y-m-d h:i:s', $item['rs_time']); return $item; });
        }

        // $ShotEnt = !$ShotEnt ? array() : $ShotEnt;
        $json['shot'] = $ShotEnt;
        echo json_encode($json);
    }
    public function GetRoomStatus(){
     //   $sql = "insert into ss_roomshotlog values(3,'41161465369008','admin',1466430020,'0'),(4,'41161465369008','admin',1466430030,'3')";
       // M()->execute($sql);
       echo time();
    }
    /*
     public function GetDisableList(){
        $code = $_POST["code"];
        if($code != "Get"){
            echo "非法操作";
            exit;
        }
        //查出RoomID和禁止时间,列表
        $Disables = M("roomshotlog")->group("roomid")->field("max(logid) as logid")->select();
        $disSQL = "select * from ss_roomshotlog where logid in (";
        //拼装SQL，查出被禁止的房间的最新图片
        $picSQL = "select MAX(rs_id)as rs_id,rs_time,rs_roomid,rs_picurl FROM ss_roomshot WHERE rs_roomid IN (";
        $cnt = 0;
        foreach($Disables as $Disable){
                $disSQL .= $Disable["logid"];
        }
        $disSQL = substr($picSQL,0,-1);
        $disSQL .= ")";
        $DisableEnt = M()->query($disSQL);
        foreach($DisableEnt as $Temp){
            if($Temp["roomstatus"] == "3"){
                $picSQL .= "'".$Temp["roomid"]."'";
                $cnt++;
            }
        }
        if($cnt == 0){
        $PicList = array();
        }else{
            $picSQL = substr($picSQL,0,-1);
            $picSQL .= ") GROUP BY rs_roomid";
            $PicList = M()->query($picSQL);

            if (!empty($PicList)) {
                array_walk($PicList, function(&$item) { $item['rs_time'] = date('Y-m-d h:i:s', $item['rs_time']); return $item; });
            }
        }

        //查询出图片列表
        $json['list'] = $PicList;
        echo json_encode($json);
    }
    */

    public function SetShotStatus()
    {
        //0 没问题
        //1 有风险
        //2 有问题
        //3 禁播
        //true表示从禁播列表进入的
        $AllArray = array();
        $Temp0 = $_POST["Temp0Arr"];
        $Temp1 = $_POST["Temp1Arr"];
        $Temp2 = $_POST["Temp2Arr"];
        $Temp3 = $_POST["Temp3Arr"];
        $updateSQL = array();
        $Status = $_POST["status"];
        foreach($Temp0 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "0";
            $updateSQL[0] .= $temp . ",";
            array_push($AllArray,$Array);
        }
        foreach($Temp1 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "1";
            $updateSQL[1] .= $temp . ",";
            array_push($AllArray,$Array);
            $this->sendToRoom($temp, '系统消息：【有风险】当前房间已被管理员标记，请文明聊天');
        }
        foreach($Temp2 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "2";
            $updateSQL[2] .= $temp . ",";
            array_push($AllArray,$Array);
            $this->sendToRoom($temp, '系统消息：【有问题】当前房间已被管理员标记，请文明聊天');
        }
        foreach($Temp3 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "3";
            $updateSQL[3] .= $temp . ",";
            array_push($AllArray,$Array);
            $message['type'] = 'error.outlive';
            $message['content'] = '系统消息：【禁播】当前房间已被管理员禁播三天';
            $this->sendToRoom($temp, $message);
        }


        $userid = $_SESSION['adminname'];
        $sql = "insert into ss_roomshotlog(roomid,roomstatus,userid,checktime)values";
        foreach($AllArray as $Temp){
            if($Status != "true"){
                //不是禁播列表中提交的
                if($Temp["status"] == "3"){
                    //如果是禁止就禁流
                    $this->disableStream($Temp["roomid"]);
                }
            }else{
                //从禁播列表中提交的
                if($Temp["status"] != "3"){
                    //开流
                    $this->enableStream($Temp["roomid"]);
                }
            }
            //然后还是要把数据拼装日志记录里
            $sql .=  "('".$Temp['roomid']."','".$Temp['status']."','".$userid."',".time()."),";
        }

        //执行变更状态的sql
        for($i = 0;$i < 4;$i++){
            if(strlen($updateSQL[$i]) > 0){
                $updateSQL[$i] = substr($updateSQL[$i],0,-1);
                $update = "update ss_member set roomstatus = '".$i."' where curroomnum in (".$updateSQL[$i].")";
                M()->execute($update);
            }
        }
        $sql = substr($sql,0,-1);
        $status = M()->execute($sql);
        if($status){
            echo "标记成功";
        }else{
            echo "标记成功,但日志插入失败";
        }
    }

     public function GetDisableList(){
        $code = $_POST["code"];
        if($code != "Get"){
            echo "非法操作";
            exit;
        }

        $roomList = M("member")->where("roomstatus = '3'")->select();
        $picSQL = "select MAX(rs_id)as rs_id,rs_time,rs_roomid,rs_picurl,mem.id,mem.nickname FROM ss_roomshot,ss_member as mem WHERE mem.curroomnum=ss_roomshot.rs_roomid and  ss_roomshot.rs_roomid IN (";
        foreach($roomList as $room){
            $picSQL .= "'".$room["curroomnum"]."',";
        }
        if(count($roomList) > 0){
            $picSQL = substr($picSQL,0,-1);
            $picSQL .= ") GROUP BY rs_roomid";
            $PicList = M()->query($picSQL);
            if (!empty($PicList)) {
                array_walk($PicList, function(&$item) { 
                    $item['rs_time'] = date('Y-m-d h:i:s', $item['rs_time']);
                    return $item; 
                });
            }
        }
        $json['list'] = $PicList;
        echo json_encode($json);
    }

    /*
    public function SetShotStatus()
    {
        //0 没问题
        //1 有风险
        //2 有问题
        //3 禁播
        //true表示从禁播列表进入的
        $AllArray = array();
        $Temp0 = $_POST["Temp0Arr"];
        $Temp1 = $_POST["Temp1Arr"];
        $Temp2 = $_POST["Temp2Arr"];
        $Temp3 = $_POST["Temp3Arr"];
        $Status = $_POST["status"];
        foreach($Temp0 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "0";
            array_push($AllArray,$Array);
        }
        foreach($Temp1 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "1";
            array_push($AllArray,$Array);
            $this->sendToRoom($temp, '系统消息：【有风险】当前房间已被管理员标记，请文明聊天');
        }
        foreach($Temp2 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "2";
            array_push($AllArray,$Array);
            $this->sendToRoom($temp, '系统消息：【有问题】当前房间已被管理员标记，请文明聊天');
        }
        foreach($Temp3 as $temp){
            $Array["roomid"] = $temp;
            $Array["status"] = "3";
            array_push($AllArray,$Array);
            $this->sendToRoom($temp, '系统消息：【禁播】当前房间已被管理员禁播三天');
        }
        $userid = $_SESSION['adminname'];
        $sql = "insert into ss_roomshotlog(roomid,roomstatus,userid,checktime)values";
        foreach($AllArray as $Temp){
            if($Status != "true"){
                //不是禁播列表中提交的
                if($Temp["status"] == "3"){
                    //如果是禁止就禁流
                    $this->disableStream($Temp["roomid"]);
                }
            }else{
                //从禁播列表中提交的
                if($Temp["status"] != "3"){
                    //开流
                    $this->enableStream($Temp["roomid"]);
                }
            }
            //然后还是要把数据拼装日志记录里
            $sql .=  "('".$Temp['roomid']."','".$Temp['status']."','".$userid."',".time()."),";
        }
        $sql = substr($sql,0,-1);
        $status = M()->execute($sql);
        if($status){
            echo "标记成功";
        }else{
            echo "标记出错";
        }
    }
    */

    /**
    * 禁播
    *
    * @param int $room_id
    */
    protected function disableStream($room_id)
    {
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';

        $url = $domain . '/OpenAPI/v1/Qiniu/disableStream';
        $data = array('room_id'=>$room_id);
        $this->curlRequest($url, true, $data);
    }
    //fix by zdd
    public function view_liverecord()
    {
        $userId = $_GET['userid'];
        $numEntity = M("member")->where("id = " .$userId)->find();
        if(!empty($_GET['start_time'])){
            $startTime = strtotime($_GET['start_time']);
            $endTime = time()+86400;
        }else{
            $startTime = time() - 86400*30;
            $endTime = time()+86400; 
        }
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';
        $url = $domain . '/OpenAPI/v1/Qiniu/getSegments';
        $data = array('roomID'=>$numEntity['curroomnum'],'startTime'=>$startTime,'endTime'=>$endTime);
        $arrayList = $this->curlRequest($url, false,$data);
        $arrayList = (Array)json_decode($arrayList,true);
        $this->assign('segments',$arrayList['data']['segments']);
        $this->assign('roomID',$numEntity['curroomnum']);
        $this->assign('duration',$arrayList['data']['duration']);
        $this->display();
    }

    public function video_m3u8(){
        $roomId = $_GET['roomId'];
        $startTime = $_GET['start'];
        $endTime = $_GET['end'];
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
        $curl_url = $domain . '/OpenAPI/v1/Qiniu/getPlayback';
        $data = array('roomID'=>$roomId,'startTime'=>$startTime,'endTime'=>$endTime);
        $url = $this->curlRequest($curl_url, false, $data);
        $url = (Array)json_decode($url,true);
        $this->assign('url',$url['data']['ORIGIN']);
        $this->display();
    }

    public function listvideo(){
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
        $curl_url = $domain . '/OpenAPI/v1/Qiniu/getOnlineHlsUrls';
        $url = $this->curlRequest($curl_url, false, array());
        // $url = (Array)json_decode($url,true);
        //$url = array();
        $this->assign('url',$url);
        $this->display();
    }

    public function onlineVideo() {
        $roomID = $_GET['room_id'];
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://demo.meilibo.net';
        $curl_url = $domain . '/OpenAPI/v1/Qiniu/getRtmpUrls';



        $data = array('roomID'=>$roomID);
        $result = $this->curlRequest($curl_url, false, $data);

        $result = json_decode($result,true);



        $this->assign('url',$result['data']['ORIGIN']);
        $this->display();
    }


    public function gettest(){
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';
        $curl_url = $domain . '/OpenAPI/v1/Qiniu/getOnlineHlsUrls';
        $url1 = $this->curlRequest($curl_url, false, $data);
        $url = (Array)json_decode($url1,true);
        if($url['data'] == null){
            echo json_encode(array());
            exit;
        }
        echo json_encode($url['data']);
    }
    public function listguild(){
        $count=M("agentfamily")->where("zhuangtai='已通过'")->count();
        //使用联合查询带分页 查询出申请用户的相关信息
        import("@.ORG.Page");
        $p = new Page($count,20);
        $p->setConfig('header','条');
        $page = $p->show();
        $fix= C('DB_PREFIX');
        $field="m.nickname,m.earnbean,af.*";
        $res = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("zhuangtai='已通过'")->limit($p->firstRow.",".$p->listRows)->select();
        //根据查到的earnbean 查询用户等级
        $a=0;
        foreach($res as $k=>$vo){
        $emceelevel = getEmceelevel($vo['earnbean']);
        $res[$a]['emceelevel']=$emceelevel;
        $a++;
        }
        $this->assign("page",$page);
        $this->assign("lists",$res);
        $this->display();
    }
    public function addguild(){
        $this->display();
    }
    public function do_addguild(){
        $uid = $_POST["uid"];
        $name = $_POST["name"];
        $shortname = substr($name,0,4);
        $info = $_POST["info"];
        $ratio = $_POST["ratio"];
        $sqtime = time();
        $zhuangtai='已通过';
        $yuanyin = "无";
        $shtime = time();
        $anchorratio = $_POST["anchorratio"];
        if($uid == "" || $name == "" || $info == ""){
            $this->error("还有信息未填写完整");
        }
        $rs = M("member")->field("username")->where("id = ".$uid)->find();
        if($rs == null){
            $this->error("未找到该用户");
        }
        if(!$this->CheckProp($ratio) || !$this->CheckProp($anchorratio)){
            $this->error("请输入1-100之内的比例");
        }
        if($_FILES['img']['tmp_name'] != ''){
            //上传图片
            import("@.ORG.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = 1048576 ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,png');
            //设置上传目录
            //每个用户一个文件夹
            $uploadPath =  APP_WEB.'/style/Familyimg/';
            if(!is_dir($uploadPath)){
                mkdir($uploadPath,0777,true);
            }
            $upload->savePath =  $uploadPath;
            $upload->saveRule = uniqid;
            //执行上传操作
            if(!$upload->upload()) {
                // 捕获上传异常
                if($upload->getErrorMsg() != '没有选择上传文件'){
                    $this->error($upload->getErrorMsg());
                }
            }
            else{
                $uploadList = $upload->getUploadFileInfo();
                $picurl = $uploadList[0]['savename'];
            }
        }else{
            $this->error("请上传图片");
        }
        $sql = "insert into ss_agentfamily (uid,familyname,shortname,familyinfo,familyimg,sqtime,zhuangtai,familyratio,anchorratio,yuanyin,shtime) values({$uid},'{$name}','{$shortname}','{$info}','{$picurl}',{$sqtime},'{$zhuangtai}',{$ratio},{$anchorratio},'{$yuanyin}',$shtime)";
        $rs = M()->execute($sql);
        if($rs > 0){
            $this->success("添加成功");
        }else{
            $this->error("添加失败");
        }
    }
    public function editguild(){
        $id = $_GET['id'];
        if($id == null || $id <= 0){
            $this -> error("非法参数");
        }
        $info = M("agentfamily")->where("id = ".$id)->find();
        $this->assign("info",$info);
        $this->display();
    }
    public function do_editguild(){
        $id = $_POST['id'];
        $uid = $_POST["uid"];
        $name = $_POST["name"];
        $shortname = substr($name,0,4);
        $info = $_POST["info"];
        $ratio = $_POST["ratio"];
        $sqtime = time();
        $zhuangtai='已通过';
        $yuanyin = "无";
        $shtime = time();
        $anchorratio = $_POST["anchorratio"];
        if($uid == "" || $name == "" || $info == ""){
            $this->error("还有信息未填写完整");
        }
        $rs = M("member")->field("username")->where("id = ".$uid)->find();
        if($rs == null){
            $this->error("未找到该用户");
        }
        if(!$this->CheckProp($ratio) || !$this->CheckProp($anchorratio)){
            $this->error("请输入1-100之内的比例");
        }
        if($_FILES['img']['tmp_name'] != ''){
            //上传图片
            import("@.ORG.UploadFile");
            $upload = new UploadFile();
            //设置上传文件大小
            $upload->maxSize  = 1048576 ;
            //设置上传文件类型
            $upload->allowExts  = explode(',','jpg,png');
            //设置上传目录
            //每个用户一个文件夹
            $uploadPath =  APP_WEB.'/style/Familyimg/';
            if(!is_dir($uploadPath)){
                mkdir($uploadPath,0777,true);
            }
            $upload->savePath =  $uploadPath;
            $upload->saveRule = uniqid;
            //执行上传操作
            if(!$upload->upload()) {
                // 捕获上传异常
                if($upload->getErrorMsg() != '没有选择上传文件'){
                    $this->error($upload->getErrorMsg());
                }
            }
            else{
                $uploadList = $upload->getUploadFileInfo();
                $picurl = $uploadList[0]['savename'];
            }
            $sql = "update ss_agentfamily set familyname = '{$name}',familyimg = '{$picurl}',uid = '{$uid}',familyinfo = '{$info}',familyratio = '{$ratio}',anchorratio = '{$anchorratio}' where id = '{$id}'";
        }else{
            $sql = "update ss_agentfamily set familyname = '{$name}',uid = '{$uid}',familyinfo = '{$info}',familyratio = '{$ratio}',anchorratio = '{$anchorratio}' where id = '{$id}'";
        }
        $rs = M()->execute($sql);
        if($rs >= 0){
            $this->success("修改成功");
        }else{
            $this->error("修改失败");
        }
    }
    public function delguild(){
        $id = $_GET['id'];
        $uid = $_GET['uid'];
        if($id == "" || $id <= 0){
            $this->error("参数错误");
        }
        M("agentfamily")->where("id = ".$id)->delete();
        $sql = "update ss_member set agentuid = 0 where agentuid = {$uid}";
        M()->execute($sql);
        $this->success("您已成功解散该公会");
    }

    public function listteam(){
        $sql = "select ss_agentfamily.familyname,ss_team.* from ss_team inner join ss_agentfamily on ss_team.teamagent = ss_agentfamily.id";
        $teamList = M()->query($sql);
        $this->assign("teamList",$teamList);
        $this->display();
    }
    public function addteam(){
        $familyList = M("agentfamily")->where("zhuangtai='已通过'")->select();
        $this->assign("familyList",$familyList);
        $this->display();
    }
    public function do_addteam(){
        $agentId = $_POST['agentId'];
        $rs = M("team")->where("teamagent = " . $agentId)->select();
        if($rs != NULL){
            $this->error("该家族已存在战队");
        }else{
            $teamName = $_POST['name'];
            $addtime = time();
            $sql = "insert into ss_team (teamname,teamagent,addtime) values('{$teamName}',$agentId,$addtime)";
            $rs = M()->execute($sql);
            if($rs > 0){
                $this->success("战队添加成功");
            }else{
                $this->error("战队添加失败");
            }
        }
    }
    public function editteam(){
        $teamId = $_GET['id'];
        $info = M("team")->where("id = ".$teamId)->find();
        $familyList = M("agentfamily")->where("zhuangtai='已通过'")->select();
        $this->assign("info",$info);
        $this->assign("familyList",$familyList);
        $this->display();
    }
    public function do_editteam(){
        $teamId = $_POST['id'];
        $teamName = $_POST['name'];
        $sql = "update ss_team set teamname = '{$teamName}' where id = {$teamId}";
        $exe_rs = M()->execute($sql);
        if($exe_rs >= 0){
            $this->success("战队信息修改成功");
        }else{
            $this->error("战队信息修改失败");
        }

        //下面的方法执行修改战队信息和所属公会，理论上战队不可更改所属公会
        /*
        $teamId = $_POST['id'];
        $agentId = $_POST['agentId'];
        $team = M("team")->where("id = ".$teamId)->find();
        $rs = M("team")->where("teamagent = " . $agentId)->select();
        if($rs != NULL){
            if($rs[0]["teamagent"] != $team['teamagent']){
                $this->error("该家族已存在战队");
            }
        }
        $teamName = $_POST['name'];
        $sql = "update ss_team set teamname = '{$teamName}',teamagent = {$agentId} where id = {$teamId}";
        $exe_rs = M()->execute($sql);
        if($exe_rs >= 0){
            $this->success("战队信息修改成功");
        }else{
            $this->error("战队信息修改失败");
        }
        */
    }
    public function delteam(){
        $id = $_GET['id'];
        if($id == "" || $id <= 0){
            $this->error("参数错误");
        }
        M("team")->where("id = ".$id)->delete();
        //设置member的战队信息变更
        $sql = "update ss_member set agentuid = 0 where agentuid = {$uid}";
        M()->execute($sql);
        $this->success("您已成功解散该战队");
    }

    public function teammember(){
        $id = $_GET['id']; //战队ID
        if(empty($_GET['id'])){
            $this->error("参数错误");
        }
        $condition = 'isdelete="n"';
        if(!empty($_GET['start_time'])){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime>='.$unixtime;
        }
        if(!empty($_GET['end_time'])){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if(!empty($_GET['keyword']) && $_GET['keyword'] != '请输入用户ID或用户名'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and username like \'%'.$_GET['keyword'].'%\'';
            }
        }
        if(!empty($_GET['sign'])){
            $condition .= ' and sign="'.$_GET['sign'].'"';
        }
        if(!empty($_GET['emceeagent'])){
            $condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
        }
        if(!empty($_GET['payagent'])){
            $condition .= ' and payagent="'.$_GET['payagent'].'"';
        }
        $condition .= " and teamid = ".$id;
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        // echo $member->getLastSql();
        $p->setConfig('header','条');
        $page = $p->show();
        $team = M("team")->where("id = ".$id)->find();
        $this->assign("team",$team);
        $this->assign('page',$page);
        $this->assign('members',$members);
        $this->display();
    }
    public function addteammember(){
        $teamId = $_GET['id'];
        $familySQL = "select ss_agentfamily.uid as uid from ss_team inner join ss_agentfamily on ss_team.teamagent = ss_agentfamily.id where ss_team.id = ".$teamId;
        $FamilyInfo = M()->query($familySQL);
        $userList = M("member")->field("username,id,sign")->where("teamid = 0 and agentuid = ".$FamilyInfo[0]['uid'])->select();
        $this->assign("list",$userList);
        $this->display();
    }
    public function do_addteammember(){
        $id = $_POST['id'];
        $userIds = $_POST['userIds'];
        $Ids;
        foreach($userIds as $temp){
            $Ids .= $temp . ",";
        }
        $Ids = substr($Ids,0,-1);
        //获取战队的家族信息中userid
        $familySQL = "select ss_agentfamily.uid as uid from ss_team inner join ss_agentfamily on ss_team.teamagent = ss_agentfamily.uid where ss_team.teamagent = ".$id;
        $FamilyInfo = M()->query($familySQL);
        $userInfo = M("member")->field("agentuid")->where("id = ".$userId)->find();
        if($FamilyInfo['uid'] != $userInfo['agentuid']){
            $this->error("该用户不属于该战队所属的家族，不可添加");
        }else{
            $sql = "update ss_member set teamid = {$id} where id in ({$Ids})";
            $rs = M()->execute($sql);
            if($rs >= 0){
                $this->success("添加成功");
            }else{
                $this->error("添加失败，请稍后再试");
            }
        }
    }
    public function delteammember(){
        $id = $_GET['id'];
        $userId = $_GET["userid"];
        $userInfo = M("member")->where("id = ".$userId)->field("teamid")->find();
        if($userInfo['teamid'] != $id){
            $this->error("该用户不属于该战队，无法踢出");
        }else{
            $sql = "update ss_member set teamid = 0 where id = {$userId}";
            $rs = M()->execute($sql);
            if($rs >= 0){
                $this->success("已成功清除该成员");
            }else{
                $this->error("清除失败，请稍后再试");
            }
        }
    }

    public function guildmember(){
        $agentuid = $_GET['agentuid']; //战队ID
        if(empty($_GET['agentuid'])){
            $this->error("参数错误");
        }
        $condition = 'isdelete="n"';
        if(!empty($_GET['start_time'])){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime>='.$unixtime;
        }
        if(!empty($_GET['end_time'])){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
        }
        if(!empty($_GET['keyword']) && $_GET['keyword'] != '请输入用户ID或用户名'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and username like \'%'.$_GET['keyword'].'%\'';
            }
        }
        if(!empty($_GET['sign'])){
            $condition .= ' and sign="'.$_GET['sign'].'"';
        }
        if(!empty($_GET['emceeagent'])){
            $condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
        }
        if(!empty($_GET['payagent'])){
            $condition .= ' and payagent="'.$_GET['payagent'].'"';
        }
        $condition .= " and agentuid = ".$agentuid;
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        // echo $member->getLastSql();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('members',$members);
        $this->display();
    }
    public function addguildmember(){
        $agentuid = $_GET['agentuid']; //战队ID
        if(empty($_GET['agentuid'])){
            $this->error("参数错误");
        }
        $cnt = 0;
        $condition = 'isdelete="n"';
        if(!empty($_GET['start_time'])){
            $timeArr = explode("-", $_GET['start_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime>='.$unixtime;
            $cnt++;
        }
        if(!empty($_GET['end_time'])){
            $timeArr = explode("-", $_GET['end_time']);
            $unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
            $condition .= ' and addtime<='.$unixtime;
            $cnt++;
        }
        if(!empty($_GET['keyword']) && $_GET['keyword'] != '请输入用户ID或用户名'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and username like \'%'.$_GET['keyword'].'%\'';
            }
            $cnt++;
        }
        if(!empty($_GET['sign'])){
            $condition .= ' and sign="'.$_GET['sign'].'"';
            $cnt++;
        }
        if(!empty($_GET['emceeagent'])){
            $condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
            $cnt++;
        }
        if(!empty($_GET['payagent'])){
            $condition .= ' and payagent="'.$_GET['payagent'].'"';
            $cnt++;
        }
        $orderby = 'id desc';
        $member = D("Member");
        $count = $member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
        // echo $member->getLastSql();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        if($cnt > 0){
            $this->assign('members',$members);
        }else{
            $this->assign('members',array());
        }
        $this->display();
    }
    public function do_addguildmember(){
        $userId = $_GET['userid'];
        $agentuid = $_GET['agentuid'];
        $userInfo = M("member")->where("id = ".$userId)->field("agentuid")->find();
        if($userInfo['agentuid'] > 0){
            $this->error("该用户已加入其他公会");
        }else{
            $sql = "update ss_member set agentuid = {$agentuid} where id = {$userId}";
            $rs = M()->execute($sql);
            if($rs >= 0){
                $this->success("添加成功");
            }else{
                $this->error("添加失败，请稍后再试");
            }
        }

    }
    public function delguildmember(){
        $agentuid = $_GET['agentuid'];
        $userId = $_GET["userid"];
        $userInfo = M("member")->where("id = ".$userId)->field("agentuid")->find();
        if($userInfo['agentuid'] != $agentuid){
            $this->error("该用户不属于该公会，无法踢出");
        }else{
            $sql = "update ss_member set agentuid = 0 , teamid = 0 where id = {$userId}";
            $rs = M()->execute($sql);
            if($rs >= 0){
                $this->success("已成功清除该成员");
            }else{
                $this->error("清除失败，请稍后再试");
            }
        }
    }
    public function listapprove(){
        //获取列表信息
        $sql = "select count(ss_approve.id) count from ss_approve";
        $count = M("")->query($sql);
        $count = $count[0]['count'];
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $p->setConfig('header','条');
        $sql = "SELECT mem.nickname,sa.* FROM `ss_approve` as sa, `ss_member` as mem  WHERE mem.id=sa.uid ORDER BY sa.uptime DESC limit ".$p->firstRow.",".$p->listRows;
        $approveList = M("")->query($sql);
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('approveList',$approveList);
        $this->display();
    }
    public function setStatus(){
        if(empty($_POST['id']) || empty($_POST['status'])){
            $returnData["code"] = 0;
            $returnData["msg"] = "参数错误";
            echo json_encode($returnData);
            exit;
        }
        $id = $_POST['id'];
        $status = $_POST['status'];
        $where_id['id'] = $id;
        $approve_dao = D('approve');
        $member_dao = M('member');
        $approve = $approve_dao->where($where_id)->find();
        if($approve){
                if($approve['status'] == 2 ){
                    $returnData["code"] = 0;
                    $returnData["msg"] = "该数据暂无法更改";
                    echo json_encode($returnData);
                    exit;
                }else{
                    $confirm = true;
                    $update_member = 1;

                    //这里判断他是什么时候选择的
                    if($approve['status'] < 2){
                        if($status == 1 && $approve['status'] == 0){
                            $approveid = $approve['sid'];
                        }elseif($status == 2 && $approve['status'] == 0){
                            $confirm = false;
                        }elseif($status == 2 && $approve['status'] == 1){
                            $approveid = '无';
                        }else{
                            $returnData["code"] = 0;
                            $returnData["msg"] = "参数错误";
                            echo json_encode($returnData);
                            exit;
                        }
                    }
                    if($confirm){
                        $member = array(
                            'id'    =>  $approve['uid'],
                            'approveid'=> $approveid
                            );
                        $update_member = $member_dao->save($member);
                    }
                    $approve['status'] = $status;
                    $approve['checktime'] = time();
                    $approve['adminuser'] = $_SESSION['adminname'];
                    $update_approve = $approve_dao->where($where_id)->save($approve);
                    if($update_approve && $update_member){
                        $returnData["code"] = 1;
                        $returnData["msg"] = "修改成功";
                        // $returnData["msg"] = $sql;
                        echo json_encode($returnData);
                        exit;
                    }else{
                        $returnData["code"] = 0;
                        $returnData["msg"] = "修改失败";
                        // $returnData["msg"] = $sql;
                        echo json_encode($returnData);
                        exit;
                    }
                }
        }else{
            $returnData["code"] = 0;
            $returnData["msg"] = "用户参数错误";
            echo json_encode($returnData);
            exit;
        }

    }

    /**
    * 开启
    *
    * @param int $room_id
    */
    protected function enableStream($room_id)
    {
        $config = M('Siteconfig')->find();
        $domain = !empty($config['siteurl']) ? $config['siteurl'] : 'http://www.meilibo.net';

        $url = $domain . '/OpenAPI/v1/Qiniu/enableStream';
        $data = array('room_id'=>$room_id);
        $this->curlRequest($url, true, $data);
    }


    /**
    * 单独发给主播的消息
    *
    * @param int $id
    * @param string $message
    */
    protected function sendToAnchor($id, $message)
    {
        $this->pipe('sendToUid',$id, $message);
    }

    /**
    * 发送给房间的消息
    *
    * @param int $roomid
    * @param string $message
    */
    protected function sendToRoom($roomid, $message)
    {
        $this->pipe('sendToGroup', $roomid, $message);
    }

    /**
    * 后台到聊天室的通信接口
    *
    * @param string $method  GatewayClient 内部接口
    * @param int    $id      用户ID/房间ID
    * @param string $message 消息
    * @return void
    */
    private function pipe($method, $id, $message)
    {   
        if(is_array($message)){
            APIAction::Instance()->Gateway($method,array($id,json_encode(array("type"=>$message['type'],"content"=>$message['content']))));
        }else{
            APIAction::Instance()->Gateway($method,array($id,json_encode(array("type"=>"error","content"=>$message))));
        }
    }


    /**
    * 发起CURL请求
    *
    * @param string url
    * @param bool isPost
    * @param array $data
    */
    protected function curlRequest($url, $isPost, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $url =  $url . '?' . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }


    /**
     *已禁用用户列表
     *
     */
    public function banlists(){

        $condition = 'banstatus = "0" ';
        if(!empty($_GET['start_time'])){
            $condition .= ' and  bantime>='.strtotime($_GET['start_time']);
        }
        if(!empty($_GET['end_time'])){
            $condition .= ' and  bantime<='.strtotime($_GET['end_time']);
        }
        if(!empty($_GET['keyword']) && $_GET['keyword'] != '请输入用户ID或用户名'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (uid='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and username like \'%'.$_GET['keyword'].'%\'';
            }
        }
        if(!empty($_GET['sign'])){
            $condition .= ' and sign="'.$_GET['sign'].'"';
        }
        if(!empty($_GET['emceeagent'])){
            $condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
        }
        if(!empty($_GET['payagent'])){
            $condition .= ' and payagent="'.$_GET['payagent'].'"';
        }

        $banlist = M('banlist');
        $join = array('ss_member ON ss_member.id = ss_banlist.uid');
        $count = $banlist->where($condition)->join($join)->count();
        // echo $banlist->_sql();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);

        $this->assign('banlists',  $banlist->where($condition)->join($join)->limit($p->firstRow.','.$p->listRows)->field('ss_banlist.*,ss_member.nickname,ss_member.username')->select());
        $this->display();
    }
    /**
     *已禁用用户列表
     *
     */
    public function banlistRecord(){
        $uid = $_GET['userid'];
        $condition = ' 1 ';
        if(!empty($_GET['start_time'])){
            $condition .= ' and  bantime>='.strtotime($_GET['start_time']);
        }
        if(!empty($_GET['end_time'])){
            $condition .= ' and bantime<='.strtotime($_GET['end_time']);
        }
        if(empty($uid)){
            if(!empty($_GET['keyword']) && $_GET['keyword'] != '请输入用户ID或用户名'){
                if(preg_match("/^\d*$/",$_GET['keyword'])){
                    $condition .= ' and (uid='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\')';
                }
                else{
                    $condition .= ' and username like \'%'.$_GET['keyword'].'%\'';
                }
                if(!empty($_GET['sign'])){
                    $condition .= ' and sign="'.$_GET['sign'].'"';
                }
                if(!empty($_GET['emceeagent'])){
                    $condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
                }
                if(!empty($_GET['payagent'])){
                    $condition .= ' and payagent="'.$_GET['payagent'].'"';
                }
            }
        }elseif(is_numeric($uid)){
            $condition .= ' and uid = '.$uid.' and banstatus = "1"';
        }else{
            $this->error("参数错误");
        }


        $banlist = M('banlist');
        $join = array('ss_member ON ss_member.id = ss_banlist.uid');
        $count = $banlist->where($condition)->join($join)->count();
        // echo $banlist->_sql();

        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('uid',$uid);

        $this->assign('banlists',  $banlist->where($condition)->join($join)->limit($p->firstRow.','.$p->listRows)->field('ss_banlist.*,ss_member.nickname,ss_member.username')->select());
        $this->display();
    }
    /**
     *
     *
     */
    public function listtopic(){
        $topic = D('topic')->where('promote = "1"')->order('promote desc, createtime desc')->select();
        // $topic = array_unique($topic);
        $backstream = D('backstream')->where('status = "1"')->select();
        $all_data = array();
        $i = 0;
        // 1,2

        foreach ($topic as $key => $one_topic) {
            $n = 0;
            foreach ($backstream as $one_backstream) {
                $topics = explode(",", $one_backstream['topics']);
                $taf = in_array($one_topic['id'], $topics);

                //计算发起该话题用户数量
                if(in_array($one_topic['id'], $topics)){
                        $n++;
                    }

            }
            //存入数据
                $topic_title_end[$key] = array(
                    'topic_id'      =>  $one_topic['id'],
                    'topic_title'   =>  $one_topic['title'],
                    'topic_num'     =>  $n,
                    'promote'     =>  $one_topic['promote']
                    );
        }
        for ($i=0; $i < count($topic_title_end)-1 ; $i++) {
            if($topic_title_end[$i]['promote'] != "1"){
                for ($j=$i+1; $j < count($topic_title_end); $j++) {
                    if($topic_title_end[$i]['topic_num'] < $topic_title_end[$j]['topic_num'] ){
                        $arr = $topic_title_end[$j];
                        $topic_title_end[$j] = $topic_title_end[$i];
                        $topic_title_end[$i] = $arr;
                    }
                }
            }
        }
        $this->assign("topics", $topic_title_end);
        $this->display();
    }
    public function alltopic(){
        $condition = '';
        $topic_name = $_GET['topic_name'];
        $type = $_GET['type'];
        if(!empty($_GET['topic_name'])){
            $condition = 'title like "%'.$topic_name.'%"';
        }
        $topic_dao = M('topic');

        $topic = $topic_dao->where($condition)->order('promote desc, createtime desc')->select();

        $backstream = D('backstream')->where('status = "1"')->select();
        $all_data = array();
        $i = 0;
        // 1,2

        foreach ($topic as $key => $one_topic) {
            $n = 0;
            foreach ($backstream as $one_backstream) {
                $topics = explode(",", $one_backstream['topics']);
                $taf = in_array($one_topic['id'], $topics);

                //计算发起该话题用户数量
                if(in_array($one_topic['id'], $topics)){
                        $n++;
                    }

            }
            //存入数据
                $topic_title_end[$key] = array(
                    'topic_id'      =>  $one_topic['id'],
                    'topic_title'   =>  $one_topic['title'],
                    'topic_num'     =>  $n,
                    'promote'     =>  $one_topic['promote']
                    );
        }
        for ($i=0; $i < count($topic_title_end)-1 ; $i++) {
            if($topic_title_end[$i]['promote'] != "1"){
                for ($j=$i+1; $j < count($topic_title_end); $j++) {
                    if($topic_title_end[$i]['topic_num'] < $topic_title_end[$j]['topic_num'] ){
                        $arr = $topic_title_end[$j];
                        $topic_title_end[$j] = $topic_title_end[$i];
                        $topic_title_end[$i] = $arr;
                    }
                }
            }
        }
        foreach ($topic_title_end as $key => $topic_one) {
            if(!empty($_GET['type'])){
                if($_GET['type'] == 2){
                    if($topic_one['topic_num'] > 0){
                        unset($topic_title_end[$key]);
                    }
                }else{
                    if($topic_one['topic_num'] == 0){
                        unset($topic_title_end[$key]);
                    }
                }
            }
        }
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page(count($topic_title_end),$listRows,$linkFront);
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign("topics", array_slice($topic_title_end, (($_GET['p']-1)*20), 20));
        $this->assign('topic_name',$topic_name);
        $this->assign('type',$type);
        $this->display();
    }
    function searchTpic(){

    }
    /**
     *
     */
    public function addtopic(){
        $this->display();
    }
    public function doAddTopic(){
        if(empty($_POST['topic_title'])){
            $this->error("请输入有效的话题名称");
        }
        if(strlen($_POST['topic_title']) > 16){
            $this->error("话题长度不允许超过16个字");
        }
        $topic_title = $_POST['topic_title'];
        $topic = M('topic');
        if($topic->where("title = '".$topic_title."'")->select()>0){
            $this->error("添加推广话题失败，该话题已存在...");
        }
        $data = array(
            'title' =>  $topic_title,
            'uid'   =>  $_SESSION["adminid"],
            'createtime'    =>  time(),
            'promote'   =>  '1'
            );
        if($topic->data($data)->add()){
            $this->success("添加推广话题成功！");
        }else{
            $this->error("添加推广话题失败！");
        }
    }
    public function edit_topic(){
        if(!isset($_GET['id'])){
            $this->error("参数错误!");
        }
        $topic = M('topic')->where('id = '.$_GET['id'])->select();
        if($topic == 0){
            $this->error("参数错误!");
        }
        $this->assign("topic", $topic[0]);
        $this->display();
    }
    public function doEditTopic(){
        if(!isset($_POST['topic_id']) || !isset($_POST['topic_title'])){
            $this->error("请输入有效的话题名称");
        }
        if(empty($_POST['topic_title'])){
            $this->error("请输入有效的话题名称");
        }
        $topic_title = $_POST['topic_title'];
        $topic = M('topic');
        $this_topic = $topic->where("title = '".$topic_title."'")->select();
        if( $this_topic>0){
            if($this_topic[0]['id'] != $_POST['topic_id']){
                $this->error("修改推广话题失败，该话题已存在...");
            }else{
                $this->error("没有修改...");
            }
        }
        $data = array(
            'id'    =>  $_POST['topic_id'],
            'title' =>  $topic_title,
            'uid'   =>  $_SESSION["adminid"],
            'createtime'    =>  time(),
            'promote'   =>  '1'
            );
        if($topic->save($data)){
            $this->success("修改推广话题成功！");
        }else{
            $this->error("修改推广话题失败！");
        }
    }
    public function delTopic(){
        if(!isset($_GET['id'])){
            $this->error("参数错误!");
        }
        $id = $_GET['id'];
        $data = array(
            'id'    =>  $id,
            'promote'   => '0'
            );
        if(M('topic')->save($data)){
            $this->success("取消推广话题成功！");
        }else{
            $this->error("取消推广话题失败！");
        }
    }
    public function onlineTopic(){
        if(empty($_GET['online'])){
            $online = "off";
        }else{
            $online = "on";
        }
        if($online == "off"){
            if(!empty($_GET['title'])){
                $data['title'] = array("like","%".$_GET['title']."%");
            }
            $topic = M('topic')->where($data)->select();

            foreach ($topic as $key => $one_topic) {
                $n = 0;
                foreach ($backstream as $one_backstream) {
                    $topics = explode(",", $one_backstream['topics']);
                    //计算发起该话题用户数量
                    if(in_array($one_topic['id'], $topics)){
                            $n++;
                        }
                    //一共有几个匹配到的话题
                    if(!in_array($one_topic['title'], $topic_title)){
                        if(in_array($one_topic['id'], $topics)){
                            $topic_title[++$i] = $one_topic['title'];
                        }
                    }
                }
            }

            $this->assign("topic",$topic);
            $this->display();
        }else{
            if(!empty($_GET['title'])){
                $where['title']=array('like','%'.$_GET['title'].'%');
                $where['promote'] = "1";
                $where['_logic'] = "or";
                $topic = M('topic')->where($where)->select();
            }
            $backstream = M('backstream')->where('streamstatus = "1"')->select();
            $all_data = array();
            $topic_title = array();
            $i = 0;
            foreach ($topic as $key => $one_topic) {
                $n = 0;
                foreach ($backstream as $one_backstream) {
                    $topics = explode(",", $one_backstream['topics']);
                    //计算发起该话题用户数量
                    if(in_array($one_topic['id'], $topics)){
                            $n++;
                        }
                    //一共有几个匹配到的话题
                    if(!in_array($one_topic['title'], $topic_title)){
                        if(in_array($one_topic['id'], $topics)){
                            $topic_title[++$i] = $one_topic['title'];
                        }
                    }
                }
                if($one_topic['promote'] == "1"){
                    $temp = array(
                        'id'      =>  $one_topic['id'],
                        'title'   =>  $one_topic['title'],
                        'promote'   =>  $one_topic['promote'],
                        'user_num'     =>  $n
                        );
                    array_unshift($topic_title_end,$temp);
                }else{
                    //存入数据
                    if($n != 0){
                        $topic_title_end[] = array(
                            'id'      =>  $one_topic['id'],
                            'title'   =>  $one_topic['title'],
                            'promote'   =>  $one_topic['promote'],
                            'user_num'     =>  $n
                            );
                    }
                }
                if($n != 0){
                    if($i == $n){
                        break;
                    }
                }
            }
            $this->assign("topic",$topic_title_end);
            $this->display();
        }
    }

    /**
     * 私密类型设置文轩
     */
    public function privatetype(){
          $private_sel=M('privatetype');
          $datas=$private_sel->select();
          $this->assign('privates',$datas);
          $this->display();
    }

    public function private_del(){
        if(!isset($_GET['d_id'])){
            $this->error('操作失败！');
        }else{
            $data['id']=  $_GET['d_id'];
            $delete=M('privatetype');
            if($delete->where($data)->delete()){
                 $this->success('该私密类型已被删除！');
            }else{
                $this->error('该私密类型删除失败！');
            }
        }
    }

    public function add_display(){
              $this->display('add_private');
    }
    public function add_private(){
        if(isset($_POST['private_title'])){
            if (empty($_POST['private_title'])) {
                $this->error('请输入私密类型名称！');
            }
            $data=$_POST['private_title'];
            $datas['name']=$data;
            $private=M('privatetype');
            if($private->data($datas)->add())
            {
                $this->success('添加私密类型成功！');
            }else{
                $this->error('很抱歉！你添加的私密类型失败！');
            }
        }
    }

    public function private_update(){
        if(isset($_GET['u_id'])){
            $data_id['id']= $_GET['u_id'];
            $datas=M('privatetype')->where($data_id)->find();

            $this->assign('private_data',$datas);
            $this->display();
        }

        if(isset($_POST['private_id'])&&!empty($_POST['private_name']))
        {   $data_update['id']=$_POST['private_id'];
            $data_update['name']=$_POST['private_name'];
            $update=M('privatetype');
            if($update->data($data_update)->save()){
                $this->success('私密类型已更改！');
            }else{
                $this->error('私密类型更改失败！');
            }
        }
    }


    /**
     * 机器人数据服务据列表——----------------------文轩
     */
    public function robotlist(){
        $condition = 'isdelete="n" and is_robot = 1 ';
        
        if(!empty($_GET['start_time'])){
            $condition .= ' and regtime>='.strtotime($_GET['start_time']);
        }
        if(!empty($_GET['end_time'])){
            $condition .= ' and regtime<='.strtotime($_GET['end_time']+1);
        }
        if(!empty($_GET['keyword']) && $_GET['keyword'] != '请输入用户ID或用户名'){
            if(preg_match("/^\d*$/",$_GET['keyword'])){
                $condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\' or nickname like \'%'.$_GET['keyword'].'%\')';
            }
            else{
                $condition .= ' and (username like \'%'.$_GET['keyword'].'%\'  or nickname like \'%'.$_GET['keyword'].'%\') ';
            }
        }
        if(!empty($_GET['sign'])){
            $condition .= ' and sign="'.$_GET['sign'].'"';
        }
        if(!empty($_GET['emceeagent'])){
            $condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
        }
        if(!empty($_GET['payagent'])){
            $condition .= ' and payagent="'.$_GET['payagent'].'"';
        }                
        $orderby = 'id desc';
        $robot_member = D("Member");
        $count = $robot_member->where($condition)->count();
        $listRows = 20;
        $linkFront = '';
        import("@.ORG.Page");
        $p = new Page($count,$listRows,$linkFront);

        $robot_members = $robot_member->limit($p->firstRow.",".$p->listRows)->where( $condition)->order($orderby)->select();

        // echo $member->getLastSql();
        $p->setConfig('header','条');
        $page = $p->show();
        $this->assign('page',$page);
        $this->assign('members',$robot_members);
            $this->display();

    }

    public function robot_more_delete(){
        if(is_array($_REQUEST['ids'])){
            foreach($_REQUEST['ids'] as $id){
                $condition .= " id = ".$id." or ";
            }
            $condition = substr($condition ,0 , -3);
            $result=M('member')->where($condition)->delete();
            if($result >= 0){
                $this->success('批量删除操作成功!');
            }else{ 
                $this->error('批量删除失败'); 
            }
        }
    }

    public function add_robot(){
         $this->display();
    }
    /**
     * 添加机器人信息w
     */
    public function do_addrobot(){
         //include APP_PATH.'config.inc.php';
        $User=D("Member");
        $data['is_robot'] = 1;
        $User->create();
        $User->username = $_POST['username'];
        $User->nickname = $_POST['username'];
        $User->password = md5($_POST['password']);
        $User->email = $_POST['email'];

        $User->regtime = time();
        $roomnum = 99999;
        do {
            $roomnum = rand(1000000000,1999999999);
        } while ($this->checkIt($roomnum)=='');
        $User->curroomnum = $roomnum;
        $defaultserver = D("Server")->where('isdefault="y"')->select();
        if($defaultserver){
            $User->host = $defaultserver[0]['server_ip'];
        }
        $userId = $User->add();
        D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values('.$userId.','.$roomnum.','.time().')');

        $member=M("Member");
        $is_robot['id'] =$userId ;
        $is_robot['is_robot'] =1 ;
        if($member->save($is_robot)){
            
            $this->success('添加机器人成功');
       // $this->assign('jumpUrl',__URL__.'/robotlist/');
        };
    }    

    public function robot_del(){
           if(isset($_GET['robot_id'])&&$_GET['robot_id']!=''){
              $data['id']=$_GET['robot_id'];
              $robot_member=M("Member");
             if(!($robot_member->where($data)->delete())){
                 $this->error('删除失败@！');
             }else{
                 $this->success('删除机器人成功');
             }

           }else{
                 $this->error('操作失败！');
           }

    }
    /**
     * 
     * 微信结算统计报表
     * 
     */
   public function wxtable()
    {
        if($_GET['start_time'] != ''){
            $timeArr = explode("-", $_GET['start_time']);
            $starttime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
        }else{
            $starttime = 0;
        }
        if($_GET['end_time'] != ''){
            $timeArr = explode("-", $_GET['end_time']);
            $endtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
        }else{
            $endtime = time();
        }
        if($_GET['status'] != ''){
            $status = "and earn.status = '".$_GET['status']."'";
        }else{
            $status = '';
        }

        $data = M('Earncash')->query("select earn.id,earn.uid,earn.cash,earn.time,earn.status,earn.type,mem.username,mem.nickname from ss_earncash as earn,ss_member as mem where earn.time>={$starttime} and earn.time<={$endtime} and earn.uid=mem.id and earn.type = '2'".$status);
        if (!$data) {
            $data = array();
        }else{
            foreach ($data as $k => $val) {
                $data[$k]['time'] = date('Y-m-d h:i:s',$val['time']);
            }
        }
        $this->assign('history', $data);
        $this->display();
    }


    /**
     * 
     * 支付宝结算统计报表
     * 
     */
    public function alitable()
    {
        // TODO  分页
        $starttime = 0;
        $endtime = time();
        $data = M('Earncash')->query("select earn.id,earn.uid,earn.cash,earn.time,earn.status,earn.type,mem.username,mem.nickname from ss_earncash as earn,ss_member as mem where earn.time>={$starttime} and earn.time<={$endtime} and earn.uid=mem.id and earn.type = '1'");

        if (!$data) {
            $data = array();
        }
        foreach ($data as $k => $val) {
            $data[$k]['time'] = date('Y-m-d h:i:s',$val['time']);
        }
        $this->assign('history', $data);
        $this->display();
    }



    //微信支付
    //
    /*
    *微信提现
    */
    // private static $configATMWeiXin = array();
    // public function __construct()
    // {
    //     require_once APP_PATH . 'config.inc.php';
    //     static::$configATMWeiXin = $atm_weixin;
    // }

    public function wxpay(){
        $id = $_POST['id'];
        if( empty($id) && isset($id) ){
            echo "参数错误";
        }
        require_once APP_PATH . 'config.inc.php';
        $configATMWeiXin = $atm_weixin;
        // $configATMWeiXin = $atm_weixin;  
        // dump($atm_weixin);
        // exit;
        $earncash =  M('earncash')->where('id = '.$id)->field('uid, cash')->find();
        $openid = M('member')->where('id = '.$earncash['uid'])->getField('wxopenid');
        dump($this->appWeixinCash($id, $earncash['uid'], $earncash['cash'], $openid, null,  $configATMWeiXin));
    }
    //微信支付
    //
    /*
    *微信提现(红包支付模式)
    */
    public function wxpayredpack(){
        $id = $_POST['id'];
        if( empty($id) && isset($id) ){
            echo "参数错误";
        }
        require_once APP_PATH . 'config.inc.php';
        $configATMWeiXin = $atm_weixin;
        // $configATMWeiXin = $atm_weixin;  
        // dump($atm_weixin);
        // exit;
        $earncash =  M('earncash')->where('id = '.$id)->field('uid, cash')->find();
        $openid = M('member')->where('id = '.$earncash['uid'])->getField('wxopenid');
        echo $this->sentHB($id, $earncash['uid'], $earncash['cash'], $openid,$configATMWeiXin);
        // if( empty($id) && isset($id) ){
        //     echo "参数错误";
        // }
        // require_once APP_PATH . 'config.inc.php';
        // $configATMWeiXin = $atm_weixin;
        // // $configATMWeiXin = $atm_weixin;  
        // // dump($atm_weixin);
        // // exit;
        // $earncash =  M('earncash')->where('id = '.$id)->field('uid, cash')->find();
        // $openid = M('member')->where('id = '.$earncash['uid'])->getField('wxopenid');
        // dump($this->appWeixinCash($id, $earncash['uid'], $earncash['cash'], $openid, null,  $configATMWeiXin));
    }


    /**
    * 微信APP提现接口
    *
    * @param string $token
    * @param int    $cash  金额，RMB
    * @param string    $openid  授权获得openid，必须
    * @param string    $realname    提现是输入的真实姓名，必须与实名认证一致,可不填
    */
    private function appWeixinCash($id ,$uid = 0, $cash = 0, $openid = null, $realname = null, $configATMWeiXin)
    {
        $cash = 1;
        $realname = "123";

        $cash = $cash * 100;
        $result = $this->initCashOrderData($openid, $realname, $cash, $uid, $configATMWeiXin);
        // return $result;
        $xml = $this->postXmlCurl($result, $configATMWeiXin, true);
        $data = $this->xmlToArr($xml);
        if($data['RETURN_CODE'] == 'FAIL'){
            return $data['RETURN_MSG'];
        }
        if($data['RESULT_CODE'] == 'SUCCESS'){
            $earncash_data = array(
                'id' => $id,
                'checktime'  =>  $data['PAYMENT_TIME'],
                'status'    =>  '审核通过',
                'selfOrderNo'   =>  $data['PARTNER_TRADE_NO'],
                'weixinOrderNo' =>   $data['PAYMENT_NO'],
            );
            $updateEarncash = M('earncash')->save($earncash_data);
            return "提现成功";
        }else{
            return $data['RETURN_MSG'];
            // $this->responseError($data['ERR_CODE_DES']);
            //正式启用错误信息反馈
            $arr = array(
                'AMOUNT_LIMIT','NAME_MISMATCH','NOAUTH','PARAM_ERROR'
            );
            if(in_array($data['RETURN_CODE'],$arr)){
                return $data['RETURN_MSG'];
            }else{
                return "异常错误，请告诉开发人员发生了错误";
            }
            return $data['RETURN_MSG'];

        }

    }
    /**
    *   初始化提现数据
    *                               ----------------------------
    */
    protected function initCashOrderData($openid, $realname, $cash, $uid, $configATMWeiXin)
    {
        $nonce_str = $this->getRandomStr();
        // return $configATMWeiXin['CHECK_NAME'];
        $param = array(
            'amount' => $cash,
            'check_name'=> $configATMWeiXin['CHECK_NAME'],
            'desc'=>'美丽播用户微信提现',
            'mch_appid'=> $configATMWeiXin['APPID'],
            'mchid'=> $configATMWeiXin['MCHID'],
            'nonce_str'=> $nonce_str ,
            'openid'=> $openid,
            'partner_trade_no'=> $this->createOrderNo($uid),
            're_user_name'=> $realname,
            'spbill_create_ip'=>$_SERVER["REMOTE_ADDR"],
            );
        $str = $this->arrayToKeyValueString($param);
        // return $str;
        $param['sign'] = $this->getSign($str, $configATMWeiXin);
        return $this->arrToXML($param);
    }
    protected function createOrderNo($uid){
        do {
            $orderNo = date('YmdHis').$uid.rand(100,999);
            // return ($orderNo);
        }while(count(M('earncash')->where('selfOrderNo="'.$orderNo.'"')->select()) > 0 );
        return $orderNo;
    }
    /**
    * 数组转XML
    */
    protected function arrToXML($param, $cdata = false)
    {
        $xml = "<xml>";
        $cdataPrefix = $cdataSuffix = '';
        if ($cdata) {
            $cdataPrefix = '<![CDATA[';
            $cdataSuffix = ']]>';
        }

        foreach($param as $key => $value) {
            $xml .= "<{$key}>{$cdataPrefix}{$value}{$cdataSuffix}</$key>";
        }
        $xml .= "</xml>";

        return $xml;
    }

    /**
    * XML转数组
    * 数组格式 array('大写xml的tag'    =>  'xml的value');
    * 数组所有键为大写！！！-----重要！
    */
    protected function xmlToArr($xml)
    {
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, $xml, $data, $index);
        $arr = array();
        foreach ($data as $key => $value) {
            $arr[$value['tag']] = $value['value'];
        }
        return $arr;
    }
    /**
    * 获取签名
    */
    private function getSign($str, $configATMWeiXin)
    {
        $str = $this->joinApiKey($str, $configATMWeiXin);
        return strtoupper(md5($str));
    }
    /**
    * 拼接API密钥
    *                               ----------------------------
    */
    protected function joinApiKey($str, $configATMWeiXin)
    {
        return $str . "key=".$configATMWeiXin['APIKEY'];
        // return $str . "key=D4FF6168E5DC4452A46364ACF842301B";
    }
    protected function arrayToKeyValueString($param)
    {
        $str = '';
        foreach($param as $key => $value) {
            $str = $str . $key .'=' . $value . '&';
        }
        return $str;
    }
    protected function getRandomStr()
    {
        return md5('meilibo' . microtime() . 'weixin' . rand(100,9999));
    }
    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     *                              ----------------------------
     */
    private static function postXmlCurl($xml, $configATMWeiXin, $useCert = false, $second = 30)
    {

        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        /*
        if(static::$configWeiXin['CURL_PROXY_HOST'] != "0.0.0.0" && static::$configWeiXin['CURL_PROXY_PORT'] != 0){
            curl_setopt($ch,CURLOPT_PROXY, static::$configWeiXin['CURL_PROXY_HOST']);
            curl_setopt($ch,CURLOPT_PROXYPORT, static::$configWeiXin['CURL_PROXY_PORT']);
        }
        */
        if($useCert == true){
            curl_setopt($ch,CURLOPT_URL,  $configATMWeiXin['CASH_HTTPS']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST" );
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $configATMWeiXin['SSLCERT_PATH']);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $configATMWeiXin['SSLKEY_PATH']);
        }else{
            curl_setopt($ch,CURLOPT_URL, $configWeiXin['PLACE_ORDER']);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
            //post提交方式
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //设置header
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
        }
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            // throw new Exception("curl出错，错误码:$error");
            return "<xml><return_code>FAIL</return_code><return_msg>系统错误，请稍后重试</return_msg></xml>";
        }
    }

//视频设置
    public function videosys(){
        $this->assign("videolist", M('videolist')->order("id desc")->select());
        $this->display();
    }
    public function save_video(){
        if(!empty($_POST['uid'])){
            M('videolist')->where('id ='.$_POST['uid'])->delete(); 
            echo json_encode('ok');die;
        }
        $data['uid'] = $_POST['uid'];
        $data['roomid'] = $_POST['roomid'];
        $data['address'] = $_POST['address'];
        $data['status'] = $_POST['status'];

        if($data['roomid']){
            $this->error('房间号不能为空');
        }
        $info = M('member')->where('curroomnum = '.$data['roomid'])->find();
        if(empty($info)){
            $this->error('无效的房间号');
        }
        $video = M('videolist');
        $id = $video->where('uid ='.$info['id'])->save($data);
        if($id){
            $this->success('成功插入一条数据');
        }else{
            $this->success('操作数据库失败');
        }
    }
    public function addvideo(){
        $roomid = $_POST['add_roomid'];
        $videourl = $_POST['add_videourl'];
        $status = $_POST['add_status'];
        if(empty($roomid)){
            $this->error('房间号不能为空');
        }
        $info = M('member')->where('curroomnum = '.$roomid)->find();
        if(empty($info)){
            $this->error('无效的房间号');
        }
        $video = M('videolist');
        $video->uid = $info['id'];
        $video->roomid = $roomid;
        $video->address = $videourl;
        $video->status = $status;
        $id = $video->add();
        if($id){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }
    //视频保存上传
    // public function save_video(){
    //     $accessKey = C('QINIU.ACCESS_KEY');
    //     $secretKey = C('QINIU.SECRET_KEY');

    //     // 构建鉴权对象
    //     $auth = new Auth($accessKey, $secretKey);

    //     // 要上传的空间
    //     $bucket = 'meilibo-video';
    //     $filePath = $_FILES['video']['tmp_name'];

    //     $data['title'] = $_POST['add_title'];
    //     // $data['topics'] = $_POST['add_topics'];
    //     $data['status'] = $_POST['add_status'];
    //     $data['uid'] = $_POST['add_uid'];
    //     $data['roomid'] = M('member')->where('id='.$data['uid'])->getField('curroomnum');
    //     if( !empty($_POST['add_uid']) ){
    //         if( empty($data['roomid']) ){
    //             $this->error("该用户不存在");
    //         }


    //         // 上传到七牛后保存的文件名
    //         $key = time().'.mp4';
    //         // 初始化 UploadManager 对象并进行文件的上传。
    //         $uploadMgr = new UploadManager();

    //         $pfop = "avthumb/mp4";

    //         $policy = array(
    //             'scope' => $bucket.':'.$_FILES['video']['name'],
    //             'persistentOps' => $pfop,
    //         );
    //         $token = $auth->uploadToken($bucket, null, 2592000, $policy);

    //         list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
    //         if ($err !== null) {
    //             var_dump($err);
    //         } else {

    //             $config = M('Siteconfig')->find();
    //             $domain = !empty($config['videourl']) ? $config['videourl'] : 'http://www.meilibo.net';
    //             $data['address'] = $domain . $key;
    //             $data['topics'] = $this->addTopics("#".$_POST['add_topics']."#", $data['uid']);
    //             M('videolist')->add($data);
    //         }
    //     }

    //     $Edit_ID = $_POST['id'];
    //     $Edit_Title = $_POST['title'];
    //     $Edit_Topics = $_POST['topics'];
    //     // $Edit_Status = $_POST['status'];
    //     $Edit_Uid = $_POST['uid'];
    //     $Edit_DelID = $_POST['ids'];

    //     //删除操作
    //     $num = count($Edit_DelID);
    //     for($i=0;$i<$num;$i++)
    //     {
    //         $address = M('videolist')->where('id='.$Edit_DelID[$i])->getField('address');
    //         //初始化BucketManager
    //         $bucketMgr = new BucketManager($auth);

    //         //你要测试的空间， 并且这个key在你空间中存在
    //         $filename = substr($address, strrpos($address,"/")+1);

    //         D("videolist")->where('id='.$Edit_DelID[$i])->delete();
    //         //删除$bucket 中的文件 $key
    //         $err = $bucketMgr->delete($bucket, $filename);
    //     }
    //     //编辑
    //     $num = count($Edit_ID);
    //     for($i=0;$i<$num;$i++)
    //     {
    //         D("videolist")->execute('update ss_videolist set title="'.$Edit_Title[$i].'",uid="'.
    //             $Edit_Uid[$i].'",topics="'.$Edit_Topics[$i].'",status="'.$_POST['status'.$Edit_ID[$i]].'" where id='.$Edit_ID[$i]);
    //     }

    //     $this->assign('jumpUrl',__URL__."/videosys/");
    //     $this->success('操作成功');

    // }

    protected function addTopics( $string = NULL , $uid = 0){
        $time = time();
        $status = 0;
        if($string != NULL){
           
            $arr = array();
            $string = htmlspecialchars_decode($string);  //解码
            preg_match_all('/\#([^\#]+?)\#/',$string,$arr); // 依照正则表达式替换出 #???# 格式的话��
            foreach($arr[0] as $temparr){
                $string = str_replace($temparr,"",$string);
            }
            if(strlen($string) < 1){
                $string = " ";
            }
            $topicList = $arr[1]; // 得到已剔��号的话题名称
            $topicList = array_unique($topicList);


            //拼装查询现有话题的名称
            $key = "";
            foreach($topicList as $topic){
                $key .= $topic.",";

            }
            $key = substr($key,0,-1);
            $haveListdata['title'] = array("in",$key);
            //查询数据库中已有的话题，并删除掉数组中的这个话题
            $haveList = M("topic")->where($haveListdata)->select();
            if($haveList != NULL){
                $ids = "";
                foreach($haveList as $have){
                    if(in_array($have['title'],$topicList)){
                        $ids .= $have['id'] .",";
                        array_splice($topicList,$arrayKey,1); //从数组中移除该话��remove topic for this array
                    }
                }
            }
            //将新建的话题添加进数据库 add new topic to database
            if(count($topicList) > 0){
                foreach($topicList as $temp){
                    $data['title'] = $temp;
                    $data['uid'] = $uid;
                    $data['createtime'] = $time;
                    $id = M("topic")->data($data)->add();
                    $ids .= $id . ",";
                }
            }//这里是添加回放链接
            $ids = substr($ids,0,-1);
            return $ids;

        }

    }
    //微信企业红包方式结算
    public function sentHB($id,$uid = 0,$money = 0,$openid = '',$configATMWeiXin){
        //必填
        $Parameters['mch_billno'] = 'saso'.time().rand(10000, 99999);//商户订单号
        $Parameters['mch_id'] = $configATMWeiXin['MCHID'];//微信支付分配的商户号
        $Parameters['wxappid'] = $configATMWeiXin['APPID'];//商户appid
        $Parameters['nick_name'] = '美丽播直播';//提供方名称
        $Parameters['send_name'] = '美丽播';//红包发送者名称
        $Parameters['re_openid'] = $openid;//接受收红包的用户，用户在wxappid下的openid
        $Parameters['total_amount'] = $money;//付款金额，单位分
        $Parameters['min_value'] = $money;//最小红包金额，单位分
        $Parameters['max_value'] = $money;//最大红包金额，单位分
        $Parameters['total_num'] = 1;//红包发放总人数
        $Parameters['wishing'] = '恭喜发财！';//红包祝福语
        $Parameters['client_ip'] = $_SERVER['SERVER_ADDR'];//调用接口的机器Ip地址
        $Parameters['act_name'] = '红包提现';//活动名称
        $Parameters['remark'] = '请尽快领取,逾期平台不负责.若12小时内未收到红包请联系客服';//备注信息
        $Parameters['nonce_str'] = $this->createNoncestr();//随机字符串，不长于32位
        //非必填
        //$Parameters['logo_imgurl'] = '商户logo的url';//商户logo的url
        //$Parameters['share_content']= '备注信息';//分享文案
        //$Parameters['share_url']= '备注信息';//分享链接
        //$Parameters['share_imgurl']= '备注信息';//分享的图片url
        $Parameters['sign'] = $this->getRedSign($Parameters);//签名

        $xml = $this->arrayToXml($Parameters);
        $res = $this->postXmlSSLCurl($xml,'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack');
        $return = $this->xmlToArray($res);
        
        file_put_contents('./logs/HB_'.date('Ymd').'.log', "===================================================================================\r\n".date('Y-m-d H:i:s')."\r\n".$res."\r\n\r\n",FILE_APPEND);
        if($return['return_code'] == 'SUCCESS' && $return['result_code'] == 'SUCCESS'){
            echo $return;
            //echo '{"code":"1","msg":"发送成功！"}';
        }else{
            echo '{"code":"-5","msg":"'.$return['return_msg'].'"}';
        }
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) 
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    /**
     *  作用：生成签名
     */
    public function getRedSign($Obj){
        foreach ($Obj as $k => $v)
        {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$this->KEY;
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    public function createNoncestr( $length = 32 ) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }  
        return $str;
    }

    /**
     *  作用：array转xml
     */
    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
             if (is_numeric($val))
             {
                $xml.="<".$key.">".$val."</".$key.">"; 

             }
             else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }

    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml)
    {       
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);      
        return $array_data;
    }

    /**
     *  作用：使用证书，以post方式提交xml到对应的接口url
     */
    public function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, $configATMWeiXin['SSLCERT_PATH']);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, $configATMWeiXin['SSLKEY_PATH']);
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else { 
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>"; 
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

    // 系统客服
    public function customer_service(){
        $this->display();
    }

    // 统计数据
    public function statistics( $channel = "all" ){
        if( $channel != "all" ){
            $condition = "terminal = ".$channel;
        }else{
            $condition = " 1 ";
        }
        $array = array(
            'register' => M('member')->where( $condition." and regtime >= ".strtotime( date("Y-m-d",strtotime("-90 day") ) ) )->getField('id, regtime'),
            'login' => M('loginrecord')->where($condition." and logintime >= ".strtotime( date("Y-m-d",strtotime("-90 day") ) ) )->getField('uid, logintime'),
        );
        $array['register'] = $this->formatKeepUser( $array['register'] );
        $array['login'] = $this->formatKeepUser( $array['login'] );
        $data = $this->keepUserRate( $array['login'], $array['register'], $channel );
        $this->assign('statistics', $data);
        $this->display();
    }
    // 统计数据
    public function rechargerate( $channel = "all" ){
                if( $channel != "all" ){
            $condition = "terminal = ".$channel;
        }else{
            $condition = " 1 ";
        }
        $array = array(
            'register' => M('member')->where( $condition." and regtime >= ".strtotime( date("Y-m-d",strtotime("-90 day") ) ) )->getField(' id, regtime'),
            'charge' => M('chargedetail')->where($condition." and status = '1' and addtime >= ".strtotime( date("Y-m-d",strtotime("-90 day") ) ) )->getField(' id, uid, rmb, addtime'),
            'login' => M('loginrecord')->where($condition." and logintime >= ".strtotime( date("Y-m-d",strtotime("-90 day") ) ) )->getField('uid, logintime'),

        );
        $array['register'] = $this->formatKeepUser( $array['register'] );
        $array['charge'] = $this->formatCharge( $array['charge'] );
        $array['login'] = $this->formatKeepUser( $array['login'] );
        $data = $this->chargeRate( $array['charge'], $array['register'], $channel , $array['login']);
        $this->assign('rechargerates', $data);
        $this->display();
    }

    /**
     * 格式化留存用户数据
     * @param  array $data 数据源
     * @return array       已格式化的数据
     */
    private function formatKeepUser( $data ){
        $regArray = array();
        if( count( $data ) > 0 ){
            foreach ($data as $key => $value) {
                $date = date("Y-m-d", $value );
                if( !in_array( $key, $regArray[$date] ) ){
                    $regArray[$date][] = $key;
                }
            }
        }
        return $regArray;
    }
    /**
     * 格式化留存用户数据
     * @param  array $data 数据源
     * @return array       已格式化的数据
     */
    private function formatCharge( $data ){
        $regArray = array();
        if( count( $data ) > 0 ){
            foreach ($data as $key => $value) {
                $date = date("Y-m-d", $value['addtime'] );
                array_key_exists( $date, $regArray) ? true : $regArray[$date]['rmb'] = 0;

                $regArray[$date]['users'][] = $value['uid'];
                $regArray[$date]['rmb'] += $value['rmb'];
                
            }
        }
        return $regArray;
    }

    private function chargeRate( $charge, $register, $channel, $login ){
        for( $i = 0; $i > -90; $i-- ){
            $today = date( 'Y-m-d', strtotime( $i." day") );
            $todayReg = count( $register[$today] );
            $chargeUser = count( array_unique($charge[$today]['users']) );
            $data[] = array(
                'date' => $today,
                'channel' => $channel,
                'todayReg' => $todayReg,
                'activeUser' => count( $login[$today] ),
                'chargeCount' => count( $charge[$today]['users'] ),
                'chargeUser' => $chargeUser ,
                'rmb' => $charge[$today]['rmb'] ? $charge[$today]['rmb'] : 0,
                'chargeRate' => round( $chargeUser / count( $login[$today] ) * 100 , 2 )."%",
                'ARPU' => round( $charge[$today]['rmb'] / $chargeUser ),
                );
        }
        return $data;
    }
    private function keepUserRate( $login, $register, $channel ){
        for( $i = 0; $i > -90; $i-- ){
            $today = date( 'Y-m-d', strtotime( $i." day") );
            $day1 = date( 'Y-m-d', strtotime( ($i+1)." day") );
            $day3 = date( 'Y-m-d', strtotime( ($i+3)." day") );
            $day7 = date( 'Y-m-d', strtotime( ($i+7)." day") );
            $day15 = date( 'Y-m-d', strtotime( ($i+15)." day") );
            $day30 = date( 'Y-m-d', strtotime( ($i+30)." day") );
            $todayReg = count( $register[$today] );
            $day1Count = count( array_intersect( $login[$day1], $register[$today] ) );
            $day3Count = count( array_intersect( $login[$day3], $register[$today] ) );
            $day7Count = count( array_intersect( $login[$day7], $register[$today] ) );
            $day15Count = count( array_intersect( $login[$day15], $register[$today] ) );
            $day30Count = count( array_intersect( $login[$day30], $register[$today] ) );
            $data[] = array(
                'date' => $today,
                'todayReg' => $todayReg,
                'channel' => $channel,
                'day1Count' => $day1Count,
                'day3Count' => $day3Count,
                'day7Count' => $day7Count,
                'day15Count' => $day15Count,
                'day30Count' => $day30Count,
                'day1rate' => round( $day1Count/$todayReg * 100 , 2 )."%",
                'day3rate' => round( $day3Count/$todayReg * 100 , 2 )."%",
                'day7rate' => round( $day7Count/$todayReg * 100 , 2 )."%",
                'day15rate' => round( $day15Count/$todayReg * 100 , 2 )."%",
                'day30rate' => round( $day30Count/$todayReg * 100 , 2 )."%",
                );
        }
        return $data;
    }

    // 举报列表
    public function listreport(){
        $count = M('report')->join(" ss_member on ss_member.id = ss_report.accused ")->group("bsid")->count();
        import("@.ORG.Page");
        $p = new Page($count,20);
        $p->setConfig('header','条');
        $page = $p->show();
        $reports = M('report')
        ->join(" ss_member on ss_member.id = ss_report.accused ")
        ->field(" count(ss_report.id) count, ss_report.id rid, ss_member.id uid, ss_member.username username, ss_member.nickname nickname, ss_member.sign sign, ss_member.isaudit isaudit, ss_report.time time")
        ->group("accused")
        ->order(" time desc")
        ->limit($p->firstRow.",".$p->listRows)
        ->select();
        $this->assign("page", $page);
        $this->assign("reports", $reports);
        $this->display();
    }

    public function listcharge(){
        $charges = D("charge")->order('chargeid asc')->select();
        $this->assign("charges",$charges);
        $this->display();
    }

    public function save_charge()
    {
        $Edit_ID = $_POST['id'];
        $Edit_chargeid = $_POST['chargeid'];
        $Edit_rmb = $_POST['rmb'];
        $Edit_diamond = $_POST['diamond'];
        $Edit_present = $_POST['present'];
        $Edit_DelID = $_POST['ids'];

        //删除操作
        $num = count($Edit_DelID);
        for($i=0;$i<$num;$i++)
        {
            D("charge")->where('id='.$Edit_DelID[$i])->delete();
        }
        //编辑
        $num = count($Edit_ID);
        for($i=0;$i<$num;$i++)
        {
            D("charge")->execute('update ss_charge set chargeid='.$Edit_chargeid[$i].',rmb="'.$Edit_rmb[$i].'",diamond='.$Edit_diamond[$i].',present='.$Edit_present[$i].' where id='.$Edit_ID[$i]);
        }

        if($_POST['add_chargeid'] != '' && $_POST['add_rmb'] != '' && $_POST['add_diamond'] != '' && $_POST['add_present'] != ''){
            $Charge = D('charge');
            $Charge->create();
            $Charge->chargeid = $_POST['add_chargeid'];
            $Charge->rmb = $_POST['add_rmb'];
            $Charge->diamond = $_POST['add_diamond'];
            $Charge->present = $_POST['add_present'];
            $Chargeid = $Charge->add();
        }

        $this->assign('jumpUrl',__URL__."/listcharge/");
        $this->success('操作成功');
    }
    //专属礼物列表
    public function gifttable(){
        $start_day = $_GET['start_time'];
        $end_day = $_GET['end_time'];
        if($start_day!=null && $end_day!=null){
            $start_time=date_create_from_format('Y-m-d H:i:s',$start_day.' 00:00:00')->getTimestamp();
            $end_time=date_create_from_format('Y-m-d H:i:s',$end_day.' 00:00:00')->getTimestamp();
            $data['create_time'] = array(['egt',$start_time],['lt',$end_time]);
        }
        $keyword = $_GET['keyword'];
        $data['status'] = $_GET['status'];
        if($data['status']==null){
            unset($data['status']);
        }
        $data['to_uid'] = array('like','%'.trim($keyword).'%');
        $list = M('Exclusivegift')->where($data)->select();
        $num = 0;
        foreach ($list as $key=>$value){
            $userinfo = M("Member")->where('id='.$value['uid'])->find();
            $anchorinfo = M("Member")->where('id='.$value['to_uid'])->find();
            $giftinfo = M("Gift")->where('id='.$value['gift_id'])->find();
            $list[$num]['user_nickname'] = $userinfo['nickname'];
            $list[$num]['anchor_nickname'] = $anchorinfo['nickname'];
            $list[$num]['giftname'] = $giftinfo['giftname'];
            $list[$num]['time'] = date('Y-m-d H:i:s',$value['create_time']);
            $list[$num]['giftcoin'] = $giftinfo['needcoin'];
            
            $gift_ratio = M('Giftsort')->where(['orderno'=>4])->find();
            $list[$num]['beannum'] = $giftinfo['needcoin']* $value['count'] * (int)$gift_ratio['ratio'] / 100;
            $num++;
        }
        $this->assign('list',$list);
        $this->display();
    }
    public function doGiftInfo(){
        $id = $_POST['id'];
        $data['content'] = $_POST['content'];
        $data['status'] = 1;
        $update = M('Exclusivegift')->where('id='.$id)->save($data);
        if($update){
            echo json_encode(['code'=>'0','message'=>'success']);
        }else{
            echo json_encode(['code'=>'1','message'=>'error']);
        }
    }
    public function findGiftInfo(){
        $id = $_POST['id'];
        $findinfo = M('Exclusivegift')->where('id='.$id)->find();
        if($findinfo){
            echo json_encode(['code'=>'0','message'=>'success','data'=>$findinfo['content']]);
        }else{
            echo json_encode(['code'=>'1','message'=>'error']);
        }
    }
}

?>
