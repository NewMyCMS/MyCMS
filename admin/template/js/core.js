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
	var script1 = document.getElementById("script1");
	if(script1){
		document.body.removeChild(script1);
	}
	if(elem.nodeName.toUpperCase() == "SPAN"){
		elem = elem.parentNode;
		url = elem.previousSibling.href;
	}else if(elem.nodeName.toUpperCase() == "LI"){
		if(previousMenuItem == elem){ return;}
		if(previousMenuItem){
			previousMenuItem.className = "defaultItem";
		}
		previousMenuItem = elem;
		elem.className = "currentItem";
		url = elem.firstChild.href;
		asideClick(url);
		document.getElementById("aside").style.minHeight = ((document.body.scrollHeight > screen.availHeight)?document.body.scrollHeight:screen.availHeight) + "px";
		if(document.getElementById("UEditor")){
			UE.delEditor("UEditor");
        	var ue = UE.getEditor('UEditor');
		}
		return;
	}else if(elem.nodeName.toUpperCase() == "A"){
		if(evt.preventDefault){
			evt.preventDefault();
		}else if(evt.returnValue){
			evt.returnValue = false;
		}
		url = elem.href;
		elem = elem.parentNode;
		if(elem.nodeName.toUpperCase() == "LI"){
			if(previousMenuItem == elem){ return;}
			if(previousMenuItem){
				previousMenuItem.className = "defaultItem";
			}
			previousMenuItem = elem;
			elem.className = "currentItem";
			asideClick(url);
			document.getElementById("aside").style.minHeight = ((document.body.scrollHeight > screen.availHeight)?document.body.scrollHeight:screen.availHeight) + "px";
			if(document.getElementById("UEditor")){
				UE.delEditor("UEditor");
        		var ue = UE.getEditor('UEditor');
			}
			return;
		}
	}
	if(!url){
		url = elem.childNodes[1].href;
	}
	if(previousMenu != elem.parentNode){
		previousMenu.className  = "asideMenuWrapper";
		previousMenu = elem.parentNode;
		previousMenu.className = "currentMenu";
		if(previousMenuItem){
			previousMenuItem.className = "defaultItem";
		}
		asideClick(url);
		document.getElementById("aside").style.minHeight = ((document.body.scrollHeight > screen.availHeight)?document.body.scrollHeight:screen.availHeight) + "px";
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

function checkAll(evt){
	if(!evt){ return;}
	var evt = getEvent(evt);
	var elem = getElement(evt);
	var form = elem.form;
	for(i in form.elements){
		if(elem.checked){
			if(form.elements[i].type == "checkbox"){
				form.elements[i].checked = "checked";
			}
		}else{
			if(form.elements[i].type == "checkbox"){
				form.elements[i].checked = "";
			}
		}
	}
}

function ul(evt){
	if(!evt){ return;}
	var evt = getEvent(evt);
	var elem = getElement(evt);
	var url = null;
	var form = document.getElementById("table_form");
	if(elem.nodeName == "A"){
		if(evt.preventDefault){
			evt.preventDefault();
		}else if(evt.returnValue){
			evt.returnValue = false;
		}
		elem = elem.parentNode;
	}else if(elem.nodeName == "UL"){
		return;
	}
	if(!document.getElementById("script1")){
		var script = document.getElementById("script");
		var script1 = document.createElement("script");
		script1.id = "script1";
		script1.innerHTML = script.innerHTML;
		document.body.appendChild(script1);
	}
	if(pageCount == 1){ return;}
	var p_ul = document.getElementById("p_ul");
	if(!previous){
		previous = p_ul.childNodes[1];
	}
	switch(elem.className){
		case "previous":
			if(pagination == 1){ return;}
			previous.className = "";
			previous = previous.previousSibling;
			previous.className = "current";
			var li = p_ul.firstChild.nextSibling;
			var p = parseInt(li.firstChild.innerHTML);
			if(pageCount > 5 && (pagination - p) == 2 && pagination > 3){
				var newLi = li.cloneNode(true);
				p--;
				newLi.firstChild.innerHTML = p;
				newLi.firstChild.href = "table.html#" + p;
				p_ul.removeChild(p_ul.lastChild.previousSibling);
				p_ul.insertBefore(newLi, li);
			}
			pagination -= 1;
			break;
		case "next":
			if(pagination == pageCount){ return;}
			previous.className = "";
			previous = previous.nextSibling;
			previous.className = "current";
			var li = p_ul.lastChild.previousSibling;
			var p = parseInt(li.firstChild.innerHTML);
			if(pageCount > 5 && (p - pagination) == 2 && (pageCount - pagination) > 2){
				var newLi = li.cloneNode(true);
				p++;
				newLi.firstChild.innerHTML = p;
				newLi.firstChild.href = "table.html#" + p;
				p_ul.removeChild(p_ul.firstChild.nextSibling);
				p_ul.insertBefore(newLi, p_ul.lastChild);
			}
			pagination += 1;
			break;
		default:
			var pagination1 = parseInt(elem.firstChild.innerHTML);
			if(pagination == pagination1){ return;}
			previous.className = "";
			previous = elem;
			previous.className = "current";
			pagination = pagination1;
			if(pageCount > 5){
				var m = parseInt(p_ul.childNodes[3].firstChild.innerHTML);
				var diff = m - pagination;
				if(diff > 0){
					if(m <= 3 && pagination <= 3){ return;}
					if(m < 5){ diff = 1;}
					for(var i = 0; i < diff; i++){
						var li = p_ul.childNodes[1];
						var p = parseInt(li.firstChild.innerHTML);
						var newLi = li.cloneNode(true);
						newLi.className = "";
						p--;
						newLi.firstChild.innerHTML = p;
						newLi.firstChild.href = "table.html#" + p;
						p_ul.removeChild(p_ul.lastChild.previousSibling);
						p_ul.insertBefore(newLi, li);
					}
				}else{
					diff = Math.abs(diff);
					if((pageCount - m) <= 2 && (pageCount - pagination) <= 2){ return;}
					if((pageCount - m) < 4){ diff = 1;}
					for(var i = 0; i < diff; i++){
						var li = p_ul.lastChild.previousSibling;
						var p = parseInt(li.firstChild.innerHTML);
						var newLi = li.cloneNode(true);
						newLi.className = "";
						p++;
						newLi.firstChild.innerHTML = p;
						newLi.firstChild.href = "table.html#" + p;
						p_ul.removeChild(p_ul.firstChild.nextSibling);
						p_ul.insertBefore(newLi, p_ul.lastChild);
					}
				}
			}
	}
}

function init(){
	var asideMenu = document.getElementById("asideMenu");
	document.getElementById("aside").style.minHeight = ((document.body.scrollHeight > screen.availHeight)?document.body.scrollHeight:screen.availHeight) + "px";
	for(var i in asideMenu.childNodes){
		if(asideMenu.childNodes[i].nodeName.toUpperCase() == "NAV") break;
	}
	asideMenu.childNodes[i].className = "currentMenu";
	previousMenu = asideMenu.childNodes[i];
	addEvent(document.getElementById("asideMenu"), "click", function(evt){
		fold(evt);
		addEvent(document.getElementById("checkAll"), "click", checkAll);
		addEvent(document.getElementById("p_ul"), "click", ul);
	});
}

addEvent(window, "load", init);
