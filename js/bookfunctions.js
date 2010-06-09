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
//
function tinyTabs(tabObject){
	// the id of containing div (eg. area for statementbank)
	var tabmenuId=tabObject.parentNode.parentNode.parentNode.id;
	var chosentab=tabObject.getAttribute("class");
	document.getElementById("current-tinytab").removeAttribute("id");
	document.getElementById("tinytab-"+tabmenuId+"-"+chosentab).firstChild.setAttribute("id","current-tinytab");
	var targetId="tinytab-display-"+tabmenuId;
	var sourceId="tinytab-xml-"+tabmenuId+"-"+chosentab;
	var fragment=document.getElementById(sourceId).innerHTML;
	document.getElementById(targetId).innerHTML="";
	document.getElementById(targetId).innerHTML=fragment;
	if(document.getElementById("statementbank")){
		//this must be running the statement bank
		filterStatements(subarea,ability);
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


//Only for rowaction or sideoption buttons NOT for buttonmenu form buttons.
//The type of action is specified as the button's name attribute and
//possible action values are Edit, New, process, print, chart and current.
//With current it will always ask for confirmation before making a xmlhttprequest 
//and it applies returned xml to update the current page wihtout a reload.
//The print and chart actions are for pop-up report windows and don't affect any changes.
function clickToAction(buttonObject){
	var i=0;
	//need the id of the div containing the xml-record 
	var theDivId=buttonObject.parentNode.id;
	if(theDivId==""){
		//gets it from the id of the tbody container for this row
		var theContainerId=buttonObject.parentNode.parentNode.parentNode.id;
	//alert(theContainerId);
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
		document.getElementById("formstatus-new").setAttribute("class","hidden");
		document.getElementById("formstatus-edit").setAttribute("class","");
		document.getElementById("formstatus-action").setAttribute("class","hidden");
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
			var recordId=xmlRecord.childNodes[1].childNodes[0].nodeValue;
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
		var recordId=xmlRecord.childNodes[1].childNodes[0].nodeValue;
		var script=buttonObject.value;
		var url=pathtobook + "httpscripts/" + script + "?uniqueid=" + escape(recordId);
		if(action!="print" && action!="chart"){
			var answer=confirmAction(buttonObject.title);
			var params="";
			}
		else{
			var answer=true;
			var params="";
			// this is for passing a list of sids grabbed from the tr ids of a sidtable
			// used for example by report_profile_print
			if(document.getElementById("sidtable")){
				var sidrows=document.getElementById("sidtable").getElementsByTagName("tr");
				for(var c=0; c<sidrows.length; c++){
					if(sidrows[c].id!=""){
						var rowId=escape(sidrows[c].attributes["id"].value);
						var sidId=rowId.substring(4,rowId.length);//strip off "sid-" part
						params=params+"&sids[]=" + sidId;
						}
					}
				}
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
	}


//Pop-up report window for one student in a sidtable.
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
		tableObj=document.getElementById("sidtable");
		var trs=tableObj.getElementsByTagName("tr");
		for(var c=0;c<trs.length;c++){
			var rowId=trs[c].id;
			filterrowIndicator(rowId,"")
			}
		}
	}

//------------------------------------------------------- 
// Only called by form buttons in place of processContent() 
// this will pass all the checked sids[] in a sidtable along-with 
// whatever parameters are listed in the embedded xml contained 
// in a div with id=xml-checked-action
// whatever xml is returned by the httpscript called by the button
// is transformed using the xsl transformation named in transform 
// (which must be listed along-with the other params in the embedded xml)

function checksidsAction(buttonObject){
	var formObject=document.formtoprocess;
	var formElements=formObject.elements;
	var action=buttonObject.name;
	var script=buttonObject.value;
	var params="";
	var xsltransform="";
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
				else if(paramname=="selectname"){
					//used by the js and not passed as a param
					var selectname=escape(xmlvalue);
					}
				else{
					params=params + "&" + paramname + "=" + escape(xmlvalue);
					}
		    	}
			}

		}


	//now grab all the checked input sids
	var sids=new Array();
	var sidno=0;
	for(var c=0; c<formObject.elements.length; c++){
		if(formObject.elements[c].name=="checkall"){
			c=c+1;
			}
		if(formObject.elements[c].type=="checkbox" && formObject.elements[c].name=="sids[]"){
			if(formObject.elements[c].checked){
				sids[sidno++]=formObject.elements[c].value;
				params=params+"&sids[]=" + escape(formObject.elements[c].value);
				//and uncheck them for (maybe) convenience
				formObject.elements[c].checked=false;
				}
			}
		else if(formObject.elements[c].name==selectname && 
						formObject.elements[c].type=="select-one"){
			var selectObj=formObject.elements[c];
			for(var i=0; i < selectObj.options.length; i++){
				if(selectObj.options[i].selected){
					params=params + "&" + selectname + "=" + escape(selectObj.options[i].value);
					}
				}
			}
		else if(formObject.elements[c].name==selectname && 
						formObject.elements[c].type=="select-multiple"){
			var selectObj=formObject.elements[c];
			for(var i=0; i < selectObj.options.length; i++){
				if(selectObj.options[i].selected){
					params=params + "&" + selectname + "[]=" + escape(selectObj.options[i].value);
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
						xsltransform=xmlReport.getElementsByTagName("transform")[0].firstChild.nodeValue;
						paper=xmlReport.getElementsByTagName("paper")[0].firstChild.nodeValue;
						}
					//function to actually process the returned xml	
					if(xsltransform!=""){
						var xmlResult=processXML(xmlReport,xsltransform,"../templates/");
						if(action=="chart"){
							openCharttReport(xmlResult,xsltransform,paper);
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


/* Fetches the names xsl transformation sheet and uses it to process the xmlsource.*/
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
			if(document.getElementById(fieldId)){
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
*/
function checkAll(checkAllBox){
	var formObject=checkAllBox.form;
	for(var c=0; c<formObject.elements.length; c++){
		if(formObject.elements[c].name=="checkall"){
			c=c+1;
			}
		if(formObject.elements[c].type=="checkbox" && getrowIndicator(formObject.elements[c])!="hidden"){
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
// adds the images and attributes to required input fields
// inits the js-calendar elements and the tooltip titles

function loadRequired(){
	var firstFocus;
	var formObject;
	var elementObject;
	var imageRequired;
	firstFocus=-1;
	for(i=0;i<document.forms.length;i++){
		formObject=document.forms[i];
		for(c=0;c<formObject.elements.length;c++){
			elementObject=formObject.elements[c];
			if(elementObject.className=="required"){
				elementObject.setAttribute("onChange","validateRequired(this)");
				imageRequired=document.createElement("img");
				imageRequired.className="required";
				elementObject.parentNode.insertBefore(imageRequired, elementObject);
				}
			else if(elementObject.className=="requiredor"){
				elementObject.setAttribute('onChange','validateRequiredOr(this)');
				imageRequired=document.createElement("img");
				imageRequired.className="required";
				elementObject.parentNode.insertBefore(imageRequired, elementObject);
				}
			else if(elementObject.className=="switcher"){
				switcherId=elementObject.getAttribute("id");
				//alert(switcherId,elementObject.value);
				parent.selerySwitch(switcherId,elementObject.value);
				elementObject.setAttribute("onChange","selerySwitch('"+switcherId+"',this.value)");
				}

			// add event handlers to the checkbox input elements
			if(elementObject.getAttribute("type")=="checkbox" && elementObject.name=="sids[]"){
				elementObject.onchange=function(){checkrowIndicator(this)};
				}
			if(elementObject.getAttribute("tabindex")=="1" && firstFocus=="-1"){
				firstFocus=c;
				}
			if(elementObject.getAttribute("maxlength")){
				var maxlength=elementObject.getAttribute("maxlength");
				if(maxlength>180){
					elementObject.style.width="80%";
					}
				else if(maxlength>50){
					elementObject.style.width="60%";
					}
				else if(maxlength<20 && maxlength>0){
					elementObject.style.width=maxlength+"em";
					}
				}
			if(elementObject.getAttribute("type")=="date"){
				var inputId=elementObject.getAttribute("id");
				Calendar.setup({
      					inputField  : inputId,
      					ifFormat    : "%Y-%m-%d",
      					button      : "calendar-"+inputId
    					});
				}
			}
		}
	/*load the first tiny-tab (if there is one)*/
	if(document.getElementById('current-tinytab')){
		tinyTabs(document.getElementById("current-tinytab"));
		}

	/*prepares the span elements with title attributes for qtip*/
	tooltip.init();

	/*prepares a sidtable if it is present*/
	if(document.getElementById("sidtable")){
		sidtableInit();
		}


	/*give focus to the tab=1 form element if this is a form*/
	/*should always be last!*/
	if(i>0){
		if(firstFocus==-1){firstFocus=0;}
		if(document.forms[0].elements[firstFocus]){
		  document.forms[0].elements[firstFocus].focus();  
		  }
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
		if(fieldClass=="requiredor"){
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
	var fieldValue=fieldObj.value;
	var fieldClass=fieldObj.className;
	if(fieldObj.id){var fieldLabel=getLabel(fieldObj.id);}
	else{var fieldLabel='';}
	var patternName=fieldObj.getAttribute("pattern");
	var fieldTitle=fieldObj.getAttribute("title");
	var maxLength=fieldObj.getAttribute("maxlength");
	if(fieldTitle=="spellcheck" && currObj.spellingResultsDiv!=null){
//		setCurrentObject(currObj); 
//		resumeEditing();
		result="You need to 'Resume Editing' before you SUBMIT!";
		}
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
