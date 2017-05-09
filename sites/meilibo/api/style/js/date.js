/**
@author:Pancras
@version:1.01
@date:2008-03-02
@methods:
     DateUtil.getDate(year,month,day)        输入参数为年、月、日，返回一个JS的Date型对象
     DateUtil.formateDate(pattern,date)        输入参数一个是格式化字符串，比如"yyyy-MM-dd"，和JS日期对象，输出格式化后的日期字符串（基本上和Java的格式化类似）
     DateUtil.formateDateTime(pattern,date)    参数类型同上，但是该方法是在要格式化包含时间的时候使用，如"yyyy-MM-dd HH-mm-ss"
     DateUtil.isLeapYear(date)                输入一个JS日期型参数，判断该日期所在的年是不是闰年
**/    
     
      function DateUtil(){
         alert("请不要new这个对象，所有方法都是可以通过DateUtil.方法名来调用");
     }
//-------------------------------------------
     //定义类方法getDate(year,month,day)创建对应的日期对象
     function getDate(year,month,day)
      {
         var date = new Date();
         if(year)
          {
             date.setYear(year);
         }
         if(month)
          {
             date.setMonth(month-1);
         }
         if(day)
          {
             date.setDate(day);
         }
         return date;
     }
     DateUtil.getDate=getDate;
//-------------------------------------------
     //定义日期时间格式化
     DateUtil.yearPattern = [new RegExp("y{4}","g"),new RegExp("y{3}","g"),new RegExp("y{2}","g")];
     DateUtil.monthPattern = [new RegExp("M{2}","g"),new RegExp("M{1}","g")];
     DateUtil.datePattern = [new RegExp("d{2}","g"),new RegExp("d{1}","g")];
     DateUtil.hourPattern=[new RegExp("H{2}","g"),new RegExp("H{1}","g"),new RegExp("h{2}","g"),new RegExp("h{1}","g")];
     DateUtil.minutePattern=[new RegExp("m{2}","g"),new RegExp("m{1}","g")];
     DateUtil.secondPattern=[new RegExp("s{2}","g"),new RegExp("s{1}","g")];
     //格式化年
     function formatYear(pattern,year)
      {            
         var result = pattern.match(DateUtil.yearPattern[0]);
          if(result!=null){
             pattern = pattern.replace(DateUtil.yearPattern[0],year);
             return pattern;
          }else {
     
             //不允许匹配成yyy
             result = pattern.match(DateUtil.yearPattern[1]);
              if(result!=null){
                 throw "Unknown pattern:"+pattern;
                 
              }else {
                 result = pattern.match(DateUtil.yearPattern[2]);
                 if(result!=null)
                  {
                     pattern = pattern.replace(DateUtil.yearPattern[2],(""+year).substring(2,4));
                     return pattern;
                 }
             }
         }
     }
     //格式化月
     function formatMonth(pattern,month)
      {
         
         var result = pattern.match(DateUtil.monthPattern[0]);
          if(result!=null){
             if(month<10)
              {
                 month = "0"+month;
             }
             pattern = pattern.replace(DateUtil.monthPattern[0],month);
             return pattern;
         }else
          {
             result = pattern.match(DateUtil.monthPattern[1]);
              if(result!=null){
                 pattern = pattern.replace(DateUtil.monthPattern[1],month);
                 return pattern;
             }
              else{
                 throw "Unknown pattern:"+pattern;
             }
             
         }
     }
     //格式化日期
     function formatDay(pattern,day)
      {
             
         var result = pattern.match(DateUtil.datePattern[0]);
          if(result!=null){
             if(day<10)
              {
                 day = "0"+day;
             }
             pattern = pattern.replace(DateUtil.datePattern[0],day);
             return pattern;
         }else
          {
             result = pattern.match(DateUtil.datePattern[1]);
              if(result!=null){
                 pattern = pattern.replace(DateUtil.datePattern[1],day);
                 return pattern;
             }
              else{
                 throw "Unknown pattern:"+pattern;
             }
             
         }
     }
     //格式化小时
     function formatHour(pattern,hour)
      {        
             var result = pattern.match(DateUtil.hourPattern[0]);
              if(result!=null){
                 if(hour<10)
                  {
                     hour = "0"+hour;
                 }
                 pattern = pattern.replace(DateUtil.hourPattern[0],hour);
                 return pattern;
             }else
              {
                 result = pattern.match(DateUtil.hourPattern[1]);
                  if(result!=null){
                     pattern = pattern.replace(DateUtil.hourPattern[1],hour);
                     return pattern;
                 }
                  else{
                         result = pattern.match(DateUtil.hourPattern[2]);
                          if(result!=null){
                             if(hour<10)
                              {
                                 hour = "0"+hour;
                             }
                             if(hour>12)
                              {
                                  hour = hour-12;
                             }
                             pattern = pattern.replace(DateUtil.hourPattern[2],hour);
                             return pattern;
                          }else{
                             result = pattern.match(DateUtil.hourPattern[3]);
                              if(result!=null){
                                 if(hour>12)
                                  {
                                      hour = hour-12;
                                 }
                                 pattern = pattern.replace(DateUtil.hourPattern[3],hour);
                                 return pattern;
                              }else{
                                 throw "Unknown pattern:"+pattern;
                             }
                         }
                 
                 }
             }
     }
     //格式化分钟
     function formatMinute(pattern,minute)
      {
             
         var result = pattern.match(DateUtil.minutePattern[0]);
          if(result!=null){
             if(minute<10)
              {
                 minute = "0"+minute;
             }
             pattern = pattern.replace(DateUtil.minutePattern[0],minute);
             return pattern;
         }else
          {
             result = pattern.match(DateUtil.minutePattern[1]);
              if(result!=null){
                 pattern = pattern.replace(DateUtil.minutePattern[1],minute);
                 return pattern;
             }
              else{
                 throw "Unknown pattern:"+pattern;
             }
             
         }
     }
     //格式化秒
     function formatSecond(pattern,second)
      {
             
         var result = pattern.match(DateUtil.secondPattern[0]);
          if(result!=null){
             if(second<10)
              {
                 second = "0"+second;
             }
             pattern = pattern.replace(DateUtil.secondPattern[0],second);
             return pattern;
         }else
          {
             result = pattern.match(DateUtil.secondPattern[1]);
              if(result!=null){
                 pattern = pattern.replace(DateUtil.secondPattern[1],second);
                 return pattern;
             }
              else{
                 throw "Unknown pattern:"+pattern;
             }
             
         }
     }
     function formateDate(pattern,date)
      {
         var oldpattern = pattern;
          try{
             pattern = formatYear(pattern,date.getFullYear());
             pattern = formatMonth(pattern,date.getMonth()+1);
             pattern = formatDay(pattern,date.getDate());
             return pattern;
          }catch(err){
              if(err=="Unknown pattern"){
                 alert("Unknown pattern:"+oldpattern);
              }else{
                 alert(err);
             }
             
         }    
     }
     function formateDateTime(pattern,date)
      {
         var oldpattern = pattern;
          try{
             pattern = DateUtil.formateDate(pattern,date);
             pattern = formatHour(pattern,date.getHours());
             pattern = formatMinute(pattern,date.getMinutes());
             pattern = formatSecond(pattern,date.getSeconds());
             return pattern;
          }catch(err){
              if(err=="Unknown pattern"){
                 alert("Unknown pattern:"+oldpattern);
              }else{
                 alert(err);
             }
             
         }    
     }
     
     function formateDateMonth(pattern,date)
      {
         var oldpattern = pattern;
          try{
             pattern = formatYear(pattern,date.getFullYear());
             pattern = formatMonth(pattern,date.getMonth()+1);
             return pattern;
          }catch(err){
              if(err=="Unknown pattern"){
                 alert("Unknown pattern:"+oldpattern);
              }else{
                 alert(err);
             }
             
         }    
     }

     DateUtil.formateDate = formateDate;
     DateUtil.formateDateTime = formateDateTime;
     DateUtil.formateDateMonth = formateDateMonth;
//-------------------------------------------
     //是不是闰年
     function isLeapYear(date)
      {
         var year = DateUtil.getYear();
         if(year%100==0)
          {
             if(year%400==0)
              {
                 return true;
             }
         }    
         if(year%100!=0)     {
             if(year%4==0)
            {
                 return true;
             }
         }    
         return false;
     }

     DateUtil.isLeapYear = isLeapYear;
    