<?php
/**								search_action.php
 *
 * called by the search options in the sidebar
 */

$action='student_list.php';

$sids=array();	

if(isset($_POST['newyid']) and $_POST['newyid']!=''){
	$com=array('id'=>'','type'=>'year','name'=>$_POST['newyid']);
	}
elseif(isset($_POST['newfid']) and $_POST['newfid']!=''){
	$com=array('id'=>'','type'=>'form','name'=>$_POST['newfid']);
	}
elseif(isset($_POST['newcomid']) and $_POST['newcomid']!=''){
	$com=array('id'=>$_POST['newcomid'],'type'=>'','name'=>'');
	}

if(isset($com)){
	$students=(array)listin_community($com);
	$rows=sizeof($students);
	while(list($index,$student)=each($students)){
		$sids[]=$student['id'];
		}
	}
else{

	if(isset($_POST['forename'])){$forename=clean_text($_POST['forename']);}else{$forename='';}
	if(isset($_POST['surname'])){$surname=clean_text($_POST['surname']);}else{$surname='';}

	include('scripts/find_sid.php');

	if($rows>0){
		/*rows is the number of matching students found by find_sid*/
		while($student=mysql_fetch_array($d_sids,MYSQL_ASSOC)){
			$sids[]=$student['id'];
			}
		}
	}

if($rows>0){
	$_SESSION['infosids']=$sids;
	}
else{
	$result[]=get_string('nostudentsfoundtryanothersearch',$book);
	$action='';
	include('scripts/results.php');
	}
include('scripts/redirect.php');
?>
