var ldUiObjects=(function(){
    var elements=[];
    var ctrlClick = false;
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
        selectObject.touchPanel=$("<div class='touch-panel'>")
        var touchEnd=selectObject.touchEnd=$("<button type='button' class='action'>Done</button>");
        var touchAll=selectObject.touchEnd=$("<button type='button'>All</button>");
        var touchClear=selectObject.touchEnd=$("<button type='button'>Clear</button>");
        selectObject.touchPanel.append(touchAll);
        selectObject.touchPanel.append(touchClear);
        selectObject.touchPanel.append(touchEnd);
        var listItem=selectObject.listItem=$("<ul class='option-group'>");
        optPanel.append(listItem);        
        for (var i=0;i<options.length;i++){
            var label=$("<li class='option' value="+ options[i].value +
                          ">" + options[i].textContent + "</li>");
            label.on('mousedown', {selectObject: selectObject}, optionSelect);
            //label.on('touchstart', {selectObject: selectObject}, touchSelect);
            listItem.append(label);
        }        
        multi.append(optPanel);
        $(selectElem).after(multi);
        selectElem.style.display="none";
        //mouseevents
        button.on('click', {selectObject: selectObject}, toggleOptionsPanel); 
        optPanel.on('mousedown', {selectObject:selectObject}, selectMouseDown);
        optPanel.on('keydown', function(event) {
          if (event.ctrlKey || event.metaKey) {
              ctrlClick = true
            }
        })
        listItem.on('scroll', function(event){
            turnOffSelectEvents(selectObject);
        })
        //touchevents
        button.on('touchstart', {selectObject: selectObject}, toggleTouchPanel); 
        optPanel.on('touchstart', function(event){
            optPanel.off('mousedown', selectMouseDown);
        })
        touchEnd.on('touchstart', function(event) {endSelectTouch(selectObject)});
        touchAll.on('touchstart', function(event) {
            selectObject.optPanel.find('li').addClass('selected');
            event.preventDefault();
        });
        touchClear.on('touchstart', function(event) {
            selectObject.optPanel.find('li').removeClass('selected');
            event.preventDefault();
        });
        optPanel.on('blur', function(event) { //need to remove button click temporally
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
    function selectMouseDown(event){
        //if scrollbar
        var selectObject=event.data.selectObject;
        var target = (event.target) ? event.target : event.srcElement //IE8
        if ($(target).is('li')) {
            selectObject.startIndex=selectObject.endIndex=$(target).index();
            console.log(target, $(target).index())
            startSelectGroup(selectObject, event);
            selectObject.listItem.off('scroll');
            selectObject.listItem.on('scroll', {selectObject: selectObject}, scrollToView)
        }
    }
    function toggleOptionsPanel(event) {
        var selectObject = event.data.selectObject;
        if (selectObject.optPanel.hasClass('show-selection')) {
            selectObject.optPanel.removeClass('show-selection')
            $('body').removeClass('ld-select-on');
            if (selectObject.touchPanel.parent()) {
                selectObject.touchPanel.detach();
                selectObject.optPanel.on('mousedown', {selectObject:selectObject}, selectMouseDown);
            }
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
            if (ctrlClick == true) {
              return  
            }
            if ($(event.target).is('li')) {
                selectObject.endIndex=$(event.target).index()
                updateOptionSelect(selectObject)
            }
        })
        selectObject.optPanel.off("keydown");
        selectObject.optPanel.off("keyup");
        selectObject.optPanel.on('keydown', function(event) {
          if (event.keyCode == 17 || event.keyCode == 91) {
              ctrlClick = true
            }
        })
        selectObject.optPanel.on('keyup', function(event) {
          ctrlClick = false
           var selected=[];
           selectObject.optPanel.find('li').each(function(index, element){
              if ($(element).hasClass('selected')){
                selected.push(element.value);
              }
           })
           $(selectObject.select).val(selected);
           turnOffSelectEvents(selectObject);
           selectObject.optPanel.removeClass('show-selection');
           $('body').removeClass('ld-select-on');
           updateDisplay(selectObject);
           selectObject.select.onchange();
        })
        selectObject.optPanel.on('mouseup', function(event){
            if (ctrlClick == true) {
              return  
            }
            if ($(event.target).is('li')) {
                selectObject.endIndex=$(event.target).index()
            }
            endSelectGroup(selectObject)
        })
        selectObject.optPanel.on('mouseleave', function(event){
            selectObject.endIndex=$(event.target).index();
            $(document).off('mouseup')
            $(document).on('mouseup', function(){
                clearBrowserSelection();
                endSelectGroup(selectObject)});
        })
    }
    function optionSelect(event) {
        var selectObject = event.data.selectObject
        if (selectObject.touchPanel.parent()){
            $(event.currentTarget).toggleClass('selected');
        } else if (ctrlClick) {
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
    function touchSelect(event) {
        var selectObject = event.data.selectObject
        $(event.currentTarget).toggleClass('selected');
    }
    function updateOptionSelect(selectObject){
        if (selectObject.startIndex == undefined || selectObject.endIndex == undefined) {
            return
        }
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
    function endSelectGroup(selectObject){
        var selected=[];
        var min = Math.min(selectObject.startIndex, selectObject.endIndex);
        var max = Math.max(selectObject.startIndex, selectObject.endIndex);
        console.log(max, min)
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
    function endSelectTouch(selectObject){
        var selected=[];
        selectObject.optPanel.find('li').each(function(index, element){
            if ($(element).hasClass('selected')){
                selected.push(element.value);
            }
        })
        $(selectObject.select).val(selected);
        selectObject.optPanel.removeClass('show-selection');
        selectObject.touchPanel.detach();
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
        delete selectObject.scrollDown;
    }
    function scrollToView(event){ 
        var selectObject = event.data.selectObject
        if(event.currentTarget.scrollTop>selectObject.currentScroll) {
            selectObject.scrollDown=true;
        } else if (event.currentTarget.scrollTop<selectObject.currentScroll) {
            selectObject.scrollDown=false;
        }
        selectObject.currentScroll=event.currentTarget.scrollTop
        var position
        selectObject.optPanel.find('li').each(function(index, element){
            var top=$(element).position().top;
            var bottom=top+$(element).height();
            var height=$(event.currentTarget).height()+$(event.currentTarget).offset().top
            if(selectObject.scrollDown){
                if (top<=height){
                    position=index
                }
                if (top>height){
                    return false;
                }
            }else{
                if (bottom>0){
                    position=index
                    return false;
                }
            }
        })
        console.log(position)
        selectObject.endIndex=position;
        updateOptionSelect(selectObject)
    }
    //touch
    function toggleTouchPanel(event){
        event.preventDefault();
        var selectObject=event.data.selectObject;
        if (selectObject.optPanel.find('li').length==0){
            return;
        }
        selectObject.optPanel.append(selectObject.touchPanel);
        toggleOptionsPanel(event);
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