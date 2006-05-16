//--------------------------------------------------------
//functions for the marktable to display columns from gradebox

// array for the gradechoice box - (mid, style.display, select)
var marks = new Array();

function updateMarkDisplay(state){
	// called whenever the marktable is reloaded to check for changes
	// and adjust the marks array accordingly
	var theBook = window.frames["viewmarkbook"].document;
	var selMarks = document.getElementById('mids');
	if(!theBook.getElementById("marktable")){
		//marks not displayed
		selMarks.style.display = "none";
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

function changeMarkDisplay(){
	// takes the display state from the selected values in the options 
	// and stores in the marks array (calls markDisplay to apply them)

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

function markDisplay(){
	// takes the selected state from those stored in the marks array
	// and applies them to the marktable

	var theBook = window.frames["viewmarkbook"].document;
	var selMarks = document.getElementById('mids');

	var theRows = theBook.getElementsByTagName("tr");
	var i = 0;
	var sids = new Array();
	for(var c=0; c < theRows.length; c++){
		if(theRows[c].attributes.getNamedItem("id")){
			sids[i] = theRows[c].attributes.getNamedItem("id").value;
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
//  the scripts for the userinterface - handles the Book Tabs and bookframe

function loadLogin(page){
//  only called when index is loaded or the LogIn button is hit
//  displays the cover or login page respectively
	window.frames["viewlogbook"].location.href="logbook/exit.php";
	window.frames["viewlogbook"].location.href=page+".php";
	document.getElementById("sidebuttons").style.zIndex="-100";
	document.getElementById("viewlogbook").style.zIndex="100";
	}

function logInSuccess(){
//  only called once after a new session has been started
//  flashscreen is the aboutbook followed after delay by markbook
	document.getElementById("navtabs").innerHTML=viewlogbook.document.getElementById("hiddennavtabs").innerHTML;
	document.getElementById("loginchoice").innerHTML=viewlogbook.document.getElementById("hiddenlogbook").innerHTML;
	document.getElementById("sidebuttons").innerHTML=viewlogbook.document.getElementById("hiddensidebuttons").innerHTML;
	document.getElementById("loginlabel").innerHTML=viewlogbook.document.getElementById("hiddenloginlabel").innerHTML;
	document.getElementById("langchoice").innerHTML="";
	document.getElementById("langchoice").style.zIndex="-100";
	document.getElementById("viewlogbook").innerHTML="";
	document.getElementById("viewlogbook").style.zIndex="-100";
	document.getElementById("logbookoptions").innerHTML="";
	document.getElementById("logbookoptions").style.zIndex = "-100";
	document.getElementById("sidebuttons").style.zIndex="10";
	viewBook("aboutbook");
	}

function logOut(){
//  only called when the LogOut button is hit
	window.frames["viewlogbook"].location.href="logbook/exit.php";
	}

function loadBook(book){
//	reloads this book without giving focus (never used for logbook!)
//	always called by logbook if a session is set
//	also called when changes in one book needs to update another
	var currentbook='';	
	if(document.getElementById("currentbook")){
		currentbook=document.getElementById("currentbook").getAttribute("class");	
		}
	window.frames["view"+book].location.href = book+".php";
	if(book!=currentbook){
		document.getElementById("view"+book).style.zIndex = "-100";
		document.getElementById(book+"options").style.zIndex = "-100";
		}
	}

function loadBookOptions(book){
	document.getElementById(book+"options").innerHTML=window.frames["view"+book].document.getElementById('hiddenbookoptions').innerHTML;
	}

function viewBook(newbook){
	// hide the oldbook and tab first
	var oldbook=document.getElementById("currentbook").getAttribute("class");	
	document.getElementById(oldbook+"options").style.zIndex = "-100";
	document.getElementById("view"+oldbook).style.zIndex = "-100";
	document.getElementById('currentbook').removeAttribute('id');
	// now bring the new tab and book to the top
	document.getElementById("view"+newbook).style.zIndex = "50";
	document.getElementById(newbook+"options").style.zIndex = "50";
	document.getElementById(newbook+"tab").firstChild.setAttribute('id','currentbook');
	// change the colour of the logbook's stripe to match
	document.getElementById('logbookstripe').setAttribute('class',newbook);
	}


//----------------------------------------------------
//for the Student Search in the sidebar of InfoBook

function validateQuickSearch(formObject) 
	{
 	var str = "";
 	for (var i=0; i < formObject.elements.length; i++) 
		{
		formObject.elements[i].style.borderStyle = "none"; 
		var thisId = formObject.elements[i].id;
		var thisType = formObject.elements[i].type;
		var thisValue = formObject.elements[i].value;
	  	var pattern = '[^A-Za-z ]+';
   		if (thisType == 'text') 
			{
     		var offendingChar = thisValue.match(pattern);
    		if(offendingChar != null) 
				{
				formObject.elements[i].style.border = "2px solid #ff9900"; 
       			str += "Found this illegal value '" +offendingChar+ "' in " +thisId+ " ! \n";
				}
  			}
 		}  
 	if (str != "") 
		{
   		// do not submit the form
   		alert("Check Entry!\n" +str);
		return false;  
 		} 
	else 
		{
   		// form values are valid; submit
		return true;
		}
	}


// A print function that handles pages designated as printable
function printGenericContent(iFrameName){
	var printWindow;
	var contentToPrint="";
	var currentbook=document.getElementById("currentbook").getAttribute("class");	

	if(window.frames["view"+currentbook].document.getElementById("viewcontent")){
		contentToPrint=window.frames["view"+currentbook].document.getElementById("viewcontent").innerHTML;
		}
	else{
		contentToPrint="<h3>There is no printer friendly content on this page.</h3>";
		}
	printWindow=window.open("","","height=800,width=750,dependent,resizable,menubar,left=170,scrollbars");
	if(printWindow!=null){
		printWindow.document.write("<html><head><link rel='stylesheet' type='text/css' href='stylesheets/printstyle.css' /></head>");	
		printWindow.document.write("<body><br />"+contentToPrint+"</body></html>");
		printWindow.document.close();
		}
	}


// A print function that handles pages designated as printable
function helpPage(){
	var helpWindow;
	var helpContent;
	var currentbook=document.getElementById("currentbook").getAttribute("class");	
	if(window.frames["view"+currentbook].document.getElementById("helpcontent")){
		helpContent=window.frames["view"+currentbook].document.getElementById("helpcontent").innerHTML;
		}
	else{
		helpContent="<h3>Sorry, there is not yet any help instructions for this page.</h3>";
		}
	helpWindow=window.open("","","height=400,width=750,dependent,resizable,menubar,left=170,scrollbars");
	if(helpWindow!=null){
		helpWindow.document.write("<html><head><link rel='stylesheet' type='text/css' href='stylesheets/printstyle.css' /></head><body>");	
		helpWindow.document.write(helpContent);
		helpWindow.document.writeln("<form><input type='button' value='Close This Window' onClick='window.close();'></form>");
		helpWindow.document.write("</body></html>");
		helpWindow.document.close();
		}
	}
