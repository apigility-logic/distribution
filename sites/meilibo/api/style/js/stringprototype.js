/**
 *@Title:扩展JS内部对象的功能方法！使用prototype原型方法
 *@Author:铁木箱子
 *@Date:2006-10-17
 */

/**
 *Desc:扩展String对象的方法!注意所有的方法都是返回新字符串,不会修改原字符串！
 */
 
//在字符串末尾追加字符串
String.prototype.append=function(aStr){
 return this.concat(aStr);
}
 
//删除指定索引位置的字符，索引无效将不删除任何字符
String.prototype.deleteCharAt=function(sIndex){
 if(sIndex<0 || sIndex>=this.length){
  return this.valueOf();
 }else if(sIndex==0){
  return this.substring(1,this.length);
 }else if(sIndex==this.length-1){
  return this.substring(0,this.length-1);
 }else{
  return this.substring(0,sIndex)+this.substring(sIndex+1);
 }
}
 
//删除指定索引间的字符串.sIndex和eIndex所在的字符不被删除！
String.prototype.deleteString=function(sIndex,eIndex){
 if(sIndex==eIndex){
  return this.deleteCharAt(sIndex);
 }else{
  if(sIndex>eIndex){
   var tIndex=eIndex;
   eIndex=sIndex;
   sIndex=tIndex;
  }
  if(sIndex<0)sIndex=0;
  if(eIndex>this.length-1)eIndex=this.length-1;
  return this.substring(0,sIndex+1)+this.substring(eIndex,this.length);
 }
}
 
//检查字符串是否以某个字符串(aStr)结尾
String.prototype.endsWith=function(aStr){
 if(aStr.length>this.length)return false;
 return (this.lastIndexOf(aStr)==(this.length-aStr.length))?true:false;
}
 
//比较两个字符串是否相等。其实也可以直接使用==进行比较
String.prototype.equals=function(aStr){
 if(this.length!=aStr.length){
  return false;
 }else{
  for(var i=0;i<this.length;i++){
   if(this.charAt(i)!=aStr.charAt(i)){
    return false;
   }
  }
  return true;
 }
}
 
//比较两个字符串是否相等，不区分大小写!
String.prototype.equalsIgnoreCase=function(aStr){
 if(this.length!=aStr.length){
  return false;
 }else{
  var tmp1=this.toLowerCase();
  var tmp2=aStr.toLowerCase();
  return tmp1.equals(tmp2);
 }
}
 
//将指定的字符串插入到指定的位置后面!索引无效将直接追加到字符串的末尾
String.prototype.insert=function(ofset,aStr){
 if(ofset<0 || ofset>=this.length-1){
  return this.append(aStr);
 }
 return this.substring(0,ofset+1)+aStr+this.substring(ofset+1);
}
 
//查看该字符串是否是数字串
String.prototype.isAllNumber=function(){
 for(var i=0;i<this.length;i++){
  if(this.charAt(i)<'0' || this.charAt(i)>'9'){
   return false;
  }
 }
 return true;
}
 
//将该字符串反序排列
String.prototype.reverse=function(){
 var aStr="";
 for(var i=this.length-1;i>=0;i--){
  aStr=aStr.concat(this.charAt(i));
 }
 return aStr;
},
 
//将指定的位置的字符设置为另外指定的字符或字符串.索引无效将直接返回不做任何处理！
String.prototype.setCharAt=function(sIndex,aStr){
 if(sIndex<0 || sIndex>this.length-1){
  return this.valueOf();
 }
 return this.substring(0,sIndex)+aStr+this.substring(sIndex+1);
}
 
//检查该字符串是否以某个字符串开始
String.prototype.startsWith=function(aStr){
 if(aStr.length > this.length){
  return false;
 }
 return (this.indexOf(aStr)==0)?true:false;
}
 
//去掉字符串两端的空格
String.prototype.trim=function(){
 return this.replace(/(^\s*)|(\s*$)/g, "");
}
 
//计算长度，每个汉字占两个长度，英文字符每个占一个长度
String.prototype.ucLength=function(){
 var len = 0;
   for(var i=0;i<this.length;i++){
     if(this.charCodeAt(i)>255)
         len+=2;
     else
         len++;
   }
 return len;
}

//让JS能够以字节方式计算长度
String.prototype.lenB = function(){var arr=this.match(/[^\x00-\xff]/ig);return this.length+(arr==null?0:arr.length);}
//从左取n个字符或字节
String.prototype.left = function(num,mode){if(!/\d+/.test(num))return(this);var str = this.substr(0,num);if(!mode) return str;var n = str.lenB() - str.length;num = num - parseInt(n/2);return this.substr(0,num);}
//从右取n个字符或字节
String.prototype.right = function(num,mode){if(!/\d+/.test(num))return(this);var str = this.substr(this.length-num);if(!mode) return str;var n = str.lenB() - str.length;num = num - parseInt(n/2);return this.substr(this.length-num);}
//让你的JS支持Trim
String.prototype.trim=function(){return this.replace(/(^\s*)|(\s*$)/g,"");}
//去掉左边的空格
String.prototype.Ltrim = function(){return this.replace(/(^\s*)/g, "");}
//去掉右边的空格
String.prototype.Rtrim = function(){return this.replace(/(\s*$)/g, "");}

var formats = {
            '%': function(val) { return '%'; },
            'b': function(val) { return parseInt(val, 10).toString(2); },
            'c': function(val) { return String.fromCharCode(parseInt(val, 10)); },
            'd': function(val) { return parseInt(val, 10) ? parseInt(val, 10) : 0; },
            'u': function(val) { return Math.abs(val); },
            'f': function(val, p) { return (p > -1) ? Math.round(parseFloat(val) * Math.pow(10, p)) / Math.pow(10, p) : parseFloat(val); },
            'o': function(val) { return parseInt(val, 10).toString(8); },
            's': function(val) { return val; },
            'S': function(val, p) { var len = p - val.toString().length; for (i = 0; i < len; i++) val = '0' + val; return val; },
            'x': function(val) { return ('' + parseInt(val, 10).toString(16)).toLowerCase(); },
            'X': function(val) { return ('' + parseInt(val, 10).toString(16)).toUpperCase(); }
        };

        var re = /%(?:(\d+)?(?:\.(\d+))?|\(([^)]+)\))([%bcdufosSxX])/g;

        var dispatch = function(data) {
            if (data.length == 1 && typeof data[0] == 'object') {
                data = data[0];
                return function(match, w, p, lbl, fmt, off, str) {
                    return formats[fmt](data[lbl]);
                };
            } else {
                var idx = 0;
                return function(match, w, p, lbl, fmt, off, str) {
                    return formats[fmt](data[idx++], p);
                };
            }
        };

        String.prototype.sprintf = function() {
            //var argv = Array.apply(null, arguments);
            return this.replace(re, dispatch(arguments));
        }
        String.prototype.vsprintf = function(data) {
            return this.replace(re, dispatch(data));
        }

        //sprintf
/*
        var classixxc = '%.5S'.sprintf(10);
        alert(classixxc)
        //classic = '00010'
        var classic = '%s %d% %.3f'.sprintf('string', 40, 3.141593);
        alert(classic)
        // classic = 'string 40% 3.142'
        var named = '%(name)s: %(value)d'.sprintf({ name: 'age', value: 40 });
        alert(named)
        //named = 'age: 40'
        //vsprintf
        var classisc = '%s %d% %.3f'.vsprintf(['string', 40, 3.141593]);
        alert(classisc)
        // classisc = 'string 40% 3.142'
    }
    
    */


