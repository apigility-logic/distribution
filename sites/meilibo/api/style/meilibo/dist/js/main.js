/**
*直播间js
*编码utf8
*美丽播
*/
$(function(){
    //点击微信分享
    $(".iShare_wechat").click(function(){
        var objbtn=$(this);
        Ctrfn.iShare(objbtn);
    })
    //点击QQ分享
    $(".iShare_qq").click(function(){
        var objbtn=$(this);
        Ctrfn.iShare(objbtn);
    })
   $("#share_alert").click(function(){
        $(this).hide();
   })
    //微信支付
    $(".weixin_pay").click(function(){
        Ctrfn.wxPay();
    })
    //分享
    $("#more_share").click(function(e){
        Ctrfn.moreShare();
    })
    $("#more-btn").click(function(e){
        Ctrfn.moreBtn(); 
    })
 
    //弹幕
    $(".chat_barrage span").click(function(){
        if($(this).parent().hasClass("animte")){
            $(this).parent().removeClass("animte");
            fly=""
        }else{
            $(this).parent().addClass("animte");
            $("#message").val("").focus();
            fly="FlyMsg"
        }
    })
    $("#chat").click(function(){
        var url='/OpenAPI/V1/Gift/sendBarrage'
        Ctrfn.onmessage(url);
    })

    $(document).on("click",".user_followed",function(){
            var token = User.token;
            if($.trim($(this).text()) == '已关注'){
              var url = "/OpenAPI/v1/User/unfollow";
            }else{
              var url = "/OpenAPI/v1/User/follow";
            }
            var attentionid = $(this).attr("value");
            var _this = $(this);
            console.log(_this);
            $.ajax({
                    type: 'POST',
                    url: url,
                    data:{'token': token,'uid':attentionid},
                    dataType:'json',
                    success: function(data){
                        console.log(data);
                            if (data.code== 0){                         
                                if (data.data=="关注成功!"){                     
                                    _this.text('已关注');
                                }else{
                                    _this.text('关注');
                                }
                            }
                    }
            });
   })

    //设置充值总金额值
    // $(".total_money span").text($(".bglance_money").text());

    //点击聊天按钮，显示输入框
    $("#talk-btn").click(function(e){
        User ? Ctrfn.talkBtn(e) : $("#weui_dialog_alert").show();
    })
    
    //点击礼物tool
    $("#gift-btn").click(function(){
        User ? Ctrfn.giftTool() : $("#weui_dialog_alert").show(); 
    })

    //阻止事件冒泡
    $(".chat_input").click(function (e){
            e.stopPropagation();
        });

    //点击播放按钮
    $(document).on("click","#play",function(){
        var objbtn=$(this);
        Ctrfn.play(objbtn);
    })
    //点击魅力值
    $(".charmval").click(function(){
        var objbtn=$(this);
        var url='/OpenAPI/V1/user/sharecontributelist';
        Ctrfn.charmval(objbtn,url);
    })
    
    //点击发送禮物
    $(".red_btn").click(function(){
        var url='/OpenAPI/V1/Gift/send';
        Ctrfn.sendBtn(url);
    })

    //点击充值选项
    $(document).on("click",".chongzhi_num",function(){
        var str='';
        if($(".chongzhi_num ul").hasClass("none")){
            $.ajax({
                url:'/OpenAPI/V1/user/getchargeoption',
                type: 'get',
                dataType: 'json',
                data:{'token': User.token},
                success: function(rep) {
                    var data=rep.data.list;
                    for(var i=0;i<data.length;i++){
                        str+='<li><img src="/style/meilibo/dist/images/zhuanshi.png">'+data[i].diamond+'<span>￥'+data[i].rmb+'</span></li>'
                        
                    }
                    $(".chongzhi_num ul").append(str);
                    $(".chongzhi_num ul").removeClass("none");
                }
            });
        }else{
            $(".chongzhi_num ul").addClass("none");
            $(".chongzhi_num ul").html("");
        }
        
    })
    $(".chongzhi_num ul").on("click","li",function(){
        var val=$(this).find("span").text();
        $("#chongzhi_number").val(val.substring(1));
    })


    //点击礼物
    $(document).on("click",".swiper-slide > div",function(e){
        var objbtn=$(this);
        Ctrfn.giftBtn(objbtn);
    })

    //红包个数发生改变时
    var red_val='';
    $("#red_number").keyup(function(e){
        red_val=$(this).val();
        $("#total_number").val(red_val*giftmoney);
    })


    //点击用户头像
        $(document).on("click",".userpic li > img",function(){
        var objbtn=$(this);
        var url='/OpenAPI/V1/user/shareProfile';
        Ctrfn.userpicBtn(objbtn,url);
        })
        $(document).on("click",".user_close",function(){
            $('.user_info_con').hide();
        });

    //点击主播头像显示详情
    $(".section1_box .userinfo > img").click(function(){
        var objbtn=$(this);
        if(User.islogin == "true"){
            var url='/OpenAPI/V1/user/profile';
        }else{
            var url='/OpenAPI/V1/user/shareProfile';
        }
        Ctrfn.userinfoBtn(objbtn,url);
    })

})
//加载礼物tool
$(function(){
if(User){
    $.ajax({
        url:'/OpenAPI/V1/Gift/collection',
        type: 'post',
        data:{token:User.token},
        dataType: 'json',
        success: function(data) {
            var json=eval(data);
            var pagenum=Math.ceil(json.data.length/8);
            var num=[];
            for(var i=1;i<pagenum;i++){
                num[i]=i;
            }
            var gift = {
                giftlist: json.data,
                pagenum:num,
            };
            var html = template('giftlist', gift);
            document.getElementById('swiper-wrapper').innerHTML = html;
            //礼物列表切换
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationClickable: true,
                observer: true,
                observeParents: true
            });
        }
    }); 
    }
})
var $n=0;
$(function(){
    videotimer=setInterval("connectChange()", 3000);
    $n = 0;
});
connectChange()
function connectChange(){
    $n++;
    myPlayer = videojs("videoHLS");
    request=$.ajax({
          type:"get",
          url:videosrc,
          success:function(data,status,xhr){
            if($n==1){
                
                $("#videoPlay").show(); 
                var div='<button id="play"><img src="/style/meilibo/dist/images/play.png" width="61"></button>';
                $("#top_box").append(div);
                $("#state").hide();
                myPlayer.height(h);
                myPlayer.show()
                $("#top_box").show();
            } 
          },
          error: function(msg) {  
                $("#videoPlay").hide();   
                $("#play").remove();       
                $("#state").show();
                $("#top_box").hide();
                $(".jw-preview").show();
                $(".section1_box .roomtitle").remove();
                $n=0;
                // clearInterval(videotimer);
           },
        });
    //开始或恢复播放
    myPlayer.on('play', function() {  
        if(document.getElementById('roomtitle')) {
            $("#roomtitle").remove();
        }
        if(pla==1){
            var html='<div id="roomtitle" class="roomtitle">'+User.nickname+'的直播间</div>';
            $(".section1_box .header").prepend(html);
        }
        //myPlayer.height(h);
        $("#top_box").hide();
        $(".jw-preview").hide();

    });
    // 暂停播放
    myPlayer.on('pause', function() { 
        $(".section1_box .roomtitle").remove(); 
        $("#top_box").show();
    });

}

//滑动清屏
    var viewport = document.getElementById("section1");
    var obj = document.getElementById("section1_box");
    viewport.addEventListener('touchstart', function(e) {
        var touch = e.touches[0];
        startX = touch.pageX;
        startY = touch.pageY;
    }, false)

    viewport.addEventListener('touchmove', function(e) {
        var touch = e.touches[0];
        var deltaX = touch.pageX - startX;
        var deltaY = touch.pageY - startY;
        //如果X方向上的位移大于Y方向，则认为是左右滑动
        if(Math.abs(deltaX) > Math.abs(deltaY) && deltaX > 50){
            $(".section1_box").hasClass("animate")?obj.className="section1_box animate animte":obj.className="section1_box animte";
            $(".chat_input").hide();
            $(".chat_barrage ").removeClass("animte");
            fly=""
        }else if(Math.abs(deltaX)>Math.abs(deltaY) && deltaX<-50){ 
            $(".section1_box").hasClass("animate")?obj.className="section1_box animate":obj.className="section1_box";
        }else if(Math.abs(deltaX)<Math.abs(deltaY) && deltaY<-10){
            if(User){
                if(!$(".section1_box").hasClass("animate")){
                    $(".section1_box").addClass("animate");
                    $(".chat_gift").addClass("animate");
                    
                }
            }
            
        }else if(Math.abs(deltaX)<Math.abs(deltaY) && deltaY>0){
            if(User){
                if($(".section1_box").hasClass("animate")){
                    $(".section1_box").removeClass("animate");
                    $(".chat_gift").removeClass("animate");
                    
                }
            }
            
        }else{
            console.log('点击未滑动');
        }
    }, false)





















