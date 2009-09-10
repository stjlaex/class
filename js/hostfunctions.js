//--------------------------------------------------------
//functions for the marktable to display columns from gradebox

// array for the gradechoice box - (mid, style.display, select)
var marks = new Array();

// called whenever the marktable is reloaded to check for changes
// and adjust the marks array accordingly
// state=0 means no change
// state=-1 means a change
// state=mid where mid is the value of the new mark column to display
function updateMarkDisplay(state){
	var theBook = window.frames["viewmarkbook"].document;
	var selMarks = document.getElementById('mids');
	if(!theBook.getElementById("sidtable")){
		//marks not displayed
		selMarks.style.display="none";
		return;
		}
	else if(state!=0) {
		//the mark selection box has changed need to 
		//keep the state of any of the previous marks the same
		for(var markno = 0; markno < marks.length; markno++){
			if(marks[markno][2]=="selected"){
				var mid = marks[markno][0];
				if(document.getElementById("sel-"+mid)){ 
					document.getElementById("sel-"+mid).selected = "selected";
					}
				}
			}
		if(document.getElementById("sel-"+state)){ 
			//if state is set the mid of a newly created mark then display it
			document.getElementById("sel-"+state).selected = "selected";
			}
		marks.length = 0; //blank old marks array first
		changeMarkDisplay();
		}
	else {
		//no change
		markDisplay();
		}
	}


// takes the display state from the selected values in the options 
// and stores in the marks array (calls markDisplay to apply them)
function changeMarkDisplay(){
	var i=0;
	var theBook = window.frames["viewmarkbook"].document;
	var selMarks = document.getElementById('mids');
	for(var c=0; c < selMarks.options.length; c++){
		if(selMarks.options[c].selected){
			marks[c] = Array(selMarks.options[c].value, "table-cell", "selected");
			}
		else{
			marks[c] = Array(selMarks.options[c].value, "none", "");
			}
		}
	markDisplay();
	}


// takes the selected state from those stored in the marks array
// and applies them to the marktable
function markDisplay(){
	var theBook = window.frames["viewmarkbook"].document;
	var selMarks = document.getElementById('mids');

	var theRows = theBook.getElementsByTagName("tr");
	var i = 0;
	var sids = new Array();
	for(var c=0; c < theRows.length; c++){
		if(theRows[c].attributes.getNamedItem("id")){
			var rowId=escape(theRows[c].attributes["id"].value);
			sids[i] = rowId.substring(4,rowId.length);
			//sids[i] = theRows[c].attributes.getNamedItem("id").value;
			i++;
			}
		}
	for(var markno=0; markno < marks.length; markno++){
		var mark = marks[markno];
		theBook.getElementById(mark[0]).style.display = mark[1];
		selMarks.options[markno].selected = mark[2];
		var i = 0;
		while((sid=sids[i++])){
			theBook.getElementById(sid+"-"+mark[0]).style.display = mark[1];
			}
		}
	}

//--------------------------------------------------------
//  the scripts for the userinterface - handles the selery menu in the bookoptions
//  and grows selery buttons

function seleryCheckKey(event,formObj){
	if(event.keyCode==13){
		formObj.submit();
		}
	}

function selerySubmit(liObj){
	liObj.getElementsByTagName("input")[0].setAttribute("checked","checked");
	//assumes the form to be the direct parent of the fieldset containing the selery ul
	liObj.parentNode.parentNode.parentNode.submit();
	}

function seleryGrow(buttonObj){
	var start=buttonObj.value;
	var end=++start;
	if(end>4){end=0;}
	buttonObj.value=end;
	buttonObj.parentNode.getElementsByTagName("input")[0].value=end;
	}

function selerySwitch(servantclass,fieldvalue){
	switchedId="switch"+servantclass;
	newfielddivId="switch"+servantclass+fieldvalue;
	if(document.getElementById(newfielddivId)){	
		//alert(switchedId,fieldvalue);
		document.getElementById(switchedId).innerHTML=document.getElementById(newfielddivId).innerHTML;
		}
	}

//--------------------------------------------------------
//  the scripts for the userinterface - handles the Book Tabs and bookframe

//  only called when index is loaded or the LogIn button is hit
//  displays the cover or login page respectively
function loadLogin(page){
	window.frames["viewlogbook"].location.href="logbook/exit.php";
	window.frames["viewlogbook"].location.href=page+".php";
	document.getElementById("viewlogbook").style.zIndex="100";
	document.getElementById("viewlogbook").focus();
	}

//  only called once after a new session has been started
//  flashscreen is the aboutbook followed after delay by markbook
function logInSuccess(){
	document.getElementById("navtabs").innerHTML=viewlogbook.document.getElementById("hiddennavtabs").innerHTML;
	document.getElementById("logbook").innerHTML=viewlogbook.document.getElementById("hiddenlogbook").innerHTML;
	document.getElementById("loginlabel").innerHTML=viewlogbook.document.getElementById("hiddenloginlabel").innerHTML;
	document.getElementById("viewlogbook").innerHTML="";
	document.getElementById("viewlogbook").style.zIndex="-100";
	document.getElementById("logbookoptions").innerHTML="";
	document.getElementById("logbookoptions").style.zIndex = "-100";
	viewBook("aboutbook");
	}

//  only called when the LogOut button is hit
function logOut(){
	if(window.frames["vieweportfolio"].document.getElementById("eportfoliosite")){
		var epflogout=window.frames["vieweportfolio"].document.getElementById("eportfoliosite").getAttribute("logout");
		window.frames["vieweportfolio"].frames["externalbook"].location.href=epflogout;
		}
	if(window.frames["viewwebmail"].document.getElementById("webmailsite")){
		var epflogout=window.frames["viewwebmail"].document.getElementById("webmailsite").getAttribute("logout");
		window.frames["viewwebmail"].frames["externalbook"].location.href=epflogout;
		}
	window.frames["viewlogbook"].location.href="logbook/exit.php";
	}

//	Reloads the book without giving focus (never used for logbook!)
//	always called by logbook if a session is set,
//	also called when changes in one book needs to update another
function loadBook(book){
	var currentbook="";	
	if(document.getElementById("currentbook")){
		currentbook=document.getElementById("currentbook").getAttribute("class");	
		}
	if(book==""){book=currentbook;}
	if(book!=""){window.frames["view"+book].location.href = book+".php";}
	if(book!=currentbook){
		document.getElementById("view"+book).style.zIndex = "-100";
		document.getElementById(book+"options").style.zIndex = "-100";
		}
	//window.frames["view"+book].history.forward();
	}

function loadBookOptions(book){
	document.getElementById(book+"options").innerHTML=window.frames["view"+book].document.getElementById("hiddenbookoptions").innerHTML;
	}

function viewBook(newbook){
	// hide the oldbook and tab first
	var oldbook=document.getElementById("currentbook").getAttribute("class");
	document.getElementById(oldbook+"options").style.zIndex = "-100";
	document.getElementById("view"+oldbook).style.zIndex = "-100";
	document.getElementById("currentbook").removeAttribute('id');
	// now bring the new tab and book to the top
	document.getElementById("view"+newbook).style.zIndex = "50";
	document.getElementById("view"+newbook).focus();
	document.getElementById(newbook+"options").style.zIndex = "60";
	document.getElementById(newbook+"tab").firstChild.setAttribute("id","currentbook");
	// change the colour of the logbook's stripe to match
	document.getElementById("logbookstripe").setAttribute("class",newbook);
	}

// A print function that handles pages designated as printable
function printGenericContent(iFrameName){
	var printWindow;
	var contentToPrint="";
	var currentbook=document.getElementById("currentbook").getAttribute("class");

	if(window.frames["view"+currentbook].document.getElementById("viewcontent")){
		if(window.frames["view"+currentbook].document.getElementById("heading")){
			contentToPrint=window.frames["view"+currentbook].document.getElementById("heading").innerHTML;
			}
		contentToPrint=contentToPrint + window.frames["view"+currentbook].document.getElementById("viewcontent").innerHTML;
		}
	else{
		contentToPrint="<h3>There is no printer friendly content on this page.</h3>";
		}
	printWindow=window.open("","","height=800,width=750,dependent,resizable,menubar,left=170,scrollbars");
	if(printWindow!=null){
		printWindow.document.write("<html><head><link rel='stylesheet' type='text/css' href='css/printstyle.css' /></head>");	
		printWindow.document.write("<body><br />"+contentToPrint+"</body></html>");
		printWindow.document.close();
		}
	}

// Keep the php session alive 
function sessionAlive(pathtobook){
	var url=pathtobook + "httpscripts/session_alive.php?uniqueid=1";
	var xmlHttp = false;
	requestxmlHttp();
	function requestxmlHttp(){
		try { xmlHttp=new XMLHttpRequest(); } 
		catch (failed) { xmlHttp=false; }
		if (!xmlHttp) {alert("Error initializing XMLHttpRequest!");}
		}
	xmlHttp.open("GET", url, true);
	xmlHttp.send(null);
	}
