var multiSelectObjects = {}
//get mulitselect options and add to a new div, also create a new button to display
//selected options and toggle the new options div. Also hide the current select.
function multiSelect(selectElem) {
    
    var selectObject = {select: selectElem};
    var options = selectElem.getElementsByTagName('option');
    var multi = $("<div class='ld-multi-select'>");
    multi.css("max-width", selectObject.width);
    var button = selectObject.button = $("<span class='btn-select'>");
    button.on('click', function(event) {toggleOptionsPanel(selectObject);});
    var display = selectObject.display = $("<span class='placeholder'>");
    button.append(display);
    var optPanel = selectObject.optPanel = $("<div class='option-panel' tabindex=-1>");
    optPanel.on('blur', function() { //need to remove button click tempurally
        button.off('click');
        selectObject.optPanel.removeClass('show-selection');
        $('body').removeClass('ld-select-on');
        setTimeout(function() {
            button.on('click', function(event) {toggleOptionsPanel(selectObject);});
        }, 100)
        
    })
    var listItem = selectObject.listItem =$("<ul class='option-group'>");
    optPanel.append(listItem);
    optPanel.on('mousedown', function(event) {
        //if scrollbar
        var target = (event.target) ? event.target : event.srcElement //IE8
        if ($(target).is('li')) {
            startSelectGroup(selectObject, event)
            listItem.off('scroll')
        } 
    })
    listItem.on('scroll', function(event){
        turnOffSelectEvents(selectObject);
    })
    for (var i = 0; i < options.length; i++) {
        var label = $("<li class='option' value="+ options[i].value +
                      ">" + options[i].textContent + "</li>");
        label.on('mousedown', function(event) {
            optionSelectStart(selectObject, event)
        });
        listItem.append(label);
    }
    multi.append(button);
    multi.append(optPanel);
    $(selectElem).after(multi);
    if ($('.ld-select-mask').length == 0) {
        $('body').append('<div class="ld-select-mask">');
    }
    $('.ld-select-mask').css('top', button.offset().top)
    selectElem.style.display = "none";
    updateDisplay(selectObject);
    multiSelectObjects[selectElem.getAttribute("name")] = selectObject
}
function optionSelectStart(selectObject, event) {
    if (!event.ctrlKey) {
        selectObject.optPanel.find('li').each(function(index, element) {
        var elementObj = $(element)
        resetOption(elementObj);
        $(event.currentTarget).addClass('locked selected');
        })
    } else {
        $(event.currentTarget).toggleClass('selected');
        selectObject.optPanel.find('li.selected').each(function(index, element) {
            $(element).addClass('locked');
        });
    }
}
function resetOption(elementObj) {
    elementObj.removeClass('locked');
    elementObj.removeClass('selected');
    elementObj.off('mouseenter', addSelectClass);
    elementObj.off('mouseleave', removeSelectClass);
}
function toggleOptionsPanel(selectObject) {
    if (selectObject.optPanel.hasClass('show-selection')) {
        selectObject.optPanel.removeClass('show-selection')
        $('body').removeClass('ld-select-on');
        return
    }
    $('body').addClass('ld-select-on');
    $('.ld-select-mask').css('top', selectObject.button.offset().top + selectObject.button.height())
    selectObject.optPanel.addClass('show-selection');
    selectObject.optPanel.find('li').each(function(index, element) {
        var elementObj = $(element);
        resetOption(elementObj);
        selectedItems = $(selectObject.select).find("[value='" + element.value + "']:selected")
        if (selectedItems.length > 0) {
            elementObj.addClass('selected');
        }
    })
    selectObject.optPanel.focus();
}
function startSelectGroup(selectObject, event){
    selectObject.optPanel.on('mouseup', function(){ endSelectGroup(selectObject)})
    selectObject.optPanel.on('mouseleave', function(){
        $(document).on('mouseup', function(){
            clearBrowserSelection();
            endSelectGroup(selectObject)});
    })
    setSelectGroup(selectObject);
    var position = event.clientY;
    var diff = null;
    selectObject.optPanel.off('mousemove');
    selectObject.optPanel.on('mousemove', function(event) {
        if (diff != null && Math.abs(diff) > Math.abs(event.clientY - position)) { //reverse
            setSelectGroup(selectObject);
        }
        diff = event.clientY - position;
    })
}

function setSelectGroup(selectObject) {
    selectObject.optPanel.find('li').each(function(index, element) {
        if ($(element).hasClass('locked')) {
            return
        }
        if ($(element).hasClass('selected')) {
            $(element).off('mouseenter', addSelectClass);
            $(element).on('mouseleave', removeSelectClass);
        } else {
            $(element).on('mouseenter', addSelectClass);
            $(element).off('mouseleave', removeSelectClass);
        }
    })
}
function endSelectGroup(selectObject) {
    var selected = [];
    selectObject.optPanel.find('li').each(function(index, element) {
        if ($(element).hasClass("selected")) {
            selected.push(element.value);
        }
    })
    $(selectObject.select).val(selected);
    turnOffSelectEvents(selectObject);
    selectObject.optPanel.removeClass('show-selection');
    $('body').removeClass('ld-select-on');
    updateDisplay(selectObject);
    selectObject.select.onchange();
}
function turnOffSelectEvents(selectObject) {
    selectObject.optPanel.off("mouseup");
    selectObject.optPanel.off("mousemove");
    selectObject.optPanel.off("mouseleave");
    $(document).off("mouseup");
    selectObject.optPanel.find('li').each(function(index, element) {
        var elementObj = $(element);
        elementObj.off('mouseenter', addSelectClass);
        elementObj.off('mouseleave', removeSelectClass);
    })
}
function updateDisplay(selectObject) {
    var count = $(selectObject.select).find("option").length;
    var selectedCount = $(selectObject.select).find(":selected").length;
    var title = $(selectObject.select).attr("title");
    selectObject.display.text(title + "  " + selectedCount + "/" + count);
}
function addSelectClass(event) {
    $(event.currentTarget).addClass('selected');
}
function removeSelectClass(event) {
    $(event.currentTarget).removeClass('selected');
}
//cross browser clear selection function
function clearBrowserSelection() {
    if (window.getSelection) {
        if (window.getSelection().empty) {//chrome
            window.getSelection().empty();
        } else if (window.getSelection().removeAllRanges) {//firefox
          window.getSelection().removeAllRanges();(window.getSelection().empty)    
        }
    } else if (document.selection && document.selection.empty()) {//ie
        document.selection.empty();
    }
}