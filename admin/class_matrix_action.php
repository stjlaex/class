<?php 
/**		  		       			class_matrix_action.php
 */

$action='class_matrix.php';

include('scripts/sub_action.php');

list($crid,$bid,$error)=checkCurrentRespon($r,$respons,'course');
if(sizeof($error)>0){include('scripts/results.php');exit;}

if($sub=='Update'){

	$subjects=(array)list_course_subjects($crid);
	$stages=(array)list_course_stages($crid);

	for($c=0;$c<sizeof($subjects);$c++){
  		$bid=$subjects[$c]['id'];
		for($c2=0;$c2<sizeof($stages);$c2++){
	  		$stage=$stages[$c2]['id'];
			$ing=$bid. $stage.'g';
			$inm=$bid. $stage.'m';
			$ins=$bid. $stage.'s';
			$ind=$bid. $stage.'d';
			$inblock=$bid. $stage.'block';
			$classdef=array('crid'=>$crid,'bid'=>$bid,'stage'=>$stage);
			if(isset($_POST[$ing])){
				if($_POST[$ing]=='forms'){
					$classdef['generate']=$_POST[$ing];
					$classdef['formgroup']='N';
					$classdef['many']='0'; 
					}
				elseif($_POST[$ing]=='formtutors'){
					$classdef['generate']='forms';
					$classdef['formgroup']='Y';
					$classdef['many']='0'; 
					}
				else{
					$classdef['generate']='sets';
					$classdef['formgroup']='N';
					$classdef['many']=$_POST[$ing]; 
					}
				$classdef['sp']=$_POST[$ins];
				$classdef['dp']=$_POST[$ind];
				$classdef['block']=$_POST[$inblock];
				update_subjectclassdef($classdef);
				}
			}
		}
	}

elseif($sub=='Generate'){
	$action='class_matrix_action.php';
	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
	  <fieldset class="center">
		<legend><?php print_string('confirm',$book);?></legend>
		<?php print_string('generateclassstructurequestion',$book);?>		
		<div class="right">
		  <?php include('scripts/check_yesno.php');?>
		  </div>
	  </fieldset>

		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		  
	  </form>
  </div>
<?php
	exit;
	}

elseif($sub=='Submit'){

	include('scripts/answer_action.php');

	$result[]=get_string('newclassstructure',$book);


	/* Clear out the class records */
	$cohorts=(array)list_course_cohorts($crid);
	foreach($cohorts as $cohort){
		$cohid=$cohort['id'];
		mysql_query("DELETE cidsid.* FROM cidsid, class WHERE
					class.id=cidsid.class_id AND class.cohort_id='$cohid';");
   		mysql_query("DELETE tidcid.* FROM tidcid, class WHERE 
					class.id=tidcid.class_id AND class.cohort_id='$cohid';");
   		mysql_query("DELETE score.* FROM score, midcid WHERE
					score.mark_id=midcid.id AND midcid.class_id=ANY(SELECT id FROM class WHERE cohort_id='$cohid');");
  		mysql_query("DELETE midcid.* FROM midcid, class WHERE
					class.id=midcid.class_id AND class.cohort_id='$cohid';");
 		mysql_query("DELETE FROM class WHERE cohort_id='$cohid';");
		}

	/* Generate the new class structure. */
	$d_classes=mysql_query("SELECT * FROM classes WHERE course_id='$crid' ORDER BY subject_id, stage;");
	while($classes=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
		$bid=$classes['subject_id'];
		$stage=$classes['stage'];
		$classdef=get_subjectclassdef($crid,$bid,$stage);
		populate_subjectclassdef($classdef);
		}
	}

elseif($sub=='Refresh'){

	$result[]=get_string('classes',$book).' '. get_string('update',$book);



	/* Refresh the class structure and students. */
	$d_classes=mysql_query("SELECT * FROM classes WHERE course_id='$crid' ORDER BY subject_id, stage;");
	while($classes=mysql_fetch_array($d_classes,MYSQL_ASSOC)){
		$bid=$classes['subject_id'];
		$stage=$classes['stage'];
		$classdef=get_subjectclassdef($crid,$bid,$stage);
		if($classdef['generate']=='forms'){
			if($curryear==''){$curryear=get_curriculumyear($crid);}
			$cohid=update_cohort(array('year'=>$curryear,'course_id'=>$crid,'stage'=>$stage));
			/* TODO: make this optional becuase its a lot more than jst a refresh...
			mysql_query("DELETE cidsid.* FROM cidsid, class WHERE
					class.id=cidsid.class_id AND class.subject_id='$bid' AND class.cohort_id='$cohid';");
			mysql_query("DELETE tidcid.* FROM tidcid, class WHERE 
					class.id=tidcid.class_id AND class.subject_id='$bid' AND class.cohort_id='$cohid';");
			mysql_query("DELETE score.* FROM score, midcid WHERE
					score.mark_id=midcid.id AND midcid.class_id=ANY(SELECT id FROM class 
					WHERE subject_id='$bid' AND cohort_id='$cohid');");
			mysql_query("DELETE midcid.* FROM midcid, class WHERE
					class.id=midcid.class_id AND class.subject_id='$bid' AND class.cohort_id='$cohid';");
			mysql_query("DELETE FROM class WHERE subject_id='$bid' AND cohort_id='$cohid';");
			*/
			}
		populate_subjectclassdef($classdef);
		}
	}



if(isset($result)){include('scripts/results.php');}
include('scripts/redirect.php');
?>
