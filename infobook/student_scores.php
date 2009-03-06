<?php 
/**					   				student_scores.php
 */

$action='student_scores_action.php';

twoplus_buttonmenu($sidskey,sizeof($sids));
?>
  <div id="heading">
	<label><?php print_string('student');?></label>
<?php	
	print $Student['Forename']['value'].' '.$Student['Surname']['value'].' ';
	print '('.$Student['RegistrationGroup']['value'].')';
?>
  </div>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print 'student_view.php';?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>

	<table class="listmenu">
	  <tr>
		<th><?php print_string('assessments');?></th>
<?php
	$Assessments=fetchAssessments($sid);

	/* generate two key indexes to lookup values from the assessments array*/
	$eids=array();
	$bids=array();
	$elements=array();
	while(list($assno,$Assessment)=each($Assessments)){
		$eid=$Assessment['id_db'];
		$bid=$Assessment['Subject']['value'];
		$pid=$Assessment['SubjectComponent']['value'];
		$year=$Assessment['Year']['value'];
		/*spaces are cause of nullCorrect*/
		if($Assessment['Element']['value']==' '){$element=$eid;}
		else{$element=$year. $Assessment['Element']['value'];}
		if($pid==''){$pid=' ';}
		$elements[$element][]=$assno;
		$eids[$eid][]=$assno;
		$bids[$bid][$pid][]=$assno;
		}
	ksort($bids);
	krsort($elements);

	/* display the column headers - subject codes*/
	while(list($bid,$pids)=each($bids)){
		while(list($pid,$assnos)=each($pids)){
		  if($pid==' ' or $pid==''){print '<th>'.$bid.'</th>';}
		  else{print '<th>'.$pid.'</th>';}
		  }
		}
?>
   	<th><?php print_string('average',$book);?></th>
	</tr>
<?php
	/* each row in the table is for a single assessment using eid as
	 * the key, first find the mark type for this row's values*/
	while(list($element,$assnos)=each($elements)){
		$gradesum=0;
		$gradecount=0;
		$eid=$Assessments[$assnos[0]]['id_db'];
		$AssDef=(array)fetchAssessmentDefinition($eid);
		$element=$AssDef['Element']['value'];
		$grading_grades=$AssDef['GradingScheme']['grades'];
		$crid=$AssDef['Course']['value'];

		/* Only include assessments in the table which are for all subjects.*/
		/* This is controversial, in a sense, and it needs care for*/
		/* assessments which are of general interest, say those which have a single*/
		/* subject but are perhaps across a range of components. NB/
		/* temporary fix for Inf and Jun subjects!*/
		if($AssDef['Subject']['value']=='%' or
		$AssDef['Subject']['value']=='Jun' or $AssDef['Subject']['value']=='Inf'){
			/* display the row for this eid*/
			print '<tr><td>';
			print $Assessments[$assnos[0]]['Year']['value'].' ';
			print $Assessments[$assnos[0]]['Description']['value'];
			print '</td>';

			/* display values for each bid along the row*/
			reset($bids);
			while(list($bid,$pids)=each($bids)){
				while(list($pid,$assses)=each($pids)){
					print '<td>';
					/* iterate over assnos, that is the pointer to all entries in
					assessments with this eid*/
					for($c=0;$c<sizeof($assnos);$c++){
						$assno=$assnos[$c];
						$Assessment=$Assessments[$assno];
						/* if this matches the bid for this cell we use it*/
						if($bid==$Assessment['Subject']['value'] 
						   and $pid==$Assessment['SubjectComponent']['value']){
							$result=$Assessment['Result']['value'];
							print $result;
							/* fetch the numerical equivalent for averaging*/
							if($grading_grades!=''){
								$score=gradeToScore($result,$grading_grades);
								}
							else{$score=$result;}
							$gradesum=$gradesum+$score;
							$gradecount++;
							}
						}
					print '</td>';
					}
				}
			/* display the assessment row average*/
			if($gradecount>0 and $grading_grades!=''){
				$scoreaverage=$gradesum/$gradecount;
				$scoreaverage=round($scoreaverage,1);
				$score=round($scoreaverage);
				$grade=scoreToGrade($score,$grading_grades);
				}
			elseif($gradecount>0){
				$scoreaverage=round($gradesum/$gradecount);
				$grade='';
				}
			else{$grade='';$scoreaverage='';}
			print '<td> '.$grade.' ('.$scoreaverage.')'.'</td>';
			print '</tr>';
			}
		}
?>
	</table>
  </div>
