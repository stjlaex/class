<?php
/**										curriculum_matrix.php
 *	Select which subjects are taught for which courses
 *	Select which courses are taught to which yeargroups
 */

$host='admin.php';
$current='curriculum_matrix.php';
$action='course_edit2.php';
$choice='curriculum_matrix.php';

$d_yeargroup = mysql_query("SELECT * FROM yeargroup ORDER BY ncyear, id");
$d_course = mysql_query("SELECT * FROM course ORDER BY stage, id");
$d_subject = mysql_query("SELECT * FROM subject ORDER BY id");

three_buttonmenu();

?>

<div class="content">
<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>">

<table class="listmenu">
	  <tr><th>&nbsp;</th>
<?php
	
	$c=0;
    while($subject = mysql_fetch_array($d_subject,MYSQL_ASSOC)){
		$bid[$c]=$subject{'id'};
		print '<th>'.$subject{'id'}.'</th>';
		$c++;
   		}
?>
	</tr>
<?php	
	$c=0;
	while($course = mysql_fetch_array($d_course,MYSQL_ASSOC)){
   		$crid[$c]=$course{'id'};
		print '<tr id="'.$c.'"><th>'.$crid[$c].'</th>';
		
		for($c2=0; $c2<sizeof($bid); $c2++){
			$d_cridbid = mysql_query("SELECT * FROM cridbid WHERE 
									subject_id='$bid[$c2]' AND course_id='$crid[$c]'");
			$exists = mysql_num_rows($d_cridbid);
			if($exists==1){
?>
				<td>
				<input type="checkbox" name="<?php print $crid[$c].$bid[$c2];?>" 
					value="Y" checked="checked" />
				</td>
<?php
				}
			else{
?>
				<td>
				<input type="checkbox" name="<?php print $crid[$c].$bid[$c2];?>" value="N" />
				</td>	
<?php
				}
			}
   		print '</tr>';
		$c++;
		}
?>
</table>

<table class="listmenu">
	<tr><th>&nbsp;</th>
<?php
	$c=0;
    while($yeargroup = mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
		$yid[$c]=$yeargroup{'id'};
		print '<th>'.$yeargroup{'name'}.'</th>';
		$c++;
   		}
	
	print '</tr>';
	for($c2=0; $c2<sizeof($crid); $c2++){
		$c3=$c2+100;
    	print '<tr id="'.$c3.'" ><th>'.$crid[$c2].'</th>';
		for($c=0; $c<sizeof($yid); $c++){
			$d_classes = mysql_query("SELECT * FROM classes WHERE
					course_id='$crid[$c2]' AND yeargroup_id='$yid[$c]'");
 			$exists = mysql_num_rows($d_classes);
			if($exists>0){
?>
				<td>
				<input type="checkbox" name="<?php print
						$crid[$c2];?>[]" value="<?php print $yid[$c];?>" checked="checked" />
				</td>
<?php
				}
			else{
?>
				<td>
				<input type="checkbox" name="<?php print
				  $crid[$c2];?>[]" value="<?php print $yid[$c];?>" />
				</td>
<?php
				}
			}
		print '</tr>';
		}
?>
</table>
		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
</form>
</div>
  
