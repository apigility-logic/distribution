String.prototype.trim=function(){ return this.replace(/(^\s*)|(\s*$)/g, ""); }
var WishGiftCtrl={
    choiceGift:function(giftid,giftName) {

        WishGiftCtrl.gift_id=giftid;
        WishGiftCtrl.gift_name=giftName;
        $("#wishGiftName").html(WishGiftCtrl.gift_name);
        $("#wishGiftId").html(WishGiftCtrl.gift_id);
        $('#wish-gift-tip').hide();
        if($("#wishGiftNum").val()=="") {
            $("#wishGiftNum").val(1);
        }
    },
    save:function(num,giftName,type){
        var re=/^[\d]+$/;
        var p;
        if(type==3){
            //p='m=save&type=' + type+ "&custom=" + $("#customWish").val() +"&r="+Math.random() ;
            p = 'type_' + type + '_custom_' + encodeURIComponent($("#customWish").val()) + '_r_' + Math.random() + '.htm';
        }else{
            if(re.test(num)&&parseInt(num)>0){
                if(giftName!=""){
                    p = 'action=save&num='+ num + "&type=" + type +'&giftName=' + giftName +"&r="+Math.random();
                    //p = 'num_'+ num + '_type_' + type + '_giftName_' + giftName + '_r_' + Math.random() + '.htm';
                }else{
                    p = 'action=save&num='+ num + "&type=" + type + "&r="+Math.random();
                    //p = 'num_' + num + '_type_' + type +'_r_' + Math.random() + '.htm';
                }
            }else{
                $(document).trigger('close.JShowTip');    
                _alert("数量不正确，请重新输入",5);
                return false;
            }
            if(type==1&&giftName==""){
                $(document).trigger('close.JShowTip');        
                _alert("请选择礼物",5);
                return false;
            }
        }
         $.ajax({
             type:'post',
             dataType:"json",
             url:'/my/wishing/',
             data:p,
             success:function(data){
                if(data.wishedFlag==1){
                    _alert("许愿成功!",3);
                    
                    var wishContent;
                    if(data.wishType==1){
                        wishContent = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">' + data.count + '</strong>个' + data.giftName + '</p>';
                    }
                    if(data.wishType==2){
                        wishContent = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">' + data.count + '</strong>人热捧</p>';
                    }
                    if(data.wishType==3){
                        wishContent = '<strong class="p1">我的心愿：</strong>' + wishJson.custom + '</p>';
                    }
                    $("#wishImitation").html(wishContent);
                    Dom.$swfId("flashCallChat")._chatToSocket(0, 2, '{"_method_":"makeWish","wishType":"' + data.wishType + '","count":"' + data.count + '","giftName":"' + data.giftName + '"}');
                }else{
                    _alert("许愿不成功！您的虚拟币余额不足"+ data.price,5);
                }
                
             }
         });
        $(document).trigger('close.JShowTip');        
    },
    makeWish:function(){
        $.ajax({
             type:'post',
             url:'/my/wishing/',
             data:'action=isWished&r='+Math.random(),
             dataType:'text',
             async: false,
             success:function(data) {
                if(data==1){
                     _alert("今天已许过愿！", 3);
                 }else{
                     $.JShowTip({centerTip:$('#giveBox')});
                 }
                 
             }
         });    
    },
    //公告内容回写
    getLastestBulletin:function(bulletinId,familyId){
        $.ajax({
            type:'post',
             url:'/bulletin.do',
             cache: false,
             data:'m=getLastestBulletin&bulletinId='+bulletinId+'&familyId='+familyId,
             dataType:'html',
             async: false,
             success:function(data) {
                 if(data == 0){
                     _alert("公告回写失败！", 3);
                 }else{
                     $("#bulletin").html('<em style="display:block;height:45px;">'+data+'</em>');
                 }
             }
         });
    },
    
    //接收公告保存请求
    saveFamilyBulletin:function(familyId,context,bulletinId){
        //公告关键字过滤
        var con=context.trim();
        if(con.length>40){
            _alert("公告字数不能超过40字", 10);
            return false;
        }else{
          var thiss=this;
          $.ajax({
            type:'post',
            url:'/family.do',
            data:'m=isIllegalWords&context='+context,
            dataType:'text',
            contentType:"application/x-www-form-urlencoded;charset=utf-8",
            async: false,
            success:function(response){
                if(response == 1){
                    _alert("公告包含非法字符！", 3);
                    $("#bulletinContext").val("");
                }else if(response==0){
                    var that=thiss;
                    $.ajax({
                         type:'post',
                         url:'/bulletin.do',
                         data:'m=saveFamilyBulletin&familyId='+familyId+'&context='+context+'&bulletinId='+bulletinId,
                         dataType:'text',
                         contentType:"application/x-www-form-urlencoded;charset=utf-8",
                         async: false,
                         success:function(bulletinId) {
                             if(bulletinId != 0){
                                 //执行回调函数取出修改后的公告信息并展示
                                 that.getLastestBulletin(bulletinId,familyId);
                                 _alert("编辑成功！", 3);
                             }else{
                                 _alert("编辑失败！", 3);
                             }
                            window.setTimeout(function(){ $("#giveBox .close").click(); }, 3000);
                         }
                     });
                }
            }
        });
     }
    },
    
    saveQuitFamilyReason:function(familyId,quitReason){
        $.ajax({
            type:'post',
            url:'/family.do',
            data:'m=ajax4ApplyQuitFamily&familyId='+familyId+'&quitReason='+quitReason,
            dataType:'text',
            async: false,
            success:function(data){
                var json = eval("(" + data + ")");
                window.setTimeout(function(){ $("#giveBoxQuitFamily .close").click(); }, 1000);
                _alert(json.info,5);
             }
        });
    },
    
    firstPageSave:function(num,giftId,type){
        var re=/^[\d]+$/;
        var p;
        
        if(type==3){
            //p='m=save&type=' + type+ "&custom=" + $("#customWish").val() +"&r="+Math.random() ;
            p = 'type_' + type + '_custom_' + encodeURIComponent($("#customWish").val()) + '_r_' + Math.random() + '.htm';
        }else{
            if(re.test(num)&&parseInt(num)>0){
                if(giftId!=""){
                    //p = 'm=save&num='+ num + "&type=" + type +'&giftId=' + giftId +"&r="+Math.random();
                    p = 'num_'+ num + '_type_' + type + '_giftId_' + giftId + '_r_' + Math.random() + '.htm';
                }else{
                    //p = 'm=save&num='+ num + "&type=" + type + "&r="+Math.random();
                    p = 'num_' + num + '_type_' + type +'_r_' + Math.random() + '.htm';
                }
            }else{
                $(document).trigger('close.JShowTip');    
                _alert("数量不正确，请重新输入",5);
                    
                return false;
            }
            if(type==1&&giftId==""){
                $(document).trigger('close.JShowTip');    
                _alert("请选择礼物",5);
                return false;
            }
        }
         $.ajax({
             type:'post',
             url:'wishing_save_' + p,
             //url:'/wishing.do',
            // data:p,
             success:function(data){
              var json = eval("(" + data + ")");
                if(json.wishedFlag==1){
                    _alert("许愿成功!",3);
                    var wishContent;
                    var wishJson = json.rows;
                    if(wishJson.wishType==1){
                        wishContent = '<p class="t4">我的心愿：我今天希望得到<strong>'+ wishJson.count +'</strong>个'+ wishJson.giftName +'</p>';
                    }
                    if(wishJson.wishType==2){
                        wishContent = '<p class="t4">我的心愿：我今天希望得到<strong>'+ wishJson.count +'</strong>人热捧</p>';
                    }
                    if(wishJson.wishType==3){
                        wishContent = '<p class="t4">我的心愿：'+ wishJson.custom +'</p>';
                    }
                    $("#wishImitation").html(
                       '<div class="wish">'+
                            '<div class="wishtip">'+
                                wishContent+
                         '</div>'+
                       '</div>'
                    );
                }else{
                    _alert("许愿不成功！您的秀币余额不足"+ json.price,5);
                }
                
             }
         });
         $(document).trigger('close.JShowTip');        
    },
    Countdown:function() {  
           var date  = new Date();
          date.setDate(date.getDate()+1);
          var tomorrow = DateUtil.formateDate("yyyy/MM/dd",date);
          SysSecond = (Date.parse(tomorrow)-Date.parse(new Date()))/1000;//这里获取倒计时的起始时间  
          InterValObj = window.setInterval(this.SetRemainTime, 1000); //间隔函数，1秒执行  
     },
     
     //将时间减去1秒，计算天、时、分、秒  
     SetRemainTime:function() {  
          if (SysSecond > 0) {  
               SysSecond = SysSecond - 1;  
               //alert(String.format("%03d", 1)); 
               var second = Math.floor(SysSecond % 60);
               second = '%.2S'.sprintf(second);            // 计算秒      
               var minite = Math.floor((SysSecond / 60) % 60);      //计算分 
               minite = '%.2S'.sprintf(minite); 
               var hour = Math.floor((SysSecond / 3600) % 24);      //计算小时  
               hour = '%.2S'.sprintf(hour);
              // var day = Math.floor((SysSecond / 3600) / 24);        //计算天  
              //$("#remainTime").html(day + "天" + hour + "小时" + minite + "分" + second + "秒"); 
               $("#surplusTime").html(hour + ":" + minite + ":" + second);  
          } else {//剩余时间小于或等于0的时候，就停止间隔函数  
                  window.clearInterval(InterValObj);  
           //这里可以添加倒计时时间为0后需要执行的事件  
          }  
     }
}