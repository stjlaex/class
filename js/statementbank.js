//							statementbank.js

var area;
var ability;

function chooseStatement(statementObject){
	var comment=document.getElementById("Comment").value;
	comment=comment+statementObject.innerHTML;
	document.getElementById("Comment").value=comment;
	}


function filterbyArea(newarea){
	area=newarea;
	filterStatements(area,ability);
	}

function filterbyAbility(newability){
	ability=newability;
	filterStatements(area,ability);
	}

function filterStatements(area,ability){

	}
