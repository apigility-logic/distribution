/**
*直播间js
*编码utf8
*喵榜直播
*/

var point_val=10,bglance_money=parseInt($(".bglance_money").text());
$(document).on("touchend",".balance .betting small",function(){
    $(this).addClass("active").siblings().removeClass("active");
    point_val=parseInt($(this).attr('data-val'));
})

$(document).on("touchend",".gamepoint1",function(){
    if(!err_true){
        return;
    }
    bglance_money=bglance_money-point_val;
    if(bglance_money<=0){
        bglance_money=0;
        return;
    }
    $(".bglance_money").text(bglance_money)
    GameSocketIO._betTing(1,point_val);
})

$(document).on("touchend",".gamepoint2",function(){
    if(!err_true){
        return;
    }
    bglance_money=bglance_money-point_val;
    if(bglance_money<=0){
        bglance_money=0;
        return;
    }
    $(".bglance_money").text(bglance_money)
    GameSocketIO._betTing(2,point_val);
})

$(document).on("touchend",".gamepoint3",function(){
    if(!err_true){
        return;
    }
    bglance_money=bglance_money-point_val;
    if(bglance_money<=0){
        bglance_money=0;
        return;
    }
    $(".bglance_money").text(bglance_money)
    GameSocketIO._betTing(3,point_val);
})


function getCoin(){
    $.ajax({
        type:"GET",
        url:"http://demo.meilibo.net/OpenAPI/V1/APIgame/getCoin",
        dataType:"json",
        data:{"uid":User.id,"nickname":User.nickname,"token":User.token},
        success:function(data){
            console.log(data);
            if(data.code=='0'){
                getCoinTime();
            }
        }
    })
}


getCoinTime();
var time='',getmoney='';
function getCoinTime(){
    $.ajax({
        type:"GET",
        url:"http://demo.meilibo.net/OpenAPI/V1/APIgame/getCoinTime",
        dataType:"json",
        data:{"uid":User.id,"token":User.token},
        success:function(data){
            console.log(data);
            var minutes='',seconds='',gettime='';
            time=data.data.leave_time;
            getmoney=parseInt(data.data.get_money);
            gettime=setInterval(function(){
                minutes= Math.floor(time/60);
                seconds=time-minutes*60;
                if(seconds<10){
                    seconds="0"+seconds;
                }else{
                    seconds=seconds;
                }
                $(".game_task .label").html("0"+minutes+":"+seconds);
                time--;
                if(time<0){
                    clearInterval(gettime);
                    time=time;
                    $(".game_task .label").html("可领取");
                    $(".game_task .solid").removeClass("addbg");
                }else{
                    $(".game_task .solid").addClass("addbg");
                }
            },1000)
            
        }
    })
}
$(document).on("touchend",".game_task .solid",function(){
    if(time<0){
        getCoin();
        $(".bglance_money").text(bglance_money+getmoney)
        $(".game_task .solid").addClass("addbg");
        if($(".section1_box").hasClass("animate")){
            $(".game_task .small_xx i").addClass("animt2").removeClass("animt1");
        }else{
            $(".game_task .small_xx i").addClass("animt1").removeClass("animt2");
        }
    }
    
})
//游戏历史记录
$(document).on("touchend",".historybutton",function(){
    $(".game_popups").addClass("animt");
    $.ajax({
        url:'http://gapi.meilibo.net/Home/api/openCard_js',
        type:'get',
        jsonp: 'callback', 
        dataType: 'JSONP', 
        jsonpCallback:'callback',
        data:{'roomid':room_id,'gameType':gameType},
        success:function(data){
            console.log(data);
            var info = {
                    gameId:data.gameId,
                    list: data.data,
                };
            var html = template('gametemplate', info);
            document.getElementById('game_popups').innerHTML = html;
        }
    })
})

$(document).on("touchend",".game_line",function(){
    $(".game_popups").removeClass("animt");
    setTimeout(function(){
        $(".history_record,.task_list").remove();
    },500)
    
})

//game_task();
function game_task(){
    $.ajax({
        url:'http://gapi.meilibo.net/Home/Api/game_task',
        type:'get',
        jsonp: 'callback', 
        dataType: 'JSONP', 
        jsonpCallback:'callback',
        data:{'uid':User.id,'token':User.token},
        success:function(data){
            console.log(data);
            var tasklist = {
                    list:data.data,
                };
            var html = template('task', tasklist);
            document.getElementById('game_popups').innerHTML = html;
            $(".game_task .count i").html($(".task_list li.addcss").length);
        }
    })
}
$(document).on("touchend",".game_task .chest",function(){
    $(".game_popups").addClass("animt");
    game_task();
})

$(document).on("touchend",".task_list .task_btn",function(){
    var _this=$(this); 
    $.ajax({
        url:'http://gapi.meilibo.net/Home/Api/get_task',
        type:'get',
        jsonp: 'callback', 
        dataType: 'JSONP', 
        jsonpCallback:'callback',
        data:{'uid':User.id,'token':User.token,'task_id':_this.attr("task_id")},
        success:function(data){
            console.log(data);
            _this.text("已领取");
            _this.parents("li").addClass("addcss");
            $(".game_task .count i").html($(".task_list li.addcss").length);
        }
    })
})













