//							statementbank.js

var subarea="*";
var ability="*";

function chooseStatement(statementObject){
	var comment=document.getElementById("Comment").value;
	comment=comment+' '+statementObject.innerHTML;
	document.getElementById("Comment").value=comment;
	}

function filterbySubarea(newsubarea){
	area=newsubarea;
	filterStatements(subarea,ability);
	}

function filterbyAbility(newability){
	ability=newability;
	filterStatements(subarea,ability);
	}

function filterStatements(subarea,ability){
	currentArea=document.getElementById("current-tinytab").getAttribute("class");
	var sourceId="tinytab-display-area";
	var statements=document.getElementById(sourceId).getElementsByTagName('td');
	for(var i=0; (statement=statements[i]); i++){
		if(statement.getAttribute('ability')){
			if(statement.getAttribute('ability')==ability || ability=='*'){
				statement.style.display="table-cell";
				}
			else{
				statement.style.display="none";
				}
			}
		}
	return;
	}
