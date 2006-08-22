<?php
/**									ents_list_action.php
 *
 */

$action='ents_list.php';

$id=$_POST['id_db'];
$result[]=$id;
$tagname=$_POST['tagname'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid=array();}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='N';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid='';}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$d_catdef=mysql_query("SELECT subject_id FROM categorydef WHERE 
				type='ent' AND name='$tagname'");
	$backgroundtype=mysql_result($d_catdef,0);

	if($bid=='' OR $bid=='%'){$bid='General';}
	$category='';
	for($c=0;$c<sizeof($catid);$c++){
	    $category=$category.$catid[$c].':'.$ratvalue.';';
		}

	if($id!=''){
		if(mysql_query("UPDATE background SET 
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
		subject_id='$bid', category='$category', teacher_id='$tid'
		WHERE id='$id'")){
		}
		else{$error[]=mysql_error();}
		}
	else{
		if(mysql_query("INSERT INTO background SET student_id='$sid', type='$backgroundtype',
		detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid', subject_id='$bid',
		category='$category', teacher_id='$tid'")){}
		else{$error[]=mysql_error();}
		}
	$_SESSION{'Student'}=fetchStudent($sid);
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
