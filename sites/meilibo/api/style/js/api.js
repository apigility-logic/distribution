 /*
     * js call AS 20120306
 */
var getmsgStrs = new Array();

var Dom={
    $C:function(a){
        return document.createElement(a);
    },
    $getid:function(b){
        return document.getElementById(b);    
    }
    ,$gTag:function(c){
        return document.getElementsByTagName(c);    
    },
    $swfId:function(d){
        /*
        if(d == 'flashCallChat'){
            if ((navigator.userAgent.indexOf("Maxthon") != -1 && navigator.userAgent.indexOf("WebKit") == -1) || (navigator.userAgent.indexOf("theworld") != -1 && navigator.userAgent.indexOf("WebKit") == -1) || (navigator.userAgent.indexOf("MSIE 9.0") != -1 && navigator.userAgent.indexOf("WebKit") == -1)) { 
                return window["flashCallChat2"];
            }
            else{
                return swfobject.getObjectById(d);
            }
        }
        else{
            return swfobject.getObjectById(d);
        }
        */
        return websocketChat;
    }
}
/**
 * 聊天表情
 */
var Face={
    de:function(str){
        str=str.replace(/<br \/>/ig, '\n').replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/(\n)+/igm, "<br>").replace(/\[`(.*?)`\]/ig,"<img src=\"/style/images/face/$1.gif\"/>");
        return str;
    },
    showFace:function(){
        var objFace=$('#showFaceInfo'),chatR=$("#ChatFace"),facePos={'facel':objFace.offset().left,'facet':objFace.offset().top};
        if(chatR.is(':hidden')){
            chatR.css({"left":(facePos.facel-142)+"px","top":(facePos.facet-182-11-100)+"px"}).show();
        }else{
            chatR.hide();
        }
    },
    deimg:function(str){
        str=str.replace(/\[`(.*?)`\]/ig,"");
        return str;
    },
    addEmot:function(myValue) {
        var objEditor=$("#msg");
        objEditor.val(objEditor.val()+myValue);
        $('#ChatFace').hide();
        objEditor.focus();
    }
};
var FaceGb={
    de:function(str){
        str=str.replace(/<br \/>/ig, '').replace(/(\n)+/igm, "").replace(/\[`(.*?)`\]/ig,"<img src=\"/style/images/face/$1.gif\"/>");
        return str;
    },
    showFace:function(){

        var objFace=$('#showFaceInfoGb'),chatR=$("#ChatFaceGb"),facePos={'facel':objFace.offset().left,'facet':objFace.offset().top};
        if(chatR.is(':hidden')){
            chatR.css({"display":"block","width":"300px","left":(facePos.facel-50)+"px","top":(facePos.facet-182-11)+"px","zIndex":"1000001"});
        }else{
            chatR.hide();
        }
    },
    deimg:function(str){
        str=str.replace(/\[`(.*?)`\]/ig,"");
        return str;
    },
    addEmot:function(myValue) {
        var objEditor=$("#msgGb");
        objEditor.val(objEditor.val()+myValue);
        $('#ChatFaceGb').hide();
        objEditor.focus();
    }
};
function DecodeName(str){
    var s = str;
    if(s.length == 0){
        return "";
    }
    s = s.replace(/&amp;/g, "&");
    s = s.replace(/&lt;/g, " <");
    s = s.replace(/&gt;/g, ">");
    s = s.replace(/&nbsp;/g, " ");
    s = s.replace(/&quot;/g, "\"");
    s = s.replace(/<br>/g, "\n");
    return s;  
}
/*
* 初始化flash
* @param url 视频采集(rtmp)/视频播放(http)url
* @param roomname 房间名(采集:"Stream_"+rid;播放:"Stream_"+rid+"_"+type(视频流格式1,2,3))
* @param token 验证token
*/
function ObjvideoControl(){
    this.con_ip="vts";
    this.con_rid=_show.emceeId; //房间ID
    this.con_uid=_show.userId;  //用户ID
    this.con_moveid="JoyCamLivePlayer";
    this.flash_url='';
    this.flash_roomname='';
    this.socket_ip=_show.chat;
    this.default_ip="127.0.0.1";
    this.socket_port=80;
    this.camCount=3; 
    this.liveCount=3;
    this.camlive=0;
    this.playlive=0;
    this.chatdomain="";
}
ObjvideoControl.prototype={
    getParam:function(param){
        var livehash=window.location.hash;
        var ipos=livehash.indexOf("#");
        livehash=livehash.substr(ipos+1);
        var param=param,arrname=[],arrval=[];
        if(livehash!=""){
            var arrd=livehash.split('&');
            for(var i=0;i<arrd.length;i++){
                var arrtemp=arrd[i].split("=");
                if(arrtemp[0]==param){
                    return arrtemp[1];
                }
            }
        }
    },
    getclientNode:function(){
        var that=this,camlive=0,playlive=0,chatdomain="";
        camlive=that.getParam("cam");
        if(camlive){that.camlive=camlive;}
        playlive=that.getParam("play");
        if(playlive){that.playlive=playlive;}
        chatdomain=that.getParam("chat");
        if(chatdomain){that.chatdomain=chatdomain;}
    },
    fetchurl:function(params){
        var urldata=params;
        return     urldata.substring(0,urldata.lastIndexOf('/')+1);
    },
    fetchroomname:function(params){
        var roomnamedata=params;
        return roomnamedata.substring(roomnamedata.lastIndexOf('/')+1);
    },
    create_url_name:function(callback){
        var that=this;
        that.getclientNode();
        var camnode=that.camlive;
        this.con_url="http://"+this.con_ip+"/RequestLive?rid="+this.con_rid+"_"+_show.showId+"&uid="+this.con_uid;
        if(camnode!=0){ //read client data
            this.con_url+="&node="+camnode;
        }else if(_show.up!=0){ //read server data
            this.con_url+="&node="+_show.up;    
        }
        $.getScript(this.con_url,function(){
            var retCode=strGetcontrol.retcode;
            if(retCode=="000000"){
                that.con_data=strGetcontrol.data;    
                that.flash_url=that.fetchurl(that.con_data); //rtmp://122.227.201.65/live/ 
                that.flash_roomname="stream_"+that.con_rid+"_"+_show.showId;  //stream_1555117924    
                callback(that.flash_url, that.flash_roomname);
            }else{
              that.camCount--;
              if(that.camCount>0){//retry 3
                that.create_url_name(callback);    
              }
            }
        });
    },
    //采集
    //c :0新播  1续播
    collect_v:function(c){
        var url="";
        var name="";
        var that = this;
        if (c==1){
            that.create_url_name(function(url, name){
                $.getJSON("show_sha_eid_"+_show.emceeId+"_uid_"+_show.userId+"_t_2"+Sys.ispro+".htm?t="+Math.random(),function(ret){
                    if(ret.code==0)
                        Dom.$swfId(that.con_moveid).initialize(url,name,ret.data.tokenU,c);
                    else
                        _alert('认证失败！',3);
                });    
                
            });
        }else{
            $.getJSON("show_sha_eid_"+_show.emceeId+"_uid_"+_show.userId+"_t_2"+Sys.ispro+".htm?t="+Math.random(),function(ret){
                if(ret.code==0)
                    Dom.$swfId(that.con_moveid).initialize("","",ret.data.tokenU,c);
                else
                    _alert('认证失败！',3);
            });    
        }
        
    
    },
    //播放 0:普通观众 1：主播画面
    collect_p:function(type){
        var that=this;
        that.getclientNode();
        var playnode=that.playlive;
        this.con_url="/RequestPlayUrl?pubpoint=live/stream_"+that.con_rid+"_"+_show.showId;
        if(playnode!=0){ //read client data
            this.con_url+="&node="+playnode;
        }else if(_show.down!=0){ //read server data
            this.con_url+="&node="+_show.down;    
        }
        $.getScript(this.con_url,function(){
                var retCode=strGetPlayUrl.retcode;
                if(retCode=="000000"){
                    var Arr_playUrl=[];
                    var Arr_playName=[];
                    var strgetUrl=strGetPlayUrl["playurl"];
                    var arrUrl=strgetUrl.split("|");
                    var intplay=arrUrl.length-1;
                    for(var i=0;i<intplay;i++){
                        Arr_playUrl.push(that.fetchurl(arrUrl[i]));
                        if(_show.showId>0){ //正在直播
                            if(_show.closed==0)
                                Arr_playName.push(that.fetchroomname(arrUrl[i]));
                            else
                                Arr_playName.push("loading");
                        }else{//没有直播
                            Arr_playName.push("");    
                        }
                    }
                    $.getJSON("show_sha_eid_"+_show.emceeId+"_uid_"+_show.userId+"_t_3"+Sys.ispro+".htm?t="+Math.random(),function(json){
                        if(json && json.code==0){
                            if(type==0){
                                Dom.$swfId(that.con_moveid).showPromotion(_show.titlesUrl,_show.titlesLength);
                            }
                            Dom.$swfId(that.con_moveid).initialize(Arr_playUrl,Arr_playName,json.data.tokenV);
                        }
                        else{
                            _alert("认证失败！",3);
                            return;
                        }
                    });            
                }else{
                  that.liveCount--;
                  if(that.liveCount>0){//retry 3
                    that.collect_p(type);    
                  }
                }
        })
    }
}


var JsInterface={
    getMsgstrs:[],
    inCount:0,
    person:"",
    arrManage:[],
    arrPeople:[],
    arrVisitor:[],
    cntManage:0,
    cntPeople:0,
    guePeople:0,     //guest
    liveTimer:null,
    minCount:500,
    initnum:0,
    inf:0,
    inf2:0,
    ing:0,
    ing2:0,
    isAll:0,
    minorder:0,
    initLogin:0,
    flush:function(){
        window.location.reload();    
    },
    giftloading:0 //礼物flash装载状态 1 成功  0未成功
    ,
    callActiveX:function(fobj){ //检测插件video:视频设备名,audio:音频视频名,url:rtmp服务器路径
        try{
                var Afunc=fobj.func;
                var cmd="<setting company='joy'><video_dev>"+fobj.video+"</video_dev>";
                cmd+="<audio_dev>"+fobj.audio+"</audio_dev>";
                cmd+="<servers><vpush>"+encodeURIComponent(fobj.url)+"</vpush></servers>";
                cmd+="</setting>";
                switch(Afunc){
                    case "ready":
                       document.all.VideoStudioControl.InvokeCommand(cmd);
                       break;
                    case "publish":
                       document.all.VideoStudioControl.InvokeCommand(cmd);
                       document.all.VideoStudioControl.InvokeCommand("<joystartcatpure/>");
                       break;
                    case "close":
                       document.all.VideoStudioControl.InvokeCommand("<videoclose/>");
                       break;
                }
        }catch(e){
            alert('Error!');
        }
    },
    /**
    * 设置房间类型
    * @param type 房间类型(0:普通房间,1:付费房间,2:加密房间)
    * @param add 房间所需金额/房间密码
    * @return true/false; function setRoomType(type:int,add:String="");
    */
    setRoomType:function(type,add){
        var ptype=type,padd=add,roomAPI="";
        if(padd!=""){
            roomAPI="show_beginLiveShow_roomtype_"+ptype+"_add_"+padd+"_isHD_"+_show.isHD+".htm";
        }else{
            roomAPI="show_beginLiveShow_roomtype_"+ptype+"_isHD_"+_show.isHD+".htm";    
        }
        this.liveTimer=setTimeout(function(){
            $.getJSON(roomAPI+"?t="+Math.random(),
                function(json){
                    if(json){
                        if(json.code==0){
                            _show.showId = json.showId;
                            var Camlive=new ObjvideoControl();
                            Camlive.create_url_name(function(url, name){
                                $.getJSON("show_sha_eid_"+_show.emceeId+"_uid_"+_show.userId+"_t_2"+Sys.ispro+".htm?t="+Math.random(),function(ret){
                                    if(ret.code==0)
                                        Dom.$swfId("JoyCamLivePlayer").setRoomTypeSuccess(true,ret.data.tokenU,url,name);
                                    else
                                        _alert('认证失败！',3);
                                });    
                                
                            });        
                        }else{
                            Dom.$swfId("JoyCamLivePlayer").setRoomTypeSuccess(false);    
                            _alert(json.msg,3);
                            return false;
                        }
                    }
            });                                       
        },3000);
    },
    /**
     *主播开始直播前检查是否需要续播
    */
    getReplay:function(){
        $.getJSON("show_replay.htm?t="+Math.random(),function(json){
            if(json && json.code==0 && json.data["continues"]==1){
                Dom.$swfId("JoyCamLivePlayer").setReplay(true);
            }
            else{
                Dom.$swfId("JoyCamLivePlayer").setReplay(false);
            }
        });
    },
    /**
    * 弹出窗口显示观众画面 function showGuest();
    */
    showGuest:function(){
            //观众画面
            if(!objC){
                if($("#myVideoBox").length<=0){
                    var objG=Dom.$C("div");
                    objG.id="myVideoBox";
                    objG.className="previewMic";
                    objG.innerHTML='<h5 id="dragguest">预览<em onclick="JsInterface.closeMyvideo();">关闭</em></h5>';
                    document.body.appendChild(objG); 
                    objG.style.display='block';
                    var objC=Dom.$C("div");
                    objC.id="JoyShowLivePlayer";
                    objC.className="myVideoFlash";
                    Dom.$getid('myVideoBox').appendChild(objC);
                    var s=Dom.$C("script");
                    s.text="swfobject.embedSWF(\"/style/swf/liveShowLivePlayer.swf?roomId="+_show.goodNum+"&rtmpHost="+_show.chat+"&cdn="+_show.cdnl+"&keyframe="+_show.zjg+"&height="+_show.height+"&width="+_show.width+"&fps="+_show.fps+"&bandwidth="+_show.zddk+"&quality="+_show.pz+"&rtmpPort=1935&appName="+_show.appName+"&stream="+_show.stream+"\", \"JoyShowLivePlayer\", 428,316,\"10.0\", \"\", {},{wmode:\"transparent\",allowscriptaccess:\"always\"});guestflashSwf();JsInterface.guestdrag(\"myVideoBox\",\"dragguest\");";
                    Dom.$getid('myVideoBox').appendChild(s);
                }else{
                    $('#myVideoBox').css("display","block");
                }
            }
    },
    /**
    * 返回直播大厅 function backLobby();
    * Disconnect 0:心跳 1：断开chat 2：返回
    */
    backLobby:function(Disconnect){
        if(Disconnect==2){
            window.location.href="/";    
        }else if(Disconnect==1){ //1
            if(this.initLogin==0){
                chatPop();
            }
        }else{  //0
            chatPop();
        }
        return;
    },
    /**
    * 获得播放视频相关信息
    * @return obj 视频相关信息 function getLiveInfo():Object;
    */
    getLiveInfo:function(){
            
    },
    
    /**
    * 重新获取Token
    */
    getToken:function(type){
            $.getJSON("show_sha_eid_"+_show.emceeId+"_uid_"+_show.userId+"_t_"+type+Sys.ispro+".htm?t="+Math.random(),function(json){
                if(json && json.code==0){
                    if(type==2){
                        Dom.$swfId("JoyCamLivePlayer").setToken(json.data.tokenU,type);
                    }
                    else if(type==3){
                        Dom.$swfId("JoyShowLivePlayer").setToken(json.data.tokenV,type);
                    }
                }
                else{
                    _alert("认证失败！",3);
                    return;
                }
            });            
    },
    endLiveShow:function(rcode){
        
        $.ajax({
            url:"show_shutLiveShow_rid_"+_show.emceeId+"_rcode_"+rcode+".htm",
            data:"t="+Math.random(),
            type:'get',
            async:false,
            success: function(data){
                window.location.reload();
            }
        });
        
/*        $.getJSON("show_shutLiveShow_rid_"+_show.emceeId+"_rcode_"+rcode+".htm?t="+Math.random(),function(json){
            window.location.reload();
            setTimeout(function(){window.location.reload();},1000);
        });*/
        
    },showCloseReasonDiv:function(){
        $("#closeReasonDiv").show();
    },
    filterScript:function(s){//过滤特殊字符
        var pattern=new RegExp("[`~!#$^&*()=|{}':;',\\[\\].<>/?~！#￥……&*（）——|{}【】‘；：”“'ڪ、？]") 
        var rs=""; 
        for(var i=0;i<s.length;i++){ 
            rs=rs+s.substr(i,1).replace(pattern,''); 
        } 
        return rs;     
    },
    isScroll:function(chatarea){ //判断是否滚屏
        var objarea=Dom.$getid(chatarea);
        var harea=objarea.scrollHeight;
        if(Chat.scrollChatFlag==1){objarea.scrollTop=harea;}
    },
    showFlash:function(data){//礼物展示
        var t='';
        var v=true;
        if(data['giftCount'] >= 1314){
            var giftIcon = data['giftIcon'];
            var effectId = 8;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }
        else if(data['giftCount'] >= 520){
            var giftIcon = data['giftIcon'];
            var effectId = 7;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }else if(data['giftCount'] >= 300){
            var giftIcon = data['giftIcon'];
            var effectId = 5;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }else if(data['giftCount'] >= 99){
            var giftIcon = data['giftIcon'];
            var effectId = 3;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }else if(data['giftCount'] >= 66){
            var giftIcon = data['giftIcon'];
            var effectId = 2;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }else if(data['giftCount'] >= 11){
            var giftIcon = data['giftIcon'];
            var effectId = 0;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }else if(data['giftCount'] > 1){
            var giftIcon = data['giftIcon'];
            var effectId = 0;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }else{
            var effectId = -1;
            if(data.giftId==33 || data.giftId==51||data.giftId==34||data.giftId==55||data.giftId==58||data.giftId==35||data.giftId==60||data.giftId==53||data.giftId==57){
                var giftIcon = data['giftSwf'];
                var left = '48.2%';
                var top = '203px';
                var top2 = '164px';
                creaTime();
            }else{
                var giftIcon = data['giftIcon'];
                var left = parseInt(Math.random()*(100-48)+48) + '%';
                var top = parseInt(Math.random()*(633-273)+273) + 'px';
                var top2 = parseInt(Math.random()*(594-234)) + 'px';
                settime2();
                setTimeout(function(){
                        $('#flashCallGift').css({"left":left,"top":top2,"width":"100px","height":"100px"});
                        Dom.$swfId("flashCallGift").playEffect(giftIcon, effectId, 200);
                    },20);
                t=setTimeout(settime2,15000); 
            }
        }

        if(data['giftSwf'] != ''){
            var giftIcon = data['giftSwf'];
            var effectId = -1;
            var left = '48.2%';
            var top = '203px';
            var top2 = '164px';
            creaTime();
        }
                //-1一个 0三角形 1不显示 2六字形 3嘴形 4元宝 5心形 7 ILOVEYOU 8一生一世 9海枯石烂
                //  if(timeout){
                //     window.clearTimeout(timeout);
                    
                // } 
                
                function settime(){
                    Dom.$swfId("flashCallGiftswf").clearEffectswf();
                    $('#flashCallGiftswf').css({"width":"1px","height":"1px"}); 
                }
                function settime2(){
                    Dom.$swfId("flashCallGift").clearEffect();
                    $('#flashCallGift').css({"width":"1px","height":"1px"});
                }
                
                function creaTime(){
                    settime2();
                    $('#flashCallGiftswf').css({"left":left,"bottom":'0',"width":"1080px","height":"514px"});
                    Dom.$swfId("flashCallGiftswf").playEffectswf(giftIcon, effectId, 200);
                    t=setTimeout(settime,15000);   
                }
               
                           
    },
    /**
    * flash收到socket数据后转发给js
    * @param json 数据
    */
    chatFromSocket:function(data){
        //alert(Dom.$swfId("flashCallGift"));
        //Dom.$swfId("flashCallGift").playEffect("http://img.jiabei.com/img/giftjs/gift_ordinary/50/19.png", -1, 300);
        //alert(data);
      var data=evalJSON(data);
      if(data.retcode){
          if(data.retcode=="409003"){
             _alert('你已经被踢出房间！',3);
             setTimeout(function(){
                window.location.href='/';
             },3)
             return false;
          }
          if(data["retcode"]=="409002"){
             _alert('你已经被禁言!',3);
             return false;
          }
          if(data["retcode"]=="401008"){
             _alert('富豪等级1以下用户发言不能超过10个字!',3);
             return false;
          }
          if(data.vc && data.vc!=""){
              _vc = data.vc;
              if(data.refresh==1){
                 Dom.$swfId("flashCallChat").chatVerificationCode(-1, 0, '', _vc);
              }
          }
          if(data.retcode=='000000'){
             if(this.inCount==0){ //socket link sucsess    
                 this.inCount=1;
                    //$.getJSON("show_sha_eid_"+_show.emceeId+"_uid_"+_show.userId+"_t_1"+Sys.ispro+".htm?t="+Math.random(),function(json){
                        //if(json && json.code==0){
                            //Dom.$swfId("flashCallChat").chatToSocket(0,0,'{"_method_":"Enter","rid":"'+_show.emceeId+'","uid":"'+_show.userId+'","uname":"","token":"'+json.data.tokenC+'","md5":"RTYUI"}');
                        //}else{
                            //_alert("认证失败！",3);
                            //return;
                        //}
                    //});
                    Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"Connect"}');
                 return false;
             }else{
                  var msgObject=data.msg[0];
                  var msgtype=msgObject.msgtype;
                  var msgaction=msgObject.action;
                  var msgArray=new Array();
                  
                  // 0 通知   1 系统   2 聊天   3 公告   4 特权   5 消费  6 获取在线用户列表
                  switch(msgtype){
                      case "0":
                        msgArray.push(this.showNoticeMsg(msgObject));
                        break;
                      case "1": //系统消息
                        msgArray.push(this.showSystemMsg(msgObject));
                        break;
                      case "2":
                        msgArray.push(this.showSendMsg(msgObject));
                        break;
                      case "3":
                        msgArray.push(this.showAnnouncementMsg(msgObject));
                        break;
                      case "4":     //特权
                        msgArray.push(this.showActionMsg(msgObject));
                        break;
                      case "5":
                        msgArray.push(this.showGiftMsg(msgObject));
                        break;
                      case "6":
                          this.getChatOnline(msgObject);
                        break;
                      case "7": //在线用户信息更新
                        this.changeUser(msgObject);
                        break;
                      case "11"://更新倒量用户
                        this.reflashCount(msgObject);
                        break;
                  }
                setTimeout(function(){
                     if(msgArray && msgArray.join("")!=""){
                            Chat.msgLen++;
                            if(Chat.msgLen>200){
                                $("#chat_hall > p:first-child").remove();
                            }
                     }
                     if(msgArray.join("")!=""){$("#chat_hall").append(msgArray.join(""));}
                     if(msgArray){JsInterface.isScroll("chat_hall");}
                }, 100);
             }
           }else{ //抛出异常错误
              _alert(data["retmsg"],3);
              return false;
           }
      }
    },
    showNoticeMsg:function(data){ //msgType:0
    
        var naction=data["action"];    
        var str="",user="";
        if(naction==0){ //上线
            try{
              if(data){
                user=data["ct"];
                //var userid=user['userid'];
                //$.ajax({url:"/index.php/Show/getCard",success:function(data){var data=eval("("+data+")");alert(data['data']['daoju4'])}});
                
                //$("#chat_hall").append("<span>"+user['userName']+"</span>");
                _show.enterChat=1; //Enter Room标识
                
                if(_show.userId==user["userid"]){
                    Dom.$swfId("flashCallChat").chatToSocket(0,6,'{"_method_":"GetUsetList","pno":1,"rpp":0,"otype":1,"checksum":""}');    
                    
                    //进入房间成功后加载宠物
                    loadpet();
                }else{
                    this.doAdd(user);//增加一个用户
                }
                if(user["userid"]>0){ //不是游客
                    if(user["h"]==0 && user["userid"]!=_show.emceeId){ //1: 隐身 0：显示
                        str="<p class=\"tx_focus\">热烈欢迎:"+decodeURIComponent(data['uname'])+" 进入房间</p>";        
                    }else{
                        if(user["userType"]==10){ //巡管
                            return;    
                        }else if(user["richlevel"]>0 && user["richlevel"]<=10){
                            str="<p class=\"tx_focus\">欢迎 <a class=\"chatuser\" gn="+user["goodnum"]+" id="+user["userid"]+" href=\"javascript:void(0);\">"+decodeURIComponent(user["username"])+"</a> 进入房间</p>";    
                        }else if(user["richlevel"]>10 && user["richlevel"]<=18){
                            str="<p class=\"tx_focus\">欢迎 <span class='cracy cra"+user["richlevel"]+"'></span><a class=\"chatuser\" id="+user["userid"]+" gn="+user["goodnum"]+" href=\"javascript:void(0);\">"+decodeURIComponent(user["username"])+"</a> <img src=\"/style/images/cz01.gif\"/></p>";
                        }else if(user["richlevel"]>18 && user["richlevel"]<=21){
                            str="<p class=\"tx_focus\">欢迎 <span class='cracy cra"+user["richlevel"]+"'></span><a class=\"chatuser\" id="+user["userid"]+" gn="+user["goodnum"]+" href=\"javascript:void(0);\">"+decodeURIComponent(user["username"])+"</a> <img src=\"/style/images/cz02.gif\"/></p>";
                        }else if(user["richlevel"]>21 && user["richlevel"]<=26){
                            str="<p class=\"tx_focus\">欢迎 <span class='cracy cra"+user["richlevel"]+"'></span><a class=\"chatuser\" id="+user["userid"]+" gn="+user["goodnum"]+" href=\"javascript:void(0);\">"+decodeURIComponent(user["username"])+"</a> <img src=\"/style/images/cz03.gif\"/></p>";
                        }else if(user["richlevel"]>26 && user["richlevel"]<=30){
                            str="<p class=\"tx_focus\">欢迎 <span class='cracy cra"+user["richlevel"]+"'></span><a class=\"chatuser\" id="+user["userid"]+" gn="+user["goodnum"]+" href=\"javascript:void(0);\">"+decodeURIComponent(user["username"])+"</a> <img src=\"/style/images/cz04.gif\"/></p>";
                        }else if(user["richlevel"]>30){
                            str="<p class=\"tx_focus\">欢迎 <span class='cracy cra"+user["richlevel"]+"'></span><a class=\"chatuser\" id="+user["userid"]+" gn="+user["goodnum"]+" href=\"javascript:void(0);\">"+decodeURIComponent(user["username"])+"</a> <img src=\"/style/images/cz05.gif\"/></p>";
                        }else{
                           return;
                        }
                    }
                 }
              }
              return str;
            }catch(e){}    
       }else if(naction==1){ //下线 
          try{
             if(data){
                 this.remove(data['uid']);
                str="<p class=\"tx_focus\"><span>系统消息:"+decodeURIComponent(data['uname'])+" 离开房间</span></p>";
                return str;
             }
          }catch(e){}
       }
       else if(naction==7){
            this.initLogin=1;
               _alert4("此账号在异地重复登录！");
        }
    },
    showActionMsg:function(data){ //msgType 4 特权
        var taction=parseInt(data["action"]);
        var str="";
        try{  
            switch(taction){
                case 0: //踢人
                    if(_show.userId==data['touid']){
                         this.initLogin=1;
                        _alert4(data["ct"]);    
                    }
                    //this.remove(data['touid']);//删除用户
                    str="<p><span>"+data["timestamp"]+"</span><a href=javascript:void(0); class=\"chatuser\" gn="+data["tougood"]+" id="+data["touid"]+">"+ decodeURIComponent(data["touname"])+"</a> 被 <a href=javascript:void(0);  class=\"chatuser\" gn="+data["ugood"]+" id="+data["uid"]+">"+decodeURIComponent(data["uname"])+"</a> 踢出房间一小时</p>";
                    break;
                case 1: //禁言
                    str="<p><span>"+data["timestamp"]+"</span><a href=javascript:void(0); class=\"chatuser\" gn="+data["tougood"]+" id="+data["touid"]+" >"+ decodeURIComponent(data["touname"])+"</a> 被 <a href=javascript:void(0);  class=\"chatuser\" gn="+data["ugood"]+" id="+data["uid"]+">"+decodeURIComponent(data["uname"])+"</a>  禁言5分钟</p>";
                    if(_show.userId==data["touid"]){ //禁言提示
                        _alert('你已经被禁言！',3);
                    }
                    break;
                case 2: //恢复发言
                    str="<p><span>"+data["timestamp"]+"</span><a href=javascript:void(0); class=\"chatuser\" gn="+data["ugood"]+" id="+data["uid"]+" >"+ decodeURIComponent(data["uname"])+"</a> 恢复 <a href=javascript:void(0); class=\"chatuser\" gn="+data["tougood"]+" id="+data["touid"]+" >"+ decodeURIComponent(data["touname"])+"</a> 发言</p>";
                    if(_show.userId==data["touid"]){ //禁言提示
                        _alert('你已经恢复发言！',3);
                    }
                    break;
                case 41: //宠物操作
                    var uid=data['uid'], uname=decodeURIComponent(data['uname']), ugood=data['ugood'], touid=data['touid'], touname=decodeURIComponent(data['touname']),tougood=data['tougood'];
                    var ct=evalJSON(decodeURIComponent(data.ct));
                    var pet=evalJSON(ct.pet);
                    Dom.$swfId("JoyPet_"+ct.pos).petShow(ct.callMethod,ct.pet);
                    if(ct.func=="gag"){
                        if(_show.userId==touid){ //禁言提示
                            _alert('你已经被禁言！',3);
                        }
                        str="<p><span>"+data["timestamp"]+"</span><a href=\"javascript:void(0);\" class=\"chatuser\" gn=\""+ugood+"\" id=\""+uid+"\">"+uname+"</a> 的宠物 "+pet.petName+" 跑来堵住了 <a href=\"javascript:void(0);\" class=\"chatuser\" gn=\""+tougood+"\" id=\""+touid+"\"> "+touname+"</a> 的嘴（5分钟）。</p>";
                    }else if(ct.func=="kick"){
                        if(_show.userId==touid){
                             this.initLogin=1;
                            _alert4("你被踢出房间一小时!");    
                        }
                        this.remove(touid);
                        str="<p><span>"+data["timestamp"]+"</span><a href=\"javascript:void(0);\" class=\"chatuser\" gn=\""+ugood+"\" id=\""+uid+"\">"+uname+"</a> 的宠物 "+pet.petName+" 一脚将 <a href=\"javascript:void(0);\" class=\"chatuser\" gn=\""+tougood+"\" id=\""+touid+"\"> "+touname+"</a> 踢出了房间（1小时）。</p>";
                    }
                    break;
            }
    
            return str;
        }catch(e){}
    },
    showSendMsg:function(data){ //msgType:2
        var str="";
        try{
                if(data){
                    var tempMsg=Face.de(data["ct"]),time=data["timestamp"],ugood=data["ugood"],uid=data["uid"],uname=decodeURIComponent(data["uname"]),cugood=this.chatgnum(data["ugood"]),tougood=data["tougood"],touid=data["touid"],touname=decodeURIComponent(data["touname"]),ctougood=this.chatgnum(data["tougood"]),icon="";
                    
                    //20141124
                    if(tempMsg == 'I am alive'){return;}
                    //20141124

                    //color 主播：#ff34ff  本场皇冠：#ff0101 超级皇冠：#0166ff
                    if(uid==_show.emceeId && uid==_show.local && uid==_show.supper){
                        tempMsg='<span class="m u">'+tempMsg+'</span>';    
                        icon="<img src='/style/images/local.gif'/>";    
                    }else if(uid==_show.emceeId && uid==_show.local){
                        tempMsg='<span class="m u">'+tempMsg+'</span>';    
                        icon="<img src='/style/images/local.gif'/>";    
                    }else if(uid==_show.emceeId && uid==_show.supper){
                        tempMsg='<span class="m u">'+tempMsg+'</span>';    
                        icon="<img src='/style/images/supper.gif'/>";    
                    }else if(uid==_show.local && uid==_show.supper){
                        tempMsg='<span class="m l">'+tempMsg+'</span>';    
                        icon="<img src='/style/images/local.gif'/>";    
                    }else if(uid==_show.emceeId){
                        tempMsg='<span class="m u">'+tempMsg+'</span>';    
                    }else if(uid==_show.local){
                        tempMsg='<span class="m l">'+tempMsg+'</span>';    
                        icon="<img src='/style/images/local.gif'/>";
                    }else if(uid==_show.supper){
                        tempMsg='<span class="m s">'+tempMsg+'</span>';    
                        icon="<img src='/style/images/supper.gif'/>";        
                    }
                    if(data["action"]==0){ //公聊
                        var toAllSay='<p><span>'+time+'</span>'+icon+'<a class=\"chatuser\" gn="'+ugood+'" id='+uid+' href="javascript:void(0);">'+uname+'</a>'+cugood+': '+tempMsg+'</p>';
                        str=toAllSay;
                    }else if(data["action"]==1){ //悄悄
                        var toOneSay='<p><span>'+time+'</span>'+icon+'<a class=\"chatuser\" gn="'+ugood+'" id='+uid+' href="javascript:void(0);">'+uname+'</a>'+cugood+' 对 <a href="javascript:void(0);" class=\"chatuser\" gn="'+tougood+'" id='+touid+'>'+touname+'</a>'+ctougood+' 说: '+tempMsg+'</p>';
                        str=toOneSay;
                    }else if(data["action"]==2){ //私聊
                        if(Chat.is_private==0){//无关闭私聊
                            if(data["uid"]==_show.userId){                                
                                str='<p><span>'+time+'</span> 你对 <a href="javascript:void(0);" class=\"chatuser\" gn="'+tougood+'" id='+touid+'>' + touname + '</a>'+ctougood+' 说: ' +tempMsg+ '</p>';
                            }else if(_show.admin==1){//巡官
                                str='<p>'+icon+' <a href="javascript:void(0);" gn='+ugood+' class=\"chatuser\" id='+uid+'>' + uname + '</a>'+cugood+' 对 <a href="javascript:void(0);" gn="'+tougood+'" class=\"chatuser\" id='+touid+'>' + touname + '</a>'+ctougood+' 说: ' +tempMsg+ '<span>(' + time + ')</span> </p>';
                            }else{
                                if(data["touid"]==_show.userId){
                                    str='<p><span>'+time+'</span> '+icon+' <a href="javascript:void(0);" gn="'+ugood+'" class=\"chatuser\" id='+uid+'>' + uname + '</a>'+cugood+' 对你说: ' +tempMsg+ '</p>';
                                }else{
                                    //str='<p><span>' + time + '</span>'+icon+' <a href="javascript:void(0);" gn='+ugood+' class=\"chatuser\" id='+uid+'>' + uname + '</a>'+cugood+' 对 <a href="javascript:void(0);" gn="'+tougood+'" class=\"chatuser\" id='+touid+'>' + touname + '</a>'+ctougood+' 说: ' +tempMsg+ '</p>';
                                }
                            }
                            $("#chat_hall_private").append(str);
                            this.isScroll("chat_hall_private");
                            return;
                        }
                    }
                }
                return str;
            }catch(e){}
    }, 
    showSystemMsg:function(data){ //msgType:1
       
        var str="";
        try{
            var Saction=parseInt(data["action"]);
            var obj_box=data["ct"];
            obj_box=obj_box.replace(/\+/g,"%20");
            obj_box=evalJSON(decodeURIComponent(obj_box));
            switch(Saction){
                  case 33:  //送红包
                    var gethb = parseInt($("#gethb").text()) + 1;
                    $("#gethb").html(gethb);
                    if(obj_box["userId"]==_show.userId){
                        var fundhb = parseInt($("#fundhb").text()) - 1;
                        $("#fundhb").html(fundhb);
                        var sendhb = parseInt($("#sendhb").text()) + 1;
                        $("#sendhb").html(sendhb);
                    }
                    str='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+obj_box["userNo"]+' id='+obj_box["userId"]+'> '+decodeURIComponent(obj_box["userName"])+' </a>'+this.chatgnum(obj_box["userNo"])+' 送了红包一个<img src="/style/images/hb.gif" /></p>';
                    $("#hbrank").load('/index.php/Show/show_redbagrank/t/'+Math.random(),function (responseText, textStatus, XMLHttpRequest){this;});
                    break;
                     case 3:  //送礼物
                    var giftIcon=obj_box.giftIcon,giftNum=obj_box.giftCount,giftName=obj_box.giftName,giftimg='',isGift=obj_box.isGift || 0,ugood=obj_box["userNo"],uid=obj_box["userId"],uname=decodeURIComponent(obj_box["userName"]),cugood=this.chatgnum(obj_box["userNo"]),tougood=obj_box["toUserNo"],touid=obj_box["toUserId"],touname=decodeURIComponent(obj_box['toUserName']),tocugood=this.chatgnum(obj_box["toUserNo"]),gifttop=parseInt($('#gift_history li').size()) || 0;
                    if(giftNum){
                        var giftshow=(giftNum>150?150:giftNum);
                        for(var i=0;i<giftshow;i++){giftimg+= '<img src="'+obj_box["giftPath"]+'" class="gt"/>';}
                    }
if(giftNum!=0){
                    var giftstr='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+ugood+' id='+uid+'>'+uname+'</a>'+cugood+' 送给 <a href="javascript:void(0);" gn='+tougood+' class=\"chatuser\" id='+touid+'>' +touname+ '</a>'+tocugood+':'+giftimg+giftNum+'个</p>';
}else{

if(giftName==14)
{
var test1='<param name="movie" value="/style/car/9164.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9164.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">科幻超级摩托</span>入场</p><p><em style=" background:url(/style/car/gift_9164.png); float:left;height:55px; width:83px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}else if(giftName==13)
{
var test1='<param name="movie" value="/style/car/9168.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9168.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">赤兔马</span>入场</p><p><em style=" background:url(/style/car/gift_9168.png); float:left;height:100px; width:160px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;

}else if(giftName==12)
{
var test1='<param name="movie" value="/style/car/9167.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9167.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">小毛驴</span>入场</p><p><em style=" background:url(/style/car/gift_9167.png); float:left;height:100px; width:160px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;

}else if(giftName==11)
{
var test1='<param name="movie" value="/style/car/9166.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9166.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">小乌龟</span>入场</p><p><em style=" background:url(/style/car/gift_9166.png); float:left;height:100px; width:160px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;

}else if(giftName==10)
{
var test1='<param name="movie" value="/style/car/9182.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9182.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">宇宙战舰</span>入场</p><p><em style=" background:url(/style/car/gift_9182.png); float:left;height:55px; width:83px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;

}else if(giftName==9)
{
var test1='<param name="movie" value="/style/car/9175.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9175.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">福特</span>入场</p><p><em style=" background:url(/style/car/gift_9175.png); float:left;height:78px; width:146px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}else if(giftName==8)
{
var test1='<param name="movie" value="/style/car/9169.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9169.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">GMC</span>入场</p><p><em style=" background:url(/style/car/gift_9169.png); float:left;height:98px; width:146px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}else if(giftName==7)
{
var test1='<param name="movie" value="/style/car/9170.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9170.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">Nissan GTR</span>入场</p><p><em style=" background:url(/style/car/gift_9170.png); float:left;height:69px; width:150px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;


}else if(giftName==6)
{
var test1='<param name="movie" value="/style/car/z01.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/z01.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">自行车</span>入场</p><p><em style=" background:url(/style/car/gift_9165.png); float:left;height:100px; width:160px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;


}else if(giftName==5)
{
var test1='<param name="movie" value="/style/car/9174.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9174.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">UFO</span>入场</p><p><em style=" background:url(/style/car/gift_9174.png); float:left;height:69px; width:150px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}else if(giftName==4)
{
var test1='<param name="movie" value="/style/car/9176.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9176.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">悍马</span>入场</p><p><em style=" background:url(/style/car/gift_9176.png); float:left;height:95px; width:150px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}else if(giftName==3)
{
var test1='<param name="movie" value="/style/car/9178.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9178.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">考维特</span>入场</p><p><em style=" background:url(/style/car/gift_9178.png); float:left;height:78px; width:146px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}else if(giftName==2)
{
var test1='<param name="movie" value="/style/car/9177.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9177.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">法拉利</span>入场</p><p><em style=" background:url(/style/car/gift_9177.png); float:left;height:73px; width:150px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}else if(giftName==1)
{
var test1='<param name="movie" value="/style/car/9172.swf"><param name="quality" value="high"><param name="loop" value="false"><param name="wmode" value="transparent"><embed src="/style/car/9172.swf"  quality=high pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="1100"   height="800"  wmode="transparent"></embed>';

var giftstr='<p>土豪 <span style="color:#FF4040" scroll="#FF4040">'+uname+'</span> 驾驶着<span style="color:#FF4040" scroll="#FF4040">布加迪威航</span>入场</p><p><em style=" background:url(/style/car/gift_9172.png); float:left;height:85px; width:150px;"></em></p><br /><br /><br /><br /><br />';
document.getElementById("test1").innerHTML=test1;
}

}
                    if(giftstr!=""){Chat.msgLen++;$("#chat_hall").append(giftstr);this.isScroll("chat_hall");}
                     //礼物列表
                    if(touid==_show.emceeId&&giftNum!=0){//是送个主播的礼物
                       var gift_history='<li><span>'+giftNum+'个</span><img src="'+giftIcon+'" class="gt"/><em>' + giftName + '</em><a title='+uname+' href="/'+ugood+'" target="_blank">'+ (gifttop+1)+ '. ' +uname+'</a></li>';
                       $('#gift_history').append(gift_history);
                    }
                    //小人flash/gif 展示效果
                    if(isGift==0){this.openSmallf(uid,uname,ugood);}else{this.openSmallg(uid,uname,ugood,giftNum,giftName,giftIcon);}
                    //调用礼物动画效果
                    var giftloadings=this.giftloading;
                    //if(giftloadings==1){
                        this.showFlash(obj_box);
                    //}else{
                        //_alert('礼物展示出现意外！',3);    
                    //}
                    Chat.getUserBalance();//用户秀币更新    
                    Chat.getRankByShow();//本场排行
                    break;
                   case 4:  //抢座
                       var seatstr='<p><span>'+ data["timestamp"] +'</span>' + obj_box["message"] + ' <img src="/style/images/gift/sofa.png" /></p>';
                       if(seatstr!=""){Chat.msgLen++;$("#chat_hall").append(seatstr);this.isScroll("chat_hall");}
                       var sofa_o=$('#user_sofa .t'+obj_box["seatId"]).find('img');
                      
                       sofa_o.attr({'seatnum':obj_box["seatPrice"],'src':obj_box["userIcon"],'title':decodeURIComponent(obj_box["userNick"])});
                       //礼物列表
                       var gifthisNum=parseInt($('#gift_history li').size()) || 0;
                        var gift_history='<li><span>'+obj_box["seatPrice"]+'</span><img src="/style/images/gift/sofa.png" width="24" height="24"/><em>沙发</em><a title='+decodeURIComponent(obj_box["userNick"])+' href="/'+obj_box["userNo"]+'" target="_blank">'+ (gifthisNum+1)+ '. ' +decodeURIComponent(obj_box["userNick"])+'</a></li>';
                       $('#gift_history').append(gift_history);
                       $('#get_sofa').hide();
                       Chat.getUserBalance();//用户秀币更新    
                       Chat.getRankByShow();//本场排行
                    break;   
                  case 5:  //房间公告
                       if(obj_box["link"]==""){
                           str=obj_box["text"];
                       }else{
                           str="<a href='"+obj_box["link"]+"' target='_blank'>" + obj_box["text"] + "</a>";
                       }
                     $('#room_public_notice').html(str);
                     $('#notice-modle').hide();
                     
                     return;
                     break;
                  case 6: //私聊公告
                       if(obj_box["link"]==""){
                           str=obj_box["text"];
                       }
                       else{
                           str="<a href='"+obj_box["link"]+"' target='_blank'>" + obj_box["text"] + "</a>";
                       }
                     $('#room_private_notice').html(str);
                     $('#notice-modle').hide();
                     return;
                     break;
                  case 7: //设置房间背景
                       var filepath=obj_box["image"];
                     $("body").css('background','url('+filepath+') repeat center');
                     $('#background-modle').hide();
                     return;
                     break;
                  case 8: //取消房间背景
                     $("body").css({'background':'url("")','background-color':'#e6e6e6'});
                     return;
                     break;
                  case 9: //点歌
                       Song.initVodSong();
                      str='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+obj_box["userNo"]+' id='+obj_box["userId"]+'> '+decodeURIComponent(obj_box["userName"])+' </a>'+this.chatgnum(obj_box["userNo"])+' 点歌 '+obj_box["songName"]+' <img src="/style/images/gift/song.png" /></p>';
                     break;
                  case 10: //同意点歌
                     Song.initVodSong();
                      str='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" gn='+_show.goodNum+' class=\"chatuser\" id='+_show.emceeId+'> '+_show.emceeNick+' </a>'+this.chatgnum(_show.goodNum)+' 同意 <a href="javascript:void(0);"  class=\"chatuser\" gn='+obj_box["userNo"]+' id='+obj_box["userId"]+'> '+decodeURIComponent(obj_box["userName"])+' </a>'+this.chatgnum(obj_box["userNo"])+' 点歌 '+obj_box["songName"]+' <img src="/style/images/gift/song.png" /></p>';
                     if(obj_box["userId"]==_show.userId){
                        Chat.getUserBalance();//用户秀币更新     
                     }
                     Chat.getRankByShow();//本场排行
                     break;
                  case 11: //房间公聊设置
                     var ispub=obj_box["state"];
                     var chatSet=$("#chatSet");
                     if(ispub=="1"){ //开启状态
                         if(chatSet){
                            chatSet.attr('state',0).html('公聊室开启<cite class="on"></cite>');
                        }
                        $('#chat_close').hide();_show.is_public="1";
                        str="<p class=tx_focus>开启房间公聊</p>"
                     }else if(ispub=="0"){
                         if(chatSet){
                            chatSet.attr('state',1).html('公聊室关闭<cite class="off"></cite>');
                        }
                        $('#chat_close').show();_show.is_public="0";    
                        str="<p class=tx_focus>关闭房间公聊</p>"
                     }
                     break;
                  case 12: //礼物、沙发、礼物周星、飞屏  大公告 

                          var url = "/index.php/Show/get_gift_list";
                          $.ajax({
                              url:url,
                              success:function(data)
                              {
                                  $(".js-hl-list").html(data);
                              }
                          });

                      setTimeout(function(){
                        var recent_size=parseInt($('#gift_recent p').size()) ;
                        //$("#gift_recent > p:first-child").remove();
                        var html = obj_box["message"];
if(html.indexOf('0个',1)==-1){
                        html = html.replace(/25/g,'20');
                        $("#gift_recent_next").append(html);

                        if(recent_size==0){
                            $("#gift_recent").append(html);
                        
                            $("#gift_recent").css('position','relative');
                            $("#gift_recent p").css('position','absolute');
                            var bw=$("#gift_recent").width();
                            var wrap_w=$("#gift_recent p").width();
                            if(bw>=wrap_w){
                                $("#gift_recent p").css("left",bw-wrap_w+"px");
                            }else{
                                $("#gift_recent p").css("left","0px");
                            }
                            roll(bw,wrap_w);
                        };
}
                    
                    },1000);

                      break;
                  case 13:  //设置管理员
                    str='<p><span>'+data["timestamp"]+'</span> '+obj_box["message"]+'</p>';
                    if(_show.userId>0 && _show.userId==obj_box["userId"]){$(".tdeal,.menuline").show();}
                    var meq=-1,peq=-1;
                    var userid=obj_box["userId"];
                    if(userid==_show.userId && _show.userId>0){_show.sa=1;}
                    var muser=null;
                    peq=this.getloc(this.arrPeople,userid);
                    if(peq>=0){muser=this.arrPeople[peq];}
                    meq=this.getloc(this.arrManage,userid);
                    if(meq<0){this.arrManage.push(muser);this.reflashM(1);this.chatManage();}
                    break;
                  case 14: //取消管理员
                    str='<p><span>'+data["timestamp"]+'</span> '+obj_box["message"]+'</p>';
                     if(_show.userId>0 && _show.userId==obj_box["userId"]){
                         _show.sa=0;
                        $(".tdeal,.menuline").hide();        
                     }
                     var hostid=obj_box["userId"];
                     var uid=hostid,meq=-1;
                     meq=this.getloc(this.arrManage,uid);
                     if(meq>=0){ //manager
                        $('#manage_'+uid).remove();
                        this.arrManage.splice(meq,1);
                        this.reflashM(0);
                     }
                      break;
                  case 15://开始直播
                      if(_show.deny!=4){
                        var that=this;
                         _show.closed=0;
                        that.beginLive(obj_box);
                        setTimeout(function(){
                            if(obj_box["continues"]==0){
                                var strlive="<p class=\"tx_focus12\"><span>"+obj_box["showTime"]+"</span> 直播开始</p>";
                                $("#chat_hall").append(strlive);
                            }
                        }, 1);
                    }
                    break;
                   case 16://直播许愿
                       var wishCont=obj_box["wishContent"];
                    if(_show.emceeId!=_show.userId){//不是主播
                         if(wishCont!=""){
                             $('#mywishCont,#wishImitation').html(wishCont);
                         }
                    }
                    return;
                    break;
                  case 17://设置点歌
                      var apply=obj_box['apply'],sa=$("#songApply"),sa1=$("#songApply_1"),sa2=$("#songApply_2"),sas=$("#songApplyShow"),sai=$("#songApplyIcon");
                    if(apply==1){ //允许
                        if(sa){sa.show();}
                        if(sa1){sa1.show();}
                        if(sa2){sa2.hide();}
                        if(sas){sas.html("允许");}
                        if(sai){sai.attr('class','on');}
                        return;
                    }else{ //禁止
                        if(sa){sa.hide();}
                        if(sa1){sa1.hide();}
                        if(sa2){sa2.show();}
                        if(sas){sas.html("禁止");}
                        if(sai){sai.attr('class','off');}
                        return;
                    }
                    return ;
                    break;
                  case 18: //结束直播
                    if(_show.userId==_show.emceeId && obj_box["code"]!=2){
                        alert(obj_box["reson"]);
                        window.location.reload();
                        return;
                    }
                    if(_show.userId!=_show.emceeId){
                        //var Arr_playName=[""];
                        //Dom.$swfId("JoyShowLivePlayer").initialize(Arr_playName,Arr_playName,"");
                        _alert('直播已结束！',3);
                    }
                    str="<p class=\"tx_focus\"><span>"+obj_box["showTime"]+"</span> "+obj_box["reson"]+"</p>";
                    this.endLive();
                    $(".lpet").css("left", "-198px").html("<div id=\"JoyPet_left\"></div>");
                    $(".rpet").css("right", "-198px").html("<div id=\"JoyPet_right\"></div>");
                    loadpet();
                    break;
                  case 19: //转移房间
                      window.location.href=obj_box["url"];
                    break;
                  case 212://历史大公告最新3条
                     setTimeout(function(){
                        
                     }, 1);
                     break;
                  case 21: //礼物之星活动
                      var giftstr='<p><span>'+data["timestamp"]+'</span>礼物之星颁发给<a href="javascript:void(0);" gn='+obj_box["no"]+' class=\"chatuser\" id='+obj_box["uid"]+'>' +decodeURIComponent(obj_box['uname'])+ '</a>'+this.chatgnum(obj_box["no"])+'一个礼物周星<cite class="astar">礼物周星</cite></p>';
                     if(giftstr!=""){Chat.msgLen++;$("#chat_hall").append(giftstr);this.isScroll("chat_hall");}
                     var giftloadings=this.giftloading;
                     if(giftloadings==1){
                         this.showFlash(obj_box);
                     }else{
                        _alert('礼物展示出现意外！',3);
                     }
                    break;
                  case 22: //直播基页内主播、本场皇冠、超级皇冠公屏聊天颜色 主播：#ff34ff  本场皇冠：#ff0101 超级皇冠：#0166ff
                      _show.local=obj_box["luid"]; //本场皇冠 userid
                    break;
                  case 23://飞屏
                  //alert(obj_box["word"]);
                      /**
                        *
                        showFlyWord(msg:String,size:int=48,speed:int=3)
                        msg：需要显示的文本
                        size：需要显示文本字体的大小，默认48像素，也是最大像素
                        speed：文字移动的速度，默认为3像素每帧，主帧频为35帧每秒
                    */
                    /*
                    var gifttop=parseInt($('#gift_history li').size()) || 0;
                    var flyAraa='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" gn='+obj_box["uno"]+' class=\"chatuser\" id='+obj_box["uid"]+'>' +decodeURIComponent(obj_box['uname'])+ '</a>'+this.chatgnum(obj_box["uno"])+' 发送: <img src='+obj_box["gift"]+' class="gt"/></p>';
                    if(flyAraa!=""){Chat.msgLen++;$("#chat_hall").append(flyAraa);this.isScroll("chat_hall");}
                    var flymessage="";
                    if(obj_box['touid']==0){
                        flymessage=DecodeName(obj_box['uname'])+" 说: "+Face.deimg(obj_box["word"]);    
                    }else{
                        flymessage=DecodeName(obj_box['uname'])+" 对 "+DecodeName(obj_box['touname'])+" 说: "+Face.deimg(obj_box["word"]);    
                    }
                    $('#flashFlyWord').css({"width":"990px","height":"400px"});
                    if(Dom.$swfId("flashFlyWord").showFlyword){Dom.$swfId("flashFlyWord").showFlyword(flymessage,48,3);}
                    //礼物列表
                    var gift_history='<li><span>1</span><img src="'+obj_box["gift"]+'" class="gt"/><em> 飞屏 </em><a title='+decodeURIComponent(obj_box['uname'])+' href="/'+obj_box["uno"]+'" target="_blank">'+ (gifttop+1)+ '. ' +decodeURIComponent(obj_box['uname'])+'</a></li>';
                    $('#gift_history').append(gift_history);
                    Chat.getUserBalance();//用户秀币更新    
                    Chat.getRankByShow();//本场排行
                    */
                    break;
                   case 24://系统公告广播
                       /**
                      * mes:广播内容
                      * links:广播链接
                      * broad:公聊/私聊 标识  0:公聊  1：私聊  2：公聊和私聊
                    */
                    var links=obj_box["links"],mes=obj_box["mes"],isbroad=obj_box["broad"];
                    if(links!=""){
                        mes="<a href="+obj_box["links"]+" target='_blank'>"+mes+"</a>"    
                    }
                                        if(obj_box["isspecial"]==1){
                                            var strBroad="<p class=\"notice\"><span>"+data["timestamp"]+"</span>: "+mes+"</p>";
                                        }else{
                                            var strBroad="<p class=\"notice\"><span>"+data["timestamp"]+"</span><strong>系统消息</strong>: "+mes+"</p>";
                                        }
                                        
                    if(isbroad==0){
                        Chat.msgLen++;
                        $("#chat_hall").append(strBroad);
                        this.isScroll("chat_hall");    
                    }else if(isbroad==1){
                        $("#chat_hall_private").append(strBroad);
                        this.isScroll("chat_hall_private");    
                    }else{
                        Chat.msgLen++;
                        $("#chat_hall").append(strBroad);    
                        this.isScroll("chat_hall");
                        $("#chat_hall_private").append(strBroad);    
                        this.isScroll("chat_hall_private");    
                    }
                    break;
                case 45:    //小喇叭
                    var getmsgStr = FaceGb.de(obj_box['message']);

                        if (this.getMsgstrs.length<=5)
                        {
                            this.getMsgstrs.push("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+getmsgStr);
                        }
                        else
                        {
                            this.getMsgstrs.shift();
                            this.getMsgstrs.push("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+getmsgStr);
                        }

                        //console.log('传递的内容：'+this.getMsgstrs.join(''))
                        
                        if (labasb.flag)
                        {
                            $('#theText').html(this.getMsgstrs.join(''));
                            labasb.Initialize();
                        }
        
                        
                    break;
                 case 29:    //寻宝大公告
                    setTimeout(function(){
                        var gstr='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+obj_box["uno"]+' id='+obj_box["uid"]+'> '+obj_box["unick"]+' </a>'+JsInterface.chatgnum(obj_box["uno"])+' 在欢乐寻宝游戏中获得';
                        for(i=0;i<obj_box["count"];i++){
                            gstr+='<img class="gt" src="'+obj_box["icon"]+'"/>';
                        }
                        gstr+=obj_box["award"]+'共'+obj_box["count"]+'个</p>';
                        Chat.msgLen++;
                        $("#chat_hall").append(gstr);
                        JsInterface.isScroll("chat_hall"); 
                    },2000);
                    break;
                 case 30:    //寻宝游戏开关
                     clearTimer();
                                        clearTimerRabbit();
                                        //try{ console.log("接受到30...");}catch(e){ }
                     if(obj_box["backend"]==1){//try{ console.log("接受到30关闭砸蛋...");}catch(e){ }
                        var str='<p><span>'+data["timestamp"]+'</span> 系统消息：欢乐寻宝游戏暂停开放。</p>';
                        $("#chat_hall_private").append(str);
                        this.isScroll("chat_hall_private");
                     }
                                        if(obj_box["backend"]==2){//try{ console.log("接受到30关闭魔法兔子...");}catch(e){ }
                        var str='<p><span>'+data["timestamp"]+'</span> 系统消息：魔法兔子游戏暂停开放。</p>';
                        $("#chat_hall_private").append(str);
                        this.isScroll("chat_hall_private");
                     }
                                        if(obj_box["backend"]==0){//try{ console.log("接受到30关闭此房间游戏...");}catch(e){ }
                        var str='<p><span>'+data["timestamp"]+'</span> 系统消息：此房间游戏暂停开放。</p>';
                        $("#chat_hall_private").append(str);
                        this.isScroll("chat_hall_private");
                     }
                     break;
                 case 31:
                                        //try{ console.log("接受到31...");}catch(e){ }
                     if(obj_box["backend"]==1){//try{ console.log("开启砸蛋...");}catch(e){ }
                         //_game.eggstatus=1;
                                                _game.eggclosed=1;
                     }else{//try{ console.log("关闭砸蛋...");}catch(e){ }
                         _game.eggclosed=0;
                     }
                                        if(obj_box["backend"]==2){//try{ console.log("开启魔法兔子...");}catch(e){ }
                         _game.rabbitstatus=1;
                                                _game.rabbitclosed=1;
                     }else{//try{ console.log("关闭魔法兔子...");}catch(e){ }
                                                _game.rabbitclosed=0;
                                                closeChipperRabbit();
                     }
                     /* _game.eggtimer=setTimeout("showEgg()",_game.eggstart*60*1000); */
                                        //_game.rabbittimer=setTimeout("showRabbit()",_game.rabbitstart*60*1000);
                     break;
                 case 41://宠物接口
                    var pet=evalJSON(obj_box.pet);
                    switch(obj_box.callMethod){
                        case "callPet":
                        case "giftPet":
                            var giftIcon=obj_box.giftIcon,giftNum=obj_box.giftCount,giftName=obj_box.giftName,giftimg='',isGift=obj_box["isGift"] || 0,ugood=obj_box["userNo"],uid=obj_box["userId"],uname=obj_box["userName"],cugood=this.chatgnum(obj_box["userNo"]),tougood=obj_box["toUserNo"],touid=obj_box["toUserId"],touname=obj_box['toUserName'],tocugood=this.chatgnum(obj_box["toUserNo"]),gifttop=parseInt($('#gift_history li').size()) || 0;
                            if(obj_box.callMethod=="giftPet"){
                                if(pet.hostId==_show.emceeId){    
                                    var gs3='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+ugood+' id='+uid+'> '+uname+' </a>'+cugood+' 贿赂了主播的宠物 '+pet.petName+' 在房间中得瑟了'+giftNum+'下<img src="'+obj_box.giftIcon+'"  class="gt" />！</p>';
                                    if(gs3!=""){Chat.msgLen++;$("#chat_hall").append(gs3);this.isScroll("chat_hall");}
                                }else{
                                    var gs='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+ugood+' id='+uid+'> '+uname+' </a>'+cugood+' 领着TA的宠物 '+pet.petName+' 在房间中抛出了 '+giftNum+' 个炫酷大礼^_^ <img src="'+obj_box.giftIcon+'"  class="gt" /></p>';
                                    if(gs!=""){Chat.msgLen++;$("#chat_hall").append(gs);this.isScroll("chat_hall");}
                                }
                                 //礼物列表
                                if(touid==_show.emceeId){//是送个主播的礼物
                                    var gift_history='<li><span>'+giftNum+'</span><img src="'+giftIcon+'" class="gt"/><em>' + giftName + '</em><a title='+uname+' href="/'+ugood+'" target="_blank">'+ (gifttop+1)+ '. ' +uname+'</a></li>';
                                    $('#gift_history').append(gift_history);
                                }
                                //小人flash/gif 展示效果
                                 if(isGift==0){this.openSmallf(uid,uname,ugood);}else{this.openSmallg(uid,uname,ugood,giftNum,giftName,giftIcon);}
                            }else{
                                var gs1='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+ugood+' id='+uid+'> '+uname+' </a>'+cugood+' 的宠物 '+pet.petName+' 炫彩登场！</p>';
                                if(gs1!=""){Chat.msgLen++;$("#chat_hall").append(gs1);this.isScroll("chat_hall");}
                            }
                            //调用礼物动画效果
                            var giftloadings=this.giftloading;
                            if(giftloadings==1){
                                this.showFlash(obj_box);
                            }else{
                                _alert('礼物展示出现意外！',3);    
                            }
                            Dom.$swfId("JoyPet_"+obj_box.pos).petShow(obj_box.callMethod,obj_box.pet);
                            break;
                        case "namePet":
                            Dom.$swfId("JoyPet_"+obj_box.pos).petShow(obj_box.callMethod,obj_box.pet);
                            break;
                        case "fightPet":
                            var ugood=obj_box["userNo"],uid=obj_box["userId"],uname=obj_box["userName"],cugood=this.chatgnum(obj_box["userNo"]);
                            var gs2='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+ugood+' id='+uid+'> '+uname+' </a>'+cugood+' 的宠物 '+pet.petName+' 在争夺中花费'+pet.fightCost+'奇币将对方打得七零八落！</p>';
                            if(gs2!=""){Chat.msgLen++;$("#chat_hall").append(gs2);this.isScroll("chat_hall");}
                            Dom.$swfId("JoyPet_right").petShow(obj_box.callMethod,obj_box.pet);
                            break;
                    }
                    break;
                case 55: //特殊礼物发放
                    var prizeinfos=obj_box["extdata"].split('|');
                    var itemid=prizeinfos[0];
                    var prizeid=prizeinfos[1];
                    var itemstr="";
                    var prizestr="";
                    var moneystr="";
                    var prizeurl="";
                    if(itemid=="a"){
                        itemstr="田径项目";
                    }else if(itemid=="b"){
                        itemstr="竞技项目";
                    }else if(itemid=="c"){
                        itemstr="游泳项目";
                    }else if(itemid=="d"){
                        itemstr="球类项目";
                    }else if(itemid=="e"){
                        itemstr="体操项目";
                    }else if(itemid=="f"){
                        itemstr="水上项目";
                    }
                
                    if(prizeid=="1"){
                        prizestr="金牌";
                        prizeurl="/style/images/active/lday/images/1.png"
                        moneystr="50000";
                    }else if(prizeid=="2"){
                        prizestr="银牌";
                        prizeurl="/style/images/active/lday/images/2.png"
                        moneystr="30000";
                    }else if(prizeid=="3"){
                        prizestr="铜牌";
                        prizeurl="/style/images/active/lday/images/3.png"
                        moneystr="10000";
                    }else if(prizeid=="4"){
                        prizestr="奖杯";
                        prizeurl="/style/images/active/lday/images/J1.png"
                        moneystr="50000";
                    }else if(prizeid=="5"){
                        prizestr="奖杯";
                        prizeurl="/style/images/active/lday/images/J2.png"
                        moneystr="30000";
                    }else if(prizeid=="6"){
                        prizestr="奖杯";
                        prizeurl="/style/images/active/lday/images/J3.png"
                        moneystr="10000";
                    }
                
                    
                    var giftimg='<img title="'+prizestr+'" src="'+prizeurl+'" class="gt" />';
                    var giftstr='<p><span>'+data["timestamp"]+'</span>恭喜 <a href="javascript:void(0);" gn='+obj_box["no"]+' class=\"chatuser\" id='+obj_box["uid"]+'>' +decodeURIComponent(obj_box['uname'])+ '</a>'+this.chatgnum(obj_box["no"])+' 获得我秀奥运活动<b>' + itemstr + '</b>的' + giftimg + ' 并获得奇币奖励 ' + moneystr +'!</p>';
                    if(giftstr!=""){Chat.msgLen++;$("#chat_hall").append(giftstr);this.isScroll("chat_hall");}
                    var giftloadings=this.giftloading;
                    if(giftloadings==1){
                        this.showFlash(obj_box);
                    }else{
                        _alert('礼物展示出现意外！',3);
                    }
                    break;
                case 77: //鹊桥礼物发放
                    var giftIcon=obj_box.giftIcon,giftNum=obj_box.giftCount,giftName=obj_box.giftName,giftimg='',ugood=obj_box["userNo"],uid=obj_box["userId"],uname=decodeURIComponent(obj_box["userName"]),cugood=this.chatgnum(obj_box["userNo"]),tougood=obj_box["toUserNo"],touid=obj_box["toUserId"],touname=decodeURIComponent(obj_box['toUserName']),tocugood=this.chatgnum(obj_box["toUserNo"]);
                    giftimg+= '<img style=\"width:52px\" src="'+giftIcon+'" class="gt"/>';
                    var giftstr='<p><a href="javascript:void(0);" gn='+tougood+' class=\"chatuser\" id='+touid+'>' +touname+ '</a>'+tocugood+' 收到 <a href="javascript:void(0);" class=\"chatuser\" gn='+ugood+' id='+uid+'> '+uname+' </a>'+cugood+' 的鹊桥'+giftimg+giftNum+'座，1314天长地久。<span>('+data["timestamp"]+')</span></p>';
                    if(giftstr!=""){Chat.msgLen++;$("#chat_hall").append(giftstr);this.isScroll("chat_hall");}
                    var giftloadings=this.giftloading;
                    if(giftloadings==1){
                        this.showFlash(obj_box);
                    }else{
                        _alert('礼物展示出现意外！',3);
                    }
                    break;
                                case 47: //贴条操作
                                    clearInterval(ttsi);
                                    if(obj_box['add']==1){
                                        var nttime = new Date().getTime();
                                        $("#tt_"+data["touid"]).html('<img src="/style/images/note/bandingImg/tt'+obj_box['stk']+'.png" />');
                                        //try{ console.log(data["touid"]+"贴条id..."+obj_box['stk']);}catch(e){ }
                                        var ti = lj.checkin(data["touid"]);//try{ console.log(data["touid"]+"ti..."+ti);}catch(e){ }
                                        if(ti == -1){//try{ console.log(data["touid"]+"ti..."+ti);}catch(e){ }
                                            //try{ console.log("tieTiaoArray.length..."+tieTiaoArray.length);}catch(e){ }
                                            var tar = [data["touid"],parseInt(obj_box['stke'])*1000+nttime,obj_box['stk']];
                                            tieTiaoArray[tieTiaoArray.length]=tar;
                                        }else{
                                            tieTiaoArray[ti]=[data["touid"],parseInt(obj_box['stke'])*1000+nttime,obj_box['stk']];
                                        }
                                    }
                                    
                                    var ttstr='<p><span>'+data["timestamp"]+'</span><a href="javascript:void(0);" class=\"chatuser\" gn='+data["ugood"]+' id='+data["uid"]+' >'+decodeURIComponent(data["uname"])+' </a> 给 <a href="javascript:void(0);" class=\"chatuser\" gn='+data["tougood"]+' id='+data["touid"]+' >' +decodeURIComponent(data["touname"])+ '</a> 贴了一个条！</p>';
                                    Chat.msgLen++;
                                    $("#chat_hall").append(ttstr);
                                    this.isScroll("chat_hall");
                                    
//                                    try{ console.log("tieTiaoArray.length..."+tieTiaoArray.length);}catch(e){ }
//                                    if(tieTiaoArray.length>0){
//                                        for(var i in tieTiaoArray){
//                                            try{ console.log("贴条操作..."+tieTiaoArray[i][0]+"*"+tieTiaoArray[i][1]+"*"+tieTiaoArray[i][2]);}catch(e){ }
//                                        }
//                                    }
                                    ttsi = setInterval("lj.checkTietiao()", 10000);
                                    break;
                                case 48://魔法兔子
//                                    try{ console.log("收到48...");}catch(e){ }
//                                    try{ console.log("魔法兔子状态..."+_game.rabbitstatus);}catch(e){ }
//                                    try{ console.log("魔法兔子个人房状态..."+_game.rabbitclosed);}catch(e){ }
                                    if(_game.rabbitstatus==1&&_game.rabbitclosed==1){//try{ console.log("魔法兔子执行...");}catch(e){ }
                                        if(obj_box['type']==1){//try{ console.log("魔法兔子开始运行游戏...");}catch(e){ }
                                            mrid = obj_box['mrid'];//try{ console.log("mrid..."+mrid);}catch(e){ }
                                            showRabbit();
                                            setTimeout(function(){Dom.$swfId('ShellSmashRabbit').initRabbit(obj_box['mrid'],_show.emceeId,obj_box['gamelength']);},2000);
                                        }else if(obj_box['type']==2 && mrid == obj_box['mrid']){//try{ console.log("魔法兔子开奖...");}catch(e){ }
                                            var stype = 1;
                                            obj_box['bigawarduseridlist'];
                                            var stypeArray = obj_box['bigawarduseridlist'].split('|');
                                            for(var i in stypeArray){
                                                if(stypeArray[i]==_show.userId){
                                                    stype = 2;
                                                    break;
                                                }
                                            }
                                            Dom.$swfId('ShellSmashRabbit').showRabbitResult(obj_box['awardtype'],stype);
                                        }
                                    }
                                    break;
             }
            return str;
        }catch(e){}
    },
    beginLive:function(data){ //开始直播 DOM deal
        var obj_box=data;
        _show.showId=obj_box["showId"];
        $("#showTime").html("开播时间："+obj_box["showTime"]);
        Chat.getRankByShow();
        var roomtype=obj_box["roomType"];
        if(_show.emceeId!=_show.userId){ //不是主播
            if(roomtype>0){
                _show.deny=roomtype;
                if(roomtype==1){ //收费房间
                    $('#money').html(obj_box["money"]);
                    $('#mask2').show();        
                }else if(roomtype==2){//密码房间
                    $('#mask3').show();            
                }
                $('#chatroom_area').show();
                $('#chatroom_limit').show();
            }else{                        
                var playlive=new ObjvideoControl();
                playlive.con_moveid="JoyShowLivePlayer";
                playlive.collect_p(0);
            }
        }
    },
    endLive:function(){ //end play
        _show.showId=0;
        _show.local=0;
        $("#showTime").html("");
        Chat.getRankByShow();
        for(i=1;i<=5;i++){
             var sofa_o=$('#user_sofa .t'+i).find('img');
             sofa_o.attr({'seatnum':0,'src':'/style/images/default1.gif','title':''});
         }
         $('#get_sofa').hide();
         $("#usersonglist").html('');
         $(".lpet").html("<div id=\"JoyPet_left\"></div>");
         $(".rpet").html("<div id=\"JoyPet_right\"></div>");
         loadpet();
    },
    showGiftMsg:function(data){
        var str="";
        var showt=0;
        try{
            
        }catch(e){}
    },
    sortBy:function(reverse) { //sort
        reverse = (reverse) ? -1 : 1;
        return function (a, b) {
            a = a["sortnum"];
            b = b["sortnum"];
            if (a < b) {
                return reverse * -1;
            }
            else{
                return reverse * 1;
            }
        }
    },
    backTopUser:function(){
        this.minCount=500;
        this.isAll=0;
        Dom.$swfId("flashCallChat").chatToSocket(0,6,'{"_method_":"GetUsetList","pno":1,"rpp":0,"otype":1,"checksum":""}');        //翻页 0 - 50
    },
    getAllUser:function(){
        var that=this,backTimer=null;
        that.minCount=500;
        that.isAll=1;
        Dom.$swfId("flashCallChat").chatToSocket(0,6,'{"_method_":"GetUsetList","pno":1,"rpp":100,"otype":1,"checksum":""}');        //翻页 0 - 100
        backTimer=setTimeout(function(){that.backTopUser();},1000*60);
    },
    reflashCount:function(data){
        var udata=data["ct"];
        this.cntPeople=parseInt(udata[0]["ucount"]);
        $('#lm2_2').find('cite').html(this.cntPeople);
    },
    getChatOnline:function(data){ //fet onlinelist
        //clear_data deal
        this.arrPeople=[];
        this.arrManage=[];
        this.cntManage=0;
        this.cntPeople=0;
        this.arrVisitor=[];
        var udata=data["ct"];
        this.person=udata[1]['ulist'];
        this.cntPeople=parseInt(udata[0]["ucount"]);
        this.guePeople=parseInt(udata[0]["tucount"]);  //guest
        var perobj=this.person;
        this.initnum=perobj.length;
        this.initnum=(this.initnum > this.minCount) ? this.minCount : this.initnum;
        this.minorder=parseInt(perobj[this.initnum-1]["sortnum"]);//min ordernum
        var uinitnum=this.initnum;
        for(var b=0;b<uinitnum;b++){
            var user=perobj[b],utype=perobj[b]["userType"];
            if(utype==40){this.arrManage.push(user);this.cntManage++;}
            if(utype==20){
                this.arrVisitor.push(user);
            }else{
                this.arrPeople.push(user);
            }
        }
        /*
        userType:用户类型  主播：50   管理员：40   普通用户：30   游客：20   巡管：10   僵尸:5
        */
        //20120827 altheran
        this.arrPeople.sort(this.sortBy(true));
        this.chatPeople();
        this.chatManage();
    },
    gnum:function(gn){
        var goodnum=gn;
        var gnbuy="";
        if(goodnum!="" && goodnum.length<10){ //is buy goodnum
           gnbuy=goodnum;
        }
        return gnbuy;
    },
    chatgnum:function(gn){
        var goodnum=gn;
        var gnbuy="";
        if(goodnum!="" && goodnum.length<10){ //is buy goodnum
           gnbuy="(<span class=\"ugood\">"+goodnum+"</span>)";
        }
        return gnbuy;
    },
    getloc:function(arr,uid){//fech array eq
        var loc=-1;
        var arruser=arr;
        var uid=uid;
        for(var i in arruser){
            if(parseInt(arruser[i]["userid"])==uid){
               loc=i;
               break;
            }
        }
        return loc;
    },
    dealBadges:function(bid){ //活动徽章
      var badgeId=bid,badimg="";
      if(badgeId!=""){
          var arrBad=badgeId.split(",");
          var intlen=arrBad.length;
        for(var i=0;i<intlen;i++){
            var img=_badges[arrBad[i]];
            badimg+="<span class=\"actbadge\"><img src="+img+"></span>";    
        }
      }
      return badimg;
    },
    chatManage:function(){
        var managerArray=[],mitem="";
        var arrManage=this.arrManage;
        for(var key in arrManage){//manage
            var strMin1="",strMin2="",ptxt="",pcolor="";
            mitem=arrManage[key];
            if(mitem["h"]==0 || mitem["userType"]==50){//显身
                managerArray.push('<li id="manage_'+mitem["userid"]+'" tid="'+mitem["userid"]+'" onclick="UserListCtrl.chatPublic();" utype="'+mitem["userType"]+'"  level="'+mitem["level"]+'" goodnum="'+mitem["goodnum"]+'" richlevel="'+mitem["richlevel"]+'" order="'+mitem["sortnum"]+'" title="'+decodeURIComponent(mitem["username"])+'">');
                var actBadge=mitem["actBadge"],sbadges=""; //活动徽章
                if(actBadge!=""){sbadges=this.dealBadges(actBadge);}
                if(mitem["userType"]==10){ //巡管
                    ptxt="<span class='props patrol'></span>";
                    pcolor=" class='p'";
                }
                strMin1+=sbadges;
                if(mitem["richlevel"]>0){ //富豪等级
                    strMin1+=" <span class='cracy cra"+mitem["richlevel"]+"'></span>";    
                }
                if(mitem["vip"]!=0){//VIP
                    if(mitem["vip"]==1){strMin1+=" <span class='props vip1'></span>";}else if(mitem["vip"]==2){strMin1+=" <span class='props vip2'></span>";}
                }
                if(this.gnum(mitem["goodnum"])!=""){strMin1+=" <em"+pcolor+">"+mitem["goodnum"]+"</em>";}
                strMin2+=ptxt;
                if(mitem["sellm"]!=0){//代理标准
                    strMin2+=" <img src=\"/style/images/sell.gif\" width=\"35\" height=\"16\"/>";    
                }
                
                if(mitem["familyname"]!=""){//徽章
                    strMin2+=" <span class=family>"+mitem["familyname"]+"</span>";    
                }
                strMin2+=" <a"+pcolor+">"+decodeURIComponent(mitem["username"])+"</a>";
                if(strMin1!=""){managerArray.push('<p>'+strMin1+'</p>');}
                if(strMin2!=""){managerArray.push('<p>'+strMin2+'</p>');}
                managerArray.push('</li>');
            }
        }
        
        $('#loading_manage').remove();
        $('#lm2_1').find('cite').html(this.cntManage);
        $("#content2_1").html(managerArray.join(""));
    },
    chatPeople:function(){
        var userArray=[],visitorArray=[],giftArray=[],chatArray=[],pitem="",vitem="";
                //tieTiaoArray=[];
        var arrPeople=this.arrPeople;
                clearInterval(ttsi);
        for(var key in arrPeople){ //chatonline
            var strOn0="",strOn1="",strOn2="",ptxt="",pcolor="";
            pitem=arrPeople[key];
            if(pitem["h"]==0 || pitem["userType"]==50){//显身
                if(pitem["userType"]==50){//主播
                    $.ajax({url:"/index.php/Show/getucuid/uid/"+pitem["userid"],type:"get",datatype:"text",async:false,success:function(ucuid){
                        
                        userArray.push("<li id='online_"+pitem["userid"]+"' tid='"+pitem["userid"]+"' title='"+decodeURIComponent(pitem["username"])+"' utype='"+pitem["userType"]+"' goodnum='"+pitem["goodnum"]+"' level='"+pitem["level"]+"' richlevel='"+pitem["richlevel"]+"' order='"+pitem["sortnum"]+"' onclick='UserListCtrl.chatPublic();'><img style='width:40px' class='tou_xiang' src='http://video.7958.com/passport/avatar.php?uid="+ucuid+"&size=middle'/>");

                    }});
                    
                }else if(pitem["userType"]==5){//僵尸
                    $.ajax({url:"/index.php/Show/getucuid/uid/"+pitem["userid"],type:"get",datatype:"text",async:false,success:function(ucuid){
                    userArray.push("<li id='online_"+pitem["userid"]+"' tid='"+pitem["userid"]+"' title='"+decodeURIComponent(pitem["username"])+"' utype='"+pitem["userType"]+"' goodnum='"+pitem["goodnum"]+"' level='"+pitem["level"]+"' richlevel='"+pitem["richlevel"]+"' order='"+pitem["sortnum"]+"' onclick='UserListCtrl.chatPublic();'><img style='width:40px'  class='tou_xiang' src='http://video.7958.com/passport/avatar.php?uid="+ucuid+"&size=middle'/>");
                    }});
                }else{//富豪
                    $.ajax({url:"/index.php/Show/getucuid/uid/"+pitem["userid"],type:"get",datatype:"text",async:false,success:function(ucuid){
                    userArray.push("<li id='online_"+pitem["userid"]+"' tid='"+pitem["userid"]+"' title='"+decodeURIComponent(pitem["username"])+"'  utype='"+pitem["userType"]+"' goodnum='"+pitem["goodnum"]+"' level='"+pitem["level"]+"' richlevel='"+pitem["richlevel"]+"' order='"+pitem["sortnum"]+"' onclick='UserListCtrl.chatPublic();'><img style='width:40px'  class='tou_xiang' src='http://video.7958.com/passport/avatar.php?uid="+ucuid+"&size=middle'/>");
                    }});
                }
                if(pitem["userType"]==5){ //游客僵尸
                    $.ajax({url:"/index.php/Show/getucuid/uid/"+pitem["userid"],type:"get",datatype:"text",async:false,success:function(ucuid){
                    userArray.push("<span id='tt_"+pitem["userid"]+"' style='width:53px; height:32px;position:absolute; left:70px;'></span><p><a>"+decodeURIComponent(pitem["username"])+"</a><img class='tou_xiang' style='width:40px' src='http://video.7958.com/passport/avatar.php?uid="+ucuid+"&size=middle'/></p>");
                    }});
                }else{
                    var actBadge=pitem["actBadge"],sbadges=""; //活动徽章
                    if(actBadge!=""){sbadges=this.dealBadges(actBadge);}
                    //if(pitem["userType"]==10 || pitem["userType"]==60){
                    if(pitem["userType"]==10){ //巡管
                        ptxt="<span class='props patrol'></span>";
                        pcolor=" class='p'";
                    }
                    strOn1+=sbadges;
                    if(pitem["userType"]==50){ //主播
                        if(pitem["level"]>0){ //主播等级
                            strOn1+=" <span class='star star"+pitem["level"]+"'></span>";    
                        }
                    }else{
                        if(pitem["richlevel"]>0){
                            //strOn1+=" <span class='cracy cra"+pitem["richlevel"]+"'></span>";
                                                        var sx = 0;
                                                        if(pitem['star']){
                                                            sx = pitem['star'];
                                                        }
                                                        strOn1+="<img class='cracy cra"+pitem["richlevel"]+"' src='/style/images/sx"+sx+".gif'>";//123456
                        }
                    }
                    if(pitem["vip"]!=0){//VIP
                        if(pitem["vip"]==1){strOn1+=" <span class='props vip1'></span>";}else if(pitem["vip"]==2){strOn1+=" <span class='props vip2'></span>";}
                    }
                    
                    if(this.gnum(pitem["goodnum"])!=""){strOn1+=" <em"+pcolor+">"+pitem["goodnum"]+"</em>";}
                    
                    strOn2+=ptxt;
                    if(pitem["sellm"]!=0){//代理标准
                        strOn2+=" <img src=\"/style/images/sell.gif\" width=\"35\" height=\"16\"/>";    
                    }
                    if(pitem["familyname"]!=""){//徽章
                        strOn2+=" <span class=family>"+pitem["familyname"]+"</span>";
                    }
                    strOn2+=" <a"+pcolor+">"+decodeURIComponent(pitem["username"])+"</a>";//123456
                                        var ttstk = pitem["stk"]?pitem["stk"]:'';
//                                        if(ttstk==''){
//                                            strOn2+=" <a "+pcolor+">"+decodeURIComponent(pitem["username"])+"</a>";
//                                        }else{
//                                            strOn2+=" <a "+pcolor+">"+decodeURIComponent(pitem["username"])+"</a>";
////                                            var ncttime = new Date().getTime();
////                                            
////                                            var ti = lj.checkin(pitem["userid"]);
////                                            if(ti != -1){
////                                                tieTiaoArray[ti]=[pitem["userid"],parseInt(pitem["stke"])*1000+ncttime];
////                                            }else{
////                                                tieTiaoArray.push([pitem["userid"],parseInt(pitem["stke"])*1000+ncttime]);
////                                            }
//                                        }
                                        
                                        if(ttstk==''){
                                            strOn0+='<span id="tt_'+pitem["userid"]+'" style="width:53px; height:32px;position:absolute; left:70px;"></span>';
                                        }else{
                                            strOn0+='<span id="tt_'+pitem["userid"]+'" style="width:53px; height:32px;position:absolute; left:70px;"><img src="/style/images/note/bandingImg/tt'+pitem['stk']+'.png"></span>';
                                        }
                                        userArray.push(strOn0);
                    if(strOn2!=""){userArray.push('<p>'+strOn2+'</p>');}
                    if(strOn1!=""){userArray.push('<p>'+strOn1+'</p>');}//123456
                    
                }
                userArray.push("</li>");
                //在线观众 end
                if(pitem["userType"]==50){ //主播
                    giftArray.push("<li><a href=\"javascript:void(0);\" onclick=\"GiftCtrl.setGift("+pitem["userid"]+",'"+decodeURIComponent(pitem["username"])+"');\"><span class=\"star star"+pitem["level"]+"\"></span>"+decodeURIComponent(pitem["username"])+"</a></li>");
                    chatArray.push("<li><a href=\"javascript:void(0);\" onclick=\"GiftCtrl.setUser("+pitem["userid"]+",'"+decodeURIComponent(pitem["username"])+"');\"><span class=\"star star"+pitem["level"]+"\"></span>"+decodeURIComponent(pitem["username"])+"</a></li>");
                }
            }
                        var ttstk = pitem["stk"]?pitem["stk"]:'';//try{ console.log(pitem["userid"]+"stk..."+ttstk);}catch(e){ }
                        if(ttstk==''){
                            //strOn2+=" <a "+pcolor+">"+decodeURIComponent(pitem["username"])+"</a>";
                        }else{
                            //strOn2+=" <a "+pcolor+">"+decodeURIComponent(pitem["username"])+"</a>";
                            var ncttime = new Date().getTime();

                            var ti = lj.checkin(pitem["userid"]);
//                            try{ console.log("刷新用户列表...");}catch(e){ }
//                            try{ console.log("1贴条数组长度..."+tieTiaoArray.length);}catch(e){ }
                            if(ti != -1){//try{ console.log("贴条数组-1..."+ti);}catch(e){ }
                                //try{ console.log("tieTiaoArray"+ti+"*"+tieTiaoArray[ti]['userid']);}catch(e){ }
                                tieTiaoArray[ti]=[pitem["userid"],parseInt(pitem["stke"])*1000+ncttime,pitem['stk']];
                            }else{//try{ console.log("贴条数组ti..."+ti);}catch(e){ }
                                tieTiaoArray.push([pitem["userid"],parseInt(pitem["stke"])*1000+ncttime],pitem['stk']);
                            }
                            //try{ console.log("2贴条数组长度..."+tieTiaoArray.length);}catch(e){ }
                        }
        }
                
        var arrVisistor=this.arrVisitor; //visitor
        for(var key in arrVisistor){
            vitem=arrVisistor[key];
            $.ajax({url:"/index.php/Show/getucuid/uid/"+pitem["userid"],type:"get",datatype:"text",async:false,success:function(ucuid){
               userArray.push("<li id='online_"+vitem["userid"]+"' tid='"+vitem["userid"]+"' utype='"+vitem["userType"]+"' order='"+vitem["sortnum"]+"' richlevel=0 title='"+decodeURIComponent(vitem["username"])+"'><p><a>"+decodeURIComponent(vitem["username"])+"</a><img class='tou_xiang' style='width:40px' src='http://video.7958.com/passport/avatar.php?uid="+ucuid+"&size=middle'/></p></li>");    
            }});
        }
        if(this.cntPeople > this.minCount && this.isAll==0) {
            //userArray.push('<li onclick="JsInterface.getAllUser();" title="下一页" class="getuserall">点击更多 >> </li>')
        }
        $('#loading_online').remove();
        $('#lm2_2').find('cite').html(this.cntPeople);
        $("#content2_2").html(userArray.join(""));
        //$('#content2_2').append("<li style='text-align:right;width:122px;'><a>游客"+this.guePeople+"人</a></li>");
        $('#gift_userlist').html(giftArray.join(""));
        $('#chat_userlist').html(chatArray.join(""));
                ttsi = setInterval("lj.checkTietiao()", 10000);
    },
    reflashP:function(num){ //change people
        var rnum=num;
        var nowp=this.cntPeople || 0;
        if(rnum==0){
            nowp=(nowp>0)?(nowp-1):0;
        }else{
            nowp=nowp+1;
        }
        this.cntPeople=nowp;
        $('#lm2_2').find('cite').html(nowp);    
    },
    reflashM:function(num){ //change mananger
        var rnum=num;
        var nowm=this.cntManage || 0;
        if(rnum==0){
            nowm=(nowm>0)?(nowm-1):0;
        }else{
            nowm=nowm+1;
        }
        this.cntManage=nowm;
        $('#lm2_1').find('cite').html(nowm);    
    },
    reMinorder:function(){
         var varr=this.arrVisitor.length,parr=this.arrPeople.length;
         if(varr>0){
            this.minorder=parseInt(this.arrVisitor[varr-1]["sortnum"]);//min ordernum 
         }else{
            this.minorder=parseInt(this.arrPeople[parr-1]["sortnum"]);//min ordernum      
         }
    },
    remove:function(hostid){//simple remove
        var uid=hostid,meq=-1,peq=-1,veq=-1;
        if(uid<0){ //visitor
             veq=this.getloc(this.arrVisitor,uid);
             if(veq>=0){
                 $('#online_'+uid).remove();
                 this.arrVisitor.splice(veq,1);
             }
             this.reMinorder();
             this.reflashP(0);
         }else{
            peq=this.getloc(this.arrPeople,uid);
            if(peq>=0){ //people
                $('#online_'+uid).remove();
                this.arrPeople.splice(peq,1);
                this.reMinorder();
            }
            
            this.reflashP(0);
            meq=this.getloc(this.arrManage,uid);
            if(meq>=0){ //manager
                $('#manage_'+uid).remove();
                this.arrManage.splice(meq,1);
                this.reflashM(0);
            }
         }
         if(this.initnum>this.cntPeople){
             this.initnum--;
         }
         var total=parseInt(this.arrPeople.length)+parseInt(this.arrVisitor.length);
         if(total<35 && this.cntPeople>50){
            Dom.$swfId("flashCallChat").chatToSocket(0,6,'{"_method_":"GetUsetList","pno":1,"rpp":0,"otype":1,"checksum":""}');
         }
    },
    doAdd:function(data){ //simple add
        if(data){
            var udata=data;
            //alert(JSON.stringify(udata));
            var utype=data["userType"];
            var meq=-1,peq=-1;
            var userid=udata["userid"];
            if(userid<0){//visistor
                this.reflashP(1);
                if(this.initnum<this.minCount){
                    $('#content2_2').append("<li id='online_"+udata["userid"]+"' tid='"+udata["userid"]+"' order='"+udata["sortnum"]+"' utype='"+udata["userType"]+"' title='"+decodeURIComponent(udata["username"])+"'><p><a>"+decodeURIComponent(udata["username"])+"</a></p></li>");    
                    this.arrVisitor.push(udata);
                    this.initnum++;
                }
            }else{
                var oneorder=udata["sortnum"];
                peq=this.getloc(this.arrPeople,userid);
                if(peq<0)
                { //people
                    if(this.initnum<this.minCount)
                    {
                        this.initnum++;
                        this.arrPeople.push(udata);
                    }else{
                        if(this.arrVisitor.length > 0)
                        {var larr=this.arrVisitor.length;
                                this.arrVisitor.splice(larr-1,1);
                                this.arrPeople.push(udata);
                        }
                        else
                        {
                            if(oneorder>this.minorder){
                                var larr=this.arrPeople.length;
                                this.arrPeople.splice(larr-1,1,udata);
                            }
                        }
                    }
                    this.reflashP(1);
                    this.arrPeople.sort(this.sortBy(true));
                    this.reMinorder();
                    this.chatPeople();
                                        for(var i in tieTiaoArray){
                                            $("#tt_"+tieTiaoArray[i][0]).html('<img src="/style/images/note/bandingImg/tt'+tieTiaoArray[i][2]+'.png">');
                                        }
                }
                if(utype==40){
                    meq=this.getloc(this.arrManage,userid);
                    if(meq<0){ //manager
                        this.arrManage.push(udata);
                        this.arrManage.sort(this.sortBy(true));
                        this.reflashM(1);
                        this.chatManage();
                    }
                }
            }
        }
    },
    changeUser:function(data){ //更新用户信息

        var user=data["ct"],meq=-1,peq=-1;
        var userid=user["userid"],utype=user["userType"],uorder=user["sortnum"];
        peq=this.getloc(this.arrPeople,userid);
        if(peq>=0){ //people
            this.arrPeople.splice(peq,1,user);
        }else{
            if(uorder>this.minorder){
                var larr=this.arrPeople.length;
                this.arrPeople.splice(larr-1,1,user);
            }
        }
        this.arrPeople.sort(this.sortBy(true));
        this.reMinorder();
        this.chatPeople();
        
        if(utype==40){
            meq=this.getloc(this.arrManage,userid);
            if(meq>=0){ //manager
                this.arrManage.splice(meq,1,user);
                this.arrManage.sort(this.sortBy(true));
                this.chatManage();
            }
        }
        if(_show.userId==userid){
            var prichlevel=$('#online_'+userid).attr('richlevel');
            if(prichlevel>0){$('.pubChatSet').hide();}
            _show.richlevel=prichlevel;
        }
    }
    ,closeGiftSwf:function(){
        $('#flashCallGift').css({"width":"1px","height":"1px"});
        $('#flashCallGiftswf').css({"width":"1px","height":"1px"});
    },
    giftready:function(){
        this.giftloading=1;
        $('#flashCallGift').attr('name','flashCallGift');
        $('#flashCallGiftswf').attr('name','flashCallGiftswf');
    },
    loadGiftswfError:function(txt){
        var txtgift=txt;
        _alert(txtgift,5);
    },
    closeMyvideo:function(){
        //$('#myVideoBox').remove();
                $('#myVideoBox').css("display","none");
    },
    loadSwf:function(swf,height,width,id){
            var h='\
          <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="osmall_'+id+'" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" height="' + height + '" width="' + width + '">\
              <param name="movie" value="' + swf + '">\
              <param name="quality" value="high">\
              <param name="allowScriptAccess" value="always">\
              <param name="wmode" value="transparent">\
              <param name="allowFullScreen" value="true">\
              <embed  allowScriptAccess = "always" wmode="transparent" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowfullscreen="true" height="'+height+'" width="'+width+'">\
          </object>';
            return h;
    }
    ,
    clearGift:function(gname){
        if(gname=="f_small"){
            $('.f_small').remove();    
            this.inf=0;this.inf2=0;
        }else{
            $('.g_small').remove();    
            this.ing=0;this.ing2=0;    
        }
    },
    openSmallf:function(uid,uname,ugood){  //this.initflash
        var that=this,oPos=$('.chat_online').offset(),tpl="<div class=\"s_flash\"></div>",domf=Dom.$C("div"),fJson={},loadf="";
        that.inf++;
        domf.id="domflash"+that.inf;
        domf.className="f_small";
        domf.innerHTML=tpl;
        document.body.appendChild(domf);
        var domDiv=$('#domflash'+that.inf);
        loadf=that.loadSwf('/style/images/smallflash.swf',50,50,that.inf);
        fJson.l=oPos.left;
        fJson.t=oPos.top+20+((that.inf-1)*82);
        domDiv.find('.s_flash').html(loadf);
        domDiv.find('').html("<a href='/"+ugood+"' title='"+uname+"' target='_blank'>"+uname+"</a>");
        $('#domflash'+that.inf).css({top:fJson.t,left:fJson.l+2}).show();
        var inittimer=setTimeout(
            function(){
                that.inf2++;
                $('#domflash'+that.inf2).remove();
                that.inf--;
                if(that.inf<=0){that.clearGift("f_small");}
            },3000);
    }
    ,
    openSmallg:function(gid,uname,ugood,gcount,gname,gimg){ //initgift
        var that=this,oPos=$('.chat_online').offset(),tpl="<div class=\"tit\"><div class=\"txt\"></div></div><div class=\"g_gif\"><img src=\"/style/images/smallgift.gif\" /></div><div class=\"sticon\"></div>",domg=Dom.$C("div"),gJson={};
        that.ing++;
        
        domg.id="domgift"+that.ing;domg.className="g_small";domg.innerHTML=tpl;document.body.appendChild(domg); 
        
        var domDiv=$('#domgift'+that.ing);    
        gJson.l=oPos.left;
        gJson.t=oPos.top+20+(that.ing*52);
        domDiv.find('.txt').html("<a href='/"+ugood+"' title='"+uname+"' target='_blank'>"+uname+"</a>送"+gcount+"个"+gname+"<img src='"+gimg+"'/></div>");
        $('#domgift'+that.ing).css({top:gJson.t,left:gJson.l-170}).show();
        var smallgtimer=setTimeout(function(){
            that.ing2++;
            $('#domgift'+that.ing2).remove();
            that.ing--;
            if(that.ing<=0){that.clearGift("g_small");}
        },3000);
    },
    removeFlyword:function(){//JsInterface.removeFlyword
        $('#flashFlyWord').css({"width":"1px","height":"1px"});
    },
    guestdrag:function(o,drag){  
        if(typeof(o)=="string"){o=Dom.$getid(o);} 
        if(typeof(drag)=="string"){drag=Dom.$getid(drag);} 
        if(o){
            o.orig_x=parseInt(o.style.left)-oPos.scrollX();  
            o.orig_y=parseInt(o.style.top)-oPos.scrollY();  
            drag.onmousedown=function(a){  
                var d=document;  
                if(!a)a=window.event || a;  
                var x=a.clientX+oPos.scrollX()-o.offsetLeft;  
                var y=a.clientY+oPos.scrollY()-o.offsetTop;  
                document.onselectstart=function(e){
                    return false;
                };
                document.body.onselectstart=function(e){
                    return false;
                };
                if(o.setCapture)  
                   o.setCapture();  
                else if(window.captureEvents)  
                   window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);  
                d.onmousemove=function(a){  
                   if(!a)a=window.event || a;  
                   o.style.left=a.clientX+oPos.scrollX()-x+'px';  
                   o.style.top=a.clientY+oPos.scrollY()-y+'px';  
                   o.orig_x=parseInt(o.style.left)-oPos.scrollX();  
                   o.orig_y=parseInt(o.style.top)-oPos.scrollY();  
                }  
                d.onmouseup=function(){  
                    if(o.releaseCapture)  
                        o.releaseCapture();  
                    else if(window.captureEvents)  
                        window.captureEvents(Event.MOUSEMOVE|Event.MOUSEUP);  
                    d.onmousemove=null;  
                    d.onmouseup=null;  
                    d.ondragstart=null;  
                    d.onselectstart=null;  
                    d.onselect=null;  
                } 
            }
        }
    },
    closeVC:function(){    //隐藏验证码
        $("#ChatWrap").css("top","-1000000px");
        var rich=Chat.richLevel(_show.userId);
        if(rich>0){Chat.setDisabled(3);}else{Chat.setDisabled(5);}
        _vc = "";
    },
    addJFSkill:function(pos, op, func){    //宠物加技能
        if(pos=="left"){
            if(_show.userId==_show.emceeId && $("li[name=petdeal]").length==0){
                str="<span id=\"petline\" class=\"menuline\" style=\"display:block;\"></span><li name=\"petdeal\" onclick=\"Pet.skill('"+func+"');\" class=\"tdeal\" style=\"display:block;\">"+op+"</li>";
                $("#ctrllist").append(str);
            }
        }else{
            if(_show.userId!=_show.emceeId && $("li[name=petdeal]").length==0){
                str="<span id=\"petline\" class=\"menuline\" style=\"display:block;\"></span><li name=\"petdeal\" onclick=\"Pet.skill('"+func+"');\" class=\"tdeal\" style=\"display:block;\">"+op+"</li>";
                $("#ctrllist").append(str);
            }
        }
    },
    removeJFSkill:function(pos){    //移除宠物技能
        if(pos=="left"){
            if(_show.userId==_show.emceeId && $("li[name=petdeal]").length>0){
                $("li[name=petdeal], #petline").remove();
            }
        }else{
            if(_show.userId!=_show.emceeId && $("li[name=petdeal]").length>0){
                $("li[name=petdeal], #petline").remove();
            }
        }
    },
    swapIndex:function(pos, index){    //设置宠物显示位置
        if(pos=="left"){
            //$(".lpet").css("z-index", index);
            if(index==0){$(".lpet").css("left", "-198px");}
            else if(index==1){$(".lpet").css({"left":"0", "z-index":"299"});}
        }else{
            if(index==0){$(".rpet").css("right", "-198px");}
            else if(index==1){$(".rpet").css({"right":"0", "z-index":"299"});}
        }
    }
}
/*=====================================================================检测flash加载 begin=====================================================================================*/
function flashSwf(){ 
    var videotimer=null,chattimer=null,attrflash=[];
     if(_show.emceeId==_show.userId){ //主播身份 ---CamLive
         attrflash=['JoyCamLivePlayer','flashCallChat'];
     }else{
        attrflash=['JoyShowLivePlayer','flashCallChat'];
     }
     var f1=attrflash[0],f2=attrflash[1];
     chattimer=setInterval(function(){
        try{
           var cparam=Dom.$swfId(f2).flashready();
           if(cparam){
               if(cparam=="chat"){
                    $('#flashCallChat').attr('name','flashCallChat');
                    if(_show.deny==0){ //是普通房间                    
                        var chatR=new ObjvideoControl();
                        var chatnode="";
                        chatR.getclientNode();
                        chatnode=chatR.chatdomain;
                        if(chatnode!=""){
                            chatR.socket_ip=chatnode;    
                        }
                        Dom.$swfId(f2).initialize(chatR.socket_ip,chatR.default_ip,chatR.socket_port,_show.emceeId+"|"+_show.roomId, 0);
                    }
                    clearInterval(chattimer);
               }
           }
        }catch(e){}
     },400);
     videotimer=setInterval(function(){
        try{
           var vparam=Dom.$swfId(f1).flashready(); 
           if(vparam){
               switch(vparam){
                    case "live":
                        $('#JoyCamLivePlayer').attr('name','JoyCamLivePlayer');
                        try{
                            if(Dom.$getid("VideoStudioControl")){var intStudio=VideoStudioControl.GetVersion();}
                            _show.isHD=1; //高清
                            Dom.$swfId(f1).setBrowseType(true);
                        }catch(e){
                            Dom.$swfId(f1).setBrowseType(false);
                        }
                        var Camlive=new ObjvideoControl();
                        Camlive.con_moveid=f1;
                        Camlive.collect_v(_show.showId>0?1:0);
                     break;
                    case "play":
                        $('#JoyShowLivePlayer').attr('name','JoyShowLivePlayer');
                        if(_show.deny==0){//是普通房间
                            if(_show.showId<=0 && _show.offline>0){ //没有直播有离线视频
                                Dom.$swfId(f1).showRecord(_show.offline);
                                break;
                            }
                            var Showlive=new ObjvideoControl();
                            Showlive.con_moveid=f1;
                            if(_show.userId==_show.emceeId){
                                Showlive.collect_p(1);    
                            }else{Showlive.collect_p(0);}
                            
                        }
                     break;
                }
                clearInterval(videotimer);
           }
        }catch(e){}
    },400);
}
function guestflashSwf(){
    var guestplaytimer=null;
    var f1="JoyShowLivePlayer";
    guestplaytimer=setInterval(function(){
        try{
           var pparam=Dom.$swfId(f1).flashready(); 
           if(pparam){
               $('#JoyShowLivePlayer').attr('name','JoyShowLivePlayer');
                var Showlive=new ObjvideoControl();
                Showlive.con_moveid=f1;                
                Showlive.collect_p(1);
                clearInterval(guestplaytimer);
           }
        }catch(e){
        }
    },400);
}
/*=====================================================================检测flash加载 end=====================================================================================*/
/**
 * 推后的结果
*/
function OnVideoCtrlEvent(rdata){
    try{
        Dom.$swfId("JoyCamLivePlayer").ActiveXBack(rdata);//success,fail        
    }catch(e){}
}

function loadpet(){    //加载宠物
    var uid=_show.userId;
    var zid=_show.emceeId;
    var url="/showPet.do?";
    $(".lpet").append("<scr"+"ipt>swfobject.embedSWF(\"/style/swf/JoyPet.swf\",\"JoyPet_left\", 230,300,\"10.0\", \"\",{pos:\"left\",url:\""+url+"\",uid:\""+uid+"\",zid:\""+zid+"\"},{quality:\"high\",wmode:\"transparent\",allowscriptaccess:\"always\"});</scr"+"ipt>");
    $(".rpet").append("<scr"+"ipt>swfobject.embedSWF(\"/style/swf/JoyPet.swf\",\"JoyPet_right\", 230,300,\"10.0\", \"\",{pos:\"right\",url:\""+url+"\",uid:\""+uid+"\",zid:\""+zid+"\"},{quality:\"high\",wmode:\"transparent\",allowscriptaccess:\"always\"});</scr"+"ipt>");
}

/**
 * 初始化User Cache
*/
var InitCache=function(){
    _vc = "";
    //statistics.promotion(); //判断推广
    
    $.getJSON("/index.php/Show/show_showData/eid/"+_show.emceeId+"/rid/"+_show.roomId+Sys.ispro+"/t/"+Math.random(),function(json){
        if(json && json.code==0){
            
            var book="", sofa="", song="", interest="", showPrice="";

            function defineVars(json){
                var udata=json.data;
                var user=udata.userInfo,node=udata.nodeInfo,show=udata.showInfo,egg=udata.eggInfo;rabbit=udata.rabbitInfo;

                _game.eggstatus=egg.status;_game.egginterval=egg.interval,_game.eggclosed=egg.closed;
                                //_game.rabbitstatus=rabbit.status;_game.rabbitinterval=rabbit.interval,_game.rabbitclosed=rabbit.closed;
                _show.up=node.up;_show.down=node.down;
                _show.chat=node.chat==""?"csp":node.chat;_show.userId=user.userId;
                _show.version=udata.version;_show.admin=user.admin;_show.sa=user.sa;_show.deny=show.deny;
                _show.showId=show.showId;_show.is_public=show.isPublicChat;_show.closed=show.closed;
                _show.richlevel=user.richlevel;_show.local=0;_show.showTime=show.showTime;
                book=user.isBookmark, sofa=show.seats, song=show.songApply, interest=user.interest, showPrice=show.showPrice;
            
                if(Sys.ispro!=""){
                    setTimeout(function(){if(_show.userId<0){UAC.openUAC(0);return false;}}, 5000);
                }
                setTimeout(showReg, 1);
            }

            setTimeout(function(){defineVars(json);}, 1);

            function showReg(){
                $('#showTime').html(_show.showTime);
                if(_show.userId<=0){
                    $('.pri_login').html("欢迎您进入房间。请 <a href=\"javascript:UAC.openUAC(1);\">注册</a> 或 <a href=\"javascript:UAC.openUAC(0);\">登录</a>，与主播进行交流和互动。");
                }

                setTimeout(loadFlash, 1);
            }

            function loadFlash(){
                var initLive=Dom.$C('div');
                if(_show.emceeId==_show.userId){initLive.id="JoyCamLivePlayer";}else{initLive.id="JoyShowLivePlayer";}
                initLive.innerHTML='<div class="fInstall">请先 <a href=" http://get.adobe.com/cn/flashplayer/" target="_blank">安装FLASH播放器</a></div>';
                $('#livebox').append(initLive);
                var _script=Dom.$C("script");
                if(_show.emceeId==_show.userId){
                    _script.text="swfobject.embedSWF(\"/style/swf/liveCamLivePlayer.swf?roomId="+_show.goodNum+"&rtmpHost="+_show.chat+"&cdn="+_show.cdn+"&keyframe="+_show.zjg+"&fps="+_show.fps+"&bandwidth="+_show.zddk+"&width="+_show.width+"&height="+_show.height+"&quality="+_show.pz+"&rtmpPort=1935&appName="+_show.appName+"&stream="+_show.stream+"\",\"JoyCamLivePlayer\",480,360,\"10.0\", \"\",{},{quality:\"high\",wmode:\"opaque\",allowscriptaccess:\"always\"});"   
                }else{
                    _script.text="swfobject.embedSWF(\"/style/swf/liveShowLivePlayer.swf?roomId="+_show.goodNum+"&rtmpHost="+_show.chat+"&cdn="+_show.cdnl+"&keyframe="+_show.zjg+"&fps="+_show.fps+"&bandwidth="+_show.zddk+"&width="+_show.width+"&height="+_show.height+"&quality="+_show.pz+"&rtmpPort=1935&appName="+_show.appName+"&stream="+_show.stream+"\",\"JoyShowLivePlayer\", 480,360,\"10.0\", \"\",{},{quality:\"high\",wmode:\"opaque\",allowscriptaccess:\"always\"});"     
                }
                Dom.$getid('livebox').appendChild(_script);
                flashSwf();

                setTimeout(operate, 1);
            }

            function operate(){
                if(_show.emceeId==_show.userId || _show.admin==1){$('.my_tab').show();}

                $('#chatSet').attr('state',_show.is_public);
                if(_show.is_public==0){
                    $('#chatSet').attr('state',1).html('关闭公聊室<cite class="off"></cite>');
                    $('#chat_close').show();
                }else{
                    $('#chatSet').attr('state',0).html('开启公聊室<cite class="on"></cite>');
                    $('#chat_close').hide();
                }

                if(_show.emceeId==_show.userId){
                   //$('.tab-info ul').append("<li><a href=\"javascript:WishGiftCtrl.makeWish();\" class=\"m-dream\" title=\"许愿\">许愿</a></li><li class=\"end\"><a href=\"javascript:Song.userVodSong();\" class=\"m-song\" title=\"点歌本\">点歌本</a></li>");
                }
                if(_show.admin==1 && _show.emceeId!=_show.userId){
                    $('.tab-info ul').append("<li class=\"end\"><a href=\"javascript:JsInterface.showCloseReasonDiv();\" class=\"m-song\" title=\"关闭直播\">关闭直播</a></li>");  
                }

                if(_show.admin!=1 && _show.emceeId!=_show.userId && _show.sa!=1){
                    $('.login_tips').show();
                    if(_show.richlevel==0){$('.pubChatSet').show();}
                }

                if(_show.emceeId==_show.userId){
                    var strsong="";
                    if(song==1){
                        applySongFlag = 1;
                        strsong='<span id="songApply_1" class="sdeal" onclick="Song.setSongApply(1);">允许点歌<cite class="on"></cite></span><span id="songApply_2" class="sdeal" style="display:none" onclick="Song.setSongApply(2);">禁止点歌<cite class="off"></cite></span>';
                    }else{
                        applySongFlag = 2;
                        strsong='<span id="songApply_1" class="sdeal" style="display:none" onclick="Song.setSongApply(1);">允许点歌<cite class="on"></cite></span><span id="songApply_2" class="sdeal" onclick="Song.setSongApply(2);">禁止点歌<cite class="off"></cite></span>';
                    }
                    $('.song_deal').find('p').html('<a href="javascript:Song.batchAddSong();" title="添加歌曲">添加歌曲</a><span> | </span><a href="javascript:Song.userVodSong();" title="管理歌曲">管理歌曲</a>');
                    $('.song_deal').append(strsong);
                }else{
                    $('.sdeal').show();
                    if(song==1){
                        applySongFlag = 1;
                        $('#songApplyShow').html('允许');
                        $('#songApplyIcon').attr('class','on');
                        $('.song_deal').find('p').html('<a id="songApply" href="javascript://" onclick="Song.wangSong();" title="我要点歌">我要点歌</a>');      
                    }else{
                        applySongFlag = 2;
                        $('#songApplyShow').html('禁止');
                        $('#songApplyIcon').attr('class','off');
                    }
                }

                if(_show.emceeId==_show.userId || _show.admin==1 || _show.sa==1){$('.tdeal,.menuline').show();}
                if(_show.emceeId==_show.userId || _show.admin==1){$('.dmanage').show();}
                if(_show.emceeId==_show.userId && _show.emceeLevel >= 13){$('.dblack').show();}

                setTimeout(setRoom, 1);
            }

            function setRoom(){
                if(_show.deny>0){
                    if(_show.deny==1){ //收费房间
                        $('#money').html(showPrice);
                        $('#mask2').show();        
                    }else if(_show.deny==2){//密码房间
                        $('#mask3').show();            
                    }
                    else if(_show.deny==3){ //房间满员
                        $('#mask1').show();            
                    }
                    else if(_show.deny==4){ //禁止账号
                        $('#mask4').show();
                    }
                    $('#chatroom_area').show();
                    $('#chatroom_limit').show();
                }

                if(interest==2){
                    $('.attentions, #gzbtn').attr("state","2");
                    $('.attentions, #gzbtn').html("取消关注");
                }else{
                    $('.attentions, #gzbtn').attr("state","1");
                    $('.attentions, #gzbtn').html("+ 关注");
                }

                if(book==0){
                    $('.favrite-room').attr({"title":"收藏房间","state":0}).html("收藏房间");
                }else{
                    $('.favrite-room').attr({"title":"取消收藏","state":1}).html("取消收藏");
                }

                var strsofa="";
            
                
                sofa["seat6"]=sofa["seat5"];
                
                for(var i=1;i<=4;i++){
                    
                    var sf=sofa["seat"+i];
                   
                    if(i<5){
                        if(sf.count==0){
                            strsofa+='<li><a href="javascript:void(0)" data-seat="0" class="seatlight t'+i+'"><img onerror="javascript:this.src=\'/style/avatar/0/0_big.jpg\'"  src="'+sf.icon+'" title="'+sf.nick+'" seatnum="'+sf.count+'"/><span seatid="'+i+'">抢座</span> </a></li>'; 
                            sofaUserList[i] = sf.nick;
                        }else{
                            strsofa+='<li><a href="javascript:void(0)" data-seat="0" class="seatlight t'+i+'"><img onerror="javascript:this.src=\'/style/avatar/0/0_big.jpg\'"  src="'+sf.icon+'" title="'+sf.nick+'" seatnum="'+sf.count+'"/><span seatid="'+i+'">'+sf.nick+'</span> </a></li>';
                            sofaUserList[i] = sf.nick;
                        }
                     }else if(i==4){
                        if(sf.count==0){
                         strsofa+='<li style="float:left;margin-left:320px;margin-top:'+(7-i)*7+'px;"><a href="javascript:void(0)" data-seat="0" class="seatlight t'+i+'"><img onerror="javascript:this.src=\'/style/avatar/0/0_big.jpg\'"  src="'+sf.icon+'" title="'+sf.nick+'" seatnum="'+sf.count+'"/><span seatid="'+i+'">抢座</span> </a></li>';
                        }else{
                         strsofa+='<li style="float:left;margin-left:320px;margin-top:'+(7-i)*7+'px;"><a href="javascript:void(0)" data-seat="0" class="seatlight t'+i+'"><img onerror="javascript:this.src=\'/style/avatar/0/0_big.jpg\'"  src="'+sf.icon+'" title="'+sf.nick+'" seatnum="'+sf.count+'"/><span seatid="'+i+'">'+sf.nick+'</span> </a></li>'; 
                        }
                      }else{
                         if(sf.count==0){
                             strsofa+='<li style="float:left;margin-top:'+(7-i)*7+'px;"><a href="javascript:void(0)" data-seat="0" class="seatlight t'+i+'"><img onerror="javascript:this.src=\'/style/avatar/0/0_big.jpg\'"  src="'+sf.icon+'" title="'+sf.nick+'" seatnum="'+sf.count+'"/><span seatid="'+i+'">抢座</span> </a></li>';
                         }else{
                             strsofa+='<li style="float:left;margin-top:'+(7-i)*7+'px;"><a href="javascript:void(0)" data-seat="0" class="seatlight t'+i+'"><img onerror="javascript:this.src=\'/style/avatar/0/0_big.jpg\'"  src="'+sf.icon+'" title="'+sf.nick+'" seatnum="'+sf.count+'"/><span seatid="'+i+'">'+sf.nick+'</span> </a></li>';
                         }
                    }    
                } 
                strsofa="<ul class='giftsoft'>"+strsofa+"</ul>";
                $('#user_sofa').append(strsofa);
                $("#user_sofa .giftsoft li").eq(0).css("margin-left","0");
                setTimeout(initUpload, 1);
            }

            function initUpload(){
                var initControl;
                if(_show.emceeId==_show.userId){
                    initControl=Dom.$C('div');
                    initControl.id="objectControl";
                    initControl.innerHTML='<OBJECT ID="VideoStudioControl" width="0" height="0" CLASSID="CLSID:C7F8892D-04EB-46C9-B0F4-24AA44EB7217"><param name="xmlSetting" value="" /><param name="model" value="joy" /></OBJECT>';
                    Dom.$gTag("body")[0].appendChild(initControl);
                }

                setTimeout(loadOtherList, 1);
            }

            function loadOtherList(){
                if(_show.userId<=0){
                    var resentTimer=setTimeout(function(){Song.initVodSong();GiftCtrl.giftList();Chat.getRankByShow();},1*60*1000);
                }else{
                    setTimeout(function(){Song.initVodSong();GiftCtrl.giftList();Chat.getRankByShow();}, 3*1000);
                }
            }

            _game.eggtimer=setTimeout("showEgg()",_game.eggstart*60*1000);
                        //_game.eggtimer=setTimeout("showEgg()",10*1000);
                        //_game.rabbittimer=setTimeout("showRabbit()",_game.rabbitstart*60*1000);
                        //_game.rabbittimer=setTimeout("showRabbit()",10*1000);
        

        }
    });
    //更改房间背景
    ChatMessage.changeRoomBackImg(_show.bgimg, false);

    var RankTimer=setInterval(function(){Chat.getRankByShow();},1*60*1000);//本场排行榜
    var BalanceTimer=setInterval(function(){Chat.getUserBalance();},5*60*1000);//用户秀币更新
    GiftCtrl.gift_to_id=_show.emceeId;
    $("#giftto").html(_show.emceeNick);
    var jiathis_config={url:window.location,title:"大家好，我正在直播，邀请大家强势围观 url地址 "+window.location,hideMore:false};    
}

/**
*发布广播
**/
var broadcast = {
    showBroadcast: function() {
        $('#msgGb').html(speaktxt);
        $('#tishikuang').fadeIn();
        
    },
    closeBroadcast: function() {
        $('#tishikuang').fadeOut();
    },
    submitBroadcast: function() {
        // json获取
        var jsontext = null;
        var msgGb = $('#msgGb').val();
        var roomid = _show.emceeId;
        var callstate = null;
        var msg = '';
        var biglen = $('#msgGb').val().length>80;
        var checkinput = msgGb.indexOf("输入文字不超过50个字")>0;
        if(biglen)
        {
            msg = '输入文字不能超过40个字';
            _alert(msg,5);
            return;
        }
        if(checkinput)
        {
            msg = '消息不能为空！';
            _alert(msg,5);
            return;
        }
                
        $.ajax({
            contentType:"application/x-www-form-urlencoded:charset=UTF-8",
            url:'/index.php/Show/speaker_handler/',
            data:'msg='+encodeURIComponent(msgGb)+'&emceeId='+_show.goodNum+'&t='+new Date().getTime(),
            //url:'speaker_handler_msg_'+escape(msgGb)+'_emceeId_'+roomid+'_time_'+new Date().getTime()+'.htm',
            type:'get',
            async:false,
            success: function(data){
                jsontext = $.parseJSON(data);
                callstate = jsontext['code'];
                msg = jsontext['msg'];
            }
        });
        if (callstate == 1) {
            if(!msg){
                msg = '消息不能为空！';
            }
            _alert(msg, 5);
        } 
        else if (callstate == 2) 
        {
            //msg = msg == ''? '您还没有登录！请先登录！' : msg;
            //console.log('msg:'+msg.length);
            UAC.openUAC(0);

        } 
        else if (callstate == 3) 
        {
            //msg = msg ==''? '您的秀豆余额不足！请充值后再发布广播！' : msg;
                        msg = '您的秀豆余额不足！请充值后再发布广播！';
            _alert2(msg, [function() {
                window.location = '/my/charge/'
            },
            function() {
                _closePop();
            }]);
        } 
        else if (callstate == 4) 
        {
            //msg = msg ==''? '对不起，您已经被禁言！目前不能发布广播！' : msg;
                        msg = '对不起，您已经被禁言！目前不能发布广播！';
            _alert(msg,7);
        }else if(callstate == 5){
        
            msg = '输入文字不能超过50个字';
            _alert(msg,5);
            
        }else if(callstate == 0){
            //msg = msg ==''?'发送成功!':msg;
                //msg = '发送成功!';
            _alert('发送成功!',5);
            broadcast.closeBroadcast();
            //$('#msgGb').val('| 输入文字不超过50个字。每次广播话费300秀币');
            Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"submitBroadcast","userName":"' + jsontext['userName'] + '","userNo":"' + jsontext['userNo'] + '","emceeId":"' + jsontext['emceeId'] + '","msg":"' + jsontext['msg'] + '"}');
        }
    },
    initialize: function() {
        $('#msgGb').bind('click',function(){
               $(this).css("color",'#000');
               $(this).val().indexOf("输入文字不超过50个字")>0?$(this).val(''):'';
        });
        $('#btnsubmit').bind('click', broadcast.submitBroadcast);
        $('#scroll_lb').bind('click',broadcast.showBroadcast);
        $('#guan').bind('click',broadcast.closeBroadcast);
    }
};



/**
@para scrollblock 滚动条容器
@para scrollbar   滚动条按钮
@para theContent  要滚动的内容外层容器
@para direction      水平或者垂直滚动 (待扩展)
@para theText      要滚的内容容器 注：white-space:nowrap 保证不换行
@para leftarrow      左箭头
@para rightarrow  右箭头
**/

function myscrollbar(scrollblock,scrollbar,theContent,theText,leftarrow,rightarrow)
{
        this.sblock = $('#'+scrollblock);    // 滑块
        this.sbar = $('#'+scrollbar);        // 滚动条
        this.offset = this.sbar.offset();    // 滚动条位置
        this.scontent = $('#'+theContent);          // 内容容器
        this.larrow = $('#'+leftarrow);        // 左箭头
        this.rarrow = $('#'+rightarrow);    // 右箭头
        this.thetxt = $('#'+theText);
        this.clen = this.thetxt.innerWidth() - this.scontent.innerWidth();
        //this.lefto = this.offset['left'];
        this.len = this.sbar.innerWidth()-this.sblock.innerWidth();
        
        this.flag = 1;
        //console.log("滚动条创建成功")
}
 $(function(){
    function myscrollbar(scrollblock,scrollbar,theContent,theText,leftarrow,rightarrow)
        {
         this.lefto = this.offset['left'];
        }   
 })

myscrollbar.prototype = {
        constructor:myscrollbar,

        Initialize:function()
        {
             // 初始化左侧位置
             this.clen = this.thetxt.innerWidth() - this.scontent.innerWidth();
             this.sblock.css('left',this.len+'px');
             this.thetxt.css('left','-'+this.clen+'px');
        },
        execute:function()
        {
            this.drag();
            this.toleft();
            this.toright();
            this.Initialize();
            
            this.larrow.bind('mouseenter',function()
            {
                    that.flag = 0;    
            });
                
            this.larrow.bind('mouseleave',function()
            {
                    that.flag = 1;    
            });
            
            this.rarrow.bind('mouseenter',function()
            {
                    that.flag = 0;    
            });
                
            this.rarrow.bind('mouseleave',function()
            {
                    that.flag = 1;    
            });
            
            this.sbar.bind('mouseenter',function()
            {
                    that.flag = 0;    
            });
                
            this.sbar.bind('mouseleave',function()
            {
                    that.flag = 1;    
            });
            
            // 鼠标放开释放拖动
            $(document).mouseup(function()
            {
                $(document).unbind('mousemove');
            });
        },
        drag:function(){
            that=this;
            
            that.sblock.mousedown(function(event)
            {
                // 鼠标相对于滑块坐标
                var offsetX = event.offsetX ? event.offsetX: event.layerX;
                var offsetY = event.offsetY ? event.offsetY: event.layerY;
                
                // 拖动
                $(document).mousemove(function(event) 
                {
                    // 初始化左侧值
                    var x = event.clientX - offsetX - that.lefto;
                        // 滑动块只能在0~len之间
                        x = x <= 0 ? 0 : x;
                        x = x >= that.len ? that.len : x;
                        
                        // 执行移动动作
                        that.sblock.css({'left':x,'top':0});
                        that.thetxt.css({left:-Math.round((that.clen*x/that.len))});

                })
            
            });
        },
        
        toleft:function(){
            // 左箭头方法
            that = this;
            that.larrow.click(t = function(e) {
                // 初始化左值
                var x = that.sblock.css('left');
                    x = x=='auto'? that.len : x;
                    x = parseInt(x)
                    x += -100;
                    
                    // 滑动块只能在0~len之间
                    x = x <= 0 ? 0 : x;
                    x = x >= that.len ? that.len : x;
                    
                    // 限定内容左侧范围
                    that.clen= that.clen<0?0:that.clen;
                    // 定位移动元素
                    that.sblock.css({'left':x,'top':0});
                    that.thetxt.css({left:-Math.round((that.clen*x/that.len))});
            });
        },
        // 右箭头方法
        toright:function(){
            that = this;
            
            that.rarrow.click(function(e) {
                // 初始化左值
                var x = that.sblock.css('left');
                    x = x=='auto'? that.len : x;
                    x = parseInt(x)
                    x += 100;
                    
                    // 滑动块只能在0~len之间
                    x = x <= 0 ? 0 : x;
                    x = x >= that.len ? that.len : x;
                    
                    // 限定内容左侧范围
                    that.clen= that.clen<0?0:that.clen;
                    // 执行移动动作
                    that.sblock.css({'left':x,'top':0});
                    that.thetxt.css({left:-Math.round((that.clen*x/that.len))});
            });
        }

    }
        
var lj = {
    checklogin:function(){
        if(_show.userId<=0 && Dom.$getid("uac_div")==null){
            UAC.openUAC(0);
            return false;
        }
        return true;
    },
    sendTietiao:function(ttid){
        if($('#tietiaob').hasClass('tt')){
            _alert('正在执行贴条，请稍等！',3);
            return false;
        }
        if(UserListCtrl.user_id==_show.emceeId){
            _alert('不能对主播贴条！',3);
            return false;
        }
        $('#tietiaob').addClass("tt");
        $.ajax({
            url:"/index.php/Show/show_bandingNote/recieverId/"+UserListCtrl.user_id+"/rid/"+_show.emceeId+"/noteId/"+ttid+"/",
            data:"t/"+Math.random(),
            type:'get',
            async:false,
            success: function(data){
                data=evalJSON(data);
                if(data.code=='0'){
                    Chat.getUserBalance();
                    _alert('贴条成功！并成功扣除了'+data.money+'秀币！',3);
                    Dom.$swfId("flashCallChat")._chatToSocket(2,2,'{"_method_":"sendTietiao","touid":"'+UserListCtrl.user_id+'","touname":"'+UserListCtrl.nickname+'","tougood":"'+UserListCtrl.goodnum+'","stk":"'+ttid+'","stke":"100"}');
                }else if(data.code=='1'){
                    _alert('金额不足！',3);
                }else if(data.code=='2'){
                    _alert('请求出错！',3);
                }else if(data.code=='3'){
                    _alert('已被贴过！',3);
                }
            }
        });
        $(tietiaob).removeClass("hover").removeClass("tt").hide();
    },
    checkTietiao:function(){
        var t = new Date().getTime();
        if(tieTiaoArray.length){
            for(var i in tieTiaoArray){
                if(tieTiaoArray[i][1] <= t){
                    $("#tt_"+tieTiaoArray[i][0]).html('');
                    tieTiaoArray.splice(i, 1);
                    break;
                }
            }
        }
    },
    set_tietiaocontextmenu:function(c){
        var box=$('#tietiaob');
        var alertPop=getMiddlePos('tietiaob');
    var vl=alertPop.pl;
    var vt=alertPop.pt;
    box.css({"left":vl+"px","top":vt+"px","z-index": "520","position":"absolute"}).show();
    },
    checkin:function(tuid){
        for(var i in tieTiaoArray){
            if(tieTiaoArray[i][0] == tuid){
                return i;
            }
        }
        return -1;
    },
    checkCss:function(){
        if(!$('#tietiaob').hasClass('hover')){
            $('#tietiaob').removeClass("tt");
            $('#tietiaob').hide();
        }
    }
}

$('#tietiao,.user-Person .add_note').live("click",function(e){
    if(lj.checklogin()){
        if(_show.userId==""){return false;}
        var a=$(this);
        lj.set_tietiaocontextmenu(a);
    }
});

$('#tietiaob').live("mouseenter",function(e){
        $(this).addClass("hover");
        clearTimeout(ccc);
}).live("mouseleave",function(e){
        $(this).removeClass("hover");
        if(!isInRegion($('#tietiaob'),e.pageX,e.pageY)){cc();}
});

var mrid = 0;
var tieTiaoArray = [];
var ccc;
var cc = function(){window.ccc = setTimeout("lj.checkCss()",500);}
var ttsi = setInterval("lj.checkTietiao()", 10000);
setInterval("lj.checklogin()", 60000);

function roll(bw,wrap_w){
    if($("#gift_recent_next>p").size()>0){
        $("#gift_recent>p").html($("#gift_recent_next>p:first").html());
        $("#gift_recent_next>p:first").remove();
    
        $("#gift_recent").css('position','relative');
        $("#gift_recent p").css('position','absolute');
        bw=$("#gift_recent").width();
        wrap_w=$("#gift_recent p").width();
        if(bw>=wrap_w){
            $("#gift_recent p").css("left",bw-wrap_w+"px");
        }else{
            $("#gift_recent p").css("left","0px");
        }
    }
    
    $("#gift_recent p").animate({"left":(-wrap_w-10)+"px"},13000,function(){
        $(this).css("left",(bw-wrap_w)+"px");
        roll(bw,wrap_w);
    })
}