<?php
/**								search_action.php
 *
 * called by the search options in the sidebar
 */

$ids=array();	

/*posts from the student group search*/
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
	$table='student';
	$students=(array)listin_community($com);
	$rows=sizeof($students);
	while(list($index,$student)=each($students)){
		$ids[]=$student['id'];
		}
	}
/*else results from the fre text name searches*/
else{

	if(isset($_POST['forename']) and $_POST['forename']!=''){$forename=clean_text($_POST['forename']);$table='student';}
	if(isset($_POST['surname']) and $_POST['surname']!=''){$surname=clean_text($_POST['surname']);$table='student';}

	if(isset($_POST['gname']) and $_POST['gname']!=''){$surname=clean_text($_POST['gname']);$table='guardian';}

	if(isset($table)){
		include('scripts/find_sid.php');
		}

	if(!isset($rows)){
		$rows=0;
		$result[]='No matches found!';
		}
	elseif($rows>0){
		/*rows is the number of matching students found by find_sid*/
		while($student=mysql_fetch_array($d_sids,MYSQL_ASSOC)){
			$ids[]=$student['id'];
			}
		}
	}


if($rows>0 and $table=='student'){
	$_SESSION['infosids']=$ids;
	$_SESSION['infogids']=array();
	$action='student_list.php';
	}
elseif($rows>0 and $table=='guardian'){
	$_SESSION['infogids']=$ids;
	$_SESSION['infosids']=array();
	$action='contact_list.php';
	}
else{
	$result[]=get_string('nostudentsfoundtryanothersearch',$book);
	$action='';
	include('scripts/results.php');
	}
include('scripts/redirect.php');
?>
