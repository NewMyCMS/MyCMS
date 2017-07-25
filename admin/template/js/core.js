var previousMenu = null;
var previousMenuItem = null;

function addEvent(elem, evtType, handler, bool){
	var bool = bool || false;
	if(elem.addEventListener){
		elem.addEventListener(evtType, handler, bool);
	}else if(elem.attachEvent){
		elem.attachEvent("on"+evtType, handler);
	}
}

function getEvent(evt){
	return (evt)?evt:((window.event)?window.event:null);
}

function getElement(evt){
	return (evt.srcElement)?evt.srcElement:((evt.target)?evt.target:null);
}

function myFireEvent(elem, evtType, evt){
	if(elem.dispatchEvent(evt)){
		elem.dispatchEvent(evt);
	}else{
		elem.fireEvent(evtType, evt);
	}
}

function fold(evt){
	if(!evt){ return;}
	var evt = getEvent(evt);
	var elem = getElement(evt);
	var url = "";
	if(elem.nodeName.toUpperCase() == "SPAN"){
		elem = elem.parentNode;
		url = elem.previousSibling.href;
	}else if(elem.nodeName.toUpperCase() == "LI"){
		if(previousMenuItem){
			previousMenuItem.className = "defaultItem";
		}
		previousMenuItem = elem;
		elem.className = "currentItem";
		url = elem.firstChild.href;
		asideClick(url);
		if(document.getElementById("UEditor")){
			UE.delEditor("UEditor");
        	var ue = UE.getEditor('UEditor');
		}
		if(document.getElementById("kindEditor")){
        	KindEditor.ready(function(K) {
                window.editor = K.create('#kindEditor');
        	});
		}
		return;
	}else if(elem.nodeName.toUpperCase() == "A"){
		if(evt.preventDefault){
			evt.preventDefault();
		}else if(evt.returnValue){
			evt.returnValue = false;
		}
		url = elem.href;
		if(elem.parentNode.nodeName.toUpperCase() == "LI"){
			elem = elem.parentNode;
			if(previousMenuItem){
				previousMenuItem.className = "defaultItem";
			}
			previousMenuItem = elem;
			elem.className = "currentItem";
			asideClick(url);
			if(document.getElementById("UEditor")){
				UE.delEditor("UEditor");
        		var ue = UE.getEditor('UEditor');
			}
			if(document.getElementById("editor_id")){
        		KindEditor.ready(function(K) {
                	window.editor = K.create('#editor_id');
        		});
			}
			return;
		}else{
			elem = elem.parentNode;
		}
	}
	if(!url){
		url = elem.firstChild.href;
	}
	if(previousMenu != elem.parentNode){
		previousMenu.className  = "asideMenuWrapper";
		previousMenu = elem.parentNode;
		previousMenu.className = "currentMenu";
		asideClick(url);
	}
}

function creatXHR(){
	var xhr = null;
	if(window.XMLHttpRequest){
 		xhr = new XMLHttpRequest();
	}else if(window.ActivexObject){
    	xhr = new ActivexObject("Microsoft.XMLHTTP");
	}else{
		alert("创建XMLHttpRequest错误！");
	}
	return xhr;
}

function asideClick(url){
	var xhr = creatXHR();
	xhr.onreadystatechange = function(){
		if(xhr.readyState === 4){
			if(xhr.status === 200){
				document.getElementById("main").innerHTML = xhr.responseText;
			}
		}else{
			dialog.innerHTML = "错误!"+xhr.readyState+xhr.status;
		}
	};
 	xhr.open("post", url, false);
 	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("OK");
}

function formSerialize(form){
	var parts = [], field = null;
	for(var i = 0, len = form.elements.length; i < len; i++){
		field = form.elements[i];
		switch(field.type){
			case "select-one":
				for(var j = 0, oLen = field.options.length; j < oLen; j++){
					var option = field.options[j];
					if(option.selected){
						var oValue;
						if(option.hasAttribute){
							oValue = option.hasAttribute("value") ? option.value : option.text;
						}else{
							oValue = option.Attributes["value"].specified ? option.value : option.text;
						}
						parts.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(oValue));
						break;
					}
				}
			case "select-multiple":
				for(var j = 0, oLen = field.options.length; j < oLen; j++){
					var option = field.options[j];
					if(option.selected){
						var oValue;
						if(option.hasAttribute){
							oValue = option.hasAttribute("value") ? option.value : option.text;
						}else{
							oValue = option.Attributes["value"].specified ? option.value : option.text;
						}
						parts.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(oValue));
					}
				}
				break;
			case "radio":
				if(!field.checked){
				break;
				}
			case "checkbox":
				if(!field.checked){
					break;
				}
			case "button":
				break;
			case "reset":
				break;
			case "image":
				break;
			case "submit":
				break;
			case "file":
				break;
			case undefined:
				break;
			default:
				parts.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
		}
	}
	return parts.join("&");
}

function init(){
	var asideMenu = document.getElementById("asideMenu");
	document.getElementById("aside").style.minHeight = ((document.body.scrollHeight > screen.availHeight)?document.body.scrollHeight:screen.availHeight) + "px";
	for(var i in asideMenu.childNodes){
		if(asideMenu.childNodes[i].nodeName.toUpperCase() == "NAV") break;
	}
	asideMenu.childNodes[i].className = "currentMenu";
	previousMenu = asideMenu.childNodes[i];
	addEvent(document.getElementById("asideMenu"), "click", fold);
}

addEvent(window, "load", init);
