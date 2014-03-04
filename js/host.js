$(document).ready(function() {
   $(window).resize(function() {
     //resizeFrame();
     });
  });

function resizeFrame(height,top,book){
     $('#view' + book).css('height', height);
     $('#view' + book).css('top', top);
     }

//--------------------------------------------------------
//functions for the marktable to display columns from gradebox

// array for the gradechoice box - (mid, style.display, select)
var marks = new Array();

// called whenever the marktable is reloaded to check for changes
// and adjust the marks array accordingly
// state=0 means no change
// state=-1 means a change
// state=mid where mid is the value of the new mark column to display
function updateMarkDisplay(state) {
    var theBook = window.frames["viewmarkbook"].document;
    var selMarks = document.getElementById('mids');
    if (!theBook.getElementById("sidtable")) {
        //marks not displayed
        selMarks.style.display = "none";
        return;
    } else if (state != 0) {
        //the mark selection box has changed need to
        //keep the state of any of the previous marks the same
        for (var markno = 0; markno < marks.length; markno++) {
            if (marks[markno][2] == "selected") {
                var mid = marks[markno][0];
                if (document.getElementById("sel-" + mid)) {
                    document.getElementById("sel-" + mid).selected = "selected";
                }
            }
        }
        if (document.getElementById("sel-" + state)) {
            //if state is set the mid of a newly created mark then display it
            document.getElementById("sel-" + state).selected = "selected";
        }
        marks.length = 0;
        //blank old marks array first
        changeMarkDisplay();
    } else {
        //no change
        markDisplay();
    }
}

// takes the display state from the selected values in the options
// and stores in the marks array (calls markDisplay to apply them)
function changeMarkDisplay() {
    var i = 0;
    var theBook = window.frames["viewmarkbook"].document;
    var selMarks = document.getElementById('mids');
    for (var c = 0; c < selMarks.options.length; c++) {
        if (selMarks.options[c].selected) {
            marks[c] = Array(selMarks.options[c].value, "table-cell", "selected");
        } else {
            marks[c] = Array(selMarks.options[c].value, "none", "");
        }
    }
    markDisplay();
}

// takes the selected state from those stored in the marks array
// and applies them to the marktable
function markDisplay() {
    var theBook = window.frames["viewmarkbook"].document;
    var selMarks = document.getElementById('mids');

    var theRows = theBook.getElementsByTagName("tr");
    var i = 0;
    var sids = new Array();
    for (var c = 0; c < theRows.length; c++) {
        if (theRows[c].attributes.getNamedItem("id")) {
            var rowId = escape(theRows[c].attributes["id"].value);
            sids[i] = rowId.substring(4, rowId.length);
            //sids[i] = theRows[c].attributes.getNamedItem("id").value;
            i++;
        }
    }
    for (var markno = 0; markno < marks.length; markno++) {
        var mark = marks[markno];
        theBook.getElementById(mark[0]).style.display = mark[1];
        selMarks.options[markno].selected = mark[2];
        var i = 0;
        while (( sid = sids[i++])) {
            theBook.getElementById(sid + "-" + mark[0]).style.display = mark[1];
        }
    }
}

//--------------------------------------------------------
//  the scripts for the userinterface - handles the selery menu in the bookoptions
//  and grows selery buttons

function seleryCheckKey(event, formObj) {
    if (event.keyCode == 13) {
        formObj.submit();
    }
}

function selerySubmit(liObj) {
    liObj.getElementsByTagName("input")[0].setAttribute("checked", "checked");
    //assumes the form to be the direct parent of the fieldset containing the selery ul
    liObj.parentNode.parentNode.parentNode.submit();
}

function selerySelectSubmit(selectObj) {
    selectObj.form.submit();
}

function seleryGrow(buttonObj, limit) {
    var start = buttonObj.value;
    var end = ++start;
    if (end > limit) {
        end = 0;
    }
    buttonObj.value = end;
    buttonObj.parentNode.getElementsByTagName("input")[0].value = end;
}

function selerySwitch(servantclass, fieldvalue, bookname) {
    switchedId = "switch" + servantclass;
    newfielddivId = "switch" + servantclass + fieldvalue;
    if (document.getElementById(newfielddivId)) {
        document.getElementById(switchedId).innerHTML = document.getElementById(newfielddivId).innerHTML;
    } else if (window.frames["view" + bookname].document.getElementById(newfielddivId)) {
        window.frames["view" + bookname].document.getElementById(switchedId).innerHTML = window.frames["view" + bookname].document.getElementById(newfielddivId).innerHTML;
    } else {
        window.frames["view" + bookname].document.getElementById(switchedId).innerHTML = '';
    }
}

//--------------------------------------------------------
//  the scripts for the userinterface - handles the Book Tabs and bookframe

//  only called when index is loaded or the LogIn button is hit
//  displays the cover or login page respectively
function loadLogin(page) {
    //window.frames["viewlogbook"].location.href="logbook/exit.php";
    //setTimeout(window.frames["viewlogbook"].location.href=page,200);
    window.frames["viewlogbook"].location.href = page;
    document.getElementById("viewlogbook").style.zIndex = "100";
    document.getElementById("viewlogbook").focus();
}

//  only called once after a new session has been started
//  flashscreen is the aboutbook followed after delay by markbook
function logInSuccess() {
    document.getElementById("navtabs").innerHTML = viewlogbook.document.getElementById("hiddennavtabs").innerHTML;
    document.getElementById("logbook").innerHTML = viewlogbook.document.getElementById("hiddenlogbook").innerHTML;
    document.getElementById("logbook").className = "loggedin";
    document.getElementById("loginlabel").innerHTML = viewlogbook.document.getElementById("hiddenloginlabel").innerHTML;
    document.getElementById("viewlogbook").innerHTML = "";
    document.getElementById("viewlogbook").style.zIndex = "-100";
    document.getElementById("logbookoptions").innerHTML = "";
    document.getElementById("logbookoptions").style.zIndex = "-100";
    viewBook("aboutbook");
}

//  only called when the LogOut button is hit
function logOut() {

    //if(window.frames["vieweportfolio"]){
    //	var epflogout=window.frames["vieweportfolio"].document.getElementById("eportfoliosite").getAttribute("logout");
    //	window.frames["vieweportfolio"].frames["externalbook"].location.href=epflogout;
    //	}

    window.frames["viewlogbook"].location.href = "logbook/exit.php";
}

//	Reloads the book without giving focus (never used for logbook!)
//	always called by logbook if a session is set,
//	also called when changes in one book needs to update another
function loadBook(book) {
    var currentbook = "";
    if (document.getElementById("currentbook")) {
        currentbook = document.getElementById("currentbook").getAttribute("class");
    }
    if (book == "") {
        book = currentbook;
    }
    if (book != "") {
        window.frames["view" + book].location.href = book + ".php";
    }
    if (book != currentbook) {
        document.getElementById("view" + book).style.zIndex = "-100";
        document.getElementById(book + "options").style.zIndex = "-100";
    }
    //window.frames["view"+book].history.forward();
}

function loadBookOptions(book) {
    document.getElementById(book + "options").innerHTML = window.frames["view" + book].document.getElementById("hiddenbookoptions").innerHTML;
}

function viewBook(newbook) {
    // hide the oldbook and tab first
    var oldbook = document.getElementById("currentbook").getAttribute("class");
    document.getElementById(oldbook + "options").style.zIndex = "-100";
    document.getElementById("view" + oldbook).style.zIndex = "-100";
    document.getElementById("currentbook").removeAttribute('id');
    // now bring the new tab and book to the top
    document.getElementById("view" + newbook).style.zIndex = "50";
    document.getElementById("view" + newbook).focus();
    document.getElementById(newbook + "options").style.zIndex = "60";
    document.getElementById(newbook + "tab").firstChild.setAttribute("id", "currentbook");
    // change the colour of the logbook's stripe to match
    document.getElementById("logbookstripe").setAttribute("class", newbook);
}

// A print function that handles pages designated as printable
function printGenericContent(iFrameName) {
    var printWindow;
    var contentToPrint = "";
    var currentbook = document.getElementById("currentbook").getAttribute("class");

    if (window.frames["view" + currentbook].document.getElementById("viewcontent")) {
        if (window.frames["view" + currentbook].document.getElementById("heading")) {
            contentToPrint = window.frames["view" + currentbook].document.getElementById("heading").innerHTML;
        }

        var contentDiv = window.frames["view" + currentbook].document.getElementById("viewcontent");

        var alinks = contentDiv.getElementsByTagName("a");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("href", "#");
            alinks[c].setAttribute("onclick", "return false");
        }
        var alinks = contentDiv.getElementsByTagName("input");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("onclick", "return false");
        }
        var alinks = contentDiv.getElementsByTagName("button");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("onclick", "return false");
        }
        var alinks = contentDiv.getElementsByTagName("select");
        for ( c = 0; c < alinks.length; c++) {
            alinks[c].setAttribute("disabled", "disabled");
        }

        contentToPrint = contentToPrint + contentDiv.innerHTML;
    } else {
        contentToPrint = "<h3>There is no printer friendly content on this page.</h3>";
    }
    printWindow = window.open("", "", "height=800,width=750,dependent,resizable,menubar,left=170,scrollbars");
    if (printWindow != null) {
        printWindow.document.write("<html><head><link rel='stylesheet' type='text/css' href='css/printstyle.css' /></head>");
        printWindow.document.write("<body><br />" + contentToPrint + "</body></html>");
        printWindow.document.close();
    }
}

// Keep the php session alive
function sessionAlive(pathtobook) {
    var url = pathtobook + "httpscripts/session_alive.php?uniqueid=1";
    var xmlHttp = false;
    requestxmlHttp();
    function requestxmlHttp() {
        try {
            xmlHttp = new XMLHttpRequest();
        } catch (failed) {
            xmlHttp = false;
        }
        if (!xmlHttp) {
            alert("Error initializing XMLHttpRequest!");
        }
    }


    xmlHttp.open("GET", url, true);
    xmlHttp.send(null);
}

//------------------------------------------------------
//
function tinyTabs(tabObject) {
    // the id of containing div (eg. area for statementbank)
    var tabmenuId = tabObject.parentNode.parentNode.parentNode.id;
    var chosentab = tabObject.getAttribute("class");
    var currentbook = document.getElementById("currentbook").getAttribute("class");

    window.frames["view" + currentbook].document.getElementById("current-tinytab").removeAttribute("id");
    window.frames["view" + currentbook].document.getElementById("tinytab-" + tabmenuId + "-" + chosentab).firstChild.setAttribute("id", "current-tinytab");
    var targetId = "tinytab-display-" + tabmenuId;
    var sourceId = "tinytab-xml-" + tabmenuId + "-" + chosentab;
    var fragment = window.frames["view" + currentbook].document.getElementById(sourceId).innerHTML;
    window.frames["view" + currentbook].document.getElementById(targetId).innerHTML = "";
    window.frames["view" + currentbook].document.getElementById(targetId).innerHTML = fragment;
    if (window.frames["view" + currentbook].document.getElementById("statementbank")) {
        //this must be running the statement bank
        filterStatements(subarea, ability);
    }
}


var previousPage="";
var previousPageScroll=0;

/**
 * adds the images and attributes to required input fields
 * inits the js-calendar elements and the tooltip titles
 */
function loadRequired(book) {
    var firstFocus;
    var formObject;
    var elementObject;
    var imageRequired;
    firstFocus = -1;
    for ( i = 0; i < window.frames["view" + book].document.forms.length; i++) {
        formObject = window.frames["view"+book].document.forms[i];
        for ( c = 0; c < formObject.elements.length; c++) {
            elementObject = formObject.elements[c];
            if (elementObject.className.indexOf("required") != -1) {
                if(elementObject.tagName!='SELECT'){elementObject.setAttribute("onChange", "validateRequired(this)");}
                else{elementObject.setAttribute("onChange", "validateSelectRequired(this)");}
                imageRequired = window.frames["view" + book].document.createElement("span");
                imageRequired.className = "required";
                elementObject.parentNode.insertBefore(imageRequired, elementObject);
            }
            if (elementObject.className.indexOf("eitheror") != -1) {
                elementObject.setAttribute('onChange', 'validateRequiredOr(this)');
                imageRequired = window.frames["view" + book].document.createElement("span");
                imageRequired.className = "required";
                elementObject.parentNode.insertBefore(imageRequired, elementObject);
            }
            if (elementObject.className.indexOf("switcher") != -1) {
                switcherId = elementObject.getAttribute("id");
                parent.selerySwitch(switcherId, elementObject.value, book);
                elementObject.setAttribute("onChange", "parent.selerySwitch('" + switcherId + "',this.value,'" + book + "')");
            }

            // add event handlers to the checkbox input elements
            if (elementObject.getAttribute("type") == "checkbox" && elementObject.name == "sids[]") {
                elementObject.onchange = function() {
                    window.frames["view" + book].checkrowIndicator(this)
                };
            }
            if (elementObject.getAttribute("type") == "radio" && elementObject.parentNode.tagName != "TH") {
                elementObject.parentNode.onclick = function() {
                    window.frames["view" + book].checkRadioIndicator(this)
                };
            }
            if (elementObject.getAttribute("tabindex") == "1" && firstFocus == "-1") {
                firstFocus = c;
            }
            if (elementObject.getAttribute("maxlength")) {
                var maxlength = elementObject.getAttribute("maxlength");
                if (maxlength > 180) {
                    elementObject.style.width = "80%";
                } else if (maxlength > 50) {
                    elementObject.style.width = "60%";
                } else if (maxlength < 20 && maxlength > 0) {
                    elementObject.style.width = maxlength + "em";
                }
            }
            if (elementObject.getAttribute("type") == "date") {
                var inputId = elementObject.getAttribute("id");
                window.frames["view" + book].Calendar.setup({
                    inputField : inputId,
                    ifFormat : "%Y-%m-%d",
                    button : "calendar-" + inputId
                });
            }
        }
    }
    /*load the first tiny-tab (if there is one)*/
    if (window.frames["view" + book].document.getElementById("current-tinytab")) {
        tinyTabs(window.frames["view" + book].document.getElementById("current-tinytab"));
    }

    /*prepares the span elements with title attributes for qtip*/
    if (window.frames["view" + book].tooltip) {
        window.frames["view" + book].tooltip.init();
    }

    /*prepares a sidtable if it is present*/
    if (window.frames["view" + book].document.getElementById("sidtable")) {
        window.frames["view" + book].sidtableInit();
    }

    if (window.frames["view" + book].document.getElementById("formdocumentdrop")) {
        window.frames["view" + book].documentdropInit();
    }

    /*give focus to the tab=1 form element if this is a form*/
    /*should always be last!*/
    if (i > 0) {
        if (firstFocus == -1) {
            firstFocus = 0;
        }
        if (window.frames["view"+book].document.forms[0].elements[firstFocus]) {
            window.frames["view"+book].document.forms[0].elements[firstFocus].focus();
        }
    }

    /*window.frames["view" + book].document.onscroll = temp1;
    function temp1() {
        //console.log('1', $(window.frames["view" + book]).scrollTop())
        //console.log(book)
        var bookframeHeight = $('#view' + book).height();
        if ($(window.frames["view" + book]).scrollTop() >  0) {
            $('#' + book + "options").slideToggle(300, function() {
                $('#' + book + "options").css("display", "none");
            });
            $('#view' + book).css('top', 80);
            if (book == 'infobook' || book == 'entrybook' || book == 'markbook') {
                var bheight = bookframeHeight + 180;
            } else {
                var bheight = bookframeHeight + 80;
            }
            $('#view' + book).css('height', bheight);

            window.frames["view" + book].document.onscroll = null
            //window.frames["view" + book].document.onscroll = temp
        }
    }

    function temp() {
        var bookframeHeight = $('#view' + book).height();
        console.log('0', $(window.frames["view" + book])[0].document.getElementById('bookbox').clientHeight)
        console.log($(window.frames["view" + book])[0].document.body.scrollHeight)
        if ($(window.frames["view" + book]).scrollTop() == 0 ) {
            $('#' + book + "options").slideToggle(300, function() {
                $('#' + book + "options").css("display", "block");
            });
            if (book == 'infobook' || book == 'entrybook' || book == 'markbook') {
                var btop = 260;
                var bheight = bookframeHeight - 180;
            } else {
                var btop = 170;
                var bheight = bookframeHeight - 80;
            }
            $('#view' + book).css('top', btop);
            $('#view' + book).css('height', bheight);

           window.frames["view" + book].document.onscroll = temp1
        }
    }*/

}
    var previousScroll = new Array();
    previousScroll[book] = 0;

    /*heights*/
    var contentsHeight = $('#view' + book).contents().find("#bookbox").outerHeight(true);
    var frameHeight = $('#view' + book).height();
    var menuHeight = $('#' + book + "options").outerHeight(true);
    var headerHeight = $('.booktabs').height();
    var windowHeight = $(window).outerHeight(true);

    var currentPage=$('#view' + book).contents().find("input[name='current']").val();
    if(previousPage==currentPage && book!="logbook" && previousPageScroll>0){
      $('#' + book + "options").css("display", "none");
      resizeFrame(windowHeight - headerHeight, headerHeight, book);
      $(window.frames["view" + book]).scrollTop(2);
      }
    

    /*default settings*/
    if(book!="logbook"){resizeFrame(windowHeight - headerHeight - menuHeight, menuHeight + headerHeight, book);}
    if($('#' + book + "options").css("display")=="none"){$('#' + book + "options").css("display", "block");}
    if(contentsHeight>=frameHeight && contentsHeight<=(frameHeight+menuHeight)){$('#view' + book).contents().find("#bookbox").css('padding-bottom',menuHeight);}

    /*on frame's scroll resize the frame*/
    $(window.frames["view" + book]).scroll(function() {
      var currentScroll = new Array();
      currentScroll[book] = $(this).scrollTop();
        if(currentScroll[book] == 0) {
         $('#' + book + "options").slideToggle(300, function() {
            $('#' + book + "options").css("display", "block");
            resizeFrame(windowHeight - headerHeight - menuHeight, menuHeight + headerHeight, book);
            previousPageScroll=0;
            });
           }
         else if (currentScroll[book] > previousScroll[book] && previousScroll[book]==0){
           $('#' + book + "options").slideToggle(300, function() {
              $('#' + book + "options").css("display", "none");
              resizeFrame(windowHeight - headerHeight, headerHeight, book);
              previousPage=currentPage;
              previousPageScroll=1;
            });
           }
         previousScroll[book] = currentScroll[book];
   });
}
