<?php 
/**									logbook/list_languages.php
 *
 */

if(isset($_SESSION['lang'])){$sellang=$_SESSION['lang'];}else{$sellang='';}
$languages=get_list_of_languages();
?>
  <div id="loginlang">
	<select onChange="document.langpref.submit();" name="langchoice" id="Language" class="language" size="1"  >
	  <option value="">Language</option>
<?php
	foreach($languages as $key => $language){		
		print "<option ";
		if($sellang==$key){print 'selected="selected"';}
		print	" value='".$key."'>".$language."</option>";
		}
?>
	</select>
  </div>
