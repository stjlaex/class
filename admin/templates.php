<?php 
/**									templates.php
 *
 *
 */
$action='templates_tags.php';
$choice='templates.php';

	$tags=getTags(true,'default',array('student_id'=>2,'guardian_id'=>2,'user_id'=>2));
?>


<link rel="stylesheet" href="../../codemirror/lib/codemirror.css">
<style>
	.CodeMirror{width:49%;height:395px;float:left;}
      .cm-mustache {color: #f57900;font-weight:bold;}
      .colors:after{content:' ';width:35px;height:8px;display: inline-block;padding:1%;margin-left:13px;}
</style>

<script src="../../codemirror/lib/codemirror.js"></script>
<script src="../../codemirror/mode/smartymixed.js"></script>
<script src="../../codemirror/mode/xml.js"></script>
<script src="../../codemirror/mode/overlay.js"></script>
<script src="../../codemirror/mode/javascript.js"></script>
<script src="../../codemirror/mode/css.js"></script>
<script src="../../codemirror/mode/htmlmixed.js"></script>
<script src="../../codemirror/mode/smarty.js"></script>

<script type="text/javascript" src="js/jscolor/jscolor.js"></script>


<?php

$extrabuttons['save']=array('name'=>'current','value'=>'templates_action.php');
two_buttonmenu($extrabuttons);

$templates=getTemplates();
?>
  <div style="margin-top:50px;">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />

		<fieldset class="center divgroup" id="viewcontent" style="background: none repeat scroll 0 0 #666666 !important;">
			<legend><?php print_string('tags',$book);?></legend>
			<label><?php print_string('infotag',$book);?></label>
			<select id="tags" onchange="process(this);">
				<option value=""></option>
				<?php
					list_tags_name($tags);
				?>
			</select>
			<label style="float:none"><?php print_string('insertobject',$book);?></label>
			<select id="htmltags" onchange="process(this);" style="float:none">
				<option value=""></option>
				<option value="img"><?php print_string('image',$book);?></option>
				<option value="div"><?php print_string('div',$book);?></option>
				<option value="span"><?php print_string('span',$book);?></option>
				<option value="table"><?php print_string('table',$book);?></option>
				<option value="link"><?php print_string('link',$book);?></option>
				<option value="strong"><?php print_string('strong',$book);?></option>
				<option value="em"><?php print_string('emphasized',$book);?></option>
				<option value="u"><?php print_string('underlined',$book);?></option>
				<option value="p"><?php print_string('paragraph',$book);?></option>
				<option value="br"><?php print_string('linebreak',$book);?></option>
			</select>
			<label style="float:right"><?php print_string('color',$book);?></label>
			<input id="picker" class="color {hash:true}" style="float:right" onchange="process(this)">
			<button name="add" type="submit"><?php print_string('tags',$book);?></button>
		</fieldset>
		<br>
		<fieldset  style="background: none repeat scroll 0 0 #666666 !important;">
			<legend><?php print_string('template',$book);?></legend>
			<label style="color:#FFFFEE !important"><?php print_string('templatename',$book);?></label>
			<input type="text" id="template_name" name="template_name">
			<div style="float:right"><label style="color:#FFFFEE !important"><?php print_string('templates',$book);?></label>
			<select id="templates" onchange="process(this)">
				<option> </option>
<?php
				foreach($templates as $template){
					echo "<option value='".$template['comment']."'>".$template['name']."</option>";
					}
?>
			</select></div><br><br>
			<textarea id="code" name='content' style="width:49%;float:left" rows="30"></textarea>
			<div id="preview" style="float:right;width:49%;background:#ffffff !important;height:395px;"></div>
		</fieldset>
		

	</form>
  </div>

	<script>
		/*Mustache highlight*/
		CodeMirror.defineMode("mustache", function(config, parserConfig) {
		  var mustacheOverlay = {
		    token: function(stream, state) {
			 var ch;
			 if (stream.match("{{")) {
			   while ((ch = stream.next()) != null)
				if (ch == "}" && stream.next() == "}") break;
			   stream.eat("}");
			   return "mustache";
			 }
			 while (stream.next() != null && !stream.match("{{", false)) {}
			 return null;
		    }
		  };
		  return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "htmlmixed"), mustacheOverlay);
		});

		/*Opens the code editor within the text area*/
		var myCodeMirror = CodeMirror.fromTextArea(document.getElementById("code"), {
				mode: "mustache",
				lineNumbers: true,
				lineWrapping: true
			});

		/*On change event the preview is updated*/
		myCodeMirror.on('change',function(myCodeMirror, change) {createFrame()});

		/*Objects handling*/
		function process(elem){
			if(elem.value=='img'){
				var link=prompt('Please enter the image location','http://www.learningdata.ie/wp-content/uploads/learning-data-logo1.png');
				myCodeMirror.replaceRange('<img src=\''+link+'\'>', myCodeMirror.getCursor());
				}
			else if(elem.value=='link'){
				var link=prompt('Please enter link','http://learningdata.ie');
				var title=prompt('Please enter Title','My Website');
				myCodeMirror.replaceRange('<a href=\''+link+'\'>'+title+'</a>', myCodeMirror.getCursor());
				}
			else if(elem.value=='div' || elem.value=='span' || elem.value=='p'
						|| elem.value=='strong' || elem.value=='em' || elem.value=='u'){
				myCodeMirror.replaceRange('<'+elem.value+'>Insert content here</'+elem.value+'>', myCodeMirror.getCursor());
				}
			else if(elem.value=='br'){
				myCodeMirror.replaceRange('<'+elem.value+'>', myCodeMirror.getCursor());
				}
			else if(elem.value=='table'){
				var rows=prompt('Please enter number of rows','2');
				var cols=prompt('Please enter number of columns','2');
				tableElem='<table>';
				for(var i=0;i<rows;i++){
					rowElem='\n\t<tr>';
					for(var j=0;j<cols;j++){
						colElem='\n\t\t<td>';
						colElem+='R'+(i+1)+'C'+(j+1);
						colElem+='</td>';
						rowElem+=colElem;
						}
					rowElem+='\n\t</tr>';
					tableElem+=rowElem;
					}
				tableElem+='\n</table>';
				myCodeMirror.replaceRange(tableElem, myCodeMirror.getCursor());
				}
			else if(elem.id=='colors' || elem.id=='picker' || elem.id=='templates'){
				myCodeMirror.replaceRange(elem.value, myCodeMirror.getCursor());
				if(elem.id=='templates'){document.getElementById('template_name').value=elem.options[elem.selectedIndex].text;}
				}
			else{
				if(elem.value!=''){myCodeMirror.replaceRange('{{'+elem.value+'}}', myCodeMirror.getCursor());}
				}
			elem.value='';
			}

			/*Replaces tags with values for preview*/
			String.prototype.strtr = function (replacePairs) {
				"use strict";
				var str=this.toString(),key,re;
				for(key in replacePairs){
					if(replacePairs.hasOwnProperty(key)) {
						re=new RegExp(escapeRegExp(key),"g");
						str=str.replace(re,replacePairs[key]);
						}
					}
				return str;
				}

			/*Escapes the tag names*/
			function escapeRegExp(str) {
				return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|\:\;]/g, "\\$&");
				}

			/*Creates a frames for the preview*/
			function createFrame(){
				<?php $js_array=json_encode($tags);?>
				var jArray=<?php echo json_encode($js_array); ?>;
				var iframe=document.createElement('iframe');
				var code=myCodeMirror.getValue();
				var prediv=document.getElementById('preview');
				iframe.style.cssText='width:100%;height:395px;';
				prediv.innerHTML='';
				console.log(jArray);
				code=code.strtr($.parseJSON(jArray));
				code=code.strtr($.parseJSON(jArray));
				prediv.appendChild(iframe);
				iframe.contentWindow.document.open();
				iframe.contentWindow.document.write(code);
				iframe.contentWindow.document.close();
				document.getElementById('code').value=code;
				}
			
	</script>
