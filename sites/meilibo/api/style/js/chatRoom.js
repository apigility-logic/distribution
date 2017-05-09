
/*聊天*/
var Chat={
    msgLen:0,
    scrollChatFlag:1,
    is_private:0, //是否私聊
    tempMsg:"",
    gift_swf:"",
    chat_max_text_len:200,
    fly_max_text_len:40,
    userlengthcontrol:10,
    toGiftInfo:"",
    arrGiftInfo:[],
    videoTimer:null,
    arrChatModel:["gift_model","gift-givenum","playerBox","playerBox1","gift_name","gift_num","gift_to","msg_to_all","ChatFace","showFaceInfo","get_sofa","user_sofa","hoverPerson","msgGb","scroll_lb","btnsubmit","guan","showFaceInfoGb","ChatFaceGb"],
    clearChat:function(flag){
        if(flag=='pulic'){
           Chat.msgLen=0;
           $("#chat_hall").empty();
        }else if(flag=='private'){
           $('#chat_hall_private').find('p').remove();
        }
    },    
    
    closeTopBar:function(modelID){
        $('#'+modleID).hide();
    },
    richLevel:function(uid){
        var user=$('#online_'+uid);
        var rich=user.attr('richlevel');
        if(!rich){rich=0;}
        return rich;
    },
    scrollChat:function(){
        if(Chat.scrollChatFlag==1){
            Chat.scrollChatFlag=0;
            $("#scrollSign").text("解屏");
            $(".gunping").addClass("winlock_on").removeClass("winlock");
            $("#chat_hall").scrollTop($("#upchat_hall").scrollTop());
        }else{
            Chat.scrollChatFlag=1;
            $("#scrollSign").text("锁屏");
            $(".gunping").addClass("winlock").removeClass("winlock_on");
            var scrojh=$("#upchat_hall")[0].scrollHeight;
            $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
            
        }
    },
    turnPrivateChat:function(){
        if(Chat.scrollChatFlag==1){
            Chat.scrollChatFlag=0;
            $("#privateSign").text("解屏");
            $(".gunping2").addClass("winlock_on").removeClass("winlock");
            $("#sm_chat").scrollTop($("#chat_hall_private").scrollTop());

        }else{
            Chat.scrollChatFlag=1;
            $("#privateSign").text("锁屏");
            $(".gunping2").addClass("winlock").removeClass("winlock_on");
            var scrojh=$("#chat_hall_private")[0].scrollHeight;
            $("#sm_chat").scrollTop($("#chat_hall_private").scrollTop(scrojh));
            
        }    
    },
    setDisabled: function(n) {
        $("#btnsay").attr("disabled","disabled");
        $("#btnsay").attr("class","say sayoff");
        setTimeout(function(){
            $("#btnsay").attr("disabled",false);
            $("#btnsay").attr("class","say sayon");
            $("#msg").focus();
        },n*80);
    },
        dosendFly:function(){//飞屏
            if(_show.userId<0){
                UAC.openUAC(0);
                return false;
            }
            // if(_show.enterChat==0){//没有进入chat
            //     _alert('连接异常请等待！',3);
            //     return false;    
            // }    
         
            var touid=$("#to_user_id").val(),toname=$('#to_nickname').val(),fmsg=Face.deimg($("#msg").val()),eid=_show.emceeId;
            if(fmsg.length>this.fly_max_text_len){
                _alert('您的飞屏内容过长,请确保不超过40个汉字！',3);
                $("#msg").focus();    
                return false;
            }
            // if(touid==""){touid=0;}
            // if(!fmsg){
            //     _alert('请输入内容！',3);
            //     $("#msg").focus();
            //     return false;
            // }
            var wval=$("#msg").val();
            wval=wval.substr(0,this.chat_max_text_len).trim();
            if (wval[wval.length-1] == '\\') {
                wval = wval.substr(0,wval.length-1);
            }
            if(!wval){
                _alert('请输入内容！',3);
                return false;
            }
            $("#msg").focus();
            $("#msg").val('');
            if(confirm("每条飞屏将花费您1000秀币，请确认是否发送？")){
               var url="/index.php/Show/dosendFly/eid/"+eid+"/toid/"+touid+"/toname/"+encodeURIComponent(toname)+"/fmsg/"+encodeURIComponent(fmsg)+"/t/"+Math.random();
               $.getJSON(url,function(data){
                   //alert(fmsg);
                  if(data && data.code==0){
                    var to_user_id=$("#to_user_id").val();
                    var to_nickname=$("#to_nickname").val();
                    var to_goodnum=$("#to_goodnum").val();
                    if(to_user_id=="" && to_nickname==""){ //公聊
                        //Dom.$swfId("flashCallChat").chatToSocket(0,2,'{"_method_":"SendPubMsg","ct":"'+wval+'","checksum":""}');
                        Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"SendPubMsg","fly":"FlyMsg", "client_name":"'+$("#user_info_nickname").text()+'", "content":"' + wval + '","checksum":""}');
                    }else{
                        if($("#whisper").attr("checked") == "checked"){ // 别人看不到(私聊)
                            Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"SendPrvMsg","fly":"FlyMsg", "to_client_id":"'+to_user_id+'","to_client_name":"'+to_nickname+'","tougood":"'+to_goodnum+'","content":"'+wval+'","pub":"0","checksum":""}');
                        }else{
                            //在公聊区域显示 大家都能看到(悄悄)
                            Dom.$swfId("flashCallChat")._chatToSocket(1,2,'{"_method_":"SendPrvMsg","fly":"FlyMsg", "to_client_id":"'+to_user_id+'","to_client_name":"'+to_nickname+'","tougood":"'+to_goodnum+'","content":"'+wval+'","pub":"1","checksum":""}');
                        }
                        $("#to_user_id").val('');
                        $("#to_nickname").val('');
                        $("#to_goodnum").val('');
                        $("#msg_to_one>span").text('');
                        $("#msg_to_one").hide();
                        $("#msg_to_all").show();
                        $("#whisper").attr("checked" ,false);
        
                    }
                    // Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"SendPubMsg","client_name":"'+$("#user_info_nickname").text()+'", "content":"' + wval + '","checksum":""}');
                    // Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"SendFlyMsg","touid":"'+touid+'","touname":"'+toname+'","ct":"'+fmsg+'"}');}else{_alert(data.info,5);
                    }
               });
            }
    },
    doSendMessage:function(){
        if(_show.userId<0){
            UAC.openUAC(0);
            return false;
        }
        if(_show.enterChat==0){//没有进入chat
            _alert('连接异常请等待！',3);
            return false;    
        }
        var w=$("#msg");
        var wval=$("#msg").val();
        var to_client_id=$("#to_user_id").val();
        var to_nickname=$("#to_nickname").val();
        var to_goodnum=$("#to_goodnum").val();
        var whisper=$("#whisper").attr("checked") ? 1 : -1;

        if(to_client_id == userInfo.client_id){
            _alert('自己不能给自己聊！',3);
            return false;
        }

        $("#msg").val('');
        $("#msg").focus();

        wval=wval.substr(0,this.chat_max_text_len).trim();
        if (wval[wval.length-1] == '\\') {
            wval = wval.substr(0,wval.length-1);
        }
        if(!wval){
            _alert('请输入内容！',3);
            return false;
        }

        /*
        if(_show.richlevel==0 && _show.admin!=1 && _show.emceeId!=_show.userId && _show.sa!=1){
            if(wval.length>this.userlengthcontrol){
                  _alert('富豪等级1以下用户发言不能超过10个字！快快升级吧！',5);
                  return false;
            }
        }
        */
        
        /*
        if(_show.is_public=="0" && whisper==-1){ //关闭私聊
            if(_show.emceeId!=_show.userId && _show.admin=="0" && _show.sa=="0"){ //是普通、游客类型的用户
                _alert('房间公聊已关闭！',3);
                return false;    
            }                
        }
        */
        
        //var rich=Chat.richLevel(_show.userId);//Chat.richLevel()
        //if(rich>0){Chat.setDisabled(2);}else{Chat.setDisabled(5);}
        _vc = typeof(_vc)==undefined ? "" : _vc;
        // action  0:公聊  1 悄悄  2 私聊
        if(_vc==""){
            var rich=Chat.richLevel(_show.userId);
            if(rich>0){Chat.setDisabled(3);}else{Chat.setDisabled(5);}
            if(to_client_id=="" && to_nickname==""){ //公聊
                //Dom.$swfId("flashCallChat").chatToSocket(0,2,'{"_method_":"SendPubMsg","ct":"'+wval+'","checksum":""}');
                Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"SendPubMsg","client_name":"'+$("#user_info_nickname").text()+'", "content":"' + wval + '","checksum":""}');
            
            }else{
                var to_user_id = null;
                if ($('.viewer_list>li[client_id='+to_client_id+']').length != 0) {
                    to_user_id = $('.viewer_list>li[client_id='+to_client_id+']').attr('id').split('_')[1]
                } else if (managedClientUser.hasOwnProperty('user_id')) {
                    to_user_id = managedClientUser.user_id;
                }
                if (to_user_id == null) {
                    _alert('参数错误，请重新选择', 3);
                    return ;
                }

                if(whisper==1){ // 别人看不到(私聊)
                    Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"SendPrvMsg", "to_user_id":"'+to_user_id+'", "to_client_id":"'+to_client_id+'","to_client_name":"'+to_nickname+'","tougood":"'+to_goodnum+'","content":"'+wval+'","pub":"0"}');
                }else{
                    //在公聊区域显示 大家都能看到(悄悄)
                    Dom.$swfId("flashCallChat")._chatToSocket(1,2,'{"_method_":"SendPrvMsg", "to_user_id":"'+to_user_id+'", "to_client_id":"'+to_client_id+'","to_client_name":"'+to_nickname+'","tougood":"'+to_goodnum+'","content":"'+wval+'","pub":"1"}');
                }
            }
        }else{
            var cts = $("#ChatWrap").css({"top":"-114px","display":"block"});
            if(to_client_id=="" && to_nickname==""){ //公聊
                Dom.$swfId("flashCallChat").chatVerificationCode(0, 2, '{"_method_":"SendPubMsg", content":"'+wval+'","checksum":""}', _vc);
            }else{
                var to_user_id = null;
                if ($('.viewer_list>li[client_id='+to_client_id+']').length != 0) {
                    to_user_id = $('.viewer_list>li[client_id='+to_client_id+']').attr('id').split('_')[1]
                } else if (managedClientUser.hasOwnProperty('user_id')) {
                    to_user_id = managedClientUser.user_id;
                }
                if (to_user_id == null) {
                    _alert('参数错误，请重新选择', 3);
                    return ;
                }

                if(whisper==1){ // 别人看不到(私聊)
                    Dom.$swfId("flashCallChat").chatVerificationCode(2, 2, '{"_method_":"SendPrvMsg","to_user_id":"'+to_user_id+'", "to_client_id":"'+to_client_id+'","to_client_name":"'+to_nickname+'","content":"'+wval+'","pub":"0"}', _vc);
                }else{
                    //在公聊区域显示 大家都能看到(悄悄)
                    Dom.$swfId("flashCallChat").chatVerificationCode(1, 2, '{"_method_":"SendPrvMsg","to_user_id":"'+to_user_id+'", "to_client_id":"'+to_client_id+'","to_client_name":"'+to_nickname+'","content":"'+wval+'","pub":"1"}', _vc);
                }
            }
        }
        $("#to_user_id").val('');
        $("#to_nickname").val('');
        $("#to_goodnum").val('');
        $("#msg_to_one>span").text('');
        $("#msg_to_one").hide();
        $("#msg_to_all").show();
        $("#whisper").attr("checked" ,false);
        managedClientUser = {};
    },
    //20141124
    doSendMessage2:function(){
        Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"pong"}');
    },
    submitForm:function(evt){
        var evt=evt?evt:(window.event?window.event:null);
        if(evt.keyCode==13 || (evt.ctrlKey && evt.keyCode==13) || (evt.altKey && evt.keyCode==83)){
                if($("#btnsay").attr("disabled")!="disabled"){
                    Chat.doSendMessage();
                }
        }
    }
    ,getUserBalance:function(){//用户秀币更新
         var url="/index.php/Show/show_getUserBalance/t/"+Math.random();
         $.getJSON(url,function(json){
                if(json){
                    if(json["code"]=="0"){$('.others .red').html(json["value"].replace(/^(\d*)\.\d+$/,"$1"));}
                }
                    
           });    
    },
    getRankByShow:function(){ //更新本场排行榜
        var showId=_show.showId;
        if(showId=="0"){
            $('#thistop').html('<div><li class="title"><span class="t1">排名</span> <span class="t2">本场粉丝</span> <span class="t3">贡献值</span> </li></div>');
            return;
        }
        $.getJSON("/index.php/Show/show_getRankByShow/showId/"+showId+"/",{random:Math.random()},
        function(data) {
            var obj_tmp=$("<div></div>");
            obj_tmp.append('<li class="title"><span class="t1">排名</span><span class="t2">本场粉丝</span><span class="t3">贡献值</span></li>');
            if(data && data.length>0){
                _show.local=data[0].userid; //本场皇冠 userid
                for(i=0; i<data.length; i++) {
                    var obj_li = $("<li></li>");
                    obj_li.append("<em>" + (i+1) + "</em>");
                    var obj_div_pepole = $('<div class="pepole"></div>');
                    obj_div_pepole.append('<div class="img"><a href="/' + data[i].emceeno + '" target="_blank"><img src="' + data[i].icon + '" /></a></div>');
                    var obj_div_txt = $('<div class="txt"></div>');
                    obj_div_txt.append('<p><span class="cracy cra' + data[i].fanlevel + '"></span></p>');
                    obj_div_txt.append('<p><a href="/' + data[i].emceeno + '" title="' + data[i].nickname + '" target="_blank">' + data[i].nickname + '</a></p>');
                    obj_div_pepole.append(obj_div_txt);
                    obj_li.append(obj_div_pepole);
                    obj_li.append('<span class="nums">' + data[i].amount + '</span>');
                    obj_tmp.append(obj_li);
                    $('#thistop').html(obj_tmp.html());
                }
            }
        });
    },
    checkVideoLive:function(){ //client 检测是否在直播
       if(_show.emceeId!=_show.userId){ //不是主播
          if(_show.enterChat==0){ //未进入聊天
              $.getJSON("/show_checkVideoLive_rid="+_show.emceeId+Sys.ispro+".htm?t="+Math.random(),function(json){
                    if(json){
                        var str="";
                        if(json["data"]["showId"]>0){//正在直播状态
                           
                           JsInterface.beginLive(json["data"]);
                        }else{ //结束直播状态
                           
                           JsInterface.endLive();
                        }
                    }
               });
              }
              else{
                  clearInterval(Chat.videoTimer);
              }
       }
      
    }
}

/*送礼物接口  刘俊*/

var GiftCtrl={
    gift_to_id:'',
    gift_id:'', 
    choiceGift:function(giftid,giftName) {
           $(".giftItem").removeClass("giftbg");
           $("#giftbg"+giftid).addClass("giftbg");
          
        GiftCtrl.gift_id=giftid;
        GiftCtrl.gift_name=giftName;
        $("#giftname").html(GiftCtrl.gift_name);
        $('#gift_model').hide();
        if($("#giftnum").val()=="") {
            $("#giftnum").val(1);
        }
        
    },
    setGift:function(user_id,user_nick){
        GiftCtrl.gift_to_id=user_id;
        $("#giftto").html(Face.de(user_nick));
        $("#playerBox").toggle();
        $("#show_gift_user_list_btn").attr("class","btn_down");
        Gift_obj.left=$('#gift_name').offset().left;
        Gift_obj.top=$('#gift_name').offset().top;
        if($('#giftname').html()==''){$('#gift_model').css({"left":(Gift_obj.left-56)+"px","top":((Gift_obj.top)-234)+"px"}).show();}
        $("#choose_btn").attr("className","btn_up");
        
    },
    setUser:function(user_id,user_nick){
        $('#msg_to_all,#playerBox1').hide();
        $('#msg_to_one').show();
        $('#whisper').get(0).disabled=false;
        $('#msg_to_one').find('span').html(Face.de(user_nick));
        $('#to_user_id').val(user_id);
        GiftCtrl.gift_to_id=user_id;
        $('#to_nickname').val(user_nick);
    },
    closeToWho:function(){
        $("#to_user_id").val("");
        $("#to_nickname").val("");
        $('#whisper').get(0).disabled=true;
        $("#whisper").attr("checked",false);
        
        $("#msg_to_all").show();
        $("#msg_to_one").hide();
        $("#msg").focus();
    },
    giftNum:function(num){
        var gnum=parseInt(num);
        $("#show_num_btn").attr("class","btn_down");
        $("#gift-givenum").toggle();
        $("#giftnum").val(gnum);
    },
    giftNumDIY:function(){
        $("#show_num_btn").attr("class","btn_down");
        $("#gift-givenum").toggle();
        $("#giftnum").val("");
        $("#giftnum").focus();
    },
    realizeWish:function(uid,uname){//帮他实现愿望
        $(document).scrollTop(200);
        GiftCtrl.gift_to_id=uid;
        $("#giftto").html(uname);
        $("#choose_btn").attr("class", "btn_up");
        Gift_obj.left=$('#gift_name').offset().left;
        Gift_obj.top=$('#gift_name').offset().top;
        $('#gift_model').css({"left":(Gift_obj.left-56)+"px","top":((Gift_obj.top)-234)+"px"}).show();
    },
//新增加礼物接口
    kaichang:function(lie,giftName,uid){
        return ;
GiftCtrl.gift_id=lie;
GiftCtrl.gift_name=giftName;
gift_to_id=uid;
        $("#gift_model").hide();
        if(_show.userId<0){
            UAC.openUAC(0);
            return false;
        }
    /*    if(_show.enterChat==0){ //没有进入chat
            _alert('连接异常请等待！',3);
            return false;    
        }
*/
        var giftNum=1;
        var re=/^[\d]+$/;
        if(GiftCtrl.gift_id){
            if(re.test(giftNum)&&parseInt(giftNum)>0){
                if(GiftCtrl.gift_to_id != undefined){
                    var url="/index.php/Show/show_sendGift/eid/"+_show.emceeId+"/toid/"+GiftCtrl.gift_to_id+"/count/"+giftNum+"/gid/"+GiftCtrl.gift_id+"/kk/kc"+"/t/"+Math.random();
                    var tmpgid=GiftCtrl.gift_id;
                    GiftCtrl.clearGiftCfg();
                    $.getJSON(url,function(json){
                        
                        if(json){
                            if(json.code==0){
                               GiftCtrl.gift_to_id=_show.emceeId;
                               $('#giftto').html(_show.emceeNick);
                             
                               Chat.getUserBalance();//用户秀币更新
                            }else{
                               _alert(json.info,5);
                               GiftCtrl.gift_to_id=_show.emceeId;
                               $('#giftto').html(_show.emceeNick);
            }                }
                        
                    });
}}}
//新增礼物接口结束
                },

    sendGift:function(){
        $("#gift_model").hide();
        if(_show.userId<0){
            UAC.openUAC(0);
            return false;
        }
        if(_show.enterChat==0){ //没有进入chat
            _alert('连接异常请等待！',3);
            return false;    
        }
        if (parseInt(_show.emceeId) == userInfo.user_id) {
            _alert('不能给自己送礼物!',3);
            return false;
        }
        var giftNum=$.trim($("#giftnum").val());
        var re=/^[\d]+$/;
        if(GiftCtrl.gift_id){
            if(re.test(giftNum)&&parseInt(giftNum)>0){
                if(GiftCtrl.gift_to_id){
                    var url="/index.php/Show/show_sendGift/eid/"+_show.emceeId+"/toid/"+GiftCtrl.gift_to_id+"/count/"+giftNum+"/gid/"+GiftCtrl.gift_id+"/t/"+Math.random();
                    var tmpgid=GiftCtrl.gift_id;
                    GiftCtrl.clearGiftCfg();
                    $.getJSON(url,function(json){
                        if(json){
                            if(json.code==0){
                               GiftCtrl.gift_to_id=_show.emceeId;
                               $('#giftto').html(_show.emceeNick);
                               Chat.getUserBalance();//用户秀币更新
                            }else{
                               _alert(json.info,5);
                               GiftCtrl.gift_to_id=_show.emceeId;
                               $('#giftto').html(_show.emceeNick);
                            }
                        }
                    });
                }else{
                    _alert("请选择赠送人！",3);
                    return false;
                }
            }else{
                _alert("数量错误！",3);
                $("#giftnum").focus();
                return false;
            }
        }else{
            _alert("请选择礼物！", 3);
            return false;
        }
        
        
    },
    
    sendHb:function(){
        if(_show.userId<0){
            UAC.openUAC(0);
            return false;
        }
        if(_show.enterChat==0){ //没有进入chat
            _alert('连接异常请等待！',3);
            return false;    
        }
        
                    var url="/index.php/Show/show_sendHb/eid/"+_show.emceeId+"/t/"+Math.random();
                    $.getJSON(url,function(json){
                        if(json){
                            if(json.code==0){
                               Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"sendHb","userNo":"' + json.userNo + '","userId":"' + json.userId + '","userName":"' + json.userName + '"}');
                            }else{
                               _alert(json.info,5);
                            }
                        }
                    });
                
            
        
    },

    clearGiftCfg:function(){
        //GiftCtrl.gift_id=0;
        //$("#giftname").html('');
        $("#giftnum").val(1);
        //$("#giftto").html("");
    },
    clearSofa:function(){
      $('#getseatnum').val('');    
    }
    ,
    fetch_sofa:function(){
        if(_show.userId<0){
            UAC.openUAC(0);
            return false;
        }
        if(_show.enterChat==0){//没有进入chat
            _alert('连接异常请等待！',3);
            return false;    
        }
        var sofa_num=$('#getseatnum').val();
        var sof_id=parseInt($('#sofaid').val());
        var expsofa=/^([0-9])+$/;
        if(!expsofa.test(sofa_num) || sofa_num==""){
            _alert('请输入正确的数量',3);
            $('#getseatnum').val('');
        }else if(parseInt(sofa_num)<=_show.oldseatnum){
            _alert('您的沙发不够,请加油!',3);
            $('#getseatnum').val('');
        }else{
           GiftCtrl.clearSofa();
           var url="/index.php/Show/show_takeSeat/seatid/"+sof_id+"/count/"+sofa_num+"/emceeId/"+_show.emceeId+Sys.ispro+"/t/"+Math.random();
           $.getJSON(url,function(json){
                   if(json){
                       if(json.code==0){
                        Chat.getUserBalance();//用户秀币更新
                           _alert("抢座成功！",3);
                           //alert(_show.userId);
                        // Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"fetch_sofa","userNick":"' + json.userNick + '","userIcon":"' + json.userIcon + '","seatId":"' + json.seatId + '","seatPrice":"' + json.seatPrice + '"}');
                       }
                       else{
                           _alert(json.info,3);
                       }
                       $("#get_sofa").hide();
                   }
                       
           });
        }
    }
    ,giftList:function(){ //礼物列表
            var intShow=_show.showId;
          if(intShow>0){
                var giftList=new Array();
                $.getJSON("/index.php/Show/show_getgiftList/showID/"+intShow+"/t/"+Math.random(),
                function(json){
                    if(json){
                        $.each(json["giftList"],
                        function(i,item){
                            giftList.push('<li>');
                            giftList.push('<span>' +item['giftcount']+ '个</span>');
                            giftList.push('<img src="'+item['giftpath']+'" width="24" height="24" title="' + item['giftname'] + '">');
                            giftList.push('<em>' + item['giftname'] + '</em>')
                            giftList.push('<a href="javascript:void(0);" title="'+item["username"]+'">'+ (i+1)+ '. ' +item['username'] + '</a>');
                            giftList.push('</li>');
                        });
                    }
                    $("#gift_history").html(giftList.join(""));
                });
          }
    }
}

/* 宠物 */
var Pet={
    skill:function(fn){
        if(UserListCtrl.user_id==_show.userId){
            _alert("不能对自己操作哦！",3);
            return false;
        }else{
            $.getJSON("showPet.do?m=skill",{
                func:fn,
                zid:_show.emceeId,
                toid:UserListCtrl.user_id,
                timeout:5,
                t:Math.random()
            },function(json){
                if(json){
                    if(json["code"]!=0){
                        _alert(json["info"],3);
                        return false;
                    }else{
                        _alert("操作成功！",3);
                    }
                }
            });
        }
    }
}


var Song={
    intMiddle:'',
    userSureVodSongid:'',
    initVodSong:function(){
        var strSong="";
        var url="/index.php/Show/show_listSongs/eid/"+_show.emceeId+"/t/"+Math.random();
        if (_show.emceeId==_show.userId){
            $.getJSON(url,function(json){
                Song.displayShowSong(json,1);
            });
        }else{
            $.getJSON(url,function(json){
                Song.displayShowSong(json,2);
            });
        }
    },
    userVodSong:function(page){
        $('.p-Song').hide();
        page=page||1;
        $.getJSON("/index.php/Show/show_showSongs/eid/"+_show.emceeId+"/p/"+page+Sys.ispro+"/t/"+Math.random(),function(json){Song.displaySongs(json);});
    },
    userAddSong:function(){
        var songName=$.trim($("#songName").val());
        var songSinger=$("#songSinger").val();
        if(songName=='' || songName=='歌曲名(必选)'){
            _alert("请填写歌曲名称！",10);
            return false;
        }
    },
    batchAddSong:function(){
        $('.p-Song').hide();
        this.intMiddle=getMiddlePos('addSong');    
        $('#addSong').css({"left":(this.intMiddle.pl)+"px","top":(this.intMiddle.pt)+"px"}).show();
    },
    saveBatchSong:function(){
        var url="/index.php/Show/show_addSongs/eid/"+_show.emceeId;
        var song1=$("#name_1").val().trim();
        var song2=$("#name_2").val().trim();
        var song3=$("#name_3").val().trim();
        var song4=$("#name_4").val().trim();
        var song5=$("#name_5").val().trim();
        var song=(song1==""?"":("/name_1/"+encodeURIComponent(song1)+"/singer_1/"+encodeURIComponent($("#singer_1").val().trim())))+
                 (song2==""?"":("/name_2/"+encodeURIComponent(song2)+"/singer_2/"+encodeURIComponent($("#singer_2").val().trim())))+
                 (song3==""?"":("/name_3/"+encodeURIComponent(song3)+"/singer_3/"+encodeURIComponent($("#singer_3").val().trim())))+
                 (song4==""?"":("/name_4/"+encodeURIComponent(song4)+"/singer_4/"+encodeURIComponent($("#singer_4").val().trim())))+
                 (song5==""?"":("/name_5/"+encodeURIComponent(song5)+"/singer_5/"+encodeURIComponent($("#singer_5").val().trim())));
        if(song!=""){
            url+=song+"/t/"+Math.random();
            $.getJSON(url,function(data){
                $("#name_1,#name_2,#name_3,#name_4,#name_5,#singer_1,#singer_2,#singer_3,#singer_4,#singer_5").val("");
                $('#addSong').hide();
                Song.displaySongs(data);
            });
        }
    },
    DelSong:function(id){
        if(!id){
            _alert("歌曲出错，请刷新再试！",3);
            return false;
        }
        if(confirm("确定要删除该歌曲！")==false){return false;}
        $.getJSON("/index.php/Show/show_delSong/eid/"+_show.emceeId+"/sid/"+id+Sys.ispro+"/t/"+Math.random(),function(json){
            if(json && json["code"]==0){
                $("#songbook_"+id).remove();
                _alert("操作成功!",3);
            }else{
                _alert("操作失败，请重试！",5);
                return false;
            }
        });
    },
    wangSong:function(page){
        if(_show.enterChat==0){//没有进入chat
            _alert('连接异常请等待！',3);
            return false;    
        }
        page=page||1;
        $('.p-Song').hide();
        var songArray=new Array();
        $.getJSON("/index.php/Show/show_showSongs/eid/"+_show.emceeId+"/p/"+page+"/t/"+Math.random(),
        function(json){
            songArray.push('<tr>');
                songArray.push('<th>日期</th>');
                songArray.push('<th>歌名</th>');
                songArray.push('<th>原唱</th>');
                songArray.push('<th>操作</th>');
            songArray.push('</tr>');
            if(json && json["code"]==0){            
                if(json["data"]){
                    $.each(json["data"]["songs"],
                    function(i,item){
                        songArray.push('<tr id="songbook_'+item['id']+'">');
                        songArray.push('<td class="mt1">'+item['createTime']+'</td>');
                        songArray.push('<td class="mt1"><div class="song_name">'+item['songName']+'</div></td>');
                        songArray.push('<td class="mt1"><div class="song_singer">'+item['singer']+'</div></td>');
                        songArray.push('<td class="mt1"><a href="javascript:void(0);" onclick="Song.vodSongPre(\''+item.songName+'\',\''+item.singer+'\','+item.id+')">点歌</a></td>');
                        
                        songArray.push('</tr>');
                    });
                }
                
                var pages=json.data.page;
                var cur=json.data.cur;
                var cols=5;
                var str="";
                if(cur>1)
                    str+="<a href=\"javascript:Song.wangSong("+(cur-1)+");\">上一页</a>";
                else
                    str+="<span>上一页</span>";
        
                var start = cur>2?cur-2:1;
                if (pages - start <= cols && start >= cols ){
                    start = pages - (cols-1);
                }
                if(start>1)
                    str+="<span onclick='javascript:Song.wangSong(1);'>1</span>";
                if(start>2)
                    str+="<em>...</em>";
                var end=pages;
                for(i = start; i < start+cols && i<= pages; i++){
                    end=i;
                    if(i==cur)
                        str+="<span class=\"cur\">"+i+"</span>";
                    else
                        str+="<a href=\"javascript:Song.wangSong("+i+");\">"+i+"</a>";
                }
                if(pages-1>end)
                    str+="<em>...</em>";
                if(cur<pages)
                    str+="<a href=\"javascript:Song.wangSong("+(cur+1)+");\">下一页</a>";
                else
                    str+="<span>下一页</span>";
                
                $("#page2").html(str);
            }
            $("#song_table2").html(songArray.join(""));
        });
        this.intMiddle=getMiddlePos('song_dialog2');
        $('#song_dialog2').css({"left":(this.intMiddle.pl)+"px","top":"10%"}).show();
    }
    ,
    vodSongPre:function(songName,singer,id){
        if(!songName){
            _alert("歌曲出错，请刷新再试！",3);
            return false;
        }
        $("#songName").val(songName);
        $("#songSinger").val(singer);
        $("#songId").val(id);
        
        var txt="点歌需"+_show.songPrice+"个秀币,主播确认后才收取！";
        
        if(confirm(txt))
            Song.agreeDemand();
        else
            Song.disagreeDemand();
    },
    agreeDemand:function(){
        _closePop();
        Song.vodSong();
    },
    vodSong:function(){
        var songName=$("#songName").val();
        var singer=$("#songSinger").val();
        var songId=$("#songId").val();
        if (applySongFlag != 1) {
            _alert("主播已禁用点歌！", 3);
            return false;
        }
        if(songName==""){
            _alert("请先选择或输入您想要点播的歌曲名！",3);
            return false;
        }
        if (userInfo.user_id == -1) {
            _alert("游客不能点歌！",3);
            return false;
        }
        songName=encodeURIComponent(songName);
        singer=encodeURIComponent(singer);
        
        $.getJSON("/index.php/Show/pickSong/songName/"+songName+"/singer/"+singer+"/songId/"+songId+"/emceeId/"+_show.emceeId+"/t/"+Math.random(),
            function(json){
                if(json && json.code==0){
                    // Song.initVodSong();
                    _alert("点歌成功，等待主播同意！",3);
                    Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"vodSong","songName":"'+songName+'"}');
                }
                else
                    _alert(json.info,3);
        });    
    },
    agreeSong:function(songId){
        if(!songId){
            _alert("请先选择歌曲！",3);
            return;
        }
        $("#song_"+songId).html("同意");
        $.getJSON("/index.php/Show/show_agreeSong/eid/"+_show.emceeId+"/ssid/"+songId+"/t/"+Math.random(),function(json){
            if(json && json.code==0){
                // $("#song_"+songId).html("已同意");
                _alert("操作成功！",3);
                Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"agreeSong","songId":"'+songId+'", "userNo":"'+json.userNo+'","userId":"'+json.userId+'","userName":"'+json.userName+'","songName":"'+json.songName+'"}');
            }
            else{
                $("#song_"+songId).html("<a onclick=\"Song.agreeSong("+songId+")\" href=\"javascript:void(0);\">等待同意</a>");
                _alert(json.info,3);
            }
        });
    },
    disAgreeSong:function(songId){
        if(!songId){
            _alert("请先选择歌曲！",3);
            return;
        }
        $.getJSON("/index.php/Show/show_disAgreeSong/eid/"+_show.emceeId+"/ssid/"+songId+"/t/"+Math.random(),function(json){
            if(json && json.code==0){
                // $("#song_"+songId).html("未同意");
                _alert("操作成功！",3);
                Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"disAgreeSong"}');
            }
        });
    },
    setSongApply:function(a){
        a=a||1;
        $.getJSON("/index.php/Show/show_setSongApply/eid/"+_show.emceeId+"/apply/"+a+"/t/"+Math.random(),function(json){
            if(json.code>0)
                _alert("操作失败，请稍后重试!",3);
                Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"setSongApply","apply":"' + a + '"}');
        });
    },
    disagreeDemand:function(){
        _closePop();
        return false;
    }
    ,
    displaySongs:function(json){
        var songArray=new Array();
    songArray.push('<tr>');
        songArray.push('<th>日期</th>');
        songArray.push('<th>歌名</th>');
        songArray.push('<th>原唱</th>');
        songArray.push('<th>操作</th>');
    songArray.push('</tr>');
    if(json && json["code"]==0){            
        if(json["data"]){
            $.each(json["data"]["songs"],
            function(i,item){
                songArray.push('<tr id="songbook_'+item['id']+'">');
                songArray.push('<td class="mt1">'+item['createTime']+'</td>');
                songArray.push('<td class="mt1"><div class="song_name">'+item['songName']+'</div></td>');
                songArray.push('<td class="mt1"><div class="song_singer">'+item['singer']+'</div></td>');
                songArray.push('<td class="mt1"><a href="javascript:void(0);" onclick="Song.DelSong('+item['id']+')">删除</a></td>');
                songArray.push('</li>');
            });
        }
        var pages=json.data.page;
        var cur=json.data.cur;
        var cols=5;
        var str="";
        if(cur>1)
            str+="<a href=\"javascript:Song.userVodSong("+(cur-1)+");\">上一页</a>";
        else
            str+="<span>上一页</span>";

        var start = cur>2?cur-2:1;
        if (pages - start <= cols && start >= cols ){
            start = pages - (cols-1);
        }
        if(start>1)
            str+="<span onclick='javascript:Song.userVodSong(1);'>1</span>";
        if(start>2)
            str+="<em>...</em>";
        var end=pages;
        for(i = start; i < start+cols && i<= pages; i++){
            end=i;
            if(i==cur)
                str+="<span class=\"cur\">"+i+"</span>";
            else
                str+="<a href=\"javascript:Song.userVodSong("+i+");\">"+i+"</a>";
        }
        if(pages-1>end)
            str+="<em>...</em>";
        if(cur<pages)
            str+="<a href=\"javascript:Song.userVodSong("+(cur+1)+");\">下一页</a>";
        else
            str+="<span>下一页</span>";
        
        $("#page").html(str);
        
    }
    $("#song_table").html(songArray.join(""));
    this.intMiddle=getMiddlePos('song_dialog');    
    $('#song_dialog').css({"left":(this.intMiddle.pl)+"px","top":(this.intMiddle.pt)+"px"}).show();
    },
    displayShowSong:function(json,type){
        var json=json;
        var userSongArray=new Array();
        if(json && json.code==0){
            $.each(json.data.songs,function(i,item){
                strSong="";
                    if(item['status']==0){
                        if(type==1){
                            strSong='<cite id="song_'+item['id']+'"><a href="javascript:Song.agreeSong('+item['id']+');"><img src="/style/images/right_icon.gif"/></a>  <a href="javascript:Song.disAgreeSong('+item['id']+');"><img src="/style/images/wrong_icon.gif"/></a></cite>';
                        }else{
                            strSong='<cite id="song_'+item['id']+'">'+item['showStatus']+'</a></cite>';
                        }
                    }else if(item['status']==1){
                        strSong='<cite id="song_'+item['id']+'" style="color: green;">'+item['showStatus']+'</cite>';
                    }else if(item['status']==2){
                        strSong='<cite id="song_'+item['id']+'" style="color: red;">'+item['showStatus']+'</cite>';
                    }
                userSongArray.push('<li id="everysong_'+item.id+'">');
                userSongArray.push('<span class="t1">'+item.createTime+'</span>');
                userSongArray.push('<span class="t2">'+item.songName+'</span>');
                userSongArray.push('<span class="t3">'+item.userNick+'</span>');
                userSongArray.push('<span class="t4">'+strSong+'</span>');
                userSongArray.push('</li>');
            });
            
            $("#usersonglist").html(userSongArray.join(""));
        }
    }
}


//
var jumpAnchor=function(){
        var _time=1000;
        if(arguments.length == 2) _time =  arguments[1];
        if ($("."+arguments[0]).length > 0)
            $("html,body").animate({scrollTop: $("."+arguments[0]).offset().top}, {duration: _time,queue: false});
}

/*特权命令操作*/
var UserListCtrl={
    user_id:'',
    nickname:'',
    Tid:'', 
    level:'',//等级
    goodnum:'',
    sendGift:function(){
        try{
            if(UserListCtrl.user_id){
                if(!in_array(UserListCtrl.user_id,Chat.arrGiftInfo) && _show.emceeId!=UserListCtrl.user_id){ //防止重复 且 不是主播
                    Chat.toGiftInfo="<li><a href=\"javascript:void(0);\" onclick=\"GiftCtrl.setGift('"+UserListCtrl.user_id+"','"+UserListCtrl.nickname+"')\"><span class=\"cracy cra"+UserListCtrl.level+"\"></span>"+UserListCtrl.nickname+"</a></li>";    
                    Chat.arrGiftInfo.push(UserListCtrl.user_id);
                    $('#gift_userlist').append(Chat.toGiftInfo);
                    $('#chat_userlist').append(Chat.toGiftInfo.replace('setGift','setUser'));
                }
                GiftCtrl.gift_to_id=UserListCtrl.user_id;
                $("#giftto").html(UserListCtrl.nickname);
                $("#choose_btn").attr("class", "btn_up");
                Gift_obj.left=$('#gift_name').offset().left;
                Gift_obj.top=$('#gift_name').offset().top;
                $('#gift_model').css({"left":(Gift_obj.left-56)+"px","top":((Gift_obj.top)-234)+"px"}).show();
                $("#giftnum").focus();
                
            } else {
                return false;
            }
        }catch(e){}
    },
    chatPublic:function(){
        try{
            if (UserListCtrl.user_id){
                $("#to_user_id").val($("#online_"+UserListCtrl.user_id).attr('client_id'));
                $("#to_nickname").val(UserListCtrl.nickname);
                $("#to_goodnum").val(UserListCtrl.goodnum);
                $("#msg_to_one").html('<span>' + UserListCtrl.nickname + '</span>');
                $(".msg_to_all").hide();
                $("#msg_to_one").show();
                $("#whisper").attr("disabled",false);
                $("#whisper").attr("checked",false);
                $("#msg").focus();
            }else{
                return false;
            }
        }catch(e){}
    },
    chatPrivate:function(){
        try{
            if(UserListCtrl.user_id){
                $("#to_user_id").val($("#online_"+UserListCtrl.user_id).attr('client_id'));
                $("#to_nickname").val(UserListCtrl.nickname);
                $("#to_goodnum").val(UserListCtrl.goodnum);
                $("#msg_to_one").html('<span>' + UserListCtrl.nickname + '</span>');
                $("#whisper").attr("checked",true);
                $(".msg_to_all").hide();
                $("#msg_to_one").show();
                $("#msg").focus();
            }else{
                return false;
            }
        }catch(e){}    
    },
    givesendGift:function(){
        try{
            if(UserListCtrl.user_id){
                $("#giftto").text(UserListCtrl.nickname);
            }else{
                return false;
            }
        }catch(e){}    
    }
}
var ChatApp={
    serverID:"",
    /**
    * 根据rid uid 取出 管理员列表
    * @param rid 房间ID,uid 用户ID
    * @return json
    */
    GetManagerList:function(){}
    ,
    /**
    * 根据uidlist 踢出指定的多个用户
    * @param rid 房间ID,uid 用户ID/uidlist 被踢的用户列表 
    */
    Kick:function(){
        if(UserListCtrl.user_id==_show.userId){
            _alert("不能踢自己啊！",3);
            return false;
        }

        var client_id = $("#online_"+UserListCtrl.user_id).attr('client_id');
        Dom.$swfId("flashCallChat")._chatToSocket(0,0,'{"_method_":"Manage", "_type_":"addKicked","managed_user_id":"'+UserListCtrl.user_id+'", "managed_user_name":"'+UserListCtrl.nickname+'", "managed_client_id":"'+client_id+'"}');
    },
    /**
    * 根据uidlist 将指定的多个用户禁言
    * @param rid 房间ID,uid 用户ID/uidlist 被禁言的用户列表  timeout(禁言时间) 
    */
    ShutUp:function(){
        if (UserListCtrl.user_id==_show.userId){
            _alert("不能给自己禁言！",3);
            return false;
        }
        var client_id = $("#online_"+UserListCtrl.user_id).attr('client_id');
        Dom.$swfId("flashCallChat")._chatToSocket(0,0,'{"_method_":"Manage", "_type_":"disableMsg","managed_user_id":"'+UserListCtrl.user_id+'", "managed_user_name":"'+UserListCtrl.nickname+'", "managed_client_id":"'+client_id+'"}');
        /*
        if(UserListCtrl.user_id==_show.userId){
            _alert("不能给自己禁言！",3);
            return false;
        }else{
            $.getJSON("/index.php/Show/shutup/",{
                    rid:_show.emceeId,
                    uidlist:UserListCtrl.user_id,
                    timeout:5,
                    t:Math.random()
                },function(json){
                        if(json){
                            if(json["code"]!=0){
                                _alert(json["info"],3);
                                return false;
                            }
                            else
                                Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"ShutUpUser","tougood":"' + UserListCtrl.goodnum + '","touid":"' + UserListCtrl.user_id + '","touname":"' + UserListCtrl.nickname + '"}');
                                _alert("操作成功！",3);
                        }
                    }
            );
        }
        */
    },
    /**
    * 根据uidlist 将指定的多个用户恢复发言
    * @param rid 房间ID,uid 用户ID/uidlist 被恢复发言的用户列表
    */
    Resume:function(){
        if(UserListCtrl.user_id==_show.userId){
            _alert("不能恢复自己的发言！", 3);
            return false;
        }else{
            /*
            $.getJSON("show.do?m=resume",{
                    rid:_show.emceeId,
                    uidlist:UserListCtrl.user_id,
                    t:Math.random()
                },function(json){
                    if(json){
                        if(json["code"]!=0){
                            _alert(json["info"],3);
                            return false;
                        }else{
                            _alert("操作成功！",3);
                        }
                    }
                }
            );
            */
            var client_id = $("#online_"+UserListCtrl.user_id).attr('client_id');
            Dom.$swfId("flashCallChat")._chatToSocket(0,0,'{"_method_":"Manage", "_type_":"enableMsg","managed_user_id":"'+UserListCtrl.user_id+'", "managed_user_name":"'+UserListCtrl.nickname+'", "managed_client_id":"'+client_id+'"}');
            // Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"ResumeUser","tougood":"' + UserListCtrl.goodnum + '","touid":"' + UserListCtrl.user_id + '","touname":"' + UserListCtrl.nickname + '"}');
        }
    }
    ,
    setManager:function(){ //设为管理员
        if(UserListCtrl.user_id==_show.userId){
            _alert("不能对自己操作！",3);
            return false;
        }else{
            /*
            $.getJSON("/index.php/Show/toggleShowAdmin/",{
                    eid:_show.emceeId,
                    state:1,
                    userid:UserListCtrl.user_id,
                    t:Math.random()
                },function(json){
                        if(json){
                            if(json["code"]==0){
                                Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"setManager","tougood":"' + UserListCtrl.goodnum + '","touid":"' + UserListCtrl.user_id + '","touname":"' + UserListCtrl.nickname + '"}');
                                _alert('操作成功！',3);    
                            }
                            else{
                                _alert(json["info"],3);
                            }
                        }
                    }
            );
            */
            var client_id = $("#online_"+UserListCtrl.user_id).attr('client_id');
            Dom.$swfId("flashCallChat")._chatToSocket(0,0,'{"_method_":"Manage", "_type_":"adminer","managed_user_id":"'+UserListCtrl.user_id+'", "managed_user_name":"'+UserListCtrl.nickname+'", "managed_client_id":"'+client_id+'"}');
        }
    },
    setBlack:function(){ //黑名单操作
        if(UserListCtrl.user_id==_show.userId){
            _alert("不能对自己操作！",3);
            return false;
        }else{
            /*
            $.getJSON("bl.do",{
                    eid:_show.emceeId,
                    m:"setBlack",
                    userid:UserListCtrl.user_id,
                    t:Math.random()
                },function(json){
                        if(json){
                            if(json.code==0){
                                _alert(json.info,3);
                            }
                            else{
                                _alert(json.info,3);
                            }
                        }
                    }
            );
            */
        }
    },
    
    delManager:function(){ //删除管理员
        if(UserListCtrl.user_id==_show.userId){
            _alert("不能对自己操作！",3);
            return false;
        }else{
            /*
            $.getJSON("/index.php/Show/toggleShowAdmin/",{
                eid:_show.emceeId,
                state:0,
                userid:UserListCtrl.user_id,
                t:Math.random()
            },
            function(json){
                if(json){
                    if(json["code"]==0){
                        Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"delManager","tougood":"' + UserListCtrl.goodnum + '","touid":"' + UserListCtrl.user_id + '","touname":"' + UserListCtrl.nickname + '"}');
                        _alert('操作成功！',3);    
                    }
                    else{
                        _alert(json["info"],3);
                    }
                }
            });
            */
            var client_id = $("#online_"+UserListCtrl.user_id).attr('client_id');
            Dom.$swfId("flashCallChat")._chatToSocket(0,0,'{"_method_":"Manage", "_type_":"removeAdminer","managed_user_id":"'+UserListCtrl.user_id+'", "managed_user_name":"'+UserListCtrl.nickname+'", "managed_client_id":"'+client_id+'"}');
        }
    }
}

/**
 * 主播Menu SetTing
 */
var playerMenu={
    bulletin:function(t){
        var ot="#b"+t+"t";
        var ou="#b"+t+"u";
        var text=$("#b"+t+"t").val();
        var link=$("#b"+t+"u").val();
        if(text.length>40 || text.trim()=="" || text.trim()=="请输入文字,不超过40个..."){
            _alert("请输入文字,不超过40个...",5);
            return;
        }
        if(link=="请输入链接地址")
            link="";
        
        $.post("/my/setBulletin/",{
                m:"setBulletin",
                eid:_show.emceeId,
                bt:t,
                t:text,
                u:link,
                r:Math.random()
            },function(data){
                if(data.code==0){
                    $(ot).val("");
                    $(ou).val("");
                    _alert("操作成功！",3);
                    $("#notice-modle").hide();
                    Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"setBulletin","bt":"' + t + '","t":"' + text + '","u":"' + link + '"}');
                }
                else
                    _alert(data.info,5);
            },"json"
        );
    },
    offVideo:function(s){
        if(s==1){
            var addr=$("#video").val().trim();
            if(addr=="" || addr=="请输入离线地址..."){
                _alert("请输入离线地址...",5);
                return;
            }
            var url="/my/setOfflineVideo/?&url="+encodeURIComponent(addr)+"&eid="+_show.emceeId+"&t="+Math.random();
        }
        else{
            var url="/my/cancelOfflineVideo/eid/"+_show.emceeId+"/t/"+Math.random();
        }
        $.getJSON(url,function(data){
            if(data && data.code==0){
                $("#video").val("");
                $('.pop-play').hide();
                _alert("操作成功！",5);
            }
            else
                _alert(data.info,5);
        });
    },
    setBackground:function(t){
        if(t==1){
            var file=$("#bg3").val().toLowerCase();
            if(file!=""){
                if(file.indexOf(".jpg")==-1){
                    _alert("图片须为jpg格式文件！",5);
                    return;
                }
            }else{
                _alert("请选择背景图片！",5);
                return;
            }
            var f=Dom.$getid("frm");
            f.action="/my/setBackground/eid/"+_show.emceeId;
            f.target="frmFile";
            f.submit();
        }
        else{
            var url="/my/cancelBackground/eid/"+_show.emceeId+"/t/"+Math.random();
            $.getJSON(url,function(data){
                if(data && data.code==0){
                    $("body").removeAttr("style");
                    var file=$("#bg3");
                    file.after(file.clone().val(""));
                    file.remove(); 
                    _alert("操作成功！",3);
                    Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"cancelBackground"}');
                }
                else
                    _alert("操作失败，请重试！",5);
            });
        }
    },
    setBackground2:function(bg){
        Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"setBackground","bgimg":"' + bg + '"}');
    },
    enter:function(){
        var url="/index.php/Show/enterspeshow/eid/"+_show.emceeId+"/type/"+_show.deny;
        if(_show.deny==2)
            url+="/password/"+$("#room_pwd").val();
        url+="/t/"+Math.random();
        $.getJSON(url,function(json){
            if(json){
                if(json.code==0){
                    window.location.reload();
                }
                else{
                    _alert(json.info,5);
                }        
            }
        });
    },
    sel:function(i){
        $("#bg1").removeClass();
        $("#bg2").removeClass();
        $("#bg"+i).addClass("on");
        var file=$("#bg3");
        file.after(file.clone().val(""));
        file.remove(); 
        $("#bgh").val(i);    
    },
    moveroom:function(){
        var moveurl=$('#roomurl').val();
        var rexp=/^http:\/\/demo.meilibo.net\/[0-9]{1,12}$/;
        var rexp1=/^http:\/\/www.meilibo.net\/f\/[0-9]{1,12}$/;
        var rexp2=/^http:\/\/meilibo.net\/[0-9]{1,12}$/;
        if(moveurl!="" && (rexp.test(moveurl) || rexp1.test(moveurl) || rexp2.test(moveurl))){
            /*
            var urlhttp="show.do?m=shiftRoom&rid="+_show.emceeId+"&url="+encodeURIComponent(moveurl)+"&t="+Math.random();
            $.getJSON(urlhttp,function(json){
                if(json){
                    if(json.code!=0){
                        _alert(json.info,"5");
                     }
                     else{
                         _alert("操作成功！","5");
                     }    
                }
            });
            */
            Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"moveroom","url":"' + moveurl + '"}');
        }else{
            _alert("请输入正确的房间地址！",5);
        }
    }
}
