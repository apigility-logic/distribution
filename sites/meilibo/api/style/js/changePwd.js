jQuery.extend({
  passwordCheck:function(id,id_div){
	  var obj=$(id);
	  var obj_div=$(id_div);
	  var strPwd=obj.val();
	  var pattern=/^[A-Za-z0-9]{6,20}$/;
      if(pattern.test(strPwd)){
		 obj_div.html("<span class='ok'></span>");		
		 return true;
      }else{ 
		  obj_div.html("6-20个字符(字母、数字)区分大小写");
		  return false;
      } 	   
  },
  passwordOldCheck:function(id,id_div){
	  var obj=$(id);
	  var obj_div=$(id_div);
	  var strPwd=obj.val();
	  var pattern = /^[A-Za-z0-9]{6,20}$/;
      if(pattern.test(strPwd)){
      	 // 加入ajax验证原密码是否正确
      	 //var bol = false;
      	 //$.ajax({
      	 	//type: "POST",
      	 	//async: false,
      	 	//url: "/info_oldpass.htm",
      	 	//data: "oldpass=" + strPwd,
      	 	//success: function(msg) {
      	 		//if(msg == "0") {
      	 			//bol = true;
      	 		//}
      	 	//}
      	 //});
		 var bol = true;
      	 if(bol) {
		 	obj_div.html("<span class='ok'></span>");		
		 	return true;
		 } else {
		 	obj_div.html("原密码输入错误");
		    return false;
		 }
      	 return bol;
      }else{ 
		  obj_div.html("6-20个字符(字母、数字)区分大小写");
		  return false;
      } 	   
  },
  repasswordCheck:function(id0,id_div0,id1,id_div1){
	    var obj_div=$(id_div1);
		if(jQuery.passwordCheck(id0,id_div0)){
		  	var strValidatePwd=$(id1).val();
		 	 var strPwd=$(id0).val();
			  if(strValidatePwd==strPwd){
				  obj_div.html("<span class='ok'></span>");
				  return true;
			  }else{
				  obj_div.html("两次输入密码不相同，请重新输入确认密码！");
				  return false;					
			  }				
		}
    }
 }); 
