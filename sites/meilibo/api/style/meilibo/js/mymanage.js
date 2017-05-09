 /*
 * myManage-Center
 * */
var Manage={
    count4:2,
    count5:2,
    count6:2,
    count7:2,
    count8:2,
    //赠送他人
    GiveGoodNum:function(){ 
        var no=$("#no").html(); 
        var grantId=$("#grantId").val();    
        $('#msg-datas,#pop-msgs').show();
        $('#msg-return').hide();
        $.ajax({
            type:"post",
            //url:no+"_personal_grant_grantId_"+grantId+".htm",
            url:"/my/transroomnum/roomnum/"+no+"/grantId/"+grantId+"/",
            data:{t:Math.random()},
            success:function(data){
                if(data=='success')
                {
                    $('#msg-datas,#pop-msgs').hide();
                    $('#msg-return').show();
                    $('#return-text').html('操作成功!');
                    $('#return-button').click(function(){
                        $(document).trigger('close.JShowTip');
                        window.location.reload();
                    })
                }
                if(data=='error')
                {
                    $('#msg-datas,#pop-msgs').hide();
                    $('#msg-return').show();
                    $('#return-text').html('操作错误!');
                    $('#return-button').click(function(){
                        $(document).trigger('close.JShowTip');
                    })
                }
            }
        });    
    },
    //靓号用户刷新
    refreshUser:function(){
        //return;
        var tmp = 1;
        $.ajax({
            type:"post",
            url:"/index.php/emceeno/emceeno_refreshEmceeNoList/",
            dataType:'json',
            data:"",
            cache: false,
            success:function(data){
                var dataArray=new Array();
                $.each(data,
                function(i,item) {
                    dataArray.push('<ul><li>');
                    dataArray.push('<a href=/'+item["num"]+'><img width="40" height="40" src='+item["logo"]+'></a>');
                    dataArray.push('<p>'+item["num"]+'</p>');
                    dataArray.push('</li></ul>');
                });
                $("#guser-info").html(dataArray.join(""));
            }
        });    
    },
    checknum:function(){
        var n=$("#goognums").val();    
        if(!this.checkInt(n) || !n)
         {
            alert("请输入有效数字！");
            $("#goognums").focus();
            return;
        } else if(n < 1000 || n > 99999999)
         {
            alert("请输入4~8位号码");
            $("#goognums").focus();
            return;
        }else if(n.substring(0, 1) == 0) {
            alert("号码不能是0开头");
            $("#goognums").focus();
            return;
        }
        $("#giveBox").css({"left":getMiddlePos('giveBox').pl+"px","top":"40%"}).show();
        var str='<p class="txt">查询中...</p>';
        $("#goodnum-tip").html(str);
        $.ajax({
            type:"post",
            url:"/index.php/emceeno/emceeno_search_emceeno/num/"+n+"/",
            dataType:'json',
            data:"",
            cache: false,
            success:function(data){
                if(data['state']==0){
                    str='<p class="txt">恭喜，此号码还未被抢占！</p>\
                    <p class="txt">靓号:'+n+'</p>\
                    <p class="txt">价值: '+data["price"]+'个秀币</p>\
                    <p class="txt"><span class="sendBtn" onclick="Manage.Buynum('+n+','+data["price"]+')">购买</span><span  class="sendBtn ml10" onclick="Manage.closeBox();">取消</span></p>';
                }else if(data['state']==1){
                    str='<p class="txt">该号码已被一位幸运用户捷足先登了！</p>\
                    <p class="luckyboy">'+data['nick']+'</a></p>\
                    <p class="txt"><span class="sendBtn" onclick="Manage.closeBox();">重新查询</span></p>\
                    </div>';
                }else if(data['state']==2){
                    str='<p class="txt">该号码不存在！</p>\
                    <p class="txt"><span class="sendBtn" onclick="Manage.closeBox();">重新查询</span></p>\
                    </div>';
                }else{
                    Manage.resetsearch();
                    return;
                }
                $("#goodnum-tip").html(str);
            }
        });    
    },
    checkInt:function(num){
        return (num % 1==0?true:false);
    },
    closeBox:function(){
        $('#giveBox').hide();    
        $("#goognums").val('').focus();

    },
    reload:function(){
        window.location.reload();
    },
    buynum:function(){
            
    },
    changeNum:function(type){
        //alert('暂未开放');
        //return;
        var intTo=type;
        count = 0;
        if(type == 4)
        {
            this.count4++;
            count = this.count4;
        }
        if(type == 5)
        {
            this.count5++;  
            count = this.count5;
        }
        if(type == 6)
        {
            this.count6++;
            count = this.count6;
        }   
        if(type == 7)
        {
            this.count7++;
            count = this.count7;
        }     
        if(type == 8)
        {
            this.count8++;
            count = this.count8;
        } 
                      
        $.ajax({
            type:"post",
            url:"/index.php/emceeno/emceeno_refreshSaleNoList/length/"+type+"/",
            dataType:'json',
            data:"",
            cache: false,
            success:function(data){

            },
        });    
    },
    Buynum:function(num,isLogin){      
        $("#giveBox").css({"left":getMiddlePos('giveBox').pl+"px","top":"40%"}).show();
        var str='<p class="txt">处理中...</p>';
        $("#goodnum-tip").html(str);
        $.ajax({
            type:"post",
            url:"/index.php/emceeno/emceeno_buyEmceeNo/emceeno/"+num+"/",
            dataType:'json',
            data:"",
            success:function(data){
                if(data["state"]==4){
                    str='<p class="txt">对不起，此号码已被别人抢购！</p>\
                    <p class="txt"><span  class="sendBtn" onclick="Manage.closeBox();">确定</span></p>\
                    ';
                }else if(data["state"]==0){
                    str='<p class="txt">恭喜，抢购成功！</p>\
                    <p class="txt"><span  class="sendBtn" onclick="Manage.reload();">确定</span></p>\
                    ';
                    Manage.changeNum(parseInt(num.toString().length));

                }else if(data["state"]==3){
                    str='<p class="txt">恭喜，抢购成功！您现在使用的号码是'+num+'</p>\
                    <p class="txt"><span  class="sendBtn" onclick="Manage.closeBox();">确定</span></p>\
                    ';
                    //alert(num+"===="+num.toString().length+"=="+typeof(num));
                    $("#MyNums").html(num);
                    Manage.changeNum(parseInt(num.toString().length));
                }else if(data["state"]==1){
                    str='<p class="txt">秀币不足，<a href="/my/charge/" target="_blank" class="c2">点击充值</a></p>\
                    <p class="txt"><span  class="sendBtn" onclick="Manage.closeBox();">确定</span></p>\
                    ';
                }else if(data["state"]==2){
                    str='<p class="txt">请登录后操作</p>\
                    <p class="txt"><span  class="sendBtn" onclick="Manage.closeBox();">确定</span></p>\
                    ';
                }else if(data["state"]==5){
                    str='<p class="txt">该靓号不存在</p>\
                    <p class="txt"><span  class="sendBtn" onclick="Manage.closeBox();">确定</span></p>\
                    ';
                }
                $("#goodnum-tip").html(str);
            }
        });
    },
    CollectRoom:function(obj){ //收藏房间
        if(_show.userId<0){ //未登录弹出登陆框
           UAC.openUAC(0);
           return false;
        }
        var that=$(obj);
        playid=_show.emceeId;
        var state=that.attr('state');
        if(state==0){
            url="/my/bookmark_add/emceeid/"+playid+"/";    
        }else{
            url="/my/bookmark_cancle/emceeid/"+playid+"/";
        }
        $.ajax({
            type:"post",
            url:url,
            dataType:'json',
            data:"t="+Math.random(),
            success:function(data){
                if(data["state"]==0){
                    if(data["op"]=="cancle"){
                        _alert('房间收藏成功!',3);
                        that.html('取消收藏');
                        that.attr('state',1);
                    }else if(data["op"]=="repeat"){
                        _alert('该房间已经收藏!',3);
                        that.html('取消收藏');
                        that.attr('state',1);
                    }else{
                        _alert('取消收藏成功!',3);
                        that.html('收藏房间');
                        that.attr('state',0);    
                    }
                }else{
                    _alert('系统出错，请稍后再试试!',3);
                    return false;
                }
            }
        })
    },
    SetchatPublic:function(obj){ //设置公告 私聊/公聊
        var that=$(obj);
        var playid=_show.emceeId;
        var flag=that.attr('state');
        $.ajax({
            type:"post",
            url:"/my/setPublicChat/eid/"+playid+"/flag/"+flag+"/",
            dataType:'json',
            data:"t="+Math.random(),
            success:function(data){
                    if(data.state==0){
                        _alert('公聊室关闭!',5)
                        that.attr('state',1);
                        that.html('公聊室关闭<cite class="off"></cite>');
                        $("#chat_close").show();
                        _show.is_public=0;
                        Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"SetchatPublic","state":"0"}');
                    }
                    else if(data.state==1){
                        _alert('公聊室开启!',5);
                        that.attr('state',0);
                        that.html('公聊室开启<cite class="on"></cite>');
                        $("#chat_close").hide();
                        _show.is_public=1;
                        Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"SetchatPublic","state":"1"}');
                        return false;
                    }
                    else if(data.state==2){
                        _alert("直播未开始！",5);
                    }
                    else 
                        _alert("操作失败！",5);
                    
                
                    
            }
        });
    },
    SetGameOAO:function(object){//设置游戏开关
        var that = $(object);
        var playid=_show.emceeId;
        var flag=that.attr('state');
        var url_post = "showadmin.do?m=setEggGameOAO&emceeId="+playid+"&flag="+flag;
        $.ajax({
            type:"post",
            dataType:"json",
            url:url_post,
            success:function(response){
                if(response.state==0){
                    _alert('游戏关闭!',5)
                    that.attr('state',1);
                    $("#shezhi-modle").hide();
                }else if(response.state==1){
                    _alert('游戏开启!',5);
                    that.attr('state',0);
                    $("#shezhi-modle").hide();
                }else{
                    _alert("操作失败！",5);
                    $("#setGameFail").show();
                }
            }
        });
    },
    AttentionTo:function(obj){
            if(_show.userId<0){ //未登录弹出登陆框
               UAC.openUAC(0);
               return false;
            }
            var that=$(obj);
            var intState=that.attr('state');    
            var attentionUrl;
            //关注1，取消2
            if(intState==1){upurl="/my/interest/";}else{upurl="/my/cancelInterest/";}
            var attentionID=_show.emceeId;
            $.ajax({
                type:"post",
                url:upurl,
                data:{"uid":attentionID,"t":Math.random()},
                success:function(response){
                  if(response!=0){
                        if(intState=='1'){
                            that.attr("state",2);
                            that.text("取消关注");
                        }else{
                            that.attr("state",1);
                            that.text("+ 关注");
                        }
                  }
                }
            });
    }
}