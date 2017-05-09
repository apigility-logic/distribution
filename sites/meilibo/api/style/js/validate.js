function register(){
	var item=['name','card','tel','qq','address','bank','otherbank','city','bankname','bankcard','bankuser','postfile'];
	
	var intAccounttype=1;
	$("input[name=accounttype]").each(function(){
        if(this.checked){intAccounttype=this.value;}
    })
	
	if(intAccounttype==2){
		item=['name','card','tel','qq','address','alipayname','postfile'];
	}

	var result=true;

	for(var i=0;i<item.length;i++){
		if(!validateRegInfo(item[i])){
			result=false;
			break;
		}
	}
	
	//alert(result);
	return result;
}
function validateRegInfo(type){
	var name_pattern=/^[\u4e00-\u9fa5]+$/;
	var exp_icard1=/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/; 
	var exp_icard2=/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[A-Z])$/; 
	if(type=="name"){
		var name=$("#userName").val();
		if(name_pattern.test(name)){
			$("#unamemsg").css("display","none");
			$("#unameprompt").css("display","none");
			$("#unameok").css("display","inline-block");
			return true;
		}else{
			$("#unamemsg").css("display","");
			$("#unameprompt").css("display","none");
			$("#unameok").css("display","none");
			return false;
		}
	}else if(type=='card'){
		var card=$('#userCard').val();
		if(exp_icard1.test(card) || exp_icard2.test(card)){
			$("#ucardmsg").css("display","none");
			$("#ucardprompt").css("display","none");
			$("#ucardok").css("display","inline-block");
			return true;
		}else{
			$("#ucardmsg").css("display","");
			$("#ucardprompt").css("display","none");
			$("#ucardok").css("display","none");
			return false;
		}
	}else if(type=="tel"){
		var tel=$('#userTel').val();
	    var exp_t1=/^([0\d]{1,4})[\s-]?([\d]{7,8})$/;
		var exp_t2=/^(13\d{9})|(15[8,9]\d{8})$/;
		
		if(exp_t1.test(tel) || exp_t2.test(tel)){
			$("#telmsg").css("display","none");
			$("#telprompt").css("display","none");
			$("#telok").css("display","inline-block");
			return true;
		}else{
			$("#telmsg").css("display","");
			$("#telprompt").css("display","none");
			$("#telok").css("display","none");
			return false;
		}
	}else if(type=="qq"){
		var qq=$('#userQQ').val();
	    var exp_qq=/^\d+(\.\d+)?$/;
		if(exp_qq.test(qq) && qq!=""){
			$("#qqmsg").css("display","none");
			$("#qqprompt").css("display","none");
			$("#qqok").css("display","inline-block");
			return true;
		}else{
			$("#qqmsg").css("display","");
			$("#qqprompt").css("display","none");
			$("#qqok").css("display","none");
			return false;
		}
	}else if(type=="address"){
		var address=$('#userAdd').val();
		if(address!=""){
			$("#addmsg").css("display","none");
			$("#addprompt").css("display","none");
			$("#addok").css("display","inline-block");
			return true;
		}else{
			$("#addmsg").css("display","");
			$("#addprompt").css("display","none");
			$("#addok").css("display","none");
			return false;
		}
	}else if(type=="bank"){
		var bank=$('#kh_bank').val();
		if(bank!="") {
			$("#kh_bankmsg").css("display","none");
			$("#kh_bankprompt").css("display","none");
			$("#kh_bankok").css("display","inline-block");
			return true;
		}else{
			$("#kh_bankmsg").css("display","");
			$("#kh_bankprompt").css("display","none");
			$("#kh_bankok").css("display","none");
			return false;
		}
	}else if(type=="otherbank") {
		var bank=$('#kh_bank').val();
		if('其它' == bank) {
			var otherbank=$('#otherbank').val();
			if(name_pattern.test(otherbank)) {
				$("#otherbankmsg").css("display","none");
				$("#otherbankprompt").css("display","none");
				$("#otherbankok").css("display","inline-block");
				return true;
			}else{
				$("#otherbankmsg").css("display","");
				$("#otherbankprompt").css("display","none");
				$("#otherbankok").css("display","none");
				return false;
			}
		}
		return true;
	}else if(type=="city"){
		if($('#selectArea').val()!='' && $('#selectSubArea').val()!=''){
			$("#citymsg").css("display","none");
			$("#cityprompt").css("display","none");
			$("#cityok").css("display","inline-block");
			return true;
		}else{
			$("#citymsg").css("display","");
			$("#cityprompt").css("display","none");
			$("#cityok").css("display","none");	
			return false;
		}
	}else if(type=="bankname"){
		var bankname=$('#userBankName').val();
		if(bankname!=""){
			$("#banknamemsg").css("display","none");
			$("#banknameprompt").css("display","none");
			$("#banknameok").css("display","inline-block");
			return true;
		}else{
			$("#banknamemsg").css("display","");
			$("#banknameprompt").css("display","none");
			$("#banknameok").css("display","none");
			return false;
		}
	}else if(type=="bankuser"){
		var bankuser=$('#bankuser').val();
		if(bankuser!=""){
			$("#bankusermsg").css("display","none");
			$("#bankuserprompt").css("display","none");
			$("#bankuserok").css("display","inline-block");
			return true;
		}else{
			$("#bankusermsg").css("display","");
			$("#bankuserprompt").css("display","none");
			$("#bankuserok").css("display","none");
			return false;
		}
	}
	else if(type=="bankcard"){
		var bankcard=$('#bankcard').val();
		var Expcard=/^\d+$/;
		if(bankcard!="" && Expcard.test(bankcard)){
			$("#bankcardmsg").css("display","none");
			$("#bankcardprompt").css("display","none");
			$("#bankcardok").css("display","inline-block");
			return true;
		}else{
			$("#bankcardmsg").css("display","");
			$("#bankcardprompt").css("display","none");
			$("#bankcardok").css("display","none");
			return false;
		}
	}
	else if(type=="alipayname"){
		var alipayname=$('#alipayname').val();
		if(alipayname!=""){
			$("#alipaynamemsg").css("display","none");
			$("#alipaynameprompt").css("display","none");
			$("#alipaynameok").css("display","inline-block");
			return true;
		}else{
			$("#alipaynamemsg").css("display","");
			$("#alipaynameprompt").css("display","none");
			$("#alipaynameok").css("display","none");
			return false;
		}
	}
	else if(type=="postfile"){
		if($('#postfile').val()!=''){
			$("#postfilemsg").css("display","none");
			$("#postfileprompt").css("display","none");
			$("#postfileok").css("display","inline-block");
			return true;
		}else{
			$("#postfilemsg").css("display","");
			$("#postfileprompt").css("display","none");
			$("#postfileok").css("display","none");	
			return false;
		}
	}
}
function showRegInfo(type){
	if(type=="name"){
		$("#unameprompt").css("display","");
		$("#unameok").css("display","none");
		$("#unamemsg").css("display","none");
	}else if(type=="card"){
		$("#ucardmsg").css("display","none");
		$("#ucardprompt").css("display","");
		$("#ucardok").css("display","none");
	}else if(type=="tel"){
		$("#telmsg").css("display","none");
		$("#telprompt").css("display","");
		$("#telok").css("display","none");
	}else if(type=="qq"){
		$("#qqmsg").css("display","none");
		$("#qqprompt").css("display","");
		$("#qqok").css("display","none");
	}else if(type=="bankname"){
		$("#banknamemsg").css("display","none");
		$("#banknameprompt").css("display","");
		$("#banknameok").css("display","none");
	}else if(type=="bankuser"){
		$("#bankusermsg").css("display","none");
		$("#bankuserprompt").css("display","");
		$("#bankuserok").css("display","none");
	}else if(type=="bankcard"){
		$("#bankcardmsg").css("display","none");
		$("#bankcardprompt").css("display","");
		$("#bankcardok").css("display","none");
	}
	else if(type=="alipayname"){
		$("#alipaynamemsg").css("display","none");
		$("#alipaynameprompt").css("display","");
		$("#alipaynameok").css("display","none");
	}else if(type=="otherbank"){
		$("#otherbankmsg").css("display","none");
		$("#otherbankprompt").css("display","");
		$("#otherbankok").css("display","none");
	}
}


function inputOtherBank(){
	var bName = $('#kh_bank').val();
	if('其它' == bName ) {
		 $('#otherBankInput').show();
	} else {
		 $('#otherBankInput').hide();
	}
 }

$(document).ready(function() {
	inputOtherBank();
});

document.onkeydown=function(){
	if(window.event&&window.event.keyCode==13){register();}
}
