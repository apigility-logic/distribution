//websocket 断线重链
(function(global,factory){if(typeof define==="function"&&define.amd){define([],factory)}else{if(typeof module!=="undefined"&&module.exports){module.exports=factory()}else{global.ReconnectingWebSocket=factory()}}})(this,function(){if(!("WebSocket" in window)){return}function ReconnectingWebSocket(url,protocols,options){var settings={debug:false,automaticOpen:true,reconnectInterval:1000,maxReconnectInterval:30000,reconnectDecay:1.5,timeoutInterval:2000,maxReconnectAttempts:null,binaryType:"blob"};if(!options){options={}}for(var key in settings){if(typeof options[key]!=="undefined"){this[key]=options[key]}else{this[key]=settings[key]}}this.url=url;this.reconnectAttempts=0;this.readyState=WebSocket.CONNECTING;this.protocol=null;var self=this;var ws;var forcedClose=false;var timedOut=false;var eventTarget=document.createElement("div");eventTarget.addEventListener("open",function(event){self.onopen(event)});eventTarget.addEventListener("close",function(event){self.onclose(event)});eventTarget.addEventListener("connecting",function(event){self.onconnecting(event)});eventTarget.addEventListener("message",function(event){self.onmessage(event)});eventTarget.addEventListener("error",function(event){self.onerror(event)});this.addEventListener=eventTarget.addEventListener.bind(eventTarget);this.removeEventListener=eventTarget.removeEventListener.bind(eventTarget);this.dispatchEvent=eventTarget.dispatchEvent.bind(eventTarget);function generateEvent(s,args){var evt=document.createEvent("CustomEvent");evt.initCustomEvent(s,false,false,args);return evt}this.open=function(reconnectAttempt){ws=new WebSocket(self.url,protocols||[]);ws.binaryType=this.binaryType;if(reconnectAttempt){if(this.maxReconnectAttempts&&this.reconnectAttempts>this.maxReconnectAttempts){return}}else{eventTarget.dispatchEvent(generateEvent("connecting"));this.reconnectAttempts=0}if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","attempt-connect",self.url)}var localWs=ws;var timeout=setTimeout(function(){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","connection-timeout",self.url)}timedOut=true;localWs.close();timedOut=false},self.timeoutInterval);ws.onopen=function(event){clearTimeout(timeout);if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onopen",self.url)}self.protocol=ws.protocol;self.readyState=WebSocket.OPEN;self.reconnectAttempts=0;var e=generateEvent("open");e.isReconnect=reconnectAttempt;reconnectAttempt=false;eventTarget.dispatchEvent(e)};ws.onclose=function(event){clearTimeout(timeout);ws=null;if(forcedClose){self.readyState=WebSocket.CLOSED;eventTarget.dispatchEvent(generateEvent("close"))}else{self.readyState=WebSocket.CONNECTING;var e=generateEvent("connecting");e.code=event.code;e.reason=event.reason;e.wasClean=event.wasClean;eventTarget.dispatchEvent(e);if(!reconnectAttempt&&!timedOut){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onclose",self.url)}eventTarget.dispatchEvent(generateEvent("close"))}var timeout=self.reconnectInterval*Math.pow(self.reconnectDecay,self.reconnectAttempts);setTimeout(function(){self.reconnectAttempts++;self.open(true)},timeout>self.maxReconnectInterval?self.maxReconnectInterval:timeout)}};ws.onmessage=function(event){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onmessage",self.url,event.data)}var e=generateEvent("message");e.data=event.data;eventTarget.dispatchEvent(e)};ws.onerror=function(event){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onerror",self.url,event)}eventTarget.dispatchEvent(generateEvent("error"))}};if(this.automaticOpen==true){this.open(false)}this.send=function(data){if(ws){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","send",self.url,data)}return ws.send(data)}else{throw"INVALID_STATE_ERR : Pausing to reconnect websocket"}};this.close=function(code,reason){if(typeof code=="undefined"){code=1000}forcedClose=true;if(ws){ws.close(code,reason)}};this.refresh=function(){if(ws){ws.close()}}}ReconnectingWebSocket.prototype.onopen=function(event){};ReconnectingWebSocket.prototype.onclose=function(event){};ReconnectingWebSocket.prototype.onconnecting=function(event){};ReconnectingWebSocket.prototype.onmessage=function(event){};ReconnectingWebSocket.prototype.onerror=function(event){};ReconnectingWebSocket.debugAll=false;ReconnectingWebSocket.CONNECTING=WebSocket.CONNECTING;ReconnectingWebSocket.OPEN=WebSocket.OPEN;ReconnectingWebSocket.CLOSING=WebSocket.CLOSING;ReconnectingWebSocket.CLOSED=WebSocket.CLOSED;return ReconnectingWebSocket});


var reconnFlag = false;
var timerID = 0;
var modefontcolor = [];
var onLineUserhtml='';
//modefontcolor数组中一维代表登录模式
//1电脑直播手机观看
//2手机直播手机观看
//3电脑观看
//数组二维代表字体颜色(0聊天1礼物2公告)
modefontcolor[1] = ['#ffffff','#ff9900','yellow'];
modefontcolor[2] = ['#717171','#ff9900','red'];
modefontcolor[3] = ['#717171','#ff9900','red'];
modefontcolor[4] = ['#717171','#ff9900','red'];

//socket数据
var SocketIO = {

    _firstLogin:false,
    _initConnect:function() {
        console.log('正在建立连接...');
        try{
            socket  =  new WebSocket('ws://chatroom.mimilove520.com:7272');
        }catch(e){
            console.log('连接异常 ： '+e);
            return;
        }
        SocketIO._wbSocket = socket;
        socket.onclose = function() {
            console.log('连接关闭.');
        }

        socket.onopen = function() {
            if(!SocketIO._firstLogin) {
                if(User.isLogin){
                    var data = {};
                    //'{"_method_":"login","user_name":"'+userInfo.client_name+'", "user_id":"'+userInfo.user_id+'", "levelid":"'+userInfo['levelid']+'", "daoju":"'+userInfo['daoju']+'", "token":"'+token+'", "ucuid":"'+ucuid+'", "room_id":"'+_show['roomId']+'"}')
                    data._method_ = "login";
                    data.user_name = User.nickname;
                    data.user_id = User.id;
                    data.levelid = User.level;
                    data.daoju   = "",
                        data.token   = User.token;
                    data.ucuid = '';
                    data.room_id  = Anchor.curroomnum;
                    console.log('ws'+JSON.stringify(data));
                    SocketIO._sendMsg(JSON.stringify(data));
                    SocketIO._firstLogin = true;
                } else {
                    var data = {};
                    //'{"_method_":"login","user_name":"'+userInfo.client_name+'", "user_id":"'+userInfo.user_id+'", "levelid":"'+userInfo['levelid']+'", "daoju":"'+userInfo['daoju']+'", "token":"'+token+'", "ucuid":"'+ucuid+'", "room_id":"'+_show['roomId']+'"}')
                    data._method_ = "login";
                    data.ucuid = '';
                    data.room_id  = Anchor.curroomnum;
                    SocketIO._sendMsg(JSON.stringify(data));
                    SocketIO._firstLogin = true;
                }


            }
        }

        socket.onmessage = SocketIO._msgReceive
    },

    _chatMessage:function(msg){
        var data = {
            _method_ : "SendPubMsg",
            client_name : User.nickname,
            content   : msg,
            checksum : ""
        };
        console.log('发送信息'+JSON.stringify(data));
        SocketIO._sendMsg(JSON.stringify(data));
    },
    _sendMsg:function(msgBuf){
        if(msgBuf!=null&&msgBuf!='undefined'){
            console.log(msgBuf)
            SocketIO._wbSocket.send(msgBuf);
        }else{
            console.log('发送消息为空!');
        }
    },

    _msgReceive:function(event) {
        var data = JSON.parse(event.data);
        console.log(data);
        if(data.type=="sysmsg.alert"){
            data.type="sysmsg_alert";
        }
        if(data.type == 'error.kicked'){
            eval('_chat._func_kicked(data)');
        }else{
            eval('_chat._func_' + data.type  + '(data)');
        }
    }
}
//消息逻辑处理
var _chat = {
    _func_LightHeart:function(data){
        console.log(data);
    },
    remove_msg:function(){
        while ($("#chat_hall>p").length > 100) {
                $("#chat_hall").children().first().remove();
            }
        // if($("#chat_hall>p").length > 100) {
        //     $("#chat_hall>p").slice(0,50).remove()
        // }
    },
    gift_msg:function(count,html) {
        if(count != 0){
            $(".msg-box .msg-con").append(html);
            $(".live-msg-list").append(html);
            _chat.remove_msg();
            setTimeout("_chat.gift_msg("+ (count-1) +",'"+html+"')",500);
            if(mode<3){
                var scrojh=$("#upchat_hall")[0].scrollHeight;
                $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
            }else{
                Fn.scrollTop();
            }
        }
    },
    _func_login: function(data) {
        //var imgpath = _chat.img_path(data.user_id);
        console.log(data);
        _chat.show_message(data.client_name,"来到了直播间",data.levelid,1,0,data.user_id,data.avatar);
    },
    _func_kicked: function(data) {
        console.log("踢出房间...");
        $(".layer").removeClass("hide");
        var data = {content:data.content};
        var html = template('outroom', data);
        document.getElementById('layer-box').innerHTML = html;
        
        //setTimeout(function(){window.location.href=HOST_URL;},6000); 
    },
    _func_sysmsg: function(data) {
        _chat.show_message("系统提示",data.content,"","",2,'');
    },
    _func_sysmsg_alert:function(data){
        alert(data.content);
    },
    _func_sendGift: function (data) {
        console.log(JSON.stringify(data));

        if(mode < 3){
            msgHtml ='<p><label><img src="http://ob83ribqd.bkt.clouddn.com/img/level/public_icon_vip'+data.levelid+'@2x.png" style="margin-bottom: -2px;margin-right:2px;" width="25" height="15">'+data.from_client_name+'</label>：送了1个<font style="color:'+modefontcolor[mode][1]+'";> '+data.giftName+'</font></p>';
        }else{
            var errorImg='/style/avatar/0/0_big.jpg';
            var srcImg=_chat._isHasImg(data.from_client_avatar);
            if(srcImg){
                msgHtml ='<li class="msg  not-show" id="'+data.from_user_id+'">'
                +'<img class="avatar" src="'+data.from_client_avatar+'">'
                +'<p class="username quotename">'+data.from_client_name+'</p>'
                +'<div class="user-msg-box clearfix">'
                +'<div class="content normal type1  left"><p style="color:#ff8000">送播主一个<span><img src="'+data.giftPath+'" alt=""></span></p></div>'
                +'</div>'
                +'</li>'; 
            }else{
                msgHtml ='<li class="msg  not-show" id="'+data.from_user_id+'">'
                +'<img class="avatar" src="'+errorImg+'">'
                +'<p class="username quotename">'+data.from_client_name+'</p>'
                +'<div class="user-msg-box clearfix">'
                +'<div class="content normal type1  left"><p style="color:#ff8000">送播主一个<span><img src="'+data.giftPath+'" alt=""></span></p></div>'
                +'</div>'
                +'</li>'; 
            } 
        }
        setTimeout("_chat.gift_msg("+ (data.giftCount) +",'"+msgHtml+"')",500);

        Fn.sendShowqueue(data.giftCount,data.from_client_name,data.from_client_avatar,data.giftPath,data.giftName);
        if(data.giftId==84||data.giftId==79||data.giftId==73||data.giftId==85||data.giftId==81){
            Fn.bigshowgift(data.giftId);
        }
    },

    _func_onLineClient: function(data) {
        console.log(data);
        var all_num = data.all_num;
        if(all_num!=0){
            for (var i=0;i<all_num;i++) {

                if( typeof(data.client_list[i]) == "object") {
                    this._onlineList(data.client_list[i])
               }
                if(typeof(data.adminer_list[i]) == "object"){
                    this._onlineList(data.adminer_list[i])
                }
        }
            $(".layfolkList").html(onLineUserhtml);
            var onlineViewer = all_num+' 观众'; 
            $(".onWatch").html(onlineViewer); 
        }else{
            $(".layfolkList").html("<p style='padding-top:20px;font-size:12px;text-align:center;color:#999;'>该房间"+all_num+"观众</p>");
        }
        
        onLineUserhtml='';
    },
    _onlineList:function(data){
        var errorImg='/style/avatar/0/0_big.jpg';
        var errorImg2='/style/meilibo/vzboostyle/img/fortuneLevel/c_lv_0@2x.png';
        var srcImg=_chat._isHasImg(avatarPaths(data.user_id));
        if(data.levelid==null){
            data.levelid=0;
        }
        if(srcImg){
             onLineUserhtml +='<li class="userItem onlineItem layfolkItem clearfix" data-id="'+data.user_id+'">'
                    +'<img class="userIcon" src="'+avatarPaths(data.user_id)+'">'
                    +'<div class="userMsg">'
                    +'<p class="userName">'+data.client_name+'</p>'
                    +'<img class="charmlevel level" src="'+HOST_URL+'/style/meilibo/vzboostyle/img/fortuneLevel/c_lv_'+data.levelid+'@2x.png" data-charm="0">'
                    +'</div>'
                    +'</li>';
        }else{
             onLineUserhtml +='<li class="userItem onlineItem layfolkItem clearfix" data-id="'+data.user_id+'">'
            +'<img class="userIcon" src="'+errorImg+'">'
            +'<div class="userMsg">'
            +'<p class="userName">'+data.client_name+'</p>'
            +'<img class="charmlevel level" src="'+errorImg2+'" data-charm="0">'
            +'</div>'
            +'</li>';
        }
    },
    _isHasImg:function(pathImg){
        var ImgObj=new Image();
        ImgObj.src= pathImg;        
        if( (ImgObj.width > 0 && ImgObj.height > 0))
        {
            return true;
        } else {
            return false;
        }
    },
    _func_error:function(data) {
         _chat.show_message("系统提示",data.content,"","",2,'');
    },

    _func_SendPubMsg:function(data) {
        
        if(data.type=="SendPubMsg"){
            _chat.show_message(data.from_client_name,data.content,data.levelid,1,0,data.from_user_id,data.avatar);
            if(data.fly =="FlyMsg"){
                var div='<div>'+data.from_client_name+'：'+data.content+'</div>';;
                $(".chat_barrage_box").append(div);
                Fn.init_screen();
            }
        }

    },
    _func_ping:function(data) {

        var msg = {};
        msg._method_ = "pong";

        SocketIO._sendMsg(JSON.stringify(msg));
    },

    _func_logout: function(data) {
        console.log(data);
    },
    //showType表示消息在不同聊天环境下的样式 eg:
    //1:PC端直播 手机直播 手机观看
    //2:pc端直播 手机直播 PC端观看
    //msg_type消息种类 0:聊天2:系统提示；
    show_message: function(nickName,msg,level,showType,msg_type,user_id,imgpath ) {
        var color = modefontcolor[mode][0];
        var _msg = '';
        var user_id=''+user_id+'';
        if(msg_type == 1) {
            _msg = '<p><div class ="user_nickname" user_id='+user_id+'><label>'+nickName+'</label></div>:<font color="'+color+'">'+msg+'</font></p>'
        } else if(msg_type == 2) {
            _msg = '<p><font color="'+modefontcolor[mode][2]+'" class="firstfont">'+msg+'</font></p>'
        } else {
            if(showType==0){
                _msg = '<p><label class ="user_nickname" user_id='+user_id+'><img src="http://ob83ribqd.bkt.clouddn.com/img/level/public_icon_vip'+level+'@2x.png" style="margin-bottom: -2px;margin-right:2px;" width="25" height="15" >'+nickName+'</label> : <font color="'+color+'">'+msg+'</font></p>'
            }else if(showType == 1){
                if(mode >=3){
                    _msg = '<li class="msg not-show" id="'+user_id+'">'
                        +'<img class="avatar" onerror="this.src=\'/style/avatar/0/0_big.jpg\'" src="'+avatarPaths(user_id)+'" data-id="'+user_id+'">'
                        +'<p class="username quotename">'+nickName+'</p>'
                        +'<div class="user-msg-box clearfix">'
                        +'<div class="content normal type1  left"><p>'+msg+'</p></div>'
                        +'</div>'
                        +'</li>';
                }else{
                    _msg = '<p><label class ="user_nickname" user_id='+user_id+'><img src="http://ob83ribqd.bkt.clouddn.com/img/level/public_icon_vip'+level+'@2x.png" style="margin-bottom: -2px;margin-right:2px;" width="25" height="15" >'+nickName+'</label> : <font color="'+modefontcolor[mode][0]+'">'+msg+'</font></p>'
                }
            } else if(showType==2) {

            }
        }

        $('#chat_hall').append(_msg);
        _chat.remove_msg();
        if(mode<3){
            var scrojh=$("#upchat_hall")[0].scrollHeight;
            $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
        }else{
        	if($('.chat-msg-box').length>0){
        		 Fn.scrollTop();
        	}
        }
    }
}

function sysmsg(msg) {
    $("#chat_hall").append("<p><font color='greenyellow'>" + msg + "<br /></font></p>");
}

function onLineClient(data) {
    $(".userinfo .unum").html(data.all_num);

    var onLineUserhtml = '';
    for (var i=0;i<5;i++) {
        onLineUserhtml += '<li><img src="'+avatarPath(data.client_list[i].avatar)+'"></li>';
    }

    $("#userpic").html(onLineUserhtml);
}

function onUserLogin(data) {
    $("#chat_hall").append("<p><font color='greenyellow'>" + msg + "<br /></font></p>");
}

function avatarPath(path) {
    return path;
}
function avatarPaths(path) {
    return "/style/avatar/"+$.md5(path.toString()).substring(0,3)+"/"+path+"_small.jpg"
}

