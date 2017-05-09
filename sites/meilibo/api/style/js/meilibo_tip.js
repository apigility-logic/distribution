/**
 * zoom
 * 继续观看直播
 */
var zoom={
	locationUrl:"/",
	showBox:function(boxid,tipheight){
		if(!boxid){boxid='videobox';}
		if(!tipheight){tipheight=155;}
		var objclientheigth=document.getElementById(boxid).clientHeight;
		$('#'+boxid).height(objclientheigth+2);
		if(objclientheigth<tipheight){ 
			setTimeout(function(){zoom.showBox(boxid,tipheight)},5);
		}
	},
	zoomInBox:function(boxid) {
		if(!boxid){boxid='videobox';}
		var objclientheigth=document.getElementById(boxid).clientHeight;
		$('#'+boxid).animate({height:27},600,function(){});
	},
	zoomOutBox:function(boxid,tipheight){
		if(!boxid){boxid='videobox';}
		if(!tipheight){tipheight=155;}
		var objclientheigth=document.getElementById(boxid).clientHeight;
		$('#'+boxid).height(objclientheigth+2);
		if(objclientheigth<tipheight) {
			setTimeout(function(){zoom.zoomOutBox(boxid,tipheight)},5);	
		}	
	}
}
$(function(){
	var tipheight=155;
	var refurl=document.referrer;
	if(refurl!=""){
		var quickExpr=/(http\:\/\/)?(dsp)([\w.]+)/;
		var match=quickExpr.exec(refurl);
		if(match && match[2]){
			zoom.locationUrl=getParam('url',refurl);
			$('#videobox').show();
			$('#jumplikn').attr('href',zoom.locationUrl);
		}
	}else{return false;}
	$('#zoom').click(function(){
		if(document.getElementById('videobox').clientHeight>=tipheight) { 
			zoom.zoomInBox('videobox');
			$(this).attr('class','off');
		}else{
			zoom.zoomOutBox('videobox',tipheight);
			$(this).attr('class','on');
		}
	});	
})