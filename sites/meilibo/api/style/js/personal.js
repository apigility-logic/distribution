/*加载函数*/
$(function(){
	$(".bigimg li").OverChange();
	$('.level-1').hover(function(){
		$(this).find('.prompt').fadeIn();
	},function(){
		$(this).find('.prompt').fadeOut();
	});
	$('.level-1_1').hover(function(){
		$(this).find('.prompt').fadeIn();
	},function(){
		$(this).find('.prompt').fadeOut();
	});
	$('.r-table tr,.gn_item,.top_board li,.chat p,.song_list li,.gift_get li,.song-table tr,.giftBox li,.playerlist li').live("mouseenter",function(){
		$(this).addClass("hover");
	}).live("mouseleave",function(){
		$(this).removeClass("hover");
	});
	$('.bigimg li').hover(function(){
		$(this).find(".bigbg").fadeIn();
		$(this).find(".rechange").fadeIn();
	},function(){
		$(this).find('.bigbg').fadeOut();
		$(this).find(".rechange").fadeOut();
	});
	$('.timelist .on1,.timelist .on2,.timelist .now').hover(function(){
		var position=$(this).offset();
		var rel=$("#wishLayer");
		rel.css({"left":(position.left-60)+"px","top":(position.top+30)+"px"}).show();
	},function(){
		$("#wishLayer").hide();
	});
	$('.aboutme p').hover(function(){
		var position=$(this).position();
		var rel=$("#about-tips");
		rel.css({"left":(position.left)+30+"px","top":(position.top+54)+"px"}).show();
	},function(){
		$("#about-tips").hide();
	});
	$('.tab-info li .m-notice,.tab-info li .m-bgimg,.tab-info li .m-video,.tab-info li .m-move').click(function(){
		 var boxObj=$(this).siblings('div');
		 $('.pop-play').hide();
		 if(boxObj.is(":hidden")){
			 boxObj.show();	 
		 }else{
			 boxObj.hide(); 
		 }
	});
	$('.dh').click(function(){
		$(".dh_table").show();
	});
	$('.tab-info li .p-close').click(function(){$('.pop-play').hide();});
	$('#user_sofa li').live("mouseenter",function(){
		var seatnum=$(this).find('img').attr('seatnum');
		document.onmousemove=function(e){$("#current_sofa").css({"left":mousePosition(e).x+10+"px","top":mousePosition(e).y+"px"}).show();}
		$("#current_sofa").find('span').html(seatnum);	
		
	}).live("mouseleave",function(){
		document.onmousemove=null;
		$("#current_sofa").hide();
		return;	
	});
	$('#user_sofa li').live("click",function(){
		var seatnum=$(this).find('img').attr('seatnum');
		$("#get_sofanum").find('b').html(seatnum);
	})
	$('#user_sofa li').live('click',function(){
		if (parseInt(_show.emceeId) == userInfo.user_id) {
			_alert('主播无法抢座!',3);
			return false;
		}
		var sofaid=$(this).find('span').attr('seatid');
		_show.oldseatnum=$(this).find('img').attr('seatnum');
		$('#sofaid').val(sofaid);
		if($("#get_sofa").is(":hidden")){
			 $("#get_sofa").hide();
			 var sofa_p=$(this).find('span').offset();	
			 $("#get_sofa").css({"left":sofa_p.left-100+"px","top":sofa_p.top-130+"px"}).show();
		}else{$("#get_sofa").hide();}
	});
});