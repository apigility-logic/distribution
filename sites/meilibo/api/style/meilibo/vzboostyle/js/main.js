'use strict';

$(function(){
    ajaxFn.getNowPrivateLimit();
    var giftval = '';
	//热门直播/我的主播
	$(".live-page-window .roomList.noLive span").click(function(){
		var _index=$(this).index() - 1;
		$(".recommend-live-tab").eq(_index).removeClass("hide").siblings().addClass("hide");
	})
    
    $(document).on("click",".go-live",function(){
        if(User.isLogin){
            window.location.href="/Show/anchorshow";
        }else{
            Fn.nulogin();
        }
    })
    //排行榜/在线用户
	$(".fansAttraction .tab_header ul li a").click(function(){
        var wleft="";
        if($(".page-window").width()>950){
             wleft=($(window).width()-1122)/3 + 905;
        }else{
             wleft=($(window).width()-950)/3 + 731;
        }
		
		var _left=$(this).offset().left-wleft;
	   	$(".fansAttraction-slide-border").stop().animate({left:_left},50);
		$(".fansAttraction .tab_header ul li a").removeClass("on");
		$(this).addClass("on");
		var _index=$(this).parent().index();
		$(".tab_content_container .tab_content").eq(_index).addClass("current").siblings().removeClass("current");
	})
    //关注主播列表
    $(document).on('click',".myAnchor",function(){
        Fn.getMyAnchor();
    })
    //发送消息
    $(document).on('click',".sendBtn",function(){
        Fn.chatfn();
    })

    //发送消息键盘事件
    $(document).on('keydown',".editArea",function(e){
        if(e.keyCode==13){
            Fn.chatfn();
		}
    })
    //滚屏
    $(document).on('click',".scrollBtn",function(){
       Fn.scrollChat();
    })
    //点击下载
    $(".operate-content .account-box .goDownloads>a").click(function(){
        $(".mm-downloads-wrap").removeClass("hide");
        $("body.live-skin").click(function(e){
            var target=$(e.target);
            if(!target.is('.goDownloads')&&!target.is('.goDownloads *')&&!target.is('.mm-downloads-wrap')&&!target.is('.mm-downloads-wrap *')) {
               $(".mm-downloads-wrap").addClass("hide");
            }
        })
    })
    //聊天底部消息
    $(document).on('click',".live-new-msg-tips",function(){
       Fn.msgTips();  
    })
    //点击关闭/取消
    $(document).on("click",".toolTip .cancel,.layer .closeLayer",function(){
    	$('.layer').addClass("hide");
        $("#layer-box").html("");
    })
    //点击头像显示个人信息
    $(".operate-content .account-box .avatar").click(function(){
    	$(".operate-content .account-box .account-info-box").removeClass("hide");
    	$("body.live-skin").click(function(e){
    		var target=$(e.target);
    		if(!target.is('.operate-content .account-box .avatar')&&!target.is('.account-pro *')&&!target.is('.account-pro')) {
               $(".operate-content .account-box .account-info-box").addClass("hide");
            }
    	})
    })
    //点击个人资料
    $(".account-list .oneself-profile").click(function(){
    	Fn.oneselfn();
    })
    //隐藏弹层
    $(".layer").click(function(e){
    	var target=$(e.target);
        if($(".layer .outroom").length>0){
            window.location.href=HOST_URL;
        }
    	if(!target.is(".profile-box *")&&!target.is(".profile-box")&&!target.is(".payToolTip")&&!target.is(".payToolTip *")&&!target.is(".giftToolTip")&&!target.is(".giftToolTip *")&&!target.is(".login-box *")&&!target.is(".profileContent")&&!target.is(".profileContent *")&&!target.is(".pushaddress *")&&!target.is(".pushaddress")){
    		$(".layer").addClass("hide");
            $("#layer-box").html("");
    	}
       
        
        
    })
    //点击礼物
    $(document).on("click",".gift-box-wrap .giftItem",function(){
        if(User.isLogin){
            var me=$(this);
            giftval = me.attr('data-giftvalue');
            Fn.giftItem(me);
        }else{
            Fn.nulogin();
        }
    	
    })
    //点击确定发送礼物
    $(document).on("click",".toolTip .sure",function(){
        var count = $('.giftn').val();
        Fn.sendgift(count);
    })
    //改变礼物个数
    $(document).on("keyup",".giftToolTip .giftn",function(){
        var val=$(this).val();
        Fn.changeMoney(val);
    })

    //点击用户列表显示用户信息
    $(document).on("click",".starCharts .rankingItem,.onlineUsers  .userItem,.live-msg-list .msg .avatar",function(){
        var userid = $(this).attr('data-id');
    	Fn.profileContent(userid,User.id);
    })
    //关注/取消关注 主播
    var focusstatus = 0;
    $(document).on("click", ".focus", function () {
        if(!User.isLogin){
            Fn.nulogin();
            return false;
        }
        if (focusstatus != 1) {
            focusstatus = 1;
            if ($.trim($(this).text()) == "已关注") {
                var id = 0;
            } else {
                var id = 1;
            }
            if (id == 1) {
                $.ajax({
                    type: 'POST',
                    url: '/OpenAPI/V1/User/follow',
                    data: {"uid": Anchor.id,"roomid":room_id,"token":User.token},
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if(data.code == 0){
                            $('.focus').removeClass("nofocus");
                            $('.focus span').addClass("fod").removeClass("noFod").text("已关注");
                        }
                        focusstatus = 0;
                    }
                });
            }else{
                $.ajax({
                    type: 'POST',
                    url: '/OpenAPI/V1/User/unfollow',
                    data: {"uid": Anchor.id,"token":User.token},
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        if(data.code == 0){
                            $('.focus').addClass("nofocus");
                            $('.focus span').removeClass("fod").addClass("noFod").text("关注");
                        }
                        focusstatus = 0;
                    }
                });
            }
        } else {
            console.log('too fast，please wait...');
        }
    })
    //充值
    $(".goPay .goToPay").click(function(){
    	Fn.goToPay();
    })

    //立即充值
    $(document).on("click",".paymentbtn",function(){
        if(isNaN($("#inputmoney").val())||$("#inputmoney").val() < 0.01){
           return false;
        }else{
            Fn.wxpay();
        }
    })
    //弹幕
     $(document).on('click',".live-edit-box-inner .barrageBtn",function(){
        if($(".editAreaWrap .setting-toggle").hasClass("checked")){
            $(".editAreaWrap .setting-toggle").removeClass("checked");
            $("#message").attr('placeholder','快和大家一起聊天吧');
            fly=""
        }else{
            $(".editAreaWrap .setting-toggle").addClass("checked");
            $("#message").attr('placeholder','发送弹幕将消费100秀币');
            fly="FlyMsg"
        }
    })
    //礼物列表加载
    ajaxFn.giftajax();
    
    //点击加载热门主播列表
    /*初始化*/
    var counter =0; /*计数器*/
    var pageStart =0; /*offset*/
    var pageSize =8; /*size*/
    ajaxFn.livegetData(pageStart, pageSize);
    $(document).on('click', '.liveButton', function(){
        counter ++;
        pageStart = counter * pageSize;
        ajaxFn.livegetData(pageStart, pageSize);
    });
    //点击登陆

    // $(document).on("click",".noname-login",function(){
    //     $(".layer").removeClass("hide");
    //     var data = {};
    //     var html = template('loginUser', data);
    //     document.getElementById('layer-box').innerHTML = html;
    // })

    //点击登陆注册
    $(document).on("click",".logintab > a",function(){
        var _index=$(this).index();
        $(".login-box .login-info").eq(_index).removeClass("hide").siblings(".login-box .login-info").addClass("hide");
    })

//    //点击登陆
//    $(document).on("click",".get-login",function(){
//        Fn.ValidationLogin();
//    })
//    
//    //点击注册
//    $(document).on("click",".get-reg",function(){
//        Fn.ValidationReg();
//    })

//推流地址
$(".notify-box .change-btn.go-dress").click(function(){
    Fn.pushAddress();
})


$(document).on('click',".pushaddress .button1",function(){
    Fn.CopyText("code1");
});
$(document).on('click',".pushaddress .button2",function(){
    Fn.CopyText("code2");
});
$(document).on('click',".pushaddress .button3",function(){
    Fn.CopyText("code3");
});

 //管理员操作：禁言、踢人、设为管理员、取消管理员
$(document).on('click', '.profile-operation .adminbtn', function() {
       var data = $(this).data();
        Fn.manageOperation({
            _method_: 'Manage',
            _type_: data.type,
            managed_user_id: data.id,
            managed_user_name: data.name
        });
});

$(document).on("click",".tab-manger",function(){
    Fn.getAdmin();
});
  Fn.shimibo();
  //门票入场
 $(document).on("click","#Ticket",function(){
    	Fn.Ticket();
 });
 //等级入场
 $(document).on("click","#Grade",function(){
    	Fn.Grade();
 });
 //密码入场
  $(document).on("click","#PASSword",function(){
    	Fn.PASSword();
 });
})








































