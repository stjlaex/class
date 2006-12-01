function changeFont(){
    changeFontSize(2,2);
	}

// --------------------------------------------------------------------
// Javascript Magnifier v 0.97
// Written by Dino Termini - termini@email.it - May 9, 2003
// This script is freeware (GPL) but if you use it, please let me know!
//
// Adapted and simplified for ClaSS by S T Johnson
// --------------------------------------------------------------------

// Configuration parameters
// ------------------------
// Measure unit in pixel (px) or points (pt)
// measureUnit = "pt"
measureUnit = "px"

// Start size for tags with no font-size STYLE or CLASS attribute defined
startStyleSize = 6;
// Minimum size allowed for STYLE attribute (like in <FONT STYLE="font-size: 10px"> )
minStyleSize = 6;
// Maximum size allowed for STYLE attribute
maxStyleSize = 15;

// Start size for tags with no SIZE attribute defined
startSize = 6;
// Minimum size allowed for SIZE attribute (like in <FONT SIZE="1"> )
minSize = 6;
// Maximum size allowed for SIZE attribute
maxSize = 15;


// Allow input fields resize (text, buttons, and so on)
//allowInputResize = false;

// End of configuration parameters. Please do not edit below this line
// --------------------------------------------------------------------------------


function searchTags(childTree, level) {
  	var retArray = new Array();
  	var tmpArray = new Array();
  	var j = 0;
  	var childName = "";
  	for (var i=0; i<childTree.length; i++) {
    	childName = childTree[i].nodeName;
    	if (childTree[i].hasChildNodes()) {
      		if ((childTree[i].childNodes.length == 1) && (childTree[i].childNodes[0].nodeName == "#text"))
        	retArray[j++] = childTree[i];
      	else {
        	tmpArray = searchTags(childTree[i].childNodes, level+1);
        	for (var k=0;k<tmpArray.length; k++)
          	retArray[j++] = tmpArray[k];
        	retArray[j++] = childTree[i];
      		}
    	}
    	else
      	retArray[j++] = childTree[i];
  		}
  	return(retArray);
	}

function changeFontSize(stepSize, stepStyleSize){
	var useCookie=false;
	var currentbook=document.getElementById("currentbook").getAttribute("class");

    var myObj = searchTags(window.frames["view"+currentbook].document.body.childNodes, 0);
    var myStepSize = stepSize;
    var myStepStyleSize = stepStyleSize;

    myObjNumChilds = myObj.length;
    for(i=0; i<myObjNumChilds; i++){
      myObjName=myObj[i].nodeName;

      // Only some tags will be parsed
      if(myObjName != "#text" && myObjName != "HTML" &&
          myObjName != "HEAD" && myObjName != "TITLE" &&
          myObjName != "STYLE" && myObjName != "SCRIPT" &&
          myObjName != "BR" && myObjName != "TBODY" &&
          myObjName != "#comment" && myObjName != "FORM"){

        // Skip fields, if required
        if(myObjName == "INPUT") continue;
        if(myObjName == "SELECT") continue;
        if(myObjName == "OPTION") continue;
        if(myObjName == "BUTTON") continue;
        if(myObjName == "H2") continue;
        if(myObjName == "SPAN") continue;
        if(myObjName == "IMG") continue;
        if(myObjName == "LABEL") continue;

        size = parseInt(myObj[i].getAttribute("size"));
		styleSize = parseInt(window.getComputedStyle(myObj[i], null).fontSize);

        //if (!confirm("TAG ["+myObjName+"] SIZE ["+size+"] STYLESIZE ["+styleSize+"]")) return(0);

        if(isNaN(size) || (size < minSize) || (size > maxSize)){size = startSize;}

        if(isNaN(styleSize) || (styleSize < minStyleSize) || (styleSize > maxStyleSize))
          {styleSize = startStyleSize;}

        if( ((size > minSize) && (size < maxSize)) || 
             ((size == minSize) && (stepSize > 0)) || 
             ((size == maxSize) && (stepSize < 0)) || useCookie) {
          	myObj[i].setAttribute("size", size+myStepSize);
        	}

        if ( ((styleSize > minStyleSize) && (styleSize < maxStyleSize)) || 
             ((styleSize == minStyleSize) && (stepStyleSize > 0)) ||
             ((styleSize == maxStyleSize) && (stepStyleSize < 0)) || useCookie) {
          	newStyleSize = styleSize+myStepStyleSize;
          	myObj[i].style.fontSize = newStyleSize+measureUnit;
        	}
      	} // End if condition ("only some tags")
      } // End main for cycle
	}
