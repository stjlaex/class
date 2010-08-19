<?php
/**								search_action.php
 *
 * called by the search options in the sidebar
 */

$ids=array();	
$action_post_vars=array('savedview','colno');
if(isset($_POST['savedview'])){$savedview=$_POST['savedview'];}

/*posts from the student group search*/
if(isset($_POST['newyid']) and $_POST['newyid']!=''){
	$com=array('id'=>'','type'=>'year','name'=>$_POST['newyid']);
	if(!isset($savedview)){$savedview='year';}
	}
elseif(isset($_POST['newfid']) and $_POST['newfid']!=''){
	$com=array('id'=>'','type'=>'form','name'=>$_POST['newfid']);
	if(!isset($savedview)){$savedview='form';}
	}
elseif(isset($_POST['newcomid']) and $_POST['newcomid']!=''){
	//$com=array('id'=>$_POST['newcomid'],'type'=>'','name'=>'');
	$com=get_community($_POST['newcomid']);
	if(!isset($savedview)){$savedview='';}
	}
elseif(isset($_POST['newcomid1']) and $_POST['newcomid1']!=''){
	//$com=array('id'=>$_POST['newcomid1'],'type'=>'','name'=>'');
	$com=get_community($_POST['newcomid1']);
	if(!isset($savedview)){$savedview='';}
	}

if(isset($com)){
	$table='student';
	if($com['type']=='accomodation'){
		/*temporary hack!*/
		$startdate='2000-01-01';
		$enddate='2015-01-01';
		$students=(array)listin_community($com,$enddate,$startdate);
		/*to remove!*/
		}
	else{
		$students=(array)listin_community($com);
		}
	$rows=sizeof($students);
	while(list($index,$student)=each($students)){
		$ids[]=$student['id'];
		}
	}
/*else results from the free text searches*/
else{
	if(isset($_POST['forename']) and $_POST['forename']!=''){$forename=clean_text($_POST['forename']);$table='student';}
	if(isset($_POST['surname']) and $_POST['surname']!=''){$surname=clean_text($_POST['surname']);$table='student';}
	/*these are the switchlabels set for guardian and student*/
	$gfield=$_POST['gfield'];
	$sfield=$_POST['sfield'];
	if(isset($_POST['contact'.$gfield]) and $_POST['contact'.$gfield]!=''){
		$value=clean_text($_POST['contact'.$gfield]);
		$table='guardian';
		$field=$gfield;
		}
	if(isset($_POST['student'.$sfield]) and $_POST['student'.$sfield]!=''){
		$value=clean_text($_POST['student'.$sfield]);
		$table='student';
		$field=$sfield;
		}

	/*
	 *	Returns array of $d_sids and number of $rows in it
	 *  $table should be set to student or guardian
	 */

	/*new search method using field and value - currently for admin and office only*/
	if(isset($field)){
		if($table=='guardian' and $field=='country'){
			$d_sids=mysql_query("SELECT DISTINCT guardian_id AS id FROM gidaid
				JOIN address ON gidaid.address_id=address.id WHERE
				MATCH (address.country) AGAINST ('$value*' IN BOOLEAN MODE) 
				OR address.country='$value'");
			}
		elseif($table=='student' and $field=='surname'){
			/*defaults to surname or gender search*/
				//OR $field LIKE '%$value%' 
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				MATCH (surname) AGAINST ('*$value*' IN BOOLEAN MODE) 
				OR surname LIKE '%$value%' OR surname='$value' 
				ORDER BY surname, forename");
			}
		elseif($table=='student' and $field=='forename'){
			$d_sids=mysql_query("SELECT id FROM $table WHERE 
				MATCH (forename,preferredforename) AGAINST ('*$value*' IN BOOLEAN MODE) 
				OR forename='$value' OR preferredforename='$value' 
				ORDER BY surname, forename");
			}
		elseif($table=='student' and ($field!='surname' and $field!='gender')){
			$d_sids=mysql_query("SELECT id FROM student JOIN info ON
					info.student_id=student.id WHERE
				MATCH (info.$field) AGAINST ('$value*' IN BOOLEAN MODE) 
				OR info.$field='$value' ORDER BY student.surname, student.forename");
			}
		else{
			/*defaults to gender search*/
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				$field='$value' ORDER BY surname, forename");
			}
		}

	/*old search method for students only using surname and forename*/
	elseif(isset($surname) and $surname!=''){
		if(isset($forename) and $forename!=''){
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				(MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
					OR surname LIKE '%$surname%') 
					AND (MATCH (forename,preferredforename) 
					AGAINST ('$forename*' IN BOOLEAN MODE) 
					OR forename='$forename' OR preferredforename='$forename')
						ORDER BY surname, forename");
			}
		else{
			$d_sids=mysql_query("SELECT id FROM $table WHERE
				MATCH (surname) AGAINST ('$surname*' IN BOOLEAN MODE) 
				OR surname LIKE '%$surname%' 
				OR surname='$surname' ORDER BY surname, forename");
			}
		}
	elseif(isset($forename) and $forename!=''){
		$d_sids=mysql_query("SELECT id FROM $table WHERE 
				MATCH (forename,preferredforename) 
				AGAINST ('*$forename*' IN BOOLEAN MODE) 
				OR forename='$forename' OR preferredforename='$forename' 
				ORDER BY surname, forename");
		}

	if(!isset($d_sids)){
		$rows=0;
		$result[]='No matches found!';
		}
	else{
		$rows=mysql_num_rows($d_sids);
		while($student=mysql_fetch_array($d_sids,MYSQL_ASSOC)){
			$ids[]=$student['id'];
			}
		}
	}


if($rows>0 and $table=='student'){
	$_SESSION['infosids']=$ids;
	$_SESSION['infosearchgids']=array();
	$action='student_list.php';
	}
elseif($rows>0 and $table=='guardian'){
	$_SESSION['infosearchgids']=$ids;
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
