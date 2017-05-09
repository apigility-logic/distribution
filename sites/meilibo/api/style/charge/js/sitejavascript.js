function trim(str) {
    return str.replace(/(^\s*)|(\s*$)/g, "");
}

function PGJump(Url, MaxPage) {
    var sPGPage = document.getElementById('PGPage').value;

    var r = sPGPage.match(/^\+?[0-9]*[1-9][0-9]*$/);
    if (r == null) {
        alert("请输入数值");
        return;
    }

    var nPGPage = parseInt(sPGPage);
    if (nPGPage < 1 || nPGPage > MaxPage) {
        alert("请输入范围内的数值");
        return;
    }

    window.location.href = Url;
}

function securitypassset_submit() {
    if ($("#c_LoginPass").val() == "") {
        alert("请输入登陆密码");
        return;
    }

    if ($("#c_ChargePass").val() == "") {
        alert("请输入支付密码");
        return;
    }

    if ($("#c_ChargePass").val().length < 6
            || $("#c_ChargePass").val().length > 16) {
        alert("支付密码必须6-16位");
        return;
    }

    if ($("#c_ChargePass").val() != $("#c_CheckChargePass").val()) {
        alert("支付密码与确认支付密码不一致");
        return;
    }

    $("#fpost").submit();
}

function securityemailset_submit() {
    if ($("#c_LoginPass").val() == "") {
        alert("请输入登陆密码");
        return;
    }

    if ($("#c_ResetPassEmail").val() == "") {
        alert("请输入安全邮箱");
        return;
    }

    var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
    var chkFlag = pattern.test($("#c_ResetPassEmail").val());
    if (!chkFlag) {
        alert("请输入正确的邮箱格式");
        return;
    }

    $("#fpost").submit();
}

function securityqaset_submit() {
    if ($("#c_LoginPass").val() == "") {
        alert("请输入登陆密码");
        return;
    }

    if ($("#c_ResetPassQuestion").val().length < 1
            || $("#c_ResetPassQuestion").val().length > 6) {
        alert("无效问题");
        return;
    }

    if ($("#c_ResetPassAnswer").val() == "") {
        alert("请输入答案");
        return;
    }

    if ($("#c_ResetPassAnswer").val().length < 2
            || $("#c_ResetPassAnswer").val().length > 20) {
        alert("答案必须2-20位");
        return;
    }

    $("#fpost").submit();
}

function showfindpass(type) {
    if (type == 1) {
        $("#Email_Panel").css("display", "block");
        $("#QA_Panel").css("display", "none");
        $("input[name=CheckEmail]").attr("checked", true);
        $("input[name=CheckQA]").attr("checked", false);
    } else if (type == 2) {
        $("#Email_Panel").css("display", "none");
        $("#QA_Panel").css("display", "block");
        $("input[name=CheckEmail]").attr("checked", false);
        $("input[name=CheckQA]").attr("checked", true);
    }
}

function securityfindpassqa_submit() {
    if ($("#c_ResetPassAnswer").val() == "") {
        alert("请输入答案");
        return;
    }

    if ($("#c_ChargePassQA").val() == "") {
        alert("请输入支付密码");
        return;
    }

    if ($("#c_ChargePassQA").val().length < 6
            || $("#c_ChargePassQA").val().length > 16) {
        alert("支付密码必须6-16位");
        return;
    }

    if ($("#c_ChargePassQA").val() != $("#c_CheckChargePassQA").val()) {
        alert("支付密码与确认支付密码不一致");
        return;
    }

    $("#fpostqa").submit();
}

function securityfindpassemail_submit() {
    if ($("#c_ChargePassEmail").val() == "") {
        alert("请输入支付密码");
        return;
    }

    if ($("#c_ChargePassEmail").val().length < 6
            || $("#c_ChargePassEmail").val().length > 16) {
        alert("支付密码必须6-16位");
        return;
    }

    if ($("#c_ChargePassEmail").val() != $("#c_CheckChargePassEmail").val()) {
        alert("支付密码与确认支付密码不一致");
        return;
    }

    $("#fpostemail").submit();
}

function showcharge(value) {
    $("input[name=c_PayType][value=" + value + "]").attr("checked", true);

    if (value == 1) {
        $("#PayType1").css("display", "block");
        $("#PayType2").css("display", "none");
    } else if (value == 2) {
        $("#PayType1").css("display", "none");
        $("#PayType2").css("display", "block");
    }

    charge_closeallalert();
}

function showuser(value) {
    $("input[name=c_ChargeType][value=" + value + "]").attr("checked", true);

    if (value == 1) {
        $("#ChargeType1").css("display", "block");
        $("#ChargeType2").css("display", "none");
    } else if (value == 2) {
        $("#ChargeType1").css("display", "none");
        $("#ChargeType2").css("display", "block");
    }

    charge_closeallalert();
}

var proxydivishow = false;
function showproxy(nAppID) {
    if ($("#proxy").css("display") == "none") {
        if (g_ProxyUserIDArray.length > 0) {
            // random array
            g_ProxyUserIDArray.sort(function() {
                return Math.random() > 0.5 ? -1 : 1;
            });

            var sHtml = "";
            for ( var i = 0; i < g_ProxyUserIDArray.length; i++) {
                sHtml += "<li><label><em class=\"inputcheckbox\"> <a href=\"/my/charge/AppID/"
                        + nAppID
                        + "/ProxyUserID/"
                        + g_ProxyUserIDArray[i][0]
                        + "\">"
                        + g_ProxyUserIDArray[i][1]
                        + "</a> </em></label></li>";
            }

            $("#ProxyUserList").html(sHtml);
        }

        $("#proxy").css("display", "block");
        setTimeout('Setproxydiv(true)', 100);
    } else {
        $("#proxy").css("display", "none");
        proxydivishow = false;
    }
}

function Setproxydiv(status) {
    proxydivishow = status;
}

function hiddenproxy() {
    if ($("#proxy").css("display") != "none" && proxydivishow) {
        $("#proxy").css("display", "none");
        proxydivishow = false;
    }
}

function ChangePPType(no, checkvalue, chargeusecurrencyname) {
    for ( var i = 1; i <= 4; i++) {
        $("#PPType_" + i).removeClass("on");
        $("#PPTypePanel_" + i).css("display", "none");
    }

    $("#PPType_" + no).addClass("on");
    $("#PPTypePanel_" + no).css("display", "block");
    $("input[name=c_PPPayID][value=" + checkvalue + "]").attr("checked", true);
    $("#c_Money1").val("");
    $("#c_Money2").val("");
    $("#swapcurrencyRMB").text(0);
    $("#swapcurrencyJOYB").text(0);

    //if ($("input[name=c_PayType][value=1]").attr("checked") == true) {
        $("#swapcurrencyRMBShow").text(chargeusecurrencyname + "与秀币的比例为1：" + getSwapCurrency());
    //}
    
    $("#ChargeUseCurrencyName").text(chargeusecurrencyname);
    
    if(no == 1){
        $("#paycardPanel_1").css("display", "none");
        //$("#paycardPanel_2").css("display", "none");
        //$("#paycardPanel_3").css("display", "none");
        $("#gamecardPanel_1").css("display", "none");
        //$("#gamecardPanel_2").css("display", "none");
        //$("#gamecardPanel_3").css("display", "none");
    }
    if(no == 2){
        $("#paycardPanel_1").css("display", "block");
        //$("#paycardPanel_2").css("display", "block");
        //$("#paycardPanel_3").css("display", "block");
        $("#gamecardPanel_1").css("display", "none");
        //$("#gamecardPanel_2").css("display", "none");
        //$("#gamecardPanel_3").css("display", "none");
    }
    if(no == 3){
        $("#paycardPanel_1").css("display", "none");
        //$("#paycardPanel_2").css("display", "none");
        //$("#paycardPanel_3").css("display", "none");
        $("#gamecardPanel_1").css("display", "none");
        //$("#gamecardPanel_2").css("display", "block");
        //$("#gamecardPanel_3").css("display", "block");
    }
    if(no == 4){
        $("#paycardPanel_1").css("display", "none");
        //$("#paycardPanel_2").css("display", "none");
        //$("#paycardPanel_3").css("display", "none");
        $("#gamecardPanel_1").css("display", "block");
        //$("#gamecardPanel_2").css("display", "block");
        //$("#gamecardPanel_3").css("display", "block");
    }    
}

function charge_submit() {
    charge_closeallalert();
    ChargeType = $("input:radio[name=c_ChargeType]:checked").val();

    //if ($("input[name=c_PayType][value=1]").attr("checked") == true) {
        var PPPayID = $("input:radio[name=c_PPPayID]:checked").val();
        if (PPPayID == "") {
            charge_submitalert("无效的支付方式");
            return;
        }

        var re = /^[1-9]+[0-9]*]*$/;
        if (!re.test($("#c_Money1").val())) {
            charge_submitalert2("c_Money1_Alert", "充值额度必须为正整数");
            return;
        }

        if (PPPayID == 19) {
            if ($("#c_Money1").val() > 10000) {
                charge_submitalert2("c_Money1_Alert", "充值额度必须小于10000");
                return;
            }
        } else {
            if ($("#c_Money1").val() > g_MaxMoney) {
                charge_submitalert2("c_Money1_Alert", "充值额度必须小于" + g_MaxMoney);
                return;
            }
        }
    //} else if ($("input[name=c_PayType][value=2]").attr("checked")) {
        //var re = /^[1-9]+[0-9]*]*$/;
        //if (!re.test($("#c_Money2").val())) {
            //charge_submitalert2("c_Money2_Alert", "充值额度必须为正整数");
            //return;
        //}

        //if ($("#c_Money2").val() > g_MaxMoney) {
            //charge_submitalert2("c_Money2_Alert", "充值额度必须小于" + g_MaxMoney);
            //return;
        //}
    //}

    //if (PPPayID == "14_SZX-NET") {
        //if ($("#paycard_num").val() == "") {
            //charge_submitalert2("paycard_num_Alert", "卡号不能为空");
            //return;
        //}
        //if ($("#paycard_psw").val() == "") {
            //charge_submitalert2("paycard_psw_Alert", "卡密不能为空");
            //return;
        //}
    //}
    //if (PPPayID == "17_JIUYOU-NET") {
        //if ($("#gamecard_num").val() == "") {
            //charge_submitalert2("gamecard_num_Alert", "卡号不能为空");
            //return;
        //}
        //if ($("#gamecard_psw").val() == "") {
            //charge_submitalert2("gamecard_psw_Alert", "卡密不能为空");
            //return;
        //}
    //}

    if (ChargeType == 2) {
        if ($("#c_DestUserName").val() == "") {
            $("#c_DestUserName_Alert2").css("display", "block");
            charge_submitalert2("c_DestUserName_Alert2", "请输入需充值的用户房间号");
            return;
        }

        if (!g_CheckUser) {
            $("#c_DestUserName_Alert2").css("display", "block");
            charge_submitalert2("c_DestUserName_Alert2", "请先点击验证按钮并确认您的充值目标无误");
            return;
        }

        var CurDestUserName = $("#c_DestUserName").val();
        if (g_CurDestUserName != CurDestUserName) {
            $("#c_DestUserName_Alert2").css("display", "block");
            charge_submitalert2("c_DestUserName_Alert2", "您的目标已变更，请再次确认");
            g_CheckUser = false;
            return;
        }
    }

    $("#fpost").submit();
}

function charge_submitalert(info) {
    $("#showmessage").text(info);
    $("#showmessagepanel").css("display", "block");
}

function charge_submitalert2(showid, info) {
    $("#" + showid).text(info);
    $("#" + showid).css("display", "");
}

function charge_closeallalert() {
    $("#showmessage").text("");
    $("#showmessagepanel").css("display", "none");

    $("#c_Money1_Alert").text("");
    $("#c_Money1_Alert").css("display", "none");
    $("#c_Money2_Alert").text("");
    $("#c_Money2_Alert").css("display", "none");
    $("#c_DestUserName_Alert").text("");
    $("#c_DestUserName_Alert").css("display", "none");
}

function swapcurrency(idvalue, idshow) {
    var Value = $("#" + idvalue).val();

    var re = /^[1-9]+[0-9]*]*$/;
    if (re.test(Value)) {
        $("#" + idshow).text(Value * getSwapCurrency());
    } else {
        $("#" + idshow).text(0);
    }

    $("#c_Money1_Alert").text("");
    $("#c_Money1_Alert").css("display", "none");
    $("#c_Money2_Alert").text("");
    $("#c_Money2_Alert").css("display", "none");
}

function getSwapCurrency() {
    var SwapCurrency = 100;
    //if ($("input[name=c_PayType][value=1]").attr("checked") == true) {
        var PPPayID = $("input:radio[name=c_PPPayID]:checked").val();
        if (PPPayID == 19) {
            SwapCurrency = 600;
        } else {
            var PPPayIDArray = PPPayID.split("_");
            if (PPPayIDArray.length == 2 && PPPayIDArray[0] == 17) {
                SwapCurrency = 80;
            }
        }
    //}

    return SwapCurrency;
}

function ajaxcheckuser(id) {
    $("#c_DestUserName_Alert2").text("");
    $("#c_DestUserName_Alert2").css("display", "none");

    var CurDestUserName = $("#" + id).val();

    $.ajax({
        type : "get",
        url : "/my/ajaxcheckuser/roomnum/" + CurDestUserName,
        cache : false,
        success : function(data) {
            if (data != "") {
                charge_submitalert2("c_DestUserName_Alert", data);
                g_CheckUser = true;
                g_CurDestUserName = CurDestUserName;
            } else {
                charge_submitalert2("c_DestUserName_Alert", "用户房间号无效");
                g_CheckUser = false;
            }
        }
    });
}

function ReSetDate() {
    var TimeType = $("#c_TimeType").val();
    if (TimeType == 1) {
        $("#c_StartTime").val(DefaultStartTime);
        $("#c_EndTime").val(DefaultEndTime);
    } else if (TimeType == 2) {
        $("#c_StartTime").val(DefaultStartTimeHistory);
        $("#c_EndTime").val(DefaultEndTimeHistory);
    }
}

function GetMinDate() {
    var TimeType = $("#c_TimeType").val();
    if (TimeType == 1) {
        return DefaultEndTimeHistory;
    } else if (TimeType == 2) {
        return "";
    }
}

function GetMaxDate() {
    var TimeType = $("#c_TimeType").val();
    if (TimeType == 1) {
        return "";
    } else if (TimeType == 2) {
        return DefaultEndTimeHistory;
    }
}