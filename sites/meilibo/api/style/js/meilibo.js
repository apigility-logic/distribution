//房间的一些处理
$(function () {
    $(".listcon li").hover(function (e) {
        var xx = e.originalEvent.x || e.originalEvent.layerX || 0;
        var yy = e.originalEvent.y || e.originalEvent.layerY || 0;
        var left = $(this).offset().left + 260;
        var top = $(this).offset().top;
        $("#dialog-contextmenu").css({ "left": xx, "top": yy }).show();
    }, function (e) {
        $("#dialog-contextmenu").hide();
    });
    $("#dialog-contextmenu").hover(function () {
        $("#dialog-contextmenu").show();
    }, function () {
        $("#dialog-contextmenu").hide();
    })

    //初始化物品的定位样式
    $("#gift_tabs .items").each(function () {
        //初始化物品的定位样式
        $(this).find(".giftItem:gt(8)").hide();
        $(this).find(".giftItem").each(function (i) {
            var me = $(this);
            me.attr("class", "giftItem");
            $(this).addClass("posit" + (parseInt(i) % 9 + 1));
        });
    });
    //物品左箭头被点击
    $("#gift_tabs .arrow_left").bind("click", function () {
        var me = $("#gift_tabs .items:visible");//获取可见的物品条
        var index = me.find(".giftItem:not(.disabled):visible:first").index();//获取可视物品的第一个物品的index
        var startIndex = index - 9 - 1;
        if (startIndex < -1)
            return false;
        me.find(".giftItem").hide();//隐藏所有物品
        if (startIndex < 0)
            me.find(".giftItem:not(.disabled):lt(9)").show();
        else
            me.find(".giftItem:not(.disabled):gt(" + startIndex + "):lt(9)").show();
    });

    //物品右箭头被点击
    $("#gift_tabs .arrow_right").bind("click", function () {
        var me = $("#gift_tabs .items:visible");//获取可见的物品条
        var index = me.find(".giftItem:not(.disabled):visible:first").index();//获取可视物品的第一个物品的index

        var total = me.find(".giftItem:not(.disabled)").length;
        var startIndex = index + 8;
        if (startIndex >= total)
            return false;
        me.find(".giftItem").hide();//隐藏所有物品
        me.find(".giftItem:not(.disabled):gt(" + startIndex + "):lt(9)").show();
    });

    //选择礼物分类
    $("#gift_tab li").click(function () {
        var me = $(this); var type = me.attr("data-type");
        $("#gift_tab li").removeClass("gift_btn_on");
        showItems(type);
        me.addClass("gift_btn_on");
    });

    showItems(0);

 /*    var scrollConfig = {
        W: "15px"
        , bodyElement: ".c_scroll_con"
        , BgUrl: "url(http://sr.9513.com/live/" + version + "/images/s_bg2.png)"
        , Bg: "right 0 repeat-y"
        , Bar: {
            Pos: "up"  //up：置顶,bottom：置底,upAndBottom : 初始置顶，内容超出时置底
            , Bd: { bd: false, Out: "#a3c3d5", Hover: "#b7d5e6" }
            , Bg: { Out: "-45px 0 repeat-y", Hover: "-45px 0 repeat-y", Focus: "-45px 0 repeat-y" }
            , Tb: { tb: true, topHeight: 4, bHeight: 4, TPos: { Out: "-58px 0 no-repeat", Hover: "-58px 0 no-repeat", Focus: "-58px 0 no-repeat" }, BPos: { Out: "-58px -26px no-repeat", Hover: "-58px -26px no-repeat", Focus: "-58px -26px no-repeat" } }
        }
        , Btn: { btn: false }
        , Fn: function () { }
    }; */

    //$(".ZBJ_jscroll").jscroll(scrollConfig);
    //var newScrollConfig = jQuery.extend(true, {}, scrollConfig); //深复制
   /*  newScrollConfig.Bar.Pos = "upAndBottom";
    $(".ZBJ_jscrollUpAndBottom").jscroll(newScrollConfig); */






    //关于左侧与右侧的黑条在1366宽度的浏览器中的问题
    if ($("body").width() <= 1366)
    {
        //左侧
        var zbj_left = $("#ZBJ_leftNav");
        zbj_left.css("left", "-20px");
        zbj_left.hover(function () {
            zbj_left.animate({ "left": "0px" }, 250);
        }, function () {
            zbj_left.animate({ "left": "-20px" }, 250);
        });

        var zbj_right = $("#rightGame");
        zbj_right.css("right", "-25px");
        zbj_right.hover(function () {
            zbj_right.animate({ "right": "0px" }, 250);
        }, function () {
            zbj_right.animate({ "right": "-25px" }, 250);
        });
    }
/**
    //守护头像悬停
    $("#GuardList").on("mouseover", "li", function (e) {
        var me = $(this);
        var cno = me.attr("dataIdx");
        var Name = me.attr("dataName");
        $("#guard_Name").html(Name + "<br/>(" + cno + ")");
        $("#guard_Img").attr("src", "http://sr.9513.com/Images/guard/" + me.attr("data-Type") + ".png");
        $("#dialog-guard").css({ left: me.offset().left + me.width(), top: me.offset().top })
        $("#dialog-guard").show();
    });

    $("#GuardList").on("mouseout", "li", function () {
        $("#dialog-guard").hide();
    });
    
**/

    /*测试按钮*/
    $("#ceshi").click(function () {
        jw.Message.chat.Addlb.Airing('16:30&nbsp;&nbsp;头发乱了，：数量巨大浪费精神了大家分厘卡三季稻了科技时代浪费精力<a href="javascript:void(0)">（小逗比）</a>');
    });

});




function showItems(n) {
    $("#gift_tabs .items").hide();
    $("#gift_tabs .items:eq(" + n + ")").show();
}







//直播间原始JS配置








