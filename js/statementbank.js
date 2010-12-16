//							statementbank.js

var subarea="*";
var ability="*";

function chooseStatement(statementObject){
	var ifr=document.getElementById("Comment0_ifr").contentDocument;
	var newPara = document.createElement("p");
	var newTxt = document.createTextNode(statementObject.innerHTML);
	newPara.appendChild(newTxt);
	ifr.body.appendChild(newPara);
	}

function filterbySubarea(newsubarea){
	area=newsubarea;
	filterStatements(subarea,ability);
	}

function filterbyAbility(tdObj){
	newability=tdObj.getAttribute("abilityoption");
	var divs=document.getElementsByTagName("div");
	for(var i=0; (div=divs[i]); i++){
		if(div.getAttribute("class")=="statementlevels"){
			var choices=div.getElementsByTagName("td");
			for(var i2=0; (choice=choices[i2]); i2++){
				if(choice.getAttribute("abilityoption")==ability && newability!=ability){
					choice.removeAttribute("class");
					}
				else if(choice.getAttribute("abilityoption")==newability){
					choice.setAttribute("class","vspecial");
					}
				}
			}
		}
	ability=newability;
	filterStatements(subarea,ability);
	}

function filterStatements(subarea,ability){
	currentArea=document.getElementById("current-tinytab").getAttribute("class");
	var sourceId="tinytab-display-area";
	var statements=document.getElementById(sourceId).getElementsByTagName('td');
	for(var i=0; (statement=statements[i]); i++){
		if(statement.getAttribute("ability")){
			if(statement.getAttribute("ability")==ability || ability=='*'){
				statement.style.display="table-cell";
				}
			else{
				statement.style.display="none";
				}
			}
		}
	return;
	}
