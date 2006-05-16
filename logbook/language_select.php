<?php 
/**									users/list_languages.php
 *
 */

$sellang=$_SESSION['lang'];
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
  </div>