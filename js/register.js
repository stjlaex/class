// some of this is register specific
// but lots needs to be lifted out to make sidtable of general utility 
// --- in progress

function sidtableInit(){
	// sets up the sidtable and should be called by loadRequired
	// if there is a table with id=sidtable

	// add event handlers to the th elements
	var ths=document.getElementsByTagName("th");
	for(var i=2;i<ths.length;i++){
		var thObj=ths[i];
		if(thObj.id){
			if(thObj.className=="selected"){var colId=thObj.id;}
			thObj.onclick=function(){selectColumn(this,1)};
			thObj.onfocus=function(){selectColumn(this,1)};
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
		if(tdObj.className=="edit" | tdObj.className=="edit extra"){
			var selObj=tdObj.getElementsByTagName("select")[0];
			selObj.onfocus=function(){checkAttendance(this)};
			selObj.onblur=function(){processAttendance(this)};
			//selObj.addEventListener("focus",checkAttendance(this),false);
			//selObj.addEventListener("blur",processAttendance(this),false);
			}
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
		removeExtraFields(sidId,"extra-p");
		selObj.parentNode.className=selObj.parentNode.className+" extra";
		if(!document.getElementById("code-"+sidId)){
			addExtraFields(sidId,null,"extra-a");
			}
		}
	else{
		if(selObj.value=="n"){selObj.value="p";}
		removeExtraFields(sidId,"extra-a");
		if(!document.getElementById("late-"+sidId)){
			addExtraFields(sidId,null,"extra-p");
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
		for(var c=2;c<(theCols.length-1);c++){
			if(theCols[c].className=="selected"){
				theCols[c].getElementsByTagName("input")[0].removeAttribute("checked");
				var colId=theCols[c].getElementsByTagName("input")[0].value;
				theCols[c].removeAttribute("class");
				for(var c=0; c < sids.length; c++){
					var cellId="cell-"+colId+'-'+sids[c];
					document.getElementById(cellId).className="";
					}
				}
			}
		}

	thObj.className="selected";
	thObj.getElementsByTagName("input")[0].setAttribute("checked","checked");
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
				removeExtraFields(sids[c],"extra-a");
				removeExtraFields(sids[c],"extra-p");
				if(selObj.value=="a"){
					tdEditClaSS=tdEditClaSS+" extra";
					addExtraFields(sids[c],cellId,"extra-a");
					}
				else{
					tdEditClaSS="edit";
					if(selObj.value=="p"){addExtraFields(sids[c],cellId,"extra-p")}
					}
				tdEditObj.className=tdEditClaSS;
				}
			i++;
			}
	}

function addExtraFields(sidId,cellId,extraId){
	var editContainer=document.getElementById("edit-"+sidId);
	var extraDiv=document.getElementById(extraId).cloneNode(true);
	extraDiv.removeAttribute("class");
	extraDiv.id=extraId+"-"+sidId;
	var newElements=extraDiv.childNodes;
	var cellObj=document.getElementById(cellId);
	for(var i=0;i<newElements.length;i++){
		var genName=newElements[i].name;
		var genId=newElements[i].id;
		if(genName){
			newElements[i].name=genName+"-"+sidId;
			newElements[i].id=genId+"-"+sidId;
			if(cellId!=null){
				newElements[i].value=cellObj.attributes.getNamedItem(genName).value;
				}
			}
		}
	editContainer.insertBefore(extraDiv,null);
	}

function removeExtraFields(sidId,extraId){
	var editContainer=document.getElementById("edit-"+sidId);
	var extraDiv=document.getElementById(extraId+"-"+sidId);
	if(extraDiv){document.getElementById("edit-"+sidId).removeChild(extraDiv);}
	}


//-------------------------------------------------------
// sets all attendance boxes to present

function setAll(state){
	var sids=getSidsArray();

	for(var c=0;c<sids.length;c++){
			var editId="edit-"+sids[c];
			if(document.getElementById(editId)){
				var tdEditObj=document.getElementById(editId);
				var selObj=tdEditObj.getElementsByTagName("select")[0];
				selObj.value=state;
				var tdEditClaSS=tdEditObj.className;
				removeExtraFields(sids[c],"extra-a");
				removeExtraFields(sids[c],"extra-p");
				if(state=="a"){
					tdEditClaSS=tdEditClaSS+" extra";
					addExtraFields(sids[c],null,"extra-a");
					}
				else{
					tdEditClaSS="edit";
					addExtraFields(sids[c],null,"extra-p");
					}
				tdEditObj.className=tdEditClaSS;
				}
			i++;
			}
	}
