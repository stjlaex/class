//////////////////////////////////////////////////////////////////
// qTip - CSS Tool Tips - by Craig Erskine
// http://qrayg.com | http://solardreamstudios.com
//
// Inspired by code from Travis Beckham
// http://www.squidfingers.com | http://www.podlob.com
//
// Removed the IE specific kludges, and 
// moved the init to loadRequired() for ClaSS - by Stuart T Johnson
//////////////////////////////////////////////////////////////////

//var qTipTag = "span";
//var qTipX = -30;
//var qTipY = 25;

tooltip = {
  	name : "qTip",
  	offsetX : 15,
  	offsetY : 0,
  	tip : null
	}

tooltip.init = function(){
	var tipNameSpaceURI = "http://www.w3.org/1999/xhtml";
	if(!tipContainerID){ var tipContainerID = "qTip";}
	var tipContainer = document.getElementById(tipContainerID);
	if(!tipContainer){
	  	tipContainer = document.createElementNS ? document.createElementNS(tipNameSpaceURI, "div") : document.createElement("div");
		tipContainer.setAttribute("id", tipContainerID);
	  	document.getElementsByTagName("body").item(0).appendChild(tipContainer);
		}
	if(!document.getElementById){return;}
	this.tip = document.getElementById(this.name);
	if(this.tip){document.onmousemove=function(evt){tooltip.move(evt)}};

	var a, sTitle;
	var anchors=document.getElementsByTagName("span");
	for(var i=0; i<anchors.length; i++){
		a=anchors[i];
		sTitle=a.getAttribute("title");
		if(sTitle!="" && sTitle!=null){
			a.setAttribute("tiptitle", sTitle);
			a.removeAttribute("title");
			a.onmouseover=function(){tooltip.show(this.getAttribute("tiptitle"))};
			a.onmouseout=function(){tooltip.hide()};
			}
		}
	}

tooltip.move = function(evt){
	var x=0;
	var y=0;
	x=evt.pageX;
	y=evt.pageY;
	this.tip.style.left = (x + this.offsetX) + "px";
	this.tip.style.top = (y + this.offsetY) + "px";
	}

tooltip.show = function(text){
	if(!this.tip){return;}
	this.tip.innerHTML = text;
	this.tip.style.display = "block";
	}

tooltip.hide = function(){
	if(!this.tip){return;}
	this.tip.innerHTML = "";
	this.tip.style.display = "none";
	}
