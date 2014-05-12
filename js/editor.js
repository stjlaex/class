/**
 * adds the images and attributes to required input fields
 * inits the js-calendar elements and the tooltip titles
 */
function loadRequired(book){
	var firstFocus;
	var formObject;
	var elementObject;
	var imageRequired;
	firstFocus=-1;
	for(i=0;i<document.forms.length;i++){
		formObject=document.forms[i];
		for(c=0;c<formObject.elements.length;c++){
			elementObject=formObject.elements[c];
			if(elementObject.className.indexOf("required")!=-1){
				elementObject.setAttribute("onChange","validateRequired(this)");
				imageRequired=document.createElement("span");
				imageRequired.className="required";
				if (elementObject.parentNode.className.indexOf("selector") != -1) { //uniform
					elementObject.parentNode.parentNode.insertBefore(imageRequired, elementObject.parentNode);
					elementObject.setAttribute("onChange","validateSelectRequired(this)");
				} else {
					elementObject.parentNode.insertBefore(imageRequired, elementObject);
					}
				}
			if(elementObject.className.indexOf("eitheror")!=-1){
				elementObject.setAttribute('onChange','validateRequiredOr(this)');
				imageRequired=document.createElement("span");
				imageRequired.className="required";
				if (elementObject.parentNode.className.indexOf("selector") != -1) { //uniform
                    elementObject.parentNode.parentNode.insertBefore(imageRequired, elementObject.parentNode);
                    elementObject.setAttribute("onChange","validateSelectRequired(this)");
                    }
                else {
                    elementObject.parentNode.insertBefore(imageRequired, elementObject);
                    }
				}
			if(elementObject.className.indexOf("switcher")!=-1){
				switcherId=elementObject.getAttribute("id");
				parent.selerySwitch(switcherId,elementObject.value,book);
				elementObject.setAttribute("onChange","parent.selerySwitch('"+switcherId+"',this.value,'"+book+"')");
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

	/*prepares the span elements with title attributes for qtip*/
	tooltip.init();


	/*give focus to the tab=1 form element if this is a form*/
	/*should always be last!*/
	if(i>0){
		if(firstFocus==-1){firstFocus=0;}
		if(document.forms[0].elements[firstFocus]){
		  document.forms[0].elements[firstFocus].focus();  
		  }
		}
	}


function selerySwitch(servantclass,fieldvalue){
	switchedId="switch"+servantclass;
	newfielddivId="switch"+servantclass+fieldvalue;
	if(document.getElementById(newfielddivId)){	
		document.getElementById(switchedId).innerHTML=document.getElementById(newfielddivId).innerHTML;
		}
	}
