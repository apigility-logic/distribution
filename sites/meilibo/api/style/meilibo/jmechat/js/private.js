var appkey = "d6c4ba516dc1702bf597fd3d";
var random_str = "022cd9fd995849b58b3ef0e943421ed9";
var timestamp = new Date().getTime();
var secret = "b67b8a6bc75420df78cb3799";
var signature =$.md5("appkey="+appkey+"&timestamp="+timestamp+"&random_str="+random_str+"&key="+secret);
var target_username='';//接收消息者username
var target_nickname='';//接收消息者nickname
var MEDIA_URL = "http://media.file.jpush.cn/";
var new_User=new Array();
var add_User=new Array();
var chat_recordArr = new Array();//聊天记录
var myusername='meilibo';
var pagesize=20;
var recordScrollHeight = 0;
var thisNewDate = 0;
var thisOldDate = 0;
var promptNum=0;
window.JIM = new JMessage({
    debug : false,
});
JIM.onDisconnect(function(){
	console.log("【disconnect】");
}); //异常断线监听
$(function(){

$(document).on("click",".private_box_chat .chatMiniRoot .fold_cont",function(){
	$(this).parent().addClass("hide");
	$(".webim_chat_window").removeClass("hide");
})

$(document).on("click",".private_dialogue_box .chat_head .ficon_close",function(){
	$(".private_box_chat .chatMiniRoot").removeClass("hide");
	$(".webim_chat_window").addClass("hide");
})


$(document).on("click",".webim_contacts_mod .contacts",function(){
	var self=$(this);
	var data=self.data();
	self.addClass("active").siblings().removeClass("active");
	$(".private_dialogue_box .chat_title").html(data.name);
	$(this).find(".W_new_count").addClass("hide").html("0");
	$(".private_dialogue_cont .private_dialogue_cont_list").each(function(){
		var datac=$(this).data();
		if(data.id==datac.id){
			$(this).removeClass("hide").siblings().addClass("hide");
		}
	})

})

$(document).on("click",".webim_contacts_mod .ficon_close",function(){
	var self=$(this);
	var data=self.parents(".contacts").data();
	self.parents(".contacts").remove();
	delFriend(self,data);
})

$(document).on("keyup",".WB_search_s .W_input",function(e){
	e = e || window.event;
	var val=$(".WB_search_s .W_input").val();
	var html='';
	if($(this).val()!=""){
		$("#webim_contacts_list .contacts").each(function(i){
			var data=$("#webim_contacts_list .contacts").eq(i).data();
			var str=Trim(data.name,'g');
				if(str.indexOf(val) >= 0){
					html+='<li class="contacts SW_fun_bg clearfix" data-id="'+data.id+'">'
		  						+'<img class="userimg fl" src="'+data.img+'" width="30" height="30"/>'
		  						+'<p class="name fl W_autocut">'+data.name+'</p>'
			  					+'</li>';
					$(".webim_serch_mod .webim_contacts_list").html(html);
					$(".webim_serch_mod").removeClass("hide");
					$(".webim_contacts_man").addClass("hide");
				}
		})
	}else{
		$(".webim_serch_mod .webim_contacts_list").html("");
		$(".webim_serch_mod").addClass("hide");
		$(".webim_contacts_man").removeClass("hide");
	}
})

$(document).on("keydown",".sendbox_area .W_input",function(event){
	event=document.all?window.event:event;
	if((event.keyCode || event.which)==13){
		$(".private_dialogue_cont_list").each(function(){
			if(!$(this).hasClass("hide")){
				var data=$(this).data();
				target_username=myusername+data.id;
				target_nickname=data.name;
			}
		})
		
		sendSingleMsg(target_username,target_nickname)
    }

})

$(document).on("click",".sendbox_mod_r .sendbutton",function(){
	$(".private_dialogue_cont_list").each(function(){
			if(!$(this).hasClass("hide")){
				var data=$(this).data();
				target_username=myusername+data.id;
				target_nickname=data.name;
			}
		})
	sendSingleMsg(target_username,target_nickname)
})

$(document).on("click",".privateChat",function(){
	if(User.isLogin){
		var self=$(this);
		var data=self.data();
		var data_arr=new Array();
		var _this='';
		var datai='';
		$(".layer").addClass("hide");
	    $("#layer-box").html("");
		$("#webim_contacts_list .contacts").each(function(){
		    _this=$(this);
			datai=_this.data();
			data_arr.push(datai.id);
		})
		addUser(_this,datai,data,data_arr);
	}else{
		Fn.nulogin();
	}
	
})

$(".private_dialogue_cont_list").niceScroll({  
		cursorcolor:"#afafaf",  
		cursoropacitymax:1,  
		touchbehavior:false,  
		cursorwidth:"5px",  
		cursorborder:"0",  
		cursorborderradius:"5px"  
}); 

$(".webim_contacts_mod").niceScroll({  
		cursorcolor:"#afafaf",  
		cursoropacitymax:1,  
		touchbehavior:false,  
		cursorwidth:"5px",  
		cursorborder:"0",  
		cursorborderradius:"5px"  
}); 

$(document).on("click",".chat_record_button",function(){
	var data=$(this).data();
	add_User=[];
	$(".webim_contacts_mod .contacts").each(function(){
		var datag=$(this).data();
		add_User.push(datag);
	})
	console.log(data);
	console.log(add_User);
	getChatMsg(data.id,data.myname,data.name);
})

setTimeout(function(){
	init();
},3000)
})
function init(){
	JIM.init({
        "appkey":appkey,
        "random_str":random_str,
        "signature":signature,
        "timestamp":timestamp
    }).onSuccess(function(data) {
    	login();
        console.log('success:' + JSON.stringify(data));
    }).onFail(function(data) {
        console.log('error:' + JSON.stringify(data))
    });
}

function loginOut(){
	JIM.loginOut();
}

function login(){
	JIM.login({
	    'username' :myusername+User.id,
	    'password' :myusername+User.id,
	}).onSuccess(function(data) {
		console.log(data);
		$(".webim_contacts_mod .contacts").each(function(){
			var datag=$(this).data();
			new_User.push(datag);
			console.log(new_User);
		})
		getUser();
		JIM.onMsgReceive(function(data) {//聊天消息实时监听
		    console.log(data);
		    var username = data.messages[0].content.from_id;
		    getSelfInfo( data, username , 1) ;
		    onMsgprompt(data);
		});

		JIM.onEventNotification(function(data) {
            console.log('event_receive: ' + JSON.stringify(data));
        });
		
		JIM.onSyncConversation(function(data) {//离线消息同步监听
            console.log('event_receive: ' + JSON.stringify(data));
        });
	}).onFail(function(data){
	  	console.log(data);
	}).onTimeout(function(data) {
        console.log('timeout:' + JSON.stringify(data));
    });
}

function onMsgprompt(data){
	if(!$(".private_box_chat .chatMiniRoot").hasClass("hide")){
		promptNum++;
		$(".chatMiniRoot .fold_font").html("你有"+promptNum+"条新私信")    	
    }else{
    	$(".webim_contacts_mod .contacts").each(function(i){
    		var udata=$(this).data();
    		if(!$(this).hasClass("active")){
    			if(udata.username == data.messages[0].content.from_id){
	    			$(this).find(".W_new_count").removeClass("hide");
	    			$(this).find(".W_new_count").html(++promptNum);
	    		}
    		}
    	})
    }	
}


function getUser(){//获取会话列表
	JIM.getConversation().onSuccess(function(data) {
      	console.log("user",data);
      	var messages = "";
      	for (var key in data['conversations']) {
    		getSelfInfo( messages, data['conversations'][key].name , 2);

      	}
   	}).onFail(function(data) {
      console.log("error",data);
   	});
}

function getSelfInfo( messages, username , type) {//获取个人信息
        JIM.getUserInfo({
            'username' : username
        }).onSuccess(function(data) {
            if( data.user_info.avatar ){
	            if( type == 1 ){
	            	var g_add_User=[];
					$(".webim_contacts_mod .contacts").each(function(){
						var datag=$(this).data();
						g_add_User.push(datag.username);
					})
					if(g_add_User.indexOf(data.user_info.username) < 0){
						getaddUser( data );
					}
	            	ReceiveMessage(data, messages );
	            }
	            if( type == 2 ){
	            	getaddUser( data );
	            }
            }
        }).onFail(function(data) {
            console.log('error:' + JSON.stringify(data));
        });
		
    }

function getaddUser(data){
	var str_num=data.user_info.username;
	var userid=parseInt(str_num.replace(/[^0-9]/ig,""));
	for(var key in new_User){
		if(new_User[key]['id'].indexOf(userid) < 0){
			var html='<li class="contacts SW_fun_bg clearfix" id="user'+userid+'" data-id="'+userid+'" data-name="'+data.user_info.nickname+'" data-img="'+MEDIA_URL+data.user_info.avatar+'" data-username="'+myusername+userid+'">'
					+'<img class="userimg fl" src="'+MEDIA_URL+data.user_info.avatar+'" width="30" height="30"/>'
					+'<p class="name fl W_autocut">'+data.user_info.nickname+'</p>'
					+'<span class="W_new_count fr hide">1</span>'
					+'<a class="ficon_close S_ficon fr " href="javascript:;">X</a>'
				+'</li>';
			$("#webim_contacts_list").prepend(html);

			var chatHtml='<div class="private_dialogue_cont_list hide" id="private'+userid+'" data-id="'+userid+'" data-name="'+data.user_info.nickname+'">'
	  						+'<p class="private_dialogue_more">'
		  						+'<a href="javascript:void(0)" class="S_link1 chat_record_button" data-id="'+userid+'" data-myname="'+myusername+User.id+'" data-name="'+myusername+userid+'">查看更多消息</a>'
		  					+'</p>'
	  					+'</div>';
	  			$(".private_dialogue_cont").prepend(chatHtml);
	  			$(".private_dialogue_cont_list").niceScroll({  
						cursorcolor:"#afafaf",  
						cursoropacitymax:1,  
						touchbehavior:false,  
						cursorwidth:"5px",  
						cursorborder:"0",  
						cursorborderradius:"5px"  
				}); 
				
		}
	}
}

function getChatMsg(id,username,targetuser){//获取聊天记录
	console.log(username,targetuser);
	if(!chat_recordArr.hasOwnProperty(targetuser)){
		$.ajax({
			url:'/OpenAPI/v1/jmessage/getChatMsgForWeb',
			type:'POST',
			datatype:'json',
			data:{
				'username':username,
				'targetuser':targetuser,
			},
			success:function(rep){
				console.log(rep);
				chat_recordArr[targetuser]=rep.data[targetuser];
				chat_recordArr[targetuser].page=1;
				chat_recordArr[targetuser].pageCount=Math.ceil(chat_recordArr[targetuser].total/pagesize);
				console.log(chat_recordArr);
				chatRecord(id,chat_recordArr[targetuser].page,pagesize);
			}
		})
	}else{
			chat_recordArr[targetuser].page++;
			if(chat_recordArr[targetuser].page <= chat_recordArr[targetuser].pageCount){
				var index=chat_recordArr[targetuser].page * pagesize - chat_recordArr[targetuser].total;
				chatRecord(id,chat_recordArr[targetuser].page,index > 0 ? pagesize - index : pagesize)
			}else{
				$("#private"+id+" .chat_record_button").html("没有更多消息了");
				return;
			}
	}
	
}

function pagination( name, page, size) {
    return chat_recordArr[name].messages.reverse().slice( ((page-1)*size), page*size );
}

function chatRecord(id,page, size){
		console.log(page,size);
		recordScrollHeight = $("#private"+id)[0].scrollHeight;
        var messages = pagination(myusername+id, page, size);
        var username=myusername+User.id;
        console.log(messages);
		for (var key in messages) {
			var message = messages[key];
	    	if( message.from_id == username ){
	    		addSend(id,message);
	    	}else{
	    		addReceive(id,message);
	    	}
		}
}

function addSend(id,data ){
	var chatmsg = data.msg_body.text;
	var nowDate = data.msg_ctime;
	var time = "";
	var html='';
	if( ( thisOldDate - nowDate ) > 300000 ){
		time = new Date(data.msg_ctime).Format("yyyy-MM-dd hh:mm");
	}
	thisOldDate = nowDate;
	if(time){
		html='<fieldset class="private_dialogue_prompt"><legend class="prompt_font">'+time+'</legend></fieldset>';
	}
		html+='<div class="msg_bubble_list  bubble_r">'
					+'<div class="bubble_mod clearfix">'
						+'<div class="bubble_user"><img src="'+User.avatar+'" width="30" height="30"></div>'
						+'<p class="bubble_name W_autocut">'+User.nickname+'</p>'
						+'<div class="bubble_box SW_fun">'
							+'<div class="bubble_cont">'
								+'<div class="bubble_arrow"><div class="W_arrow_bor  W_arrow_bor_r"><i></i><em></em></div></div>'
								+'<div class="bubble_main clearfix">'
									+'<div class="cont"><p class="page">'+chatmsg+'</p></div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
				+'</div>';
	$("#private"+id+" > p").after(html);
}

function addReceive(id,data){
	var chatmsg=data.msg_body.text;
	var nowDate = data.msg_ctime;
	var time = "";
	var html='';
	if( ( thisOldDate - nowDate) > 300000 ){
		time = new Date(data.msg_ctime).Format("yyyy-MM-dd hh:mm");
	}
	thisOldDate = nowDate;
	for(var key in add_User){
		if(id==add_User[key]['id']){
			if(time){
				html='<fieldset class="private_dialogue_prompt"><legend class="prompt_font">'+time+'</legend></fieldset>';
			}
				html+='<div class="msg_bubble_list  bubble_l">'
						+'<div class="bubble_mod clearfix"> '
							+'<div class="bubble_user"><img src="'+add_User[key]['img']+'" width="30" height="30"></div>'
							+'<p class="bubble_name W_autocut">'+add_User[key]['name']+'</p>'
							+'<div class="bubble_box SW_fun">'
								+'<div class="bubble_cont">'
									+'<div class="bubble_arrow"><div class="W_arrow_bor  W_arrow_bor_l"><i></i><em></em></div></div>'
									+'<div class="bubble_main clearfix">'
										+'<div class="cont"><p class="page">'+chatmsg+'</p></div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>';
			$("#private"+id+" > p").after(html);
		}
			
	}
}

function addUser(obj,datai,data,arr){
	if(arr.indexOf(data.id) < 0){
		var html='<li class="contacts SW_fun_bg clearfix" id="user'+data.id+'" data-id="'+data.id+'" data-name="'+data.name+'" data-img="'+data.avatar+'" data-username="'+myusername+data.id+'">'
					+'<img class="userimg fl" src="'+data.avatar+'" width="30" height="30"/>'
					+'<p class="name fl W_autocut">'+data.name+'</p>'
					+'<span class="W_new_count fr hide">1</span>'
					+'<a class="ficon_close S_ficon fr " href="javascript:;">X</a>'
				+'</li>';
	    $("#webim_contacts_list").prepend(html);
	    $(".private_box_chat .chatMiniRoot").addClass("hide");
		$(".webim_chat_window").removeClass("hide");
		$("#user"+data.id).addClass("active").siblings().removeClass("active");
		$(".private_dialogue_box .chat_title").html(data.name);

		var chatHtml='<div class="private_dialogue_cont_list" id="private'+data.id+'" data-id="'+data.id+'" data-name="'+data.name+'">'
  						+'<div class="WB_empty">还没收到私信哦</div>'
  					+'</div>';
  			$(".private_dialogue_cont").prepend(chatHtml);
  		$("#private"+data.id).siblings().addClass("hide");
  		$(".private_dialogue_cont_list").niceScroll({  
				cursorcolor:"#afafaf",  
				cursoropacitymax:1,  
				touchbehavior:false,  
				cursorwidth:"5px",  
				cursorborder:"0",  
				cursorborderradius:"5px"  
		}); 
	}else{
		$(".private_box_chat .chatMiniRoot").addClass("hide");
		$(".webim_chat_window").removeClass("hide");
		$("#webim_contacts_list .contacts").removeClass("active");
		$(".private_dialogue_box .chat_title").html(data.name);
		obj.addClass("active");
		$(".private_dialogue_cont .private_dialogue_cont_list").each(function(){
			var datac=$(this).data();
			if(datai.id==datac.id){
				$(this).removeClass("hide").siblings().addClass("hide");
			}
		})
		
	}
}

function sendSingleMsg(target_username,target_nickname) {//发生消息
	var content=$(".sendbox_area .W_input").val();
	console.log(target_username,target_nickname);
    JIM.sendSingleMsg({
        'target_username' : target_username,
		'target_nickname' : target_nickname,
        'content' :content,
        'appkey' : appkey
    }).onSuccess(function(data) {
        console.log('success:' + JSON.stringify(data));
        sendMsg();
    }).onFail(function(data) {
        console.log('error:' + JSON.stringify(data));
    });
}


function sendMsg(){//发送消息
	var nowDate = Date.parse(new Date());
    var time = "";
    var html='';
	var chatmsg=$(".sendbox_area .W_input").val();
	if(Trim(chatmsg,'g').length > 0){
		$(".private_dialogue_cont .private_dialogue_cont_list").each(function(i){
		var nid=$(this).attr("id");
		if(!$(this).hasClass("hide")){
			if(( nowDate - thisNewDate ) > 300000){
				time = new Date().Format("yyyy-MM-dd hh:mm");
    			thisNewDate = nowDate;
    			html='<fieldset class="private_dialogue_prompt"><legend class="prompt_font">'+time+'</legend></fieldset>';
			}
				html+='<div class="msg_bubble_list  bubble_r">'
					+'<div class="bubble_mod clearfix">'
						+'<div class="bubble_user"><img src="'+User.avatar+'" width="30" height="30"></div>'
						+'<p class="bubble_name W_autocut">'+User.nickname+'</p>'
						+'<div class="bubble_box SW_fun">'
							+'<div class="bubble_cont">'
								+'<div class="bubble_arrow"><div class="W_arrow_bor  W_arrow_bor_r"><i></i><em></em></div></div>'
								+'<div class="bubble_main clearfix">'
									+'<div class="cont"><p class="page">'+chatmsg+'</p></div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>'
				+'</div>';
			if($(this).children(".WB_empty").length > 0){
				$(this).html(html);
			}else{
				$(this).append(html);
			}
			var scrollheight=$("#"+nid)[0].scrollHeight; 
			console.log(scrollheight);
			$("#"+nid).getNiceScroll(0).doScrollTop(scrollheight,0)
			$("#"+nid).getNiceScroll(0).doScrollTop(scrollheight,0);
			$(".sendbox_area .W_input").val("").focus();
		}
	  })
	}else{
    	return;
    }
}

function ReceiveMessage(userinfo, data){//接收消息
	var str_num=userinfo.user_info.username;
	var userid="private"+parseInt(str_num.replace(/[^0-9]/ig,""));
	var msg='';
    if(data.messages[0].content.msg_type){
	  $(".private_dialogue_cont .private_dialogue_cont_list").each(function(i){
		var nid=$(this).attr("id");
		 if(nid == userid){
				 	msg='<div class="msg_bubble_list  bubble_l">'
						+'<div class="bubble_mod clearfix"> '
							+'<div class="bubble_user"><img src="'+MEDIA_URL+userinfo.user_info.avatar+'" width="30" height="30"></div>'
							+'<p class="bubble_name W_autocut">'+userinfo.user_info.nickname+'</p>'
							+'<div class="bubble_box SW_fun">'
								+'<div class="bubble_cont">'
									+'<div class="bubble_arrow"><div class="W_arrow_bor  W_arrow_bor_l"><i></i><em></em></div></div>'
									+'<div class="bubble_main clearfix">'
										+'<div class="cont"><p class="page">'+data.messages[0].content.msg_body.text+'</p></div>'
									+'</div>'
								+'</div>'
							+'</div>'
						+'</div>'
					+'</div>';
				if($("#"+nid).children(".WB_empty").length > 0){
					$("#"+nid).html(msg);
				}else{
					$("#"+nid).append(msg);
				}
				var scrollheight=$("#"+nid)[0].scrollHeight; 
				$("#"+nid).getNiceScroll(0).doScrollTop(scrollheight,0)
				$("#"+nid).getNiceScroll(0).doScrollTop(scrollheight,0);
		 }
	  })
	}
}

function delFriend(obj,data){//删除好友
  if(obj.parents(".contacts").hasClass("active")){
		var wdata=$(".webim_contacts_mod .contacts").first().data();
		$(".webim_contacts_mod .contacts").first().addClass("active");
		$(".private_dialogue_cont .private_dialogue_cont_list").each(function(){
			var datac=$(this).data();
			if(data.id==datac.id){
				$(this).remove();
			}
			if(wdata.id==datac.id){
				$(this).removeClass("hide").siblings().addClass("hide");
			}
		})
	}else{
		$(".private_dialogue_cont .private_dialogue_cont_list").each(function(){
			var datac=$(this).data();
			if(data.id==datac.id){
				$(this).remove();
			}
		})
	}
}


function Trim(str,is_global){
	var result;
	result = str.replace(/(^\s+)|(\s+$)/g,"");
	if(is_global.toLowerCase()=="g")
	{
	    result = result.replace(/\s/g,"");
	 }
	return result;
}

Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}


// function scrollFuncAdd(event){
// 	event.scrollTop( event[0].scrollHeight - recordScrollHeight );
// }