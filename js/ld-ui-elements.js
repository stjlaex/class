//get mulitselect options and add to a new div, also create a new button to display
//selected options and toggle the new options div. Also hide the current select.
function multiSelect(selectElem) {
    var selectObject = {select: selectElem}
    var options = selectElem.getElementsByTagName('option');
    var multi = $("<div class='ld-multi-select'>");
    multi.css("max-width", selectObject.width)
    var span = $("<span class='btn-select'>");
    span.on('click', function() {toggleOptions(selectObject)})
    var label = $("<span class='placeholder'>");
    span.append(label);
    var optPanel = selectObject.optPanel = $("<div class='option-panel'>");
    list =$("<ul class='option-group'>")
    optPanel.append(list)
    optPanel.on('mousedown', function(event){ startSelectGroup(selectObject, event)})
    for (var i = 0; i < options.length; i++){
        var label = $("<li class='option' value="+ options[i].value +
                      ">" + options[i].textContent + "</li>");
        label.on('mousedown', function(event) {
            $(event.currentTarget).addClass('clicked');
            addSelect(event);
        });
        list.append(label);
    }
    multi.append(span)
    multi.append(optPanel)
    $(selectElem).after(multi);
    selectElem.style.display = "none"
    
}

function toggleOptions(selectObject) {
    selectObject.optPanel.toggleClass('show-selection');
    selectObject.optPanel.find('li').each(function(index, element) {
        resetOption($(element));
        $(element).removeClass('selected');
    })
}
function resetOption(elementObj) {
    elementObj.removeClass('clicked');
    elementObj.removeClass('selected');
    elementObj.off('mouseenter', addSelect);
    elementObj.off('mouseleave', removeSelect);
}
function startSelectGroup(selectObject, event){
    event.preventDefault();
    selectObject.optPanel.on('mouseup', function(){ endSelectGroup(selectObject)})
    selectObject.optPanel.on('mouseleave', function(){
        $(document).on('mouseup', function(){endSelectGroup(selectObject)})
    })
    setSelectGroup(selectObject)
    var position = event.clientY
    var diff = null
    selectObject.optPanel.off('mousemove');
    selectObject.optPanel.on('mousemove', function(event) {
        if (diff != null && Math.abs(diff) > Math.abs(event.clientY - position)) { //reverse
            setSelectGroup(selectObject)
        }
        diff = event.clientY - position
    })
}

function setSelectGroup(selectObject) {
    selectObject.optPanel.find('li').each(function(index, element) {
        if ($(element).hasClass('clicked')) {
            return
        }
        if ($(element).hasClass('selected')) {
            $(element).off('mouseenter', addSelect);
            $(element).on('mouseleave', removeSelect);
        } else {
            $(element).on('mouseenter', addSelect);
            $(element).off('mouseleave', removeSelect);
        }
    })
}

function endSelectGroup(selectObject) {
    var selected = [];
    selectObject.optPanel.find('li').each(function(index, element) {
        if ($(element).hasClass("selected")) {
            selected.push(element.value);
        }
        $(element).off('mouseenter', addSelect);
    })
    $(selectObject.select).val(selected);
    selectObject.select.onchange();
    selectObject.optPanel.off("mouseup");
    selectObject.optPanel.off("mousemove");
    selectObject.optPanel.off("mouseleave");
    $(document).off("mouseup");
    toggleOptions(selectObject);
}
function addSelect(event) {
    $(event.currentTarget).addClass('selected')
}
function removeSelect(event) {
    $(event.currentTarget).removeClass('selected')
}