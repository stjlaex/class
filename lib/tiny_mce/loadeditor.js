function loadEditor(){
	var formObject;
	for(i=0;document.forms.length;i++){
		formObject=document.forms[i];
		//alert(i+formObject);
		for(c=0;c<formObject.elements.length;c++){
			elementObject=formObject.elements[c];
			if(elementObject.className.indexOf("htmleditorarea")!=-1){
				/*prepares textareas with tinyMCE */
				  tinyMCE.init({
					mode : "specific_textareas",
					editor_selector : "htmleditorarea",
					editor_deselector : "texteditor",
					theme : "advanced",
					  theme_advanced_buttons1 : "bold,italic,underline,bullist,separator,undo,redo,spellchecker,pastetext,pasteword,selectall,code,link", 
					  theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					plugins : 'inlinepopups,spellchecker,paste',
					paste_auto_cleanup_on_paste : true,
				    paste_retain_style_properties : '',

	                setup : function (ed){
                  	  ed.onKeyDown.add(
                  	  function (ed, evt){
						  var lenid='textlen'+tinyMCE.activeEditor.id;
						  var maxlenid='maxtextlen'+tinyMCE.activeEditor.id;
                      	  var char_counter=document.getElementById(maxlenid).value;
						  var count=tinyMCE.activeEditor.getContent().replace(/<[^>]+>/g, '').length;
                      	  if(char_counter==0){
                    		  document.getElementById(lenid).value = count;
							}
						  else{
                    		  document.getElementById(lenid).value = char_counter - count;
							}
                      	  if(document.getElementById(lenid).value<=0){
                      	    }
						  else{
                        	  char_overflow_flag=0;
							}
                      	  if(document.getElementById(lenid).value<=0){
                        	  if(char_overflow_flag<3) {
                          	      alert("Comment should not be longer than " + char_counter + " characters");
                            	  char_overflow_flag++;
                          	  }
                      	  }
                  	    });
					  }

					});

				  tinyMCE.init({
					mode : "specific_textareas",
					editor_selector : "subeditorarea",
					editor_deselector : "htmleditorarea",
					theme : "advanced",
					  theme_advanced_buttons1 : "bold,italic,underline,bullist,separator,undo,redo,spellchecker,pastetext,pasteword,selectall,code,link", 
					  theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					plugins : 'inlinepopups,spellchecker,paste',
					paste_auto_cleanup_on_paste : true,
					  paste_retain_style_properties : '',

	                setup : function (ed){
                  	  ed.onKeyDown.add(
                  	  function (ed, evt){
						  var lenid='textlen'+tinyMCE.activeEditor.id;
						  var maxlenid='maxtextlen'+tinyMCE.activeEditor.id;
                      	  var char_counter=document.getElementById(maxlenid).value;
						  var count=tinyMCE.activeEditor.getContent().replace(/<[^>]+>/g, '').length;
                      	  if(char_counter==0){
                    		  document.getElementById(lenid).value = count;
							}
						  else{
                    		  document.getElementById(lenid).value = char_counter - count;
							}
                      	  if(document.getElementById(lenid).value<=0){
                      	    }
						  else{
                        	  char_overflow_flag="0";
							}
                      	  if(document.getElementById(lenid).value<=0){
                        	  if(char_overflow_flag=="0") {
                          	      alert("Comment should not be longer than " + char_counter + " characters");
                            	  char_overflow_flag="1";
                          	  }
                      	  }
                  	    });
					  }

					});
				}
			}
		}
	}
