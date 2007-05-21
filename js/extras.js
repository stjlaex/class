//  Functions used for extra DOMwindows and more

function stats(grades, gradestats, percents){

DOMWindow.zCount=0
DragManager = {}
DragManager.trackMouse=function(e){
	DragManager.mousex = e.pageX
	DragManager.mousey = e.pageY
	if (DragManager.grabbed) {
		var obj = DragManager.grabbedObj.proxyFor||DragManager.grabbedObj
		var x = Math.round(DragManager.mousex - DragManager.lyrx - 7) + "px"
		var y = Math.round(DragManager.mousey - DragManager.lyry - 7) + "px"
		obj.style.left = x
		obj.style.top = y
	}
}
DragManager.grab = function(e){
	DragManager.lyrx = e.layerX
	DragManager.lyry = e.layerY
	DragManager.grabbed=true
	DragManager.grabbedObj = this.proxyFor||this
	DragManager.grabbedObj.style.zIndex = parseInt(DragManager.grabbedObj.style.zIndex) + 10
}
DragManager.ungrab = function(){
	DragManager.grabbed=false
	DragManager.grabbedObj = null	
}
DragManager.manage = function(obj){
	 	obj.addEventListener("mousedown",DragManager.grab,false)
	 	obj.addEventListener("mouseup",DragManager.ungrab,false)
}

	insertText=	"<iframe frameborder='1' scrolling='no' width='400' height='300' name='F1'></iframe> <table>";
//	for (grade in grades){
//		insertText=insertText+"<tr><td>"+grades[grade]+"</td><td>"+gradestats[grade]+"</td></tr>";
//	}
	insertText=insertText+"</table>";

	myDomWin1 = new DOMWindow(40,40,450,350,"Assessment Statistics");
	document.body.appendChild(myDomWin1);
//	window.onmousemove = DragManager.trackMouse;

_DiagramTarget=window.frames["F1"];
_DiagramTarget.document.open();
_DiagramTarget.document.writeln("<html><head></head><body bgcolor='#eeeeee'>");
var D=new Diagram();
D.SetFrame(50, 20, 350, 280);
D.SetBorder(-1, 6, 0, 100);
D.SetText("","", "Assessment Statistics");
D.XScale=0;
D.Draw("#FFFF80", "#004080", false);
var i, j, y;
i=0;
_BFont="font-family:Verdana;font-weight:bold;font-size:8pt;line-height13pt;"

for (i=1;i in grades;i++)
{ 
  y=percents[i];
  j=D.ScreenX(i);
  label=grades[i]+"<br>"+percents[i]+"%";
  leftset=j-20;
  rightset=j+20;
  if (i%2==0) new Bar(leftset, D.ScreenY(y), rightset, D.ScreenY(0), "#0000FF", label, "#000000", "");
  else new Bar(leftset, D.ScreenY(y), rightset, D.ScreenY(0), "#FF0000", label, "#000000", "");
}
_DiagramTarget.document.close();

}



//DOM windows after Scott Andrew

function DOMWindow(x,y,w,h,text){

 var winBody = new DOMDiv(x,y,w,h,"#cccccc")
  winBody.style.borderStyle = "outset"
  winBody.style.borderWidth = "2px"
  winBody.style.borderColor = "#aaaaaa"
  winBody.style.zIndex = (DOMWindow.zCount++)
  
 
 var toolBar = new DOMDiv(4,4,w-14,18,"#006699")
  toolBar.style.color = "#ffffff"
  toolBar.style.fontFamily = "arial"
  toolBar.style.fontSize = "10pt"
  toolBar.style.paddingLeft="4px"
  
  toolBar.proxyFor = winBody
  DragManager.manage(toolBar)
 
 var contentArea = new DOMDiv(4,26,w-10,h-40,"#ffffff")
  contentArea.style.width = (parseInt(contentArea.style.width)-7)+"px"
  contentArea.style.borderColor="#cccccc"
  contentArea.style.borderStyle="inset"
  contentArea.style.borderWidth="1px"
  contentArea.style.overflow="auto"
  contentArea.style.paddingLeft="4px"
  contentArea.style.paddingRight="2px"
  contentArea.style.fontFamily = "arial"
  contentArea.style.fontSize = "10pt"
  winBody.content = contentArea;
  
 var titleText = document.createTextNode(text)
 
 contentArea.innerHTML = insertText

 winBody.appendChild(contentArea)
 toolBar.appendChild(titleText)
 winBody.appendChild(toolBar)
 return winBody

}

function DOMDiv (x,y,w,h,col){
	var lyr = document.createElement("DIV")
 	 lyr.style.position = "absolute"
	 lyr.style.left = x + "px"
	 lyr.style.top = y + "px"
	 lyr.style.width = w + "px"
	 lyr.style.height = h + "px"
	 lyr.style.backgroundColor = col
	 lyr.style.visibility = "visible"
	 lyr.style.padding= "0px 0px 0px 0px"
	return lyr
}
