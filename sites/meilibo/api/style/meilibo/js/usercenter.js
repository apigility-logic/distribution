function init(){
	if(parent.UAC!=null)
		parent.UAC.iframeReady = true;
}
//±êÇ©ÇÐ»»
function switchDiv(type){
	if(type == 0){//µÇÂ½
		if($("#div_login").css("display") == "none"){
			$("#div_title").text("ÓÃ»§µÇÂ¼");
			$("#btn_login").attr("class", "cur");
			$("#btn_reg").removeAttr("class");
			$("#div_login").css("display", "");
			$("#div_reg").css("display", "none");
			$("#id_googtip").css("display", "");
		}
		$("#userName").focus();
		$("#switch_type").val("0");
	}else{//×¢²á
		if($("#div_reg").css("display") == "none"){
			$("#div_title").text("ÐÂÓÃ»§×¢²á");
			$("#btn_login").removeAttr("class");
			$("#btn_reg").attr("class", "cur");
			$("#div_login").css("display", "none");
			$("#div_reg").css("display", "");
			$("#id_googtip").css("display", "none");
		}
		$("#reg_userName").focus();
		$("#switch_type").val("1");
	}
}
//µÇÂ¼====================================================================
function login(){
	var userName = $("#userName").val();
	var password = $("#password").val();
	var code = encodeURI($("#login_validateCode").val());
	
	var autoLogin = 0;
	if($("#autoLogin").attr("checked")=="checked"){
		autoLogin = 1;
	}
	if($.trim(userName) == ""){
		alert("用户名不能为空");
		return false;
	}
	if($.trim(password) == ""){
		alert("密码不能为空");
		return false;
	}
	
	if($.trim(code) == ""){
		alert("ÑéÖ¤Âë²»ÄÜÎª¿Õ");
		return false;
	}
	
	var param = {
		userName : userName,
		password : password,
		autoLogin : autoLogin,
		validateCode:code
	};
	$.ajax({
		url : '/index.php/Passport/dologin/',
		data : param,
		success : function(data){
			//document.getElementById('img_authCode1').src='/index.php/Base/verify/v/'+Math.random();
			if(data!=null&&data!=""){
				var obj = eval("("+data+")");
				if(obj.code == "000000"){
					var json = {};
					json.isLogin = "1";
					json.connectType = 0;
					json.connectId = "";
					json.userInfo = {};
					json.userInfo.userName = obj.user.userName;
					json.userInfo.userId = obj.user.userId;
					json.userInfo.nickName = obj.user.nick;
					parent.UAC.showUserInfo(json);
					parent.UAC.closeUAC();
					websocketChat._initConnect();
				}else if(obj.code == "001004"){
					$("#login_info").text("用户名密码错误");
				}else if(obj.code == '001001'){
					$("#login_info").text("001001错误");
				}else{
					$("#login_info").text("其他错误");
				}
			}
		}
	});
}
//ÈËÈËµÇ³ö
function logout(returnUrl){
	RenRen.logout(returnUrl);
}

//===============================================================
//×¢²á============================================================
function showRegInfo(type){
	if(type == "name"){
		$("#li_userName").children("span").attr("class", "cur");
		$("#name_con01").css("display", "none");
		$("#name_con02").css("display", "none");
		$("#name_empty").css("display", "");
		$("#name_false").css("display", "");
		$("#name_false2").css("display", "none");
	}else if(type == "pwd"){
		$("#li_pwd").children("span").attr("class", "cur");
		$("#pwd_con01").css("display", "none");
		$("#pwd_con02").css("display", "none");
		$("#pwd_empty").css("display", "");
		$("#pwd_false").css("display", "");
	}else if(type == "pwd2"){
		$("#li_pwd2").children("span").attr("class", "cur");
		$("#pwd2_con01").css("display", "none");
		$("#pwd2_con02").css("display", "none");
		$("#pwd2_empty").css("display", "");
		$("#pwd2_false").css("display", "");
	}else if(type == "email"){
		$("#li_email").children("span").attr("class", "cur");
		$("#email_con01").css("display", "none");
		$("#email_con02").css("display", "none");
		$("#email_empty").css("display", "");
		$("#email_false").css("display", "");
		$("#email_false2").css("display", "none");
	}else if(type == "mobile"){
		$("#li_mobile").children("span").attr("class", "cur");
		$("#mobile_con01").css("display", "none");
		$("#mobile_con02").css("display", "none");
		$("#mobile_empty").css("display", "");
		$("#mobile_false").css("display", "");
		$("#mobile_false2").css("display", "none");
	}else if(type == "qq"){
		$("#li_qq").children("span").attr("class", "cur");
		$("#qq_con01").css("display", "none");
		$("#qq_con02").css("display", "none");
		$("#qq_empty").css("display", "");
		$("#qq_false").css("display", "");
		$("#qq_false2").css("display", "none");
	}else if(type == "code"){
		$("#li_code").children("span").attr("class", "jmlma1 cur3");
		$("#code_con01").css("display", "none");
		$("#code_con02").css("display", "none");
		$("#code_empty").css("display", "");
		$("#vcode_text").text("ÇëÊäÈëÑéÖ¤Âë");
		$("#code_false").css("display", "");
	}
}
//¼ì²éÓÃ»§ÃûÊÇ·ñÒÑ´æÔÚ
function checkUserNameExist(userName){
	$.ajax({
		url : '/index.php/Passport/checkusername/',
		data : {
			userName : userName,
			rand : Math.random()
		},
		success : function(data){
			if(data == "1"){
				$("#id_isExist").val("1");
			}else{
				$("#id_isExist").val("0");
			}
		}
	});
}
//¼ì²éÓÊÏäÊÇ·ñÒÑ´æÔÚ
function checkEmailExist(email){
	$.ajax({
		url : '/index.php/Passport/checkemail/',
		data : {
			email : email,
			rand : Math.random()
		},
		success : function(data){
			if(data == "1"){
				$("#id_isExist2").val("1");
			}else{
				$("#id_isExist2").val("0");
			}
		}
	});
}
var intervalId = null;
var intervalId2 = null;
function isUserNameExist(){
	if($("#id_isExist").val() != ""){
		if($("#id_isExist").val() == "1"){
			$("#name_con01").css("display", "");
			$("#name_con02").css("display", "none");
			$("#li_userName").children("span").attr("class", "cur2");
			if($("#name_false2").css("display", "")){
					$("#name_con01").text('');
				}
		}else{
			$("#name_con01").css("display", "none");
			$("#name_con02").css("display", "");
			$("#li_userName").children("span").removeAttr("class");
		}
		$("#id_isExist").val("");
		window.clearInterval(intervalId);
	}
}
function isEmailExist(){
	if($("#id_isExist2").val() != ""){
		if($("#id_isExist2").val() == "1"){
			$("#email_con01").css("display", "");
			$("#email_con02").css("display", "none");
			$("#li_email").children("span").attr("class", "cur2");
			$("#email_false2").css("display", "");
		}else{
			$("#email_con01").css("display", "none");
			$("#email_con02").css("display", "");
			$("#li_email").children("span").removeAttr("class");
		}
		$("#id_isExist2").val("");
		window.clearInterval(intervalId2);
	}
}
function validateRegInfo(type){
	var name_pattern = /^[a-zA-Z]{1}(\w){4,16}[a-zA-Z0-9]{1}$/;
	var pwd_pattern = /^[a-zA-Z0-9]{6,16}$/;
	var email_pattern = /^[0-9a-zA-Z_\-\.]+@[0-9a-zA-Z_\-]+(\.[0-9a-zA-Z_\-]+)*$/;
	var mobile_pattern = /^1[0-9]{10,12}$/;
	var qq_pattern = /^[0-9]+$/;
	var code_pattern = /^[0-9]{4}$/;
	if(type == "name"){
		var name = $("#reg_userName").val();
		$("#name_false").css("display", "none");
		$("#name_false2").css("display", "none");
		$("#name_empty").css("display", "none");
		if(!name_pattern.test(name)){
			$("#name_con01").css("display", "");
			$("#name_con02").css("display", "none");
			$("#li_userName").children("span").attr("class", "cur2");
			return false;
		}else{
			checkUserNameExist(name);
			intervalId = window.setInterval("isUserNameExist()",50);
		}
	}else if(type == "pwd"){
		var pwd = $("#reg_password").val();
		$("#pwd_false").css("display", "none");
		$("#pwd_empty").css("display", "none");
		if(!pwd_pattern.test(pwd)){
			$("#pwd_con01").css("display", "");
			$("#pwd_con02").css("display", "none");
			$("#li_pwd").children("span").attr("class", "cur2");
			return false;
		}else{
			$("#pwd_con01").css("display", "none");
			$("#pwd_con02").css("display", "");
			$("#li_pwd").children("span").removeAttr("class");
		}
	}else if(type == "pwd2"){
		var pwd = $("#reg_password").val();
		var pwd2 = $("#reg_password2").val();
		$("#pwd2_false").css("display", "none");
		$("#pwd2_empty").css("display", "none");
		if(!pwd_pattern.test(pwd2)|| pwd2 != pwd){
			$("#pwd2_con01").css("display", "");
			$("#pwd2_con02").css("display", "none");
			$("#li_pwd2").children("span").attr("class", "cur2");
			return false;
		}else{
			$("#pwd2_con01").css("display", "none");
			$("#pwd2_con02").css("display", "");
			$("#li_pwd2").children("span").removeAttr("class");
		}
	}else if(type == "email"){
		var email = $("#reg_email").val();
		$("#email_false").css("display", "none");
		$("#email_false2").css("display", "none");
		$("#email_empty").css("display", "none");
		if(!email_pattern.test(email)){
			$("#email_con01").css("display", "");
			$("#email_con02").css("display", "none");
			$("#li_email").children("span").attr("class", "cur2");
			return false;
		}else{
			checkEmailExist(email);
			intervalId = window.setInterval("isEmailExist()",50);
		}
	}else if(type == "mobile"){
		var mobile = $("#reg_mobile").val();
		$("#mobile_false").css("display", "none");
		$("#mobile_false2").css("display", "none");
		$("#mobile_empty").css("display", "none");
		if(!mobile_pattern.test(mobile)){
			$("#mobile_con01").css("display", "");
			$("#mobile_con02").css("display", "none");
			$("#li_mobile").children("span").attr("class", "cur2");
			return false;
		}else{
			$("#mobile_con01").css("display", "none");
			$("#mobile_con02").css("display", "");
			$("#li_mobile").children("span").removeAttr("class");
		}
	}else if(type == "qq"){
		var qq = $("#reg_qq").val();
		$("#qq_false").css("display", "none");
		$("#qq_false2").css("display", "none");
		$("#qq_empty").css("display", "none");
		if(!qq_pattern.test(qq)){
			$("#qq_con01").css("display", "");
			$("#qq_con02").css("display", "none");
			$("#li_qq").children("span").attr("class", "cur2");
			return false;
		}else{
			$("#qq_con01").css("display", "none");
			$("#qq_con02").css("display", "");
			$("#li_qq").children("span").removeAttr("class");
		}
	}else if(type == "code"){
		$("#code_con01").css("display", "none");
		$("#code_con02").css("display", "none");
		$("#li_code").children("span").attr("class", "jmlma1");
	}
	return true;
}
 // ´´½¨XMLHttpRequest¶ÔÏó
function createXMLHttpRequest() {
	if (window.XMLHttpRequest) {// IE 7.0¼°ÒÔÉÏ°æ±¾ºÍ·ÇIEµÄä¯ÀÀÆ÷
		xmlHttpReq = new XMLHttpRequest();
	} else {// IE 6.0¼°ÒÔÏÂ°æ±¾
		try {
			xmlHttpReq = new ActiveXObject("MSXML2.XMLHTTP");
		}catch (e) {
			try {
				xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
			}catch (e) {}
		}
	}
	if (!xmlHttpReq) {
		alert("µ±Ç°ä¯ÀÀÆ÷²»Ö§³Ö!");
		return null;
	}
	return xmlHttpReq;
}
function reg(){
	if(!$("#id_protocol").attr("checked")){
		$("#div_protocol").css("display", "");
		return false;
	}else{
		$("#div_protocol").css("display", "none");
	}
	//var item = ['name','pwd','pwd2','code'];
	var item = ['name','pwd','code'];
	for(var i=0;i<item.length;i++){
		if(!validateRegInfo(item[i])){
			return false;
		}
	}
	var name = $("#reg_userName").val();
	var pwd = $("#reg_password").val();
	var email = $("#reg_email").val();
	var mobile = $("#reg_mobile").val();
	var qq = $("#reg_qq").val();
	var code = $("#reg_validateCode").val();
	//var code = encodeURI($("#reg_validateCode").val());
	var param = {
		userName : name,
		password : pwd,
		email : email,
		mobile : mobile,
		qq : qq,
		validateCode : code
	};

	$.ajax({
		url : '/index.php/Passport/doreg/',
		data : param,
		success : function(data){
			switchAuthCode();
			if(data!=null&&data!=""){
				var obj = eval("("+data+")");
				if(obj.code == "001001"){//ÑéÖ¤Âë´íÎó
					$("#code_con01").css("display", "");
					$("#code_con02").css("display", "none");
					$("#li_code").children("span").attr("class", "jmlma1 cur2");
					$("#vcode_text").text(obj.info);
					$("#code_false").css("display", "");
				}else if(obj.code == "001500"){//Òì³£´íÎó
					alert(obj.info);
				}else if(obj.code == "000000"){//×¢²á³É¹¦
					var json = {};
					json.isLogin = "1";
					json.connectType = 0;
					json.connectId = "";
					json.userInfo = {};
					json.userInfo.userName = obj.userName;
					json.userInfo.userId = obj.userId;
					json.userInfo.nickName = obj.nick;
					
					if(obj.jxyrecordid!='')
					{
						
						var tmpstr = "http://api.juxiangyou.com/web/enjoy.php?tokenID=5191&recordID="+obj.jxyrecordid+"&accessCode="+obj.userName+"&accessKey="+obj.jxyaccessKey;
						/*
						var param2 = {
							tokenID : 5191,
							recordID : obj.jxyrecordid,
							accessCode : obj.userName,
							accessKey : obj.jxyaccessKey
						};
						alert(tmpstr);
						$.ajax({
							url : tmpstr,
							//data : param2,
							success : function(data){
								//alert(data);
							}
						});*/
						// ¹ã¸æ:°®ÓÆÓÆÐã³¡,tokenID:5191,tokenKey:1a7dc1db135903dd
						
						//createRequest();
						/*
						var xmlHttpforJxy=createXMLHttpRequest();
						alert(tmpstr);
						xmlHttpforJxy.open("POST",tmpstr, false);
						xmlHttpforJxy.onreadystatechange=function(){
							alert("responseText="+xmlHttpforJxy.responseText+" status="+xmlHttpforJxy.status+" readyState="+xmlHttpforJxy.readyState);
							if (xmlHttpforJxy.readyState==4){
								
							}
						};
						xmlHttpforJxy.open("GET",tmpstr,true);
						xmlHttpforJxy.send(null);
						*/
						window.open(tmpstr);
					}
					parent.UAC.showUserInfo(json);
					window.location.href="/index.php/Passport/regSuccess/userName/"+obj.userName;
				}else if(obj.code == "001002"){//ÓÃ»§ÃûÒÑ´æÔÚ
					$("#name_con01").css("display", "");
					$("#name_con02").css("display", "none");
					$("#li_userName").children("span").attr("class", "cur2");
					if($("#name_false2").css("display", "")){
							$("#name_con01").text('');
						}
				}else if(obj.code == '001190'){
					alert(obj.info);
				}
			}
		}
	});
}
//
function switchAuthCode(){
	$("#img_authCode").attr("src", "/index.php/Base/verify/rand/"+Math.random());
}	
//===============================================================
//ÕÒ»ØÃÜÂë========================================================
function checkFBUserName(){
	var userName = $("#fb_userName").val();
	if($.trim(userName) == ""){
		$("#fb_info").text("ÓÃ»§Ãû²»ÄÜÎª¿Õ");
		return false;
	}else{
		$("#fb_info").text("");
		return true;
	}
}
function checkFBEmail(){
	var email = $("#fb_email").val();
	var pattern = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
	if($.trim(email) == ""){
		$("#fb_info").text("ÓÊÏä²»ÄÜÎª¿Õ");
		return false;
	}else if(!pattern.test(email)){
		$("#fb_info").text("ÓÊÏä¸ñÊ½²»ÕýÈ·");
		return false;
	}else{
		$("#fb_info").text("");
		return true;
	}
}
//ÕÒ»ØÃÜÂë
function findBackPwd(){
	var userName = $("#fb_userName").val();
	var email = $("#fb_email").val();
	if(!checkFBUserName()){
		return false;
	}
	if(!checkFBEmail()){
		return false;
	}
	var param = {
		userName : userName,
		email : email,
		rand : Math.random()
	}
	//$("#btn_fb").attr("disabled","true");
	$.ajax({
		url : '/index.php/Passport/findBackPwd/',
		data : param,
		success : function(data){
			if(data!=null&&data!=""){
				var obj = eval("("+data+")");
				if(obj.code == "000000"){
					window.location.href="/index.php/Passport/findBackPwdSuccess/userName/"+userName+"/email/"+email;
				}else{
					//$("#btn_fb").attr("disabled","");
					$("#fb_info").text(obj.info);
				}
			}
		}
	});
}
//===========================================================


var QQ = {
	connect : function(){
		window.location.href="/qqlogin/oauth/login.php";
		if(parent.document.getElementById("uac_div")!=null){
			parent.document.getElementById("uac_iframe_close").style.display = "block";
			parent.document.getElementById("uac_div").style.width = "666px";
			parent.document.getElementById("uac_div").style.height = "402px";
			parent.document.getElementById("uac_frame").style.width = "666px";
			parent.document.getElementById("uac_frame").style.height = "402px";
		}
	}
};

var RenRen = {
	connect : function(){
		window.location.href="/index.php/ThirdParty/renrenloginlink/";
		if(parent.document.getElementById("uac_div")!=null){
			//parent.document.getElementById("uac_iframe_close").style.display = "block";
			parent.document.getElementById("uac_div").style.width = "666px";
			parent.document.getElementById("uac_div").style.height = "402px";
			parent.document.getElementById("uac_frame").style.width = "666px";
			parent.document.getElementById("uac_frame").style.height = "402px";
		}
	}
};