// some of this is register specific
// but lots needs to be lifted out to make sidtable of general utility 
// --- in progress

// used to identify the sid of the current row under the mouse in a sidtable
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

function checkAttendance(selObj){
	var editId=selObj.parentNode.id;
	var sidId=editId.substring(5,editId.length);//strip off "edit-" part
	processAttendance(selObj);
	var rowId="sid-"+sidId;
	selectRow(rowId);
	}

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
			i++;
			}
	}

/*
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


//-------------------------------------------------------
// sets all attendance boxes to a preset value of either a or p, if set is a 
// numerical event_id then all values are preset with the existing value from
// that column
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
			i++;
			}
	}
