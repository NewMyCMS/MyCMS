  
var  dragDrop  =  function(dragArea, moveArea){//拖拽  
this.dragArea  =  dragArea;
this.moveArea  =  moveArea;
this.xdom  =  null;  
this.mask  =  null;//遮挡iframe  
this.x  =  0;  
this.y  =  0;  
this.dbsw  =  top.document.scrollWidth;  
this.dbsh  =  top.document.scrollHeight;  
this.init();  
};  
dragDrop.prototype  =  {  
getPosition:function(){//取元素坐标，如元素或其上层元素设置position  relative，应该getpos(子元素).y-getpos(父元素).y  
return  document.documentElement.getBoundingClientRect  &&  function(o){//IE,FF,Opera  
var  pos  =  o.getBoundingClientRect(),  root  =  o.ownerDocument  ||  o.document;  
return  {x:pos.left+root.documentElement.scrollLeft,y:pos.top+root.documentElement.scrollTop};  
}  ||  function(o){//Safari,Chrome,Netscape,Mozilla  
var  x  =  0,  y  =  0;  
do{x  +=  o.offsetLeft;y  +=  o.offsetTop;}while((o=o.offsetParent));  
return  {'x':x,'y':y};  
};  
}(),  
setPos:function(obj,x,y){  
obj.style.left  =  x  +  "px";  
obj.style.top  =  y  +  "px";  
},  
mDown:function(e){  
var  isWK  =  (/applewebkit/i.test(navigator.appVersion.replace(/\s/g,'')));  
var  pos  =  this.getPosition(this.moveArea);  
this.x  =  (isWK)?e.offsetX:(e.clientX-pos.x);  
this.y  =  (isWK)?e.offsetY:(e.clientY-pos.y);  
pos  =  null;  
if(this.dragArea.setCapture){this.dragArea.setCapture();}else{e.preventDefault();}  
this.mask.style.left  =  this.xdom.parentNode.scrollLeft+'px';;  
this.mask.style.top  =  this.xdom.parentNode.scrollTop+'px';  
this.mask.style.display  =  'block';  
this.xdom.onmousemove  =  this.mMove.bind(this);  
this.xdom.onmouseup  =  this.mUp.bind(this);  
},  
mMove:function(e){  
this.dragArea.style.cursor  =  "move";  
e  =  e||window.event;  
var  mx  =  (e.clientX  -  this.x);  
var  my  =  (e.clientY  -  this.y);  
var  rx  =  this.xdom.offsetWidth-this.moveArea.offsetWidth;  
var  ry  =  this.xdom.offsetHeight-this.moveArea.offsetHeight;  
mx  =  (mx<=0)?0:(mx>=rx)?rx:mx;  
my  =  (my<=0)?0:(my>=ry)?ry:my;  
this.setPos(this.moveArea,mx,my);  
},  
mUp:function(e){  
this.dragArea.style.cursor  =  "default";  
e  =  e||window.event;  
var  mx  =  (e.clientX  -  this.x);  
var  my  =  (e.clientY  -  this.y);  
var  rx  =  this.xdom.offsetWidth-this.moveArea.offsetWidth;  
var  ry  =  this.xdom.offsetHeight-this.moveArea.offsetHeight;  
mx  =  (mx<=0)?0:(mx>=rx)?rx:mx;  
my  =  (my<=0)?0:(my>=ry)?ry:my;  
this.setPos(this.moveArea,mx,my);  
if(this.dragArea.releaseCapture){this.dragArea.releaseCapture();}  
this.mask.style.display  =  'none';  
this.xdom.onmousemove  =  function(){};  
this.xdom.onmouseup  =  function(){};  
},  
init:function(){  
if(Function.prototype.bind==undefined){  
Function.prototype.bind  =  function(obj){  
var  owner  =  this,args  =  Array.prototype.slice.call(arguments),callobj  =  Array.prototype.shift.call(args);  
return  function(e){e=e||top.window.event||window.event;owner.apply(callobj,[e].concat(args));}  
};  
}  
var  ieop  =  (/(?:microsoft|opera)/i.test(navigator.appName));  
var  xDom  =  (window.top.document==document)?document:(window.top.document.body.tagName!='BODY')?document:window.top.document;  
this.xdom  =  (window.top.document==document)?document.body:(window.top.document.body.tagName=='BODY')?window.top.document.body:(ieop)?document.body:window.top.document.documentElement;  
var  xdoc  =  xDom.createDocumentFragment();  
this.mask  =  xDom.createElement('div');  
this.mask.style.cssText  =  'display:none;position:absolute;left:0;top:0;width:110%;height:110%;overflow:hidden;background:#000;-moz-opacity:0;opacity:0;filter:alpha(opacity=0);z-index:9999;';  
xdoc.appendChild(this.mask);  
this.xdom.appendChild(xdoc);  
xDom  =  xdoc  =  null;  
this.moveArea.style.position  =  "absolute";  
this.moveArea.style.zIndex  =  10000;  
this.dragArea.onmousedown  =  this.mDown.bind(this);  
}  
};  

var  maskTips  =  function(width,height){//遮罩提示框  
this.width  =  width||500;  
this.height  =  height||400;  
this.xdom  =  null;  
this.mask  =  null;  
this.ifr  =  null;  
this.layer  =  null;  
this.title  =  null;  
this.content  =  null;  
this.isOpen  =  false;  
this.init();  
};  
maskTips.prototype  =  {  
getStyle:function(dom,stylename){  
if(dom.currentStyle){  
return  dom.currentStyle[stylename];  
}else{  
return  window.getComputedStyle(dom,null).getPropertyValue(stylename);  
}  
},  
open:function(width,height){
var  xdom  =  this.xdom.documentElement;xelm  =  this.xdom.body;width  =  width||this.width;height  =  height||this.height;  
xdom.style.width  =  '100%';xdom.style.height  =  '100%';xdom.style.overflow  =  'hidden';  
xelm.style.width  =  '100%';xelm.style.height  =  '100%';xelm.style.overflow  =  'hidden';  
this.ifr.style.left  =  xdom.scrollLeft+'px';  
this.ifr.style.top  =  xdom.scrollTop+'px';  
this.ifr.style.display  =  'block';  
this.mask.style.left  =  xdom.scrollLeft+'px';  
this.mask.style.top  =  xdom.scrollTop+'px';  
this.mask.style.display  =  'block';  
this.content.style.width  =  width-10+'px';  
this.content.style.height  =  height-40+'px';  
var  left  =  xdom.scrollLeft+xdom.offsetWidth/2-(width/2)+'px';  
var  top  =  xdom.scrollTop+this.mask.offsetHeight/2-(height/1.8)+'px';  
this.layer.style.width  =  width+'px';  
this.layer.style.height  =  height+'px';  
this.layer.style.left  =  left;  
this.layer.style.top  =  top;  
this.layer.style.display  =  'block';  
this.isOpen  =  true;  
},  

hide:function(){  
this.layer.style.display  =  'none';  
this.mask.style.display  =  'none';  
this.ifr.style.display  =  'none';  
var  xdom  =  this.xdom.documentElement,xelm  =  this.xdom.body;  
xdom.style.width  =  'auto';xdom.style.height  =  'auto';xdom.style.overflow  =  'auto';  
xelm.style.width  =  'auto';xelm.style.height  =  'auto';xelm.style.overflow  =  'auto';  
this.isOpen  =  false;  
},  
init:function(){  
var  cssMask  =  'display:none;position:absolute;left:0;top:0;width:110%;height:110%;overflow:hidden;background:#000;-moz-opacity:0.6;opacity:0.6;filter:alpha(opacity=60);z-index:101;border:none;';  
var  cssIfr  =  'display:none;position:absolute;left:0;top:0;width:110%;height:110%;overflow:hidden;background:#fff;-moz-opacity:0;opacity:0;filter:alpha(opacity=0);z-index:100;border:none;';  
var  cssLayer  =  'display:none;position:absolute;left:0;top:0;width:'+this.width+'px;height:'+this.height+'px;overflow:hidden;zoom:1;background:white;border:5px  solid  #ddd;z-index:102;padding:0;';  
var  ieop  =  (/(?:microsoft|opera)/i.test(navigator.appName));  
var  xDom  =  (window.top.document==document)?document:(window.top.document.body.tagName!='BODY')?document:window.top.document;  
var  xElm  =  (window.top.document==document)?document.body:(window.top.document.body.tagName=='BODY')?window.top.document.body:(ieop)?document.body:window.top.document.documentElement;  
this.xdom  =  xDom;  
var  xdoc  =  xDom.createDocumentFragment();  
this.mask  =  xDom.createElement('div');  
this.ifr  =  xDom.createElement('iframe');  
this.layer  =  xDom.createElement('div');  
xdoc.appendChild(this.ifr);  
xdoc.appendChild(this.mask);  
xdoc.appendChild(this.layer);  
this.layer.innerHTML  =  '<div  style="height:30px;background:#ddd;text-align:right;overflow:hidden;zoom:1;"><h3  style="float:left;margin:0;padding:0;font-size:12px;">上传完毕后请点关闭按钮</h3><button  style="width:25px;font-size:12px;padding:0;text-align:center;">×</button></div><div  class="tips-bd"  style="width:'+(this.width-10)+'px;height:'+(this.height-40)+'px;padding:5px;overflow:auto;">这里是内容</div><div  class="tips-ft"></div>';  
this.mask.style.cssText  =  cssMask;  
this.ifr.style.cssText  =  cssIfr;  
this.layer.style.cssText  =  cssLayer; 
this.title  =  this.layer.childNodes[0].childNodes[0];  
this.content  =  this.layer.childNodes[1];  
new  dragDrop(this.layer.childNodes[0],this.layer);  
this.layer.getElementsByTagName('button')[0].onclick  =  (function(o){return  function(){o.hide()}})(this);  
window.setTimeout(function(){xElm.appendChild(xdoc);xDom  =  xElm  =  xdoc  =  null;});
}
};
var  x  =  new  maskTips(300,200);
function  openDiv(){
x.open(750,700);//750宽度，700高度
}