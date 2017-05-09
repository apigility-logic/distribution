var UAC = {
	iframeReady : false,
	readyCount : 0,
	_charset : "",
	_type : "",
	_callback : "",
	
	createNode : function(data){
		var flag = false;
		if(UAC._type == "index"){
			flag = true;
		}
	
		
	},
	//注册或登录后的回调方法
	showUserInfo : function(data){
                console.log(data);
		UAC.createNode(data);
		if(top.document.getElementById('toolbar')!=null && typeof(top.toolbar)!="undefined") {
			top.toolbar.showUserInfo(top.toolbar);
		}
		try{
			if(UAC._callback != ""){
				eval(UAC._callback+"();");
			}
			index_showuserinfo();
		}catch(e){}
		
	},
	
	openUAC : function(type, callback){
		if(typeof(callback) != "undefined"){
			UAC._callback = callback;
		}
		UAC.closeUAC();
		var node = "<div id=\"uac_div\" style=\"display:none\">";
		node += "<iframe id=\"uac_frame\" src=\"/index.php/Passport/usercenter/\" scrolling=\"no\" frameborder=\"0\" width=\"100%\" height=\"100%\" allowtransparency=\"true\"/>";
		node += "<span id=\"uac_iframe_close\" onclick=\"UAC.closeUAC();\" style=\"display:none; position: absolute; z-index: 100001; top: 10px; left: 460px; cursor: pointer;\">[关闭]</span>";
		node += "</div>";
		jQuery("body").append(node);
		var tops = (document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.getElementsByTagName("body").item(0).scrollTop;
		tops += 150;
		jQuery("#uac_div").css("width","100%");
		jQuery("#uac_div").css("height","100%");
		jQuery("#uac_div").css("position","fixed");
		jQuery("#uac_div").css("top","0px");
		jQuery("#uac_div").css("z-index","100000");
		//jQuery("#uac_div").css("left",parseInt((parseInt($("body").width()) - parseInt(jQuery("#uac_div").width()))/2)+"px");
		jQuery("#uac_div").css("left",'0');
		UAC.showUAC(type);
	},
	showUAC : function(type){
		UAC.readyCount++ ;
		if(UAC.iframeReady){
			jQuery("#uac_div").css("display","");
			document.getElementById("uac_frame").contentWindow.switchDiv(type);
			UAC.readyCount = 0;
		}else{
			if(UAC.readyCount <= 5){
				setTimeout("UAC.showUAC("+type+")", 1000);
			}else{
				UAC.readyCount = 0;
			}
		}
	},
	closeUAC : function(){
		if(jQuery("#uac_div").length > 0){
			jQuery("#uac_div").remove();
			UAC.iframeReady = false;
		}
	},
	logout : function(returnUrl, type){
		if(type == 1){
			if(jQuery("#uac_div").length < 1){
				var node = "<div id=\"uac_div\" style=\"display:none\">";
				node += "<iframe id=\"uac_frame\" src=\"/index.php/Passport/userCenter/\" scrolling=\"no\" frameborder=\"0\" width=\"780px\" height=\"320\" allowtransparency=\"true\"/>";
				node += "<span id=\"uac_iframe_close\" onclick=\"UAC.closeUAC();\" style=\"display:none; position: absolute; z-index: 100001; top: 10px; left: 470px; cursor: pointer;\" >关闭</span>";
				node += "</div>";
				jQuery("body").append(node);
			}
			if(jQuery("#uac_div").length > 0){
				UAC.readyCount++ ;
				if(UAC.iframeReady){
					document.getElementById("uac_frame").contentWindow.logout(returnUrl);
					UAC.readyCount = 0;
				}else{
					if(UAC.readyCount <= 5){
						setTimeout("UAC.logout('"+returnUrl+"',"+type+")", 1000);
					}else{
						UAC.readyCount = 0;
					}
				}
			}
		}else if(type == 2){
			jQuery.getScript('/index.php/Passport/logout/', function(){
				window.location.href = returnUrl;
            });
		}else{
			jQuery.getScript('/index.php/Passport/logout/', function(){
				window.location.href = returnUrl;
            });
		}
	}
};
