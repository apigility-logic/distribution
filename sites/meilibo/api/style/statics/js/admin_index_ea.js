//clientHeight-0; 空白值 iframe自适应高度
function windowW(){
    if($(window).width()<980){
            $('.header').css('width',980+'px');
            $('#content').css('width',980+'px');
            $('body').attr('scroll','');
            $('body').css('overflow','');
    }
}
windowW();
$(window).resize(function(){
    if($(window).width()<980){
        windowW();
    }else{
        $('.header').css('width','auto');
        $('#content').css('width','auto');
        $('body').attr('scroll','no');
        $('body').css('overflow','hidden');
        
    }
});
window.onresize = function(){
    var heights = document.documentElement.clientHeight-150;document.getElementById('rightMain').height = heights;
    var openClose = $("#rightMain").height()+39;
    $('#center_frame').height(openClose+9);
    $("#openClose").height(openClose+30);    
}
window.onresize();
//站点下拉菜单
$(function(){
    $("#leftMain").load("/boboagent/index.php/Index/leftFrame/");
})
//站点选择
function site_select(id,name, domain, siteid) {
    
}
//隐藏站点下拉。
var s = 0;
var h;
function hidden_site_list() {
    s++;
    if(s>=3) {
        $('.tab-web-panel').hide();
        clearInterval(h);
        s = 0;
    }
}
function clearh(){
    if(h)clearInterval(h);
}
function hidden_site_list_1() {
    h = setInterval("hidden_site_list()", 1);
}

//左侧开关
$("#openClose").click(function(){
    if($(this).data('clicknum')==1) {
        $("html").removeClass("on");
        $(".left_menu").removeClass("left_menu_on");
        $(this).removeClass("close");
        $(this).data('clicknum', 0);
    } else {
        $(".left_menu").addClass("left_menu_on");
        $(this).addClass("close");
        $("html").addClass("on");
        $(this).data('clicknum', 1);
    }
    return false;
});

function _M(menuid,targetUrl) {
    $("#menuid").val(menuid);
    $("#bigid").val(menuid);
    //$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em>添加</em></a>');
    $("#leftMain").load("/2016168/index.php/Index/leftFrame/menuid/"+menuid);
    
    //$("#rightMain").attr('src', targetUrl);
    $('.top_menu').removeClass("on");
    $('#_M'+menuid).addClass("on");
    $.get("/2016168/index.php/Index/public_current_pos/menuid/"+menuid, function(data){
        $("#current_pos").html(data);
    });
    //当点击顶部菜单后，隐藏中间的框架
    $('#display_center_id').css('display','none');
    //显示左侧菜单，当点击顶部时，展开左侧
    $(".left_menu").removeClass("left_menu_on");
    $("#openClose").removeClass("close");
    $("html").removeClass("on");
    $("#openClose").data('clicknum', 0);
    $("#current_pos").data('clicknum', 1);
}
function _MP(menuid,targetUrl) {
    $("#menuid").val(menuid);

    $("#rightMain").attr('src', targetUrl);
    $('.sub_menu').removeClass("on fb blue");
    $('#_MP'+menuid).addClass("on fb blue");
    
    show_help(targetUrl);
}

function show_help(targetUrl) {
    
}
setInterval("hidden_help()", 10000);
function hidden_help() {
    var htime = $("#help").data('time')+1;
    $("#help").data('time', htime);
    if(htime>2) $("#help").slideUp("slow");
}
function add_panel() {
    var menuid = $("#menuid").val();
    $.ajax({
        type: "POST",
        url: "/2016168/index.php/Index/public_ajax_add_panel/",
        data: "menuid=" + menuid,
        success: function(data){
            if(data) {
                $("#panellist").html(data);
            }
        }
    });
}
function delete_panel(menuid, id) {
    $.ajax({
        type: "POST",
        url: "/2016168/index.php/Index/public_ajax_delete_panel/",
        data: "menuid=" + menuid,
        success: function(data){
            $("#panellist").html(data);
        }
    });
}

function paneladdclass(id) {
    $("#panellist span a[class='on']").removeClass();
    $(id).addClass('on')
}
setInterval("session_life()", 160000);
function session_life() {
    $.get("/2016168/index.php/Index/public_session_life/");
}
function lock_screen() {
    $.get("/2016168/index.php/Index/public_lock_screen/");
    $('#dvLockScreen').css('display','');
}
function check_screenlock() {
    var lock_password = $('#lock_password').val();
    if(lock_password=='') {
        $('#lock_tips').html('<font color="red">密码不能为空。</font>');
        return false;
    }
    $.get("/2016168/index.php/Index/public_login_screenlock/", { lock_password: lock_password},function(data){
        if(data==1) {
            $('#dvLockScreen').css('display','none');
            $('#lock_password').val('');
            $('#lock_tips').html('锁屏状态，请输入密码解锁');
        } else if(data==3) {
            $('#lock_tips').html('<font color="red">密码重试次数太多</font>');
        } else {
            strings = data.split('|');
            $('#lock_tips').html('<font color="red">密码错误，您还有'+strings[1]+'次尝试机会！</font>');
        }
    });
}
$(document).bind('keydown', 'return', function(evt){check_screenlock();return false;});