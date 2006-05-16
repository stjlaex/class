<?php
/*									student_view_student.php
*/

$action='student_view_student1.php';

include('scripts/sub_action.php');

/********Check user has permission to view*************/
$yid=$Student['NCyearActual']['id_db'];
$perm=getYearPerm($yid, $respons);
if($perm['r']!=1){
	print '<h5 class="warn">You do not have the permissions to view this page!</h5>';
	$current='student_view.php';
	include('scripts/redirect.php');
	exit;
	}	

three_buttonmenu();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		<table class="listmenu">
		  <caption><?php print_string('studentdetails',$book);?></caption>

<?php	
	$in=0;
	while(list($key,$val)=each($Student)){
		if(isset($val['value']) & is_array($val)){
?>	
		  <tr>
			<td><label><?php print_string( $val['label'],$book); ?></label></td>
			<td>
<?php 
				if($val['type_db']=='enum'){
					$enum=getEnumArray($val['field_db']);
					print '<select name="'.$val['field_db'].$in.'" size="1">';
					print '<option value=""></option>';
					while(list($inval,$description)=each($enum)){	
						print '<option ';
						if($val['value']==$inval){print 'selected="selected"';}
						print ' value="'.$inval.'">'.$description.'</option>';
						}
					print '</select>';					
					}
				else {
?>
	<input type="text" name="<?php print $val['field_db'].$in; ?>" value="<?php print $val['value']; ?>" />
<?php				} 
?>
			</td>
		  </tr>
<?php	
			$in++;	
			}
		}
?>
		</tr>
		</table>
	  </div>	
	  
	  <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $cancel;?>">
		  <input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>