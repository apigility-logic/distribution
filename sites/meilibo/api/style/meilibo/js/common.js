/**
 * 判断浏览器
 */
var Sys={};
var Gift_obj={};
var Gift_numobj={};
var ua=navigator.userAgent.toLowerCase();
Sys.ie=(s=ua.match(/msie ([\d.]+)/)) ? true : false;
Sys.ie6=(s=ua.match(/msie ([0-6]\.+)/)) ? s[1] : false;
Sys.ie7=(s=ua.match(/msie ([7]\.+)/)) ? s[1] : false;
Sys.ie8=(s=ua.match(/msie ([8]\.+)/)) ? s[1] : false;
Sys.firefox=(s=ua.match(/firefox\/([\d.]+)/)) ? true : false;
Sys.chrome=(s=ua.match(/chrome\/([\d.]+)/)) ? true : false;
Sys.opera=(s=ua.match(/opera.([\d.]+)/)) ? s[1] : false;
Sys.safari=(s=ua.match(/version\/([\d.]+).*safari/)) ? s[1] : false;
Sys.ie6&&document.execCommand("BackgroundImageCache",false,true);
Sys.ispro="";//是否推广url过来
String.prototype.hasString=function(a){
    if(typeof a=="object"){
        for(var b=0,c=a.length;b<c;b++)
           if(!this.hasString(a[b]))
                 return false;
            return true
    }else if(this.indexOf(a)!=-1)
        return true
};

/**
 * 计算位置
 */
var dom=document.documentElement || document.body;
var oPos={
    width:function(a){return parseInt(a.offsetWidth)},
    height:function(a){return parseInt(a.offsetHeight)},
    pageWidth:function(){return document.body.scrollWidth||document.documentElement.scrollWidth},
    pageHeight:function(){return document.body.scrollHeight||document.documentElement.scrollHeight},
    windowWidth:function(){var a=document.documentElement;return self.innerWidth||a&&a.clientWidth||document.body.clientWidth},
    windowHeight:function(){var a=document.documentElement;return self.innerHeight||a&&a.clientHeight||document.body.clientHeight},
    scrollX:function(){
      var b=document.documentElement;
      return self.pageXOffset||b&&b.scrollLeft||document.body.scrollLeft
    }
    ,scrollY:function(){
      var b=document.documentElement;
      return self.pageYOffset||b&&b.scrollTop||document.body.scrollTop
    },
    popW:function(){return Math.max(dom.clientWidth,dom.scrollWidth)},
    popH:function(){return Math.max(dom.clientHeight,dom.scrollHeight)}
}
var mousePosition=function(e){
  var e=e || window.event;
  return {x:e.clientX+oPos.scrollX(),y:e.clientY+oPos.scrollY()}
};

/**
 * 获取layer居中的位置
 */
var getMiddlePos=function(obj){
    this.objPop=obj;
    this.winW=oPos.windowWidth();  
    this.winH=oPos.windowHeight();  
    this.dScrollTop=oPos.scrollY();
    this.dScrollLeft=oPos.scrollX();
    this.dWidth=$('#'+this.objPop).width(),dHeight=$('#'+this.objPop).height();
    this.dLeft=(this.winW/2)-(this.dWidth)/2+this.dScrollLeft;
    this.dTop=(this.winH/2)-(this.dHeight/2)+this.dScrollTop;
    return {"pl":this.dLeft,'pt':this.dTop};
}

/**
 * read html ? param
 * @param strName:需要获取参数的名字
 * @return string
 */
function getParam(strName)
{
    var arg=arguments[1]?arguments[1]:"";
    var strHref=location.href;
    if(arg!=""){
        strHref=arg;    
    }
    var intPos=strHref.indexOf("?");
    var strRight=strHref.substr(intPos + 1);
    var arrTmp=strRight.split("&");
    for(var i=0;i<arrTmp.length;i++)
    {
        var arrTemp=arrTmp[i].split("=");
        if(arrTemp[0].toUpperCase()==strName.toUpperCase()) return arrTemp[1];
    }
    return "";
}
/**
 * jump location.href
 * @param urlfile:跳转URL
 */
var jump=function(urlfile)
{
    window.location.href=urlfile;
    return false;
}
/**
 * 将JSON字符串转换成object
 * @param evalJSON:json string
 * @return json object
 * */
function evalJSON(strJSON)
{
    if (typeof(strJSON)=='undefined'||strJSON=='')
        return false;

    return eval("("+ strJSON +")");
}

/**
 * 是否在数组内
 */
function in_array(searcher,sArray)
{
    for(var i=sArray.length;i--;)
    {
        if(searcher==sArray[i])// TODO 应该用===
        {
            return true;
        }
    }
    return false;
}

function jmsgPop(txt,showtime){
    var box=$('#giveBox');
    if(txt){$('#pop-text').html(txt);}
    var alertPop=getMiddlePos('giveBox');
    var vl=alertPop.pl;
    var vt=alertPop.pt;
    box.css({"left":vl+"px","top":"40%"}).show(); 
    $('#pop-close,#pop-btnclose').click(function(){
        box.hide();
    })
    if(isNaN(showtime)){
        box.hide();
    }else{
        window.setTimeout(function(){
            box.hide();                       
        },showtime*1000)
    }
}

function addEvent(obj,type,fn){
    if (obj.attachEvent){
        obj['e'+type+fn]=fn;
        obj[type+fn]=function(){
            obj['e'+type+fn](window.event);
        }
        obj.attachEvent('on'+type,obj[type+fn]);
    } else
     obj.addEventListener(type,fn,false);
}
function removeEvent(obj,type,fn){
    if (obj.detachEvent){
        obj.detachEvent('on'+type,obj[type+fn]);
        obj[type+fn]=null;
    } else
     obj.removeEventListener(type,fn,false);
}
/*
 * jQuery.JShowTip({centerTip:$('#signuplogin_tip')}); 居中显示POP Tip
 * $(document).trigger('close.JoyShowTip')
 * */
(function($){
          $.JShowTip=function(data){$.JShowTip.loading(data);}
          $.extend($.JShowTip,{
              settings:{opacity:'0.2'},
              loading:function(_tarwap){
                            showOverlay();
                            _tarwap.centerTip.css({
                                "left":$(window).width()/2-Math.ceil($('.poptip').width()/2),
                                "top":oPos.scrollY()+(oPos.windowHeight()/3),
                                "z-index":100
                            }).show();
                            if(_tarwap.param){
                                var param_obj=_tarwap.param;
                                for(var key in param_obj[0]){
                                    $('#'+key).html(param_obj[0][key]);
                                }
                            }
                            $(document).bind('keydown',function(e){
                                if(e.keyCode==27){//esc 关闭
                                    $(document).trigger('close.JShowTip');
                                }
                            });
                       }
          });
          function showOverlay(){
                $('body').append('<div id="face_overlay"></div>');
                $('#face_overlay').hide().addClass('face_overlayBG')
                    .css({opacity:$.JShowTip.settings.opacity,height:oPos.popH()})
                    .click(function(){
                        $(document).trigger('close.JShowTip');
                }).fadeIn(200);
                $('.close').click(function(){
                    $(document).trigger('close.JShowTip');                           
                })
          }
          function hideOverlay(){
                $this=$('.poptip,.p-Song');
                $this.fadeOut(200,function(){
                    $('#face_overlay').remove();
                });
          }
          $(document).bind('close.JShowTip',function(){
                $(document).unbind('keydown');
                hideOverlay();
          });
})(jQuery);

/**  
    标签
*/
function turn(n,m,x){
    for(i=1;i<=m;i++){
        if(i==n){
            jQuery('#lm'+x+'_'+i).addClass("on");
            jQuery('#content'+x+'_'+i).css("display","block");    
             
        }else{
            jQuery('#lm'+x+'_'+i).removeClass("on");
            
            jQuery('#content'+x+'_'+i).css("display","none");
            
        }
    }

}

/**
 * 滚动
 * @name HtmlMove
 * @grammar HtmlMove("play_triger","cf","scrollLeft","play_pre","play_next");
 * @param {string}     fid         父容器节点
 * @param {string}     fnodename         要遍历的节点
 * @param {string}     path         滚动方向
 * @param {string}     prebtn         往上滚 按钮 ID
 * @param {string}     nextbtn     往下滚 按钮 ID
 */
var HtmlMove=function(fid,fnodename,path,prebtn,nextbtn){
    var h=$('#'+fid),pre=$('#'+prebtn),next=$('#'+nextbtn),pageCount=h.find('.'+fnodename).size(),intIndex,scrollW=0,scrollval;
    pre.addClass('dis');
    intIndex=0;
    if(path=='scrollLeft'){
        var oneset=parseInt(h.find('.'+fnodename).eq(0).width());
    }else{
        var oneset=parseInt(h.find('.'+fnodename).eq(0).height());
    }
    if(pageCount==1){
        pre.addClass('dis');next.addClass('dis');
    }else{
        pre.addClass('dis');next.removeClass('dis');
    }
    var dur=300;
    pre.click(function(){
        if(intIndex>0){changePage(intIndex-1);}
    })
    next.click(function(){
        if(intIndex<pageCount-1){changePage(intIndex+1);}
    })
    function changePage(pageNum){
        intIndex=pageNum;
        if(intIndex>0 && intIndex<pageCount-1){
            pre.removeClass('dis');
            next.removeClass('dis');
        }else if(intIndex==0){
            pre.addClass('dis');
            next.removeClass('dis')    
        }else{
            next.addClass('dis');pre.
            removeClass('dis');    
        }
        scrollval=parseInt(intIndex*oneset);
        if(path=='scrollLeft'){
            h.stop().animate({'scrollLeft':scrollval},dur,'linear');
        }else{
            h.stop().animate({'scrollTop':scrollval},dur,"linear");
        }
        
    }
}

/*
  图片滚动
 */    
//滚动/切屏效果，参数说明:[id,子容器/孙容器,方向,速度,上按钮,下按钮,分页切换时间,每次切屏的条数]
function HtmlMove2(id,tag,path,upbt,downbt,pgtime,lis){
        var c,mous=false,fg=tag.split('/'),o=$('#'+id),as=o.find(fg[1]),fx=(path=='scrollRight'||path=='scrollLeft')?'scrollLeft':'scrollTop',
        ow=fx=='scrollTop'?as.eq(0).get(0).offsetHeight:as.eq(0).get(0).offsetWidth;
        o.hover(function(){mous=true;},function(){mous=false;})
        var pgsize=as.size();
        var pw=fx=='scrollTop'?o.height():o.width(),pgli=lis||Math.floor((pw+ow/2)/ow),pg=Math.floor((pgsize+(pgli-1))/pgli),pgmx=ow*pgli,now=0,mx,d;
        var os=o.find(fg[0]).eq(0),dur=600;    
        o.find(fg[0]).append(os.html());    
        if(pgtime==null){$('#'+upbt).click(function(){go_to(true);});$('#'+downbt).click(function(){go_to(false);});}else{d=setInterval(function(){go_to((path=="scrollTop"||path=="scrollLeft")?true:false);},pgtime);$('#'+upbt).click(function(){clearInterval(d);go_to(true);d=setInterval(function(){go_to(true);},pgtime);});$('#'+downbt).click(function(){clearInterval(d);go_to(false);d=setInterval(function(){go_to(false);},pgtime);});}    
         function go_to(fxs){
                 if(mous){return;};
                if(fxs){
                        if(now<pg){
                            now++;
                        }else{
                            now=1;
                            if(fx=='scrollTop'){
                                o.scrollTop(0);
                            }else{
                                o.scrollLeft(0);    
                            }
                        }
                        mx=now*pgmx;
                        if(fx=='scrollTop'){
                            o.stop().animate({'scrollTop':mx},dur,'linear');
                        }else{
                            o.stop().animate({'scrollLeft':mx},dur,'linear');
                        }
                        
                }else{
                    if(now>0){
                        now--;
                    }else{
                        now=pg-1;
                        if(fx=='scrollTop'){
                                o.scrollTop(pg*pgmx);
                        }else{
                                o.scrollLeft(pg*pgmx);    
                        }
                    }
                    mx=now*pgmx;
                    if(fx=='scrollTop'){
                        o.stop().animate({'scrollTop':mx},dur,'linear');
                    }else{
                        o.stop().animate({'scrollLeft':mx},dur,'linear');
                    }
                }
            }
}
/**
    焦点图
*/
var FocusImages=function(){
    var FocusImage=arguments[0];
    this.id=jQuery(FocusImage.id);
    this.imgW=parseInt(FocusImage.imgW);
    this.imgH=parseInt(FocusImage.imgH);
    this.speed=parseInt(FocusImage.speed)||3000;
    this.focusData=arguments;
    this.autoPlay;
    this.links="";
};

var carouselImages=function(){
    var carouselImage=arguments[0];
    this.id=jQuery(carouselImage.id);
    this.imgW=parseInt(carouselImage.imgW);
    this.imgH=parseInt(carouselImage.imgH);
    this.speed=parseInt(carouselImage.speed)||3000;
    this.focusData=arguments;
    this.autoPlay;
    this.links="";
};

carouselImages.prototype={    
    init:function(){
        var that=this;
        var strSpan='',strImg='';
        for(var i=1;i<that.focusData.length;i++){
            strSpan+="<span></span>";
            strImg+='<img src="'+that.focusData[i][0]+'" width="'+this.imgW+'" height="'+this.imgH+'" style="display:block;">';
        };
        that.id.find('.sildetab').html(strSpan);
        that.id.find('.slideimg1').html(strImg);
        this.spring();
    },
    spring:function(){
        var that=this,_tab=that.id.find(".sildetab span");
        var gogo=function(i){
            var _index=i||1;
            that.alternation(_index);
            that.autoPlay=setInterval(function(){
                that.alternation(_index==that.focusData.length-1?_index=1:++_index);
            },that.speed);

        };
        gogo();        
        _tab.each(function(i){
            (function(i){
                _tab.eq(i).click(function(){
                    if(jQuery(this)[0].className!="on"){
                        clearTimeout(that.autoPlay);
                        gogo(i+1)
                    }
                })
            })(i)
        });
        this.id.find(".slideimg1").click(function(){window.open(that.links);})
    },
    alternation:function(i){
        var that=this;
        that.id.find(".sildetab span.on").removeClass();
        that.id.find(".sildetab span").eq(i-1).addClass("on");
        var slideImg=that.id.find("img");
        slideImg.css({display:"none",opacity:1});
        slideImg.eq(i-1).css({display:"block",opacity:0}).stop().animate({opacity:1},300);
        that.links=that.focusData[i][1];
    }
};
/*===========top menu begin===========*/
$('.mycenter').live("mouseenter",function(){
    $(this).find('.mysetting').show();
}).live("mouseleave",function(){
    $(this).find('.mysetting').hide();
})

FocusImages.prototype={    
    init:function(){
        var that=this;
        var strSpan='',strImg='';
        for(var i=1;i<that.focusData.length;i++){
            strSpan+="<span></span>";
            strImg+='<img src="'+that.focusData[i][0]+'" width="'+this.imgW+'" height="'+this.imgH+'" style="display:none;">';
        };
        that.id.find('.sildetab').html(strSpan);
        that.id.find('.slideimg').html(strImg);
        this.spring();
    },
    spring:function(){
        var that=this,_tab=that.id.find(".sildetab span");
        var gogo=function(i){
            var _index=i||1;
            that.alternation(_index);
            that.autoPlay=setInterval(function(){
                that.alternation(_index==that.focusData.length-1?_index=1:++_index);
            },that.speed);
        };
        gogo();        
        _tab.each(function(i){
            (function(i){
                _tab.eq(i).click(function(){
                    if(jQuery(this)[0].className!="on"){
                        clearTimeout(that.autoPlay);
                        gogo(i+1)
                    }
                })
            })(i)
        });
        this.id.find(".slideimg").click(function(){window.open(that.links);})
    },
    alternation:function(i){
        var that=this;
        that.id.find(".sildetab span.on").removeClass();
        that.id.find(".sildetab span").eq(i-1).addClass("on");
        var slideImg=that.id.find("img");
        slideImg.css({display:"none",opacity:1});
        slideImg.eq(i-1).css({display:"block",opacity:0}).stop().animate({opacity:1},300);
        that.links=that.focusData[i][1];
    }
};
/*===========top menu begin===========*/
$('.mycenter').live("mouseenter",function(){
    $(this).find('.mysetting').show();
}).live("mouseleave",function(){
    $(this).find('.mysetting').hide();
})
/*============top menu end============*/
/*===========================right Menu begin=====================================*/
function isInRegion(a,x,y){
    try{
        var b=a.offset();
        var c=b.left;
        var d=b.top;
        var e=d + a.height();
        var f=c + a.width();
        return x > c && x < f && y > d && y < e;
    }catch(e){}
}
$('.hover_des li').live("mouseenter",function(){
        $(this).addClass("hover");
    }).live("mouseleave",function(){
        $(this).removeClass("hover");
    });
function set_contextmenu(a,c,d){
    var e=$('#hoverPerson');
    var f=c.offset();
    var g=d == null || d=='right'?f.left-e.width()-8:f.left+c.width()-10;
    $('#person_title').text(a);
    e.css("left", g).css("top", f.top).show();
}
$('.viewer_list li').live("mouseenter",
    function(e){
        if(_show.userId==""){return false;}
        var vl=$('#operson');
        if($('#operson').is(":visible")){return;}
         var a=$(this),that=$(this);
         var paramid=that.attr("tid"), utype=that.attr("utype"), partle=that.attr("title"), level=that.attr("richlevel"), gnum=that.attr('goodnum'), layer=that.attr("layer");
         if(!paramid || utype==20) return;
         if(paramid=='loading_manage' || paramid=='loading_online'){
            return false;
         }else{
            var t=that.offset().top,l=that.offset().left;
            vl.css({top:t,left:l+132}).show();
            $('#person_title').text(partle);
            $('.enterroom').attr({'href':'/'+gnum,'title':partle});
            if( (_show.role&1)>0 || (_show.role&2)>0 || (_show.role&4)>0 ){
                if (paramid==_show.userId_1){
                    $('#setMike').html('<li onclick="UserListCtrl.downMike(1);">下1麦</li><span class="menuline" style="display:block;"></span>');
                }else if (paramid==_show.userId_2){
                    $('#setMike').html('<li onclick="UserListCtrl.downMike(2);">下2麦</li><span class="menuline" style="display:block;"></span>');
                }else if (paramid==_show.userId_3){
                    $('#setMike').html('<li onclick="UserListCtrl.downMike(3);">下3麦</li><span class="menuline" style="display:block;"></span>');
                }else{
                    var ctrlstr = "";
                    if(_show.showId_1<=0){
                        ctrlstr += '<li onclick="UserListCtrl.upMike(1);">上1麦</li>';
                    }
                    if(_show.showId_2<=0){
                        ctrlstr += '<li onclick="UserListCtrl.upMike(2);">上2麦</li>';
                    }    
                    if(_show.showId_3<=0){
                        ctrlstr += '<li onclick="UserListCtrl.upMike(3);">上3麦</li>';
                    }
                    if(ctrlstr!=""){
                        $('#setMike').html(ctrlstr + "<span class=\"menuline\" style=\"display:block;\"></span>");
                    }else{
                        $('#setMike').html("");
                    }
                }
            }
            UserListCtrl.user_id=paramid;
            UserListCtrl.nickname=partle;
            UserListCtrl.level=level;
            UserListCtrl.layer=layer;
            //20120829
            UserListCtrl.goodnum=that.attr('goodnum');
         }
        if(a.parent("ul").attr("id")=="view2_1" && ((_show.role&1)>0 || (_show.role&2)>0 || (_show.role&4)>0)){ if($("#setMike")){ $("#setMike").show(); } }
        else{ if($("#setMike")){ $("#setMike").hide(); } }
        var c=partle;
        a.addClass("hover");
        set_contextmenu(c,a);
    }).live("mouseleave",function(e){
        var a=$(this);
        a.removeClass("hover");
        if(isInRegion(a,e.pageX,e.pageY)){
            return false;
        }
        if(!isInRegion($('#operson'),e.pageX,e.pageY)){ $('#operson').hide(); }
        if(!isInRegion($('#hoverPerson'),e.pageX,e.pageY)){ $('#hoverPerson').hide(); }
    });
$('#hoverPerson,#operson').live("mouseenter",function(e){
    var a=$(this);
    p=0;
    a.show();
}).live("mouseleave",function(e){
    $('.viewer_list').find("li[class='hover']").removeClass("hover");
    if (!isInRegion($('.viewer_list').find("li"),e.pageX,e.pageY)){ $('#operson').hide(); $('#hoverPerson').hide(); }
});
/*===========================right Menu end=======================================*/
/*===========================chat Menu begin=======================================*/
$('.chatuser').live('click',function(){
     var that=$(this);
     var chatmenu=$('#hoverPerson');
     var uid=that.attr("id");
     if(uid!=0 && uid>0){
         if($("#setMike")){ $("#setMike").hide(); }
         var gnum=that.attr('gn'),title=that.html();
         if(gnum==""){gnum="show_goToRoom_uid_"+uid+".htm"}
         var objuser=uid+"@@@"+title+"@@@"+gnum;
         var arrchat=objuser.split("@@@"),userid=arrchat[0],username=arrchat[1],gnum=arrchat[2],t=that.offset().top,l=that.offset().left;
         chatmenu.css({top:t+18,left:l-26}).show();
         if(_show.emceeId==_show.userId || _show.sa==1 || _show.admin==1){$('.tdeal').show();}
         if(_show.admin==1 || _show.emceeId==_show.userId){$('.dmanage').show();}
         $('#person_title').html(username);
         $('.enterroom').attr({'href':'/'+gnum,'title':username});
         UserListCtrl.user_id=userid;
         UserListCtrl.nickname=username;
         UserListCtrl.level=0;
         //20120829
         UserListCtrl.goodnum=that.attr('gn');
         UserListCtrl.chatPublic();
     }
});
/* $('#hoverPerson').live('mouseleave',function(){$(this).hide();}) */
/*===========================chat Menu end=========================================*/
/**
 * Drag
 */
function Drager(drag,left,right,path,minDist,maxDist){
    this.drag=document.getElementById(drag);
    this.left=document.getElementById(left);
    this.right=document.getElementById(right);
    this.path=path;
    this.minDist=minDist;
    this.maxDist=maxDist;
    this.lDist=(this.path=='v') ? this.left.clientHeight:this.left.clientWidth;
    this.rDist=(this.path=='v') ? this.right.clientHeight:this.right.clientWidth;
    this.start=false;
    this.location=null;
    this.step=null;
    var _this=this;
    this.doDrag=function _doDrag(e){
        _this.draging.call(_this,e||window.event);
    };
    this.doBegin=function _doBegin(e){
        _this.beginDrag.call(_this,e||window.event);
    };
    this.doEnd=function _doEnd(e){
        _this.endDrag.call(_this,e||window.event);
    };
    addEvent(this.drag,'mousedown',this.doBegin);
}
Drager.prototype={
    beginDrag:function(){
        this.start=true;
        document.onselectstart=function(e){
            return false;
        };
        document.body.onselectstart=function(e){
            return false;
        }
        window.document.body.className='play drag';
        addEvent(window.document.body,'mouseover',this.doDrag);
        addEvent(window.document.body,'mouseup',this.doEnd);
    },
    endDrag:function(){
        this.start=false;
        document.onselectstart=function(e){
            return true;
        };
        document.body.onselectstart=function(e){
            return true;
        }
        if (this.start){
            removeEvent(window.document.body,'mouseover',this.doDrag);
            removeEvent(window.document.body,'mouseup',this.doEnd);
        }
        this.start=false;
    },
    draging:function(e){
        if(this.start){
            //alert(1)
            var e=e||window.event;
            if(!this.location){
                this.location=(this.path=='v')?e.clientY:e.clientX;
            }else{
                this.step=this.location-e.clientY;
                if((this.step >= 0 && this.left.clientHeight < this.minDist) || (this.step <= 0 && this.left.clientHeight > this.maxDist)){return false;}
                this.lDist-=this.step;
                this.rDist+=this.step;
                this.left.style.height=this.lDist+'px';
                this.right.style.height=this.rDist+'px';
                this.location=e.clientY;
            }
        }
        return false;
    }
};

function _closePop(){
    var box=$('#alertBox');
    box.hide();
    return false;
}
/** 
 * 调用各种提示层 信息 Demo: onclick="_alert('请输入内容!',5,1,[Song.attion,Song.disattion])
 */
function _alert(txt,showtime){
    var box=$('#alertBox');
    var type=type || 1;
    if(txt){$('#msg_text').html(txt);}
    var alertPop=getMiddlePos('alertBox');
    var vl=alertPop.pl;
    var vt=alertPop.pt;
    $('#poptype1').show();
    $('#poptype2').hide();
    $('#poptype3').hide();
    box.css({"left":vl+"px","top":vt+"px","z-index": "1000"}).show(); 
    $('#close_msg,#data-confirm').click(function(){
        _closePop();
    });
    var timeSong=window.setTimeout(_closePop,showtime*1000);
}

function _alert2(txt,func){
    $('#btnAgree,#btnDisgree').unbind("click");
    var box=$('#alertBox');
    if(txt){$('#msg_text').html(txt);}
    var alertPop=getMiddlePos('alertBox');
    var vl=alertPop.pl;
    var vt=alertPop.pt;
    $('#poptype1').hide();    
    $('#poptype2').show();
    $('#poptype3').hide();
    if(func[0]){$('#btnAgree').click(function(){func[0]();})}
    if(func[1]){$('#btnDisgree').click(function(){func[1]();})}
    box.css({"left":vl+"px","top":vt+"px"}).show(); 
    $('#close_msg,#data-confirm').click(function(){
        _closePop();
    });
}
function _alert3(txt){
    var box=$('#alertBox');
    if(txt){$('#msg_text').html(txt);}
    var alertPop=getMiddlePos('alertBox');
    var vl=alertPop.pl;
    var vt=alertPop.pt;
    $('#poptype1').hide();    
    $('#poptype2').hide();    
    $('#poptype3').show();
    box.css({"left":vl+"px","top":vt+"px"}).show(); 
    $('#close_msg,#data-confirm').click(function(){
        _closePop();
        window.location.href='/';
    });
    $('#btnConfirm').click(function(){
        window.location.href='/';
    });
}

function _alert4(txt){
    var box=$('#alertBox');
    if(txt){$('#msg_text').html(txt);}
    var alertPop=getMiddlePos('alertBox');
    var vl=alertPop.pl;
    var vt=alertPop.pt;
    $('#poptype1').hide();    
    $('#poptype2').hide();    
    $('#poptype3').show();
    box.css({"left":vl+"px","top":vt+"px"}).show(); 
    $('#close_msg,#data-confirm').click(function(){
        _closePop();
        window.location.href='/';
    });
    $('#btnConfirm').click(function(){
        window.location.href='/';
    });
    var kicktimer=setTimeout(function(){window.location.href='/';},5000);
}

function chatPop(){
    var box=getMiddlePos('chatOff');
    var vl=box.pl;
    var vt=box.pt;
    $('#chatOff').css({"left":vl+"px","top":vt+"px"}).show(); 
    $('#chat_button').click(function(){window.location.reload();})
    var chatTime=setTimeout(function(){window.location.reload();},5000);
}

/*加载函数*/
$(function(){
    $.fn.extend({
        Discover:function(){
            var scrtime;
            $("#discover").hover(function(){
                clearInterval(scrtime);
            },function(){
                scrtime=setInterval(function() {
                    var allMsg=$("#discover");
                    var $ul=allMsg.find('ul.bigimg');
                    var last=$ul.last();
                    var liHeight=last.height();
                    allMsg.animate({marginTop:(liHeight)},1000,function(){
                        last.hide();
                        last.prependTo(allMsg);
                        allMsg.css({marginTop:0});
                        last.fadeIn(300);
                    });
                },5000);
            });
        },
        ClearRoom:function(){
            var that=$(this);
            that.blur(function(){
                if($(this).val()==''){$(this).val($(this).attr('pro-msg'));}
            })
            that.focus(function(){
                if($(this).val()==$(this).attr('pro-msg')){$(this).val('');}
            })    
        },
        setMyTab:function(){ //管理中心 tab导航
            var strCenterUrl=location.href;
            var arrNav=[
                        {"tag":"bookmark","name":"我的收藏","loc":0},
                        {"tag":"mygods","name":"我的偶像","loc":1},
                        {"tag":"mynumbers","name":"我的靓号","loc":2},
                        {"tag":"tool","name":"我的道具","loc":3},
                        //{"tag":"wishing","name":"我的许愿","loc":4},
                        {"tag":"mymanagers","name":"我的房管","loc":4},
                        //{"tag":"family","name":"我的家族","loc":6},
                        {"tag":"myfans","name":"我的粉丝","loc":5},
                        {"tag":"info","name":"我的个人设置","loc":6},
                        {"tag":"get","name":"我的账单","loc":7},
                        {"tag":"listAward","name":"我的账单","loc":7},
                        {"tag":"exchange","name":"我的兑换","loc":10},
                        {"tag":"settlement","name":"我的结算","loc":11}
                        //{"tag":"bl","name":"我的黑名单","loc":9}
                       ];
            var loc=0,stitle="我的收藏";
            for(var i=0;i<arrNav.length;i++){
                var json=arrNav[i];
                if(strCenterUrl.hasString(json["tag"])){
                   loc=json["loc"];
                   stitle=json["name"];
                }
            }
            $(this).eq(loc).addClass('on').html('<span>'+stitle+'</span>');
        }
        ,
        OverChange:function(){
            $(this).hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover")});    
        }
    });
    $('.mycenter').live("mouseenter",function(){
        $(this).find('.mysetting').show();
    }).live("mouseleave",function(){
        $(this).find('.mysetting').hide();
    })
    if(document.all){
        $("a[href='javascript:void(0);']").live("click",function(e){e.preventDefault();});
    }
    $(".chat_room").mouseover(function(){
        $(this).find('.chat_btn').show();
    });
    $(".chat_room").mouseout(function() {
        $(this).find('.chat_btn').hide();
    });
    $('.dh').click(function(){
        $(".dh_table").show();
    });
    $('#gift_name').click(function(){
        Gift_obj.left=$('#gift_name').offset().left;
        Gift_obj.top=$('#gift_name').offset().top;
        $('#gift_model').css({"left":(Gift_obj.left-56)+"px","top":((Gift_obj.top)-234)+"px"});
        if($("#gift_model").is(":hidden")){
            $('#gift_model').show();    
            $('#choose_btn').attr('class','btn_up');
        }else{
            $('#gift_model').hide();    
            $('#choose_btn').attr('class','');    
        }
        
    });
    $('#show_num_btn').click(function(){
        Gift_numobj.left=$('#gift_num').offset().left;
        Gift_numobj.top=$('#gift_num').offset().top;
        $('#gift-givenum').css({"left":(Gift_numobj.left)+"px","top":((Gift_numobj.top)-120)+"px"});
        if($("#gift-givenum").is(":hidden")){
            $('#gift-givenum').show();
            $('#show_num_btn').attr("class","btn_up");
        }else{
            $('#gift-givenum').hide();
            $('#show_num_btn').attr("class","");    
        }
    });

    $("#hb_btn").mouseover(function(){
        Gift_numobj.left=$('#gift_num').offset().left;
        Gift_numobj.top=$('#gift_num').offset().top;
        $('#redBagBox').css({"left":(Gift_numobj.left+180)+"px","top":((Gift_numobj.top)-40)+"px"});
        //if($("#redBagBox").is(":hidden")){
            $('#redBagBox').show();
        //}else{
            //$('#redBagBox').hide();
        //}
    });
    $("#hb_btn").mouseout(function(){
        $('#redBagBox').hide();
    });
    $("#redBagBox").mouseover(function(){
        $('#redBagBox').show();
    });
    $("#redBagBox").mouseout(function(){
        $('#redBagBox').hide();
    });

    $('#gift_to').click(function(){
        var gift_to=$(this).offset();
    
        $('#playerBox').css({"left":(gift_to.left)+"px","top":((gift_to.top)-124)+"px"});
        if($("#playerBox").is(":hidden")){
            $('#playerBox').show();
            $('#show_gift_user_list_btn').attr("class","btn_up");
        }else{
            $('#playerBox').hide();
            $('#show_gift_user_list_btn').attr("class","");    
        }
    });
    $('#msg_to_all').click(function(){
        var msg_to=$(this).offset();
        $('#playerBox1').css({"left":(msg_to.left)+"px","top":((msg_to.top)-130)+"px"});
        if($("#playerBox1").is(":hidden")){
            $('#playerBox1').show();
        }else{
            $('#playerBox1').hide();
        }
        $('#whisper').get(0).disabled=true;
    });
    $('.gift-v li').hover(function(e){
        $('.gift-v li').find('div').hide();                       
        var obj_gift=$(this).position();
        $(this).find('div').css({"left":(obj_gift.left)+"px","top":((obj_gift.top))+"px"}).show();
    });
    $('.gift_close').click(function(){
        $("#gift_model").hide();        
        $('#choose_btn').attr("class","");
    });
    //$('.p-Song .close').click(function(){
        //var that=$(this);    
        //that.parent().parent().hide();
    //});
    $("input[name=accounttype]").click(function(){
        var that=$(this);
        $('.paymethod').hide();
        $('.paymethod').eq(that.attr("value")-1).show();
    });
    $('#username').keydown(function(e){
        var keyCode=e.keyCode?e.keyCode:e.which?e.which:e.charCode;
        if(keyCode==13){
            e.preventDefault();
            $('#password')[0].focus();
        }
    });
    $('#password').keydown(function(e){
        var keyCode=e.keyCode?e.keyCode:e.which?e.which:e.charCode;
        if(keyCode==13){
            e.preventDefault();
            $('.btn_login').click();
            return false;
        }
    });
    
    
    
    
});

var $cookie={
    getCookie:function(a){
        var b=a+"=";
        a=document.cookie.indexOf(b);
        if(a!=-1){
            a+=b.length;
            b=document.cookie.indexOf(";",a);
            if(b==-1)b=document.cookie.length;
            return unescape(document.cookie.substring(a,b))
        }else return""
    },
    _getCookie:function(a){
        return document.cookie.match(RegExp("(^"+a+"| "+a+")=([^;]*)"))==null?"":decodeURIComponent(RegExp.$2);
    },
    setCookie:function(a,b,c){
        c=new Date((new Date).getTime()+c*36E5);
        document.cookie=a+"="+escape(b)+"; path=/; domain=" + window.location.host + "; expires="+c.toGMTString();
    },
    delCookie:function(a){
        var b=new Date((new Date).getTime());
        document.cookie=a+"= ; path=/; domain=" + window.location.host + "; expires="+b.toGMTString()
    }
}
var favorite={
    favBoxId:"floatLayerFavDiv",
    add:function(){
        var sURL=window.location.href;
        var sTitle=document.title;
        try{
            window.external.addFavorite(sURL,sTitle);
        }catch(e){
            try{
                window.sidebar.addPanel(sTitle,sURL,"");
            }catch(e){
                alert("很抱歉,您的浏览器不支持该功能,请手动Ctrl+D加入收藏");
            }
        }    
    },
    hide:function(){$cookie.setCookie("fav_cookie",1,100);$('#'+this.favBoxId).hide();},
    close:function(){
       var boxId=arguments[0]?arguments[0]:this.favBoxId;
       $('#'+boxId).fadeOut();
    }
}
/**
     * @description 通用复选框全选
     * @param {String} string 操作复选框的ID
     * @param {String} string 操作选中复选框的Name属性
     * @example checkfilter('blackall','blackuser');
*/
var checkfilter=function(dom,name){
    var dealDom=$('#'+dom);
    var eachcheckbox=$("input[name='"+name+"']");
    if(dealDom.attr('type')=='checkbox'){
        if(dealDom.attr("checked")){
            eachcheckbox.each(function(){ //全选
                $(this).attr("checked","checked");
            });
        }else{
            eachcheckbox.each(function(){ //反选
                $(this).attr("checked",false);
            });    
        }
    }
}
var initBack=function(){
    var backtop=$("#back2top"),body_width=990,pos=$('.wrap').offset();
    $("#back2top").css("left",(body_width+pos.left+2)+"px").show();
    $(window).scroll(function(){$(window).scrollTop()==0?backtop.fadeOut():backtop.fadeIn()});
    $(window).resize(function(){
        pos=$('.wrap').offset();                      
        if(body_width>10){backtop.css("left",(body_width+pos.left+2)+"px");}
    });    
}
var statistics={ //判断直播页/首页是否从推广联盟过来 标识  如：/xmlService_emceeRandom.htm?czac=75ce82c70833e9d01a25c6f9cefcc445
    promotion:function(){
        var referurl=location.href; 
        if(referurl.indexOf("czac")>0){
           Sys.ispro="_pop_1";
        }
        return;
    }
}
//message
function showMessages(){
    var url="/show_listMessage"+Sys.ispro+".htm?t="+Math.random();
    $.getJSON(url,function(json){
        if(json && json.code==0){
            var total=json.data.count;
            $("#msgnum").html(total);
            if(total>0){
                var msgArray=new Array();
                $.each(json.data.messages,function(i,item){
                    msgArray.push("<li mid=\""+item.userMsgId+"\" class=\""+(item.status==1?"read":"")+"\">");
                    msgArray.push("<span>【系统】："+item.title+"</span>");
                    msgArray.push("<p>"+item.content+"</p>");
                    msgArray.push("</li>");
                });
                $("#message").html(msgArray.join(""));
            }
        }
        $('.mesage-tip li span').click(function(){
            var parentobj=$(this).parent();
            var p_obj=parentobj.find('p');
            var messageid=parentobj.attr('mid');
            var mestate=parentobj.attr('class');
            if(mestate!='read'){
                $.getJSON("show_readMessage_umid_"+messageid+Sys.ispro+".htm?t="+Math.random(),function(json){});
            }
            if(p_obj.is(':hidden')){
                p_obj.show();
            }else{
                p_obj.hide();
            }
            $(this).parent().addClass('read');
        })
        $('.mesage-tip li').hover(function(){
            if($(this).attr('class')==''){
                $(this).addClass("hover");
            }
        },function(){
            $(this).removeClass("hover");
        })
    });
    setTimeout("showMessages()",5*60*1000);
}

var userhidden=function(st){
    var urlhttp="/show.do?m=hidden&h="+st+"&t="+Math.random();
    $.getJSON(urlhttp,function(json){
        if(json){
            if(json.code!=0){
                _alert(json.info,"5");
            }
            else{
                if(st==1){
                    _alert("您已成功隐身，请刷新直播间页面！",5);
                    $("#hclose").show();
                    $("#hopen").hide();
                }
                else if(st==0){
                    alert("您已成功显身，请刷新直播间页面！",5);
                    $("#hclose").hide();
                    $("#hopen").show();
                }
                else{
                    _alert("操作成功！","5");
                    $("#hclose").hide();
                    $("#hopen").show();
                }
            }    
        }
    });    
}
statistics.promotion(); //判断推广

//注册用户后验证邮箱
var firstApplyByEmail = {
    arrayObj:[
        'qq.com',
        '163.com',
        'sina.com',
        'sina.com.cn',
        '126.com',
        'sohu.com',
        'yahoo.com.cn',
        'yahoo.cn',
        'hotmail.com',
        'gmail.com',
    ],
    _closeLayer1:function (){
        var box=$('#regSucMsg');
        box.hide();
        return false;
    },
    _closeLayer2:function (){
        var box=$('#alertBox1');
        box.hide();
        return false;
    },
    emailCheck:function (text){
        var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        if(!myreg.test($.trim(text))){
            _alert('提示\n\n请输入有效的E_mail！',5);
            $("#emailAddress").focus();
            return false;
        }else{
            $.ajax({
                url:"show_toValidateEmail_email_"+$.trim(text)+".htm",
                data:"t="+Math.random(),
                type:'get',
                async:false,
                success: function(data){
                    if(data=='1'){
                        var ics = firstApplyByEmail.isContentSpace($.trim(text));
                        if(ics==-1){
                            firstApplyByEmail._closeLayer2();
                            _alert("请登陆邮箱完成验证吧！",3);
                        }else{
                            window.location.href="http://"+firstApplyByEmail.arrayObj[ics];
                        }
                    }else if(data=='3'){
                        _alert('邮箱已绑定！',3);
                    }else{
                        _alert('认证失败！',3);
                    }
               }
            });
        }
    },
    isContentSpace:function (s){
        var ss = s.split("@");
        var patt = new RegExp(ss[1]);
        for(var i=0;i<firstApplyByEmail.arrayObj.length;i++){
            var str = firstApplyByEmail.arrayObj[i];
            if(patt.test(str)){
                return i;  
            }
        }
        return -1;
    },
    _alertLayer1:function (){
        var box=$('#regSucMsg');
        var alertPop=getMiddlePos('regSucMsg');
        var vl=alertPop.pl;
        var vt=alertPop.pt;
        box.css({"left":vl+"px","top":vt+"px","z-index": "999"}).show(); 
        $('#close_msg1').click(function(){
            firstApplyByEmail._closeLayer1();
        });
        $('#data-confirm1').click(function(){
            firstApplyByEmail._closeLayer1();
            firstApplyByEmail._alertLayer2();
        });
    },
     _alertLayer2:function (){
        var box=$('#alertBox1');
        var alertPop=getMiddlePos('alertBox1');
        var vl=alertPop.pl;
        var vt=alertPop.pt;
        box.css({"left":vl+"px","top":vt+"px","z-index": "999"}).show(); 
        $('#close_msg2').click(function(){
            firstApplyByEmail._closeLayer2();
        });
        $('#data-confirm2').click(function(){
            var emailAddress = $("#emailAddress").val();
            firstApplyByEmail.emailCheck(emailAddress);
        });
    }
}

//var lj = {
//    checklogin:function(){
//        if(lj.getCookie(['current_user_info'])==null && Dom.$getid("uac_div")==null){
//            UAC.openUAC(0);
//            return false;
//        }
//    },
//    getCookie:function (name){
//        var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
//        if(arr != null){
//            return unescape(arr[2]);
//        }
//        return null; 
//    }
//}
//setInterval("lj.checklogin()", 60000);

$(document).ready(function(){
    this.url=encodeURI(window.location.href);
    var czac=this.url.indexOf('czac'),
        czclac=this.url.indexOf('czclac'),
        session=this.url.indexOf('session');
    //var    htm=this.url.indexOf('htm');
    
    var cz_dom=czac==-1?czclac:czac;
        cz_elm=czac==-1?czclac+6:czac+4;
    if(cz_dom!=-1&&session!=-1){
        var _url=this.url.substr(0,cz_dom-1),
            //_session=this.url.substring(session+8,htm-1),
            _session=this.url.substring(session+8),
            _czdom=this.url.substring(cz_dom,cz_elm),
            _cz=this.url.substring(cz_elm+1,session-1);
        var i=0;
        
        function test(){
            i+=1;
            if(i==1){
                this.urls='session='+_session+'&'+_czdom+'='+_cz+'&event=1&url='+_url+'';
            }
            else{   
                this.urls='session='+_session+'&'+_czdom+'='+_cz+'&event=2&dur='+((i-1)*10)+'&url='+_url+'';
            }
            var _urla="/aa";
            this._script="<script src='"+_urla+"?"+this.urls+"'></script>";
            $("body").append(this._script);

            if(i*10<60){
                setTimeout(test,10000)
            }
        }
        test();    
    }
});
