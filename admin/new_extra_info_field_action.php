<?php
/**											new_extra_info_field_action.php
 */

$action='staff_list.php';

include('scripts/sub_action.php');

$action_post_vars=array('subtype');

if($sub=='Submit'){
	if(isset($_POST['fieldname']) and $_POST['fieldname']!=""){$fieldname=$_POST['fieldname'];}else{$fieldname='';}
	if(isset($_POST['subtype']) and $_POST['subtype']!=""){$subtype=$_POST['subtype'];}else{$subtype='';}
	if(isset($_POST['othertype']) and $_POST['othertype']!=""){$othertype=$_POST['othertype'];}else{$othertype='text';}
	if(isset($_POST['rating']) and $_POST['rating']!=""){$rating=$_POST['rating'];}else{$rating='0';}
	if(isset($_POST['othertypeoptions']) and $_POST['othertypeoptions']!=""){$othertypeoptions=$_POST['othertypeoptions'];}else{$othertypeoptions='';}

	if($fieldname!='' and $subtype!=''){
		$d_cd=mysql_query("SELECT * FROM categorydef WHERE name='$fieldname' AND type='inf' AND subtype='$subtype';");
		if(mysql_num_rows($d_cd)==0){
			mysql_query("INSERT INTO categorydef (name, type, subtype, comment, othertype, rating) VALUES ('$fieldname','inf','$subtype','$othertypeoptions','$othertype','$rating');");
			$result[]="New field added.";
			}
		else{
			$action='new_extra_info_field.php';
			$result[]="Field already exists.";
			}
		}

	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
