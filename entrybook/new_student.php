<?php
/**									new_student.php
 */

$choice='new_student.php';
$action='new_student_action.php';

include('scripts/sub_action.php');

three_buttonmenu();

$Student=fetchStudent();
?>

  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <div class="center">
		<table class="listmenu">
		  <caption><?php print_string('student',$book);?></caption>
<?php	
	while(list($key,$val)=each($Student)){
		if(isset($val['value']) and is_array($val) and isset($val['table_db'])){
?>	
		  <tr>
			<td><label><?php print_string($val['label'],$book); ?></label></td>
			<td>
<?php 
				if($val['type_db']=='enum'){
					$enum=getEnumArray($val['field_db']);
					print '<select name="'.$val['field_db'].'" size="1">';
					print '<option value=""></option>';
					while(list($inval,$description)=each($enum)){	
						print '<option ';
						if($val['value']==$inval){print 'selected="selected"';}
						print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
						}
					print '</select>';
					}
				elseif($val['type_db']=='date'){
					$required='no';$todate=''; $xmldate=$val['field_db'];
					include('scripts/jsdate-form.php');
					}
				else{
?>
			  <input type="text" name="<?php print $val['field_db']; ?>" 
								value="<?php print $val['value']; ?>" />
<?php
					}
?>
			</td>
		  </tr>
<?php	
			}
		}
?>
		</tr>
		</table>
	  </div>
	    <input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print '';?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>