<?php 
/**									logbook/list_languages.php
 *
 */

if(isset($_SESSION['lang'])){$sellang=$_SESSION['lang'];}else{$sellang='';}
$languages=get_list_of_languages();
?>
  <div id="loginlang">
	<label for="Language">Language</label>
	<select onChange="document.langpref.submit();" name="langchoice"
	  id="Language" size="1"  >
	  <option value=""></option>
<?php
	foreach($languages as $key => $language){		
		print "<option ";
		if($sellang==$key){print "selected='selected'";}
		print	" value='".$key."'>".$language."</option>";
		}
?>
	</select>
<?php
	if($CFG->theme20!=""){
?>
	<label for="Theme">Theme</label>
	<select class="theme-selector" onChange="parent.window.location.replace('../'+this.value+'/index.php');" 
	  name="theme" id="Theme" size="1"  >
		<option value="<?php echo $CFG->applicationdirectory;?>" selected>Classis 1.0</option>
		<option value="<?php echo $CFG->theme20;?>">Classis 2.0</option>
	</select>
<?php
		}
?>
  </div>
