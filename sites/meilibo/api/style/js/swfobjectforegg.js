function initEgg(){
	var swfVersionStr = "10";
	// To use express install, set to playerProductInstall.swf, otherwise the empty string. 
	var xiSwfUrlStr = "/style/swf/playerProductInstall.swf";
	var flashvars = {};
	flashvars.id      = _show.emceeId;
	flashvars.key     = "<b><font color='#000000' size='10'>   金锄头/500</font>\n<font color='#FF0000' size='10'>中奖几率较高</font></b>|<b><font color='#000000' size='10'>   银锄头250</font>\n<font color='#FF0000' size='10'>中奖几率一般</font></b>|<b><font color='#000000' size='10'>   铁锄头/100</font>\n<font color='#FF0000' size='10'>中奖几率较低</font></b>";
	flashvars.time   = 20;
	flashvars.again   = 0;
	flashvars.gateway = "/gateway";
	flashvars.service = "eggService";
	var params = {};
	params.quality = "high";
	params.bgcolor = "#cccccc";
	params.allowscriptaccess = "always";
	params.allowfullscreen = "true";
	params.wmode="transparent";
	var attributes = {};
	attributes.id = "ShellSmash";
	attributes.name = "ShellSmash";
	attributes.align = "middle";
	swfobject.embedSWF(
	    "/style/swf/zjd.swf", "flashContent", 
	    "845", "400", 
	    swfVersionStr, xiSwfUrlStr, 
	    flashvars, params, attributes);
	// JavaScript enabled so display the flashContent div in case it is not replaced with a swf object.

}
function showEgg(){
/* 	if(_game.eggstatus==1 && _game.eggclosed==1){
		initEgg();
		$("#egg").css("top","250px");
		$("#egg").css("left","250px");
	} */
}
function clearTimer(){
	window.clearTimeout(_game.eggtimer);
}
function closeChipper(){
/* 	$("#egg").css("top","-10000px");
	$("#ShellSmash").remove();
	$("#egg").html('<div id="flashContent" style="text-align:left;"></div>');
	_game.eggtimer=setTimeout("showEgg()",_game.egginterval*60*1000); */
}
function syserror(){
	closeChipper();
	alert("系统繁忙，操作失败！");
}
function nomoney(){
	closeChipper();
	alert("余额不足，请充值！");
}
function notlogin(){
	closeChipper();
	UAC.openUAC(0);
}
function gameClosed(){
	closeChipper();
	alert("游戏已关闭！");
}
