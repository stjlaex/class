<?php
/**									report_assessments_view.php
 *
 *	Uses yid, bid and eids to collate and display assessment
 *	grades. The table displayed depends on the combination of (yid,
 *	bid, eid) as follows, multiple values are indicated by X:
 *
 *	(yid, bid, X) multiple assessment grades, values for one subject
 *	(yid, %, eid) single assessment grade, values for each subject 
 *	(yid, %,   X) multiple assessment grades,
 *
 *	NB. This can only handle scoretypes which are grades. Needs to have
 *	raw scores added.
 */

$action='report_assessments.php';

if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['selyid'])){$yid=$_POST['selyid'];}else{$yid='';}
if(isset($_POST['selfid'])){$fid=$_POST['selfid'];}else{$fid='';}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}else{$gender='';}
if(isset($_POST['profid'])){$profid=$_POST['profid'];}
if(isset($_POST['template'])){$template=$_POST['template'];}
if(isset($_POST['bids'])){$selbids=(array)$_POST['bids'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}
if(isset($_POST['breakdown'])){$breakdown=$_POST['breakdown'];}else{$breakdown='subject';}

include('scripts/sub_action.php');

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'print',
									   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/markbook/',
									   'value'=>'report_profile_print.php',
									   'onclick'=>'checksidsAction(this)');

/*
$extrabuttons['displaybysubject']=array('name'=>'breakdown',
										'value'=>'subject'
										);
$extrabuttons['displaybyassessment']=array('name'=>'breakdown',
										   'value'=>'assessment'
										   );
*/
two_buttonmenu($extrabuttons,$book);

$profile=get_assessment_profile($profid);
$students=array();

	if($fid!=''){
		$students=listin_community(array('id'=>'','type'=>'form','name'=>$fid));
		}
	elseif($yid!=''){
		$students=listin_community(array('id'=>'','type'=>'year','name'=>$yid));
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN component ON component.course_id=cohort.course_id WHERE
				component.subject_id='$rbid' AND component.id='' AND cohort.stage='$stage' AND cohort.year='$year'");
			$rcrid=mysql_result($d_course,0);
			}

		/*TODO: this just guesses a date in the middle of the academic year! */
		$todate=$year-1;
		$todate=$todate.'-12-31';
		$students=listin_cohort(array('id'=>'','course_id'=>$rcrid,'year'=>$year,'stage'=>$stage),$todate);
		}



	$viewtable=array();/*The array used to store the information to display*/
	$gradestats=array();
	$assdefs=array();
	$assbids=array();/*index all the bids these asses may relate to*/
	$asscrids=array();/*index all the crids these asses may relate to*/


/****************/
	while(list($index,$eid)=each($eids)){
		$AssDef=fetchAssessmentDefinition($eid);
		$assdefs[$eid]=$AssDef;
		$asscrids[$AssDef['Course']['value']]=$AssDef['Course']['value'];
		//trigger_error($eid,E_USER_WARNING);
		}
	reset($eids);

	if(!isset($selbids)){
		/*all subjects selected so fetch bids for each crid*/
		/*the assessments may be across multiple crids to make even harder!*/
		foreach($asscrids as $asscrid){
			$subjects=(array)list_course_subjects($asscrid);
			foreach($subjects as $subject){
				$bid=$subject['id'];
				$assbids[$bid]=$bid.' ';
				$compstatus='%';
				$comps=list_subject_components($bid,$asscrid,$compstatus);
				foreach($comps as $comp){
					$assbids[$bid.$comp['id']]=$bid . $comp['id'];
					}
				}
			}
		}
	else{
		/*selbids can only be returned when rcrid is set so easier!*/
		if($selbids[0]=='%'){
			$selbids=array();
			$subjects=list_course_subjects($rcrid);
			foreach($subjects as $subject){
				$selbids[]=$subject['id'];
				}
			}

		while(list($bindex,$bid)=each($selbids)){
			$assbids[]=$bid.' ';
			$compstatus='%';
			$comps=list_subject_components($bid,$rcrid,$compstatus);
			foreach($comps as $component){
				$assbids[]=$bid . $component['id'];
				$strands=list_subject_components($component['id'],$rcrid,$compstatus);
				foreach($strands as $strand){
					$assbids[]=$bid . $strand['id'];
					}
				}
			}

		reset($selbids);
		}

/****************/

	$viewtable[0]['out']=array();
	$viewtable[0]['count']=array();
	$c=1;/*$c=0 in viewtable is for column headers*/
	while(list($index,$student)=each($students)){
		/* TODO: improve and extend the filter methods... */
		if($gender=='' or $gender==$student['gender']){
			$sid=$student['id'];
			$viewtable[$c]=array();
			$viewtable[$c]['student']=(array)$student;
			$viewtable[$c]['out']=array();
			$c++;
			}
		}


	/* the displayed table will either show columns for subject (and */
	/* average over the assessments) or show columns for assessments (and */
	/* average over the subjects) --- averaging is done over $aids for */
	/* each column in $hids */

	/* First row of table is column headers - starting with yeargroup*/
	if($yid!=''){$viewtable[0]['cohort']=get_string('yeargroup').' '.$yid;}
	elseif($fid!=''){$viewtable[0]['cohort']=get_string('formgroup').' '.$fid;}
	else{$viewtable[0]['cohort']=get_string('cohort').' '.$stage.' '.$year;}
	if($breakdown=='subject'){
		$hids=$assbids;
		$aids=$eids;
		while(list($c3,$bidpid)=each($assbids)){
			$viewtable[0]['out'][]=$bidpid;
			$viewtable[0]['count'][]=0;
			}
		}
	elseif($breakdown=='assessment'){
		$hids=$eids;
		$aids=$assbids;
		while(list($c4,$eid)=each($hids)){
			$viewtable[0]['out'][]=$assdefs[$eid]['Description']['value'];
			}
		}

	/* the main loop - working the values for each student row in the table*/
	for($rowno=1;$rowno<sizeof($viewtable);$rowno++){
		$sid=$viewtable[$rowno]['student']['id'];
		$Assessments=array();
		while(list($index,$eid)=each($eids)){
			$Assessments=array_merge($Assessments,fetchAssessments($sid,$eid));
			}
		reset($eids);

		/* generate an index to lookup values from the assessments' array*/
		$assaids=array();
		$asshids=array();
		if($breakdown=='subject'){
			while(list($assno,$Assessment)=each($Assessments)){
				$eid=$Assessment['id_db'];
				$bid=$Assessment['Subject']['value'];
				$pid=$Assessment['SubjectComponent']['value'];
				$asshids[$bid . $pid][]=$assno;
				$assaids[$eid][]=$assno;
				}
			}
		elseif($breakdown=='assessment'){
			while(list($assno,$Assessment)=each($Assessments)){
				$eid=$Assessment['id_db'];
				$bid=$Assessment['Subject']['value'];
				$pid=$Assessment['SubjectComponent']['value'];
				$asshids[$eid][]=$assno;
				$assaids[$bid. $pid][]=$assno;
				}
			}

		/* each cell averages over all selected assessments for one hid*/
		reset($hids);
		$colcount=-1;
		while(list($c33,$hid)=each($hids)){
			$colcount++;
		  if(array_key_exists($hid,$asshids)){
			  /*any assessments for this hid?*/
			$assnos=$asshids[$hid];/*all of the entries in Assessments for this hid*/
			$gradesum=0;
			$gradecount=0;
			$scorecount=0;
			/*average over all possible Assessments, indexed by assnos*/
			for($c=0;$c<sizeof($assnos);$c++){
				$assno=$assnos[$c];
				$Assessment=$Assessments[$assno];
				$eid=$Assessment['id_db'];
				if($breakdown=='subject'){$aid=$eid;}
				else{$aid=$Assessment['Subject']['value'].$Assessment['SubjectComponent']['value'];}
				/* if this matches one of the chosen aids then include in average*/
				if(in_array($aid,$aids)){
					/* get the marktype for this cell  based on one of the
					Assessments, this simplistically assumes all assessments have
					the same marktype - can't do much else until there is the ability
					to translate between grading schemes*/
			   		$crid=$Assessment['Course']['value'];
					if($assdefs[$eid]['GradingScheme']['grades']!=''){
						$result=$Assessment['Result']['value'];
						$score=gradeToScore($result,$assdefs[$eid]['GradingScheme']['grades']);
	   					$gradesum=$gradesum+$score;
		   				$gradecount++;
						}
					else{
						$result=$Assessment['Value']['value'];
						$score=$result;
	   					$gradesum=$gradesum+$score;
		   				$scorecount++;
						}
					}
				}
			}
		  else{$assnos=array(); $gradecount=0; $scorecount=0;}

		  /*display the assessment average*/
		  if($gradecount>0){
					$scoreaverage=$gradesum/$gradecount;
					$scoreaverage=round($scoreaverage,1);
					$score=round($scoreaverage);
					$grade=scoreToGrade($score,$assdefs[$eid]['GradingScheme']['grades']);
					$viewtable[0]['count'][$colcount]=1;
					}
		  elseif($scorecount>0){
					$scoreaverage=$gradesum/$scorecount;
					$scoreaverage=round($scoreaverage,1);
					$grade=round($scoreaverage);
					$viewtable[0]['count'][$colcount]=1;
					}
		  else{$score='';$scoreaverage='';$grade='';}
		  if(isset($gradestats[$grade])){$gradestats[$grade]=$gradestats[$grade]+1;/*sum for stats*/}
		  else{$gradestats[$grade]=0;$gradestats[$grade]=$gradestats[$grade]+1;}
		  if($gradecount>1){
			  $viewtable[$rowno]['out'][]=$grade.' ('.$scoreaverage.')';
			  }
		  elseif($scorecount>1){
			  $viewtable[$rowno]['out'][]=$scoreaverage;
			  }
		  else{
			  $viewtable[$rowno]['out'][]=$grade;
			  }
			}

		/*TODO: end of row average needed here*/
		}
?>

	  <div id="viewcontent" class="content fullwidth">
		<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
		<table class="listmenu sidtable" id="sidtable">
			<tr>
		<th colspan="1"><?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
		</th>

<?php
		  /*  display the column headers*/
		  print '<th colspan="1">'.$viewtable[0]['cohort'].'</th>';
		  for($c2=0;$c2<sizeof($viewtable[0]['out']);$c2++){
			  if($viewtable[0]['count'][$c2]>0){
				  print '<th style="font-weight:300;">'.$viewtable[0]['out'][$c2].'</th>';
				  }
			  }
?>
			</tr>
<?php
	for($rowno=1;$rowno<sizeof($viewtable);$rowno++){
		$row=$viewtable[$rowno];
		$sid=$row['student']['id'];
?>
				  <tr>
		<tr id="sid-<?php print $sid;?>">
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
			<?php print $rowno;?>
		  </td>
					<td>
					<?php print $row['student']['surname']; ?>,
					<?php print $row['student']['forename']; ?>
					(<?php print $row['student']['form_id']; ?>)
					</td>
<?php
		for($c2=0;$c2<sizeof($row['out']);$c2++){
			  if($viewtable[0]['count'][$c2]>0 and $row['out'][$c2]!=' ()'){
?>
					<td>
<?php 				print $row['out'][$c2]; ?>
					</td>
<?php 
				}
			}
?>
				  </tr>
<?php	
		}
?>
				</table>

<?php if(isset($stage)){?>
			<input type="hidden" name="stage" value="<?php print $stage;?>" />
<?php	} ?>
<?php if(isset($year)){?>
			<input type="hidden" name="year" value="<?php print $year;?>" />
<?php	} ?>
<?php if(isset($yid)){?>
			<input type="hidden" name="selyid" value="<?php print $yid;?>" />
<?php	} ?>
<?php if(isset($fid)){?>
			<input type="hidden" name="selfid" value="<?php print $fid;?>" />
<?php	} ?>
<?php 
	if(isset($selbids)){
		while(list($index,$bid)=each($selbids)){
?>
			<input type="hidden" name="bids[]" value="<?php print $bid;?>" />
<?php
			}
		}
	while(list($index,$eid)=each($eids)){
?>
			<input type="hidden" name="eids[]" value="<?php print $eid;?>" />
<?php
		}
?>
			<input type="hidden" name="current" value="<?php print $action;?>" />
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
			<input type="hidden" name="cancel" value="<?php print $choice;?>" />
			</form>
		  </div>

		<div id="xml-checked-action" style="display:none;">
<?php 
$profile['name']='blank';
$profile['course_id']='';
$profile['subject_id']='%';
$profile['component_status']='All';
$profile['rating_name']='average';
$profile['bid']='%';
$profile['eids']=(array)$eids;
if($profile['crid']=='FS'){
        $profile['bid']='EY';
	}
if($template!=''){
        $profile['transform']=$template;
        }
//$profile['stage']=$stage;
$profile['pid']='';
$profile['stage']='R';
xmlechoer('Profile',$profile);
?>
		</div>
