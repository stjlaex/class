<?php 
/**								   		import_students_action1.php
 *
 *	Reads the import file into an array.
 *	Allows user-defined definition of the fields in array.
 *	
 */

$action='import_students_action2.php';

$idef=$_SESSION['idef'];
$instudents=$_SESSION['instudents'];
$nofields=$_SESSION['nofields'];

/***********************************************************************/
/*The imported definition idef() must match the nofields imported, so */
/* adding blank entries to ensure this*/

		$blanks=$nofields-sizeof($idef);
		for($b=0;$b<$blanks;$b++){
				$idef[$b]=array($b, '', '', '');
				}
	
/************************Define the fields******************************/	
//				the possible fields for student data
		$sidfields=array();
		$c=0;	
   		$d_student=mysql_query("DESCRIBE student");
//		remove the first field from user options (id)
		$ignore=mysql_fetch_array($d_student,MYSQL_ASSOC);
		while($student_fields=mysql_fetch_array($d_student,MYSQL_ASSOC)){
				$field_name=$student_fields['Field'];
   				array_push($sidfields, $field_name);
				$c++;		
				}
		$c=0;	
   		$d_info=mysql_query("DESCRIBE info");
		$ignore=mysql_fetch_array($d_info,MYSQL_ASSOC);
		while($info_fields=mysql_fetch_array($d_info,MYSQL_ASSOC)){
				$field_name=$info_fields['Field'];
   				array_push($sidfields, $field_name);
				$c++;
				}

//accomodation stay	
		$c=0;	
   		$d_acc=mysql_query("DESCRIBE accomodation");
		$ignore=mysql_fetch_array($d_acc,MYSQL_ASSOC);
		while($info_fields=mysql_fetch_array($d_acc,MYSQL_ASSOC)){
				$field_name=$info_fields['Field'];
   				array_push($sidfields, $field_name);
				$c++;
				}

//				the possible fields for guardian data
		$gidfields=array();
		$c=0;	
   		$d_guardian=mysql_query("DESCRIBE guardian");
		$ignore_id=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
		while($guardian_fields=mysql_fetch_array($d_guardian,MYSQL_ASSOC)){
				$field_name=$guardian_fields['Field'];
   				array_push($gidfields, $field_name);
				$c++;		
				}
//				plus the relationship
		array_push($gidfields, 'relationship');
		array_push($gidfields, 'mailing');

		$c=0;
   		$d_address=mysql_query("DESCRIBE address");
		$ignore_id=mysql_fetch_array($d_address,MYSQL_ASSOC);
		while($address_fields=mysql_fetch_array($d_address,MYSQL_ASSOC)){
				$field_name=$address_fields['Field'];
   				array_push($gidfields, $field_name);
				$c++;		
				}
   		array_push($gidfields, 'home phone');
   		array_push($gidfields, 'mobile phone');
   		array_push($gidfields, 'work phone');
   		array_push($gidfields, 'fax');

	$extrabuttons['loaddefinition']=array('name'=>'sub','value'=>'Load');
	three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>">
	  <table class="listmenu">
		<tr>
		  <th>Field No.</th>
		  <th>Example Record</th>
		  <th>Preset Values</th>
		  <th>Student</th>
		  <th>Contact One</th>
		  <th>Contact Two</th>
		  <th>Contact Three</th>
		</tr>
<?php
		/*column showing an example imported row*/
		$egstudent=$instudents[0];
		$nofields=sizeof($egstudent);
		for($c=0;$c<$nofields;$c++){
?>
		<tr>
		  <td><?php print $c;?></td>
		  <td><?php print $egstudent[$c];?></td>
		  <td>
			<input type="text" size="10" 
			  name="<?php print 'preset'.$c; ?>" value="<?php print $idef[$c][3];?>"/>
		  </td>
		  <td>
			<select tabindex="<?php print $tab++;?>" name="<?php print 'sidfield'.$c;?>">
			  <option value=""></option>
<?php
    for($c2=0; $c2<(sizeof($sidfields)); $c2++){
		print '<option value="'.$sidfields[$c2].'" '; 
		if($idef[$c][1]=='sid' & $idef[$c][2]==$sidfields[$c2]){print 'selected';}
		print '>'.$sidfields[$c2].'</option>';
		}
?>
			</select>
		  </td>
		  <td>
			<select name="<?php print 'gid1field'.$c;?>">
			 <option value=""></option>
<?php    
    for($c2=0; $c2<(sizeof($gidfields)); $c2++){
		print '<option value="'.$gidfields[$c2].'" '; 
		if($idef[$c][1]=='gid1' & $idef[$c][2]==$gidfields[$c2]){print 'selected';}
		print '>'.$gidfields[$c2].'</option>';
		}
?>
			</select>
		  </td>
		  <td>
			<select name="<?php print 'gid2field'.$c;?>">
			  <option value=""></option>
<?php
    for($c2=0; $c2<(sizeof($gidfields)); $c2++){
		print '<option value="'.$gidfields[$c2].'"'; 
		if($idef[$c][1]=='gid2' & $idef[$c][2]==$gidfields[$c2]){print 'selected';}
		print '>'.$gidfields[$c2].'</option>';
		}
?>
			</select>
		  </td>
		  <td>
			<select name="<?php print 'gid3field'.$c;?>">
			  <option value="" selected></option>
<?php
    for($c2=0; $c2<(sizeof($gidfields)); $c2++){
		print '<option value="'.$gidfields[$c2].'" '; 
		if($idef[$c][1]=='gid3' & $idef[$c][2]==$gidfields[$c2]){print 'selected';}
		print '>'.$gidfields[$c2].'</option>';
		}
?>
			</select>
		  </td>
		</tr>
<?php  
	}
?>
	  </table>
	  <input type="hidden" name="current" value="<?php print $action;?>">
	  <input type="hidden" name="choice" value="<?php print $choice;?>">
	  <input type="hidden" name="cancel" value="<?php print $choice;?>">
	</form>
  </div>