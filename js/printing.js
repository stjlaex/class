
function openFileExport(){
	printWindow = window.open('','','height=250,width=450,dependent,left=170');
	printWindow.document.open();
	printWindow.document.writeln("<html>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<script type='text/javascript'>function actionpage(){document.location='scripts/export.php'}</script>");
	printWindow.document.writeln("<body onLoad=\"setTimeout('actionpage()', 5000);\">");
	printWindow.document.writeln("<h3>The CSV file will download shortly.<h2>");
	printWindow.document.writeln("<h4>Open using your favourtie Spreadsheet.<h4>");
	printWindow.document.writeln("<form><input type='button' value='Close This Window' onClick='window.close();' /></form>");
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();
	}

function xulsave(){
        path = document.location.toString().slice(8).replace(/\//g,"\\");
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		var content = document.getElementById("content").innerHTML;
        data = "<html>" + content + "</html>";
        var file = Components.classes["@mozilla.org/file/local;1"].createInstance(Components.interfaces.nsILocalFile);
        file.initWithPath(path);
        if (file.exists()) { file.remove(true); }
        file.create(file.NORMAL_FILE_TYPE, 0666);
        var outputStream = Components.classes[ "@mozilla.org/network/file-output-stream;1" ].createInstance( Components.interfaces.nsIFileOutputStream );
        //outputStream.init( file, 0x04 | 0x08, 420, 0 );
        outputStream.init( file, 2, 0x200, false);
      var result = outputStream.write( data, data.length );
        outputStream.close();
      }


// A function taylored to process xml and an xsl-template in a new window for printing
function openPrintReport(contentId, xsltName){
	if (document.getElementById(contentId)){
		var content = document.getElementById(contentId).innerHTML;

		printWindow = window.open('','','height=800,width=750,dependent,resizable,menubar,left=170,scrollbars');
		printWindow.document.open();
		printWindow.document.writeln("<html>");

		printWindow.document.writeln("<head>");
		printWindow.document.writeln("<link rel='stylesheet' type='text/css' href='../templates/"+xsltName+".css' media='all' title='ReportBook Output' />");	
		printWindow.document.writeln("<link rel='stylesheet' type='text/xsl' href='../templates/"+xsltName+".xsl' media='all' title='ReportBook Output' />");	
		printWindow.document.writeln("<script language='JavaScript' type='text/javascript' src='js/printing.js'></script>");
		printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
		printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
		printWindow.document.writeln("</head>");

		printWindow.document.writeln("<body onLoad=\"process('xmlStudent','"+xsltName+"')\">");
		printWindow.document.writeln("<div id='xmlStudent'>"+content+"</div>");
		printWindow.document.writeln("</body>");

		printWindow.document.writeln("</html>");
		printWindow.document.close();
		}
	}

function process(contentId, xsltName){ 
	var xslRef;
	var xsltProcessor = new XSLTProcessor();
	var myDOM;
	if (document.getElementById(contentId)){

 		var xmlRef=document.implementation.createDocument("", "", null);
  		var myNode=document.getElementById(contentId);
  		var clonedNode=xmlRef.importNode(myNode, true);

  		xmlRef.appendChild(clonedNode); 

  		var myXMLHTTPRequest = new XMLHttpRequest();
  		myXMLHTTPRequest.open("GET", "../templates/"+xsltName+".xsl", false);
  		myXMLHTTPRequest.send(null);

		xslRef=myXMLHTTPRequest.responseXML;
  		xsltProcessor.importStylesheet(xslRef);

		var fragment=xsltProcessor.transformToFragment(xmlRef, document);

		document.getElementById(contentId).innerHTML = "";

		myDOM=fragment;

		document.getElementById(contentId).appendChild(fragment);
		}
	else{}
	}	

// turns xml into a single string
function serializeXML (xmlDocument) {
  var xmlSerializer;
  try {
    xmlSerializer = new XMLSerializer();
    return xmlSerializer.serializeToString(xmlDocument);
  }
  catch (e) {
//    output("");
    return "Can't serialize XML document.";
  }
}
