<?php
class AppapiAction extends BaseAction{
    public $url="/";
        //输入房间号直达
    public function searchroom(){
        $roomnum=$_GET['roomnum'];
        $res=M("member")->where("curroomnum=".$roomnum)->select();
        $callb= $_GET['callback'];
        if($res==null){
            echo $callb."(".json_encode("0").")"; 
            exit;
        }else{
            echo $callb."(".json_encode("1").")"; 
            exit;
        }
    }
    
     //小编推荐
        public function  tuijianemcee(){
        $recommend = M("member")->where("recommend = 'y'")->order("id desc")->limit(8)->select();
        //查询出等级
        $a=0; 
        foreach($recommend as $vo){
            $emceelevel = getEmceelevel($vo['earnbean']);
            $recommend[$a]["emceelevel"]=$emceelevel[0]['levelid'];
            $a++;
        } 
        foreach($recommend as $vo){
                $face=$vo["bigpic"];
                
            if($face==null){
                $face="/style/bigpic/2015-07/55a1ed57d1821.jpg";
            }
             if($vo['virtualguest'] > 0){
            $gknums=$vo['online'] + $vo['virtualguest']+$virtualcount;}else{
                 $gknums=$vo['online'];}
            $tuijianstr.= "<li>
         <div class='emceeimg'><a href='show.html?roomid={$vo["curroomnum"]}'><img src='{$this->url}{$face}'><em class='islive'>直播</em></a></div>
         <div class='emceename'><p>{$gknums}人</P><a href='show.html?roomid={$vo["curroomnum"]}' target='_blank' title='{$vo["nickname"]}'>{$vo['nickname']}</a></div> 
     </li>";
        }  
                    $callb= $_GET['callback'];
        
         $data["tuijianemcee"]=$tuijianstr;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='tuijian'){
            echo $callb."(".json_encode($data['tuijianemcee']).")"; 
            exit;
        
         
    }
        
        
    }
    //根据分类获取推荐主播列表
    public  function   feileiemcee(){
        $fenleiid=$_GET['fenleiid'];
        //根据分类id获取主播列表
     if($fenleiid==0){
         
                 $recommend = M("member")->where("recommend = 'y'")->order("id desc")->limit(8)->select();
        //查询出等级
        $a=0; 
        foreach($recommend as $vo){
            $emceelevel = getEmceelevel($vo['earnbean']);
            $recommend[$a]["emceelevel"]=$emceelevel[0]['levelid'];
            $a++;
        } 
        foreach($recommend as $vo){
                $face=$vo["bigpic"];
                
            if($face==null){
                $face="/style/bigpic/2015-07/55a1ed57d1821.jpg";
            }
             if($vo['virtualguest'] > 0){
            $gknums=$vo['online'] + $vo['virtualguest']+$virtualcount;}else{
                 $gknums=$vo['online'];}
            $emceesstr.= "<li>
         <div class='emceeimg'><a href='show.html?roomid={$vo["curroomnum"]}'><img src='{$this->url}{$face}'><em class='islive'>直播</em></a></div>
         <div class='emceename'><p>{$gknums}人</P><a href='show.html/{$vo["curroomnum"]}' title='{$vo["nickname"]}'>{$vo['nickname']}</a></div> 
     </li>";
        } 
         
     }else{
        
        $emcees = M('Member')->where("sid=".$fenleiid." and broadcasting='y'")->order('online desc')->limit(100)->select();
    

            foreach($emcees as $vo){
                
                $face=$vo["bigpic"];
                
            if($face==null){
                $face="/style/bigpic/2015-07/55a1ed57d1821.jpg";
            }
             if($vo['virtualguest'] > 0){
            $gknums=$vo['online'] + $vo['virtualguest']+$virtualcount;}else{
                 $gknums=$vo['online'];}
            $emceesstr.= "<li>
         <div class='emceeimg'><a href='show.html?roomid={$vo["curroomnum"]}'><img src='{$this->url}{$face}'><em class='islive'>直播</em></a></div>
         <div class='emceename'><p>{$gknums}人</P><a href='show.html/{$vo["curroomnum"]}' title='{$vo["nickname"]}'>{$vo['nickname']}</a></div> 
     </li>";
        } 
     }
        $callb= $_GET['callback'];
        echo  $callb."(".json_encode($emceesstr).")"; 
            exit;
    }
    
    
    //获取首页中部推荐导航栏内容
    public function tuijiannavi(){
        //查询出数据
        $usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
        foreach($usersorts as $n=> $val){
            $usersorts[$n]['voo']=D("Usersort")->where('parentid='.$val['id'])->order('orderno')->select();
        }
        $tuijiannavi="<li ><a href='#' id='navi0' class='navi_off navi_on' onclick='getlists(0);'>推荐</a></li>"; 
        foreach($usersorts as $vo){
            foreach($vo['voo'] as $vo2){
                $tuijiannavi.="<li ><a href='#' class='navi_off' id='navi{$vo2["id"]}' onclick='getlists({$vo2["id"]});'>{$vo2['sortname']}</a></li>";
            }
            
            
        }
            $callb= $_GET['callback'];
        
    
    if(isset($_GET['ajax'])&&$_GET['ajax']=='tuijiannavi'){
            echo $callb."(".json_encode($tuijiannavi).")"; 
            exit;
        
        
    }    
    
    }
        //活动文章详情
    public function wenzhanginfo(){
        //获取文章id
        $wenzhangid=$_GET['wenzhangid'];
        $wenzhanginfo=M("announce")->where("id='$wenzhangid'")->select();
        $wenzhangstr="<div class='wenzhangimg'>
            <img src='{$this->url}style/Uploads/{$wenzhanginfo[0]["fengmian"]}'>
            </div>
            <div class='content'>
              　　<h2>{$wenzhanginfo[0]['title']}</h2>
               <div class='wenzhangcontent'>
               {$wenzhanginfo[0]['content']}
               </div>
            </div>";
        $callb= $_GET['callback'];
        
         $data["wenzhanginfo"]=$wenzhangstr;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='wenzhanginfo'){
            echo $callb."(".json_encode($data['wenzhanginfo']).")"; 
            exit;
         
         
    }
        
        
        
    }
    
    
    //活动相关
    public function  activity(){
        
        
        
        
        
        //获取全部文章列表
        $list = M("announce")->order("addtime desc")->select();
        
        /*$a=0;
        foreach($list as $k=>$v){
            $zhuangtai=$v["zhuangtai"];
            //var_dump($zhuangtai);
            if($zhuangtai=='未开始'){
                $list[$a]['num']=2;
            }elseif($zhuangtai=='正在进行'){
                $list[$a]['num']=1;
            }elseif($zhuangtai=='已结束'){
                $list[$a]['num']=3;
            }
            $a++;
        }*/
        foreach($list as $vo){
            $time=date("Y-m-d",$vo['addtime']);
            $zhuangtai=$vo['zhuangtai'];
            if($zhuangtai=='未开始'){
                $backgroundcolor="#FD31B7";
            }elseif($zhuangtai=='正在进行'){
                $backgroundcolor="#FD31B7";
            }elseif($zhuangtai=='已结束'){
                $backgroundcolor="#605069";
            }
            
            $wenzhang.="  <div class='wenzhang1'>
         <div class='wenzhangimg'>
             <a href='activityinfo.html?wenzhangid={$vo['id']}'><img src='{$this->url}style/Uploads/{$vo["fengmian"]}'/></a>
         </div>
         <div class='activityzhuangtai' style='background:{$backgroundcolor}'> {$vo['zhuangtai']}</div>
         <div class='wenzhanginfo'>
             <a href='activityinfo.html?wenzhangid={$vo['id']}'><p>{$vo['title']}    </p></a>
          
         </div>
       </div>";
            
        }
        $callb= $_GET['callback'];
        
         $data["wenzhang"]=$wenzhang;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='wenzhang'){
            echo $callb."(".json_encode($data['wenzhang']).")"; 
            exit;
         
         
    }
        
        //活动分类列表
        $fenlei=M("huodongfenlei")->select();
        $fenleistr="<li onclick='fenlei(\"\",\"活动中心\");'>活动中心</li>";
        foreach($fenlei as $vo){
            $title=$vo['title'];
            $fenleiid=$vo['id'];
        $fenleistr.="<li><a onclick='fenlei({$fenleiid},\"{$title}\");'>{$vo['title']}</a></li>";    
        }
            $callb= $_GET['callback'];
        
         $data["fenleilists"]=$fenleistr;
         if(isset($_GET['ajax'])&&$_GET['ajax']=='activityfenlei'){
            echo $callb."(".json_encode($data['fenleilists']).")"; 
            exit;
         
         
    }

    
    //获取分类id
    $fenleiid=$_GET['fenleiid'];
    
        if($fenleiid==0){
            $list = M("announce")->order("addtime desc")->select();
                foreach($list as $vo){
            $time=date("Y-m-d",$vo['addtime']);
            $zhuangtai=$vo['zhuangtai'];
            if($zhuangtai=='未开始'){
                $backgroundcolor="#FD31B7";
            }elseif($zhuangtai=='正在进行'){
                $backgroundcolor="#FD31B7";
            }elseif($zhuangtai=='已结束'){
                $backgroundcolor="#605069";
            }
            
            $wenzhang1.="  <div class='wenzhang1'>
         <div class='wenzhangimg'>
             <a href='activityinfo.html?wenzhangid={$vo['id']}'><img src='{$this->url}style/Uploads/{$vo["fengmian"]}'/></a>
         </div>
         <div class='activityzhuangtai' style='background:{$backgroundcolor}'> {$vo['zhuangtai']}</div>
         <div class='wenzhanginfo'>
             <a href='activityinfo.html?wenzhangid={$vo['id']}'><p>{$vo['title']}    </p></a>
         
         </div>
       </div>";
            
        }
                 $data["wenzhang1"]=$wenzhang1;
        $callb= $_GET['callback'];
        echo  $callb."(".json_encode($data['wenzhang']).")"; 
            exit;
        }else{
            $list = M("announce")->where("fid=".$fenleiid)->order("addtime desc")->select();
            foreach($list as $vo){
            $time=date("Y-m-d",$vo['addtime']);
             $zhuangtai=$vo['zhuangtai'];
            if($zhuangtai=='未开始'){
                $backgroundcolor="#FD31B7";
            }elseif($zhuangtai=='正在进行'){
                $backgroundcolor="#FD31B7";
            }elseif($zhuangtai=='已结束'){
                $backgroundcolor="#605069";
            }
            
            $wenzhang1.="  <div class='wenzhang1'>
         <div class='wenzhangimg'>
             <a href='activityinfo.html?wenzhangid={$vo['id']}'><img src='{$this->url}style/Uploads/{$vo["fengmian"]}'/></a>
         </div>
         <div class='activityzhuangtai' style='background:{$backgroundcolor}'> {$vo['zhuangtai']}</div>
         <div class='wenzhanginfo'>
             <a href='activityinfo.html?wenzhangid={$vo['id']}'><p>{$vo['title']}    </p></a>
            
         </div>
       </div>";
            
        }
                 $data["wenzhang1"]=$wenzhang1;
        $callb= $_GET['callback'];
        echo  $callb."(".json_encode($data['wenzhang1']).")"; 
            exit;
            
        }
    
     
    
    } 
    
    
    
    //活动页图片轮播
    public  function  activityrollpic(){
        $rollpics = M('huodongrollpic')->where('')->field('picpath,title,linkurl')->order('orderno asc')->limit(3)->select();
    
        
            foreach($rollpics as $vo){
                $rollpicstr.="<li><img src='{$this->url}{$vo["picpath"]}'/></li>";
            }
            
            $callb= $_GET['callback'];
        
         $data["rollpic"]=$rollpicstr;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='rollpic'){
            echo $callb."(".json_encode($data['rollpic']).")"; 
            exit;
        
         
    }
    }

    /*//小编推荐主播列表
    public function  tuijianemcee(){
        $recommend = M("member")->where("recommend = 'y'")->order("id desc")->limit(8)->select();
        //查询出等级
        $a=0; 
        foreach($recommend as $vo){
            $emceelevel = getEmceelevel($vo['earnbean']);
            $recommend[$a]["emceelevel"]=$emceelevel[0]['levelid'];
            $a++;
        } 
        foreach($recommend as $vo){
             if($vo['virtualguest'] > 0){
            $gknums=$vo['online'] + $vo['virtualguest']+$virtualcount;}else{
                 $gknums=$vo['online'];}
            $tuijianstr.= "<li>
         <div class='emceeimg'><a href='show.html?roomid={$vo["curroomnum"]}'><img src='{$this->url}{$vo["bigpic"]}'></a></div>
         <div class='emceename'><p>{$gknums}人</P><a href='show.html?roomid={$vo["curroomnum"]}' title='{$vo["nickname"]}'>{$vo['nickname']}</a></div> 
     </li>";
        } 
                    $callb= $_GET['callback'];
        
         $data["tuijianemcee"]=$tuijianstr;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='tuijian'){
            echo $callb."(".json_encode($data['tuijianemcee']).")"; 
            exit;
        
          
    }
         
        
    }*/
        //获取首页轮播信息
public  function  rollpic(){
    $rollpics = D('Rollpic')->where('')->field('picpath,title,linkurl')->order('orderno asc')->limit(3)->select();
    
        
            foreach($rollpics as $vo){
                $rollpicstr.="<li><img src='{$this->url}{$vo["picpath"]}'/></li>";
            }
            
            $callb= $_GET['callback'];
        
         $data["rollpic"]=$rollpicstr;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='rollpic'){
            echo $callb."(".json_encode($data['rollpic']).")"; 
            exit;
        
         
    }
    
    
}    
    //获取show 信息
    public function  getshowinfo(){
        
        /*var _show={"isHD":0,"enterChat":0,"emceeId":"<?php echo $userinfo['id']; ?>","fps":"<?php echo $userinfo['fps'];?>","cdnl":"<?php echo $userinfo['cdnl'];?>","cdn":"<?php echo $userinfo['cdn'];?>","zddk":"<?php echo $userinfo['zddk'];?>","pz":"<?php echo $userinfo['pz']?>","zjg":"<?php echo $userinfo['zjg']?>","height":"<?php echo $userinfo['height']?>","width":"<?php echo $userinfo['width']?>","emceeLevel":1,"goodNum":<?php echo $userinfo['curroomnum']; ?>,"emceeNick":"<?php echo $userinfo['nickname']; ?>","oldseatnum":"0","songPrice":"1500","offline":0,"roomId":"<?php echo $userinfo['curroomnum']; ?>","titlesUrl":"","titlesLength":"4","bgimg":"<?php echo $userinfo['bgimg']; ?>"};*/
        $uid=$_SESSION['uid'];
        //$uid=$_GET['uid'];
        $userinfo=M("member")->where("id=$uid")->select();
            $callb= $_GET['callback'];
        
         
    if(isset($_GET['ajax'])&&$_GET['ajax']=='showinfo'){
            echo $callb."(".json_encode($userinfo[0]).")"; 
            exit;
        
        
    }
    }
    //发现 心主播页面
    public function findbobo(){
        //发现新主播
        
        $recusers = D('Member')->where('bigpic<>"" and recommend="y" and broadcasting="y" and isdelete="n"')->field('nickname,curroomnum,bigpic,online,virtualguest')->order('rectime desc')->limit(12)->select();
        //var_dump($recusers);
    
        $emceestr='<ul class="bigimg" id="emceelists" style="width:100%;margin:0;">';
        foreach($recusers as $vo){
            
            /*<?php if($vo['virtualguest'] > 0){echo ($vo['online'] + $vo['virtualguest'] + $virtualcount);}else{echo $vo['online'];} ?>*/
                if($vo['virtualguest'] > 0){
            $gknums=$vo['online'] + $vo['virtualguest']+$virtualcount;}else{
                 $gknums=$vo['online'];} 
        
            
            $emceestr.= "<li style='width:50%; margin-left: 0px;'>
            <div class='findemceeimg'>
                    <a href='show.html?roomid={$vo["curroomnum"]}'  target='_blank'><img src='{$this->url}{$vo["bigpic"]}' style='width:100%;' /></a> 
                    </div>
                        <div class='text' style=''>
                        <p><span>
                       {$gknums}
                       </span>人</p>
                        <a  href='show.html?roomid={$vo["curroomnum"]}' title='{$vo["nickname"]}' target='_blank'>{$vo['nickname']}</a> 
                        
                        </div>
                    </li>";    
        }
        $emceestr.='</ul>';
            $callb= $_GET['callback'];
        
        $data['emcees'] = $emceestr;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='findbobo'){
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
        
    }
    //靓号页面
    public function emceeno(){
        //四位靓号
        $four_goodnums = D('Goodnum')->where('length=4 and issale="n"')->order('rand()')->limit(4)->select();    
        $str="<ul>";
        foreach($four_goodnums as $vo){
       $str.="<li><p>靓号: <strong class='f-1'>{$vo['num']}</strong></p><p class='price'>价值: <strong class='f-2'>{$vo['price']}</strong> 个秀币</p><span class='gn_btn mt5' onClick='Manage.Buynum({$vo["num"]},false)'><cite>购买</cite></span></li>";                      
        }
        $str.="</ul>";
    $callb= $_GET['callback'];
        
        $data['aa'] = $str;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='emceeno4'){
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
    
//五位靓号
    $five_goodnums = D('Goodnum')->where('length=5 and issale="n"')->order('rand()')->limit(4)->select();
    $str="<ul>";
        foreach($five_goodnums as $vo){
       $str.="<li><p>靓号: <strong class='f-1'>{$vo['num']}</strong></p><p class='price'>价值: <strong class='f-2'>{$vo['price']}</strong> 个秀币</p><span class='gn_btn mt5' onClick='Manage.Buynum({$vo["num"]},false)'><cite>购买</cite></span></li>";                      
        }
        $str.="</ul>";
    $callb= $_GET['callback'];
        
        $data['length5'] = $str;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='emceeno5'){
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
    //6位靓号
    $six_goodnums = D('Goodnum')->where('length=6 and issale="n"')->order('rand()')->limit(4)->select();
      $str="<ul>";
        foreach($six_goodnums as $vo){
       $str.="<li><p>靓号: <strong class='f-1'>{$vo['num']}</strong></p><p class='price'>价值: <strong class='f-2'>{$vo['price']}</strong> 个秀币</p><span class='gn_btn mt5' onClick='Manage.Buynum({$vo["num"]},false)'><cite>购买</cite></span></li>";                      
        }
        $str.="</ul>";
    $callb= $_GET['callback'];
        
        $data['length6'] = $str;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='emceeno6'){
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
//7位靓号
$seven_goodnums = D('Goodnum')->where('length=7 and issale="n"')->order('rand()')->limit(4)->select();    
           $str="<ul>";
        foreach($seven_goodnums as $vo){
       $str.="<li><p>靓号: <strong class='f-1'>{$vo['num']}</strong></p><p class='price'>价值: <strong class='f-2'>{$vo['price']}</strong> 个秀币</p><span class='gn_btn mt5' onClick='Manage.Buynum({$vo["num"]},false)'><cite>购买</cite></span></li>";                      
        }
        $str.="</ul>";
    $callb= $_GET['callback'];
        
        $data['length7'] = $str;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='emceeno7'){
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
    
//8位靓号
$eight_goodnums = D('Goodnum')->where('length=8 and issale="n"')->order('rand()')->limit(4)->select();    
           $str="<ul>";
        foreach($eight_goodnums as $vo){
       $str.="<li><p>靓号: <strong class='f-1'>{$vo['num']}</strong></p><p class='price'>价值: <strong class='f-2'>{$vo['price']}</strong> 个秀币</p><span class='gn_btn mt5' onClick='Buynums({$vo["num"]},false)'><cite>购买</cite></span></li>";                      
        }
        $str.="</ul>";
    $callb= $_GET['callback'];
        
        $data['length8'] = $str;
    if(isset($_GET['ajax'])&&$_GET['ajax']=='emceeno8'){
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
    }
    
    

    
    //申请家族表单
    public function sqagentinfo(){
       
        $uid=$_SESSION['uid'];
        $model=M("agentfamily");
        $model->uid=$uid;
        $model->familyname=$_REQUEST['familyname'];
        $model->familyinfo=$_REQUEST['familyinfo'];
        $model->sqtime=time();
      
        if($model->add()){
            $code['s'] = 1;
        }else{
            $code['s'] = 2;
        }
        $code['c']=M()->getLastsql();
        $json= json_encode($code);
        echo $_GET['callback']."(".$json.")";
    }
    
    
    //申请加入家族
    public function sqjoinfamily(){
         $fid=$_GET['fid'];
         $uid=$_SESSION['uid'];
         $res=M("member")->where("id=".$uid)->getField("emceeagent");
         $agentuid=M("member")->where("id=".$uid)->getField("agentuid");
          $sqjoininfo=M("sqjoinfamily")->where("uid=".$uid)->order("sqtime desc")->limit(1)->select();
    
        $sqzt=$sqjoininfo[0]["zhuangtai"];
           if(!$uid || $uid < 0){
               //未登录
            $data["zhuangtai"]=1;
            
        }elseif($res=='y'){
            //创建过自己的家族
            $data["zhuangtai"]=2;
        }elseif($agentuid!='0'){
            //已经加入过其它家族
            $data["zhuangtai"]=3;
        }elseif($sqzt=='0'){
            //有一条申请  审核中
            $data["zhuangtai"]=4;
        }else{
            //条件允许可以申请 将申请的数据插入到数据库中等待审核
            $model=M("sqjoinfamily");
        $model->uid=$uid;
        $model->familyid=$fid;
        $model->sqtime=time();
        if($model->add()){
            //申请成功，等待审核
            $data["zhuangtai"]=0;
        }else{
            $data["zhuangtai"]=5;
        }
            
            
            
        }
           
       $callb= $_GET['callback'];
         if(isset($_GET['ajax'])&&$_GET['ajax']=='sqjoinfamily'){
            /*echo json_encode($data);*/
             echo $callb."(".json_encode($data).")"; 
            exit;
            
        }   
           
    }
    
    
    //申请成为代理 创建家族
public function sqagent(){
     $uid=$_SESSION['uid'];

     $res=M("member")->where("id=".$uid)->getField("emceeagent");
     $sqinfo=M("agentfamily")->where("uid=".$uid)->order("sqtime desc")->limit(1)->select();
   $sqzt=$sqinfo[0]["zhuangtai"];
    //1.未登录  2.已经创建过家族 3.提交过申请 正在审核 
    //判断是否登录
   if($uid==null){ 
       $data["zhuangtai"]=1;
   } elseif($res=='y'){
            $data["zhuangtai"]=2;
        
        }elseif($sqzt=="未审核"){
      $data["zhuangtai"]=3;
   }elseif($sqzt=="未通过"){
      $data["zhuangtai"]=4;
   }else{
       $data["zhuangtai"]=0;
   }
       
    
    
    

      $callb= $_GET['callback'];
  if(isset($_GET['ajax'])&&$_GET['ajax']=='zhuangtai'){
        /*    echo json_encode($data);*/
       echo $callb."(".json_encode($data).")"; 
            exit;
            
        }     
}        
//家族内页相关内容
public function jiazunei(){
        $fid=$_GET['fid'];
//通过$fid 获取相应家族信息
   $familyinfo=M("agentfamily")->where("uid='$fid'&& zhuangtai='已通过'")->select();
$str=<<<EOT
    <li class="" style="">
        <a href="#">  
            <img class="jiazuimg" src="{$this->url}style/Familyimg/{$familyinfo[0]['familyimg']}" >
            <div class="jiazuinfos" >
                {$familyinfo[0]['familyname']}
                <p class=''>家族签名：{$familyinfo[0]['familyinfo']}</p>
            </div>
        </a>
    </li>
EOT;
    $data['jiazuinfo'] = $str;
$callb= $_GET['callback'];
        if(isset($_GET['ajax'])&&$_GET['ajax']=='jiazuinfo'){
            /*echo json_encode($data);*/
            //$this->ajaxReturn($data);
              echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
//家族经纪人想关信息
    $agentinfo=M("member")->where("id='$fid'")->select();
    //var_dump($agentinfo);        
    $str=<<<EOT
<li class="">
        <a href="#">
            <div><img class="jiazuimg" src=""></div>
            <div class="jiazuinfos"style="height:90px;">
                {$agentinfo[0]['nickname']} 
                <p class=''>签名：{$agentinfo[0]['intro']}</p>
            </div>
        </a> 
    </li>
EOT;


    $data['jiazuagent'] = $str;
$callb= $_GET['callback'];
        if(isset($_GET['ajax'])&&$_GET['ajax']=='jiazuagent'){
            /*echo json_encode($data);*/
            //$this->ajaxReturn($data);
              echo $callb."(".json_encode($data).")"; 
            exit;
            
        }    
        
//当前家族在线人气主播
$olrqzb = D('Member')->where(" broadcasting='y' and isdelete='n' and agentuid=$fid")->field('nickname,curroomnum,bigpic,online,virtualguest,agentuid,online')->order('online desc')->limit(8)->select();
//var_dump($olrqzb);
foreach($olrqzb as $v){

    $strol.=<<<EOT
<li class="onlinerq_lists"><div class="onlinerq_pic"><a href="show.html?roomid={$v["curroomnum"]}"><img class="onlinerq_img" src="{$this->url}{$v['bigpic']}"></div><div class="jiazuinfos">主播：{$v["nickname"]}<p class='mui-ellipsis'>签名：{$v["intro"]}</p>

EOT;
    
    
}    
$data['jiazuonline'] = $strol;

$callb= $_GET['callback'];
        if(isset($_GET['ajax'])&&$_GET['ajax']=='jiazuonline'){
            /*echo json_encode($data);*/
            //$this->ajaxReturn($data);
              echo $callb."(".json_encode($data).")"; 
            exit;
            
        }

//家族全部主播
$emceeinfo=M("member")->where("agentuid=$fid")->select();
    //var_dump($emceeinfo);
    foreach($emceeinfo as $v){
    
    $strzb.=<<<EOT
<li class="onlinerq_lists"><div class="onlinerq_pic"><a href="show.html?roomid={$v["curroomnum"]}"><img class="onlinerq_img" src="{$this->url}{$v['bigpic']}"></div><div class="jiazuinfos">主播：{$v["nickname"]}<p class='mui-ellipsis'>签名：{$v["intro"]}</p>

EOT;
    
    
}    
$data['jiazuzb'] = $strzb;

$callb= $_GET['callback'];
        if(isset($_GET['ajax'])&&$_GET['ajax']=='jiazuzb'){
            /*echo json_encode($data);*/
            //$this->ajaxReturn($data);
             echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
    
     
}    
    
    
//家族相关
 public function jiazu(){
     
//最新组建
  $res=M("member")->where("emceeagent='y'")->order("emceeagenttime desc")->limit(8)->select();    
    //根据uid 去查询相关信息
    $a=0;
    foreach($res as $v){
        $uid=$v['id'];
        
        $familyinfo=M("agentfamily")->where("uid='$uid' && zhuangtai='已通过'")->select();
        $zbcount=M("member")->where("agentuid=$uid")->count();
        $familyinfo['zbcount']=$zbcount;
        $res[$a]['familyinfo']=$familyinfo;
        $a++;
}
$a=0;
foreach($res as $v){
    $str.=<<<EOT
<li class="newjiazu_lists"><a href="jiazunei.html?familyid={$v["familyinfo"][0]["uid"]}"><div class="jiazupic"><img class="newjiazu_img" src="{$this->url}style/Familyimg/{$v["familyinfo"][0]["familyimg"]}"></div><div class="newjiazu_info">{$v["familyinfo"][0]["familyname"]}</div></a></li>
EOT;
} 

  $callb= $_GET['callback'];
    $data['newjiazu'] = $str;

        if(isset($_GET['ajax'])&&$_GET['ajax']=='newjiazu'){
            /*echo json_encode($data);*/
            //$this->ajaxReturn($data);
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }
            
        
//推荐家族直播
$recusers = D('Member')->where('bigpic<>"" and broadcasting="y" and isdelete="n" and agentuid!=0 ')->field('nickname,curroomnum,bigpic,online,virtualguest,agentuid')->order('rectime desc')->limit(10)->select();        
    
        $a=0;
        foreach($recusers as $k=>$v){
            $uid=$v['agentuid'];
            
            $familyname=M("agentfamily")->where("uid='$uid'&& zhuangtai='已通过'")->getField("familyname");
            
            
            $recusers[$a]['familyinfo']=$familyname;
            $a++;
        }
          
$str=<<<EOT
EOT;
$a=0;        
foreach($recusers as $v){
    $curroomnum=$v["curroomnum"];
    //var_dump($curroomnum);
        $str.=<<<EOT
<li class="newjiazu_lists"><a href='show.html?roomid={$v["curroomnum"]}'><div class="jiazupic"><img class="newjiazu_img" src="{$this->url}{$v["bigpic"]}"></div><div class="newjiazu_info">{$v["nickname"]}<p class=''>家族：{$v["familyinfo"]}</p></div></a></li>
EOT;
    
}
$data['zbjiazu'] = $str;
  $callb= $_GET['callback'];
        if(isset($_GET['ajax'])&&$_GET['ajax']=='zbjiazu'){
            /*echo json_encode($data);*/
                echo $callb."(".json_encode($data).")"; 
            //$this->ajaxReturn($data);
            exit;
            
        }        
//热门家族排行
 //按照主播人数实现热门家族人气排行
 $zbcount=M("member")->query('select count(*) as total,agentuid from `ss_member` where agentuid>0 group by agentuid order by total desc limit 10');
 //var_dump($zbcount);
 //根据agentuid得到相应的家族信息 实现家族列表
     $a=0;
         $data1=array();
        foreach($zbcount as $k=>$v){
            $aid=$v['agentuid'];
              
            $familyinfo=M("agentfamily")->where("uid='$aid'&& zhuangtai='已通过'")->select();
                
            $zbcounts=M("member")->query("select count(*) as total from `ss_member` where agentuid=$aid ");
              $familyinfo[0]['zbtotal']=$zbcounts;
             $data1[$a]=$familyinfo;
            
        
             $a++;
        
        }    
        
        $str=<<<EOT
EOT;
        
foreach($data1 as $v){

    $str.=<<<EOT
<li class="newjiazu_lists"><a href="jiazunei.html?familyid={$v[0]["uid"]}"><div class="jiazupic"><img class="newjiazu_img" src='{$this->url}style/Familyimg/{$v[0]["familyimg"]}'></div><div class="newjiazu_info">家族：{$v[0]['familyname']}<p class='mui-ellipsis'>主播人数：{$v[0]['zbtotal'][0]['total']}</p></div></a></li>
EOT;

}
$data['hotjiazu'] = $str;
  $callb= $_GET['callback'];
        if(isset($_GET['ajax'])&&$_GET['ajax']=='hotjiazu'){
            /*echo json_encode($data);*/
            //$this->ajaxReturn($data);
            echo $callb."(".json_encode($data).")"; 
            exit;
            
        }        

        
 }    
    
//排行相关
    public function order(){
        //手机APP相关
                //礼物相关排行
                $callb= $_GET['callback'];
        //礼物总榜
        $gifts_toall=M("coindetail")->query("SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` group by giftid order by total desc limit 20");
        $a=0;
        foreach($gifts_toall as $k=>$vo){

            $userinfo = D("Member")->find($vo['touid']);
            $gifts_toall[$a]['userinfo']=$userinfo;
            $emceelevel = getEmceelevel($userinfo['earnbean']);
            $gifts_toall[$a]['emceelevel']=$emceelevel;
            $giftinfo=D("gift")->find($vo['giftid']);
            $gifts_toall[$a]['giftinfo']=$giftinfo;
            $a++;
        }
        $a=1;
        $liwuallstr = '';
             foreach($gifts_toall as $v){
             
                 $liwuallstr.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='{$this->url}{$v['giftinfo']['gifticon']}' width='40px' height='40px;'style='border-radius:50%;' /></div><p class='orderemceename' >{$v['giftinfo']['giftname']}</p><p class='orderemceename' style='float:right; margin-top:-20px;' >{$v['total']}</p></li>";
                 $a++;
             }    
            
    
        
        $data['ul'] = $liwuallstr;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='liwuall'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            //$this->ajaxReturn($data);
            exit;
            
        }
      //礼物月榜
          $gifts_month = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by giftid order by total desc limit 20');
        $a=0;
        foreach($gifts_month as $k=>$vo){
        
        $userinfo = D("Member")->find($vo['touid']);
        $gifts_month[$a]['userinfo']=$userinfo;
        $emceelevel = getEmceelevel($userinfo['earnbean']);
        $gifts_month[$a]['emceelevel']=$emceelevel;
        $giftinfo=D("gift")->find($vo['giftid']);
        $gifts_month[$a]['giftinfo']=$giftinfo;
        $a++;
        }
       $a=1;
             foreach($gifts_month as $v){
             
                 $liwumonthstr.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='{$this->url}{$v['giftinfo']['gifticon']}' width='40px' height='40px;'style='border-radius:50%;' /></div><p class='orderemceename' >{$v['giftinfo']['giftname']}</p><p class='orderemceename' style='float:right; margin-top:-20px;' >{$v['total']}</p></li>";
                 $a++;
             }    
            
    
        
        $data['ul'] = $liwumonthstr;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='liwumonth'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            //$this->ajaxReturn($data);
            exit;
            
        }
        
    //礼物周榜
    $gifts_week = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by giftid order by total desc limit 20');
        $a=0;
        foreach($gifts_week as $k=>$vo){
        
        $userinfo = D("Member")->find($vo['touid']);
        $gifts_week[$a]['userinfo']=$userinfo;
        $emceelevel = getEmceelevel($userinfo['earnbean']);
        $gifts_week[$a]['emceelevel']=$emceelevel;
        $giftinfo=D("gift")->find($vo['giftid']);
        $gifts_week[$a]['giftinfo']=$giftinfo;
        $a++;
        }
       $a=1;
             foreach($gifts_week as $v){
             
                 $liwuweekstr.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='{$this->url}{$v['giftinfo']['gifticon']}' width='40px' height='40px;'style='border-radius:50%;' /></div><p class='orderemceename' >{$v['giftinfo']['giftname']}</p><p class='orderemceename' style='float:right; margin-top:-20px;' >{$v['total']}</p></li>";
                 $a++;
             }    
            
    
        
        $data['ul'] = $liwuweekstr;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='liwuweek'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            //$this->ajaxReturn($data);
            exit;
            
        }    
        
//礼物日榜
        $gifts_day = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by giftid order by total desc limit 20 ');
            $a=0;
        foreach($gifts_day as $k=>$vo){
        
        $userinfo = D("Member")->find($vo['touid']);
        $gifts_day[$a]['userinfo']=$userinfo;
        $emceelevel = getEmceelevel($userinfo['earnbean']);
        $gifts_day[$a]['emceelevel']=$emceelevel;
        $giftinfo=D("gift")->find($vo['giftid']);
        $gifts_day[$a]['giftinfo']=$giftinfo;
        $a++;
        }
       $a=1;
             foreach($gifts_day as $v){
             
                 $liwudaystr.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='{$this->url}{$v['giftinfo']['gifticon']}' width='40px' height='40px;'style='border-radius:50%;' /></div><p class='orderemceename' >{$v['giftinfo']['giftname']}</p><p class='orderemceename' style='float:right; margin-top:-20px;' >{$v['total']}</p></li>";
                 $a++;
             }    
            
    
        
        $data['ul'] = $liwudaystr;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='liwuday'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            //$this->ajaxReturn($data);
            exit;
            
        }    
        
        //明星总榜
                $emceeRank_all = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 20');     
                $a=0;
                foreach($emceeRank_all as $v ){
                $userinfo=D("Member")->find($v['uid']);
                    //var_dump($userinfo['earnbean']);
                $emceelevel = getEmceelevel($userinfo['earnbean']);
                
            
                $emceeRank_all[$a]['emceelevel']=$emceelevel;
                $emceeRank_all[$a]['userinfo']=$userinfo;
                
                $a++;
               }
                
            
           $str = "";
           $a=1;
             foreach($emceeRank_all as $v){
                 $levelid=$v["emceelevel"][0]["levelid"];
                $roomnum=$v['userinfo']['curroomnum'];
                    $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$v["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 //$str.="<a href=''><li class='mui-table-view-cell'>".$a.".{$v['userinfo']['nickname']}（{$v['userinfo']['curroomnum']}）<span class='star star6 fr' style='background:url(img/userLevel_vv8.png);'></span></li></a>";
                 $a++;
             }     
            
                
        //$str = "<ul class='mui-table-view-cell'>";
        //foreach($emceeRank_all as $v )
        //{
            //$str.="<li>{$v['uid']}</li>";
        //}
        
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='mingxingall'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            //$this->ajaxReturn($data);
            exit;
            
        }
        
    //手机APP明星周榜    
        $emceeRank_week = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 20');    
        $a=0;
                foreach($emceeRank_week as $v ){
                $userinfo=D("Member")->find($v['uid']);
                    $emceelevel = getEmceelevel($userinfo['earnbean']);
                
            
                $emceeRank_week[$a]['emceelevel']=$emceelevel;
                $emceeRank_week[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($emceeRank_week as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
                $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 //$str.="<a href='#'><li class='mui-table-view-cell'>".$a.".{$v['userinfo']['nickname']}（{$v['userinfo']['curroomnum']}）11<span class='cracy cra fr'>22</span>33</li></a>";
                 $a++;
             }    
                
                
        
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='mingxingweek'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
    //手机APP明星月榜
    $emceeRank_month = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 20');
        $a=0;
                foreach($emceeRank_month as $v ){
                $userinfo=D("Member")->find($v['uid']);
                $emceeRank_month[$a]['userinfo']=$userinfo;
                    $emceelevel = getEmceelevel($userinfo['earnbean']);
                
            
                $emceeRank_month[$a]['emceelevel']=$emceelevel;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($emceeRank_month as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
                    $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 //$str.="<a href='#'><li class='mui-table-view-cell'>".$a.".{$v['userinfo']['nickname']}（{$v['userinfo']['curroomnum']}）</li></a>";
                 $a++;
             }    
                
                
        
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='mingxingmonth'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
//手机APP明星日榜
     $emceeRank_day = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 20');
    $a=0;
                foreach($emceeRank_day as $v ){
                $userinfo=D("Member")->find($v['uid']);
                $emceeRank_day[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($emceeRank_day as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
                $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 //$str.="<a href='#'><li class='mui-table-view-cell'>".$a.".{$v['userinfo']['nickname']}（{$v['userinfo']['curroomnum']}）</li></a>";
                 $a++;
             }    
            
                
    
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='mingxingday'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
//手机APP人气排行榜
 

 //手机人气日榜
       $rqRank_day = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 20');
        $a=0;
                foreach($rqRank_day as $v ){
                $userinfo=D("Member")->find($v['uid']);
                $emceelevel = getEmceelevel($userinfo['earnbean']);
        $rqRank_day[$a]['emceelevel']=$emceelevel;    
                $rqRank_day[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($rqRank_day as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
            $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }    
            
                
    
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='renqiday'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
          
   //手机人气周榜     
        
        $rqRank_week = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(starttime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 20');
        //var_dump($rqRank_week);
            $a=0;
                foreach($rqRank_week as $v ){
                $userinfo=D("Member")->find($v['uid']);
                    $emceelevel = getEmceelevel($userinfo['earnbean']);
        $rqRank_week[$a]['emceelevel']=$emceelevel;    
                $rqRank_week[$a]['userinfo']=$userinfo;
                $a++;
               }

           $str = "";
           $a=1;
             foreach($rqRank_week as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
             $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }    
            
                
    
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='renqiweek'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
  //手机人气月榜      
        $rqRank_month = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 20');
        
                $a=0;
                foreach($rqRank_month as $v ){
                $userinfo=D("Member")->find($v['uid']);
                    $emceelevel = getEmceelevel($userinfo['earnbean']);
        $rqRank_month[$a]['emceelevel']=$emceelevel;    
                $rqRank_month[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($rqRank_month as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
                 $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }    
            
                
    
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='renqimonth'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
//手机人气总榜        
        $rqRank_all = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` group by uid order by total desc LIMIT 20');
        //var_dump($rqRank_all);
            $a=0;
                foreach($rqRank_all as $v ){
                $userinfo=D("Member")->find($v['uid']);
                $emceelevel = getEmceelevel($userinfo['earnbean']);
        $rqRank_all[$a]['emceelevel']=$emceelevel;    
                $rqRank_all[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($rqRank_all as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
             $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi star  star{$v["emceelevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }    
            
                
    
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='renqiall'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
                
//手机富豪排行榜
//富豪日榜
$richRank_day = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 20');        
        
            $a=0;
                foreach($richRank_day as $v ){
                $userinfo=D("Member")->find($v['uid']);
                         $richlevel = getRichlevel($userinfo['spendcoin']);
              $richRank_day[$a]['richlevel']=$richlevel;        
                $richRank_day[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($richRank_day as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
             $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi cracy cra{$v["richlevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }    
            
                
    
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='fuhaoday'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
        
//富豪周榜        
    $richRank_week = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 20');    
        $a=0;
                foreach($richRank_week as $v ){
                $userinfo=D("Member")->find($v['uid']);
                         $richlevel = getRichlevel($userinfo['spendcoin']);
                $richRank_week[$a]['richlevel']=$richlevel;        
                $richRank_week[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($richRank_week as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
             $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi cracy cra{$v["richlevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }    
            
                
    
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='fuhaoweek'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
//富豪月榜
$richRank_month = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 20');        
            $a=0;
                foreach($richRank_month as $v ){
                $userinfo=D("Member")->find($v['uid']);
                     $richlevel = getRichlevel($userinfo['spendcoin']);
                $richRank_month[$a]['richlevel']=$richlevel;            
                $richRank_month[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($richRank_month as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
             $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi cracy cra{$v["richlevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }    
            
                
     
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='fuhaomonth'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
        
    //富豪总榜
    $richRank_all = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 20');
            //$userinfo = D("Member")->find($vo['uid']);
            //$richlevel = getRichlevel($userinfo['spendcoin']);
            
            $a=0;
                foreach($richRank_all as $v ){
                $userinfo=D("Member")->find($v['uid']);    
                $richlevel = getRichlevel($userinfo['spendcoin']);
                $richRank_all[$a]['richlevel']=$richlevel;        
                $richRank_all[$a]['userinfo']=$userinfo;
                $a++;
               }
           $str = "";
           $a=1;
             foreach($richRank_all as $v){
                 $roomnum=$v['userinfo']['curroomnum'];
            $str.="<li class=''><p  class='ordernum'>".$a.".</p><div class='orderpic' ><img src='' width='40px' height='40px;'style='border-radius:50%;' /></div><a href='show.html?roomid={$vo["curroomnum"]}'><p class='orderemceename' >{$v['userinfo']['nickname']}</p></a><em class='weizhi cracy cra{$v["richlevel"][0]["levelid"]}'></em></li>";
                 $a++;
             }
        $data['ul'] = $str;
        if(isset($_GET['ajax'])&&$_GET['ajax']=='fuhaoall'){
            echo $callb."(".json_encode($data).")"; 
            //echo json_encode($data);
            exit;
            
        }
        $this->display();
    }
}
