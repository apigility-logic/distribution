$(function(){
    $(document).on("click",".login_box .title span",function(){
         $(this).addClass("active").siblings().removeClass("active");
         var idx=$(this).index();
         $(".login_reg_box").eq(idx).removeClass("hide").siblings(".login_reg_box").addClass("hide");
    })

     //点击关闭/取消
    $(document).on("click",".layer .closeLayer",function(){
        $('.layer').addClass("hide");
        $("#layer-box").html("");
    })

    //隐藏弹层

    $(".layer").click(function(e){
      var target=$(e.target);
      if(!target.is(".profile-box *")&&!target.is(".profile-box")&&!target.is(".payToolTip")&&!target.is(".payToolTip *")&&!target.is(".giftToolTip")&&!target.is(".giftToolTip *")&&!target.is(".login-box *")&&!target.is(".profileContent")&&!target.is(".profileContent *")&&!target.is(".pushaddress *")&&!target.is(".pushaddress")&&!target.is(".livemanual")&&!target.is(".livemanual *")&&!target.is(".live_types")&&!target.is(".live_types *")){
        $(".layer").addClass("hide");
            $("#layer-box").html("");
      }
    })
    //点击用户显示用户信息
    $(document).on("click",".userItem",function(){
        var userid = $(this).attr('data-id');
        Fn.profileContent(userid,User.id);
    })

    $(document).on("click",".onlineUser span",function(){
        var _index=$(this).index();
        $(this).addClass("active").siblings().removeClass("active");
        $('.userlist').eq(_index).removeClass("hide").siblings(".userlist").addClass("hide");
    })

    $(document).on("click",".push_tutorial",function(){
        $(".layer").removeClass("hide"); 
        var data={};          
        var html = template('livemanual',data);
        document.getElementById('layer-box').innerHTML = html;
    })

    //发送消息
    $(".chat_msg_btn ").click(function(){
        Fn.chatfn();
    })
    //发送消息键盘事件
    $(".editArea").keydown(function(e){
        if(e.keyCode==13){
            Fn.chatfn();
        }
    })
    
    $(document).on("click", ".close", function () {
        var regurl = '/OpenAPI/V1/Room/entryOfflineRoomForPc';
          $.ajax({
              url:regurl,
              type: 'post',
              dataType: 'JSON', 
              data:{roomid:User.curroomnum},
              success: function(data) {
                console.log(data);
                if(data.code != 0){
                    alert(data.msg);
                }else{
                  Fn.anchorlogout();
                }
              }
          });

      })
    $(document).on("click", ".window_content_left .live", function () {
          $(".layer").removeClass("hide"); 
          var data={};
          var html = template('live_types',data);
          document.getElementById('layer-box').innerHTML = html;
      })
    $(document).on("click",".playbutton button",function(){
        var checkurl ='/OpenAPI/V1/Qiniu/checkObsStream';
        $.ajax({
          url:checkurl,
          type: 'post',
          dataType: 'JSON', 
          data:{roomid:User.curroomnum},
          success: function(data) {
            console.log(data);
            if(data.data){
                $.getScript('/style/meilibo/vzboostyle/js/video.js',function(){
                  videojs.options.flash.swf = "/style/meilibo/vzboostyle/js/video-js.swf";
                  var html='<video id="example_video_1" class="video-js" controls preload="auto"  autoplay="autoplay"  data-setup="{}"><source src="'+data.data.ORIGIN+'" type="rtmp/flv"></video>';
                  $(".play_video").html(html);
                });
            }
            if(data.code == 0){
              Fn.start_play();
            }else{
                alert(data.msg);
            }
          }
       });
    })

    $(document).on("click",".live_types .topic",function(){
        if(!$(this).hasClass("active")){
            $(this).addClass("active")
            $(".live_types .topic ul").removeClass("hide");
        }else{
            $(this).removeClass("active")
            $(".live_types .topic ul").addClass("hide");
        }
        
    })

    $(document).on("click",".live_types .topic ul li",function(){
        var val=$(this).text();
        $(".live_types .topic input").val(val);
        $(".live_types .topic ul").addClass("hide");
    })

    $(document).on("click",".live_types .types",function(){
        if(!$(this).hasClass("active")){
            $(this).addClass("active")
            $(".live_types .types ul").removeClass("hide");
        }else{
            $(this).removeClass("active")
            $(".live_types .types ul").addClass("hide");
        }
        
    })
    //设置直播类型
    $(document).on("click",".live_types .types ul li",function(){
        var val=$(this).text();
        $(".live_types .types input").val(val);
        $(".live_types .types ul").addClass("hide");
        $(".live_types .type_key").removeClass("hide");
        $(".live_types .type_key input").attr("placeholder","请设置"+val);

    })

    //点击头像显示个人信息
    $(".operate-content .account-box .avatar").click(function(){
        $(".operate-content .account-box .account-info-box").removeClass("hide");
        $(".page-window").click(function(e){
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

    //点击下载
    $(".operate-content .account-box .goDownloads>a").click(function(){
        $(".mm-downloads-wrap").removeClass("hide");
        $(".page-window").click(function(e){
            var target=$(e.target);
            if(!target.is('.goDownloads')&&!target.is('.goDownloads *')&&!target.is('.mm-downloads-wrap')&&!target.is('.mm-downloads-wrap *')) {
               $(".mm-downloads-wrap").addClass("hide");
            }
        })
    })

    //管理员操作：禁言、踢人、设为管理员、取消管理员
    $(document).on('click', '.profile-operation button', function() {
           var data = $(this).data();
            Fn.manageOperation({
                _method_: 'Manage',
                _type_: data.type,
                managed_user_id: data.id,
                managed_user_name: data.name
            });
    });
    //管理员列表
    $(document).on("click",".tab-manger",function(){
      if(User){
        Fn.getAdmin();
      }else{
        alert("会话到期，请重新登录");
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
})