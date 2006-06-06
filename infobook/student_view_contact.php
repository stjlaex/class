<?php
/**									student_view_contact.php
 *
 *	Called by student view. Displays contact details in a form for possible editing.
 *
 */

$action='student_view_contact1.php';

include('scripts/sub_action.php');

if(isset($_GET{'contactno'})){$contactno=$_GET{'contactno'};}
else{$contactno=$_POST{'contactno'};}

$Contact=$Student['Contacts'][$contactno];
	
/*Check user has permission to view*/
$yid=$Student['NCyearActual']['id_db'];
$contactgid=$Student['Contacts'][$contactno]['id_db'];
$perm=getYearPerm($yid,$respons);
include('scripts/perm_action.php');

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <button onClick="processContent(this);" name="sub"  value="Delete Checked">Delete&nbsp;Checked</button>

	  <fieldset class="center">
		<legend>Contact details</legend>
		<table>
<?php	
	$in=0;
	while(list($key,$val)=each($Contact)){
		if(isset($val['value']) & is_array($val)){
?>	
		  <tr>
			<th><label><?php print $val['label']; ?></label></th>
			<th>
<?php 
				if($val['type_db']=='enum'){
					$enum=getEnumArray($val['field_db']);
					print '<select name="'.$val['field_db'].$in.'" size="1">';
					while(list($inval,$description)=each($enum)){	
						print '<option ';
						if($val['value']==$inval){print 'selected="selected"';}
						print ' value="'.$inval.'">'.$description.'</option>';
						}	
					print '</select>';					
					}
				else {
?>
	<input type="text" name="<?php print $val['field_db'].$in; ?>" 
								value="<?php print $val['value']; ?>" />
<?php				} ?>
			</th>
		  </tr>
<?php			
			$in++;
			}
		}
?>
		</table>
	  </fieldset>
	
	  <fieldset class="center">
		<legend>Contact address</legend>
		<button onClick="processContent(this);" name="sub" value="New Address">New&nbsp;Address</button>
		<table>

<?php	
	$Addresses=$Contact['Addresses'];
	if(!is_array($Addresses)){$Addresses=array();}
	while(list($addressno,$Address)=each($Addresses)){
		
/*	Generate a seperate input area for each contact address... within a table*/
?>
		  <tr>
			<th valign="top">
			  <table>
				<tr>
				  <th colspan="2">
					<label>Select this address:</label>
					<input type="checkbox" name="unaids[]" value="<?php print
					$Address['id_db']; ?>" />
				  </th>
				</tr>
<?php
			while(list($addresskey,$val)=each($Address)){
				if(isset($val['value']) & is_array($val)){
?>			
				<tr>
				  <th><label><?php print $val['label']; ?></label></th>
					<th>
<?php //print $val['field_db'].$addressno.$in;
					if($val['type_db']=='enum'){
						$enum=getEnumArray($val['field_db']);
						print '<select name="'.$val['field_db'].$addressno.$in.'" size="1">';
						while(list($inval,$description)=each($enum)){	
							print '<option ';
							if($val['value']==$inval){print 'selected="selected"';}
							print ' value="'.$inval.'">'.$description.'</option>';
							}
						print '</select>';
						}
					else 
						{
?>
						<input type="text" name="<?php print $val['field_db'].$addressno.$in; ?>" value="<?php print $val['value']; ?>" />

<?php					}
   				$in++;	
				}		
?>
				  </th>
				</tr>
<?php				
			}	
?>		
			  </table>
			</th>
			<th valign="top">
			  <table>	
			
<?php
/*				find other contacts who share this address
*/
				$aid=$Address['id_db'];
				$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE address_id='$aid'");
?>
				<tr>
				  <th></th>
				  <th><label>Linked to:</th>
				  <th><label>Relationships</label></th>
				</tr>
					   
<?php
					while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
						$gid=$gidaid['guardian_id'];
				   		$d_guardian=mysql_query("SELECT * FROM guardian WHERE id='$gid'");
				   		$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE guardian_id='$gid'");
						$guardian=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
?>
				<tr>
				  <td><label>UnLink</label>
					<input type="checkbox" name="ungidaids[]" value="<?php print
												$gid.':'.$aid; ?>" />
				  </td>
<?php
						print '<td>'.$guardian['forename'].'<br />'.$guardian['surname'].'</td>';
						print '<td>';
						while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
							$siblingsid=$gidsid['student_id'];
							$d_student=mysql_query("SELECT * FROM
													student WHERE id='$siblingsid'");
							$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
							print displayEnum($gidsid['relationship'],'relationship').'
								of<br />'.$student['forename'].'&nbsp;'.$student['surname'].'.<br />';
							}
						print '</td>';
?>							
				</tr>					
<?php			
					}
?>
			  </table>
			</th>
		  </tr>
<?php 
			}
?>
		</table>
	  </fieldset>
<?php	
	$Phones=$Contact['Phones'];
?>

	  <fieldset class="center">
		<legend>Phone-numbers</legend>
		<button onClick="processContent(this);" name="sub" value="New Phone">New&nbsp;Phone</button>

		<table>

<?php	
	while(list($phoneno,$Phone)=each($Phones)){
		$pid=$Phone['id_db'];
?>		
		  <tr>
			<th>
			  <table>
				<tr>
				  <th colspan="2"><label>Select this number:</label>
<input type="checkbox" name="unpids[]" value="<?php print $pid; ?>" /></th>
		</tr>
<?php
			while(list($phonekey,$val)=each($Phone)){
				if(isset($val['value']) & is_array($val)){
?>	
				<tr>
				  <th><label><?php print $val['label']; ?></label></th>
				  <th>
<?php 
					if($val['type_db']=='enum'){
						$enum=getEnumArray($val['field_db']);
						print '<select name="'.$val['field_db'].$phoneno.$in.'" size="1">';
						print '<option>Select</option>';
						while(list($inval,$description)=each($enum)){
							print '<option ';
							if($val['value']==$inval){print 'selected="selected"';}
							print ' value="'.$inval.'">'.$description.'</option>';
							}
						print '</select>';				
						}
					else {
?>
					<input type="text" name="<?php print $val['field_db'].$phoneno.$in; ?>" value="<?php print $val['value']; ?>" />
<?php					} ?>
				  </th>
				</tr>
<?php			
					$in++;
					}
				}
?>
			  </table>
			</th>
		  </tr>
<?php 
		}
?>

		</table>
	  </fieldset>

 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
 	<input type="hidden" name="contactgid" value="<?php print $contactgid;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>

</div>

