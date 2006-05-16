<?php 
/* 									define_levels.php
*/

$host='markbook.php';
$current='define_levels.php';
$action='define_levels_action1.php';
$choice='class_view.php';

   	$c=0;
	$grading=array();
/*	select grading schemes by the crid/bid of the displayed classes*/
	for($c=0;$c<sizeof($cids);$c++){
		$cid=$cids[$c];	
		$d_cridbid=mysql_query("SELECT DISTINCT subject_id, 
				course_id FROM class WHERE id='$cid'");
		$cridbid = mysql_fetch_array($d_cridbid,MYSQL_ASSOC);
		$bid=$cridbid{'subject_id'};
		$crid=$cridbid{'course_id'};
		$d_grading=mysql_query("SELECT name FROM grading
					WHERE (subject_id LIKE '$bid' OR subject_id='%') 
						AND (course_id LIKE '$crid' OR course_id='%')");
		$c2=0;
		while ($new=mysql_fetch_array($d_grading,MYSQL_ASSOC)){
			if(!in_array($new,$grading)){
				$grading[$c2]=$new;
				$c2++;
				}
			}
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
			for($c=0; $c<sizeof($grading); $c++){
				print '<option ';
				print	' value="'.$grading[$c]{'name'}.'">'.$grading[$c]{'name'}.'</option>';
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

	<input type="hidden" name="mid" value="<?php print $_POST{'mid'};?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form>
</div>

<div class="buttonmenu">
	<button onClick="processContent(this);" name="sub" value="Submit">Submit</button>
	<button onClick="processContent(this);" name="sub" value="Cancel">Cancel</button>
	<button onClick="processContent(this);" name="sub" value="Reset">Reset</button>
	<button style="visibility:hidden;" name="" value=""></button>
</div>












