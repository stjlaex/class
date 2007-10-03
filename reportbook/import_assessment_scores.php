<?php
/**							    import_assessment_scores.php
 */

$action='edit_scores.php';
$action_post_vars=array('eid','bid','pid');

include('scripts/sub_action.php');

	/*Check user has permission to configure*/
	$perm=getCoursePerm($rcrid,$respons);
	$neededperm='x';
	include('scripts/perm_action.php');


$eid=$_POST['eid'];
$bid=$_POST['bid'];
$firstcol=$_POST['firstcol'];

if($sub=='Submit'){
	$importfile=$_POST['importfile'];
	$fname=$_FILES['importfile']['tmp_name'];
	$fuser=$_FILES['importfile']['name'];
	$ferror=$_FILES['importfile']['error'];
	$ftype=$_FILES['importfile']['type'];
	if($fname!=''){
	   	$result[]='Loading file '.$importfile;
		include('scripts/file_import_csv.php');
		if(sizeof($inrows>0)){
		    $in=0;
			while(list($index,$d)=each($inrows)){
				$sid='';
				$no=$d[0];
				$value=$d[3];
				if($firstcol=='enrolno'){
					$d_student=mysql_query("SELECT student_id FROM 
							info WHERE formerupn='$enrolno'");
					$sid=mysql_result($d_student,0);
					}
				elseif($firstcol=='sid'){
				    $sid=$no;
					}
				if($value!='' and $sid!=''){
					$res=sigfigs($value,3);
		   			if(mysql_query("INSERT INTO eidsid (assessment_id,
					   	student_id, subject_id, component_id, result, value
					) VALUES ('$eid', '$sid', '$bid', '', '$res','$value');")){$in++;}
	   				}
				}
			}
		$result[]='Entered '.$in.' assessment scores into the database.';

		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
