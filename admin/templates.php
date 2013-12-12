<?php 
/**									templates.php
 *
 *
 */
$action='templates_tags.php';
$choice='templates.php';

$tags=getTags(true,'default',array('student_id'=>2,'guardian_id'=>2,'user_id'=>2));
?>

<link rel="stylesheet" href="lib/codemirror/lib/codemirror.css">
<style>
	.CodeMirror{width:47%;height:395px;float:left;}
	.cm-mustache {color: #f57900;font-weight:bold;}
</style>

<script src="lib/codemirror/lib/codemirror.js"></script>
<script src="lib/codemirror/mode/smartymixed.js"></script>
<script src="lib/codemirror/mode/xml.js"></script>
<script src="lib/codemirror/mode/overlay.js"></script>
<script src="lib/codemirror/mode/javascript.js"></script>
<script src="lib/codemirror/mode/css.js"></script>
<script src="lib/codemirror/mode/htmlmixed.js"></script>
<script src="lib/codemirror/mode/smarty.js"></script>

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
			<select id="tags" onchange="processObject(this);">
				<option value=""></option>
				<?php
					list_tags_name($tags);
				?>
			</select>
			<label style="float:none"><?php print_string('insertobject',$book);?></label>
			<select id="htmltags" onchange="processObject(this);" style="float:none">
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
			<input id="picker" class="color {hash:true}" style="float:right" onchange="processObject(this)">
			<button name="add" type="submit"><?php print_string('tags',$book);?></button>
		</fieldset>
		<br>
		<fieldset  style="background: none repeat scroll 0 0 #666666 !important;">
			<legend><?php print_string('template',$book);?></legend>
			<label style="color:#FFFFEE !important"><?php print_string('templatename',$book);?></label>
			<input type="text" id="template_name" name="template_name">
			<div style="float:right"><label style="color:#FFFFEE !important"><?php print_string('templates',$book);?></label>
			<select id="templates" onchange="processObject(this)">
				<option> </option>
<?php
				foreach($templates as $template){
					echo "<option value='".$template['comment']."'>".$template['name']."</option>";
					}
?>
			</select></div><br><br><br>
			<textarea id="code" name='content' style="width:47%;float:left" rows="30"></textarea>
			<div id="preview" style="float:right;width:47%;background:#ffffff !important;height:395px;"></div>
		</fieldset>
		

	</form>
  </div>

<script>
/*Mustache highlight*/
CodeMirror.defineMode("mustache",function(config,parserConfig) {
	var mustacheOverlay={
		token: function(stream,state) {
			var ch;
			if(stream.match("{{")){
				while((ch=stream.next())!=null)
				if(ch=="}" && stream.next()=="}") break;
				stream.eat("}");
				return "mustache";
				}
			while(stream.next()!=null && !stream.match("{{", false)){}
			return null;
			}
		};
	return CodeMirror.overlayMode(CodeMirror.getMode(config, parserConfig.backdrop || "htmlmixed"), mustacheOverlay);
	});

/*Opens the code editor within the text area*/
var myCodeMirror=CodeMirror.fromTextArea(document.getElementById("code"),{
	mode: "mustache",
	lineNumbers: true,
	lineWrapping: true
	});

/*On change event the preview is updated*/
myCodeMirror.on('change',function(myCodeMirror, change) {
	<?php $jstags=json_encode($tags);?>
	var jstags=<?php echo json_encode($jstags); ?>;
	var code=myCodeMirror.getValue();
	createPreviewFrame(jstags,395,code);
	});
</script>
