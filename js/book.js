var xmlHttp = false;
requestxmlHttp();

function requestxmlHttp(){
	try { xmlHttp=new XMLHttpRequest(); } 
	catch (failed) { xmlHttp=false; }
	if (!xmlHttp) {alert("Error initializing XMLHttpRequest!");}
	}

//------------------------------------------------------
// A bunch of functions which launch a helper window before working with
// calls to httpscripts to do their work

//opens the category editor window
function clickToConfigureCategories(type,rid,bid,pid,stage,openId){
	var helperurl="reportbook/httpscripts/category_editor.php";
	var getvars="type="+type+"&rid="+rid+"&bid="+bid+"&pid="+pid+"&stage="+stage+"&openid="+openId;
	openHelperWindow(helperurl,getvars);
	}

//opens the comment writer window
function clickToWriteComment(sid,rid,bid,pid,entryn,openId){
	var helperurl="reportbook/httpscripts/comment_writer.php";
	var getvars="sid="+sid+"&rid="+rid+"&bid="+bid+"&pid="+pid+"&entryn="+entryn+"&openid="+openId;
	openHelperWindow(helperurl,getvars);
	}
//opens the comment writer window
function clickToWriteCommentNew(sid,rid,bid,pid,entryn,openId){
	var helperurl="reportbook/httpscripts/newcomment_writer.php";
	var getvars="sid="+sid+"&rid="+rid+"&bid="+bid+"&pid="+pid+"&entryn="+entryn+"&openid="+openId;
	openHelperWindow(helperurl,getvars);
	}

//opens the a window for file attachments
function clickToAttachFile(sid,mid,cid,pid,openId){
	var helperurl="markbook/httpscripts/upload_file.php";
	var getvars="sid="+sid+"&mid="+mid+"&cid="+cid+"&pid="+pid+"&openid="+openId;
	openHelperWindow(helperurl,getvars);
	}

//opens the merit window
function clickToAddMerit(bid,pid,openId){
	var sidId=currentsidrow;
	var helperurl="infobook/httpscripts/merit_adder.php";
	var getvars="sid="+sidId+"&bid="+bid+"&pid="+pid+"&openid="+openId+'-'+sidId;
	openHelperWindow(helperurl,getvars);
	}

/* Opens the transport edit window */
function clickToEditTransport(sid,date,bookingid,openId){
	var helperurl="admin/httpscripts/transport_editor.php";
	var getvars="sid="+sid+"&date="+date+"&bookingid="+bookingid+"&openid="+openId;
	openHelperWindow(helperurl,getvars);
	}

/* Opens the attendance booking edit window */
function clickToEditAttendance(sid,date,bookingid,openId){
	var helperurl="register/httpscripts/attendance_editor.php";
	var getvars="sid="+sid+"&date="+date+"&bookingid="+bookingid+"&openid="+openId;
	openHelperWindow(helperurl,getvars);
	}

/**/
function closeAttendanceHelper(sid,date,openId){
	if(openId!="-100"){
		var container='sid-'+sid;
		var script='attendance_display.php';
		var url="register/httpscripts/" + script + "?uniqueid=" + escape(openId) +"&sid=" + sid + "&date=" + date;
		opener.updateDisplay(container,url);
		}
	window.close();
	}

function closeTransportHelper(sid,date,openId){
	if(openId!="-100"){
		var container='sid-'+sid;
		var script='transport_display.php';
		var url="admin/httpscripts/" + script + "?uniqueid=" + escape(openId) +"&sid=" + sid + "&date=" + date;
		opener.updateDisplay(container,url);
		}
	window.close();
	}

function updateDisplay(container,url){

	if(document.getElementById(container)){
		xmlHttp.open("GET", url, true);
		xmlHttp.onreadystatechange=function () {
			if(xmlHttp.readyState==4){
				if(xmlHttp.status==200){
					var html=xmlHttp.responseText;
					document.getElementById(container).innerHTML="";
					document.getElementById(container).innerHTML=html;
					}
				else if(xmlHttp.status==404){alert ("Requested URL is not found.");}
        		else if(xmlHttp.status==403){alert("Access denied.");} 
				else {alert("status is " + xmlHttp.status);}
				progressIndicator("stop");
				}
			else{
				progressIndicator("start");
				}
			}
		xmlHttp.send(null);
		}

	}
/*****/


function openHelperWindow(helperurl,getvars){
	writerWindow=window.open("","","height=680,width=720,screenX=50,dependent");
	writerWindow.document.open();
	writerWindow.document.writeln("<html>");
	writerWindow.document.writeln("<head>");
	writerWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	writerWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	writerWindow.document.writeln("</head>");
	writerWindow.document.writeln("<script type=\"text/javascript\">function actionpage(){document.location='"+helperurl+"?"+getvars+"';}</script>");
	writerWindow.document.writeln("<body onLoad=\"setTimeout('actionpage()', 100);\">");
	writerWindow.document.writeln("</body>");
	writerWindow.document.writeln("</html>");
	writerWindow.document.close();
	}

/* For text editor only */
function closeHelperWindow(openId,entryn,text){
	if(openId!="-100"){
		opener.updateLauncher(openId,entryn,text);
		}
	window.close();
	}

function updateLauncher(openId,entryn,text){
//	the id should refer to the containing html entity for the icon (probably a td)
//  and the actual textarea for the text
	if(document.getElementById("text"+openId)){
		document.getElementById("text"+openId).value=text;
		}
	if(document.getElementById("icon"+openId)){
		document.getElementById("icon"+openId).setAttribute("class","vspecial");
		}
	if(document.getElementById("inmust"+openId)){
		document.getElementById("inmust"+openId).value=entryn;
		}
	}



//------------------------------------------------------
//used within a listmenu table
function clickToReveal(rowObject){
	var rowId=rowObject.parentNode.id;
	var theRow;
	var i=0;
	while(theRow=document.getElementById(rowId+"-"+i)){
		if(theRow.className=="rowplus"){ 
			theRow.className="rowminus"; 
			}		
		else if(theRow.className=="rowminus"){ 
			theRow.className="rowplus";
			}
		else if(theRow.className=="revealed"){ 
			theRow.className="hidden";
			}
		else if(theRow.className=="hidden"){ 
			theRow.className="revealed";
			}	
		i++;
		}	
	}


/**
 * Only for rowaction or sideoption buttons NOT for buttonmenu form buttons.
 * The type of action is specified as the button's name attribute and
 * possible action values are Edit, New, process, print, chart and current.
 * With current it will always ask for confirmation before making a xmlhttprequest 
 * and it applies returned xml to update the current page wihtout a reload.
 * The print and chart actions are for pop-up report windows and don't effect any changes.
 */
function clickToAction(buttonObject){
	var i=0;
	//need the id of the div containing the xml-record 
	var theDivId=buttonObject.parentNode.id;
	if(theDivId==""){
		//gets it from the id of the tbody container for this row
		var theContainerId=buttonObject.parentNode.parentNode.parentNode.id;
		}
	else{
		//or gets it from the id of the parent div container
		var theContainerId=theDivId;
		}
	var xmlId="xml-"+theContainerId;
	var xmlContainer=document.getElementById(xmlId);
	var xmlRecord=xmlContainer.childNodes[1];
	var action=buttonObject.name;
	if(action=="Edit"){
		var test=fillxmlForm(xmlRecord);
		document.getElementById("Subject").parentNode.setAttribute("class","right");
		if(document.getElementById("No_db")){document.getElementById("No_db").value="";}
		if(document.getElementById("formstatus-new")){document.getElementById("formstatus-new").setAttribute("class","hidden");}
		if(document.getElementById("formstatus-edit")){document.getElementById("formstatus-edit").setAttribute("class","");}
		if(document.getElementById("formstatus-action")){document.getElementById("formstatus-action").setAttribute("class","hidden");}
		}
	else if(action=="New"){
		document.formtoprocess.reset();
		var recordId=xmlRecord.getElementsByTagName("id_db").item(0).firstChild.data;
		document.getElementById("Id_db").value=recordId;
		document.getElementById("No_db").value="-1";
		document.getElementById("Subject").parentNode.setAttribute("class","hidden");
		document.getElementById("formstatus-new").setAttribute("class","hidden");
		document.getElementById("formstatus-edit").setAttribute("class","hidden");
		document.getElementById("formstatus-action").setAttribute("class","");
		}
	else if(action=="process"){
		if(buttonObject.value=="cancel" || buttonObject.value=="delete"){
			var answer=confirmAction(buttonObject.title);
			}
		else{
			var answer=true;
			}
		if(answer){
			var recordId=xmlRecord.childNodes[0].childNodes[0].nodeValue;
			var formObject=document.formtoprocess;
			var formElements=formObject.elements;
			var input1=document.createElement("input");
			input1.type="hidden";
			input1.name="recordid";
			input1.value=recordId;
			document.formtoprocess.appendChild(input1);
			var input2=document.createElement("input");
			input2.type="hidden";
			input2.name="sub";
			input2.value=buttonObject.value;
			document.formtoprocess.appendChild(input2);
			document.formtoprocess.submit();
			}
		}
	else if(action=="current" || action=="print" || action=="chart"){
		var recordId=xmlRecord.childNodes[0].childNodes[0].nodeValue;
		var script=buttonObject.value;
		var url=pathtobook + "httpscripts/" + script + "?uniqueid=" + escape(recordId);
		if(action!="print" && action!="chart"){
			var answer=confirmAction(buttonObject.title);
			var params="";
			}
		else{
			var answer=true;
			var params="";
			if(parent.document.getElementById("Chart-template")){
				var selectObj=parent.document.getElementById("Chart-template");
				for(var i=0; i < selectObj.options.length; i++){
					if(selectObj.options[i].selected){
						params="&template=" + escape(selectObj.options[i].value);
						}
					}
				}
			/* This is for passing a list of sids grabbed from the tr ids of a sidtable
			 * used for example by report_profile_print
			 */
			if(document.getElementById("sidtable")){
				var sidrows=document.getElementById("sidtable").getElementsByTagName("tr");
				for(var c=0; c<sidrows.length; c++){
					if(sidrows[c].id!="" && sidrows[c].id!="sid-0"){
						var rowId=escape(sidrows[c].attributes["id"].value);
						var sidId=rowId.substring(4,rowId.length);//strip off "sid-" part
						params=params+"&sids[]=" + sidId;
						}
					}
				}
			/*
			 * Reads through some xml adding as params with name = tagname
			 */
			for(var i=0; i < xmlRecord.childNodes.length; i++){
				var xmlfieldid=xmlRecord.childNodes[i];
				if(xmlfieldid.tagName){
					var paramname=makeParam(xmlfieldid.tagName);
					if(xmlfieldid.firstChild){var xmlvalue=xmlfieldid.firstChild.data;}
					else{var xmlvalue="";}
					params=params + "&" + paramname + "=" + escape(xmlvalue);
					}
				}
			url=url + params;
			}
		if(answer){
			xmlHttp.open("GET", url, true);
			xmlHttp.onreadystatechange=function () {
					if(xmlHttp.readyState==4){
						if(xmlHttp.status==200){
							xmlRecord=xmlHttp.responseXML;
							if(action=="current"){
								//function to actually process the returned xml
								updatexmlRecord(xmlRecord);
								}
							else if(action=="print" || action=="chart"){
								xsltransform=xmlRecord.getElementsByTagName("transform")[0].firstChild.nodeValue;
								paper=xmlRecord.getElementsByTagName("paper")[0].firstChild.nodeValue;
								if(xsltransform!=""){
									var xmlResult=processXML(xmlRecord,xsltransform,"../templates/");
									if(action=="print"){
										openPrintReport(xmlResult,xsltransform,paper);
										}
									else if(action=="chart"){
										openChartReport(xmlResult,xsltransform,paper);
										}
									}
								}
							}
						else if(xmlHttp.status==404){alert ("Requested URL is not found.");}
        				else if(xmlHttp.status==403){alert("Access denied.");} 
						else {
							//alert("status is " + xmlHttp.status);
							}
						progressIndicator("stop");
						}
					else{
						progressIndicator("start");
						}
				}
			xmlHttp.send(null);
			}
		}
	}


/* Pop-up report window for one student in a sidtable.
* Currently fixed to http scripts in the MarkBook
*/
function clickToPresentSid(script,xsltransform){
	var sidId=currentsidrow;
	var helperurl="markbook/httpscripts/" + script;
	var getvars="&sid="+sidId;
	var url=helperurl + "?uniqueid=" + sidId + getvars;
	var paper="portrait";
	xmlHttp.open("GET", url, true);
	xmlHttp.onreadystatechange=function () {
		if(xmlHttp.readyState==4){
			if(xmlHttp.status==200){
				xmlRecord=xmlHttp.responseXML;
				var xmlResult=processXML(xmlRecord,xsltransform,"../templates/");
				openPrintReport(xmlResult,xsltransform,paper);
				}
			else if(xmlHttp.status==404){alert ("Requested URL is not found.");}
			else if(xmlHttp.status==403){alert("Access denied.");}
			else {alert("status is " + xmlHttp.status);}
			progressIndicator("stop");
			}
		else{
			progressIndicator("start");
			}
		}
	xmlHttp.send(null);
	}

/* 
* More general pop-up report window.
*/
function clickToPresent(book,script,xsltransform){
	var url=book + "/httpscripts/" + script;
	var paper="portrait";
	xmlHttp.open("GET", url, true);
	xmlHttp.onreadystatechange=function () {
		if(xmlHttp.readyState==4){
			if(xmlHttp.status==200){
				xmlRecord=xmlHttp.responseXML;
				var xmlResult=processXML(xmlRecord,xsltransform,"../templates/");
				openPrintReport(xmlResult,xsltransform,paper);
				}
			else if(xmlHttp.status==404){alert ("Requested URL is not found.");}
			else if(xmlHttp.status==403){alert("Access denied.");}
			else {alert("status is " + xmlHttp.status);}
			progressIndicator("stop");
			}
		else{
			progressIndicator("start");
			}
		}
	xmlHttp.send(null);
	}


function confirmAction(title){
	var message="You have requested the following action:\n\n";
	message=message + title + "\n\n";
	message=message + "Are you sure you want to continue?";
	var answer=window.confirm(message);
	return answer;
	}


function updatexmlRecord(xmlRecord){
	var exists=false;
	if(xmlRecord!=""){
		var recordId=xmlRecord.getElementsByTagName("id_db").item(0).firstChild.data;
		var exists=xmlRecord.getElementsByTagName("exists").item(0).firstChild.data;
//		var xmlId="xml-"+recordId;
//		var xmlContainer=document.getElementById(xmlId);
//	    xmlContainer.firstChild.data=xmlRecord;
		if(exists!="true"){
			var tableRecord=document.getElementById(recordId);
			while(tableRecord.hasChildNodes()){
				tableRecord.removeChild(tableRecord.childNodes[0]);
				}
			}
		else{
			fillxmlTable(recordId, xmlRecord);
			}
		}
	}

//------------------------------------------------------- 
// Hides all the rows in a sidtable which don't have a particular 
// input radio box checked.

function sidtableFilter(buttonObject){
	var formObject=document.formtoprocess;
	var formElements=formObject.elements;
	var buttonname=buttonObject.name;
	var filtername=buttonObject.value;
	var selectObj=document.getElementById("Filtervalue");
	var filtervalue='';
	for(var i=0;i<selectObj.options.length;i++){
		if(selectObj.options[i].selected){
			filtervalue=escape(selectObj.options[i].value);
			}
		}
	if(filtervalue!=''){
		var row=0;
		for(var c=0; c<formObject.elements.length; c++){
			var inputObj=formObject.elements[c];
			if(inputObj.type=="radio" && inputObj.name.substr(0,filtername.length)==filtername 
							&& inputObj.value==filtervalue){
				var rowId='sid-'+inputObj.name.substr(filtername.length);
				if(inputObj.checked){
					filterrowIndicator(rowId,"")
					}
				else{
					filterrowIndicator(rowId,"hidden")
					}
				
				}
			}
		}
	else{
		var sidrows=document.getElementById("sidtable").getElementsByTagName("tr");
		for(var c=0; c<sidrows.length; c++){
			if(sidrows[c].id!="" && sidrows[c].id!="sid-0"){
				var rowId=sidrows[c].attributes["id"].value;
				filterrowIndicator(rowId,"")
				}
			}
		}
	}

//------------------------------------------------------- 
// Highlights the checked radio input and unhighlights any others with 
// the same name


function checkRadioIndicator(parentObj){
	var inputname=parentObj.childNodes[1].name;
	var inputval=parentObj.childNodes[1].value;
	var radioObjs=document.getElementsByName(inputname);
	if(radioObjs.length==4){
		if(inputval=="-1"){var fieldclass="hilite";}
		else if(inputval=="0"){var fieldclass="pauselite";}
		else if(inputval=="1"){var fieldclass="golite";}
		}
	else if(inputval=="uncheck"){var fieldclass="";}
	else {var fieldclass="checked";}
	for(var c=0;c<radioObjs.length;c++){
		//if(radioObjs[c].value==inputval && inputval!="uncheck"){
		if(radioObjs[c].value==inputval){
			radioObjs[c].parentNode.setAttribute("class",fieldclass);
			radioObjs[c].checked=true;
			}
		else{
			radioObjs[c].parentNode.setAttribute("class","notchecked");
			radioObjs[c].checked=false;
			}
		}
	}


/**
 *
 * Only called by form buttons in place of processContent() 
 * this will pass all the checked boxes along-with selected form variables.
 * The names of checkname, selectname and transform are passed as parameters 
 * listed in an embedded xml div with id="xml-checked-action". 
 * The names of the checkboxes defaults to sids but can be set by checkname.
 * 
 * Whatever xml is returned by the httpscript called by the button
 * is transformed using the xsl transformation named in transform. 
 *
 */
function checksidsAction(buttonObject){
	var formObject=document.formtoprocess;
	var formElements=formObject.elements;
	var action=buttonObject.name;
	var script=buttonObject.value;
	var params="";
	var xsltransform="";
	var checkname1="sids[]";
	var checkname2="sids[]";
	var selectnames=new Array();
	var selno=0;

	// Need the path for the script being called - this is set 
	// by default to the path of the current book but can be overridden
	// if the buttonObject has this attribute set.
	var pathtoscript=pathtobook;
	if(buttonObject.getAttribute("pathtoscript")){
		pathtoscript=buttonObject.getAttribute("pathtoscript");
		}
	// Need the id of the div containing the params to work with.
	// This defaults to checked-action but can be overridden.
	var theContainerId="checked-action";
	if(buttonObject.getAttribute("xmlcontainerid")){
		theContainerId=buttonObject.getAttribute("xmlcontainerid");
		}

	if(theContainerId!="" && document.getElementById("xml-"+theContainerId)){
		var xmlId="xml-"+theContainerId;
		var xmlContainer=document.getElementById(xmlId);
		var xmlRecord=xmlContainer.childNodes[1];

        for(var i=0; i < xmlRecord.childNodes.length; i++){
			var xmlfieldid=xmlRecord.childNodes[i];
			if(xmlfieldid.tagName){
				var paramname=makeParam(xmlfieldid.tagName);
				if(xmlfieldid.firstChild){var xmlvalue=xmlfieldid.firstChild.data;}
				else{var xmlvalue="";}
				if(paramname=="transform"){
					//the transform is used by the js and not passed as a param
					var xsltransform=escape(xmlvalue);
					if(action=="chart"){var paper="landscape";}
					else{var paper="portrait";}
					}
				else if(paramname=="paper"){
					//the transform is used by the js and not passed as a param
					var paper=escape(xmlvalue);
					}
				else if(paramname=="selectname"){
					//used by the js and not passed as a param
					selectnames[selno++]=escape(xmlvalue);
					}
				else if(paramname=="checkname" && checkname1=="sids[]"){
					//used by the js and not passed as a param
					checkname1=escape(xmlvalue)+"[]";
					}
				else if(paramname=="checkname" && checkname1!="sids[]"){
					//used by the js and not passed as a param
					checkname2=escape(xmlvalue)+"[]";
					}
				else{
					params=params + "&" + paramname + "=" + escape(xmlvalue);
					}
		    	}
			}

		}


	/* Now grab all the checked input boxes with name=checkname plus
	 * any form elements identified with name=selectname
	*/
	var sids=new Array();
	var sidno=0;
	for(var c=0; c<formObject.elements.length; c++){
		if(formObject.elements[c].name=="checkall"){
			c=c+1;
			}
		if(formObject.elements[c].type=="checkbox" && (formObject.elements[c].name==checkname1 || formObject.elements[c].name==checkname2)){
			if(formObject.elements[c].checked){
				sids[sidno++]=formObject.elements[c].value;
				params=params+"&sids[]=" + escape(formObject.elements[c].value);
				//and uncheck them for (maybe) convenience
				formObject.elements[c].checked=false;
				}
			}
		else {
			for(var sc=0; sc<selno; sc++){
				if(formObject.elements[c].name==selectnames[sc]){
					if(formObject.elements[c].type=="select-one"){
						var selectObj=formObject.elements[c];
						for(var i=0; i < selectObj.options.length; i++){
							if(selectObj.options[i].selected){
								params=params + "&" + selectnames[sc] + "=" + escape(selectObj.options[i].value);
								}
							}
						}
					else if(formObject.elements[c].type=="select-multiple"){
						var selectObj=formObject.elements[c];
						for(var i=0; i < selectObj.options.length; i++){
							if(selectObj.options[i].selected){
								params=params + "&" + selectnames[sc] + "[]=" + escape(selectObj.options[i].value);
								}
							}
						}
					else if(formObject.elements[c].type=="radio"){
						if(formObject.elements[c].checked==true){
							var selectObj=formObject.elements[c];
							params=params + "&" + selectnames[sc] + "=" + escape(selectObj.value);
							}
						}
					else{
						var selectObj=formObject.elements[c];
						params=params + "&" + selectnames[sc] + "=" + escape(selectObj.value);
						}
					}
				}
			}
		}

	var url=pathtoscript + "httpscripts/" + script + "?" +params;
	xmlHttp.open("GET", url, true);
	xmlHttp.onreadystatechange=function () {
			if(xmlHttp.readyState==4){
				if(xmlHttp.status==200){
					var xmlReport=xmlHttp.responseXML;
					if(xsltransform==""){
						/* only if its been set in some non-standard way (should be one of the params!) */
						xsltransform=xmlReport.getElementsByTagName("transform")[0].firstChild.nodeValue;
						paper=xmlReport.getElementsByTagName("paper")[0].firstChild.nodeValue;
						}
					//function to actually process the returned xml	
					if(xsltransform!=""){
						var xmlResult=processXML(xmlReport,xsltransform,"../templates/");
						if(action=="chart"){
							openChartReport(xmlResult,xsltransform,paper);
							}
						else{
							openPrintReport(xmlResult,xsltransform,paper);
							}
						}
					}
				else if(xmlHttp.status==404){alert ("Requested URL is not found.");}
        		else if(xmlHttp.status==403){alert("Access denied.");} 
				else {alert("status is " + xmlHttp.status);}
				progressIndicator("stop");
				}
			else{
				progressIndicator("start");
				}
			}
	xmlHttp.send(null);
	}


/* Fetches the named xsl transformation sheet and uses it to process the xmlsource.*/
function processXML(xmlsource, xsltName, xsltPath){ 
	var xslsheet;
	var xProcessor=new XSLTProcessor();
  	var myXMLHTTPRequest=new XMLHttpRequest();
  	myXMLHTTPRequest.open("GET", xsltPath+xsltName+".xsl", false);
  	myXMLHTTPRequest.send(null);
	xslsheet=myXMLHTTPRequest.responseXML;
  	xProcessor.importStylesheet(xslsheet);
	var xmlResult=xProcessor.transformToDocument(xmlsource);
	//alert(serializeXML(xmlResult));
	return xmlResult;
	}


function progressIndicator(action){
	//var statusObject=parent.document.getElementById("sitestatus");
	if(action=="start"){
		parent.document.getElementById("sitestatus").setAttribute("class","show");
		parent.document.getElementById("siteicon").setAttribute("class","hide");
		}
	else{
		parent.document.getElementById("sitestatus").setAttribute("class","hide");
		parent.document.getElementById("siteicon").setAttribute("class","show");
		}
	}

//-------------------------------------------------------
// uses the id to refer to a <value> and replace its content

function fillxmlTable(recordId, xmlRecord){
    if(xmlRecord.hasChildNodes()){
        for(var i=0; i < xmlRecord.childNodes.length; i++){
			fillxmlTable(recordId, xmlRecord.childNodes[i]);
		    }
		}
    else{
		var xmltag=xmlRecord.parentNode.tagName;
		if(xmltag=="value"){
			var xmltag=xmlRecord.parentNode.parentNode.tagName;
	        var xmlvalue=xmlRecord.nodeValue;
			xmltag=makeLabel(xmltag);
			fieldId=recordId+"-"+xmltag;
			if(document.getElementById(fieldId) && document.getElementById(fieldId).firstChild){
				document.getElementById(fieldId).firstChild.data=xmlvalue;
				}
			}
		}
	}


//-------------------------------------------------------
// Uses the html field id to refer to an input field and replace its value
// does this for the xml value contained by VALUE or VALUE_ID where the display
// VALUE is different from the stored database value
// ID_DB is the special, hidden form field, which must be the unique identifier
// for the record in the database record

function fillxmlForm(xmlRecord){
	var test='';
    if(xmlRecord.hasChildNodes()){
        for(var i=0; i < xmlRecord.childNodes.length; i++){
            test=test + "i=" + i + fillxmlForm(xmlRecord.childNodes[i]);
		    }
		}
    else{
		var xmltag=xmlRecord.parentNode.tagName;
		if(xmltag=='VALUE' || xmltag=='VALUE_DB'){
			var xmltag=xmlRecord.parentNode.parentNode.tagName;
	        var xmlvalue=xmlRecord.nodeValue;
			fieldId=makeLabel(xmltag);
			//test=xmltag + ' : ' + xmlvalue;
			if(document.getElementById(fieldId)){
				document.getElementById(fieldId).value=xmlvalue;
				}
			}
		else if(xmltag=='ID_DB'){
	        var xmlvalue=xmlRecord.nodeValue;
			fieldId=makeLabel(xmltag);
			//test=xmltag + ' : ' + xmlvalue;
			if(document.getElementById(fieldId)){
				document.getElementById(fieldId).value=xmlvalue;
				}
			}
		}
	return test;
	}

function makeLabel(xmltag){
	// the id of the form element is expected to be first-letter capitalised only
	// ie. does not follow the xml capitalisation!
	var lower=xmltag.toLowerCase();
	var upper=xmltag.toUpperCase();
	var label=upper.substring(0,1) + lower.substring(1,lower.length);
	return label;
	}

function makeParam(xmltag){
	// the id of the form element is expected to be first-letter capitalised only
	// ie. does not follow the xml capitalisation!
	var lower=xmltag.toLowerCase();
	//var upper=xmltag.toUpperCase();
	var plurality=lower.substring(lower.length-1,lower.length);
	if(plurality=='s'){
		param=lower + "[]";
		}
	else{
		param=lower;
		}
	return param;
	}

//------------------------------------------------------
//used by the buttonmenu to submit or reset the content form

function processContent(buttonObject){
	var formObject=document.formtoprocess;
	var formElements=formObject.elements;
	var buttonname=buttonObject.name;
	if(buttonObject.value=="Reset"){
		document.formtoprocess.reset();
		if(document.getElementById("No_db")){document.getElementById("No_db").value="";}
		if(document.getElementById("formstatus-new")){document.getElementById("formstatus-new").setAttribute("class","");}
		if(document.getElementById("formstatus-edit")){document.getElementById("formstatus-edit").setAttribute("class","hidden");}
		if(document.getElementById("formstatus-action")){document.getElementById("formstatus-action").setAttribute("class","hidden");}
		}
	else if(buttonObject.value=="Cancel"){
		var input=document.createElement("input");
		input.type="hidden";
		input.name=buttonObject.name;
		input.value=buttonObject.value;
		document.formtoprocess.appendChild(input);
		document.formtoprocess.submit();
		}
	else{
		var done=0;
		for(c=0; c<formElements.length; c++){
			if(buttonname==formElements[c].name){
				document.formtoprocess.elements[c].value=buttonObject.value;
				var done=1;
				}
			}
		if(done!=1){
			var input=document.createElement('input');
			input.type="hidden";
			input.name=buttonObject.name;
			input.value=buttonObject.value;
			document.formtoprocess.appendChild(input);
			}
		if(buttonObject.value!="Submit" && buttonObject.value!="Enter"){
			document.formtoprocess.submit();
			}
		else if(validateForm()){
			document.formtoprocess.submit();
			}
		}
	}


function processHeader(buttonObject){
	var formObject=document.headertoprocess;
	var formElements=formObject.elements;
	var buttonname=buttonObject.name;
	document.headertoprocess.submit();
	}


/*------------------------------------------------------- 
* Ticks all checkboxes in a form.  
* Only ticks if the are not hidden.  
* Optional parameter to limit to checkboxes of the same name.
*/
function checkAll(checkAllBox,checkname){
	var formObject=checkAllBox.form;
	if(!checkname) {var checkname='';}
	for(var c=0; c<formObject.elements.length; c++){
		if(formObject.elements[c].name=="checkall"){
			c=c+1;
			}
		if(formObject.elements[c].type=="checkbox" && getrowIndicator(formObject.elements[c])!="hidden" && (checkname=="" || formObject.elements[c].name==checkname)){
			if(checkAllBox.checked){
				formObject.elements[c].checked=true;
				}
			else{
				formObject.elements[c].checked=false;
				}
			checkrowIndicator(formObject.elements[c]);
			}
		}
	}

/* Changes the class of the row when checked and unchecked. */
function checkrowIndicator(inputObj){
	var rowId="sid-"+inputObj.value;
	var theRow;
	if(document.getElementById(rowId)){
		theRow=document.getElementById(rowId);
		if(inputObj.checked){
			theRow.setAttribute("class","lowlite");
			}
		else{
			theRow.setAttribute("class","");
			}
		}
	}

/* Returns the class of the row which could be lowlite, hidden or null. */
function getrowIndicator(inputObj){
	var rowId="sid-"+inputObj.value;
	var theRow;
	var theRowClass;
	if(document.getElementById(rowId)){
		theRow=document.getElementById(rowId);
		theRowClass=theRow.getAttribute("class");
		}
	else{
		theRowClass=false;
		}
	return theRowClass;
	}

/*changes the class of the row when filtered by sidtableFilter*/
function filterrowIndicator(rowId,state){
	var theRow;
	if(document.getElementById(rowId)){
		theRow=document.getElementById(rowId);
			theRow.setAttribute("class",state);
		}
	}



//-------------------------------------------------------
// regular expressions for input validation

function getPattern(patternName){
	if(patternName=='integer'){ var pattern = '[^0-9]+';}
	if(patternName=='numeric'){ var pattern = '[^.0-9]+';}
	if(patternName=='decimal'){ var pattern = '[^.0-9]+';}
	// TODO: How to make these utf8 friendly?
	if(patternName=='alphanumeric'){ var pattern = '[^-.?,!;()+/\':A-Za-z0-9_ ]+';}
	if(patternName=='truealphanumeric'){ var pattern = '[^A-Za-z0-9]+';}
	if(patternName=='email'){ var pattern = '[-.A-Za-z0-9_]+@[-.A-Za-z0-9_]+\.[-.A-Za-z]{2,4}';}
	return pattern;
	}

	
//-------------------------------------------------------
// does validation for all input fields when a form is submitted

function validateForm(formObj){
	if(!formObj){var formObj=document.formtoprocess;}
 	var errorMessage="";
 	for(var i=0; i<formObj.elements.length; i++){
		var fieldClass=formObj.elements[i].className;
		if(fieldClass.indexOf("eitheror")!=-1){
			message=validateRequiredOr(formObj.elements[i]);
			}
		else{
			message=validateResult(formObj.elements[i]);
			}
		if(message){errorMessage=errorMessage+" \n"+message;};
 		}
 	if(errorMessage!=""){
   		parent.alert("Check your entries! \n" + errorMessage);
		return false;
 		}
	else{
		return true;
 		}
	}


//-------------------------------------------------------
// does validation for one input field triggered by an event

function validateRequired(fieldObj){
	var fieldImage=fieldObj.previousSibling;
 	if(validateResult(fieldObj)){
		fieldImage.className="caution";
		fieldObj.focus();
 		}
 	else{
		fieldImage.className="completed";
		}
	}

//-------------------------------------------------------
// Does validation triggered by an event, checks either current 
// field or field identified by eitheror attribute for values
// This is not compatible with checkboxes - their value 
// may get blanked instead of being unchecked!
function validateRequiredOr(eifieldObj){
	var result="";
	var eiLen=eifieldObj.value.length;
	var eifieldImage=eifieldObj.previousSibling;
	var eifieldLabel=getLabel(eifieldObj.id);

	var orId=eifieldObj.getAttribute("eitheror");
	if(document.getElementById(orId)){
		var orfieldObj=document.getElementById(orId);
		var orfieldLabel=getLabel(orfieldObj.id);
		var orLen=orfieldObj.value.length;
		var orfieldImage=orfieldObj.previousSibling;
		}
	else{
		var orfieldObj="";
		var orfieldLabel="";
		var orLen=0;
		var orfieldImage="";
		}
	if(eiLen==0 && orLen==0){
		eifieldImage.className="caution";
		orfieldImage.className="caution";
		result="Please complete "+eifieldLabel+" or "+orfieldLabel+".";  
		}
	else if(eiLen==0 && orLen!=0){
		eifieldImage.className="completed";
		eifieldObj.value="";
		}
	else if(eiLen!=0){
	 	if(validateResult(eifieldObj)){
			eifieldImage.className="caution";
			eifieldObj.focus();
 			}
 		else{
			eifieldImage.className="completed";
			orfieldImage.className="completed";
			orfieldObj.value="";
			}
		}
	if(result==""){return false;}else{return result;}
	}

//---------------------------------------------------------
//

function validateResult(fieldObj){
	var result="";

	if(fieldObj.value){
		var fieldValue=trim(fieldObj.value);
		}
	else{
		var fieldValue=fieldObj.value;
		}
	var fieldClass=fieldObj.className;
	if(fieldObj.id){var fieldLabel=getLabel(fieldObj.id);}
	else{var fieldLabel='';}
	var patternName=fieldObj.getAttribute("pattern");
	var fieldTitle=fieldObj.getAttribute("title");
	var maxLength=fieldObj.getAttribute("maxlength");
	if(fieldClass=="required" && fieldValue.length==0){
		result="Please complete "+fieldLabel+".";  
		}
   	else if(patternName!=null && patternName!="email"){
		var pattern=getPattern(patternName);
     	var problem=fieldValue.match(pattern);
    	if(problem!=null){
       		result="Found this non-allowed value '"+problem+"' in "+fieldLabel+"! \n";
			}
  		}
   	else if(patternName!=null && patternName=="email"){
		var pattern=getPattern(patternName);
     	var problem=fieldValue.match(pattern);
    	if(problem==null && fieldValue!=''){
       		result="This is not a valid email address! \n";
			}
  		}
   	else if(maxLength!=null){
    	if(fieldValue.length>maxLength){
       		result="Too many characters in "+fieldLabel+"! \n";
			}
  		}
	if(result==""){return false;}else{return result;}
	}


//-------------------------------------------------------
// uses the label to refer to an input field

function getLabel(fieldId) {
 	var label;
	var labels=document.getElementsByTagName("label");
 	for(var i=0; (label=labels[i]); i++){
   		if(label.getAttribute('for')==fieldId){
			var fieldLabel=label.firstChild.nodeValue;
     		return fieldLabel;
   			}
 		}
	fieldLabel='text box'
    return fieldLabel;
	}


//-------------------------------------------------------
// checks if CAPSLOCK is on during the login
// the fine logic for this script came courtesy of http://www.howtocreate.co.uk 

function capsCheck(e){
	if(!e){e=window.event;} 
	if(!e){return;}
	var theKey=e.which ? e.which : (e.keyCode ? e.keyCode : (e.charCode ? e.charCode : 0));
	var theShift=e.shiftKey || (e.modifiers && (e.modifiers & 4));
	if(((theKey>64 && theKey<91 && !theShift) || (theKey>96 && theKey<123 && theShift))){
		alert('WARNING:\n\nCaps Lock is enabled on the keyboard\n\nPlease turn it off. Your login is case sensitive.');
		}
	}


/**
 * Removing leading and trailing spaces
 */

function trim(s){
	s=s.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	return s;
	}

/*-------------------------------------------------------*/

/**
 * functions previously in the file printing.js
 */
function openFileExport(ftype){
	printWindow=window.open('','','height=250,width=450,dependent');
	printWindow.document.open();
	printWindow.document.writeln("<html>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<script type='text/javascript'>function actionpage(){document.location='scripts/export.php?ftype="+ftype+"'}</script>");
	printWindow.document.writeln("<body onLoad=\"setTimeout('actionpage()', 5000);\">");
	printWindow.document.writeln("<h3>The file will download shortly.<h2>");
	printWindow.document.writeln("<h4>Open using your favourite Spreadsheet.<h4>");
	printWindow.document.writeln("<form><input type='button' value='Close This Window' onClick='window.close();' /></form>");
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();
	}

function openXMLExport(ftype){
	printWindow=window.open('','','height=250,width=450,dependent');
	printWindow.document.open();
	printWindow.document.writeln("<html>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<script type='text/javascript'>function actionpage(){document.location='scripts/export.php?ftype="+ftype+"'}</script>");
	printWindow.document.writeln("<body onLoad=\"setTimeout('actionpage()', 5000);\">");
	printWindow.document.writeln("<h3>The XML file will download shortly.<h2>");
	printWindow.document.writeln("<h4>Save to disk.<h4>");
	printWindow.document.writeln("<form><input type='button' value='Close This Window' onClick='window.close();' /></form>");
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();
	}

function xulsave(){
        path = document.location.toString().slice(8).replace(/\//g,"\\");
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		var content = document.getElementById("content").innerHTML;
        data = "<html>" + content + "</html>";
        var file = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
        file.initWithPath(path);
        if (file.exists()) { file.remove(true); }
        file.create(file.NORMAL_FILE_TYPE, 0666);
        var outputStream = Components.classes[ "@mozilla.org/network/file-output-stream;1" ].createInstance( Components.interfaces.nsIFileOutputStream );
        //outputStream.init( file, 0x04 | 0x08, 420, 0 );
        outputStream.init( file, 2, 0x200, false);
      var result = outputStream.write( data, data.length );
        outputStream.close();
      }


function openChartReport(xml, xsltName, paper){
	var content="";
	if(xml!=""){
		content=serializeXML(xml);
		}

	if(paper=="landscape"){
		printWindow=window.open('','','height=600,width=900,dependent,resizable,menubar,screenX=50,scrollbars');
		}
	else{
		printWindow=window.open('','','height=800,width=750,dependent,resizable,menubar,screenX=50,scrollbars');
		}

	printWindow.document.open("text/html");
	printWindow.document.writeln("<html xmlns='http://www.w3.org/1999/xhtml'>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<link rel='stylesheet' type='text/css' href='../templates/"+xsltName+".css' media='all' title='ReportBook Output' />");
	printWindow.document.writeln("<script language='JavaScript' type='text/javascript' src='js/raphael.js' charset='utf-8'></script>");
	printWindow.document.writeln("<script language='JavaScript' type='text/javascript' src='../templates/"+xsltName+".js' charset='utf-8'></script>");
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<body onLoad=\""+xsltName+"();\">");
	printWindow.document.writeln(content);
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();
	}

/* Receives the result of an xsl transformation as xml and opens a
separate preview window to display. xsltName defines the css sheet to
apply and paper is either ladnscape or portrait.*/
function openPrintReport(xml, xsltName, paper){
	var content="";

	if(xml!=""){
		content=serializeXML(xml);
		}

	if(paper=="landscape"){
		printWindow=window.open('','','height=600,width=900,dependent,resizable,menubar,screenX=50,scrollbars');
		}
	else{
		printWindow=window.open('','','height=800,width=750,dependent,resizable,menubar,screenX=50,scrollbars');
		}

	printWindow.document.open("text/html");
	printWindow.document.writeln("<html xmlns='http://www.w3.org/1999/xhtml'>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<link rel='stylesheet' type='text/css' href='../templates/"+xsltName+".css' media='all' title='ReportBook Output' />");	
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<body>");
	printWindow.document.writeln(content);
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();

	}



// turns xml into a single string
function serializeXML(xmlDocument){
  var xmlSerializer;
  try {
    xmlSerializer=new XMLSerializer();
    return xmlSerializer.serializeToString(xmlDocument);
  }
  catch (e) {
//    output("");
    return "Can't serialize XML document.";
  }
}



/*-------------------------------------------------------*/

/**
 * Functions previous in separate file register.js
 */

/* used to identify the sid of the current row under the mouse in a sidtable */
var currentsidrow=-1;

function sidtableInit(){
	// sets up the sidtable and should be called by loadRequired
	// if there is a table with id=sidtable

	// add event handlers to the th elements
	var ths=document.getElementsByTagName("th");
	for(var i=1;i<ths.length;i++){
		var thObj=ths[i];
		if(thObj.id){
			if(thObj.className=="selected"){var colId=thObj.id;}
			thObj.onclick=function(){selectColumn(this,1);};
			thObj.onfocus=function(){selectColumn(this,1);};
			//thObj.addEventListener("click",selectColumn(this,1),false);
			//thObj.addEventListener("focus",selectColumn(this,1),false);
			}
		}

	// select an initial column, identified by class=selected
	if(colId){
		//var colId="event-"+eveId;
		thObj=document.getElementById(colId);
		if(thObj){selectColumn(thObj,1);}
		}

	// add event handlers to the td elements in the edit column
	var tds=document.getElementsByTagName("td");
	for(var i=0;i<tds.length;i++){
		var tdObj=tds[i];
		if(tdObj.className=="student"){
			tdObj.onmouseover=function(){decorateStudent(this)};
			}
		else if(tdObj.className=="edit" | tdObj.className=="edit extra"){
			var selObj=tdObj.getElementsByTagName("select")[0];
			selObj.onfocus=function(){checkAttendance(this)};
			selObj.onblur=function(){processAttendance(this)};
			}
		}
	}

function decorateStudent(tdObj){
	var rowId=tdObj.parentNode.id;
	var sidId=rowId.substring(4,rowId.length);//strip off "sid-" part
	if(sidId!=currentsidrow){
		setTimeout("addExtraFields("+sidId+",null,'merit','')",100);
		setTimeout("removeExtraFields("+currentsidrow+",'merit','')",100);
		currentsidrow=sidId;
		}
	}

/**
 * Highlight the student row when the attendnace input has focus. 
 */
function checkAttendance(selObj){
	var editId=selObj.parentNode.id;
	var sidId=editId.substring(5,editId.length);//strip off "edit-" part
	processAttendance(selObj);
	var rowId="sid-"+sidId;
	selectRow(rowId);
	}

/**
 * As focus leaves the attendance input add/remove the extra fields
 * for absence codes.
 */
function processAttendance(selObj){
	var editId=selObj.parentNode.id;
	var sidId=editId.substring(5,editId.length);//strip off "edit-" part
	if(selObj.value=="a"){
		removeExtraFields(sidId,"extra-p","edit");
		selObj.parentNode.className=selObj.parentNode.className+" extra";
		if(!document.getElementById("code-"+sidId)){
			addExtraFields(sidId,null,"extra-a","edit");
			}
		}
	else{
		if(selObj.value=="n"){selObj.value="p";}
		removeExtraFields(sidId,"extra-a","edit");
		if(!document.getElementById("late-"+sidId)){
			addExtraFields(sidId,null,"extra-p","edit");
			}
		}
	}

function selectRow(rowId){
	var oldtdObj=document.getElementById("selected-row");
	if(oldtdObj){
		oldtdObj.className="";
		oldtdObj.id="";
		var len=oldtdObj.parentNode.getElementsByTagName("td").length;
		var oldtdEditObj=oldtdObj.parentNode.getElementsByTagName("td")[len-1];
		if(oldtdEditObj.getElementsByTagName("select")[0].value=="a"){
			oldtdEditObj.className="edit extra";
			}
		else{
			oldtdEditObj.className="edit";
			}
		}

	// expects the third td of the row to contain the sid's name
	// consequently this gets the class
	var tdObj=document.getElementById(rowId).getElementsByTagName("td")[2];
	tdObj.className="selected";
	tdObj.setAttribute("id","selected-row");
	len=document.getElementById(rowId).getElementsByTagName("td").length;
	// expects the last cell of the row to be the edit cell
	var tdEditObj=document.getElementById(rowId).getElementsByTagName("td")[len-1];
	if(tdEditObj.getElementsByTagName("select")[0].value=="a"){
		tdEditObj.className="edit selected extra";
		}
	else{
		tdEditObj.className="edit selected";
		}
	}

// get an index of all sids with a table row
function getSidsArray(){

	var i=0;
	var sids=new Array();
	var theRows=document.getElementsByTagName("tr");
	for(var c=0;c<theRows.length;c++){
		if(theRows[c].attributes.getNamedItem("id")){
			var rowId=theRows[c].attributes.getNamedItem("id").value;
			sids[i]=rowId.substring(4,rowId.length);
			i++;
			}
		}

	return sids;
	}


function selectColumn(thObj,multi){

	var sids=getSidsArray();

	if(multi==1){
		// only allowed one checked column, so un-select all other columns
		var theCols=document.getElementsByTagName("th");
		for(var c=1;c<(theCols.length-1);c++){
			if(theCols[c].className=="selected"){
				theCols[c].getElementsByTagName("input")[0].removeAttribute("checked");
				var colId=theCols[c].getElementsByTagName("input")[0].value;
				theCols[c].removeAttribute("class");
				for(var d=0; d < sids.length; d++){
					var cellId="cell-"+colId+'-'+sids[d];
					document.getElementById(cellId).className="";
					}
				}
			}
		}

	thObj.getElementsByTagName("input")[0].setAttribute("checked","checked");
	thObj.className="selected";
	var colId=thObj.getElementsByTagName("input")[0].value;
	for(var c=0;c<sids.length;c++){
			var cellId="cell-"+colId+"-"+sids[c];
			var editId="edit-"+sids[c];
			cellObj=document.getElementById(cellId);
			cellObj.className="selected";
			if(document.getElementById(editId)){
				var tdEditObj=document.getElementById(editId);
				var selObj=tdEditObj.getElementsByTagName("select")[0];
				selObj.value=cellObj.attributes.getNamedItem("status").value;
				var tdEditClaSS=tdEditObj.className;
				removeExtraFields(sids[c],"extra-a","edit");
				removeExtraFields(sids[c],"extra-p","edit");
				if(selObj.value=="a"){
					tdEditClaSS=tdEditClaSS+" extra";
					addExtraFields(sids[c],cellId,"extra-a","edit");
					}
				else{
					tdEditClaSS="edit";
					if(selObj.value=="p"){addExtraFields(sids[c],cellId,"extra-p","edit")}
					}
				tdEditObj.className=tdEditClaSS;
				}
			//i++;
			}
	}


/**
 * Will grab a hidden div identified by extraDiv (id="add-extraDiv") and 
 * place a copy in the sidtable for a particular sid.
 * The exact location it is added to is identified by the containerId (id="containerId-sid")
 */
function addExtraFields(sidId,cellId,extraId,containerId){
	if(containerId==''){containerId=extraId;}
	var editContainer=document.getElementById(containerId+"-"+sidId);
	var extraDiv=document.getElementById("add-"+extraId).cloneNode(true);
	extraDiv.removeAttribute("class");
	extraDiv.id="add-"+extraId+"-"+sidId;
	var newElements=extraDiv.childNodes;
	if(cellId!=null){var cellObj=document.getElementById(cellId);}
	for(var i=0;i<newElements.length;i++){
		var genName=newElements[i].name;
		var genId=newElements[i].id;
		if(genName){
			newElements[i].name=genName+"-"+sidId;
			newElements[i].id=genId+"-"+sidId;
			if(cellId!=null){newElements[i].value=cellObj.attributes.getNamedItem(genName).value;}
			}
		}
	editContainer.insertBefore(extraDiv,null);
	}

function removeExtraFields(sidId,extraId,containerId){
	if(containerId==''){containerId=extraId;}
	var editContainer=document.getElementById(containerId+"-"+sidId);
	var extraDiv=document.getElementById("add-"+extraId+"-"+sidId);
	if(extraDiv){document.getElementById(containerId+"-"+sidId).removeChild(extraDiv);}
	}


/**
 * sets all attendance boxes to a preset value of either a or p, if set is a 
 * numerical event_id then all values are preset with the existing value from
 * that column
 */
function setAll(set){
	var sids=getSidsArray();
	for(var c=0;c<sids.length;c++){
			var editId="edit-"+sids[c];
			if(document.getElementById(editId)){
				var tdEditObj=document.getElementById(editId);
				var selObj=tdEditObj.getElementsByTagName("select")[0];
				var tdEditClaSS=tdEditObj.className;
				removeExtraFields(sids[c],"extra-a","edit");
				removeExtraFields(sids[c],"extra-p","edit");

				if(set!="p" & set!="a"){
					//var colId="event-"+eveId;
					var cellId="cell-"+set+'-'+sids[c];
					var cellObj=document.getElementById(cellId);
					if(cellId!=null){
						status=cellObj.attributes.getNamedItem("status").value;
						}
					else{
						status="a";
						}
					}
				else{
					status=set;
					cellId=null;
					}

				if(status=="a"){
					tdEditClaSS=tdEditClaSS+" extra";
					addExtraFields(sids[c],cellId,"extra-a","edit");
					selObj.value=status;
					}
				else if(status=="p"){
					tdEditClaSS="edit";
					addExtraFields(sids[c],cellId,"extra-p","edit");
					selObj.value=status;
					}
				tdEditObj.className=tdEditClaSS;
				}
			//i++;
			}
	}


//
function openAlert(book) {
	//document.getElementById(book+"options").innerHTML=window.frames["view"+book].document.getElementById("hiddenbookoptions").innerHTML;

	document.getElementById("notice").className="overlay";

	}

function closeAlert() {
	document.getElementById('notice').className="hidden";
	}

