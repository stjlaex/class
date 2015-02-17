<?php
/**							    import_assessment_scores_action.php
 */

$action="import_assessment_scores_action2.php";
$cancel="import_assessment_scores.php";

$action_post_vars=array('curryear');
include('scripts/sub_action.php');

/*Check user has permission to configure*/
$perm=getCoursePerm($rcrid,$respons);
$neededperm='x';
include('scripts/perm_action.php');

$firstcol=$_POST['firstcol'];
$colstart=$_POST['colstart'];
$subject=$_POST['subject'];
if(isset($_POST['headers']) and $_POST['headers']=='yes'){$rowstart=1;}else{$rowstart=0;}
if(isset($_POST['year']) and $_POST['year']!=''){$curryear=$_POST['year'];}else{$curryear=$_POST['curryear'];}

function list_all_subjects(){
	$subjects=array();
	$d_s=mysql_query("SELECT id,name FROM subject ORDER BY id ASC;");
	$subjects['G']=array('id'=>'G','name'=>'General');
	while($subject=mysql_fetch_array($d_s,MYSQL_ASSOC)){
		$subjects[$subject['id']]=$subject;
		}
	return $subjects;
	}

$subjects=list_all_subjects();
$cohort=array('id'=>'','course_id'=>$rcrid,'stage'=>'%','year'=>$curryear);
if($_POST['profile']==0){
	$profiles=list_assessment_profiles($rcrid);
	foreach($profiles as $profile){
		$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$profile['id']);
		foreach($AssDefs as $AssDef){
			$eid=$AssDef['id_db'];
			$name=$AssDef['Description']['value'];
			$date=$AssDef['Creation']['value'];
			$assessments[$eid]=$name.' '.$date;
			}
		}
	}
elseif($_POST['profile']==''){
	$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort);
	foreach($AssDefs as $AssDef){
		$eid=$AssDef['id_db'];
		$name=$AssDef['Description']['value'];
		$date=$AssDef['Creation']['value'];
		$assessments[$eid]=$name.' '.$date;
		}
	}
else{
	$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort,$_POST['profile']);
		foreach($AssDefs as $AssDef){
			$eid=$AssDef['id_db'];
			$name=$AssDef['Description']['value'];
			$date=$AssDef['Creation']['value'];
			$assessments[$eid]=$name.' '.$date;
			}
	}

if($_POST['separator']=='semicolon'){$separator=';';}else{$separator=',';}

if($sub=='Submit'){
	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>">
	  <div class="table-scrollable">
		<table class="listmenu">
<?php
	$fname=$_FILES['importfile']['tmp_name'];
	$ext=pathinfo($_FILES['importfile']['name'], PATHINFO_EXTENSION);
	if($fname!='' and $ext=='csv'){
		$result[]='Loading file '.$fname;
		include('scripts/file_import_csv.php');
		if(count($inrows)>0){
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
					if($sid=='' or $sid==0){
						$d_student=mysql_query("SELECT id FROM student WHERE (surname='$inrow[1]' AND forename='$inrow[2]') OR (surname='$inrow[2]' AND forename='$inrow[1]');");
						$sid=mysql_result($d_student,0);
						}
					if($sid=='' or $sid==0){
						$d_student=mysql_query("SELECT id,surname,forename,form_id FROM student WHERE (surname LIKE '%$inrow[1]%' AND forename LIKE '%$inrow[2]%') OR (surname LIKE '%$inrow[2]%' AND forename LIKE '%$inrow[1]%');");
						if(mysql_num_rows($d_student)>0){
							while($sts=mysql_fetch_array($d_student,MYSQL_ASSOC)){
								$foundstudents[$sts['id']]=$sts;
								$options.="<option value='".$sts['id']."'>".$sts['forename']." ".$sts['surname']." (".$sts['form_id'].")</option>";
								}
							}
						else{
							$sts=search_student_fulltext($inrow[1].' '.$inrow[2]);
							foreach($sts as $id){
								$d_student=mysql_query("SELECT surname,forename,form_id FROM student WHERE id='$id';");
								$surname=mysql_result($d_student,0,'surname');
								$forename=mysql_result($d_student,0,'forename');
								$form_id=mysql_result($d_student,0,'form_id');
								$options.="<option value='".$id."'>".$forename." ".$surname." (".$form_id.")</option>";
								}
							}
						}

					if(($sid=='' or $sid==0) and strlen($options)==0 and $rowno>=$rowstart){$rowclass="midspecialrow";}
					elseif(($sid=='' or $sid==0) and strlen($options)>1 and $rowno>=$rowstart){$rowclass="pausespecialrow";}
					else{$rowclass="";}

					echo "<tr class='$rowclass'><td>";
					if($rowno>=$rowstart){
						if(strlen($options)>1){
							echo "<select name='selectedstudent-".$rowno."' onchange=\"this.parentNode.parentNode.parentNode.className='';\">";
							echo "<option value=''>-</option>".$options;
							echo "</select>";
							}
						echo $inrow[2]." ".$inrow[1];
						}
					echo "</td>";
					foreach($inrow as $colno=>$invalue){
						if($colno>=$colstart){
							echo "<td>";
							if($rowstart==1 and $rowno==0){
								if($_POST['subject']=="0"){
									$listname='subject-'.$colno;
									$listlabel='subject';
									include('scripts/set_list_vars.php');
									list_select_list($subjects,$listoptions);
									}
								echo "<br>";
								$level=10;
								foreach($assessments as $index=>$assessment){
									$lev=levenshtein(strtolower($invalue), strtolower($assessment));
									if($lev>=0 and $lev<=$level){${'selassess-'.$colno}=$index; $level=$lev;}
									if(strpos(strtolower($invalue),strtolower($assessment)) or strpos(strtolower($assessment),strtolower($invalue))){${'selassess-'.$colno}=$index; $level=$lev;}
									}
								$listname='assess-'.$colno;
								$listlabel='assessment';
								include('scripts/set_list_vars.php');
								list_select_list($assessments,$listoptions);
								echo "<br>";
								}
							echo "$invalue";
							if(($rowstart==1 and $rowno>0) or ($rowstart==0)){
								echo "<input type='hidden' name='scores[]' value='$invalue:::$sid:::$colno:::$rowno'>";
								}
							echo "</td>";
							}
						}
					echo "</tr>";
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
		$action="import_assessment_scores.php";
		include('scripts/results.php');
		include('scripts/redirect.php');
		}
?>

		</table>
	  </div>
<?php 
	if($_POST['subject']!="" and $_POST['subject']!="0"){
?>
		<input type="hidden" name="subject" value="<?php print $_POST['subject'];?>">
<?php
		}
?>
	  <input type="hidden" name="curryear" value="<?php print $curryear;?>">
	  <input type="hidden" name="studentid" value="<?php print $firstcol;?>">
	  <input type="hidden" name="current" value="<?php print $action;?>">
	  <input type="hidden" name="choice" value="<?php print $choice;?>">
	  <input type="hidden" name="cancel" value="<?php print $choice;?>">
	</form>
  </div>
<?php
	}
?>
