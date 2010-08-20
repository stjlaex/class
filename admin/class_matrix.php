<?php
/**											class_matrix.php
 *
 *	Select which subjects are taught for which courses
 */

$action='class_matrix_action.php';
$choice='class_matrix.php';

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if(sizeof($error)>0){include('scripts/results.php');exit;}

$stages=(array)list_course_stages($crid);
$subjects=(array)list_course_subjects($crid);

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
	foreach($stages as $stage){
  		print '<th>'.$stage['name'].'</th>';
		}
?>
		</tr>
<?php
	for($c2=0; $c2<sizeof($subjects); $c2++){
		$bid=$subjects[$c2]['id'];
   		print '<tr id="'.$c2.'" >';
   		print '<th>'.$crid.':'.$bid.'</th>';
	
   		for($c=0; $c<sizeof($stages); $c++){
			$stage=$stages[$c]['id'];	
   			$d_classes=mysql_query("SELECT * FROM classes WHERE
				subject_id='$bid' AND stage='$stage' AND course_id='$crid'");
   			$classes=mysql_fetch_array($d_classes,MYSQL_ASSOC);
   			$many=$classes['many'];
   			$generate=$classes['generate'];
   			$sp=$classes['sp'];
   			$dp=$classes['dp'];
   			$block=$classes['block'];
?>
		  <td>
			<select name="<?php print $bid.$stage.'g';?>">
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
			  name="<?php print $bid.$stage.'s';?>" value="<?php print $sp;?>"/>
			<input style="width:25%;" type="text" maxlength="1"
			  name="<?php print $bid.$stage.'d';?>" value="<?php print $dp;?>"/>
			<input style="width:25%;" type="text" maxlength="3"
			  name="<?php print $bid.$stage.'block';?>" value="<?php print $block;?>"/>
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
