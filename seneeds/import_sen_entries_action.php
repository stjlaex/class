<?php
/**							    import_sen_entries_action.php
 */

$action="import_sen_entries_action.php";
$cancel="import_sen_entries.php";

include('scripts/sub_action.php');

$firstcol=$_POST['firstcol'];
$colstart=$_POST['colstart'];
if($_POST['separator']=='semicolon'){$separator=';';}else{$separator=',';}
if($sub=='Submit'){
	$fname=$_FILES['importfile']['tmp_name'];
	$ext=pathinfo($_FILES['importfile']['name'], PATHINFO_EXTENSION);
	if($fname!='' and $ext=='csv'){
		$result[]='Loading file '.$fname;
		include('scripts/file_import_csv.php');
		if(count($inrows)>0){
			$count=0;
			foreach($inrows as $rowno=>$inrow){
				$sid='';
				$options='';
				if($firstcol=='enrolno'){
					$d_student=mysql_query("SELECT student_id FROM info WHERE formerupn='$inrow[0]';");
					$sid=mysql_result($d_student,0);
					}
				elseif($firstcol=='sid'){
					$d_student=mysql_query("SELECT student_id FROM info WHERE student_id='$inrow[0]';");
					$sid=mysql_result($d_student,0);
					}
				elseif($firstcol=='upn'){
					$d_student=mysql_query("SELECT student_id FROM info WHERE upn='$inrow[0]';");
					$sid=mysql_result($d_student,0);
					}

				$yeargroup_id=$inrow[2];
				$form_id=$inrow[3];
				$d_student=mysql_query("SELECT id FROM student WHERE (surname='$inrow[0]' AND forename='$inrow[1]') OR (surname='$inrow[1]' AND forename='$inrow[0]');");
				$sid=mysql_result($d_student,0);

				if(mysql_num_rows($d_student)==0 or $sid==''){
					$d_student=mysql_query("SELECT id,surname,forename,form_id FROM student WHERE (surname LIKE '%$inrow[0]%' AND forename LIKE '%$inrow[1]%') OR (surname LIKE '%$inrow[1]%' AND forename LIKE '%$inrow[0]%') AND yeargroup_id='$yeargroup_id' OR yeargroup_id=NULL;");
					$sid=mysql_result($d_student,0);
					}
				if($sid==''){
					$sts=search_student_fulltext($inrow[0].' '.$inrow[1]);
					print_r($sts);
					foreach($sts as $id){
						$d_student=mysql_query("SELECT id FROM student WHERE id='$id' AND yeargroup_id='$yeargroup_id' OR yeargroup_id=NULL;");
						if(mysql_num_rows($d_student)==1){$sid=mysql_result($d_student,0);echo "<br>....fulltextfound-$sid";break;}
						}
					}
				echo "<br>".$sid.": ".$inrow[1].", ".$inrow[0]." ".$form_id."<br><br>";

				if($sid!=''){
					$count++;
					$startdate=$inrow[10];
					$comm1=$inrow[8];
					$comm2=$inrow[9];
					$sentype1=$inrow[4];
					$senranking1=$inrow[5];
					$sentype2=$inrow[6];
					$senranking2=$inrow[7];
					mysql_query("UPDATE info SET sen='Y' WHERE student_id='$sid';");
					mysql_query("INSERT INTO senhistory SET student_id='$sid', startdate='$startdate', reviewdate='2014-09-15';");
					$senhid=mysql_insert_id();
					mysql_query("INSERT INTO sencurriculum SET senhistory_id='$senhid', subject_id='General', 
								curriculum='A';");
					echo $comm1."<br><br>".$comm2;
					if($comm1!='' or $comm2!=''){
						mysql_query("UPDATE sencurriculum SET  comments='$comm1', targets='$comm2' WHERE senhistory_id='$senhid';");
						}
					if($sentype1!=''){
						mysql_query("INSERT INTO sentype SET student_id='$sid', entryn='1', 
								senranking='$senranking1', sentype='$sentype1', senassessment='I';");
						}
					if($sentype2!=''){
						mysql_query("INSERT INTO sentype SET student_id='$sid', entryn='2', 
								senranking='$senranking2', sentype='$sentype2', senassessment='I';");
						}
					}
				}
			}
		else{
			$error[]='Empty file';
			}
		}
	else{
		$error[]='Invalid file extension '.$ext;
		}

	if(count($error)>0){
		$result=array();
		$action="import_sen_entries.php";
		include('scripts/results.php');
		include('scripts/redirect.php');
		}
	
	echo $count;
	}
?>
