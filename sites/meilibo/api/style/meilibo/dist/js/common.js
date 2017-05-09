/**
*直播间js
*编码utf8
*美丽播
*/

//设置礼物id giftid，礼物需要金额giftmoney，余额money
var giftid='',giftmoney='',money='',giftimg='',giftname='';
var myVideo=document.getElementById("video1");
var gifti=0;
var giftj=0;
var queue = new Queue(3);
var queueGift = new Queue(3);
var chattool=$(".chat-tool"),
userinfocon=$(".user_info_con"),
rednumber=$("#red_number"),
send_button=$(".send_button"),
bglancemoney=$(".bglance_money"),
chongzhialert=$("#chongzhi_alert"),
totalnumber=$("#total_number");
var disturl=public;
var Ctrfn={
    wxPay:function(){
         var re = /^[1-9]+[0-9]*]*$/;
         var money = $("#chongzhi_number").val();
        if(money==""){
            Txbb.Pop('toast', '充值金额不能为空');
            return;
        }
        if(money<1){
            Txbb.Pop('toast', '充值金额至少1元');
            return;
        }
        if(!re.test(money)){
            Txbb.Pop('toast', '充值金额必须为整数');
            return;
        } 
        $.post( 'weixinpay', { uid: User.id, num: money } ,function(data){
            data = JSON.parse(data);
            if(data.error_code == 0) {
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',
                    data.msg,
                    function(res){
                        console.log(data.msg);
                        if(res.err_msg == 'get_brand_wcpay_request:ok') {
                            Ctrfn.coinbalance();
                        } else {
                            Txbb.Pop('toast', '充值失败');
                            
                        }
                    }
                );
            } else {
                alert(data.msg);
            }
        });
    },
    //获取当前余额
    coinbalance:function(){
        $.post("getCoinBalance",{uid:User.id},function(res){
            bglancemoney.text(" "+res+" ");
            chongzhialert.hide();
            Txbb.Pop('toast', '恭喜你，充值成功');
        })
    },
    moreShare:function(){
        userinfocon.hide();
        $(".chat-tool .more_list").hide();
        $(".share_box").addClass("sanimt");
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏分享列表
            if(!target.is('.more_list *')&&!target.is('.share_box') && !target.is('.share_box *') && !target.is("#flower-btn")) {
               $(".share_box").removeClass("sanimt");
            }
        });
    },
    //更多
    moreBtn:function(){
        if($(".chat-tool .more_list").is(":hidden")){
            $(".chat-tool .more_list").show();
        }else{
            $(".chat-tool .more_list").hide();
        }
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏礼物列表
            if(!target.is('.more_list *')&&!target.is('#more-btn')) {
               $(".chat-tool .more_list").hide();
            }
        });
    },
    talkBtn:function(e){
        userinfocon.hide();
        if(!$(".chat-tool").is(":hidden")){
            $(".chat-tool").hide();
        }
        $(".chat_input").show();  
        $("#message").focus(); 
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏礼物列表
            if(!target.is('#talk-btn')&&!target.is('.chat_input')&&!target.is('.chat_input *')&&!target.is('#gift-btn')) {
                if($(".chat-tool").is(":hidden")){
                    $(".chat-tool").show();
                }
                $(".chat_input").hide();
                $(".chat_barrage ").removeClass("animte");
                fly=""
                
            }
        });
    },
    giftTool:function(){
        userinfocon.hide();
        if(!$(".chat-tool").is(":hidden")){
            $(".chat-tool").hide();
        }
        $(".chat_gift").css({"opacity":"1","z-index":"23","display":"block"});
        
        $(".section1").click(function(e) {
            var target = $(e.target);
            //点击其他地方隐藏礼物列表
            if(!target.is('.chat_gift') && !target.is(".chat_gift *") && !target.is("#gift-btn") && !target.is("#red_alert") && !target.is("#red_alert *") && !target.is("#chongzhi_alert") && !target.is("#chongzhi_alert *")&& !target.is("#talk-btn")) {
                $(".chongzhi_num input").val('');
                $(".chat_gift").css({"opacity":"0","z-index":"-1"});
                $(".sel").remove();
                if($(".chat-tool").is(":hidden")){
                    $(".chat-tool").show();
                }
                giftid='';
            }

        });
    },
//    //点击发送信息设置
    onmessage:function(url){
        if($("#message").val() == ""){
            Txbb.Pop('toast', '消息不能为空...','center');
            return;
        }
        if(User){
            this.flymsgfn(url);
        }else{
            $("#weui_dialog_alert").show();
        }
    },
    flymsgfn:function(url){
        if(fly=="FlyMsg"){
            $.ajax({
                type: 'POST',
                url: url,
                data:{"token":User.token,"roomid":room_id,"content":$("#message").val()},
                dataType:'json',
                success: function(data){
                    if((Number($(".bglance_money").text())-100)<0){
                        $(".bglance_money").text(0); 
                        Txbb.Pop('toast', '余额不足，请充值...');
                    }else{
                        $(".bglance_money").text(Number($(".bglance_money").text())-100); 
                    }
                }
            });
        }else{
            SocketIO._chatMessage($("#message").val());
        }

        $("#message").val("");
        $(".chat_input").hide();
        chattool.show();
        $(".chat_input > .chat_barrage").removeClass("animte ");
        fly="";
    },
    init_screen:function(){
            var _top = 0;
            $(".chat_barrage_box > div").show().each(function () {
                var _left = $(window).width() - $(this).width()+200;
                var _height = $(window).height();
                _top = _top + 45;
                if (_top >= _height - 200) { 
                    _top = 40;
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
    play:function(objbtn){
        var myVideo=document.getElementById("videoHLS_html5_api");
        objbtn.parent().hide();
        $(".jw-preview").hide();
        myVideo.play();
    },
    giftBtn:function(objbtn){
        var swiperSlide=$(".swiper-slide-active > div");
        //获取当前礼物id
        giftid=objbtn.attr("data-id");
        //获取当前礼物所需金额
        giftmoney=Number(objbtn.find("img").attr("data-money"));
        //获取礼物图片
        giftimg=objbtn.find("img").attr("src");
        //获取礼物名称
        giftname=objbtn.attr("data-giftname");
        //当前余额
        totalnumber.val(giftmoney);
        $(".total_money span").text(bglancemoney.text());
        //选中状态
        var img='<span class="sel"><img src="'+disturl+'images/sel.png"></span>';
        //除了当前礼物选中，其他的都移除状态
        swiperSlide.find(".sel").remove();
        swiperSlide.eq(objbtn.index()).append(img);
        swiperSlide.attr("data-tag","1");
        //当前为1添加状态，为0移除状态
        if(objbtn.attr("data-tag")== "1"){
            swiperSlide.attr("data-tag","1");
            objbtn.attr("data-tag","0");
            send_button.css("background","rgb(229,100,110)");
            $("#red_alert").show();
        }else{
            objbtn.find(".sel").remove();
            objbtn.attr("data-tag","1");
            send_button.css("background","#989898");
            giftmoney='';
        }
    },
    //点击发送礼物按钮
    sendBtn:function(url){
        red_val=rednumber.val();
        //把余额转为数值类型
        money=Number(bglancemoney.text());
        var count=$("#red_number").val();//获取礼物个数
        //如果发生红包个数为0

        if(rednumber.val()=="0"){
            Txbb.Pop('toast', '礼物个数不能为空...'); 
        }

        //如果余额小于当前礼物所需金额，或者小于所发红包个数总金额
        if( money < giftmoney ||  money < red_val*giftmoney ){
            Txbb.Pop('toast', '余额不足，请充值...');
            //所需金额差值
            $(".chongzhi_num input").val(totalnumber.val()-money);
            return;
        }
        //如果没有选中礼物
        if(giftid==''){
            Txbb.Pop('toast', '请选择礼物...');
            return;
        }
        this.giftsuccess(url,money,giftid,red_val);
        giftid='';
    },
    giftsuccess:function(url,money,giftid,red_val){
        //成功发送红包
        var html='';
        var sred_val = rednumber.val();
        var red_number=sred_val*giftmoney;
        $.ajax({
            type:"POST",
            url:url,
            dataType:'json',
            data:{
                gift_id:giftid,
                count:red_val,
                to_uid:to_uid,
                token:User.token
            },
            success:function(data){
                console.log(data);
                bglancemoney.text(money-red_number+'.00');
                $(".total_money span").text(money-red_number+'.00');
                $(".swiper-slide > div").attr("data-tag","1");
                rednumber.val('1');
                $(".sel").remove();
                $(".red_alert").hide();
                $(".chat_gift").hide();
                chattool.show();
                Txbb.Pop('toast', '礼物发送成功');
                
            }
        })
    },
    gifthert:function(){
        var images = [],id = 0,confirm = true,num = 20,max = 2,loopNum = 0,speed = 30,timer = null;
        timer = setInterval(loop , speed);
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
                src:''+disturl+'images/gift/'+this.imgAddress[this.img], 
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
    bigshowgift:function(giftid){
           var box=$("#big_gift_show_box");
           var len=$("#big_gift_show_box > div").length;
           if(len<1){
           if(giftid==84){
              var gifthtml='<div class="big_gift_animte_feiji">'
                            +'<div><img src="'+disturl+'images/gift/bomber.png" width="150" style="margin-bottom:-85px;"></div>'
                            +'<div class="giftan">'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: 38px;left: 6px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: 23px;left: 18px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: 12px;left: 47px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 25px;height: 25px;position:relative;top: -12px;left: 66px;background-size: 69px;"></span>'
                            +'<span class="giftspan" style="width: 40px;height: 50px;position:relative;top: -58px;left: 8px;background-size: 135px;"></span></div>'
                            +'<div><img src="'+disturl+'images/gift/bomber_shadow.png"/ style="width:150px;margin-top:50px;"></div>'
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
                feiji.animate({"left":"40%"},2000,function(){
                    Ctrfn.gifthert();
                    setTimeout(function(){
                        feiji.animate({"left":"0"},2000);
                    },2000)
                })
                setTimeout(function(){
                    feiji.animate({"opacity":"0"},600,function(){
                        clearTimeout(timer);
                        feiji.remove();
                    });
                },6500);
            }else if(giftid==79){
                var str='<div class="big_gift_animte_fireworks">'
                        +'<div id="fireworks" class="giftan"></div>'
                        +'</div>';
                box.append(str);
                var timer=null;
                var imgurl=""+disturl+"images/gift/fireworks.png";
                var positions=['0 -880','-200 -880','-400 -880','-600 -880','-800 -880','-1000 -880','-1200 -880','-1400 -880','-1600 -880','-1800 -880','0 0','-204 0','-408 0','-612 0','0 -300','-212 -300','-424 -300','-635 -300','-847 -300','-1059 -300','-1270 -300','-1482 -300','-1 -499','-212 -499','-424 -499','-635 -499','-847 -499','-1059 -499','-1269 -498','-1479 -497','0 -696','-212 -696','-424 -695','-635 -696'];   
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
                var str='<div class="big_gift_animte_yacht" style="padding-top:220px;">'
                          +'<div class="yacht_shui animt">'
                            +'<div class="giftan" style="text-align: center;">'
                                +'<img src="'+disturl+'images/gift/yacht_hull.png" width="120" style="display:block;">'
                                +'<img src="'+disturl+'images/gift/yacht_shadow.png" width="120" style="display:block;margin-top:20px;">'
                            +'</div>'
                          +'</div>'
                        +'</div>';
                box.append(str);
                var giftan=$(".big_gift_animte_yacht .yacht_shui .giftan");
                var yacht=$(".big_gift_animte_yacht");
                giftan.animate({"margin-left":"20px"},2000,function(){
                    setTimeout(function(){
                        giftan.animate({"margin-left":"110%"},4000,function(){
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
                            +'<div class="car_img2 none">'
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
                big_car_box.animate({"right":"20px","top":"160px"},2000,function(){
                    setTimeout(function(){
                        big_car_box.animate({"right":"150%","top":"220px"},2000,function(){
                            $(".big_car_box .car_img1").addClass("none");
                            $(".big_car_box .car_img2").removeClass("none");
                            big_car_box.css({"right":"-100%","top":"260px"});
                            big_car_box.animate({"right":"20px","top":"180px"},2000,function(){
                                $(".big_car_box .car_img2 .car_weideng").show();
                                setTimeout(function(){
                                    big_car_box.animate({"right":"150%","top":"100px"},2000,function(){
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
                        $('.big_gift_show_box >div[name="big_tx_box"]').attr("class","big_tx_box bg"+i);
                        i++;
                    }else{
                        clearInterval(timer);
                        $('.big_tx_box').remove();
                    }
                },200)
                
            }
          }
    },
    sendShow:function(red_val_temp,nickname,userphoto,giftimg,giftname){
        // 创建一个具有3个线程的队列
        
        queue.push(function(){
            var defer = $.Deferred();
            var gSlen=$(".gift_show").length;
            var numGift=1;
            if(gSlen < 3){
                gifti++;
                giftj++;
                var temp = "gift"+gifti;
                var temp2="giftshow"+giftj;
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
                $("#gift_show_box").append(gift_show);
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
    //关闭充值弹框
    closeCzhi:function(){
        chongzhialert.hide();
        $(".sel").remove();
        $(".chongzhi_num ul").addClass("none");
        $(".swiper-slide > div").attr("data-tag","1");
    },
    //充值
    balanceFn:function(){
        $(".total_money span").text(bglancemoney.text());
        chongzhialert.show();
        $("#red_alert").hide();
        rednumber.val('1');
    },
    //关闭礼物弹框
    closeRed:function(){
        $("#red_alert").hide();
        rednumber.val('1');
        $(".sel").remove();
        $(".swiper-slide > div").attr("data-tag","1");
    },
    contr_close:function(){
        $("#contributionval").removeClass("anit");
    },
    charmval:function(objbtn,url){
        var user_id=objbtn.attr("userid");
        $.ajax({
            url:url,
            type: 'get',
            dataType: 'json',
            data:{"user_id":user_id},
            success: function(data) {
                console.log(1,data);
                var info = {
                        wealth: data.data['sum_coin'],
                        list: data.data['list']
                    };
                var html = template('ranklist', info);
                document.getElementById('contributionval').innerHTML = html;
            }
        });
        $("#contributionval").addClass("anit");
    },
    userpicBtn:function(objbtn,url){
           var user_id=objbtn.attr("user_id");
           console.log(user_id);
           if(userinfocon.is(":hidden")){
                $.ajax({
                    url:url,
                    type: 'get',
                    dataType: 'json',
                    data:{"uid":user_id},
                    success: function (data) {
                        var html = template('userinfo', data.data);
                        document.getElementById('user_info_con').innerHTML = html;
                        userinfocon.show();
                    }
                });
            }else{
               userinfocon.hide();
            }
    },
    userinfoBtn:function(objbtn,url){
        var user_id=objbtn.attr("userid");
           console.log(user_id);
           var profileData;
           if(User.islogin == "true"){
                profileData={"uid":user_id,"token":User.token};
            }else{
                profileData={"uid":user_id};
            }
            console.log(profileData);
           if(userinfocon.is(":hidden")){
                $.ajax({
                    url:url,
                    type: 'get',
                    dataType: 'json',
                    data:profileData,
                    success: function (data) {
                        console.log("asd",data);
                        if(data.code == 0){
                            var html = template('anchorInfo', data.data);
                            document.getElementById('user_info_con').innerHTML = html;
                            userinfocon.show();
                        }else{
                            alert(data.msg);
                        }

                    }
                });
            }else{
               userinfocon.hide();
            }
    },
    pcanchorinfo:function(objbtn,url){
        var user_id=objbtn.attr("userid");
                $.ajax({
                    url:url,
                    type: 'POST',
                    dataType: 'json',
                    data:{"uid":user_id},
                    cache: false,
                    success: function (data) {
                        var json=eval(data.data);
                        var html = template('anchorInfo', json);
                        document.getElementById('anchorinfo').innerHTML = html;
                    }   
                });
    },
    iShare:function(objbtn){
        $("#share_alert").show();
        $(".share_box").removeClass("sanimt");
        if(objbtn.hasClass("iShare_wechat")){
            $(".share_prompt p").html("分享到微信，请点击右上角</br>再选择【分享给朋友】")
        }else{
            $(".share_prompt p").html("分享到QQ，请点击右上角</br>再选择【分享到手机QQ】")
        }
        
    },
    userFollowed:function(objbtn){
        if(objbtn.attr("data-follow")==0){
            objbtn.text("已关注").css("background","rgba(235,79,56,1)");
            objbtn.attr("data-follow","1");
        }else{
            objbtn.text("关注").css("background","rgba(235,79,56,0.6)");;
            objbtn.attr("data-follow","0");
        }
    }


}
