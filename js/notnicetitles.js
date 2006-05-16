/*
NiceTitles() takes the following arguments (in this order):

	sTemplate 
	Pretty obvious, the template. If it's not a string (or
	does not exist), the default template attr(nicetitle) is used.

	nDelay 
	This is the delay before the pop-up shows up. It must be
	larger than 0, and it is specified in milliseconds. If you don't
	want a delay, use null as value. By default there is no delay.

	nStringMaxLength 
	This is the maximum length of attribute values to
	be displayed in the pop-up. Everything larger then this number is
	cut off and "..." is added to the end of the value. The default is
	80 characters.  
	
	nMarginX 
	This is the horizontal margin of the
	pop-up from its calculated position. For calculation purposes it
	is set here, it's added to the left position of the pop-up. The
	default is 0 

	nMarginY Sa
	me as nMarginX, but vertical this time ;-)

	sContainerID 
	This is the ID of the div which contains the content
	of the pop-up. If an element with this ID already exists, it is
	used as container. If not, a new container is created. The default
	is nicetitlecontainer.  

	sClassName 
	This is the class of the
	container. The default is nicetitle. Please note: you can't
	specify a new class on an existing container.

	Afer you've created a new NiceTitle, you must add elements to
	it. This is done using the method addElements():
	myNiceTitles.addElements()

	addElements() takes two arguments:

	collNodes
	This is a collection of elements (nodeList) for which
	NiceTitles will be enabled. It's a result of a
	document.getElementsByTagName() call.  
	
	sAttribute 
	This is the name
	of the attribute which is required to exist on the element. Only
	for elements which have this attribute NiceTitles is enabled. It
	will be "renamed" to nicetitle, to prevent browsers from, for
	example, showing their default tooltip for a title attribute.  

*/
	
function niceTitlesOnTables(){ 
	var myNiceTitles = new NiceTitles("<p class=\"titletext\">attr(nicetitle)</p>", 300, 50, -90, -45); 
	myNiceTitles.addElements(document.getElementsByTagName("td"), "title"); 
	} 

addEvent(window, "load", niceTitlesOnTables);