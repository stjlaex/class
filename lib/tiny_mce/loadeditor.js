function loadEditor(){
	var formObject;
	for(i=0;document.forms.length;i++){
		formObject=document.forms[i];
		//alert(i+formObject);
		for(c=0;c<formObject.elements.length;c++){
			elementObject=formObject.elements[c];
			if(elementObject.className.indexOf("htmleditorarea")!=-1){
			 /*prepares textareas with tinyMCE */
			 var selectors=new Array();
			 selectors['htmleditorarea']="texteditor";
			 selectors['subeditorarea']="htmleditorarea";
			 for(selector in selectors){
				tinyMCE.init({
					mode : "specific_textareas",
					editor_selector : selector,
					editor_deselector : selectors[selector],
					theme : "advanced",
					theme_advanced_buttons1 : "bold,italic,underline,bullist,separator,undo,redo,pastetext,pasteword,selectall,code,link", 
					theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					plugins : 'inlinepopups,paste',
					paste_auto_cleanup_on_paste : true,
				    paste_retain_style_properties : '',
					paste_preprocess : function(pl, o) {                                        
						var maxlenid='maxtextlen'+tinyMCE.activeEditor.id;
						o.content = nice_splice(o.content, document.getElementById(maxlenid).value);
						},                                                                      
					paste_postprocess : function(pl, o) {                                                                            
						o.content = o.node.innerHTML;                
						},        
	                setup : function (ed){
					  ed.onInit.add(function(ed, evt) {
						  var maxlenid='maxtextlen'+tinyMCE.activeEditor.id;
                      	  var char_counter=document.getElementById(maxlenid).value;
						  var lenid='textlen'+tinyMCE.activeEditor.id;
						  ed.getBody().setAttribute('spellcheck', true);
						  var content=tinyMCE.activeEditor.getContent().replace(/([&]nbsp[;])/gi,' ').replace(/<[^>]+>/g, '');
						  var count=content.replace(/&#?[a-zA-Z0-9]+;/g,' ').length;
                      	  if(char_counter==0){
                    		  document.getElementById(lenid).value = count;
							}
						  else{
                    		  document.getElementById(lenid).value = char_counter - count;
							}
					  	  char_overflow_flag=0;
						});
                  	  ed.onKeyUp.add(function (ed, evt){
						  var lenid='textlen'+tinyMCE.activeEditor.id;
						  var maxlenid='maxtextlen'+tinyMCE.activeEditor.id;
                      	  var char_counter=document.getElementById(maxlenid).value;
						  var content=tinyMCE.activeEditor.getContent().replace(/([&]nbsp[;])/gi,' ').replace(/<[^>]+>/g, '');
						  var count=content.replace(/&#?[a-zA-Z0-9]+;/g,' ').length;
                      	  if(char_counter==0){
                    		  document.getElementById(lenid).value = count;
							}
						  else{
                    		  document.getElementById(lenid).value = char_counter - count;
							}
                      	  if(document.getElementById(lenid).value<0){
                      	    }
						  else{
                        	  char_overflow_flag=0;
							}
                      	  if(document.getElementById(lenid).value<0){
                        	  if(char_overflow_flag<3) {
                          	      alert("Comment should not be longer than " + char_counter + " characters");
                            	  char_overflow_flag++;
                          	  	}
							  ed.setContent(nice_splice(content, document.getElementById(maxlenid).value));
							  ed.selection.select(ed.getBody(), true);
							  ed.selection.collapse(false);
                      	  	}
                  	    });
					  }

					});

				function nice_splice(str, max) {
					var tags = 0,
					entities = 0,
					sQuotes = 0,
					dQuotes = 0,
					char,
					count = 0,
					result = [];
					for (var i = 0, len = str.length; i < len; ++i) {
						char = str.charAt(i);
						switch(char) {
						case '<':
							if (!sQuotes && !dQuotes) {
								++tags;
								result.push(char);
								continue;
							}
							break;
						case '>':
							if (!sQuotes && !dQuotes) {
								--tags;
								result.push(char);
								continue;
							}
							break;
						case '&':
							if (!sQuotes && !dQuotes) {
								++entities;
								++count;
								if(count <= max) {
									result.push(char);
									}
								continue;
							}
							break;
						case ';':
							if (entities && !sQuotes && !dQuotes) {
								--entities;
								if(count <= max){
									result.push(char);
									}
								continue;
							}
							break;
						case "'":
							if (tags && !dQuotes)
								sQuotes = !sQuotes;
							break;
						case '"':
							if (tags && !sQuotes)
								dQuotes = !dQuotes;
							break;
						}
						if (tags || (entities && count <= max)) {
							result.push(char);
						} else {
							if (++count <= max)
								result.push(char);
						}
					}
					return result.join('');
				    }
			      }
				}
			}
		}
	}
