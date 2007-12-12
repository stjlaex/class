<?php
/**											class_matrix.php
 *
 *	Select which subjects are taught for which courses
 */

$action='class_matrix_action.php';
$choice='class_matrix.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if(sizeof($error)>0){include('scripts/results.php');exit;}

	$d_classes=mysql_query("SELECT DISTINCT stage FROM classes WHERE
							course_id='$crid'");
	$d_subject=mysql_query("SELECT DISTINCT subject_id FROM cridbid 
				WHERE course_id='$crid' ORDER BY subject_id");
	$bids=array();
   	while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
   		$bids[]=$subject['subject_id'];
		}

$extrabuttons['generateclasses']=array('name'=>'sub','value'=>'Generate');
$extrabuttons['savechanges']=array('name'=>'sub','value'=>'Update');
two_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
	  <table class="listmenu">
		<tr>
		  <th></th>
<?php
	$stages=array();
	while($stage=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
   		$stages[]=$stage['stage'];
  		print '<th>'.$stage['stage'].'</th>';
		}
?>
		</tr>
<?php
	for($c2=0; $c2<sizeof($bids); $c2++){
   		print '<tr id="'.$c2.'" >';
   		print '<th>'.$crid.':'.$bids[$c2].'</th>';
	
   		for($c=0; $c<sizeof($stages); $c++){	
   			$d_classes=mysql_query("SELECT * FROM classes WHERE
				subject_id='$bids[$c2]' AND stage='$stages[$c]' AND course_id='$crid'");
   			$classes=mysql_fetch_array($d_classes,MYSQL_ASSOC);
   			$many=$classes['many'];
   			$generate=$classes['generate'];
?>
		  <td>
			<input style="width:25%;" type="text" 
			  name="<?php print $bids[$c2].$stages[$c].'m';?>" value="<?php print $many;?>"/>

			  <select name="<?php print $bids[$c2].$stages[$c].'g';?>">
				<option value="none" <?php if($generate=="none"){print "selected='selected'";}?>></option>	
				<option value="sets" <?php if($generate=="sets"){print "selected='selected'";}?>>sets</option>	
				<option value="forms" <?php if($generate=="forms"){print "selected='selected'";}?>>forms</option>	
			  </select>
		  </td>
<?php
	  		}
?>
		</tr>
<?php
	   	}
?>
	  </table>

	  <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>
