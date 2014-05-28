var ldUiObjects=(function(){
    var elements=[];
    //get mulitselect options and add to a new div, also create a new button to display
    //selected options and toggle the new options div. Also hide the current select.
    function multiSelect(selectElem){
        var selectObject={select: selectElem};
        var options=selectElem.getElementsByTagName('option');
        var multi=$("<div class='ld-multi-select'>");
        //multi.css("max-width", selectObject.width);
        var button=selectObject.button=$("<span class='btn-select'>");
        var display=selectObject.display=$("<span class='display'>");
        button.append(display);
        multi.append(button);
        var optPanel=selectObject.optPanel=$("<div class='option-panel' tabindex=-1>");
        var listItem=selectObject.listItem=$("<ul class='option-group'>");
        optPanel.append(listItem);        
        for (var i=0;i<options.length;i++){
            var label=$("<li class='option' value="+ options[i].value +
                          ">" + options[i].textContent + "</li>");
            label.on('mousedown', {selectObject: selectObject}, optionSelect);
            listItem.append(label);
        }        
        multi.append(optPanel);
        $(selectElem).after(multi);
        selectElem.style.display="none";
        button.on('click', {selectObject: selectObject}, toggleOptionsPanel);
        optPanel.on('mousedown', function(event){
            //if scrollbar
            var target = (event.target) ? event.target : event.srcElement //IE8
            if ($(target).is('li')) {
                selectObject.startIndex=selectObject.endIndex=$(target).index();
                startSelectGroup(selectObject, event);
                listItem.off('scroll');
                listItem.on('scroll', {selectObject: selectObject}, scrollToView)
            }
        })
        //add touch
        listItem.on('scroll', function(event){
            turnOffSelectEvents(selectObject);
        })
        optPanel.on('blur', function() { //need to remove button click temporally
            button.off('click');
            selectObject.optPanel.removeClass('show-selection');
            $('body').removeClass('ld-select-on');
            setTimeout(function() {
                button.on('click', {selectObject: selectObject}, toggleOptionsPanel);
            }, 100)
            
        })
        
        if ($('.ld-select-mask').length==0) {
            $('body').append('<div class="ld-select-mask">');
        }
        $('.ld-select-mask').css('top', button.offset().top)
        updateDisplay(selectObject);
        elements[selectElem.getAttribute("name")]=selectObject;
    }
    function toggleOptionsPanel(event) {
        var selectObject = event.data.selectObject;
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
        var position = event.clientY;
        var diff = null;
        selectObject.startIndex=$(event.target).index()
        selectObject.optPanel.off('mousemove');
        selectObject.optPanel.on('mousemove', function(event) {
            selectObject.endIndex=$(event.target).index()
            updateOptionSelect(selectObject)
        })
        selectObject.optPanel.on('mouseup', function(event){
            selectObject.endIndex=$(event.target).index();
            endSelectGroup(event, selectObject);
        })
        selectObject.optPanel.on('mouseleave', function(event){
            selectObject.endIndex=$(event.target).index();
            $(document).on('mouseup', function(){
                clearBrowserSelection();
                endSelectGroup(event, selectObject)});
        })
    }
    function optionSelect(event) {
        var selectObject = event.data.selectObject
        if (!event.ctrlKey) {
            selectObject.optPanel.find('li').each(function(index, element) {
            var elementObj = $(element)
            resetOption(elementObj);
            $(event.currentTarget).addClass('locked selected');
            })
        } else {
            $(event.currentTarget).toggleClass('selected');
            $(event.currentTarget).addClass('locked');
            selectObject.optPanel.find('li.selected').each(function(index, element) {
                $(element).addClass('locked');
            });
        }
    }
    function updateOptionSelect(selectObject){
        var min = Math.min(selectObject.startIndex, selectObject.endIndex);
        var max = Math.max(selectObject.startIndex, selectObject.endIndex);
        selectObject.optPanel.find('li').each(function(index, element) {
            if ($(element).hasClass('locked')) {
                return
            }
            if (index>=min && index<=max) {
                $(element).addClass("selected");
            } else {
                $(element).removeClass("selected");
            }
        })
    }
    function endSelectGroup(event, selectObject){
        var selected=[];
        var min = Math.min(selectObject.startIndex, selectObject.endIndex);
        var max = Math.max(selectObject.startIndex, selectObject.endIndex);
        selectObject.optPanel.find('li').each(function(index, element){
            if ($(element).hasClass('locked')) {
                if ($(element).hasClass('selected')){
                    selected.push(element.value);
                }
            } else if (index>=min&&index<=max){
                $(element).addClass("selected");
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
        delete selectObject.startIndex;
        delete selectObject.endIndex;
        delete selectObject.currentScroll;
        //selectObject.optPanel.find('li').each(function(index, element) {
          //  var elementObj = $(element);
        //})
    }
    function scrollToView(event){ 
        var selectObject = event.data.selectObject
        scrollDown=false;
        if(event.currentTarget.scrollTop>selectObject.currentScroll) {
            scrollDown=true;
        }
        selectObject.currentScroll=event.currentTarget.scrollTop
        var position
        selectObject.optPanel.find('li').each(function(index, element){
            var top=$(element).position().top;
            var height=$(event.currentTarget).height()+$(event.currentTarget).offset().top
            if(scrollDown){
                if (top<=height){
                    position=index
                }
                if (top>height){
                    return false;
                }
            }else{
                if (top>0){
                    position=index
                    return false;
                }
            }
        })
        selectObject.endIndex=position;
        updateOptionSelect(selectObject)
    }
    function updateDisplay(selectObject) {
        var count = $(selectObject.select).find("option").length;
        var selectedCount = $(selectObject.select).find(":selected").length;
        var title = $(selectObject.select).attr("title");
        selectObject.display.text(title + "  " + selectedCount + "/" + count);
    }
    function resetOption(elementObj) {
        elementObj.removeClass('locked');
        elementObj.removeClass('selected');
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
    return{
        elements: elements,
        multiSelect: multiSelect,
        updateDisplay: updateDisplay
    };
})();