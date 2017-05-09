/**
*聊天接口
*/
// 在线用户列表
var userList = {};
// 当前操作的用户对象
var managedClientUser = {};

// 是否关闭礼物特效
var playFlashEffect = true;
var emToWord = {
    'OK':'/OK',
    'aoman':'/傲慢',
    'baiyan':'/白眼',
    'banya':'/板牙',
    'bianda':'/鞭打',
    'bishi':'/鄙视',
    'bizui':'/闭嘴',
    'brzs':'/不忍直视',
    'bule':'/不了',
    'chahan':'/擦汗',
    'choudale':'/丑大了',
    'ciya':'/呲牙',
    'dabing':'/大兵',
    'dahan':'/大汗',
    'daku':'/大哭',
    'daxiao':'/大笑',
    'fadai':'/发呆',
    'fake':'/吼吼',
    'fanu':'/发怒',
    'feiwen':'/飞吻',
    'fendou':'/奋斗',
    'fuyan':'/敷衍',
    'gangga':'/尴尬',
    'gouyin':'/勾引',
    'guzhang':'/鼓掌',
    'haixiu':'/害羞',
    'hanxiao':'/憨笑',
    'haoxiao':'/好笑',
    'haqian':'/哈欠',
    'huaixiao':'/坏笑',
    'huanle':'/欢乐',
    'jingkong':'/惊恐',
    'jingya':'/惊讶',
    'keai':'/可爱',
    'keling':'/可怜',
    'koubi':'/抠鼻',
    'ku':'/酷',
    'kuaikule':'/快哭了',
    'kuanghan':'/狂汗',
    'lenghan':'/冷汗',
    'lengxiao':'/冷笑',
    'liaojie':'/了解',
    'liuhan':'/流汗',
    'liulei':'/流泪',
    'meiyan':'/媚眼',
    'men':'/萌',
    'meng':'/懵',
    'nengwen':'/熊吻',
    'papa':'/怕怕',
    'piezui':'/撇嘴',
    'pu':'/噗',
    'qiang':'/强',
    'qiaoda':'/敲打',
    'qinqin':'/亲亲',
    'rengle':'/认了',
    'se':'/色',
    'senan':'/色男',
    'shuai':'/衰',
    'shuaidai':'/帅呆',
    'shui':'/睡觉',
    'shutan':'/舒坦',
    'taozui':'/陶醉',
    'teng':'/疼',
    'tiaodou':'/挑逗',
    'tiaopi':'/调皮',
    'toukan':'/偷看',
    'touxiao':'/偷笑',
    'tuxie':'/吐血',
    'weiqu':'/委屈',
    'woqu':'/我去',
    'wulai':'/无奈',
    'xia':'/吓',
    'xieyan':'/斜眼',
    'xu':'/嘘',
    'yeah':'/Yeah',
    'yinxian':'/阴险',
    'yinxiao':'/阴笑',
    'yiwen':'/疑问',
    'youhengheng':'/右哼哼',
    'zhuohenghen':'/左哼哼',
    'yun':'/晕',
    'zaijian':'/再见',
    'zhuakuang':'/抓狂',
    'zouma':'/咒骂'
};
// levelid < 7 .png  levelid >7 .gif
var levelidPartition = 7;
var levelPath = 'style/images/level/';

var daoJu = {
    '1':'9172',
    '2':'9177',
    '3':'9178',
    '4':'9176',
    '5':'9174',
    '6':'9165',
    '7':'9170',
    '8':'9169',
    '9':'9175',
    '10':'9182',
    '11':'9166',
    '12':'9167',
    '13':'9168',
    '14':'9164'
};
var vipWord = {
    '1':'至尊VIP',
    '2':'黄金VIP'
};

var defaultAvartar = "javascript:this.src='/style/avatar/0/0_big.jpg'";
var vipPath = {
    '1':'/style/images/vip1.png',
    '2':'/style/images/vip2.png'
};
var defaultRoomBackImg ='/style/meilibo/images/show_bg.jpg';
var ChatMessage = {
    changeRoomBackImg: function(path, flyFlag) {
        if (path == "") {
            path = defaultRoomBackImg;
        }
        $("body").css('background','url('+path+')');
        if (flyFlag) {
            ChatMessage.showFlash("^_^主播更改了房间背景^_^",48,6);
        }
    },
    updateGuard: function(data) {
        var length = $("#lb_list>li[class=guardians]").length;
        for(var index = 0;index < length ; index++) {
            if ($("#lb_list>li[class=guardians]").eq(index).children().last().text() == data.nickname) {
                // 已经购买守护
                return ;
            }
        }
        var gardStr = '<a href="javascript:;" target="_blank">'
        +'<img src="'+data.icon+'" width="45" height="45" border="0" /></a>'
        +'<div class="shouhu_show"><a href="javascript:;" title="'+data.nickname+'"  target="_blank">'
        +'<img src="/style/images/shou.png" width="5" height="5"  border="0"></a></div>'
        +'<div style="width: 50px ;margin-right:0px" class="shouhu_06" style="font-size:16px;; display:block;">'
        +data.nickname+'</div>';
        $("#lb_list>li[class=guard_no]").first().attr("class","guardians").html(gardStr);
    },
    updateSofaSeat: function(data) {
        var strsofa ='<a href="javascript:void(0)" data-seat="0" class="seatlight t'+data.seatId+'"><img onerror="javascript:this.src=\'/style/avatar/0/0_big.jpg\'" src="'+data.userIcon+'" title="'+data.client_name+'" seatnum="'+data.seatPrice+'"/><span seatid="'+data.seatId+'">'+data.client_name+'</span> </a>';
        var index = parseInt(data.seatId) - 1;
        sofaUserList[index+1] = data.client_name;
        $('#user_sofa>ul>li').eq(index).html(strsofa); 
    },
    cancelRoomBackImg: function() {
        $("body").css('background','');   
        ChatMessage.showFlash("主播取消了房间背景～～～",48,6);
    },
    add_message:function(client_name, content, to_client_id, to_client_name, time, is_private) {
        send_veiw(client_name, content, to_client_id, to_client_name, time, is_private);

    },
    user_login_callback:function(msg) {
        if($("#online_"+msg.user_id).length > 0){
            return;
        }else{
            add_user_login(msg);
        } 
    },
    user_logout_callback:function(msg) {
        del_user_logout(msg);
    },
    recv_display_gift: function(msg) {
        displayGift(msg);
    },
    scrollChatHeight:function() {
        $("#chat_hall").scrollTop($("#chat_hall")[0].scrollHeight);
        $("#sm_chat").scrollTop($("#sm_chat")[0].scrollHeight);
    },
    showFlash: function(message,size,speed) {
        // size= 48 speed = 3
        $('#flashFlyWord').css({"width":"990px","height":"400px"});
        flashFlyWord.showFlyword(message,size,speed);
        Chat.getUserBalance();//用户秀币更新    
        Chat.getRankByShow();//本场排行
    },
    removeMoreMsg: function() {
        //只显示一百条聊天,多的删除掉
        while ($("#chat_hall>p").length > 200) {
                $("#chat_hall").children().first().remove();
            }
        
    },
    add_user_adminer: function(data) {
        var user_id = data.user_id;
        // var client_id;
        // var ucuid;
        // var title;
        
        if (user_id != userInfo.user_id) {
            // client_id = $("#online_"+user_id).attr('client_id');
            // ucuid = $("#online_"+user_id).attr('ucuid');
            // title = $("#online_"+user_id).attr('title');
            data.client_id = $("#online_"+user_id).attr('client_id');
            data.ucuid = $("#online_"+user_id).attr('ucuid');
            data.client_name = $("#online_"+user_id).attr('title');
            data.vip = $("#online_"+user_id).attr('vip');
            data.levelid = $("#online_"+user_id).attr('levelid');
            $("#online_"+user_id).remove();
            insertExactPos('adminer',data);
            // $("#content2_1").append('<li id="online_'+user_id+'" tid="'+user_id+'" title="'+title+'" client_id="'+client_id+'" ucuid="'+ucuid+'" onclick="UserListCtrl.chatPublic();" class="">'+vipPic+levelPic+'<img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+ucuid+'&size=middle"><span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+title+'</a></p></li>');
            return;
        }
        _show.admin = 1;
        // client_id = userInfo.client_id;
        // ucuid = userInfo.ucuid;
        // title = userInfo.client_name;
        data.client_id = userInfo.client_id
        data.ucuid = userInfo.ucuid;
        data.client_name = userInfo.client_name;
        data.levelid = userInfo.levelid;
        data.vip = userInfo.vip;
        $("#online_"+user_id).remove();
        insertExactPos('adminer',data);
        // $("#content2_1").append('<li id="online_'+user_id+'" tid="'+user_id+'" title="'+title+'" client_id="'+client_id+'" ucuid="'+ucuid+'" onclick="UserListCtrl.chatPublic();" class="">'+vipPic+levelPic+'<img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+ucuid+'&size=middle"><span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+title+'</a></p></li>');
        $("#ctrllist>li").attr('style','display: list-item;');
        $("#ctrllist>font>span").attr('style','display: inline;');
        $("#ctrllist>font>li:eq(6)").attr('style','display: inline;');
        $("#ctrllist>font>li:eq(7)").attr('style','display: inline;');
    },
    remove_user_adminer: function(data) {
        var user_id = data.user_id;
        // var client_id;
        // var ucuid;
        // var title;
        if (user_id != userInfo.user_id) {
            // client_id = $("#online_"+user_id).attr('client_id');
            // ucuid = $("#online_"+user_id).attr('ucuid');
            // title = $("#online_"+user_id).attr('title');
            data.client_id = $("#online_"+user_id).attr('client_id');
            data.ucuid = $("#online_"+user_id).attr('ucuid');
            data.client_name = $("#online_"+user_id).attr('title');
            data.vip = $("#online_"+user_id).attr('vip');
            data.levelid = $("#online_"+user_id).attr('levelid');
            $("#online_"+user_id).remove();
            insertExactPos('client',data);
            // $("#content2_2").append('<li id="online_'+user_id+'" tid="'+user_id+'" title="'+title+'" client_id="'+client_id+'" ucuid="'+ucuid+'" onclick="UserListCtrl.chatPublic();" class=""><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+ucuid+'&size=middle"><span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+title+'</a></p></li>');
            return;
        }
        _show.admin = 0;
        // client_id = userInfo.client_id;
        // ucuid = userInfo.ucuid;
        // title = userInfo.client_name;
        data.client_id = userInfo.client_id
        data.ucuid = userInfo.ucuid;
        data.client_name = userInfo.client_name;
        data.vip = userInfo.vip;
        data.levelid = userInfo.levelid;
        $("#online_"+user_id).remove();
        insertExactPos('client',data);
        // $("#content2_2").append('<li id="online_'+user_id+'" tid="'+user_id+'" title="'+title+'" client_id="'+client_id+'" ucuid="'+ucuid+'" onclick="UserListCtrl.chatPublic();" class=""><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+ucuid+'&size=middle"><span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+title+'</a></p></li>');
        $("#ctrllist>li").attr('style','');
        $("#ctrllist>font>span").attr('style','');
        $("#ctrllist>font>li:eq(6)").attr('style','');
        $("#ctrllist>font>li:eq(7)").attr('style','');
    },
    ShutUp: function() {
        if (!isValidOpera()) {
            return ;
        }
        chatSocket.send('{"_method_":"Manage", "_type_":"disableMsg","managed_user_id":"'+managedClientUser.user_id+'", "managed_user_name":"'+managedClientUser.client_name+'", "managed_client_id":"'+managedClientUser.client_id+'"}');
        hideChatHallManage();
    },
    Kick:function() {
        if (!isValidOpera()) {
            return ;
        }
        chatSocket.send('{"_method_":"Manage", "_type_":"addKicked","managed_user_id":"'+managedClientUser.user_id+'", "managed_user_name":"'+managedClientUser.client_name+'", "managed_client_id":"'+managedClientUser.client_id+'"}');
        hideChatHallManage();
    },
    Resume:function() {
        if (!isValidOpera()) {
            return ;
        }
        chatSocket.send('{"_method_":"Manage", "_type_":"enableMsg","managed_user_id":"'+managedClientUser.user_id+'", "managed_user_name":"'+managedClientUser.client_name+'", "managed_client_id":"'+managedClientUser.client_id+'"}');
        hideChatHallManage();
    },
    setManager:function() {
        if (!isValidOpera()) {
            return ;
        }
        chatSocket.send('{"_method_":"Manage", "_type_":"adminer","managed_user_id":"'+managedClientUser.user_id+'", "managed_user_name":"'+managedClientUser.client_name+'", "managed_client_id":"'+managedClientUser.client_id+'"}');
        hideChatHallManage();
    },
    delManager:function() {
        if (!isValidOpera()) {
            return ;
        }
        chatSocket.send('{"_method_":"Manage", "_type_":"removeAdminer","managed_user_id":"'+managedClientUser.user_id+'", "managed_user_name":"'+managedClientUser.client_name+'", "managed_client_id":"'+managedClientUser.client_id+'"}');
        hideChatHallManage();
    },
    chatPublic:function() {
        if (!isValidOpera()) {
            return ;
        }
        $("#to_user_id").val(managedClientUser.client_id);
        $("#to_nickname").val(managedClientUser.client_name);
        $("#msg_to_one>span").text(managedClientUser.client_name);
        $("#msg_to_one").show();
        $("#msg_to_all").hide();
        $("#whisper").attr("disabled",false);
        $("#whisper").attr("checked",false);
        $("#msg").focus();
        hideChatHallManage();
    },
    chatPrivate:function() {
        ChatMessage.chatPublic();
        $("#whisper").attr("checked",true);
        hideChatHallManage();
    }
}


//接收聊天信息回调
function send_veiw(msg) {
    $(document).ready(function() {
        var is_private = msg.type == 'SendPrvMsg' ? 1 : 0;
        var time = msg.time;
        var vipPic = getVipPic(msg);
        var levelPic = getLevelPic(msg);
        var em = msg.content.match(/\[`\w*`\]/g);
        var flyContent = msg.content;
        if (em) {
            for(var index in em) {
                var gif = em[index].split("`")[1];
                var emNode = '<em class="show_message_face" style="background: url(/style/images/face/'+gif+'.gif) no-repeat;"></em>';
                msg.content = msg.content.replace(em[index], emNode);
                flyContent = flyContent.replace(em[index],emToWord[gif]);
            }
        }
        var msgStr = '', msgStr1 = '';
        var flyWord = '';
        var sofaWord = '';
        if (is_private) 
        {//悄悄话
            var pub = !parseInt(msg.pub) ? '(私密)' : '(公开)';
            if (msg.from_client_id == userInfo.client_id) {
                //发送者回显消息
                msgStr1 = "<p>"+time + " 悄悄话" + pub + " <font color='greenyellow'>你</font> 对 <font color='greenyellow'><a class='msga' onclick='resizeManageBox();chatHallMange(this);' style='color:#35c4ff;' user_id='"+msg.to_user_id+"' client_id='"+msg.to_client_id+"' title='"+msg.to_client_name+"'>" + msg.to_client_name + "</a></font> 说: " + msg.content + "<br /></p>";
                sofaWord = flyWord = " 悄悄话" + pub + " 你对" + msg.to_client_name + " 说: " + flyContent; 
            } else if (msg.to_client_id == userInfo.client_id) {
                //对当前用户说的悄悄话
                msgStr1 = "<p>"+time + " 悄悄话" + pub + " <font color='greenyellow'><a class='msga' style='color:#35c4ff;' onclick='resizeManageBox();chatHallMange(this);' user_id='"+msg.from_user_id+"' client_id='"+msg.from_client_id+"' title='"+msg.from_client_name+"'>"+msg.from_client_name+"</a></font> 对 <font color='greenyellow'>你</font> 说: " + msg.content + "<br /></p>";
                sofaWord = flyWord = " 悄悄话" + pub + " "+msg.from_client_name+" 对你说: " + flyContent;
            } else {
                //公开的悄悄话
                msgStr1 = "<p>"+time + " 悄悄话 <font color='greenyellow'><a class='msga' style='color:#35c4ff;' onclick='resizeManageBox();chatHallMange(this);' user_id='"+msg.from_user_id+"' client_id='"+msg.from_client_id+"' title='"+msg.from_client_name+"'>"+msg.from_client_name+"</a></font> 对 <font color='greenyellow'><a class='msga' style='color:#35c4ff;' onclick='resizeManageBox();chatHallMange(this);' user_id='"+msg.to_user_id+"' client_id='"+msg.to_client_id+"' title='"+msg.to_client_name+"'>"+msg.to_client_name+"</a></font> 说: " + msg.content + "<br /></p>";
                sofaWord = flyWord = " 悄悄话 "+msg.from_client_name+"对"+msg.to_client_name+"说: " + flyContent;
            }
        } else {
            if(msg.from_client_id == userInfo.client_id){
                msgStr = "<p>"+time + vipPic + levelPic +" <font color='greenyellow'><a class='msga' style='color:#35c4ff;' onclick='resizeManageBox();chatHallMange(this);' user_id='"+msg.from_user_id+"' client_id='"+msg.from_client_id+"' title='"+msg.from_client_name+"'>" + msg.from_client_name + "</a> : " + "<b style='color:#fff;'>"+msg.content + "</b><br /></p>";
                flyWord = msg.from_client_name + " : " + flyContent;
                sofaWord = flyContent;
            }else{
                msgStr = "<p>"+time + vipPic + levelPic +" <font color='greenyellow'><a class='msga' style='color:#35c4ff;' onclick='resizeManageBox();chatHallMange(this);' user_id='"+msg.from_user_id+"' client_id='"+msg.from_client_id+"' title='"+msg.from_client_name+"'>" + msg.from_client_name + "</a> : " + msg.content + "<br /></p>";
                flyWord = msg.from_client_name + " : " + flyContent;
                sofaWord = flyContent;
            }
        }
        $("#sm_chat").append(msgStr1);
        $("#chat_hall").append(msgStr);

        if (msg.hasOwnProperty('fly') && msg.fly == 'FlyMsg') {
            ChatMessage.showFlash(flyWord,48,6);
        }
        var sofamsg = false;
        for(var index in sofaUserList) {
            if (sofaUserList[index] == msg.from_client_name) {
                sofamsg = true; 
            }
        }
        if (sofamsg) {
            if(is_private){
            }else{
                for(var index=1;index<5;index++){
                    if(sofaUserList[index]==msg.from_client_name){
                        $("#user_chat>ul>li").eq(index-1).css("display","block").find("p").text(flyContent);
                    }                  
                }
            }
            setTimeout(function(){
                $("#user_chat>ul>li").css("display","none").find("p").text('');
            }, 10000);    
        }
        $("#msg_to_all").text('所有人');
        $("#to_user_id").val('');
        $("#to_nickname").val('');
        $("#whisper").attr('disabled','disabled');
        

    }) 

}

function getDaojuPic(data){
    var daojuPic = '';
    if (data.hasOwnProperty('daoju') && data.daoju != '') {
        // 加载flash较慢，放到最前面
        daojuPic = '驾驶着  <img src="/style/carpic/s'+data.daoju+'.png" /> ';
    }
    return daojuPic;
}
function getVipPic(data) {
    var vipPic = '';
    if (data.hasOwnProperty('vip') && (data.vip == 1 || data.vip == 2)) {
        vipPic = '<img src="'+vipPath[data.vip]+'" />';
    }
    return vipPic;
}
function getLevelPic(data) {
    var levelPic = '';
    if (data.hasOwnProperty('levelid') && data.levelid >=0 && data.levelid <= 29) {
        var suffix = '.png';
        if (data.levelid > levelidPartition) {
            suffix = '.gif';
        }
        levelPic = '<img src="'+levelPath+data.levelid+suffix+'">';
    }
    return levelPic;
}

function insertExactPos(role,data)
{
    var levelPic = getLevelPic(data);
    var vipPic = getVipPic(data);
    var className = '';
    var obj = null;
    var direction = 'before';
    if (vipPic == '' && levelPic == '') {
        className = 'viewer';
        if ($("."+className).length >0) {
            obj = $("."+className).last();
            direction = 'after';
        }
    }
    else if (vipPic == '' && levelPic != '') {
        var cp = data.levelid;
        var tmpObj = null;
        while(cp <= 18) {
            className = role+'userLevel' + cp;
            tmpObj = $("."+className);
            if (tmpObj.length == 0) {
                cp++;
                continue;
            } else if (tmpObj.length > 0) {
                if (cp == data.levelid) {
                    obj = tmpObj.last();
                    direction = 'after';
                } else {
                    obj = tmpObj.first();
                }
                break;
            }
            if ($(".viewer").length > 0) {
                obj = $(".viewer").first();
            }
            break;
        }
    }
    else if (vipPic != '') {
        var cp = data.levelid;
        var tmpObj = null;
        while(cp <= 18) {
            className = role+'vip' + data.vip + '_' + 'userLevel' + cp;
            tmpObj = $("."+className);
            if (tmpObj.length == 0) {
                cp++;
                continue;
            } else if (tmpObj.length > 0) {
                if (cp == data.levelid) {
                    obj = tmpObj.last();
                    direction = 'after';
                } else {
                    obj = tmpObj.first();
                }
                break;
            }
        }
        if (tmpObj == null) {
            cp = 1;
            while(cp <= 18) {
                className = role+'userLevel' + cp;
                tmpObj = $("."+className);
                if (tmpObj.length == 0) {
                    cp++;
                    continue;
                } else if (tmpObj.length > 0) {
                    if (cp == data.levelid) {
                        obj = tmpObj.last();
                        direction = 'after';
                    } else {
                        obj = tmpObj.first();
                    }
                    break;
                }
                if ($(".viewer").length > 0) {
                    obj = $(".viewer").first();
                }
                break;
            }
        }
    } else {
        _alert('参数错误',3);
        return;
    }
    var liStr = '<li id="online_'+data.user_id+'" tid="'+data.user_id+'" vip="'+data.vip+'" levelid="'+data.levelid+'" title="'+data.client_name+'" client_id="'+data.client_id+'" ucuid="'+data.ucuid+'" onclick="UserListCtrl.chatPublic();" class="'+className+'"><img style="width:44px" class="tou_xiang" src="'+getAvatar(data.user_id)+'" onerror="'+defaultAvartar+'">'+vipPic+levelPic+'<span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+data.client_name+'</a></p></li>';
    if (obj == null) {
        if (role == 'adminer') {
            $("#content2_1").append(liStr);
        } else if (role == 'client') {
            $("#content2_2").append(liStr);
        }
    } else {
        if (direction == 'before') {
            obj.before(liStr);
        } else if (direction == 'after'){
            obj.after(liStr);
        }
    }
}

function add_user_login(data) {
    if (!reconnFlag) {
        
        var daojuPic = '';
        if (isGuard) {
            daojuPic = getDaojuPic(data);
            // 加载flash较慢，放到最前面
            $("body").prepend('<div  style="position:fixed;z-index:100; bottom:40px;right:0;" class="daoju_shouhu"><embed src="/style/car/zj_jin_shouhu01.swf" wmode="transparent" width="500" height="400" class="flash" type=""></div>');
            setTimeout(function(){
                $(".daoju_shouhu").remove();
            }, 5000);
        }else if (data.hasOwnProperty('daoju') && data.daoju != '') {
            // 加载flash较慢，放到最前面
            daojuPic = getDaojuPic(data);
            $("body").prepend('<div  style="position:fixed;z-index:100; bottom:40px;right:0;" class="daoju_yulan"><embed src="/style/car/'+data.daoju+'.swf" wmode="transparent" width="500" height="400" class="flash" type=""></div>');
            setTimeout(function(){
                $(".daoju_yulan").remove();
            }, 10000);
        }

        

        var levelPic = getLevelPic(data);
        var vipPic = getVipPic(data);

        if (data.user_id != -1 && (daojuPic != '' || vipPic != '' || levelPic != '')) {
            $("#chat_hall").append("<p>"+data.time + vipPic+ levelPic + ' <font color="#FF0E0E"><a style="color:#ccc;" onclick="resizeManageBox();chatHallMange(this);" user_id="'+data.user_id+'" client_id="'+data.client_id+'" title="'+data.client_name+'">'+data.client_name+"</a>"+daojuPic +' 进入房间<br/></p>');
        }
    }

    if (!data.hasOwnProperty('client_list')) {
        userList[data.client_id] = data.client_name;
        // $("#chat_userlist").append('<li id="'+data.client_id+'" onclick="javascript:addPriChat(this);">'+vipPic+levelPic+data.client_name+'</li>');
        if (data.hasOwnProperty('role')) {
            // adminerList[data.client_id] = data.client_name;
            $("#lm2_1").children().text(parseInt($("#lm2_1").children().text())+1);
            insertExactPos('adminer',data);
            // $("#content2_1").append('<li id="online_'+data.user_id+'" tid="'+data.user_id+'" title="'+data.client_name+'" client_id="'+data.client_id+'" ucuid="'+data.ucuid+'" onclick="UserListCtrl.chatPublic();" class="'+className+'"><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+data.ucuid+'&size=middle">'+vipPic+levelPic+'<span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+data.client_name+'</a></p></li>');        
            return ;
        }
        $("#lm2_2").children().text(parseInt($("#lm2_2").children().text())+1);
        insertExactPos('client', data);
        // $("#content2_2").append('<li id="online_'+data.user_id+'" tid="'+data.user_id+'" title="'+data.client_name+'" client_id="'+data.client_id+'" ucuid="'+data.ucuid+'" onclick="UserListCtrl.chatPublic();" class="'+className+'"><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+data.ucuid+'&size=middle">'+vipPic+levelPic+'<span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+data.client_name+'</a></p></li>');
    } else {
        userInfo['client_id'] = data.client_id;
        userInfo['client_name'] = data.client_name;
        userInfo['user_id'] = data.user_id;
        userInfo['ucuid'] = data.ucuid;
        userInfo['vip'] = data.vip;
        userInfo['role'] = '';
        var incrAdminer = incrUser = 0;
        if (data.hasOwnProperty('role') && data.role == 'adminer') {
            // $("#content2_1").append('<li id="online_'+data.user_id+'" tid="'+data.user_id+'" title="'+data.client_name+'" client_id="'+data.client_id+'" onclick="UserListCtrl.chatPublic();" class=""><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+data.ucuid+'&size=middle"><span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+data.client_name+'</a></p></li>');            
            // incrAdminer = 1;
            if ($("#online_"+data.user_id).length == 0) {
                $("#lm2_1").children().text(parseInt($("#lm2_1").children().text())+1);
                userInfo.role = 'adminer';
                ChatMessage.add_user_adminer(data);
            }
        } else {
            insertExactPos('client',data);
            // $("#content2_2").append('<li id="online_'+data.user_id+'" tid="'+data.user_id+'" title="'+data.client_name+'" client_id="'+data.client_id+'" ucuid="'+data.ucuid+'" onclick="UserListCtrl.chatPublic();" class="'+className+'"><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+data.ucuid+'&size=middle">'+vipPic+levelPic+'<span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+data.client_name+'</a></p></li>');
            incrUser = 1;
        }

        var onLineClient = {};
        for(var client_id in data.adminer_list) {
            onLineClient = data.adminer_list[client_id];
            if ($("#online_"+onLineClient.user_id).length) {
                continue ;
            }
            incrAdminer++;
            userList[client_id] = onLineClient.client_name;
            // adminerList[client_id] = onLineClient.client_name;
            // var vipPic = getVipPic(onLineClient);
            // var levelPic = getLevelPic(onLineClient);
            insertExactPos('adminer',onLineClient);
            // $("#content2_1").append('<li id="online_'+onLineClient.user_id+'" tid="'+onLineClient.user_id+'" title="'+onLineClient.client_name+'" client_id="'+onLineClient.client_id+'" ucuid="'+data.ucuid+'" onclick="UserListCtrl.chatPublic();" class="'+className+'"><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+onLineClient.ucuid+'&size=middle">'+vipPic+levelPic+'<span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+onLineClient.client_name+'</a></p></li>');
            // if (client_id != userInfo['client_id']) {
                // $("#chat_userlist").append('<li id="'+client_id+'" onclick="javascript:addPriChat(this)">'+onLineClient.client_name+'</li>');
            // }    
        }
        for(var client_id in data.client_list) {
            onLineClient = data.client_list[client_id];
            if ($("#online_"+onLineClient.user_id).length) {
                continue ;
            }
            incrUser++;
            userList[client_id] = onLineClient.client_name;
            // var vipPic = getVipPic(onLineClient);
            // var levelPic = getLevelPic(onLineClient);
            insertExactPos('client',onLineClient);
            // $("#content2_2").append('<li id="online_'+onLineClient.user_id+'" tid="'+onLineClient.user_id+'" title="'+onLineClient.client_name+'" client_id="'+onLineClient.client_id+'" ucuid="'+data.ucuid+'" onclick="UserListCtrl.chatPublic();" class="'+className+'"><img style="width:44px" class="tou_xiang" src="/passport/avatar.php?uid='+onLineClient.ucuid+'&size=middle">'+vipPic+levelPic+'<span id="tt_592" style="width:53px; height:32px;position:absolute; left:70px;"></span><p> <a>'+onLineClient.client_name+'</a></p></li>');
            // if (client_id != userInfo['client_id']) {
                // $("#chat_userlist").append('<li id="'+client_id+'" onclick="javascript:addPriChat(this)">'+onLineClient.client_name+'</li>');
            // }
        }
        $("#lm2_1").children().text(parseInt($("#lm2_1").children().text())+incrAdminer);
        $("#lm2_2").children().text(parseInt($("#lm2_2").children().text())+incrUser);
    }
}
function del_user_logout(msg) {
    userId = msg.user_id;
    client_id = msg.from_client_id;
    if ($("#online_"+userId).parent().attr('id') == 'content2_2') {
        $("#lm2_2").children().text(parseInt($("#lm2_2").children().text())-1);
    } else if ($("#online_"+userId).parent().attr('id') == 'content2_1') {
        $("#lm2_1").children().text(parseInt($("#lm2_1").children().text())-1);
    }
    delete userList[client_id];
    $("#"+client_id).remove();
    $("#online_"+userId).remove();
}

function addPriChat(obj) {
    var to_client_id = obj.id;
    var to_client_name = obj.textContent;
    $("#playerBox1").hide();
    $("#msg_to_all").text(to_client_name);
    $("#to_user_id").val(to_client_id);
    $("#to_nickname").val(to_client_name);
    $("#whisper").attr('disabled','');
}

function displayGift(msg) {
    var time = msg.time;
    var vipPic = getVipPic(msg);
    var levelPic = getLevelPic(msg);
    var num = parseInt(msg.giftCount);
    var giftImg = '';
    for(var index = 0; index < num && index <= 100 ; index++) {
        giftImg += "<img src='"+msg.giftPath+"'>";
    }
    if (num > 100) {
        giftImg += " ...... ";
    }
    if(msg.userId == userInfo.user_id){
        $("#chat_hall").append("<p>"+time + vipPic + levelPic +" <font style='color:#fff'><a class='msga' onclick='resizeManageBox();chatHallMange(this);' style='color:#35c4ff;' client_id='"+msg.from_client_id+"' user_id='"+msg.from_user_id+"' title='"+msg.from_client_name+"'>"+msg.from_client_name+"</a> 送给主播 "+msg.giftCount+" 个"+msg.giftName+": "+giftImg+"<br/></p>"); 
    }else{
        $("#chat_hall").append("<p>"+time + vipPic + levelPic +" <font color='greenyellow'><a class='msga' onclick='resizeManageBox();chatHallMange(this);' style='color:#35c4ff;' client_id='"+msg.from_client_id+"' user_id='"+msg.from_user_id+"' title='"+msg.from_client_name+"'>"+msg.from_client_name+"</a> 送给主播 "+msg.giftCount+" 个"+msg.giftName+": "+giftImg+"<br/></p>");
    }
    var gifttop=parseInt($('#gift_history li').size()) || 0;
    var gift_history='<li><span>'+msg.giftCount+'个</span>'+'<img src="'+msg.giftPath+'" width="24" height="24">'+'<em>' + msg.giftName + '</em><a title='+msg.from_client_name+' href="javascript:void(0);" target="_blank">'+ (gifttop+1)+ '. ' +msg.from_client_name+'</a></li>';
    $('#gift_history').append(gift_history);
}

function switchFlashEffect(update)
{
    var cookie = document.cookie.split('; ');
    var item ;
    for(var index in cookie) {
        item = cookie[index].split("=");
        if (item[0] == 'playFlashEffect') {
            break;
        }
    }
    if (!update) {
        if (item[0] == 'playFlashEffect') {
            playFlashEffect = parseInt(item[1]);
        } else {
            var exp = new Date(); 
            exp.setTime(exp.getTime() + 30*24*60*60*1000); 
            document.cookie = "playFlashEffect=1; expires="+exp.toGMTString();
        }
    } else {
        playFlashEffect = !playFlashEffect;
        cookie[index] = 'playFlashEffect=' + parseInt(playFlashEffect); 
        var exp = new Date(); 
        exp.setTime(exp.getTime() + 30*24*60*60*1000); 
        document.cookie = 'playFlashEffect=' + (playFlashEffect?1:0)+'; expires='+exp.toGMTString();
    }

    $("#switchFlashEffect b").text( playFlashEffect ? '特效':'特效');
    if(!playFlashEffect){
         $("#switchFlashEffect").find("span").removeClass('on');
     }else{
         $("#switchFlashEffect").find("span").addClass("on");
     }
}
function changeOnlineNum()
{
    var num1 = parseInt($("#lm2_1").children().text());
    var num2 = parseInt($("#lm2_2").children().text())
    $("#onlineNum2").text(num1+num2);
}
function clearOnlineNum()
{
    $("#lm2_1").children().text(0);
    $("#lm2_2").children().text(0)
}
function clearOnlineUser()
{
    $("#content2_1").children().remove();
    $("#content2_2").children().remove();
}

function chatHallMange(obj){
    managedClientUser['client_id'] = $(obj).attr('client_id');
    managedClientUser['client_name'] = $(obj).attr('title');
    managedClientUser['user_id'] = $(obj).attr('user_id');
    // managedClientUser['user_id'] = $('.viewer_list>li[client_id='+client_id+']').attr('id').split('_')[1];
    $("#userperson_title").text(managedClientUser.client_name);
}
function resizeManageBox(e){
    if (userInfo.role != 'adminer') {
        $("#ctrllistperson>li[class=tdeal]").css("display","none");
        $("#ctrllistperson>li[class=dmanage]").css("display","none");
    } else {
        $("#ctrllistperson>li[class=tdeal]").css("display","list-item");
        $("#ctrllistperson>li[class=dmanage]").css("display","list-item");
    }

    var e = e || window.event; 
    var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
    var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
    var x = e.pageX || e.clientX + scrollX;
    var y = e.pageY || e.clientY + scrollY;
    $(".user-Person").css({"top":y-60,"left":x+20,"display":"block"});
}

function isValidOpera()
{
    if (!managedClientUser.hasOwnProperty("client_id") || !managedClientUser.hasOwnProperty('client_name') || !managedClientUser.hasOwnProperty("user_id")) {
        _alert("非法操作！请重新选择要操作的用户!",5);
        return false;
    }
    if (managedClientUser.client_id == userInfo.client_id) {
        _alert("不能操作自己!",3);
        return false;
    }
    return true;
}
function hideChatHallManage()
{
    $(".user-Person").css("display","none");
}

function getAvatar(uid)
{
    return '/style/avatar/'+$.md5(uid).substr(0,3)+'/'+uid+'_middle.jpg';
}