var CommonFn={
	gamerank:function(){
		var gamearr=new Array();
		var html='',div='',ten_html='',elv_html='',self='',money='';
		$.ajax({
			type:"get",
			url:"http://demo.meilibo.net/OpenAPI/v1/APIgame/game_rank",
			jsonp: 'callback', 
	        dataType: 'JSONP', 
	        jsonpCallback:'game_rank',
			data:{uid:uid,token:token},
			success:function(rep){
				console.log(rep);
				var arr=rep.data;
				if(arr.length==0){
					$(".mui-table-view").html("<div><img src='http://ga.meilibo.net/Public/rank/images/0_big.jpg'>什么都没有哦，可能是网络问题</div>")
				}else{
					for(var i=0;i<arr.length;i++){
						if(arr[i].money >= 10000 && arr[i].money <= 100000000){
							money=(arr[i].money/10000).toFixed(1)+"万";
						}else if(arr[i].money > 100000000){
							money=(arr[i].money/100000000).toFixed(1)+"亿";
						}else{
							money=arr[i].money;
						}
						if(uid==arr[i].uid){
							self=htmlFn(avatar,"已上榜",birthday,city,intro,nickname,uid,level,fans)
						}else{
							self=htmlFn(avatar,"未上榜",birthday,city,intro,nickname,uid,level,fans);
						}
						if(i>=0&&i<3){
						    html+=div+htmlFn2(arr[i].avatar,arr[i].nickname,money,'<div class="addkey">+5</div>',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].uid,arr[i].level,arr[i].fans,arr[i].is_attention);
						}
				    	if(i>2&&i<10){
				    		ten_html+=htmlFn3(' ',(i+1),arr[i].avatar,arr[i].nickname,money,'<div class="addkey">+5</div>',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].uid,arr[i].level,arr[i].fans,arr[i].is_attention);
				    	}
				    	if(i>=10){
				    		elv_html+=htmlFn3("rank_center",(i+1),arr[i].avatar,arr[i].nickname,money,'',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].uid,arr[i].level,arr[i].fans,arr[i].is_attention);
				    	}
					}
				    $(".mui-table-view").append(self);
				    $(".mui-table-view").append("<li class='rank_li'>"+html+"</li>");
				    $(".mui-table-view").append(ten_html);
				    $(".mui-table-view").append(elv_html);
				}
				
			}
		})
	},
	charmrankDay:function(){
		var gamearr=new Array();
		var html='',div='',ten_html='',elv_html='',self='',money='';
		$.ajax({
			type:"get",
			url:"http://demo.meilibo.net/OpenAPI/v1/APIgame/charmListDay",
			jsonp: 'callback', 
	        dataType: 'JSONP', 
	        jsonpCallback:'callback',
			data:{uid:uid,token:token},
			success:function(rep){
				console.log(rep);
				var arr=rep.data;
				if(arr.length==0){
					$("#item1mobile .mui-table-view").html("<div><img src='http://ga.meilibo.net/Public/rank/images/0_big.jpg'>什么都没有哦，可能是网络问题</div>")
				}else{
					for(var i=0;i<arr.length;i++){
						if(arr[i].coin >= 10000 && arr[i].coin <= 100000000){
							money=(arr[i].coin/10000).toFixed(1)+"万";
						}else if(arr[i].coin > 100000000){
							money=(arr[i].coin/100000000).toFixed(1)+"亿";
						}else{
							money=arr[i].coin;
						}
						if(uid==arr[i].touid){
							self=htmlFn(avatar,"已上榜",birthday,city,intro,nickname,uid,level,fans)
						}else{
							self=htmlFn(avatar,"未上榜",birthday,city,intro,nickname,uid,level,fans);
						}
						if(i>=0&&i<3){
						    html+=div+htmlFn2(arr[i].avatar,arr[i].nickname,money,'<div class="addkey">+5</div>',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].touid,arr[i].level,arr[i].fans,arr[i].is_attention);
						}
				    	if(i>2&&i<10){
				    		ten_html+=htmlFn3(' ',(i+1),arr[i].avatar,arr[i].nickname,money,'<div class="addkey">+5</div>',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].touid,arr[i].level,arr[i].fans,arr[i].is_attention);
				    	}
				    	if(i>=10){
				    		elv_html+=htmlFn3("rank_center",(i+1),arr[i].avatar,arr[i].nickname,money,'',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].touid,arr[i].level,arr[i].fans,arr[i].is_attention);
				    	}
					}
				    $("#item1mobile .mui-table-view").append(self);
				    $("#item1mobile .mui-table-view").append("<li class='rank_li'>"+html+"</li>");
				    $("#item1mobile .mui-table-view").append(ten_html);
				    $("#item1mobile .mui-table-view").append(elv_html);
				}
				
			}
		})
	},
	charmrankTotal:function(){
		var gamearr=new Array();
		var html='',div='',ten_html='',elv_html='',self='',money='';
		$.ajax({
			type:"get",
			url:"http://demo.meilibo.net/OpenAPI/v1/APIgame/charmListTotal",
			// jsonp: 'callback', 
	        dataType: 'JSON', 
	        // jsonpCallback:'callback_Total',
			data:{uid:uid,token:token},
			success:function(rep){
				console.log(rep);
				var arr=rep.data;
				if(arr.length==0){
					$("#item2mobile .mui-table-view").html("<div><img src='http://ga.meilibo.net/Public/rank/images/0_big.jpg'>什么都没有哦，可能是网络问题</div>")
				}else{
					for(var i=0;i<arr.length;i++){
						if(arr[i].coin >= 10000 && arr[i].coin <= 100000000){
							money=(arr[i].coin/10000).toFixed(1)+"万";
						}else if(arr[i].coin > 100000000){
							money=(arr[i].coin/100000000).toFixed(1)+"亿";
						}else{
							money=arr[i].coin;
						}
						if(uid==arr[i].touid){
							self=htmlFn(avatar,"已上榜",birthday,city,intro,nickname,uid,level,fans)
						}else{
							self=htmlFn(avatar,"未上榜",birthday,city,intro,nickname,uid,level,fans);
						}
						if(i>=0&&i<3){
						    html+=div+htmlFn2(arr[i].avatar,arr[i].nickname,money,' ',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].touid,arr[i].level,arr[i].fans,arr[i].is_attention);
						}
				    	if(i>2){
				    		elv_html+=htmlFn3("rank_center",(i+1),arr[i].avatar,arr[i].nickname,money,'',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].touid,arr[i].level,arr[i].fans,arr[i].is_attention);
				    	}
					}
				    $("#item2mobile .mui-table-view").append(self);
				    $("#item2mobile .mui-table-view").append("<li class='rank_li'>"+html+"</li>");
				    $("#item2mobile .mui-table-view").append(ten_html);
				    $("#item2mobile .mui-table-view").append(elv_html);
				}
				
			}
		})
	},
	playrankDay:function(){
		var gamearr=new Array();
		var html='',div='',ten_html='',elv_html='',self='',money='';
		$.ajax({
			type:"get",
			url:"http://demo.meilibo.net/OpenAPI/v1/APIgame/dedicateListDay",
			jsonp: 'callback', 
	        dataType: 'JSONP', 
	        jsonpCallback:'callback',
			data:{uid:uid,token:token},
			success:function(rep){
				console.log(rep);
				var arr=rep.data;
				if(arr.length==0){
					$("#item1mobile .mui-table-view").html("<div><img src='http://ga.meilibo.net/Public/rank/images/0_big.jpg'>什么都没有哦，可能是网络问题</div>")
				}else{
					for(var i=0;i<arr.length;i++){
						if(arr[i].coin >= 10000 && arr[i].coin <= 100000000){
							money=(arr[i].coin/10000).toFixed(1)+"万";
						}else if(arr[i].coin > 100000000){
							money=(arr[i].coin/100000000).toFixed(1)+"亿";
						}else{
							money=arr[i].coin;
						}
						if(uid==arr[i].uid){
							self=htmlFn(avatar,"已上榜",birthday,city,intro,nickname,uid,level,fans)
						}else{
							self=htmlFn(avatar,"未上榜",birthday,city,intro,nickname,uid,level,fans);
						}
						if(i>=0&&i<3){
						    html+=div+htmlFn2(arr[i].avatar,arr[i].nickname,money,'<div class="addkey">+5</div>',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].uid,arr[i].level,arr[i].fans,arr[i].is_attention);
						}
				    	if(i>2&&i<10){
				    		ten_html+=htmlFn3(' ',(i+1),arr[i].avatar,arr[i].nickname,money,'<div class="addkey">+5</div>',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].uid,arr[i].level,arr[i].fans,arr[i].is_attention);
				    	}
				    	if(i>=10){
				    		elv_html+=htmlFn3("rank_center",(i+1),arr[i].avatar,arr[i].nickname,money,'',arr[i].birthday,arr[i].city,arr[i].intro,arr[i].nickname,arr[i].uid,arr[i].level,arr[i].fans,arr[i].is_attention);
				    	}
					}
				    $("#item1mobile .mui-table-view").append(self);
				    $("#item1mobile .mui-table-view").append("<li class='rank_li'>"+html+"</li>");
				    $("#item1mobile .mui-table-view").append(ten_html);
				    $("#item1mobile .mui-table-view").append(elv_html);
				}
				
			}
		})
	},
}

function htmlFn(avatar,text,birthday,city,intro,nickname,uid,level,fans){
	var html='<li class="rank_li"><div class="user1"><img  class="avatar" avatar="'+avatar+'" birthday="'+birthday+'" city="'+city+'" intro="'+intro+'" nickname="'+nickname+'" uid="'+uid+'" level="'+level+'" fans="'+fans+'" onerror="this.src=\'http://ga.meilibo.net/Public/rank/images/0_big.jpg\'" src="http://demo.meilibo.net/'+avatar+'"><span class="name">我</span></div><div class="user2"><span>'+text+'</span></div><div class="user3"><i></i></div></li>';
	return html;
}
function htmlFn2(avatar,nickname,coin,div,birthday,city,intro,nickname,uid,level,fans,is_attention){
	var html='<div><div class="user_pic"><img  onerror="this.src=\'http://ga.meilibo.net/Public/rank/images/0_big.jpg\'" src="http://demo.meilibo.net/'+avatar+'"><i class="bg avatar" avatar="'+avatar+'" birthday="'+birthday+'" city="'+city+'" intro="'+intro+'" nickname="'+nickname+'" uid="'+uid+'" level="'+level+'" fans="'+fans+'" is_attention="'+is_attention+'"></i></div>'+div+'<div class="username">'+nickname+'</div><div class="usernum">'+coin+'</div></div>';
	return html;
}
function htmlFn3(center,num,avatar,nickname,coin,div,birthday,city,intro,nickname,uid,level,fans,is_attention){
	var html='<li class="rank_li list '+center+'"><div class="left"><div class="num">NO.'+num+'</div>'+div+'</div><div class="center"><img class="avatar" avatar="'+avatar+'" birthday="'+birthday+'" city="'+city+'" intro="'+intro+'" nickname="'+nickname+'" uid="'+uid+'" level="'+level+'" fans="'+fans+'" is_attention="'+is_attention+'" onerror="this.src=\'http://ga.meilibo.net/Public/rank/images/0_big.jpg\'"src="http://demo.meilibo.net/'+avatar+'">'+nickname+'</div><div class="right">'+coin+'</div></li>';
	return html;
}


$(function(){
$(document).on("touchend",".avatar",function(){
	var avatar=$(this).attr("avatar"),birthday=$(this).attr("birthday"),city=$(this).attr("city"),intro=$(this).attr("intro"),nickname=$(this).attr("nickname"),uid=$(this).attr("uid"),level=$(this).attr("level"),fans=$(this).attr("fans"),is_attention=$(this).attr("is_attention");
	$(".person_info").removeClass("none");
	var data={
		avatar:avatar,
		birthday:birthday,
		city:city,
		intro:intro,
		nickname:nickname,
		uid:uid,
		level:level,
		fans:fans,
		is_attention:is_attention
	};
	var html = template('info_box', data);
    document.getElementById('person_info').innerHTML = html;
})
$(document).on("touchend",".info_line",function(){
	$(".person_info").addClass("none");
	$(".info_box").remove();
})
})