//------------------------------------------------------
//opens the comment writer window
function clickToWriteComment(sid,rid,bid,pid,entryn,openId){
	var commenturl;
	commenturl="sid="+sid+"&rid="+rid+"&bid="+bid+"&pid="+pid+"&entryn="+entryn+"&openid="+openId;
	openCommentWriter(commenturl);
	}

function openCommentWriter(commenturl){
	writerWindow=window.open('','','height=650,width=650,screenX=50,dependent');
	writerWindow.document.open();
	writerWindow.document.writeln("<html>");
	writerWindow.document.writeln("<head>");
	writerWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	writerWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	writerWindow.document.writeln("</head>");
	writerWindow.document.writeln("<script type='text/javascript'>function actionpage(){document.location='reportbook/httpscripts/comment_writer.php?"+commenturl+"';}</script>");
	writerWindow.document.writeln("<body onLoad=\"setTimeout('actionpage()', 100);\">");
	writerWindow.document.writeln("</body>");
	writerWindow.document.writeln("</html>");
	writerWindow.document.close();
	}

function closeCommentWriter(commentId,entryn,text){
	if(commentId!='-100'){
		opener.updateComment(commentId,entryn,text);
		}
	window.close();
	}

function updateComment(commentId,entryn,text){
//	the id should refer to the containing html entity for the icon (probably a td)
//  and the actual textarea for the text
	if(document.getElementById('text'+commentId)){
		document.getElementById('text'+commentId).value=text;
		}
	if(document.getElementById('icon'+commentId)){
		document.getElementById('icon'+commentId).setAttribute("class","vspecial");
		}
	if(document.getElementById('inmust'+commentId)){
		document.getElementById('inmust'+commentId).value=entryn;
		}
	}

//------------------------------------------------------
//
function tinyTabs(tabObject){
	// the id of containing div (eg. area for statementbank)
	var tabmenuId=tabObject.parentNode.parentNode.parentNode.id;
	var chosentab=tabObject.getAttribute("class");
	document.getElementById("current-tinytab").removeAttribute('id');
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
		if(theRow.className=='rowplus'){ 
			theRow.className='rowminus'; 
			}		
		else if(theRow.className=='rowminus'){ 
			theRow.className='rowplus';
			}
		else if(theRow.className=='revealed'){ 
			theRow.className='hidden';
			}
		else if(theRow.className=='hidden'){ 
			theRow.className='revealed';
			}	
		i++;
		}	
	}


var xmlHttp = false;
requestxmlHttp();

function clickToAction(buttonObject){
	var i=0;
	//need the id of the div containing the xml-record 
	var theDivId=buttonObject.parentNode.id;
	if(theDivId==''){
		//gets it from the id of the tbody container for this row
		var theContainerId=buttonObject.parentNode.parentNode.parentNode.id;
		}
	else{
		//or gets it from the id of the parent div container
		var theContainerId=theDivId;
		}
	var xmlId='xml-'+theContainerId;
	var xmlContainer=document.getElementById(xmlId);
	var xmlRecord=xmlContainer.childNodes[1];
	var action=buttonObject.name;
	if(action=='Edit'){
		var test=fillxmlForm(xmlRecord);
		}
	else if(action=='current'){
		var recordId=xmlRecord.childNodes[1].childNodes[0].nodeValue;
		var script=buttonObject.value;
		var url=pathtobook + "httpscripts/" + script + "?uniqueid=" + escape(recordId);
		var answer=confirmAction(buttonObject.title);
		if(answer){
				xmlHttp.open("GET", url, true);
				xmlHttp.onreadystatechange=updatexmlRecord;
				xmlHttp.send(null);
				}
		}
	}

function confirmAction(title){
	var message="You have requested the following action:\n\n";
	message=message + title + "\n\n";
	message=message + "Are you sure you want to continue?";
	var answer=window.confirm(message);
	return answer;
	}

function updatexmlRecord(){
	var exists=false;
	if(xmlHttp.readyState==4){
//		test=xmlHttp.responseText;
		if(xmlHttp.status==200){
			xmlRecord=xmlHttp.responseXML;
			var recordId=xmlRecord.getElementsByTagName('id_db').item(0).firstChild.data;
			var exists=xmlRecord.getElementsByTagName('exists').item(0).firstChild.data;
//		var xmlId='xml-'+recordId;
//		var xmlContainer=document.getElementById(xmlId);
//	    xmlContainer.firstChild.data=xmlRecord;
			if(exists!='true'){
				var tableRecord=document.getElementById(recordId);
				while(tableRecord.hasChildNodes()){
					tableRecord.removeChild(tableRecord.childNodes[0]);
					}
				}
			else{
				fillxmlTable(recordId, xmlRecord);
				}
			}
		else if(xmlHttp.status==404){alert ("Requested URL is not found.");}
        else if(xmlHttp.status==403){alert("Access denied.");} 
		else {alert("status is " + xmlHttp.status);}
		}
	}

function requestxmlHttp(){
	try { xmlHttp=new XMLHttpRequest(); } 
	catch (failed) { xmlHttp=false; }
	if (!xmlHttp) {alert("Error initializing XMLHttpRequest!");}
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
		if(xmltag=='value'){
			var xmltag=xmlRecord.parentNode.parentNode.tagName;
	        var xmlvalue=xmlRecord.nodeValue;
			fieldId=recordId+'-'+xmltag;
			if(document.getElementById(fieldId)){
				document.getElementById(fieldId).firstChild.data=xmlvalue;
				}
			}
		}
	}


//-------------------------------------------------------
// uses the html field id to refer to an input field and replace its value
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

//------------------------------------------------------
//used by the buttonmenu to submit or reset the content form

function processContent(buttonObject){
	var formObject=document.formtoprocess;
	var formElements=formObject.elements;
	var buttonname=buttonObject.name;
	if(buttonObject.value=="Reset"){
		document.formtoprocess.reset();
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



//-------------------------------------------------------
//ticks all checkboxes in a form

function checkAll(checkAllBox){
	var formObject=checkAllBox.form;
	for(var c=0; c<formObject.elements.length; c++){
		if(formObject.elements[c].name=='checkall'){
			c=c+1;
			}
		if(formObject.elements[c].type=='checkbox'){
			if(checkAllBox.checked){
				formObject.elements[c].checked=true;
				}
			else{
				formObject.elements[c].checked=false;
				}
			}
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
			if(elementObject.className=='required'){
				elementObject.setAttribute('onChange','validateRequired(this)');
				imageRequired=document.createElement('img');
				imageRequired.className='required';
				elementObject.parentNode.insertBefore(imageRequired, elementObject);
				}
			else if(elementObject.className=='requiredor'){
				elementObject.setAttribute('onChange','validateRequiredOr(this)');
				imageRequired=document.createElement('img');
				imageRequired.className='required';
				elementObject.parentNode.insertBefore(imageRequired, elementObject);
				}

			if(elementObject.getAttribute('tabindex')=='1' & firstFocus=='-1'){
				firstFocus=c;
				}
			if(elementObject.getAttribute('type')=='date'){
				var inputId=elementObject.getAttribute('id');
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
// does validation triggered by an event, checks either current 
// field or field identified by eitheror attribute for values

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
	var fieldLabel=getLabel(fieldObj.id);
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
	var labels=document.getElementsByTagName('label');
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
