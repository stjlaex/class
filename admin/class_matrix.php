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
   			$sp=$classes['sp'];
   			$dp=$classes['dp'];
   			$block=$classes['block'];
?>
		  <td>
			<select name="<?php print $bids[$c2].$stages[$c].'g';?>">
			  <option value="none" <?php if($generate=="none"){print "selected='selected'";}?>>
			  </option>
			  <option value="forms" 
				<?php if($generate=="forms"){print 'selected="selected"';}?>
				>
				<?php print_string('forms',$book);?>
			  </option>
<?php
		   		for($no=1; $no<10; $no++){
?>
			  <option value="<?php print $no;?>" 
				<?php if($generate=='sets' and $many==$no){print "selected='selected'";}?>
				>
				<?php print $no.' '.get_string('sets',$book);?>
			  </option>
<?php
					}
?>
			  </select>

			<input style="width:25%;" type="text" maxlength="1" 
			  name="<?php print $bids[$c2].$stages[$c].'s';?>" value="<?php print $sp;?>"/>
			<input style="width:25%;" type="text" maxlength="1"
			  name="<?php print $bids[$c2].$stages[$c].'d';?>" value="<?php print $dp;?>"/>
			<input style="width:25%;" type="text" maxlength="3"
			  name="<?php print $bids[$c2].$stages[$c].'block';?>" value="<?php print $block;?>"/>
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
