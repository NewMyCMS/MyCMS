var prevCF = null;
var dialog = null;

function creatXHR(){
 var xhr = null;
 if(window.XMLHttpRequest){
  xhr = new XMLHttpRequest();
 }else if(window.ActivexObject){
  var xhr_ver = ['MSXML2.XMLHTTP.3.0', 'Msxml2.XMLHTTP.6.0'];
  for(var i = 0; i < xhr_ver.length; i++){
   try{
    xhr = new ActivexObject(xhr_ver[i]);
   }catch(e){
    //
   }
  }
 }else{
  alert("创建XMLHttpRequest错误！");
 }
 return xhr;
}

function commentHandler(btn){
 var form = btn.form;
 var xhr = creatXHR();
 if(dialog){
  dialog.innerHTML = "";
 }else{
  dialog = document.createElement("div");
  document.body.appendChild(dialog);
  dialog.className = "fixed";
 }
 xhr.onreadystatechange = function(){
  if(xhr.readyState === 4){
   if(xhr.status === 200){
    dialog.innerHTML = xhr.responseText;
   }
  }else{
   dialog.innerHTML = "错误!"+xhr.readyState+xhr.status;
  }
 };
 xhr.open("post", "http://127.0.0.1/comment.php", true);
 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 xhr.send(formSerialize(form));
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

function creatCommentForm(pid, aid, hascomment, a){
 if(prevCF != null){
  prevCF.removeChild(prevCF.lastChild);
 }
 var r = a.parentNode;
 var rb = r.parentNode;
 var w = rb.parentNode;
 var c = w.parentNode;
 if(prevCF === c){
  prevCF = null;
  return;
 }
 /*var f = '<div class="form"><form id="reply-form" action="comment.php" method="post" onsubmit=""><div id="reply-body"><input id="pid" name="pid" type="hidden"><input id="aid" name="aid" type="hidden"><input id="hascomment" name="hascomment" type="hidden"><textarea id="comment_content" name="comment_content"></textarea></div><div style="height:20px;padding:5px 5px 0 5px;"><span class="fright"><input id="submit" type="button" onclick="commentHandler(this)" value="发表"></span></div></form><div id="relative"></div></div>';*/
 c.innerHTML += f;
 document.getElementById("pid").value = pid;
 document.getElementById("aid").value = aid;
 document.getElementById("hascomment").value = hascomment;
 prevCF = c;
}

function addclass(cid){
 var pidobj = document.getElementById("parent");
 //alert('pidobj.name');
 for(var i = 0; i < pidobj.options.length; i++){
  if(pidobj.options[i].value == cid){
  //alert('a');
   pidobj.options[i].selected = true;
   document.getElementById("name").focus();
   break;
  }
 }
   //return false;
}

function del(cid){
 var bool = confirm("这将永久删除该栏目及其非终极子栏目！该栏目的所有内容信息将保持原有数据结构不变。终极栏目将被放入回收站(仅为其保留ID和名称)，你可以在回收站转移或永久删除终极栏目及其内容信息。在严谨目录模式下，将删除该栏目原有目录及其子栏目目录，包括为该栏目生成的静态html；在混乱目录结构模式下，将保留该栏目及其子栏目的目录结构，并且保留为该栏目生成的静态html。\n你确定要执行'删除'操作吗？");
 if(bool){
  window.location = "manage_category.php?action=delete&cid=" + cid;
 }
}

function selectType(obj){
 
}

function submitHandle(btn){
 var bool = confirm("改变栏目别名，改变栏目结构，其子栏目结构将跟随其改变。在'严谨目录结构模式'下，改将删除该栏目原有目录及其子栏目目录，包括为该栏目生成的静态html，然后为其及其子栏目重建目录结构。在'混乱目录结构模式'下，若目录结构改变，将保留该栏目及其子栏目原有目录结构，然后仅为该栏目建立新目录，其子目录目录结构保持不变；若目录结构未改变，则不操作目录结构。\n你确定要执行此操作吗？");
}







