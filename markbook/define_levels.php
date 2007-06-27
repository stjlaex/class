<?php 
/** 									define_levels.php
 */

$action='define_levels_action1.php';


three_buttonmenu();

$grading=array();
$d_grading=mysql_query("SELECT name FROM grading");
while($new=mysql_fetch_array($d_grading,MYSQL_ASSOC)){
	$grading[]=$new;
	}
?>

  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>"> 

	  <fieldset class="lefttop">
		<legend>Choose the Grade Scheme to Use</legend>
		<label for="Grading Scheme">Grade Scheme</label>
		<select class="required" name="gena" id="Grading Scheme" tabindex="1">
		  <option selected="selected" value=""></option>
<?php	  
			for($c=0;$c<sizeof($grading);$c++){
				print '<option ';
				print	' value="'.$grading[$c]['name'].'">'.$grading[$c]['name'].'</option>';
				}
?>  
		</select>
	  </fieldset>

	  <fieldset class="centerrighttop">
		<legend>Details of New Levelling Scheme</legend>
		<label for="Name">Levelling Scheme's Title (an identifying name)</label>
		<input class="required" type="text" name="lena" id="Name"
		  tabindex="2" size="20" maxlength="20" pattern="alphanumeric" />
		  <label for="Comment">Brief description</label>
		  <input type="text" name="comment" id="Comment" tabindex="3"
			size="60" maxlength="98" pattern="alphanumeric" />
	  </fieldset>

	    <input type="hidden" name="checkmid" value="<?php print $_POST['checkmid'];?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	</form>
  </div>
