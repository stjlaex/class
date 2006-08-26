<?php
/**									contact_details.php
 *
 */

$action='contact_details_action.php';
$cancel='student_view.php';

include('scripts/sub_action.php');

if(isset($_GET{'contactno'})){$contactno=$_GET{'contactno'};}
else{$contactno=$_POST{'contactno'};}

if($contactno!='-1'){
	$Contact=$Student['Contacts'][$contactno];
	$Phones=$Contact['Phones'];
	$Addresses=$Contact['Addresses'];
	}
else{
	$Contact=fetchContact();
	}
$Phones[]=fetchPhone();
$Addresses[]=fetchAddress();

/*Check user has permission to view*/
$yid=$Student['YearGroup']['value'];
$contactgid=$Contact['id_db'];
$perm=getYearPerm($yid,$respons);
include('scripts/perm_action.php');

$extrabuttons['removecontact']=array('name'=>'sub','value'=>'Delete Checked');
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="left">
		<legend><?php print_string('contactdetails',$book);?></legend>
<?php

	reset($Contact);
	while(list($key,$val)=each($Contact)){
		if(isset($val['value']) & is_array($val)){
?>	
		<label><?php print_string($val['label'],$book); ?></label>
<?php 
				if($val['type_db']=='enum'){
					$enum=getEnumArray($val['field_db']);
					print '<select name="'.$val['field_db'].'" size="1">';
					while(list($inval,$description)=each($enum)){	
						print '<option ';
						if($val['value']==$inval){print 'selected="selected"';}
						print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
						}
					print '</select>';					
					}
				else{
?>
	<input type="text" name="<?php print $val['field_db']; ?>" 
								value="<?php print $val['value']; ?>" />
<?php
					}
				}
		}
?>
	  </fieldset>	
<?php

?>
	  <fieldset class="right">
		<legend><?php print_string('contactphones',$book);?></legend>
<?php	
	while(list($phoneno,$Phone)=each($Phones)){
		$pid=$Phone['id_db'];
		while(list($phonekey,$val)=each($Phone)){
?>
		<div class=center">
<?php
				if(isset($val['value']) & is_array($val)){
?>	
		<label><?php print_string($val['label'],$book); ?></label>
<?php 
					if($val['type_db']=='enum'){
						$enum=getEnumArray($val['field_db']);
						print '<select name="'.$val['field_db'].$phoneno.'" size="1">';
							print '<option ';
							if($val['value']==''){print 'selected="selected"';}
							print ' value=""></option>';

						while(list($inval,$description)=each($enum)){
							print '<option ';
							if($val['value']==$inval){print 'selected="selected"';}
							print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
							}
						print '</select>';
						}
					else {
?>
		<input type="text" 
		  name="<?php print $val['field_db'].$phoneno; ?>" 
		  value="<?php print $val['value']; ?>" />
<?php					}
					}
				}
?>
		</div>
<?php
			}
?>
	  </fieldset>

	  <fieldset class="center">
		<legend><?php print_string('contactaddress',$book);?></legend>
<?php	

	while(list($addressno,$Address)=each($Addresses)){
?>
		  <div class="left">
<?php
			while(list($addresskey,$val)=each($Address)){
				if(isset($val['value']) & is_array($val)){
?>			
				<label><?php print_string($val['label'],$book); ?></label>
<?php
					if($val['type_db']=='enum'){
						$enum=getEnumArray($val['field_db']);
						print '<select name="'.$val['field_db'].$addressno.'" size="1">';
						while(list($inval,$description)=each($enum)){	
							print '<option ';
							if($val['value']==$inval){print 'selected="selected"';}
							print ' value="'.$inval.'">'.$description.'</option>';
							}
						print '</select>';
						}
					else{
?>
						<input type="text" name="<?php print $val['field_db'].$addressno; ?>" value="<?php print $val['value']; ?>" />

<?php					}
					}
				}

/*				find other contacts who share this address*/
				$aid=$Address['id_db'];
				$d_gidaid=mysql_query("SELECT * FROM gidaid WHERE address_id='$aid'");
?>
		</div>
		<div class="right">
		  <label><?php print_string('sharedwith',$book);?></label>
<?php
					while($gidaid=mysql_fetch_array($d_gidaid,MYSQL_ASSOC)){
						$gid=$gidaid['guardian_id'];
				   		$d_guardian=mysql_query("SELECT * FROM guardian WHERE id='$gid'");
				   		$d_gidsid=mysql_query("SELECT * FROM gidsid WHERE guardian_id='$gid'");
						$guardian=mysql_fetch_array($d_guardian,MYSQL_ASSOC);
?>
		  <div class="right">
			<input type="checkbox" name="ungidaids[]" 
			  value="<?php print $gid.':'.$aid; ?>" />
<?php
						print $guardian['forename'].'&nbsp;'.$guardian['surname'].'&nbsp;';
						while($gidsid=mysql_fetch_array($d_gidsid,MYSQL_ASSOC)){
							$siblingsid=$gidsid['student_id'];
							$d_student=mysql_query("SELECT * FROM
													student WHERE id='$siblingsid'");
							$student=mysql_fetch_array($d_student,MYSQL_ASSOC);
							print displayEnum($gidsid['relationship'],'relationship').'
								of<br />'.$student['forename'].'&nbsp;'.$student['surname'];
							}
?>
		  </div>
<?php
					}
			}
?>
		  </div>
	  </fieldset>
 	<input type="hidden" name="contactno" value="<?php print $contactno;?>">
 	<input type="hidden" name="contactgid" value="<?php print $contactgid;?>">
 	<input type="hidden" name="current" value="<?php print $action;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
</div>
