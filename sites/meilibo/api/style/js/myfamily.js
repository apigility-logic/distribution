 /*
 * Family
 * */
var Family={
	AjaxSubmit:function(url,divClass,obj){
		var div = "." + divClass;
		//alert(url);
		$(div).load(url,function (responseText, textStatus, XMLHttpRequest){obj;});
	}
}

function buyFamilyBadge(familyId){
	//var upurl = 'family.do?m=buyBadge';
	//alert(upurl);
	//if(confirm("确定购买家族徽章吗?")){
		$.ajax({
			type:'post',
			url:'/family.do?m=buyBadge',
			data:'fId='+familyId+'&time='+new Date().getTime(),
			success:function(json){
				var json=evalJSON(json);
				if(json && json.code==0){
					window.location.reload();
				}else{
					jmsgPop(json.info,3);
				}
			}
		});
	//}
}

function buyBudge(id,cost){//加入家族徽章价格
	
	_alert2('购买此徽章将花费'+cost+'秀币,是否购买？',[function(){
		buyFamilyBadge(id);
		$('#alertBox').hide();
	},function(){
		$('#alertBox').hide();
	}]);
}