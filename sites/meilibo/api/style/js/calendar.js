function calendar(){
	this.dayArray=[31,28,31,30,31,30,31,31,30,31,30,31];
	this.currentDate={year:null,month:null};
	this.cache={
		selectedDates:{},
		currentDateStrings:[]
	};
	this.properties={
		dayNames:['日','一','二','三','四','五','六'],
		monthNames:['1','2','3','4','5','6','7','8','9','10','11','12']
	};
}
calendar.prototype={
	isLeapYear:function(year){return (year % 4==0 && year % 100 !=0) || (year % 400==0);},
	isToday:function(date){return this.isSameDate(date,new Date());},
	isSameDate:function(a,b){return a.getFullYear()==b.getFullYear() && a.getMonth()==b.getMonth() && a.getDate()==b.getDate();},
	setcurrentDate:function(year,month){
		this.dayArray[1]=this.isLeapYear(year)?29:28;
		this.currentDate.year=year;
		this.currentDate.month=month;
	},
	digitFix:function(number,count){
		var _string=number+"";
		var _count=count-_string.length;
		for(var i=0; i<_count; i++)
			_string="0"+_string;
		return _string;
	},
	dateToString:function(date){
		return date.getFullYear()+this.digitFix(date.getMonth()+1,2)+this.digitFix(date.getDate(),2);
	},
	preRender:function(year,month){
		this.setcurrentDate(year,month);
		this.cache.currentDateStrings.length=0;
	},
	renderHeader:function(){
		var html='';
		html+='<div class="week-w"><span class="pre" onclick="preMonth()"></span>'+this.currentDate.year+'年'+this.properties.monthNames[this.currentDate.month]+'月<span class="next" onclick="nextMonth()"></span></div>';
		return html;
	},
	renderWeekdays:function(){
		var html='';
		for(var i in this.properties.dayNames)
			html +=  '<span>'+this.properties.dayNames[i]+'</span>';
		return '<div class="week-name">'+html+'</div>';
	},
	render:function(year,month){
		this.preRender(year,month);
		var date=new Date(year,month,1),dateString='';
		var dayCount=this.dayArray[month];
		var preDayCount=date.getDay(),preDayCounter=preDayCount;
		var afterDayCounter=(this.dayArray[month]+preDayCount)%7==0?0:7-((this.dayArray[month]+preDayCount)%7);
		var html=this.renderHeader()+'<div class="timelist">';
		html+=this.renderWeekdays();
		html+='<ul>';
		while(preDayCounter-- > 0) html += '<li></li>';
		for(var i=1; i<=dayCount;i++ ){
			date=new Date(year,month,i);
			dateString=this.dateToString(date);
			this.cache.currentDateStrings.push(dateString);
			if(this.isToday(date))
				html += '<li class="now tm"><span>'+i+'</span><s></s></li>';
 			else 
				html += '<li class="tm"><span>'+i+'</span><s></s></li>';
		}
		while(afterDayCounter-- > 0) html += '<li><s></s></li>';
		html += '</ul><div class="clear"></div>';
		html += '</div>';
		$('#calendar').html(html);
		this.CallsetData(year,month);
	},
	init:function(year,month){
		if(year==null && month==null){
			var _date=new Date();
			year=_date.getFullYear();
			month=_date.getMonth();
		}
		this.render(year,month);
	},
	CallsetData:function(year,month){
		//var temp;
		//var objLi=$('.timelist .tm');
		//var offset;
		//var tmonth=month.toString();
		//if(tmonth.length==1){
		//	tmonth = '0' + tmonth;
		//}
		this.digitFix(month,2);
		var con = 0;
		//$.getJSON("/wishing.do",{"m":"wishing","mon":year+this.digitFix(month+1,2),"ajaxFlag":1,"t":Math.random()},
		$.getJSON("/wishing_calendar_mon_" + year+this.digitFix(month+1,2) + "_t_" + Math.random() + ".htm",
		function(json){
			var objLi=$('.timelist .tm');
			var offset;
			if(json!=null){
				con = json.rows.length;
			}
			if(json){
				var dataArray=new Array();
				$.each(json.rows,function(i,item){
					var Lis=item["state"];
					var LiDay=parseInt(item["day"])-1;
					if(Lis==1){
						objLi.eq(LiDay).addClass('on1');
					}else{
						objLi.eq(LiDay).addClass('on2');	
					}
					
					objLi.eq(LiDay).hover(function(){

							offset=$(this).offset();
							$("#wishLayer").find('p').html(item["wishword"].toString());
							$("#wishLayer").css({"left":(offset.left-60)+"px","top":(offset.top+30)+"px"}).show();		
					},function(){
							offset=$(this).offset();
							$("#wishLayer").find('p').html("");
							$("#wishLayer").css({"left":(offset.left-60)+"px","top":(offset.top+30)+"px"}).hide();		
					})
					
				/*	objLi.eq(LiDay).bind('mouseover',function(){
						position=$(this).position();
						$("#wishLayer").find('p').html(item["wishword"].toString());
						$("#wishLayer").css({"left":(position.left-60)+"px","top":(position.top+30)+"px"}).show();
					})*/
					dataArray.push('<div class="witem">');
					dataArray.push('<div class="witem-l">'+item["idate"]+'</div>');
					dataArray.push('<div class="witem-r">');
					dataArray.push('<p><span>我希望：</span><strong>•'+item["wishword"]+'</strong></p>');

					var date = new Date();
					if(date.getDate()==LiDay+1){
						dataArray.push('<p class="t2 mywish-a" id="remainTime" ></p>');
					}else{
						if(Lis==1){
							dataArray.push('<p class="t1 mywish-a">您的愿望己实现！</p>');
						}else if(Lis==0){
							dataArray.push('<p class="t2 mywish-a">您的愿望未实现！</p>');
						}else{
							dataArray.push('<p class="t2 mywish-a">您的自定义愿望！</p>');
						}
					}
					dataArray.push('</div></div>');
				});
		  		var curDate;
		  		monthStr = month+"";
		  		if(monthStr.length==1 && monthStr !=9){
		  			curDate = year + '-0' + (month+1);
		  		}else{
		  			curDate = year + '-' + (month+1);
		  		}
				if(con!=0){
					$("#mywish-list-o").html(dataArray.join(""));
				}else{
					$("#mywish-list-o").html(
								'<div class="witem">'+
									 '<div class="witem-l">'+
										curDate+
									 '</div>'+	
									 '<div class="witem-r">'+
										'<p> </p>'+
										'<p>您本月还未许愿,快来许愿吧！ </p>'+
									 '</div>'+
							   '</div>'
					);
				}
				
				HtmlMove("mywish-list","witem","scrollTop","play_pre","play_next");
				
				$("#wishT").html(json.total);
				$("#successT").html(json.successTotal);
			}
		});
		this.Countdown();
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
			   $("#remainTime").html("剩余时间：" + hour + ":" + minite + ":" + second);  
		  } else {//剩余时间小于或等于0的时候，就停止间隔函数  
		   	   window.clearInterval(InterValObj);  
		   //这里可以添加倒计时时间为0后需要执行的事件  
		  }  
	 },
	/**格式化数字为一个定长的字符串，前面补０ 
	 *参数: 
	 * Source 待格式化的字符串 
	 * Length 需要得到的字符串的长度 
	 */ 
	FormatNum:function(Source,Length){ 
		var strTemp=""; 
		for(i=1;i<=Length-Source.length;i++){ 
			strTemp+="0"; 
		} 
		return strTemp+Source; 
	},
	clearInterval:function(){
		window.clearInterval(InterValObj);  
	}
}
