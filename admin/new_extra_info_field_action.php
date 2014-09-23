<?php 
/**											new_extra_info_field_action.php
 */

$action='staff_list.php';

include('scripts/sub_action.php');

if($sub=='Submit'){
	if(isset($_POST['fieldname']) and $_POST['fieldname']!=""){$fieldname=$_POST['fieldname'];}else{$fieldname='';}
	if(isset($_POST['subtype']) and $_POST['subtype']!=""){$subtype=$_POST['subtype'];}else{$subtype='';}
	if(isset($_POST['othertype']) and $_POST['othertype']!=""){$othertype=$_POST['othertype'];}else{$othertype='text';}
	if(isset($_POST['othertypeoptions']) and $_POST['othertypeoptions']!=""){$othertypeoptions=$_POST['othertypeoptions'];}else{$othertypeoptions='';}

	if($fieldname!='' and $subtype!=''){
		mysql_query("INSERT INTO categorydef (name, type, subtype, comment, othertype) VALUES ('$fieldname','inf','$subtype','$othertypeoptions','$othertype');");
		}
	}

include('scripts/redirect.php');
?>

