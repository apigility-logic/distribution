var chatSocket;
var flashEffectObject;
var flashEffectObjectswf;
var flashFlyWordObject;
var applySongFlag;
// 如果服务端挂掉，记录定时器ID
var timerID = 0;
var reconnFlag = false;
var sofaUserList = {};

var websocketChat = {
		_chatToSocket:function(param1,param2, msg) {
                        console.log(msg);
			chatSocket.send(msg);
		},
		_initConnect:function() {
			console.log('正在建立连接...');
			var address = '7272';
			if (document.domain == "net.cn") {
                                address = '7474';
			}
			chatSocket =  new WebSocket('ws://139.129.19.190:'+address);
			flashEffectObject = websocketChat._initFlashEffectObject('flashCallGift');
			flashEffectObjectswf = websocketChat._initFlashEffectObject('flashCallGiftswf');
			flashFlyWordObject = websocketChat._initFlashEffectObject('flashFlyWord');
			$('#loading_online').remove();
			$("#loading_manage").remove();
			chatSocket.onclose = function() {
				if (!reconnFlag) {
					timerID = setInterval(websocketChat._initConnect,5000);
					reconnFlag = true;
				}
			}
			chatSocket.onopen = function() {
				// var user_id = $("#user_personal_id").text().split(":");
				// user_id[1] = user_id[1] == undefined ? '' : user_id[1];
				if (reconnFlag) {
					window.clearInterval(window.timerID);
   					timerID=0;
   					reconnFlag = false;
   					clearOnlineNum();
					clearOnlineUser();
				}
				var cookie = document.cookie.split('; ');
				var token = '';
				var ucuid = '';
				for(var index in cookie) {
					if (token !== '' && ucuid !== '') {
						break;
					}
					var sub = cookie[index].split('=');
					if ( sub[0] == 'token') {
						token = sub[1];
						continue;
					}
					if ( sub[0] == 'ucuid') {
						ucuid = sub[1];
						continue;
					}
				}
				// if (!token || !ucuid) {
				// 	alert('请重新登录服务器');
				// }
				switchFlashEffect(false);
				chatSocket.send('{"_method_":"login","user_name":"'+userInfo.client_name+'", "user_id":"'+userInfo.user_id+'", "levelid":"'+userInfo['levelid']+'", "daoju":"'+userInfo['daoju']+'", "token":"'+token+'", "ucuid":"'+ucuid+'", "room_id":"'+_show['roomId']+'"}');
			}

			chatSocket.onmessage = function(event) {
				var msg = eval("("+event.data+")");
				var type = msg.type.split('.');
				console.log(msg);
				switch (type[0]) {
					case 'cancelBackground':
						ChatMessage.cancelRoomBackImg();
						break;
					case 'setBackground':
						if (msg.hasOwnProperty('bgimg')) {
							ChatMessage.changeRoomBackImg(msg.bgimg,true);
							return ;
						}
						break
					case 'takeSeat':
						ChatMessage.updateSofaSeat(msg);
						break;
					case 'takeGuard':
						ChatMessage.updateGuard(msg);
						break;
					case 'SendPubMsg':
					case 'SendPrvMsg':
						ChatMessage.add_message(msg);
						break;
					case 'login':
						if ($("#online_"+msg.user_id).length) {
							return ;
						}
						ChatMessage.user_login_callback(msg);
						changeOnlineNum();
						break;
					case 'logout':
						ChatMessage.user_logout_callback(msg);
						changeOnlineNum();
						break;
					case 'setSongApply':
						var a = parseInt(msg.apply);
						var b = a == 1 ? 2 : 1;
						if (_show.emceeId==_show.userId) {
							// 主播
							applySongFlag = b;
            				$("#songApply_"+a).css("display","none");
            				$("#songApply_"+b).css("display","");
            			}
            			else {
            				$('.sdeal').show();
                    		if(a == 1){
                        		applySongFlag = 1;
                        		$('#songApplyShow').html('允许');
                        		$('#songApplyIcon').attr('class','on');
                        		$('.song_deal').find('p').html('<a id="songApply" href="javascript://" onclick="Song.wangSong();" title="我要点歌">我要点歌</a>');      
                    		}else{
                        		applySongFlag = 2;
                        		$('#songApplyShow').html('禁止');
                        		$('#songApplyIcon').attr('class','off');
                        		$(".song_deal").find('p').html('');
                    		}
            			}
            			break;
					case 'vodSong':
					case 'agreeSong':
					case 'disAgreeSong':
						if (type[0] == 'agreeSong' && applySongFlag == 1) {
							var flyWord = "^_^ 主播已同意 "+msg.client_name+" 点歌："+msg.songName;
							ChatMessage.showFlash(flyWord,48,6);
						}
						Song.initVodSong();
						break;
					case 'agreeSong':
						Song.agreeSong(msg.songId);
						break;
					case 'sendGift':
						if (playFlashEffect) { //msg.userId == userInfo.user_id
							JsInterface.showFlash(msg);
						}
						ChatMessage.recv_display_gift(msg);
						break;
					case 'error':
						// $("#chat_hall").append(" <font color='greenyellow'>" + msg.content + "<br />");
						_alert(msg.content, 5);
						if (type[1] == 'kicked') {
							// TODO 关闭页面
							window.location.href="/";
						}
						break;
					case 'right':
						var userId = msg.user_id;
						if (type[1] == 'adminer' && $("#online_"+userId).parent().attr('id') == 'content2_2') {
							$("#lm2_2").children().text(parseInt($("#lm2_2").children().text())-1);
							ChatMessage.add_user_adminer(msg);
							$("#lm2_1").children().text(parseInt($("#lm2_1").children().text())+1);
						} else if (type[1] == 'removeAdminer' && $("#online_"+userId).parent().attr('id') == 'content2_1') {
							$("#lm2_1").children().text(parseInt($("#lm2_1").children().text())-1);
							ChatMessage.remove_user_adminer(msg);
							$("#lm2_2").children().text(parseInt($("#lm2_2").children().text())+1);
						}
						break;
					case 'sysmsg':
						if (type[1] == 'alert') {
							_alert(msg.content, 3);
							return ;
						}
						$("#chat_hall").append(" <font color='greenyellow'>" + msg.content + "<br />");
						break;
					default:
						console.log('recv other');
				}
				ChatMessage.scrollChatHeight();
				if($(".gunping").hasClass("winlock")){
					ChatMessage.removeMoreMsg();
					var scrojh=$("#upchat_hall")[0].scrollHeight;
		            $("#chat_hall").scrollTop($("#upchat_hall").scrollTop(scrojh));
				}
				if($(".gunping2").hasClass("winlock")){
					ChatMessage.removeMoreMsg();
					var scrojh=$("#chat_hall_private")[0].scrollHeight;
		            $("#sm_chat").scrollTop($("#chat_hall_private").scrollTop(scrojh));
				}
				
			}
		},
		playEffect:function(giftIcon, effectId, time) {
			//console.log(new Date().getTime());
			flashEffectObject.playEffect(giftIcon, effectId, time);
		},
		playEffectswf:function(giftIcon, effectId, time) {
			//console.log(new Date().getTime());
			flashEffectObjectswf.playEffect(giftIcon, effectId, time);
		},
		clearEffect:function() {
			//console.log(new Date().getTime());
			flashEffectObject.clearEffect();
		},
		clearEffectswf:function() {
			flashEffectObjectswf.clearEffect();
		},
		_initFlashEffectObject:function(id) {
			// flashFlyWord  flashCallGift
			return swfobject.getObjectById(id);
		}
	}
