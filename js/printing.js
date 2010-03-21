
function openFileExport(){
	printWindow=window.open('','','height=250,width=450,dependent');
	printWindow.document.open();
	printWindow.document.writeln("<html>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<script type='text/javascript'>function actionpage(){document.location='scripts/export.php?ftype=csv'}</script>");
	printWindow.document.writeln("<body onLoad=\"setTimeout('actionpage()', 5000);\">");
	printWindow.document.writeln("<h3>The CSV file will download shortly.<h2>");
	printWindow.document.writeln("<h4>Open using your favourtie Spreadsheet.<h4>");
	printWindow.document.writeln("<form><input type='button' value='Close This Window' onClick='window.close();' /></form>");
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();
	}

function openXMLExport(ftype){
	printWindow=window.open('','','height=250,width=450,dependent');
	printWindow.document.open();
	printWindow.document.writeln("<html>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<script type='text/javascript'>function actionpage(){document.location='scripts/export.php?ftype="+ftype+"'}</script>");
	printWindow.document.writeln("<body onLoad=\"setTimeout('actionpage()', 5000);\">");
	printWindow.document.writeln("<h3>The XML file will download shortly.<h2>");
	printWindow.document.writeln("<h4>Save to disk.<h4>");
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


function openChartReport(xml, xsltName, paper){
	var content="";
	if(xml!=""){
		content=serializeXML(xml);
		}

	if(paper=="landscape"){
		printWindow=window.open('','','height=600,width=900,dependent,resizable,menubar,screenX=50,scrollbars');
		}
	else{
		printWindow=window.open('','','height=800,width=750,dependent,resizable,menubar,screenX=50,scrollbars');
		}

	printWindow.document.open("text/html");
	printWindow.document.writeln("<html xmlns='http://www.w3.org/1999/xhtml'>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<link rel='stylesheet' type='text/css' href='../templates/"+xsltName+".css' media='all' title='ReportBook Output' />");
	printWindow.document.writeln("<script language='JavaScript' type='text/javascript' src='js/raphael.js' charset='utf-8'></script>");
	printWindow.document.writeln("<script language='JavaScript' type='text/javascript' src='../templates/"+xsltName+".js' charset='utf-8'></script>");
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<body onLoad=\""+xsltName+"();\">");
	printWindow.document.writeln(content);
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();
	}

/* Receives the result of an xsl transformation as xml and opens a
separate preview window to display.xsltName defines the css sheet to
apply and paper is either ladnscape or portrait.*/
function openPrintReport(xml, xsltName, paper){
	var content="";

	if(xml!=""){
		content=serializeXML(xml);
		}

	if(paper=="landscape"){
		printWindow=window.open('','','height=600,width=900,dependent,resizable,menubar,screenX=50,scrollbars');
		}
	else{
		printWindow=window.open('','','height=800,width=750,dependent,resizable,menubar,screenX=50,scrollbars');
		}

	printWindow.document.open("text/html");
	printWindow.document.writeln("<html xmlns='http://www.w3.org/1999/xhtml'>");
	printWindow.document.writeln("<head>");
	printWindow.document.writeln("<link rel='stylesheet' type='text/css' href='../templates/"+xsltName+".css' media='all' title='ReportBook Output' />");	
	printWindow.document.writeln("<meta http-equiv='pragma' content='no-cache'/>");
	printWindow.document.writeln("<meta http-equiv='Expires' content='0'/>");
	printWindow.document.writeln("</head>");
	printWindow.document.writeln("<body>");
	printWindow.document.writeln(content);
	printWindow.document.writeln("</body>");
	printWindow.document.writeln("</html>");
	printWindow.document.close();
		
	}



// turns xml into a single string
function serializeXML(xmlDocument){
  var xmlSerializer;
  try {
    xmlSerializer=new XMLSerializer();
    return xmlSerializer.serializeToString(xmlDocument);
  }
  catch (e) {
//    output("");
    return "Can't serialize XML document.";
  }
}
