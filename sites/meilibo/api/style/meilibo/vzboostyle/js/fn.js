var queue = new Queue(4);
var Fn={
	scrollChatFlag:1,
    scrojh:null,
    giftimg:null,
    giftvalue:0,//单个礼物
    giftName:null,
    gifti:0,
    giftj:0,
    gift_number:1,//礼物个数
    giftid:null,
    giftval:0,//总共礼物
	oneselfmoney:null,
	plid:null,
	ptname:null,
	passid:null,
	sum:true,
	scrollChat:function(){
            if(this.scrollChatFlag==1){
                this.scrollChatFlag=0;
                $(".scrollBtn").removeClass("onScroll");    
            }else{
                this.scrollChatFlag=1;
                $(".scrollBtn").addClass("onScroll");
            }
        },
	chatfn:function(){
        if(!User.isLogin){
            this.nulogin();
        }else{
            if($("#message").val() != ""){
                this.flymsgfn();
            }
        }
	},
    nulogin:function(){
        $(".layer").removeClass("hide");
        var data={};
        var html = template('loginremind', data);
        document.getElementById('layer-box').innerHTML = html;
    },
    flymsgfn:function(){
        if(fly=="FlyMsg"){
            $.ajax({
                type: 'POST',
                url:'/OpenAPI/V1/Gift/sendBarrage' ,
                data:{"token":User.token,"roomid":room_id,"content":$("#message").val()},
                dataType:'json',
                success: function(data){
                    console.log(data);
                    if(data.code==1){
                        $(".layer").removeClass("hide");
                        var data = {};
                        var html = template('payToolTip', data);
                        document.getElementById('layer-box').innerHTML = html;
                        console.log("余额不足 ");
                        return false;
                    }
                    $(".account-momoid span").text(data.data.coinbalance);
                    User.coinbalance = data.data.coinbalance;
                    this.oneselfmoney=data.data.coinbalance;
                },
                eorro:function(){
                    console.log("qingqiushibai");
                }
            });
        }else{
            SocketIO._chatMessage($("#message").val());
        }
        $("#message").val("").focus();
    },
    init_screen:function(){
            var _top =0;
            $(".chat_barrage_box > div").show().each(function () {
                var _left = $(".playerContent").width() - $(this).width()+200;
                var _height = $(".playerContent").height();
                _top = _top + 30;
                if (_top >= _height - 15) { 
                    _top = 30;
                }
                $(this).css({left: _left, top: _top});
                var time = 12000;
                // if ($(this).index() % 2 == 0) {
                //     time = 12000;
                // }
                $(this).animate({left: "-" + _left + "px"}, time, function () {
                    $(this).remove();
                });
            });
    },
	scrollTop:function(){
		if(this.scrollChatFlag==1){
			$(".live-new-msg-tips").addClass("hide");
			this.scrollFn();   
                }else{
                    $(".live-msg-list").scrollTop($(".chat-msg-box").scrollTop());  
                    this.scrojh=$(".chat-msg-box")[0].scrollHeight;
                    if(this.scrojh>$(".chat-msg-box").scrollTop() && this.scrojh > $(".chat-msg-box").height()) {
                        $(".live-new-msg-tips").removeClass("hide");
                    }   
                }

            if($(".live-msg-list .msg").length > 150){
                    $(".live-msg-list .msg").first().remove();
            }
	},
	msgTips:function(){
		this.scrollFn();
		$(".live-new-msg-tips").addClass("hide");
	},
	scrollFn:function(){
		this.scrojh=$(".chat-msg-box")[0].scrollHeight;
		$(".live-msg-list").scrollTop($(".chat-msg-box").scrollTop(this.scrojh));
	},
    loginremind:function(){
        if(User.isLogin == false){
            console.log('没有登录');
            $(".layer").removeClass("hide");
            var data={};
            var html = template('loginremind', data);
            document.getElementById('layer-box').innerHTML = html;
        }
    },
    loginroom:function(roomid){
        // var roomid = roomid;
        // console.log(roomid);
        // window.location.href = "http://vzboo.com/"+merchant.hash+"/"+roomid+"?mode=3";
    },
    getMyAnchor:function(){
        $.ajax({    
            type:"POST",
            url:'/OpenAPI/V1/User/followees',
            dataType:'json',
            cache:false,
            data:{
                uid:User.id,
                token:User.token,
            },
            success:function(data){
            var anchorlist = {list:data.data.list}
            console.log(data);
            var html = template('getMyAnchorlist', anchorlist);
            document.getElementById('liveRoomLists').innerHTML = html
            }
        })
    },
	giftItem:function(me){//点击礼物
        this.giftid=me.attr("data-giftid");
        $(".layer").removeClass("hide");
        this.giftimg=me.attr("data-giftimg");
        this.giftvalue=me.attr("data-giftvalue");
        this.giftName=me.attr("data-giftname");
        var data = {
            gifid:this.giftid,
            giftvalue:this.giftvalue
        };
        var html = template('giftToolTip', data);
        document.getElementById('layer-box').innerHTML = html;
    },
    changeMoney:function(val){
        this.gift_number=val;
        $(".toolTip .toolTipTitle .consume").val(this.gift_number*this.giftvalue);
    },
	sendgift:function(count){//发送礼物
        this.giftval=this.giftvalue*this.gift_number;
        this.oneselfmoney=$(".account-momoid span").text();
        console.log(this.giftval);
        if(this.oneselfmoney<this.giftval){
            $(".layer").removeClass("hide");
            var data = {};
            var html = template('payToolTip', data);
            document.getElementById('layer-box').innerHTML = html;
            console.log("余额不足");
            return false;
        }else{
            $(".layer").addClass("hide");
        }
        this.sendgiftshow(count);
        this.scrollTop();
        this.gift_number=1;
    },
	sendgiftshow:function(count){
            $.ajax({    
                type:"POST",
                url:'/OpenAPI/V1/Gift/send',
                dataType:'json',
                cache:false,
                data:{
                    gift_id:this.giftid,
                    count:count,
                    to_uid:to_uid,
                    token:User.token,
                },
                success:function(data){
                    console.log("coin",data);
                    $(".account-momoid span").text(data.data.coinbalance);
                    User.coinbalance = data.data.coinbalance;
                    this.oneselfmoney=data.data.coinbalance
                }
            })
	},
    bigshowgift:function(giftid){//大礼物
           var box=$(".giftBigshow");
           var len=$(".giftBigshow > div").length;
           if(len<1){
           if(giftid==84){
              var gifthtml='<div class="big_gift_animte_feiji">'
                            +'<div><img src="/style/meilibo/vzboostyle/img/bigift/bomber.png" width="150" style="margin-bottom:-85px;"></div>'
                            +'<div class="giftan">'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: 38px;left: 6px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: 23px;left: 18px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: 12px;left: 47px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: -12px;left: 66px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 40px;height: 50px;position:relative;top: -58px;left: 8px;background-size: 135px;"></span></div>'
                            +'<div><img src="/style/meilibo/vzboostyle/img/bigift/bomber_shadow.png"/ style="width:150px;"></div>'
                            +'</div>';
                box.append(gifthtml);
                var positions=[['1 0','-23 0','-46 0'],['0 0','-48 0','-93 1']];
                var ele=document.getElementsByClassName("giftspan");
                var timer=null;
                animation(ele,positions);
                function animation(ele,positions){
                    var index=0;
                    function run(){
                        var position1=positions[0][index].split(' ');
                        var position2=positions[1][index].split(' ');
                        ele[0].style.backgroundPosition=position1[0]+'px '+position1[1]+'px';
                        ele[1].style.backgroundPosition=position1[0]+'px '+position1[1]+'px';
                        ele[2].style.backgroundPosition=position1[0]+'px '+position1[1]+'px';
                        ele[3].style.backgroundPosition=position1[0]+'px '+position1[1]+'px';
                        ele[4].style.backgroundPosition=position2[0]+'px '+position2[1]+'px';
                        index++;
                        if(index>=positions.length){
                            index=0;
                        }
                        timer=setTimeout(run,60);
                    }
                    run();
                }
                var feiji=$(".big_gift_animte_feiji");
                feiji.animate({"left":"50%"},2000,function(){
                    Fn.gifthert();
                    setTimeout(function(){
                        feiji.animate({"left":"-40%"},2600);
                    },2000)
                })
                setTimeout(function(){
                        clearTimeout(timer);
                        feiji.remove();
                },6500);
            }else if(giftid==79){
                var str='<div class="big_gift_animte_fireworks">'
                        +'<div id="fireworks" class="giftan"></div>'
                        +'</div>';
                box.append(str);
                var timer=null;
                var imgurl="/style/meilibo/vzboostyle/img/bigift/fireworks.png";
                var positions=['0 -880','-200 -880','-400 -880','-600 -880','-800 -880','-1000 -880','-1200 -880','-1400 -880','-1600 -880','-1800 -880','0 0','-204 0','-408 0','-612 0','0 -300','-212 -300','-424 -300','-635 -300','-847 -300','-1059 -300','-1270 -300','-1482 -300','-1 -499','-212 -499','-424 -499','-635 -499','-847 -499','-1059 -499','-1269 -498','-1479 -497','0 -696','-212 -696','-424 -694','-635 -696'];   
                var ele=document.getElementById("fireworks");
                var timer=null;
                animtion(ele,positions,imgurl);
                function animtion(ele,positions,imgurl){
                    ele.style.backgroundImage="url("+imgurl+")";
                    ele.style.backgroundRepeat="no-repeat";
                    ele.style.backgroundPosition="0 -880px";
                    var index=0;
                    function run(){
                        var position=positions[index].split(' ');
                        ele.style.backgroundPosition=position[0]+'px '+position[1]+'px';
                        index++;
                        if(index>14){
                            ele.style.height="200px";
                        }else{
                            ele.style.height="300px";
                        }
                        timer=setTimeout(run,200);
                        if(index>positions.length-1){
                            clearInterval(timer);
                        }
                        
                    }
                    run();
                }      
                var fireworks=$(".big_gift_animte_fireworks");
                setTimeout(function(){
                    fireworks.animate({"opacity":"0"},600,function(){
                        fireworks.remove();
                    });
                },6800)
            }else if(giftid==73){
                var str='<div class="big_gift_animte_yacht">'
                          +'<div class="yacht_shui animt">'
                            +'<div class="giftan" style="text-align: center;">'
                                +'<img src="/style/meilibo/vzboostyle/img/bigift/yacht_hull.png" width="120" style="display:block;">'
                                +'<img src="/style/meilibo/vzboostyle/img/bigift/yacht_shadow.png" width="120" style="display:block;margin-top:20px;">'
                            +'</div>'
                          +'</div>'
                        +'</div>';
                box.append(str);
                var giftan=$(".big_gift_animte_yacht .yacht_shui .giftan");
                var yacht=$(".big_gift_animte_yacht");
                giftan.animate({"marginLeft":"20px"},2000,function(){
                    setTimeout(function(){
                        giftan.animate({"marginLeft":"110%"},4000,function(){
                            yacht.stop().animate({"opacity":"0"},600,function(){
                                yacht.remove();
                            });
                        });
                    },1000);
                })
            }else if(giftid==81){
                var str='<div class="big_car_box">'
                            +'<div class="car_img1">'
                                +'<span name="car_lun1" class="car_lun1 llun1"></span>'
                                +'<span name="car_lun2" class="car_lun2 rlun1"></span>'
                            +'</div>'
                            +'<div class="car_img2 hide">'
                                +'<span name="car_lun1" class="car_lun1 llun1"></span>'
                                +'<span name="car_lun2" class="car_lun2 rlun1"></span>'
                                +'<span name="car_weideng" class="car_weideng"></span>'
                            +'</div>'
                        +'</div>';
                box.append(str);
                var i=1,timer=null;
                timer=setInterval(function(){
                    if(i<5){
                        $(".car_img1 span[name='car_lun1']").attr("class","car_lun1 llun"+i);
                        $(".car_img1 span[name='car_lun2']").attr("class","car_lun2 rlun"+i);
                        $(".car_img2 span[name='car_lun1']").attr("class","car_lun1 llun"+i);
                        $(".car_img2 span[name='car_lun2']").attr("class","car_lun2 rlun"+i);
                        i++;
                    }else{
                        i=1;
                        $(".car_img1 span[name='car_lun1']").attr("class","car_lun1 llun1");
                        $(".car_img1 span[name='car_lun2']").attr("class","car_lun2 rlun1");
                        $(".car_img2 span[name='car_lun1']").attr("class","car_lun1 llun1");
                        $(".car_img2 span[name='car_lun2']").attr("class","car_lun2 rlun1");
                    }
                    
                },20);
                var big_car_box=$(".big_car_box");
                big_car_box.animate({"right":"20px","top":"60px"},2000,function(){
                    setTimeout(function(){
                        big_car_box.animate({"right":"150%","top":"120px"},2000,function(){
                            $(".big_car_box .car_img1").addClass("hide");
                            $(".big_car_box .car_img2").removeClass("hide");
                            big_car_box.css({"right":"-100%","top":"160px"});
                            big_car_box.animate({"right":"20px","top":"60px"},2000,function(){
                                $(".big_car_box .car_img2 .car_weideng").show();
                                setTimeout(function(){
                                    big_car_box.animate({"right":"150%","top":"0px"},2000,function(){
                                        big_car_box.remove();
                                        clearInterval(timer);
                                    });
                                },1000)
                            })
                        });
                    },1000)
                });
            }else if(giftid==85){
                var str='<div name="big_tx_box" class="big_tx_box bg1"></div>';
                box.append(str);
                var i=1,timer=null;
                timer=setInterval(function(){
                    if(i<40){
                        $('.giftBigshow >div[name="big_tx_box"]').attr("class","big_tx_box bg"+i);
                        i++;
                    }else{
                        clearInterval(timer);
                        $('.big_tx_box').remove();
                    }
                },200)
                
            }
          }
    },
    gifthert:function(){//星星
        var images = [],id = 0,confirm = true,num = 20,max = 2,loopNum = 0,speed = 30,timer = null;
        var timer = setInterval(loop , speed);
        function loop() {
            if( images.length <= num && confirm ){
                var image = new Image(100, 60, ++id);
                images.push(image);
            }
            if(images.length > num && confirm){
                confirm = false;
            }

            if(!confirm && images.length == 0){
                clearInterval(timer);
            }

            for (var i = 0; i < images.length; i++) {
                var image = images[i];
                if( image.opacity < 0 ){
                    $("#"+image.id).remove();
                    // console.log(images[i].id);
                    images.shift();
                }
                image.frameskip();
                image.update();
            };
            
        }
        function Image (xPos, yPos, i) { 
            this.xPos = xPos;//中心X坐标
            this.yPos = yPos; //中心Y坐标
            this.id = i;
            this.height = Math.random();//Math.random()：得到一个0到1之间的随机数
            this.height = Math.ceil(this.height * 10 +20); //四舍五入
            this.imgAddress = new Array(
                "heart9@2x.png",
                "heart@2x.png",
                "heart0@2x.png",
                "heart1.png",
                "heart2.png",
                "heart3.png",
                "heart1@2x.png",
                "heart2@2x.png",
                "heart3@2x.png",
                "heart4@2x.png",
                "heart5@2x.png",
                "heart6@2x.png",
                "heart7@2x.png",
                "heart8@2x.png",
                "heart10@2x.png",
                "heart11@2x.png"
                );
            this.img = Math.random();//Math.random()：得到一个0到1之间的随机数
            this.img = Math.ceil(this.img * this.imgAddress.length-1); //四舍五入
            $("<img />",{
                src:'/style/meilibo/vzboostyle/img/bigift/'+this.imgAddress[this.img], 
                id:i,
                style:'position:absolute;height:'+this.height+'px; top: '+this.yPos+'px, left: '+this.xPos+'px; opacity:1;'
                }).appendTo($(".big_gift_animte_feiji"));
            
            if(Math.round(Math.random()) == 0){
                this.yVel = Math.random()*4;
            }else{
                this.yVel = -Math.random()*4;
            }
            this.xVel = 0;
            this.gravity = 0.5;//重力影响
            this.opacity = 1;   //透明度
            this.opacityChange = 0.1; //透明度变化量

            this.frameskip = function(){ //实现更改
                $("#"+this.id).css({
                    top:this.xPos,
                    left:this.yPos,
                    opacity:this.opacity
                });

            }
            this.update = function(){ //更新自己的方法
                if(Math.round(Math.random()) == 0){
                    this.yVel += this.gravity;
                }else{
                    this.yVel -= this.gravity;
                }
                if(this.xPos > 200){
                    this.opacity -= this.opacityChange;
                }
                this.xVel += this.gravity;              
                this.yPos += this.yVel;
                this.xPos += this.xVel;
            }
        }
    },
    sendShowqueue:function(red_val_temp,nickname,userphoto,giftimg,giftname){
        queue.push(function(){
            var defer = $.Deferred();
            var giftlen=$(".gift_show").length;
            var numGift=1;
            if(giftlen<4){
                Fn.gifti++;
                Fn.giftj++;
                var temp = "gift"+Fn.gifti;
                var temp2="giftshow"+Fn.giftj;
                var gift_show='<div class="gift_show '+temp+'">'
                        +'<img class="gift_user" src="'+userphoto+'"/>'
                        +'<span class="msg_info">'+nickname+'<a>送一个'+giftname+'</a></span>'
                        +'<img  class="gift_img" src="'+giftimg+'">'
                        +'<span class="gift_num"><i class="gift_x '+temp2+'">X 1</i></span>'
                        +'</div>';
                var Inttime=setInterval(function(){
                    numGift++;
                    numGift <=red_val_temp?$("."+temp2).html("X "+numGift):clearInterval(Inttime);
                },500);
                $(".giftShowqueue").append(gift_show);
                $(".gift_show."+temp+" i").css({"animation":"animate 0.5s linear 0s "+red_val_temp,"-webkit-animation":"animate 0.5s linear 0s "+red_val_temp,"-moz-animation":"animate 0.5s linear 0s "+red_val_temp});
            }
            setTimeout(function(){
                $("."+temp).addClass("animtright");
                setTimeout(function(){
                    $("."+temp).remove();
                },1000);
                defer.resolve();
            }, 500*red_val_temp+700);

            return defer.promise();
        })
    },
	oneselfn:function(){
            $(".layer").removeClass("hide");
            $(".layer").removeClass("none");
            $.ajax({
                type:"POST",
                url:'/OpenAPI/V1/User/shareProfile',
                dataType:'json',
                cache:false,
                data:{
                    uid:User.id,
                    token:User.token
                },
                success:function(data){
                    console.log(data);
                var html = template('profile-box', data);
                document.getElementById('layer-box').innerHTML = html;
                }
            })
    },
    hotanchorlist:function(){
        $.ajax({
            url: '/ajax/anchorhotlist',
            type: 'post',
            data: {merchant_id: Anchor.merchant_id},
            dataType: 'json',
            cache: false,
            success: function (data) {
                console.log(data);
                var html = template('hotanchor', data);
                document.getElementById('liveShow_info').innerHTML = html;
            }
        });
    },
    getAdmin: function() {
        var data={};
        var html = template('loading',data);
        document.getElementById('managerlist').innerHTML = html;
        return $.ajax({
                type: 'GET',
                url: '/OpenAPI/V1/Room/getadmin',
                dataType: 'json',
                data: {
                    token: User.token,
                    uid: Anchor.id
                },
                success:function(rep){
                    console.log(rep);
                    if(rep.data){
                        var html="";
                        for(var i=0;i<rep.data.length;i++){
                            html+='<li class="userItem layfolkItem clearfix" data-id="'+rep.data[i].id+'">'
                                    +'<img class="userIcon" onerror="this.src=\'/style/avatar/0/0_big.jpg\'" src="'+rep.data[i].avatar+'">'
                                    +'<div class="userMsg">'
                                        +'<p class="userName">'+rep.data[i].nickname+'</p>'
                                        +'<img class="charmlevel level" src="http://demo.meilibo.net/style/meilibo/vzboostyle/img/fortuneLevel/c_lv_'+rep.data[i].emceelevel+'@2x.png" data-charm="0">'
                                    +'</div>'
                                +'</li>';
                        }
                        $(".managerlist").html(html);
                    }else{
                        $(".managerlist").html("<p style='padding-top:20px;font-size:12px;text-align:center;color:#999;'>该房间"+rep.msg+"</p>");
                    }
                    
                }
            });
    },
    start_play:function(){
        var regurl = '/OpenAPI/V1/Room/createRoomForWeb';
        $.ajax({
            url:regurl,
            type: 'post',
            dataType: 'JSON', 
            data:{token:User.token,roomid:User.curroomnum,title:$('.ctitle').val(),
                  topic:$('.ctopic').val(),password:$('.cpassword').val(),type:$('.ctype').val(),
          },
            success: function(data) {
              console.log(data);
              if(data.code != 0){
                alert(data.msg);
              }else{
                $(".layer").addClass("none"); 
                $(".window_content_left .start_play").removeClass("live").addClass("close");
                $(".window_content_left .start_play button").css("background","#999").text("结束直播");
              }
            }
        });
  },
	profileContent:function(id,selfid){
        console.log(this.getAdmin());
        $(".layer").removeClass("hide");
        var data={};
        var html = template('loading',data);
        document.getElementById('layer-box').innerHTML = html;
        this.getAdmin().then(function(ret) {
                console.log(ret);
                
                var adminArr=[];
                if(ret.data){
                    adminArr=ret.data;
                }
                adminArr.push(Anchor)
                console.log(adminArr);
                var adminList = (adminArr || []).map(function(admin) {
                   return '' + (admin.id || admin.user_id);
                });
                console.log(adminList);
                $.ajax({
                    type:"POST",
                    url:'/OpenAPI/V1/user/shareProfile',
                    dataType:'json',
                    cache:false,
                    data:{uid:id,selfid:selfid},
                    success:function(rep){
                        console.log(rep);
                        var data=rep.data;
                        var userinfo={
                            avatar:data.avatar,
                            sex:data.sex,
                            level:data.emceelevel,
                            intro:data.intro,
                            nickname:data.nickname,
                            id: data.id,
                            hasPermission: adminList.indexOf('' + User.id) >= 0,
                            isAdmin: adminList.indexOf('' + data.id) >= 0,
                            isAnchor: Anchor.id == User.id
                        };
                        console.log(userinfo);
                        var html = template('profileContent',userinfo);
                        document.getElementById('layer-box').innerHTML = html;
                    }
                })
            })
    },
	goToPay:function(){
        $(".layer").removeClass("hide");
        if(User.coinbalance == 0){
            var balance = '0.00';
        }else{
          var balance = User.coinbalance;
        }
        var data = {balance:balance};
        var html = template('payment', data);
        document.getElementById('layer-box').innerHTML = html;
	},
    pushAddress:function(){
        $(".layer").removeClass("hide");
        $.ajax({
                type:"POST",
                url:'/OpenAPI/v1/qiniu/getAllUrls',
                dataType:'json',
                cache:false,
                data:{roomID:room_id},
                success:function(rep){
                    console.log(rep);
                    var data=rep.data;
                    var addressinfo={
                        flv:data['flv'],
                        hls:data['hls'],
                        rtmp:data['rtmp'],
                    };
                    var html = template('pushAddress',addressinfo);
                    document.getElementById('layer-box').innerHTML = html;
                }
            })
    },
    wxpay:function(){
        $.ajax({
            //url:"{:U('weixinpay')}",
            url:"/my/weixinpay",
            type:"post",
            data:{uid:User.id,num:$("#inputmoney").val()},
            success:function(json){
                var json = eval("("+json+")");
                var payimg = "http://paysdk.weixin.qq.com/example/qrcode.php?data="+json.imgurl;
                json.orderno = json.orderno.substring( json.orderno.indexOf("_")+1);
                var orderno = json.orderno;
                var pay     = $("#inputmoney").val();
                $(".layer").removeClass("hide");
                var data = {img:payimg,pay:pay};
                var html = template('payment', data);
                document.getElementById('layer-box').innerHTML = html;
                $(".payment .tab_content_container").addClass("hide");
                $(".pay-wxin").removeClass("hide");
                $(".tab_header").addClass("hide");
                Pulse.create({
                    param: [[10000, 10000], [70000, 3000], [150000, 5000], [600000, 7000], [1800000, 10000]],
                    delay: false,
                    start: true,
                    stop: true,
                    pulse: function() {
                        $.ajax({
                            url:'/my/weixinCallback',
                            type: 'post',
                            data:{orderno:orderno},
                            dataType: 'json',
                            cache: false,
                            success: function(data) {
                                if ($.trim(data) === '1') {
                                    location.reload();
                                    //console.log(data);

                                }
                            }
                        });
                    }
                });
            },
            error:function(err){
                console.log(err);
            }
        })
    },
    CopyText:function(code){
        var e=document.getElementById(code);//对象是content
        e.select(); //选择对象
        document.execCommand("Copy"); //执行浏览器复制命令
        console.log("复制成功");
    },
    //注册/登录表单验证
    ValidationLogin:function(){
        for(var i=0;i<document.loginform.elements.length-1;i++){
              if(document.loginform.elements[i].value=="")
              {
                 $(".login-info.login .errormsg").text("当前表单不能有空");
                 document.loginform.elements[i].focus();
                 return false;
              }
           }
        return true;
    },
    ValidationReg:function(){
        for(var i=0;i<document.regform.elements.length-1;i++){
              if(document.regform.elements[i].value=="")
              {
                 $(".login-info.reg .errormsg").text("当前表单不能有空");
                 document.regform.elements[i].focus();
                 return false;
              }
              if(document.regform.elements[1].value!=document.regform.elements[2].value){
                $(".login-info.reg .errormsg").text("密码不一致");
                document.regform.elements[2].focus();
                return false;
              }
              var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
              var emailval=document.regform.elements[3].value;
              if(!reg.test(emailval)){
                $(".login-info.reg .errormsg").text("邮箱格式不正确");
                document.regform.elements[3].focus();
                return false;
              }
           }
           return true;
    },
    manageOperation: function(data) {
        $('.layer').addClass('hide');
        $("#layer-box").html("");
        if (data.managed_user_id == User.id) {
            alert('不能对自己进行此项操作');
            return;
        };

        SocketIO._sendMsg(JSON.stringify(data));
        
    },
    anchorlogout:function(){
    var regurl = '/OpenAPI/V1/User/anchorlogout';
    $.ajax({
        url:regurl,
        type: 'post',
        dataType: 'JSON', 
        data:{uid:User.id,roomid:User.curroomnum},
        success: function(data) {
          console.log(data);
          if(data.code != 0){
            alert(data.msg);
          }else{
            window.location.href=document.URL;
          }
        }
    });
  },
  shimibo:function(){
    var data={};
    var html = template('loading',data);
    document.getElementById('private_alert').innerHTML = html;
  	var shimi='/OpenAPI/V1/Private/getNowPrivateLimitForWeb';
  	var mymoley=$(".account-momoid span").text();
  	var self=this;
    $.ajax({
  		url:shimi,
  		type:'post',
  		dataType:'json',
  		data:{uid: Anchor.id,token:User.token},
  		success:function(data){
  			console.log(data);
		  	if(data.data.ptname){
                var data = {
                    ptid:data.data.ptid,
                    prerequisite:data.data.prerequisite,
                    mymoley:mymoley,
                    level:User.level,
                    ptname:data.data.ptname,
                    passid:data.data.id
                 };
                  self.plid=data.ptid;
                  self.ptname=data.ptname;
                  self.passid=data.passid;
                  var html = template('shimibo', data);
                  document.getElementById('private_alert').innerHTML = html;
		  	}else{
                $.getScript('/style/meilibo/vzboostyle/js/video.js',function(){
                  videojs.options.flash.swf = "/style/meilibo/vzboostyle/js/video-js.swf";
                    var data={};
                    var html = template('private', data);
                    document.getElementById('private_alert').innerHTML = html;
                    SocketIO._initConnect();
                });
                
		  	}
		
	  	}
  	})
  	
  },
  Ticket:function(){
	  console.log(this.plid);
	  if(!User.isLogin){
	  	this.nulogin();
	  }else{
	  		if($("._men ._menp2 span").html() >= $("._men ._menp1 span").html()){
	  			$.ajax({
						url:"/OpenAPI/V1/Private/checkPrivateChargeForWeb",
						type:'post',
						dataType:'json',
						data:{plid:Fn.passid,uid:User.id,aid:Anchor.id,token:User.token},
						success:function(data){
								console.log(data)
                                $.getScript('/style/meilibo/vzboostyle/js/video.js',function(){
                                  videojs.options.flash.swf = "/style/meilibo/vzboostyle/js/video-js.swf";
                                    var data={};
                                    var html = template('private', data);
                                    document.getElementById('private_alert').innerHTML = html;
                                    SocketIO._initConnect();
                                });
								
						},
						error:function(err){
							console.log(err)
						}
				})
	  		}else{
	  			this.goToPay();
	  		}		  	
	  }
  },
 Grade:function(){
		 if(!User.isLogin){
		  	this.nulogin();
		  }else{
		  	if($("._men ._menp2 span").html() >= $("._men ._menp1 span").html()){
		  		$.ajax({
		  			url:"/OpenAPI/V1/Private/checkPrivateLeveForWeb",
		  			type:'post',
					dataType:'json',
					data:{plid:Fn.passid,uid:User.id,aid:Anchor.id,token:User.token},
					success:function(data){
                        $.getScript('/style/meilibo/vzboostyle/js/video.js',function(){
                          videojs.options.flash.swf = "/style/meilibo/vzboostyle/js/video-js.swf";
                            var data={};
                            var html = template('private', data);
                            document.getElementById('private_alert').innerHTML = html;
                            SocketIO._initConnect();
                        });
						
					},
					error:function(err){
							console.log(err)
					}
		  		})
		  	}else{
	  			$(".layer").removeClass("hide");
		        var data = {};
		        var html = template('Nograde', data);
		        document.getElementById('layer-box').innerHTML = html;		        
			  }
		  }
    },
    PASSword:function(){
    	var pass=$(".shuru").val();
    	 if(!User.isLogin){
		  	this.nulogin();
		  }else{		  	
		  	$.ajax({
		  	  	url:"/OpenAPI/V1/Private/checkPrivatePassForWeb",
		  	  	type:'post',
				dataType:'json',
				data:{ 
					plid:Fn.passid,
					uid:User.id,
					aid:Anchor.id,
					token:User.token,
					prerequisite:pass
				},
				success:function(data){					
					console.log(data)
					if(data.data){
                        $.getScript('/style/meilibo/vzboostyle/js/video.js',function(){
                          videojs.options.flash.swf = "/style/meilibo/vzboostyle/js/video-js.swf";
                            var data={};
                            var html = template('private', data);
                            document.getElementById('private_alert').innerHTML = html;
                            SocketIO._initConnect();
                        });
						
					}else{
						$(".layer").removeClass("hide");
						var data={};
					 	var html = template('passWORD', data);
		                document.getElementById('layer-box').innerHTML = html;
					}
				 }
		  	})	  	  		  				  		
		   }
    }
    
}

function reloadAbleJSFn(id,newJS)
{   
    var oldjs = null; 
    var t = null; 
    var oldjs = document.getElementById(id); 
    if(oldjs) oldjs.parentNode.removeChild(oldjs); 
    var scriptObj = document.createElement("script"); 
    scriptObj.src = newJS; 
    scriptObj.type = "text/javascript"; 
    scriptObj.id   = id; 
    document.getElementsByTagName("head")[0].appendChild(scriptObj);
}