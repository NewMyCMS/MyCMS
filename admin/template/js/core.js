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
	if(elem.nodeName.toUpperCase() == "SPAN"){
		elem = elem.parentNode;
	}else if(elem.nodeName.toUpperCase() == "LI"){
		if(previousMenuItem){
			previousMenuItem.className = "defaultItem";
		}
		previousMenuItem = elem;
		elem.className = "currentItem";
		return;
	}else if(elem.nodeName.toUpperCase() == "A"){
		if(evt.preventDefault){
			evt.preventDefault();
		}else if(evt.returnValue){
			evt.returnValue = false;
		}
		if(elem.parentNode.nodeName.toUpperCase() == "LI"){
			elem = elem.parentNode;
			if(previousMenuItem){
				previousMenuItem.className = "defaultItem";
			}
			previousMenuItem = elem;
			elem.className = "currentItem";
			return;
		}else{
			elem = elem.parentNode;
		}
	}
	if(previousMenu != elem.parentNode){
		previousMenu.className  = "asideMenuWrapper";
		previousMenu = elem.parentNode;
		previousMenu.className = "currentMenu";
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
	addEvent(document.getElementById("asideMenu"), "click", fold);
}

addEvent(window, "load", init);
