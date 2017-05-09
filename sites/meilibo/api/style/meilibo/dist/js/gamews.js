//websocket 断线重链
(function(global,factory){if(typeof define==="function"&&define.amd){define([],factory)}else{if(typeof module!=="undefined"&&module.exports){module.exports=factory()}else{global.ReconnectingWebSocket=factory()}}})(this,function(){if(!("WebSocket" in window)){return}function ReconnectingWebSocket(url,protocols,options){var settings={debug:false,automaticOpen:true,reconnectInterval:1000,maxReconnectInterval:30000,reconnectDecay:1.5,timeoutInterval:2000,maxReconnectAttempts:null,binaryType:"blob"};if(!options){options={}}for(var key in settings){if(typeof options[key]!=="undefined"){this[key]=options[key]}else{this[key]=settings[key]}}this.url=url;this.reconnectAttempts=0;this.readyState=WebSocket.CONNECTING;this.protocol=null;var self=this;var ws;var forcedClose=false;var timedOut=false;var eventTarget=document.createElement("div");eventTarget.addEventListener("open",function(event){self.onopen(event)});eventTarget.addEventListener("close",function(event){self.onclose(event)});eventTarget.addEventListener("connecting",function(event){self.onconnecting(event)});eventTarget.addEventListener("message",function(event){self.onmessage(event)});eventTarget.addEventListener("error",function(event){self.onerror(event)});this.addEventListener=eventTarget.addEventListener.bind(eventTarget);this.removeEventListener=eventTarget.removeEventListener.bind(eventTarget);this.dispatchEvent=eventTarget.dispatchEvent.bind(eventTarget);function generateEvent(s,args){var evt=document.createEvent("CustomEvent");evt.initCustomEvent(s,false,false,args);return evt}this.open=function(reconnectAttempt){ws=new WebSocket(self.url,protocols||[]);ws.binaryType=this.binaryType;if(reconnectAttempt){if(this.maxReconnectAttempts&&this.reconnectAttempts>this.maxReconnectAttempts){return}}else{eventTarget.dispatchEvent(generateEvent("connecting"));this.reconnectAttempts=0}if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","attempt-connect",self.url)}var localWs=ws;var timeout=setTimeout(function(){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","connection-timeout",self.url)}timedOut=true;localWs.close();timedOut=false},self.timeoutInterval);ws.onopen=function(event){clearTimeout(timeout);if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onopen",self.url)}self.protocol=ws.protocol;self.readyState=WebSocket.OPEN;self.reconnectAttempts=0;var e=generateEvent("open");e.isReconnect=reconnectAttempt;reconnectAttempt=false;eventTarget.dispatchEvent(e)};ws.onclose=function(event){clearTimeout(timeout);ws=null;if(forcedClose){self.readyState=WebSocket.CLOSED;eventTarget.dispatchEvent(generateEvent("close"))}else{self.readyState=WebSocket.CONNECTING;var e=generateEvent("connecting");e.code=event.code;e.reason=event.reason;e.wasClean=event.wasClean;eventTarget.dispatchEvent(e);if(!reconnectAttempt&&!timedOut){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onclose",self.url)}eventTarget.dispatchEvent(generateEvent("close"))}var timeout=self.reconnectInterval*Math.pow(self.reconnectDecay,self.reconnectAttempts);setTimeout(function(){self.reconnectAttempts++;self.open(true)},timeout>self.maxReconnectInterval?self.maxReconnectInterval:timeout)}};ws.onmessage=function(event){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onmessage",self.url,event.data)}var e=generateEvent("message");e.data=event.data;eventTarget.dispatchEvent(e)};ws.onerror=function(event){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","onerror",self.url,event)}eventTarget.dispatchEvent(generateEvent("error"))}};if(this.automaticOpen==true){this.open(false)}this.send=function(data){if(ws){if(self.debug||ReconnectingWebSocket.debugAll){console.debug("ReconnectingWebSocket","send",self.url,data)}return ws.send(data)}else{throw"INVALID_STATE_ERR : Pausing to reconnect websocket"}};this.close=function(code,reason){if(typeof code=="undefined"){code=1000}forcedClose=true;if(ws){ws.close(code,reason)}};this.refresh=function(){if(ws){ws.close()}}}ReconnectingWebSocket.prototype.onopen=function(event){};ReconnectingWebSocket.prototype.onclose=function(event){};ReconnectingWebSocket.prototype.onconnecting=function(event){};ReconnectingWebSocket.prototype.onmessage=function(event){};ReconnectingWebSocket.prototype.onerror=function(event){};ReconnectingWebSocket.debugAll=false;ReconnectingWebSocket.CONNECTING=WebSocket.CONNECTING;ReconnectingWebSocket.OPEN=WebSocket.OPEN;ReconnectingWebSocket.CLOSING=WebSocket.CLOSING;ReconnectingWebSocket.CLOSED=WebSocket.CLOSED;return ReconnectingWebSocket});

var gameFlag = false;
var gametimerID = 0;

//socket数据
var Gamesocket='';
var GameSocketIO = {

    _firstLogin:false,
    _initConnect:function() {
        console.log('正在建立连接...');
        try{
            if(gameType==2){
                Gamesocket  =  new WebSocket('ws://game.meilibo.net:7575');
            }else if(gameType==1){
                Gamesocket  =  new WebSocket('ws://game.meilibo.net:7474');
            }else{
                Gamesocket  =  new WebSocket('ws://game.meilibo.net:7676');
            }
        }catch(e){
            console.log('连接异常 ： '+e);
            return;
        }
        GameSocketIO._wbSocket = Gamesocket;

        Gamesocket.onclose = function() {
            console.log('连接关闭.');

            if (!gameFlag) {
                gametimerID = setInterval(GameSocketIO._initConnect,2000);
                gameFlag = true;
                // GameSocketIO._firstLogin = false;
            }
        }

        Gamesocket.onopen = function() {
            console.log("打开链接");
            if (gameFlag) {
                window.clearInterval(window.gametimerID);
                gametimerID=0;
                gameFlag = false;
            }
            console.log(!GameSocketIO._firstLogin);
            if(!GameSocketIO._firstLogin) {
                var data = {};
                data._method_ = "login";
                data.room_id  = room_id;
                data.user_name = User.nickname;
                data.user_type=1;
                data.user_id = User.id;
                data.token   = User.token;
                GameSocketIO._sendMsg(JSON.stringify(data));
                GameSocketIO._firstLogin = true;

            }
            
        }

        Gamesocket.onmessage = GameSocketIO._msgReceive
    },

    _sendMsg:function(msgBuf){
        if(msgBuf!=null&&msgBuf!='undefined'){
            GameSocketIO._wbSocket.send(msgBuf);
        }else{
            console.log('发送消息为空!');
        }
    },

    _msgReceive:function(event) {
        var data = JSON.parse(event.data);
        console.log(data);
        eval('_game._func_' + data.type  + '(data)');

    },

    _betTing:function(roleName,betMoney){
        var data={};
        data._method_="gamePlay";
        data.room_id = room_id;
        data.token   = User.token;
        data.user_id = User.id;
        data.user_name = User.nickname;
        data.user_type=4; 
        data.role = roleName;
        data.money = betMoney;
        GameSocketIO._sendMsg(JSON.stringify(data));

    }
}
GameSocketIO._initConnect();
//消息逻辑处理
var countdown=0,countdown2=38;
var color='',poker='',biggest_cards='',num1='',num2='',num3='';
var newpoker=[],arr=[];
var sameColor=false;
var numberarry=[],colorarry=[];
var niuniu=[['10','10','10'],['1','9','10'],['1','1','8'],['1','2','7'],['1','3','6'],['1','4','5'],['2','3','5'],['2','2','6'],['2','4','4'],['2','8','10'],['2','9','9'],['3','3','4'],['3','7','10'],['3','8','9'],['4','6','10'],['4','8','8'],['4','7','9'],['5','5','10'],['5','6','9'],['5','7','8'],['6','6','8'],['6','7','7']];
var result=[];
var err_true=true;
var catmousearr=[];
var one_flg=false,samecolor=false;
var gametime="";
var $c=0;
var _game = {
    card:'',
    _func_ping:function(data){
        var msg = {};
        msg._method_ = "pong";
        GameSocketIO._sendMsg(JSON.stringify(msg));
    },
    _func_login:function(data){
        console.log("游戏登陆成功");
        clearInterval(gametime);
        this.endFn();
    },
    _func_error:function(data){
        var  type=data.type;
        if(type=='error'){
            err_true=false;
        }
    },
    _func_gameChange:function(data){
        $(".gamebox").remove();
        var msg=data.message;
        GameSocketIO._firstLogin = false;
        Gamesocket.close();
        if(msg==1){
            gameType=1;
            var data = {gamename:"炸金花"};
            var html = template('GoldenFlower', data);
            document.getElementById('gamebox').innerHTML = html;
        }else if(msg==2){
            gameType=2;
            var data = {gamename:"斗牛牛"};
            var html = template('Bullfighting', data);
            document.getElementById('gamebox').innerHTML = html;
        }else{
            gameType=3;
            var data = {gamename:"猫鼠乱斗"};
            var html = template('Catmouse', data);
            document.getElementById('gamebox').innerHTML = html;
        }
        // if (!gameFlag) {
        //         GameSocketIO._initConnect();
        //         gameFlag = true;
        // }
    },
    _func_serverTime:function(data){
        countdown=data.message;
        console.log(countdown);
        clearInterval(gametime);
        gametime=setInterval(function(){
            countdown++;
            if(countdown>55){
                    countdown=0;
            }
            _game.settime(countdown);
        },1000)
    },
    _func_gameWinFly:function(data){
        console.log(data);
    },
    millionFn:function(n1,n2,n3){
        if(n1>=10000){
            num1=n1/10000+'万';
        }else{
            num1=n1;
        }
        if(n2>=10000){
            num2=n2/10000+'万';
        }else{
            num2=n2;
        }
        if(n3>=10000){
            num3=n3/10000+'万';
        }else{
            num3=n3;
        }
    },
    _func_userFill:function(data){
        this.millionFn(data.message[0],data.message[1],data.message[2]);
        $('.gamepoint1 .points2').text(num1);
        $('.gamepoint2 .points2').text(num2);
        $('.gamepoint3 .points2').text(num3);
    },
     _func_peopleFill:function(data){
        this.millionFn(data.message[0],data.message[1],data.message[2]);
        $('.gamepoint1 .points1').text(num1);
        $('.gamepoint2 .points1').text(num2);
        $('.gamepoint3 .points1').text(num3);
    },
    _func_result:function(data){
        console.log(data);
        bglance_money=bglance_money+data.message;
        $(".bglance_money").text(bglance_money);
        if(data.message!=0){
            $(".game_popups").addClass("animt");
            var victory = {
                    value:data.message,
                };
            var html = template('victory', victory);
            document.getElementById('game_popups').innerHTML = html;
        }
    },
    _func_openCard:function(data){
        card=data.message.card;
        biggest_cards=data.message.biggest_cards;
        countdown2=38;
        for(var i=0;i<card.length;i++){
            var _card=card[i];
            for(var j=0;j<_card.length;j++){
                var _card_=_card[j];
                if(_card_[0]=='diamond'||_card_[0]=='heart'){
                    color='red';
                }else{
                    color='black';
                }
                if(!isNaN(_card_[1])||_card_[1]=='A'){
                    poker=_card_[0];
                }else{
                    _card_[1]=_card_[1].toString().toLowerCase();
                    poker=_card_[0]+'_'+_card_[1];
                }
                if(_card_[1]=='A'){
                    _card_[1]=_card_[1].toString().toLowerCase();
                }
                if(gameType!=1&&gameType!=2){
                    arr=[_card_[1],color,_card_[0],poker,_card_[2]];  
                }else{
                    arr=[_card_[1],color,_card_[0],poker]; 
                }
                newpoker.push(arr);
            }
        }
        console.log(newpoker);
    },
    settime:function(countdown){
        if(countdown>=0&&countdown<3){
            //即将开始游戏，请准备
            this.startFn(countdown);
            console.log(countdown);
        }else if(countdown>=3&&countdown<6){
            //动画，缩放，发牌
            this.zoomFn(countdown);
            console.log(countdown);
        }else if(countdown>=6&&countdown<44){
            //押注时间
            this.zoomFn(countdown);
            if(!$('.gametext').hasClass("none")){
                $('.gametext').addClass("none").removeClass("animt");
            }
            $c++;
            if($c==1){
                countdown2=countdown2-(countdown-6);
            }
            countdown2--;
            if(countdown2<=0){
                countdown2=0;
            }
            $(".countdown").text(countdown2);
            console.log(countdown);
        }else if(countdown>=44&&countdown<47){
            //准备揭晓结果
            this.readyFn(countdown);
            console.log(countdown);
        }else if(countdown>=47&&countdown<50){
            //翻牌
            $(".gametext").addClass("none");
           if(gameType==2){
                this.Bullfighting_flopFn(countdown,newpoker);
            }else if(gameType==1){
                this.GoldenFlower_flopFn(countdown,newpoker);
            }else{
                this.Catmouse_flopFn(countdown,newpoker);
            }
            console.log(countdown);
        }else if(countdown>=50&&countdown<53){
            //比牌大小，揭晓结果
            this.resultFn(biggest_cards);
            console.log(countdown);
        }else{
            //回到准备开始游戏
            this.endFn();
            console.log(countdown);
        }
    },
    endFn:function(){
        $('.gamebox .cover').addClass("none");
        $(".gamebox .examples").removeClass("zindex");
        $('.brandbox .brand').html(" ");
        $(".brandbox").removeClass("open");
        $(".brandbox .brand").addClass("none");
        $('.brandbox').addClass("none");
        $('.gamebox .examples .gameuserimg').removeClass("animt");
        $('.countdown').text("38").addClass("none");
        $('.gametext').removeClass("none").addClass("animt").text("即将开始游戏，请准备");
        $(".brandbox .result i").attr("class"," ");
        $(".gamepoint").addClass("none");
        $('.examples .gamepoint .point').text("0");
        $(".Catmouse .vs").removeClass("none");
        newpoker=[];
        err_true=true;
        $c=0;
    },
    startFn:function(){
        $('.gametext').text("即将开始游戏，请准备").removeClass("none").addClass('animt');
        $(".Catmouse .vs").removeClass("none")
        $(".game_popups").removeClass("animt");
    },
    zoomFn:function(countdown){
        $(".brandbox").removeClass("none");
        $(".brandbox .brand").removeClass("none");
        $(".brandbox").addClass("animt");
        $(".examples .gameuserimg").addClass("animt");
        $(".gameuserimg.animt .gamepoint").removeClass("none");
        $(".countdown").removeClass("none");
        $(".Catmouse .vs").addClass("none")
        $('.Catmouse .examples .gameuserimg').removeClass("bg");
        if(countdown==4){
            $('.gametext').addClass("none");
        }else{
            $('.gametext').removeClass("none").addClass("animt").text("开始支持");
        }
    },
    readyFn:function(countdown){
        $(".countdown").addClass("none");
        $(".game_popups").removeClass("animt");
        $(".history_record,.task_list").remove();
        if(countdown==44){
            $('.gametext').removeClass("none").addClass("animt").text("揭晓结果");
        }
        if(countdown==45){
            $('.gametext').addClass("none");
        }
    },
    GoldenFlower_flopFn:function(countdown,newpoker){
        if(countdown==47){
            $(".example1 .brandbox .result i").attr("class"," ");
            $(".example1 .brandbox").addClass("open");
            $('.example1 .brandbox.open .result').addClass("animt");
            $(".example1 .brand1").html(this.eg_brand(newpoker[0][0],newpoker[0][1],newpoker[0][2],newpoker[0][3]));
            $(".example1 .brand2").html(this.eg_brand(newpoker[1][0],newpoker[1][1],newpoker[1][2],newpoker[1][3]));
            $(".example1 .brand3").html(this.eg_brand(newpoker[2][0],newpoker[2][1],newpoker[2][2],newpoker[2][3]));
            this.GoldenFlower_comparison(newpoker.slice(0,3));
        
        }
        if(countdown==48){
            $(".example2 .brandbox .result i").attr("class"," ");
            $(".example2 .brandbox").addClass("open");
            $('.example2 .brandbox.open .result').addClass("animt");
            $(".example2 .brand1").html(this.eg_brand(newpoker[3][0],newpoker[3][1],newpoker[3][2],newpoker[3][3]));
            $(".example2 .brand2").html(this.eg_brand(newpoker[4][0],newpoker[4][1],newpoker[4][2],newpoker[4][3]));
            $(".example2 .brand3").html(this.eg_brand(newpoker[5][0],newpoker[5][1],newpoker[5][2],newpoker[5][3]));
            this.GoldenFlower_comparison(newpoker.slice(3,6));
        }
        if(countdown==49){
            $(".example3 .brandbox .result i").attr("class"," ");
            $(".example3 .brandbox").addClass("open");
            $('.example3 .brandbox.open .result').addClass("animt");
            $(".example3 .brand1").html(this.eg_brand(newpoker[6][0],newpoker[6][1],newpoker[6][2],newpoker[6][3]));
            $(".example3 .brand2").html(this.eg_brand(newpoker[7][0],newpoker[7][1],newpoker[7][2],newpoker[7][3]));
            $(".example3 .brand3").html(this.eg_brand(newpoker[8][0],newpoker[8][1],newpoker[8][2],newpoker[8][3]));
            this.GoldenFlower_comparison(newpoker.slice(6,9));
        }
    },
    Bullfighting_flopFn:function(countdown,newpoker){
        if(countdown==47){
            $(".example1 .brandbox .result i").attr("class"," ");
            $(".example1 .brandbox").addClass("open");
            $('.example1 .brandbox.open .result').addClass("animt");
            $(".example1 .brand1").html(this.eg_brand(newpoker[0][0],newpoker[0][1],newpoker[0][2],newpoker[0][3]));
            $(".example1 .brand2").html(this.eg_brand(newpoker[1][0],newpoker[1][1],newpoker[1][2],newpoker[1][3]));
            $(".example1 .brand3").html(this.eg_brand(newpoker[2][0],newpoker[2][1],newpoker[2][2],newpoker[2][3]));
            $(".example1 .brand4").html(this.eg_brand(newpoker[3][0],newpoker[3][1],newpoker[3][2],newpoker[3][3]));
            $(".example1 .brand5").html(this.eg_brand(newpoker[4][0],newpoker[4][1],newpoker[4][2],newpoker[4][3]));
            this.Bullfighting_comparison(newpoker.slice(0,5));
        
        }
        if(countdown==48){
            $(".example2 .brandbox .result i").attr("class"," ");
            $(".example2 .brandbox").addClass("open");
            $('.example2 .brandbox.open .result').addClass("animt");
            $(".example2 .brand1").html(this.eg_brand(newpoker[5][0],newpoker[5][1],newpoker[5][2],newpoker[5][3]));
            $(".example2 .brand2").html(this.eg_brand(newpoker[6][0],newpoker[6][1],newpoker[6][2],newpoker[6][3]));
            $(".example2 .brand3").html(this.eg_brand(newpoker[7][0],newpoker[7][1],newpoker[7][2],newpoker[7][3]));
            $(".example2 .brand4").html(this.eg_brand(newpoker[8][0],newpoker[8][1],newpoker[8][2],newpoker[8][3]));
            $(".example2 .brand5").html(this.eg_brand(newpoker[9][0],newpoker[9][1],newpoker[9][2],newpoker[9][3]));
            this.Bullfighting_comparison(newpoker.slice(5,10));
        }
        if(countdown==49){
            $(".example3 .brandbox .result i").attr("class"," ");
            $(".example3 .brandbox").addClass("open");
            $('.example3 .brandbox.open .result').addClass("animt");
            $(".example3 .brand1").html(this.eg_brand(newpoker[10][0],newpoker[10][1],newpoker[10][2],newpoker[10][3]));
            $(".example3 .brand2").html(this.eg_brand(newpoker[11][0],newpoker[11][1],newpoker[11][2],newpoker[11][3]));
            $(".example3 .brand3").html(this.eg_brand(newpoker[12][0],newpoker[12][1],newpoker[12][2],newpoker[12][3]));
            $(".example3 .brand4").html(this.eg_brand(newpoker[13][0],newpoker[13][1],newpoker[13][2],newpoker[13][3]));
            $(".example3 .brand5").html(this.eg_brand(newpoker[14][0],newpoker[14][1],newpoker[14][2],newpoker[14][3]));
            this.Bullfighting_comparison(newpoker.slice(10,15));
        }
    },
    Catmouse_flopFn:function(countdown,newpoker){
        console.log(countdown,newpoker);
        catmousearr=newpoker.slice(2,7);
        if(countdown==47){
            $(".example2 .brandbox .result i").attr("class"," ");
            $(".example2 .brandbox").addClass("open");
            $('.example2 .brandbox.open .result').addClass("animt");
            $(".example2 .brand1").html(this.eg_brand(newpoker[2][0],newpoker[2][1],newpoker[2][2],newpoker[2][3]));
            $(".example2 .brand2").html(this.eg_brand(newpoker[3][0],newpoker[3][1],newpoker[3][2],newpoker[3][3]));
            $(".example2 .brand3").html(this.eg_brand(newpoker[4][0],newpoker[4][1],newpoker[4][2],newpoker[4][3]));
            $(".example2 .brand4").html(this.eg_brand(newpoker[5][0],newpoker[5][1],newpoker[5][2],newpoker[5][3]));
            $(".example2 .brand5").html(this.eg_brand(newpoker[6][0],newpoker[6][1],newpoker[6][2],newpoker[6][3]));
            // this.Catmouse_comparison(newpoker.slice(2,7),catmousearr);  
        }
        if(countdown==48){
            $(".example1 .brandbox .result i").attr("class"," ");
            $(".example1 .brandbox").addClass("open");
            $('.example1 .brandbox.open .result').addClass("animt");
            $(".example1 .brand1").html(this.eg_brand(newpoker[0][0],newpoker[0][1],newpoker[0][2],newpoker[0][3]));
            $(".example1 .brand2").html(this.eg_brand(newpoker[1][0],newpoker[1][1],newpoker[1][2],newpoker[1][3]));
            this.Catmouse_comparison(newpoker.slice(0,2),catmousearr);
        }
        if(countdown==49){
            $(".example3 .brandbox .result i").attr("class"," ");
            $(".example3 .brandbox").addClass("open");
            $('.example3 .brandbox.open .result').addClass("animt");
            $(".example3 .brand1").html(this.eg_brand(newpoker[7][0],newpoker[7][1],newpoker[7][2],newpoker[7][3]));
            $(".example3 .brand2").html(this.eg_brand(newpoker[8][0],newpoker[8][1],newpoker[8][2],newpoker[8][3]));
            this.Catmouse_comparison(newpoker.slice(7,9),catmousearr);
        }
    },
    resultFn:function(biggest_cards){
        $('.gamebox .cover').removeClass("none");
        $(".gametext").addClass("none");
        if(biggest_cards==1){
            $('.gamebox .example1').addClass("zindex");
        }else if(biggest_cards==2){
            $('.gamebox .example2').addClass("zindex");
        }else{
            $('.gamebox .example3').addClass("zindex");
        }
    },
    eg_brand:function(a,b,c,d){
        var html='<img class="img1" src="/style/meilibo/dist/gameimg/poker/poker_'+a+'_'+b+'@2x.png">'
                +'<img class="img2" src="/style/meilibo/dist/gameimg/poker/poker_'+c+'@2x.png">'
                +'<img class="img3" src="/style/meilibo/dist/gameimg/poker/poker_'+d+'@2x.png">';
            return html;   
    },
    GoldenFlower_comparison:function(newarr){
            for(var i=0;i<newarr.length;i++){
                if(newarr[i][0]=='j'){
                    newarr[i][0]='11';
                }else if(newarr[i][0]=='q'){
                    newarr[i][0]='12';
                }else if(newarr[i][0]=='k'){
                    newarr[i][0]='13';
                }else if(newarr[i][0]=='a'){
                    newarr[i][0]='14';
                }
                numberarry.push(newarr[i][0]);
                colorarry.push(newarr[i][2]);
            };
            numberarry.sort(sortNumber);
            if(colorarry[0]===colorarry[1]&&colorarry[1]===colorarry[2]){
                sameColor=true;
            }
            if(numberarry[0]===numberarry[1]&&numberarry[1]===numberarry[2]){
                console.log("豹子");
                this._comfn(addClass_i5);
            }else if((numberarry[2]-numberarry[1])==1&&(numberarry[1]-numberarry[0]==1)||(numberarry[2]=='3'&&numberarry[1]=='2'&&numberarry[0]=='14')){
                console.log("顺子");
                this._comfn(addClass_i2);
            }else if(numberarry[0]==numberarry[1]||numberarry[1]==numberarry[2]){
                console.log("对子");
                this._comfn(addClass_i1);
            }else if((numberarry[2]-numberarry[1])==1&&(numberarry[1]-numberarry[0]==1)||(numberarry[2]=='3'&&numberarry[1]=='2'&&numberarry[0]=='14')){
                if(sameColor){
                    console.log("同花顺");
                    this._comfn(addClass_i4);
                    sameColor=false;
                } 
            }else if(sameColor){
                console.log("同花");
                this._comfn(addClass_i3);
                sameColor=false;
            }else{
                console.log("单牌");
                this._comfn(addClass_i0);
            }
            numberarry=[];
            colorarry=[];
    },
    Bullfighting_comparison:function(newarr){
        console.log(newarr);
        for(var i=0;i<newarr.length;i++){
            numberarry.push(newarr[i][0]);
        };
        if(this.uniquefn(numberarry).length==2){
            console.log("炸弹");
            this._comfn(addClass_i12);
        }
        console.log(this.uniquefn(numberarry))
        for(var j=0;j<numberarry.length;j++){
                if(numberarry[j]=='j'){
                    numberarry[j]='11';
                }else if(numberarry[j]=='q'){
                    numberarry[j]='12';
                }else if(numberarry[j]=='k'){
                    numberarry[j]='13';
                }else if(numberarry[j]=='a'){
                    numberarry[j]='1';
                }  
        }
        if(numberarry[0]>10&&numberarry[1]>10&&numberarry[2]>10&&numberarry[3]>10&&numberarry[4]>10){
            console.log("五花妞");
            this._comfn(addClass_i11);
        }
        if((numberarry[0]+numberarry[1]+numberarry[2]+numberarry[3]+numberarry[4])<10){
            console.log("五小妞");
            this._comfn(addClass_i13);
        }
        for(var i=0;i<numberarry.length;i++){
            if(numberarry[i]>10){
                numberarry[i]='10';
            }
        }
        numberarry.sort(sortNumber);
        this.switchFn();

        numberarry=[];
        result=[];
    },
    Catmouse_comparison:function(newarr,catmousearr){
        var onearr=new Array(),threearr=new Array();
        var arr=new Array(),flower_arr=new Array();
        var reparr=new Array();
        var arrlen='';
        for(var i=0;i<catmousearr.length;i++){
            newarr.push(catmousearr[i]);
        }
        for(var i=0;i<newarr.length;i++){
            onearr.push(newarr[i][4]);
            threearr.push(newarr[i][2])
        }
        flower_arr=[
            [threearr[0],threearr[1],threearr[2],threearr[3],threearr[4]],
            [threearr[0],threearr[1],threearr[2],threearr[3],threearr[5]],
            [threearr[0],threearr[1],threearr[2],threearr[3],threearr[6]],
            [threearr[0],threearr[1],threearr[2],threearr[4],threearr[5]],
            [threearr[0],threearr[1],threearr[2],threearr[4],threearr[6]],
            [threearr[0],threearr[1],threearr[2],threearr[5],threearr[6]],
            [threearr[0],threearr[1],threearr[3],threearr[4],threearr[5]],
            [threearr[0],threearr[1],threearr[3],threearr[4],threearr[6]],
            [threearr[0],threearr[1],threearr[3],threearr[5],threearr[6]],
            [threearr[0],threearr[1],threearr[4],threearr[5],threearr[6]],
            [threearr[0],threearr[2],threearr[3],threearr[4],threearr[5]],
            [threearr[0],threearr[2],threearr[3],threearr[4],threearr[6]],
            [threearr[0],threearr[2],threearr[3],threearr[5],threearr[6]],
            [threearr[0],threearr[2],threearr[4],threearr[5],threearr[6]],
            [threearr[0],threearr[3],threearr[4],threearr[5],threearr[6]],
            [threearr[1],threearr[2],threearr[3],threearr[4],threearr[5]],
            [threearr[1],threearr[2],threearr[3],threearr[4],threearr[6]],
            [threearr[1],threearr[2],threearr[3],threearr[5],threearr[6]],
            [threearr[1],threearr[2],threearr[4],threearr[5],threearr[6]],
            [threearr[1],threearr[3],threearr[4],threearr[5],threearr[6]],
            [threearr[2],threearr[3],threearr[4],threearr[5],threearr[6]]
        ]
        for(var i=0;i<flower_arr.length;i++){
            if(flower_arr[i][0]===flower_arr[i][1]&&flower_arr[i][1]===flower_arr[i][2]&&flower_arr[i][2]===flower_arr[i][3]&&flower_arr[i][3]===flower_arr[i][4]){
                samecolor=true;
                console.log("同花");
                this._comfn(addClass_i5);
            }
        }
        onearr.sort(sortNumber);
        console.log(onearr);
        console.log(threearr);
        arr=[
                [onearr[0],onearr[1],onearr[2],onearr[3],onearr[4]],
                [onearr[0],onearr[1],onearr[2],onearr[3],onearr[5]],
                [onearr[0],onearr[1],onearr[2],onearr[3],onearr[6]],
                [onearr[0],onearr[1],onearr[2],onearr[4],onearr[5]],
                [onearr[0],onearr[1],onearr[2],onearr[4],onearr[6]],
                [onearr[0],onearr[1],onearr[2],onearr[5],onearr[6]],
                [onearr[0],onearr[1],onearr[3],onearr[4],onearr[5]],
                [onearr[0],onearr[1],onearr[3],onearr[4],onearr[6]],
                [onearr[0],onearr[1],onearr[3],onearr[5],onearr[6]],
                [onearr[0],onearr[1],onearr[4],onearr[5],onearr[6]],
                [onearr[0],onearr[2],onearr[3],onearr[4],onearr[5]],
                [onearr[0],onearr[2],onearr[3],onearr[4],onearr[6]],
                [onearr[0],onearr[2],onearr[3],onearr[5],onearr[6]],
                [onearr[0],onearr[2],onearr[4],onearr[5],onearr[6]],
                [onearr[0],onearr[3],onearr[4],onearr[5],onearr[6]],
                [onearr[1],onearr[2],onearr[3],onearr[4],onearr[5]],
                [onearr[1],onearr[2],onearr[3],onearr[4],onearr[6]],
                [onearr[1],onearr[2],onearr[3],onearr[5],onearr[6]],
                [onearr[1],onearr[2],onearr[4],onearr[5],onearr[6]],
                [onearr[1],onearr[3],onearr[4],onearr[5],onearr[6]],
                [onearr[2],onearr[3],onearr[4],onearr[5],onearr[6]]
            ];
      for(var k=0;k<arr.length;k++){
          this.dezhouFn(arr[k]);
      }
        
    },
    switchFn:function(){
        var new_numberarry=this.removeByValueFn(niuniu,numberarry);
        console.log(new_numberarry);
        if(new_numberarry.length<=2){
            var niu_number=(parseInt(new_numberarry[0])+parseInt(new_numberarry[1]))%10;
            switch(niu_number){
                case 1:
                    console.log("妞1");
                    this._comfn(addClass_i1);
                break;
                case 2:
                    console.log("妞2");
                    this._comfn(addClass_i2);
                break;
                case 3:
                    console.log("妞3");
                    this._comfn(addClass_i3);
                break;
                case 4:
                    console.log("妞4");
                    this._comfn(addClass_i4);
                break;
                case 5:
                    console.log("妞5");
                    this._comfn(addClass_i5);
                break;
                case 6:
                    console.log("妞6");
                    this._comfn(addClass_i6);
                break;
                case 7:
                    console.log("妞7");
                    this._comfn(addClass_i7);
                break;
                case 8:
                    console.log("妞8");
                    this._comfn(addClass_i8);
                break;
                case 9:
                    console.log("妞9");
                    this._comfn(addClass_i9);
                break;
                case 0:
                    console.log("妞妞");
                    this._comfn(addClass_i10);
                break;
            }
        }else{
            console.log("没妞");
            this._comfn(addClass_i0);
        }
    },
    dezhouFn:function(arr){
        console.log(arr);
        if((arr[4]-arr[3])==1&&(arr[3]-arr[2])==1&&(arr[2]-arr[1])==1&&(arr[1]-arr[0])==1){
            one_flg=true;
            console.log("顺子")
            this._comfn(addClass_i4);
        }
        if(one_flg&&samecolor){
            console.log("同花顺");
            this._comfn(addClass_i8);
        }
        if(arr[4]=='14' && one_flg && samecolor){
            console.log("皇家同花顺");
            this._comfn(addClass_i9);
        }
        arrlen=this.isRepeat(arr);
        console.log(arrlen);
        if(this.countProperties(arrlen)==2){
            for(var i in arrlen){
                var $arr=new Array();
                $arr.push(arrlen[i]);
                if($arr.indexOf(4)!= -1){
                    console.log("四条");
                    this._comfn(addClass_i7);
                }else{
                    console.log("葫芦");
                    this._comfn(addClass_i6);
                }
            }    
        }else if(this.countProperties(arrlen)==3){
            for(var i in arrlen){
                var $arr=new Array();
                $arr.push(arrlen[i]);
                if($arr.indexOf(3)!= -1){
                    console.log("三条");
                    this._comfn(addClass_i3);
                }else{
                    console.log("两对")
                    this._comfn(addClass_i2);
                }
            }      
        }else if(this.countProperties(arrlen)==4){
            console.log("一对");
            this._comfn(addClass_i1);
        }else{
            if(!one_flg||!samecolor){
                console.log("高牌");
                this._comfn(addClass_i0);
            }
        }

        
    },
    uniquefn:function(arr){
        for(var i=0;i<arr.length;i++){
            if(result.indexOf(arr[i])==-1){
                result.push(arr[i])
            }
        }
        return result;
    },
    isRepeat:function(array){//判断数组中是否有相同元素，然后转换成对象
        var json = new Object();
        for( var i in array ){
            json.hasOwnProperty(array[i]) ? json[array[i]]++ : json[array[i]] = 1;
        }
        return json;
    },
    countProperties:function(obj){//判断对象长度
        var count = 0;
        for (var property in obj) {
            if (Object.prototype.hasOwnProperty.call(obj, property)) {
                count++;
            }
        }
        return count;
    },
    removeByValueFn:function(a, b){//删除数组中相同元素，然后返回剩下的元素
        var array=[];
        for ( var key in a )
            {
                var arr = new Array();
                $.extend(arr, b);
                var confirm = 0;
                for( var index in a[key] ){
                    if( $.inArray( a[key][index], arr ) >= 0 ){
                        var i =  arr.indexOf(a[key][index]);
                        i >= 0 ? arr.splice(i, 1) : false;
                        confirm++;
                    }
                }
                array=arr;
                if( confirm >= 3 ){
                    return array;
                }
            }
            return array;
    },
    _comfn:function(fn){//显示结果class
        if(gameType!=1&&gameType!=2){
            if(countdown==48){
                fn(".example1 .result.animt i");
            }else if(countdown==47){
                fn(".example2 .result.animt i");
            }else if(countdown==49){
                fn(".example3 .result.animt i");
            };
        }else{
            if(countdown==47){
                fn(".example1 .result.animt i");
            }else if(countdown==48){
                fn(".example2 .result.animt i");
            }else if(countdown==49){
                fn(".example3 .result.animt i");
            };
        }
        
    }
}
function addClass_i13(example){
    $(example).addClass("i13");
}
function addClass_i12(example){
    $(example).addClass("i12");
}
function addClass_i11(example){
    $(example).addClass("i11");
}
function addClass_i10(example){
    $(example).addClass("i10");
}
function addClass_i9(example){
    $(example).addClass("i9");
}
function addClass_i8(example){
    $(example).addClass("i8");
}
function addClass_i7(example){
    $(example).addClass("i7");
}
function addClass_i6(example){
    $(example).addClass("i6");
}
function addClass_i5(example){
    $(example).addClass("i5");
}
function addClass_i4(example){
    $(example).addClass("i4");
}
function addClass_i3(example){
    $(example).addClass("i3");
}
function addClass_i2(example){
    $(example).addClass("i2");
}
function addClass_i1(example){
    $(example).addClass("i1");
}
function addClass_i0(example){
    $(example).addClass("i0");
}
function sortNumber(a, b){return a - b}

